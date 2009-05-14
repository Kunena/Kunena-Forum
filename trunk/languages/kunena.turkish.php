<?php
/**
* @version $Id: kunena.english.php 602 2009-04-03 20:13:16Z fxstein $
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
* Kunena Forum Turkish
* @author Ugur Uygur
* @link http://www.joomlatr.org
**/

// Dont allow direct linking
defined( '_JEXEC' ) or defined( '_JEXEC' ) or die('Sınırlı Erişim');

// 1.0.9
DEFINE('_KUNENA_INSTALLED_VERSION', 'Kurulu versiyon');
DEFINE('_KUNENA_COPYRIGHT', 'Teelif Hakkı');
DEFINE('_KUNENA_LICENSE', 'Lisans');
DEFINE('_KUNENA_PROFILE_NO_USER', 'Kullanıcı yok.');
DEFINE('_KUNENA_PROFILE_NOT_FOUND', 'Kullanıcının henüz profili yok ve forumu ziyaret etmemiştir.');

// Search
DEFINE('_KUNENA_SEARCH_RESULTS', 'Arama Sonuçları');
DEFINE('_KUNENA_SEARCH_ADVSEARCH', 'Gelişmiş Arama');
DEFINE('_KUNENA_SEARCH_SEARCHBY_KEYWORD', 'Anahtar kelimelere göre ara');
DEFINE('_KUNENA_SEARCH_KEYWORDS', 'Anahtar kelimeler');
DEFINE('_KUNENA_SEARCH_SEARCH_POSTS', 'Tüm mesajlarda ara');
DEFINE('_KUNENA_SEARCH_SEARCH_TITLES', 'Sadece başlıkları ara');
DEFINE('_KUNENA_SEARCH_SEARCHBY_USER', 'Kullanıcı adına göre ara');
DEFINE('_KUNENA_SEARCH_UNAME', 'Kullanıcı Adı');
DEFINE('_KUNENA_SEARCH_EXACT', 'Tam İsim');
DEFINE('_KUNENA_SEARCH_USER_POSTED', 'Tarafından yazılan mesajlar');
DEFINE('_KUNENA_SEARCH_USER_STARTED', 'Tarafından başlatılan konular');
DEFINE('_KUNENA_SEARCH_USER_ACTIVE', 'Konulardaki aktivite');
DEFINE('_KUNENA_SEARCH_OPTIONS', 'Arama seçenekleri');
DEFINE('_KUNENA_SEARCH_FIND_WITH', 'İle konuları bul');
DEFINE('_KUNENA_SEARCH_LEAST', 'En azından');
DEFINE('_KUNENA_SEARCH_MOST', 'En fazla');
DEFINE('_KUNENA_SEARCH_ANSWERS', 'Cevaplar');
DEFINE('_KUNENA_SEARCH_FIND_POSTS', 'Ait olan mesajları bul');
DEFINE('_KUNENA_SEARCH_DATE_ANY', 'Tüm zaman');
DEFINE('_KUNENA_SEARCH_DATE_LASTVISIT', 'Son ziyaret');
DEFINE('_KUNENA_SEARCH_DATE_YESTERDAY', 'Dün');
DEFINE('_KUNENA_SEARCH_DATE_WEEK', 'Bir hafta önce');
DEFINE('_KUNENA_SEARCH_DATE_2WEEKS', '2 hafta önce');
DEFINE('_KUNENA_SEARCH_DATE_MONTH', 'Bir ay önce');
DEFINE('_KUNENA_SEARCH_DATE_3MONTHS', '3 ay önce');
DEFINE('_KUNENA_SEARCH_DATE_6MONTHS', '6 ay önce');
DEFINE('_KUNENA_SEARCH_DATE_YEAR', 'Bir yıl önce');
DEFINE('_KUNENA_SEARCH_DATE_NEWER', 'Ve asla');
DEFINE('_KUNENA_SEARCH_DATE_OLDER', 'Ve daha eski');
DEFINE('_KUNENA_SEARCH_SORTBY', 'Sonuçlar göre sırala');
DEFINE('_KUNENA_SEARCH_SORTBY_TITLE', 'Başlık');
DEFINE('_KUNENA_SEARCH_SORTBY_POSTS', 'Mesaj sayısı');
DEFINE('_KUNENA_SEARCH_SORTBY_VIEWS', 'Gösterim sayısı');
DEFINE('_KUNENA_SEARCH_SORTBY_START', 'Konu başlama tarihi');
DEFINE('_KUNENA_SEARCH_SORTBY_POST', 'Mesaj tarihi');
DEFINE('_KUNENA_SEARCH_SORTBY_USER', 'Kullanıcı adı');
DEFINE('_KUNENA_SEARCH_SORTBY_FORUM', 'Forum');
DEFINE('_KUNENA_SEARCH_SORTBY_INC', 'Artan sıra');
DEFINE('_KUNENA_SEARCH_SORTBY_DEC', 'Azalan sıra');
DEFINE('_KUNENA_SEARCH_START', 'Sonuç sayısına atla');
DEFINE('_KUNENA_SEARCH_LIMIT5', '5 arama sonucunu göster');
DEFINE('_KUNENA_SEARCH_LIMIT10', '10 arama sonucunu göster');
DEFINE('_KUNENA_SEARCH_LIMIT15', '15 arama sonucunu göster');
DEFINE('_KUNENA_SEARCH_LIMIT20', '20 arama sonucunu göster');
DEFINE('_KUNENA_SEARCH_SEARCHIN', 'Kategorilerde ara');
DEFINE('_KUNENA_SEARCH_SEARCHIN_ALLCATS', 'Tüm kategorilerde ara');
DEFINE('_KUNENA_SEARCH_SEARCHIN_CHILDREN', 'Alt forumlarda da ara');
DEFINE('_KUNENA_SEARCH_SEND', 'Gönder');
DEFINE('_KUNENA_SEARCH_CANCEL', 'İptal');
DEFINE('_KUNENA_SEARCH_ERR_NOPOSTS', 'Arama kriterlerinizi içeren mesaj bulunmamaktadır.');
DEFINE('_KUNENA_SEARCH_ERR_SHORTKEYWORD', 'Bir anahtar kelime en az 3 karakterli olmalıdır!');

