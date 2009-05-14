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
* Croatian Translation: www.cms-joomla.com / Valentino Me&#273;imorec/ info@cms-joomla.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Copying "%src%" to "%dst%"...');
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
DEFINE('_KUNENA_UNIST_SUCCESS', "FireBoard component was successfully uninstalled!");
DEFINE('_KUNENA_PDF_VERSION', 'FireBoard Forum Component version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generated: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'No forums to search in.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Error adding users:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Users syncronized; Deleted:');
DEFINE('_KUNENA_USERSSYNCADD', ', add:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'user profiles.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'No profiles found eligible for syncronizing.');
DEFINE('_KUNENA_SYNC_USERS', 'Syncronize Users');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Sync FireBoard user table with Joomla! user table');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Email Administrators');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Edit Rank');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Hide Email');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Nedjelja');
DEFINE('_KUNENA_DT_LDAY_MON', 'Ponedjeljak');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Utorak');
DEFINE('_KUNENA_DT_LDAY_WED', 'Srijeda');
DEFINE('_KUNENA_DT_LDAY_THU', '&#268;etvrtak');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Petak');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Subota');
DEFINE('_KUNENA_DT_DAY_SUN', 'Ned');
DEFINE('_KUNENA_DT_DAY_MON', 'Pon');
DEFINE('_KUNENA_DT_DAY_TUE', 'Uto');
DEFINE('_KUNENA_DT_DAY_WED', 'Sri');
DEFINE('_KUNENA_DT_DAY_THU', '&#268;et');
DEFINE('_KUNENA_DT_DAY_FRI', 'Pet');
DEFINE('_KUNENA_DT_DAY_SAT', 'Sub');
DEFINE('_KUNENA_DT_LMON_JAN', 'Sije&#269;anj');
DEFINE('_KUNENA_DT_LMON_FEB', 'Velja&#269;a');
DEFINE('_KUNENA_DT_LMON_MAR', 'O&#382;ujak');
DEFINE('_KUNENA_DT_LMON_APR', 'Travanj');
DEFINE('_KUNENA_DT_LMON_MAY', 'Svibanj');
DEFINE('_KUNENA_DT_LMON_JUN', 'Lipanj');
DEFINE('_KUNENA_DT_LMON_JUL', 'Srpanj');
DEFINE('_KUNENA_DT_LMON_AUG', 'Kolovoz');
DEFINE('_KUNENA_DT_LMON_SEP', 'Rujan');
DEFINE('_KUNENA_DT_LMON_OCT', 'Listopad');
DEFINE('_KUNENA_DT_LMON_NOV', 'Studeni');
DEFINE('_KUNENA_DT_LMON_DEV', 'Prosinac');
DEFINE('_KUNENA_DT_MON_JAN', 'Sij');
DEFINE('_KUNENA_DT_MON_FEB', 'Velj');
DEFINE('_KUNENA_DT_MON_MAR', 'O&#382;u');
DEFINE('_KUNENA_DT_MON_APR', 'Tra');
DEFINE('_KUNENA_DT_MON_MAY', 'Svi');
DEFINE('_KUNENA_DT_MON_JUN', 'Lip');
DEFINE('_KUNENA_DT_MON_JUL', 'Srp');
DEFINE('_KUNENA_DT_MON_AUG', 'Kol');
DEFINE('_KUNENA_DT_MON_SEP', 'Ruj');
DEFINE('_KUNENA_DT_MON_OCT', 'Lis');
DEFINE('_KUNENA_DT_MON_NOV', 'Stu');
DEFINE('_KUNENA_DT_MON_DEV', 'Pro');
DEFINE('_KUNENA_CHILD_BOARD', 'Podtema');
DEFINE('_WHO_ONLINE_GUEST', 'Gostiju');
DEFINE('_WHO_ONLINE_MEMBER', '&#268;lanova');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'none');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Potvrdi za nastavak...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Potvrdi!');
DEFINE('_KUNENA_NO_ACCESS', 'Nemate pristup ovom forumu!');
DEFINE('_KUNENA_TIME_SINCE', 'Prije %time%');
DEFINE('_KUNENA_DATE_YEARS', 'Godina');
DEFINE('_KUNENA_DATE_MONTHS', 'Mjeseci');
DEFINE('_KUNENA_DATE_WEEKS','Tjedana');
DEFINE('_KUNENA_DATE_DAYS', 'Dana');
DEFINE('_KUNENA_DATE_HOURS', 'Sati');
DEFINE('_KUNENA_DATE_MINUTES', 'Minuta');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Are you sure you want to remove the sample data? This action is irreversible.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumheader:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Prikaz Foruma");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix za index, prikazivanje kategorije, pregled postova i dozvoljava druga&#269;iji dizajn na kategorijama u forumu");
DEFINE('_COM_A_USER_EDIT_TIME', 'Rok ze editiranje postova');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Stavite 0 za neograni&#269;eno, jer &#263;e korisnik biti ograni&#269;en vremenski kada mo&#382;e editirati post');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Dopu&#154;teno prekorak&#269;enje vremena');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [sekundi], dozvoljava spremanje promjena nakon 600 sekundi nakon &#154;to edit link nestane');
DEFINE('_KUNENA_HELPPAGE','Prika&#382;i Pomo&#263;');
DEFINE('_KUNENA_HELPPAGE_DESC','Ako je postavljeno na &quot;Da&quot; link &#263;e se pojaviti u zaglavlju foruma sa linkom prema va&#154;oj help stranici.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Prika&#382;i pomo&#263;u unutar Fireboard Foruma');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Ako je postavljeno na &quot;Da&quot; pomo&#263;(help stranica)&#263;e se pokazati unutar fireboard foruma i eksterni link za pomo&#263; ne&#263;e raditi. <b>Napomena:</b> Trebate dodati ID sadr&#382;aja koji &#263;ete koristiti kao Pomo&#263; korisnicima .');
DEFINE('_KUNENA_HELPPAGE_CID','Pomo&#263;(sadr&#382;aj) ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Trebate izabrati <b>"DA"</b> u  "Prika&#382;i pomo&#263; unutar Fireboard forumu" postavci.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Eksterni link za Pomo&#263;');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Ako prikazujtet pomo&#263; kao eksterni link, izaberite <b>"Ne"</b> u  "Prika&#382;i pomo&#263; unutar Fireboard forumu" postavci.');
DEFINE('_KUNENA_RULESPAGE','Prika&#382;i Pravila');
DEFINE('_KUNENA_RULESPAGE_DESC','Ako je postavljeno na &quot;Da&quot; link &#263;e se pojaviti u zaglavlju foruma sa linkom prema va&#154;im pravilima.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Prika&#382;i pravila unutra Fireboard Foruma');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Ako je postavljeno na &quot;Da&quot; Pravila &#263;e se pokazati unutar fireboard foruma i eksterni link za Pravila ne&#263;e raditi. <b>Napomena:</b> Trebate dodati ID sadr&#382;aja koji &#263;ete koristiti kao Pravila ..');
DEFINE('_KUNENA_RULESPAGE_CID','Pravila( sadr&#382;aj) ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Trebtae izabrati <b>"Da"</b> u  "Prika&#382;i pravila unutra Fireboard Foruma" postavci.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Eksterni link za Pravila');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Ako koristite eksterni link izaberite  <b>"Ne"</b> u "Prika&#382;i pravila unutra Fireboard Foruma" postavci.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library nije porna&#273;eno');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD2 Library nije prona&#273;eno');
DEFINE('_KUNENA_GD_INSTALLED','GD slobodna verzija ');
DEFINE('_KUNENA_GD_NO_VERSION','Ne mogu otkriti GD version');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD nije instaliran, mo&#382;ete vi&#154;e informacija dobiti ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Mala visina slike :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Mala &#154;irina slike :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Srednja visina slike :');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','Srednja &#154;irina slike :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Velika visina slike :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Velika &#154;irina slike :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar kvaliteta');
DEFINE('_KUNENA_WELCOME','Dobrodo&#154;li na Fireboard');
DEFINE('_KUNENA_WELCOME_DESC','Hvala na izboru Fireboard-a kao rje&#154;enje za va&#154; forum. Ovaj pregled &#263;e vam omogu&#263;iti kratak uvid u detalje i statistiku va&#154;eg foruma. Linkovi sa lijeve strane slu&#382;e za  administraciju i upravljanje va&#154;im forumom(npr. postavke, korisnici, forumi, datoteke).');
DEFINE('_KUNENA_STATISTIC','Statistika');
DEFINE('_KUNENA_VALUE','Vrijednost');
DEFINE('_GEN_CATEGORY','Kategorija');
DEFINE('_GEN_STARTEDBY','Pokrenuo: ');
DEFINE('_GEN_STATS','stanje');
DEFINE('_STATS_TITLE',' forum - statistika');
DEFINE('_STATS_GEN_STATS','Op&#263;enita statistika');
DEFINE('_STATS_TOTAL_MEMBERS','&#268;lanovi:');
DEFINE('_STATS_TOTAL_REPLIES','Odgovora:');
DEFINE('_STATS_TOTAL_TOPICS','Tema:');
DEFINE('_STATS_TODAY_TOPICS','Tema danas:');
DEFINE('_STATS_TODAY_REPLIES','Odgovora danas:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategorije:');
DEFINE('_STATS_TOTAL_SECTIONS','Sekcije:');
DEFINE('_STATS_LATEST_MEMBER','Zadnji &#269;lanovi:');
DEFINE('_STATS_YESTERDAY_TOPICS','Tema ju&#269;er:');
DEFINE('_STATS_YESTERDAY_REPLIES','Odgovora ju&#269;er:');
DEFINE('_STATS_POPULAR_PROFILE','Popularnih 10 &#269;lanova (Pregleda profila)');
DEFINE('_STATS_TOP_POSTERS','Top posteri');
DEFINE('_STATS_POPULAR_TOPICS','Top popularnih tema');
DEFINE('_COM_A_STATSPAGE','omogu&#263;i stranicu sa statistikom');
DEFINE('_COM_A_STATSPAGE_DESC','Ako je postavljeno ne &quot;Da&quot; link &#263;e se pojaviti u zaglavlju foruma sa linkom prema statistici foruma. <em>Statistika &#263;e biti uvijek dostupna administratorima unato&#269; ovoj opciji!</em>');
DEFINE('_COM_C_FBSTATS','Forum statistika');
DEFINE('_COM_C_FBSTATS_DESC','Forum statistika');
define('_GEN_GENERAL','Op&#263;enito');
define('_PERM_NO_READ','Nemate dopu&#154;tenje za pregled foruma.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smje&#154;ko spremljen');
DEFINE ('_KUNENA_SMILEY_DELETED','Smje&#154;ko izbrisan');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Kod ve&#263; postoji');
DEFINE ('_KUNENA_MISSING_PARAMETER','Parametri nedostaju');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Status ve&#263; postoji');
DEFINE ('_KUNENA_RANK_DELETED','Status izbrisan');
DEFINE ('_KUNENA_RANK_SAVED','Status spremljen');
DEFINE ('_KUNENA_DELETE_SELECTED','Izbri&#154;ite selektirano');
DEFINE ('_KUNENA_MOVE_SELECTED','Premjesti selektirano');
DEFINE ('_KUNENA_REPORT_LOGGED','Logiran');
DEFINE ('_KUNENA_GO','Idi');
DEFINE('_KUNENA_MAILFULL','Uklju&#269;i cijeli tekst u e-mail koji se &#154;alje pretplatnicima');
DEFINE('_KUNENA_MAILFULL_DESC','Ako je izabrano Ne -  pretplatnici &#263;e dobivati samo naslov odgovora');
DEFINE('_KUNENA_HIDETEXT','Logirajte se za pregled izabranog sadr&#382;aja!');
DEFINE('_BBCODE_HIDE','Skriven tekst: [hide]unesite ovdje tekst[/hide] - sakrij dio poruke od gostiju');
DEFINE('_KUNENA_FILEATTACH','Datoteka u prilogu: ');
DEFINE('_KUNENA_FILENAME','Ime datoteke: ');
DEFINE('_KUNENA_FILESIZE','Veli&#269;ina datoteke: ');
DEFINE('_KUNENA_MSG_CODE','Kod: ');
DEFINE('_KUNENA_CAPTCHA_ON','Za&#154;tita od spama');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA sustav Da/Ne');
DEFINE('_KUNENA_CAPDESC','unesite kod ovdje');
DEFINE('_KUNENA_CAPERR','Kod neispravan!');
DEFINE('_KUNENA_COM_A_REPORT', 'Prijavljivanje postova');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Ako &#382;elite da korisnik mo&#382;e prijaviti post, izaberite Da.');
DEFINE('_KUNENA_REPORT_MSG', 'Prijava posta');
DEFINE('_KUNENA_REPORT_REASON', 'Razlog');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Va&#154;a poruka');
DEFINE('_KUNENA_REPORT_SEND', 'Po&#154;alji prijavu');
DEFINE('_KUNENA_REPORT', 'Prijavi moderatoru');
DEFINE('_KUNENA_REPORT_RSENDER', 'Ime po&#154;iljatelja: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Razlog prijave: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Poruka prijave: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Prijavljen post: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Subjekt poruke: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'poruka: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Link posta: ');
DEFINE('_KUNENA_REPORT_INTRO', 'vam je poslao poruku zbog');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Prijava uspje&#154;no poslana!');
DEFINE('_KUNENA_EMOTICONS', 'Emotikoni');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Emotikon');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kod');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Uredi smje&#154;ka');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Uredi emotikone');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Novi emotikon');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Vi&#154;e emotikona');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Zatvori prozor');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Dodatni emotikoni');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Izaberi emotikona');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot podr&#154;ka');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Omogu&#263;i Joomla Mambot podr&#154;ku');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Moj profil stranica');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Dozvoli promjenu korisni&#269;kog imena');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Dozvoli promjenu korisni&#269;kog imena na Moj profil stranici');
DEFINE ('_KUNENA_RECOUNTFORUMS','Prebroji stanje kategorija');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Sve statistike kategorija su prebrojane.');
DEFINE ('_KUNENA_EDITING_REASON','Razlog editiranja');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Zadnje editiranje');
DEFINE ('_KUNENA_BY','od');
DEFINE ('_KUNENA_REASON','Razlog');
DEFINE('_GEN_GOTOBOTTOM', 'Idi na dno');
DEFINE('_GEN_GOTOTOP', 'Idi na vrh');
DEFINE('_STAT_USER_INFO', 'Info o korisniku');
DEFINE('_USER_SHOWEMAIL', 'Prika&#382;i e-mail');
DEFINE('_USER_SHOWONLINE', 'Prika&#382;i online status');
DEFINE('_KUNENA_HIDDEN_USERS', 'Skrivenih korisnika');
DEFINE('_KUNENA_SAVE', 'Spremi');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Default galerija');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Osobne postavke');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Pregled profila');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Moj avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum postavke');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Izgled&Prikaz');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Profil detalji');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Moji postovi');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Moje pretplate');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Moji favoriti');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Privatne poruke');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Dolazna poruke');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nova poruke');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Odlazne poruke');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Sme&#263;e');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Postavke');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakti');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Lista blokiranih');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Dodatne informacije');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Ime i prezime');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Korisni&#269;ko ime');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Email');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Status korisnika');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Datum registracije');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Zadnja aktivnost');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Postovi');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Pregleda profila');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Osobni tekst');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Spol');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Datum ro&#273;enja');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Godina (YYYY) - Mjesec (MM) - Dan (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Lokacija');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Va&#154; ICQ broj.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Va&#154; AOL Instant Messenger nadimak.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Va&#154; Yahoo! Instant Messenger nadimak.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Va&#154; Skype nadimak.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Va&#154; Gtalk nadimak.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Web lokacija');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Website ime');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Primjer: CMS Joomla');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Website URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Primjer: www.cms-joomla.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Vs&#154;a MSN messenger email adresa.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Potpis');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Mu&#154;ko');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Žensko');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Postovi su uspje&#154;no izbrisani');
DEFINE('_KUNENA_DATE_YEAR', 'Godina');
DEFINE('_KUNENA_DATE_MONTH', 'Mjesec');
DEFINE('_KUNENA_DATE_WEEK','Tjedan');
DEFINE('_KUNENA_DATE_DAY', 'Dan');
DEFINE('_KUNENA_DATE_HOUR', 'Sat');
DEFINE('_KUNENA_DATE_MINUTE', 'Minuta');
DEFINE('_KUNENA_IN_FORUM', ' u Forumu: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Please note, although no boardcode and smiley buttons are shown, they are still useable');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum alati');
//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Korisnici');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s ima <b>%d</b> registriranih korisnika');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Unesite pojam za pretragu!');
DEFINE ('_KUNENA_USRL_SEARCH','NA&#273;i korisnika');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Tra&#382;i');
DEFINE ('_KUNENA_USRL_LIST_ALL','Prika&#382;i sve');
DEFINE ('_KUNENA_USRL_NAME','Ime');
DEFINE ('_KUNENA_USRL_USERNAME','Korisni&#269;ko ime');
DEFINE ('_KUNENA_USRL_GROUP','Grupa');
DEFINE ('_KUNENA_USRL_POSTS','Postova');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Pregleda');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Status');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Datum registracije');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Zadnja aktivnost');
DEFINE ('_KUNENA_USRL_NEVER','Nikad');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Slika');
DEFINE ('_KUNENA_USRL_ASC','Uzlazno');
DEFINE ('_KUNENA_USRL_DESC','Silazno');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Prikaz');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugin postavke');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Lista korisnika');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Broj korisnika koji &#263;e se prikazati po stranici');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Broj korisnika po stranici');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Prika&#382;i online status korisnika');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Prika&#382;i avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Prika&#382;i pravo ime');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Prika&#382;i korisni&#269;ko ime');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Prika&#382;i korisni&#269;ku grupu');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Prika&#382;i broj postova');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Prika&#382;i karmu');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Prika&#382;i e-mail');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Prika&#382;i status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Prika&#382;i datum registracije');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Prika&#382;i zadnju aktivnost');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Prika&#382;i broj pregleda profila');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');

