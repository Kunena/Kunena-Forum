<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*/
defined( '_JEXEC' ) or die();

require_once (KPATH_SITE . '/lib/kunena.defines.php');

class CKunenaTools {
		/**
		 * This function formats a number to n significant digits when above
		 * 10,000. Starting at 10,0000 the out put changes to 10k, starting
		 * at 1,000,000 the output switches to 1m. Both k and m are defined
		 * in the language file. The significant digits are used to limit the
		 * number of digits displayed when in 10k or 1m mode.
		 *
		 * @param int $number 		Number to be formated
		 * @param int $precision	Significant digits for output
		 */
		function formatLargeNumber($number, $precision = 4) {
			$output = '';
			// Do we need to reduce the number of significant digits?
			if ($number >= 10000){
				// Round the number to n significant digits
				$number = round ($number, -1*(log10($number)+1) + $precision);
			}

			if ($number < 10000) {
				$output = $number;
			} elseif ($number >= 1000000) {
				$output = $number / 1000000 . JText::_('COM_KUNENA_MILLION');
			} else {
				$output = $number / 1000 . JText::_('COM_KUNENA_THOUSAND');
			}

			return $output;
		}

		/**
		 * This function loads the appropriate template file
		 * It checks if the selected template contains an override
		 * and if so loads it. Fall back is the default template
		 * implementation
		 *
		 * @param string 	$relpath	Relative path to template file
		 * @param bool 		$once		limit to single include default false
		 * @param string 	$template	Custom path to template (relative to Joomla)
		 */
		function loadTemplate($relpath, $once=false, $template=null) {
			$template = KunenaFactory::getTemplate($template);
			if ($once){
				require_once (JPATH_ROOT.'/'.$template->getFile($relpath));
			} else {
				require (JPATH_ROOT.'/'.$template->getFile($relpath));
			}
		}

		/**
		 * Wrapper to addStyleSheet
		 *
		 */
		function addStyleSheet($filename) {

			$document = JFactory::getDocument ();
			$config = KunenaFactory::getConfig ();

			if ($config->debug || KunenaForum::isSvn()) {
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace ( '/\-min\./u', '.', $filename );
			}

			return $document->addStyleSheet ( $filename );
		}

		/**
		 * Wrapper to addScript
		 *
		 */
		function addScript($filename) {

			$document = JFactory::getDocument ();
			$config = KunenaFactory::getConfig ();

			if ($config->debug || KunenaForum::isSvn()) {
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace ( '/\-min\./u', '.', $filename );
			}

			return $document->addScript ( $filename );
		}

    } // end of class