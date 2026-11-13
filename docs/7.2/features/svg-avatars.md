# SVG avatars, cleaned on upload

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Administrators, users · **Issue:** [#10381](https://github.com/Kunena/Kunena-Forum/issues/10381)

A user can upload an SVG file as his avatar, when the administrator allows it.

To enable it, add `svg` to **Allowed avatar types** in the configuration of Kunena (for example
`gif,jpeg,jpg,png,webp,avif,svg`). It is not in the default list.

- The uploaded file is cleaned before it is saved: scripts, event attributes (`onload`, `onclick`...), `javascript:` links,
  `foreignObject` elements and references to remote files are removed. Only the cleaned file is stored, as
  `media/kunena/avatars/users/avatar<user id>.svg`.
- A file which cannot be cleaned safely (a file which is not an SVG document, or which declares XML entities) is refused with an
  error, and the previous avatar stays.
- The size limit of avatars (**Max. Avatar File Size (KB)**) applies to SVG files too.
- SVG avatars are not resized: the same file is shown at every size, in an `<img>` tag.
- SVG is allowed only for avatars. Attachments, private message files, category icons and the avatar gallery still refuse it.

The cleaning uses the bundled [svg-sanitizer](https://github.com/darylldoyle/svg-sanitizer) library (GPL-2.0-or-later).

---

[← All the new features of Kunena 7.2](../README.md)
