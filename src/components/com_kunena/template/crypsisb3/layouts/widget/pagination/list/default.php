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

<nav class="hidden-xs">
	<ul class="pagination">
		<?php
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->start);
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->previous);

		foreach ($data->pages as $k => $item)
		{
			if ($last + 1 != $k)
			{
				echo '<li><a class="disabled">...</a></li>';
			}

			$last = $k;

			echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
		}

		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->next);
		echo $this->subLayout('Widget/Pagination/Item')->set('item', $data->end);
		?>
	</ul>
</nav>

<nav class="visible-xs-block">
	<ul class="pagination">
		<?php
		foreach ($data->pages as $k => $item)
		{
			echo $this->subLayout('Widget/Pagination/Item')->set('item', $item);
		}
		?>
	</ul>
</nav>
