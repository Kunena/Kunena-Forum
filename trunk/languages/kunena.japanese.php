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
* @author TSMF & Jan de Graaff*
* Additional Japanese Translation for Kunena 1.0.9 by Nhat@vietnavi-dot-com, * using existing translation for Fireboard 1.0.4 by Joomlaway.net* Put this language file at joomla/components/com_kunena/language*
**/// Dont allow direct linkingdefined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Installed version');
DEFINE('_KUNENA_COPYRIGHT', 'Copyright');
DEFINE('_KUNENA_LICENSE', 'License');
DEFINE('_KUNENA_PROFILE_NO_USER', '利用者は存在しません。');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', '論壇訪問利用者が存在しないので、プロフィールもありません。');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', '検索結果');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', '高度な検索');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', '単語で検索');
DEFINE('_KUNENA_SEARCH_KEYWORDS', '単語');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', '全投稿を検索');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', '表題のみ検索');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', '利用者名で検索');
DEFINE('_KUNENA_SEARCH_UNAME', '利用者名');
DEFINE('_KUNENA_SEARCH_EXACT', '完全一致名');
DEFINE('_KUNENA_SEARCH_USER_POSTED', '投稿者: ');
DEFINE('_KUNENA_SEARCH_USER_STARTED', '話題開始者: ');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'この話題における活動水準');
DEFINE('_KUNENA_SEARCH_OPTIONS', '検索補助項目');
DEFINE('_KUNENA_SEARCH_FIND_WITH', '話題検索語: ');
DEFINE('_KUNENA_SEARCH_LEAST', '最少');
DEFINE('_KUNENA_SEARCH_MOST', '最多');
DEFINE('_KUNENA_SEARCH_ANSWERS', '結果');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', '投稿の所在: ');
DEFINE('_KUNENA_SEARCH_DATE_ANY', '日付不問');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', '最終訪問: ');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', '昨日');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', '一週前');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '二週前');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', '一月前');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '三月前');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '六ヶ月前');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', '一年前');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', '以降');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', '以前');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sort Results by');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', '表題');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', '投稿数');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', '閲覧数');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Thread start date');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', '投稿日');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', '利用者名');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', '論壇');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Increasing order');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Decreasing order');
DEFINE('_KUNENA_SEARCH_START', 'Jump to Result Number');
DEFINE('_KUNENA_SEARCH_LIMIT5', '５件表示');
DEFINE('_KUNENA_SEARCH_LIMIT10', '１０件表示');
DEFINE('_KUNENA_SEARCH_LIMIT15', '１５件表示');
DEFINE('_KUNENA_SEARCH_LIMIT20', '２０件表示');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Search in Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'All Categories');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Also search in child forums');
DEFINE('_KUNENA_SEARCH_SEND', '投稿');
DEFINE('_KUNENA_SEARCH_CANCEL', '取り止め');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'この検索に該当する投稿はありませんでした。');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', '検索語は最低半角４文字以上なければいけません。');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'あなたは世話役ではありません。');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'This thread has <b>NOT</b> been added to your favorites');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Kunena user table と the Joomla user table を同期させる。');
DEFINE('_POST_FORGOT_EMAIL', 'E-mail アドレスが記入漏れです。ブラウザのバックボタンで前画面に戻り、やり直してください。');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', '全部削除してしまいましたか？なにか添付ファイルがなくなっています。');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', '論壇');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Main 論壇');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'In order to provide additional information for you guests and members, the forum header can be leveraged to display text at the very top of a particular category.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Welcome Mat');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', '新たに参加される方には、この項目に簡単な自己紹介を投稿するようにお勧めしています。興味があることや関心を紹介してみましょう。');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Welcome to the Kunena forum![/b]

ようこそいらっしゃい。是非自己紹介を兼ねて興味やどうして会員登録したのか、投稿してみましょう。
それでは、活発に参加してみてください。
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'ご意見賜ります');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'このサイトについてご意見がありましたら、遠慮なくお申し出ください。
よりよいサイトを作り上げていくためにも、参加者のご意見を歓迎します。');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'This is the optional Forum header for the Suggestion Box.
');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Welcome to Kunena!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Welcome to Kunena![/b][/size]

Thank you for choosing Kunena for your community forum needs in Joomla.

Kunena, translated from Swahili meaning "to speak", is built by a team of open source professionals with the goal of providing a top-quality, tightly unified forum solution for Joomla. Kunena even supports social networking components like Community Builder and JomSocial.


[size=4][b]Additional Kunena Resources[/b][/size]

[b]Kunena Documentation:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Support Forum[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena Downloads:[/b] [url=http://joomlacode.org/gf/project/kunena/frs/]http://www.kunena.com/downloads[/url]
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Submit your feature ideas:[/b] [url=http://kunena.uservoice.com/pages/general?referer_type=top3]http://www.kunena.com/uservoice[/url]
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Follow Kunena on Twitter:[/b] [url=https://twitter.com/kunena]http://www.kunena.com/twitter[/url]
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the Kunena code tag highlighting Javascript. If your members post PHP or other code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from becoming malformed.');
DEFINE('_COM_A_RSS_TYPE', 'RSS タイプの初期設定');
DEFINE('_COM_A_RSS_TYPE_DESC', 'RSS フィードのタイプを、「Thread による」または「投稿による」のいずれかに設定します。「Thread による」では、各話題の総投稿数とは関係なく、話題ごとにひとつの投稿のみを RSS フィードします。「Thread による」を選ぶと、RSS フォードのサイズは小さくなりますが、すべての投稿を RSS 表示するとは限らないという制約があります。');
DEFINE('_COM_A_RSS_BY_THREAD', 'Thread による');
DEFINE('_COM_A_RSS_BY_POST', '投稿による');
DEFINE('_COM_A_RSS_HISTORY', 'RSS 履歴');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'RSS フィードの記録保存期間を指定します。初期設定は一ヶ月ですが、投稿量が多いサイトでは一週間にとどめることをお勧めします。');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '一週間');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '一ヶ月');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '一年');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Kunena 表示の初期設定');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Kunena へのリンクをクリックしたとき、最初に表示される内容を設定します。初期設定は「最新の発言」ですが、default_ex 以外のテンプレートを使う時には「大分類」を選ぶほうがよいでしょう。もし「購読中」を選ぶと、未登録の方には「最新の発言」が表示されます。');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', '至近の投稿');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', '購読中');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', '大分類');
DEFINE('_KUNENA_BBCODE_HIDE', '次の項目は未登録利用者が閲覧できないよう設定されています。:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'The parent Forum must not be the same.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', '上位の論壇を、自らに付随する下位論壇として登録することはできません。');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'この Forum ID は存在しません。');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', '名前の記入漏れです。');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'E-Mail アドレスの記入漏れです。');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', '表題の記入漏れです。');
DEFINE('_KUNENA_EDIT_TITLE', '自己紹介を編集');
DEFINE('_KUNENA_YOUR_NAME', 'お名前:');
DEFINE('_KUNENA_EMAIL', 'E-mail:');
DEFINE('_KUNENA_UNAME', 'ユーザ名:');
DEFINE('_KUNENA_PASS', 'Password:');
DEFINE('_KUNENA_VPASS', 'Password を再度入力:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', '利用者情報を更新しました。');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trim long URLs');
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
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'セッション有効時間');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', '初期設定は 1800 秒です。この概念は、Joomla 本体におけるセッション有効時間と似ています。この概念は、書込権限の判定や閲覧者の表示などに利用されます。セッションが更新されないまま時間が経過すると、書込権限などが取消されます。');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Merge');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Merge this thread with');
DEFINE('_POST_MERGE_GHOST', 'Leave ghost copy of thread');
DEFINE('_POST_SUCCESS_MERGE', 'Thread successfully merged.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Merge failed.');
DEFINE('_GEN_SPLIT', 'Split');
DEFINE('_GEN_DOSPLIT', 'Go');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Thread split successfully.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Topic successfully changed.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Topic change failed.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Split failed.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Duplicate. Identical message has been ignored.');
DEFINE('_POST_SPLIT_HINT', '<br />Hint: You can promote a post to topic position if you select it in the second column and check nothing to split.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'link orphans to topic');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Link orphans to new topic post.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'link orphans to previous post');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Link orphans to previous post.');
DEFINE('_POST_MERGE', 'merge');
DEFINE('_POST_MERGE_TITLE', 'Attach this thread to targets first post.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Attach targets first post to this thread.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'This thread has been removed from your favorites.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'This thread has <b>NOT</b> been removed from your favorites');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Your request to remove from favorites has been processed.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'This thread has been removed from your subscriptions.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'This thread has <b>NOT</b> been removed from your subscriptions');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Your request to remove from subscriptions has been processed.');
DEFINE('_POST_NO_DEST_CATEGORY', 'No destination category was selected. Nothing was moved.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', '至近の投稿');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'My Discussions');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_KUNENA_CATEGORY', 'Category:');
DEFINE('_KUNENA_CATEGORIES', '大分類');
DEFINE('_KUNENA_POSTED_AT', 'Posted');
DEFINE('_KUNENA_AGO', 'ago');
DEFINE('_KUNENA_DISCUSSIONS', 'Discussions');
DEFINE('_KUNENA_TOTAL_THREADS', 'Total Threads:');
DEFINE('_SHOW_DEFAULT', '初期設定');
DEFINE('_SHOW_MONTH', '月');
DEFINE('_SHOW_YEAR', '年');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', '"%src%" を "%dst%" へコピーしています...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'CSSファイルはここに保存する必要があります...ファイル="%file%"');
DEFINE('_KUNENA_UP_ATT_10', '付属テーブルを最新の1.0.xシリーズ構造へ正常にアップグレードしました!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'メッセージテーブルを最新の1.0.xシリーズ構造へ正常にアップグレードしました!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', '投稿階層で子ボードを昇格できませんでした。何も削除しませんでした。');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', '投稿を削除することができませんでした - 他の何も削除しませんでした。');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', '投稿のテキストを削除することができませんでした。手動でデータベースをアップデートして下さい(mesid=%id%)。');
DEFINE('_KUNENA_POST_DEL_ERR_USR', '全て削除しましたが、利用者投稿統計の更新に失敗しました!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "深刻なデータベースエラー。トピックへの返答が新しい論壇と一致するようにデータベースを手動で更新してください");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena コンポーネントは正常に削除されました!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena 論壇コンポーネントのバージョン: %version%');
DEFINE('_KUNENA_PDF_DATE', '作成日: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', '検索する論壇はありません');

DEFINE('_KUNENA_ERRORADDUSERS', '利用者追加中のエラー:');
DEFINE('_KUNENA_USERSSYNCDELETED', '利用者を同期しました。; 削除済み:');
DEFINE('_KUNENA_USERSSYNCADD', ', 追加:');
DEFINE('_KUNENA_SYNCUSERPROFILES', '利用者プロファイル');
DEFINE('_KUNENA_NOPROFILESFORSYNC', '同期するプロファイルがありません。');
DEFINE('_KUNENA_SYNC_USERS', '利用者の同期');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Kunena利用者テーブルとJoomla!利用者テーブルを同期します');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Eメール管理者');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    '新規投稿を通知するEメールをシステム管理者へ送信するには「はい」を設定します。');
DEFINE('_KUNENA_RANKS_EDIT', 'ランク編集');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Eメールを隠す');
DEFINE('_KUNENA_DT_DATE_FMT','%Y/%m/%d');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%Y/%m/%d %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', '日曜日');
DEFINE('_KUNENA_DT_LDAY_MON', '月曜日');
DEFINE('_KUNENA_DT_LDAY_TUE', '火曜日');
DEFINE('_KUNENA_DT_LDAY_WED', '水曜日');
DEFINE('_KUNENA_DT_LDAY_THU', '木曜日');
DEFINE('_KUNENA_DT_LDAY_FRI', '金曜日');
DEFINE('_KUNENA_DT_LDAY_SAT', '土曜日');
DEFINE('_KUNENA_DT_DAY_SUN', '日');
DEFINE('_KUNENA_DT_DAY_MON', '月');
DEFINE('_KUNENA_DT_DAY_TUE', '火');
DEFINE('_KUNENA_DT_DAY_WED', '水');
DEFINE('_KUNENA_DT_DAY_THU', '木');
DEFINE('_KUNENA_DT_DAY_FRI', '金');
DEFINE('_KUNENA_DT_DAY_SAT', '土');
DEFINE('_KUNENA_DT_LMON_JAN', '1月');
DEFINE('_KUNENA_DT_LMON_FEB', '2月');
DEFINE('_KUNENA_DT_LMON_MAR', '3月');
DEFINE('_KUNENA_DT_LMON_APR', '4月');
DEFINE('_KUNENA_DT_LMON_MAY', '5月');
DEFINE('_KUNENA_DT_LMON_JUN', '6月');
DEFINE('_KUNENA_DT_LMON_JUL', '7月');
DEFINE('_KUNENA_DT_LMON_AUG', '8月');
DEFINE('_KUNENA_DT_LMON_SEP', '9月');
DEFINE('_KUNENA_DT_LMON_OCT', '10月');
DEFINE('_KUNENA_DT_LMON_NOV', '11月');
DEFINE('_KUNENA_DT_LMON_DEV', '12月');
DEFINE('_KUNENA_DT_MON_JAN', '1月');
DEFINE('_KUNENA_DT_MON_FEB', '2月');
DEFINE('_KUNENA_DT_MON_MAR', '3月');
DEFINE('_KUNENA_DT_MON_APR', '4月');
DEFINE('_KUNENA_DT_MON_MAY', '5月');
DEFINE('_KUNENA_DT_MON_JUN', '6月');
DEFINE('_KUNENA_DT_MON_JUL', '7月');
DEFINE('_KUNENA_DT_MON_AUG', '8月');
DEFINE('_KUNENA_DT_MON_SEP', '9月');
DEFINE('_KUNENA_DT_MON_OCT', '10月');
DEFINE('_KUNENA_DT_MON_NOV', '11月');
DEFINE('_KUNENA_DT_MON_DEV', '12月');
DEFINE('_KUNENA_CHILD_BOARD', '子ボード');
DEFINE('_WHO_ONLINE_GUEST', 'ゲスト');
DEFINE('_WHO_ONLINE_MEMBER', 'メンバー');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'なし');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'イメージ処理:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', '続けるにはここをクリックして下さい...');
DEFINE('_KUNENA_INSTALL_APPLY', '適用しました!');
DEFINE('_KUNENA_NO_ACCESS', 'この論壇へのアクセス権がありません!');
DEFINE('_KUNENA_TIME_SINCE', '%time% 前');
DEFINE('_KUNENA_DATE_YEARS', '年');
DEFINE('_KUNENA_DATE_MONTHS', '月');
DEFINE('_KUNENA_DATE_WEEKS','週');
DEFINE('_KUNENA_DATE_DAYS', '日');
DEFINE('_KUNENA_DATE_HOURS', '時間');
DEFINE('_KUNENA_DATE_MINUTES', '分');

