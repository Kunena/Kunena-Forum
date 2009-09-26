<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
$profile = KFactory::getProfile();
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>

		<div class="top_info_box">
			<div class="counter">
				<span><?php echo $this->pagination->getResultsCounter(); ?></span> <?php // echo JText::_('K_DISCUSSIONS'); ?>
			</div>
<?php if ($this->state->params->get('filter_limitstart_allow', 1)): ?>
			<div class="pagination">
				<?php echo JText::_('K_PAGE'); ?>: <?php echo $this->pagination->getPagesLinks(); ?>
			</div>
<?php endif; ?>	
		</div>
		<div class="clr"></div>

<?php echo $this->loadCommonTemplate('footer'); ?>
</div>