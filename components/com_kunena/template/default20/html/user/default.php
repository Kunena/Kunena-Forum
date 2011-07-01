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

JHTML::_('behavior.tooltip');
?>
		<div class="kuserprofile">
			<?php if (!empty($this->editLink)) echo $this->editLink ?>
			<h2 class="kheader"><a href="#" rel="kmod-detailsbox"><?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></a></h2>
			<div class="kdetailsbox kmod-userbox" id="kmod-detailsbox">
				<?php $this->displaySummary(); ?>
				<div class="clrline"></div>
				<?php $this->displayTab(); ?>
				<div class="clr"></div>
			</div>
		</div>
<script type="text/javascript">
// <![CDATA[
window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>
</script>