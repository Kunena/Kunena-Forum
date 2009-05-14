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
*    Language: Hungarian (informal)
* For version: 1.0.9
*    Encoding: utf-8
*  Translator: József Tamás Herczeg
*      E-mail: info@joomla.org.hu
*     Website: http://www.joomla.org.hu
*	     Date: April 4, 2009
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ( '_VALID_MOS' ) or die( 'A közvetlen hozzáférés ehhez a helyhez nem engedélyezett.' );

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Telepített verzió');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'Licenc');
DEFINE('_KUNENA_PROFILE_NO_USER', 'A felhazsnáló nem létezik.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'A felhasználó még nem kereste föl a fórumot, s nincs profilja.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Találatok');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Speciális keresés');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Kulcsszó szerinti keresés');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Kulcsszavak');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Keresés a hozzászólások szövegében');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Keresés csak a címekben');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Felhasználónév szerinti keresés');
DEFINE('_KUNENA_SEARCH_UNAME', 'Felhasználónév');
DEFINE('_KUNENA_SEARCH_EXACT', 'Pontos név');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'A hozzászólások beküldője');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'A témák indítója');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Tevékenység a témákban');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Keresés beállításai');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'Témák keresése');
DEFINE('_KUNENA_SEARCH_LEAST', 'Legalább');
DEFINE('_KUNENA_SEARCH_MOST', 'Legfeljebb');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Válaszok');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Hozzászólások keresése');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Bármikortól');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Utolsó látogatás óta');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Tegnap óta');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', '1 hete');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 hete');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', '1 hónapja');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 hónapja');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 hónapja');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', '1 éve');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'És újabbak');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'És régebbiek');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Találatok rendezése');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Cím szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'A hozzászólások száma szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'A találatok száma szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'A téma indításának dátuma szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'A hozzászólás dátuma szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'Felhasználónév szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Fórum szerint');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Növekvő sorrendben');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Csökkenő sorrendben');
DEFINE('_KUNENA_SEARCH_START', 'Ugrás találathoz');
DEFINE('_KUNENA_SEARCH_LIMIT5', '5 találat megjelenítése');
DEFINE('_KUNENA_SEARCH_LIMIT10', '10 találat megjelenítése');
DEFINE('_KUNENA_SEARCH_LIMIT15', '15 találat megjelenítése');
DEFINE('_KUNENA_SEARCH_LIMIT20', '20 találat megjelenítése');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Keresés a kategóriákban');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'Minden kategória');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Keresés az alfórumokban is');
DEFINE('_KUNENA_SEARCH_SEND', 'Indítás');
DEFINE('_KUNENA_SEARCH_CANCEL', 'Mégse');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'Nem található az általad megadott keresési kifejezéseket tartalmazó hozzászólás.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'Legalább egy kulcsszónak hoszabbnak kell lennie 3 karakternél!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'AZ');
DEFINE('_POST_NOT_MODERATOR', 'Neked nincsenek moderátori jogaid!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Ezt a témát <b>NEM</b> adtad hozzá a kedvenceidhez');
DEFINE('_COM_C_SYNCEUSERSDESC', 'A Kunena felhasználói tábla szinkronizálása a Joomla felhasználói táblával');
DEFINE('_POST_FORGOT_EMAIL', 'Elfelejtetted megadni az e-mail címedet.  Kattints a böngésződ Vissza gombjára a visszalépéshez, s próbáld meg újra.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Minden törlésre került, néhány mellékletfájl hiányzott!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Fórum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Fő fórum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Ez a fő fórumkategória. Első szintű kategóriaként az egyes fórumok tárolójaként szolgál. 1. szintű kategóriaként is hivatkozunk rá, és bármilyen Kunena fórum telepítés számára kötelező.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'A vendégek és a tagok további tájékoztatása céljából a fórum fejléce kinyitható, hogy a szöveg egy bizonyos kategória legelején jelenjen meg.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Szia, Matyi!');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Az új tagoknak javasoljuk, hogy röviden mutatkozzanak be ebben a fórumkategóriában. Ismerkedjenek meg egymással, és osszák meg a közös érdeklődési köröket.
');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Üdvözöl a Kunena fórum![/b]

Írj saját magadról nekünk és a tagjainknak, miért tetszik ez a weblap, és miért lettél a tagja.

Szívesen látunk minden új tagot, s reméljük, hogy sokszor látunk viszont!
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Ötletláda');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Van valamilyen megosztandó ötleted vagy észrevételed?
Írd meg nyugodtan nekünk. Örömmel hallunk felőled, ugyanis azon fáradozunk, hogy vendégeink és tagjaink számára egyaránt jobbá és felhasználóbarátabbá tegyük a weblapunkat.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Ez az ötletláda tetszőleges fórumfejléce.
');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Üdvözöl a Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Üdvözöl a Kunena![/b][/size]

Köszönjük, hogy a Kunenát választottad közösséged fórummegoldásaként a Joomlához.

A Kunena szuahéliül annyit tesz: "beszélni", a nyílt forráskódot kedvelő profik csapata által, azzal a céllal épített fórum, hogy csúcsminőségű, nagyon eredeti fórummegoldást nyújtson a Joomlához. A Kunena közösségi hálózatépítő komponenseket is támogat, mint a Community Builder és a JomSocial.


[size=4][b]További Kunena-lelőhelyek[/b][/size]

[b]Kunena dokumentáció:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena támogató fórum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena letöltések:[/b] http://www.kunena.com/downloads
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Funkciójavaslatok beküldése:[/b] http://www.kunena.com/uservoice
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Kunena a Twitteren:[/b] http://www.kunena.com/twitter
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Kód szövegkiemelésének engedélyezése');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Engedélyezi a Kunena kódszövegkiemelő javascriptet. Ha a tagok a code címkék közé zárt PHP vagy hasonló kódrészleteket küldenek be, ennek a beállításnak a bekapcsolásával színkódolással jelenítheted meg a kódot. Ha a fórumban nem küldenek be ilyen programozási nyelves hozzászólásokat, akkor kikapcsolhatod, ezáltal elkerülheted a kód címkék hibás formázását.');
DEFINE('_COM_A_RSS_TYPE', 'Alapértelmezett RSS típus');
DEFINE('_COM_A_RSS_TYPE_DESC', 'A téma vagy hozzászólás szerinti RSS-csatorna közül választhatsz. A téma szerint azt jelenti, hogy témánként csak egy tétel kerül listázásra az RSS-csatornában, függetlenül attól, hogy hányan szóltak hozzá ahhoz a témához. A téma szerinti beállítás rövidebb és tömörebb RSS-csatornát eredményez, de nem listáz ki minden választ.');
DEFINE('_COM_A_RSS_BY_THREAD', 'Téma szerint');
DEFINE('_COM_A_RSS_BY_POST', 'Hozzászólás szerint');
DEFINE('_COM_A_RSS_HISTORY', 'RSS-előzmények');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Itt választhatod ki, hogy hány előzményt tartalmazzon az RSS-csatorna. Alapértelmezés az 1 hónap, azonban nagy forgalmú helyeken korlátozhatod 1 hétre.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 hét');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 hónap');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 év');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Alapértelmezett Kunena oldal');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Itt választhatod ki a fórum hivatkozására kattintáskor megjelenő alapértelmezett Kunena oldalt, ahol kezdetben a fórumba belépés történik. Az Új viták az alapértelmezés. A default_ex sablontól eltérő választása esetén a Kategóriákra állítsd. A Vitáim választásakor a vendégek alapértelmezése az Új viták lesz.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Új viták');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Vitáim');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategóriák');
DEFINE('_KUNENA_BBCODE_HIDE', 'A következő a nem regisztrált felhasználók elől rejtett:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Figyelmeztetés elterelő!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Nem lehet ugyanaz a szülőfórum.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'A szülőfórum az alfórumok egyike.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'A fórum azonosítószáma nem létezik.');
DEFINE('_KUNENA_RECURSION', 'Rekurzió észlelhető.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Elfelejtetted beírni a nevedet.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Elfelejtetted beírni az e-mail címedet.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Elfelejtetted beírni a tárgyat.');
DEFINE('_KUNENA_EDIT_TITLE', 'Adatmódosítás');
DEFINE('_KUNENA_YOUR_NAME', 'A neved:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'Felhasználónév:');
DEFINE('_KUNENA_PASS', 'Jelszó:');
DEFINE('_KUNENA_VPASS', 'Jelszó megerősítése:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'A felhasználó adatainak mentése megtörtént.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Készítők');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode beállításai');
DEFINE('_COM_A_SHOWSPOILERTAG', 'A spoiler címke megjelenítése a szerkesztő eszköztárán');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Az &quot;Igen&quot; választása esetén a [spoiler] kódelem látható lesz a hozzászólás-szerkesztő eszköztárán.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'A video címke megjelenítése a szerkesztő eszköztárán');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Az &quot;Igen&quot; választása esetén a [video] kódelem látható lesz a hozzászólás-szerkesztő eszköztárán.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Az eBay címke megjelenítése a szerkesztő eszköztárán');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Az &quot;Igen&quot; választása esetén az [ebay] kódelem látható lesz a szerkesztő eszköztárán.');
DEFINE('_COM_A_TRIMLONGURLS', 'A hosszú URL-ek levágása');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Az &quot;Igen&quot; választása esetén a hosszú URL-ek lerövidítésre kerülnek. Lásd az URL levágás elöl és hátul beállításait.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'A levágott URL-ek első része');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'A levágott URL-ek első része karaktereinek száma. A hosszú URL-ek levágását &quot;Igen&quot;-re kell állítani.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'A levágott URL-ek hátsó része');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'A levágott URL-ek hátsó része karaktereinek száma. A hosszú URL-ek levágását &quot;Igen&quot;-re kell állítani.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'A YouTube videók automatikus beágyazása');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Az &quot;Igen&quot; választása esetén a YouTube videók URL-jei automatikusan beágyazásra kerülnek.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Az eBay árucikkek automatikus beágyazása');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Az &quot;Igen&quot; választása esetén az eBay árucikkek és keresések automatikusan beágyazásra kerülnek.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'Az eBay vezérlő nyelvi kódja');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'A megfelelő nyelvi kód megadása fontos, ugyanis az eBay vezérlő a nyelvet is, és a pénznmet is ez alapján állapítja meg. Alapértelmezett az en-us az ebay.com esetén. Példák: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Munkamenet élettartama');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Alapértelmezés: 1800 [másodperc]. A munkamenet élettartama (időtúllépés) másodpercben hasonló a Joomla munkamenet élettartamához. A munkamenet élettartama a hozzáférési jogok újraszámolásához, a jelenlévők és az ÚJ jelző megjelenítéséhez fontos. Amint ezen időtúllépés után lejár a munkamenet, megtörténik a hozzáférési jogok és az ÚJ jelző alaphelyzetbe állítása.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Összefűzés');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Ezen téma összefűzése a következővel');
DEFINE('_POST_MERGE_GHOST', 'Szellemmásolat hagyása a témáról');
DEFINE('_POST_SUCCESS_MERGE', 'A témák összefűzése sikerült.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Az összefűzés nem sikerült.');
DEFINE('_GEN_SPLIT', 'Kettéosztás');
DEFINE('_GEN_DOSPLIT', 'Indítás');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'A téma kettéosztása sikerült.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'A téma módosítása sikerült.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'A téma módosítása nem sikerült.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'A kettéosztás nem sikerült.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Dupla, megegyező hozzászólás mellőzésre került.');
DEFINE('_POST_SPLIT_HINT', '<br />Tanács: Egy hozzászólást témapozícióba teheted, ha a kijelölöd a második oszlopban, és nem jelölsz be semmi szétbontandót.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'árvák témához csatolása');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Az árvák új téma hozzászólásához csatolása.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'az árvák előző hozzászóláshoz csatolása');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Az árvák előző hozzászóláshoz csatolása.');
DEFINE('_POST_MERGE', 'összefűzés');
DEFINE('_POST_MERGE_TITLE', 'Ezen téma a cél első hozzászóláshoz csatolása.');
DEFINE('_POST_INVERSE_MERGE', 'fordított összefűzés');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'A cél első hozzászólás ezen témához csatolása.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Ez a téma eltávolításra került a kedvenceidből.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Ez a téma <b>NEM</b> került eltávolításra a kedvenceidből');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'A kedvencekből történő eltávolításod kérését feldolgoztuk.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Ez a téma eltávolításra került a témafigyelésből.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Ez a téma <b>NEM</b> került eltávolításra a témafigyelésből');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'A témafigyelésből történő eltávolításod kérését feldolgoztuk.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Nem választottad ki a célkategóriát. Nem került semmi sem áthelyezésre.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Legújabb viták');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Vitáim');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Általam indított viták, vagy melyekhez hozzászóltam');
DEFINE('_KUNENA_CATEGORY', 'Kategória:');
DEFINE('_KUNENA_CATEGORIES', 'Kategóriák');
DEFINE('_KUNENA_POSTED_AT', 'Érkezett');
DEFINE('_KUNENA_AGO', 'napja');
DEFINE('_KUNENA_DISCUSSIONS', 'vita');
DEFINE('_KUNENA_TOTAL_THREADS', 'Témák:');
DEFINE('_SHOW_DEFAULT', 'Alapértelmezett');
DEFINE('_SHOW_MONTH', 'Hónap');
DEFINE('_SHOW_YEAR', 'Év');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', '"%src%" másolása ide: "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'A CSS fájlt ide kellene menteni...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'A mellékletek táblájának frissítése a legújabb 1.0.x sorozat szerkezetére sikerült!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'A hozzászólások táblájában lévő mellékletek frissítése a legújabb 1.0.x sorozat szerkezetére sikerült!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Nem szerepeltethetők a sarjak a hozzászólás-hierarchiában. Nem került semmi sem törlésre.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'A hozzászólás(ok) nem törölhetők - más nem került törlésre');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Nem törölhető(k) a hozzászólás(ok) szövege(i). Frissítsd kézzel az adatbázist (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Minden törlésre került, viszont nem sikerült frissíteni a felhasználók hozzászólásainak statisztikáját!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Súlyos adatbázishiba. Frissítsd kézzel az adatbázist, hogy a témákba beküldött hozzászólások a fórummal is egyezzenek");
DEFINE('_KUNENA_UNIST_SUCCESS', "A Kunena komponens eltávolítása sikerült!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena fórumkomponens, verzió: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Készült: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Nincs kereshető fórum.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Hiba történt a felhasználók hozzáadásakor:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'A felhasználók szinkronizálása kész; Töröltek:');
DEFINE('_KUNENA_USERSSYNCADD', ', hozzáadás:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'felhasználói profilok.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Nem található a szinkronizáláshoz megfelelő profil.');
DEFINE('_KUNENA_SYNC_USERS', 'Felhasználók szinkronizálása');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'A Kunena felhasználói táblájának szinkronizálása a Joomla! felhasználói táblájával');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Az adminisztrátorok értesítése e-mailben');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Az &quot;Igen&quot; választásakor a rendszergazdá(k) értesítést kap(nak) minden új hozzászólásról.');
DEFINE('_KUNENA_RANKS_EDIT', 'Rang módosítása');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'E-mail cím elrejtése');
DEFINE('_KUNENA_DT_DATE_FMT','%Y.%m.%d.');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%Y.%m.%d., %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Vasárnap');
DEFINE('_KUNENA_DT_LDAY_MON', 'Hétfő');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Kedd');
DEFINE('_KUNENA_DT_LDAY_WED', 'Szerda');
DEFINE('_KUNENA_DT_LDAY_THU', 'Csütörtök');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Péntek');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Szombat');
DEFINE('_KUNENA_DT_DAY_SUN', 'V');
DEFINE('_KUNENA_DT_DAY_MON', 'H');
DEFINE('_KUNENA_DT_DAY_TUE', 'K');
DEFINE('_KUNENA_DT_DAY_WED', 'Sze');
DEFINE('_KUNENA_DT_DAY_THU', 'Cs');
DEFINE('_KUNENA_DT_DAY_FRI', 'P');
DEFINE('_KUNENA_DT_DAY_SAT', 'Szo');
DEFINE('_KUNENA_DT_LMON_JAN', 'Január');
DEFINE('_KUNENA_DT_LMON_FEB', 'Február');
DEFINE('_KUNENA_DT_LMON_MAR', 'Március');
DEFINE('_KUNENA_DT_LMON_APR', 'Április');
DEFINE('_KUNENA_DT_LMON_MAY', 'Május');
DEFINE('_KUNENA_DT_LMON_JUN', 'Jónius');
DEFINE('_KUNENA_DT_LMON_JUL', 'Július');
DEFINE('_KUNENA_DT_LMON_AUG', 'Augusztus');
DEFINE('_KUNENA_DT_LMON_SEP', 'Szeptember');
DEFINE('_KUNENA_DT_LMON_OCT', 'Október');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'December');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan.');
DEFINE('_KUNENA_DT_MON_FEB', 'Febr.');
DEFINE('_KUNENA_DT_MON_MAR', 'Márc.');
DEFINE('_KUNENA_DT_MON_APR', 'Ápr.');
DEFINE('_KUNENA_DT_MON_MAY', 'Máj.');
DEFINE('_KUNENA_DT_MON_JUN', 'Jún.');
DEFINE('_KUNENA_DT_MON_JUL', 'Júl.');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug.');
DEFINE('_KUNENA_DT_MON_SEP', 'Szept.');
DEFINE('_KUNENA_DT_MON_OCT', 'Okt.');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov.');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec.');
DEFINE('_KUNENA_CHILD_BOARD', 'Alfórum');
DEFINE('_WHO_ONLINE_GUEST', 'vendég');
DEFINE('_WHO_ONLINE_MEMBER', 'Tag');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'nincs');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Képfeldolgozó:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Kattintson ide a folytatáshoz...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Alkalmaz!');
DEFINE('_KUNENA_NO_ACCESS', 'A hozzáférés ehhez a fórumhoz a számodra nem engedélyezett!');
DEFINE('_KUNENA_TIME_SINCE', '%time%');
DEFINE('_KUNENA_DATE_YEARS', 'éve');
DEFINE('_KUNENA_DATE_MONTHS', 'hónapja');
DEFINE('_KUNENA_DATE_WEEKS','hete');
DEFINE('_KUNENA_DATE_DAYS', 'napja');
DEFINE('_KUNENA_DATE_HOURS', 'órája');
DEFINE('_KUNENA_DATE_MINUTES', 'perce');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'A fórum fejléce:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "A fórum megjelenése");
DEFINE('_KUNENA_CLASS_SFX', "A fórum CSS-osztályának utótagja");
DEFINE('_KUNENA_CLASS_SFXDESC', "Az index, showcat, view oldalakra alkalmazott CSS-utótag, mely fórumonként más arculat tervezését teszi lehetővé.");
DEFINE('_COM_A_USER_EDIT_TIME', 'A felhasználók szerkesztési időtartama');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Korlátlan időtartamhoz írj be 0-t, különben az ablak másodpercben a 
hozzászólástól vagy az utolsó módosítástól engedélyezni a szerkesztést.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Alapértelmezés: 600 [másodperc], 
mely lehetővé teszi egy módosítás tárolását legfeljebb 600 másodpercig, mely után a szerkesztés hivatkozása eltűnik');
DEFINE('_KUNENA_HELPPAGE','A Súgó oldal engedélyezése');
DEFINE('_KUNENA_HELPPAGE_DESC','Az &quot;Igen&quot; választása esetén a súgó oldalra mutató hivatkozás látható a fejléc menüsorában.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','A Súgó megjelenítése a Kunenában');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Az &quot;Igen&quot; választása esetén a súgó tartalma a Kunenában lesz elérhető, és a Súgó külső oldal hivatkozása nem fog működni. <b>Megjegyzés:</b> Meg kell adnod a "Súgó tartalom-azonosítóját" .');
DEFINE('_KUNENA_HELPPAGE_CID','A Súgó tartalom-azonosítója');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Válaszd az <b>"IGEN"</b> "A súgó megjelenítése a Kunenában" lehetőséget.');
DEFINE('_KUNENA_HELPPAGE_LINK',' A külső súgó oldal hivatkozása');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','A súgó külső oldal hivatkozása használata esetén válassza a <b>"NEM"</b> "A súgó megjelenítése a Kunenában" lehetőséget.');
DEFINE('_KUNENA_RULESPAGE','A Szabályok oldal engedélyezése');
DEFINE('_KUNENA_RULESPAGE_DESC','Az &quot;Igen&quot; választása esetén a Szabályokat ismertető oldalra mutató hivatkozás látható a fejléc menüsorában.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','A szabályok megjelenítése a Kunenában');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Az &quot;Igen&quot; választása esetén a szabályok szövege a Kunenában lesz elérhető, és a szabályok külső oldal hivatkozása nem fog működni. <b>Megjegyzés:</b> Meg kell adnod a "Szabályok tartalom-azonosítóját" .');
DEFINE('_KUNENA_RULESPAGE_CID','A Szabályok tartalom-azonosítója');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Válaszd az <b>"IGEN"</b> "A szabályok megjelenítése a Kunenában" lehetőséget.');
DEFINE('_KUNENA_RULESPAGE_LINK',' A szabályok külső oldal hivatkozása');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','A szabályok külső hivatkozásának megjelenítése esetén válassza a <b>"NEM"</b> "A szabályok megjelenítése a Kunenában" lehetőséget.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','A GD függvénytár nem található');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','A GD2 függvénytár nem található');
DEFINE('_KUNENA_GD_INSTALLED','A GD elérhető, verziója: ');
DEFINE('_KUNENA_GD_NO_VERSION','Nem állapítható meg a GD verziószáma');
DEFINE('_KUNENA_GD_NOT_INSTALLED','A GD nem került még telepítésre, további információ: ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Kis kép magassága :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Kis kép szélessége :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Közepes kép magassága :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Közepes kép szélessége :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Nagy kép magassága :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Nagy kép szélessége :');
DEFINE('_KUNENA_AVATAR_QUALITY','Az avatár minősége');
DEFINE('_KUNENA_WELCOME','Üdvözöl a Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','Köszönjük, hogy fórummegoldásként a Kunenát választottad. Ezen a lapon röviden áttekintheted a fórum különféle statisztikáit. A bal oldali menüpontokból a fórum beállításának testreszabását végezheted el. Mindegyik oldalon találsz az eszközök használatát ismertető utasításokat.');
DEFINE('_KUNENA_STATISTIC','Statisztika');
DEFINE('_KUNENA_VALUE','Érték');
DEFINE('_GEN_CATEGORY','Kategória');
DEFINE('_GEN_STARTEDBY','Indította: ');
DEFINE('_GEN_STATS','statisztika');
DEFINE('_STATS_TITLE',' fórum - statisztika');
DEFINE('_STATS_GEN_STATS','Általános statisztika');
DEFINE('_STATS_TOTAL_MEMBERS','Tagok:');
DEFINE('_STATS_TOTAL_REPLIES','Hozzászólások:');
DEFINE('_STATS_TOTAL_TOPICS','Témák:');
DEFINE('_STATS_TODAY_TOPICS','Ma indított témák:');
DEFINE('_STATS_TODAY_REPLIES','Mai hozzászólások:');
DEFINE('_STATS_TOTAL_CATEGORIES','Fórumok:');
DEFINE('_STATS_TOTAL_SECTIONS','Kategóriák:');
DEFINE('_STATS_LATEST_MEMBER','Legújabb tag:');
DEFINE('_STATS_YESTERDAY_TOPICS','Tegnap indított témák:');
DEFINE('_STATS_YESTERDAY_REPLIES','Tegnapi hozzászólások:');
DEFINE('_STATS_POPULAR_PROFILE','Legnépszerűbb 10 tag (profiltalálatok)');
DEFINE('_STATS_TOP_POSTERS','Legaktívabb hozzászólók');
DEFINE('_STATS_POPULAR_TOPICS','Legnépszerűbb témák');
DEFINE('_COM_A_STATSPAGE','A Statisztika oldal engedélyezése');
DEFINE('_COM_A_STATSPAGE_DESC','Az &quot;Igen&quot; választása esetén a fejlécben lévő menüben megjelenik a Fórumstatisztika menüpont. Az innen megnyitható oldalon áttekinthetők a fórum különféle statisztikái. <em>Az adminisztrátor a beállításoktól függetlenül mindig hozzáférhet a Statisztika oldalhoz!</em>');
DEFINE('_COM_C_JBSTATS','Fórumstatisztika');
DEFINE('_COM_C_JBSTATS_DESC','Fórumstatisztika');
define('_GEN_GENERAL','Általános');
define('_PERM_NO_READ','Nem rendelkezel eme fórumhoz történő hozzáféréshez szükséges engedélyekkel.');
DEFINE ('_KUNENA_SMILEY_SAVED','A hangulatjel mentése kész');
DEFINE ('_KUNENA_SMILEY_DELETED','A hangulatjel törlése kész');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Ez a kód már létezik');
DEFINE ('_KUNENA_MISSING_PARAMETER','Hiányosak a paraméterek');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','A rang már létezik');
DEFINE ('_KUNENA_RANK_DELETED','A rang törlése kész');
DEFINE ('_KUNENA_RANK_SAVED','A rang mentése kész');
DEFINE ('_KUNENA_DELETE_SELECTED','Kijelöltek törlése');
DEFINE ('_KUNENA_MOVE_SELECTED','Kijelöltek áthelyezése');
DEFINE ('_KUNENA_REPORT_LOGGED','Naplózott');
DEFINE ('_KUNENA_GO','Ugrás');
DEFINE('_KUNENA_MAILFULL','A teljes hozzászólást tartalmazó értesítés küldése a témafigyelést megrendelőknek');
DEFINE('_KUNENA_MAILFULL_DESC','Ha Nem - a témafigyelést megrendelők csak az új hozzászólások címeit kapják meg');
DEFINE('_KUNENA_HIDETEXT','Kérjük, hogy jelentkezz be e tartalom megtekintéséhez!');
DEFINE('_BBCODE_HIDE','Rejtett szöveg: [hide]valamilyen elrejtendő szöveg[/hide] - a hozzászólás egy részének elrejtése a vendégek elől');
DEFINE('_KUNENA_FILEATTACH','Fájlmelléklet');
DEFINE('_KUNENA_FILENAME','Fájlnév: ');
DEFINE('_KUNENA_FILESIZE','Fájlméret: ');
DEFINE('_KUNENA_MSG_CODE','Kód: ');
DEFINE('_KUNENA_CAPTCHA_ON','Spamvédelmi rendszer használata');
DEFINE('_KUNENA_CAPTCHA_DESC','Itt kapcsolhatod be/ki az antispam és antibot CAPTCHA rendszert');
DEFINE('_KUNENA_CAPDESC','Írd be ide a kódot');
DEFINE('_KUNENA_CAPERR','Pontatlan a kód!');
DEFINE('_KUNENA_COM_A_REPORT', 'Hozzászólás bejelentése');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Ha engedélyezni kívánod a hozzászólások felhasználók által történő bejelentését, akkor válaszd az igent.');
DEFINE('_KUNENA_REPORT_MSG', 'Bejelentett üzenet');
DEFINE('_KUNENA_REPORT_REASON', 'Indok');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Az üzeneted');
DEFINE('_KUNENA_REPORT_SEND', 'Bejelentés küldése');
DEFINE('_KUNENA_REPORT', 'Bejelentés moderátornak');
DEFINE('_KUNENA_REPORT_RSENDER', 'A bejelentés feladója: ');
DEFINE('_KUNENA_REPORT_RREASON', 'A bejelentés indoka: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Bejelentett hozzászólás: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'A hozzászóló neve: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'A hozzászólás tárgya: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Hozzászólás: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'A hozzászólás webcíme: ');
DEFINE('_KUNENA_REPORT_INTRO', 'küldött neked egy üzenetet, mert');
DEFINE('_KUNENA_REPORT_SUCCESS', 'A bejelentés elküldése sikerült!');
DEFINE('_KUNENA_EMOTICONS', 'Hangulatjelek');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Hangulatjel');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kód');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Hangulatjel módosítása');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Hangulatjelek kezelése');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Látható a hangulatjel sávon');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Új hangulatjel');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Több hangulatjel...');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Ablak bezárása');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'További hangulatjelek');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Válassz a hangulatjelek közül');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla mambot támogatás');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'A Joomla mambot támogatás engedélyezése');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Profil beépülő modul beállításai');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'A felhasználónév módosításának engedélyezése');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'A felhasználónév módosításának engedélyezése a profilom beépülő modul oldalon');
DEFINE ('_KUNENA_RECOUNTFORUMS','Kategória-statisztika újraszámolása');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Az összes kategória statisztikájának újraszámolása kész.');
DEFINE ('_KUNENA_EDITING_REASON','A módosítás oka');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Módosítás');
DEFINE ('_KUNENA_BY','Név:');
DEFINE ('_KUNENA_REASON','Indok');
DEFINE('_GEN_GOTOBOTTOM', 'A végére');
DEFINE('_GEN_GOTOTOP', 'Az elejére');
DEFINE('_STAT_USER_INFO', 'Felhasználó adatai');
DEFINE('_USER_SHOWEMAIL', 'Látható az e-mail cím');
DEFINE('_USER_SHOWONLINE', 'Láthatók a jelenlévő felhasználók');
DEFINE('_KUNENA_HIDDEN_USERS', 'Láthatatlan felhasználók');
DEFINE('_KUNENA_SAVE', 'Mentés');
DEFINE('_KUNENA_RESET', 'Alaphelyzet');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Alapértelmezett galéria');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Személyes adatok');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Összegzés');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Avatár');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Fórumbeállítások');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Arculat és elrendezés');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Profiladataim');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Hozzászólásaim');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Témafigyeléseim');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Kedvenc témáim');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Magánüzenetek');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Beérkezett üzenetek');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Új üzenet');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Küldemények');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Szemetes');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Beállítások');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Partnerek');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Blokkoltak névsora');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Kiegészítő adatok');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Név');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Felhasználónév');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-mail cím');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Felhasználócsoport');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Regisztrálás dátuma');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Utolsó látogatás dátuma');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Hozzászólások');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profil találatai');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Rövid személyes mottó');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Nem');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Születési dátum');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Év (ÉÉÉÉ) - Hó (HH) - Nap (NN)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Lakóhely');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Az ICQ-számod');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Az AOL Instant Messenger beceneved');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'A Yahoo! Instant Messenger beceneved');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'Skype');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'A Skype-neved');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTalk');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'A GTalk beceneved');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Weblap');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'A weblap neve');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Pl.: Best of Joomla!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Weblap URL-je');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Pl.: www.bestofjoomla.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Az MSN Messenger e-mail címed');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Aláírás');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Férfi');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Nő');
DEFINE('_KUNENA_BULKMSG_DELETED', 'A hozzászólás(ok) törlése sikerült');
DEFINE('_KUNENA_DATE_YEAR', 'éve');
DEFINE('_KUNENA_DATE_MONTH', 'hónapja');
DEFINE('_KUNENA_DATE_WEEK','hete');
DEFINE('_KUNENA_DATE_DAY', 'napja');
DEFINE('_KUNENA_DATE_HOUR', 'órája');
DEFINE('_KUNENA_DATE_MINUTE', 'perce');
DEFINE('_KUNENA_IN_FORUM', ' a fórumban: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum at: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Megjegyzés: bár a fórumkód és a hangulatjelek nem láthatók, attól függetlenül még használhatod őket');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Fórumeszközök');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Felhasználók névsora');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s fórum - Regisztrált felhasználók száma: <b>%d</b> fő');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Kérjük, hogy írd be a keresendő értéket!');
DEFINE ('_KUNENA_USRL_SEARCH','Felhasználó keresése');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Keresés');
DEFINE ('_KUNENA_USRL_LIST_ALL','Az összes listázása');
DEFINE ('_KUNENA_USRL_NAME','Név');
DEFINE ('_KUNENA_USRL_USERNAME','Felhasználónév');
DEFINE ('_KUNENA_USRL_GROUP','Csoport');
DEFINE ('_KUNENA_USRL_POSTS','Hozzászólások');
DEFINE ('_KUNENA_USRL_KARMA','Karma');
DEFINE ('_KUNENA_USRL_HITS','Találatok');
DEFINE ('_KUNENA_USRL_EMAIL','E-mail cím');
DEFINE ('_KUNENA_USRL_USERTYPE','Felhasználócsoport');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Belépés dátuma');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Utolsó bejelentkezés');
DEFINE ('_KUNENA_USRL_NEVER','Soha');
DEFINE ('_KUNENA_USRL_ONLINE','Állapot');
DEFINE ('_KUNENA_USRL_AVATAR','Kép');
DEFINE ('_KUNENA_USRL_ASC','Növekvő');
DEFINE ('_KUNENA_USRL_DESC','Csökkenő');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Tételek');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%Y.%m.%d.');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Beépülő modulok');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Felhasználók névsora');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','A felhasználók névsora sorainak száma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','A névsor sorainak száma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Online állapot');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Látható a felhasználók online állapota');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Látható az avatár');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Látható a valódi név');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Látható a felhasználónév');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Látható a felhasználócsoport');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Látható a hozzászólások száma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Látható a Karma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Látható az e-mail cím');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Látható a felhasználócsoport');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Látható a belépés dátuma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Látható az utolsó látogatás dátuma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Látható a profiltalálatok száma');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ','Adatbázis varázsló');
DEFINE('_KUNENA_DBMETHOD','Kérjük, válaszd ki, hogy melyik módszerrel akarod befejezni a telepítést:');
DEFINE('_KUNENA_DBCLEAN','Új telepítés');
DEFINE('_KUNENA_DBUPGRADE','Joomlaboard áttelepítése');
DEFINE('_KUNENA_TOPLEVEL','Felső szintű kategória');
DEFINE('_KUNENA_REGISTERED','Regisztráltak');
DEFINE('_KUNENA_PUBLICBACKEND','Nyilvános kiszolgálói oldal');
DEFINE('_KUNENA_SELECTANITEMTO','Válaszd ki az elemet a következő művelethez:');
DEFINE('_KUNENA_ERRORSUBS','Valamilyen hiba történt a hozzászólások és a témafigyelések törlésekor');
DEFINE('_KUNENA_WARNING','Figyelmeztetés...');
DEFINE('_KUNENA_CHMOD1','Állítsd 766-ra az engedélyt, hogy frissíthető legyen a fájl.');
DEFINE('_KUNENA_YOURCONFIGFILEIS','A konfigurációs fájl');
DEFINE('_KUNENA_KUNENA','Kunena');
DEFINE('_KUNENA_CLEXUS','Clexus PM');
DEFINE('_KUNENA_CB','Community Builder');
DEFINE('_KUNENA_MYPMS','myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM','uddeIM');
DEFINE('_KUNENA_JIM','JIM');
DEFINE('_KUNENA_MISSUS','Missus');
DEFINE('_KUNENA_SELECTTEMPLATE','Válassz sablont');
DEFINE('_KUNENA_CONFIGSAVED', 'A konfiguráció mentése megtörtént.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'VÉGZETES HIBA: Nem lehetett menteni a konfigurációt.');
DEFINE('_KUNENA_TFINW','A fájl nem írható.');
DEFINE('_KUNENA_FBCFS','A Kunena CSS fájl mentése megtörtént.');
DEFINE('_KUNENA_SELECTMODTO','Válassz moderátort...');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE','Ki kell választanod a kitakarítandó fórumot!');
DEFINE('_KUNENA_DELMSGERROR','A hozzászólások törlése nem sikerült:');
DEFINE('_KUNENA_DELMSGERROR1','A hozzászólások szövegeinek törlése nem sikerült:');
DEFINE('_KUNENA_CLEARSUBSFAIL','A témafigyelések törlése nem sikerült:');
DEFINE('_KUNENA_FORUMPRUNEDFOR','A fórum kitakarítása');
DEFINE('_KUNENA_PRUNEDAYS','napja történt');
DEFINE('_KUNENA_PRUNEDELETED','Törölve:');
DEFINE('_KUNENA_PRUNETHREADS','téma');
DEFINE('_KUNENA_ERRORPRUNEUSERS','Hiba történt a felhasználók kitakarításakor:');
DEFINE('_KUNENA_USERSPRUNEDDELETED','Kitakarított felhasználók; Törölve:');
DEFINE('_KUNENA_PRUNEUSERPROFILES','felhasználói profilok');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING','Nem találhatók kitakarításra alkalmas profilok.');
DEFINE('_KUNENA_TABLESUPGRADED','A Kunena tábláinak frissítése kész a verzióra');
DEFINE('_KUNENA_FORUMCATEGORY','Fórumkategória');
DEFINE('_KUNENA_IMGDELETED','A kép törlése megtörtént');
DEFINE('_KUNENA_FILEDELETED','A fájl törlése megtörtént');
DEFINE('_KUNENA_NOPARENT','Nincs szülő');
DEFINE('_KUNENA_DIRCOPERR','Hiba: A fájl (');
DEFINE('_KUNENA_DIRCOPERR1',') nem másolható!\n');
DEFINE('_KUNENA_INSTALL1','<strong>Kunena Fórum</strong> komponens <em>Joomla! CMS-hez</em> <br />&copy; 2006 - 2007 by Best Of Joomla<br />Minden jog fenntartva.');
DEFINE('_KUNENA_INSTALL2','Az átvitel/telepítés :</code></strong><br /><br /><font color="red"><b>sikerült');
DEFINE('_KUNENA_FORUMPRF_TITLE','Profil beállításai');
DEFINE('_KUNENA_FORUMPRF','Profil');
DEFINE('_KUNENA_FORUMPRRDESC','Ha telepítetted a Clexus PM vagy a Community Builder komponenst, akkor beállíthatod úgy a Kunenát, hogy használja a profillapot.');
DEFINE('_KUNENA_USERPROFILE_PROFILE','profilja');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profil találatai</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES','Hozzászólásaim');
DEFINE('_KUNENA_USERPROFILE_TOPICS','Témák');
DEFINE('_KUNENA_USERPROFILE_STARTBY','Indította');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES','Kategóriák');
DEFINE('_KUNENA_USERPROFILE_DATE','Dátum');
DEFINE('_KUNENA_USERPROFILE_HITS','Találatok');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS','A felhasználó nem szólt hozzá a fórumban');
DEFINE('_KUNENA_TOTALFAVORITE','Kedvencekhez hozzáadta:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON','Az alfórum rovatainak száma  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC','A főkategóriában lévő alfórumok száma ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED','Mindig bejelölt a témafigyelés megrendelése?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC','Az &quot;Igen&quot; választása esetén mindig bejelölt lesz a témafigyelés megrendelése');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1','Meg kell adnod a kategória / fórum nevét');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS','Statisztika megjelenítése');
DEFINE('_KUNENA_SHOWSTATSDESC','Ha meg akarod jeleníteni a statisztikát, akkor válaszd az Igent');
DEFINE('_KUNENA_SHOWWHOIS','A jelenlévő felhasználók kijelzése');
DEFINE('_KUNENA_SHOWWHOISDESC','Ha ki akarod jelezni a jelenlévő felhasználókat, akkor válaszd az Igent');
DEFINE('_KUNENA_STATSGENERAL','Általános statisztika megjelenítése');
DEFINE('_KUNENA_STATSGENERALDESC','Ha meg akarod jeleníteni az általános statisztikát, akkor válaszd az Igent');
DEFINE('_KUNENA_USERSTATS','A legaktívabb felhasználók statisztikájának megjelenítése');
DEFINE('_KUNENA_USERSTATSDESC','Ha meg akarod jeleníteni a legaktívabbak statisztikáját, akkor válaszd az Igent');
DEFINE('_KUNENA_USERNUM','Az aktív felhasználók száma');
DEFINE('_KUNENA_USERPOPULAR','A legkedveltebb témák statisztikájának megjelenítése');
DEFINE('_KUNENA_USERPOPULARDESC','Ha meg akarod jeleníteni a legkedveltebb témákat, akkor válaszd az Igent');
DEFINE('_KUNENA_NUMPOP','A legkedveltebb témák száma');
DEFINE('_KUNENA_INFORMATION',
   'A Kunena csapata örömmel jelenti be, hogy megjelent a Kunena 1.0.8. Ez egy hatékony és ízléses fórumkomponens egy érdemesnek bizonyoló tartalomkezelő rendszerhez, ami a Joomla!. Kiindulási alapja a Joomlaboard csapatának kemény munkája, és minden elismerésünk ennek a csapatnak. A Kunena néhány fontos funkciója az alábbiakban található (a JB jelenlegi funkcióin kívül):<br /><br /><ul><li>Sokkal grafikai tervezőbarátabb fórumrendszer. Egyszerűbb szerkezetével közeljár az SMF sablonrendszeréhez. Mindössze néhány lépéssel módosítható a fórum teljes arculata. Köszönet érte csapatunk kitűnő tervezőinek.</li><li>Korlátlan alkategóriarendszer jobb adminisztrációs kiszolgálói oldallal.</li><li>Gyorsabb rendszer és jobb programozási élmény külső fejlesztők számára.</li><li>Ugyanaz<br /></li><li>Profilkeret a fórum tetején</li><li>Népszerű magánüzenet-küldő rendszerek, mint a ClexusPM és az uddeIM támogatása</li><li>Alapszintű beépülő modul rendszer (inkább gyakorlati, mint tökéletes)</li><li>Nyelvi meghatározású ikonrendszer.<br /></li><li>Más sablonok képrendszerének megosztása. Vagyis lehetőség van a sablonok és a képsorozatok közti választásra</li><li>Joomla modulokat helyezhet el a fórum sablonján belül. Akart reklámot tenni a fórumba?</li><li>Kedvenc témák kiválasztása és kezelése</li><li>Fórumreflektorok és kiemelések</li><li>Fórumközlemények arra szolgáló panelban</li><li>Legújabb hozzászólások (többfüles)</li><li>Statisztika a fórum alján</li><li>Ki van jelen, melyik oldalon?</li><li>Kategóriaspecifikus képrendszer</li><li>RSS, PDF kimenet</li><li>Speciális keresés (fejlesztés alatt)</li><li>Community Builder és JomSocial profil lehetőségek</li><li>Avatárkezelés : CB és JomSocial lehetőségek<br /></li></ul><br />Ez több fejlesztő és tervező közös munkája, akik szívesen vettek részt ennek a kiadásnak a létrejöttében. Ezúton köszönjük meg valamennyiüknek, és bízunk abban, hogy tetszeni fog ez a kiadás!<br /><br />a Kunena csapat<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS','Utasítások');
