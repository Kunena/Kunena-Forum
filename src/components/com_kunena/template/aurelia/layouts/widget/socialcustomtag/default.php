<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use function defined;

if (\Kunena\Forum\Libraries\User\Helper::getMyself()->socialshare == 0 && \Kunena\Forum\Libraries\User\Helper::getMyself()->exists())
{
	return false;
}

$this->ktemplate = \Kunena\Forum\Libraries\Factory\KunenaFactory::getTemplate();
$socialsharetag  = $this->ktemplate->params->get('socialsharetag');

echo HTMLHelper::_('content.prepare', $socialsharetag);
