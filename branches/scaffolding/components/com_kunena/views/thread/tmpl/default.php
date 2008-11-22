<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>
<table class="" id="" cellpadding="2" cellspacing="0" border="1" width="100%">
	<thead>
		<tr>
			<th align="left">
				<div class="">
					<strong><?php echo JText::_('KUNENA_KUNENA_TOPIC'); ?></strong>
					<?php echo $this->thread->subject; ?>
				</div>
				<!-- FORUM TOOLS -->
				<!-- /FORUM TOOLS -->
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
<?php
if (!empty($this->thread->messages)) {
	foreach ($this->thread->messages as $message)
	{
		var_dump($message);
//		echo $this->loadTemplate('message');
	}
}
?>
			</td>
		</tr>
<?php if ($this->_layout == 'threaded') : ?>
		<tr>
			<td>
				<?php echo $this->loadTemplate('threaded'); ?>
			</td>
		</tr>
<?php endif; ?>
	</tbody>
</table>
