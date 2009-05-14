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
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.4
DEFINE('_KUNENA_SAMPLEREMOVED', 'اطلاعات نمونه حذف شد');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','عرض متوسط تصویر :');
DEFINE('_KUNENA_COPY_FILE', 'کپی "%src%" به "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'تایید');
DEFINE('_KUNENA_CSS_SAVE', 'ذخیره فایل css باید در این محل باشد...فایل="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'ساختار پیوست ها ارتقا پیدا کرد!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'ساختار پیوست ها ارتقا پیدا کرد!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'هیج چیز پاک نشد.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'ارسال پاک نشد');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'امکان حذف وجود ندارد.');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'همه چیز پاک شد!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "پایگاه داده ایراد دارد");
DEFINE('_KUNENA_UNIST_SUCCESS', "کامپوننت امجمن با موفقیت حذف گردید!");
DEFINE('_KUNENA_PDF_VERSION', 'نسخه انجمن: %version%');
DEFINE('_KUNENA_PDF_DATE', 'ایجاد شده در: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'هیچ انجمنی برای جستجو انتخاب نشده است.');
DEFINE('_KUNENA_COPY_FILE_KUNENA_COPY_OK', 'فایل با موفقیت کپی شد');
DEFINE('_KUNENA_UNIST_SUCCESS', 'کامپوننت با موفقیت حذف شد');


DEFINE('_KUNENA_ERRORADDUSERS', 'ایراد در اضافه نمودن کاربر:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'کاربران منتقل شده ای که حذف شدند:');
DEFINE('_KUNENA_USERSSYNCADD', ', ایجاد:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'مشخصات کاربران.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'هیچ مشخصاتی برای انتقال پیدا نشد.');
DEFINE('_KUNENA_SYNC_USERS', 'انتقال کاربران');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'انتقال کاربران از سیستم مدیریت محتوا به انجمن');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'ایمیل مدیران');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC','با انتخاب &quot;بله&quot; با ارسال هر موضوع جدید یک ایمیل به مدیران ارسال خواهد شد.');
DEFINE('_KUNENA_RANKS_EDIT', 'ویرایش رتبه');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'مخفی بودن ایمیل');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'یکشنبه');
DEFINE('_KUNENA_DT_LDAY_MON', 'دوشنبه');
DEFINE('_KUNENA_DT_LDAY_TUE', 'سه شنبه');
DEFINE('_KUNENA_DT_LDAY_WED', 'چهارشنبه');
DEFINE('_KUNENA_DT_LDAY_THU', 'پنچ شنبه');
DEFINE('_KUNENA_DT_LDAY_FRI', 'جمعه');
DEFINE('_KUNENA_DT_LDAY_SAT', 'شنبه');
DEFINE('_KUNENA_DT_DAY_SUN', 'یکشنبه');
DEFINE('_KUNENA_DT_DAY_MON', 'دوشنبه');
DEFINE('_KUNENA_DT_DAY_TUE', 'سه شنبه');
DEFINE('_KUNENA_DT_DAY_WED', 'چهرشنبه');
DEFINE('_KUNENA_DT_DAY_THU', 'پنج شنبه');
DEFINE('_KUNENA_DT_DAY_FRI', 'جمعه');
DEFINE('_KUNENA_DT_DAY_SAT', 'شنبه');
DEFINE('_KUNENA_DT_LMON_JAN', 'January');
DEFINE('_KUNENA_DT_LMON_FEB', 'February');
DEFINE('_KUNENA_DT_LMON_MAR', 'March');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'May');
DEFINE('_KUNENA_DT_LMON_JUN', 'June');
DEFINE('_KUNENA_DT_LMON_JUL', 'July');
DEFINE('_KUNENA_DT_LMON_AUG', 'August');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'October');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'December');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'May');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Oct');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec');
DEFINE('_KUNENA_CHILD_BOARD', 'زیر انجمن');
define('_WHO_ONLINE_MEMBER', 'کاربر آنلاین');
define('_WHO_ONLINE_GUEST', 'مهمان آنلاین');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'none');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'پردازشگر تصویر:');
define('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'نصب با موفقت انجام شد برای ادامه کلیک نمایید');
define('_KUNENA_INSTALL_APPLY', 'تایید');
DEFINE('_KUNENA_NO_ACCESS', 'شما به این انجمن دسترسی ندارید!');
DEFINE('_KUNENA_TIME_SINCE', '%time% قبل');
DEFINE('_KUNENA_DATE_YEARS', 'سال');
DEFINE('_KUNENA_DATE_MONTHS', 'ماه');
DEFINE('_KUNENA_DATE_WEEKS','هفته');
DEFINE('_KUNENA_DATE_DAYS', 'روز');
DEFINE('_KUNENA_DATE_HOURS', 'ساعت');
DEFINE('_KUNENA_DATE_MINUTES', 'دقیقه');
// 1.0.3
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'آیا مطمئن هستید که می خواهید اطلاعات نمونه را حذف نمایید؟');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'هدر انجمن:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "نمایش انجمن");
DEFINE('_KUNENA_CLASS_SFX', "پسوند کلاس CSS انجمن");
DEFINE('_KUNENA_CLASS_SFXDESC', "پسوند CSS برای نظاهر انجمن.");
DEFINE('_COM_A_USER_EDIT_TIME', 'زمان ویرایش کاربر');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'برای زمان نا محدود 0 بگذارید، در غیر اینصورت
مقدار زمان مجاز کاربر برای ویرایش مطلب خود بین ارسال و اخرین ویرایش را به ثانیه تعیین کنید.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'زمان تایید ویرایش کاربر');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'پیشفرض 600 [ثانیه]، دادناجازه به کاربر برای ویرایش بعد از این زمان');
DEFINE('_KUNENA_HELPPAGE','صفحه راهنما فعال');
DEFINE('_KUNENA_HELPPAGE_DESC','با انتخاب &quot;بله&quot; لینک صفحه راهنما در بالای انجمن نمایش داده می شود.');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA','نمایش راهنما در انجمن');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','با انتخاب &quot;بله&quot; یک متن به عنوان راهنما نمایش داده میشود .');
DEFINE('_KUNENA_HELPPAGE_CID','شناسه مطلب حاوی راهنما');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','شما باید "نمایش راهنما در انجمن" را برابر <b>"بله"</b> قرار بدهید.');
DEFINE('_KUNENA_HELPPAGE_LINK',' لینک صفحه خارجی راهنما');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','برای نمایش راهنما از وب سایت دیگر لطفا گزینه "نمایش راهنما در انجمن"  را برابر<b>"خیر"</b> قرار دهید.');
DEFINE('_KUNENA_RULESPAGE','صفحه مقررات فعال');
DEFINE('_KUNENA_RULESPAGE_DESC','با انتخاب &quot;بله&quot; لینک مقررات در بالای انجمن نمایش داده می شود.');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA','نمایش مقررات در انجمن');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','با انتخاب &quot;بله&quot; یک محتوا به عنوان قوانین انجمن نمایش داده می شود. <b>توجه:</b> شما باید "شناسه محتوای مقرراتت" را وارد نمایید .');
DEFINE('_KUNENA_RULESPAGE_CID','شناسه مطلب حاوی مقررات');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','شما باید "نمایش مقررات انجمن" را برابر <b>"بله"</b> قرار دهید.');
DEFINE('_KUNENA_RULESPAGE_LINK',' لینک صفحه خارجی مقررات');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','برای نمایش مقررات از لینک خارجی در "نمایش مققرات در انجمن" گزینه <b>"خیر"</b> "را انتخاب نمایید.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','کتابخانه GD پیدا نشد');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','کتابخانه GD2 پیدا نشد');
DEFINE('_KUNENA_GD_INSTALLED','نسخه GD موجود ');
DEFINE('_KUNENA_GD_NO_VERSION','نسخه GD قابل شناسایی نیست');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD نصب نیست ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','ارتفاع تصویر کوچک :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','عرض تصویر کوچک :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','ارتفاع تصویر متوسط :');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','عرض تصویر متوسط :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','ارتفاع تصویر بزرگ :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','عرض تصویر بزرگ :');
DEFINE('_KUNENA_AVATAR_QUALITY','کیفیت آواتار');
DEFINE('_KUNENA_WELCOME','به انجمن گفتگو خوش آمدید');
DEFINE('_KUNENA_WELCOME_DESC','شما در این قسمت می توانید کلیه تنظیمات مربوط به انجمن های گفتگوی سایت خود را انجام دهید.');
DEFINE('_KUNENA_STATISTIC','آمار');
DEFINE('_KUNENA_VALUE','مقدار');
DEFINE('_GEN_CATEGORY','مجموعه');
DEFINE('_GEN_STARTEDBY','شروع توسط: ');
DEFINE('_GEN_STATS','آمار');
DEFINE('_STATS_TITLE',' انجمن - آمار');
DEFINE('_STATS_GEN_STATS','آمار اصلی');
DEFINE('_STATS_TOTAL_MEMBERS','کاربران:');
DEFINE('_STATS_TOTAL_REPLIES','پاسخ ها:');
DEFINE('_STATS_TOTAL_TOPICS','موضوعات:');
DEFINE('_STATS_TODAY_TOPICS','موضوعات امروز:');
DEFINE('_STATS_TODAY_REPLIES','پاسخ های امروز:');
DEFINE('_STATS_TOTAL_CATEGORIES','مجموعه ها:');
DEFINE('_STATS_TOTAL_SECTIONS','بخش ها:');
DEFINE('_STATS_LATEST_MEMBER','آخرین عضو:');
DEFINE('_STATS_YESTERDAY_TOPICS','موضوعات دیروز:');
DEFINE('_STATS_YESTERDAY_REPLIES','پاسخ های دیروز:');
DEFINE('_STATS_POPULAR_PROFILE','10 کاربر محبوب (بر اساس بازدید پروفایل)');
DEFINE('_STATS_TOP_POSTERS','ارسال کنندگان برتر');
DEFINE('_STATS_POPULAR_TOPICS','برترین موضوعات');
DEFINE('_COM_A_STATSPAGE','فعال سازی صفحه آمار');
DEFINE('_COM_A_STATSPAGE_DESC','با انتخاب &quot;بله&quot; لینک امار انجمن نمایش داده می شود ');
DEFINE('_COM_C_JBSTATS','آمار انجمن');
DEFINE('_COM_C_JBSTATS_DESC','آمار انجمن');
define('_GEN_GENERAL','اصلی');
define('_PERM_NO_READ','شما دسترسی لازم برای استفاده از انجمن را ندارید.');
DEFINE ('_KUNENA_SMILEY_SAVED','شکلک ذخیره شد');
DEFINE ('_KUNENA_SMILEY_DELETED','شکلک حذف شد');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','کد از قبل موجود است');
DEFINE ('_KUNENA_MISSING_PARAMETER','پارمترهای وارد نشده');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','امتیاز از قبل موجود است');
DEFINE ('_KUNENA_RANK_DELETED','امتیاز حذف شد');
DEFINE ('_KUNENA_RANK_SAVED','امتیاز ذخیره شد');
DEFINE ('_KUNENA_DELETE_SELECTED','حذف شد');
DEFINE ('_KUNENA_MOVE_SELECTED','منتقل شد');
DEFINE ('_KUNENA_REPORT_LOGGED','وارد شده');
DEFINE ('_KUNENA_GO','برو');
DEFINE('_KUNENA_MAILFULL','متن کامل پست در ایمیل باخبرسازی');
DEFINE('_KUNENA_MAILFULL_DESC','تجر خیر را انتخاب کنید - فقط عنوان پیام ها فرستاده می شود');
DEFINE('_KUNENA_HIDETEXT','برای دیدن این مطلب ابتدا باید به سایت وارد شوید!');
DEFINE('_BBCODE_HIDE','متن مخفی: [hide]متن مخفی[/hide] - مخفی شدن قسمتی از متن برای مهمان ها');
DEFINE('_KUNENA_FILEATTACH','فایل پیوست: ');
DEFINE('_KUNENA_FILENAME','نام فایل: ');
DEFINE('_KUNENA_FILESIZE','اندازه فایل: ');
DEFINE('_KUNENA_MSG_CODE','کد: ');
DEFINE('_KUNENA_CAPTCHA_ON','سیستم محافظت اسپم');
DEFINE('_KUNENA_CAPTCHA_DESC','سیستم ضد اسپم و ضد bot CAPTCHA روشن/خاموش');
DEFINE('_KUNENA_CAPDESC','کد را اینجا وارد کنید');
DEFINE('_KUNENA_CAPERR','کد صحیح نیست!');
DEFINE('_KUNENA_COM_A_REPORT', 'گزارش به مدیر');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'اگر می خواهید سیستم گزارش پست ها به مدیر از جانب اعضا فعال باشد بلی را انتخاب نمایید.');
DEFINE('_KUNENA_REPORT_MSG', 'پیغام گزارش شد');
DEFINE('_KUNENA_REPORT_REASON', 'دلیل');
DEFINE('_KUNENA_REPORT_MESSAGE', 'پیغام شما');
DEFINE('_KUNENA_REPORT_SEND', 'گزارش ارسال');
DEFINE('_KUNENA_REPORT', 'گزارش به مدیر');
DEFINE('_KUNENA_REPORT_RSENDER', 'فرستنده گزارش: ');
DEFINE('_KUNENA_REPORT_RREASON', 'دلیل گزارش: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'پیغام گزارش: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'ارسال کننده پیغام: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'عنوان پیغام: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'پیغام: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'لینک پیغام: ');
DEFINE('_KUNENA_REPORT_INTRO', 'برای شما پیغامی فرستاده است به دلیل');
DEFINE('_KUNENA_REPORT_SUCCESS', 'گزارش با موفقیت ارسال شد!');
DEFINE('_KUNENA_EMOTICONS', 'شکلک ها');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'شکلک');
DEFINE('_KUNENA_EMOTICONS_CODE', 'کد');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'ویرایش شکلک');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'ویرایش شکلک ها');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'نوار شکلک ها');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'شکلک جدید');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'سایر شکلک ها');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'بستن پنجره');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'شکلک های اضافی');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'انتخاب یک شکلک');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'مامبوت پشتیبانی جوملا');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'فعال سازی مامبوت پشتیبانی جوملا');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'تنظیمات پلاگین پروفایل من');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'اجازه تغییر نام کاربری');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'اجازه تغییر نام کاربری در صفحه پلاگین پروفایل من');
DEFINE ('_KUNENA_RECOUNTFORUMS','شمارش مجدد آمار مجموعه ها');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','همه آمار مجموعه ها شمارش مجدد شدند.');
DEFINE ('_KUNENA_EDITING_REASON','دلیل ویرایش');
DEFINE ('_KUNENA_EDITING_LASTEDIT','آخرین ویرایش');
DEFINE ('_KUNENA_BY','توسط');
DEFINE ('_KUNENA_REASON','دلیل');
DEFINE('_GEN_GOTOBOTTOM', 'رفتن به پایین');
DEFINE('_GEN_GOTOTOP', 'رفتن به بالا');
DEFINE('_STAT_USER_INFO', 'اطلاعات کاربر');
DEFINE('_USER_SHOWEMAIL', 'نمایش ایمیل');
DEFINE('_USER_SHOWONLINE', 'نمایش آنلاین بودن');
DEFINE('_KUNENA_HIDDEN_USERS', 'کاربران مخفی');
DEFINE('_KUNENA_SAVE', 'ذخیره');
DEFINE('_KUNENA_RESET', 'Reset');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'گالری پیشفرض');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'اطلاعات شخصی');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'خلاصه');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'آواتار من');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'تنظیمات انجمن');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'ظاهر و چینش');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'اطلاعات پروفایل من');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'پست های من');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'باخبرسازی ها');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'علاقه مندی ها');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'پیغام خصوصی');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'صندوق پیغام');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'پیغام جدید');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'ارسالات');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'سطل بازیافت');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'تنظیمات');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'تماس ها');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'لیست سیاه');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'اطلاعات اضافی');
DEFINE('_KUNENA_MYPROFILE_NAME', 'نام');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'نام کاربری');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'ایمیل');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'نوع کاربر');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'تاریخ ثبت نام');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'تاریخ آخرین بازدید');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'پست ها');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'نمایش پروفایل');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'متن شخصی');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'جنس');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'تاریخ تولد');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'سال (YYYY) - ماه (MM) - رز (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'مکان');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'شماره ICQ شما.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'Tشناسه AOL Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'شناسه Yahoo! Instant Messenger.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'شناسه Skype.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'شناسه Gtalk.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'وب سایت');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'نام وب سایت');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'مثال: سایت من');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'URL وب سایت');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'مثال: www.mambolearn.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'شناسه MSN.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'امضا');
DEFINE('_KUNENA_MYPROFILE_MALE', 'مرد');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'زن');
DEFINE('_KUNENA_BULKMSG_DELETED', 'پست ها با موفقیت حذف شدند');
DEFINE('_KUNENA_DATE_YEAR', 'سال');
DEFINE('_KUNENA_DATE_MONTH', 'ماه');
DEFINE('_KUNENA_DATE_WEEK','هفته');
DEFINE('_KUNENA_DATE_DAY', 'روز');
DEFINE('_KUNENA_DATE_HOUR', 'ساعت');
DEFINE('_KUNENA_DATE_MINUTE', 'دقیقه');
DEFINE('_KUNENA_IN_FORUM', ' در انجمن: ');
DEFINE('_KUNENA_FORUM_AT', ' انجمن در: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'شکلک ها و امکانات ویرایشگر غیرفعال می باشد');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','ابزار انجمن');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','لیست کاربران');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s دارای <b>%d</b> کاربر ثبت شده می باشد');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','لطفا مقادیری را برای جستجو وارد کنید!');
DEFINE ('_KUNENA_USRL_SEARCH','جستجوی کاربران');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','جستجو');
DEFINE ('_KUNENA_USRL_LIST_ALL','لیست همه');
DEFINE ('_KUNENA_USRL_NAME','نام');
DEFINE ('_KUNENA_USRL_USERNAME','نام کاربری');
DEFINE ('_KUNENA_USRL_GROUP','گروه');
DEFINE ('_KUNENA_USRL_POSTS','ارسال ها');
DEFINE ('_KUNENA_USRL_KARMA','خوب');
DEFINE ('_KUNENA_USRL_HITS','بازدیدها');
DEFINE ('_KUNENA_USRL_EMAIL','ایمیل');
DEFINE ('_KUNENA_USRL_USERTYPE','درجه کاربری');
DEFINE ('_KUNENA_USRL_JOIN_DATE','تاریخ عضویت');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','آخرین زمان حضور در انجمن');
DEFINE ('_KUNENA_USRL_NEVER','هرگز');
DEFINE ('_KUNENA_USRL_ONLINE','وضعیت');
DEFINE ('_KUNENA_USRL_AVATAR','عکس');
DEFINE ('_KUNENA_USRL_ASC','صعودی');
DEFINE ('_KUNENA_USRL_DESC','نزولی');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','نمایش');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','ملحقات');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','لیست کاربران');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','تعداد کاربران در هر ردیف');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','تعداد کاربران در هر ردیف');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','وضعیت حضور');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','نمایش دهنده وضعیت حضور کاربران');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','نمایش آواتار');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','نمایش نام واقعی');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','نمایش نام کاربری');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','نمایش گروه کاربری');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','نمایش تعداد ارسالها');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','نمایش خوبی');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','نمایش ایمیل');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','نمایش نوع کاربر');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','نمایش تاریخ عضویت');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','نمایش آخرین تاریخ بازدید');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','نمایش تعداد بازدید از مشخصات');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');