// 1.0.8
DEFINE('_KUNENA_CATID', 'ID');
DEFINE('_POST_NOT_MODERATOR', 'Yönetici izinleriniz yok!');
DEFINE('_POST_NO_FAVORITED_TOPIC', 'Bu konu favorilerinize <b>EKLENMEMİŞTİR</b>');
DEFINE('_COM_C_SYNCEUSERSDESC', 'Kunena kullanıcı tablosunu Joomla kullanıcı tablosu ile eşleştir');
DEFINE('_POST_FORGOT_EMAIL', 'Kendi eposta adresinizi unuttunuz.  Tarayıcınızın&#146s gei butonuna basarak tekrar deneyiniz.');
DEFINE('_KUNENA_POST_DEL_ERR_FILE', 'Her şey silindi some attachment files were missing!');
// New strings for initial forum setup. Replacement for legacy sample data
DEFINE('_KUNENA_SAMPLE_FORUM_MENU_TITLE', 'Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_TITLE', 'Ana Forum');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_DESC', 'Bu Ana Forum kategorisidir. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.');
DEFINE('_KUNENA_SAMPLE_MAIN_CATEGORY_HEADER', 'Üyeleriniz ve ziyaretçilerinize ek bilgi sağlamak için, kategori üzerinde metin gösterilmek üzere forum başlığı ayarlanabilir.');
DEFINE('_KUNENA_SAMPLE_FORUM1_TITLE', 'Hoşgeldin Ali');
DEFINE('_KUNENA_SAMPLE_FORUM1_DESC', 'Bu forum kategorisinde üyelerin kendilerinden bahsetmelerini istiyoruz. Diğerleri ile tanışmak ve kendinizi tanıtmak için.
');
DEFINE('_KUNENA_SAMPLE_FORUM1_HEADER', '[b]Kunena foruma hoşgeldiniz![/b]Bize ve üyelerimize kim olduğunuzu anlatın, Nelerden hoşlanırsınız ve bu foruma neden üye oldunuz.
Tüm üyelerimize hoşgeldiniz diyoruz ve sizi buralarda daha çok görmek istiyoruz!
');
DEFINE('_KUNENA_SAMPLE_FORUM2_TITLE', 'Öneri kutusu');
DEFINE('_KUNENA_SAMPLE_FORUM2_DESC', 'Geri bildirimde bulunmak ister misiniz?
Çekinmeyin ve bize not bırakın. Sizden daha fazla görüş almak isteriz.');
DEFINE('_KUNENA_SAMPLE_FORUM2_HEADER', 'Bu Öneri Kutusu için tercihi forum başlığıdfır.
');
DEFINE('_KUNENA_SAMPLE_POST1_SUBJECT', 'Kunena Hoşgeldiniz!');
DEFINE('_KUNENA_SAMPLE_POST1_TEXT', '[size=4][b]Kunenaya hoşgeldiniz![/b][/size]
Joomla topluluğunuz için Kunena Forumu tercih ettiğinizi çin teşekkürler.

Kunena, Swahili dilinden çevrilmiştir ve "konuşmak" demektir, is built by a team of open source professionals with the goal of providing a top-quality, tightly unified forum solution for Joomla. Kunena even supports social networking components like Community Builder and JomSocial.


[size=4][b]Ek Kunena kaynakları[/b][/size]

[b]Kunena Dokümantasyon:[/b] http://www.kunena.com/docs
(http://docs.kunena.com)

[b]Kunena Destek Forumu[/b]: http://www.kunena.com/forum
(http://www.kunena.com/index.php?option=com_kunena&Itemid=125)

[b]Kunena İndirmeler:[/b] [url=http://joomlacode.org/gf/project/kunena/frs/]http://www.kunena.com/downloads[/url]
(http://joomlacode.org/gf/project/kunena/frs/)

[b]Kunena Blog:[/b] http://www.kunena.com/blog
(http://www.kunena.com/index.php?option=com_content&view=section&layout=blog&id=7&Itemid=128)

[b]Fikirlerinizi ekleyin:[/b] [url=http://kunena.uservoice.com/pages/general?referer_type=top3]http://www.kunena.com/uservoice[/url]
(http://kunena.uservoice.com/pages/general?referer_type=top3)

[b]Follow Kunena on Twitter:[/b] [url=https://twitter.com/kunena]http://www.kunena.com/twitter[/url]
(https://twitter.com/kunena)');

// 1.0.6
DEFINE('_KUNENA_JOMSOCIAL', 'JomSocial');

// 1.0.5
DEFINE('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the Kunena code tag highlighting Javascript. If your members post PHP or other code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from becoming malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Varsayılan RSS tipi');
DEFINE('_COM_A_RSS_TYPE_DESC', 'RSS beslemelerini seçiniz &quot;Konuya göre &quot; or &quot;Mesaja göre.&quot; &quot;Konuya göre &quot; means that only one entry per thread will be listed in the RSS feed independent of how many posts have been made within that thread. &quot;By Thread&quot; creates a shorter, more compact RSS feed but will not list every reply.');
DEFINE('_COM_A_RSS_BY_THREAD', 'By Thread');
DEFINE('_COM_A_RSS_BY_POST', 'By Post');
DEFINE('_COM_A_RSS_HISTORY', 'RSS Tipi');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'RSS listesisnde konularınmı yoksa konuya yaızlan cevapların mı gösterileceğini buradan seçebilirsiniz.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', 'Son 1 haftada göndeirlen mesajlar');
DEFINE('_COM_A_RSS_HISTORY_MONTH', 'Son 1 ayda gönderilen mesajlar');
DEFINE('_COM_A_RSS_HISTORY_YEAR', 'Son 1 yılda gönderilen mesajlar');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Geçerli Ana Sayfa');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Foruma ilk girişte gösterilecek olan sayfayı seçiniz.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Son Yazılanlar');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'Mesajlarım');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Kategoriler');
DEFINE('_KUNENA_BBCODE_HIDE', 'Aşağıdakiler kayıtlı olmayan kullanıcılardan gizlenmiştir:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Uyarı: Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Ana forum aynısı olmalı.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Ana forum, alt forumlardan biri.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum kimliği bulunamadı.');
DEFINE('_KUNENA_RECURSION', 'Tekrarlama saptandı.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'Adınızı girmeyi unuttunuz.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'E-Posta adresinizi girmeyi unuttunuz.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'Başlık girmeyi unuttunuz.');
DEFINE('_KUNENA_EDIT_TITLE', 'Detaylarınızı Düzenleyin');
DEFINE('_KUNENA_YOUR_NAME', 'Adınız:');
DEFINE('_KUNENA_EMAIL', 'Eposta:');
DEFINE('_KUNENA_UNAME', 'Kullanıcı Adı:');
DEFINE('_KUNENA_PASS', 'Şifre:');
DEFINE('_KUNENA_VPASS', 'Şifreyi Teyit Edin:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'Kullanıcı detayları kaydedildi.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Ayarları');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Video etiketini editör araççubuğunda göster');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', '&quot;Evet&quot; olarak işaretlerseniz mesaj editörünün araççubuğunda [video] etiketi görünecektir.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Video etiketini editör araççubuğunda göster');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', '&quot;Evet&quot; olarak işaretlerseniz mesaj editörünün araççubuğunda [ebay] etiketi görünecektir.');
DEFINE('_COM_A_TRIMLONGURLS', 'Uzun URL leri kısalt');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Uzun URL lerin kısaltılmasını istiyorsanız &quot;Evet&quot; olarak seçin. URL kısaltmanın ön ve arka ayarlarına bakınız.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Front portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Önyüz için kısaltılacak URL lerin karakter sayısı. Uzun URL leri kısalt &quot;Evet&quot; olarak seçilmelidir.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Back portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Otomatik gömülü youtube videoları');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Youtube video URL lerinin otomatik olarak gömülmesini istiyorsanız &quot;Evet&quot; olarak seçiniz.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Otomatik gömülü ebay ögeleri');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay araç dil kodu');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Oturum süresi');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'varsayılan ayar 1800 [saniyedir]. Oturum ayarları Joomlanın oturum ayarlarına benzer. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and the NEW indicator are reset.');

// Advanced administrator merge-split functions
DEFINE('_GEN_MERGE', 'Birleştir');
DEFINE('_VIEW_MERGE', '');
DEFINE('_POST_MERGE_TOPIC', 'Bu konuyu şununla birleştir');
DEFINE('_POST_MERGE_GHOST', 'Konunun hayalet kopyasını bırak');
DEFINE('_POST_SUCCESS_MERGE', 'Konu başarıyla birleştirildi.');
DEFINE('_POST_TOPIC_NOT_MERGED', 'Birleştirme başarısız.');
DEFINE('_GEN_SPLIT', 'Ayır');
DEFINE('_GEN_DOSPLIT', 'Git');
DEFINE('_VIEW_SPLIT', '');
DEFINE('_POST_SUCCESS_SPLIT', 'Konu ayırma başarılı.');
DEFINE('_POST_SUCCESS_SPLIT_TOPIC_CHANGED', 'Başlık başarıyla değiştirildi.');
DEFINE('_POST_SPLIT_TOPIC_NOT_CHANGED', 'Başlık değiştirme başarısız.');
DEFINE('_POST_TOPIC_NOT_SPLIT', 'Ayırma başarısız.');
DEFINE('_POST_DUPLICATE_IGNORED', 'Tekrarlanmış mesaj yok sayıldı.');
DEFINE('_POST_SPLIT_HINT', '<br />İpucu: Eğer konu gönderim pozisyonunu ikinci sütun oalrak ayarlarsanız, göndeirlerin bölümleme yapılmadan yayınlanmasını sağlarsınız.<br />');
DEFINE('_POST_LINK_ORPHANS_TOPIC', 'sahipsizleri başlığa yönlendir');
DEFINE('_POST_LINK_ORPHANS_TOPIC_TITLE', 'Sahipsizleri yeni başlığa yönlendir.');
DEFINE('_POST_LINK_ORPHANS_PREVPOST', 'sahipsizleri önceki başlığa yönlendir');
DEFINE('_POST_LINK_ORPHANS_PREVPOST_TITLE', 'Sahipsizleri önceki başlığa yönlendir.');
DEFINE('_POST_MERGE', 'birleştir');
DEFINE('_POST_MERGE_TITLE', 'ters birleştir.');
DEFINE('_POST_INVERSE_MERGE', 'inverse merge');
DEFINE('_POST_INVERSE_MERGE_TITLE', 'Hedefin ilk yazısını bu başlığa birleştir.');

// Additional
DEFINE('_POST_UNFAVORITED_TOPIC', 'Bu konu favorilerinizden çıkarıldı.');
DEFINE('_POST_NO_UNFAVORITED_TOPIC', 'Bu konu favorilerinizden <b>ÇIKARILAMADI</b>');
DEFINE('_POST_SUCCESS_UNFAVORITE', 'Favorilerden çıkarılma başarılı.');
DEFINE('_POST_UNSUBSCRIBED_TOPIC', 'Bu konu sizin gönderimlerinizden çıkarıldı.');
DEFINE('_POST_NO_UNSUBSCRIBED_TOPIC', 'Bu konu gönderimlerinizden <b>ÇIKARILAMADI</b>');
DEFINE('_POST_SUCCESS_UNSUBSCRIBE', 'Gönderimlerden çıkarılma başarılı.');
DEFINE('_POST_NO_DEST_CATEGORY', 'Hedef kategori seçilmedi. Hiçbirşey taşınmadı.');
// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'Son Tatışmalar');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'Benim Tartışmalarım');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Benim başlattığım veya katıldığım tartışmalar');
DEFINE('_KUNENA_CATEGORY', 'Kategori:');
DEFINE('_KUNENA_CATEGORIES', 'Kategoriler');
DEFINE('_KUNENA_POSTED_AT', 'Gönderildi');
DEFINE('_KUNENA_AGO', 'önce');
DEFINE('_KUNENA_DISCUSSIONS', 'Tatışmalar');
DEFINE('_KUNENA_TOTAL_THREADS', 'Toplam Konu:');
DEFINE('_SHOW_DEFAULT', 'Varsayılan');
DEFINE('_SHOW_MONTH', 'Ay');
DEFINE('_SHOW_YEAR', 'Yılr');

// 1.0.4
DEFINE('_KUNENA_COPY_FILE', 'Kopyalama "%src%" to "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'TAMAM');
DEFINE('_KUNENA_CSS_SAVE', 'CSS dosyası bu dosyada olmalıdır...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Ek tabloları başarıyla son sürüm yapısına yükseltildi!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'İletilerdeki ek tabloları başarıyla son sürüm yapısına yükseltildi!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Gönderi sıradüzeninde, alt kategori yükseltilemedi. Hiçbirşey Silinmedi.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Gönderi(ler) silinemedi - Hiçbirşey silinemedi');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Gönderi metin(ler)i silinemedi. Veritabanını el ile güncelleyiniz (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Herşey silindi, ama kullanıcı gönderi istatistikleri güncellenemedi!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Önemli Veritabanı Hatası. Cevapların Yeni Forum İle Eşleşebilmesi İçin Veritabanını El İle Güncelleyiniz.");
DEFINE('_KUNENA_UNIST_SUCCESS', "Kunena bileşeni başarıyla kaldırıldı!");
DEFINE('_KUNENA_PDF_VERSION', 'Kunena Forum Bileşeni. Sürüm: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Oluşturulma: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'Aranacak forum yok.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Kullanıcı eklemede hata:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Kullanıcılar eşleştirildi; Silindi:');
DEFINE('_KUNENA_USERSSYNCADD', ', ekle:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'kullanıcı profilleri.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'Eşleştirme İçin Uygun Profil Bulunamadı.');
DEFINE('_KUNENA_SYNC_USERS', 'Kullanıcıları Eşleştir');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Kunena Kullanıcı Tablolarını, Joomla! Kullanıcı Tablolarıyla Eşleştir');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Eposta Yöneticileri');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Sistem yönetici(leri)nizin her yeni gönderide eposta ile bilgilendirilmesini istiyorsanız &quot;Evet&quot;i seçiniz.');
DEFINE('_KUNENA_RANKS_EDIT', 'Seviyeyi Düzenle');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Epostayı Gizle');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Pazar');
DEFINE('_KUNENA_DT_LDAY_MON', 'Pazartesi');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Salı');
DEFINE('_KUNENA_DT_LDAY_WED', 'Çarşamba');
DEFINE('_KUNENA_DT_LDAY_THU', 'Perşembe');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Cuma');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Cumartesi');
DEFINE('_KUNENA_DT_DAY_SUN', 'Pz');
DEFINE('_KUNENA_DT_DAY_MON', 'Pzt');
DEFINE('_KUNENA_DT_DAY_TUE', 'Sa');
DEFINE('_KUNENA_DT_DAY_WED', 'Çrş');
DEFINE('_KUNENA_DT_DAY_THU', 'Prş');
DEFINE('_KUNENA_DT_DAY_FRI', 'Cu');
DEFINE('_KUNENA_DT_DAY_SAT', 'Cts');
DEFINE('_KUNENA_DT_LMON_JAN', 'Ocak');
DEFINE('_KUNENA_DT_LMON_FEB', 'Şubat');
DEFINE('_KUNENA_DT_LMON_MAR', 'Mart');
DEFINE('_KUNENA_DT_LMON_APR', 'Nisan');
DEFINE('_KUNENA_DT_LMON_MAY', 'Mayıs');
DEFINE('_KUNENA_DT_LMON_JUN', 'Haziran');
DEFINE('_KUNENA_DT_LMON_JUL', 'Temmuz');
DEFINE('_KUNENA_DT_LMON_AUG', 'Ağustos');
DEFINE('_KUNENA_DT_LMON_SEP', 'Eylül');
DEFINE('_KUNENA_DT_LMON_OCT', 'Ekim');
DEFINE('_KUNENA_DT_LMON_NOV', 'Kasım');
DEFINE('_KUNENA_DT_LMON_DEV', 'Aralık');
DEFINE('_KUNENA_DT_MON_JAN', 'Oca');
DEFINE('_KUNENA_DT_MON_FEB', 'Şbt');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Nis');
DEFINE('_KUNENA_DT_MON_MAY', 'May');
DEFINE('_KUNENA_DT_MON_JUN', 'Haz');
DEFINE('_KUNENA_DT_MON_JUL', 'Tem');
DEFINE('_KUNENA_DT_MON_AUG', 'Ağu');
DEFINE('_KUNENA_DT_MON_SEP', 'Eyl');
DEFINE('_KUNENA_DT_MON_OCT', 'Eki');
DEFINE('_KUNENA_DT_MON_NOV', 'Kas');
DEFINE('_KUNENA_DT_MON_DEV', 'Ara');
DEFINE('_KUNENA_CHILD_BOARD', 'Alt Forum');
DEFINE('_WHO_ONLINE_GUEST', 'Ziyaretçi');
DEFINE('_WHO_ONLINE_MEMBER', 'Üye');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'hiçbiri');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Resim İşleyici:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Devam etmek  için tıklayınız...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Uygula!');
DEFINE('_KUNENA_NO_ACCESS', 'Foruma erişemezsiniz!');
DEFINE('_KUNENA_TIME_SINCE', '%time% önce');
DEFINE('_KUNENA_DATE_YEARS', 'Yıl');
DEFINE('_KUNENA_DATE_MONTHS', 'Ay');
DEFINE('_KUNENA_DATE_WEEKS','Hafta');
DEFINE('_KUNENA_DATE_DAYS', 'Gün');
DEFINE('_KUNENA_DATE_HOURS', 'Saat');
DEFINE('_KUNENA_DATE_MINUTES', 'Dakika');
// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forum Başlığı:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum Görünümü");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS Sınıfı Soneki");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS soneki; içerikleme, kategori gösterimi, gönderi izlemesi ve forum kategorisine göre farklı tasarımlar kullanabilmeniz için ayarlanabilir.");
DEFINE('_COM_A_USER_EDIT_TIME', 'Gönderi Düzenleme Süresi');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Gönderimden veya son değiştirmeden sonra yeniden düzenlemek için gereken süre. Süre sınırlaması getirmek istemiyorsanız 0 olarak ayarlayınız.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'Değişikliklerin Depolanmaya Başlayacağı Süre');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Varsayılan 600 [saniye], allows
storing Varsayılan 600 [sn], düzenleme bağlantısı kaybolduktan 600 sn sonra değişikliklerin depolanmasına izin verir');
DEFINE('_KUNENA_HELPPAGE','Yardım Sayfasını Etkinleştir');
DEFINE('_KUNENA_HELPPAGE_DESC','&quot;Evet&quot;i seçerseniz, Yardım sayfasına giden bir bağlantı görünür olacaktır.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Kunena İçinde Yardım Göster');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','&quot;Evet&quot;i seçerseniz, yardım içeriği Kunena içinde görüntülenecektir ve dış yardım sayfasına olan bağlantı çalışmayacaktır. <b>Not:</b> "Yardım İçeriği ID"sini Girmelisiniz.');
DEFINE('_KUNENA_HELPPAGE_CID','Yardım İçeriği ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','Kunena İçinde Yardım Göster",i <b>"EVET"</b> olarak ayarlamalısınız.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Dış Yardım Sayfası Bağlantısı');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','Dış yardım sayfası bağlantısını göstermek isterseniz, "Kunena İçinde Yardım Göster"i <b>"HAYIR"</b> olarak ayarlayınız.');
DEFINE('_KUNENA_RULESPAGE','Kurallar Sayfasını Etkinleştir');
DEFINE('_KUNENA_RULESPAGE_DESC','&quot;Evet&quot;i seçerseniz, Kurallar sayfasına giden bir bağlantı görünür olacaktır.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Kunena İçinde Kuralları Göster');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','&quot;Evet&quot;i seçerseniz, kurallar içeriği Kunena içinde görüntülenecektir ve dış kurallar sayfasına olan bağlantı çalışmayacaktır. <b>Not:</b> "Kurallar İçeriği ID"sini Girmelisiniz.');
DEFINE('_KUNENA_RULESPAGE_CID','Kurallar İçeriği ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','Kunena İçinde Kuralları Göster",i <b>"EVET"</b> olarak ayarlamalısınız.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Dış kurallar sayfası bağlantısı');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','Dış kurallar sayfası bağlantısını göstermek isterseniz, "Kunena İçinde Kuralları Göster"i <b>"HAYIR"</b> olarak ayarlayınız.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Kütüphanesi Bulunamadı');
DEFINE('_KUNENA_AVATAR_GD2IMAGE_NOT','GD2 Kütüphanesi Bulunamadı');
DEFINE('_KUNENA_GD_INSTALLED','GD Kütüphanesi Sürümü&#32;');
DEFINE('_KUNENA_GD_NO_VERSION','GD Kütüphanesi Sürümü Belirlenemedi');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD Kütüphanesi Yüklenmemiş, Daha Fazla Bilgi&#32;');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Küçük Boy Resim Yüksekliğ :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Küçük Boy Resim Genişliği :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Orta Boy Resim Yüksekliği :');
DEFINE('_KUNENA_AVATAR_MEDIUM_WIDTH','Orta Boy Resim Genişliği :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Büyük Boy Resim Yüksekliği :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Büyük Boy Resim Genişliği :');
DEFINE('_KUNENA_AVATAR_QUALITY','Miniresim Kalitesi');
DEFINE('_KUNENA_WELCOME','Kunena\'a Hoşgeldiniz!');
DEFINE('_KUNENA_WELCOME_DESC','Forum sistemi olarak Kunena\'yı tercih ettiğiniz için teşekkür ederiz. Bu panel, forumunuzla ilgili genel bilgileri size sunacaktır. Forumunuzla ilgili tüm kontrollere soldaki alandan ulaşabilirsiniz. Her sayfada kontrollerin nasıl kullanılacağı ile bilgi verilmiştir.');
DEFINE('_KUNENA_STATISTIC','İstatistikler');
DEFINE('_KUNENA_VALUE','Değer');
DEFINE('_GEN_CATEGORY','Kategori');
DEFINE('_GEN_STARTEDBY','Başlatan:&#32;');
DEFINE('_GEN_STATS','istatistikler');
DEFINE('_STATS_TITLE',' forum istatistikleri');
DEFINE('_STATS_GEN_STATS','Genel İstatistikler');
DEFINE('_STATS_TOTAL_MEMBERS','Üyeler:');
DEFINE('_STATS_TOTAL_REPLIES','Cevaplar:');
DEFINE('_STATS_TOTAL_TOPICS','Başlıklar:');
DEFINE('_STATS_TODAY_TOPICS','Bugünkü başlıklar:');
DEFINE('_STATS_TODAY_REPLIES','Bugünkü cevaplar:');
DEFINE('_STATS_TOTAL_CATEGORIES','Kategoriler:');
DEFINE('_STATS_TOTAL_SECTIONS','Bölümler:');
DEFINE('_STATS_LATEST_MEMBER','Son üye:');
DEFINE('_STATS_YESTERDAY_TOPICS','Dünkü Başlıklar:');
DEFINE('_STATS_YESTERDAY_REPLIES','Dünkü Cevaplar:');
DEFINE('_STATS_POPULAR_PROFILE','En Popüler 10 Üye (Profil İzlenimi)');
DEFINE('_STATS_TOP_POSTERS','En Çok Gönderiye Sahip Olanlar');
DEFINE('_STATS_POPULAR_TOPICS','En Popüler Başlıklar');
DEFINE('_COM_A_STATSPAGE','İstatistikler Sayfasını Etkinleştir');
DEFINE('_COM_A_STATSPAGE_DESC','&quot;Evet&quot;i seçerseniz, forum istatistiklerinizi herkesin erişimine açmış olacaksınız. Bu sayfa, forumunuza ait çeşitli sayısal bilgileri sunmaktadır. <em>İstatistikler sayfasını yöneticiler her zaman görür');
DEFINE('_COM_C_JBSTATS','Forum İstatistikleri');
DEFINE('_COM_C_JBSTATS_DESC','Forum İstatistikleri');
define('_GEN_GENERAL','Genel');
define('_PERM_NO_READ','Foruma erişim izniniz yok.');
DEFINE ('_KUNENA_SMILEY_SAVED','Simge kaydedildi.');
DEFINE ('_KUNENA_SMILEY_DELETED','Simge silindi.');
DEFINE ('_KUNENA_CODE_ALLREADY_EXITS','Kod Mevcut.');
DEFINE ('_KUNENA_MISSING_PARAMETER','Kayıp Parametre');
DEFINE ('_KUNENA_RANK_ALLREADY_EXITS','Seviye Mevcut.');
DEFINE ('_KUNENA_RANK_DELETED','Seviye Silindi.');
DEFINE ('_KUNENA_RANK_SAVED','Seviye Kaydedildi.');
DEFINE ('_KUNENA_DELETE_SELECTED','Seçilenler silindi');
DEFINE ('_KUNENA_MOVE_SELECTED','Seçilenler taşındı');
DEFINE ('_KUNENA_REPORT_LOGGED','Kayıt tutuldu');
DEFINE ('_KUNENA_GO','Git');
DEFINE('_KUNENA_MAILFULL','Abonelere gönderilen epostada tüm gönderi bulunsun.');
DEFINE('_KUNENA_MAILFULL_DESC','&quot;Hayır&quot;ı seçerseniz, aboneler yeni mesajların sadece başlıklarını alacak.');
DEFINE('_KUNENA_HIDETEXT','İçeriği görmek için giriş yapınız!');
DEFINE('_BBCODE_HIDE','Gizli metin: [hide]gizli bir metin[/hide] - ziyaretçilerinizden gizleyeceğiniz mesaj parçası');
DEFINE('_KUNENA_FILEATTACH','Dosya Eki:&#32;');
DEFINE('_KUNENA_FILENAME','Dosya İsmi:&#32;');
DEFINE('_KUNENA_FILESIZE','Dosya Boyutu:&#32;');
DEFINE('_KUNENA_MSG_CODE','Kod:&#32;');
DEFINE('_KUNENA_CAPTCHA_ON','Reklam engelleme sistemi');
DEFINE('_KUNENA_CAPTCHA_DESC','Reklam engelleme sistemi CAPTHCA Açık/Kapalı');
DEFINE('_KUNENA_CAPDESC','Kodu girin');
DEFINE('_KUNENA_CAPERR','Kod yanlış!');
DEFINE('_KUNENA_COM_A_REPORT', 'Mesaj Raporla');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'Herhangi bir mesajın raporlanabilmesini isterseniz &quot;Evet.&quot;i seçin');
DEFINE('_KUNENA_REPORT_MSG', 'Mesajı Raporlandı');
DEFINE('_KUNENA_REPORT_REASON', 'Sebep');
DEFINE('_KUNENA_REPORT_MESSAGE', 'Mesajınız');
DEFINE('_KUNENA_REPORT_SEND', 'Raporu Yolla');
DEFINE('_KUNENA_REPORT', 'Yetkiliye Raporla');
DEFINE('_KUNENA_REPORT_RSENDER', 'Yollayana Raporla:&#32;');
DEFINE('_KUNENA_REPORT_RREASON', 'Rapor Nedeni:&#32;');
DEFINE('_KUNENA_REPORT_RMESSAGE', 'Rapor Mesajı:&#32;');
DEFINE('_KUNENA_REPORT_POST_POSTER', 'Mesaj Göndericisi:&#32;');
DEFINE('_KUNENA_REPORT_POST_SUBJECT', 'Mesaj Konusu:&#32;');
DEFINE('_KUNENA_REPORT_POST_MESSAGE', 'Mesaj:&#32;');
DEFINE('_KUNENA_REPORT_POST_LINK', 'Mesaj Bağlantısı:&#32;');
DEFINE('_KUNENA_REPORT_INTRO', 'tarafından bu mesajın yollanma nedeni');
DEFINE('_KUNENA_REPORT_SUCCESS', 'Raporlama Başarılı!');
DEFINE('_KUNENA_EMOTICONS', 'İfadeler');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Simgeler');
DEFINE('_KUNENA_EMOTICONS_CODE', 'Kod');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Simgeyi Düzenle');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Simgeleri Düzenle');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'İfade Çubuğu');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'Yeni Simge');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'Daha Fazla Simge');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Pencereyi Kapat');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Ekstra İfadeler');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Simge Seç');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Desteği');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Joomla Mambot Desteğini Etkinleştir');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'Profil Eklentisi Ayarları');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Kullanıcı Adını Değişimine İzin Ver');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Profilindeki kullanıcı adını değiştirmesine izin ver');
DEFINE ('_KUNENA_RECOUNTFORUMS','Kategori İstatistiklerini Güncelle');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','Kategori İstatistikleri Güncellendi.');
DEFINE ('_KUNENA_EDITING_REASON','Düzenleme Sebebi');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Son Düzenleme');
DEFINE ('_KUNENA_BY','Tarafından');
DEFINE ('_KUNENA_REASON','Sebep');
DEFINE('_GEN_GOTOBOTTOM', 'En alt');
DEFINE('_GEN_GOTOTOP', 'En üst');
DEFINE('_STAT_USER_INFO', 'Kullanıcı Bilgisi');
DEFINE('_USER_SHOWEMAIL', 'Epostayı Göster'); // <=FB 1.0.3
DEFINE('_USER_SHOWONLINE', 'Çevrimiçi Kullanıcıları Göster');
DEFINE('_KUNENA_HIDDEN_USERS', 'Gizli Kullanıcılar');
DEFINE('_KUNENA_SAVE', 'Kaydet');
DEFINE('_KUNENA_RESET', 'Sıfırla');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'Varsayılan Galeri');
DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'Kişisel Bilgi');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'Özet');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'Miniresmim');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'Forum Ayarları');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'Görsellik');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'Profil bilgim');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'Gönderilerim');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'Aboneliklerim');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'Beğendiklerim');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Özel Mesajlaşma');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Gelen');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'Yeni Mesaj');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Giden');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Çöp Kutusu');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Ayarlar');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Arkadaşlarım');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'Engelli Listesi');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'Ek Bilgi');
DEFINE('_KUNENA_MYPROFILE_NAME', 'İsim');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Kullanıcı Adı');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Eposta');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'Kullanıcı Tipi');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'Kayıt Tarihi');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'Son ziyaret');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Gönderiler');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'Profil Gösterimi');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'Kişisel Metin');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'Cinsiyet');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'Doğum günü');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'Yıl (YYYY) - Ay (MM) - Gün (DD)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'Konum');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'ICQ Numaranız.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'AOL Kullanıcı Adınız.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'Yahoo! Kullanıcı Adınız.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'Skype Kullanıcı Adınız.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'GTalk Kullanıcı Adınız.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Web site');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Site ismi');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Örnek: Joomla!Türkiye JTR!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Web site URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Example: www.Joomlatr.org');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'MSN eposta adresiniz.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'İmza');
DEFINE('_KUNENA_MYPROFILE_MALE', 'Bay');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'Bayan');
DEFINE('_KUNENA_BULKMSG_DELETED', 'Gönderiler Silindi');
DEFINE('_KUNENA_DATE_YEAR', 'Yıl');
DEFINE('_KUNENA_DATE_MONTH', 'Ay');
DEFINE('_KUNENA_DATE_WEEK','Hafta');
DEFINE('_KUNENA_DATE_DAY', 'Gün');
DEFINE('_KUNENA_DATE_HOUR', 'Saat');
DEFINE('_KUNENA_DATE_MINUTE', 'Dakika');
DEFINE('_KUNENA_IN_FORUM', '&#32;Tarih:&#32;');
DEFINE('_KUNENA_FORUM_AT', '&#32;Saat:&#32;');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'Forum kodları ve simge butonları gösterilmemesine karşın, hala kullanılabilirler.');

