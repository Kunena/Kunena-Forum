# Multilanguage support

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Administrators, users

A language for each category and topic, a language filter, and translated categories.

Active only when the Joomla **Language Filter** plugin is on and more than one content language is published.

- Categories and topics get a **language** (default: all languages). The language of a topic is chosen when a new topic is made.
- Category names and descriptions can be **translated** per language (the *Translations* tab of the category form). The originals are
  never overwritten when the category is saved.
- Categories and topics of other languages are hidden in the index, on the category page, in the lists of topics and in the search.
- The lists of topics get a **language filter** (the language of the page by default, or all languages).
- The menu items of the default menu are made for every installed content language.
- Database: language columns on the categories and the topics, and the new table `#__kunena_categories_translations`.

## The language of a new topic (Aurelia template)

| Option | Choices | Default |
| --- | --- | --- |
| Language of a new topic | *The user can pick the language*; *Hide the option (the language of the page)*; *Hide the option (the language of the category)* | The user can pick the language |

The choice is applied on the server, so a hidden option can not be bypassed. A category with a language still gives that language to every
new topic.

---

[← All the new features of Kunena 7.2](../README.md)
