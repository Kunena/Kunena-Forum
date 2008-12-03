<?php
/**
 * @version		$Id: default.php 5 2008-11-22 07:05:46Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>

<tr>
	<td>
		<a href="<?php echo JRoute::_('index.php?option=com_kunena&view=thread&cat_id='.$this->category->id.':'.$this->category->path.'&t_id='.$this->thread->id); ?>"><?php echo $this->thread->subject; ?></a>
	</td>
	<td>
		<?php echo $this->thread->name; ?>
	</td>
	<td>
		<?php echo $this->thread->replies; ?>
	</td>
	<td>
		<?php echo $this->thread->hits; ?>
	</td>
	<td>
		<?php echo JHtml::_('date', $this->thread->last_post_time); ?> by
		<?php echo $this->thread->last_post_name; ?>
	</td>
</tr>
