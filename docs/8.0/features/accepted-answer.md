# Accepted answer

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, moderators, administrators

The author of a topic or a moderator accepts one reply as the answer of the topic: the reply gets a badge, the answer is shown at the
top of the topic, and the topic is marked as solved.

## Accept a reply

Each reply of a topic has an **Accept answer** button for the author of the topic (when the topic is not locked and he is not banned)
and for the moderators of the category. A topic has one accepted answer: accepting another reply replaces it, and **Remove answer** on
the accepted reply removes it.

The first post of a topic cannot be the answer, nor a reply which is waiting for approval or deleted.

## How it looks

- The accepted reply has the badge **Accepted answer** above its text.
- At the top of the topic, on every page, a box **Accepted answer** shows the author of the answer, its date, the beginning of its
  text and a link **Go to the answer**. A guest sees only the link when the category hides the replies for guests.
- With the topic icons on, accepting the answer gives the topic the icon *Solved* (see [Mark as solved](topic-status-solved.md));
  removing it gives the topic back the normal icon.

A reply which is moved to another topic, deleted or held for review is no longer shown as the answer.

The moderation log keeps who accepted or removed an answer (the operations *Post Accepted as Answer* and *Post Answer Removed*), and
the Joomla action log too when it is on.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Accepted answers | Kunena configuration, *Posting and editing* › *Closing topics* | Yes |

---

[← All the new features of Kunena 8.0](../README.md)