// 1.0.2
DEFINE('_KUNENA_HEADERADD', '論壇ヘッダ:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "論壇表示");
DEFINE('_KUNENA_CLASS_SFX', "論壇CSSクラスサフィックス");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSSサフィックスはindex、大分類表示、ビューに適用され、論壇を異なるデザインに変更することができます。");
DEFINE('_COM_A_USER_EDIT_TIME', '利用者編集時間');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', '時間無制限なら0を設定します。else window
in seconds from post or last modification to allow edit.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', '利用者編集猶予時間');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'デフォルトは600（秒）です。編集リンクの非表示後、修正を最大600秒間許可します。');
DEFINE('_KUNENA_HELPPAGE','ヘルプページの利用');
DEFINE('_KUNENA_HELPPAGE_DESC','「はい」に設定すると、ヘッダメニュー内にヘルプページへのリンクが表示されます。');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Kunena内にヘルプを表示');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','「はい」を選択すると、Kunena内にヘルプテキストが含まれるようになり、外部ヘルプページのリンクは動作しなくなります。<b>注意:</b> ヘルプコンテンツIDを追加する必要があります。');
DEFINE('_KUNENA_HELPPAGE_CID','ヘルプコンテンツID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','「Kunena内にヘルプを表示」を「はい」に設定する必要があります。');
DEFINE('_KUNENA_HELPPAGE_LINK',' 外部ヘルプページリンク');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','外部ヘルプページリンクを表示するなら、「Kunena内にヘルプを表示」を「いいえ」に設定して下さい。');
DEFINE('_KUNENA_RULESPAGE','ルールページの利用');
DEFINE('_KUNENA_RULESPAGE_DESC','「はい」を選択すると、ヘッダメニュー内にルールページを表示するリンクが表示されます。');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Kunena内にルールを表示');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','「はい」を選択すると、Kunena内にルールテキストが含まれるようになり、外部ルールページのリンクは動作しなくなります。. <b>注意:</b> 「ルールコンテンツID」を追加する必要があります。');
DEFINE('_KUNENA_RULESPAGE_CID','ルールコンテンツID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','「Kunena内にルールを表示」を「はい」に設定する必要があります。');
DEFINE('_KUNENA_RULESPAGE_LINK',' 外部ルールページリンク');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','外部ルールページリンクを表示するなら「Kunena内にルールを表示」を「いいえ」に設定して下さい。');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD ライブラリが見つかりません');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD2 ライブラリが見つかりません');
DEFINE('_KUNENA_GD_INSTALLED','GD は次のバージョンで利用します ');
DEFINE('_KUNENA_GD_NO_VERSION','GD バージョンを見つけることができません');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD はインストールされていません。あなたは更に情報を得ることができます。 ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','イメージ（小）の高さ :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','イメージ（小）の幅 :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','イメージ（中）の高さ :');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','イメージ（中）の幅 :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','イメージ（大）の高さ :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','イメージ（大）の幅 :');
DEFINE('_KUNENA_AVATAR_QUALITY','アバター画像の品質');
DEFINE('_KUNENA_WELCOME','Kunenaへようこそ');
DEFINE('_KUNENA_WELCOME_DESC','ボードソリューションにKunenaを選んでいただきありがとうございます。この画面はボードの様々な統計情報の概要をすばやく確認できます。この画面の左側にあるリンクを使用し、ボード運用における全ての局面でKunenaをコントロールする事ができます。ツールの使い方について各ページには解説があります。');
DEFINE('_KUNENA_STATISTIC','統計');
DEFINE('_KUNENA_VALUE','値');
DEFINE('_GEN_CATEGORY','大分類');
DEFINE('_GEN_STARTEDBY','開始日: ');
DEFINE('_GEN_STATS','統計');
DEFINE('_STATS_TITLE',' 論壇 - 統計');
DEFINE('_STATS_GEN_STATS','全般統計');
DEFINE('_STATS_TOTAL_MEMBERS','メンバー:');
DEFINE('_STATS_TOTAL_REPLIES','返信:');
DEFINE('_STATS_TOTAL_TOPICS','トピック:');
DEFINE('_STATS_TODAY_TOPICS','本日のトピック:');
DEFINE('_STATS_TODAY_REPLIES','本日の返信:');
DEFINE('_STATS_TOTAL_CATEGORIES','大分類:');
DEFINE('_STATS_TOTAL_SECTIONS','セクション:');
DEFINE('_STATS_LATEST_MEMBER','最新メンバー:');
DEFINE('_STATS_YESTERDAY_TOPICS','昨日のトピック:');
DEFINE('_STATS_YESTERDAY_REPLIES','昨日の返信:');
DEFINE('_STATS_POPULAR_PROFILE','人気の10メンバー (プロファイルヒット数)');
DEFINE('_STATS_TOP_POSTERS','投稿者のトップ');
DEFINE('_STATS_POPULAR_TOPICS','人気トピックのトップ');
DEFINE('_COM_A_STATSPAGE','統計ページを有効');
DEFINE('_COM_A_STATSPAGE_DESC','「はい」に設定すると、ヘッダメニュー内に論壇統計ページへのリンクが表示されます。このページは論壇の様々な統計情報を表示します。<em>この設定にかかわらず管理者は常に統計ページを利用できます！</em>');
DEFINE('_COM_C_JBSTATS','論壇統計');
DEFINE('_COM_C_JBSTATS_DESC','論壇統計');
define('_GEN_GENERAL','全般');
define('_PERM_NO_READ','この論壇へアクセスするために必要な権限を持っていません');
DEFINE ('_KUNENA_SMILEY_SAVED','スマイルマークを保存しました');
DEFINE ('_KUNENA_SMILEY_DELETED','スマイルマークを削除しました');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','コードは既に存在しています');
DEFINE ('_KUNENA_MISSING_PARAMETER','パラメータが間違っています');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','ランクは既に存在しています');
DEFINE ('_KUNENA_RANK_DELETED','ランクを削除しました');
DEFINE ('_KUNENA_RANK_SAVED','ランクを保存しました');
DEFINE ('_KUNENA_DELETE_SELECTED','選択したものを削除');
DEFINE ('_KUNENA_MOVE_SELECTED','選択したものを移動');
DEFINE ('_KUNENA_REPORT_LOGGED','ログ記録');
DEFINE ('_KUNENA_GO','Go');
DEFINE('_KUNENA_MAILFULL','購読者へ送信するEメール内に完全な投稿内容を含める');
DEFINE('_KUNENA_MAILFULL_DESC','「いいえ」を選択した場合、購読者はニュースメッセージの表題のみ受け取ります。');
DEFINE('_KUNENA_HIDETEXT','このコンテンツを閲覧するにはログインして下さい！');
DEFINE('_BBCODE_HIDE','隠しテキスト: [hide]何らかの隠しテキスト[/hide] - メッセージの一部分がゲストから隠されます');
DEFINE('_KUNENA_FILEATTACH','添付されたファイル: ');
DEFINE('_KUNENA_FILENAME','ファイル名: ');
DEFINE('_KUNENA_FILESIZE','ファイルサイズ: ');
DEFINE('_KUNENA_MSG_CODE','コード: ');
DEFINE('_KUNENA_CAPTCHA_ON','スパム保護システム');
DEFINE('_KUNENA_CAPTCHA_DESC','アンチスパム & アンチボット CAPTCHA システムのOn/Off');
DEFINE('_KUNENA_CAPDESC','コードをここに入力して下さい');
DEFINE('_KUNENA_CAPERR','コードは正しくありません！');
DEFINE('_KUNENA_COM_A_REPORT', 'メッセージ報告');
DEFINE('_KUNENA_COM_A_REPORT_DESC', '利用者レポートにどんなメッセージでも欲しいなら「はい」を選択して下さい。');
DEFINE('_KUNENA_REPORT_MSG', '報告されたメッセージ');
DEFINE('_KUNENA_REPORT_REASON', '理由');
DEFINE('_KUNENA_REPORT_MESSAGE', 'あなたのメッセージ');
DEFINE('_KUNENA_REPORT_SEND', '報告を送信');
DEFINE('_KUNENA_REPORT', 'モデレータに報告');
DEFINE('_KUNENA_REPORT_RSENDER', '報告送信者: ');
DEFINE('_KUNENA_REPORT_RREASON', '報告理由: ');
DEFINE('_KUNENA_REPORT_RMESSAGE', '報告メッセージ: ');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'メッセージ投稿者: ');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'メッセージ件名: ');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'メッセージ: ');
DEFINE('_KUNENA_REPORT_POST_LINK', 'メッセージリンク: ');
DEFINE('_KUNENA_REPORT_INTRO', 'は、次の理由からメッセージが送られました:');
DEFINE('_KUNENA_REPORT_SUCCESS', '報告の送信に成功しました！');
DEFINE('_KUNENA_EMOTICONS', '顔文字');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'スマイルマーク');
DEFINE('_KUNENA_EMOTICONS_CODE', 'コード');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'スマイルマーク編集');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'スマイルマーク編集');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', '顔文字バー');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', '新規スマイルマーク');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', '顔文字をさらに選ぶ');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'ウインドウを閉じる');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', '追加の顔文字');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'スマイルマークを選ぶ');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla マンボットサポート');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Joomla マンボットサポートを有効');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'プロファイルプラグイン設定');
DEFINE('_KUNENA_USERNAMECANCHANGE', '利用者名の変更を許可する');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'プロファイルプラグインページ上で利用者名の変更を許可して下さい。');
DEFINE ('_KUNENA_RECOUNTFORUMS','大分類統計の再カウント');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','全ての大分類の統計情報が再カウントされました。');
DEFINE ('_KUNENA_EDITING_REASON','編集の理由');
DEFINE ('_KUNENA_EDITING_LASTEDIT','最終編集');
DEFINE ('_KUNENA_BY','By');
DEFINE ('_KUNENA_REASON','理由');
DEFINE('_GEN_GOTOBOTTOM', '下部へ移動する');
DEFINE('_GEN_GOTOTOP', '上部へ移動する');
DEFINE('_STAT_USER_INFO', '利用者情報');
DEFINE('_USER_SHOWEMAIL', 'Eメール表示');
DEFINE('_USER_SHOWONLINE', 'オンライン表示');
DEFINE('_KUNENA_HIDDEN_USERS', '隠された利用者');
DEFINE('_KUNENA_SAVE', '保存');
DEFINE('_KUNENA_RESET', 'リセット');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'デフォルトギャラリー');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', '個人情報');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', '要約');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'アバター');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', '論壇設定');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', '外観とレイアウト');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'プロファイル情報');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', '投稿');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', '購読');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'お気に入り');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'プライベートメッセージ');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'インボックス');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', '新規メッセージ');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'アウトボックス');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'ゴミ箱');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', '設定');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', '連絡先');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'ブロックリスト');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', '追加情報');
DEFINE('_KUNENA_MYPROFILE_NAME', '名前');
DEFINE('_KUNENA_MYPROFILE_USERNAME', '利用者名');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Eメール');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', '利用者タイプ');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', '登録日');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', '最終訪問日');
DEFINE('_KUNENA_MYPROFILE_POSTS', '投稿');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'プロファイル閲覧');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', '個人テキスト');
DEFINE('_KUNENA_MYPROFILE_GENDER', '性別');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', '誕生日');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', '年 (YYYY) - 月 (MM) - 日 (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', '地域');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'あなたのICQナンバー');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'あなたのAOLインスタントメッセンジャーニックネーム');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'あなたのYahoo!インスタントメッセンジャーニックネーム');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'あなたのSkypeハンドル');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'あなたのGtalkニックネーム');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Webサイト');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Webサイト名');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', '例: Best of Joomla!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Webサイト URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', '例: www.bestofjoomla.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'あなたのMSNメッセンジャーEメールアドレス');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', '署名');
DEFINE('_KUNENA_MYPROFILE_MALE', '男性');
DEFINE('_KUNENA_MYPROFILE_FEMALE', '女性');
DEFINE('_KUNENA_BULKMSG_DELETED', '投稿は正常に削除されました');
DEFINE('_KUNENA_DATE_YEAR', '年');
DEFINE('_KUNENA_DATE_MONTH', '月');
DEFINE('_KUNENA_DATE_WEEK','週');
DEFINE('_KUNENA_DATE_DAY', '日');
DEFINE('_KUNENA_DATE_HOUR', '時間');
DEFINE('_KUNENA_DATE_MINUTE', '分');
DEFINE('_KUNENA_IN_FORUM', ' 論壇名: ');
DEFINE('_KUNENA_FORUM_AT', ' Forum at: ');
DEFINE('_KUNENA_QMESSAGE_NOTE', '注意してください。ボードコードとスマイルマークボタンがありませんが使用可能です');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','論壇ツール');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','利用者リスト');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s には <b>%d</b> 人の登録利用者がいます');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','検索する値を入力してください！');
DEFINE ('_KUNENA_USRL_SEARCH','利用者を検索');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','検索');
DEFINE ('_KUNENA_USRL_LIST_ALL','全てをリスト');
DEFINE ('_KUNENA_USRL_NAME','名前');
DEFINE ('_KUNENA_USRL_USERNAME','利用者名');
DEFINE ('_KUNENA_USRL_GROUP','グループ');
DEFINE ('_KUNENA_USRL_POSTS','投稿');
DEFINE ('_KUNENA_USRL_KARMA','他の参加者からの評価');
DEFINE ('_KUNENA_USRL_HITS','ヒット');
DEFINE ('_KUNENA_USRL_EMAIL','Eメール');
DEFINE ('_KUNENA_USRL_USERTYPE','利用者タイプ');
DEFINE ('_KUNENA_USRL_JOIN_DATE','登録日');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','最終ログイン');
DEFINE ('_KUNENA_USRL_NEVER','Never');
DEFINE ('_KUNENA_USRL_ONLINE','ステータス');
DEFINE ('_KUNENA_USRL_AVATAR','写真');
DEFINE ('_KUNENA_USRL_ASC','昇順');
DEFINE ('_KUNENA_USRL_DESC','降順');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','表示');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%Y.%m.%d');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','プラグイン');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','利用者リスト');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','利用者リストの行数');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','利用者リストの行数');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','オンラインステータス');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','利用者のオンライン状態を表示');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','アバターを表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','本名を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','利用者名を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','利用者グループを表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','投稿数を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','他の参加者からの評価を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Eメールを表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','利用者タイプを表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','登録日を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','最終訪問日を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','プロファイルヒット数を表示');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'データベースウィザード');
DEFINE('_KUNENA_DBMETHOD', 'インストールを完了させたい方法を選択して下さい:');
DEFINE('_KUNENA_DBCLEAN', 'クリーンインストール');
DEFINE('_KUNENA_DBUPGRADE', 'Joomlaboardからアップグレード');
DEFINE('_KUNENA_TOPLEVEL', '最上位大分類');
DEFINE('_KUNENA_REGISTERED', '登録');
DEFINE('_KUNENA_PUBLICBACKEND', 'バックエンド公開');
DEFINE('_KUNENA_SELECTANITEMTO', 'Select an item to');
DEFINE('_KUNENA_ERRORSUBS', 'メッセージと購読を削除する事に何らかの問題があります');
DEFINE('_KUNENA_WARNING', '警告...');
DEFINE('_KUNENA_CHMOD1', 'ファイルを更新するにはchmodで766にする必要があります。');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'あなたの設定ファイルは');
DEFINE('_KUNENA_FIREBOARD', 'Fireboard');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'テンプレートの選択');
DEFINE('_KUNENA_CONFIGSAVED', '設定ファイルを保存しました');
DEFINE('_KUNENA_CONFIGNOTSAVED', '致命的なエラー: 設定ファイルに書き込みできません');


