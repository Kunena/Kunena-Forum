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

<!-- Kunena -->
<div id="Kunena">
<?php echo (!empty($this->offline_messqge)) ? $this->offline_message : null; ?>
<table class="header" width="100%" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="" align="left" width="1%">
				<img id="BoxSwitch_topprofilebox__topprofilebox_tbody" class="hideshow" src="{PB_IMGSWITCHURL}" alt="" />
			</th>

			<th class="" align="left" width="10%" nowrap="nowrap">
				<span class="board-title"><?php echo $this->params->get('board_title'); ?></span>
			</th>

			<th class="" align="center">
				<?php //echo $this->menu; ?>
				<?php echo $this->loadCommonTemplate('header_menu'); ?>
			</th>

			<th class="" width="5%" align="right">
				<div id="searchbox">
					<form action="<?php echo JRoute::_('index.php?option=com_kunena&view=search');?>" name="searchFB" method="post">
						<input class="fb_search_inputbox fbs" type="text" name="searchword" size="<?php min( 15, strlen(JText::_('KUNENA_GEN_SEARCH_BOX')) );?>" value="<?php echo JText::_('KUNENA_GEN_SEARCH_BOX');?>" onblur="if(this.value=='') this.value='<?php echo JText::_('KUNENA_GEN_SEARCH_BOX', true); ?>';" onfocus="if(this.value=='<?php echo JText::_('KUNENA_GEN_SEARCH_BOX', true);?>') this.value='';" />
						<input type="submit" value="<?php echo JText::_('KUNENA_KUNENA_GO');?>" name="submit" class="fb_search_button fbs" />
					</form>
				</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</tbody>
</table>
