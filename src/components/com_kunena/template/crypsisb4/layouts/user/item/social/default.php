<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

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
