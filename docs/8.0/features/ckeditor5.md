# CKEditor 5

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Administrators, users

The CKEditor of the templates is now CKEditor 5. CKEditor 4 is no longer maintained, and it is no longer part of Kunena.

The messages are still stored as BBCode. The editor shows a message formatted, and it writes the same BBCode back when the message is
sent. A message that is opened and sent without a change of its text is sent exactly as it was written.

## What changes for the users

- The toolbar has the formatting of CKEditor 5: bold, italic, underline, strikethrough, subscript, superscript, the colour and the size
  of the text, the alignment, lists, quotes, code blocks, links and images. The buttons of Kunena are in the same toolbar: the spoiler,
  hidden text, confidential text, the map, the video, the emoticons, the poll, and the embeds of eBay, Instagram, X and SoundCloud.
- **Source** shows the message as BBCode text, and it can be edited there.
- Typing `@` and some letters lists the users who can be [mentioned](mentions.md).
- **New window** in the form of a link writes `target=_blank` ([links in a new window](link-target.md)).
- A `[code]` block is shown as a code block, and its text stays as written ([code highlighting](code-highlighting.md)).
- An emoticon is inserted as its code, for example `:D`, and is shown as an image in the message.
- Pasting an image into the editor is refused, with a warning: an image is added as an attachment.
- A tag the editor does not know, for example `[spoiler]` or `[attachment=12]`, stays in the text as it was written.

## For administrators

- **Editor buttons** in the template settings still hides buttons. It accepts both the names of CKEditor 4 and the names of SCEditor.
  For example, `Subscript,Superscript` hides those two buttons.
- The template settings for the CKEditor skin and for the prefix of a custom CKEditor configuration file are removed. CKEditor 5 takes its
  colours from the template. The Agora template matches its own colours, light and dark.
- The editor uses the language of the user when CKEditor 5 has it, and English otherwise.
- The language packs no longer have a `kunena_ckeditor.js` file: the texts of the buttons of Kunena are in the language files of
  Kunena.

## For developers

- The source of the editor is in `editor/ckeditor5/src`:
  - `bbcode.js` converts between BBCode and the HTML of the editor;
  - `kunena-editor.js` holds the plugins of Kunena and the `window.KunenaEditor` API;
  - `dialogs.js` holds the map, video, emoticon and poll dialogs.
- `npm run build:editor` builds it with esbuild into `media/kunena/core/js/ckeditor5`, with the translations of CKEditor 5.
- `npm run test:js` runs the tests of the converter (`tests/Js`). With `KUNENA_BBCODE_CORPUS` pointing to a JSON list of messages, it
  also checks that each one comes back unchanged.
- Scripts reach the editor of a textarea through `window.KunenaEditor`:
  - `get(id)`, and `ready(id)`, which returns a promise of the editor;
  - on an editor: `getData()`, `setData()`, `insertText()`, `removeText()`, `updateElement()`, `setSourceMode()`,
    `setCommandEnabled()` and `on('change')`.
- The Cypress specs use the `editor_*` commands (`tests/System/support/commands/editor.mjs`).

---

[← All the new features of Kunena 8.0](../README.md)
