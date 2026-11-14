# Plugin events of Kunena 8.0

This page lists every plugin event that Kunena 8.0 dispatches, the way Joomla's "Plugin/Events" page does for Joomla. It is
written from the code: the event names below are all the names that Kunena code outside `src/plugins` dispatches.

Before an event, Kunena loads the plugins of the `kunena` group (`PluginHelper::importPlugin('kunena')`). The exceptions are
said at the events: `onKunenaGetAccessControl` loads only the plugin `joomla` of the `kunena` group, `OnKunenaAfterProfileUpdate`
loads the `system` group, the two socials events load the `modifysocials` group, and a few events load nothing themselves.

In Kunena 8.0 a plugin is a Joomla 6 plugin: its class extends `Joomla\CMS\Plugin\CMSPlugin`, implements
`Joomla\Event\SubscriberInterface`, maps the event names to its methods in `getSubscribedEvents()`, and each method gets the
event object as its only argument. The smallest example to read is `src/plugins/plg_kunena_gravatar/src/Extension/Gravatar.php`. Abbreviated (the
real plugin subscribes only on the site and in the administrator, and returns early when its `avatar` parameter is off):

```php
class Gravatar extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['onKunenaGetAvatar' => 'onKunenaGetAvatar'];
    }

    public function onKunenaGetAvatar(KunenaGetAvatarEvent $event): void
    {
        $event->stopPropagation();
        $event->setAvatar(new KunenaAvatarGravatar($this->params));
    }
}
```

The event classes are in `src/libraries/kunena/src/Event/` (namespace `Kunena\Forum\Libraries\Event`). A few things are the
same for many events:

- An argument which Kunena passes as a PHP reference (`&$contents`) stays a reference in the event: a listener that writes it
  with `$event->setArgument('content', $value)` changes the variable of Kunena. An object (a table, a topic, a user) can be
  changed by any listener, reference or not.
- Some events are built with positional arguments (`[$layout, $pluginmode, &$topics, ...]`). Their arguments are named `0`,
  `1`, `2`...: read them with `$event->getArgument(2)` or `$event->getArguments()`.
- "Result" below says what Kunena reads back after the dispatch. When it says "nothing", a listener can only act on its own
  or change the objects it was given.

## Integration

These events ask the plugins for the integration objects of Kunena: the avatars, the profiles, the private messages, the
activity, the login and the access control. Each is dispatched when the integration object is built, the first time it is needed.

### onKunenaGetAvatar

- When: the first time Kunena needs the avatar integration (`KunenaAvatar::getInstance()`).
- Arguments: none. The listener gives a `KunenaAvatar` object with `$event->setAvatar($avatar)` (argument `avatar`), or with
  `$event->addResult($avatar)`. The bundled plugins also call `$event->stopPropagation()`, so the first enabled plugin wins.
- Context: none.
- Result: Kunena uses the `avatar` argument when it is a `KunenaAvatar`, otherwise the first object in `result`. Without one,
  it uses a plain `KunenaAvatar`, which has no images.
- Dispatched in: `src/libraries/kunena/src/Integration/KunenaAvatar.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetAvatarEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_gravatar`, `plg_kunena_kunena`, `plg_kunena_community`, `plg_kunena_comprofiler`,
  `plg_kunena_easyblog`, `plg_kunena_easysocial`.

### onKunenaGetProfile

- When: the first time Kunena needs the profile integration (`KunenaProfile::getInstance()`).
- Arguments: none. The listener gives a `KunenaProfile` with `$event->setProfile($profile)` (argument `profile`) or
  `$event->addResult($profile)`.
- Context: none.
- Result: the `profile` argument, otherwise the first object in `result`. Without one, the profile integration is switched
  off: Kunena uses a plain `KunenaProfile` with `enabled = false`.
- Dispatched in: `src/libraries/kunena/src/Integration/KunenaProfile.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetProfileEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_kunena`, `plg_kunena_community`, `plg_kunena_comprofiler`, `plg_kunena_easyblog`,
  `plg_kunena_easysocial`.

### onKunenaGetPrivate

- When: the first time Kunena needs the private messages integration (`KunenaPrivate::getInstance()`).
- Arguments: none. The listener gives a `KunenaPrivate` with `$event->setPrivate($private)` (argument `private`) or
  `$event->addResult($private)`.
