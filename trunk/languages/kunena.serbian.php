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
*            Language: Serbian
*         For version: 1.0.8
*            Encoding: UTF-8
*              Author: Dragan Zečević <dragan@megasystem.ch>
*                      Miloš Komarčević <kmilos@gmail.com>, Fedora Srbija (http://fedora.fsn.org.rs)
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Директан приступ овој локацији није дозвољен.');

// 1.0.8
DEFINE('_POST_FORGOT_EMAIL', 'Заборавили сте да приложите своју адресу е-поште.  Притисните дугме за назад у вашем читачу да бисте се вратили назад и покушали поново.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Све је обрисано, недостајале су неке датотеке прилога!');
// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Укључи обележавање кода');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Укључује Kunena јава скрипту за обележавање ознака кода. Ако ваши чланови објављују php и сличне одломке кода унутар ознака кода, укључивање овога ће обојити код. Ако ваш форум не користи такве поруке са програмским језицима, можда би требало да искључите ово како би се избегло погрешно формирање ознака кода.');
DEFINE('_COM_A_RSS_TYPE', 'Подразумевана RSS врста');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Изаберите између RSS извора по теми или по поруци. По теми значи да ће само једна ставка по теми бити наведена у RSS извору, независно од броја порука које су објављене у тој теми. По теми производи краћи и компактнији RSS извор али неће навести сваки одговор.');
DEFINE('_COM_A_RSS_BY_THREAD', 'По теми');
DEFINE('_COM_A_RSS_BY_POST', 'По поруци');
DEFINE('_COM_A_RSS_HISTORY', 'RSS историјат');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Изаберите колики историјат треба укључити у RSS извор. Подразумевано је 1 месец али би можда требало да га ограничите на 1 недељу на сајтовима са великим прометом.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 недеља');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 месец');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 година');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Подразумевана Kunena страница');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Изаберите подразумевану Kunena страницу која ће се приказати када се кликне веза ка форуму или када се прво уђе у форум. Подразумеване су скорашње дискусије. Треба да буде постављено на категорије за шаблоне који нису default_ex. Ако су изабране моје дискусије, гости ће подразумевано видети скорашње дискусије.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Скорашње дискусије');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Моје дискусије');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Категорије');
DEFINE('_KUNENA_BBCODE_HIDE', 'Следеће је сакривено од нерегистрованих корисника:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Упозорење, откривање!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Родитељски форум не сме бити исти.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Родитељски форум је један од своје сопствене деце.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'ИД форума не постоји.');
DEFINE('_KUNENA_RECURSION', 'Откривена је рекурзија.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Заборавили сте да унесете име.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Заборавили сте да унесете адресу е-поште.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Заборавили сте да унесете тему.');
DEFINE('_KUNENA_EDIT_TITLE', 'Уредите своје детаље');
DEFINE('_KUNENA_YOUR_NAME', 'Ваше име:');
DEFINE('_KUNENA_EMAIL', 'е-пошта:');
DEFINE('_KUNENA_UNAME', 'Корисничко име:');
DEFINE('_KUNENA_PASS', 'Лозинка:');
DEFINE('_KUNENA_VPASS', 'Потврдите лозинку:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Кориснички детаљи су сачувани.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Заслуге');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode поставке');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Прикажи ознаку за откривање у траци са алаткама уређивача');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Поставите на „Да“ ако желите да ознака [spoiler] (откривање) буде приказана у траци са алаткама уређивача поруке.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Прикажи ознаку за видео у траци са алаткама уређивача');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Поставите на „Да“ ако желите да ознака [video] буде приказана у траци са алаткама уређивача поруке.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Прикажи ознаку за eBay у траци са алаткама уређивача');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Поставите на „Да“ ако желите да ознака [ebay] буде приказана у траци са алаткама уређивача поруке.');
DEFINE('_COM_A_TRIMLONGURLS', 'Скрати дугачке УРЛ-ове');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Поставите на „Да“ ако желите да дугачки УРЛ-ови буду скраћени. Погледајте поставке за скраћивање почетка и завршетка УРЛ-а.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Почетни део скраћених УРЛ-ова');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Број знакова за почетни део скраћених УРЛ-ова. Скраћивање дугачких УРЛ-ова мора бити постављено на „Да“.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Завршни део скраћених УРЛ-ова');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Број знакова за завршни део скраћених УРЛ-ова. Скраћивање дугачких УРЛ-ова мора бити постављено на „Да“.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Аутоматски угнезди YouTube видео');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Поставите на „Да“ ако желите да УРЛ-ови YouTube видеа буду аутоматски угнеждени.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Аутоматски угнезди eBay предмете');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Поставите на „Да“ ако желите да eBay предмети и претраге буду аутоматски угнеждени.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'Језички код eBay виџета');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Важно је поставити исправан језички код јер eBay виџет из њега закључује и језик и валуту. Подразумевано је en-us за ebay.com. Примери: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Дужина сесије');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Подразумевано је 1800 [секунди]. Дужина (до истека) сесије у секундама слично као и дужини Joomla сесије. Дужина сесије је важна за поновно одређивање права приступа, приказа ко је на вези и показатеља НОВО. Када сесија једном истекне ван тог времена, права приступа и показатељ НОВО се поново успостављају.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Састави');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Састави ову тему са');
DEFINE('_POST_MERGE_GHOST', 'Остави тему утвару');
DEFINE('_POST_SUCCESS_MERGE', 'Тема је успешно састављена.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Састављање није успело.');
DEFINE('_GEN_SPLIT', 'Растави');
DEFINE('_GEN_DOSPLIT', 'Иди');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Тема је успешно растављена.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Тема је успешно измењена.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Измена теме није успела.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Растављање није успело.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Дупликат, истоветна порука је игнорисана.');
DEFINE('_POST_SPLIT_HINT', '<br />Савет: Можете унапредити поруку на место теме ако је изаберете у другој колони и штиклирате да нема ничега за растављање.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'повежи сирочиће са темом');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Повежи сирочиће са новом поруком у теми.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'повежи сирочиће са претходном поруком');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Повежи сирочиће са претходном поруком.');
DEFINE('_POST_MERGE', 'састави');
DEFINE('_POST_MERGE_TITLE', 'Споји ову тему на прву поруку циља.');
DEFINE('_POST_INVERSE_MERGE', 'састави уназад');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Споји прву поруку циља на ову тему.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Ова тема је уклоњена из ваших омиљених.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Ова тема <b>НИЈЕ</b> уклоњена из ваших омиљених');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Ваш захтев за уклањање из омиљених је обрађен.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Ова тема је уклоњена из ваших претплаћених.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Ова тема <b>НИЈЕ</b> уклоњена из ваших претплаћених.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Ваш захтев за уклањање из претплаћених је обрађен.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Није изабрана циљна категорија. Ништа није премештено.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Скорашње дискусије');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Моје дискусије');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Дискусије које сам покренуо/ла или на које сам одговорио/ла');
DEFINE('_KUNENA_CATEGORY', 'Категорија:');
DEFINE('_KUNENA_CATEGORIES', 'Категорије');
DEFINE('_KUNENA_POSTED_AT', 'Објављено');
DEFINE('_KUNENA_AGO', 'раније');
DEFINE('_KUNENA_DISCUSSIONS', 'Дискусије');
DEFINE('_KUNENA_TOTAL_THREADS', 'Укупно тема:');
DEFINE('_SHOW_DEFAULT', 'Подразумевано');
DEFINE('_SHOW_MONTH', 'месец');
DEFINE('_SHOW_YEAR', 'година');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Копирам „%src%“ у „%dst%“...');
DEFINE('_KUNENA_COPY_OK', 'У реду');
DEFINE('_KUNENA_CSS_SAVE', 'Складиштење css датотеке треба да буде овде...датотека=„%file%“');
DEFINE('_KUNENA_UP_ATT_10', 'Табела прилога је успешно надграђена на структуру најновије 1.0.x серије!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Прилози у табели порука су успешно надграђени на структуру најновије 1.0.x серије!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Не могу да унапредим одговоре у хијерархији порука. Ништа није обрисано.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Не могу да обришем поруку(е) - ништа друго није обрисано');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Не могу да обришем текст поруке(а). Ажурирајте базу података ручно (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Све је обрисано, али ажурирање корисничке статистике порука није успело!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Озбиљна грешка у бази података. Ажурирајте базу података ручно тако да одговори на тему такође одговарају новом форуму");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena компонента је успешно деинсталирана!");
DEFINE('_KUNENA_PDF_VERSION', 'Верзија Kunena форум компоненте: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Направљено: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Нема форума за претрагу.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Грешка при додавању корисника:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Корисници су усаглашени; избрисано:');
DEFINE('_KUNENA_USERSSYNCADD', ', додато:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'профила корисника.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Нема профила за усаглашавање.');
DEFINE('_KUNENA_SYNC_USERS', 'Усагласи кориснике');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Усагласи Kunena табелу корисника са Joomla! табелом');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Е-пошта администратору');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Поставите на „Да“ ако желите да за сваку нову поруку буде послато обавештење е-поштом свим активним администраторима.');
DEFINE('_KUNENA_RANKS_EDIT', 'Измена рангова');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Сакриј е-пошту');
DEFINE('_KUNENA_DT_DATE_FMT','%d.%m.%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d.%m.%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'недеља');
DEFINE('_KUNENA_DT_LDAY_MON', 'понедељак');
DEFINE('_KUNENA_DT_LDAY_TUE', 'уторак');
DEFINE('_KUNENA_DT_LDAY_WED', 'среда');
DEFINE('_KUNENA_DT_LDAY_THU', 'четвртак');
DEFINE('_KUNENA_DT_LDAY_FRI', 'петак');
DEFINE('_KUNENA_DT_LDAY_SAT', 'субота');
DEFINE('_KUNENA_DT_DAY_SUN', 'нед');
DEFINE('_KUNENA_DT_DAY_MON', 'пон');
DEFINE('_KUNENA_DT_DAY_TUE', 'уто');
DEFINE('_KUNENA_DT_DAY_WED', 'сре');
DEFINE('_KUNENA_DT_DAY_THU', 'чет');
DEFINE('_KUNENA_DT_DAY_FRI', 'пет');
DEFINE('_KUNENA_DT_DAY_SAT', 'суб');
DEFINE('_KUNENA_DT_LMON_JAN', 'јануар');
DEFINE('_KUNENA_DT_LMON_FEB', 'фебруар');
DEFINE('_KUNENA_DT_LMON_MAR', 'март');
DEFINE('_KUNENA_DT_LMON_APR', 'април');
DEFINE('_KUNENA_DT_LMON_MAY', 'мај');
DEFINE('_KUNENA_DT_LMON_JUN', 'јун');
DEFINE('_KUNENA_DT_LMON_JUL', 'јул');
DEFINE('_KUNENA_DT_LMON_AUG', 'август');
DEFINE('_KUNENA_DT_LMON_SEP', 'септембар');
DEFINE('_KUNENA_DT_LMON_OCT', 'октобар');
DEFINE('_KUNENA_DT_LMON_NOV', 'новембар');
DEFINE('_KUNENA_DT_LMON_DEV', 'децембар');
DEFINE('_KUNENA_DT_MON_JAN', 'јан');
DEFINE('_KUNENA_DT_MON_FEB', 'феб');
DEFINE('_KUNENA_DT_MON_MAR', 'мар');
DEFINE('_KUNENA_DT_MON_APR', 'апр');
DEFINE('_KUNENA_DT_MON_MAY', 'мај');
DEFINE('_KUNENA_DT_MON_JUN', 'јун');
DEFINE('_KUNENA_DT_MON_JUL', 'јул');
DEFINE('_KUNENA_DT_MON_AUG', 'авг');
DEFINE('_KUNENA_DT_MON_SEP', 'сеп');
DEFINE('_KUNENA_DT_MON_OCT', 'окт');
DEFINE('_KUNENA_DT_MON_NOV', 'нов');
DEFINE('_KUNENA_DT_MON_DEV', 'дец');
DEFINE('_KUNENA_CHILD_BOARD', 'Подфорум');
DEFINE('_WHO_ONLINE_GUEST', 'Гост');
DEFINE('_WHO_ONLINE_MEMBER', 'Члан');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'нема');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Процесор слика:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Кликните овде за наставак...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Примени!');
DEFINE('_KUNENA_NO_ACCESS', 'Немате приступ овом форуму!');
DEFINE('_KUNENA_TIME_SINCE', 'пре %time%');
DEFINE('_KUNENA_DATE_YEARS', 'година');
DEFINE('_KUNENA_DATE_MONTHS', 'месеци');
DEFINE('_KUNENA_DATE_WEEKS','недеља');
DEFINE('_KUNENA_DATE_DAYS', 'дана');
DEFINE('_KUNENA_DATE_HOURS', 'часова');
DEFINE('_KUNENA_DATE_MINUTES', 'минута');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Да ли сигурно желите да уклоните пробне податке? Ова радња је неповратна.');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Заглавље форума:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', 'Приказ форума');
DEFINE('_KUNENA_CLASS_SFX', 'Додатак CSS класе форума');
DEFINE('_KUNENA_CLASS_SFXDESC', 'CSS додатак примењен на index, showcat, view и допушта различите дизајне за форуме.');
DEFINE('_COM_A_USER_EDIT_TIME', 'Време корисничке измене');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Поставите на 0 за неограничено време, иначе дозвољен
прозор у секундама за измене од објављивања или последње промене.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Време одлагања корисничке измене');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Подразумевано 600 [секунди], допушта
чување измене до 600 секунди након што веза за уређивање нестане');
DEFINE('_KUNENA_HELPPAGE','Укључи страницу помоћи');
DEFINE('_KUNENA_HELPPAGE_DESC','Ако поставите на „Да“ у менију заглавља ће бити приказана веза до странице за помоћ.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Прикажи помоћ у Kunena-и');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Ако поставите на „Да“ текстуални садржај помоћи ће бити укључен у Kunena-у и веза ка спољашњој страници за помоћ неће радити. <b>Напомена:</b> Треба да додате „ИД садржаја помоћи“.');
DEFINE('_KUNENA_HELPPAGE_CID','ИД садржаја помоћи');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Ставку „Прикажи помоћ у Kunena-и“ треба да поставите на <b>„ДА“</b>.');
DEFINE('_KUNENA_HELPPAGE_LINK','Веза ка спољашњој страници помоћи');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Ако приказујете везу ка спољашњој страници помоћи, ставку „Прикажи помоћ у Kunena-и“ поставите на <b>„НЕ“</b>.');
DEFINE('_KUNENA_RULESPAGE','Укључи страницу правила');
DEFINE('_KUNENA_RULESPAGE_DESC','Ако поставите на „Да“ у менију заглавља ће бити приказана веза до странице са правилима.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Прикажи правила у Kunena-и');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Ако поставите на „Да“ текстуални садржај правила ће бити укључен у Kunena-у и веза ка спољашњој страници са правилима неће радити. <b>Напомена:</b> Треба да додате „ИД садржаја правила“.');
DEFINE('_KUNENA_RULESPAGE_CID','ИД садржаја правила');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Ставку „Прикажи правила у Kunena-и“ треба да поставите на <b>„ДА“</b>.');
DEFINE('_KUNENA_RULESPAGE_LINK','Веза ка спољашњој страници правила');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Ако приказујете везу ка спољашњој страници правила, ставку „Прикажи правила у Kunena-и“ поставите на <b>„НЕ“</b>.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD библиотека није пронађена');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 библиотека није пронађена');
DEFINE('_KUNENA_GD_INSTALLED','GD је доступна у верзији ');
DEFINE('_KUNENA_GD_NO_VERSION','Не могу да откријем GD верзију');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD није инсталирана, више података можете наћи ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Висина мале слике :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Ширина мале слике :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Висина средње слике :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Ширина средње слике :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Висина велике слике :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Ширина велике слике :');
DEFINE('_KUNENA_AVATAR_QUALITY','Квалитет аватара');
DEFINE('_KUNENA_WELCOME','Добро дошли на Kunena!');
DEFINE('_KUNENA_WELCOME_DESC','Хвала што сте изабрали Kunena-у као решење за ваш форум. Овај екран пружа кратак преглед разне статистике вашег форума. Везе са леве стране овог екрана омогућују контролу угођаја форума у сваком могућем погледу. Свака страница ће садржати упутства о употреби својих алата.');
DEFINE('_KUNENA_STATISTIC','Статистика');
DEFINE('_KUNENA_VALUE','Вредност');
DEFINE('_GEN_CATEGORY','Категорија');
DEFINE('_GEN_STARTEDBY','Покренуо/ла је: ');
DEFINE('_GEN_STATS','статистика');
DEFINE('_STATS_TITLE',' форум - статистика');
DEFINE('_STATS_GEN_STATS','Општа статистика');
DEFINE('_STATS_TOTAL_MEMBERS','Чланови:');
DEFINE('_STATS_TOTAL_REPLIES','Одговори:');
DEFINE('_STATS_TOTAL_TOPICS','Теме:');
DEFINE('_STATS_TODAY_TOPICS','Данашње теме:');
DEFINE('_STATS_TODAY_REPLIES','Данашњи одговори:');
DEFINE('_STATS_TOTAL_CATEGORIES','Категорије:');
DEFINE('_STATS_TOTAL_SECTIONS','Одељци:');
DEFINE('_STATS_LATEST_MEMBER','Најновији члан:');
DEFINE('_STATS_YESTERDAY_TOPICS','Јучерашње теме:');
DEFINE('_STATS_YESTERDAY_REPLIES','Јучерашњи одговори:');
DEFINE('_STATS_POPULAR_PROFILE','Популарних 10 чланова (посета профилу)');
DEFINE('_STATS_TOP_POSTERS','Најактивнији чланови');
DEFINE('_STATS_POPULAR_TOPICS','Најпопуларније теме');
DEFINE('_COM_A_STATSPAGE','Укључи страницу статистике');
DEFINE('_COM_A_STATSPAGE_DESC','Ако је постављено на „Да“, биће приказана јавна веза у менију заглавља странице за статистику форума. Ова страница ће приказивати разну статистику о вашем форуму. <em>Страница за статистику ће увек бити доступна администраторима без обзира на ову поставку!</em>');
DEFINE('_COM_C_JBSTATS','Статистика форума');
DEFINE('_COM_C_JBSTATS_DESC','Статистика форума');
define('_GEN_GENERAL','Опште');
define('_PERM_NO_READ','Не поседујете довољна овлашћења да приступите овом форуму.');
DEFINE ('_KUNENA_SMILEY_SAVED','Смајли је сачуван');
DEFINE ('_KUNENA_SMILEY_DELETED','Смајли је обрисан');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Код већ постоји');
DEFINE ('_KUNENA_MISSING_PARAMETER','Недостаје параметар');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Ранг већ постоји');
DEFINE ('_KUNENA_RANK_DELETED','Ранг је обрисан');
DEFINE ('_KUNENA_RANK_SAVED','Ранг је сачуван');
DEFINE ('_KUNENA_DELETE_SELECTED','Обриши изабрано');
DEFINE ('_KUNENA_MOVE_SELECTED','Премести изабрано');
DEFINE ('_KUNENA_REPORT_LOGGED','Записано');
DEFINE ('_KUNENA_GO','Иди');
DEFINE('_KUNENA_MAILFULL','Укључи потпун садржај поруке у е-пошту послату претплатницима');
DEFINE('_KUNENA_MAILFULL_DESC','Ако је Не - претплатници ће примити само наслове нових порука');
DEFINE('_KUNENA_HIDETEXT','Пријавите се да би видели садржај!');
DEFINE('_BBCODE_HIDE','Скривен текст: [hide]неки скривен текст[/hide] - сакријте део поруке од гостију');
DEFINE('_KUNENA_FILEATTACH','Приложена датотека: ');
DEFINE('_KUNENA_FILENAME','Име датотеке: ');
DEFINE('_KUNENA_FILESIZE','Величина датотеке: ');
DEFINE('_KUNENA_MSG_CODE','Код: ');
DEFINE('_KUNENA_CAPTCHA_ON','Систем заштите од нежељених порука');
DEFINE('_KUNENA_CAPTCHA_DESC','Укључивање/искључивање CAPTCHA система против нежељених порука и ботова');
DEFINE('_KUNENA_CAPDESC','Унесите код овде');
DEFINE('_KUNENA_CAPERR','Код није исправан!');
DEFINE('_KUNENA_COM_A_REPORT', 'Пријављивање порука');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Ако желите да корисници могу пријавити било коју поруку, изаберите да.');
DEFINE('_KUNENA_REPORT_MSG', 'Порука је пријављена');
DEFINE('_KUNENA_REPORT_REASON', 'Разлог');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Ваша порука');
DEFINE('_KUNENA_REPORT_SEND', 'Пошаљи извештај');
DEFINE('_KUNENA_REPORT', 'Извести модератора');
DEFINE('_KUNENA_REPORT_RSENDER', 'Пошиљалац извештаја: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Разлог извештаја: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Порука извештаја: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Објављивач поруке: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Тема поруке: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Порука: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Веза ка поруци: ');
DEFINE('_KUNENA_REPORT_INTRO', 'послали смо вам поруку због');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Извештај је успешно послат!');
DEFINE('_KUNENA_EMOTICONS', 'Емотиконе');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Смајли');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Код');
DEFINE('_KUNENA_EMOTICONS_URL', 'УРЛ');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Уреди смајлија');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Уреди смајлије');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Трака емотикона');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Нов смајли');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Још смајлија');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Затвори прозор');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Додатне емотиконе');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Изаберите смајлија');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot подршка');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Укључи Joomla Mambot подршку');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Поставке додатка за мој профил');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Дозволи промену корисничког имена');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Дозволи промену корисничког имена на страници додатка за мој профил');
DEFINE ('_KUNENA_RECOUNTFORUMS','Преброј статистику категорија');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Сва статистика категорија је сада пребројана.');
DEFINE ('_KUNENA_EDITING_REASON','Разлог измене');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Последња измена');
DEFINE ('_KUNENA_BY','од');
DEFINE ('_KUNENA_REASON','Разлог');
DEFINE('_GEN_GOTOBOTTOM', 'Иди на дно');
DEFINE('_GEN_GOTOTOP', 'Иди на врх');
DEFINE('_STAT_USER_INFO', 'Кориснички подаци');
DEFINE('_USER_SHOWEMAIL', 'Прикажи е-пошту'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Прикажи статус везе');
DEFINE('_KUNENA_HIDDEN_USERS', 'Скривени корисници');
DEFINE('_KUNENA_SAVE', 'Сачувај');
DEFINE('_KUNENA_RESET', 'Врати на почетно');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Подразумевана галерија');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Лични подаци');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Сажетак');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Мој аватар');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Поставке форума');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Изглед и распоред');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Подаци мог профила');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Моје поруке');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Моје претплате');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Моје омиљене');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Приватне поруке');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Пријемно сандуче');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Нова порука');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Отпремно сандуче');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Смеће');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Поставке');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Контакти');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Списак блокираних');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Додатни подаци');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Име');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Корисничко име');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Е-пошта');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Врста корисника');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Датум регистрације');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Датум последње посете');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Порука');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Преглед профила');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Лични текст');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Пол');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Рођендан');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Година (ГГГГ) - Месец (ММ) - Дан (ДД)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Место');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Ово је ваш ICQ број.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Ово је ваш AOL Instant Messenger надимак.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Ово је ваш Yahoo! Instant Messenger надимак.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Ово је ваш Skype надимак.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Ово је ваш Gtalk надимак.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Веб страница');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Назив веб странице');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Пример: Kunena!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'УРЛ веб странице');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Пример: www.kunena.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Ваша MSN messenger адреса е-поште.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Потпис');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Мушко');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Женско');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Поруке су успешно обрисане');
DEFINE('_KUNENA_DATE_YEAR', 'годину');
DEFINE('_KUNENA_DATE_MONTH', 'месец');
DEFINE('_KUNENA_DATE_WEEK','недељу');
DEFINE('_KUNENA_DATE_DAY', 'дан');
DEFINE('_KUNENA_DATE_HOUR', 'час');
DEFINE('_KUNENA_DATE_MINUTE', 'минут');
DEFINE('_KUNENA_IN_FORUM', ' у форуму: ');
DEFINE('_KUNENA_FORUM_AT', ' Форум на: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Примите к знању да, иако дугмад за код форума и смајлије нису приказана, она се и даље могу користити');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Алати форума');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Списак корисника');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s има <b>%d</b> регистрованих корисника');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Унесите исправну вредност за претрагу!');
DEFINE ('_KUNENA_USRL_SEARCH','Пронађи корисника/цу');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Тражи');
DEFINE ('_KUNENA_USRL_LIST_ALL','Испиши све');
DEFINE ('_KUNENA_USRL_NAME','Име');
DEFINE ('_KUNENA_USRL_USERNAME','Корисничко име');
DEFINE ('_KUNENA_USRL_GROUP','Група');
DEFINE ('_KUNENA_USRL_POSTS','Порука');
DEFINE ('_KUNENA_USRL_KARMA','Карма');
DEFINE ('_KUNENA_USRL_HITS','Посета');
DEFINE ('_KUNENA_USRL_EMAIL','Е-пошта');
DEFINE ('_KUNENA_USRL_USERTYPE','Врста корисника');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Датум придруживања');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Последња пријава');
DEFINE ('_KUNENA_USRL_NEVER','Никад');
DEFINE ('_KUNENA_USRL_ONLINE','Статус');
DEFINE ('_KUNENA_USRL_AVATAR','Слика');
DEFINE ('_KUNENA_USRL_ASC','Растући');
DEFINE ('_KUNENA_USRL_DESC','Опадајући');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Приказ');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Додаци');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Списак корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Број редова у списку корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Број редова у списку корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Статус везе корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Прикажи статус везе корисника');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Прикажи аватар');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Прикажи право име');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Прикажи корисничко име');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Прикажи групу корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Прикажи број порука');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Прикажи карму');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Прикажи е-пошту');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Прикажи врсту корисника');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Прикажи датум придруживања');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Прикажи датум последње посете');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Прикажи посете профила');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Чаробњак за базу података');
DEFINE('_KUNENA_DBMETHOD', 'Изаберите којом методом желите да завршите инсталацију:');
DEFINE('_KUNENA_DBCLEAN', 'Чиста инсталација');
DEFINE('_KUNENA_DBUPGRADE', 'Надградња са Joomlaboard-а');
DEFINE('_KUNENA_TOPLEVEL', 'Категорија највишег нивоа');
DEFINE('_KUNENA_REGISTERED', 'Регистровано');
DEFINE('_KUNENA_PUBLICBACKEND', 'Јавно зачеље');
DEFINE('_KUNENA_SELECTANITEMTO', 'Изаберите ставку за');
DEFINE('_KUNENA_ERRORSUBS', 'Дошло је до неке грешке при брисању порука и претплата');
DEFINE('_KUNENA_WARNING', 'Упозорење...');
DEFINE('_KUNENA_CHMOD1', 'Морате извршити chmod 766 над овом датотеком како би могла да буде ажурирана.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Датотека подешавања је');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II отвореног кода');
DEFINE('_KUNENA_UDDEIM', 'uddeIM');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Изаберите шаблон');
DEFINE('_KUNENA_CONFIGSAVED', 'Подешавања су сачувана.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'КОБНА ГРЕШКА: Подешавања није било могуће сачувати.');
DEFINE('_KUNENA_TFINW', 'Датотека није уписива.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS датотека је сачувана.');
DEFINE('_KUNENA_SELECTMODTO', 'Изаберите модератора за');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Морате изабрати форум за прочишћавање!');
DEFINE('_KUNENA_DELMSGERROR', 'Неуспело брисање порука:');
DEFINE('_KUNENA_DELMSGERROR1', 'Неуспело брисање текстова порука:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Неуспело брисање претплата:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Форум се прочишћава за');
DEFINE('_KUNENA_PRUNEDAYS', 'дана');
DEFINE('_KUNENA_PRUNEDELETED', 'Обрисано:');
DEFINE('_KUNENA_PRUNETHREADS', 'тема');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Грешка при чишћењу корисника:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Корисници су прочишћени. Обрисано:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'корисничких профила'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Нису пронађени подобни профили за прочишћавање.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena табеле су надграђене на верзију');
DEFINE('_KUNENA_FORUMCATEGORY', 'Категорија форума');
DEFINE('_KUNENA_SAMPLWARN1', '-- Будите потпуно сигурни да учитавате пробне податке у потпуно празне Kunena табеле. Ако нечега има у њима, ово неће радити!');
DEFINE('_KUNENA_FORUM1', 'Форум 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Пробна порука 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Пробни форум 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Пробна порука[/color][/size][/b]\nЧеститке на вашем новом форуму!\n\n[url=http://kunena.com]- Kunena[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Пробни подаци су учитани');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Пробни подаци су уклоњени');
DEFINE('_KUNENA_CBADDED', 'Community Builder профил је додат');
DEFINE('_KUNENA_IMGDELETED', 'Слика је обрисана');
DEFINE('_KUNENA_FILEDELETED', 'Датотека је обрисана');
DEFINE('_KUNENA_NOPARENT', 'Нема родитеља');
DEFINE('_KUNENA_DIRCOPERR', 'Грешка: Датотеку');
DEFINE('_KUNENA_DIRCOPERR1', 'није било могуће копирати!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena форум</strong> компонента <em>за Joomla! </em> <br />&copy; 2008 - 2009 www.kunena.com<br />Сва права задржана.');
DEFINE('_KUNENA_INSTALL2', 'Пребацивање/инсталирање :</code></strong><br /><br /><font color="red"><b>успешно');
// added by aliyar
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Подешавања профила');
DEFINE('_KUNENA_FORUMPRF', 'Профил');
DEFINE('_KUNENA_FORUMPRRDESC', 'Ако имате инсталиране Clexus PM или Community Builder компоненте, можете подесити да Kunena користи страницу корисничког профила.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Профил');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Преглед профила</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Све поруке форума');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Теме');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Започео/ла');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Категорије');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Датум');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Посета');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Нема порука у форуму');
DEFINE('_KUNENA_TOTALFAVORITE', 'Омиљени:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Број колона подфорума  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Број колона при формирању подфорума под главном категоријом ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Подразумевано штиклирана претплата на поруку?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Поставите на „Да“ ако желите да кућица за претплату поруке буде увек штиклирана');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Категорија / форум мора имати назив');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'Прикажи статистику');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Ако желите да прикажете статистику, изаберите Да');
DEFINE('_KUNENA_SHOWWHOIS', 'Прикажи ко је на вези');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Ако желите да прикажете ко је на вези, изаберите Да');
DEFINE('_KUNENA_STATSGENERAL', 'Прикажи општу статистику');
DEFINE('_KUNENA_STATSGENERALDESC', 'Ако желите да прикажете општу статистику, изаберите Да');
DEFINE('_KUNENA_USERSTATS', 'Прикажи статистику популарних корисника');
DEFINE('_KUNENA_USERSTATSDESC', 'Ако желите да прикажете статистику популарности, изаберите Да');
DEFINE('_KUNENA_USERNUM', 'Број популарних корисника');
DEFINE('_KUNENA_USERPOPULAR', 'Прикажи статистику популарних тема');
DEFINE('_KUNENA_USERPOPULARDESC', 'Ако желите да прикажете популарне теме, изаберите Да');
DEFINE('_KUNENA_NUMPOP', 'Број популарних тема');
DEFINE('_KUNENA_INFORMATION',
    'Kunena тим са поносом најављује Kunena 1.0.5 издање. Ово је моћна и модерна компонента форума за веома заслужени систем управљања садржајем, Joomla!. Почетно је заснован на тешком раду Joomlaboard и Fireboad тимова и већина наших похвала иде њима. Неке од главних могућности Kunena-е су наведене испод (поред постојећих могућности JB-а):<br /><br /><ul><li>Систем форума много више наклоњен дизајнерима. Близак је SMF систему шаблона са једноставнијом структуром. У само неколико корака можете изменити потпун изглед форума. Захвалнице иду сјајним дизајнерима у нашем тиму.</li><li>Неограничени систем подкатегорија са бољим администрационим зачељем.</li><li>Бржи систем и лакше искуство кодирања за додатке са стране.</li><li>Исти<br /></li><li>Profilebox на врху форума</li><li>Подршка за познате PM системе, попут ClexuxPM и uddeIM</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />Ово је заједнички рад неколико програмера и дизајнера који су љубазно учествовали и учинили ово издање могућим. Овде се захваљујемо свима и желимо да уживате у овом издању!<br /><br />Kunena! тим<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Упутства');
