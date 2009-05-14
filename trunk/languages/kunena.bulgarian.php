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
* Language: Bulgarian
* For version: 1.0.7
* Translators: Taken from the file for Fireboard with translators Ivo Apostolov, Plamen Penkov, Teodor Baturov. Recent corrections: Aleksandar Tsonev
* Web:  http://www.archerybg.com
* Translation version: 1.0
* Encoding: UTF-8
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Нямате право на директен достъп до този адрес.');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5 // _EDIT_TITLE 
DEFINE('_EDIT_TITLE', 'Промяна');
DEFINE('_YOUR_NAME', 'Вашето име:');
DEFINE('_EMAIL', 'Е-мейл:');
DEFINE('_UNAME', 'Потребителско име:');
DEFINE('_PASS', 'Парола:');
DEFINE('_VPASS', 'Потвърдете паролата:');

// 1.0.5

DEFINE('_KUNENA_BBCODE_HIDE', 'Това е скрито за нерегистрирани:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Parent Forum must not be the same.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Parent Forum is one of its own childs.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID does not exist.');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Май забрави да си напишеш името!');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Зададе ли E-Mail-а?')
;
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Напиши поне едно заглавие.');
DEFINE('_KUNENA_EDIT_TITLE', 'Смяна на паролата');
DEFINE('_KUNENA_YOUR_NAME', 'Моето име:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'Ник:');
DEFINE('_KUNENA_PASS', 'Парола:');
DEFINE('_KUNENA_VPASS', 'Повтори Парола:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'User details have been saved.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
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
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Сесийно време');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and NEW indicator are reset.');

// Advanced administrator merge-split functions

DEFINE('_GEN_MERGE', 'Сливане');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Слей тази тема с');
DEFINE('_POST_MERGE_GHOST', 'Остави едно копие в темата');
DEFINE('_POST_SUCCESS_MERGE', 'Темите бяха успешно слети!');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Сравнението беше неуспешно!'); 
DEFINE('_GEN_SPLIT', 'Раздели');
DEFINE('_GEN_DOSPLIT', 'Иди');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Темата беше успешно разделена!');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Заглавието е променено.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Промяна на заглавие неуспешно.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Разделянето беше неуспешно!');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicate, identical message has been ignored.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'Слей');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'Обратно сливане');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional

DEFINE('_POST_UNFAVORITED_TOPIC', 'Тази тема беше премахната от вашите любими.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'This thread has <b>NOT</b> removed from your favorites');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Your request to remove from favorites has been processed.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'This thread has been removed from your subscriptions.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'This thread has <b>NOT</b> removed from your subscriptions');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Your request to remove from subscriptions has been processed.');
DEFINE('_POST_NO_DEST_CATEGORY', 'No destination category was selected. Nothing was moved.');

// Default_EX template

DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Последни Дискусии');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Моите Дискусии');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_KUNENA_CATEGORY', 'Категория:');
DEFINE('_KUNENA_CATEGORIES', 'Категории');
DEFINE('_KUNENA_POSTED_AT', 'Публикуван');
DEFINE('_KUNENA_AGO', 'преди');
DEFINE('_KUNENA_DISCUSSIONS', 'Дискусии');
DEFINE('_KUNENA_TOTAL_THREADS', 'Общо Връзки:');
DEFINE('_SHOW_DEFAULT', 'Стандартно');
DEFINE('_SHOW_MONTH', 'Месец');
DEFINE('_SHOW_YEAR', 'Година');

// 1.0.4

DEFINE('_KUNENA_COPY_FILE', 'Копирай "%src%" в "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Запиши CSS-файла тук... Файл="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Прикаченият табелен файл беше успешно с последната 1.0.x- версия актуализиран.');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Прикачените файлове бяха успешно с последната 1.0.x- версия актуализирани.');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Подфорумите не можаха да бъдат сортирани във йерархията на постовете. Нищо не беше изтрито!');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Поста не беше изтрит! И нищо друго не беше изтрито!');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', ' Текста не беше изтрит! Моля актуализирай дата банка мануално (mesid=%id%)!');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Всичко беше изтрито, но актуализирането на потребителската статистика беше неуспешно!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Намерена е грашка в дата банка! За да съгласуваш отговорите към новият форум, актуализирай дата банка.");
DEFINE('_KUNENA_UNIST_SUCCESS', "FireBoard беше успешно деинсталиран!");
DEFINE('_KUNENA_PDF_VERSION', 'FireBoard-Форум-Версия: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Генериран: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Няма форуми за претърсване!');

DEFINE('_KUNENA_ERRORADDUSERS', 'Грешка при прибавяне на потребител!');
DEFINE('_KUNENA_USERSSYNCMSG', 'Потребителите бяха синхронизирани! Изтрити: %d, Добавени: %d');
DEFINE('_KUNENA_USERSSYNCADD', ', Добавени:');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Не бяха открити подходящи профили за синхронизиране!');
DEFINE('_KUNENA_SYNC_USERS', 'Синхронизирай потребителите');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Синхронизиране на потребителската табела на FireBoard с тази на Joomla!');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Изпрати E-Mail на администратора');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Избери &quot;Ja&quot;, ако искаш да получиш всяко изпратено до администратора съобщение и с E-Mail.');
DEFINE('_KUNENA_RANKS_EDIT', 'Обработка на ранга');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Скрий E-Mail-а');
DEFINE('_KUNENA_DT_DATE_FMT','%d.%m.%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%d.%m.%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Неделя');
DEFINE('_KUNENA_DT_LDAY_MON', 'Понеделник');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Вторник');
DEFINE('_KUNENA_DT_LDAY_WED', 'Сряда');
DEFINE('_KUNENA_DT_LDAY_THU', 'Четвъртък');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Петък');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Събота');
DEFINE('_KUNENA_DT_DAY_SUN', 'Нед');
DEFINE('_KUNENA_DT_DAY_MON', 'Пон');
DEFINE('_KUNENA_DT_DAY_TUE', 'Вт');
DEFINE('_KUNENA_DT_DAY_WED', 'Сря');
DEFINE('_KUNENA_DT_DAY_THU', 'Чет');
DEFINE('_KUNENA_DT_DAY_FRI', 'Пет');
DEFINE('_KUNENA_DT_DAY_SAT', 'Съб');
DEFINE('_KUNENA_DT_LMON_JAN', 'Януари');
DEFINE('_KUNENA_DT_LMON_FEB', 'Февруари');
DEFINE('_KUNENA_DT_LMON_MAR', 'Март');
DEFINE('_KUNENA_DT_LMON_APR', 'Април');
DEFINE('_KUNENA_DT_LMON_MAY', 'Май');
DEFINE('_KUNENA_DT_LMON_JUN', 'Юни');
DEFINE('_KUNENA_DT_LMON_JUL', 'Юли');
DEFINE('_KUNENA_DT_LMON_AUG', 'Август');
DEFINE('_KUNENA_DT_LMON_SEP', 'Септември');
DEFINE('_KUNENA_DT_LMON_OCT', 'Октомври');
DEFINE('_KUNENA_DT_LMON_NOV', 'Ноември');
DEFINE('_KUNENA_DT_LMON_DEV', 'Декември');
DEFINE('_KUNENA_DT_MON_JAN', 'Ян');
DEFINE('_KUNENA_DT_MON_FEB', 'Фев');
DEFINE('_KUNENA_DT_MON_MAR', 'Мар');
DEFINE('_KUNENA_DT_MON_APR', 'Апр');
DEFINE('_KUNENA_DT_MON_MAY', 'Май');
DEFINE('_KUNENA_DT_MON_JUN', 'Юни');
DEFINE('_KUNENA_DT_MON_JUL', 'Юли');
DEFINE('_KUNENA_DT_MON_AUG', 'Авг');
DEFINE('_KUNENA_DT_MON_SEP', 'Сеп');
DEFINE('_KUNENA_DT_MON_OCT', 'Окт');
DEFINE('_KUNENA_DT_MON_NOV', 'Нов');
DEFINE('_KUNENA_DT_MON_DEV', 'Дек');
DEFINE('_KUNENA_CHILD_BOARD', 'Подфорум');
DEFINE('_WHO_ONLINE_GUEST', 'Гости');
DEFINE('_WHO_ONLINE_MEMBER', 'Потребител');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'няма');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Обработка на картини:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Щракни тук за да продължиш...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Изпълни!');
DEFINE('_KUNENA_NO_ACCESS', 'Вие нямате достъп за този форум!');
DEFINE('_KUNENA_TIME_SINCE', '%time%');
DEFINE('_KUNENA_DATE_YEARS', 'Години');
DEFINE('_KUNENA_DATE_MONTHS', 'Месеци');
DEFINE('_KUNENA_DATE_WEEKS','Седмици');
DEFINE('_KUNENA_DATE_DAYS', 'Дни');
DEFINE('_KUNENA_DATE_HOURS', 'Часове');
DEFINE('_KUNENA_DATE_MINUTES', 'Минути');

// 1.0.3

DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Сигурни ли сте, че искате да премахнете примерните данни? Ще бъдат изтрити безвъзвратно.');

// 1.0.2