- Context: none.
- Result: the `private` argument, otherwise the first object in `result`. Without one, Kunena uses a plain `KunenaPrivate`,
  which has no inbox.
- Dispatched in: `src/libraries/kunena/src/Integration/KunenaPrivate.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetPrivateEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_inbox`, `plg_kunena_uddeim`, `plg_kunena_community`, `plg_kunena_comprofiler`,
  `plg_kunena_easysocial`.

### onKunenaGetActivity

- When: when the activity integration is built (`new KunenaActivity()`).
- Arguments: none. Each listener adds a `KunenaActivity` object with `$event->addResult($activity)`.
- Context: none.
- Result: Kunena keeps every `KunenaActivity` in `result` and calls all of them (points, medals, after post, after reply...).
- Dispatched in: `src/libraries/kunena/src/Integration/KunenaActivity.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetActivityEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_community`, `plg_kunena_comprofiler`, `plg_kunena_easysocial`.

### onKunenaGetLogin

- When: when the login integration is built (`new KunenaLogin()`).
- Arguments: none. Each listener adds a `KunenaLoginAbstract` object with `$event->addResult($login)`.
- Context: none.
- Result: Kunena keeps every `KunenaLoginAbstract` in `result`. With none, the login of Kunena is disabled.
- Dispatched in: `src/libraries/kunena/src/Login/KunenaLogin.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetLoginEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_joomla`, `plg_kunena_community`, `plg_kunena_comprofiler`, `plg_kunena_easysocial`.

### onKunenaGetAccessControl

- When: when the access control is built (`new KunenaAccess()`).
- Arguments: none. Each listener adds a `KunenaAccessAbstract` object with `$event->addResult($access)`.
- Context: none.
- Result: Kunena keeps every `KunenaAccessAbstract` in `result`, sorted by the access types it gives (`getAccessTypes()`).
- Dispatched in: `src/libraries/kunena/src/Access/KunenaAccess.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetAccessControlEvent` (immutable, result aware, results are objects).
- Listeners: `plg_kunena_joomla`, `plg_kunena_acl`, `plg_kunena_community`, `plg_kunena_comprofiler`.
- Note: before this event Kunena loads only the plugin `joomla` of the `kunena` group (`importPlugin('kunena', 'joomla')`).
  Another `kunena` plugin hears it only when an earlier `importPlugin('kunena')` in the same request has loaded it.

### onKunenaGetIpLocation

- When: when Kunena shows the IP address of a message to a user allowed to see it, and asks where it is
  (`KunenaIpLocation::get()`). Local and reserved addresses are skipped, and each address is asked once per request.
- Arguments: `ip` (string, the address); `location` (null). The listener sets `location` with
  `$event->setArgument('location', [...])`: an array with `label` (string), and optionally `country` (code), `latitude` and
  `longitude`.
- Context: none.
- Result: Kunena reads `location` back. It uses it when it is an array with a non-empty `label`: the label is shown next to the
  address, as a link to a map when `latitude` and `longitude` are set. An exception of a listener is caught: no place is shown.
- Dispatched in: `src/libraries/kunena/src/Html/KunenaIpLocation.php`, on the dispatcher of the application.
- Event class: none (generic event): `Joomla\CMS\Event\GenericEvent`.
- Listeners: `plg_kunena_geoip`.

## Database objects

These events fire when a Kunena database object is saved or deleted.

The four save and delete events use Kunena classes that extend Joomla's `Joomla\CMS\Event\Model\DeleteEvent` (or, for
`onKunenaAfterDelete`, `Joomla\CMS\Event\Content\ContentPrepareEvent`). They are not instances of Joomla's
`Model\BeforeSaveEvent`, `Model\AfterSaveEvent`, `Model\BeforeDeleteEvent` or `Model\AfterDeleteEvent`: type the argument of
a listener with the Kunena class. Known bug, not changed here: `plg_kunena_finder` types its four listeners with those Joomla
classes (`src/plugins/plg_kunena_finder/src/Extension/Finder.php`), so each of them would get a `TypeError` when the event
reaches it. A `TypeError` is an `Error`, not an `Exception`, so the `catch (Exception $e)` around the dispatch in
`KunenaDatabaseObject` does not catch it.