// added by Ivo Apostolov: Cleaning up of admin.fireboard.php from hardcoded strings
DEFINE('_KUNENA_DBWIZ', 'Database &#269;arobnjak');
DEFINE('_KUNENA_DBMETHOD', 'Izaberite metodu kojom &#382;elite dovr&#154;iti instalaciju Fireboard foruma:');
DEFINE('_KUNENA_DBCLEAN', '&#268;ista instalacija');
DEFINE('_KUNENA_DBUPGRADE', 'Nadogradnja sa Joomlaboard foruma');
DEFINE('_KUNENA_TOPLEVEL', 'Top level kategorija');
DEFINE('_KUNENA_REGISTERED', 'Registrirani');
DEFINE('_KUNENA_PUBLICBACKEND', 'Public Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Select an item to');
DEFINE('_KUNENA_ERRORSUBS', 'Gre&#154;ka kod brisanja postova i pretplate');
DEFINE('_KUNENA_WARNING', 'Upozorenje...');
DEFINE('_KUNENA_CHMOD1', 'Morate pormijeniti dozvolu (chmod)te datoteke u 766 da bi datoteka bila spremljena.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Va&#154; config file je');
DEFINE('_KUNENA_FIREBOARD', 'Fireboard');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Izaberi predlo&#382;ak');
DEFINE('_KUNENA_CFNW', 'GRE&#138;KA: Config File nije &#269;itljiv(promijenite dozvole)');
DEFINE('_KUNENA_CFS', 'Config datoteka spremljenja');
DEFINE('_KUNENA_CFCNBO', 'GRE&#154;KA: Datoteka se ne mo&#382;e otvoriti.');
DEFINE('_KUNENA_TFINW', 'Datoteka nije &#269;itljiva.');
DEFINE('_KUNENA_FBCFS', 'Fireboard CSS datoteka spremljena.');
DEFINE('_KUNENA_SELECTMODTO', 'Izaberite moderatora za');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Morate izbrati forum za  prune funkciju!');
DEFINE('_KUNENA_DELMSGERROR', 'Brisanje poruke neuspjelo:');
DEFINE('_KUNENA_DELMSGERROR1', 'Brisanje teksta poruke neuspjelo:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Brisanje pretplate neuspjelo:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned za');
DEFINE('_KUNENA_PRUNEDAYS', 'dana');
DEFINE('_KUNENA_PRUNEDELETED', 'Izbrisano:');
DEFINE('_KUNENA_PRUNETHREADS', 'tema');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Gre&#154;ka kod brisanja korisnika:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Korisnici pruned; Izbrisano:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'korisni&#269;kih profila');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Nema stavki da se neki profil obri&#154;e.');
DEFINE('_KUNENA_TABLESUPGRADED', 'Fireboard tablice nadogra&#273;ene na verziju');
// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - SAMPLE DATA
DEFINE('_KUNENA_FORUMCATEGORY', 'Forum Kategorije');
DEFINE('_KUNENA_SAMPLWARN1', '-- Make absolutely sure that you load the sample data on completely empty fireboard tables. If anything is in them, it will not work!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Sample Post 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Sample Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Sample Post[/color][/size][/b]\nCongratulations with your new Forum!\n\n[url=http://bestofjoomla.com]- Best of Joomla[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Sample data loaded');
// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - Other adjustments to the file
DEFINE('_KUNENA_CBADDED', 'Community Builder profil dodan');
DEFINE('_KUNENA_IMGDELETED', 'Slika izbrisana');
DEFINE('_KUNENA_FILEDELETED', 'Datoteka izbrisana');
DEFINE('_KUNENA_NOPARENT', 'Bez roditelja');
DEFINE('_KUNENA_DIRCOPERR', 'Gre&#154;ka: Datoteka');
DEFINE('_KUNENA_DIRCOPERR1', 'se nemo&#382;e kopirati!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Fireboard Forum</strong> Komponenta <em>za Joomla! CMS</em> <br />&copy; 2006 - 2007 by Best Of Joomla<br>All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Instalacija :</code></strong><br /><br /><font color="red"><b>uspje&#154;na');
// added by aliyar 
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Postavke profila');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Ako imate Clexus PM ili Community Builder komponentu instaliranu, mo&#382;ete koristiti profile preko tih komponenti.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Pregled profila</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Sve poruke na forumu');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Tema');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Pokrenuo');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorije');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Datum');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Pregleda');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Nema postova');
DEFINE('_KUNENA_TOTALFAVORITE', 'Favoriti:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Broj pod-kategorija  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Broj pod-kategorija za prikazati u jednom redu ispod glavne kategorije ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Pretplata na post uvijek uklju&#269;ena?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Izaberite &quot;Da&quot; ako &#382;elite da je korisnik automatski pretpla&#263;en na temu u kojoj sudjeluje');
// Added by Ivo Apostolov
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategorija / Forum mora imati ime');
// Forum Configuration (New in Fireboard)
DEFINE('_KUNENA_SHOWSTATS', 'Prika&#382;i statistiku');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Ako &#382;elite prikazati statistiku, izaberite Da');
DEFINE('_KUNENA_SHOWWHOIS', 'Prika&#382;i Tko je online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Ako &#382;elite prikazati Tko je online, izaberite Da');
DEFINE('_KUNENA_STATSGENERAL', 'Prika&#382;i op&#263;enitu statistiku');
DEFINE('_KUNENA_STATSGENERALDESC', 'Ako &#382;elite prikazati Op&#263;enitu statistiku, izaberite Da');
DEFINE('_KUNENA_USERSTATS', 'Prika&#382;i poularne/aktivne korisnike');
DEFINE('_KUNENA_USERSTATSDESC', 'Ako &#382;elite prikazati popularne/aktivne korisnike, izaberite Da');
DEFINE('_KUNENA_USERNUM', 'Broj popularnih korisnika');
DEFINE('_KUNENA_USERPOPULAR', 'Prika&#382;i popularne postove');
DEFINE('_KUNENA_USERPOPULARDESC', 'Ako &#382;elite prikazati popularne postove, izaberite Da');
DEFINE('_KUNENA_NUMPOP', 'Broj popularnih postova');
DEFINE('_KUNENA_INFORMATION',
    'Best of Joomla team is proud to announce the release of Fireboard 1.0.0. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard team and most of our praises goes to their team.Some of the main features of Fireboard can be listed as below (in addition to FB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Best of Joomla! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instrukcije');
DEFINE('_KUNENA_FINFO', 'Fireboard Forum informacije');
DEFINE('_KUNENA_CSSEDITOR', 'Fireboard Template CSS Editor');
DEFINE('_KUNENA_PATH', 'Putanja:');
DEFINE('_KUNENA_CSSERROR', 'Pozor:CSS Template file mora biti Writable(&#269;itljiv) da bi mogli spremiti izmjene.');
// User Management
DEFINE('_KUNENA_FUM', 'Fireboard menad&#382;er korisnika');
DEFINE('_KUNENA_SORTID', 'Poredaj po userid');
DEFINE('_KUNENA_SORTMOD', 'Poredaj prema moderatorima');
DEFINE('_KUNENA_SORTNAME', 'Poredaj prema imenima');
DEFINE('_KUNENA_VIEW', 'Na&#269;in pregleda');
DEFINE('_KUNENA_NOUSERSFOUND', 'Korisnik nije prona&#273;en.');
DEFINE('_KUNENA_ADDMOD', 'Dodaj moderatora za');
DEFINE('_KUNENA_NOMODSAV', 'Nijedan moderator nije prona&#273;en. Pro&#269;itajte \'note\' dolje.');
DEFINE('_KUNENA_NOTEUS',
    'NOTE: Only users which have the moderator flag set in their fireboard Profile are shown here. In order to be able to add a moderator give a user a moderator flag, go to <a href="index2.php?option=com_fireboard&task=profiles">User Administration</a> and search for the user you want to make a moderator. Then select his or her profile and update it. The moderator flag can only be set by an site administrator. ');
DEFINE('_KUNENA_PROFFOR', 'Profil za');
DEFINE('_KUNENA_GENPROF', 'Op&#263;enite postavke profila');
DEFINE('_KUNENA_PREFVIEW', 'Preferirani na&#269;in pregleda:');
DEFINE('_KUNENA_PREFOR', 'Preferirani poredak postova:');
DEFINE('_KUNENA_ISMOD', 'Moderator foruma:');
DEFINE('_KUNENA_ISADM', '<strong>Da</strong> (nepromjenjiva postavka, korisnik je (super)administrator)');
DEFINE('_KUNENA_COLOR', 'Boja');
DEFINE('_KUNENA_UAVATAR', 'Avatar korisnika:');
DEFINE('_KUNENA_NS', 'Nema izabranih');
DEFINE('_KUNENA_DELSIG', ' ozna&#269;ite za brisanje potpisa');
DEFINE('_KUNENA_DELAV', ' ozna&#269;ite za brisanje avatara');
DEFINE('_KUNENA_SUBFOR', 'Pretplate za');
DEFINE('_KUNENA_NOSUBS', 'Korisnik nema preptplata');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Osnovno');
DEFINE('_KUNENA_BASICSFORUM', 'Osnovne Forum informacije');
DEFINE('_KUNENA_PARENT', 'Roditelj:');
DEFINE('_KUNENA_PARENTDESC',
    'Please note: Za kreiranje top kategorije(glavne), izaberite \'bez roditelja\' . Kategorija slu&#382;i za prikaz foruma( forum ima roditelja, a ktegorije nemaju.<br />A Forum mo&#382;e <strong>jedino</strong> kreiran ako se izabere neka roditeljska kategorija<br /> Postovi <strong>nemogu</strong> se postati u glavnim kategorijama, samo u forumima.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Ime i opis foruma');
DEFINE('_KUNENA_NAMEADD', 'Ime:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Opis:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Napredne forum postavke');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Sigurnost i pristup forumu');
DEFINE('_KUNENA_LOCKEDDESC', 'Izaberite  &quot;Da&quot; ako &#382;elite zaklju&#269;ati forum za javan pristup, jedino &#263;e administratori i moderatori imati mogu&#263;nost postanja, editiranja, itd..).');
DEFINE('_KUNENA_LOCKED1', 'Zaklju&#269;ano:');
DEFINE('_KUNENA_PUBACC', 'Javan pristup (svi) :');
DEFINE('_KUNENA_PUBACCDESC',
    'Za kreiranje foruma koji nije javan( ne vide svi) izaberite minimalan nivo pristupa prema grupama korisnika. Default kao minimalan pristup je &quot;Everybody&quot;.<br /><b>Please note</b>: if you restrict access on a whole Category to one or more certain groups, it will hide all Forums it contains to anybody not having proper privileges on the Category <b>even</b> if one or more of these Forums have a lower access level set! This holds for Moderators too; you will have to add a Moderator to the moderator list of the Category if (s)he does not have the proper group level to see the Category.<br /> This is irrespective of the fact that Categories can not be Moderated; Moderators can still be added to the moderator list.');