// added by Ivo Apostolov: Cleaning up of admin.fireboard.php from hardcoded strings
DEFINE('_KUNENA_DBWIZ', 'بانک اطلاعاتی');
DEFINE('_KUNENA_DBMETHOD', 'لطفا گزینه مورد نظر را برای نصب انتخاب کنید');
DEFINE('_KUNENA_DBCLEAN', 'نصب جدید');
DEFINE('_KUNENA_DBUPGRADE', 'بروزرسانی از');
DEFINE('_KUNENA_TOPLEVEL', 'مجموعه اول');
DEFINE('_KUNENA_REGISTERED', 'عضو شده');
DEFINE('_KUNENA_PUBLICBACKEND', 'عمومی کاربران');
DEFINE('_KUNENA_SELECTANITEMTO', 'یک آیتم را انتخاب کنید برای');
DEFINE('_KUNENA_ERRORSUBS', 'یک ایراد در حذ پیغام یا باخبرسازی رخ داد');
DEFINE('_KUNENA_WARNING', 'اخطار....');
DEFINE('_KUNENA_CHMOD1', 'لطفا سطوح دسترسی را بررسی نمایید');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'فایل تنظیمات شما');
DEFINE('_KUNENA_FIREBOARD', 'Fireboard');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'انتخاب قالب');
DEFINE('_KUNENA_CFNW', 'اخطار : فایل تنظیمات قابل ویرایش نیست');
DEFINE('_KUNENA_CFS', 'فایل تنظیمات با موفقیت ذخیره شد');
DEFINE('_KUNENA_CFCNBO', 'اخطار : فایل مورد نظر قابل باز شدن نیست');
DEFINE('_KUNENA_TFINW', 'فایل قابل ویرایش نیست');
DEFINE('_KUNENA_KUNENACFS', 'CSS ذخیره سازی شد');
DEFINE('_KUNENA_SELECTMODTO', 'مدیری را انتخاب کنید برای');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'شما میبایست انجمنی را انتخاب کنید');
DEFINE('_KUNENA_DELMSGERROR', 'حذف پیغام با خطا روبرو شد:');
DEFINE('_KUNENA_DELMSGERROR1', 'حذف متن پیغام با خطا روبرو شد:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'پاکسازی باخبرسازی ها موفقیت امیز نبود:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'پاکسازی انجمن برای');
DEFINE('_KUNENA_PRUNEDAYS', 'روز');
DEFINE('_KUNENA_PRUNEDELETED', 'حذف شده:');
DEFINE('_KUNENA_PRUNETHREADS', 'موضوعات');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'خطا در پاکسازی کردن کاربران:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'کاربران پاکسازی شدند. حذف شده ها:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'مشخصات کاربران:');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'هیچ مشخصاتی واجد شرایط پاکسازی پیدا نشد.');
DEFINE('_KUNENA_TABLESUPGRADED', 'جداول انجمن به این نسخه ارتقا یافت');
// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - SAMPLE DATA
DEFINE('_KUNENA_FORUMCATEGORY', 'مجموعه انجمن');
DEFINE('_KUNENA_SAMPLWARN1', '-- مطمئن شوید که اطلاعات نمونه در جداول خالی انجمن بارگذاری شده باشد. در غیر اینصورت بدرستی کار نخواهد کرد!');
DEFINE('_KUNENA_FORUM1', 'انجمن 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'ارسال آزمایشی 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'انجمن ازمایشی 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]ارسال ازمایشی[/color][/size][/b]\nپیکربندی انجمن جدید شما!\n\n[url=http://mambolearn.com]- بهترین جوملا[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'اطلاعات نمونه بارگزاری شد');
// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - Other adjustments to the file
DEFINE('_KUNENA_CBADDED', 'مشخصات Community Builder اضافه گردید');
DEFINE('_KUNENA_IMGDELETED', 'تصویر حذف شد');
DEFINE('_KUNENA_FILEDELETED', 'فایل حذف شد');
DEFINE('_KUNENA_NOPARENT', 'شاخه اصلی');
DEFINE('_KUNENA_DIRCOPERR', 'ایراد: فایل');
DEFINE('_KUNENA_DIRCOPERR1', 'کپی نشد!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>کامپوننت</strong> انجمن گفتگو <em>برای سیستم مدیریت محتوای جوملا</em> <br />&copy; 2006 - 2007 برای بهترین جوملا<br>کلیه حقوق محفوظ است.');
DEFINE('_KUNENA_INSTALL2', 'انتقال/نصب :</code></strong><br /><br /><font color="red"><b>با موفیت انجام شد');
// added by aliyar 
DEFINE('_KUNENA_FORUMPRF_TITLE', 'تنظیمات مشخصات');
DEFINE('_KUNENA_FORUMPRF', 'مشخصات');
DEFINE('_KUNENA_FORUMPRRDESC', 'اگر شما کامپوننت Clexus PM یا Community Builder را نصب کرده اید،می توانید از آنها در مشخصات کاربران انجمن استفاده نمایید.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'مشخصات');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>دیدن مشخصات</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'تمام پیغام های انجمن');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'موضوع ها');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'شروع شده توسط');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'مجموعه ها');
DEFINE('_KUNENA_USERPROFILE_DATE', 'تاریخ');
DEFINE('_KUNENA_USERPROFILE_HITS', 'بازدیدها');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'ارسالی در انجمن وجود ندارد');
DEFINE('_KUNENA_TOTALFAVORITE', 'مورد علاقه:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'تعداد مجموعه های زیر گروه در هر ستون  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'مشخص نمودن تعداد مجموعه های زیر گروه ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'گزینه باخبرسازی همیشه انتخاب شده باشد؟');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'با انتخاب &quot;بله&quot; گزینه باخبر سازی همیشه انتخاب شده است');
// Added by Ivo Apostolov
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'مجموعه / انجمن باید نامی داشته باشد');
// Forum Configuration (New in Fireboard)
DEFINE('_KUNENA_SHOWSTATS', 'نمایش آمار');
DEFINE('_KUNENA_SHOWSTATSDESC', 'برای نمایش آمار گزینه بله را انتخاب نمایید');
DEFINE('_KUNENA_SHOWWHOIS', 'چه کسانی آنلاین هستند');
DEFINE('_KUNENA_SHOWWHOISDESC', 'برای نمایش اینکه چه کسانی آنلاین هستند این گزینه بله را انتخاب نمایید');
DEFINE('_KUNENA_STATSGENERAL', 'نمایش آمار کلی');
DEFINE('_KUNENA_STATSGENERALDESC', 'برای نمایش آمار کلی گزینه بله را انتخاب نمایید');
DEFINE('_KUNENA_USERSTATS', 'نمایش آمار محبوب ترین کاربران');
DEFINE('_KUNENA_USERSTATSDESC', 'برای نمایش محبوب ترین کاربران گزینه بله را انتخاب نمایید');
DEFINE('_KUNENA_USERNUM', 'تعداد کاربران محبوب');
DEFINE('_KUNENA_USERPOPULAR', 'نمایش آمار محبوب ترین موضوع ها');
DEFINE('_KUNENA_USERPOPULARDESC', 'برای نمایش محبوب ترین موضوع ها گزینه بله را انتخاب نمایید');
DEFINE('_KUNENA_NUMPOP', 'تعداد موضوع های محبوب');
DEFINE('_KUNENA_INFORMATION',
    '');
DEFINE('_KUNENA_INSTRUCTIONS', 'دستورالعمل ها');
DEFINE('_KUNENA_FINFO', 'اطلاعات انجمن Fireboard');
DEFINE('_KUNENA_CSSEDITOR', 'ویرایشگر CSS قالب انجمن');
DEFINE('_KUNENA_PATH', 'مسیر:');
DEFINE('_KUNENA_CSSERROR', 'توجه کنید که قالب فایل CSS باید قابل ویرایش باشد');
// User Management
DEFINE('_KUNENA_FUM', 'مدیریت مشخصات کاربران انجمن');
DEFINE('_KUNENA_SORTID', 'مرتب سازی بر اساس شناسه');
DEFINE('_KUNENA_SORTMOD', 'مرتب سازی براساس مدیران');
DEFINE('_KUNENA_SORTNAME', 'مرتب سازی بر اساس نام');
DEFINE('_KUNENA_VIEW', 'View');
DEFINE('_KUNENA_NOUSERSFOUND', 'مشخصات هیچ کاربری وجود ندارد.');
DEFINE('_KUNENA_ADDMOD', 'اضافه نمودن مدیر به');
DEFINE('_KUNENA_NOMODSAV', 'هیچ مدیری پیدا نشد ، لطفا متن زیر را مطالعه نمایید.');
DEFINE('_KUNENA_NOTEUS',
    'توجه: فقط کاربرانی که در مشخصات خود در انجمن پرچم مدیریتی دارند نمایش داده می شوند. برای ایجاد مدیر با دسترسی مدیریتی به <a href="index2.php؟option=com_fireboard&task=profiles">مدیریت کاربران</a> مراجعه نموده و کاربری را که می خواهید مدیر شود را انتخاب نمایید . و سپس مشخصات کاربر را به مدیر تغییر دهید. پرچم مدیریت فقط توسط مدیرکل انجمن قابل اعطا می باشد. ');
DEFINE('_KUNENA_PROFFOR', 'مشخصات برای');
DEFINE('_KUNENA_GENPROF', 'گزینه های اصلی مشخصات');
DEFINE('_KUNENA_PREFVIEW', 'نحوه نمایش:');
DEFINE('_KUNENA_PREFOR', 'نحوه نمایش پیغام ها:');
DEFINE('_KUNENA_ISMOD', 'انتصاب به عنوان مدیر:');
DEFINE('_KUNENA_ISADM', '<strong>بله</strong> (غیرقابل تغییر، این کاربر مدیر ارشد سایت است');
DEFINE('_KUNENA_COLOR', 'Color');
DEFINE('_KUNENA_UAVATAR', 'آواتور کاربر:');
DEFINE('_KUNENA_NS', 'هیچ گزینه ای انتخاب نشده');
DEFINE('_KUNENA_DELSIG', ' برای حذف امضا این گزینه را انتخاب نمایید ');
DEFINE('_KUNENA_DELAV', ' با انتخاب این گزینه آواتور حذف می شود');
DEFINE('_KUNENA_SUBFOR', 'باخبرسازی برای');
DEFINE('_KUNENA_NOSUBS', 'هیچگونه باخبرسازی برای این کاربر وجود ندارد');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'اصلی');
DEFINE('_KUNENA_BASICSFORUM', 'اطلاعات اصلی انجمن');
DEFINE('_KUNENA_PARENT', 'شاخه اصلی:');
DEFINE('_KUNENA_PARENTDESC',
    'توجه: برای ایجاد مجموعه \'شاخه اصلی\' را انتخاب نمایید. مجموعه شامل چندین انجمن می باشد.<br />انجمن می تواند  <strong>فقط</strong> در مجموعه ها ایجاد شود ، برای این کار یک مجموعه را برای ساختن انجمن در آن انتخاب نمایید.<br /> امکان ارسال در مجموعه ها <strong>وجود ندارد</strong> و فقط امکان ارسال در انجمن ها ممکن می باشد.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'نام انجمن و توضیحات');
DEFINE('_KUNENA_NAMEADD', 'نام:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'توضیحات:');
DEFINE('_KUNENA_ADVANCEDDESC', 'تنظیمات پیشرفته انجمن');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'امنیت و دسترسی انجمن');
DEFINE('_KUNENA_LOCKEDDESC', 'با انتخاب &quot;بله&quot; این انجمن قفل می شود، فقط مدیران می توانند موضوع ایجاد و پاسخ دهند (یا موضوع را منتقل کنند).');
DEFINE('_KUNENA_LOCKED1', 'قفل شده:');
DEFINE('_KUNENA_PUBACC', 'سطح دسترسی عمومی:');
DEFINE('_KUNENA_PUBACCDESC',
    'برای ایجاد یک انجمن غیرعمومی شما می توانید سطح دسترسی های مختلف را برای ورود به انجمن انتخاب نمایید. سطح دسترسی پیش فرض که کمترین سطح دسترسی &quot;همه&quot; می باشد.');
DEFINE('_KUNENA_CGROUPS', 'شامل گروه های کوچکتر:');
DEFINE('_KUNENA_CGROUPSDESC', 'آیا گروه های کوچک تر امکان دسترسی داشته باشند؟  با انتخاب &quot;خیر&quot; دسترسی به انجمن <b>فقط</b> توسط گروه های مشخص شده ممکن می باشد');
DEFINE('_KUNENA_ADMINLEVEL', 'سطح دسترسی مدیر:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'اگر شما یک انجمن برای سطح دسترسی عمومی ایجاد کردید، شما می توانید سطح دسترسی مدیریتی برای آن نیز ایجاد کنید.<br /> اگر می خواهید که دسترسی مدیران را محدود کنید می توانید سطح دسترسی مربوطه را انتخاب نمایید.');
DEFINE('_KUNENA_ADVANCED', 'پیشرفته');
DEFINE('_KUNENA_CGROUPS1', 'شامل گروه های کوچکتر:');
DEFINE('_KUNENA_CGROUPS1DESC', 'آیا گروه های کوچک تر امکان دسترسی داشته باشند؟ با انتخاب &quot;خیر&quot; دسترسی به انجمن <b>فقط</b> توسط گروه های مشخص شده ممکن می باشد');
DEFINE('_KUNENA_REV', 'مرور ارسال ها:');
DEFINE('_KUNENA_REVDESC',
    'با انتخاب &quot;بله&quot; ارسال های انجمن قبل از انتشار باید توسط مدیر مرور و تایید شوند. این گزینه فقط برای انجمن های مدیریت شده مفید می باشد!<br />اگر شما مدیری را انتخاب نکرده باشید، مدیر اصلی سایت باید ارسال ها را مرور و تایید نماید');
DEFINE('_KUNENA_MOD_NEW', 'مدیریت');
DEFINE('_KUNENA_MODNEWDESC', 'مدیریت انجمن ها');
DEFINE('_KUNENA_MOD', 'مدیر:');
DEFINE('_KUNENA_MODDESC',
    'با انتخاب &quot;بله&quot; می توانید برای آن انجمن یک مدیر انتخاب نمایید.<br /><strong>توجه:</strong> این بدین معنا نیست که ارسال های جدید باید توسط مدیر مرور شوند!<br /> برای این کار شما نیاز دارید که گزینه &quot;مرور&quot; را از سربرگ پیشرفته انتخاب نمایید.<br /><br /> <strong>توجه مهم:</strong> بعد از تنظیم گزینه مدیریت به &quot;بله&quot; و ذخیره تنظمیات شما می توانید با استفادع از آیکون "مدیر جدید" مدیر انجمن را انتخاب نمایید.');
DEFINE('_KUNENA_MODHEADER', 'تنظیمات مدیریت برای این انجمن');
DEFINE('_KUNENA_MODSASSIGNED', 'مدیر انتصاب شده به این انجمن:');
DEFINE('_KUNENA_NOMODS', 'هیچ مدیری به این انجمن انتصاب داده نشده');
// Some General Strings (Improvement in Fireboard)
DEFINE('_KUNENA_EDIT', 'ویرایش');
DEFINE('_KUNENA_ADD', 'ایجاد');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'انتقال به بالا');
DEFINE('_KUNENA_MOVEDOWN', 'انتقال به پایین');
// Groups - Integration in Fireboard
DEFINE('_KUNENA_ALLREGISTERED', 'همه کاربران ثبت شده');
DEFINE('_KUNENA_EVERYBODY', 'همه');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'چینش');
DEFINE('_KUNENA_CHECKEDOUT', 'Check Out');
DEFINE('_KUNENA_ADMINACCESS', 'دسترسی مدیر');
DEFINE('_KUNENA_PUBLICACCESS', 'دسترسی عمومی');
DEFINE('_KUNENA_PUBLISHED', 'وضعیت انتشار');
DEFINE('_KUNENA_REVIEW', 'مرور');
DEFINE('_KUNENA_MODERATED', 'مدیر');
DEFINE('_KUNENA_LOCKED', 'قفل شده');
DEFINE('_KUNENA_CATFOR', 'مجموعه / انجمن');
DEFINE('_KUNENA_ADMIN', '&nbsp;مدیریت انجمن');
DEFINE('_KUNENA_CP', '&nbsp;مدیریت انجمن گفتگو');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'مجتمع سازی آواتور');
DEFINE('_COM_A_RANKS_SETTINGS', 'رتبه');
DEFINE('_COM_A_RANKING_SETTINGS', 'تنظیمات رتبه دهی');
DEFINE('_COM_A_AVATAR_SETTINGS', 'تنظیمات آواتور');
DEFINE('_COM_A_SECURITY_SETTINGS', 'تنظیمات امنیتی');
DEFINE('_COM_A_BASIC_SETTINGS', 'تنظیمات اساسی');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'اجازه علاقه مندی');
DEFINE('_COM_A_FAVORITES_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند موضوعات مورد علاقه خود را انتخاب نمایند ');
DEFINE('_USER_UNFAVORITE_ALL', 'با انتخاب این گزینه تمامی علاقه مندی ها  لغو می شوند');
DEFINE('_VIEW_FAVORITETXT', 'علاقه مندی به این موضوع ');
DEFINE('_USER_UNFAVORITE_YES', 'شما به این تاپیک علاقه مندی ندارید');
DEFINE('_POST_FAVORITED_TOPIC', 'علاقه مندی شما 
پردازش شد.');
DEFINE('_VIEW_UNFAVORITETXT', 'علاقه مندی');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'باخبرسازی');
DEFINE('_USER_NOFAVORITES', 'هیچ موضوع مورد علاقه ای وجود ندارد');
DEFINE('_POST_SUCCESS_FAVORITE', 'علاقه مندی شما پردازش شد.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'نتایج جستجو');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'نمایش تعداد نتایج جستجو در هر صفحه ');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'استفاده از قالب جوملا؟');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'اگر گزینه استفاده از قالب جوملا را برابر بله قرار دهید. (class: مانند sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'نمایش تصاویر انجمن های زیر گروه');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'برای نمایش آیکون کوچک در کنار انجمن های زیر مجموعه گزینه بله را انتخاب نمایید. ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'نمایش اخبار');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'با انتخاب "بله" ، یک جعبه اخبار در صفحه اصلی نمایش داده می شود.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'نمایش آواتور در لیست مجموعه ها؟');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'با انتخاب "بله" ، آواتور کاربر در لیست مجموعه انجمن شما نمایش داده می شود.');
DEFINE('_KUNENA_RECENT_POSTS', 'تنظیمات آخرین ارسال ها');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'نمایش ارسال های جدید');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'با انتخاب "بله" آخرین ارسال های انجمن با استفاده از پلاگین نمایش داده می شود');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'تعداد ارسال های جدید');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'تعداد ارسال های جدید');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'تعداد در هر سربرگ ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'تعداد ارسال ها در هر سربرگ');
DEFINE('_KUNENA_LATEST_CATEGORY', 'نمایش مجموعه');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'مشخص کردن مجموعه های خاص برای اخرین ارسال ها. برای مثال:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'نمایش یک موضوع');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'نمایش یک موضوع');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'نمایش پاسخ به موضوع');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'نمایش پاسخ به موضوع (پاسخ:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'طول موضوع');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'طول موضوع');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'نمایش تاریخ');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'نمایش تاریخ');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'نمایش بازدیدها');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'نمایش بازدیدها');
DEFINE('_KUNENA_SHOW_AUTHOR', 'نمایش نویسنده');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=نام کاربری، 2=نام واقعی، 0=عدم نمایش');
DEFINE('_KUNENA_STATS', 'تنظیمات پلاگین آمار');
DEFINE('_KUNENA_CATIMAGEPATH', 'مسیر تصویر مجموعه ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'مسیر تصویر مجموعه. اگر مسیر "category_images/" باشد مسیر اصلی "components/com_fireboard/category_images/ می باشد');
DEFINE('_KUNENA_ANN_MODID', 'شناسه مدیران خبری ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'اضافه کردن شناسه مدیران برای اخبار. مثال. 62,63,73 . مدیران خبری امکان ایجاد ، ویرایش و حذف اخبار را دارند.');
//
DEFINE('_KUNENA_FORUM_TOP', 'مجموعه های انجمن ');
DEFINE('_KUNENA_CHILD_BOARDS', 'انجمن زیر شاخه ');
DEFINE('_KUNENA_QUICKMSG', 'پاسخ سریع ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'موضوعات این انجمن ');
DEFINE('_KUNENA_FORUM', 'انجمن ');
DEFINE('_KUNENA_SPOTS', 'موضوع مهم');
DEFINE('_KUNENA_CANCEL', 'لغو');
DEFINE('_KUNENA_TOPIC', 'موضوع: ');
DEFINE('_KUNENA_POWEREDBY', '');
DEFINE('_KUNENA_DEVELOPBY', '');
// Time Format
DEFINE('_TIME_TODAY', '<b>امروز</b> ');
DEFINE('_TIME_YESTERDAY', '<b>دیروز</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'آخرین ارسال ها');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'چه کسانی آنلاین هستند');
DEFINE('_KUNENA_WHO_MAINPAGE', 'انجمن اصلی');
DEFINE('_KUNENA_GUEST', 'مهمان');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'بازدیدکننده');
DEFINE('_KUNENA_ATTACH', 'پیوست');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'علاقه مندی');
DEFINE('_USER_FAVORITES', 'علاقه مندی های شما');
DEFINE('_THREAD_UNFAVORITE', 'حذف از علاقه مندی');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'خوش آمدید');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'نمایش آخرین ارسال ها');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'انتخاب آواتور');
DEFINE('_PROFILEBOX_MYPROFILE', 'مشخصات من');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'نمایش ارسال های من');
DEFINE('_PROFILEBOX_GUEST', 'مهمان');
DEFINE('_PROFILEBOX_LOGIN', 'ورود');
DEFINE('_PROFILEBOX_REGISTER', 'ثبت نام');
DEFINE('_PROFILEBOX_LOGOUT', 'خروج');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'آیا رمز عبور را فراموش کرده اید؟');
DEFINE('_PROFILEBOX_PLEASE', 'لطفا');
DEFINE('_PROFILEBOX_OR', 'یا');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'ارسال های جدید');
DEFINE('_RECENT_TOPICS', 'موضوع');
DEFINE('_RECENT_AUTHOR', 'ایجاد کننده');
DEFINE('_RECENT_CATEGORIES', 'مجموعه');
DEFINE('_RECENT_DATE', 'تاریخ');
DEFINE('_RECENT_HITS', 'بازدیدها');
// announcement
DEFINE('_ANN_ID', 'شناسه');
DEFINE('_ANN_DATE', 'تاریخ');
DEFINE('_ANN_TITLE', 'عنوان');
DEFINE('_ANN_SORTTEXT', 'متن کوتاه');
DEFINE('_ANN_LONGTEXT', 'متن بلند');
DEFINE('_ANN_ORDER', 'چینش');
DEFINE('_ANN_PUBLISH', 'انتشار');
DEFINE('_ANN_PUBLISHED', 'منتشر شده');
DEFINE('_ANN_UNPUBLISHED', 'منتشر نشده');
DEFINE('_ANN_EDIT', 'ویرایش');
DEFINE('_ANN_DELETE', 'حذف');
DEFINE('_ANN_SUCCESS', 'موفقیت آمیز');
DEFINE('_ANN_SAVE', 'ذخیره');
DEFINE('_ANN_YES', 'بله');
DEFINE('_ANN_NO', 'خیر');
DEFINE('_ANN_ADD', 'جدید');
DEFINE('_ANN_SUCCESS_EDIT', 'با موفقیت ویرایش شد');
DEFINE('_ANN_SUCCESS_ADD', 'با موفقیت اضافه شد');
DEFINE('_ANN_DELETED', 'با موفقیت حذف شد');
DEFINE('_ANN_ERROR', 'ایراد');
DEFINE('_ANN_READMORE', 'ادادمه مطلب...');
DEFINE('_ANN_CPANEL', 'کنترل پنل اخبار');
DEFINE('_ANN_SHOWDATE', 'نمایش تاریخ');
// Stats
DEFINE('_STAT_FORUMSTATS', 'آمار انجمن');
DEFINE('_STAT_GENERAL_STATS', 'آمار کلی');
DEFINE('_STAT_TOTAL_USERS', 'تعداد اعضا');
DEFINE('_STAT_LATEST_MEMBERS', 'آخرین عضو');
DEFINE('_STAT_PROFILE_INFO', 'دیدن اطلاعات مشخصات');
DEFINE('_STAT_TOTAL_MESSAGES', 'تعداد ارسال ها');
DEFINE('_STAT_TOTAL_SUBJECTS', 'تعداد موضوع ها');
DEFINE('_STAT_TOTAL_CATEGORIES', 'تعداد مجموعه ها');
DEFINE('_STAT_TOTAL_SECTIONS', 'تعداد بخش ها');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'تعداد موضوع های امروز');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'تعداد موضوع های دیروز');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'تعداد ارسال های امروز');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'تعداد ارسال های دیروز');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'دیدین ارسال های جدید');
DEFINE('_STAT_MORE_ABOUT_STATS', 'سایر آمار و اطلاعات');
DEFINE('_STAT_USERLIST', 'لیست کاربران');
DEFINE('_STAT_TEAMLIST', 'تیم انجمن');
DEFINE('_STATS_FORUM_STATS', 'آمار انجمن');
DEFINE('_STAT_POPULAR', '');
DEFINE('_STAT_POPULAR_USER_TMSG', 'کاربر برتر ( براساس تعداد ارسال ها) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'موضوع برتر ');
DEFINE('_STAT_POPULAR_USER_GSG', 'کاربر برتر ( براساس تعداد بازدیدها از مشخصات) ');
//Team List
DEFINE('_MODLIST_STATUS', 'Status');
DEFINE('_MODLIST_USERNAME', 'نام کاربری');
DEFINE('_MODLIST_FORUMS', 'انجمن ها');
DEFINE('_MODLIST_ONLINE', 'کاربر آنلاین می باشد');
DEFINE('_MODLIST_OFFLINE', 'کاربر آفلاین می باشد');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'چه کسانی آنلاین هستند');
DEFINE('_WHO_ONLINE_NOW', 'آنلاین');
DEFINE('_WHO_ONLINE_MEMBERS', 'عضو');
DEFINE('_WHO_AND', 'و');
DEFINE('_WHO_ONLINE_GUESTS', 'مهمان');
DEFINE('_WHO_ONLINE_USER', 'کاربر');
DEFINE('_WHO_ONLINE_TIME', 'زمان');
DEFINE('_WHO_ONLINE_FUNC', 'عمل');
// Userlist
DEFINE('_USRL_USERLIST', 'لیست کاربران');
DEFINE('_USRL_REGISTERED_USERS', '%s دارای <b>%d</b> کاربر ثیت شده می باشد');
DEFINE('_USRL_SEARCH_ALERT', 'یک عبارت برای جستجو وارد نمایید!');
DEFINE('_USRL_SEARCH', 'جستجو کاربر');
DEFINE('_USRL_SEARCH_BUTTON', 'جستجو');
DEFINE('_USRL_LIST_ALL', 'تمام لیست');
DEFINE('_USRL_NAME', 'نام');
DEFINE('_USRL_USERNAME', 'نام کاربری');
DEFINE('_USRL_EMAIL', 'ایمیل');
DEFINE('_USRL_USERTYPE', 'نوع کاربر');
DEFINE('_USRL_JOIN_DATE', 'تاریخ عضویت');
DEFINE('_USRL_LAST_LOGIN', 'آخرین ورود');
DEFINE('_USRL_NEVER', 'هرگز');
DEFINE('_USRL_BLOCK', 'Status');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'صعودب');
DEFINE('_USRL_DESC', 'نزولی');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_PARTYSTAFF', 'Staff');
DEFINE('_USRL_AKOSTAFF', 'AkoStaff');
DEFINE('_USRL_USEREXTENDED', 'جزئیات');
DEFINE('_USRL_COMPROFILER', 'مشخصات');
DEFINE('_USRL_THUMBNAIL', 'Pic');
DEFINE('_USRL_READON', 'نمایش');
DEFINE('_USRL_MAMBLOG', 'Mamblog');
DEFINE('_USRL_VIEWBLOG', 'View blog');
DEFINE('_USRL_NOBLOG', 'No blog');
DEFINE('_USRL_MYHOMEPAGE', 'صفحه خانگی من');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_ADD_BUDDY', 'اضافه نمودن به لیست دوستان');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'ارسال پیغام خصوصی');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'نتایح جستجو برای');
DEFINE('_USRL_STATUS', 'Status');
DEFINE('_USRL_LISTSETTINGS', 'تنظیمات لیست کاربران');
DEFINE('_USRL_ERROR', 'ایراد');
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
//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'کامپوننت پیغام خصوصی');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'جستجو برای: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'آیا برای حذف این پیغام مطمئن هستید؟ \n\n توجه: هیچ راهی بازیابی پیغام حذف شده وجود ندارد!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'ارسال حذف گردید');
DEFINE('_COM_A_KUNENA_BY', 'کامپوننت انجمن جوملا توسط');
DEFINE('_COM_A_RANKING', 'سیستم رتبه دهی');
DEFINE('_COM_A_BOT_REFERENCE', 'نمایش چارت مرجع Bot');
DEFINE('_COM_A_MOSBOT', 'فعال سازی Discuss Bot');
DEFINE('_PREVIEW', 'پیش نمایش');
DEFINE('_COM_C_UPGRADE', 'ارتقا پایگاه داده به: ');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'با فعال کردن Discuss Bot کاربران می توانند درباره محتواهای سایت در انجمن بحث نمایند. عنوان محتوا به عنوان عنوان موضوع استفاده می شود.'
           . '<br />اگر موضوع موجود نباشد ، ایجاد می شود و اگر موضوع موجود باشد آن موضوع نمایش داده شده و امکان پاسخ به آن موضوع بوجود می آید.' . '<br /><strong>شما نیاز دارید Bot را جدا دریافت نموده و نصب نمایید.</strong>'
           . '<br />برای اطلاعات بیشتر به سایت <a href="http://www.mambolearn.com">سایت بهرتین جوملا</a> مراجعه نمایید.' . '<br />زمانی که نصب تمام شد شما نیاز دارید که خط زیر را به محتوا خود اضافه نمایید:' . '<br /><span dir="ltr">{mos_KUNENA_discuss:<em>catid</em>}</span>'
           . '<br /> <em>catid</em> شناسه مجموعه برای موضوع را مشخص می کند. برای یافتن catid، در داخل انجمن ها ' . 'با نگاه کردن بر روی آدرس انجمن در نوار آدرس شناسه را متوجه بشوید.'
           . '<br />مثال: برای بحث در مورد مقاله با catid 26،  Bot باید شبیه این باشد: {mos_KUNENA_discuss:26}');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'جستجو');
