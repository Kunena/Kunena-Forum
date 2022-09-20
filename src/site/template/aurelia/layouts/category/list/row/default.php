<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/*
 * @var \Kunena\Forum\Libraries\Forum\Topic\Topic $topic
 */
$topic  = $this->category->getLastTopic();
$avatar = $this->config->avatarOnCategory ? $topic->getAuthor()->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb') : null;
?>

<tr>
	<td>
		<h3>
			<?php echo $this->getCategoryLink($this->category); ?>
			<small class="d-none d-sm-block">
				(<?php echo Text::sprintf('COM_KUNENA_X_TOPICS_MORE', $this->formatLargeNumber($this->category->getTopics())); ?>
				)
			</small>
		</h3>
	</td>

	<?php if (!$topic->exists())
		:
		?>
		<td>
			<?php echo Text::_('COM_KUNENA_NO_POSTS'); ?>
		</td>

	<?php else

		:
		?>

		<?php if ($avatar)
		:
		?>
		<td class="center">
		<span class="d-none d-sm-block">
			<?php echo $topic->getLastPostAuthor()->getLink($avatar); ?>
		</span>
		</td>
	<?php endif; ?>

		<td<?php if (!$avatar)
		{
			echo ' colspan="2"';
		} ?>>
			<div>
				<?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, 'hasTooltip', $this->category, true, true); ?>
			</div>
			<div>
				<?php echo $topic->getLastPostAuthor()->getLink(null, null, '', '', null, $this->category->id); ?>
			</div>
			<div>
				<?php echo $topic->getLastPostTime()->toSpan('config_postDateFormat', 'config_postDateFormatHover'); ?>
			</div>
		</td>
	<?php endif; ?>

	<?php if ($this->checkbox)
		:
		?>
		<td class="center">
			<label>
				<input class="kcheckallcategory" type="checkbox"
					   name="categories[<?php echo (int) $this->category->id ?>]" value="1"/>
			</label>
		</td>
	<?php endif; ?>

</tr>
