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

$display = isset($this->display);

$data = $this->pagination->getData();

$count = count($data->pages);

if ($count == 0)
{
	return;
}
elseif ($count == 1 && empty($display)) return;
$last = 0;
?>

<nav class="hidden-xs-down">
	<ul class="pagination">
		<?php
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->start);
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->previous);

		foreach ($data->pages as $k => $item)
		{
			if ($last + 1 != $k)
			{
				echo '<li class="page-item"><a class="disabled">...</a></li>';
			}

			$last = $k;

			echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
		}

		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->next);
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->end);
		?>
	</ul>
</nav>

<nav class="d-block d-sm-none">
	<ul class="pagination">
		<?php
		foreach ($data->pages as $k => $item)
		{
			echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
		}
		?>
	</ul>
</nav>
