<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
$this->displayBreadcrumb (); 

?>
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>

<div class="pull-right hidden-phone" style="margin:-42px 4px 0 0;" >
  <?php $this->displayForumjump(); ?>
</div>
<div class="row-fluid column-row">
  <div class="span12 column-item" style="margin-bottom:10px;">
    <?php if (!empty($this->markAllReadURL)) : ?>
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="markAllForumsRead" id="markAllForumsRead">
      <input type="hidden" name="view" value="category" />
      <input type="hidden" name="task" value="markread" />
      <?php echo JHtml::_( 'form.token' ); ?>
      <button type="submit" class="btn"><?php echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ'); ?></button>
    </form>
    <?php endif; ?>
    <?php if (!empty($this->category_manage)) echo $this->category_manage; ?>
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
