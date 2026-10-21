# How the pictures are made

The pictures in [`images/`](images) are not made by hand. The Cypress specs in [`tests/System/docs`](../../tests/System/docs) log in to the
test site, put the forum in the state of the picture, and take the screenshot, so the pictures can be made again when the forum looks
different.

## What you need

- The Joomla site of the Cypress tests (`localhost/tests`) with Kunena linked to this checkout, and its database on the 7.2 schema. See
  [tests/README.md](../../tests/README.md), the part *Cypress against the checkout*.
- Google Chrome. The pictures are taken in a big window (the options in `cypress.docs.config.js`), which Electron does not give.
- The users `ci-admin` (the administrator of the test site) and `ci-user`, which the specs of the tests make.

## Make the pictures

```bash
KUNENA_CMS_PATH=E:/www/apache/htdocs/tests npx cypress run --browser chrome --headless --config-file cypress.docs.config.js
```

One spec makes a group of pictures:

| Spec | Pictures |
| --- | --- |
| `config-options.cy.js` | the row of each new option of the configuration (`option-<name>.png`) |
| `backend.cy.js` | the pages of the administration and of the plugins (`admin-*.png`) |
| `frontend-admin.cy.js` | the forum as the administrator: index, lists, log, announcements, profile |
| `frontend-user.cy.js` | the forum as a user: drafts, popup, follow, ignore, tags, badges, inbox |
| `frontend-topic.cy.js` | a topic: the ban box, link cards, players, map, mention, Markdown |
| `frontend-template.cy.js` | options of the template: icon names, the first post on every page |

The pictures are saved as `docs/7.2/images/<name>.png`. Run one spec with `--spec tests/System/docs/<name>.cy.js`.

## What the specs change

Every spec puts back what it changed: the options of Kunena in the database (`cy.kunena_setOption`), the file of the options of the Aurelia
template (`config/params.ini`), and the rows it added (follows, ignores, tags, drafts, messages). The sample forum (categories, topics and
users with a readable text) is data of the test site.

## Making a new picture

1. Add a test to the spec of the group, set the state with the helpers of `tests/System/docs/helpers.mjs` (`open`, `forum`,
   `addMessage`, `templateOptions`) and call `forum('<name>', '<selector>')`. `forum` cuts the picture from a tall window; give it a list of
   selectors for a picture of more elements (a button and its menu).
2. Run the spec and look at the picture. Mention it in the page of the feature with `![text](../images/<name>.png)`.