DEFINE('_KUNENA_FINFO', 'Информације Kunena форума');
DEFINE('_KUNENA_CSSEDITOR', 'Уређивач Kunena CSS шаблона');
DEFINE('_KUNENA_PATH', 'Путања:');
DEFINE('_KUNENA_CSSERROR', 'Напомена: датотека CSS шаблона мора бити уписива при чувању измена.');
// User Management
DEFINE('_KUNENA_FUM', 'Управник Kunena корисничких профила');
DEFINE('_KUNENA_SORTID', 'поређај по корисничком ид-у');
DEFINE('_KUNENA_SORTMOD', 'поређај по модератору');
DEFINE('_KUNENA_SORTNAME', 'поређај по имену');
DEFINE('_KUNENA_VIEW', 'Преглед');
DEFINE('_KUNENA_NOUSERSFOUND', 'Нису пронађени кориснички профили.');
DEFINE('_KUNENA_ADDMOD', 'Додај модератора у');
DEFINE('_KUNENA_NOMODSAV', 'Нису пронађени могући модератори. Прочитајте „белешку“ испод.');
DEFINE('_KUNENA_NOTEUS',
    'БЕЛЕШКА: Овде су приказани само корисници за које је постављена модераторска ознака у Kunena профилу. Дајте кориснику ознаку модератора да би имали могућност додавања модератора, посетите <a href="index2.php?option=com_kunena&task=profiles">Администрацију корисника</a> и потражите корисника кога желите учинити модератором. Затим изаберите његов или њен профил и ажурирајте га. Модераторску ознаку може постављати само администратор сајта. ');
