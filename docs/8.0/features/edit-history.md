# Edit history of messages

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Moderators, users, administrators

Each edit of a message keeps the subject and the text before it. A page shows every edit of a message with who edited it, when, the
reason and the changes word by word.

## The history of a message

When a message was edited, the note *Last edit* under it gets a link **History (n)**, with the number of earlier versions, for the
users who may see the history. The link opens the page **Edit history** of the message:

- the edits, the newest first: who edited, when, the reason of the edit, and the change of the subject and of the text; the words
  which the edit removed are struck through, the words it added are highlighted (and underlined);
- the original version of the message, as its author wrote it.

The text is shown as it was typed (BBCode, Markdown), not formatted: the changes are easier to see.

An edit keeps a version only when it changes the subject or the text (an edit of only the reason keeps none). A message keeps its
last 20 versions.

## Who sees it

| Option | Where | Default |
| --- | --- | --- |
| Edit history | Kunena configuration, *Posting and editing* › *Editing and deleting* | Moderators |

- **Off**: no version is kept and nobody sees a history (the versions which were kept stay in the database).
- **Moderators**: the moderators of the category of the message.
- **Everybody who can read the message**.

The link is in the note *Last edit*, which is shown when the option *Show Edited Mark Up* is on.

## Data

The history is deleted with its messages, topics and categories. A user who is deleted takes with him the versions of his edits; the
privacy tools of Joomla export and remove them, and the diagnostics of Kunena (*historyOrphaned*) find the history of messages which
do not exist any more.

---

[← All the new features of Kunena 8.0](../README.md)
