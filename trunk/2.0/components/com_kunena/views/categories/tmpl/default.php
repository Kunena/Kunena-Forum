<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->common->display ('announcement');
$this->getModulePosition ( 'kunena_announcement' );
$this->common->display ('pathway');
?>

<div class="klist-markallcatsread kcontainer">
	<div class="ksectionbody">
		<div class="fltlft">
			<?php if (KunenaFactory::getUser()->userid) : ?>
			<form action="<?php echo 'index.php?option=com_kunena'; ?>" name="markAllForumsRead" method="post">
				<input type="hidden" name="markaction" value="allread" />
				<input type="submit" class="kbutton button ks" value="<?php echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ'); ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
			<?php endif; ?>
		</div>
		<div class="fltrt">
			<?php $this->displayForumjump(); ?>
		</div>
	</div>
</div>



<?php
if (count ( $this->categories )) {
	echo $this->loadTemplate('clean');
} else {
	$this->displayInfoMessage ();
}
$this->common->display('whosonline');
$this->common->display('stats');
?>