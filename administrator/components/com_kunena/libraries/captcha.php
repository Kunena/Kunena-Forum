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

class KunenaCaptcha {
	public static function enabled() {
		if (KunenaFactory::getConfig()->captcha == 1 && !KunenaFactory::getUser()->exists())
			return true;
		return false;
	}

	public static function display() {
		if (! self::enabled ())
			return false;

		$dispatcher = JDispatcher::getInstance ();
		$results = $dispatcher->trigger ( 'onCaptchaRequired', array ('kunena.post' ) );

		if (! JPluginHelper::isEnabled ( 'system', 'captcha' ) || ! $results [0]) {
			echo JText::_ ( 'COM_KUNENA_CAPTCHA_NOT_CONFIGURED' );
			return false;
		}

		if ($results [0]) {
			$dispatcher->trigger ( 'onCaptchaView', array ('kunena.post', 0, '', '<br />' ) );
		}
		return true;
	}

	public static function verify() {
		if (! self::enabled ())
			return true;

		$app = JFactory::getApplication ();
		$dispatcher = JDispatcher::getInstance ();
		$results = $dispatcher->trigger ( 'onCaptchaRequired', array ('kunena.post' ) );

		if (! JPluginHelper::isEnabled ( 'system', 'captcha' ) || ! $results [0]) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHA_CANNOT_CHECK_CODE' ), 'error' );
			return false;
		}

		if ($results [0]) {
			$captchaparams = array (
				JRequest::getVar ( 'captchacode', '', 'post' ),
				JRequest::getVar ( 'captchasuffix', '', 'post' ),
				JRequest::getVar ( 'captchasessionid', '', 'post' ) );
			$results = $dispatcher->trigger ( 'onCaptchaVerify', $captchaparams );
			if (! $results [0]) {
				$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CAPTCHACODE_DO_NOT_MATCH' ), 'error' );
				return false;
			}
		}
		return true;
	}
}