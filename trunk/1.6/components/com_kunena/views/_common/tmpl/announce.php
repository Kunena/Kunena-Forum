<?php
/**
 * @version		$Id:$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<?php if (isset($this->announcements)) foreach ($this->announcements as $this->current=>$this->announcement): ?>									
<table class="forum_body announcements">
	<thead>
		<tr>
			<th>
				<h3>
					<a href=""><?php echo $this->escape($this->announcement->title); ?></a>
				</h3>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="fcol announce_summary">
				<span><?php echo JHTML::_('date', $this->announcement->created); ?></span>
				<?php echo $this->escape($this->announcement->sdescription); ?>
				<a href=""><?php echo JText::_('K_READ_MORE'); ?></a>
			</td>
		</tr>
	</tbody>
</table>
<?php endforeach; ?>

<div class="clr"></div>