DEFINE('_KUNENA_PROFFOR', 'Профил за');
DEFINE('_KUNENA_GENPROF', 'Опште опције профила');
//DEFINE('_KUNENA_PREFVIEW', 'Жељена врста прегледа:');
DEFINE('_KUNENA_PREFOR', 'Жељени поредак порука:');
DEFINE('_KUNENA_ISMOD', 'Је модератор:');
DEFINE('_KUNENA_ISADM', '<strong>Да</strong> (није променљиво, овај корисник је (супер)администратор сајта)');
DEFINE('_KUNENA_COLOR', 'Боја');
DEFINE('_KUNENA_UAVATAR', 'Аватар корисника:');
DEFINE('_KUNENA_NS', 'Нема изабраних');
DEFINE('_KUNENA_DELSIG', ' штиклирајте ову кућицу за брисање овог потписа');
DEFINE('_KUNENA_DELAV', ' штиклирајте ову кућицу за брисање овог аватара');
DEFINE('_KUNENA_SUBFOR', 'Претплате за');
DEFINE('_KUNENA_NOSUBS', 'Нису пронађене претплате овог корисника');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Основе');
DEFINE('_KUNENA_BASICSFORUM', 'Основни подаци форума');
DEFINE('_KUNENA_PARENT', 'Родитељ:');
DEFINE('_KUNENA_PARENTDESC',
    'Примите к знању: За прављење категорије, изаберите „Категорију највишег нивоа“ као родитеља. Категорија врши улогу спремишта за форуме.<br />Форум се може направити <strong>само</strong> унутар категорије бирањем претходно направљене категорије као родитеља за тај форум.<br /> Поруке <strong>НЕ</strong> могу бити постављене у категорије; само у форуме.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Назив и опис форума');
DEFINE('_KUNENA_NAMEADD', 'Име:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Опис:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Напредна подешавања форума');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Сигурност форума и приступ');
DEFINE('_KUNENA_LOCKEDDESC', 'Поставите на „Да“ ако желите да закључате овај форум. Нико, сем модератора и администратора не може правити нове теме и одговоре у закључаном форуму (или премештати поруке у њега).');
DEFINE('_KUNENA_LOCKED1', 'Закључано:');
DEFINE('_KUNENA_PUBACC', 'Ниво јавног приступа:');
DEFINE('_KUNENA_PUBACCDESC',
    'За прављење форума који нису јавни, овде можете навести најнижи кориснички ниво који може видети/приступити форуму. Најнижи кориснички ниво је подразумевано постављен на „Сви“.<br /><b>Примите к знању</b>: ако једној или више група ограничите приступ целој категорији, сви форуми које она садржи ће бити сакривени свакоме ко нема исправне привилегије за категорију <b>чак</b> и ако један или више ових форума имају постављен нижи ниво приступа! Ово важи и за модераторе, мораћете да додате модератора списку модератора категорије ако он или она не поседује исправан ниво групе за преглед категорије.<br /> Ово је невезано за чињеницу да категорије не могу да буду модериране. Модераторе је и даље могуће додати на списак модератора.');
