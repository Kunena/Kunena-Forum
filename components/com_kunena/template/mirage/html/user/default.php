<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHTML::_('behavior.tooltip');
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="userprofile block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header"><a href="#" rel="kmod-detailsbox"><?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></a></h2>
				</div>
				<div class="header fr">
					<?php if (!empty($this->editLink)) : ?>
						<a class="link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit') ?>">Edit</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="kdetailsbox kmod-userbox" id="kmod-detailsbox">
					<?php $this->displaySummary(); ?>

					<?php //$this->displayTab(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php $this->displayTab(); ?>
<script type="text/javascript">
// <![CDATA[
window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>
</script>