DEFINE('_KUNENA_FINFO','Tájékoztató a Kunena fórumról');
DEFINE('_KUNENA_CSSEDITOR','Kunena sablon CSS-szerkesztő');
DEFINE('_KUNENA_PATH','Útvonal:');
DEFINE('_KUNENA_CSSERROR','Megjegyzés: A sablon CSS-fájlja legyen írható a változtatások mentéséhez.');
// User Management
DEFINE('_KUNENA_FUM','Kunena Felhasználói profilkezelő');
DEFINE('_KUNENA_SORTID','rendezés felhasználói azonosító szerint');
DEFINE('_KUNENA_SORTMOD','rendezés moderátorok szerint');
DEFINE('_KUNENA_SORTNAME','rendezés név szerint');
DEFINE('_KUNENA_VIEW','Nézet');
DEFINE('_KUNENA_NOUSERSFOUND','Nem található felhasználói profil.');
DEFINE('_KUNENA_ADDMOD','Kinevezés a következő fórum moderátorának');
DEFINE('_KUNENA_NOMODSAV','Egy lehetséges moderátor sem található. Olvasd el az alábbi \'megjegyzést\'.');
DEFINE('_KUNENA_NOTEUS',
   'MEGJEGYZÉS: Itt csak azok a felhasználók láthatók, akiknek a Kunena profiljában be van jelölve a moderátor jelző. Hogy hozzá lehessen rendelni egy moderátort, jelöld be a felhasználó moderátor jelzőjét, nyisd meg a <a href="index2.php?option=com_kunena&task=profiles">Felhasználók kezelése</a> funkciót, és keresd meg a moderátorrá kinevezendő felhasználót. Majd válaszd ki a profilját és frissítsd. A moderátor jelzőt csak adminisztrátor állíthatja be. ');
