# Warning before leaving the posting form

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users

The posting form asks before it is left with text which was not sent.

The form of a new topic, of a reply and of an edit watches the subject and the message text. When they were changed and the user
leaves the page (a link, the back button, closing the tab), the browser asks whether to leave the page. **Cancel** asks
*Leave this form? The text you have written will be lost.* before it empties the form and goes back.

## How it works

- Only the subject, the message and the private message count. Typing a text and removing it again leaves the form untouched:
  no question.
- **Submit**, **Save draft** and Enter in the subject send the form without a question. When the browser or a script stops
  the sending (an empty subject, for example), the warning stays on.
- **Cancel** on an untouched form goes back at once, as before. With changes it asks once; **OK** empties the form and goes
  back without a second question, **Cancel** keeps the form and its text.
- A form shown again after a post which failed (the text comes back from the server) warns from the start, because the text
  is kept nowhere else. A form opened from a draft does not, because the draft keeps the text.
- The question of the browser is its own and in its own language: browsers do not show a text of the site there.
- It works with both editors of the template, CKEditor and SCEditor. The quick reply is not watched.

## Options

None: the warning is always on.

---

[← All the new features of Kunena 8.0](../README.md)
