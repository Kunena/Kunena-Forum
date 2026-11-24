"""Tests of factory/bin/site-cypress. Run: python3 -X utf8 -m unittest discover -s factory/tests

No test runs Cypress or touches the site: `settings`, `lock` and `run` are replaced by functions which fail the test when a
command would get that far.
"""

import importlib.machinery
import importlib.util
import unittest
from pathlib import Path

PATH = Path(__file__).resolve().parents[1] / "bin" / "site-cypress"


def load_site_cypress():
    loader = importlib.machinery.SourceFileLoader("site_cypress", str(PATH))
    module = importlib.util.module_from_spec(importlib.util.spec_from_loader("site_cypress", loader))
    loader.exec_module(module)
    return module


site_cypress = load_site_cypress()


class InstallSpecsTest(unittest.TestCase):
    """The install specs never run: uninstall.cy.js removes Kunena from the site, and through the links of the site it deletes the
    files of the checkout. --spec refused them only when its text named their folder; a wider glob (all the specs of
    tests/System/integration) matched them and ran them."""

    def setUp(self):
        self.saved = {name: getattr(site_cypress, name) for name in ("settings", "lock", "run")}

        def far(*args, **kwargs):
            raise AssertionError("site-cypress went on to the site with specs which match the install specs")

        for name in self.saved:
            setattr(site_cypress, name, far)

    def tearDown(self):
        for name, value in self.saved.items():
            setattr(site_cypress, name, value)

    def assert_refused(self, specs):
        with self.assertRaises(SystemExit) as stop:
            site_cypress.main(["--spec", specs])
        self.assertEqual(stop.exception.code, 2)

    def test_a_glob_which_matches_the_install_specs_is_refused(self):
        self.assert_refused("tests/System/integration/**/*.cy.js")

    def test_an_install_spec_among_others_is_refused(self):
        self.assert_refused("tests/System/integration/site/**/*.cy.js,tests/System/**/uninstall.cy.js")


if __name__ == "__main__":
    unittest.main()
