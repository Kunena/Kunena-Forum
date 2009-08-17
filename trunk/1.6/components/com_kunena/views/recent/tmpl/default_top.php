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
$this->filter_time_options = array(
	4=>'4 '.JText::_('K_HOURS'),
	8=>'8 '.JText::_('K_HOURS'),
	12=>'12 '.JText::_('K_HOURS'),
	24=>'24 '.JText::_('K_HOURS'),
	48=>'48 '.JText::_('K_HOURS'),
	168=>JText::_('K_WEEK'),
	720=>JText::_('K_MONTH'),
	8760=>JText::_('K_YEAR')
);
$filter_time = JRequest::getVar('filter_time');
?>
		<div class="top_info_box">
			<div class="choose_time">
				<form name="choose_timeline" method="post" target="_self" action="<?php JRequest::getURI(); ?>">
				<select class="input_time" onchange="this.form.submit();" name="filter_time">
<?php foreach ($this->filter_time_options as $key=>$time): ?>
					<option value="<?php echo $key; ?>"<?php if ($filter_time == $key) echo ' selected="selected"'; ?>><?php echo $time; ?></option>
<?php endforeach; ?>
				</select>
				</form>
			</div>
			<div class="choose_forumcat">
				<form name="choose_forum" method="post" target="_self" action="/forum">
					<input type="hidden" name="func" value="showcat"/>
					<select name="catid" class="input_forum" onchange="if(this.options[this.selectedIndex].value> 0){ this.form.submit() }">
						<option value="0" selected="selected"><?php echo JText::_('K_BOARD_CATEGORIES'); ?></option>
						<option value="94">Kunena - To Speak!</option>
						<option value="77">...&nbsp;General Talk about Kunena</option>
						<option value="119">...&nbsp;Feature Requests</option>
						<option value="136">...&nbsp;Templates and Design</option>
					</select>
					<input type="submit" name="<?php echo JText::_('K_GO'); ?>" class="go_btn" value="<?php echo JText::_('K_GO'); ?>"/>
				</form>
			</div>
			<div class="pagination_box">
				<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<div class="discussions">
				<span><?php echo $this->pagination->getResultsCounter(); ?></span> <?php echo JText::_('K_DISCUSSIONS'); ?>
			</div>
			
		</div>
		<div class="clr"></div>