<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      SyncUsers
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
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
					  method="post"
					  id="adminForm" name="adminForm">
					<input type="hidden" name="task" value=""/>
					<?php echo HTMLHelper::_('form.token'); ?>

					<table class="table table-striped" id="Subscriptions">
						<tr>
							<th><?php echo Text::_('Active Category Subscriptions'); ?></th>
						</tr>

						<?php
						foreach ($this->catSubscribersUsers as $user)
						:
							?>
							<tr>
								<td class="hidden-phone center">
									<input type="checkbox" id="cb<?php echo $user->id; ?>" name="cid[]"
										   value="<?php echo $this->escape($user->id); ?>"
										   onclick="Joomla.isChecked(this.checked);"/>
								</td>
								<td><?php echo $user->id ?></td>
								<td><?php echo $user->username ?></td>
								<td><?php echo $user->email ?></td>
							</tr>
						<?php endforeach ?>
						<tr>
							<th><?php echo Text::_('Active Topic Subscriptions'); ?></th>
						</tr>
						<?php
						foreach ($this->topicSubscribersUsers as $user)
						:
							?>
							<tr>
								<td class="hidden-phone center">
									<input type="checkbox" id="cb<?php echo $user->id; ?>" name="cid[]"
										   value="<?php echo $this->escape($user->id); ?>"
										   onclick="Joomla.isChecked(this.checked);"/>
								</td>
								<td><?php echo $user->id ?></td>
								<td><?php echo $user->username ?></td>
								<td><?php echo $user->email ?></td>
							</tr>
						<?php endforeach ?>
						<tr>
							<th><?php echo Text::_('E-mails sent to'); ?></th>
						</tr>
						<?php
						foreach ($this->catTopicSubscribers as $sub)
						:
							?>
							<tr>
								<td class="hidden-phone center">
									<input type="checkbox" id="cb<?php echo $sub->id; ?>" name="cid[]"
										   value="<?php echo $this->escape($sub->id); ?>"
										   onclick="Joomla.isChecked(this.checked);"/>
								</td>
								<td><?php echo $sub->id ?></td>
								<td><?php echo $sub->username ?></td>
								<td><?php echo $sub->email ?></td>
								<td><?php echo intval($sub->subscription) ?></td>
								<td><?php echo intval($sub->moderator) ?></td>
								<td><?php echo intval($sub->admin) ?></td>
							</tr>
						<?php endforeach ?>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
