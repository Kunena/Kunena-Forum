# Links in a new window from the CKEditor link dialog

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users · **Issue:** [#7793](https://github.com/Kunena/Kunena-Forum/issues/7793)

The link dialog of CKEditor asks whether a link opens in the same window or in a new window.

When a link to an address is added or edited in CKEditor, the dialog has the choice *Open the link in*: *Same window* (the default) or
*New window*. *New window* writes the link as `[url=https://example.com target=_blank]text[/url]`, *Same window* as
`[url=https://example.com]text[/url]`.

## How it works

- A message which already holds `[url=... target=...]` keeps the parameter when it is edited or quoted in CKEditor, also after a switch to
  the source mode and back. The dialog of such a link shows *New window*, and choosing *Same window* removes the parameter.
- Only whether the parameter is there counts: any value of `target` opens the link in a new window (`target="_blank"`), and the value
  written in the message is never printed in the page.
- Links inside the forum (the addresses of the site itself) always open in the same window, even with *New window*.
- The `rel` of the link does not change: links to other sites get `rel="nofollow noopener noreferrer"`, and links to the
  [own domains](own-domains.md) get no `rel`.
- The choice is shown for links to an address only, not for e-mail links.
- SCEditor does not offer the choice. Its WYSIWYG mode also drops the `target` parameter of a link when the message is sent; its source
  mode keeps it.

---

[← All the new features of Kunena 8.0](../README.md)
