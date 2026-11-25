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
$button = function (string $scope, int $id) use ($action): string {
    if (!$this->canAdmin) {
        return '';
    }

    return '<form action="' . $action . '" method="post">'
        . '<input type="hidden" name="task" value="tools.unsubscribeAll">'
        . '<input type="hidden" name="scope" value="' . $scope . '">'
        . '<input type="hidden" name="id" value="' . $id . '">'
        . HTMLHelper::_('form.token')
        . '<button type="submit" class="kd-btn kd-btn--sm kd-btn--ghost" data-kd-confirm>' . Text::_('COM_KUNENA_TOOLS_SUBS_UNSUBSCRIBE') . '</button>'
        . '</form>';
};
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
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . (int) $row['id']) ?>"><?php echo $this->escape($row['username']) ?></a>
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
