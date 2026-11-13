# Other users of the same IP address in the topic

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Moderators, administrators · **Issue:** [#5626](https://github.com/Kunena/Kunena-Forum/issues/5626)

The IP address of a message gets a badge when other registered users posted from the same address.

The IP address of a message, which moderators and administrators see in the topic, gets a small **badge** next to it when other
registered users posted messages from that address too. It helps to spot a second account of the same person, as the IP tab of a
user in the backend does.

- **The badge** shows the number of the other users. Its tooltip (and the text for screen readers) names them with their number of
  messages from that address: *Other users posted from this IP address: Anna (12), Bob (3)*. After 5 names the list ends with
  *and N more*.
- **Who sees it:** the people who see the IP address of the message: the administrators, and the moderators when the option
  *Hide IP Addresses From Moderators* is off. The others see neither the address nor the badge.
- **What is compared:** only the IP address saved with the messages, whatever their state (deleted and unapproved messages
  included). Guests and the author of the message are not counted. The IP address of the registration and of the last visit are
  not compared.
- The addresses of all the messages of a page are looked up in one query.

---

[← All the new features of Kunena 8.0](../README.md)
