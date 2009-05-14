<?php
/**
* @version $Id: kunena.hungarian.php 597 2009-04-03 17:50:33Z localicer $
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
*    Language: Dutch
* For version: 1.0.7 or higher
*    Encoding: UTF-8
*  Translator: Jelle Kok
*      E-mail: /
*      Website: www.newf1.nl
* based on the translation by Gerard van Enschut (Fireboard version)
*     # Copyright (C) 2005 - 2007 Joomla.org. Alle rechten voorbehouden.
*     # Url: http://joomlacode.org/gf/project/nederlands/
*     # Licentie http://www.gnu.org/copyleft/gpl.html GNU/GPL
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Geinstalleerde versie');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'Licentie');
DEFINE('_KUNENA_PROFILE_NO_USER', 'Gebruiker bestaat niet.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'de gebruiker is nog niet op de forum geweest en heeft ook nog niet een profiel gemaakt.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Zoek resultaten');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Geavanceerd Zoeken');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Zoeken door Sleutelwoorden');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Sleutelwoorden');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Doorzoek gehele posts');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Doorzoek alleen titels');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Doorzoek op Gebruikers');
DEFINE('_KUNENA_SEARCH_UNAME', 'Gebruikersnaam');
DEFINE('_KUNENA_SEARCH_EXACT', 'echte naam');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Bericht aangemaakt door');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Onderwerp begonnen door');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Activiteiten in het onderwerp');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Zoek Opties');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Vind onderwerp met');
DEFINE('_KUNENA_SEARCH_LEAST', 'Tenminste');
DEFINE('_KUNENA_SEARCH_MOST', 'Aan meest');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Andwoorden');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'vind Posts van');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Elke datum');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Laatst bezocht');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Gisteren');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'een week begonnen');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 weken geleden');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'een maand geleden');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 maanden geleden');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 maanden geleden');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'een jaar geleden');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'En nieuwer');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'En ouder');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sorteer Resulaten op');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Titel');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Nummer van post');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Nummer of views');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Start onderwerp datum');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Posting datum');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'Gebruikersnaam');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Verminder volgorders');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'meer volgorders');
DEFINE('_KUNENA_SEARCH_START', 'Spring naar resultaat nummer');
DEFINE('_KUNENA_SEARCH_LIMIT5', 'Laat 5 zoek resultaten zien');
DEFINE('_KUNENA_SEARCH_LIMIT10', 'Laat 10 zoek resultaten zien');
DEFINE('_KUNENA_SEARCH_LIMIT15', 'Laat 15 zoek resultaten zien');
DEFINE('_KUNENA_SEARCH_LIMIT20', 'Laat 20 zoek resultaten zien');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Zoeken in Categorieen');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'Alle Categorieen');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Doorzoek ook in onderliggende forums');
DEFINE('_KUNENA_SEARCH_SEND', 'Verstuur');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Annuleer');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'Geen berichten die alle uw zoektochttermijnen bevatten werden gevonden.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'Tenminste een trefwoord zou over 3 tekens lang moeten zijn!');


// 1.0.8 Hack 
DEFINE('_COM_A_SHOWIMGFORGUEST', 'Laat afbeeldingen zien voor gasten');
DEFINE('_COM_A_SHOWFILEFORGUEST', 'Laat bestanden zien voor gasten');
DEFINE('_COM_A_SHOWIMGFORGUEST_DESC', 'Laat afbeeldingen zien voor gasten; Ja = afbeeldingen voor iedereen Nee= Alleen gebruikers');
DEFINE('_COM_A_SHOWFILEFORGUEST_DESC', 'Laat bestanden zien voor gasten; Ja = bestanden voor iedereen Nee= Alleen gebruikers');
DEFINE('_KUNENA_CONFIGSAVED', 'Instellingen Opgeslagen');


// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'U hebt geen moderator permissies!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Deze onderwerp is <b> NIET </ b> toegevoegd aan uw favorieten');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Sync de Kunena gebruiker tabel met de Joomla gebruiker tabel');
DEFINE('_POST_FORGOT_EMAIL', 'U heeft de e-mail adres vergeten te invullen . Klik op uw browser terug knop om terug te gaan en probeer het opnieuw.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Alle bijlages zijn niet goed verwijderd!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Hoofd Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Dit is het belangrijkste forum categorie. Als een niveau een categorie dient het als een container voor individuele boards of fora. Het is ook bedoeld als een niveau 1-categorie en is een must-have voor elke Kunena Forum setup.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'Om extra informatie voor je gasten en leden van het forum header kan worden benut om tekst weer te geven op de top van een bepaalde categorie.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Welkom Jelle');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'We moedigen nieuwe leden om een korte introductie van zichzelf in dit forum categorie. Elkaar leren kennen en deel je de gemeenschappelijke belangen.<br>');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '<strong> Welkom op de Kunena forum! </ strong> <br> <br> Vertel het ons en onze leden die je bent, wat u wilt en waarom je lid geworden van deze site. <br> Wij verwelkomen alle nieuwe leden en hoop je te zien rond een lot!<br>');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Ideeënbus');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Hebben wat feedback en input te delen? <br> Don\'t worden verlegen en drop ons een notitie. Wij willen van u horen en streven ernaar onze site beter en gebruiksvriendelijker voor onze gasten en de leden een soortgelijk.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Dit is de optionele Forum header voor de suggestie Box.<br>');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Welkom bij Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Welkom bij Kunena![/b][/size]

Kunena, wat vertaald vanuit het Swahili "om te praten" betekend, is ontstaan uit een splitsing vanuit het voormalige Fireboard Forum met een nieuw team en sommige van de vorige ontwikkelaars, inclusief een aantal Joomla! Kern ontwikkelaars. Kunena is uitgebracht op 27 januari 2009.

Vele reparaties en nieuwe kenmerken die waren gepland voor de laatste uitgave maar nooit ten uitvoer zijn gebracht, zijn nu toegevoegd aan de oorspronkelijke uitgave. Kunena 1.0.x, die alleen voor test doeleinde is, is volledig verenigbaar met alle voorgaande versies van Fireboard en kunnen parallel geïnstalleerd worden. Migratie van Fireboard naar Kunena kan niet makkelijker. Simpel, verwijder Fireboard, zorg dat uw menu link up-to-date is en u bent klaar om te starten.

Een Joomla 1.5.x native versie van Kunena is in de maak en we zijn actief aan het werk op nieuwe toevoegingen en stimuleren suggesties in ons forum. Probeer Kunena!');

// 1.0.5RC2
DEFINE('_COM_A_HIGHLIGHTCODE', 'Zet Code Highlighting aan');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Zet het FireBoard code tag highlighting java script aan. Indien leden PHP en andere gelijksoortige code fragmenten plaatsen binnen code tags, zal deze optie de code van een kleur voorzien. Indien uw forum geen gebruik maakt van zulke programmeertaal posts, kan u het beter uitzetten om te voorkomen dat code tags worden vervormd.');
DEFINE('_COM_A_RSS_TYPE', 'Standaard RSS type');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Kies tussen RSS feeds op basis van onderwerp of post. Op basis van onderwerp betekent dat enkel 1 inschrijving per onderwerp getoond zal worden in de RSS feed, onafhankelijk van hoeveel posts er zijn gemaakt binnen het onderwerp. Door draad maakt een kortere meer compacte RSS feed maar zal niet iedere gemaakt antwoord laten zien.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Op basis van onderwerp');
DEFINE('_COM_A_RSS_BY_POST', 'Op basis van post');
DEFINE('_COM_A_RSS_HISTORY', 'RSS Historie');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Selecteer hoeveel historie moet worden opgenomen binnen de RSS feed. De standaard is 1 maand, maar u kan het limiteren tot 1 week op websites met een hoge bandbreedte.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Week');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Maand');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Jaar');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Standaard Kunena Pagina');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Selecteer de standaard Kunena pagina die wordt getoond wanneer er op een forum link wordt geklikt of wanneer het forum aanvankelijk binnengekomen wordt. Standaard is Recente Discussies. Dient gezet te worden op Categori&euml;n voor andere templates dan default_ex. Indien Mijn Discussies is geselecteerd, zullen gasten standaard uitkomen bij Recente Discussies.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Recente Discussies');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Mijn Discussies');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categorien');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH', 'Avatar breedte');

// 1.0.5
DEFINE('_KUNENA_BBCODE_HIDE', 'Het volgende is verborgen voor niet geregistreerde gebruikers:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Waarschuwing spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Hoofd Forum mag niet hetzelfde zijn.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Hoofd Forum is één van zijn eigen subfora.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID bestaat niet.');
DEFINE('_KUNENA_RECURSION', 'Recursie gedetecteerd.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'U bent vergeten uw naam op te geven.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'U bent vergeten uw e-mail op te geven.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'U bent vergeten een onderwerp op te geven.');
DEFINE('_KUNENA_EDIT_TITLE', 'Wijzig uw Details');
DEFINE('_KUNENA_YOUR_NAME', 'Uw Naam:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'Gebruikersnaam:');
DEFINE('_KUNENA_PASS', 'Wachtwoord:');
DEFINE('_KUNENA_VPASS', 'Nogmaals Wachtwoord:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Gebruiker details zijn opgeslagen.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Instellingen');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Laat spoiler tag zien in de editor balk');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Zet op &quot;Ja&quot; indien u de [spoiler] tag binnen de post editor balk wilt zien.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Laat de video tag zien binnen de editor balk');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Zet op &quot;Ja&quot; indien u de [video] tag binnen de post editor balk wilt zien.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Laat de eBay tag zien binnen de editor balk');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Zet op &quot;Ja&quot; indien u de [eBay] tag binnen de post editor balk wilt zien.');
DEFINE('_COM_A_TRIMLONGURLS', 'Kort lange URLs in');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Zet op &quot;Ja&quot; indien u lange URLs wilt inkorten. Zie URL inkorten voorste en achterste deel instellingen.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Voorste deel van ingekorte URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Aantal karakters voor voorzijde onderdeel van ingekorte URLs. Kort lange URLs in moet op &quot;Ja&quot; zijn gezet.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Achterste deel van ingekorte URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Aantal karakters voor achterzijde onderdeel van ingekorte URLs. Kort lange URLs in moet op &quot;Ja&quot; zijn gezet.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Automatisch YouTube videos inbedden');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Zet op &quot;Ja&quot; indien u youtube video URLs automatisch wilt inbedden.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Automatisch eBay waren inbedden');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Zet op &quot;Ja&quot; indien u eBay waren en zoekopdrachten automatisch wilt inbedden.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay widget taal code');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Het is belangrijk om de juiste taal code te gebruiken aangezien de eBay widget zowel de taal als de valuta er van afleidt. Standaard is en-us voor ebay.com. Voorbeelden: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Sessie Levensduur');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Standaard is 1800 [seconden]. Sessie Levensduur (time-out) in seconden gelijk aan de Joomla Sessie Levensduur. De Sessie Levensduur is belangrijk voor toegangsrechten herberekening, whoisonline afbeelden en de NIEUW indicator. Wanneer een sessie verloopt voorbij die time-out, zullen de toegangsrechten en de NIEUW indicator worden gereset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Samenvoegen');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Voeg dit onderwerp samen met');
DEFINE('_POST_MERGE_GHOST', 'Bewaar een shaduw kopie van het onderwerp');
DEFINE('_POST_SUCCESS_MERGE', 'Onderwerp succesvol samengevoegd.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Samenvoegen mislukt.');
DEFINE('_GEN_SPLIT', 'Scheiden');
DEFINE('_GEN_DOSPLIT', 'Gaan');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Onderwerp succesvol gescheiden.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Onderwerp succesvol gewijzigd.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Onderwerp wijzigen mislukt.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Scheiden mislukt.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicaat, identieke boodschap is genegeerd geweest.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: U kunt een post promoten naar onderwerp positie indien u dit in de tweede kolom selecteert en niets aanduidt om te scheiden.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link wezen naar onderwerp');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link wezen naar nieuw onderwerp post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link wezen naar voorgaande post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link wezen naar voorgaande post.');
DEFINE('_POST_MERGE', 'samenvoegen');
DEFINE('_POST_MERGE_TITLE', 'Hecht dit onderwerp aan de eerste post van het doel onderwerp.');
DEFINE('_POST_INVERSE_MERGE', 'Omgekeerd samenvoegen');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Hecht de eerste post van het doel onderwerp aan dit onderwerp.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Dit onderwerp is verwijderd uit uw favorieten.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Dit onderwerp is <b>NIET</b> verwijderd uit uw favorieten');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Uw verzoek tot verwijdering uit favorieten is verwerkt.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Dit onderwerp is verwijderd uit uw subscripties.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Dit onderwerp is <b>NIET</b> verwijderd uit uw subscripties');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Uw verzoek tot verwijdering uit subscripties is verwerkt.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Geen bestemmingscategorie is geselecteerd. Er is niets verplaatst.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Recente Discussies');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Mijn Discussies');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussies die ik ben gestart of op heb geantwoord');
DEFINE('_KUNENA_CATEGORY', 'Categorie:');
DEFINE('_KUNENA_CATEGORIES', 'Home');
DEFINE('_KUNENA_POSTED_AT', 'Geplaatst');
DEFINE('_KUNENA_AGO', 'geleden');
DEFINE('_KUNENA_DISCUSSIONS', 'Discussies');
DEFINE('_KUNENA_TOTAL_THREADS', 'Totaal onderwerpen:');
DEFINE('_SHOW_DEFAULT', 'Standaard');
DEFINE('_SHOW_MONTH', 'Maand');
DEFINE('_SHOW_YEAR', 'Jaar');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Copi&euml;ren "%src%" naar "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Opslaan css bestand zou hier moeten gebeuren...bestand="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Bijlage tabel succesvol bijgewerkt naar de laatste 1.0.x serie structuur!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Bijlage in boodschappen tabel succesvol bijgewerkt naar de laatste 1.0.x serie structuur!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Kon geen subforum promoten binnen de post hi&euml;rarchie. Niets verwijderd.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Kon de post(s) niet verwijderen - Niets anders verwijderd');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Kon geen teksten van de post(s) verwijderen. Werk de database handmatig bij (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Alles verwijderd maar bijwerken gebruiker post statistieken mislukt!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Kritieke database fout. Werk uw database handmatig bij zodat de antwoorden op het onderwerp ook overeenkomen in het nieuwe forum");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena component was succesvol gedeinstalleerd!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena Forum Component versie: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Aangemaakt: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Geen forums om in te zoeken.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Fout bij toevoegen gebruikers:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Gebruikers gesynchroniseerd; Verwijderd:');
DEFINE('_KUNENA_USERSSYNCADD', ', toevoegen:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'Gebruikersprofielen.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Geen profielen gevonden bruikbaar voor synchroniseren.');
DEFINE('_KUNENA_SYNC_USERS', 'Synchronizeer Gebruikers');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synchronizeer Kunena gebruikerstabel met de Joomla! gebruikerstabel');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Email Administrators');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Zet op &quot;Ja&quot; indien u email notificaties wilt ontvangen van iedere nieuwe post verzonden door de systeem administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Wijzig Rang');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Verberg Email');
DEFINE('_KUNENA_DT_DATE_FMT','%d/%m/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Zondag');
DEFINE('_KUNENA_DT_LDAY_MON', 'Maandag');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Dinsdag');
DEFINE('_KUNENA_DT_LDAY_WED', 'Woensdag');
DEFINE('_KUNENA_DT_LDAY_THU', 'Donderdag');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Vrijdag');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Zaterdag');
DEFINE('_KUNENA_DT_DAY_SUN', 'Zo');
DEFINE('_KUNENA_DT_DAY_MON', 'Ma');
DEFINE('_KUNENA_DT_DAY_TUE', 'Di');
DEFINE('_KUNENA_DT_DAY_WED', 'Wo');
DEFINE('_KUNENA_DT_DAY_THU', 'Do');
DEFINE('_KUNENA_DT_DAY_FRI', 'Vr');
DEFINE('_KUNENA_DT_DAY_SAT', 'Za');
DEFINE('_KUNENA_DT_LMON_JAN', 'Januari');
DEFINE('_KUNENA_DT_LMON_FEB', 'Februari');
DEFINE('_KUNENA_DT_LMON_MAR', 'Maart');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'Mei');
DEFINE('_KUNENA_DT_LMON_JUN', 'Juni');
DEFINE('_KUNENA_DT_LMON_JUL', 'Juli');
DEFINE('_KUNENA_DT_LMON_AUG', 'Augustus');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'Oktober');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'December');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mrt');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'Mei');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Okt');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec');
DEFINE('_KUNENA_CHILD_BOARD', 'subforum');
DEFINE('_WHO_ONLINE_GUEST', 'Gast');
DEFINE('_WHO_ONLINE_MEMBER', 'Lid');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'geen');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Afbeelding Bewerker:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Klik hier om verder te gaan...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Toepassen!');
DEFINE('_KUNENA_NO_ACCESS', 'U heeft geen toegang tot dit Forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% geleden');
DEFINE('_KUNENA_DATE_YEARS', 'Jaren');
DEFINE('_KUNENA_DATE_MONTHS', 'Maanden');
DEFINE('_KUNENA_DATE_WEEKS','Weken');
DEFINE('_KUNENA_DATE_DAYS', 'Dagen');
DEFINE('_KUNENA_DATE_HOURS', 'Uren');
DEFINE('_KUNENA_DATE_MINUTES', 'Minuten');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Weet u zeker dat u de voorbeelddata wilt verwijderen? Dit kan niet ongedaan gemaakt worden.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumhoofding:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum weergave");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS klasse achtervoegsel");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS achtervoegsel wordt gebruikt bij index, showcat, view en laat verschillende layouts per forum toe.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Gebruiker wijzigingstijd');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Zet op 0 voor oneindig, anders het aantal seconden waarbinnen de gebruiker zijn bericht mag wijzigen
na de laatste wijziging van dat bericht.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Gebruikers wijzigingstijd gratie');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Standaard 600 [seconden], laat het opslaan van een wijziging toe tot 600 seconden na 
het verdwijnen van de wijzig knop.');
DEFINE('_KUNENA_HELPPAGE','Help pagina inschakelen');
DEFINE('_KUNENA_HELPPAGE_DESC','Zet op &quot;Ja&quot; om een link naar de help pagina in het header menu te tonen.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Toon help in Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Zet op &quot;Ja&quot; om de help tekst in Kunena te gebruiken, de externe help pagina link werkt dan niet. <b>Let op:</b> Je moet "Help tekst ID" invullen.');
DEFINE('_KUNENA_HELPPAGE_CID','Help tekst ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Vul <b>"JA"</b> in bij "Toon help in Kunena".');
DEFINE('_KUNENA_HELPPAGE_LINK',' Externe help pagina link');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Als je deze externe help pagina link wilt gebruiken, vul dan <b>"NEE"</b> in bij "Toon help in Kunena".');
DEFINE('_KUNENA_RULESPAGE','Regels pagina inschakelen');
DEFINE('_KUNENA_RULESPAGE_DESC','Zet op &quot;Ja&quot; om een link naar de regels pagina in het header menu te tonen.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Toon het reglement in fireboard');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Zet op &quot;Ja&quot; om de reglement tekst in Kunena te gebruiken, de externe regels pagina link werkt dan niet. <b>Let op:</b> Je moet "Regel tekst ID" invullen.');
DEFINE('_KUNENA_RULESPAGE_CID','Regel tekst ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Vul <b>"JA"</b> in bij "Toon regels in Kunena".');
DEFINE('_KUNENA_RULESPAGE_LINK',' Regels externe pagina link');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Als je deze externe regels pagina link wilt gebruiken, vul dan <b>"NEE"</b> in bij "Toon regels in Kunena".');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library niet gevonden');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD2 Library niet gevonden');
DEFINE('_KUNENA_GD_INSTALLED','Beschikbare GD versie ');
DEFINE('_KUNENA_GD_NO_VERSION','Kan GD versie niet detecteren');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD is niet ge&iuml;nstalleerd, meer informatie ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Kleine afbeelding hoogte :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Kleine afbeelding breedte :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium afbeelding hoogte :');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','Medium afbeelding breedte :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Grote afbeelding hoogte :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Grote afbeelding breedte :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar Kwaliteit');
DEFINE('_KUNENA_WELCOME','Welkom bij Kunena');
DEFINE('_KUNENA_WELCOME_DESC','Bedankt voor uw keuze voor Kunena als uw forum. Dit scherm geeft u een snel overzicht van alle verschillende statistieken van uw forum. Via de links aan de linkerkant van uw scherm kunt u alle aspecten van uw forum aanpassen. Elke pagina bevat een uitleg hoe die pagina gebruikt kan worden.');
DEFINE('_KUNENA_STATISTIC','Statistiek');
DEFINE('_KUNENA_VALUE','Waarde');
DEFINE('_GEN_CATEGORY','Categorie');
DEFINE('_GEN_STARTEDBY','Gestart door: ');
DEFINE('_GEN_STATS','stats');
DEFINE('_STATS_TITLE',' forum - stats');
DEFINE('_STATS_GEN_STATS','Algemene stats');
DEFINE('_STATS_TOTAL_MEMBERS','Leden:');
DEFINE('_STATS_TOTAL_REPLIES','Reacties:');
DEFINE('_STATS_TOTAL_TOPICS','Onderwerpen:');
DEFINE('_STATS_TODAY_TOPICS','Onderwerpen vandaag:');
DEFINE('_STATS_TODAY_REPLIES','Reacties vandaag:');
DEFINE('_STATS_TOTAL_CATEGORIES','Categorie&euml;n:');
DEFINE('_STATS_TOTAL_SECTIONS','Secties:');
DEFINE('_STATS_LATEST_MEMBER','Nieuwste lid:');
DEFINE('_STATS_YESTERDAY_TOPICS','Onderwerpen gisteren:');
DEFINE('_STATS_YESTERDAY_REPLIES','Reacties gisteren:');
DEFINE('_STATS_POPULAR_PROFILE','10 populairste leden (Profiel bezichtigingen)');
DEFINE('_STATS_TOP_POSTERS','Meeste berichten');
DEFINE('_STATS_POPULAR_TOPICS','Populairste onderwerpen');
DEFINE('_COM_A_STATSPAGE','Stats pagina inschakelen');
DEFINE('_COM_A_STATSPAGE_DESC','Zet op &quot;Ja&quot; om een link naar je forums stats pagina in het publieke header menu te tonen. Deze pagina zal verschillende statistieken van je forum tonen. <em>De stats pagina zal altijd beschikbaar zijn voor de admin, ongeacht deze instelling!</em>');
DEFINE('_COM_C_JBSTATS','Forum Stats');
DEFINE('_COM_C_JBSTATS_DESC','Forum Statistieken');
define('_GEN_GENERAL','Algemeen');
define('_PERM_NO_READ','Je hebt niet voldoende rechten om dit forum te bekijken.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smiley opgeslagen');
DEFINE ('_KUNENA_SMILEY_DELETED','Smiley verwijderd');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Code bestaat reeds');
DEFINE ('_KUNENA_MISSING_PARAMETER','Er ontbreekt een parameter');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rang bestaat al');
DEFINE ('_KUNENA_RANK_DELETED','Rang verwijderd');
DEFINE ('_KUNENA_RANK_SAVED','Rang opgeslagen');
DEFINE ('_KUNENA_DELETE_SELECTED','Verwijder geselecteerden');
DEFINE ('_KUNENA_MOVE_SELECTED','Verplaats geselecteerden');
DEFINE ('_KUNENA_REPORT_LOGGED','Gelogd');
DEFINE ('_KUNENA_GO','Ga');
DEFINE('_KUNENA_MAILFULL','Voeg het volledige bericht toe bij emails verstuurd naar geabonneerden.');
DEFINE('_KUNENA_MAILFULL_DESC','Bij &quot;Nee&quot; zullen aboneerders alleen de titels van nieuwe berichten ontvangen');
DEFINE('_KUNENA_HIDETEXT','Log a.u.b. in om deze inhoud te bekijken!');
DEFINE('_BBCODE_HIDE','Verborgen tekst: [hide]een verborgen tekst[/hide] - verberg een deel van het bericht voor gasten');
DEFINE('_KUNENA_FILEATTACH','Bijlage: ');
DEFINE('_KUNENA_FILENAME','Bestandsnaam: ');
DEFINE('_KUNENA_FILESIZE','Bestandsgrootte: ');
DEFINE('_KUNENA_MSG_CODE','Code: ');
DEFINE('_KUNENA_CAPTCHA_ON','Spam preventie systeem');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA systeem Aan/Uit');
DEFINE('_KUNENA_CAPDESC','Vul de code hier in');
DEFINE('_KUNENA_CAPERR','Code niet correct!');
DEFINE('_KUNENA_COM_A_REPORT', 'Bericht rapportage');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Als u gebruikers de mogelijkheid wil geven om berichten te rapporteren, kies &quot;Ja&quot;.');
DEFINE('_KUNENA_REPORT_MSG', 'Bericht gerapporteerd');
DEFINE('_KUNENA_REPORT_REASON', 'Reden');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Uw bericht');
DEFINE('_KUNENA_REPORT_SEND', 'Verzend rapport');
DEFINE('_KUNENA_REPORT', 'Rapporteer aan moderator');
DEFINE('_KUNENA_REPORT_RSENDER', 'Rapport Verstuurder: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Rapport Reden: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Rapport Bericht: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Bericht auteur: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Bericht onderwerp: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Bericht: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Bericht link: ');
DEFINE('_KUNENA_REPORT_INTRO', 'heeft u een bericht gestuurd, omdat');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Rapport succesvol verstuurd!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Code');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Wijzig Smiley');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Wijzig Smilies');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBalk');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nieuwe Smiley');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Meer Smilies');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Sluit scherm');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Extra emoticons');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Kies een smiley');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Joomla Mambot Support Inschakelen');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Instellingen');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Sta gebruikersnaam wijziging toe');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Sta het wijzigen van de gebruikersnaam op de myprofile plugin pagina toe.');
DEFINE ('_KUNENA_RECOUNTFORUMS','Herbereken Categorie Stats');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Alle categorie statistieken zijn opnieuw berekend.');
DEFINE ('_KUNENA_EDITING_REASON','Reden voor wijziging');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Laatste Wijziging');
DEFINE ('_KUNENA_BY','Door');
DEFINE ('_KUNENA_REASON','Reden');
DEFINE('_GEN_GOTOBOTTOM', 'Ga naar onderkant');
DEFINE('_GEN_GOTOTOP', 'Ga naar bovenkant');
DEFINE('_STAT_USER_INFO', 'Gebruikersinformatie');
DEFINE('_USER_SHOWEMAIL', 'Toon email');
DEFINE('_USER_SHOWONLINE', 'Toon Online');
DEFINE('_KUNENA_HIDDEN_USERS', 'Verborgen gebruikers');
DEFINE('_KUNENA_SAVE', 'Opslaan');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Standaard gallerij');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Persoonlijke informatie');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Samenvatting');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Mijn Avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum Instellingen');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Uiterlijke weergave');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Mijn Profiel Info');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Mijn Berichten');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Mijn Abonnementen');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Mijn Favorieten');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Privé Berichten');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Inbox');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nieuw bericht');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Outbox');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Prullenbak');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Instellingen');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Contacten');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Geblokkeerdenlijst');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Extra informatie');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Naam');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Gebruikersnaam');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Email');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Gebruikerstype');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Registratie datum');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Laatste bezoek');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Berichten');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profiel bezoeken');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Persoonlijk bericht');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Geslacht');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Geboortedatum');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Jaar (YYYY) - Maand (MM) - Dag (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Locatie');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Dit is je ICQ number.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Dit is je AOL Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Dit is je Yahoo! Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Dit is je Skype gebruikersnaam.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Dit is je Gtalk nickname.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Website');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Website Naam');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Voorbeeld: Best of Joomla!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Website URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Voorbeeld: www.bestofjoomla.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Je MSN messenger emailadres.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Handtekening');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Man');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Vrouw');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Berichten succesvol verwijderd');
DEFINE('_KUNENA_DATE_YEAR', 'Jaar');
DEFINE('_KUNENA_DATE_MONTH', 'Maand');
DEFINE('_KUNENA_DATE_WEEK','Week');
DEFINE('_KUNENA_DATE_DAY', 'Dag');
DEFINE('_KUNENA_DATE_HOUR', 'Uur');
DEFINE('_KUNENA_DATE_MINUTE', 'Minuut');
DEFINE('_KUNENA_IN_FORUM', ' in Forum: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum op: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Let op, hoewel forumcode en smiley knoppen niet worden weergegeven, kunnen ze nog wel gebruikt worden');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum Tools');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Gebruikerslijst');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s heeft <b>%d</b> geregistreerde gebruikers');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Vul een waarde in naar waar gezocht moet worden!');
DEFINE ('_KUNENA_USRL_SEARCH','Vind gebruiker');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Zoeken');
DEFINE ('_KUNENA_USRL_LIST_ALL','Toon alles');
DEFINE ('_KUNENA_USRL_NAME','Naam');
DEFINE ('_KUNENA_USRL_USERNAME','Gebruikersnaam');
DEFINE ('_KUNENA_USRL_GROUP','Groep');
DEFINE ('_KUNENA_USRL_POSTS','Berichten');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Hits');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Gebruikerstype');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Lid sinds');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Laatst ingelogd');
DEFINE ('_KUNENA_USRL_NEVER','Nooit');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Afbeelding');
DEFINE ('_KUNENA_USRL_ASC','oplopend');
DEFINE ('_KUNENA_USRL_DESC','aflopend');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Toon');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d/%m/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Gebruikerslijst');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Aantal rijen in gebruikerslijst');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Aantal rijen in gebruikerslijst');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online Status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Toon andere gebruikers uw online status');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Toon avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Toon echte naam');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Toon gebruikersnaam');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Toon gebruikersgroep');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Toon aantal berichten');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Toon Karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Toon E-mail');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Toon gebruikersype');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Toon registratiedatum');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Toon laatst bezochte datum');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Toon profielhits');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Database wizard');
DEFINE('_KUNENA_DBMETHOD', 'Selecteer de methode om uw installatie te voltooien:');
DEFINE('_KUNENA_DBCLEAN', 'Nieuwe installatie');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade van Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Top Level categorie');
DEFINE('_KUNENA_REGISTERED', 'Geregistreerde leden');
DEFINE('_KUNENA_PUBLICBACKEND', 'Publieke backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Selecteer een item om te');
DEFINE('_KUNENA_ERRORSUBS', 'Er ging iets mis bij het verwijderen van de berichten en inschrijvingen');
DEFINE('_KUNENA_WARNING', 'Waarschuwing...');
DEFINE('_KUNENA_CHMOD1', 'U dient dit te chmodden naar 766 voor dat het bestand geupdate kan worden.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Uw configuratiebestand is');
DEFINE('_KUNENA_FIREBOARD', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Selecteer template');
DEFINE('_KUNENA_CFNW', 'FATALE FOUT: Configuratiebestand NIET schrijfbaar');
DEFINE('_KUNENA_CFS', 'Configuratie succesvol opgeslagen');
DEFINE('_KUNENA_CFCNBO', 'FATALE FOUT: Bestand kon niet geopend worden.');
DEFINE('_KUNENA_TFINW', 'Het bestand is niet schrijfbaar.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS bestand opgeslagen.');
DEFINE('_KUNENA_SELECTMODTO', 'Selecteer een moderator om te');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'u dient een forum te kiezen om op te kuisen!');
DEFINE('_KUNENA_DELMSGERROR', 'Verwijderen van berichten mislukt:');
DEFINE('_KUNENA_DELMSGERROR1', 'Verwijderen van berichtteksten mislukt.');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Verwijderen van abonnementen mislukt:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum opgekuist voor');
DEFINE('_KUNENA_PRUNEDAYS', 'dagen');
DEFINE('_KUNENA_PRUNEDELETED', 'Verwijderd:');
DEFINE('_KUNENA_PRUNETHREADS', 'onderwerpen');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Fout bij opkuisen gebruikers:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Gebruikers opgekuist; Verwijderd:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'gebruikersprofielen');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Geen profielen gevonden die in aanmerking komen voor opkuising.');
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena tabellen zijn geupgrade naar versie');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forumcategorie');
DEFINE('_KUNENA_SAMPLWARN1', '-- Wees er zeker van dat je de voorbeelddata ENKEL laad op volledig lege Kunena tabellen. Het zal niet werken als deze niet leeg zijn!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Voorbeeldbericht 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Sample Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Voorbeeldbericht[/color][/size][/b]\nProficiat met het nieuwe forum met de Nederlandstalige vertaling door Joomla! Nederlands!!\n\n[url=http://joomlacode.org/gf/nederlands]- Joomla! Nederlands[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Voorbeelddata ingeladen');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Voorbeelddata verwijderd');
DEFINE('_KUNENA_CBADDED', 'Community Builder profielen toegevoegd');
DEFINE('_KUNENA_IMGDELETED', 'Afbeelding verwijderd');
DEFINE('_KUNENA_FILEDELETED', 'Bestand verwijderd');
DEFINE('_KUNENA_NOPARENT', 'Geen parentforum');
DEFINE('_KUNENA_DIRCOPERR', 'Fout: Bestand');
DEFINE('_KUNENA_DIRCOPERR1', 'kon niet gekopieerd worden!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> Component <em>voor Joomla! CMS</em> <br />&copy; 2008 - 2009 door www.kunena.com<br>Alle rechten voorbehouden.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installatie :</code></strong><br /><br /><font color="red"><b>succesvol');
// added by aliyar 
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profielinstellingen');
DEFINE('_KUNENA_FORUMPRF', 'Profiel');
DEFINE('_KUNENA_FORUMPRRDESC', 'Indien u Clexus PM of Community Builder component ge&iuml;nstalleerd heeft kunt u Kunena configureren om deze gebruikersprofielen te gebruiken.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profiel');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profielhits</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Alle forumberichten');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Onderwerpen');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Gestart door');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Categorie&euml;n');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Datum');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Hits');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Geen forumberichten');
DEFINE('_KUNENA_TOTALFAVORITE', 'Favoriet:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Aantal subfora kolommen  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Aantal subfora kolommen formattering onder de hoofdcategorie ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Berichtabonnering standaard selecteren?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Stel in op &quot;ja&quot; indien het selectievakje voor abonnementen op een bericht altijd moet aangevinkt zijn');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Categorie / forum moet een naam hebben');
// Forum Configuration (New in Fireboard)
DEFINE('_KUNENA_SHOWSTATS', 'Toon stats');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Indien je stats wenst weer te geven, stel je dit in op &quot;Ja&quot;');
DEFINE('_KUNENA_SHOWWHOIS', 'Toon wie er online is');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Indien je wil zien wie er online is, stel je dit in op &quot;Ja&quot;');
DEFINE('_KUNENA_STATSGENERAL', 'Toon algemene statistieken');
DEFINE('_KUNENA_STATSGENERALDESC', 'Indien je algemene statistieken wenst weer te geven, stel je dit in op &quot;Ja&quot;');
DEFINE('_KUNENA_USERSTATS', 'Toon populaire gebruikersstatistieken');
DEFINE('_KUNENA_USERSTATSDESC', 'Indien je de populaire gebruikersstatistieken wenst weer te geven, stel je dit in op &quot;Ja&quot;');
DEFINE('_KUNENA_USERNUM', 'Aantal populaire gebruikers');
DEFINE('_KUNENA_USERPOPULAR', 'Toon populaire onderwerpen');
DEFINE('_KUNENA_USERPOPULARDESC', 'Indien je populaire onderwerpen wenst weer te geven, stel je dit in op &quot;Ja&quot;');
DEFINE('_KUNENA_NUMPOP', 'Aantal populaire onderwerpen');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.5. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be released pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instructies');
DEFINE('_KUNENA_FINFO', 'Kunena forum informatie');
DEFINE('_KUNENA_CSSEDITOR', 'Kunena template CSS editor');
DEFINE('_KUNENA_PATH', 'Pad:');
DEFINE('_KUNENA_CSSERROR', 'Opmerking: CSS template bestand moet schrijfbaar zijn om wijzigingen te kunnen opslaan.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena gebruikersprofielbeheer');
DEFINE('_KUNENA_SORTID', 'sorteer op userid');
DEFINE('_KUNENA_SORTMOD', 'sorteer op moderatoren');
DEFINE('_KUNENA_SORTNAME', 'sorteer op namen');
DEFINE('_KUNENA_VIEW', 'Bekijk');
DEFINE('_KUNENA_NOUSERSFOUND', 'Geen gebruikersprofielen gevonden.');
DEFINE('_KUNENA_ADDMOD', 'Moderator toevoegen aan');
DEFINE('_KUNENA_NOMODSAV', 'Er zijn geen mogelijke moderatoren gevonden. Lees de opmerking hieronder.');
DEFINE('_KUNENA_NOTEUS',
    'Opmerking: Enkel gebruikers waarbij de moderatievlag werd ingesteld in hun Kunena profiel worden hier getoond. Om dit te doen ga je naar <a href="index2.php?option=com_kunena&task=profiles">gebruikersadministratie</a> aen zoek voor de gebruiker die je tot moderator wil promoveren. Selecteer zijn of haar profiel en update deze. De moderatorvlag kan enkel ingesteld worden door een administrator. ');
DEFINE('_KUNENA_PROFFOR', 'Profiel voor');
DEFINE('_KUNENA_GENPROF', 'Algemene profielopties');
DEFINE('_KUNENA_PREFVIEW', 'Gewenste weergave:');
DEFINE('_KUNENA_PREFOR', 'Gewenste rangschikking berichten:');
DEFINE('_KUNENA_ISMOD', 'Is moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Ja</strong> (kan niet veranderd worden aangezien deze gebruiker een (super)administrator is)');
DEFINE('_KUNENA_COLOR', 'Kleur');
DEFINE('_KUNENA_UAVATAR', 'Avatar gebruiker:');
DEFINE('_KUNENA_NS', 'Geen geselecteerd');
DEFINE('_KUNENA_DELSIG', ' vink dit vakje aan om de handtekening van deze gebruiker te wissen');
DEFINE('_KUNENA_DELAV', ' vink dit vakje aan om de avatar van deze gebruiker te verwijderen');
DEFINE('_KUNENA_SUBFOR', 'Geabonneerd op');
DEFINE('_KUNENA_NOSUBS', 'Geen abonnementen gevonden voor deze gebruiker');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Algemeen');
DEFINE('_KUNENA_BASICSFORUM', 'Algemene forum informatie');
DEFINE('_KUNENA_PARENT', 'Parent:');
DEFINE('_KUNENA_PARENTDESC',
    'Opmerking: Om een categorie aan te maken kiest u \'Top level categorie\' als parent. Een categorie dient als een container voor fora.<br />Een forum kan <strong>enkel</strong> aangemaakt worden binnen een categorie door het selecteren van een eerder aangemaakte categorie als parent van het forum.<br /> Berichten kunnen <strong>NIET</strong> geplaatst worden in een categorie, enkel in fora.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Forumnaam en omschrijving');
DEFINE('_KUNENA_NAMEADD', 'Naam:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Omschrijving:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Geavanceerde forumconfiguratie');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Forumbeveiliging en toegang');
DEFINE('_KUNENA_LOCKEDDESC', 'Stel in op &quot;Ja&quot; als u dit forum wil sluiten. Niemand, behalve moderatoren en administratoren kunnen nieuwe topics of antwoorden plaatsen in een gesloten forum (of berichten ernaar verplaatsen).');
DEFINE('_KUNENA_LOCKED1', 'Gesloten:');
DEFINE('_KUNENA_PUBACC', 'Public Access Level:');
DEFINE('_KUNENA_PUBACCDESC',
    'Om een niet-publiek forum aan te maken, kan je het minimum gebruikersniveau selecteren dat het forum mag zien. Standaard gebruikersniveau is ingesteld op &quot;Iedereen&quot;.<br /><b>Merk op</b>: indien u de toegang beperkt tot een volledige categorie, zullen alle fora in deze categorie verborgen zijn voor gebruikers die lager zijn dan de ingestelde rang, zelfs als deze fora op een lagere gebruikersrang zijn ingesteld! Dit geld ook voor moderatoren, u dient deze personen toe te voegen aan de moderatorlijst van de categorie wanneer deze niet het juiste gebruikersniveau heeft om de categorie te zien.<br /> Dit staat los van het feit dat categori&euml;en niet gemodereerd kunnen worden; Moderators kunnen altijd toegevoegd worden aan de moderatorenlijst.');
DEFINE('_KUNENA_CGROUPS', 'Subgroepen meerekenen:');
DEFINE('_KUNENA_CGROUPSDESC', 'Dienen subgroepen ook toegang te hebben? Indien &quot;Nee&quot; zal de toegang beperkt zijn tot de geselecteerde groep');
DEFINE('_KUNENA_ADMINLEVEL', 'Admin toegangsniveau:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Indien u een forum aanmaakt met restricties op de publieke toegang, kunt u hier additionele administratietoegangsniveau\'s selecteren.<br /> Indien u dit beperkt tot een bepaalde frontend gebruikersgroep en geen publieke backend groep selecteert, zullen administrators het forum niet kunnen bekijken.');
DEFINE('_KUNENA_ADVANCED', 'Geavanceerd');
DEFINE('_KUNENA_CGROUPS1', 'Subgroepen meerekenen:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Dienen subgroepen ook toegang te hebben? Indien &quot;Nee&quot; zal de toegang beperkt zijn tot de geselecteerde groep');
DEFINE('_KUNENA_REV', 'Review berichten:');
DEFINE('_KUNENA_REVDESC',
    'Stel in op &quot;Ja&quot; als de posts eerst beoordeeld moeten worden door moderatoren alvorens ze gepubliceerd worden in dit forum. Dit is vooral handig op een uitsluitend gemodereerd forum!<br />Indien je dit instelt zonder moderatoren, zal de administrator alleen verantwoordelijk zijn voor het goedkeuren/verwijderen van nieuwe berichten aangezien deze zich in de wachtrij zullen bevinden\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderatie');
DEFINE('_KUNENA_MODNEWDESC', 'Moderatie van het forum en forum moderatoren');
DEFINE('_KUNENA_MOD', 'Gemodereerd:');
DEFINE('_KUNENA_MODDESC',
    'Stel in op &quot;Ja&quot; als u moderatoren wil toewijzen aan dit forum.<br /><strong>Opmerking:</strong> Dit betekend niet dat een post beoordeeld moet worden voordat het gepubliceerd kan worden op het forum!<br /> U dient dit in te stellen via de &quot;Review&quot; optie op het tabblad geavanceerd.<br /><br /> <strong>Merk ook op:</strong> Na het instellen op &quot;Ja&quot; dient u de forumconfiguratie eerst op te slaan vooraleer je moderatoren kan toewijzen via de nieuw knop bovenaan.');
DEFINE('_KUNENA_MODHEADER', 'Moderatie instellingen voor dit forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatoren toegewezen aan dit forum:');
DEFINE('_KUNENA_NOMODS', 'Er zijn geen moderatoren toegewezen aan dit forum');
// Some General Strings (Improvement in Fireboard)
DEFINE('_KUNENA_EDIT', 'Wijzigingen');
DEFINE('_KUNENA_ADD', 'Toevoegen');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Naar boven');
DEFINE('_KUNENA_MOVEDOWN', 'Naar beneden');
// Groups - Integration in Fireboard
DEFINE('_KUNENA_ALLREGISTERED', 'Alle leden');
DEFINE('_KUNENA_EVERYBODY', 'Iedereen');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Herrangschik');
DEFINE('_KUNENA_CHECKEDOUT', 'Uitchecken');
DEFINE('_KUNENA_ADMINACCESS', 'Administratortoegang');
DEFINE('_KUNENA_PUBLICACCESS', 'Publieke toegang');
DEFINE('_KUNENA_PUBLISHED', 'Gepubliceerd');
DEFINE('_KUNENA_REVIEW', 'Review');
DEFINE('_KUNENA_MODERATED', 'Gemodereerd');
DEFINE('_KUNENA_LOCKED', 'Gesloten');
DEFINE('_KUNENA_CATFOR', 'Categorie / Forum');
DEFINE('_KUNENA_ADMIN', '&nbsp;Kunena administratie');
DEFINE('_KUNENA_CP', '&nbsp;Kunena Controlepaneel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar Integratie');
DEFINE('_COM_A_RANKS_SETTINGS', 'Rangen');
DEFINE('_COM_A_RANKING_SETTINGS', 'Ranginstellingen');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Avatar instellingen');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Beveiligingsinstellingen');
DEFINE('_COM_A_BASIC_SETTINGS', 'Basisinstellingen');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES','Favorieten toestaan');
DEFINE('_COM_A_FAVORITES_DESC','Stel in op &quot;Ja&quot; indien u geregistreerde gebruikers wil toestaan om een topic als favoriet aan te duiden ');
DEFINE('_USER_UNFAVORITE_ALL','Vink het selectievak aan om alle favoriete topics <b><u>te verwijderen</u></b> (inclusief onzichtbare topics bedoeld voor het opsporen en oplossen van problemen)');
DEFINE('_VIEW_FAVORITETXT','Voeg dit topic toe aan uw favorieten');
DEFINE('_USER_UNFAVORITE_YES','Dit topic is verwijdert van uw favorieten');
DEFINE('_POST_FAVORITED_TOPIC','Het onderwerp is toegevoegd aan uw favorieten');
DEFINE('_VIEW_UNFAVORITETXT','Verwijder Favoriet');
DEFINE('_VIEW_UNSUBSCRIBETXT','Annuleer Abonnement');
DEFINE('_USER_NOFAVORITES','Geen favorieten');
DEFINE('_POST_SUCCESS_FAVORITE','Uw favoriet aanvraag werd verwerkt.');
DEFINE('_COM_A_MESSAGES_SEARCH','Zoekresultaten');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH','Berichten per pagina op Zoekresultaten');
DEFINE('_KUNENA_USE_JOOMLA_STYLE','Gebruik de Joomla stijl?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Indien u de Joomla stijl wenst te gebruiken, selecteert u &quot;Ja&quot;. (Klasse: zoals sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST','Toon afbeelding onderliggende categorie&euml;en');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC','Indien u niet wenst dat de iconen van de onderliggende categorie&euml;en getoond worden, stelt u dit in op &quot;Ja&quot;. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','Toon Aankondigingen');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Stel in op &quot;Ja&quot;, indien u een aankondigingskader op de forum hoofdpagina wil zien.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Toon avatar in categorielijst');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Stel in op &quot;Ja&quot;, indien u de avatar van de gebruiker in de forumcategorielijst wil zien.');
DEFINE('_KUNENA_RECENT_POSTS','Recente berichten instellingen');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','Toon recente berichten');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','Stel in op &quot;Ja&quot; indien u de recente berichten wilt weergeven op het forum');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','Aantal recente berichten');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','Vul het aantal recente berichten in die getoond moeten worden');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Aantal per tab');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','Het aantal berichten per tab');
DEFINE('_KUNENA_LATEST_CATEGORY','Toon categorie');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','Specifieke categorie die je kan tonen bij de recente berichten. Bijvoorbeeld:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Toon enkel onderwerp');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','Toon enkel onderwerp');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Toon antwoord onderwerp');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','Toon antwoord onderwerp (Re:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','Onderwerp lengte');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','Onderwerp lengte');
DEFINE('_KUNENA_SHOW_LATEST_DATE','Toon datum');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC','Toon datum');
DEFINE('_KUNENA_SHOW_LATEST_HITS','Toon hits');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC','Toon hits');
DEFINE('_KUNENA_SHOW_AUTHOR','Toon auteur');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC','1=gebruikersnaam, 2=echtenaam, 0=geen');
DEFINE('_KUNENA_STATS','Statistieken plugin instellingen ');
DEFINE('_KUNENA_CATIMAGEPATH','Categorie afbeeldingenpad ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','Categorie afbeeldingenpad. Indien ingesteld op "category_images/" zal het pad "components/com_fireboard/category_images/ zijn');
DEFINE('_KUNENA_ANN_MODID','Aankondigingen Moderator IDs ');
DEFINE('_KUNENA_ANN_MODID_DESC','Voeg gebruikers IDs toe voor moderatie van de aankondigingen. Vb. 62,63,73 . Deze kunnen aankondigingen toevoegen, aanpassen en verwijderen.');
//
DEFINE('_KUNENA_FORUM_TOP','Board Categorie&euml;n ');
DEFINE('_KUNENA_CHILD_BOARDS','Onderliggende Boards ');
DEFINE('_KUNENA_QUICKMSG','Snel antwoorden ');
DEFINE('_KUNENA_THREADS_IN_FORUM','Onderwerpen in forum ');
DEFINE('_KUNENA_FORUM','Forum ');
DEFINE('_KUNENA_SPOTS','Schijnwerpers');
DEFINE('_KUNENA_CANCEL','annuleren');
DEFINE('_KUNENA_TOPIC','ONDERWERP: ');
DEFINE('_KUNENA_POWEREDBY','');
// Time Format
DEFINE('_TIME_TODAY','<b>Vandaag</b> ');
DEFINE('_TIME_YESTERDAY','<b>Gisteren</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS','Laatste berichten');
DEFINE('_KUNENA_WHO_WHOISONLINE','Wie is er online');
DEFINE('_KUNENA_WHO_MAINPAGE','Forum hoofdpagina');
DEFINE('_KUNENA_GUEST','Gast');
DEFINE('_KUNENA_PATHWAY_VIEWING','bezoeker(s)');
DEFINE('_KUNENA_ATTACH','Bijlage');
// Favorite
DEFINE('_KUNENA_FAVORITE','Favoriet');
DEFINE('_USER_FAVORITES','Uw favorieten');
DEFINE('_THREAD_UNFAVORITE','Verwijder als favoriet');
// profilebox
DEFINE('_PROFILEBOX_WELCOME','Welkom');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS','Toon laatste berichten');
DEFINE('_PROFILEBOX_SET_MYAVATAR','Avatar instellen');
DEFINE('_PROFILEBOX_MYPROFILE','Mijn profiel');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS','Toon mijn berichten');
DEFINE('_PROFILEBOX_GUEST','Gast');
DEFINE('_PROFILEBOX_LOGIN','Inloggen');
DEFINE('_PROFILEBOX_REGISTER','Registreren');
DEFINE('_PROFILEBOX_LOGOUT','Afmelden');
DEFINE('_PROFILEBOX_LOST_PASSWORD','Wachtwoord verloren?');
DEFINE('_PROFILEBOX_PLEASE','Alstublieft');
DEFINE('_PROFILEBOX_OR','of');
// recentposts
DEFINE('_RECENT_RECENT_POSTS','Recente berichten');
DEFINE('_RECENT_TOPICS','Topic');
DEFINE('_RECENT_AUTHOR','Auteur');
DEFINE('_RECENT_CATEGORIES','Categori&euml;en');
DEFINE('_RECENT_DATE','Datum');
DEFINE('_RECENT_HITS','Hits');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Aankondigingen');
DEFINE('_ANN_ID','ID');
DEFINE('_ANN_DATE','Datum');
DEFINE('_ANN_TITLE','Titel');
DEFINE('_ANN_SORTTEXT','Sorteer tekst');
DEFINE('_ANN_LONGTEXT','Lange Tekst');
DEFINE('_ANN_ORDER','Rangschik');
DEFINE('_ANN_PUBLISH','Publiceer');
DEFINE('_ANN_PUBLISHED','Gepubliceerd');
DEFINE('_ANN_UNPUBLISHED','Niet gepubliceerd');
DEFINE('_ANN_EDIT','Wijzig');
DEFINE('_ANN_DELETE','Verwijder');
DEFINE('_ANN_SUCCESS','Succesvol');
DEFINE('_ANN_SAVE','Opslaan');
DEFINE('_ANN_YES','Ja');
DEFINE('_ANN_NO','Nee');
DEFINE('_ANN_ADD','Nieuw toevoegen');
DEFINE('_ANN_SUCCESS_EDIT','Succesvol aangepast');
DEFINE('_ANN_SUCCESS_ADD','Succesvol toegevoegd');
DEFINE('_ANN_DELETED','Succesvol verwijderd');
DEFINE('_ANN_ERROR','FOUT');
DEFINE('_ANN_READMORE','Lees verder...');
DEFINE('_ANN_CPANEL','Controlepaneel aankondigingen');
DEFINE('_ANN_SHOWDATE','Toon datum');
// Stats
DEFINE('_STAT_FORUMSTATS','Forumstatistieken');
DEFINE('_STAT_GENERAL_STATS','Algemene statistieken');
DEFINE('_STAT_TOTAL_USERS','Totaal aantal leden');
DEFINE('_STAT_LATEST_MEMBERS','Nieuwste lid');
DEFINE('_STAT_PROFILE_INFO','Bekijk profielinfo');
DEFINE('_STAT_TOTAL_MESSAGES','Totaal aantal berichten');
DEFINE('_STAT_TOTAL_SUBJECTS','Totaal aantal onderwerpen');
DEFINE('_STAT_TOTAL_CATEGORIES','Totaal aantal categori&euml;en');
DEFINE('_STAT_TOTAL_SECTIONS','Totaal aantal secties');
DEFINE('_STAT_TODAY_OPEN_THREAD','Vandaag open');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD','Gisteren open');
DEFINE('_STAT_TODAY_TOTAL_ANSWER','Vandaag totaal beantwoord');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER','Gisteren totaal beantwoord');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM','Bekijk recente berichten');
DEFINE('_STAT_MORE_ABOUT_STATS','Meer over de statistieken');
DEFINE('_STAT_USERLIST','Gebruikerslijst');
DEFINE('_STAT_TEAMLIST','De Leiding');
DEFINE('_STATS_FORUM_STATS','Forumstatistieken');
DEFINE('_STAT_POPULAR','Populair');
DEFINE('_STAT_POPULAR_USER_TMSG','Gebruikers ( Totaal aantal berichten) ');
DEFINE('_STAT_POPULAR_USER_KGSG','Onderwerpen ');
DEFINE('_STAT_POPULAR_USER_GSG','Gebruikers ( Totaal aantal profielviews) ');
//Team List
DEFINE('_MODLIST_ONLINE','Gebruiker nu online');
DEFINE('_MODLIST_OFFLINE','Gebruiker offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE','Wie is online');
DEFINE('_WHO_ONLINE_NOW','Online');
DEFINE('_WHO_ONLINE_MEMBERS','Leden');
DEFINE('_WHO_AND','en');
DEFINE('_WHO_ONLINE_GUESTS','Gasten');
DEFINE('_WHO_ONLINE_USER','Gebruiker');
DEFINE('_WHO_ONLINE_TIME','Tijd');
DEFINE('_WHO_ONLINE_FUNC','Actie');
// Userlist
DEFINE ('_USRL_USERLIST','Gebruikerslijst');
DEFINE ('_USRL_REGISTERED_USERS','%s heeft <b>%d</b> geregistreerde gebruikers');
DEFINE ('_USRL_SEARCH_ALERT','Vul een waarde in om naar te zoeken!');
DEFINE ('_USRL_SEARCH','Zoek gebruiker');
DEFINE ('_USRL_SEARCH_BUTTON','Zoeken');
DEFINE ('_USRL_LIST_ALL','Toon alles');
DEFINE ('_USRL_NAME','Naam');
DEFINE ('_USRL_USERNAME','Gebruikersnaam');
DEFINE ('_USRL_EMAIL','E-mail');
DEFINE ('_USRL_USERTYPE','Gebruikerstype');
DEFINE ('_USRL_JOIN_DATE','Lid sinds');
DEFINE ('_USRL_LAST_LOGIN','Laatst ingelogd');
DEFINE ('_USRL_NEVER','Nooit');
DEFINE ('_USRL_BLOCK','Status');
DEFINE ('_USRL_MYPMS2','Mijn privé berichten');
DEFINE ('_USRL_ASC','Oplopend');
DEFINE ('_USRL_DESC','Aflopend');
DEFINE ('_USRL_DATE_FORMAT','%d.%m.%Y');
DEFINE ('_USRL_TIME_FORMAT','%H:%M');
DEFINE ('_USRL_USEREXTENDED','Details');
DEFINE ('_USRL_COMPROFILER','Profiel');
DEFINE ('_USRL_THUMBNAIL','Pic');
DEFINE ('_USRL_READON','toon');
DEFINE ('_USRL_MYPMSPRO','Clexus PM');
DEFINE ('_USRL_MYPMSPRO_SENDPM','Verzend PM');
DEFINE ('_USRL_JIM','PM');
DEFINE ('_USRL_UDDEIM','PM');
DEFINE ('_USRL_SEARCHRESULT','Zoekresultaten voor');
DEFINE ('_USRL_STATUS','Status');
DEFINE ('_USRL_LISTSETTINGS','Gebruikerslijst instellingen');
DEFINE ('_USRL_ERROR','Fout');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE','Private messaging component');
DEFINE('_COM_A_COMBUILDER_TITLE','Community Builder');
DEFINE('_FORUM_SEARCH','Zoeken naar: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','U staat op het punt om dit bericht te verwijderen!');
DEFINE('_MODERATION_DELETE_SUCCESS','Bericht verwijderd.');
DEFINE('_COM_A_RANKING','Ranking');
DEFINE('_COM_A_BOT_REFERENCE','Toon Mosbot reference chart');
DEFINE('_COM_A_MOSBOT','Discussie Mosbot inschakelen');
DEFINE('_PREVIEW','voorbeeld');
DEFINE('_COM_A_MOSBOT_TITLE','Discussie Mosbot');
DEFINE('_COM_A_MOSBOT_DESC',
 'Met de discussie Mosbot kunnen gebruikers over content van de site discussiëren in de forums. De titel van het artikel wordt gebruikt als titel van het onderwerp.'
.'<br />Als het onderwerp nog niet bestaat wordt deze aangemaakt. Als het onderwerp al bestaat wordt de discussie getoond en kan de gebruiker zijn reactie toevoegen.'
.'<br /><strong>De mosbot moet apart worden gedownload en ge&iuml;nstalleerd.</strong>'
.'<br />Kijk op de <a href="http://tsmf.com">TSMF Site</a> voor meer informatie.'
.'<br />Na installatie moeten de volgende mosbot regels in de content worden toegevoegd:'
.'<br />{mos_sb_discuss:<em>catid</em>}'
.'<br />De <em>catid</em> is de categorie waarin over het artikel kan worden gediscussieerd. Kijk voor de juiste catid in de forums '
.'en controleer de category id van de URLs in de browsers status balk.'
.'<br />Voorbeeld: als over een artikel een discussie moet komen in het forum met catid 26, moet de mosbot er als volgt uit zien: {mos_sb_discuss:26}'
.'<br />Dit lijkt wat complex, maar hiermee kan voor alle Content bepaald worden in welk forum de discussie moet komen.' );

//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE','zoeken');
DEFINE('_FORUM_SEARCHRESULTS','Laat zien %s van de %s resultaten.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES','Forum Regels');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Bewerk deze file met uw eigen forum regels joomlaroot/administrator/components/com_fireboard/language/english.php</li><li>Regel 2</li><li>Regel 3</li><li>Regel 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE','Boardcode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS','De berichten zijn goedgekeurd');
DEFINE('_MODERATION_DELETE_ERROR','ERROR: De berichten zijn niet verwijderd');
DEFINE('_MODERATION_APPROVE_ERROR','ERROR: De berichten zijn niet goedgekeurd');
// listcat.php
DEFINE('_GEN_NOFORUMS','Er zijn geen forums in deze categorie!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED','Het is niet gelukt om een “spook” onderwerp in het oude forum te creëren');
DEFINE('_POST_MOVE_GHOST','Verlaat “spook” bericht in oude forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP','Forum navigatie');
DEFINE('_COM_A_FORUM_JUMP','Forum navigatie aan');
DEFINE('_COM_A_FORUM_JUMP_DESC','Indien ingesteld op &quot;Ja&quot; wordt er een dropdown box getoond met de forum index. ');
//new in 1.1 RC1
DEFINE('_GEN_RULES','regels');
DEFINE('_COM_A_RULESPAGE','Regelspagina inschakelen');
DEFINE('_COM_A_RULESPAGE_DESC','Indien ingesteld op &quot;Ja&quot; wordt een link in het topmenu toegevoegd aan de regelspagina. Deze pagina kan gebruikt worden voor het instellen van forumregels enzovoort. U kunt dit bestand aanpassen door rules.php te openen in /joomla_root/components/com_fireboard. <em>Zorg ervoor dat u een backup heeft van dit bestand aangezien deze overschreven wordt bij een eventuele upgrade!</em>');
DEFINE('_MOVED_TOPIC','VERPLAATST:');
DEFINE('_COM_A_PDF','PDF aanmaken inschakelen');
DEFINE('_COM_A_PDF_DESC','Schakel dit op &quot;Ja&quot; als u gebruikers wilt toestaan om een PDF document te genereren van het forumonderwerp.<br />Het is een <u>simpel</u> PDF document; zonder opmaak of layout maar het bevat alle tekst van het forumonderwerp.');
DEFINE('_GEN_PDFA','Klik op deze knop om een PDF document te genereren van deze thread (opent in een nieuw venster).');
DEFINE('_GEN_PDF', 'PDF');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE','Klik hier om het gebruikersprofiel van deze gebruiker te zien');
DEFINE('_VIEW_ADDBUDDY','Klik hier om deze gebruiker tot uw buddy lijst toe te voegen');
DEFINE('_POST_SUCCESS_POSTED','Uw bericht is succesvol gepost');
DEFINE('_POST_SUCCESS_VIEW','[ Terug naar uw post ]');
DEFINE('_POST_SUCCESS_FORUM','[ Terug naar het forum ]');
DEFINE('_RANK_ADMINISTRATOR','Administrator');
DEFINE('_RANK_MODERATOR','Moderator');
DEFINE('_SHOW_LASTVISIT','Sinds laatste bezoek');
DEFINE('_COM_A_BADWORDS_TITLE','Filter slechte woorden');
DEFINE('_COM_A_BADWORDS','Gebruik slechte woorden filter');
DEFINE('_COM_A_BADWORDS_DESC','Zet op &quot;Ja&quot; als u wilt dat slechte woorden worden gefilterd. Om deze fuctie te willen gebruiken moet u Badword Component hebben geinstaleerd!');
DEFINE('_COM_A_BADWORDS_NOTICE','* This message has been censored because it contained one or more words set by the administrator *');
DEFINE('_COM_A_COMBUILDER_PROFILE','Maak Community Builder forum profiel');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC','Klik op deze link om de nodige velden in Community Builder gebruikersprofiel te maken. Nadat deze gemaakt zijn kunt u ze eenvoudig verplaatsen waneer u maar wilt met de Community Builder admin, enkel hun namen en hun opties niet wijzigen. Als u ze verwijderd uit de Community Builder admin, kunt u ze met deze link terug aanmaken (klik niet meerdere malen op de link!)');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK','> Klik hier <');
DEFINE('_COM_A_COMBUILDER','Community Builder gebruikersprofielen');
DEFINE('_COM_A_COMBUILDER_DESC','Indien u dit op &quot;JA&quot; instelt zal dit de integratie van het Community Builder component activeren(www.joomlapolis.com). Alle fireboard gebruikersprofiel functies zullen behandeld worden door Community Builder en de profiellink in de forums zal u naar het Community Builder gebruikersprofiel brengen. Deze instelling overschrijft de myPMS Pro profiel instellingen, indien beide op &quot;Ja&quot; staan. Zorg er ook voor dat u de vereiste wijzigingen in de Community Builder database aanbrengt door op onderstaande url te klikken.');
DEFINE('_COM_A_AVATAR_SRC','Avatarbron');
DEFINE('_COM_A_AVATAR_SRC_DESC','Als u Clexus PMS of Community Builder component heeft geinstaleerd, dan kan u Fireboard instellen om de avatar van Clexus PMS of Community Builder te gebruiken. NOTE Voor Community Builder u moet de thubnail optie aan hebben staan, want het forum laat alleen de thubnail zien en niet het orgineel.');
DEFINE('_COM_A_KARMA','Toon karma indicator');
DEFINE('_COM_A_KARMA_DESC','Zet op &quot;Ja&quot; om de gebruikers karma en de gerelateerde knoppen (verhogen / verlagen) uit te schakelen als ze geactiveerd zijn.');
DEFINE('_COM_A_DISEMOTICONS','Emoticons uitschakelen');
DEFINE('_COM_A_DISEMOTICONS_DESC','Zet op &quot;Ja&quot; om de grafische emoticons volledig uit te schakelen (smileys).');
DEFINE('_COM_C_FBCONFIG','Kunena<br/>Configuratie');
DEFINE('_COM_C_FBCONFIGDESC','Configureer alle kunena functionaliteiten');
DEFINE('_COM_C_FORUM','Forum<br/>Administratie');
DEFINE('_COM_C_FORUMDESC','Toevoegen categori&euml;n/forums en configureer ze');
DEFINE('_COM_C_USER','Gebruikers<br/>Administratie');
DEFINE('_COM_C_USERDESC','Basis gebruikers en gebruikersprofiel administratie');
DEFINE('_COM_C_FILES','Geuploade<br/>bestandsbeheer');
DEFINE('_COM_C_FILESDESC','Blader en administreer de geuploade bestanden');
DEFINE('_COM_C_IMAGES','Geuploade<br/>afbeeldingen Browser');
DEFINE('_COM_C_IMAGESDESC','Blader en administreer de geuploade afbeeldingen');
DEFINE('_COM_C_CSS','Wijzig<br/>CSS bestand');
DEFINE('_COM_C_CSSDESC','Tweak Fireboard\'s ontwerp');
DEFINE('_COM_C_SUPPORT','Steun<br/>Website');
DEFINE('_COM_C_SUPPORTDESC','Ga naar de TSMF website (nieuw venster)');
DEFINE('_COM_C_PRUNETAB','Opschonen <br/>Forums');
DEFINE('_COM_C_PRUNETABDESC','Verwijder oude onderwerpen (configureerbaar)');
DEFINE('_COM_C_PRUNEUSERS','Opschonen<br/>Gebruikers');
DEFINE('_COM_C_PRUNEUSERSDESC','Synchroniseer Fireboard gebruikerstabel met Joomla gebruikerstabel');
DEFINE('_COM_C_LOADSAMPLE','Voorbeeld data<br/>laden');
DEFINE('_COM_C_LOADSAMPLEDESC','Voor een eenvoudige start: laadt de voorbeeld data in een lege Fireboard database');
DEFINE('_COM_C_REMOVESAMPLE', 'Verwijder voorbeelddata');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Verwijder de voorbeelddata uit uw database');
DEFINE('_COM_C_LOADMODPOS', 'Laad Module Posities');
DEFINE('_COM_C_LOADMODPOSDESC', 'Laad module posities voor FireBoard Template');
DEFINE('_COM_C_UPGRADEDESC','Update uw database naar de laatste versie na een upgrade');
DEFINE('_COM_C_BACK','Terug naar Fireboard controle paneel');
DEFINE('_SHOW_LAST_SINCE','Actieve onderwerpen sinds laatste bezoek op:');
DEFINE('_POST_SUCCESS_REQUEST2','Uw verzoek is verwerkt');
DEFINE('_POST_NO_PUBACCESS3','Klik hier om te registreren.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE','Het bericht is succcesvol verwijderd.');
DEFINE('_POST_SUCCESS_EDIT','Het bericht is succesvol gewijzigd.');
DEFINE('_POST_SUCCESS_MOVE','Het onderwerp is succesvol verplaatst.');
DEFINE('_POST_SUCCESS_POST','Uw bericht is succesvol gepost.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Uw abonnement op dit item is verwerkt.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA','Karma');
DEFINE('_KARMA_SMITE','Teleurstellend');
DEFINE('_KARMA_APPLAUD','Prachtig');
DEFINE('_KARMA_BACK','Om terug te keren naar het onderwerp');
DEFINE('_KARMA_WAIT','U kunt de karma van een persoon elke 6 uur wijzigen. <br/>Wacht 6 uur, tot u de karma van een andere persoon kan wijzigen.');
DEFINE('_KARMA_SELF_DECREASE','Probeer niet uw eigen karma te doen dalen!');
DEFINE('_KARMA_SELF_INCREASE','Uw karma is gedaald omdat u geprobeerd heeft om het bij u zelf te doen stijgen!');
DEFINE('_KARMA_DECREASED','De karma van deze persoon is gedaald. Klik hier als u niet terugkeert naar het onderwerp in enkele ogenblikken,');
DEFINE('_KARMA_INCREASED','De karma van deze persoon is toegenomen. Klik hier als u niet terugkeert naar het onderwerp in enkele ogenblikken,');
DEFINE('_COM_A_TEMPLATE','Template');
DEFINE('_COM_A_TEMPLATE_DESC','Kies een template voor gebruik.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH','Afbeeldingsets');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC','Kies de afbeeldingsset template om te gebruiken.');
DEFINE('_PREVIEW_CLOSE','Sluit dit venster');
//==========================================
//new in 1.0 Stable
DEFINE('_GEN_PATHWAY','Pad :: ');
DEFINE('_COM_A_POSTSTATSBAR','Grafische berichtenbalk');
DEFINE('_COM_A_POSTSTATSBAR_DESC','Indien op &quot;Ja&quot; wordt het aantal berichten voorzien van een grafische balk.');
DEFINE('_COM_A_POSTSTATSCOLOR','Kleurnummer voor de berichtenbalk');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Geef het kleurnummer voor de grafische berichtenbalk');
DEFINE('_LATEST_REDIRECT','Fireboard moet de toegangsrechten (opnieuw) instellen om een overzicht van laatste berichten te maken.\nDit is normaal na 30 minuten geen activiteit of na opnieuw inloggen.\nGeef de zoekopdracht nogmaals in.');
DEFINE('_SMILE_COLOUR','Kleur');
DEFINE('_SMILE_SIZE','Grootte');
DEFINE('_COLOUR_DEFAULT','Standaard');
DEFINE('_COLOUR_RED','Rood');
DEFINE('_COLOUR_PURPLE','Paars');
DEFINE('_COLOUR_BLUE','Blauw');
DEFINE('_COLOUR_GREEN','Groen');
DEFINE('_COLOUR_YELLOW','Geel');
DEFINE('_COLOUR_ORANGE','Oranje');
DEFINE('_COLOUR_DARKBLUE','Donkerblauw');
DEFINE('_COLOUR_BROWN','Bruin');
DEFINE('_COLOUR_GOLD','Goud');
DEFINE('_COLOUR_SILVER','Zilver');
DEFINE('_SIZE_NORMAL','Normaal');
DEFINE('_SIZE_SMALL','Klein');
DEFINE('_SIZE_VSMALL','Heel klein');
DEFINE('_SIZE_BIG','Groot');
DEFINE('_SIZE_VBIG','Erg groot');
DEFINE('_IMAGE_SELECT_FILE','Kies afbeelding als bijlage');
DEFINE('_FILE_SELECT_FILE','Kies bestand als bijlage');
DEFINE('_FILE_NOT_UPLOADED','Het bestand is niet geplaatst. Probeer het nogmaals of pas het bericht aan');
DEFINE('_IMAGE_NOT_UPLOADED','De afbeelding is niet geplaatst. Probeer het nogmaals of pas het bericht aan');
DEFINE('_BBCODE_IMGPH','Voeg [img] in het bericht toe voor de afbeeldingsbijlage');
DEFINE('_BBCODE_FILEPH','Voeg [file] in het bericht toe voor de bestandsbijlage');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','Selecteer hier om het abonnement op alle berichten te stoppen (ook de onzichtbare abonnementen in geval van problemen)');
DEFINE('_LINK_JS_REMOVED','<em>Hyperlink met JavaScript is automatisch verwijderd</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS','Uiterlijk');
DEFINE('_COM_A_USERS','Gerelateerd aan gebruiker');
DEFINE('_COM_A_LENGTHS','Verschillende lengte instellingen');
DEFINE('_COM_A_SUBJECTLENGTH','Max. lengte onderwerp');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maximale lengte van de onderwerpregel. De database ondersteunt maximaal 255 karakters. Als de site is geconfigureerd voor multi-byte karaktersets zoals Unicode, UTF-8, non-ISO-8599-x wordt het maximum lager volgens de volgende formule:<br/><tt>Rond af (255/(maximum aantal bytes per teken))</tt><br/> Bijvoorbeeld voor UTF-8, waarbij het maximum aantal bytes per teken gelijk is aan 4: 255/4=63.');
DEFINE('_LATEST_THREADFORUM','Onderwerp/Forum');
DEFINE('_LATEST_NUMBER','Nieuwe berichten');
DEFINE('_COM_A_SHOWNEW','Toon nieuwe berichten');
DEFINE('_COM_A_SHOWNEW_DESC','Als deze op &quot;Ja&quot; staat, dan geeft Fireboard aan welke forums een nieuw bericht bevatten en welke berichten nieuw zijn sinds het laatste bezoek.');
DEFINE('_COM_A_NEWCHAR','&quot;Nieuw&quot; teken');
DEFINE('_COM_A_NEWCHAR_DESC','Kies hier het teken dat gebruikt wordt om nieuwe berichten te merken (zoals een &quot;!&quot; of &quot;Nieuw!&quot;)');
DEFINE('_LATEST_AUTHOR','Auteur laatste bericht');
DEFINE('_GEN_FORUM_NEWPOST','Geeft aan dat er nieuwe berichten in dit forum zijn sinds het laatste bezoek');
DEFINE('_GEN_FORUM_NOTNEW','Geeft aan dat er geen nieuwe berichten in dit forum zijn sinds het laatste bezoek');
DEFINE('_GEN_UNREAD','Geeft aan dat er nieuwe ongelezen reacties in dit forum zijn sinds het laatste bezoek');
DEFINE('_GEN_NOUNREAD','Geen nieuwe ongelezen reacties sinds het laatste bezoek');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Markeer alle forums als gelezen');
DEFINE('_GEN_MARK_THIS_FORUM_READ','Markeer dit forum als gelezen');
DEFINE('_GEN_FORUM_MARKED','Alle berichten in dit forum zijn gemarkeerd als gelezen');
DEFINE('_GEN_ALL_MARKED','Alle berichten zijn gemarkeerd als gelezen');
DEFINE('_IMAGE_UPLOAD','Afbeelding inzenden');
DEFINE('_IMAGE_DIMENSIONS','De afbeelding mag maximaal (breedte x hoogte - grootte) zijn');
DEFINE('_IMAGE_ERROR_TYPE','Kies alleen jpeg, gif of png afbeeldingen');
DEFINE('_IMAGE_ERROR_EMPTY','Selecteer een bestand voor uploaden');
DEFINE('_IMAGE_ERROR_SIZE','De afbeelding is groter dan toegestaan.');
DEFINE('_IMAGE_ERROR_WIDTH','De breedte van de afbeelding is groter dan toegestaan.');
DEFINE('_IMAGE_ERROR_HEIGHT','De hoogte van de afbeelding is groter dan toegestaan.');
DEFINE('_IMAGE_UPLOADED','De afbeelding is geplaatst.');
DEFINE('_COM_A_IMAGE','Afbeelding');
DEFINE('_COM_A_IMGHEIGHT','Max. hoogte afbeelding');
DEFINE('_COM_A_IMGWIDTH','Max. breedte afbeelding');
DEFINE('_COM_A_IMGSIZE','Max. bestandsgroote<br/>van afbeelding <em>in Kilobyte</em>');
DEFINE('_COM_A_IMAGEUPLOAD','Inzenden door iedereen toestaan');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Zet op &quot;Ja&quot; als iedereen (public) afbeeldingen mag inzenden.');
DEFINE('_COM_A_IMAGEREGUPLOAD','Inzenden door geregistreerde gebruikers');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Zet op &quot;Ja&quot; als geregistreerde en ingelogde gebruikers afbeeldingen mogen inzenden.<br/>Let op: (Super)administrators en moderators mogen altijd bestanden inzenden.');
  //New since preRC4-II:
DEFINE('_COM_A_UPLOADS','Inzendingen');
DEFINE('_FILE_TYPES','Het bestand mag zijn van type - max. grootte');
DEFINE('_FILE_ERROR_TYPE','Alleen bestanden inzenden van type:\n');
DEFINE('_FILE_ERROR_EMPTY','Kies eerst een bestand');
DEFINE('_FILE_ERROR_SIZE','De bestandsgrootte is groter dan maximaal toegestaan.');
DEFINE('_COM_A_FILE','Bestanden');
DEFINE('_COM_A_FILEALLOWEDTYPES','Bestandstypen toegestaan');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Geef hier op welke bestandstypen zijn toegestaan. Gebruik kommagescheiden <strong>(geen hoofdletters!)</strong> lijsten zonder spaties.<br />Voorbeeld: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Max. bestandsgrootte<br/><em>in Kilobyte</em>');
DEFINE('_COM_A_FILEUPLOAD','Inzenden door iedereen toestaan');
DEFINE('_COM_A_FILEUPLOAD_DESC','et op &quot;Ja&quot; als iedereen (public) bestanden mag inzenden.');
DEFINE('_COM_A_FILEREGUPLOAD','Inzenden door geregistreerde gebruikers');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Zet op &quot;Ja&quot; als geregistreerde en ingelogde gebruikers bestanden mogen inzenden.<br/>Let op: (Super)administrators en moderators mogen altijd bestanden inzenden.');
DEFINE('_SUBMIT_CANCEL','Het bericht inzenden is geannuleerd');
DEFINE('_HELP_SUBMIT','Klik hier om het bericht in te zenden');
DEFINE('_HELP_PREVIEW','Klik hier om een voorbeeld van het bericht te bekijken');
DEFINE('_HELP_CANCEL','Klik hier om het bericht te annuleren');
DEFINE('_POST_DELETE_ATT','Als dit is geselecteerd worden alle bijlagen bij de berichten verwijderd als de berichten worden verwijderd (aanbevolen).');
   //new since preRC4-III
DEFINE('_COM_A_USER_MARKUP','Toon gewijzigd');
DEFINE('_COM_A_USER_MARKUP_DESC','Zet op &quot;Ja&quot; als in een gewijzigd bericht getoond moet worden dat het bericht is gewijzigd door de gebruiker.');
DEFINE('_EDIT_BY','Bericht gewijzigd door:');
DEFINE('_EDIT_AT','op:');
DEFINE('_UPLOAD_ERROR_GENERAL','Er is een fout opgetreden bij het versturen van de Avatar. Probeer het nogmaals of waarschuw de beheerder.');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Ingezonden afbeeldingen bekijken Uploaded Images Browser');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Ingezonden bestanden bekijken');
DEFINE('_COM_A_IMGB_TOTAL_IMG','Aantal ingezonden afbeeldingen');
DEFINE('_COM_A_IMGB_TOTAL_FILES','Aantal ingezonden bestanden');
DEFINE('_COM_A_IMGB_ENLARGE','Klik op de afbeelding om op originele grootte te bekijken');
DEFINE('_COM_A_IMGB_DOWNLOAD','Klik op een bestand om te downloaden');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'De optie &quot;Vervang door dummy&quot; vervangt de geselecteerde afbeelding door een dummy afbeelding.<br /> Hiermee kan een bestand vervangen worden zonder het bericht te beschadigen.<br /><small><em>Een browser refresh (F5) is vaak nodig om het vervangen op te merken.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY','Huidige dummy afbeelding');
DEFINE('_COM_A_IMGB_REPLACE','Vervang door dummy ');
DEFINE('_COM_A_IMGB_REMOVE','Geheel verwijderen');
DEFINE('_COM_A_IMGB_NAME','Naam');
DEFINE('_COM_A_IMGB_SIZE','Grootte');
DEFINE('_COM_A_IMGB_DIMS','Afmetingen');
DEFINE('_COM_A_IMGB_CONFIRM','Bestand ECHT verwijderen? \n Een bestand verwijderen kan een bericht beschadigen...');
DEFINE('_COM_A_IMGB_VIEW','Open bericht (om te bewerken)');
DEFINE('_COM_A_IMGB_NO_POST','Geen refererent bericht!');
DEFINE('_USER_CHANGE_VIEW','Veranderingen in deze instellingen worden pas zichtbaar bij het volgende bezoek aan het forum.<br /> Als de layout direct aangepast moet worden, gebruik dan de optie in de forum balk.');
DEFINE('_MOSBOT_DISCUSS_A','Discussieer over dit artikel in de forums. (');
DEFINE('_MOSBOT_DISCUSS_B',' berichten)');
DEFINE('_POST_DISCUSS','Dit onderwerp gaat over content artikel');
DEFINE('_COM_A_RSS','RSS feed inschakelen');
DEFINE('_COM_A_RSS_DESC','Met de RSS feed optie kunnen derden de laatste berichten bekijken op hun desktop/RSS aplicatie (zie bijvoorbeeld <a href="http://www.rssreader.com" target="_blank">rssreader.com</a>.');
DEFINE('_LISTCAT_RSS','bezorg de laatste berichten direct op de desktop');
DEFINE('_SEARCH_REDIRECT','Fireboard moet de toegangsrechten (opnieuw) instellen voordat het de zoekopdracht kan uitvoeren.\nDit is normaal na 30 minuten geen activiteit of na opnieuw inloggen.\nGeef de zoekopdracht nogmaals in.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG','Configuratie');
DEFINE('_COM_A_VERSION',' Versie ');
DEFINE('_COM_A_DISPLAY','Toon #');
DEFINE('_COM_A_CURRENT_SETTINGS','Huidige instellingen');
DEFINE('_COM_A_EXPLANATION','Uitleg');
DEFINE('_COM_A_BOARD_TITLE','Forum titel');
DEFINE('_COM_A_BOARD_TITLE_DESC','De naam van het forum');
DEFINE('_COM_A_VIEW_TYPE','Standaard weergave type');
DEFINE('_COM_A_VIEW_TYPE_DESC','Kies tussen platte of boom weergave type');
DEFINE('_COM_A_THREADS','Threads per pagina');
DEFINE('_COM_A_THREADS_DESC','Aantal threads per pagina tonen');
DEFINE('_COM_A_REGISTERED_ONLY','Alleen geregistreerde gebruikers ');
DEFINE('_COM_A_REG_ONLY_DESC','Zet op &quot;Ja&quot; om alleen geregistreerde gebruikers toe te staan het forum te gebruiken. Zet &quot;Nee&quot; om alle bezoekers toe te laten');
DEFINE('_COM_A_PUBWRITE','Publiek lezen/schrijven');
DEFINE('_COM_A_PUBWRITE_DESC','Zet op &quot;Ja&quot; om iedereen toe te staan te schrijven, Zet op &quot;Nee&quot; om bezoekers toe te staan te lezen, maar niet te schrijven.');
DEFINE('_COM_A_USER_EDIT','Gebruikers bewerken');
DEFINE('_COM_A_USER_EDIT_DESC','Zet op &quot;Ja&quot; om geregistreerde gebruikers toe te staan hun eigen berichten te bewerken.');
DEFINE('_COM_A_MESSAGE','Klik op &quot;Save&quot; om bovenstaande waarde op te slaan.');
DEFINE('_COM_A_HISTORY','Toon geschiedenis');
DEFINE('_COM_A_HISTORY_DESC','Zet op &quot;Ja&quot; als onderwerp Geschiedenis getoond moet worden als een gebruiker reageert/citeert');
DEFINE('_COM_A_SUBSCRIPTIONS','Sta abonnementen toe');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','Zet op &quot;Ja&quot; om geregistreerde gebruikers toe te staan zich te abonneren op onderwerpen zodat ze op de hoogte gebracht worden van nieuwe reacties (per email).');
DEFINE('_COM_A_HISTLIM','Limiet geschiedenis');
DEFINE('_COM_A_HISTLIM_DESC','Limiteer het aantal berichten getoond in de geschiedenis');
DEFINE('_COM_A_FLOOD','Flood Protectie');
DEFINE('_COM_A_FLOOD_DESC','Het aantal seconden dat een gebruiker moet wachten tussen twee opeenvolgende plaatsingen. Zet op nul om dit uit te schakelen (Let op: Flood Protectie <em>kan</em> leiden tot verslechtering van de performance).');
DEFINE('_COM_A_MODERATION','E-mail moderators');
DEFINE('_COM_A_MODERATION_DESC','Zet op &quot;Ja&quot; als forum moderators een e-mail moeten krijgen als er een nieuw bericht in hun forum geplaatst is. Let op: Hoewel Site Admininstrators automatisch alle Moderator rechten hebben moeten ze expliciet toegevoegd worden als Moderator om een e-mail te krijgen bij een nieuw bericht!');
DEFINE('_COM_A_SHOWMAIL','Toon e-mail');
DEFINE('_COM_A_SHOWMAIL_DESC','Zet op &quot;Nee&quot; als gebruikers e-mail adresen niet getoond worden op de site; zelfs niet aan geregistreerde gebruikers.');
DEFINE('_COM_A_AVATAR','Avatars toestaan');
DEFINE('_COM_A_AVATAR_DESC','Zet op &quot;Ja&quot; om geregistreerde gebruikers toe te staan Avatars te gebruiken (beheersbaar via hun profiel).');
DEFINE('_COM_A_AVHEIGHT','Max. Avatar hoogte');
DEFINE('_COM_A_AVWIDTH','Max. Avatar breedte');
DEFINE('_COM_A_AVSIZE','Max. Avatar bestandsgrootte<br/><em>in Kilobyte</em>');
DEFINE('_COM_A_USERSTATS','Laat gebruikers statistieken zien');
DEFINE('_COM_A_USERSTATS_DESC','Zet op &quot;Ja&quot; om gebruikers statistieken te laten zien, zoals het aantal berichten dat de gebruiker heeft geplaatst, het type gebruiker (Admin, Moderator,Gebruiker, etc.).');
DEFINE('_COM_A_AVATARUPLOAD','Sta Avatar upload toe');
DEFINE('_COM_A_AVATARUPLOAD_DESC','Zet op &quot;Ja&quot; als geregistreerde gebruikers eigen Avatars mogen uploaden.');
DEFINE('_COM_A_AVATARGALLERY','Gebruik Avatars gallerij');
DEFINE('_COM_A_AVATARGALLERY_DESC','Zet op &quot;Ja&quot; als gebruikers kunnen kiezen uit een standaard Avatar gallerij (components/com_fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','Zet op &quot;Ja&quot; als de gebruikersrang getoond moet worden.<br/><strong>Opmerking: gebruikers statistieken moeten ingeschakeld zijn in de Frontend configuratie.</strong>');
DEFINE('_COM_A_RANKINGIMAGES','Gebruik rang afbeeldingen');
DEFINE('_COM_A_RANKINGIMAGES_DESC','Zet op &quot;Ja&quot; als met een afbeelding getoond wordt welke rang gebruikers hebben (uit components/com_Fireboard/ranks). Als u dit uitzet komt er gewoon tekst te staan.');

//email and stuff
$_COM_A_NOTIFICATION ="Notificatie: Nieuw bericht van ";
$_COM_A_NOTIFICATION1="Een nieuw bericht is geplaatst in een forum op ";
$_COM_A_NOTIFICATION2="Abonnementen kunnen bijgewerkt worden door op de 'mijn profiel' link op de forum homepage te klikken, nadat op de site is ingelogd. Vanuit uw profiel kunnen ook abonnementen geannuleerd worden.";
$_COM_A_NOTIFICATION3="Beantwoord deze email niet, dit is een automatisch gegenereerd bericht.";
$_COM_A_NOT_MOD1="Een nieuw bericht is geplaatst op een forum waarvan u moderator bent: ";
$_COM_A_NOT_MOD2="Bekijk het bericht nadat u bent ingelogd op de site.";
DEFINE('_COM_A_NO','Nee');
DEFINE('_COM_A_YES','Ja');
DEFINE('_COM_A_FLAT','Plat');
DEFINE('_COM_A_THREADED','Boomstructuur');
DEFINE('_COM_A_MESSAGES','Berichten per pagina');
DEFINE('_COM_A_MESSAGES_DESC','Aantal berichten om per pagina te tonen');
   //admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME','Gebruikersnaam');
DEFINE('_COM_A_USERNAME_DESC','Stel in op &quot;Ja&quot; als de gebruikersnaam (zoals bij login) wordt gebruikt in plaats van de echte naam.');
DEFINE('_COM_A_CHANGENAME','Sta toe dat de gebruikersnaam word veranderd');
DEFINE('_COM_A_CHANGENAME_DESC','Stel in op &quot;Ja&quot; als gebruikers de gebruikersnaam mogen veranderen als zij berichten plaatsen.');
   //admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE','Forums uitgeschakeld');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','Geef aan of de forums beschikbaar zijn');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Bericht dat getoond wordt bij niet beschikbaar');
DEFINE('_COM_A_PRUNE','Opschonen van het forum');
DEFINE('_COM_A_PRUNE_NAME','Op te schonen forums:');
DEFINE('_COM_A_PRUNE_DESC',
    'De functie om het forum op te schonen biedt de mogelijkheid berichten op te schonen waar geen reacties meer op zijn geweest binnen het opgegeven aantal dagen. Let op, dit verwijdert niet de onderwerpen waar het sticky bit aan staat of die vergrendeld staan, deze moeten handmatig verwijderd worden. Artikelen die in een vergrendeld forum staan kunnen niet opgeschoond worden, en komen niet in de lijst voor.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Verwijder berichten waar niet op gereageerd is sinds');
DEFINE('_COM_A_PRUNE_DAYS','dag(en)');
DEFINE('_COM_A_PRUNE_USERS','Opschonen gebruikers');
DEFINE('_COM_A_PRUNE_USERS_DESC','Deze functie maakt het mogelijk om de Fireboard gebruikerslijst op te schonen ten opzichte van de Joomla gebruikerslijst. De functie verwijdert alle profielen van Fireboard gebruikers die verwijderd zijn uit Joomla. Wanneer u zeker weet dat u hiermee door wilt gaan, kunt u op de optie "Start Pruning" in de menu balk hierboven klikken.');

//general
DEFINE('_GEN_ACTION','Actie');
DEFINE('_GEN_AUTHOR','Auteur');
DEFINE('_GEN_BY','door');
DEFINE('_GEN_CANCEL','annuleer');
DEFINE('_GEN_CONTINUE','doorgaan');
DEFINE('_GEN_DATE','Datum');
DEFINE('_GEN_DELETE','verwijder');
DEFINE('_GEN_EDIT','bewerk');
DEFINE('_GEN_EMAIL','e-mail');
DEFINE('_GEN_EMOTICONS','emoticons');
DEFINE('_GEN_FLAT','Plat');
DEFINE('_GEN_FLAT_VIEW','Platte weergave');
DEFINE('_GEN_FORUMLIST','Forum lijst');
DEFINE('_GEN_FORUM','Forum');
DEFINE('_GEN_HELP','help');
DEFINE('_GEN_HITS','Bekeken');
DEFINE('_GEN_LAST_POST','Laatste Bericht');
DEFINE('_GEN_LATEST_POSTS','Toon laatste berichten');
DEFINE('_GEN_LOCK','sluiten');
DEFINE('_GEN_UNLOCK','openen');
DEFINE('_GEN_LOCKED_FORUM','dit forum is gesloten; geen nieuwe berichten mogelijk .');
DEFINE('_GEN_LOCKED_TOPIC','dit onderwerp is gesloten; geen nieuwe berichten mogelijk.');
DEFINE('_GEN_MESSAGE','Bericht');
DEFINE('_GEN_MODERATED','Dit forum is gemodereerd; Nieuwe berichten worden eerst bekeken voor publicatie.');
DEFINE('_GEN_MODERATORS','Moderators');
DEFINE('_GEN_MOVE','verplaats');
DEFINE('_GEN_NAME','Naam');
DEFINE('_GEN_POST_NEW_TOPIC','::Maak een nieuw onderwerp::');
DEFINE('_GEN_POST_REPLY','Reageer');
DEFINE('_GEN_MYPROFILE','Profiel');
DEFINE('_GEN_QUOTE','citeer');
DEFINE('_GEN_REPLY','reageer');
DEFINE('_GEN_REPLIES','Reacties');
DEFINE('_GEN_THREADED','Boom');
DEFINE('_GEN_THREADED_VIEW','Boom weergave');
DEFINE('_GEN_SIGNATURE','Onderschrift');
DEFINE('_GEN_ISSTICKY','Dit onderwerp is gesloten.');
DEFINE('_GEN_STICKY','sticky');
DEFINE('_GEN_UNSTICKY','niet sticky');
DEFINE('_GEN_SUBJECT','Onderwerp');
DEFINE('_GEN_SUBMIT','Stuur in');
DEFINE('_GEN_TOPIC','Onderwerp');
DEFINE('_GEN_TOPICS','Onderwerpen');
DEFINE('_GEN_TOPIC_ICON','onderwerp icoon');
DEFINE('_GEN_SEARCH_BOX','Zoeken...');
$_GEN_THREADED_VIEW="boom weergave";
$_GEN_FLAT_VIEW    ="platte weergave";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD','Upload');
DEFINE('_UPLOAD_DIMENSIONS','De Avatar afmetingen mogen niet meer zijn dan (breedte x hoogte - grootte)');
DEFINE('_UPLOAD_SUBMIT','Geef een Avatar voor uploaden ');
DEFINE('_UPLOAD_SELECT_FILE','Selecteer bestand');
DEFINE('_UPLOAD_ERROR_TYPE','Gebruik alleen jpeg, gif of png plaatjes');
DEFINE('_UPLOAD_ERROR_EMPTY','Selecteer eerst een bestand voor het uploaden');
DEFINE('_UPLOAD_ERROR_NAME','Het bestand mag alleen alphanumerieke karacters en geen spaties bevatten.');
DEFINE('_UPLOAD_ERROR_SIZE','De grootte van het bestand overschrijdt de door de Administrator vastgestelde limiet.');
DEFINE('_UPLOAD_ERROR_WIDTH','De breedte van de afbeelding overschrijdt de door de Administrator vastgestelde limiet.');
DEFINE('_UPLOAD_ERROR_HEIGHT','De hoogte van de afbeelding overschrijdt de door de Administrator vastgestelde limiet');
DEFINE('_UPLOAD_ERROR_CHOOSE',"Geen Avatar uit de gallerij gekozen...");
DEFINE('_UPLOAD_UPLOADED','De Avatar is opgeslagen.');
DEFINE('_UPLOAD_GALLERY','Kies een Avatar uit de gallerij:');
DEFINE('_UPLOAD_CHOOSE','Bevestig keuze.');
// listcat.php
DEFINE('_LISTCAT_ADMIN','Een administrator moet deze eerst maken vanuit het ');
DEFINE('_LISTCAT_DO','Zij weten wat ze moeten doen ');
DEFINE('_LISTCAT_INFORM','Breng ze hiervan op de hoogte en zeg dat ze voortmaken!');
DEFINE('_LISTCAT_NO_CATS','Er zijn nog geen categori&euml;en gedefinieerd in dit forum.');
DEFINE('_LISTCAT_PANEL','Administratie paneel van het Joomla CMS.');
DEFINE('_LISTCAT_PENDING','wachtende bericht(en)');
// moderation.php
DEFINE('_MODERATION_MESSAGES','Er zijn geen berichten die op beoordeling wachten.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE','U staat op het punt het volgende bericht te verwijderen');
DEFINE('_POST_ABOUT_DELETE','<strong>LET OP:</strong><br/>
-Als u een forum onderwerp verwijderd (het eerste bericht van het onderwerp) worden ook alle reacties verwijderd!
..Overweeg om de inhoud van het bericht te legen als alleen de inhoud verwijderd moet worden..
<br/>
- Alle reacties op een gewoon bericht worden 1 plaats omhoog geschoven in de berichtenlijst.');
DEFINE('_POST_CLICK','klik hier');
DEFINE('_POST_ERROR','Kan gebruikersnaam/e-mail niet vinden. Ernstige database fout; niet gedocumenteerd');
DEFINE('_POST_ERROR_MESSAGE','Er is een onbekende SQL fout ontstaan en het bericht is niet opgeslagen. Als het probleem zich voor blijft doen, neem dan contact op met de Administrator.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Er is een fout onstaan en het bericht is niet gewijzigd. Probeer het nog eens. Als het probleem zich voor blijft doen, neem dan contact op met de Administrator.');
DEFINE('_POST_ERROR_TOPIC','Er is een fout ontstaan. Bekijk de melding hieronder:');
DEFINE('_POST_FORGOT_NAME','Geen naam vermeld! Klik op de browser&#146s terug button om terug te gaan en probeer het nog eens.You forgot to include your name.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_SUBJECT','Geen onderwerp ingevuld! Klik op de browser&#146s terug button om terug te gaan en probeer het nog eens.');
DEFINE('_POST_FORGOT_MESSAGE','Geen bericht! Klik op de browser&#146s terug button om terug te gaan en probeer het nog eens.');
DEFINE('_POST_INVALID','Een ongeldig bericht nummer is opgevraagd.');
DEFINE('_POST_LOCK_SET','Het onderwerp is afgesloten.');
DEFINE('_POST_LOCK_NOT_SET','Het onderwerp kan niet afgesloten worden.');
DEFINE('_POST_LOCK_UNSET','Het onderwerp is weer geopend.');
DEFINE('_POST_LOCK_NOT_UNSET','Het onderwerp kan niet weer geopend worden.');
DEFINE('_POST_MESSAGE','Maak een nieuw bericht in ');
DEFINE('_POST_MOVE_TOPIC','Verplaats dit onderwerp naar forum ');
DEFINE('_POST_NEW','Maak een nieuw bericht in: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','Abonnementsaanvraag kan niet behandeld worden.');
DEFINE('_POST_NOTIFIED','Markeer dit vakje om op de hoogte gehouden te worden van reacties.');
DEFINE('_POST_STICKY_SET','Het sticky bit is gezet voor dit onderwerp.');
DEFINE('_POST_STICKY_NOT_SET','Het sticky bit kan niet gezet worden voor dit onderwerp.');
DEFINE('_POST_STICKY_UNSET','Het sticky bit is weggehaald voor dit onderwerp.');
DEFINE('_POST_STICKY_NOT_UNSET','THet sticky bit kan niet weggehaald worden voor dit onderwerp.');
DEFINE('_POST_SUBSCRIBE','Abonneer');
DEFINE('_POST_SUBSCRIBED_TOPIC','U bent op dit onderwerp geabonneerd.');
DEFINE('_POST_SUCCESS','Het bericht is succesvol toegevoegd');
DEFINE('_POST_SUCCES_REVIEW','Het bericht is succesvol toegevoegd. Het zal worden beoordeeld door een Moderator alvorens het gepubliceerd zal worden op het forum.');
DEFINE('_POST_SUCCESS_REQUEST','Het verzoek is verwerkt. Als u niet terug gaat naar het bericht in een paar seconden,');
DEFINE('_POST_TOPIC_HISTORY','Onderwerp geschiedenis van ');
DEFINE('_POST_TOPIC_HISTORY_MAX','Max. getoond:');
DEFINE('_POST_TOPIC_HISTORY_LAST','berichten  -  <i>(Laatste bericht eerst)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','het onderwerp kan niet worden verplaatst. Om terug te gaan naar het onderwerp:');
DEFINE('_POST_TOPIC_FLOOD1','De administrator van dit forum heeft Flood Protection ingeschakeld en bepaalt dat');
DEFINE('_POST_TOPIC_FLOOD2',' seconden gewacht moet worden voordat er weer een bericht geplaatst mag worden.');
DEFINE('_POST_TOPIC_FLOOD3','Klik de terug button van de browser om terug te gaan naar het bericht.');
DEFINE('_POST_EMAIL_NEVER','Het e-mail adres zal niet worden getoond op de site.');
DEFINE('_POST_EMAIL_REGISTERED','het e-mail adres zal alleen beschikbaar zijn voor geregistreerde gebruikers.');
DEFINE('_POST_LOCKED','gesloten door de Administrator.');
DEFINE('_POST_NO_NEW','Nieuwe reacties zijn niet toegestaan.');
DEFINE('_POST_NO_PUBACCESS1','De administrator heeft publieke schrijftoegang uitgeschakeld.');
DEFINE('_POST_NO_PUBACCESS2','Het forum mag bekeken worden, maar alleen geregistreerde<br/>gebruikers mogen berichten achterlaten.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','>> Er zijn nog geen onderwerpen in dit forum <<');
DEFINE('_SHOWCAT_PENDING','wachtende bericht(en)');
// userprofile.php
DEFINE('_USER_DELETE',' selecteer dit vakje om het onderschrift te verwijderen');
DEFINE('_USER_ERROR_A','Foutief op deze pagine terecht gekomen. Informeer de beheerder op welke links ');
DEFINE('_USER_ERROR_B','is geklikt om hier te komen. De beheerder kan dan een foutrapport indienen.');
DEFINE('_USER_ERROR_C','Bedankt!');
DEFINE('_USER_ERROR_D','Fout nummer die bijgevoegd moet worden: ');
DEFINE('_USER_GENERAL','Algemene profiel opties');
DEFINE('_USER_MODERATOR','U bent aangewezen als Moderator aan forums');
DEFINE('_USER_MODERATOR_NONE','Geen forums aan toegewezen');
DEFINE('_USER_MODERATOR_ADMIN','Admins zijn moderators op alle forums.');
DEFINE('_USER_NOSUBSCRIPTIONS','Geen abonnementen gevonden');
DEFINE('_USER_PREFERED','Voorkeur weergave type');
DEFINE('_USER_PROFILE','Profiel van ');
DEFINE('_USER_PROFILE_NOT_A','Profiel kan ');
DEFINE('_USER_PROFILE_NOT_B','niet');
DEFINE('_USER_PROFILE_NOT_C',' worden bijgewerkt.');
DEFINE('_USER_PROFILE_UPDATED','Profiel is bijgewerkt.');
DEFINE('_USER_RETURN_A','Als u niet, binnen een paar seconden, word terug gezonden naar uw profiel, ');
DEFINE('_USER_RETURN_B','klik hier');
DEFINE('_USER_SUBSCRIPTIONS','Uw abonnementen');
DEFINE('_USER_UNSUBSCRIBE',':: schrijf uit :: ');
DEFINE('_USER_UNSUBSCRIBE_A','Er kan ');
DEFINE('_USER_UNSUBSCRIBE_B','niet');
DEFINE('_USER_UNSUBSCRIBE_C',' worden uitgeschreven van het onderwerp.');
DEFINE('_USER_UNSUBSCRIBE_YES','U bent uitgeschreven van het onderwerp.');
DEFINE('_USER_DELETEAV',' selecteer dit vakje om Avatar te verwijderen');
//New 0.9 to 1.0
DEFINE('_USER_ORDER','Stel bericht volgorde in');
DEFINE('_USER_ORDER_DESC','Laatste bericht eerst');
DEFINE('_USER_ORDER_ASC','Eerste bericht eerst');
// view.php
DEFINE('_VIEW_DISABLED','De Administrator heeft publieke schrijf toegang geblokkeerd.');
DEFINE('_VIEW_POSTED','Geschreven door');
DEFINE('_VIEW_SUBSCRIBE',':: Abonneer op dit onderwerp ::');
DEFINE('_MODERATION_INVALID_ID','Een ongeldig bericht nummer is opgevraagd.');
DEFINE('_VIEW_NO_POSTS','Er zijn geen posts in dit forum.');
DEFINE('_VIEW_VISITOR','Bezoeker(s)');
DEFINE('_VIEW_ADMIN','Admin');
DEFINE('_VIEW_USER','Gebruiker');
DEFINE('_VIEW_MODERATOR','Moderator');
DEFINE('_VIEW_REPLY','Reageer op dit bericht');
DEFINE('_VIEW_EDIT','Wijzig dit bericht');
DEFINE('_VIEW_QUOTE','Citeer dit bericht');
DEFINE('_VIEW_DELETE','Verwijder dit bericht');
DEFINE('_VIEW_STICKY','Onderwerp vastzetten');
DEFINE('_VIEW_UNSTICKY','Onderwerp losmaken');
DEFINE('_VIEW_LOCK','Onderwerp sluiten');
DEFINE('_VIEW_UNLOCK','Onderwerp openen');
DEFINE('_VIEW_MOVE','Onderwerp verplaatsen naar een ander forum');
DEFINE('_VIEW_SUBSCRIBETXT','Abonneer op dit onderwerp om een email te ontvangen als er een nieuw bericht is geplaatst');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME','home');
DEFINE('_POSTS','Berichten:');
DEFINE('_TOPIC_NOT_ALLOWED','Bericht');
DEFINE('_FORUM_NOT_ALLOWED','Forum');
DEFINE('_FORUM_IS_OFFLINE','Forum is OFFLINE!');
DEFINE('_PAGE','Pagina: ');
DEFINE('_NO_POSTS','Geen berichten');
DEFINE('_CHARS','max. 150 karakters');
DEFINE('_HTML_YES','HTML is ingeschakeld');
DEFINE('_YOUR_AVATAR','<b>Uw Avatar</b>');
DEFINE('_NON_SELECTED','Nog niet geselecteerd <br>');
DEFINE('_SET_NEW_AVATAR','Selecteer nieuwe Avatar');
DEFINE('_THREAD_UNSUBSCRIBE',':: uitschrijven ::');
DEFINE('_SHOW_LAST_POSTS','Berichten in de afgelopen');
DEFINE('_SHOW_HOURS','uren');
DEFINE('_SHOW_POSTS','Totaal: ');
DEFINE('_DESCRIPTION_POSTS','De laatste berichten voor de actieve onderwerpen worden weergegeven');
DEFINE('_SHOW_4_HOURS','4 Uur');
DEFINE('_SHOW_8_HOURS','8 Uur');
DEFINE('_SHOW_12_HOURS','12 Uur');
DEFINE('_SHOW_24_HOURS','24 Uur');
DEFINE('_SHOW_48_HOURS','48 Uur');
DEFINE('_SHOW_WEEK','Week');
DEFINE('_POSTED_AT','Geplaatst op');
DEFINE('_DATETIME','d/m/Y H:i');
DEFINE('_NO_TIMEFRAME_POSTS','Er zijn geen nieuwe berichten in het geselecteerde tijdvak.');
DEFINE('_MESSAGE','Berichten:');
DEFINE('_NO_SMILIE','nee');
DEFINE('_FORUM_UNAUTHORIZIED','Dit forum is alleen toegankelijk voor geregistreerde en aangemelde gebruikers.');
DEFINE('_FORUM_UNAUTHORIZIED2','Als je al bent geregistreerd, log dan eerst in.');
DEFINE('_MESSAGE_ADMINISTRATION','Moderatie');
DEFINE('_MOD_APPROVE','Goedgekeurd');
DEFINE('_MOD_DELETE','Verwijderen');
//NEW in RC1
DEFINE('_SHOW_LAST','Toon meest recente bericht');
DEFINE('_POST_WROTE','schrijft');
DEFINE('_COM_A_EMAIL','Forum Email adres');
DEFINE('_COM_A_EMAIL_DESC','Dit is het e-mail adres van het forum. Vul hier een werkend e-mail adres in.');
DEFINE('_COM_A_WRAP','Wrap woorden langer dan');
DEFINE('_COM_A_WRAP_DESC','Geef het max. aantal letters van een woord. Met deze optie kunt u de uitvoer van berichten aanpassen aan uw template.<br/> 70 karakters is het maximum voor vaste breedte van templates maar u moet hier wellicht even mee experimenteren.<br/>URLs, ongeacht hoe lang, worden niet beïnvloed door de wordwrap');
DEFINE('_COM_A_SIGNATURE','Max. ondertekening lengte');
DEFINE('_COM_A_SIGNATURE_DESC','Max. aantal karakters voor de ondertekening.');
DEFINE('_SHOWCAT_NOPENDING','Geen wachtende berichten');
DEFINE('_COM_A_BOARD_OFSET','Forum tijd offset');
DEFINE('_COM_A_BOARD_OFSET_DESC','Sommige forums zijn in een andere tijdzone als waar de gebruikers zijn. Positieve en negatieve getallen zijn toegestaan.');

//New in RC2
DEFINE('_COM_A_BASICS','Basis');
DEFINE('_COM_A_FRONTEND','Frontend');
DEFINE('_COM_A_SECURITY','Beveiliging');
DEFINE('_COM_A_AVATARS','Avatars');
DEFINE('_COM_A_INTEGRATION','Integratie');
DEFINE('_COM_A_PMS','myPMS2 Prive Berichten');
DEFINE('_COM_A_PMS_DESC',
    'Zet op &quot;Ja&quot; als myPMS2 is geïnstalleerd voor persoonlijke berichten en geregistreerde gebruikers mogen dit gebruiken.');
DEFINE('_VIEW_PMS','Klik hier voor een persoonlijk bericht.');
//new in RC3
DEFINE('_POST_RE','Re:');
DEFINE('_BBCODE_BOLD','Dik gedrukte tekst: [b]text[/b]');
DEFINE('_BBCODE_ITALIC','Cursieve tekst: [i]text[/i]');
DEFINE('_BBCODE_UNDERL','Onderstreepte tekst: [u]text[/u]');
DEFINE('_BBCODE_QUOTE','Quote tekst: [quote]tekst[/quote]');
DEFINE('_BBCODE_CODE','Code weergeven: [code]code[/code]');
DEFINE('_BBCODE_ULIST','Ongeordende Lijst: [ul] [li]text[/li] [/ul] - Tip: een lijst moet Lijst Items bevatten');
DEFINE('_BBCODE_OLIST','Geordende lijst: [ol] [li]text[/li] [/ol] - Tip: een lijst moet Lijst Items bevatten');
DEFINE('_BBCODE_IMAGE','Afbeelding: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK','Link: [url]http://www.zzz.com/[/url] of [url=http://www.zzz.com/]Dit is een link[/url]');
DEFINE('_BBCODE_CLOSA','Alle tags sluiten');
DEFINE('_BBCODE_CLOSE','Sluit alle open bbCode tags');
DEFINE('_BBCODE_COLOR','Kleur: [color=#FF6600]tekst[/color]');
DEFINE('_BBCODE_SIZE','Grootte: [size=1]tekstgrootte[/size] - Tip: grootte varieert tussen 1 en 5');
DEFINE('_BBCODE_LITEM','Lijst Item: [li]lijst item[/li]');
DEFINE('_BBCODE_HINT','bbCode Help - Tip: bbCode kan worden toegepast op geselecteerde tekst!');
DEFINE('_COM_A_TAWIDTH','Tekstvak breedte');
DEFINE('_COM_A_TAWIDTH_DESC',' Pas de breedte van uw tekstvlak aan op het gebruikte template. <br/>De Onderwerp Emoticon Toolbar wordt over 2 regels verdeeld als breedte <= 450 pixels');
DEFINE('_COM_A_TAHEIGHT','Tekstvaak hoogte');
DEFINE('_COM_A_TAHEIGHT_DESC','Pas de hoogte van het tekstvlak aan op het gebruikte template');
DEFINE('_COM_A_ASK_EMAIL','E-mail nodig');
DEFINE('_COM_A_ASK_EMAIL_DESC','Is een e-mail adres noodzakelijk als gebruikers of bezoekers berichten plaatsen? Zet op &quot;Nee&quot; als deze optie niet nodig is op het forum. Gebruikers zullen niet om een e-mail adres worden gevraagd.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Rangen Management');
define('_KUNENA_SORTRANKS', 'Sorteer op rang');

define('_KUNENA_RANKSIMAGE', 'Rang plaatje');
define('_KUNENA_RANKS', 'Rang Titel');
define('_KUNENA_RANKS_SPECIAL', 'Speciaal');
define('_KUNENA_RANKSMIN', 'Minimum aantal berichten');
define('_KUNENA_RANKS_ACTION', 'Acties');
define('_KUNENA_NEW_RANK', 'Nieuwe Rang');

//zelf bijgevoegd
define('_KUNENA_PATHWAY_VIEWING', 'online');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y');
define('_KUNENA_TOPIC', 'onderwerp');
DEFINE('_KUNENA_TOTALFAVORITE', 'Favoriet:  ');
define('_KUNENA_FORUMTOOLS', 'tools');
Define('_KUNENA_TIME_SINCE','');
DEFINE ('_KUNENA_REPORT_LOGGED','Logged');
DEFINE ('_KUNENA_GO','Go');
DEFINE ('_KUNENA_EDITING_REASON','Reason for Editing');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Laast gewijzigd');
DEFINE ('_KUNENA_BY','Door');
DEFINE ('_KUNENA_REASON','reden');
DEFINE('_GEN_GOTOBOTTOM', 'Ga naar beneden');
DEFINE('_GEN_GOTOTOP', 'Ga naar boven');
DEFINE('_KUNENA_FORUM_TOP', 'forum Categorien ');
DEFINE('_KUNENA_POWEREDBY', 'Gemaakt door ');
DEFINE('_UE_KUNENA_VIEWTYPE_TITLE:', 'weergaven ');
DEFINE('_UE_KUNENA_TABDESC', 'instellingen Forum ');
DEFINE('_UE_KUNENA_ORDERING_TITLE:', 'Welke post eerst ');
DEFINE('_UE_KUNENA_SIGNATURE:', 'handtekening ');
DEFINE('_UE_KUNENA_VIEWTYPE_FLAT', 'Flatweergaven ');
DEFINE('_UE_KUNENA_VIEWTYPE_TREADEd', 'boomweergaven ');
DEFINE('_UE_KUNENA_ORDERING_OLDEST', 'Oudste eerst ');
DEFINE('_UE_KUNENA_ORDERING_LASTET', 'Nieuwste eerst ');

// Time Format
DEFINE('_TIME_TODAY', '<b>Vandaag</b> ');
DEFINE('_TIME_YESTERDAY', '<b>Gisteren</b> ');


?>
