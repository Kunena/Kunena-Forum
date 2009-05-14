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
* @author ArMyBoT, faber
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Abilita evidenziazione del codice'); //Enable Code Highlighting
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Abilita l\'evidenziazione del codice sorgente. I frammenti di codice inseriti tra i tag "code", con questa funzione abilitata, saranno colorati. Se non hai bisogno di questa funzione, disabilitala.');  //Enables the Kunena code tag highlighting java script. If your members post php and similar code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from getting malformed.
DEFINE('_COM_A_RSS_TYPE', 'Tipologia RSS di default'); //Default RSS type
DEFINE('_COM_A_RSS_TYPE_DESC', 'Scegli tra feed RSS per post o per discussione.  Per discussione si avr&#224; un feed pi&#249; compatto ma non riporter&#224; tutte le risposte del thread.'); //Choose between RSS feeds by thread or post. By thread means that only one entry per thread will be listed in the RSS feed, independet of how many posts have been made within that thread. By thread creates a shorter more compact RSS feed but will not list every reply made.
DEFINE('_COM_A_RSS_BY_THREAD', 'Per discussione');  //By Thread
DEFINE('_COM_A_RSS_BY_POST', 'Per messaggio'); //By Post
DEFINE('_COM_A_RSS_HISTORY', 'Storico RSS'); //RSS History
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Seleziona quanto mantenere come storico RSS. Il default &#232; 1 mese ma si consiglia di ridurre ad 1 settimana in caso di alto volume di informazioni');  //Select how much history should be included in the RSS feed. Default is 1 month but you might want to limit it to 1 week on high volume sites.
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Settimana'); //1 Week
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Mese'); //1 Month
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Anno'); //1 Year
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Pagina di default di Kunena'); //Default Kunena Page
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Seleziona la pagina di default che verr&#224; visualizzata. Di default &#232; la pagina con le discussioni recenti. Dovrebbe essere impostata su Categorie per i temi diversi da default_ex. Se hai selezionato Le Mie Discussioni, i visitatori avranno come pagina di default le discussioni recenti.');  //Select the default Kunena page that gets displayed when a forum link is clicked or the forum is entered initially. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Discussioni recenti'); //Recent Discussions
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Le mie discussioni');  //My Discussions
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categorie'); //Categories
DEFINE('_KUNENA_BBCODE_HIDE', 'Quanto segue non &#232; visibile agli utenti non registrati:'); //The following is hidden from non registered users:
DEFINE('_KUNENA_BBCODE_SPOILER', 'Attention, spoiler (trame di film, libri,...)!'); //Warning Spoiler!
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Il forum padre non deve essere lo stesso.'); //Parent Forum must not be the same.
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Il forum padre &#232; uno dei propri forum figli.'); //Parent Forum is one of its own childs.
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID non esiste.'); //Forum ID does not exist.
DEFINE('_KUNENA_RECURSION', 'Rilevata ricorsione.'); //Recursion detected.
DEFINE('_POST_FORGOT_NAME_ALERT', 'Hai dimenticato di inserire il tuo nome.'); //You forgot to enter your name.
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Hai dimenticato di inserire la tua mail.');  //You forgot to enter your email.
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Hai dimenticati di inserire l\'oggetto.'); //You forgot to enter a subject.
DEFINE('_KUNENA_EDIT_TITLE', 'Modifica le tue informazioni'); //Edit Your Details
DEFINE('_KUNENA_YOUR_NAME', 'Il tuo nome:'); //Your Name:
DEFINE('_KUNENA_EMAIL', 'e-mail:'); //e-mail:
DEFINE('_KUNENA_UNAME', 'Nome utente:'); //User Name:
DEFINE('_KUNENA_PASS', 'Password:');  //Password:
DEFINE('_KUNENA_VPASS', 'Verifica Password:'); //Verify Password:
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Le informazioni utente sono state salvate.'); //User details have been saved.
DEFINE('_KUNENA_TEAM_CREDITS', 'Crediti'); //Credits
DEFINE('_COM_A_BBCODE', 'BBCode');  //BBCode
DEFINE('_COM_A_BBCODE_SETTINGS', 'Impostazioni BBCode');  //BBCode Settings
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trimm long URLs');
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
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Unisci');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Unione con questo thread');
DEFINE('_POST_MERGE_GHOST', 'Lascia una copia del thread');
DEFINE('_POST_SUCCESS_MERGE', 'Thread unito con successo.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Unione non riuscita.');
DEFINE('_GEN_SPLIT', 'Dividi');
DEFINE('_GEN_DOSPLIT', 'Vai');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Thread diviso con successo.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Topic cambiato con successo.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Cambiamento Topic non riuscito.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Divisione non riuscita.');
DEFINE('_POST_DUPLICATE_IGNORED', 'La duplicazione del messaggio &#232; stata ignorata.');
DEFINE('_POST_SPLIT_HINT', '<br />Suggerimento: &#232; possibile promuovere un post di un topic selezionanado la seconda colonna e non abilitando alcuna divisione.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link al topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link al post del nuovo topic.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link al precedente post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link al precedente post.');
DEFINE('_POST_MERGE', 'merge');
DEFINE('_POST_MERGE_TITLE', 'Fissare gli obiettivi del primo post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Fissare gli obiettivi al primo post di questo thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Questo thread &#232; stato rimosso dai tuoi preferiti.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Questo thread <b>NON</b> &#232; stato rimosso dai tuoi preferiti');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'La tua richiesta di eliminazione dai tuoi preferiti &#232; stata compiuta.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Questo thread &#232; stato rimosso dal tuo abbonamento.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Questo thread <b>NON</b> &#232; stato rimosso dal tuo abbonamento');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'La tua richiesta di eliminazione dal tuo abbonamento &#232; stata compiuta.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Non &#232; stata selezionata la categoria di destinazione. Non &#232; stato spostato nulla.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Discussioni recenti');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Le mie discussioni');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussioni che ho aperto io o nelle quali ho partecipato');
DEFINE('_KUNENA_CATEGORY', 'Categoria:');
DEFINE('_KUNENA_CATEGORIES', 'Categorie');
DEFINE('_KUNENA_POSTED_AT', 'Inviato');
DEFINE('_KUNENA_AGO', 'fa');
DEFINE('_KUNENA_DISCUSSIONS', 'Discussioni');
DEFINE('_KUNENA_TOTAL_THREADS', 'Thread totali:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'Mese');
DEFINE('_SHOW_YEAR', 'Anno');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Copying "%src%" to "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Il salvataggio del css file si trova a...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Attachment table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Attachments in messages table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Could not delete the post(s) - nothing else deleted');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum as well");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena component was successfully uninstalled!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena Forum Component version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generated: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'No forums to search in.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Error adding users:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Users syncronized; Deleted:');
DEFINE('_KUNENA_USERSSYNCADD', ', Aggiungi:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'Profilo utente.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Nessun profilo ammissibile.');
DEFINE('_KUNENA_SYNC_USERS', 'Syncronize Users');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Sync Kunena user table with Joomla! user table');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Email Administrators');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Modifica Rank');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Nascondi Email');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Domenica');
DEFINE('_KUNENA_DT_LDAY_MON', 'Luned&#236;');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Marted&#236;');
DEFINE('_KUNENA_DT_LDAY_WED', 'Mercoled&#236;');
DEFINE('_KUNENA_DT_LDAY_THU', 'Gioved&#236;');
DEFINE('_KUNENA_DT_LDAY_FRI', '&#236;enerd&#236;');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Sabato');
DEFINE('_KUNENA_DT_DAY_SUN', 'Dom');
DEFINE('_KUNENA_DT_DAY_MON', 'Lun');
DEFINE('_KUNENA_DT_DAY_TUE', 'Mar');
DEFINE('_KUNENA_DT_DAY_WED', 'Mer');
DEFINE('_KUNENA_DT_DAY_THU', 'Gio');
DEFINE('_KUNENA_DT_DAY_FRI', 'Ven');
DEFINE('_KUNENA_DT_DAY_SAT', 'Sab');
DEFINE('_KUNENA_DT_LMON_JAN', 'Gennaio');
DEFINE('_KUNENA_DT_LMON_FEB', 'Febbraio');
DEFINE('_KUNENA_DT_LMON_MAR', 'Marzo');
DEFINE('_KUNENA_DT_LMON_APR', 'Aprile');
DEFINE('_KUNENA_DT_LMON_MAY', 'Maggio');
DEFINE('_KUNENA_DT_LMON_JUN', 'Giugno');
DEFINE('_KUNENA_DT_LMON_JUL', 'Luglio');
DEFINE('_KUNENA_DT_LMON_AUG', 'Agosto');
DEFINE('_KUNENA_DT_LMON_SEP', 'Settembre');
DEFINE('_KUNENA_DT_LMON_OCT', 'Ottobre');
DEFINE('_KUNENA_DT_LMON_NOV', 'Novembre');
DEFINE('_KUNENA_DT_LMON_DEV', 'Dicembre');
DEFINE('_KUNENA_DT_MON_JAN', 'Gen');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'Mag');
DEFINE('_KUNENA_DT_MON_JUN', 'Giu');
DEFINE('_KUNENA_DT_MON_JUL', 'Lug');
DEFINE('_KUNENA_DT_MON_AUG', 'Ago');
DEFINE('_KUNENA_DT_MON_SEP', 'Set');
DEFINE('_KUNENA_DT_MON_OCT', 'Ott');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dic');
DEFINE('_KUNENA_CHILD_BOARD', 'Child Board');
DEFINE('_WHO_ONLINE_GUEST', 'Ospite');
DEFINE('_WHO_ONLINE_MEMBER', 'Stato');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'Nessuno');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Clicca qui per continuare...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Applicare!');
DEFINE('_KUNENA_NO_ACCESS', 'Non hai accesso a questo Forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% fa');
DEFINE('_KUNENA_DATE_YEARS', 'Anni');
DEFINE('_KUNENA_DATE_MONTHS', 'Mesi');
DEFINE('_KUNENA_DATE_WEEKS','Settimane');
DEFINE('_KUNENA_DATE_DAYS', 'Giorni');
DEFINE('_KUNENA_DATE_HOURS', 'Ore');
DEFINE('_KUNENA_DATE_MINUTES', 'Minuti');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Sei sicuro di voler rimuovere il campione dei dati? Questa azione &#232; irreversibile.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumheader:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum display");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Modifica tempo utente');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Impostare a 0 per tempo illimitato, altrimenti impostare in secondi la finestra per consentire l&#180;ultimo aggiornamento.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], permette si memorizzare una modifica fino a 600 secondi dopo il link Modifica scompare');
DEFINE('_KUNENA_HELPPAGE','Abilita la pagina di Help');
DEFINE('_KUNENA_HELPPAGE_DESC','Se impostato a &quot;Si&quot; un link nel menu di intestazione sar&#224; mostrato nalla pagina di Help.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Mostra help in Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Se si imposta su &quot;Si&quot; l&#180;help sar&#224; incluso nella Guida ed il link alla pagina Help esterna non sar&#224; attivo. <b>Nota:</b> Si dovrebbe aggiungere "Help Contenuto ID" .');
DEFINE('_KUNENA_HELPPAGE_CID','Help Contenuto ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','E&#180; necessario impostare <b>"SI"</b> su "Mostra help in Kunena".');
DEFINE('_KUNENA_HELPPAGE_LINK',' Help link pagina esterna');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Se si mostra help link pagina esterna, si prega di impostare <b>"NO"</b> in "Mostra help in Kunena".');
DEFINE('_KUNENA_RULESPAGE','Abilita la pagina delle regole');
DEFINE('_KUNENA_RULESPAGE_DESC','Se impostato su &quot;Si&quot; sar&#224; visibile un link nel menu di intestazione alla pagina delle regole.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Mostra regole in Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Se si imposta su &quot;Si&quot; le regole saranno incluse in Kunena ed il link alla pagina esterna delle regole non sar&#224; attivo. <b>Note:</b> Si dovrebbe aggiungere "ID del contenuto delle regole" .');
DEFINE('_KUNENA_RULESPAGE_CID','ID del contenuto delle regole');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','E&#180; necessario impostare <b>"SI"</b>su "Abilita la pagina delle regole".');
DEFINE('_KUNENA_RULESPAGE_LINK',' Link alla pagina esterna delle regole');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Se si mostra il link alla pagina esterna delle regole, si prega di impostare <b>"NO"</b>su "Mostra regole in Kunena". ');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library non trovata');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Library non trovata');
DEFINE('_KUNENA_GD_INSTALLED','GD is avabile version ');
DEFINE('_KUNENA_GD_NO_VERSION','Non si riesce ad individuare kla versione di GD');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD non &#232; installato, &#232; possibile ottenere ulteriori informazioni ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Altezza immagine piccola :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Ampiezza immagine piccola :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Altezza immagine media :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Altezza immagine media :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Altezza immagine grande :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Altezza immagine grande :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar Quality');
DEFINE('_KUNENA_WELCOME','Benvenuto in Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','	Grazie per aver scelto il vostro bordo Kunena come soluzione. Questa schermata vi dar&#224; una rapida panoramica di tutte le varie statistiche della vostra pensione. I link sulla sinistra di questa schermata consente di controllare ogni aspetto della tua esperienza di bordo. Ogni pagina avr&#224; le istruzioni su come utilizzare gli strumenti.');
DEFINE('_KUNENA_STATISTIC','Statistica');
DEFINE('_KUNENA_VALUE','Valore');
DEFINE('_GEN_CATEGORY','Categoria');
DEFINE('_GEN_STARTEDBY','Creato da: ');
DEFINE('_GEN_STATS','statistiche');
DEFINE('_STATS_TITLE',' forum - statistiche');
DEFINE('_STATS_GEN_STATS','Statistiche generali');
DEFINE('_STATS_TOTAL_MEMBERS','Membri:');
DEFINE('_STATS_TOTAL_REPLIES','Risposte:');
DEFINE('_STATS_TOTAL_TOPICS','Discussioni:');
DEFINE('_STATS_TODAY_TOPICS','Discussioni di oggi:');
DEFINE('_STATS_TODAY_REPLIES','Le risposte di oggi:');
DEFINE('_STATS_TOTAL_CATEGORIES','Categorie:');
DEFINE('_STATS_TOTAL_SECTIONS','Sezioni:');
DEFINE('_STATS_LATEST_MEMBER','Ultimo utente registrato:');
DEFINE('_STATS_YESTERDAY_TOPICS','Discussioni di ieri:');
DEFINE('_STATS_YESTERDAY_REPLIES','Risposte di ieri:');
DEFINE('_STATS_POPULAR_PROFILE','I profili pi&#249; visti');
DEFINE('_STATS_TOP_POSTERS','Chi scrive di pi&#249;');
DEFINE('_STATS_POPULAR_TOPICS','Le discussioni pi&#249; popolari');
DEFINE('_COM_A_STATSPAGE','Abilita la pagina delle statistiche');
DEFINE('_COM_A_STATSPAGE_DESC','Se configurato a &quot;Si&quot; verr&#224; mostrato un link pubblico alla pagina delle statistiche nel menu intestazione.<em>Le pagine di statistiche saranno comunque presenti per gli amministratori!</em>');
DEFINE('_COM_C_JBSTATS','Statistiche forum');
DEFINE('_COM_C_JBSTATS_DESC','Statistiche Forum');
define('_GEN_GENERAL','Generale');
define('_PERM_NO_READ','Non hai i permessi sufficienti per accedere al forum.');
DEFINE ('_KUNENA_SMILEY_SAVED','Smile salvato');
DEFINE ('_KUNENA_SMILEY_DELETED','Smile cancellato');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Il codice gi&#224; esiste');
DEFINE ('_KUNENA_MISSING_PARAMETER','Parametro mancante');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Rank gi&#224; esistente');
DEFINE ('_KUNENA_RANK_DELETED','Rank cancellato');
DEFINE ('_KUNENA_RANK_SAVED','Rank salvato');
DEFINE ('_KUNENA_DELETE_SELECTED','Cancella i selezionati');
DEFINE ('_KUNENA_MOVE_SELECTED','Sposta i selezionati');
DEFINE ('_KUNENA_REPORT_LOGGED','Loggato');
DEFINE ('_KUNENA_GO','Vai');
DEFINE('_KUNENA_MAILFULL','Includi il contenuto completo del post nella mail spedita agli iscritti');
DEFINE('_KUNENA_MAILFULL_DESC','Se No - gli iscritti riceveranno solo i titoli dei nuovi messaggi');
DEFINE('_KUNENA_HIDETEXT','Effettua il login per vedere questo contenuto!');
DEFINE('_BBCODE_HIDE','Testo nascosto: [hide]testo da nascondere[/hide] - nasconde parte del testo agli ospiti del forum');
DEFINE('_KUNENA_FILEATTACH','File Allegato: ');
DEFINE('_KUNENA_FILENAME','Nome file: ');
DEFINE('_KUNENA_FILESIZE','Dimensione file: ');
DEFINE('_KUNENA_MSG_CODE','Codice: ');
DEFINE('_KUNENA_CAPTCHA_ON','Sistema anti spam');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA system On/Off');
DEFINE('_KUNENA_CAPDESC','Inserisci il codice');
DEFINE('_KUNENA_CAPERR','Codice errato!');
DEFINE('_KUNENA_COM_A_REPORT', 'Message Reporting');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Per consentire agli utenti di segnalare dei messaggi agli amministratori, scegliere si.');
DEFINE('_KUNENA_REPORT_MSG', 'Message Reported');
DEFINE('_KUNENA_REPORT_REASON', 'Motivo');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Il tuo messaggio');
DEFINE('_KUNENA_REPORT_SEND', 'Invia Report');
DEFINE('_KUNENA_REPORT', 'Riporta al moderatore');
DEFINE('_KUNENA_REPORT_RSENDER', 'Mittente del report: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Motivo del report: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Report Message: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Autore del messaggio: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Oggetto del messaggio: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Messaggio: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Message Link: ');
DEFINE('_KUNENA_REPORT_INTRO', 'ti &#232; stato spedito un report perch&#232;');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Report inviato con successo!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Codice');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Modifica Smiley');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Modifica Smilies');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nuovo Smiley');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Altri smile');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Chiudi finestra');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Emoticons aggiuntivi');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Prendi uno smile');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Abilita l&#180;assistenza di Joomla Mambot');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Impostazioni del My Profile Plugin');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Consenti cambio username');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Consenti cambiamento username sul My profile plugin');
DEFINE ('_KUNENA_RECOUNTFORUMS','Rigenera le statistiche delle categorie');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Le statistiche delle categorie sono state rigenerate.');
DEFINE ('_KUNENA_EDITING_REASON','Motivo della modifica');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Ultima modifica');
DEFINE ('_KUNENA_BY','Da');
DEFINE ('_KUNENA_REASON','motivo');
DEFINE('_GEN_GOTOBOTTOM', 'Vai alla fine');
DEFINE('_GEN_GOTOTOP', 'Vai all\'inizio');
DEFINE('_STAT_USER_INFO', 'Informazioni utente');
DEFINE('_USER_SHOWEMAIL', 'Mostra Email'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Mostra Online');
DEFINE('_KUNENA_HIDDEN_USERS', 'Utenti nascosti');
DEFINE('_KUNENA_SAVE', 'Salva');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Galleria di default');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Informazioni personali');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Sommario');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Il mio Avatar');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Settaggi Forum');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Look e Layout');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Informazioni sul mio profilo');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'I miei interventi');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Le mie iscrizioni');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'I miei preferiti');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Messaggistica privata');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'In arrivo');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Nuovo Messaggio');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Inviata');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Cestino');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Settings');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Contatti');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Lista bloccati');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Informazioni aggiuntive');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Nome');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Nome utente');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Email');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Tipo utente');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Data registrazione');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Data ultima visita');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'interventi');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Vista profilo');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Testo personale');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Gender');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Data di nascita');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Anno (AAAA) - Mese (MM) - Giorno (GG)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Location');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Il tuo ICQ User-ID.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Il tuo AOL Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Il tuo Yahoo! Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Il tuo username Skype.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Il tuo nickname Gtalk.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Website');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Nome sito web');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Ad esempio Virginio Galizia!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Indirizzo sito web');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Esempio: www.virginiogalizia.it');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Il tuo indirizzo di mail MSN messenger.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Firma');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Uomo');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Donna');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Gli interventi sono stati rimossi con successo');
DEFINE('_KUNENA_DATE_YEAR', 'Anno');
DEFINE('_KUNENA_DATE_MONTH', 'Mese');
DEFINE('_KUNENA_DATE_WEEK','Settimana');
DEFINE('_KUNENA_DATE_DAY', 'Giorno');
DEFINE('_KUNENA_DATE_HOUR', 'Ora');
DEFINE('_KUNENA_DATE_MINUTE', 'Minuto');
DEFINE('_KUNENA_IN_FORUM', ' nel Forum: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum alle: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Nota che sebbene i bottoni dei boardcode e smile non sono mostrati sono comunque utilizzabili');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum Tools');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Lista utenti');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s ha <b>%d</b> utenti registrati');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Inserire il testo da ricercare!');
DEFINE ('_KUNENA_USRL_SEARCH','Trova utente');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Cerca');
DEFINE ('_KUNENA_USRL_LIST_ALL','Elenca tutto');
DEFINE ('_KUNENA_USRL_NAME','Nome');
DEFINE ('_KUNENA_USRL_USERNAME','Nome utente');
DEFINE ('_KUNENA_USRL_GROUP','Gruppo');
DEFINE ('_KUNENA_USRL_POSTS','Interventi');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Hits');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE','Tipo utente');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Data registrazione');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Ultimo login');
DEFINE ('_KUNENA_USRL_NEVER','Mai');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR','Immagini');
DEFINE ('_KUNENA_USRL_ASC','Ascendente');
DEFINE ('_KUNENA_USRL_DESC','Discendente');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Display');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%m/%d/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Plugins');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Lista');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Numero di righe Lista utenti');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Numero di righe Lista utenti');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online Status');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Visualizza gli utenti online');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Display Avatar');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Visualizza Nome Reale');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Mostra Username');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Mostra User Group');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Mostra Numero di Posts');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Mostra Karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Mostra Email');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Mostra User Type');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Mostra Join Date');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Inserisci Data Ultima Visita');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Visualizza Profilo Hits');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Database Wizard');
DEFINE('_KUNENA_DBMETHOD', 'Si prega di selezionare il metodo con il quale si vuole completare l&#180;installazione:');
DEFINE('_KUNENA_DBCLEAN', 'Clean installation');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade da Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Top Level Categoria');
DEFINE('_KUNENA_REGISTERED', 'Registrato');
DEFINE('_KUNENA_PUBLICBACKEND', 'Pubblica Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Seleziona una voce per');
DEFINE('_KUNENA_ERRORSUBS', 'Qualcosa non &#232; andato a buon fine cancellando messaggi ed abbonamenti');
DEFINE('_KUNENA_WARNING', 'Avviso...');
DEFINE('_KUNENA_CHMOD1', 'E&#180; necessario effettuare il chmod 766 per l&#180;aggiornamento del file.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Il tuo file di configurazione &#232;');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Seleziona il Template');
DEFINE('_KUNENA_CONFIGSAVED', 'Configurazione salvata.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'ERRORE FATALE: La configurazione non pu&#243; essere salvata.');
DEFINE('_KUNENA_TFINW', 'Il file non &#232; scrivibile.');
DEFINE('_KUNENA_CFS', 'Kunena CSS file salvato.');
DEFINE('_KUNENA_SELECTMODTO', 'Selezionare un moderatore');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'E&#180; necessario scegliere un forum');
DEFINE('_KUNENA_DELMSGERROR', 'Eliminazione di messaggi fallita:');
DEFINE('_KUNENA_DELMSGERROR1', 'Eliminazione messaggi di testo fallita:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Clearing abbonamento fallita:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned for');
DEFINE('_KUNENA_PRUNEDAYS', 'giorni');
DEFINE('_KUNENA_PRUNEDELETED', 'Eliminato:');
DEFINE('_KUNENA_PRUNETHREADS', 'threads');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Error pruning users:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Utenti ridotti; Eliminati:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'Pofilo utente'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Nessun profilo trovato per la riduzione.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Tabelle Kunena aggiornate alla versione');
DEFINE('_KUNENA_FORUMCATEGORY', 'Categorie Forum');
DEFINE('_KUNENA_SAMPLWARN1', '-- Assicurarsi che i dati di esempio siano stati caricati sulle tabelle vuote di Kunena, in caso contrario esso non funzioner&#224;!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Esempio Post 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Esempio Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Esempio Post[/color][/size][/b]\nCongratulazioni per il nuovo Forum!\n\n[url=http://Kunena.com]- Kunena[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Dati di esempio caricati');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Dati di esempio rimossi');
DEFINE('_KUNENA_CBADDED', 'Profilo Community Builder aggiunto');
DEFINE('_KUNENA_IMGDELETED', 'Immagine eliminata');
DEFINE('_KUNENA_FILEDELETED', 'File eliminato');
DEFINE('_KUNENA_NOPARENT', 'No Parent');
DEFINE('_KUNENA_DIRCOPERR', 'Errore: File');
DEFINE('_KUNENA_DIRCOPERR1', 'Non pu&#243; essere copiato!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> Component <em>for Joomla! </em> <br />&copy; 2008 - 2009 by www.Kunena.com<br />All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Impostazioni Profilo');
DEFINE('_KUNENA_FORUMPRF', 'Profilo');
DEFINE('_KUNENA_FORUMPRRDESC', 'Se avete installato Clexus PM o Community Builder &#232; possibile configurare Kunena per utilizzare il profilo utente.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profilo');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Visualizza profilo</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Tutti i messaggi del Forum');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Topics');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Iniziato da');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Categorie');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Data');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Visualizzazioni');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Nessun intervento sul forum');
DEFINE('_KUNENA_TOTALFAVORITE', 'Preferiti:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Number of child board columns  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Number of child board column formating under main category ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Post-abbonamento impostato per default?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Scegliere "Si" se volete impostare l&#180;abbonamento sempre controllato');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Categoria / Forum deve avere un nome');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Viasualizza Statistiche');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Se vuoi visualizzare le statistiche, seleziona Si');
DEFINE('_KUNENA_SHOWWHOIS', 'Visualizza chi &#232; Online');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Se vuoi visualizzare chi &#232; Online, seleziona Si');
DEFINE('_KUNENA_STATSGENERAL', 'Visualizza statistiche generali');
DEFINE('_KUNENA_STATSGENERALDESC', 'Se vuoi visualizzare le statistiche generali, seleziona Si');
DEFINE('_KUNENA_USERSTATS', 'Visualizza le statistiche utente');
DEFINE('_KUNENA_USERSTATSDESC', 'Se vuoi visualizzare le statistiche utente, seleziona Si');
DEFINE('_KUNENA_USERNUM', 'Numero di utenti pi&#249; popolari');
DEFINE('_KUNENA_USERPOPULAR', 'Mostra le statistiche relative all\'oggetto del messaggio pi&#249; popolare');
DEFINE('_KUNENA_USERPOPULARDESC', 'Se vuoi mostrare le statistiche dell\'oggetto pi&#249; popolare seleziona Si');
DEFINE('_KUNENA_NUMPOP', 'Numero degli oggetti popolari');
DEFINE('_KUNENA_INFORMATION',
    'Il team di Kunena &#232; orgoglioso di annunciare il rilascio di Kunena 1.0.5. Si tratta di un potente ed elegante forum per il content management system, Joomla!. E&#180; basata su Joomlaboard e Fireboard e la maggior parte del nostro merito v&#224; al loro team. Alcue delle caratteristiche principali di Kunena possono essere elencate come segue (in aggiunta alle attuali caratteristiche di JB&#39;s):<br /><br /><ul><li>Un designer pi&#249; amichevole. E&#180; vicino al template SMF ed ha una struttura semplice. Con pochi passi &#232; possibile modificare il look del forum. Un grande grazie va al nostro team di designers.</li><li>Sottocategorei illimitate con una migliore amministrazione del backend.</li><li>Sistema pi&#249; veloce.</li><li>Lo stesso<br /></li><li>Profilebox nella parte superiore del forum</li><li>Supporto per sistemi popolari, quali PM systems, such as ClexusPM and Uddeim</li><li>Sistema di plugin di base</li><li>Lingua definita per le icone di sistema.<br /></li><li>Sistema di condivisione delle immagini con altri templates. E&#180; possibile scegliere tra templates ed una serie di immagini</li><li>E&#180; possibile aggiungere templates di joomla e personali nel forum. Potrete avere banner all&#180; interno del forum?</li><li>threads preferiti di selezione e gestione</li><li>Forum spotlights and highlights</li><li>Pannello di annunci nel forum</li><li>Ultimi messaggi (schede)</li><li>Statistiche in basso</li><li>chi &#232; online, e su quale pagina</li><li>Sistema di immagine specifica per categoria</li><li>pathway rafforzata</li><li><strong>Importazione Joomlaboard</strong></li><li>RSS, PDF output</li><li>Ricerca avanzata (in sviluppo)</li><li>Profilo opzionale su Community builder e Clexus PM</li><li>Avatar di gestione : CB and Clexus PM<br /></li></ul><br />Si prega di tenere presente che questa versione non &#232; pensata per essere usata su siti di produzione, anche se viene testata attraverso di essi. Continueremo a lavorare su questo progetto, e saremmo felici di accogliere chiunque volesse unirsia a noi.<br /><br />Si tratta di un lavoro di collaborazione di diversi sviluppatori e designers che hanno partecipato insieme e che qui ringraziamo tutti!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Istruzioni');