DEFINE('_KUNENA_PROFFOR','Profil :');
DEFINE('_KUNENA_GENPROF','Általános profilbeállítások');
DEFINE('_KUNENA_PREFVIEW','Használandó nézettípus:');
DEFINE('_KUNENA_PREFOR','A hozzászólások sorrendje:');
DEFINE('_KUNENA_ISMOD','Moderátor:');
DEFINE('_KUNENA_ISADM','<strong>Igen</strong> (megváltoztathatatlan, ez a felhasználó (fő)adminisztrátor)');
DEFINE('_KUNENA_COLOR','Szín');
DEFINE('_KUNENA_UAVATAR','A felhasználó avatárja:');
DEFINE('_KUNENA_NS','Nincs avatár');
DEFINE('_KUNENA_DELSIG',' Az aláírás törlése');
DEFINE('_KUNENA_DELAV',' Az avatár törlése');
DEFINE('_KUNENA_SUBFOR','Témafigyelések: ');
DEFINE('_KUNENA_NOSUBS','A felhasználó egy téma figyelését sem rendelte meg');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS','Alapok');
DEFINE('_KUNENA_BASICSFORUM','Alapvető információk a fórumról');
DEFINE('_KUNENA_PARENT','Szülő:');
DEFINE('_KUNENA_PARENTDESC',
   'Megjegyzés: Kategória létrehozásához a \'Felső szintű kategóriát\' válaszd szülőként. A kategória a fórumok tárolója.<br />Fórumot <strong>csak</strong> kategóriában lehet létrehozni egy már létrehozott kategória kiválasztásával a fórum szülőjeként.<br /> Kategóriába <strong>NEM</strong> lehet beküldeni hozzászólásokat; csak fórumokba.');
