# Template overrides

## HMVC templates

Template file overrides are searched in the following order (assuming they extend Aurelia):

- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/[TEMPLATE]/topics/default.php (TODO)
- ./components/com_kunena/template/[TEMPLATE]/layouts/topics/default.php
- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/aurelia/topics/default.php (TODO)
- ./components/com_kunena/template/aurelia/layouts/topics/default.php
- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/topics/default.php (TODO)
- ./layouts/com_kunena/topics/default.php (TODO)

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
