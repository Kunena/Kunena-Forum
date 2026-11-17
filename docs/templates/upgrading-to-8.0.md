# Upgrading a template to Kunena 8.0

This page is for the builders of third-party Kunena templates and of template overrides. It lists what Kunena 8.0 removed or
changed in the code that a template uses: scripts, layouts, HTML helpers, template settings and language strings. Each entry says
what to use instead.

A template that extends Aurelia and overrides only some layouts is affected by the layouts it overrides and by the scripts it loads
itself. The layouts it does not override come from Aurelia and are already up to date.

## Find what your template still uses

Run these searches in the folder of your template (or of your overrides). Each hit points to a section of this page.

```bash
grep -rnE "jQuery|\\\$\(|\\\$\." --include=*.js --include=*.php .
grep -rnE "fancybox|datepicker|jquery\.fileupload|\.fileupload\(|Tribute|tribute\.|sisyphus|[^k]rating\.js|CKEDITOR\.|insertAtCaret|sortablelist" .
grep -rnE "kunenagrid\.(boolean|behavior|order|orderUp|orderDown|checkedOut)|kunenaforum\.sortablelist" .
grep -rnE "widget/datepicker|Widget/Datepicker|kShowAccessType" .
grep -rnE "COM_KUNENA_(BOOTSTRAP_DATEPICKER|AJAXMODAL|DT_|LIB_MOVE|LIB_SAVE_ORDER|LIB_CHECKED_OUT|LIB_CLICK_TO_TOGGLE_STATE)" .
```

## jQuery is no longer loaded

Kunena 8.0 does not load jQuery on the pages of the forum (the template `aurelia` and the templates built on it) nor in the backend.
None of Kunena's scripts uses it.

- A script of your template that uses `jQuery(...)` or `$(...)` stops with `jQuery is not defined`.
- Best: rewrite it in plain JavaScript (`document.querySelector`, `addEventListener`, `fetch`, `element.closest()`). The scripts of
  Aurelia in `components/com_kunena/template/aurelia/assets/js` are examples.
- Quick fix: ask Joomla for jQuery in the `template.php` of your template, before your scripts are added:

  ```php
  Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('jquery');
  ```

- Bootstrap 5 has no jQuery API. Use `bootstrap.Modal.getOrCreateInstance(element).show()` instead of `$(element).modal('show')`,
  and the same for `Tab`, `Collapse`, `Popover` and the other components.
- Test with the debugging of the site switched off. The debug bar of Joomla (plugin System - Debug) loads jQuery for itself, which
  hides a missing jQuery.

## Removed libraries and what replaces them

The files below are gone from `media/kunena/core`. A template that loads one of them gets a 404, and the code that calls it stops.

