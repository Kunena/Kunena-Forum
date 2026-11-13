# The Unread list shows only the unread topics

> **Label:** `bug` · **Since:** Kunena 7.2 · **For:** Users · **Issue:** [#6036](https://github.com/Kunena/Kunena-Forum/issues/6036)

The Unread menu item lists the topics the user has not read yet, and only those.

Before, the list loaded a page of all the topics of the period and hid the read ones: a page could show a few topics or none,
and the number of pages followed all the topics. Now the unread topics are selected before the paging: every page is full
except the last one, and the number of pages follows the unread topics. A topic the user opens leaves the list at the next
reload.

"Unread" is the same as the "new" indicator of the other lists: a topic with a published message newer than the last visit of
the user in the topic, and newer than his *Mark all read* of the forum or of the category.

A guest, or a forum with the option *Show new* off, gets an empty list without page numbers.

---

[← All the new features of Kunena 7.2](../README.md)
