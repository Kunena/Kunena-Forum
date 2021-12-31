<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Pagination
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

$data = $this->pagination->getData();

if (count($data->pages) <= 1)
{
	return;
}

$last = 0;
?>

<div class="pagination pagination-small">
	<ul class="pagination">
		<?php
		foreach ($data->pages as $k => $item)
		{
			if ($last + 1 != $k)
			{
				echo '<li><a class="disabled">...</a></li>';
			}

			$last = $k;

			echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
		}
		?>
	</ul>
</div>
