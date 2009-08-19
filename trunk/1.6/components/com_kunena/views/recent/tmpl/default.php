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
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
	<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>

<?php echo $this->loadTemplate('top'); ?>
		
		<div class="corner_tl">
			<div class="corner_tr">
				<div class="corner_br">
					<div class="corner_bl">
						<form action="index.php" method="post" name="bodyform">
							<table class="forum_body">
								<thead>
									<tr>
										<td class="col1"><?php echo JText::_('K_REPLIES'); ?></td>
										<td class="col2">&nbsp;</td>
										<td class="col3"><?php echo JText::_('K_TOPICS'); ?></td>
										<td class="col4"><?php echo JText::_('K_LAST_POST'); ?></td>
									</tr>
								</thead>
								<tbody>

<!--
<pre><?php print_r($this->threads[0]); ?></pre> 
-->
<?php
foreach ($this->threads as $this->current=>$this->thread):
	echo $this->loadTemplate('thread'); 
endforeach;
?>

								</tbody>
							</table>
															<input type="hidden" name="Itemid" value="125"/>
															<input type="hidden" name="option" value="com_kunena"/>
															<input type="hidden" name="func" value="bulkactions" />
															<input type="hidden" name="return" value="/forum" />
														</form>
													
												</div>
											</div>
										</div>
									</div>

<?php echo $this->loadTemplate('bottom'); ?>

<?php echo $this->loadCommonTemplate('footer'); ?>
	</div>