DEFINE('_KUNENA_CGROUPS', 'Укључи подгрупе:');
DEFINE('_KUNENA_CGROUPSDESC', 'Треба ли такође дозволити приступ подгрупама? Ако је постављено на „Не“, приступ овом форуму је ограничен <b>само</b> на изабрану групу');
DEFINE('_KUNENA_ADMINLEVEL', 'Ниво административног приступа:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Ако направите форум са ограничењима јавног приступа, овде можете навести додатне нивое административног приступа.<br /> Ако ограничите приступ форуму само посебној корисничкој групи јавног сучеља, а овде не наведете групу јавног зачеља, администратори неће моћи да приступе/виде форум.');
DEFINE('_KUNENA_ADVANCED', 'Напредно');
DEFINE('_KUNENA_CGROUPS1', 'Укључи подгрупе:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Треба ли такође дозволити приступ подгрупама? Ако је постављено на „Не“, приступ овом форуму је ограничен <b>само</b> на изабрану групу');
DEFINE('_KUNENA_REV', 'Прегледај поруке:');
DEFINE('_KUNENA_REVDESC',
    'Поставите на „Да“ ако желите да поруке прегледају модератори пре њиховог објављивања у овом форуму. Ово је корисно само у модерираним форумима!<br />Ако поставите ово без одређених модератора, само је администратор сајта одговоран за одобравање/брисање поднетих порука јер ће оне бити „задржане“!');
DEFINE('_KUNENA_MOD_NEW', 'Модерација');
DEFINE('_KUNENA_MODNEWDESC', 'Модерација и модератори форума');
DEFINE('_KUNENA_MOD', 'Модерира:');
DEFINE('_KUNENA_MODDESC',
    'Поставите на „Да“ ако желите да имате могућност одређивања модератора за овај форум.<br /><strong>Напомена:</strong> Ово не значи да нове поруке морају бити прегледане пре њиховог објављивања на форуму!<br /> За то морате поставити опцију „Прегледање“ на напредном језичку.<br /><br /> <strong>Примите к знању:</strong> Након подешавања Модерације на „Да“, морате прво сачувати подешавања форума пре него што можете додавати Модераторе користећи дугме за нове.');
DEFINE('_KUNENA_MODHEADER', 'Подешавања модерације за овај форум');
DEFINE('_KUNENA_MODSASSIGNED', 'Модератори одређени за овај форум:');
DEFINE('_KUNENA_NOMODS', 'Нису одређени модератори за овај форум');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Уреди');
DEFINE('_KUNENA_ADD', 'Додај');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Помери изнад');
DEFINE('_KUNENA_MOVEDOWN', 'Помери испод');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Сви регистровани');
DEFINE('_KUNENA_EVERYBODY', 'Сви');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Преуреди');
DEFINE('_KUNENA_CHECKEDOUT', 'Провери');
DEFINE('_KUNENA_ADMINACCESS', 'Административни приступ');
DEFINE('_KUNENA_PUBLICACCESS', 'Јавни приступ');
DEFINE('_KUNENA_PUBLISHED', 'Објављено');
DEFINE('_KUNENA_REVIEW', 'Преглед');
DEFINE('_KUNENA_MODERATED', 'Модерирано');
DEFINE('_KUNENA_LOCKED', 'Закључано');
DEFINE('_KUNENA_CATFOR', 'Категорија / Форум');
DEFINE('_KUNENA_ADMIN', 'Kunena администрација');
DEFINE('_KUNENA_CP', 'Kunena контролна табла');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Интеграција аватара');
DEFINE('_COM_A_RANKS_SETTINGS', 'Рангови');
DEFINE('_COM_A_RANKING_SETTINGS', 'Подешавања рангирања');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Подешавања аватара');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Подешавања сигурности');
DEFINE('_COM_A_BASIC_SETTINGS', 'Основне поставке');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Укључи Омиљене');
DEFINE('_COM_A_FAVORITES_DESC', 'Поставите на „Да“ ако желите да омогућите регистрованим корисницима означавање теме као омиљене ');
DEFINE('_USER_UNFAVORITE_ALL', 'Скидање ознаке <b><u>омиљена</u></b> са свих тема (укључујући и невидљиве ради отклањања грешака)');
DEFINE('_VIEW_FAVORITETXT', 'Означи тему као омиљену ');
DEFINE('_USER_UNFAVORITE_YES', 'Скинута је ознака омиљене са ове теме.');
DEFINE('_POST_FAVORITED_TOPIC', 'Ова тема је додата у ваше омиљене.');
DEFINE('_VIEW_UNFAVORITETXT', 'Скини омиљену');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Откажи претплату');
DEFINE('_USER_NOFAVORITES', 'Нема омиљених');
DEFINE('_POST_SUCCESS_FAVORITE', 'Ваш захтев за додавање у омиљене је обрађен.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Резултати претраживања');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Поруке по страници резултата претраживања');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Користити Joomla стил?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Ако желите користити Joomla стилове, поставите на ДА. (class: као sectionheader, sectionentry1...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Прикажи слику подкатегорије');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Ако желите приказати малу икону уз подкатегорије на листи форума, поставите ово на ДА. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Прикажи обавештења');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Поставите на „Да“ ако желите да прикажете оквир за обавештења на главној страници форума.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Приказати аватар на списку категорија?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Поставите на „Да“ ако желите да прикажете корисничке аватаре на списку категорија форума.');
DEFINE('_KUNENA_RECENT_POSTS', 'Подешавања најновијих порука');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Прикажи најновије поруке');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Поставите на „Да“ ако желите да прикажете додатак за најновије поруке на форуму');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Број најновијих порука');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Број најновијих порука');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Број по језичку ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Број порука по једном језичку');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Прикажи категорију');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'Одређена категорија из које ће се приказивати најновије поруке. На пример:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Прикажи јединствен наслов');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Прикажи јединствен наслов');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Прикажи наслов одговора');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', ' Прикажи наслов одговора (Одг:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Дужина наслова');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Дужина наслова');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Прикажи датум');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Прикажи датум');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'Прикажи број посета');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'Прикажи број посета');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Прикажи аутора');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=корисничко име, 2=право име, 0=ништа');
DEFINE('_KUNENA_STATS', 'Подешавања статистичких додатака ');
DEFINE('_KUNENA_CATIMAGEPATH', 'Путања до слике категорије ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Путања до слике за категорију. Ако унесете „category_images/“ путања ће бити „vasa_korenska_html_fascikla/images/fbfiles/category_images/“');
DEFINE('_KUNENA_ANN_MODID', 'ИД-ови модератора обавештења ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Додајте ид-ове корисника који могу модерисати обавештења, нпр. 62,63,73. Модератори обавештења их могу додавати, уређивати и брисати.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Категорије форума ');
DEFINE('_KUNENA_CHILD_BOARDS', 'Подфоруми ');
DEFINE('_KUNENA_QUICKMSG', 'Брзи одговор ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Нити у форуму ');
DEFINE('_KUNENA_FORUM', 'Форум ');
DEFINE('_KUNENA_SPOTS', 'Најзанимљивије');
DEFINE('_KUNENA_CANCEL', 'Одустани');
DEFINE('_KUNENA_TOPIC', 'ТЕМА: ');
DEFINE('_KUNENA_POWEREDBY', 'У погону је ');
// Time Format
DEFINE('_TIME_TODAY', '<b>данас</b> ');
DEFINE('_TIME_YESTERDAY', '<b>јуче</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Последње поруке');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Ко је на вези');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Главна форума');
DEFINE('_KUNENA_GUEST', 'Гост');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'прегледа');
DEFINE('_KUNENA_ATTACH', 'Прилог');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Омиљена');
DEFINE('_USER_FAVORITES', 'Моје омиљене теме');
DEFINE('_THREAD_UNFAVORITE', 'Уклони из омиљених');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Добро дошли');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Прикажи последње поруке');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Постави мој аватар');
DEFINE('_PROFILEBOX_MYPROFILE', 'Мој профил');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Прикажи моје поруке');
DEFINE('_PROFILEBOX_GUEST', 'гост');
DEFINE('_PROFILEBOX_LOGIN', 'Пријавите се');
DEFINE('_PROFILEBOX_REGISTER', 'региструјте');
DEFINE('_PROFILEBOX_LOGOUT', 'Одјава');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Заборавили сте лозинку?');
DEFINE('_PROFILEBOX_PLEASE', '');
DEFINE('_PROFILEBOX_OR', 'или');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Најновије поруке');
DEFINE('_RECENT_TOPICS', 'Тема');
DEFINE('_RECENT_AUTHOR', 'Аутор');
DEFINE('_RECENT_CATEGORIES', 'Категорије');
DEFINE('_RECENT_DATE', 'Датум');
DEFINE('_RECENT_HITS', 'Посета');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Обавештења');
DEFINE('_ANN_ID', 'ИД');
DEFINE('_ANN_DATE', 'Датум');
DEFINE('_ANN_TITLE', 'Наслов');
DEFINE('_ANN_SORTTEXT', 'Кратки текст');
DEFINE('_ANN_LONGTEXT', 'Дуги текст');
DEFINE('_ANN_ORDER', 'Редослед');
DEFINE('_ANN_PUBLISH', 'Објави');
DEFINE('_ANN_PUBLISHED', 'Објављено');
DEFINE('_ANN_UNPUBLISHED', 'Необјављено');
DEFINE('_ANN_EDIT', 'Уреди');
DEFINE('_ANN_DELETE', 'Обриши');
DEFINE('_ANN_SUCCESS', 'Успешно');
DEFINE('_ANN_SAVE', 'Сачувај');
DEFINE('_ANN_YES', 'Да');
DEFINE('_ANN_NO', 'Не');
DEFINE('_ANN_ADD', 'Додај ново');
DEFINE('_ANN_SUCCESS_EDIT', 'Успешно уређено');
DEFINE('_ANN_SUCCESS_ADD', 'успешно додато');
DEFINE('_ANN_DELETED', 'Успешно обрисано');
DEFINE('_ANN_ERROR', 'ГРЕШКА');
DEFINE('_ANN_READMORE', 'Опширније...');
DEFINE('_ANN_CPANEL', 'Контролна табла за обавештења');
DEFINE('_ANN_SHOWDATE', 'Прикажи датум');
// Stats
DEFINE('_STAT_FORUMSTATS', 'статистика форума');
DEFINE('_STAT_GENERAL_STATS', 'Општа статистика');
DEFINE('_STAT_TOTAL_USERS', 'Укупно корисника/ца');
DEFINE('_STAT_LATEST_MEMBERS', 'Најновији/а корисник/ца');
DEFINE('_STAT_PROFILE_INFO', 'Погледај информације профила');
DEFINE('_STAT_TOTAL_MESSAGES', 'Укупно порука');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Укупно тема');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Укупно категорија');
DEFINE('_STAT_TOTAL_SECTIONS', 'Укупно одељака');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Отворено данас');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Отворено јуче');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Укупно одговора данас');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Укупно одговора јуче');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Погледај најновије поруке');
DEFINE('_STAT_MORE_ABOUT_STATS', 'Опширније о статистици');
DEFINE('_STAT_USERLIST', 'Списак корисника');
DEFINE('_STAT_TEAMLIST', 'Тим форума');
DEFINE('_STATS_FORUM_STATS', 'статистика форума');
DEFINE('_STAT_POPULAR', 'Популарних');
DEFINE('_STAT_POPULAR_USER_TMSG', 'корисника (укупно порука) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'тема ');
DEFINE('_STAT_POPULAR_USER_GSG', 'корисника (укупно посета профилу) ');
//Team List
DEFINE('_MODLIST_ONLINE', 'Корисник је тренутно на вези');
DEFINE('_MODLIST_OFFLINE', 'Корисник није на вези');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Ко је на вези');
DEFINE('_WHO_ONLINE_NOW', 'На вези је');
DEFINE('_WHO_ONLINE_MEMBERS', 'чланова');
DEFINE('_WHO_AND', 'и');
DEFINE('_WHO_ONLINE_GUESTS', 'гостију');
DEFINE('_WHO_ONLINE_USER', 'Корисник');
DEFINE('_WHO_ONLINE_TIME', 'Време');
DEFINE('_WHO_ONLINE_FUNC', 'Акција');
// Userlist
DEFINE('_USRL_USERLIST', 'Списак корисника');
DEFINE('_USRL_REGISTERED_USERS', '%s има <b>%d</b> регистрованих корисника');
DEFINE('_USRL_SEARCH_ALERT', 'Унесите вредност за претраживање!');
DEFINE('_USRL_SEARCH', 'Нађи корисника');
DEFINE('_USRL_SEARCH_BUTTON', 'Тражи');
DEFINE('_USRL_LIST_ALL', 'Испиши све');
DEFINE('_USRL_NAME', 'Име');
DEFINE('_USRL_USERNAME', 'Корисничко име');
DEFINE('_USRL_EMAIL', 'Е-пошта');
DEFINE('_USRL_USERTYPE', 'Тип корисника');
DEFINE('_USRL_JOIN_DATE', 'Датум придруживања');
DEFINE('_USRL_LAST_LOGIN', 'Последња пријава');
DEFINE('_USRL_NEVER', 'Никада');
DEFINE('_USRL_BLOCK', 'Статус');
DEFINE('_USRL_MYPMS2', 'myPMS');
DEFINE('_USRL_ASC', 'Растући');
DEFINE('_USRL_DESC', 'Опадајући');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Детаљи');
DEFINE('_USRL_COMPROFILER', 'Профил');
DEFINE('_USRL_THUMBNAIL', 'Слика');
DEFINE('_USRL_READON', 'прикажи');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Пошаљи личну поруку');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Резултати претраживања за');
DEFINE('_USRL_STATUS', 'Статус');
DEFINE('_USRL_LISTSETTINGS', 'Подешавања списка корисника');
DEFINE('_USRL_ERROR', 'Грешка');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Компонента за лично дописивање');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Претраживано је за: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Да ли сте сигурни да желите да обришете ову поруку? \n\n НАПОМЕНА: Обрисану поруку НЕ можете повратити!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Порука(е) је(су) избрисана(е)');
DEFINE('_COM_A_RANKING', 'Рангирање');
DEFINE('_COM_A_BOT_REFERENCE', 'Приказивање графикона Бот референце');
DEFINE('_COM_A_MOSBOT', 'Укључивање Бота за дискусију');
DEFINE('_PREVIEW', 'Преглед');
DEFINE('_COM_A_MOSBOT_TITLE', 'Бот за дискусију (Discussbot)');
DEFINE('_COM_A_MOSBOT_DESC', 'Бот за дискусију омогућава корисницима да дискутују о садржајима на форуму. Наслов садржаја се употребљава као наслов теме.'
           . '<br />Уколико тема не постоји, прави се нова. Уколико тема већ постоји приказује се, како би одговор био могућ.' . '<br /><strong>Потребно је засебно преузети и инсталирати овај Бот.</strong>'
           . '<br />погледајте <a href="http://www.kunena.com">Kunena сајт</a> за више информација.' . '<br />Након инсталације биће потребно додати следеће линије бота у садржај:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br /><em>catid</em> је категорија у којој се налази садржај који се дискутује. Како би пронашли catid, погледајте форум ' . ' и проверите ИД категорије из УРЛ адресе у веб читачу.'
           . '<br />Пример: уколико желите чланак коментарисан у форуму под catid 26, Бот би требало да изгледа овако: {mos_fb_discuss:26}'
           . '<br />Ово може изгледати компликовано, али омогућава коментарисање сваког дела садржаја на форуму из жељеног дела форума.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Претрага');
