<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-border_radius-child box-shadow">
		<div id="announce" class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a href="" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox">
							<?php echo $this->displayField('title') ?>
						</a>
					</h2>
				</div>
			</div>
			<?php echo $this->displayActions() ?>
			<div class="detailsbox-wrapper innerspacer">
				<div class="announce-detailsbox detailsbox box-full box-hover box-border box-border_radius box-shadow">
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
<div class="spacer"></div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>
