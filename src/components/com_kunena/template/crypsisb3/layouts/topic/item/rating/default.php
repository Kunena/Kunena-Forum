<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();
?>

<?php if ($this->category->allow_ratings && $this->config->ratingenabled)
	:
	?>
	<input id="topic_id" type="hidden" value="<?php echo $this->topicid ?>"/>
	<input type="hidden" id="krating_url" name="krating_url"
	       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=getrate&format=raw'); ?>"/>
	<input type="hidden" id="krating_submit_url" name="url"
	       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=rate&topic_id=' . $this->topicid . '&format=raw'); ?>"/>
	<div id="krating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
		<p class="unseen element-invisible">
			<meta itemprop="ratingCount"
			      content="<?php echo KunenaForumTopicRateHelper::getSelected($this->topicid); ?>"/>
			<meta itemprop="worstRating" content="1"/>
		</p>
	</div>
<?php endif;