### onKunenaBeforeSave

- When: in `KunenaDatabaseObject::save()`, after the checks and before the table is stored. It fires for every object that
  extends `KunenaDatabaseObject`: `KunenaCategory`, `KunenaTopic`, `KunenaMessage`, `KunenaAttachment`, `KunenaAnnouncement`,
  and the private message classes `KunenaPrivateMessage`, `KunenaPost` and `KunenaUser` (namespaces
  `Kunena\Forum\Libraries\KunenaPrivate` and `Kunena\Forum\Libraries\KunenaPrivate\Message`).
- Arguments: Kunena passes `['com_kunena.' . $this->_name, &$table, $isNew]`. The event class keeps two of them: `context`
  (string, `$event->getContext()`) and `subject` (the `KunenaTable` object that is stored next, `$event->getItem()`). A listener
  may change the fields of the table before it is stored. The third value, `$isNew`, is dropped: the event class only knows
  `context` and `subject`, so `$event->getArgument('isNew')` is `null`.
- Context: `com_kunena.` followed by the full class name of the object, for example
  `com_kunena.Kunena\Forum\Libraries\Forum\Message\KunenaMessage` (`$_name` is `get_class($this)`).
- Result: nothing is read back. An exception of a listener is thrown again, and the object is not saved.
- Dispatched in: `src/libraries/kunena/src/Database/KunenaDatabaseObject.php`, on the dispatcher from the container
  (`Factory::getContainer()->get(DispatcherInterface::class)`), which on the site and in the administrator is the dispatcher
  of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBeforeSaveEvent`. Mind that it extends Joomla's
  `Joomla\CMS\Event\Model\DeleteEvent`, not `Joomla\CMS\Event\Model\BeforeSaveEvent`: type the argument of the listener with the
  Kunena class.
- Listeners: `plg_kunena_finder`, whose listener has the wrong type (see the start of this section).

### onKunenaAfterSave

- When: in `KunenaDatabaseObject::save()`, after the table is stored and the object updated.
- Arguments: as `onKunenaBeforeSave`: `context` (string) and `subject` (the stored `KunenaTable`); the `$isNew` that Kunena
  passes is dropped by the event class.
- Context: `com_kunena.` followed by the full class name of the object.
- Result: nothing.
- Dispatched in: `src/libraries/kunena/src/Database/KunenaDatabaseObject.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaAfterSaveEvent` (extends Joomla's `Model\DeleteEvent`, as above).
- Listeners: `plg_kunena_finder`, whose listener has the wrong type (see the start of this section).

### onKunenaBeforeDelete

- When: in `KunenaDatabaseObject::delete()`, before the row is deleted.
- Arguments: `context` (string) and `subject` (the `KunenaTable` object of the row), passed positionally as
  `['com_kunena.' . $this->_name, $table]`.
- Context: `com_kunena.` followed by the full class name of the object.
- Result: nothing is read back. An exception of a listener is thrown again, and the row is not deleted.
- Dispatched in: `src/libraries/kunena/src/Database/KunenaDatabaseObject.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBeforeDeleteEvent` (extends Joomla's `Model\DeleteEvent`).
- Listeners: `plg_kunena_finder`, whose listener has the wrong type (see the start of this section).

### onKunenaAfterDelete

- When: in `KunenaDatabaseObject::delete()`, after the row is deleted.
- Arguments: `context` (string) and `subject` (the `KunenaTable` object of the deleted row).
- Context: `com_kunena.` followed by the full class name of the object.
- Result: nothing.
- Dispatched in: `src/libraries/kunena/src/Database/KunenaDatabaseObject.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaAfterDeleteEvent`. It extends Joomla's
  `Joomla\CMS\Event\Content\ContentPrepareEvent`, so it also has the `params` and `page` arguments. Kunena does not
  pass them: Joomla's `ContentPrepareEvent` turns the missing `params` into an empty `Joomla\Registry\Registry`, and `page`
  is `null`.
- Listeners: `plg_kunena_finder`, whose listener has the wrong type (see the start of this section).

### onDeleteKunenaPost

- When: when a published message stops counting in its topic: it is unpublished (held for approval, trashed) or deleted.
  Kunena dispatches it in `KunenaMessage::update()` when the change of the message takes one post from its topic.
- Arguments: Kunena passes `context`, `subject` (the `KunenaMessage`) and `ids` (`[$this->id]`). The event class keeps only
  `context` and `subject`: `ids` is dropped, read the id from `$event->getItem()->id`.
- Context: `com_kunena.post`.
- Result: nothing.
- Dispatched in: `src/libraries/kunena/src/Forum/Message/KunenaMessage.php`, on the dispatcher from the container. Unlike the
  other events, the name puts `Delete` before `Kunena`, and the dispatch does not load a plugin group itself.
- Event class: `Kunena\Forum\Libraries\Event\KunenaDeleteEvent` (extends Joomla's `Model\DeleteEvent`).

## Rendering and content

These events let a plugin see or change what Kunena shows.

### onKunenaBeforeRender

- When: on every page of the forum on the site, after the controller ran and before the output is turned into a string.
- Arguments: `context` (string); `content`, a reference to the output of the controller: a `KunenaLayout` object for the
  pages of the HMVC controllers, a string for the older controllers. A listener can replace it with
  `$event->setArgument('content', $value)` (the argument is a reference) or change the layout object.
- Context: `com_kunena.{view}`, the view of the request, for example `com_kunena.topic` or `com_kunena.category`. In
  `src/site/tmpl/Credits/default.php` the view is the `func` of the request, or its `view`.
- Result: Kunena casts the content to a string and shows it.
- Dispatched in: `src/site/src/Dispatcher/Dispatcher.php` and `src/site/tmpl/Credits/default.php`, on the dispatcher from the
  container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBeforeRenderEvent` (extends the abstract `KunenaContentEvent`, which needs
  `context` and `content`).

