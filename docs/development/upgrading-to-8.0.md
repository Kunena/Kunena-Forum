# Upgrading code to Kunena 8.0

This page is for the developers of extensions, plugins and integrations which use the code of Kunena, and for sites with their own
code on top of it. It lists the PHP classes, methods, tasks, events and bundled libraries which Kunena 8.0 removed or changed since
Kunena 7.1, and what replaces them.

The scripts, layouts, HTML helpers, template settings and language strings are in
[Upgrading a template to Kunena 8.0](../templates/upgrading-to-8.0.md).

The lists below come from a comparison of the public and protected API of the two versions (every class, method signature,
constant and property under `src`, without the third-party libraries), and of the events, the configuration and the database
schema.

## Requirements

| | Kunena 7.1 | Kunena 8.0 |
| --- | --- | --- |
| Joomla | 5.4.10 | 6.2.1 |
| PHP | 8.3 | 8.4.1 |

Code that still calls an API which Joomla 6 removed (the deprecated parts of Joomla 4 and 5) stops on Kunena 8.0 too, because Kunena
8.0 runs on Joomla 6 only.

## Find what your code still uses

Run these searches in the folder of your extension. Each hit points to a section of this page.

```bash
grep -rnE "KunenaBBCodeEditor|onKunenaBBCodeEditorInit|KunenaApplication|KunenaViewTopics?\b|ApplicationTopic(Flat|Indented|Threaded)Display" .
grep -rnE "sendNotification\(|KunenaCategoryHelper::recount\(|filterByUserUnread\(|removeOrphanedAttachments|checkpermissions\(" .
grep -rnE "kunenagrid\.(boolean|behavior|order|orderUp|orderDown|checkedOut)|kunenaforum\.sortablelist|Kunenaforum::sortablelist" .
grep -rnE "plugins\.resync|layout=galleryimages|Application/attachment\.php|Pedrollo\\\\GravatarLib" .
grep -rnE "socials->(google|skype|yim)\b" .
```

## Removed classes

| Removed | Why | Use instead |
| --- | --- | --- |
| `Kunena\Forum\Libraries\BBCode\KunenaBBCodeEditor`, with `KunenaBBCodeEditorElement`, `KunenaBBCodeEditorButton` and `KunenaBBCodeEditorSeparator` | They built the buttons of the markItUp editor into a `markitup.editor.js` which nothing loaded. | Nothing. The editors are CKEditor 5 and SCEditor. |
| `Kunena\Forum\Site\KunenaApplication` (`site/src/Application/attachment.php`) | The light application of Kunena 2 for the attachments. It could be opened without the router of Joomla, and it showed the paths of the server in an error. | The attachments are served by the component: `KunenaAttachment::getUrl()`, which routes `index.php?option=com_kunena&view=attachment&id=<id>&format=raw`. |
| `KunenaViewTopic` and `KunenaViewTopics` (`site/src/View/Topic/HtmlViewJson.php`, `HtmlViewRaw.php`, `site/src/View/Topics/HtmlViewJson.php`) | Views of Kunena 2 without a namespace, extending a class which no longer exists. They could not be loaded. | Nothing. |
| `Kunena\Forum\Site\Controller\Application\Topic\Flat\ApplicationTopicFlatDisplay`, and the `Indented` and `Threaded` ones | The topic layouts are chosen by `getTopicLayout()` and shown by the display controller of the topic. | Addresses with `layout=flat`, `threaded` or `indented` open the topic page in that layout. |

## Removed methods

