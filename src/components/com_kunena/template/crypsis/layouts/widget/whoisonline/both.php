<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Statistics
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<?php if (!empty($this->onlineList)) : ?>
	<div id="whoisonlinelist">
		<?php
		foreach ($this->onlineList as $user)
		{
			$avatar       = $user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' ', 20, 20);
			$onlinelist[] = $user->getLink($avatar, null, '') . $user->getLink();
		}
		?>
		<?php echo implode(', ', $onlinelist); ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->hiddenList)) : ?>
	<div id="whoisonlinehidden">
		<span><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>:</span>

		<?php
		foreach ($this->hiddenList as $user)
		{
			$avatar       = $user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' ', 20, 20);
			$hiddenlist[] = $user->getLink($avatar, null, '') . $user->getLink();
		}
		?>
		<?php echo implode(', ', $hiddenlist); ?>
	</div>
<?php endif; ?>