DEFINE('_FORUM_SEARCHRESULTS', 'приказивање %s од %s резултата.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'ЧПП');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Правила');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Уредите датотеку joomlaroot/administrator/components/com_kunena/language/kunena.serbian.php како бисте поставили ваша правила</li><li>Правило 2</li><li>Правило 3</li><li>Правило 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Код форума');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Порука(е) је(су) одобрена(е)');
DEFINE('_MODERATION_DELETE_ERROR', 'ГРЕШКА: Порука(е) не може(могу) бити обрисана(е)');
DEFINE('_MODERATION_APPROVE_ERROR', 'ГРЕШКА: Порука(е) не може(могу) бити одобрена(е)');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Нема форума у овој категорији!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Неуспело прављење теме утваре у старом форуму!');
DEFINE('_POST_MOVE_GHOST', 'Остави поруку утвару у старом форуму');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Брзи прелазак');
DEFINE('_COM_A_FORUM_JUMP', 'Омогући брзи прелазак');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'Подесите на „Да“ и изборник који ће се појавити ће омогућити брзи прелазак на други форум или тему.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Правила');
DEFINE('_COM_A_RULESPAGE', 'Омогући страницу са правилима');
DEFINE('_COM_A_RULESPAGE_DESC',
    'Ако је подешено на „Да“, биће приказана веза у менију заглавља ка страници са правилима. Ова страница се може искористити за ствари као што су правила форума, итд. Можете изменити садржај ове датотеке по жељи отварањем rules.php у /joomla_root/components/com_kunena. <em>Осигурајте да увек имате резервну копију ове датотеке јер ће бити прегажена током надградње!</em>');
DEFINE('_MOVED_TOPIC', 'ПРЕМЕШТЕНО:');
DEFINE('_COM_A_PDF', 'Омогући прављење PDF датотека');
DEFINE('_COM_A_PDF_DESC',
    'Подесите на „Да“ уколико желите да омогућите корисницима преузимање једноставне PDF датотеке са садржајем теме.<br />То је <u>једноставан</u> PDF документ; нема ознака, китњастог изгледа и слично, али садржи сав текст који се појављује у теми.');
