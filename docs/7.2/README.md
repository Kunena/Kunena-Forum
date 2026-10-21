# What is new in Kunena 7.2

Every new feature of Kunena 7.2 has a page with what it does, how it looks and which options it has. The pictures are made on a test
forum with the Aurelia template in the Cassiopeia template of Joomla.

All the pages have the label `feature`. The number of the issue is the issue in the repository on GitHub.

## Writing and reading

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Drafts](features/drafts.md) | Users |  | Save a topic, a reply or an edit as a draft and finish it later. |
| [Markdown and safe HTML in messages](features/markdown-and-html.md) | Administrators, users |  | Messages can use Markdown, and a safe set of HTML tags, next to BBCode. |
| [Link cards for pasted links](features/link-cards.md) | Administrators, users | [#3769](https://github.com/Kunena/Kunena-Forum/issues/3769) | A link in a message becomes a card with the title, a short text and the picture of the page. |
| [Spotify and Deezer players](features/spotify-and-deezer.md) | Administrators, users | [#3769](https://github.com/Kunena/Kunena-Forum/issues/3769) | A link to a Spotify or Deezer track, album, playlist, show or episode is shown as the player of that service. |
| [Maps with OpenStreetMap](features/maps-openstreetmap.md) | Administrators, users | [#6088](https://github.com/Kunena/Kunena-Forum/issues/6088) | The [map] BBCode and the location links can use OpenStreetMap, without a key. |
| [Mentions: search while typing, e-mail and SCEditor](features/mentions.md) | Users |  | Typing @ and some letters finds the users, also on a new topic and in the SCEditor, and a mentioned user can get an e-mail. |
| [Tag topics with Joomla Tags](features/tags.md) | Administrators, moderators, users |  | Topics get tags of the Joomla component Tags, shown as labels and usable as a filter. |
| [Suggest similar topics](features/similar-topics.md) | Users |  | While the subject of a new topic is typed, topics with a similar subject are shown. |
| [The first post of a topic on every page](features/first-post-on-every-page.md) | Users |  | The first message of a topic is shown on top of every page of a long topic. |
| [Topic status: Mark as solved](features/topic-status-solved.md) | Users, moderators | [#5146](https://github.com/Kunena/Kunena-Forum/issues/5146) | A Mark as solved button, the name under the topic icons and a batch status for moderators. |
| [Filter bar on the lists of topics](features/topic-list-filter.md) | Users |  | Filter a list of topics on category, status, tag, words in the subject and author, without leaving the page. |
| [Topic lists and index reload in the background](features/background-reload.md) | Users |  | The lists of topics and the forum index update by themselves, and new items blink. |
| [Order of the attachments of a message](features/attachments-order.md) | Administrators |  | Choose how the attachments of a message are sorted. |
| [Members box: filter the online users by role](features/members-box.md) | Users |  | Click a chip of the legend to show only the online users with that role. |

## Users and profiles

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Follow User](features/follow-user.md) | Users |  | Follow another user: e-mail on new topics, a list of the latest topics and a marker in the lists. |
| [Ignore User](features/ignore-user.md) | Users |  | Hide the messages, topics and e-mails of a user you do not want to read. |
| [Three names for a user: username, real name and nickname](features/user-names.md) | Administrators, users |  | Show the username, the real name or a nickname, and let users choose. |
| [Badges for user groups and ranks](features/badges.md) | Administrators |  | Coloured labels next to the name of a user, for a Joomla user group or a Kunena rank. |
| [Ranks linked to a Joomla user group](features/ranks-user-groups.md) | Administrators | [#8339](https://github.com/Kunena/Kunena-Forum/issues/8339) | A rank can be given to every member of a Joomla user group. |
| [Social websites: edit the providers, retire the dead ones](features/social-websites.md) | Administrators |  | A view to add and remove the social websites of the profile; Skype, Yahoo Messenger and Google+ are gone. |
| [Delete a user in Joomla and request the deletion of an account](features/account-deletion.md) | Administrators, users |  | Links to delete a user in Joomla, and a button to ask for the removal of the own account. |

## Moderation and safety

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Ban button on messages](features/quick-ban.md) | Moderators, administrators | [#4158](https://github.com/Kunena/Kunena-Forum/issues/4158) | Ban a user from the Moderate menu of a message, with the duration and the reason in one box. |
| [Frontend moderation log](features/moderation-log.md) | Moderators, administrators |  | A page with the actions of the moderators, in the forum itself. |
| [Deleted topics answer with 410 Gone](features/deleted-topics-410.md) | Administrators |  | A deleted topic tells the search engines it is gone (410), instead of a 404 error. |
| [Report the author of a message to StopForumSpam](features/stopforumspam-report.md) | Moderators, administrators |  | A button in the Moderate menu reports a spammer to StopForumSpam. |
| [Reports: handled by plugins, and the plugin Reports in a category](features/reports.md) | Moderators, administrators | [#1077](https://github.com/Kunena/Kunena-Forum/issues/1077) | A report of a post can become a topic for the moderators, and the e-mail to the moderators is an option. |
| [Access by rank: the plugin Kunena - ACL](features/category-access-rank.md) | Administrators |  | Restrict a category to users with a minimum rank (by post count). |
| [Choose the default announcement](features/default-announcement.md) | Administrators | [#1612](https://github.com/Kunena/Kunena-Forum/issues/1612) | The administrator chooses which announcement is the banner, with a star on the list of announcements. |
| [GeoIP plugin: the place next to the IP address](features/geoip.md) | Moderators, administrators | [#6771](https://github.com/Kunena/Kunena-Forum/issues/6771) | The IP address of a message gets its place (city, region and country) for the people who see the IP address. |

## Categories and menus

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Create, edit and delete categories in the forum](features/frontend-categories.md) | Administrators | [#3878](https://github.com/Kunena/Kunena-Forum/issues/3878) | Administrators manage sections and categories from the forum pages, without the backend. |
| [Category alias with the id](features/category-alias-with-id.md) | Administrators | [#5802](https://github.com/Kunena/Kunena-Forum/issues/5802) | An option to start the automatic alias of a category with its id, for example 12-general. |
| [A link to a category: forum#123](features/category-links.md) | Users | [#6857](https://github.com/Kunena/Kunena-Forum/issues/6857) | An address like example.com/forum#123 goes to the category, and opens the section when it is collapsed. |
| [Upload an icon image for a category](features/category-icon-image.md) | Administrators |  | A category can have its own icon image instead of an icon class. |
| [Hide the replies for guests, per category](features/hide-replies-for-guests.md) | Administrators | [#3104](https://github.com/Kunena/Kunena-Forum/issues/3104) | A category can hide the replies for guests, show them, or use the global option. |
| [Users see only their own topics (helpdesk)](features/own-topics-only.md) | Administrators | [#6758](https://github.com/Kunena/Kunena-Forum/issues/6758) | A category can work as a helpdesk: a user only sees the topics he started. |
| [Close inactive topics for replies](features/close-inactive-topics.md) | Administrators |  | After a number of days without a message, a topic can not get a reply any more. |
| [Multilanguage support](features/multilanguage.md) | Administrators, users |  | A language for each category and topic, a language filter, and translated categories. |

## Mail and notifications

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Notification e-mails: unsubscribe from the e-mail itself](features/unsubscribe-from-email.md) | Users |  | Every subscription e-mail has a link, and the headers for one-click unsubscribe. |
| [Popup notifications](features/popup-notifications.md) | Users, administrators |  | A modern popup with a link when you are mentioned, get a reply, or a topic starts in a category you follow. |
| [Inbox: private messages and notifications in one place](features/inbox.md) | Users, administrators | [#36](https://github.com/Kunena/Kunena-Forum/issues/36) | The plugin Kunena - Inbox gives every user private messages and a list of notifications. |
| [Mail templates of Joomla for the e-mails of the forum](features/mail-templates.md) | Administrators | [#6639](https://github.com/Kunena/Kunena-Forum/issues/6639) | The e-mails of the forum can be edited as mail templates in Joomla, in HTML or plain text. |
| [Forum headers and threading headers in e-mails](features/email-headers.md) | Administrators |  | Mail programs can filter on the forum, and show the e-mails of a topic as one thread. |

## Administration

| Feature | For | Issue | What it does |
| --- | --- | --- | --- |
| [Multiple forums on one site](features/multi-forum-menu.md) | Administrators | [#780](https://github.com/Kunena/Kunena-Forum/issues/780) | A Forum Home menu item can show only its own categories. |
| [Backend: menu icons, order of the views and update check](features/administration-dashboard.md) | Administrators |  | A cleaner administration: icons in the menu, the views in alphabetical order and a real update check. |
| [Choose the Joomla content plugins for the forum](features/content-plugins.md) | Administrators |  | Run only the content plugins you choose on the text of the forum. |
| [New options of the Aurelia template](features/template-options.md) | Administrators |  | An overview of the options which the Aurelia template has now. |

## Making the pictures again

The pictures in `images/` are made by the Cypress specs in `tests/System/docs`; see [How the pictures are made](screenshots.md).