DEFINE('_KUNENA_FINFO', 'Informazioni Kunena Forum');
DEFINE('_KUNENA_CSSEDITOR', 'Modifica Kunena Template CSS');
DEFINE('_KUNENA_PATH', 'Path:');
DEFINE('_KUNENA_CSSERROR', 'Nota:file template CSS deve essere scrivibile per Salavare i cambiamenti.');
// User Management
DEFINE('_KUNENA_FUM', 'Profilo Utente Manager Kunena');
DEFINE('_KUNENA_SORTID', 'ordina per userid');
DEFINE('_KUNENA_SORTMOD', 'ordina per moderatori');
DEFINE('_KUNENA_SORTNAME', 'ordina per nome');
DEFINE('_KUNENA_VIEW', 'Visualizza');
DEFINE('_KUNENA_NOUSERSFOUND', 'Nessun profilo utente trovato.');
DEFINE('_KUNENA_ADDMOD', 'Aggiungi Moderatore');
DEFINE('_KUNENA_NOMODSAV', 'Non sono stati trovati moderatori. Leggi le \'note\' sotto.');
DEFINE('_KUNENA_NOTEUS',
    'NOTA: Solo gli utenti che hanno il flag moderatore impostato nel loro Profilo Kunena Profile sono mostrati qui. Per poter aggiungere un moderatore fornite ad un utente il flag modaratore, vai ad <a href="index2.php?option=com_kunena&task=profiles">User Administration</a> e cerca l&#180;utente che vuoi far diventare moderatore. Quindi seleziona il suo profilo e aggiornalo. Il flag moderatore non pu&#242; essere impostato dall&#180;administratore del sito. ');
