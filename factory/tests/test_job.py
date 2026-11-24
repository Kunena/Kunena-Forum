"""Tests of factory/bin/job. Run: python3 -X utf8 -m unittest discover -s factory/tests

Every command the script would run (gh, git) goes through a stub of `run`, which
raises on a command a test did not expect: no test touches GitHub, git or the
real factory/jobs/.
"""

import importlib.machinery
import importlib.util
import json
import subprocess
import sys
import tempfile
import threading
import unittest
from pathlib import Path

PATH = Path(__file__).resolve().parents[1] / "bin" / "job"


def load_job():
    loader = importlib.machinery.SourceFileLoader("job", str(PATH))
    module = importlib.util.module_from_spec(importlib.util.spec_from_loader("job", loader))
    loader.exec_module(module)
    return module


job = load_job()

BRANCH = "factory/185-factory-pass-comments-of"


def pr(number, state="MERGED", fork=False, branch=BRANCH):
    return {"number": number, "url": f"https://github.com/o/r/pull/{number}", "state": state,
            "headRefName": branch, "isCrossRepository": fork}


def new_job(stage="needs-human", pr_number=None):
    return {
        "id": "185-factory-pass-comments-of", "issue": 10491, "title": "t", "issue_url": "u",
        "pr": pr_number, "pr_url": None, "branch": BRANCH,
        "worktree": "/nonexistent/.worktrees/185-factory-pass-comments-of",
        "stage": stage, "round": 1, "round_limit": 3,
        "intake": None, "agents": None, "gate": None, "reviews": {},
        "cost_usd": 0.0, "started": "2026-11-01T00:00:00Z", "ended": "2026-11-01T01:00:00Z",
    }


class Stub:
    """Stands in for `run`: answers the commands of `answers`, a list of (prefix, stdout or
    exception), in any order, and raises on any other command."""

    def __init__(self, answers):
        self.answers = answers
        self.calls = []

    def __call__(self, cmd, cwd=None, check=True):
        self.calls.append(cmd)
        for prefix, answer in self.answers:
            if cmd[:len(prefix)] == prefix:
                if isinstance(answer, Exception):
                    raise answer
                out = answer if isinstance(answer, str) else json.dumps(answer)
                return subprocess.CompletedProcess(cmd, 0, out, "")
        raise AssertionError(f"unexpected command: {cmd}")


PR_LIST = ["gh", "pr", "list", "--head", BRANCH]


class TestCase(unittest.TestCase):
    def setUp(self):
        self.saved = {name: getattr(job, name) for name in ("run", "JOBS", "cost_usd")}
        self.tmp = tempfile.TemporaryDirectory()
        job.JOBS = Path(self.tmp.name)
        job.cost_usd = lambda j: 0.0
        self.addCleanup(self.restore)

    def restore(self):
        for f in job._locks.values():
            f.close()
        job._locks.clear()
        getattr(job, "_busy", set()).clear()
        for name, value in self.saved.items():
            setattr(job, name, value)
        self.tmp.cleanup()

    def stub(self, *answers):
        job.run = Stub(list(answers))
        return job.run


class FindPrTest(TestCase):
    def test_one_merged_pr_is_recorded(self):  # (a)
        stub = self.stub((PR_LIST, [pr(10492)]))
        j = job.find_pr(new_job())
        self.assertEqual((j["pr"], j["pr_url"]), (10492, "https://github.com/o/r/pull/10492"))
        self.assertEqual(stub.calls, [["gh", "pr", "list", "--head", BRANCH, "--state", "all", "--limit", "100",
                                       "--json", "number,url,state,headRefName,isCrossRepository"]])

    def assert_unchanged(self, prs):
        self.stub((PR_LIST, prs))
        before = new_job()
        self.assertEqual(job.find_pr(new_job()), before)

    def test_no_pr(self):  # (b)
        self.assert_unchanged([])

    def test_two_prs(self):  # (c)
        self.assert_unchanged([pr(10492), pr(10493, "OPEN")])

    def test_fork_pr_only(self):  # (d)
        self.assert_unchanged([pr(10492, fork=True)])

    def test_closed_pr_only(self):  # (e)
        self.assert_unchanged([pr(10492, "CLOSED")])

    def test_other_branch_only(self):
        self.assert_unchanged([pr(10492, branch=BRANCH + "-2")])

    def test_pr_already_set(self):  # (f)
        stub = self.stub()
        j = job.find_pr(new_job(pr_number=10400))
        self.assertEqual(j["pr"], 10400)
        self.assertEqual(stub.calls, [])

    def test_no_lookup_before_a_branch_or_after_the_end(self):  # (g)
        stub = self.stub()
        for stage in ("intake", "needs-info", "spec", "merged", "rejected"):
            self.assertEqual(job.find_pr(new_job(stage)), new_job(stage))
        self.assertEqual(stub.calls, [])

    def test_failure_propagates(self):
        self.stub((PR_LIST, job.Refused("gh failed")))
        with self.assertRaises(job.Refused):
            job.find_pr(new_job())


