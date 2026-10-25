# Agora Template Structure

Agora is a modern look for Kunena (cards, clear type, light and dark mode, mobile first) built on the template Aurelia.

## Parent template

`template.php` declares `protected mixed $default = ['aurelia'];`: every layout, page, script and image which Agora does not
have itself is taken from Aurelia. Agora so keeps all the features of Aurelia (BBCode, the editors, polls, attachments, the inbox,
the notifications, the mentions, the link cards, the moderation tools, the profiles, ...) and only replaces the files listed
below. A new feature of Aurelia is in Agora at once; it only needs styles in Agora when it looks wrong there.

## ./config

`config.xml` has every option of Aurelia (with the same start values, see `AgoraOptionsTest`) and the options of Agora in the tab
Colors: the colour scheme (auto: as the device of the visitor, light or dark) and the accent colour. The scheme is chosen by the
administrator only: the forum has no switcher for the visitors.

## ./layouts

Only the layouts which Agora changes:

- `page/default.php`, `page/offline.php`, `page/unauthorized.php`: the element `#kunena` gets the class `agora` and the colour
  scheme of the options (`data-bs-theme` of Bootstrap); with the scheme auto, a small script gives it the colours of the device
  before the page is shown.
- `widget/menubar/default.php`: the menu bar.
- `user/item/default.php`: the header of the profile as a card.

Each of them must replace a layout of Aurelia of the same name (tested by `AgoraOptionsTest`).

## ./assets/scss

`agora.scss` imports the styles of Aurelia, then the ones of Agora, all inside `#kunena.layout.agora`:

- `tokens.scss`: the colours, radii and shadows as CSS variables, for light and for dark. The variables of Bootstrap (`--bs-*`) and
  the ones of the build of Bootstrap of Cassiopeia (without prefix) get the same colours.
- `base.scss`: type, links, buttons, badges, forms, dropdowns, modals, pagination, tabs and tables.
- `navigation.scss`, `forum.scss`, `topic.scss`, `profile.scss`, `editor.scss`: the parts of the forum.
- `mobile.scss`: small screens (tables as lists of cards, the author above a message, no horizontal scrolling at 360px).
- `custom.scss`: your own styles (kept during an upgrade).

The style sheet is compiled to `media/kunena/cache/agora/css/kunena.css` when a source of Agora or of Aurelia changed.

## ./assets/js

`agora.js`: with the scheme auto, the colours follow the device when it changes them. The other scripts are the ones of Aurelia (`KunenaTemplateAgora::addScript()`).
