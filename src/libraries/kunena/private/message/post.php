<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Private
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Private message mapping to forum message.
 *
 * @property int $private_id
 * @property int $message_id
 *
 * @since   Kunena 6.0
 */
class KunenaPrivateMessagePost extends KunenaDatabaseObject
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaPrivatePostMap';
}