DEFINE('_KUNENA_HEADERADD', 'Заглавие над форума:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', 'Екран на форума');
DEFINE('_KUNENA_CLASS_SFX', 'CSS class suffix на форума');
DEFINE('_KUNENA_CLASS_SFXDESC', 'CSS suffix позволява направата на различен дизайн на форума.');
DEFINE('_COM_A_USER_EDIT_TIME', 'Време за писане във форума');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', '0 за неограничено време, иначе - време във секунди.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Допълнително време за запаметяване');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Зададените по подразбиране 600 секунди позволяват запаметяването на направените промени до 600 секунди, след като изчезне линкът за промяна');
DEFINE('_KUNENA_HELPPAGE','Разреши страницата с помощна информация');
DEFINE('_KUNENA_HELPPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Help page.');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA','Показване на помощна информация във kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','Ако е &quot;Да&quot; помощната информация ще се включи във kunena и връзката към външна помоща информация няма да работи. <b>Заб.:</b> Трябва да добавите &quot;ID на съдържанието на помощната информация&quot;.');
DEFINE('_KUNENA_HELPPAGE_CID','ID на помощната информация');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Трябва да настроите <b>"Да"</b> за настройката "Покажи помощ в kunena".');
DEFINE('_KUNENA_HELPPAGE_LINK','Връзка към външна помощна информация');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Ако имате връзка към външна пом.информация, моля включете <b>"Не"</b>за настроката "Покажи помощна информация във kunena".');
DEFINE('_KUNENA_RULESPAGE','Показвай страницата с правила');
DEFINE('_KUNENA_RULESPAGE_DESC','Ако е &quot;Да&quot; в заглавното меню ще се появи връзка към Вашата страница с правила.');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA','Покажи правилата във Fireboard');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','Ако е &quot;Да&quot; текста с правила ще бъде включен във kunena и връзката към външна страница с правила няма да работи. <b>Заб.:</b> Трябва да добавите и "ID на страницата с правила".');
DEFINE('_KUNENA_RULESPAGE_CID','ID на правилата');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Има значение, когато настройката "Покажи правила във kunena е <b>&quot;Да&quot;</b> за .');
DEFINE('_KUNENA_RULESPAGE_LINK','Връзка към външна страница с правила');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Ако използвате външна връзка за правила, настройте <b>"Не"</b> на "Покажи правила в kunena".');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','Не е намерена библиотеката GD');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','Не е намерена библиотеката GD');
DEFINE('_KUNENA_GD_INSTALLED','GD е достъпна, версия ');
DEFINE('_KUNENA_GD_NO_VERSION','Версията на GD не може да се извлече');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD не е инсталирана, за повече информация ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Височина на малко изображение:');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Ширина на малко изображение:');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Височина на средно изображение:');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','Ширина на средно изображение:');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Височина на голямо изображение:');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Ширина на голямо изображение:');
DEFINE('_KUNENA_AVATAR_QUALITY','Качество на аватара');
DEFINE('_KUNENA_WELCOME','Добре дошли във FireBoard!');
DEFINE('_KUNENA_WELCOME_DESC','Благодарим Ви, че избрахте FireBoard за Вашия сайт. Този екран ще Ви даде бърз поглед върху основните статистики на Вашия форум. Връзките отляво Ви позволяват да контролирате целия форум. Всяка страница съдържа инструкции как да се ползват различните инструменти.');
DEFINE('_KUNENA_STATISTIC','Статистика');
DEFINE('_KUNENA_VALUE','Стойност');
DEFINE('_GEN_CATEGORY','Категория');
DEFINE('_GEN_STARTEDBY','Започната от: ');
DEFINE('_GEN_STATS','статистика');
DEFINE('_STATS_TITLE','Статистика за форума');
DEFINE('_STATS_GEN_STATS','Обща статистика');
DEFINE('_STATS_TOTAL_MEMBERS','Потребители:');
DEFINE('_STATS_TOTAL_REPLIES','Отговори:');
DEFINE('_STATS_TOTAL_TOPICS','Теми:');
DEFINE('_STATS_TODAY_TOPICS','Теми днес:');
DEFINE('_STATS_TODAY_REPLIES','Отговори днес:');
DEFINE('_STATS_TOTAL_CATEGORIES','Категории:');
DEFINE('_STATS_TOTAL_SECTIONS','Секции:');
DEFINE('_STATS_LATEST_MEMBER','Последно регистриран:');
DEFINE('_STATS_YESTERDAY_TOPICS','Теми вчера:');
DEFINE('_STATS_YESTERDAY_REPLIES','Отговори вчера:');
DEFINE('_STATS_POPULAR_PROFILE','Топ 10 потребители <br />(по брой преглеждания на профила им)');
DEFINE('_STATS_TOP_POSTERS','Топ 10 потребители <br />(по брой мнения)');
DEFINE('_STATS_POPULAR_TOPICS','Най-популярни мнения');
DEFINE('_COM_A_STATSPAGE','Разреши старницата със статистика');
DEFINE('_COM_A_STATSPAGE_DESC','Ако е &quot;Да&quot; в заглавното меню ще има връзка към страницата със статистика за форума. <em>Тази страница е винаги видима за администраторите независимо от тази настройка!</em>');
DEFINE('_COM_C_JBSTATS','Статистика');
DEFINE('_COM_C_JBSTATS_DESC','Статистика, свързана с форума');
DEFINE('_GEN_GENERAL','Общо');
DEFINE('_PERM_NO_READ','Нямате нужните права за достъп до този форум.');
DEFINE('_KUNENA_SMILEY_SAVED','Усмивката е записана');
DEFINE('_KUNENA_SMILEY_DELETED','Усмивката е изтрита');
DEFINE('_KUNENA_CODE_ALLREADY_EXITS','Кодът вече съществува');
DEFINE('_KUNENA_MISSING_PARAMETER','Липсващ параметър');
DEFINE('_KUNENA_RANK_ALLREADY_EXITS','Рангът вече съществува');
DEFINE('_KUNENA_RANK_DELETED','Рангът е изтрит');
DEFINE('_KUNENA_RANK_SAVED','Рангът е успешно записан');
DEFINE('_KUNENA_DELETE_SELECTED','Избрано е изтриване');
DEFINE('_KUNENA_MOVE_SELECTED','Избрано е преместване');
DEFINE('_KUNENA_REPORT_LOGGED','Записано в лога');
DEFINE('_KUNENA_GO','Търси');
DEFINE('_KUNENA_MAILFULL','Включи цялото мнение в е-мейла, който се изпраща до администратора');
DEFINE('_KUNENA_MAILFULL_DESC','Ако е &quot;Не&quot; - абонатите ще получачават само заглавията на новите съобщения');
DEFINE('_KUNENA_HIDETEXT','Моля, влезте за да видите това съдържание!');
DEFINE('_BBCODE_HIDE','Скрит текст: [hide]някъкъв скрит текст[/hide] - скриване на част от текста за нерегистрираните.');
DEFINE('_KUNENA_FILEATTACH','Прикачен файл: ');
DEFINE('_KUNENA_FILENAME','Име на файл: ');
DEFINE('_KUNENA_FILESIZE','Размер на файл: ');
DEFINE('_KUNENA_MSG_CODE','Код: ');
DEFINE('_KUNENA_CAPTCHA_ON','Защита от спам');
DEFINE('_KUNENA_CAPTCHA_DESC','Включване на системата CAPTCHA срещу спам');
DEFINE('_KUNENA_CAPDESC','Въведете код');
DEFINE('_KUNENA_CAPERR','Грешен код!');
DEFINE('_KUNENA_COM_A_REPORT', 'Сигнализиране');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Могат ли потребителите да сигнализират за неподходящи съобщения до модератора.');
DEFINE('_KUNENA_REPORT_MSG', 'Съобщението е изпратено');
DEFINE('_KUNENA_REPORT_REASON', 'Причина');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Вашето съобщение');
DEFINE('_KUNENA_REPORT_SEND', 'Изпрати сигнал');
DEFINE('_KUNENA_REPORT', 'Сигнал до модератора');
DEFINE('_KUNENA_REPORT_RSENDER', 'Докладвай: ');
DEFINE('_KUNENA_REPORT_RREASON', 'Причина за сигнала: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Сигнализирай за съобщение: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Автор на съобщението: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Тема на съобщението: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Съобщение: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Връзка към съобщението: ');
DEFINE('_KUNENA_REPORT_INTRO', 'Ви изпрати съощение, защото');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Сигналът е успешно изпратен!');
DEFINE('_KUNENA_EMOTICONS', 'Емоции');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Усмивка');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Код');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Редактирай усмивка');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Редактиране на усмивките');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Лента с емотикони');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Нова усмивка');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Още усмивки');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Затвори прозореца');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Допълнителни емоции');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Избери усмивка');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Поддръжка на Joomla Mambot');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Позволи смяна на потребителското име');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Позволява смяна на потребителското име (username) в страницата с профила');
DEFINE ('_KUNENA_RECOUNTFORUMS','Нулиране на статистиката');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Всички броячи в статистиката са нулирани.');
DEFINE ('_KUNENA_EDITING_REASON','Причина за редактиране');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Последна редакция');
DEFINE ('_KUNENA_BY','от');
DEFINE ('_KUNENA_REASON','Причина');
DEFINE('_GEN_GOTOBOTTOM', 'Отиди най-отдолу');
DEFINE('_GEN_GOTOTOP', 'Отиди най-отгоре');
DEFINE('_STAT_USER_INFO', 'Информация за потребителя');
DEFINE('_USER_SHOWEMAIL', 'Показвай е-мейл');
DEFINE('_USER_SHOWONLINE', 'Покажи активен/неактивен');
DEFINE('_KUNENA_HIDDEN_USERS', 'Скрити потребители');
DEFINE('_KUNENA_SAVE', 'Запиши');
DEFINE('_KUNENA_RESET', 'Нулиране');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Галерия по подразбиране');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Лична информация');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Преглед');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Моят аватар');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Настройки на форума');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Изглед и подредба');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Моят профил');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Моите отговори');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Моите абонаменти');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Моите любими');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Лични съобщения');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Входящи');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Ново съобщение');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Изходящи');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Кошче');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Настройки');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Контакти');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Списък блокирани');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Допълнителна информация');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Име');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Потребителско име');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Е-мейл');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Тип потребител');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Дата на регистрация');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Последно влизане');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Отговори');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Преглеждания на профила');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Личен текст');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Пол');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Рожденна дата');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'година (YYYY) - месец (MM) - ден (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Място');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Вашият ICQ номер.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Вашето AOL Instant Messenger име.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Вашето Yahoo! Instant Messenger име.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Вашето Skype име.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Вашето Gtalk име.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Сайт');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Име на сайта');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Примерно: Стрелба с лък');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL на сайта');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Примерно: archerybg.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'E-mail');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Вашият е-мейл.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Подпис');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Мъж');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Жена');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Темите и отговорите бяха изтрити успешно');
DEFINE('_KUNENA_DATE_YEAR', 'година(и)');
DEFINE('_KUNENA_DATE_MONTH', 'месец');
DEFINE('_KUNENA_DATE_WEEK','седмица');
DEFINE('_KUNENA_DATE_DAY', 'ден');
DEFINE('_KUNENA_DATE_HOUR', 'час');
DEFINE('_KUNENA_DATE_MINUTE', 'минута');
DEFINE('_KUNENA_IN_FORUM', ' във форум: ');
DEFINE('_KUNENA_FORUM_AT', ' Форум на: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'код и усмивки са позволени.');

// 1.0.1

DEFINE ('_KUNENA_FORUMTOOLS','Бързи връзки');

//userlist

DEFINE ('_KUNENA_USRL_USERLIST','Списък на потребителите');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s има <b>%d</b> регистриран(и) потребители.');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Моля въведете параметри на търсенето!');
DEFINE ('_KUNENA_USRL_SEARCH','Търсене на потребител');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Търсене');
DEFINE ('_KUNENA_USRL_LIST_ALL','Покажи всички');
DEFINE ('_KUNENA_USRL_NAME','Име');
DEFINE ('_KUNENA_USRL_USERNAME','Потребителско име');
DEFINE ('_KUNENA_USRL_GROUP','Група');
DEFINE ('_KUNENA_USRL_POSTS','Мнения');
DEFINE ('_KUNENA_USRL_KARMA','Карма');
DEFINE ('_KUNENA_USRL_HITS','Преглеждания');
DEFINE ('_KUNENA_USRL_EMAIL','E-мейл');
DEFINE ('_KUNENA_USRL_USERTYPE','Тип потребител');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Дата на регистрация');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Последно влизане');
DEFINE ('_KUNENA_USRL_NEVER','Никога');
DEFINE ('_KUNENA_USRL_ONLINE','Статус');
DEFINE ('_KUNENA_USRL_AVATAR','Снимка');
DEFINE ('_KUNENA_USRL_ASC','Възходящо');
DEFINE ('_KUNENA_USRL_DESC','Низходящо');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Покажи');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%y %H:%M');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Приложения');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Списък на потребителите');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Брой редове с потребители');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Брой редове');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Статус активност');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Показва статуса на потребителите (активен/неактивен)');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Покажи снимка');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Покажи име');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Покажи потребителско име');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Покажи потребителска група');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Покажи брой на мненията');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Покажи карма');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Покажи е-мейл');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Покажи типа на потребителя');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Покажи датата на включване');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Покажи последната дата на влизане');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Преглеждания на профила');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Fireboard съветник');
DEFINE('_KUNENA_DBMETHOD', 'Моля,изберете метод за завършване на инсталацията:');
DEFINE('_KUNENA_DBCLEAN', 'Чиста инсталация');
DEFINE('_KUNENA_DBUPGRADE', 'Обновяване на Fireboard');
DEFINE('_KUNENA_TOPLEVEL', 'Категория');
DEFINE('_KUNENA_REGISTERED', 'Регистриран');
DEFINE('_KUNENA_PUBLICBACKEND', 'Публична администрация ');
DEFINE('_KUNENA_SELECTANITEMTO', 'Изберете точка от списъка');
DEFINE('_KUNENA_ERRORSUBS', 'Появи се грешка при изтриване на съобщенията и абонаментите');
DEFINE('_KUNENA_WARNING', 'Предупреждение...');
DEFINE('_KUNENA_CHMOD1', 'Трябва да смените правата на файла на 766 (chmod 766) за да може .');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Вашият конфигурационен файл е:');
DEFINE('_KUNENA_FIREBOARD', 'Fireboard');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Избор на шаблон');
DEFINE('_KUNENA_CFNW', 'Грешка: Конфигурационния файл няма права за запис.');
DEFINE('_KUNENA_CFS', 'Конфигурационния файл е записан.');
DEFINE('_KUNENA_CFCNBO', 'Грешка: Избраният файл не може да бъде отворен.');
DEFINE('_KUNENA_TFINW', 'Този файл е без права за запис.');
DEFINE('_KUNENA_KUNENACFS', 'Fireboard CSS файлът е записан.');
DEFINE('_KUNENA_SELECTMODTO', 'Изберете модератор за');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Трябва да изберете форум за прочистване!');
DEFINE('_KUNENA_DELMSGERROR', 'Изтриването на съобщенията невъзможно:');
DEFINE('_KUNENA_DELMSGERROR1', 'Изтриването на текста на съобщенията невъзможно:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Изчистването на абонаментите - неуспешно:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Форумът е изчистен за');
DEFINE('_KUNENA_PRUNEDAYS', 'дена');
DEFINE('_KUNENA_PRUNEDELETED', 'Изтрито:');
DEFINE('_KUNENA_PRUNETHREADS', 'нишки');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Грешка при изчистване на потребители:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Потребителите са изчистени; Изтрити:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'потребителски профили');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Няма открити профили подлежащи на изчистване.');
DEFINE('_KUNENA_TABLESUPGRADED', 'Таблиците на Fireboard са обновени до версия');
DEFINE('_KUNENA_FORUMCATEGORY', 'Категория на форум');
DEFINE('_KUNENA_SAMPLWARN1', 'Уверете се, че зареждате примерни данни върху напълно празен kunena. Ако вече има въведени някакви данни, това няма да работи!');
DEFINE('_KUNENA_FORUM1', 'Форум 1 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Примерно мнение 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Примерен форум 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Примерено мнение[/color][/size][/b]\nПоздравления за Вашия нов Форум!\n\n[url=http://bestofjoomla.com]- Best of Joomla[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'Примерните данни са заредени.');
DEFINE('_KUNENA_SAMPLEREMOVED', 'Примерните данни са премахнати.');
DEFINE('_KUNENA_CBADDED', 'Community Builder профила е доавен.');
DEFINE('_KUNENA_IMGDELETED', 'Изображението е изтрито.');
DEFINE('_KUNENA_FILEDELETED', 'Файлът е изтрит');
DEFINE('_KUNENA_NOPARENT', 'Няма по-горен');
DEFINE('_KUNENA_DIRCOPERR', 'Грешка: Файл');
DEFINE('_KUNENA_DIRCOPERR1', 'could not be copied!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Fireboard Forum</strong> Component <em>for Joomla! CMS</em> <br />&copy; 2006 - 2007 by Best Of Joomla<br>All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Внасяне/Инсталиране: </code></strong><br /><br /><font color="red"><b>успешно</b>');

// added by aliyar 

DEFINE('_KUNENA_FORUMPRF_TITLE', 'Настройки на профилите');
DEFINE('_KUNENA_FORUMPRF', 'Профил');
DEFINE('_KUNENA_FORUMPRRDESC', 'Ако имате инсталирани компонентите Clexus PM или Community Builder, Вие можете да настроите Kunena да използва страницата с потребителски профили.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Профил');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Профил</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Всички съобщения от форума');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Теми');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Започнат от');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Категории');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Дата');
DEFINE('_KUNENA_USERPROFILE_HITS', 'Прегледи');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Няма мнение във форума');
DEFINE('_KUNENA_TOTALFAVORITE', 'Любими:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Брой подчинени колони  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Брой на подфорумите column formating under main category ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Отметката &quot;Абонамент&quot; да е винаги включена?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Ако е &quot;Да&quot; отметката за &quot;Абонамент&quot; ще излиза включена във формата за писане.');

