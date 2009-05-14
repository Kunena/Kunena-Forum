<?php
/**
* @version $Id: kunena.polish.php 597 2009-04-03 17:50:33Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Installed version');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'Licencja');
DEFINE('_KUNENA_PROFILE_NO_USER', 'User does not exist.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'User has not yet visited forum and has no profile.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Search Results');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Advanced Search');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Search by Keyword');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Keywords');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Search entire posts');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Search titles only');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Search by User Name');
DEFINE('_KUNENA_SEARCH_UNAME', 'User Name');
DEFINE('_KUNENA_SEARCH_EXACT', 'Exact Name');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Messages posted by');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Threads started by');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Activity in threads');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Search Options');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Find Threads with');
DEFINE('_KUNENA_SEARCH_LEAST', 'At least');
DEFINE('_KUNENA_SEARCH_MOST', 'At most');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Answers');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Find Posts from');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Any date');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Last visit');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Yesterday');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'A week ago');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 weeks ago');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'A month ago');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 months ago');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 months ago');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'A year ago');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'And newer');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'And older');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sort Results by');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Title');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Number of posts');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Number of views');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Thread start date');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Posting date');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'User name');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Increasing order');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Decreasing order');
DEFINE('_KUNENA_SEARCH_START', 'Jump to Result Number');
DEFINE('_KUNENA_SEARCH_LIMIT5', 'Show 5 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT10', 'Show 10 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT15', 'Show 15 Search Results');
DEFINE('_KUNENA_SEARCH_LIMIT20', 'Show 20 Search Results');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Search in Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'All Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Also search in child forums');
DEFINE('_KUNENA_SEARCH_SEND', 'Send');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Cancel');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'No messages containing all your search terms were found.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'At least one keyword should be over 3 characters long!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'You don\'t have moderator permissions!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'This thread has <b>NOT</b> been added to your favorites');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Sync the Kunena user table with the Joomla user table');
DEFINE('_POST_FORGOT_EMAIL', 'You forgot to include your e-mail address.  Click your browser&#146s back button to go back and try again.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Everything deletedâ some attachment files were missing!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Main Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'In order to provide additional information for you guests and members, the forum header can be leveraged to display text at the very top of a particular category.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Welcome Mat');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'We encourage new members to post a short introduction of themselves in this forum category. Get to know each other and share you common interests.
');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Welcome to the Kunena forum![/b]

Tell us and our members who you are, what you like and why you became a member of this site.
We welcome all new members and hope to see you around a lot!
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Suggestion Box');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Have some feedback and input to share?
Don\'t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'This is the optional Forum header for the Suggestion Box.
');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Welcome to Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Welcome to Kunena![/b][/size]

Thank you for choosing Kunena for your community forum needs in Joomla.

Kunena, translated from Swahili meaning "to speak", is built by a team of open source professionals with the goal of providing a top-quality, tightly unified forum solution for Joomla. Kunena even supports social networking components like Community Builder and JomSocial.


[size=4][b]Additional Kunena Resources[/b][/size]

[b]Kunena Documentation:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Support Forum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena Downloads:[/b] [url=http://joomlacode.org/gf/project/kunena/frs/]http://www.kunena.com/downloads[/url]
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Submit your feature ideas:[/b] [url=http://kunena.uservoice.com/pages/general?referer_type=top3]http://www.kunena.com/uservoice[/url]
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Follow Kunena on Twitter:[/b] [url=https://twitter.com/kunena]http://www.kunena.com/twitter[/url]
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Włącz kolorowanie kodu');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the Kunena code tag highlighting java script. If your members post php and similar code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from getting malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Domyślny typ RSS');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Choose between RSS feeds by thread or post. By thread means that only one entry per thread will be listed in the RSS feed, independet of how many posts have been made within that thread. By thread creates a shorter more compact RSS feed but will not list every reply made.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Według wątków');
DEFINE('_COM_A_RSS_BY_POST', 'Według postów');
DEFINE('_COM_A_RSS_HISTORY', 'Historia RSS');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Select how much history should be included in the RSS feed. Default is 1 month but you might want to limit it to 1 week on high volume sites.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 tydzień');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 miesiąc');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 rok');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Domyślna strona Kunena');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default Kunena page that gets displayed when a forum link is clicked or the forum is entered initially. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Ostatnie rozmowy');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Moje rozmowy');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategorie');
DEFINE('_KUNENA_BBCODE_HIDE', 'Ta zawartość jest ukryta dla niezarejestrowanych użytkowników:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Forum nadrzędne nie może być takie samo.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Forum nadrzędne jest jednym z forów podrzędnych.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID nie istnieje.');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Zapomniałeś wipsać imienia.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Zapomniałeś wipsać adresu email.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Zapomniałeś wipsać tematu.');
DEFINE('_KUNENA_EDIT_TITLE', 'Edytuj swoje dane');
DEFINE('_KUNENA_YOUR_NAME', 'Imię:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'Nazwa użytkownika:');
DEFINE('_KUNENA_PASS', 'Hasło:');
DEFINE('_KUNENA_VPASS', 'Powtórz hasło:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Twoje dane zostały zapisane.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'Ustawienia BBCode');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Pokazuj spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Pokazuj video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Pokazuj eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Skracaj długie adresy URL');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Set to &quot;Yes&quot; if you want long URLs to be trimmed. See URL trim front and back settings.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Ilość znaków z początku');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Number of characters for front portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Ilość znaków z końca');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Automatycznie wstawiaj filmy z YouTube');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Set to &quot;Yes&quot; if you want youtube video urls to get automatically embedded.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Automatycznie wstawiaj produkty z eBay');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay widget language code');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Czas sesji');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Scal');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Scal ten wątek z');
DEFINE('_POST_MERGE_GHOST', 'Pozostaw ukrytą kopię');
DEFINE('_POST_SUCCESS_MERGE', 'Wątek scalony pomyślnie.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Błąd scalania.');
DEFINE('_GEN_SPLIT', 'Podziel');
DEFINE('_GEN_DOSPLIT', 'OK');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Wątek podzielony pomyślnie.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Temat zmieniony pomyślnie.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Zmiana tematu nie została ukończona.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Błąd podziału.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplikat, identyczna wiadomość została zignorowana.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'Scal');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Ten wątek został usunięty z ulubionych.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Ten wątek <b>NIE</b> został usunięty z ulubionych');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Twoje żądanie usunięcia z ulubionych zostało wykonane.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Ten wątek został unsunięty z subskrypcji.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Ten wątek <b>NIE</b> został unsunięty z subskrypcji');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Twoje żądanie usunięcia z subskrypcji zostało wykonane.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Nie wybrano kategorii docelowej. Nic nie zostało przeniesione.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Ostatnie dyskusje');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Moje dyskusje');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Dyskusje w których barałem udział');
DEFINE('_KUNENA_CATEGORY', 'Kategoria:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorie');
DEFINE('_KUNENA_POSTED_AT', 'Utworzony');
DEFINE('_KUNENA_AGO', 'temu');
DEFINE('_KUNENA_DISCUSSIONS', 'Dyskusje');
DEFINE('_KUNENA_TOTAL_THREADS', 'Wątków łącznie:');
DEFINE('_SHOW_DEFAULT', 'Domyślny');
DEFINE('_SHOW_MONTH', 'Miesiąc');
DEFINE('_SHOW_YEAR', 'Rok');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Kopiowanie "%src%" do "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Saving css file should be here...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Attachment table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Attachments in messages table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Could not delete the post(s) - nothing else deleted');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum as well");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena component został pomyślnie odinstalowany!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena Forum Component version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Utworzony: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Brak forów do przeszukania.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Błąd dodawania użytkowników:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Użytkownicy zsynchronizowani; Usunięto:');
DEFINE('_KUNENA_USERSSYNCADD', ', dodaj:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'profile użytkowników.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'No profiles found eligible for syncronizing.');
DEFINE('_KUNENA_SYNC_USERS', 'Synchronizuj użytkowników');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synchronizuj użytkowników Kunena z tabelą użytkowników Joomla!');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Powiadamiaj administratorów');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Edytuj ranking');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Ukryj email');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Niedziela');
DEFINE('_KUNENA_DT_LDAY_MON', 'Poniedziałek');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Wtorek');
DEFINE('_KUNENA_DT_LDAY_WED', 'środa');
DEFINE('_KUNENA_DT_LDAY_THU', 'Czwartek');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Piątek');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Sobota');
DEFINE('_KUNENA_DT_DAY_SUN', 'Ni');
DEFINE('_KUNENA_DT_DAY_MON', 'Po');
DEFINE('_KUNENA_DT_DAY_TUE', 'Wt');
DEFINE('_KUNENA_DT_DAY_WED', 'śr');
DEFINE('_KUNENA_DT_DAY_THU', 'Cz');
DEFINE('_KUNENA_DT_DAY_FRI', 'Pt');
DEFINE('_KUNENA_DT_DAY_SAT', 'So');
DEFINE('_KUNENA_DT_LMON_JAN', 'Styczeń');
DEFINE('_KUNENA_DT_LMON_FEB', 'Luty');
DEFINE('_KUNENA_DT_LMON_MAR', 'Marzec');
DEFINE('_KUNENA_DT_LMON_APR', 'Kwiecień');
DEFINE('_KUNENA_DT_LMON_MAY', 'Maj');
DEFINE('_KUNENA_DT_LMON_JUN', 'Czerwiec');
DEFINE('_KUNENA_DT_LMON_JUL', 'Lipiec');
DEFINE('_KUNENA_DT_LMON_AUG', 'Sierpień');
DEFINE('_KUNENA_DT_LMON_SEP', 'Wrzesień');
DEFINE('_KUNENA_DT_LMON_OCT', 'Październik');
DEFINE('_KUNENA_DT_LMON_NOV', 'Listopad');
DEFINE('_KUNENA_DT_LMON_DEV', 'Grudzień');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'May');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Oct');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec');
DEFINE('_KUNENA_CHILD_BOARD', 'Forum podrzędne');
DEFINE('_WHO_ONLINE_GUEST', 'Gość');
DEFINE('_WHO_ONLINE_MEMBER', 'Użytkownik');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'żaden');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Procesor obrazków:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Kliknij tutaj aby kontynować...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Zastosuj!');
DEFINE('_KUNENA_NO_ACCESS', 'Nie masz dostępu do tego forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% temu');
DEFINE('_KUNENA_DATE_YEARS', 'Lata');
DEFINE('_KUNENA_DATE_MONTHS', 'Miesiące');
DEFINE('_KUNENA_DATE_WEEKS','Tygodnie');
DEFINE('_KUNENA_DATE_DAYS', 'Dni');
DEFINE('_KUNENA_DATE_HOURS', 'Godzin');
DEFINE('_KUNENA_DATE_MINUTES', 'Minut');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Are you sure you want to remove the sample data? This action is irreversible.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumheader:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum display");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'User Edit Time');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Set to 0 for unlimited time, else window
in seconds from post or last modification to allow edit.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], allows
storing a modification up to 600 seconds after edit link disappears');
DEFINE('_KUNENA_HELPPAGE','Enable Help Page');
DEFINE('_KUNENA_HELPPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Help page.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Show help in Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','If set to &quot;Yes&quot; help content text will be include in Kunena and Help external page link will not work. <b>Note:</b> You should add "Help Content ID" .');
DEFINE('_KUNENA_HELPPAGE_CID','Help Content ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','You should set <b>"YES"</b> "Show help in Kunena" setting.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Help external page link');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','If you show help external link, please set <b>"NO"</b> "Show help in Kunena" setting.');
DEFINE('_KUNENA_RULESPAGE','Enable Rules Page');
DEFINE('_KUNENA_RULESPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Show rules in Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','If set to &quot;Yes&quot; rules content text will be include in Kunena and Rules external page link will not work. <b>Note:</b> You should add "Rules Content ID" .');
DEFINE('_KUNENA_RULESPAGE_CID','Rules Content ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','You should set <b>"YES"</b> "Show rules in Kunena" setting.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Rules external page link');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','If you show rules external link, please set <b>"NO"</b> "Show rules in Kunena" setting.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library not found');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Library not found');
DEFINE('_KUNENA_GD_INSTALLED','GD is avabile version ');
DEFINE('_KUNENA_GD_NO_VERSION','Can not detect GD version');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD isnt installed, you can get more info ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Small Image Height :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Small Image Width :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium Image Height :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Medium Image Width :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Large Image Height :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Large Image Width :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar Quality');
DEFINE('_KUNENA_WELCOME','Witamy w Kunena forum!');
DEFINE('_KUNENA_WELCOME_DESC','Thank you for choosing Kunena as your board solution. This screen will give you a quick overview of all the various statistics of your board. The links on the left hand side of this screen allow you to control every aspect of your board experience. Each page will have instructions on how to use the tools.');
DEFINE('_KUNENA_STATISTIC','Statystyki');
DEFINE('_KUNENA_VALUE','Wartość');
DEFINE('_GEN_CATEGORY','Kategoria');
DEFINE('_GEN_STARTEDBY','Rozpoczęte przez: ');
DEFINE('_GEN_STATS','statystyki');
DEFINE('_STATS_TITLE',' statystyki - forum');
DEFINE('_STATS_GEN_STATS','Statystyki główne');
DEFINE('_STATS_TOTAL_MEMBERS','Użytkowników:');
DEFINE('_STATS_TOTAL_REPLIES','Odpowiedzi:');
DEFINE('_STATS_TOTAL_TOPICS','Tematów:');
DEFINE('_STATS_TODAY_TOPICS','Tematów dzisiaj:');
DEFINE('_STATS_TODAY_REPLIES','Odpowiedzi dzisiaj:');
DEFINE('_STATS_TOTAL_CATEGORIES','kategorii:');
DEFINE('_STATS_TOTAL_SECTIONS','Sekcji:');
DEFINE('_STATS_LATEST_MEMBER','Najnowszy użytkownik:');
DEFINE('_STATS_YESTERDAY_TOPICS','Tematów wczoraj:');
DEFINE('_STATS_YESTERDAY_REPLIES','Odpowiedzi wczoraj:');
DEFINE('_STATS_POPULAR_PROFILE','10 najpopularniejszych użytkowników (Wyświetleń profilu)');
DEFINE('_STATS_TOP_POSTERS','Najwięcej postów');
DEFINE('_STATS_POPULAR_TOPICS','Najpopularniejsze tematy');
DEFINE('_COM_A_STATSPAGE','Włącz strone statystyk');
DEFINE('_COM_A_STATSPAGE_DESC','If set to &quot;Yes&quot; a public link in the header menu will be shown to your forum Stats page. This page will display various statistics about your forum. <em>Stats page will be always available to admin regardless of this setting!</em>');
DEFINE('_COM_C_JBSTATS','Statystyki forum');
DEFINE('_COM_C_JBSTATS_DESC','Statystyki forum');
define('_GEN_GENERAL','Ogólne');
define('_PERM_NO_READ','Nie posiadasz wystarczających uprawnień aby uzyskać dostęp do tego forum.');
DEFINE ('_KUNENA_SMILEY_SAVED','Emotikona zapisana');
DEFINE ('_KUNENA_SMILEY_DELETED','Emotikona usunięta');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Kod już istnieje');
DEFINE ('_KUNENA_MISSING_PARAMETER','Brakuje parametru');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rank już istnieje');
DEFINE ('_KUNENA_RANK_DELETED','Rank usunięty');
DEFINE ('_KUNENA_RANK_SAVED','Rank zapisany');
DEFINE ('_KUNENA_DELETE_SELECTED','Usuń zaznaczone');
DEFINE ('_KUNENA_MOVE_SELECTED','Przenieś zaznaczone');
DEFINE ('_KUNENA_REPORT_LOGGED','Zalogowany');
DEFINE ('_KUNENA_GO','OK');
DEFINE('_KUNENA_MAILFULL','W subskrypcji email zawieraj całą treść wiadomości');
DEFINE('_KUNENA_MAILFULL_DESC','If No - subscribers will receive only titles of new messages');
DEFINE('_KUNENA_HIDETEXT','Prosze się zalogować aby uzyskać dostęp do zawartości!');
DEFINE('_BBCODE_HIDE','Tekst ukryty: [hide]dowolny tekst ukryty[/hide] - ukryj część wiadomości przed goścmi');
DEFINE('_KUNENA_FILEATTACH','Załącznik: ');
DEFINE('_KUNENA_FILENAME','Nazwa pliku: ');
DEFINE('_KUNENA_FILESIZE','Rozmiar pliku: ');
DEFINE('_KUNENA_MSG_CODE','Kad: ');
DEFINE('_KUNENA_CAPTCHA_ON','System Antyspamowy');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA system On/Off');
DEFINE('_KUNENA_CAPDESC','Tutaj wpisz kod');
DEFINE('_KUNENA_CAPERR','Kod nieprawidłowy!');
DEFINE('_KUNENA_COM_A_REPORT', 'Raportowanie wiadomości');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'If you want to users report any message, choose yes.');
DEFINE('_KUNENA_REPORT_MSG', 'Wiadomość zgłoszona');
DEFINE('_KUNENA_REPORT_REASON', 'Powód');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Twoja wiadomość');
DEFINE('_KUNENA_REPORT_SEND', 'Wyślij');
DEFINE('_KUNENA_REPORT', 'Zgłoś do moderatora');
DEFINE('_KUNENA_REPORT_RSENDER', 'Nadawca zgłoszenia: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Powód zgłoszenia: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Zgłoszenie: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Autor wiadomości: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Temat wiadomości: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Wiadomość: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Link do wiadomości: ');
DEFINE('_KUNENA_REPORT_INTRO', 'przysłał wiadomość z powodu');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Zgłoszenie wysłane!');
DEFINE('_KUNENA_EMOTICONS', 'Emotikony');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Emotikona');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kod');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Edytuj emotikonę');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Edytuj emotikony');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Pasek emotikon');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nowa emotikona');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Więcej emotikon');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Zamknij to okno');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Dodatkowe Emotikony');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Wybierz emotikonę');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Allow username change');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Allow username change on myprofile plugin page');
DEFINE ('_KUNENA_RECOUNTFORUMS','Recount Categories Stats');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','All category statistics now are recounted.');
DEFINE ('_KUNENA_EDITING_REASON','Powód edycji');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Ostatnia edycja');
DEFINE ('_KUNENA_BY','przez');
DEFINE ('_KUNENA_REASON','Powód');
DEFINE('_GEN_GOTOBOTTOM', 'Idź na dół');
DEFINE('_GEN_GOTOTOP', 'Idź na górę');
DEFINE('_STAT_USER_INFO', 'Informacja o Użytkowniku');
DEFINE('_USER_SHOWEMAIL', 'Pokaż adres email'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Pokaż status aktywności');
DEFINE('_KUNENA_HIDDEN_USERS', 'Użytkownicy ukryci');
DEFINE('_KUNENA_SAVE', 'Zapisz');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Domyślna galeria');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Moje informacje');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Podumowanie');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Mój avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Ustawienia forum');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Wygląd');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Mój profil');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Moje posty');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Moje subskrybcje');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Moje ulubione');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Wiadomości prywatne');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Skrynka odbiorcza');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nowa wiadomość');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Wychodzące');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Kosz');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Ustawienia');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakty');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'List zablokowanych');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Dodatkowe informacje');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Imię');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Nazwa użytkownika');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Email');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Typ użytkownika');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Data rejestracji');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Ostatnia wizyta');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Posty');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Pokaż profil');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Własny tekst');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Płeć');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Data urodzenia');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Rok (RRRR) - Miesiąc (MM) - Dzień (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Lokalizacja');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Twój numer ICQ.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Twój nick w AOL Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Twój nick w Yahoo! Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Twoja nazwa w Skype.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Twój nick w Gtalk.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Strona www');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Nazwa strony');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Przykład: jak-oszczędzać');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Adres URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Przykład: www.jak-oszczedzac.pl');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Twój adres MSN messenger.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Podpis');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Mężczyzna');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Kobieta');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Posty usunięte pomyślnie');
DEFINE('_KUNENA_DATE_YEAR', 'Rok');
DEFINE('_KUNENA_DATE_MONTH', 'Miesiąc');
DEFINE('_KUNENA_DATE_WEEK','Tydzień');
DEFINE('_KUNENA_DATE_DAY', 'Dzień');
DEFINE('_KUNENA_DATE_HOUR', 'Godzina');
DEFINE('_KUNENA_DATE_MINUTE', 'Minuta');
DEFINE('_KUNENA_IN_FORUM', ' w forum: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum w: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Pamiętaj, że nawet ukryte emotikony działają');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Narzędzia forum');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Lista użytkowników');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s has <b>%d</b> registered users');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Wpisz czego chcesz szukać!');
DEFINE ('_KUNENA_USRL_SEARCH','Znajdź użytkownika');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Szukaj');
DEFINE ('_KUNENA_USRL_LIST_ALL','Pokaż wszystko');
DEFINE ('_KUNENA_USRL_NAME','Imię');
DEFINE ('_KUNENA_USRL_USERNAME','Nazwa użytkownika');
DEFINE ('_KUNENA_USRL_GROUP','Grupa');
DEFINE ('_KUNENA_USRL_POSTS','Posty');
DEFINE ('_KUNENA_USRL_KARMA','Ocena');
DEFINE ('_KUNENA_USRL_HITS','Wyświetleń');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Typ użytkownika');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Data rejestracji');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Ostatnie logowanie');
DEFINE ('_KUNENA_USRL_NEVER','Nigdy');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Obrazek');
DEFINE ('_KUNENA_USRL_ASC','Rosnąco');
DEFINE ('_KUNENA_USRL_DESC','Malejąco');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Pokaż');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%m/%d/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Lista użytkowników');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Number of userlist rows');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Number of userlist rows');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Show users online status');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Pokaż Avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Pokaż imię');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Pokaż nazwę użytkownika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Pokaż grupę');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Pokaż ilość postów');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Pokaż ocenę');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Pokaż Email');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Pokaż typ użytkownika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Pokaż datę rejestracji');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Pokaż datę ostatnij wizyty');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Pokaż wyświetlenia profilu');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Database Wizard');
DEFINE('_KUNENA_DBMETHOD', 'Wybierz metodę instalacji:');
DEFINE('_KUNENA_DBCLEAN', 'Czysta');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade z Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Kategoria główna');
DEFINE('_KUNENA_REGISTERED', 'Zarejestrowany');
DEFINE('_KUNENA_PUBLICBACKEND', 'Zaplecze publiczne');
DEFINE('_KUNENA_SELECTANITEMTO', 'Wybierz obiekt do');
DEFINE('_KUNENA_ERRORSUBS', 'Wystąpił nieokreślony błąd podczas usuwania');
DEFINE('_KUNENA_WARNING', 'Uwaga...');
DEFINE('_KUNENA_CHMOD1', 'Musisz to chmod na 766 żeby uaktualnić plik.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Twój plik konfiguracyjny to');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Wybierz szablon');
DEFINE('_KUNENA_CONFIGSAVED', 'Konfiguracja zapisana.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'FATAL ERROR: Konfiguracja nie zapisana.');
DEFINE('_KUNENA_TFINW', 'Plik nie jest zapisywalny.');
DEFINE('_KUNENA_FBCFS', 'Plik Kunena CSS zapisany.');
DEFINE('_KUNENA_SELECTMODTO', 'Wybierz moderatora do');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Musisz wybrać forum do prune!');
DEFINE('_KUNENA_DELMSGERROR', 'Błąd usuwania wiadomości:');
DEFINE('_KUNENA_DELMSGERROR1', 'Błąd usuwania tekstu wiadomości:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Błąd usuwania subskrypcji:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned for');
DEFINE('_KUNENA_PRUNEDAYS', 'dni');
DEFINE('_KUNENA_PRUNEDELETED', 'Usunięto:');
DEFINE('_KUNENA_PRUNETHREADS', 'wątków');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Error pruning users:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Users pruned; Deleted:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'user profiles'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'No profiles found eligible for pruning.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena Tables are upgraded to version');
DEFINE('_KUNENA_FORUMCATEGORY', 'Kategoria forum');
DEFINE('_KUNENA_SAMPLWARN1', '-- Make absolutely sure that you load the sample data on completely empty Kunena tables. If anything is in them, it will not work!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Przykładowy Post 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Przykładowe Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Przykładowy Post[/color][/size][/b]\nCongratulations with your new Forum!\n\n[url=http://Kunena.com]- Kunena[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Przykładowe dane wczytane');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Przykładowe dane usunięte');
DEFINE('_KUNENA_CBADDED', 'Profil Community Builder dodany');
DEFINE('_KUNENA_IMGDELETED', 'Obrazek usunięty');
DEFINE('_KUNENA_FILEDELETED', 'Plik usunięty');
DEFINE('_KUNENA_NOPARENT', 'Brak rodzica');
DEFINE('_KUNENA_DIRCOPERR', 'Błąd: Plik');
DEFINE('_KUNENA_DIRCOPERR1', 'could not be copied!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> Component <em>for Joomla! </em> <br />© 2008 - 2009 by www.Kunena.com<br />All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Ustawienia profilu');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'If you have Clexus PM or Community Builder component installed, you can configure Kunena to use the user profile page.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Pokaż profil</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Wszystkie wiadomości');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Tematy');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Rozpoczęte przez');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorie');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Data');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Wyświetlenia');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Brak postów');
DEFINE('_KUNENA_TOTALFAVORITE', 'Ulubionych:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Ilość kolumn forów podrzędnych  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Number of child board column formating under main category ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Subskrypcja zaznaczona domyślnie?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Set to "Yes" If you want to post subscription box always checked');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategoria / Forum musi mieć nazwę');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Pokaż statystyki');
DEFINE('_KUNENA_SHOWSTATSDESC', 'If you want to show Stats, select Yes');
DEFINE('_KUNENA_SHOWWHOIS', 'Pokaż kto jest Online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'If you want to show  Whois Online, select Yes');
DEFINE('_KUNENA_STATSGENERAL', 'Pokaż statystyki ogólne');
DEFINE('_KUNENA_STATSGENERALDESC', 'If you want to show General Stats, select Yes');
DEFINE('_KUNENA_USERSTATS', 'Pokaż popularnych użytkowników');
DEFINE('_KUNENA_USERSTATSDESC', 'If you want to show Popular Stats, select Yes');
DEFINE('_KUNENA_USERNUM', 'Ilość popularnych użytkowników');
DEFINE('_KUNENA_USERPOPULAR', 'Pokaż popularne tematy');
DEFINE('_KUNENA_USERPOPULARDESC', 'If you want to show Popular Subject, select Yes');
DEFINE('_KUNENA_NUMPOP', 'Ilość popularnych tematów');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.5. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instrukcje');
DEFINE('_KUNENA_FINFO', 'Informacje Kunena Forum');
DEFINE('_KUNENA_CSSEDITOR', 'Edytor Kunena Template CSS');
DEFINE('_KUNENA_PATH', 'ścieżka:');
DEFINE('_KUNENA_CSSERROR', 'Pamiętaj: Plik CSS Template musi być zapisywalny.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena User Profile Manager');
DEFINE('_KUNENA_SORTID', 'sortuj według user id');
DEFINE('_KUNENA_SORTMOD', 'sortuj według moderatorów');
DEFINE('_KUNENA_SORTNAME', 'sortuj według nazw');
DEFINE('_KUNENA_VIEW', 'Widok');
DEFINE('_KUNENA_NOUSERSFOUND', 'Nie znaleziono profilów.');
DEFINE('_KUNENA_ADDMOD', 'Dodaj Moderatora do');
DEFINE('_KUNENA_NOMODSAV', 'Nie znaleziono moderatorów. Przeczytaj poniższą \'note\'.');
DEFINE('_KUNENA_NOTEUS',
    'NOTE: Only users which have the moderator flag set in their Kunena Profile are shown here. In order to be able to add a moderator give a user a moderator flag, go to <a href="index2.php?option=com_kunena&task=profiles">User Administration</a> and search for the user you want to make a moderator. Then select his or her profile and update it. The moderator flag can only be set by an site administrator. ');
DEFINE('_KUNENA_PROFFOR', 'Profil dla');
DEFINE('_KUNENA_GENPROF', 'Główne opcje profilu');
DEFINE('_KUNENA_PREFVIEW', 'Preferowany widok:');
DEFINE('_KUNENA_PREFOR', 'Preferowana kolejność wiadoości:');
DEFINE('_KUNENA_ISMOD', 'Jest moderatorem:');
DEFINE('_KUNENA_ISADM', '<strong>Tak</strong> (not changeable, this user is an site (super)administrator)');
DEFINE('_KUNENA_COLOR', 'Kolor');
DEFINE('_KUNENA_UAVATAR', 'Obrazek użytkownika:');
DEFINE('_KUNENA_NS', 'Nie zaznaczono');
DEFINE('_KUNENA_DELSIG', ' zaznacz ten kwadrat aby usunąć podpis');
DEFINE('_KUNENA_DELAV', ' zaznacz ten kwadrat aby usunąć ten obrazek');
DEFINE('_KUNENA_SUBFOR', 'Subskrybcje dla');
DEFINE('_KUNENA_NOSUBS', 'Brak subskrypcji dla tego użytkownika');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Podstawowe');
DEFINE('_KUNENA_BASICSFORUM', 'Podstawowe informacje forum');
DEFINE('_KUNENA_PARENT', 'Rodzic:');
DEFINE('_KUNENA_PARENTDESC',
    'Please note: To create a Category, choose \'Top Level Category\' as a Parent. A Category serves as a container for Forums.<br />A Forum can <strong>only</strong> be created within a Category by selecting a previously created Category as the Parent for the Forum.<br /> Messages can <strong>NOT</strong> be posted to a Category; only to Forums.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Nazwa i opis forum');
DEFINE('_KUNENA_NAMEADD', 'Nazwa:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Opis:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Zaawansowana konfiguracja forum');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Zabezpieczenia i dostęp do forum');
DEFINE('_KUNENA_LOCKEDDESC', 'Set to &quot;Yes&quot; if you want to lock this forum Nobody, except Moderators and Admins can create new topics or replies in a locked forum (or move posts to it).');
DEFINE('_KUNENA_LOCKED1', 'Zablokowane:');
DEFINE('_KUNENA_PUBACC', 'Poziom dostępu publicznego:');
DEFINE('_KUNENA_PUBACCDESC',
    'To create a Non-Public Forum you can specify the minimum userlevel that can see/enter the forum here.By default the minumum userlevel is set to &quot;Everybody&quot;.<br /><b>Please note</b>: if you restrict access on a whole Category to one or more certain groups, it will hide all Forums it contains to anybody not having proper privileges on the Category <b>even</b> if one or more of these Forums have a lower access level set! This holds for Moderators too; you will have to add a Moderator to the moderator list of the Category if (s)he does not have the proper group level to see the Category.<br /> This is irrespective of the fact that Categories can not be Moderated; Moderators can still be added to the moderator list.');
DEFINE('_KUNENA_CGROUPS', 'Załącz podrzędne grupy:');
DEFINE('_KUNENA_CGROUPSDESC', 'Should child groups be allowed access as well? If set to &quot;No&quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_KUNENA_ADMINLEVEL', 'Poziomo dostępu administratora:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'If you create a forum with Public Access restrictions, you can specify here an additional Administration Access Level.<br /> If you restrict the access to the forum to a special Public Frontend user group and don\'t specify a Public Backend Group here, administrators will not be able to enter/view the Forum.');
DEFINE('_KUNENA_ADVANCED', 'Zaawansowane');
DEFINE('_KUNENA_CGROUPS1', 'Załącz podrzędne grupy:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Should child groups be allowed access as well? If set to &quot;No &quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_KUNENA_REV', 'Przeglądaj posty:');
DEFINE('_KUNENA_REVDESC',
    'Set to &quot;Yes&quot; if you want posts to be reviewed by Moderators prior to publishing them in this forum. This is useful in a Moderated forum only!<br />If you set this without any Moderators specified, the Site Admin is solely responsible for approving/deleting submitted posts as these will be kept \'on hold\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderacja');
DEFINE('_KUNENA_MODNEWDESC', 'Moderation of the forum and forum moderators');
DEFINE('_KUNENA_MOD', 'Moderowane:');
DEFINE('_KUNENA_MODDESC',
    'Set to &quot;Yes&quot; if you want to be able to assign Moderators to this forum.<br /><strong>Note:</strong> This doesn\'t mean that new post must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>Please do note:</strong> After setting Moderation to &quot;Yes&quot; you must save the forum configuration first before you will be able to add Moderators using the new button.');
DEFINE('_KUNENA_MODHEADER', 'Ustawienia moderacji forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatorzy przypisani do tego forum:');
DEFINE('_KUNENA_NOMODS', 'Brak przypisanych moderatorów');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Edytuj');
DEFINE('_KUNENA_ADD', 'Dodaj');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Przenieś w górę');
DEFINE('_KUNENA_MOVEDOWN', 'Przenieś w dół');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Wszyscy zarejestrowani');
DEFINE('_KUNENA_EVERYBODY', 'Wszyscy');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Reorder');
DEFINE('_KUNENA_CHECKEDOUT', 'Zaznacz');
DEFINE('_KUNENA_ADMINACCESS', 'Dostęp administratora');
DEFINE('_KUNENA_PUBLICACCESS', 'Dostęp publiczny');
DEFINE('_KUNENA_PUBLISHED', 'Opublikowane');
DEFINE('_KUNENA_REVIEW', 'Przeglądaj');
DEFINE('_KUNENA_MODERATED', 'Moderowane');
DEFINE('_KUNENA_LOCKED', 'Zablokowane');
DEFINE('_KUNENA_CATFOR', 'Kategoria / Forum');
DEFINE('_KUNENA_ADMIN', 'Kunena Administration');
DEFINE('_KUNENA_CP', 'Kunena Control Panel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integracja obrazków');
DEFINE('_COM_A_RANKS_SETTINGS', 'Rangi');
DEFINE('_COM_A_RANKING_SETTINGS', 'Ustawienia rankingu');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Ustawienia obrazków');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Ustawienia zabezpieczeń');
DEFINE('_COM_A_BASIC_SETTINGS', 'Ustawienia podstawowe');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Zezwalaj na ulubione');
DEFINE('_COM_A_FAVORITES_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to favorite to a topic ');
DEFINE('_USER_UNFAVORITE_ALL', 'Zaznacz aby <b><u>usunąć z ulubionych</u></b> wszystkie tematy (łącznie z ukrytymi)');
DEFINE('_VIEW_FAVORITETXT', 'Dodaj ten temat do ulubionych ');
DEFINE('_USER_UNFAVORITE_YES', 'Temat usnunięty z ulubionych.');
DEFINE('_POST_FAVORITED_TOPIC', 'Temat dodany do ulubionych.');
DEFINE('_VIEW_UNFAVORITETXT', 'Usuń z ulubionych');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Usuń z subskrypcji');
DEFINE('_USER_NOFAVORITES', 'Brak ulubionych');
DEFINE('_POST_SUCCESS_FAVORITE', 'Twoje żądanie dodania do ulubionych zostało wykonane.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Wyniki wyszukiwania');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Wiadomości na stronie');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Użyć stylu Joomla?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'If you want to use joomla style set to YES. (class: like sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Pokaż obrazek podrzędnej kategorii');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'If you want to show child category small icon  on your forum list, set to YES. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Pokaż ogłoszenia');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Set to "Yes" , if you want to show announcement box on forum homepage.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Pokazać avatar na liście kategorii?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Set to "Yes" , if you want to show user avatar on your forum category list.');
DEFINE('_KUNENA_RECENT_POSTS', 'Ustawienia ostatnich postów');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Pokaż ostatnie posty');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Set to "Yes" if you want to show recent post plugin on your forum');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Ilość ostatnich postów');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Number of Recent Posts');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Ilość na zakładkę ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Number of Post per one tab');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Pokaż kategorie');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Specific category you can show on recent posts. For example:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Pokaż pojedyncze tematy');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Pokaż pojedyncze tematy');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Pokaż tematy odpowiedzi');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Pokaż tematy odpowiedzi (Re:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Długość teatu');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Długość teatu');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Pokaż datę');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Pokaż datę');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Pokaż wyświetlenia');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Pokaż wyświetlenia');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Pokaż autora');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=nazwa użytkownika, 2=imię, 0=nic');
DEFINE('_KUNENA_STATS', 'Ustawienia dodatku statystyk ');
DEFINE('_KUNENA_CATIMAGEPATH', 'ścieżka obrazka kategorii ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'ścieżka obrazka kategorii. jeżeli wybierzesz "category_images/" ścieżką będzie "your_html_rootfolder/images/fbfiles/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'ID moderatorów ogłoszeń ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Podaj ID moderatorów. np 62,63,73 . Mogą oni dodawać, edytować i usuwać ogłoszenia.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Kategorie główne ');
DEFINE('_KUNENA_CHILD_BOARDS', 'Kategroie podrzędne ');
DEFINE('_KUNENA_QUICKMSG', 'Szybka odpowiedź ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Wątki w forum ');
DEFINE('_KUNENA_FORUM', 'Forum ');
DEFINE('_KUNENA_SPOTS', 'Spotlights');
DEFINE('_KUNENA_CANCEL', 'anuluj');
DEFINE('_KUNENA_TOPIC', 'TEMAT: ');
DEFINE('_KUNENA_POWEREDBY', 'Stworzone przez ');
// Time Format
DEFINE('_TIME_TODAY', '<b>Dzisiaj</b> ');
DEFINE('_TIME_YESTERDAY', '<b>Wczoraj</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Ostatnie posty');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Obecni Online');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Główna');
DEFINE('_KUNENA_GUEST', 'Gość');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'przegląda');
DEFINE('_KUNENA_ATTACH', 'Załącznik');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Ulubiony');
DEFINE('_USER_FAVORITES', 'Moje ulubione');
DEFINE('_THREAD_UNFAVORITE', 'Usuń z ulubionych');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Witaj');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Pokaż ostatnie posty');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Ustaw mój obrazek');
DEFINE('_PROFILEBOX_MYPROFILE', 'Mój profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Pokaż moje posty');
DEFINE('_PROFILEBOX_GUEST', 'Gość');
DEFINE('_PROFILEBOX_LOGIN', 'Zaloguj');
DEFINE('_PROFILEBOX_REGISTER', 'Zarejestruj');
DEFINE('_PROFILEBOX_LOGOUT', 'Wyloguj');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Zapomniałeś hasła?');
DEFINE('_PROFILEBOX_PLEASE', 'Proszę');
DEFINE('_PROFILEBOX_OR', 'lub');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Ostatnie posty');
DEFINE('_RECENT_TOPICS', 'Temat');
DEFINE('_RECENT_AUTHOR', 'Autor');
DEFINE('_RECENT_CATEGORIES', 'Kategorie');
DEFINE('_RECENT_DATE', 'Data');
DEFINE('_RECENT_HITS', 'Wyświetlenia');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Ogłoszenia');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Data');
DEFINE('_ANN_TITLE', 'Tytuł');
DEFINE('_ANN_SORTTEXT', 'Krótki tekst');
DEFINE('_ANN_LONGTEXT', 'Długi tekst');
DEFINE('_ANN_ORDER', 'Kolejność');
DEFINE('_ANN_PUBLISH', 'Publikuj');
DEFINE('_ANN_PUBLISHED', 'Opublikowane');
DEFINE('_ANN_UNPUBLISHED', 'Nieopublikowane');
DEFINE('_ANN_EDIT', 'Edytuj');
DEFINE('_ANN_DELETE', 'Usuń');
DEFINE('_ANN_SUCCESS', 'Wykonano');
DEFINE('_ANN_SAVE', 'Zapisz');
DEFINE('_ANN_YES', 'Tak');
DEFINE('_ANN_NO', 'Nie');
DEFINE('_ANN_ADD', 'Dodaj nowy');
DEFINE('_ANN_SUCCESS_EDIT', 'Edytowano');
DEFINE('_ANN_SUCCESS_ADD', 'Dodano');
DEFINE('_ANN_DELETED', 'Usunięto');
DEFINE('_ANN_ERROR', 'BŁąD');
DEFINE('_ANN_READMORE', 'Czytaj więcej...');
DEFINE('_ANN_CPANEL', 'Ustawienia ogłoszeń');
DEFINE('_ANN_SHOWDATE', 'Pokaż datę');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Statystyki forum');
DEFINE('_STAT_GENERAL_STATS', 'Statystyki ogólne');
DEFINE('_STAT_TOTAL_USERS', 'Ilość użytkowników');
DEFINE('_STAT_LATEST_MEMBERS', 'Najnowszy użytkownik');
DEFINE('_STAT_PROFILE_INFO', 'Pokaż informacje o profilu');
DEFINE('_STAT_TOTAL_MESSAGES', 'Ilość wiadomości');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Ilość tematów');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Ilość kategorii');
DEFINE('_STAT_TOTAL_SECTIONS', 'Ilość sekcji');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Dzisiejsze');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Wczorajsze');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Ilość opdowiedzi dzisiaj');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Ilość opdowiedzi wczoraj');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Pokaż ostatnie posty');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Więcej o statystykach');
DEFINE('_STAT_USERLIST', 'Lista użytkowników');
DEFINE('_STAT_TEAMLIST', 'Obsługa forum');
DEFINE('_STATS_FORUM_STATS', 'Statystyki forum');
DEFINE('_STAT_POPULAR', 'Popularnych');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Użytkowników ( Ilość wiadomości) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Wątków ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Użytkowników ( Ilość wyświetleń profilu) ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Jest dostępny');
DEFINE('_MODLIST_OFFLINE', 'Nie jest dostępny');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Kto jest dostępny');
DEFINE('_WHO_ONLINE_NOW', 'Dostępny');
DEFINE('_WHO_ONLINE_MEMBERS', 'Użytkownicy');
DEFINE('_WHO_AND', 'i');
DEFINE('_WHO_ONLINE_GUESTS', 'Gość');
DEFINE('_WHO_ONLINE_USER', 'Użytkownik');
DEFINE('_WHO_ONLINE_TIME', 'Czas');
DEFINE('_WHO_ONLINE_FUNC', 'Akcja');
// Userlist
DEFINE('_USRL_USERLIST', 'Lista użytkowników');
DEFINE('_USRL_REGISTERED_USERS', '%s ma <b>%d</b> zarejestrowanych użytkowników');
DEFINE('_USRL_SEARCH_ALERT', 'Wpisz kogo chcesz szukać!');
DEFINE('_USRL_SEARCH', 'Znadź użytkownika');
DEFINE('_USRL_SEARCH_BUTTON', 'Szukaj');
DEFINE('_USRL_LIST_ALL', 'Pokaż wszystko');
DEFINE('_USRL_NAME', 'Imię');
DEFINE('_USRL_USERNAME', 'Nazwa użytkownika');
DEFINE('_USRL_EMAIL', 'E-mail');
DEFINE('_USRL_USERTYPE', 'Typ użytkownika');
DEFINE('_USRL_JOIN_DATE', 'Data rejestracji');
DEFINE('_USRL_LAST_LOGIN', 'Ostatnie logowanie');
DEFINE('_USRL_NEVER', 'Nigdy');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'Rosnąco');
DEFINE('_USRL_DESC', 'Malejąco');
DEFINE('_USRL_DATE_FORMAT', '%m/%d/%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Szczegóły');
DEFINE('_USRL_COMPROFILER', 'Profil');
DEFINE('_USRL_THUMBNAIL', 'Obrazek');
DEFINE('_USRL_READON', 'pokaż');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Wyślij wiadomość prywatną');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Wyniki dla');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'Ustawienia listy użytkowników');
DEFINE('_USRL_ERROR', 'Błąd');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Komponent wiadomości prywatnych');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Wyszukiwanie dla: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Czy napewno usunąć wiadomość? \n\n NOTE: Usuniętej wiadomości NIE można przywrócić!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Wiadomość(ci) usunięta');
DEFINE('_COM_A_RANKING', 'Ranking');
DEFINE('_COM_A_BOT_REFERENCE', 'Show Bot Reference Chart');
DEFINE('_COM_A_MOSBOT', 'Włącz Discuss Bot');
DEFINE('_PREVIEW', 'podgląd');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'The Discuss Bot enables your users to discuss content items in the forums. The Content Title is used as the topic subject.'
           . '<br />If a topic does not exist yet a new one is created. If the topic already exists, the user is shown the thread and (s)he can reply.' . '<br /><strong>You will need to download and install the Bot separately.</strong>'
           . '<br />check the <a href="http://www.Kunena.com">Kunena Site</a> for more information.' . '<br />When Installed you will need to add the following bot lines to your Content:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the Content Item can be discussed. To find the proper catid, just look into the forums ' . 'and check the category id from the URLs from your browsers status bar.'
           . '<br />Example: if you want the article discussed in Forum with catid 26, the Bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each Content Item to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Szukaj');
DEFINE('_FORUM_SEARCHRESULTS', 'Wyświetlone %s z %s znalezionych wyników.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Regulamin');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Edit this file to insert your rules joomlaroot/administrator/components/com_kunena/language/kunena.english.php</li><li>Rule 2</li><li>Rule 3</li><li>Rule 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Boardcode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Post(y) został zaakceptowany');
DEFINE('_MODERATION_DELETE_ERROR', 'BŁąD: Post(y) nie może zostać usunięty');
DEFINE('_MODERATION_APPROVE_ERROR', 'BŁąD: Post(y) nie może zostać zaakceptowany');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Brak forów w tej kategorii!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Nie udało się utworzyć ukrytego tematu!');
DEFINE('_POST_MOVE_GHOST', 'Pozostaw ukrytą wiadomość');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Skocz do forum');
DEFINE('_COM_A_FORUM_JUMP', 'Włącz skocz do forum');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'If set to &quot;Yes&quot; a selector will be shown on the forum pages that allow for a quick jump to another forum or category.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Regulamin');
DEFINE('_COM_A_RULESPAGE', 'Włącz stronę regulaminu');
DEFINE('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page. This page can be used for things like your forum rules etcetera. You can alter the contents of this file to your liking by opening the rules.php in /joomla_root/components/com_kunena. <em>Make sure you always have a backup of this file as it will be overwritten when upgrading!</em>');
DEFINE('_MOVED_TOPIC', 'PRZENIESIONO:');
DEFINE('_COM_A_PDF', 'Włącz tworzenie PDF');
DEFINE('_COM_A_PDF_DESC',
    'Set to &quot;Yes&quot; if you would like to enable users to download a simple PDF document with the contents of a thread.<br />It is a <u>simple</u> PDF document; no mark up, no fancy layout and such but it does contain all the text from the thread.');
DEFINE('_GEN_PDFA', 'Kliknij aby utworzyć plik PDF (w nowym oknie).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Kliknij aby zobaczyć profil użytkownika');
DEFINE('_VIEW_ADDBUDDY', 'Kliknij aby dodać użytkownika do znajomych');
DEFINE('_POST_SUCCESS_POSTED', 'Twoja wiadomość została opublikowana');
DEFINE('_POST_SUCCESS_VIEW', '[ Wróć do tematu ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Wróć do forum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Administrator');
DEFINE('_RANK_MODERATOR', 'Moderator');
DEFINE('_SHOW_LASTVISIT', 'Od ostatniej wizyty');
DEFINE('_COM_A_BADWORDS_TITLE', 'Cenzura');
DEFINE('_COM_A_BADWORDS', 'Użyj cenzury');
DEFINE('_COM_A_BADWORDS_DESC', 'Set to &quot;Yes&quot; if you want to filter posts containing the words you defined in the Badword Component config. To use this function you must have Badword Component installed!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Ta wiadomość została ocenzurowana *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Create Community Builder forum profile');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Click the link to create necessary Forum fields in Community Builder user profile. After they are created you are free to move them whenever you want using the Community Builder admin, just do not rename their names or options. If you delete them from the Community Builder admin, you can create them again using this link, otherwise do not click on the link multiple times!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Click here <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder user profiles');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Setting to &quot;Yes&quot; will activate the integration with Community Builder component (www.joomlapolis.com). All Kunena user profile functions will be handled by the Community Builder and the profile link in the forums will take you to the Community Builder user profile. This setting will override the Clexus PM profile setting if both are set to &quot;Yes&quot;. Also, make sure you apply the required changes in the Community Builder database by using the option below.');
DEFINE('_COM_A_AVATAR_SRC', 'Użyj obrazka z');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'If you have JomSocial, Clexus PM or Community Builder component installed, you can configure Kunena to use the user avatar picture from JomSocial, Clexus PM or Community Builder user profile. NOTE: For Community Builder you need to have thumbnail option turned on because forum uses thumbnail user pictures, not the originals.');
DEFINE('_COM_A_KARMA', 'Pokaż ocenę');
DEFINE('_COM_A_KARMA_DESC', 'Set to &quot;Yes&quot; to show user Karma and related buttons (increase / decrease) if the User Stats are activated.');
DEFINE('_COM_A_DISEMOTICONS', 'Wyłącz emotikony');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Set to &quot;Yes&quot; to completely disable graphic emoticons (smileys).');
DEFINE('_COM_C_FBCONFIG', 'Konfiguracja Kunena');
DEFINE('_COM_C_FBCONFIGDESC', 'Configure all Kunena\'s functionality');
DEFINE('_COM_C_FORUM', 'Administracja Forum');
DEFINE('_COM_C_FORUMDESC', 'Add categories/forums and configure them');
DEFINE('_COM_C_USER', 'Administracja użytkowników');
DEFINE('_COM_C_USERDESC', 'Basic user and user profile administration');
DEFINE('_COM_C_FILES', 'Przeglądarka plików');
DEFINE('_COM_C_FILESDESC', 'Browse and administer uploaded files');
DEFINE('_COM_C_IMAGES', 'Przeglądarka obrazków');
DEFINE('_COM_C_IMAGESDESC', 'Browse and administer uploaded images');
DEFINE('_COM_C_CSS', 'Edytuj plik CSS');
DEFINE('_COM_C_CSSDESC', 'Tweak Kunena\'s look and feel');
DEFINE('_COM_C_SUPPORT', 'Strona wspracia Kunena');
DEFINE('_COM_C_SUPPORTDESC', 'Connect to the Kunena website (new window)');
DEFINE('_COM_C_PRUNETAB', 'Prune Forums');
DEFINE('_COM_C_PRUNETABDESC', 'Remove old threads (configurable)');
DEFINE('_COM_C_PRUNEUSERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sync Kunena user table with Joomla! user table'); // <=FB 1.0.3
DEFINE('_COM_C_LOADSAMPLE', 'Wczytaj przykładowe dane');
DEFINE('_COM_C_LOADSAMPLEDESC', 'For an easy start: load the Sample Data on an empty Kunena database');
DEFINE('_COM_C_REMOVESAMPLE', 'Usuń  przykładowe dane');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Remove Sample Data from your database');
DEFINE('_COM_C_LOADMODPOS', 'Load Module Positions');
DEFINE('_COM_C_LOADMODPOSDESC', 'Load Module Positions for Kunena Template');
DEFINE('_COM_C_UPGRADEDESC', 'Get your database up to the latest version after an upgrade');
DEFINE('_COM_C_BACK', 'Wróć do panelu głównego');
DEFINE('_SHOW_LAST_SINCE', 'Aktywne tematy od ostatniej wizyty:');
DEFINE('_POST_SUCCESS_REQUEST2', 'Twoje żądanie zostało wykonane');
DEFINE('_POST_NO_PUBACCESS3', 'Kliknij aby się zarejestrować.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Wiadomość pomyślnie usunięta.');
DEFINE('_POST_SUCCESS_EDIT', 'Wiadomość edytowana pomyślnie.');
DEFINE('_POST_SUCCESS_MOVE', 'Temat przeniesiony pomyślnie.');
DEFINE('_POST_SUCCESS_POST', 'Twoja wiadomość zostałą opublikowana.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Twoja subskrypcja została zapisana.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Ocena');
DEFINE('_KARMA_SMITE', 'Zmniejsz');
DEFINE('_KARMA_APPLAUD', 'Zwiększ');
DEFINE('_KARMA_BACK', 'Aby powrócić do tematu,');
DEFINE('_KARMA_WAIT', 'W ciągu 6 godzin możesz zagłosować tylko raz. <br/>Poczekaj aż czas minie i wtedy spróbuj ponownie.');
DEFINE('_KARMA_SELF_DECREASE', 'Nie możesz głosować na siebie!');
DEFINE('_KARMA_SELF_INCREASE', 'Twoja ocena została obniżona z powodu próby oceny samego siebie!');
DEFINE('_KARMA_DECREASED', 'Ocena użytkownika została zmniejszona. Jeżeli nie zostaniesz przeniesiony automatycznie,');
DEFINE('_KARMA_INCREASED', 'Ocena użytkownika została zwiększona. Jeżeli nie zostaniesz przeniesiony automatycznie,');
DEFINE('_COM_A_TEMPLATE', 'Szablon');
DEFINE('_COM_A_TEMPLATE_DESC', 'Wybierz z którego szablonu chcesz korzystać.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Zestaw obrazków');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Wybierz zestaw obrazków z którego chcesz korzystać.');
DEFINE('_PREVIEW_CLOSE', 'Zamknij to okno');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Użyj paska statystyki postów');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Set to &quot;Yes&quot; if you want the number of posts a user has made to be depicted graphically by a Statistics Bar.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Wybierz kolor');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Give the number of the color you want to use for the Post Stats Bar');
DEFINE('_LATEST_REDIRECT',
    'Forum musi przypisać Ci prawa dostępowe zanim stworzy listę ostatnich postów/wpisów dla Ciebie. \nNie przejmuj się tym, to jest zazwyczaj normalne po braku aktywności dłuższej niż 30 minut lub po ponownym zalogowaniu. \nProszę wpisz ponownie wyszukiwaną frazę. ');
DEFINE('_SMILE_COLOUR', 'Kolor');
DEFINE('_SMILE_SIZE', 'Rozmiar');
DEFINE('_COLOUR_DEFAULT', 'Standard');
DEFINE('_COLOUR_RED', 'Czerwony');
DEFINE('_COLOUR_PURPLE', 'Purpurowy');
DEFINE('_COLOUR_BLUE', 'Niebieski');
DEFINE('_COLOUR_GREEN', 'Zielony');
DEFINE('_COLOUR_YELLOW', 'Żółty');
DEFINE('_COLOUR_ORANGE', 'Pomarańczowy');
DEFINE('_COLOUR_DARKBLUE', 'Granatowy');
DEFINE('_COLOUR_BROWN', 'Brązowy');
DEFINE('_COLOUR_GOLD', 'Złoty');
DEFINE('_COLOUR_SILVER', 'Srebrny');
DEFINE('_SIZE_NORMAL', 'Normalny');
DEFINE('_SIZE_SMALL', 'Mały');
DEFINE('_SIZE_VSMALL', 'Bardzo mały');
DEFINE('_SIZE_BIG', 'Duży');
DEFINE('_SIZE_VBIG', 'Bardzo duży');
DEFINE('_IMAGE_SELECT_FILE', 'Wybierz obrazek do załączenia');
DEFINE('_FILE_SELECT_FILE', 'Wybierz plik do załączenia');
DEFINE('_FILE_NOT_UPLOADED', 'Twój plik nie został wysłany. Spróbuj ponownie lub edytuj wiadomość');
DEFINE('_IMAGE_NOT_UPLOADED', 'Twój obrazek nie został wysłany. Spróbuj ponownie lub edytuj wiadomość');
DEFINE('_BBCODE_IMGPH', 'Ustaw kotwicę załączonego obrazu [img]');
DEFINE('_BBCODE_FILEPH', 'Ustaw kotwicę załączonego pliku [file]');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Zaznacz aby <b><u>usunąć z subskrypcji</u></b> wszystkie tematy (łącznie z ukrytymi)');
DEFINE('_LINK_JS_REMOVED', '<em>Aktywny link zawierający JavaScript został usunięty automatycznie</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Wygląd');
DEFINE('_COM_A_USERS', 'User Related');
DEFINE('_COM_A_LENGTHS', 'Różne ustawienia długości');
DEFINE('_COM_A_SUBJECTLENGTH', 'Maksymalna długość subskrypcji');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maximum Subject line length. The maximum number supported by the database is 255 characters. If your site is configured to use multi-byte character sets like Unicode, UTF-8 or non-ISO-8599-x make the maximum smaller using this forumula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Example for UTF-8, for which the max. character bite syze per character is 4 bytes: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Temat/Forum');
DEFINE('_LATEST_NUMBER', 'Nowe posty');
DEFINE('_COM_A_SHOWNEW', 'Pokaż nowe posty');
DEFINE('_COM_A_SHOWNEW_DESC', 'If set to &quot;Yes&quot; Kunena will show the user an indicator for forums that contain new posts and which posts are new since his/her last visit.');
DEFINE('_COM_A_NEWCHAR', '&quot;Nowy&quot; indicator');
DEFINE('_COM_A_NEWCHAR_DESC', 'Define here what should be used to indicate new posts (like an &quot;!&quot; or &quot;New!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Autor ostatniego posta');
DEFINE('_GEN_FORUM_NEWPOST', 'Nowe posty');
DEFINE('_GEN_FORUM_NOTNEW', 'Brak nowych postów');
DEFINE('_GEN_UNREAD', 'Temat nieprzeczytany');
DEFINE('_GEN_NOUNREAD', 'Czytaj');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Zaznacz wszystko jako przeczytane');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Zaznacz to forum jako przeczytane');
DEFINE('_GEN_FORUM_MARKED', 'Wszystkie posty w tym forum zostały zaznaczone jako przeczytane');
DEFINE('_GEN_ALL_MARKED', 'Wszystkie posty zostały zaznaczone jako przeczytane');
DEFINE('_IMAGE_UPLOAD', 'Wyślij obrazek');
DEFINE('_IMAGE_DIMENSIONS', 'Twój obrazek może mieć maksymalnie (szerokość x wysokość - rozmiar)');
DEFINE('_IMAGE_ERROR_TYPE', 'Dozwolone rozszerzenia to jpeg, gif i png');
DEFINE('_IMAGE_ERROR_EMPTY', 'Proszę wybrać plik');
DEFINE('_IMAGE_ERROR_SIZE', 'Wielkość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_IMAGE_ERROR_WIDTH', 'Szerokość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'Wysokość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_IMAGE_UPLOADED', 'Twój obrazek został wysłany.');
DEFINE('_COM_A_IMAGE', 'Obrazki');
DEFINE('_COM_A_IMGHEIGHT', 'Maksymalna wysokość obrazka');
DEFINE('_COM_A_IMGWIDTH', 'Maksymalna szerokość obrazka');
DEFINE('_COM_A_IMGSIZE', 'Maksymalny rozmiar obrazka<br/><em>w Kilobajtach</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Zezwalaj wszystkim na wysyłanie obrazków');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload an image.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Zezwalaj zarejestrowanym użytkownikom na wysyłanie obrazków');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload an image.<br/> Note: (Super)administrators and moderators are always able to upload images.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Wysyłania');
DEFINE('_FILE_TYPES', 'Twój plik może mieć rozszerzenie - maksymalny rozmiar');
DEFINE('_FILE_ERROR_TYPE', 'Możesz wysyłać tylko pliki z rozszerzeniem:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Wybierz plik do wysłania');
DEFINE('_FILE_ERROR_SIZE', 'Rozmiar pliku przekracza maksimum ustalone przez administratora.');
DEFINE('_COM_A_FILE', 'Pliki');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Dozwolone rozszerzenia');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Specify here which file types are allowed for upload. Use comma separated <strong>lowercase</strong> lists without spaces.<br />Example: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Maksymalny rozmiar pliku<br/><em>w Kilobajtach</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Zezwalaj wszystkim na wysyłanie plików');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want everybody (public) to be able to upload a file.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Zezwalaj zarejestrowanym użytkownikom na wysyłanie plików');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload a file.<br/> Note: (Super)administrators and moderators are always able to upload files.');
DEFINE('_SUBMIT_CANCEL', 'Dodawanie postu zostało anulowane');
DEFINE('_HELP_SUBMIT', 'Kliknij aby wysłać wiadomość');
DEFINE('_HELP_PREVIEW', 'Kliknij aby zobaczyć podgląd wiadomości');
DEFINE('_HELP_CANCEL', 'Kliknij aby anulować');
DEFINE('_POST_DELETE_ATT', 'Jeżeli ten kwadrat jest zaznaczony obrazki i załączniki zostaną usunięte razem z usuwaną wiadomością (zalecane).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Pokaż, że post był edytowany');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Set to &quot;Yes&quot; if you want an edited post be marked up with text showing that the post is edited by a user and when it was edited.');
DEFINE('_EDIT_BY', 'Post edytowany przez:');
DEFINE('_EDIT_AT', 'o:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'Wystąpił błąd podczas wysyłania Twojego obrazka. Proszę spróbować ponownie lub skontaktować się z administratorem');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Przeglądarka wysłanych obrazków');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Przeglądarka wysłanych plików');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Ilość wysłanych obrazków');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Ilość wysłanych plików');
DEFINE('_COM_A_IMGB_ENLARGE', 'Kilknij obrazek aby zobaczyć w pełnej wielkości');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Kliknij ikonę pliku aby go pobrać');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'The option &quot;Replace with dummy&quot; will replace the selected image with a dummy image.<br /> This allows you to remove the actual file without breaking the post.<br /><small><em>Please note that sometimes an explicit browser refresh is needed to see the dummy replacement.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Bieżący obrazek tymczasowy');
DEFINE('_COM_A_IMGB_REPLACE', 'Zamień na obrazek tymczasowy');
DEFINE('_COM_A_IMGB_REMOVE', 'Usuń całkowicie');
DEFINE('_COM_A_IMGB_NAME', 'Nazwa');
DEFINE('_COM_A_IMGB_SIZE', 'Rozmiar');
DEFINE('_COM_A_IMGB_DIMS', 'Wymiary');
DEFINE('_COM_A_IMGB_CONFIRM', 'Czy napewno usunąć ten plik? \n Usunięcie pliku może spowodować wadliwe działanie aplikacji..');
DEFINE('_COM_A_IMGB_VIEW', 'Otwórz post (do edycji)');
DEFINE('_COM_A_IMGB_NO_POST', 'No referencing post!');
DEFINE('_USER_CHANGE_VIEW', 'Zmiany zostaną zaktualizowane przy nastepnej wizycie.<br /> Jeżeli chcesz zmienić typ podglądu &quot;Mid-Flight&quot; możesz użyć opcji znajdujących się w pasku Menu.');
DEFINE('_MOSBOT_DISCUSS_A', 'Skomentuj ten artykuł na forum. (');
DEFINE('_MOSBOT_DISCUSS_B', ' posty)');
DEFINE('_POST_DISCUSS', 'Ten wątek dotyczy treści artykułu');
DEFINE('_COM_A_RSS', 'Włącz RSS');
DEFINE('_COM_A_RSS_DESC', 'The RSS feed enables users to download the latest posts to their desktop/RSS Reader aplication (see <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.');
DEFINE('_LISTCAT_RSS', 'otrzymuj najnowsze posty wprost na pulpit');
DEFINE('_SEARCH_REDIRECT', 'Forum musi przypisać Ci prawa dostępowe zanim stworzy listę ostatnich postów/wpisów dla Ciebie. \nNie przejmuj się tym, to jest zazwyczaj normalne po braku aktywności dłuższej niż 30 minut lub po ponownym zalogowaniu. \nProszę wpisz ponownie wyszukiwaną frazę.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Konfiguracja Kunena');
DEFINE('_COM_A_DISPLAY', 'Pokaż #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Bieżące ustawienie');
DEFINE('_COM_A_EXPLANATION', 'Wyjaśnienie');
DEFINE('_COM_A_BOARD_TITLE', 'Nazwa forum');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Nazwa Twojego forum');
DEFINE('_COM_A_VIEW_TYPE', 'Domyślny typ widoku');
DEFINE('_COM_A_VIEW_TYPE_DESC', 'Wybierz pomiędzy wątkowym a płaskim');
DEFINE('_COM_A_THREADS', 'Wątków na stronę');
DEFINE('_COM_A_THREADS_DESC', 'Ilość wątków do pokazania na jednej stronie');
DEFINE('_COM_A_REGISTERED_ONLY', 'Tylko zarejestrowani');
DEFINE('_COM_A_REG_ONLY_DESC', 'Set to &quot;Yes&quot; to allow only registered users to use the Forum (view & post), Set to &quot;No&quot; to allow any visitor to use the Forum');
DEFINE('_COM_A_PUBWRITE', 'Publiczne Pisanie/Czytanie');
DEFINE('_COM_A_PUBWRITE_DESC', 'Set to &quot;Yes&quot; to allow for public write privileges, Set to &quot;No&quot; to allow any visitor to see posts, but only registered users to write posts');
DEFINE('_COM_A_USER_EDIT', 'Edycja przez użytkowników');
DEFINE('_COM_A_USER_EDIT_DESC', 'Set to &quot;Yes&quot; to allow registered Users to edit their own posts.');
DEFINE('_COM_A_MESSAGE', 'In order to save changes of the values above, press the &quot;Save&quot; button at the top.');
DEFINE('_COM_A_HISTORY', 'Pokaż historie');
DEFINE('_COM_A_HISTORY_DESC', 'Set to &quot;Yes&quot; if you want the topic history shown when a reply/quote is made');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Pozwól na subskrypcje');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Set to &quot;Yes&quot; if you want to allow registered users to subscribe to a topic and recieve email notifications on new posts');
DEFINE('_COM_A_HISTLIM', 'Limit historii');
DEFINE('_COM_A_HISTLIM_DESC', 'Number of posts to show in the history');
DEFINE('_COM_A_FLOOD', 'Zabezpieczenie przed powodzią');
DEFINE('_COM_A_FLOOD_DESC', 'The amount of seconds a user has to wait between two consecutive post. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection <em>can</em> cause degradation of performance..');
DEFINE('_COM_A_MODERATION', 'Email Moderators');
DEFINE('_COM_A_MODERATION_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on new posts sent to the forum moderator(s). Note: although every (super)administrator has automatically all Moderator privileges assign them explicitly as moderators on
 the forum to recieve emails too!');
DEFINE('_COM_A_SHOWMAIL', 'Pokaż Email');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Set to &quot;No&quot; if you never want to display the users email address; not even to registered users.');
DEFINE('_COM_A_AVATAR', 'Zezwalaj na avatary');
DEFINE('_COM_A_AVATAR_DESC', 'Set to &quot;Yes&quot; if you want registered users to have an avatar (manageable trough their profile)');
DEFINE('_COM_A_AVHEIGHT', 'Max. Avatar wysokość');
DEFINE('_COM_A_AVWIDTH', 'Max. Avatar szerokość');
DEFINE('_COM_A_AVSIZE', 'Max. Avatar rozmiar<br/><em>in Kilobytes</em>');
DEFINE('_COM_A_USERSTATS', 'Pokaż statystyki użytkowników');
DEFINE('_COM_A_USERSTATS_DESC', 'Set to &quot;Yes&quot; to show User Statistics like number of posts user made, User type (Admin, Moderator, User, etc.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Pozwól na wysyłanie avatarów');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to upload an avatar.');
DEFINE('_COM_A_AVATARGALLERY', 'Użyj galerii avatarów');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to choose an avatar from a Gallery you provide (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Set to &quot;Yes&quot; if you want to show the rank registered users have based on the number of posts they made.<br/><strong>Note that you must enable User Stats in the Advanced tab too to show it.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Użyj obrazków rang');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Set to &quot;Yes&quot; if you want to show the rank registered users have with an image (from components/com_kunena/ranks). Turning this of will show the text for that rank. Check the documentation on www.kunena.com for more information on ranking images');

//email and stuff
$_COM_A_NOTIFICATION = "Powiadomienie o nowym poście ";
$_COM_A_NOTIFICATION1 = "W subskrybowanym przez Ciebie temacie pojawił się nowy post ";
$_COM_A_NOTIFICATION2 = "Możesz zarządzać swoim profilem przez wejście na 'Mój profil' na stronie głównej forum po uprzednim zalogowaniu się. Ze swojego profilu możesz również wypisać się z danego tematu.";
$_COM_A_NOTIFICATION3 = "Ta wiadomość została wygenerowana automatycznie. Prosimy na nią nie odpowiadać.";
$_COM_A_NOT_MOD1 = "A new post has been made to a forum to which you have assigned as moderator on the ";
$_COM_A_NOT_MOD2 = "Please have a look at it after you have logged in on the site.";
DEFINE('_COM_A_NO', 'Nie');
DEFINE('_COM_A_YES', 'Tak');
DEFINE('_COM_A_FLAT', 'Płaski');
DEFINE('_COM_A_THREADED', 'Wątkowy');
DEFINE('_COM_A_MESSAGES', 'Wiadomości na stronę');
DEFINE('_COM_A_MESSAGES_DESC', 'Ilość wiadomośći do pokazania na stronie');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Nazwa użytkownika');
DEFINE('_COM_A_USERNAME_DESC', 'Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the users real name');
DEFINE('_COM_A_CHANGENAME', 'Pozwól na zmianę imienia');
DEFINE('_COM_A_CHANGENAME_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to change their name when posting. If set to &quot;No&quot; then registered users will not be able to edit his/her name');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum Offline');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Set to &quot;Yes&quot; if you want to take the Forum section offline. The forum will remain browsable by site (super)admins.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Forum Offline Message');
DEFINE('_COM_A_PRUNE', 'Prune Forums');
DEFINE('_COM_A_PRUNE_NAME', 'Forum to prune:');
DEFINE('_COM_A_PRUNE_DESC',
    'The Prune Forums function allows you to prune threads to which there have not been any new posts for the specified number of days. This does not remove topics with the sticky bit set or which are explicitly locked; these must be removed manually. Threads in locked forums can not be pruned.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Prune threads with no posts for the past ');
DEFINE('_COM_A_PRUNE_DAYS', 'dni');
DEFINE('_COM_A_PRUNE_USERS', 'Prune Users'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'This function allows you to prune your Kunena user list against the Joomla! Site user list. It will delete all profiles for Kunena Users that have been deleted from your Joomla! Framework.<br/>When you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Akcja');
DEFINE('_GEN_AUTHOR', 'Autor');
DEFINE('_GEN_BY', 'przez');
DEFINE('_GEN_CANCEL', 'Anuluj');
DEFINE('_GEN_CONTINUE', 'Prześlij');
DEFINE('_GEN_DATE', 'Data');
DEFINE('_GEN_DELETE', 'Usuń');
DEFINE('_GEN_EDIT', 'Edytuj');
DEFINE('_GEN_EMAIL', 'Email');
DEFINE('_GEN_EMOTICONS', 'Emotikony');
DEFINE('_GEN_FLAT', 'Płaski');
DEFINE('_GEN_FLAT_VIEW', 'Płaski');
DEFINE('_GEN_FORUMLIST', 'Lista forów');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Pomoc');
DEFINE('_GEN_HITS', 'Wyświetleń');
DEFINE('_GEN_LAST_POST', 'Ostatni post');
DEFINE('_GEN_LATEST_POSTS', 'Pokaż ostatnie posty');
DEFINE('_GEN_LOCK', 'Zablokuj');
DEFINE('_GEN_UNLOCK', 'Odblokuj');
DEFINE('_GEN_LOCKED_FORUM', 'Forum zablokowane');
DEFINE('_GEN_LOCKED_TOPIC', 'Temat zablokowany');
DEFINE('_GEN_MESSAGE', 'Wiadomość');
DEFINE('_GEN_MODERATED', 'Forum jest moderowane; Wiadomości podlegają wcześniejszej weryfikacji.');
DEFINE('_GEN_MODERATORS', 'Moderatorzy');
DEFINE('_GEN_MOVE', 'Przeniś');
DEFINE('_GEN_NAME', 'Imię');
DEFINE('_GEN_POST_NEW_TOPIC', 'Napisz nowy temat');
DEFINE('_GEN_POST_REPLY', 'Napisz odpowiedź');
DEFINE('_GEN_MYPROFILE', 'Mój profil');
DEFINE('_GEN_QUOTE', 'Cytuj');
DEFINE('_GEN_REPLY', 'Odpowiedź');
DEFINE('_GEN_REPLIES', 'Odpowiedzi');
DEFINE('_GEN_THREADED', 'Wątkowy');
DEFINE('_GEN_THREADED_VIEW', 'Wątkowy');
DEFINE('_GEN_SIGNATURE', 'Podpis');
DEFINE('_GEN_ISSTICKY', 'Topic is sticky.');
DEFINE('_GEN_STICKY', 'Sticky');
DEFINE('_GEN_UNSTICKY', 'Unsticky');
DEFINE('_GEN_SUBJECT', 'Temat');
DEFINE('_GEN_SUBMIT', 'Prześlij');
DEFINE('_GEN_TOPIC', 'Temat');
DEFINE('_GEN_TOPICS', 'Tematy');
DEFINE('_GEN_TOPIC_ICON', 'ikona tematu');
DEFINE('_GEN_SEARCH_BOX', 'Przeszukaj forum');
$_GEN_THREADED_VIEW = "Wątkowo";
$_GEN_FLAT_VIEW = "Płaski";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Wyślij');
DEFINE('_UPLOAD_DIMENSIONS', 'Wielkość obrazka nie może przekarczać (szerokość x wysokość - rozmiar)');
DEFINE('_UPLOAD_SUBMIT', 'Dodaj nowy obrazek do wysłania');
DEFINE('_UPLOAD_SELECT_FILE', 'Wybierz plik');
DEFINE('_UPLOAD_ERROR_TYPE', 'Dozwolone typy plików: jpeg, gif i png');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Proszę wybrać plik do wysłania');
DEFINE('_UPLOAD_ERROR_NAME', 'Obrazek musi zawierać tylko znaki alfanumeryczne i nie może zawierać spacji.');
DEFINE('_UPLOAD_ERROR_SIZE', 'Wielkość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'Szerokość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Wysokość obrazka przekracza maksimum ustalone przez administratora.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "Nie wybrałeś obrazka z galerii...");
DEFINE('_UPLOAD_UPLOADED', 'Twój obrazek został wysłany.');
DEFINE('_UPLOAD_GALLERY', 'Wybierz z galerii obrazków:');
DEFINE('_UPLOAD_CHOOSE', 'Potwierdź wybór.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Administrator powinien stworzyć je/ich najpierw od ');
DEFINE('_LISTCAT_DO', 'Będą wiedzieć co robić ');
DEFINE('_LISTCAT_INFORM', 'Poinformuj ich i powiedz, żeby się pospieszyli!');
DEFINE('_LISTCAT_NO_CATS', 'Na forum nie ma jeszcze zdefinowanych kategorii.');
DEFINE('_LISTCAT_PANEL', 'Panel Administracyjny Joomla1 OS CMS.');
DEFINE('_LISTCAT_PENDING', 'pending message(s)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'There are no pending messages in this forum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Czy chcesz usunąć wiadomość');
DEFINE('_POST_ABOUT_DELETE', '<strong>UWAGA:</strong><br/>
-jeżeli usuniesz Temat Forum (pierwszy wpis w wątku), wszystkie pozostałe wpisy również zostaną usunięte!
..rozważ wyczyszczenie postu i nazw użytkowników, jeżeli tylko treść ma być usunięta..
<br/>
- Wszystkie następne wpisy pod usuniętym wątkiem zostaną przeniesione o jeden poziom wyżej w chierarchii wątku.');
DEFINE('_POST_CLICK', 'kliknij tutaj');
DEFINE('_POST_ERROR', 'Nie można odnaleźć nazwy użytkownika/adresu email. Nieokreślony poważny błąd bazy danych');
DEFINE('_POST_ERROR_MESSAGE', 'Wystąpił nieznany błąd SQL i Twoja wiadomość nie została zapisana.  Jeżeli problem będzie się powtarzał skontaktuj się z administratorem.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Wystąpił błąd i wiadomość nie została uaktualniona.  Proszę spróbować ponownie.  Jeżeli błąd będzie się powtarzał skontaktuj się z administratorem.');
DEFINE('_POST_ERROR_TOPIC', 'Wystąpił błąd podczas usuwania. Proszę sprawdzić szczeguły poniżej:');
DEFINE('_POST_FORGOT_NAME', 'Zapomniałeś wpisać imię.  Naciśnij przycisk wstecz Twojej przeglądarki by powrócić i spróbować ponownie.');
DEFINE('_POST_FORGOT_SUBJECT', 'Zapomniałeś wpisać temat.  Naciśnij przycisk wstecz Twojej przeglądarki by powrócić i spróbować ponownie.');
DEFINE('_POST_FORGOT_MESSAGE', 'Zapomniałeś wpisać wiadomość.  Naciśnij przycisk wstecz Twojej przeglądarki by powrócić i spróbować ponownie.');
DEFINE('_POST_INVALID', 'Zarządano nieprawidłowego ID wiadmości');
DEFINE('_POST_LOCK_SET', 'Temat został zablokowany.');
DEFINE('_POST_LOCK_NOT_SET', 'Temat nie może zostać zablokowany.');
DEFINE('_POST_LOCK_UNSET', 'Temat został odblokowany.');
DEFINE('_POST_LOCK_NOT_UNSET', 'Temat nie może zostać odblokowany.');
DEFINE('_POST_MESSAGE', 'Napisz nową wiadomość w ');
DEFINE('_POST_MOVE_TOPIC', 'Przenieś ten temat do forum ');
DEFINE('_POST_NEW', 'Napisz nową wiadomość w: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Subskrypcja do tego tematu nie mogła zostać zrealizowana.');
DEFINE('_POST_NOTIFIED', 'Zaznacz ten kwadrat jeżeli chcesz być powiadomiony o odpowiedziach.');
DEFINE('_POST_STICKY_SET', 'The sticky bit has been set for this topic.');
DEFINE('_POST_STICKY_NOT_SET', 'The sticky bit could not be set for this topic.');
DEFINE('_POST_STICKY_UNSET', 'The sticky bit has been unset for this topic.');
DEFINE('_POST_STICKY_NOT_UNSET', 'The sticky bit could not be unset for this topic.');
DEFINE('_POST_SUBSCRIBE', 'susbkrybuj');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Dodałeś ten temat do subskrypcji.');
DEFINE('_POST_SUCCESS', 'Twoja wiadomość została pomyślnie');
DEFINE('_POST_SUCCES_REVIEW', 'Twoja wiadomość została pomyślnie zapisana.  Po sprawdzeniu przez moderatora pojowi się na forum.');
DEFINE('_POST_SUCCESS_REQUEST', 'Twoje żądanie zostało wykonane.  Jeżeli nie zostaniesz przeniesiony automatycznie,');
DEFINE('_POST_TOPIC_HISTORY', 'Topic History of');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Max. showing the last');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(Last post first)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Temat nie może zostać przeniesiony. By powrócić do tematu:');
DEFINE('_POST_TOPIC_FLOOD1', 'Forum wymaga odczekania ');
DEFINE('_POST_TOPIC_FLOOD2', ' sekund zanim będziesz mógł znów napisać posta.');
DEFINE('_POST_TOPIC_FLOOD3', 'Naciśnij przycisk wstecz Twojej przeglądarki by powrócić do forum.');
DEFINE('_POST_EMAIL_NEVER', 'Twój adres email nigdy nie będzie widoczny na stronie.');
DEFINE('_POST_EMAIL_REGISTERED', 'Twój adres email będzie dostępny tylko dla zarejestrowanych użytkowników.');
DEFINE('_POST_LOCKED', 'zablokowane przez administratora.');
DEFINE('_POST_NO_NEW', 'Nowe odpowiedzi nie dozwolone.');
DEFINE('_POST_NO_PUBACCESS1', 'Niezarejestrowani użytkownicy nie mogą pisać na forum.');
DEFINE('_POST_NO_PUBACCESS2', 'Tylko zalogowani / zarejestrowani użytkownicy<br /> mogą pisać na forum.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Na tym forum niema jeszcze tematów <<');
DEFINE('_SHOWCAT_PENDING', 'pending message(s)');
// userprofile.php
DEFINE('_USER_DELETE', ' zaznacz ten kwadrat aby usunąć swój podpis');
DEFINE('_USER_ERROR_A', 'Trafiłeś na tą stronę z powodu błędu. Proszę powiadomić administratora który link ');
DEFINE('_USER_ERROR_B', 'spowodował przeniesienie na tą stronę.');
DEFINE('_USER_ERROR_C', 'Dziękujemy!');
DEFINE('_USER_ERROR_D', 'Numer błędu do załączenia w raporcie: ');
DEFINE('_USER_GENERAL', 'Ogólne ustawienia profilu');
DEFINE('_USER_MODERATOR', 'You are assigned as a Moderator to forums');
DEFINE('_USER_MODERATOR_NONE', 'Żadne forum nie jest przypisane do Ciebie');
DEFINE('_USER_MODERATOR_ADMIN', 'Administratorzy są moderatorami na wszystkich forach.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Brak subskrypcji dla Ciebie');
DEFINE('_USER_PREFERED', 'Preferowany typ widoku');
DEFINE('_USER_PROFILE', 'Profil dla ');
DEFINE('_USER_PROFILE_NOT_A', 'Twój profil ');
DEFINE('_USER_PROFILE_NOT_B', 'nie może');
DEFINE('_USER_PROFILE_NOT_C', ' zostać uaktualniony.');
DEFINE('_USER_PROFILE_UPDATED', 'Twój profil został uaktualniony.');
DEFINE('_USER_RETURN_A', 'Jeżeli nie zostaniesz przeniesiony automatycznie ');
DEFINE('_USER_RETURN_B', 'kliknij tutaj');
DEFINE('_USER_SUBSCRIPTIONS', 'Twoje subskrypcje');
DEFINE('_USER_UNSUBSCRIBE', 'Usuń z subskrypcji');
DEFINE('_USER_UNSUBSCRIBE_A', 'Nie ');
DEFINE('_USER_UNSUBSCRIBE_B', 'możesz');
DEFINE('_USER_UNSUBSCRIBE_C', ' usunąć tego tematu z subskrypcji.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Temat usunięty z subskrypcji.');
DEFINE('_USER_DELETEAV', ' zaznacz ten kwadrat aby usunąć swój obrazek');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Preferowane sortowanie wiadomości');
DEFINE('_USER_ORDER_DESC', 'Najnowszy post pierwszy');
DEFINE('_USER_ORDER_ASC', 'Najstarszy post pierwszy');
// view.php
DEFINE('_VIEW_DISABLED', 'Administrator zablokował publiczny dostęp do forum.');
DEFINE('_VIEW_POSTED', 'Napisane przez');
DEFINE('_VIEW_SUBSCRIBE', ':: Subskrybuj ten temat ::');
DEFINE('_MODERATION_INVALID_ID', 'Zażądano nieprawidłowe ID postu.');
DEFINE('_VIEW_NO_POSTS', 'Brak postów na tym forum.');
DEFINE('_VIEW_VISITOR', 'Odwiedzający');
DEFINE('_VIEW_ADMIN', 'Administrator');
DEFINE('_VIEW_USER', 'Użytkownik');
DEFINE('_VIEW_MODERATOR', 'Moderator');
DEFINE('_VIEW_REPLY', 'Odpowiedz na tą wiadomość');
DEFINE('_VIEW_EDIT', 'Edytuj tą wiadomość');
DEFINE('_VIEW_QUOTE', 'Cytuj tą wiadomość w nowym poście');
DEFINE('_VIEW_DELETE', 'Usuń tą wiadomość');
DEFINE('_VIEW_STICKY', 'Set this topic sticky');
DEFINE('_VIEW_UNSTICKY', 'Unset this topic sticky');
DEFINE('_VIEW_LOCK', 'Zablokuj ten temat');
DEFINE('_VIEW_UNLOCK', 'Odblokuj ten temat');
DEFINE('_VIEW_MOVE', 'Przenieś ten temat do innego forum');
DEFINE('_VIEW_SUBSCRIBETXT', 'Subskrybuj ten temat i otrzymuj powiadomienia email o nowych postach');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Forum');
DEFINE('_POSTS', 'Postów:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Post');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forum jest wyłączone!');
DEFINE('_PAGE', 'Strona: ');
DEFINE('_NO_POSTS', 'Brak postów');
DEFINE('_CHARS', 'Maksymalna ilość znaków.');
DEFINE('_HTML_YES', 'HTML jest wyłączone');
DEFINE('_YOUR_AVATAR', '<b>Twój obrazek</b>');
DEFINE('_NON_SELECTED', 'Jeszcze nie wybrano <br />');
DEFINE('_SET_NEW_AVATAR', 'Wybierz nowy obrazek');
DEFINE('_THREAD_UNSUBSCRIBE', 'Usuń z subskrypcji');
DEFINE('_SHOW_LAST_POSTS', 'Aktywne tematy z');
DEFINE('_SHOW_HOURS', 'godzin');
DEFINE('_SHOW_POSTS', 'Razem: ');
DEFINE('_DESCRIPTION_POSTS', 'Wyświetlane najnowsze posty z aktywnych tematów');
DEFINE('_SHOW_4_HOURS', '4 godzin');
DEFINE('_SHOW_8_HOURS', '8 godzin');
DEFINE('_SHOW_12_HOURS', '12 godzin');
DEFINE('_SHOW_24_HOURS', '24 godzin');
DEFINE('_SHOW_48_HOURS', '48 godzin');
DEFINE('_SHOW_WEEK', 'Week');
DEFINE('_POSTED_AT', 'Napisane');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Brak postów w wybranym przez Ciebie zakresie.');
DEFINE('_MESSAGE', 'Wiadomość');
DEFINE('_NO_SMILIE', 'nie');
DEFINE('_FORUM_UNAUTHORIZIED', 'To forum jest dostępne tylko dla zarejestrowanych i zalogowanych użytkowników.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Jeżeli jesteś zarejestrowany proszę się zalogować.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderacja');
DEFINE('_MOD_APPROVE', 'Akceptuj');
DEFINE('_MOD_DELETE', 'Usuń');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Pokaż najnowsze posty');
DEFINE('_POST_WROTE', 'napisał');
DEFINE('_COM_A_EMAIL', 'Adres email forum');
DEFINE('_COM_A_EMAIL_DESC', 'This is the Boards email address. Make this a valid email address');
DEFINE('_COM_A_WRAP', 'Zawijaj wyrazy dłuższe niż');
DEFINE('_COM_A_WRAP_DESC',
    'Enter the maximum number of characters a single word may have. This feature allows you to tune the output of Kunena Posts to your template.<br/> 70 characters is probably the maximum for fixed width templates but you might need to experiment a bit.<br/>URLs, no matter how long, are not affected by the wordwrap');
DEFINE('_COM_A_SIGNATURE', 'Maksymalna długość podpisu');
DEFINE('_COM_A_SIGNATURE_DESC', 'Maximum number of characters allowed for the user signature.');
DEFINE('_SHOWCAT_NOPENDING', 'No pending message(s)');
DEFINE('_COM_A_BOARD_OFSET', 'Przesunięcie czau');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Some boards are located on servers in a different timezone than the users are. Set the Time Offset Kunena must use in the post time in hours. Positive and negative numbers can be used');
//New in RC2
DEFINE('_COM_A_BASICS', 'Podstawy');
DEFINE('_COM_A_FRONTEND', 'Frontend');
DEFINE('_COM_A_SECURITY', 'Bezpieczeństwo');
DEFINE('_COM_A_AVATARS', 'Avatary');
DEFINE('_COM_A_INTEGRATION', 'Integracja');
DEFINE('_COM_A_PMS', 'Włącz wiadomości prywatne');
DEFINE('_COM_A_PMS_DESC',
    'Select the appropriate private messaging component if you have installed any. Selecting Clexus PM will also enable ClexusPM user profile related options (like ICQ, AIM, Yahoo, MSN and profile links if supported by the Kunena template used');
DEFINE('_VIEW_PMS', 'Click here to send a Private Message to this user');
//new in RC3
DEFINE('_POST_RE', 'Odpowiedź:');
DEFINE('_BBCODE_BOLD', 'Pogrubienie: [b]tekst[/b] ');
DEFINE('_BBCODE_ITALIC', 'Pochylenie: [i]tekst[/i]');
DEFINE('_BBCODE_UNDERL', 'Podkreślenie: [u]tekst[/u]');
DEFINE('_BBCODE_QUOTE', 'Cytowanie: [quote]tekst[/quote]');
DEFINE('_BBCODE_CODE', 'Kod: [code]kod[/code]');
DEFINE('_BBCODE_ULIST', 'Lista nieuporządkowana: [ul] [li]tekst[/li] [/ul] - Podpowiedź: lista musi zawierać elementy listy');
DEFINE('_BBCODE_OLIST', 'Lista uporządkowana: [ol] [li]tekst[/li] [/ol] - Podpowiedź: lista musi zawierać elementy listy');
DEFINE('_BBCODE_IMAGE', 'Obrazek: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]To jest link[/url]');
DEFINE('_BBCODE_CLOSA', 'Zamknij wszystkie tagi');
DEFINE('_BBCODE_CLOSE', 'Zamknij wszystkie otwarte tagi bbCode');
DEFINE('_BBCODE_COLOR', 'Kolor: [color=#FF6600]tekst[/color]');
DEFINE('_BBCODE_SIZE', 'Rozmiar: [size=1]tekst[/size] - Podpowiedź: rozmiar w zakresie od 1 do 5');
DEFINE('_BBCODE_LITEM', 'Element listy: [li] element listy [/li]');
DEFINE('_BBCODE_HINT', 'Pomoc bbCode - Podpowiedź: bbCode można używać na zaznaczonym tekscie!');
DEFINE('_COM_A_TAWIDTH', 'Szerokość pola tekstowego');
DEFINE('_COM_A_TAWIDTH_DESC', 'Adjust the width of the reply/post text entry area to match your template. <br/>The Topic Emoticon Toolbar will be wrapped accross two lines if width <= 420 pixels');
DEFINE('_COM_A_TAHEIGHT', 'Wysokość pola tekstowego');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Adjust the height of the reply/post text entry area to match your template');
DEFINE('_COM_A_ASK_EMAIL', 'Wymagaj adresu email');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Require an email address when users or visitors make a post. Set to &quot;No&quot; if you want this feature to be skipped on the frontend. Posters will not be asked for their email address.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Zarządzanie rangami');
define('_KUNENA_SORTRANKS', 'Sortuj według rangi');

define('_KUNENA_RANKSIMAGE', 'Obrazek rangi');
define('_KUNENA_RANKS', 'Nawa rangi');
define('_KUNENA_RANKS_SPECIAL', 'Specjalny');
define('_KUNENA_RANKSMIN', 'Minimalna ilość postów');
define('_KUNENA_RANKS_ACTION', 'Akcje');
define('_KUNENA_NEW_RANK', 'Nawa ranga');

?>
