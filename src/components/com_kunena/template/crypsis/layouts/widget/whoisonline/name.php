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
	<div>
		<?php
		foreach ($this->onlineList as $user)
		{
			$onlinelist[] = $user->getLink(null, null, '');
		}
		?>
		<?php echo implode(', ', $onlinelist); ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->hiddenList)) : ?>
	<div>
		<span><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>:</span>

		<?php
		foreach ($this->hiddenList as $user)
		{
			$hiddenlist[] = $user->getLink(null, null, '');
		}
		?>
		<?php echo implode(', ', $hiddenlist); ?>
	</div>
<?php endif; ?>
