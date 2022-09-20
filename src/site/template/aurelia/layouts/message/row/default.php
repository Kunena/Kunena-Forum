<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

$this->addStyleSheet('rating.css');

$message         = $this->message;
$author          = $message->getAuthor();
$topic           = $message->getTopic();
$userTopic       = $topic->getUserTopic();
$category        = $message->getCategory();
$this->ktemplate = KunenaFactory::getTemplate();
$config          = KunenaFactory::getConfig();
$avatar          = $config->avatarOnCategory ? $topic->getLastPostAuthor()->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb') : null;
$cols            = empty($this->checkbox) ? 5 : 6;
$txt             = $topic->getActions();
$topicPages      = $topic->getPagination(null, KunenaConfig::getInstance()->messagesPerPage, 3);

?>
<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?>">
	<?php
	if ($topic->unread)
		:
		?>
        <th scope="row" class="d-none d-md-table-cell topic-item-unread">
			<?php echo $this->getTopicLink($topic, 'unread', $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, true); ?>
        </th>
	<?php else

		:
		?>
        <th scope="row" class="d-none d-md-table-cell">
			<?php echo $this->getTopicLink($topic, $this->message, $topic->getIcon($topic->getCategory()->iconset), '', null, $category, true, false); ?>
        </th>
	<?php endif; ?>
    <td>
        <div>
			<?php
			if ($this->ktemplate->params->get('labels') != 0)
			{
				echo $this->subLayout('Widget/Label')->set('topic', $topic)->setLayout('default');
			}

			if ($topic->unread)
			{
				echo $this->getTopicLink(
					$topic,
					'unread',
					$this->escape($topic->subject) .
					'<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' ' . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>',
					null,
					KunenaTemplate::getInstance()->tooltips() . ' topictitle',
					$category,
					true,
					true
				);
			}
			else
			{
				echo $this->getTopicLink($topic, $this->message, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false);
			}

			echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $topic)->setLayout('default'); ?>
        </div>
        <div class="float-end">
			<?php if ($userTopic->favorite)
				:
				?>
                <span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
							data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
			<?php endif; ?>

			<?php if ($userTopic->posts)
				:
				?>
                <span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
							data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_MYPOSTS'); ?>"><?php echo KunenaIcons::flag(); ?></span>
			<?php endif; ?>

			<?php if ($topic->attachments)
				:
				?>
                <span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
							data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
			<?php endif; ?>

			<?php if ($topic->poll_id && $category->allowPolls)
				:
				?>
                <span <?php echo KunenaTemplate::getInstance()->tooltips(true); ?>
							data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
			<?php endif; ?>
        </div>

        <div class="started">
			<?php echo Text::_('COM_KUNENA_TOPIC_STARTED_ON') ?>
			<?php if ($config->postDateFormat != 'none')
				:
				?>
				<?php echo $topic->getFirstPostTime()->toKunena('config_postDateFormat'); ?>,
			<?php endif; ?>
			<?php echo Text::_('COM_KUNENA_BY') ?>
			<?php echo $topic->getAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getFirstPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
            <div class="float-end">
				<?php /** TODO: New Feature - LABELS
				 * <span class="badge bg-info">
				 * <?php echo Text::_('COM_KUNENA_TOPIC_ROW_TABLE_LABEL_QUESTION'); ?>
				 * </span>    */ ?>
				<?php if ($topic->locked != 0)
					:
					?>
                    <span class="badge bg-warning">
							<span data-bs-toggle="tooltip"
                                  title="<?php echo Text::_('COM_KUNENA_LOCKED'); ?>"><?php echo KunenaIcons::lock(); ?></span>
						</span>
				<?php endif; ?>
            </div>
        </div>

        <div id="klastpostphone" class="visible-xs">
			<?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, null, $category, false, true); ?>
			<?php
			if ($config->postDateFormat != 'none')
				:
				?>
				<?php echo $topic->getLastPostTime()->toKunena('config_postDateFormat'); ?> <br>
			<?php endif; ?>
			<?php echo Text::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink(null, null, '', '', null, $category->id); ?>
        </div>

        <div class="float-start">
			<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
        </div>
    </td>

    <td class="d-none d-md-table-cell">
        <div class="replies"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>:<span
                    class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span></div>
        <div class="views"><?php echo Text::_('COM_KUNENA_GEN_HITS'); ?>:<span
                    class="viewsnum"><?php echo $this->formatLargeNumber($topic->hits); ?></span></div>
    </td>

    <td class="d-none d-md-table-cell">
        <div class="row">
			<?php if ($config->avatarOnCategory)
			:
			?>
            <div class="col-xs-6 col-md-3">
				<?php echo $author->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id, $config->avatarEdit); ?>
            </div>
            <div class="col-xs-6 col-md-9">
				<?php else

				:
				?>
                <div class="col-md-12">
					<?php endif; ?>
                    <span class="lastpostlink"><?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, KunenaTemplate::getInstance()->tooltips(), $category, false, true); ?>
						<?php echo ' ' . Text::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getLastPostAuthor()->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), $category->id); ?>
						</span>
                    <br>
                    <span class="datepost"><?php echo $topic->getLastPostTime()->toKunena('config_postDateFormat'); ?></span>
                </div>
            </div>
        </div>
    </td>

	<?php if (!empty($this->checkbox))
		:
		?>
        <td class="center">
            <label>
                <input class="kcheck" type="checkbox" name="posts[<?php echo $message->id; ?>]"
                       value="1"/>
            </label>
        </td>
	<?php endif; ?>

	<?php
	if (!empty($this->position))
	{
		echo $this->subLayout('Widget/Module')
			->set('position', $this->position)
			->set('cols', $cols)
			->setLayout('table_row');
	}
	?>
</tr>