class QueueTest(TestCase):
    def write(self, j):
        job.job_dir(j["id"]).mkdir()
        job.save(j)

    def read(self, j):
        return json.loads((job.job_dir(j["id"]) / "job.json").read_text())

    def test_merged_pr_moves_the_job_to_merged(self):  # (h)
        j = new_job()
        self.write(j)
        stub = self.stub(
            (PR_LIST, [pr(10492)]),
            (["gh", "pr", "view", "10492", "--json", "state"], {"state": "MERGED"}),
            (["gh", "issue", "edit", "10491", "--remove-label"], ""),
            (["git", "push", "origin", "--delete", BRANCH], ""),
            (["gh", "issue", "list"], []),
        )
        job.cmd_queue()
        saved = self.read(j)
        self.assertEqual((saved["stage"], saved["pr"]), ("merged", 10492))
        self.assertIn(["gh", "issue", "edit", "10491", "--remove-label", job.LABELS["needs_human"]], stub.calls)

    def test_failed_lookup_leaves_the_job(self):  # (i)
        j = new_job()
        self.write(j)
        self.stub(
            (PR_LIST, job.Refused("gh failed")),
            (["gh", "issue", "list"], []),
        )
        job.cmd_queue()
        self.assertEqual(self.read(j), j)


class BusyJobTest(TestCase):
    """`job merge` runs the gate (Cypress, up to an hour) in the background and holds the lock of its job all that time. `job
    queue`, `job summary` and `job create` read every job: they waited for that lock, so the factory started nothing new until
    the merge ended."""

    HOLDER = (
        "import os, sys, time\n"
        "f = open(sys.argv[1], 'w')\n"
        "if os.name == 'nt':\n"
        "    import msvcrt; msvcrt.locking(f.fileno(), msvcrt.LK_LOCK, 1)\n"
        "else:\n"
        "    import fcntl; fcntl.flock(f, fcntl.LOCK_EX)\n"
        "print('locked', flush=True)\n"
        "time.sleep(60)\n"
    )

    def test_queue_does_not_wait_for_a_job_which_another_process_holds(self):
        j = new_job()
        job.job_dir(j["id"]).mkdir()
        job.save(j)
        holder = subprocess.Popen([sys.executable, "-c", self.HOLDER, str(job.job_dir(j["id"]) / ".lock")],
                                  stdout=subprocess.PIPE, text=True)
        self.addCleanup(holder.kill)
        self.assertEqual(holder.stdout.readline().strip(), "locked")
        stub = self.stub((["gh", "issue", "list"], []))

        done = threading.Event()
        threading.Thread(target=lambda: (job.cmd_queue(), done.set()), daemon=True).start()

        self.assertTrue(done.wait(5), "job queue waits for the lock of a job which another process holds")
        # The busy job is only read: no lookup of its PR, and its file stays as the other process leaves it.
        self.assertEqual(stub.calls, [["gh", "issue", "list", "--label", job.LABELS["queue"], "--state", "open",
                                       "--limit", "200", "--json", "number,title"]])
        self.assertEqual(json.loads((job.job_dir(j["id"]) / "job.json").read_text()), j)


if __name__ == "__main__":
    unittest.main()
