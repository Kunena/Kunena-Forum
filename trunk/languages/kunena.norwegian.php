<?php
/**
* @version $Id: kunena.norwegian.php 537 2009-02-22 19:59:25Z rued $
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
* Norwegian Translation
* @Copyright Translation (C) 2006 - 2009 Joomla! i Norge
* @translator FB 1.0.x -> 1.0.4 Birger J. Nordølum, Supplements by Gunnar Holmstad, Kristian Wengen and Karl-Gustav Freding
* @translator FB/Kunena 1.0.5 -> 1.0.8 Rune Rasmussen
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'Du har ikke moderator-rettigheter!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Denne tråden ble <b>IKKE</b> lagt til dine favoritter');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Synkroniser Kunenas brukertabell med Joomlas brukertabell');
DEFINE('_POST_FORGOT_EMAIL', 'Du glemte å inkludere din e-postadresse. Klikk på din nettlesers tilbakeknapp for å gå tilbake og forsøke på nytt.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Alt slettet, noen vedleggfiler manglet!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Hovedforum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Dette er forumets hovedkategori. Som en nivå en kategori fungerer den som en konteiner for individuelle forumer. Det blir også referert til som en nivå 1 kategori og er påkrevd for alle Kunena-forumoppsett.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'For å gi ytterligere informasjon for gjester og medlemmer kan forumhodet utnyttes til å vise tekst på toppen av en bestemt kategori.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Velkommen');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Vi oppfordrer nye medlemmer til å poste en kort introduksjon av dem selv i denne forumkategorien. Bli kjent med hverandre og del deres felles interesser.<br>');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '<strong>Velkommen til Kunena-forumet!</strong><br><br>Fortell oss og våre medlemmer hvem du er, hva du liker og hvorfor du ble medlem på denne siden.<br>Vi ønsker alle nye medlemmer velkommen og håper vi får se mye til dere fremover!<br>');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Forslagkasse');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Har du noen tilbakemeldinger eller innspill du vil dele?<br>Ikke vær sjenert, send oss en melding. Vi ønsker å høre fra deg og streber etter å gjøre vår side bedre og mer brukervennlig for våre gjester og medlemmer.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Dette er den valgfrie forumoverskriften for forslagkassen.<br>');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Velkommen til Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Velkommen til Kunena![/b][/size]

Takk for at du valgte Kunena for ditt forumbehov i Joomla.

Kunena, som oversatt fra Swahili betyr "å snakke", er bygget av et lag åpen kildekode-profesjonelle med mål om å tilby toppkvalitets, tett samlet forumløsning for Joomla. Kunena støtter også sosiale nettverksløsninger som Community Builder og JomSocial.


[size=4][b]Andre Kunena-resurser[/b][/size]

[b]Kunena dokumentasjon:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena støtteforum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena nedlastinger:[/b] http://www.kunena.com/downloads
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena blogg:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Send inn dine ideer:[/b] http://www.kunena.com/uservoice
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Følg Kunena på Twitter:[/b] http://www.kunena.com/twitter
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Aktiver fremheving av kode');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Aktiverer Kunenas java-skript for kodefremheving. Aktivering av dette vil fargemerke kode, om dine brukere poster php og lignende kodefragmenter innenfor kode-tagger. Om ditt forum ikke har behov for posting av slike koder vil du kanskje slå av funksjonen for å unngå at kode-tagger blir feilformet.');
DEFINE('_COM_A_RSS_TYPE', 'Standard RSS-type');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Velg mellom RSS-mating etter tråd eller innlegg. Etter tråd betyr at bare en oppføring per tråd vil bli listet i RSS-amtingen, uavhengig av hvor mange innlegg som finnes i den tråden. Etter tråd gir en kortere og mer kompakt RSS-mating, men vil ikke liste alle svar som legges inn.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Etter tråd');
DEFINE('_COM_A_RSS_BY_POST', 'Etter innlegg');
DEFINE('_COM_A_RSS_HISTORY', 'RSS-historie');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Velg hvor mye historie som skal inkluderes i RSS-matingen. Standard er en måned, men du vil kanskje begrense dette til f.eks en uke på forum med mye trafikk.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 uke');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 måned');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 år');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Standard Kunena-side');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Velg standard Kunena-side som skal vises når en forumlenke klikkes, og vises som forumets startside. Standard er "Siste diskusjoner". Bør settes til "Kategorier" for andre maler enn default_ex. Om "Mine diskusjoner" velges vil gjester få "Siste diskusjoner" som sin startside i forumet.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Siste diskusjoner');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Mine innlegg');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_BBCODE_HIDE', 'Følgende er skjult for uregistrerte brukere:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Advarsel-spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Overordnet forum kan ikke være det samme.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Overordnet forum er et underordnet ... .');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum-ID finnes ikke.');
DEFINE('_KUNENA_RECURSION', 'Rekursjon oppdaget.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Du glemte å fylle inn ditt navn.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Du glemte å fylle inn din e-postadresse.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Du glemte å fylle inn et emne.');
DEFINE('_KUNENA_EDIT_TITLE', 'Rediger dine detaljer');
DEFINE('_KUNENA_YOUR_NAME', 'Ditt navn:');
DEFINE('_KUNENA_EMAIL', 'E-post:');
DEFINE('_KUNENA_UNAME', 'Brukernavn:');
DEFINE('_KUNENA_PASS', 'Passord:');
DEFINE('_KUNENA_VPASS', 'Bekreft passord:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Brukerdetaljer ble lagret.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Bidragsytere');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode-innstillinger');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Vis spoiler-tagg i verktøylinjen');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Sett til &quot;Ja&quot; om du vil at [spoiler]-tagg skal listes i tekstbehandlers verktøylinje.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Vis video-tagg i verktøylinjen');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Sett til &quot;Ja&quot; om du vil at [video]-tagg skal listes i tekstbehandlers verktøylinje.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Vis eBay-tagg i verktøylinjen');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Sett til &quot;Ja&quot; om du vil at [ebay]-tagg skal listes i tekstbehandlers verktøylinje.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trim lange nettadresser');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Sett til &quot;Ja&quot; om du vil at visning av lange nettadresser skal kuttes ned. Se innstillinger for trimming av nettadressers begynnelse og slutt.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Begynnelse av trimmede nettadresser');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Antall tegn for første del av trimmede nettadresser. Trim lange nettadresser må være satt til &quot;Ja&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Siste del av trimmede nettadresser');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Antall tegn for siste del av trimmede nettadresser. Trim lange nettadresser må være satt til &quot;Ja&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Automatisk inkludering av YouTube-videoer');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Sett til &quot;Ja&quot; om du vil at YouTube-videoadresser automatisk skal inkluderes.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Automatisk inkludering av eBay-produkter');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Sett til &quot;Ja&quot; om du vil at eBay-produkter og søk automatisk skal inkluderes.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay-elements språkkode');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Det er viktig å angi riktig språkkode ettersom eBay-elementet arver både språk og valutta fra dette. Standard er en-us for ebay.com. Eksempler: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Økt-levetid');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Standard er 1800 [sekunder]. Økt-levetid (tidsutløp) i sekunder, tilsvarende Joomlas økt-levetid. Økt-levetid er viktig for fornyelse (oppdatering) av tilgangskontroll, hvem som er logget på og Ny-indikator. Når en økt utløper blir tilgangrettigheter og Ny-indikator tilbakestilt.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Slå sammen');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Slå denne tråden sammen med');
DEFINE('_POST_MERGE_GHOST', 'Legg igjen skyggekopi av tråd');
DEFINE('_POST_SUCCESS_MERGE', 'Tråd ble vellykket slått sammen.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Sammenslåing feilet.');
DEFINE('_GEN_SPLIT', 'Splitt');
DEFINE('_GEN_DOSPLIT', 'Splitt nå');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Tråd ble vellykket splittet.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Tittel ble vellykket endret.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Tittelendring feilet.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Splitt feilet.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplikat, identisk melding har blitt ignorert.');
DEFINE('_POST_SPLIT_HINT', '<br />Tips: Du kan fremheve et innlegg i trådposisjon hvis du velger det i den andre kolonnen og merker av for ingenting å splitte.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'lenk foreldreløse til emne');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Lenk foreldreløse til nytt emne.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'lenk foreldreløse til forrige post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Lenk foreldreløse til forrige post.');
DEFINE('_POST_MERGE', 'slå sammen');
DEFINE('_POST_MERGE_TITLE', 'Legg denne tråden til målets første innlegg.');
DEFINE('_POST_INVERSE_MERGE', 'omvendt sammenslåing');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Legg målets første innlegg til denne tråden.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Denne tråden ble fjernet fra dine favoritter.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Denne tråden ble <b>IKKE</b> fjernet fra dine favoritter.');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Din forespørsel om fjerning fra favoritter har blitt behandlet.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Denne tråden ble fjernet fra dine abonnementer.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Denne tråden ble <b>IKKE</b> fjernet fra dine abonnementer.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Din forespørsel om fjerning fra abonnementer har blitt behandlet.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Ingen destinasjonkategori ble valgt. Ingenting ble flyttet.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Siste diskusjoner');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Mine diskusjoner');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Diskusjoner jeg har startet eller svart på');
DEFINE('_KUNENA_CATEGORY', 'kategori:');
DEFINE('_KUNENA_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_POSTED_AT', 'Postet');
DEFINE('_KUNENA_AGO', 'siden');
DEFINE('_KUNENA_DISCUSSIONS', 'Diskusjoner');
DEFINE('_KUNENA_TOTAL_THREADS', 'Tråder totalt:');
DEFINE('_SHOW_DEFAULT', 'Standard');
DEFINE('_SHOW_MONTH', 'Måned');
DEFINE('_SHOW_YEAR', 'År');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Kopierer "%src%" til "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'CSS filen bør lagres her...fil="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Vedleggstabell er oppgradert til siste 1.0.x serie struktur!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Vedlegg i meldingstabell er oppgradert til siste 1.0.x serie struktur!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Kunne ikke slette underkategori i hierarkiet. Ingenting er slettet.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Kunne ikke slette posten(e) - Ingenting annet er slettet');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Kunne ikke slette teksten(e) i posten(e). Oppdater databasen manuelt (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Alt slettet, men klarte ikke å oppdatere poststatistikk til bruker!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Alvorlig databasefeil. Oppdater databasen din manuelt så svarene i emnet samsvarer med det nye forumet");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena-komponenten er avinstallert!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena-komponentens versjon: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generert: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Ingen forum å søke i.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Feil ved tillegging av bruker:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Brukere synkronisert; Slettet:');
DEFINE('_KUNENA_USERSSYNCADD', ', Legg til:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'Brukerprofiler.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Ingen passende profiler funnet for synkronisering.');
DEFINE('_KUNENA_SYNC_USERS', 'Synkroniserer brukere');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Synkroniserer Kunena brukertabell med Joomla! brukertabell');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Send en e-post til en administrator');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC', 'Sett til &quot;Ja&quot; vis du vil ha e-postvarsel for hvert nytt emne, sendt til aktivert systemadministrator(er).');
DEFINE('_KUNENA_RANKS_EDIT', 'Rediger rangering');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Skjul e-post');
DEFINE('_KUNENA_DT_DATE_FMT', '%d/%m/%Y');
DEFINE('_KUNENA_DT_TIME_FMT', '%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT', '%d/%m/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Søndag');
DEFINE('_KUNENA_DT_LDAY_MON', 'Mandag');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Tirsdag');
DEFINE('_KUNENA_DT_LDAY_WED', 'Onsdag');
DEFINE('_KUNENA_DT_LDAY_THU', 'Torsdag');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Fredag');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Lørdag');
DEFINE('_KUNENA_DT_DAY_SUN', 'Søn');
DEFINE('_KUNENA_DT_DAY_MON', 'Man');
DEFINE('_KUNENA_DT_DAY_TUE', 'Tir');
DEFINE('_KUNENA_DT_DAY_WED', 'Ons');
DEFINE('_KUNENA_DT_DAY_THU', 'Tor');
DEFINE('_KUNENA_DT_DAY_FRI', 'Fre');
DEFINE('_KUNENA_DT_DAY_SAT', 'Lør');
DEFINE('_KUNENA_DT_LMON_JAN', 'Januar');
DEFINE('_KUNENA_DT_LMON_FEB', 'Februar');
DEFINE('_KUNENA_DT_LMON_MAR', 'Mars');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'Mai');
DEFINE('_KUNENA_DT_LMON_JUN', 'Juni');
DEFINE('_KUNENA_DT_LMON_JUL', 'Juli');
DEFINE('_KUNENA_DT_LMON_AUG', 'August');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'Oktober');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'Desember');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'Mai');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Okt');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Des');
DEFINE('_KUNENA_CHILD_BOARD', 'Underforum');
DEFINE('_WHO_ONLINE_GUEST', 'Gjest');
DEFINE('_WHO_ONLINE_MEMBER', 'Medlem');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'ingen');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Bildebehandler:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Klikk her for å fortsette...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Bruk!');
DEFINE('_KUNENA_NO_ACCESS', 'Du har ikke adgang til dette forumet!');
DEFINE('_KUNENA_TIME_SINCE', '%time% siden');
DEFINE('_KUNENA_DATE_YEARS', 'År');
DEFINE('_KUNENA_DATE_MONTHS', 'Måneder');
DEFINE('_KUNENA_DATE_WEEKS', 'Uker');
DEFINE('_KUNENA_DATE_DAYS', 'Dager');
DEFINE('_KUNENA_DATE_HOURS', 'Timer');
DEFINE('_KUNENA_DATE_MINUTES', 'Minutter');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Er du sikker på at du vil fjerne eksempeldataen? Dette er uomstøtelig.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumtittel:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forumvisning");
DEFINE('_KUNENA_CLASS_SFX', "CSS klassesuffiks for forumet");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffiks lagt til indeks, vis kategori, viser og tillater forskjellige design pr. forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Brukers endretid');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Sett til 0 for uendelig tid, eller sett hvor lang tid i sekunder fra innsendt eller siste endring det er lov å endre posten.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Brukers endringstid');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Standard 600 [sekunder], tillater lagring av en modifikasjon opp til 600 sekunder, før endrelenken forsvinner');
DEFINE('_KUNENA_HELPPAGE', 'Aktiver hjelpeside');
DEFINE('_KUNENA_HELPPAGE_DESC', 'Hvis satt til &quot;Ja&quot;, en lenke til hjelpesiden vil bli vist.');
DEFINE('_KUNENA_HELPPAGE_IN_FB', 'Vis hjelp i Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC', 'Hvis satt til &quot;Ja&quot;, en hjelpetekst vil bli inkludert i forumet men lenken til ekstern hjelp vil ikke fungerer. <b>Notis:</b> Du burde legge til "InnholdsIDen til hjelp" .');
DEFINE('_KUNENA_HELPPAGE_CID', 'InnholdsID til hjelp');
DEFINE('_KUNENA_HELPPAGE_CID_DESC', 'Du burde sette <b>"JA"</b> "Vis hjelp i Kunena" konfigurasjonen.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Ekstern lenke til hjelpeside');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC', 'Hvis du viser ekstern lenke til hjelpeside, vennligst sett til <b>"NEI"</b> "Vis hjelp i Kunena" konfigurasjonen.');
DEFINE('_KUNENA_RULESPAGE', 'Aktiver regelside');
DEFINE('_KUNENA_RULESPAGE_DESC', 'Hvis satt til &quot;Ja&quot;, en lenke til forumregler vil bli vist på forsiden.');
DEFINE('_KUNENA_RULESPAGE_IN_FB', 'Vis regler i Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC', 'Hvis satt til &quot;Ja&quot; regler vil bli limt inn i forumet, og lenken til en ekstern regelside vil bli deaktivert. <b>Notis:</b> Du burde legge til "InnholdsIDer for regler" .');
DEFINE('_KUNENA_RULESPAGE_CID', 'InnholdsID til regler');
DEFINE('_KUNENA_RULESPAGE_CID_DESC', 'Du burde sette <b>"JA"</b> "Vis regler i Kunena" konfigurasjon.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Ekstern lenke til regelside');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC', 'Hvis du viser ekstern lenke til regelside, vennligst sett til <b>"JA"</b> "Vis regelside i Kunena" konfigurasjonen.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT', 'GD-bibliotek er ikke funnet');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT', 'GD2-bibliotek er ikke funnet');
DEFINE('_KUNENA_GD_INSTALLED', 'GD er tilgjengelig i versjon ');
DEFINE('_KUNENA_GD_NO_VERSION', 'Kan ikke fastslå GD-versjonen');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD er ikke installert, du kan innhente mer informasjon ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT', 'Høyde på lite bilde :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH', 'Bredde på lite bilde :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT', 'Høyde på medium bilde :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH', 'Bredde på medium bilde :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT', 'Høyde på stort bilde :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH', 'Bredde på stort bilde :');
DEFINE('_KUNENA_AVATAR_QUALITY', 'Kvalitet på profilbilde');
DEFINE('_KUNENA_WELCOME', 'Velkommen til Kunena');
DEFINE('_KUNENA_WELCOME_DESC', 'Takk for at du valgte Kunena som din forumløsning. Denne siden vil gi deg et raskt overblikk over de forskjellige statistikkene for forumet. Lenkene på venstre side gir deg kontroll over alle aspektene for forumet. Hver side har instruksjoner for bruk av verktøyene.');
DEFINE('_KUNENA_STATISTIC', 'Statistikk');
DEFINE('_KUNENA_VALUE', 'Verdi');
DEFINE('_GEN_CATEGORY', 'Kategori');
DEFINE('_GEN_STARTEDBY', 'Startet av: ');
DEFINE('_GEN_STATS', 'statistikk');
DEFINE('_STATS_TITLE',' forumstatistikk');
DEFINE('_STATS_GEN_STATS', 'Generell statistikk');
DEFINE('_STATS_TOTAL_MEMBERS', 'Medlemmer:');
DEFINE('_STATS_TOTAL_REPLIES', 'Svar:');
DEFINE('_STATS_TOTAL_TOPICS', 'Emner:');
DEFINE('_STATS_TODAY_TOPICS', 'Emner i dag:');
DEFINE('_STATS_TODAY_REPLIES', 'Svar i dag:');
DEFINE('_STATS_TOTAL_CATEGORIES', 'Kategorier:');
DEFINE('_STATS_TOTAL_SECTIONS', 'Seksjoner:');
DEFINE('_STATS_LATEST_MEMBER', 'Siste medlem:');
DEFINE('_STATS_YESTERDAY_TOPICS', 'Emner i går:');
DEFINE('_STATS_YESTERDAY_REPLIES', 'Svar i går:');
DEFINE('_STATS_POPULAR_PROFILE', '10 mest populære brukere (Treff på profil');
DEFINE('_STATS_TOP_POSTERS', 'Bruker med flest innlegg');
DEFINE('_STATS_POPULAR_TOPICS', 'Mest populære emner');
DEFINE('_COM_A_STATSPAGE', 'Aktiver statistikksiden');
DEFINE('_COM_A_STATSPAGE_DESC', 'Hvis satt til &quot;Ja&quot;, en lenke vil bli lagt til forumet for å vise statistikk. Denne siden vil vise masse forskjellig statistikk om forumet ditt. <em>Statistikksiden vil alltid være tilgjengelig for administratorer!</em>');
DEFINE('_COM_C_JBSTATS', 'Forumstatistikk');
DEFINE('_COM_C_JBSTATS_DESC', 'Forumstatistikk');
DEFINE('_GEN_GENERAL', 'Generell');
DEFINE('_PERM_NO_READ', 'Du har ikke tilstrekkelig rettighet for å få adgang til dette forumet.');
DEFINE('_KUNENA_SMILEY_SAVED', 'Smilefjes lagret');
DEFINE('_KUNENA_SMILEY_DELETED', 'Smilefjes slettet');
DEFINE('_KUNENA_CODE_ALLREADY_EXITS', 'Kode eksisterer allerede');
DEFINE('_KUNENA_MISSING_PARAMETER', 'Mangler parametere');
DEFINE('_KUNENA_RANK_ALLREADY_EXITS', 'Rang eksisterer allerede');
DEFINE('_KUNENA_RANK_DELETED', 'Rang slettet');
DEFINE('_KUNENA_RANK_SAVED', 'Rang lagret');
DEFINE('_KUNENA_DELETE_SELECTED', 'Slett valgte');
DEFINE('_KUNENA_MOVE_SELECTED', 'Flytt valgte');
DEFINE('_KUNENA_REPORT_LOGGED', 'Lagret i loggen');
DEFINE('_KUNENA_GO', 'Start');
DEFINE('_KUNENA_MAILFULL', 'Legg ved hele innlegget i e-posten til abonnenter');
DEFINE('_KUNENA_MAILFULL_DESC', 'Hvis nei, vil abonnenter bare få tilsendt tittelen på innlegget');
DEFINE('_KUNENA_HIDETEXT', 'Vennligst logg inn for å vise innholdet!');
DEFINE('_BBCODE_HIDE', 'Skjult tekst: [hide]skjult tekst her[/hide] - skjul del av innlegg for gjester');
DEFINE('_KUNENA_FILEATTACH', 'Filvedlegg: ');
DEFINE('_KUNENA_FILENAME', 'Filnavn: ');
DEFINE('_KUNENA_FILESIZE', 'Filstørrelse: ');
DEFINE('_KUNENA_MSG_CODE', 'Kode: ');
DEFINE('_KUNENA_CAPTCHA_ON', 'Beskyttelsessystem for søppelpost');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA system På/Av');
DEFINE('_KUNENA_CAPDESC', 'Tast inn kode');
DEFINE('_KUNENA_CAPERR', 'Kode er ikke korrekt!');
DEFINE('_KUNENA_COM_A_REPORT', 'Innleggsrapportering');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Hvis du vil at brukere kan rapportere innlegg, velg ja.');
DEFINE('_KUNENA_REPORT_MSG', 'Innlegg rapportert');
DEFINE('_KUNENA_REPORT_REASON', 'Grunn');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Din beskjed');
DEFINE('_KUNENA_REPORT_SEND', 'Send rapport');
DEFINE('_KUNENA_REPORT', 'Rapporter til en moderator');
DEFINE('_KUNENA_REPORT_RSENDER', 'Avsender av rapport: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Grunn for rapportering: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Rapportbeskjed: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Forfatter til beskjed: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Beskjedemne: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Beskjed: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Innleggslenke: ');
DEFINE('_KUNENA_REPORT_INTRO', 'det ble sendt deg en beskjed grunnet');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Rapport ble sendt!');
DEFINE('_KUNENA_EMOTICONS', 'Emoticons');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smilefjes');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kode');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Endre smilefjes');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Endre smilefjes');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Smilefjes-linje');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Nytt smilefjes');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Flere Smilefjes');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Lukk vindu');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Flere smilefjes');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Velg ett smilefjes');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot støtte');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Aktiver Joomla Mambot støtte');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', '"Min innstilling" profiltillegg');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Tillat endring av brukernavn');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Tillat endring av brukernavn i siden profiltillegg');
DEFINE('_KUNENA_RECOUNTFORUMS', 'Tell kategoristatistikken på nytt');
DEFINE('_KUNENA_RECOUNTFORUMS_DONE', 'Hele kategoristatistikken er telt på nytt.');
DEFINE('_KUNENA_EDITING_REASON', 'Grunn for endring');
DEFINE('_KUNENA_EDITING_LASTEDIT', 'Siste endring');
DEFINE('_KUNENA_BY', 'Av');
DEFINE('_KUNENA_REASON', 'Grunn');
DEFINE('_GEN_GOTOBOTTOM', 'Gå til bunnen');
DEFINE('_GEN_GOTOTOP', 'Gå til toppen');
DEFINE('_STAT_USER_INFO', 'Brukerinfo');
DEFINE('_USER_SHOWEMAIL', 'Vis e-post');
DEFINE('_USER_SHOWONLINE', 'Vis pålogget');
DEFINE('_KUNENA_HIDDEN_USERS', 'Skjulte brukere');
DEFINE('_KUNENA_SAVE', 'Lagre');
DEFINE('_KUNENA_RESET', 'Tilbakestill');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Standardgalleri');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Personlig informasjon');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Sammendrag');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Mitt profilbilde');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Foruminnstillinger');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Utseende og sideoppsett');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Min profilinfo');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Mine innlegg');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Mine abonnementer');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Mine favoritter');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Private meldinger');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Innboks');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Ny melding');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Utboks');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Papirkuv');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Innstillinger');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Kontakter');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Liste over blokkerte');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Tilleggsinformasjon');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Navn');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Brukernavn');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-post');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Brukertype');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Registreringsdato');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Siste besøksdato');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Innlegg');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profilvisning');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Personlig tekst');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Kjønn');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Bursdag');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'År (YYYY) - Måned (MM) - Dag (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Sted');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Dette er ditt ICQ nummer.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Dette er ditt "AOL Instant Messenger" kallenavn.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Dette er ditt "Yahoo! Instant Messenger" kallenavn.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Dette er din "Skype handle".');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Dette er ditt Gtalk kallenavn.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Nettsted');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Nettstedets navn');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Eksempel: Joomla! i Norge');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL til nettstedet');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Eksempel: www.joomlainorge.no');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Din "MSN messenger" e-postadresse.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Signatur');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Mann');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Kvinne');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Innlegg ble slettet');
DEFINE('_KUNENA_DATE_YEAR', 'År');
DEFINE('_KUNENA_DATE_MONTH', 'Måned');
DEFINE('_KUNENA_DATE_WEEK', 'Uke');
DEFINE('_KUNENA_DATE_DAY', 'Dag');
DEFINE('_KUNENA_DATE_HOUR', 'Time');
DEFINE('_KUNENA_DATE_MINUTE', 'Minutt');
DEFINE('_KUNENA_IN_FORUM', 'i forum');
DEFINE('_KUNENA_FORUM_AT', ' Forum på: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Merk, selv om det ikke vises noen boardkode eller smilefjesknapper, kan de fortsatt brukes');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS', 'Forumverktøy');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST', 'Liste over brukere');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS', '%s har <b>%d</b> registrerte brukere');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT', 'Skriv inn en verdi for å gjøre et søk!');
DEFINE ('_KUNENA_USRL_SEARCH', 'Finn bruker');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON', 'Søk');
DEFINE ('_KUNENA_USRL_LIST_ALL', 'Fullstendig liste');
DEFINE ('_KUNENA_USRL_NAME', 'Navn');
DEFINE ('_KUNENA_USRL_USERNAME', 'Brukernavn');
DEFINE ('_KUNENA_USRL_GROUP', 'Gruppe');
DEFINE ('_KUNENA_USRL_POSTS', 'Innlegg');
DEFINE ('_KUNENA_USRL_KARMA', 'Karma');
DEFINE ('_KUNENA_USRL_HITS', 'Treff');
DEFINE ('_KUNENA_USRL_EMAIL', 'E-post');
DEFINE ('_KUNENA_USRL_USERTYPE', 'Brukertype');
DEFINE ('_KUNENA_USRL_JOIN_DATE', 'Innmeldingsdato');
DEFINE ('_KUNENA_USRL_LAST_LOGIN', 'Siste innlogging');
DEFINE ('_KUNENA_USRL_NEVER', 'Aldri');
DEFINE ('_KUNENA_USRL_ONLINE','Status');
DEFINE ('_KUNENA_USRL_AVATAR', 'Bilde');
DEFINE ('_KUNENA_USRL_ASC', 'Stigende');
DEFINE ('_KUNENA_USRL_DESC', 'Synkende');
DEFINE ('_KUNENA_USRL_DISPLAY_NR', 'Vis');
DEFINE ('_KUNENA_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS', 'Tillegg');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST', 'Brukerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC', 'Antall rader i brukerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS', 'Antall rader i brukerliste');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE', 'Påloggingsstatus');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC', 'Vis brukerens påloggingsstatus');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR', 'Vis profilbilde');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME', 'Vis navn');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME', 'Vis brukernavn');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP', 'Vis brukergruppe');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS', 'Vis antall innlegg');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA', 'Vis Karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL', 'Vis e-post');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE', 'Vis brukertype');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE', 'Vis innmeldingsdato');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE', 'Vis siste besøksdato');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS', 'Vis profiltreff');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Databaseveiviser');
DEFINE('_KUNENA_DBMETHOD', 'Velg hvilken metode du vil bruke for å ferdigstille installasjonen:');
DEFINE('_KUNENA_DBCLEAN', 'Ren installasjon');
DEFINE('_KUNENA_DBUPGRADE', 'Oppgrader fra Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Toppnivå kategori');
DEFINE('_KUNENA_REGISTERED', 'Registrert');
DEFINE('_KUNENA_PUBLICBACKEND', 'Åpen backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Velg en for å');
DEFINE('_KUNENA_ERRORSUBS', 'Noe gikk galt når du slettet meldingene og abonnentene');
DEFINE('_KUNENA_WARNING', 'Advarsel...');
DEFINE('_KUNENA_CHMOD1', 'Du må forandre filrettigheten til 766 for å oppdatere filen.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Din konfigurasjonsfil er');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Velg malside');
DEFINE('_KUNENA_CFNW', 'FATAL FEIL: Du kan ikke skrive til konfigurasjonsfilen');
DEFINE('_KUNENA_CFS', 'Konfigurasjonsfil er lagret');
DEFINE('_KUNENA_CFCNBO', 'FATAL FEIL: Filen kunne ikke åpnes.');
DEFINE('_KUNENA_TFINW', 'Du kan ikke skrive til denne filen.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS fil er lagret.');
DEFINE('_KUNENA_SELECTMODTO', 'Velg en moderator for å');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Du må velge et forum for å rydde opp (prune)!');
DEFINE('_KUNENA_DELMSGERROR', 'Sletting av meldinger mislyktes:');
DEFINE('_KUNENA_DELMSGERROR1', 'Sletting av meldingstekst mislyktes:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Sletting av abbonementer mislyktes:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum ryddet (pruned) for');
DEFINE('_KUNENA_PRUNEDAYS', 'dager');
DEFINE('_KUNENA_PRUNEDELETED', 'Slettet:');
DEFINE('_KUNENA_PRUNETHREADS', 'tråder');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Feil ved rydding (pruning) av brukere:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Brukere oppryddet (pruned); Slettet:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'brukerprofiler');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Ingen profiler tilfredsstilte kravet til opprydding (pruning).');
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunenatabeller er oppgradert til versjon');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forumkategori');
DEFINE('_KUNENA_SAMPLWARN1', '-- Forsikre deg om at du laster inn eksempeldata i en tom Kunenatabell. Hvis du har lagt inn noe i tabellene vil du ikke kunne installere eksempeldata!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Eksempelinnlegg 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Eksempelforum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Eksempelinnlegg[/color][/size][/b]\nGratulerer med ditt nye forum!\n\n[url=http://www.kunena.com]- Kunena[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Eksempeldata er lastet inn');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Eksempeldata er fjernet');
DEFINE('_KUNENA_CBADDED', 'Profil fra Community Builder er lagt til');
DEFINE('_KUNENA_IMGDELETED', 'Bilde slettet');
DEFINE('_KUNENA_FILEDELETED', 'Fil slettet');
DEFINE('_KUNENA_NOPARENT', 'Ingen overordnet');
DEFINE('_KUNENA_DIRCOPERR', 'Feil: fil');
DEFINE('_KUNENA_DIRCOPERR1', 'kunne ikke kopieres!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena Forum</strong> Komponent <em>til Joomla! CMS</em> <br />&copy; 2006 - 2009 av Kunena<br>Alle rettigheter forbeholdt.');
DEFINE('_KUNENA_INSTALL2', 'Overflytting/Installasjon :</code></strong><br /><br /><font color="red"><b>vellykket');
// added by aliyar 
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profilinnstillinger');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Hvis du har Clexus PM eller Community Builder komponent installert kan du konfigurere Kunena til å bruke deres brukerprofil.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profilvisning</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Alle foruminnlegg');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Emne');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Startet av');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategorier');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Dato');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Treff');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Ingen innlegg');
DEFINE('_KUNENA_TOTALFAVORITE', 'Foretrukket:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Antall kolonner i underforum ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Antall kolonner i underforum formatert under hovedkategorien ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Emneabonnement valgt som standard?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Velg &quot;Ja&quot; hvis du ønsker at emneabonnement alltid skal være avkrysset');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategori / Forum må ha et navn');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Vis statistikk');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Hvis du vil vise statistikk, velg ja');
DEFINE('_KUNENA_SHOWWHOIS', 'Hvis hvem som er pålogget');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Hvis du ønsker å vise hvem som er pålogget, velg ja');
DEFINE('_KUNENA_STATSGENERAL', 'Vis generell statistikk');
DEFINE('_KUNENA_STATSGENERALDESC', 'Hvis du vil vise generell statistikk, velg ja');
DEFINE('_KUNENA_USERSTATS', 'Vis statistikk over mest populære brukere');
DEFINE('_KUNENA_USERSTATSDESC', 'Hvis du vil vise statistikk over de mest populære brukere, velg ja');
DEFINE('_KUNENA_USERNUM', 'Antall populære brukere');
DEFINE('_KUNENA_USERPOPULAR', 'Vis statistikk over mest populære emner');
DEFINE('_KUNENA_USERPOPULARDESC', 'Hvis du vil vise statistikk over mest populære emner, velg ja');
DEFINE('_KUNENA_NUMPOP', 'Antall populære emner');
DEFINE('_KUNENA_INFORMATION','Kunena-samfunnet er stolt å annonsere utgivelsen av Kunena 1.0.0. Den er en kraftig og stilig forumkomponent for en meget god content management system, Joomla!. Den er basert på det harde arbeidet til Joomlaboardteamet og mesteparten av våre gode ønsker går til dette teamet. Noen av hovedfunksjonene i Kunena som kan nevnes er listet under (i tillegg til JB&#39;s nåværende funksjoner):<br /><br /><ul><li>Et mye mere designervennlig forumsystem. Det er nært SMF malsystem, men har en enklere struktur. Med noen få trinn, kan du modifisere utseende totalt på forumet. Takk går til våre flinke designere i teamet.</li><li>Ubegrenset underkategorisystem med bedre administrasjonssystem.</li><li>Raskere system og bedre kodingsopplevelse for tredjepart.</li><li>Ikke forandret<br /></li><li>Profilboks på toppen av forumet</li><li>Støtte for populære PM systemer, som ClexusPM og Uddeim</li><li>Enkelt tilleggssystem (praktisk i stedet for perfekt)</li><li>Språkvalgt ikonsystem.<br /></li><li>Deling av bildesystem fra andre maler. Velging mellom maler og bildesystemer er mulig</li><li>Du kan legge til Joomla! moduler inne i forummalen. Ville du ha bannere inne i forumet ditt?</li><li>Valg og behandling av favorittråd</li><li>Forumfremvisninger og høydepunkter</li><li>Forumannonseringer og dets felt</li><li>Siste innlegg (Etiketter)</li><li>Statistikk på bunnen</li><li>Hvem&#39;s er pålogget, på hvilken side?</li><li>Kategorispesifikk bildesystem</li><li>Forbedret søkesti</li><li><strong>Joomlaboard importering, SMF er planlagt snart</strong></li><li>RSS, PDF utskrift</li><li>Avansert søk (under utvikling)</li><li>Community builder og Clexus PM profilvalg</li><li>Behandling av profilbilde : CB og Clexus PM valg<br /></li></ul><br />Husk at denne utgivelsen ikke er ment å brukes på produksjonssider, men vi har testet den grundig. Vi planlegger å fortsette med arbeidet på dette prosjektet, som vi gjør med våre andre prosjekter, og vi vil gjerne at du hjelper oss med å lage bridge-free løsning til Joomla! forumer.<br /><br />Dette er et samarbeidsarbeid med flere utviklere og designere som har deltatt og gjort det mulig å realisere. Vi takker alle og ønsker at du setter pris på denne utgaven!<br /><br />Kunena-samfunnet<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instruksjoner');
DEFINE('_KUNENA_FINFO', 'Kunena foruminformasjon');
DEFINE('_KUNENA_CSSEDITOR', 'Skriveprogram for CSS, til Kunenamal');
DEFINE('_KUNENA_PATH', 'Sti:');
DEFINE('_KUNENA_CSSERROR', 'Vær obs:CSS malfilen må ha skriverettigheter for å lagre endringer.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena kontrollpanel for brukerprofiler');
DEFINE('_KUNENA_SORTID', 'sorter på brukerID');
DEFINE('_KUNENA_SORTMOD', 'sorter på moderatorer');
DEFINE('_KUNENA_SORTNAME', 'sorter på navn');
DEFINE('_KUNENA_VIEW', 'Vis');
DEFINE('_KUNENA_NOUSERSFOUND', 'Ingen brukerprofiler funnet.');
DEFINE('_KUNENA_ADDMOD', 'Legg moderator til');
DEFINE('_KUNENA_NOMODSAV', 'Ingen mulige moderatorer er funnet. Les \'meldingen\' nedenfor.');
DEFINE('_KUNENA_NOTEUS', 'MELDING: Kun brukere som har moderatorflagget valgt i deres Kunenaprofil vises her. For å legge til en moderator, gi en bruker et moderatorflagg, gå til  <a href="index2.php?option=com_kunena&task=profiles">Brukeradministrasjon</a> og søk etter brukeren du ønsker å legge til som moedrator. Velg så dennes profil og oppdater den. Moderatorflagg kan bare settes av en administrator. ');
DEFINE('_KUNENA_PROFFOR', 'Profil for');
DEFINE('_KUNENA_GENPROF', 'Generelle profilvalg');
DEFINE('_KUNENA_PREFVIEW', 'Foretrukket visningstype:');
DEFINE('_KUNENA_PREFOR', 'Foretrukket meldingssortering:');
DEFINE('_KUNENA_ISMOD', 'Er moderator:');
DEFINE('_KUNENA_ISADM', '<strong>Ja</strong> (ikke mulig å endre, denne brukeren er en (super)administrator)');
DEFINE('_KUNENA_COLOR', 'Farge');
DEFINE('_KUNENA_UAVATAR', 'Profilbilde til bruker:');
DEFINE('_KUNENA_NS', 'Ikke valgt');
DEFINE('_KUNENA_DELSIG', ' huk av i denne boksen for å slette denne signaturen');
DEFINE('_KUNENA_DELAV', ' huk av i denne boksen for å slette dette profilbildet');
DEFINE('_KUNENA_SUBFOR', 'Abonnementer for');
DEFINE('_KUNENA_NOSUBS', 'Ingen abonnementer funnet for denne brukeren');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Grunnleggende');
DEFINE('_KUNENA_BASICSFORUM', 'Grunnleggende foruminformasjon');
DEFINE('_KUNENA_PARENT', 'Overordnet:');
DEFINE('_KUNENA_PARENTDESC', 'Vær oppmerksom: For å lage en kategori, velg \'Toppnivå kategori\' som en overordnet. En kategori fungerer som en beholder for forumene.<br />Et forum kan  <strong>bare</strong> opprettes innenfor en kategori, ved å velge en tidligere opprettet kategori som overordnet til forumet.<br /> Meldinger kan  <strong>IKKE</strong> legges inn i en kategori; kun i forum.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Forumnavn og beskrivelse');
DEFINE('_KUNENA_NAMEADD', 'Navn:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Beskrivelse:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Avansert forumkonfigurasjon');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Forumsikkerhet og tilgang');
DEFINE('_KUNENA_LOCKEDDESC', 'Velg &quot;Ja&quot; hvis du ønsker å låse dette forumet. Ingen, bortsett fra moderatorer og administratorer kan legge til nye emner og svar i et låst forum (eller flytte innlegg til det).');
DEFINE('_KUNENA_LOCKED1', 'Låst:');
DEFINE('_KUNENA_PUBACC', 'Offentlig tilgangsnivå:');
DEFINE('_KUNENA_PUBACCDESC', 'For å opprette et forum som ikke er tilgjengelig for offentligheten, kan du spesifisere her hvilket minimum brukernivå som kan se eller gå inn på forumet. Standardinstillingen er at minimum brukernivå er satt til &quot;Alle&quot;.<br /><b>Vær oppmerksom på</b>: Hvis du forbeholder en hel kategori til en eller flere grupper vil alle forum i denne kategorien bli skjult for alle som ikke er i disse gruppene. Dette vil skje <b>selv om</b> en eller flere av forumene har et lavere tilgangsnivå! Dette gjelder også for moderatorer; du må legge en moderator til moderatorlisten hvis moderatoren ikke har det nødvendige gruppenivået for å se kategorien. <br /> Dette er uavhengig av om kategorien ikke kan bli moderert, moderatorer kan fortsatt legges til moderatorlisten.');
DEFINE('_KUNENA_CGROUPS', 'Inkluder undergrupper:');
DEFINE('_KUNENA_CGROUPSDESC', 'Skal undergrupper bli gitt tilgang også? Hvis satt til &quot;Nei&quot; tilgang til dette forumet er forbeholdt den valgte gruppen');
DEFINE('_KUNENA_ADMINLEVEL', 'Administrators tilgangsnivå:');
DEFINE('_KUNENA_ADMINLEVELDESC', 'Hvis du lager et forum med restriksjoner mot offentlig tilgang så kan du her spesifiser et ekstra tilgangsnivå for administrator.<br /> Hvis du forbeholder tilgang til forumet til en spesiell brukergruppe fra brukersystemet og ikke spesifiserer en gruppe fra administrasjonssystemet her, så vil ikke administratorer ha mulighet til å se forumet.');
DEFINE('_KUNENA_ADVANCED', 'Avansert');
DEFINE('_KUNENA_CGROUPS1', 'Inkluderer undergrupper:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Skal undergrupper bli gitt tilgang også? Hvis satt til &quot;Nei &quot; så er tilgang til dette forumet forbeholdt den valgte gruppen');
DEFINE('_KUNENA_REV', 'Vurder innlegg:');
DEFINE('_KUNENA_REVDESC', 'Sett til &quot;Ja&quot; hvis du ønsker at innlegg skal vurderes av moderatorer før de publiseres. Dette har kun betydning i et moderert forum!<br />Hvis du velger dette uten at noen moderatorer er valgt vil administratoren(e) for nettstedet ha eneansvar for godkjenning/sletting av innlagte innlegg, siden disse vil bli satt \'på vent\'!');
DEFINE('_KUNENA_MOD_NEW', 'Moderering');
DEFINE('_KUNENA_MODNEWDESC', 'Moderering av forumet og forummoderatorer');
DEFINE('_KUNENA_MOD', 'Moderert:');
DEFINE('_KUNENA_MODDESC', 'Satt til &quot;Ja&quot; hvis du vil ha mulighet til å tilordne moderatorer til dette forumet.<br /><strong>Merk:</strong> Dette betyr ikke at nye innlegg må bli satt til å vurderes før de publiseres!<br /> Du må velge &quot;Vurdering&quot; for det i fanen &quot;Avansert&quot;.<br /><br /> <strong>OBS:</strong> Etter å ha satt modererering til &quot;Ja&quot; må du lagre forumkonfigurasjonenen før du får mulighet til å legge til moderatorer med den nye knappen.');
DEFINE('_KUNENA_MODHEADER', 'Modereringsinstillinger for dette forumet');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderatorer knyttet til dette forumet');
DEFINE('_KUNENA_NOMODS', 'Det er ingen moderator knyttet til dette forumet');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Endre');
DEFINE('_KUNENA_ADD', 'Legg til');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Flytt opp');
DEFINE('_KUNENA_MOVEDOWN', 'Flytt ned');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Alle registrerte');
DEFINE('_KUNENA_EVERYBODY', 'Alle');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Omorganiser');
DEFINE('_KUNENA_CHECKEDOUT', 'Sjekk ut');
DEFINE('_KUNENA_ADMINACCESS', 'Administrator tilgang');
DEFINE('_KUNENA_PUBLICACCESS', 'Offentlig tilgang');
DEFINE('_KUNENA_PUBLISHED', 'Publisert');
DEFINE('_KUNENA_REVIEW', 'Gjennomgå');
DEFINE('_KUNENA_MODERATED', 'Moderert');
DEFINE('_KUNENA_LOCKED', 'Låst');
DEFINE('_KUNENA_CATFOR', 'Kategori / Forum');
DEFINE('_KUNENA_ADMIN', 'Kunena administrasjon');
DEFINE('_KUNENA_CP', 'Kunena kontrollpanel');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Integrasjon for profilbilde');
DEFINE('_COM_A_RANKS_SETTINGS', 'Rangering');
DEFINE('_COM_A_RANKING_SETTINGS', 'Innstillinger for rangering');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Innstillinger for profilbilde');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Sikkerhetsinnstillinger');
DEFINE('_COM_A_BASIC_SETTINGS', 'Grunninnstillinger');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Tillat favoritter');
DEFINE('_COM_A_FAVORITES_DESC', 'Velg &quot;Ja&quot; dersom du vil tillate registrerte brukere å legge emner til sine favoritter ');
DEFINE('_USER_UNFAVORITE_ALL', 'Marker boksen for å <b><u>fjerne alle emnefavoritter</u></b> (inkludert usynlige emner - for problemløsning)');
DEFINE('_VIEW_FAVORITETXT', 'Lagre dette emnet som favoritt ');
DEFINE('_USER_UNFAVORITE_YES', 'Du har slettet emnet fra dine favoritter');
DEFINE('_POST_FAVORITED_TOPIC', 'Favoritten er lagret.');
DEFINE('_VIEW_UNFAVORITETXT', 'Fjern fra favoritt');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Fjern abonnementet');
DEFINE('_USER_NOFAVORITES', 'Ingen favoritter');
DEFINE('_POST_SUCCESS_FAVORITE', 'Din favoritt er lagret.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Søkeresultat');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Innlegg pr. side i søkeresultater');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Bruk joomlastil?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Velg "Ja" dersom du vil bruke stilsettet fra Joomla. (class: som sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Vis bilde for underkategori');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Velg "Ja" dersom du vil vise et lite ikon for underkategorier i kategorilisten din. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Vis kunngjøring');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Velg "Ja" dersom du vil vise en boks for kunngjøringer på hovedsiden til forumet.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Vis profilbilde i kategorilister?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Velg "Ja" dersom du vil vise profilbilder i kategorilister.');
DEFINE('_KUNENA_RECENT_POSTS', 'Innstillinger for "Siste innlegg"');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', '"Vis siste innlegg"');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Velg "Ja" dersom du vil vise en liste over de siste innleggene i forumet');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Antall "Siste innlegg"');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Antall nye innlegg som skal vises i listen');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Antall nye innlegg pr. fane ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Antall innlegg pr. enkle fane');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Vis kategori');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Spesifikk kategoriID som du kan vise med "Siste innlegg" modulen. F.eks:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Vis enkelt emne');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Vis et enkelt emne');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Vis svar på emne');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Vis svar på emne (Sv:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Emnelengde');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Hvor mye av innlegget som skal vises');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Vis dato');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Vis når innlegget ble skrevet');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Vis treff');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Vis hvor mange som har lest innlegget');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Vis forfatter');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=brukernavn, 2=navn, 0=ingen');
DEFINE('_KUNENA_STATS', 'Innstillinger for forumstatistikk ');
DEFINE('_KUNENA_CATIMAGEPATH', 'Sti til kategoribilder ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Sti til kategoribilder. Hvis du angir "kategoribilder/" vil stien være "../components/com_kunena/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'ID til kunngøringsmoderator(er) ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Legg til brukerID for moderasjon av kunngjøringer. F.eks: 62,63,73 . Kunngjøringsmoderatorer kan legge til, redigere, eller slette kunngjøringer.');
DEFINE('_KUNENA_FORUM_TOP', 'Forumkategorier ');
DEFINE('_KUNENA_CHILD_BOARDS', 'Underkategorier ');
DEFINE('_KUNENA_QUICKMSG', 'Raskt svar ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Tråder i forum ');
DEFINE('_KUNENA_FORUM', 'Kategorier ');
DEFINE('_KUNENA_SPOTS', 'Søkelys');
DEFINE('_KUNENA_CANCEL', 'avbryt');
DEFINE('_KUNENA_TOPIC', 'EMNE: ');
DEFINE('_KUNENA_POWEREDBY', 'Forumløsning av: ');
// Time Format
DEFINE('_TIME_TODAY', '<b>I dag</b> ');
DEFINE('_TIME_YESTERDAY', '<b>I går</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Siste innlegg');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Hvem er pålogget');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Hovedforum');
DEFINE('_KUNENA_GUEST', 'Gjester');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'visning');
DEFINE('_KUNENA_ATTACH', 'Vedlegg');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Favoritt');
DEFINE('_USER_FAVORITES', 'Dine favoritter');
DEFINE('_THREAD_UNFAVORITE', 'Fjern fra favoritter');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Velkommen');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Vis nye innlegg');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Mitt profilbilde');
DEFINE('_PROFILEBOX_MYPROFILE', 'Min profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Vis mine innlegg');
DEFINE('_PROFILEBOX_GUEST', 'Gjest');
DEFINE('_PROFILEBOX_LOGIN', 'Logg inn');
DEFINE('_PROFILEBOX_REGISTER', 'Registrer deg');
DEFINE('_PROFILEBOX_LOGOUT', 'Logg ut');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Glemt passord?');
DEFINE('_PROFILEBOX_PLEASE', 'Vennligst');
DEFINE('_PROFILEBOX_OR', 'eller');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Siste innlegg');
DEFINE('_RECENT_TOPICS', 'Emne');
DEFINE('_RECENT_AUTHOR', 'Skrevet av');
DEFINE('_RECENT_CATEGORIES', 'Kategorier');
DEFINE('_RECENT_DATE', 'Dato');
DEFINE('_RECENT_HITS', 'Treff');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Annonsering');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Dato');
DEFINE('_ANN_TITLE', 'Tittel');
DEFINE('_ANN_SORTTEXT', 'Kort tekst');
DEFINE('_ANN_LONGTEXT', 'Lang tekst');
DEFINE('_ANN_ORDER', 'Sorter');
DEFINE('_ANN_PUBLISH', 'Publiser');
DEFINE('_ANN_PUBLISHED', 'Publisert');
DEFINE('_ANN_UNPUBLISHED', 'Ikke publisert');
DEFINE('_ANN_EDIT', 'Rediger');
DEFINE('_ANN_DELETE', 'Slett');
DEFINE('_ANN_SUCCESS', 'Suksess');
DEFINE('_ANN_SAVE', 'Lagre');
DEFINE('_ANN_YES', 'Ja');
DEFINE('_ANN_NO', 'Nei');
DEFINE('_ANN_ADD', 'Legg til ny');
DEFINE('_ANN_SUCCESS_EDIT', 'Redigering fullført');
DEFINE('_ANN_SUCCESS_ADD', 'Innlegg lagt til');
DEFINE('_ANN_DELETED', 'Sletting fullført');
DEFINE('_ANN_ERROR', 'FEIL');
DEFINE('_ANN_READMORE', 'Les mer...');
DEFINE('_ANN_CPANEL', 'Kontrollpanel for kunngjøringer');
DEFINE('_ANN_SHOWDATE', 'Vis dato');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Forumstatistikk');
DEFINE('_STAT_GENERAL_STATS', 'Generell statistikk');
DEFINE('_STAT_TOTAL_USERS', 'Brukere totalt');
DEFINE('_STAT_LATEST_MEMBERS', 'Nyeste medlem');
DEFINE('_STAT_PROFILE_INFO', 'Se profilinfo');
DEFINE('_STAT_TOTAL_MESSAGES', 'Meldinger totalt');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Emner totalt');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Kategorier totalt');
DEFINE('_STAT_TOTAL_SECTIONS', 'Seksjoner totalt');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Lest i dag');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Lest i går');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Svar totalt i dag');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Svar totalt i går');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Vis nye innlegg');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Mer om statistikken');
DEFINE('_STAT_USERLIST', 'Liste over brukere');
DEFINE('_STAT_TEAMLIST', 'Forumgruppe');
DEFINE('_STATS_FORUM_STATS', 'Forumstatistikk');
DEFINE('_STAT_POPULAR', 'Mest leste');
DEFINE('_STAT_POPULAR_USER_TMSG', 'brukere (Totalt antall meldinger) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'tråder ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Brukere (Totalt antall profilvisninger) ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Bruker er pålogget nå');
DEFINE('_MODLIST_OFFLINE', 'Bruker frakoblet');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Hvem er pålogget');
DEFINE('_WHO_ONLINE_NOW', 'Pålogget:');
DEFINE('_WHO_ONLINE_MEMBERS', 'medlem(mer)');
DEFINE('_WHO_AND', 'og');
DEFINE('_WHO_ONLINE_GUESTS', 'gjest(er)');
DEFINE('_WHO_ONLINE_USER', 'Bruker');
DEFINE('_WHO_ONLINE_TIME', 'Tid');
DEFINE('_WHO_ONLINE_FUNC', 'Handling');
// Userlist
DEFINE('_USRL_USERLIST', 'Brukerliste');
DEFINE('_USRL_REGISTERED_USERS', '%s har <b>%d</b> registrerte brukere');
DEFINE('_USRL_SEARCH_ALERT', 'Vennligst skriv inn søkeord!');
DEFINE('_USRL_SEARCH', 'Finn bruker');
DEFINE('_USRL_SEARCH_BUTTON', 'Søk');
DEFINE('_USRL_LIST_ALL', 'List opp alle');
DEFINE('_USRL_NAME', 'Navn');
DEFINE('_USRL_USERNAME', 'Brukernavn');
DEFINE('_USRL_EMAIL', 'E-post');
DEFINE('_USRL_USERTYPE', 'Brukertype');
DEFINE('_USRL_JOIN_DATE', 'Registrert dato');
DEFINE('_USRL_LAST_LOGIN', 'Sist innlogget');
DEFINE('_USRL_NEVER', 'Aldri');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'Mine PMer');
DEFINE('_USRL_ASC', 'Stigende');
DEFINE('_USRL_DESC', 'Synkende');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Detaljer');
DEFINE('_USRL_COMPROFILER', 'Profil');
DEFINE('_USRL_THUMBNAIL', 'Foto');
DEFINE('_USRL_READON', 'vis');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Send PM');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Søkeresultat for');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'Innstillinger for brukerliste');
DEFINE('_USRL_ERROR', 'FEIL');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Privat meldingskomponent');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Søkte etter: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Er du sikke på at du vil slette denne meldingen? \n\n MERK: Det er IKKE mulig å gjenopprette slettede meldinger!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Innlegg slettet');
DEFINE('_COM_A_RANKING', 'Rangering');
DEFINE('_COM_A_BOT_REFERENCE', 'Vis botreferansediagram');
DEFINE('_COM_A_MOSBOT', 'Aktiver diskusjonsbot');
DEFINE('_PREVIEW', 'forhåndsvis');
DEFINE('_COM_A_MOSBOT_TITLE', 'Diskusjonsbot');
DEFINE('_COM_A_MOSBOT_DESC', 'Diskusjonsboten gjør det mulig for dine brukere å diskutere artikler på nettsidene dine. Artikkelens tittel vil bli brukt som tittel på emnet.'
.'<br />Dersom emnet ikke allerede eksisterer, vil en ny bli opprettet. Dersom emnet allerede eksisterer, blir brukeren vist til tråden hvor hun/han kan legge inn sitt svar.' . '<br /><strong>Du må laste ned og installere diskusjonsboten separat.</strong>'
.'<br />Gå til <a href="http://www.kunena.com">Kunena-Sidene</a> for mere informasjon.' . '<br />Etter installasjonen må du legge til følgende bot-kommando i artiklene dine:' . '<br />{mos_fb_discuss:<em>kategoriID</em>}'
.'<br /><em>kategoriID</em> er den forumskategorien hvor artiklene skal diskuteres. For å finne den rette kategoriIDen kan du sjekke i forumet ' . 'og finne kategoriens ID i URLen i nettleseren din.'
.'<br />Eksempel: Hvis du vil at artikkelen skal diskuteres i et forum med kategoriID 26, bør Bot-kommandoen se slik ut: {mos_fb_discuss:26}'
.'<br />Dette kan se vanskelig ut, men det gir deg muligheten til å få artiklene dine diskutert i et matchende forum.' );
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Søk');
DEFINE('_FORUM_SEARCHRESULTS', 'viser %s av %s resultater.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'OSS');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Regler');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Rediger denne filen for å legge inn dine regler joomlaroten/administrator/components/com_kunena/language/english.php</li><li>Regel 2</li><li>Regel 3</li><li>Regel 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Forumkode');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Innlegg godkjent');
DEFINE('_MODERATION_DELETE_ERROR', 'FEIL: Kunne ikke slette innlegg(ene)');
DEFINE('_MODERATION_APPROVE_ERROR', 'FEIL: Kunne ikke godkjenne innlegg(ene)');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Det er ingen forum i denne kategorien!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Kunne ikke opprette "spøkelsestråd" i gammelt forum!');
DEFINE('_POST_MOVE_GHOST', 'Legg "spøkelsesmelding" i gammelt forum');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Bytt kategori');
DEFINE('_COM_A_FORUM_JUMP', 'Aktiver "Bytt kategori"');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'Dersom du velger &quot;Ja&quot; vil en nedtrekksmeny vises, hvor du kan velge andre kategorier og raskt bytte kategori.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Regler');
DEFINE('_COM_A_RULESPAGE', 'Aktiver regelside');
DEFINE('_COM_A_RULESPAGE_DESC', 'Dersom du velger &quot;Ja&quot; vil en lenke til en "regelside" vises i toppen av forumet. Denne siden kan brukes til regler, eller hva du måtte ønske. Du kan endre innholdet på denne siden ved å redigere filen rules.php i mappen /joomla_root/components/com_kunena. <em>Sørg for at du alltid har en kopi av denne filen da den vil bli overskrevet ved oppgradering!</em>');
DEFINE('_MOVED_TOPIC', 'FLYTTET:');
DEFINE('_COM_A_PDF', 'Aktiver PDF oppretting');
DEFINE('_COM_A_PDF_DESC', 'Velg &quot;Ja&quot; dersom brukerne skal ha mulighet til å laste ned et enkelt PDF dokument som inneholder teksten i tråden.<br />Det er et <u>enkelt</u> PDF dokument; ingen mark up, ingen pyntet utseende osv, men den inneholder all teksten fra tråden.');
DEFINE('_GEN_PDFA', 'Klikk her for å opprette et PDF dokument fra denne tråden (åpnes i et nytt vindu).');
DEFINE('_GEN_PDF', 'Lag Pdf-fil av tråden');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Klikk her for å se brukerens profil');
DEFINE('_VIEW_ADDBUDDY', 'Klikk her for å legge brukeren til i vennelisten din');
DEFINE('_POST_SUCCESS_POSTED', 'Ditt innlegg er publisert');
DEFINE('_POST_SUCCESS_VIEW', '[ Gå tilbake til emnet ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Gå tilbake til forumet ]');
DEFINE('_RANK_ADMINISTRATOR', 'Administrator');
DEFINE('_RANK_MODERATOR', 'Moderator');
DEFINE('_SHOW_LASTVISIT', 'Siden siste besøk');
DEFINE('_COM_A_BADWORDS_TITLE', 'Filter for uønskede ord');
DEFINE('_COM_A_BADWORDS', 'Bruk filter for uønskede ord');
DEFINE('_COM_A_BADWORDS_DESC', 'Velg &quot;Ja&quot; dersom du vil filtrere bort innlegg som inneholder ord du har definert i "uønskede ord" konfigurasjonen. For å bruke denne funksjonen må du ha "uønskede ord" komponent installert!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Meldingen/innlegget ble slettet fordi det inneholdt et eller flere ord som vi har definert som uønsket på nettstedet vårt *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Opprett "community builder" forumprofil');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC', 'Klikk linken for å opprette de nødvendige felt i "Community Builder" brukerprofil. Etter at de er opprettet kan du fritt flytte dem når som helst som administrator, men ikke endre navn eller innstillinger. Hvis du sletter dem fra community builder administrasjonen, kan du opprette dem igjen ved å bruke denne linken, forøvrig bør du unngå å klikke denne linken flere ganger!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Klikk her <');
DEFINE('_COM_A_COMBUILDER', '"Community Builder" brukerprofiler');
DEFINE('_COM_A_COMBUILDER_DESC', 'Ved å velge &quot;Ja&quot; aktiveres integrasjonen med "Community Builder" komponenten (www.joomlapolis.com). Alle profilfunksjoner i Kunena forumet vil bli behandlet av "Community Builder", også linker til profiler vil referere til brukerprofilen i "Community Builder". Dette valget vil overstyre "Clexus PM" profilinnstillinger dersom begge er satt til &quot;Ja&quot;. Husk å legge til de nødvendige endringene i innstillingene  for "Community Builder" databasen ved å bruke valget nedenfor.');
DEFINE('_COM_A_AVATAR_SRC', 'Bruk profilbilde fra');
DEFINE('_COM_A_AVATAR_SRC_DESC', 'Hvis du har "Clexus PM" eller "Community Builder" komponent installert, kan du konfigurere Kunena til å benytte profilbildet fra "Clexus PM" eller "Community Builder" brukerprofilen. MERK: For "Community Builder" må du ha miniatyrbilde opsjonen slått på fordi forumet bruker miniatyrbilde profilbilder, ikke orginalene.');
DEFINE('_COM_A_KARMA', 'Vis Karma indikator');
DEFINE('_COM_A_KARMA_DESC', 'Veld &quot;Ja&quot; for å vise bruker Karma og relaterte knapper (øk / senk) dersom brukerstatistikk er aktivert.');
DEFINE('_COM_A_DISEMOTICONS', 'Deaktiver emoticons');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Velg &quot;Ja&quot; for å deaktivere grafiske emoticons (smilefjes).');
DEFINE('_COM_C_FBCONFIG', 'Konfigurasjon');
DEFINE('_COM_C_FBCONFIGDESC', 'Konfigurer all funksjonalitet i Kunena');
DEFINE('_COM_C_FORUM', 'Forumadministrasjon');
DEFINE('_COM_C_FORUMDESC', 'Legg til kategorier/forum, og konfigurer dem');
DEFINE('_COM_C_USER', 'Brukeradministrasjon');
DEFINE('_COM_C_USERDESC', 'Grunnleggende bruker- og brukerprofiladministrasjon');
DEFINE('_COM_C_FILES', 'Bla gjennom opplastede filer');
DEFINE('_COM_C_FILESDESC', 'Bla gjennom og administrer opplastede filer');
DEFINE('_COM_C_IMAGES', 'Bla gjennom opplastede bilder');
DEFINE('_COM_C_IMAGESDESC', 'Bla gjennom og administrer opplastede bilder');
DEFINE('_COM_C_CSS', 'Rediger CSS Filen');
DEFINE('_COM_C_CSSDESC', 'Optimaliser Kunena\'s utseende og følelse');
DEFINE('_COM_C_SUPPORT', 'Hjelp til forumet');
DEFINE('_COM_C_SUPPORTDESC', 'Koble til "Kunena"-siden (nytt vindu)');
DEFINE('_COM_C_PRUNETAB', 'Rydd opp i forum');
DEFINE('_COM_C_PRUNETABDESC', 'Fjern gamle tråder (konfigurerbar)');
DEFINE('_COM_C_PRUNEUSERS', 'Rydd opp i brukere');
DEFINE('_COM_C_PRUNEUSERSDESC', 'Synkroniser Kunena sin brukertabell med Joomla! sin brukertabell');
DEFINE('_COM_C_LOADSAMPLE', 'Legg inn eksempeldata');
DEFINE('_COM_C_LOADSAMPLEDESC', 'For å komme lett i gang: Legg inn eksempeldata i en tom Kunena database');
DEFINE('_COM_C_REMOVESAMPLE', 'Slett eksempeldata');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Slett eksempeldata fra din database');
DEFINE('_COM_C_LOADMODPOS', 'Last modulposisjoner');
DEFINE('_COM_C_LOADMODPOSDESC', 'Last modulposisjoner for Kunenamalen');
DEFINE('_COM_C_UPGRADEDESC', 'Få din database oppdatert til siste versjon ved en oppdatering');
DEFINE('_COM_C_BACK', 'Tilbake til Kunena kontrollpanel');
DEFINE('_SHOW_LAST_SINCE', 'Aktive emner siden siste besøk:');
DEFINE('_POST_SUCCESS_REQUEST2', 'Din forespørsel er behandlet');
DEFINE('_POST_NO_PUBACCESS3', 'Klikk her for å registrere deg.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Meldingen er slettet.');
DEFINE('_POST_SUCCESS_EDIT', 'Meldingen har blitt redigert.');
DEFINE('_POST_SUCCESS_MOVE', 'Emnet er flyttet.');
DEFINE('_POST_SUCCESS_POST', 'Din melding er postet.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Ditt abonnement er lagret.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Karma');
DEFINE('_KARMA_SMITE', 'Negativ');
DEFINE('_KARMA_APPLAUD', 'Positiv');
DEFINE('_KARMA_BACK', 'For å komme tilbake til emnet,');
DEFINE('_KARMA_WAIT', 'Du kan kun endre en person\'s karma hver sjette time. <br/>Vennligst vent seks timer før du endrer en persons karma igjen.');
DEFINE('_KARMA_SELF_DECREASE', 'Vennligst ikke prøv å endre din egen Karma!');
DEFINE('_KARMA_SELF_INCREASE', 'Din Karma har blitt senket fordi du prøvde å øke den selv!');
DEFINE('_KARMA_DECREASED', 'Brukerens karma ble senket. Dersom du ikke blir tatt tilbake til emnet snart,');
DEFINE('_KARMA_INCREASED', 'Brukerens karma ble økt. Dersom du ikke blir tatt tilbake til emnet snart,');
DEFINE('_COM_A_TEMPLATE', 'Mal');
DEFINE('_COM_A_TEMPLATE_DESC', 'Velg hvilken mal du vil bruke.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Bildesett');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Velg hvilket bildesett som skal brukes.');
DEFINE('_PREVIEW_CLOSE', 'Lukk dette vinduet');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Bruk statistikkfane');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Velg &quot;Ja&quot; dersom du vil at antall innlegg brukeren har skrevet skal illustreres grafisk i en statistikkfane.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Fargenummer for grafisk illustrasjon');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Angi nummeret for fargen du vil bruke til grafisk illustrasjon i statistikkfanen');
DEFINE('_LATEST_REDIRECT', 'Kunena må (re)etablere dine aksessprivilegier før det kan opprette en liste over de nyeste innleggene for deg.\nVær ikke bekymret, dette er helt normalt etter mer enn 30 minutter uten aktivitet etter innlogging.\nVennligst legg inn ditt søk om igjen.');
DEFINE('_SMILE_COLOUR', 'Farge');
DEFINE('_SMILE_SIZE', 'Størrelse');
DEFINE('_COLOUR_DEFAULT', 'Standard');
DEFINE('_COLOUR_RED', 'Rød');
DEFINE('_COLOUR_PURPLE', 'Lilla');
DEFINE('_COLOUR_BLUE', 'Blå');
DEFINE('_COLOUR_GREEN', 'Grønn');
DEFINE('_COLOUR_YELLOW', 'Gul');
DEFINE('_COLOUR_ORANGE', 'Oransje');
DEFINE('_COLOUR_DARKBLUE', 'Mørkeblå');
DEFINE('_COLOUR_BROWN', 'Brun');
DEFINE('_COLOUR_GOLD', 'Gull');
DEFINE('_COLOUR_SILVER', 'Sølv');
DEFINE('_SIZE_NORMAL', 'Normal');
DEFINE('_SIZE_SMALL', 'Liten');
DEFINE('_SIZE_VSMALL', 'Veldig liten');
DEFINE('_SIZE_BIG', 'Stor');
DEFINE('_SIZE_VBIG', 'Veldig stor');
DEFINE('_IMAGE_SELECT_FILE', 'Velg bildefilen du vil legge ved');
DEFINE('_FILE_SELECT_FILE', 'Velg filen du vil legge ved');
DEFINE('_FILE_NOT_UPLOADED', 'Filen ble ikke lastet opp. Prøv å legge inn på nytt eller rediger innlegget');
DEFINE('_IMAGE_NOT_UPLOADED', 'bildefilen ble ikke lastet opp. Prøv å legge inn på nytt eller rediger innlegget');
DEFINE('_BBCODE_IMGPH', 'Sett inn [img] plassholder i innlegget for det vedlagte bildet');
DEFINE('_BBCODE_FILEPH', 'Sett inn [file] plassholder i innlegget for den vedlagte filen');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Marker boksen for å <b><u>fjerne alle emneabonnement</u></b> (inkludert usynlige emner - for problemløsning)');
DEFINE('_LINK_JS_REMOVED', '<em>Aktiv link som inneholder JavaScript er fjernet automatisk</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Utseende og følelse');
DEFINE('_COM_A_USERS', 'Brukerrelatert');
DEFINE('_COM_A_LENGTHS', 'Ulike innstillinger for lengde');
DEFINE('_COM_A_SUBJECTLENGTH', 'Maks. tittellengde');
DEFINE('_COM_A_SUBJECTLENGTH_DESC', 'Maksimal tittellengde. Det maksimale antallet som støttes av databasen er 255 karakterer. Hvis ditt nettsted er konfigurert til å støtte multi-byte karaktersett som Unicode, UTF-8 eller ikke-ISO-8599-x må du gjøre maksimumet mindre ved å bruke denne formelen:<br/><tt>rund_av_nedover(255/(maks. karaktersett byte-størrelse pr. karakter))</tt><br/> Eksempel for UTF-8, hvor maks. karaktersett byte-størrelse pr. karakter er 4 byter: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Emne/Forum');
DEFINE('_LATEST_NUMBER', 'Nye innlegg');
DEFINE('_COM_A_SHOWNEW', 'Vis nye innlegg');
DEFINE('_COM_A_SHOWNEW_DESC', 'Dersom &quot;Ja&quot; er valgt, vil Kunena vise en indikator for forum som har nye innlegg og hvilke innlegg som er ny siden hans/hennes siste besøk.');
DEFINE('_COM_A_NEWCHAR', '&quot;Ny&quot; indikator');
DEFINE('_COM_A_NEWCHAR_DESC', 'Her definerer du hva som markerer nye innlegg (F.eks.: &quot;!&quot; eller &quot;NYTT!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Siste innleggsskribent');
DEFINE('_GEN_FORUM_NEWPOST', 'Nye innlegg');
DEFINE('_GEN_FORUM_NOTNEW', 'Ingen nye innlegg');
DEFINE('_GEN_UNREAD', 'Ulest emne');
DEFINE('_GEN_NOUNREAD', 'Lest emne');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Marker alle forumene som lest');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Marker dette forumet som lest');
DEFINE('_GEN_FORUM_MARKED', 'Alle innleggene i forumet er markert som lest');
DEFINE('_GEN_ALL_MARKED', 'Alle innlegg er markert som lest');
DEFINE('_IMAGE_UPLOAD', 'Bildeopplasting');
DEFINE('_IMAGE_DIMENSIONS', 'Bildet kan være maksimalt (bredde x høyde - størrelse)');
DEFINE('_IMAGE_ERROR_TYPE', 'Vennligst bare bruk jpeg, gif eller png bildefiler');
DEFINE('_IMAGE_ERROR_EMPTY', 'Vennligst velg en fil før du laster opp');
DEFINE('_IMAGE_ERROR_SIZE', 'Filen overgår maks. størrelse satt av administratoren.');
DEFINE('_IMAGE_ERROR_WIDTH', 'Bildets bredde overgår maks. bredde satt av administratoren.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'Bildets høyde overgår maks. høyde satt av administratoren.');
DEFINE('_IMAGE_UPLOADED', 'Ditt bilde ble lastet opp.');
DEFINE('_COM_A_IMAGE', 'Bilder');
DEFINE('_COM_A_IMGHEIGHT', 'Maks. bildehøyde');
DEFINE('_COM_A_IMGWIDTH', 'Maks. bildebredde');
DEFINE('_COM_A_IMGSIZE', 'Maks. bildefilstørrelse <br/><em>i Kilobyte</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Tillat uregistrerte brukere å laste opp bilder');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Velg &quot;Ja&quot; om du vil at alle (besøkende) skal kunne laste opp bilder.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Tillat registrerte bruker å laste opp bilder');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Velg &quot;Ja&quot; dersom du vil at registrerte og innloggede brukere skal kunne laste opp bilder.<br/> Merk: (Super)administratorer og moderatorer har alltid tilgang til å laste opp bilder.');

//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Opplastinger');
DEFINE('_FILE_TYPES', 'Filen kan være av typen - maks. størrelse');
DEFINE('_FILE_ERROR_TYPE', 'Du kan bare laste opp filer av typen:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Vennligst velg en fil før du laster opp');
DEFINE('_FILE_ERROR_SIZE', 'Filstørrelsen overskrider maks. størrelse satt av administrator.');
DEFINE('_COM_A_FILE', 'Filer');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Tillatte filtyper');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Her spesifiserer du hvilke filtyper som er tillatt å laste opp. Bruk komma til å skille typene og <strong>små bokstaver/tall</strong> uten mellomrom.<br />Eksempel: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Maks. filstørrelse<br/><em>i Kilobyte</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Tillat alle å laste opp filer (Besøkende)');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Velg &quot;Ja&quot; dersom du vil at alle (offentlig) skal kunne laste opp filer.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Tillat registrerte brukere å laste opp filer');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Velg &quot;Ja&quot; dersom du vil at registrerte og innloggede brukere skal kunne laste opp filer.<br/> Merk: (Super)administratorer og moderatorer kan alltid laste opp filer.');
DEFINE('_SUBMIT_CANCEL', 'Ditt innlegg er avbrutt');
DEFINE('_HELP_SUBMIT', 'Klikk her for å lagre ditt innlegg');
DEFINE('_HELP_PREVIEW', 'Klikk her for forhåndsvisning');
DEFINE('_HELP_CANCEL', 'Klikk her for å avbryte ditt innlegg');
DEFINE('_POST_DELETE_ATT', 'Kryss av i boksen for å slette alle bilder og vedlegg også (anbefalt).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Vis om innlegget er redigert');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Velg &quot;Ja&quot; dersom du vil at redigerte innlegg skal markeres som redigert, og når redigeringen skjedde.');
DEFINE('_EDIT_BY', 'Endret av');
DEFINE('_EDIT_AT', 'den');
DEFINE('_UPLOAD_ERROR_GENERAL', 'En feil oppstod under opplasting av ditt profilbilde. Vennligst prøv igjen, eller gi beskjed til systemadministrator');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Bla gjennom opplastede bilder');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Bla gjennom opplastede filer');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Antall opplastede bilder');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Antall opplastede filer');
DEFINE('_COM_A_IMGB_ENLARGE', 'Klikk på bildet for å se dets fulle størrelse');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Klikk på filen for å laste den ned');
DEFINE('_COM_A_IMGB_DUMMY_DESC', 'Alternativ &quot;Bytt med "dummy"&quot; vil bytte ut det valgte bildet med et "dummy"-bilde.<br /> Dette lar deg fjerne filen uten å "brekke" innlegget.<br /><small><em>Husk at noen ganger må nettleseren oppdateres for å se at filen er byttet ut med en "dummy".</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', '<h2>Nåværende "dummybilde"</h2>');
DEFINE('_COM_A_IMGB_REPLACE', 'Bytt med "dummy"');
DEFINE('_COM_A_IMGB_REMOVE', 'Fjern helt');
DEFINE('_COM_A_IMGB_NAME', 'Navn');
DEFINE('_COM_A_IMGB_SIZE', 'Størrelse');
DEFINE('_COM_A_IMGB_DIMS', 'Dimensjoner');
DEFINE('_COM_A_IMGB_CONFIRM', 'Er du helt sikker på at du vil fjerne denne filen? \n Sletting av en fil kan gi et misvisende referanseinnlegg');
DEFINE('_COM_A_IMGB_VIEW', 'Åpne innlegg (for redigering)');
DEFINE('_COM_A_IMGB_NO_POST', 'Intet referanseinnlegg!');
DEFINE('_USER_CHANGE_VIEW', 'Endringer i disse innstillingene vil vises neste gang du besøker forumet.<br /> Hvis du vil endre visningen mens du bruker forumet kan dette gjøres i toppmenyen på forumet.');
DEFINE('_MOSBOT_DISCUSS_A', 'Diskuter denne artikkelen i forumet. (');
DEFINE('_MOSBOT_DISCUSS_B', ' innlegg)');
DEFINE('_POST_DISCUSS', 'Denne tråden gjelder artikkelen');
DEFINE('_COM_A_RSS', 'Aktiver RSS feed');
DEFINE('_COM_A_RSS_DESC', 'RSS feed lar brukere laste ned nye innlegg til sin RSS leserapplikasjon (se <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for et eksempel).');
DEFINE('_LISTCAT_RSS', 'få nye innlegg direkte til ditt skrivebord');
DEFINE('_SEARCH_REDIRECT', 'Kunenaforumet må (re)etablere dine aksessprivilegier før det kan utføre ditt søk.\nDette er helt normalt etter mer enn 30 minutter uten aktivitet.\nVennligst kjør ditt søk igjen.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunenakonfigurasjon');
DEFINE('_COM_A_DISPLAY', 'Vis #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Nåværende innstillinger');
DEFINE('_COM_A_EXPLANATION', 'Forklaring');
DEFINE('_COM_A_BOARD_TITLE', 'Forumtittel');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Forumets navn/tittel');
DEFINE('_COM_A_VIEW_TYPE', 'Standard visning');
DEFINE('_COM_A_VIEW_TYPE_DESC', 'Velg mellom trådbasert, eller flat visning som standard');
DEFINE('_COM_A_THREADS', 'Antall tråder pr. side');
DEFINE('_COM_A_THREADS_DESC', 'Antall tråder som skal vises pr. side');
DEFINE('_COM_A_REGISTERED_ONLY', 'Kun registrerte brukere');
DEFINE('_COM_A_REG_ONLY_DESC', 'Velg &quot;Ja&quot; for å la kun registrerte brukere benytte forumet (lese & skrive), Velg &quot;Nei&quot; for å la enhver besøkende bruke forumet');
DEFINE('_COM_A_PUBWRITE', 'Besøkende kan lese/skrive');
DEFINE('_COM_A_PUBWRITE_DESC', 'Velg &quot;Ja&quot; for å la alle besøkende kunne skrive i forumet, Velg &quot;Nei&quot; for å la alle besøkende kunne lese, mens bare registrerte og innloggede brukere kan skrive');
DEFINE('_COM_A_USER_EDIT', 'La brukere redigere');
DEFINE('_COM_A_USER_EDIT_DESC', 'Velg &quot;Ja&quot; for å la registrerte brukere redigere/endre sine egne innlegg.');
DEFINE('_COM_A_MESSAGE', 'For å lagre endringer i verdiene ovenfor klikker du &quot;Lagre&quot; knappen på toppen.');
DEFINE('_COM_A_HISTORY', 'Vis historie');
DEFINE('_COM_A_HISTORY_DESC', 'Velg &quot;Ja&quot; dersom du vil vise til tidligere innlegg når svar/sitat brukes');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Tillat abonnement');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Velg &quot;Ja&quot; dersom du vil la registrerte brukere abonnere på emner og motta e-post ved nye innlegg');
DEFINE('_COM_A_HISTLIM', 'Historiebegrensning');
DEFINE('_COM_A_HISTLIM_DESC', 'Antall innlegg som skal vises i historien');
DEFINE('_COM_A_FLOOD', 'Flood-beskyttelse');
DEFINE('_COM_A_FLOOD_DESC', 'Antall sekunder en bruker må vente mellom hvert innlegg han/hun legger inn. Velg 0 (null) for å slå Flood-beskyttelse av. MERK: Flood-beskyttelse <em>kan</em> påvirke ytelsen negativt..');
DEFINE('_COM_A_MODERATION', 'Moderatorer e-post');
DEFINE('_COM_A_MODERATION_DESC', 'Velg &quot;Ja&quot; dersom du vil at moderatorer skal motta e-post ved nye innlegg. Merk: Selv om alle (super)administratorer automatisk har alle moderatorprivilegiene må du spesifikt angi dem som moderatorer på de forskjellige forumene for at de også skal motta e-post!');
DEFINE('_COM_A_SHOWMAIL', 'Vis e-post');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Velg &quot;Nei&quot; dersom du ikke vil vise brukernes e-postadresser; ikke engang til registrerte brukere.');
DEFINE('_COM_A_AVATAR', 'Tillat profilbilder');
DEFINE('_COM_A_AVATAR_DESC', 'Velg &quot;Ja&quot; dersom du vil at brukerne skal ha et profilbilde (administreres i profilen deres)');
DEFINE('_COM_A_AVHEIGHT', 'Maks. høyde på profilbilde');
DEFINE('_COM_A_AVWIDTH', 'Maks. bredde på profilbilde');
DEFINE('_COM_A_AVSIZE', 'Maks. filstørrelse på profilbilde<br/><em>i Kilobyte</em>');
DEFINE('_COM_A_USERSTATS', 'Vis brukerstatistikk');
DEFINE('_COM_A_USERSTATS_DESC', 'Velg &quot;Ja&quot; for å vise brukerstatistikk som antall innlegg, brukertype (Administrator, Moderator, bruker, etc.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Tillat opplasting av profilbilde');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Velg &quot;Ja&quot; dersom du vil at brukerne skal kunne laste opp et profilbilde.');
DEFINE('_COM_A_AVATARGALLERY', 'Bruk profilbildegalleri');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Velg &quot;Ja&quot; dersom du vil gi brukere mulighet til å velge profilbilde fra et galleri du tilbyr (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Velg &quot;Ja&quot; dersom du vil vise rangeringen registrerte brukere har, basert på antall innlegg de har skrevet.<br/><strong>Merk at du må aktivere brukerstatistikk under fanen Brukerrelatert for å vise den.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Bruk rangeringsikoner');
DEFINE('_COM_A_RANKINGIMAGES_DESC', 'Velg &quot;Ja&quot; dersom du vil vise rangeringen registrerte brukere har med et ikon (fra components/com_kunena/ranks). Om du slår dette av vil en tekst for rangeringen vises. Sjekk dokumentasjonen på www.kunena.com for mere informasjon om rangeringsikoner (ranking images)');

//email and stuff
$_COM_A_NOTIFICATION = "Melding om nytt innlegg fra ";
$_COM_A_NOTIFICATION1 = "Et nytt innlegg er lagt til i et emne du abonnerer på ";
$_COM_A_NOTIFICATION2 = "Du kan administrere dine abonnement ved å klikke på 'Min profil' linken på forumets startside etter at du har logget inn. Du kan også stoppe abonnement på emner i profilen din.";
$_COM_A_NOTIFICATION3 = "Ikke svar på denne e-posten, da den er automatisk generert.";
$_COM_A_NOT_MOD1 = "Et nytt innlegg er lagt til i et forum du er moderator i ";
$_COM_A_NOT_MOD2 = "Vennligst ta en titt på innlegget etter at du har logget inn på siden.";
DEFINE('_COM_A_NO', 'Nei');
DEFINE('_COM_A_YES', 'Ja');
DEFINE('_COM_A_FLAT', 'Flat');
DEFINE('_COM_A_THREADED', 'Tråd');
DEFINE('_COM_A_MESSAGES', 'Antall innlegg pr. side');
DEFINE('_COM_A_MESSAGES_DESC', 'Antall innlegg som skal vises pr. side');

   //admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Brukernavn');
DEFINE('_COM_A_USERNAME_DESC', 'Velg &quot;Ja&quot; dersom du vil at brukernavnet (som ved innlogging) skal brukes istedenfor brukerens egentlige navn');
DEFINE('_COM_A_CHANGENAME', 'Tillat endring av navn');
DEFINE('_COM_A_CHANGENAME_DESC', 'Velg &quot;Ja&quot; dersom du vil la registrerte brukere ha mulighet til å endre sitt navn når de skriver et innlegg.');

//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum nede for vedlikehold!');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Velg &quot;Ja&quot; dersom du vil ta ned forumkomponenten for vedlikehold. Forumet vil forbli tilgjengelig for (super)administratorer.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Frakoblet forumbeskjed');
DEFINE('_COM_A_PRUNE', 'Rydd i Forum');
DEFINE('_COM_A_PRUNE_NAME', 'Forum å rydde i:');
DEFINE('_COM_A_PRUNE_DESC', 'Rydde-funksjonen lar deg fjerne tråder hvor det ikke har blitt skrevet nye innlegg på det spesifiserte antallet dager. Dette fjerner ikke emner som er prioriterte eller låste; disse må fjernes manuelt. Tråder i låste forum vil ikke fjernes.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Fjern tråder uten nye innlegg i løpet av de siste ');
DEFINE('_COM_A_PRUNE_DAYS', 'dager');
DEFINE('_COM_A_PRUNE_USERS', 'Rydd i brukere');
DEFINE('_COM_A_PRUNE_USERS_DESC', 'Denne funksjonen lar deg fjerne brukere ved å sammenligne brukerlisten til Kunena med brukerlisten til joomla nettstedet ditt. Den vil slette alle Kunenabrukere som har blitt slettet fra brukere i Joomla! <br/>Når du er sikker på at du vil fortsette trykker du &quot;Prune&quot; i menyen ovenfor.');

//general
DEFINE('_GEN_ACTION', 'Handling');
DEFINE('_GEN_AUTHOR', 'Forfatter');
DEFINE('_GEN_BY' ,'av');
DEFINE('_GEN_CANCEL', 'Avbryt');
DEFINE('_GEN_CONTINUE', 'Lagre');
DEFINE('_GEN_DATE', 'Dato');
DEFINE('_GEN_DELETE', 'Slett');
DEFINE('_GEN_EDIT', 'Rediger');
DEFINE('_GEN_EMAIL', 'E-post');
DEFINE('_GEN_EMOTICONS', 'Uttrykksikoner');
DEFINE('_GEN_FLAT', 'Flat');
DEFINE('_GEN_FLAT_VIEW', 'Flat visning');
DEFINE('_GEN_FORUMLIST', 'Forumliste');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Hjelp');
DEFINE('_GEN_HITS', 'Visninger');
DEFINE('_GEN_LAST_POST', 'Siste innlegg');
DEFINE('_GEN_LATEST_POSTS', 'Vis nye innlegg');
DEFINE('_GEN_LOCK', 'Lås');
DEFINE('_GEN_UNLOCK', 'Lås opp');
DEFINE('_GEN_LOCKED_FORUM', 'Forumet er låst');
DEFINE('_GEN_LOCKED_TOPIC', 'Emnet er låst');
DEFINE('_GEN_MESSAGE', 'Melding');
DEFINE('_GEN_MODERATED', 'Forumet modereres; innlegg blir gjennomgått før publisering.');
DEFINE('_GEN_MODERATORS', 'Moderatorer');
DEFINE('_GEN_MOVE', 'Flytt');
DEFINE('_GEN_NAME', 'Navn');
DEFINE('_GEN_POST_NEW_TOPIC', 'Legg inn nytt emne');
DEFINE('_GEN_POST_REPLY', 'Legg inn svar');
DEFINE('_GEN_MYPROFILE', 'Min profil');
DEFINE('_GEN_QUOTE', 'Siter');
DEFINE('_GEN_REPLY', 'Svar');
DEFINE('_GEN_REPLIES', 'Svar');
DEFINE('_GEN_THREADED', 'Tråd');
DEFINE('_GEN_THREADED_VIEW', 'Trådbasert visning');
DEFINE('_GEN_SIGNATURE', 'Signatur');
DEFINE('_GEN_ISSTICKY', 'Emnet er prioritert.');
DEFINE('_GEN_STICKY', 'Prioritert');
DEFINE('_GEN_UNSTICKY', 'Ikke prioritert');
DEFINE('_GEN_SUBJECT', 'Emne');
DEFINE('_GEN_SUBMIT', 'Lagre');
DEFINE('_GEN_TOPIC', 'Emne');
DEFINE('_GEN_TOPICS', 'Emner');
DEFINE('_GEN_TOPIC_ICON', 'Emneikon');
DEFINE('_GEN_SEARCH_BOX', 'Søk i forumet');
$_GEN_THREADED_VIEW = "Trådbasert visning";
$_GEN_FLAT_VIEW = "Flat visning";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Last opp');
DEFINE('_UPLOAD_DIMENSIONS', 'Bildefilen kan maksimalt være (bredde x høyde - størrelse)');
DEFINE('_UPLOAD_SUBMIT', 'Legg til ett nytt profilbilde for opplasting');
DEFINE('_UPLOAD_SELECT_FILE', 'Velg fil');
DEFINE('_UPLOAD_ERROR_TYPE', 'Bruk ett av formatene jpeg, gif eller png bilde');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Vennligst velg en fil før du laster opp');
DEFINE('_UPLOAD_ERROR_NAME', 'Bildefilens navn må kun bestå av alfanumeriske tegn, ikke æ, ø, og å og ingen mellomrom.');
DEFINE('_UPLOAD_ERROR_SIZE', 'Bildefilens størrelse overstiger maks. størrelse satt av administrator.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'Bildefilens bredde overstiger maks. bredde satt av administrator.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Bildefilens høyde overstiger maks. høyde satt av administrator.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "Du valgte ikke et profilbilde fra galleriet...");
DEFINE('_UPLOAD_UPLOADED', 'Ditt profilbilde ble lastet opp.');
DEFINE('_UPLOAD_GALLERY', 'Velg et bilde fra profilbildegalleriet:');
DEFINE('_UPLOAD_CHOOSE', 'Bekreft valg.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'En administrator må opprette dem først fra ');
DEFINE('_LISTCAT_DO', 'De vet hva som må til ');
DEFINE('_LISTCAT_INFORM', 'Informer dem, og be dem skynde seg!');
DEFINE('_LISTCAT_NO_CATS', 'Det er ingen kategorier i forumet ennå.');
DEFINE('_LISTCAT_PANEL', 'Administrasjonspanelet i Joomla! OS CMS.');
DEFINE('_LISTCAT_PENDING', 'ventende melding(er)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Det er ingen ventende meldinger i dette forumet.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Du er i ferd med å slette meldingen');
DEFINE('_POST_ABOUT_DELETE', '<strong>MERK:</strong><br/>
-hvis du sletter et emne (det første innlegget i en tråd) vil også alle underemner bli slettet!
..vurder om du heller vil slette teksten og forfatterens navn i innlegget.....
<br/>
- alle svarinnlegg vil bli flyttet opp 1 plass i trådhierarkiet.');
DEFINE('_POST_CLICK', 'klikk her');
DEFINE('_POST_ERROR', 'Kunne ikke finne brukernavn/E-post. Alvorlig ukjent databasefeil');
DEFINE('_POST_ERROR_MESSAGE', 'En ukjent SQL-feil oppstod og meldingen ble ikke lagt inn. Kontakt administrator dersom problemet fortsetter.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'En feil oppstod, og innlegget ble ikke oppdatert. Vennligst prøv igjen. Kontakt administrator dersom problemet fortsetter.');
DEFINE('_POST_ERROR_TOPIC', 'En feil oppstod ved sletting(ene). Vennligst sjekk feilmeldingen under:');
DEFINE('_POST_FORGOT_NAME', 'Du glemte å skrive ditt navn. Klikk knappen&#146s for å gå tilbake i nettleseren din og prøv igjen.');
DEFINE('_POST_FORGOT_SUBJECT', 'Du glemte å skrive emne. Klikk knappen&#146s for å gå tilbake i nettleseren din og prøv igjen.');
DEFINE('_POST_FORGOT_MESSAGE', 'Du glemte å skrive melding. Klikk knappen&#146s for å gå tilbake i nettleseren din og prøv igjen.');
DEFINE('_POST_INVALID', 'En ugyldig innleggsID ble forespurt.');
DEFINE('_POST_LOCK_SET', 'Emnet er låst.');
DEFINE('_POST_LOCK_NOT_SET', 'Emnet kunne ikke låses.');
DEFINE('_POST_LOCK_UNSET', 'Emnet er låst opp.');
DEFINE('_POST_LOCK_NOT_UNSET', 'Emnet kunne ikke låses opp.');
DEFINE('_POST_MESSAGE', 'Skriv en ny melding i ');
DEFINE('_POST_MOVE_TOPIC', 'Flytt dette emnet til forumet ');
DEFINE('_POST_NEW', 'Skriv en ny melding i: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Ditt abonnement til emnet kunne ikke behandles.');
DEFINE('_POST_NOTIFIED', 'Marker denne boksen for å motta varsel ved svar i dette emnet.');
DEFINE('_POST_STICKY_SET', 'Prioritering av emnet er satt.');
DEFINE('_POST_STICKY_NOT_SET', 'Prioritering av emnet kunne ikke settes.');
DEFINE('_POST_STICKY_UNSET', 'Prioritering av emnet er fjernet.');
DEFINE('_POST_STICKY_NOT_UNSET', 'Prioritering kunne ikke fjernes.');
DEFINE('_POST_SUBSCRIBE', 'abonner');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Du abonnerer nå på emnet.');
DEFINE('_POST_SUCCESS', 'Din melding ble lagt inn');
DEFINE('_POST_SUCCES_REVIEW', 'Din melding ble lagt inn. Den vil bli gjennomgått av en moderator før det legges ut på forumet.');
DEFINE('_POST_SUCCESS_REQUEST', 'Din forespørsel er behandlet. Hvis du ikke blir tatt tilbake til emnet om et øyeblikk,');
DEFINE('_POST_TOPIC_HISTORY', 'Emnehistorie for');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Maks. viser de siste');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'innlegg  -  <i>(siste innlegg først)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Ditt emne kunne ikke flyttes. For å komme tilbake til emnet:');
DEFINE('_POST_TOPIC_FLOOD1', 'Administratoren av dette forumet har aktiver "Flood-beskyttelse" og har bestemt at du må vente ');
DEFINE('_POST_TOPIC_FLOOD2', ' sekunder før du kan legge inn et nytt innlegg.');
DEFINE('_POST_TOPIC_FLOOD3', 'Vennlist klikk&#146s tilbakeknappen i nettleseren din for å komme tilbake til forumet.');
DEFINE('_POST_EMAIL_NEVER', 'E-post adressen vil ikke vises på nettstedet.');
DEFINE('_POST_EMAIL_REGISTERED', 'din e-postadresse vil bare være tilgjengelig for registrerte brukere.');
DEFINE('_POST_LOCKED', 'låst av administrator.');
DEFINE('_POST_NO_NEW', 'Nye svar er ikke tillatt.');
DEFINE('_POST_NO_PUBACCESS1', 'Administratoren har sperret skrivetilgang for besøkende.');
DEFINE('_POST_NO_PUBACCESS2', 'Kun innloggede / registrerte brukere<br /> har tillatelse til å skrive i forumet.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Det er ingen emner i dette forumet ennå <<');
DEFINE('_SHOWCAT_PENDING', 'ventende melding(er))');
// userprofile.php
DEFINE('_USER_DELETE', ' marker denne boksen for å slette din signatur');
DEFINE('_USER_ERROR_A', 'Du kom til denne siden p.g.a. en feil. Vennligst informer administratoren om hva ');
DEFINE('_USER_ERROR_B', 'du klikket på, som sendte deg hit. Han/hun kan rapportere feilen til utviklerne av dette scriptet.');
DEFINE('_USER_ERROR_C', 'Tusen takk!');
DEFINE('_USER_ERROR_D', 'Feilnummer som skal inkluderes i din rapport: ');
DEFINE('_USER_GENERAL', 'Generelle profilinnstillinger');
DEFINE('_USER_MODERATOR', 'Du er angitt som Moderator for forum');
DEFINE('_USER_MODERATOR_NONE', 'Ingen forum er tildelt deg for moderasjon');
DEFINE('_USER_MODERATOR_ADMIN', 'Administratorer er moderatorer i alle forum.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Ingen abonnement ble funnet hos deg');
DEFINE('_USER_PREFERED', 'Foretrukket visning');
DEFINE('_USER_PROFILE', 'Profil for ');
DEFINE('_USER_PROFILE_NOT_A', 'Din profil kunne ');
DEFINE('_USER_PROFILE_NOT_B', 'ikke');
DEFINE('_USER_PROFILE_NOT_C', ' oppdateres.');
DEFINE('_USER_PROFILE_UPDATED', 'Profilen din er oppdatert.');
DEFINE('_USER_RETURN_A', 'Dersom du ikke blir ført tilbake til profilen din etter et øyeblikk ');
DEFINE('_USER_RETURN_B', 'klikk her');
DEFINE('_USER_SUBSCRIPTIONS', 'Dine abonnementer');
DEFINE('_USER_UNSUBSCRIBE', 'Avslutt abonnement');
DEFINE('_USER_UNSUBSCRIBE_A', 'Du kunne ');
DEFINE('_USER_UNSUBSCRIBE_B', 'ikke');
DEFINE('_USER_UNSUBSCRIBE_C', ' avslutte abonnementet på emnet.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Du har avsluttet abonnementet på emnet.');
DEFINE('_USER_DELETEAV', ' merk boksen for å slette ditt profilbilde');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Foretrukket rekkefølge på meldinger');
DEFINE('_USER_ORDER_DESC', 'Siste innlegg først');
DEFINE('_USER_ORDER_ASC', 'Første innlegg først');
// view.php
DEFINE('_VIEW_DISABLED', 'Du må registrere deg og logge inn for å kunne skrive i forumet.');
DEFINE('_VIEW_POSTED', 'Skrevet av');
DEFINE('_VIEW_SUBSCRIBE', ':: Abonner på tråden ::');
DEFINE('_MODERATION_INVALID_ID', 'En ugyldig innleggsID ble forespurt.');
DEFINE('_VIEW_NO_POSTS', 'Det er ingen innlegg i dette forumet.');
DEFINE('_VIEW_VISITOR', 'Besøkende');
DEFINE('_VIEW_ADMIN', 'Admin');
DEFINE('_VIEW_USER', 'Bruker');
DEFINE('_VIEW_MODERATOR', 'Moderator');
DEFINE('_VIEW_REPLY', 'Svar på denne meldingen');
DEFINE('_VIEW_EDIT', 'Rediger denne meldingen');
DEFINE('_VIEW_QUOTE', 'Siter meldingen i et nytt innlegg');
DEFINE('_VIEW_DELETE', 'Slett meldingen');
DEFINE('_VIEW_STICKY', 'Prioriter dette emnet');
DEFINE('_VIEW_UNSTICKY', 'Fjern prioritering av dette emnet');
DEFINE('_VIEW_LOCK', 'Lås emnet');
DEFINE('_VIEW_UNLOCK', 'Lås opp emnet');
DEFINE('_VIEW_MOVE', 'Flytt emnet til et annet forum');
DEFINE('_VIEW_SUBSCRIBETXT', 'Abonner på emnet for å motta e-post ved nye innlegg');

//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Forum');
DEFINE('_POSTS', 'Innlegg:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Innlegg');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forumet er frakoblet!');
DEFINE('_PAGE', 'Side: ');
DEFINE('_NO_POSTS', 'Ingen innlegg');
DEFINE('_CHARS', 'maks. tegn');
DEFINE('_HTML_YES', 'HTML er deaktivert');
DEFINE('_YOUR_AVATAR', '<b>Ditt profilbilde</b>');
DEFINE('_NON_SELECTED', 'Ikke valgt ennå <br>');
DEFINE('_SET_NEW_AVATAR', 'Velg nytt profilbilde');
DEFINE('_THREAD_UNSUBSCRIBE', 'Avslutt abonnement');
DEFINE('_SHOW_LAST_POSTS', 'Aktive emner siste');
DEFINE('_SHOW_HOURS', 'timer');
DEFINE('_SHOW_POSTS', 'Totalt: ');
DEFINE('_DESCRIPTION_POSTS', 'De nyeste innleggene i aktive emner vises');
DEFINE('_SHOW_4_HOURS', '4 timer');
DEFINE('_SHOW_8_HOURS', '8 timer');
DEFINE('_SHOW_12_HOURS', '12 timer');
DEFINE('_SHOW_24_HOURS', '24 timer');
DEFINE('_SHOW_48_HOURS', '48 timer');
DEFINE('_SHOW_WEEK', 'uke');
DEFINE('_POSTED_AT', 'Skrevet');
DEFINE('_DATETIME', 'd/m/Y H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Ingen nye innlegg i den tidsrammen du valgte.');
DEFINE('_MESSAGE', 'Melding');
DEFINE('_NO_SMILIE', 'ingen');
DEFINE('_FORUM_UNAUTHORIZIED', 'Dette forumet er kun åpent for innloggede og registrerte brukere.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Vennligst logg inn først, dersom du allerede er registrert.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderasjon');
DEFINE('_MOD_APPROVE', 'Godkjenn');
DEFINE('_MOD_DELETE', 'Slett');

//NEW in RC1
DEFINE('_SHOW_LAST', 'Vis siste innlegg');
DEFINE('_POST_WROTE', 'skrev');
DEFINE('_COM_A_EMAIL', 'Forumets e-postadresse');
DEFINE('_COM_A_EMAIL_DESC', 'Dette er forumets e-postadresse. Sørg for at det er en gyldig adresse');
DEFINE('_COM_A_WRAP', 'Bryt ord lengre enn');
DEFINE('_COM_A_WRAP_DESC', 'Angi det maksimale antall karakterer ett enkelt ord kan ha. Denne opsjonen lar deg tilpasse hvordan Kunena virker sammen med Joomlamalen din.<br/> 70 karakterer er antakeligvis maksimum for maler med forhåndssatt bredde, men du bør kanskje eksperimentere litt.<br/>URLer, uansett lengde, affiseres ikke av denne opsjonen');
DEFINE('_COM_A_SIGNATURE', 'Maks. signaturlengde');
DEFINE('_COM_A_SIGNATURE_DESC', 'Maksimalt antall tegn tillatt i en brukers signatur.');
DEFINE('_SHOWCAT_NOPENDING', 'Ingen ventende innlegg');
DEFINE('_COM_A_BOARD_OFSET', 'Tidsforskyvning');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Noen forum er lokalisert på servere i andre tidssoner enn der brukerne befinner seg. Sett tidsforskyvningen Kunena skal bruke i timer. Positive og negative tall kan brukes');

//New in RC2
DEFINE('_COM_A_BASICS', 'Grunnleggende');
DEFINE('_COM_A_FRONTEND', 'Grenseflate');
DEFINE('_COM_A_SECURITY', 'Sikkerhet');
DEFINE('_COM_A_AVATARS', 'Profilbilder');
DEFINE('_COM_A_INTEGRATION', 'Integrasjon');
DEFINE('_COM_A_PMS', 'Aktiver private meldinger');
DEFINE('_COM_A_PMS_DESC', 'Velg den riktige komponenten for private meldinger dersom du har installert en slik. Dersom du bruker Clexus PM, vil også ClexusPM profilopsjoner som ICQ, AIM, Yahoo, MSN og profillinker, virke dersom dette støttes av Kunenamalen som brukes.');
DEFINE('_VIEW_PMS', 'Klikk her for å sende en privat melding til denne brukeren');

//new in RC3
DEFINE('_POST_RE', 'Sv:');
DEFINE('_BBCODE_BOLD', 'Fete typer: [b]tekst[/b] ');
DEFINE('_BBCODE_ITALIC', 'Kursiv: [i]tekst[/i]');
DEFINE('_BBCODE_UNDERL', 'Understrek: [u]tekst[/u]');
DEFINE('_BBCODE_QUOTE', 'I anførselstegn: [quote]tekst[/quote]');
DEFINE('_BBCODE_CODE', 'Kode: [code]kode[/code]');
DEFINE('_BBCODE_ULIST', 'Punktliste: [ul] [li]tekst[/li] [/ul] - Hint: en liste må inneholde listeenheter');
DEFINE('_BBCODE_OLIST', 'Numerert list: [ol] [li]tekst[/li] [/ol] - Hint: en liste må inneholde listeenheter');
DEFINE('_BBCODE_IMAGE', 'Bilde: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Lenke: [url=http://www.nnfa.no/]Dette er en lenke[/url]');
DEFINE('_BBCODE_CLOSA', 'Lukk alle tagger');
DEFINE('_BBCODE_CLOSE', 'Lukk alle åpne bb kodetagger');
DEFINE('_BBCODE_COLOR', 'Farge: [color=#FF6600]tekstfarge[/color]');
DEFINE('_BBCODE_SIZE', 'Størrelse: [size=1]skriftstørrelse[/size] - Hint: størrelse mellom 1 og 5');
DEFINE('_BBCODE_LITEM', 'Liste enhet: [li] listeenhet [/li]');
DEFINE('_BBCODE_HINT', 'bb kodehjelp - Hint: bb kode kan brukes på markert tekst!');
DEFINE('_COM_A_TAWIDTH', 'Tekstområde bredde');
DEFINE('_COM_A_TAWIDTH_DESC', 'Juster bredden på innlegg/svar til å passe din mal. <br/>Verktøylinjen for tråd-ikoner vil fordeles over to linjer dersom bredden er mindre enn 420 piksler');
DEFINE('_COM_A_TAHEIGHT', 'Tekstområdets høyde');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Juster høyden på svar/innlegg området til å passe din mal');
DEFINE('_COM_A_ASK_EMAIL', 'Krev e-post');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Krev e-postadresse når brukere eller besøkende skriver innlegg. Velg &quot;Nei&quot; dersom du vil deaktivere denne funksjonen. De som skriver innlegg vil ikke bli spurt om e-postadresse.');

//Rank Administration - Dan Syme/IGD
DEFINE('_KUNENA_RANKS_MANAGE', 'Administrering av rang');
DEFINE('_KUNENA_SORTRANKS', 'Sorter etter rang');

DEFINE('_KUNENA_RANKSIMAGE', 'Bilde til rang');
DEFINE('_KUNENA_RANKS', 'Tittel til rang');
DEFINE('_KUNENA_RANKS_SPECIAL', 'Spesial');
DEFINE('_KUNENA_RANKSMIN', 'Minumium antall innlegg');
DEFINE('_KUNENA_RANKS_ACTION', 'Handlinger');
DEFINE('_KUNENA_NEW_RANK', 'Ny rang');

?>