DEFINE('_KUNENA_CGROUPS', 'Uklju&#269;i pod-kategorije:');
DEFINE('_KUNENA_CGROUPSDESC', 'Želite li da se pod-kategorije prikazuju na glavnoj stranici? Ako izaberete &quot;Ne&quot; pristup tom forumu je dopu&#154;ten samo odre&#273;enoj grupi <b></b>');
DEFINE('_KUNENA_ADMINLEVEL', 'Administratorski pristup:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'If you create a forum with Public Access restrictions, you can specify here an additional Administration Access Level.<br /> If you restrict the access to the forum to a special Public Frontend user group and don\'t specify a Public Backend Group here, administrators will not be able to enter/view the Forum.');
DEFINE('_KUNENA_ADVANCED', 'Napredno');
DEFINE('_KUNENA_CGROUPS1', 'Uklju&#269;i pod-podkategorije:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Želite li da se pod-kategorije prikazuju na glavnoj stranici? Ako izaberete &quot;Ne&quot; pristup tom forumu je dopu&#154;ten samo odre&#273;enoj grupi<b>only</b>');
DEFINE('_KUNENA_REV', 'Moderacija postova:');
DEFINE('_KUNENA_REVDESC',
    'Izaberite  &quot;Da&quot; ako &#382;elite da moderatori prije objavljivanja pregledaju postove, pa objave tek nakon pregleda. This is useful in a Moderated forum only!<br />Ako nemate dodijeljinih moderatora, navedena zada&#263;a se odnosi na administratora\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderacija');
DEFINE('_KUNENA_MODNEWDESC', 'Moderacija foruma i moderatori');
DEFINE('_KUNENA_MOD', 'Moderacija:');
DEFINE('_KUNENA_MODDESC',
    'Izaberite  &quot;Da&quot; ako &#382;elite mogu&#263;nost dodjeljivanja moderatora za taj forum.<br /><strong>Note:</strong> This doesn\'t mean that new post must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>Please do note:</strong> After setting Moderation to &quot;Yes&quot; you must save the forum configuration first before you will be able to add Moderators using the new button.');
DEFINE('_KUNENA_MODHEADER', 'Postavke moderacije za ovaj forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatori dodijelji forumu:');
DEFINE('_KUNENA_NOMODS', 'Nema dodijeljenih moderatora za ovaj forum');
// Some General Strings (Improvement in Fireboard)
DEFINE('_KUNENA_EDIT', 'Editiraj');
DEFINE('_KUNENA_ADD', 'Dodaj');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Pomakni gore');
DEFINE('_KUNENA_MOVEDOWN', 'Pomakni dolje');
// Groups - Integration in Fireboard
DEFINE('_KUNENA_ALLREGISTERED', 'Svi registrirani');
DEFINE('_KUNENA_EVERYBODY', 'Svi');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Reorder');
DEFINE('_KUNENA_CHECKEDOUT', 'Check Out');
DEFINE('_KUNENA_ADMINACCESS', 'Admin pristup');
DEFINE('_KUNENA_PUBLICACCESS', 'Public pristup');
DEFINE('_KUNENA_PUBLISHED', 'Objavljeno');
DEFINE('_KUNENA_REVIEW', 'Moderacija postova');
DEFINE('_KUNENA_MODERATED', 'Moderirano');
DEFINE('_KUNENA_LOCKED', 'Zaklju&#269;ano');
DEFINE('_KUNENA_CATFOR', 'Kategorija / Forum');
DEFINE('_KUNENA_ADMIN', '&nbsp;Fireboard administracija');
DEFINE('_KUNENA_CP', '&nbsp;Fireboard Control Panel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integracija avatara');
DEFINE('_COM_A_RANKS_SETTINGS', 'Status');
DEFINE('_COM_A_RANKING_SETTINGS', 'Postavke statusa');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Postavke avatara');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Sigurnosne postavke');
DEFINE('_COM_A_BASIC_SETTINGS', 'Osnovne postavke');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES','Omogu&#263;iti Favorite');
DEFINE('_COM_A_FAVORITES_DESC','Postavite na "Da" , ako &#382;elite registriranim korisnicima omogu&#263;iti ozna&#269;avanje teme kao favorita ');
DEFINE('_USER_UNFAVORITE_ALL','Skidanje oznake <b><u>favorita</u></b> sa svih tema (uklju&#269;uju&#263;i i onih koje mo&#382;da nisu vidljive)');
DEFINE('_VIEW_FAVORITETXT','Ozna&#269;iti temu kao favorit ');
DEFINE('_USER_UNFAVORITE_YES','Skinuta je oznaka favorita s ove teme');
DEFINE('_POST_FAVORITED_TOPIC','Va&#154; favorit je obra&#273;en.');
DEFINE('_VIEW_UNFAVORITETXT','Favorit');
DEFINE('_VIEW_UNSUBSCRIBETXT','Pretplata');
DEFINE('_USER_NOFAVORITES','Nema Favorita');
DEFINE('_POST_SUCCESS_FAVORITE','Va&#154; favorit je obra&#273;en.');


DEFINE('_COM_A_MESSAGES_SEARCH','Rezultati Pretra&#382;ivanja');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH','Poruka po stranici rezultata pretra&#382;ivanja');

DEFINE('_KUNENA_USE_JOOMLA_STYLE','Koristiti Joomla CSS');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Ako &#382;elite koristiti joomla stilove, postavite ovo na Da. (npr: sectionheader, sectionentry1 ...) ');

DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST','Slika Podkategorije');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC','Ako &#382;elite prikazati malu ikonu uz pod-kategorije na popisu foruma, postavite ovo na Da. ');

DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','Prika&#382;i Obavjesti');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Postavite na "Da" , ako &#382;elite prikazati okvir za obavjesti na naslovnici foruma.');

DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Avatar na Popisu Kategorija');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Postavite na "Da" , ako &#382;elite prikazati korisni&#269;ke avatare na popisu forumskih kategorija.');

DEFINE('_KUNENA_RECENT_POSTS','Postavke Najnovijih Postova');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','Prikazati Najnovije Postove');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','Postavite na "Da" , ako &#382;elite prikazati plugin za najnovije postove na svom forumu');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','Broj Najnovijih Postova');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','Koliko najnovijih postova &#382;elite prikazati.');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Postova po Tabu');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','Koliko najnovijih postova &#382;elite prikazati u jednom tabu');

DEFINE('_KUNENA_LATEST_CATEGORY','Kategorija');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','Odredite kategorije iz kojih &#263;e se prikazivati najnoviji postovi. Primjer:2,3,7 ');

DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Jednostruki Naslov');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','Jednostruki Naslov');

DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Naslov Odgovora');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','Prikazati naslov odgovora (Re:)');

DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','Duljina Naslova');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','Duljina naslova teme na popisu najnovijih postova');

DEFINE('_KUNENA_SHOW_LATEST_DATE','Datum');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC','Prikazati datum');

DEFINE('_KUNENA_SHOW_LATEST_HITS','Broj Posjeta');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC','Prikazati broj posjeta');

DEFINE('_KUNENA_SHOW_AUTHOR','Autor');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC','Prikazati autore postova kao: 1=korisni&#269;ko ime, 2=pravo ime, 0=ni&#154;ta');

DEFINE('_KUNENA_STATS','Postavke Statisti&#269;kog Plugina ');

DEFINE('_KUNENA_CATIMAGEPATH','Slike Kategorija ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','Putanja slika za kategoriju. Ako unesete "category_images/" putanja &#263;e biti "components/com_fireboard/category_images/');

DEFINE('_KUNENA_ANN_MODID','ID Moderatora Obavjesti ');
DEFINE('_KUNENA_ANN_MODID_DESC','ID-i korisnika koji mogu moderirati obavjesti. npr. 62,63,73 . Moderatori obavjesti mogu dodavati, ure&#273;ivati i brisati obavjesti.');


//
DEFINE('_KUNENA_FORUM_TOP','Kategorije ');
DEFINE('_KUNENA_CHILD_BOARDS','Pod-Kategorije ');
DEFINE('_KUNENA_QUICKMSG','Brzi Odgovor ');
DEFINE('_KUNENA_THREADS_IN_FORUM','Tema na forumu ');
DEFINE('_KUNENA_FORUM','Forum ');
DEFINE('_KUNENA_SPOTS','Spotlights');
DEFINE('_KUNENA_CANCEL','odustani');
DEFINE('_KUNENA_TOPIC','TEMA: ');
DEFINE('_KUNENA_POWEREDBY','Powered by ');

// Time Format

DEFINE('_TIME_TODAY','<b>Danas</b> ');
DEFINE('_TIME_YESTERDAY','<b>Ju&#269;er</b> ');


//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS','Zadnji postovi');
DEFINE('_KUNENA_WHO_WHOISONLINE','Tko je online');
DEFINE('_KUNENA_WHO_MAINPAGE','Forum');
DEFINE('_KUNENA_GUEST','Gost');
DEFINE('_KUNENA_PATHWAY_VIEWING','posjeta');
DEFINE('_KUNENA_ATTACH','Privitak');


// Favorite

DEFINE('_KUNENA_FAVORITE','Favorit');
DEFINE('_USER_FAVORITES','Va&#154;i favoriti');
DEFINE('_THREAD_UNFAVORITE','Ukloni iz Favorita');


// profilebox
DEFINE('_PROFILEBOX_WELCOME','Dobrodo&#154;li');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS','Prika&#382;i zadnje postove');
DEFINE('_PROFILEBOX_SET_MYAVATAR','Moj Avatar');
DEFINE('_PROFILEBOX_MYPROFILE','Moj Profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS','Prika&#382;i Moje Postove');
DEFINE('_PROFILEBOX_GUEST','Gost');
DEFINE('_PROFILEBOX_LOGIN','Prijava');
DEFINE('_PROFILEBOX_REGISTER','Registracija');
DEFINE('_PROFILEBOX_LOGOUT','Odjava');
DEFINE('_PROFILEBOX_LOST_PASSWORD','Zaboravljena &#138;ifra?');
DEFINE('_PROFILEBOX_PLEASE','');
DEFINE('_PROFILEBOX_OR',' - ');

// recentposts
DEFINE('_RECENT_RECENT_POSTS','Novi Postovi');
DEFINE('_RECENT_TOPICS','Tema');
DEFINE('_RECENT_AUTHOR','Autor');
DEFINE('_RECENT_CATEGORIES','Kategorije');
DEFINE('_RECENT_DATE','Datum');
DEFINE('_RECENT_HITS','Hitova');


// announcement
DEFINE('_ANN_ANNOUNCEMENTS', 'Obavijesti');
DEFINE('_ANN_ID','ID');
DEFINE('_ANN_DATE','Datum');
DEFINE('_ANN_TITLE','Naslov');
DEFINE('_ANN_SORTTEXT','Kratki Tekst');
DEFINE('_ANN_LONGTEXT','Dugi Tekst');
DEFINE('_ANN_ORDER','Redosljed');
DEFINE('_ANN_PUBLISH','Objavi');
DEFINE('_ANN_PUBLISHED','Objavljeno');
DEFINE('_ANN_UNPUBLISHED','Neobjavljeno');
DEFINE('_ANN_EDIT','Uredi');
DEFINE('_ANN_DELETE','Izbri&#154;i');
DEFINE('_ANN_SUCCESS','Uspjeh');
DEFINE('_ANN_SAVE','Spremi');
DEFINE('_ANN_YES','Da');
DEFINE('_ANN_NO','Ne');
DEFINE('_ANN_ADD','Novo');
DEFINE('_ANN_SUCCESS_EDIT','Uspje&#154;no Ure&#273;eno');
DEFINE('_ANN_SUCCESS_ADD','Uspje&#154;no Dodano');
DEFINE('_ANN_DELETED','Uspje&#154;no Izbrisano');
DEFINE('_ANN_ERROR','GRE&#138;KA');
DEFINE('_ANN_READMORE','Op&#154;irnije...');
DEFINE('_ANN_CPANEL','Obavjesti');
DEFINE('_ANN_SHOWDATE','Prika&#382;i Datum');


// Stats
DEFINE('_STAT_FORUMSTATS','Statistika Foruma');
DEFINE('_STAT_GENERAL_STATS','Op&#263;enita Statistika');
DEFINE('_STAT_TOTAL_USERS','Ukupno Korisnika');
DEFINE('_STAT_LATEST_MEMBERS','Zadnja Registracija');
DEFINE('_STAT_PROFILE_INFO','Profil Info');
DEFINE('_STAT_TOTAL_MESSAGES','Ukupno Poruka');
DEFINE('_STAT_TOTAL_SUBJECTS','Ukupno Tema');
DEFINE('_STAT_TOTAL_CATEGORIES','Ukupno Kategorija');
DEFINE('_STAT_TOTAL_SECTIONS','Ukupno Sekcija');
DEFINE('_STAT_TODAY_OPEN_THREAD','Otvoreno Danas');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD','Otvoreno Ju&#269;er');
DEFINE('_STAT_TODAY_TOTAL_ANSWER','Ukupno Odgovora Danas');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER','Ukupno Odgovora Ju&#269;er');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM','Najnoviji Postovi');
DEFINE('_STAT_MORE_ABOUT_STATS','Vi&#154;e Statistika');
DEFINE('_STAT_USERLIST','Lista Korisnika');
DEFINE('_STAT_TEAMLIST','Board Tim');
DEFINE('_STATS_FORUM_STATS','Statistika Foruma');
DEFINE('_STAT_POPULAR','Popularnih');
DEFINE('_STAT_POPULAR_USER_TMSG','Korisnika (ukupno poruka) ');
DEFINE('_STAT_POPULAR_USER_KGSG','Tema ');
DEFINE('_STAT_POPULAR_USER_GSG','Korisnika (ukupno posjeta profilu) ');


