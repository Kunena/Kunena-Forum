<?php
/**
 * @version		$Id: kunenapinning.php 4169 2011-03-16 02:49:49Z 810 $
 * KunenaPinning Plugin for Kunena
 * @package plg_kunena_pinning
 * @copyright	Copyright (C) 2009 - 2011 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
// no direct access

defined ( '_JEXEC' ) or die ();
jimport('joomla.environment.browser');

class  plgSystemPlg_Kunena_Pinning extends JPlugin {

function onAfterRender(){
		$browser = &JBrowser::getInstance();
		if (($browser->getBrowser() != 'msie')&&($browser->getMajor()<=9)){
			return; // Don't run if not IE9
		}
		
		$conf = &JFactory::getConfig();
		$this->params->def('appname', $conf->getValue( 'config.sitename' ));
		$this->params->def('apptooltip', $conf->getValue( 'config.MetaDesc' ));
		$this->params->def('navcolor', $conf->getValue('#5388b4' ));
		$meta = array();

		$meta[] = "<meta name=\"application-name\" content=\"{$this->params->get('application_name')}\" />";
		$meta[] = "<meta name=\"msapplication-tooltip\" content=\"{$this->params->get('application_tooltip')}\" />";
		$meta[] = "<meta name=\"msapplication-navbutton-color\" content=\"{$this->params->get('navcolor')}\" />";		
		$meta[] = '<meta name="msapplication-task" content="name=Recent Topics;action-uri=index.php?option=com_kunena&view=latest;icon-uri=http://www.newf1.nl/recent.ico "/>';
		$meta[] = '<meta name="msapplication-task" content="name=New topic;action-uri=index.php?option=com_kunena&view=post&do=new;icon-uri=http://www.newf1.nl/new.ico"/>';
		$meta[] = '<meta name="msapplication-task" content="name=Search;action-uri=index.php?option=com_kunena&view=search;icon-uri=http://www.newf1.nl/search.ico"/>';
		$meta[] = '<meta name="msapplication-task" content="name=Subscribe to RSS;action-uri=index.php?option=com_kunena&func=rss;icon-uri=http://www.newf1.nl/rss.ico"/>';
		$meta[] = '<meta name="msapplication-task" content="name=Kunena;action-uri=http://www.kunena.com;icon-uri=http://www.newf1.nl/kunena.ico"/>	';
		
		//$meta[] =  "<script type=\"text/javascript\" src=\"plugins/system/kunenapinned.js\"></script>";
			
        $meta[] =  "<script type='text/javascript'>"; 
			$meta[] =  "function MyJumplist(){";
			$meta[] =  "window.external.msSiteModeCreateJumplist('Forum');";
			$meta[] =  "window.external.msSiteModeAddJumpListItem('Profile', 'index.php?option=com_kunena&view=profile','http://www.newf1.nl/profile.ico');";
			$meta[] =  "window.external.msSiteModeAddJumpListItem('Login', 'index.php?option=com_kunena', 'http://www.newf1.nl/login.ico');";
			$meta[] =  "window.external.msSiteModeShowJumplist();";
			$meta[] =  "}";

			$meta[] =  "MyJumplist();";
			$meta[] =  "</script>";
		
		$body= &JResponse::getbody();
		$body = str_replace('</head>', implode("\n", $meta)."\n</head>", $body);
		
		JResponse::setBody($body);
		
	}
   }