DEFINE('_KUNENA_PROFFOR', 'Profilo per');
DEFINE('_KUNENA_GENPROF', 'Opzioni generli del Profilo');
DEFINE('_KUNENA_PREFVIEW', 'Prefered Viewtype:');
DEFINE('_KUNENA_PREFOR', 'Prefered Message Ordering:');
DEFINE('_KUNENA_ISMOD', 'Moderatore:');
DEFINE('_KUNENA_ISADM', '<strong>Si</strong> (non modificabile, questo utente &#232; un (super)administrator del sito)');
DEFINE('_KUNENA_COLOR', 'Colore');
DEFINE('_KUNENA_UAVATAR', 'Utente avatar:');
DEFINE('_KUNENA_NS', 'Nessun selezionato');
DEFINE('_KUNENA_DELSIG', ' Selezionare questa casella per eliminare questa firma');
DEFINE('_KUNENA_DELAV', ' Selezionare questa casella per eliminare questo avatar');
DEFINE('_KUNENA_SUBFOR', 'Abbonamento per');
DEFINE('_KUNENA_NOSUBS', 'Nessun abbonamento per questo utente');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Nozioni di base');
DEFINE('_KUNENA_BASICSFORUM', 'Informazioni di base del Forum');
DEFINE('_KUNENA_PARENT', 'Parent:');
DEFINE('_KUNENA_PARENTDESC',
    'Nota: per creare una Categoria, scegliere \'Top Level Category\' come Parent. Una Categoria funge da contenitore per i Forums.<br />Un Forum pu&#242; essere <strong>solo</strong> creato all&#180;interno di una Categoria selezionandone una precedentemente creata, come per i Parent per il Forum.<br /> I messaggi <strong>NON</strong> possono essere inviati ad una Categoria; ma solo ai Forums.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Nome Forum e descrizione');
DEFINE('_KUNENA_NAMEADD', 'Nome:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Descrizione:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Configurazione avanzata del Forum');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Sicurezza ed accesso al Forum');
DEFINE('_KUNENA_LOCKEDDESC', 'Impostare su &quot;Si&quot; se si desidera bloccare questo Forum a Nessuno, eccetto Moderatori ed Ammnistrazione i quali possono creare nuovi argomenti, risposte in un forum bloccato o spostarli.');
DEFINE('_KUNENA_LOCKED1', 'Chiusi:');
DEFINE('_KUNENA_PUBACC', 'Livello di Accesso pubblico:');
DEFINE('_KUNENA_PUBACCDESC',
    'Per creare un forum non pubblico &#232; possibile specificare qu&#237; il minimo livello che deve avere l\'utente per vedere/entrare nel forum. Per default il minimo livello utente &#232; &quot;Tutti&quot;.<br /><b>Nota</b>: se limiti l\'accesso ad una Categoria oppure ad uno o pi&#249; grouppi, il Forums sar&#224; nascosto a tutti coloro che non hanno gli adeguati privilegi su quella determinata Categoria <b>anche se</b> uno o pi&#249; Forum hanno impostati livelli di accesso pi&#249; bassi! TQuestao vale anche per i Moderatori; si dovr&#225; aggiungere un Moderatore alla lista delle categorie dei moderatori se lui/lei non ha l\'adeguato livello per vedere tale Categoria.<br /> Ci&#242; a prescindere dal fatto che alle categorie non possono accedere i moderatori; i Moderatori possono essere aggiunti alla lista.');
DEFINE('_KUNENA_CGROUPS', 'Includi Child Groups:');
DEFINE('_KUNENA_CGROUPSDESC', 'Nel caso si child groups &#232; consentito l\'accesso? Se &#232; impostato su &quot;No&quot; l\'accesso a questo forum &#232; limitato <b>solo</b> al gruppo selezionato');
DEFINE('_KUNENA_ADMINLEVEL', 'Livello di accesso Admin:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Se si crea un forum con restrizioni agli accessi pubblici, qu&#237; &#232; possibile specificare un ulteriore livello Administration di accesso.<br /> Se si limita l\'accesso al forum per un particolare gruppo di utenti del fontend e non si specifica un gruppo pubblico al backend qu&#237;, gli administrators non saranno in grado di entrare/visualizzare il Forum.');
DEFINE('_KUNENA_ADVANCED', 'Avanzato');
DEFINE('_KUNENA_CGROUPS1', 'Includi Child Groups:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Nel caso si child groups &#232; consentito l\'accesso? Se &#232; impostato su &quot;No&quot; l\'accesso a questo forum &#232; limitato <b>solo</b> al gruppo selezionato');
DEFINE('_KUNENA_REV', 'Rassegna posti:');
DEFINE('_KUNENA_REVDESC',
    'Imposta a &quot;Si&quot; se si vuole sottoporre a revisone da parte dei Moderatori prima della pubblicazione su questo forum. Questo &#232; utile solo in un forum Moderato!<br /> Se si imposta senza specificare la presenza di un Moderatore, l\'Amministrazione del sito &#232; il solo responsabile dell\'approvazione/cancellazione del post, il quale sar&#224; posto \'in attesa\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderazione');
DEFINE('_KUNENA_MODNEWDESC', 'Moderation of the forum and forum moderators');
DEFINE('_KUNENA_MOD', 'Moderatore:');
DEFINE('_KUNENA_MODDESC',
    'Imposta &quot;Si&quot; se si vuole poter assegnare i Moderatori di questo forum.<br /><strong>Nota:</strong> Ci&#242; non significa che i nuovi post devono essere riesaminati prima della pubblicazione sul forum!<br /> E\' necesario impostare  &quot;Modifica&quot; dell\'opzione sulla scheda Avanzata.<br /><br /> <strong>Nota:</strong> Dopo aver impostato la moderazione su &quot;Si&quot; &#232; necessario salvare la configurazione del forum prima, poi potrete aggiungere i Moderatori usando il nuovo pulsante.');
DEFINE('_KUNENA_MODHEADER', 'Impostazione Moderatori per questo forum');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatori attribuiti a questo forum:');
DEFINE('_KUNENA_NOMODS', 'Non ci sono Moderatori assegnati a questo forum');
// Some General Strings (Miglioramento Kunena)
DEFINE('_KUNENA_EDIT', 'Modifica');
DEFINE('_KUNENA_ADD', 'Aggiungi');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Sposta gi&#');
DEFINE('_KUNENA_MOVEDOWN', 'Sposta s&#249;');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Tutti gli iscritti');
DEFINE('_KUNENA_EVERYBODY', 'Tutti');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Riordina');
DEFINE('_KUNENA_CHECKEDOUT', 'Check Out');
DEFINE('_KUNENA_ADMINACCESS', 'Accesso Admin');
DEFINE('_KUNENA_PUBLICACCESS', 'Accesso Pubblico');
DEFINE('_KUNENA_PUBLISHED', 'Pubblicato');
DEFINE('_KUNENA_REVIEW', 'Revisione');
DEFINE('_KUNENA_MODERATED', 'Moderatore');
DEFINE('_KUNENA_LOCKED', 'Bloccato');
DEFINE('_KUNENA_CATFOR', 'Categoria / Forum');
DEFINE('_KUNENA_ADMIN', 'Administration Kunena');
DEFINE('_KUNENA_CP', 'Kunena Pannello di Controllo');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integrazione Avatar');
DEFINE('_COM_A_RANKS_SETTINGS', 'Ranks');
DEFINE('_COM_A_RANKING_SETTINGS', 'Impostazioni Ranking');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Impostazioni Avatar');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Impostazioni di sicurezza');
DEFINE('_COM_A_BASIC_SETTINGS', 'Impostazioni di base');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Consenti preferiti');
DEFINE('_COM_A_FAVORITES_DESC', 'Imposta &quot;Si&quot; se si desidera di permettere agli utenti registrati di aggiungere un topic ai preferiti ');
DEFINE('_USER_UNFAVORITE_ALL', 'Seleziona questa casella per impostare <b><u> non preferiti </u></b> tuuti i topics (compresi quelli invisibili per la risoluzione dei problemi)');
DEFINE('_VIEW_FAVORITETXT', 'Aggiungi ai preferiti questo topic ');
DEFINE('_USER_UNFAVORITE_YES', 'Hai aggiunto questo topic ai non preferiti.');
DEFINE('_POST_FAVORITED_TOPIC', 'Questo thread &#232; stato aggiunto ai non preferiti.');
DEFINE('_VIEW_UNFAVORITETXT', 'Non preferito');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Disdici');
DEFINE('_USER_NOFAVORITES', 'Non Preferito');
DEFINE('_POST_SUCCESS_FAVORITE', 'La tua richiesta di aggiunta ai preferiti &#232; stata effettuata.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Risultati della ricerca');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Messaggi per pagina nei risultati di ricerca');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Usa Stile Joomla?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Se vuoi usare lo stile di joomla scegli SI. (class: like sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Visualizza le immagini della Categoria Child');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Se vuoi visualizzare la categoria child con piccole icone sul tuo forum, scegli SI. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Show Announcement');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Set to "Yes" , if you want to show announcement box on forum homepage.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Visualizza Avatar sulla lista delle Categorie?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Scegli "Si" , se vuoi visualizzare l\'utente avatar sulla lista delle categorei del forum.');
DEFINE('_KUNENA_RECENT_POSTS', 'Impostazioni dei Post recenti');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Visualizza Post recenti');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Scegli "Si" se si desidera mostare gli ultimi post sul forum');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Numero di Post recenti');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Numero di Post recenti');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Count Per Tab ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Numero di post per scheda');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Visualizza Categoria');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Specifica la categoria della quale visualizzare i post recenti. Per esempio: 2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Mostra oggetto singlo');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Mostra oggetto singolo');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Mostra Risposta nell\'oggetto');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Mostra (Re:) nell\'oggetto');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Lunghezza dell\'oggetto');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Lunghezza dell\oggetto');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Mostra data');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Mostra data');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Mostra visualizzazioni');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Mostra visualizzazioni');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Mostra autori');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=username, 2=realname, 0=none');
DEFINE('_KUNENA_STATS', 'Statistiche impostazioni Plugin');
DEFINE('_KUNENA_CATIMAGEPATH', 'Path immagine categoria');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Path immagine categoria. Se si imposta "category_images/" come path essa sar&#224; "percorso_propria_cartella/images/fbfiles/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'ID Annuncio Moderatore ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Aggiungi ID utente per gli annunci di moderazione. Ad esempio 62,63,73 . I moderatori possono aggiungere, modificare, eliminare gli annunci.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Categorie del forum ');
DEFINE('_KUNENA_CHILD_BOARDS', 'Categorie di dettaglio ');
DEFINE('_KUNENA_QUICKMSG', 'Risposta rapida ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Threads in Forum ');
DEFINE('_KUNENA_FORUM', 'Forum ');
DEFINE('_KUNENA_SPOTS', 'Spotlights');
DEFINE('_KUNENA_CANCEL', 'cancella');
DEFINE('_KUNENA_TOPIC', 'TOPIC: ');
DEFINE('_KUNENA_POWEREDBY', 'Powered by ');
// Time Format
DEFINE('_TIME_TODAY', '<b>Oggi</b> ');
DEFINE('_TIME_YESTERDAY', '<b>Ieri</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Ultimi interventi');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Chi &#232; online');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Forum Main');
DEFINE('_KUNENA_GUEST', 'Ospite');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'attualmente in lettura da');
DEFINE('_KUNENA_ATTACH', 'Allegati');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Preferito');
DEFINE('_USER_FAVORITES', 'I miei preferiti');
DEFINE('_THREAD_UNFAVORITE', 'Rimuovi dai preferiti');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Benvenuta/o');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Mostra ultimi interventi');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Configura il mio Avatar');
DEFINE('_PROFILEBOX_MYPROFILE', 'Il mio profilo');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Mostra i miei interventi');
DEFINE('_PROFILEBOX_GUEST', 'Ospite');
DEFINE('_PROFILEBOX_LOGIN', 'Login');
DEFINE('_PROFILEBOX_REGISTER', 'Registrati');
DEFINE('_PROFILEBOX_LOGOUT', 'Logout');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Password dimenticata?');
DEFINE('_PROFILEBOX_PLEASE', 'Effettua il');
DEFINE('_PROFILEBOX_OR', 'o');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Interventi recenti');
DEFINE('_RECENT_TOPICS', 'Topic');
DEFINE('_RECENT_AUTHOR', 'Autore');
DEFINE('_RECENT_CATEGORIES', 'Categorie');
DEFINE('_RECENT_DATE', 'Data');
DEFINE('_RECENT_HITS', 'Visualizzazioni');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Annunci');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Data');
DEFINE('_ANN_TITLE', 'Titolo');
DEFINE('_ANN_SORTTEXT', 'Testo breve');
DEFINE('_ANN_LONGTEXT', 'Testo completo');
DEFINE('_ANN_ORDER', 'Ordine');
DEFINE('_ANN_PUBLISH', 'Pubblica');
DEFINE('_ANN_PUBLISHED', 'Pubblicato');
DEFINE('_ANN_UNPUBLISHED', 'Non Pubblicato');
DEFINE('_ANN_EDIT', 'Modifica');
DEFINE('_ANN_DELETE', 'Cancella');
DEFINE('_ANN_SUCCESS', 'Successo');
DEFINE('_ANN_SAVE', 'Salva');
DEFINE('_ANN_YES', 'Si');
DEFINE('_ANN_NO', 'No');
DEFINE('_ANN_ADD', 'Aggiungi nuovo');
DEFINE('_ANN_SUCCESS_EDIT', 'Modifica con successo');
DEFINE('_ANN_SUCCESS_ADD', 'Aggiunto con successo');
DEFINE('_ANN_DELETED', 'Cancellato con successo');
DEFINE('_ANN_ERROR', 'ERRORE');
DEFINE('_ANN_READMORE', 'Leggi tutto...');
DEFINE('_ANN_CPANEL', 'Pannello di controllo degli annunci');
DEFINE('_ANN_SHOWDATE', 'Mostra data');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Statistiche del Forum');
DEFINE('_STAT_GENERAL_STATS', 'Statistiche Generali');
DEFINE('_STAT_TOTAL_USERS', 'Utenti totali');
DEFINE('_STAT_LATEST_MEMBERS', 'Ultimo utente registrato');
DEFINE('_STAT_PROFILE_INFO', 'Vedi le informazioni del profilo');
DEFINE('_STAT_TOTAL_MESSAGES', 'Numero messaggi');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Numero argomenti');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Totale Categorie');
DEFINE('_STAT_TOTAL_SECTIONS', 'Totale Sezioni');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Aperti oggi');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Aperti ieri');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Totale risposte oggi');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Totale risposte ieri');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Vedi interventi recenti');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Dettagli statistiche');
DEFINE('_STAT_USERLIST', 'Lista utenti');
DEFINE('_STAT_TEAMLIST', 'Board Team');
DEFINE('_STATS_FORUM_STATS', 'Statistiche Forum');
DEFINE('_STAT_POPULAR', 'Popolare');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Utenti ( Totale Messaggi) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Threads ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Utenti ( Totale visualizzazioni profili) ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Utente attualmente Online');
DEFINE('_MODLIST_OFFLINE', 'Utente Offline');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Chi &#232; online');
DEFINE('_WHO_ONLINE_NOW', 'Online');
DEFINE('_WHO_ONLINE_MEMBERS', 'Membri');
DEFINE('_WHO_AND', 'e');
DEFINE('_WHO_ONLINE_GUESTS', 'Ospiti');
DEFINE('_WHO_ONLINE_USER', 'Utente');
DEFINE('_WHO_ONLINE_TIME', 'Time');
DEFINE('_WHO_ONLINE_FUNC', 'Action');
// Userlist
DEFINE('_USRL_USERLIST', 'Lista utenti');
DEFINE('_USRL_REGISTERED_USERS', '%s ha <b>%d</b> gli utenti registrati');
DEFINE('_USRL_SEARCH_ALERT', 'Si prega di inserire un valore per la ricerca !');
DEFINE('_USRL_SEARCH', 'Cerca utente');
DEFINE('_USRL_SEARCH_BUTTON', 'Cerca');
DEFINE('_USRL_LIST_ALL', 'Elenca tutti');
DEFINE('_USRL_NAME', 'Name');
DEFINE('_USRL_USERNAME', 'Username');
DEFINE('_USRL_EMAIL', 'E-mail');
DEFINE('_USRL_USERTYPE', 'Usertype');
DEFINE('_USRL_JOIN_DATE', 'Iscrivi data');
DEFINE('_USRL_LAST_LOGIN', 'Ultimo login');
DEFINE('_USRL_NEVER', 'Mai');
DEFINE('_USRL_BLOCK', 'Stato');
DEFINE('_USRL_MYPMS2', 'Miei messaggi privati');
DEFINE('_USRL_ASC', 'Acendente');
DEFINE('_USRL_DESC', 'Discendente');
DEFINE('_USRL_DATE_FORMAT', '%m/%d/%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Dettagli');
DEFINE('_USRL_COMPROFILER', 'Profilo');
DEFINE('_USRL_THUMBNAIL', 'Pic');
DEFINE('_USRL_READON', 'Mostra');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Invia Messaggi Privati');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Risultato ricerca per');
DEFINE('_USRL_STATUS', 'Stato');
DEFINE('_USRL_LISTSETTINGS', 'Impostazioni lista utenti');
DEFINE('_USRL_ERROR', 'Error');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Componenti Messaggi privati');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Ricerca effettuata per: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Sei sicuro/a di voler eliminare questo messaggio? \n\n NOTA: Non &#232; possibile recuperare i messaggi eliminati!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Il post &#232; stato eliminato');
DEFINE('_COM_A_RANKING', 'Ranking');
DEFINE('_COM_A_BOT_REFERENCE', 'Visualizza Bot Grafico di riferimento');
DEFINE('_COM_A_MOSBOT', 'Attiva il Discuss Bot');
DEFINE('_PREVIEW', 'anteprima');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'Il Discuss Bot consente agli utenti di discutere gli elementi presenti nel forums. Il contenuto viene utilizzato come titolo del tema in oggetto.'
           . '<br />Se il topic non esiste, ne viene creato uno nuovo. Se l\'argomento esiste gi&#224;, l\'utente visualizza il thread e pu&#242; rispondere.' . '<br /><strong>E\' necessario scaricare ed installare separatamente il Bot.</strong>'
           . '<br />Visita il sito di<a href="http://www.Kunena.com">Kunena</a> per maggiori informazioni.' . '<br />Una volta installato sar&#224; necessario inserire le seguenti righe nel Bot:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />Il <em>catid</em> &#232; l\'ID della categoria i cui elementi possono essere discussi. Per torvate il corretto catid, basta guardare nel forum ' . 'e controllare l\'ID della categoria id dall\'URLs presente nella barra di stato.'
           . '<br />Esempio: se si desidera discutere l\'articolo il cui catid &#232; 26, il Bot dovr&#224; essere: {mos_fb_discuss:26}'
           . '<br />Sembra difficile, ma permette ad ogni elemento di essere discusso nel forum corrispondente.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Cerca');
