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
use function defined;

if ($this->me->socialshare == 0 && $this->me->exists())
{
	return false;
}

$socialtheme = $this->ktemplate->params->get('socialtheme');
$this->addStyleSheet('jssocials.css');
$this->addStyleSheet('jssocials-theme-' . $socialtheme . '.css');
$this->addScript('jssocials.js');
?>

<div id="share"></div>