### onKunenaAfterRender

- When: right after `onKunenaBeforeRender`, when the output is a string, before it is echoed.
- Arguments: `context` (string); `content`, a reference to the html of the page (string). A listener can replace it with
  `$event->setArgument('content', $html)`.
- Context: `com_kunena.{view}`, as above.
- Result: Kunena echoes the content.
- Dispatched in: `src/site/src/Dispatcher/Dispatcher.php` and `src/site/tmpl/Credits/default.php`, on the dispatcher from the
  container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaAfterRenderEvent` (extends `KunenaContentEvent`).

### onKunenaPrepare

- When: before a topic, its messages, a list of topics or messages, or a profile box is shown.
- Arguments: `context` (string); `subject` (the object to show); `params` (a `Joomla\Registry\Registry` with `ksource` =
  `kunena`, `kunena_view` and `kunena_layout`); `page` (`0`). A listener may change the subject object.
- Contexts, at each call site:
  - `kunena.topic`, subject the `KunenaTopic`: `src/site/src/Controller/Topic/Item/TopicItemDisplay.php`,
    `src/site/src/Controller/Topic/Form/Edit/TopicFormEditDisplay.php`,
    `src/site/src/Controller/Topic/Form/Reply/TopicFormReplyDisplay.php`, and `src/site/src/View/Topic/HtmlView.php`
    (`displayDefault()`, `DisplayReply()`, `displayEdit()`).
  - `kunena.messages`, subject one message (once per message): `src/site/src/Controller/Topic/Item/TopicItemDisplay.php`,
    `src/site/src/Controller/Topic/Form/History/TopicFormHistoryDisplay.php`, `src/site/src/Layout/Search/SearchResults.php`,
    `src/site/src/View/Topic/HtmlView.php` (`displayDefault()`, `displayThreadHistory()`), and `displayPostRows()` of
    `src/site/src/View/Misc/HtmlView.php`, `src/site/src/View/Search/HtmlView.php`, `src/site/src/View/Statistics/HtmlView.php`,
    `src/site/src/View/Topics/HtmlView.php` and `src/site/src/View/User/HtmlView.php`.
  - `kunena.topics`: in `displayTopicRows()` of `src/site/src/View/Topics/HtmlView.php` the subject is one topic (once per
    topic); in `displayTopicRows()` of `src/site/src/View/Misc/HtmlView.php`, `src/site/src/View/Search/HtmlView.php`,
    `src/site/src/View/Statistics/HtmlView.php` and `src/site/src/View/User/HtmlView.php` it is the whole `$this->topics` list.
  - `kunena.user`, subject the `KunenaUser` of the profile box: `src/site/src/View/Topic/HtmlView.php`
    (`getMessageProfileBox()`).
- Result: nothing is read back; Kunena shows the subject as the listeners left it.
- Dispatched in: the files above, on the dispatcher of the application. The call in
  `src/site/src/Controller/Topic/Listing/ListDisplay.php` (context `kunena.topic.list`) is commented out: it never fires.
- Event class: `Kunena\Forum\Libraries\Event\KunenaPrepareEvent` (extends Joomla's
  `Joomla\CMS\Event\Content\ContentPrepareEvent`: `getContext()`, `getItem()`, `getParams()`, `getPage()`).
- Listeners: `plg_kunena_comprofiler` (context `kunena.user`).

### onKunenaDisplay

- When: around the display of a view of the older controllers (`KunenaController::display()`, html only): once with type
  `start` before the view is shown and once with type `end` after it. And when the profile box of a message is built in the
  topic view (`getMessageProfileBox()`), with type `topic.profile`.
- Arguments: `type` (string: `start`, `end` or `topic.profile`); `view` (the view object); `params` (a
  `Joomla\Registry\Registry`, only with `topic.profile`).
- Context: none; the `type` says where it fires.
- Result: for `start` and `end`, nothing. For `topic.profile`, Kunena reads the argument `result` (an array of html strings) and
  appends it, joined with spaces, to the profile box. The event is not result aware: a listener sets the whole array with
  `$event->setArgument('result', [...])`.
- Dispatched in: `src/libraries/kunena/src/Controller/KunenaController.php` and `src/site/src/View/Topic/HtmlView.php`, on the
  dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaDisplayEvent` (immutable, needs `type`).