DEFINE('_FORUM_SEARCHRESULTS', 'visualizzato %s dei %s risultati.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Regole');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Modifica questo file per inserire le tue regole joomlaroot/administrator/components/com_kunena/language/kunena.italian.php</li><li>Regola 2</li><li>Regola 3</li><li>Regola 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Boardcode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Il post &#232; stato approvato');
DEFINE('_MODERATION_DELETE_ERROR', 'ERRORE: Il post non &#232; stato eliminato');
DEFINE('_MODERATION_APPROVE_ERROR', 'ERRORE: Il post non &#232; stato approvato');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Non ci sono forum appartenenti a questa categoria!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Fallita la creazione di ghost topic nel vecchio forum!');
DEFINE('_POST_MOVE_GHOST', 'Lesciare ghost message nel vecchio forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Forum Jump');
DEFINE('_COM_A_FORUM_JUMP', 'Abilita Forum Jump');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'Se impostato su &quot;Si&quot; sar&#224; mostrato un selettore sulle pagine del forum che permetter&#224; un pi&#249; rapido passaggio ad un altro forum o categoria.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Rules');
DEFINE('_COM_A_RULESPAGE', 'Enable Rules Page');
DEFINE('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page. This page can be used for things like your forum rules etcetera. You can alter the contents of this file to your liking by opening the rules.php in /joomla_root/components/com_kunena. <em>Make sure you always have a backup of this file as it will be overwritten when upgrading!</em>');
DEFINE('_MOVED_TOPIC', 'SPOSTA:');
DEFINE('_COM_A_PDF', 'Abilita la creazione di PDF');
DEFINE('_COM_A_PDF_DESC',
    'Impostare su &quot;Si&quot; se si vuole abilitare l\'utente al download di documenti PDF con il contenuto del thread.<br />Questo &#232; un <u>documento</u> PDF; non segnare, contiene il testo del thread.');
DEFINE('_GEN_PDFA', 'Clicca su questo pulsante per creare un documento PDF per questo thread (apri in una nuova finestra).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Clicca qu&#236; per visualizzare il profilo di questo utente');
DEFINE('_VIEW_ADDBUDDY', 'Clicca qu&#236; per aggiungere un utente alla tua Lista di amici');
DEFINE('_POST_SUCCESS_POSTED', 'Il tuo messaggio &#232; stato inviato con successo.');
DEFINE('_POST_SUCCESS_VIEW', '[ Ritorna al topic ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Ritorna al forum ]');
DEFINE('_RANK_ADMINISTRATOR', 'Admin');
DEFINE('_RANK_MODERATOR', 'Moderatore');
DEFINE('_SHOW_LASTVISIT', 'Dall\'ultima visita');
DEFINE('_COM_A_BADWORDS_TITLE', 'Filtraggio parole non ammesse');
DEFINE('_COM_A_BADWORDS', 'Usa il filtraggio di parole non ammesse');
DEFINE('_COM_A_BADWORDS_DESC', 'Imposta su &quot;Si&quot; se si desidera filtrare i messaggi contententi le parole che hai definito nella configurazione delle parole non ammesse. Per usare questa funzione devi aver installato il componente del filtraggio delle parole non ammesse!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Questo messaggio &#232; stato censurato perch&#232; contiene parole non ammesse impostate dall\'amministratore *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Crea il profilo del forum Community Builder');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Click the link to create necessary Forum fields in Community Builder user profile. After they are created you are free to move them whenever you want using the Community Builder admin, just do not rename their names or options. If you delete them from the Community Builder admin, you can create them again using this link, otherwise do not click on the link multiple times!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Clicca qu&#236; <');
DEFINE('_COM_A_COMBUILDER', 'Profilo utente Community Builder');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Imposatndo su &quot;Si&quot; sar&#224; attivata l\'integrazione con i componenti del Community Builder component (www.joomlapolis.com). Tutte le funzioni dei profili utenti di Kunena saranno gestite dalla Community Builder e il link del profilo nel sar&#224; indirizzato al profilo utente della Community Builder. Questa impostazione sovrascrive le impostazioni del profilo di Clexus PM se sono entrambe impostate su &quot;Si&quot;. Inoltre, asicuratevi si modificare il database della Community Builder mediante l\'opzione riportata di seguito.');
DEFINE('_COM_A_AVATAR_SRC', 'Usa immagini da avatar');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'If you have JomSocial, Clexus PM or Community Builder installato, &#232; posibile configurare Kunena per utilizzare l\'utente avatar e l\'immagine del profilo utente da JomSocial, Clexus PM or Community Builder. NOTA: Per Community Builder hai bisogno che l\'opzione di anteprima sia attivata poich&#232; il forum usa l\'anteprima delle immagini, non le originali.');
DEFINE('_COM_A_KARMA', 'Mostra indicatore di Karma');
DEFINE('_COM_A_KARMA_DESC', 'Imposta &quot;Si&quot; per visualizzare l\'utente Karma ed i relativi pulsanti (aumenta / diminuisci) se le statistiche utente sono attivate.');
DEFINE('_COM_A_DISEMOTICONS', 'Disabilita emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Imposta &quot;Si&quot; per disattivare completamente la grapica delle emoticons (smileys).');
DEFINE('_COM_C_FBCONFIG', 'Configurazione di Kunena');
DEFINE('_COM_C_FBCONFIGDESC', 'Configura tutte le funzionalit&#224; di Kunena');
DEFINE('_COM_C_FORUM', 'Amministrazione Forum');
DEFINE('_COM_C_FORUMDESC', 'Aggiungi categorie/forum e configurale');
DEFINE('_COM_C_USER', 'Amministrazione utenti');
DEFINE('_COM_C_USERDESC', 'Utenti base e gestione del profilo utente');
DEFINE('_COM_C_FILES', 'Gestione file scaricati');
DEFINE('_COM_C_FILESDESC', 'Sfoglia e gestisci i file scaricati');
DEFINE('_COM_C_IMAGES', 'Gestione immagini scaricate');
DEFINE('_COM_C_IMAGESDESC', 'Gestisci le immagini scaricate');
DEFINE('_COM_C_CSS', 'Modifica File CSS');
DEFINE('_COM_C_CSSDESC', 'Tweak Kunena\'s look and feel');
DEFINE('_COM_C_SUPPORT', 'Supporto WebSite');
DEFINE('_COM_C_SUPPORTDESC', 'Connessione con il sito di Kunena (nuova finestra)');
DEFINE('_COM_C_PRUNETAB', 'Sfoltisci Forums');
DEFINE('_COM_C_PRUNETABDESC', 'Rimuovi vecchio threads (configurabile)');
DEFINE('_COM_C_PRUNEUSERS', 'Sfoltisci Users'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Sync Kunena use tab with Joomla! user table'); // <=FB 1.0.3
DEFINE('_COM_C_LOADSAMPLE', 'Carica dati di esempio');
DEFINE('_COM_C_LOADSAMPLEDESC', 'Per un facile utilizzo: carica i dati d\'esempio su un database vuoto di Kunena');
DEFINE('_COM_C_REMOVESAMPLE', 'Rimuovi i dati di esempio');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Rimuovi dati d\'esempio dal database');
DEFINE('_COM_C_LOADMODPOS', 'Carica il modulo delle posizioni');
DEFINE('_COM_C_LOADMODPOSDESC', 'Carica il modulo delle posizioni per il Template di Kunena');
DEFINE('_COM_C_UPGRADEDESC', 'Aggiorna il database all\'ultima versione');
DEFINE('_COM_C_BACK', 'Ritorna al Pannello di Controllo di Kunena');
DEFINE('_SHOW_LAST_SINCE', 'Topics attivi dall\'ultima visita:');
DEFINE('_POST_SUCCESS_REQUEST2', 'La tua richiesta &#232; stata elaborata');
DEFINE('_POST_NO_PUBACCESS3', 'Clicca qu&#236; per registrarti.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Il messaggio &#232; stato cancellato correttamente.');
DEFINE('_POST_SUCCESS_EDIT', 'Il messaggio &#232; stato modificato correttamente.');
DEFINE('_POST_SUCCESS_MOVE', 'Il topic &#232; stato spostato correttamente.');
DEFINE('_POST_SUCCESS_POST', 'Il tuo messaggio &#232; stato inviato correttamente.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'La tua iscrizione &#232; stata correttamente elaborata.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Karma');
DEFINE('_KARMA_SMITE', 'Copisci');
DEFINE('_KARMA_APPLAUD', 'Applaudi');
DEFINE('_KARMA_BACK', 'Torna al topic,');
DEFINE('_KARMA_WAIT', 'E\' possibile modificare il Karma di una persona ogni 6 ore. <br/>Ti preghiamo di attendere fino alla fine del tempo prima di controllare il karma di qualsiasi persona.');
DEFINE('_KARMA_SELF_DECREASE', 'Please do not attempt to decrease your own karma!');
DEFINE('_KARMA_SELF_INCREASE', 'Your karma has been decreased for attempting to increase it yourself!');
DEFINE('_KARMA_DECREASED', 'User\'s karma decreased. If you are not taken back to the topic in a few moments,');
DEFINE('_KARMA_INCREASED', 'User\'s karma increased. If you are not taken back to the topic in a few moments,');
DEFINE('_COM_A_TEMPLATE', 'Template');
DEFINE('_COM_A_TEMPLATE_DESC', 'Choose the template to use.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Image Sets');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Choose the images set template to use.');
DEFINE('_PREVIEW_CLOSE', 'Chiudi questa finestra');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Use Posts Statistics Bar');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Set to &quot;Yes&quot; if you want the number of posts a user has made to be depicted graphically by a Statistics Bar.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Numero del colore della barra della statistica');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Scegliere il numero del colore che si vuole utilizzare per il post della barra della statistica');
DEFINE('_LATEST_REDIRECT',
    'Kunena ha bisogno di (ri)stabilire le autorizzazioni di accesso prima di reare un elenco degli ultimi post.\nNon dovete preoccuparvi, ci&#242; &#232; normale dopo 30 minuti di inattivit&#224; dal login.\nSi prega di reinviare la richiesta.');
