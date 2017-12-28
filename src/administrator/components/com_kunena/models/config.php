<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Config Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelConfig extends KunenaModel
{
	/**
	 * @return array
	 */
	function getConfiglists()
	{
		$lists = array();

		// RSS
		{
			// options to be used later
			$rss_yesno    = array();
			$rss_yesno [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_NO'));
			$rss_yesno [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_YES'));

			// ------

			$rss_type    = array();
			$rss_type [] = JHtml::_('select.option', 'post', JText::_('COM_KUNENA_A_RSS_TYPE_POST'));
			$rss_type [] = JHtml::_('select.option', 'topic', JText::_('COM_KUNENA_A_RSS_TYPE_TOPIC'));
			$rss_type [] = JHtml::_('select.option', 'recent', JText::_('COM_KUNENA_A_RSS_TYPE_RECENT'));

			// build the html select list
			$lists ['rss_type'] = JHtml::_('select.genericlist', $rss_type, 'cfg_rss_type', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_type);

			// ------

			$rss_timelimit    = array();
			$rss_timelimit [] = JHtml::_('select.option', 'week', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_WEEK'));
			$rss_timelimit [] = JHtml::_('select.option', 'month', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_MONTH'));
			$rss_timelimit [] = JHtml::_('select.option', 'year', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_YEAR'));

			// build the html select list
			$lists ['rss_timelimit'] = JHtml::_('select.genericlist', $rss_timelimit, 'cfg_rss_timelimit', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_timelimit);

			// ------

			$rss_specification = array();

			$rss_specification [] = JHtml::_('select.option', 'rss0.91', 'RSS 0.91');
			$rss_specification [] = JHtml::_('select.option', 'rss1.0', 'RSS 1.0');
			$rss_specification [] = JHtml::_('select.option', 'rss2.0', 'RSS 2.0');
			$rss_specification [] = JHtml::_('select.option', 'atom1.0', 'Atom 1.0');

			// build the html select list
			$lists ['rss_specification'] = JHtml::_('select.genericlist', $rss_specification, 'cfg_rss_specification', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_specification);

			// ------

			$rss_author_format    = array();
			$rss_author_format [] = JHtml::_('select.option', 'name', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_NAME'));
			$rss_author_format [] = JHtml::_('select.option', 'email', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_EMAIL'));
			$rss_author_format [] = JHtml::_('select.option', 'both', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_BOTH'));

			// build the html select list
			$lists ['rss_author_format'] = JHtml::_('select.genericlist', $rss_author_format, 'cfg_rss_author_format', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_author_format);

			// ------

			// build the html select list
			$lists ['rss_author_in_title'] = JHtml::_('select.genericlist', $rss_yesno, 'cfg_rss_author_in_title', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_author_in_title);

			// ------

			$rss_word_count    = array();
			$rss_word_count [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_RSS_WORD_COUNT_ALL'));
			$rss_word_count [] = JHtml::_('select.option', '50', '50');
			$rss_word_count [] = JHtml::_('select.option', '100', '100');
			$rss_word_count [] = JHtml::_('select.option', '250', '250');
			$rss_word_count [] = JHtml::_('select.option', '500', '500');
			$rss_word_count [] = JHtml::_('select.option', '750', '750');
			$rss_word_count [] = JHtml::_('select.option', '1000', '1000');

			// build the html select list
			$lists ['rss_word_count'] = JHtml::_('select.genericlist', $rss_word_count, 'cfg_rss_word_count', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_word_count);

			// ------

			// build the html select list
			$lists ['rss_allow_html'] = JHtml::_('select.genericlist', $rss_yesno, 'cfg_rss_allow_html', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_allow_html);

			// ------

			// build the html select list
			$lists ['rss_old_titles'] = JHtml::_('select.genericlist', $rss_yesno, 'cfg_rss_old_titles', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_old_titles);

			// ------

			$rss_cache = array();

			$rss_cache [] = JHtml::_('select.option', '0', '0');        // disable
			$rss_cache [] = JHtml::_('select.option', '60', '1');
			$rss_cache [] = JHtml::_('select.option', '300', '5');
			$rss_cache [] = JHtml::_('select.option', '900', '15');
			$rss_cache [] = JHtml::_('select.option', '1800', '30');
			$rss_cache [] = JHtml::_('select.option', '3600', '60');

			$lists ['rss_cache'] = JHtml::_('select.genericlist', $rss_cache, 'cfg_rss_cache', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_cache);

			// ------

			// build the html select list - (moved enablerss here, to keep all rss-related features together)
			$lists ['enablerss'] = JHtml::_('select.genericlist', $rss_yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $this->config->enablerss);
		}

		// build the html select list
		// make a standard yes/no list
		$yesno    = array();
		$yesno [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_NO'));
		$yesno [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_YES'));

		$lists ['disemoticons']           = JHtml::_('select.genericlist', $yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $this->config->disemoticons);
		$lists ['regonly']                = JHtml::_('select.genericlist', $yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $this->config->regonly);
		$lists ['board_offline']          = JHtml::_('select.genericlist', $yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $this->config->board_offline);
		$lists ['pubwrite']               = JHtml::_('select.genericlist', $yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $this->config->pubwrite);
		$lists ['showhistory']            = JHtml::_('select.genericlist', $yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $this->config->showhistory);
		$lists ['showannouncement']       = JHtml::_('select.genericlist', $yesno, 'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $this->config->showannouncement);
		$lists ['avataroncat']            = JHtml::_('select.genericlist', $yesno, 'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $this->config->avataroncat);
		$lists ['showchildcaticon']       = JHtml::_('select.genericlist', $yesno, 'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $this->config->showchildcaticon);
		$lists ['showuserstats']          = JHtml::_('select.genericlist', $yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showuserstats);
		$lists ['showwhoisonline']        = JHtml::_('select.genericlist', $yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $this->config->showwhoisonline);
		$lists ['showpopsubjectstats']    = JHtml::_('select.genericlist', $yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopsubjectstats);
		$lists ['showgenstats']           = JHtml::_('select.genericlist', $yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showgenstats);
		$lists ['showpopuserstats']       = JHtml::_('select.genericlist', $yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopuserstats);
		$lists ['allowsubscriptions']     = JHtml::_('select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowsubscriptions);
		$lists ['subscriptionschecked']   = JHtml::_('select.genericlist', $yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $this->config->subscriptionschecked);
		$lists ['allowfavorites']         = JHtml::_('select.genericlist', $yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowfavorites);
		$lists ['showemail']              = JHtml::_('select.genericlist', $yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $this->config->showemail);
		$lists ['askemail']               = JHtml::_('select.genericlist', $yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $this->config->askemail);
		$lists ['allowavatarupload']      = JHtml::_('select.genericlist', $yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowavatarupload);
		$lists ['allowavatargallery']     = JHtml::_('select.genericlist', $yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowavatargallery);
		$lists ['showstats']              = JHtml::_('select.genericlist', $yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showstats);
		$lists ['showranking']            = JHtml::_('select.genericlist', $yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $this->config->showranking);
		$lists ['rankimages']             = JHtml::_('select.genericlist', $yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $this->config->rankimages);
		$lists ['username']               = JHtml::_('select.genericlist', $yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $this->config->username);
		$lists ['shownew']                = JHtml::_('select.genericlist', $yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $this->config->shownew);
		$lists ['editmarkup']             = JHtml::_('select.genericlist', $yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $this->config->editmarkup);
		$lists ['showkarma']              = JHtml::_('select.genericlist', $yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $this->config->showkarma);
		$lists ['enableforumjump']        = JHtml::_('select.genericlist', $yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $this->config->enableforumjump);
		$lists ['userlist_online']        = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_online);
		$lists ['userlist_avatar']        = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_avatar);
		$lists ['userlist_posts']         = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_posts);
		$lists ['userlist_karma']         = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_karma);
		$lists ['userlist_email']         = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_email);
		$lists ['userlist_joindate']      = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_joindate);
		$lists ['userlist_lastvisitdate'] = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_lastvisitdate);
		$lists ['userlist_userhits']      = JHtml::_('select.genericlist', $yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_userhits);
		$lists ['reportmsg']              = JHtml::_('select.genericlist', $yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $this->config->reportmsg);

		$captcha = array();
		$captcha[] = JHtml::_('select.option', '-1', JText::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_NOBODY'));
		$captcha[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_REGISTERED_USERS'));
		$captcha[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_CONFIGURATION_OPTION_CAPTCHA_GUESTS_REGISTERED_USERS'));

		$lists ['captcha']                = JHtml::_('select.genericlist', $captcha, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $this->config->captcha);
		$lists ['mailfull']               = JHtml::_('select.genericlist', $yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailfull);
		// New for 1.0.5
		$lists ['showspoilertag']   = JHtml::_('select.genericlist', $yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showspoilertag);
		$lists ['showvideotag']     = JHtml::_('select.genericlist', $yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showvideotag);
		$lists ['showebaytag']      = JHtml::_('select.genericlist', $yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showebaytag);
		$lists ['trimlongurls']     = JHtml::_('select.genericlist', $yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $this->config->trimlongurls);
		$lists ['autoembedyoutube'] = JHtml::_('select.genericlist', $yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $this->config->autoembedyoutube);
		$lists ['autoembedebay']    = JHtml::_('select.genericlist', $yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $this->config->autoembedebay);
		$lists ['highlightcode']    = JHtml::_('select.genericlist', $yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $this->config->highlightcode);
		// New for 1.5.8 -> SEF
		$lists ['sef'] = JHtml::_('select.genericlist', $yesno, 'cfg_sef', 'class="inputbox" size="1"', 'value', 'text', $this->config->sef);
		// New for 1.6 -> Hide images and files for guests
		$lists['showimgforguest']  = JHtml::_('select.genericlist', $yesno, 'cfg_showimgforguest', 'class="inputbox" size="1"', 'value', 'text', $this->config->showimgforguest);
		$lists['showfileforguest'] = JHtml::_('select.genericlist', $yesno, 'cfg_showfileforguest', 'class="inputbox" size="1"', 'value', 'text', $this->config->showfileforguest);
		// New for 1.6 -> Check Image MIME types
		$lists['checkmimetypes'] = JHtml::_('select.genericlist', $yesno, 'cfg_checkmimetypes', 'class="inputbox" size="1"', 'value', 'text', $this->config->checkmimetypes);
		//New for 1.6 -> Poll
		$lists['pollallowvoteone']     = JHtml::_('select.genericlist', $yesno, 'cfg_pollallowvoteone', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollallowvoteone);
		$lists['pollenabled']          = JHtml::_('select.genericlist', $yesno, 'cfg_pollenabled', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollenabled);
		$lists['showpoppollstats']     = JHtml::_('select.genericlist', $yesno, 'cfg_showpoppollstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpoppollstats);
		$lists['pollresultsuserslist'] = JHtml::_('select.genericlist', $yesno, 'cfg_pollresultsuserslist', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollresultsuserslist);
		//New for 1.6 -> Choose ordering system
		$ordering_system_list     = array();
		$ordering_system_list[]   = JHtml::_('select.option', 'mesid', JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_NEW'));
		$ordering_system_list[]   = JHtml::_('select.option', 'replyid', JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_OLD'));
		$lists['ordering_system'] = JHtml::_('select.genericlist', $ordering_system_list, 'cfg_ordering_system', 'class="inputbox" size="1"', 'value', 'text', $this->config->ordering_system);
		// New for 1.6: datetime
		$dateformatlist                 = array();
		$time                           = KunenaDate::getInstance(time() - 80000);
		$dateformatlist[]               = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
		$dateformatlist[]               = JHtml::_('select.option', 'ago', $time->toKunena('ago'));
		$dateformatlist[]               = JHtml::_('select.option', 'datetime_today', $time->toKunena('datetime_today'));
		$dateformatlist[]               = JHtml::_('select.option', 'datetime', $time->toKunena('datetime'));
		$lists['post_dateformat']       = JHtml::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat', 'class="inputbox" size="1"', 'value', 'text', $this->config->post_dateformat);
		$lists['post_dateformat_hover'] = JHtml::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat_hover', 'class="inputbox" size="1"', 'value', 'text', $this->config->post_dateformat_hover);
		// New for 1.6: hide ip
		$lists['hide_ip'] = JHtml::_('select.genericlist', $yesno, 'cfg_hide_ip', 'class="inputbox" size="1"', 'value', 'text', $this->config->hide_ip);
		//New for 1.6: choose if you want that ghost message box checked by default
		$lists['boxghostmessage'] = JHtml::_('select.genericlist', $yesno, 'cfg_boxghostmessage', 'class="inputbox" size="1"', 'value', 'text', $this->config->boxghostmessage);
		// New for 1.6 -> Thank you button
		$lists ['showthankyou'] = JHtml::_('select.genericlist', $yesno, 'cfg_showthankyou', 'class="inputbox" size="1"', 'value', 'text', $this->config->showthankyou);

		$listUserDeleteMessage       = array();
		$listUserDeleteMessage[]     = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_DELETEMESSAGE_NOT_ALLOWED'));
		$listUserDeleteMessage[]     = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALLOWED_IF_REPLIES'));
		$listUserDeleteMessage[]     = JHtml::_('select.option', '2', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALWAYS_ALLOWED'));
		$listUserDeleteMessage[]     = JHtml::_('select.option', '3', JText::_('COM_KUNENA_CONFIG_DELETEMESSAGE_NOT_FIRST_MESSAGE'));
		$lists['userdeletetmessage'] = JHtml::_('select.genericlist', $listUserDeleteMessage, 'cfg_userdeletetmessage', 'class="inputbox" size="1"', 'value', 'text', $this->config->userdeletetmessage);

		$latestCategoryIn           = array();
		$latestCategoryIn[]         = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_LATESTCATEGORY_IN_HIDE'));
		$latestCategoryIn[]         = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_LATESTCATEGORY_IN_SHOW'));
		$lists['latestcategory_in'] = JHtml::_('select.genericlist', $latestCategoryIn, 'cfg_latestcategory_in', 'class="inputbox" size="1"', 'value', 'text', $this->config->latestcategory_in);

		$optionsShowHide         = array(JHtml::_('select.option', 0, JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_SHOWALL')));
		$params                  = array('sections' => false, 'action' => 'read');
		$lists['latestcategory'] = JHtml::_('kunenaforum.categorylist', 'cfg_latestcategory[]', 0, $optionsShowHide, $params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',', $this->config->latestcategory), 'latestcategory');

		$lists['topicicons'] = JHtml::_('select.genericlist', $yesno, 'cfg_topicicons', 'class="inputbox" size="1"', 'value', 'text', $this->config->topicicons);

		$lists['debug'] = JHtml::_('select.genericlist', $yesno, 'cfg_debug', 'class="inputbox" size="1"', 'value', 'text', $this->config->debug);

		$lists['showbannedreason'] = JHtml::_('select.genericlist', $yesno, 'cfg_showbannedreason', 'class="inputbox" size="1"', 'value', 'text', $this->config->showbannedreason);

		$lists['time_to_create_page'] = JHtml::_('select.genericlist', $yesno, 'cfg_time_to_create_page', 'class="inputbox" size="1"', 'value', 'text', $this->config->time_to_create_page);

		$lists['showpopthankyoustats'] = JHtml::_('select.genericlist', $yesno, 'cfg_showpopthankyoustats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopthankyoustats);

		$seerestoredeleted         = array();
		$seerestoredeleted[]       = JHtml::_('select.option', 2, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_NOBODY'));
		$seerestoredeleted[]       = JHtml::_('select.option', 1, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINSMODS'));
		$seerestoredeleted[]       = JHtml::_('select.option', 0, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINS'));
		$lists ['mod_see_deleted'] = JHtml::_('select.genericlist', $seerestoredeleted, 'cfg_mod_see_deleted', 'class="inputbox" size="1"', 'value', 'text', $this->config->mod_see_deleted);

		$listBbcodeImgSecure         = array();
		$listBbcodeImgSecure[]       = JHtml::_('select.option', 'text', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_TEXT'));
		$listBbcodeImgSecure[]       = JHtml::_('select.option', 'link', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_LINK'));
		$listBbcodeImgSecure[]       = JHtml::_('select.option', 'image', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_IMAGE'));
		$lists ['bbcode_img_secure'] = JHtml::_('select.genericlist', $listBbcodeImgSecure, 'cfg_bbcode_img_secure', 'class="inputbox" size="1"', 'value', 'text', $this->config->bbcode_img_secure);
		$lists ['listcat_show_moderators'] = JHtml::_('select.genericlist', $yesno, 'cfg_listcat_show_moderators', 'class="inputbox" size="1"', 'value', 'text', $this->config->listcat_show_moderators);
		$showlightbox       = $yesno;
		$showlightbox[]     = JHtml::_('select.option', 2, JText::_('COM_KUNENA_A_LIGHTBOX_NO_JS'));
		$lists ['lightbox'] = JHtml::_('select.genericlist', $showlightbox, 'cfg_lightbox', 'class="inputbox" size="1"', 'value', 'text', $this->config->lightbox);

		$timesel[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_SELECT_ALL'));
		$timesel[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		// build the html select list
		$lists ['show_list_time'] = JHtml::_('select.genericlist', $timesel, 'cfg_show_list_time', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_list_time);

		$sessiontimetype[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_ALL'));
		$sessiontimetype[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_VALID'));
		$sessiontimetype[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_TIME'));

		$lists ['show_session_type'] = JHtml::_('select.genericlist', $sessiontimetype, 'cfg_show_session_type', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_session_type);

		$userlist_allowed           = array();
		$userlist_allowed []        = JHtml::_('select.option', '0', JText::_('COM_KUNENA_A_NO'));
		$userlist_allowed []        = JHtml::_('select.option', '1', JText::_('COM_KUNENA_A_YES'));
		$lists ['userlist_allowed'] = JHtml::_('select.genericlist', $userlist_allowed, 'cfg_userlist_allowed', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_allowed);
		$lists ['pubprofile']       = JHtml::_('select.genericlist', $yesno, 'cfg_pubprofile', 'class="inputbox" size="1"', 'value', 'text', $this->config->pubprofile);

		$userlist_count_users[]         = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ALL'));
		$userlist_count_users[]         = JHtml::_('select.option', 1, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVATED_ACCOUNT'));
		$userlist_count_users[]         = JHtml::_('select.option', 2, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVE'));
		$userlist_count_users[]         = JHtml::_('select.option', 3, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_NON_BLOCKED_USERS'));
		$lists ['userlist_count_users'] = JHtml::_('select.genericlist', $userlist_count_users, 'cfg_userlist_count_users', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_count_users);

		// Added new options into K1.6.4
		$lists ['allowsubscriptions'] = JHtml::_('select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowsubscriptions);

		$category_subscriptions           = array();
		$category_subscriptions[]         = JHtml::_('select.option', 'disabled', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_DISABLED'));
		$category_subscriptions[]         = JHtml::_('select.option', 'topic', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_TOPIC'));
		$category_subscriptions[]         = JHtml::_('select.option', 'post', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_POST'));
		$lists ['category_subscriptions'] = JHtml::_('select.genericlist', $category_subscriptions, 'cfg_category_subscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->category_subscriptions);

		$topic_subscriptions           = array();
		$topic_subscriptions[]         = JHtml::_('select.option', 'disabled', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_DISABLED'));
		$topic_subscriptions[]         = JHtml::_('select.option', 'first', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_FIRST'));
		$topic_subscriptions[]         = JHtml::_('select.option', 'every', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_EVERY'));
		$lists ['topic_subscriptions'] = JHtml::_('select.genericlist', $topic_subscriptions, 'cfg_topic_subscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->topic_subscriptions);

		// Added new options into K1.6.6
		$email_recipient_privacy           = array();
		$email_recipient_privacy[]         = JHtml::_('select.option', 'to', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_TO'));
		$email_recipient_privacy[]         = JHtml::_('select.option', 'cc', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_CC'));
		$email_recipient_privacy[]         = JHtml::_('select.option', 'bcc', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_BCC'));
		$lists ['email_recipient_privacy'] = JHtml::_('select.genericlist', $email_recipient_privacy, 'cfg_email_recipient_privacy', 'class="inputbox" size="1"', 'value', 'text', $this->config->email_recipient_privacy);

		$uploads                = array();
		$uploads[]              = JHtml::_('select.option', 'everybody', JText::_('COM_KUNENA_EVERYBODY'));
		$uploads[]              = JHtml::_('select.option', 'registered', JText::_('COM_KUNENA_REGISTERED_USERS'));
		$uploads[]              = JHtml::_('select.option', 'moderator', JText::_('COM_KUNENA_MODERATORS'));
		$uploads[]              = JHtml::_('select.option', 'admin', JText::_('COM_KUNENA_ADMINS'));
		$uploads[]              = JHtml::_('select.option', '', JText::_('COM_KUNENA_NOBODY'));
		$lists ['image_upload'] = JHtml::_('select.genericlist', $uploads, 'cfg_image_upload', 'class="inputbox" size="1"', 'value', 'text', $this->config->image_upload);
		$lists ['file_upload']  = JHtml::_('select.genericlist', $uploads, 'cfg_file_upload', 'class="inputbox" size="1"', 'value', 'text', $this->config->file_upload);

		$topic_layout[]         = JHtml::_('select.option', 'flat', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_FLAT'));
		$topic_layout[]         = JHtml::_('select.option', 'threaded', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_THREADED'));
		$topic_layout[]         = JHtml::_('select.option', 'indented', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_INDENTED'));
		$lists ['topic_layout'] = JHtml::_('select.genericlist', $topic_layout, 'cfg_topic_layout', 'class="inputbox" size="1"', 'value', 'text', $this->config->topic_layout);

		$lists ['show_imgfiles_manage_profile'] = JHtml::_('select.genericlist', $yesno, 'cfg_show_imgfiles_manage_profile', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_imgfiles_manage_profile);

		$lists ['show_imgfiles_manage_profile'] = JHtml::_('select.genericlist', $yesno, 'cfg_show_imgfiles_manage_profile', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_imgfiles_manage_profile);

		$lists ['hold_guest_posts'] = JHtml::_('select.genericlist', $yesno, 'cfg_hold_guest_posts', 'class="inputbox" size="1"', 'value', 'text', $this->config->hold_guest_posts);

		$lists ['pickup_category'] = JHtml::_('select.genericlist', $yesno, 'cfg_pickup_category', 'class="inputbox" size="1"', 'value', 'text', $this->config->pickup_category);

		$article_display[]         = JHtml::_('select.option', 'full', JText::_('COM_KUNENA_COM_A_FULL_ARTICLE'));
		$article_display[]         = JHtml::_('select.option', 'intro', JText::_('COM_KUNENA_COM_A_INTRO_ARTICLE'));
		$article_display[]         = JHtml::_('select.option', 'link', JText::_('COM_KUNENA_COM_A_ARTICLE_LINK'));
		$lists ['article_display'] = JHtml::_('select.genericlist', $article_display, 'cfg_article_display', 'class="inputbox" size="1"', 'value', 'text', $this->config->article_display);

		$lists ['send_emails']             = JHtml::_('select.genericlist', $yesno, 'cfg_send_emails', 'class="inputbox" size="1"', 'value', 'text', $this->config->send_emails);
		$lists ['enable_threaded_layouts'] = JHtml::_('select.genericlist', $yesno, 'cfg_enable_threaded_layouts', 'class="inputbox" size="1"', 'value', 'text', $this->config->enable_threaded_layouts);

		$default_sort           = array();
		$default_sort[]         = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_OPTION_DEFAULT_SORT_FIRST'));
		$default_sort[]         = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_OPTION_DEFAULT_SORT_LAST'));
		$lists ['default_sort'] = JHtml::_('select.genericlist', $default_sort, 'cfg_default_sort', 'class="inputbox" size="1"', 'value', 'text', $this->config->default_sort);

		$lists ['fallback_english'] = JHtml::_('select.genericlist', $yesno, 'cfg_fallback_english', 'class="inputbox" size="1"', 'value', 'text', $this->config->fallback_english);

		$cache       = array();
		$cache[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_CFG_OPTION_USE_GLOBAL'));
		$cache[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_CFG_OPTION_NO_CACHING'));

		$cachetime            = array();
		$cachetime[]          = JHtml::_('select.option', '60', JText::_('COM_KUNENA_CFG_OPTION_1_MINUTE'));
		$cachetime[]          = JHtml::_('select.option', '120', JText::_('COM_KUNENA_CFG_OPTION_2_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '180', JText::_('COM_KUNENA_CFG_OPTION_3_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '300', JText::_('COM_KUNENA_CFG_OPTION_5_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '600', JText::_('COM_KUNENA_CFG_OPTION_10_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '900', JText::_('COM_KUNENA_CFG_OPTION_15_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '1800', JText::_('COM_KUNENA_CFG_OPTION_30_MINUTES'));
		$cachetime[]          = JHtml::_('select.option', '3600', JText::_('COM_KUNENA_CFG_OPTION_60_MINUTES'));
		$lists ['cache']      = JHtml::_('select.genericlist', $yesno, 'cfg_cache', 'class="inputbox" size="1"', 'value', 'text', $this->config->cache);
		$lists ['cache_time'] = JHtml::_('select.genericlist', $cachetime, 'cfg_cache_time', 'class="inputbox" size="1"', 'value', 'text', $this->config->cache_time);

		// Added new options into Kunena 2.0.1
		$mailoptions   = array();
		$mailoptions[] = JHtml::_('select.option', '-1', JText::_('COM_KUNENA_NO'));
		$mailoptions[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_CFG_OPTION_UNAPPROVED_POSTS'));
		$mailoptions[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_CFG_OPTION_ALL_NEW_POSTS'));

		$lists ['mailmod']   = JHtml::_('select.genericlist', $mailoptions, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailmod);
		$lists ['mailadmin'] = JHtml::_('select.genericlist', $mailoptions, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailadmin);

		$lists ['iptracking'] = JHtml::_('select.genericlist', $yesno, 'cfg_iptracking', 'class="inputbox" size="1"', 'value', 'text', $this->config->iptracking);

		// Added new options into Kunena 3.0.0
		$lists ['autolink']         = JHtml::_('select.genericlist', $yesno, 'cfg_autolink', 'class="inputbox" size="1"', 'value', 'text', $this->config->autolink);
		$lists ['access_component'] = JHtml::_('select.genericlist', $yesno, 'cfg_access_component', 'class="inputbox" size="1"', 'value', 'text', $this->config->access_component);
		$lists ['componentUrl']     = preg_replace('|/+|', '/', JUri::root() . ($this->config->get('sef_rewrite') ? '' : 'index.php') . ($this->config->get('sef') ? '/component/kunena' : '?option=com_kunena'));

		// Added new options into Kunena 4.0.0
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('version')->from('#__kunena_version')->order('id');
		$db->setQuery($query, 0, 1);
		$lists['legacy_urls_version'] = $db->loadResult();

		$options                        = array();
		$options[]                      = JHtml::_('select.option', '0', JText::_('COM_KUNENA_NO'));
		$options[]                      = JHtml::_('select.option', '1', 'Kunena 1.x');
		$lists['legacy_urls_desc']      = version_compare($lists['legacy_urls_version'], '2.0', '<') ? JText::_('COM_KUNENA_CFG_LEGACY_URLS_DESC_YES') : JText::_('COM_KUNENA_CFG_LEGACY_URLS_DESC_NO');
		$lists['legacy_urls']           = JHtml::_('select.genericlist', $options, 'cfg_legacy_urls', 'class="inputbox" size="1"', 'value', 'text', $this->config->legacy_urls);
		$lists['attachment_protection'] = JHtml::_('select.genericlist', $yesno, 'cfg_attachment_protection', 'class="inputbox" size="1"', 'value', 'text', $this->config->attachment_protection);

		// Option to select if the stats link need to be showed for all users or only for registred users
		$lists ['statslink_allowed']   = JHtml::_('select.genericlist', $yesno, 'cfg_statslink_allowed', 'class="inputbox" size="1"', 'value', 'text', $this->config->statslink_allowed);
		$lists ['superadmin_userlist'] = JHtml::_('select.genericlist', $yesno, 'cfg_superadmin_userlist', 'class="inputbox" size="1"', 'value', 'text', $this->config->superadmin_userlist);
		$resizeoptions                 = array();
		$resizeoptions[]               = JHtml::_('select.option', '0', JText::_('COM_KUNENA_RESIZE_RESIZE'));
		$resizeoptions[]               = JHtml::_('select.option', '1', JText::_('COM_KUNENA_RESIZE_INTERPOLATION'));
		$resizeoptions[]               = JHtml::_('select.option', '2', JText::_('COM_KUNENA_RESIZE_BICUBIC'));
		$lists ['avatarresizemethod']  = JHtml::_('select.genericlist', $resizeoptions, 'cfg_avatarresizemethod', 'class="inputbox" size="1"', 'value', 'text', $this->config->avatarresizemethod);
		$lists ['avatarcrop']          = JHtml::_('select.genericlist', $yesno, 'cfg_avatarcrop', 'class="inputbox" size="1"', 'value', 'text', $this->config->avatarcrop);
		$lists ['user_report']         = JHtml::_('select.genericlist', $yesno, 'cfg_user_report', 'class="inputbox" size="1"', 'value', 'text', $this->config->user_report);

		$searchtime           = array();
		$searchtime[]         = JHtml::_('select.option', 1, JText::_('COM_KUNENA_CFG_SEARCH_DATE_YESTERDAY'));
		$searchtime[]         = JHtml::_('select.option', 7, JText::_('COM_KUNENA_CFG_SEARCH_DATE_WEEK'));
		$searchtime[]         = JHtml::_('select.option', 14, JText::_('COM_KUNENA_CFG_SEARCH_DATE_2WEEKS'));
		$searchtime[]         = JHtml::_('select.option', 30, JText::_('COM_KUNENA_CFG_SEARCH_DATE_MONTH'));
		$searchtime[]         = JHtml::_('select.option', 90, JText::_('COM_KUNENA_CFG_SEARCH_DATE_3MONTHS'));
		$searchtime[]         = JHtml::_('select.option', 180, JText::_('COM_KUNENA_CFG_SEARCH_DATE_6MONTHS'));
		$searchtime[]         = JHtml::_('select.option', 365, JText::_('COM_KUNENA_CFG_SEARCH_DATE_YEAR'));
		$searchtime[]         = JHtml::_('select.option', 'all', JText::_('COM_KUNENA_CFG_SEARCH_DATE_ANY'));
		$lists ['searchtime'] = JHtml::_('select.genericlist', $searchtime, 'cfg_searchtime', 'class="inputbox" size="1"', 'value', 'text', $this->config->searchtime);

		$lists ['teaser'] = JHtml::_('select.genericlist', $yesno, 'cfg_teaser', 'class="inputbox" size="1"', 'value', 'text', $this->config->teaser);

		// List of eBay language code
		$ebay_language   = array();
		$ebay_language[] = JHtml::_('select.option', '0', 'en-US');
		$ebay_language[] = JHtml::_('select.option', '2', 'en-CA');
		$ebay_language[] = JHtml::_('select.option', '3', 'en-GB');
		$ebay_language[] = JHtml::_('select.option', '15', 'en-AU');
		$ebay_language[] = JHtml::_('select.option', '16', 'de-AT');
		$ebay_language[] = JHtml::_('select.option', '23', 'fr-BE');
		$ebay_language[] = JHtml::_('select.option', '71', 'fr-FR');
		$ebay_language[] = JHtml::_('select.option', '77', 'de-DE');
		$ebay_language[] = JHtml::_('select.option', '101', 'it-IT');
		$ebay_language[] = JHtml::_('select.option', '123', 'nl-BE');
		$ebay_language[] = JHtml::_('select.option', '146', 'nl-NL');
		$ebay_language[] = JHtml::_('select.option', '186', 'es-ES');
		$ebay_language[] = JHtml::_('select.option', '193', 'ch-CH');
		$ebay_language[] = JHtml::_('select.option', '201', 'hk-HK');
		$ebay_language[] = JHtml::_('select.option', '203', 'in-IN');
		$ebay_language[] = JHtml::_('select.option', '205', 'ie-IE');
		$ebay_language[] = JHtml::_('select.option', '207', 'my-MY');
		$ebay_language[] = JHtml::_('select.option', '210', 'fr-CA');
		$ebay_language[] = JHtml::_('select.option', '211', 'ph-PH');
		$ebay_language[] = JHtml::_('select.option', '212', 'pl-PL');
		$ebay_language[] = JHtml::_('select.option', '216', 'sg-SG');

		$lists['ebay_language'] = JHtml::_('select.genericlist', $ebay_language, 'cfg_ebay_language', 'class="inputbox" size="1"', 'value', 'text', $this->config->ebay_language);

		$useredit = array();
		$useredit[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_NO'));
		$useredit[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_YES'));
		$useredit[] = JHtml::_('select.option', '2', JText::_('COM_KUNENA_A_EDIT_ALLOWED_IF_REPLIES'));
		$lists['useredit'] = JHtml::_('select.genericlist', $useredit, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $this->config->useredit);

		$lists ['allow_change_subject'] = JHtml::_('select.genericlist', $yesno, 'cfg_allow_change_subject', 'class="inputbox" size="1"', 'value', 'text', $this->config->allow_change_subject);

		//K5.0
		$lists ['read_only'] = JHtml::_('select.genericlist', $yesno, 'cfg_read_only', 'class="inputbox" size="1"', 'value', 'text', $this->config->read_only);

		$lists['ratingenabled'] = JHtml::_('select.genericlist', $yesno, 'cfg_ratingenabled', 'class="inputbox" size="1"', 'value', 'text', $this->config->ratingenabled);

		$lists ['url_subject_topic'] = JHtml::_('select.genericlist', $yesno, 'cfg_url_subject_topic', 'class="inputbox" size="1"', 'value', 'text', $this->config->url_subject_topic);

		$lists ['log_moderation'] = JHtml::_('select.genericlist', $yesno, 'cfg_log_moderation', 'class="inputbox" size="1"', 'value', 'text', $this->config->log_moderation);

		$lists ['attachment_utf8'] = JHtml::_('select.genericlist', $yesno, 'cfg_attachment_utf8', 'class="inputbox" size="1"', 'value', 'text', $this->config->attachment_utf8);

		$lists ['autoembedsoundcloud'] = JHtml::_('select.genericlist', $yesno, 'cfg_autoembedsoundcloud', 'class="inputbox" size="1"', 'value', 'text', $this->config->autoembedsoundcloud);

		$lists ['user_status'] = JHtml::_('select.genericlist', $yesno, 'cfg_user_status', 'class="inputbox" size="1"', 'value', 'text', $this->config->user_status);

		$lists ['plain_email'] = JHtml::_('select.genericlist', $yesno, 'cfg_plain_email', 'class="inputbox" size="1"', 'value', 'text', $this->config->plain_email);
		$lists ['moderator_permdelete'] = JHtml::_('select.genericlist', $yesno, 'cfg_moderator_permdelete', 'class="inputbox" size="1"', 'value', 'text', $this->config->moderator_permdelete);
		return $lists;
	}
}
