<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined( '_JEXEC' ) or die();


class html_Kunena {
	// Begin: HEADER FUNC
	function showFbHeader() {
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
		?>
<!--[if IE]>
<style type="text/css">

table.kadmin-stat caption {
	display:block;
	font-size:12px !important;
	padding-top: 10px !important;
}

</style>
<![endif]-->

<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
			<?php
			} // Finish: HEADER FUNC

			// Begin: FOOTER FUNC
			function showFbFooter() {
?>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>

	<?php
	} // Finish: FOOTER FUNC

	function controlPanel() {
		$kunena_config = KunenaFactory::getConfig ();
		?>

	<div class="kadmin-functitle icon-cpanel"><?php echo JText::_('COM_KUNENA_CP'); ?></div>
	<?php
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/kunena.cpanel.php';

		if (file_exists ( $path )) {
			require $path;
		} else {
			echo '<br />mcap==: ' . JPATH_COMPONENT_ADMINISTRATOR . ' .... help!!';
		}
	}


} //end class