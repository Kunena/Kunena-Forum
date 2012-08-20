<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Config Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelConfig extends KunenaModel {
	function getConfiglists() {
		$lists = array ();

		// RSS
		{
		// options to be used later
		$rss_yesno = array ();
		$rss_yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_NO') );
		$rss_yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_YES') );

		// ------

		$rss_type = array ();
		$rss_type [] = JHTML::_ ( 'select.option', 'post', JText::_('COM_KUNENA_A_RSS_TYPE_POST') );
		$rss_type [] = JHTML::_ ( 'select.option', 'topic', JText::_('COM_KUNENA_A_RSS_TYPE_TOPIC') );
		$rss_type [] = JHTML::_ ( 'select.option', 'recent', JText::_('COM_KUNENA_A_RSS_TYPE_RECENT') );

		// build the html select list
		$lists ['rss_type'] = JHTML::_ ( 'select.genericlist', $rss_type, 'cfg_rss_type', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_type );

		// ------

		$rss_timelimit = array ();
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'week', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_WEEK') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'month', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_MONTH') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'year', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_YEAR') );

		// build the html select list
		$lists ['rss_timelimit'] = JHTML::_ ( 'select.genericlist', $rss_timelimit, 'cfg_rss_timelimit', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_timelimit );

		// ------

		$rss_specification = array ();

		$rss_specification [] = JHTML::_ ( 'select.option', 'rss0.91', 'RSS 0.91');
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss1.0', 'RSS 1.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss2.0', 'RSS 2.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'atom1.0', 'Atom 1.0' );

		// build the html select list
		$lists ['rss_specification'] = JHTML::_ ( 'select.genericlist', $rss_specification, 'cfg_rss_specification', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_specification );

		// ------

		$rss_author_format = array ();
		$rss_author_format [] = JHTML::_ ( 'select.option', 'name', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_NAME') );
		$rss_author_format [] = JHTML::_ ( 'select.option', 'email', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_EMAIL') );
		$rss_author_format [] = JHTML::_ ( 'select.option', 'both', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_BOTH') );

		// build the html select list
		$lists ['rss_author_format'] = JHTML::_ ( 'select.genericlist', $rss_author_format, 'cfg_rss_author_format', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_author_format );

		// ------

		// build the html select list
		$lists ['rss_author_in_title'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_author_in_title', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_author_in_title );

		// ------

		$rss_word_count = array ();
		$rss_word_count [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_RSS_WORD_COUNT_ALL') );
		$rss_word_count [] = JHTML::_ ( 'select.option', '50', '50' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '100', '100' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '250', '250' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '500', '500' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '750', '750' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '1000', '1000' );

		// build the html select list
		$lists ['rss_word_count'] = JHTML::_ ( 'select.genericlist', $rss_word_count, 'cfg_rss_word_count', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_word_count );

		// ------

		// build the html select list
		$lists ['rss_allow_html'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_allow_html', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_allow_html );

		// ------

		// build the html select list
		$lists ['rss_old_titles'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_old_titles', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_old_titles );

		// ------

		$rss_cache = array ();

		$rss_cache [] = JHTML::_ ( 'select.option', '0', '0' );		// disable
		$rss_cache [] = JHTML::_ ( 'select.option', '60', '1' );
		$rss_cache [] = JHTML::_ ( 'select.option', '300', '5' );
		$rss_cache [] = JHTML::_ ( 'select.option', '900', '15' );
		$rss_cache [] = JHTML::_ ( 'select.option', '1800', '30' );
		$rss_cache [] = JHTML::_ ( 'select.option', '3600', '60' );

		$lists ['rss_cache'] = JHTML::_ ( 'select.genericlist', $rss_cache, 'cfg_rss_cache', 'class="inputbox" size="1"', 'value', 'text', $this->config->rss_cache );

		// ------

		// build the html select list - (moved enablerss here, to keep all rss-related features together)
		$lists ['enablerss'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $this->config->enablerss );
		}

		// build the html select list
		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_NO') );
		$yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_YES') );

		$lists ['disemoticons'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $this->config->disemoticons );
		$lists ['regonly'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $this->config->regonly );
		$lists ['board_offline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $this->config->board_offline );
		$lists ['pubwrite'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $this->config->pubwrite );
		$lists ['useredit'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $this->config->useredit );
		$lists ['showhistory'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $this->config->showhistory );
		$lists ['showannouncement'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $this->config->showannouncement );
		$lists ['avataroncat'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $this->config->avataroncat );
		$lists ['showchildcaticon'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $this->config->showchildcaticon );
		$lists ['showuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showuserstats );
		$lists ['showwhoisonline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $this->config->showwhoisonline );
		$lists ['showpopsubjectstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopsubjectstats );
		$lists ['showgenstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showgenstats );
		$lists ['showpopuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopuserstats );
		$lists ['allowsubscriptions'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowsubscriptions );
		$lists ['subscriptionschecked'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $this->config->subscriptionschecked );
		$lists ['allowfavorites'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowfavorites );
		$lists ['showemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $this->config->showemail );
		$lists ['askemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $this->config->askemail );
		$lists ['allowavatarupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowavatarupload );
		$lists ['allowavatargallery'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowavatargallery );
		$lists ['showstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showstats );
		$lists ['showranking'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $this->config->showranking );
		$lists ['rankimages'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $this->config->rankimages );
		$lists ['username'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $this->config->username );
		$lists ['shownew'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $this->config->shownew );
		$lists ['editmarkup'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $this->config->editmarkup );
		$lists ['showkarma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $this->config->showkarma );
		$lists ['enableforumjump'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $this->config->enableforumjump );
		$lists ['userlist_online'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_online );
		$lists ['userlist_avatar'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_avatar );
		$lists ['userlist_name'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_name', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_name );
		$lists ['userlist_posts'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_posts );
		$lists ['userlist_karma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_karma );
		$lists ['userlist_email'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_email );
		$lists ['userlist_usertype'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_usertype', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_usertype );
		$lists ['userlist_joindate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_joindate );
		$lists ['userlist_lastvisitdate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_lastvisitdate );
		$lists ['userlist_userhits'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_userhits );
		$lists ['usernamechange'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_usernamechange', 'class="inputbox" size="1"', 'value', 'text', $this->config->usernamechange );
		$lists ['reportmsg'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $this->config->reportmsg );
		$lists ['captcha'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $this->config->captcha );
		$lists ['mailfull'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailfull );
		// New for 1.0.5
		$lists ['showspoilertag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showspoilertag );
		$lists ['showvideotag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showvideotag );
		$lists ['showebaytag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $this->config->showebaytag );
		$lists ['trimlongurls'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $this->config->trimlongurls );
		$lists ['autoembedyoutube'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $this->config->autoembedyoutube );
		$lists ['autoembedebay'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $this->config->autoembedebay );
		$lists ['highlightcode'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $this->config->highlightcode );
		// New for 1.5.8 -> SEF
		$lists ['sef'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sef', 'class="inputbox" size="1"', 'value', 'text', $this->config->sef );
		$lists ['sefutf8'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sefutf8', 'class="inputbox" size="1"', 'value', 'text', $this->config->sefutf8 );
		// New for 1.6 -> Hide images and files for guests
		$lists['showimgforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showimgforguest', 'class="inputbox" size="1"', 'value', 'text', $this->config->showimgforguest);
		$lists['showfileforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showfileforguest', 'class="inputbox" size="1"', 'value', 'text', $this->config->showfileforguest);
		// New for 1.6 -> Check Image MIME types
		$lists['checkmimetypes'] = JHTML::_('select.genericlist', $yesno, 'cfg_checkmimetypes', 'class="inputbox" size="1"', 'value', 'text', $this->config->checkmimetypes);
		//New for 1.6 -> Poll
		$lists['pollallowvoteone'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollallowvoteone', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollallowvoteone);
  		$lists['pollenabled'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollenabled', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollenabled);
  		$lists['showpoppollstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpoppollstats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpoppollstats);
  		$lists['pollresultsuserslist'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollresultsuserslist', 'class="inputbox" size="1"', 'value', 'text', $this->config->pollresultsuserslist);
  		//New for 1.6 -> Choose ordering system
  		$ordering_system_list = array ();
  		$ordering_system_list[] = JHTML::_('select.option', 'mesid',JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_NEW'));
  		$ordering_system_list[] = JHTML::_('select.option', 'replyid', JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_OLD'));
  		$lists['ordering_system'] = JHTML::_('select.genericlist', $ordering_system_list, 'cfg_ordering_system', 'class="inputbox" size="1"', 'value', 'text', $this->config->ordering_system);
		// New for 1.6: datetime
		$dateformatlist = array ();
		$time = KunenaDate::getInstance(time()-80000);
		$dateformatlist[] = JHTML::_('select.option', 'none', JText::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
		$dateformatlist[] = JHTML::_('select.option', 'ago', $time->toKunena('ago'));
		$dateformatlist[] = JHTML::_('select.option', 'datetime_today', $time->toKunena('datetime_today'));
		$dateformatlist[] = JHTML::_('select.option', 'datetime', $time->toKunena('datetime'));
		$lists['post_dateformat'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat', 'class="inputbox" size="1"', 'value', 'text', $this->config->post_dateformat);
		$lists['post_dateformat_hover'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat_hover', 'class="inputbox" size="1"', 'value', 'text', $this->config->post_dateformat_hover);
		// New for 1.6: hide ip
		$lists['hide_ip'] = JHTML::_('select.genericlist', $yesno, 'cfg_hide_ip', 'class="inputbox" size="1"', 'value', 'text', $this->config->hide_ip);
		//New for 1.6: choose if you want that ghost message box checked by default
		$lists['boxghostmessage'] = JHTML::_('select.genericlist', $yesno, 'cfg_boxghostmessage', 'class="inputbox" size="1"', 'value', 'text', $this->config->boxghostmessage);
		// New for 1.6 -> Thank you button
		$lists ['showthankyou'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showthankyou', 'class="inputbox" size="1"', 'value', 'text', $this->config->showthankyou );

		$listUserDeleteMessage = array();
		$listUserDeleteMessage[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_A_DELETEMESSAGE_NOT_ALLOWED'));
		$listUserDeleteMessage[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALLOWED_IF_REPLIES'));
		$listUserDeleteMessage[] = JHTML::_('select.option', '2', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALWAYS_ALLOWED'));
		$lists['userdeletetmessage'] = JHTML::_('select.genericlist', $listUserDeleteMessage, 'cfg_userdeletetmessage', 'class="inputbox" size="1"', 'value', 'text', $this->config->userdeletetmessage);

		$latestCategoryIn = array();
		$latestCategoryIn[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_A_LATESTCATEGORY_IN_HIDE'));
		$latestCategoryIn[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_A_LATESTCATEGORY_IN_SHOW'));
		$lists['latestcategory_in'] = JHTML::_('select.genericlist', $latestCategoryIn, 'cfg_latestcategory_in', 'class="inputbox" size="1"', 'value', 'text', $this->config->latestcategory_in);

		$optionsShowHide = array(JHTML::_('select.option', 0, JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_SHOWALL')));
		$params = array ('sections' => false, 'action' => 'read');
		$lists['latestcategory'] = JHTML::_('kunenaforum.categorylist', 'cfg_latestcategory[]', 0, $optionsShowHide, $params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',',$this->config->latestcategory), 'latestcategory');

		$lists['topicicons'] = JHTML::_('select.genericlist', $yesno, 'cfg_topicicons', 'class="inputbox" size="1"', 'value', 'text', $this->config->topicicons);

		$lists['debug'] = JHTML::_('select.genericlist', $yesno, 'cfg_debug', 'class="inputbox" size="1"', 'value', 'text', $this->config->debug);

		$lists['showbannedreason'] = JHTML::_('select.genericlist', $yesno, 'cfg_showbannedreason', 'class="inputbox" size="1"', 'value', 'text', $this->config->showbannedreason);

		$lists['version_check'] = JHTML::_('select.genericlist', $yesno, 'cfg_version_check', 'class="inputbox" size="1"', 'value', 'text', $this->config->version_check);

		$lists['time_to_create_page'] = JHTML::_('select.genericlist', $yesno, 'cfg_time_to_create_page', 'class="inputbox" size="1"', 'value', 'text', $this->config->time_to_create_page);

		$lists['showpopthankyoustats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpopthankyoustats', 'class="inputbox" size="1"', 'value', 'text', $this->config->showpopthankyoustats);

		$seerestoredeleted = array();
		$seerestoredeleted[] =JHTML::_('select.option', 2, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_NOBODY'));
		$seerestoredeleted[] =JHTML::_('select.option', 1, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINSMODS'));
		$seerestoredeleted[] =JHTML::_('select.option', 0, JText::_('COM_KUNENA_A_SEE_RESTORE_DELETED_ADMINS'));
		$lists ['mod_see_deleted'] = JHTML::_('select.genericlist', $seerestoredeleted, 'cfg_mod_see_deleted', 'class="inputbox" size="1"', 'value', 'text', $this->config->mod_see_deleted);

		$listBbcodeImgSecure = array();
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'text', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_TEXT'));
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'link', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_LINK'));
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'image', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_IMAGE'));
		$lists ['bbcode_img_secure'] = JHTML::_('select.genericlist', $listBbcodeImgSecure, 'cfg_bbcode_img_secure', 'class="inputbox" size="1"', 'value', 'text', $this->config->bbcode_img_secure);

		$lists ['listcat_show_moderators'] = JHTML::_('select.genericlist', $yesno, 'cfg_listcat_show_moderators', 'class="inputbox" size="1"', 'value', 'text', $this->config->listcat_show_moderators);

		$showlightbox = $yesno;
		$showlightbox[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_A_LIGHTBOX_NO_JS'));
		$lists ['lightbox'] = JHTML::_('select.genericlist', $showlightbox, 'cfg_lightbox', 'class="inputbox" size="1"', 'value', 'text', $this->config->lightbox);

		$timesel[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = JHTML::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = JHTML::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = JHTML::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = JHTML::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = JHTML::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = JHTML::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = JHTML::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = JHTML::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		// build the html select list
		$lists ['show_list_time'] = JHTML::_('select.genericlist', $timesel, 'cfg_show_list_time', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_list_time);

		$sessiontimetype[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_ALL'));
		$sessiontimetype[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_VALID'));
		$sessiontimetype[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_TIME'));

		$lists ['show_session_type'] = JHTML::_('select.genericlist', $sessiontimetype, 'cfg_show_session_type', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_session_type);

		$userlist_allowed = array ();
		$userlist_allowed [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_NO') );
		$userlist_allowed [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_YES') );
		$lists ['userlist_allowed'] = JHTML::_('select.genericlist', $userlist_allowed, 'cfg_userlist_allowed', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_allowed);
		$lists ['pubprofile'] = JHTML::_('select.genericlist', $yesno, 'cfg_pubprofile', 'class="inputbox" size="1"', 'value', 'text', $this->config->pubprofile);

		$userlist_count_users[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ALL'));
		$userlist_count_users[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVATED_ACCOUNT'));
		$userlist_count_users[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_ACTIVE'));
		$userlist_count_users[] = JHTML::_('select.option', 3, JText::_('COM_KUNENA_SHOW_USERLIST_COUNTUNSERS_NON_BLOCKED_USERS'));
		$lists ['userlist_count_users'] = JHTML::_('select.genericlist', $userlist_count_users, 'cfg_userlist_count_users', 'class="inputbox" size="1"', 'value', 'text', $this->config->userlist_count_users);

		// Added new options into K1.6.4
		$lists ['allowsubscriptions'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->allowsubscriptions );

		$category_subscriptions = array();
		$category_subscriptions[] = JHTML::_('select.option', 'disabled', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_DISABLED'));
		$category_subscriptions[] = JHTML::_('select.option', 'topic', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_TOPIC'));
		$category_subscriptions[] = JHTML::_('select.option', 'post', JText::_('COM_KUNENA_OPTION_CATEGORY_SUBSCRIPTIONS_POST'));
		$lists ['category_subscriptions'] = JHTML::_ ( 'select.genericlist', $category_subscriptions, 'cfg_category_subscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->category_subscriptions );

		$topic_subscriptions = array();
		$topic_subscriptions[] = JHTML::_('select.option', 'disabled', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_DISABLED'));
		$topic_subscriptions[] = JHTML::_('select.option', 'first', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_FIRST'));
		$topic_subscriptions[] = JHTML::_('select.option', 'every', JText::_('COM_KUNENA_OPTION_TOPIC_SUBSCRIPTIONS_EVERY'));
		$lists ['topic_subscriptions'] = JHTML::_ ( 'select.genericlist', $topic_subscriptions, 'cfg_topic_subscriptions', 'class="inputbox" size="1"', 'value', 'text', $this->config->topic_subscriptions );

		// Added new options into K1.6.6
		$email_recipient_privacy = array();
		$email_recipient_privacy[] = JHTML::_('select.option', 'to', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_TO'));
		$email_recipient_privacy[] = JHTML::_('select.option', 'cc', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_CC'));
		$email_recipient_privacy[] = JHTML::_('select.option', 'bcc', JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_OPTION_BCC'));
		$lists ['email_recipient_privacy'] = JHTML::_ ( 'select.genericlist', $email_recipient_privacy, 'cfg_email_recipient_privacy', 'class="inputbox" size="1"', 'value', 'text', $this->config->email_recipient_privacy );

		$recaptcha_theme = array();
		$recaptcha_theme[] = JHTML::_('select.option', 'red', JText::_('COM_KUNENA_A_RECAPTCHA_THEME_OPTION_RED'));
		$recaptcha_theme[] = JHTML::_('select.option', 'white', JText::_('COM_KUNENA_A_RECAPTCHA_THEME_OPTION_WHITE'));
		$recaptcha_theme[] = JHTML::_('select.option', 'blackglass', JText::_('COM_KUNENA_A_RECAPTCHA_THEME_OPTION_BLACK'));
		$recaptcha_theme[] = JHTML::_('select.option', 'clean', JText::_('COM_KUNENA_A_RECAPTCHA_THEME_OPTION_CLEAN'));
		$lists ['recaptcha_theme'] = JHTML::_ ( 'select.genericlist', $recaptcha_theme, 'cfg_recaptcha_theme', 'class="inputbox" size="1"', 'value', 'text', $this->config->recaptcha_theme );

		// Added new options into Kunena 2.0.0
		$lists ['keywords'] = JHTML::_('select.genericlist', $yesno, 'cfg_keywords', 'class="inputbox" size="1"', 'value', 'text', $this->config->keywords);
		$lists ['userkeywords'] = JHTML::_('select.genericlist', $yesno, 'cfg_userkeywords', 'class="inputbox" size="1"', 'value', 'text', $this->config->userkeywords);

		$uploads = array();
		$uploads[] = JHTML::_('select.option', 'everybody', JText::_('COM_KUNENA_EVERYBODY'));
		$uploads[] = JHTML::_('select.option', 'registered', JText::_('COM_KUNENA_REGISTERED_USERS'));
		$uploads[] = JHTML::_('select.option', 'moderator', JText::_('COM_KUNENA_MODERATORS'));
		$uploads[] = JHTML::_('select.option', 'admin', JText::_('COM_KUNENA_ADMINS'));
		$uploads[] = JHTML::_('select.option', '', JText::_('COM_KUNENA_NOBODY'));
		$lists ['image_upload'] = JHTML::_('select.genericlist', $uploads, 'cfg_image_upload', 'class="inputbox" size="1"', 'value', 'text', $this->config->image_upload);
		$lists ['file_upload'] = JHTML::_('select.genericlist', $uploads, 'cfg_file_upload', 'class="inputbox" size="1"', 'value', 'text', $this->config->file_upload);

		$topic_layout[] = JHTML::_('select.option', 'flat', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_FLAT'));
		$topic_layout[] = JHTML::_('select.option', 'threaded', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_THREADED'));
		$topic_layout[] = JHTML::_('select.option', 'indented', JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_INDENTED'));
		$lists ['topic_layout'] = JHTML::_('select.genericlist', $topic_layout, 'cfg_topic_layout', 'class="inputbox" size="1"', 'value', 'text', $this->config->topic_layout);

		$lists ['show_imgfiles_manage_profile'] = JHTML::_('select.genericlist', $yesno, 'cfg_show_imgfiles_manage_profile', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_imgfiles_manage_profile);

		$lists ['show_imgfiles_manage_profile'] = JHTML::_('select.genericlist', $yesno, 'cfg_show_imgfiles_manage_profile', 'class="inputbox" size="1"', 'value', 'text', $this->config->show_imgfiles_manage_profile);

		$lists ['hold_guest_posts'] = JHTML::_('select.genericlist', $yesno, 'cfg_hold_guest_posts', 'class="inputbox" size="1"', 'value', 'text', $this->config->hold_guest_posts);

		$lists ['pickup_category'] = JHTML::_('select.genericlist', $yesno, 'cfg_pickup_category', 'class="inputbox" size="1"', 'value', 'text', $this->config->pickup_category);

		$article_display[] = JHTML::_('select.option', 'full', JText::_('COM_KUNENA_COM_A_FULL_ARTICLE'));
		$article_display[] = JHTML::_('select.option', 'intro', JText::_('COM_KUNENA_COM_A_INTRO_ARTICLE'));
		$article_display[] = JHTML::_('select.option', 'link', JText::_('COM_KUNENA_COM_A_ARTICLE_LINK'));
		$lists ['article_display'] = JHTML::_('select.genericlist', $article_display, 'cfg_article_display', 'class="inputbox" size="1"', 'value', 'text', $this->config->article_display);

		$lists ['send_emails'] = JHTML::_('select.genericlist', $yesno, 'cfg_send_emails', 'class="inputbox" size="1"', 'value', 'text', $this->config->send_emails);
		$lists ['enable_threaded_layouts'] = JHTML::_('select.genericlist', $yesno, 'cfg_enable_threaded_layouts', 'class="inputbox" size="1"', 'value', 'text', $this->config->enable_threaded_layouts);

		$default_sort = array();
		$default_sort[] = JHTML::_('select.option', 'asc', JText::_('COM_KUNENA_OPTION_DEFAULT_SORT_FIRST'));
		$default_sort[] = JHTML::_('select.option', 'desc', JText::_('COM_KUNENA_OPTION_DEFAULT_SORT_LAST'));
		$lists ['default_sort'] = JHTML::_('select.genericlist', $default_sort, 'cfg_default_sort', 'class="inputbox" size="1"', 'value', 'text', $this->config->default_sort);

		$lists ['fallback_english'] = JHTML::_('select.genericlist', $yesno, 'cfg_fallback_english', 'class="inputbox" size="1"', 'value', 'text', $this->config->fallback_english);

		$cache = array();
		$cachetime[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_CFG_OPTION_USE_GLOBAL'));
		$cachetime[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_CFG_OPTION_NO_CACHING'));

		$cachetime = array();
		$cachetime[] = JHTML::_('select.option', '60', JText::_('COM_KUNENA_CFG_OPTION_1_MINUTE'));
		$cachetime[] = JHTML::_('select.option', '120', JText::_('COM_KUNENA_CFG_OPTION_2_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '180', JText::_('COM_KUNENA_CFG_OPTION_3_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '300', JText::_('COM_KUNENA_CFG_OPTION_5_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '600', JText::_('COM_KUNENA_CFG_OPTION_10_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '900', JText::_('COM_KUNENA_CFG_OPTION_15_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '1800', JText::_('COM_KUNENA_CFG_OPTION_30_MINUTES'));
		$cachetime[] = JHTML::_('select.option', '3600', JText::_('COM_KUNENA_CFG_OPTION_60_MINUTES'));
		$lists ['cache'] = JHTML::_('select.genericlist', $yesno, 'cfg_cache', 'class="inputbox" size="1"', 'value', 'text', $this->config->cache);
		$lists ['cache_time'] = JHTML::_('select.genericlist', $cachetime, 'cfg_cache_time', 'class="inputbox" size="1"', 'value', 'text', $this->config->cache_time);

		// Added new options into Kunena 2.0.1
		$mailoptions = array();
		$mailoptions[] = JHTML::_('select.option', '-1', JText::_('COM_KUNENA_NO'));
		$mailoptions[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_CFG_OPTION_UNAPPROVED_POSTS'));
		$mailoptions[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_CFG_OPTION_ALL_NEW_POSTS'));

		$lists ['mailmod'] = JHTML::_ ( 'select.genericlist', $mailoptions, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailmod );
		$lists ['mailadmin'] = JHTML::_ ( 'select.genericlist', $mailoptions, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $this->config->mailadmin );

		$lists ['iptracking'] = JHTML::_('select.genericlist', $yesno, 'cfg_iptracking', 'class="inputbox" size="1"', 'value', 'text', $this->config->iptracking);

		return $lists;
	}
}
