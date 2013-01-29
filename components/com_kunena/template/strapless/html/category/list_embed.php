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

$tabclass = array ("row1", "row2" );
$mmm=0;
foreach ( $this->sections as $section ) :
	$htmlClassBlockTable = !empty ( $section->class_sfx ) ? ' kblocktable' . $this->escape($section->class_sfx) : '';
	$htmlClassTitleCover = !empty ( $section->class_sfx ) ? ' ktitle-cover' . $this->escape($section->class_sfx) : '';
?>

<div class="well well-small kfrontend">
  <?php if (count($this->sections) > 0) : ?>
  <span class="ktoggler"></span>
  <?php endif; ?>
  <?php if ($this->me->isAdmin()) : ?>
  <div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
    <ul class="dropdown-menu actions">
      <li> <a href="index.php?option=com_kunena&amp;view=category&amp;layout=create" ><i class="hasTip icon-new tip"></i> New Category</a> </li>
      <li> <a href="index.php?option=com_kunena&amp;view=category&amp;layout=manage" ><i class="hasTip icon-edit tip"></i> Edit Section</a> </li>
    </ul>
  </div>
  <?php endif; ?>
  <h2><span><?php echo $this->GetCategoryLink ( $section, $this->escape($section->name) ); ?><a href="" rel="follow"><span class="kicon krss" title="get the latest posts directly to your desktop"></span></a></span></h2>
  <?php if (!empty($section->description)) : ?>
  <div class="ktitle-desc km hidden-phone"> <?php echo KunenaHtmlParser::parseBBCode ( $section->description ); ?> </div>
  <?php endif; ?>
  <div class="clearfix"></div>
  <div class="row-fluid column-row">
    <div class="span12 column-item">
      <table class="table table-striped table-hover">
        <?php if (empty ( $this->categories [$section->id] )) { echo JText::_('COM_KUNENA_GEN_NOFORUMS');
		} else {
		$k = 0;
		foreach ( $this->categories [$section->id] as $category ) {
	?>
        <tr class="k<?php echo $tabclass [$k ^= 1], isset ( $category->class_sfx ) ? ' k' . $this->escape($tabclass [$k]) . $this->escape($category->class_sfx) : '' ?> kprofile"
			id="kcat<?php echo intval($category->id) ?>">
          <td class="span1 hidden-phone"> <?php echo $this->getCategoryLink($category, $this->getCategoryIcon($category), '') ?> </td>
          <td class="span6">
            <div class="kthead-title kl">
              <?php
				// Show new posts, locked, review
				echo $this->getCategoryLink($category);
				?>
              <span class="kinfo1">
              <?php
				if ($category->getNewCount()) {
					echo '<sup class="knewchar">(' . $category->getNewCount() . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>";
				}
				if ($category->locked) {
					echo $this->getIcon ( 'kforumlocked', JText::_('COM_KUNENA_LOCKED_CATEGORY') );
				}
				if ($category->review) {
					echo $this->getIcon ( 'kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED') );
				}
				?>
              <a href="" rel="follow"><span class="kicon kmark-read" title="mark category read"></span></a> <a href="" rel="follow"><span class="kicon krss-small" title="get the latest posts directly to your desktop"></span></a> </span> </div>
            <?php if (!empty($category->description)) : ?>
            <div class="kthead-desc km hidden-phone ktest"><?php echo KunenaHtmlParser::parseBBCode ($category->description) ?> </div>
            <?php endif; ?>
            <?php
			// Display subcategories
			if (! empty ( $this->categories [$category->id] )) :
		?>
            <div class="kthead-child">
              <div class="kcc-table">
                <?php foreach ( $this->categories [$category->id] as $childforum ) : ?>
                <div class="kcc-subcat km">
                  <?php
				echo $this->getCategoryIcon($childforum, true);
				echo $this->getCategoryLink($childforum);
				echo '<span class="kchildcount ks">(' . $childforum->getTopics() . "/" . $childforum->getReplies() . ')</span>';
			?>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
            <?php if (! empty ( $category->moderators )) : ?>
            <div class="kthead-moderators ks">
              <?php
				// get the Moderator list for display
				$modslist = array();
				foreach ( $category->moderators as $moderator ) {
					$modslist[] = KunenaFactory::getUser($moderator)->getLink();
				}
				echo JText::_('COM_KUNENA_MODERATORS') . ': ' . implode(', ', $modslist);
		?>
            </div>
            <?php endif; ?>
            <?php if (! empty ( $this->pending [$category->id] )) : ?>
            <div class="ks kalert"> <?php echo JHtml::_('kunenaforum.link', 'index.php?option=com_kunena&view=topics&layout=posts&mode=unapproved&userid=0&catid='.intval($category->id), intval($this->pending [$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'), '', '', 'nofollow'); ?> </div>
            <?php endif; ?>
          </td>
          <?php $last = $category->getLastTopic();
			if ($last->exists()) { ?>
          <td class="span2 hidden-phone"><span class="kcat-topics-number"><?php echo $this->formatLargeNumber ( $category->getTopics() ) ?></span> <span class="kcat-topics"><?php echo JText::_('COM_KUNENA_TOPICS');?></span> <br/>
            <span class="kcat-replies-number"><?php echo $this->formatLargeNumber ( $category->getReplies() ) ?></span> <span class="kcat-replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES');?> </span> </td>
          <?php } else { ?>
          <td class="span2 hidden-phone">
            <div class="kinfo"> <span class="kcat-topics-number"><?php echo $this->formatLargeNumber ( $category->getTopics() ) ?></span> <span class="kcat-topics"><?php echo JText::_('COM_KUNENA_TOPICS');?></span> <br/>
              <span class="kcat-replies-number"><?php echo $this->formatLargeNumber ( $category->getReplies() ) ?></span> <span class="kcat-replies"><?php echo JText::_('COM_KUNENA_GEN_REPLIES');?> </span></div>
          </td>
          <td class="span1 hidden-phone"></td>
          <?php } ?>
          <?php $last = $category->getLastTopic();
			if ($last->exists()) { ?>
          <td class="span1 hidden-phone">
            <?php if ($this->config->avataroncat > 0) : ?>
            <?php
				$profile = KunenaFactory::getUser((int)$last->last_post_userid);
				$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
				if ($useravatar) : ?>
            <span class="klatest-avatar hidden-phone"> <?php echo $last->getLastPostAuthor()->getLink( $useravatar ); ?></span>
            <?php endif; ?>
            <?php endif; ?>
          </td>
          <td class="span2">
            <div class="klatest-subject ks"> <?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. $this->getLastPostLink($category) ?> </div>
            <div class="klatest-subject-by ks">
              <?php
					echo JText::_('COM_KUNENA_BY') . ' ';
					echo $last->getLastPostAuthor()->getLink();
					echo '<br /><span class="nowrap" title="' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat_hover') . '">' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat') . '</span>';
					?>
            </div>
          </td>
          <?php } else { ?>
          <td class="span2"><span class="kinfo"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></span></td>
          <?php } ?>
        </tr>
        <?php } } ?>
      </table>
    </div>
  </div>
</div>
<!-- Begin: Category Module Position -->
<?php $this->displayModulePosition('kunena_section_' . ++$mmm) ?>
<!-- Finish: Category Module Position -->
<?php endforeach; ?>
