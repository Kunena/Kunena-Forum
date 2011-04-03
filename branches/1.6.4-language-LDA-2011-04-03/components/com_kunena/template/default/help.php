<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$kunena_config = KunenaFactory::getConfig ();
$document = JFactory::getDocument ();
$document->setTitle ( JText::_('COM_KUNENA_GEN_HELP') . ' - ' . $kunena_config->board_title );
$introtext = CKunenaTools::getRulesHelpDatas($kunena_config->help_cid);
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_FORUM_HELP'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<div class="khelprulescontent">
				<?php echo $introtext; ?>
			</div>
		</div>
	</div>
</div>
<!-- Begin: Forum Jump -->
<?php if ($kunena_config->enableforumjump) : ?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_GO_TO_CATEGORY'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="khelprulesjump">
			<?php CKunenaTools::loadTemplate('/forumjump.php') ?>
		</div>
	</div>
</div>
<?php endif; ?>
<!-- Finish: Forum Jump -->