DEFINE('_FORUM_SEARCHRESULTS', 'نمایش %s لز %s نتیجه.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'پرسش و پاسخ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'قوانین');
DEFINE('_COM_FORUM_RULES_DESC', 'قوانین انجمن');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'کدهای انجمن');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'ارسال تایید شد');
DEFINE('_MODERATION_DELETE_ERROR', 'ایراد: ارسال نمی تواند حذف شود');
DEFINE('_MODERATION_APPROVE_ERROR', 'ایراد: ارسال نمی تواند تایید شود');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'هیچ انجمنی در این مجموعه وجود ندارد!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'عدم موفقیت در ایجاد یک رونوشت از تاپیک قدیمی!');
DEFINE('_POST_MOVE_GHOST', 'گذاشتن یک رونوشت در انجمن قبلی');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'پرش به انجمن');
DEFINE('_COM_A_FORUM_JUMP', 'فعال پرش به انجمن');
DEFINE('_COM_A_FORUM_JUMP_DESC', ' با انتخاب &quot;بله&quot; یک گزینه جهت رفتن سریع به مجموعه ها و انجمن ها نمایش داده می شود.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'قوانین');
DEFINE('_COM_A_RULESPAGE', 'فعال سازی صفحه قوانین');
DEFINE('_COM_A_RULESPAGE_DESC',
    'با انتخاب &quot;بله&quot;یک لینک در منوی بالا انجمن نمایش داده می شود که قوانین انجمن شما را نمایش می دهد.  ساختار محتویات قوانین از فایل rules.php در /joomla_root/components/com_fireboard فراخوانی می شود. <em>یک پشتیبان از این فایل داشته باشید تا در هنگام ارتقا آن را جایگزین نمایید!</em>');
DEFINE('_MOVED_TOPIC', 'منتقل شده:');
DEFINE('_COM_A_PDF', 'فعالی سازی ایجاد PDF');
DEFINE('_COM_A_PDF_DESC',
    'با انتخاب &quot;بله&quot; کاربران می توانند محتویات هر موضوع را به صورت PDF دریافت کنند.<br />این یک سند PDF <u>ساده</u> می باشد، هیچگونه لایه بندی و گرافیکی وجود ندارد.');
DEFINE('_GEN_PDFA', 'با کلیک بر روی این آیکون سند PDF ساخته می شود (در پنجره جدید باز می شود).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'برای دیدن مشخصات کاربر اینجا کلیک نمایید');
DEFINE('_VIEW_ADDBUDDY', 'برای اضافه کردن این کاربر به لیست دوستان اینجا کلیک نمایید');
DEFINE('_POST_SUCCESS_POSTED', 'پیام شما با موفقیت ارسال گردید');
DEFINE('_POST_SUCCESS_VIEW', '[ بازگشت به موضوع ]');
DEFINE('_POST_SUCCESS_FORUM', '[ بازگشت به انجمن ]');
DEFINE('_RANK_ADMINISTRATOR', 'مدیر ارشد');
DEFINE('_RANK_MODERATOR', 'مدیر');
DEFINE('_SHOW_LASTVISIT', 'آخرین بازدید');
DEFINE('_COM_A_BADWORDS_TITLE', 'فیلتر کردن کلمات نامناسب');
DEFINE('_COM_A_BADWORDS', 'استفاده از فیلتر نمودن کلمات نامناسب');
DEFINE('_COM_A_BADWORDS_DESC', 'با انتخاب &quot;بله&quot; با کلمات نامناسب طبق تنظیمات کامپوننت کلمات نامناسب برخورد می شود. برای استفاده از این امکان باید کامپوننت کلمات نامناسب نصب باشد!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* متن پیغام شما سانسور شده زیرا شامل چندین کلمه نامناسب بود *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'ساخت مشخصات انجمن برای  Community Builder');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'کلیک بر روی این لینک برای ایجاد فیلدهای لازم در مشخصات Community Builder لازم می باشد. بعد از اینجام این کار شما ازاد هستید که از مدیر Community Builder استفاده نمایید، فقط گزینه ها و نام ها را تغییر ندهید. اگر شما مدیر Community Builder را حذف نمودید، می توانید مجددا از همین لینک آن را ایجاد نمایید، درغیر اینصورت چندین بار بر روی این لینک کلیک نکنید!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> اینجا کلیک کنید <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder مشخصات کاربران');
DEFINE('_COM_A_COMBUILDER_DESC',
    'با انتخاب &quot;بله&quot; مجتمع سازی با کامپوننت Community Builder فعال می شود (www.joomlapolis.com). تمام کاربران انجمن امکان اتصال به Community Builder و لینک دادن مشخصات در انجمن به مشخصات Community Builder را دارا می باشند. این تنظیمات باعث باطل شدن تنظیمات مشخصات Clexus PMمی شود . همچنین، برای این اتصال باید گزینه زیر را برای تغییرات در پایگاه داده Community Builder انجام دهید.');
DEFINE('_COM_A_AVATAR_SRC', 'استفاده از آواتورهای');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'اگر شما کامپوننت Clexus PM یا Community Builder را نصب کرده اید، شما می توانید با پیکربندی انجمن از تصاویر آواتور Clexus PM یا Community Builder در مشخصات کاربران استفاده نمایید. توجه: برای Community Builder شما باید گزینه تصاویر بندانگشتی را فعال نمایید چون انجمن از تصاویر بندانگشتی برای آواتور استفاده می کند.');
DEFINE('_COM_A_KARMA', 'نمایش نماد خوب');
DEFINE('_COM_A_KARMA_DESC', 'با انتخاب &quot;بله&quot; دکمه های (بد / خوب) برای کاربران فعال می شود.');
DEFINE('_COM_A_DISEMOTICONS', 'غیر فعال بودن صورتک ها');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'با انتخاب &quot;بله&quot; صورتک ها غیرفعال می شود.');
DEFINE('_COM_C_KUNENACONFIG', 'پیکربندی<br/>انجمن');
DEFINE('_COM_C_KUNENACONFIGDESC', 'پیکربندی کلی انجمن');
DEFINE('_COM_C_FORUM', 'مدیریت<br/>انجمن');
DEFINE('_COM_C_FORUMDESC', 'ایجاد مجموعه ها/انجمن ها و پیکربندی آنها');
DEFINE('_COM_C_USER', 'مدیریت<br/>کاربران');
DEFINE('_COM_C_USERDESC', 'مدیریت کاربران و مشخصات آنها');
DEFINE('_COM_C_FILES', 'مدیریت<br/>فایل های آپلود شده');
DEFINE('_COM_C_FILESDESC', 'مدیریت فایل های آپلود شده');
DEFINE('_COM_C_IMAGES', 'مدیریت<br/>تصاویر آپلود شده');
DEFINE('_COM_C_IMAGESDESC', 'مدیریت تصاویر آپلود شده');
DEFINE('_COM_C_CSS', 'ویرایش<br/>فایل CSS');
DEFINE('_COM_C_CSSDESC', 'بهبود قالب انجمن');
DEFINE('_COM_C_SUPPORT', 'وب سایت<br/>پشتیبان');
DEFINE('_COM_C_SUPPORTDESC', 'اتصال به سایت پشتیبانی');
DEFINE('_COM_C_PRUNETAB', 'پاک سازی<br/>انجمن ها');
DEFINE('_COM_C_PRUNETABDESC', 'حذف موضوع های قدیمی (قابل پیکربندی)');
DEFINE('_COM_C_PRUNEUSERS', 'پاک سازی<br/>کاربران');
DEFINE('_COM_C_PRUNEUSERSDESC', 'انتقال جداول کاربر جوملا به جداول کاربر انجمن');
DEFINE('_COM_C_LOADSAMPLE', 'بارگذاری<br/>اطلاعات نمونه');
DEFINE('_COM_C_LOADSAMPLEDESC', 'برای شروع آسان اطلاعات نمونه انجمن را با پایگاه داده خالی جایگزین نمایید');
DEFINE('_COM_C_REMOVESAMPLE', 'حذف اطلاعات نمونه');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'حذف اطلاعات نمونه از بانک اطلاعاتی');
DEFINE('_COM_C_LOADMODPOS', 'بارگزاری موقعیت ماژول ها');
DEFINE('_COM_C_LOADMODPOSDESC', 'بارگزاری موقعیت ماژول ها در قالب انجمن');
DEFINE('_COM_C_UPGRADEDESC', 'دادن پایگاه به آخرین نسخه بعد از ارتقا');
DEFINE('_COM_C_BACK', 'بازگشت به مدیریت انجمن');
DEFINE('_SHOW_LAST_SINCE', 'موضوع های فعال پس از اخرین بازدید:');
DEFINE('_POST_SUCCESS_REQUEST2', 'درخواست شما در حال پردازش می باشد');
DEFINE('_POST_NO_PUBACCESS3', 'برای ثبت نام اینجا کلیک نمایید.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'پیغام با موفقیت حذف گردید.');
DEFINE('_POST_SUCCESS_EDIT', 'پیغام با موفقیت ویرایش گردید.');
DEFINE('_POST_SUCCESS_MOVE', 'موضوع با موفقیت منتقل گردید.');
DEFINE('_POST_SUCCESS_POST', 'پیام شما با موفقیت ارسال گردید.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'باخبر سازی شما در حال پردازش می باشد.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'خوب');
DEFINE('_KARMA_SMITE', 'بد');
DEFINE('_KARMA_APPLAUD', 'تشکر');
DEFINE('_KARMA_BACK', 'بازگشت به موضوع قبل,');
DEFINE('_KARMA_WAIT', 'شما می توانید رتبه را بعد از 6 ساعت تغییر دهید. <br/>لطفا صبر کنید تا زمانی که شخص دیگری رتبه بدهد.');
DEFINE('_KARMA_SELF_DECREASE', 'لطفا سعی نکنید دوباره به خودتان رتبه بدهید!');
DEFINE('_KARMA_SELF_INCREASE', 'در صورت تلاش برای افزایش رتبه ، رتبه شما کاهش خواهد یافت!');
DEFINE('_KARMA_DECREASED', 'رتبه کاربر کاهش یافت.  در صورتی که شما بعد از مدتی به موضوع برنگردید,');
DEFINE('_KARMA_INCREASED', 'رتبه کاربر افزایش یافت. در صورتی که شما بعد از مدتی به موضوع برنگردید,');
DEFINE('_COM_A_TEMPLATE', 'قالب');
DEFINE('_COM_A_TEMPLATE_DESC', 'انتخاب قالب مورد استفاده.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'انتخاب تصویر');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'انتخاب تصاویر قالب جهت استفاده.');
DEFINE('_PREVIEW_CLOSE', 'بستن این پنجره');
//==========================================
//new in 1.0 Stable
DEFINE('_GEN_PATHWAY', 'Boardwalk :: ');
DEFINE('_COM_A_POSTSTATSBAR', 'استفاده از ارسال ها در نوار امار');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'با انتخاب &quot;بله&quot; تعداد ارسال های کاربران به صورت گرافیکی نمایش داده می شود.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'شماره رنگ برای آمار کاربر');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'شماره رنگ مورد نظر را برای نوار ارسال ها انتخاب نمایید');
DEFINE('_LATEST_REDIRECT',
    'انجمن  قبل از انجام خواسته شما نیاز به پایداری مجدد دارد.\nنگران نباشید، این اتفاق پس از ورود و فعالیت نکردن به مدت 30 دقیقه عادی است.\nلطفا مجددا جستجو نمایید.');
DEFINE('_SMILE_COLOUR', 'رنگ');
DEFINE('_SMILE_SIZE', 'اندازه');
DEFINE('_COLOUR_DEFAULT', 'استاندارد');
DEFINE('_COLOUR_RED', 'قرمز');
DEFINE('_COLOUR_PURPLE', 'بنفش');
DEFINE('_COLOUR_BLUE', 'آبی');
DEFINE('_COLOUR_GREEN', 'سبز');
DEFINE('_COLOUR_YELLOW', 'زرد');
DEFINE('_COLOUR_ORANGE', 'نارنجی');
DEFINE('_COLOUR_DARKBLUE', 'آبی تیره');
DEFINE('_COLOUR_BROWN', 'قهوه ای');
DEFINE('_COLOUR_GOLD', 'طلایی');
DEFINE('_COLOUR_SILVER', 'نقره ای');
DEFINE('_SIZE_NORMAL', 'معمولی');
DEFINE('_SIZE_SMALL', 'کوچک');
DEFINE('_SIZE_VSMALL', 'خیلی کوچک');
DEFINE('_SIZE_BIG', 'بزرگ');
DEFINE('_SIZE_VBIG', 'خیلی بزرگ');
DEFINE('_IMAGE_SELECT_FILE', 'انتخاب فایل تصویر برای پیوست');
DEFINE('_FILE_SELECT_FILE', 'انتخاب فایل برای پیوست');
DEFINE('_FILE_NOT_UPLOADED', 'فایل شما آپلود نشد. دوباره ارسال کنید یا ارسال را ویرایش نمایید');
DEFINE('_IMAGE_NOT_UPLOADED', 'تصویر شما آپلود نشد. دوباره ارسال کنید یا ارسال را ویرایش نمایید');
DEFINE('_BBCODE_IMGPH', 'قرار دادن [img] جهت استفاده از تصویر پیوست شده در ارسال');
DEFINE('_BBCODE_FILEPH', 'قرار دادن [file] جهت استفاده از فایل پیوست شده در ارسال');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'با انتخاب این گزینه تمامی باخبرسازی ها لغو می شوند');
DEFINE('_LINK_JS_REMOVED', '<em>لینک فعال سازی شامل JavaScript به صورت خودکار حذف میشود</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'ظاهر و قیافه');
DEFINE('_COM_A_USERS', 'مرتبط به کاربر');
DEFINE('_COM_A_LENGTHS', 'تنظیملت طول های متفاوت');
DEFINE('_COM_A_SUBJECTLENGTH', 'حداکثر طول موضوع');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'حداکثر طول موضوع. حداکثر کاراکتر پشتیبانی شده 255 کاراکتر می باشد. اگر شما از سیستم Unicode، UTF-8 یا non-ISO-8599-x استفاده می کنید حداکثر کاراکتر از این فرمول بدست می آید:<br/><tt>round_down(255/(حداکثر کاراکتر))</tt><br/> برای مثال در UTF-8، برای حداکثر کاراکتر. هر کاراکتر فارسی برابر 4 کاراکتر انگلیسی می باشد: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'موضوع/انجمن');
DEFINE('_LATEST_NUMBER', 'ارسال جدید');
DEFINE('_COM_A_SHOWNEW', 'نمایش ارسال های جدید');
DEFINE('_COM_A_SHOWNEW_DESC', ' با انتخاب &quot;بله&quot; انجمن ارسال های جدید انجمن را از آخرین ورود کاربر نمایش می دهد.');
DEFINE('_COM_A_NEWCHAR', 'نماد &quot;جدید&quot;');
DEFINE('_COM_A_NEWCHAR_DESC', 'این نماد در جلوی موضوع های جدید نمایش داده می شود (مانند &quot;!&quot; یا &quot;جدید!&quot;)');
DEFINE('_LATEST_AUTHOR', 'آخرین ارسال توسط');
DEFINE('_GEN_FORUM_NEWPOST', 'ارسال جدید');
DEFINE('_GEN_FORUM_NOTNEW', 'ارسال جدیدی وجود ندارد');
DEFINE('_GEN_UNREAD', 'موضوع خوانده نشده');
DEFINE('_GEN_NOUNREAD', 'موضوع خوانده شده');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'نشانه گذاری تمامی انجمن ها به عنوان خوانده شده');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'نشانه گذاری این انجمن به عنوان خوانده شده');
DEFINE('_GEN_FORUM_MARKED', 'تمام موضوع های این انجمن به عنوان خوانده شده مشخص گردید');
DEFINE('_GEN_ALL_MARKED', 'تمام ارسال ها به عنوان خوانده شده مشخص گردید');
DEFINE('_IMAGE_UPLOAD', 'آپلود تصویر');
DEFINE('_IMAGE_DIMENSIONS', 'فایل تصویر شما باید حداکثر(عرض * ارتقاع - اندازه)');
DEFINE('_IMAGE_ERROR_TYPE', 'لطفا فقط از تصاویر jpeg, gif یا png استفاده نمایید');
DEFINE('_IMAGE_ERROR_EMPTY', 'لطفا قبل از آپلود یک فایل انتخاب نمایید');
DEFINE('_IMAGE_ERROR_SIZE', 'حجم فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است.');
DEFINE('_IMAGE_ERROR_WIDTH', 'عرض فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'ارتفاع فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است.');
DEFINE('_IMAGE_UPLOADED', 'تصویر شما آپلود گردید.');
DEFINE('_COM_A_IMAGE', 'تصاویر');
DEFINE('_COM_A_IMGHEIGHT', 'حداکثر ارتفاع تصویر');
DEFINE('_COM_A_IMGWIDTH', 'حداکثر عرض تصویر');
DEFINE('_COM_A_IMGSIZE', 'حداکثر حجم فایل تصویر <br/><em>به کیلوبایت</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'اجازه آپلود تصاویر به همه');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'با انتخاب &quot;بله&quot; تمامی کاربران می توانند تصویر آپلود کنند.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'اجازه آپلود به کاربران ثبت شده');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند تصویر آپلود کنند.<br/> توجه : مدیر ارشد و مدیران امکان آپلود فایل را همیشه دارند.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'آپلودها');
DEFINE('_FILE_TYPES', 'نوع فایل - حداکثر حجم');
DEFINE('_FILE_ERROR_TYPE', 'شما فقط می توانید تصاویر با این پسوند ها را آپلود نمایید:\n');
DEFINE('_FILE_ERROR_EMPTY', 'لطفا قبل از آپلود یک فایل انتخاب نمایید');
DEFINE('_FILE_ERROR_SIZE', 'حجم فایل از حداکثر حجم درنظر گرفته شده توسط مدیر بیشتر می باشد.');
DEFINE('_COM_A_FILE', 'فایل ها');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'فایل های مجاز');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'پسوند فایل هایی که مجاز به آپلود می باشد. برای جدا کردن از کاما استفاده نمایید ، پسوند ها با حروف کوچک باید باشد.<br />مثال: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'حداکثر حجم فایل<br/><em>به کیلوبایت</em>');
DEFINE('_COM_A_FILEUPLOAD', 'اجازه آپلود فایل به مهمان');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'با انتخاب &quot;بله&quot; تمامی کاربران می توانند فایل آپلود کنند.');
DEFINE('_COM_A_FILEREGUPLOAD', 'اجازه آپلود فایل برای کاربران ثبت شده');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند فایل آپلود کنند.<br/> توجه : مدیر ارشد و مدیران امکان آپلود فایل را همیشه دارند.');
DEFINE('_SUBMIT_CANCEL', 'ثبت ارسال شما لغو گردید');
DEFINE('_HELP_SUBMIT', 'با کلیک بر روی این کلید موضوع شما ارسال می شود');
DEFINE('_HELP_PREVIEW', 'با کلیک بر روی این کلید می توانید پیش نمایشی از موضوع بعد از ارسال را مشاهده نمایید');
DEFINE('_HELP_CANCEL', 'با کلید بر روی این کلید ارسال شما لغو خواهد شد');
DEFINE('_POST_DELETE_ATT', 'اگر این گزینه را انتخاب نمایید کلیه تصاویر و فایل های پیوست این موضوع حذف می شوند (توصیه می شود).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'نمایش نشانه ویرایش شده');
DEFINE('_COM_A_USER_MARKUP_DESC', 'با انتخاب &quot;بله&quot; بعد از ویرایش ارسال نام ویرایش کننده و تاریخ ویرایش در زیر ارسال درج می شود.');
DEFINE('_EDIT_BY', 'ویرایش توسط:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'این ایراد در زمان آپلود آواتور رخ داد. لطفا مجددا سعی نمایید یا مدیر سیستم را مطلع سازید');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'مدیریت تصاویر آپلود شده');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'مدیریت فایل های آپلود شده');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'تعداد تصاویر آپلود شده');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'تعداد فایل های آپلود شده');
DEFINE('_COM_A_IMGB_ENLARGE', 'برای دیدن تصاویر به صورت بزرگ بر روی آنها کلیک نمایید');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'برای دریافت فایل تصویر بر روی آن کلیک نمایید');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'گزینه &quot;تصویر ساختگی فعلی&quot; برای جایگزین نمودن تصویر انتخاب شده با تصویر ساختگی فعلی می باشد.<br /> این گزینه امکان حذف فایل را بدون آسیب رساندن به ارسال را می دهد.<br /><small><em>توجه کنید که بعضی از مرورگرها نیاز به تازه سازی جهت نمایش تصویر ساختگی فعلی دارند.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'تصویر ساختگی فعلی');
DEFINE('_COM_A_IMGB_REPLACE', 'جایگزین با تصویر ساختگی');
DEFINE('_COM_A_IMGB_REMOVE', 'حذف');
DEFINE('_COM_A_IMGB_NAME', 'نام');
DEFINE('_COM_A_IMGB_SIZE', 'حجم');
DEFINE('_COM_A_IMGB_DIMS', 'ابعاد');
DEFINE('_COM_A_IMGB_CONFIRM', 'آیا شما از حذف این فایل مطمئن هستید؟ ');
DEFINE('_COM_A_IMGB_VIEW', 'ارسال باز (برای ویرایش)');
DEFINE('_COM_A_IMGB_NO_POST', 'در هیچ ارسالی استفاده نشده!');
DEFINE('_USER_CHANGE_VIEW', 'تغیرات انجام شده در بازدید بعدی از انجمن اعمال می شود.<br /> اگر شما می خواهید تغییرات را به سرعت ببینید از دکمه انجمن در منو بالا استفاده نمایید.');
DEFINE('_MOSBOT_DISCUSS_A', 'بحث در مورد این مقاله در انجمن. (');
DEFINE('_MOSBOT_DISCUSS_B', ' posts)');
DEFINE('_POST_DISCUSS', 'این موضوع در مورد بحث در مورد محتوای مقاله می باشد');
DEFINE('_COM_A_RSS', 'فعال سازی خروجی RSS');
DEFINE('_COM_A_RSS_DESC', 'با فعال سازی خروجی RSS ، آخرین ارسال ها با استفاده از نرم افزارهای RSS خوان قابل استفاده می باشد   (برای مثال <a href="http://www.rssreader.com" target="_blank">rssreader.com</a>).');
DEFINE('_LISTCAT_RSS', 'آخرین ارسال ها را روی کامپیوتر خود ببینید');
DEFINE('_SEARCH_REDIRECT', 'انجمن  قبل از انجام خواسته شما نیاز به پایداری مجدد دارد.\nنگران نباشید, این اتفاق پس از ورود و فعالیت نکردن به مدت 30 دقیقه عادی است.\nلطفا مجددا جستجو نمایید.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'پیکربندی انجمن');
DEFINE('_COM_A_VERSION', 'نسخه مورد استفاده شما ');
DEFINE('_COM_A_DISPLAY', 'نمایش #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'تنظیم جاری');
DEFINE('_COM_A_EXPLANATION', 'توضیح');
DEFINE('_COM_A_BOARD_TITLE', 'عنوان انجمن');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'نام انجمن شما');
DEFINE('_COM_A_VIEW_TYPE', 'نحوه نمایش پیش فرض');
DEFINE('_COM_A_VIEW_TYPE_DESC', 'انتخاب نمایش به صورت درختی یا ساده');
DEFINE('_COM_A_THREADS', 'موضوعات در صفحه');
DEFINE('_COM_A_THREADS_DESC', 'نمایش تعداد موضوع ها در هر صفحه');
DEFINE('_COM_A_REGISTERED_ONLY', 'فقط برای کاربران عضو شده');
DEFINE('_COM_A_REG_ONLY_DESC', 'با انتخاب &quot;بله&quot; فقط کاربران ثبت شده مجاز به دیدن و ارسال موضوع می باشند، با انتخاب &quot;خیر&quot; همه بازدیدکنندگان امکان بازدید از انجمن را دارا می باشند');
DEFINE('_COM_A_PUBWRITE', 'خواند و نوشتن عمومی');
DEFINE('_COM_A_PUBWRITE_DESC', 'با انتخاب &quot;بله&quot; اماکن نوشتن عمومی به تمامی بازدیدکنندگان داده می شود، با انتخاب &quot;خیر&quot; هیچ بازدیدکننده ای امکان دیدن ارسال ها را ندارد و فقط کاربران عضو امکان دیدن و نوشتن را دارا می باشند');
DEFINE('_COM_A_USER_EDIT', 'امکان ویرایش');
DEFINE('_COM_A_USER_EDIT_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند موضوع های خود را ویرایش کنند.');
DEFINE('_COM_A_MESSAGE', 'برای ذخیره چینش جدید بر روی آیکون &quot;ذخیره&quot; کلیک نمایید.');
DEFINE('_COM_A_HISTORY', 'نمایش ارسال های پاسخ');
DEFINE('_COM_A_HISTORY_DESC', 'با انتخاب &quot;بله&quot; ارسال های موضوع  در پاسخ و نقل قول نمایش داده می شود');
DEFINE('_COM_A_SUBSCRIPTIONS', 'امکان باخبرسازی');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'با انتخاب &quot;بله&quot; کاربران می توانند با انتخاب گزینه باخبرسازی از ارسال های جدید به آن موضوع توسط ایمیل باخبر شوند');
DEFINE('_COM_A_HISTLIM', 'محدودیت ارسال ها');
DEFINE('_COM_A_HISTLIM_DESC', 'تعداد ارسال های پاسخ برای نمایش');
DEFINE('_COM_A_FLOOD', 'زملن محافظ');
DEFINE('_COM_A_FLOOD_DESC', 'مدت زمانی به ثانیه که باید بین 2 ارسال وجود داشته باشد. با وارد کردن عدد صفر محافظ زمان غیرفعال می شود. توجه: محافظ زمانی ممکن است باعث کاهش کارایی انجمن بشود.');
DEFINE('_COM_A_MODERATION', 'ایمیل مدیران');
DEFINE('_COM_A_MODERATION_DESC',
    'با انتخاب &quot;بله&quot; یک ایمیل باخبرسازی برای مدیر انجمن از ارسال های جدید فرستاده می شود.');
DEFINE('_COM_A_SHOWMAIL', 'نمایش ایمیل');
DEFINE('_COM_A_SHOWMAIL_DESC', 'با انتخاب &quot;خیر&quot; ایمیل کاربران قابل دیدن نمی باشد.');
DEFINE('_COM_A_AVATAR', 'اجازه انتخاب آواتور');
DEFINE('_COM_A_AVATAR_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند برای خود آواتور انتخاب نمایند');
DEFINE('_COM_A_AVHEIGHT', 'حداکثر ارتفاع آواتور');
DEFINE('_COM_A_AVWIDTH', 'حداکثر عرض آواتور');
DEFINE('_COM_A_AVSIZE', 'حداکثر حجم فایل<br/><em>به کیلوبایت</em>');
DEFINE('_COM_A_USERSTATS', 'دیدن آمار کاربر');
DEFINE('_COM_A_USERSTATS_DESC', 'با انتخاب &quot;بله&quot; می توانید آمار مربوط به کاربر را از قبیل تعداد ارسال ها را برای عموم نمایش دهید.');
DEFINE('_COM_A_AVATARUPLOAD', 'اجازه آپلود آواتور');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند آواتور آپلود کنند.');
DEFINE('_COM_A_AVATARGALLERY', 'اجازه استفاده از گالری آواتورها');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'با انتخاب &quot;بله&quot; امکان انتخاب آواتور از داخل گالری تصاویر امکان پذیر می باشد (components/com_fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'با انتخاب &quot;بله&quot; رتبه کاربران بر اساس تعداد ارسال هایشان مشخص می شود.<br/><strong>برای این کار باید گزینه آمار کاربر را نیز فعال نمایید.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'استفاده از تصویر رتبه');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'با انتخاب &quot;بله&quot; رتبه کاربران ثبت شده با استفاده از تصویر نمایش داده می شود (از components/com_fireboard/ranks). برای دریافت اطلاعات بیشتر در مورد سیستم رتبه دهی و امکانات بیشتر به آدرس www.mambolearn.com مراجعه نمایید');
DEFINE('_COM_A_RANK1', 'رتبه 1');
DEFINE('_COM_A_RANK1TXT', 'متن رتبه 1');
DEFINE('_COM_A_RANK2', 'رتبه 2');
DEFINE('_COM_A_RANK2TXT', 'متن رتبه 2');
DEFINE('_COM_A_RANK3', 'رتبه 3');
DEFINE('_COM_A_RANK3TXT', 'متن رتبه 3');
DEFINE('_COM_A_RANK4', 'رتبه 4');
DEFINE('_COM_A_RANK4TXT', 'متن رتبه 4');
DEFINE('_COM_A_RANK5', 'رتبه 5');
DEFINE('_COM_A_RANK5TXT', 'متن رتبه 5');
DEFINE('_COM_A_RANK6', 'رتبه 6');
DEFINE('_COM_A_RANK6TXT', 'متن رتبه 6');
DEFINE('_COM_A_RANK', 'رتبه');
DEFINE('_COM_A_RANK_NAME', 'نام رتبه');
DEFINE('_COM_A_RANK_LIMIT', 'تعداد ارسال ها');
//email and stuff
$_COM_A_NOTIFICATION = "یک باخبرسازی از طرف ";
$_COM_A_NOTIFICATION1 = "یک ارسال جدید به موضوعی که شما برای باخبرسازی انتخاب نموده اید صورت گرفته است ";
$_COM_A_NOTIFICATION2 = "شما می توانید در  'مشخصات' lخود پس از ورود به سایت باخبرسازی مربوطه را غیرفعال نمایید.";
$_COM_A_NOTIFICATION3 = "این یک ایمیل باخبرسازی می باشد ف لطفا پاسخ ندهید.";
$_COM_A_NOT_MOD1 = "یک ارسال جدید به انجمنی که شما به مدیریت آن منصوب شدید صورت گرفته است ";
$_COM_A_NOT_MOD2 = "لطفا وارد سایت شوید.";
DEFINE('_COM_A_NO', 'خیر');
DEFINE('_COM_A_YES', 'بله');
DEFINE('_COM_A_FLAT', 'ساده');
DEFINE('_COM_A_THREADED', 'درختی');
DEFINE('_COM_A_MESSAGES', 'پیغام ها در صفحه');
DEFINE('_COM_A_MESSAGES_DESC', 'نمایش تعداد پیغام ها در هر صفحه ');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'نام کاربری');
DEFINE('_COM_A_USERNAME_DESC', 'با انتخاب  &quot;بله&quot; با همان نامی که وارد می شوید به عنوان نام اصلی شما در نظر گرفته می شود');
DEFINE('_COM_A_CHANGENAME', 'اجازه تغییر نام');
DEFINE('_COM_A_CHANGENAME_DESC', 'با انتخاب &quot;بله&quot; کاربران ثبت شده می توانند نام خود را در هنگام ارسال تغییر دهند. اگر &quot;خیر&quot; را انتخاب نمایید کاربران ثبت شده نمی توانند نام خود را ویرایش کنند');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'غیرفعال شدن انجمن');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'با انتخاب &quot;بله&quot; انجمن شما به حالت غیرفعال در می آید. انجمن فقط برای مدیر ارشد قابل رویت می باشد.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'پیام غیرفعال شدن انجمن');
DEFINE('_COM_A_PRUNE', 'پاک سازی انجمن ها');
DEFINE('_COM_A_PRUNE_NAME', 'انتخاب انجمن برای پاک سازی:');
DEFINE('_COM_A_PRUNE_DESC',
    'پاک سازی انجمن این امکان را به شما می دهد تا موضوعاتی را که در روزهای مشخص شده هیچ ارسالی نداشته اند را حذف نمایید. این امکان برای حذف موضوع های مهم و حذف شده کاربرد ندارد و پاک سازی این موضوع ها باید به صورت دستی اقدام نمایید.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'پاک سازی موضوع هایی که در چند روز مشخص شده هیچ ارسالی نداشته اند ');
DEFINE('_COM_A_PRUNE_DAYS', 'روز');
DEFINE('_COM_A_PRUNE_USERS', 'پاک سازی کاربران');
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'پاک سازی کاربران این امکان را می دهد تا مشخصات کاربران انجمن را از جوملا خود حذف نمایید.<br/>بعد از اطمینان از پاک سازی بر روی  &quot;شروع پاک سازی&quot; از نوار ابزار اقدام نمایید.');
//general
DEFINE('_GEN_ACTION', 'عمل');
DEFINE('_GEN_AUTHOR', 'ایجاد کننده');
DEFINE('_GEN_BY', 'توسط');
DEFINE('_GEN_CANCEL', 'لغو');
DEFINE('_GEN_CONTINUE', 'ارسال');
DEFINE('_GEN_DATE', 'تاریخ');
DEFINE('_GEN_DELETE', 'حذف');
DEFINE('_GEN_EDIT', 'ویرایش');
DEFINE('_GEN_EMAIL', 'ایمیل');
DEFINE('_GEN_EMOTICONS', 'شکلک ها');
DEFINE('_GEN_FLAT', 'ساده');
DEFINE('_GEN_FLAT_VIEW', 'نمایش ساده');
DEFINE('_GEN_FORUMLIST', 'لیست انجمن');
DEFINE('_GEN_FORUM', 'انجمن');
DEFINE('_GEN_HELP', 'راهنما');
DEFINE('_GEN_HITS', 'بازدیدها');
DEFINE('_GEN_LAST_POST', 'آخرین ارسال');
DEFINE('_GEN_LATEST_POSTS', 'نمایش آخرین ارسال ها');
DEFINE('_GEN_LOCK', 'قفل');
DEFINE('_GEN_UNLOCK', 'باز');
DEFINE('_GEN_LOCKED_FORUM', 'انجمن قفل شده است');
DEFINE('_GEN_LOCKED_TOPIC', 'موضوع قفل شده است');
DEFINE('_GEN_MESSAGE', 'متن پیام ');
DEFINE('_GEN_MODERATED', 'انجمن مدیریت شده ، ارسال ها قبل از انتشار مرور می شوند.');
DEFINE('_GEN_MODERATORS', 'مدیران');
DEFINE('_GEN_MOVE', 'انتقال');
DEFINE('_GEN_NAME', 'نام');
DEFINE('_GEN_POST_NEW_TOPIC', 'ارسال موضوع جدید');
DEFINE('_GEN_POST_REPLY', 'پاسخ موضوع');
DEFINE('_GEN_MYPROFILE', 'مشخصات من');
DEFINE('_GEN_QUOTE', 'نقل قول');
DEFINE('_GEN_REPLY', 'پاسخ');
DEFINE('_GEN_REPLIES', 'پاسخ ها');
DEFINE('_GEN_THREADED', 'درختی');
DEFINE('_GEN_THREADED_VIEW', 'نمایش درختی');
DEFINE('_GEN_SIGNATURE', 'امضا');
DEFINE('_GEN_ISSTICKY', 'موضوع مهم.');
DEFINE('_GEN_STICKY', 'مهم');
DEFINE('_GEN_UNSTICKY', 'عادی');
DEFINE('_GEN_SUBJECT', 'موضوع');
DEFINE('_GEN_SUBMIT', 'تایید');
DEFINE('_GEN_TOPIC', 'موضوع');
DEFINE('_GEN_TOPICS', 'موضوع ها');
DEFINE('_GEN_TOPIC_ICON', 'آیکون موضوع');
DEFINE('_GEN_SEARCH_BOX', 'جستجو انجمن');
$_GEN_THREADED_VIEW = "نمایش درختی";
$_GEN_FLAT_VIEW = "نمایش ساده";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'آپلود');
DEFINE('_UPLOAD_DIMENSIONS', 'فایل تصویر شما باید حداکثر(عرض * ارتقاع - اندازه)');
DEFINE('_UPLOAD_SUBMIT', 'انتخاب یک آواتور جدید برای آپلود');
DEFINE('_UPLOAD_SELECT_FILE', 'انتخاب فایل');
DEFINE('_UPLOAD_ERROR_TYPE', 'لطفا فقط از تصاویر jpeg, gif یا png استفاده نمایید');
DEFINE('_UPLOAD_ERROR_EMPTY', 'لطفا قبل از آپلود یک فایل انتخاب نمایید');
DEFINE('_UPLOAD_ERROR_NAME', 'فایل تصوصیر شما باید شامل حروف الفبا انگلیسی باشد و در آن فاصله ای وجود نداشته باشد.');
DEFINE('_UPLOAD_ERROR_SIZE', 'حجم فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'عرض فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'ارتفاع فایل تصویر شما از حداکثر اندازه ای که توسط مدیر تعیین شده است تجاوز کرده است..');
DEFINE('_UPLOAD_ERROR_CHOOSE', "شما هیچ آواتوری را از لیست انتخاب نکرده اید");
DEFINE('_UPLOAD_UPLOADED', 'آواتور شما آپلود گردید.');
DEFINE('_UPLOAD_GALLERY', 'یک آواتور از گالری آواتورها انتخاب نمایید:');
DEFINE('_UPLOAD_CHOOSE', 'تایید انتخاب');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'مدیر سایت باید تعدادی انجمن ایجاد نماید  ');
DEFINE('_LISTCAT_DO', '');
DEFINE('_LISTCAT_INFORM', '');
DEFINE('_LISTCAT_NO_CATS', 'هیچ مجموعه ای در انجمن وجود ندارد.');
DEFINE('_LISTCAT_PANEL', 'پنل مدیریت در سیستم مدیریت محتوای جوملا.');
DEFINE('_LISTCAT_PENDING', 'پیغام های معلق');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'هیچ پیغام معلقی در این انجمن وجود ندارد.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'شما می خواهید این موضوع را حذف نمایید');
DEFINE('_POST_ABOUT_DELETE', '<strong>توجه:</strong><br/>
-اگر این موضوع را حذف نمایید (اول ارسال موضوع) تمام ارسال های این موضوع نیز حذف می شود!
');
DEFINE('_POST_CLICK', 'اینجا کلیک کنید');
DEFINE('_POST_ERROR', 'نام کاربری/ایمیل پیدا نشد. هیچ ایرادی از پایگاه داده لیست نشده');
DEFINE('_POST_ERROR_MESSAGE', 'یک ایراد نامشخص SQL رخ داد و پیغام ارسال نشد.  اگر ایراد مجددا رخ داد با مدیر سیستم تماس بگیرید.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'یک ایراد رخ داده است.  لطفا مجددا سعی نمایید.  اگر این ایراد دوباره رخ داد با مدیر سیستم تماس بگیرید.');
DEFINE('_POST_ERROR_TOPIC', 'یک ایراد هنگام حذف کردن رخ داد. لطفا ایراد زیر را کنترل نمایید:');
DEFINE('_POST_FORGOT_NAME', 'شما فراموش کرده اید که نام خود را وارد نمایید.  از دکمه بازگشت مرورگر برای بازگشت به عقب و سعی مجدد استفاده نمایید.');
DEFINE('_POST_FORGOT_SUBJECT', 'شما فراموش کرده اید که موضوع را وارد نمایید.    از دکمه بازگشت مرورگر برای بازگشت به عقب و سعی مجدد استفاده نمایید.');
DEFINE('_POST_FORGOT_MESSAGE', 'شما فراموش کرده اید که متن را وارد نمایید.    از دکمه بازگشت مرورگر برای بازگشت به عقب و سعی مجدد استفاده نمایید.');
DEFINE('_POST_INVALID', 'ارسال درخواست شده شناسایی نشد.');
DEFINE('_POST_LOCK_SET', 'موضوع قفل شد.');
DEFINE('_POST_LOCK_NOT_SET', 'موضوع نمی تواند قفل شود.');
DEFINE('_POST_LOCK_UNSET', 'موضوع باز شد.');
DEFINE('_POST_LOCK_NOT_UNSET', 'امکان باز شدن موضوع وجود ندارد.');
DEFINE('_POST_MESSAGE', 'ارسال پیغام جدید در ');
DEFINE('_POST_MOVE_TOPIC', 'انتقال این موضوع به انجمن ');
DEFINE('_POST_NEW', 'ارسال پیغام جدید در: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'باخبرسازی برای این موضوع قابل پردازش نمی باشد.');
DEFINE('_POST_NOTIFIED', 'با انتخاب این گزینه در صورت پاسخ به این موضوع شما با خبر خواهید شد.');
DEFINE('_POST_STICKY_SET', 'این موضوع به عنوان موضوع مهم مشخص گردید.');
DEFINE('_POST_STICKY_NOT_SET', 'مهم کردن این موضوع ممکن نمی باشد.');
DEFINE('_POST_STICKY_UNSET', 'این موضوع از مهم به موضوع عادی تبدیل شد.');
DEFINE('_POST_STICKY_NOT_UNSET', 'امکام عادی کردن این موضوع وجود ندارد.');
DEFINE('_POST_SUBSCRIBE', 'باخبر سازی');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'باخبرسازی شما برای این موضوع با موفقیت انجام شد.');
DEFINE('_POST_SUCCESS', 'پیغام شما با موفقیت ثبت شد');
DEFINE('_POST_SUCCES_REVIEW', 'پیام شما با موفقیت ارسال گردید.  پیام شما توسط یک مدیر مرور و پس از تایید در انجمن منتشر خواهد شد.');
DEFINE('_POST_SUCCESS_REQUEST', 'درخواست شما پردازش شد.  تا چندلحظه دیگر به موضوع باز می گردید,');
DEFINE('_POST_TOPIC_HISTORY', 'ارسال های این موضوع');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'نمایش آخرین');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'ارسال های این موضوع  -  <i>(آخرین ارسال در ابتدا)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'موضوع شما منتقل نشد. برای بازگشت به موضوع:');
DEFINE('_POST_TOPIC_FLOOD1', 'مدیر این انجمن محافظ زمانی را فعال کرده است ، لطفا چند لحظه صبر کنید ');
DEFINE('_POST_TOPIC_FLOOD2', ' ثانیه قبل شما یک ارسال ایجاد کرده اید.');
DEFINE('_POST_TOPIC_FLOOD3', 'لطفا دکمه بازگشت مرورگر را فشار دهید.');
DEFINE('_POST_EMAIL_NEVER', 'آدرس ایمیل شما هیچ وقت در انجمن نمایش داده نخواهد شد.');
DEFINE('_POST_EMAIL_REGISTERED', 'آدرس ایمیل شما فقط برای کاربران ثبت شده نمایش داده می شود.');
DEFINE('_POST_LOCKED', 'توسط مدیر قفل شده است.');
DEFINE('_POST_NO_NEW', 'اجازه ارسال پاسخ جدید وجود ندارد.');
DEFINE('_POST_NO_PUBACCESS1', 'مدیر انجمن امکان نوشتن عمومی را غیرفعال نموده است.');
DEFINE('_POST_NO_PUBACCESS2', 'فقط کاربران ثبت شده<br /> اجازه استفاده از انجمن را دارند.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> هیچ موضوعی در انجمن وجود ندارد <<');
DEFINE('_SHOWCAT_PENDING', 'پیغام های معلق');
// userprofile.php
DEFINE('_USER_DELETE', ' برای حذف امضا این گزینه را انتخاب نمایید');
DEFINE('_USER_ERROR_A', 'یک ایراد رخ داده است.لطفا مدیر را از این خطا مطلع نمایید ');
DEFINE('_USER_ERROR_B', '');
DEFINE('_USER_ERROR_C', 'با تشکر!');
DEFINE('_USER_ERROR_D', 'شماره ایراد جهت گزارش دادن: ');
DEFINE('_USER_GENERAL', 'گزینه های اصلی مشخصات');
DEFINE('_USER_MODERATOR', 'انتصاب شما به مدیریت انجمن ها');
DEFINE('_USER_MODERATOR_NONE', 'مدیریت هیچ انجمنی به شما داده نشده است');
DEFINE('_USER_MODERATOR_ADMIN', 'شما مدیر کل انجمن ها هستید.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'هیچ موضوعی برای باخبرسازی شما انتخاب نشده است');
DEFINE('_USER_PREFERED', 'نحوه نمایش');
DEFINE('_USER_PROFILE', 'مشخصات برای ');
DEFINE('_USER_PROFILE_NOT_A', 'مشخصات شما ');
DEFINE('_USER_PROFILE_NOT_B', 'نتوانست');
DEFINE('_USER_PROFILE_NOT_C', ' بروز بشود.');
DEFINE('_USER_PROFILE_UPDATED', 'مشخصات شما به روز شد.');
DEFINE('_USER_RETURN_A', 'اگر برای بازگشت به مشخصات خود صبر نمی کنید ');
DEFINE('_USER_RETURN_B', 'اینجا را کلیک نمایید');
DEFINE('_USER_SUBSCRIPTIONS', 'باخبرسازی های شما');
DEFINE('_USER_UNSUBSCRIBE', 'لغو باخبرسازی');
DEFINE('_USER_UNSUBSCRIBE_A', 'شما ');
DEFINE('_USER_UNSUBSCRIBE_B', 'نمی توانید');
DEFINE('_USER_UNSUBSCRIBE_C', ' باخبرسازی موضوع را لغو نمایید.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'باخبرسازی برای این موضوع غیرفعال شد.');
DEFINE('_USER_DELETEAV', ' برای حذف آواتور این گزینه را انتخاب نمایید');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'نحوه چینش پیغام ها');
DEFINE('_USER_ORDER_DESC', 'آخرین ارسال در ابتدا');
DEFINE('_USER_ORDER_ASC', 'اولین ارسال در ابتدا');
// view.php
DEFINE('_VIEW_DISABLED', 'مدیر دسترسی عمومی را غیرفعال کرده است.');
DEFINE('_VIEW_POSTED', 'ارسال شده توسط');
DEFINE('_VIEW_SUBSCRIBE', ':: باخبرسازی این موضوع ::');
DEFINE('_MODERATION_INVALID_ID', 'یک درخواست اشتباه رخ داد.');
DEFINE('_VIEW_NO_POSTS', 'هیچ ارسالی در این انجمن وجود ندارد.');
DEFINE('_VIEW_VISITOR', 'بازدیدکننده');
DEFINE('_VIEW_ADMIN', 'مدیر');
DEFINE('_VIEW_USER', 'کاربر');
DEFINE('_VIEW_MODERATOR', 'مدیر');
DEFINE('_VIEW_REPLY', 'پاسخ به این پیغام');
DEFINE('_VIEW_EDIT', 'ویرایش این پیغام');
DEFINE('_VIEW_QUOTE', 'نقل قول کردن این موضوع در پیغام جدید');
DEFINE('_VIEW_DELETE', 'حذف این پیغام');
DEFINE('_VIEW_STICKY', 'مشخص کردن این موضوع به عنوان موضوع مهم');
DEFINE('_VIEW_UNSTICKY', 'مشخص کردن این موضوع به عنوان یک موضوع عادی');
DEFINE('_VIEW_LOCK', 'قفل کردن این موضوع');
DEFINE('_VIEW_UNLOCK', 'باز کردن این موضوع');
DEFINE('_VIEW_MOVE', 'انتقال این موضوع به یک انجمن دیگر');
DEFINE('_VIEW_SUBSCRIBETXT', 'باخبرسازی برای این موضوع فعال شد ، در صورت ارسال پاسخ جدید توسط ایمیل به شما اطلاع داده می شود');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'انجمن');
DEFINE('_POSTS', 'ارسال ها:');
DEFINE('_TOPIC_NOT_ALLOWED', 'ارسال ها');
DEFINE('_FORUM_NOT_ALLOWED', 'انجمن');
DEFINE('_FORUM_IS_OFFLINE', 'انجمن غیرفعال است!');
DEFINE('_PAGE', 'صفحه: ');
DEFINE('_NO_POSTS', 'هیچ ارسالی وجود ندارد');
DEFINE('_CHARS', 'حداکثر کاراکتر.');
DEFINE('_HTML_YES', 'HTML غیرفعال است');
DEFINE('_YOUR_AVATAR', '<b>آواتور شما</b>');
DEFINE('_NON_SELECTED', 'انتخابی صورت نگرفته است <br>');
DEFINE('_SET_NEW_AVATAR', 'انتخاب آواتور جدید');
DEFINE('_THREAD_UNSUBSCRIBE', 'لغو باخبرسازی');
DEFINE('_SHOW_LAST_POSTS', 'موضوع های فعال در');
DEFINE('_SHOW_HOURS', 'ساعت گذشته');
DEFINE('_SHOW_POSTS', 'مجموع: ');
DEFINE('_DESCRIPTION_POSTS', 'آخرین ارسال های موضوعات فعال نمایش داده می شود');
DEFINE('_SHOW_4_HOURS', '4 ساعت');
DEFINE('_SHOW_8_HOURS', '8 ساعت');
DEFINE('_SHOW_12_HOURS', '12 ساعت');
DEFINE('_SHOW_24_HOURS', '24 ساعت');
DEFINE('_SHOW_48_HOURS', '48 ساعت');
DEFINE('_SHOW_WEEK', 'هفته');
DEFINE('_POSTED_AT', 'ارسال شده در');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'هیچ ارسال جدیدی در بازه زمانی انتخاب شده توسط شما وجود ندارد.');
DEFINE('_MESSAGE', 'متن پیام');
DEFINE('_NO_SMILIE', 'no');
DEFINE('_FORUM_UNAUTHORIZIED', 'این انجمن فقط برای کاربران ثبت شده قابل استفاده می باشد.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'اگر شما عضو شده اید ، ابتدا وارد سایت شوید.');
DEFINE('_MESSAGE_ADMINISTRATION', 'مدیریت');
DEFINE('_MOD_APPROVE', 'تایید');
DEFINE('_MOD_DELETE', 'حذف');
//NEW in RC1
DEFINE('_SHOW_LAST', 'نمایش پیغام های اخیر');
DEFINE('_POST_WROTE', 'wrote');
DEFINE('_COM_A_EMAIL', 'آدرس ایمیل انجمن');
DEFINE('_COM_A_EMAIL_DESC', 'این آدرس ایمیل انجمن می باشد ، لطفا آن را صحیح وارد نمایید');
DEFINE('_COM_A_WRAP', 'حداکثر طول کلمه');
DEFINE('_COM_A_WRAP_DESC',
    'حداکثر طول یک کلمه را وارد نمایید');
DEFINE('_COM_A_SIGNATURE', 'حداکثر طول امضا');
DEFINE('_COM_A_SIGNATURE_DESC', 'حداکثر تعداد کاراکترهای مجاز برای استفاده در امضا.');
DEFINE('_SHOWCAT_NOPENDING', 'پیغام های معلق');
DEFINE('_COM_A_BOARD_OFSET', 'اختلاف زمانی انجمن');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'اختلاف زمانی انجمن با سرور را وارد نمایید. می توانید از اعداد مثبت و منفی استفاده نمایید');
//New in RC2
DEFINE('_COM_A_BASICS', 'اصلی');
DEFINE('_COM_A_FRONTEND', 'بخش کاربری');
DEFINE('_COM_A_SECURITY', 'امنیتی');
DEFINE('_COM_A_AVATARS', 'آواتورها');
DEFINE('_COM_A_INTEGRATION', 'مجتمع سازی');
DEFINE('_COM_A_PMS', 'فعال کردن پیغام خصوصی');
DEFINE('_COM_A_PMS_DESC',
    'شما می توانید یکی از سیستم های پیغام خصوصی نصب شده را انتخاب نمایید. با انتخاب Clexus و فعال سازی مشخصات کاربران ClexusPM می توانید از این گزینه ها استفاده نمایید (مانند ICQ, AIM, Yahoo, MSN) و لینک مشخصات در صورتی که قالب انجمن  از آن پشیبانی نماید.');