// 1.0.1
DEFINE ('_KUNENA_FORUMTOOLS','Forum Araçları');

//userlist
DEFINE ('_KUNENA_USRL_USERLIST','Kullanıcı listesi');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s Sitesinde <b>%d</b> Kayıtlı Kullanıcı Mevcuttur');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','Lütfen aramak için bir değer giriniz!');
DEFINE ('_KUNENA_USRL_SEARCH','Kullanıcı bul');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','Arama');
DEFINE ('_KUNENA_USRL_LIST_ALL','Hepsini listele');
DEFINE ('_KUNENA_USRL_NAME','İsim');
DEFINE ('_KUNENA_USRL_USERNAME','Kullanıcı adı');
DEFINE ('_KUNENA_USRL_GROUP','Grup');
DEFINE ('_KUNENA_USRL_POSTS','Gönderiler');
DEFINE ('_KUNENA_USRL_KARMA','Başarı');
DEFINE ('_KUNENA_USRL_HITS','İzlenimler');
DEFINE ('_KUNENA_USRL_EMAIL','Eposta');
DEFINE ('_KUNENA_USRL_USERTYPE','Kullanıcı tipi');
DEFINE ('_KUNENA_USRL_JOIN_DATE','Kayıt tarihi');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','Son ziyaret');
DEFINE ('_KUNENA_USRL_NEVER','Asla');
DEFINE ('_KUNENA_USRL_ONLINE','Durum');
DEFINE ('_KUNENA_USRL_AVATAR','Miniresim');
DEFINE ('_KUNENA_USRL_ASC','Artan');
DEFINE ('_KUNENA_USRL_DESC','Azalan');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','Görüntüleme');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%m/%d/%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','Geliştiriciler');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','Kullanıcı Listesi');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','Kullanıcı Listesi Sıra Sayısı');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','Kullanıcı Listesi Sıra Sayısı');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','Çevrimiçi');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','Çevrimiçi Kullanıcıları Göster');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','Miniresmi Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','Gerçek İsmi Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','Kullanıcı Adını Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','Kullanıcı Grubunu Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','Gönderi Sayısını Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','Başarıyı Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','Epostayı Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Kullanıcı Tipini Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','Katılım Tarihini Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','Son Giriş Tarihini Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','Profil İzlenimlerini Göster');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');
DEFINE('_KUNENA_DBWIZ', 'Veritabanı Sihirbazı');
DEFINE('_KUNENA_DBMETHOD', 'Kurulumunuzu tamamlamak için kullanacağınız yöntemi seçiniz:');
DEFINE('_KUNENA_DBCLEAN', 'Kurulumu temizle');
DEFINE('_KUNENA_DBUPGRADE', 'Joomlaboard\'dan yükselt');
DEFINE('_KUNENA_TOPLEVEL', 'Üst düzey kategori');
DEFINE('_KUNENA_REGISTERED', 'KAyıtlı');
DEFINE('_KUNENA_PUBLICBACKEND', 'Kullanıcı Arkaplanı');
DEFINE('_KUNENA_SELECTANITEMTO', 'Öğe seçin');
DEFINE('_KUNENA_ERRORSUBS', 'İletileri silmede ve eklemede hata.');
DEFINE('_KUNENA_WARNING', 'uyarı...');
DEFINE('_KUNENA_CHMOD1', 'Dosyayı güncelleyebilmeniz için CHMOD değerini 766 olarak ayarlamalısınız.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Yapılandırma dosyanız');
DEFINE('_KUNENA_KUNENA', 'Kunena');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Tema seçin');
DEFINE('_KUNENA_CONFIGSAVED', 'Ayarlar kaydedildi.');
DEFINE('_KUNENA_CONFIGNOTSAVED', 'ÖNEMLİ HATA: Ayarlar kaydedilmedi.');
DEFINE('_KUNENA_TFINW', 'Dosya yazılabilir değil.');
DEFINE('_KUNENA_FBCFS', 'Kunena CSS dosyası kaydedildi.');
DEFINE('_KUNENA_SELECTMODTO', 'Yetkili seçin');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'Temizlemek için bir forum seçmelisiniz!');
DEFINE('_KUNENA_DELMSGERROR', 'İleti silmede başarısızlık:');
DEFINE('_KUNENA_DELMSGERROR1', 'İleti metni silmede başarısızlık:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'Abonelik temizlemede başarısızlık:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forumu temizle');
DEFINE('_KUNENA_PRUNEDAYS', 'gün');
DEFINE('_KUNENA_PRUNEDELETED', 'Silinmiş:');
DEFINE('_KUNENA_PRUNETHREADS', 'Başlıklar');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Kullanıcı temizlemede hata:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Silinen kullanıcılar:'); // <=FB 1.0.3
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'Kullanıcı profilleri'); // <=FB 1.0.3
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'Temizlemeye uygun profil bulunamadı.'); // <=FB 1.0.3
DEFINE('_KUNENA_TABLESUPGRADED', 'Kunena tablolarının yükseltildiği sürüm');
DEFINE('_KUNENA_FORUMCATEGORY', 'Forum Kategori');
DEFINE('_KUNENA_IMGDELETED', 'Resim silindi');
DEFINE('_KUNENA_FILEDELETED', 'Dosya silindi');
DEFINE('_KUNENA_NOPARENT', 'Üstü Yok');
DEFINE('_KUNENA_DIRCOPERR', 'Hata: Dosya Hatası');
DEFINE('_KUNENA_DIRCOPERR1', 'kopyalanamadı!\n');
DEFINE('_KUNENA_INSTALL1', '<em>Joomla! İçerik Yönetim Sistemi</em> için <strong>Kunena Forum Bileşeni</strong><br />&copy; 2008 - 2009 by www.Kunena.com<br />All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Aktarım/Kurulum :</code></strong><br /><br /><font color="red"><b>Başarılı');
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profil Ayarları');
DEFINE('_KUNENA_FORUMPRF', 'Profil');
DEFINE('_KUNENA_FORUMPRRDESC', 'Jomsocial, Clexus PM veya Community Builder bileşenlerine sahipseniz, profil sayfalarını kullanmak için Kunena\'da ayarlama yapabilirsiniz.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'Profil');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>Profili Göster</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'Tüm Forum İletileri');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'Başlık');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'Başlatan');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'Kategori');
DEFINE('_KUNENA_USERPROFILE_DATE', 'Zaman');
DEFINE('_KUNENA_USERPROFILE_HITS', 'İzlenim');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'Gönderi yok');
DEFINE('_KUNENA_TOTALFAVORITE', 'Beğenilen: &#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'Alt forum sütün sayısı&#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'Ana kategori altındaki forum sütunu sayısı&#32;');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'Gönderi abonelikleri her seferinde kontrol edilsin mi?');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'Gönderi aboneliklerinin her seferinde kontrol edilmesini istiyorsanız &quot;Evet&quot; olarak işaretleyiniz');
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'Kategori/Forum, bir isme sahip olmalıdır');
// Forum Configuration (New in Kunena)
DEFINE('_KUNENA_SHOWSTATS', 'İstatistikleri Göster');
DEFINE('_KUNENA_SHOWSTATSDESC', 'İstatistikleri göstermek isterseniz &quot;Evet.&quot;i seçin');
DEFINE('_KUNENA_SHOWWHOIS', 'Çevrimiçi  kullanıcıları göster');
DEFINE('_KUNENA_SHOWWHOISDESC', 'Çevrimiçi kullanıcıları göstermek isterseniz &quot;Evet.&quot;i seçin');
DEFINE('_KUNENA_STATSGENERAL', 'Genel İstatistikleri Göster');
DEFINE('_KUNENA_STATSGENERALDESC', 'Genel istatistikleri göstermek isterseniz &quot;Evet.&quot;i seçin');
DEFINE('_KUNENA_USERSTATS', 'Popüler Kullanıcı İstatistiklerini Göster');
DEFINE('_KUNENA_USERSTATSDESC', 'Popüler kullanıcı istatistiklerini göstermek isterseniz &quot;Evet.&quot;i seçiniz');
DEFINE('_KUNENA_USERNUM', 'Popüler Kullanıcı Sayısı');
DEFINE('_KUNENA_USERPOPULAR', 'Popüler Konu İstatistiklerini Göster');
DEFINE('_KUNENA_USERPOPULARDESC', 'Popüler konu istatistiklerini göstermek isterseniz &quot;Evet.&quot;i seçiniz');
DEFINE('_KUNENA_NUMPOP', 'Popüler Konu Sayısı');
DEFINE('_KUNENA_INFORMATION',
    'Kunena Takımı, Kunenea 1.0.9\'u sunmaktan gurur duyar. Tüm ihtiyaçlara cevap verebilen içerik yönetim sistemi Joomla!\'nın  güçlü ve estetik forumu Kunena, JoomlaBoard üzerine kurulmuş ve bizim çalışmalarımızla geliştirilen bir bileşendir. Kunena\'nın JoomlaBoard\'un özelliklerine ek olarak gelen özellikleri:<br /><br /><ul><li>Tasarım açısından kullanıcı dostu. Basitçe, SMF\'nin tema yapısında. Birkaç adımda, forumunuzun tasarımını tamamen değiştirebilirsiniz. Bu özellik için takımımızın harika tasarımcılarına teşekkür ederiz.</li><li>Daha iyi bir yönetim için, sınırsız alt kategori özelliği.</li><li>Daha hızlı sistem ve eklentiler için daha iyi kod yapısı.</li><li>Forumun üstünde profil alanı</li><li>Clexus PM ve Uddeim gibi popüler özel mesaj bileşenlerini destekleme</li><li>Basit eklenti sistemi</li><li>Dil tanımlı simge sistemi<br /></li><li>Diğer temalarla paylaşımlı resim sistemi. (temalar ve resim dizileri arasında seçim yapabilme)</li><li>Joomla! modüllerini forum temasının içine yerleştirebilme. Forumunuzun içinde reklam alanı olsun istemez misiniz??</li><li>Sevilen başlıkları seçme ve yönetme</li><li>Forum öne çıkanlar ve vurgulanları</li><li>Forum duyuruları ve duyuru paneli</li><li>Tablı son mesajlar</li><li>Forumun altında istatistikler</li><li>Kimler hangi sayfada çevrimiçi?</li><li>Kategoriye özel resim sistemi</li><li>Geliştirilmiş içerik yolu</li><li><strong>JoomlaBoard\'den içe aktarım. (SMF\'den aktarım çok yakında)</strong></li><li>RSS ve PDF desteği</li><li>Gelişmiş arama (geliştirilmekte)</li><li>Community Builder ve Clexus PM profili seçenekleri</li><li>Miniresim yönetimi : CB ve ClexusPM seçenekleri<br /></li></ul><br />Joomla!\ya ücretsiz olarak kaliteli bir forum sistemi ekleyebildiysek ne mutlu bize...<br /><br />Kunena projesi, birçok geliştirici ve tasarımcının ortaklaşa çalışmaları ile yürütülmektedir. Her birine teşekkürlerimizi sunuyoruz ve projemizden memnun kaldığınızı umuyoruz!<br /><br />Kunena! Takımı<br /><br /><br />J!T Notu: Kuenana Türkçe Dil Dosyaları, Joomla!Türkiye JoomlaTr.org Çeviri Ekibi tarafından hazırlanmaktadır.</td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Yönergeler');
