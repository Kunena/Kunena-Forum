# Scheduled replies

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, moderators, administrators · **Issue:** [#9830](https://github.com/Kunena/Kunena-Forum/issues/9830)

A reply can be written now and published later, on a chosen date and time: a birthday wish which appears on the day, an
announcement at the start of an event.

## Schedule a reply

The full reply form of a topic has a section **Publish later** with a field **Publish on** (a date and a time). Left empty, the reply
is posted at once, as before. With a date in the future, the reply is saved and waits for that date: the message after posting gives
the date of the publication.

The date and time are read in the time zone of the user: the one of his Joomla account, else the one of the site. The help text under
the field names it. A date which is not valid, or which is not in the future, is refused and the form keeps its content.

Only logged-in users get the field, and only on the full reply form: the quick reply, a new topic and the edit of a message cannot be
scheduled. The date cannot be changed after the reply is saved.

## Who sees it before the date

Until it is published, a scheduled reply is like a message which waits for approval: only its author and the moderators of the
category see it, with the notice **Scheduled for** and the date (in the time zone of the reader). It is not counted in the topic or in
the category, it is not the last post of the topic, and nobody gets an e-mail about it yet. Its author can still edit it; it keeps its
date.

## Moderation

Scheduling never gets around moderation. When the reply would have waited for a moderator anyway (a category under review, a new user
or a guest whose posts are held, a spam check which holds it), the moderators get their e-mail when it is posted, as for any held
message, and it is **not** published at its date: it stays for a moderator.

A moderator can approve a scheduled reply early: it is published at once, with the time it was written, and its schedule is gone.
Deleting it removes the schedule as well.

## When the date comes

The scheduler task publishes the reply as if its author posted it at that moment: it gets that time, it is counted in the topic, the
category and the posts of the author, it becomes the last post of the topic, and the subscribers and the mentioned users get their
e-mails.

A reply is not published, and stays a message waiting for approval which the moderators can approve or delete, when at its date:

- it was held for review (see above);
- its topic was deleted, is waiting for approval or is locked;
- its author was removed, is banned or blocked, or may no longer reply in the topic.

## The scheduler task

The replies are published by the Joomla scheduler task **Kunena - Publish scheduled replies** (*System → Scheduled Tasks*), which the
installation of Kunena creates to run every 5 minutes. The task must run: a reply appears at the first run of the task after its date
(with the lazy scheduler of Joomla, at the first visit of the site after that run is due). One run publishes at most 50 replies, the
oldest first.

---

[← All the new features of Kunena 8.0](../README.md)