DEFINE('_SMILE_COLOUR', 'Colore');
DEFINE('_SMILE_SIZE', 'Dimensione');
DEFINE('_COLOUR_DEFAULT', 'Standard');
DEFINE('_COLOUR_RED', 'Rosso');
DEFINE('_COLOUR_PURPLE', 'Viola');
DEFINE('_COLOUR_BLUE', 'Blu');
DEFINE('_COLOUR_GREEN', 'Verde');
DEFINE('_COLOUR_YELLOW', 'Giallo');
DEFINE('_COLOUR_ORANGE', 'Arancione');
DEFINE('_COLOUR_DARKBLUE', 'Blu scuro');
DEFINE('_COLOUR_BROWN', 'Marrone');
DEFINE('_COLOUR_GOLD', 'Oro');
DEFINE('_COLOUR_SILVER', 'Argento');
DEFINE('_SIZE_NORMAL', 'Normale');
DEFINE('_SIZE_SMALL', 'Piccolo');
DEFINE('_SIZE_VSMALL', 'Molto piccolo');
DEFINE('_SIZE_BIG', 'Grande');
DEFINE('_SIZE_VBIG', 'Molto grande');
DEFINE('_IMAGE_SELECT_FILE', 'Seleziona un\'immagine da allegare');
DEFINE('_FILE_SELECT_FILE', 'Seleziona un file da allegare');
DEFINE('_FILE_NOT_UPLOADED', 'Il file non &#232; stato caricato. Riprova e/o modifica l\'intervento');
DEFINE('_IMAGE_NOT_UPLOADED', 'La tua immagine non &#232; stata caricata. Riprova e/o modifica l\'intervento');
DEFINE('_BBCODE_IMGPH', 'Inserisci [img]  nel messaggio per inserire l\immagine allegata');
DEFINE('_BBCODE_FILEPH', 'Inserisci [file] nel messaggio per inserire il file allegato');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Spunta questa casella per <b><u>rimuovere l\'iscrizione</u></b> da tutti i topic (inclusi quelli non visibili)');
DEFINE('_LINK_JS_REMOVED', '<em>Link contenenti JavaScript sono stati rimossi automaticamente</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Aspetto'); //Look and Feel
DEFINE('_COM_A_USERS', 'Utenti'); //User Related
DEFINE('_COM_A_LENGTHS', 'Impostazioni sulle dimensioni'); //Various length settings
DEFINE('_COM_A_SUBJECTLENGTH', 'Lunghezza massima dell\'oggetto'); //Max. Subject length
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'La lunghezza massima dell\'oggetto della discussione. Il massimo numero di caratteri gestibili dal database &#232; 255. Se il tuo sito usa set di caratteri multi-byte come Unicode, UTF-8 o non-ISO-8599-x make la lunghezza massima sar&#224; data da:<br/><tt>arrotonda_per_difetto(255/(byte per carattere)</tt><br/> Esempio per UTF-8: un carattere &#232; da 4 bytes: 255/4=63.'); //Maximum Subject line length. The maximum number supported by the database is 255 characters. If your site is configured to use multi-byte character sets like Unicode, UTF-8 or non-ISO-8599-x make the maximum smaller using this forumula:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Example for UTF-8, for which the max. character bite syze per character is 4 bytes: 255/4=63.
DEFINE('_LATEST_THREADFORUM', 'Discussione/Forum'); //Topic/Forum
DEFINE('_LATEST_NUMBER', 'Nuovi messaggi'); //New posts
DEFINE('_COM_A_SHOWNEW', 'Mostra i nuovi messaggi'); //Show New posts
DEFINE('_COM_A_SHOWNEW_DESC', 'Se scegli &quot;Si&quot; Kunena mostrer&#224; un\'icona per i forum che contengono nuovi messaggi e quali di questi sono stati inviati dall\'ultima visita.'); //If set to &quot;Yes&quot; Kunena will show the user an indicator for forums that contain new posts and which posts are new since his/her last visit.
DEFINE('_COM_A_NEWCHAR', 'Icona &quot;Nuovo&quot;'); //&quot;New&quot; indicator
DEFINE('_COM_A_NEWCHAR_DESC', 'Scegli quale simbolo si vuole utilizzare per indicare un nuovo posts (come ad esempio &quot;!&quot; or &quot;New!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Latest post author');
DEFINE('_GEN_FORUM_NEWPOST', 'Nuovi messaggi'); //New Posts
DEFINE('_GEN_FORUM_NOTNEW', 'Nessun nuovo messaggio'); //No New Posts
DEFINE('_GEN_UNREAD', 'Discussione non letta'); //Unread Topic
DEFINE('_GEN_NOUNREAD', 'Discussione letta'); //Read Topic
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Segna tutti i forum come letti'); //Mark all forums read
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Segna questo forum come letto'); //Mark this forum read
DEFINE('_GEN_FORUM_MARKED', 'Tutti i messaggi in questo forum sono stati segnati come letti'); //All posts in this forum have been marked as read
DEFINE('_GEN_ALL_MARKED', 'Tutti i messaggi sono stati segnati come letti'); //All posts have been marked as read
DEFINE('_IMAGE_UPLOAD', 'Invio immagine'); //Image Upload
DEFINE('_IMAGE_DIMENSIONS', 'Il tuo file immagine pu&#242; essere al massimo (larghezza x alterzza - dimensione)'); //Your image file can be maximum (width x height - size)
DEFINE('_IMAGE_ERROR_TYPE', 'Usa solo immagini jpeg, gif o png'); //Please use only jpeg, gif or png images
DEFINE('_IMAGE_ERROR_EMPTY', 'Seleziona un file prima di inviare'); //Please select a file before uploading
DEFINE('_IMAGE_ERROR_SIZE', 'La dimensione del file immagine eccede i limiti impostati dall\'amministratore.'); //The image file size exceeds the maximum set by the Administrator.
DEFINE('_IMAGE_ERROR_WIDTH', 'La larghezza del file immagine eccede i limiti impostati dall\'amministratore.'); //The image file width exceeds the maximum set by the Administrator.
DEFINE('_IMAGE_ERROR_HEIGHT', 'L\'altezza del file immagine eccede i limiti impostati dall\'amministratore.'); //The image file height exceeds the maximum set by the Administrator.
DEFINE('_IMAGE_UPLOADED', 'La tua immagine &#232; stata inviata al forum.'); //Your Image has been uploaded.
DEFINE('_COM_A_IMAGE', 'Immagini'); //Images
DEFINE('_COM_A_IMGHEIGHT', 'Altezza massima dell\'immagine'); //Max. Image Height
DEFINE('_COM_A_IMGWIDTH', 'Larghezza massima dell\'immagine'); //Max. Image Width
DEFINE('_COM_A_IMGSIZE', 'Dimensione massima del file immagine<br/><em>in Kilobytes</em>'); //Max. Image Filesize<br/><em>in Kilobytes</em>
DEFINE('_COM_A_IMAGEUPLOAD', 'Permetti invio pubblico di immagini'); //Allow Public Upload for Images
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Scegli &quot;Si&quot; se vuoi che chiunque possa inviare immagini.'); //Set to &quot;Yes&quot; if you want everybody (public) to be able to upload an image.
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Permetti invio di immagini ai soli utenti registrati'); //Allow Registered Upload for Images
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Scegli &quot;Si&quot; se vuoi che solo gli utenti registrati, e che hanno effettuato l\'accesso, possano inviare immagini.<br/> Nota: (Super)amministratori e moderatori possono sempre inviare immagini.'); //Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload an image.<br/> Note: (Super)administrators and moderators are always able to upload images.
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Uploads'); //Uploads
DEFINE('_FILE_TYPES', 'Il file pu&#242; essere di tipo - dimensione massima'); //Your file can be of type - max. size
DEFINE('_FILE_ERROR_TYPE', 'Hai il permesso di caricare solo file di tipo:\n'); //You are only allowed to upload files of type:\n
DEFINE('_FILE_ERROR_EMPTY', 'Seleziona un file prima di inviare'); //Please select a file before uploading
DEFINE('_FILE_ERROR_SIZE', 'La dimensione del file eccede il limite imposto dall\'amministratore.'); //
DEFINE('_COM_A_FILE', 'File'); //Files
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Tipi di file permessi'); //File types allowed
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Specifica qui i tipi di file permessi per l\'upload. Usa una lista separata da virgole <strong>in minuscolo</strong> senza spazi.<br />Esempio: zip,txt,exe,htm,html'); //Specify here which file types are allowed for upload. Use comma separated <strong>lowercase</strong> lists without spaces.<br />Example: zip,txt,exe,htm,html
DEFINE('_COM_A_FILESIZE', 'Dimesione massima del file<br/><em>in Kilobytes</em>'); //Max. File size<br/><em>in Kilobytes</em>
DEFINE('_COM_A_FILEUPLOAD', 'Permetti invio pubblico di file'); //Allow File Upload for Public
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Scegli &quot;Si&quot; se vuoi che chiunque possa inviare file.'); //Set to &quot;Yes&quot; if you want everybody (public) to be able to upload a file.
DEFINE('_COM_A_FILEREGUPLOAD', 'Permetti invio di file ai soli utenti registrati'); //Allow File Upload for Registered
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Scegli &quot;Siquot; se vuoi che soli gli utenti registrati, e che hanno effettuato l\'accesso, possano inviare file.<br/> Nota: (Super)amministratori e moderatori possono sempre inviare file.'); //Set to &quot;Yes&quot; if you want registered and logged in users to be able to upload a file.<br/> Note: (Super)administrators and moderators are always able to upload files.
DEFINE('_SUBMIT_CANCEL', 'L\invio del tuo messaggio &#232; stato cancellato'); //Your post submission has been cancelled
DEFINE('_HELP_SUBMIT', 'Clicca qui per inviare il tuo messaggio'); //Click here to submit your message
DEFINE('_HELP_PREVIEW', 'Clicca qui per vedere l\'anteprima del messaggio'); //Click here to see what your message will look like when submitted
DEFINE('_HELP_CANCEL', 'Clicca qui per cancellare il tuo messaggio'); //Click here to cancel your message
DEFINE('_POST_DELETE_ATT', 'Se questa casella &#232; attivata, tutte le immagini ed i file contenuti nel messagio che stai cancellando, verranno rimossi (raccomandato).'); //If this box is checked, all image and file attachments of posts you are going to delete will be deleted as well (recommended).
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Mostra le modifiche'); //Show Edited Mark Up
DEFINE('_COM_A_USER_MARKUP_DESC', 'Scegli &quot;Si&quot; se vuoi che un messaggio modificato venga segnato con il nome dell\'utente che ha apportato le modifiche e la data delle stesse.'); //Set to &quot;Yes&quot; if you want an edited post be marked up with text showing that the post is edited by a user and when it was edited.
DEFINE('_EDIT_BY', 'Messaggio modificato da:'); //Post edited by:
DEFINE('_EDIT_AT', 'alle:'); //at:
DEFINE('_UPLOAD_ERROR_GENERAL', 'Errore: impossibile inviare la tua immagine personale. Prova di nuovo o avvisa un amministratore'); //An error occured when uploading your Avatar. Please try again or notify your system administrator
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Esplora immagine inviate'); //Uploaded Images Browser
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Esplora file inviati'); //Uploaded Files Browser
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Numero di immagini inviate'); //Number of uploaded images
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Numero di file inviati'); //Number of uploaded files
DEFINE('_COM_A_IMGB_ENLARGE', 'Clicca sull\'immagina per vederla nelle dimensioni originale'); //Click the image to see its full size
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Clicca sull\'immagine del file per scaricarlo'); //Click the file image to download it
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'L\'opzionie &quot;Rimpiazza con un\'immagine di servizio (dummy)&quot; rimpiazzer&#224; l\'immagine selezionata con quella di servizio.<br /> Questo permette di rimuovere di fatto l\'immagine, senza compromettere il messaggio (il link non verr&#224; rotto).<br /><small><em>Nota che spesso &#232; necessario cancellare la cache del browser per vedere l\'immagine rimpiazzata da quella di servizio.</em></small>'); //The option &quot;Replace with dummy&quot; will replace the selected image with a dummy image.<br /> This allows you to remove the actual file without breaking the post.<br /><small><em>Please note that sometimes an explicit browser refresh is needed to see the dummy replacement.</em></small>
DEFINE('_COM_A_IMGB_DUMMY', 'Immagine di servizio (dummy) corrente'); //Current dummy image
DEFINE('_COM_A_IMGB_REPLACE', 'Rimpiazza con immagine di servizio (dummy)'); //Replace with dummy
DEFINE('_COM_A_IMGB_REMOVE', 'Rimuovi completamente'); //Remove entirely
DEFINE('_COM_A_IMGB_NAME', 'Nome'); //Name
DEFINE('_COM_A_IMGB_SIZE', 'Dimensione'); //Size
DEFINE('_COM_A_IMGB_DIMS', 'Misure'); //Dimensions
DEFINE('_COM_A_IMGB_CONFIRM', 'Sei veramente sicuro di voler cancellare questo file? \nCancellando un file, il messaggio rester&#224; compromesso...'); //Are you absolutely sure you want to delete this file? \n Deleting a file, will give a crippled referencing post...'
DEFINE('_COM_A_IMGB_VIEW', 'Apri il messaggio (per modificarlo)'); //Open post (to edit)
DEFINE('_COM_A_IMGB_NO_POST', 'Nessun messaggio collegato!'); //No referencing post!
DEFINE('_USER_CHANGE_VIEW', 'I cambiamenti saranno effettivi alla tua prossima visita.<br /> Se vuoi cambiare il tipo di visualizzazione &quot;Mid-Flight&quot; puoi usare l\'opzione dalla barra del menu del forum.'); //Changes in these settings will become effective the next time you visit the forums.<br /> If you want to change the view type &quot;Mid-Flight&quot; you can use the options from the forum menu bar.
DEFINE('_MOSBOT_DISCUSS_A', 'Discuti questo articolo sul forum. ('); //Discuss this article on the forums. (
DEFINE('_MOSBOT_DISCUSS_B', ' messaggi)'); //posts
DEFINE('_POST_DISCUSS', 'Questa discussione &#232; relativa al contenuto dell\'articolo'); //This thread discusses the Content article
DEFINE('_COM_A_RSS', 'Abilita i feed RSS'); //Enable RSS feed
DEFINE('_COM_A_RSS_DESC', 'I feed RSS permettono agli utenti di scaricare i nuovi messaggi sul computer (applicatione RSS) o aggregatore online (vedi <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> per un esempio.'); //The RSS feed enables users to download the latest posts to their desktop/RSS Reader aplication (see <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.
DEFINE('_LISTCAT_RSS', 'ottieni gli ultimi messaggi direttamente sul tuo desktop'); //get the latest posts directly to your desktop
DEFINE('_SEARCH_REDIRECT', 'Devi effettuare di nuovo l\'accesso per proseguire con la ricerca.\nNon preoccuparti, &#232; normale dopo pi&#249; di 30 minuti di inattivit&#224;.\nEsegui la ricerca nuovamente.'); //Kunena needs to (re)establish your access privileges before it can perform your search request.\nDo not worry, this is quite normal after more than 30 minutes of inactivity.\nPlease submit your search request again.
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Configurazione di Kunena'); //Kunena Configuration
DEFINE('_COM_A_DISPLAY', 'Mostra #'); //Display #
DEFINE('_COM_A_CURRENT_SETTINGS', 'Impostazioni attuali'); //Current Setting'
DEFINE('_COM_A_EXPLANATION', 'Spiegazione'); //Explanation
DEFINE('_COM_A_BOARD_TITLE', 'Titolo della board'); //Board Title
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Il nome della tua board'); //The name of your board
DEFINE('_COM_A_VIEW_TYPE', 'Visualizzazione di default'); //Default View type
DEFINE('_COM_A_VIEW_TYPE_DESC', 'Scegli tra visualizzazione piatta o per discussione'); //Choose between a threaded or flat view as default
DEFINE('_COM_A_THREADS', 'Discussioni per pagina'); //Threads Per Page
DEFINE('_COM_A_THREADS_DESC', 'numero di discussioni da mostrare per pagina'); //Number of threads per page to show
DEFINE('_COM_A_REGISTERED_ONLY', 'Solo utenti regitrati'); //Registered Users Only
DEFINE('_COM_A_REG_ONLY_DESC', 'Scegli &quot;Si;&quot; per permettere solo agli utenti registrati di usare il forum (leggere & scrivere). Scegli &quot;No&quot; per permettere a chiunque l\'uso del forum'); //Set to &quot;Yes&quot; to allow only registered users to use the Forum (view & post), Set to &quot;No&quot; to allow any visitor to use the Forum
DEFINE('_COM_A_PUBWRITE', 'Permesso di Lettura/Scrittura pubblico'); //Public Read/Write
DEFINE('_COM_A_PUBWRITE_DESC', 'Scegli &quot;Si&quot; per mettere a chiunque di scrivere sul forum. Scegli &quot;No&quot; per permette ai chiunque di leggere, ma solo agli utenti registrati di scrivere'); //Set to &quot;Yes&quot; to allow for public write privileges, Set to &quot;No&quot; to allow any visitor to see posts, but only registered users to write posts
DEFINE('_COM_A_USER_EDIT', 'Modifiche dell\'utente'); //User Edits
DEFINE('_COM_A_USER_EDIT_DESC', 'Scegli &quot;Si&quot; per permettere agli utenti registrati di modificare i propri messaggi inviati.'); //Set to &quot;Yes&quot; to allow registered Users to edit their own posts.
DEFINE('_COM_A_MESSAGE', 'Per salvare i cambiati effettuati, premi il bottone &quot;Salva&quot; in cima.'); //In order to save changes of the values above, press the &quot;Save&quot; button at the top.
DEFINE('_COM_A_HISTORY', 'Mostra cronologia'); //Show History
DEFINE('_COM_A_HISTORY_DESC', 'Scegli &quot;Si&quot; se vuoi visualizzare la cronologia della discussione quando si scrive una risposta'); //Set to &quot;Yes&quot; if you want the topic history shown when a reply/quote is made
DEFINE('_COM_A_SUBSCRIPTIONS', 'Permetti le sottoscrizioni'); //Allow Subscriptions
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Scegli &quot;Si&quot; se vuoi permettere agli utenti registrati di sottoscriversi alle discussioni e ricevere gli aggiornamenti via email'); //Set to &quot;Yes&quot; if you want to allow registered users to subscribe to a topic and recieve email notifications on new posts
DEFINE('_COM_A_HISTLIM', 'Limite della cronologia'); //History Limit
DEFINE('_COM_A_HISTLIM_DESC', 'Numero di messaggi da visualizzare nella cronologia'); //Number of posts to show in the history
DEFINE('_COM_A_FLOOD', 'Protezione dalla ripetizioni (Flood)'); //Flood Protection
DEFINE('_COM_A_FLOOD_DESC', 'Quanti secondi deve aspettare un utente per inviare due messaggi consecutivi. Imposta su 0 (zero) per disabilitare la protezione dalle ripetizioni (Flood Protection). NOTA: la protezione dalle ripetizioni <em>pu&#242;</em> causare un decremento delle prestazioni..'); //The amount of seconds a user has to wait between two consecutive post. Set to 0 (zero) to turn Flood Protection off. NOTE: Flood Protection <em>can</em> cause degradation of performance..
DEFINE('_COM_A_MODERATION', 'Inviare email ai moderatori'); //Email Moderators
DEFINE('_COM_A_MODERATION_DESC',
    'Scegli &quot;Si&quot; se vuoi inviare un\'email di notifica per i nuovi messaggi ai moderatori dei rispettivi forum. Nota: sebbene ogni (super)amministratore ha automaticamente tutti i privilegi di moderatore, bisogna assegnarli in modo esplicitido come moderatori
 the del forum per ricevere le notifiche!'); //Set to &quot;Yes&quot; if you want email notifications on new posts sent to the forum moderator(s). Note: although every (super)administrator has automatically all Moderator privileges assign them explicitly as moderators on the forum to recieve emails too!
DEFINE('_COM_A_SHOWMAIL', 'Mostra email'); //Show Email
DEFINE('_COM_A_SHOWMAIL_DESC', 'Scegli &quot;No&quot; se non vuoi mostrare gli indirizzi email degli utenti; nemmeno agli utenti registrati.'); //Set to &quot;No&quot; if you never want to display the users email address; not even to registered users.
DEFINE('_COM_A_AVATAR', 'Permetti le immagini personali'); //Allow Avatars
DEFINE('_COM_A_AVATAR_DESC', 'Scegli &quot;Si&quot; per abilitare l\'immagine personale degli utenti registrati (configurabile attraverso la pagina del loro profilo)'); //Set to &quot;Yes&quot; if you want registered users to have an avatar (manageable trough their profile)
DEFINE('_COM_A_AVHEIGHT', 'Altezza massima dell\'immagine personale'); //Max. Avatar Height
DEFINE('_COM_A_AVWIDTH', 'Larghezza massima dell\'immagine personale'); //Max. Avatar Width
DEFINE('_COM_A_AVSIZE', 'Dimensione massimo del file immagine<br/><em>in Kilobytes</em>'); //Max. Avatar Filesize<br/><em>in Kilobytes</em>
DEFINE('_COM_A_USERSTATS', 'Mostra le statistiche degli utenti'); //Show User Stats
DEFINE('_COM_A_USERSTATS_DESC', 'Scegli &quot;Si&quot; per mostrare le statistiche degli utenti, come il numero dei messaggi scritti dall\'utente, il tipo di utente (Amministratore, Moderatore, Utente, ecc.).'); //Set to &quot;Yes&quot; to show User Statistics like number of posts user made, User type (Admin, Moderator, User, etc.).
DEFINE('_COM_A_AVATARUPLOAD', 'Permetti il caricamento delle immagini personali'); //Allow Avatar Upload
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Scegli &quot;Si&quot; per permettere agli utenti registrati di inviare al server le proprie immagini personali dal computer.'); //Set to &quot;Yes&quot; if you want registered users to be able to upload an avatar.
DEFINE('_COM_A_AVATARGALLERY', 'Usa la galleria delle immagini personali'); //Use Avatars Gallery
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Scegli &quot;si&quot; per permettere agli utenti registrati di scegliere un\'immagine personale dalla galleria (presenti nella cartella components/com_kunena/avatars/gallery).'); //Set to &quot;Yes&quot; if you want registered users to be able to choose an avatar from a Gallery you provide (components/com_kunena/avatars/gallery).
DEFINE('_COM_A_RANKING_DESC', 'Scegli &quot;Si&quot; per visualizzare i livelli degli utenti registrati, basati sul numero di messaggi scritti.<br/><strong>Devi abilitare anche le statistiche degli utenti nella tab "Avanzate".</strong>'); //Set to &quot;Yes&quot; if you want to show the rank registered users have based on the number of posts they made.<br/><strong>Note that you must enable User Stats in the Advanced tab too to show it.</strong>
DEFINE('_COM_A_RANKINGIMAGES', 'Mostra i livelli degli utenti con le immagini'); //Use Rank Images
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Scegli &quot;Si&quot; se vuoi visualizzare i livelli degli utenti registarti con un\'immagine (presenti nella cartella components/com_kunena/ranks). Se non abiliti questa funzione, verr&#224; usato del testo semplice. Controlla la documentazione su on www.kunena.com per maggiori informazioni sulle immagini dei livelli'); //Set to &quot;Yes&quot; if you want to show the rank registered users have with an image (from components/com_kunena/ranks). Turning this of will show the text for that rank. Check the documentation on www.kunena.com for more information on ranking images

