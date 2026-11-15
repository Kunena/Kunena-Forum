# REST API (read only)

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Developers, administrators

The categories, topics and posts of the forum through the web services of Joomla (JSON:API), read only.

## Endpoints

The API is at `/api/index.php/v1/kunena/` of the site. All the endpoints use the method `GET`.

| Endpoint | What it gives | Filter |
| --- | --- | --- |
| `v1/kunena/categories` | The published categories, in the order of the forum | `filter[parent]=<id>`: the children of a category |
| `v1/kunena/categories/<id>` | A category | |
| `v1/kunena/topics` | The published topics, the last active first | `filter[category]=<id>` |
| `v1/kunena/topics/<id>` | A topic | |
| `v1/kunena/posts` | The published posts, the newest first | `filter[topic]=<id>`: the posts of a topic, in their order |
| `v1/kunena/posts/<id>` | A post | |

The lists have the pages of Joomla: `page[offset]` and `page[limit]` (at most 100, 20 by default), with the links `next`, `previous`,
`first` and `last` and the meta `total-pages`.

Example:

```bash
curl "https://example.org/api/index.php/v1/kunena/topics?filter[category]=2&page[limit]=5"
```

### The fields

- **Category**: `id`, `parent_id`, `name`, `alias`, `description`, `topics`, `posts`, `last_post_id`, `last_post_time`, `url`.
- **Topic**: `id`, `category_id`, `subject`, `author_id`, `author`, `first_post_id`, `first_post_time`, `replies`, `hits`, `locked`,
  `sticky`, `answer_id` (the [accepted answer](accepted-answer.md)), `last_post_id`, `last_post_time`, `url`.
- **Post**: `id`, `topic_id`, `category_id`, `parent_id`, `subject`, `author_id`, `author`, `time`, `modified_time`, `message` (the
  BBCode), `text` (the text without BBCode), `url`.

The times are in UTC (ISO 8601); `url` is the address of the page of the forum.

## Who reads what

The API gives what the user may read on the site, with the permissions of the forum:

- **Without a token** (the option *Readable without a token* of the plugin, on by default): the request reads as a guest.
- **With the API token of a user** (header `X-Joomla-Token`, the plugin *API Authentication - Web Services Joomla Token*): the request
  reads what this user may read.

Only the published categories, topics and posts are given. The text of a post never has its confidential, private and hidden parts
(`[confidential]`, `[private]`, `[hide]`), and a guest gets no text for the replies of a category which hides them from the guests.
Something which the user may not read is answered *not found* (404).

## Install

The plugin **Web Services - Kunena Forum** (group *webservices*) is installed with Kunena and has to be enabled, as the other web
services of Joomla. Its option:

| Option | Default |
| --- | --- |
| Readable without a token | Yes |

Writing (new topics, replies) is not in this version of the API.

---

[← All the new features of Kunena 8.0](../README.md)