DEFINE('_KUNENA_BASICSFORUMINFO','A fórum neve és leírása');
DEFINE('_KUNENA_NAMEADD','Név:');
DEFINE('_KUNENA_DESCRIPTIONADD','Leírás:');
DEFINE('_KUNENA_ADVANCEDDESC','A fórum speciális beállításai');
DEFINE('_KUNENA_ADVANCEDDESCINFO','A fórum biztonsága és elérése');
DEFINE('_KUNENA_LOCKEDDESC','Az &quot;Igen&quot; választása esetén lezárásra kerül ez a fórum. A moderátorok és az adminisztrátorok kivételével senki sem indíthat új témát vagy szólhat hozzá egy lezárt fórumban (vagy helyezhet át ide hozzászólásokat).');
DEFINE('_KUNENA_LOCKED1','Lezárt:');
DEFINE('_KUNENA_PUBACC','Nyilvános hozzáférési szint:');
DEFINE('_KUNENA_PUBACCDESC',
   'Zártkörű fórum létrehozása esetén itt adhatod meg azt a legalacsonyabb felhasználói szintet, amelyik megtekintheti/beléphet a fórumba. A legalacsonyabb felhasználói szint alapértelmezett beállítása a &quot;Mindenki&quot;.<br /><b>Megjegyzés</b>: ha egy teljes kategóriában bizonyos csoport(ok) számára korlátozod a hozzáférést, akkor az el fogja rejteni a tartalmazott fórumokat azok számára, akik nem rendelkeznek megfelelő jogokkal a kategóriában, <b>még akkor is</b>, ha ezen fórumok egynémelyikének alacsonyabbra állították a hozzáférési szintjét! Ez vonatkozik a moderátorokra is; fel kell venned egy moderátort a kategória moderátorlistájára, ha a kategória megtekintéséhez nem a megfelelő csoportszinten van.<br /> Ez független attól a ténytől, hogy a kategóriákat nem lehet moderálni; moderátorokoat azért még fel lehet venni a moderátorlistára.');
DEFINE('_KUNENA_CGROUPS','Az alcsoportokban is:');
DEFINE('_KUNENA_CGROUPSDESC','Az alcsoportok számára is engedélyezett a hozzáférés? A &quot;Nem&quot; választása esetén <b>csak</b> a kiválasztott csoport számára korlátozott a fórumhoz való hozzáférés');
DEFINE('_KUNENA_ADMINLEVEL','Adminisztrátori hozzáférési szint:');
DEFINE('_KUNENA_ADMINLEVELDESC',
   'Ha nyilvános hozzáférés korlátozású fórumot hozol létre, akkor itt adhatsz meg további adminisztrációs hozzáférési szintet.<br /> Ha a fórumhoz történő hozzáférést egy speciális nyilvános felhasnálói oldali felhasználócsoportra korlátozod, és nem adod meg itt a nyilvános kiszolgálói oldali csoportot, akkor az adminisztrátorok nem tudnak majd belépni a fórumba, ill. megtekinteni azt.');
DEFINE('_KUNENA_ADVANCED','Speciális');
DEFINE('_KUNENA_CGROUPS1','Az alcsoportokban is:');
DEFINE('_KUNENA_CGROUPS1DESC','Az alcsoportok számára is engedélyezett a hozzáférés? A &quot;Nem&quot; választása esetén <b>csak</b> a kiválasztott csoport számára korlátozott a fórumhoz való hozzáférés');
DEFINE('_KUNENA_REV','A hozzászólások jóváhagyása:');
DEFINE('_KUNENA_REVDESC',
   'Az &quot;Igen&quot; választása esetén a moderátoroknak kell átnézniük a hozzászólásokat a fórumban történő közzététel előtt. Ez csak moderált fórumban hasznos!<br />Ha moderátor megadása nélkül állítod ezt be, akkor kizárólag a webhely adminisztrátora felelős a beküldött hozzászólások jóváhagyásáért/törléséért, mivel ezek \'vissza lesznek\' tartva!');
DEFINE('_KUNENA_MOD_NEW','Moderálás');
DEFINE('_KUNENA_MODNEWDESC','A fórum moderálása és moderátorai');
DEFINE('_KUNENA_MOD','Moderált:');
DEFINE('_KUNENA_MODDESC',
   'Az &quot;Igen&quot; választása esetén moderátor(oka)t lehet hozzárendelni ehhez a fórumhoz.<br /><strong>Megjegyzés:</strong> Ez nem jelenti azt, hogy az új hozzászólásokat át kell nézni a közzététel előtt!<br /> A Speciális fülön a &quot;Jóváhagyás&quot; lehetőséget kell választania hozzá..<br /><br /> <strong>Megjegyzés:</strong> A moderálás &quot;Igen&quot;-re állítása után előbb mentened kell a fórum beállításait, hogy tudj moderátorokat hozzáadni az Új gombbal.');
DEFINE('_KUNENA_MODHEADER','A fórum moderálásának beállításai');
DEFINE('_KUNENA_MODSASSIGNED','A fórum moderátora(i)');
DEFINE('_KUNENA_NOMODS','Ennek a fórumnak nincs moderátora');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT','Módosítás :');
DEFINE('_KUNENA_ADD','Hozzáadás :');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP','Léptetés felfelé');
DEFINE('_KUNENA_MOVEDOWN','Léptetés lefelé');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED','Minden regisztrált');
DEFINE('_KUNENA_EVERYBODY','Mindenki');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER','Átrendezés');
DEFINE('_KUNENA_CHECKEDOUT','Check Out');
DEFINE('_KUNENA_ADMINACCESS','Adminisztrátori hozzáférés');
DEFINE('_KUNENA_PUBLICACCESS','Nyilvános hozzáférés');
DEFINE('_KUNENA_PUBLISHED','Közzétéve');
DEFINE('_KUNENA_REVIEW','Jóváhagyás');
DEFINE('_KUNENA_MODERATED','Moderált');
DEFINE('_KUNENA_LOCKED','Lezárt');
DEFINE('_KUNENA_CATFOR','Kategória / Fórum');
DEFINE('_KUNENA_ADMIN','&nbsp;Kunena kezelése');
DEFINE('_KUNENA_CP','&nbsp;Kunena vezérlőpult');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION','Avatárbeépítés');
DEFINE('_COM_A_RANKS_SETTINGS','Rangok használata');
DEFINE('_COM_A_RANKING_SETTINGS','Rangok beállításai');
DEFINE('_COM_A_AVATAR_SETTINGS','Avatár beállításai');
DEFINE('_COM_A_SECURITY_SETTINGS','Biztonsági beállítások');
DEFINE('_COM_A_BASIC_SETTINGS','Alapbeállítások');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES','A kedvencek engedélyezése');
DEFINE('_COM_A_FAVORITES_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók hozzáadhatják a témákat a kedvenceikhez ');
DEFINE('_USER_UNFAVORITE_ALL','Az összes téma (a hibakeresési célból láthatatlanok is) <b><u>törlése a kedvencekből</u></b>');
DEFINE('_VIEW_FAVORITETXT','A téma hozzáadása a kedvencekhez ');
DEFINE('_USER_UNFAVORITE_YES','Törölted ezt a témát a kedvenceid közül');
DEFINE('_POST_FAVORITED_TOPIC','A témát hozzáadtuk a kedvenceihez.');
DEFINE('_VIEW_UNFAVORITETXT','A téma törlése a kedvencekből');
DEFINE('_VIEW_UNSUBSCRIBETXT','A témafigyelés lemondása');
DEFINE('_USER_NOFAVORITES','Nincs kedvenc témád');
DEFINE('_POST_SUCCESS_FAVORITE','A téma hozzáadása a kedvenceidhez megtörtént.');
DEFINE('_COM_A_MESSAGES_SEARCH','Keresési eredmények');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH','A hozzászólások száma oldalanként a Keresési eredményekben');
DEFINE('_KUNENA_USE_JOOMLA_STYLE','A Joomla stílusának felhasználása');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Ha a Joomla stílusát akarod használni, akkor válaszd az IGEN-t. (osztály: mint a sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST','Az alfórum ikonjának megjelenítése');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC','Ha ikonnal akarod megjeleníteni az alkategóriát a fórumlistában, akkor válaszd az IGEN-t. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','A közlemény megjelenítése');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Az "Igen" választása esetén láthatók a közlemények a fórum főlapján.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Az avatár megjelenítése a kategórialistában');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Az "Igen" választása esetén látható a felhasználó avatárja a fórumkategóriák listájában.');
DEFINE('_KUNENA_RECENT_POSTS','Legújabb hozzászólások beállításai');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','A legújabb hozzászólások megjelenítése');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','Az "Igen" választása esetén látható a legújabb hozzászólások beépülő modul a fórumban');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','A legújabb hozzászólások száma');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','A legújabb hozzászólások száma');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Mennyiség fülenként ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','A hozzászólások fülenkénti száma');
DEFINE('_KUNENA_LATEST_CATEGORY','Látható a kategória');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','A legújabb hozzászólásokban megjeleníthető bizonyos kategóriák. Például: 2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Egyetlen Tárgy megjelenítése');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','Egyetlen Tárgy megjelenítése');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Látható a Válasz tárgymegjelölés');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','Látható a Válasz tárgymegjelölés (Vá:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','A tárgy hossza');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','A tárgy hossza');
DEFINE('_KUNENA_SHOW_LATEST_DATE','Látható a dátum');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC','Látható a dátum');
DEFINE('_KUNENA_SHOW_LATEST_HITS','Látható a találatok száma');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC','Látható a találatok száma');
DEFINE('_KUNENA_SHOW_AUTHOR','A hozzászóló megjelenítése');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC','1=felhasználónév, 2=valódi név, 0=nincs');
DEFINE('_KUNENA_STATS','Statisztika beépülő modul beállításai ');
DEFINE('_KUNENA_CATIMAGEPATH','A kategóriakép útvonala ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','A kategóriakép útvonala. Engedélyezése esetén a "category_images/" útvonala a "html_gyökérmappa/images/fbfiles/category_images/" lesz ');
DEFINE('_KUNENA_ANN_MODID','A közleménymoderátorok azonosítói ');
DEFINE('_KUNENA_ANN_MODID_DESC','Írd be ide a felhasználói azonosítókat a közlemények moderálásához, pl. 62,63,73 . A közleménymoderátorok tehetik közzé, módosíthatják vagy törölhetik a közleményeket.');
//
DEFINE('_KUNENA_FORUM_TOP','Fórumkategóriák ');
DEFINE('_KUNENA_CHILD_BOARDS','Alfórumok ');
DEFINE('_KUNENA_QUICKMSG','Gyors hozzászólás ');
DEFINE('_KUNENA_THREADS_IN_FORUM','A fórum témája ');
DEFINE('_KUNENA_FORUM','Fórum ');
DEFINE('_KUNENA_SPOTS','Reflektorban');
DEFINE('_KUNENA_CANCEL','Mégse');
DEFINE('_KUNENA_TOPIC','TÉMA: ');
DEFINE('_KUNENA_POWEREDBY','Támogatja a ');
// Time Format
DEFINE('_TIME_TODAY','<b>Ma</b> ');
DEFINE('_TIME_YESTERDAY','<b>Tegnap</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS','Legújabb hozzászólások');
DEFINE('_KUNENA_WHO_WHOISONLINE','Jelenlévők');
DEFINE('_KUNENA_WHO_MAINPAGE','Fórum főlap');
DEFINE('_KUNENA_GUEST','Vendég');
DEFINE('_KUNENA_PATHWAY_VIEWING','fő olvassa');
DEFINE('_KUNENA_ATTACH','Melléklet');
// Favorite
DEFINE('_KUNENA_FAVORITE','Kedvencek');
DEFINE('_USER_FAVORITES','Kedvenc témáim');
DEFINE('_THREAD_UNFAVORITE','Törlés a Kedvencekből');
// profilebox
DEFINE('_PROFILEBOX_WELCOME','Helló');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS','Új hozzászólások');
DEFINE('_PROFILEBOX_SET_MYAVATAR','Avatár feltöltése');
DEFINE('_PROFILEBOX_MYPROFILE','Profil');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS','Hozzászólásaim megjelenítése');
DEFINE('_PROFILEBOX_GUEST','vendég');
DEFINE('_PROFILEBOX_LOGIN','jelentkezz be');
DEFINE('_PROFILEBOX_REGISTER','regisztrálj');
DEFINE('_PROFILEBOX_LOGOUT','Kijelentkezés');
DEFINE('_PROFILEBOX_LOST_PASSWORD','Elfelejtetted a jelszavadat?');
DEFINE('_PROFILEBOX_PLEASE','Kérjük, hogy');
DEFINE('_PROFILEBOX_OR','vagy');
// recentposts
DEFINE('_RECENT_RECENT_POSTS','Legújabb hozzászólások');
DEFINE('_RECENT_TOPICS','Téma');
DEFINE('_RECENT_AUTHOR','Hozzászóló');
DEFINE('_RECENT_CATEGORIES','Kategória');
DEFINE('_RECENT_DATE','Dátum');
DEFINE('_RECENT_HITS','Találatok');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Közlemények');
DEFINE('_ANN_ID','AZ');
DEFINE('_ANN_DATE','Dátum');
DEFINE('_ANN_TITLE','Cím');
DEFINE('_ANN_SORTTEXT','Rövid szöveg');
DEFINE('_ANN_LONGTEXT','Hosszú szöveg');
DEFINE('_ANN_ORDER','Sorrend');
DEFINE('_ANN_PUBLISH','Közzététel');
DEFINE('_ANN_PUBLISHED','Közzétéve');
DEFINE('_ANN_UNPUBLISHED','Visszavonva');
DEFINE('_ANN_EDIT','Módosítás');
DEFINE('_ANN_DELETE','Törlés');
DEFINE('_ANN_SUCCESS','Siker');
DEFINE('_ANN_SAVE','Mentés');
DEFINE('_ANN_YES','Igen');
DEFINE('_ANN_NO','Nem');
DEFINE('_ANN_ADD','Új közlemény');
DEFINE('_ANN_SUCCESS_EDIT','A módosítás mentése kész');
DEFINE('_ANN_SUCCESS_ADD','A közlemény mentése kész');
DEFINE('_ANN_DELETED','A közleményt töröltük');
DEFINE('_ANN_ERROR','HIBA');
DEFINE('_ANN_READMORE','Folytatás...');
DEFINE('_ANN_CPANEL','Közlemények kezelőpult');
DEFINE('_ANN_SHOWDATE','Látható a dátum');
// Stats
DEFINE('_STAT_FORUMSTATS','fórumstatisztikája');
DEFINE('_STAT_GENERAL_STATS','Általános statisztika');
DEFINE('_STAT_TOTAL_USERS','Felhasználók');
DEFINE('_STAT_LATEST_MEMBERS','Legújabb tag');
DEFINE('_STAT_PROFILE_INFO','Profil megtekintése:');
DEFINE('_STAT_TOTAL_MESSAGES','Hozzászólások');
DEFINE('_STAT_TOTAL_SUBJECTS','Témák');
DEFINE('_STAT_TOTAL_CATEGORIES','Fórumok');
DEFINE('_STAT_TOTAL_SECTIONS','Kategóriák');
DEFINE('_STAT_TODAY_OPEN_THREAD','Ma indított témák');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD','Tegnap indított témák');
DEFINE('_STAT_TODAY_TOTAL_ANSWER','Mai hozzászólások');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER','Tegnapi hozzászólások');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM','Legújabb hozzászólások');
DEFINE('_STAT_MORE_ABOUT_STATS','Részletes statisztika');
DEFINE('_STAT_USERLIST','Felhasználók névsora');
DEFINE('_STAT_TEAMLIST','A fórum csapata');
DEFINE('_STATS_FORUM_STATS','Fórumstatisztika');
DEFINE('_STAT_POPULAR','Legnépszerűbb');
DEFINE('_STAT_POPULAR_USER_TMSG','felhasználó (hozzászólások alapján) ');
DEFINE('_STAT_POPULAR_USER_KGSG','téma ');
DEFINE('_STAT_POPULAR_USER_GSG','felhasználó (profiltalálatok alapján) ');
//Team List
DEFINE('_MODLIST_ONLINE','A felhasználó most jelen van');
DEFINE('_MODLIST_OFFLINE','A felhasználó távol van');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE','Ki mit olvas?');
DEFINE('_WHO_ONLINE_NOW','Jelenlévők:');
DEFINE('_WHO_ONLINE_MEMBERS','tag');
DEFINE('_WHO_AND','és');
DEFINE('_WHO_ONLINE_GUESTS','vendég');
DEFINE('_WHO_ONLINE_USER','Felhasználó');
DEFINE('_WHO_ONLINE_TIME','Időpont');
DEFINE('_WHO_ONLINE_FUNC','Tevékenység');
// Userlist
DEFINE ('_USRL_USERLIST','Felhasználók névsora');
DEFINE ('_USRL_REGISTERED_USERS','A(z) %s fórumnak <b>%d</b> regisztrált felhasználója van');
DEFINE ('_USRL_SEARCH_ALERT','Kérjük, hogy írd be a keresendő értéket!');
DEFINE ('_USRL_SEARCH','Felhasználó keresése');
DEFINE ('_USRL_SEARCH_BUTTON','Keresés');
DEFINE ('_USRL_LIST_ALL','Az összes listázása');
DEFINE ('_USRL_NAME','Név');
DEFINE ('_USRL_USERNAME','Felhasználónév');
DEFINE ('_USRL_EMAIL','E-mail');
DEFINE ('_USRL_USERTYPE','Felhasználócsoport');
DEFINE ('_USRL_JOIN_DATE','Csatlakozás dátuma');
DEFINE ('_USRL_LAST_LOGIN','Utolsó bejelentkezés');
DEFINE ('_USRL_NEVER','Soha');
DEFINE ('_USRL_BLOCK','Állapot');
DEFINE ('_USRL_MYPMS2','MyPMS');
DEFINE ('_USRL_ASC','Növekvő');
DEFINE ('_USRL_DESC','Csökkenő');
DEFINE ('_USRL_DATE_FORMAT','%Y.%m.%d.');
DEFINE ('_USRL_TIME_FORMAT','%H:%M');
DEFINE ('_USRL_USEREXTENDED','Részletek');
DEFINE ('_USRL_COMPROFILER','Profil');
DEFINE ('_USRL_THUMBNAIL','Bélyegkép');
DEFINE ('_USRL_READON','látható');
DEFINE ('_USRL_MYPMSPRO','Clexus PM');
DEFINE ('_USRL_MYPMSPRO_SENDPM','Magánüzenet küldése');
DEFINE ('_USRL_JIM','MÜ');
DEFINE ('_USRL_UDDEIM','MÜ');
DEFINE ('_USRL_SEARCHRESULT','Keresési eredmény:');
DEFINE ('_USRL_STATUS','Állapot');
DEFINE ('_USRL_LISTSETTINGS','Felhasználói névsor beállításai');
DEFINE ('_USRL_ERROR','Hiba');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE','Magánüzenet-küldő komponens');
DEFINE('_COM_A_COMBUILDER_TITLE','Community Builder');
DEFINE('_FORUM_SEARCH','Keresés: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','Biztosan törölni akarod ezt a hozzászólást? \n\n MEGJEGYZÉS: A törölt hozzászólások visszanyerésére NINCS mód!');
DEFINE('_MODERATION_DELETE_SUCCESS','A hozzászólás(ok) törlése megtörtént');
DEFINE('_COM_A_RANKING','Rangok');
DEFINE('_COM_A_BOT_REFERENCE','A mambot referenciatáblázat megtekíntése');
DEFINE('_COM_A_MOSBOT','A DiscussBot engedélyezése');
DEFINE('_PREVIEW','Előnézet');
DEFINE('_COM_A_MOSBOT_TITLE','DiscussBot');
DEFINE('_COM_A_MOSBOT_DESC',  'A DiscussBot teszi lehetővé, hogy a felhasználók megvitassák az oldalon olvasottakat. A tartalom címe egyben témaként is szolgál.' 
          . '<br />Ha a téma nem létezik még, akkor készül egy új. Ha létezik már a téma, akkor megmutatja a felhasználónak a témát, és hozzá tud szólni.' . '<br /><strong>A mambotot külön kell letöltened és telepítened.</strong>'
          . '<br />További információ a <a href="http://www.bestofjoomla.com">Best of Joomla</a> webhelyén.' . '<br />Telepítés után a következő mambotsorokat kell beszúrnod a tartalmi elembe:' . '<br />{mos_KUNENA_discuss:<em>catid</em>}'
          . '<br />A <em>kategória-azonosító</em> azt a kategóriát jelöli, amelyben a tartalmi elem megvitatható. A megfelelő kategória-azonosító megtalálásához nézz bele a fórumba ' . 'és az URL-ből kiolvashatod a kategória-azonosítót.'
          . '<br />Példa: ha a fórumban megvitatott cikk kategória-azonosítója 26, a mambotnak így kell kinéznie: {mos_KUNENA_discuss:26}'
          . '<br />Ez egy kissé bonyolultnak tűnik, viszont így minden tartalmi elem megvitatható a kapcsolódó fórumban.' );
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE','Keresés');
DEFINE('_FORUM_SEARCHRESULTS','%s látható a(z) %s találatból.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP','GYIK');
// rules.php
DEFINE('_COM_FORUM_RULES','Szabályzat');
DEFINE('_COM_FORUM_RULES_DESC','<ul><li>A joomlagyökér/administrator/components/com_kunena/language/hungarian.php fájl rules.php szakaszának megfelelő sorában írhatod le a szabályokat:</li><li>2. szabály</li><li>3. szabály</li><li>4. szabály</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE','Fórumkód');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS','A hozzászólás(ok) jóváhagyása megtörtént');
DEFINE('_MODERATION_DELETE_ERROR','HIBA: A hozzászólás(ok) törlése nem sikerült');
DEFINE('_MODERATION_APPROVE_ERROR','HIBA: A hozzászólás(ok) jóváhagyása nem sikerült');
// listcat.php
DEFINE('_GEN_NOFORUMS','Nincs fórum ebben a kategóriában!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED','Nem sikerült létrehozni a szellemtémát a régi fórumban!');
DEFINE('_POST_MOVE_GHOST','Szellemüzenet hagyása a régi fórumban');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP','Ugrás fórumhoz');
DEFINE('_COM_A_FORUM_JUMP','A fórumhoz ugrás engedélyezése');
DEFINE('_COM_A_FORUM_JUMP_DESC','Az &quot;Igen&quot; választása esetén egy kiválasztó lesz látható a fórum oldalain, amivel gyorsan oda lehet ugrani egy másik fórumhoz vagy kategóriához.');
//new in 1.1 RC1
DEFINE('_GEN_RULES','Szabályzat');
DEFINE('_COM_A_RULESPAGE','A Szabályzat oldal engedélyezése');
DEFINE('_COM_A_RULESPAGE_DESC',
   'Az &quot;Igen&quot; választása esetén a fejlécmenüben megjelenik a Szabályok oldalra mutató hivatkozás. Ezen az oldalon ismertetheted a fórum használati szabályait. Ennek a fájlnak a tartalmát a components/com_kunena könyvtárban található rules.php fájl megnyitásával változtathatod meg. <em>Mindig legyen biztonsági másolatod erről a fájlról, mert frissítéskor felülírásra kerül!</em>');
