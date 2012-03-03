<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<!-- ANNOUNCEMENTS BOX -->
<div class="kblock kannouncement">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kannouncement"></a></span>
		<h2><?php echo JHtml::_('kunenaforum.link', $this->annUrl, $this->annTitle, JText::_('COM_KUNENA_ANN_READMORE'), null, 'follow'); ?></h2>
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
				<div class="anncreated"><?php echo $this->annDate->toKunena('date_today') ?></div>
				<?php endif; ?>
				<div class="anndesc">
					<?php echo $this->annDescription; ?>
					<?php if (!empty($this->announcement->description)) : ?>
					...<br /><?php echo JHtml::_('kunenaforum.link', $this->annUrl, JText::_('COM_KUNENA_ANN_READMORE'), JText::_('COM_KUNENA_ANN_READMORE'),'follow'); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->getModulePosition ( 'kunena_announcement' ) ?>
<!-- / ANNOUNCEMENTS BOX -->