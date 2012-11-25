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

JHtml::_('behavior.tooltip');
?>
<div class="kmodule user-edit">
	<div class="kbox-wrapper kbox-full">
		<div class="user-edit-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-child kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header"><a href="#" rel="kmod-detailsbox"><?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></a></h2>
				</div>
				<div class="header fr">
					<?php if (!empty($this->editLink)) : ?>
						<a class="link" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user') ?>">Back</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="kdetailsbox kmod-userbox" id="kmod-detailsbox">
				<?php $this->displaySummary(); ?>
				<div class="clrline"></div>
				<?php //$this->displayTab(); ?>
			</div>
		</div>
	</div>
</div>

<?php $this->displayTab(); ?>
