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
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div id="announce" class="block">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header">
						<a href="" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox"><?php echo KunenaHtmlParser::parseText($this->announcement->title) ?></a>
					</h2>
					<?php if ($this->actions) : ?>
					<div class="kactions">
						<?php
						foreach ($this->actions as $name => $url) {
							$links[] = JHtml::_('kunenaforum.link', $url, JText::_("COM_KUNENA_ANN_{$name}"), JText::_("COM_KUNENA_ANN_{$name}"));
						}
						echo implode(' | ', $links);
						?>
					</div>
					<?php endif ?>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="detailsbox innerspacer box-full box-hover box-border box-border_radius box-shadow" id="announce-detailsbox">
					<ul class="list-unstyled details-desc">
						<?php if ($this->showdate) : ?>
						<li class="kannounce-date"><?php echo $this->announcement->getCreationDate()->toKunena('date_today') ?></li>
						<?php endif ?>
						<li class="kannounce-desc"><?php echo !empty($this->announcement->description) ? KunenaHtmlParser::parseBBCode($this->announcement->description) : KunenaHtmlParser::parseBBCode($this->announcement->sdescription) ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>