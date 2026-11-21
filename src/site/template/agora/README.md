# Agora Template Structure

Agora is a modern look for Kunena (cards, clear type, light and dark mode, mobile first) built on the template Aurelia.

## Parent template

`template.php` declares `protected mixed $default = ['aurelia'];`: every layout, page, script and image which Agora does not
have itself is taken from Aurelia. Agora so keeps all the features of Aurelia (BBCode, the editors, polls, attachments, the inbox,
the notifications, the mentions, the link cards, the moderation tools, the profiles, ...) and only replaces the files listed
below. A new feature of Aurelia is in Agora at once; it only needs styles in Agora when it looks wrong there.

## ./config

`config.xml` has every option of Aurelia (with the same start values, see `AgoraOptionsTest`) and the options of Agora in the tab
Colors: the style (the look, see `./assets/scss/styles`), the layout (`layoutSet`, the HTML, see `./layoutsets`), the colour scheme
(auto: the light or dark colours of the site template around the forum, light or dark) and the accent colour. The style and the
layout are chosen apart: every layout goes with every style. With a style other than Default, the style keeps its own accent
while the option is still `#3d5afe` (`KunenaTemplateAgora::DEFAULT_ACCENT`); another colour in the option wins over the accent
of the style. The scheme auto follows the site template, not the device: the `data-bs-theme` of the page (`html`, `body` or another
ancestor of `#kunena`), else the colour of its background. The scheme is chosen by the administrator only: the forum has no
switcher for the visitors.

## ./layouts

Only the layouts which Agora changes:

- `page/default.php`, `page/offline.php`, `page/unauthorized.php`: the element `#kunena` gets the class `agora` and the colour
  scheme of the options (`data-bs-theme` of Bootstrap); with the scheme auto, a small script gives it the colours of the site
  template (`data-bs-theme` of the page, else its background) before the page is shown.
- `widget/menubar/default.php`: the menu bar.
- `widget/statistics/default.php`: the statistics of the index as tiles, as on the statistics page, then the activity of
  today and yesterday, the latest member and a link to the statistics page.
- `announcement/edit/default.php`: the form of an announcement in one card, the dates and the choices side by side.
- `user/item/default.php`: the header of the profile as a card.

Each of them must replace a layout of Aurelia of the same name (tested by `AgoraOptionsTest`).

## ./pages

- `topics/default/default.php`: the lists of topics without who is online and the statistics, which Agora shows only on the
  index of the forum.

## ./assets/scss

`agora.scss` imports the styles of Aurelia, then the ones of Agora, all inside `#kunena.layout.agora`:

- `tokens.scss`: the colours, the radius, the shadows and the spaces as CSS variables, for light and for dark. The accent is only
  on the main button and the links; the neutrals are tinted with it. Every space is on the 8px scale (4px is the only half step),
  every card has 1rem inside, one radius for all that is not round, 1px borders. The variables of Bootstrap (`--bs-*`) and
  the ones of the build of Bootstrap of Cassiopeia (without prefix) get the same colours.
- `base.scss`: type, links, buttons, badges, forms, dropdowns, modals, pagination, tabs and tables.
- `navigation.scss`, `forum.scss`, `topic.scss`, `profile.scss`, `editor.scss`: the parts of the forum.
- `mobile.scss`: small screens (tables as lists of cards, the author above a message, no horizontal scrolling at 360px).
- `custom.scss`: your own styles (kept during an upgrade).

The style sheet is compiled to `media/kunena/cache/agora/css/kunena.css` when a source of Agora or of Aurelia changed.

### ./assets/scss/styles

The styles of the option style, the look only, one file each, imported after the base so that they win. `#kunena` has the
attribute `data-agora-style` (`KunenaTemplateAgora::getSchemeAttributes()`), and each file only sets the tokens of `tokens.scss`
(for light and for dark) under `#kunena.layout.agora[data-agora-style="<style>"]`, plus a few rules where the look needs more than
tokens (pill badges, round buttons, the weight of the headings). A style has no rule of a layout. No style loads a web font: a font
is used only when the device has it.

