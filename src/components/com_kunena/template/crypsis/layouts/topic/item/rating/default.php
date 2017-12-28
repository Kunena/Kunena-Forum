<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

defined('_JEXEC') or die ();
$this->addStyleSheet('assets/css/rating.css');

?>

<?php if ($this->category->allow_ratings && $this->config->ratingenabled): ?>
	<input id="topic_id" type="hidden" value="<?php echo $this->topicid ?>"/>
	<input type="hidden" id="krating_url" name="krating_url"
	       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=getrate&format=raw'); ?>"/>
	<input type="hidden" id="krating_submit_url" name="url"
	       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=rate&topic_id=' . $this->topicid . '&format=raw'); ?>"/>
	<div id="krating"></div>

	<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<meta itemprop="worstRating" content="1">
		<meta itemprop="bestRating" content="5">
		<meta itemprop="reviewCount" content="<?php echo KunenaForumTopicRate::getTotalUsers($this->topicid);?>">
		<meta itemprop="ratingValue" content="<?php echo KunenaForumTopicRateHelper::getSelected($this->topicid); ?>">
	</div>
<?php endif; ?>
