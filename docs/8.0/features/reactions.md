# Reactions on messages

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, administrators

Users react to the messages of other users with an emoji of a short list: each message shows the reactions it got, with their
numbers.

## React to a message

Under each message of another user there is a **+** button with the list of the reactions. A user has one reaction per message:

- choosing a reaction gives it to the message;
- choosing another one replaces it;
- choosing the same one again (or clicking its pill) removes it.

The reactions which a message got are shown as pills with their numbers, in the order of the list; the reaction of the user has the
colour of the links. Everybody sees them, guests too, also in a locked topic. Reacting needs a user who is logged in and not banned,
on a published message of another user in a topic which is not locked.

Reactions are next to Thank you: both can be used.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Reactions | Kunena configuration, *Community* › *Thank you, karma and reactions* | 👍 ❤️ 😂 😮 😢 🎉 |

The emoji are separated by spaces, at most 8. An empty field turns the reactions off; the reactions which were given stay in the
database and come back when the field is filled again. A reaction which is removed from the list stays on the messages (after the
others) but cannot be given any more.

## Data

The reactions are deleted with their messages, topics and categories, and with a user who is deleted. The privacy tools of Joomla
export and remove the reactions of a user, and the diagnostics of Kunena (*reactionOrphaned*) find the reactions of messages which do
not exist any more.

---

[← All the new features of Kunena 8.0](../README.md)
