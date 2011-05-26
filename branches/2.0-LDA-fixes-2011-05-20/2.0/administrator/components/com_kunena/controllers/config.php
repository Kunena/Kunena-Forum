<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.error' );

/**
 * Kunena Backend Config Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerConfig extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=config';
	}

	function save() {
		$app = JFactory::getApplication ();
		$config = KunenaFactory::getConfig ();
		$db = JFactory::getDBO ();

		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		foreach ( JRequest::get('post', JREQUEST_ALLOWHTML) as $postsetting => $postvalue ) {
			if (JString::strpos ( $postsetting, 'cfg_' ) === 0) {
				//remove cfg_ and force lower case
				if ( is_array($postvalue) ) {
					$postvalue = implode(',',$postvalue);
				}
				$postname = JString::strtolower ( JString::substr ( $postsetting, 4 ) );

				// No matter what got posted, we only store config parameters defined
				// in the config class. Anything else posted gets ignored.
				if (array_key_exists ( $postname, $config->GetClassVars () )) {
					if (is_numeric ( $postvalue )) {
      					$config->$postname = intval($postvalue);
     				} else {
      					$config->$postname = strval($postvalue);
     				}
				}
			}
		}

		$config->backup ();
		$config->remove ();
		$config->create ();

		$app->enqueueMessage ( JText::_('COM_KUNENA_CONFIGSAVED'));
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}

	function setdefault() {
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		$config = KunenaFactory::getConfig ();

		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$config->backup ();
		$config->remove ();
		$config = new CKunenaConfig();
		$config->create();

		$app->enqueueMessage ( JText::_('COM_KUNENA_CONFIG_DEFAULT'));
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