//Team List
DEFINE('_MODLIST_STATUS','Status');
DEFINE('_MODLIST_USERNAME','Korisni&#269;ko ime');
DEFINE('_MODLIST_FORUMS','Forumi');
DEFINE('_MODLIST_ONLINE','Trenutno Online');
DEFINE('_MODLIST_OFFLINE','Korisnik Offline');


// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE','Tko je online');
DEFINE('_WHO_ONLINE_NOW','Online');
DEFINE('_WHO_ONLINE_MEMBERS','&#268;lanova');
DEFINE('_WHO_AND','i');
DEFINE('_WHO_ONLINE_GUESTS','Gostiju');
DEFINE('_WHO_ONLINE_USER','User');
DEFINE('_WHO_ONLINE_TIME','Vrijeme');
DEFINE('_WHO_ONLINE_FUNC','Akcija');


// Userlist
DEFINE ('_USRL_USERLIST','Lista Korisnika');
DEFINE ('_USRL_REGISTERED_USERS','%s ima <b>%d</b> registriranih korisnika');
DEFINE ('_USRL_SEARCH_ALERT','Unesite vrijednost za pretra&#382;ivanje!');
DEFINE ('_USRL_SEARCH','Na&#273;i korisnika');
DEFINE ('_USRL_SEARCH_BUTTON','Tra&#382;i');
DEFINE ('_USRL_LIST_ALL','Lista svih');
DEFINE ('_USRL_NAME','Ime');
DEFINE ('_USRL_USERNAME','Korisni&#269;ko Ime');
DEFINE ('_USRL_EMAIL','E-mail');
DEFINE ('_USRL_USERTYPE','Tip korisniak');
DEFINE ('_USRL_JOIN_DATE','Datum regostracije');
DEFINE ('_USRL_LAST_LOGIN','Zadnja prijava');
DEFINE ('_USRL_NEVER','Nikada');
DEFINE ('_USRL_BLOCK','Status');
DEFINE ('_USRL_MYPMS2','MyPMS');
DEFINE ('_USRL_ASC','Uzlazno');
DEFINE ('_USRL_DESC','Silazno');
DEFINE ('_USRL_DATE_FORMAT','%d.%m.%Y');
DEFINE ('_USRL_TIME_FORMAT','%H:%M');
DEFINE ('_USRL_PARTYSTAFF','Staff');
DEFINE ('_USRL_AKOSTAFF','AkoStaff');
DEFINE ('_USRL_USEREXTENDED','Detalji');
DEFINE ('_USRL_COMPROFILER','Profil');
DEFINE ('_USRL_THUMBNAIL','Slika');
DEFINE ('_USRL_READON','prika&#382;i');
DEFINE('_USRL_MAMBLOG', 'Mamblog');
DEFINE('_USRL_VIEWBLOG','Pregled bloga');
DEFINE('_USRL_NOBLOG','Nema bloga');
DEFINE('_USRL_MYHOMEPAGE','MojaNaslovnica');
DEFINE ('_USRL_MYPMSPRO','Clexus PM');
DEFINE ('_USRL_MYPMSPRO_ADD_BUDDY','Dodati na BuddyList-u');
DEFINE ('_USRL_MYPMSPRO_SENDPM','Po&#154;alji PM');
DEFINE ('_USRL_JIM','PM');
DEFINE ('_USRL_UDDEIM','PM');
DEFINE ('_USRL_SEARCHRESULT','Rezultati pretra&#382;ivanja za');
DEFINE ('_USRL_STATUS','Status');
DEFINE ('_USRL_LISTSETTINGS','Postavke Korisni&#269;ke Liste');
DEFINE ('_USRL_ERROR','Gre&#154;ka');



//removed in 1.1.4 stable
/*
_POST_HTML_ENABLED,_BACK_TO_FORUM,_RESULTS_CATEGORY,_RESULTS_FORUM_NAME,_RESULTS_CONTENT,
_RESULTS_TITLE,_SEARCH_HITS,_SEARCH_RESULTS,_DESCRIPTION_BOLD,_DESCRIPTION_ITALIC,_DESCRIPTION_U,
_DESCRIPTION_QUOTE,_DESCRIPTION_URL,_DESCRIPTION_CODE,_DESCRIPTION_IMAGE,_DESCRIPTION_LIST,_DESCRIPTION_SIZE
_DESCRIPTION_RED,_DESCRIPTION_BLUE,_DESCRIPTION_GREEN,_DESCRIPTION_YELLOW,_DESCRIPTION_ORANGE,
_DESCRIPTION_PURPLE,_DESCRIPTION_NAVY,_DESCRIPTION_DARKGREEN,_DESCRIPTION_AQUA,_DESCRIPTION_MAGENTA,
_HTML_NO,_POST_FORUM,_POST_NO_PUBACCES1_,_USAGE_BOARDCODE,_USAGE_INSTRUCTIONS,_USAGE_MYPROFILE,
_USAGE_PREVIOUS,_USAGE_TEXT,_USAGE_TEXT_BOLD,_USAGE_TEXT_ITALIC,_USAGE_TEXT_QUOTE,_USAGE_TEXT_UNDERLINE,
_USAGE_TEXT_WILL,_VIEW_LOCKED,_VIEW_EDITOR,_SEARCH_HEADER,_POST_TO_VIEW,_POST_POSTED,_POST_BOARDCODE,
_POST_BBCODE_HELP,_POST_ERROR_EXIT,_POST_EDIT_MESSAGE,_MODERATION_DELETE_REPLIES,_MODERATION_DELETE_POST,
_MODERATION_ERROR_MESSAGE,_SEARCH_ON_USER,_SEARCH_OTHER_OPTIONS,_FORUM_USERSEARCH,_RESULTS_USERNAME,
_UPLOAD_UPDATED,_GEN_TODAYS_POSTS,_GEN_UNANSWERED,_GEN_USAGE,_GEN_VIEWS,_GEN_STARTED,_GEN_POST_A_PROFILE,
_GEN_MY_PROFILE,_GEN_NEW,_GEN_NO_NEW,_GEN_NO_ACCESS,_GEN_POSTS,_GEN_POSTS_DISPLAY,_GEN_CATEGORY,
_GEN_EDIT_MESSAGE,_COM_A_HEADER,_COM_A_HEADER_DESC,_COM_A_CONFIG_DESC,_FILE_INSERT,_FILE_COPY_PASTE,
_FILE_BUTTON,_FILE_UPLOAD,_FILE_SUBMIT,_FILE_ERROR_NAME,_FILE_ERROR_EXISTS,_FILE_UPLOADED,_FILE_UPDATED,
_IMAGE_SUBMIT,_IMAGE_ERROR_NAME,_IMAGE_UPDATED,_IMAGE_COPY_PASTE,_COM_A_IMAGES,_IMAGE_INSERT,_IMAGE_BUTTON,
_IMAGE_ERROR_EXISTS,_COM_A_FILES,_GEN_SUBSCRIPTIONS,_POST_SUCCESS_THREAD_VIEW,_COM_C_BOARDSTATS
*/

// changed in 1.1.5

// added in joomlaboard 1.1.5
DEFINE('_GEN_CATEGORY','Kategorija');
DEFINE('_GEN_STARTEDBY','Autor: ');
DEFINE('_GEN_STATS','statistika');
DEFINE('_STATS_TITLE',' forum - statistika');
DEFINE('_STATS_GEN_STATS','Osnovna statistika');
DEFINE('_STATS_TOTAL_MEMBERS','&#268;lanova:');
DEFINE('_STATS_TOTAL_REPLIES','Odgovora:');
DEFINE('_STATS_TOTAL_TOPICS','Tema:');
DEFINE('_STATS_TODAY_TOPICS','Tema danas:');
DEFINE('_STATS_TODAY_REPLIES','Odgovora danas:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategorija:');
DEFINE('_STATS_TOTAL_SECTIONS','Sekcija:');
DEFINE('_STATS_LATEST_MEMBER','Zadnja registracija:');
DEFINE('_STATS_YESTERDAY_TOPICS','Tema ju&#269;er:');
DEFINE('_STATS_YESTERDAY_REPLIES','Odgovora ju&#269;er:');
DEFINE('_STATS_POPULAR_PROFILE','10 najpopularnijih &#269;lanova (po posjetama profilu)');
DEFINE('_STATS_TOP_POSTERS','Najaktivniji &#269;lanovi');
DEFINE('_STATS_POPULAR_TOPICS','Najpopularnije teme');
DEFINE('_COM_A_STATSPAGE','Prikazati Statistiku');
DEFINE('_COM_A_STATSPAGE_DESC','Ako je ovo postavljeno na &quot;Da&quot; u izborniku na vrhu foruma &#263;e se prikazivati link na stranicu sa raznim statisti&#269;kim informacijama foruma. <em>Stranica sa statistikom &#263;e uvijek biti dostupna administratorima, bez obzira na ovu postavku!</em>');
DEFINE('_COM_C_FBSTATS','Forum Stats');
DEFINE('_COM_C_FBSTATS_DESC','Forum Statistika');
define('_GEN_GENERAL','Op&#263;enito');
define('_PERM_NO_READ','Niste autorizirani za pristup ovom forumu.');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE','Privatne Poruke');
DEFINE('_COM_A_COMBUILDER_TITLE','Community Builder');
DEFINE('_FORUM_SEARCH','Na&#273;i: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','Jeste li sigurni da &#382;elite izbrisati ovu poruku? \n\n Napomena: Izbrisane poruke nije mogu&#263;e vratiti!');
DEFINE('_MODERATION_DELETE_SUCCESS','Postovi su izbrisani');
DEFINE('_COM_A_KUNENA_BY','Joomla! Forum Komponenta - Autor');
DEFINE('_COM_A_RANKING','Status');
DEFINE('_COM_A_BOT_REFERENCE','Discuss Bot - Referentna Karta');
DEFINE('_COM_A_MOSBOT','Koristiti Discuss Bot');
DEFINE('_PREVIEW','Pregled');
DEFINE('_COM_C_UPGRADE','A&#382;uriranje Baze na verziju: ');
DEFINE('_COM_A_MOSBOT_TITLE','Discussbot');
DEFINE('_COM_A_MOSBOT_DESC',
 'Discuss Bot vam mogu&#263;ava da svojim sadr&#382;ajima vrlo lako dodate link na forum diskusiju. Naslov sadr&#382;aja postaje naslov teme. Klikom na takav link, korisnici ili automatski kreiraju novu temu ili se uklju&#269;uju u ve&#263; postoje&#263;u diskusiju.'
.'<br /><strong>Ovaj mosbot se instalira odvojeno.</strong> Vi&#154;e infornacija na <a href="http://www.bestofjoomla.com" target="_blank">Best of Joomla</a>.'
.'<br /><br />Nakon instalacije dodajte: {mos_KUNENA_discuss:<em>catid</em>} u svoj sadr&#382;aj. <em>catid</em> je oznaka kategorije u kojoj &#263;e biti otvorena rasprava. Na po&#269;etnoj stranici foruma postavite pokaziva&#269; mi&#154;a na &#382;eljnu temu i pogledajte statusnu traku.'
.'<br />Na kraju linka nalazi se <em>catid=neki broj</em>. To je broj koji trebate unijeti umijesto <em>catid</em>.'
.'<br />Primjer: ako &#382;elite kreirati diskusiju u Forumu sa catid 26, mosbot &#263;e izgledati ovako: {mos_KUNENA_discuss:26}'
.'<br /><br />Ovo djeluje malo komplicirano ali vam omogu&#263;ava da precizno odaberete odgovaraju&#263;i forum za diskusiju odre&#273;enog sadr&#382;aja. Dugme Mosbot - Referentna Karta &#263;e vam uvelike olak&#154;ati posao.'
);

//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE','Tra&#382;i');
DEFINE('_FORUM_SEARCHRESULTS','rezltati %s od %s');

// Help, FAQ
DEFINE('_COM_FORUM_HELP','FAQ');

// rules.php
DEFINE('_COM_FORUM_RULES','Pravila');
DEFINE('_COM_FORUM_RULES_DESC','<ul><li>Pravila mo&#382;ete urediti u ovoj datoteci joomlaroot/administrator/components/com_fireboard/language/hrvatski.php</li><li>Pravilo 1</li><li>Pravilo 2</li><li>Pravilo 3</li><li>Pravilo 4</li><li>...</li></ul>');


//smile.class.php
DEFINE('_COM_BOARDCODE','Boardcode');

// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS','Postovi su odobreni');
DEFINE('_MODERATION_DELETE_ERROR','Gre&#154;ka: Brisanje postova nije uspjelo');
DEFINE('_MODERATION_APPROVE_ERROR','Gre&#154;ka: Odobravanje postova nije uspjelo');

// listcat.php
DEFINE('_GEN_NOFORUMS','Nema foruma u ovoj kategoriji!');

//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED','Nije uspjelo kreiranje ghost teme u starom forumu!');
DEFINE('_POST_MOVE_GHOST','Ostaviti ghost poruku u starom forumu');

//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP','Odabir Foruma');
DEFINE('_COM_A_FORUM_JUMP','Brzi Odabir Foruma');
DEFINE('_COM_A_FORUM_JUMP_DESC','Postavite na &quot;Da&quot; ako &#382;elite prikazati padaju&#263;i izbornik za brzo prebacivanje iz foruma u forum ili kategoriju.');

//new in 1.1 RC1
DEFINE('_GEN_RULES','Pravila');
DEFINE('_COM_A_RULESPAGE','Stranica s Pravilima');
DEFINE('_COM_A_RULESPAGE_DESC','Postavite na &quot;Da&quot; ako &#382;elite prikazati link na stranicu s pravilima foruma u izborniku na vrhu foruma. Sadr&#382;aj stranice s pravilima mo&#382;ete urediti u joomla_dir/components/com_fireboard/rules.php datoteci. <em>Uvijek napravite sigurnosnu kopiju izmjenjene datoteke ako je ne &#382;elite izgubiti pri eventualnom a&#382;uriranju komponente!</em>');
DEFINE('_MOVED_TOPIC','Premje&#154;teno:');
DEFINE('_COM_A_PDF','Uklju&#269;iti PDF');
DEFINE('_COM_A_PDF_DESC','Postavite na &quot;Da&quot; ako &#382;elite korisnicima omogu&#263;iti download tema u obliku PDF dokumenta.<br />Radi se o <u>jednostavnom</u> PDF dokumentu; bez html-a i elegantnog rasporeda ali sadr&#382;i cjelokupan tekst teme.');
DEFINE('_GEN_PDFA','Kliknite ovdje ako &#382;elite kreirati PDF dokument od sadr&#382;aja ove teme (otvara novi prozor).');
DEFINE('_GEN_PDF', 'PDF');