- Listeners: `plg_kunena_comprofiler` (`start` and `end`).

### onKunenaBbcodeConstruct

- When: each time a `KunenaBBCode` parser is built, at the end of its constructor.
- Arguments: `this`, the `KunenaBBCode` object. A listener can change the parser: add or remove rules, smileys, settings.
- Context: none.
- Result: nothing is read back; the parser is used as the listeners left it.
- Dispatched in: `src/libraries/kunena/src/BBCode/KunenaBBCode.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBbcodeConstructEvent`.

### onKunenaBBCodeEditorInit

- When: when the buttons of the BBCode editor are built from `aurelia_editor.xml` (`KunenaBBCodeEditor::initializeHMVC()`).
  In Kunena 8.0 that happens in the administrator, on the check-in and the resync of the plugins
  (`src/admin/src/Controller/PluginsController.php`).
- Arguments: argument `0`, the `KunenaBBCodeEditor` object (`$event->getArgument(0)`). A listener adds or moves buttons with
  `insertElement()` and `insertElements()`.
- Context: none.
- Result: nothing is read back; Kunena builds the javascript of the editor from the elements of the object.
- Dispatched in: `src/libraries/kunena/src/BBCode/KunenaBBCodeEditor.php`, through `Factory::getApplication()->triggerEvent()`.
  Mind the case: `BBCode` here, `Bbcode` in `onKunenaBbcodeConstruct`.
- Event class: none: `triggerEvent` with a plain array (`[$this]`), so the listener gets a `Joomla\Event\Event`.

## User interface hooks

### onKunenaGetButtons

- When: after Kunena has built the action buttons of a category, a topic or a message.
- Arguments: `context` (string); `buttons` (the `Joomla\Registry\Registry` of the buttons, an object: a listener adds, changes
  or removes buttons in it); `object` (the controller or view that built them).
- Contexts, at each call site:
  - `category.action`: `src/site/src/Controller/Category/Index/Actions/CategoryIndexActionsDisplay.php`.
  - `topic.action`: `src/site/src/Controller/Topic/Item/Actions/TopicItemActionsDisplay.php`, and `getTopicActions()` of
    `src/site/src/View/Topic/HtmlView.php`.
  - `message.action`: `src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php`, and
    `getMessageActions()` of `src/site/src/View/Topic/HtmlView.php`.
