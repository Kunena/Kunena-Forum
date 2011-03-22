<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<?php echo $this->getModulePosition( 'kunena_bottom' ); ?>
		<div id="kcredit">
			<p><a href="http://www.kunena.org" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_FOOTER_POWERED_BY_TITLE') ?>"><?php echo JText::_('COM_KUNENA_VIEW_COMMON_FOOTER_POWERED_BY') ?></a></p>
			<p><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $this->getTime()) ?></p>
		</div>