DEFINE('_KUNENA_FINFO', 'Kunena Forum Bilgileri');
DEFINE('_KUNENA_CSSEDITOR', 'Kunena Tema CSS Editörü');
DEFINE('_KUNENA_PATH', 'Yol:');
DEFINE('_KUNENA_CSSERROR', 'Değişiklikleri kaydedebilmeniz için CSS Tema dosyası yazılabilir olmalıdır.');
// User Management
DEFINE('_KUNENA_FUM', 'Kunena Kullanıcı Profil Yönetimi');
DEFINE('_KUNENA_SORTID', 'kullanıcı ID göre sırala');
DEFINE('_KUNENA_SORTMOD', 'yetkililere göre sırala');
DEFINE('_KUNENA_SORTNAME', 'isme göre sırala');
DEFINE('_KUNENA_VIEW', 'Görünüm');
DEFINE('_KUNENA_NOUSERSFOUND', 'Kullanıcı Profili Bulunamadı.');
DEFINE('_KUNENA_ADDMOD', 'Yetkili Ekle');
DEFINE('_KUNENA_NOMODSAV', 'Yetkilendirilecek Kullanıcı Bulunamadı. Lütfen aşağıdaki \'açıklama\'yı okuyunuz.');
DEFINE('_KUNENA_NOTEUS',
    'NOT: Burada sadece Kunena profillerinde yetkili olarak tanımlanan kullanıcılar gösterilir. Kullanıcıyı yetkili olarak tanımlamak için: <a href="index2.php?option=com_kunena&task=profiles">Kullanıcı Yönetimi</a>\'ne gidin ve yetkili olarak belirlemek istediğiniz kullanıcıyı bulun. Kullanıcının profil bilgilerini, yetkili kullanıcı olarak güncelleyin. Yetkili kullanıcı belirleme hakkı sadece Site Yöneticileri tarafından yapılabilir.');
DEFINE('_KUNENA_PROFFOR', 'Profil');
DEFINE('_KUNENA_GENPROF', 'Genel Profil Seçenekleri');
//DEFINE('_KUNENA_PREFVIEW', 'Tercihi Görünüm Tipi:');
DEFINE('_KUNENA_PREFOR', 'Tercihi İleti Sıralaması:');
DEFINE('_KUNENA_ISMOD', 'Yetkili:');
DEFINE('_KUNENA_ISADM', '<strong>Evet</strong> (kullanıcı (süper) yönetici ile değiştirilemez');
DEFINE('_KUNENA_COLOR', 'Renk');
DEFINE('_KUNENA_UAVATAR', 'Kullanıcı miniresmi:');
DEFINE('_KUNENA_NS', 'Seçili yok');
DEFINE('_KUNENA_DELSIG', '&#32;imzayı silmek için kutucuğu işaretşeyiniz');
DEFINE('_KUNENA_DELAV', '&#32;miniresmi silmek için kutucuğu işaretleyiniz');
DEFINE('_KUNENA_SUBFOR', 'Abonelikler');
DEFINE('_KUNENA_NOSUBS', 'Kullanıcı aboneliği bulunamadı');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'Temel');
DEFINE('_KUNENA_BASICSFORUM', 'Temel Forum Bilgileri');
DEFINE('_KUNENA_PARENT', 'Üst Kategori:');
DEFINE('_KUNENA_PARENTDESC',
    'Açıklama: Üst kategori oluşturmak için, düzenlemelerde \'Üst Seviye Kategori\'yi seçiniz. Kategoriler, forumlarınızı kapsar.<br />Forumlar <strong>sadece</strong> \'Üst Kategori\' olarak tanımlanmış kategoriler içinde oluşturulabilir.<br />İletiler, kategorilere değil forumlara gönderilebilir.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'Forum ismi ve açıklaması');
DEFINE('_KUNENA_NAMEADD', 'İsim:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'Açıklama:');
DEFINE('_KUNENA_ADVANCEDDESC', 'Gelişmiş Ayarlar');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'Güvenlik ve Erişim');
DEFINE('_KUNENA_LOCKEDDESC', 'Forumu kilitlemek için &quot;Evet&quot;i seçiniz. Sadece yetkililer ve yöneticiler yeni başlık açabilir, başlıkları cevaplayabilir veya gönderileri taşıyabilir.');
DEFINE('_KUNENA_LOCKED1', 'Kilitli:');
DEFINE('_KUNENA_PUBACC', 'Genel Erişim Seviyesi:');
DEFINE('_KUNENA_PUBACCDESC',
    'Genel erişime açık olmayan forumlar oluşturmak için, forumu görüntüleyecek ve mesaj yollayabilecek en düşük dereceli kullanıcı grubunu belirlemelisiniz.  Varsayılan olarak, en düşük kullanıcı grubu &quot;Herkes&quot; olarak tanımlanmıştır.<br /><b>Açıklama</b>: Tüm kategoriyi belirli grup ya da gruplarla sınırlandırırsanız; tüm forum içeriği, forumlardan bir ya da daha fazlası daha düşük erişim gruplarına izin verse bile, kategoriye erişim izni olmayanlardan gizlenecektir. Bu durum forum yetkilileri için de geçerlidir. Forum yetkiliniz kategori için izin verilen kullanıcı grubunda değilse, onu kategoriye yetkili olarak atamalısınız.<br /> Forum yetkilileri, yetkili listesinden eklenebilir.');
DEFINE('_KUNENA_CGROUPS', 'Alt Gruplar:');
DEFINE('_KUNENA_CGROUPSDESC', 'Alt gruplar da foruma erişebilsin mi? &quot;Hayır&quot;ı seçerseniz, forum <b>sadece</b> seçili grupla sınırlanacaktır.');
DEFINE('_KUNENA_ADMINLEVEL', 'Genel Erişim Seviyesi:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'Genel erişim kısıtlamaları olan bir forum oluşturmuşsanız, ek olarak Yönetici Erişim Seviyesi tanımlayabilirsiniz.<br /> Forumu önplanda özel kullanıcı grubunun erişimine açıp arkaplanda erişime kapatırsanız, yöneticiler foruma giriş yapamaz ve forumu görüntüleyemez.');
DEFINE('_KUNENA_ADVANCED', 'Gelişmiş');
DEFINE('_KUNENA_CGROUPS1', 'Alt gruplar:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Alt gruplar da foruma erişebilsin mi? &quot;Hayır&quot;ı seçerseniz, forum <b>sadece</b> seçili grupla sınırlanacaktır.');
DEFINE('_KUNENA_REV', 'İleti Kontrolü:');
DEFINE('_KUNENA_REVDESC',
    '&quot;Evet&quot;i seçerseniz, gönderilen mesajlar yayınlanmadan önce forum yetkilileri tarafından kontrol edilecektir. Sadece yetkilendirilmiş forumlar için kullanışlıdır!<br />Herhangi bir forum yetkili tanımlanmadan bu özellik aktif edilirse, gönderilen mesajların onaylanmasında ve silinmesinde tek sorumlu site yöneticisi olur.');
DEFINE('_KUNENA_MOD_NEW', 'Yetkilendirme');
DEFINE('_KUNENA_MODNEWDESC', 'Forum yetkililerin belirlenmesi');
DEFINE('_KUNENA_MOD', 'Yetkililik:');
DEFINE('_KUNENA_MODDESC',
    '&quot;Evet&quot;i seçerseniz, foruma yetkili tanımlayabileceksiniz.<br /><strong>Not:</strong> Yeni mesajların yayınlanmadan önce kontrol edileceği anlamına gelmemektedir!<br /> &quot;Kontrol&quot; seçeneğini, gelişmiş ayarlar sekmesinden ayarlayabilirsiniz.<br /><br /> <strong>Açıklama:</strong> After setting Moderation to &quot;Evet&quot;i seçtikten sonra, forum yapılandırmasını kaydetmeden önce, \'Yeni\' butonuna tıklayarak Forum Yetkilisi eklemelisiniz.');
DEFINE('_KUNENA_MODHEADER', 'Yetkilendirme Seçenekleri');
DEFINE('_KUNENA_MODSASSIGNED', 'Forum Yetkilileri:');
DEFINE('_KUNENA_NOMODS', 'Forum İçin Yetkili Bulunamadı');
// Some General Strings (Improvement in Kunena)
DEFINE('_KUNENA_EDIT', 'Düzenle');
DEFINE('_KUNENA_ADD', 'Ekle');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'Yukarı Taşı');
DEFINE('_KUNENA_MOVEDOWN', 'Aşağı Taşı');
// Groups - Integration in Kunena
DEFINE('_KUNENA_ALLREGISTERED', 'Tüm Kayıtlı Kullanıcılar');
DEFINE('_KUNENA_EVERYBODY', 'Herkes');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'Sırala');
DEFINE('_KUNENA_CHECKEDOUT', 'Denetle');
DEFINE('_KUNENA_ADMINACCESS', 'Yönetici');
DEFINE('_KUNENA_PUBLICACCESS', 'Genel');
DEFINE('_KUNENA_PUBLISHED', 'Yayınlanmış');
DEFINE('_KUNENA_REVIEW', 'Denetle');
DEFINE('_KUNENA_MODERATED', 'Yetkili');
DEFINE('_KUNENA_LOCKED', 'Kilitli');
DEFINE('_KUNENA_CATFOR', 'Kategori / Forum');
DEFINE('_KUNENA_ADMIN', 'Kunena Yönetimi');
DEFINE('_KUNENA_CP', 'Kunena Kontrol Paneli');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Miniresim Uyumluluğu');
DEFINE('_COM_A_RANKS_SETTINGS', 'Seviye Ayarları');
DEFINE('_COM_A_RANKING_SETTINGS', 'Seviye Ayarları');
DEFINE('_COM_A_AVATAR_SETTINGS', 'Miniresim Ayarları');
DEFINE('_COM_A_SECURITY_SETTINGS', 'Güvenlik Ayarları');
DEFINE('_COM_A_BASIC_SETTINGS', 'Temel Ayarlar');
// Kunena 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'Beğenilenlere İzin Ver');
DEFINE('_COM_A_FAVORITES_DESC', 'Kayıtlı kullanıcıların başlığı beğenilenlerine eklemesine izin vermek için &quot;Evet&quot; i seçiniz.&#32;');
DEFINE('_USER_UNFAVORITE_ALL', 'Tüm başlıkların <b><u>beğenilmeyen</u></b> olmasını istiyorsanız bu kuturcuğu işaretleyin (çeşitli nedenlerden ötürü gösterilemeyenleri de içerir)');
DEFINE('_VIEW_FAVORITETXT', 'Beğenilmiş&#32;');
DEFINE('_USER_UNFAVORITE_YES', 'Beğenilmemiş.');
DEFINE('_POST_FAVORITED_TOPIC', 'Beğeniniz işleme alınmıştır.');
DEFINE('_VIEW_UNFAVORITETXT', 'Beğendim');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'Abone Ol');
DEFINE('_USER_NOFAVORITES', 'Beğenilen Yok');
DEFINE('_POST_SUCCESS_FAVORITE', 'Beğeni talebiniz kabul edilmiştir.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'Arama sonuçlarında, sayfa başına düşen mesaj');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'Arama sonuçlarında, sayfa başına düşen mesaj');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'Joomla stilini mi kullanacaksınız?');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'Joomla stilini kullanmak için &quot;Evet&quot;i seçiniz. (class: like sectionheader, sectionentry1 ...)&#32;');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'Alt Kategori Simgesini Göster');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'Forum listenizde alt kategori simgesini göstermek isterseniz &quot;Evet&quot;i seçiniz.&#32;');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'Duyuruları Göster');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'Duyurular kutusunu forum sayfanızda göstermek isterseniz &quot;Evet&quot;, i seçiniz.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'Kategori listesinde miniresmi göster?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'Forum kategorileri listesinde, kullanıcı miniresminin sunulmasını aktif etmek için &quot;Evet&quot;, i seçiniz.');
DEFINE('_KUNENA_RECENT_POSTS', 'Son Gönderi Ayarları');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'Son Gönderileri Göster');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'Son gönderileri göstermek için &quot;Evet&quot; i seçiniz');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'Son Gönderi Sayısı');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'Son Gönderi Sayısı');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'Sekme Başına Gösterilecek Gönderi&#32;');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'Sekme başına gösterilecek gönderi');
DEFINE('_KUNENA_LATEST_CATEGORY', 'Kategoriyi Göster');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'İstediğiniz kategorileri belirterek sadece onlara ait son gönderileri gösterebilirsiniz. For örnek: 2,3,7&#32;');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'Tekil Konu Göster');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'Tekil Konu Göster');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'Cevabı Göster');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'Cevabı Göster (Cvp:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'Konu Uzunluğu');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'Konu Uzunluğu');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'Tarihi Göster');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'Tarihi Göster');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'İzlenimi Göster');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'İzlenimi Göster');
DEFINE('_KUNENA_SHOW_AUTHOR', 'Yazarı Göster');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=kullanıcı adı, 2=gerçek isim, 0=hiç');
DEFINE('_KUNENA_STATS', 'İstatistik eklentisi ayarları&#32;');
DEFINE('_KUNENA_CATIMAGEPATH', 'Kategori Resmi Yolu&#32;');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'Kategori Resmi Yolu "category_images/" olarak ayarlarsanız bu yol "your_html_rootfolder/images/fbfiles/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'Duyuru Yetkilileri ID\'leri&#32;');
DEFINE('_KUNENA_ANN_MODID_DESC', 'Sitenizdeki bazı kullanıcıları duyuru ekleyip silmeleri için yetkilendirebilirsiniz. Örneğin : 62,63,73 . Duyuru yetkilileri, duyuru ekleyebilir, düzenleyebilir ve silebilir.');
//
DEFINE('_KUNENA_FORUM_TOP', 'Forum Kategorileri&#32;');
DEFINE('_KUNENA_CHILD_BOARDS', 'Alt Forumlar&#32;');
DEFINE('_KUNENA_QUICKMSG', 'Hızlı Cevap&#32;');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Forumdaki Başlıklar&#32;');
DEFINE('_KUNENA_FORUM', 'Forum&#32;');
DEFINE('_KUNENA_SPOTS', 'Öne Çıkanlar');
DEFINE('_KUNENA_CANCEL', 'iptal');
DEFINE('_KUNENA_TOPIC', 'BAŞLIK:&#32;');
DEFINE('_KUNENA_POWEREDBY', 'Sistem&#32;');
// Time Format
DEFINE('_TIME_TODAY', '<b>Bugün</b>&#32;');
DEFINE('_TIME_YESTERDAY', '<b>Dün</b>&#32;');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'Son  Gönderiler');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'Kimler Çevrimiçi');
DEFINE('_KUNENA_WHO_MAINPAGE', 'Forum Anasayfa');
DEFINE('_KUNENA_GUEST', 'Ziyaretçi');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'inceleyen');
DEFINE('_KUNENA_ATTACH', 'Dosya Ekle');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'Beğenilme');
DEFINE('_USER_FAVORITES', 'Beğendikleriniz');
DEFINE('_THREAD_UNFAVORITE', 'Beğendiklerimden Kaldır');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'Hoşgeldiniz');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'Son Gönderileri Göster');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'Miniresmimi Ayarla');
DEFINE('_PROFILEBOX_MYPROFILE', 'Profilim');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'Gönderilerimi Göster');
DEFINE('_PROFILEBOX_GUEST', 'Ziyaretçi');
DEFINE('_PROFILEBOX_LOGIN', 'Giriş');
DEFINE('_PROFILEBOX_REGISTER', 'Kayıt');
DEFINE('_PROFILEBOX_LOGOUT', 'Çıkış');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'Kayıp Parola?');
DEFINE('_PROFILEBOX_PLEASE', 'Lütfen');
DEFINE('_PROFILEBOX_OR', 'yada');
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'Son Gönderiler');
DEFINE('_RECENT_TOPICS', 'Başlık');
DEFINE('_RECENT_AUTHOR', 'Yazar');
DEFINE('_RECENT_CATEGORIES', 'Kategori');
DEFINE('_RECENT_DATE', 'Tarih');
DEFINE('_RECENT_HITS', 'İzlenim');
// announcement

