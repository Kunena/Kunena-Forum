<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Prune
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
			  method="post" id="adminForm"
			  name="adminForm">
			<input type="hidden" name="task" value="cleanupip"/>
			<?php echo HTMLHelper::_('form.token'); ?>

			<fieldset>
				<legend><?php echo Text::_('COM_KUNENA_LEGEND_CLEANUP_IP'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td width="40%"><?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_FROMDAYS') ?></td>
                        <td width="5%"><input type="text" name="cleanup_ip_days" value="30"/></td>
                        <td width="55%"><?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_DAYS'); ?></td>
					</tr>
					<tr>
						<td width="40%"><?php echo Text::_('COM_KUNENA_DELETE_USERS_IP') ?></td>
						<td width="5%"><input type="checkbox" name="deleteipusers" value="1"/></td>
						<td width="55%"><?php echo Text::_('COM_KUNENA_DELETE_USERS_IP_DESC'); ?></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
