# New options of the Aurelia template

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Administrators

An overview of the options which the Aurelia template has now.

![The tab Features of the Aurelia template](../images/admin-template-features.png)

| Option | Choices | Default | Page |
| --- | --- | --- | --- |
| Reload topic lists and the index in the background | No, Yes | Yes | [Background reload](background-reload.md) |
| Seconds between reloads | number (minimum 10) | 30 | [Background reload](background-reload.md) |
| Order of the attachments | ID, Date, Name, Size, Type | ID | [Attachments order](attachments-order.md) |
| Direction of the attachments order | Ascending, Descending | Ascending | [Attachments order](attachments-order.md) |
| Show the first post on every page | No, Yes | No | [First post](first-post-on-every-page.md) |
| Text under the topic icons | No, Yes | Yes | [Topic status](topic-status-solved.md) |
| Show badges | No, Yes | No | [Badges](badges.md) |
| Language of a new topic | pick, hide (page), hide (category) | pick | [Multilanguage](multilanguage.md) |
| Suggest similar topics | No, Yes | No | [Similar topics](similar-topics.md) |
| Number of similar topics | 1 - 10 | 5 | [Similar topics](similar-topics.md) |

The options are kept in the file `config/params.ini` of the template. Kunena keeps them in a cache of its own, which is made again when the
options are saved in the administration.

---

[← All the new features of Kunena 7.2](../README.md)
