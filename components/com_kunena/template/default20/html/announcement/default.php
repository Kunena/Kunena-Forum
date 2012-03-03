<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div id="kannounce">
			<h2 class="kheader">
				<a href="" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>" rel="kannounce-detailsbox">
					<?php echo KunenaHtmlParser::parseText($this->announcement->title) ?>
				</a>
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
			<?php endif; ?>
			<div class="kdetailsbox" id="kannounce-detailsbox">
				<ul class="kheader-desc">
					<?php if ($this->showdate) : ?>
					<li class="kannounce-date" title="<?php echo $this->announcement->getCreationDate()->toKunena('ago') ?>">
						<?php echo $this->announcement->getCreationDate()->toKunena('date_today') ?>
					</li>
					<?php endif; ?>
					<li class="kannounce-desc">
						<p><?php echo !empty($this->announcement->description) ? KunenaHtmlParser::parseBBCode($this->announcement->description) : KunenaHtmlParser::parseBBCode($this->announcement->sdescription) ?></p>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>