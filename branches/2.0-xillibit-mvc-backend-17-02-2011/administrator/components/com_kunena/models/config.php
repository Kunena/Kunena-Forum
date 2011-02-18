<?php
/**
 * @version $Id: stats.php 4387 2011-02-08 16:19:37Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Config Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelConfig extends JModel {
	function getConfiglists() {
		require_once (KUNENA_PATH_LIB.'/kunena.timeformat.class.php');
		$config = KunenaFactory::getConfig ();

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
		$lists ['rss_type'] = JHTML::_ ( 'select.genericlist', $rss_type, 'cfg_rss_type', 'class="inputbox" size="1"', 'value', 'text', $config->rss_type );

		// ------

		$rss_timelimit = array ();
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'week', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_WEEK') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'month', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_MONTH') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'year', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_YEAR') );

		// build the html select list
		$lists ['rss_timelimit'] = JHTML::_ ( 'select.genericlist', $rss_timelimit, 'cfg_rss_timelimit', 'class="inputbox" size="1"', 'value', 'text', $config->rss_timelimit );

		// ------

		$rss_specification = array ();

		$rss_specification [] = JHTML::_ ( 'select.option', 'rss0.91', 'RSS 0.91');
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss1.0', 'RSS 1.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss2.0', 'RSS 2.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'atom1.0', 'Atom 1.0' );

		// build the html select list
		$lists ['rss_specification'] = JHTML::_ ( 'select.genericlist', $rss_specification, 'cfg_rss_specification', 'class="inputbox" size="1"', 'value', 'text', $config->rss_specification );

		// ------

		$rss_author_format = array ();
		$rss_author_format [] = JHTML::_ ( 'select.option', 'name', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_NAME') );
		$rss_author_format [] = JHTML::_ ( 'select.option', 'email', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_EMAIL') );
		$rss_author_format [] = JHTML::_ ( 'select.option', 'both', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_BOTH') );

		// build the html select list
		$lists ['rss_author_format'] = JHTML::_ ( 'select.genericlist', $rss_author_format, 'cfg_rss_author_format', 'class="inputbox" size="1"', 'value', 'text', $config->rss_author_format );

		// ------

		// build the html select list
		$lists ['rss_author_in_title'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_author_in_title', 'class="inputbox" size="1"', 'value', 'text', $config->rss_author_in_title );

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
		$lists ['rss_word_count'] = JHTML::_ ( 'select.genericlist', $rss_word_count, 'cfg_rss_word_count', 'class="inputbox" size="1"', 'value', 'text', $config->rss_word_count );

		// ------

		// build the html select list
		$lists ['rss_allow_html'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_allow_html', 'class="inputbox" size="1"', 'value', 'text', $config->rss_allow_html );

		// ------

		// build the html select list
		$lists ['rss_old_titles'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_old_titles', 'class="inputbox" size="1"', 'value', 'text', $config->rss_old_titles );

		// ------

		$rss_cache = array ();

		$rss_cache [] = JHTML::_ ( 'select.option', '0', '0' );		// disable
		$rss_cache [] = JHTML::_ ( 'select.option', '60', '1' );
		$rss_cache [] = JHTML::_ ( 'select.option', '300', '5' );
		$rss_cache [] = JHTML::_ ( 'select.option', '900', '15' );
		$rss_cache [] = JHTML::_ ( 'select.option', '1800', '30' );
		$rss_cache [] = JHTML::_ ( 'select.option', '3600', '60' );

		$lists ['rss_cache'] = JHTML::_ ( 'select.genericlist', $rss_cache, 'cfg_rss_cache', 'class="inputbox" size="1"', 'value', 'text', $config->rss_cache );

		// ------

		// build the html select list - (moved enablerss here, to keep all rss-related features together)
		$lists ['enablerss'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $config->enablerss );
		}

		// build the html select list
		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_NO') );
		$yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_YES') );

		$lists ['jmambot'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_jmambot', 'class="inputbox" size="1"', 'value', 'text', $config->jmambot );
		$lists ['disemoticons'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $config->disemoticons );
		$lists ['regonly'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $config->regonly );
		$lists ['board_offline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $config->board_offline );
		$lists ['pubwrite'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $config->pubwrite );
		$lists ['useredit'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $config->useredit );
		$lists ['showhistory'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $config->showhistory );
		$lists ['showannouncement'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $config->showannouncement );
		$lists ['avataroncat'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $config->avataroncat );
		$lists ['showchildcaticon'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $config->showchildcaticon );
		$lists ['showuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $config->showuserstats );
		$lists ['showwhoisonline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $config->showwhoisonline );
		$lists ['showpopsubjectstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $config->showpopsubjectstats );
		$lists ['showgenstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $config->showgenstats );
		$lists ['showpopuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $config->showpopuserstats );
		$lists ['allowsubscriptions'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $config->allowsubscriptions );
		$lists ['subscriptionschecked'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $config->subscriptionschecked );
		$lists ['allowfavorites'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $config->allowfavorites );
		$lists ['mailmod'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $config->mailmod );
		$lists ['mailadmin'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $config->mailadmin );
		$lists ['showemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $config->showemail );
		$lists ['askemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $config->askemail );
		$lists ['changename'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_changename', 'class="inputbox" size="1"', 'value', 'text', $config->changename );
		$lists ['allowavatarupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $config->allowavatarupload );
		$lists ['allowavatargallery'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $config->allowavatargallery );
		$lists ['showstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $config->showstats );
		$lists ['showranking'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $config->showranking );
		$lists ['rankimages'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $config->rankimages );
		$lists ['username'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $config->username );
		$lists ['shownew'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $config->shownew );
		$lists ['allowimageupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowimageupload', 'class="inputbox" size="1"', 'value', 'text', $config->allowimageupload );
		$lists ['allowimageregupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowimageregupload', 'class="inputbox" size="1"', 'value', 'text', $config->allowimageregupload );
		$lists ['allowfileupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfileupload', 'class="inputbox" size="1"', 'value', 'text', $config->allowfileupload );
		$lists ['allowfileregupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfileregupload', 'class="inputbox" size="1"', 'value', 'text', $config->allowfileregupload );
		$lists ['editmarkup'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $config->editmarkup );
		$lists ['showkarma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $config->showkarma );
		$lists ['enableforumjump'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $config->enableforumjump );
		$lists ['userlist_online'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_online );
		$lists ['userlist_avatar'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_avatar );
		$lists ['userlist_name'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_name', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_name );
		$lists ['userlist_username'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_username', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_username );
		$lists ['userlist_posts'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_posts );
		$lists ['userlist_karma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_karma );
		$lists ['userlist_email'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_email );
		$lists ['userlist_usertype'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_usertype', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_usertype );
		$lists ['userlist_joindate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_joindate );
		$lists ['userlist_lastvisitdate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_lastvisitdate );
		$lists ['userlist_userhits'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_userhits );
		$lists ['usernamechange'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_usernamechange', 'class="inputbox" size="1"', 'value', 'text', $config->usernamechange );
		$lists ['reportmsg'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $config->reportmsg );
		$lists ['captcha'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $config->captcha );
		$lists ['mailfull'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $config->mailfull );
		// New for 1.0.5
		$lists ['showspoilertag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $config->showspoilertag );
		$lists ['showvideotag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $config->showvideotag );
		$lists ['showebaytag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $config->showebaytag );
		$lists ['trimlongurls'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $config->trimlongurls );
		$lists ['autoembedyoutube'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $config->autoembedyoutube );
		$lists ['autoembedebay'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $config->autoembedebay );
		$lists ['highlightcode'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $config->highlightcode );
		// New for 1.5.8 -> SEF
		$lists ['sef'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sef', 'class="inputbox" size="1"', 'value', 'text', $config->sef );
		$lists ['sefcats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sefcats', 'class="inputbox" size="1"', 'value', 'text', $config->sefcats );
		$lists ['sefutf8'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sefutf8', 'class="inputbox" size="1"', 'value', 'text', $config->sefutf8 );
		// New for 1.6 -> Hide images and files for guests
		$lists['showimgforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showimgforguest', 'class="inputbox" size="1"', 'value', 'text', $config->showimgforguest);
		$lists['showfileforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showfileforguest', 'class="inputbox" size="1"', 'value', 'text', $config->showfileforguest);
		// New for 1.6 -> Check Image MIME types
		$lists['checkmimetypes'] = JHTML::_('select.genericlist', $yesno, 'cfg_checkmimetypes', 'class="inputbox" size="1"', 'value', 'text', $config->checkmimetypes);
		//New for 1.6 -> Poll
		$lists['pollallowvoteone'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollallowvoteone', 'class="inputbox" size="1"', 'value', 'text', $config->pollallowvoteone);
  		$lists['pollenabled'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollenabled', 'class="inputbox" size="1"', 'value', 'text', $config->pollenabled);
  		$lists['showpoppollstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpoppollstats', 'class="inputbox" size="1"', 'value', 'text', $config->showpoppollstats);
  		$lists['pollresultsuserslist'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollresultsuserslist', 'class="inputbox" size="1"', 'value', 'text', $config->pollresultsuserslist);
  		//New for 1.6 -> Choose ordering system
  		$ordering_system_list = array ();
  		$ordering_system_list[] = JHTML::_('select.option', 'mesid',JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_NEW'));
  		$ordering_system_list[] = JHTML::_('select.option', 'replyid', JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_OLD'));
  		$lists['ordering_system'] = JHTML::_('select.genericlist', $ordering_system_list, 'cfg_ordering_system', 'class="inputbox" size="1"', 'value', 'text', $config->ordering_system);
		// New for 1.6: datetime
		$dateformatlist = array ();
		$time = CKunenaTimeformat::internalTime() - 80000;
		$dateformatlist[] = JHTML::_('select.option', 'none', JText::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
		$dateformatlist[] = JHTML::_('select.option', 'ago', CKunenaTimeformat::showDate($time, 'ago'));
		$dateformatlist[] = JHTML::_('select.option', 'datetime_today', CKunenaTimeformat::showDate($time, 'datetime_today'));
		$dateformatlist[] = JHTML::_('select.option', 'datetime', CKunenaTimeformat::showDate($time, 'datetime'));
		$lists['post_dateformat'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat', 'class="inputbox" size="1"', 'value', 'text', $config->post_dateformat);
		$lists['post_dateformat_hover'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat_hover', 'class="inputbox" size="1"', 'value', 'text', $config->post_dateformat_hover);
		// New for 1.6: hide ip
		$lists['hide_ip'] = JHTML::_('select.genericlist', $yesno, 'cfg_hide_ip', 'class="inputbox" size="1"', 'value', 'text', $config->hide_ip);
		//New for 1.6: choose if you want that ghost message box checked by default
		$lists['boxghostmessage'] = JHTML::_('select.genericlist', $yesno, 'cfg_boxghostmessage', 'class="inputbox" size="1"', 'value', 'text', $config->boxghostmessage);
		// New for 1.6 -> Thank you button
		$lists ['showthankyou'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showthankyou', 'class="inputbox" size="1"', 'value', 'text', $config->showthankyou );

		kimport('kunena.integration');
		$lists['integration_access'] = KunenaIntegration::getConfigOptions('access');
		$lists['integration_activity'] = KunenaIntegration::getConfigOptions('activity');
		$lists['integration_avatar'] = KunenaIntegration::getConfigOptions('avatar');
		$lists['integration_login'] = KunenaIntegration::getConfigOptions('login');
		$lists['integration_profile'] = KunenaIntegration::getConfigOptions('profile');
		$lists['integration_private'] = KunenaIntegration::getConfigOptions('private');

		$listUserDeleteMessage = array();
		$listUserDeleteMessage[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_A_DELETEMESSAGE_NOT_ALLOWED'));
		$listUserDeleteMessage[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALLOWED_IF_REPLIES'));
		$listUserDeleteMessage[] = JHTML::_('select.option', '2', JText::_('COM_KUNENA_A_DELETEMESSAGE_ALWAYS_ALLOWED'));
		$lists['userdeletetmessage'] = JHTML::_('select.genericlist', $listUserDeleteMessage, 'cfg_userdeletetmessage', 'class="inputbox" size="1"', 'value', 'text', $config->userdeletetmessage);

		$latestCategoryIn = array();
		$latestCategoryIn[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_IN_HIDE'));
		$latestCategoryIn[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_IN_SHOW'));
		$lists['latestcategory_in'] = JHTML::_('select.genericlist', $latestCategoryIn, 'cfg_latestcategory_in', 'class="inputbox" size="1"', 'value', 'text', $config->latestcategory_in);

		$optionsShowHide = array(JHTML::_('select.option', 0, JText::_('COM_KUNENA_COM_A_LATESTCATEGORY_SHOWALL')));
		$params = array ('sections' => false, 'action' => 'read');
		$lists['latestcategory'] = JHTML::_('kunenaforum.categorylist', 'cfg_latestcategory[]', 0, $optionsShowHide, $params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',',$config->latestcategory), 'latestcategory');

		$lists['topicicons'] = JHTML::_('select.genericlist', $yesno, 'cfg_topicicons', 'class="inputbox" size="1"', 'value', 'text', $config->topicicons);

		$lists['debug'] = JHTML::_('select.genericlist', $yesno, 'cfg_debug', 'class="inputbox" size="1"', 'value', 'text', $config->debug);

		$lists['showbannedreason'] = JHTML::_('select.genericlist', $yesno, 'cfg_showbannedreason', 'class="inputbox" size="1"', 'value', 'text', $config->showbannedreason);

		$lists['version_check'] = JHTML::_('select.genericlist', $yesno, 'cfg_version_check', 'class="inputbox" size="1"', 'value', 'text', $config->version_check);

		$lists['showpopthankyoustats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpopthankyoustats', 'class="inputbox" size="1"', 'value', 'text', $config->showpopthankyoustats);

		$lists ['mod_see_deleted'] = JHTML::_('select.genericlist', $yesno, 'cfg_mod_see_deleted', 'class="inputbox" size="1"', 'value', 'text', $config->mod_see_deleted);

		$listBbcodeImgSecure = array();
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'text', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_TEXT'));
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'link', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_LINK'));
		$listBbcodeImgSecure[] = JHTML::_('select.option', 'image', JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_OPTION_IMAGE'));
		$lists ['bbcode_img_secure'] = JHTML::_('select.genericlist', $listBbcodeImgSecure, 'cfg_bbcode_img_secure', 'class="inputbox" size="1"', 'value', 'text', $config->bbcode_img_secure);

		$lists ['listcat_show_moderators'] = JHTML::_('select.genericlist', $yesno, 'cfg_listcat_show_moderators', 'class="inputbox" size="1"', 'value', 'text', $config->listcat_show_moderators);

		$lists ['lightbox'] = JHTML::_('select.genericlist', $yesno, 'cfg_lightbox', 'class="inputbox" size="1"', 'value', 'text', $config->lightbox);

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
		$lists ['show_list_time'] = JHTML::_('select.genericlist', $timesel, 'cfg_show_list_time', 'class="inputbox" size="1"', 'value', 'text', $config->show_list_time);

		$sessiontimetype[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_ALL'));
		$sessiontimetype[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_VALID'));
		$sessiontimetype[] = JHTML::_('select.option', 2, JText::_('COM_KUNENA_SHOW_SESSION_TYPE_TIME'));

		$lists ['show_session_type'] = JHTML::_('select.genericlist', $sessiontimetype, 'cfg_show_session_type', 'class="inputbox" size="1"', 'value', 'text', $config->show_session_type);

		$userlist_allowed[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_USERLIST_TYPE_ALL'));
		$userlist_allowed[] = JHTML::_('select.option', 1, JText::_('COM_KUNENA_SHOW_USERLIST_TYPE_REGISTRED'));
		$lists ['userlist_allowed'] = JHTML::_('select.genericlist', $userlist_allowed, 'cfg_userlist_allowed', 'class="inputbox" size="1"', 'value', 'text', $config->userlist_allowed);

		return $lists;
	}
}
