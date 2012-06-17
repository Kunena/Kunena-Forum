<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-config"><?php echo JText::_('Diagnostics'); ?></div>
		<table class="adminform">
			<?php foreach (KunenaForumDiagnostics::getList() as $item) : ?>
			<tr>
				<td><?php echo $item ?></td>
				<td><?php echo KunenaForumDiagnostics::count($item) ?></td>
			</tr>
			<?php endforeach ?>
		</table>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
