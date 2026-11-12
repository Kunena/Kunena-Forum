# Moderator groups for a category

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Administrators · **Issue:** [#3400](https://github.com/Kunena/Kunena-Forum/issues/3400)

A category can be moderated by Joomla user groups, not only by users assigned one by one.

The setting **Moderator groups** is in the Moderators tab of the category form in the backend. Select one or more user groups.

- Every member of a selected group, and of its child groups, moderates the category.
- The groups moderate this category only, not its subcategories.
- The members are moderators in every way: they appear in the list of moderators of the category in the forum and receive the
  moderator e-mails of the category.
- The right follows the membership: a user who is added to or removed from a group is a moderator or not on his next page.
- Guests and banned users are never moderators, even when they are in a selected group.
- The moderators assigned by hand stay, and are listed apart below the setting. A user who is both is a moderator once.
- The setting is kept in the parameters of the category (no database change). It needs the plugin Kunena - Joomla.

---

[← All the new features of Kunena 7.2](../README.md)
