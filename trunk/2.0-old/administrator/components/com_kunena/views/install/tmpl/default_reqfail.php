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
?>
<table
	style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both;">
	<tr>
		<th colspan="3">REQUIREMENTS CHECK: <font color="red">FAILED</font> -
		<a href="http://docs.kunena.com/index.php/Technical_Requirements">Minimum
		Version Requirements not satisfied</a></th>
	</tr>
	<tr>
		<td>PHP version:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['php'])?'red':'green'; ?>"><?php echo $this->requirements->php; ?></font></td>
		<td>(Required &gt;= <?php echo KUNENA_MIN_PHP; ?>)</td>
	</tr>
	<tr>
		<td>MySQL version:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['mysql'])?'red':'green'; ?>"><?php echo $this->requirements->mysql; ?></font></td>
		<td>(Required &gt;= <?php echo KUNENA_MIN_MYSQL; ?>)</td>
	</tr>
	<tr>
		<td>Joomla version:</td>
		<td><font
			color="<?php echo isset($this->requirements->fail['joomla'])?'red':'green'; ?>"><?php echo $this->requirements->joomla; ?></font></td>
		<td>(Required &gt;= <?php echo KUNENA_MIN_JOOMLA; ?>)</td>
	</tr>
</table>