//email and stuff
$_COM_A_NOTIFICATION = "Notifiche per i nuovi messaggi da "; //New post notification from 
$_COM_A_NOTIFICATION1 = "Un nuovo messaggio &#232; stato inviato nella discussione alla quale ti sei sottoscritto il "; //A new post has been made to a topic to which you have subscribed on the 
$_COM_A_NOTIFICATION2 = "Puoi amministrare le tue sottoscrizioni dal tuo profilo."; //You can administer your subscriptions by following the 'my profile' link on the forum home page after you have logged in on the site. From your profile you can also unsubscribe from the topic.
$_COM_A_NOTIFICATION3 = "Non rispondere a questa email."; //Do not answer to this email notification as it is a generated email.
$_COM_A_NOT_MOD1 = "Un nuovo messaggio &#232; stato inviato nel forum assegnatoti come moderatore il "; //A new post has been made to a forum to which you have assigned as moderator on the 
$_COM_A_NOT_MOD2 = "Dagli un'occhiata una volta effettuato l'accesso al sito."; //Please have a look at it after you have logged in on the site.
DEFINE('_COM_A_NO', 'No');
DEFINE('_COM_A_YES', 'Si'); //Yes
DEFINE('_COM_A_FLAT', 'Piatto'); //Flat
DEFINE('_COM_A_THREADED', 'Per discussione'); //Threaded
DEFINE('_COM_A_MESSAGES', 'Messaggi per pagina'); //Messages per page
DEFINE('_COM_A_MESSAGES_DESC', 'Numero di messaggi da visualizzare su ogni pagina'); //Number of messages per page to show
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Nome utente'); //Username
DEFINE('_COM_A_USERNAME_DESC', 'Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the users real name'); //Set to &quot;Yes&quot; if you want the username (as in login) to be used instead of the users real name
DEFINE('_COM_A_CHANGENAME', 'Permetti il cambiamento del nome'); //Allow Name Change
DEFINE('_COM_A_CHANGENAME_DESC', 'Set to &quot;Yes&quot; if you want registered users to be able to change their name when posting. If set to &quot;No&quot; then registered users will not be able to edit his/her name');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum Offline'); //Forum Offline
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Set to &quot;Yes&quot; if you want to take the Forum section offline. The forum will remain browsable by site (super)admins.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Messaggio da visualizzare quando il forum &#232; offline'); //Forum Offline Message
DEFINE('_COM_A_PRUNE', 'Sfoltisci i forum'); //Prune Forums
DEFINE('_COM_A_PRUNE_NAME', 'Forum da sfoltire:'); //Forum to prune:
DEFINE('_COM_A_PRUNE_DESC',
    'Sfoltendo i forum ti permette di cancellare le discussioni che non hanno nuovi messaggi da un certo numero di giorni. Non verranno rimosse le discussioni evidenziate o bloccate. Queste devono essere rimosse manualmente. Le discussioni nei forum bloccato non verranno sfoltiti.'); //The Prune Forums function allows you to prune threads to which there have not been any new posts for the specified number of days. This does not remove topics with the sticky bit set or which are explicitly locked; these must be removed manually. Threads in locked forums can not be pruned.
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Sfoltisci le discussioni senza messaggi da almeno '); //Prune threads with no posts for the past 
DEFINE('_COM_A_PRUNE_DAYS', 'giorni'); //days
DEFINE('_COM_A_PRUNE_USERS', 'Sfoltisci utenti'); // <=FB 1.0.3 Prune Users
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'Questa funzione ti permette di sfoltire gli utenti di Kunena. Verranno cancellati tutti i profili di utenti che sono stati cancellati da Joomla!<br/>Se vuoi continuare, clicca &quot;Sfoltisci gli utenti&quot;, nel menu di sopra.'); // <=FB 1.0.3 This function allows you to prune your Kunena user list against the Joomla! Site user list. It will delete all profiles for Kunena Users that have been deleted from your Joomla! Framework.<br/>When you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.
//general
DEFINE('_GEN_ACTION', 'Azione'); //Actions
DEFINE('_GEN_AUTHOR', 'Autore'); //Author
DEFINE('_GEN_BY', 'di'); //by
DEFINE('_GEN_CANCEL', 'Annulla'); //Cancel
DEFINE('_GEN_CONTINUE', 'Invia'); //Submit
DEFINE('_GEN_DATE', 'Data'); //Date
DEFINE('_GEN_DELETE', 'Cancella'); //Delete
DEFINE('_GEN_EDIT', 'Modifica'); //Edit
DEFINE('_GEN_EMAIL', 'Email');
DEFINE('_GEN_EMOTICONS', 'Emoticons');
DEFINE('_GEN_FLAT', 'Piatta');
DEFINE('_GEN_FLAT_VIEW', 'Piatta');
DEFINE('_GEN_FORUMLIST', 'Lista dei forum'); //Forum List
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Aiuto'); //Help
DEFINE('_GEN_HITS', 'Visualizzazioni'); //Views
DEFINE('_GEN_LAST_POST', 'Ultimo intervento'); //Last Post
DEFINE('_GEN_LATEST_POSTS', 'Mostra gli ultimi interventi'); //Show latest posts
DEFINE('_GEN_LOCK', 'Blocca'); //Lock
DEFINE('_GEN_UNLOCK', 'Sblocca'); //Unlock
DEFINE('_GEN_LOCKED_FORUM', 'Il forum &#232; bloccato'); //Forum is locked
DEFINE('_GEN_LOCKED_TOPIC', 'La discussione &#232; bloccata'); //Topic is locked
DEFINE('_GEN_MESSAGE', 'Messaggio'); //Message
DEFINE('_GEN_MODERATED', 'Forum moderato, verr&#224; valutata la pubblicazione.'); //Forum is moderated; Reviewed prior to publishing.
DEFINE('_GEN_MODERATORS', 'Moderatori'); //Moderators
DEFINE('_GEN_MOVE', 'Sposta'); //Move
DEFINE('_GEN_NAME', 'Nome'); //Name
DEFINE('_GEN_POST_NEW_TOPIC', 'Invia nuova discussione'); //Post New Topic
DEFINE('_GEN_POST_REPLY', 'Invia risposta'); //Post Reply
DEFINE('_GEN_MYPROFILE', 'Il mio profilo'); //My Profile
DEFINE('_GEN_QUOTE', 'Cita'); //Quote
DEFINE('_GEN_REPLY', 'Risposta'); //Reply
DEFINE('_GEN_REPLIES', 'Risposte'); //Replies
DEFINE('_GEN_THREADED', 'Threaded'); //Threaded
DEFINE('_GEN_THREADED_VIEW', 'Threaded'); //Threaded
DEFINE('_GEN_SIGNATURE', 'Firma');
DEFINE('_GEN_ISSTICKY', 'La discussione &#232; evidenziata'); //Topic is sticky.
DEFINE('_GEN_STICKY', 'Evidenzia'); //Sticky
DEFINE('_GEN_UNSTICKY', 'Non evidenziare'); //Unsticky
DEFINE('_GEN_SUBJECT', 'Oggetto'); //Subject
DEFINE('_GEN_SUBMIT', 'Invia'); //Submit
DEFINE('_GEN_TOPIC', 'Discussione'); //Topic
DEFINE('_GEN_TOPICS', 'Discussioni'); //Topics
DEFINE('_GEN_TOPIC_ICON', 'icona della discussione'); //topic icon
DEFINE('_GEN_SEARCH_BOX', 'Cerca nel forum'); //Search Forum
$_GEN_THREADED_VIEW = "Per discussione";
$_GEN_FLAT_VIEW = "Flat";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Invio al server'); //Upload
DEFINE('_UPLOAD_DIMENSIONS', 'La tua immagine personale pu&#242; essere al massimo (larghezza x altezza)'); //Your image file can be maximum (width x height - size)
DEFINE('_UPLOAD_SUBMIT', 'Invia una nuova immagine personale'); //Submit a new Avatar for Upload
DEFINE('_UPLOAD_SELECT_FILE', 'Seleziona file'); //Select file
DEFINE('_UPLOAD_ERROR_TYPE', 'Usa solo immagini in formato jpeg, gif o png'); //Please use only jpeg, gif or png images
DEFINE('_UPLOAD_ERROR_EMPTY', 'Selezionare il file prima di inviare la richiesta'); //Please select a file before uploading
DEFINE('_UPLOAD_ERROR_NAME', 'Il file deve contenere solo caratteri alfanumerici e nessuno spazio.'); //The image file must contain only alphanumeric characters and no spaces please.
DEFINE('_UPLOAD_ERROR_SIZE', 'Dimensioni del file eccessive.'); //The image file size exceeds the maximum set by the Administrator.
DEFINE('_UPLOAD_ERROR_WIDTH', 'Larghezza dell\'immagine eccessiva.'); //The image file width exceeds the maximum set by the Administrator.
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Altezza dell\'immagine eccessiva.'); //The image file height exceeds the maximum set by the Administrator.
DEFINE('_UPLOAD_ERROR_CHOOSE', "Non hai scelto un\'immagine personale dalla galleria..."); //"You didn't choose an Avatar from the gallery..."
DEFINE('_UPLOAD_UPLOADED', 'La tua immagine personale &#232; stata caricata.'); //Your avatar has been uploaded.
DEFINE('_UPLOAD_GALLERY', 'Scegline uno dalla galleria:'); //Choose one from the Avatar Gallery:
DEFINE('_UPLOAD_CHOOSE', 'Conferma la scelta.'); //Confirm Choice.
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Un amministratore dovrebbe creale prima da '); //An administrator should create them first from the 
DEFINE('_LISTCAT_DO', 'Loro sapranno che fare '); //They will know what to do 
DEFINE('_LISTCAT_INFORM', 'Informali e digli di fare in fretta!'); //Inform them and tell them to hurry up!
DEFINE('_LISTCAT_NO_CATS', 'Non sono state ancora definite delle categorie nel forum.'); //There are no categories in the forum defined yet.
DEFINE('_LISTCAT_PANEL', 'Pannello di amministrazione di Joomla!.'); //Administration Panel of the Joomla! OS CMS.
DEFINE('_LISTCAT_PENDING', 'messaggio/messaggi in sospeso'); //pending message(s)
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Non ci sono messaggi in sospesi nel forum.'); //There are no pending messages in this forum.
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Stai per cancellare il messaggio'); //You are about to delete the message
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTE:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Consider blanking the post and posters name if only the contents should be removed..
<br/>
- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.'); //TODO
DEFINE('_POST_CLICK', 'clicca qui'); //click here
DEFINE('_POST_ERROR', 'Impossibile trovare username/mail'); //Could not find username/email. Severe database error not listed //TODO
DEFINE('_POST_ERROR_MESSAGE', 'Errore SQL sconosciuto: il tuo messaggio non &#232; stato inviato. Se il problema persiste, contattare l\'amministratore del sistema.'); //An unknown SQL Error occured and your message was not posted.  If the problem persists, please contact the administrator.
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Errore: il tuo messaggio non &#232; stato aggiornato. Riprova. Se il problema persiste, contattare l\'amministratore del sistema.'); //An error has occured and the message was not updated.  Please try again.  If this error persists please contact the administrator.
DEFINE('_POST_ERROR_TOPIC', 'Si &#232; verificato un errore durante la cancellazione. Controlla il messaggio di errore:'); //An error occured during the delete(s). Please check the error below:
DEFINE('_POST_FORGOT_NAME', 'Hai dimenticato di includere il nome.  Clicca il bottone INDIETRO del tuo browser e riprova.'); //You forgot to include your name.  Click your browser&#146s back button to go back and try again.
DEFINE('_POST_FORGOT_SUBJECT', 'Hai dimenticato di includere l\'oggetto.  Clicca il bottone INDIETRO del tuo browser e riprova.'); //You forgot to include a subject.  Click your browser&#146s back button to go back and try again.
DEFINE('_POST_FORGOT_MESSAGE', 'Hai dimenticato di includere il messaggio. Clicca il bottone INDIETRO del tuo browser e riprova.'); //You forgot to include a message.  Click your browser&#146s back button to go back and try again.
DEFINE('_POST_INVALID', 'ID del messaggio richiesto non valido.'); //An invalid post id was requested.
DEFINE('_POST_LOCK_SET', 'La discussione &#232; stato bloccata.'); //The topic has been locked.
DEFINE('_POST_LOCK_NOT_SET', 'La discussione non pu&#242; essere bloccata.'); //The topic could not be locked.
DEFINE('_POST_LOCK_UNSET', 'La discussione &#232; stata sbloccata.'); //The topic has been unlocked.
DEFINE('_POST_LOCK_NOT_UNSET', 'La discussione non pu&#242; essere sbloccata.'); //The topic could not be unlocked.
DEFINE('_POST_MESSAGE', 'Inserisci un messaggio in '); //Post a new message in 
DEFINE('_POST_MOVE_TOPIC', 'Sposta questa discussione nel forum '); //Move this Topic to forum 
DEFINE('_POST_NEW', 'Inserisci un nuovo messaggio in: '); //Post a new message in: 
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'La tua sottoscrizione a questo topic non pu&#242; essere elaborata.'); //Your subscription to this topic could not be processed.
DEFINE('_POST_NOTIFIED', 'Spunta questa casella se vuoi essere notificato in caso di risposte a questa discussione.'); //Check this box to have yourself notified about replies to this topic.
DEFINE('_POST_STICKY_SET', 'La discussione &#232; ora evidenziata'); //The sticky bit has been set for this topic.
DEFINE('_POST_STICKY_NOT_SET', 'Questa discussione non pu&#242; essere evidenziata.'); //The sticky bit could not be set for this topic.
DEFINE('_POST_STICKY_UNSET', 'Questa discussione non &#232; pi&#249; evidenziata.'); //The sticky bit has been unset for this topic.
DEFINE('_POST_STICKY_NOT_UNSET', 'Non &#232; possibile togliere l\'evidenziazione per questa discussione.'); //The sticky bit could not be unset for this topic.
DEFINE('_POST_SUBSCRIBE', 'sottoscriviti'); //subscribe
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Ti sei sottoscritto a questa discussione.'); //You have been subscribed to this topic.
DEFINE('_POST_SUCCESS', 'Messaggio inviato con successo'); //Your message has been successfully
DEFINE('_POST_SUCCES_REVIEW', 'Il tuo messaggio &#232; stato inviato con successo. Verr&#224; valutato da un moderatore e successivamente pubblicato.'); //Your message has been successfully posted.  It will be reviewed by a Moderator before it will be published on the forum.
DEFINE('_POST_SUCCESS_REQUEST', 'La tua richiesta &#232; stata elaborata. Se non verrai rediretto nuovamente alla discussione in pochi secondi,'); //Your request has been processed.  If you are not taken back to the topic in a few moments,
DEFINE('_POST_TOPIC_HISTORY', 'Cronologia della discussione'); //Topic History of
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Max. visualizza ultimo'); //TODO
DEFINE('_POST_TOPIC_HISTORY_LAST', 'messaggi  -  <i>(Ultimi messaggi in cima)</i>'); //posts  -  <i>(Last post first)</i>'
DEFINE('_POST_TOPIC_NOT_MOVED', 'La tua discussione non pu&#242; essere spostata. Per ritornare alla discussione:'); //Your topic could not be moved. To get back to the topic:
DEFINE('_POST_TOPIC_FLOOD1', 'L\'amministratore di questo forum ha abilitato la Flood Protection (protezione dalle ripetizioni) e devi aspettare '); //The administrator of this forum has enabled Flood Protection and has decided that you must wait 
DEFINE('_POST_TOPIC_FLOOD2', ' secondi prima di poter scrivere un altro messaggio.'); //seconds before you can make another post.
DEFINE('_POST_TOPIC_FLOOD3', 'Clicca il bottone INDIETRO del tuo browser per tornare al forum.'); //Please click your browser&#146s back button to get back to the forum.
DEFINE('_POST_EMAIL_NEVER', 'il tuo indirizzo email non verr&#224; mai pubblicato su questo sito.'); //your email address will never be displayed on the site.
DEFINE('_POST_EMAIL_REGISTERED', 'il tuo indirizzo email sar&#224; visibile solamente agli utenti registrati.'); //your email address will only be available to registered users.
DEFINE('_POST_LOCKED', 'bloccato dall\'amministratore.'); //locked by the administrator.
DEFINE('_POST_NO_NEW', 'Non si pu&#242; rispondere.'); //New replies are not allowed.
DEFINE('_POST_NO_PUBACCESS1', 'Gli utenti anonimi non possono scrivere.'); //The administrator has disabled public write access.
DEFINE('_POST_NO_PUBACCESS2', 'Solo gli utenti registrati e che hanno effettuato l\'accesso<br /> possono scrivere sul forum.'); //Only logged in / registered users<br /> are allowed to contribute to the forum.
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Non ci sono ancora discussioni in questo forum <<'); //>> There are no topics in this forum yet <<
DEFINE('_SHOWCAT_PENDING', 'messaggio/messaggi in sospeso'); //pending message(s)
// userprofile.php
DEFINE('_USER_DELETE', ' spunta questa casella per cancellare la tua firma'); //check this box to delete your signature
DEFINE('_USER_ERROR_A', 'Stai visualizzando questa pagina per errore. Informa l\'amministratore su quale link '); //You came to this page in error. Please inform the administrator on which links 
DEFINE('_USER_ERROR_B', 'hai cliccato per arrivare a qui. L\'amministratore potr&#224; in seguito creare un rapporto.'); //you clicked that got you here. She or he can then file a bug report.
DEFINE('_USER_ERROR_C', 'Grazie!'); //Thank you!
DEFINE('_USER_ERROR_D', 'Numero dell\'errore da include nel report: '); //Error number to include in your report: 
DEFINE('_USER_GENERAL', 'Opzioni generali di profilo'); //'General Profile Options
DEFINE('_USER_MODERATOR', 'Sei stato assegnato come moderatore'); //You are assigned as a Moderator to forums
DEFINE('_USER_MODERATOR_NONE', 'Nessun forum ti &#232; stato assegnato'); //No forums found assigned to you
DEFINE('_USER_MODERATOR_ADMIN', 'Gli amministratori sono dei moderatori in tutti i forum.'); //Admins are moderator on all forums.
DEFINE('_USER_NOSUBSCRIPTIONS', 'Nessuna sottoscrizione trovata'); //No subscriptions found for you
DEFINE('_USER_PREFERED', 'Prefered Viewtype'); //TODO
DEFINE('_USER_PROFILE', 'Profilo di '); //Profile for
DEFINE('_USER_PROFILE_NOT_A', 'Il tuo profilo '); //Your profile could 
DEFINE('_USER_PROFILE_NOT_B', 'non'); //not
DEFINE('_USER_PROFILE_NOT_C', ' pu&#242; essere aggiornato.'); //be updated.
DEFINE('_USER_PROFILE_UPDATED', 'Il tuo profilo &#232; stato aggiornato.'); //Your profile is updated.
DEFINE('_USER_RETURN_A', 'Se non verrai rediretto al tuo profilo in pochi secondi '); //If you are not taken back to your profile in a few moments 
DEFINE('_USER_RETURN_B', 'clicca qua'); //click here
DEFINE('_USER_SUBSCRIPTIONS', 'Le tue sottoscrizioni'); //Your Subscriptions
DEFINE('_USER_UNSUBSCRIBE', 'Annulla sottoscrizione'); //Unsubscribe
DEFINE('_USER_UNSUBSCRIBE_A', 'Tu '); //You could 
DEFINE('_USER_UNSUBSCRIBE_B', 'non'); //not
DEFINE('_USER_UNSUBSCRIBE_C', ' puoi annullare la sottoscrizione a questa discussione.'); //be unsubscribed from the topic.
DEFINE('_USER_UNSUBSCRIBE_YES', 'Hai annullato la sottoscrizione alla discussione.'); //You have been unsubscribed from the topic.
DEFINE('_USER_DELETEAV', ' spunta questa casella per cancellare la tua immagine personale'); //check this box to delete your Avatar
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Ordinamento dei messaggi'); //Preferred Message Ordering
DEFINE('_USER_ORDER_DESC', 'Messaggi pi&#249; nuovi in cima'); //Last post first
DEFINE('_USER_ORDER_ASC', 'Messaggi pi&#249; vecchi in cima'); //First post first
// view.php
DEFINE('_VIEW_DISABLED', 'L\'amministratore ha vietato la scrittura pubblica'); //The administrator has disabled public write access.
DEFINE('_VIEW_POSTED', 'Scritto da'); //Posted by
DEFINE('_VIEW_SUBSCRIBE', ':: Sottoscrivi questa discussione ::'); //:: Subscribe to this topic ::
DEFINE('_MODERATION_INVALID_ID', 'E\' stato richiesto un id di messaggio non valido.'); //An invalid post id was requested.
DEFINE('_VIEW_NO_POSTS', 'Non ci sono messaggi in questo forum.'); //There are no posts in this forum.
DEFINE('_VIEW_VISITOR', 'Visitor'); //Visitor
DEFINE('_VIEW_ADMIN', 'Amministratore'); //Admin
DEFINE('_VIEW_USER', 'Utente'); //User
DEFINE('_VIEW_MODERATOR', 'Moderatore'); //Moderator
DEFINE('_VIEW_REPLY', 'Rispondi a questo messaggio'); //Reply to this message
DEFINE('_VIEW_EDIT', 'Modifica questo messaggio'); //Edit this message
DEFINE('_VIEW_QUOTE', 'Cita questo messaggio in uno nuovo'); //Quote this message in a new post
DEFINE('_VIEW_DELETE', 'Cancella questo messaggio'); //Delete this message
DEFINE('_VIEW_STICKY', 'Evidenzia questa discussione'); //Set this topic sticky
DEFINE('_VIEW_UNSTICKY', 'Non evidenziare questa discussione'); //Unset this topic sticky
DEFINE('_VIEW_LOCK', 'Blocca questo topic'); //Lock this topic
DEFINE('_VIEW_UNLOCK', 'Sblocca questa discussione'); //Unlock this topic
DEFINE('_VIEW_MOVE', 'Sposta questa discussione su un altro forum'); //Move this topic to another forum
DEFINE('_VIEW_SUBSCRIBETXT', 'Abbonati a questa discussione e ricevi gli aggiornamenti via email'); //Subscribe to this topic and get notified by mail about new posts
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Forum');
DEFINE('_POSTS', 'Messaggi:'); //Posts:
DEFINE('_TOPIC_NOT_ALLOWED', 'Messaggio'); //Post
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Il forum &#232; offline!'); //Forum is OFFLINE!
DEFINE('_PAGE', 'Pagina: '); //Page: 
DEFINE('_NO_POSTS', 'Nessun messaggio'); //No Posts
DEFINE('_CHARS', 'caratteri massimi.'); //characters max.
DEFINE('_HTML_YES', 'HTML &#232; disabilitato'); //HTML is disabled
DEFINE('_YOUR_AVATAR', '<b>La tua immagine personale</b>'); //<b>Your Avatar</b>
DEFINE('_NON_SELECTED', 'Non ancora solezionata <br />'); //Not yet selected <br />
DEFINE('_SET_NEW_AVATAR', 'Seleziona una nuova immagine personale'); //Select new Avatar
DEFINE('_THREAD_UNSUBSCRIBE', 'Annulla sottoscrizione'); //Unsubscribe
DEFINE('_SHOW_LAST_POSTS', 'Discussioni attive per ultime'); //Active topics in last
DEFINE('_SHOW_HOURS', 'ore'); //hours
DEFINE('_SHOW_POSTS', 'Totale: '); //Total: 
DEFINE('_DESCRIPTION_POSTS', 'Ecco i nuovi messaggi per le discussioni attive'); //The newest posts for the active topics are shown
DEFINE('_SHOW_4_HOURS', '4 ore'); //4 Hours
DEFINE('_SHOW_8_HOURS', '8 ore'); //8 Hours
DEFINE('_SHOW_12_HOURS', '12 ore'); //12 Hours
DEFINE('_SHOW_24_HOURS', '24 ore'); //24 Hours
DEFINE('_SHOW_48_HOURS', '48 ore'); //48 Hours
DEFINE('_SHOW_WEEK', 'Settimana'); //Week
DEFINE('_POSTED_AT', 'Inviato alle'); //Posted at
DEFINE('_DATETIME', 'Y/m/d H:i'); //Y/m/d H:i
DEFINE('_NO_TIMEFRAME_POSTS', 'Non ci sono messaggi nel raggio di tempo selezionato.'); //There are no new posts in the timeframe you selected.
DEFINE('_MESSAGE', 'Messaggio'); //Message
DEFINE('_NO_SMILIE', 'no');
DEFINE('_FORUM_UNAUTHORIZIED', 'Questo forum &#232; ristetto ai soli utenti registrati e che hanno eseguito l\'accesso.'); //This forum is open only to registered and logged in users.
DEFINE('_FORUM_UNAUTHORIZIED2', 'Se sei gi&#224; registrato, esegui l\'accesso'); //If you are already registered, please log in first.
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderazione'); //Moderation
DEFINE('_MOD_APPROVE', 'Approva'); //Approve
DEFINE('_MOD_DELETE', 'Cancella'); //Delete
//NEW in RC1
DEFINE('_SHOW_LAST', 'Mostra i messaggi pi&#249; recenti'); //Show most recent message
DEFINE('_POST_WROTE', 'ha scritto'); //wrote
DEFINE('_COM_A_EMAIL', 'Email del forum'); //Board Email Address
DEFINE('_COM_A_EMAIL_DESC', 'Questo &#232; l\'indirizzo email del forum. Deve essere un indirizzo email valido'); //This is the Boards email address. Make this a valid email address
DEFINE('_COM_A_WRAP', 'Spezza le parole pi&#249; lunghe di'); //Wrap Words Longer Than
DEFINE('_COM_A_WRAP_DESC',
    'Imposta il numero massimo di caratteri per una singola parola. Questa funziona di permette di modificare l\'output dei messaggi nel tuo con il tuo tema.<br/> 70 caratteri &#232; probabilmente il massimo per i temi con larghezza fissa, ma dovresti provare un po\'.<br/>Gli URL, di qualsiasi lunghezza, non sono affetti da questa funzione'); //Enter the maximum number of characters a single word may have. This feature allows you to tune the output of Kunena Posts to your template.<br/> 70 characters is probably the maximum for fixed width templates but you might need to experiment a bit.<br/>URLs, no matter how long, are not affected by the wordwrap