//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE','Profil ovog korisnika');
DEFINE('_VIEW_ADDBUDDY','Dodavanje ovog korisnika na va&#154;u buddy listu');
DEFINE('_POST_SUCCESS_POSTED','Va&#154;a poruka je uspje&#154;no poslana');
DEFINE('_POST_SUCCESS_VIEW','[ Povratak na post ]');
DEFINE('_POST_SUCCESS_FORUM','[ Povratak na forum ]');
DEFINE('_RANK_ADMINISTRATOR','Admin');
DEFINE('_RANK_MODERATOR','Moderator');
DEFINE('_SHOW_LASTVISIT','Od zadnje posjete');
DEFINE('_COM_A_BADWORDS_TITLE','Filtriranje Neprihvatljivih rije&#269;i');
DEFINE('_COM_A_BADWORDS','Filtriranje Uklju&#269;eno');
DEFINE('_COM_A_BADWORDS_DESC','Postavite na &quot;Da&quot; ako &#382;elite filtrirati postove koji sadr&#382;e rije&#269;i koje ste, u konfiguraciji Badword komponente, definirali kao neprihvatljive. Ova funkcija je dostupna samo ako ste instalirali Badword komponentu!');
DEFINE('_COM_A_BADWORDS_NOTICE','* Ova poruka je cenzurirana jer sadr&#382;i jednu ili vi&#154;e rije&#269;i koje je administrator ozna&#269;io kao neprihvatljive *');
DEFINE('_COM_A_COMBUILDER_PROFILE','Kreiranje Community Builder profila');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC','Klikom na link se kreiraju dodatna polja, nu&#382;na za forum, u Community Builder korisni&#269;kom profilu. Kreirana dodatna polja mo&#382;ete premjestiti gdje &#382;elite kroz Community Builder admin, samo im nemojte mijenjati imena ili opcije. Ako ih izbri&#154;ete kroz Community Builder administraciju, mo&#382;ete ih ponovo kreirati uz pomo&#263; ovog linka, ina&#269;e nemojte klikati na ovaj link vi&#154;e puta.'); //x
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK','> Kliknite ovdje <');
DEFINE('_COM_A_COMBUILDER','Community Builder korisni&#269;ki profili'); //x
DEFINE('_COM_A_COMBUILDER_DESC','Ovime se aktivira integracija sa Community Builder komponentom (www.joomlapolis.com). Sve funkcije korisni&#269;kog profila &#263;e biti preba&#269;ene na Community Builder i link na forumu &#263;e voditi na profil iz te komponente. Ova postavka ima prednost nad Clexus PM postavkom ako su obje postavljene na &quot;Da&quot;. Napomena: Ne zaboravite primjeniti promjene u Community Builder bazi uz pomo&#263; opcije ispod.'); //x
DEFINE('_COM_A_AVATAR_SRC','Avatar slike iz');
DEFINE('_COM_A_AVATAR_SRC_DESC','Ako imate instaliranu Clexus PM ili Community Builder komponentu, mo&#382;ete konfigurirati Fireboard da koristi avatare iz tih komponenti. NAPOMENA: Za Community Builder treba biti uklju&#269;ena miniSlike (thumbnails) opcija jer forum koristi umanjene slike a ne originale.');
DEFINE('_COM_A_KARMA','Karma indikator');
DEFINE('_COM_A_KARMA_DESC','Postavite na &quot;Da&quot; za kori&#154;tenje Karme i karma dugmadi (pohvala / pokuda) ako je uklju&#269;ena Korisni&#269;ka Statistika.');
DEFINE('_COM_A_DISEMOTICONS','Isklju&#269;i emoticone');
DEFINE('_COM_A_DISEMOTICONS_DESC','Postavite na &quot;Da&quot; ako &#382;elite potpuno isklju&#269;iti grafi&#269;ke emoticon-e (smje&#154;ki&#263;e).');
DEFINE('_COM_C_FBCONFIG','Fireboard Konfiguracija');
DEFINE('_COM_C_FBCONFIGDESC','Konfiguriranje svih fireboard funkcija');
DEFINE('_COM_C_FORUM','Administracija Foruma');
DEFINE('_COM_C_FORUMDESC','Dodavanje kategorija/foruma i njihovo konfiguriranje');
DEFINE('_COM_C_USER','Administracija Korisnika');
DEFINE('_COM_C_USERDESC','Upravljanje korisnicima i korisni&#269;kim profilima');
DEFINE('_COM_C_FILES','Preglednik Datoteka');
DEFINE('_COM_C_FILESDESC','Pregledavanje i administracija uploadanih datoteka');
DEFINE('_COM_C_IMAGES','Preglednik Slika');
DEFINE('_COM_C_IMAGESDESC','Pregledavanje i administracija uploadanih slika');
DEFINE('_COM_C_CSS','CSS');
DEFINE('_COM_C_CSSDESC','Ure&#273;ivanje CSS datoteke');
DEFINE('_COM_C_SUPPORT','Podr&#154;ka');
DEFINE('_COM_C_SUPPORTDESC','Spajanje na Best of Joomla website (novi prozor)');
DEFINE('_COM_C_PRUNETAB','Odr&#382;avanje Foruma');
DEFINE('_COM_C_PRUNETABDESC','Uklanjanje starih tema (configurabilno)');
DEFINE('_COM_C_PRUNEUSERS','Odr&#382;avanje Korisnika');
DEFINE('_COM_C_PRUNEUSERSDESC','Sinhronizacija Fireboard i Joomla! korisnika');
DEFINE('_COM_C_LOADSAMPLE','U&#269;itavanje Primjera');
DEFINE('_COM_C_LOADSAMPLEDESC','Za lak&#154;i po&#269;etak: u&#269;itajte Primjere u praznu Fireboard tablicu');
DEFINE('_COM_C_REMOVESAMPLE', 'Izbri&#154;i primjere');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Brisanje u&#269;itanih primjera iz baze');
DEFINE('_COM_C_LOADMODPOS','Pozicije Modula');
DEFINE('_COM_C_LOADMODPOSDESC','U&#269;itavanje pozicija modula za FireBoard Predlo&#382;ak');
DEFINE('_COM_C_UPGRADEDESC','A&#382;urirajte bazu na najnoviju verziju');
DEFINE('_COM_C_BACK','Natrag na Fireboard Kontrolnu Plo&#269;u');
DEFINE('_SHOW_LAST_SINCE','Aktivne teme od va&#154;eg zadnjeg posjeta:');
DEFINE('_POST_SUCCESS_REQUEST2','Dovr&#154;eno');
DEFINE('_POST_NO_PUBACCESS3','Kliknite ovdje za Registraciju.');

//==================================================================================================

//Changed in 1.0.4

//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE','Poruka je izbrisana.');
DEFINE('_POST_SUCCESS_EDIT','Poruka je uspje&#154;no ure&#273;ena.');
DEFINE('_POST_SUCCESS_MOVE','Tema je uspje&#154;no premje&#154;tena.');
DEFINE('_POST_SUCCESS_POST','Va&#154;a poruka je uspje&#154;no poslana.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Pretplata je uklju&#269;ena.');

//==================================================================================================

//new in 1.0.3 stable

//Karma
DEFINE('_KARMA','Karma');
DEFINE('_KARMA_SMITE','Pokuda');
DEFINE('_KARMA_APPLAUD','Pohvala');
DEFINE('_KARMA_BACK','Za povratak na temu,');
DEFINE('_KARMA_WAIT','Karmu jedne osobe mo&#382;ete mijenjati svakih 6 sati. <br/>Molimo, pri&#269;ekajte toliko prije ponovnog mijenjanja karme odre&#273;ene osobe.');
DEFINE('_KARMA_SELF_DECREASE','Molimo, nemojte poku&#154;avati smanjiti svoju karmu!');
DEFINE('_KARMA_SELF_INCREASE','Va&#154;a karma je umanjena zbog poku&#154;aja da je sami pove&#263;ate!');
DEFINE('_KARMA_DECREASED','Korisnikova karma je umanjena. Ako se ne vratite na temu za koji trenutak,');
DEFINE('_KARMA_INCREASED','Korisnikova karma je uve&#263;ana. Ako se ne vratite na temu za koji trenutak,');
DEFINE('_COM_A_TEMPLATE','Predlo&#382;ak');
DEFINE('_COM_A_TEMPLATE_DESC','Odaberite &#382;eljeni predlo&#382;ak.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH','Grafi&#269;ki Elementi');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC','Odabir kompleta grafi&#269;kih elemenata koji &#263;e se koristiti u predlo&#154;ku.');
DEFINE('_PREVIEW_CLOSE','Zatvori prozor');

//==========================================

