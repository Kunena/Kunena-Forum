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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Tools\KunenaPermissionCheck;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$data     = $this->tool;
$page     = static fn (int $userid, int $catid) => Route::_('index.php?option=com_kunena&view=tools&layout=permissions&userid=' . $userid . '&catid=' . $catid);
$children = [];

foreach ($data['categories'] as $category) {
    $children[(int) $category['parentid']][] = $category;
}

$names = array_column($data['categories'], 'name', 'id');

$options = [];
$walk    = static function (int $parent, int $depth) use (&$walk, &$options, $children): void {
    foreach ($children[$parent] ?? [] as $category) {
        $options[] = [(int) $category['id'], str_repeat('— ', $depth) . $category['name']];
        $walk((int) $category['id'], $depth + 1);
    }
};
$walk(0, 0);
$yesNo = static fn (bool $value) => Text::_($value ? 'JYES' : 'JNO');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-shield" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="permissions">
        <input type="hidden" name="userid" value="<?php echo (int) $data['userid'] ?>">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-perms-search"><?php echo Text::_('COM_KUNENA_TOOLS_USER_SEARCH') ?></label>
                <input type="search" class="form-control" id="kd-perms-search" name="search" value="<?php echo $this->escape($data['search']) ?>"
                       data-kd-users="<?php echo $this->escape($this->usersEndpoint) ?>"
                       data-kd-users-go="<?php echo $this->escape(Route::_('index.php?option=com_kunena&view=tools&layout=permissions&userid=__ID__&catid=' . (int) $data['catid'], false)) ?>"
                       placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_USER_SEARCH_HINT')) ?>">
            </div>
            <div class="kd-form__field">
                <label for="kd-perms-category"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></label>
                <select class="form-select" id="kd-perms-category" name="catid">
                    <option value="0"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_CHOOSE') ?></option>
                    <?php foreach ($options as [$id, $label]) : ?>
                        <option value="<?php echo $id ?>"<?php echo $id === $data['catid'] ? ' selected' : '' ?>><?php echo $this->escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_PERMS_CHECK') ?>
                </button>
            </div>
        </div>

        <ul class="kd-tags mt-3">
            <li><a href="<?php echo $page(0, $data['catid']) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_AS_GUEST') ?></a></li>
            <?php foreach ($data['matches'] as $id => $match) : ?>
                <li><a href="<?php echo $page((int) $id, $data['catid']) ?>"><?php echo $this->escape($match['username']) ?> (#<?php echo (int) $id ?>)</a></li>
            <?php endforeach ?>
        </ul>
        <?php if ($data['search'] !== '' && !$data['matches']) : ?>
            <p class="kd-form__hint mt-2"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NO_MATCH') ?></p>
        <?php endif ?>
    </form>

    <?php if ($data['result']) :
        $profile = $data['profile'];
        $access  = $data['category']; ?>
        <div class="kd-columns">
            <section class="kd-panel" aria-labelledby="kd-perms-who">
                <h3 id="kd-perms-who" class="kd-panel__title"><?php echo $this->escape($data['userName']) ?></h3>
                <dl class="kd-kv">
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_GROUPS') ?></dt>
                    <dd><?php echo $this->escape(implode(', ', $profile['groups'])) ?: Text::_('COM_KUNENA_TOOLS_NONE') ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_LEVELS') ?></dt>
                    <dd><?php echo $this->escape(implode(', ', $profile['levels'])) ?: Text::_('COM_KUNENA_TOOLS_NONE') ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_FORUM_ADMIN') ?></dt>
                    <dd><?php echo $yesNo($profile['admin']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_MODERATOR') ?></dt>
                    <dd><?php echo $yesNo($profile['moderator']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_BANNED') ?></dt>
                    <dd><?php echo $yesNo($profile['banned']) ?></dd>
                </dl>
            </section>
            <section class="kd-panel" aria-labelledby="kd-perms-category-title">
                <h3 id="kd-perms-category-title" class="kd-panel__title"><?php echo $this->escape((string) ($names[$data['catid']] ?? '')) ?></h3>
                <dl class="kd-kv">
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_ACCESS') ?></dt>
                    <dd><code><?php echo $this->escape($access['type']) ?></code> · <?php echo $this->escape($access['access']) ?></dd>
                    <dt><?php echo Text::_('JPUBLISHED') ?></dt>
                    <dd><?php echo $yesNo($access['published']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_LOCKED') ?></dt>
                    <dd><?php echo $yesNo($access['locked']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_SECTION') ?></dt>
                    <dd><?php echo $yesNo($access['section']) ?></dd>
                </dl>
            </section>
        </div>

        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_ACTION') ?></th>
                        <th scope="col" class="w-10"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_RULES') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['result'] as $action => $item) :
                        $failing = array_filter($item['rules'], static fn ($message) => $message !== null); ?>
                        <tr>
                            <th scope="row">
                                <?php echo Text::_(KunenaPermissionCheck::ACTIONS[$action]) ?>
                                <br><code class="kd-check__code"><?php echo $this->escape($action) ?></code>
                            </th>
                            <td>
                                <span class="kd-pill kd-pill--<?php echo $item['allowed'] ? 'ok' : 'warn' ?>">
                                    <?php echo Text::_($item['allowed'] ? 'COM_KUNENA_TOOLS_PERMS_ALLOWED' : 'COM_KUNENA_TOOLS_PERMS_REFUSED') ?>
                                </span>
                            </td>
                            <td>
                                <ul class="kd-tags">
                                    <?php foreach ($item['rules'] as $rule => $message) : ?>
                                        <li title="<?php echo $this->escape((string) $message) ?>">
                                            <span class="fa-solid fa-<?php echo $message === null ? 'check' : 'xmark' ?>" aria-hidden="true"></span>
                                            <?php echo Text::_('COM_KUNENA_TOOLS_PERMS_RULE_' . strtoupper($rule)) ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                                <?php if (!$item['allowed']) : ?>
                                    <span class="kd-form__hint">
                                        <?php echo $this->escape($failing ? reset($failing) : Text::_('COM_KUNENA_TOOLS_PERMS_PLUGIN')) ?>
                                    </span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($data['catid']) : ?>
        <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_PROFILE_NO_USER') ?></div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
