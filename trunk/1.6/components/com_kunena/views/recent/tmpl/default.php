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
<?php echo $this->loadCommonTemplate('header'); ?>

<?php echo $this->loadTemplate('header'); ?>
		
		<div class="corner1">
			<div class="corner2">
				<div class="corner3">
					<div class="corner4">
						<form action="index.php" method="post" name="">
							<table class="forum_body">
								<thead>
									<tr>
										<td>Replies</td>
										<td>&nbsp;</td>
										<td>Topics</td>
										<td>Last Post</th>
									</tr>
								</thead>
								<tbody>

<?php echo $this->loadTemplate('thread'); ?>

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
									</div>

<?php echo $this->loadTemplate('footer'); ?>

<?php echo $this->loadCommonTemplate('footer'); ?>