<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
defined('_JEXEC') or die;
$version = reset($this->versions);
?>
<table style="text-align: center; border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both; width: 50em;">
	<tr><th style="font-size: 1.5em;"><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING') ?></th></tr>
	<?php if ($version->action != 'REINSTALL') : ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_DESC') ?></td></tr>
	<?php else: ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_DESC_REINSTALL') ?></td></tr>
	<?php endif; ?>
	<tr><td><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_WARNING_BACKUP') ?></td></tr>
</table>

<?php foreach ($this->versions as $type=>$version) :
switch ($type) {
	case 'kunena':
		$style = 'background: #aadd44; border: solid 1px #669900;';
		break;
	case 'uninstall':
		$style = 'background: #ff7777; border: solid 1px #993333;';
		break;
	default:
		$style = 'background: #ffff33; border: solid 1px #888800;';
}
?>
<table
	style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both; width: 50em;">
	<tr>
		<th style="font-size: 1.25em; text-align: center;"><?php echo $version->description; ?></th>
	</tr>
	<tr>
		<td style="text-align: center;"><input type="button"
			onclick="window.location='<?php echo $version->link; ?>'"
			value="<?php echo $version->label; ?>"
			style="padding: 10px; text-align: center; font-weight: bold; <?php echo $style ?> cursor: pointer;" />
		</td>
	</tr>
	<tr>
		<td style="text-align: center;">
			<div><?php echo $version->hint ?></div>
			<?php if (!empty($version->warning)) : ?><br /><div><?php echo $version->warning ?></div><?php endif; ?>
		</td>
	</tr>
</table>
<?php endforeach; ?>
