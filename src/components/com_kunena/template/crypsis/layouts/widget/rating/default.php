<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Rating
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

if ($this->config->ratingenabled && $this->category->allow_ratings) :
	if ($this->topic->rating) : ?>
		<div id="krating-top">
			<ul class="c-rating">
				<li class="c-rating__item is-active" data-index="0"></li>
				<li class="c-rating__item <?php echo $this->topic->rating >= 2 ? 'is-active': ''; ?>" data-index="1"></li>
				<li class="c-rating__item <?php echo $this->topic->rating >= 3 ? 'is-active': ''; ?>" data-index="2"></li>
				<li class="c-rating__item <?php echo $this->topic->rating >= 4 ? 'is-active': ''; ?>" data-index="3"></li>
				<li class="c-rating__item <?php echo $this->topic->rating >= 5 ? 'is-active': ''; ?>" data-index="4"></li>
			</ul>
		</div>
	<?php endif;
endif; ?>
