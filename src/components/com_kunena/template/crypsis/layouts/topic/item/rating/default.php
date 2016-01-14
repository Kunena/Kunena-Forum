<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Blue_Eagle
 * @subpackage  Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/

defined('_JEXEC') or die ();
?>
<div id="krating_block" class="krating">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" id="krate-form">
		<input class="kratingstar" type="radio" id="star5" name="krating" value="5" <?php if ($this->rating == 5)
		{
			echo ' checked="checked" ';
		} ?>/><label for="star5" title="COM_KUNENA_TOPIC_RATING_ROCKS">5 stars</label>
		<input class="kratingstar" type="radio" id="star4" name="krating" value="4" <?php if ($this->rating == 4)
		{
			echo ' checked="checked" ';
		} ?>/><label for="star4" title="COM_KUNENA_TOPIC_RATING_PRETTYGOOD">4 stars</label>
		<input class="kratingstar" type="radio" id="star3" name="krating" value="3" <?php if ($this->rating == 3)
		{
			echo ' checked="checked" ';
		} ?>/><label for="star3" title="COM_KUNENA_TOPIC_RATING_MDEIUM">3 stars</label>
		<input class="kratingstar" type="radio" id="star2" name="krating" value="2" <?php if ($this->rating == 2)
		{
			echo ' checked="checked" ';
		} ?>/><label for="star2" title="COM_KUNENA_TOPIC_RATING_KINDABAD">2 stars</label>
		<input class="kratingstar" type="radio" id="star1" name="krating" value="1" <?php if ($this->rating == 1)
		{
			echo ' checked="checked" ';
		} ?>/><label for="star1" title="COM_KUNENA_TOPIC_RATING_SUCKS">1 star</label>
		<input type="hidden" id="krating_submit_url" name="url" value="<?php echo 'index.php?option=com_kunena&view=topic&layout=rate&topicid=' . $this->id . '&format=raw'; ?>" />
	</form>
</div>
