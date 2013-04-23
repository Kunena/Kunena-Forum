<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO: add support for announcement RSS feed
?>

<div class="kmodule common-announcement">
	<div class="kbox-wrapper kbox-full">
		<div class="common-announcement-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header link-header2">
						<?php echo JHtml::_('kunenaforum.link', $this->annListUrl, $this->announcement->displayField('title'), JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST'), null, 'follow', array('rel'=>'kannounce-detailsbox')); ?>
					</h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="common-announce-detailbox detailsbox innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled details-desc">
						<?php if ($this->showdate) : ?>
						<li class="kannounce-date" title="<?php echo $this->announcement->displayField('created', 'ago'); ?>">
							<?php echo $this->announcement->displayField('created') ?>
						</li>
						<?php endif ?>
						<li class="kannounce-desc">
							<?php echo $this->announcement->displayField('sdescription') ?>
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

<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>
