<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-config"><?php echo JText::_('Subscriptions'); ?></div>
			<table class="adminform">
				<tr>
					<th><?php echo JText::_('Active Category Subscriptions'); ?></th>
				</tr>
				<?php
					$topic = KunenaForumTopicHelper::get($this->id);
					$acl = KunenaAccess::getInstance();
					$subscribers = $acl->loadSubscribers($topic, KunenaAccess::CATEGORY_SUBSCRIPTION);
					foreach ($subscribers as $sub) :
						// FIXME: inefficient to load users one by one
						$user = JFactory::getUser($sub);
				?>
				<tr>
					<td><?php echo $user->id ?></td>
					<td><?php echo $user->username ?></td>
					<td><?php echo $user->email ?></td>
				</tr>
				<?php endforeach ?>
				<tr>
					<th><?php echo JText::_('Active Topic Subscriptions'); ?></th>
				</tr>
				<?php
					$acl = KunenaAccess::getInstance();
					$subscribers = $acl->loadSubscribers($topic, KunenaAccess::TOPIC_SUBSCRIPTION);
					foreach ($subscribers as $sub) :
						$user = JFactory::getUser($sub);
				?>
				<tr>
					<td><?php echo $user->id ?></td>
					<td><?php echo $user->username ?></td>
					<td><?php echo $user->email ?></td>
				</tr>
				<?php endforeach ?>
				<tr>
					<th><?php echo JText::_('E-mails sent to'); ?></th>
				</tr>
				<?php
					$subscribers = $acl->getSubscribers($topic->getCategory()->id, $this->id, KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION, 1, 1);
					foreach ($subscribers as $sub) :
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
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