DEFINE('_MOVED_TOPIC','ÁTHELYEZVE:');
DEFINE('_COM_A_PDF','A PDF létrehozás engedélyezése');
DEFINE('_COM_A_PDF_DESC',
    'Az &quot;Igen&quot; választásával engedélyezheted a felhasználóknak, hogy egyszerű PDF dokumentumként letölthessék egy téma tartalmát.<br />Ez egy <u>egyszerű</u> PDF dokumentum; de tartalmazza a téma összes szövegét.');
DEFINE('_GEN_PDFA','Kattints erre a gombra a téma PDF dokumentumának elkészítéséhez (új ablakban nyílik meg).');
DEFINE('_GEN_PDF', 'PDF');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE','Kattints ide a felhasználó profiljának megtekintéséhez');
DEFINE('_VIEW_ADDBUDDY','Kattints ide a felhasználó címjegyzékbe történő felvételéhez');
DEFINE('_POST_SUCCESS_POSTED','A hozzászólás beküldése sikeresen megtörtént');
DEFINE('_POST_SUCCESS_VIEW','[ Vissza a témához ]');
DEFINE('_POST_SUCCESS_FORUM','[ Vissza a fórumhoz ]');
DEFINE('_RANK_ADMINISTRATOR','Adminisztrátor');
DEFINE('_RANK_MODERATOR','Moderátor');
DEFINE('_SHOW_LASTVISIT','Az utolsó látogatás óta');
DEFINE('_COM_A_BADWORDS_TITLE','Csúnya szavak szűrése');
DEFINE('_COM_A_BADWORDS','A csúnya szavak szűrése');
DEFINE('_COM_A_BADWORDS_DESC','Az &quot;Igen&quot; választása esetén a Badword komponens beállításában definiált szavak alapján szűrhetők a hozzászólások. Eme funkció használatához telepített Badword komponens szükséges!');
DEFINE('_COM_A_BADWORDS_NOTICE','* Ezt az üzenetet cenzúráztuk, mert olyan szót/szavakat tartalmaz, amelyet az adminisztrátor beállított *');
DEFINE('_COM_A_AVATAR_SRC','Felhasználandó avatár');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Ha telepítetted a Clexus PM vagy a Community Builder komponenst, akkor konfigurálhatod úgy a Kunenát, hogy a felhasználó avatárja a Clexus PM vagy a Community Builder felhasználói profiljából jöjjön. MEGJEGYZÉS: Community Builder esetén engedélyezned kell a bélyegkép beállítást, mert a fórum a bélyegkép felhasználói képeket használja, nem az eredetieket.');
DEFINE('_COM_A_KARMA','A Karma kijelzése');
DEFINE('_COM_A_KARMA_DESC','Az &quot;Igen&quot; választása esetén látható a felhasználó Karmája és a hozzátartozó gombok (növelés / csökkentés), ha aktiváltad a Felhasználói Statisztikát.');
DEFINE('_COM_A_DISEMOTICONS','A hangulatjelek letiltása');
DEFINE('_COM_A_DISEMOTICONS_DESC','Az &quot;Igen&quot; választásával kikapcsolhatod a hangulatjeleket.');
DEFINE('_COM_C_FBCONFIG','Kunena<br/>beállításai');
DEFINE('_COM_C_FBCONFIGDESC','A Kunena összes funkciójának beállítása');
DEFINE('_COM_C_FORUM','Fórumok<br/>kezelése');
DEFINE('_COM_C_FORUMDESC','A kategóriák/fórumok hozzáadása és konfigurálása');
DEFINE('_COM_C_USER','Felhasználók<br/>kezelése');
DEFINE('_COM_C_USERDESC','Alapvető felhasználó és felhasználói profil kezelés');
DEFINE('_COM_C_FILES','Feltöltött<br/>fájlok tallózása');
DEFINE('_COM_C_FILESDESC','A feltöltött fájlok tallózása és kezelése');
DEFINE('_COM_C_IMAGES','Feltöltött<br/>képek tallózása');
DEFINE('_COM_C_IMAGESDESC','A feltöltött képek tallózása és kezelése');
DEFINE('_COM_C_CSS','CSS fájl<br/>szerkesztése');
DEFINE('_COM_C_CSSDESC','A Kunena kinézetének beállítása');
DEFINE('_COM_C_SUPPORT','Támogató<br/>webhely');
DEFINE('_COM_C_SUPPORTDESC','Kapcsolódás a Best of Joomla webhelyhez (új ablak)');
DEFINE('_COM_C_PRUNETAB','Fórum<br/>kitakarítása');
DEFINE('_COM_C_PRUNETABDESC','A régi témák törlése (konfigurálható)');
DEFINE('_COM_C_PRUNEUSERS','Felhasználók<br/>kitakarítása');
DEFINE('_COM_C_PRUNEUSERSDESC','A Kunena felhasználói tábla szinkronizálása a Joomla! felhasználói táblájával');
DEFINE('_COM_C_LOADMODPOS','Modulpozíciók betöltése');
DEFINE('_COM_C_LOADMODPOSDESC','A modulpozíciók betöltése a Kunena sablonhoz');
DEFINE('_COM_C_UPGRADEDESC','Adatbázis frissítés és verziókövetés a legfrissebb verzióhoz');
DEFINE('_COM_C_BACK','Vissza a Kunena vezérlőpulthoz');
DEFINE('_SHOW_LAST_SINCE','Aktív témák a legutolsó látogatás óta:');
DEFINE('_POST_SUCCESS_REQUEST2','A kérés feldolgozása kész');
DEFINE('_POST_NO_PUBACCESS3','Kattints ide a regisztrációhoz.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE','A hozzászólást töröltük.');
DEFINE('_POST_SUCCESS_EDIT','A hozzászólás módosítása sikeresen megtörtént.');
DEFINE('_POST_SUCCESS_MOVE','A téma áthelyezése megtörtént.');
DEFINE('_POST_SUCCESS_POST','A hozzászólás beküldése sikerült.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Az Ön által kért téma figyelését elindítottuk.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA','Karma');
DEFINE('_KARMA_SMITE','Büntetés');
DEFINE('_KARMA_APPLAUD','Jutalmazás');
DEFINE('_KARMA_BACK','A témához való visszatéréshez,');
DEFINE('_KARMA_WAIT','Egy felhasználó karmáját csak 6 óránként módosíthatod. <br/>Várj, amíg lejár ez az idő, és csak utána módosítsd valamelyik felhasználó karmáját.');
DEFINE('_KARMA_SELF_DECREASE','Kérjük, hogy ne csökkentsd a saját karmádat!');
DEFINE('_KARMA_SELF_INCREASE','A karmád csökkent, mert növelni szeretted volna!');
DEFINE('_KARMA_DECREASED','A felhasználó karmáját csökkentetted. Ha nem térsz néhány pillanaton belül vissza a témához,');
DEFINE('_KARMA_INCREASED','A felhasználó karmáját megnövelted. Ha nem térsz néhány pillanaton belül vissza a témához,');
DEFINE('_COM_A_TEMPLATE','Sablon');
DEFINE('_COM_A_TEMPLATE_DESC','Válaszd ki a használni kívánt sablont.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH','Képkészletek');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC','Válaszd ki a sablonhoz használni kívánt képkészletet.');
DEFINE('_PREVIEW_CLOSE','Ablak bezárása');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR','A Statisztika sáv használata');
DEFINE('_COM_A_POSTSTATSBAR_DESC','Az &quot;Igen&quot; választása esetén a felhasználó hozzászólásai számának megjelenítése grafikusan történik.');
DEFINE('_COM_A_POSTSTATSCOLOR','A színek száma a Statisztika sávon');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Add meg a Statisztika sávhoz használandó színek számát');
DEFINE('_LATEST_REDIRECT',
    'A Kunenának (ismét) meg kell állapítania a hozzáférési jogaidat, mielőtt elkészíti a legutóbbi hozzászólások listáját.\nAggodalomra semmi ok, mivel 30 percnél is több inaktivitás vagy újbóli bejelentkezés után ez normális jelenség.\nKüldd el újra a keresési kérést.');