// Errors (Re-integration from Joomlaboard 1.2)

DEFINE('_KUNENA_ERROR1', 'Не сте избрали име за форума!');

// Forum Configuration (New in Kunena)

DEFINE('_KUNENA_SHOWSTATS', 'Покажи статистика');
DEFINE('_KUNENA_SHOWSTATSDESC', 'Изберете &quot;Да&quot; за показване статистика на форума');
DEFINE('_KUNENA_SHOWWHOIS', 'Покажи списък с активни потребители');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Изберете &quot;Да&quot; за показване на списъкът с активни потребители');
DEFINE('_KUNENA_STATSGENERAL', 'Покажи общи статистики');
DEFINE('_KUNENA_STATSGENERALDESC', 'Ако искате да се показват общи статистики, изберете &quot;Да&quot;');
DEFINE('_KUNENA_USERSTATS', 'Покажи статистика на редовните потребители');
DEFINE('_KUNENA_USERSTATSDESC', '');
DEFINE('_KUNENA_USERNUM', 'Брой популярни потребители');
DEFINE('_KUNENA_USERPOPULAR', 'Покажи статистика за популярните теми');
DEFINE('_KUNENA_USERPOPULARDESC', '');
DEFINE('_KUNENA_NUMPOP', 'Брой популярни теми');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.5. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Инструкции');
DEFINE('_KUNENA_FINFO', 'Информация за Fireboard');
DEFINE('_KUNENA_CSSEDITOR', 'Редактор за CSS файла на Fireboard Template');
DEFINE('_KUNENA_PATH', 'Път до файл:');
DEFINE('_KUNENA_CSSERROR', 'Важно: CSS.');

// User Management

DEFINE('_KUNENA_FUM', 'Kunena User Profile Manager');
DEFINE('_KUNENA_SORTID', 'сортиране по номер');
DEFINE('_KUNENA_SORTMOD', 'сортиране по модератор');
DEFINE('_KUNENA_SORTNAME', 'сортиране по име');
DEFINE('_KUNENA_VIEW', 'Изглед');
DEFINE('_KUNENA_NOUSERSFOUND', 'Няма открит потребителски профил.');
DEFINE('_KUNENA_ADDMOD', 'Добави модератор за');
DEFINE('_KUNENA_NOMODSAV', 'Няма открити модератори. Виж забележката по-долу.');
DEFINE('_KUNENA_NOTEUS','Забележка: Само потребители, които в профила си са с модераторски флаг са показани тук. За да добавите модератор отидете на <a href="index2.php?option=com_kunena&task=profiles">Управление на потребители</a>. След това обновете профила. Модератори може да създава/премахва само администартор.');
DEFINE('_KUNENA_PROFFOR', 'Профил на');
DEFINE('_KUNENA_GENPROF', 'Общи настройки на профила');
DEFINE('_KUNENA_PREFVIEW', 'Предпочитан изглед:');
DEFINE('_KUNENA_PREFOR', 'Предпочитана подреба на съобщенията:');
DEFINE('_KUNENA_ISMOD', 'Модератор?:');
DEFINE('_KUNENA_ISADM', '<strong>Да</strong> (не може да се промени, този потребител е (супер)администратор)');
DEFINE('_KUNENA_COLOR', 'Цвят');
DEFINE('_KUNENA_UAVATAR', 'Потребителска снимка:');
DEFINE('_KUNENA_NS', 'Няма избрана');
DEFINE('_KUNENA_DELSIG', ' поставете отметка за да изтриете подписа');
DEFINE('_KUNENA_DELAV', ' поставете отметка за да изтриете снимката');
DEFINE('_KUNENA_SUBFOR', 'Абонаменти за');
DEFINE('_KUNENA_NOSUBS', 'Този потребител няма абонаменти');

// Forum Administration (Re-integration from Joomlaboard 1.2)

DEFINE('_KUNENA_BASICS', 'Основни');
DEFINE('_KUNENA_BASICSFORUM', 'Основна информация за форума');
DEFINE('_KUNENA_PARENT', 'Горен:');
DEFINE('_KUNENA_PARENTDESC','Заб.: За да създадете категория, изберете \'Top Level Category\' като горен. Една категория може да съдържа няколко форума.<br />И форум може да бъде създаден <strong>само</strong> в категория, като изберете вече създадена категория за родителска.<br />Мнения/отговори/ <strong>НЕ МОГАТ</strong> да бъдат публикувани в категория, а само във форум.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Описание и име на форума');
DEFINE('_KUNENA_NAMEADD', 'Име:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Описание:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Допълнителни настройки на форума');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Сигурност и достъп');
DEFINE('_KUNENA_LOCKEDDESC', 'Задайте на &quot;Да&quot;, ако искате този форум да бъде заключен и само администратори и модератори да могат да създават нови теми и/или отговори (или да местят тук).');
DEFINE('_KUNENA_LOCKED1', 'Заключен:');
DEFINE('_KUNENA_PUBACC', 'Ниво на публичен достъп:');
DEFINE('_KUNENA_PUBACCDESC','За да създадете форум, който да е достъпен само за определн кръг потребители, трябва да определите минималното ниво на достъп тук. По подразбиране то е: &quot;Всички&quot;.<br /><b>Забележка</b>: Ако ограничите достъпа на цяла категория до една или няколко групи, това ще скрие всички съдържащи се в нея форуми за всички, които нямат нужните права <b>even</b> if one or more of these Forums have a lower access level set! This holds for Moderators too; you will have to add a Moderator to the moderator list of the Category if (s)he does not have the proper group level to see the Category.<br /> This is irrespective of the fact that Categories can not be Moderated; Moderators can still be added to the moderator list.');
DEFINE('_KUNENA_CGROUPS', 'Включи подгрупите:');
DEFINE('_KUNENA_CGROUPSDESC', 'Да се позволи ли достъп до подгрупите? Ако e &quot;Не&quot;, достъпът до този форум ще бъде ограничен до избрани групи');
DEFINE('_KUNENA_ADMINLEVEL', 'Ниво на администраторски достъп:');
DEFINE('_KUNENA_ADMINLEVELDESC','Ако създадете форум ограничен за публичен достъп, тук определете допълнителното ниво за администраторски достъп.<br />Ако ограничите достъпа до форума до ограничена потребителска група и не определите групата тук, администраторите няма да могат да влизат/преглеждат форума.');
DEFINE('_KUNENA_ADVANCED', 'Допълнителни');
DEFINE('_KUNENA_CGROUPS1', 'Включване на стъпаловидни групи:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Да се позволи ли достъп и до под-форумите? Ако е &quot;Не &quot; достъпът до този форум е ограничен <b>само</b> до избраната група.');
DEFINE('_KUNENA_REV', 'Одобрение от модератор:');
DEFINE('_KUNENA_REVDESC','Изберете &quot;Да&quot; за да включите одобрение от модератор, преди мнението да бъде публикувано. Тази функция е активна само за форуми,които се модерират!<br />Ако я включите, а нямате модератори, администратора на сайта е единствено отговорен за одобрението/изтриването на пуснатите мнения, които ще стоят като &quot;чакащи&quot;!');
DEFINE('_KUNENA_MOD_NEW', 'Модериране');
DEFINE('_KUNENA_MODNEWDESC', 'Модериране и модератори');
DEFINE('_KUNENA_MOD', 'сортиране по модератори');
DEFINE('_KUNENA_MODDESC','Изберете &quot;Да&quot; ако желаете да добавите модератори в този форум.<br /><strong>Заб.:</strong> Това не означава, че новите мнения трябва да бъдат прегледани преди да се публикуват във форума!<br /> Трябва да включите настройката &quot;Преглед&quot; в таба &quot;Допълнителни&quot;.<br /><br /> <strong>Важна забележка:</strong> След включване &quot;Модериране&quot; на &quot;Да&quot; трябва първо да запазите конфигурацията и след това ще можете да добавяте модератори.');
DEFINE('_KUNENA_MODHEADER', 'Права на модераторите за този форум');
DEFINE('_KUNENA_MODSASSIGNED', 'Модератори на форума:');
DEFINE('_KUNENA_NOMODS', 'Няма определени модератори за този форум.');

// Some General Strings (Improvement in Kunena)

DEFINE('_KUNENA_EDIT', 'Промяна');
DEFINE('_KUNENA_ADD', 'Добавияне');

// Reorder (Re-integration from Joomlaboard 1.2)

DEFINE('_KUNENA_MOVEUP', 'Нагоре');
DEFINE('_KUNENA_MOVEDOWN', 'Надолу');

// Groups - Integration in Kunena

DEFINE('_KUNENA_ALLREGISTERED', 'Само регистрирани');
DEFINE('_KUNENA_EVERYBODY', 'Всички');

// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)

DEFINE('_KUNENA_REORDER', 'Подредба');
DEFINE('_KUNENA_CHECKEDOUT', 'Заключено');
DEFINE('_KUNENA_ADMINACCESS', 'Достъп на администраторите');
DEFINE('_KUNENA_PUBLICACCESS', 'Достъп на всички');
DEFINE('_KUNENA_PUBLISHED', 'Публикуван');
DEFINE('_KUNENA_REVIEW', 'Преглед');
DEFINE('_KUNENA_MODERATED', 'Модериран');
DEFINE('_KUNENA_LOCKED', 'Заключен');
DEFINE('_KUNENA_CATFOR', 'Категория / Форум');
DEFINE('_KUNENA_ADMIN', '&nbsp;?Kunena администрация');
DEFINE('_KUNENA_CP', 'Контролен панел на Fireboard');

// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)

DEFINE('_COM_A_AVATAR_INTEGRATION', 'Интеграция на аватарите');
DEFINE('_COM_A_RANKS_SETTINGS', 'Рангове');
DEFINE('_COM_A_RANKING_SETTINGS', 'Настройка на ранговете');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Настройка на аватарите');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Настройки по сигурността');
DEFINE('_COM_A_BASIC_SETTINGS', 'Основни настройки');

// Kunena 1.0.0

//
DEFINE('_COM_A_FAVORITES','Разрешаване на &quot;любими теми&quot;');
DEFINE('_COM_A_FAVORITES_DESC','Изберете &quot;ДА&quot; регистрираните потребители да маркират тема като любима.');
DEFINE('_USER_UNFAVORITE_ALL','Изберете това поле за да <b><u>премахнете от фаворити</u></b> за всички теми (включително и невидимите)');
DEFINE('_VIEW_FAVORITETXT','Добавяне в любими ');
DEFINE('_USER_UNFAVORITE_YES','Вие премахнахте темата от любими');
DEFINE('_POST_FAVORITED_TOPIC','Мнението е добавено в любими.');
DEFINE('_VIEW_UNFAVORITETXT','Любима тема');
DEFINE('_VIEW_UNSUBSCRIBETXT','Абонамент');
DEFINE('_USER_NOFAVORITES','Няма любими теми');
DEFINE('_POST_SUCCESS_FAVORITE','Темата е добавена в любими.');
DEFINE('_COM_A_MESSAGES_SEARCH','Резултати от търсенето');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH','Брой резултати от търсенето на страница.');
DEFINE('_KUNENA_USE_JOOMLA_STYLE','Да използва ли Joomla стил?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC','Ако искате да използвате стил Joomla, изберете ДА. (class: като sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST','Показва икони във форума');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC','Ако искате да показва икони във форума, изберете ДА. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT','Показва анонс');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC','Изберете &quot;ДА&quot; , ако искате да показва поле с анонс за форума.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT','Показва аватарите в списъка с категории?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC','Изберете &quot;Да&quot; , ако искате да показва потребителските коментари в списъка с категории.');
DEFINE('_KUNENA_RECENT_POSTS','Настройки за последните мнения');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES','Последни мнения');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC','Изберете &quot;Да&quot; ако искате да показва последните мнения във Вашия форум');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES','Брой последни мнения');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC','Брой последни мнения които да показва');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES','Брой за таб ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC','Брой мнения за таб');
DEFINE('_KUNENA_LATEST_CATEGORY','Избрана категория');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC','Изберете категория(и) от които да показва последните мнения. Пример:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT','Показва темата');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC','');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT','Показва отговор към тема');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC','');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH','Дължина на заглавието');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC','');
DEFINE('_KUNENA_SHOW_LATEST_DATE','Показва дата');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC','');
DEFINE('_KUNENA_SHOW_LATEST_HITS','Показва прегледа');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC','');
DEFINE('_KUNENA_SHOW_AUTHOR','Показва автор');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC','1=потребителско име, 2=истинско име, 0=няма');
DEFINE('_KUNENA_STATS','Настройки на статистиката ');
DEFINE('_KUNENA_CATIMAGEPATH','Път към иконите за категория ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC','Ако сте избрали "category_images/", целият път ще бъде "components/com_kunena/category_images/"');
DEFINE('_KUNENA_ANN_MODID','Добавя ID на модераторите ');
DEFINE('_KUNENA_ANN_MODID_DESC','Добавя потребителските id за анонсите. пр. 62,63,73 . Анонсиращите модератори, могат да трият, променят и публикуват анонси.');

//

DEFINE('_KUNENA_FORUM_TOP','Категории ');
DEFINE('_KUNENA_CHILD_BOARDS','Подкатегории');
DEFINE('_KUNENA_QUICKMSG','Бърз отговор ');
DEFINE('_KUNENA_THREADS_IN_FORUM','Теми във форума');
DEFINE('_KUNENA_FORUM','Форум ');
DEFINE('_KUNENA_SPOTS','Spotlights');
DEFINE('_KUNENA_CANCEL','отказ');
DEFINE('_KUNENA_TOPIC','ТЕМА: ');
DEFINE('_KUNENA_POWEREDBY','Създадено с ');

// Time Format

DEFINE('_TIME_TODAY','<b>Днес</b> ');
DEFINE('_TIME_YESTERDAY','<b>Вчера</b> ');

//  STARTS HERE!

DEFINE('_KUNENA_WHO_LATEST_POSTS','Последни мнения');
DEFINE('_KUNENA_WHO_WHOISONLINE','Кой е на линия');
DEFINE('_KUNENA_WHO_MAINPAGE','Начало на форума');
DEFINE('_KUNENA_GUEST','Гост');
DEFINE('_KUNENA_PATHWAY_VIEWING','разглежда(т)');
DEFINE('_KUNENA_ATTACH','Прикачено');

// Favorite

DEFINE('_KUNENA_FAVORITE','Любима тема');
DEFINE('_USER_FAVORITES','Вашите любими теми');
DEFINE('_THREAD_UNFAVORITE','Премахване от любими теми');

// profilebox

DEFINE('_PROFILEBOX_WELCOME','Добре дошли');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS','Последни мнения');
DEFINE('_PROFILEBOX_SET_MYAVATAR','Избор на аватар');
DEFINE('_PROFILEBOX_MYPROFILE','Профил');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS','Моите мнения');
DEFINE('_PROFILEBOX_GUEST','Гост');
DEFINE('_PROFILEBOX_LOGIN','влезте');
DEFINE('_PROFILEBOX_REGISTER','се регистрирайте');
DEFINE('_PROFILEBOX_LOGOUT','Изход');
DEFINE('_PROFILEBOX_LOST_PASSWORD','Забравена парола?');
DEFINE('_PROFILEBOX_PLEASE','Моля');
DEFINE('_PROFILEBOX_OR','или');

// recentposts

DEFINE('_RECENT_RECENT_POSTS','Последни мнения');
DEFINE('_RECENT_TOPICS','Тема');
DEFINE('_RECENT_AUTHOR','Автор');
DEFINE('_RECENT_CATEGORIES','Категории');
DEFINE('_RECENT_DATE','Дата');
DEFINE('_RECENT_HITS','Видяно');

// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'СЪОБЩЕНИЯ');
DEFINE('_ANN_ID','ID');
DEFINE('_ANN_DATE','Дата');
DEFINE('_ANN_TITLE','Заглавие');
DEFINE('_ANN_SORTTEXT','Текст за сортиране');
DEFINE('_ANN_LONGTEXT','Дълъг текст');
DEFINE('_ANN_ORDER','Ред');
DEFINE('_ANN_PUBLISH','Публикуване');
DEFINE('_ANN_PUBLISHED','Публикувано');
DEFINE('_ANN_UNPUBLISHED','Скрито');
DEFINE('_ANN_EDIT','Промяна');
DEFINE('_ANN_DELETE','Изтриване');
DEFINE('_ANN_SUCCESS','Успешно');
DEFINE('_ANN_SAVE','Запис');
DEFINE('_ANN_YES','Да');
DEFINE('_ANN_NO','Не');
DEFINE('_ANN_ADD','Ново съобщение');
DEFINE('_ANN_SUCCESS_EDIT','Успешна промяна');
DEFINE('_ANN_SUCCESS_ADD','Успешно добавено');
DEFINE('_ANN_DELETED','Успешно изтрито');
DEFINE('_ANN_ERROR','ГРЕШКА!');
DEFINE('_ANN_READMORE','още...');
DEFINE('_ANN_CPANEL','Контролен панел за новини');
DEFINE('_ANN_SHOWDATE','Показва дата');

// Stats

DEFINE('_STAT_FORUMSTATS','Статистики');
DEFINE('_STAT_GENERAL_STATS','Общи статистики');
DEFINE('_STAT_TOTAL_USERS','Общо потребители');
DEFINE('_STAT_LATEST_MEMBERS','Последно регистриран');
DEFINE('_STAT_PROFILE_INFO','Покажи инфо за профила');
DEFINE('_STAT_TOTAL_MESSAGES','Общо мнения');
DEFINE('_STAT_TOTAL_SUBJECTS','Общо теми');
DEFINE('_STAT_TOTAL_CATEGORIES','Общо категории');
DEFINE('_STAT_TOTAL_SECTIONS','Общо секции');
DEFINE('_STAT_TODAY_OPEN_THREAD','Започнати днес');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD','Започнати вчера');
DEFINE('_STAT_TODAY_TOTAL_ANSWER','Общо отговори за днес');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER','Общо отговори за вчера');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM','Преглед на последните мнения');
DEFINE('_STAT_MORE_ABOUT_STATS','Още статистики');
DEFINE('_STAT_USERLIST','Списък с потребители');
DEFINE('_STAT_TEAMLIST','Екип');
DEFINE('_STATS_FORUM_STATS','Статистики');
DEFINE('_STAT_POPULAR','Популярно');
DEFINE('_STAT_POPULAR_USER_TMSG','Потребители (Общо мнения) ');
DEFINE('_STAT_POPULAR_USER_KGSG','Теми ');
DEFINE('_STAT_POPULAR_USER_GSG','Потребители (Общо прегледа на профили) ');

//Team List

DEFINE('_MODLIST_ONLINE','Активни потребители: ');
DEFINE('_MODLIST_OFFLINE','Изключен');

// Whoisonline

DEFINE('_WHO_WHOIS_ONLINE','Кой е на линия');
DEFINE('_WHO_ONLINE_NOW','На линия:');
DEFINE('_WHO_ONLINE_MEMBERS','потребители');
DEFINE('_WHO_AND','и');
DEFINE('_WHO_ONLINE_GUESTS','гости');
DEFINE('_WHO_ONLINE_USER','Потребител');
DEFINE('_WHO_ONLINE_TIME','Час');
DEFINE('_WHO_ONLINE_FUNC','Действие');

// Userlist

DEFINE('_USRL_USERLIST','Потребителски списък');
DEFINE('_USRL_REGISTERED_USERS','%s има <b>%d</b> регистрирани потребители');
DEFINE('_USRL_SEARCH_ALERT','Моля въведете стойност на търсене!');
DEFINE('_USRL_SEARCH','Търсене на потребител');
DEFINE('_USRL_SEARCH_BUTTON','Търсене');
DEFINE('_USRL_LIST_ALL','Показва всички');
DEFINE('_USRL_NAME','Име');
DEFINE('_USRL_USERNAME','Потребител');
DEFINE('_USRL_EMAIL','Е-мейл');
DEFINE('_USRL_USERTYPE','Потр. тип');
DEFINE('_USRL_JOIN_DATE','Регистриран');
DEFINE('_USRL_LAST_LOGIN','Последен вход');
DEFINE('_USRL_NEVER','Никога');
DEFINE('_USRL_BLOCK','Статут');
DEFINE('_USRL_MYPMS2','MyPMS');
DEFINE('_USRL_ASC','Възходящо');
DEFINE('_USRL_DESC','Низходящо');
DEFINE('_USRL_DATE_FORMAT','%d.%m.%y %H:%M');
DEFINE('_USRL_TIME_FORMAT','%H:%M');
DEFINE('_USRL_USEREXTENDED','Детайли');
DEFINE('_USRL_COMPROFILER','Профил');
DEFINE('_USRL_THUMBNAIL','Снимка');
DEFINE('_USRL_READON','показва');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Изпрати ЛС');
DEFINE('_USRL_JIM', 'ЛС');
DEFINE('_USRL_UDDEIM', 'ЛС');
DEFINE('_USRL_SEARCHRESULT', 'Резултати за:');
DEFINE('_USRL_STATUS', 'Статус');
DEFINE('_USRL_LISTSETTINGS', 'Userlist Settings');
DEFINE('_USRL_ERROR', 'Грешка');

//changed in 1.1.4 stable