//new in 1.0 Stable
DEFINE('_GEN_PATHWAY',':: ');
DEFINE('_COM_A_POSTSTATSBAR','Grafi&#269;ka Statistika Postova');
DEFINE('_COM_A_POSTSTATSBAR_DESC','Postavite na &quot;Da&quot; ako &#382;elite prikazati broj postova odre&#273;enog korisnika u grafi&#269;kom obliku.');
DEFINE('_COM_A_POSTSTATSCOLOR','Boja Grafi&#269;ke Statistike Postova');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Upi&#154;ite broj boje koju &#382;elite koristiti za Grafi&#269;ku Statistiku Postova');
DEFINE('_LATEST_REDIRECT','Fireboard mora ponovo uspostaviti pristupne privilegije prije kreiranja liste najnovijih postova.\nNe brinite, ovo je potpuno normalno nakon vi&#154;e od 30 minuta neaktivnosti ili nakon ponovne prijave.\nMolimo, ponovite svoju akciju.');
DEFINE('_SMILE_COLOUR','Boja');
DEFINE('_SMILE_SIZE','Veli&#269;ina');
DEFINE('_COLOUR_DEFAULT','Standardna');
DEFINE('_COLOUR_RED','Crvena');
DEFINE('_COLOUR_PURPLE','Ljubi&#269;asta');
DEFINE('_COLOUR_BLUE','Plava');
DEFINE('_COLOUR_GREEN','Zelena');
DEFINE('_COLOUR_YELLOW','Žuta');
DEFINE('_COLOUR_ORANGE','Naran&#269;asta');
DEFINE('_COLOUR_DARKBLUE','TamnoPlava');
DEFINE('_COLOUR_BROWN','Sme&#273;a');
DEFINE('_COLOUR_GOLD','Zlatna');
DEFINE('_COLOUR_SILVER','Srebrena');
DEFINE('_SIZE_NORMAL','Normal');
DEFINE('_SIZE_SMALL','Mala');
DEFINE('_SIZE_VSMALL','Vrlo Mala');
DEFINE('_SIZE_BIG','Velika');
DEFINE('_SIZE_VBIG','Vrlo Velika');
DEFINE('_IMAGE_SELECT_FILE','Prilo&#382;ena Slika');
DEFINE('_FILE_SELECT_FILE','Prilo&#382;ena Datoteka');
DEFINE('_FILE_NOT_UPLOADED','Va&#154;a datoteka nije poslana. Poku&#154;ajte ponoviti ili urediti post');
DEFINE('_IMAGE_NOT_UPLOADED','Va&#154;a slika nije poslana. Poku&#154;ajte ponoviti ili urediti post');
DEFINE('_BBCODE_IMGPH','Insert [img] placeholder in the post for attached image');
DEFINE('_BBCODE_FILEPH','Insert [file] placeholder in the post for attached file');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','Otkazivanje <b><u>svih pretplata</u></b> (uklju&#269;uju&#263;i i one koje mo&#382;da nisu vidljive)');
DEFINE('_LINK_JS_REMOVED','<em>Aktivni link, koji sadr&#382;i JavaScript, je automatski uklonjen</em>');

//==========================================

//new in 1.0 RC4
DEFINE('_COM_A_LOOKS','Izgled Foruma');
DEFINE('_COM_A_USERS','Korisni&#269;ke postavke');
DEFINE('_COM_A_LENGTHS','Duljina potpisa i sli&#269;no');
DEFINE('_COM_A_SUBJECTLENGTH','Maksimalna Duljina Naslova');
DEFINE('_COM_A_SUBJECTLENGTH_DESC','Maksimalna duljina Naslova teme ili posta. Baza podr&#382;ava maksimum od 255 znakova. Ako koristite multi-byte znakove poput Unicode, UTF-8, ne-ISO-8599-x postavite (u ovo polje) manji maksimum po forumuli:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Primjer za UTF-8, za koji je max. character bite syze per character is 4 bytes: 255/4=63.');
DEFINE('_LATEST_THREADFORUM','Tema/Forum');
DEFINE('_LATEST_NUMBER','Novih');
DEFINE('_COM_A_SHOWNEW','Prika&#382;i Nove postove');
DEFINE('_COM_A_SHOWNEW_DESC','Ako je postavljeno na &quot;Da&quot; Fireboard korisnicima pokazuje indikator uz forume koji sadr&#382;e nove postove i koji postovi su novi od njihovog zadnjeg posjeta.');
DEFINE('_COM_A_NEWCHAR','Indikator za &quot;Novo&quot;');
DEFINE('_COM_A_NEWCHAR_DESC','Ovdje definirate &#154;to &#382;elite koristiti kao indikator novih postova (kao &quot;!&quot; ili &quot;Novo!&quot;)');
DEFINE('_LATEST_AUTHOR','Autor');
DEFINE('_GEN_FORUM_NEWPOST','Novi Postovi');
DEFINE('_GEN_FORUM_NOTNEW','Nema Novih Postova');
DEFINE('_GEN_UNREAD','Nepro&#269;itana Tema');
DEFINE('_GEN_NOUNREAD','Pro&#269;itana Tema');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Ozna&#269;i sve forume pro&#269;itanima');
DEFINE('_GEN_MARK_THIS_FORUM_READ','Ozna&#269;i ovaj forum pro&#269;itanim');
DEFINE('_GEN_FORUM_MARKED','Svi su postovi u ovom forumu ozna&#269;eni kao pro&#269;itani');
DEFINE('_GEN_ALL_MARKED','Svi su postovi ozna&#269;eni kao pro&#269;itani');
DEFINE('_IMAGE_UPLOAD','Upload Slike');
DEFINE('_IMAGE_DIMENSIONS','Va&#154;a slika mo&#382;e biti maksimalno (&#154;irina x visina - veli&#269;ina)');
DEFINE('_IMAGE_ERROR_TYPE','Koristite samo jpeg, gif ili png slike');
DEFINE('_IMAGE_ERROR_EMPTY','Izaberite dokument prije uploada');
DEFINE('_IMAGE_ERROR_SIZE','Veli&#269;ina slike nadilazi maksimum koji je postavio administrator.');
DEFINE('_IMAGE_ERROR_WIDTH','&#138;irina slike nadilazi maksimum koji je postavio Administrator.');
DEFINE('_IMAGE_ERROR_HEIGHT','Visina slike nadilazi maksimum koji je postavio Administrator.');
DEFINE('_IMAGE_UPLOADED','Va&#154;a slika je poslana na server.');
DEFINE('_COM_A_IMAGE','Slike');
DEFINE('_COM_A_IMGHEIGHT','Maksimalna Visina Slike');
DEFINE('_COM_A_IMGWIDTH','Maksimalna &#138;irina Slike');
DEFINE('_COM_A_IMGSIZE','Maksimalna Veli&#269;ina Slike <br/><em>u Kilobajtima</em>');
DEFINE('_COM_A_IMAGEUPLOAD','Upload Slika - svi');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Postavite na &quot;Da&quot; ako &#382;elite da svi posjetitelji (public), mogu slati slike na server.');
DEFINE('_COM_A_IMAGEREGUPLOAD','Upload Slika - registrirani');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu slati slike na server.<br/>Napomena: (Super)administratori i moderatori to mogu uvijek.');

  //New since preRC4-II:
DEFINE('_COM_A_UPLOADS','Upload');
DEFINE('_FILE_TYPES','Datoteka mo&#382;e biti tip - max. veli&#269;ina');
DEFINE('_FILE_ERROR_TYPE','Upload je dozvoljen samo za datoteke tipa:\n');
DEFINE('_FILE_ERROR_EMPTY','Izaberite datoteku prije uploada');
DEFINE('_FILE_ERROR_SIZE','Veli&#269;ina datoteke nadilazi dozvoljeni maksimum.');
DEFINE('_COM_A_FILE','Datoteke');
DEFINE('_COM_A_FILEALLOWEDTYPES','Dozvoljene vrste datoteka');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Navedite tipove datoteka dozvoljenih za upload. Stavke razdvojite zarezom, koristite samo <strong>mala slova</strong> bez razmaka.<br />Primjer: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Maksimalna Veli&#269;ina Datoteke<br/><em>u Kilobajtima</em>');
DEFINE('_COM_A_FILEUPLOAD','Upload Datoteka - svi');
DEFINE('_COM_A_FILEUPLOAD_DESC','Postavite na &quot;Da&quot; ako &#382;elite da svi posjetitelji (public), mogu slati dokumente na server.');
DEFINE('_COM_A_FILEREGUPLOAD','Upload Datoteka - registrirani');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu slati dokumente na server.<br/>Napomena: (Super)administratori i moderatori to mogu uvijek.');
DEFINE('_SUBMIT_CANCEL','Va&#154; post je otkazan');
DEFINE('_HELP_SUBMIT','Kliknite ovdje za slanje poruke');
DEFINE('_HELP_PREVIEW','Kliknite ovdje da vidite kako &#263;e izgledati va&#154;a poruka kada je po&#154;aljete');
DEFINE('_HELP_CANCEL','Kliknite ovdje za odustajanje od slanja poruke');
DEFINE('_POST_DELETE_ATT','Ako je ova ku&#263;ica popujena, sve slike i datoteke iz ovog posta &#263;e tako&#273;er biti izbrisane (preporu&#269;eno).');

   //new since preRC4-III
DEFINE('_COM_A_USER_MARKUP','Ozna&#269;ene Izmjene');
DEFINE('_COM_A_USER_MARKUP_DESC','Postavite na &quot;Da&quot; ako &#382;elite da mjenjani post bude ozna&#269;en tekstom koji pokazuje tko je i kada izvr&#154;io izmjene.');
DEFINE('_EDIT_BY','Mijenjano:');
DEFINE('_EDIT_AT',' ');
DEFINE('_UPLOAD_ERROR_GENERAL','Gre&#154;ka pri uploadu Avatara.');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Preglednik Slika');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Preglednik Datoteka');
DEFINE('_COM_A_IMGB_TOTAL_IMG','Broj uploadanih slika');
DEFINE('_COM_A_IMGB_TOTAL_FILES','Broj uploadanih datoteka');
DEFINE('_COM_A_IMGB_ENLARGE','Kliknite na sliku ako je &#382;elite vidjeti u punoj veli&#269;ini');
DEFINE('_COM_A_IMGB_DOWNLOAD','Kliknite na sliku datoteke za download');
DEFINE('_COM_A_IMGB_DUMMY_DESC','Opcija &quot;Zamjeni neutralnom slikom&quot; &#263;e zamijeniti odabranu sliku neutralnom.<br /> Ovo vam omogu&#263;ava brisanje ne&#382;eljenih slika bez o&#154;te&#263;enja postova.<br /><small><em>Ponekad izmjena nije vidljiva dok ne osvje&#382;ite ovu stranicu.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY','Trenutna neutralna slika');
DEFINE('_COM_A_IMGB_REPLACE','Zamjeni neutralnom slikom');
DEFINE('_COM_A_IMGB_REMOVE','Ukloni potpuno');
DEFINE('_COM_A_IMGB_NAME','Naziv');
DEFINE('_COM_A_IMGB_SIZE','Veli&#269;ina');
DEFINE('_COM_A_IMGB_DIMS','Dimenzije');
DEFINE('_COM_A_IMGB_CONFIRM','Jeste li potpuno sigurni da &#382;elite izbrisati ovu datoteku? \n Brisanje datoteke mo&#382;e o&#154;tetiti referentni post...');
DEFINE('_COM_A_IMGB_VIEW','Uredi referentni post');
DEFINE('_COM_A_IMGB_NO_POST','Nema referentnog posta!');
DEFINE('_USER_CHANGE_VIEW','Promjene ovih postavki &#263;e postati aktivne kada ponovo posjetite Forum.<br /> Ako &#382;elite izmijeniti tip prikaza  &quot;U Letu&quot; mo&#382;ete upotrijebiti opciju u forumskoj alatnoj traci.');
DEFINE('_MOSBOT_DISCUSS_A','Forum Rasprava Na Ovu Temu. ( ');
DEFINE('_MOSBOT_DISCUSS_B',' )');
DEFINE('_POST_DISCUSS','Ovo je rasprava o tekstu');
DEFINE('_COM_A_RSS','RSS');
DEFINE('_COM_A_RSS_DESC','RSS omogu&#263;ava korisnicima download najnovijih postova na njihov desktop/RSS aplikaciju (pogledajte <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> za primjer).');
DEFINE('_LISTCAT_RSS','primajte najnovije postove direktno na desktop.');
DEFINE('_SEARCH_REDIRECT','Fireboard mora ponovo uspostaviti pristupne privilegije prije kreiranja liste najnovijih postova.\nNe brinite, ovo je potpuno normalno nakon vi&#154;e od 30 minuta neaktivnosti ili nakon ponovne prijave.\nMolimo, ponovite svoju akciju.');



//====================

//admin.forum.html.php
DEFINE('_COM_A_CONFIG','Konfiguracija');
DEFINE('_COM_A_VERSION','Va&#154;a verzija je ');
DEFINE('_COM_A_DISPLAY','Prika&#382;i #');
DEFINE('_COM_A_CURRENT_SETTINGS','Trenutne Postavke');
DEFINE('_COM_A_EXPLANATION','Obja&#154;njenje');
DEFINE('_COM_A_BOARD_TITLE','Naziv Foruma');
DEFINE('_COM_A_BOARD_TITLE_DESC','Naziv Foruma');
DEFINE('_COM_A_VIEW_TYPE','Prikaz');
DEFINE('_COM_A_VIEW_TYPE_DESC','Birajte izme&#273;u razgranatog i plo&#154;nog prikaza - korisnici ovo mogu mijenjati po &#382;elji');
DEFINE('_COM_A_THREADS','Tema po Stranici');
DEFINE('_COM_A_THREADS_DESC','Broj tema koji &#263;e biti prikazan na svakoj stranici.');
DEFINE('_COM_A_REGISTERED_ONLY','Samo za Registrirane Korisnike');
DEFINE('_COM_A_REG_ONLY_DESC','Postavite na &quot;Da&quot; ako &#382;elite dozvoliti kori&#154;tenje Foruma samo registriranim korisnicima (&#269;itanje & pisanje), Postavite na &quot;Ne&quot; ako &#382;elite da svi posjetitelji mogu koristiti Forum');
DEFINE('_COM_A_PUBWRITE','Gosti Mogu Pisati');
DEFINE('_COM_A_PUBWRITE_DESC','Postavite na &quot;Da&quot; ako &#382;elite dozvoliti pisanje svim posjetiteljima, Postavite na &quot;Ne&quot; ako &#382;elite dozvoliti &#269;itanje svim posjetiteljima, a pisanje samo registriranim korisnicima');
DEFINE('_COM_A_USER_EDIT','Korisni&#269;ke Izmjene');
DEFINE('_COM_A_USER_EDIT_DESC','Postavite na &quot;Da&quot; ako &#382;elite dozvoliti registriranim korisnicima da rade izmjene u svojim postovima.');
DEFINE('_COM_A_MESSAGE','Kako bi ste sa&#269;uvali izmjene ovih vrijednosti koristite &quot;Spremi&quot; dugme na vrhu.');
DEFINE('_COM_A_HISTORY','Povijest Teme');
DEFINE('_COM_A_HISTORY_DESC','Postavite na &quot;Da&quot; ako &#382;elite, pri slanju odgovora, prikazati nekoliko zadnjih postova iz teme na koju se odgovara');
DEFINE('_COM_A_SUBSCRIPTIONS','Pretplate');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','Postavite na &quot;Da&quot; ako &#382;elite dozvoliti registriranim korisnicima da se pretplate na temu i primaju email obavijesti o novim postovima');
DEFINE('_COM_A_HISTLIM','Povijest Limit');
DEFINE('_COM_A_HISTLIM_DESC','Koliko zadnjih postova &#382;elite prikazati pri slanju odgovora');
DEFINE('_COM_A_FLOOD','Flood Za&#154;tita');
DEFINE('_COM_A_FLOOD_DESC','Koliko sekundi mora pro&#263;i izme&#273;u dva uzastopna posta istog korisnika. Postavite na 0 (nula) za isklju&#269;ivanje Flood Za&#154;tite. Napomena: Flood Za&#154;tita <em>mo&#382;e</em> izazvati degradaciju performansi foruma.');
DEFINE('_COM_A_MODERATION','Obavijesti za Moderatore');
DEFINE('_COM_A_MODERATION_DESC','Postavite na &quot;Da&quot; ako &#382;elite da moderatori primaju email obavijesti o novim postovima u forumima koje moderiraju. Napomena: iako je svaki (super)administrator automatski i Moderator svih foruma, ne&#263;e dobivati ove obavijesti ako nije eksplicitno postavljen kao moderator odre&#273;enog foruma!.');
DEFINE('_COM_A_SHOWMAIL','Prikaz Email-a');
DEFINE('_COM_A_SHOWMAIL_DESC','Postavite na &quot;Da&quot; ako nikada ne &#382;elite prikazati korisnikovu email adresu (ni registriranim korisnicima).');
DEFINE('_COM_A_AVATAR','Avatari Dopu&#154;teni');
DEFINE('_COM_A_AVATAR_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu imati avatar (korisnici mogu birati avatar kroz svoj profil)');
DEFINE('_COM_A_AVHEIGHT','Maksimalna Visina Avatara');
DEFINE('_COM_A_AVWIDTH','Maksimalna &#138;irina Avatara');
DEFINE('_COM_A_AVSIZE','Maksimalna Veli&#269;ina Avatara<br/><em>u Kilobajtima</em>');
DEFINE('_COM_A_USERSTATS','Korisni&#269;ka Statistika');
DEFINE('_COM_A_USERSTATS_DESC','Postavite na &quot;Da&quot; ako &#382;elite prikazati Korisni&#269;ku Statistiku kao broj postova, tip korisnika (Admin, Moderator,Korisnik, itd.).');
DEFINE('_COM_A_AVATARUPLOAD','Upload Avatara');
DEFINE('_COM_A_AVATARUPLOAD_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu uploadati avatar.');
DEFINE('_COM_A_AVATARGALLERY','Kori&#154;tenje Galerije Avatara');
DEFINE('_COM_A_AVATARGALLERY_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu izabrati avatar iz Galerije avatara (components/com_fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','Postavite na &quot;Da&quot; ako &#382;elite prikazati status registriranih korisnika na osnovi broja njihovih postova.<br/><strong>Korisni&#269;ka Statistika mora biti uklju&#269;ena da bi ovo funkcioniralo.</strong>');
DEFINE('_COM_A_RANKINGIMAGES','Statusna Grafika');
DEFINE('_COM_A_RANKINGIMAGES_DESC','Postavite na &quot;Da&quot; ako &#382;elite grafi&#269;ki prikaz statusa registriranih korisnika (iz components/com_fireboard/ranks). Ako je ovo isklju&#269;eno, prikaz &#263;e biti tekstualan. Dodatna dokumentacija je dostupna na www.bestofjoomla.com');
DEFINE('_COM_A_RANK1','Status 1');
DEFINE('_COM_A_RANK1TXT','Status 1 tekst');
DEFINE('_COM_A_RANK2','Status 2');
DEFINE('_COM_A_RANK2TXT','Status 2 tekst');
DEFINE('_COM_A_RANK3','Status 3');
DEFINE('_COM_A_RANK3TXT','Status 3 tekst');
DEFINE('_COM_A_RANK4','Status 4');
DEFINE('_COM_A_RANK4TXT','Status 4 tekst');
DEFINE('_COM_A_RANK5','Status 5');
DEFINE('_COM_A_RANK5TXT','Status 5 tekst');
DEFINE('_COM_A_RANK6','Status 6');
DEFINE('_COM_A_RANK6TXT','Status 6 tekst');
DEFINE('_COM_A_RANK','Status');
DEFINE('_COM_A_RANK_NAME','Status Naslov');
DEFINE('_COM_A_RANK_LIMIT','Status Limit');