DEFINE('_SMILE_COLOUR','Szín');
DEFINE('_SMILE_SIZE','Méret');
DEFINE('_COLOUR_DEFAULT','Szokásos');
DEFINE('_COLOUR_RED','Piros');
DEFINE('_COLOUR_PURPLE','Lila');
DEFINE('_COLOUR_BLUE','Kék');
DEFINE('_COLOUR_GREEN','Zöld');
DEFINE('_COLOUR_YELLOW','Sárga');
DEFINE('_COLOUR_ORANGE','Narancs');
DEFINE('_COLOUR_DARKBLUE','Sötétkék');
DEFINE('_COLOUR_BROWN','Barna');
DEFINE('_COLOUR_GOLD','Arany');
DEFINE('_COLOUR_SILVER','Ezüst');
DEFINE('_SIZE_NORMAL','Normál');
DEFINE('_SIZE_SMALL','Kicsi');
DEFINE('_SIZE_VSMALL','Nagyon kicsi');
DEFINE('_SIZE_BIG','Nagy');
DEFINE('_SIZE_VBIG','Nagyon nagy');
DEFINE('_IMAGE_SELECT_FILE','Válaszd ki a csatolandó képfájlt');
DEFINE('_FILE_SELECT_FILE','Válaszd ki a csatolandó fájlt');
DEFINE('_FILE_NOT_UPLOADED','A fájl feltöltése nem sikerült. Próbáld meg újra, vagy módosítsd a hozzászólást.');
DEFINE('_IMAGE_NOT_UPLOADED','A kép feltöltése nem sikerült. Próbáld meg újra, vagy módosítsd a hozzászólást.');
DEFINE('_BBCODE_IMGPH','Az [img] helyfoglaló beszúrása a csatolandó kép számára');
DEFINE('_BBCODE_FILEPH','A [file] helyfoglaló beszúrása a csatolandó fájl számára');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','Az összes téma figyelésének <b><u>lemondása</u></b> (hibajavító szándékkal a láthatatlanokat is beleértve)');
DEFINE('_LINK_JS_REMOVED','<em>A JavaScriptet tartalmazó aktív hivatkozás automatikusan törlésre került.</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS','Arculat');
DEFINE('_COM_A_USERS','Felhasználó vonatkozású');
DEFINE('_COM_A_LENGTHS','Különféle hosszúság beállítások');
DEFINE('_COM_A_SUBJECTLENGTH','A Tárgy hossza');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'A Tárgy mező hossza. Az adatbázis által támogatott hossz legfeljebb 255 karakter. Ha több bájtos karakterkészletet használsz a webhelyen, mint az Unicode, az UTF-8 vagy a nem ISO-8599-x, akkor ezzel a képlettel csökkentheted:<br/><tt>round_down(255/(karakterenkénti legnagyobb karakterkészlet bájtméret))</tt><br/>Példa UTF-8-hoz, melynél a karakterenkénti legnagyobb karakter bájtméret 4 bájt: 255/4=63.');
DEFINE('_LATEST_THREADFORUM','Téma/Fórum');
DEFINE('_LATEST_NUMBER','Új hozzászólások');
DEFINE('_COM_A_SHOWNEW','Új hozzászólások megjelenítése');
DEFINE('_COM_A_SHOWNEW_DESC','Az &quot;Igen&quot; választása esetén a felhasználó láthatja, hogy melyik fórumba hány hozzászólás érkezett be az utolsó látogatása óta.');
DEFINE('_COM_A_NEWCHAR','&quot;Új&quot; jelző');
DEFINE('_COM_A_NEWCHAR_DESC','Itt határozhatod meg, hogy mit kell használni az új hozzászólások jelzésére (pl. &quot;!&quot; vagy &quot;Új!&quot;)');
DEFINE('_LATEST_AUTHOR','Legutolsó hozzászóló');
DEFINE('_GEN_FORUM_NEWPOST','Új hozzászólások');
DEFINE('_GEN_FORUM_NOTNEW','Nincs új hozzászólás');
DEFINE('_GEN_UNREAD','Olvasatlan téma');
DEFINE('_GEN_NOUNREAD','Olvasott téma');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Az összes fórum megjelölése olvasottként');
DEFINE('_GEN_MARK_THIS_FORUM_READ','A fórum megjelölése olvasottként');
DEFINE('_GEN_FORUM_MARKED','Az ebben a fórumban lévő összes hozzászólást megjelöltük olvasottként');
DEFINE('_GEN_ALL_MARKED','Az összes hozzászólást megjelöltük olvasottként');
DEFINE('_IMAGE_UPLOAD','Kép feltöltése');
DEFINE('_IMAGE_DIMENSIONS','A képfájl mérete legfeljebb (szélesség x magasság - méret)');
DEFINE('_IMAGE_ERROR_TYPE','Kérjük, hogy csak JPG, GIF vagy PNG típusú képet tölts fel');
DEFINE('_IMAGE_ERROR_EMPTY','Válaszd ki a fájlt a feltöltés előtt');
DEFINE('_IMAGE_ERROR_SIZE','A képfájl mérete nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_IMAGE_ERROR_WIDTH','A képfájl szélessége nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_IMAGE_ERROR_HEIGHT','A képfájl magassága nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_IMAGE_UPLOADED','A kép feltöltése sikerült.');
DEFINE('_COM_A_IMAGE','Képek');
DEFINE('_COM_A_IMGHEIGHT','Legnagyobb képmagasság');
DEFINE('_COM_A_IMGWIDTH','Legnagyobb képszélesség');
DEFINE('_COM_A_IMGSIZE','A képfájl legnagyobb mérete<br/><em>kilobájtban</em>');
DEFINE('_COM_A_IMAGEUPLOAD','A nyilvános képfeltöltés engedélyezése');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Az &quot;Igen&quot; választása esetén mindenki (látogatók) tölthet fel képet.');
DEFINE('_COM_A_IMAGEREGUPLOAD','A képfeltöltés engedélyezése a regisztráltaknak');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Az &quot;Igen&quot; választásával engedélyezheted a regisztrált és bejelentkezett felhasználóknak a képfeltöltést.<br/> Megjegyzés: A (fő)adminisztrátorok és a moderátorok mindig tudnak képeket feltölteni.');
  //New since preRC4-II:
DEFINE('_COM_A_UPLOADS','Feltöltések');
DEFINE('_FILE_TYPES','A fájltípus ez lehet - legnagyobb méret');
DEFINE('_FILE_ERROR_TYPE','Csak a következő fájltípusok feltöltése engedélyezett:');
DEFINE('_FILE_ERROR_EMPTY','Kérjük, hogy válaszd ki a fájlt a feltöltés előtt');
DEFINE('_FILE_ERROR_SIZE','A fájl mérete túllépi az adminisztrátorok által engedélyezettet.');
DEFINE('_COM_A_FILE','Fájlok');
DEFINE('_COM_A_FILEALLOWEDTYPES','Engedélyezett fájltípusok');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Add meg a feltölthető fájltípusokat. Vesszővel elválasztott szóköz nélküli <strong>kisbetűs</strong> listát használj.<br />Például: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Legnagyobb fájlméret<br/><em>kilobájtban</em>');
DEFINE('_COM_A_FILEUPLOAD','A nyilvános fájlfeltöltés engedélyezése');
DEFINE('_COM_A_FILEUPLOAD_DESC','Az &quot;Igen&quot; választása esetén mindenki (látogatók) tölthet fel fájlokat.');
DEFINE('_COM_A_FILEREGUPLOAD','A fájlfeltöltés engedélyezése a regisztráltaknak');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Az &quot;Igen&quot; választása esetén csak a regisztrált és bejelentkezett felhasználók tölthetnek fel fájlokat.<br/> Megjegyzés: A (fő)adminisztrátorok és moderátorok mindig tölthetnek fel fájlokat.');
DEFINE('_SUBMIT_CANCEL','A hozzászólás beküldésének visszavonása megtörtént');
DEFINE('_HELP_SUBMIT','Kattints ide a hozzászólás elküldéséhez');
DEFINE('_HELP_PREVIEW','Kattints ide a hozzászólás előzetes megtekintéséhez');
DEFINE('_HELP_CANCEL','Kattints ide a hozzászólás törléséhez');
DEFINE('_POST_DELETE_ATT','Ha bejelölöd ezt a jelölőnégyzetet, akkor a törölni kívánt hozzászólások kép- és fájlmellékletei is törlésre kerülnek (ajánlott).');
   //new since preRC4-III