DEFINE('_KUNENA_TFINW', 'ファイルは書き込みできません。');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS ファイルを保存しました。');
DEFINE('_KUNENA_SELECTMODTO', 'モデレータを選択して下さい to');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', '削除するには論壇を選択する必要があります！');
DEFINE('_KUNENA_DELMSGERROR', 'メッセージの削除に失敗しました:');
DEFINE('_KUNENA_DELMSGERROR1', 'メッセージテキストの削除に失敗しました:');
DEFINE('_KUNENA_CLEARSUBSFAIL', '購読の解除に失敗しました:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', '論壇を除去しました:');
DEFINE('_KUNENA_PRUNEDAYS', '日');
DEFINE('_KUNENA_PRUNEDELETED', '削除:');
DEFINE('_KUNENA_PRUNETHREADS', 'スレッド');
DEFINE('_KUNENA_ERRORPRUNEUSERS', '削除エラー利用者:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', '利用者除去; 削除しました:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', '利用者プロファイル'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', '除去するプロファイルが見つかりませんでした。'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunenaテーブルは次のバージョンにアップグレードされました:');
DEFINE('_KUNENA_FORUMCATEGORY', '論壇大分類');


DEFINE('_KUNENA_IMGDELETED', 'イメージを削除しました');
DEFINE('_KUNENA_FILEDELETED', 'ファイルを削除しました');
DEFINE('_KUNENA_NOPARENT', '親なし');
DEFINE('_KUNENA_DIRCOPERR', 'エラー: ファイル');
DEFINE('_KUNENA_DIRCOPERR1', 'はコピーできませんでした！\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Kunena 論壇</strong>コンポーネント<em>Joomla!CMS向け</em> <br />&copy; 2006 - 2007 by Best Of Joomla<br>All rights reserved.');
DEFINE('_KUNENA_INSTALL2', '転送/インストール :</code></strong><br /><br /><font color="red"><b>成功');

DEFINE('_KUNENA_FORUMPRF_TITLE', 'プロファイル設定');
DEFINE('_KUNENA_FORUMPRF', 'プロファイル');
DEFINE('_KUNENA_FORUMPRRDESC', 'Clexus PMかCommunity Builderコンポーネントをインストールしたなら、利用者プロファイルページを使用するようKunenaに設定できます。');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'プロファイル');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>プロファイル閲覧</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', '全ての論壇メッセージ');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'トピック');
DEFINE('_KUNENA_USERPROFILE_STARTBY', '開始日');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', '大分類');
DEFINE('_KUNENA_USERPROFILE_DATE', '日付');
DEFINE('_KUNENA_USERPROFILE_HITS', 'ヒット');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', '論壇への投稿はありません');
DEFINE('_KUNENA_TOTALFAVORITE', 'お気に入り:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', '子ボードの列数  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'メイン大分類下に配置される子ボードの列数 ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', '投稿購読のチェックをデフォルトでONにしますか？');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', '投稿購読のチェックボックスを常にONにするなら「はい」を設定して下さい。');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', '大分類 / 論壇 は名前を付ける必要があります');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', '統計情報を表示');
DEFINE('_KUNENA_SHOWSTATSDESC', '統計情報を表示したいなら「はい」を選択して下さい');
DEFINE('_KUNENA_SHOWWHOIS', 'オンラインの利用者を表示');
DEFINE('_KUNENA_SHOWWHOISDESC', '誰がオンラインか表示するなら「はい」を選択します。');
DEFINE('_KUNENA_STATSGENERAL', '全般統計を表示');
DEFINE('_KUNENA_STATSGENERALDESC', '全般統計を表示したいなら「はい」を選択して下さい');
DEFINE('_KUNENA_USERSTATS', '人気利用者統計を表示');
DEFINE('_KUNENA_USERSTATSDESC', '人気利用者統計を表示したいなら「はい」を選択して下さい');
DEFINE('_KUNENA_USERNUM', '人気利用者数');
DEFINE('_KUNENA_USERPOPULAR', '人気題名統計を表示');
DEFINE('_KUNENA_USERPOPULARDESC', '人気の題名を表示するなら「はい」を選択します。');
DEFINE('_KUNENA_NUMPOP', '人気題名数');
DEFINE('_KUNENA_INFORMATION',
    'The Kunena team is proud to announce the release of Kunena 1.0.8. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard and Fireboard and most of our praises goes to their team. Some of the main features of Kunena can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add Joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community Builder and JomSocial profile options</li><li>Avatar management : Community Builder and JomSocial options<br /></li></ul><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Kunena! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'インストラクション');
DEFINE('_KUNENA_FINFO', 'Kunena 論壇インフォメーション');
DEFINE('_KUNENA_CSSEDITOR', 'Kunena テンプレートCSSエディタ');
DEFINE('_KUNENA_PATH', 'パス:');
DEFINE('_KUNENA_CSSERROR', '注意:CSSテンプレートファイルは変更を保存するために書き込み可能である必要があります。');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena 利用者プロファイルマネージャ');
DEFINE('_KUNENA_SORTID', '利用者IDで並び替え');
DEFINE('_KUNENA_SORTMOD', 'モデレータで並び替え');
DEFINE('_KUNENA_SORTNAME', '名前で並び替え');
DEFINE('_KUNENA_VIEW', 'ビュー');
DEFINE('_KUNENA_NOUSERSFOUND', '利用者プロファイルが見つかりません。');
DEFINE('_KUNENA_ADDMOD', 'モデレータを追加');
DEFINE('_KUNENA_NOMODSAV', '可能なモデレータが見つかりません。下記の\'注意\'を読んで下さい。');
DEFINE('_KUNENA_NOTEUS',
    '注意: Kunena プロファイル内にモデレータフラグがセットされた利用者だけがここに表示されます。モデレータを追加するにはモデレータフラグを利用者に与えて下さい。<a href="index2.php?option=com_kunena&task=profiles">利用者管理</a>を訪れ、モデレータを作成したい利用者を検索して下さい。それから利用者のプロファイルを選択し更新して下さい。モデレータフラグはサイト管理者だけが設定できます。');
DEFINE('_KUNENA_PROFFOR', 'プロファイル');
DEFINE('_KUNENA_GENPROF', '全般プロファイルオプション');
DEFINE('_KUNENA_PREFVIEW', '好みのビュータイプ:');
DEFINE('_KUNENA_PREFOR', '好みのメッセージ並び順:');
DEFINE('_KUNENA_ISMOD', 'モデレータ:');
DEFINE('_KUNENA_ISADM', '<strong>はい</strong> (変更できません。この利用者はサイト管理者（super administrator）です。');
DEFINE('_KUNENA_COLOR', '色');
DEFINE('_KUNENA_UAVATAR', '利用者アバター:');
DEFINE('_KUNENA_NS', '選択なし');
DEFINE('_KUNENA_DELSIG', ' この署名を削除するにはボックスにチェックして下さい。');
DEFINE('_KUNENA_DELAV', ' このアバターを削除するにはボックスにチェックして下さい。');
DEFINE('_KUNENA_SUBFOR', '通知:');
DEFINE('_KUNENA_NOSUBS', 'この利用者への通知はありません');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'ベーシック');
DEFINE('_KUNENA_BASICSFORUM', 'ベーシック論壇インフォメーション');
DEFINE('_KUNENA_PARENT', '親:');
DEFINE('_KUNENA_PARENTDESC',
    '注意して下さい: 大分類を作成するためには「最上位大分類」を親として選択して下さい。大分類は論壇のコンテナとして動作します。<br />論壇の親として先に作成した大分類を選択することで、論壇は<strong>大分類の中だけ</strong>に作成できます。<br /> 大分類に対し<strong>メッセージを投稿することはできません。</strong>投稿できるのは論壇だけです。');
DEFINE('_KUNENA_BASICSFORUMINFO', '論壇の名前と説明');
DEFINE('_KUNENA_NAMEADD', '名前:');
DEFINE('_KUNENA_DESCRIPTIONADD', '説明:');
DEFINE('_KUNENA_ADVANCEDDESC', '論壇拡張設定');
DEFINE('_KUNENA_ADVANCEDDESCINFO', '論壇のセキュリティとアクセス');
DEFINE('_KUNENA_LOCKEDDESC', 'この論壇を誰からもロックしたいなら「はい」を設定して下さい。モデレータと管理者はロックした論壇に返信もしくは新しいトピックを作成(もしくは投稿を移動）する事ができます。');
DEFINE('_KUNENA_LOCKED1', 'ロック:');
DEFINE('_KUNENA_PUBACC', '公開アクセスレベル:');
DEFINE('_KUNENA_PUBACCDESC',
    '非公開の論壇を作成するために、投稿もしくは参照可能な最小の利用者レベルをここで指定することができます。デフォルトの最小利用者レベルは「全員」に設定されています。<br /><b>注意</b>: 1つ以上の特定のグループに対し大分類全体をアクセス制限した場合、1つ以上の論壇に低いアクセスレベルが設定されていたとしても、適切な権限を全員が持たないことで大分類内に含む全ての論壇は見えなくなります！この制限はモデレータも同様です。; 大分類を参照するのに適切なグループレベルを持たない場合、大分類のモデレータリストにモデレータを追加する必要があります。<br /> これは大分類がモデレート不可という事実とは関係ありません。モデレータはまだモデレータリストに追加することができます。');
