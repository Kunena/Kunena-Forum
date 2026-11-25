<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$data   = $this->tool;
$action = KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools');
// A button which removes subscriptions: of everybody to an item, or (with a user) of that user only.
$button = function (string $scope, int $id, int $userid = 0, string $label = 'COM_KUNENA_TOOLS_SUBS_UNSUBSCRIBE') use ($action): string {
    if (!$this->canAdmin) {
        return '';
    }

    return '<form action="' . $action . '" method="post">'
        . '<input type="hidden" name="task" value="tools.unsubscribeAll">'
        . '<input type="hidden" name="scope" value="' . $scope . '">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . ($userid > 0 ? '<input type="hidden" name="user_id" value="' . $userid . '">' : '')
        . HTMLHelper::_('form.token')
        . '<button type="submit" class="kd-btn kd-btn--sm kd-btn--ghost" data-kd-confirm>' . Text::_($label) . '</button>'
        . '</form>';
};
$user     = $data['user'];
$userSubs = $data['userSubs'];
$topicUrl = static fn (array $row) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['category_id'] . '&id=' . (int) $row['id']);
$lists = [
    'categories' => ['COM_KUNENA_TOOLS_SUBS_TOP_CATEGORIES', $data['topCategories'], 'category'],
    'topics'     => ['COM_KUNENA_TOOLS_SUBS_TOP_TOPICS', $data['topTopics'], 'topic'],
    'users'      => ['COM_KUNENA_TOOLS_SUBS_TOP_USERS', $data['topUsers'], 'user'],
];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-bell" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <section class="kd-stats kd-stats--auto">
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_CATEGORIES') ?></span>
            <span class="kd-stat__value"><?php echo (int) $data['categories'] ?></span>
        </div>
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_TOPICS') ?></span>
            <span class="kd-stat__value"><?php echo (int) $data['topics'] ?></span>
        </div>
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_USERS') ?></span>
            <span class="kd-stat__value"><?php echo (int) $data['users'] ?></span>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="subscriptionsoverview">
        <h3 class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_OF_USER') ?></h3>
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-subs-search"><?php echo Text::_('COM_KUNENA_TOOLS_USER_SEARCH') ?></label>
                <input type="search" class="form-control" id="kd-subs-search" name="search" value="<?php echo $this->escape($data['search']) ?>"
                       data-kd-users="<?php echo $this->escape($this->usersEndpoint) ?>"
                       data-kd-users-go="<?php echo $this->escape(Route::_('index.php?option=com_kunena&view=tools&layout=subscriptionsoverview&userid=__ID__', false) . '#kd-subs-user') ?>"
                       placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_USER_SEARCH_HINT')) ?>">
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
                </button>
            </div>
        </div>

        <?php if ($data['search'] !== '') : ?>
            <?php if ($data['matches']) : ?>
                <ul class="kd-tags mt-3">
                    <?php foreach ($data['matches'] as $id => $match) : ?>
                        <li>
                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=subscriptionsoverview&userid=' . (int) $id) ?>">
                                <?php echo $this->escape($match['username']) ?> (<?php echo $this->escape($match['name']) ?>, #<?php echo (int) $id ?>)
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php else : ?>
                <p class="kd-form__hint mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NO_MATCH') ?></p>
            <?php endif ?>
        <?php endif ?>
    </form>

    <?php if ($user && $userSubs) :
        $userid = (int) $user['id'];
        $count  = \count($userSubs['categories']) + $userSubs['topicsTotal']; ?>
        <section class="kd-panel" aria-labelledby="kd-subs-user">
            <div class="kd-section__head">
                <h3 id="kd-subs-user" class="kd-panel__title">
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . $userid) ?>"><?php echo $this->escape($user['username']) ?></a>
                    · <?php echo Text::plural('COM_KUNENA_TOOLS_SUBS_N', $count) ?>
                </h3>
                <?php if ($count) : ?>
                    <div class="kd-check__actions"><?php echo $button('user', $userid, $userid, 'COM_KUNENA_TOOLS_SUBS_UNSUBSCRIBE_USER') ?></div>
                <?php endif ?>
            </div>

            <?php if (!$count) : ?>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_SUBS_USER_NONE') ?></p>
            <?php endif ?>

            <?php foreach (['categories' => 'COM_KUNENA_TOOLS_SUBS_USER_CATEGORIES', 'topics' => 'COM_KUNENA_TOOLS_SUBS_USER_TOPICS'] as $key => $title) :
                if (!$userSubs[$key]) {
                    continue;
                } ?>
                <h4 class="kd-form__label mt-3"><?php echo Text::_($title) ?></h4>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach ($userSubs[$key] as $row) : ?>
                                <tr>
                                    <th scope="row">
                                        <?php if ($key === 'categories') : ?>
                                            <?php echo $this->escape($row['name']) ?>
                                        <?php else : ?>
                                            <a href="<?php echo $this->escape($topicUrl($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($row['subject']) ?></a>
                                        <?php endif ?>
                                    </th>
                                    <td class="w-15 text-end"><?php echo $button($key === 'categories' ? 'category' : 'topic', (int) $row['id'], $userid, 'COM_KUNENA_TOOLS_SUBS_UNSUBSCRIBE_ONE') ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($key === 'topics' && $userSubs['topicsTotal'] > \count($userSubs['topics'])) : ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', $userSubs['topicsTotal'] - \count($userSubs['topics'])) ?></p>
                <?php endif ?>
            <?php endforeach ?>
        </section>
    <?php elseif ($data['userid'] && !$user) : ?>
        <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_PROFILE_NO_USER') ?></div>
    <?php endif ?>

    <?php foreach ($lists as $key => [$title, $rows, $scope]) : ?>
        <section class="kd-section" aria-labelledby="kd-subs-<?php echo $key ?>">
            <h3 id="kd-subs-<?php echo $key ?>" class="kd-section__title"><?php echo Text::_($title) ?></h3>
            <?php if (!$rows) : ?>
                <p class="kd-section__desc"><?php echo Text::_('COM_KUNENA_TOOLS_NONE') ?></p>
            <?php else : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach ($rows as $row) : ?>
                                <tr>
                                    <th scope="row">
                                        <?php if ($scope === 'category') : ?>
                                            <?php echo $this->escape($row['name']) ?>
                                        <?php elseif ($scope === 'topic') : ?>
                                            <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['category_id'] . '&id=' . (int) $row['id'])) ?>"
                                               target="_blank" rel="noopener"><?php echo $this->escape($row['subject']) ?></a>
                                        <?php else : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=subscriptionsoverview&userid=' . (int) $row['id']) ?>#kd-subs-user"><?php echo $this->escape($row['username']) ?></a>
                                        <?php endif ?>
                                    </th>
                                    <td class="w-15 text-end"><?php echo Text::plural($scope === 'user' ? 'COM_KUNENA_TOOLS_SUBS_N' : 'COM_KUNENA_TOOLS_CATMERGE_N_SUBSCRIBERS', (int) $row['total']) ?></td>
                                    <td class="w-15 text-end"><?php echo $button($scope, (int) $row['id']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </section>
    <?php endforeach ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
