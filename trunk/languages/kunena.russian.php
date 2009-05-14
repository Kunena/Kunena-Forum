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
* Kunena Russian Translation
* @version 1.0
* Copyright (C) Translation 2009 www.kunena.ru
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Включить Подсветку Кода');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Включает скрипт подсветки кода. Если ваши посетители оставляют сообщения с фрагментами php или похожего кода внутри специального тэга, включение данной опции будет подсвечивать данные фрагменты.');
DEFINE('_COM_A_RSS_TYPE', 'Тип RSS-канала по умолчанию');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Выберите вид RSS-канала ("По Теме" или "По Сообщению"). "По Теме" означает что для каждой темы будет формироваться только одна запись, независимо от того сколько сообщений было написано в этой теме. "По Сообщению" формирует более компактную RSS-ленту но не учитывает оставленные ответы. ');
DEFINE('_COM_A_RSS_BY_THREAD', 'По Теме');
DEFINE('_COM_A_RSS_BY_POST', 'По Сообщению');
DEFINE('_COM_A_RSS_HISTORY', 'История RSS-канала');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Выберите период включения истории в RSS-ленту. По умолчанию период равен 1 месяцу, но для посещаемых сайтов можно ограничить 1 неделей.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 неделя');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 месяц');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 год');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Страница по умолчанию');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Выберите стартовую страницу по умолчанию. По умолчанию это "Последние Обсуждения". Для шаблонов отличных от "Default_ex" должна быть выбрана страница "Категории". Если выбрана страница "Мои обсуждения", то для гостя применяется страница "Самое Обсуждаемое".');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Последние Обсуждения');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Мои Обсуждения');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Категории');
DEFINE('_KUNENA_BBCODE_HIDE', 'Скрытая информация от незарегистрированных пользователей:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Предупреждающий Блок!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Родительский форум не должен быть тем же.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Родительский форум один из собственных дочерних.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Отсутствует ID форума.');
DEFINE('_KUNENA_RECURSION', 'Обнаружена рекурсия.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Вы забыли ввести своё имя.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'Вы забыли ввести свой e-mail.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Вы забыли указать тему.');
DEFINE('_KUNENA_EDIT_TITLE', 'Редактировать Ваши Данные');
DEFINE('_KUNENA_YOUR_NAME', 'Ваше Имя');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'Имя Пользователя:');
DEFINE('_KUNENA_PASS', 'Пароль:');
DEFINE('_KUNENA_VPASS', 'Подтверждение Пароля:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Пользовательские данные были сохранены.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BB-коды');
DEFINE('_COM_A_BBCODE_SETTINGS', 'Настройка BB-кодов');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Показывать тэг [spoiler] в панели инструментов редактора');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Установите "Да" для отображение тэга [spoiler] в панели инструментов редактора сообщения');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Показывать тэг [video] в панели инструментов редактора');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Установите "Да" для отображение тэга [video] в панели инструментов редактора сообщения');
DEFINE('_COM_A_SHOWEBAYTAG', 'Показывать тэг [ebay] в панели инструментов редактора');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Установите "Да" для отображение тэга [ebay] в панели инструментов редактора сообщения');
DEFINE('_COM_A_TRIMLONGURLS', 'Обрезать длинные URL');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Установите "Да" для обрезания длинных URL. Смотрите настройки обрезки передней и задней части.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Передняя часть обрезаемых URL');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Количество символов для передней части обрезаемых URL. Опция "Обрезать длинные URL" должна быть включена.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Задняя часть обрезаемых URL');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Количество символов для задней части обрезаемых URL. Опция "Обрезать длинные URL" должна быть включена.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Встраивание YouTube видео');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Установите "Да" если хотите чтобы ссылки на видео YouTube автоматически встраивались в форум.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Встраивание предметов eBay');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Установите "Да" если хотите чтобы предметы и поиски eBay автоматически встраивались в форум.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'Языковой код виджета eBay');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'Важно указать правильный языковой код так как виджет определяет из него не только язык но и валюту. По умолчанию en-us для ebay.com. Примеры: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Время жизни сессии (сек)');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'По умолчанию 1800 [секунд]. Время жизни сессии (таймаут) схоже с "Временем жизни Joomla" и используется для определения прав доступа, подсчетов "Кто онлайн", индикатора "Новое" и т.д.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Объединить');
DEFINE('_VIEW_MERGE', 'Объединить');
DEFINE('_POST_MERGE_TOPIC', 'Объединить эту тему с');
DEFINE('_POST_MERGE_GHOST', 'Оставить копию темы');
DEFINE('_POST_SUCCESS_MERGE', 'Тема успешно объединена.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Объединение не удалось.');
DEFINE('_GEN_SPLIT', 'Разделить');
DEFINE('_GEN_DOSPLIT', 'Ок');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Тема разделена успешно.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Тема успешно изменена.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Изменить тему не удалось');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Разделение не выполнено.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Одинаковые сообщения игнорируются.');
DEFINE('_POST_SPLIT_HINT', '<br />Подсказка: Вы можете поднять сообщение в теме если выберете во второй колонке и ничего не выберете для разделения.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'объединить');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Ветка была удалена из вашего избранного.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Ветка <b>НЕ</b> была удалена из вашего избранного.');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Ваш запрос на удаление из избранного обработан.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Ветка была удалена из ваших подписок.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Ветка <b>НЕ</b> была удалена из ваших подписок.');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Ваш запрос на удаление из подписок обработан.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Не указана Категория назначения. Ничего не было перенесено.');

// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Последнее');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Моё');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Обсуждения которые я начал или в которых принимал участие');
DEFINE('_KUNENA_CATEGORY', 'Категория');
DEFINE('_KUNENA_CATEGORIES', 'Категории');
DEFINE('_KUNENA_POSTED_AT', 'Написано');
DEFINE('_KUNENA_AGO', 'назад');
DEFINE('_KUNENA_DISCUSSIONS', 'Обсуждения');
DEFINE('_KUNENA_TOTAL_THREADS', 'Всего Тем:');
DEFINE('_SHOW_DEFAULT', 'По умолчанию');
DEFINE('_SHOW_MONTH', 'Месяц');
DEFINE('_SHOW_YEAR', 'Год');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Копируется "%src%" в "%dst%"...');
DEFINE('_KUNENA_CSS_SAVE', 'Сохранение css-файла должно быть здесь... файл="%file%"'); DEFINE('_KUNENA_UP_ATT_10', 'Таблица вложений успешно обновлена до последней структуры 1.0.х серии!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Вложения в таблице сообщений успешно обновлены до последней структуры 1.0.х серии!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Невозможно повысить дочерний объект в иерархии сообщений. Ничего не удалено.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Невозможно удалить сообщение(я) - ничего не удалено.');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Невозможно удалить текст сообщения(ий). Обновите БД вручную (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Все удалено, но не выполнено обновление показателей пользовательских сообщений.');
DEFINE('_KUNENA_POST_MOV_ERR_DB', 'Ошибка сервера БД. Обновите БД вручную.');
DEFINE('_KUNENA_UNIST_SUCCESS', 'Компонент был успешно деинсталлирован.');
DEFINE('_KUNENA_PDF_VERSION', 'Версия компонента форума Kunena: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Сгенерировано: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Нет форумов для поиска.');
DEFINE('_KUNENA_ERRORADDUSERS', 'Ошибка добавления пользователей:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Пользователи синхронизированы; Удалено:');
DEFINE('_KUNENA_USERSSYNCADD', 'добавлено');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'пользовательских профилей.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Не найдено профилей подходящих для синхронизации.');
DEFINE('_KUNENA_SYNC_USERS', 'Синхронизация Пользователей');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Синхронизация таблицы пользователей Kunena с таблицей пользователей Joomla.');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Почтовые Администраторы');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC', 'Установить &quot;Да&quot; если вы хотите получать уведомления на почту о каждом новом сообщении.');
DEFINE('_KUNENA_RANKS_EDIT', 'Редактировать Ранг');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Скрыть e-mail');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Воскресенье');
DEFINE('_KUNENA_DT_LDAY_MON', 'Понедельник');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Вторник');
DEFINE('_KUNENA_DT_LDAY_WED', 'Среда');
DEFINE('_KUNENA_DT_LDAY_THU', 'Четверг');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Пятница');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Суббота');
DEFINE('_KUNENA_DT_DAY_SUN', 'Вс');
DEFINE('_KUNENA_DT_DAY_MON', 'Пн');
DEFINE('_KUNENA_DT_DAY_TUE', 'Вт');
DEFINE('_KUNENA_DT_DAY_WED', 'Ср');
DEFINE('_KUNENA_DT_DAY_THU', 'Чт');
DEFINE('_KUNENA_DT_DAY_FRI', 'Пт');
DEFINE('_KUNENA_DT_DAY_SAT', 'Сб');
DEFINE('_KUNENA_DT_LMON_JAN', 'Январь');
DEFINE('_KUNENA_DT_LMON_FEB', 'Февраль');
DEFINE('_KUNENA_DT_LMON_MAR', 'Март');
DEFINE('_KUNENA_DT_LMON_APR', 'Апрель');
DEFINE('_KUNENA_DT_LMON_MAY', 'Май');
DEFINE('_KUNENA_DT_LMON_JUN', 'Июнь');
DEFINE('_KUNENA_DT_LMON_JUL', 'Июль');
DEFINE('_KUNENA_DT_LMON_AUG', 'Август');
DEFINE('_KUNENA_DT_LMON_SEP', 'Сентябрь');
DEFINE('_KUNENA_DT_LMON_OCT', 'Октябрь');
DEFINE('_KUNENA_DT_LMON_NOV', 'Ноябрь');
DEFINE('_KUNENA_DT_LMON_DEV', 'Декабрь');
DEFINE('_KUNENA_DT_MON_JAN', 'Янв');
DEFINE('_KUNENA_DT_MON_FEB', 'Фев');
DEFINE('_KUNENA_DT_MON_MAR', 'Март');
DEFINE('_KUNENA_DT_MON_APR', 'Апр');
DEFINE('_KUNENA_DT_MON_MAY', 'Май');
DEFINE('_KUNENA_DT_MON_JUN', 'Июнь');
DEFINE('_KUNENA_DT_MON_JUL', 'Июль');
DEFINE('_KUNENA_DT_MON_AUG', 'Авг');
DEFINE('_KUNENA_DT_MON_SEP', 'Сент');
DEFINE('_KUNENA_DT_MON_OCT', 'Окт');
DEFINE('_KUNENA_DT_MON_NOV', 'Ноябрь');
DEFINE('_KUNENA_DT_MON_DEV', 'Дек');
DEFINE('_KUNENA_CHILD_BOARD', 'Дочерняя Тема');
DEFINE('_WHO_ONLINE_GUEST', 'Гость');
DEFINE('_WHO_ONLINE_MEMBER', 'Зарегистрированный пользователь');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'нет');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Процессор Обработки Изображений:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Нажмите здесь для продолжения...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Применить!');
DEFINE('_KUNENA_NO_ACCESS', 'Нет доступа в этот Форум!');
DEFINE('_KUNENA_TIME_SINCE', '%time% назад');
DEFINE('_KUNENA_DATE_YEARS', 'Год');
DEFINE('_KUNENA_DATE_MONTHS', 'мес.');
DEFINE('_KUNENA_DATE_WEEKS','нед.');
DEFINE('_KUNENA_DATE_DAYS', 'д.');
DEFINE('_KUNENA_DATE_HOURS', 'ч.');
DEFINE('_KUNENA_DATE_MINUTES', 'м.');

// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'Вы действительно хотите удалить ознакомительные данные? Это действие необратимо.');