DEFINE('_KUNENA_CGROUPS', '子グループを含む:');
DEFINE('_KUNENA_CGROUPSDESC', '子グループも同様にアクセスを許可しますか？「いいえ」を設定した場合、この論壇へのアクセスは<b>選択したグループのみ</b>に制限されます。');
DEFINE('_KUNENA_ADMINLEVEL', '管理者アクセスレベル:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    '一般アクセス制限で論壇を作成した場合、ここで追加の管理アクセスレベルを指定できます。<br />特別なPublic Frontend利用者グループに論壇へのアクセスを制限し、Public Backendグループをここで指定しなかった場合、管理者は論壇へ入室/参照することができなくなります。');
DEFINE('_KUNENA_ADVANCED', '拡張');
DEFINE('_KUNENA_CGROUPS1', '子グループを含む:');
DEFINE('_KUNENA_CGROUPS1DESC', '子グループも同様にアクセスを許可しますか？「いいえ」を設定した場合、この論壇へのアクセスは<b>選択したグループのみ</b>に制限されます。');
DEFINE('_KUNENA_REV', '投稿をレビュー:');
DEFINE('_KUNENA_REVDESC',
    '投稿前にモデレータによるレビューをこの論壇で行いたい場合は「はい」を設定してください。これはモデレートされた論壇でのみ有効です！<br />モデレータを指定せずに設定した場合、送信された待機状態の投稿を承認/削除するのはサイト管理者1人の責任になります。');
DEFINE('_KUNENA_MOD_NEW', 'モデレーション');
DEFINE('_KUNENA_MODNEWDESC', '論壇のモデレーションと論壇モデレータ');
DEFINE('_KUNENA_MOD', 'モデレート:');
DEFINE('_KUNENA_MODDESC',
    'この論壇にモデレータを割り当てるなら「はい」を設定して下さい。<br /><strong>注意:</strong> 論壇への新しい投稿が公開前にレビューされるという意味ではありません！そのためには拡張タブで「レビュー」オプションを設定する必要があります。<br /><br /> <strong>注意:</strong> 新規ボタンを使用してモデレータを追加する前に、モデレーションに「はい」を設定後、最初に論壇設定を保存する必要があります。');
DEFINE('_KUNENA_MODHEADER', 'この論壇のモデレーション設定');
DEFINE('_KUNENA_MODSASSIGNED', 'この論壇へのモデレータ割り当て:');
DEFINE('_KUNENA_NOMODS', 'この論壇に割り当てられたモデレータはいません。');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', '編集');
DEFINE('_KUNENA_ADD', '追加');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', '上へ移動');
DEFINE('_KUNENA_MOVEDOWN', '下へ移動');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', '全ての登録利用者');
DEFINE('_KUNENA_EVERYBODY', '全員');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', '並び順');
DEFINE('_KUNENA_CHECKEDOUT', 'チェックアウト');
DEFINE('_KUNENA_ADMINACCESS', '管理者アクセス');
DEFINE('_KUNENA_PUBLICACCESS', '公開アクセス');
DEFINE('_KUNENA_PUBLISHED', '公開');
DEFINE('_KUNENA_REVIEW', 'レビュー');
DEFINE('_KUNENA_MODERATED', 'モデレート');
DEFINE('_KUNENA_LOCKED', 'ロック');
DEFINE('_KUNENA_CATFOR', '大分類 / 論壇');
DEFINE('_KUNENA_ADMIN', 'Kunena 管理');
DEFINE('_KUNENA_CP', 'Kunena コントロールパネル');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'アバター統合');
DEFINE('_COM_A_RANKS_SETTINGS', 'ランク');
DEFINE('_COM_A_RANKING_SETTINGS', 'ランキング設定');
DEFINE('_COM_A_AVATAR_SETTINGS', 'アバター設定');
DEFINE('_COM_A_SECURITY_SETTINGS', 'セキュリティ設定');
DEFINE('_COM_A_BASIC_SETTINGS', '基本設定');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'お気に入りを許可');
DEFINE('_COM_A_FAVORITES_DESC', 'トピックのお気に入り追加を登録利用者に許可するなら「はい」を設定して下さい。');
DEFINE('_USER_UNFAVORITE_ALL', '全てのトピックから<b><u>お気に入りを解除</u></b>するにはこのボックスにチェックしてください(トラブルシューティング目的のため見えない1つを含んでいます）');
DEFINE('_VIEW_FAVORITETXT', 'このトピックをお気に入りに追加 ');
DEFINE('_USER_UNFAVORITE_YES', 'トピックからお気に入りが解除されました');
DEFINE('_POST_FAVORITED_TOPIC', 'あなたのお気に入りは処理されました。');
DEFINE('_VIEW_UNFAVORITETXT', 'お気に入り解除');
DEFINE('_VIEW_UNSUBSCRIBETXT', '購読解除');
DEFINE('_USER_NOFAVORITES', 'お気に入りなし');
DEFINE('_POST_SUCCESS_FAVORITE', 'あなたのお気に入りは処理されました。');
DEFINE('_COM_A_MESSAGES_SEARCH', '検索結果');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', '1ページあたりに表示する検索結果メッセージ');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Joolmaスタイルを使用しますか？');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Joomlaスタイルを使用するなら「はい」を設定します。(sectionheader, sectionentry1 ...のようなクラス) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', '子大分類イメージを表示');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', '論壇リストに子大分類の小さいアイコンを表示するには「はい」を設定します。');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'お知らせを表示');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', '論壇のホームページにお知らせボックスを表示するなら「はい」を設定します。');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', '大分類リストにアバターを表示');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', '論壇大分類リストに利用者アバターを表示するなら「はい」を設定します。');
DEFINE('_KUNENA_RECENT_POSTS', '最近の投稿設定');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', '最近の投稿を表示');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', '論壇に最近の投稿プラグインを表示するなら「はい」を設定します。');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', '最近の投稿数');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', '最近の投稿数');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'タブあたりのカウント ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', '1つのタブあたりの投稿数');
DEFINE('_KUNENA_LATEST_CATEGORY', '大分類を表示');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', '指定した大分類を最近の投稿に表示することができます。例:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', '単独題名を表示');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', '単独題名を表示します');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', '返信題名を表示');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', '返信題名（Re:）を表示します');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', '題名の長さ');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', '題名の長さです');
DEFINE('_KUNENA_SHOW_LATEST_DATE', '日付を表示');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', '日付を表示する');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'ヒット数を表示');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'ヒット数を表示する');
DEFINE('_KUNENA_SHOW_AUTHOR', '作者を表示');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=利用者名, 2=本名, 0=なし');
DEFINE('_KUNENA_STATS', '統計プラグイン設定 ');
DEFINE('_KUNENA_CATIMAGEPATH', '大分類イメージパス ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', '大分類のイメージを保存するパスです。「category_images/」と設定すれば「your_html_rootfolder/images/fbfiles/category_images/」に保存されます。');
DEFINE('_KUNENA_ANN_MODID', 'お知らせモデレータID ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'お知らせをモデレートする利用者のIDを追加して下さい。例:62,63,73 . お知らせモデレータはお知らせを追加、編集、削除することができます。');
//
DEFINE('_KUNENA_FORUM_TOP', 'ボード大分類 ');
DEFINE('_KUNENA_CHILD_BOARDS', '子ボード ');
DEFINE('_KUNENA_QUICKMSG', 'クイック返信 ');
DEFINE('_KUNENA_THREADS_IN_FORUM', '論壇内のスレッド ');
DEFINE('_KUNENA_FORUM', '論壇 ');
DEFINE('_KUNENA_SPOTS', 'スポットライト');
DEFINE('_KUNENA_CANCEL', 'キャンセル');
DEFINE('_KUNENA_TOPIC', 'トピック: ');
DEFINE('_KUNENA_POWEREDBY', 'Powered by ');
// Time Format
DEFINE('_TIME_TODAY', '<b>本日</b> ');
DEFINE('_TIME_YESTERDAY', '<b>昨日</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', '最近の投稿');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'オンラインの利用者');
DEFINE('_KUNENA_WHO_MAINPAGE', '論壇メイン');
DEFINE('_KUNENA_GUEST', 'ゲスト');
DEFINE('_KUNENA_PATHWAY_VIEWING', '人が閲覧中');
DEFINE('_KUNENA_ATTACH', '添付');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'お気に入り');
DEFINE('_USER_FAVORITES', 'あなたのお気に入り');
DEFINE('_THREAD_UNFAVORITE', 'お気に入りを解除');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'ようこそ');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', '最新投稿を表示');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'マイアバターを設定');
DEFINE('_PROFILEBOX_MYPROFILE', 'プロファイル');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', '自分の投稿を表示');
DEFINE('_PROFILEBOX_GUEST', 'ゲスト');
DEFINE('_PROFILEBOX_LOGIN', 'ログイン');
DEFINE('_PROFILEBOX_REGISTER', '登録して下さい');
DEFINE('_PROFILEBOX_LOGOUT', 'ログアウト');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'パスワードを忘れましたか？');
DEFINE('_PROFILEBOX_PLEASE', '');
DEFINE('_PROFILEBOX_OR', 'または');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', '最近の投稿');
DEFINE('_RECENT_TOPICS', 'トピック');
DEFINE('_RECENT_AUTHOR', '作者');
DEFINE('_RECENT_CATEGORIES', '大分類');
DEFINE('_RECENT_DATE', '日付');
DEFINE('_RECENT_HITS', 'ヒット');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'お知らせ');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', '日付');
DEFINE('_ANN_TITLE', '表題');
DEFINE('_ANN_SORTTEXT', '短いテキスト');
DEFINE('_ANN_LONGTEXT', '長いテキスト');
DEFINE('_ANN_ORDER', '並び順');
DEFINE('_ANN_PUBLISH', '公開');
DEFINE('_ANN_PUBLISHED', '公開済');
DEFINE('_ANN_UNPUBLISHED', '非公開');
DEFINE('_ANN_EDIT', '編集');
DEFINE('_ANN_DELETE', '削除');
DEFINE('_ANN_SUCCESS', '成功');
DEFINE('_ANN_SAVE', '保存');
DEFINE('_ANN_YES', 'はい');
DEFINE('_ANN_NO', 'いいえ');
DEFINE('_ANN_ADD', '新規追加');
DEFINE('_ANN_SUCCESS_EDIT', '編集成功');
DEFINE('_ANN_SUCCESS_ADD', '追加成功');
DEFINE('_ANN_DELETED', '削除成功');
DEFINE('_ANN_ERROR', 'エラー');
DEFINE('_ANN_READMORE', '続きを読む...');
DEFINE('_ANN_CPANEL', 'お知らせコントロールパネル');
DEFINE('_ANN_SHOWDATE', '日付を表示');
// Stats
DEFINE('_STAT_FORUMSTATS', '論壇統計');
DEFINE('_STAT_GENERAL_STATS', '全般統計');
DEFINE('_STAT_TOTAL_USERS', '利用者合計');
DEFINE('_STAT_LATEST_MEMBERS', '最新メンバー');
DEFINE('_STAT_PROFILE_INFO', 'プロファイルを見る');
DEFINE('_STAT_TOTAL_MESSAGES', 'メッセージ合計');
DEFINE('_STAT_TOTAL_SUBJECTS', 'トピック合計');
DEFINE('_STAT_TOTAL_CATEGORIES', '大分類合計');
DEFINE('_STAT_TOTAL_SECTIONS', 'セクション合計');
DEFINE('_STAT_TODAY_OPEN_THREAD', '本日オープン');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', '昨日オープン');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', '本日の返信合計');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', '昨日の返信合計');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', '最近の投稿を見る');
DEFINE('_STAT_MORE_ABOUT_STATS', '統計をさらに見る');
DEFINE('_STAT_USERLIST', '利用者リスト');
DEFINE('_STAT_TEAMLIST', 'Board チーム');
DEFINE('_STATS_FORUM_STATS', '論壇統計');
DEFINE('_STAT_POPULAR', '人気');
DEFINE('_STAT_POPULAR_USER_TMSG', '利用者 ( メッセージ合計) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'スレッド ');
DEFINE('_STAT_POPULAR_USER_GSG', '利用者 ( プロファイル閲覧合計) ');
//Team List
DEFINE('_MODLIST_ONLINE', '利用者は現在オンライン');
DEFINE('_MODLIST_OFFLINE', '利用者はオフライン');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'オンラインの利用者');
DEFINE('_WHO_ONLINE_NOW', 'オンライン');
DEFINE('_WHO_ONLINE_MEMBERS', 'メンバー');
DEFINE('_WHO_AND', 'and');
DEFINE('_WHO_ONLINE_GUESTS', 'ゲスト');
DEFINE('_WHO_ONLINE_USER', '利用者');
DEFINE('_WHO_ONLINE_TIME', '時間');
DEFINE('_WHO_ONLINE_FUNC', '動作');
// Userlist
DEFINE('_USRL_USERLIST', '利用者リスト');
DEFINE('_USRL_REGISTERED_USERS', '%s には <b>%d</b> 人の登録利用者がいます');
DEFINE('_USRL_SEARCH_ALERT', '検索する値を入力して下さい！');
DEFINE('_USRL_SEARCH', '利用者を見つける');
DEFINE('_USRL_SEARCH_BUTTON', '検索');
DEFINE('_USRL_LIST_ALL', '全てをリスト');
DEFINE('_USRL_NAME', '名前');
DEFINE('_USRL_USERNAME', '利用者名');
DEFINE('_USRL_EMAIL', 'Eメール');
DEFINE('_USRL_USERTYPE', '利用者タイプ');
DEFINE('_USRL_JOIN_DATE', '登録日');
DEFINE('_USRL_LAST_LOGIN', '最終ログイン');
DEFINE('_USRL_NEVER', 'Never');
DEFINE('_USRL_BLOCK', 'ステータス');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', '昇順');
DEFINE('_USRL_DESC', '降順');
DEFINE('_USRL_DATE_FORMAT', '%Y.%m.%d');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', '詳細');
DEFINE('_USRL_COMPROFILER', 'プロファイル');
DEFINE('_USRL_THUMBNAIL', 'Pic');
DEFINE('_USRL_READON', '表示');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Send PM');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', '検索結果');
DEFINE('_USRL_STATUS', 'ステータス');
DEFINE('_USRL_LISTSETTINGS', '利用者リスト設定');
DEFINE('_USRL_ERROR', 'エラー');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'プライベートメッセージコンポーネント');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', '次の語句で検索: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'このメッセージを本当に削除してもよろしいですか？ \n\n 注意: 削除したメッセージを復活させる方法はありません！');
DEFINE('_MODERATION_DELETE_SUCCESS', '投稿は削除されました');
DEFINE('_COM_A_RANKING', 'ランキング');
DEFINE('_COM_A_BOT_REFERENCE', 'ボットコード参考を表示');
DEFINE('_COM_A_MOSBOT', 'ディスカスボットを有効');
DEFINE('_PREVIEW', 'プレビュー');
DEFINE('_COM_A_MOSBOT_TITLE', 'ディスカスボット');
DEFINE('_COM_A_MOSBOT_DESC', 'コンテンツアイテムについて論壇内で議論する事をディスカスボットは可能にします。コンテンツ表題はトピックの題名として使われます。'
           . '<br />まだトピックが存在しない場合は新しく作成されます。トピックが存在する場合はスレッドが表示され利用者は返信可能になります。' . '<br /><strong>別途ボットをダウンロードしてインストールする必要があります。</strong>'
           . '<br />より多くの情報は<a href="http://www.Kunena.com">Kunena Site</a>を確認して下さい。' . '<br />インストール後、コンテンツアイテムに次のボット行を追加する必要があります:' . '<br />{mos_fb_discuss:<em>大分類ID</em>}'
           . '<br /><em>大分類ID</em>はコンテンツアイテムについて議論可能な大分類です。適切な大分類IDを知るには論壇内を参照し' . 'ブラウザのステータスバーに表示されたURLを確認して下さい。'
           . '<br />例: 大分類IDが26の論壇内で記事について議論したい場合は、{mos_fb_discuss:26}のように入力します。'
           . '<br />少しだけ難しそうに見えますが、コンテンツアイテムに一致した論壇で議論することができます。');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', '検索');