DEFINE('_COM_A_SIGNATURE', 'Lunghezza max della firma'); //Max. Signature Length
DEFINE('_COM_A_SIGNATURE_DESC', 'Numero massimo di caratteri permessi per la firma dell\'utente.'); //Maximum number of characters allowed for the user signature.
DEFINE('_SHOWCAT_NOPENDING', 'Nessun messaggio in sospeso'); //No pending message(s)
DEFINE('_COM_A_BOARD_OFSET', 'Differenza con il fuso orario locale'); //Board Time Offset
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Alcuni forum risiedono su server con fuso orario diverso da quello degli utenti. Imposta la differenza di ore da quella locale che verr&#224; usata nei messaggi. Si possono usare numeri positivi o negativi'); //Some boards are located on servers in a different timezone than the users are. Set the Time Offset Kunena must use in the post time in hours. Positive and negative numbers can be used
//New in RC2
DEFINE('_COM_A_BASICS', 'Impostazioni di base');  //Basics
DEFINE('_COM_A_FRONTEND', 'Frontend');  //Frontend
DEFINE('_COM_A_SECURITY', 'Sicurezza'); //Security
DEFINE('_COM_A_AVATARS', 'Immagini personali'); //Avatars
DEFINE('_COM_A_INTEGRATION', 'Integrazione'); //Integration
DEFINE('_COM_A_PMS', 'Permetti l\'invio di messaggi privati'); //Enable private messaging
DEFINE('_COM_A_PMS_DESC',
    'Seleziona il componente per la messaggistica privata, se ne hai installato uno. Selezionando Clexus PM verranno abilitate anche le opzioni per i profili utente di Clexus PM (come ICQ, AIM, Yahoo, MSN e i link al profilo, se supportato dal tema di Kunena in uso)'); //Select the appropriate private messaging component if you have installed any. Selecting Clexus PM will also enable ClexusPM user profile related options (like ICQ, AIM, Yahoo, MSN and profile links if supported by the Kunena template used
