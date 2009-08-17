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
		<div class="bottom_info_box">
			<div class="pagination_box">
				<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<div class="discussions">
				<span><?php echo $this->pagination->getResultsCounter(); ?></span> <?php echo JText::_('K_DISCUSSIONS'); ?> 
			</div>
		</div>