DEFINE('_FORUM_SEARCHRESULTS', '%s件の結果を表示しています（%s件中）');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'FAQ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'ルール');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>ルールを挿入するためにこのファイルを編集して下さい joomlaroot/administrator/components/com_kunena/language/kunena.japanese.php</li><li>ルール 2</li><li>ルール 3</li><li>ルール 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'ボードコード');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', '投稿は承認されました');
DEFINE('_MODERATION_DELETE_ERROR', 'エラー: 投稿は削除できませんでした');
DEFINE('_MODERATION_APPROVE_ERROR', 'エラー: 投稿は承認できませんでした');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'この大分類内には論壇がありません！');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', '古い論壇内でゴーストトピックを作成することに失敗しました！');
DEFINE('_POST_MOVE_GHOST', '古い論壇内のゴーストメッセージを残す');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', '論壇ジャンプ');
DEFINE('_COM_A_FORUM_JUMP', '論壇ジャンプを有効');
DEFINE('_COM_A_FORUM_JUMP_DESC', '「はい」に設定すると、論壇ページ上に他の論壇や大分類にすばやくジャンプするセレクタが表示されます。');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'ルール');
DEFINE('_COM_A_RULESPAGE', 'ルールページを有効');
DEFINE('_COM_A_RULESPAGE_DESC',
    '「はい」に設定すると、ヘッダメニュー内のリンクがルールページに表示されます。このページはあなたの論壇ルール等について使用できます。次の場所に保存された「rules.php」を開くことで、このファイルの内容を好みに変更することができます。（/joomla_root/components/com_kunena） <em>アップグレード時にこのファイルは上書きされるので常にバックアップしてください！</em>');
DEFINE('_MOVED_TOPIC', '移動:');
DEFINE('_COM_A_PDF', 'PDF作成を有効');
DEFINE('_COM_A_PDF_DESC',
    'スレッドのコンテンツをシンプルなPDFドキュメントでダウンロード可能にするなら「はい」を設定してください。<br />それは<u>シンプル</u>なPDFドキュメントです。; マークアップ無し、装飾的なレイアウト無しですが、スレッドの全てのテキストを含みます。');
DEFINE('_GEN_PDFA', 'このスレッドからPDFドキュメントを作成するには、このボタンをクリックしてください(新しいウインドウが開きます）。');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'この利用者のプロファイルを見るにはここをクリックして下さい');
DEFINE('_VIEW_ADDBUDDY', '友達リストにこの利用者を追加するにはここをクリックして下さい');
DEFINE('_POST_SUCCESS_POSTED', 'あなたのメッセージは正しく投稿されました');
DEFINE('_POST_SUCCESS_VIEW', '[ 投稿へ戻る ]');
DEFINE('_POST_SUCCESS_FORUM', '[ 論壇へ戻る ]');
DEFINE('_RANK_ADMINISTRATOR', '管理者');
DEFINE('_RANK_MODERATOR', '世話役');
DEFINE('_SHOW_LASTVISIT', '最終訪問から');
DEFINE('_COM_A_BADWORDS_TITLE', '禁止語句フィルタリング');
DEFINE('_COM_A_BADWORDS', '禁止語句フィルタリングを使用');
DEFINE('_COM_A_BADWORDS_DESC', '禁止語句コンポーネント設定で定義した語句を含む投稿についてフィルタしたい場合は「はい」を設定して下さい。この機能を使用するには禁止語句コンポーネントをインストールしておく必要があります！');
DEFINE('_COM_A_BADWORDS_NOTICE', '* このメッセージは管理者によって設定された1つ以上の語句を含むため取り除かれました。*');
DEFINE('_COM_A_AVATAR_SRC', 'アバター画像の使用元');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'Clexus PM もしくは Community Builder コンポーネントがインストールされているなら、利用者のアバター写真をClexus PM もしくは Community Builder利用者プロファイルからKunenaに設定する事ができます。注意: 論壇はオリジナル画像ではないサムネイルの利用者画像を使用するため、Community BuilderではサムネイルオプションをONにする必要があります。');
DEFINE('_COM_A_KARMA', '他の参加者からの評価インジケータを表示');
DEFINE('_COM_A_KARMA_DESC', '利用者統計が有効な時、利用者他の参加者からの評価と関連するボタン（増加/減少）を表示するには「はい」を設定して下さい。');
DEFINE('_COM_A_DISEMOTICONS', '顔文字を無効');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'グラフィック顔文字（スマイルマーク）を完全に無効にするには「はい」を選択して下さい。');
DEFINE('_COM_C_FBCONFIG', 'Kunena 設定');
DEFINE('_COM_C_FBCONFIGDESC', 'Kunena 全ての機能を設定します');
DEFINE('_COM_C_FORUM', '論壇管理');
DEFINE('_COM_C_FORUMDESC', '大分類/論壇を追加/設定します');
DEFINE('_COM_C_USER', '利用者管理');
DEFINE('_COM_C_USERDESC', '基本利用者と利用者プロファイルを管理します');
DEFINE('_COM_C_FILES', 'アップロードファイルブラウザ');
DEFINE('_COM_C_FILESDESC', 'アップロードしたファイルを参照/管理します');
DEFINE('_COM_C_IMAGES', 'アップロードイメージブラウザ');
DEFINE('_COM_C_IMAGESDESC', 'アップロードしたイメージを参照/管理します');
DEFINE('_COM_C_CSS', 'CSSファイル編集');
DEFINE('_COM_C_CSSDESC', 'Kunenaのデザインを調整します');
DEFINE('_COM_C_SUPPORT', 'サポートWebサイト');
DEFINE('_COM_C_SUPPORTDESC', 'Best of JoomlaのWebサイトへ接続します（新しいウインドウで）');
DEFINE('_COM_C_PRUNETAB', '論壇除去');
DEFINE('_COM_C_PRUNETABDESC', '古いスレッドを削除します（設定可能）');
DEFINE('_COM_C_PRUNEUSERS', '利用者除去');
DEFINE('_COM_C_PRUNEUSERSDESC', 'Kunena 利用者テーブルとJoomla!利用者テーブルを同期します');
DEFINE('_COM_C_LOADMODPOS', 'モジュールポジションの読み込み');
DEFINE('_COM_C_LOADMODPOSDESC', 'Kunenaテンプレート向けにモジュールポジションを読み込みます');
DEFINE('_COM_C_UPGRADEDESC', 'アップグレード後、データベースは最新版になります');
DEFINE('_COM_C_BACK', 'Kunenaコントロールパネルへ戻る');
DEFINE('_SHOW_LAST_SINCE', '最終訪問以降のアクティブなトピック:');
DEFINE('_POST_SUCCESS_REQUEST2', 'あなたのリクエストは処理されました');
DEFINE('_POST_NO_PUBACCESS3', '登録するにはここをクリックしてください。');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'メッセージは正常に削除されました。');
DEFINE('_POST_SUCCESS_EDIT', 'メッセージは正常に編集されました。');
DEFINE('_POST_SUCCESS_MOVE', 'トピックは正常に移動されました。');
DEFINE('_POST_SUCCESS_POST', 'あなたのメッセージは正常に投稿されました。');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'あなたの購読は処理されました。');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', '他の参加者からの評価');
DEFINE('_KARMA_SMITE', '減少');
DEFINE('_KARMA_APPLAUD', '増加');
DEFINE('_KARMA_BACK', 'トピックへ戻るため、');
DEFINE('_KARMA_WAIT', '6時間毎に1人だけ他の参加者からの評価を修正する事ができます。<br/>誰かの他の参加者からの評価を再度修正する前にタイムアウト期間が経過するまでお待ちください。');
DEFINE('_KARMA_SELF_DECREASE', 'あなた自身の他の参加者からの評価を減少するような事を試さないで下さい！');
DEFINE('_KARMA_SELF_INCREASE', '自身で他の参加者からの評価を増加させようとしたため、あなたの他の参加者からの評価は減少されました！');
DEFINE('_KARMA_DECREASED', '利用者他の参加者からの評価は減少しました。すぐにトピックへ戻らない場合は、');
DEFINE('_KARMA_INCREASED', '利用者他の参加者からの評価は増加しました。すぐにトピックへ戻らない場合は、');
DEFINE('_COM_A_TEMPLATE', 'テンプレート');
DEFINE('_COM_A_TEMPLATE_DESC', '使用するテンプレートを選択して下さい。');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'イメージセット');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', '使用するイメージセットテンプレートを選択して下さい。');
DEFINE('_PREVIEW_CLOSE', 'ウインドウを閉じる');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', '利用者投稿統計バー');
DEFINE('_COM_A_POSTSTATSBAR_DESC', '利用者の投稿数を統計バーでグラフィカルに描画したいなら「はい」を選択して下さい。');
DEFINE('_COM_A_POSTSTATSCOLOR', '統計バーの色番号');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', '投稿統計バーに使用したい色の番号を与えてください。');
DEFINE('_LATEST_REDIRECT',
    'あなたの最新投稿のリストを作成する前に、Kunena はあなたのアクセス権を確立する必要があります。\n 心配しないで下さい。これは30分以上何もしなかった後、またはログインし直した後なら正常です。\n 検索リクエストを再度送信して下さい。');