DEFINE('_ANN_ANNOUNCEMENTS', 'Duyurular');
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'Tarih');
DEFINE('_ANN_TITLE', 'Başlık');
DEFINE('_ANN_SORTTEXT', 'Kısa Metin');
DEFINE('_ANN_LONGTEXT', 'Uzun Metin');
DEFINE('_ANN_ORDER', 'Sırala');
DEFINE('_ANN_PUBLISH', 'Yayınla');
DEFINE('_ANN_PUBLISHED', 'Yayınlandı');
DEFINE('_ANN_UNPUBLISHED', 'Yayından Kalktı');
DEFINE('_ANN_EDIT', 'Düzenle');
DEFINE('_ANN_DELETE', 'Sil');
DEFINE('_ANN_SUCCESS', 'Başarılı');
DEFINE('_ANN_SAVE', 'Kaydet');
DEFINE('_ANN_YES', 'Evet');
DEFINE('_ANN_NO', 'HAyır');
DEFINE('_ANN_ADD', 'Yeni Ekle');
DEFINE('_ANN_SUCCESS_EDIT', 'Düzenleme Başarılı');
DEFINE('_ANN_SUCCESS_ADD', 'Ekleme Başarılı');
DEFINE('_ANN_DELETED', 'Silme Başarılı');
DEFINE('_ANN_ERROR', 'HATA');
DEFINE('_ANN_READMORE', 'Devamını Oku...');
DEFINE('_ANN_CPANEL', 'Duyuru Kontrol Paneli');
DEFINE('_ANN_SHOWDATE', 'Tarih Göster');
// Stats
DEFINE('_STAT_FORUMSTATS', 'Forum İstatistikleri');
DEFINE('_STAT_GENERAL_STATS', 'Genel İstatistikler');
DEFINE('_STAT_TOTAL_USERS', 'Toplam Kullanıcı');
DEFINE('_STAT_LATEST_MEMBERS', 'Son Üye');
DEFINE('_STAT_PROFILE_INFO', 'Profil Bilgilerini Göster');
DEFINE('_STAT_TOTAL_MESSAGES', 'Toplam İleti');
DEFINE('_STAT_TOTAL_SUBJECTS', 'Toplam Başlık');
DEFINE('_STAT_TOTAL_CATEGORIES', 'Toplam Kategori');
DEFINE('_STAT_TOTAL_SECTIONS', 'Toplam Bölüm');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'Bugün Açılan');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'Dün Açılan');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'Bugünkü Toplam Cevapr');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'Dünkü Toplam Cevap');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'Son Gönderileri Göster');
DEFINE('_STAT_MORE_ABOUT_STATS', 'İstatistikler Hakkında');
DEFINE('_STAT_USERLIST', 'Kullanıcı Listesi');
DEFINE('_STAT_TEAMLIST', 'Forum Ekibi');
DEFINE('_STATS_FORUM_STATS', 'Forum İstatistikleri');
DEFINE('_STAT_POPULAR', 'Popüler');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Kullanıcılar ( Toplam ileti)&#32;');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Başlıklar&#32;');
DEFINE('_STAT_POPULAR_USER_GSG', 'Kullanıcılar ( Toplam profil incelemesi)&#32;');
//Team List
DEFINE('_MODLIST_ONLINE', 'Şu an sitede');
DEFINE('_MODLIST_OFFLINE', 'Şu an sitede değil');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'Kimler çevrimiçi');
DEFINE('_WHO_ONLINE_NOW', 'Çevrimiçi');
DEFINE('_WHO_ONLINE_MEMBERS', 'Üye');
DEFINE('_WHO_AND', 've');
DEFINE('_WHO_ONLINE_GUESTS', 'Ziyaretçi');
DEFINE('_WHO_ONLINE_USER', 'Kullanıcı');
DEFINE('_WHO_ONLINE_TIME', 'Zaman');
DEFINE('_WHO_ONLINE_FUNC', 'İşlem');
// Userlist
DEFINE('_USRL_USERLIST', 'Kullanıcı listesi');
DEFINE('_USRL_REGISTERED_USERS', '%s sitesinde <strong>%d</strong> kayıtlı kullanıcı mevcuttur');
DEFINE('_USRL_SEARCH_ALERT', 'Lütfen aramak için bir değer giriniz!');
DEFINE('_USRL_SEARCH', 'Kullanıcı bul');
DEFINE('_USRL_SEARCH_BUTTON', 'Arama');
DEFINE('_USRL_LIST_ALL', 'Hepsini listele');
DEFINE('_USRL_NAME', 'İsim');
DEFINE('_USRL_USERNAME', 'Kullanıcı adı');
DEFINE('_USRL_EMAIL', 'Epostal');
DEFINE('_USRL_USERTYPE', 'Kullanıcı tipi');
DEFINE('_USRL_JOIN_DATE', 'Kayıt tarihi');
DEFINE('_USRL_LAST_LOGIN', 'Son ziyaret');
DEFINE('_USRL_NEVER', 'Asla');
DEFINE('_USRL_BLOCK', 'Durum');
DEFINE('_USRL_MYPMS2', 'ÖM');
DEFINE('_USRL_ASC', 'Artan');
DEFINE('_USRL_DESC', 'Azalan');
DEFINE('_USRL_DATE_FORMAT', '%m/%d/%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_USEREXTENDED', 'Detay');
DEFINE('_USRL_COMPROFILER', 'Profil');
DEFINE('_USRL_THUMBNAIL', 'Resim');
DEFINE('_USRL_READON', 'göster');
DEFINE('_USRL_MYPMSPRO', 'Clexus ÖM');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'ÖM Gönder');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'ÖM');
DEFINE('_USRL_SEARCHRESULT', 'Arama sonucu');
DEFINE('_USRL_STATUS', 'Durum');
DEFINE('_USRL_LISTSETTINGS', 'Kullanıcı Listesi Ayarları');
DEFINE('_USRL_ERROR', 'Hata');

