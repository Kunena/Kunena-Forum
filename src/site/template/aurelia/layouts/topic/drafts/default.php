<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$token = Session::getFormToken() . '=1';

// Topics which the drafts belong to, as far as they still exist and the user may read them.
$topicIds = array_filter(array_map(static fn ($draft) => (int) $draft->topic_id, (array) $this->drafts));
$topics   = $topicIds ? KunenaTopicHelper::getTopics(array_values(array_unique($topicIds))) : [];
?>

<h1><?php echo Text::_('COM_KUNENA_DRAFTS'); ?></h1>

<?php if (!$this->drafts) : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_DRAFTS_NONE'); ?></div>
<?php else : ?>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></th>
            <th><?php echo Text::_('COM_KUNENA_CATEGORY'); ?></th>
            <th><?php echo Text::_('COM_KUNENA_DRAFT_LAST_SAVED'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->drafts as $draft) :
            $category = KunenaCategoryHelper::get((int) $draft->catid);
            $title    = trim((string) $draft->subject);
            $topic    = $topics[(int) $draft->topic_id] ?? null;

            if ($title === '') {
                // A reply has no subject of its own: show the start of the text.
                $title = mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags((string) $draft->message))), 0, 80);
            }
            ?>
            <tr>
                <td>
                    <?php $titleHtml = htmlspecialchars($title !== '' ? $title : Text::_('COM_KUNENA_DRAFT_NO_SUBJECT'), ENT_QUOTES, 'UTF-8'); ?>
                    <?php if ($topic && $topic->exists()) : ?>
                        <a href="<?php echo $topic->getUrl(); ?>"><?php echo $titleHtml; ?></a>
                    <?php else : ?>
                        <?php echo $titleHtml; ?>
                    <?php endif; ?>
                    <?php if ($draft->message_id) : ?>
                        <span class="badge bg-secondary"><?php echo Text::_('COM_KUNENA_DRAFT_EDIT'); ?></span>
                    <?php elseif ($draft->parent_id) : ?>
                        <span class="badge bg-secondary"><?php echo Text::_('COM_KUNENA_DRAFT_REPLY'); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $category->exists() ? $category->displayField('name') : ''; ?></td>
                <td><?php echo HTMLHelper::_('date', $draft->modified_time, 'DATE_FORMAT_LC5'); ?></td>
                <td class="text-end">
                    <a class="btn btn-outline-primary btn-sm"
                       href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loaddraft&draftid=' . (int) $draft->id . '&' . $token); ?>">
                        <?php echo KunenaIcons::edit(); ?> <?php echo Text::_('COM_KUNENA_DRAFT_CONTINUE'); ?>
                    </a>
                    <a class="btn btn-outline-danger btn-sm"
                       href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=deletedraft&draftid=' . (int) $draft->id . '&' . $token); ?>"
                       onclick="return confirm('<?php echo Text::_('COM_KUNENA_DRAFT_DELETE_CONFIRM', true); ?>');">
                        <?php echo KunenaIcons::delete(); ?> <?php echo Text::_('COM_KUNENA_DELETE'); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