DEFINE('_SMILE_COLOUR', '色調');
DEFINE('_SMILE_SIZE', 'サイズ');
DEFINE('_COLOUR_DEFAULT', '標準');
DEFINE('_COLOUR_RED', 'レッド');
DEFINE('_COLOUR_PURPLE', 'パープル');
DEFINE('_COLOUR_BLUE', 'ブルー');
DEFINE('_COLOUR_GREEN', 'グリーン');
DEFINE('_COLOUR_YELLOW', 'イエロー');
DEFINE('_COLOUR_ORANGE', 'オレンジ');
DEFINE('_COLOUR_DARKBLUE', 'ダークブルー');
DEFINE('_COLOUR_BROWN', 'ブラウン');
DEFINE('_COLOUR_GOLD', 'ゴールド');
DEFINE('_COLOUR_SILVER', 'シルバー');
DEFINE('_SIZE_NORMAL', '通常');
DEFINE('_SIZE_SMALL', '小');
DEFINE('_SIZE_VSMALL', '最小');
DEFINE('_SIZE_BIG', '大');
DEFINE('_SIZE_VBIG', '最大');
DEFINE('_IMAGE_SELECT_FILE', '添付するイメージファイルを選択して下さい');
DEFINE('_FILE_SELECT_FILE', '添付するファイルを選択して下さい');
DEFINE('_FILE_NOT_UPLOADED', 'ファイルはアップロードされませんでした。投稿を再度試すか、投稿を編集してください。');
DEFINE('_IMAGE_NOT_UPLOADED', 'イメージはアップロードされませんでした。投稿を再度試すか、投稿を編集してください。');
DEFINE('_BBCODE_IMGPH', 'イメージを添付するため、投稿内に[img]プレースホルダを挿入してください。');
DEFINE('_BBCODE_FILEPH', 'ファイルを添付するため、投稿内に[file]プレースホルダを挿入してください。');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', '全てのトピックから<b><u>購読を解除</u></b>するにはこのボックスにチェックしてください(トラブルシューティング目的のため見えない1つを含んでいます）');
DEFINE('_LINK_JS_REMOVED', '<em>JavaScriptを含んでいる有効なリンクは自動的に削除されました。</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'デザイン');
DEFINE('_COM_A_USERS', '利用者関連');
DEFINE('_COM_A_LENGTHS', '各種長さ設定');
DEFINE('_COM_A_SUBJECTLENGTH', '題名の最大長さ');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    '題名の最大長さです。データベースでサポートされている最大値は255文字です。あなたのサイトがUnicodeのようなマルチバイト文字を使用する場合、UTF-8や非ISO-8599-xが作成する最大値は規定値より小さくなります:<br/><tt>round_down(255/(最大文字のバイト数/文字))</tt><br/> 例えばUTF-8の場合、1文字あたりの最大バイト数は4バイトです: 255/4=63.');
DEFINE('_LATEST_THREADFORUM', 'トピック/論壇');
DEFINE('_LATEST_NUMBER', '新規投稿');
DEFINE('_COM_A_SHOWNEW', '新規投稿を表示');
DEFINE('_COM_A_SHOWNEW_DESC', '「はい」に設定すると、新規投稿と最終訪問以降の投稿を含む論壇へ、インジケータを利用者に表示します。');
DEFINE('_COM_A_NEWCHAR', '&quot;新規&quot; インジケータ');
DEFINE('_COM_A_NEWCHAR_DESC', '新規投稿を示すために何を使用するか定義して下さい (&quot;!&quot;または &quot;New!&quot;)など');
DEFINE('_LATEST_AUTHOR', '最近の投稿者');
DEFINE('_GEN_FORUM_NEWPOST', '新規投稿');
DEFINE('_GEN_FORUM_NOTNEW', '古い投稿');
DEFINE('_GEN_UNREAD', '未読のトピック');
DEFINE('_GEN_NOUNREAD', '既読のトピック');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', '全ての論壇を既読としてマークする');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'この論壇を既読としてマークする');
DEFINE('_GEN_FORUM_MARKED', 'この論壇内にある全ての投稿は既読としてマークされました。');
DEFINE('_GEN_ALL_MARKED', '全ての投稿は既読としてマークされました');
DEFINE('_IMAGE_UPLOAD', 'イメージアップロード');
DEFINE('_IMAGE_DIMENSIONS', 'あなたのイメージファイルは最大である可能性があります (幅 x 高さ - サイズ)');
DEFINE('_IMAGE_ERROR_TYPE', 'jpeg、gif、pngイメージだけ使用してください');
DEFINE('_IMAGE_ERROR_EMPTY', 'アップロード前にファイルを選択して下さい');
DEFINE('_IMAGE_ERROR_SIZE', 'イメージファイルサイズが管理者によって設定された最大値を超えました。');
DEFINE('_IMAGE_ERROR_WIDTH', 'イメージファイルの幅が管理者によって設定された最大値を超えました。');
DEFINE('_IMAGE_ERROR_HEIGHT', 'イメージファイルの高さが管理者によって設定された最大値を超えました。');
DEFINE('_IMAGE_UPLOADED', 'あなたのイメージはアップロードされました。');
DEFINE('_COM_A_IMAGE', 'イメージ');
DEFINE('_COM_A_IMGHEIGHT', '最大イメージ高さ');
DEFINE('_COM_A_IMGWIDTH', '最大イメージ幅');
DEFINE('_COM_A_IMGSIZE', 'イメージファイルサイズの最大値<br/><em>（キロバイト）</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'イメージのアップロードを全員に許可');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', '誰でもイメージをアップロード可能にしたいなら「はい」を選択して下さい。');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'イメージのアップロードを登録者に許可');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', '登録/ログインした利用者に対しイメージアップロード可能にするなら「はい」を選択して下さい。<br/> 注意: (Super)administrators と モデレータは常にイメージのアップロードが可能です。');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'アップロード');
DEFINE('_FILE_TYPES', 'ファイルのタイプは最大サイズの可能性があります。');
DEFINE('_FILE_ERROR_TYPE', '次のファイルだけがアップロードを許可されています:\n');
DEFINE('_FILE_ERROR_EMPTY', 'アップロード前にファイルを選択して下さい');
DEFINE('_FILE_ERROR_SIZE', 'ファイルサイズが管理者によって設定された最大値を超えました。');
DEFINE('_COM_A_FILE', 'ファイル');
DEFINE('_COM_A_FILEALLOWEDTYPES', '許可されたファイルタイプ');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'アップロードを許可するファイルタイプをここで指定して下さい。スペースなしの<strong>小文字</strong>で記入し、区切るにはカンマを使用して下さい。<br />例: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', '最大ファイルサイズ<br/><em>（キロバイト）</em>');
DEFINE('_COM_A_FILEUPLOAD', '全員にファイルアップロードを許可');
DEFINE('_COM_A_FILEUPLOAD_DESC', '誰でもファイルをアップロード可能にしたいなら「はい」を選択して下さい。');
DEFINE('_COM_A_FILEREGUPLOAD', '登録者にファイルアップロードを許可');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', '登録/ログインした利用者にファイルアップロード可能にするなら「はい」を選択して下さい。<br/> 注意: (Super)administrators と モデレータは常にファイルのアップロードが可能です。');
DEFINE('_SUBMIT_CANCEL', 'あなたの投稿送信はキャンセルされました');
DEFINE('_HELP_SUBMIT', 'メッセージを送信するにはここをクリックして下さい');
DEFINE('_HELP_PREVIEW', 'メッセージを送信した状態で見るにはここをクリックしてください');
DEFINE('_HELP_CANCEL', 'メッセージをキャンセルするにはここをクリックして下さい');
DEFINE('_POST_DELETE_ATT', 'このボックスをチェックすると、投稿に添付された全てのイメージ/ファイルが同じように削除されます。(推奨).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'マークアップ編集を表示');
DEFINE('_COM_A_USER_MARKUP_DESC', '「はい」を設定すると、if you want an edited post be marked up with text showing that the post is edited by a user and when it was edited.');
DEFINE('_EDIT_BY', '投稿編集者:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'アバターアップロード時にエラーが発生しました。再試行するかシステム管理者に連絡して下さい。');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'アップロードイメージブラウザ');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'アップロードファイルブラウザ');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'アップロードされたイメージ数');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'アップロードされたファイル数');
DEFINE('_COM_A_IMGB_ENLARGE', 'イメージを最大サイズで見るにはここをクリックして下さい');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'ファイルをダウンロードするにはファイル画像をクリックして下さい');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    '「ダミーで置き換え」オプションは、選択したイメージをダミー画像で置き換えます。<br />投稿を破壊する事なく、実際のファイルを削除する事ができます。<br /><small><em>置き換えたダミー画像を見るには時々ブラウザを再読み込みする必要があることに注意してください。</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', '現在のダミー画像');
DEFINE('_COM_A_IMGB_REPLACE', 'ダミーで置き換え');
DEFINE('_COM_A_IMGB_REMOVE', '完全削除');
DEFINE('_COM_A_IMGB_NAME', '名前');
DEFINE('_COM_A_IMGB_SIZE', 'サイズ');
DEFINE('_COM_A_IMGB_DIMS', '寸法');
DEFINE('_COM_A_IMGB_CONFIRM', 'このファイルを本当に削除してもよろしいですか？\n ファイルを削除すると、このファイルを参照している投稿の障害になります...');
DEFINE('_COM_A_IMGB_VIEW', '投稿を開く (編集)');
DEFINE('_COM_A_IMGB_NO_POST', '参照投稿なし！');
DEFINE('_USER_CHANGE_VIEW', 'これらの設定変更は論壇への次回訪問時に有効になります。<br />ビュータイプを「Mid-Flight」に変更する場合は、論壇メニューバーを使用することができます。');
DEFINE('_MOSBOT_DISCUSS_A', '論壇でこの記事について議論する (');
DEFINE('_MOSBOT_DISCUSS_B', ' 投稿)');
DEFINE('_POST_DISCUSS', 'This thread discusses the Content article');
DEFINE('_COM_A_RSS', 'RSSフィードを有効');
DEFINE('_COM_A_RSS_DESC', 'RSSフィードはRSSリーダアプリケーションへ最新の投稿をダウンロードする事を可能にします。(例として<a href="http://www.rssreader.com" target="_blank">rssreader.com</a>をご覧下さい。');
DEFINE('_LISTCAT_RSS', '最新の投稿をあなたのデスクトップへ');
DEFINE('_SEARCH_REDIRECT', 'あなたの検索リクエストを実行する前に、Kunenaはあなたのアクセス権を確立する必要があります。\n心配しないで下さい。これは30分以上何もしなかった後、またはログインし直した後なら正常です。\n検索リクエストを再度送信して下さい');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunena 設定');
DEFINE('_COM_A_DISPLAY', '表示 #');
DEFINE('_COM_A_CURRENT_SETTINGS', '現在の設定');
DEFINE('_COM_A_EXPLANATION', '説明');
DEFINE('_COM_A_BOARD_TITLE', 'ボード表題');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'あなたのボード名');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'デフォルトビュータイプ');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'デフォルトのビュータイプを「スレッド」もしくは「フラット」から選択して下さい');
DEFINE('_COM_A_THREADS', 'ページあたりのスレッド');
DEFINE('_COM_A_THREADS_DESC', '1ページあたりに表示するスレッド数です');
DEFINE('_COM_A_REGISTERED_ONLY', '登録利用者のみ');
DEFINE('_COM_A_REG_ONLY_DESC', '登録利用者だけが論壇を使用する事を許可するなら「はい」を設定して下さい（閲覧と投稿）。「いいえ」を設定すると、訪問者誰でも論壇を使用する事を許可します。');
DEFINE('_COM_A_PUBWRITE', '一般利用者 閲覧/書き込み');
DEFINE('_COM_A_PUBWRITE_DESC', '「はい」を設定すると、一般利用者に書き込みを許可します。「いいえ」を設定すると一般利用者に投稿を閲覧する事を許可しますが、登録利用者のみ投稿することができます。');
DEFINE('_COM_A_USER_EDIT', '利用者編集');
DEFINE('_COM_A_USER_EDIT_DESC', '「はい」を設定した場合、登録利用者に自身の投稿を編集することを許可します。');
DEFINE('_COM_A_MESSAGE', '上記の値の変更を保存するために、トップにある「保存」ボタンを押してください。');
DEFINE('_COM_A_HISTORY', '履歴を表示');
DEFINE('_COM_A_HISTORY_DESC', '返信/引用の作成時にトピックの履歴を表示するなら「はい」を設定して下さい。');
DEFINE('_COM_A_SUBSCRIPTIONS', '購読を許可');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', '「はい」に設定すると、トピックを購読し新しい投稿についてEメール通知を受信する事を登録利用者に許可します。');
DEFINE('_COM_A_HISTLIM', '履歴の制限');
DEFINE('_COM_A_HISTLIM_DESC', '履歴に表示する投稿数です');
DEFINE('_COM_A_FLOOD', '大量投稿保護');
DEFINE('_COM_A_FLOOD_DESC', '2回連続投稿の間に利用者が待機する秒数の合計です。0（ゼロ）を設定すると大量投稿保護は無効になります。注意: 大量投稿保護はパフォーマンス低下の原因となる可能性があります..');
DEFINE('_COM_A_MODERATION', 'モデレータにEメール');
DEFINE('_COM_A_MODERATION_DESC',
    '新規投稿について論壇モデレータにEメールで通知を送る場合「はい」を設定します。注意: 全ての（スーパー）管理者は自動的に全てのモデレータ権限を持ちますが、Eメールを受信するため論壇に対して明示的にモデレータを割り当ててください！');