DEFINE('_GEN_PDFA', 'Кликните на ово дугме да бисте направили PDF датотеку из ове теме (отвара се у новом прозору).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Кликните овде да бисте видели профил овог корисника');
DEFINE('_VIEW_ADDBUDDY', 'Кликните овде да бисте додали корисника на ваш списак ортака');
DEFINE('_POST_SUCCESS_POSTED', 'Ваша порука је успешно објављена');
DEFINE('_POST_SUCCESS_VIEW', '[ Повратак на тему ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Повратак на форум ]');
DEFINE('_RANK_ADMINISTRATOR', 'Администратор');
DEFINE('_RANK_MODERATOR', 'Модератор');
DEFINE('_SHOW_LASTVISIT', 'од последње посете');
DEFINE('_COM_A_BADWORDS_TITLE', 'Филтрирање ружних речи');
DEFINE('_COM_A_BADWORDS', 'Користи филтер за ружне речи');
DEFINE('_COM_A_BADWORDS_DESC', 'Подесите на „Да“ уколико желите да филтрирате поруке које садрже речи које сте дефинисали у подешавању компоненте за ружне речи. Да бисте користили ову опцију морате имати инсталирану компоненту за ружне речи!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Ова порука је цензурисана јер садржи неку од речи које је навео администратор *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Прављење профила форума за Community Builder');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Кликните на ову везу да бисте направили неопходна поља форума у Community Builder корисничком профилу. Након што их направите, можете их померати када год желите користећи Community Builder администрацију, само немојте мењати њихова имена и опције. Уколико их обришете из Community Builder администрационог панела, можете их поново направити кликом на ову везу, али у противном не треба кликтати на линк више пута!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> Кликните овде <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder кориснички профили');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Подешавање на „Да“ ће активирати интеграцију са Community Builder компонентом (www.joomlapolis.com). Свим опцијама Kunena корисничких профила ће руковати Community Builder и све везе ка профилима из форума ће водити ка Community Builder корисничким профилима. Овакво подешавање ће премостити подешавања Clexus PM профила уколико су обе постављене на „Да“. Такође се побрините да су све неопходне измене примењене у Community Builder бази података користећи опцију испод.');
DEFINE('_COM_A_AVATAR_SRC', 'Користи слику аватара из');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Уколико имате JomSocial, Clexus PM или Community Builder компоненте инсталиране, можете подесити да Kunena користи слике аватара из JomSocial, Clexus PM или Community Builder корисничких профила. НАПОМЕНА: За Community Builder је потребно имати укључену опцију за умањене слике јер форум користи умањене слике корисника, а не оригинале.');
DEFINE('_COM_A_KARMA', 'Прикажи карма индикатор');
DEFINE('_COM_A_KARMA_DESC', 'Поставите на „Да“ за приказивање карме и релевантних дугмића (похвала / критика) ако је укључена корисничка статистика.');
DEFINE('_COM_A_DISEMOTICONS', 'Искључи смајлије');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'Поставите на „Да“ ако желите да потпуно искључите графичке смајлије.');
DEFINE('_COM_C_FBCONFIG', 'Kunena подешавање');
DEFINE('_COM_C_FBCONFIGDESC', 'Подешавање свих Kunena могућности');
DEFINE('_COM_C_FORUM', 'Администрација форума');
DEFINE('_COM_C_FORUMDESC', 'Додавање категорија/форума и њихово подешавање');
DEFINE('_COM_C_USER', 'Администрација корисника');
DEFINE('_COM_C_USERDESC', 'Основна администрација корисника и корисничких профила');
DEFINE('_COM_C_FILES', 'Преглед постављених датотека');
DEFINE('_COM_C_FILESDESC', 'Преглед и администрација постављених датотека');
DEFINE('_COM_C_IMAGES', 'Преглед постављених слика');
DEFINE('_COM_C_IMAGESDESC', 'Преглед и администрација постављених слика');
DEFINE('_COM_C_CSS', 'Уређивање CSS датотеке');
DEFINE('_COM_C_CSSDESC', 'Дотерајте Kunena изглед и угођај');
DEFINE('_COM_C_SUPPORT', 'Веб страница за подршку');
DEFINE('_COM_C_SUPPORTDESC', 'Повежите се на Kunena веб страницу (нови прозор)');
DEFINE('_COM_C_PRUNETAB', 'Чишћење форума');
DEFINE('_COM_C_PRUNETABDESC', 'Уклањање старих тема (може се подесити)');
DEFINE('_COM_C_PRUNEUSERS', 'Чишћење корисника'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Синхронизација Kunena корисничке табеле са Joomla! корисничком табелом'); // <=FB 1.0.3
DEFINE('_COM_C_LOADSAMPLE', 'Учитавање пробних података');
DEFINE('_COM_C_LOADSAMPLEDESC', 'За лагани почетак: учитајте пробне податке у празну Kunena базу');
DEFINE('_COM_C_REMOVESAMPLE', 'Уклањање пробних података');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Уклоните пробне податке из базе');
DEFINE('_COM_C_LOADMODPOS', 'Учитавање позиција модула');
DEFINE('_COM_C_LOADMODPOSDESC', 'Учитавање позиција модула за Kunena шаблон');
DEFINE('_COM_C_UPGRADEDESC', 'Дођите до последње верзије базе података након надградње');
DEFINE('_COM_C_BACK', 'Назад на Kunena контролну таблу');
DEFINE('_SHOW_LAST_SINCE', 'Активне теме од последње посете:');
DEFINE('_POST_SUCCESS_REQUEST2', 'Ваш захтев је обрађен');
DEFINE('_POST_NO_PUBACCESS3', 'Кликните овде за регистрацију.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Порука је успешно избрисана.');
DEFINE('_POST_SUCCESS_EDIT', 'Порука је успешно уређена.');
DEFINE('_POST_SUCCESS_MOVE', 'Тема је успешно премештена.');
DEFINE('_POST_SUCCESS_POST', 'Ваша порука је успешно објављена.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Ваша претплата је обрађена.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Карма');
DEFINE('_KARMA_SMITE', 'Критика');
DEFINE('_KARMA_APPLAUD', 'Похвала');
DEFINE('_KARMA_BACK', 'За повратак на тему,');
DEFINE('_KARMA_WAIT', 'Можете мењати карму само једне особе у сваких 6 часова. <br/>Молимо сачекајте толико пре поновног мењања било чије карме.');
DEFINE('_KARMA_SELF_DECREASE', 'Молимо немојте покушавати да смањите своју карму!');
DEFINE('_KARMA_SELF_INCREASE', 'Ваша карма је смањена због покушаја да је сами повећате!');
DEFINE('_KARMA_DECREASED', 'Корисникова карма је смањена. Ако се не будете враћени на тему за који тренутак,');
DEFINE('_KARMA_INCREASED', 'Корисникова карма је увећана. Ако се не будете враћени на тему за који тренутак,');
DEFINE('_COM_A_TEMPLATE', 'Шаблон');
DEFINE('_COM_A_TEMPLATE_DESC', 'Одаберите шаблон за употребу.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Скупови слика');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Одаберите шаблон скупа слика за употребу.');
DEFINE('_PREVIEW_CLOSE', 'Затвори овај прозор');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Користи статистичку траку порука');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Поставите на „Да“ ако желите да прикажете број порука одређеног корисника у графичком облику на статистичкој траци.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'Број боје за статистичку траку');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'Упишите број боје коју желите користити за статистичку траку порука');
DEFINE('_LATEST_REDIRECT',
    'Kunena мора (поново) успоставити привилегије приступа пре прављења списка најновијих порука.\nНе брините, ово је потпуно нормално након више од 30 минута неактивности или након поновне пријаве.\nМолимо, поновите свој захтев.');
DEFINE('_SMILE_COLOUR', 'Боја');
DEFINE('_SMILE_SIZE', 'Величина');
DEFINE('_COLOUR_DEFAULT', 'Уобичајена');
DEFINE('_COLOUR_RED', 'Црвена');
DEFINE('_COLOUR_PURPLE', 'Љубичаста');
DEFINE('_COLOUR_BLUE', 'Плава');
DEFINE('_COLOUR_GREEN', 'Зелена');
DEFINE('_COLOUR_YELLOW', 'Жута');
DEFINE('_COLOUR_ORANGE', 'Наранџаста');
DEFINE('_COLOUR_DARKBLUE', 'Тамно плава');
DEFINE('_COLOUR_BROWN', 'Браон');
DEFINE('_COLOUR_GOLD', 'Златна');
DEFINE('_COLOUR_SILVER', 'Сребрна');
DEFINE('_SIZE_NORMAL', 'Нормална');
DEFINE('_SIZE_SMALL', 'Мала');
DEFINE('_SIZE_VSMALL', 'Врло мала');
DEFINE('_SIZE_BIG', 'Велика');
DEFINE('_SIZE_VBIG', 'Врло велика');
DEFINE('_IMAGE_SELECT_FILE', 'Одабир слике коју желите приложити');
DEFINE('_FILE_SELECT_FILE', 'Одабир датотеке коју желите приложити');
DEFINE('_FILE_NOT_UPLOADED', 'Ваша датотека није послата. Покушајте слање поново или уредите поруку');
DEFINE('_IMAGE_NOT_UPLOADED', 'Ваша слика није послата. Покушајте слање поново или уредите поруку');
DEFINE('_BBCODE_IMGPH', 'Уметните [img] ознаку у текст како бисте додали слику');
DEFINE('_BBCODE_FILEPH', ' Уметните [file] ознаку у текст како бисте додали датотеку');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Штиклирајте да би <b><u>отказали претплате</u></b> на све теме (укључујући и невидљиве ради отклањања грешака)');
DEFINE('_LINK_JS_REMOVED', '<em>Активна веза која садржи JavaScript је аутоматски уклоњена</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Изглед и угођај');
DEFINE('_COM_A_USERS', 'Корисничка подешавања');
DEFINE('_COM_A_LENGTHS', 'Разна подешавања дужина');
DEFINE('_COM_A_SUBJECTLENGTH', 'Макс. дужина наслова');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Максимална дужина линије наслова. База подржава максималан број од 255 знакова. Ако је сајт подешен за употребу вишебајтовних знакова попут Unicode, UTF-8, не-ISO-8599-x поставите мањи максимум по формули:<br/><tt>round_down(255/(maximum character set byte size per character))</tt><br/> Пример за UTF-8, за који је макс. величина знака у бајтовима по знаку 4 бајта: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'Тема/Форум');
DEFINE('_LATEST_NUMBER', 'Нових порука');
DEFINE('_COM_A_SHOWNEW', 'Прикажи нове поруке');
DEFINE('_COM_A_SHOWNEW_DESC', 'Ако је постављено на „Да“ Kunena показује корисницима индикатор уз форуме који садрже нове поруке и које су поруке нове од њихове последње посете.');
DEFINE('_COM_A_NEWCHAR', 'Индикатор за „Нове“');
DEFINE('_COM_A_NEWCHAR_DESC', 'Овде дефинишете шта желите користити као индикатор нових порука (као „!“ или „Ново!“)');
DEFINE('_LATEST_AUTHOR', 'Аутор последње поруке');
DEFINE('_GEN_FORUM_NEWPOST', 'Нове поруке');
DEFINE('_GEN_FORUM_NOTNEW', 'Нема нових порука');
DEFINE('_GEN_UNREAD', 'Непрочитана тема');
DEFINE('_GEN_NOUNREAD', 'Прочитана тема');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Означи све форуме као прочитане');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Означи овај форум као прочитан');
DEFINE('_GEN_FORUM_MARKED', 'Све поруке у овом форуму су означене као прочитане');
DEFINE('_GEN_ALL_MARKED', 'Све поруке су означене као прочитане');
DEFINE('_IMAGE_UPLOAD', 'Постављање слике');
DEFINE('_IMAGE_DIMENSIONS', 'Ваша слика може бити максимално (ширина x висина - величина)');
DEFINE('_IMAGE_ERROR_TYPE', 'Користите само jpeg, gif или png формате слика');
DEFINE('_IMAGE_ERROR_EMPTY', 'Изаберите датотеку пре постављања');
DEFINE('_IMAGE_ERROR_SIZE', 'Величина слике прелази максимум који је поставио администратор.');
DEFINE('_IMAGE_ERROR_WIDTH', 'Ширина слике прелази максимум који је поставио администратор.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'Висина слике прелази максимум који је поставио администратор.');
DEFINE('_IMAGE_UPLOADED', 'Ваша слика је постављена.');
DEFINE('_COM_A_IMAGE', 'Слике');
DEFINE('_COM_A_IMGHEIGHT', 'Макс. висина слике');
DEFINE('_COM_A_IMGWIDTH', 'Макс. ширина слике');
DEFINE('_COM_A_IMGSIZE', 'Макс. величина слике<br/><em>у килобајтима</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Дозволи јавно постављање слика');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Поставите на „Да“ ако желите да сви (јавност), могу постављати слике.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Дозволи регистровано постављање слика');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Поставите на „Да“ ако желите да регистровани и пријављени корисници могу постављати слике.<br/>Напомена: (Супер)администратори и модератори могу увек да постављају слике.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Слање датотека');
DEFINE('_FILE_TYPES', 'Датотека може бити врста - макс. величина');
DEFINE('_FILE_ERROR_TYPE', 'Постављање је дозвољено само за датотеке врсте:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Изаберите датотеку пре постављања');
DEFINE('_FILE_ERROR_SIZE', 'Величина датотеке прелази максимум који је поставио администратор.');
DEFINE('_COM_A_FILE', 'Датотеке');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'Дозвољене врсте датотека');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Наведите врсте датотека које су дозвољене за постављање. Ставке у списку раздвојите зарезом и користите само <strong>мала слова</strong> без размака.<br />Пример: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Мак. величина датотеке<br/><em>у килобајтима</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Дозволи јавно постављање датотека');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Поставите на „Да“ ако желите да сви (јавност) могу постављати датотеке.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Дозволи регистровано постављање датотека');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Поставите на „Да“ ако желите да регистровани и пријављени корисници могу постављати датотеке.<br/>Напомена: (Супер)администратори и модератори могу увек да постављају датотеке.');
DEFINE('_SUBMIT_CANCEL', 'Слање поруке је отказано');
DEFINE('_HELP_SUBMIT', 'Кликните овде за слање поруке');
DEFINE('_HELP_PREVIEW', 'Кликните овде да видите како ће изгледати порука када је пошаљете');
DEFINE('_HELP_CANCEL', 'Кликните овде за отказивање поруке');
DEFINE('_POST_DELETE_ATT', 'Ако је кућица штиклирана, сви прилози слика и датотека уз поруку коју бришете ће такође бити избрисани (препоручљиво).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Прикажи ознаке измена');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Поставите на „Да“ ако желите да измењена порука буде означена текстом који показује ко је и када извршио измене.');
DEFINE('_EDIT_BY', 'Поруку изменио/ла:');
DEFINE('_EDIT_AT', ' ');
DEFINE('_UPLOAD_ERROR_GENERAL', 'Грешка при слању аватара. Покушајте поново или обавестите администратора система');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Преглед послатих слика');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Преглед послатих датотека');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Број послатих слика');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Број послатих датотека');
DEFINE('_COM_A_IMGB_ENLARGE', 'Кликните на слику да би је видели у пуној величини');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'Кликните на слику датотеке да би је преузели');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'Опција „Замени неутралном сликом“ ће заменити одабрану слику неутралном.<br /> Ово вам омогућава уклањање стварне датотеке без оштећења порука.<br /><small><em>Приметићете да понекад измена није видљива док не освежите ову страницу у читачу.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Тренутна неутрална слика');
DEFINE('_COM_A_IMGB_REPLACE', 'Замени неутралном сликом');
DEFINE('_COM_A_IMGB_REMOVE', 'Потпуно уклањање');
DEFINE('_COM_A_IMGB_NAME', 'Назив');
DEFINE('_COM_A_IMGB_SIZE', 'Величина');
DEFINE('_COM_A_IMGB_DIMS', 'Димензије');
DEFINE('_COM_A_IMGB_CONFIRM', 'Јесте ли потпуно сигурни да желите избрисати ову датотеку? \n Брисање датотеке може произвести осакаћену референтну поруку...');
DEFINE('_COM_A_IMGB_VIEW', 'Отвори поруку (за уређивање)');
DEFINE('_COM_A_IMGB_NO_POST', 'Нема референтне поруке!');
DEFINE('_USER_CHANGE_VIEW', 'Промене ових поставки ће прећи у дејство када поново посетите форум.<br /> Ако желите да промените врсту приказа „у лету“ можете употребити опције из траке менија форума.');
DEFINE('_MOSBOT_DISCUSS_A', 'Дискутујте овај чланак у форуму. (');
DEFINE('_MOSBOT_DISCUSS_B', ' порука)');
DEFINE('_POST_DISCUSS', 'Ово је дискусија о садржају чланка');
DEFINE('_COM_A_RSS', 'Укључи RSS извор');
DEFINE('_COM_A_RSS_DESC', 'RSS извор омогућава корисницима примање најновијих порука на своју радну површ или кроз RSS читач (погледајте <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> за пример).');
DEFINE('_LISTCAT_RSS', 'примите најновије поруке директно на радну површ');
DEFINE('_SEARCH_REDIRECT', 'Kunena мора поново успоставити привилегије приступа пре испуњавања захтева за претрагу.\nНе брините, ово је потпуно нормално након више од 30 минута неактивности.\nПоднесите захтев за претрагу поново.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunena подешавања');
DEFINE('_COM_A_DISPLAY', 'Прикажи #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Текућа подешавања');
DEFINE('_COM_A_EXPLANATION', 'Објашњење');
DEFINE('_COM_A_BOARD_TITLE', 'Назив форума');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Име за форум');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Подразумевана врста приказа');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Бирајте између разгранатог и равног подразумеваног приказа');
DEFINE('_COM_A_THREADS', 'Тема по страници');
DEFINE('_COM_A_THREADS_DESC', 'Број тема који ће бити приказан на свакој страници');
DEFINE('_COM_A_REGISTERED_ONLY', 'Само за регистроване кориснике');
DEFINE('_COM_A_REG_ONLY_DESC', 'Поставите на „Да“ ако желите да дозволите коришћење форума само регистрованим корисницима (читање и писање), поставите на „Не“ ако желите да сви посетиоци могу да користите форум');
DEFINE('_COM_A_PUBWRITE', 'Јавно читање/писање');
DEFINE('_COM_A_PUBWRITE_DESC', 'Поставите на „Да“ ако желите да омогућите јавне привилегије писања, поставите на „Не“ ако желите да дозволите свим посетиоцима да виде поруке, а само регистрованим корисницима њихово писање');
DEFINE('_COM_A_USER_EDIT', 'Корисничке измене');
DEFINE('_COM_A_USER_EDIT_DESC', 'Поставите на „Да“ ако желите да дозволите регистрованим корисницима да врше измене својих порука.');
DEFINE('_COM_A_MESSAGE', 'Да бисте сачували измене горњих вредности притисните дугме „Сачувај“ на врху.');
DEFINE('_COM_A_HISTORY', 'Прикажи историју');
DEFINE('_COM_A_HISTORY_DESC', 'Поставите на „Да“ ако желите да прикажете историју теме при писању одговора');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Дозволи претплате');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Поставите на „Да“ ако желите да дозволите регистрованим корисницима да се претплате на тему и примају обавештења о новим порукама е-поштом');
DEFINE('_COM_A_HISTLIM', 'Ограничење историје');
DEFINE('_COM_A_HISTLIM_DESC', 'Колико порука треба да буде приказано у историји');
DEFINE('_COM_A_FLOOD', 'Заштита од преплављивања');
DEFINE('_COM_A_FLOOD_DESC', 'Колико секунди корисник мора да чека између две узастопне поруке. Поставите на 0 (нула) за искључивање заштите од преплављивања. НАПОМЕНА: Заштита од преплављивања <em>може</em> изазвати опадање перформанси.');
DEFINE('_COM_A_MODERATION', 'Обавештења за модераторе');
DEFINE('_COM_A_MODERATION_DESC',
    'Поставите на „Да“ ако желите да модератори примају е-поштом обавештење о новим порукама у форумима. Напомена: иако сваки (супер)администратор аутоматски има и све модераторске привилегије, изричито их одредите као модераторе
 форума како би и они примали е-пошту!');
DEFINE('_COM_A_SHOWMAIL', 'Приказ адресе е-поште');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Поставите на „Да“ ако не желите да икада буде приказана корисникова адреса е-поште; чак ни регистрованим корисницима.');
DEFINE('_COM_A_AVATAR', 'Дозволи аватаре');
DEFINE('_COM_A_AVATAR_DESC', 'Поставите на „Да“ ако желите да регистровани корисници имају аватар (могу их подешавати преко свог профила)');
DEFINE('_COM_A_AVHEIGHT', 'Макс. висина аватара');
DEFINE('_COM_A_AVWIDTH', 'Макс. ширина аватара');
DEFINE('_COM_A_AVSIZE', 'Макс. величина аватара<br/><em>у килобајтима</em>');
DEFINE('_COM_A_USERSTATS', 'Прикажи корисничку статистику');
DEFINE('_COM_A_USERSTATS_DESC', 'Поставите на „Да“ ако желите да корисничка статистика буде приказана, као број порука, врста корисника (Админ, Модератор, Корисник, итд.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Постављање аватара');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Поставите на „Да“ ако желите да регистровани корисници могу да постављају аватар на сервер.');
DEFINE('_COM_A_AVATARGALLERY', 'Коришћење галерије аватара');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Поставите на „Да“ ако желите да регистровани корисници могу изабрати аватар из галерије коју пружите (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Поставите на „Да“ ако желите да ранг регистрованих корисника буде приказан на основу броја њихових порука.<br/><strong>Морате такође укључити Корисничку статистику на Напредном језичку да би ово било приказано.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Користи слике рангирања');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Поставите на „Да“ ако желите да прикажете ранг регистрованих корисника сликом (из components/com_kunena/ranks). Ако је ово искључено, биће приказан текст ранга. Проверите документацију на www.kunena.com за више података о сликама рангирања');

//email and stuff
$_COM_A_NOTIFICATION = "Обавештење о новој поруци од ";
$_COM_A_NOTIFICATION1 = "Нова порука је послата на тему коју пратите на ";
$_COM_A_NOTIFICATION2 = "Теме које пратите можете администрирати кроз пратећи везу „мој профил“ на почетној страници форума након пријављивања. Из профила такође можете отказати претплату на тему.";
$_COM_A_NOTIFICATION3 = "Не одговарајте на ово обавештење јер је аутоматски послата е-пошта.";
$_COM_A_NOT_MOD1 = "Нова порука је послата у форум који ви модерирате на ";
$_COM_A_NOT_MOD2 = "Погледајте га кад се следећи пут пријавите.";
DEFINE('_COM_A_NO', 'Не');
DEFINE('_COM_A_YES', 'Да');
DEFINE('_COM_A_FLAT', 'Равно');
DEFINE('_COM_A_THREADED', 'Разгранато');
DEFINE('_COM_A_MESSAGES', 'Порука по страници');
DEFINE('_COM_A_MESSAGES_DESC', 'Број порука приказаних на страници');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Корисничко име');
DEFINE('_COM_A_USERNAME_DESC', 'Поставите на „Да“ ако желите да корисничко (пријавно) име буде употребљено уместо корисниковог правог имена.');
DEFINE('_COM_A_CHANGENAME', 'Дозволи промену имена');
DEFINE('_COM_A_CHANGENAME_DESC', 'Поставите на „Да“ ако желите да регистровани корисници могу мењати своје име када пишу поруку. Ако поставите на „Не“ регистровани корисници неће моћи да мењају своје име.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Форум је неактиван');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Поставите на „Да“ ако желите да учините форум привремено неактивним. Форум ће и даље бити доступан за преглед (супер)администраторима.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Порука неактивног форума');
DEFINE('_COM_A_PRUNE', 'Прочишћавање форума');
DEFINE('_COM_A_PRUNE_NAME', 'Форум за прочишћавање:');
DEFINE('_COM_A_PRUNE_DESC',
    'Функција Прочишћавања форума омогућава једноставно брисање тема у којима није било нових порука одређени број дана. Ово не уклања усидрене и закључане теме; њих морате уклонити ручно. Теме у закључаним форумима се не могу прочистити.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Прочисти теме у којима није било порука у задњих ');
DEFINE('_COM_A_PRUNE_DAYS', 'дана');
DEFINE('_COM_A_PRUNE_USERS', 'Прочишћавање корисника'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'Ова функција омогућава усклађивање списка Kunena корисника и списка корисника Joomla! веб места. Биће уклоњени сви профили Kunena корисника, који више не постоје у Joomla! систему.<br/>Када будете сигурни да желите да наставите са чишћењем, кликните на „Започни чишћење“ у траци менија изнад.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Акција');
DEFINE('_GEN_AUTHOR', 'Аутор');
DEFINE('_GEN_BY', 'од');
DEFINE('_GEN_CANCEL', 'Одустани');
DEFINE('_GEN_CONTINUE', 'Пошаљи');
DEFINE('_GEN_DATE', 'Датум');
DEFINE('_GEN_DELETE', 'Обриши');
DEFINE('_GEN_EDIT', 'Уреди');
DEFINE('_GEN_EMAIL', 'Е-пошта');
DEFINE('_GEN_EMOTICONS', 'Смајлији');
DEFINE('_GEN_FLAT', 'Равно');
DEFINE('_GEN_FLAT_VIEW', 'Равно');
DEFINE('_GEN_FORUMLIST', 'Списак форума');
DEFINE('_GEN_FORUM', 'Форум');
DEFINE('_GEN_HELP', 'Помоћ');
DEFINE('_GEN_HITS', 'Посета');
DEFINE('_GEN_LAST_POST', 'Последња порука');
DEFINE('_GEN_LATEST_POSTS', 'Прикажи последње поруке');
DEFINE('_GEN_LOCK', 'Закључај');
DEFINE('_GEN_UNLOCK', 'Откључај');
DEFINE('_GEN_LOCKED_FORUM', 'Форум је закључан');
DEFINE('_GEN_LOCKED_TOPIC', 'Тема је закључана');
DEFINE('_GEN_MESSAGE', 'Порука');
DEFINE('_GEN_MODERATED', 'Форум је модериран; Преглед пре објављивања.');
DEFINE('_GEN_MODERATORS', 'Модератори');
DEFINE('_GEN_MOVE', 'Премести');
DEFINE('_GEN_NAME', 'Име');
DEFINE('_GEN_POST_NEW_TOPIC', 'Објави нову тему');
DEFINE('_GEN_POST_REPLY', 'Пошаљи одговор');
DEFINE('_GEN_MYPROFILE', 'Мој профил');
DEFINE('_GEN_QUOTE', 'Цитат');
DEFINE('_GEN_REPLY', 'Одговор');
DEFINE('_GEN_REPLIES', 'Одговора');
DEFINE('_GEN_THREADED', 'Разгранато');
DEFINE('_GEN_THREADED_VIEW', 'Разгранато');
DEFINE('_GEN_SIGNATURE', 'Потпис');
DEFINE('_GEN_ISSTICKY', 'Тема је усидрена.');
DEFINE('_GEN_STICKY', 'Усидри');
DEFINE('_GEN_UNSTICKY', 'Одсидри');
DEFINE('_GEN_SUBJECT', 'Наслов');
DEFINE('_GEN_SUBMIT', 'Пошаљи');
DEFINE('_GEN_TOPIC', 'Тема');
DEFINE('_GEN_TOPICS', 'Теме');
DEFINE('_GEN_TOPIC_ICON', 'икона теме');
DEFINE('_GEN_SEARCH_BOX', 'Претражи форум');
$_GEN_THREADED_VIEW = "Разгранато";
$_GEN_FLAT_VIEW = "Равно";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Постави');
DEFINE('_UPLOAD_DIMENSIONS', 'Ваша слика може бити максимално (ширина x висина - величина)');
DEFINE('_UPLOAD_SUBMIT', 'Изаберите нови аватар за постављање');
DEFINE('_UPLOAD_SELECT_FILE', 'Одабир датотеке');
DEFINE('_UPLOAD_ERROR_TYPE', 'Користите само jpeg, gif или png формат слике');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Изаберите датотеку пре постављања');
DEFINE('_UPLOAD_ERROR_NAME', 'Име датотеке слике може садржати само алфанумеричке знаке без размака.');
DEFINE('_UPLOAD_ERROR_SIZE', 'Величина слике прелази максимум који је поставио администратор.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'Ширина слике прелази максимум који је поставио администратор.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Висина слике прелази максимум који је поставио администратор.');
DEFINE('_UPLOAD_ERROR_CHOOSE', 'Нисте изабрали аватар из галерије...');
DEFINE('_UPLOAD_UPLOADED', 'Ваш аватар је постављен на сервер.');
DEFINE('_UPLOAD_GALLERY', 'Изаберите један од аватара из галерије:');
DEFINE('_UPLOAD_CHOOSE', 'Потврди избор');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Администратор их мора прво направити из ');
DEFINE('_LISTCAT_DO', 'Они ће знати шта треба учинити ');
DEFINE('_LISTCAT_INFORM', 'Обавестите их и реците им да пожуре!');
DEFINE('_LISTCAT_NO_CATS', 'Још нема дефинисаних категорија на форуму.');
DEFINE('_LISTCAT_PANEL', 'Администрациони панел за Joomla! OS CMS.');
DEFINE('_LISTCAT_PENDING', 'поруке на чекању');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Нема порука на чекању у овом форуму.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'Припремате се да избришете поруку');
DEFINE('_POST_ABOUT_DELETE', '<strong>НАПОМЕНА:</strong><br/>
- Ако избришете тему у форуму (прву поруку у нити) биће такође избрисани и сви одговори!
Ако желите да уклоните само садржај размотрите пражњење поруке и имена аутора.
<br/>
- Ако избришете неку од обичних порука остали одговори ће бити померени на горе за једно место у хијерархији.');
DEFINE('_POST_CLICK', 'кликните овде');
DEFINE('_POST_ERROR', 'Није пронађено корисничко име/адреса е-поште. Значајна грешка у бази података није записана');
DEFINE('_POST_ERROR_MESSAGE', 'Дошло је до непознате SQL грешке и ваша порука није послата.  Ако се грешка понавља контактирајте администратора.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Дошло је до грешке и ваша порука није ажурирана.  Покушајте поново.  Ако се грешка понавља контактирајте администратора.');
DEFINE('_POST_ERROR_TOPIC', 'Грешка приликом брисања. Проверите детаље о грешци испод:');
DEFINE('_POST_FORGOT_NAME', 'Заборавили сте да унесете ваше име.  Кликните дугме за назад у вашем веб читачу и покушајте поново.');
DEFINE('_POST_FORGOT_SUBJECT', 'Заборавили сте да унесете наслов.  Кликните дугме за назад у вашем веб читачу и покушајте поново.');
DEFINE('_POST_FORGOT_MESSAGE', 'Заборавили сте да унесете поруку.  Кликните дугме за назад у вашем веб читачу и покушајте поново.');
DEFINE('_POST_INVALID', 'Затражен је неисправан ИД поруке.');
DEFINE('_POST_LOCK_SET', 'Тема је закључана.');
DEFINE('_POST_LOCK_NOT_SET', 'Тема није закључана.');
DEFINE('_POST_LOCK_UNSET', 'Тема је откључана.');
DEFINE('_POST_LOCK_NOT_UNSET', 'Тема није откључана.');
DEFINE('_POST_MESSAGE', 'Слање нове поруке у ');
DEFINE('_POST_MOVE_TOPIC', 'Премештање ове теме у форум ');
DEFINE('_POST_NEW', 'Слање нове поруке у: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Ваша претплата за ову тему није извршена.');
DEFINE('_POST_NOTIFIED', 'Штиклирајте ову кућицу ако желите да примате обавештење о одговорима на ову тему.');
DEFINE('_POST_STICKY_SET', 'Тема је усидрена.');
DEFINE('_POST_STICKY_NOT_SET', 'Тема није усидрена.');
DEFINE('_POST_STICKY_UNSET', 'Тема је одсидрена.');
DEFINE('_POST_STICKY_NOT_UNSET', 'Тема није одсидрена.');
DEFINE('_POST_SUBSCRIBE', 'претплата');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Претплаћени сте на ову тему.');
DEFINE('_POST_SUCCESS', 'Ваша порука је успешно');
DEFINE('_POST_SUCCES_REVIEW', 'Ваша порука је успешно послата.  Модератор ће је прегледати пре објаве на форуму.');
DEFINE('_POST_SUCCESS_REQUEST', 'Ваш захтев је обрађен.  Ако не будете враћени на тему за неколико тренутака,');
DEFINE('_POST_TOPIC_HISTORY', 'Историја теме');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Макс. приказивање последњих');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'порука  -  <i>(Последња порука на врху)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Ваша тема НИЈЕ премештена. За повратак на тему:');
DEFINE('_POST_TOPIC_FLOOD1', 'Администратор овог форума је укључио заштиту од преплављивања, што значи да морате причекати ');
DEFINE('_POST_TOPIC_FLOOD2', ' секунди између слања нових порука.');
DEFINE('_POST_TOPIC_FLOOD3', 'За повратак на форум кликните дугме за назад у вашем веб читачу.');
DEFINE('_POST_EMAIL_NEVER', ' ваша адреса е-поште никада неће бити приказана на сајту.');
DEFINE('_POST_EMAIL_REGISTERED', 'ваша адреса е-поште ће бити доступна само регистрованим корисницима.');
DEFINE('_POST_LOCKED', ' закључао администратор.');
DEFINE('_POST_NO_NEW', 'Нови одговори нису дозвољени.');
DEFINE('_POST_NO_PUBACCESS1', 'Администратор је онемогућио јавни приступ писања.');
DEFINE('_POST_NO_PUBACCESS2', 'Само регистровани / пријављени корисници<br /> могу учествовати у форуму.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Још нема тема у овом форуму <<');
DEFINE('_SHOWCAT_PENDING', 'поруке на чекању');
// userprofile.php
DEFINE('_USER_DELETE', ' штиклирајте за брисање вашег потписа');
DEFINE('_USER_ERROR_A', 'На ову страницу сте стигли грешком. Обавестите администратора на које везе ');
DEFINE('_USER_ERROR_B', 'сте кликнули да би стигли овде. Она или он онда могу поднети извештај о грешци.');
DEFINE('_USER_ERROR_C', 'Хвала!');
DEFINE('_USER_ERROR_D', 'Број грешке који треба укључити у извештај: ');
DEFINE('_USER_GENERAL', 'Опште опције профила');
DEFINE('_USER_MODERATOR', 'Ви сте модератор ових форума');
DEFINE('_USER_MODERATOR_NONE', 'Нема форума додељених вама');
DEFINE('_USER_MODERATOR_ADMIN', 'Администратори су модератори свих форума.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Немате никаквих претплата');
//DEFINE('_USER_PREFERED', 'Жељени приказ');
DEFINE('_USER_PROFILE', 'Профил за ');
DEFINE('_USER_PROFILE_NOT_A', 'Ваш профил ');
DEFINE('_USER_PROFILE_NOT_B', 'није');
DEFINE('_USER_PROFILE_NOT_C', ' ажуриран.');
DEFINE('_USER_PROFILE_UPDATED', 'Ваш профил је ажуриран.');
DEFINE('_USER_RETURN_A', 'Ако не будете враћени на профил за неколико тренутака ');
DEFINE('_USER_RETURN_B', 'кликните овде');
DEFINE('_USER_SUBSCRIPTIONS', 'Ваше претплате');
DEFINE('_USER_UNSUBSCRIBE', 'Откажи претплату');
DEFINE('_USER_UNSUBSCRIBE_A', 'Ваша претплата на ову тему ');
DEFINE('_USER_UNSUBSCRIBE_B', 'није');
DEFINE('_USER_UNSUBSCRIBE_C', ' отказана.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Ваша претплата на ову тему је отказана.');
DEFINE('_USER_DELETEAV', ' штиклирајте за брисање вашег аватара');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Редослед');
DEFINE('_USER_ORDER_DESC', 'Последња порука на врху');
DEFINE('_USER_ORDER_ASC', 'Прва порука на врху');
// view.php
DEFINE('_VIEW_DISABLED', 'Администратор је онемогућио јавни приступ писања.');
DEFINE('_VIEW_POSTED', 'Послао/ла');
DEFINE('_VIEW_SUBSCRIBE', ':: Претплаћивање на ову тему ::');
DEFINE('_MODERATION_INVALID_ID', 'Затражен је неисправан ИД поруке.');
DEFINE('_VIEW_NO_POSTS', 'Нема порука у овом форуму.');
DEFINE('_VIEW_VISITOR', 'Посетилац');
DEFINE('_VIEW_ADMIN', 'Администратор');
DEFINE('_VIEW_USER', 'Корисник');
DEFINE('_VIEW_MODERATOR', 'Модератор');
DEFINE('_VIEW_REPLY', 'Одговор на ову поруку');
DEFINE('_VIEW_EDIT', 'Уређивање ове поруке');
DEFINE('_VIEW_QUOTE', 'Цитирање ове поруке у новој поруци');
DEFINE('_VIEW_DELETE', 'Брисање ове поруке');
DEFINE('_VIEW_STICKY', 'Сидрење ове теме');
DEFINE('_VIEW_UNSTICKY', 'Одсидравање ове теме');
DEFINE('_VIEW_LOCK', 'Закључавање ове теме');
DEFINE('_VIEW_UNLOCK', 'Откључавање ове теме');
DEFINE('_VIEW_MOVE', 'Премештање теме у други форум');
DEFINE('_VIEW_SUBSCRIBETXT', 'Претплатите се на ову тему да би примали обавештења о новим порукама е-поштом.');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Форум');
DEFINE('_POSTS', 'Порука:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Порука');
DEFINE('_FORUM_NOT_ALLOWED', 'Форум');
DEFINE('_FORUM_IS_OFFLINE', 'Форум је НЕАКТИВАН!');
DEFINE('_PAGE', 'Страница: ');
DEFINE('_NO_POSTS', 'Нема порука');
DEFINE('_CHARS', 'знакова макс.');
DEFINE('_HTML_YES', 'HTML је искључен');
DEFINE('_YOUR_AVATAR', '<b>Ваш аватар</b>');
DEFINE('_NON_SELECTED', 'Још није одабран <br />');
DEFINE('_SET_NEW_AVATAR', 'Одабир новог аватара');
DEFINE('_THREAD_UNSUBSCRIBE', 'Откажи претплату');
DEFINE('_SHOW_LAST_POSTS', 'Теме активне у задњих');
DEFINE('_SHOW_HOURS', 'часова');
DEFINE('_SHOW_POSTS', 'укупно: ');
DEFINE('_DESCRIPTION_POSTS', 'Приказане су најновије поруке из активних тема');
DEFINE('_SHOW_4_HOURS', '4 часа');
DEFINE('_SHOW_8_HOURS', '8 часова');
DEFINE('_SHOW_12_HOURS', '12 часова');
DEFINE('_SHOW_24_HOURS', '24 часа');
DEFINE('_SHOW_48_HOURS', '48 часова');
DEFINE('_SHOW_WEEK', 'недеља');
DEFINE('_POSTED_AT', 'Послато');
DEFINE('_DATETIME', 'd.m.y H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Нема нових порука у временском интервалу који сте одабрали.');
DEFINE('_MESSAGE', 'Порука');
DEFINE('_NO_SMILIE', 'не');
DEFINE('_FORUM_UNAUTHORIZIED', 'Овај форум је отворен само за регистроване и пријављене кориснике.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Ако сте већ регистровани, прво се пријавите.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Модерирање');
DEFINE('_MOD_APPROVE', 'Одобри');
DEFINE('_MOD_DELETE', 'Избриши');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Прикажи најновију поруку');
DEFINE('_POST_WROTE', 'написао/ла');
DEFINE('_COM_A_EMAIL', 'Адреса е-поште форума');
DEFINE('_COM_A_EMAIL_DESC', 'Ово је адреса е-поште форума. Ово треба да је важећа адреса е-поште');
DEFINE('_COM_A_WRAP', 'Растави речи дуже од');
DEFINE('_COM_A_WRAP_DESC',
    'Унесите максимални број знакова које једна реч сме да има. Ова опција омогућава да дотерате испис Kunena порука у склад са шаблоном.<br/> 70 знакова је вероватно максимум за шаблон фиксне ширине, али можда ћете морати мало експериментисати.<br/>УРЛ, без обзира колико је дуг, неће бити растављен.');
DEFINE('_COM_A_SIGNATURE', 'Макс. дужина потписа');
DEFINE('_COM_A_SIGNATURE_DESC', 'Максимални број знакова дозвољен у потпису корисника.');
DEFINE('_SHOWCAT_NOPENDING', 'Нема порука на чекању');
DEFINE('_COM_A_BOARD_OFSET', 'Временска разлика форума');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Неки форуми су налазе на серверима у временској зони различитој од корисникове. Подесите разлику у часовима коју Kunena мора користити у време остављања поруке. Можете користити позитивне и негативне бројеве.');
//New in RC2
DEFINE('_COM_A_BASICS', 'Основе');
DEFINE('_COM_A_FRONTEND', 'Изглед');
DEFINE('_COM_A_SECURITY', 'Сигурност');
DEFINE('_COM_A_AVATARS', 'Аватари');
DEFINE('_COM_A_INTEGRATION', 'Интеграција');
DEFINE('_COM_A_PMS', 'Омогући приватне поруке');
DEFINE('_COM_A_PMS_DESC',
    'Изаберите одговарајућу компоненту за приватне поруке, ако сте је инсталирали. Clexus PM избор ће такође омогућити опције везане за ClexusPM кориснички профил (као што су ICQ, AIM, Yahoo, MSN и везе ка профилу ако их подржава употребљени Kunena шаблон).');
DEFINE('_VIEW_PMS', 'Кликните овде за слање приватне поруке овом кориснику.');
//new in RC3
DEFINE('_POST_RE', 'Одг: ');
DEFINE('_BBCODE_BOLD', 'Подебљано: [b]текст[/b]');
DEFINE('_BBCODE_ITALIC', 'Курзив: [i]текст[/i]');
DEFINE('_BBCODE_UNDERL', 'Подвучено: [u]текст[/u]');
DEFINE('_BBCODE_QUOTE', 'Цитат: [quote]текст[/quote]');
DEFINE('_BBCODE_CODE', 'Код: [code]код[/code]');
DEFINE('_BBCODE_ULIST', 'Неуређена листа: [ul] [li]текст[/li] [/ul] - Напомена: листа мора имати ставке');
DEFINE('_BBCODE_OLIST', 'Уређена листа: [ol] [li]текст[/li] [/ol] - Напомена: листа мора имати ставке');
DEFINE('_BBCODE_IMAGE', 'Слика: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Веза: [url=http://www.zzz.com/]Ово је веза[/url]');
DEFINE('_BBCODE_CLOSA', 'Затвори све ознаке');
DEFINE('_BBCODE_CLOSE', 'Затвори све bbCode ознаке');
DEFINE('_BBCODE_COLOR', 'Боја: [color=#FF6600]текст[/color]');
DEFINE('_BBCODE_SIZE', 'Величина: [size=1]величина слова[/size] - Напомена: величине могу бити од 1 до 5');
DEFINE('_BBCODE_LITEM', 'Ставка: [li] ставка на листи [/li]');
DEFINE('_BBCODE_HINT', 'bbCode помоћ - Напомена: bbCode се може употребљавати и на означеном тексту!');
DEFINE('_COM_A_TAWIDTH', 'Ширина текст поља');
DEFINE('_COM_A_TAWIDTH_DESC', 'Прилагодите ширину текст поља за поруку да одговара вашем шаблону. <br/>Палета са смајлијима теме ће бити преломљена у други ред ако је ширина <= 420 пиксела');
DEFINE('_COM_A_TAHEIGHT', 'Висина текст поља');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Прилагодите висину текст поља за поруку да одговара вашем шаблону.');
DEFINE('_COM_A_ASK_EMAIL', 'Обавезна е-пошта');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Захтевање адресе е-поште када корисници или посетиоци објављују поруку. Поставите на „Не“ ако желите да ова могућност буде првобитно прескочена. Писцима порука неће бити захтевана адреса е-поште.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Управљање рангирањем');
define('_KUNENA_SORTRANKS', 'Поређај по рангу');

define('_KUNENA_RANKSIMAGE', 'Слика ранга');
define('_KUNENA_RANKS', 'Назив ранга');
define('_KUNENA_RANKS_SPECIAL', 'Посебно');
define('_KUNENA_RANKSMIN', 'Најмањи број порука');
define('_KUNENA_RANKS_ACTION', 'Акције');
define('_KUNENA_NEW_RANK', 'Нови ранг');

?>
