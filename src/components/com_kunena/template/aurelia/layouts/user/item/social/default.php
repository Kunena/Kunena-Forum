<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$showAll = isset($this->showAll) ? $this->showAll : false;
?>
<div class="inline float-right">
	<?php foreach ($this->socials as $key => $social)
	{
		if (!empty($this->profile->$key))
		{
			echo $this->profile->socialButtonsTemplate($key, $showAll);
		}
	}
	?>
</div>