DEFINE('_VIEW_PMS', 'برای ارسال پیغام خصوصی به این کاربر اینجا کلیک نمایید');
//new in RC3
DEFINE('_POST_RE', 'پاسخ:');
DEFINE('_BBCODE_BOLD', 'متن ضخیم: [b]متن[/b] ');
DEFINE('_BBCODE_ITALIC', 'متن مورب: [i]متن[/i]');
DEFINE('_BBCODE_UNDERL', 'خط زیر متن: [u]متن[/u]');
DEFINE('_BBCODE_QUOTE', 'نقل قول متن: [quote]متن[/quote]');
DEFINE('_BBCODE_CODE', 'نمایش کد: [code]code[/code]');
DEFINE('_BBCODE_ULIST', 'لیست نامرتب: [ul] [li]متن[/li] [/ul] - نکته: لیست باید شامل ایتم های لیست باشد');
DEFINE('_BBCODE_OLIST', 'لیست مرتب: [ol] [li]متن[/li] [/ol] - نکته: لیست باید شامل ایتم های لیست باشد');
DEFINE('_BBCODE_IMAGE', 'تصویر: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'لینک: [url=http://www.zzz.com/]این یک لینک است[/url]');
DEFINE('_BBCODE_CLOSA', 'بستن تمام تگ ها');
DEFINE('_BBCODE_CLOSE', 'بستن تمام تگهای باز bbCode');
DEFINE('_BBCODE_COLOR', 'زنگ: [color=#FF6600]متن[/color]');
DEFINE('_BBCODE_SIZE', 'اندازه: [size=1]اندازه متن[/size] - نکته: اندازه از 1 تا 5');
DEFINE('_BBCODE_LITEM', 'آیتم لسیت: [li] آیتم لسیت [/li]');
DEFINE('_BBCODE_HINT', 'راهنمای bbCode - نکته: bbCode برای متن انتخاب شده عمل می کند!');
DEFINE('_COM_A_TAWIDTH', 'عرض جعبه متن');
DEFINE('_COM_A_TAWIDTH_DESC', 'اندازه عرض جعبه متن برای پاسخ و ایجاد موضوع مطابق با قالب. <br/>دو خط نوار شکلک ها و ابزارها برای عرض <= 420 پیکسل');
DEFINE('_COM_A_TAHEIGHT', 'ارتفاع جعبه متن');
DEFINE('_COM_A_TAHEIGHT_DESC', 'اندازه ارتفاع جعبه متن برای پاسخ و ایجاد موضوع مطابق با قالب');
DEFINE('_COM_A_ASK_EMAIL', 'ایمیل الزامی');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'الزامی بودن ایمیل برای بازدیدکنندگان برای پاسخ دادن و ایجاد موضوع جدید. با انتخاب &quot;خیر&quot; از این امکان صرف نظر می شود. و از ارسال کنندگان آدرس ایمیل خواسته نمی شود.');

DEFINE('_GEN_GOTOBOTTOM', 'به سمت پایین');
DEFINE('_GEN_GOTOTOP', 'به سمت بالا');
DEFINE('_ANN_ANNOUNCEMENTS', 'اخبارها');
DEFINE('_TOOL_MODERATOR', 'مدیر');
DEFINE('_TOOL_BACK', 'بازگشت');
DEFINE('_TOOL_EDIT', 'ویرایش');
DEFINE('_TOOL_PRUNE', 'Prune');
DEFINE('_KUNENA_EDIT_TITLE', 'ویرایش مشخصات');
DEFINE('_KUNENA_YOUR_NAME', 'نام:');
DEFINE('_KUNENA_EMAIL', 'ایمیل:');
DEFINE('_KUNENA_UNAME', 'نام کاربری:');
DEFINE('_KUNENA_PASS', 'رمز عبور:');
DEFINE('_KUNENA_VPASS', 'تکرار رمز عبور:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'مشخصات ذخیره شد.');
DEFINE('_KUNENA_TEAM_CREDITS', 'اعتبار');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'مدیریت امتیازات');
define('_KUNENA_SORTRANKS', 'مرتب سازی بر اساس امتیاز');

define('_KUNENA_RANKSIMAGE', 'تصویر امتیاز');
define('_KUNENA_RANKS', 'عنوان امتیاز');
define('_KUNENA_RANKS_SPECIAL', 'مخصوص');
define('_KUNENA_RANKSMIN', 'حداقل تعداد پست');
define('_KUNENA_RANKS_ACTION', 'عملیات');
define('_KUNENA_NEW_RANK', 'امتیاز جدید');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'گفتگوها');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'گفتگوهای من');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_KUNENA_CATEGORY', 'مجموعه:');
DEFINE('_KUNENA_CATEGORIES', 'مجموعه ها');
DEFINE('_KUNENA_POSTED_AT', 'پست ها');
DEFINE('_KUNENA_AGO', 'گذشته');
DEFINE('_KUNENA_DISCUSSIONS', 'گفتگو');
DEFINE('_KUNENA_TOTAL_THREADS', 'Total Threads:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'ماه');
DEFINE('_SHOW_YEAR', 'سال');



?>
