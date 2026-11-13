# Change the rank of a user from his profile page

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Moderators, administrators · **Issue:** [#5380](https://github.com/Kunena/Kunena-Forum/issues/5380)

The rank of a user can be changed in the forum, on his profile page, without opening the backend.

The rank entry of the profile shows a list of ranks and a button **Change rank** when you may change it. The entry is shown when
the options **Show User Statistics** and **Show Ranking** are on.

- The administrators of the forum may change the rank of every user, their own rank and the rank of another administrator.
- The global moderators may change the rank of the other users, but not their own rank and not the rank of an administrator.
- The moderators of a category do not get the list.
- The list offers the special ranks (the ranks marked **Special** in the backend), the same ranks as the user form of the
  backend, plus **No rank given (automatic rank)**.
- **No rank given** removes the rank given by hand: the user gets his automatic rank again, by his user group or by his number
  of messages.
- The change is the same as in the backend: the rank of the user is saved, nothing else of his profile changes.

---

[← All the new features of Kunena 8.0](../README.md)
