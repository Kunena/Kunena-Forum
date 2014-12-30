<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
?>
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>
<div class="klist-markallcatsread kcontainer">
	<div class="ksectionbody">
		<div class="fltlft">
			<div class="fltlft">
			<?php if (!empty($this->markAllReadURL)) : ?>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" name="markAllForumsRead" method="post">
				<input type="hidden" name="view" value="category" />
				<input type="hidden" name="task" value="markread" />
				<?php echo JHtml::_( 'form.token' ); ?>

				<input type="submit" class="kbutton ks" value="<?php echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ'); ?>" />
			</form>
			</div>
			<div class="kmessage-buttons-row">
			<?php endif; ?>
			<?php if (!empty($this->category_manage)) echo $this->category_manage; ?>
			</div>
		</div>
		<div class="fltrt">
			<?php $this->displayForumjump(); ?>
		</div>
	</div>
</div>

<?php
if (count ( $this->categories )) {
	$this->displayTemplateFile('category', 'list', 'embed');
} else {
	$this->displayInfoMessage ();
}
$this->displayWhoIsOnline();
$this->displayStatistics();
?>
