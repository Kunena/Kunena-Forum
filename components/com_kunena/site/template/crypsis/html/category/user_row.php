<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

static $k=0;
$tabclass = array ("row1", "row2" );
?>

<tr class="k<?php echo $tabclass [$k^=1] ?>">
  <td>
    <div> <?php echo $this->getCategoryLink($this->category, null, null, 'ktopic-title km') ?> </div>
  </td>
  <td> 
    <!-- Views --> 
    <span><?php echo $this->formatLargeNumber ( ( int ) $this->category->numTopics );?></span> <span> <?php echo JText::_('COM_KUNENA_TOPICS'); ?> </span> 
    <!-- /Views --> 
  </td>
  <td> 
    <!-- Posts --> 
    <span><?php echo $this->formatLargeNumber ( ( int ) $this->category->numPosts ); ?></span> <span> <?php echo JText::_('COM_KUNENA_MY_POSTS'); ?> </span> 
    <!-- /Posts --> 
  </td>
  <?php
	$last = $this->category->getLastTopic();
	if ($last->exists()) { ?>
  <td>
    <?php if ($this->config->avataroncat > 0) : ?>
    <!-- Avatar -->
    <?php
			$profile = KunenaFactory::getUser((int)$last->last_post_userid);
			$useravatar = $profile->getAvatarImage('klist-avatar', 'list');
			if ($useravatar) : ?>
    <span> <?php echo $last->getLastPostAuthor()->getLink( $useravatar ); ?></span>
    <?php endif; ?>
    <!-- /Avatar -->
    <?php endif; ?>
    <div> <?php echo JText::_('COM_KUNENA_GEN_LAST_POST') . ': '. $this->getTopicLink($last, 'last', KunenaHtmlParser::parseText($last->subject, 30)) ?> </div>
    <div>
      <?php
				echo JText::_('COM_KUNENA_BY') . ' ';
				echo $last->getLastPostAuthor()->getLink();
				echo '<br /><span class="nowrap" title="' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat_hover') . '">' . KunenaDate::getInstance($last->last_post_time)->toKunena('config_post_dateformat') . '</span>';
				?>
    </div>
  </td>
  <?php } else { ?>
  <td><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></td>
  <?php } ?>
  <td>
    <input class ="kcheck" type="checkbox" name="categories[<?php echo $this->category->id?>]" value="1" />
  </td>
</tr>
