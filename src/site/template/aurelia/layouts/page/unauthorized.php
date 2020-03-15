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

?>
<div id="kunena" class="layout">
	<?php
	echo $this->subLayout('Widget/MenuBar');

	// Display current view/layout
	echo $this->content;

	echo $this->subLayout('Widget/Footer');
	?>
</div>
