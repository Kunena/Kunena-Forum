<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Trash
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTrash $this */
?>

<div id="kunena" class="admin override">
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div id="j-sidebar-container" class="span2">
          <div id="sidebar">
            <div class="sidebar-nav">
              <?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?>
            </div>
          </div>
        </div>
        <div id="j-main-container" class="span10">
          <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
            <input type="hidden" name="view" value="trash" />
            <input type="hidden" name="task" value="purge" />
            <input type="hidden" name="boxchecked" value="1" />
            <input type="hidden" name="md5" value="<?php echo $this->md5Calculated ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
            <table class="adminheading">
            </table>
            <table class="table table-striped">
              <tr>
                <td><strong><?php echo JText::_('COM_KUNENA_NUMBER_ITEMS'); ?>:</strong> <br />
                  <font color="#000066"><strong><?php echo count( $this->purgeitems ); ?></strong></font> <br />
                  <br />
                </td>
                <td  valign="top" width="25%"> <strong><?php echo JText::_('COM_KUNENA_ITEMS_BEING_DELETED'); ?>:</strong> <br />
                  <?php echo "<ol>";
							foreach ( $this->purgeitems as $item ) {
								echo "<li>". $this->escape($item->subject) ."</li>";
							}
							echo "</ol>";
						?> </td>
                <td valign="top"><span style="color:red;"><strong><?php echo JText::_('COM_KUNENA_PERM_DELETE_ITEMS'); ?></strong></span> </td>
              </tr>
            </table>
          </form>
        </div>
        <div class="pull-right small"> <?php echo KunenaVersion::getLongVersionHTML (); ?> </div>
      </div>
    </div>
  </div>
</div>
