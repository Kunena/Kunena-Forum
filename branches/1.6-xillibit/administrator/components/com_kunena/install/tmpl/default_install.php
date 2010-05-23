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
<table>
<?php if ($this->status) foreach ($this->status as $status): ?>
	<tr>
		<td><?php echo $status['step']; ?>: <?php echo $status['task']; ?></td>
		<td style="color: <?php echo $status['success'] ? "green" : "red"; ?>">
		... <?php echo $status['success'] ? "OK" : "Failed"; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<br />
	<?php echo $status['msg']; ?>