//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Özel Mesaj Bileşeni');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'Arama: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Mesajı silmek istediğinizden emin misiniz? \n\n GERİ DÖNÜŞÜ YOKTUR!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'Gönderiler Silindi');
DEFINE('_COM_A_RANKING', 'Seviyelendirme');
DEFINE('_COM_A_BOT_REFERENCE', 'Bot Referans Çizelgesini Göster');
DEFINE('_COM_A_MOSBOT', 'Tartışma Botunu Aktif Et');
DEFINE('_PREVIEW', 'Önizleme');
DEFINE('_COM_A_MOSBOT_TITLE', 'Tartışma Botu');
DEFINE('_COM_A_MOSBOT_DESC', 'Tartışma Botu (Discuss Bot) kullanıcılarınızın sitenizdeki içeriği forumda tartışabilmesini sağlar. İçerik başlığı, forumda konu başlığı olarak kullanılır.'
           . '<br />If a topic does not exist, a new one is created. If the topic already exists, the user is shown the thread and where to reply.' . '<br /><strong>You will need to download and install the bot separately.</strong>'
           . '<br />Daha fazla bilgi için <a href="http://www.Kunena.com">Kunena Site</a> sitesini ziyaret ediniz.' . '<br />When installed, you will need to add the following bot lines to your Content:' . '<br />{mos_fb_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> si içeriğin hangi forum kategorisi altında tartışılacağını belirtir. catid ler için lütfen forumu açın ' . 've internet gezgininizin durum çubuğundaki urldeki kategori id sini kontrol ediniz.'
           . '<br />Örnek: Makalenin 26 numaralı catid ye sahip Forum kategorisinde tartışılmasını istiyorsanız, bot satırı şöyle olmalıdır'
           . '<br />İlk başta biraz zor gözükebilir ama bu dilediğiniz içerik öğesinin dilediğiniz forum kategorisinde tartışılmasını mümkün kılar.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'Arama');
DEFINE('_FORUM_SEARCHRESULTS', '%s sonuçların %s gösterimi.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'SSS');
// rules.php
DEFINE('_COM_FORUM_RULES', 'Kurallar');
DEFINE('_COM_FORUM_RULES_DESC', '<ul><li>Forum kurallarınızı joomlaroot/administrator/components/com_kunena/language/turkish.php dosyasına ekleyerek düzenleyiniz.</li><li>Kural 2</li><li>Kural 3</li><li>Kural 4</li><li>...</li></ul>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'Forum Kodu');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'Gönderiler Onaylandı.');
DEFINE('_MODERATION_DELETE_ERROR', 'HATA: Gönderi Silinemedi.');
DEFINE('_MODERATION_APPROVE_ERROR', 'HATA: Gönderi Onaylanamadı.');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'Kategoride Forum Yok!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Eski forumda gölge başlık bırakmada hata oluştu!');
DEFINE('_POST_MOVE_GHOST', 'Eski forumda gölge mesaj bırak');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'Foruma Atlama');
DEFINE('_COM_A_FORUM_JUMP', 'Foruma Atlamayı Etkinleştir');
DEFINE('_COM_A_FORUM_JUMP_DESC', '&quot;Evet&quot;i seçerseniz, forumunuzun altında kategoriler arasında hızlı atlamayı sağlayacak bir menü belirecektir.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'Kurallar');
DEFINE('_COM_A_RULESPAGE', 'Kurallar Sayfasını Etkinleştir');
DEFINE('_COM_A_RULESPAGE_DESC',
    '&quot;Evet&quot;i seçerseniz, forumunuzun üstünde, forum kurallarını görebilecekleri bir bağlantı belirecek. Kural sayfasını doldurmak ve düzenlemek için /joomla_root/components/com_kunena/rules.php dosyasını açınız.<em>Lütfen herhangi bir değişiklik yapmadan önce yedek alınız!</em>');
DEFINE('_MOVED_TOPIC', 'TAŞINDI:');
DEFINE('_COM_A_PDF', 'PDF Oluşturmayı Etkinleştir');
DEFINE('_COM_A_PDF_DESC',
    '&quot;Evet&quot;i seçerseniz, konu içinde yazılanları köklü görünüm olarak basit bir pdf olarak kaydetmeyi sağlayan simge belirecektir.');
DEFINE('_GEN_PDFA', 'Konunun PDF dosyasını yaratmak için tıklayınız (yeni pencerede açılır).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Kullanıcı bilgilerini görmek için tıklayın');
DEFINE('_VIEW_ADDBUDDY', 'Kullanıcıyı arkadaş listenize eklemek için tıklayın');
DEFINE('_POST_SUCCESS_POSTED', 'İletiniz başarıyla gönderildi');
DEFINE('_POST_SUCCESS_VIEW', '[ Gönderiye dönmek için tıklayın ]');
DEFINE('_POST_SUCCESS_FORUM', '[ Foruma dönmek için tıklayın ]');
DEFINE('_RANK_ADMINISTRATOR', 'Yönetici');
DEFINE('_RANK_MODERATOR', 'Yetkili');
DEFINE('_SHOW_LASTVISIT', 'Son ziyaretten bu yana');
DEFINE('_COM_A_BADWORDS_TITLE', 'Kötü sözcük filtresi');
DEFINE('_COM_A_BADWORDS', 'Kötü sözcük filtresini kullan');
DEFINE('_COM_A_BADWORDS_DESC', '&quot;Evet&quot;i seçerseniz kötü sözcük filtresini etkinleştirmiş olursunuz. Bunun çalışabilmesi için Kötü Sözcük Bileşeni (Badword Component) yüklü ve ayarlanmış olmalıdır!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* Bu mesaj, belirlenen kelimeleri içerdiği için sansürlendi *');
DEFINE('_COM_A_AVATAR_SRC', 'Miniresim Kullanım Yeri');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'JomSocial, Clexus PM veya Community Builder bileşenleri yüklü ise, Kunena miniresmi Jomsocial, Clexus PM veya Community Builder profillerinden gösterebilirsiniz.<br/>Not : Miniresmi Community Builder bileşeninden gösterecekseniz, küçük resim (thumbnail) seçeneğinin aktif olmasına dikkat ediniz.');
DEFINE('_COM_A_KARMA', 'Başarı Göstergesini Göster');
DEFINE('_COM_A_KARMA_DESC', 'Kullanıcı istatistikleri aktif ise, kullanıcının başarısını ve ilgili butonları göstermek için &quot;Evet&quot;i seçiniz.');
DEFINE('_COM_A_DISEMOTICONS', 'İfadeleri kapat');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'İfadeleri tamamen kapatmak için &quot;Evet&quot;i seçiniz.');
DEFINE('_COM_C_FBCONFIG', 'Kunena Yapılandırma');
DEFINE('_COM_C_FBCONFIGDESC', 'Kunena işlevlerini kontrol edin');
DEFINE('_COM_C_FORUM', 'Forum Yönetimi');
DEFINE('_COM_C_FORUMDESC', 'Kategoriler/forumlar ekleyin ve yönetin');
DEFINE('_COM_C_USER', 'Kullanıcı Yönetimi');
DEFINE('_COM_C_USERDESC', 'Temel kullanıcı ve kullanıcı profilini yönetin');
DEFINE('_COM_C_FILES', 'Yüklenmiş Dosya Gezgini');
DEFINE('_COM_C_FILESDESC', 'Yüklenmiş dosyaları izleyin ve yönetin');
DEFINE('_COM_C_IMAGES', 'Yüklenmiş Resim Gezgini');
DEFINE('_COM_C_IMAGESDESC', 'Yüklenmiş resimleri izleyin ve yönetin');
DEFINE('_COM_C_CSS', 'CSS Dosyası Düzenleme');
DEFINE('_COM_C_CSSDESC', 'Kunena\'nın Görünümünü Değiştirin');
DEFINE('_COM_C_SUPPORT', 'Üreticinin Resmi Sitesi');
DEFINE('_COM_C_SUPPORTDESC', 'Kunena Sitesine Bağlanın (Yeni Pencerede Açılır)');
DEFINE('_COM_C_PRUNETAB', 'Forumu Temizle');
DEFINE('_COM_C_PRUNETABDESC', 'Eski Konuları Kaldırın (Ayarlanabilir)');
DEFINE('_COM_C_PRUNEUSERS', 'Kullanıcı Temizle'); // <=FB 1.0.3
DEFINE('_COM_C_PRUNEUSERSDESC', 'Kunena Kullanıcılarını, Site Kullanıcılarınız İle Eşitleyin'); // <=FB 1.0.3
DEFINE('_COM_C_LOADMODPOS', 'Modül Konumlarını Yükle');
DEFINE('_COM_C_LOADMODPOSDESC', 'Kunena Teması İçin Modül Konumlarını Yükleyin');
DEFINE('_COM_C_UPGRADEDESC', 'Veritabanını güncel sürüme yükseltin');
DEFINE('_COM_C_BACK', 'Kunena Kontrol Paneline Dön');
DEFINE('_SHOW_LAST_SINCE', 'Son başlıklar - son ziyaretiniz:');
DEFINE('_POST_SUCCESS_REQUEST2', 'İsteğiniz işleme alındı');
DEFINE('_POST_NO_PUBACCESS3', 'Kayıt için tıklayınız.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'Gönderi başarı ile silindi.');
DEFINE('_POST_SUCCESS_EDIT', 'Gönderi başarı ile düzenlendi.');
DEFINE('_POST_SUCCESS_MOVE', 'Gönderi başarı ile taşındı.');
DEFINE('_POST_SUCCESS_POST', 'Gönderi başarı ile iletildi.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'Aboneliğiniz işleme alındı.');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'Karma');
DEFINE('_KARMA_SMITE', 'Düşür');
DEFINE('_KARMA_APPLAUD', 'Yükselt');
DEFINE('_KARMA_BACK', 'Başlığa dönmek için,');
DEFINE('_KARMA_WAIT', '6 saat içinde sadece bir kişinin başarı notunu değiştirebilirsiniz. <br/>Lütfen sürenin dolmasını bekleyiniz.');
DEFINE('_KARMA_SELF_DECREASE', 'Lütfen kendi başarı notunuzu düşürmeye çalışmayınız!');
DEFINE('_KARMA_SELF_INCREASE', 'Başarınızı yükseltmeye çalıştığınız için başarı notunuz düşürüldü!');
DEFINE('_KARMA_DECREASED', 'Kullanıcı başarı notu düşürüldü. Sayfa kısa bir süre içinde konuya geri dönmezse,');
DEFINE('_KARMA_INCREASED', 'Kullanıcı başarı notu yükseltildi. Sayfa kısa bir süre içinde konuya geri dönmezse,');
DEFINE('_COM_A_TEMPLATE', 'Tema');
DEFINE('_COM_A_TEMPLATE_DESC', 'Kullanmak istediğiniz temayı seçiniz.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'Tema Simgeleri Ayarı');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'Seçtiğiniz tema ile kullanılacak tema simgelerini seçiniz.');
DEFINE('_PREVIEW_CLOSE', 'Pencereyi kapat');
//==========================================
//new in 1.0 Stable
DEFINE('_COM_A_POSTSTATSBAR', 'Gönderi İstatistik Çubuğunu Kullan');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'Kullanıcının gönderilerini grafiksel istatistik çubuğu ile göstermek isterseniz &quot;Evet&quot;i seçiniz.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'İstatistik Çubuğu İçin Renk Seç');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'İstatistik çubuğu için kullanmak istediğiniz rengin numarasını giriniz');
DEFINE('_LATEST_REDIRECT',
    'Sizin için son gönderilenler listesi oluşturulmadan önce Kunena erişim haklarınızı yeniden düzenlemenizi ister. \n Endişelenmeyin, bu durum 30 dakikalık pasif bekleyiş ya da yeniden giriş için normaldir. \n Lütfen arama talebinizi yenileyiniz.');
DEFINE('_SMILE_COLOUR', 'Renk');
DEFINE('_SMILE_SIZE', 'Boyut');
DEFINE('_COLOUR_DEFAULT', 'Standart');
DEFINE('_COLOUR_RED', 'Kırmızı');
DEFINE('_COLOUR_PURPLE', 'Mor');
DEFINE('_COLOUR_BLUE', 'Mavi');
DEFINE('_COLOUR_GREEN', 'Yeşil');
DEFINE('_COLOUR_YELLOW', 'Sarı');
DEFINE('_COLOUR_ORANGE', 'Turuncu');
DEFINE('_COLOUR_DARKBLUE', 'Lacivert');
DEFINE('_COLOUR_BROWN', 'Kahverengi');
DEFINE('_COLOUR_GOLD', 'Altın');
DEFINE('_COLOUR_SILVER', 'Gümüş');
DEFINE('_SIZE_NORMAL', 'Normal');
DEFINE('_SIZE_SMALL', 'Küçük');
DEFINE('_SIZE_VSMALL', 'Çok Küçük');
DEFINE('_SIZE_BIG', 'Büyük');
DEFINE('_SIZE_VBIG', 'Çok Büyük');
DEFINE('_IMAGE_SELECT_FILE', 'Eklenecek resmi seçin');
DEFINE('_FILE_SELECT_FILE', 'Eklenecek dosyayı seçin');
DEFINE('_FILE_NOT_UPLOADED', 'Dosyanız Yüklenemedi. Tekrar göndermeyi deneyin veya gönderinizi düzenleyin.');
DEFINE('_IMAGE_NOT_UPLOADED', 'Resminiz Yüklenemedi. Tekrar göndermeyi deneyin veya gönderinizi düzenleyin.');
DEFINE('_BBCODE_IMGPH', 'Eklediğiniz resim için gönderinize [img] imgesi ekleyin');
DEFINE('_BBCODE_FILEPH', 'Eklediğiniz resim için gönderinize [file] imgesi ekleyin');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'Tüm aboneliklerinizi iptal etmek için bu kutuyu işaretleyin (çeşitli nedenlerle gösterilemeyenleri de içerir)');
DEFINE('_LINK_JS_REMOVED', '<em>JavaScript içeren aktif bağlantı, otomatik olarak kaldırıldı.</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'Görsellik');
DEFINE('_COM_A_USERS', 'İlişkili Kullanıcılar');
DEFINE('_COM_A_LENGTHS', 'Uzunluk ayarları');
DEFINE('_COM_A_SUBJECTLENGTH', 'Maksimum Konu Uzunluğu');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'Maksimum Konu Satırı Uzunluğu. Veritabanı tarafından desteklenen maksimum karakter sayısı 255\'tir. Siteniz Unicode gibi çoklu-bit karakterini destekliyorsa, UTF-8 veya ISO-8599-x olmayan karakter türleri için maksimumu hesaplamak için şu formülü kullanabilirsiniz :<br/><tt>round_down(255/(karakter başına maksimum karakter türü bit boyutu))</tt><br/> Örneğin karakter boyutu 4 olan UTF-8 için maksimum karakter sayısı: 255/4=63\'tür.');
DEFINE('_LATEST_THREADFORUM', 'Başlık/Forum');
DEFINE('_LATEST_NUMBER', 'Yeni Gönderiler');
DEFINE('_COM_A_SHOWNEW', 'Yeni Gönderileri Göster');
DEFINE('_COM_A_SHOWNEW_DESC', '&quot;Evet&quot;i seçerseniz, Kunena kullanıcıya hangi kategorilerin yeni gönderi içerdiğini ve hangi gönderilerin son ziyaretten sonra yollandığını bir işaretle gösterecektir.');
DEFINE('_COM_A_NEWCHAR', '&quot;Yeni&quot; imgesi');
DEFINE('_COM_A_NEWCHAR_DESC', 'Yeni gönderileri imgelemek için ne kullanılacağını tanımlayınız (Örnek : &quot;!&quot; veya &quot;YENİ!&quot;)');
DEFINE('_LATEST_AUTHOR', 'Son gönderi yazarı');
DEFINE('_GEN_FORUM_NEWPOST', 'Yeni Gönderiler');
DEFINE('_GEN_FORUM_NOTNEW', 'Yeni Gönderi Yok');
DEFINE('_GEN_UNREAD', 'Okunmamış Başlık');
DEFINE('_GEN_NOUNREAD', 'Okunmuş Başlık');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'Tümünü Okunmuş Olarak İşaretle');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'Kategoriyi Okunmuş Olarak İşaretle');
DEFINE('_GEN_FORUM_MARKED', 'Kategorideki tüm gönderiler okunmuş olarak işaretlendi');
DEFINE('_GEN_ALL_MARKED', 'Tüm gönderiler okunmuş olarak işaretlendi');
DEFINE('_IMAGE_UPLOAD', 'Resim Yükle');
DEFINE('_IMAGE_DIMENSIONS', 'Yükleyeceğiniz resim maksimum (genişlik x yükseklik - boyut)');
DEFINE('_IMAGE_ERROR_TYPE', 'Lütfen sadece jpeg, gif veya png türlerini kullanın');
DEFINE('_IMAGE_ERROR_EMPTY', 'Yükleme için öncelikle bir dosya seçin');
DEFINE('_IMAGE_ERROR_SIZE', 'Resmin boyutu belirlenen sınırların dışında.');
DEFINE('_IMAGE_ERROR_WIDTH', 'Resmin genişliği belirlenen sınırların dışında.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'Resmin yüksekliği belirlenen sınırların dışında.');
DEFINE('_IMAGE_UPLOADED', 'Resminiz yüklendi.');
DEFINE('_COM_A_IMAGE', 'Resimler');
DEFINE('_COM_A_IMGHEIGHT', 'Maks. Resim Yüksekliği');
DEFINE('_COM_A_IMGWIDTH', 'Maks. Resim Genişliği');
DEFINE('_COM_A_IMGSIZE', 'Maks. Resim Boyutu<br/><em>kb olarak</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'Herkezin resim yüklemesine izin ver');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'Herkesin resim yükleyebilmesini istiyorsanız, &quot;Evet&quot;i seçiniz.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'Kayıtlı kullanıcıların resim yüklemesine izin ver');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'Sadece kayıtlı kullanıcıların resim yüklemesini istiyorsanız, &quot;Evet&quot;i seçiniz.<br/> Not: Yöneticiler ve yetkililer her zaman resim yükleyebilir.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'Yüklemeler');
DEFINE('_FILE_TYPES', 'Dosya Tipi - Maks. Boyut');
DEFINE('_FILE_ERROR_TYPE', 'Sadece bu tür dosyaları yükleyebilirsiniz:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Yüklemeden önce bir dosya seçiniz');
DEFINE('_FILE_ERROR_SIZE', 'Dosya boyutu belirlenen maksimum boyutu aşmaktadır.');
DEFINE('_COM_A_FILE', 'Dosyalar');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'İzin verilen türler');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'Yükleme için izin verilen türleri belirtiniz. Virgülle ayırıp <strong>küçük</strong> harfle arada boşluk olmadan listeleyin.<br />Örnek: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'Maks. Dosya boyutu<br/><em>kb olarak</em>');
DEFINE('_COM_A_FILEUPLOAD', 'Herkesin Dosya Yüklemesine İzin Ver');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'Herkesin dosya yükleyebilmesini istiyorsanız &quot;Evet&quot;i seçiniz.');
DEFINE('_COM_A_FILEREGUPLOAD', 'Kayıtlı kullanıcıların dosya yüklemesine izin ver');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'Sadece kayıtlı kullanıcıların yükleme yapmasını istiyorsanız &quot;Evet&quot;i seçiniz.<br/> Not: Yöneticiler ve yetkililer her zaman dosya yükleyebilir.');
DEFINE('_SUBMIT_CANCEL', 'Gönderi eklemeniz iptal edildi.');
DEFINE('_HELP_SUBMIT', 'Mesajınızı eklemek için tıklayınız');
DEFINE('_HELP_PREVIEW', 'Mesajınızın görünümünü görmek için tıklayın');
DEFINE('_HELP_CANCEL', 'Mesajınızı iptal etmek için tıklayın');
DEFINE('_POST_DELETE_ATT', 'Bu kutucuğu işaretlerseniz, silinen gönderideki tüm ekler de silinecektir.(önerilen).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'Değiştirilmiş İşaretini Göster');
DEFINE('_COM_A_USER_MARKUP_DESC', 'Düzenlenen gönderinin, kim tarafından ve ne zaman düzenlendiğini gösteren bir metinle işaretlenmesini istiyorsanız &quot;Evet&quot;i seçiniz.');
DEFINE('_EDIT_BY', 'Düzenleyen:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'Miniresim yüklenirken bir sorun oluştu. Lütfen tekrar deneyin veya yöneticiyi uyarın.');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'Yüklenmiş resim gezgini');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'Yüklenmiş dosya gezgini');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'Yüklenmiş resim sayısı');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'Yüklenmiş dosya sayısı');
DEFINE('_COM_A_IMGB_ENLARGE', 'Gerçek boyutlarında görmek için tıklayın');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'İndirmek için tıklayın');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    '&quot;Sahte Resimle Değiştir&quot;i seçerseniz, seçili olan resmin yerine sahte bir resim geçecek ve böylece mesaja zarar vermeden resmi silmenize olanak sağlanacaktır.<br /><small><em>Değişikliği görebilmeniz için tarayıcınızı yenilemeniz gerekebilir.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'Geçerli sahte resim');
DEFINE('_COM_A_IMGB_REPLACE', 'Sahte resimle değiştir');
DEFINE('_COM_A_IMGB_REMOVE', 'Tamamen kaldır');
DEFINE('_COM_A_IMGB_NAME', 'isim');
DEFINE('_COM_A_IMGB_SIZE', 'Dosya boyutu');
DEFINE('_COM_A_IMGB_DIMS', 'Boyutlar');
DEFINE('_COM_A_IMGB_CONFIRM', 'Bu dosyayı silmek istediğinizden emin misiniz? \n Dosyayı silmek içinde bulunduğu gönderiye zarar verebilir...');
DEFINE('_COM_A_IMGB_VIEW', 'Gönderiyi düzenlemek için aç');
DEFINE('_COM_A_IMGB_NO_POST', 'İlgili gönderi yok!');
DEFINE('_USER_CHANGE_VIEW', 'Ayar değişiklikleri forumda bir sonraki ziyaretinizde etkili olacaktır. Görüntü tipini değiştirmek için, forum menüsünden ilgili seçenekleri kullanabilirsiniz.');
DEFINE('_MOSBOT_DISCUSS_A', 'Bu yazıyı forumda tartış. (');
DEFINE('_MOSBOT_DISCUSS_B', '&#32;gönderiler)');
DEFINE('_POST_DISCUSS', 'Bu başlık içerik tatışmasıdır');
DEFINE('_COM_A_RSS', 'RSS desteğini etkinleştir');
DEFINE('_COM_A_RSS_DESC', 'RSS Desteği, kullanıcıların gelen son gönderileri masaüstüne indirmelerini sağlar./RSS Okuyucu (bkz. <a href="http://www.rssreader.com" target="_blank">rssreader.com</a>');
DEFINE('_LISTCAT_RSS', 'gönderileri masaüstünüze alın');
DEFINE('_SEARCH_REDIRECT', 'Arama talebinizi gerçekleştirmeden önce erişiminizi yenileyiniz.\nEndişelenmeyin, bu 30 dakikadır işlem yapılmadığı için normaldir.\nLütfen arama isteğinizi yineleyin.');
//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'Kunena Yapılandırması');
DEFINE('_COM_A_DISPLAY', 'Görüntüle #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'Geçerli Ayarlar');
DEFINE('_COM_A_EXPLANATION', 'Açıklama');
DEFINE('_COM_A_BOARD_TITLE', 'Forum Başlığı');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'Forum ismi');
//Removed Threaded View Option - No longer support in Kunena - It has been broken for years
//DEFINE('_COM_A_VIEW_TYPE', 'Default View type');
//DEFINE('_COM_A_VIEW_TYPE_DESC', 'Düz veya köklü izlemeden birini seçiniz');
DEFINE('_COM_A_THREADS', 'Sayfa Başına Başlık');
DEFINE('_COM_A_THREADS_DESC', 'Sayfa başına gösterilecek başlık sayısı');
DEFINE('_COM_A_REGISTERED_ONLY', 'Sadece Kayıtlı Kullanıcılar');
DEFINE('_COM_A_REG_ONLY_DESC', '&quot;Evet&quot;i seçerseniz, sadece kayıtlı kullanıcılar forumu görebilir ve gönderi yollayabilir. &quot;Hayır&quot;ı seçerseniz ziyaretçiler de forumu kullanabilir.');
DEFINE('_COM_A_PUBWRITE', 'Herkes Okur/Yazar');
DEFINE('_COM_A_PUBWRITE_DESC', '&quot;Evet&quot;i seçerseniz, foruma herkes yazı yollayabilir. Herhangi bir ziyaretçinin mesajları görmesini, fakat sadece kayıtlı kullanıcıların mesaj gönderebilmesini istiyorsanız &quot;Hayır&quot;ı seçiniz.');
DEFINE('_COM_A_USER_EDIT', 'Kullanıcı Düzenlemeleri');
DEFINE('_COM_A_USER_EDIT_DESC', 'Kayıtlı kullanıcıların gönderilerini düzenlemelerine izin vermek için &quot;Evet&quot; i seçin.');
DEFINE('_COM_A_MESSAGE', 'Yapılan değişiklikleri kaydetmek için, yukarıdaki &quot;Kaydet&quot;e basınız.');
DEFINE('_COM_A_HISTORY', 'Geçmişi Göster');
DEFINE('_COM_A_HISTORY_DESC', 'Cevap vermek/alıntı yapmak istendiğinde konunun görünmesini istiyorsanız &quot;Evet&quot; i seçiniz');
DEFINE('_COM_A_SUBSCRIPTIONS', 'Aboneliğe İzin Ver');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'Kayıtlı kullanıcıların bir konuya abone olmalarını ve gelen yeni postalar için e-posta ile haberdar edilmelerini istiyorsanız &quot;Evet&quot;i seçiniz');
DEFINE('_COM_A_HISTLIM', 'Geçmiş Sınırı');
DEFINE('_COM_A_HISTLIM_DESC', 'Geçmiş gönderi sayısını sınırla');
DEFINE('_COM_A_FLOOD', 'Tekrarlı Gönderim Koruması');
DEFINE('_COM_A_FLOOD_DESC', 'Kullanıcı artarda iki gönderi için beklemelidir. Bu korumayı kapatmak için ayarı 0 (sıfır) yapınız. NOT: Bu koruma, forum performansının düşmesine neden olabilir.');
DEFINE('_COM_A_MODERATION', 'Yetkililere Eposta');
DEFINE('_COM_A_MODERATION_DESC',
    'Yeni gönderilerin forum yetkililerine eposta ile bildirilmesini istiyorsanız &quot;Evet&quot; i seçiniz. Not: Tüm yöneticilerin de yetkili olarak değerlendirildiğini unutmayınız!');