DEFINE('_COM_A_PMS_TITLE','Лични съобщения');
DEFINE('_COM_A_COMBUILDER_TITLE','Community Builder');
DEFINE('_FORUM_SEARCH','Търсене за: %s');
DEFINE('_MODERATION_DELETE_MESSAGE','Сигурни ли сте, че искате да изтриете това съобщение? Забележка: Не можете да възстановите съобщението след това!');
DEFINE('_MODERATION_DELETE_SUCCESS','Мненията бяха изтрити');
DEFINE('_COM_A_RANKING','Рангове');
DEFINE('_COM_A_BOT_REFERENCE','Покажи референциите на бота');
DEFINE('_COM_A_MOSBOT','Включване на бота за дискутиране');
DEFINE('_PREVIEW','преглед');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC','Ботът позволява статиите от съдържанието да бъдат дискутирани във форума. За повече информация, моля прочетете документацията на самия бот. Той трябва да бъде инсталиран отделно.' );

//new in 1.1.4 stable

// search.class.php

DEFINE('_FORUM_SEARCHTITLE','Търсене');
DEFINE('_FORUM_SEARCHRESULTS','Показани са %s от общо %s резултата.');

// Help, FAQ

DEFINE('_COM_FORUM_HELP','Въпроси');

// rules.php

DEFINE('_COM_FORUM_RULES','Правила');
DEFINE('_COM_FORUM_RULES_DESC','<h3 class="contentheading">Правила на форума</h3><ul><li>Редактирайте този текст във файла joomlaroot/administrator/components/com_kunena/language/bulgarian.php</li><li>Правило 2</li><li>Правило 3</li><li>Правило 4</li><li>...</li></ul>');

//smile.class.php

DEFINE('_COM_BOARDCODE','Код');

// moderate_messages.php

DEFINE('_MODERATION_APPROVE_SUCCESS','Мненията бяха одобрени');
DEFINE('_MODERATION_DELETE_ERROR','ГРЕШКА: Мнението не може да бъде изтрито');
DEFINE('_MODERATION_APPROVE_ERROR','ГРЕШКА: Мнението не може да бъде одобрено');

// listcat.php

DEFINE('_GEN_NOFORUMS','Няма форуми в тази категория!');

//new in 1.1.3 stable

DEFINE('_POST_GHOST_FAILED','Съобщението в стария форум не бе създадено!');
DEFINE('_POST_MOVE_GHOST','Оставете съобщение за преместването в сегашния форум');

//new in 1.1 Stable

DEFINE('_GEN_FORUM_JUMP','Отиди до форум');
DEFINE('_COM_A_FORUM_JUMP','Отиди до форум');
DEFINE('_COM_A_FORUM_JUMP_DESC','Изберете &quot;Да&quot; за да се показва падащо меню с бърза връзка към форумите.');

//new in 1.1 RC1

DEFINE('_GEN_RULES','Правила');
DEFINE('_COM_A_RULESPAGE','Правила');
DEFINE('_COM_A_RULESPAGE_DESC','Изберете &quot;Да&quot; за да се показват връзка към правилата на форума.');
DEFINE('_MOVED_TOPIC','ПРЕМЕСТЕНА:');
DEFINE('_COM_A_PDF','Ползване на PDF');
DEFINE('_COM_A_PDF_DESC','Изберете &quot;Да&quot; ако искате потребителите да мога да експортират мненията в PDF. Имайте предвид, че опцията не работи на кирилица.');
DEFINE('_GEN_PDFA','Натиснете за да създадете PDF документ.');
DEFINE('_GEN_PDF', 'PDF');

//new in 1.0.4 stable

DEFINE('_VIEW_PROFILE','Вижте профила на потребителя');
DEFINE('_VIEW_ADDBUDDY','Натиснете тук за да добавите този потребител в списъка с приятели');
DEFINE('_POST_SUCCESS_POSTED','Мнението бе публикувано успешно');
DEFINE('_POST_SUCCESS_VIEW','[ Обратно към мнението ]');
DEFINE('_POST_SUCCESS_FORUM','[ Обратно към форума ]');
DEFINE('_RANK_ADMINISTRATOR','Администратор');
DEFINE('_RANK_MODERATOR','Модератор');
DEFINE('_SHOW_LASTVISIT','След последното посещение');
DEFINE('_COM_A_BADWORDS_TITLE','Цензуриране на думи');
DEFINE('_COM_A_BADWORDS','Ползване на филтър за цензура');
DEFINE('_COM_A_BADWORDS_DESC','Изберете &quot;Да&quot; ако искате да включите интеграцията на компонента за цензура на лоши думи. За да ползвате тази функция, трябва да имате инсталиран компонента Badwords!');
DEFINE('_COM_A_BADWORDS_NOTICE','* Мнението е цензурирано защото съдържа неприлични думи *');
DEFINE('_COM_A_COMBUILDER_PROFILE','Създаване на полетата на форума в Community Builder');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC','');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK','Натиснете тук');
DEFINE('_COM_A_COMBUILDER','Профили в Community Builder');
DEFINE('_COM_A_COMBUILDER_DESC','Изберете &quot;Да&quot; за да интегрирате форума заедно с Community Builder. За да функционира правилно интеграцията натиснете еднократно бутона в ляво за създаване на необходимите полета в Community Builder.');
DEFINE('_COM_A_AVATAR_SRC','Използвай снимка от');
DEFINE('_COM_A_AVATAR_SRC_DESC','Ако имате инсталирани myPMS (Clexus) или Community Builder, можете да изберете да се показва снимката от тези компоненти.');
DEFINE('_COM_A_KARMA','Ползване на рангове');
DEFINE('_COM_A_KARMA_DESC','Изберете &quot;Да&quot; за да разрешите ранг за потребителите.');
DEFINE('_COM_A_DISEMOTICONS','Спиране на емоциите');
DEFINE('_COM_A_DISEMOTICONS_DESC','Изберете &quot;Да&quot; за да изключите напълно емоциите във форума.');
DEFINE('_COM_C_KUNENACONFIG','Настройки на Fireboard');
DEFINE('_COM_C_KUNENACONFIGDESC','Основните настройки на форума');
DEFINE('_COM_C_FORUM','Администрация на форумите');
DEFINE('_COM_C_FORUMDESC','Добавяне и управление на форуми и категории');
DEFINE('_COM_C_USER','Администрация на потребителите');
DEFINE('_COM_C_USERDESC','Основни настройки на потребителите и администрирането им');
DEFINE('_COM_C_FILES','Качени файлове');
DEFINE('_COM_C_FILESDESC','Управление на качените файлове');
DEFINE('_COM_C_IMAGES','Качени снимки');
DEFINE('_COM_C_IMAGESDESC','Управление на качените снимки');
DEFINE('_COM_C_CSS','Редакция на CSS файла');
DEFINE('_COM_C_CSSDESC','Променете изгледа на Джумлаборд');
DEFINE('_COM_C_SUPPORT','Официален уеб сайт');
DEFINE('_COM_C_SUPPORTDESC','Отворете официалния уеб сайт в нов прозорец');
DEFINE('_COM_C_PRUNETAB','Прочистване на форумите');
DEFINE('_COM_C_PRUNETABDESC','Изтрийте старите теми');
DEFINE('_COM_C_PRUNEUSERS','Прочистване на потребители');
DEFINE('_COM_C_PRUNEUSERSDESC','Синхронизирайте потребителите на форума със сайта');
DEFINE('_COM_C_LOADSAMPLE','Заредеждане на примерни данни');
DEFINE('_COM_C_LOADSAMPLEDESC','За да започнете лесно, заредете примерни данни във форума');
DEFINE('_COM_C_REMOVESAMPLE', 'Премахване на примерни данни');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'Премахва примерните данни от вашата база данни.');
DEFINE('_COM_C_LOADMODPOS', 'Зареждане позиции на модулите');
DEFINE('_COM_C_LOADMODPOSDESC', 'Зарежда позиции на модулите за шаблона на FireBoard');
DEFINE('_COM_C_UPGRADEDESC','Обновете базата си данни до текущата версия.');
DEFINE('_COM_C_BACK','Контролен панел');
DEFINE('_SHOW_LAST_SINCE','Активни теми след последното Ви посещение на:');
DEFINE('_POST_SUCCESS_REQUEST2','Вашете искане бе изпратено');
DEFINE('_POST_NO_PUBACCESS3','Натиснете за да се регистрирате.');

//==================================================================================================

//Changed in 1.0.4
//please update your local language file with these changes as well

DEFINE('_POST_SUCCESS_DELETE','Мнението бе изтрито успешно.');
DEFINE('_POST_SUCCESS_EDIT','Мнението бе редактирано успешно.');
DEFINE('_POST_SUCCESS_MOVE','Мнението бе преместено успешно.');
DEFINE('_POST_SUCCESS_POST','Мнението бе успешно публикувано.');
DEFINE('_POST_SUCCESS_SUBSCRIBE','Абонаментът е успешен.');

//==================================================================================================

//new in 1.0.3 stable
//Karma
DEFINE('_KARMA','Карма');
DEFINE('_KARMA_SMITE','Ужас');
DEFINE('_KARMA_APPLAUD','Браво');
DEFINE('_KARMA_BACK','За да се върнете в темата,');
DEFINE('_KARMA_WAIT','Можете да гласувате за определен потребител на всеки шест часа.');
DEFINE('_KARMA_SELF_DECREASE','Не се опитвайте да занижавате собствената си карма!');
DEFINE('_KARMA_SELF_INCREASE','Вашият ранг бе занижен, заради опита си да го повишите!');
DEFINE('_KARMA_DECREASED','Рангът бе занижен');
DEFINE('_KARMA_INCREASED','Рангът бе повишен,');
DEFINE('_COM_A_TEMPLATE','Шаблон');
DEFINE('_COM_A_TEMPLATE_DESC','Изберете кой шаблон да ползвате.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Набор иконки');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Изберете набор иконки за шаблона.');
DEFINE('_PREVIEW_CLOSE','Затвори');

//==========================================

//new in 1.0 Stable

DEFINE('_COM_A_POSTSTATSBAR','Показване на статистика чрез графика');
DEFINE('_COM_A_POSTSTATSBAR_DESC','Изберете &quot;Да&quot; ако искате броя на мненията да се показва чрез графика.');
DEFINE('_COM_A_POSTSTATSCOLOR','Цвят на статистиката');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC','Изберете в какъв цвят да се показва статистиката.');
DEFINE('_LATEST_REDIRECT','Нужно е да се идентифицират отново.');
DEFINE('_SMILE_COLOUR','Цвят');
DEFINE('_SMILE_SIZE','Размер');
DEFINE('_COLOUR_DEFAULT','Нормален');
DEFINE('_COLOUR_RED','Червен');
DEFINE('_COLOUR_PURPLE','Лилав');
DEFINE('_COLOUR_BLUE','Син');
DEFINE('_COLOUR_GREEN','Зелен');
DEFINE('_COLOUR_YELLOW','Жълт');
DEFINE('_COLOUR_ORANGE','Оранжев');
DEFINE('_COLOUR_DARKBLUE','Тъмно синьо');
DEFINE('_COLOUR_BROWN','Кафяво');
DEFINE('_COLOUR_GOLD','Златен');
DEFINE('_COLOUR_SILVER','Сребърен');
DEFINE('_SIZE_NORMAL','Нормален');
DEFINE('_SIZE_SMALL','Малък');
DEFINE('_SIZE_VSMALL','Много малък');
DEFINE('_SIZE_BIG','Голям');
DEFINE('_SIZE_VBIG','Много голям');
DEFINE('_IMAGE_SELECT_FILE','Изберете изображение');
DEFINE('_FILE_SELECT_FILE','Изберете файл');
DEFINE('_FILE_NOT_UPLOADED','Файлът не бе качен. Опитайте да редактирате мнението и да го добавите отново.');
DEFINE('_IMAGE_NOT_UPLOADED','Изображението не бе качено. Опитайте да редактирате мнението и да го добавите отново.');
DEFINE('_BBCODE_IMGPH','Добавете [img]в мнението за да добавите връзка към него');
DEFINE('_BBCODE_FILEPH','Добавете [file] в мнението за да добавите връзка към него');
DEFINE('_POST_ATTACH_IMAGE','[img]');
DEFINE('_POST_ATTACH_FILE','[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL','Спиране на всички абонаменти');
DEFINE('_LINK_JS_REMOVED','<em>Джава скрипт бе премахнат.</em>');

