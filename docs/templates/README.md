# Template overrides

## HMVC templates

Template file overrides are searched in the following order (assuming they extend Crypsis):

- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/[TEMPLATE]/topics/default.php (TODO)
- ./components/com_kunena/template/[TEMPLATE]/layouts/topics/default.php
- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/crypsis/topics/default.php (TODO)
- ./components/com_kunena/template/crypsis/layouts/topics/default.php
- ./templates/[JOOMLATEMPLATE]/layouts/com_kunena/topics/default.php (TODO)
- ./layouts/com_kunena/topics/default.php (TODO)

## Legacy templates

Legacy template file overrides are searched in the following order (assuming they extend Blue Eagle):

- ./templates/[JOOMLATEMPLATE]/html/com_kunena/[TEMPLATE]/topics/default.php
- ./templates/[JOOMLATEMPLATE]/html/com_kunena/topics/default.php
- ./components/com_kunena/template/[TEMPLATE]/html/topics/default.php
- ./components/com_kunena/template/blue_eagle/html/topics/default.php
