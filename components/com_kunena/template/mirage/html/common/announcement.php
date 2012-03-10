<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO: add support for announcement RSS feed
?>

<div class="box-module">
	<div class="box-wrapper">
		<div class="announce-kbox kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header link-header2">
						<?php echo JHtml::_('kunenaforum.link', $this->annListUrl, $this->announcement->displayField('title'), JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST'), null, 'follow', array('rel'=>'kannounce-detailsbox')); ?>
					</h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="announce-details detailsbox innerspacer box-full box-hover box-border box-border_radius box-shadow" id="announce-detailsbox">
					<ul class="list-unstyled details-desc">
						<?php if ($this->showdate) : ?>
						<li class="kannounce-date" title="<?php echo $this->announcement->displayField('created', 'ago'); ?>">
							<?php echo $this->announcement->displayField('created') ?>
						</li>
						<?php endif ?>
						<li class="kannounce-desc">
							<p><?php echo $this->announcement->displayField('description') ?></p>
						</li>
						<?php if ($this->announcement->description) : ?>
						<li class="kannounce-desc kreadmore">
							<?php echo JHtml::_('kunenaforum.link', $this->announcement->getUri(), JText::_('COM_KUNENA_ANN_READMORE'), null, 'follow'); ?>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>
