<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>

<div class="well well-small"> <span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kwhoisonline"></a></span>
  <div class="row-fluid column-row">
    <h2 class="page-header"><span><?php echo $this->getStatsLink($this->config->board_title.' '.JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE'), ''); ?></span></h2>
    <div class="row-fluid column-row">
      <div class="span12 column-item">
        <div class="krow2">
          <div class="kwhoonline kwho-total ks">
            <?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?>
          </div>
          <div>
            <?php $onlinelist = array();
					foreach ($this->onlineList as $user) {
						$onlinelist[] = $user->getLink();
					}

					echo implode(', ', $onlinelist); ?>
            <?php if (!empty($this->hiddenList)) : ?>
            <br />
            <span class="khidden-ktitle ks"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span> <br />
            <?php $hiddenlist = array();
						foreach ($this->hiddenList as $user) {
							$hiddenlist[] = $user->getLink();
						}

						echo implode(', ', $hiddenlist); ?>
            <?php endif; ?>
          </div>
          <div class="kwholegend ks"> <span><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</span>&nbsp; <span class = "kwho-admin" title = "<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></span>,&nbsp; <span class = "kwho-globalmoderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></span>,&nbsp; <span class = "kwho-moderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></span>,&nbsp; <span class = "kwho-banned" title = "<?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?></span>,&nbsp; <span class = "kwho-user" title = "<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></span>,&nbsp; <span class = "kwho-guest" title = "<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></span> </div>
        </div>
      </div>
    </div>
  </div>
</div>
