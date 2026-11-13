# Moderation log in the User Actions Log of Joomla

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Administrators

The moderation actions of the forum can also be written to the User Actions Log of Joomla (Users → User Actions Log), next to the
log of Kunena itself.

## How it works

- Every moderation entry of the Kunena log (lock, move, delete, approve, ban, block, an announcement...) is copied to the User
  Actions Log as extension *Kunena Forum*, when the entry is written.
- What is copied: the moderator (user name and id, with a link to the user in the backend), the action, and the topic (subject and
  id), the category (name and id) and the user the action was about (user name and id), when the action has them.
- What is never copied: the reasons (also the private and the public reason of a ban), the comments, the expiration and the
  options of a ban, the options of a move, the other data of the entry, the IP address and the time of the Kunena entry, and the
  e-mail address or the real name of anyone. Joomla stamps its own date, and records an IP address only when its own option
  *IP logging* is on.
- Two settings are needed:
  - in Kunena, *Define if you want to log action or moderation* and, below it, *Copy moderation actions to the User Actions
    Log* (both off by default);
  - in the options of User Actions Log, *Kunena Forum* selected in *Events To Log*.
- The log of Kunena itself is unchanged: the table, the Logs page of the backend, the frontend moderation log and the privacy
  export work as before. A failure of the User Actions Log never stops a moderation action or the Kunena log.
- The install and the update of Kunena add *Kunena Forum* to the extensions of the User Actions Log. The uninstall removes it
  there, but keeps the entries which were already written: they can be purged in User Actions Log.

---

[← All the new features of Kunena 8.0](../README.md)
