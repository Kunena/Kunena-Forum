<?php
/**
* @version $Id$
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
*            Language: Serbian Latin
*         For version: 1.0.8
*            Encoding: UTF-8
*              Author: Dragan Zečević <dragan@megasystem.ch>
*                      Miloš Komarčević <kmilos@gmail.com>, Fedora Srbija (http://fedora.fsn.org.rs)
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direktan pristup ovoj lokaciji nije dozvoljen.');

// 1.0.8
DEFINE('_POST_FORGOT_EMAIL', 'Zaboravili ste da priložite svoju adresu e-pošte.  Pritisnite dugme za nazad u vašem čitaču da biste se vratili nazad i pokušali ponovo.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Sve je obrisano, nedostajale su neke datoteke priloga!');
// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Uključi obeležavanje koda');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Uključuje Kunena java skriptu za obeležavanje oznaka koda. Ako vaši članovi objavljuju php i slične odlomke koda unutar oznaka koda, uključivanje ovoga će obojiti kod. Ako vaš forum ne koristi takve poruke sa programskim jezicima, možda bi trebalo da isključite ovo kako bi se izbeglo pogrešno formiranje oznaka koda.');
DEFINE('_COM_A_RSS_TYPE', 'Podrazumevana RSS vrsta');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Izaberite između RSS izvora po temi ili po poruci. Po temi znači da će samo jedna stavka po temi biti navedena u RSS izvoru, nezavisno od broja poruka koje su objavljene u toj temi. Po temi proizvodi kraći i kompaktniji RSS izvor ali neće navesti svaki odgovor.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Po temi');
DEFINE('_COM_A_RSS_BY_POST', 'Po poruci');
DEFINE('_COM_A_RSS_HISTORY', 'RSS istorijat');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Izaberite koliki istorijat treba uključiti u RSS izvor. Podrazumevano je 1 mesec ali bi možda trebalo da ga ograničite na 1 nedelju na sajtovima sa velikim prometom.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 nedelja');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 mesec');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 godina');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Podrazumevana Kunena stranica');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Izaberite podrazumevanu Kunena stranicu koja će se prikazati kada se klikne veza ka forumu ili kada se prvo uđe u forum. Podrazumevane su skorašnje diskusije. Treba da bude postavljeno na kategorije za šablone koji nisu default_ex. Ako su izabrane moje diskusije, gosti će podrazumevano videti skorašnje diskusije.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Skorašnje diskusije');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Moje diskusije');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategorije');
DEFINE('_KUNENA_BBCODE_HIDE', 'Sledeće je sakriveno od neregistrovanih korisnika:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Upozorenje, otkrivanje!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Roditeljski forum ne sme biti isti.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Roditeljski forum je jedan od svoje sopstvene dece.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'ID foruma ne postoji.');
DEFINE('_KUNENA_RECURSION', 'Otkrivena je rekurzija.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Zaboravili ste da unesete ime.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Zaboravili ste da unesete adresu e-pošte.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Zaboravili ste da unesete temu.');
DEFINE('_KUNENA_EDIT_TITLE', 'Uredite svoje detalje');
DEFINE('_KUNENA_YOUR_NAME', 'Vaše ime:');
DEFINE('_KUNENA_EMAIL', 'e-pošta:');
DEFINE('_KUNENA_UNAME', 'Korisničko ime:');
DEFINE('_KUNENA_PASS', 'Lozinka:');
DEFINE('_KUNENA_VPASS', 'Potvrdite lozinku:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Korisnički detalji su sačuvani.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Zasluge');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode postavke');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Prikaži oznaku za otkrivanje u traci sa alatkama uređivača');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Postavite na „Da“ ako želite da oznaka [spoiler] (otkrivanje) bude prikazana u traci sa alatkama uređivača poruke.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Prikaži oznaku za video u traci sa alatkama uređivača');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Postavite na „Da“ ako želite da oznaka [video] bude prikazana u traci sa alatkama uređivača poruke.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Prikaži oznaku za eBay u traci sa alatkama uređivača');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Postavite na „Da“ ako želite da oznaka [ebay] bude prikazana u traci sa alatkama uređivača poruke.');
DEFINE('_COM_A_TRIMLONGURLS', 'Skrati dugačke URL-ove');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Postavite na „Da“ ako želite da dugački URL-ovi budu skraćeni. Pogledajte postavke za skraćivanje početka i završetka URL-a.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Početni deo skraćenih URL-ova');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Broj znakova za početni deo skraćenih URL-ova. Skraćivanje dugačkih URL-ova mora biti postavljeno na „Da“.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Završni deo skraćenih URL-ova');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Broj znakova za završni deo skraćenih URL-ova. Skraćivanje dugačkih URL-ova mora biti postavljeno na „Da“.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Automatski ugnezdi YouTube video');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Postavite na „Da“ ako želite da URL-ovi YouTube videa budu automatski ugneždeni.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Automatski ugnezdi eBay predmete');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Postavite na „Da“ ako želite da eBay predmeti i pretrage budu automatski ugneždeni.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'Jezički kod eBay vidžeta');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Važno je postaviti ispravan jezički kod jer eBay vidžet iz njega zaključuje i jezik i valutu. Podrazumevano je en-us za ebay.com. Primeri: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Dužina sesije');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Podrazumevano je 1800 [sekundi]. Dužina (do isteka) sesije u sekundama slično kao i dužini Joomla sesije. Dužina sesije je važna za ponovno određivanje prava pristupa, prikaza ko je na vezi i pokazatelja NOVO. Kada sesija jednom istekne van tog vremena, prava pristupa i pokazatelj NOVO se ponovo uspostavljaju.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Sastavi');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Sastavi ovu temu sa');
DEFINE('_POST_MERGE_GHOST', 'Ostavi temu utvaru');
DEFINE('_POST_SUCCESS_MERGE', 'Tema je uspešno sastavljena.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Sastavljanje nije uspelo.');
DEFINE('_GEN_SPLIT', 'Rastavi');
DEFINE('_GEN_DOSPLIT', 'Idi');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Tema je uspešno rastavljena.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Tema je uspešno izmenjena.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Izmena teme nije uspela.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Rastavljanje nije uspelo.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplikat, istovetna poruka je ignorisana.');
DEFINE('_POST_SPLIT_HINT', '<br />Savet: Možete unaprediti poruku na mesto teme ako je izaberete u drugoj koloni i štiklirate da nema ničega za rastavljanje.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'poveži siročiće sa temom');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Poveži siročiće sa novom porukom u temi.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'poveži siročiće sa prethodnom porukom');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Poveži siročiće sa prethodnom porukom.');
DEFINE('_POST_MERGE', 'sastavi');
DEFINE('_POST_MERGE_TITLE', 'Spoji ovu temu na prvu poruku cilja.');
DEFINE('_POST_INVERSE_MERGE', 'sastavi unazad');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Spoji prvu poruku cilja na ovu temu.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Ova tema je uklonjena iz vaših omiljenih.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Ova tema <b>NIJE</b> uklonjena iz vaših omiljenih');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Vaš zahtev za uklanjanje iz omiljenih je obrađen.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Ova tema je uklonjena iz vaših pretplaćenih.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Ova tema <b>NIJE</b> uklonjena iz vaših pretplaćenih.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Vaš zahtev za uklanjanje iz pretplaćenih je obrađen.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Nije izabrana ciljna kategorija. Ništa nije premešteno.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Skorašnje diskusije');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Moje diskusije');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Diskusije koje sam pokrenuo/la ili na koje sam odgovorio/la');
DEFINE('_KUNENA_CATEGORY', 'Kategorija:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorije');
DEFINE('_KUNENA_POSTED_AT', 'Objavljeno');
DEFINE('_KUNENA_AGO', 'ranije');
DEFINE('_KUNENA_DISCUSSIONS', 'Diskusije');
DEFINE('_KUNENA_TOTAL_THREADS', 'Ukupno tema:');
DEFINE('_SHOW_DEFAULT', 'Podrazumevano');
DEFINE('_SHOW_MONTH', 'mesec');
DEFINE('_SHOW_YEAR', 'godina');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Kopiram „%src%“ u „%dst%“...');
DEFINE('_KUNENA_COPY_OK', 'U redu');
DEFINE('_KUNENA_CSS_SAVE', 'Skladištenje css datoteke treba da bude ovde...datoteka=„%file%“');
DEFINE('_KUNENA_UP_ATT_10', 'Tabela priloga je uspešno nadgrađena na strukturu najnovije 1.0.x serije!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Prilozi u tabeli poruka su uspešno nadgrađeni na strukturu najnovije 1.0.x serije!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Ne mogu da unapredim odgovore u hijerarhiji poruka. Ništa nije obrisano.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Ne mogu da obrišem poruku(e) - ništa drugo nije obrisano');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Ne mogu da obrišem tekst poruke(a). Ažurirajte bazu podataka ručno (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Sve je obrisano, ali ažuriranje korisničke statistike poruka nije uspelo!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Ozbiljna greška u bazi podataka. Ažurirajte bazu podataka ručno tako da odgovori na temu takođe odgovaraju novom forumu");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena komponenta je uspešno deinstalirana!");
DEFINE('_KUNENA_PDF_VERSION', 'Verzija Kunena forum komponente: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Napravljeno: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Nema foruma za pretragu.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Greška pri dodavanju korisnika:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Korisnici su usaglašeni; izbrisano:');
DEFINE('_KUNENA_USERSSYNCADD', ', dodato:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'profila korisnika.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Nema profila za usaglašavanje.');
DEFINE('_KUNENA_SYNC_USERS', 'Usaglasi korisnike');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Usaglasi Kunena tabelu korisnika sa Joomla! tabelom');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'E-pošta administratoru');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Postavite na „Da“ ako želite da za svaku novu poruku bude poslato obaveštenje e-poštom svim aktivnim administratorima.');
DEFINE('_KUNENA_RANKS_EDIT', 'Izmena rangova');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Sakrij e-poštu');
DEFINE('_KUNENA_DT_DATE_FMT','%d.%m.%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d.%m.%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'nedelja');
DEFINE('_KUNENA_DT_LDAY_MON', 'ponedeljak');
DEFINE('_KUNENA_DT_LDAY_TUE', 'utorak');
DEFINE('_KUNENA_DT_LDAY_WED', 'sreda');
DEFINE('_KUNENA_DT_LDAY_THU', 'četvrtak');
DEFINE('_KUNENA_DT_LDAY_FRI', 'petak');
DEFINE('_KUNENA_DT_LDAY_SAT', 'subota');
DEFINE('_KUNENA_DT_DAY_SUN', 'ned');
DEFINE('_KUNENA_DT_DAY_MON', 'pon');
DEFINE('_KUNENA_DT_DAY_TUE', 'uto');
DEFINE('_KUNENA_DT_DAY_WED', 'sre');
DEFINE('_KUNENA_DT_DAY_THU', 'čet');
DEFINE('_KUNENA_DT_DAY_FRI', 'pet');
DEFINE('_KUNENA_DT_DAY_SAT', 'sub');
DEFINE('_KUNENA_DT_LMON_JAN', 'januar');
DEFINE('_KUNENA_DT_LMON_FEB', 'februar');
DEFINE('_KUNENA_DT_LMON_MAR', 'mart');
DEFINE('_KUNENA_DT_LMON_APR', 'april');
DEFINE('_KUNENA_DT_LMON_MAY', 'maj');
DEFINE('_KUNENA_DT_LMON_JUN', 'jun');
DEFINE('_KUNENA_DT_LMON_JUL', 'jul');
DEFINE('_KUNENA_DT_LMON_AUG', 'avgust');
DEFINE('_KUNENA_DT_LMON_SEP', 'septembar');
DEFINE('_KUNENA_DT_LMON_OCT', 'oktobar');
DEFINE('_KUNENA_DT_LMON_NOV', 'novembar');
DEFINE('_KUNENA_DT_LMON_DEV', 'decembar');
DEFINE('_KUNENA_DT_MON_JAN', 'jan');
DEFINE('_KUNENA_DT_MON_FEB', 'feb');
DEFINE('_KUNENA_DT_MON_MAR', 'mar');
DEFINE('_KUNENA_DT_MON_APR', 'apr');
DEFINE('_KUNENA_DT_MON_MAY', 'maj');
DEFINE('_KUNENA_DT_MON_JUN', 'jun');
DEFINE('_KUNENA_DT_MON_JUL', 'jul');
DEFINE('_KUNENA_DT_MON_AUG', 'avg');
DEFINE('_KUNENA_DT_MON_SEP', 'sep');
DEFINE('_KUNENA_DT_MON_OCT', 'okt');
DEFINE('_KUNENA_DT_MON_NOV', 'nov');
DEFINE('_KUNENA_DT_MON_DEV', 'dec');
DEFINE('_KUNENA_CHILD_BOARD', 'Podforum');
DEFINE('_WHO_ONLINE_GUEST', 'Gost');
DEFINE('_WHO_ONLINE_MEMBER', 'Član');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'nema');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Procesor slika:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Kliknite ovde za nastavak...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Primeni!');
DEFINE('_KUNENA_NO_ACCESS', 'Nemate pristup ovom forumu!');
DEFINE('_KUNENA_TIME_SINCE', 'pre %time%');
DEFINE('_KUNENA_DATE_YEARS', 'godina');
DEFINE('_KUNENA_DATE_MONTHS', 'meseci');
DEFINE('_KUNENA_DATE_WEEKS','nedelja');
DEFINE('_KUNENA_DATE_DAYS', 'dana');
DEFINE('_KUNENA_DATE_HOURS', 'časova');
DEFINE('_KUNENA_DATE_MINUTES', 'minuta');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Da li sigurno želite da uklonite probne podatke? Ova radnja je nepovratna.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Zaglavlje foruma:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', 'Prikaz foruma');
DEFINE('_KUNENA_CLASS_SFX', 'Dodatak CSS klase foruma');
DEFINE('_KUNENA_CLASS_SFXDESC', 'CSS dodatak primenjen na index, showcat, view i dopušta različite dizajne za forume.');
DEFINE('_COM_A_USER_EDIT_TIME', 'Vreme korisničke izmene');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Postavite na 0 za neograničeno vreme, inače dozvoljen
prozor u sekundama za izmene od objavljivanja ili poslednje promene.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Vreme odlaganja korisničke izmene');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Podrazumevano 600 [sekundi], dopušta
čuvanje izmene do 600 sekundi nakon što veza za uređivanje nestane');
DEFINE('_KUNENA_HELPPAGE','Uključi stranicu pomoći');
DEFINE('_KUNENA_HELPPAGE_DESC','Ako postavite na „Da“ u meniju zaglavlja će biti prikazana veza do stranice za pomoć.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Prikaži pomoć u Kuneni');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Ako postavite na „Da“ tekstualni sadržaj pomoći će biti uključen u Kunenu i veza ka spoljašnjoj stranici za pomoć neće raditi. <b>Napomena:</b> Treba da dodate „ID sadržaja pomoći“.');
DEFINE('_KUNENA_HELPPAGE_CID','ID sadržaja pomoći');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Stavku „Prikaži pomoć u Kuneni“ treba da postavite na <b>„DA“</b>.');
DEFINE('_KUNENA_HELPPAGE_LINK','Veza ka spoljašnjoj stranici pomoći');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Ako prikazujete vezu ka spoljašnjoj stranici pomoći, stavku „Prikaži pomoć u Kuneni“ postavite na <b>„NE“</b>.');
DEFINE('_KUNENA_RULESPAGE','Uključi stranicu pravila');
DEFINE('_KUNENA_RULESPAGE_DESC','Ako postavite na „Da“ u meniju zaglavlja će biti prikazana veza do stranice sa pravilima.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Prikaži pravila u Kuneni');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Ako postavite na „Da“ tekstualni sadržaj pravila će biti uključen u Kunenu i veza ka spoljašnjoj stranici sa pravilima neće raditi. <b>Napomena:</b> Treba da dodate „ID sadržaja pravila“.');
DEFINE('_KUNENA_RULESPAGE_CID','ID sadržaja pravila');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Stavku „Prikaži pravila u Kuneni“ treba da postavite na <b>„DA“</b>.');
DEFINE('_KUNENA_RULESPAGE_LINK','Veza ka spoljašnjoj stranici pravila');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Ako prikazujete vezu ka spoljašnjoj stranici pravila, stavku „Prikaži pravila u Kuneni“ postavite na <b>„NE“</b>.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD biblioteka nije pronađena');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 biblioteka nije pronađena');
DEFINE('_KUNENA_GD_INSTALLED','GD je dostupna u verziji ');
DEFINE('_KUNENA_GD_NO_VERSION','Ne mogu da otkrijem GD verziju');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD nije instalirana, više podataka možete naći ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Visina male slike :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Širina male slike :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Visina srednje slike :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Širina srednje slike :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Visina velike slike :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Širina velike slike :');
DEFINE('_KUNENA_AVATAR_QUALITY','Kvalitet avatara');
DEFINE('_KUNENA_WELCOME','Dobro došli na Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','Hvala što ste izabrali Kunenu kao rešenje za vaš forum. Ovaj ekran pruža kratak pregled razne statistike vašeg foruma. Veze sa leve strane ovog ekrana omogućuju kontrolu ugođaja foruma u svakom mogućem pogledu. Svaka stranica će sadržati uputstva o upotrebi svojih alata.');
DEFINE('_KUNENA_STATISTIC','Statistika');
DEFINE('_KUNENA_VALUE','Vrednost');
DEFINE('_GEN_CATEGORY','Kategorija');
DEFINE('_GEN_STARTEDBY','Pokrenuo/la je: ');
DEFINE('_GEN_STATS','statistika');
DEFINE('_STATS_TITLE',' forum - statistika');
DEFINE('_STATS_GEN_STATS','Opšta statistika');
DEFINE('_STATS_TOTAL_MEMBERS','Članovi:');
DEFINE('_STATS_TOTAL_REPLIES','Odgovori:');
DEFINE('_STATS_TOTAL_TOPICS','Teme:');
DEFINE('_STATS_TODAY_TOPICS','Današnje teme:');
DEFINE('_STATS_TODAY_REPLIES','Današnji odgovori:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategorije:');
DEFINE('_STATS_TOTAL_SECTIONS','Odeljci:');
DEFINE('_STATS_LATEST_MEMBER','Najnoviji član:');
DEFINE('_STATS_YESTERDAY_TOPICS','Jučerašnje teme:');
DEFINE('_STATS_YESTERDAY_REPLIES','Jučerašnji odgovori:');
DEFINE('_STATS_POPULAR_PROFILE','Popularnih 10 članova (poseta profilu)');
DEFINE('_STATS_TOP_POSTERS','Najaktivniji članovi');
DEFINE('_STATS_POPULAR_TOPICS','Najpopularnije teme');
DEFINE('_COM_A_STATSPAGE','Uključi stranicu statistike');
DEFINE('_COM_A_STATSPAGE_DESC','Ako je postavljeno na „Da“, biće prikazana javna veza u meniju zaglavlja stranice za statistiku foruma. Ova stranica će prikazivati raznu statistiku o vašem forumu. <em>Stranica za statistiku će uvek biti dostupna administratorima bez obzira na ovu postavku!</em>');
DEFINE('_COM_C_JBSTATS','Statistika foruma');
DEFINE('_COM_C_JBSTATS_DESC','Statistika foruma');
define('_GEN_GENERAL','Opšte');
define('_PERM_NO_READ','Ne posedujete dovoljna ovlašćenja da pristupite ovom forumu.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smajli je sačuvan');
DEFINE ('_KUNENA_SMILEY_DELETED','Smajli je obrisan');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Kod već postoji');
DEFINE ('_KUNENA_MISSING_PARAMETER','Nedostaje parametar');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rang već postoji');
DEFINE ('_KUNENA_RANK_DELETED','Rang je obrisan');
DEFINE ('_KUNENA_RANK_SAVED','Rang je sačuvan');
DEFINE ('_KUNENA_DELETE_SELECTED','Obriši izabrano');
DEFINE ('_KUNENA_MOVE_SELECTED','Premesti izabrano');
DEFINE ('_KUNENA_REPORT_LOGGED','Zapisano');
DEFINE ('_KUNENA_GO','Idi');
DEFINE('_KUNENA_MAILFULL','Uključi potpun sadržaj poruke u e-poštu poslatu pretplatnicima');
DEFINE('_KUNENA_MAILFULL_DESC','Ako je Ne - pretplatnici će primiti samo naslove novih poruka');
DEFINE('_KUNENA_HIDETEXT','Prijavite se da bi videli sadržaj!');
DEFINE('_BBCODE_HIDE','Skriven tekst: [hide]neki skriven tekst[/hide] - sakrijte deo poruke od gostiju');
DEFINE('_KUNENA_FILEATTACH','Priložena datoteka: ');
DEFINE('_KUNENA_FILENAME','Ime datoteke: ');
DEFINE('_KUNENA_FILESIZE','Veličina datoteke: ');
DEFINE('_KUNENA_MSG_CODE','Kod: ');
DEFINE('_KUNENA_CAPTCHA_ON','Sistem zaštite od neželjenih poruka');
DEFINE('_KUNENA_CAPTCHA_DESC','Uključivanje/isključivanje CAPTCHA sistema protiv neželjenih poruka i botova');
DEFINE('_KUNENA_CAPDESC','Unesite kod ovde');
DEFINE('_KUNENA_CAPERR','Kod nije ispravan!');
DEFINE('_KUNENA_COM_A_REPORT', 'Prijavljivanje poruka');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Ako želite da korisnici mogu prijaviti bilo koju poruku, izaberite da.');
DEFINE('_KUNENA_REPORT_MSG', 'Poruka je prijavljena');
DEFINE('_KUNENA_REPORT_REASON', 'Razlog');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Vaša poruka');
DEFINE('_KUNENA_REPORT_SEND', 'Pošalji izveštaj');
DEFINE('_KUNENA_REPORT', 'Izvesti moderatora');
DEFINE('_KUNENA_REPORT_RSENDER', 'Pošiljalac izveštaja: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Razlog izveštaja: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Poruka izveštaja: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Objavljivač poruke: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Tema poruke: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Poruka: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Veza ka poruci: ');
DEFINE('_KUNENA_REPORT_INTRO', 'poslali smo vam poruku zbog');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Izveštaj je uspešno poslat!');
DEFINE('_KUNENA_EMOTICONS', 'Emotikone');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smajli');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kod');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Uredi smajlija');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Uredi smajlije');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Traka emotikona');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nov smajli');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Još smajlija');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Zatvori prozor');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Dodatne emotikone');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Izaberite smajlija');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot podrška');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Uključi Joomla Mambot podršku');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Postavke dodatka za moj profil');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Dozvoli promenu korisničkog imena');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Dozvoli promenu korisničkog imena na stranici dodatka za moj profil');
DEFINE ('_KUNENA_RECOUNTFORUMS','Prebroj statistiku kategorija');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Sva statistika kategorija je sada prebrojana.');
DEFINE ('_KUNENA_EDITING_REASON','Razlog izmene');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Poslednja izmena');
DEFINE ('_KUNENA_BY','od');
DEFINE ('_KUNENA_REASON','Razlog');
DEFINE('_GEN_GOTOBOTTOM', 'Idi na dno');
DEFINE('_GEN_GOTOTOP', 'Idi na vrh');
DEFINE('_STAT_USER_INFO', 'Korisnički podaci');
DEFINE('_USER_SHOWEMAIL', 'Prikaži e-poštu'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Prikaži status veze');
DEFINE('_KUNENA_HIDDEN_USERS', 'Skriveni korisnici');
DEFINE('_KUNENA_SAVE', 'Sačuvaj');
DEFINE('_KUNENA_RESET', 'Vrati na početno');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Podrazumevana galerija');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Lični podaci');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Sažetak');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Moj avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Postavke foruma');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Izgled i raspored');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Podaci mog profila');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Moje poruke');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Moje pretplate');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Moje omiljene');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Privatne poruke');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Prijemno sanduče');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nova poruka');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Otpremno sanduče');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Smeće');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Postavke');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakti');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Spisak blokiranih');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Dodatni podaci');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Ime');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Korisničko ime');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-pošta');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Vrsta korisnika');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Datum registracije');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Datum poslednje posete');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Poruka');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Pregled profila');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Lični tekst');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Pol');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Rođendan');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Godina (GGGG) - Mesec (MM) - Dan (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Mesto');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Ovo je vaš ICQ broj.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Ovo je vaš AOL Instant Messenger nadimak.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Ovo je vaš Yahoo! Instant Messenger nadimak.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Ovo je vaš Skype nadimak.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Ovo je vaš Gtalk nadimak.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Veb stranica');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Naziv veb stranice');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Primer: Kunena!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL veb stranice');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Primer: www.kunena.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Vaša MSN messenger adresa e-pošte.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Potpis');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Muško');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Žensko');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Poruke su uspešno obrisane');
DEFINE('_KUNENA_DATE_YEAR', 'godinu');
DEFINE('_KUNENA_DATE_MONTH', 'mesec');
DEFINE('_KUNENA_DATE_WEEK','nedelju');
DEFINE('_KUNENA_DATE_DAY', 'dan');
DEFINE('_KUNENA_DATE_HOUR', 'čas');
DEFINE('_KUNENA_DATE_MINUTE', 'minut');
DEFINE('_KUNENA_IN_FORUM', ' u forumu: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum na: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Primite k znanju da, iako dugmad za kod foruma i smajlije nisu prikazana, ona se i dalje mogu koristiti');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Alati foruma');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Spisak korisnika');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s ima <b>%d</b> registrovanih korisnika');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Unesite ispravnu vrednost za pretragu!');
DEFINE ('_KUNENA_USRL_SEARCH','Pronađi korisnika/cu');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Traži');
DEFINE ('_KUNENA_USRL_LIST_ALL','Ispiši sve');
DEFINE ('_KUNENA_USRL_NAME','Ime');
DEFINE ('_KUNENA_USRL_USERNAME','Korisničko ime');
DEFINE ('_KUNENA_USRL_GROUP','Grupa');
DEFINE ('_KUNENA_USRL_POSTS','Poruka');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Poseta');
DEFINE ('_KUNENA_USRL_EMAIL','E-pošta');
DEFINE ('_KUNENA_USRL_USERTYPE','Vrsta korisnika');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Datum pridruživanja');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Poslednja prijava');
DEFINE ('_KUNENA_USRL_NEVER','Nikad');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Slika');
DEFINE ('_KUNENA_USRL_ASC','Rastući');
DEFINE ('_KUNENA_USRL_DESC','Opadajući');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Prikaz');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Dodaci');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Spisak korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Broj redova u spisku korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Broj redova u spisku korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Status veze korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Prikaži status veze korisnika');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Prikaži avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Prikaži pravo ime');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Prikaži korisničko ime');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Prikaži grupu korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Prikaži broj poruka');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Prikaži karmu');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Prikaži e-poštu');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Prikaži vrstu korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Prikaži datum pridruživanja');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Prikaži datum poslednje posete');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Prikaži posete profila');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Čarobnjak za bazu podataka');
DEFINE('_KUNENA_DBMETHOD', 'Izaberite kojom metodom želite da završite instalaciju:');
DEFINE('_KUNENA_DBCLEAN', 'Čista instalacija');
DEFINE('_KUNENA_DBUPGRADE', 'Nadgradnja sa Joomlaboarda');
DEFINE('_KUNENA_TOPLEVEL', 'Kategorija najvišeg nivoa');
DEFINE('_KUNENA_REGISTERED', 'Registrovano');
DEFINE('_KUNENA_PUBLICBACKEND', 'Javno začelje');
DEFINE('_KUNENA_SELECTANITEMTO', 'Izaberite stavku za');
DEFINE('_KUNENA_ERRORSUBS', 'Došlo je do neke greške pri brisanju poruka i pretplata');
DEFINE('_KUNENA_WARNING', 'Upozorenje...');
DEFINE('_KUNENA_CHMOD1', 'Morate izvršiti chmod 766 nad ovom datotekom kako bi mogla da bude ažurirana.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Datoteka podešavanja je');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II otvorenog koda');
DEFINE('_KUNENA_UDDEIM', 'uddeIM');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Izaberite šablon');
DEFINE('_KUNENA_CONFIGSAVED', 'Podešavanja su sačuvana.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'KOBNA GREŠKA: Podešavanja nije bilo moguće sačuvati.');
DEFINE('_KUNENA_TFINW', 'Datoteka nije upisiva.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS datoteka je sačuvana.');
DEFINE('_KUNENA_SELECTMODTO', 'Izaberite moderatora za');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Morate izabrati forum za pročišćavanje!');
DEFINE('_KUNENA_DELMSGERROR', 'Neuspelo brisanje poruka:');
DEFINE('_KUNENA_DELMSGERROR1', 'Neuspelo brisanje tekstova poruka:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Neuspelo brisanje pretplata:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum se pročišćava za');
DEFINE('_KUNENA_PRUNEDAYS', 'dana');
DEFINE('_KUNENA_PRUNEDELETED', 'Obrisano:');
DEFINE('_KUNENA_PRUNETHREADS', 'tema');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Greška pri čišćenju korisnika:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Korisnici su pročišćeni. Obrisano:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'korisničkih profila'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Nisu pronađeni podobni profili za pročišćavanje.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena tabele su nadgrađene na verziju');
DEFINE('_KUNENA_FORUMCATEGORY', 'Kategorija foruma');
DEFINE('_KUNENA_SAMPLWARN1', '-- Budite potpuno sigurni da učitavate probne podatke u potpuno prazne Kunena tabele. Ako nečega ima u njima, ovo neće raditi!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Probna poruka 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Probni forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Probna poruka[/color][/size][/b]\nČestitke na vašem novom forumu!\n\n[url=http://kunena.com]- Kunena[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Probni podaci su učitani');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Probni podaci su uklonjeni');
DEFINE('_KUNENA_CBADDED', 'Community Builder profil je dodat');
DEFINE('_KUNENA_IMGDELETED', 'Slika je obrisana');
DEFINE('_KUNENA_FILEDELETED', 'Datoteka je obrisana');
DEFINE('_KUNENA_NOPARENT', 'Nema roditelja');
DEFINE('_KUNENA_DIRCOPERR', 'Greška: Datoteku');
DEFINE('_KUNENA_DIRCOPERR1', 'nije bilo moguće kopirati!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena forum</strong> komponenta <em>za Joomla! </em> <br />&copy; 2008 - 2009 www.kunena.com<br />Sva prava zadržana.');
DEFINE('_KUNENA_INSTALL2', 'Prebacivanje/instaliranje :</code></strong><br /><br /><font color="red"><b>uspešno');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Podešavanja profila');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Ako imate instalirane Clexus PM ili Community Builder komponente, možete podesiti da Kunena koristi stranicu korisničkog profila.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Pregled profila</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Sve poruke foruma');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Teme');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Započeo/la');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorije');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Datum');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Poseta');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Nema poruka u forumu');
DEFINE('_KUNENA_TOTALFAVORITE', 'Omiljeni:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Broj kolona podforuma  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Broj kolona pri formiranju podforuma pod glavnom kategorijom ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Podrazumevano štiklirana pretplata na poruku?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Postavite na „Da“ ako želite da kućica za pretplatu poruke bude uvek štiklirana');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategorija / forum mora imati naziv');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Prikaži statistiku');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Ako želite da prikažete statistiku, izaberite Da');
DEFINE('_KUNENA_SHOWWHOIS', 'Prikaži ko je na vezi');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Ako želite da prikažete ko je na vezi, izaberite Da');
DEFINE('_KUNENA_STATSGENERAL', 'Prikaži opštu statistiku');
DEFINE('_KUNENA_STATSGENERALDESC', 'Ako želite da prikažete opštu statistiku, izaberite Da');
DEFINE('_KUNENA_USERSTATS', 'Prikaži statistiku popularnih korisnika');
DEFINE('_KUNENA_USERSTATSDESC', 'Ako želite da prikažete statistiku popularnosti, izaberite Da');
DEFINE('_KUNENA_USERNUM', 'Broj popularnih korisnika');
DEFINE('_KUNENA_USERPOPULAR', 'Prikaži statistiku popularnih tema');
DEFINE('_KUNENA_USERPOPULARDESC', 'Ako želite da prikažete popularne teme, izaberite Da');
DEFINE('_KUNENA_NUMPOP', 'Broj popularnih tema');
DEFINE('_KUNENA_INFORMATION',
    'Kunena tim sa ponosom najavljuje Kunena 1.0.5 izdanje. Ovo je moćna i moderna komponenta foruma za veoma zasluženi sistem upravljanja sadržajem, Joomla!. Početno je zasnovan na teškom radu Joomlaboard i Fireboad timova i većina naših pohvala ide njima. Neke od glavnih mogućnosti Kunene su navedene ispod (pored postojećih mogućnosti JB-a):<br /><br /><ul><li>Sistem foruma mnogo više naklonjen dizajnerima. Blizak je SMF sistemu šablona sa jednostavnijom strukturom. U samo nekoliko koraka možete izmeniti potpun izgled foruma. Zahvalnice idu sjajnim dizajnerima u našem timu.</li><li>Neograničeni sistem podkategorija sa boljim administracionim začeljem.</li><li>Brži sistem i lakše iskustvo kodiranja za dodatke sa strane.</li><li>Isti<br /></li><li>Profilebox na vrhu foruma</li><li>Podrška za poznate PM sisteme, poput ClexuxPM i uddeIM</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />Ovo je zajednički rad nekoliko programera i dizajnera koji su ljubazno učestvovali i učinili ovo izdanje mogućim. Ovde se zahvaljujemo svima i želimo da uživate u ovom izdanju!<br /><br />Kunena! tim<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Uputstva');
DEFINE('_KUNENA_FINFO', 'Informacije Kunena foruma');
DEFINE('_KUNENA_CSSEDITOR', 'Uređivač Kunena CSS šablona');
DEFINE('_KUNENA_PATH', 'Putanja:');
DEFINE('_KUNENA_CSSERROR', 'Napomena: datoteka CSS šablona mora biti upisiva pri čuvanju izmena.');
// User Management
DEFINE('_KUNENA_FUM', 'Upravnik Kunena korisničkih profila');
DEFINE('_KUNENA_SORTID', 'poređaj po korisničkom id-u');
DEFINE('_KUNENA_SORTMOD', 'poređaj po moderatoru');
DEFINE('_KUNENA_SORTNAME', 'poređaj po imenu');
DEFINE('_KUNENA_VIEW', 'Pregled');
DEFINE('_KUNENA_NOUSERSFOUND', 'Nisu pronađeni korisnički profili.');
DEFINE('_KUNENA_ADDMOD', 'Dodaj moderatora u');
DEFINE('_KUNENA_NOMODSAV', 'Nisu pronađeni mogući moderatori. Pročitajte „belešku“ ispod.');
DEFINE('_KUNENA_NOTEUS',
    'BELEŠKA: Ovde su prikazani samo korisnici za koje je postavljena moderatorska oznaka u Kunena profilu. Dajte korisniku oznaku moderatora da bi imali mogućnost dodavanja moderatora, posetite <a href="index2.php?option=com_kunena&task=profiles">Administraciju korisnika</a> i potražite korisnika koga želite učiniti moderatorom. Zatim izaberite njegov ili njen profil i ažurirajte ga. Moderatorsku oznaku može postavljati samo administrator sajta. ');
DEFINE('_KUNENA_PROFFOR', 'Profil za');
DEFINE('_KUNENA_GENPROF', 'Opšte opcije profila');
//DEFINE('_KUNENA_PREFVIEW', 'Željena vrsta pregleda:');
DEFINE('_KUNENA_PREFOR', 'Željeni poredak poruka:');
DEFINE('_KUNENA_ISMOD', 'Je moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Da</strong> (nije promenljivo, ovaj korisnik je (super)administrator sajta)');
DEFINE('_KUNENA_COLOR', 'Boja');
DEFINE('_KUNENA_UAVATAR', 'Avatar korisnika:');
DEFINE('_KUNENA_NS', 'Nema izabranih');
DEFINE('_KUNENA_DELSIG', ' štiklirajte ovu kućicu za brisanje ovog potpisa');
DEFINE('_KUNENA_DELAV', ' štiklirajte ovu kućicu za brisanje ovog avatara');
DEFINE('_KUNENA_SUBFOR', 'Pretplate za');
DEFINE('_KUNENA_NOSUBS', 'Nisu pronađene pretplate ovog korisnika');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Osnove');
DEFINE('_KUNENA_BASICSFORUM', 'Osnovni podaci foruma');
DEFINE('_KUNENA_PARENT', 'Roditelj:');
DEFINE('_KUNENA_PARENTDESC',
    'Primite k znanju: Za pravljenje kategorije, izaberite „Kategoriju najvišeg nivoa“ kao roditelja. Kategorija vrši ulogu spremišta za forume.<br />Forum se može napraviti <strong>samo</strong> unutar kategorije biranjem prethodno napravljene kategorije kao roditelja za taj forum.<br /> Poruke <strong>NE</strong> mogu biti postavljene u kategorije; samo u forume.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Naziv i opis foruma');
DEFINE('_KUNENA_NAMEADD', 'Ime:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Opis:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Napredna podešavanja foruma');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Sigurnost foruma i pristup');
DEFINE('_KUNENA_LOCKEDDESC', 'Postavite na „Da“ ako želite da zaključate ovaj forum. Niko, sem moderatora i administratora ne može praviti nove teme i odgovore u zaključanom forumu (ili premeštati poruke u njega).');
DEFINE('_KUNENA_LOCKED1', 'Zaključano:');
DEFINE('_KUNENA_PUBACC', 'Nivo javnog pristupa:');
DEFINE('_KUNENA_PUBACCDESC',
    'Za pravljenje foruma koji nisu javni, ovde možete navesti najniži korisnički nivo koji može videti/pristupiti forumu. Najniži korisnički nivo je podrazumevano postavljen na „Svi“.<br /><b>Primite k znanju</b>: ako jednoj ili više grupa ograničite pristup celoj kategoriji, svi forumi koje ona sadrži će biti sakriveni svakome ko nema ispravne privilegije za kategoriju <b>čak</b> i ako jedan ili više ovih foruma imaju postavljen niži nivo pristupa! Ovo važi i za moderatore, moraćete da dodate moderatora spisku moderatora kategorije ako on ili ona ne poseduje ispravan nivo grupe za pregled kategorije.<br /> Ovo je nevezano za činjenicu da kategorije ne mogu da budu moderirane. Moderatore je i dalje moguće dodati na spisak moderatora.');
DEFINE('_KUNENA_CGROUPS', 'Uključi podgrupe:');
DEFINE('_KUNENA_CGROUPSDESC', 'Treba li takođe dozvoliti pristup podgrupama? Ako je postavljeno na „Ne“, pristup ovom forumu je ograničen <b>samo</b> na izabranu grupu');
DEFINE('_KUNENA_ADMINLEVEL', 'Nivo administrativnog pristupa:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Ako napravite forum sa ograničenjima javnog pristupa, ovde možete navesti dodatne nivoe administrativnog pristupa.<br /> Ako ograničite pristup forumu samo posebnoj korisničkoj grupi javnog sučelja, a ovde ne navedete grupu javnog začelja, administratori neće moći da pristupe/vide forum.');
DEFINE('_KUNENA_ADVANCED', 'Napredno');
DEFINE('_KUNENA_CGROUPS1', 'Uključi podgrupe:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Treba li takođe dozvoliti pristup podgrupama? Ako je postavljeno na „Ne“, pristup ovom forumu je ograničen <b>samo</b> na izabranu grupu');
DEFINE('_KUNENA_REV', 'Pregledaj poruke:');
DEFINE('_KUNENA_REVDESC',
    'Postavite na „Da“ ako želite da poruke pregledaju moderatori pre njihovog objavljivanja u ovom forumu. Ovo je korisno samo u moderiranim forumima!<br />Ako postavite ovo bez određenih moderatora, samo je administrator sajta odgovoran za odobravanje/brisanje podnetih poruka jer će one biti „zadržane“!');
DEFINE('_KUNENA_MOD_NEW', 'Moderacija');
DEFINE('_KUNENA_MODNEWDESC', 'Moderacija i moderatori foruma');
DEFINE('_KUNENA_MOD', 'Moderira:');
DEFINE('_KUNENA_MODDESC',
    'Postavite na „Da“ ako želite da imate mogućnost određivanja moderatora za ovaj forum.<br /><strong>Napomena:</strong> Ovo ne znači da nove poruke moraju biti pregledane pre njihovog objavljivanja na forumu!<br /> Za to morate postaviti opciju „Pregledanje“ na naprednom jezičku.<br /><br /> <strong>Primite k znanju:</strong> Nakon podešavanja Moderacije na „Da“, morate prvo sačuvati podešavanja foruma pre nego što možete dodavati Moderatore koristeći dugme za nove.');
DEFINE('_KUNENA_MODHEADER', 'Podešavanja moderacije za ovaj forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatori određeni za ovaj forum:');
DEFINE('_KUNENA_NOMODS', 'Nisu određeni moderatori za ovaj forum');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Uredi');
DEFINE('_KUNENA_ADD', 'Dodaj');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Pomeri iznad');
DEFINE('_KUNENA_MOVEDOWN', 'Pomeri ispod');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Svi registrovani');
DEFINE('_KUNENA_EVERYBODY', 'Svi');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Preuredi');
DEFINE('_KUNENA_CHECKEDOUT', 'Proveri');
DEFINE('_KUNENA_ADMINACCESS', 'Administrativni pristup');
DEFINE('_KUNENA_PUBLICACCESS', 'Javni pristup');
DEFINE('_KUNENA_PUBLISHED', 'Objavljeno');
DEFINE('_KUNENA_REVIEW', 'Pregled');
DEFINE('_KUNENA_MODERATED', 'Moderirano');
DEFINE('_KUNENA_LOCKED', 'Zaključano');
DEFINE('_KUNENA_CATFOR', 'Kategorija / Forum');
DEFINE('_KUNENA_ADMIN', 'Kunena administracija');
DEFINE('_KUNENA_CP', 'Kunena kontrolna tabla');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integracija avatara');
DEFINE('_COM_A_RANKS_SETTINGS', 'Rangovi');
DEFINE('_COM_A_RANKING_SETTINGS', 'Podešavanja rangiranja');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Podešavanja avatara');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Podešavanja sigurnosti');
DEFINE('_COM_A_BASIC_SETTINGS', 'Osnovne postavke');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Uključi Omiljene');
DEFINE('_COM_A_FAVORITES_DESC', 'Postavite na „Da“ ako želite da omogućite registrovanim korisnicima označavanje teme kao omiljene ');
DEFINE('_USER_UNFAVORITE_ALL', 'Skidanje oznake <b><u>omiljena</u></b> sa svih tema (uključujući i nevidljive radi otklanjanja grešaka)');
DEFINE('_VIEW_FAVORITETXT', 'Označi temu kao omiljenu ');
DEFINE('_USER_UNFAVORITE_YES', 'Skinuta je oznaka omiljene sa ove teme.');
DEFINE('_POST_FAVORITED_TOPIC', 'Ova tema je dodata u vaše omiljene.');
DEFINE('_VIEW_UNFAVORITETXT', 'Skini omiljenu');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Otkaži pretplatu');
DEFINE('_USER_NOFAVORITES', 'Nema omiljenih');
DEFINE('_POST_SUCCESS_FAVORITE', 'Vaš zahtev za dodavanje u omiljene je obrađen.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Rezultati pretraživanja');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Poruke po stranici rezultata pretraživanja');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Koristiti Joomla stil?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Ako želite koristiti Joomla stilove, postavite na DA. (class: kao sectionheader, sectionentry1...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Prikaži sliku podkategorije');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Ako želite prikazati malu ikonu uz podkategorije na listi foruma, postavite ovo na DA. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Prikaži obaveštenja');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Postavite na „Da“ ako želite da prikažete okvir za obaveštenja na glavnoj stranici foruma.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Prikazati avatar na spisku kategorija?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Postavite na „Da“ ako želite da prikažete korisničke avatare na spisku kategorija foruma.');
DEFINE('_KUNENA_RECENT_POSTS', 'Podešavanja najnovijih poruka');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Prikaži najnovije poruke');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Postavite na „Da“ ako želite da prikažete dodatak za najnovije poruke na forumu');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Broj najnovijih poruka');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Broj najnovijih poruka');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Broj po jezičku ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Broj poruka po jednom jezičku');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Prikaži kategoriju');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Određena kategorija iz koje će se prikazivati najnovije poruke. Na primer:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Prikaži jedinstven naslov');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Prikaži jedinstven naslov');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Prikaži naslov odgovora');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', ' Prikaži naslov odgovora (Odg:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Dužina naslova');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Dužina naslova');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Prikaži datum');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Prikaži datum');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Prikaži broj poseta');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Prikaži broj poseta');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Prikaži autora');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=korisničko ime, 2=pravo ime, 0=ništa');
DEFINE('_KUNENA_STATS', 'Podešavanja statističkih dodataka ');
DEFINE('_KUNENA_CATIMAGEPATH', 'Putanja do slike kategorije ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Putanja do slike za kategoriju. Ako unesete „category_images/“ putanja će biti „vasa_korenska_html_fascikla/images/fbfiles/category_images/“');
DEFINE('_KUNENA_ANN_MODID', 'ID-ovi moderatora obaveštenja ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Dodajte id-ove korisnika koji mogu moderisati obaveštenja, npr. 62,63,73. Moderatori obaveštenja ih mogu dodavati, uređivati i brisati.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Kategorije foruma ');
DEFINE('_KUNENA_CHILD_BOARDS', 'Podforumi ');
DEFINE('_KUNENA_QUICKMSG', 'Brzi odgovor ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Niti u forumu ');
DEFINE('_KUNENA_FORUM', 'Forum ');
DEFINE('_KUNENA_SPOTS', 'Najzanimljivije');
DEFINE('_KUNENA_CANCEL', 'Odustani');
DEFINE('_KUNENA_TOPIC', 'TEMA: ');
DEFINE('_KUNENA_POWEREDBY', 'U pogonu je ');
// Time Format
DEFINE('_TIME_TODAY', '<b>danas</b> ');
DEFINE('_TIME_YESTERDAY', '<b>juče</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Poslednje poruke');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Ko je na vezi');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Glavna foruma');
DEFINE('_KUNENA_GUEST', 'Gost');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'pregleda');
DEFINE('_KUNENA_ATTACH', 'Prilog');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Omiljena');
DEFINE('_USER_FAVORITES', 'Moje omiljene teme');
DEFINE('_THREAD_UNFAVORITE', 'Ukloni iz omiljenih');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Dobro došli');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Prikaži poslednje poruke');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Postavi moj avatar');
DEFINE('_PROFILEBOX_MYPROFILE', 'Moj profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Prikaži moje poruke');
DEFINE('_PROFILEBOX_GUEST', 'gost');
DEFINE('_PROFILEBOX_LOGIN', 'Prijavite se');
DEFINE('_PROFILEBOX_REGISTER', 'registrujte');
DEFINE('_PROFILEBOX_LOGOUT', 'Odjava');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Zaboravili ste lozinku?');
DEFINE('_PROFILEBOX_PLEASE', '');
DEFINE('_PROFILEBOX_OR', 'ili');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Najnovije poruke');
DEFINE('_RECENT_TOPICS', 'Tema');
DEFINE('_RECENT_AUTHOR', 'Autor');
DEFINE('_RECENT_CATEGORIES', 'Kategorije');
DEFINE('_RECENT_DATE', 'Datum');
DEFINE('_RECENT_HITS', 'Poseta');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Obaveštenja');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Datum');
DEFINE('_ANN_TITLE', 'Naslov');
DEFINE('_ANN_SORTTEXT', 'Kratki tekst');
DEFINE('_ANN_LONGTEXT', 'Dugi tekst');
DEFINE('_ANN_ORDER', 'Redosled');
DEFINE('_ANN_PUBLISH', 'Objavi');
DEFINE('_ANN_PUBLISHED', 'Objavljeno');
DEFINE('_ANN_UNPUBLISHED', 'Neobjavljeno');
DEFINE('_ANN_EDIT', 'Uredi');
DEFINE('_ANN_DELETE', 'Obriši');
DEFINE('_ANN_SUCCESS', 'Uspešno');
DEFINE('_ANN_SAVE', 'Sačuvaj');
DEFINE('_ANN_YES', 'Da');
DEFINE('_ANN_NO', 'Ne');
DEFINE('_ANN_ADD', 'Dodaj novo');
DEFINE('_ANN_SUCCESS_EDIT', 'Uspešno uređeno');
DEFINE('_ANN_SUCCESS_ADD', 'uspešno dodato');
DEFINE('_ANN_DELETED', 'Uspešno obrisano');
DEFINE('_ANN_ERROR', 'GREŠKA');
DEFINE('_ANN_READMORE', 'Opširnije...');
DEFINE('_ANN_CPANEL', 'Kontrolna tabla za obaveštenja');
DEFINE('_ANN_SHOWDATE', 'Prikaži datum');
// Stats
DEFINE('_STAT_FORUMSTATS', 'statistika foruma');
DEFINE('_STAT_GENERAL_STATS', 'Opšta statistika');
DEFINE('_STAT_TOTAL_USERS', 'Ukupno korisnika/ca');
DEFINE('_STAT_LATEST_MEMBERS', 'Najnoviji/a korisnik/ca');
DEFINE('_STAT_PROFILE_INFO', 'Pogledaj informacije profila');
DEFINE('_STAT_TOTAL_MESSAGES', 'Ukupno poruka');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Ukupno tema');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Ukupno kategorija');
DEFINE('_STAT_TOTAL_SECTIONS', 'Ukupno odeljaka');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Otvoreno danas');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Otvoreno juče');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Ukupno odgovora danas');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Ukupno odgovora juče');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Pogledaj najnovije poruke');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Opširnije o statistici');
DEFINE('_STAT_USERLIST', 'Spisak korisnika');
DEFINE('_STAT_TEAMLIST', 'Tim foruma');
DEFINE('_STATS_FORUM_STATS', 'statistika foruma');
DEFINE('_STAT_POPULAR', 'Popularnih');
DEFINE('_STAT_POPULAR_USER_TMSG', 'korisnika (ukupno poruka) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'tema ');
DEFINE('_STAT_POPULAR_USER_GSG', 'korisnika (ukupno poseta profilu) ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Korisnik je trenutno na vezi');
DEFINE('_MODLIST_OFFLINE', 'Korisnik nije na vezi');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Ko je na vezi');
DEFINE('_WHO_ONLINE_NOW', 'Na vezi je');
DEFINE('_WHO_ONLINE_MEMBERS', 'članova');
DEFINE('_WHO_AND', 'i');
DEFINE('_WHO_ONLINE_GUESTS', 'gostiju');
DEFINE('_WHO_ONLINE_USER', 'Korisnik');
DEFINE('_WHO_ONLINE_TIME', 'Vreme');
DEFINE('_WHO_ONLINE_FUNC', 'Akcija');
// Userlist
DEFINE('_USRL_USERLIST', 'Spisak korisnika');
DEFINE('_USRL_REGISTERED_USERS', '%s ima <b>%d</b> registrovanih korisnika');
DEFINE('_USRL_SEARCH_ALERT', 'Unesite vrednost za pretraživanje!');
DEFINE('_USRL_SEARCH', 'Nađi korisnika');
DEFINE('_USRL_SEARCH_BUTTON', 'Traži');
DEFINE('_USRL_LIST_ALL', 'Ispiši sve');
DEFINE('_USRL_NAME', 'Ime');
DEFINE('_USRL_USERNAME', 'Korisničko ime');
DEFINE('_USRL_EMAIL', 'E-pošta');
DEFINE('_USRL_USERTYPE', 'Tip korisnika');
DEFINE('_USRL_JOIN_DATE', 'Datum pridruživanja');
DEFINE('_USRL_LAST_LOGIN', 'Poslednja prijava');
DEFINE('_USRL_NEVER', 'Nikada');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'myPMS');
DEFINE('_USRL_ASC', 'Rastući');
DEFINE('_USRL_DESC', 'Opadajući');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Detalji');
DEFINE('_USRL_COMPROFILER', 'Profil');
DEFINE('_USRL_THUMBNAIL', 'Slika');
DEFINE('_USRL_READON', 'prikaži');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Pošalji ličnu poruku');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Rezultati pretraživanja za');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'Podešavanja spiska korisnika');
DEFINE('_USRL_ERROR', 'Greška');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Komponenta za lično dopisivanje');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Pretraživano je za: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Da li ste sigurni da želite da obrišete ovu poruku? \n\n NAPOMENA: Obrisanu poruku NE možete povratiti!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Poruka(e) je(su) izbrisana(e)');
DEFINE('_COM_A_RANKING', 'Rangiranje');
DEFINE('_COM_A_BOT_REFERENCE', 'Prikazivanje grafikona Bot reference');
DEFINE('_COM_A_MOSBOT', 'Uključivanje Bota za diskusiju');
DEFINE('_PREVIEW', 'Pregled');
DEFINE('_COM_A_MOSBOT_TITLE', 'Bot za diskusiju (Discussbot)');
DEFINE('_COM_A_MOSBOT_DESC', 'Bot za diskusiju omogućava korisnicima da diskutuju o sadržajima na forumu. Naslov sadržaja se upotrebljava kao naslov teme.'
           . '<br />Ukoliko tema ne postoji, pravi se nova. Ukoliko tema već postoji prikazuje se, kako bi odgovor bio moguć.' . '<br /><strong>Potrebno je zasebno preuzeti i instalirati ovaj Bot.</strong>'
           . '<br />pogledajte <a href="http://www.kunena.com">Kunena sajt</a> za više informacija.' . '<br />Nakon instalacije biće potrebno dodati sledeće linije bota u sadržaj:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br /><em>catid</em> je kategorija u kojoj se nalazi sadržaj koji se diskutuje. Kako bi pronašli catid, pogledajte forum ' . ' i proverite ID kategorije iz URL adrese u veb čitaču.'
           . '<br />Primer: ukoliko želite članak komentarisan u forumu pod catid 26, Bot bi trebalo da izgleda ovako: {mos_fb_discuss:26}'
           . '<br />Ovo može izgledati komplikovano, ali omogućava komentarisanje svakog dela sadržaja na forumu iz željenog dela foruma.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Pretraga');
DEFINE('_FORUM_SEARCHRESULTS', 'prikazivanje %s od %s rezultata.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'ČPP');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Pravila');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Uredite datoteku joomlaroot/administrator/components/com_kunena/language/kunena.serbian_lat.php kako biste postavili vaša pravila</li><li>Pravilo 2</li><li>Pravilo 3</li><li>Pravilo 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Kod foruma');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Poruka(e) je(su) odobrena(e)');
DEFINE('_MODERATION_DELETE_ERROR', 'GREŠKA: Poruka(e) ne može(mogu) biti obrisana(e)');
DEFINE('_MODERATION_APPROVE_ERROR', 'GREŠKA: Poruka(e) ne može(mogu) biti odobrena(e)');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Nema foruma u ovoj kategoriji!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Neuspelo pravljenje teme utvare u starom forumu!');
DEFINE('_POST_MOVE_GHOST', 'Ostavi poruku utvaru u starom forumu');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Brzi prelazak');
DEFINE('_COM_A_FORUM_JUMP', 'Omogući brzi prelazak');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'Podesite na „Da“ i izbornik koji će se pojaviti će omogućiti brzi prelazak na drugi forum ili temu.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Pravila');
DEFINE('_COM_A_RULESPAGE', 'Omogući stranicu sa pravilima');
DEFINE('_COM_A_RULESPAGE_DESC',
    'Ako je podešeno na „Da“, biće prikazana veza u meniju zaglavlja ka stranici sa pravilima. Ova stranica se može iskoristiti za stvari kao što su pravila foruma, itd. Možete izmeniti sadržaj ove datoteke po želji otvaranjem rules.php u /joomla_root/components/com_kunena. <em>Osigurajte da uvek imate rezervnu kopiju ove datoteke jer će biti pregažena tokom nadgradnje!</em>');
DEFINE('_MOVED_TOPIC', 'PREMEŠTENO:');
DEFINE('_COM_A_PDF', 'Omogući pravljenje PDF datoteka');
DEFINE('_COM_A_PDF_DESC',
    'Podesite na „Da“ ukoliko želite da omogućite korisnicima preuzimanje jednostavne PDF datoteke sa sadržajem teme.<br />To je <u>jednostavan</u> PDF dokument; nema oznaka, kitnjastog izgleda i slično, ali sadrži sav tekst koji se pojavljuje u temi.');
DEFINE('_GEN_PDFA', 'Kliknite na ovo dugme da biste napravili PDF datoteku iz ove teme (otvara se u novom prozoru).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Kliknite ovde da biste videli profil ovog korisnika');
DEFINE('_VIEW_ADDBUDDY', 'Kliknite ovde da biste dodali korisnika na vaš spisak ortaka');
DEFINE('_POST_SUCCESS_POSTED', 'Vaša poruka je uspešno objavljena');
DEFINE('_POST_SUCCESS_VIEW', '[ Povratak na temu ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Povratak na forum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Administrator');
DEFINE('_RANK_MODERATOR', 'Moderator');
DEFINE('_SHOW_LASTVISIT', 'od poslednje posete');
DEFINE('_COM_A_BADWORDS_TITLE', 'Filtriranje ružnih reči');
DEFINE('_COM_A_BADWORDS', 'Koristi filter za ružne reči');
DEFINE('_COM_A_BADWORDS_DESC', 'Podesite na „Da“ ukoliko želite da filtrirate poruke koje sadrže reči koje ste definisali u podešavanju komponente za ružne reči. Da biste koristili ovu opciju morate imati instaliranu komponentu za ružne reči!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Ova poruka je cenzurisana jer sadrži neku od reči koje je naveo administrator *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Pravljenje profila foruma za Community Builder');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Kliknite na ovu vezu da biste napravili neophodna polja foruma u Community Builder korisničkom profilu. Nakon što ih napravite, možete ih pomerati kada god želite koristeći Community Builder administraciju, samo nemojte menjati njihova imena i opcije. Ukoliko ih obrišete iz Community Builder administracionog panela, možete ih ponovo napraviti klikom na ovu vezu, ali u protivnom ne treba kliktati na link više puta!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Kliknite ovde <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder korisnički profili');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Podešavanje na „Da“ će aktivirati integraciju sa Community Builder komponentom (www.joomlapolis.com). Svim opcijama Kunena korisničkih profila će rukovati Community Builder i sve veze ka profilima iz foruma će voditi ka Community Builder korisničkim profilima. Ovakvo podešavanje će premostiti podešavanja Clexus PM profila ukoliko su obe postavljene na „Da“. Takođe se pobrinite da su sve neophodne izmene primenjene u Community Builder bazi podataka koristeći opciju ispod.');
DEFINE('_COM_A_AVATAR_SRC', 'Koristi sliku avatara iz');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Ukoliko imate JomSocial, Clexus PM ili Community Builder komponente instalirane, možete podesiti da Kunena koristi slike avatara iz JomSocial, Clexus PM ili Community Builder korisničkih profila. NAPOMENA: Za Community Builder je potrebno imati uključenu opciju za umanjene slike jer forum koristi umanjene slike korisnika, a ne originale.');
DEFINE('_COM_A_KARMA', 'Prikaži karma indikator');
DEFINE('_COM_A_KARMA_DESC', 'Postavite na „Da“ za prikazivanje karme i relevantnih dugmića (pohvala / kritika) ako je uključena korisnička statistika.');
DEFINE('_COM_A_DISEMOTICONS', 'Isključi smajlije');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Postavite na „Da“ ako želite da potpuno isključite grafičke smajlije.');
DEFINE('_COM_C_FBCONFIG', 'Kunena podešavanje');
DEFINE('_COM_C_FBCONFIGDESC', 'Podešavanje svih Kunena mogućnosti');
DEFINE('_COM_C_FORUM', 'Administracija foruma');
DEFINE('_COM_C_FORUMDESC', 'Dodavanje kategorija/foruma i njihovo podešavanje');
DEFINE('_COM_C_USER', 'Administracija korisnika');
DEFINE('_COM_C_USERDESC', 'Osnovna administracija korisnika i korisničkih profila');
DEFINE('_COM_C_FILES', 'Pregled postavljenih datoteka');
DEFINE('_COM_C_FILESDESC', 'Pregled i administracija postavljenih datoteka');
DEFINE('_COM_C_IMAGES', 'Pregled postavljenih slika');
DEFINE('_COM_C_IMAGESDESC', 'Pregled i administracija postavljenih slika');
DEFINE('_COM_C_CSS', 'Uređivanje CSS datoteke');
DEFINE('_COM_C_CSSDESC', 'Doterajte Kunena izgled i ugođaj');
DEFINE('_COM_C_SUPPORT', 'Veb stranica za podršku');
DEFINE('_COM_C_SUPPORTDESC', 'Povežite se na Kunena veb stranicu (novi prozor)');
DEFINE('_COM_C_PRUNETAB', 'Čišćenje foruma');
DEFINE('_COM_C_PRUNETABDESC', 'Uklanjanje starih tema (može se podesiti)');
DEFINE('_COM_C_PRUNEUSERS', 'Čišćenje korisnika'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sinhronizacija Kunena korisničke tabele sa Joomla! korisničkom tabelom'); // <=FB 1.0.3
DEFINE('_COM_C_LOADSAMPLE', 'Učitavanje probnih podataka');
DEFINE('_COM_C_LOADSAMPLEDESC', 'Za lagani početak: učitajte probne podatke u praznu Kunena bazu');
DEFINE('_COM_C_REMOVESAMPLE', 'Uklanjanje probnih podataka');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Uklonite probne podatke iz baze');
DEFINE('_COM_C_LOADMODPOS', 'Učitavanje pozicija modula');
DEFINE('_COM_C_LOADMODPOSDESC', 'Učitavanje pozicija modula za Kunena šablon');
DEFINE('_COM_C_UPGRADEDESC', 'Dođite do poslednje verzije baze podataka nakon nadgradnje');
DEFINE('_COM_C_BACK', 'Nazad na Kunena kontrolnu tablu');
DEFINE('_SHOW_LAST_SINCE', 'Aktivne teme od poslednje posete:');
DEFINE('_POST_SUCCESS_REQUEST2', 'Vaš zahtev je obrađen');
DEFINE('_POST_NO_PUBACCESS3', 'Kliknite ovde za registraciju.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Poruka je uspešno izbrisana.');
DEFINE('_POST_SUCCESS_EDIT', 'Poruka je uspešno uređena.');
DEFINE('_POST_SUCCESS_MOVE', 'Tema je uspešno premeštena.');
DEFINE('_POST_SUCCESS_POST', 'Vaša poruka je uspešno objavljena.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Vaša pretplata je obrađena.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Karma');
DEFINE('_KARMA_SMITE', 'Kritika');
DEFINE('_KARMA_APPLAUD', 'Pohvala');
DEFINE('_KARMA_BACK', 'Za povratak na temu,');
DEFINE('_KARMA_WAIT', 'Možete menjati karmu samo jedne osobe u svakih 6 časova. <br/>Molimo sačekajte toliko pre ponovnog menjanja bilo čije karme.');
DEFINE('_KARMA_SELF_DECREASE', 'Molimo nemojte pokušavati da smanjite svoju karmu!');
DEFINE('_KARMA_SELF_INCREASE', 'Vaša karma je smanjena zbog pokušaja da je sami povećate!');
DEFINE('_KARMA_DECREASED', 'Korisnikova karma je smanjena. Ako se ne budete vraćeni na temu za koji trenutak,');
DEFINE('_KARMA_INCREASED', 'Korisnikova karma je uvećana. Ako se ne budete vraćeni na temu za koji trenutak,');
DEFINE('_COM_A_TEMPLATE', 'Šablon');
DEFINE('_COM_A_TEMPLATE_DESC', 'Odaberite šablon za upotrebu.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Skupovi slika');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Odaberite šablon skupa slika za upotrebu.');
DEFINE('_PREVIEW_CLOSE', 'Zatvori ovaj prozor');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Koristi statističku traku poruka');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Postavite na „Da“ ako želite da prikažete broj poruka određenog korisnika u grafičkom obliku na statističkoj traci.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Broj boje za statističku traku');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Upišite broj boje koju želite koristiti za statističku traku poruka');
DEFINE('_LATEST_REDIRECT',
    'Kunena mora (ponovo) uspostaviti privilegije pristupa pre pravljenja spiska najnovijih poruka.\nNe brinite, ovo je potpuno normalno nakon više od 30 minuta neaktivnosti ili nakon ponovne prijave.\nMolimo, ponovite svoj zahtev.');
DEFINE('_SMILE_COLOUR', 'Boja');
DEFINE('_SMILE_SIZE', 'Veličina');
DEFINE('_COLOUR_DEFAULT', 'Uobičajena');
DEFINE('_COLOUR_RED', 'Crvena');
DEFINE('_COLOUR_PURPLE', 'Ljubičasta');
DEFINE('_COLOUR_BLUE', 'Plava');
DEFINE('_COLOUR_GREEN', 'Zelena');
DEFINE('_COLOUR_YELLOW', 'Žuta');
DEFINE('_COLOUR_ORANGE', 'Narandžasta');
DEFINE('_COLOUR_DARKBLUE', 'Tamno plava');
DEFINE('_COLOUR_BROWN', 'Braon');
DEFINE('_COLOUR_GOLD', 'Zlatna');
DEFINE('_COLOUR_SILVER', 'Srebrna');
DEFINE('_SIZE_NORMAL', 'Normalna');
DEFINE('_SIZE_SMALL', 'Mala');
DEFINE('_SIZE_VSMALL', 'Vrlo mala');
DEFINE('_SIZE_BIG', 'Velika');
DEFINE('_SIZE_VBIG', 'Vrlo velika');
DEFINE('_IMAGE_SELECT_FILE', 'Odabir slike koju želite priložiti');
DEFINE('_FILE_SELECT_FILE', 'Odabir datoteke koju želite priložiti');
DEFINE('_FILE_NOT_UPLOADED', 'Vaša datoteka nije poslata. Pokušajte slanje ponovo ili uredite poruku');
DEFINE('_IMAGE_NOT_UPLOADED', 'Vaša slika nije poslata. Pokušajte slanje ponovo ili uredite poruku');
DEFINE('_BBCODE_IMGPH', 'Umetnite [img] oznaku u tekst kako biste dodali sliku');
DEFINE('_BBCODE_FILEPH', ' Umetnite [file] oznaku u tekst kako biste dodali datoteku');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Štiklirajte da bi <b><u>otkazali pretplate</u></b> na sve teme (uključujući i nevidljive radi otklanjanja grešaka)');
DEFINE('_LINK_JS_REMOVED', '<em>Aktivna veza koja sadrži JavaScript je automatski uklonjena</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Izgled i ugođaj');
DEFINE('_COM_A_USERS', 'Korisnička podešavanja');
DEFINE('_COM_A_LENGTHS', 'Razna podešavanja dužina');
DEFINE('_COM_A_SUBJECTLENGTH', 'Maks. dužina naslova');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maksimalna dužina linije naslova. Baza podržava maksimalan broj od 255 znakova. Ako je sajt podešen za upotrebu višebajtovnih znakova poput Unicode, UTF-8, ne-ISO-8599-x postavite manji maksimum po formuli:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Primer za UTF-8, za koji je maks. veličina znaka u bajtovima po znaku 4 bajta: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Tema/Forum');
DEFINE('_LATEST_NUMBER', 'Novih poruka');
DEFINE('_COM_A_SHOWNEW', 'Prikaži nove poruke');
DEFINE('_COM_A_SHOWNEW_DESC', 'Ako je postavljeno na „Da“ Kunena pokazuje korisnicima indikator uz forume koji sadrže nove poruke i koje su poruke nove od njihove poslednje posete.');
DEFINE('_COM_A_NEWCHAR', 'Indikator za „Nove“');
DEFINE('_COM_A_NEWCHAR_DESC', 'Ovde definišete šta želite koristiti kao indikator novih poruka (kao „!“ ili „Novo!“)');
DEFINE('_LATEST_AUTHOR', 'Autor poslednje poruke');
DEFINE('_GEN_FORUM_NEWPOST', 'Nove poruke');
DEFINE('_GEN_FORUM_NOTNEW', 'Nema novih poruka');
DEFINE('_GEN_UNREAD', 'Nepročitana tema');
DEFINE('_GEN_NOUNREAD', 'Pročitana tema');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Označi sve forume kao pročitane');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Označi ovaj forum kao pročitan');
DEFINE('_GEN_FORUM_MARKED', 'Sve poruke u ovom forumu su označene kao pročitane');
DEFINE('_GEN_ALL_MARKED', 'Sve poruke su označene kao pročitane');
DEFINE('_IMAGE_UPLOAD', 'Postavljanje slike');
DEFINE('_IMAGE_DIMENSIONS', 'Vaša slika može biti maksimalno (širina x visina - veličina)');
DEFINE('_IMAGE_ERROR_TYPE', 'Koristite samo jpeg, gif ili png formate slika');
DEFINE('_IMAGE_ERROR_EMPTY', 'Izaberite datoteku pre postavljanja');
DEFINE('_IMAGE_ERROR_SIZE', 'Veličina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_IMAGE_ERROR_WIDTH', 'Širina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'Visina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_IMAGE_UPLOADED', 'Vaša slika je postavljena.');
DEFINE('_COM_A_IMAGE', 'Slike');
DEFINE('_COM_A_IMGHEIGHT', 'Maks. visina slike');
DEFINE('_COM_A_IMGWIDTH', 'Maks. širina slike');
DEFINE('_COM_A_IMGSIZE', 'Maks. veličina slike<br/><em>u kilobajtima</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Dozvoli javno postavljanje slika');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Postavite na „Da“ ako želite da svi (javnost), mogu postavljati slike.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Dozvoli registrovano postavljanje slika');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Postavite na „Da“ ako želite da registrovani i prijavljeni korisnici mogu postavljati slike.<br/>Napomena: (Super)administratori i moderatori mogu uvek da postavljaju slike.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Slanje datoteka');
DEFINE('_FILE_TYPES', 'Datoteka može biti vrsta - maks. veličina');
DEFINE('_FILE_ERROR_TYPE', 'Postavljanje je dozvoljeno samo za datoteke vrste:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Izaberite datoteku pre postavljanja');
DEFINE('_FILE_ERROR_SIZE', 'Veličina datoteke prelazi maksimum koji je postavio administrator.');
DEFINE('_COM_A_FILE', 'Datoteke');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Dozvoljene vrste datoteka');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Navedite vrste datoteka koje su dozvoljene za postavljanje. Stavke u spisku razdvojite zarezom i koristite samo <strong>mala slova</strong> bez razmaka.<br />Primer: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Mak. veličina datoteke<br/><em>u kilobajtima</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Dozvoli javno postavljanje datoteka');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Postavite na „Da“ ako želite da svi (javnost) mogu postavljati datoteke.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Dozvoli registrovano postavljanje datoteka');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Postavite na „Da“ ako želite da registrovani i prijavljeni korisnici mogu postavljati datoteke.<br/>Napomena: (Super)administratori i moderatori mogu uvek da postavljaju datoteke.');
DEFINE('_SUBMIT_CANCEL', 'Slanje poruke je otkazano');
DEFINE('_HELP_SUBMIT', 'Kliknite ovde za slanje poruke');
DEFINE('_HELP_PREVIEW', 'Kliknite ovde da vidite kako će izgledati poruka kada je pošaljete');
DEFINE('_HELP_CANCEL', 'Kliknite ovde za otkazivanje poruke');
DEFINE('_POST_DELETE_ATT', 'Ako je kućica štiklirana, svi prilozi slika i datoteka uz poruku koju brišete će takođe biti izbrisani (preporučljivo).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Prikaži oznake izmena');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Postavite na „Da“ ako želite da izmenjena poruka bude označena tekstom koji pokazuje ko je i kada izvršio izmene.');
DEFINE('_EDIT_BY', 'Poruku izmenio/la:');
DEFINE('_EDIT_AT', ' ');
DEFINE('_UPLOAD_ERROR_GENERAL', 'Greška pri slanju avatara. Pokušajte ponovo ili obavestite administratora sistema');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Pregled poslatih slika');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Pregled poslatih datoteka');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Broj poslatih slika');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Broj poslatih datoteka');
DEFINE('_COM_A_IMGB_ENLARGE', 'Kliknite na sliku da bi je videli u punoj veličini');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Kliknite na sliku datoteke da bi je preuzeli');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'Opcija „Zameni neutralnom slikom“ će zameniti odabranu sliku neutralnom.<br /> Ovo vam omogućava uklanjanje stvarne datoteke bez oštećenja poruka.<br /><small><em>Primetićete da ponekad izmena nije vidljiva dok ne osvežite ovu stranicu u čitaču.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Trenutna neutralna slika');
DEFINE('_COM_A_IMGB_REPLACE', 'Zameni neutralnom slikom');
DEFINE('_COM_A_IMGB_REMOVE', 'Potpuno uklanjanje');
DEFINE('_COM_A_IMGB_NAME', 'Naziv');
DEFINE('_COM_A_IMGB_SIZE', 'Veličina');
DEFINE('_COM_A_IMGB_DIMS', 'Dimenzije');
DEFINE('_COM_A_IMGB_CONFIRM', 'Jeste li potpuno sigurni da želite izbrisati ovu datoteku? \n Brisanje datoteke može proizvesti osakaćenu referentnu poruku...');
DEFINE('_COM_A_IMGB_VIEW', 'Otvori poruku (za uređivanje)');
DEFINE('_COM_A_IMGB_NO_POST', 'Nema referentne poruke!');
DEFINE('_USER_CHANGE_VIEW', 'Promene ovih postavki će preći u dejstvo kada ponovo posetite forum.<br /> Ako želite da promenite vrstu prikaza „u letu“ možete upotrebiti opcije iz trake menija foruma.');
DEFINE('_MOSBOT_DISCUSS_A', 'Diskutujte ovaj članak u forumu. (');
DEFINE('_MOSBOT_DISCUSS_B', ' poruka)');
DEFINE('_POST_DISCUSS', 'Ovo je diskusija o sadržaju članka');
DEFINE('_COM_A_RSS', 'Uključi RSS izvor');
DEFINE('_COM_A_RSS_DESC', 'RSS izvor omogućava korisnicima primanje najnovijih poruka na svoju radnu površ ili kroz RSS čitač (pogledajte <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> za primer).');
DEFINE('_LISTCAT_RSS', 'primite najnovije poruke direktno na radnu površ');
DEFINE('_SEARCH_REDIRECT', 'Kunena mora ponovo uspostaviti privilegije pristupa pre ispunjavanja zahteva za pretragu.\nNe brinite, ovo je potpuno normalno nakon više od 30 minuta neaktivnosti.\nPodnesite zahtev za pretragu ponovo.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunena podešavanja');
DEFINE('_COM_A_DISPLAY', 'Prikaži #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Tekuća podešavanja');
DEFINE('_COM_A_EXPLANATION', 'Objašnjenje');
DEFINE('_COM_A_BOARD_TITLE', 'Naziv foruma');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Ime za forum');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Podrazumevana vrsta prikaza');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Birajte između razgranatog i ravnog podrazumevanog prikaza');
DEFINE('_COM_A_THREADS', 'Tema po stranici');
DEFINE('_COM_A_THREADS_DESC', 'Broj tema koji će biti prikazan na svakoj stranici');
DEFINE('_COM_A_REGISTERED_ONLY', 'Samo za registrovane korisnike');
DEFINE('_COM_A_REG_ONLY_DESC', 'Postavite na „Da“ ako želite da dozvolite korišćenje foruma samo registrovanim korisnicima (čitanje i pisanje), postavite na „Ne“ ako želite da svi posetioci mogu da koristite forum');
DEFINE('_COM_A_PUBWRITE', 'Javno čitanje/pisanje');
DEFINE('_COM_A_PUBWRITE_DESC', 'Postavite na „Da“ ako želite da omogućite javne privilegije pisanja, postavite na „Ne“ ako želite da dozvolite svim posetiocima da vide poruke, a samo registrovanim korisnicima njihovo pisanje');
DEFINE('_COM_A_USER_EDIT', 'Korisničke izmene');
DEFINE('_COM_A_USER_EDIT_DESC', 'Postavite na „Da“ ako želite da dozvolite registrovanim korisnicima da vrše izmene svojih poruka.');
DEFINE('_COM_A_MESSAGE', 'Da biste sačuvali izmene gornjih vrednosti pritisnite dugme „Sačuvaj“ na vrhu.');
DEFINE('_COM_A_HISTORY', 'Prikaži istoriju');
DEFINE('_COM_A_HISTORY_DESC', 'Postavite na „Da“ ako želite da prikažete istoriju teme pri pisanju odgovora');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Dozvoli pretplate');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Postavite na „Da“ ako želite da dozvolite registrovanim korisnicima da se pretplate na temu i primaju obaveštenja o novim porukama e-poštom');
DEFINE('_COM_A_HISTLIM', 'Ograničenje istorije');
DEFINE('_COM_A_HISTLIM_DESC', 'Koliko poruka treba da bude prikazano u istoriji');
DEFINE('_COM_A_FLOOD', 'Zaštita od preplavljivanja');
DEFINE('_COM_A_FLOOD_DESC', 'Koliko sekundi korisnik mora da čeka između dve uzastopne poruke. Postavite na 0 (nula) za isključivanje zaštite od preplavljivanja. NAPOMENA: Zaštita od preplavljivanja <em>može</em> izazvati opadanje performansi.');
DEFINE('_COM_A_MODERATION', 'Obaveštenja za moderatore');
DEFINE('_COM_A_MODERATION_DESC',
    'Postavite na „Da“ ako želite da moderatori primaju e-poštom obaveštenje o novim porukama u forumima. Napomena: iako svaki (super)administrator automatski ima i sve moderatorske privilegije, izričito ih odredite kao moderatore
 foruma kako bi i oni primali e-poštu!');
DEFINE('_COM_A_SHOWMAIL', 'Prikaz adrese e-pošte');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Postavite na „Da“ ako ne želite da ikada bude prikazana korisnikova adresa e-pošte; čak ni registrovanim korisnicima.');
DEFINE('_COM_A_AVATAR', 'Dozvoli avatare');
DEFINE('_COM_A_AVATAR_DESC', 'Postavite na „Da“ ako želite da registrovani korisnici imaju avatar (mogu ih podešavati preko svog profila)');
DEFINE('_COM_A_AVHEIGHT', 'Maks. visina avatara');
DEFINE('_COM_A_AVWIDTH', 'Maks. širina avatara');
DEFINE('_COM_A_AVSIZE', 'Maks. veličina avatara<br/><em>u kilobajtima</em>');
DEFINE('_COM_A_USERSTATS', 'Prikaži korisničku statistiku');
DEFINE('_COM_A_USERSTATS_DESC', 'Postavite na „Da“ ako želite da korisnička statistika bude prikazana, kao broj poruka, vrsta korisnika (Admin, Moderator, Korisnik, itd.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Postavljanje avatara');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Postavite na „Da“ ako želite da registrovani korisnici mogu da postavljaju avatar na server.');
DEFINE('_COM_A_AVATARGALLERY', 'Korišćenje galerije avatara');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Postavite na „Da“ ako želite da registrovani korisnici mogu izabrati avatar iz galerije koju pružite (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Postavite na „Da“ ako želite da rang registrovanih korisnika bude prikazan na osnovu broja njihovih poruka.<br/><strong>Morate takođe uključiti Korisničku statistiku na Naprednom jezičku da bi ovo bilo prikazano.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Koristi slike rangiranja');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Postavite na „Da“ ako želite da prikažete rang registrovanih korisnika slikom (iz components/com_kunena/ranks). Ako je ovo isključeno, biće prikazan tekst ranga. Proverite dokumentaciju na www.kunena.com za više podataka o slikama rangiranja');

//email and stuff
$_COM_A_NOTIFICATION = "Obaveštenje o novoj poruci od ";
$_COM_A_NOTIFICATION1 = "Nova poruka je poslata na temu koju pratite na ";
$_COM_A_NOTIFICATION2 = "Teme koje pratite možete administrirati kroz prateći vezu „moj profil“ na početnoj stranici foruma nakon prijavljivanja. Iz profila takođe možete otkazati pretplatu na temu.";
$_COM_A_NOTIFICATION3 = "Ne odgovarajte na ovo obaveštenje jer je automatski poslata e-pošta.";
$_COM_A_NOT_MOD1 = "Nova poruka je poslata u forum koji vi moderirate na ";
$_COM_A_NOT_MOD2 = "Pogledajte ga kad se sledeći put prijavite.";
DEFINE('_COM_A_NO', 'Ne');
DEFINE('_COM_A_YES', 'Da');
DEFINE('_COM_A_FLAT', 'Ravno');
DEFINE('_COM_A_THREADED', 'Razgranato');
DEFINE('_COM_A_MESSAGES', 'Poruka po stranici');
DEFINE('_COM_A_MESSAGES_DESC', 'Broj poruka prikazanih na stranici');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Korisničko ime');
DEFINE('_COM_A_USERNAME_DESC', 'Postavite na „Da“ ako želite da korisničko (prijavno) ime bude upotrebljeno umesto korisnikovog pravog imena.');
DEFINE('_COM_A_CHANGENAME', 'Dozvoli promenu imena');
DEFINE('_COM_A_CHANGENAME_DESC', 'Postavite na „Da“ ako želite da registrovani korisnici mogu menjati svoje ime kada pišu poruku. Ako postavite na „Ne“ registrovani korisnici neće moći da menjaju svoje ime.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum je neaktivan');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Postavite na „Da“ ako želite da učinite forum privremeno neaktivnim. Forum će i dalje biti dostupan za pregled (super)administratorima.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Poruka neaktivnog foruma');
DEFINE('_COM_A_PRUNE', 'Pročišćavanje foruma');
DEFINE('_COM_A_PRUNE_NAME', 'Forum za pročišćavanje:');
DEFINE('_COM_A_PRUNE_DESC',
    'Funkcija Pročišćavanja foruma omogućava jednostavno brisanje tema u kojima nije bilo novih poruka određeni broj dana. Ovo ne uklanja usidrene i zaključane teme; njih morate ukloniti ručno. Teme u zaključanim forumima se ne mogu pročistiti.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Pročisti teme u kojima nije bilo poruka u zadnjih ');
DEFINE('_COM_A_PRUNE_DAYS', 'dana');
DEFINE('_COM_A_PRUNE_USERS', 'Pročišćavanje korisnika'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'Ova funkcija omogućava usklađivanje spiska Kunena korisnika i spiska korisnika Joomla! veb mesta. Biće uklonjeni svi profili Kunena korisnika, koji više ne postoje u Joomla! sistemu.<br/>Kada budete sigurni da želite da nastavite sa čišćenjem, kliknite na „Započni čišćenje“ u traci menija iznad.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Akcija');
DEFINE('_GEN_AUTHOR', 'Autor');
DEFINE('_GEN_BY', 'od');
DEFINE('_GEN_CANCEL', 'Odustani');
DEFINE('_GEN_CONTINUE', 'Pošalji');
DEFINE('_GEN_DATE', 'Datum');
DEFINE('_GEN_DELETE', 'Obriši');
DEFINE('_GEN_EDIT', 'Uredi');
DEFINE('_GEN_EMAIL', 'E-pošta');
DEFINE('_GEN_EMOTICONS', 'Smajliji');
DEFINE('_GEN_FLAT', 'Ravno');
DEFINE('_GEN_FLAT_VIEW', 'Ravno');
DEFINE('_GEN_FORUMLIST', 'Spisak foruma');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Pomoć');
DEFINE('_GEN_HITS', 'Poseta');
DEFINE('_GEN_LAST_POST', 'Poslednja poruka');
DEFINE('_GEN_LATEST_POSTS', 'Prikaži poslednje poruke');
DEFINE('_GEN_LOCK', 'Zaključaj');
DEFINE('_GEN_UNLOCK', 'Otključaj');
DEFINE('_GEN_LOCKED_FORUM', 'Forum je zaključan');
DEFINE('_GEN_LOCKED_TOPIC', 'Tema je zaključana');
DEFINE('_GEN_MESSAGE', 'Poruka');
DEFINE('_GEN_MODERATED', 'Forum je moderiran; Pregled pre objavljivanja.');
DEFINE('_GEN_MODERATORS', 'Moderatori');
DEFINE('_GEN_MOVE', 'Premesti');
DEFINE('_GEN_NAME', 'Ime');
DEFINE('_GEN_POST_NEW_TOPIC', 'Objavi novu temu');
DEFINE('_GEN_POST_REPLY', 'Pošalji odgovor');
DEFINE('_GEN_MYPROFILE', 'Moj profil');
DEFINE('_GEN_QUOTE', 'Citat');
DEFINE('_GEN_REPLY', 'Odgovor');
DEFINE('_GEN_REPLIES', 'Odgovora');
DEFINE('_GEN_THREADED', 'Razgranato');
DEFINE('_GEN_THREADED_VIEW', 'Razgranato');
DEFINE('_GEN_SIGNATURE', 'Potpis');
DEFINE('_GEN_ISSTICKY', 'Tema je usidrena.');
DEFINE('_GEN_STICKY', 'Usidri');
DEFINE('_GEN_UNSTICKY', 'Odsidri');
DEFINE('_GEN_SUBJECT', 'Naslov');
DEFINE('_GEN_SUBMIT', 'Pošalji');
DEFINE('_GEN_TOPIC', 'Tema');
DEFINE('_GEN_TOPICS', 'Teme');
DEFINE('_GEN_TOPIC_ICON', 'ikona teme');
DEFINE('_GEN_SEARCH_BOX', 'Pretraži forum');
$_GEN_THREADED_VIEW = "Razgranato";
$_GEN_FLAT_VIEW = "Ravno";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Postavi');
DEFINE('_UPLOAD_DIMENSIONS', 'Vaša slika može biti maksimalno (širina x visina - veličina)');
DEFINE('_UPLOAD_SUBMIT', 'Izaberite novi avatar za postavljanje');
DEFINE('_UPLOAD_SELECT_FILE', 'Odabir datoteke');
DEFINE('_UPLOAD_ERROR_TYPE', 'Koristite samo jpeg, gif ili png format slike');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Izaberite datoteku pre postavljanja');
DEFINE('_UPLOAD_ERROR_NAME', 'Ime datoteke slike može sadržati samo alfanumeričke znake bez razmaka.');
DEFINE('_UPLOAD_ERROR_SIZE', 'Veličina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'Širina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Visina slike prelazi maksimum koji je postavio administrator.');
DEFINE('_UPLOAD_ERROR_CHOOSE', 'Niste izabrali avatar iz galerije...');
DEFINE('_UPLOAD_UPLOADED', 'Vaš avatar je postavljen na server.');
DEFINE('_UPLOAD_GALLERY', 'Izaberite jedan od avatara iz galerije:');
DEFINE('_UPLOAD_CHOOSE', 'Potvrdi izbor');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Administrator ih mora prvo napraviti iz ');
DEFINE('_LISTCAT_DO', 'Oni će znati šta treba učiniti ');
DEFINE('_LISTCAT_INFORM', 'Obavestite ih i recite im da požure!');
DEFINE('_LISTCAT_NO_CATS', 'Još nema definisanih kategorija na forumu.');
DEFINE('_LISTCAT_PANEL', 'Administracioni panel za Joomla! OS CMS.');
DEFINE('_LISTCAT_PENDING', 'poruke na čekanju');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Nema poruka na čekanju u ovom forumu.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Pripremate se da izbrišete poruku');
DEFINE('_POST_ABOUT_DELETE', '<strong>NAPOMENA:</strong><br/>
- Ako izbrišete temu u forumu (prvu poruku u niti) biće takođe izbrisani i svi odgovori!
Ako želite da uklonite samo sadržaj razmotrite pražnjenje poruke i imena autora.
<br/>
- Ako izbrišete neku od običnih poruka ostali odgovori će biti pomereni na gore za jedno mesto u hijerarhiji.');
DEFINE('_POST_CLICK', 'kliknite ovde');
DEFINE('_POST_ERROR', 'Nije pronađeno korisničko ime/adresa e-pošte. Značajna greška u bazi podataka nije zapisana');
DEFINE('_POST_ERROR_MESSAGE', 'Došlo je do nepoznate SQL greške i vaša poruka nije poslata.  Ako se greška ponavlja kontaktirajte administratora.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Došlo je do greške i vaša poruka nije ažurirana.  Pokušajte ponovo.  Ako se greška ponavlja kontaktirajte administratora.');
DEFINE('_POST_ERROR_TOPIC', 'Greška prilikom brisanja. Proverite detalje o grešci ispod:');
DEFINE('_POST_FORGOT_NAME', 'Zaboravili ste da unesete vaše ime.  Kliknite dugme za nazad u vašem veb čitaču i pokušajte ponovo.');
DEFINE('_POST_FORGOT_SUBJECT', 'Zaboravili ste da unesete naslov.  Kliknite dugme za nazad u vašem veb čitaču i pokušajte ponovo.');
DEFINE('_POST_FORGOT_MESSAGE', 'Zaboravili ste da unesete poruku.  Kliknite dugme za nazad u vašem veb čitaču i pokušajte ponovo.');
DEFINE('_POST_INVALID', 'Zatražen je neispravan ID poruke.');
DEFINE('_POST_LOCK_SET', 'Tema je zaključana.');
DEFINE('_POST_LOCK_NOT_SET', 'Tema nije zaključana.');
DEFINE('_POST_LOCK_UNSET', 'Tema je otključana.');
DEFINE('_POST_LOCK_NOT_UNSET', 'Tema nije otključana.');
DEFINE('_POST_MESSAGE', 'Slanje nove poruke u ');
DEFINE('_POST_MOVE_TOPIC', 'Premeštanje ove teme u forum ');
DEFINE('_POST_NEW', 'Slanje nove poruke u: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Vaša pretplata za ovu temu nije izvršena.');
DEFINE('_POST_NOTIFIED', 'Štiklirajte ovu kućicu ako želite da primate obaveštenje o odgovorima na ovu temu.');
DEFINE('_POST_STICKY_SET', 'Tema je usidrena.');
DEFINE('_POST_STICKY_NOT_SET', 'Tema nije usidrena.');
DEFINE('_POST_STICKY_UNSET', 'Tema je odsidrena.');
DEFINE('_POST_STICKY_NOT_UNSET', 'Tema nije odsidrena.');
DEFINE('_POST_SUBSCRIBE', 'pretplata');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Pretplaćeni ste na ovu temu.');
DEFINE('_POST_SUCCESS', 'Vaša poruka je uspešno');
DEFINE('_POST_SUCCES_REVIEW', 'Vaša poruka je uspešno poslata.  Moderator će je pregledati pre objave na forumu.');
DEFINE('_POST_SUCCESS_REQUEST', 'Vaš zahtev je obrađen.  Ako ne budete vraćeni na temu za nekoliko trenutaka,');
DEFINE('_POST_TOPIC_HISTORY', 'Istorija teme');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Maks. prikazivanje poslednjih');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'poruka  -  <i>(Poslednja poruka na vrhu)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Vaša tema NIJE premeštena. Za povratak na temu:');
DEFINE('_POST_TOPIC_FLOOD1', 'Administrator ovog foruma je uključio zaštitu od preplavljivanja, što znači da morate pričekati ');
DEFINE('_POST_TOPIC_FLOOD2', ' sekundi između slanja novih poruka.');
DEFINE('_POST_TOPIC_FLOOD3', 'Za povratak na forum kliknite dugme za nazad u vašem veb čitaču.');
DEFINE('_POST_EMAIL_NEVER', ' vaša adresa e-pošte nikada neće biti prikazana na sajtu.');
DEFINE('_POST_EMAIL_REGISTERED', 'vaša adresa e-pošte će biti dostupna samo registrovanim korisnicima.');
DEFINE('_POST_LOCKED', ' zaključao administrator.');
DEFINE('_POST_NO_NEW', 'Novi odgovori nisu dozvoljeni.');
DEFINE('_POST_NO_PUBACCESS1', 'Administrator je onemogućio javni pristup pisanja.');
DEFINE('_POST_NO_PUBACCESS2', 'Samo registrovani / prijavljeni korisnici<br /> mogu učestvovati u forumu.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Još nema tema u ovom forumu <<');
DEFINE('_SHOWCAT_PENDING', 'poruke na čekanju');
// userprofile.php
DEFINE('_USER_DELETE', ' štiklirajte za brisanje vašeg potpisa');
DEFINE('_USER_ERROR_A', 'Na ovu stranicu ste stigli greškom. Obavestite administratora na koje veze ');
DEFINE('_USER_ERROR_B', 'ste kliknuli da bi stigli ovde. Ona ili on onda mogu podneti izveštaj o grešci.');
DEFINE('_USER_ERROR_C', 'Hvala!');
DEFINE('_USER_ERROR_D', 'Broj greške koji treba uključiti u izveštaj: ');
DEFINE('_USER_GENERAL', 'Opšte opcije profila');
DEFINE('_USER_MODERATOR', 'Vi ste moderator ovih foruma');
DEFINE('_USER_MODERATOR_NONE', 'Nema foruma dodeljenih vama');
DEFINE('_USER_MODERATOR_ADMIN', 'Administratori su moderatori svih foruma.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Nemate nikakvih pretplata');
//DEFINE('_USER_PREFERED', 'Željeni prikaz');
DEFINE('_USER_PROFILE', 'Profil za ');
DEFINE('_USER_PROFILE_NOT_A', 'Vaš profil ');
DEFINE('_USER_PROFILE_NOT_B', 'nije');
DEFINE('_USER_PROFILE_NOT_C', ' ažuriran.');
DEFINE('_USER_PROFILE_UPDATED', 'Vaš profil je ažuriran.');
DEFINE('_USER_RETURN_A', 'Ako ne budete vraćeni na profil za nekoliko trenutaka ');
DEFINE('_USER_RETURN_B', 'kliknite ovde');
DEFINE('_USER_SUBSCRIPTIONS', 'Vaše pretplate');
DEFINE('_USER_UNSUBSCRIBE', 'Otkaži pretplatu');
DEFINE('_USER_UNSUBSCRIBE_A', 'Vaša pretplata na ovu temu ');
DEFINE('_USER_UNSUBSCRIBE_B', 'nije');
DEFINE('_USER_UNSUBSCRIBE_C', ' otkazana.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Vaša pretplata na ovu temu je otkazana.');
DEFINE('_USER_DELETEAV', ' štiklirajte za brisanje vašeg avatara');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Redosled');
DEFINE('_USER_ORDER_DESC', 'Poslednja poruka na vrhu');
DEFINE('_USER_ORDER_ASC', 'Prva poruka na vrhu');
// view.php
DEFINE('_VIEW_DISABLED', 'Administrator je onemogućio javni pristup pisanja.');
DEFINE('_VIEW_POSTED', 'Poslao/la');
DEFINE('_VIEW_SUBSCRIBE', ':: Pretplaćivanje na ovu temu ::');
DEFINE('_MODERATION_INVALID_ID', 'Zatražen je neispravan ID poruke.');
DEFINE('_VIEW_NO_POSTS', 'Nema poruka u ovom forumu.');
DEFINE('_VIEW_VISITOR', 'Posetilac');
DEFINE('_VIEW_ADMIN', 'Administrator');
DEFINE('_VIEW_USER', 'Korisnik');
DEFINE('_VIEW_MODERATOR', 'Moderator');
DEFINE('_VIEW_REPLY', 'Odgovor na ovu poruku');
DEFINE('_VIEW_EDIT', 'Uređivanje ove poruke');
DEFINE('_VIEW_QUOTE', 'Citiranje ove poruke u novoj poruci');
DEFINE('_VIEW_DELETE', 'Brisanje ove poruke');
DEFINE('_VIEW_STICKY', 'Sidrenje ove teme');
DEFINE('_VIEW_UNSTICKY', 'Odsidravanje ove teme');
DEFINE('_VIEW_LOCK', 'Zaključavanje ove teme');
DEFINE('_VIEW_UNLOCK', 'Otključavanje ove teme');
DEFINE('_VIEW_MOVE', 'Premeštanje teme u drugi forum');
DEFINE('_VIEW_SUBSCRIBETXT', 'Pretplatite se na ovu temu da bi primali obaveštenja o novim porukama e-poštom.');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Forum');
DEFINE('_POSTS', 'Poruka:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Poruka');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forum je NEAKTIVAN!');
DEFINE('_PAGE', 'Stranica: ');
DEFINE('_NO_POSTS', 'Nema poruka');
DEFINE('_CHARS', 'znakova maks.');
DEFINE('_HTML_YES', 'HTML je isključen');
DEFINE('_YOUR_AVATAR', '<b>Vaš avatar</b>');
DEFINE('_NON_SELECTED', 'Još nije odabran <br />');
DEFINE('_SET_NEW_AVATAR', 'Odabir novog avatara');
DEFINE('_THREAD_UNSUBSCRIBE', 'Otkaži pretplatu');
DEFINE('_SHOW_LAST_POSTS', 'Teme aktivne u zadnjih');
DEFINE('_SHOW_HOURS', 'časova');
DEFINE('_SHOW_POSTS', 'ukupno: ');
DEFINE('_DESCRIPTION_POSTS', 'Prikazane su najnovije poruke iz aktivnih tema');
DEFINE('_SHOW_4_HOURS', '4 časa');
DEFINE('_SHOW_8_HOURS', '8 časova');
DEFINE('_SHOW_12_HOURS', '12 časova');
DEFINE('_SHOW_24_HOURS', '24 časa');
DEFINE('_SHOW_48_HOURS', '48 časova');
DEFINE('_SHOW_WEEK', 'nedelja');
DEFINE('_POSTED_AT', 'Poslato');
DEFINE('_DATETIME', 'd.m.y H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Nema novih poruka u vremenskom intervalu koji ste odabrali.');
DEFINE('_MESSAGE', 'Poruka');
DEFINE('_NO_SMILIE', 'ne');
DEFINE('_FORUM_UNAUTHORIZIED', 'Ovaj forum je otvoren samo za registrovane i prijavljene korisnike.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Ako ste već registrovani, prvo se prijavite.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderiranje');
DEFINE('_MOD_APPROVE', 'Odobri');
DEFINE('_MOD_DELETE', 'Izbriši');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Prikaži najnoviju poruku');
DEFINE('_POST_WROTE', 'napisao/la');
DEFINE('_COM_A_EMAIL', 'Adresa e-pošte foruma');
DEFINE('_COM_A_EMAIL_DESC', 'Ovo je adresa e-pošte foruma. Ovo treba da je važeća adresa e-pošte');
DEFINE('_COM_A_WRAP', 'Rastavi reči duže od');
DEFINE('_COM_A_WRAP_DESC',
    'Unesite maksimalni broj znakova koje jedna reč sme da ima. Ova opcija omogućava da doterate ispis Kunena poruka u sklad sa šablonom.<br/> 70 znakova je verovatno maksimum za šablon fiksne širine, ali možda ćete morati malo eksperimentisati.<br/>URL, bez obzira koliko je dug, neće biti rastavljen.');
DEFINE('_COM_A_SIGNATURE', 'Maks. dužina potpisa');
DEFINE('_COM_A_SIGNATURE_DESC', 'Maksimalni broj znakova dozvoljen u potpisu korisnika.');
DEFINE('_SHOWCAT_NOPENDING', 'Nema poruka na čekanju');
DEFINE('_COM_A_BOARD_OFSET', 'Vremenska razlika foruma');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Neki forumi su nalaze na serverima u vremenskoj zoni različitoj od korisnikove. Podesite razliku u časovima koju Kunena mora koristiti u vreme ostavljanja poruke. Možete koristiti pozitivne i negativne brojeve.');
//New in RC2
DEFINE('_COM_A_BASICS', 'Osnove');
DEFINE('_COM_A_FRONTEND', 'Izgled');
DEFINE('_COM_A_SECURITY', 'Sigurnost');
DEFINE('_COM_A_AVATARS', 'Avatari');
DEFINE('_COM_A_INTEGRATION', 'Integracija');
DEFINE('_COM_A_PMS', 'Omogući privatne poruke');
DEFINE('_COM_A_PMS_DESC',
    'Izaberite odgovarajuću komponentu za privatne poruke, ako ste je instalirali. Clexus PM izbor će takođe omogućiti opcije vezane za ClexusPM korisnički profil (kao što su ICQ, AIM, Yahoo, MSN i veze ka profilu ako ih podržava upotrebljeni Kunena šablon).');
DEFINE('_VIEW_PMS', 'Kliknite ovde za slanje privatne poruke ovom korisniku.');
//new in RC3
DEFINE('_POST_RE', 'Odg: ');
DEFINE('_BBCODE_BOLD', 'Podebljano: [b]tekst[/b]');
DEFINE('_BBCODE_ITALIC', 'Kurziv: [i]tekst[/i]');
DEFINE('_BBCODE_UNDERL', 'Podvučeno: [u]tekst[/u]');
DEFINE('_BBCODE_QUOTE', 'Citat: [quote]tekst[/quote]');
DEFINE('_BBCODE_CODE', 'Kod: [code]kod[/code]');
DEFINE('_BBCODE_ULIST', 'Neuređena lista: [ul] [li]tekst[/li] [/ul] - Napomena: lista mora imati stavke');
DEFINE('_BBCODE_OLIST', 'Uređena lista: [ol] [li]tekst[/li] [/ol] - Napomena: lista mora imati stavke');
DEFINE('_BBCODE_IMAGE', 'Slika: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Veza: [url=http://www.zzz.com/]Ovo je veza[/url]');
DEFINE('_BBCODE_CLOSA', 'Zatvori sve oznake');
DEFINE('_BBCODE_CLOSE', 'Zatvori sve bbCode oznake');
DEFINE('_BBCODE_COLOR', 'Boja: [color=#FF6600]tekst[/color]');
DEFINE('_BBCODE_SIZE', 'Veličina: [size=1]veličina slova[/size] - Napomena: veličine mogu biti od 1 do 5');
DEFINE('_BBCODE_LITEM', 'Stavka: [li] stavka na listi [/li]');
DEFINE('_BBCODE_HINT', 'bbCode pomoć - Napomena: bbCode se može upotrebljavati i na označenom tekstu!');
DEFINE('_COM_A_TAWIDTH', 'Širina tekst polja');
DEFINE('_COM_A_TAWIDTH_DESC', 'Prilagodite širinu tekst polja za poruku da odgovara vašem šablonu. <br/>Paleta sa smajlijima teme će biti prelomljena u drugi red ako je širina <= 420 piksela');
DEFINE('_COM_A_TAHEIGHT', 'Visina tekst polja');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Prilagodite visinu tekst polja za poruku da odgovara vašem šablonu.');
DEFINE('_COM_A_ASK_EMAIL', 'Obavezna e-pošta');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Zahtevanje adrese e-pošte kada korisnici ili posetioci objavljuju poruku. Postavite na „Ne“ ako želite da ova mogućnost bude prvobitno preskočena. Piscima poruka neće biti zahtevana adresa e-pošte.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Upravljanje rangiranjem');
define('_KUNENA_SORTRANKS', 'Poređaj po rangu');

define('_KUNENA_RANKSIMAGE', 'Slika ranga');
define('_KUNENA_RANKS', 'Naziv ranga');
define('_KUNENA_RANKS_SPECIAL', 'Posebno');
define('_KUNENA_RANKSMIN', 'Najmanji broj poruka');
define('_KUNENA_RANKS_ACTION', 'Akcije');
define('_KUNENA_NEW_RANK', 'Novi rang');

?>