- Result: nothing is read back from the event; the layout shows the buttons of the registry as the listeners left them.
- Dispatched in: the files above, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetButtonsEvent`.

### onKunenaSidebar

- When: when the profile box next to a message is built (`KunenaUser::getSideProfile()`).
- Arguments: `userid` (int, the author of the message).
- Context: none.
- Result: nothing. Kunena returns the profile box of the profile integration whatever the listeners do.
- Dispatched in: `src/libraries/kunena/src/User/KunenaUser.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaSidebarEvent`.

### onKunenaUserTabs

- When: when the tabs of a profile page are built.
- Arguments: `tabs` (array of `stdClass` tabs keyed by name, each with `title`, `content` and `active`); `profile` (the
  `KunenaUser` of the profile); `me` (the `KunenaUser` who looks at it). A listener adds or changes tabs and sets the array back
  with `$event->setArgument('tabs', $tabs)`.
- Context: none.
- Result: Kunena reads `tabs` back and shows it when it is an array.
- Dispatched in: `src/site/src/Layout/User/UserItem.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaUserTabsEvent`.
- Listeners: `plg_kunena_inbox` (the inbox tab of the own profile).

### onKunenaUserTabsEdit

- When: when the tabs of the page that edits a profile are built.
- Arguments: `tabs` (array of `stdClass` tabs, as above).
- Context: none.
- Result: nothing. Kunena returns its own `$tabs`; the line that would add the tabs of the plugins is commented out. A listener
  can only change the existing tab objects.
- Dispatched in: `src/site/src/Layout/User/UserEdit.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaUserTabsEditEvent`.

### onKunenaInboxView

- When: when the inbox page of the forum (a menu item of the view inbox) is shown to a logged-in user.
- Arguments: `me` (the `KunenaUser`); `inboxto` (int, the user to write to, from the request); `html` (string, empty). The
  listener sets the html of the inbox with `$event->setArgument('html', $html)`.
- Context: none.
- Result: Kunena reads `html` back and shows it. When it is empty, it shows that the inbox is not available.
- Dispatched in: `src/site/src/Controller/Application/Inbox/Initial/InboxDisplay.php`, on the dispatcher of the application.
- Event class: none (generic event): `Joomla\CMS\Event\GenericEvent`.
- Listeners: `plg_kunena_inbox`.

## Lists

### onKunenaGetTopics

- When: when a topics list is loaded in the mode `plugin` with a non-empty `modetype` (`TopicsModel::getTopics()`).
- Arguments, positional: `0` the layout (string); `1` the mode type (string); `2` a reference to the topics (`false`); `3` a
  reference to the total (int, `0`); `4` the model. A listener writes the list with `$event->setArgument(2, $topics)` and
  `$event->setArgument(3, $total)` (the arguments are references; Joomla marks the numeric names as deprecated).
- Context: none; the mode type says which list is asked.
- Result: when the topics are not empty, Kunena uses them and the total. Otherwise it loads its own list.
- Dispatched in: `src/site/src/Model/TopicsModel.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetTopicsEvent`.

### onKunenaGetUserTopics

- When: when the topics of a user are listed in the mode `plugin` (layout `user`).
- Arguments, positional: `0` the mode type (string, `list.modetype`); `1` a reference to the `KunenaTopicFinder`; `2` a reference
  to the order (string, `last_post_time`); `3` a reference to the category ids (array); `4` the controller. A listener adds its
  filters to the finder, or changes the order and the categories.
- Context: none.
- Result: Kunena runs the finder as the listeners left it. The title of the page is the language key
  `COM_KUNENA_VIEW_TOPICS_USERS_MODE_PLUGIN_` followed by the mode type in capitals.
- Dispatched in: `src/site/src/Controller/Topic/Listing/User/TopicListingUserDisplay.php`, on the dispatcher from the
  container. The dispatch does not load a plugin group itself.
- Event class: `Kunena\Forum\Libraries\Event\KunenaGetUserTopicsEvent`.

## Attachments

### onKunenaAfterAttachmentUpload

- When: after a file uploaded through the editor's uploader (`TopicController::upload()`) is complete, copied to its final
  place and its row saved. Once per file, not per chunk, and not when the upload or the save fails.
- Arguments: `subject`, the saved `KunenaAttachment` (`$event->getAttachment()`), with `id`, `userid`, `folder`, `filename`
  (the stored name), `filename_real`, `filetype`, `size` and `hash`. The file is at
  `JPATH_ROOT . '/' . $attachment->folder . '/' . $attachment->filename` (`$attachment->getPath()`).
- Context: none.
- Result: nothing is read back from the event. Kunena keeps the local file and serves it as before. An exception or error
  thrown by a listener is caught and logged (category `kunena`): the upload succeeds, and the file stays. Kunena builds the
  JSON response of the upload from the attachment after the listeners ran, so a listener should not change its fields
  (`id`, `hash`, `size`, `filetype`, `filename_real`, `inline`).
- Dispatched in: `src/libraries/kunena/src/Attachment/KunenaAttachment.php` (`dispatchAfterUpload()`), called from
  `src/site/src/Controllers/TopicController.php`, on the dispatcher of the application, after `importPlugin('kunena')`.
- Event class: `Kunena\Forum\Libraries\Event\KunenaAfterAttachmentUploadEvent` (immutable, needs `subject`).
- Note: at this moment the attachment belongs to no message (`mesid` 0, `status` 0). It is linked to a message later, when
  the message is posted or edited: from the attachments list of the form, or because the text names it with
  `[attachment=ID]`. That linking is a plain save, which fires `onKunenaAfterSave`, not this event. The file of a private or
  anonymous message is renamed to a random name at that later save, so the `filename` a listener saw changes. An upload
  that is never posted is deleted by the scheduled clean-up (`onKunenaBeforeDelete`/`onKunenaAfterDelete`). The old
  multipart form upload (`KunenaAttachment::upload()`) does not dispatch this event.

## Posting

### onKunenaCheckSpam

- When: when a new topic or a reply is posted from the frontend (`TopicController::post()`), after all of Kunena's own
  checks (captcha, flood, duplicate, StopForumSpam, maximum links) and the hold rules for guests and new users, and before
  the activity integration and the save. It runs for every poster, guests, moderators and administrators included: a
  plugin that wants to skip some users, for example the moderators or a whitelist, checks `$event->getUser()` or
  `$event->getIp()` itself. Edited posts are not checked.
- Arguments: `message`, the `KunenaMessage` about to be saved (`$event->getMessage()`), with `subject`, `message` (the
  text), `name`, `email`, `userid` and `parent`. When the user posts as anonymous, the message is already anonymised at
  this point. `topic`, the `KunenaTopic` (`$event->getTopic()`), the new and unsaved topic for a new topic. `category`, the
  `KunenaCategory` (`$event->getCategory()`). `user`, the posting `KunenaUser` (`$event->getUser()`), a guest user object
  for a guest. `isNewTopic`, true for a new topic and false for a reply (`$event->isNewTopic()`). `ip`, the address of the
  visitor (`$event->getIp()`), also when the Kunena option `ipTracking` is off: the plugin decides whether it sends it to a
  service.
- Context: none.
- Result: a listener answers with `$event->reject($reason)` (the post is not saved, `$reason` is an optional text for the
  user) or `$event->hold()` (the post waits for a moderator). Kunena reads `$event->getVerdict()`, one of
  `KunenaCheckSpamEvent::NONE`, `HOLD` and `REJECT`, and `$event->getReason()`. The strictest answer wins: `hold()` never
  lowers a rejection, and the reason is the first non-empty one given with `reject()`. On `REJECT`, Kunena shows the
  reason (escaped as HTML) or "Your message was rejected by the spam check of this site." and brings the user back to the
  form with the text. On `HOLD`, the post is saved unapproved, as a held post of a new user is. Without an answer the post
  goes on as before. An exception or error thrown by a listener is caught and logged (category `kunena`) and the post goes
  on, with the answers that the listeners before it gave.
- Dispatched in: `src/libraries/kunena/src/Forum/Message/KunenaMessage.php` (`dispatchSpamCheck()`), called from
  `src/site/src/Controllers/TopicController.php`, on the dispatcher of the application, after `importPlugin('kunena')`.
- Event class: `Kunena\Forum\Libraries\Event\KunenaCheckSpamEvent` (immutable, needs all six arguments).
- Listeners: none.

An example plugin which holds the posts of guests that contain a link and rejects the posts which name a blocked word:

```php
class Spamwords extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['onKunenaCheckSpam' => 'onKunenaCheckSpam'];
    }

    public function onKunenaCheckSpam(KunenaCheckSpamEvent $event): void
    {
        $message = $event->getMessage();

        if ($event->getUser()->isModerator()) {
            return;
        }

        if (stripos($message->subject . ' ' . $message->message, 'casino') !== false) {
            $event->reject('Your message names a word which is not allowed here.');

            return;
        }

        if (!$event->getUser()->exists() && str_contains($message->message, '[url')) {
            $event->hold();
        }
    }
}
```

## Others

### onKunenaReportMessage

- When: when a user reports a message to the moderators, after the flood check and before the e-mails.
- Arguments: `message` (the reported `KunenaMessage`); `topic` (its `KunenaTopic`); `target` (the message or topic the report
  is about); `reporter` (the `KunenaUser`); `reason` (string); `text` (string); `url` (string, the link to the message);
  `handled` (`false`). A listener that took care of the report sets `$event->setArgument('handled', true)`.
- Context: none.
- Result: Kunena reads `handled` back. When it is true, the report counts as done even if no e-mail could be sent, and it is
  logged.
- Dispatched in: `src/site/src/Controllers/TopicController.php` (`report()`), on the dispatcher of the application.
- Event class: none (generic event): `Joomla\CMS\Event\GenericEvent`.
- Listeners: `plg_kunena_reports`.

### OnKunenaAfterProfileUpdate

- When: after a profile is saved from the edit page of the forum (`UserController::save()`), also when the save failed.
- Arguments, positional: `0` the Joomla user (`Joomla\CMS\User\User`) of the profile; `1` whether the save succeeded (bool).
- Context: none.
- Result: nothing.
- Dispatched in: `src/site/src/Controllers/UserController.php`, on the dispatcher from the container.
- Event class: `Kunena\Forum\Libraries\Event\KunenaAfterProfileUpdateEvent`.
- Note: the name starts with a capital `O`, and Kunena loads the `system` plugin group before it, not `kunena`. Subscribe
  with exactly `'OnKunenaAfterProfileUpdate'`.

### onKunenaBeforeModifySocials

- When: in the administrator, Tools, when a social network of the profiles is changed (`ToolsController::modifysocials()`).
- Arguments: `socials` (array with `name`, `profileURL`, `noURL` and `fa`, from the form). A listener can add a string result
  with `$event->addResult($message)`.
- Context: none.
- Result: Kunena shows the first result as a message.
- Dispatched in: `src/admin/src/Controller/ToolsController.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBeforeModifySocialsEvent` (result aware, results are strings).
- Note: Kunena first checks that the plugin `modifysocials` of the `system` group is enabled, then loads the plugin group
  `modifysocials`.

### onKunenaBeforeDeleteSocials

- When: in the administrator, Tools, when social networks of the profiles are deleted (`ToolsController::deletesocials()`).
- Arguments: `socials` (array with `socialsToDelete`, the networks chosen in the form). A listener can add a string result
  with `$event->addResult($message)`.
- Context: none.
- Result: Kunena shows the first result as a message.
- Dispatched in: `src/admin/src/Controller/ToolsController.php`, on the dispatcher of the application.
- Event class: `Kunena\Forum\Libraries\Event\KunenaBeforeDeleteSocialsEvent` (result aware, results are strings).
- Note: the same plugin checks as `onKunenaBeforeModifySocials`.

## Joomla content plugins

Kunena also runs Joomla `content` plugins on forum text through Joomla's `onContentPrepare`: with the context
`com_kunena.bbcode` for the text of an article in the BBCode tag `[article]` (`src/libraries/kunena/src/BBCode/KunenaBBCode.php`),
and with the context `kunena.contentprepare.<target>` when the option of the Kunena system plugin is on
(`src/libraries/kunena/src/Html/KunenaParser.php`). The Joomla events that Kunena only listens to or forwards are not
listed on this page.
