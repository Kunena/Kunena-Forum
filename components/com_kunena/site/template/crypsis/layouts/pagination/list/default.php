<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Pagination
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$data = $this->pagination->getData();
if (count($data->pages) <= 1) return;
?>

<div class="pagination pagination-small">
	<ul>
		<?php
		$last = 0;
		foreach($data->pages as $k=>$item) {
			if ($last+1 != $k) {
				echo '<li class="disabled">...</li>';
			}
			$last = $k;

			if (!is_null($item->base)) {
				// Check if the item can be clicked.
				$limit = 'limitstart.value=' . (int) $item->base;
				echo '<li><a href="' . $item->link . '" title="' . $item->text . '">' . $item->text . '</a></li>';
			} elseif (!empty($item->active)) {
				// Check if the item is the active (or current) page.
				echo '<li class="active"><a>' . $item->text . '</a></li>';
			} else {
				// Doesn't match any other condition, render disabled item.
				echo '<li class="disabled"><a>' . $item->text . '</a></li>';
			}
		}
		?>
	</ul>
</div>