DEFINE('_COM_A_SHOWMAIL', 'Eメールを表示');
DEFINE('_COM_A_SHOWMAIL_DESC', '利用者のEメールアドレスを表示しない場合は「いいえ」を設定します。（登録利用者でも表示しない）');
DEFINE('_COM_A_AVATAR', 'アバターを許可');
DEFINE('_COM_A_AVATAR_DESC', '登録利用者がアバターを持つ事を許可するなら「はい」を設定します。(アバターはプロファイルで管理可能）');
DEFINE('_COM_A_AVHEIGHT', '最大アバター高さ');
DEFINE('_COM_A_AVWIDTH', '最大アバター幅');
DEFINE('_COM_A_AVSIZE', '最大アバターファイルサイズ<br/><em>（キロバイト）</em>');
DEFINE('_COM_A_USERSTATS', '利用者統計を表示');
DEFINE('_COM_A_USERSTATS_DESC', '利用者の投稿数や利用者タイプ（管理者、モデレータ、登録利用者ほか）などの利用者統計情報を表示するなら「はい」を選択して下さい。 ');
DEFINE('_COM_A_AVATARUPLOAD', 'アバターのアップロードを許可');
DEFINE('_COM_A_AVATARUPLOAD_DESC', '登録利用者にアバターのアップロードを許可するなら「はい」を設定します。');
DEFINE('_COM_A_AVATARGALLERY', 'アバターギャラリーを使用');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'あなたが提供するギャラリー(components/com_kunena/avatars/gallery)から、登録利用者がアバターを選択できるようにするには「はい」を設定します。');
DEFINE('_COM_A_RANKING_DESC', '登録利用者の投稿数に基づいたランクを表示する場合は「はい」を設定します。<br/><strong>ランクを表示するには拡張タブ内の利用者統計も有効にする必要がある事に注意してください。</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'ランクイメージを使用');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    '登録利用者のランクをイメージ(from components/com_kunena/ranks)で表示するなら「はい」を設定します。「いいえ」を設定するとランクをテキストで表示します。ランキングイメージについての詳しい情報は、www.kunena.comのドキュメントを確認して下さい。');

//email and stuff
$_COM_A_NOTIFICATION = "新規投稿の通知: ";
$_COM_A_NOTIFICATION1 = "次の論壇であなたが購読しているトピックに新しい投稿がありました:";
$_COM_A_NOTIFICATION2 = "サイトにログイン後、論壇ホームページ上の「プロファイル」リンクから購読を管理することができます。またプロファイルからトピックの購読解除も可能です。";
$_COM_A_NOTIFICATION3 = "この通知メールに返信しないで下さい。";
$_COM_A_NOT_MOD1 = "あなたがモデレータとして割り当てられた次の論壇に新しい投稿がありました: ";
$_COM_A_NOT_MOD2 = "サイトへログイン後ごらん下さい。";
DEFINE('_COM_A_NO', 'いいえ');
DEFINE('_COM_A_YES', 'はい');
DEFINE('_COM_A_FLAT', 'フラット');
DEFINE('_COM_A_THREADED', 'スレッド');
DEFINE('_COM_A_MESSAGES', 'ページあたりのメッセージ');
DEFINE('_COM_A_MESSAGES_DESC', '1ページあたりに表示するメッセージ数');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', '利用者名');
DEFINE('_COM_A_USERNAME_DESC', '利用者の本名の替わりに利用者名を使用するなら「はい」を設定します。');
DEFINE('_COM_A_CHANGENAME', '名前の変更を許可');
DEFINE('_COM_A_CHANGENAME_DESC', '投稿時に登録利用者が名前を変更できるようにするなら「はい」を設定します。「いいえ」を設定すると、登録利用者は名前を変更する事はできません。');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', '論壇オフライン');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', '論壇セクションをオフラインにするなら「はい」を設定します。サイト管理者は閲覧可能なままです。');
DEFINE('_COM_A_BOARD_OFFLINE_MES', '論壇オフラインメッセージ');
DEFINE('_COM_A_PRUNE', '論壇除去');
DEFINE('_COM_A_PRUNE_NAME', '除去する論壇:');
DEFINE('_COM_A_PRUNE_DESC',
    '論壇除去機能は、指定した日数の間、新しい投稿がない余分なスレッドを取り除く事ができます。これはスティッキービットが設定された、もしくは明示的にロックされたトピックは削除しません。これらは手動で削除する必要があります。ロックされた論壇内のスレッドは除去できません。');
