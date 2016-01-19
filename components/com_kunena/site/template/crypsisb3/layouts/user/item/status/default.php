<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$user = isset($this->user) ? $this->user : KunenaUserHelper::getMyself();
$status = $user->getStatus();
$status_text = $user->getStatusText();
$link = $user->getURL();

switch ($status)
{
	case 0:
		$label = JText::_("COM_KUNENA_ONLINE");
		$state = "success";
		break;
	case 1:
		$label = JText::_("COM_KUNENA_AWAY");
		$state = "warning";
		break;
	case 2:
		$label = JText::_("COM_KUNENA_BUSY");
		$state = "important";
		break;
	case 3:
		$label = JText::_("COM_KUNENA_INVISIBLE");
		$state = "default";
		break;
	default:
		$label = JText::_("COM_KUNENA_OFFLINE");
		$state = "default";
		break;
}

echo $this->subLayout('Widget/Label')
	->set('label', $label)
	->set('description', $status_text)
	->set('state', $state)
	->set('link', $link)
	->setLayout('link');
