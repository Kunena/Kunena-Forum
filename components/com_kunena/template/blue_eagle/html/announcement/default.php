<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kannouncement">
	<div class="kheader">
		<h2>
			<span><?php echo KunenaHtmlParser::parseText($this->announcement->title); ?></span>
		</h2>
	</div>
	<div class="kcontainer" id="kannouncement">
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
		<div class="kbody">
			<div class="kanndesc">
				<?php if ($this->showdate) : ?>
				<div class="anncreated" title="<?php echo $this->announcement->getCreationDate()->toKunena('ago'); ?>">
					<?php echo $this->announcement->getCreationDate()->toKunena('date_today'); ?>
				</div>
				<?php endif; ?>
				<div class="anndesc"><?php echo KunenaHtmlParser::parseBBCode(!empty($this->announcement->description) ? $this->announcement->description : $this->announcement->sdescription) ?></div>
			</div>
		</div>
	</div>
</div>
