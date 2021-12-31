<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"
					  method="post" id="adminForm" name="adminForm">
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="boxchecked" value="0"/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<div class="btn-group pull-right hidden-phone">
						<label for="limit"
							   class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					</div>

					<table class="table table-striped">
						<thead>
						<tr>
							<th width="1%"></th>
							<th><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
							<th class="center"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT'); ?></th>
							<th><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
							<th><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
							<th><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?></th>
							<th><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="7">
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php foreach ($this->templates as $id => $row)
							:
							?>
							<tr>
								<td>
									<input type="radio" id="cb<?php echo $this->escape($row->directory); ?>"
										   name="cid[]"
										   value="<?php echo $this->escape($row->directory); ?>"
										   onclick="Joomla.isChecked(this.checked);"/>
								</td>
								<td>
									<?php $img_path = Uri::root(true) . '/components/com_kunena/template/' . $row->directory . '/assets/images/template_thumbnail.png'; ?>
									<span class="editlinktip hasTip"
										  title="<?php echo $this->escape($row->name . '::<img loading=lazy border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
										<a href="<?php echo Route::_('index.php?option=com_kunena&view=template&layout=edit&name=' . $this->escape($row->directory)); ?>"
										   title="<?php echo $this->escape($row->name); ?>">
													<?php echo $this->escape($row->name); ?></a>
							</span>
								</td>
								<td class="center">
									<?php if ($row->published == 1)
										:
										?>
										<a class="tbody-icon jgrid" title="Default"><span
													class="icon-featured" style="color: green; border: 2px solid green;"></span></a>
										
									<?php else

										:
										?>
										<a href="javascript: void(0);"
										   onclick="return Joomla.listItemTask('cb<?php echo urlencode($row->directory); ?>','publish')">
											<span class="icon-featured pl-2"
												  title="<?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_DEFAULT'); ?>"></span>
										</a>
									<?php endif; ?>
								</td>
								<td>
									<?php echo $row->authorEmail ? '<a href="mailto:' . $this->escape($row->authorEmail) . '">' . $this->escape($row->author) . '</a>' : $this->escape($row->author); ?>
								</td>
								<td>
									<?php echo $this->escape($row->version); ?>
								</td>
								<td>
									<?php echo $this->escape($row->creationdate); ?>
								</td>
								<td>
									<a href="<?php echo $this->escape($row->authorUrl); ?>" target="_blank"
									   rel="noopener noreferrer"><?php echo $this->escape($row->authorUrl); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<table class="table table-striped" style="padding-top: 200px;">
						<thead>
						<tr>
							<td colspan="7">
								<strong><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_PREMIUM'); ?></strong></td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<th width="5%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_PRICE'); ?></th>
							<th width="5%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TYPE'); ?></th>
							<th width="5%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
							<th width="15%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
							<th width="10%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?></th>
							<th width="10%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
							<th width="10%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></th>
							<th width="25%"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
							<th width="30%"></th>
						</tr>
						<?php if ($this->templatesxml !== false) : ?>
							<?php foreach ($this->templatesxml as $row) : ?>
								<tr>
									<td style="width: 5%;"><?php echo $row->price; ?>
									</td>
									<td style="width: 5%;"><?php echo $row->type == 'paid-template' ? Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_PAID') : Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FREE'); ?>
									</td>
									<td style="width: 5%;"><?php echo $row->version; ?>
									</td>
									<td style="width: 15%;">
									<span class="editlinktip hasTip"
										  title="<?php echo $row->name . $this->escape('::<img loading=lazy border="1" src="' . $row->thumbnail . '" name="imagelib" alt="' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
										<a href="<?php echo $row->detailsurl; ?>" target="_blank"
										   rel="noopener noreferrer"><?php echo $row->name; ?></a>
									</span>
									</td>
									<td style="width: 10%;">
										<?php echo $row->created; ?>
									</td>
									<td style="width: 10%;">
										<a href="mailto:<?php echo $row->authoremail; ?>"><?php echo $row->author; ?></a>
									</td>
									<td style="width: 10%;">
										<a href="<?php echo $row->detailsurl; ?>" target="_blank"
										   rel="noopener noreferrer"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></a>
									</td>
									<td style="width: 25%;">
										<a href="<?php echo $row->authorurl; ?>" target="_blank"
										   rel="noopener noreferrer"><?php echo $row->authorurl; ?></a>
									</td>
									<td style="width: 30%;">
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td style="width: 30%;"><?php echo Text::_('COM_KUNENA_ADMIN_TEMPLATE_MANAGER_ERROR_CANNOT_CONNECT_TO_KUNENA_SERVER'); ?>
								</td>
							</tr>
						<?php endif; ?>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
