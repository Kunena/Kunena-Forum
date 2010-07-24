<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena Latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

// Detect and load Kunena 1.6+
$kunena_api = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php';
if (! JComponentHelper::isEnabled ( 'com_kunena', true ) || ! is_file ( $kunena_api ))
	return JError::raiseError ( JText::_ ( 'Kunena Error' ), JText::_ ( 'Kunena 1.6 is not installed on your system' ) );

require_once ($kunena_api);

$params = ( object ) $params;
$klatest = new modKunenaLatest($params);

class modKunenaLatest
{
	public function __construct($params) {
		require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.image.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/libraries/html/parser.php');
		$this->kunena_config = KunenaFactory::getConfig ();
		$this->document = JFactory::getDocument ();
		$this->document->addStyleSheet ( JURI::root () . 'modules/mod_kunenalatest/tmpl/klatest.css' );

		$this->latestdo = null;

		 if ($params->get( 'choosemodel' ) != 'latest') {
        	$this->latestdo = $params->get( 'choosemodel' );
      	}

		// Load topic icons
		$this->topic_emoticons = array ();
    	$this->topic_emoticons [0] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-default.gif';
    	$this->topic_emoticons [1] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-exclamation.png';
    	$this->topic_emoticons [2] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-question.png';
    	$this->topic_emoticons [3] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-arrow.png';
    	$this->topic_emoticons [4] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-love.png';
    	$this->topic_emoticons [5] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-grin.png';
    	$this->topic_emoticons [6] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-shock.png';
    	$this->topic_emoticons [7] = JURI::root () . 'components/com_kunena/template/default/images/icons/topic-smile.png';

		// Include the kunenalatest functions only once
		require_once (dirname ( __FILE__ ) . '/helper.php');

		$this->params = $params;
		$this->klistpost = modKunenaLatestHelper::getKunenaLatestList ( $params );

		require (JModuleHelper::getLayoutPath ( 'mod_kunenalatest' ));
	}
}