<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$user        = isset($this->user) ? $this->user : KunenaUserHelper::getMyself();
$status      = $user->getStatus();
$status_text = $user->getStatusText();
$link        = $user->getURL();

switch ($status)
{
	case 0:
		$label = Text::_("COM_KUNENA_ONLINE");
		$state = "success";
		break;
	case 1:
		$label = Text::_("COM_KUNENA_AWAY");
		$state = "warning";
		break;
	case 2:
		$label = Text::_("COM_KUNENA_BUSY");
		$state = "important";
		break;
	case 3:
		$label = Text::_("COM_KUNENA_INVISIBLE");
		$state = "default";
		break;
	default:
		$label = Text::_("COM_KUNENA_OFFLINE");
		$state = "default";
		break;
}

if (!$user->showOnline)
{
	$label = Text::_("COM_KUNENA_OFFLINE");
	$state = "default";
}

echo $this->subLayout('Widget/Label')
	->set('label', $label)
	->set('description', $status_text)
	->set('state', $state)
	->set('link', $link)
	->setLayout('link');