- `_soft.scss`: white surfaces without borders on a light lavender tint, an indigo accent, large radii, pill badges.
- `_midnight.scss`: deep blue with a lime accent, always dark (`getColorScheme()` returns dark), dark text on the main button.
- `_compact.scss`: smaller type and spaces, tighter table cells, thin borders, no shadows, a green accent; the touch sizes stay.
- `_conversation.scss`: a teal accent on a cool grey.
- `_nordic.scss`: warm stone greys, a sage accent, no shadows, more room.
- `_tonal.scss`: surfaces tinted with the accent (also an accent of the options), large radii, round buttons and badges.

A new style needs its value in `KunenaTemplateAgora::STYLES`, an option in `config.xml` with its language key, and its file
imported in `agora.scss` (tested by `AgoraStyleTest`).

### ./assets/scss/layoutsets

The rules of the layouts of the option `layoutSet`, one file each, under `#kunena.layout.agora[data-agora-layout="<layout>"]`
(the attribute of `#kunena`). They only use the tokens, never a colour of their own (tested by `AgoraStyleTest`), so the look comes
from the style. `_card-grid.scss` has the mixin of the cards, used by the layouts Cards and Profile.

## ./layoutsets

The layouts of the option `layoutSet`: `layoutsets/<layout>/layouts/...` and `layoutsets/<layout>/pages/...` replace the layout or
the page of the same name of Agora or of Aurelia, only for that layout. `KunenaTemplateAgora::getTemplatePaths()` puts the folder of
the chosen layout right before the one of Agora, so the overrides of the site template (`html/com_kunena`) still win. Each one
starts from the layout it replaces and keeps all of its features (moderation, pagination, quick reply, modules...).

- `cards/layouts/topic/row` (`default`, and `category` and `user` which include it): a topic as a card; the lists of topics are a
  grid of cards. On a small screen the button for a new topic floats at the bottom right.
- `columns/layouts/topic/item`: the head of the topic (the accepted answer, the lock and the tags as chips, the title, one line of
  numbers) and two columns, with a side column for the numbers of the topic, the search, the moderators and the forum jump (under
  the messages on a small screen).
- `dense/layouts/topic/row` (the same three as Cards): a topic in one dense row: a dot for its state, the title, one line of meta,
  the numbers. `dense/pages/topics/default`: the lists of topics with a side column of filters (the modes of the list, my topics).
- `chat/layouts/topic/item`: three panes, the latest topics of the category, the conversation, the people who wrote on the page.
  `chat/layouts/topic/item/message`: a message as a line of a chat, a small avatar beside a bubble with the name in it; the
  messages of the logged in user on the other side.
- `profile/layouts/user/item`: the profile in two columns, the card of the user beside the tabs (above them when there is no room).
  `profile/layouts/message/row` and `profile/layouts/topic/row/user.php`: the posts and the topics of a profile as cards.
- `app/layouts/widget/menubar`: the menu always open, as tabs; on a small screen a bar fixed at the bottom of the screen.
  `app/pages/category/list`: the index starts like the home of an app: a greeting, tiles (new topics of the last day, the own
  posts, the thanks), the latest activity.

The data of these layouts comes from the helpers which check the access (`KunenaTopicHelper::getLatestTopics()` lists only the
topics of the categories the visitor may read) or from what the page already has (the messages of the page, the visitor).

A new layout needs its value in `KunenaTemplateAgora::LAYOUTS`, an option of `layoutSet` in `config.xml` with its language key, its
folder in `layoutsets/` and its file in `assets/scss/layoutsets/` imported in `agora.scss` (tested by `AgoraStyleTest`).

## ./assets/js

`agora.js`: the text of SCEditor (in a frame with its own style sheet) gets the colours of the scheme, and with the scheme auto
the colours follow the site template when it changes them (`data-bs-theme` of `html` or `body`, or its own colours after a change
of the colours of the device). The other scripts are the ones of Aurelia (`KunenaTemplateAgora::addScript()`).
