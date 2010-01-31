<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2010 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// no direct access
defined('_JEXEC') or die();

jimport( 'joomla.plugin.plugin' );

class plgSystemJFirePHP extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemJFirePHP(& $subject, $config)
	{
		$this->_db = JFactory::getDBO();
		parent :: __construct($subject, $config);
	}

	/**
	 * onAfterInitialise handler
	 *
	 * Register FirePHP libraries and set options according to paramters
	 *
	 * @access	public
	 * @return null
	 */

	function onAfterInitialise()
	{
		// php version check to allow usage in php 4.x
		$phpver = explode(".",phpversion());
		if (intval($phpver[0])==4) {
			require_once(JPATH_PLUGINS.DS.'system'.DS.'jfirephp'.DS.'firephpcore'.DS.'fb.php4');
		} else {
			require_once(JPATH_PLUGINS.DS.'system'.DS.'jfirephp'.DS.'firephpcore'.DS.'fb.php');
		}

		// JFirePHP is installed and loaed
		define('JFIREPHP', 1);

		$firephp = FirePHP::getInstance(true);

		// Before doing any checks lets disable logging
		$firephp->setEnabled(false);

		// Check if the integration is set to enabled
		$enable = $this->params->get('enable', 0);

		// Only turn on if enabled
		if($enable){
			// if limited to debug mode, check JDEBUG
			$limittodebug = $this->params->get('limittodebug', 1);
			if(!$limittodebug || JDEBUG){
				// We are enabled and either in Debug mode, or it does not matter
				$firephp->setEnabled(true);

				$verbose = $this->params->get('verbose', 0);

				if($verbose){
					$firephp->group('JFirePHP Startup',array('Collapsed' => true,'Color' => '#FF4000'));
					$firephp->log('JFirePHP enabled! - Verbose Output Mode: ON');
				}

				$options = array('maxObjectDepth' => intval($this->params->get('maxObjectDepth', 10)),
				                 'maxArrayDepth' => intval($this->params->get('maxArrayDepth', 20)),
				                 'useNativeJsonEncode' => intval($this->params->get('useNativeJsonEncode', 1)),
				                 'includeLineNumbers' => intval($this->params->get('includeLineNumbers', 1)));

				$firephp->setOptions($options); // Alternate call syntax: FB::setOptions($options);

				if($verbose) $firephp->log('JFirePHP: Options Set - maxObjectDepth:'.$options['maxObjectDepth'].
														 ' maxArrayDepth:'.$options['maxArrayDepth'].
														 ' useNativeJsonEncode:'.$options['useNativeJsonEncode'].
														 ' includeLineNumbers:'.$options['includeLineNumbers']);

				$redirectphp = $this->params->get('redirectphp', 0);

				if($redirectphp){
					// Convert E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE and
					// E_RECOVERABLE_ERROR errors to ErrorExceptions and send all Exceptions to Firebug automatically
					$firephp->registerErrorHandler(true);
					$firephp->registerExceptionHandler();
					$firephp->registerAssertionHandler(true, false);

					if($verbose) $firephp->log('JFirePHP: E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE and E_RECOVERABLE_ERROR redirected.');
				}

				if($verbose) $firephp->groupEnd();
			}
		}
	}

}

