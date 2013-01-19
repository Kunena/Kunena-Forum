<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// FIXME: add pagination
?>

<div class="well well-small">
  <h2 class="page-header"><span> <?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>
    <?php
				if (!empty($this->actions['add']))
					echo '<div class="btn-group pull-right"><a class="btn dropdown-toggle" data-toggle="dropdown" href=""> <i class="icon-cog"></i> <span class="caret"></span> </a>
								<ul class="dropdown-menu actions" style="min-width:0 !important;">
									<li> <a href="index.php?option=com_kunena&view=announcement&layout=create" ><i class="hasTip icon-plus tip" title="Add"></i> Add</a> </li>
									<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-delete tip" title="Delete"></i> Delete</a> </li>
									<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-edit tip" title="Edit"></i> Edit</a> </li>
									<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-ok tip" title="Edit"></i> Publish</a> </li>
									<li> <a href="index.php?option=com_kunena&view=category&layout=manage" ><i class="hasTip icon-remove tip" title="Edit"></i> Unpublished</a> </li>
									</ul>
			</div>';
				?>
    </span> </h2>
  <div class="row-fluid column-row">
    <div class="span12 column-item">
      <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement') ?>" method="post" id="adminForm" name="adminForm">
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_( 'form.token' ); ?>
        <table class="kannouncement">
          <tbody id="kannouncement_body">
            <tr class="ksth">
              <?php if ($this->actions): ?>
              <th class="kcol-annid">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count ( $this->announcements ); ?>);" />
              </th>
              <?php endif; ?>
              <th class="kcol-annid"><?php echo JText::_('COM_KUNENA_ANN_ID'); ?></th>
              <th class="kcol-anndate"><?php echo JText::_('COM_KUNENA_ANN_DATE'); ?></th>
              <th class="kcol-anntitle"><?php echo JText::_('COM_KUNENA_ANN_TITLE'); ?></th>
              <?php if ($this->actions): ?>
              <th class="kcol-annpublish"><?php echo JText::_('COM_KUNENA_ANN_PUBLISH'); ?></th>
              <th class="kcol-annedit"><?php echo JText::_('COM_KUNENA_ANN_EDIT'); ?></th>
              <th class="kcol-anndelete"><?php echo JText::_('COM_KUNENA_ANN_DELETE'); ?></th>
              <?php endif; ?>
            </tr>
            <?php $this->displayItems() ?>
            <?php  if ( !empty($this->announcementActions) ) : ?>
            <!-- Bulk Actions -->
            <tr class="krow1">
              <td colspan="<?php echo empty($this->announcementActions) ? 5 : 7 ?>" class="kcol krowmoderation"> <?php echo JHtml::_('select.genericlist', $this->announcementActions, 'task', 'class="inputbox kchecktask" size="1"', 'value', 'text', 0, 'kchecktask'); ?>
                <input type="submit" name="kcheckgo" class="kbutton" value="<?php echo JText::_('COM_KUNENA_GO') ?>" />
              </td>
            </tr>
            <!-- /Bulk Actions -->
            <?php endif; ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
