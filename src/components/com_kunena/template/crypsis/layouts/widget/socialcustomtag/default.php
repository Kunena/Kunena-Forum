<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Widget
 *
 * @copyright   (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

if (KunenaUserHelper::getMyself()->socialshare == 0 && KunenaUserHelper::getMyself()->exists())
{
        return false;
}

$this->ktemplate = KunenaFactory::getTemplate();
$socialsharetag  = $this->ktemplate->params->get('socialsharetag');

echo HTMLHelper::_('content.prepare', $socialsharetag);