DEFINE('_VIEW_PMS', 'Clicca qua per inviare un messaggio privato all\'utente'); //Click here to send a Private Message to this user
//new in RC3
DEFINE('_POST_RE', 'Re:'); //Re:
DEFINE('_BBCODE_BOLD', 'Testo in grassetto: [b]testo[/b] '); //Bold text: [b]text[/b] 
DEFINE('_BBCODE_ITALIC', 'Testo in corsivo: [i]testo[/i]'); //Italic text: [i]text[/i]
DEFINE('_BBCODE_UNDERL', 'Testo sottolineato: [u]testo[/u]'); //Underline text: [u]text[/u]
DEFINE('_BBCODE_QUOTE', 'Citazione: [quote]testo[/quote]'); //Quote text: [quote]text[/quote]
DEFINE('_BBCODE_CODE', 'Codice: [code]codice[/code]'); //Code display: [code]code[/code]
DEFINE('_BBCODE_ULIST', 'Elenco puntato: [ul] [li]testo[/li] [/ul] - Consiglio: deve contenere degli elementi di un elenco'); //Unordered List: [ul] [li]text[/li] [/ul] - Hint: a list must contain List Items
DEFINE('_BBCODE_OLIST', 'Elenco numerato: [ol] [li]testo[/li] [/ol] - Consiglio: deve contenere degli elementi di un elenco'); //Ordered List: [ol] [li]text[/li] [/ol] - Hint: a list must contain List Items
DEFINE('_BBCODE_IMAGE', 'Immagine: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]'); //Image: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]'
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.zzz.com/]Questo &#232; un link[/url]'); //Link: [url=http://www.zzz.com/]This is a link[/url]'
DEFINE('_BBCODE_CLOSA', 'Chiudi tutti i tag'); //Close all tags
DEFINE('_BBCODE_CLOSE', 'Chiudi tutti i tag bbCode'); //Close all open bbCode tags
DEFINE('_BBCODE_COLOR', 'Colore: [color=#FF6600]testo[/color]'); //Color: [color=#FF6600]text[/color]
DEFINE('_BBCODE_SIZE', 'Dimesione: [size=1]dimensione del testo[/size] - Consiglio: usare una dimensione tra 1 e 5'); //Size: [size=1]text size[/size] - Hint: sizes range from 1 to 5
DEFINE('_BBCODE_LITEM', 'Elemento di un elenco: [li] elemento [/li]'); //List Item: [li] list item [/li]
DEFINE('_BBCODE_HINT', 'Aiuto per il bbCode - Consiglio: il codice bbCode pu&#242; essere usato sul testo gi&#224; selezionato!'); //bbCode Help - Hint: bbCode can be used on selected text!'
DEFINE('_COM_A_TAWIDTH', 'Larghezze aree di testo');  //Textarea Width
DEFINE('_COM_A_TAWIDTH_DESC', 'Regola la larghezza delle aree di testo dei messaggi per adattarsi al tuo tema. <br/>La barra delle emoticon verr&#224; spezzata su due linee se la larghezza sar&#224; <= 420 pixels');  //Adjust the width of the reply/post text entry area to match your template. <br/>The Topic Emoticon Toolbar will be wrapped accross two lines if width <= 420 pixels
DEFINE('_COM_A_TAHEIGHT', 'Altezza aree di testo'); //Textarea Height
DEFINE('_COM_A_TAHEIGHT_DESC', 'Regola l\'altezza delle aree di testo dei messaggi per adattarsi al tuo tema');  //Adjust the height of the reply/post text entry area to match your template
DEFINE('_COM_A_ASK_EMAIL', 'Richiedi Email'); //Require Email
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Richiedi un indirizzo email quando gli utenti o i visitatori scrivono un messaggio. Imposta su &quot;No&quot; se vuoi tralasciare questa funzione. Non verr&#224; l\'indirizzo email.');  //Require an email address when users or visitors make a post. Set to &quot;No&quot; if you want this feature to be skipped on the frontend. Posters will not be asked for their email address.

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Gestioni dei livelli');  //Ranks Management
define('_KUNENA_SORTRANKS', 'Ordina in base ai livelli'); //Sort by Ranks

define('_KUNENA_RANKSIMAGE', 'Immagine del livello'); //Rank Image
define('_KUNENA_RANKS', 'Titolo del livello'); //Rank Title
define('_KUNENA_RANKS_SPECIAL', 'Speciale');  //Special
define('_KUNENA_RANKSMIN', 'Numero minimo di messaggi');  //Minimum Post Count
define('_KUNENA_RANKS_ACTION', 'Azioni');  //Actions
define('_KUNENA_NEW_RANK', 'Nuovo livello'); //New Rank


?>