DEFINE('_COM_A_USER_MARKUP','Látható a Módosította megjelölés');
DEFINE('_COM_A_USER_MARKUP_DESC','Az &quot;Igen&quot; választása esetén a módosított hozzászólások olyan szöveggel jelölhetők, ami arról értesít, hogy egy felhasználó módosította-e és mikor a hozzászólást.');
DEFINE('_EDIT_BY','Módosította:');
DEFINE('_EDIT_AT','Időpont:');
DEFINE('_UPLOAD_ERROR_GENERAL','Hiba történt az avatár feltöltésekor. Kérjük, hogy próbáld meg újra, vagy értesítsd a rendszergazdát!');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Feltöltött képek tallózó');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Feltöltött fájlok tallózó');
DEFINE('_COM_A_IMGB_TOTAL_IMG','A feltöltött képek száma');
DEFINE('_COM_A_IMGB_TOTAL_FILES','A feltöltött fájlok száma');
DEFINE('_COM_A_IMGB_ENLARGE','Kattints a képre a teljes méretű kép megtekintéséhez');
DEFINE('_COM_A_IMGB_DOWNLOAD','Kattints a fájl képére a letöltéshez');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'A &quot;Helyettesítés álképpel&quot; beállítás álképre cseréli a kiválasztott képet.<br /> Ezáltal a postázás megszakítása nélkül távolíthatod el a tényleges fájlt.<br /><small><em>A csere megjelenítéséhez esetenként frissíteni kell az oldalt a böngészőben.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY','Jelenlegi álkép');
DEFINE('_COM_A_IMGB_REPLACE','Helyettesítés álképpel');
DEFINE('_COM_A_IMGB_REMOVE','Teljes törlés');
DEFINE('_COM_A_IMGB_NAME','Név');
DEFINE('_COM_A_IMGB_SIZE','Fájlméret');
DEFINE('_COM_A_IMGB_DIMS','Képméret');
DEFINE('_COM_A_IMGB_CONFIRM','Teljesen biztos, hogy törlöd ezt a fájlt? \n A fájl törlése tönkreteheti a hozzászólásban lévő hivatkozást.');
DEFINE('_COM_A_IMGB_VIEW','Üzenet megnyitása (szerkesztésre)');
DEFINE('_COM_A_IMGB_NO_POST','Nincs hivatkozó üzenet!');
DEFINE('_USER_CHANGE_VIEW','Ezen beállítások módosításai a fórum következő felkeresésekor lépnek érvénybe.<br /> A nézettípust a fórum menüsoráról hozzáférhető beállításokkal válthatod át &quot;Mid-Flight&quot;-ra.');
DEFINE('_MOSBOT_DISCUSS_A','Szólj hozzá a fórumban. (');
DEFINE('_MOSBOT_DISCUSS_B',' hozzászólás)');
DEFINE('_POST_DISCUSS','Ebben a témában a következő cikket vitatjuk meg');
DEFINE('_COM_A_RSS','Az RSS-csatorna engedélyezése');
DEFINE('_COM_A_RSS_DESC','Az RSS-csatornával a felhasználók az asztalukra/RSS-olvasójukba tölthetik le a legújabb hozzászólásokat (lásd <a href="http://www.rssreader.com" target="_blank">rssreader.com</a>).');
DEFINE('_LISTCAT_RSS','egyenesen az asztalra hozza a legújabb hozzászólásokat');
DEFINE('_SEARCH_REDIRECT','A Kunenának szüksége van a hozzáférési jogaidra, mielőtt a keresést végrehajtja.\nEz akár 30 másodpercet is igénybe vehet, ami normális dolog.\nKüldd el újra a keresési kérést.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG','Kunena beállításai');
DEFINE('_COM_A_DISPLAY','Tételek #');
DEFINE('_COM_A_CURRENT_SETTINGS','Jelenlegi beállítás');
DEFINE('_COM_A_EXPLANATION','Magyarázat');
DEFINE('_COM_A_BOARD_TITLE','A fórum neve');
DEFINE('_COM_A_BOARD_TITLE_DESC','A fórum megnevezése');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE','Alapértelmezett nézet');
//DEFINE('_COM_A_VIEW_TYPE_DESC','Válassz a füzérszerű és a sima nézet közül');
DEFINE('_COM_A_THREADS','Témák oldalanként');
DEFINE('_COM_A_THREADS_DESC','Az oldalanként megjelenítendő témák száma');
DEFINE('_COM_A_REGISTERED_ONLY','Csak regisztrált felhasználók számára');
DEFINE('_COM_A_REG_ONLY_DESC','Az &quot;Igen&quot; választása esetén csak a regisztrált felhasználók használhatják a fórumot (megtekintés és hozzászólás), A &quot;Nem&quot; választása esetén bármilyen felhasználó használhatja.');
DEFINE('_COM_A_PUBWRITE','Nyilvános olvasás és írás');
DEFINE('_COM_A_PUBWRITE_DESC','Az &quot;Igen&quot; választásával mindenki számára engedélyezed a hozzászólást. A &quot;Nem&quot; választása esetén a látogatók olvashatják a fórumot, de csak a regisztrált felhasználók küldhetnek be hozzászólásokat.');
DEFINE('_COM_A_USER_EDIT','Felhasználók módosításai');
DEFINE('_COM_A_USER_EDIT_DESC','Az &quot;Igen&quot; választásával engedélyezheted a regisztrált felhasználóknak a saját hozzászólásaik módosítását.');
DEFINE('_COM_A_MESSAGE','A fenti értékek változtatásainak mentéséhez nyomd meg fent a &quot;Mentés&quot; gombot.');
DEFINE('_COM_A_HISTORY','Az Előzmények megjelenítése');
DEFINE('_COM_A_HISTORY_DESC','Az &quot;Igen&quot; választása esetén láthatók lesznek a téma előzményei a hozzászólás/idézet készítésekor');
DEFINE('_COM_A_SUBSCRIPTIONS','A témafigyelés engedélyezése');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók megrendelhetik a témafigyelést, és értesítést kapnak új hozzászólás érkezésekor');
DEFINE('_COM_A_HISTLIM','Előzmények korlátozása');
DEFINE('_COM_A_HISTLIM_DESC','Az előzményekben látható hozzászólások száma');
DEFINE('_COM_A_FLOOD','Áradásvédelem');
DEFINE('_COM_A_FLOOD_DESC','Azon másodpercek száma, ameddig a felhasználónak várnia kell két egymás utáni hozzászólás között. Az áradásvédelem kikapcsolásához állítsd 0 (nulla) értékre. MEGJEGYZÉS: Az áradásvédelem <em>lassíthatja</em> a működést.');
DEFINE('_COM_A_MODERATION','E-mail a moderátoroknak');
DEFINE('_COM_A_MODERATION_DESC',
    'Az &quot;Igen&quot; választása esetén a fórum moderátorai értesítést kapnak e-mailben az új hozzászólásokról. Megjegyzés: minden (fő)adminisztrátor automatikusan moderátori jogokkal is rendelkezik, 
tehát kap értesítő levelet!');
DEFINE('_COM_A_SHOWMAIL','Az e-mail cím megjelenítése');
DEFINE('_COM_A_SHOWMAIL_DESC','A &quot;Nem&quot; választása esetén láthatatlanok maradnak a felhasználók e-mail címei; még a regisztrált felhasználóké is.');
DEFINE('_COM_A_AVATAR','Az avatárok engedélyezése');
DEFINE('_COM_A_AVATAR_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók avatárokat használhatnak a profiljaikban');
DEFINE('_COM_A_AVHEIGHT','Az avatár legnagyobb magassága');
DEFINE('_COM_A_AVWIDTH','Az avatár legnagyobb szélessége');
DEFINE('_COM_A_AVSIZE','Az avatár legnagyobb fájlmérete<br/><em>kilobájtban</em>');
DEFINE('_COM_A_USERSTATS','A felhasználói statisztika megjelenítése');
DEFINE('_COM_A_USERSTATS_DESC','Az &quot;Igen&quot; választása esetén látható lesz a felhasználói statisztika, mint a hozzászólások száma, a felhasználócsoport (adminisztrátor, moderátor, felhasználó stb.).');
DEFINE('_COM_A_AVATARUPLOAD','Az avatárfeltöltés engedélyezése');
DEFINE('_COM_A_AVATARUPLOAD_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók avatárokat tölthetnek fel.');
DEFINE('_COM_A_AVATARGALLERY','Avatárgaléria használata');
DEFINE('_COM_A_AVATARGALLERY_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók előre megadott avatárokból választhatnak (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók rangot kapnak a beküldött hozzászólások száma alapján.<br/><strong>Ehhez a felhasználói oldal fülön is engedélyezned kell a Felhasználói statisztika megjelenítését.');
DEFINE('_COM_A_RANKINGIMAGES','Rangjelző képek használata');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Az &quot;Igen&quot; választása esetén a regisztrált felhasználók rangjának kijelzése képpel történik (components/com_kunena/ranks). Kikapcsolása esetén a rang kijelzése szöveggel történik. További információ a rangképekről a www.kunena.com címen lelhető dokumentációban');

//email and stuff
$_COM_A_NOTIFICATION ="Új hozzászólás: ";
$_COM_A_NOTIFICATION1 = "Új hozzászólás érkezett egy olyan témára, amelynek megrendelted a figyelését a következő fórumban: ";
$_COM_A_NOTIFICATION2 = "A témafigyeléseidet a fórum kezdőlapján található 'Profil' hivatkozás követésével kezelheted, miután bejelentkeztél a webhelyre. A profilodban le is mondhatod a témafigyelést.";
$_COM_A_NOTIFICATION3 = "Ne válaszolj erre az értesítésre, ez egy automatikusan generált e-mail.";
$_COM_A_NOT_MOD1 = "Új hozzászólás érkezett egy olyan fórumba, amelynek a moderátora vagy a következő fórumban: ";
$_COM_A_NOT_MOD2 = "Kérjük, hogy tekintsd meg, miután bejelentkeztél!";
DEFINE('_COM_A_NO','Nem');
DEFINE('_COM_A_YES','Igen');
DEFINE('_COM_A_FLAT','Sima');
DEFINE('_COM_A_THREADED','Füzérszerű');
DEFINE('_COM_A_MESSAGES','Hozzászólások oldalanként');
DEFINE('_COM_A_MESSAGES_DESC','Az oldalanként megjelenítendő hozzászólások száma');
   //admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME','A felhasználónév használata');
DEFINE('_COM_A_USERNAME_DESC','Az &quot;Igen&quot; választása esetén (a bejelentkezéshez hasonlóan) a felhasználónév lesz használatban a valódi név helyett');
DEFINE('_COM_A_CHANGENAME','A felhasználónév megváltoztatásának engedélyezése');
DEFINE('_COM_A_CHANGENAME_DESC','Az &quot;Igen&quot; választása esetén a regisztrált felhasználók megváltoztathatják a felhasználónevüket a hozzászólások beküldésekor. A &quot;Nem&quot; választása esetén nem tudják a regisztrált felhasználók módosítani a nevüket.');
   //admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE','A fórum leállítása');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','Az &quot;Igen&quot; választása esetén leállításra kerül a fórum. A (fő)adminisztrátor részére böngészhető marad.');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Az üzemen kívüli fórum üzenete');
DEFINE('_COM_A_PRUNE','Fórumok kitakarítása');
DEFINE('_COM_A_PRUNE_NAME','Kitakarítandó fórum:');
DEFINE('_COM_A_PRUNE_DESC',
    'A Fórumok kitakarítása funkcióval azokat a témákat távolíthatod el, melyekbe nem érkeztek új hozzászólások a megadott napok óta. A kiemelt vagy a nyíltan lezárt témákat nem távolítja el; ezeket kézzel kell törölnöd. A lezárt fórumokban lévő témák nem takaríthatók ki.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Témák kitakarítása, melyekbe nem érkezett hozzászólás az elmúlt ');
DEFINE('_COM_A_PRUNE_DAYS','napon belül');
DEFINE('_COM_A_PRUNE_USERS','Felhasználók kitakarítása');
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'Ezzel a funkcióval a Kunena felhasználók névsorát takaríthatod ki a Joomla! webhely felhasználólista alapján. Minden olyan Kunena felhasználó profilját fogja törölni, melyek törlésre kerültek a Joomla! keretrendszerből.<br/>Amikor biztosan akarod folytatni, akkor a fenti menüsávban kattints a &quot;Kitakarítás indítása&quot; hivatkozásra.');
//general
DEFINE('_GEN_ACTION','Művelet');
DEFINE('_GEN_AUTHOR','Hozzászóló');
DEFINE('_GEN_BY','Írta:');
DEFINE('_GEN_CANCEL','Mégse');
DEFINE('_GEN_CONTINUE','Küldés');
DEFINE('_GEN_DATE','Dátum');
DEFINE('_GEN_DELETE','Törlés');
DEFINE('_GEN_EDIT','Szerkesztés');
DEFINE('_GEN_EMAIL','E-mail');
DEFINE('_GEN_EMOTICONS','Hangulatjelek');
DEFINE('_GEN_FLAT','Sima');
DEFINE('_GEN_FLAT_VIEW','Sima nézet');
DEFINE('_GEN_FORUMLIST','Fórumok');
DEFINE('_GEN_FORUM','Fórum');
DEFINE('_GEN_HELP','Súgó');
DEFINE('_GEN_HITS','Találatok');
DEFINE('_GEN_LAST_POST','Utolsó hozzászólás');
DEFINE('_GEN_LATEST_POSTS','Új hozzászólások');
DEFINE('_GEN_LOCK','Lezárás');
DEFINE('_GEN_UNLOCK','Kinyitás');
DEFINE('_GEN_LOCKED_FORUM','Lezárt fórum');
DEFINE('_GEN_LOCKED_TOPIC','Lezárt téma');
DEFINE('_GEN_MESSAGE','Hozzászólás');
DEFINE('_GEN_MODERATED','Moderált fórum; a hozzászólásokat közzététel előtt ellenőrizzük.');
DEFINE('_GEN_MODERATORS','Moderátorok');
DEFINE('_GEN_MOVE','Áthelyezés');
DEFINE('_GEN_NAME','Név');
DEFINE('_GEN_POST_NEW_TOPIC','Új téma indítása');
DEFINE('_GEN_POST_REPLY','Hozzászólás a témához');
DEFINE('_GEN_MYPROFILE','Profil');
DEFINE('_GEN_QUOTE','Idézés');
DEFINE('_GEN_REPLY','Válasz');
DEFINE('_GEN_REPLIES','Hozzá-<br />szólások');
DEFINE('_GEN_THREADED','Füzérszerű');
DEFINE('_GEN_THREADED_VIEW','Füzérszerű nézet');
DEFINE('_GEN_SIGNATURE','Aláírás');
DEFINE('_GEN_ISSTICKY','Kiemelt téma');
DEFINE('_GEN_STICKY','Kiemelés');
DEFINE('_GEN_UNSTICKY','Kiemelés megszüntetése');
DEFINE('_GEN_SUBJECT','Tárgy');
DEFINE('_GEN_SUBMIT','Mentés');
DEFINE('_GEN_TOPIC','Téma');
DEFINE('_GEN_TOPICS','Témák');
DEFINE('_GEN_TOPIC_ICON','Bélyeg');
DEFINE('_GEN_SEARCH_BOX','Keresés a fórumban');
$_GEN_THREADED_VIEW="Füzérszerű nézet";
$_GEN_FLAT_VIEW    ="Sima nézet";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD','Feltöltés');
DEFINE('_UPLOAD_DIMENSIONS','A feltölthető kép legnagyobb mérete (szélesség x magasság - méret)');
DEFINE('_UPLOAD_SUBMIT','Új avatár feltöltése');
DEFINE('_UPLOAD_SELECT_FILE','Válaszd ki a fájlt');
DEFINE('_UPLOAD_ERROR_TYPE','Csak JPG, GIF vagy PNG képet válassz');
DEFINE('_UPLOAD_ERROR_EMPTY','Kérjük, hogy válaszd ki a fájlt a feltöltés előtt');
DEFINE('_UPLOAD_ERROR_NAME','A fájlnévben csak alfanumerikus karakterek lehetnek szóköz nélkül.');
DEFINE('_UPLOAD_ERROR_SIZE','A képfájl mérete nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_UPLOAD_ERROR_WIDTH','A képfájl szélessége nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_UPLOAD_ERROR_HEIGHT','A képfájl magassága nagyobb az adminisztrátor által engedélyezettnél.');
DEFINE('_UPLOAD_ERROR_CHOOSE',"Nem választhatsz avatárt a galériából.");
DEFINE('_UPLOAD_UPLOADED','Az avatár feltöltése sikerült.');
DEFINE('_UPLOAD_GALLERY','Válassz az avatárgalériából:');
DEFINE('_UPLOAD_CHOOSE','Választás jóváhagyása');
// listcat.php
DEFINE('_LISTCAT_ADMIN','Egy adminisztrátornak előbb létre kell hoznia itt: ');
DEFINE('_LISTCAT_DO','Ők tudni fogják, hogy mit kell tenniük.');
DEFINE('_LISTCAT_INFORM','Tájékoztasd őket, és mondd meg nekik, hogy siessenek!');
DEFINE('_LISTCAT_NO_CATS','Még nem hozták létre a fórum kategóriáit.');
DEFINE('_LISTCAT_PANEL','Joomla! OS CMS adminisztrációs panel.');
DEFINE('_LISTCAT_PENDING','függőben lévő hozzászólás(ok)');
// moderation.php
DEFINE('_MODERATION_MESSAGES','Nincs függőben lévő hozzászólás ebben a fórumban.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE','A következő hozzászólás törlésére készülsz');
DEFINE('_POST_ABOUT_DELETE','<strong>MEGJEGYZÉSEK:</strong><br/>
- Ha törölsz egy témát (egy téma első hozzászólását), akkor minden további hozzászólás is törlésre kerül! 
..Ha csak a tartalmat kell törölni, akkor a hozzászólás és a beküldő neve legyen üres..
<br/>
- Ekkor minden hozzászólás előbbre sorolódik.');
DEFINE('_POST_CLICK','kattints ide');
DEFINE('_POST_ERROR','A felhasználónév/e-mail cím nem található. Súlyos adatbázishiba nem szerepel a listán.');
DEFINE('_POST_ERROR_MESSAGE','Ismeretlen SQL hiba történt, és a hozzászólás beküldése nem történt meg.  Ha a probléma továbbra is fennáll, akkor értesítsd az adminisztrátort.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Ismeretlen hiba történt, és a hozzázólás módosítása nem történt meg.  Kérjük, próbáld meg újra.  Ha a hiba továbbra is fennáll, akkor értesítsd az adminisztrátort.');
DEFINE('_POST_ERROR_TOPIC','Hiba történt a törlés során. Kérjük, ellenőrizd az alábbi hibákat');
DEFINE('_POST_FORGOT_NAME','Elfelejtetted megadni a nevedet. Kattints a böngésződ Vissza gombjára, és próbáld újra.');
DEFINE('_POST_FORGOT_SUBJECT','Elfelejtetted megadni a tárgyat. Kattints a böngésződ Vissza gombjára, és próbáld újra.');
DEFINE('_POST_FORGOT_MESSAGE','Elfelejtetted megírni a hozzászólásodat. Kattints a böngésződ Vissza gombjára, és próbáld újra.');
DEFINE('_POST_INVALID','Érvénytelen hozzászólási azonosító kérelem érkezett.');
DEFINE('_POST_LOCK_SET','A téma lezárása megtörtént.');
DEFINE('_POST_LOCK_NOT_SET','A téma nem zárható le.');
DEFINE('_POST_LOCK_UNSET','A téma kinyitása megtörtént.');
DEFINE('_POST_LOCK_NOT_UNSET','A téma nem nyitható ki.');
DEFINE('_POST_MESSAGE','Új hozzászólás:');
DEFINE('_POST_MOVE_TOPIC','A téma áthelyezése ebbe a fórumba');
DEFINE('_POST_NEW','Új hozzászólás: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','A témafigyelés megrendelése nem dolgozható fel.');
DEFINE('_POST_NOTIFIED','Jelöld be, ha szeretnél értesítést kapni a beérkezett hozzászólásokról.');
DEFINE('_POST_STICKY_SET','A téma kiemelése megtörtént.');
DEFINE('_POST_STICKY_NOT_SET','Nem emelhető ki ez a téma.');
DEFINE('_POST_STICKY_UNSET','Ennek a témának a kiemelését megszüntettük.');
DEFINE('_POST_STICKY_NOT_UNSET','Nem szüntethető meg ennek a témának a kiemelése.');
DEFINE('_POST_SUBSCRIBE','Témafigyelés');
DEFINE('_POST_SUBSCRIBED_TOPIC','A témafigyelés megrendelése megtörtént.');
DEFINE('_POST_SUCCESS','A hozzászólás sikeresen beérkezett');
DEFINE('_POST_SUCCES_REVIEW','A hozzászólás sikeresen beérkezett. A moderátor ellenőrizni fogja a fórumban történő közzététel előtt.');
DEFINE('_POST_SUCCESS_REQUEST','A kérés feldolgozása megtörtént. Ha nem jutsz vissza néhány pillanaton belül a témához,');
DEFINE('_POST_TOPIC_HISTORY','Téma előzményei');
DEFINE('_POST_TOPIC_HISTORY_MAX','Max. látható az utolsó');
DEFINE('_POST_TOPIC_HISTORY_LAST','hozzászólás  -  <i>(Az utolsóval kezdődik)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','A téma nem helyezhető át. A témához való visszatéréshez:');
DEFINE('_POST_TOPIC_FLOOD1','A fórum adminisztrátora engedélyezte az áradatvédelmet, és úgy döntött, hogy ');
DEFINE('_POST_TOPIC_FLOOD2',' másodpercet kell várni egy újabb hozzászólás beküldéséig.');
DEFINE('_POST_TOPIC_FLOOD3','Kattints a böngésző Vissza gombjára a fórumba való visszatéréshez.');
DEFINE('_POST_EMAIL_NEVER','Az e-mail címed sohasem jelenik meg az oldalakon.');
DEFINE('_POST_EMAIL_REGISTERED','Az e-mail címedet csak a regisztrált felhasználók látják.');
DEFINE('_POST_LOCKED','adminisztrátor által lezárva.');
DEFINE('_POST_NO_NEW','Új hozzászólás nem engedélyezett.');
DEFINE('_POST_NO_PUBACCESS1','Az adminisztrátor megtiltotta a mindenki által történő hozzászólást.');
DEFINE('_POST_NO_PUBACCESS2','Csak a regisztrált / bejelentkezett felhasználók számára<br />engedélyezett a hozzászólás.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','>> Ebben a fórumban nem indított még senki témát <<');
DEFINE('_SHOWCAT_PENDING','függőben lévő hozzászólás(ok)');
// userprofile.php
DEFINE('_USER_DELETE',' Az aláírás törlése');
DEFINE('_USER_ERROR_A','Erre az oldalra hiba miatt érkeztél. Kérjük, értesítsd az adminisztrátort, hogy melyik hivatkozásokra ');
DEFINE('_USER_ERROR_B','kattintottál, amik ide irányítottak téged. Ő majd javítja a hibát.');
DEFINE('_USER_ERROR_C','Köszönjük!');
DEFINE('_USER_ERROR_D','A jelentésbe beveendő hibaszám: ');
DEFINE('_USER_GENERAL','Arculat és elrendezés');
DEFINE('_USER_MODERATOR','Általam moderált fórumok');
DEFINE('_USER_MODERATOR_NONE','Nem található hozzád hozzárendelt fórum');
DEFINE('_USER_MODERATOR_ADMIN','Az adminisztrátorok az összes fórumban moderátorok.');
DEFINE('_USER_NOSUBSCRIPTIONS','Nincs figyelt témám');
DEFINE('_USER_PREFERED','Használandó nézettípus');
DEFINE('_USER_PROFILE','Profil: ');
DEFINE('_USER_PROFILE_NOT_A','A profilod ');
DEFINE('_USER_PROFILE_NOT_B','nem');
DEFINE('_USER_PROFILE_NOT_C',' frissíthető.');
DEFINE('_USER_PROFILE_UPDATED','A profilodat módosítottuk.');
DEFINE('_USER_RETURN_A','Ha néhány pillanaton belül nem irányítunk vissza a profilodhoz, akkor ');
DEFINE('_USER_RETURN_B','kattints ide!');
DEFINE('_USER_SUBSCRIPTIONS','Témafigyeléseim');
DEFINE('_USER_UNSUBSCRIBE','Lemondás');
DEFINE('_USER_UNSUBSCRIBE_A','Te nem');
DEFINE('_USER_UNSUBSCRIBE_B','mondhatod le');
DEFINE('_USER_UNSUBSCRIBE_C',' ennek a témának a figyelését.');
DEFINE('_USER_UNSUBSCRIBE_YES','A témafigyelés lemondása megtörtént.');
DEFINE('_USER_DELETEAV',' Jelöld be, ha törölni akarod az avatárt');
//New 0.9 to 1.0
DEFINE('_USER_ORDER','A hozzászólások sorrendje');
DEFINE('_USER_ORDER_DESC','Az utolsóval kezdődik');
DEFINE('_USER_ORDER_ASC','Az elsővel kezdődik');
// view.php
DEFINE('_VIEW_DISABLED','Az adminisztrátor megtiltotta a mindenki által történő hozzászólást.');
DEFINE('_VIEW_POSTED','Írta:');
DEFINE('_VIEW_SUBSCRIBE',':: A témafigyelés megrendelése ::');
DEFINE('_MODERATION_INVALID_ID','Érvénytelen hozzászólás-azonosítószám kérése történt.');
DEFINE('_VIEW_NO_POSTS','Nincs hozzászólás ebben a fórumban.');
DEFINE('_VIEW_VISITOR','Vendég');
DEFINE('_VIEW_ADMIN','Adminisztrátor');
DEFINE('_VIEW_USER','Felhasználó');
DEFINE('_VIEW_MODERATOR','Moderátor');
DEFINE('_VIEW_REPLY','Válasz erre a hozzászólásra');
DEFINE('_VIEW_EDIT','A hozzászólás módosítása');
DEFINE('_VIEW_QUOTE','A hozzászólás idézése új hozzászólásban');
DEFINE('_VIEW_DELETE','A hozzászólás törlése');
DEFINE('_VIEW_STICKY','A téma kiemelése');
DEFINE('_VIEW_UNSTICKY','A téma kiemelésének megszüntetése');
DEFINE('_VIEW_LOCK','A téma lezárása');
DEFINE('_VIEW_UNLOCK','A téma kinyitása');
DEFINE('_VIEW_MOVE','A téma áthelyezése másik fórumba');
DEFINE('_VIEW_SUBSCRIBETXT','A témafigyelés megrendelése, és értesítés e-mailben az új hozzászólásokról');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME','Fórum');
DEFINE('_POSTS','Hozzászólások:');
DEFINE('_TOPIC_NOT_ALLOWED','Hozzászólás');
DEFINE('_FORUM_NOT_ALLOWED','Fórum');
DEFINE('_FORUM_IS_OFFLINE','A fórum nem üzemel!');
DEFINE('_PAGE','Oldal: ');
DEFINE('_NO_POSTS','Nincs hozzászólás');
DEFINE('_CHARS','karakter legfeljebb');
DEFINE('_HTML_YES','A HTML-kód letiltott');
DEFINE('_YOUR_AVATAR','<b>Avatár</b>');
DEFINE('_NON_SELECTED','Neked még nincs avatárod <br />');
DEFINE('_SET_NEW_AVATAR','Új avatár kiválasztása');
DEFINE('_THREAD_UNSUBSCRIBE','Lemondás');
DEFINE('_SHOW_LAST_POSTS','Aktív témák, utolsó');
DEFINE('_SHOW_HOURS','óra');
DEFINE('_SHOW_POSTS','összesen: ');
DEFINE('_DESCRIPTION_POSTS','Az aktív témák legújabb hozzászólásai láthatók');
DEFINE('_SHOW_4_HOURS','4 óra');
DEFINE('_SHOW_8_HOURS','8 óra');
DEFINE('_SHOW_12_HOURS','12 óra');
DEFINE('_SHOW_24_HOURS','24 óra');
DEFINE('_SHOW_48_HOURS','48 óra');
DEFINE('_SHOW_WEEK','hét');
DEFINE('_POSTED_AT','Érkezett');
DEFINE('_DATETIME','Y.m.d H:i');
DEFINE('_NO_TIMEFRAME_POSTS','Nem érkezett új hozzászólás a kiválasztott időtartamon belül.');
DEFINE('_MESSAGE','Hozzászólás');
DEFINE('_NO_SMILIE','nincs');
DEFINE('_FORUM_UNAUTHORIZIED','A fórum csak regisztrált és bejelentkezett felhasználók számára nyitott.');
DEFINE('_FORUM_UNAUTHORIZIED2','Ha már regisztráltál, akkor jelentkezz előbb be!');
DEFINE('_MESSAGE_ADMINISTRATION','Moderálás');
DEFINE('_MOD_APPROVE','Jóváhagyás');
DEFINE('_MOD_DELETE','Törlés');
//NEW in RC1
DEFINE('_SHOW_LAST','A legutolsó hozzászólás elolvasása');
DEFINE('_POST_WROTE','írta:');
DEFINE('_COM_A_EMAIL','A fórum e-mail címe');
DEFINE('_COM_A_EMAIL_DESC','Ez a fórum e-mail címe. Legyen érvényes az e-mail cím');
DEFINE('_COM_A_WRAP','A szavak tördelése, ha hosszabbak mint:');
DEFINE('_COM_A_WRAP_DESC',
    'Add meg egy szó lehetséges maximális hosszát. Ez lehetővé teszi, hogy a hozzászólások a használt sablonhoz igazodjanak.<br/> A 70 karakter elfogadható érték, de a tapasztalatod szerint változtathatsz rajta. Az URL-eket nem érinti a tördelés.');
DEFINE('_COM_A_SIGNATURE','Az aláírás hossza');
DEFINE('_COM_A_SIGNATURE_DESC','A felhasználó aláírásában engedélyezett karakterek száma.');
DEFINE('_SHOWCAT_NOPENDING','Nincs függőben lévő hozzászólás');
DEFINE('_COM_A_BOARD_OFSET','A fórum időeltolódása');
DEFINE('_COM_A_BOARD_OFSET_DESC','Némelyik fórum a felhasználókétól eltérő időzónában lévő kiszolgálókon működik. Állítsd be órában a Kunena által a hozzászólásokban használandó időeltolódást. Pozitív és negatív értéket egyaránt megadhatsz.');
//New in RC2
DEFINE('_COM_A_BASICS','Alapok');
DEFINE('_COM_A_FRONTEND','Felhasználói oldal');
DEFINE('_COM_A_SECURITY','Biztonság');
DEFINE('_COM_A_AVATARS','Avatárok');
DEFINE('_COM_A_INTEGRATION','Beépülés');
DEFINE('_COM_A_PMS','A magánüzenetek engedélyezése');
DEFINE('_COM_A_PMS_DESC',
    'Ha telepítetted valamelyik magánüzenet-küldő komponenst, akkor válaszd itt ki a megfelelőt. A Clexus PM választása a ClexusPM felhasználói profillal kapcsolatos beállításokat (mint ICQ, AIM, Yahoo, MSN) és a profilhivatkozásokat is engedélyezni fogja, ha a felhasznált Kunena sablon támogatja.');
DEFINE('_VIEW_PMS','Kattints ide, ha magánüzenetet akarsz küldeni ennek a felhasználónak');
//new in RC3
DEFINE('_POST_RE','Vá: ');
DEFINE('_BBCODE_BOLD','Félkövér szöveg: [b]szöveg[/b] ');
DEFINE('_BBCODE_ITALIC','Dőlt szöveg: [i]szöveg[/i]');
DEFINE('_BBCODE_UNDERL','Aláhúzott szöveg: [u]szöveg[/u]');
DEFINE('_BBCODE_QUOTE','Idézett szöveg: [quote]szöveg[/quote]');
DEFINE('_BBCODE_CODE','Kód: [code]kód[/code]');
DEFINE('_BBCODE_ULIST','Rendezetlen lista: [ul] [li]szöveg[/li] [/ul] - Tanács: a listának listatételeket kell tartalmaznia');
DEFINE('_BBCODE_OLIST','Rendezett lista: [ol] [li]szöveg[/li] [/ol] - Tanács: a listának listatételeket kell tartalmaznia');
DEFINE('_BBCODE_IMAGE','Kép: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK','Hivatkozás: [url=http://www.valahol.hu/]Ez egy hivatkozás[/url]');
DEFINE('_BBCODE_CLOSA','Az összes kódelem bezárása');
DEFINE('_BBCODE_CLOSE','Az összes nyitott BBCode bezárása');
DEFINE('_BBCODE_COLOR','Szín: [color=#FF6600]szöveg[/color]');
DEFINE('_BBCODE_SIZE','Méret: [size=1]szövegméret[/size] - Tanács: 1 és 5 közti méretek');
DEFINE('_BBCODE_LITEM','Listatétel: [li] listatétel [/li]');
DEFINE('_BBCODE_HINT','BBCode súgó - Tanács: A BBCode-ot kijelölt szöveghez használhatod!');
DEFINE('_COM_A_TAWIDTH','A szövegterület szélessége');
DEFINE('_COM_A_TAWIDTH_DESC','Állítsd be a sablonnak megfelelően a hozzászólás/válasz szövegterületének szélességét. <br/>A hangulatjel eszköztárat két sorba fogja tördelni, ha a szélesség <= 420 képpont');
DEFINE('_COM_A_TAHEIGHT','A szövegterület magassága');
DEFINE('_COM_A_TAHEIGHT_DESC','Állítsd be a sablonnak megfelelően a hozzászólás/válasz szövegterületének magasságát');
DEFINE('_COM_A_ASK_EMAIL','Az e-mail cím kérése');
DEFINE('_COM_A_ASK_EMAIL_DESC','Meg kell adniuk hozzászóláskor a felhasználóknak vagy a látogatóknak az e-mail címüket. Válaszd a &quot;Nem&quot;-et, ha mellőzni akarod ez a funkciót a felhasználói oldalon. Nem fogjuk kérni a látogatók e-mail címét.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Rangok kezelése');
define('_KUNENA_SORTRANKS', 'Rendezés rangok szerint');

define('_KUNENA_RANKSIMAGE', 'Rangkép');
define('_KUNENA_RANKS', 'Rangnév');
define('_KUNENA_RANKS_SPECIAL', 'Speciális tag');
define('_KUNENA_RANKSMIN', 'Min. hozzászólás');
define('_KUNENA_RANKS_ACTION', 'Műveletek');
define('_KUNENA_NEW_RANK', 'Új rang');

?>
