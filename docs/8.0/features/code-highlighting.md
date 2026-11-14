# Code highlighting in [code] blocks

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Administrators and users · **Issue:** [#6823](https://github.com/Kunena/Kunena-Forum/issues/6823)

The code in `[code]` blocks is coloured by highlight.js, bundled with Kunena.

A `[code]` block is shown as a standard `<pre><code>` block in the "Code:" box, with its line breaks and its indentation. With the option
*Enable Code Highlighting* on, the code is coloured by [highlight.js](https://highlightjs.org/), in the language the tag names or, without
one, in the language highlight.js detects. Whatever is typed inside the block stays text: HTML, BBCode, smileys and links are shown as they
were written.

## How it works

- The tag names the language in one of two ways: `[code=php]...[/code]` or `[code type=php]...[/code]`. `[code]...[/code]` names none,
  and highlight.js guesses it. When both are given, `type` wins.
- A language name is lowercase letters, digits, `_` and `-`, at most 32 characters; uppercase is read as lowercase (`PHP` is `php`). The
  names and aliases of highlight.js work, for example `php`, `js`, `html`, `xml`, `css`, `sql`, `sh`, `bash`, `py`, `java`, `cs`, `json`
  and `yaml`. Any other name (`c++`, a name with a space or a quote) is ignored, and the block is shown without a language.
- A language highlight.js does not know leaves the block plain, uncoloured.
- highlight.js 11.11.1 (the official build with the common languages) and its `github-dark` theme are bundled in
  `media/kunena/core/highlightjs` and served by your own site: no CDN is used. The dark theme gives the block its own background, so it
  reads the same on a light and on a dark page.
- The files are loaded only on a page which shows a code block, and only when the option is on. With the option off the block keeps the
  look of the template, with its indentation, and no script is loaded. In Aurelia a block that is not coloured is shown on a light
  background, with padding.
- E-mails (the subscriptions) and feeds get the plain block, without colours and without a script. The plain text of an e-mail shows
  `Code:` and the code.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Enable Code Highlighting | Kunena configuration, tab *BBCode Settings* | No |

---

[← All the new features of Kunena 8.0](../README.md)
