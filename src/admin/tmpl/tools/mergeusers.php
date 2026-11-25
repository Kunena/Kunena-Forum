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

$from   = $this->tool['from'];
$into   = $this->tool['into'];
$fromId = $from ? (int) $from['id'] : 0;
$intoId = $into ? (int) $into['id'] : 0;
$page   = static fn (int $f, int $i) => Route::_('index.php?option=com_kunena&view=tools&layout=mergeusers&from=' . $f . '&into=' . $i);
$sides  = [
    'from' => ['COM_KUNENA_TOOLS_MERGE_FROM', 'COM_KUNENA_TOOLS_MERGE_FROM_DESC', $from, $this->tool['fromQuery'], $this->tool['fromMatch']],
    'into' => ['COM_KUNENA_TOOLS_MERGE_INTO', 'COM_KUNENA_TOOLS_MERGE_INTO_DESC', $into, $this->tool['intoQuery'], $this->tool['intoMatch']],
];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-code-merge" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_MERGE') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_MERGE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <div class="kd-columns">
        <?php foreach ($sides as $side => [$title, $desc, $user, $query, $matches]) : ?>
            <section class="kd-panel" aria-labelledby="kd-merge-<?php echo $side ?>">
                <h3 id="kd-merge-<?php echo $side ?>" class="kd-panel__title"><?php echo Text::_($title) ?></h3>
                <p class="kd-form__hint"><?php echo Text::_($desc) ?></p>

                <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-form">
                    <input type="hidden" name="option" value="com_kunena">
                    <input type="hidden" name="view" value="tools">
                    <input type="hidden" name="layout" value="mergeusers">
                    <input type="hidden" name="from" value="<?php echo $fromId ?>">
                    <input type="hidden" name="into" value="<?php echo $intoId ?>">
                    <div class="kd-form__field">
                        <label for="kd-merge-search-<?php echo $side ?>"><?php echo Text::_('COM_KUNENA_TOOLS_USER_SEARCH') ?></label>
                        <input type="search" class="form-control" id="kd-merge-search-<?php echo $side ?>" name="<?php echo $side ?>_search"
                               data-kd-users="<?php echo $this->escape($this->usersEndpoint) ?>"
                               data-kd-users-go="<?php echo $this->escape(Route::_('index.php?option=com_kunena&view=tools&layout=mergeusers&' . ($side === 'from' ? 'from=__ID__&into=' . $intoId : 'from=' . $fromId . '&into=__ID__'), false)) ?>"
                               value="<?php echo $this->escape($query) ?>" placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_USER_SEARCH_HINT')) ?>">
                    </div>
                    <div>
                        <button type="submit" class="kd-btn kd-btn--ghost">
                            <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                            <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
                        </button>
                    </div>
                </form>

                <?php if ($matches) : ?>
                    <ul class="kd-tags mt-3">
                        <?php foreach ($matches as $id => $match) : ?>
                            <li>
                                <a href="<?php echo $side === 'from' ? $page((int) $id, $intoId) : $page($fromId, (int) $id) ?>">
                                    <?php echo $this->escape($match['username']) ?> (#<?php echo (int) $id ?>)
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php elseif (trim($query) !== '') : ?>
                    <p class="kd-form__hint mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NO_MATCH') ?></p>
                <?php endif ?>

                <?php if ($user) : ?>
                    <dl class="kd-kv mt-3">
                        <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_NAME') ?></dt>
                        <dd><strong><?php echo $this->escape($user['username']) ?></strong> — <?php echo $this->escape($user['name']) ?> (#<?php echo (int) $user['id'] ?>)</dd>
                        <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_EMAIL') ?></dt>
                        <dd><?php echo $this->escape($user['email']) ?></dd>
                        <dt><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_MESSAGES') ?></dt>
                        <dd><?php echo (int) $user['messages']['total'] ?></dd>
                        <dt><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_TOPICS') ?></dt>
                        <dd><?php echo (int) $user['topics'] ?></dd>
                    </dl>
                <?php endif ?>
            </section>
        <?php endforeach ?>
    </div>

    <?php if ($this->tool['error'] && $fromId && $intoId) : ?>
        <div class="alert alert-warning"><?php echo Text::_($this->tool['error']) ?></div>
    <?php endif ?>

    <?php if ($from && $into && !$this->tool['error']) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel"
              aria-labelledby="kd-merge-confirm">
            <input type="hidden" name="task" value="tools.mergeUsers">
            <input type="hidden" name="from" value="<?php echo $fromId ?>">
            <input type="hidden" name="into" value="<?php echo $intoId ?>">
            <?php echo HTMLHelper::_('form.token') ?>
            <h3 id="kd-merge-confirm" class="kd-panel__title">
                <?php echo Text::sprintf('COM_KUNENA_TOOLS_MERGE_SUMMARY', $this->escape($from['username']), $this->escape($into['username'])) ?>
            </h3>

            <?php if ($this->tool['counts']) : ?>
                <ul class="kd-tags">
                    <?php foreach ($this->tool['counts'] as $table => $count) : ?>
                        <li><code><?php echo $this->escape(str_replace('#__kunena_', '', $table)) ?></code> × <?php echo (int) $count ?></li>
                    <?php endforeach ?>
                </ul>
            <?php else : ?>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_MERGE_NOTHING') ?></p>
            <?php endif ?>

            <p class="kd-form__hint mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_MERGE_KEPT') ?></p>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="block" id="kd-merge-block" value="1" checked>
                <label class="form-check-label" for="kd-merge-block"><?php echo Text::sprintf('COM_KUNENA_TOOLS_MERGE_BLOCK', $this->escape($from['username'])) ?></label>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-code-merge" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_MERGE_APPLY') ?>
                    </button>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
