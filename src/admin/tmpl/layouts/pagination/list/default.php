<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Layouts.Pagination
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

use Kunena\Forum\Libraries\Factory\KunenaFactory;

defined('_JEXEC') or die();

$data     = $this->pagination->getData();
$template = KunenaFactory::getAdminTemplate();

if ($data->pages && count($data->pages) <= 1)
{
	return;
}
?>
<ul class="pagination pagination-sm justify-content-center">
	<?php echo $template->paginationItem($data->start); ?>
	<?php echo $template->paginationItem($data->previous); ?>

	<?php foreach ($data->pages as $k => $page)
	{
		echo $template->paginationItem($page);
	} ?>

	<?php echo $template->paginationItem($data->next); ?>
	<?php echo $template->paginationItem($data->end); ?>
</ul>
