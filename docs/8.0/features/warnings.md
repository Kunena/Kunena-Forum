# Warnings with points

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Moderators, users, administrators

Moderators give warnings with points to the author of a message. The points count until they expire; when the active points of a
user reach a threshold, he is banned automatically.

## Give a warning

The moderation actions of a message have a **Warn** button for the moderators of its category. It opens a box with:

- **Points**: 1 to 5;
- **Reason**: required; the user sees it.

A moderator may warn the users whom he may ban: not himself, not an administrator, and a moderator only when he is an administrator.

After the warning, the message tells the moderator the active points of the user. When they reach the threshold of the automatic ban,
the user is banned for the days of the setting (a ban of Kunena, not a block of Joomla, with the reason *Automatic ban: N points of
warnings.*); a user who is already banned keeps his ban.

## The warnings of a user

The profile has a tab **Warnings**:

- for the moderators who may warn the user, always;
- for the user himself, when he has warnings.

It shows the active points, the threshold of the automatic ban, and each warning: its date, points, reason, the moderator, a link to
the message and when it expires. An expired warning is shown in grey and does not count. A moderator can **Remove** a warning: its
points do not count any more.

The moderation log keeps the warnings which are given and removed (*User Warned*, *User Warning Removed*), and the automatic bans.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Warnings with points | Kunena configuration, tab *Frontend* | Yes |
| Points expire after (days) | same tab | 30 (0: never) |
| Ban at (points) | same tab | 10 (0: no automatic ban) |
| Length of the automatic ban (days) | same tab | 7 (0: permanent) |

## Data

The warnings of a user stay when his message is deleted (the link to the message goes). They are deleted with the user; the privacy
tools of Joomla export and remove them.

---

[← All the new features of Kunena 8.0](../README.md)
