<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;

// Kunena 5.2.0: Convert all configuration options to the news ones in K6.0
/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 5.2.4
 */
function kunena_5215_2021_07_24_update_configuration($parent) {
	$config = KunenaConfig::getInstance();

    //KunenaConfig does only automatically load parameters which are
    //available as member variables. To load the old values and convert
    //we need to load them manually and add them dynamically
    $db    = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->createQuery();
    $query->select('*')
        ->from($db->quoteName('#__kunena_configuration'))
        ->where($db->quoteName('id') . ' = 1');
    $db->setQuery($query);

    try {
        $config = $db->loadAssoc();
    } catch (ExecutionFailureException $e) {
        KunenaError::displayDatabaseError($e);
    }

    if ($config) {
        $params = json_decode($config['params']);
        if (\is_array($params) || \is_object($params)) {
            foreach ((array) $params as $k => $v) {
                // Use the set function which might be overridden.
                $config->$property = $value;
            }
        }
    }

	$config->boardTitle = $config->board_title;

	unset($config->board_title);

	$config->boardOffline = $config->board_offline;

	unset($config->board_offline);

	$config->offlineMessage = $config->offline_message;

	unset($config->offline_message);

	$config->enableRss = $config->enablerss;

	unset($config->enablerss);

	$config->threadsPerPage = $config->threads_per_page;

	unset($config->threads_per_page);

	$config->messagesPerPage = $config->messages_per_page;

	unset($config->messages_per_page);

	$config->messagesPerPageSearch = $config->messages_per_page_search;

	unset($config->messages_per_page_search);

	$config->showHistory = $config->showhistory;

	unset($config->showhistory);

	$config->historyLimit = $config->historylimit;

	unset($config->historylimit);

	$config->showNew = $config->shownew;

	unset($config->shownew);

	$config->disableEmoticons = $config->disemoticons;

	unset($config->disemoticons);

	$config->showAnnouncement = $config->showannouncement;

	unset($config->showannouncement);

	$config->avatarOnCategory = $config->avataroncat;

	unset($config->avataroncat);

	$config->showChildCatIcon = $config->showchildcaticon;

	unset($config->showchildcaticon);

	$config->rteWidth = $config->rtewidth;

	unset($config->rtewidth);

	$config->rteHeight = $config->rteheight;

	unset($config->rteheight);

	$config->enableForumJump = $config->enableforumjump;

	unset($config->enableforumjump);

	$config->reportMsg = $config->reportmsg;

	unset($config->reportmsg);

	$config->askEmail = $config->askemail;

	unset($config->askemail);

	$config->showEmail = $config->showemail;

	unset($config->showemail);

	$config->showUserStats = $config->showuserstats;

	unset($config->showuserstats);

	$config->showKarma = $config->showkarma;

	unset($config->showkarma);

	$config->userEdit = $config->useredit;

	unset($config->useredit);

	$config->userEditTime = $config->useredittime;

	unset($config->useredittime);

	$config->userEditTimeGrace = $config->useredittimegrace;

	unset($config->useredittimegrace);

	$config->editMarkup = $config->editmarkup;

	unset($config->editmarkup);

	$config->allowSubscriptions = $config->allowsubscriptions;

	unset($config->allowsubscriptions);

	$config->subscriptionsChecked = $config->subscriptionschecked;

	unset($config->subscriptionschecked);

	$config->allowFavorites = $config->allowfavorites;

	unset($config->allowfavorites);

	$config->maxSig = $config->maxsig;

	unset($config->maxsig);

	$config->regOnly = $config->regonly;

	unset($config->regonly);

	$config->pubWrite = $config->pubwrite;

	unset($config->pubwrite);

	$config->floodProtection = $config->floodprotection;

	unset($config->floodprotection);

	$config->mailModerators = $config->mailmod;

	unset($config->mailmod);

	$config->mailAdministrators = $config->mailadmin;

	unset($config->mailadmin);

	$config->mailFull = $config->mailfull;

	unset($config->mailfull);

	$config->allowAvatarUpload = $config->allowavatarupload;

	unset($config->allowavatarupload);

	$config->allowAvatarGallery = $config->allowavatargallery;

	unset($config->allowavatargallery);

	$config->avatarQuality = $config->avatarquality;

	unset($config->avatarquality);

	$config->avatarSize = $config->avatarsize;

	unset($config->avatarsize);

	$config->imageHeight = $config->imageheight;

	unset($config->imageheight);

	$config->imageWidth = $config->imagewidth;

	unset($config->imagewidth);

	$config->imageSize = $config->imagesize;

	unset($config->imagesize);

	$config->fileTypes = $config->filetypes;

	unset($config->filetypes);

	$config->fileSize = $config->filesize;

	unset($config->filesize);

	$config->showRanking = $config->showranking;

	unset($config->showranking);

	$config->rankImages = $config->rankimages;

	unset($config->rankimages);

	$config->userlistRows = $config->userlist_rows;

	unset($config->userlist_rows);

	$config->userlistOnline = $config->userlist_online;

	unset($config->userlist_online);

	$config->userlistAvatar = $config->userlist_avatar;

	unset($config->userlist_avatar);

	$config->userlistPosts = $config->userlist_posts;

	unset($config->userlist_posts);

	$config->userlistKarma = $config->userlist_karma;

	unset($config->userlist_karma);

	$config->userlistEmail = $config->userlist_email;

	unset($config->userlist_email);

	$config->userlistJoinDate = $config->userlist_joindate;

	unset($config->userlist_joindate);

	$config->userlistLastVisitDate = $config->userlist_lastvisitdate;

	unset($config->userlist_lastvisitdate);

	$config->userlistUserHits = $config->userlist_userhits;

	unset($config->userlist_userhits);

	$config->showStats = $config->showstats;

	unset($config->showstats);

	$config->showWhoIsOnline = $config->showwhoisonline;

	unset($config->showwhoisonline);

	$config->showGenStats = $config->showgenstats;

	unset($config->showgenstats);

	$config->showPopUserStats = $config->showpopuserstats;

	unset($config->showpopuserstats);

	$config->popUserCount = $config->popusercount;

	unset($config->popusercount);

	$config->showPopSubjectStats = $config->showpopsubjectstats;

	unset($config->showpopsubjectstats);

	$config->popSubjectCount = $config->popsubjectcount;

	unset($config->popsubjectcount);

	$config->showSpoilerTag = $config->showspoilertag;

	unset($config->showspoilertag);

	$config->showVideoTag = $config->showvideotag;

	unset($config->showvideotag);

	$config->showEbayTag = $config->showebaytag;

	unset($config->showebaytag);

	$config->trimLongUrls = $config->trimlongurls;

	unset($config->trimlongurls);

	$config->trimLongUrlsFront = $config->trimlongurlsfront;

	unset($config->trimlongurlsfront);

	$config->trimLongUrlsBack = $config->trimlongurlsback;

	unset($config->trimlongurlsback);

	$config->autoEmbedYoutube = $config->autoembedyoutube;

	unset($config->autoembedyoutube);

	$config->autoEmbedEbay = $config->autoembedebay;

	unset($config->autoembedebay);

	$config->ebayLanguageCode = $config->ebaylanguagecode;

	unset($config->ebaylanguagecode);

	$config->sessionTimeOut = $config->sessiontimeout;

	unset($config->sessiontimeout);

	$config->highlightCode = $config->highlightcode;

	unset($config->highlightcode);

	$config->rssType = $config->rss_type;

	unset($config->rss_type);

	$config->rssTimeLimit = $config->rss_timelimit;

	unset($config->rss_timelimit);

	$config->rssLimit = $config->rss_limit;

	unset($config->rss_limit);

	$config->rssIncludedCategories = $config->rss_included_categories;

	unset($config->rss_included_categories);

	$config->rssExcludedCategories = $config->rss_excluded_categories;

	unset($config->rss_excluded_categories);

	$config->rssSpecification = $config->rss_specification;

	unset($config->rss_specification);

	$config->rssAllowHtml = $config->rss_allow_html;

	unset($config->rss_allow_html);

	$config->rssAuthorFormat = $config->rss_author_format;

	unset($config->rss_author_format);

	$config->rssAuthorInTitle = $config->rss_author_in_title;

	unset($config->rss_author_in_title);

	$config->rssWordCount = $config->rss_word_count;

	unset($config->rss_word_count);

	$config->rssOldTitles = $config->rss_old_titles;

	unset($config->rss_old_titles);

	$config->rssCache = $config->rss_cache;

	unset($config->rss_cache);

	$config->defaultPage = $config->defaultpage;

	unset($config->defaultpage);

	$config->defaultSort = $config->default_sort;

	unset($config->default_sort);

	$config->showImgForGuest = $config->showimgforguest;

	unset($config->showimgforguest);

	$config->showFileForGuest = $config->showfileforguest;

	unset($config->showfileforguest);

	$config->pollNbOptions = $config->pollnboptions;

	unset($config->pollnboptions);

	$config->pollAllowVoteOne = $config->pollallowvoteone;

	unset($config->pollallowvoteone);

	$config->pollEnabled = $config->pollenabled;

	unset($config->pollenabled);

	$config->popPollsCount = $config->poppollscount;

	unset($config->poppollscount);

	$config->showPopPollStats = $config->showpoppollstats;

	unset($config->showpoppollstats);

	$config->pollTimeBtVotes = $config->polltimebtvotes;

	unset($config->polltimebtvotes);

	$config->pollNbVotesByUser = $config->pollnbvotesbyuser;

	unset($config->pollnbvotesbyuser);

	$config->pollResultsUserslist = $config->pollresultsuserslist;

	unset($config->pollresultsuserslist);

	$config->allowUserEditPoll = $config->allow_user_edit_poll;

	unset($config->allow_user_edit_pol);

	$config->maxPersonalText = $config->maxpersotext;

	unset($config->maxpersotext);

	$config->orderingSystem = $config->ordering_system;

	unset($config->ordering_system);

	$config->postDateFormat = $config->post_dateformat;

	unset($config->post_dateformat);

	$config->postDateFormatHover = $config->post_dateformat_hover;

	unset($config->post_dateformat_hover);

	$config->hideIp = $config->hide_ip;

	unset($config->hide_ip);

	$config->imageTypes = $config->imagetypes;

	unset($config->imagetypes);

	$config->checkMimeTypes = $config->checkmimetypes;

	unset($config->checkmimetypes);

	$config->imageMimeTypes = $config->imagemimetypes;

	unset($config->imagemimetypes);

	$config->imageQuality = $config->imagequality;

	unset($config->imagequality);

	$config->thumbHeight = $config->thumbheight;

	unset($config->thumbheight);

	$config->thumbWidth = $config->thumbwidth;

	unset($config->thumbwidth);

	$config->hideUserProfileInfo = $config->hideuserprofileinfo;

	unset($config->hideuserprofileinfo);

	$config->boxGhostMessage = $config->boxghostmessage;

	unset($config->boxghostmessage);

	$config->userDeleteMessage = $config->userdeletetmessage;

	unset($config->userdeletetmessage);

	$config->latestCategoryIn = $config->latestcategory_in;

	unset($config->latestcategory_in);

	$config->topicIcons = $config->topicicons;

	unset($config->topicicons);

	$config->catsAutoSubscribed = $config->catsautosubscribed;

	unset($config->catsautosubscribed);

	$config->showBannedReason = $config->showbannedreason;

	unset($config->showbannedreason);

	$config->showThankYou = $config->showthankyou;

	unset($config->showthankyou);

	$config->showPopThankYouStats = $config->showpopthankyoustats;

	unset($config->showpopthankyoustats);

	$config->popThanksCount = $config->popthankscount;

	unset($config->popthankscount);

	$config->modSeeDeleted = $config->mod_see_deleted;

	unset($config->mod_see_deleted);

	$config->bbcodeImgSecure = $config->bbcode_img_secure;

	unset($config->bbcode_img_secure);

	$config->listCatShowModerators = $config->listcat_show_moderators;

	unset($config->listcat_show_moderators);

	$config->showListTime = $config->show_list_time;

	unset($config->show_list_time);

	$config->showSessionType = $config->show_session_type;

	unset($config->show_session_type);

	$config->showSessionStartTime = $config->show_session_starttime;

	unset($config->show_session_starttime);

	$config->userlistAllowed = $config->userlist_allowed;

	unset($config->userlist_allowed);

	$config->userlistCountUsers = $config->userlist_count_users;

	unset($config->userlist_count_users);

	$config->enableThreadedLayouts = $config->enable_threaded_layouts;

	unset($config->enable_threaded_layouts);

	$config->categorySubscriptions = $config->category_subscriptions;

	unset($config->category_subscriptions);

	$config->topicSubscriptions = $config->topic_subscriptions;

	unset($config->topic_subscriptions);

	$config->pubProfile = $config->pubprofile;

	unset($config->pubprofile);

	$config->thankYouMax = $config->thankyou_max;

	unset($config->thankyou_max);

	$config->emailRecipientCount = $config->email_recipient_count;

	unset($config->email_recipient_count);

	$config->emailRecipientPrivacy = $config->email_recipient_privacy;

	unset($config->email_recipient_privacy);

	$config->emailVisibleAddress = $config->email_visible_address;

	unset($config->email_visible_address);

	$config->captchaPostLimit = $config->captcha_post_limit;

	unset($config->captcha_post_limit);

	$config->imageUpload = $config->image_upload;

	unset($config->image_upload);

	$config->fileUpload = $config->file_upload;

	unset($config->file_upload);

	$config->topicLayout = $config->topic_layout;

	unset($config->topic_layout);

	$config->timeToCreatePage = $config->time_to_create_page;

	unset($config->time_to_create_page);

	$config->showImgFilesManageProfile = $config->show_imgfiles_manage_profile;

	unset($config->show_imgfiles_manage_profil);

	$config->holdNewUsersPosts = $config->hold_newusers_posts;

	unset($config->hold_newusers_posts);

	$config->holdGuestPosts = $config->hold_guest_posts;

	unset($config->hold_guest_posts);

	$config->attachmentLimit = $config->attachment_limit;

	unset($config->attachment_limit);

	$config->pickupCategory = $config->pickup_category;

	unset($config->pickup_category);

	$config->articleDisplay = $config->article_display;

	unset($config->article_display);

	$config->sendEmails = $config->send_emails;

	unset($config->send_emails);

	$config->fallbackEnglish = $config->fallback_english;

	unset($config->fallback_english);

	$config->cacheTime = $config->cache_time;

	unset($config->cache_time);

	$config->ebayAffiliateId = $config->ebay_affiliate_id;

	unset($config->ebay_affiliate_id);

	$config->ipTracking = $config->iptracking;

	unset($config->iptracking);

	$config->rssFeedBurnerUrl = $config->rss_feedburner_url;

	unset($config->rss_feedburner_url);

	$config->autoLink = $config->autolink;

	unset($config->autolink);

	$config->accessComponent = $config->access_component;

	unset($config->access_component);

	$config->statsLinkAllowed = $config->statslink_allowed;

	unset($config->statslink_allowed);

	$config->superAdminUserlist = $config->superadmin_userlist;

	unset($config->superadmin_userlist);

	$config->attachmentProtection = $config->attachment_protection;

	unset($config->attachment_protection);

	$config->categoryIcons = $config->categoryicons;

	unset($config->categoryicons);

	$config->avatarCrop = $config->avatarcrop;

	unset($config->avatarcrop);

	$config->userReport = $config->user_report;

	unset($config->user_report);

	$config->searchTime = $config->searchtime;

	unset($config->searchtime);

	$config->ebayLanguage = $config->ebay_language;

	unset($config->ebay_language);

	$config->ebayApiKey = $config->ebay_api_key;

	unset($config->ebay_api_key);

	$config->allowChangeSubject = $config->allow_change_subject;

	unset($config->allow_change_subject);

	$config->maxLinks = $config->max_links;

	unset($config->max_links);

	$config->readOnly = $config->read_only;

	unset($config->read_only);

	$config->ratingEnabled = $config->ratingenabled;

	unset($config->ratingenabled);

	$config->urlSubjectTopic = $config->url_subject_topic;

	unset($config->url_subject_topic);

	$config->logModeration = $config->log_moderation;

	unset($config->log_moderation);

	$config->attachStart = $config->attach_start;

	unset($config->attach_start);

	$config->attachEnd = $config->attach_end;

	unset($config->attach_end);

	$config->googleMapApiKey = $config->google_map_api_key;

	unset($config->google_map_api_key);

	$config->attachmentUtf8 = $config->attachment_utf8;

	unset($config->attachment_utf8);

	$config->autoEmbedSoundcloud = $config->autoembedsoundcloud;

	unset($config->autoembedsoundcloud);

	$config->emailHeader = $config->emailheader;

	unset($config->emailheader);

	$config->userStatus = $config->user_status;

	unset($config->user_status);

	$config->plainEmail = $config->plain_email;

	unset($config->plain_email);

	$config->moderatorPermDelete = $config->moderator_permdelete;

	unset($config->moderator_permdelet);

	$config->avatarTypes = $config->avatartypes;

	unset($config->avatartypes);

	$config->smartLinking = $config->smartlinking;

	unset($config->smartlinking);

	$config->defaultAvatar = $config->defaultavatar;

	unset($config->defaultavatar);

	$config->defaultAvatarSmall = $config->defaultavatarsmall;

	unset($config->defaultavatarsmall);

	$config->stopForumSpamKey = $config->stopforumspam_key;

	unset($config->stopforumspam_key);

	$config->quickReply = $config->quickreply;

	unset($config->quickreply);

	$config->avatarEdit = $config->avataredit;

	unset($config->avataredit);

	$config->activeMenuItem = $config->activemenuitem;

	unset($config->activemenuitem);

	$config->mainMenuId = $config->mainmenu_id;

	unset($config->mainmenu_id);

	$config->homeId = $config->home_id;

	unset($config->home_id);

	$config->indexId = $config->index_id;

	unset($config->index_id);

	$config->moderatorsId = $config->moderators_id;

	unset($config->moderators_id);

	$config->topicListId = $config->topiclist_id;

	unset($config->topiclist_id);

	$config->miscId = $config->misc_id;

	unset($config->misc_id);

	$config->profileId = $config->profile_id;

	unset($config->profile_id);

	$config->searchId = $config->search_id;

	unset($config->search_id);

	$config->avatarType = $config->avatar_type;

	unset($config->avatar_type);

	$config->sefRedirect = $config->sef_redirect;

	unset($config->sef_redirect);

	$config->allowEditPoll = $config->allow_edit_poll;

	unset($config->allow_edit_poll);

	$config->useSystemEmails = $config->use_system_emails;

	unset($config->use_system_emails);

	$config->autoEmbedInstagram = $config->autoembedinstagram;

	unset($config->autoembedinstagram);

	$config->disableRe = $config->disable_re;

	unset($config->disable_re);

	$latestcategory = $config->latestcategory;

	if (!is_array($latestcategory)) {
		unset($config->latestcategory);

		$config->latestCategory = 0;

		// Save configuration
		$config->save();
	} else {
	    // Save configuration
	    $config->save();
	}

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_5215_UPDATE_CONFIGURATION'), 'success' => true);
}
