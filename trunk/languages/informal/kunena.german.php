<?php
/**
* @version $Id:001(first v.) kunena.german.php Kunena 1.0.9 / informal  2009-04-04 23:46:00 Lintzy $
*
**
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
*
* 1. Translation German  Fireboard (first translations) Achim Raji http://info@filmanleitungen.de and Karin Nikolai info@german.de 
* 2. Translation German  Fireboard until 22/10/2008 by Jan Erik Zassenhaus and Karin Nikolai (J!German Team at www.jgerman.de - info@german.de)
* 3. Translation German Kunena v. 1.0.7b by Zeitgeist http://www.lemovo.de 
*
* Language: German (informal)
* Translation Kunena v. 1.0.8, v. 1.0.9 and v. 1.5.0 by Angelika Reisiger (Lintzy)
* @copyright (C) 2009 Lintzy / Angelika Reisiger / http://www.webdesign-welt.de/ All Rights Reserved
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Installierte Version');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'Lizenz');
DEFINE('_KUNENA_PROFILE_NO_USER', 'Benutzer existiert nicht.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'Der Benutzer hat das Forun noch nicht besucht und hat noch kein Profil.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Suchergebnisse');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Erweiterte Suche');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Suche nach Stichwort');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Stichw&ouml;rter');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Suche im kompletten Beitrag');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Nur in Titeln suchen');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Suche nach Benutzernamen');
DEFINE('_KUNENA_SEARCH_UNAME', 'Benutzername');
DEFINE('_KUNENA_SEARCH_EXACT', 'Exakter Name');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Beitrag gepostet von');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Thema gestartet von');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Aktivit&auml;t in Beitr&auml;gen');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Suchoptionen');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Finde Themen mit');
DEFINE('_KUNENA_SEARCH_LEAST', 'Mindestens');
DEFINE('_KUNENA_SEARCH_MOST', 'H&ouml;chstens');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Antworten');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Finde Beitr&auml;ge von');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Jedes Datum');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Letzter Besuch');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Gestern');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'Vor 1 Woche');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', 'Vor 2 Wochen');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'Vor 1 Monat');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', 'Vor 3 Monaten');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', 'Vor 6 Monaten');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'Vor 1 Jahr');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'Und neuer');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'Und a&uml;lter');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sortiere Ergebnisse nach');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Titel');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Anzahl von Beitra&uml;gen');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Anzahl von Hits');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Startdatum des Themas');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Datum des Beitrags');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'Benutzername');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Sortierung aufsteigend');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Sortierung absteigend');
DEFINE('_KUNENA_SEARCH_START', 'Gehe zu Ergebnis Nummer');
DEFINE('_KUNENA_SEARCH_LIMIT5', 'Zeige 5 Suchergebnisse');
DEFINE('_KUNENA_SEARCH_LIMIT10', 'Zeige 10 Suchergebnisse');
DEFINE('_KUNENA_SEARCH_LIMIT15', 'Zeige 15 Suchergebnisse');
DEFINE('_KUNENA_SEARCH_LIMIT20', 'Zeige 20 Suchergebnisse');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Suche in allen Foren');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'Alle Foren');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Unterforen in Suche einschlie&szlig;en');
DEFINE('_KUNENA_SEARCH_SEND', 'Absenden');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Abbrechen');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'Es wurden keine Suchergebnisse gefunden, die alle von Ihnen eingegebenen Suchbegriffe enthalten.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'Ein Suchbegriff muss mehr als 3 Zeichen enthalten!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'Du hast keine Moderatorenrechte');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Dieses Thema wurde <b>nicht</b> zu deinen Favoriten hinzuge&uuml;gt');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Synchronisiert die Kunena Datenbank mit der Joomla Userdatenbank');
DEFINE('_POST_FORGOT_EMAIL', 'Du hast vergessen, deine E-Mail-Adresse einzutragen. Bitte klick auf den Return-Button deines Browsers und versuche es noch einmal.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Es wurden nicht alle Anh&auml;nge gel&ouml;scht!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Hauptforum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Das ist das oberste Level. Eine Kategorie fungiert wie ein Container, der Foren und Unterforen enthält. Sie wird auch als Level 1 Kategore bezeichnet und ist ein Muss für jede Kunena Forum Einrichtung. Einem Forum ist immer etwas übergeordnet, entweder ein anderes Forum (Eltern- / Haupt-forum) oder eine Kategorie (Level 1 Kategorie) als Container.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'Um deinen Besuchern und Mitgliedern Extra-Informationen zukommen zu lassen kannst du im Kopf des Forums im obersten Bereich ausgewählter Kategorien Textnachrichten unterbringen.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Eingangsbereich');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Wir laden unsere neuen Mitglieder ein, eine kurze Vorstellung über sich selbst in dieses Forum zu posten. Lernt euch kennen und teilt eure gemeinsamen Interessen miteinander.');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Willkommen im Kunena Forum[/b]

Erzähl uns und unseren Mitgliedern wer du bist, was du magst und wieso ein Mitglied dieser Site geworden bist.

Wir begrüßen alle neuen Mitglieder und hoffen euch oft wiederzusehen!');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Ideen-Forum');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Hast du ein Feedback für uns oder einen Verbesserungsvorschlag?

Nur Mut! Hinterlasse uns eine Nachricht, denn wir sind stets bemüht unsere Site zu verbessern, um sie attraktiver für unsere Besucher und Mitglieder zu gestalten.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Das ist der optionale Foren Header für das Ideen-Forum.');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Willkommen zu Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Willkommen zu Kunena![/b][/size]

Danke, dass du Kunena als Dein Community Forum für dein Joomla! CMS gewählt hast.

Kunena, heißt übersetzt aus Suaheli: "Sprich";, und wird von einem Team aus Opensource Spezialisten entwickelt, das sich zum Ziel gesetzt hat, eine hochqualitative sowie eng an Joomla! angepasste L&ouml;sung zu bieten. Außerdem unterstützt Kunena  Social Networking Komponenten wie Community Builder und JomSocial.



[size=4][b]Additional Kunena Ressourcen[/b][/size]

[b]Kunena Dokumentation:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Support Forum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena Downloads:[/b] [url=http://joomlacode.org/gf/project/kunena/frs/]http://www.kunena.com/downloads[/url]
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Teil deine Wünsche und Ideen mit:[/b]  [url=http://kunena.uservoice.com/pages/general?referer_type=top3]http://www.kunena.com/uservoice[/url]
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Verfolge Kunena auf Twitter:[/b]  [url=https://twitter.com/kunena]http://www.kunena.com/twitter[/url]
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Code Hervorhebung erlauben');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Aktiviert das Kunena-JavaScript f&uuml;r die Code-Hervorhebung. Wenn deine Mitglieder PHP-Code oder vergleichbaren Code in ihrem Beitrag zwischen in Code-Tags setzen, wird er farblich hervorgehoben. Wenn dein Forum diese Funktion nicht ben&ouml;tigt, solltest du diese deakivieren, damit Code-Tags nicht zerst&ouml;rt werden.');
DEFINE('_COM_A_RSS_TYPE', 'Standard RSS-Typ');
DEFINE('_COM_A_RSS_TYPE_DESC', 'W&auml;hle zwischen RSS-Feeds eines Themas oder eines Beitrags. &quot;Nach Thema&quot; bedeutet, dass nur ein Eintrag pro Thema im RSS-Feed gelistet wird, unabh&auml;ngig davon, wie viele Beitr&auml;ge in diesem Thema geschrieben wurden. &quot;Nach Thema&quot; bedeutet somit, dass ein kleinerer RSS-Feed generiert wird, der nicht alle geschriebenen Antworten beinhalten wird.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Nach Thema');
DEFINE('_COM_A_RSS_BY_POST', 'Nach Beitrag');
DEFINE('_COM_A_RSS_HISTORY', 'RSS History');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'W&auml;hle aus, wie viele Beitr&auml;ge im RSS-Verlauf enthalten sein sollen. Standard ist 1 Monat, aber eventuell solltest du es auf 1 Woche beschr&auml;nken, wenn deine Website sehr ausgelastet ist.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Woche');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Monat');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Jahr');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Kunena Startseite');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'W&auml;hle die Kunena-Startseite aus, die beim Aufruf des Forumlinks angezeigt werden soll. Standard ist &quot;Aktuelle Beitr&auml;ge&quot;. Diese Einstellung sollte auf &quot;Kategorien&quot; ge&auml;ndert werden, wenn ein anderes Template als das &quot;default_ex&quot;-Template benutzt wird. Wenn &quot;Meine Diskussionen&quot; ausgew&auml;hlt ist, wird dem Gast die &Uuml;bersicht &quot;Aktuelle Beitr&auml;ge&quot; angezeigt.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Aktuell');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Eigene');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategorien');
DEFINE('_KUNENA_BBCODE_HIDE', 'Der folgende Inhalt wird vor Besuchern versteckt:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Achtung: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Das &uuml;bergeordnete Forum darf nicht dasselbe sein!');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Das &uuml;bergeordnete Forum ist sich selbst als Unterforum zugeordnet');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Foren-ID existiert nicht!');
DEFINE('_KUNENA_RECURSION', 'Rekursion entdeckt.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Du hast vergessen deinen Namen einzugeben.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Du hast vergessen deine E-Mail Adresse einzugeben.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Du hast vergessen einen Betreff anzugeben.');
DEFINE('_KUNENA_EDIT_TITLE', 'Registrierdaten bearbeiten');
DEFINE('_KUNENA_YOUR_NAME', 'Dein Name:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'Username:');
DEFINE('_KUNENA_PASS', 'Passwort:');
DEFINE('_KUNENA_VPASS', 'Passwort wiederholen:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Die Benutzerdetails wurden gespeichert.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Einstellungen');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Spoiler-Tag im Editor anzeigen.');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Ist hier &quot;Ja&quot; aktiviert, wird der [spoiler]-Button im Texteditor angezeigt.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Video-Tag im Editor anzeigen');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Ist hier &quot;Ja&quot; aktiviert, wird der [video]-Button im Texteditor angezeigt.');
DEFINE('_COM_A_SHOWEBAYTAG', 'eBay-Tag im Editor anzeigen');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Ist hier &quot;Ja&quot; aktiviert, wird der [eBay]-Button im Texteditor angezeigt.');
DEFINE('_COM_A_TRIMLONGURLS', 'Lange URLs k&uuml;rzen');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Ist hier &quot;Ja&quot; aktiviert, dann werden lange URLs gek&uuml;rzt. Weitere Einstellungsm&ouml;glichkeiten f&uuml;r diese Option befinden sich in den Anfangs- und Endl&auml;ngeneinstellungen.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Einstellung: Anfangsl&auml;nge der gek&uuml;rzten URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Die L&auml;nge der Anfangszeichen der gek&uuml;rzten URL. &quot;Lange URLs k&uuml;rzen&quot; muss auf &quot;Ja&quot; eingestellt sein!');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Einstellung: Endl&auml;nge der gek&uuml;rzten URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC','Die L&auml;nge der Endzeichen der gek&uuml;rzten URL. &quot;Lange URLs k&uuml;rzen&quot; muss auf &quot;Ja&quot; eingestellt sein!');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'YouTube-Videos autom. einbinden');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Ist hier &quot;Ja&quot; aktiviert, dann werden YouTube-Videos (auch wenn nur die URL zum Video vorliegt) automatisch in den Beitrag eingebunden.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'eBay-Produkte autom. einbinden');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Ist hier &quot;Ja&quot; aktiviert, dann werden eBay-Produkte und die Suche nach ihnen automatisch eingebunden.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay-Widget-Sprachcode');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Es ist sehr wichtig hier den richtigen Sprachcode einzugeben, den eBay ben&ouml;tigt, um die Sprache und die W&auml;hrung richtig anzuzeigen. Standard f&uuml;r ebay.com ist &quot;en-us&quot;. Beispiele: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb.');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Sitzungsdauer');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Der Standard betr&auml;gt 1800 [Sekunden]. Die Sessiong&uuml;ltigkeit (Timeout) in Sekunden ist &auml;hnlich der gleichnamigen Joomla!-Funktion. Die Sessiong&uuml;ltigkeit ist wichtig f&uuml;r die Neuberechnung der Zugriffsrechte, der &quot;Wer ist online&quot;-Anzeige und der &quot;NEU&quot;-Anzeige. Sobald eine Sitzung abl&auml;uft, werden die Zugriffsrechte und die &quot;NEU&quot;-Anzeige zur&uuml;ckgesetzt.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Verbinden');
DEFINE('_VIEW_MERGE', 'Thema oder Beitrag mit einem anderen Thema verbinden');
DEFINE('_POST_MERGE_TOPIC', 'Dieses Thema oder diesen Beitrag verbinden mit');
DEFINE('_POST_MERGE_GHOST', 'Eine Kopie im alten Thema belassen');
DEFINE('_POST_SUCCESS_MERGE', 'Thema oder Beitrag erfolgreich verbunden.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Verbinden fehlgeschlagen!');
DEFINE('_GEN_SPLIT', 'Teilen');
DEFINE('_GEN_DOSPLIT', 'Los');
DEFINE('_VIEW_SPLIT', 'Thema teilen');
DEFINE('_POST_SUCCESS_SPLIT', 'Thema erfolgreich geteilt!');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Erster Beitrag erfolgreich gewechselt.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Erster Beitrag wechseln ist fehlgeschlagen.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Trennen fehlgeschlagen.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Doppelter, identischer Beitrag wurde ignoriert!');
DEFINE('_POST_SPLIT_HINT', '<br />Hinweis: Du kannst einen Beitrag an die erste Position eines Themas bringen, wenn du im ersten Beitrag eines Themas auf trennen klickst. Markier  den Beitrag, der nach oben soll in der zweiten Spalte und w&auml;hle sonst nichts zum Teilen aus.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'Verwaiste Beitr&auml;ge zum Thema verlinken (Funktioniert nicht).');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Verwaiste Beitr&auml;ge zum neuen Thema verlinken (Funktioniert nicht).');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'Verwaiste Beitr&auml;ge zum  vorherigen Thema verlinken (Funktioniert nicht).');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Verwaiste Beitr&auml;ge zum vorherigen Beitrag verlinken (Funktioniert nicht).');
DEFINE('_POST_MERGE', 'Verbinden');
DEFINE('_POST_MERGE_TITLE', 'Dieses Thema an den ersten Beitrag das Zielthemas anh&auml;ngen.');
DEFINE('_POST_INVERSE_MERGE', 'Umgekehrt verbinden');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Den ersten Beitrag des Zielthemas an dieses Thema anh&auml;ngen.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Dieses Thema wurde von deinen Favoriten entfernt!');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Dieses Thema wurde <b>NICHT</b> von deinen Favoriten entfernt!');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Deine Anweisung zur L&ouml;schung von den Favoriten wurde durchgef&uuml;hrt!');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Dieses Thema wurde von deiner Aboliste gel&ouml;scht!');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Dieses Thema wurde <b>NICHT</b> von deiner Aboliste gel&ouml;scht!');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Deine Anweisung zur L&ouml;schung von der Aboliste wurde durchgef&uuml;hrt!');
DEFINE('_POST_NO_DEST_CATEGORY', 'Kein Zielforum gew&auml;hlt. Es wurde nichts verschoben.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Aktuell');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Eigene');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Diskussionen, die ich gestartet oder auf die ich geantwortet habe.');
DEFINE('_KUNENA_CATEGORY', 'Kategorie:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorien');
DEFINE('_KUNENA_POSTED_AT', 'Geschrieben');
DEFINE('_KUNENA_AGO', 'her');
DEFINE('_KUNENA_DISCUSSIONS', 'Diskussionen');
DEFINE('_KUNENA_TOTAL_THREADS', 'Gesamte Themen:');
DEFINE('_SHOW_DEFAULT', 'Standard');
DEFINE('_SHOW_MONTH', 'Monat');
DEFINE('_SHOW_YEAR', 'Jahr');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Kopiert "%src%" zu "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Speicher die CSS-Datei hier... Datei="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Dateianhangstabelle wurde erforlgreich auf die letzte 1.0.x-Versionsstruktur aktualisiert!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Dateianh&auml;nge in der Beitragstabelle wurden erfolgreich auf die letzte 1.0.x-Versionsstruktur aktualisiert!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Die Unterforen konnten nicht in die Beitragshierarchie einsortiert werden! Nichts wurde gel&ouml;scht!');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Konnte den Beitrag nicht l&ouml;schen! - nichts anderes wurde gel&ouml;scht!');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Der Text konnte nicht gel&ouml;scht werden! Bitte aktualisiere die Datenbank manuell (mesid=%id%)!');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Alles gel&ouml;scht! Aber die Benutzerstatistik konnte nicht aktualisiert werden!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Datenbankfehler gefunden! Bitte aktualisiere deine Datenbank manuell, damit die Antworten zum neuen Forum passen.");
DEFINE('_KUNENA_UNIST_SUCCESS', "Die Komponente Kunena wurde erfolgreich deinstalliert!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena-Forum-Version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generiert: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Keine Foren zum Durchsuchen!');

DEFINE('_KUNENA_ERRORADDUSERS', 'Fehler beim Hinzuf&uuml;gen der Benutzer:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Benutzer synchronisiert! Gel&ouml;scht:');
DEFINE('_KUNENA_USERSSYNCADD', ', hinzugef&uuml;gt:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'Benutzerprofile.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Keine geeigneten Profile zur Synchronisation gefunden!');
DEFINE('_KUNENA_SYNC_USERS', 'Benutzer synchronisieren');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synchronisation der Kunena-Benutzertabelle mit der von Joomla!');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'E-mail an die Administratoren');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC', 'W&auml;hle &quot;Ja&quot;, wenn du willst, dass eine E-Mail-Benachrichtigung bei jedem neuen Beitrag an die Administration geschickt werden soll.');
DEFINE('_KUNENA_RANKS_EDIT', 'Rang bearbeiten');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'E-mail verstecken');
DEFINE('_KUNENA_DT_DATE_FMT','%d.%m.%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d.%m.%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Sonntag');
DEFINE('_KUNENA_DT_LDAY_MON', 'Montag');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Dienstag');
DEFINE('_KUNENA_DT_LDAY_WED', 'Mittwoch');
DEFINE('_KUNENA_DT_LDAY_THU', 'Donnerstag');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Freitag');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Samstag');
DEFINE('_KUNENA_DT_DAY_SUN', 'Son');
DEFINE('_KUNENA_DT_DAY_MON', 'Mon');
DEFINE('_KUNENA_DT_DAY_TUE', 'Die');
DEFINE('_KUNENA_DT_DAY_WED', 'Mit');
DEFINE('_KUNENA_DT_DAY_THU', 'Don');
DEFINE('_KUNENA_DT_DAY_FRI', 'Fre');
DEFINE('_KUNENA_DT_DAY_SAT', 'Sam');
DEFINE('_KUNENA_DT_LMON_JAN', 'Januar');
DEFINE('_KUNENA_DT_LMON_FEB', 'Februar');
DEFINE('_KUNENA_DT_LMON_MAR', 'M&auml;rz');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'Mai');
DEFINE('_KUNENA_DT_LMON_JUN', 'Juni');
DEFINE('_KUNENA_DT_LMON_JUL', 'Juli');
DEFINE('_KUNENA_DT_LMON_AUG', 'August');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'Oktober');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'Dezember');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'M&auml;r');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'Mai');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Okt');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dez');
DEFINE('_KUNENA_CHILD_BOARD', 'Unterforum');
DEFINE('_WHO_ONLINE_GUEST', 'Besucher');
DEFINE('_WHO_ONLINE_MEMBER', 'Benutzer');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'keine');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Bildverarbeitung:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Klicke hier, um fortzufahren...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Durchf&uuml;hren!');
DEFINE('_KUNENA_NO_ACCESS', 'Du hast keinen Zugang zu diesem Forum!');
DEFINE('_KUNENA_TIME_SINCE', 'vor %time%');
DEFINE('_KUNENA_DATE_YEARS', 'Jahren');
DEFINE('_KUNENA_DATE_MONTHS', 'Monaten');
DEFINE('_KUNENA_DATE_WEEKS','Wochen');
DEFINE('_KUNENA_DATE_DAYS', 'Tagen');
DEFINE('_KUNENA_DATE_HOURS', 'Stunden');
DEFINE('_KUNENA_DATE_MINUTES', 'Minuten');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumkopfzeile:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forumanzeige");
DEFINE('_KUNENA_CLASS_SFX', "Forum-CSS-Klassen-Suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS-Suffix, das auf &quot;index&quot;, &quot;showcat&quot; und &quot;view&quot; angewandt wird. So sind verschiedene Designs pro Forum m&ouml;glich.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Bearbeitungszeitraum');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Stell &quot;0&quot; f&uuml;r eine unbegrenzte Bearbeitungzeit ein. Oder gib in Sekunden an, wie lange ein Benutzer seinen Beitrag ver&auml;ndern darf, nachdem er ihn geschrieben bzw. zuletzt bearbeitet hat.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Bearbeitungsfrist');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Standard sind 600 [Sekunden]. Erlaubt das Speichern einer Ver&auml;nderung bis zu 600 Sekunden, nachdem der &quot;Bearbeiten&quot;-Link verschwunden ist.');
DEFINE('_KUNENA_HELPPAGE','Hilfeseite aktivieren');
DEFINE('_KUNENA_HELPPAGE_DESC','Wenn &quot;Ja&quot;, dann wird im Forummen&uuml; ein Link zur Hilfeseite angezeigt.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Hilfe in Kunena anzeigen');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Wenn &quot;Ja&quot;, dann wird der Hilfeinhalt in Kunena eingef&uuml;gt und der Link zur externen Hilfeseite wird nicht mehr funktionieren. <b>Hinweis:</b> Du musst eine &quot;Hilfeinhalt-ID&quot; eintragen!');
DEFINE('_KUNENA_HELPPAGE_CID','Hilfeinhalt-ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Du solltest <b>&quot;Ja&quot;</b> in &quot;Hilfe in Kunena anzeigen&quot; aktiviert haben.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Externe Hilfeseite');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Wenn Du eine externe Hilfeseite benutzen willst, so stell bitte unter &quot;Hilfe in Kunena anzeigen&quot; <b>&quot;Nein&quot;</b> ein.');
DEFINE('_KUNENA_RULESPAGE','Regelseite aktivieren');
DEFINE('_KUNENA_RULESPAGE_DESC','Wenn &quot;Ja&quot;, dann wird im Forummen&uuml; ein Link zur Regelseite angezeigt.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Regeln in Kunena anzeigen');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Wenn &quot;Ja&quot;, dann wird der Regelinhalt in Kunena eingef&uuml;gt und die externe Regelseite wird nicht mehr funktionieren. <b>Hinweis:</b> Du musst eine &quot;Regelinhalt-ID&quot; eintragen.');
DEFINE('_KUNENA_RULESPAGE_CID','Regelinhalt-ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Du solltest <b>&quot;Ja&quot;</b> in &quot;Regeln in Kunena anzeigen&quot; aktiviert haben.');
DEFINE('_KUNENA_RULESPAGE_LINK','Externe Regelseite');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Wenn du eine externe Regelseite benutzen wollen, so stell  bitte unter &quot;Regeln in Kunena anzeigen&quot; <b>&quot;Nein&quot;</b> ein.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD-Bibliothek nicht gefunden!');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2-Bibliothek nicht gefunden!');
DEFINE('_KUNENA_GD_INSTALLED','GD ist verf&uuml;gbar in Version ');
DEFINE('_KUNENA_GD_NO_VERSION','GD-Version kann nicht erkannt werden!');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD ist nicht installiert! Du kannst weitere Informationen erhalten ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Kleiner Avatar: Bildh&ouml;he in px');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Kleiner Avatar: Bildbreite in px');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Mittlerer Avatar: Bildh&ouml;he in px');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Mittlerer Avatar: Bildbreite in px');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Gro&szlig;er Avatar: Bildh&ouml;he in px');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Gro&szlig;er Avatar: Bildbreite in px');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar-Qualit&auml;t');
DEFINE('_KUNENA_WELCOME','Willkommen im Kunena');
DEFINE('_KUNENA_WELCOME_DESC','Danke, dass du dich f&uuml;r Kunena als dein Forensystem entschieden hast. Diese Ansicht gibt dir einen schnellen &Uuml;berblick &uuml;ber mehrere Statistiken deines Forums. Die Links auf der linken Seite dieser Ansicht geben dir die M&ouml;glichkeit jeden Aspekt deines Forums zu bearbeiten. Jede Seite hat n&auml;here Informationen, wie du diese Tools einsetzen kannst.');
DEFINE('_KUNENA_STATISTIC','Statistik');
DEFINE('_KUNENA_VALUE','Wert');
DEFINE('_GEN_CATEGORY','Kategorie');
DEFINE('_GEN_STARTEDBY','Begonnen von: ');
DEFINE('_GEN_STATS','Statistik');
DEFINE('_STATS_TITLE',' Forum-Statistik');
DEFINE('_STATS_GEN_STATS','Allgemeine Statistik');
DEFINE('_STATS_TOTAL_MEMBERS','Benutzer:');
DEFINE('_STATS_TOTAL_REPLIES','Antworten insgesamt:');
DEFINE('_STATS_TOTAL_TOPICS','Themen insgesamt:');
DEFINE('_STATS_TODAY_TOPICS','Themen heute:');
DEFINE('_STATS_TODAY_REPLIES','Antworten heute:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategorien:');
DEFINE('_STATS_TOTAL_SECTIONS','Bereiche:');
DEFINE('_STATS_LATEST_MEMBER','Neuster Benutzer:');
DEFINE('_STATS_YESTERDAY_TOPICS','Themen gestern:');
DEFINE('_STATS_YESTERDAY_REPLIES','Antworten gestern:');
DEFINE('_STATS_POPULAR_PROFILE','Die beliebtesten Benutzerprofile');
DEFINE('_STATS_TOP_POSTERS','Meiste Beitr&auml;ge');
DEFINE('_STATS_POPULAR_TOPICS','Beliebteste Themen');
DEFINE('_COM_A_STATSPAGE','Statistikseite aktivieren');
DEFINE('_COM_A_STATSPAGE_DESC','Wenn auf &quot;Ja&quot;, dann wird ein Link im oberen Bereich des Forums angezeigt, der zur Statistikseite f&uuml;hrt. Diese Seite enh&auml;lt einige Statistiken &uuml;ber dein Forum. <em>Die Statistikseite wird dem Admin immer zur Verf&uuml;gung stehen, egal was hier eingestellt wird!</em>');
DEFINE('_COM_C_JBSTATS','Statistik');
DEFINE('_COM_C_JBSTATS_DESC','Forum Statistiken');
DEFINE('_GEN_GENERAL','Allgemein');
DEFINE('_PERM_NO_READ','Du hast nicht genügend Rechte, um auf dieses Forum zuzugreifen!');
DEFINE ('_KUNENA_SMILEY_SAVED','Smiley gespeichert!');
DEFINE ('_KUNENA_SMILEY_DELETED','Smiley gel&ouml;scht!');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Code existiert bereits!');
DEFINE ('_KUNENA_MISSING_PARAMETER','Fehlender Parameter!');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rang existiert bereits!');
DEFINE ('_KUNENA_RANK_DELETED','Rang gel&ouml;scht!');
DEFINE ('_KUNENA_RANK_SAVED','Rang gespeichert!');
DEFINE ('_KUNENA_DELETE_SELECTED','L&ouml;schen');
DEFINE ('_KUNENA_MOVE_SELECTED','Verschieben nach');
DEFINE ('_KUNENA_REPORT_LOGGED','Gespeichert');
DEFINE ('_KUNENA_GO','Los');
DEFINE('_KUNENA_MAILFULL','Bei abonnierten Themen wird der gesamte neue Beitrag in der E-Mail mitgesendet.');
DEFINE('_KUNENA_MAILFULL_DESC','Wenn &quot;Nein&quot;, so wird in dieser E-Mail nur der Titel des neuen Beitrags eingef&uuml;gt.');
DEFINE('_KUNENA_HIDETEXT','Bitte melde dich an, damit du diesen Inhalt sehen kannst!');
DEFINE('_BBCODE_HIDE','Versteckter Text: [hide]Versteckter Text[/hide] - Verstecken von Text vor Gaesten');
DEFINE('_KUNENA_FILEATTACH','Dateianhang:&#32; ');
DEFINE('_KUNENA_FILENAME','Dateiname:&#32; ');
DEFINE('_KUNENA_FILESIZE','Dateigr&ouml;&szlig;e:&#32; ');
DEFINE('_KUNENA_MSG_CODE','Code:&#32; ');
DEFINE('_KUNENA_CAPTCHA_ON', 'Capcha aktivieren');
DEFINE('_KUNENA_CAPTCHA_DESC', 'Wenn aktiviert, dann muss ein Code eingegeben werden um Beitr&auml;ge zu schreiben. (Spamschutz).');
DEFINE('_KUNENA_CAPDESC','Bitte Code eingeben');
DEFINE('_KUNENA_CAPERR','Code ist falsch!');
DEFINE('_KUNENA_COM_A_REPORT', 'Beitrag melden');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'W&auml;hle &quot;Ja&quot; und der Benutzer kann Beitr&auml;ge der Administration melden.');
DEFINE('_KUNENA_REPORT_MSG', 'Beitrag wurde gemeldet!');
DEFINE('_KUNENA_REPORT_REASON', 'Grund');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Deine Nachricht');
DEFINE('_KUNENA_REPORT_SEND', 'Meldung senden');
DEFINE('_KUNENA_REPORT', 'Moderator informieren');
DEFINE('_KUNENA_REPORT_RSENDER', 'Absender:&#32; ');
DEFINE('_KUNENA_REPORT_RREASON', 'Grund:&#32; ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Nachricht:&#32; ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Autor des Beitrags:&#32; ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Beitrags&uuml;berschrift:&#32; ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Beitrag:&#32; ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Link zum Beitrag:&#32; ');
DEFINE('_KUNENA_REPORT_INTRO', 'wurde dir eine Nachricht geschickt, weil');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Beitrag erfolgreich gemeldet!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Code');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Smiley bearbeiten');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Smilies bearbeiten');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Smiley-Leiste');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Neues Smiley');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Mehr Smilies');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Fenster schlie&szlig;en');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Zus&auml;tzliche Emoticons');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'W&auml;hle einen Smiley');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla!-Mambot-Unterst&uuml;tzung');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Mambot-Unterst&uuml;tzung aktivieren');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Meine Profileinstellungen');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Erlauben, den Benutzernamen zu &auml;ndern');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Dem Benutzer erlauben seinen Benutzernamen &uuml;ber seine Profileinstellungen zu &auml;ndern');
DEFINE ('_KUNENA_RECOUNTFORUMS','Nachz&auml;hlung der Forenstatistiken');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Alle Statistiken der Foren wurden nachgez&auml;hlt!');
DEFINE ('_KUNENA_EDITING_REASON','Grund der &Auml;nderung');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Letzte &Auml;nderung');
DEFINE ('_KUNENA_BY','von');
DEFINE ('_KUNENA_REASON','Grund');
DEFINE('_GEN_GOTOBOTTOM', 'Zum Ende gehen');
DEFINE('_GEN_GOTOTOP', 'Zum Anfang gehen');
DEFINE('_STAT_USER_INFO', 'Benutzerinfo');
DEFINE('_USER_SHOWEMAIL', 'E-Mail anzeigen'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Onlinestatus anzeigen');
DEFINE('_KUNENA_HIDDEN_USERS', 'Versteckte Benutzer');
DEFINE('_KUNENA_SAVE', 'Speichern');
DEFINE('_KUNENA_RESET', 'Zur&uuml;cksetzen');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Standard Galerie');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Pers&ouml;nliche Infos');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Zusammenfassung');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Dein Avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum einrichten');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Aussehen und Layout');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Profil bearbeiten');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Deine Beitr&auml;ge');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Deine Abos');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Deine Favoriten');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Private Nachrichten');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Posteingang');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Neue Nachricht');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Postausgang');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Papierkorb');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Einstellungen');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakte');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Blockierte Benutzer');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Zus&auml;tzliche Infos');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Name');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Benutzername');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-Mail');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Benutzertyp');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Registriert seit');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Zuletzt online');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Beitr&auml;ge');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profilansicht');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Pers&ouml;nlicher Text');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Geschlecht');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Geburtstag');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Tag (DD) - Monat (MM) - Jahr (YYYY)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Ort');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Das ist deine &quot;ICQ&quot;-Nummer.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Das ist dein &quot;AOL Instant Messenger&quot;-Nickname.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Das ist dein &quot;Yahoo! Instant Messenger&quot;-Nickname.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Das ist dein &quot;Skype&quot;-Name.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Das ist dein &quot;Gtalk&quot;-Name.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Webseite');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Webseitenname');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Beispiel: www.kunena.com');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Webseite-URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Beispiel: www.kunena.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Das ist deine &quot;MSN Messenger&quot;-E-Mail-Adresse.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Signatur');
DEFINE('_KUNENA_MYPROFILE_MALE', 'm&auml;nnlich');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'weiblich');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Beitr&auml;ge wurden erfolgreich gel&ouml;scht!');
DEFINE('_KUNENA_DATE_YEAR', 'Jahr');
DEFINE('_KUNENA_DATE_MONTH', 'Monat');
DEFINE('_KUNENA_DATE_WEEK','Woche');
DEFINE('_KUNENA_DATE_DAY', 'Tag');
DEFINE('_KUNENA_DATE_HOUR', 'Stunde');
DEFINE('_KUNENA_DATE_MINUTE', 'Minute');
DEFINE('_KUNENA_IN_FORUM', ' im Forum: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum auf: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Beachte bitte: Forencode und Smiley-Button sind nicht sichtbar, aber trotzdem nutzbar!');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum Tools');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Benutzerliste');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s hat <b>%d</b> registrierte Benutzer');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Bitte gib ein Suchwort ein!');
DEFINE ('_KUNENA_USRL_SEARCH','Benutzer suchen');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Suche');
DEFINE ('_KUNENA_USRL_LIST_ALL','Alle anzeigen');
DEFINE ('_KUNENA_USRL_NAME','Name');
DEFINE ('_KUNENA_USRL_USERNAME','Benutzername');
DEFINE ('_KUNENA_USRL_GROUP','Gruppe');
DEFINE ('_KUNENA_USRL_POSTS','Beitr&auml;ge');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Zugriffe');
DEFINE ('_KUNENA_USRL_EMAIL','E-Mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Benutzertyp');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Registriert seit');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Letzter Login');
DEFINE ('_KUNENA_USRL_NEVER','Niemals');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Bild');
DEFINE ('_KUNENA_USRL_ASC','Aufsteigend');
DEFINE ('_KUNENA_USRL_DESC','Absteigend');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Anzeige');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Benutzerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Zeilenanzahl der Benutzerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Die Anzahl der Zeilen in der Benutzerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Onlinestatus');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Den Onlinestatus des Benutzers anzeigen');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Avatar anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Richtigen Namen anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Benutzername anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Benutzergruppe anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Anzahl der Beitr&auml;ge anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Karma anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','E-Mail anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Benutzertyp anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','"Registriert seit" anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','"Letzter Login" anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Zugriffe auf das Profil anzeigen');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Datenbank-Assistent');
DEFINE('_KUNENA_DBMETHOD', 'Bitte w&auml;hle die Methode aus, mit der du die Installation abschlie&szlig;en willst:');
DEFINE('_KUNENA_DBCLEAN', 'Neuinstallation');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade von Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Hauptkategorie');
DEFINE('_KUNENA_REGISTERED', 'Registriert');
DEFINE('_KUNENA_PUBLICBACKEND', '&Ouml;ffentliches Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'W&auml;hle ein Objekt zum');
DEFINE('_KUNENA_ERRORSUBS', 'Irgendwas lief schief beim L&ouml;schen der Beitr&auml;ge und Abos!');
DEFINE('_KUNENA_WARNING', 'Warnung...');
DEFINE('_KUNENA_CHMOD1', 'Die Datei muss die Rechte "766" bekommen, damit sie ge&auml;ndert werden kann!');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Deine Konfigurationsdatei ist');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Template ausw&auml;hlen');
DEFINE('_KUNENA_CONFIGSAVED', 'Konfiguration gespeichert!');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'SCHWERER FEHLER: Konfiguration konnte nicht gespeichert werden!');
DEFINE('_KUNENA_TFINW', 'Die Datei ist schreibgesch&uuml;tzt!');
DEFINE('_KUNENA_FBCFS', 'Kunena-CSS-Datei gespeichert!');
DEFINE('_KUNENA_SELECTMODTO', 'W&auml;hle  einen Moderator aus, um zu');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Du musst ein Forum zum Bereinigen ausw&auml;hlen!');
DEFINE('_KUNENA_DELMSGERROR', 'Beitr&auml;ge l&ouml;schen fehlgeschlagen:');
DEFINE('_KUNENA_DELMSGERROR1', 'Beitragstexte l&ouml;schen fehlgeschlagen:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Abos l&ouml;schen fehlgeschlagen:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum bereinigen nach');
DEFINE('_KUNENA_PRUNEDAYS', 'Tagen');
DEFINE('_KUNENA_PRUNEDELETED', 'Gel&auml;scht:');
DEFINE('_KUNENA_PRUNETHREADS', 'Themen');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Fehler beim Bereinigen der Benutzer:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Benutzerliste bereinigt; Gel&ouml;scht wurden:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'Benutzerprofile'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Keine geeigneten Profile zum Aufr&auml;umen gefunden!'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena-Tabellen sind aktualisiert zur Version');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forum-Kategorie');
DEFINE('_KUNENA_IMGDELETED', 'Bild gel&ouml;scht');
DEFINE('_KUNENA_FILEDELETED', 'Datei gel&ouml;scht');
DEFINE('_KUNENA_NOPARENT', 'Nichts ausgew&auml;hlt');
DEFINE('_KUNENA_DIRCOPERR', 'Fehler: Datei');
DEFINE('_KUNENA_DIRCOPERR1', 'kann nicht kopiert werden!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena-Forum</strong>-Komponente <em>f&uuml;r Joomla!</em> <br />&copy; 2008 - 2009 by www.Kunena.com<br />Alle Rechte vorbehalten.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>Erfolgreich!');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profileinstellungen');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Wenn du &quot;Clexus PM&quot; f&uuml;r den &quot;Community Builder&quot; installiert hast, dann kannst du Kunena so einstellen, dass &quot;Clexus&quot; die Profilseite des Benutzers benutzt.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profilanzeige</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Deine Forumbeitr&auml;ge');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Themen');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Gestartet von');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorien');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Datum');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Zugriffe');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Keine Forumbeitr&auml;ge');
DEFINE('_KUNENA_TOTALFAVORITE', 'Top: ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Spaltenanzahl f&uuml;r Unterforen  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Die Anzahl der Spalten, die unter dem &uuml;bergeordneten Forum sortiert werden sollen');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Abos standardm&auml;&szlig;ig aktivieren?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Auf &quot;Ja&quot; einstellen, wenn du willst, dass die Abobox immer ausgew&auml;hlt ist');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategorie / Forum muss einen Namen haben!');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Statistiken anzeigen');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Wenn du Statistiken anzeigen willst, dann w&auml;hle &quot;Ja&quot; aus');
DEFINE('_KUNENA_SHOWWHOIS', '&quot;Wer ist online&quot; anzeigen');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Wenn du &quot;Wer ist online&quot; anzeigen willst, dann w&auml;hle &quot;Ja&quot; aus');
DEFINE('_KUNENA_STATSGENERAL', '&quot;Allgemeine Statistiken&quot; anzeigen');
DEFINE('_KUNENA_STATSGENERALDESC', 'Wenn du &quot;allgemeine Statistiken&quot; anzeigen willst, dann w&auml;hle &quot;Ja&quot; aus');
DEFINE('_KUNENA_USERSTATS', '&quot;Beliebte Benutzer&quot; Statistiken anzeigen');
DEFINE('_KUNENA_USERSTATSDESC', 'Wenn du &quot;beliebte Benutzer&quot; Statistiken anzeigen willst, dann w&auml;hle &quot;Ja&quot; aus');
DEFINE('_KUNENA_USERNUM', 'Anzahl &quot;Beliebte Benutzer&quot;');
DEFINE('_KUNENA_USERPOPULAR', '&quot;Beliebte Beitr&auml;ge&quot; anzeigen');
DEFINE('_KUNENA_USERPOPULARDESC', 'Wenn du &quot;beliebte Beitr&auml;ge&quot; anzeigen willst, dann w&auml;hle &quot;Ja&quot; aus');
DEFINE('_KUNENA_NUMPOP', 'Anzahl der &quot;beliebten Beitr&auml;ge&quot;');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.8. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add Joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community Builder and JomSocial profile options</li><li>Avatar management : Community Builder and JomSocial options<br /></li></ul><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Anleitung');
DEFINE('_KUNENA_FINFO', 'Kunena-Forum-Information');
DEFINE('_KUNENA_CSSEDITOR', 'Kunena-Template-CSS-Editor');
DEFINE('_KUNENA_PATH', 'Pfad:');
DEFINE('_KUNENA_CSSERROR', 'Bitte beachte: Die CSS-Template-Datei darf nicht schreibgesch&uuml;tzt sein, damit die &Auml;nderungen &uuml;bernommen werden k&ouml;nnen.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena-Benutzerprofile');
DEFINE('_KUNENA_SORTID', 'Sortiert nach Benutzer-ID');
DEFINE('_KUNENA_SORTMOD', 'Sortiert nach Moderatoren');
DEFINE('_KUNENA_SORTNAME', 'Sortiert nach Namen');
DEFINE('_KUNENA_VIEW', 'Anzeige');
DEFINE('_KUNENA_NOUSERSFOUND', 'Keine Benutzerprofile gefunden.');
DEFINE('_KUNENA_ADDMOD', 'Moderator hinzuf&uuml;gen zu');
DEFINE('_KUNENA_NOMODSAV', 'Es wurden keine geeigneten Moderatoren gefunden. Bitte lies den &quot;Hinweis&quot; unten.');
DEFINE('_KUNENA_NOTEUS',
    'HINWEIS: Nur Benutzer, die in ihrem Kunena-Profil als Moderator eingestuft sind, werden hier angezeigt. Um einen Moderator hinzuzuf&uuml;gen, musst du ihm Moderatorenrechte geben. Geh hierzu in die <a href="index2.php?option=com_Kunena&task=profiles">Benutzer-Administration</a> und such nach dem jeweiligen Benutzer. W&auml;hle  dann sein Profil aus und &auml;nder  entsprechend. Der Moderationsstatus kann nur von Administratoren gesetzt werden.');
DEFINE('_KUNENA_PROFFOR', 'Profil f&uuml;r');
DEFINE('_KUNENA_GENPROF', 'Allgemeine Profileinstellungen');
DEFINE('_KUNENA_PREFVIEW', 'Bevorzugter Anzeigetyp:');
DEFINE('_KUNENA_PREFOR', 'Bevorzugte Anordnung der Beitr&auml;ge:');
DEFINE('_KUNENA_ISMOD', 'Ist Moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Ja</strong> (nicht &auml;nderbar, wenn der Benutzer ein (Super)Administrator dieser Seite ist.)');
DEFINE('_KUNENA_COLOR', 'Farbe');
DEFINE('_KUNENA_UAVATAR', 'Avatar des Benutzers:');
DEFINE('_KUNENA_NS', 'Keinen ausgew&auml;hlt');
DEFINE('_KUNENA_DELSIG', ' auw&auml;hlen, um diese Signatur zu l&ouml;schen');
DEFINE('_KUNENA_DELAV', ' ausw&auml;hlen, um diesen Avatar zu l&ouml;schen');
DEFINE('_KUNENA_SUBFOR', 'Abo f&uuml;r');
DEFINE('_KUNENA_NOSUBS', 'Keine Abos f&uuml;r diesen Benutzer gefunden!');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Grundlagen');
DEFINE('_KUNENA_BASICSFORUM', 'Foruminformation');
DEFINE('_KUNENA_PARENT', 'Aktuell:');
DEFINE('_KUNENA_PARENTDESC',
    'Bitte denk dran: Um ein Forum erstellen zu k&ouml;nnen brauchst du eine &quot;Kategorie&quot;. Diese Kategorie bildet die oberste Ebene und beinhaltet Foren samt Unterforen.<p>Ein Forum kann <strong>nur</strong> unter einer Kategorie erstellt werden. Ein Unterforum wird in einem &uuml;bergeordneten Forum erstellt.<br /> Beitr&auml;ge k&ouml;nnen <strong>NICHT</strong> in Kategorien geschrieben werden, sondern nur in Foren und Unterforen.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Name des Forums und dessen Beschreibung');
DEFINE('_KUNENA_NAMEADD', 'Name:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Beschreibung:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Erweiterte Foreneinstellungen');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Forensicherheit und -zugang');
DEFINE('_KUNENA_LOCKEDDESC', 'Auf &quot;Ja&quot; stellen, damit dieses Forum gesperrt ist. Nur Moderatoren und Administratoren k&ouml;nnen neue Themen und Antworten auf Beitr&auml;ge schreiben oder etwas in das Forum verschieben.');
DEFINE('_KUNENA_LOCKED1', 'Gesperrt:');
DEFINE('_KUNENA_PUBACC', '&Ouml;ffentlicher Zugang:');
DEFINE('_KUNENA_PUBACCDESC',
    'Um ein &quot;nicht&quot; &ouml;ffentliches Forum zu erstellen w&auml;hle hier die niedrigste Zugangsebene, die ein Benutzer braucht um das Forum zu sehen. Standardeinstellung ist die Zugangsebene &quot;Jeder&quot;, doch mit dieser Einstellung ist das Forum &ouml;ffentlich auch f&uuml;r &quot;Jeden&quot; (Besucher) zug&auml;nglich.<br /><br /><b>Denk dran</b>: Wenn du eine ganze Kategorie auf eine (oder mehrere) bestimmte Gruppe(n) beschr&auml;nkst, dann werden <b>alle Foren</b> in dieser Kategorie vor Benutzern versteckt, deren Zugangsrechte nicht der Kategorie entsprechen, selbst wenn <b>ein</b> oder mehrere Foren eine niedrigere Zugangsebene haben! Das schr&auml;nkt auch den Zugang von Moderatoren ein. Du musst den Moderator zur Liste der Moderatoren der <b>Kategorie</b> hinzuf&uuml;gen, falls er nicht &uuml;ber die entsprechende Zugangsebene verf&uuml;gt.<br /><br /> <b>Hinweis</b>: Das ist unabh&auml;ngig von der Tatsache, dass Kategorien selbst keine Beitr&auml;ge enthalten und somit nicht moderiert werden k&ouml;nnen (Nur Foren enthalten Beitr&auml;ge). Es ist dennoch m&ouml;glich, einer Kategorie Moderatoren zuzuweisen.<br /><br />');
DEFINE('_KUNENA_CGROUPS', 'Untergruppen erlauben:');
DEFINE('_KUNENA_CGROUPSDESC', 'Sollen Untergruppen Zugang zu diesem Forum (mit &ouml;ffentlichem Zugang) bekommen? Wenn hier &quot;Nein&quot; eingestellt ist, ist der Zugang <b>nur</b> f&uuml;r die ausgew&auml;hlte Gruppe.');
DEFINE('_KUNENA_ADMINLEVEL', 'Backend-Zugang:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Wenn du ein Forum mit &ouml;ffentlichem Zugang im Frontend erstellst, kannst du hier einen zus&auml;tzlichen Administrationslevel f&uuml;r das Backend definieren.<br /> Wenn du den Zugang auf eine bestimmte Beutzergruppe des Frontends beschr&auml;nkst, aber hier keine zus&auml;tzliche Benutzergruppe f&uuml;r das Backend ausw&auml;hlst, wird es Administratoren nicht m&ouml;glich sein das Forum zu sehen.');
DEFINE('_KUNENA_ADVANCED', 'Erweitert');
DEFINE('_KUNENA_CGROUPS1', 'Untergruppen erlauben:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Sollen Untergruppen Zugang zu diesem Forum (Administrationslevel) bekommen? Wenn hier &quot;Nein&quot; eingestellt ist, ist der Zugang <b>nur</b> f&uuml;r die ausgew&auml;hlte Gruppe (Zugangsebene).');
DEFINE('_KUNENA_REV', 'Beitr&auml;ge &uuml;berpr&uuml;fen:');
DEFINE('_KUNENA_REVDESC',
    'Auf &quot;Ja&quot; einstellen, wenn Beitr&auml;ge von Benutzern erst durch einen Moderator &uuml;berpr&uuml;ft werden sollen, bevor sie ver&ouml;ffentlicht werden. Diese Einstellung ist nur in moderierten Foren sinnvoll! Wenn das Forum nicht moderiert wird, kann nur der Administrator Beitr&auml;ge freigeben und ver&ouml;ffentlichen!');
DEFINE('_KUNENA_MOD_NEW', 'Moderation');
DEFINE('_KUNENA_MODNEWDESC', 'Moderation des Forums und seine Moderatoren');
DEFINE('_KUNENA_MOD', 'Moderiert:');
DEFINE('_KUNENA_MODDESC',
    'Auf &quot;Ja&quot; einstellen, wenn du diesem Forum Moderatoren zuweisen m&ouml;chtest.<br /><strong>Hinweis:</strong> Das hei&szlig;t nicht, dass neue Beitr&auml;ge erst &uuml;berpr&uuml;ft werden m&uuml;ssen, bevor sie ver&ouml;ffentlicht werden!<br /> Daf&uuml;r musst du die Option &quot;Beitr&auml;ge &uuml;berpr&uuml;fen&quot; unter dem Tab &quot;Erweitert&quot; aktivieren.<br /><br /> <strong>Wichtig!:</strong> Nachdem du &quot;Moderiert&quot; auf &quot;Ja&quot; gestellt hast, m&uuml;sst du deine Einstellungen abspeichern, bevor du Moderatoren hinzuf&uuml;gen kannst.');
DEFINE('_KUNENA_MODHEADER', 'Moderationseinstellungen f&uuml;r dieses Forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Zugeteilte Moderatoren f&uuml;r dieses Forum:');
DEFINE('_KUNENA_NOMODS', 'Es wurden keine Moderatoren zugewiesen!');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Bearbeiten');
DEFINE('_KUNENA_ADD', 'Hinzuf&uuml;gen');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Nach oben');
DEFINE('_KUNENA_MOVEDOWN', 'Nach unten');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Alle Registrierten');
DEFINE('_KUNENA_EVERYBODY', 'Jeder');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Neuordnen');
DEFINE('_KUNENA_CHECKEDOUT', 'In Bearbeitung');
DEFINE('_KUNENA_ADMINACCESS', 'Backend-Zugang');
DEFINE('_KUNENA_PUBLICACCESS', 'Frontend-Zugang');
DEFINE('_KUNENA_PUBLISHED', 'Ver&ouml;ffentlicht');
DEFINE('_KUNENA_REVIEW', '&Uuml;berpr&uuml;fung');
DEFINE('_KUNENA_MODERATED', 'Moderiert');
DEFINE('_KUNENA_LOCKED', 'Gesperrt');
DEFINE('_KUNENA_CATFOR', 'Kategorie / Forum');
DEFINE('_KUNENA_ADMIN', 'Kunena-Administration');
DEFINE('_KUNENA_CP', 'Startseite');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar-Integration');
DEFINE('_COM_A_RANKS_SETTINGS', 'R&auml;nge');
DEFINE('_COM_A_RANKING_SETTINGS', 'Rangeinstellungen');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Avatareinstellungen');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Sicherheitseinstellungen');
DEFINE('_COM_A_BASIC_SETTINGS', 'Allgemeine Einstellungen');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES','Favoriten erlauben');
DEFINE('_COM_A_FAVORITES_DESC','Auf &quot;Ja&quot; setzen, wenn du registrierten Benutzern erlauben willst, Themen zu den Favoriten hinzuzuf&uuml;gen ');
DEFINE('_USER_UNFAVORITE_ALL','Diese Box ausw&auml;hlen, um alle <b><u>Favoriten zu l&ouml;schen</u></b> (auch die unsichtbaren).');
DEFINE('_VIEW_FAVORITETXT','Dieses Thema zu den Favoriten hinzuf&uuml;gen. ');
DEFINE('_USER_UNFAVORITE_YES','Das Thema wurde aus deinen Favoriten entfernt!');
DEFINE('_POST_FAVORITED_TOPIC','Thema erfolgreich zu den Favoriten hinzugef&uuml;gt!');
DEFINE('_VIEW_UNFAVORITETXT','Favorit l&ouml;schen');
DEFINE('_VIEW_UNSUBSCRIBETXT','Abo beenden');
DEFINE('_USER_NOFAVORITES','Keine Favoriten');
DEFINE('_POST_SUCCESS_FAVORITE','Deine Anweisung zum Hinzuf&uuml;gen im die Aboliste wurde durchgef&uuml;hrt!');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Suchergebnisse');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Anzahl gesuchter Beitr&auml;ge pro Seite');
DEFINE('_KUNENA_USE_JOOMLA_STYLE','Joomla!-Style benutzen?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Wenn du den Joomla!-CSS-Klassen benutzen willst, dann bitte auf &quot;Ja&quot; setzen. (Klassen: wie sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Icons der Unterforen anzeigen');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Auf &quot;Ja&quot; setzen, um ein kleines Icon f&uuml;r die Unterforen in der Forum&uuml;bersicht anzuzeigen. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','Ank&uuml;ndigungen anzeigen');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Auf &quot;Ja&quot; einstellen, falls die Ank&uuml;ndigungen in einer Boxansicht angezeigt werden sollen.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Avatar in der Forumansicht anzeigen');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Auf &quot;Ja&quot; einstellen, um den Avatar in der Forumansicht anzuzeigen.');
DEFINE('_KUNENA_RECENT_POSTS','Einstellungen f&uuml;r &quot;aktuelle Beitr&auml;ge&quot (Kategorieansicht Template default);');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','&quot;Aktuelle Beitr&auml;ge&quot; anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','W&auml;hle  &quot;Ja&quot; aus, wenn die &quot;aktuelle Beitr&auml;ge&quot; in einer extra Box angezeigt werden sollen.');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','Anzahl &quot;aktuelle Beitr&auml;ge&quot;');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','Anzahl der angezeigten &quot;aktuelle Beitr&auml;ge&quot; insgesamt');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Anzahl pro Tab ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','Anzahl der &quot;aktuellen Beitr&auml;ge&quot; pro Tab');
DEFINE('_KUNENA_LATEST_CATEGORY','Nur bestimmte Foren anzeigen');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','Spezielle Foren aus &quot;aktuelle Beitr&auml;ge&quot; anzeigen lassen. Beispiel: 2, 3, 7 (leer lassen f&uer alle Foren)');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Einzelne &Uuml;berschrift anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','Zeigt einzelne &Uuml;berschriften an');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Antworten anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','Zeigt Antworten des Themas/Beitrags (Aw:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','L&auml;nge der &Uuml;berschrift');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','L&auml;nge der &Uuml;berschrift');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Datum anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Das Datum anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Zugriffe anzeigen');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Die Zugriffe anzeigen');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Autor anzeigen');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=Benutzername, 2=richtiger Name, 0=keine');
DEFINE('_KUNENA_STATS','Statistik-Plugin-Einstellungen&#32;');
DEFINE('_KUNENA_CATIMAGEPATH','Pfad der Forenbilder&#32;');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','Bildpfad f&uuml;r ein Forum, Standard: &quot;category_images/&quot; Pfad wird &quot;Ihr_HTML_Rootfolder/images/fbfiles/category_images/&quot; sein.');
DEFINE('_KUNENA_ANN_MODID','Benutzer-IDs f&uuml;r Ank&uuml;ndigungen&#32;');
DEFINE('_KUNENA_ANN_MODID_DESC','Hier f&uuml;gst di die IDs der Benutzer, die Ank&uuml;ndigungen verfassen oder moderieren k&ouml;nnen, ein, z.B. 62, 63, 73. Diese Benutzer k&ouml;nnen Ank&uuml;ndigungen hinzuf&uuml;gen, &auml;ndern oder l&ouml;schen.');
//
DEFINE('_KUNENA_FORUM_TOP', 'W&auml;hle ein Forum&#32;');
DEFINE('_KUNENA_CHILD_BOARDS', 'Unterforen&#32;');
DEFINE('_KUNENA_QUICKMSG', 'Kurzantwort&#32;');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Themen im Forum&#32;');
DEFINE('_KUNENA_FORUM', 'Themen&#32;');
DEFINE('_KUNENA_SPOTS', 'Wichtige Themen');
DEFINE('_KUNENA_CANCEL', 'Abbrechen');
DEFINE('_KUNENA_TOPIC', 'THEMA:&#32;');
DEFINE('_KUNENA_POWEREDBY', 'Powered by&#32;');
// Time Format
DEFINE('_TIME_TODAY', '<b>Heute</b>&#32;');
DEFINE('_TIME_YESTERDAY', '<b>Gestern</b>&#32;');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Letzte Beitr&auml;ge');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Wer ist online');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Hauptforum');
DEFINE('_KUNENA_GUEST', 'Besucher');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'Leser');
DEFINE('_KUNENA_ATTACH', 'Dateianhang');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Favoriten');
DEFINE('_USER_FAVORITES', 'Deine Favoriten');
DEFINE('_THREAD_UNFAVORITE', 'Als Favorit gel&ouml;scht');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Willkommen');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Letzte Beitr&auml;ge anzeigen');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Mein Avatar');
DEFINE('_PROFILEBOX_MYPROFILE', 'Mein Profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Meine Beitr&auml;ge anzeigen');
DEFINE('_PROFILEBOX_GUEST', 'Besucher');
DEFINE('_PROFILEBOX_LOGIN', 'anmelden');
DEFINE('_PROFILEBOX_REGISTER', 'registrieren');
DEFINE('_PROFILEBOX_LOGOUT', 'Logout');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Passwort vergessen?');
DEFINE('_PROFILEBOX_PLEASE', 'Bitte');
DEFINE('_PROFILEBOX_OR', 'oder');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Aktuelle Beitr&auml;ge');
DEFINE('_RECENT_TOPICS', 'Thema');
DEFINE('_RECENT_AUTHOR', 'Autor');
DEFINE('_RECENT_CATEGORIES', 'Kategorien');
DEFINE('_RECENT_DATE', 'Datum');
DEFINE('_RECENT_HITS', 'Zugriffe');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Ank&uuml;ndigungen');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Datum');
DEFINE('_ANN_TITLE', 'Titel');
DEFINE('_ANN_SORTTEXT', 'Kurzer Text');
DEFINE('_ANN_LONGTEXT', 'Langer Text');
DEFINE('_ANN_ORDER', 'Sortierung');
DEFINE('_ANN_PUBLISH', 'Ver&ouml;ffentlichen');
DEFINE('_ANN_PUBLISHED', 'Ver&ouml;ffentlicht');
DEFINE('_ANN_UNPUBLISHED', 'Nicht Ver&ouml;ffentlichen');
DEFINE('_ANN_EDIT', 'Bearbeiten');
DEFINE('_ANN_DELETE', 'L&ouml;schen');
DEFINE('_ANN_SUCCESS', 'Erfolg');
DEFINE('_ANN_SAVE', 'Speichern');
DEFINE('_ANN_YES', 'Ja');
DEFINE('_ANN_NO', 'Nein');
DEFINE('_ANN_ADD', 'Neue hinzuf&uuml;gen');
DEFINE('_ANN_SUCCESS_EDIT', 'Erfolgreich bearbeitet!');
DEFINE('_ANN_SUCCESS_ADD', 'Erfolgreich hinzugef&uuml;gt!');
DEFINE('_ANN_DELETED', 'Erfolgreich gel&ouml;scht!');
DEFINE('_ANN_ERROR', 'FEHLER');
DEFINE('_ANN_READMORE', 'Mehr lesen...');
DEFINE('_ANN_CPANEL', 'Ank&uuml;ndigungen verwalten');
DEFINE('_ANN_SHOWDATE', 'Datum anzeigen');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Forum-Statistik');
DEFINE('_STAT_GENERAL_STATS', 'Allgemeine Statistik');
DEFINE('_STAT_TOTAL_USERS', 'Mitglieder insgesamt');
DEFINE('_STAT_LATEST_MEMBERS', 'Neuestes Mitglied');
DEFINE('_STAT_PROFILE_INFO', 'Profil anzeigen');
DEFINE('_STAT_TOTAL_MESSAGES', 'Gesamte Beitr&auml;ge');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Gesamte Themen');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Gesamte Kategorien');
DEFINE('_STAT_TOTAL_SECTIONS', 'Gesamte Foren');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Heute begonnen');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Gestern begonnen');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Heutige Antworten');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Gestrige Antworten');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Letzte Beitr&auml;ge anzeigen');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Mehr Statistiken');
DEFINE('_STAT_USERLIST', 'Benutzerliste');
DEFINE('_STAT_TEAMLIST', 'Forenteam');
DEFINE('_STATS_FORUM_STATS', 'Forum-Statistiken');
DEFINE('_STAT_POPULAR', 'Top');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Die meisten Beitr&auml;ge ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Themen ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Die meisten Profilaufrufe ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Benutzer online');
DEFINE('_MODLIST_OFFLINE', 'Benutzer offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Wer ist online');
DEFINE('_WHO_ONLINE_NOW', 'Online:');
DEFINE('_WHO_ONLINE_MEMBERS', 'Benutzer');
DEFINE('_WHO_AND', 'und');
DEFINE('_WHO_ONLINE_GUESTS', 'Besucher');
DEFINE('_WHO_ONLINE_USER', 'Benutzer');
DEFINE('_WHO_ONLINE_TIME', 'Zeit');
DEFINE('_WHO_ONLINE_FUNC', 'Aktion');
// Userlist
DEFINE('_USRL_USERLIST', 'Benutzerliste');
DEFINE('_USRL_REGISTERED_USERS', '%s hat <b>%d</b> Benutzer');
DEFINE('_USRL_SEARCH_ALERT', 'Bitte gib ein Suchwort ein!');
DEFINE('_USRL_SEARCH', 'Benutzer finden');
DEFINE('_USRL_SEARCH_BUTTON', 'Suche');
DEFINE('_USRL_LIST_ALL', 'Alle anzeigen');
DEFINE('_USRL_NAME', 'Name');
DEFINE('_USRL_USERNAME', 'Benutzername');
DEFINE('_USRL_EMAIL', 'E-Mail');
DEFINE('_USRL_USERTYPE', 'Benutzertyp');
DEFINE('_USRL_JOIN_DATE', 'Registriert seit');
DEFINE('_USRL_LAST_LOGIN', 'Letzter Login');
DEFINE('_USRL_NEVER', 'Niemals');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'Aufsteigend');
DEFINE('_USRL_DESC', 'Absteigend');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Details');
DEFINE('_USRL_COMPROFILER', 'Profil');
DEFINE('_USRL_THUMBNAIL', 'Bild');
DEFINE('_USRL_READON', 'zeige');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'PM senden');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Suchergebnisse f&uuml;r');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'Einstellungen f&uuml;r die Benutzerliste');
DEFINE('_USRL_ERROR', 'Fehler');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE','Private-Nachrichten-Komponente');
DEFINE('_COM_A_COMBUILDER_TITLE','&quot;Community Builder&quot;');
DEFINE('_FORUM_SEARCH','Gesucht nach: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','Soll dieser Eintrag wirklich gel&ouml;scht werden? <br /> Hinweis: Es gibt keine M&ouml;glichkeit ge&ouml;schte Nachrichten wiederherzustellen!');
DEFINE('_MODERATION_DELETE_SUCCESS','Die Nachricht(en) wurde(n) gel&ouml;scht!');
DEFINE('_COM_A_RANKING','Ranganzeige');
DEFINE('_COM_A_BOT_REFERENCE','&quot;Discuss-Bot&quot; Referenz anzeigen');
DEFINE('_COM_A_MOSBOT','Den &quot;Discuss-Bot&quot; aktivieren');
DEFINE('_PREVIEW','Vorschau');
DEFINE('_COM_A_MOSBOT_TITLE','&quot;Discuss-Bot&quot;');
DEFINE('_COM_A_MOSBOT_DESC', 'Der &quot;Discuss-Bot&quot; erm&ouml;glicht den Benutzern, &uuml;ber Artikel im Forum zu diskutieren. Der Titel wird als Betreff im Forum gew&auml;hlt.Falls noch kein Diskussionsthema besteht, wird ein neues erstellt. Falls es schon existiert, bekommt der Benutzer das Thema gezeigt und kann antworten.' . '<br /><strong>Du musst den &quot;Discuss-Bot&quot; separat herunterladen.</strong>'
           . '<br /><br />Besuche die <a href="http://www.kunena.com" target="_blank">Kunena-Homepage</a> f&uuml;r weitere Informationen. Nach der Installation musst du folgenden Text f&uuml;r den Bot in deinen Beitrag einbinden:' . '<br />{mos_KUNENA_discuss:catid}'
           . '<br />Die <em>catid</em> geh&ouml;rt zu dem Forum, in welchem die  Diskussion stattfindet. Um die richtige &quot;CatID&quot; zu finden, schau  einfach im Forum nach und pr&uuml;fe  die Kategorie-Id der URL aus der Browser-Statusleiste.'
           . '<br />Beispiel: Soll der Beitrag mit der &quot;CatID&quot; 26 diskutiert werden, sollte der Bot-Tag wie folgt aussehen: {mos_KUNENA_discuss:26}'
           . '<br />Sieht schwierig aus, aber es erm&ouml;glicht, dass jeder Artikel im Forum diskutiert werden kann.' );
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE','Suchen');
DEFINE('_FORUM_SEARCHRESULTS','Zeigt %s von %s Ergebnissen.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES','<h3 class="contentheading">Forenregeln</h3>');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>bearbeite folgende Datei um die Regeln einzuf&uuml;gen joomlaroot/administrator/components/com_kunena/language/kunena.german.php</li><li>Rule 2</li><li>Rule 3</li><li>Rule 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE','Forencode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS','Dein Beitrag wurde akzeptiert');
DEFINE('_MODERATION_DELETE_ERROR','FEHLER: Die Beitr&auml;ge k&ouml;nnen nicht gel&ouml;scht werden');
DEFINE('_MODERATION_APPROVE_ERROR','FEHLER: Der Beitrag konnte nicht akzeptiert werden');
// listcat.php
DEFINE('_GEN_NOFORUMS','In diesem Forum sind keine Themen!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED','Der Link zum verschobenen Thema konnte im alten Forum nicht erstellt werden!');
DEFINE('_POST_MOVE_GHOST','Link zum verschobenen Thema im alten Forum erstellen.');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP','Forumwechsel');
DEFINE('_COM_A_FORUM_JUMP','Forumwechsel aktivieren');
DEFINE('_COM_A_FORUM_JUMP_DESC','Wenn auf &quot;Ja&quot; gesetzt, wird eine Auswahl auf den Forumseiten angezeigt, die einen schnellen Wechseln zu einem anderen Forum oder einer anderen Kategorie erlaubt.');
//new in 1.1 RC1
DEFINE('_GEN_RULES','Regeln');
DEFINE('_COM_A_RULESPAGE','Regeln anzeigen');
DEFINE('_COM_A_RULESPAGE_DESC',
    'Wenn du &quot;Ja&quot; w&auml;hlst, wird im Men&uuml; von Kunena der Link zu den Regeln angezeigt. Du kannst die Regeln in der rules.php in &quot;/joomla_root/components/com_Kunena&quot; bearbeiten. <em>Vor der n&auml;chsten Aktualisierung, solltest du diese Datei sichern!</em>');
DEFINE('_MOVED_TOPIC','Verschoben:');
DEFINE('_COM_A_PDF','PDF-Generierung aktivieren');
DEFINE('_COM_A_PDF_DESC',
    'Wenn du &quot;Ja&quot; w&auml;hlst, kann von jedem Thema ein PDF-Dokument generiert werden.<br />Es ist nur ein <u>einfaches</u> PDF-Dokument; ohne Besonderheiten...<br /><strong>Wichtig:</strong><ul><li>Diese Funktion l&auml;uft erst ab Mambo 4.5.2 aufw&auml;rts!</li><li>Die Funktion wurde nicht auf Mambo 4.5.1 getestet, aber man kann es versuchen. Dennoch wird es keinen offiziellen Support f&uuml;r 4.5.1 geben.</li><li>Es funktioniert nicht mit Mambo 4.5.0 (getestet)!</li></ul>');
DEFINE('_GEN_PDFA','Klicke auf diesen Button, um ein neues PDF-Dokument zu generieren! (&ouml;ffnet in einem neuen Fenster).');
DEFINE('_GEN_PDF', 'PDF');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE','Hier klicken, um das Profil dieses Benutzers zu sehen');
DEFINE('_VIEW_ADDBUDDY','Hier klicken, um diesen Benutzer deiner Freundesliste hinzuzuf&uuml;gen');
DEFINE('_POST_SUCCESS_POSTED','Dein Beitrag wurde erfolgreich gespeichert!');
DEFINE('_POST_SUCCESS_VIEW','[ Zur&uuml;ck zum Beitrag ]');
DEFINE('_POST_SUCCESS_FORUM','[ Zur&uuml;ck zum Forum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Admin');
DEFINE('_RANK_MODERATOR', 'Moderator');
DEFINE('_SHOW_LASTVISIT','Seit dem letzten Besuch');
DEFINE('_COM_A_BADWORDS_TITLE','Wortfilterung');
DEFINE('_COM_A_BADWORDS','Wortfilterung nutzen');
DEFINE('_COM_A_BADWORDS_DESC','W&auml;hle &quot;Ja&quot;, wenn du Worte, die in Beitr&auml;gen geschrieben wurden, filtern m&ouml;chtest. Welche W&ouml;rter gefiltert werden sollen, stelle  bitte in der &quot;Badword&quot;-Komponente ein. Bedenke dabei, dass du die Komponente vorher installieren m&uuml;ssen!');
DEFINE('_COM_A_BADWORDS_NOTICE','* Diese Nachricht wurde zensiert, da der Administrator bestimmte W&ouml;rter in deiner Nachricht blockiert hat *');
DEFINE('_COM_A_AVATAR_SRC','Avatar-Bild nutzen von');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Wenn du &quot;Clexus PM&quot; f&uuml;r den &quot;Community Builder&quot; installiert hast, dann kannst du Kunena so konfigurieren, dass das Avatar-Bild von &quot;myPMS Pro&quot; oder dem &quot;Community Builder&quot; benutzt werden kann. HINWEIS: Beim &quot;Community Builder&quot; musst du die Thumbnail-Funktion aktivieren, da das Forum die Thumbnails verwendet und nicht die originalen Bilder.');
DEFINE('_COM_A_KARMA','Karma zeigen');
DEFINE('_COM_A_KARMA_DESC','W&auml;hle  &quot;Ja&quot;, um das Benutzerkarma und die Bewertungsbuttons (Applaudieren / Peinigen) zu zeigen, sofern die Benutzer-Statistiken aktiviert sind.');
DEFINE('_COM_A_DISEMOTICONS','Smilies deaktivieren');
DEFINE('_COM_A_DISEMOTICONS_DESC','W&auml;hle  &quot;Ja&quot;, um Smilies komplett zu deaktivieren.');
DEFINE('_COM_C_FBCONFIG','Konfiguration');
DEFINE('_COM_C_FBCONFIGDESC','Alle Kunena-Funktionen konfigurieren');
DEFINE('_COM_C_FORUM','Forum-Administration');
DEFINE('_COM_C_FORUMDESC','Kategorien/Foren hinzuf&uuml;gen und konfigurieren');
DEFINE('_COM_C_USER','Benutzer-Administration');
DEFINE('_COM_C_USERDESC','Benutzer und deren Profile verwalten');
DEFINE('_COM_C_FILES','Hochgeladene Dateien');
DEFINE('_COM_C_FILESDESC','Durchsuchen und Verwalten von hochgeladenen Dateien durch den Benutzer ');
DEFINE('_COM_C_IMAGES','Hochgeladene Bilder');
DEFINE('_COM_C_IMAGESDESC','Durchsuchen und Verwalten von hochgeladenen Bildern durch den Benutzer ');
DEFINE('_COM_C_CSS','CSS-Datei bearbeiten');
DEFINE('_COM_C_CSSDESC','Aussehen & Eindruck von Kunena anpassen');
DEFINE('_COM_C_SUPPORT', 'Support WebSite');
DEFINE('_COM_C_SUPPORTDESC','Link zur Kunena-Webseite (neues Fenster)');
DEFINE('_COM_C_PRUNETAB','Bereinigen der Foren');
DEFINE('_COM_C_PRUNETABDESC','Entfernen alter Themen (konfigurierbar)');
DEFINE('_COM_C_PRUNEUSERS','Bereinigen der Benutzer'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC','Die Kunena-Benutzertabelle mit der Joomla!-Benutzertabelle synchronisieren'); // <=FB 1.0.3
DEFINE('_COM_C_LOADMODPOS', 'Modulpositionen laden');
DEFINE('_COM_C_LOADMODPOSDESC', 'Modulpositionen f&uuml;r das Kunena-Template');
DEFINE('_COM_C_UPGRADEDESC','Die Datenbank durch ein Upgrade auf den neuesten Stand bringen');
DEFINE('_COM_C_BACK','Zur&uuml;ck zur Kunena-&Uuml;bersicht');
DEFINE('_SHOW_LAST_SINCE','Aktive Themen seit dem letzten Besuch am:');
DEFINE('_POST_SUCCESS_REQUEST2','Deine Anfrage ist in Arbeit');
DEFINE('_POST_NO_PUBACCESS3','Klicke bitte hier, um dich zu registrieren.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE','Der Eintrag wurde gel&ouml;scht. ');
DEFINE('_POST_SUCCESS_EDIT','Der Eintrag wurde bearbeitet.');
DEFINE('_POST_SUCCESS_MOVE','Das Thema wurde erfolgreich verschoben.');
DEFINE('_POST_SUCCESS_POST','Dein Eintrag wurde gespeichert.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Dein Abo l&auml;uft.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA','Karma');
DEFINE('_KARMA_SMITE','Abwerten');
DEFINE('_KARMA_APPLAUD','Danke, das hat mir geholfen!');
DEFINE('_KARMA_BACK','Um zum Thema zur&uuml;ck zu gelangen,');
DEFINE('_KARMA_WAIT','Du kannst das Karma eines Benutzers nur alle 6 Stunden &auml;ndern. <br/>Bitte warte, bis die Zeit verstrichen ist, dann kannst du erneut ein Karma &auml;ndern.');
DEFINE('_KARMA_SELF_DECREASE','Bitte versuche  nicht dein eigenes Karma zu ver&auml;ndern!');
DEFINE('_KARMA_SELF_INCREASE','Dein Karma wurde verringert, weil du versucht hast, es selbst aufzuwerten!');
DEFINE('_KARMA_DECREASED','Das Karma des Benutzers wurde verringert. Falls du nicht gleich zum Thema weiter geleitet wirst,');
DEFINE('_KARMA_INCREASED','Das Karma des Benutzers wurde erh&ouml;ht. Falls du nicht gleich zum Thema weiter geleitet wirst,');
DEFINE('_COM_A_TEMPLATE','Template');
DEFINE('_COM_A_TEMPLATE_DESC','W&auml;hle  das gew&uuml;nschte Template aus.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Bildersets');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'W&auml;hlen ein Bilderset f&uuml;r das Template aus.');
DEFINE('_PREVIEW_CLOSE','Das Fenster schlie&szlig;en');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR','Benutze die Themen-Statistik');
DEFINE('_COM_A_POSTSTATSBAR_DESC','W&auml;hle &quot;Ja&quot;, um die Anzahl der Beitr&auml;ge des Benutzer grafisch in einem Statistik-Balken darzustellen.');
DEFINE('_COM_A_POSTSTATSCOLOR','Farbnummer des Statistik-Balkens');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Trage hier die Farbnummer ein, um die Farbe f&uuml;r den Statistik-Balken festzulegen');
DEFINE('_LATEST_REDIRECT','Kunena ben&ouml;tigt erneut deine Zugangsdaten, um eine aktuelle Liste der letzten Beitr&auml;ge seit der letzten Anmeldung zu erstellen. (Das ist normal, nach drei&szlig;ig Minuten Inaktivit&auml;t nach dem Einloggen.) Diese Liste bleibt 30 Minuten aktiv.');
DEFINE('_SMILE_COLOUR','Farbe');
DEFINE('_SMILE_SIZE','Gr&ouml;&szlig;e');
DEFINE('_COLOUR_DEFAULT','Standard');
DEFINE('_COLOUR_RED','Rot');
DEFINE('_COLOUR_PURPLE','Purpurrot');
DEFINE('_COLOUR_BLUE','Blau');
DEFINE('_COLOUR_GREEN','Gr&uuml;n');
DEFINE('_COLOUR_YELLOW','Gelb');
DEFINE('_COLOUR_ORANGE','Orange');
DEFINE('_COLOUR_DARKBLUE','Dunkelblau');
DEFINE('_COLOUR_BROWN','Braun');
DEFINE('_COLOUR_GOLD','Gold');
DEFINE('_COLOUR_SILVER','Silber');
DEFINE('_SIZE_NORMAL','Normal');
DEFINE('_SIZE_SMALL','Klein');
DEFINE('_SIZE_VSMALL','Sehr klein');
DEFINE('_SIZE_BIG','Gro&szlig');
DEFINE('_SIZE_VBIG','Sehr gro&szlig');
DEFINE('_IMAGE_SELECT_FILE','Bild zum Anh&auml;ngen ausw&auml;hlen');
DEFINE('_FILE_SELECT_FILE','Datei zum Anh&auml;ngen ausw&auml;hlen');
DEFINE('_FILE_NOT_UPLOADED','Die Datei konnte nicht &uuml;bertragen werden. Bitte versuche  es noch einmal');
DEFINE('_IMAGE_NOT_UPLOADED','Das Bild konnte nicht &uuml;bertragen werden. Bitte versuche es noch einmal');
DEFINE('_BBCODE_IMGPH','Platzhalter [img] einf&uuml;gen f&uuml;r Bildanhang');
DEFINE('_BBCODE_FILEPH','Platzhalter [file] einf&uuml;gen f&uuml;r Dateianhang');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','H&auml;kchen setzen, um alle Abos zu beenden (einschlie&szlig;lich der unsichtbaren).');
DEFINE('_LINK_JS_REMOVED','<em>Aktive Verlinkung durch JavaScript ist automatisch entfernt worden</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS','Aussehen & Eindruck');
DEFINE('_COM_A_USERS','Benutzerbezogene Einstellungen');
DEFINE('_COM_A_LENGTHS','Verschiedene L&auml;ngeneinstellungen');
DEFINE('_COM_A_SUBJECTLENGTH','Max. &Uuml;berschriftenl&auml;nge');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maximale &Uuml;berschriftenl&auml;nge. Die Datenbank erlaubt maximal 255 Zeichen. Falls deine Website mit &quot;multi-byte character&quot; arbeitet, wie z.B. Unicode, UTF-8, non-ISO-8599-x verringer  das Maximum durch die Verwendung folgender Formel:<br/><tt>round_down(255/(Maximale Zeichensatz Bytegr&ouml;&szlig;e pro Zeichen))</tt><br/> Beispiel f&uuml;r Utf-8, f&uuml;r welches die maximale Zeichenbytegr&ouml;&szlig;e pro Zeichen 4 Bytes ist: 255/4=63.');
DEFINE('_LATEST_THREADFORUM','Thema/Forum');
DEFINE('_LATEST_NUMBER','Neue Beitr&auml;ge');
DEFINE('_COM_A_SHOWNEW','Neue Beitr&auml;ge anzeigen');
DEFINE('_COM_A_SHOWNEW_DESC','Wenn du auf &quot;Ja&quot; stellst, dann zeigt Kunena durch eine Kennzeichnung dem Benutzer, in welchem Forum und Thema es seit seinem letzten Besuch Neuigkeiten gegeben hat.');
DEFINE('_COM_A_NEWCHAR','&quot;Neu&quot;-Kennzeichnung');
DEFINE('_COM_A_NEWCHAR_DESC','Definiere hier, wie ein neuer Beitrag gekennzeichnet werden soll (wie z.B. durch &quot;!&quot; oder &quot;Neu!&quot;)');
DEFINE('_LATEST_AUTHOR','Letzter Autor');
DEFINE('_GEN_FORUM_NEWPOST','Es gibt neue Beitr&auml;ge seit dem letzten Besuch');
DEFINE('_GEN_FORUM_NOTNEW','Keine neuen Beitr&auml;ge seit dem letzten Besuch');
DEFINE('_GEN_UNREAD','Ungelesenes Thema');
DEFINE('_GEN_NOUNREAD','Gelesenes Thema');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Alle Foren als gelesen markieren');
DEFINE('_GEN_MARK_THIS_FORUM_READ','Dieses Forum als gelesen markieren');
DEFINE('_GEN_FORUM_MARKED','Alle Beitr&auml;ge in diesem Forum sind als gelesen markiert worden!');
DEFINE('_GEN_ALL_MARKED','Alle Beitr&auml;ge wurden als gelesen markiert!');
DEFINE('_IMAGE_UPLOAD','Bilder hochladen');
DEFINE('_IMAGE_DIMENSIONS','Dein Bild darf maximal haben (Breite x H&ouml;he - Gr&ouml;&szlig;e)');
DEFINE('_IMAGE_ERROR_TYPE','Bitte benutze nur Jpeg-, Gif- oder Png-Bilder');
DEFINE('_IMAGE_ERROR_EMPTY','Bitte markier ein Bild, bevor du es hochl&auml;dst!');
DEFINE('_IMAGE_ERROR_SIZE','Die Gr&ouml;&szlig;e des Bildes &uuml;berschreitet die vom Administrator zugelassene Gr&ouml;&szlig;e.');
DEFINE('_IMAGE_ERROR_WIDTH','Die Breite des Bildes &uuml;berschreitet die vom Administrator zugelassene Gr&ouml;&szlig;e.');
DEFINE('_IMAGE_ERROR_HEIGHT','Die H&ouml;he des Bildes &uuml;berschreitet die vom Administrator zugelassene Gr&ouml;&szlig;e.');
DEFINE('_IMAGE_UPLOADED','Dein Bild wurde hochgeladen.');
DEFINE('_COM_A_IMAGE','Bilder');
DEFINE('_COM_A_IMGHEIGHT','Max. Bildh&ouml;he - Angabe in px');
DEFINE('_COM_A_IMGWIDTH','Max. Bildbreite - Angabe in px');
DEFINE('_COM_A_IMGSIZE','Max. Bildergr&ouml;&szlig;e<br/><em>in Kilobyte</em>');
DEFINE('_COM_A_IMAGEUPLOAD','Jedem erlauben Bilder hochzuladen');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Stelle  auf &quot;Ja&quot;, wenn Du es erlauben m&ouml;chtest, dass Jeder (auch ein Besucher) Bilder hochladen darf.');
DEFINE('_COM_A_IMAGEREGUPLOAD','Erlaube nur registrierten Benutzern Bilder hochzuladen');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Stelle  auf &quot;Ja&quot;, wenn du m&ouml;chtest, dass registrierte und angemeldete Benutzer Bilder hochladen d&uuml;rfen.<br/> Wichtig!: (Super)Administratoren und Moderatoren d&uuml;rfen immer Bilder hochladen.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS','Hochladen');
DEFINE('_FILE_TYPES','Deine Datei darf vom Type sein - max. Gr&ouml;&szlig;e ');
DEFINE('_FILE_ERROR_TYPE','Du kannst nur Dateien mit folgende Endungen hochladen:\n');
DEFINE('_FILE_ERROR_EMPTY','Bitte markiere  eine Datei, bevor du sie hochl&auml;dst!');
DEFINE('_FILE_ERROR_SIZE','Diese Datei &uuml;berschreitet die vom Administrator festgelegte Maximalgr&ouml;&szlig;e!');
DEFINE('_COM_A_FILE','Dateien');
DEFINE('_COM_A_FILEALLOWEDTYPES','Erlaubte Dateitypen');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Hier kannst du angeben, welche Endungen f&uuml;r Dateien erlaubt sind. Benutze das Komma zur Trennung <strong>(alles klein schreiben und ohne Leerzeichen)</strong>.<br />Beispiel: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Max. Dateig&ouml;&szlig;e<br/><em>in Kilobyte</em>');
DEFINE('_COM_A_FILEUPLOAD','Erlaube jedem Benutzer Dateien hochzuladen');
DEFINE('_COM_A_FILEUPLOAD_DESC','Stelle auf &quot;Ja&quot;, wenn du m&ouml;chtest, dass jeder Benutzer Dateien hochladen darf.');
DEFINE('_COM_A_FILEREGUPLOAD','Erlaube  nur registrierten Benutzern Dateien hochzuladen');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Stelle auf &quot;Ja&quot;, wenn du m&ouml;chtest, dass registrierte und angemeldete Benuter Dateien hochladen d&uuml;rfen.<br/> Wichtig: (Super)Administratoren und Moderatoren d&uuml;rfen immer Dateien hochladen.');
DEFINE('_SUBMIT_CANCEL','Die Erstellung deines Beitrags wurde abgebrochen');
DEFINE('_HELP_SUBMIT','Klicke  hier, um deine Nachricht zu senden');
DEFINE('_HELP_PREVIEW','Klicke hier, um eine Vorschau deiner Nachricht zu sehen');
DEFINE('_HELP_CANCEL','Klicke  hier, um deine Nachricht zu l&ouml;schen');	
DEFINE('_POST_DELETE_ATT','Wenn hier ein H&auml;kchen gesetzt ist, werden alle Anh&auml;nge gel&ouml;scht (empfohlen).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP','&Auml;nderungen im Beitrag anzeigen');
DEFINE('_COM_A_USER_MARKUP_DESC','W&auml;hle  &quot;Ja&quot;, wenn du m&ouml;chtest, dass im Beitrag des Benutzers, der den Beitrag ge&auml;ndert hat, der Name des Benutzers sowie das Datum angezeigt werden soll.');
DEFINE('_EDIT_BY','Beitrag ge&auml;ndert von:');
DEFINE('_EDIT_AT','am:');
DEFINE('_UPLOAD_ERROR_GENERAL','Ein Fehler ist beim Hochladen des Avatar aufgetreten! Versuche es noch einmal oder informiere den Administrator!');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Hochgeladene Bilder');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Hochgeladene Dateien');
DEFINE('_COM_A_IMGB_TOTAL_IMG','Anzahl der hochgeladenen Bilder');
DEFINE('_COM_A_IMGB_TOTAL_FILES','Anzahl der hochgeladenen Dateien');
DEFINE('_COM_A_IMGB_ENLARGE','Klicke auf das Bild, um es in voller Gr&ouml;&szlig;e zu sehen');
DEFINE('_COM_A_IMGB_DOWNLOAD','Klicke auf das Bild, um es herunterzuladen');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'Die Option &quot;Mit Dummy ersetzen&quot; ersetzt das markierte Bild mit einem Dummybild.<br /> Das erm&ouml;glicht dir, die aktuelle Datei zu entfernen, ohne den Beitrag zu verschieben.<br /><small><em>Bitte beachte dabei, dass manchmal erst eine Aktualisierung des Browsers die &Auml;nderung sichtbar macht!</em></small>');
DEFINE('_COM_A_IMGB_DUMMY','Aktuelles Dummybild');
DEFINE('_COM_A_IMGB_REPLACE','Mit Dummy ersetzen');
DEFINE('_COM_A_IMGB_REMOVE','Endg&uuml;ltig entfernen');
DEFINE('_COM_A_IMGB_NAME','Name');
DEFINE('_COM_A_IMGB_SIZE','Gr&ouml;&szlig;e');
DEFINE('_COM_A_IMGB_DIMS','Abmessungen');
DEFINE('_COM_A_IMGB_CONFIRM','Bist du absolut sicher, dass du diese Datei l&ouml;schen m&ouml;chtest? <br /> Eine Datei zu l&ouml;schen f&uuml;hrt zur ungenauen Darstellung des referenzierten Beitrags...');
DEFINE('_COM_A_IMGB_VIEW','Thema &ouml;ffnen (zum Bearbeiten)');
DEFINE('_COM_A_IMGB_NO_POST','Kein Verweis zu einem Thema!');
DEFINE('_USER_CHANGE_VIEW','&Auml;nderungen werden erst nach erneutem Besuch des Forums wirksam.');
DEFINE('_MOSBOT_DISCUSS_A','Diesen Beitrag im Forum diskutieren. (');
DEFINE('_MOSBOT_DISCUSS_B',' Beitr&auml;ge)');
DEFINE('_POST_DISCUSS','In diesem Thema wird dieser Beitrag diskutiert');
DEFINE('_COM_A_RSS','RSS-Feed aktivieren');
DEFINE('_COM_A_RSS_DESC','Der RSS-Feed erlaubt es Benutzern, Themen und Beitr&auml;ge direkt auf ihren eigenen Desktop zu laden. RSS Reader Applikation (siehe <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> f&uuml;r ein Beispiel.)');
DEFINE('_LISTCAT_RSS','die neusten Beitr&auml;ge direkt auf deinem Desktop erhalten');
DEFINE('_SEARCH_REDIRECT','Das Forum muss deine Zugangsprivilegien (neu)&uuml;berpr&uuml;fen, bevor es deine Suchanforderung ausf&uuml;hren kann. <br />Keine Sorge, dass ist normal nach 30 Minuten Inaktivit&auml;t.<br />Bitte gib die Suchanforderung erneut ein.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG','Einstellung');
DEFINE('_COM_A_DISPLAY','Anzeige #');
DEFINE('_COM_A_CURRENT_SETTINGS','Aktuelle Einstellung');
DEFINE('_COM_A_EXPLANATION','Erkl&auml;rung');
DEFINE('_COM_A_BOARD_TITLE','Name des Forums');
DEFINE('_COM_A_BOARD_TITLE_DESC','Der Name des Forums');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Default View type');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Choose between a threaded or flat view as default');
DEFINE('_COM_A_THREADS','Themen pro Seite');
DEFINE('_COM_A_THREADS_DESC','Wie viele Themen sollen pro Seite zu sehen sein?');
DEFINE('_COM_A_REGISTERED_ONLY','Nur f&uuml;r registrierte Benutzer');
DEFINE('_COM_A_REG_ONLY_DESC','W&auml;hle &quot;Ja&quot;, wenn nur registrierte Benutzer dieses Forum sehen und darin schreiben d&uuml;rfen, setze es auf &quot;Nein&quot; damit auch nicht registrierte Besucher dieses Forum sehen d&uuml;rfen. Die Schreibrechte der nicht registrierten Besucher werden unter &quot;&Ouml;ffentliches Schreiben&quot; definiert');
DEFINE('_COM_A_PUBWRITE','&Ouml;ffentliches Schreiben');
DEFINE('_COM_A_PUBWRITE_DESC','W&auml;hle &quot;Ja&quot; wenn jeder, auch ein unregistrierter Besucher, im Forum schreiben darf, w&auml;hle &quot;Nein&quot; und jeder kann das Forum sehen, aber nur registrierte Benutzer d&uuml;rfen schreiben.');
DEFINE('_COM_A_USER_EDIT','Benutzerrechte');
DEFINE('_COM_A_USER_EDIT_DESC','W&auml;hle &quot;Ja&quot;, um registrierten Benutzern zu erlauben ihre eigenen Beitr&auml;ge zu bearbeiten.');
DEFINE('_COM_A_MESSAGE','Um die &Auml;nderungen zu speichern, dr&uuml;cke den &quot;Save&quot;-Button oben.');
DEFINE('_COM_A_HISTORY','Verlauf des Themas anzeigen');
DEFINE('_COM_A_HISTORY_DESC','W&auml;hle &quot;Ja&quot;, damit beim Antworten der Verlauf des Themas unterhalb des Antwortfeldes angezeigt wird (empfohlen)');	
DEFINE('_COM_A_SUBSCRIPTIONS','Abonnieren erlauben');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','W&auml;hle  &quot;Ja&quot; und registrierte Benutzer k&ouml;nnen Themen abonnieren und bekommen bei Antwort eine E-Mail-Benachrichtung.');
DEFINE('_COM_A_HISTLIM','Maximale Beitra&uml;ge im Verlauf');
DEFINE('_COM_A_HISTLIM_DESC','Wie viele Beitr&auml;ge sollen in der Verlaufsansicht angezeigt werden?');
DEFINE('_COM_A_FLOOD','Anti-Flooding');
DEFINE('_COM_A_FLOOD_DESC','Die Zeit, die der Benutzer warten muss, bis er erneut Beitr&auml;ge schreiben darf. Null (0) hei&szlig;t, Anti-Flooding ist ausgeschaltet. Achtung: Anti-Flooding <em>kann</em> die Ursache f&uuml;r Performanz-Probleme sein');
DEFINE('_COM_A_MODERATION','Moderatoren benachrichtigen');
DEFINE('_COM_A_MODERATION_DESC','W&auml;hle &quot;Ja&quot; falls der Moderator bei Antworten in den von ihm moderierten Foren per E-Mail informiert werden soll. Achtung!: Ist diese Option eingeschaltet, bekommt der Superadministrator auch alle diese Mails!');
DEFINE('_COM_A_SHOWMAIL','E-Mail anzeigen');
DEFINE('_COM_A_SHOWMAIL_DESC','W&auml;hle &quot;Nein&quot;, damit E-Mail-Adressen niemals angezeigt werden, auch nicht den registrierten Benutzern.');
DEFINE('_COM_A_AVATAR','Avatare erlauben');
DEFINE('_COM_A_AVATAR_DESC','W&auml;hle &quot;Ja&quot; und registrierte Benutzer k&ouml;nnen Avatare benutzen (zum Verwalten ihrer Profile)');
DEFINE('_COM_A_AVHEIGHT','Max. Avatar-H&ouml;he');
DEFINE('_COM_A_AVWIDTH','Max. Avatar-Breite');
DEFINE('_COM_A_AVSIZE','Max. Avatar-Dateigr&ouml;&szlig;e<br/><em>in Kilobyte</em>');
DEFINE('_COM_A_USERSTATS','Benutzerstatistik anzeigen');
DEFINE('_COM_A_USERSTATS_DESC','W&auml;hle &quot;Ja&quot; und Benutzerstatistiken werden angezeigt: Anzahl der Beitr&auml;ge, Rang u.s.w. (Admin, Moderator, Benutzer, etc.).');
DEFINE('_COM_A_AVATARUPLOAD','Avatar hochladen');
DEFINE('_COM_A_AVATARUPLOAD_DESC','W&auml;hle &quot;Ja&quot; und Benutzer k&ouml;nnen eigene Avatare hochladen');
DEFINE('_COM_A_AVATARGALLERY','Benutzung der Avatar-Galerie');
DEFINE('_COM_A_AVATARGALLERY_DESC','W&auml;hle &quot;Ja&quot; und registrierte Benutzer d&uuml;rfen die Avatar-Galerie benutzen! (components/com_Kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','W&auml;hl &quot;Ja&quot;, wenn der Benutzerrang auf der Anzahl der geschriebenen Beitr&auml;ge beruhen soll.<br/><strong>Wenn &quot;Ja&quot;, muss auch die Statistikfunktion eingeschaltet sein!');
DEFINE('_COM_A_RANKINGIMAGES','Rangbilder benutzen');
DEFINE('_COM_A_RANKINGIMAGES_DESC','W&auml;hle &quot;Ja&quot; wenn der Benutzerrang per Grafik angezeigt werden soll. (in components/com_kunena/ranks). Ist diese Option ausgeschaltet, wird der Rang als Text angezeigt. &Uuml;berpr&uuml;fe die Dokumentation auf <a href="http://www.kunena.com" target="_blank">Kunena</a> f&uuml;r weitere Informationen bez&uuml;glich Rangbildern');
//email and stuff
$_COM_A_NOTIFICATION ="Es wurde ein neuer Beitrag geschrieben von ";
$_COM_A_NOTIFICATION1="In einem von dir abonnierten Thema wurde ein neuer Beitrag geschrieben.";
$_COM_A_NOTIFICATION2="Du kannst dieses Abo in deinem Profil abbestellen.";
$_COM_A_NOTIFICATION3="Bitte nicht auf diese E-Mail antworten, da diese automatisch vom System generiert wurde.";
$_COM_A_NOT_MOD1="In dem von dir moderierten Forum wurde ein neuer Beitrag geschrieben. ";
$_COM_A_NOT_MOD2="Du kannst den Beitrag lesen (ansehen), wenn du angemeldet bist.";
DEFINE('_COM_A_NO','Nein');
DEFINE('_COM_A_YES','Ja');
DEFINE('_COM_A_FLAT','kompakt');
DEFINE('_COM_A_THREADED','Direktansicht');
DEFINE('_COM_A_MESSAGES','Beitr&auml;ge pro Seite');
DEFINE('_COM_A_MESSAGES_DESC','Wie viele Nachrichten pro Seite sollen zu sehen sein?');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME','Benutzername');
DEFINE('_COM_A_USERNAME_DESC','W&auml;hle &quot;Ja&quot;, wenn du m&ouml;chtest, dass der Anmeldename des Benutzers verwendet wird (wie bei der Systemanmeldung), anstatt des realen Namens.');
DEFINE('_COM_A_CHANGENAME','Namens&auml;nderung erlauben');
DEFINE('_COM_A_CHANGENAME_DESC','W&auml;hle &quot;Ja&quot;, wenn du m&ouml;chtest, dass registrierte Benutzer ihren Namen f&uuml;r einen Beitrag &auml;ndern k&ouml;nnen. Stelle es auf &quot;Nein&quot;, wenn registrierte Benutzer ihren Namen nicht &auml;ndern sollen.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE','Forum offline stellen');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','W&auml;hle &quot;Ja&quot;, wenn du das Forum offline stellen m&ouml;chtest. (Super)Administratoren k&ouml;nnen das Forum immer noch anschauen.');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Offline-Nachricht');
DEFINE('_COM_A_PRUNE','Foren bereinigen');
DEFINE('_COM_A_PRUNE_NAME','Zu bereinigende Foren ausw&auml;hlen:');
DEFINE('_COM_A_PRUNE_DESC', 'Die Bereinigungsfunktion erlaubt es dir, Themen zu entfernen, zu denen innerhalb der gew&auml;hlten Anzahl an Tagen keine Beitr&auml;ge mehr geschrieben wurden. Die Bereinigungsfunktion entfernt keine Themen, die als "Wichtig" oder als "Gesperrt" markiert wurden, da diese manuell entfernt werden m&uuml;ssen. Themen in geschlossenen Foren, k&ouml;nnen ebenfalls nicht per Bereinigungsfunktion gel&ouml;scht werden.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Themen entfernen ohne Beitr&auml;ge seit&#32;');
DEFINE('_COM_A_PRUNE_DAYS','Tagen');
DEFINE('_COM_A_PRUNE_USERS','Benutzer bereinigen'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC', 'Diese Funktion erlaubt es dir, alle Benutzer aus dem Kunena zu entfernen, die auch nicht mehr auf deiner Joomla!-Seite registriert sind. Es werden die Benutzertabellen von Kunena und von Joomla! verglichen. Profile von Kunena-Benutzern werden gel&ouml;scht, wenn sie nicht mehr in deinem Joomla!-System vorhanden sind. Wenn du sicher bist, dass du weitermachen m&ouml;chtest, klicke auf "Prune" in der Men&uuml;leiste oben.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Aktion');
DEFINE('_GEN_AUTHOR', 'Autor');
DEFINE('_GEN_BY', 'von');
DEFINE('_GEN_CANCEL', 'Abbrechen');
DEFINE('_GEN_CONTINUE', 'Absenden');
DEFINE('_GEN_DATE', 'Datum');
DEFINE('_GEN_DELETE', 'L&ouml;schen');
DEFINE('_GEN_EDIT', 'Bearbeiten');
DEFINE('_GEN_EMAIL', 'E-Mail');
DEFINE('_GEN_EMOTICONS', 'Smilies');
DEFINE('_GEN_FLAT', 'Kompakt');
DEFINE('_GEN_FLAT_VIEW', 'Kompakt');
DEFINE('_GEN_FORUMLIST', 'Foren');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Hilfe');
DEFINE('_GEN_HITS', 'Aufrufe');
DEFINE('_GEN_LAST_POST', 'Letzter Eintrag');
DEFINE('_GEN_LATEST_POSTS', 'Letzte Beitr&auml;ge anzeigen');
DEFINE('_GEN_LOCK', 'Sperren');
DEFINE('_GEN_UNLOCK', 'Entsperren');
DEFINE('_GEN_LOCKED_FORUM', 'Dieses Forum wurde geschlossen, keine Antwort m&ouml;glich.');
DEFINE('_GEN_LOCKED_TOPIC', 'Dieses Thema wurde geschlossen, antworten nicht mehr m&ouml;glich.');
DEFINE('_GEN_MESSAGE', 'Nachricht');
DEFINE('_GEN_MODERATED', 'Dieses Forum wird moderiert, jeder Eintrag muss erst vom Moderator freigegeben werden.');
DEFINE('_GEN_MODERATORS', 'Moderation');
DEFINE('_GEN_MOVE', 'Verschieben');
DEFINE('_GEN_NAME', 'Name');
DEFINE('_GEN_POST_NEW_TOPIC', 'Neues Thema');
DEFINE('_GEN_POST_REPLY', 'Neues Thema');
DEFINE('_GEN_MYPROFILE', 'Mein Profil');
DEFINE('_GEN_QUICKMSG', 'Schnellantwort');
DEFINE('_GEN_QUOTE', 'Zitieren');
DEFINE('_GEN_REPLY', 'Antworten');
DEFINE('_GEN_REPLIES', 'Antw.');
DEFINE('_GEN_THREADED', 'Direktansicht');
DEFINE('_GEN_THREADED_VIEW', 'Direkt');
DEFINE('_GEN_SIGNATURE', 'Signatur');
DEFINE('_GEN_ISSTICKY', 'dieses Thema wurde als wichtig markiert');
DEFINE('_GEN_STICKY', 'Als Wichtig markieren');
DEFINE('_GEN_UNSTICKY', 'Oben l&ouml;sen');
DEFINE('_GEN_SUBJECT', 'Thema');
DEFINE('_GEN_SUBMIT', 'Abschicken');
DEFINE('_GEN_TOPIC', 'Thema');
DEFINE('_GEN_TOPICS', 'Themen');
DEFINE('_GEN_TOPIC_ICON', 'Icon f&uuml;r das Thema');
DEFINE('_GEN_SEARCH_BOX', 'Im Forum suchen');
$_GEN_THREADED_VIEW = "Direkt";
$_GEN_FLAT_VIEW = "Kompakt";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD','hochladen');
DEFINE('_UPLOAD_DIMENSIONS','Das Bild darf max. (Breite x H&ouml;he - Gr&ouml;&szlig;e)');
DEFINE('_UPLOAD_SUBMIT','Deinen neuen Avatar senden');
DEFINE('_UPLOAD_SELECT_FILE','Datei ausw&auml;hlen');
DEFINE('_UPLOAD_ERROR_TYPE','bitte nur Jpeg-, Gif- oder Png-Bilder verwenden');
DEFINE('_UPLOAD_ERROR_EMPTY','Erst eine Datei ausw&auml;hlen, dann hochladen');
DEFINE('_UPLOAD_ERROR_NAME','der Dateiname darf nur aus alphanumerischen Zeichen bestehen, keine Leerzeichen');
DEFINE('_UPLOAD_ERROR_SIZE','Der Dateigr&ouml;&szlig;e &uuml;berschreitet das vom Administrator festgesetzte Maximum.');
DEFINE('_UPLOAD_ERROR_WIDTH','Die max. Breite &uuml;berschreitet die vom Administrator festgesetzte Gr&ouml;&szlig;e');
DEFINE('_UPLOAD_ERROR_HEIGHT','Die max. H&ouml;he &uuml;berschreitet die vom Administrator festgesetzte Gr&ouml;&szlig;e');
DEFINE('_UPLOAD_ERROR_CHOOSE',"Du darfst kein Bild aus der Galerie w&auml;hlen");
DEFINE('_UPLOAD_UPLOADED','Dein Avatar wurde hochgeladen');
DEFINE('_UPLOAD_GALLERY','Einen Avatar aus der Galerie ausw&auml;hlen');
DEFINE('_UPLOAD_CHOOSE', 'Auswahl best&auml;tigen.');
// listcat.php
DEFINE('_LISTCAT_ADMIN','Der Administrator sollte zuerst welche erstellen aus der&#32;');
DEFINE('_LISTCAT_DO','Du solltest wissen, was zu tun ist&#32;');
DEFINE('_LISTCAT_INFORM','Informiere bitte die Administration!');
DEFINE('_LISTCAT_NO_CATS','F&uuml;r diese Kategorie sind noch keine Foren erstellt worden.');
DEFINE('_LISTCAT_PANEL','Joomla!-Administration.');
DEFINE('_LISTCAT_PENDING','unerledigte Nachricht(en)');
// moderation.php
DEFINE('_MODERATION_MESSAGES','Es gibt keine unerledigten Nachrichten in diesem Forum!');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE','Soll dieser Eintrag wirklich gel&ouml;scht werden?');
DEFINE('_POST_ABOUT_DELETE','<strong>Hinweise:</strong><br/> - L&ouml;schst du den ersten Beitrag in einem Thema, wird das ganze Thema gel&ouml;scht ... <br /> Entscheide, ob du den ganzen Beitrag und den Benutzernamen l&ouml;schen willst, oder nur der Inhalt entfernen werden soll ... <br />- Alle Antworten eines gel&ouml;schten normalen Beitrags werden im Thema eine Position nach oben verschoben.');
DEFINE('_POST_CLICK','Hier klicken');
DEFINE('_POST_ERROR','Kann den Benutzernamen/E-Mail nicht finden. Nicht gelisteter Datenbank-Fehler');
DEFINE('_POST_ERROR_MESSAGE','Ein unbekannter SQL-Fehler wurde festgestellt, deine Nachricht wurde nicht gesendet. Versuche es bitte erneut. Wenn der Fehler weiter besteht, kontaktiere  bitte den Administrator.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Ein Fehler ist festgestellt worden, die Nachricht wurde nicht aktualisiert. Versuche es bitte erneut. Wenn der Fehler weiter besteht, kontaktiere bitte den Administrator.');
DEFINE('_POST_ERROR_TOPIC','Ein Fehler wurde festgestellt. Pr&uuml;fe folgenden Fehler:');
DEFINE('_POST_FORGOT_NAME','Du hast vergessen, einen Namen einzugeben. Klicke im Browser auf den Zur&uuml;ck-Button um ihn einzugeben.');
DEFINE('_POST_FORGOT_SUBJECT','Du hastvergessen, eine &Uuml;berschrift einzugeben. Klicke im Browser auf den Zur&uuml;ck-Button um sie einzugeben.');
DEFINE('_POST_FORGOT_MESSAGE','Du hast vergessen eine Nachricht einzugeben. Klicke im Browser auf den Zur&uuml;ck-Button um sie einzugeben.');
DEFINE('_POST_INVALID','Eine falsche Beitrag-ID wurde angefordert!');
DEFINE('_POST_LOCK_SET','Das Thema wurde geschlossen!');
DEFINE('_POST_LOCK_NOT_SET','Dieses Thema kann nicht geschlossen werden!');
DEFINE('_POST_LOCK_UNSET','Das Thema wurde wieder ge&ouml;ffnet!');
DEFINE('_POST_LOCK_NOT_UNSET','Dieses Thema ist nicht mehr zu &ouml;ffnen!');
DEFINE('_POST_MESSAGE','Neue Nachricht hier schreiben: ');
DEFINE('_POST_MOVE_TOPIC','verschiebe dieses Thema nach');
DEFINE('_POST_NEW','Schreibe eine neue Nachricht in: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','Dieses Thema ist nicht zu abonnieren.');
DEFINE('_POST_NOTIFIED','Mache hier ein H&auml;kchen, wenn du per E-Mail &uuml;ber jede Anwort informiert werden m&ouml;chtest.');
DEFINE('_POST_STICKY_SET','Thema wurde als &quot;Wichtig&quot; markiert.');
DEFINE('_POST_STICKY_NOT_SET','Thema als &quot;Wichtig&quot; markieren nicht m&ouml;glich!');
DEFINE('_POST_STICKY_UNSET','Thema wurde wieder als &quot;Normal&quot; markiert!');
DEFINE('_POST_STICKY_NOT_UNSET','Thema kann nicht wieder als &quot;Normal&quot; markiert werden!');
DEFINE('_POST_SUBSCRIBE', 'Thema abonnieren');
DEFINE('_POST_SUBSCRIBED_TOPIC','Du hast dieses Thema jetzt abonniert');
DEFINE('_POST_SUCCESS','Deine Nachricht wurde erfolgreich gespeichert');
DEFINE('_POST_SUCCES_REVIEW','Dein Eintrag wurde erfolgreich gespeichert, sobald ein Moderator ihn freigegeben hat, wird er ver&ouml;ffentlicht');
DEFINE('_POST_SUCCESS_REQUEST','Deine Anfrage wurde gesendet. Falls du in K&uuml;rze nicht zur&uuml;ck zum Beitrag geleitet werden,');
DEFINE('_POST_TOPIC_HISTORY','Themen-Historie');
DEFINE('_POST_TOPIC_HISTORY_MAX','Anzeige der letzten');
DEFINE('_POST_TOPIC_HISTORY_LAST','Beitr&auml;ge - <i>(letzter Eintrag zuerst)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','Dein Thema kann nicht verschoben werden. Um zum Thema zur&uuml;ckzukehren:');
DEFINE('_POST_TOPIC_FLOOD1','Der Administrator hat Anti-Flooding aktiviert, deshalb musst du kurz warten.');
DEFINE('_POST_TOPIC_FLOOD2',' Sekunden zwischen den Beitr&auml;gen bevor du den n&auml;chsten schreiben kannst.');
DEFINE('_POST_TOPIC_FLOOD3','Bitte klicke  in deinem Browser den Zur&uuml;ck-Button, um zur Nachricht zur&uuml;ckzukommen.');
DEFINE('_POST_EMAIL_NEVER','Deine E-Mail-Adresse ist f&uuml;r andere nicht sichtbar.');
DEFINE('_POST_EMAIL_REGISTERED','DeineE-Mail ist nur f&uuml;r registrierte Benutzer erreichbar.');
DEFINE('_POST_LOCKED','vom Administrator gesperrt.');
DEFINE('_POST_NO_NEW','keine weiteren Anworten m&ouml;glich');
DEFINE('_POST_NO_PUBACCESS1','Der Administrator hat den Schreibzugriff auf registrierte Benutzer beschr&auml;nkt!');
DEFINE('_POST_NO_PUBACCESS2','Du kannst das Forum zwar sehen, aber<br/>nur registrierte Benutzer d&uuml;rfen Beitr&auml;ge schreiben!');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','>> In diesem Forum gibt es noch kein Thema  <<');
DEFINE('_SHOWCAT_PENDING','wartende Beitr&auml;ge');
// userprofile.php
DEFINE('_USER_DELETE','Um dein Profil zu l&ouml;schen, bitte hier ein H&auml;kchen setzen.');
DEFINE('_USER_ERROR_A','Du bist durch einen Fehler auf diese Seite gekommen. Wenn das &ouml;fter passiert, informiere bitte den Administrator und sende ihm den Fehlercode ');
DEFINE('_USER_ERROR_B','Du hast geklickt und bist jetzt hier. Du kannst einen Fehler-Report senden.');
DEFINE('_USER_ERROR_C','Vielen Dank!');
DEFINE('_USER_ERROR_D','Im Report einzuf&uuml;gende Fehlernummer: ');
DEFINE('_USER_GENERAL','Aussehen und Layout des Profils');
DEFINE('_USER_MODERATOR','Du bist Moderator von');
DEFINE('_USER_MODERATOR_NONE','Du bist kein Moderator');
DEFINE('_USER_MODERATOR_ADMIN','Administratoren sind gleichzeitig Moderatoren in allen Foren.');
DEFINE('_USER_NOSUBSCRIPTIONS','Nichts abonniert!');
// DEFINE('_USER_PREFERED','Bevorzugte Ansicht');
DEFINE('_USER_PROFILE','Profil von&#32;');
DEFINE('_USER_PROFILE_NOT_A','Dein Profil kann&#32;');
DEFINE('_USER_PROFILE_NOT_B','nicht');
DEFINE('_USER_PROFILE_NOT_C','&#32;aktualisiert werden');
DEFINE('_USER_PROFILE_UPDATED','Dein Profil wurde aktualisiert!');
DEFINE('_USER_RETURN_A','Du wirst gleich weitergeleitet...');
DEFINE('_USER_RETURN_B','Hier klicken!');
DEFINE('_USER_SUBSCRIPTIONS','Deine Abos');
DEFINE('_USER_UNSUBSCRIBE','Abo beenden');
DEFINE('_USER_UNSUBSCRIBE_A','Du kannst');
DEFINE('_USER_UNSUBSCRIBE_B','nicht');
DEFINE('_USER_UNSUBSCRIBE_C',' Dieses Themen-Abo beenden');
DEFINE('_USER_UNSUBSCRIBE_YES','Dieses Themen-Abo wurde beendet');
DEFINE('_USER_DELETEAV','Hier bitte ein H&auml;kchen setzten, um den Avatar zu l&ouml;schen');
//New 0.9 to 1.0
DEFINE('_USER_ORDER','Bevorzugte Nachrichtenreihenfolge');
DEFINE('_USER_ORDER_DESC','Letzten Beitrag zuerst');
DEFINE('_USER_ORDER_ASC','Ersten Beitrag zuerst');
// view.php
DEFINE('_VIEW_DISABLED', 'Kein &ouml;ffentlicher Schreibzugriff erlaubt, bitte erst registrieren!');
DEFINE('_VIEW_POSTED', 'Geschrieben von');
DEFINE('_VIEW_SUBSCRIBE', ':: Dieses Thema abbonieren ::');
DEFINE('_MODERATION_INVALID_ID', 'Es wurde eine falsche Beitrag-ID angefordert.');
DEFINE('_VIEW_NO_POSTS', 'Es sind keine Beitr&auml;ge in diesem Forum.');
DEFINE('_VIEW_VISITOR', 'Besucher');
DEFINE('_VIEW_ADMIN', 'Admin');
DEFINE('_VIEW_USER', 'Benutzer');
DEFINE('_VIEW_MODERATOR', 'Moderator');
DEFINE('_VIEW_REPLY', 'Auf diesen Beitrag antworten');
DEFINE('_VIEW_EDIT', 'Diesen Beitrag bearbeiten');
DEFINE('_VIEW_QUOTE', 'Diesen Beitrag zitieren');
DEFINE('_VIEW_DELETE', 'Diese Nachricht l&ouml;schen');
DEFINE('_VIEW_STICKY', 'Dieses Thema als &quot;Wichtig&quot; markieren');
DEFINE('_VIEW_UNSTICKY', 'Die Markierung &quot;Wichtig&quot; l&ouml;schen');
DEFINE('_VIEW_LOCK', 'Dieses Thema sperren');
DEFINE('_VIEW_UNLOCK', 'Dieses Thema entsperren');
DEFINE('_VIEW_MOVE','Thema in ein anderes Forum verschieben');
DEFINE('_VIEW_SUBSCRIBETXT','Thema abonnieren und E-Mail-Benachrichtigung einschalten');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Home');
DEFINE('_POSTS', 'Beitr&auml;ge:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Beitrag');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forum ist OFFLINE!');
DEFINE('_PAGE', 'Seite: ');
DEFINE('_NO_POSTS', 'Keine Beitr&auml;ge');
DEFINE('_CHARS', 'max. Zeichenanzahl');
DEFINE('_HTML_YES', 'HTML ist deaktiviert');
DEFINE('_YOUR_AVATAR', '<b>Dein Avatar</b>');
DEFINE('_NON_SELECTED', 'Noch keinen augew&auml;hlt <br />');
DEFINE('_SET_NEW_AVATAR', 'Neuen Avatar ausw&auml;hlen');
DEFINE('_THREAD_UNSUBSCRIBE', 'Abo beenden');
DEFINE('_SHOW_LAST_POSTS', 'Aktive Beitr&auml;ge der letzten');
DEFINE('_SHOW_HOURS', 'Stunden');
DEFINE('_SHOW_POSTS', 'Total:&#32;');
DEFINE('_DESCRIPTION_POSTS', 'immer den neuesten Beitrag oben zeigen');
DEFINE('_SHOW_4_HOURS', '4 Stunden');
DEFINE('_SHOW_8_HOURS', '8 Stunden');
DEFINE('_SHOW_12_HOURS', '12 Stunden');
DEFINE('_SHOW_24_HOURS', '24 Stunden');
DEFINE('_SHOW_48_HOURS', '48 Stunden');
DEFINE('_SHOW_WEEK', 'Woche');
DEFINE('_POSTED_AT', 'geschrieben am');
DEFINE('_DATETIME', 'd.m.Y H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'In dem gew&auml;hlten Zeitraum gibt es keine neuen Beitr&auml;ge!');
DEFINE('_MESSAGE', 'Nachricht');
DEFINE('_NO_SMILIE', 'nein');
DEFINE('_FORUM_UNAUTHORIZIED','Dieses Forum ist nur f&uuml;r registrierte bzw. angemeldete Benutzer einsehbar!');
DEFINE('_FORUM_UNAUTHORIZIED2','Wenn du bereits registriert bist, musst du dich zuerst anmelden.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderation');
DEFINE('_MOD_APPROVE', 'Freigeben');
DEFINE('_MOD_DELETE', 'L&ouml;schen');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Letzten Beitrag anzeigen');
DEFINE('_POST_WROTE', 'schrieb');
DEFINE('_COM_A_EMAIL', 'Foren E-Mail-Adresse');
DEFINE('_COM_A_EMAIL_DESC', 'Das ist die E-Mail-Adresse des Forums. Stell sicher, dass die Adresse stimmt!');
DEFINE('_COM_A_WRAP','W&ouml;rter teilen, die l&auml;nger sind als:');
DEFINE('_COM_A_WRAP_DESC','Gib hier ein, wie viele Zeichen ein Wort max. haben darf. Das erm&ouml;glicht dir die Ausgabe auf dein Template anzupassen. 70 Zeichen sind das maximale, was f&uuml;r feste Templates genommen werden sollte, aber du kannst nat&uuml;rlich ein wenig experimentieren. URLs, egal wie lang, werden nicht durch diese Einstellung beeinflusst.');
DEFINE('_COM_A_SIGNATURE','Max. Signaturl&auml;nge');
DEFINE('_COM_A_SIGNATURE_DESC','Wie lang darf die Signatur der Benutzer sein?');
DEFINE('_SHOWCAT_NOPENDING', 'Keine ungelesene(n) Nachricht(en)');
DEFINE('_COM_A_BOARD_OFSET', 'Forum-Zeitverschiebung');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Einige Foren sind auf einem Server, der eine andere Zeitzone als der Benutzer hat. Definiere hier die Zeitverschiebung f&uuml;r Kunena (Zeitangabe im Frontend) im Verh&auml;ltnis zur Serverzeit in Stunden. Positive und negative Werte k&ouml;nnen definiert werden.');
//New in RC2
DEFINE('_COM_A_BASICS', 'Grundlagen');
DEFINE('_COM_A_FRONTEND', 'Frontend');
DEFINE('_COM_A_SECURITY', 'Sicherheit');
DEFINE('_COM_A_AVATARS', 'Avatare');
DEFINE('_COM_A_INTEGRATION', 'Integration');

DEFINE('_COM_A_PMS', 'Private Nachrichten erlauben');
DEFINE('_COM_A_PMS_DESC',
    'W&auml;hle das installierte &quot;Private Nachrichten&quot;-System aus, wenn du eines installiert haben. Falls du &quot;Clexus PM&quot; ausw&auml;hlst, werden auch die &quot;Clexus PM&quot;-Profiloptionen aktiviert (wie: ICQ, AIM, Yahoo, MSN und Links zu Profilen, wenn vom Kunena-Template unterst&uuml;tzt.');
DEFINE('_VIEW_PMS', 'Klicke hier, um diesem Benutzer eine private Nachricht zu schicken');
//new in RC3
DEFINE('_POST_RE', 'Aw: ');
DEFINE('_BBCODE_BOLD', 'Fetter Text: [b]Text[/b] ');
DEFINE('_BBCODE_ITALIC', 'Kursiver Text: [i]Text[/i]');
DEFINE('_BBCODE_UNDERL', 'Unterstrichener Text: [u]Text[/u]');
DEFINE('_BBCODE_QUOTE', 'Zitierter Text: [quote]Text[/quote]');
DEFINE('_BBCODE_CODE', 'Code anzeigen: [code]Code[/code]');
DEFINE('_BBCODE_ULIST', 'Unsortierte Liste: [ul] [li]Text[/li] [/ul] - Hinweis: Eine Liste muss Listenelemente beinhalten');
DEFINE('_BBCODE_OLIST', 'Sortierte Liste: [ol] [li]Text[/li] [/ol] - Hinweis: Eine Liste muss Listenelemente beinhalten');
DEFINE('_BBCODE_IMAGE', 'Bild: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]Das ist ein Link[/url]');
DEFINE('_BBCODE_CLOSA', 'Alle Tags schliessen');
DEFINE('_BBCODE_CLOSE', 'Alle offenen bbCode-Tags schliessen');
DEFINE('_BBCODE_COLOR', 'Farbe: [color=#FF6600]text[/color]');
DEFINE('_BBCODE_SIZE', 'Groesse: [size=1]Textgroesse[/size] - Hinweis: Verfuegbare Groessen: 1 bis 5');
DEFINE('_BBCODE_LITEM', 'Listenelement: [li] Listenelement [/li]');
DEFINE('_BBCODE_HINT', 'bbCode-Hilfe - Hinweis: bbCode kann nur auf markierten Text angewandt werden!');
DEFINE('_COM_A_TAWIDTH', 'Textbereich: Breite in px');
DEFINE('_COM_A_TAWIDTH_DESC', 'Hier kannst du die Breite des Textbereichs &auml;ndern, um sie an dein Template anzupassen. <br/>Die Smiley-Toolbar wird auf zwei Reihen verteilt; wenn die Gr&ouml;&szlig;e <= 420 Pixel ist.');
DEFINE('_COM_A_TAHEIGHT', 'Textbereich: H&ouml;he in px');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Hier kannst du die H&ouml;he des Textbereichs &auml;ndern, um sie an dein Template anzupassen.');
DEFINE('_COM_A_ASK_EMAIL', 'E-Mail ist Pflicht');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Ist eine E-Mail-Adresse erforderlich, wenn ein Benutzer oder Besucher einen Beitrag schreiben will? Stelle &quot;Nein&quot; ein, wenn du das nicht willst. Der Benutzer wird dann nicht nach seiner E-Mail-Adresse gefragt.');
//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Rangverwaltung');
define('_KUNENA_SORTRANKS', 'Sortiert nach R&auml;ngen');
define('_KUNENA_RANKSIMAGE', 'Rangbild');
define('_KUNENA_RANKS', 'Rangtitel');
define('_KUNENA_RANKS_SPECIAL', 'Spezial');
define('_KUNENA_RANKSMIN', 'Min. Beitragsanzahl');
define('_KUNENA_RANKS_ACTION', 'Aktionen');
define('_KUNENA_NEW_RANK', 'Neuer Rang');

?>
