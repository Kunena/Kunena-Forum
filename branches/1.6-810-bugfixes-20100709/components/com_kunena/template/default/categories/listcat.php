<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
CKunenaTools::showModulePosition ( 'kunena_announcement' );
$this->displayPathway ();
?>
<!-- B: Cat list Top -->
<table class="klist-top">
	<tr>
		<td class="klist-markallcatsread"><?php
		if ($this->my->id != 0) {
			?>

		<form action="<?php
			echo KUNENA_LIVEURLREL;
			?>"
			name="markAllForumsRead" method="post"><input type="hidden"
			name="markaction" value="allread" /> <input type="submit"
			class="kbutton button ks"
			value="<?php
			echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ');
			?>" /></form>

		<?php
		}
		?></td>
		<td class="klist-categories"><?php
		$this->displayForumJump ();
		?>
		</td>
	</tr>
</table>
<!-- F: Cat list Top -->


<?php
if (count ( $this->categories [0] ) > 0) {
	$this->displayCategories ();
	$this->displayWhoIsOnline ();
	$this->displayStats ();
	?>

<?php
} else {
	?>

<div><?php
	echo JText::_('COM_KUNENA_LISTCAT_NO_CATS') . '<br />';
	echo JText::_('COM_KUNENA_LISTCAT_ADMIN') . '<br />';
	echo JText::_('COM_KUNENA_LISTCAT_PANEL') . '<br /><br />';
	echo JText::_('COM_KUNENA_LISTCAT_INFORM') . '<br /><br />';
	echo JText::_('COM_KUNENA_LISTCAT_DO') . ' <img src="' . KUNENA_URLEMOTIONSPATH . 'wink.png"  alt="" border="0" />';
	?>
</div>

<?php
}
