<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule announcement-default">
	<div class="kbox-wrapper kbox-full">
		<div class="announcement-default-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header">
						<a title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox">
							<?php echo $this->displayField('title') ?>
						</a>
					</h2>
				</div>
			</div>
			<?php echo $this->displayActions() ?>
			<div class="detailsbox-wrapper innerspacer">
				<div class="announce-detailsbox detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled details-desc">
						<?php if ($this->showdate) : ?>
						<li class="kannounce-date" title="<?php echo $this->displayField('created', 'ago'); ?>">
							<?php echo $this->displayField('created') ?>
						</li>
						<?php endif ?>
						<li class="kannounce-desc"><?php echo $this->displayField('description') ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>