DEFINE('_COM_A_PRUNE_NOPOSTS', '過去に投稿が存在しないスレッドを削除して下さい。 ');
DEFINE('_COM_A_PRUNE_DAYS', '日');
DEFINE('_COM_A_PRUNE_USERS', '利用者除去');
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'この機能はJoomla!サイト利用者リストに反したKunena 利用者リストを除去する事ができます。Joomla!フレームワークから削除されたKunena 利用者の全てのプロファイルを削除します。<br/>本当に続けるなら、上記メニューバー内の「除去開始」をクリックして下さい。');
//general
DEFINE('_GEN_ACTION', 'アクション');
DEFINE('_GEN_AUTHOR', '作者');
DEFINE('_GEN_BY', 'by');
DEFINE('_GEN_CANCEL', 'キャンセル');
DEFINE('_GEN_CONTINUE', '続ける');
DEFINE('_GEN_DATE', '日付');
DEFINE('_GEN_DELETE', '削除');
DEFINE('_GEN_EDIT', '編集');
DEFINE('_GEN_EMAIL', 'Eメール');
DEFINE('_GEN_EMOTICONS', '顔文字');
DEFINE('_GEN_FLAT', 'フラット');
DEFINE('_GEN_FLAT_VIEW', 'フラットビュー');
DEFINE('_GEN_FORUMLIST', '論壇リスト');
DEFINE('_GEN_FORUM', '論壇');
DEFINE('_GEN_HELP', 'ヘルプ');
DEFINE('_GEN_HITS', 'ビュー');
DEFINE('_GEN_LAST_POST', '最新投稿');
DEFINE('_GEN_LATEST_POSTS', '最新の投稿を表示');
DEFINE('_GEN_LOCK', 'ロック');
DEFINE('_GEN_UNLOCK', 'ロック解除');
DEFINE('_GEN_LOCKED_FORUM', 'ロックされた論壇');
DEFINE('_GEN_LOCKED_TOPIC', 'ロックされたトピック');
DEFINE('_GEN_MESSAGE', 'メッセージ');
DEFINE('_GEN_MODERATED', 'モデレートされた論壇。公開前にレビューされます。');
DEFINE('_GEN_MODERATORS', 'モデレータ');
DEFINE('_GEN_MOVE', '移動');
DEFINE('_GEN_NAME', '名前');
DEFINE('_GEN_POST_NEW_TOPIC', '新規トピックを投稿');
DEFINE('_GEN_POST_REPLY', '返信を投稿');
DEFINE('_GEN_MYPROFILE', 'プロファイル');
DEFINE('_GEN_QUOTE', '引用');
DEFINE('_GEN_REPLY', '返信');
DEFINE('_GEN_REPLIES', '返信');
DEFINE('_GEN_THREADED', 'スレッド');
DEFINE('_GEN_THREADED_VIEW', 'スレッドビュー');
DEFINE('_GEN_SIGNATURE', '署名');
DEFINE('_GEN_ISSTICKY', 'トピックはスティッキー');
DEFINE('_GEN_STICKY', 'スティッキー');
DEFINE('_GEN_UNSTICKY', 'スティッキー解除');
DEFINE('_GEN_SUBJECT', '題名');
DEFINE('_GEN_SUBMIT', '送信');
DEFINE('_GEN_TOPIC', 'トピック');
DEFINE('_GEN_TOPICS', 'トピック');
DEFINE('_GEN_TOPIC_ICON', 'トピックアイコン');
DEFINE('_GEN_SEARCH_BOX', '論壇を検索');
$_GEN_THREADED_VIEW = "スレッドビュー";
$_GEN_FLAT_VIEW = "フラットビュー";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'アップロード');
DEFINE('_UPLOAD_DIMENSIONS', 'あなたのイメージファイルは最大値を超えている可能性があります。(幅 x 高さ - サイズ)');
DEFINE('_UPLOAD_SUBMIT', 'アップロードするため新しいアバターを送信してください');
DEFINE('_UPLOAD_SELECT_FILE', 'ファイルを選択');
DEFINE('_UPLOAD_ERROR_TYPE', 'jpeg、gif、pngイメージのみ使用してください。');
DEFINE('_UPLOAD_ERROR_EMPTY', 'アップロード前にファイルを選択して下さい。');
DEFINE('_UPLOAD_ERROR_NAME', 'イメージファイル名は、スペース無しの英数字だけを含む必要があります。');
DEFINE('_UPLOAD_ERROR_SIZE', 'イメージファイルサイズが管理者によって設定された最大値を超えました。');
DEFINE('_UPLOAD_ERROR_WIDTH', 'イメージファイルの幅が管理者によって設定された最大値を超えました。');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'イメージファイルの高さが管理者によって設定された最大値を超えました。');
DEFINE('_UPLOAD_ERROR_CHOOSE', "ギャラリーからアバターを選択していません...");
DEFINE('_UPLOAD_UPLOADED', 'あなたのアバターはアップロードされました。');
DEFINE('_UPLOAD_GALLERY', 'アバターギャラリーから1つ選択して下さい:');
DEFINE('_UPLOAD_CHOOSE', '選択を確認');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'An administrator should create them first from the ');
DEFINE('_LISTCAT_DO', 'They will know what to do ');
DEFINE('_LISTCAT_INFORM', 'Inform them and tell them to hurry up!');
DEFINE('_LISTCAT_NO_CATS', '論壇内にまだ大分類がありません。');
DEFINE('_LISTCAT_PANEL', 'Joomla!CMSの管理パネル');
DEFINE('_LISTCAT_PENDING', '未解決のメッセージ');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'この論壇に未解決メッセージはありません。');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'あなたはメッセージを削除しようとしています。');
DEFINE('_POST_ABOUT_DELETE', '<strong>注意:</strong><br/>
-論壇のトピック（スレッドの最初の投稿）を削除する場合、全ての子も同様に削除されます！
..コンテンツだけを削除する場合、投稿と投稿者名が空白になる事をよくお考えください..
<br/>
- 削除した投稿の全ての子は、スレッド階層で1ランクアップします。');
DEFINE('_POST_CLICK', 'ここをクリックして下さい');
DEFINE('_POST_ERROR', '利用者名/Eメールを見つける事ができませんでした。深刻なデータベースエラーはリストされませんでした。');
DEFINE('_POST_ERROR_MESSAGE', '不明なSQLエラーが発生し、あなたのメッセージは投稿されませんでした。  問題が続く場合、管理者へ連絡してください。');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'エラーが発生しメッセージは更新されませんでした。再度試してみて下さい。  このエラーが続く場合、管理者へ連絡してください。');
DEFINE('_POST_ERROR_TOPIC', '削除中にエラーが発生しました。下記のエラーを確認してください:');
DEFINE('_POST_FORGOT_NAME', 'あなたは名前を含める事を忘れました。  ブラウザの戻るボタンで前に戻り、再度試してください。');
DEFINE('_POST_FORGOT_SUBJECT', 'あなたは題名を含める事を忘れました。  ブラウザの戻るボタンで前に戻り、再度試してください。');
DEFINE('_POST_FORGOT_MESSAGE', 'あなたはメッセージを含める事を忘れました。  ブラウザの戻るボタンで前に戻り、再度試してください。');
DEFINE('_POST_INVALID', '不正な投稿IDが要求されました。');
DEFINE('_POST_LOCK_SET', 'トピックはロックされています。');
DEFINE('_POST_LOCK_NOT_SET', 'トピックはロックできませんでした。');
DEFINE('_POST_LOCK_UNSET', 'トピックのロックは解除されています。');
DEFINE('_POST_LOCK_NOT_UNSET', 'トピックのロックを解除できませんでした。');
DEFINE('_POST_MESSAGE', '新しいメッセージを投稿');
DEFINE('_POST_MOVE_TOPIC', 'このトピックを論壇へ移動 ');
DEFINE('_POST_NEW', '新しいメッセージを投稿: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'このトピックへの購読は処理することができませんでした。');
DEFINE('_POST_NOTIFIED', 'このトピックへの返信を通知するには、このボックスをチェックして下さい。');
DEFINE('_POST_STICKY_SET', 'このトピックにスティッキービットが設定されました。');
DEFINE('_POST_STICKY_NOT_SET', 'このトピックのスティッキービットは設定できませんでした。');
DEFINE('_POST_STICKY_UNSET', 'このトピックのスティッキービットは解除されました。');
DEFINE('_POST_STICKY_NOT_UNSET', 'このトピックのスティッキービットは解除できませんでした。');
//
DEFINE('_POST_SUBSCRIBE', '購読');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'あなたはこのトピックを購読しました');
DEFINE('_POST_SUCCESS', 'あなたのメッセージは正常に処理されました');
DEFINE('_POST_SUCCES_REVIEW', 'メッセージは正常に投稿されました。論壇で公開される前にモデレータによってレビューされます。');
DEFINE('_POST_SUCCESS_REQUEST', '要求は処理されました。すぐにトピックへ戻らない場合、');
DEFINE('_POST_TOPIC_HISTORY', 'トピックの履歴');
DEFINE('_POST_TOPIC_HISTORY_MAX', '最近の投稿の最大を表示しています');
DEFINE('_POST_TOPIC_HISTORY_LAST', '  -  <i>(最近の投稿を最初に表示)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'あなたのトピックは移動できませんでした。トピックへ戻るために:');
DEFINE('_POST_TOPIC_FLOOD1', 'この論壇の管理者は大量投稿保護機能を有効にしています。投稿前に');
DEFINE('_POST_TOPIC_FLOOD2', ' 秒待つ必要があります。');
DEFINE('_POST_TOPIC_FLOOD3', '論壇へ戻るためにブラウザの戻るボタンをクリックしてください。');
DEFINE('_POST_EMAIL_NEVER', 'あなたのEメールアドレスはサイト上に表示しません。');
DEFINE('_POST_EMAIL_REGISTERED', 'あなたのEメールアドレスは登録利用者だけ利用可能です。');
DEFINE('_POST_LOCKED', '管理者によってロックされました。');
DEFINE('_POST_NO_NEW', '新規返信は許可されていません。');
DEFINE('_POST_NO_PUBACCESS1', '管理者は一般への書き込みアクセスを無効にしています。');
DEFINE('_POST_NO_PUBACCESS2', '登録しログインした利用者だけが<br />論壇に投稿する事ができます。');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> この論壇内にはまだトピックがありません <<');
DEFINE('_SHOWCAT_PENDING', '未解決メッセージ');
// userprofile.php
DEFINE('_USER_DELETE', ' 署名を削除するにはこのボックスをチェックしてください');
DEFINE('_USER_ERROR_A', 'エラーによってこのページに来ました。Please inform the administrator on which links ');
DEFINE('_USER_ERROR_B', 'you clicked that got you here. She or he can then file a bug report.');
DEFINE('_USER_ERROR_C', 'ありがとうございます！');
DEFINE('_USER_ERROR_D', 'レポート内に含むエラー番号: ');
DEFINE('_USER_GENERAL', '全般プロファイルオプション');
DEFINE('_USER_MODERATOR', 'あなたは論壇の世話役に任命されました');
DEFINE('_USER_MODERATOR_NONE', 'あなたに割り当てられた論壇はありません');
DEFINE('_USER_MODERATOR_ADMIN', '管理者は全ての論壇の世話役です');
DEFINE('_USER_NOSUBSCRIPTIONS', 'あなたの購読はありません');
//DEFINE('_USER_PREFERED', 'Prefered Viewtype');
//DEFINE('_USER_PREFERED', '好みのビュータイプ');
DEFINE('_USER_PROFILE', 'プロファイル ');
DEFINE('_USER_PROFILE_NOT_A', 'Your profile could ');
DEFINE('_USER_PROFILE_NOT_B', 'not');
DEFINE('_USER_PROFILE_NOT_C', ' 更新されました。');
DEFINE('_USER_PROFILE_UPDATED', 'あなたのプロファイルは更新されました。');
DEFINE('_USER_RETURN_A', 'すぐにあなたのプロファイルへ戻らない場合、 ');
DEFINE('_USER_RETURN_B', 'ここをクリックして下さい');
DEFINE('_USER_SUBSCRIPTIONS', 'あなたの購読');
DEFINE('_USER_UNSUBSCRIBE', '購読解除');
DEFINE('_USER_UNSUBSCRIBE_A', 'You could ');
DEFINE('_USER_UNSUBSCRIBE_B', 'not');
DEFINE('_USER_UNSUBSCRIBE_C', ' be unsubscribed from the topic.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'トピックから購読を解除されました。');
DEFINE('_USER_DELETEAV', ' アバターを削除するにはこのボックスにチェックして下さい');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', '好みのメッセージ並び順');
DEFINE('_USER_ORDER_DESC', '新しい投稿順');
DEFINE('_USER_ORDER_ASC', '古い投稿順');
// view.php
DEFINE('_VIEW_DISABLED', '管理者は一般書き込みアクセスを無効にしています。');
DEFINE('_VIEW_POSTED', '投稿者');
DEFINE('_VIEW_SUBSCRIBE', ':: このトピックの購読 ::');
DEFINE('_MODERATION_INVALID_ID', '不正な投稿IDが要求されました。');
DEFINE('_VIEW_NO_POSTS', 'この論壇に投稿はありません。');
DEFINE('_VIEW_VISITOR', '訪問者');
DEFINE('_VIEW_ADMIN', '管理者');
DEFINE('_VIEW_USER', '利用者');
DEFINE('_VIEW_MODERATOR', '世話役');
DEFINE('_VIEW_REPLY', 'このメッセージに返信');
DEFINE('_VIEW_EDIT', 'このメッセージを編集');
DEFINE('_VIEW_QUOTE', '新しい投稿内でこのメッセージを引用');
DEFINE('_VIEW_DELETE', 'このメッセージを削除');
DEFINE('_VIEW_STICKY', 'このトピックをスティッキーに設定');
DEFINE('_VIEW_UNSTICKY', 'このトピックをスティッキー解除');
DEFINE('_VIEW_LOCK', 'このトピックをロック');
DEFINE('_VIEW_UNLOCK', 'このトピックをロック解除');
DEFINE('_VIEW_MOVE', 'この論壇を他の論壇へ移動');
DEFINE('_VIEW_SUBSCRIBETXT', 'このトピックを購読し新規投稿をメールで通知');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', '論壇');
DEFINE('_POSTS', '投稿:');
DEFINE('_TOPIC_NOT_ALLOWED', '投稿');
DEFINE('_FORUM_NOT_ALLOWED', '論壇');
DEFINE('_FORUM_IS_OFFLINE', '論壇はオフラインです！');
DEFINE('_PAGE', 'ページ: ');
DEFINE('_NO_POSTS', '投稿なし');
DEFINE('_CHARS', '最大文字数。');
DEFINE('_HTML_YES', 'HTMLは無効です。');
DEFINE('_YOUR_AVATAR', '<b>あなたのアバター</b>');
DEFINE('_NON_SELECTED', '選択されていません<br>');
DEFINE('_SET_NEW_AVATAR', '新しいアバターを選択');
DEFINE('_THREAD_UNSUBSCRIBE', '購読解除');
DEFINE('_SHOW_LAST_POSTS', '次の時間内に活動的なトピック');
DEFINE('_SHOW_HOURS', '時間');
DEFINE('_SHOW_POSTS', '合計: ');
DEFINE('_DESCRIPTION_POSTS', '活動的なトピックの新規投稿が表示されています');
DEFINE('_SHOW_4_HOURS', '4 時間');
DEFINE('_SHOW_8_HOURS', '8 時間');
DEFINE('_SHOW_12_HOURS', '12 時間');
DEFINE('_SHOW_24_HOURS', '24 時間');
DEFINE('_SHOW_48_HOURS', '48 時間');
DEFINE('_SHOW_WEEK', '週');
DEFINE('_POSTED_AT', '投稿日時');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'あなたが選択した時間帯に新規投稿はありません。');
DEFINE('_MESSAGE', 'メッセージ');
DEFINE('_NO_SMILIE', 'no');
DEFINE('_FORUM_UNAUTHORIZIED', 'この論壇は登録しログインした利用者にのみ開かれています。');
DEFINE('_FORUM_UNAUTHORIZIED2', '既に登録済みなら始めにログイン下さい。');
DEFINE('_MESSAGE_ADMINISTRATION', 'モデレート');
DEFINE('_MOD_APPROVE', '承認');
DEFINE('_MOD_DELETE', '削除');
//NEW in RC1
DEFINE('_SHOW_LAST', '最近のメッセージを表示');
DEFINE('_POST_WROTE', 'は書きました');
DEFINE('_COM_A_EMAIL', 'ボードEメールアドレス');
DEFINE('_COM_A_EMAIL_DESC', 'ボードEメールアドレスです。正当なメールアドレスを入力して下さい。');
DEFINE('_COM_A_WRAP', 'ワードラップ長さ');
DEFINE('_COM_A_WRAP_DESC',
    '単語が持つ文字数の最大値を入力して下さい。この機能はKunenaの投稿をテンプレートへ出力する際に調整できます。<br/> 固定幅テンプレートの最大値はおそらく70文字ですが、少し実験する必要があるかもしれません。<br/>URLはどれほど長くてもワードラップに影響されません。');
DEFINE('_COM_A_SIGNATURE', '最大署名長さ');
DEFINE('_COM_A_SIGNATURE_DESC', '利用者署名で利用可能な最大文字数');
DEFINE('_SHOWCAT_NOPENDING', '未解決メッセージはありません');
DEFINE('_COM_A_BOARD_OFSET', 'ボードタイムオフセット');
DEFINE('_COM_A_BOARD_OFSET_DESC', '利用者とは異なったタイムゾーンのサーバに設置されたボードもあります。投稿時間として使用するタイムオフセットを時間で設定して下さい。正負の数字が使用できます。');
//New in RC2
DEFINE('_COM_A_BASICS', '基本');
DEFINE('_COM_A_FRONTEND', 'フロントエンド');
DEFINE('_COM_A_SECURITY', 'セキュリティ');
DEFINE('_COM_A_AVATARS', 'アバター');
DEFINE('_COM_A_INTEGRATION', '統合');
DEFINE('_COM_A_PMS', 'プライベートメッセージを有効');
DEFINE('_COM_A_PMS_DESC',
    'あなたがインストールした適当なプライベートメッセージコンポーネントを選択して下さい。Clexus PMを選択すると、ClexusPM利用者プロファイルに関連したオプション（例えばICQ, AIM, Yahoo, MSN とプロファイルリンク、使用したKunenaテンプレートでサポートされている場合）も有効になります。');
DEFINE('_VIEW_PMS', 'この利用者へプライベートメッセージを送るにはここをクリックして下さい');
//new in RC3
DEFINE('_POST_RE', 'Re:');
DEFINE('_BBCODE_BOLD', '太字: [b]テキスト[/b] ');
DEFINE('_BBCODE_ITALIC', '斜体: [i]テキスト[/i]');
DEFINE('_BBCODE_UNDERL', '下線: [u]テキスト[/u]');
DEFINE('_BBCODE_QUOTE', '引用: [quote]テキスト[/quote]');
DEFINE('_BBCODE_CODE', 'コード表示: [code]コード[/code]');
DEFINE('_BBCODE_ULIST', '順不同リスト: [ul] [li]テキスト[/li] [/ul] - ヒント: リストはリストアイテムを含む必要があります');
DEFINE('_BBCODE_OLIST', '序列リスト: [ol] [li]テキスト[/li] [/ol] - ヒント: リストはリストアイテムを含む必要があります');
DEFINE('_BBCODE_IMAGE', 'イメージ: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'リンク: [url=http://www.zzz.com/]これはリンクです[/url]');
DEFINE('_BBCODE_CLOSA', '全てのタグを閉じる');
DEFINE('_BBCODE_CLOSE', '全てのbbCodeタグを閉じる');
DEFINE('_BBCODE_COLOR', 'カラー: [color=#FF6600]テキスト[/color]');
DEFINE('_BBCODE_SIZE', 'サイズ: [size=1]テキストサイズ[/size] - ヒント: サイズの範囲は1から5の間です');
DEFINE('_BBCODE_LITEM', 'リストアイテム: [li]リストアイテム[/li]');
DEFINE('_BBCODE_HINT', 'bbCodeヘルプ - ヒント: bbCodeは選択したテキスト上で使用できます！');
DEFINE('_COM_A_TAWIDTH', 'テキストエリアの幅');
DEFINE('_COM_A_TAWIDTH_DESC', 'テンプレートに一致するよう、返信/投稿テキストを登録するエリアを調整してください。<br/>トピック顔文字ツールバーは、幅が420px以下だった場合2行にまたがって表示されます。');
DEFINE('_COM_A_TAHEIGHT', 'テキストエリアの高さ');
DEFINE('_COM_A_TAHEIGHT_DESC', 'テンプレートに一致するよう、返信/投稿テキストを登録するエリアを調整してください。');
DEFINE('_COM_A_ASK_EMAIL', 'Eメール必須');
DEFINE('_COM_A_ASK_EMAIL_DESC', '利用者もしくは訪問者の投稿時にEメールアドレスの入力を必須とします。「いいえ」を設定すると、この機能はフロントエンド上で省略され、投稿者にEメールアドレスを要求しません。');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'ランク管理');
define('_KUNENA_SORTRANKS', 'ランクで並び替え');

define('_KUNENA_RANKSIMAGE', 'ランクイメージ');
define('_KUNENA_RANKS', 'ランク名');
define('_KUNENA_RANKS_SPECIAL', 'スペシャル');
define('_KUNENA_RANKSMIN', '最小投稿数');
define('_KUNENA_RANKS_ACTION', '動作');
define('_KUNENA_NEW_RANK', '新規ランク');

?>
