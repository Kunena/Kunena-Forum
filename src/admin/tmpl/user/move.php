<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post"
				  id="adminForm" name="adminForm">
				<input type="hidden" name="view" value="users"/>
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="1"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<fieldset>
					<legend><?php echo Text::_('COM_KUNENA_A_MOVE_USERMESSAGES'); ?></legend>
					<table class="table table-striped">
						<thead>
						<tr>
							<th width="25%">Ttitle</th>
							<th width="25%">Opiton</th>
							<th>Description</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<?php echo Text::_('COM_KUNENA_CATEGORY_TARGET'); ?>
							</td>
							<td>
								<?php
								echo $this->catsList;
								?>
							</td>
							<td>
								<strong><?php echo Text::_('COM_KUNENA_MOVEUSERMESSAGES_USERS_CURRENT'); ?></strong>
								<ol>
									<?php
									foreach ($this->users as $user)
									{
										echo '<li>' . $this->escape($user->username) . ' (' . Text::_('COM_KUNENA_TRASH_AUTHOR_USERID') . ' ' . $this->escape($user->id) . ')</li> ';
									}
									?>
								</ol>
							</td>
						</tr>
						</tbody>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