// 1.0.2
DEFINE('_KUNENA_HEADERADD',  'Заголовок форума:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', 'Расширенная отображаемая информация');
DEFINE('_KUNENA_CLASS_SFX', 'Суффикс CSS-класса Форума');
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Время Редактирования Пользователем');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Установите 0 для неограниченного времени, или количество секунд от публикации или модификации до возможности редактирования.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Допустимый Интервал Редактирования Пользователем (сек)');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'По умолчанию 600 [секунд], позволяет модифицировать сообщение не позднее 600 секунд, после этого времени ссылка на редактирование исчезает.');
DEFINE('_KUNENA_HELPPAGE', 'Включить Страницу Помощи');
DEFINE('_KUNENA_HELPPAGE_DESC', 'Если включено, то ссылка на страницу помощи будет отображаться в верхнем меню.');
DEFINE('_KUNENA_HELPPAGE_IN_FB', 'Отображать помощь в Kunena');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC', 'Если включено, то контекстный текст помощи будет включен в Kunena и внешняя страница помощи не будет работать.');
DEFINE('_KUNENA_HELPPAGE_CID', 'ID Контекста Помощи');
DEFINE('_KUNENA_HELPPAGE_CID_DESC', 'Нужно включить опцию "Отображать помощь в Kunena"');
DEFINE('_KUNENA_HELPPAGE_LINK', 'Ссылка на внешнюю страницу помощи');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC', 'Если включена ссылка на внешнюю страницу помощи, необходимо выключить опцию "Отображать помощь в Kunena"');
DEFINE('_KUNENA_RULESPAGE', 'Включить Страницу Правил');
DEFINE('_KUNENA_RULESPAGE_DESC', 'Если включено, то ссылка на страницу с правилами будет отображаться в верхнем меню.');
DEFINE('_KUNENA_RULESPAGE_IN_FB', 'Показывать правила в Kunena');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC', 'Если включено, то контекстный текст правил будет включен в Kunena и внешняя страница правил не будет работать.');
DEFINE('_KUNENA_RULESPAGE_CID', 'ID Контекста Правил');
DEFINE('_KUNENA_RULESPAGE_CID_DESC', 'Нужно включить опцию "Отображать правила в Kunena"');
DEFINE('_KUNENA_RULESPAGE_LINK', 'Ссылка на внешнюю страницу правил');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC', 'Если включена ссылка на внешнюю страницу правил, необходимо выключить опцию "Отображать правила в Kunena"');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT', 'Библиотека GD не найдена');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT', 'Библиотека GD2 не найдена');
DEFINE('_KUNENA_GD_INSTALLED', 'Библиотека GD установлена');
DEFINE('_KUNENA_GD_NO_VERSION', 'Невозможно установить версию GD');
DEFINE('_KUNENA_GD_NOT_INSTALLED', 'Библиотека GD не установлена, можно получить дополнительную информацию');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT', 'Высота Маленького Изображения');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH', 'Ширина Маленького Изображения');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT', 'Высота Среднего Изображения');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH', 'Ширина Среднего Изображения');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT', 'Высота Большого Изображения');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH', 'Ширина Большого Изображения');
DEFINE('_KUNENA_AVATAR_QUALITY', 'Качество Аватара');
DEFINE('_KUNENA_WELCOME', 'Добро пожаловать в Kunena!');
DEFINE('_KUNENA_WELCOME_DESC', 'Спасибо за выбор Kunena в качестве решения для вашего форума.');
DEFINE('_KUNENA_STATISTIC', 'Статистика');
DEFINE('_KUNENA_VALUE', 'Значение');
DEFINE('_GEN_CATEGORY', 'Категория');
DEFINE('_GEN_STARTEDBY', 'Начата:');
DEFINE('_GEN_STATS', 'показатели');
DEFINE('_STATS_TITLE', 'показатели - форума');
DEFINE('_STATS_GEN_STATS', 'Основные показатели');
DEFINE('_STATS_TOTAL_MEMBERS', 'Пользователи:');
DEFINE('_STATS_TOTAL_REPLIES', 'Ответов:');
DEFINE('_STATS_TOTAL_TOPICS', 'Тем:');
DEFINE('_STATS_TODAY_TOPICS', 'Тем сегодня:');
DEFINE('_STATS_TODAY_REPLIES', 'Ответов сегодня:');
DEFINE('_STATS_TOTAL_CATEGORIES', 'Категорий:');
DEFINE('_STATS_TOTAL_SECTIONS', 'Секций:');
DEFINE('_STATS_LATEST_MEMBER', 'Последний пользователь:');
DEFINE('_STATS_YESTERDAY_TOPICS', 'Тем вчера:');
DEFINE('_STATS_YESTERDAY_REPLIES', 'Ответов вчера:');
DEFINE('_STATS_POPULAR_PROFILE', '10 Популярных Пользователей');
DEFINE('_STATS_TOP_POSTERS', 'Самые активные');
DEFINE('_STATS_POPULAR_TOPICS', 'Популярные темы');
DEFINE('_COM_A_STATSPAGE', 'Включить Страницу Показателей');
DEFINE('_COM_A_STATSPAGE_DESC', 'Если включено, то ссылка на страницу статистики форума будет отображаться в верхнем меню.');
DEFINE('_COM_C_JBSTATS', 'Показатели Форума');
DEFINE('_COM_C_JBSTATS_DESC', 'Статистика Форума');
DEFINE('_GEN_GENERAL', 'Основное');
DEFINE('_PERM_NO_READ', 'У вас не достаточно привилегий для доступа к этому форуму.');
DEFINE ('_KUNENA_SMILEY_SAVED', 'Смайлик сохранен');
DEFINE ('_KUNENA_SMILEY_DELETED', 'Смайлик удален');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS', 'Такой код уже присутствует');
DEFINE ('_KUNENA_MISSING_PARAMETER', 'Отсутствующий параметр');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS', 'Ранг уже есть');
DEFINE ('_KUNENA_RANK_DELETED', 'Ранг удален');
DEFINE ('_KUNENA_RANK_SAVED', 'Ранг сохранен');
DEFINE ('_KUNENA_DELETE_SELECTED', 'Удаление выбрано');
DEFINE ('_KUNENA_MOVE_SELECTED', 'Перенос выбран');
DEFINE ('_KUNENA_REPORT_LOGGED', 'Зарегистрировано');
DEFINE ('_KUNENA_GO', '<>');
DEFINE('_KUNENA_MAILFULL', 'Включать полное содержимое сообщения в письмах отсылаемых подписчикам');
DEFINE('_KUNENA_MAILFULL_DESC', 'Если нет - подписчики будут получать только заголовки новых сообщений');
DEFINE('_KUNENA_HIDETEXT', 'Пожалуйста выполните вход для просмотра данного содержимого!');
DEFINE('_BBCODE_HIDE', 'Скрытый текст: [hide]любой скрытый текст[/hide] - скрывает часть сообщения от незарегистрированных пользователей ');
DEFINE('_KUNENA_FILEATTACH', 'Приложенные Файлы:');
DEFINE('_KUNENA_FILENAME', 'Имя Файла:');
DEFINE('_KUNENA_FILESIZE', 'Размер Файла:');
DEFINE('_KUNENA_MSG_CODE', 'Код:');
DEFINE('_KUNENA_CAPTCHA_ON', 'Система защиты от спама');
DEFINE('_KUNENA_CAPTCHA_DESC', 'Включение/Выключение CAPTCHA для защиты от спама и ботов');
DEFINE('_KUNENA_CAPDESC', 'Введите код');
DEFINE('_KUNENA_CAPERR', 'Код не правильный');
DEFINE('_KUNENA_COM_A_REPORT', 'Отчеты о Сообщениях');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Включите для получения отчета о любом пользовательском сообщении.');
DEFINE('_KUNENA_REPORT_MSG', 'Сообщение Доложено');
DEFINE('_KUNENA_REPORT_REASON', 'Причина');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Ваше Сообщение');
DEFINE('_KUNENA_REPORT_SEND', 'Отправить ');
DEFINE('_KUNENA_REPORT', 'Сообщить Модератору');
DEFINE('_KUNENA_REPORT_RSENDER', 'Отправитель жалобы:');
DEFINE('_KUNENA_REPORT_RREASON', 'Причина жалобы');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Текст жалобы:');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Автор сообщения:');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Тема сообщения:');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Сообщение:');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Ссылка на сообщение:');
DEFINE('_KUNENA_REPORT_INTRO', 'было послано вам сообщение потому что');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Уведомление отправлено!');
DEFINE('_KUNENA_EMOTICONS', 'Смайлики');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Смайлик');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Код');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Управление Смайликами');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Редактировать Смайлики');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'Полоса Смайликов');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Новый Смайлик');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Больше Смайликов');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Закрыть Окно');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Дополнительные Смайлики');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Выберите смайлик');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Поддержка мамботов Joomla');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Включение поддержки мамботов Joomla');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Настройка плагинов для "Мой профиль"');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Разрешить смену имени пользователя');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Разрешение смены имени пользователя на странице "Мой профиль"');
DEFINE ('_KUNENA_RECOUNTFORUMS', 'Обновление Показателей');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE', 'Статистика категорий будет обновлена..');
DEFINE ('_KUNENA_EDITING_REASON', 'Причина Редактирования');
DEFINE ('_KUNENA_EDITING_LASTEDIT', 'Последнее Редактирование');
DEFINE ('_KUNENA_BY', 'Кем');
DEFINE ('_KUNENA_REASON', 'Причина');
DEFINE('_GEN_GOTOBOTTOM', 'Перейти вниз');
DEFINE('_GEN_GOTOTOP', 'Перейти наверх');
DEFINE('_STAT_USER_INFO', 'Информация о пользователе');
DEFINE('_USER_SHOWEMAIL', 'Показать e-mail'); 
DEFINE('_KUNENA_HIDDEN_USERS', 'Скрытые пользователи');
DEFINE('_KUNENA_SAVE', 'Сохранить');
DEFINE('_KUNENA_RESET', 'Сбросить');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Галерея по умолчанию');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Персональная Информация');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Сводная информация');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Мой Аватар');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Настройки Форума');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Внешний Вид');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Информация о профиле');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Мои Сообщения');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Мои Подписки');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Моё Избранное');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Личные Сообщения');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Входящие');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Новое сообщение');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Исходящие');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Корзина');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Настройки');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Контакты');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Чёрный список');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Дополнительная информация');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Имя');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Имя пользователя');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'E-mail');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Тип пользователя');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Дата регистрации');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Дата последнего визита');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Сообщений');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Просмотров профиля');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Персональный текст');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Пол');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Дата рождения');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Год (ГГГГ) - Месяц (ММ) - День (ДД)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Место');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'Ваш ICQ номер.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Ваш "AOL Instant Messenger" псевдоним.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Ваш "Yahoo! Instant Messenger" псевдоним.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Ваш "Skype" псевдоним.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'Ваш "Goggle Talk" псевдоним.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Сайт');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Название сайта');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Например: kunena.ru');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL сайта');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Например: www.Kunena.ru');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Ваш MSN адрес.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Подпись');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Мужской');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Женский');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Сообщения были успешно удалены');
DEFINE('_KUNENA_DATE_YEAR', 'Год');
DEFINE('_KUNENA_DATE_MONTH', 'Месяц');
DEFINE('_KUNENA_DATE_WEEK', 'Неделя');
DEFINE('_KUNENA_DATE_DAY', 'День');
DEFINE('_KUNENA_DATE_HOUR', 'Час');
DEFINE('_KUNENA_DATE_MINUTE', 'Минута');
DEFINE('_KUNENA_IN_FORUM', ' на Форуме: ');
DEFINE('_KUNENA_FORUM_AT', ' Форум с:');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Примечание: если BB-коды или смайлики не показаны, то они всё равно могут применяться');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Инструменты');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Список пользователей');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS', '%s имеет <b>%d</b> зарегистрированных пользователей');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT', 'Введите значение для поиска!');
DEFINE ('_KUNENA_USRL_SEARCH', 'Найти пользователя');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON', 'Поиск');
DEFINE ('_KUNENA_USRL_LIST_ALL', 'Вывести всех');
DEFINE ('_KUNENA_USRL_NAME', 'Имя');
DEFINE ('_KUNENA_USRL_USERNAME', 'Псевдоним');
DEFINE ('_KUNENA_USRL_GROUP', 'Группа');
DEFINE ('_KUNENA_USRL_POSTS', 'Сообщений');
DEFINE ('_KUNENA_USRL_KARMA', 'Карма');
DEFINE ('_KUNENA_USRL_HITS', 'Нажатий');
DEFINE ('_KUNENA_USRL_EMAIL', 'E-mail');
DEFINE ('_KUNENA_USRL_USERTYPE', 'Тип');
DEFINE ('_KUNENA_USRL_JOIN_DATE', 'Дата регистрации');
DEFINE ('_KUNENA_USRL_LAST_LOGIN', 'Последний вход');
DEFINE ('_KUNENA_USRL_NEVER', 'Никогда');
DEFINE ('_KUNENA_USRL_ONLINE', 'Статус');
DEFINE ('_KUNENA_USRL_AVATAR', 'Картинка');
DEFINE ('_KUNENA_USRL_ASC', 'Возрастание');
DEFINE ('_KUNENA_USRL_DESC', 'Убывание');
DEFINE ('_KUNENA_USRL_DISPLAY_NR', 'Отображение');
DEFINE ('_KUNENA_USRL_DATE_FORMAT', '%m/%d/%Y');

DEFINE ('_KUNENA_ADMIN_CONFIG_PLUGINS', 'Плагины');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST', 'Список пользователей');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC', 'Кол-во строк');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS', 'Кол-во строк');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE', 'Онлайн статус');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC', 'Показывать онлайн статус пользователей');

DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR', 'Отображать аватары');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC', 'Отображать аватары');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_NAME', 'Показывать настоящее имя');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME', 'Показывать псевдоним');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP', 'Показывать группу пользователя');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS', 'Показывать количество сообщений');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA', 'Показывать карму');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL', 'Показывать e-mail');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE', 'Показывать тип');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE', 'Показывать дату регистрации');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE', 'Показывать дату последнего визита');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC', '');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_HITS', 'Показывать количество просмотров профиля');
DEFINE ('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC', '');
DEFINE ('_KUNENA_DBWIZ', 'Помощник базы данных');
DEFINE ('_KUNENA_DBMETHOD', 'Пожалуйста выберите метод завершения установки:');
DEFINE ('_KUNENA_DBCLEAN', 'Чистая установка');
DEFINE ('_KUNENA_DBUPGRADE', 'Обновление с Joomlaboard');
DEFINE ('_KUNENA_TOPLEVEL', 'Категория верхнего уровня');
DEFINE ('_KUNENA_REGISTERED', 'Зарегистрирован');
DEFINE ('_KUNENA_PUBLICBACKEND', 'Публичная часть форума');
DEFINE ('_KUNENA_SELECTANITEMTO', 'Выберите элемент для');
DEFINE ('_KUNENA_ERRORSUBS', 'Что-то пошло неправильно удаляем сообщения и подписки');
DEFINE ('_KUNENA_WARNING', 'Внимание...');
DEFINE ('_KUNENA_CHMOD1', 'Необходимо выполнить "chmod 766" для того, чтобы файл был обновлен.');
DEFINE ('_KUNENA_YOURCONFIGFILEIS', 'Ваш конфигурационный файл');
DEFINE ('_KUNENA_KUNENA', 'Kunena');
DEFINE ('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE ('_KUNENA_CB', 'Community Builder');
DEFINE ('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE ('_KUNENA_UDDEIM', 'Uddeim');
DEFINE ('_KUNENA_JIM', 'JIM');
DEFINE ('_KUNENA_MISSUS', 'Missus');
DEFINE ('_KUNENA_SELECTTEMPLATE', 'Выберите шаблон');
DEFINE ('_KUNENA_CONFIGSAVED', 'Конфигурация сохранена.');
DEFINE ('_KUNENA_CONFIGNOTSAVED', 'КРИТИЧЕСКАЯ ОШИБКА: Конфигурация не может быть сохранена.');
DEFINE ('_KUNENA_TFINW', 'Файл не доступен для записи.');
DEFINE ('_KUNENA_FBCFS', 'Kunena CSS-файл сохранен.');
DEFINE ('_KUNENA_SELECTMODTO', 'Выберите модератора для');
DEFINE ('_KUNENA_CHOOSEFORUMTOPRUNE', 'Необходимо выбрать форум для очистки!');
DEFINE ('_KUNENA_DELMSGERROR', 'Ошибка удаления сообщений:');
DEFINE ('_KUNENA_DELMSGERROR1', 'Ошибка удаления текста сообщений:');
DEFINE ('_KUNENA_CLEARSUBSFAIL', 'Ошибка очистки подписок:');
DEFINE ('_KUNENA_FORUMPRUNEDFOR', 'Форум очищен до ');
DEFINE ('_KUNENA_PRUNEDAYS', 'дней');
DEFINE ('_KUNENA_PRUNEDELETED', 'Удалено:');
DEFINE ('_KUNENA_PRUNETHREADS', 'тем');
DEFINE ('_KUNENA_ERRORPRUNEUSERS', 'Ошибка очистки пользователей:');
DEFINE ('_KUNENA_USERSPRUNEDDELETED', 'Пользователи очищены; Удалены;'); 
DEFINE ('_KUNENA_FORUMCATEGORY', 'Категория Форума');
DEFINE ('_KUNENA_SAMPLWARN1', 'Полностью удостоверьтесь, что вы загружаете данные "для образца" в полностью пустые таблицы Kunena. Если в них что-то есть, то они не загрузятся.');
DEFINE ('_KUNENA_FORUM1', 'Форум 1');
DEFINE ('_KUNENA_SAMPLEPOST1', 'Образец Сообщения 1');
DEFINE ('_KUNENA_SAMPLEFORUM11', 'Образец Форума 1\r\n');
DEFINE ('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Образец Сообщения[/color][/size][/b]\nПоздравляем с новым форумом!\n\n[url=http://Kunena.com]- Kunena[/url]');
DEFINE ('_KUNENA_SAMPLESUCCESS', 'Примерные данные загружены');
DEFINE ('_KUNENA_SAMPLEREMOVED', 'Примерные данные удалены');
DEFINE ('_KUNENA_CBADDED', 'Профиль Community Builder добавлен');
DEFINE ('_KUNENA_IMGDELETED', 'Изображение удалено');
DEFINE ('_KUNENA_FILEDELETED', 'Файл удален');
DEFINE ('_KUNENA_NOPARENT', 'Нет родителя');
DEFINE ('_KUNENA_DIRCOPERR', 'Ошибка: Файл');
DEFINE ('_KUNENA_DIRCOPERR1', 'невозможно скопировать!\n');
DEFINE ('_KUNENA_INSTALL1', 'Компонент <strong>Форум Kunena</strong><em> для Joomla! </em> <br />&copy; 2008 - 2009 by www.Kunena.com<br />Все права защищены.'); 
DEFINE ('_KUNENA_INSTALL2', 'Перенос/Установка завершена успешно');

// added by aliyar
DEFINE ('_KUNENA_FORUMPRF_TITLE', 'Настройки профиля');
DEFINE ('_KUNENA_FORUMPRF', 'Профиль');
DEFINE ('_KUNENA_FORUMPRRDESC', 'Если у вас установлены Clexus PM или Community Builder, вы можете настроить Kunena для использования их профиля.');
DEFINE ('_KUNENA_USERPROFILE_PROFILE', 'Профиль');
DEFINE ('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Вид Профиля</b>');
DEFINE ('_KUNENA_USERPROFILE_MESSAGES', 'Все сообщения');
DEFINE ('_KUNENA_USERPROFILE_TOPICS', 'Темы');
DEFINE ('_KUNENA_USERPROFILE_STARTBY', 'Начато');
DEFINE ('_KUNENA_USERPROFILE_CATEGORIES', 'Категории');
DEFINE ('_KUNENA_USERPROFILE_DATE', 'Дата');
DEFINE ('_KUNENA_USERPROFILE_HITS', 'Просмотров');
DEFINE ('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Нет сообщений');
DEFINE ('_KUNENA_TOTALFAVORITE', 'Избранное:');
DEFINE ('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Количество колонок дочерних тем');
DEFINE ('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Количество колонок дочерних тем');
DEFINE ('_KUNENA_SUBSCRIPTIONSCHECKED', 'Подписка на сообщения отмечена по умолчанию?');
DEFINE ('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Установите "Да" чтобы переключатель подписки был всегда отмечен.');

// Errors (Re-integration from Joomlaboard 1.2)
DEFINE ('_KUNENA_ERROR1', 'Категория / Форум должны иметь название');

// Forum Configuration (New in Kunena)
DEFINE ('_KUNENA_SHOWSTATS', 'Отображать показатели');
DEFINE ('_KUNENA_SHOWSTATSDESC', 'Установите "Да" для отображения показателей');
DEFINE ('_KUNENA_SHOWWHOIS', 'Отображает "Кто онлайн"');
DEFINE ('_KUNENA_SHOWWHOISDESC', 'Установите "Да" для отображения "Кто онлайн"');
DEFINE ('_KUNENA_STATSGENERAL', 'Отображает главные показатели');
DEFINE ('_KUNENA_STATSGENERALDESC', 'Установите "Да" для отображения "Главных показателей"');
DEFINE ('_KUNENA_USERSTATS', 'Отображать Показатели популярных пользователей');
DEFINE ('_KUNENA_USERSTATSDESC', 'Установите "Да" для отображения "Показатели популярных пользователей"');
DEFINE ('_KUNENA_USERNUM', 'Количество популярных пользователей');
DEFINE ('_KUNENA_USERPOPULAR', 'Отображать показатели популярных тем');
DEFINE ('_KUNENA_USERPOPULARDESC', 'Установите "Да" для отображения "Показателей популярных тем"');
DEFINE ('_KUNENA_NUMPOP', 'Количество популярных тем');

DEFINE ('_KUNENA_INFORMATION', 'Команда Kunena');
DEFINE ('_KUNENA_INSTRUCTIONS', 'Инструкции');
DEFINE ('_KUNENA_FINFO', 'Информация форума Kunena');
DEFINE ('_KUNENA_CSSEDITOR', 'Редактор шаблонов CSS');
DEFINE ('_KUNENA_PATH', 'Путь:');
DEFINE ('_KUNENA_CSSERROR', 'Примечание: CSS-файл с шаблоном должен быть доступен на запись для внесения изменений.');

// User Management
DEFINE ('_KUNENA_FUM', 'Менеджер профилей пользователей Kunena');
DEFINE ('_KUNENA_SORTID', 'сорт. по userid');
DEFINE ('_KUNENA_SORTMOD', 'сорт. по модераторам');
DEFINE ('_KUNENA_SORTNAME', 'сорт. по именам');
DEFINE ('_KUNENA_VIEW', 'Вид');
DEFINE ('_KUNENA_NOUSERSFOUND', 'Профилей не найдено');
DEFINE ('_KUNENA_ADDMOD', 'Добавить модератора в');
DEFINE ('_KUNENA_NOMODSAV', 'Не найдено возможных модераторов. Читайте примечание ниже.');
DEFINE ('_KUNENA_NOTEUS', 'Примечание: Здесь отображаются только пользователи, которые отмечены как модераторы в их Профиле. Для того чтобы иметь возможность назначить модератора, добавьте этому пользователю флаг модератора в его профиле в секции <a href="index2.php?option=com_kunena&task=profiles">Управление пользователями</a> затем выберите этот профиль и обновите его. Флаг модератора может добавлять только администратор сайта.'); 
DEFINE ('_KUNENA_PROFFOR', 'Профиль для');
DEFINE ('_KUNENA_GENPROF', 'Главные установки профиля');
DEFINE ('_KUNENA_PREFVIEW', 'Предпочитаемый способ просмотра:');
DEFINE ('_KUNENA_PREFOR', 'Предпочитаемый порядок вывода сообщений:');
DEFINE ('_KUNENA_ISMOD', 'Модератор:');
DEFINE ('_KUNENA_ISADM', '<strong>Да</strong> (не изменяется, этот пользователь сайт-(супер)администратор)');
DEFINE ('_KUNENA_COLOR', 'Цвет');
DEFINE ('_KUNENA_UAVATAR', 'Пользовательский Аватар');
DEFINE ('_KUNENA_NS', 'Ничего не выбрано');
DEFINE ('_KUNENA_DELSIG', 'отметить здесь для удаления подписи');
DEFINE ('_KUNENA_DELAV', 'отметить здесь для удаления аватара');
DEFINE ('_KUNENA_SUBFOR', 'Подписки для');
DEFINE ('_KUNENA_NOSUBS', 'Для данного пользователя подписки не найдены');

// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE ('_KUNENA_BASICS', 'Основное');
DEFINE ('_KUNENA_BASICSFORUM', 'Основная информация форума');
DEFINE ('_KUNENA_PARENT', 'Родитель:');
DEFINE ('_KUNENA_PARENTDESC', 'Примечание: Для создания категории выберите "Категория верхнего уровня" в качестве родителя. Категория служит в качестве контейнера для форумов. <br />Форум может быть создан <strong>только</strong> внутри выбранной в качестве родителя категории (предварительно созданной).<br /> Сообщения <strong>НЕ</strong> пишутся в категории, только в форумы.');
DEFINE ('_KUNENA_BASICSFORUMINFO', 'Название форума и его описание');
DEFINE ('_KUNENA_NAMEADD', 'Название');
DEFINE ('_KUNENA_DESCRIPTIONADD', 'Описание');
DEFINE ('_KUNENA_ADVANCEDDESC', 'Дополнительные настройки форума');
DEFINE ('_KUNENA_ADVANCEDDESCINFO', 'Безопасность и доступ к форуму');
DEFINE ('_KUNENA_LOCKEDDESC', 'Установите "Да" если вы хотите закрыть доступ для всех кроме модераторов и администраторов.');
DEFINE ('_KUNENA_LOCKED1', 'Закрыто:'); 
DEFINE ('_KUNENA_PUBACC', 'Уровень общего доступа:');
DEFINE ('_KUNENA_PUBACCDESC', 'Для создания закрытого раздела необходимо указать минимальный уровень пользователя который может видеть/заходить в этот раздел. По умолчанию минимальный уровень "Все". <br /><b>Примечание</b>: если запретить доступ для целой категории для одной или больше групп пользователей, то это скроет все форумы внутри данной категории, даже если некоторые из них имеют более низкий уровень привилегий для просмотра. ');
DEFINE ('_KUNENA_CGROUPS', 'Включая дочерние группы:');
DEFINE ('_KUNENA_CGROUPSDESC', 'Если установить "Нет" запрет доступа будет действовать <b>только</b> для выбранных групп');
DEFINE ('_KUNENA_ADMINLEVEL', 'Уровень административного доступа:');
DEFINE ('_KUNENA_ADMINLEVELDESC', 'Если вы создаете форум с ограничение общего доступа, то можно указать дополнительный уровень административного доступа.<br /> Если вы запрещаете доступ к форуму для специальной группы пользователей "Public Frontend" и не укажете "Public Backend" группу здесь, администраторы не смогут войти/просмотреть форум.'); 
DEFINE ('_KUNENA_ADVANCED', 'Дополнительно');
DEFINE ('_KUNENA_CGROUPS1', 'Включая дочерние группы:');
DEFINE ('_KUNENA_CGROUPS1DESC', 'Если установить "Нет" запрет доступа будет действовать <b>только</b> для выбранных групп');
DEFINE ('_KUNENA_REV', 'Обзор сообщений:');
DEFINE ('_KUNENA_REVDESC', 'Установите "Да" для предварительной проверки сообщений модераторами перед публикацией на форуме. Эта функция полезна только в Модерируемых разделах. Если вы включите данную функцию и ни один модератор не будет назначен, то администратор сайта самостоятельно будет отвечать за проверку/удаление сообщений.');
DEFINE ('_KUNENA_MOD_NEW', 'Модерация');
DEFINE ('_KUNENA_MODNEWDESC', 'Модерация форума и модераторы');
DEFINE ('_KUNENA_MOD', 'Проверено:');
DEFINE ('_KUNENA_MODDESC', 'Установите "Да" для назначения Модераторов для этого форума. <br /><strong>Примечание:</strong> Это не означает что новые сообщения должны быть проверены перед публикацией их на форуме!<br /> Необходимо установить опцию "Обзор" на закладке "Дополнительно"<br /><br /> <strong>Please do note:</strong>');
DEFINE ('_KUNENA_MODHEADER', 'Настройки модерации для этого форума');
DEFINE ('_KUNENA_MODSASSIGNED', 'Модераторы назначенные для этого форума:');
DEFINE ('_KUNENA_NOMODS', 'Для этого форума не назначено ни одного модератора');

// Some General Strings (Improvement in Kunena)
DEFINE ('_KUNENA_EDIT', 'Редактировать');
DEFINE ('_KUNENA_ADD', 'Добавить');

// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE ('_KUNENA_MOVEUP', 'Вверх');
DEFINE ('_KUNENA_MOVEDOWN', 'Вниз');

// Groups - Integration in Kunena
DEFINE ('_KUNENA_ALLREGISTERED', 'Все зарегистрированные');
DEFINE ('_KUNENA_EVERYBODY', 'Все');

// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE ('_KUNENA_REORDER', 'Порядок');
DEFINE ('_KUNENA_CHECKEDOUT', 'Чекаут');
DEFINE ('_KUNENA_ADMINACCESS', 'Административный доступ');
DEFINE ('_KUNENA_PUBLICACCESS', 'Общий доступ');
DEFINE ('_KUNENA_PUBLISHED', 'Опубликовано');
DEFINE ('_KUNENA_REVIEW', 'Рецензия');
DEFINE ('_KUNENA_MODERATED', 'Модерация');
DEFINE ('_KUNENA_LOCKED', 'Закрытый');
DEFINE ('_KUNENA_CATFOR', 'Категория / Форум');
DEFINE ('_KUNENA_ADMIN', 'Администрирование Kunena');
DEFINE ('_KUNENA_CP', 'Панель управления Kunena');

// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE ('_COM_A_AVATAR_INTEGRATION', 'Интеграция аватаров');
DEFINE ('_COM_A_RANKS_SETTINGS', 'Ранги');
DEFINE ('_COM_A_RANKING_SETTINGS', 'Настройка рангов');
DEFINE ('_COM_A_AVATAR_SETTINGS', 'Настройка аватаров');
DEFINE ('_COM_A_SECURITY_SETTINGS', 'Настройки безопасности');
DEFINE ('_COM_A_BASIC_SETTINGS', 'Основные настройки');

// Kunena 1.0.0
DEFINE ('_COM_A_FAVORITES', 'Разрешить Избранное');
DEFINE ('_COM_A_FAVORITES_DESC', 'Позволяет зарегистрированным пользователям добавлять темы в Избранное');
DEFINE ('_USER_UNFAVORITE_ALL', 'Установите этот флажок чтобы <b><u>удалить</u></b> из Избранного все темы (включая невидимые)');
DEFINE ('_VIEW_FAVORITETXT', 'Добавить в Избранное');
DEFINE ('_USER_UNFAVORITE_YES', 'Тема была исключена из Избранного.');
DEFINE ('_POST_FAVORITED_TOPIC', 'Тема была добавлена в Избранное.');
DEFINE ('_VIEW_UNFAVORITETXT', 'Исключить из Избранного');
DEFINE ('_VIEW_UNSUBSCRIBETXT', 'Отписаться');
DEFINE ('_USER_NOFAVORITES', 'Нет избранных тем');
DEFINE ('_POST_SUCCESS_FAVORITE', 'Ваш запрос на добавление в Избранное был обработан.');
DEFINE ('_COM_A_MESSAGES_SEARCH', 'Результаты поиска');
DEFINE ('_COM_A_MESSAGES_DESC_SEARCH', 'Сообщений на страницу (для Результатов поиска)');
DEFINE ('_KUNENA_USE_JOOMLA_STYLE', 'Использовать стиль Joomla?');
DEFINE ('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Установите "Да" для использования стилей Joomla. (class: like sectionheader, sectionentry1 ...)');
DEFINE ('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Отображать Иконки дочерних категорий');
DEFINE ('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Установите "Да" для отображения маленьких иконок дочерних категорий.');
DEFINE ('_KUNENA_SHOW_ANNOUNCEMENT', 'Отображать Анонсы');
DEFINE ('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Установите "Да" для отображения блоков с анонсами на домашней странице форума.');
DEFINE ('_KUNENA_SHOW_AVATAR_ON_CAT', 'Показывать аватары в списке категорий?');
DEFINE ('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Установите "Да" для отображения аватар пользователей в списке категорий.');
DEFINE ('_KUNENA_RECENT_POSTS', 'Настройки Последних сообщений');
DEFINE ('_KUNENA_SHOW_LATEST_MESSAGES', 'Отображать Последние обсуждения');
DEFINE ('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Установите "Да" для отображения плагина популярных обсуждений на вашем форуме.');
DEFINE ('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Количество Последних обсуждений');
DEFINE ('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Количество Последних обсуждений');
DEFINE ('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Количество на вкладке');
DEFINE ('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Количество сообщений на одной вкладке');
DEFINE ('_KUNENA_LATEST_CATEGORY', 'Отображать Категорию');
DEFINE ('_KUNENA_LATEST_CATEGORY_DESC', 'Укажите категорию для отображения последних сообщений. Например: 2,3,7');
DEFINE ('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Отображать Тему');
DEFINE ('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Отображать Одиночную Тему');
DEFINE ('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Отображать Тему Ответа');
DEFINE ('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Отображать Тему Ответа (Re:)');
DEFINE ('_KUNENA_LATEST_SUBJECT_LENGTH', 'Длина Темы');
DEFINE ('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Длина Темы');
DEFINE ('_KUNENA_SHOW_LATEST_DATE', 'Отображать Дату');
DEFINE ('_KUNENA_SHOW_LATEST_DATE_DESC', 'Отображать Дату');
DEFINE ('_KUNENA_SHOW_LATEST_HITS', 'Отображать Просмотры');
DEFINE ('_KUNENA_SHOW_LATEST_HITS_DESC', 'Отображать Просмотры');
DEFINE ('_KUNENA_SHOW_AUTHOR', 'Отображать Автора');
DEFINE ('_KUNENA_SHOW_AUTHOR_DESC', '1=псевдоним, 2=настоящее имя, 0=не отображать');
DEFINE ('_KUNENA_STATS', 'Настройки Плагина Статистики');
DEFINE ('_KUNENA_CATIMAGEPATH', 'Путь Изображений для Категорий');
DEFINE ('_KUNENA_CATIMAGEPATH_DESC', 'Путь Изображений для Категорий. Если установить "category_images/" путь будет "your_html_rootfolder/images/fbfiles/category_images/"');
DEFINE ('_KUNENA_ANN_MODID', 'ID Модераторов Анонсов');
DEFINE ('_KUNENA_ANN_MODID_DESC', 'Добавьте ID пользователей для модерации анонсов, т.е. 62,63,73. Модераторы анонсов могут добавлять, удалять и редактировать анонсы.');

DEFINE ('_KUNENA_FORUM_TOP', 'Категории Форума');
DEFINE ('_KUNENA_CHILD_BOARDS', 'Дочерние Форумы');
DEFINE ('_KUNENA_QUICKMSG', 'Быстрый Ответ');
DEFINE ('_KUNENA_THREADS_IN_FORUM', 'Темы Форума');
DEFINE ('_KUNENA_FORUM', 'Форум');
DEFINE ('_KUNENA_SPOTS', 'Эксклюзивы');
DEFINE ('_KUNENA_CANCEL', 'Отмена');
DEFINE ('_KUNENA_TOPIC', 'Тема:');
DEFINE ('_KUNENA_POWEREDBY', 'Работает на ');

// Time Format
DEFINE ('_TIME_TODAY', '<b>Сегодня</b>');
DEFINE ('_TIME_YESTERDAY', '<b>Вчера</b>');

// STARTS HERE!
DEFINE ('_KUNENA_WHO_LATEST_POSTS', 'Последние Сообщения');
DEFINE ('_KUNENA_WHO_WHOISONLINE', 'Кто в on-line');
DEFINE ('_KUNENA_WHO_MAINPAGE', 'Кто на Главной');
DEFINE ('_KUNENA_GUEST', 'Гость');
DEFINE ('_KUNENA_PATHWAY_VIEWING', 'просм.');
DEFINE ('_KUNENA_ATTACH', 'Присоединено');

// Favorite
DEFINE ('_KUNENA_FAVORITE', 'Избранное');
DEFINE ('_USER_FAVORITES', 'Моё Избранное');
DEFINE ('_THREAD_UNFAVORITE', 'Удалить из Избранного');

// profilebox
DEFINE ('_PROFILEBOX_WELCOME', 'Добро пожаловать');
DEFINE ('_PROFILEBOX_SHOW_LATEST_POSTS', 'Последние Сообщения');
DEFINE ('_PROFILEBOX_SET_MYAVATAR', 'Установить Аватар');
DEFINE ('_PROFILEBOX_MYPROFILE', 'Мой Профиль');
DEFINE ('_PROFILEBOX_SHOW_MYPOSTS', 'Мои Сообщения');
DEFINE ('_PROFILEBOX_GUEST', 'Гость');
DEFINE ('_PROFILEBOX_LOGIN', 'Вход');
DEFINE ('_PROFILEBOX_REGISTER', 'Зарегистрируйтесь');
DEFINE ('_PROFILEBOX_LOGOUT', 'Выход');
DEFINE ('_PROFILEBOX_LOST_PASSWORD', 'Забыли пароль?');
DEFINE ('_PROFILEBOX_PLEASE', 'Пожалуйста выполните');
DEFINE ('_PROFILEBOX_OR', 'или');

// recentposts
DEFINE ('_RECENT_RECENT_POSTS', 'Недавние Сообщения');
DEFINE ('_RECENT_TOPICS', 'Тема');
DEFINE ('_RECENT_AUTHOR', 'Автор');
DEFINE ('_RECENT_CATEGORIES', 'Категории');
DEFINE ('_RECENT_DATE', 'Дата');
DEFINE ('_RECENT_HITS', 'Просмотров');

// announcement
DEFINE ('_ANN_ANNOUNCEMENTS', 'Анонсы');
DEFINE ('_ANN_ID', 'ID');
DEFINE ('_ANN_DATE', 'Дата');
DEFINE ('_ANN_TITLE', 'Заголовок');
DEFINE ('_ANN_SORTTEXT', 'Короткий текст');
DEFINE ('_ANN_LONGTEXT', 'Длинный текст');
DEFINE ('_ANN_ORDER', 'Порядок');
DEFINE ('_ANN_PUBLISH', 'Опубликовать');
DEFINE ('_ANN_PUBLISHED', 'Опубликовано');
DEFINE ('_ANN_UNPUBLISHED', 'Отмена публикации');
DEFINE ('_ANN_EDIT', 'Редактировать');
DEFINE ('_ANN_DELETE', 'Удалить');
DEFINE ('_ANN_SUCCESS', 'Успешно');
DEFINE ('_ANN_SAVE', 'Сохранить');
DEFINE ('_ANN_YES', 'Да');
DEFINE ('_ANN_NO', 'Нет');
DEFINE ('_ANN_ADD', 'Добавить новый');
DEFINE ('_ANN_SUCCESS_EDIT', 'Успешно отредактировано');
DEFINE ('_ANN_SUCCESS_ADD', 'Успешно добавлено');
DEFINE ('_ANN_DELETED', 'Успешно удалено');
DEFINE ('_ANN_ERROR', 'Ошибка');
DEFINE ('_ANN_READMORE', 'Подробнее...');
DEFINE ('_ANN_CPANEL', 'Панель управления анонсами');
DEFINE ('_ANN_SHOWDATE', 'Отображать дату');

// Stats
DEFINE ('_STAT_FORUMSTATS', 'Показатели форума');
DEFINE ('_STAT_GENERAL_STATS', 'Главные показатели');
DEFINE ('_STAT_TOTAL_USERS', 'Всего пользователей');
DEFINE ('_STAT_LATEST_MEMBERS', 'Последний зарегистрировавшийся');
DEFINE ('_STAT_PROFILE_INFO', 'Посмотреть профиль');
DEFINE ('_STAT_TOTAL_MESSAGES', 'Всего сообщений');
DEFINE ('_STAT_TOTAL_SUBJECTS', 'Всего тем');
DEFINE ('_STAT_TOTAL_CATEGORIES', 'Всего категорий');
DEFINE ('_STAT_TOTAL_SECTIONS', 'Всего секций');
DEFINE ('_STAT_TODAY_OPEN_THREAD', 'Открыто сегодня');
DEFINE ('_STAT_YESTERDAY_OPEN_THREAD', 'Открыто вчера');
DEFINE ('_STAT_TODAY_TOTAL_ANSWER', 'Всего ответов сегодня');
DEFINE ('_STAT_YESTERDAY_TOTAL_ANSWER', 'Всего ответов вчера');
DEFINE ('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Посмотреть новые сообщения');
DEFINE ('_STAT_MORE_ABOUT_STATS', 'Подробнее о показателях');
DEFINE ('_STAT_USERLIST', 'Список пользователей');
DEFINE ('_STAT_TEAMLIST', 'Команда форума');
DEFINE ('_STATS_FORUM_STATS', 'Показатели форума');
DEFINE ('_STAT_POPULAR', 'Популярное');
DEFINE ('_STAT_POPULAR_USER_TMSG', 'Пользователей (Сообщений)');
DEFINE ('_STAT_POPULAR_USER_KGSG', 'Тем');
DEFINE ('_STAT_POPULAR_USER_GSG', 'Пользователи (Просмотров профилей)');

//Team List
DEFINE ('_MODLIST_ONLINE', 'Пользователи онлайн');
DEFINE ('_MODLIST_OFFLINE', 'Пользователи оффлайн');

// Whoisonline
DEFINE ('_WHO_WHOIS_ONLINE', 'Кто Онлайн');
DEFINE ('_WHO_ONLINE_NOW', 'Онлайн');
DEFINE ('_WHO_ONLINE_MEMBERS', 'Пользователи');
DEFINE ('_WHO_AND', 'и');
DEFINE ('_WHO_ONLINE_GUESTS', 'Гости');
DEFINE ('_WHO_ONLINE_USER', 'Пользователь');
DEFINE ('_WHO_ONLINE_TIME', 'Время');
DEFINE ('_WHO_ONLINE_FUNC', 'Действие');

// Userlist
DEFINE ('_USRL_USERLIST', 'Список пользователей');
DEFINE ('_USRL_REGISTERED_USERS', '%s имеет <b>%d</b> зарегистрированных пользователей');
DEFINE ('_USRL_SEARCH_ALERT', 'Введите значение для поиска!');
DEFINE ('_USRL_SEARCH', 'Найти пользователя');
DEFINE ('_USRL_SEARCH_BUTTON', 'Поиск');
DEFINE ('_USRL_LIST_ALL', 'Вывести всё');
DEFINE ('_USRL_NAME', 'Имя');
DEFINE ('_USRL_USERNAME', 'Псевдоним');
DEFINE ('_USRL_EMAIL', 'E-mail');
DEFINE ('_USRL_USERTYPE', 'Тип');
DEFINE ('_USRL_JOIN_DATE', 'Зарегистрирован');
DEFINE ('_USRL_LAST_LOGIN', 'Последний вход');
DEFINE ('_USRL_NEVER', 'Никогда');
DEFINE ('_USRL_BLOCK', 'Статус');
DEFINE ('_USRL_MYPMS2', '');
DEFINE ('_USRL_ASC', 'Возр');
DEFINE ('_USRL_DESC', 'Убыв');
DEFINE ('_USRL_DATE_FORMAT', '%m/%d/%Y');
DEFINE ('_USRL_TIME_FORMAT', '%H:%M');
DEFINE ('_USRL_USEREXTENDED', 'Детали');
DEFINE ('_USRL_COMPROFILER', 'Профиль');
DEFINE ('_USRL_THUMBNAIL', 'Миниатюра');
DEFINE ('_USRL_READON', 'показать');
DEFINE ('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE ('_USRL_MYPMSPRO_SENDPM', 'Отправить ЛС');
DEFINE ('_USRL_JIM', 'JIM');
DEFINE ('_USRL_UDDEIM', 'UDDEIM');
DEFINE ('_USRL_SEARCHRESULT', 'Результаты поиска для');
DEFINE ('_USRL_STATUS', 'Статус');
DEFINE ('_USRL_LISTSETTINGS', 'Настройки списка пользователей');
DEFINE ('_USRL_ERROR', 'Ошибка');

//changed in 1.1.4 stable
DEFINE ('_COM_A_PMS_TITLE', 'Компонент личных сообщений');
DEFINE ('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE ('_FORUM_SEARCH', 'Поиск: %s');
DEFINE ('_MODERATION_DELETE_MESSAGE', 'Вы уверены что хотите удалить это сообщение? \n\n Примечание: Удаленные сообщения восстановить невозможно!');
DEFINE ('_MODERATION_DELETE_SUCCESS', 'Сообщение(я) удалено');
DEFINE ('_COM_A_RANKING', 'Ранги');
DEFINE ('_COM_A_BOT_REFERENCE', 'Отображать "Bot Reference Chart"');
DEFINE ('_COM_A_MOSBOT', 'Включить Разговорного Бота');
DEFINE ('_PREVIEW', 'Просмотр');
DEFINE ('_COM_A_MOSBOT_TITLE', 'Разговорный Бот');
DEFINE('_COM_A_MOSBOT_DESC', 'The Discuss Bot enables your users to discuss content items in the forums. The Content Title is used as the topic subject.'
           . '<br />If a topic does not exist yet a new one is created. If the topic already exists, the user is shown the thread and (s)he can reply.' . '<br /><strong>You will need to download and install the Bot separately.</strong>'
           . '<br />check the <a href="http://www.Kunena.com">Kunena Site</a> for more information.' . '<br />When Installed you will need to add the following bot lines to your Content:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the Content Item can be discussed. To find the proper catid, just look into the forums ' . 'and check the category id from the URLs from your browsers status bar.'
           . '<br />Example: if you want the article discussed in Forum with catid 26, the Bot should look like: {mos_fb_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each Content Item to be discussed in a matching forum.');

//new in 1.1.4 stable
// search.class.php
DEFINE ('_FORUM_SEARCHTITLE', 'Поиск');
DEFINE ('_FORUM_SEARCHRESULTS', 'выведено %s из %s результатов.');

// Help, FAQ
DEFINE ('_COM_FORUM_HELP', 'ЧаВО');

// rules.php
DEFINE ('_COM_FORUM_RULES', 'Правила');
DEFINE ('_COM_FORUM_RULES_DESC', '<ul><li>Измените этот файл чтобы вставить свои правила joomlaroot/administrator/components/com_kunena/language/kunena.russian.php</li><li>Правило 2</li><li>Правило 3</li><li>Правило 4</li><li>...</li></ul>');

//smile.class.php
DEFINE ('_COM_BOARDCODE', 'Boardcode');

// moderate_messages.php
DEFINE ('_MODERATION_APPROVE_SUCCESS', 'Сообщение(я) утверждено(ы)');
DEFINE ('_MODERATION_DELETE_ERROR', 'ОШИБКА: Сообщение(я) невозможно удалить');
DEFINE ('_MODERATION_APPROVE_ERROR', 'ОШИБКА: Сообщение(я) невозможно утвердить');

// listcat.php
DEFINE ('_GEN_NOFORUMS', 'В данной категории нет форумов!');

//new in 1.1.3 stable
DEFINE ('_POST_GHOST_FAILED', 'Невозможно создать "ghost" тему в старом форуме!');
DEFINE ('_POST_MOVE_GHOST', 'Оставить "ghost" сообщение в старом форуме');

//new in 1.1 Stable
DEFINE ('_GEN_FORUM_JUMP', 'Быстрый переход по форумам ');
DEFINE ('_COM_A_FORUM_JUMP', 'Включить быстрый переход по форумам');
DEFINE ('_COM_A_FORUM_JUMP_DESC', 'Установите "Да" чтобы включить отображение списка для быстрого переключения форумов и категорий.');

//new in 1.1 RC1
DEFINE ('_GEN_RULES', 'Правила');
DEFINE ('_COM_A_RULESPAGE', 'Включить страницу правил');
DEFINE ('_COM_A_RULESPAGE_DESC', 'Установите "Да" чтобы включить пункт "Правила" в верхнем меню форума. Страницу с правилами можно редактировать в файле rules.php расположенном в /joomla_root/components/com_kunena. <em>Обязательно делайте резервную копию этого файла т.к. после обновления он будет перезаписан!</em>');
DEFINE ('_MOVED_TOPIC', 'ПЕРЕНЕСЕНО:');
DEFINE ('_COM_A_PDF', 'Включить создание PDF');
DEFINE ('_COM_A_PDF_DESC', 'Установите "Да" чтобы включить возможность пользователям скачивать простые PDF документы с содержимым всей темы. <br />Это <u>простой</u> PDF документ без разметки, оформления и прочего но он содержит весь текст темы.');
DEFINE ('_GEN_PDFA', 'Нажмите эту кнопку для создания PDF документа содержащего данную тему (откроется в новом окне).');
DEFINE ('_GEN_PDF', 'Pdf');

//new in 1.0.4 stable
DEFINE ('_VIEW_PROFILE', 'Нажмите чтобы посмотреть профиль этого пользователя');
DEFINE ('_VIEW_ADDBUDDY', 'Нажмите чтобы добавить пользователя в список приятелей');
DEFINE ('_POST_SUCCESS_POSTED', 'Ваше сообщение было успешно опубликовано');
DEFINE ('_POST_SUCCESS_VIEW', '[Возврат к теме]');
DEFINE ('_POST_SUCCESS_FORUM', '[Возврат к форуму]');
DEFINE ('_RANK_ADMINISTRATOR', 'Админ');
DEFINE ('_RANK_MODERATOR', 'Модератор');
DEFINE ('_SHOW_LASTVISIT', 'С последнего посещения');
DEFINE ('_COM_A_BADWORDS_TITLE', 'Фильтр нецензурных выражений');
DEFINE ('_COM_A_BADWORDS', 'Использовать фильтрацию нецензурных выражений');
DEFINE ('_COM_A_BADWORDS_DESC', 'Установите "Да" для фильтрации сообщений содержащих нецензурные выражения. Список нецензурных сообщений настраивается в компоненте "Badword Component". Для использования данной функции компонент "Badword Component" должен быть установлен.');
DEFINE ('_COM_A_BADWORDS_NOTICE', '* Это сообщение подверглось цензуре *');
DEFINE ('_COM_A_COMBUILDER_PROFILE', 'Создать профиль форума "Community Builder"');
DEFINE ('_COM_A_COMBUILDER_PROFILE_DESC', 'Нажмите на ссылку чтобы создать необходимые поля для форума в пользовательском профиле "Community Builder". После того как они будут созданы вы можете свободно перенести куда угодно используя админа Community Builder, просто не переименовывайте их имена или установки. Если вы удалите их из Community Builder вы можете создать их заново используя эту ссылку, в противном случае не нажимайте на ссылку несколько раз!');    
DEFINE ('_COM_A_COMBUILDER_PROFILE_CLICK', '> Нажмите здесь <');
DEFINE ('_COM_A_COMBUILDER', 'Пользовательские профили Community Builder');
DEFINE ('_COM_A_COMBUILDER_DESC', 'Установите "Да" чтобы активировать интеграцию с компонентом "Community Builder" (www.joomlapolis.com). Все функции пользовательских профилей Kunena будут обрабатываться Community Builder и ссылка на профиль в форуме будет переходить на пользовательский профиль Community Builder. Эта опция переопределяет "Настройку Clexus PM профиля", если обе опции включены. Также убедитесь что вы применили требуемые изменения в базе данных Community Builder используя опцию ниже.');
DEFINE ('_COM_A_AVATAR_SRC', 'Использовать аватары из');
DEFINE ('_COM_A_AVATAR_SRC_DESC', 'Если у вас установлен компонент JomSocial, Clexus PM или Community Builder, вы можете настроить Kunena для использования аватар из профилей этих компонент. Примечание: Для Community Builder необходимо чтобы была включена опция миниатюр "thumbnail" потому что форум использует миниатюрные картинки а не оригинальные.');    
DEFINE ('_COM_A_KARMA', 'Показывать Индикатор Кармы');
DEFINE ('_COM_A_KARMA_DESC', 'Установите "Да" чтобы отображать карму пользователя и соответствующие кнопки (увеличить / уменьшить) если включена опция "Пользовательские Показатели".');
DEFINE ('_COM_A_DISEMOTICONS', 'Отключить смайлики');
DEFINE ('_COM_A_DISEMOTICONS_DESC', 'Установите "Да" чтобы полностью отключить графические смайлики.');
DEFINE ('_COM_C_FBCONFIG', 'Конфигурация Kunena');
DEFINE ('_COM_C_FBCONFIGDESC', 'Настройка всех функций Kunena');
DEFINE ('_COM_C_FORUM', 'Управление Форумом');
DEFINE ('_COM_C_FORUMDESC', 'Добавление категорий/форумов и их настройка');
DEFINE ('_COM_C_USER', 'Управление Пользователями');
DEFINE ('_COM_C_USERDESC', 'Основное управление пользователями и их профилями');
DEFINE ('_COM_C_FILES', 'Обзор загр. файлов');
DEFINE ('_COM_C_FILESDESC', 'Обзор и управление загруженными файлами');
DEFINE ('_COM_C_IMAGES', 'Обзор загр. изображений');
DEFINE ('_COM_C_IMAGESDESC', 'Обзор и управление загруженными изображениями');
DEFINE ('_COM_C_CSS', 'Изменение CSS-файла');
DEFINE ('_COM_C_CSSDESC', 'Изменение внешнего вида Kunena');
DEFINE ('_COM_C_SUPPORT', 'Сайт Поддержки');
DEFINE ('_COM_C_SUPPORTDESC', 'Связаться с сайтом Kunena (новое окно)');
DEFINE ('_COM_C_PRUNETAB', 'Очистка форумов');
DEFINE ('_COM_C_PRUNETABDESC', 'Удаление старых тем (настраиваемо)');
DEFINE ('_COM_C_PRUNEUSERS', 'Очистка пользователей');  
DEFINE ('_COM_C_LOADSAMPLEDESC', 'Для быстрого старта: загрузите тестовые данные в пустую базу Kunena');
DEFINE ('_COM_C_REMOVESAMPLE', 'Удаление тестовых данных');
DEFINE ('_COM_C_REMOVESAMPLEDESC', 'Удаление тестовых данных из базы');
DEFINE ('_COM_C_LOADMODPOS', 'Загрузка позиций модулей');
DEFINE ('_COM_C_LOADMODPOSDESC', 'Загрузка позиций модулей для шаблона Kunena');
DEFINE ('_COM_C_UPGRADEDESC', 'Обновление базы данных до последней версии после обновления Kunena.');
DEFINE ('_COM_C_BACK', 'Возврат к Панели управления Kunena');
DEFINE ('_SHOW_LAST_SINCE', 'Активные темы с момента последнего визита:');
DEFINE ('_POST_SUCCESS_REQUEST2', 'Ваш запрос обработан');
DEFINE ('_POST_NO_PUBACCESS3', 'Нажмите здесь для регистрации.');

//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE ('_POST_SUCCESS_DELETE', 'Сообщение было успешно удалено.');
DEFINE ('_POST_SUCCESS_EDIT', 'Сообщение было успешно изменено.');
DEFINE ('_POST_SUCCESS_MOVE', 'Тема была успешно перенесена.');
DEFINE ('_POST_SUCCESS_POST', 'Ваше сообщение было успешно опубликовано.');
DEFINE ('_POST_SUCCESS_SUBSCRIBE', 'Ваша подписка была обработана.');
//==================================================================================================

//new in 1.0.3 stable
//Karma
DEFINE ('_KARMA', 'Карма');
DEFINE ('_KARMA_SMITE', 'Поразил');
DEFINE ('_KARMA_APPLAUD', 'Аплодисменты');
DEFINE ('_KARMA_BACK', 'Вернуться к теме,');
DEFINE ('_KARMA_WAIT', 'Вы можете изменить карму только одной персоне в течение 6 часов.<br/>Подождите пока закончится этот период, чтобы поменять карму кому-нибудь другому.');
DEFINE ('_KARMA_SELF_DECREASE', 'Не пытайтесь уменьшить свою собственную карму!');
DEFINE ('_KARMA_SELF_INCREASE', 'Выша карма была уменьшена за попытку ее увеличить самому себе!');
DEFINE ('_KARMA_DECREASED', 'Карма пользователя уменьшена. Если вы не переместитесь обратно в тему через несколько секунд,');
DEFINE ('_KARMA_INCREASED', 'Карма пользователя увеличена. Если вы не переместитесь обратно в тему через несколько секунд,');
DEFINE ('_COM_A_TEMPLATE', 'Шаблон');
DEFINE ('_COM_A_TEMPLATE_DESC', 'Выберите шаблон для использования.');
DEFINE ('_COM_A_TEMPLATE_IMAGE_PATH', 'Наборы изображений');
DEFINE ('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Выберите набор изображений для использования.');
DEFINE ('_PREVIEW_CLOSE', 'Закрыть это окно');

//==========================================
//new in 1.0 Stable
DEFINE ('_COM_A_POSTSTATSBAR', 'Показывать полосу сообщений');
DEFINE ('_COM_A_POSTSTATSBAR_DESC', 'Установите "Да" чтобы количество сообщений которые написал пользователь отображалось графической полоской.');
DEFINE ('_COM_A_POSTSTATSCOLOR', 'Номер цвета для полосы сообщений');
DEFINE ('_COM_A_POSTSTATSCOLOR_DESC', 'Назначьте номер цвета для использования в полосе сообщений.');
DEFINE ('_LATEST_REDIRECT', 'Kunena необходимо выяснить ваши привилегии доступа перед тем как создать список последних сообщений для вас.\nНе волнуйтесь, это нормально если вы были неактивны более 30 минут или после того как вошли обратно на сайт.\nПожалуйста выполните ваш поисковый запрос снова.');    
DEFINE ('_SMILE_COLOUR', 'Цвет');
DEFINE ('_SMILE_SIZE', 'Размер');
DEFINE ('_COLOUR_DEFAULT', 'Стандарт');
DEFINE ('_COLOUR_RED', 'Красный');
DEFINE ('_COLOUR_PURPLE', 'Пурпурный');
DEFINE ('_COLOUR_BLUE', 'Синий');
DEFINE ('_COLOUR_GREEN', 'Зеленый');
DEFINE ('_COLOUR_YELLOW', 'Желтый');
DEFINE ('_COLOUR_ORANGE', 'Оранжевый');
DEFINE ('_COLOUR_DARKBLUE', 'Тёмно-синий');
DEFINE ('_COLOUR_BROWN', 'Коричневый');
DEFINE ('_COLOUR_GOLD', 'Золотой');
DEFINE ('_COLOUR_SILVER', 'Серебряный');
DEFINE ('_SIZE_NORMAL', 'Нормальный');
DEFINE ('_SIZE_SMALL', 'Маленький');
DEFINE ('_SIZE_VSMALL', 'Очень маленький');
DEFINE ('_SIZE_BIG', 'Большой');
DEFINE ('_SIZE_VBIG', 'Очень большой');
DEFINE ('_IMAGE_SELECT_FILE', 'Приложить изображение');
DEFINE ('_FILE_SELECT_FILE', 'Приложить файл');
DEFINE ('_FILE_NOT_UPLOADED', 'Ваш файл не был загружен. Попробуйте отправить заново или измените сообщение.');
DEFINE ('_IMAGE_NOT_UPLOADED', 'Ваше изображение не было загружено. Попробуйте отправить заново или измените сообщение.');
DEFINE ('_BBCODE_IMGPH', 'Вставьте заполнитель [img] в сообщение для приложенного изображения');
DEFINE ('_BBCODE_FILEPH', 'Вставьте заполнитель [file] в сообщение для приложенного файла');
DEFINE ('_POST_ATTACH_IMAGE', '[img]');
DEFINE ('_POST_ATTACH_FILE', '[file]');
DEFINE ('_USER_UNSUBSCRIBE_ALL', 'Отметьте этот флажок для <b><u>отписки</u></b> ото всех тем(включая невидимые, для решения проблем)');
DEFINE ('_LINK_JS_REMOVED', '<em>Активные ссылки содержащие JavaScript удаляются автоматически</em>');

//==========================================
//new in 1.0 RC4
DEFINE ('_COM_A_LOOKS', 'Внешний Вид');
DEFINE ('_COM_A_USERS', 'Пользовательские');
DEFINE ('_COM_A_LENGTHS', 'Настройка различных длин');
DEFINE ('_COM_A_SUBJECTLENGTH', 'Максимальная длина темы сообщения');
DEFINE ('_COM_A_SUBJECTLENGTH_DESC', 'Максимальная длина строки темы сообщения. Максимальное число поддерживаемое базой данных 255. Если ваш сайт настроен для использования многобайтовых наборов символов как Unicode, UTF-8 или non-ISO-8599-x используйте след. формулу:<br/><tt>окр(255/(макс. кол-во байтов на 1 символ))</tt><br/> Пример для UTF-8, у которого макс. кол-во байтов на 1 символ равно 4: 255/4=63.');    
DEFINE ('_LATEST_THREADFORUM', 'Тема/Форум');
DEFINE ('_LATEST_NUMBER', 'Новые сообщения');
DEFINE ('_COM_A_SHOWNEW', 'Показывать Новые сообщения');
DEFINE ('_COM_A_SHOWNEW_DESC', 'Установите "Да" для того чтобы Kunena показывала пользователю индикатор для форумов содержащих новые сообщения и какие сообщения новые с момента последнего визита.');
DEFINE ('_COM_A_NEWCHAR', 'Индикатор "Новое"');
DEFINE ('_COM_A_NEWCHAR_DESC', 'Определите здесь что должно указывать на новые сообщения (например "!" или "Новое!")');
DEFINE ('_LATEST_AUTHOR', 'Автор последнего сообщения');
DEFINE ('_GEN_FORUM_NEWPOST', 'Новых сообщений');
DEFINE ('_GEN_FORUM_NOTNEW', 'Нет новых сообщений');
DEFINE ('_GEN_UNREAD', 'Непрочитанная Тема');
DEFINE ('_GEN_NOUNREAD', 'Прочитанная Тема');
DEFINE ('_GEN_MARK_ALL_FORUMS_READ', 'Отметить все форумы как прочитанные');
DEFINE ('_GEN_MARK_THIS_FORUM_READ', 'Отметить этот форум как прочитанный');
DEFINE ('_GEN_FORUM_MARKED', 'Все сообщения в этом форуме помечены как прочитанные');
DEFINE ('_GEN_ALL_MARKED', 'Все сообщения помечены как прочитанные');
DEFINE ('_IMAGE_UPLOAD', 'Загрузка Изображения');
DEFINE ('_IMAGE_DIMENSIONS', 'Ваш файл с изображением может быть максимально (ширина х высота - размер)');
DEFINE ('_IMAGE_ERROR_TYPE', 'Используйте только jpeg, gif или png формат');
DEFINE ('_IMAGE_ERROR_EMPTY', 'Выберите файл перед загрузкой');
DEFINE ('_IMAGE_ERROR_SIZE', 'Размер файла изображения превышает максимально допустимый.');
DEFINE ('_IMAGE_ERROR_WIDTH', 'Ширина изображения превышает допустимую.');
DEFINE ('_IMAGE_ERROR_HEIGHT', 'Высота изображения превышает допустимую.');
DEFINE ('_IMAGE_UPLOADED', 'Изображение было загружено.');
DEFINE ('_COM_A_IMAGE', 'Изображения');
DEFINE ('_COM_A_IMGHEIGHT', 'Макс. высота изображения');
DEFINE ('_COM_A_IMGWIDTH', 'Макс. ширина изображения');
DEFINE ('_COM_A_IMGSIZE', 'Макс. размер изображения <br/><em>в Кб</em>');
DEFINE ('_COM_A_IMAGEUPLOAD', 'Разрешить публичную загрузку изображений');
DEFINE ('_COM_A_IMAGEUPLOAD_DESC', 'Установите "Да" чтобы любой посетитель (даже не зарегистрированный) мог загружать изображения.');
DEFINE ('_COM_A_IMAGEREGUPLOAD', 'Разрешить зарегистрированным загружать изображения');
DEFINE ('_COM_A_IMAGEREGUPLOAD_DESC', 'Установите "Да", чтобы зарегистрированные и вошедшие на форум пользователи могли загружать изображения. Примечание: (Супер)Администраторы и модераторы всегда могут загружать изображения.');

//New since preRC4-II:
DEFINE ('_COM_A_UPLOADS', 'Загрузки');
DEFINE ('_FILE_TYPES', 'Файл может быть след. типов - макс. размер');
DEFINE ('_FILE_ERROR_TYPE', 'Вы можете только загружать файлы след. типов:\n');
DEFINE ('_FILE_ERROR_EMPTY', 'Выберите файл перед загрузкой');
DEFINE ('_FILE_ERROR_SIZE', 'Размер файла превышает максимально допустимый.');
DEFINE ('_COM_A_FILE', 'Файлы');
DEFINE ('_COM_A_FILEALLOWEDTYPES', 'Допустимые типы файлов');
DEFINE ('_COM_A_FILEALLOWEDTYPES_DESC', 'Укажите здесь какие типы файлов допустимы для закачки. Используйте разделенный запятыми список <strong>из строчных символов</strong> без пробелов.<br />Пример: zip,txt,exe,htm,html');
DEFINE ('_COM_A_FILESIZE', 'Макс. размер файла <em>в Кб</em>');
DEFINE ('_COM_A_FILEUPLOAD', 'Разрешить публичную загрузку файлов');
DEFINE ('_COM_A_FILEUPLOAD_DESC', 'Установите "Да" чтобы любой посетитель(даже не зарегистрированный) мог загружать файлы.');
DEFINE ('_COM_A_FILEREGUPLOAD', 'Разрешить зарегистрированным загружать файлы');
DEFINE ('_COM_A_FILEREGUPLOAD_DESC', 'Установите "Да" чтобы зарегистрированные и вошедшие на форум пользователи могли загружать файлы. Примечание: (Супер)Администраторы и модераторы всегда могут загружать файлы.');
DEFINE ('_SUBMIT_CANCEL', 'Отправка сообщения была отменена');
DEFINE ('_HELP_SUBMIT', 'Нажмите здесь для отправки сообщения');
DEFINE ('_HELP_PREVIEW', 'Нажмите здесь для предварительного просмотра сообщения перед отправкой');
DEFINE ('_HELP_CANCEL', 'Нажмите здесь для отмены ввода сообщения');
DEFINE ('_POST_DELETE_ATT', 'Если этот флажок отмечен, все изображения и файлы прикрепленные к сообщению которое вы хотите удалить будут также удалены (рекомендуется).');

//new since preRC4-III
DEFINE ('_COM_A_USER_MARKUP', 'Показывать пометку о редактировании');
DEFINE ('_COM_A_USER_MARKUP_DESC', 'Установите "Да" для того чтобы отредактированное сообщение помечалось заметкой кто и когда произвел редактирование.');
DEFINE ('_EDIT_BY', 'Изменено:');
DEFINE ('_EDIT_AT', 'в:');
DEFINE ('_UPLOAD_ERROR_GENERAL', '');
DEFINE ('_COM_A_IMGB_IMG_BROWSE', 'Обозреватель загруженных изображений');
DEFINE ('_COM_A_IMGB_FILE_BROWSE', 'Обозреватель загруженных файлов');
DEFINE ('_COM_A_IMGB_TOTAL_IMG', 'Количество загруженных изображений');
DEFINE ('_COM_A_IMGB_TOTAL_FILES', 'Количество загруженных файлов');
DEFINE ('_COM_A_IMGB_ENLARGE', 'Нажмите на изображение для полноразмерного просмотра');
DEFINE ('_COM_A_IMGB_DOWNLOAD', 'Нажмите на файл изображения для его скачивания');
DEFINE ('_COM_A_IMGB_DUMMY_DESC', 'Опция "Заменить изображением-заглушкой" заменит выбранное изображение заглушкой. Это позволит удалить нужный файл без нарушения сообщения.<br /><small><em>Учтите что иногда потребуется обновить страницу чтобы увидеть произведенную замену.</em></small>');
DEFINE ('_COM_A_IMGB_DUMMY', 'Текущее изображение-заглушка');
DEFINE ('_COM_A_IMGB_REPLACE', 'Заменить ');
DEFINE ('_COM_A_IMGB_REMOVE', 'Полностью удалить');
DEFINE ('_COM_A_IMGB_NAME', 'Имя');
DEFINE ('_COM_A_IMGB_SIZE', 'Размер');
DEFINE ('_COM_A_IMGB_DIMS', 'Разрешение');
DEFINE ('_COM_A_IMGB_CONFIRM', 'Вы абсолютно уверены что хотите удалить этот файл? \n При удалении файла на форуме останется неработающая ссылка...');
DEFINE ('_COM_A_IMGB_VIEW', 'Открыть сообщение (для изменения)');
DEFINE ('_COM_A_IMGB_NO_POST', 'Нет связанного сообщения!');
DEFINE ('_USER_CHANGE_VIEW', 'Изменение этих настроек вступит в силу в ');
DEFINE ('_MOSBOT_DISCUSS_A', 'Обсудить эту статью на форумах (');
DEFINE ('_MOSBOT_DISCUSS_B', 'сообщений');
DEFINE ('_POST_DISCUSS', 'Эта тема посвящена обсуждению статьи');
DEFINE ('_COM_A_RSS', 'Включить RSS-канал');
DEFINE ('_COM_A_RSS_DESC', 'RSS-канал позволяет пользователям загружать последние сообщения к себе на рабочий стол/программу для чтения RSS-каналов (см. <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> для примера.)');
DEFINE ('_LISTCAT_RSS', 'получите последние сообщения прямо на ваш рабочий стол');
DEFINE ('_SEARCH_REDIRECT', 'Kunena необходимо выяснить ваши привилегии доступа перед тем как создать список последних сообщений для вас.\nНе волнуйтесь, это нормально если вы были неактивны более 30 минут или после того как вошли обратно на сайт.\nПожалуйста выполните ваш поисковый запрос снова.');

//====================
//admin.forum.html.php
DEFINE ('_COM_A_CONFIG', 'Конфигурация Kunena');
DEFINE ('_COM_A_DISPLAY', '');
DEFINE ('_COM_A_CURRENT_SETTINGS', 'Текущие настройки');
DEFINE ('_COM_A_EXPLANATION', 'Объяснение');
DEFINE ('_COM_A_BOARD_TITLE', 'Заголовок форума');
DEFINE ('_COM_A_BOARD_TITLE_DESC', 'Имя вашего форума');
DEFINE ('_COM_A_VIEW_TYPE', 'Тип просмотра по умолчанию');
DEFINE ('_COM_A_VIEW_TYPE_DESC', 'Выберите между "Плоским" и "Древовидным" типом просмотра форума по умолчанию');
DEFINE ('_COM_A_THREADS', 'Тем на странице');
DEFINE ('_COM_A_THREADS_DESC', 'Количество тем отображаемых на одной странице');
DEFINE ('_COM_A_REGISTERED_ONLY', 'Только зарегистрированные пользователи');
DEFINE ('_COM_A_REG_ONLY_DESC', 'Установите "Да" для доступа к форуму только зарегистрированных пользователей (просмотр / написание сообщений). Установите "Нет" чтобы разрешить любым посетителям использовать форум.');
DEFINE ('_COM_A_PUBWRITE', 'Публичный просмотр/написание');
DEFINE ('_COM_A_PUBWRITE_DESC', 'Установите "Да" для возможности любому посетителю оставлять сообщения, "Нет" - любой посетитель может читать, но писать могут только зарегистрированные пользователи.');
DEFINE ('_COM_A_USER_EDIT', 'Разрешить редактирование');
DEFINE ('_COM_A_USER_EDIT_DESC', 'Установите "Да" для включения возможности зарегистрированным пользователям редактировать свои собственные сообщения.');
DEFINE ('_COM_A_MESSAGE', 'Для сохранения изменений, нажмите кнопку "Сохранить" вверху страницы.');
DEFINE ('_COM_A_HISTORY', 'Отображение истории');
DEFINE ('_COM_A_HISTORY_DESC', 'Установите "Да" для отображения истории темы во время ответа/цитирования.');
DEFINE ('_COM_A_SUBSCRIPTIONS', 'Включить подписки');
DEFINE ('_COM_A_SUBSCRIPTIONS_DESC', 'Установите "Да" для включения возможности зарегистрированным пользователям подписываться на темы и получать уведомления об ответах на e-mail.');
DEFINE ('_COM_A_HISTLIM', 'Предел истории');
DEFINE ('_COM_A_HISTLIM_DESC', 'Количество сообщений отображаемых в истории');
DEFINE ('_COM_A_FLOOD', 'Защита от флуда');
DEFINE ('_COM_A_FLOOD_DESC', 'Количество секунд которое должно пройти между отправлением двух последовательных сообщений. Установите в 0 (ноль) чтобы выключить защиту от флуда. Примечание: Защита от флуда <em>может</em> вызвать падение производительности!');
DEFINE ('_COM_A_MODERATION', 'Уведомлять модераторов по почте');
DEFINE ('_COM_A_MODERATION_DESC', 'Установите "Да" для отправки уведомлений о новых сообщениях модераторам форума. Примечание: любой (супер)администратор автоматически имеет все привилегии модератора, поэтому он будет получать уведомления тоже.');
DEFINE ('_COM_A_SHOWMAIL', 'Показывать e-mail');
DEFINE ('_COM_A_SHOWMAIL_DESC', 'Установите "Нет" если вы не хотите показывать e-mail адреса пользователей, даже зарегистрированным участникам.');
DEFINE ('_COM_A_AVATAR', 'Включить аватары');
DEFINE ('_COM_A_AVATAR_DESC', 'Установите "Да" если вы хотите чтобы зарегистрированные пользователи могли загружать аватары (управление происходит через свои профили).');
DEFINE ('_COM_A_AVHEIGHT', 'Макс высота аватара');
DEFINE ('_COM_A_AVWIDTH', 'Макс ширина аватара');
DEFINE ('_COM_A_AVSIZE', 'Макс размер аватара (Кб)');
DEFINE ('_COM_A_USERSTATS', 'Показывать показатели пользователей');
DEFINE ('_COM_A_USERSTATS_DESC', 'Установите "Да" для отображения показателей пользователей таких как количество сообщений пользователя, тип пользователя и т.д.,');
DEFINE ('_COM_A_AVATARUPLOAD', 'Разрешить загрузку аватаров');
DEFINE ('_COM_A_AVATARUPLOAD_DESC', 'Установите "Да" если вы хотите позволить зарегистрированным пользователям загружать свои аватары.');
DEFINE ('_COM_A_AVATARGALLERY', 'Использовать галерею аватар');
DEFINE ('_COM_A_AVATARGALLERY_DESC', '');
DEFINE ('_COM_A_RANKING_DESC', '');
DEFINE ('_COM_A_RANKINGIMAGES', 'Использовать Изображения Рангов');
DEFINE ('_COM_A_RANKINGIMAGES_DESC', 'Установите "Да" для отображения изображения ранга пользователя (из components/com_kunena/ranks). Выключение данной опции будет отображать только название ранга.');

//email and stuff
$_COM_A_NOTIFICATION = "Уведомление о новом сообщении от ";
$_COM_A_NOTIFICATION1 = "Появилось новое сообщение в теме на которую вы подписаны ";
$_COM_A_NOTIFICATION2 = "Вы можете управлять своими подписками перейдя по ссылке 'Мой профиль' на главной странице форума. Из своего профиля вы также можете отписаться от этой темы.";
$_COM_A_NOTIFICATION3 = "Не отвечайте на это уведомление, так как оно сгенерировано автоматически.";
$_COM_A_NOT_MOD1 = "Появилось новое сообщение в форуме в котором вы являетесь модератором ";
$_COM_A_NOT_MOD2 = "Пожалуйста просмотрите его после того как зайдете на сайт.";
DEFINE ('_COM_A_NO', 'Нет');
DEFINE ('_COM_A_YES', 'Да');
DEFINE ('_COM_A_FLAT', 'Плоский');
DEFINE ('_COM_A_THREADED', 'Древовидный');
DEFINE ('_COM_A_MESSAGES', 'Сообщений на странице');
DEFINE ('_COM_A_MESSAGES_DESC', 'Количество сообщений для вывода на одну страницу');

//admin; changes from 0.9 to 0.9.1
DEFINE ('_COM_A_USERNAME', 'Использовать имя пользователя');
DEFINE ('_COM_A_USERNAME_DESC', 'Установите "Да" чтобы использовать имя пользователя для входа на форум (вместо настоящего имени).');
DEFINE ('_COM_A_CHANGENAME', 'Позволить смену имени');
DEFINE ('_COM_A_CHANGENAME_DESC', 'Установите "Да" чтобы позволить зарегистрированным пользователям изменять своё имя.');

//admin; changes 0.9.1 to 0.9.2
DEFINE ('_COM_A_BOARD_OFFLINE', 'Отключить форум');
DEFINE ('_COM_A_BOARD_OFFLINE_DESC', 'Установите "Да" чтобы отключить доступ к форуму. Просмотр форума может осуществляться только (Супер) Администраторами.');
DEFINE ('_COM_A_BOARD_OFFLINE_MES', 'Сообщение о причине отключения форума');
DEFINE ('_COM_A_PRUNE', 'Очистить форумы');
DEFINE ('_COM_A_PRUNE_NAME', 'Форум для очистки:');
DEFINE ('_COM_A_PRUNE_DESC', 'Функция очистки форумов позволяет очистить темы в которых не было новых сообщений за указанное количество дней. Эта функция не удаляет прикрепленные темы (их нужно удалять вручную). Темы в закрытых форумах не очищаются.');
DEFINE ('_COM_A_PRUNE_NOPOSTS', 'Очистить темы в которых не было новых сообщений в последние ');
DEFINE ('_COM_A_PRUNE_DAYS', 'дней');
DEFINE ('_COM_A_PRUNE_USERS', 'Очистка пользователей');

//general
DEFINE ('_GEN_ACTION', 'Действие');
DEFINE ('_GEN_AUTHOR', 'Автор');
DEFINE ('_GEN_BY', 'кем');
DEFINE ('_GEN_CANCEL', 'Отмена');
DEFINE ('_GEN_CONTINUE', 'Подтвердить');
DEFINE ('_GEN_DATE', 'Дата');
DEFINE ('_GEN_DELETE', 'Удалить');
DEFINE ('_GEN_EDIT', 'Редактировать');
DEFINE ('_GEN_EMAIL', 'E-mail');
DEFINE ('_GEN_EMOTICONS', 'Смайлики');
DEFINE ('_GEN_FLAT', 'Плоский');
DEFINE ('_GEN_FLAT_VIEW', 'Плоский вид');
DEFINE ('_GEN_FORUMLIST', 'Список форумов');
DEFINE ('_GEN_FORUM', 'Форум');
DEFINE ('_GEN_HELP', 'Помощь');
DEFINE ('_GEN_HITS', 'Просмотров');
DEFINE ('_GEN_LAST_POST', 'Посл. сообщ.');
DEFINE ('_GEN_LATEST_POSTS', 'Новое');
DEFINE ('_GEN_LOCK', 'Закрыть');
DEFINE ('_GEN_UNLOCK', 'Открыть');
DEFINE ('_GEN_LOCKED_FORUM', 'Форум закрыт');
DEFINE ('_GEN_LOCKED_TOPIC', 'Тема закрыта');
DEFINE ('_GEN_MESSAGE', 'Сообщение');
DEFINE ('_GEN_MODERATED', 'Форум премодерируется. Сообщения проверяются перед публикацией.');
DEFINE ('_GEN_MODERATORS', 'Модераторы');
DEFINE ('_GEN_MOVE', 'Перенести');
DEFINE ('_GEN_NAME', 'Имя');
DEFINE ('_GEN_POST_NEW_TOPIC', 'Новая тема');
DEFINE ('_GEN_POST_REPLY', 'Ответить');
DEFINE ('_GEN_MYPROFILE', 'Профиль');
DEFINE ('_GEN_QUOTE', 'Цитата');
DEFINE ('_GEN_REPLY', 'Ответить');
DEFINE ('_GEN_REPLIES', 'Ответов');
DEFINE ('_GEN_THREADED', 'Древовидный');
DEFINE ('_GEN_THREADED_VIEW', 'Древовидный вид');
DEFINE ('_GEN_SIGNATURE', 'Подпись');
DEFINE ('_GEN_ISSTICKY', 'Тема закреплена.');
DEFINE ('_GEN_STICKY', 'Закрепить');
DEFINE ('_GEN_UNSTICKY', 'Открепить');
DEFINE ('_GEN_SUBJECT', 'Тема сообщения');
DEFINE ('_GEN_SUBMIT', 'Отправить');
DEFINE ('_GEN_TOPIC', 'Тема');
DEFINE ('_GEN_TOPICS', 'Темы');
DEFINE ('_GEN_TOPIC_ICON', 'Иконка темы');
DEFINE ('_GEN_SEARCH_BOX', 'Поиск по форуму');
$_GEN_THREADED_VIEW = "Древовидный";
$_GEN_FLAT_VIEW = "Плоский";

//avatar_upload.php
DEFINE ('_UPLOAD_UPLOAD', 'Загрузить');
DEFINE ('_UPLOAD_DIMENSIONS', 'Макс. параметры загружаемого изображения (Ш х В - Размер)');
DEFINE ('_UPLOAD_SUBMIT', 'Подтвердите загрузку нового аватара');
DEFINE ('_UPLOAD_SELECT_FILE', 'Выберите файл');
DEFINE ('_UPLOAD_ERROR_TYPE', 'Допустимые форматы jpeg, gif или png');
DEFINE ('_UPLOAD_ERROR_EMPTY', 'Выберите файл перед загрузкой');
DEFINE ('_UPLOAD_ERROR_NAME', 'Файл с изображением в названии должен содержать только буквы и цифры, пробелы и другие символы не допускаются.');
DEFINE ('_UPLOAD_ERROR_SIZE', 'Размер файла изображения превышает максимально допустимый.');
DEFINE ('_UPLOAD_ERROR_WIDTH', 'Ширина изображения превышает допустимую.');
DEFINE ('_UPLOAD_ERROR_HEIGHT', 'Высота изображения превышает допустимую.');
DEFINE ('_UPLOAD_ERROR_CHOOSE', 'Вы не выбрали аватара из галереи...');
DEFINE ('_UPLOAD_UPLOADED', 'Ваш аватар был загружен.');
DEFINE ('_UPLOAD_GALLERY', 'Выберите аватара из галереи:');
DEFINE ('_UPLOAD_CHOOSE', 'Подтвердите выбор.');

// listcat.php
DEFINE ('_LISTCAT_ADMIN', 'Администратор должен вначале создать их в');
DEFINE ('_LISTCAT_DO', 'Они будут знать что делать ');
DEFINE ('_LISTCAT_INFORM', 'Проинформируйте их и попросите поторопиться!');
DEFINE ('_LISTCAT_NO_CATS', 'Еще не создано ни одной категории в этом форуме');
DEFINE ('_LISTCAT_PANEL', 'Панели Управления!');
DEFINE ('_LISTCAT_PENDING', 'сообщений(я) на проверке');

// moderation.php
DEFINE ('_MODERATION_MESSAGES', 'Нет сообщений ожидающих проверки в этом форуме.');

// post.php
DEFINE ('_POST_ABOUT_TO_DELETE', 'Хотите удалить сообщение');
DEFINE ('_POST_ABOUT_DELETE', '<strong>Примечание:</strong><br/>
- если вы удаляете Тему форума (первое сообщение в теме) все дочерние объекты тоже будут удалены!
..Применяйте очистку текста самого сообщения и имени автора если нужно удалить только содержимое...
<br/>
- Все дочерние сообщения удаленного сообщения будут подняты на один уровень выше в иерархии темы.');

//<strong>NOTES:</strong><br/>
//-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
//..Consider blanking the post and posters name if only the contents should be removed..
//<br/>
//- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.');
DEFINE ('_POST_CLICK', 'нажмите здесь');
DEFINE ('_POST_ERROR', 'Невозможно найти пользователя/e-mail. Ошибка БД не отображается.');
DEFINE ('_POST_ERROR_MESSAGE', 'Произошла неизвестная SQL ошибка и ваше сообщение не было сохранено. Если данная проблема будет повторяться свяжитесь с администратором.');
DEFINE ('_POST_ERROR_MESSAGE_OCCURED', 'Произошла ошибка и сообщение не было обновлено. Попробуйте еще раз. Если данная проблема будет повторяться свяжитесь с администратором.');
DEFINE ('_POST_ERROR_TOPIC', 'Произошла ошибка во время удаления. Проверьте возможные причины ниже:');
DEFINE ('_POST_FORGOT_NAME', 'Вы забыли указать ваше имя. Нажмите в браузере кнопку "Назад" и попробуйте снова.');
DEFINE ('_POST_FORGOT_SUBJECT', 'Вы забыли указать тему сообщения. Нажмите в браузере кнопку "Назад" и попробуйте снова.');
DEFINE ('_POST_FORGOT_MESSAGE', 'Вы забыли указать сообщение. Нажмите в браузере кнопку "Назад" и попробуйте снова.');
DEFINE ('_POST_INVALID', 'Был запрошен неверный ID сообщения.');
DEFINE ('_POST_LOCK_SET', 'Тема закрыта.');
DEFINE ('_POST_LOCK_NOT_SET', 'Тема не может быть закрыта.');
DEFINE ('_POST_LOCK_UNSET', 'Тема открыта.');
DEFINE ('_POST_LOCK_NOT_UNSET', 'Тема не может быть открыта.');
DEFINE ('_POST_MESSAGE', 'Написать новое сообщение в ');
DEFINE ('_POST_MOVE_TOPIC', 'Перенести эту Тему в форум');
DEFINE ('_POST_NEW', 'Написать новое сообщение в:');
DEFINE ('_POST_NO_SUBSCRIBED_TOPIC', 'Невозможно подписать вас на данную тему.');
DEFINE ('_POST_NOTIFIED', 'Отметьте данный флажок чтоб получать уведомления о новых ответах в этой теме.');
DEFINE ('_POST_STICKY_SET', 'Данная тема закреплена.');
DEFINE ('_POST_STICKY_NOT_SET', 'Данная тема не может быть закреплена.');
DEFINE ('_POST_STICKY_UNSET', 'Данная тема откреплена.');
DEFINE ('_POST_STICKY_NOT_UNSET', 'Данная тема не может быть откреплена.');
DEFINE ('_POST_SUBSCRIBE', 'Подписка');
DEFINE ('_POST_SUBSCRIBED_TOPIC', 'Вы были подписаны на данную тему.');
DEFINE ('_POST_SUCCESS', 'Выше сообщение было успешно отправлено.');
DEFINE ('_POST_SUCCES_REVIEW', 'Выше сообщение было успешно отправлено. Оно будет проверено модератором перед публикацией на форуме.');
DEFINE ('_POST_SUCCESS_REQUEST', 'Ваш запрос обработан. Если вы не вернетесь обратно в тему через несколько секунд, ');
DEFINE ('_POST_TOPIC_HISTORY', 'История Темы');
DEFINE ('_POST_TOPIC_HISTORY_MAX', 'Макс. отображать прошлые');
DEFINE ('_POST_TOPIC_HISTORY_LAST', 'сообщения - <i>(последние сообщ. вначале)</i>');
DEFINE ('_POST_TOPIC_NOT_MOVED', 'Ваша тема не может быть перенесена. Возвращайтесь в тему:');
DEFINE ('_POST_TOPIC_FLOOD1', 'Администрация данного форума включила защиту от флуда и решила что вы должны подождать');
DEFINE ('_POST_TOPIC_FLOOD2', 'секунд перед тем как создать новое сообщение.');
DEFINE ('_POST_TOPIC_FLOOD3', 'Нажмите в браузере кнопку "Назад" чтобы вернуться на форум.');
DEFINE ('_POST_EMAIL_NEVER', 'ваш e-mail адрес никогда не будет отображаться на форуме.');
DEFINE ('_POST_EMAIL_REGISTERED', 'ваш e-mail адрес будет доступен только зарегистрированным пользователям.');
DEFINE ('_POST_LOCKED', 'Закрыто администратором.');
DEFINE ('_POST_NO_NEW', 'Новые ответы запрещены.');
DEFINE ('_POST_NO_PUBACCESS1', 'Администратор запретил возможность анонимно оставлять сообщения.');
DEFINE ('_POST_NO_PUBACCESS2', 'Только вошедшие/зарегистрированные пользователи<br />могут оставлять сообщения.');

// showcat.php
DEFINE ('_SHOWCAT_NO_TOPICS', '>> В данном форуме пока нет тем <<');
DEFINE ('_SHOWCAT_PENDING', 'ожидающие проверки сообщения');

// userprofile.php
DEFINE ('_USER_DELETE', 'Отметьте данный флажок для удаления вашей подписи');
DEFINE ('_USER_ERROR_A', 'Вы пришли на эту страницу в результате ошибки. Пожалуйста сообщите администратору по какой ссылке вы перешли.');
DEFINE ('_USER_ERROR_B', 'you clicked that got you here. She or he can then file a bug report.');
DEFINE ('_USER_ERROR_C', 'Спасибо!');
DEFINE ('_USER_ERROR_D', 'Номер ошибки для включения в ваш доклад:');
DEFINE ('_USER_GENERAL', 'Главные Опции профиля');
DEFINE ('_USER_MODERATOR', 'Вы назначены модератором на следующие форумы.');
DEFINE ('_USER_MODERATOR_NONE', 'Не найдено форумов закрепленных за вами.');
DEFINE ('_USER_MODERATOR_ADMIN', 'Админы являются модераторами на всех форумах.');
DEFINE ('_USER_NOSUBSCRIPTIONS', 'У вас нет подписок');
DEFINE ('_USER_PREFERED', 'Предпочитаемый тип просмотра');
DEFINE ('_USER_PROFILE', 'Профиль для ');
DEFINE ('_USER_PROFILE_NOT_A', 'Ваш профиль ');
DEFINE ('_USER_PROFILE_NOT_B', 'не');
DEFINE ('_USER_PROFILE_NOT_C', ' может быть обновлен');
DEFINE ('_USER_PROFILE_UPDATED', 'Ваш профиль обновлен');
DEFINE ('_USER_RETURN_A', 'Если Вы не вернулись к просмотру Вашего профиля в течение нескольких секунд ');
DEFINE ('_USER_RETURN_B', 'Нажмите здесь');
DEFINE ('_USER_SUBSCRIPTIONS', 'Ваши подписки');
DEFINE ('_USER_UNSUBSCRIBE', 'Отписаться');
DEFINE ('_USER_UNSUBSCRIBE_A', 'Вы');
DEFINE ('_USER_UNSUBSCRIBE_B', 'не можете');
DEFINE ('_USER_UNSUBSCRIBE_C', ' отписаться от темы');
DEFINE ('_USER_UNSUBSCRIBE_YES', 'Вы отписались от темы');
DEFINE ('_USER_DELETEAV', ' отметьте для удаления аватара');

//New 0.9 to 1.0
DEFINE ('_USER_ORDER', 'Предпочтительный порядок сообщений');
DEFINE ('_USER_ORDER_DESC', 'Сначала последний пост');
DEFINE ('_USER_ORDER_ASC', 'Сначала первый пост');

// view.php
DEFINE ('_VIEW_DISABLED', 'Администратор запретил написание сообщений незарегистрированными пользователями');
DEFINE ('_VIEW_POSTED', 'Размещено');
DEFINE ('_VIEW_SUBSCRIBE', ':: Подписать на эту тему ::');
DEFINE ('_MODERATION_INVALID_ID', 'An invalid post id was requested.');
DEFINE ('_VIEW_NO_POSTS', 'В этом форуме нет сообщений');
DEFINE ('_VIEW_VISITOR', 'Посетитель');
DEFINE ('_VIEW_ADMIN', 'Администратор');
DEFINE ('_VIEW_USER', 'Пользователь');
DEFINE ('_VIEW_MODERATOR', 'Модератор');
DEFINE ('_VIEW_REPLY', 'Ответить на сообщение');
DEFINE ('_VIEW_EDIT', 'Редактировать сообщение');
DEFINE ('_VIEW_QUOTE', 'Цитировать в новом сообщении');
DEFINE ('_VIEW_DELETE', 'Удалить сообщение');
DEFINE ('_VIEW_STICKY', 'Приклеить тему');
DEFINE ('_VIEW_UNSTICKY', 'Отклеить тему');
DEFINE ('_VIEW_LOCK', 'Заблокировать тему');
DEFINE ('_VIEW_UNLOCK', 'Открыть тему');
DEFINE ('_VIEW_MOVE', 'Перенести тему в другой форум');
DEFINE ('_VIEW_SUBSCRIBETXT', 'Подписаться на тему и получать сообщения по электронной почте о новых сообщениях');

//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE ('_HOME', 'Форум');
DEFINE ('_POSTS', 'Сообщений:');
DEFINE ('_TOPIC_NOT_ALLOWED', 'Сообщение');
DEFINE ('_FORUM_NOT_ALLOWED', 'Форум');
DEFINE ('_FORUM_IS_OFFLINE', 'Форум недоступен!');
DEFINE ('_PAGE', 'Страница: ');
DEFINE ('_NO_POSTS', 'Нет сообщений');
DEFINE ('_CHARS', 'символов максимум');
DEFINE ('_HTML_YES', 'HTML выключен');
DEFINE ('_YOUR_AVATAR', '<b>Ваш аватар</b>');
DEFINE ('_NON_SELECTED', 'Еще не выбран <br />');
DEFINE ('_SET_NEW_AVATAR', 'Выберите новый аватар');
DEFINE ('_THREAD_UNSUBSCRIBE', 'Отписаться');
DEFINE ('_SHOW_LAST_POSTS', 'Активные темы за последние');
DEFINE ('_SHOW_HOURS', 'час(ов)');
DEFINE ('_SHOW_POSTS', 'Всего:');
DEFINE ('_DESCRIPTION_POSTS', 'Показаны последние сообщения в активных темах');
DEFINE ('_SHOW_4_HOURS', '4 часа');
DEFINE ('_SHOW_8_HOURS', '8 часов');
DEFINE ('_SHOW_12_HOURS', '12 часов');
DEFINE ('_SHOW_24_HOURS', '24 часа');
DEFINE ('_SHOW_48_HOURS', '48 часов');
DEFINE ('_SHOW_WEEK', 'Неделя');
DEFINE ('_POSTED_AT', 'Отправлено');
DEFINE ('_DATETIME', 'Y/m/d H:i');
DEFINE ('_NO_TIMEFRAME_POSTS', 'В течение этого периода новых постов не появилось.');
DEFINE ('_MESSAGE', 'Сообщение');
DEFINE ('_NO_SMILIE', 'нет');
DEFINE ('_FORUM_UNAUTHORIZIED', 'Этот форум открыт только для зарегистрированных пользователей');
DEFINE ('_FORUM_UNAUTHORIZIED2', 'Пожалуйста войдите, если Вы уже зарегистрированы');
DEFINE ('_MESSAGE_ADMINISTRATION', 'Модерация');
DEFINE ('_MOD_APPROVE', 'Подтвердить');
DEFINE ('_MOD_DELETE', 'Удалить');

//NEW in RC1
DEFINE ('_SHOW_LAST', 'Показывает последние сообщения');
DEFINE ('_POST_WROTE', 'написано');
DEFINE ('_COM_A_EMAIL', 'E-mail адрес форума');
DEFINE ('_COM_A_EMAIL_DESC', 'Это e-mail адрес форума. Вставьте сюда рабочий адрес.');
DEFINE ('_COM_A_WRAP', 'Переносить длинные слова');
DEFINE ('_COM_A_WRAP_DESC', '');    
DEFINE ('_COM_A_SIGNATURE', 'Макс. длина подписи');
DEFINE ('_COM_A_SIGNATURE_DESC', 'Максимально допустимое количество символов в подписи пользователя');
DEFINE ('_SHOWCAT_NOPENDING', 'Нет ожидающих сообщений');
DEFINE ('_COM_A_BOARD_OFSET', 'Board Time Offset');
DEFINE ('_COM_A_BOARD_OFSET_DESC', 'Some boards are located on servers in a different timezone than the users are. Set the Time Offset Kunena must use in the post time in hours. Positive and negative numbers can be used');

//New in RC2
DEFINE ('_COM_A_BASICS', 'Основные');
DEFINE ('_COM_A_FRONTEND', 'Фронтэнд');
DEFINE ('_COM_A_SECURITY', 'Безопасность');
DEFINE ('_COM_A_AVATARS', 'Аватары');
DEFINE ('_COM_A_INTEGRATION', 'Интеграция');
DEFINE ('_COM_A_PMS', 'Включить личные сообщения');
DEFINE ('_COM_A_PMS_DESC', 'Выберите нужный компонент личных сообщений ');    
DEFINE ('_VIEW_PMS', 'Click here to send a Private Message to this user');

//new in RC3
DEFINE ('_POST_RE', 'Re:');
DEFINE ('_BBCODE_BOLD', 'Жирный шрифт: [b]текст[/b] ');
DEFINE ('_BBCODE_ITALIC', 'Наклонный шрифт: [i]текст[/i]');
DEFINE ('_BBCODE_UNDERL', 'Подчеркнутый текст: [u]текст[/u]');
DEFINE ('_BBCODE_QUOTE', 'Цитировать текст: [quote]текст[/quote]');
DEFINE ('_BBCODE_CODE', 'Отображать код: [code]код[/code]');
DEFINE ('_BBCODE_ULIST', 'Неупорядоченный Список: [ul] [li]текст[/li] [/ul] - Подсказка: a list must contain List Items');
DEFINE ('_BBCODE_OLIST', 'Упорядоченный Список: [ol] [li]текст[/li] [/ol] - Подсказка: a list must contain List Items');
DEFINE ('_BBCODE_IMAGE', 'Изображение: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE ('_BBCODE_LINK', 'Ссылка: [url=http://www.zzz.com/]Это ссылка[/url]');
DEFINE ('_BBCODE_CLOSA', 'Закрыть все тэги');
DEFINE ('_BBCODE_CLOSE', 'Закрыть все открытые bbCode тэги');
DEFINE ('_BBCODE_COLOR', 'Цвет: [color=#FF6600]текст[/color]');
DEFINE ('_BBCODE_SIZE', 'Размер: [size=1]размер текста[/size] - Подсказка: размер варьируется от 1 до 5');
DEFINE ('_BBCODE_LITEM', 'Перечисление: [li] перечисление [/li]');
DEFINE ('_BBCODE_HINT', 'Помощь по bbCode - подсказка: bbcode может быть использован на выделенном тексте!');
DEFINE ('_COM_A_TAWIDTH', 'Ширина области с текстом');
DEFINE ('_COM_A_TAWIDTH_DESC', 'Подгоняет ширину области ввода ответа/сообщения для сочетания с шаблоном');
DEFINE ('_COM_A_TAHEIGHT', 'Высота области с текстом');
DEFINE ('_COM_A_TAHEIGHT_DESC', 'Подгоняет высоту области ввода ответа/сообщения для сочетания с шаблоном');
DEFINE ('_COM_A_ASK_EMAIL', 'Требуется e-mail');
DEFINE ('_COM_A_ASK_EMAIL_DESC', 'Требует e-mail если пользователи или посетители пишут сообщение. Установите "Да" если вы хотите чтобы у пользователей запрашивался e-mail при написании сообщения');

//Rank Administration - Dan Syme/IGD
DEFINE ('_KUNENA_RANKS_MANAGE', 'Управление статусами');
DEFINE ('_KUNENA_SORTRANKS', 'Сортировка по статусу');
DEFINE ('_KUNENA_RANKSIMAGE', 'Изображение статуса');
DEFINE ('_KUNENA_RANKS', 'Название статуса');
DEFINE ('_KUNENA_RANKS_SPECIAL', 'Специальное');
DEFINE ('_KUNENA_RANKSMIN', 'Минимальное количество сообщений');
DEFINE ('_KUNENA_RANKS_ACTION', 'Действия'); 
DEFINE ('_KUNENA_NEW_RANK', 'Новый статус'); 

?>
