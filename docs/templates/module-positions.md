# Module positions

Kunena shows Joomla modules in its own module positions: at the top and bottom of every page, on the forum index, between the topics
of a list, between the messages of a topic, in the profile next to a message, and more. This page lists every position of the
templates Aurelia and Agora, where it is shown, and the target of the Joomla content plugins of its modules.

## Placing a module

1. In the administrator of Joomla, open **Content › Site Modules** and create or open a module.
2. In **Position**, type the name of the position (for example `kunena_index_top`) and press Enter: the positions of Kunena are not in
   the list of the Joomla template, a typed name is accepted.
3. Assign the module to the menu items of the forum (**Menu Assignment**), or to all pages.

Most positions are only shown when the template of Kunena shows modules: **Kunena › Templates › (template) › Display modules**
(on by default). The table says which positions are shown without it.

An empty position draws nothing: a page asks for many positions (a topic asks for a few for each message), and only the ones with a
module cost anything.

## Every page

The frame of every page of the forum (`layouts/page/default.php`), and what is in it on every page.

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_top` | Top of the page, under the menu of the forum | Display modules | `page_top`; on a list of topics `topic_list_default` first |
| `kunena_announcement` | Above the announcement, under the breadcrumb | Always | `page_top` |
| `kunena_bottom` | Bottom of the page, above the footer | Display modules | `page_bottom` |
| `kunena_login` | In the login box of the menu (desktop and mobile), for a guest | The menu shows the login box | `page_login` |
| `kunena_logout` | In the user menu, for a logged in user | The login integration of Kunena has its option *login* on (the default) | `page_login` |
| `kunena_logout_bottom` | At the bottom of the user menu, for a logged in user | Always (in the user menu) | `page_login` |
| `kunena_custom_top` | Above the text of a message page of the forum (offline, for members only) | Always (on such a page) | `page_custom` |
| `kunena_custom_bottom` | Under the text of a message page of the forum | Always (on such a page) | `page_custom` |

## Forum index

`layouts/category/index/default.php`.

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_index_top` | Above the categories | Display modules | `index_top` |
| `kunena_section_top_<n>` | Above section *n* | Display modules | `index_sections` |
| `kunena_section_<n>` | Under section *n* | Display modules | `index_sections` |
| `kunena_index_bottom` | Under the categories | Display modules | `index_bottom` |

The sections are counted 1, 2, 3... in the order of the page, above and below together: the first section has `kunena_section_top_1`
above it and `kunena_section_2` under it, the second section `kunena_section_top_3` and `kunena_section_4`.

## Lists of topics

The recent topics, the unread topics, the topics of a user (`layouts/topic/list/default.php`).

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_topic_<n>` | Between the topics: a full row of the table after topic *n - 1* | Display modules | `topic_list_rows` |

The rows are counted from 2: `kunena_topic_2` is under the first topic, `kunena_topic_3` under the second. `kunena_top` of the frame has
the target `topic_list_default` first on these pages (see [Content plugins](#content-plugins)).

A **category** with its topics (`layouts/category/item/default.php`) shows no module between its topics: its rows get the name
`kunena_topic_<n>`, but a row does not draw a module position. `kunena_top` has the target `topic_list_default` there too.

## Lists of posts

The recent posts and the posts of a user (`layouts/message/list/default.php`, and the layout set *profile* of Agora).

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `<n>` | A full row of the table after post *n* | Always | `message_list_rows` |

The name of these positions is the bare number of the row in the list, counted from 0, and the row 0 has none: `1` is under the second
post, `2` under the third. A module for these rows gets a number as its position.

## Topic

`layouts/topic/item/default.php`, and the layout sets *chat* and *columns* of Agora.

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_topictitle` | Under the title of the topic | Display modules | `topic_top` |
| `kunena_poll` | Under the poll of the topic | Display modules | `topic_poll` |
| `kunena_msg_<n>` | Under message *n* | Always | `topic_messages` |
| `kunena_msg_row_<n>` | After message *n* of the page, outside its box | Display modules | `topic_messages` |

`kunena_msg_<n>` counts the messages of the whole topic from 1 (the first message of the topic is 1, also on page 3).
`kunena_msg_row_<n>` counts the messages of the page from 1.

### The profile of the author of a message

The profile next to a message (`layouts/user/profile/default.php`, the side profile) or above it (`horizontal.php`), as the template
shows it.

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_profile_default` | Side profile, under the details | Always | `topic_profiles` |
| `kunena_profile_default_<userid>` | Side profile, only for the user with that id | Always | `topic_profiles` |
| `kunena_profile_default_<rank>` | Side profile, only for the users with that special rank (its id) | Always | `topic_profiles` |
| `kunena_topicprofile` | Side profile, under the details | Always | `topic_profiles` |
| `kunena_topicprofilemore` | Side profile, in the part which opens with *More* | Always | `topic_profiles` |
| `kunena_profile_horizontal` | Horizontal profile | Always | `topic_profiles` |
| `kunena_profile_horizontal_<userid>` | Horizontal profile, only for the user with that id | Always | `topic_profiles` |
| `kunena_profile_horizontal_<rank>` | Horizontal profile, only for the users with that special rank | Always | `topic_profiles` |

The ids of the users and of the ranks are both numbers: `kunena_profile_default_5` is shown for the user 5 and for the users with the
rank 5. A user without a special rank has the rank 0.

## Profile

| Position | Where | Shown when | Content plugin target |
| --- | --- | --- | --- |
| `kunena_summary` | Under the summary of the profile, above its tabs | Always | `profile_summary` |

## Content plugins

The plugin **System - Kunena Forum** can run the Joomla content plugins on the forum: **Joomla Content Plugin Support** on, then
**Run plugins on** says on what. Each module position has a target in that list (the last column of the tables), so a content plugin
can change the modules of a position: a `{loadmoduleid}`, an e-mail cloak, a shortcode.

- A position is prepared only when its target is chosen in **Run plugins on**.
- **Choose the Joomla content plugins you want to load** limits the plugins which run, as it does for the text of the messages.
- A position with two targets (`kunena_top` on a list of topics: `topic_list_default`, then `page_top`) is prepared once, for the
  first of its targets which is chosen.
- The context of the event is `kunena.contentprepare.<target>`, for example `kunena.contentprepare.topic_messages`.

A developer of an extension gives a position of a page of his own a target with
`Kunena\Forum\Site\Layout\Widget\WidgetModule::prepareWith('position', 'target')` in the controller of the page, before the page is
drawn. A name which ends with `*` gives all the positions which start with it (`kunena_msg_*`). The class
`Kunena\Forum\Libraries\Layout\KunenaModuleContent` keeps the targets of the request.

---

[← Template overrides](README.md)
