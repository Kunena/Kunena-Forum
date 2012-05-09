<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$version = reset($this->versions);
?>
<table class="kinstaller">
	<?php if (!empty($version->state)) : array_shift($this->versions); ?>
	<tr><th style="font-size: 1.5em; color: #CC0000;"><?php echo JText::_('COM_KUNENA_INSTALL_DETECT_FAILED') ?></th></tr>
	<tr><td><?php echo JText::sprintf('COM_KUNENA_INSTALL_DETECT_FAILED_DESC',$version->version, '') ?></td></tr>
	<?php else: ?>
	<tr><th class="klarge"><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING') ?></th></tr>
	<?php if ($version->action != 'REINSTALL') : ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_DESC') ?></td></tr>
	<?php else: ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_DESC_REINSTALL') ?></td></tr>
	<?php endif; ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_BACKUP') ?></td></tr>
	<?php endif; ?>
</table>

<?php foreach ($this->versions as $type=>$version) :
switch ($type) {
	case 'kunena':
		$style = 'btn-style-green';
		break;
	case 'uninstall':
		$style = 'btn-style-red';
		break;
	default:
		$style = 'btn-style-yellow';
}
?>
<table class="kinstaller-btn">
	<tr>
		<th class="left"><?php echo $version->description; ?></th>
	</tr>
	<tr>
		<td class="left"><input type="button"
			onclick="window.location='<?php echo $version->link; ?>'"
			value="<?php echo $version->label; ?>"
			 class="kbutton <?php echo $style ?>" />
		</td>
	</tr>
	<tr>
		<td class="left">
			<div><?php echo $version->hint ?></div>
			<?php if (!empty($version->warning)) : ?><br /><div><?php echo $version->warning ?></div><?php endif; ?>
		</td>
	</tr>
</table>
<?php endforeach; ?>
