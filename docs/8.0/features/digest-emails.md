# Digest e-mails of the subscriptions

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, administrators

A user who follows topics and categories can get one e-mail a day or a week with all their new posts, instead of an e-mail for each
post.

## For the users

The settings of the profile have the choice **E-mails of my subscriptions**:

- **One e-mail for each new post** (the default);
- **A daily digest**;
- **A weekly digest**.

With a digest, the new posts of the subscribed topics and categories are collected. The digest is one e-mail with the new posts
grouped by topic: the author, the time, the beginning of the text and a link to each post (at most 50 posts; the others are counted,
with a link to the forum). A post which was deleted or moved where the user may not read it is left out.

The first digest comes one day (or one week) after the first collected post, the next ones one day (or one week) after the last
digest. A user who goes back to an e-mail for each post gets what was collected with the next run of the task.

Only the e-mails of the subscriptions go into a digest: a moderator or an administrator who gets the e-mail of a post for the
moderation, and the follower of a user, keep getting it at once.

## For the administrators

| Option | Where | Default |
| --- | --- | --- |
| Digest e-mails | Kunena configuration, *E-mails and notifications* › *Subscriptions* | Yes |

The digests are sent by the scheduled task **Kunena Send Digests**: add it in *System → Scheduled Tasks* (plugin *Task - Kunena*)
and run it every hour. With the option off, the users get an e-mail for each post again, and the choice is not in the profile.

## Data

The choice of the user and the time of his last digest are kept in his Joomla parameters (`kunena_digest`, `kunena_digest_sent`).
The collected posts are deleted with their messages, topics and categories, and with a user who is deleted; the privacy tools of
Joomla export and remove them, and the diagnostics of Kunena (*digestOrphaned*) find the collected posts of messages which do not
exist any more.

---

[← All the new features of Kunena 8.0](../README.md)
