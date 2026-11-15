# Bookmarks of messages

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, administrators

A user bookmarks single messages and finds them again in the list **My bookmarks** (the favourites keep whole topics).

## Bookmark a message

Each message has a **Bookmark** button for a user who is logged in; on a bookmarked message it is **Remove bookmark**.

The list **My bookmarks** is in the menu of the user (with the drafts), and can be a menu item: *Kunena → Recent Posts* with the
mode *My Bookmarks*. It shows the bookmarked messages which the user may still read, the newest first, with the pages of the other
lists of posts.

The bookmarks are private: the list shows only the bookmarks of the user who is logged in, whatever user the address names.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Bookmarks of messages | Kunena configuration, tab *Frontend* | Yes |

With the option off, the button and the link are not shown and the list answers *not found*; the bookmarks stay in the database.

## Data

The bookmarks are deleted with their messages, topics and categories, and with a user who is deleted; the privacy tools of Joomla
export and remove them, and the diagnostics of Kunena (*bookmarkOrphaned*) find the bookmarks of messages which do not exist any
more.

---

[← All the new features of Kunena 8.0](../README.md)