| Removed | Use instead |
| --- | --- |
| `KunenaMessage::sendNotification($url, $approved)` (deprecated in 7.1) | `KunenaMessage::processNotification($url, $approved, $type)`. |
| `KunenaCategoryHelper::recount($categories)` (deprecated in 7.0) | `KunenaCategoryHelper::recountBatch($categories, $batchSize = 50)`. |
| `Kunenaforum::sortablelist()` (`HTMLHelper::_('kunenaforum.sortablelist', ...)`) | Nothing: the jQuery UI sortable it used is removed. |
| `Kunenagrid::boolean()`, `behavior()`, `order()`, `orderUp()`, `orderDown()`, `checkedOut()` | Nothing called them. See [the template guide](../templates/upgrading-to-8.0.md#removed-layouts-html-helpers-and-events). |
| `PluginsController::resync()` (the task `plugins.resync` of the backend) | Nothing: it rebuilt the file of the markItUp editor. |
| `KunenaViewUser::displayGalleryimages()` (`view=user&layout=galleryimages&format=raw`) | The JSON task `index.php?option=com_kunena&view=user&task=galleryimages&format=json&<token>=1&gallery_name=<name>`. |
| `UserEditAvatarDisplay::getGallery()` and its property `$imageFilter` | The avatar gallery is read by the task `galleryimages` above. |

## Changed signatures

These changes can break a call written for 7.1, or a class which extends the class of Kunena and overrides the method.

| Method | 7.1 | 8.0 |
| --- | --- | --- |
| `KunenaTopicFinder::filterByUserUnread()` | `(KunenaUser $user)` | `(KunenaUser $user, int $allReadTime)`. Pass `(int) KunenaFactory::getSession()->getAllReadTime()`, the "mark all read" time of the user. |
| `TopicController::checkpermissions()` (protected) | `($attachment_userid)` | `(KunenaAttachment $attachment)`: the rule of the attachment itself, its uploader or a moderator of its category. |
| `TopicController::removeOrphanedAttachments()` | public, so a task `topic.removeOrphanedAttachments` | protected: no longer a task which a URL can call. |
| `KunenaControllerBase::__serialize()` / `__unserialize()` | `__serialize()` returned a string, `__unserialize($input)` | `__serialize(): array`, `__unserialize(array $data): void`, as PHP requires. |

New optional parameters, which a call of 7.1 does not need but an override must accept:

- `KunenaTopic::lock($value = 1, string $reason = '')`: the reason of the lock.
- `KunenaAvatar::getLink(KunenaUser $user, $class, $sizex, $sizey, string $online = '')`.
- `KunenaUserSocials::getInstance($userid = 0, $useCache = true, ?string $json = null)` and `KunenaUserSocials::load(?string $json = null)`.
- `KunenaDiagnostics::getItems(string $function, int $limit = 0)`.
- `KunenaMessage::attachEmailBody(..., int $recipientId = 0, bool $follower = false, bool $approvedAuthor = false)` (protected).
- `MessageList::displayTimeFilter($id, $attrib = null)` and `TopicList::displayTimeFilter($id, $attrib = null)`: the default
  attributes are set inside the method.

Return types that are more exact now, which matters for a strict comparison such as `=== true`:

- `KunenaCategory::getPosts()`: `int`, the number of posts (it was declared `bool`).
- `KunenaAnnouncement::getTaskUrl()`: `bool|string`.
- `KunenaMessageHelper::getLastUserIP()`: `string|false`.
- `KunenaTree::getLevel()`: `int|false`, the level (it returned a boolean).
- `KunenaMessageFinder::filterByCategories()`: `KunenaMessageFinder`, for chaining.
- `KunenaFactory::getApplication()`: can also be the `ApiApplication` of Joomla, for the REST API.

## Removed properties

`KunenaUserSocials` no longer has `$google`, `$skype` and `$yim`: Google+, Skype and Yahoo Messenger are shut down. The social
websites of a profile are managed in the backend (*Components, Kunena Forum, Social websites*).

## Events

- The event `onKunenaBBCodeEditorInit` is removed, together with `KunenaBBCodeEditor`. A plugin which listened to it is no longer
  called.
- New events: `onKunenaAfterAttachmentUpload`, `onKunenaCheckSpam`, `onKunenaGetIpLocation`, `onKunenaInboxView` and
  `onKunenaReportMessage`.
- `onContentPrepare` of the content plugins of Joomla still runs on the messages. An administrator can now limit it to chosen
  content plugins (see [content plugins](../8.0/features/content-plugins.md)).
- [plugin-events.md](plugin-events.md) describes every event, with its arguments.

## Bundled libraries

The libraries in `libraries/kunena/External` are for Kunena itself. Do not use them from an extension: they can change or go
away in any version.

- The Gravatar library of 2011 (`Pedrollo\GravatarLib\Gravatar`, `External/Emberlabs`) is removed. The Gravatar plugin builds its
  addresses with its own `Kunena\Forum\Plugin\Kunena\Gravatar\Helper\GravatarUrl` (SHA-256, https, sizes up to 2048).
- scssphp, which compiles the style sheets of the templates, goes from 1.10 to 2.1 (namespace `KunenaScssPhp\ScssPhp`, as before).
  Its new dependencies `league/uri` and `symfony/filesystem` are bundled with it.
- The SVG avatars are cleaned with `enshrined/svg-sanitize` 1.1.1 of Joomla (`libraries/vendor`). Kunena does not bundle it: that
  version comes with Joomla 6.2.1, the minimum of Kunena 8.0.

## The scripts

Kunena 8.0 no longer loads jQuery, and its scripts are plain JavaScript. An extension which adds a script to the pages of the forum
and uses jQuery must load it itself (`$wa->useScript('jquery')`) or be rewritten. The removed libraries (CKEditor 4, Fancybox,
bootstrap-datepicker, jQuery File Upload, Tribute, Sisyphus, rating.js) and the JavaScript API which replaces them are in
[the template guide](../templates/upgrading-to-8.0.md#removed-libraries-and-what-replaces-them).

## Unchanged

- **Configuration**: no option of 7.1 is removed (8.0 adds 44).
- **Database**: no table and no column is removed. Five tables get a primary key in place of a unique index, on the same columns:
  `#__kunena_aliases` (`alias`), `#__kunena_polls_users` (`pollid`, `userid`), `#__kunena_thankyou` (`postid`, `userid`),
  `#__kunena_user_read` and `#__kunena_user_topics` (`user_id`, `topic_id`). A query which inserts a row twice now fails on the
  key, as it already did on the unique index.
- **Plugins**: none removed. New: `plg_kunena_acl`, `plg_kunena_geoip`, `plg_kunena_inbox`, `plg_kunena_reports` and
  `plg_webservices_kunena`.

## Still deprecated

`KunenaUser::__get()` (deprecated since 6.4.2): read the properties of the user directly, `$user->name` instead of a magic
property which the method maps. It will be removed in a later version.

---

[← Plugin events](plugin-events.md) · [Upgrading a template to Kunena 8.0](../templates/upgrading-to-8.0.md)
