<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Pagination
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
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
	<ul>
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
