<?php
/**
* @version $Id: kunena.english.php 449 2009-02-17 10:55:21Z fxstein $
* Kunena Component
* @package Kunena
*
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
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'Du har inte tillräckliga rättigheter!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Denna tråd <b>NOT</b> har lagts till dina favoriter');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Synkronisera Kunena användartabell med Joomla användartabell');
DEFINE('_POST_FORGOT_EMAIL', 'Du glömde att inkludera din e-mail address. Klicka på din browser&#146s tillbakaknapp för att gå tillbaka och försöka igen.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Allt raderades, några av de bifogade filerna saknades!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Huvudforum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Detta huvudforumets kategori. Som "root kategori" fungerar den som container för individuella boards och forums. Den benäms också som "root kategori" och är ett måste vid varje upprättande av ett Kunena Forum.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'För att tillgodose ytterliggare information för dina gäster och medlemmar, kan forum headern visa text över varje enskild kategori.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Välkommen Mat');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Vi uppmuntrar alla nya medlemmar att posta en kort beskrivning av sig själva i denna kategorin. Lära känna varandra och dela gemensamma intressen.<br>');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '<strong>Välkommen till Kunena forum!</strong><br><br>Berätta för oss och våra medlemmar vem du är , vad du gillar och varför du blev medlem på sidan.<br>Vi välkommnar alla nya medlemmar och hoppas se mycket av dig!<br>');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'SFörslagslåda');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Har du någon tankar eller feedback som du vill dela med dig av?<br>Var inte blyg, hör av dig. Vi vill höra från dig så att vi kan sträva att få en bättre sida och som är ännu mer användarvänlig.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Detta är den frivilliga Forum headern för Förslagslådan.<br>');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Välkommen till Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Välkommen till Kunena![/b][/size]

Thank för att du valt Kunena forum till din Joomla sida.

Kunena, översatt från Swahili betyder "att prata", är byggt av ett professionellt open source team med målet att tillgodose ett forum i toppkvalité, nära anpassat Joomla. Kunena har supports för social networking komponenter som Community Builder och JomSocial.


[size=4][b]Övriga Kunena resurser[/b][/size]

[b]Kunena Dokumentation:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Support Forum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena Nedladdningar:[/b] http://www.kunena.com/downloads
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blogg:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Skicka in önskningar om framtida funktioner:[/b] http://www.kunena.com/uservoice
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Följ Kunena på Twitter:[/b] http://www.kunena.com/twitter
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Tillåt markerad kod');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Tillåter Kunena tagga markerad Javacriptkod. Om dina medlemmar postar PHP eller annat fragment av kod, kommer dennna att bli markerad. Om ditt forum inte använder postning av programmeringsspråk, bör du stänga av koden för att inte bli malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Default RSS typ');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Välj mellan RSS nyhetsflöden &quot;Från Tråd &quot; or &quot;Från Inlägg.&quot; &quot;Från Tråd &quot; means that only one entry per thread will be listed in the RSS feed independent of how many posts have been made within that thread. &quot;By Thread&quot; creates a shorter, more compact RSS feed but will not list every reply.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Från Tråd');
DEFINE('_COM_A_RSS_BY_POST', 'Från Inlägg');
DEFINE('_COM_A_RSS_HISTORY', 'RSS Historik');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Välj hur mycket historik som ska inkluderas i RSS flödet. Default är en månad, men det är rekomenderat att sänkas om det är stor trafik på sidan.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Vecka');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Månad');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 År');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Default Kunena Sida');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default Kunena page that is displayed when a forum link is clicked or the forum is initially entered. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Senaste diskussioner');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Mina diskussioner');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_BBCODE_HIDE', 'Följande är dolt för oregistrerade användare:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', '"Parent Forum" får inte vara samma.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', '"Parent Forum" är ett av sina egna.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID existerar inte.');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Du glömde skriva ditt namn.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Du glömde skriva din e-mail.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Du glömde skriva ett ämne.');
DEFINE('_KUNENA_EDIT_TITLE', 'Ändra dina detaljer');
DEFINE('_KUNENA_YOUR_NAME', 'Ditt namn:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'Användarnamn:');
DEFINE('_KUNENA_PASS', 'Lösenord:');
DEFINE('_KUNENA_VPASS', 'Verifiera lösenord:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Användardetaljer har sparats.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Visa spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trim long URLs');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Set to &quot;Yes&quot; if you want long URLs to be trimmed. See URL trim front and back settings.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Front portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Number of characters for front portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Back portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Auto embed YouTube videos');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Set to &quot;Yes&quot; if you want youtube video urls to get automatically embedded.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Auto embed eBay items');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay widget language code');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Session Lifetime');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and the NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Förena');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Förena denna tråd med');
DEFINE('_POST_MERGE_GHOST', 'Lämna "spökkopia" av tråd');
DEFINE('_POST_SUCCESS_MERGE', 'Tråden förenades.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Föreningen av två trådar misslyckades.');
DEFINE('_GEN_SPLIT', 'Splitt');
DEFINE('_GEN_DOSPLIT', 'Gå');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Tråden splittades.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Rubriken ändrades.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Ändring av rubrik misslyckades.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Splitt misslyckades.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicera. Identiskt meddelande ignorerades.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'merge');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Denna tråd har tagits bort från dina favoriter.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Denna tråd har <b>inte</b> tagits bort från dina favoriter');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Din begäran att ta bort från favoriter har behandlats.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Denna tråd har tagits bort från dina prenumerationer.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Denna tråd har <b>inte</b> tagits bort från dina prenumerationer.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Din önskan att avbryta prenumerationen har behandlats.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Ingen kategori var vald. Inget flyttades.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Senaste diskussionerna');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Mina diskussioner');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Diskussioner jag har startat eller svarat på');
DEFINE('_KUNENA_CATEGORY', 'Kategori:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_POSTED_AT', 'Postad');
DEFINE('_KUNENA_AGO', 'sedan');
DEFINE('_KUNENA_DISCUSSIONS', 'Diskussioner');
DEFINE('_KUNENA_TOTAL_THREADS', 'Totalt antal trådar:');
DEFINE('_SHOW_DEFAULT', 'Standard');
DEFINE('_SHOW_MONTH', 'Månad');
DEFINE('_SHOW_YEAR', 'År');

// 1.0.4

DEFINE('_KUNENA_COPY_FILE', 'Kopierar "%src%" till "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Saving css file should be here...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Tabellen för bilagor har uppgraderats till den senaste strukturen för 1.0.x-serien!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Bilagor i meddelandetabellen uppgraderade till den senaste strukturen i 1.0.x serien!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Kunde inte ta bort inlägg - inget annat borttaget');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Kunde inte ta bort texterna för inläggen. Uppdatera databasen manuellt  (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Allting borttaget, men misslyckades med att uppdatera statistiken för användarnas inlägg!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Allvarligt databasfel. Uppdatera din databas manuellt så att svaren till ämnet kopplas ihop även med det nya forumet.");
DEFINE('_KUNENA_UNIST_SUCCESS', "FireBoard-komponentens avinstallation lyckades!");
DEFINE('_KUNENA_PDF_VERSION', 'FireBoard version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Skapat: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Inga forum att söka i.');
DEFINE('_KUNENA_ERRORADDUSERS', 'Ett fel uppstod då användare skulle läggas till:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Användarna synkroniserade; Tog bort:');
DEFINE('_KUNENA_USERSSYNCADD', ', lägg till:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'användarprofiler.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Inga profiler att synkronisera.');
DEFINE('_KUNENA_SYNC_USERS', 'Synkronisera användare');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synkronisera Fireboards användartabell med Joomlas användartabell.');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Skicka epost till administratörerna');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Välj &quot;Ja&quot; ifall du vill att det för alla nya inlägg skall skickas epostmeddelanden till administratörerna.');
DEFINE('_KUNENA_RANKS_EDIT', 'Ändra ranking');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Göm epost');
DEFINE('_KUNENA_DT_DATE_FMT','%d.%m.%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d.%m.%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Söndag');
DEFINE('_KUNENA_DT_LDAY_MON', 'Måndag');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Tisdag');
DEFINE('_KUNENA_DT_LDAY_WED', 'Onsdag');
DEFINE('_KUNENA_DT_LDAY_THU', 'Torsdag');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Fredag');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Lördag');
DEFINE('_KUNENA_DT_DAY_SUN', 'Sön');
DEFINE('_KUNENA_DT_DAY_MON', 'Mån');
DEFINE('_KUNENA_DT_DAY_TUE', 'Tis');
DEFINE('_KUNENA_DT_DAY_WED', 'Ons');
DEFINE('_KUNENA_DT_DAY_THU', 'Tors');
DEFINE('_KUNENA_DT_DAY_FRI', 'Fre');
DEFINE('_KUNENA_DT_DAY_SAT', 'Lör');
DEFINE('_KUNENA_DT_LMON_JAN', 'Januari');
DEFINE('_KUNENA_DT_LMON_FEB', 'Februari');
DEFINE('_KUNENA_DT_LMON_MAR', 'Mars');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'Maj');
DEFINE('_KUNENA_DT_LMON_JUN', 'Juni');
DEFINE('_KUNENA_DT_LMON_JUL', 'Juli');
DEFINE('_KUNENA_DT_LMON_AUG', 'Augusti');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'Oktober');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'December');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'Maj');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Okt');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec');
DEFINE('_KUNENA_CHILD_BOARD', 'Underforum');
DEFINE('_WHO_ONLINE_GUEST', 'Gäst');
DEFINE('_WHO_ONLINE_MEMBER', 'Medlem');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'ingen');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Klicka här för att fortsätta...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Verkställ!');
DEFINE('_KUNENA_NO_ACCESS', 'Du har inte tillgång till detta forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% sedan');
DEFINE('_KUNENA_DATE_YEARS', 'år');
DEFINE('_KUNENA_DATE_MONTHS', 'månader');
DEFINE('_KUNENA_DATE_WEEKS','veckor');
DEFINE('_KUNENA_DATE_DAYS', 'dagar');
DEFINE('_KUNENA_DATE_HOURS', 'timmar');
DEFINE('_KUNENA_DATE_MINUTES', 'minuter');

// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Är du säker på att du vill ta bort exempeldata? Det finns inget sätt att gå tillbaka.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumheader:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum display");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Medlems ändrings tid');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Sätt till 0 för obegränsat med
tid från skickat till senast ändrat för att tillåta ändring.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Tid för medlem att ändra');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [sekunder], tillåter
modifiering upp till 600 sekunder efter ändra länk försvinner');
DEFINE('_KUNENA_HELPPAGE','Tillåt hjälpsida');
DEFINE('_KUNENA_HELPPAGE_DESC','Om du klickar på &quot;Ja&quot; visas en länk i sidhuvudmenyn till din hjälpsida.');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA','Visa hjälp i fireboard');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Om du klickar på  &quot;Ja&quot; visas hjälp text i fireboard och hjälp länksidan kommer inte att fungera. <b>OBS!:</b> Du bör lägga till "innehåll på hjälpsidan" .');
DEFINE('_KUNENA_HELPPAGE_CID','Hjälp innehåll');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Du bör klicka på <b>"Ja"</b> "Visar hjälp i fireboard" inställningar.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Hjälpsides länk');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Om du vill visa hjälpsides länk, klicka på  <b>"Nej"</b> "Visa hjälp i fireboard" inställningar.');
DEFINE('_KUNENA_RULESPAGE','Visa regel sida');
DEFINE('_KUNENA_RULESPAGE_DESC','Om &quot;Ja&quot; visas en länk i sidhuvudets meny till regelsidan.');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA','Visa regler i fireboard');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Om du klickar på &quot;Ja&quot; kommer reglerna att visasrna att visas i fireboard extern länksida. <b>OBS!:</b> Du bör lägga till " innehåll på regelsidan" .');
DEFINE('_KUNENA_RULESPAGE_CID','Regler innehåll');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Klicka på <b>"Ja"</b> "Visa regler i fireboard" inställningar.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Länk till regler');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Om du vill visa regler via extern länk, klicka på <b>"Nej"</b> "Visa regler i fireboard" inställningar.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library hittas inte');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Library hittas inte');
DEFINE('_KUNENA_GD_INSTALLED','GD finns version ');
DEFINE('_KUNENA_GD_NO_VERSION','Hittar inte GD version');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD är inte installerat, du kan inte få mer info ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Liten bild höjd :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Liten bild bredd :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium bild höjd :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Medium bild bredd :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Stor bild höjd:');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Stor blid bredd:');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatarkvalitet');
DEFINE('_KUNENA_WELCOME','Välkommen till Fireboard');
DEFINE('_KUNENA_WELCOME_DESC','Tack för att du valde Fireboard som ditt forum. Har får du en snabb överblick över den tillgängliga statistiken för ditt forum. Länkarna på vänster sida ger dig kontroll över alla aspekter av din forumupplevelse. Varje sida ger instruktioner över hur du ska använde de olika verktygen.');
DEFINE('_KUNENA_STATISTIC','Statistik');
DEFINE('_KUNENA_VALUE','Värde');
DEFINE('_GEN_CATEGORY','Kategori');
DEFINE('_GEN_STARTEDBY','Startad av: ');
DEFINE('_GEN_STATS','statistik');
DEFINE('_STATS_TITLE',' forum - statistik');
DEFINE('_STATS_GEN_STATS','Total statistik');
DEFINE('_STATS_TOTAL_MEMBERS','Medlemmar:');
DEFINE('_STATS_TOTAL_REPLIES','Svar:');
DEFINE('_STATS_TOTAL_TOPICS','Ämnen:');
DEFINE('_STATS_TODAY_TOPICS','Ämnen idag:');
DEFINE('_STATS_TODAY_REPLIES','Svar idag:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategorier:');
DEFINE('_STATS_TOTAL_SECTIONS','Sektioner:');
DEFINE('_STATS_LATEST_MEMBER','Senaste medlem:');
DEFINE('_STATS_YESTERDAY_TOPICS','Ämnen igår:');
DEFINE('_STATS_YESTERDAY_REPLIES','Svar igår:');
DEFINE('_STATS_POPULAR_PROFILE','Mest populära medlem (Profilträffar)');
DEFINE('_STATS_TOP_POSTERS','Flitigast postare');
DEFINE('_STATS_POPULAR_TOPICS','Mest populära ämnen');
DEFINE('_COM_A_STATSPAGE','Aktivera statistik sida');
DEFINE('_COM_A_STATSPAGE_DESC','Om du väljer &quot;Ja&quot; Visas en länk till forum statistiken i sidhuvudet. Denna sida kommer att visa all sorts statistik om forumet. <em>Statistik sidan kommer alltid att kunna nås av admins på forumet oavsett inställning!</em>');
DEFINE('_COM_C_JBSTATS','Forum Stats');
DEFINE('_COM_C_JBSTATS_DESC','Forum Stastik');
define('_GEN_GENERAL','Total');
define('_PERM_NO_READ','Du har inte alla rättigheter för att besöka detta forum.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smiley sparad');
DEFINE ('_KUNENA_SMILEY_DELETED','Smiley raderad');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Kod finns redan');
DEFINE ('_KUNENA_MISSING_PARAMETER','Parameter saknas');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rank finns redan');
DEFINE ('_KUNENA_RANK_DELETED','Rank raderad');
DEFINE ('_KUNENA_RANK_SAVED','Rank sparad');
DEFINE ('_KUNENA_DELETE_SELECTED','Radera utvald');
DEFINE ('_KUNENA_MOVE_SELECTED','Flytta utvald');
DEFINE ('_KUNENA_REPORT_LOGGED','Loggad');
DEFINE ('_KUNENA_GO','Kör');
DEFINE('_KUNENA_MAILFULL','Skicka hela meddelandet som epost till prenumeranter');
DEFINE('_KUNENA_MAILFULL_DESC','Om nej - får prenumeranter bara se titeln av meddelandet');
DEFINE('_KUNENA_HIDETEXT','Vänligen logga in om du vill ta del av detta!');
DEFINE('_BBCODE_HIDE','Gömd text: [hide]bla bla bla[/hide] - gömmer del av text för icke inloggade');
DEFINE('_KUNENA_FILEATTACH','Bifogad fil: ');
DEFINE('_KUNENA_FILENAME','Fil namn: ');
DEFINE('_KUNENA_FILESIZE','Fil storlek: ');
DEFINE('_KUNENA_MSG_CODE','Kod: ');
DEFINE('_KUNENA_CAPTCHA_ON','Spam protect system');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA system On/Off');
DEFINE('_KUNENA_CAPDESC','Skriv kod här');
DEFINE('_KUNENA_CAPERR','Felaktig kod!');
DEFINE('_KUNENA_COM_A_REPORT', 'Meddelande rapportering');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Om du vill att medlemmarna ska kunna rapportera inlägg, välj ja.');
DEFINE('_KUNENA_REPORT_MSG', 'Meddelande rapporterat');
DEFINE('_KUNENA_REPORT_REASON', 'orsak');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Ditt meddelande');
DEFINE('_KUNENA_REPORT_SEND', 'Skicka rapport');
DEFINE('_KUNENA_REPORT', 'Rapportera till moderator');
DEFINE('_KUNENA_REPORT_RSENDER', 'Rapport avsändare: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Rapport orsak: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Rapportera meddelande: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Avsändare av meddelande: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Meddelande ämne: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Meddelande: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Meddelande länk: ');
DEFINE('_KUNENA_REPORT_INTRO', 'har skickat ett meddelande till dig för att');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Rapporten skickades!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kod');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Ändra smiley');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Lägg till smilies');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Smilies');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nya smilies');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Fler smilies');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Stäng fönster');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Visa fler smilies');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Välj en smilies');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Tillåt Joomla Mambot Support');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Min profil plugin inställningar');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Tillåt ändring av användarnamn');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Tillåt användarnamns ändring i min profil');
DEFINE ('_KUNENA_RECOUNTFORUMS','Räkna om forumstatistiken');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','All statistik är nu omräknad.');
DEFINE ('_KUNENA_EDITING_REASON','Anledning till ändring');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Senast ändrat');
DEFINE ('_KUNENA_BY','av');
DEFINE ('_KUNENA_REASON','Anledning');
DEFINE('_GEN_GOTOBOTTOM', 'Till sista');
DEFINE('_GEN_GOTOTOP', 'Till början');
DEFINE('_STAT_USER_INFO', 'Användar info');
DEFINE('_USER_SHOWEMAIL', 'Visa epost');
DEFINE('_USER_SHOWONLINE', 'Visa online');
DEFINE('_KUNENA_HIDDEN_USERS', 'Gömda användare');
DEFINE('_KUNENA_SAVE', 'Spara');
DEFINE('_KUNENA_RESET', 'Återställ');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Standard bildgalleri');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Personlig Info');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Summering');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Min avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Foruminställningar');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Utseende och layout');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Min profilinfo');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Mina inlägg');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Mina prenumerationer');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Mina favoriter');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Privata meddelanden');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Inkorg');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nya meddelanden');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Utkorg');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Skräp');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Inställningar');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakter');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Blockerat lista');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Tilläggsinfo');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Namn');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Användarnamn');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Epost');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Medlemstyp');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Registrerades');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Senaste besök');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Inlägg');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Visa profil');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Personlig text');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Kön');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Födelsedata');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'År (xxxx) - Mån (xx) - Dag (xx)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Stad');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'Klubb');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Din klubb.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'Moderklubb');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Din moderklubb.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Ditt Yahoo! Instant Messenger nick.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Ditt Skype handle.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Ditt Gtalk nick.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Webbsida');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Webbsidans namn');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Exempel: Min webbsida!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Websida URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Exempel: www.minwebbsida.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Din MSN messenger epost-address.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Signatur');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Man');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Kvinna');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Inlägget raderades');
DEFINE('_KUNENA_DATE_YEAR', 'År');
DEFINE('_KUNENA_DATE_MONTH', 'Månad');
DEFINE('_KUNENA_DATE_WEEK','Vecka');
DEFINE('_KUNENA_DATE_DAY', 'Dag');
DEFINE('_KUNENA_DATE_HOUR', 'Timme');
DEFINE('_KUNENA_DATE_MINUTE', 'Minut');
DEFINE('_KUNENA_IN_FORUM', ' i forum: ');
DEFINE('_KUNENA_FORUM_AT', ' forum på: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'OBS! Fast det inte visas några knappar för boardcode eller smileys så går det ändå att använda det.');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forumverktyg');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Användarlista');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s har <b>%d</b> registererade användare');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Skriv in ett värde att söka efter!');
DEFINE ('_KUNENA_USRL_SEARCH','Hitta användare');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Sök');
DEFINE ('_KUNENA_USRL_LIST_ALL','Lista alla');
DEFINE ('_KUNENA_USRL_NAME','Namn');
DEFINE ('_KUNENA_USRL_USERNAME','Användarnamn');
DEFINE ('_KUNENA_USRL_GROUP','Grupp');
DEFINE ('_KUNENA_USRL_POSTS','Inlägg');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Träffar');
DEFINE ('_KUNENA_USRL_EMAIL','E-post');
DEFINE ('_KUNENA_USRL_USERTYPE','Användartyp');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Blev medlem datum');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Senaste inloggning');
DEFINE ('_KUNENA_USRL_NEVER','Aldrig');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Bild');
DEFINE ('_KUNENA_USRL_ASC','Stigande');
DEFINE ('_KUNENA_USRL_DESC','Fallande');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Visa');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Användarlista');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Antal rader för användarlista');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Antal rader för användarlista');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Onlinestatus');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Visa användares onlinestatus');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Visa avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Visa verkligt namn');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Visa användarnamn');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Visa användargrupp');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Visa antal inlägg');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Visa karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Visa e-post');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Visa användartyp');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Visa när användaren blev medlem');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Visa senaste besöksdatumet');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Visa träffar för profilen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Databasguide');
DEFINE('_KUNENA_DBMETHOD', 'Välj med vilken metod du vill komplettera din installation:');
DEFINE('_KUNENA_DBCLEAN', 'Ren installation');
DEFINE('_KUNENA_DBUPGRADE', 'Uppgradera från Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Toppnivå kategori');
DEFINE('_KUNENA_REGISTERED', 'Registererad');
DEFINE('_KUNENA_PUBLICBACKEND', 'Public Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Välj ett objekt att');
DEFINE('_KUNENA_ERRORSUBS', 'Ett fel inträffade när meddelanden och prenumerationer skulle raderas');
DEFINE('_KUNENA_WARNING', 'Varning...');
DEFINE('_KUNENA_CHMOD1', 'Du måste sätta rättigheterna/CHMOD till 766 för att filen ska kunna uppdateras.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Din konfigurationsfil är');
DEFINE('_KUNENA_FIREBOARD', 'Fireboard');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Välj Mall/Template');
DEFINE('_KUNENA_CFNW', 'FATAL ERROR: Konfigurationsfilen är inte skrivbar');
DEFINE('_KUNENA_CFS', 'Konfigurationsfilen är sparad');
DEFINE('_KUNENA_CFCNBO', 'FATAL ERROR: Filen kunde inte öppnas.');
DEFINE('_KUNENA_TFINW', 'Filen är inte skrivbar.');
DEFINE('_KUNENA_KUNENACFS', 'Fireboard CSS-fil är sparad');
DEFINE('_KUNENA_SELECTMODTO', 'Välj en moderator till');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Du måste välja ett forum att rensa!');
DEFINE('_KUNENA_DELMSGERROR', 'Radering av meddelande misslyckades:');
DEFINE('_KUNENA_DELMSGERROR1', 'Radering av textmeddelanden misslyckades:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Rensning av prenumerationer misslyckades:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum rensat för');
DEFINE('_KUNENA_PRUNEDAYS', 'dagar');
DEFINE('_KUNENA_PRUNEDELETED', 'Raderade:');
DEFINE('_KUNENA_PRUNETHREADS', 'Trådar');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Det uppstod ett fel när användare skulle rensas:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Användare rensade; Raderade:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'användarprofiler');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Hittade inga profiler som är lämpliga för rensning.');
DEFINE('_KUNENA_TABLESUPGRADED', 'Fireboard tabeller är uppgraderade till version');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forumkategori');
DEFINE('_KUNENA_SAMPLWARN1', '-- Se till att du är helt säker på att du laddar exempeldata i helt tomma fireboard tabeller. Om det finns något i dem kommer det inte att fungera!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Exempelinlägg 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Exempel Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Exempelinlägg[/color][/size][/b]\nGrattis till ditt nya forum!\n\n[url=http://bestofjoomla.com]- Best of Joomla[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Exempeldata laddat');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Exempeldata borttaget');
DEFINE('_KUNENA_CBADDED', 'Community Builder profil har lagts till');
DEFINE('_KUNENA_IMGDELETED', 'Bild raderad');
DEFINE('_KUNENA_FILEDELETED', 'Fil raderad');
DEFINE('_KUNENA_NOPARENT', 'Ingen överliggande kategori');
DEFINE('_KUNENA_DIRCOPERR', 'Fel: Fil');
DEFINE('_KUNENA_DIRCOPERR1', 'kunde inte kopieras!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Fireboard Forum</strong> Komponent <em>för Joomla! CMS</em> <br />&copy; 2006 - 2007 by Best Of Joomla<br>All rights reserved.');
DEFINE('_KUNENA_INSTALL2', '÷verföring/Installation :</code></strong><br /><br /><font color="red"><b>lyckades');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profilinställningar');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Om du har Clexus PM eller Community Builder komponent installerade, kan du konfigurera fireboard för att använda användarprofilsidan.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profile View</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Alla forummeddelanden');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Ämnen');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Startade av');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Datum');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Träffar');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Inga foruminlägg');
DEFINE('_KUNENA_TOTALFAVORITE', 'Favoriserade:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Antal kolumner för underkategorier');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Antal kolumner för underkategorier formaterade under toppkategorin ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Ska prenumeration av inlägg kontrolleras varje gång?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Välj &quot;Ja&quot; Om du vill att rutan för prenumeration av inlägg ska vara markerad');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategori / Forum måste ha ett namn');
// Forum Configuration (New in Fireboard)
DEFINE('_KUNENA_SHOWSTATS', 'Visa statistik');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Välj Ja om du vill visa statistik');
DEFINE('_KUNENA_SHOWWHOIS', 'Visa vilka som är online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Välj Ja om du vill visa vilka som är online');
DEFINE('_KUNENA_STATSGENERAL', 'Visa allmän statistik');
DEFINE('_KUNENA_STATSGENERALDESC', 'Välj Ja om du vill visa allmän statisik');
DEFINE('_KUNENA_USERSTATS', 'Visa statistik för populära användare');
DEFINE('_KUNENA_USERSTATSDESC', 'Välj Ja om du vill visa statistik för populära användare');
DEFINE('_KUNENA_USERNUM', 'Antal populära användare');
DEFINE('_KUNENA_USERPOPULAR', 'Visa statistik för populära ämnen');
DEFINE('_KUNENA_USERPOPULARDESC', 'Välj Ja om du vill visa populära ämnen');
DEFINE('_KUNENA_NUMPOP', 'Antal populära ämnen');
DEFINE('_KUNENA_INFORMATION',
    'Best of Joomla team is proud to announce the release of Fireboard 1.0.0. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard team and most of our praises goes to their team.Some of the main features of Fireboard can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Best of Joomla! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instruktioner');
DEFINE('_KUNENA_FINFO', 'Fireboard Forum Information');
DEFINE('_KUNENA_CSSEDITOR', 'CSS-editor');
DEFINE('_KUNENA_PATH', 'Sökväg:');
DEFINE('_KUNENA_CSSERROR', 'OBS!:CSS Mallfilen måste vara skrivbar för att ändringar ska kunna sparas.');
// User Management
DEFINE('_KUNENA_FUM', 'Fireboards hanterare för användarprofiler');
DEFINE('_KUNENA_SORTID', 'sortera efter användar-ID');
DEFINE('_KUNENA_SORTMOD', 'sortera efter moderatorer');
DEFINE('_KUNENA_SORTNAME', 'sortera efter namn');
DEFINE('_KUNENA_VIEW', 'Visa');
DEFINE('_KUNENA_NOUSERSFOUND', 'Hittade inga användarprofiler.');
DEFINE('_KUNENA_ADDMOD', 'Lägg till en moderator för');
DEFINE('_KUNENA_NOMODSAV', 'Det finns inga tillgängliga moderatorer. Läs \'noteringen \' nedan.');
DEFINE('_KUNENA_NOTEUS',
    'NOTERA: Bara användare som har moderatorflaggan satt i sin fireboardprofil visas här. För att kunna lägga till en moderator och ge en användare en moderatorflagga, gå till <a href="index2.php?option=com_fireboard&task=profiles">Användaradministration</a>och sök efter användaren som du vill göra till moderator. Välj hans eller hennes profil och uppdatera den. Moderatorflagga kan endast ställas in av sitens administratör. ');
DEFINE('_KUNENA_PROFFOR', 'Profil för');
DEFINE('_KUNENA_GENPROF', 'Allmänna profilinställningar');
DEFINE('_KUNENA_PREFVIEW', 'Hur ska forumet visas?:');
DEFINE('_KUNENA_PREFOR', 'Hur vill du att inläggen ska visas?:');
DEFINE('_KUNENA_ISMOD', 'Är moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Ja</strong> (går inte att ändra, denna användare är sidans (super)administrator)');
DEFINE('_KUNENA_COLOR', 'Färg');
DEFINE('_KUNENA_UAVATAR', 'Användaravatar:');
DEFINE('_KUNENA_NS', 'Ingen vald');
DEFINE('_KUNENA_DELSIG', ' markera denna ruta för att radera denna signatur');
DEFINE('_KUNENA_DELAV', ' markera denna ruta för att radera denna avatar');
DEFINE('_KUNENA_SUBFOR', 'prenumerationer för');
DEFINE('_KUNENA_NOSUBS', 'Hittade inga prenumerationer för denna användare');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Grunder');
DEFINE('_KUNENA_BASICSFORUM', 'Grundläggande foruminfo');
DEFINE('_KUNENA_PARENT', '÷verliggande kategori:');
DEFINE('_KUNENA_PARENTDESC',
    'OBS!: För att skapa en Kategori, välj \'Toppnivå kategori\' som överliggande. En kategori fungerar som en platshållare för forum.<br />Ett forum kan <strong>endast</strong> skapas inuti en kategori genom att välja en nyss skapad kategori som överliggande för forumet.<br /> Inlägg kan <strong>INTE</strong> postas i en kategori, bara i forum.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Forumets namn och beskrivning');
DEFINE('_KUNENA_NAMEADD', 'Namn:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Beskrivning:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Avancerad Forumkonfiguration');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Forumets säkerhet och åtkomst');
DEFINE('_KUNENA_LOCKEDDESC', 'Välj &quot;Ja&quot; om du vill låsa detta forum. Ingen, förutom moderatorer och administratörer kan skapa nya ämnen eller svara i ett låst forum (eller flytta inlägg i det).');
DEFINE('_KUNENA_LOCKED1', 'Låst:');
DEFINE('_KUNENA_PUBACC', 'Allmän åtkomstnivå:');
DEFINE('_KUNENA_PUBACCDESC',
    'Om du vill skapa ett privat forum så kan du ange lägsta nivån för de som kan se/eller gå in i forumet här. Som standard är lägsta användarnivån satt till &quot; ALLA &quot;. <b>OBS!</b>:  om du begränsar åtkomsten för en hel kategori till en eller speciell grupp kommer det att dölja allt foruminnehåll för alla som inte har behörighet för kategorin <b>även</b> om en eller fler av dessa forum har lägre åtkomstnivå inställd! Detta gäller även för moderatorer. Moderator kan fortfarande läggas till i moderatorlistan.');
DEFINE('_KUNENA_CGROUPS', 'Inkludera undergrupper:');
DEFINE('_KUNENA_CGROUPSDESC', 'Ska undergrupper också ha åtkomst? Om du väljer &quot;Nej&quot; kommer tillgängligheten till detta forum att begränsas till <b>endast</b> den valda gruppen ');
DEFINE('_KUNENA_ADMINLEVEL', 'Admin åtkomstnivå:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Om du skapar ett forum med restriktioner mot allmän åtkomst, så kan du här ange ytterligare en Administration åtkomstnivå.<br /> Om du begränsar åtkomsten till forumet för en speciell Public Frontend användargrupp och inte anger en Public Backend Gruppp här, så kommer inte administratörer att kunna komma in eller se forumet.');
DEFINE('_KUNENA_ADVANCED', 'Avancerat');
DEFINE('_KUNENA_CGROUPS1', 'Inkludera undergrupper:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Ska undergrupper också ha åtkomst? Om du väljer &quot;Nej &quot; så kommer <b>endast</b> den valda gruppen att ha åtkomst till detta forum');
DEFINE('_KUNENA_REV', 'Änmäla inlägg:');
DEFINE('_KUNENA_REVDESC',
    'Välj &quot;Ja&quot; om du vill attt inlägg ska först anmälas till Moderatorer innan de publiceras i forumet. Detta är endast användbart i ett Modererat forum. Om du vill använda detta utan några specificerade användare, är Site Admin ensam ansvarig för godkännande/radering av inskickade inlägg och de kommer att vara \'under behandling\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderering');
DEFINE('_KUNENA_MODNEWDESC', 'Moderering av forum och forum moderatorer');
DEFINE('_KUNENA_MOD', 'Modererad:');
DEFINE('_KUNENA_MODDESC',
    'Välj &quot;Ja&quot; om du vill kunna knyta moderatorer till detta forum.<br /><strong>Obs!:</strong> Detta betyder inte att nya inlägg måste först godkännas innan de publiceras i forumet!<br /> Du måste använda &quot;Anmäla Inlägg&quot; valet för det under Avancerat fliken.<br /><br /> <strong>OBS!:</strong> Efter det att du satt moderering till &quot;Ja&quot; måste du spara forumkonfigurationen först innan du kan lägga till moderatorer genom att använda NY-knappen.');
DEFINE('_KUNENA_MODHEADER', 'Moderations inställningar för detta forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatorer knutna till detta forum:');
DEFINE('_KUNENA_NOMODS', 'Det finns inga moderatorer knutna till detta forum');
// Some General Strings (Improvement in Fireboard)
DEFINE('_KUNENA_EDIT', 'Ändra');
DEFINE('_KUNENA_ADD', 'Lägg till');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Flytta upp');
DEFINE('_KUNENA_MOVEDOWN', 'Flytta ner');
// Groups - Integration in Fireboard
DEFINE('_KUNENA_ALLREGISTERED', 'Alla registererade');
DEFINE('_KUNENA_EVERYBODY', 'Alla');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Sortera om');
DEFINE('_KUNENA_CHECKEDOUT', 'Checka ut');
DEFINE('_KUNENA_ADMINACCESS', 'Admin åtkomst');
DEFINE('_KUNENA_PUBLICACCESS', 'Allmän åtkomst');
DEFINE('_KUNENA_PUBLISHED', 'Publicerad');
DEFINE('_KUNENA_REVIEW', 'Kommentera');
DEFINE('_KUNENA_MODERATED', 'Modererad');
DEFINE('_KUNENA_LOCKED', 'Låst');
DEFINE('_KUNENA_CATFOR', 'Kategori / Forum');
DEFINE('_KUNENA_ADMIN', '&nbsp;Fireboard Administration');
DEFINE('_KUNENA_CP', 'Fireboard kontrollpanel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar integration');
DEFINE('_COM_A_RANKS_SETTINGS', 'Rankning');
DEFINE('_COM_A_RANKING_SETTINGS', 'Rankningsinställningar');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Avatarinställningar');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Säkerhetsinställningar');
DEFINE('_COM_A_BASIC_SETTINGS', 'Allmänna instälningar');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES','Tillåt favoriter');
DEFINE('_COM_A_FAVORITES_DESC','Sätt denna till &quot;Ja&quot; om du vill tillåta registrerade användare att använda favoriter för ett ämne ');
DEFINE('_USER_UNFAVORITE_ALL','Kryssa i denna för att <b><u>ta bort favoriter</u></b> från alla ämnen (inklusive osynliga ämnen avsedda för felsökning)');
DEFINE('_VIEW_FAVORITETXT','Ange det här ämnet som en favorit ');
DEFINE('_USER_UNFAVORITE_YES','Du har tagit bort det här ämnet från favoriter');
DEFINE('_POST_FAVORITED_TOPIC','Din favorit har lagts till.');
DEFINE('_VIEW_UNFAVORITETXT','Ta bort favorit');
DEFINE('_VIEW_UNSUBSCRIBETXT','Avbryt prenumeration');
DEFINE('_USER_NOFAVORITES','Inga favoriter');
DEFINE('_POST_SUCCESS_FAVORITE','Din favorit har lagts till.');
DEFINE('_COM_A_MESSAGES_SEARCH','Sökresultat');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH','Antal meddelanden per sida vid sökning.');
DEFINE('_KUNENA_USE_JOOMLA_STYLE','Använd Joomla-stil?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Om du vill använda joomla-stil, sätt denna till JA. (class: t.ex. sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST','Visa bild för underkategorier');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC','Om du vill visa en liten ikon för underkategorier i forumlistan, sätt denna till JA. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','Visa meddelanden');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Sätt till "Ja" , om du vill visa en ruta med meddelanden på din hemsida.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Visa avatarer i kategorilistan?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Sätt till "Ja" , om du vill visa användaravatarer i forumets kategorilista.');
DEFINE('_KUNENA_RECENT_POSTS','Inställningar för visning av senaste inläggen');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','Senaste inläggen');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','Sätt till "Ja" om du vill visa senaste inläggen i ditt forum.');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','Antal senaste inlägg som ska visas');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','Antal nyligen gjorda inlägg som ska visas.');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Antal per flik ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','Antal inlägg på en flik.');
DEFINE('_KUNENA_LATEST_CATEGORY','Visa kategori');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','Här kan du välja att visa nyligen gjorda inlägg för specifika kategorier. Till exempel:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Visa enstaka ämnen');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','Visa enstaka ämnen.');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Visa svarsämnen');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','Visa svarsämne (Sv:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','Längd för ämnesrad');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','Längd för ämnesrad.');
DEFINE('_KUNENA_SHOW_LATEST_DATE','Visa datum');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC','Visa datum.');
DEFINE('_KUNENA_SHOW_LATEST_HITS','Visa träffar');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC','Visa träffar.');
DEFINE('_KUNENA_SHOW_AUTHOR','Visa författare');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC','1=användarnamn, 2=namn, 0=inget');
DEFINE('_KUNENA_STATS','Status för plugin-inställningar ');
DEFINE('_KUNENA_CATIMAGEPATH','Sökväg för kategoribilder ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','Sökväg för kategoribilder. Om du sätter denna till "category_images/" kommer sökvägen att bli "components/com_fireboard/category_images/');
DEFINE('_KUNENA_ANN_MODID','ID:n för moderatorernas meddelanden ');
DEFINE('_KUNENA_ANN_MODID_DESC','Lägg till användar-id:n för att moderera meddelanden. e.g. 62,63,73 . Meddelanden som moderatorer sedan kan lägga till, ändra eller radera.');
//
DEFINE('_KUNENA_FORUM_TOP','Forumkategorier ');
DEFINE('_KUNENA_CHILD_BOARDS','Underforum ');
DEFINE('_KUNENA_QUICKMSG','Snabbsvar ');
DEFINE('_KUNENA_THREADS_IN_FORUM','Tråd i forum ');
DEFINE('_KUNENA_FORUM','Forum ');
DEFINE('_KUNENA_SPOTS','I fokus');
DEFINE('_KUNENA_CANCEL','Ångra');
DEFINE('_KUNENA_TOPIC','ÄMNE: ');
DEFINE('_KUNENA_POWEREDBY','Powered by ');
// Time Format
DEFINE('_TIME_TODAY','<b>Idag</b> ');
DEFINE('_TIME_YESTERDAY','<b>Igår</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS','Senaste inläggen');
DEFINE('_KUNENA_WHO_WHOISONLINE','Vilka är online');
DEFINE('_KUNENA_WHO_MAINPAGE','Startsida Forum');
DEFINE('_KUNENA_GUEST','Gäst');
DEFINE('_KUNENA_PATHWAY_VIEWING','läser');
DEFINE('_KUNENA_ATTACH','Bilaga');
// Favorite
DEFINE('_KUNENA_FAVORITE','Favorit');
DEFINE('_USER_FAVORITES','Dina favoriter');
DEFINE('_THREAD_UNFAVORITE','Ta bort som favorit');
// profilebox
DEFINE('_PROFILEBOX_WELCOME','Välkommen');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS','Senaste inläggen');
DEFINE('_PROFILEBOX_SET_MYAVATAR','Lägg till min avatar');
DEFINE('_PROFILEBOX_MYPROFILE','Min profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS','Visa mina inlägg');
DEFINE('_PROFILEBOX_GUEST','Gäst');
DEFINE('_PROFILEBOX_LOGIN','Logga in');
DEFINE('_PROFILEBOX_REGISTER','Registrera');
DEFINE('_PROFILEBOX_LOGOUT','Logga ut');
DEFINE('_PROFILEBOX_LOST_PASSWORD','Glömt ditt lösenord?');
DEFINE('_PROFILEBOX_PLEASE','Vänligen');
DEFINE('_PROFILEBOX_OR','eller');
// recentposts
DEFINE('_RECENT_RECENT_POSTS','Senaste inläggen');
DEFINE('_RECENT_TOPICS','Ämne');
DEFINE('_RECENT_AUTHOR','Författare');
DEFINE('_RECENT_CATEGORIES','Kategorier');
DEFINE('_RECENT_DATE','Datum');
DEFINE('_RECENT_HITS','Träffar');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Nyhet');
DEFINE('_ANN_ID','ID');
DEFINE('_ANN_DATE','Datum');
DEFINE('_ANN_TITLE','Titel');
DEFINE('_ANN_SORTTEXT','Kort text');
DEFINE('_ANN_LONGTEXT','Lång text');
DEFINE('_ANN_ORDER','Order');
DEFINE('_ANN_PUBLISH','Publicera');
DEFINE('_ANN_PUBLISHED','Publicerad');
DEFINE('_ANN_UNPUBLISHED','Opublicerad');
DEFINE('_ANN_EDIT','Ändra');
DEFINE('_ANN_DELETE','Radera');
DEFINE('_ANN_SUCCESS','Lyckades');
DEFINE('_ANN_SAVE','Spara');
DEFINE('_ANN_YES','Ja');
DEFINE('_ANN_NO','Nej');
DEFINE('_ANN_ADD','Lägg till');
DEFINE('_ANN_SUCCESS_EDIT','Ändrad');
DEFINE('_ANN_SUCCESS_ADD','Tillagd');
DEFINE('_ANN_DELETED','Raderad');
DEFINE('_ANN_ERROR','FEL');
DEFINE('_ANN_READMORE','Läs mer...');
DEFINE('_ANN_CPANEL','Kontrollpanel för nyheter');
DEFINE('_ANN_SHOWDATE','Visa datum');
// Stats
DEFINE('_STAT_FORUMSTATS','Forumstatistik');
DEFINE('_STAT_GENERAL_STATS','Generell statistik');
DEFINE('_STAT_TOTAL_USERS','Totalt antal användare');
DEFINE('_STAT_LATEST_MEMBERS','Nyaste medlemmen');
DEFINE('_STAT_PROFILE_INFO','Visa profil');
DEFINE('_STAT_TOTAL_MESSAGES','Totalt antal meddelanden');
DEFINE('_STAT_TOTAL_SUBJECTS','Totalt antal ämnen');
DEFINE('_STAT_TOTAL_CATEGORIES','Totalt antal kategorier');
DEFINE('_STAT_TOTAL_SECTIONS','Totalt antal sektioner');
DEFINE('_STAT_TODAY_OPEN_THREAD','Öppnade idag');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD','Öppnade igår');
DEFINE('_STAT_TODAY_TOTAL_ANSWER','Besvarade idag');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER','Besvarade igår');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM','Senaste inläggen');
DEFINE('_STAT_MORE_ABOUT_STATS','Mer om statistik');
DEFINE('_STAT_USERLIST','Användarlista');
DEFINE('_STAT_TEAMLIST','Forumteam');
DEFINE('_STATS_FORUM_STATS','Forumstatistik');
DEFINE('_STAT_POPULAR','Populärt');
DEFINE('_STAT_POPULAR_USER_TMSG','Användare ( Totalt antal meddelanden) ');
DEFINE('_STAT_POPULAR_USER_KGSG','Trådar ');
DEFINE('_STAT_POPULAR_USER_GSG','Användare ( Totalt Profilvisningar) ');
//Team List
DEFINE('_MODLIST_ONLINE','Medlem online');
DEFINE('_MODLIST_OFFLINE','Medlem offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE','Vem är online');
DEFINE('_WHO_ONLINE_NOW','Online');
DEFINE('_WHO_ONLINE_MEMBERS','Medlemmar');
DEFINE('_WHO_AND','och');
DEFINE('_WHO_ONLINE_GUESTS','Gäster');
DEFINE('_WHO_ONLINE_USER','Användare');
DEFINE('_WHO_ONLINE_TIME','Tid');
DEFINE('_WHO_ONLINE_FUNC','Åtgärd');
// Userlist
DEFINE ('_USRL_USERLIST','Användarlista');
DEFINE ('_USRL_REGISTERED_USERS','%s har <b>%d</b> registrerade användare');
DEFINE ('_USRL_SEARCH_ALERT','Ange något att söka efter!');
DEFINE ('_USRL_SEARCH','Hitta användare');
DEFINE ('_USRL_SEARCH_BUTTON','Sök');
DEFINE ('_USRL_LIST_ALL','Lista alla');
DEFINE ('_USRL_NAME','Namn');
DEFINE ('_USRL_USERNAME','Användarnamn');
DEFINE ('_USRL_EMAIL','E-post');
DEFINE ('_USRL_USERTYPE','Användartyp');
DEFINE ('_USRL_JOIN_DATE','Registreringsdatum');
DEFINE ('_USRL_LAST_LOGIN','Senast inloggad');
DEFINE ('_USRL_NEVER','Aldrig');
DEFINE ('_USRL_BLOCK','Status');
DEFINE ('_USRL_MYPMS2','MyPMS');
DEFINE ('_USRL_ASC','Stigande');
DEFINE ('_USRL_DESC','Fallande');
DEFINE ('_USRL_DATE_FORMAT','%d.%m.%Y');
DEFINE ('_USRL_TIME_FORMAT','%H:%M');
DEFINE ('_USRL_USEREXTENDED','Detaljer');
DEFINE ('_USRL_COMPROFILER','Profil');
DEFINE ('_USRL_THUMBNAIL','Bild');
DEFINE ('_USRL_READON','visa');
DEFINE ('_USRL_MYPMSPRO','Clexus PM');
DEFINE ('_USRL_MYPMSPRO_SENDPM','Skicka PM');
DEFINE ('_USRL_JIM','PM');
DEFINE ('_USRL_UDDEIM','PM');
DEFINE ('_USRL_SEARCHRESULT','Sökresultat för');
DEFINE ('_USRL_STATUS','Status');
DEFINE ('_USRL_LISTSETTINGS','Inställningar för användarlista');
DEFINE ('_USRL_ERROR','Fel');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE','Privatmeddelande komponent');
DEFINE('_COM_A_COMBUILDER_TITLE','Community Builder');
DEFINE('_FORUM_SEARCH','Sökte efter: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','Är du säker på att du vill radera detta meddelande? \n\n OBS: Det finns inget sätt att återfå raderade meddelanden!');
DEFINE('_MODERATION_DELETE_SUCCESS','Meddelande(t/n) har raderats');
DEFINE('_COM_A_RANKING','Ranking');
DEFINE('_COM_A_BOT_REFERENCE','Visa botens referenser');
DEFINE('_COM_A_MOSBOT','Aktivera diskussionsbot');
DEFINE('_PREVIEW','Förhandsgranska');
DEFINE('_COM_A_MOSBOT_TITLE','Diskussionsbot');
DEFINE('_COM_A_MOSBOT_DESC', 'Diskussionsboten låter dina användare diskutera artiklar i forumen. Artikelns titel används som ämnesrubrik.'
.'<br />Om ett ämne inte redan finns så skapas ett nytt. Finns ämnet så visas tråden för användaren som kan svara i den.'
.'<br /><strong>Du måste ladda ned och installera Boten separat.</strong>'
.'<br />se <a href="http://www.bestofjoomla.com">Best of Joomla Site</a> för mer information.'
.'<br />När installationen är klar måste du ange följande Bot rader i ditt innehåll:'
.'<br />{mos_KUNENA_discuss:<em>catid</em>}'
.'<br /><em>catid</em> Är kategorin där denna artikel kan diskuteras. För att hitta rätt catid, titta i forumen '
.'och kontrollera category id i URL-erna i din webbläsares statusrad.'
.'<br />Exampelvis: om du vill diskutera en artikel i ett forum med catid 26, så skall Boten se ut så här: {mos_KUNENA_discuss:26}'
.'<br />Detta kan verka svårt, men ger dig möjligheten att låta varje artikel diskuteras i ett passande forum.' );
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE','Sök');
DEFINE('_FORUM_SEARCHRESULTS','visar %s av %s resultat.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP','FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES','<h3 class="contentheading">Forumets regler</h3><li>
</li></ul><br>');
DEFINE('_COM_FORUM_RULES_DESC','<ul><li>Redigera filen joomlaroot/administrator/components/com_fireboard/language/swedish.php för att lägga till dina regler</li><li>Regel 2</li><li>Regel 3</li><li>Regel 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE','Forumkoder');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS','Inlägg har godkänts');
DEFINE('_MODERATION_DELETE_ERROR','FEL: Inlägg kunde inte raderas');
DEFINE('_MODERATION_APPROVE_ERROR','FEL: Inlägg kunde inte godkännas');
// listcat.php
DEFINE('_GEN_NOFORUMS','Det finns inga forum i denna kategori!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED','Kunde inte skapa skuggämne i gammalt forum!');
DEFINE('_POST_MOVE_GHOST','Lämne skuggämne i gammalt forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP','Forumhopp');
DEFINE('_COM_A_FORUM_JUMP','Aktivera forumhopp');
DEFINE('_COM_A_FORUM_JUMP_DESC','Om satt till &quot;Ja&quot; dyker det upp ett snabbvalsfält för att snabbt hoppa till ett annat underforum eller en annan kategori.');
//new in 1.1 RC1
DEFINE('_GEN_RULES','Regler');
DEFINE('_COM_A_RULESPAGE','Aktivera regelsida');
DEFINE('_COM_A_RULESPAGE_DESC',
	'Om satt till &quot;Ja&quot; får du en länk till regelsidan i huvudmenyn. Denna sida kan användas till dina regler etc.');
DEFINE('_MOVED_TOPIC','FLYTTAD:');
DEFINE('_COM_A_PDF','Aktivera PDF skapande');
DEFINE('_COM_A_PDF_DESC',
	'Välj &quot;Ja&quot; om du vill låta användarna ladda ner ett enkelt PDF dokument med innehållet i en tråd.<br />Det är ett <u>enkelt</u> PDF dokument; ingen särskilt fin layout och så, men den innehåller all text från tråden.');
DEFINE('_GEN_PDFA','Klicka på den här knappen för att skapa ett PDF-dokument av den här tråden (öppnas i nytt fönster).');
DEFINE('_GEN_PDF', 'PDF');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE','Klicka här för att se användarens profil');
DEFINE('_VIEW_ADDBUDDY','Klicka här för att lägga till denna användare i din kompislista');
DEFINE('_POST_SUCCESS_POSTED','Ditt inlägg har postats');
DEFINE('_POST_SUCCESS_VIEW','Om du vill återgå till ditt inlägg, klicka här ');
DEFINE('_POST_SUCCESS_FORUM','Om du vill återgå till forumet, klicka här');
DEFINE('_RANK_ADMINISTRATOR','Admin');
DEFINE('_RANK_MODERATOR','Moderator');
DEFINE('_SHOW_LASTVISIT','Sedan senaste besöket');
DEFINE('_COM_A_BADWORDS_TITLE','Bad Words filter');
DEFINE('_COM_A_BADWORDS','Använd bad words filter');
DEFINE('_COM_A_BADWORDS_DESC','Sätt till &quot;Ja&quot; om du vill filtrera inlägg som innehåller de ord du definierat i Badword Component konfiguration. För att använda denna funktion måste Badword Component vara installerad!');
DEFINE('_COM_A_BADWORDS_NOTICE','* Detta meddelande har censurerats eftersom det innehöll ett eller flera ord som spärrats av administratör *');
DEFINE('_COM_A_COMBUILDER_PROFILE','Skapa Community Builder forumprofil');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
	'Klicka på länken för att skapa nödvändiga Forum fält i Community Builder användarprofiler. Efter att de har skapats kan de flyttas om så önskas genom Community Builder admin, men namn och alternativ får inte ändras. Om de tas bort från Community Builder admin kan de återskapas med denna länk, i annat fall ska länken inte klickas på flera gånger!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK','> Klicka här <');
DEFINE('_COM_A_COMBUILDER','Community Builder användarprofiler');
DEFINE('_COM_A_COMBUILDER_DESC',
	'Satt till &quot;Ja&quot; kommer aktivera integration med Community Builder komponenten (www.mambojoe.com). Alla Fireboards funktioner för användarprofiler kommer hanteras av Community Builder och profil länken i forum kommer länkas till Community Builder användarprofilen. Denna inställning kommer före myPMS Pro profil inställningen om båda har satts till &quot;Ja&quot;. Kontrollera att de ändringar som behövs i Community Builder databasen har genomförts genom att använda inställningen nedan.');
DEFINE('_COM_A_AVATAR_SRC','Använd avatarbild från');
DEFINE('_COM_A_AVATAR_SRC_DESC',
	'Om du har Clexus PM eller Community Builder komponenten installerad kan du konfigurera Fireboard att använda användares avatarbilder från myPMS Pro eller Community Builder användarprofiler.');
DEFINE('_COM_A_KARMA','Visa karmaindikator');
DEFINE('_COM_A_KARMA_DESC','Ställ till &quot;Ja&quot; för att visa karma och höj- och sänk-knapparna ifall statistik är aktiverat.');
DEFINE('_COM_A_DISEMOTICONS','Inaktivera emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC','Välj &quot;Ja&quot; för att helt inaktivera emoticons (smileys).');
DEFINE('_COM_C_KUNENACONFIG','Konfiguration');
DEFINE('_COM_C_KUNENACONFIGDESC','Konfigurera Fireboards alla funktioner');
DEFINE('_COM_C_FORUM','Forumadministration');
DEFINE('_COM_C_FORUMDESC','Lägg till kategorier/forum och konfigurera dem');
DEFINE('_COM_C_USER','Användaradministration');
DEFINE('_COM_C_USERDESC','Grundläggande användar- och användarprofiladministration');
DEFINE('_COM_C_FILES','Uppladdade filer');
DEFINE('_COM_C_FILESDESC','Bläddra bland och administrera uppladdade filer');
DEFINE('_COM_C_IMAGES','Uppladdade bilder');
DEFINE('_COM_C_IMAGESDESC','Bläddra bland och administrera uppladdade bilder');
DEFINE('_COM_C_CSS','Redigera CSS-fil');
DEFINE('_COM_C_CSSDESC','Ändra Fireboards utseende');
DEFINE('_COM_C_SUPPORT','Support-webbsida');
DEFINE('_COM_C_SUPPORTDESC','Koppla upp till Fireboards hemsida (nytt fönster)');
DEFINE('_COM_C_PRUNETAB','Rensa forum');
DEFINE('_COM_C_PRUNETABDESC','Radera gamla trådar (konfigurerbart)');
DEFINE('_COM_C_PRUNEUSERS','Rensa användare');
DEFINE('_COM_C_PRUNEUSERSDESC','Synka Fireboards användartabell med Joomlas användartabell');
DEFINE('_COM_C_LOADSAMPLE','Läs in exempeldata');
DEFINE('_COM_C_LOADSAMPLEDESC','För en enkel start: läs in exempeldata i en tom Fireboarddatabas');
DEFINE('_COM_C_REMOVESAMPLE', 'Ta bort exempeldata');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Ta bort exempeldata från databasen');
DEFINE('_COM_C_LOADMODPOS','Ladda modulpositioner');
DEFINE('_COM_C_LOADMODPOSDESC','Ladda modulpositioner för Fireboards template');
DEFINE('_COM_C_UPGRADEDESC','Uppdatera databasen till senaste versionen efter en uppgradering');
DEFINE('_COM_C_BACK','Tillbaka till Fireboards kontrollpanel');
DEFINE('_SHOW_LAST_SINCE','Aktiva ämnen sedan senaste besök den:');
DEFINE('_POST_SUCCESS_REQUEST2','Din förfrågan har behandlats');
DEFINE('_POST_NO_PUBACCESS3','Klicka här för registrera dig.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE','Inlägget raderat.');
DEFINE('_POST_SUCCESS_EDIT','Inlägget ändrat.');
DEFINE('_POST_SUCCESS_MOVE','Ämnet har flyttats.');
DEFINE('_POST_SUCCESS_POST','Ditt inlägg har postats.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Din prenumeration har behandlats.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA','Karma');
DEFINE('_KARMA_SMITE','Sänk karma');
DEFINE('_KARMA_APPLAUD','Höj karma');
DEFINE('_KARMA_BACK','För att återgå till ämnet,');
DEFINE('_KARMA_WAIT','Du kan bara ändra på en persons karma var 6:e timme. <br/>Du måste vänta tills den tiden gått.');
DEFINE('_KARMA_SELF_DECREASE','Du kan inte sänka din egen karma!');
DEFINE('_KARMA_SELF_INCREASE','Din karma har sänkts för att du försökt öka den själv!');
DEFINE('_KARMA_DECREASED','Användarens karma har sänkts. Om du inte kommer tillbaka till ämnet om en stund,');
DEFINE('_KARMA_INCREASED','Användarens karma har höjts. Om du inte kommer tillbaka till ämnet om en stund,');
DEFINE('_COM_A_TEMPLATE','Mall');
DEFINE('_COM_A_TEMPLATE_DESC','Välj en mall att använda.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH','Bildbibliotek');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC','Välj det bildbibliotek du vill använda.');
DEFINE('_PREVIEW_CLOSE','Stäng detta fönster');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR','Använd fält för inläggsstatistik');
DEFINE('_COM_A_POSTSTATSBAR_DESC','Välj &quot;Ja&quot; om du vill att antalet inlägg en användare skrivit ska visas grafiskt.');
DEFINE('_COM_A_POSTSTATSCOLOR','Färgnummer för statusfältet');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Ange numret på färgen du vill använda på grafiken för antalet postade inlägg');
DEFINE('_LATEST_REDIRECT',
	'Fireboard måste (åter)skapa din behörighet innan den kan skapa en lista över de senaste inläggen åt dig.\nOroa dig inte, detta är normalt efter 30 minuters inaktivitet eller efter att du loggat in på nytt.\nVänligen gör om din sökning.');
DEFINE('_SMILE_COLOUR','Färg');
DEFINE('_SMILE_SIZE','Storlek');
DEFINE('_COLOUR_DEFAULT','Standard');
DEFINE('_COLOUR_RED','Röd');
DEFINE('_COLOUR_PURPLE','Lila');
DEFINE('_COLOUR_BLUE','Blå');
DEFINE('_COLOUR_GREEN','Grön');
DEFINE('_COLOUR_YELLOW','Gul');
DEFINE('_COLOUR_ORANGE','Orange');
DEFINE('_COLOUR_DARKBLUE','Mörkblå');
DEFINE('_COLOUR_BROWN','Brun');
DEFINE('_COLOUR_GOLD','Guld');
DEFINE('_COLOUR_SILVER','Silver');
DEFINE('_SIZE_NORMAL','M');
DEFINE('_SIZE_SMALL','S');
DEFINE('_SIZE_VSMALL','XS');
DEFINE('_SIZE_BIG','L');
DEFINE('_SIZE_VBIG','XL');
DEFINE('_IMAGE_SELECT_FILE','Välj bild att bifoga');
DEFINE('_FILE_SELECT_FILE','Välj fil att bifoga');
DEFINE('_FILE_NOT_UPLOADED','Din fil har inte laddats upp. Försök skicka om eller redigera ditt inlägg');
DEFINE('_IMAGE_NOT_UPLOADED','Din bild har inte laddats upp. Försök skicka om eller redigera ditt inlägg');
DEFINE('_BBCODE_IMGPH','Sätt in [img] i inlägget för att visa bifogad bild');
DEFINE('_BBCODE_FILEPH','Sätt in [file] i inlägget för att visa bifogad fil');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','Markera den här rutan för att avsluta prenumereration på alla ämnen (inklusive osynliga ämnen avsedda för felsökning)');
DEFINE('_LINK_JS_REMOVED','<em>Aktiv länk som innehåller Javascript har tagits bort automatiskt</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS','Utseende');
DEFINE('_COM_A_USERS','Användarrelaterat');
DEFINE('_COM_A_LENGTHS','Längdinställningar');
DEFINE('_COM_A_SUBJECTLENGTH','Max. ämneslängd');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
	'Max längd på ämnesrad. Högsta värdet som stöds av databasen är 255 tecken. Om din site är konfigurerad för att använda multi-byteteckenuppsättningar som Unicode, UTF-8, non-ISO-8599-x gör högsta värdet lägre genom att använda följande formel:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Exempel för UTF-8, för vilken max teckenstorlek per tecken är 4 bytes: 255/4=63.');
DEFINE('_LATEST_THREADFORUM','Ämne/Forum');
DEFINE('_LATEST_NUMBER','Nya inlägg');
DEFINE('_COM_A_SHOWNEW','Visa nya inlägg');
DEFINE('_COM_A_SHOWNEW_DESC','Om du sätter detta på &quot;Ja&quot; visar Fireboard användare en indikator som talar om i vilka forum det finns nya inlägg sedan hans/hennes senaste besök.');
DEFINE('_COM_A_NEWCHAR','&quot;Nya inlägg&quot; indikator');
DEFINE('_COM_A_NEWCHAR_DESC','Välj hur nya inlägg ska visas (som ett &quot;!&quot; eller &quot;Nytt!&quot;)');
DEFINE('_LATEST_AUTHOR','Senaste inlägg av');
DEFINE('_GEN_FORUM_NEWPOST','Det finns nya inlägg sedan ditt senaste besök');
DEFINE('_GEN_FORUM_NOTNEW','Det finns inga nya inlägg sedan ditt senaste besök');
DEFINE('_GEN_UNREAD','Oläst inlägg');
DEFINE('_GEN_NOUNREAD','Läst inlägg');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Markera alla forum som lästa');
DEFINE('_GEN_MARK_THIS_FORUM_READ','Markera detta forum som läst');
DEFINE('_GEN_FORUM_MARKED','Alla inlägg i detta forum har markerats som läst');
DEFINE('_GEN_ALL_MARKED','Alla inlägg har markerats som lästa');
DEFINE('_IMAGE_UPLOAD','Ladda upp bild');
DEFINE('_IMAGE_DIMENSIONS','Din bild kan vara max (bredd x höjd - storlek)');
DEFINE('_IMAGE_ERROR_TYPE','Använd endast jpeg, gif eller png bilder');
DEFINE('_IMAGE_ERROR_EMPTY','Välj en fil innan du laddar upp');
DEFINE('_IMAGE_ERROR_SIZE','Bilden överskrider den tillåtna filstorleken.');
DEFINE('_IMAGE_ERROR_WIDTH','Bilden överskrider den tillåtna bredden.');
DEFINE('_IMAGE_ERROR_HEIGHT','Bilden överskrider den tillåtna höjden.');
DEFINE('_IMAGE_UPLOADED','Din bild har laddats upp.');
DEFINE('_COM_A_IMAGE','Bilder');
DEFINE('_COM_A_IMGHEIGHT','Max. bildhöjd');
DEFINE('_COM_A_IMGWIDTH','Max. bildbredd');
DEFINE('_COM_A_IMGSIZE','Max. filstorlek på bilden<br/><em>i kilobyte</em>');
DEFINE('_COM_A_IMAGEUPLOAD','Tillåt uppladdning av bilder');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Välj &quot;Ja&quot; om du vill att alla ska kunna ladda upp en bild.');
DEFINE('_COM_A_IMAGEREGUPLOAD','Tillåt uppladdning');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Välj &quot;Ja&quot; om du vill att registrerade och inloggade användare ska kunna ladda upp bilder.<br/> (Super)administratörer och moderatorer kan alltid ladda upp bilder.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS','Uppladdning');
DEFINE('_FILE_TYPES','Din fil kan vara av typen - max. filstorlek');
DEFINE('_FILE_ERROR_TYPE','Du får bara ladda upp filer av typen:\n');
DEFINE('_FILE_ERROR_EMPTY','Välj en fil innan du försöker ladda upp');
DEFINE('_FILE_ERROR_SIZE','Filstorleken överskrider maxgränsen.');
DEFINE('_COM_A_FILE','Filer');
DEFINE('_COM_A_FILEALLOWEDTYPES','Tillåtna filtyper');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Ange vilka filtyper som är tillåtna här. Använd en kommaseparerad lista utan mellanslag.<br />Exampel: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Maxs filstorlek i <br/><em>in kilobyte</em>');
DEFINE('_COM_A_FILEUPLOAD','Tillåt filuppladning');
DEFINE('_COM_A_FILEUPLOAD_DESC','Välj &quot;Ja&quot; om du vill att alla ska kunna ladda upp filer.');
DEFINE('_COM_A_FILEREGUPLOAD','Tillåt uppladdning för registrerade');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Välj &quot;Ja&quot; om du vill att registrerade och inloggade användare ska kunna ladda upp filer.<br/> (Super)administratörer och moderatorer kan alltid ladda upp filer.');
DEFINE('_SUBMIT_CANCEL','Ditt inlägg har inte skickats');
DEFINE('_HELP_SUBMIT','Klicka här för att skicka in ditt inlägg');
DEFINE('_HELP_PREVIEW','Klicka här för att se hur ditt inlägg kommer att se ut när du skickat in det');
DEFINE('_HELP_CANCEL','Klicka här för att avbryta ditt inlägg');
DEFINE('_POST_DELETE_ATT','Om det här är markerat, kommer alla bilder och filer i inlägget också tas bort (rekommenderas).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP','Visa ändrat');
DEFINE('_COM_A_USER_MARKUP_DESC','Välj &quot;Ja&quot; om du vill att ändrade inlägg ska ha en text som visar när och av vem ett inlägg har ändrats.');
DEFINE('_EDIT_BY','Inlägg ändrat av ');
DEFINE('_EDIT_AT','den ');
DEFINE('_UPLOAD_ERROR_GENERAL','Ett fel uppstod vid uppladning av din avatar. Prova igen eller meddela systemadministratören');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Bläddrare för uppladdade bilder');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Bläddrare för uppladdade filer');
DEFINE('_COM_A_IMGB_TOTAL_IMG','Antal uppladdade bilder');
DEFINE('_COM_A_IMGB_TOTAL_FILES','Antal uppladdade filer');
DEFINE('_COM_A_IMGB_ENLARGE','Klicka på bilden för att se bilden i full storlek');
DEFINE('_COM_A_IMGB_DOWNLOAD','Klicka på filen för att ladda ned den.');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
	'Alternativet &quot;Ersätt med dummy&quot; kommer ersätta bilden med en dummybild.<br /> Detta tillåter dig att ta bort en bild utan att förstöra inlägget.<br /><small><em>Ibland måste en explicit omladdning av sidan göras för att dummybilden ska synas.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY','Nuvarande dummybild');
DEFINE('_COM_A_IMGB_REPLACE','Ersätt med dummy');
DEFINE('_COM_A_IMGB_REMOVE','Ta bort helt');
DEFINE('_COM_A_IMGB_NAME','Namn');
DEFINE('_COM_A_IMGB_SIZE','Storlek');
DEFINE('_COM_A_IMGB_DIMS','Dimensioner');
DEFINE('_COM_A_IMGB_CONFIRM','Är du helt säker på att du vill radera den här filen? \n Radering av filen kommer att skapa en felaktig länk i ett inlägg...');
DEFINE('_COM_A_IMGB_VIEW','÷ppna inlägg (för redigering)');
DEFINE('_COM_A_IMGB_NO_POST','Inget inlägg är knutet!');
DEFINE('_USER_CHANGE_VIEW','Ändringar i dessa inställningar kommer att ske nästa gång du besöker forumet.<br /> Om du vill ändra din vy &quot;direkt&quot; kan du välja alternativen i forumets meny.');
DEFINE('_MOSBOT_DISCUSS_A','Diskutera den här artikeln på forumet. (');
DEFINE('_MOSBOT_DISCUSS_B',' inlägg)');
DEFINE('_POST_DISCUSS','Den här tråden diskuterar artikeln');
DEFINE('_COM_A_RSS','Aktivera RSS-flöde');
DEFINE('_COM_A_RSS_DESC','RSS-flödet tillåter användare att ladda ner de senaste inläggen till sitt skrivbord/RSS läsarprogram (se <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> för ett exempel.');
DEFINE('_LISTCAT_RSS','få de senaste inläggen direkt till ditt skrivbord');
DEFINE('_SEARCH_REDIRECT','Fireboard måste (åter)aktivera dina rättigheter innan den kan utföra din sökning. \nOroa dig inte, detta är normalt efter 30 minuters inaktivitet.\nVänligen gör om din sökning.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG','Inställningar');
DEFINE('_COM_A_DISPLAY','Visa #');
DEFINE('_COM_A_CURRENT_SETTINGS','Aktuell inställning');
DEFINE('_COM_A_EXPLANATION','Förklaring');
DEFINE('_COM_A_BOARD_TITLE','Forumets namn');
DEFINE('_COM_A_BOARD_TITLE_DESC','Namnet på ditt forum');
DEFINE('_COM_A_VIEW_TYPE','Standard vytyp');
DEFINE('_COM_A_VIEW_TYPE_DESC','Välj mellan enkel och trådad vy.');
DEFINE('_COM_A_THREADS','Trådar per sida');
DEFINE('_COM_A_THREADS_DESC','Antal trådar som visas per sida.');
DEFINE('_COM_A_REGISTERED_ONLY','Bara registrerade användare');
DEFINE('_COM_A_REG_ONLY_DESC','Välj &quot;Ja&quot; för att bara tillåta registrerade användare på forumet (visa & posta), Välj &quot;Nej&quot; för att tillåta alla i forumet');
DEFINE('_COM_A_PUBWRITE','Alla kan läsa/skriva');
DEFINE('_COM_A_PUBWRITE_DESC','Välj &quot;Ja&quot; för att alla ska kunna skriva, Välj &quot;Nej&quot; för att alla ska kunna läsa, men bara registrerade användare skriva inlägg');
DEFINE('_COM_A_USER_EDIT','Användarändringar');
DEFINE('_COM_A_USER_EDIT_DESC','Välj &quot;Ja&quot; för att registrerade användare ska kunna ändra sina inlägg.');
DEFINE('_COM_A_MESSAGE','För att spara ändringarna, klicka på &quot;Save&quot; knappen längst upp.');
DEFINE('_COM_A_HISTORY','Visa historia');
DEFINE('_COM_A_HISTORY_DESC','Välj &quot;Ja&quot; om du vill visa historia för inlägget efter ett svar.');
DEFINE('_COM_A_SUBSCRIPTIONS','Tillåt prenumeration');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','Välj &quot;Ja&quot; om du vill att registrerade användare ska kunna prenumerera på inlägget och få en e-post vid nya inlägg');
DEFINE('_COM_A_HISTLIM','Begränsa historia');
DEFINE('_COM_A_HISTLIM_DESC','Max historia av poster per inlägg.');
DEFINE('_COM_A_FLOOD','Floodskydd');
DEFINE('_COM_A_FLOOD_DESC','Antal sekunder en användare måste vänta innan han kan göra ett nytt inlägg. Sätt till 0 (noll) för att inaktivera. OBS: Floodskyddet <em>Kan</em> minska prestanda..');
DEFINE('_COM_A_MODERATION','E-posta moderatorer');
DEFINE('_COM_A_MODERATION_DESC',
	'Välj &quot;Ja&quot; om du vill ha e-post skickat till forum moderator(er) vid nya inlägg. Obs: Även om alla (super)administratörer automatiskt har alla moderatorprivilegier bör du tilldela dem moderatorstatus på forumet för att även de ska få e-post.');
DEFINE('_COM_A_SHOWMAIL','Visa E-post');
DEFINE('_COM_A_SHOWMAIL_DESC','Välj &quot;Nej&quot; om du aldrig vill visa användarnas e-post; gäller även registrerade användare.');
DEFINE('_COM_A_AVATAR','Tillåt avatarer');
DEFINE('_COM_A_AVATAR_DESC','Välj &quot;Ja&quot; om du vill tillåta avatarer (väljs sedan från profil inställningen)');
DEFINE('_COM_A_AVHEIGHT','Max. höjd för avatar');
DEFINE('_COM_A_AVWIDTH','Max. bredd för avatar');
DEFINE('_COM_A_AVSIZE','Max. filstorlek för avatar<br/><em>i kilobyte</em>');
DEFINE('_COM_A_USERSTATS','Visa användarstatistik');
DEFINE('_COM_A_USERSTATS_DESC','Välj &quot;Ja&quot; för att visa statistik som antal inlägg av användaren, användartyp (Admin, Moderator, User, etc.).');
DEFINE('_COM_A_AVATARUPLOAD','Tillåt uppladdning av avatar');
DEFINE('_COM_A_AVATARUPLOAD_DESC','Välj &quot;Ja&quot; om du vill tillåta användare att ladda upp egna avatarer');
DEFINE('_COM_A_AVATARGALLERY','Avatar-galleri');
DEFINE('_COM_A_AVATARGALLERY_DESC','Välj &quot;Ja&quot; om du vill låta användarna välja avatar från ett galleri ( (components/com_Fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','Sätt till &quot;Ja&quot; om du vill visa registrerade användares rankning baserat på antalet inlägg de har gjort.<br/><strong>Observera att du måste aktivera &quot;Visa användarstatistik&quot;  i fliken &quot;Front&quot; för att visa rankningen.</strong>');
DEFINE('_COM_A_RANKINGIMAGES','Använd rankningsbilder');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
	'Sätt till &quot;Ja&quot; om du vill visa registrerade användares rankning  med en bild (från components/com_fireboard/ranks). Om du stänger av detta kommer istället text att visas för rankning. Läs dokumentationen på www.bestofjoomla.com för mer information om rankningsbilder');

//email and stuff
$_COM_A_NOTIFICATION ="Nytt inlägg av ";
$_COM_A_NOTIFICATION1="Ett nytt inlägg har skrivits i ett ämne du prenumererar på, ";
$_COM_A_NOTIFICATION2="Du kan administrera dina prenumerationer genom att klicka på 'min profil' länken på forumsidan efter att du loggat in på siten. Från forumet kan du även sluta prenumerera på ämnet.";
$_COM_A_NOTIFICATION3="Svara inte på det här meddelandet då det är automatiskt genererat.";
$_COM_A_NOT_MOD1="Ett nytt inlägg har skrivits i ett forum där du är moderator.";
$_COM_A_NOT_MOD2="Var vänlig kontrollera det när du loggat in på webbplatsen.";
DEFINE('_COM_A_NO','Nej');
DEFINE('_COM_A_YES','Ja');
DEFINE('_COM_A_FLAT','Enkel');
DEFINE('_COM_A_THREADED','Trådad');
DEFINE('_COM_A_MESSAGES','Inlägg per sida');
DEFINE('_COM_A_MESSAGES_DESC','Antal meddelanden som visas per sida.');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME','Användarnamn');
DEFINE('_COM_A_USERNAME_DESC','Välj &quot;Ja&quot; om du vill att användarnamnet ska visas på inläggen istället för deras riktiga namn');
DEFINE('_COM_A_CHANGENAME','Tillåt namnbyte');
DEFINE('_COM_A_CHANGENAME_DESC','Välj &quot;Ja&quot; om du vill att registrerade användare ska kunna byta sitt namn när de postar inlägg. Om du väljer &quot;Nej&quot; kommer registrerade användare inte tillåtas ändra sitt namn');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE','Forumet är offline');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','Välj &quot;Ja&quot; om du vill göra forumet offline. (Super)administratörer kan fortfarande bläddra i forumet.');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Forumets offlinemeddelande');
DEFINE('_COM_A_PRUNE','Rensa forum');
DEFINE('_COM_A_PRUNE_NAME','Forum att rensa:');
DEFINE('_COM_A_PRUNE_DESC',
	'Funktionen rensa forum tillåter dig att rensa bort inlägg det inte skrivits några inlägg i på ett definierat antal dagar. Det tar INTE bort inlägg som är klistrade eller låsta. Dessa måste tas bort manuellt. Trådar i låsta forum kan inte rensas.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Rensa trådar utan nya inlägg på ');
DEFINE('_COM_A_PRUNE_DAYS','dag(ar)');
DEFINE('_COM_A_PRUNE_USERS','Rensa användare');
DEFINE('_COM_A_PRUNE_USERS_DESC',
	'Den här funktionen låter dig rensa din Fireboard användarlista jämfört mot din Mambo användarlista. Den kommer att ta bort de användare som inte har en motsvariget på siten. <br/> När du är säker på att du vill fortsätta klickar du på &quot;Börja Rensa&quot;knappen.');
//general
DEFINE('_GEN_ACTION','Aktivitet');
DEFINE('_GEN_AUTHOR','Skribent');
DEFINE('_GEN_BY','av');
DEFINE('_GEN_CANCEL','Avbryt');
DEFINE('_GEN_CONTINUE','Skicka');
DEFINE('_GEN_DATE','Datum');
DEFINE('_GEN_DELETE','Radera');
DEFINE('_GEN_EDIT','Ändra');
DEFINE('_GEN_EMAIL','E-post');
DEFINE('_GEN_EMOTICONS','Emoticons');
DEFINE('_GEN_FLAT','Enkel');
DEFINE('_GEN_FLAT_VIEW','Enkel vy');
DEFINE('_GEN_FORUMLIST','Forumlista');
DEFINE('_GEN_FORUM','Forum');
DEFINE('_GEN_HELP','Hjälp');
DEFINE('_GEN_HITS','Visningar');
DEFINE('_GEN_LAST_POST','Senaste inlägg');
DEFINE('_GEN_LATEST_POSTS','Senaste inläggen');
DEFINE('_GEN_LOCK','Lås');
DEFINE('_GEN_UNLOCK','Lås upp');
DEFINE('_GEN_LOCKED_FORUM','betyder att forumet är låst; inga nya inlägg är möjliga.');
DEFINE('_GEN_LOCKED_TOPIC','betyder att ämnet är låst; inga nya inlägg är möjliga.');
DEFINE('_GEN_MESSAGE','Inlägg');
DEFINE('_GEN_MODERATED','betyder att forumet är modererat; nya inlägg utvärderas innan de publiceras.');
DEFINE('_GEN_MODERATORS','Moderatorer');
DEFINE('_GEN_MOVE','Flytta');
DEFINE('_GEN_NAME','Namn');
DEFINE('_GEN_POST_NEW_TOPIC','Nytt ämne');
DEFINE('_GEN_POST_REPLY','Svara på det här inlägget');
DEFINE('_GEN_MYPROFILE','Min profil');
DEFINE('_GEN_QUOTE','Citera');
DEFINE('_GEN_REPLY','Svara');
DEFINE('_GEN_REPLIES','Svar');
DEFINE('_GEN_THREADED','Trådad');
DEFINE('_GEN_THREADED_VIEW','Trådad vy');
DEFINE('_GEN_SIGNATURE','Signatur');
DEFINE('_GEN_ISSTICKY','Betyder att tråden är klistrad.');
DEFINE('_GEN_STICKY','Klistrad');
DEFINE('_GEN_UNSTICKY','Ej klistrad');
DEFINE('_GEN_SUBJECT','Ämne');
DEFINE('_GEN_SUBMIT','Skicka');
DEFINE('_GEN_TOPIC','Ämne');
DEFINE('_GEN_TOPICS','Ämnen');
DEFINE('_GEN_TOPIC_ICON','Ämnesikon');
DEFINE('_GEN_SEARCH_BOX','Sök i forumet');
$_GEN_THREADED_VIEW="Trådad vy";
$_GEN_FLAT_VIEW    ="Enkel vy";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD','Ladda upp');
DEFINE('_UPLOAD_DIMENSIONS','Din bild kan vara högst (bredd x höjd - storlek)');
DEFINE('_UPLOAD_SUBMIT','Skicka in en ny avatar för uppladdning');
DEFINE('_UPLOAD_SELECT_FILE','Välj fil');
DEFINE('_UPLOAD_ERROR_TYPE','Endast jpeg, gif eller png-bilder');
DEFINE('_UPLOAD_ERROR_EMPTY','Välj en fil innan du laddar upp');
DEFINE('_UPLOAD_ERROR_NAME','Filnamnet kan bara innehålla siffror och bokstäver, inga svenska tecken eller mellanslag.');
DEFINE('_UPLOAD_ERROR_SIZE','Bilden överskrider den tillåtna filstorleken.');
DEFINE('_UPLOAD_ERROR_WIDTH','Bilden överskrider den tillåtna bredden.');
DEFINE('_UPLOAD_ERROR_HEIGHT','Bilden överskrider den tillåtna höjden.');
DEFINE('_UPLOAD_ERROR_CHOOSE',"Du valde ingen avatar från galleriet..");
DEFINE('_UPLOAD_UPLOADED','Din avatar har laddats upp.');
DEFINE('_UPLOAD_GALLERY','Välj en ur avatargalleriet:');
DEFINE('_UPLOAD_CHOOSE','Bekräfta ditt val.');
// listcat.php
DEFINE('_LISTCAT_ADMIN','En administratör måste skapa dem först från ');
DEFINE('_LISTCAT_DO','De kommer att veta vad de behöver göra ');
DEFINE('_LISTCAT_INFORM','Informera dem och be dem skynda sig!!');
DEFINE('_LISTCAT_NO_CATS','Det finns inga kategorier i forumet än.');
DEFINE('_LISTCAT_PANEL','Administrationspanelen i Joomla! CMS.');
DEFINE('_LISTCAT_PENDING','väntande meddelande(n)');
// moderation.php
DEFINE('_MODERATION_MESSAGES','Det finns inga väntande inlägg i detta forum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE','Du kommer att radera inlägget');
DEFINE('_POST_ABOUT_DELETE','<strong>OBS:</strong><br/>
-om du raderar ett forumämne (det första inlägget i en tråd) kommer alla svar att raderas också!
..÷verväg att endast ta bort innehållet i inlägget och användarnamnet på den som postat det ifall endast innehållet ska raderas..');
DEFINE('_POST_CLICK','klicka här');
DEFINE('_POST_ERROR','Kunde inte hitta användare/e-post. Allvarligt databasfel inte listat');
DEFINE('_POST_ERROR_MESSAGE','Ett okänt SQLfel uppstod och ditt inlägg postades inte. Om felet upprepar sig, kontakta systemadministratören.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Ett fel uppstod och ditt inlägg uppdaterades inte. Om felet upprepar sig, kontakta systemadministratören.');
DEFINE('_POST_ERROR_TOPIC','Ett fel uppstod under raderingen. Se felmeddelandet nedan:');
DEFINE('_POST_FORGOT_NAME','Du glömde inkludera ditt namn. Klicka på tillbaka knappen i din webbläsare och försök igen.');
DEFINE('_POST_FORGOT_SUBJECT','Du glömde ange ett ämne. Klicka på tillbaka knappen i din webbläsare och försök igen.');
DEFINE('_POST_FORGOT_MESSAGE','Du glömde skriva ditt inlägg. Klicka på tillbaka knappen i din webbläsare och försök igen.');
DEFINE('_POST_INVALID','Ett felaktigt inlägg anropades.');
DEFINE('_POST_LOCK_SET','Det valda ämnet är låst.');
DEFINE('_POST_LOCK_NOT_SET','Ämnet kunde inte låsas.');
DEFINE('_POST_LOCK_UNSET','Ämnet har låsts upp.');
DEFINE('_POST_LOCK_NOT_UNSET','Ämnet kunde inte låsas upp.');
DEFINE('_POST_MESSAGE','Skriv nytt inlägg i ');
DEFINE('_POST_MOVE_TOPIC','Flytta det här inlägget till forum');
DEFINE('_POST_NEW','Skriv nytt inlägg i: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','Din prenumeration på det här ämnet kunde inte behandlas.');
DEFINE('_POST_NOTIFIED','Markera kryssrutan om du vill ha meddelande om svar i detta ämne.');
DEFINE('_POST_STICKY_SET','Ämnet är klistrat.');
DEFINE('_POST_STICKY_NOT_SET','Ämnet kunde inte klistras.');
DEFINE('_POST_STICKY_UNSET','Ämnet är inte klistrat.');
DEFINE('_POST_STICKY_NOT_UNSET','Ämnet kunde inte avklistras.');
DEFINE('_POST_SUBSCRIBE','prenumerera');
DEFINE('_POST_SUBSCRIBED_TOPIC','Du prenumererar på det här ämnet.');
DEFINE('_POST_SUCCESS','Ditt inlägg har');
DEFINE('_POST_SUCCES_REVIEW','Ditt inlägg har postats. Det kommer att ses över av en moderator innan det publiceras på forumet.');
DEFINE('_POST_SUCCESS_REQUEST','Din begäran har behandlats. Om du inte tas tillbaka till forumet inom kort,');
DEFINE('_POST_TOPIC_HISTORY','Ämneshistoria för');
DEFINE('_POST_TOPIC_HISTORY_MAX','Max. visning at de senaste');
DEFINE('_POST_TOPIC_HISTORY_LAST','inläggen - <i>(Senaste inläggen först)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','Ditt ämne kunde inte flyttas, för att tas tillbaka till ämnet:');
DEFINE('_POST_TOPIC_FLOOD1','Forumet är skyddat mot upprepade inlägg. Du måste vänta minst ');
DEFINE('_POST_TOPIC_FLOOD2',' sekunder innan du kan posta ett nytt inlägg.');
DEFINE('_POST_TOPIC_FLOOD3','Klicka på tillbakaknappen i din webbläsare för att återgå till forumet.');
DEFINE('_POST_EMAIL_NEVER','din e-postadress kommer aldrig att visas på denna site.');
DEFINE('_POST_EMAIL_REGISTERED','din e-postadress kommer endast visas för registrerade användare.');
DEFINE('_POST_LOCKED','låst av administratören.');
DEFINE('_POST_NO_NEW','Nya svar är inte tillåtna.');
DEFINE('_POST_NO_PUBACCESS1','Administratören har stängt av anonyma inlägg.');
DEFINE('_POST_NO_PUBACCESS2','Du får läsa i forumet, men endast registrerade användare får posta.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','Det finns inga ämnen i det här forumet ännu');
DEFINE('_SHOWCAT_PENDING','Väntande meddelande(n)');
// userprofile.php
DEFINE('_USER_DELETE',' Markera den här kryssrutan för att radera din signatur');
DEFINE('_USER_ERROR_A','Du kom till den här sidan av misstag. Informera administratör genom vilken länkar ');
DEFINE('_USER_ERROR_B','du klickade på som fick dig att komma hit.');
DEFINE('_USER_ERROR_C','Tack!');
DEFINE('_USER_ERROR_D','Felnummer att inkludera i din rapport: ');
DEFINE('_USER_GENERAL','Generell profilinformation');
DEFINE('_USER_MODERATOR','Du är moderator på forumen');
DEFINE('_USER_MODERATOR_NONE','Inga forum har tilldelats dig');
DEFINE('_USER_MODERATOR_ADMIN','Administratörer är moderator på samtliga forum.');
DEFINE('_USER_NOSUBSCRIPTIONS','Inga prenumerationer har hittats');
DEFINE('_USER_PREFERED','Föredragen vytyp');
DEFINE('_USER_PROFILE','Profil för ');
DEFINE('_USER_PROFILE_NOT_A','Din profil kunde ');
DEFINE('_USER_PROFILE_NOT_B','inte');
DEFINE('_USER_PROFILE_NOT_C',' bli uppdaterad.');
DEFINE('_USER_PROFILE_UPDATED','Din profil har uppdaterats.');
DEFINE('_USER_RETURN_A','Om du inte tas tillbaka till din profil inom kort ');
DEFINE('_USER_RETURN_B','klicka här');
DEFINE('_USER_SUBSCRIPTIONS','Dina prenumerationer');
DEFINE('_USER_UNSUBSCRIBE','avbryt prenumeration');
DEFINE('_USER_UNSUBSCRIBE_A','Din prenumeration ');
DEFINE('_USER_UNSUBSCRIBE_B','på');
DEFINE('_USER_UNSUBSCRIBE_C','detta ämne kunde inte avbrytas.');
DEFINE('_USER_UNSUBSCRIBE_YES','Du har avslutat din prenumeration på ämnet.');
DEFINE('_USER_DELETEAV',' Markera den här rutan för att radera den avatar');
//New 0.9 to 1.0
DEFINE('_USER_ORDER','Vald ordning på inlägg');
DEFINE('_USER_ORDER_DESC','Senaste inläggen först');
DEFINE('_USER_ORDER_ASC','Första inläggen först');
// view.php
DEFINE('_VIEW_DISABLED','Logga in för att svara.');
DEFINE('_VIEW_POSTED','Skrivet av');
DEFINE('_VIEW_SUBSCRIBE','Prenumerera på detta ämne');
DEFINE('_MODERATION_INVALID_ID','Ett ogiltigt inläggsID begärt.');
DEFINE('_VIEW_NO_POSTS','Det finns inga inlägg i detta forum.');
DEFINE('_VIEW_VISITOR','Besökare');
DEFINE('_VIEW_ADMIN','Admin');
DEFINE('_VIEW_USER','Medlem');
DEFINE('_VIEW_MODERATOR','Moderator');
DEFINE('_VIEW_REPLY','Svara på det här inlägget');
DEFINE('_VIEW_EDIT','Redigera det här inlägget');
DEFINE('_VIEW_QUOTE','Citera det här inlägget i ett nytt inlägg');
DEFINE('_VIEW_DELETE','Radera det här inlägget');
DEFINE('_VIEW_STICKY','Klistra ämnet');
DEFINE('_VIEW_UNSTICKY','Ta bort klister från ämnet');
DEFINE('_VIEW_LOCK','Lås ämnet');
DEFINE('_VIEW_UNLOCK','Lås upp ämnet');
DEFINE('_VIEW_MOVE','Flytta ämnet till ett nytt forum');
DEFINE('_VIEW_SUBSCRIBETXT','Prenumerera på ämnet och få meddelande per e-post när nya inlägg skrivs');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-Fireboard 9.2
DEFINE('_HOME','Forum');
DEFINE('_POSTS','Inlägg:');
DEFINE('_TOPIC_NOT_ALLOWED','Inlägg');
DEFINE('_FORUM_NOT_ALLOWED','Forum');
DEFINE('_FORUM_IS_OFFLINE','Forumet är stängt!');
DEFINE('_PAGE','Sida: ');
DEFINE('_NO_POSTS','Inga inlägg');
DEFINE('_CHARS','Max antal tecken');
DEFINE('_HTML_YES','HTML är aktiverat');
DEFINE('_YOUR_AVATAR','<b>Din avatar</b>');
DEFINE('_NON_SELECTED','Inte vald ännu <br>');
DEFINE('_SET_NEW_AVATAR','Välj ny avatar');
DEFINE('_THREAD_UNSUBSCRIBE','Avsluta prenumeration');
DEFINE('_SHOW_LAST_POSTS','Senast aktiva ämnen');
DEFINE('_SHOW_HOURS','timmar');
DEFINE('_SHOW_POSTS','Totalt: ');
DEFINE('_DESCRIPTION_POSTS','Nyaste inlägget för det aktiva ämnena visas');
DEFINE('_SHOW_4_HOURS','4 timmar');
DEFINE('_SHOW_8_HOURS','8 timmar');
DEFINE('_SHOW_12_HOURS','12 timmar');
DEFINE('_SHOW_24_HOURS','24 timmar');
DEFINE('_SHOW_48_HOURS','48 timmar');
DEFINE('_SHOW_WEEK','Vecka');
DEFINE('_POSTED_AT','Postat');
DEFINE('_DATETIME','Y-m-d H:i');
DEFINE('_NO_TIMEFRAME_POSTS','Det finns inga inlägg under den tid du valt.');
DEFINE('_MESSAGE','Inlägg');
DEFINE('_NO_SMILIE','nej');
DEFINE('_FORUM_UNAUTHORIZIED','Forumet är endast öppet för registrerade användare.');
DEFINE('_FORUM_UNAUTHORIZIED2','Om du redan är registrerad användare måste du logga in först.');
DEFINE('_MESSAGE_ADMINISTRATION','Moderation');
DEFINE('_MOD_APPROVE','Godkänn');
DEFINE('_MOD_DELETE','Radera');
//NEW in RC1
DEFINE('_SHOW_LAST','Visa senaste inlägg');
DEFINE('_POST_WROTE','skrev');
DEFINE('_COM_A_EMAIL','Forumets e-postadress');
DEFINE('_COM_A_EMAIL_DESC','Det här är forumets e-postadress. Skriv in en giltig e-postadress.');
DEFINE('_COM_A_WRAP','Bryt ord längre än');
DEFINE('_COM_A_WRAP_DESC',
	'Skriv in det högsta antal tecken ett enskilt ord får ha. Den här funktionen låter dig anpassa utskriften av foruminlägg efter din sitemall.<br/> 70 tecken är det högsta rekommenderade antalet tecken för en mall med fast sidbredd, men du kanske måste experimentera med värdet litegrann.<br/>URLer, oavsett hur långa de är, påverkas INTE av ordbrytning.');
DEFINE('_COM_A_SIGNATURE','Max. signaturlängd');
DEFINE('_COM_A_SIGNATURE_DESC','Max antal tecken i signaturer.');
DEFINE('_SHOWCAT_NOPENDING','Inga väntande inlägg');
DEFINE('_COM_A_BOARD_OFSET','Forumets tidskillnad');
DEFINE('_COM_A_BOARD_OFSET_DESC','Vissa forum finns i en annan tidzon än användarna är. Välj tidskillnad Fireboard ska använda när den visar tidpunkter för skapande av inlägg. Positiva och negativa värden kan anges.');
//New in RC2
DEFINE('_COM_A_BASICS','Generellt');
DEFINE('_COM_A_FRONTEND','Front');
DEFINE('_COM_A_SECURITY','Säkerhet');
DEFINE('_COM_A_AVATARS','Avatarer');
DEFINE('_COM_A_INTEGRATION','Integration');
DEFINE('_COM_A_PMS','myPMS2 privata meddelanden');
DEFINE('_COM_A_PMS_DESC',
	'Välj &quot;Ja&quot; om du har myPMS2 installerat och vill göra det möjligt för registrerade användare att skicka privata meddelanden till varandra');
DEFINE('_VIEW_PMS','Klicka här för att skicka ett PM till användaren');
//new in RC3
DEFINE('_POST_RE','Sv:');
DEFINE('_BBCODE_BOLD','Fet text: [b]text[/b] ');
DEFINE('_BBCODE_ITALIC','Kursiv text: [i]text[/i]');
DEFINE('_BBCODE_UNDERL','Understruken text: [u]text[/u]');
DEFINE('_BBCODE_QUOTE','Citera text: [quote]text[/quote]');
DEFINE('_BBCODE_CODE','Visa kod: [code]code[/code]');
DEFINE('_BBCODE_ULIST','Onumrerad lista: [ul] [li]text[/li] [/ul] - Tips: En lista måste innehålla listobjekt');
DEFINE('_BBCODE_OLIST','Numrerad lista: [ol] [li]text[/li] [/ol] - Tips: En lista måste innehålla listobjekt');
DEFINE('_BBCODE_IMAGE','Bild: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK','Länk: [url]http://www.zzz.com/[/url] eller [url=http://www.zzz.com/]Det här är en länk[/url]');
DEFINE('_BBCODE_CLOSA','Stäng alla tags');
DEFINE('_BBCODE_CLOSE','Stäng alla öppna bbCode tags');
DEFINE('_BBCODE_COLOR','Färg: [color=#FF6600]text[/color]');
DEFINE('_BBCODE_SIZE','Storlek: [size=1]textstorlek[/size] - Tips: Storlekarna anges som värde från 1 till 5');
DEFINE('_BBCODE_LITEM','Listobjekt: [li] listobjekt [/li]');
DEFINE('_BBCODE_HINT','Forumkod hjälp - Tips: Forumkod kan användas på markerad text!');
DEFINE('_COM_A_TAWIDTH','Bredd på textarea');
DEFINE('_COM_A_TAWIDTH_DESC','Formattera bredden på textarean så den passar i webbplatsens mall. <br/>Storleken på ämne/emoticon fältet kommer bli 2 rader om bredden är <= 65');
DEFINE('_COM_A_TAHEIGHT','Storlek på textarea');
DEFINE('_COM_A_TAHEIGHT_DESC','Formattera höjden på textarean så den passar i din sitemall.');
DEFINE('_COM_A_ASK_EMAIL','Kräv e-post');
DEFINE('_COM_A_ASK_EMAIL_DESC','Vill du kräva en e-postadress när användare och besökare skriver inlägg? Sätt till &quot;Nej&quot; om du vill stänga av den här funktionen.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Administrera ranking');
define('_KUNENA_SORTRANKS', 'Sortera efter ranking');
define('_KUNENA_RANKSIMAGE', 'Rankingbild');
define('_KUNENA_RANKS', 'Rankingtitel');
define('_KUNENA_RANKS_SPECIAL', 'Special');
define('_KUNENA_RANKSMIN', 'Minsta antal inlägg');
define('_KUNENA_RANKS_ACTION', 'Aktiviteter');
define('_KUNENA_NEW_RANK', 'Ny rank');


?>