DEFINE('_COM_A_SHOWMAIL', 'Epostayı Göster');
DEFINE('_COM_A_SHOWMAIL_DESC', 'Kullanıcı eposta adreslerinin, kayıtlı kullanıcılara bile gösterilmesini istemiyorsanız, &quot;Hayır&quot; ı seçiniz.');
DEFINE('_COM_A_AVATAR', 'Miniresme İzin Ver');
DEFINE('_COM_A_AVATAR_DESC', 'Kayıtlı kullanıcıların miniresmi olsun istiyorsanız &quot;Evet&quot; i seçiniz.');
DEFINE('_COM_A_AVHEIGHT', 'Maks. Miniresim Yüksekliği');
DEFINE('_COM_A_AVWIDTH', 'Maks. Miniresim Genişliği');
DEFINE('_COM_A_AVSIZE', 'Maks. Miniresim Boyutu<br/><em>kb</em>');
DEFINE('_COM_A_USERSTATS', 'Kullanıcı İstatistiklerini Göster');
DEFINE('_COM_A_USERSTATS_DESC', 'Gönderi sayısı ve kullanıcı türü gibi kullanıcı istatistiklerini açmak için &quot;Evet&quot; i seçiniz.).');
DEFINE('_COM_A_AVATARUPLOAD', 'Miniresim Yüklemeye İzin Ver');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'Kayıtlı kullanıcıların miniresim yüklemesine izin vermek için &quot;Evet&quot; i seçiniz.');
DEFINE('_COM_A_AVATARGALLERY', 'Miniresim Galerisini Kullan');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'Kayıtlı kullanıcıların avatar galerisinden seçim yapmasını istiyorsanız &quot;Evet&quot; i seçiniz (components/com_kunena/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'Kayıtlı kullanıcıları gönderilerine göre seviyelendirmek isterseniz &quot;Evet&quot; i seçiniz.  <br/><strong>Not: Kullanıcı istatistiklerinin aktif edilmiş olması gerekmektedir.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'Seviye Resmi Kullan');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'Kayıtlı kullanıcıların seviyelerini resimle göstermek için &quot;Evet&quot; i seçiniz (components/com_kunena/ranks). Seviyelendirme resimleri için www.kunena.com\'daki bilgileri inceleyiniz.');

