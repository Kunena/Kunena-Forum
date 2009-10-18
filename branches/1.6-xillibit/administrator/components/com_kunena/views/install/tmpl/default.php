<?php
/**
 * @version		$Id: default.php 1020 2009-08-17 18:45:26Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

// Display the main toolbar.
$this->_displayMainToolbar();

$link = JURI::root().'administrator/index.php?option=com_kunena&task=install.install';

?>
<?php if (!empty($this->requirements->fail)): ?>
<table style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both;">
	<tr>
	<th colspan="3">INSTALLATION: <font color="red">FAILED</font> - <a href="http://docs.kunena.com/index.php/Technical_Requirements">Minimum Version Requirements not satisfied</a></th>
	</tr>
	<tr>
	<td>PHP version:</td>
	<td><font color="<?php echo isset($this->requirements->fail['php'])?'red':'green'; ?>"><?php echo $this->requirements->php; ?></font></td>
	<td>(Required &gt;= <?php echo KUNENA_MIN_PHP; ?>)</td>
	</tr>
	<tr>
	<td>MySQL version:</td>
	<td><font color="<?php echo isset($this->requirements->fail['mysql'])?'red':'green'; ?>"><?php echo $this->requirements->mysql; ?></font></td>
	<td>(Required &gt; <?php echo KUNENA_MIN_MYSQL; ?>)</td>
	</tr>
	<tr>
	<td>Joomla version:</td>
	<td><font color="<?php echo isset($this->requirements->fail['joomla'])?'red':'green'; ?>"><?php echo $this->requirements->joomla; ?></font></td>
	<td>(Required &gt; <?php echo KUNENA_MIN_JOOMLA; ?>)</td>
	</tr>
</table>
<?php else: ?>
<table style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both;">
	<tr>
		<th style="text-align: center;"><?php echo $this->txt_action; ?></th>
	</tr>
	<tr>
		<td style="text-align: center;">
			<input type="button" onclick="window.location='<?php echo $link; ?>'" value="<?php echo $this->txt_install; ?>" style="padding: 10px; text-align: center; font-weight: bold; background: #aadd44; border: solid 1px #669900; cursor: pointer;" />
		</td>
	</tr>
</table>
<?php endif; ?>