//email and stuff
$_COM_A_NOTIFICATION ="Obavijest o novom postu od ";
$_COM_A_NOTIFICATION1="Novi post je poslan u Temu koju pratite na ";
$_COM_A_NOTIFICATION2="Teme koje pratite mo&#382;ete administrirati kroz 'moj profil' link na forumu. Na isti na&#269;in mo&#382;ete i otkazati pretplatu.";
$_COM_A_NOTIFICATION3="Ovo je automatska obavijest - ne odgovarajte.";
$_COM_A_NOT_MOD1="Novi post je poslan u forum koji vi moderirate na ";
$_COM_A_NOT_MOD2="Pogledajte ga kad se slijede&#263;i put prijavite.";

DEFINE('_COM_A_NO','Ne');
DEFINE('_COM_A_YES','Da');
DEFINE('_COM_A_FLAT','Plo&#154;no');
DEFINE('_COM_A_THREADED','Razgranato');
DEFINE('_COM_A_MESSAGES','Postova po stranici');
DEFINE('_COM_A_MESSAGES_DESC','Broj poruka koji &#263;e biti prikazan na svakoj stranici');

   //admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME','Korisni&#269;ko ime');
DEFINE('_COM_A_USERNAME_DESC','Postavite na &quot;Da&quot; ako &#382;elite da korisni&#269;ko ime bude upotrijebljeno umjesto korisnikovog pravog imena.');
DEFINE('_COM_A_CHANGENAME','Promjena Imena');
DEFINE('_COM_A_CHANGENAME_DESC','Postavite na &quot;Da&quot; ako &#382;elite da registrirani korisnici mogu mijenjati svoje ime kad pi&#154;u poruku. Ako postavite na &quot;Ne&quot; registrirani korisnici ne&#263;e mo&#263;i mijenjati svoje ime.');

   //admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE','Forum Neaktivan');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','Ako &#382;elite forum u&#269;initi privremeno nedostupnim, postavite na &quot;Da&quot;. Forum &#263;e i dalje biti dostupan (super) administratorima.');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Forum Neaktivan - Poruka');
DEFINE('_COM_A_PRUNE','Odr&#382;avanje Foruma');
DEFINE('_COM_A_PRUNE_NAME','Odabir Foruma:');
DEFINE('_COM_A_PRUNE_DESC','Funkcija -Prune- vam omogu&#263;ava jednostavno uklanjanje tema u kojima nije bilo novih postova odre&#273;eni broj dana. Ovo ne uklanja usidrene ni zaklju&#269;ane teme - njih morate ukloniti ru&#269;no. Teme u zaklju&#269;anim forumima tako&#273;er ne&#263;e biti uklonjene.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Uklanjanje tema u kojima nije bilo postova u zadnjih ');
DEFINE('_COM_A_PRUNE_DAYS','dana');
DEFINE('_COM_A_PRUNE_USERS','Odr&#382;avanje Korisnika');
DEFINE('_COM_A_PRUNE_USERS_DESC','Funkcija  -Odr&#382;avanje Korisnika- vam omogu&#263;ava uskla&#273;ivanje Fireboard korisni&#269;ke liste i Joomla! korisni&#269;ke liste. <br />Profili Fireboard Korisnika, koji vi&#154;e ne postoje u Joomla! sustavu, &#263;e biti izbrisani.<br /><br />Kada budete &#382;eljeli obaviti uskla&#273;ivanje, kliknite &quot;Prune&quot;.');


//general
DEFINE('_GEN_ACTION','Akcija');
DEFINE('_GEN_AUTHOR','Autor');
DEFINE('_GEN_BY',' ');
DEFINE('_GEN_CANCEL','Odustani');
DEFINE('_GEN_CONTINUE','Po&#154;alji');
DEFINE('_GEN_DATE','Datum');
DEFINE('_GEN_DELETE','Izbri&#154;i');
DEFINE('_GEN_EDIT','Uredi');
DEFINE('_GEN_EMAIL','Email');
DEFINE('_GEN_EMOTICONS','Emotikone');
DEFINE('_GEN_FLAT','Plo&#154;no');
DEFINE('_GEN_FLAT_VIEW','Plo&#154;no');
DEFINE('_GEN_FORUMLIST','Lista Foruma');
DEFINE('_GEN_FORUM','Forum');
DEFINE('_GEN_HELP','Pomo&#263;');
DEFINE('_GEN_HITS','Posjeta');
DEFINE('_GEN_LAST_POST','Zadnji Post');
DEFINE('_GEN_LATEST_POSTS','Novi Postovi');
DEFINE('_GEN_LOCK','Zaklju&#269;aj');
DEFINE('_GEN_UNLOCK','Odklju&#269;aj');
DEFINE('_GEN_LOCKED_FORUM','Forum je Zaklju&#269;an');
DEFINE('_GEN_LOCKED_TOPIC','Tema je Zaklju&#269;ana');
DEFINE('_GEN_MESSAGE','Poruka');
DEFINE('_GEN_MODERATED','Forum je moderiran; novi postovi moraju biti odobreni prije objavljivanja.');
DEFINE('_GEN_MODERATORS','Moderatori');
DEFINE('_GEN_MOVE','Premjesti');
DEFINE('_GEN_NAME','Ime');
DEFINE('_GEN_POST_NEW_TOPIC','Nova Tema');
DEFINE('_GEN_POST_REPLY','Odgovor');
DEFINE('_GEN_MYPROFILE','Moj Profil');
DEFINE('_GEN_QUOTE','Citat');
DEFINE('_GEN_REPLY','Odgovor');
DEFINE('_GEN_REPLIES','Odgovora');
DEFINE('_GEN_THREADED','Razgranato');
DEFINE('_GEN_THREADED_VIEW','Razgranato');
DEFINE('_GEN_SIGNATURE','Potpis');
DEFINE('_GEN_ISSTICKY','Tema je usidrena.');
DEFINE('_GEN_STICKY','Usidri');
DEFINE('_GEN_UNSTICKY','Odsidri');
DEFINE('_GEN_SUBJECT','Naslov');
DEFINE('_GEN_SUBMIT','Po&#154;alji');
DEFINE('_GEN_TOPIC','Tema');
DEFINE('_GEN_TOPICS','Tema');
DEFINE('_GEN_TOPIC_ICON','Ikona');
DEFINE('_GEN_SEARCH_BOX','pretra&#382;i forum');
$_GEN_THREADED_VIEW="Razgranato";
$_GEN_FLAT_VIEW    ="Plo&#154;no";


//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD','Upload');
DEFINE('_UPLOAD_DIMENSIONS','Va&#154;a slika mo&#382;e biti maksimalno (&#154;irina x visina - veli&#269;ina)');
DEFINE('_UPLOAD_SUBMIT','Izaberite novi Avatar za Upload');
DEFINE('_UPLOAD_SELECT_FILE','Odabir dokumenta');
DEFINE('_UPLOAD_ERROR_TYPE','Koristite samo jpeg, gif ili png slike');
DEFINE('_UPLOAD_ERROR_EMPTY','Izaberite dokument prije uploada');
DEFINE('_UPLOAD_ERROR_NAME','Ime dokumenta mo&#382;e sadr&#382;avati samo alfanumeri&#269;ke znakove bez razmaka.');
DEFINE('_UPLOAD_ERROR_SIZE','Veli&#269;ina slike nadilazi maksimum koji je postavio Administrator.');
DEFINE('_UPLOAD_ERROR_WIDTH','&#138;irina slike nadilazi maksimum koji je postavio Administrator.');
DEFINE('_UPLOAD_ERROR_HEIGHT','Visina slike nadilazi maksimum koji je postavio Administrator.');
DEFINE('_UPLOAD_ERROR_CHOOSE',"Niste izabrali Avatar iz galerije..");
DEFINE('_UPLOAD_UPLOADED','Va&#154; avatar je poslan na server.');
DEFINE('_UPLOAD_GALLERY','Izaberite jedan od Avatara iz Galerije:');
DEFINE('_UPLOAD_CHOOSE','Potvrda Izbora');


// listcat.php
DEFINE('_LISTCAT_ADMIN','Administrator ih mora prvo kreirati iz ');
DEFINE('_LISTCAT_DO','Oni &#263;e znati &#154;to u&#269;initi ');
DEFINE('_LISTCAT_INFORM','Informirajte ih i recite im da po&#382;ure!');
DEFINE('_LISTCAT_NO_CATS','Jo&#154; nema definiranih kategorija u forumu.');
DEFINE('_LISTCAT_PANEL','Administracijski Panel.');
DEFINE('_LISTCAT_PENDING','privatne poruke');


// moderation.php
DEFINE('_MODERATION_MESSAGES','Nema nepro&#269;itanih poruka u ovom forumu.');