//email and stuff
$_COM_A_NOTIFICATION = "Yeni gönderi uyarısı";
$_COM_A_NOTIFICATION1 = "Abone olduğunuz başlıkta yeni gönderi var";
$_COM_A_NOTIFICATION2 = "Giriş yaptıktan sonra 'Profilim' bağlantısından aboneliklerinizi yönetebilirsiniz.";
$_COM_A_NOTIFICATION3 = "Bu eposta otomatik olarak oluşturulmuştur, cevaplamayınız.";
$_COM_A_NOT_MOD1 = "Yetkilisi olduğunuz forumda yeni bir gönderi var";
$_COM_A_NOT_MOD2 = "Giriş yaptıktan sonra lütfen bakınız.";
DEFINE('_COM_A_NO', 'Hayır');
DEFINE('_COM_A_YES', 'Evet');
DEFINE('_COM_A_FLAT', 'Düz');
DEFINE('_COM_A_THREADED', 'Köklü');
DEFINE('_COM_A_MESSAGES', 'Sayfa başına mesaj');
DEFINE('_COM_A_MESSAGES_DESC', 'Sayfa başına gösterilen mesaj sayısı');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'Kullanıcı Adı');
DEFINE('_COM_A_USERNAME_DESC', 'Kullanıcı adınızın gerçek isminizin yerine kullanılmasını istiyorsanız &quot;Evet&quot; i seçiniz');
DEFINE('_COM_A_CHANGENAME', 'İsim Değiştirmesine İzin Ver');
DEFINE('_COM_A_CHANGENAME_DESC', 'Kayıtlı kullanıcıların gönderilerde isimlerini değiştirmelerine izin vermek istiyorsanız &quot;Hayır&quot;, ı seçerseniz, kayıtlı kullanıcılar isimlerini değiştiremeyeceklerdir.');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'Forum kapalı');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'Forumu kapatmak istiyorsanız, &quot;Evet&quot; i seçiniz. Forumu sadece süper yöneticiler gezebilecektir.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'Forum kapalı mesajı');
DEFINE('_COM_A_PRUNE', 'Forum Temizliği');
DEFINE('_COM_A_PRUNE_NAME', 'Temizlenecek forum:');
DEFINE('_COM_A_PRUNE_DESC',
    'Forum temizliği ile, belirttiğiniz süre içinde hiç yeni gönderiye sahip olmayan konular silinecektir. Ancak sabitlenmiş ve kilitlenmiş konuları elinizle silmeniz gerekmektedir.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'Belirtilen süre&#32;');
DEFINE('_COM_A_PRUNE_DAYS', 'gün');
DEFINE('_COM_A_PRUNE_USERS', 'Kullanıcı Temizliği'); // <=FB 1.0.3
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'Kullanıcı temizliği ile forumunuzda uzun süredir aktif olmayan kullanıcılar, sitenizin Joomla! veri tabanından kaldırılacaktır.<br/>Devam etmek istediğinizden eminseniz, üstteki butonlardan &quot;Temizle&quot; ye tıklayınız.'); // <=FB 1.0.3
//general
DEFINE('_GEN_ACTION', 'Hareket');
DEFINE('_GEN_AUTHOR', 'Yazar');
DEFINE('_GEN_BY', 'by');
DEFINE('_GEN_CANCEL', 'İptal');
DEFINE('_GEN_CONTINUE', 'Ekle');
DEFINE('_GEN_DATE', 'Tarih');
DEFINE('_GEN_DELETE', 'Sil');
DEFINE('_GEN_EDIT', 'Düzenle');
DEFINE('_GEN_EMAIL', 'Eposta');
DEFINE('_GEN_EMOTICONS', 'İfadeler');
DEFINE('_GEN_FLAT', 'Düz');
DEFINE('_GEN_FLAT_VIEW', 'Düz Görünüm');
DEFINE('_GEN_FORUMLIST', 'Forum Listesi');
DEFINE('_GEN_FORUM', 'Forum');
DEFINE('_GEN_HELP', 'Yardım');
DEFINE('_GEN_HITS', 'İzlenim');
DEFINE('_GEN_LAST_POST', 'Son Gönderi');
DEFINE('_GEN_LATEST_POSTS', 'Son gönderiler');
DEFINE('_GEN_LOCK', 'Kilitle');
DEFINE('_GEN_UNLOCK', 'Kilidi aç');
DEFINE('_GEN_LOCKED_FORUM', 'Forum kilitli');
DEFINE('_GEN_LOCKED_TOPIC', 'Başlık kilitli');
DEFINE('_GEN_MESSAGE', 'Mesaj');
DEFINE('_GEN_MODERATED', 'Bu forum denetlenmektedir, yeni gönderiler yayınlanmadan önce kontrol edilecektir.');
DEFINE('_GEN_MODERATORS', 'Yetkililer');
DEFINE('_GEN_MOVE', 'Taşı');
DEFINE('_GEN_NAME', 'İsim');
DEFINE('_GEN_POST_NEW_TOPIC', 'Yeni Başlık');
DEFINE('_GEN_POST_REPLY', 'Cevapla');
DEFINE('_GEN_MYPROFILE', 'Profilim');
DEFINE('_GEN_QUOTE', 'Alıntı');
DEFINE('_GEN_REPLY', 'Cevap');
DEFINE('_GEN_REPLIES', 'Cevapla');
DEFINE('_GEN_THREADED', 'Köklü');
DEFINE('_GEN_THREADED_VIEW', 'Köklü görünüm');
DEFINE('_GEN_SIGNATURE', 'İmza');
DEFINE('_GEN_ISSTICKY', 'Sabit konu.');
DEFINE('_GEN_STICKY', 'Sabitle');
DEFINE('_GEN_UNSTICKY', 'Sabitleme');
DEFINE('_GEN_SUBJECT', 'Konu');
DEFINE('_GEN_SUBMIT', 'Ekle');
DEFINE('_GEN_TOPIC', 'Başlık');
DEFINE('_GEN_TOPICS', 'Başlıklar');
DEFINE('_GEN_TOPIC_ICON', 'başlık simgesi');
DEFINE('_GEN_SEARCH_BOX', 'Forumda Ara');
$_GEN_THREADED_VIEW = "Köklü görünüm";
$_GEN_FLAT_VIEW = "Düz görünüm";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Yükle');
DEFINE('_UPLOAD_DIMENSIONS', 'Resim dosyanız en fazla (genişlik x yükseklik - boyut)');
DEFINE('_UPLOAD_SUBMIT', 'Yükleme için yeni miniresim ekle');
DEFINE('_UPLOAD_SELECT_FILE', 'Dosya seç');
DEFINE('_UPLOAD_ERROR_TYPE', 'Sadece jpeg, gif veya png türlerini kullanınız');
DEFINE('_UPLOAD_ERROR_EMPTY', 'Yüklemeden önce bir dosya seçiniz');
DEFINE('_UPLOAD_ERROR_NAME', 'Resim dosyasının ismi sadece alfanumerik karakterler içermeli ve boşluk içermemelidir.');
DEFINE('_UPLOAD_ERROR_SIZE', 'Resim boyutu belirlenen değerden büyük.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'Resim genişliği belirlenen değerden fazla.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'Resim yüksekliği belirlenen değerden fazla.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "Galeriden miniresim seçmediniz...");
DEFINE('_UPLOAD_UPLOADED', 'Miniresminiz yüklendi.');
DEFINE('_UPLOAD_GALLERY', 'Miniresim galerisinden seçim yapın:');
DEFINE('_UPLOAD_CHOOSE', 'Seçimi Onayla.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'Yönetici kategori oluşturmalıdır&#32;');
DEFINE('_LISTCAT_DO', 'Yönetici ne yapılacağını bilir&#32;');
DEFINE('_LISTCAT_INFORM', 'Yöneticiyi acele etmesi konusunda bilgilendirin!');
DEFINE('_LISTCAT_NO_CATS', 'Henüz forum kategori içermemekedir.');
DEFINE('_LISTCAT_PANEL', 'Site Yönetim Paneli.');
DEFINE('_LISTCAT_PENDING', 'beklemedeki mesaj(lar)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'Forumda beklemede olan mesaj yok.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'İletiyi silmek üzeresiniz');
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTLAR:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Konuyu forumdan silmek isterseniz (konudaki ilk gönderi) tüm alt mesajlar da silinecektir..
<br/>
- Silme işlemi sonrasında tüm mesajlar bir yukarı çıkacaktır.');
DEFINE('_POST_CLICK', 'buraya tıklayın');
DEFINE('_POST_ERROR', 'Kullanıcı adı/eposta bulunamadı. Ciddi veritabanı hatası listelenmedi');
DEFINE('_POST_ERROR_MESSAGE', 'Bilinmeyen bir SQL hatası oluştu ve mesajınız gönderilmedi. Sorun devam ederse, Yönetici ile iletişime geçin.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'Bir hata oluştu ve mesaj güncellenmedi.  Lütfen tekrar deneyin. Sorun devam ederse, Yönetici ile iletişime geçin.');
DEFINE('_POST_ERROR_TOPIC', 'Silme sırasında bir hata oluştu. Aşağıdaki hataları kontrol ediniz:');
DEFINE('_POST_FORGOT_NAME', 'İsim eklemediniz. Tarayıcınızın&#146s geri tuşuna basıp geri dönün ve yeniden deneyin.');
DEFINE('_POST_FORGOT_SUBJECT', 'Konu eklemediniz. Tarayıcınızın&#146s geri tuşuna basıp geri dönün ve yeniden deneyin.');
DEFINE('_POST_FORGOT_MESSAGE', 'Mesaj eklemediniz. Tarayıcınızın&#146s geri tuşuna basıp geri dönün ve yeniden deneyin.');
DEFINE('_POST_INVALID', 'Geçersiz bir gönderi ID\'si istendi.');
DEFINE('_POST_LOCK_SET', 'Konu kilitlendi.');
DEFINE('_POST_LOCK_NOT_SET', 'Bu konu kilitlenemez.');
DEFINE('_POST_LOCK_UNSET', 'Konu kilidi açıldı.');
DEFINE('_POST_LOCK_NOT_UNSET', 'Bu konunun kilidi açılamaz.');
DEFINE('_POST_MESSAGE', 'Yeni mesaj gönder&#32;');
DEFINE('_POST_MOVE_TOPIC', 'Konuyu taşı&#32;');
DEFINE('_POST_NEW', 'Yeni mesaj gönder:&#32;');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Bu konu için abonelik talebiniz işleme alınamadı.');
DEFINE('_POST_NOTIFIED', 'Beni bu başlığa gelen cevaplardan haberdar et.');
DEFINE('_POST_STICKY_SET', 'Sabitleme yapılmıştır.');
DEFINE('_POST_STICKY_NOT_SET', 'Sabitleme yapılmaz.');
DEFINE('_POST_STICKY_UNSET', 'Sabitleme kaldırıldı.');
DEFINE('_POST_STICKY_NOT_UNSET', 'Sabitleme kaldırılamaz.');
DEFINE('_POST_SUBSCRIBE', 'abone ol');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'Başlığa abone oldunuz.');
DEFINE('_POST_SUCCESS', 'Mesajınız başarılı');
DEFINE('_POST_SUCCES_REVIEW', 'Mesajınız başarıyla gönderildi. Mesajınız yayınlanmadan önce yetkililerce kontrol edilecektir.');
DEFINE('_POST_SUCCESS_REQUEST', 'İsteğiniz işleme alınmıştır. Bu sayfa kısa bir süre içinde konuya geri dönmezse,');
DEFINE('_POST_TOPIC_HISTORY', 'Başlık Geçmişi');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'Maks. son gönderilen');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'gönderiler  -  <i>(Son gönderi ilk)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Konu taşınamadı. Geri dönmek için:');
DEFINE('_POST_TOPIC_FLOOD1', 'Tekrarlı gönderim koruması etkin olduğundan yeni gönderi yollamak için&#32;');
DEFINE('_POST_TOPIC_FLOOD2', '&#32;saniye beklemelisiniz.');
DEFINE('_POST_TOPIC_FLOOD3', 'Foruma geri dönmek için&#146s geri tuşuna basınız.');
DEFINE('_POST_EMAIL_NEVER', 'e-posta adresiniz sitede asla gösterilmeyecektir.');
DEFINE('_POST_EMAIL_REGISTERED', 'e-postanızı sadece kayıtlı kullanıcılar görebilecektir.');
DEFINE('_POST_LOCKED', 'Yönetici tarafından kilitlendi.');
DEFINE('_POST_NO_NEW', 'Yeni cevaplara izin verilmemektedir.');
DEFINE('_POST_NO_PUBACCESS1', 'Sadece kayıtlı kullanıcılar yazı yazabilir.');
DEFINE('_POST_NO_PUBACCESS2', 'Sadece kayıtlı kullanıcılar<br /> ekleme yapabilir.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> Forumda henüz başlık yok <<');
DEFINE('_SHOWCAT_PENDING', 'beklemedeki mesaj(lar)');
// userprofile.php
DEFINE('_USER_DELETE', '&#32;imzanızı silmek için işaretleyin');
DEFINE('_USER_ERROR_A', 'Hata sonucu bu sayfaya ulaştınız. Lütfen yöneticiyi&#32;');
DEFINE('_USER_ERROR_B', 'hangi bağlantıya tıklayarak buraya geldiğiniz konusunda bilgilendiriniz.');
DEFINE('_USER_ERROR_C', 'Teşekkürler!');
DEFINE('_USER_ERROR_D', 'Rapordaki hata numarası:&#32;');
DEFINE('_USER_GENERAL', 'Genel Profil Seçenekleri');
DEFINE('_USER_MODERATOR', 'Forum yetkilisi olarak atandınız');
DEFINE('_USER_MODERATOR_NONE', 'Atandığınız forum yok');
DEFINE('_USER_MODERATOR_ADMIN', 'Site yöneticileri tüm forumlarda yetkilidir.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'Sizle ilgili kayıt bulunamadı');
//DEFINE('_USER_PREFERED', 'Tercihi Görüntü Tipi');
DEFINE('_USER_PROFILE', 'Profil&#32;');
DEFINE('_USER_PROFILE_NOT_A', 'Profiliniz&#32;');
DEFINE('_USER_PROFILE_NOT_B', 'edilmedi');
DEFINE('_USER_PROFILE_NOT_C', '&#32;güncellendi.');
DEFINE('_USER_PROFILE_UPDATED', 'Profiliniz güncellendi.');
DEFINE('_USER_RETURN_A', 'Sayfa kısa bir süre içinde profilinize geri dönmezse&#32;');
DEFINE('_USER_RETURN_B', 'buraya tıklayın');
DEFINE('_USER_SUBSCRIPTIONS', 'Abonelikleriniz');
DEFINE('_USER_UNSUBSCRIBE', 'Abonelik iptali');
DEFINE('_USER_UNSUBSCRIBE_A', 'Siz&#32;');
DEFINE('_USER_UNSUBSCRIBE_B', 'Edemediniz');
DEFINE('_USER_UNSUBSCRIBE_C', '&#32;başlık aboneliğinden çıktınız.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'Başlık abonelği iptal edildi.');
DEFINE('_USER_DELETEAV', '&#32;miniresminizi silmek için kutucuğa tıklayın');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'Tercihli İleti Sıralaması');
DEFINE('_USER_ORDER_DESC', 'Son gönderi başta');
DEFINE('_USER_ORDER_ASC', 'İlk gönderi başta');
// view.php
DEFINE('_VIEW_DISABLED', 'Herkezin yazı yazması yönetici tarafından engellenmiştir.');
DEFINE('_VIEW_POSTED', 'Gönderen');
DEFINE('_VIEW_SUBSCRIBE', ':: Başlığa abone ol ::');
DEFINE('_MODERATION_INVALID_ID', 'Geçersiz gönderi ID\'si istendi.');
DEFINE('_VIEW_NO_POSTS', 'Kategoride gönderi yok.');
DEFINE('_VIEW_VISITOR', 'Ziyaretçi');
DEFINE('_VIEW_ADMIN', 'Yönetici');
DEFINE('_VIEW_USER', 'Kullanıcı');
DEFINE('_VIEW_MODERATOR', 'Yetkili');
DEFINE('_VIEW_REPLY', 'İletiyi cevapla');
DEFINE('_VIEW_EDIT', 'İletiyi değiştir');
DEFINE('_VIEW_QUOTE', 'Alıntı yap');
DEFINE('_VIEW_DELETE', 'İletiyi sil');
DEFINE('_VIEW_STICKY', 'Başlığı sabitle');
DEFINE('_VIEW_UNSTICKY', 'Başlığı sabitleme');
DEFINE('_VIEW_LOCK', 'Başlığı kilitle');
DEFINE('_VIEW_UNLOCK', 'Başlığı kilitleme');
DEFINE('_VIEW_MOVE', 'Başlığı başka bir foruma taşı');
DEFINE('_VIEW_SUBSCRIBETXT', 'Başlığa abone ol ve yeni gönderileri e-posta ile bildir');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'Ana Sayfa');
DEFINE('_POSTS', 'Gönderiler:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Gönderi');
DEFINE('_FORUM_NOT_ALLOWED', 'Forum');
DEFINE('_FORUM_IS_OFFLINE', 'Forum KAPALI!');
DEFINE('_PAGE', 'Sayfa:&#32;');
DEFINE('_NO_POSTS', 'Gönderi Yok');
DEFINE('_CHARS', 'maks. karakter');
DEFINE('_HTML_YES', 'HTML geçersiz');
DEFINE('_YOUR_AVATAR', '<b>Miniresminiz</b>');
DEFINE('_NON_SELECTED', 'Seçim yapılmadı <br />');
DEFINE('_SET_NEW_AVATAR', 'Yeni miniresim seç');
DEFINE('_THREAD_UNSUBSCRIBE', 'Abonelik iptali');
DEFINE('_SHOW_LAST_POSTS', 'En son etkin başlık');
DEFINE('_SHOW_HOURS', 'saat');
DEFINE('_SHOW_POSTS', 'Toplam:&#32;');
DEFINE('_DESCRIPTION_POSTS', 'Aktif başlıklar için yeni gönderiler');
DEFINE('_SHOW_4_HOURS', '4 Saat');
DEFINE('_SHOW_8_HOURS', '8 Saat');
DEFINE('_SHOW_12_HOURS', '12 Saat');
DEFINE('_SHOW_24_HOURS', '24 Saat');
DEFINE('_SHOW_48_HOURS', '48 Saat');
DEFINE('_SHOW_WEEK', 'Hafta');
DEFINE('_POSTED_AT', 'Saat');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'Seçtiğiniz zaman diliminde yeni gönderi yok.');
DEFINE('_MESSAGE', 'Mesaj');
DEFINE('_NO_SMILIE', 'hayır');
DEFINE('_FORUM_UNAUTHORIZIED', 'Forum sadece kayıtlı kullanıcılara açıktır.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'Kayıtlı iseniz, lütfen giriş yapınız.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Yönetim');
DEFINE('_MOD_APPROVE', 'Onayla');
DEFINE('_MOD_DELETE', 'Sil');
//NEW in RC1
DEFINE('_SHOW_LAST', 'Son mesajları göster');
DEFINE('_POST_WROTE', 'yazan');
DEFINE('_COM_A_EMAIL', 'Forum Eposta Adresi');
DEFINE('_COM_A_EMAIL_DESC', 'Forumun eposta adresidir. Geçerli bir eposta girin');
DEFINE('_COM_A_WRAP', 'Şundan uzun kelimeleri kırp');
DEFINE('_COM_A_WRAP_DESC',
    'Tek bir kelimenin sahip olabileceği en fazla karakter sayısını giriniz. <br/> Tema için uygun olan en fazla karakter sayısı genelde 70 dir.<br/>URL\'lerin uzunluğu önemli değildir, kırpılmazlar.');
DEFINE('_COM_A_SIGNATURE', 'İmza uzunluğu');
DEFINE('_COM_A_SIGNATURE_DESC', 'Kullanıcı imzasında izin verilen maksimum karakter sayısıdır.');
DEFINE('_SHOWCAT_NOPENDING', 'Beklemede mesaj(lar) yok');
DEFINE('_COM_A_BOARD_OFSET', 'Forum zaman ayarı');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'Sunucu üzerindeki forumlar bazen kullanıcınınkinden farklı olabilir. Saat farkını hesap edip, saat cinsinden giriş yapmalısınız. Pozitif ve negatif sayılar kullanılabilir.');
//New in RC2
DEFINE('_COM_A_BASICS', 'Temel');
DEFINE('_COM_A_FRONTEND', 'Önplan');
DEFINE('_COM_A_SECURITY', 'Güvenlik');
DEFINE('_COM_A_AVATARS', 'Miniresim');
DEFINE('_COM_A_INTEGRATION', 'Uyumluluk');
DEFINE('_COM_A_PMS', 'Özel mesajlaşma aktif');
DEFINE('_COM_A_PMS_DESC',
    'Özel mesajlaşma için yüklenmiş bileşenlerden seçim yapınız. Clexus PM i seçerseniz, Clexus PM kullanıcı profili çeşitli özellikleri ile aktif olacaktır (like ICQ, AIM, Yahoo, MSN and profile links if supported by the Kunena template used.');
DEFINE('_VIEW_PMS', 'Kullanıcıya Ö.M yollamak için tıklayın');
//new in RC3
DEFINE('_POST_RE', 'Cvp:');
DEFINE('_BBCODE_BOLD', 'Kalın metin: [b]text[/b]&#32;');
DEFINE('_BBCODE_ITALIC', 'Eğik metin: [i]text[/i]');
DEFINE('_BBCODE_UNDERL', 'Altıçizili metin: [u]metin[/u]');
DEFINE('_BBCODE_QUOTE', 'Alıntı metni: [quote]metin[/quote]');
DEFINE('_BBCODE_CODE', 'Kod görüntüleme: [code]kod[/code]');
DEFINE('_BBCODE_ULIST', 'Sırasız Liste: [ul] [li]metin[/li] [/ul] - İpucu: liste listeleme işareti içermelidir');
DEFINE('_BBCODE_OLIST', 'Sıralı Liste: [ol] [li]metin[/li] [/ol] - İpucu: liste listeleme işareti içermelidir');
DEFINE('_BBCODE_IMAGE', 'Resim: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'Link: [url=http://www.joomlatr.org/]Bu bir baglantıdır[/url]');
DEFINE('_BBCODE_CLOSA', 'Tüm etiketleri kapa');
DEFINE('_BBCODE_CLOSE', 'Tüm bbCode etiketlerini kapa');
DEFINE('_BBCODE_COLOR', 'Color: [color=#FF6600]metin[/color]');
DEFINE('_BBCODE_SIZE', 'Size: [size=1]metin boyutu[/size] - İpucu: boyut aralığı 1 to 5');
DEFINE('_BBCODE_LITEM', 'Listeleme İşareti: [li] liste işareti [/li]');
DEFINE('_BBCODE_HINT', 'bbCode Yardım - İpucu: Seçilmiş metinde bbCode kullanılabilir!');
DEFINE('_COM_A_TAWIDTH', 'Metin Alanı Genişliği');
DEFINE('_COM_A_TAWIDTH_DESC', 'Metin alanı genişliğini temanız ile eşleştiriniz. <br/>Genişlik 420 pikselden küçük olursa, ifade ekleme çubuğu iki satır olacaktır');
DEFINE('_COM_A_TAHEIGHT', 'Metin Alanı Yüksekliği');
DEFINE('_COM_A_TAHEIGHT_DESC', 'Metin alanı yüksekliğini temanız ile eşleştiriniz');
DEFINE('_COM_A_ASK_EMAIL', 'Eposta Gerekli');
DEFINE('_COM_A_ASK_EMAIL_DESC', '&quot;Evet&quot;i seçerseniz, kullanıcı veya ziyaretçi gönderi yapmak için epostasını girmelidir. &quot;Hayır&quot;ı seçerseniz, bu özellik atlanacaktır.');

//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Seviye Yönetimi');
define('_KUNENA_SORTRANKS', 'Seviyeye Göre Sırala');

define('_KUNENA_RANKSIMAGE', 'Resim');
define('_KUNENA_RANKS', 'Seviye Başlığı');
define('_KUNENA_RANKS_SPECIAL', 'Özel');
define('_KUNENA_RANKSMIN', 'En Az Gönderi Sayısı');
define('_KUNENA_RANKS_ACTION', 'Hareketler');
define('_KUNENA_NEW_RANK', 'Yeni Seviye');

?>
