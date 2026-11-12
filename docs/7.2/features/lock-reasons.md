# A reason for locked topics

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Users, moderators, administrators · **Issue:** [#48](https://github.com/Kunena/Kunena-Forum/issues/48)

A locked topic can have a reason (Closed, Solved, Sold...), shown in the lock badge next to the subject.

## Lock with a reason

The **Moderate** menu of a topic keeps **Lock**, which locks the topic without a reason, and gets one item **Lock: …** for each reason
of the list. The reason is shown with the lock icon in the lists of topics and in the heading of the topic. A topic locked without a
reason looks as before.

**Unlock** removes the reason. When the moderation log is on, the reason is in the entry of the lock.

## The author locks his own topic

When the option is on, the author of a topic gets a **Lock** menu in the actions of his topic, with one item per reason: he has to
choose one. He can do this only while his topic is not locked and is approved, and not when he is banned. Only the moderators can
unlock the topic.

## The list of reasons

One reason per line, as plain text or as a language key (the default reasons are the keys `COM_KUNENA_TOPIC_LOCK_REASON_CLOSED`,
`..._SOLVED` and `..._SOLD`, so they are translated). Only a reason which is in the list at that moment is accepted. A locked topic
keeps its reason when the list changes. An empty list turns the reasons off: the lock works as before.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Reasons to lock a topic | Kunena configuration, tab *Look and Feel* | Closed, Solved, Sold (one per line) |
| Authors can lock their own topics | Kunena configuration, tab *Look and Feel* | No |

---

[← All the new features of Kunena 7.2](../README.md)