| Removed | Files | Use instead |
| --- | --- | --- |
| jQuery File Upload, with load-image, canvas-to-blob, iframe-transport and the jQuery UI widget factory | `js/jquery.fileupload*.js`, `js/load-image.all.min.js`, `js/canvas-to-blob.min.js`, `js/jquery.iframe-transport.js`, `js/jquery.ui.widget.js`, `css/fileupload*.css`, `css/jquery.fileupload*.css` | `js/uploader.js` and `css/upload.css`: `KunenaUploader.attach(input, options)`. See [the uploader](#the-uploader). |
| bootstrap-datepicker | `js/bootstrap.datepicker.js`, `js/locales/bootstrap-datepicker.kunena.js`, `css/bootstrap.datepicker*.css` | The date fields of the browser: `<input type="date">` or `type="datetime-local"`. See [date fields](../8.0/features/date-fields.md). |
| rating.js (`window.rating`) | `js/rating.js` | `js/krating.js`: `KunenaRating.create(list, {value, max, onRate})`, with the styles of `css/rating.css`. |
| Tribute (`window.Tribute`) | `js/tribute.js`, `js/tribute.min.js`, `css/tribute.css` | `js/user-autocomplete.js`: `KunenaUserAutocomplete.attach(input, {url, minLength})`. In the editors, the mentions are part of the editor. |
| Sisyphus (`$(form).sisyphus()`) | `js/sisyphus.js` | `js/form-recover.js`: `KunenaFormRecover.protect(form, {timeout})` and `KunenaFormRecover.release(form)`. It reads the texts which Sisyphus kept. |
| Fancybox (`$.fancybox`) | `js/fancybox-min.js`, `css/fancybox*.css` and its images | `js/lightbox.js` and `css/lightbox.css`, loaded by the layout `Widget/Lightbox`. See [the lightbox](../8.0/features/lightbox.md). |
| jQuery UI core and sortable | `js/jquery.ui.core.js`, `js/jquery.ui.sortable.js`, `js/sortablelist.js` | Nothing: the HTML helper `kunenaforum.sortablelist` is removed too. |
| CKEditor 4 (`window.CKEDITOR`) | `js/ckeditor.js`, `js/ckeditor_config.js`, `js/contents.css`, `js/styles.js`, `js/build-config.js`, `js/plugins/`, `js/skins/`, `js/lang/` | CKEditor 5 in `js/ckeditor5/kunena-editor.js`, through `window.KunenaEditor`. See [CKEditor 5](../8.0/features/ckeditor5.md). |
| The copy of Joomla's multiselect script | `js/multiselect-uncompressed.js` | The asset `multiselect` of Joomla: `$wa->useScript('multiselect')`. |
| The WysiBB icon font | `fonts/wysibbiconfont-wb.*` | Nothing: no editor of Kunena uses it. |

### The lightbox

- The links of the images get `data-kunena-lightbox="<group>"`. All the links of a page with the same group are one gallery.
- An override that still prints `data-fancybox` keeps working: Kunena's lightbox opens those links too.
- `window.KunenaLightbox.open(link)` opens the lightbox on a link from a script.

### The editor

- The CKEditor of the templates is CKEditor 5. The global `CKEDITOR`, `CKEDITOR.instances` and the CKEditor 4 configuration files are
  gone.
- A script reaches the editor of a textarea with `window.KunenaEditor.get(id)`, or `window.KunenaEditor.ready(id)` for a promise of
  it. An editor has `getData()`, `setData()`, `insertText()`, `removeText()`, `updateElement()`, `setSourceMode()`,
  `setCommandEnabled()` and `on('change')`.
- The language packs no longer have a `kunena_ckeditor.js`: the texts of the buttons are in the language files of Kunena.
- The `markitup.editor.js` that Kunena wrote into the folder of a template is no longer written. Nothing loaded it.

### The uploader

`KunenaUploader.attach(input, options)` sends each file alone, as soon as it is chosen or dropped, to the same task of the server as
before. The options are `url`, `params(file)`, `accept(files)`, `add(file)`, `preview(canvas, file, item)`, `progress(loaded, total)`,
`done(answer, item, file)`, `fail(message, item, file)`, `resize` and `dropZone`. The full description is at the top of
`media/kunena/core/js/uploader.js`.

- `add(file)` returns the element that shows the file. The other callbacks get that element, not a jQuery object.
- The buttons of a file in the posting form have `data-kunena-attachment="insert"`, `"private"` or `"remove"`. A script finds them by
  that attribute, not by the text of the button.

## Changes in the scripts of Aurelia

The scripts in `components/com_kunena/template/aurelia/assets/js` are rewritten in plain JavaScript. The markup they work on is the
same. These are the changes an override can notice:

- `kShowAccessType(htmlclass, select)` (the list of the access types of a category) takes the `<select>` element. The layouts call it
  with `this`. A jQuery object, as old overrides pass with `$(this)`, still works.
- The global `dataFeed()` of `edit.js` (the users for the mentions of CKEditor 4) is gone. The mentions are part of the editor.
- The jQuery plugin `$.fn.insertAtCaret` of `upload.main.js` is gone.
- The list of the destinations of the bulk actions no longer triggers the jQuery event `list:updated`.
- The profile box opens its details at once, without the slide of 500 ms.
- Kunena no longer handles the markup below, which no layout of Kunena prints any more. An override that still prints it needs its
  own script:
  - `.openmodal` (open a box by its `href`), `#login-link` and `#userdropdown`, `#clearcache`, `.kcatcheckall`;
  - `.Kreplyclick` and `.kreply-cancel` with `data-related` (the quick reply is a Bootstrap modal: use `data-bs-toggle` and
    `data-bs-dismiss`), `#kshow_attach_form`, `#reset`.
- A script of an override that waits for the scripts of Kunena should use `DOMContentLoaded`. Kunena's scripts start then, not in
  `jQuery(document).ready()`.

## Removed layouts, HTML helpers and events

| Removed | Use instead |
| --- | --- |
| The layout `Widget/Datepicker` (`layouts/widget/datepicker/default.php`) | A field with `type="date"` needs no layout. `KunenaDate::fromPicker()` reads the value on the server. |
| `HTMLHelper::_('kunenagrid.boolean', ...)` and `kunenagrid.behavior` (a MooTools script) | A link or a button with your own script. |
| `kunenagrid.order`, `kunenagrid.orderUp`, `kunenagrid.orderDown` | Nothing: they needed the MooTools script and `saveOrder()` of Joomla 1.5. |
| `kunenagrid.checkedOut` | The `jgrid.checkedout` helper of Joomla. |
| `kunenaforum.sortablelist` | Nothing. |
| The class `KunenaBBCodeEditor` and the plugin event `onKunenaBBCodeEditorInit` | Nothing: they built the buttons of the markItUp editor, which no template uses. |

`kunenagrid.sort`, `kunenagrid.id`, `kunenagrid.published`, `kunenagrid.task` and `kunenagrid.action` are unchanged.

## Removed template settings

The settings `nameskinckeditor` (the skin of CKEditor) and `ckeditorcustomprefixconfigfile` (the prefix of a custom CKEditor
configuration file) are removed from the template of Aurelia. CKEditor 5 takes its colours from the template. A template with its
own `config/config.xml` can remove them too.

## Removed language strings

Kunena 8.0 removes about 80 English strings, most of them for the libraries above. An override that prints one of them shows the key
instead of a text. The groups are:

- `COM_KUNENA_BOOTSTRAP_DATEPICKER_*` (48 strings): the names of the months and of the days of the datepicker, and its format.
- `COM_KUNENA_AJAXMODAL_*` and `COM_KUNENA_MODAL_CLOSE`: the progress window of the recount in the backend, which was never shown.
- `COM_KUNENA_TEMPLATE_NAME_OF_SKIN*`, `COM_KUNENA_TEMPLATE_NAME_OF_PREFIX_FOR_CUSTOM_CKEDITOR_CONFIG*` and
  `COM_KUNENA_A_TEMPLATE_MANAGER_CANNOT_FIND_CKEDITOR*`: the settings of CKEditor 4.
- `COM_KUNENA_DT_*`: the date and time formats of Kunena 2.0. `KunenaDate` uses the `COM_KUNENA_LIB_*` formats.
- `COM_KUNENA_LIB_MOVE_UP`, `COM_KUNENA_LIB_MOVE_DOWN`, `COM_KUNENA_LIB_SAVE_ORDER`, `COM_KUNENA_LIB_CHECKED_OUT` and
  `COM_KUNENA_LIB_CLICK_TO_TOGGLE_STATE`: the removed `kunenagrid` helpers.
- `COM_KUNENA_SEARCH_LABEL_SELECT_DATE` and `COM_KUNENA_MYPROFILE_BIRTHDATE_DESC`: texts of the old date fields of the search and
  the profile.

Some strings moved instead of being removed: the strings of the template settings (`COM_KUNENA_TEMPLATE_*`) are now defined once, in
`com_kunena.templates.ini` of the site. To list the keys which are gone from all the English files, from a checkout of Kunena:

```bash
keys() { for f in $(git ls-tree -r --name-only "$1" -- src/site/language/en-GB src/admin/language/en-GB | grep '\.ini$'); do git show "$1:$f" | grep -o '^[A-Z0-9_]*'; done | sort -u; }
comm -23 <(keys origin/K7.1) <(keys origin/K8.0)
```

## Bootstrap 5 classes

The layouts of Aurelia use the classes of Bootstrap 5 only. The last classes of Bootstrap 3 and 4 were replaced, for example
`pull-left` and `pull-right` (now `float-start` and `float-end`), `well` (now `border rounded p-2 mb-3`), `sr-only` (now
`visually-hidden`), `hidden-xs-down` (now `d-none d-sm-inline`) and `pl-5` (now `ps-5`). An override copied from an older Aurelia may still have them. They
no longer have any style.

---

[← Template overrides](README.md)
