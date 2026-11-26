# Bluesky posts

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Administrators, users

A link to a Bluesky post is shown as the post, in the frame of Bluesky.

A pasted link of the form `https://bsky.app/profile/<handle>/post/<key>` (the address of the button *Copy link to post* of
Bluesky) is shown as the post, with its pictures and its counts, so the visitors read it without leaving the forum. The frame
takes the height of the post.

The frame of Bluesky needs the account of the author (its DID), not the handle: the forum asks the public API of Bluesky
(`public.api.bsky.app`) for the DID of a handle once and keeps it a week, an unknown handle too. A link with the DID
(`/profile/did:plc:.../post/...`) asks nothing. A link whose author is not found stays a link.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Auto Embed Bluesky posts | Kunena configuration, *Embeds and maps* › *Embedded media* | Yes |

---

[← All the new features of Kunena 8.0](../README.md)