// post.php
DEFINE('_POST_ABOUT_TO_DELETE','Pripremate se izbrisati poruku');
DEFINE('_POST_ABOUT_DELETE','<strong>Napomena:</strong> ako izbri&#154;ete prvi post u Temi biti &#263;e izbrisani i svi odgovori!<br />
..Ako &#382;elite izbrisati samo prvi post - razmotrite uklanjanje sadr&#382;aja i imena autora umjesto brisanja..
<br />
- Ako izbri&#154;ete neki od odgovora ostali odgovori &#263;e biti pomaknuti uvis za jedno mjesto.');
DEFINE('_POST_CLICK','kliknite ovdje');
DEFINE('_POST_ERROR','Nije prona&#273;eno korisni&#269;ko ime/email.');
DEFINE('_POST_ERROR_MESSAGE','Nepoznata SQL Gre&#154;ka - va&#154;a poruka nije poslana.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Gre&#154;ka - va&#154;a poruka nije a&#382;urirana.');
DEFINE('_POST_ERROR_TOPIC','Gre&#154;ka prilikom brisanja. Pogledajte detalje o gre&#154;ci ispod:');
DEFINE('_POST_FORGOT_NAME','Zaboravili ste unijeti va&#154;e ime.');
DEFINE('_POST_FORGOT_SUBJECT','Zaboravili ste unijeti naslov. ');
DEFINE('_POST_FORGOT_MESSAGE','Zaboravili ste unijeti poruku.');
DEFINE('_POST_INVALID','Neispravan post id.');
DEFINE('_POST_LOCK_SET','Tema je sada zaklju&#269;ana.');
DEFINE('_POST_LOCK_NOT_SET','Tema nije zaklju&#269;ana.');
DEFINE('_POST_LOCK_UNSET','Tema je sada otklju&#269;ana.');
DEFINE('_POST_LOCK_NOT_UNSET','Tema nije otklju&#269;ana.');
DEFINE('_POST_MESSAGE','Slanje nove poruke u ');
DEFINE('_POST_MOVE_TOPIC','Prebacivanje ove Teme u forum ');
DEFINE('_POST_NEW','Nova poruka u: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','Va&#154;a pretplata za ovu temu nije izvr&#154;ena.');
DEFINE('_POST_NOTIFIED','Popunite ovu ku&#263;icu ako &#382;elite primati obavijesti o novim porukama u ovoj temi.');
DEFINE('_POST_STICKY_SET','Ova tema je sada usidrena.');
DEFINE('_POST_STICKY_NOT_SET','Tema nije usidrena.');
DEFINE('_POST_STICKY_UNSET','Ova tema je sada odsidrena.');
DEFINE('_POST_STICKY_NOT_UNSET','Tema nije odsidrena.');
DEFINE('_POST_SUBSCRIBE','Pretplata');
DEFINE('_POST_SUBSCRIBED_TOPIC','Sada ste pretpla&#263;eni na ovu temu.');
DEFINE('_POST_SUCCESS','Va&#154;a poruka je uspje&#154;no');
DEFINE('_POST_SUCCES_REVIEW','Va&#154;a poruka je uspje&#154;no poslana.  Moderator &#263;e je pregledati prije objave na forumu.');
DEFINE('_POST_SUCCESS_REQUEST','Va&#154; zahtjev je obra&#273;en.  Ako se ne vratite na temu za nekoliko trenutaka,');
DEFINE('_POST_TOPIC_HISTORY','Pregled Teme');
DEFINE('_POST_TOPIC_HISTORY_MAX','Maks. prikaz zadnjih');
DEFINE('_POST_TOPIC_HISTORY_LAST','  -  <i>(Zadnji post na vrhu)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','Va&#154;a tema NIJE preba&#269;ena. Za povratak na temu:');
DEFINE('_POST_TOPIC_FLOOD1','Administrator ovog foruma je uklju&#269;io Flood Za&#154;titu, &#154;to zna&#269;i da morate pri&#269;ekati ');
DEFINE('_POST_TOPIC_FLOOD2',' sekundi izme&#273;u slanja poruka.');
DEFINE('_POST_TOPIC_FLOOD3','Za povratak na forum, kliknite <i>back</i> dugme va&#154;eg preglednika.');
DEFINE('_POST_EMAIL_NEVER','va&#154;a email adresa nikada ne&#263;e biti prikazana na forumu.');
DEFINE('_POST_EMAIL_REGISTERED','va&#154;a email adresa &#263;e biti dostupna samo registriranim korisnicima.');
DEFINE('_POST_LOCKED','zaklju&#269;ao administrator.');
DEFINE('_POST_NO_NEW','Novi odgovori nisu dozvoljeni.');
DEFINE('_POST_NO_PUBACCESS1','Pisanje je dozvoljeno samo registriranim korisnicima.');
DEFINE('_POST_NO_PUBACCESS2','Mo&#382;ete pregledavati Forum ali samo registrirani<br/>korisnici mogu pisati poruke.');


// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','>> Jo&#154; nema tema u ovom forumu <<');
DEFINE('_SHOWCAT_PENDING','nepro&#269;itane poruke');


// userprofile.php
DEFINE('_USER_DELETE',' izbrisati potpis');
DEFINE('_USER_ERROR_A','Na ovu stranicu ste stigli gre&#154;kom. Molimo, informirajte administratora na koje linkove ');
DEFINE('_USER_ERROR_B','ste kliknuli da bi se na&#154;li ovdje.');
DEFINE('_USER_ERROR_C','Hvala!');
DEFINE('_USER_ERROR_D','Broj gre&#154;ke koji treba uklju&#269;iti u izvje&#154;taj: ');
DEFINE('_USER_GENERAL','Profil :: Opcije');
DEFINE('_USER_MODERATOR','Vi ste Moderator ovih foruma');
DEFINE('_USER_MODERATOR_NONE','Nema foruma dodijeljenih vama');
DEFINE('_USER_MODERATOR_ADMIN','Administrator je moderator svih foruma.');
DEFINE('_USER_NOSUBSCRIPTIONS','Nema');
DEFINE('_USER_PREFERED','Prikaz');
DEFINE('_USER_PROFILE','Profil - ');
DEFINE('_USER_PROFILE_NOT_A','Va&#154; profil ');
DEFINE('_USER_PROFILE_NOT_B','nije');
DEFINE('_USER_PROFILE_NOT_C',' a&#382;uriran.');
DEFINE('_USER_PROFILE_UPDATED','Va&#154; profil je a&#382;uriran.');
DEFINE('_USER_RETURN_A','Ako se ne vratite na profil za nekoliko trenutaka ');
DEFINE('_USER_RETURN_B','kliknite ovdje');
DEFINE('_USER_SUBSCRIPTIONS','Va&#154;e Pretplate');
DEFINE('_USER_UNSUBSCRIBE','Otka&#382;i');
DEFINE('_USER_UNSUBSCRIBE_A','Va&#154;a pretplata ');
DEFINE('_USER_UNSUBSCRIBE_B','nije');
DEFINE('_USER_UNSUBSCRIBE_C',' otkazana.');
DEFINE('_USER_UNSUBSCRIBE_YES','Va&#154;a pretplata je otkazana.');
DEFINE('_USER_DELETEAV',' brisanje va&#154;eg Avatara');

//New 0.9 to 1.0
DEFINE('_USER_ORDER','Redoslijed');
DEFINE('_USER_ORDER_DESC','Zadnja poruka na vrh');
DEFINE('_USER_ORDER_ASC','Prva poruka na vrh');


// view.php
DEFINE('_VIEW_DISABLED','Pisanje je dopu&#154;teno samo registriranim korisnicima.');
DEFINE('_VIEW_POSTED','Post');
DEFINE('_VIEW_SUBSCRIBE',':: Pretplatite se na obavijesti o novim porukama u ovoj temi ::');
DEFINE('_MODERATION_INVALID_ID','Neispravan ID.');
DEFINE('_VIEW_NO_POSTS','Nema poruka u ovom forumu.');
DEFINE('_VIEW_VISITOR','Gost');
DEFINE('_VIEW_ADMIN','Admin');
DEFINE('_VIEW_USER','Korisnik');
DEFINE('_VIEW_MODERATOR','Moderator');
DEFINE('_VIEW_REPLY','Odgovor na ovu poruku');
DEFINE('_VIEW_EDIT','Ure&#273;ivanje ove poruke');
DEFINE('_VIEW_QUOTE','Citiranje poruke u novom postu');
DEFINE('_VIEW_DELETE','Brisanje poruke');
DEFINE('_VIEW_STICKY','Usidri temu');
DEFINE('_VIEW_UNSTICKY','Odsidri temu');
DEFINE('_VIEW_LOCK','Zaklju&#269;aj temu');
DEFINE('_VIEW_UNLOCK','Otklju&#269;aj temu');
DEFINE('_VIEW_MOVE','Prebacivanje teme u drugi forum');
DEFINE('_VIEW_SUBSCRIBETXT','Pretplatite se na ovu temu i primajte email obavijesti o novim porukama.');


//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME','Forum');
DEFINE('_POSTS','Postova:');
DEFINE('_TOPIC_NOT_ALLOWED','Post');
DEFINE('_FORUM_NOT_ALLOWED','Forum');
DEFINE('_FORUM_IS_OFFLINE','Forum je OFFLINE!');
DEFINE('_PAGE','Stranica: ');
DEFINE('_NO_POSTS','Nema Poruka');
DEFINE('_CHARS','znakova maks.');
DEFINE('_HTML_YES','HTML je isklju&#269;en');
DEFINE('_YOUR_AVATAR','<b>Va&#154; Avatar</b>');
DEFINE('_NON_SELECTED','Jo&#154; nije odabran <br>');
DEFINE('_SET_NEW_AVATAR','Odabir novog Avatara');
DEFINE('_THREAD_UNSUBSCRIBE','Otka&#382;i');
DEFINE('_SHOW_LAST_POSTS','Teme aktivne u zadnjih');
DEFINE('_SHOW_HOURS','sati');
DEFINE('_SHOW_POSTS','Ukupno: ');
DEFINE('_DESCRIPTION_POSTS','Najnoviji postovi iz aktivnih tema');
DEFINE('_SHOW_4_HOURS','4 Sata');
DEFINE('_SHOW_8_HOURS','8 Sati');
DEFINE('_SHOW_12_HOURS','12 Sati');
DEFINE('_SHOW_24_HOURS','24 Sata');
DEFINE('_SHOW_48_HOURS','48 Sati');
DEFINE('_SHOW_WEEK','Tjedan');
DEFINE('_POSTED_AT','Poslano');
DEFINE('_DATETIME','d.m.y H:i');
DEFINE('_NO_TIMEFRAME_POSTS','Nema novih postova u vremenskom intervalu koji ste odabrali.');
DEFINE('_MESSAGE','Poruka');
DEFINE('_NO_SMILIE','ne');
DEFINE('_FORUM_UNAUTHORIZIED','Ovaj forum je otvoren samo registriranim i prijavljenim korisnicima.');
DEFINE('_FORUM_UNAUTHORIZIED2','Ako ste ve&#263; registrirani, prijavite se.');
DEFINE('_MESSAGE_ADMINISTRATION','Moderiranje');
DEFINE('_MOD_APPROVE','Odobri');
DEFINE('_MOD_DELETE','Izbri&#154;i');


//NEW in RC1
DEFINE('_SHOW_LAST','Najnovija poruka');
DEFINE('_POST_WROTE','napisao');
DEFINE('_COM_A_EMAIL','Email Adresa');
DEFINE('_COM_A_EMAIL_DESC','Ovo je glavna adresa foruma.');
DEFINE('_COM_A_WRAP','Rastavi Rije&#269;i Dulje Od');
DEFINE('_COM_A_WRAP_DESC','Unesite maksimalni broj znakova jedne rije&#269;i. Ovime se sprje&#269;ava deformacija izgleda foruma kod kori&#154;tenja vrlo dugih rije&#269;i.<br/> 70 znakova je mogu&#263;i maksimum za predlo&#154;ke fiksne &#154;irine, ali mo&#382;da &#263;ete morati malo eksperimentirati.<br/>URL, bez obzira koliko je dug, ne&#263;e biti rastavljen.');
DEFINE('_COM_A_SIGNATURE','Maksimalna Duljina Potpisa');
DEFINE('_COM_A_SIGNATURE_DESC','Maksimalni broj znakova dozvoljen u potpisu korisnika.');
DEFINE('_SHOWCAT_NOPENDING','Nema novih privatnih poruka');
DEFINE('_COM_A_BOARD_OFSET','Vremenska Razlika');
DEFINE('_COM_A_BOARD_OFSET_DESC','Neki forumi su locirani na serverima u vremenskoj zoni razli&#269;itoj od korisnikove. Podesite razliku ovdje. Mo&#382;ete koristiti pozitivne i negativne brojeve.');


//New in RC2
DEFINE('_COM_A_BASICS','Osnove');
DEFINE('_COM_A_FRONTEND','Prezentacija');
DEFINE('_COM_A_SECURITY','Sigurnost');
DEFINE('_COM_A_AVATARS','Avatari');
DEFINE('_COM_A_INTEGRATION','Integracije');
DEFINE('_COM_A_PMS','Integracija Privatnih Poruka');
DEFINE('_COM_A_PMS_DESC','Odaberite odgovaraju&#263;u opciju za slanje Privatnih Poruka, ako je imate instaliranu. Clexus PM &#263;e omogu&#263;iti i opcije iz ClexusPM korisni&#269;kog profila (npr. ICQ, AIM, Yahoo, MSN i linkove na profil ako su podr&#382;ani u va&#154;em Fireboard predlo&#154;ku).');
DEFINE('_VIEW_PMS','Kliknite ovdje ako &#382;elite poslati Privatnu Poruku ovom korisniku.');


//new in RC3
DEFINE('_POST_RE','Odgovor:');
DEFINE('_BBCODE_BOLD','Podebljano: [b]tekst[/b] ');
DEFINE('_BBCODE_ITALIC','Kurziv: [i]tekst[/i]');
DEFINE('_BBCODE_UNDERL','Podcrtano: [u]tekst[/u]');
DEFINE('_BBCODE_QUOTE','Citat: [quote]tekst[/quote]');
DEFINE('_BBCODE_CODE','Kod: [code]neki kod[/code]');
DEFINE('_BBCODE_ULIST','Lista: [ul] [li]text[/li] [/ul] - Napomena: lista mora imati stavke');
DEFINE('_BBCODE_OLIST','Broj&#269;ana Lista: [ol] [li]text[/li] [/ol] - Napomena: lista mora imati stavke');
DEFINE('_BBCODE_IMAGE','Slika: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK','Link: [url=http://www.zzz.com/]Ovo je link[/url]');
DEFINE('_BBCODE_CLOSA','Zatvori sve tagove');
DEFINE('_BBCODE_CLOSE','Zatvori sve otvorene bbkod tagove');
DEFINE('_BBCODE_COLOR','Boja: [color=#FF6600]tekst[/color]');
DEFINE('_BBCODE_SIZE','Veli&#269;ina: [size=1]veli&#269;ina slova[/size] - Napomena: veli&#269;ine mogu biti od 1 do 5');
DEFINE('_BBCODE_LITEM','Stavka: [li] stavka na listi [/li]');
DEFINE('_BBCODE_HINT','bbkod Pomo&#263; - Napomena: bbkod se mo&#382;e upotrebljavati i na ozna&#269;enom tekstu!');
DEFINE('_COM_A_TAWIDTH','&#138;irina Tekst Polja');
DEFINE('_COM_A_TAWIDTH_DESC','Prilagodite &#154;irinu Tekst Polja va&#154;em predlo&#154;ku.');
DEFINE('_COM_A_TAHEIGHT','Visina Tekst Polja');
DEFINE('_COM_A_TAHEIGHT_DESC','Prilagodite visinu Tekst Polja va&#154;em predlo&#154;ku.');
DEFINE('_COM_A_ASK_EMAIL','Obavezan Email');
DEFINE('_COM_A_ASK_EMAIL_DESC','Postavite na &quot;Ne&quot; ako &#382;elite da gosti mogu pisati poruke bez uno&#154;enja svoje email adrese.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Upravljanje statusima');
define('_KUNENA_SORTRANKS', 'Sortiraj po statusu');

define('_KUNENA_RANKSIMAGE', 'Slika statusa');
define('_KUNENA_RANKS', 'Naziv statusa');
define('_KUNENA_RANKS_SPECIAL', 'Special');
define('_KUNENA_RANKSMIN', 'Minimalan broj postova');
define('_KUNENA_RANKS_ACTION', 'Actions');
define('_KUNENA_NEW_RANK', 'Novi status');

DEFINE ('_KUNENA_GO','TRAŽI');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'Posjetitelja');
DEFINE('_KUNENA_CHILD_BOARDS', 'Podkategorije ');
DEFINE('_KUNENA_GUEST', 'Gost');
DEFINE('_KUNENA_FORUM_TOP', 'Kategorije ');
DEFINE('_KUNENA_TOTALFAVORITE', 'Omiljeno:  ');
DEFINE ('_KUNENA_FORUMTOOLS','Alati foruma');
DEFINE ('_KUNENA_REPORT_LOGGED','Prijavljen');
DEFINE('_KUNENA_TOPIC', 'TEMA: ');
DEFINE('_KUNENA_TIME_SINCE', 'Prije %time%');
DEFINE('_KUNENA_DATE_YEARS', 'godina');
DEFINE('_KUNENA_DATE_MONTHS', 'mjeseci');
DEFINE('_KUNENA_DATE_WEEKS','tjedana');
DEFINE('_KUNENA_DATE_DAYS', 'dana');
DEFINE('_KUNENA_DATE_HOURS', 'sati');
DEFINE('_KUNENA_DATE_MINUTES', 'minuta');
DEFINE('_KUNENA_DATE_YEAR', 'godina');
DEFINE('_KUNENA_DATE_MONTH', 'mjesec');
DEFINE('_KUNENA_DATE_WEEK','tjedan');
DEFINE('_KUNENA_DATE_DAY', 'dan');
DEFINE('_KUNENA_DATE_HOUR', 'sat');
DEFINE('_KUNENA_DATE_MINUTE', 'minuta');
DEFINE('_KUNENA_POWEREDBY', '');
DEFINE('_KUNENA_EDIT_TITLE', 'Ure&#273;ivanje detalja');
DEFINE('_KUNENA_EDIT_TITLE', 'Uredi svoje detalje');
DEFINE('_KUNENA_YOUR_NAME', 'Ime:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'Korisni&#269;ko ime:');
DEFINE('_KUNENA_PASS', '&#138;ifra:');
DEFINE('_KUNENA_VPASS', 'Potvrdi &#154;ifru:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Korisni&#269;ki detalji spremljeni.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Najnovije');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Moje teme');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Teme koje sam pokrenuo ili u njima sudjelujem');
DEFINE('_KUNENA_CATEGORY', 'Kategorija:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorije');
DEFINE('_KUNENA_POSTED_AT', 'Otvoreno');
DEFINE('_KUNENA_AGO', 'prije');
DEFINE('_KUNENA_DISCUSSIONS', 'Rasprave');
DEFINE('_KUNENA_TOTAL_THREADS', 'Ukupno tema:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'Mjesec');
DEFINE('_SHOW_YEAR', 'Godina');




?>
