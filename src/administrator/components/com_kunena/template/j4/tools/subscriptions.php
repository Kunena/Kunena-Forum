<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      SyncUsers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<div class="kadmin-functitle icon-config"><?php echo Text::_('Subscriptions'); ?></div>
				<table class="adminform">
					<tr>
						<th><?php echo Text::_('Active Category Subscriptions'); ?></th>
					</tr>
					<?php
					foreach ($this->cat_subscribers_users as $user)
						:
						?>
						<tr>
							<td><?php echo $user->id ?></td>
							<td><?php echo $user->username ?></td>
							<td><?php echo $user->email ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<th><?php echo Text::_('Active Topic Subscriptions'); ?></th>
					</tr>
					<?php
					foreach ($this->topic_subscribers_users as $user)
						:
						?>
						<tr>
							<td><?php echo $user->id ?></td>
							<td><?php echo $user->username ?></td>
							<td><?php echo $user->email ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<th><?php echo Text::_('E-mails sent to'); ?></th>
					</tr>
					<?php
					foreach ($this->cat_topic_subscribers as $sub)
						:
						?>
						<tr>
							<td><?php echo $sub->id ?></td>
							<td><?php echo $sub->username ?></td>
							<td><?php echo $sub->email ?></td>
							<td><?php echo intval($sub->subscription) ?></td>
							<td><?php echo intval($sub->moderator) ?></td>
							<td><?php echo intval($sub->admin) ?></td>
						</tr>
					<?php endforeach ?>
				</table>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
