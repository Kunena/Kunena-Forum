# Template overrides

Upgrading a template or overrides from Kunena 7: see [Upgrading a template to Kunena 8.0](upgrading-to-8.0.md), the scripts,
layouts, helpers, settings and strings which Kunena 8.0 removed, and what replaces them.

The module positions of the forum, where each is shown and the target of the content plugins of its modules: see
[Module positions](module-positions.md).

## HMVC templates

A layout is searched in this order, the first file found is used (for a Kunena template [TEMPLATE] which extends Aurelia):

- ./templates/[JOOMLATEMPLATE]/html/com_kunena/layouts/topic/item/default.php: an override for every Kunena template
- ./templates/[JOOMLATEMPLATE]/html/com_kunena/[TEMPLATE]/layouts/topic/item/default.php: an override for [TEMPLATE] only
- ./components/com_kunena/template/[TEMPLATE]/layouts/topic/item/default.php
- ./templates/[JOOMLATEMPLATE]/html/com_kunena/aurelia/layouts/topic/item/default.php: an override for Aurelia and the templates
  built on it
- ./components/com_kunena/template/aurelia/layouts/topic/item/default.php
- ./components/com_kunena/template/system/layouts/topic/item/default.php

The pages are searched the same way (`pages/` in place of `layouts/`). With Agora, the chosen layout set
(`template/agora/layoutsets/<set>/layouts/...`) comes right before Agora itself, after the overrides of Agora.

## Legacy templates

Legacy template file overrides are searched in the following order (assuming they extend Blue Eagle):

- ./templates/[JOOMLATEMPLATE]/html/com_kunena/[TEMPLATE]/topics/default.php
- ./templates/[JOOMLATEMPLATE]/html/com_kunena/topics/default.php
- ./components/com_kunena/template/[TEMPLATE]/html/topics/default.php
- ./components/com_kunena/template/blue_eagle/html/topics/default.php

## Modified files

The build writes a `checksums.json` into the folder of each shipped template (`aurelia`, `agora`, `system`): the SHA-256 of
every file of the template by its path relative to the folder, computed after the replacement of the tokens such as
`@kunenaversion@`. `config/params.ini` (the settings of the site) is not in the list.

The tab **Modified files** of the edit screen of a template in the backend compares the files on the site with that list. It
shows the files whose content differs (*Modified*) and the listed files which are gone (*Missing*). Files added to the folder
are not detected.

A site running from a git checkout, or a third-party template, has no `checksums.json`: the tab then says that no checksum list
is available. To generate the lists for a folder of templates, for example a copy of `src/site/template`:

```bash
php scripts/template-checksums.php <folder of templates>
```

It writes `checksums.json` into each direct subfolder of the folder.