//==========================================

//new in 1.0 RC4

DEFINE('_COM_A_LOOKS','Изглед');
DEFINE('_COM_A_USERS','Потребители');
DEFINE('_COM_A_LENGTHS','Дължини');
DEFINE('_COM_A_SUBJECTLENGTH','Максимална дължина на заглавието');
DEFINE('_COM_A_SUBJECTLENGTH_DESC','Максимална дължина на заглавието на темата. За windows-1251 може да бъде до 255 символа, а за UTF-8 може да бъде до 63 символа.');
DEFINE('_LATEST_THREADFORUM','Тема/Форум');
DEFINE('_LATEST_NUMBER','Нови мнения');
DEFINE('_COM_A_SHOWNEW','Показване на нови мнения');
DEFINE('_COM_A_SHOWNEW_DESC','Изберете &quot;Да&quot; за да се показва индикатор за нови съобщения.');
DEFINE('_COM_A_NEWCHAR','Индикатор за &quot;Ново&quot;');
DEFINE('_COM_A_NEWCHAR_DESC','Изберете как да бъдат маркирани новите мнение (пример &quot;!&quot; или &quot;Ново!&quot;)');
DEFINE('_LATEST_AUTHOR','Последен автор');
DEFINE('_GEN_FORUM_NEWPOST','Има нови мнения след последното Ви посещение.');
DEFINE('_GEN_FORUM_NOTNEW','Няма нови мнения след последното Ви посещение.');
DEFINE('_GEN_UNREAD','Има нови отговори след последното Ви посещение.');
DEFINE('_GEN_NOUNREAD','Няма нови отговори след последното Ви посещение.');
DEFINE('_GEN_MARK_ALL_FORUMS_READ','Маркирай като прочетено');
DEFINE('_GEN_MARK_THIS_FORUM_READ','Маркирай като прочетено');
DEFINE('_GEN_FORUM_MARKED','Всички мнения в този форум бяха маркирани като прочетени');
DEFINE('_GEN_ALL_MARKED','Всички мнения бяха маркирани като прочетени');
DEFINE('_IMAGE_UPLOAD','Качване на изображение');
DEFINE('_IMAGE_DIMENSIONS','Максимални размери (широчина x височина)');
DEFINE('_IMAGE_ERROR_TYPE','Можете да качвате само jpeg, gif и png изображения');
DEFINE('_IMAGE_ERROR_EMPTY','Моля изберете файл');
DEFINE('_IMAGE_ERROR_SIZE','Размерът на изображението надхвърля максималния възможен.');
DEFINE('_IMAGE_ERROR_WIDTH','Широчината на изображението надхвърля максимума');
DEFINE('_IMAGE_ERROR_HEIGHT','Височината на изображението надхвърля максимума.');
DEFINE('_IMAGE_UPLOADED','Вашето изображение беше качено.');
DEFINE('_COM_A_IMAGE','Снимки');
DEFINE('_COM_A_IMGHEIGHT','Максимална широчина на изображенията');
DEFINE('_COM_A_IMGWIDTH','Максимална височинана изображенията');
DEFINE('_COM_A_IMGSIZE','Максимален размер на изображенията в килобайти (KB)');
DEFINE('_COM_A_IMAGEUPLOAD','Могат ли гостите да качват изображения?');
DEFINE('_COM_A_IMAGEUPLOAD_DESC','Изберете &quot;Да&quot; ако искате да разрешите на всички потребители да качват снимки към мненията.');
DEFINE('_COM_A_IMAGEREGUPLOAD','Могат ли регистрираните потребители да качват изображения?');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Изберете &quot;Да&quot; ако искате да разрешите на регистрираните потребители да добавят снимки към мненията. Администраторите и модераторите винаги имат тази възможност.');DEFINE('_COM_A_IMAGEREGUPLOAD_DESC','Set to &quot;Да&quot;, ако искате регистрираните потребители да могат да качват изображения.<br/> Note: (Супер)администраторите и модераторите винаги могат да качват изображения.');

//New since preRC4-II:

DEFINE('_COM_A_UPLOADS','Файлове');
DEFINE('_FILE_TYPES','Файлът може да бъде максимум ');
DEFINE('_FILE_ERROR_TYPE','Можете да качвате само файлове с разширение:\n');
DEFINE('_FILE_ERROR_EMPTY','Моля изберете файл');
DEFINE('_FILE_ERROR_SIZE','Файлът надвишава разрешения размер.');
DEFINE('_COM_A_FILE','Файлове');
DEFINE('_COM_A_FILEALLOWEDTYPES','Разрешени файлове');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC','Въведете тук позволените разширения на качваните файлове. Разделете ги със запетаи и <strong>ползвайте само малки букви</strong> без интервали. Пример: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE','Максимален размер на файловете в килобайти (KB)');
DEFINE('_COM_A_FILEUPLOAD','Могат ли гостите да качват файлове');
DEFINE('_COM_A_FILEUPLOAD_DESC','Изберете &quot;Да&quot; ако искате да разрешите на всички потребители и посетители да могат да добавят файлове в мненията.');
DEFINE('_COM_A_FILEREGUPLOAD','Могат ли регистрирани потребители да качват на файлове?');
DEFINE('_COM_A_FILEREGUPLOAD_DESC','Изберете &quot;Да&quot; ако искате да разрешите на регистрираните потребители да добавят файлове към мненията. Администраторите и модераторите винаги имат тази възможност.');
DEFINE('_SUBMIT_CANCEL','Мнението не беше публикувано.');
DEFINE('_HELP_SUBMIT','Натиснете тук за да публикувате мнението');
DEFINE('_HELP_PREVIEW','Натиснете тук за да видите как ще изглежда мнението');
DEFINE('_HELP_CANCEL','Откажете публикиването на мнението');
DEFINE('_POST_DELETE_ATT','Ако оставите отметката, ще бъдат изтрити и всички прикачени файлове и изображения към това мнение.');

//new since preRC4-III

DEFINE('_COM_A_USER_MARKUP','Показване на забележка при редактирано мнение');
DEFINE('_COM_A_USER_MARKUP_DESC','Изберете &quot;Да&quot; за да се добавя забележка, когато мнението е редактирано.');
DEFINE('_EDIT_BY','Мнението е редактирано от:');
DEFINE('_EDIT_AT','на:');
DEFINE('_UPLOAD_ERROR_GENERAL','Грешка при добавянето на изображението');
DEFINE('_COM_A_IMGB_IMG_BROWSE','Качени изображения');
DEFINE('_COM_A_IMGB_FILE_BROWSE','Качени файлове');
DEFINE('_COM_A_IMGB_TOTAL_IMG','Брой качени изображения');
DEFINE('_COM_A_IMGB_TOTAL_FILES','Брой качени файлове');
DEFINE('_COM_A_IMGB_ENLARGE','Натиснете върху изображението, за да го видите в цял размер.');
DEFINE('_COM_A_IMGB_DOWNLOAD','Натиснете върху изображението за да видите файла');
DEFINE('_COM_A_IMGB_DUMMY_DESC','Функцията дава възможност да замените определено изображение с празно такова.');

DEFINE('_COM_A_IMGB_DUMMY','Текущо празно изображение');
DEFINE('_COM_A_IMGB_REPLACE','Замести с празно изображение');
DEFINE('_COM_A_IMGB_REMOVE','Изтрий напълно');
DEFINE('_COM_A_IMGB_NAME','Име');
DEFINE('_COM_A_IMGB_SIZE','Големина');
DEFINE('_COM_A_IMGB_DIMS','Размери');
DEFINE('_COM_A_IMGB_CONFIRM','Сиурни ли сте в изтриването на този файл?');
DEFINE('_COM_A_IMGB_VIEW','Отворете мнението (за редакция)');
DEFINE('_COM_A_IMGB_NO_POST','Не принадлежи към мнение!');
DEFINE('_USER_CHANGE_VIEW','Промените ще бъдат приложени при следващото Ви посещение на форума.');
DEFINE('_MOSBOT_DISCUSS_A','Обсъди във форума. (');
DEFINE('_MOSBOT_DISCUSS_B',' мнения)');
DEFINE('_POST_DISCUSS','В тази тема се дискутира статията');
DEFINE('_COM_A_RSS','Ползване на RSS емисия');
DEFINE('_COM_A_RSS_DESC','RSS емисията позволява на потребителите да четат мненията във форума, чрез софтуер на техния компютър (вижте <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> като пример).');
DEFINE('_LISTCAT_RSS','емисия на последните мнения');
DEFINE('_SEARCH_REDIRECT','FireBoard изисква да се идентифирате отново.');

//====================
//admin.forum.html.php

DEFINE('_COM_A_CONFIG','FireBoard Настройки');
DEFINE('_COM_A_DISPLAY','Покажи #');
DEFINE('_COM_A_CURRENT_SETTINGS','Текуща настройка');
DEFINE('_COM_A_EXPLANATION','Обяснение');
DEFINE('_COM_A_BOARD_TITLE','Заглавие на форума');
DEFINE('_COM_A_BOARD_TITLE_DESC','Името на форума');
DEFINE('_COM_A_VIEW_TYPE','Изглед по подразбиране');
DEFINE('_COM_A_VIEW_TYPE_DESC','Изберете между плосък и стъпаловиден изглед');
DEFINE('_COM_A_THREADS','Теми на страница');
DEFINE('_COM_A_THREADS_DESC','Брой теми на една страница');
DEFINE('_COM_A_REGISTERED_ONLY','Само за регистрирани потребители');
DEFINE('_COM_A_REG_ONLY_DESC','Изберете &quot;Да&quot; ако искате форума да бъде ползван само от регистрирани потребители. Изберете &quot;Не&quot; ако искате да бъде ползван от всички.');
DEFINE('_COM_A_PUBWRITE','Могат ли нерегестрирани да публикуват');
DEFINE('_COM_A_PUBWRITE_DESC','Изберете &quot;Да&quot; за да разрешите на посетители, които не са регистрирани да добавят мнения. Изберете &quot;Не&quot; ако искате тези посетители да могат само да четат мненията.');
DEFINE('_COM_A_USER_EDIT','Редакция от потребителите');
DEFINE('_COM_A_USER_EDIT_DESC','Изберете &quot;Да&quot; за да разрешите на потребителите да могат да редактират собствените мнения.');
DEFINE('_COM_A_MESSAGE','Натиснете бутона &quot;Save&quot; за запис.');
DEFINE('_COM_A_HISTORY','Показване на история');
DEFINE('_COM_A_HISTORY_DESC','Изберете &quot;Да&quot; ако искате да се показва историята на темата при отговор');
DEFINE('_COM_A_SUBSCRIPTIONS','Разрешаване на абонаменти');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC','Изберете &quot;Да&quot; за да позволите на потребителите да се абонират за темите.');
DEFINE('_COM_A_HISTLIM','Брой мнения в история');
DEFINE('_COM_A_HISTLIM_DESC','Брой мнения показвани като история');
DEFINE('_COM_A_FLOOD','Защита');
DEFINE('_COM_A_FLOOD_DESC','Брой секунди които потребителите трябва да изчакат преди да публикуват мнение отново.');
DEFINE('_COM_A_MODERATION','Известяване на модераторите');
DEFINE('_COM_A_MODERATION_DESC','Избере &quot;Да&quot; ако искате модераторите да получават е-мейл при нови мнения. Въпреки, че всички администратори имат правата на модератори, за да получават известяванията, те трябва също да бъдат посочени като модератори в профила им във форума!');
DEFINE('_COM_A_SHOWMAIL','Показване на е-мейл');
DEFINE('_COM_A_SHOWMAIL_DESC','Изберете &quot;Не&quot; ако искате да не се показват е-мейл адресите.');
DEFINE('_COM_A_AVATAR','Ползване на аватари');
DEFINE('_COM_A_AVATAR_DESC','Изберете &quot;Да&quot; ако искате потребителите да могат да добавят аватар към профилите си.');
DEFINE('_COM_A_AVHEIGHT','Максимална височина');
DEFINE('_COM_A_AVWIDTH','Максимална широчина');
DEFINE('_COM_A_AVSIZE','Максимален размер<br/><em>в килобайти</em>');
DEFINE('_COM_A_USERSTATS','Показване на статистика');
DEFINE('_COM_A_USERSTATS_DESC','Изберете &quot;Да&quot; за да се показва статистика за потребителите като брой мнения, вид на потребителя (Администратор, Модератор, Потребител и т.н.).');
DEFINE('_COM_A_AVATARUPLOAD','Позволете качване на собствена снимка');
DEFINE('_COM_A_AVATARUPLOAD_DESC','Изберете &quot;Да&quot; ако искате потребителите да могат да качват собствени снимки.');
DEFINE('_COM_A_AVATARGALLERY','Ползване на галерия');
DEFINE('_COM_A_AVATARGALLERY_DESC','Изберете &quot;Да&quot; ако искате потребителите да имат възможност да избират снимки от галерията (components/com_joomlaboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC','Изберете &quot;Да&quot; за да определяте ранга на потребителите на база на броя на мненията им.');
DEFINE('_COM_A_RANKINGIMAGES','Ползване на изображения');
DEFINE('_COM_A_RANKINGIMAGES_DESC','Изберете &quot;Да&quot; за да се показва ранга на потребителите чрез изображение.');

