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

<!-- Bottom module -->
	<?php if ($this->document->countModules('fb_bottom')) : ?>
	<div class="bof-bottom-modul">
		<?php /* mosLoadModules('fb_bottom', -2); */ ?>
	</div>
	<?php endif;?>

<!-- credits -->
	<div class="credits">
		<?php echo JText::_('KUNENA_KUNENA_POWEREDBY');?>
		<a href="http://www.bestofjoomla.com" target="_blank">Kunena</a>

		<?php /* if ($this->config['enableRSS']) : */ ?>
		<a href="<?php echo JRoute::_('index.php?option=com_kunena&format=feed'); ?>" target="_blank">
			<img class="rsslink" src="<?php /* echo JB_URLEMOTIONSPATH; */ ?>rss.gif" border="0" alt="<?php echo JText::_('KUNENA_LISTCAT_RSS'); ?>" title="<?php echo JText::_('KUNENA_LISTCAT_RSS');?>" /></a>
		<?php /* endif; */ ?>
	</div>

	<div class="fb_footer">&nbsp;</div>
</div>
<!-- /Kunena -->
