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
	<div class="box-wrapper box-color box-border box-border_radius box-shadow">
		<div id="announce" class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header link-header2">
						<a href="<?php echo $this->annListUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox"><?php echo $this->annTitle ?></a>
					</h2>
				</div>
				<div class="header fr">
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="announce-details detailsbox innerspacer box-full box-hover box-border box-border_radius box-shadow" id="announce-detailsbox">
					<ul class="list-unstyled details-desc">
						<li class="kannounce-date"><?php echo $this->annDate->toKunena('date_today') ?></li>
						<li class="kannounce-desc"><p><?php echo $this->annDescription ?></p></li>
						<?php if ($this->annUrl) : ?>
						<li class="kannounce-desc kreadmore"><?php echo JHtml::_('kunenaforum.link', $this->annUrl, JText::_('COM_KUNENA_ANN_READMORE'), JText::_('COM_KUNENA_ANN_READMORE'),'follow'); ?></li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>