//email and stuff

$_COM_A_NOTIFICATION ="Ново мнение от ";
$_COM_A_NOTIFICATION1="Има ново мнение в тема, за която сте абонирани в ";
$_COM_A_NOTIFICATION2="Можете да промените абонаментите си в профила на форума.";
$_COM_A_NOTIFICATION3="Моля, не отговаряйте на това писмо, то е само информативно.";
$_COM_A_NOT_MOD1="Ново мнение е добавено във форума, към който сте модератор";
$_COM_A_NOT_MOD2="Моля, прегледайте го, след като си влезете в профила.";
DEFINE('_COM_A_NO','Не');
DEFINE('_COM_A_YES','Да');
DEFINE('_COM_A_FLAT','Плоско');
DEFINE('_COM_A_THREADED','Стъпаловидно');
DEFINE('_COM_A_MESSAGES','Мнения на страница');
DEFINE('_COM_A_MESSAGES_DESC','Брой на мненията на една страница');

//admin; changes from 0.9 to 0.9.1

DEFINE('_COM_A_USERNAME','Потребителско име');
DEFINE('_COM_A_USERNAME_DESC','Изберете &quot;Да&quot; ако искате да се показва потребителското име, вместо истинското име на потребителя');
DEFINE('_COM_A_CHANGENAME','Позволете промяна на името');
DEFINE('_COM_A_CHANGENAME_DESC','Изберете &quot;Да&quot; ако искате да дадете възможност на регистрираните потребители да промянет името си при публикуване на мнение.');

//admin; changes 0.9.1 to 0.9.2

DEFINE('_COM_A_BOARD_OFFLINE','Изключване на форума');
DEFINE('_COM_A_BOARD_OFFLINE_DESC','Изберете &quot;Да&quot; за да изключите форума. Форумът ще бъде видим само за администраторите.');
DEFINE('_COM_A_BOARD_OFFLINE_MES','Съобщение при изключен форум');
DEFINE('_COM_A_PRUNE','Прочистване на форуми');
DEFINE('_COM_A_PRUNE_NAME','Форуми за прочистване:');
DEFINE('_COM_A_PRUNE_DESC','Функцията за прочистване на форумите дава възможност да изтриете мнения, в които няма публикувано мнение от определен брой дни. Заключените и залепени теми не се изтриват.');
DEFINE('_COM_A_PRUNE_NOPOSTS','Прочистване на теми без мнения през последните ');
DEFINE('_COM_A_PRUNE_DAYS','дни');
DEFINE('_COM_A_PRUNE_USERS','Прочистване на потребители');
DEFINE('_COM_A_PRUNE_USERS_DESC','Функцията позволява да прочистите таблицата с потребители на форума. Ако определен потребител е бил изтрит от Джумла!, чрез тази функция ще бъде изтрит и неговият профил във форума.');

//general
DEFINE('_GEN_ACTION','Действие');
DEFINE('_GEN_AUTHOR','автор');
DEFINE('_GEN_BY','от');
DEFINE('_GEN_CANCEL','отказ');
DEFINE('_GEN_CONTINUE','потвърди');
DEFINE('_GEN_DATE','Дата');
DEFINE('_GEN_DELETE','изтрий');
DEFINE('_GEN_EDIT','редактирай');
DEFINE('_GEN_EMAIL','Е-мейл');
DEFINE('_GEN_EMOTICONS','емоции');
DEFINE('_GEN_FLAT','Плоско');
DEFINE('_GEN_FLAT_VIEW','Плоско');
DEFINE('_GEN_FORUMLIST','Форум');
DEFINE('_GEN_FORUM','Форум');
DEFINE('_GEN_HELP','Помощ');
DEFINE('_GEN_HITS','Прегледи');
DEFINE('_GEN_LAST_POST','Последно мнение');
DEFINE('_GEN_LATEST_POSTS','Последните мнения');
DEFINE('_GEN_LOCK','заключи');
DEFINE('_GEN_UNLOCK','отключи');
DEFINE('_GEN_LOCKED_FORUM','Форумът е заключен и не може да се публикуват нови мнения.');
DEFINE('_GEN_LOCKED_TOPIC','Темата е заключена и не може да се публикуват нови мнения.');
DEFINE('_GEN_MESSAGE','Мнение');
DEFINE('_GEN_MODERATED','Форумът се модерира. Новите мнения се публикуват след одобрение.');
DEFINE('_GEN_MODERATORS','Модератори');
DEFINE('_GEN_MOVE','премести');
DEFINE('_GEN_NAME','Име');
DEFINE('_GEN_POST_NEW_TOPIC','::Нова тема::');
DEFINE('_GEN_POST_REPLY','Отговори');
DEFINE('_GEN_MYPROFILE','Профил');
DEFINE('_GEN_QUOTE','цитирай');
DEFINE('_GEN_REPLY','отговори');
DEFINE('_GEN_REPLIES','Отговори');
DEFINE('_GEN_THREADED','Стъпаловидно');
DEFINE('_GEN_THREADED_VIEW','Стъпаловидно');
DEFINE('_GEN_SIGNATURE','Моят профил');
DEFINE('_GEN_ISSTICKY','означава, че темата е залепена.');
DEFINE('_GEN_STICKY','залепи');
DEFINE('_GEN_UNSTICKY','отлепи');
DEFINE('_GEN_SUBJECT','Тема');
DEFINE('_GEN_SUBMIT','Потвърди');
DEFINE('_GEN_TOPIC','Тема');
DEFINE('_GEN_TOPICS','Теми');
DEFINE('_GEN_TOPIC_ICON','Икона');
DEFINE('_GEN_SEARCH_BOX','търсене...');
$_GEN_THREADED_VIEW="стъпаловидно";
$_GEN_FLAT_VIEW    ="плоско";

//avatar_upload.php

DEFINE('_UPLOAD_UPLOAD','Качване');
DEFINE('_UPLOAD_DIMENSIONS','Максимални размери (широчина x височина - размер)');
DEFINE('_UPLOAD_SUBMIT','Качване на нов аватар');
DEFINE('_UPLOAD_SELECT_FILE','Изберете файл');
DEFINE('_UPLOAD_ERROR_TYPE','Моля, изберете jpeg, gif или png файл');
DEFINE('_UPLOAD_ERROR_EMPTY','Моля, изберете файл');
DEFINE('_UPLOAD_ERROR_NAME','Името на снимката може да съдържа само букви и цифри.');
DEFINE('_UPLOAD_ERROR_SIZE','Размерът на снимката надхвърля максимално разрешения.');
DEFINE('_UPLOAD_ERROR_WIDTH','Широчината на снимката надхвърля максимално разрешената.');
DEFINE('_UPLOAD_ERROR_HEIGHT','Височината на снимката надхвърля максимално разрешената.');
DEFINE('_UPLOAD_ERROR_CHOOSE','Не сте избрали снимка');
DEFINE('_UPLOAD_UPLOADED','Вашата снимка бе качена.');
DEFINE('_UPLOAD_GALLERY','Изберете снимка от галерията:');
DEFINE('_UPLOAD_CHOOSE','Потвърдете избора си');

// listcat.php

DEFINE('_LISTCAT_ADMIN','Администраторът на сайта трябва да създаде такива от ');
DEFINE('_LISTCAT_DO','Те са наясно какво трябва да правят ');
DEFINE('_LISTCAT_INFORM','Моля, уведомете администратора на сайта!');
DEFINE('_LISTCAT_NO_CATS','Нямате дефинирани категории във форума.');
DEFINE('_LISTCAT_PANEL','административния панел на Джумла!');
DEFINE('_LISTCAT_PENDING','чакащи мнения');

// moderation.php

DEFINE('_MODERATION_MESSAGES','Няма чакащи съобщения в този форум.');

// post.php

DEFINE('_POST_ABOUT_TO_DELETE','Вие сте на път да изтриете съобщението');
DEFINE('_POST_ABOUT_DELETE','<strong>Забележки:</strong><br/><ul><li>Ако изтриете първото мнение в определена тема, то всички мнения след него също ще бъдат изтрити!</li><li>Всички мнения които са след това, ще се преместят с една позиция нагоре.</li></ul>');
DEFINE('_POST_CLICK','натиснете тук');
DEFINE('_POST_ERROR','Не може да бъде открит потребителя/е-мейла. Грешката в базата данни е неизвестна.');
DEFINE('_POST_ERROR_MESSAGE','Грешка при връзката с базата данни, моля опитайте отново или се свържете с администратор.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED','Грешка при връзката с базата данни. Моля опитайте отново или се свържете с администратор.');
DEFINE('_POST_ERROR_TOPIC','Грешка при операцията изтриване:');
DEFINE('_POST_FORGOT_NAME','Забравихте да въведете вашето име. Моля, въведете Вашето име и опитайте отново.');
DEFINE('_POST_FORGOT_SUBJECT','Забравихте да напишете тема. Моля, върнете се и напишете тема на мнението.');
DEFINE('_POST_FORGOT_MESSAGE','Не сте въвели мнение. Моля, върнете се и напишете мнението си.');
DEFINE('_POST_INVALID','Грешен номер на мнението.');
DEFINE('_POST_LOCK_SET','Темата бе заключена.');
DEFINE('_POST_LOCK_NOT_SET','Темата не може да бъде заключена.');
DEFINE('_POST_LOCK_UNSET','Темата бе отключена.');
DEFINE('_POST_LOCK_NOT_UNSET','Темата не може да бъде отключена.');
DEFINE('_POST_MESSAGE','Публикувайте ново мнение в ');
DEFINE('_POST_MOVE_TOPIC','Преместете темата във форум ');
DEFINE('_POST_NEW','Публикувайте ново мнение в: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC','Абонаментът за тази тема е неуспешен.');
DEFINE('_POST_NOTIFIED','Абонирайте се за тази тема.');
DEFINE('_POST_STICKY_SET','Темата бе залепена.');
DEFINE('_POST_STICKY_NOT_SET','Темата не може да бъде залепена.');
DEFINE('_POST_STICKY_UNSET','Темата бе отлепена.');
DEFINE('_POST_STICKY_NOT_UNSET','Темата не може да бъде отлепена.');
DEFINE('_POST_SUBSCRIBE','Абонамент');
DEFINE('_POST_SUBSCRIBED_TOPIC','Вече сте абониран за тази тема.');
DEFINE('_POST_SUCCESS','Мнението бе публикувано');
DEFINE('_POST_SUCCES_REVIEW','Мнението бе публикувано успешно. То трябва да бъде одобрено от модератор първо.');
DEFINE('_POST_SUCCESS_REQUEST','Вашето искане е отправено. Ако темата не се зареди след няколко секунди,');
DEFINE('_POST_TOPIC_HISTORY','История на темата');
DEFINE('_POST_TOPIC_HISTORY_MAX','Последните ');
DEFINE('_POST_TOPIC_HISTORY_LAST','мнения  -  <i>(Най-новото мнение е първо)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED','Темата не може да бъде преместена. За да се върнете в темата:');
DEFINE('_POST_TOPIC_FLOOD1','Включи се функцията за защита. Не можете да публикувате две или повече мнения в толкова кратък времеви интервал. ');
DEFINE('_POST_TOPIC_FLOOD2','Върнете се обратно, изчакайте известно време и опитайте отново.');
DEFINE('_POST_TOPIC_FLOOD3','Моля, натиснете бутона "BACK" на вашия браузър');
DEFINE('_POST_EMAIL_NEVER','E-mail адресът няма да бъде показан в сайта.');
DEFINE('_POST_EMAIL_REGISTERED','E-mail адресът ще бъде видим само за регистрираните потребители.');
DEFINE('_POST_LOCKED','Заключена от администратор.');
DEFINE('_POST_NO_NEW','Не са възможни нови отговори.');
DEFINE('_POST_NO_PUBACCESS1','Само регистрирани потребители могат да публикуват нови мнения.');
DEFINE('_POST_NO_PUBACCESS2','Единствено регистрирани потребители могат да публикуват мнения в този форум.');

// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS','Няма теми в този форум');
DEFINE('_SHOWCAT_PENDING','чакащи мнения');

// userprofile.php
DEFINE('_USER_DELETE',' Изтриване на подписа');
DEFINE('_USER_ERROR_A','Моля пишете на администратора, по какъв начин сте стигнали до тази страница!');
DEFINE('_USER_ERROR_B','Възникна грешка в системата на форума.');
DEFINE('_USER_ERROR_C','Благодарим Ви!');
DEFINE('_USER_ERROR_D','Номер на грешката, който да бъде включен в доклада');
DEFINE('_USER_GENERAL','Настройки на профила');
DEFINE('_USER_MODERATOR','Вие сте Модератор във форумите');
DEFINE('_USER_MODERATOR_NONE','Не сте модератор в нито един форум');
DEFINE('_USER_MODERATOR_ADMIN','Администраторите са модератори във всички форуми.');
DEFINE('_USER_NOSUBSCRIPTIONS','Нямате абонаменти');
DEFINE('_USER_PREFERED','Изглед');
DEFINE('_USER_PROFILE','Профил на ');
DEFINE('_USER_PROFILE_NOT_A','Вашият профил може');
DEFINE('_USER_PROFILE_NOT_B','не може');
DEFINE('_USER_PROFILE_NOT_C',' да бъде обновен.');
DEFINE('_USER_PROFILE_UPDATED','Профилът Ви е обновен.');
DEFINE('_USER_RETURN_A','Ако профилът Ви не се зареди до няколко секунди ');
DEFINE('_USER_RETURN_B','натиснете тук');
DEFINE('_USER_SUBSCRIPTIONS','Абонаменти:');
DEFINE('_USER_UNSUBSCRIBE',':: спри абонамента :: ');
DEFINE('_USER_UNSUBSCRIBE_A','Не може ');
DEFINE('_USER_UNSUBSCRIBE_B','да бъде');
DEFINE('_USER_UNSUBSCRIBE_C',' спрян абонамента за тази тема.');
DEFINE('_USER_UNSUBSCRIBE_YES','Абонаментът за темата бе спрян.');
DEFINE('_USER_DELETEAV',' Поставете отметка в кутиийката, за да изтриете аватара си');

//New 0.9 to 1.0

DEFINE('_USER_ORDER','Подредба на мненията');
DEFINE('_USER_ORDER_DESC','Новите първи');
DEFINE('_USER_ORDER_ASC','Старите първи');

// view.php

DEFINE('_VIEW_DISABLED', 'Само регистрирани потребители могат да публикуват нови мнения.');
DEFINE('_VIEW_POSTED', 'Публикувано от');
DEFINE('_VIEW_SUBSCRIBE', ':: Абонамент ::');
DEFINE('_MODERATION_INVALID_ID', 'Невалиден номер на мнението.');
DEFINE('_VIEW_NO_POSTS', 'Няма мнения в този форум.');
DEFINE('_VIEW_VISITOR', 'Гост');
DEFINE('_VIEW_ADMIN', 'Администратор');
DEFINE('_VIEW_USER', 'Потребител');
DEFINE('_VIEW_MODERATOR','Модератор');
DEFINE('_VIEW_REPLY','Отговорете на това мнение');
DEFINE('_VIEW_EDIT','Редактирайте това мнение');
DEFINE('_VIEW_QUOTE','Цитирайте това мнение');
DEFINE('_VIEW_DELETE','Изтрийте това мнение');
DEFINE('_VIEW_STICKY','Залепете темата');
DEFINE('_VIEW_UNSTICKY','Отлепете темата');
DEFINE('_VIEW_LOCK','Заключете темата');
DEFINE('_VIEW_UNLOCK','Отключете темата');
DEFINE('_VIEW_MOVE','Преместете темата в друг форум');
DEFINE('_VIEW_SUBSCRIBETXT','Абонирайте се за темата и ще получавате писма при нови мнения');

//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2

DEFINE('_HOME', 'Начало');
DEFINE('_POSTS', 'Мнения:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Мнение');
DEFINE('_FORUM_NOT_ALLOWED', 'Начало');
DEFINE('_FORUM_IS_OFFLINE', 'Форумът е изключен!');
DEFINE('_PAGE', 'Страница: ');
DEFINE('_NO_POSTS', 'Няма мнения');
DEFINE('_CHARS', 'символа максимум.');
DEFINE('_HTML_YES', 'HTML е изключен');
DEFINE('_YOUR_AVATAR', '<b>Моят Аватар</b>');
DEFINE('_NON_SELECTED', 'Няма избран все още<br />');
DEFINE('_SET_NEW_AVATAR', 'Избери нов аватар');
DEFINE('_THREAD_UNSUBSCRIBE', 'Отпиши');
DEFINE('_SHOW_LAST_POSTS', 'Активни теми през последните');
DEFINE('_SHOW_HOURS', 'часа');
DEFINE('_SHOW_POSTS', 'Общо:');
DEFINE('_DESCRIPTION_POSTS', 'Показани са последните мнения от активните теми');
DEFINE('_SHOW_4_HOURS', '4 часа');
DEFINE('_SHOW_8_HOURS', '8 часа');
DEFINE('_SHOW_12_HOURS', '12 часа');
DEFINE('_SHOW_24_HOURS', '24 часа');
DEFINE('_SHOW_48_HOURS', '48 часа');
DEFINE('_SHOW_WEEK', 'Седмица');
DEFINE('_POSTED_AT', 'Публикувано на');
DEFINE('_DATETIME', 'd/m/Y H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Няма нови мнения през времевия диапазон, който сте избрали.');
DEFINE('_MESSAGE', 'Мнение');
DEFINE('_NO_SMILIE', 'Без');
DEFINE('_FORUM_UNAUTHORIZIED', 'Този форум е достъпен само за регистрирани потребители.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Ако сте се регистрирали вече, моля, влезте с акаунта си първо.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Модериран');
DEFINE('_MOD_APPROVE', 'Одобри');
DEFINE('_MOD_DELETE', 'Изтрий');

//NEW in RC1

DEFINE('_SHOW_LAST', 'Последно мнение');
DEFINE('_POST_WROTE', 'написа');
DEFINE('_COM_A_EMAIL', 'E-mail адрес на форума');
DEFINE('_COM_A_EMAIL_DESC', 'Моля, въведете валиден e-mail адрес');
DEFINE('_COM_A_WRAP', 'Сбивай думи по-дълги от');
DEFINE('_COM_A_WRAP_DESC','Въведете максималният брой символи, които може да се съдържат в една дума. <br/> 70 символа може би е максимума за шаблони, които са с фиксирана широчина, но може да се наложи и да поекспериментирате малко.<br/>Не действа върху URL-та, без значение колко дълги са те');
DEFINE('_COM_A_SIGNATURE', 'Максимална дължина на Подписа');
DEFINE('_COM_A_SIGNATURE_DESC', 'Максимален брой позволени символи за подписа на потребителите.');
DEFINE('_SHOWCAT_NOPENDING', 'Няма чакащи съобщения');
DEFINE('_COM_A_BOARD_OFSET', 'Часова разлика със сървъра');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Някои форуми се намират на сървъри, които са в различна часова зона от тази на посетителя. Въведете положително или отрицателно число, за да синхронизирате времето когато е публикувано мнение');

//New in RC2

DEFINE('_COM_A_BASICS', 'Основни');
DEFINE('_COM_A_FRONTEND', 'Форум');
DEFINE('_COM_A_SECURITY', 'Сигурност');
DEFINE('_COM_A_AVATARS', 'Аватари');
DEFINE('_COM_A_INTEGRATION', 'Интегриране');
DEFINE('_COM_A_PMS', 'Разреши изпращането на лични съобщения');
DEFINE('_COM_A_PMS_DESC','Изберете подходящ компонент за имзпращането на лични съобщения ако имате инсталиран такъв. Ако изберете Clexus PM това също ще активира ClexusPM опции, които са свързани с потребителския профил (като ICQ, AIM, Yahoo, MSN');
DEFINE('_VIEW_PMS', 'Кликни тук, за да изпратиш лично съобщение до потребителя');

//new in RC3

DEFINE('_POST_RE', 'Отг:');
DEFINE('_BBCODE_BOLD', 'Удебелен текст: [b]текст[/b] ');
DEFINE('_BBCODE_ITALIC', 'Наклонен текст: [i]текст[/i]');
DEFINE('_BBCODE_UNDERL', 'Подчертан текст: [u]текст[/u]');
DEFINE('_BBCODE_QUOTE', 'Цитат: [quote]текст[/quote]');
DEFINE('_BBCODE_CODE', 'Код: [code]код[/code]');
DEFINE('_BBCODE_ULIST', 'Неподреден списък: [ul] [li]текст[/li] [/ul] - Съвет: списъка трябва да съдържа обекти');
DEFINE('_BBCODE_OLIST', 'Номериран списък: [ol] [li]текст[/li] [/ol] - Съвет: списъка трябва да съдържа обекти');
DEFINE('_BBCODE_IMAGE', 'Изображение: [размери=(01-499)]http://www.archerybg.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Връзка: [url=http://www.archerybg.com/]Име на връзката[/url]');
DEFINE('_BBCODE_CLOSA', 'Затвори всички тагове');
DEFINE('_BBCODE_CLOSE', 'Затвори всички отворени тагове');
DEFINE('_BBCODE_COLOR', 'Цвят: [color=#FF6600]текст[/color]');
DEFINE('_BBCODE_SIZE', 'Големина: [size=1]големина на текста[/size] - Съвет: Въведете число от 1 до 5');
DEFINE('_BBCODE_LITEM', 'Обект в списък: [li]име на обекта[/li]');
DEFINE('_BBCODE_HINT', 'bbCode Помощ - Съвет: bbCode може да бъде използван върху маркиран текст!');
DEFINE('_COM_A_TAWIDTH', 'Широчина на текстовото поле');
DEFINE('_COM_A_TAWIDTH_DESC', 'Настройте широчината на текстовото поле за въвеждане на отговора/мнението, за да подхожда на вашия шаблон за сайта.');
DEFINE('_COM_A_TAHEIGHT', 'Височина на текстовото поле');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Настройте височината на текстовото поле за въвеждане на отговора/мнението, за да подхожда на вашия шаблон за сайта');
DEFINE('_COM_A_ASK_EMAIL', 'Изисквай E-mail');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'Изисква е-mail адрес, когато потребителите или посетителите искат да пуснат мнение. Нагласете на &quot;Не&quot; ако искате да деактивирате тази функция във frontend-а.');


//Rank Administration - Dan Syme/IGD

define('_KUNENA_RANKS_MANAGE', 'Управление на ранговете');
define('_KUNENA_SORTRANKS', 'Сортирай по ранг');
define('_KUNENA_RANKSIMAGE', 'Снимка на ранга');
define('_KUNENA_RANKS', 'Заглавие на ранга');
define('_KUNENA_RANKS_SPECIAL', 'Специално');
define('_KUNENA_RANKSMIN', 'Минимум брой мнения');
define('_KUNENA_RANKS_ACTION', 'Действия');
define('_KUNENA_NEW_RANK', 'Нов ранг');


?>

