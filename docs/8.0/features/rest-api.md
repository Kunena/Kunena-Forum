# REST API

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Developers, administrators

The categories, topics and posts of the forum through the web services of Joomla (JSON:API), and the replies of the users.

## Endpoints

The API is at `/api/index.php/v1/kunena/` of the site. The endpoints which read use the method `GET`; a reply is posted with
`POST v1/kunena/posts` ([Post a reply](#post-a-reply)).

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

## Post a reply

`POST v1/kunena/posts` posts a reply of the user of the API token. It is never public: without a token the answer is 401.

```bash
curl -X POST "https://example.org/api/index.php/v1/kunena/posts"   -H "X-Joomla-Token: <the token of the user>" -H "Content-Type: application/json"   -d '{"topic_id": 62, "message": "Thank you, this works.", "subscribe": true}'
```

The body (flat, or as JSON:API in `data.attributes`):

| Field | |
| --- | --- |
| `topic_id` | The topic: the reply answers its first message. |
| `parent_id` | Or the message which the reply answers (with `topic_id`, it must be a message of that topic). |
| `message` | The text, in BBCode. |
| `subscribe` | Optional: subscribe the user to the topic. |

The reply has the rules of the form of the site: the permission to reply (a locked topic, a banned user...), the flood protection
(for a reply too, by user), an identical reply of the same user within 5 minutes, the links of the new users and the maximum of
links, the moderation of the category and of the new users, and the spam check of the plugins. The subscribers and the mentioned
users get their notification, with the address of the message on the site.

| Answer | When |
| --- | --- |
| 201 | The reply is posted: the post, as `GET v1/kunena/posts/<id>` gives it. |
| 202 | The reply waits for a moderator: `{"success": true, "message": "...", "data": {"id": ..., "status": "held"}}`. |
| 400 | No topic, no text, too many links, or the reply could not be saved. |
| 403 | The user may not reply; a new user with links or images; the spam check rejected it; a user who gets a captcha on the site. |
| 404 | The topic or the message does not exist, or the user may not read it. |
| 409 | The same reply was posted a moment ago. |
| 429 | The flood protection: the user must wait. |

The error has the text of the forum in the language of the site (`errors[0].title`). The API cannot show a captcha: a user who gets
one on the site (a guest, or a user with fewer posts than the option of the captcha) posts on the site. New topics, edits and
deletes are not in the API: they answer 404 (no route) or 405.

---

[← All the new features of Kunena 8.0](../README.md)
