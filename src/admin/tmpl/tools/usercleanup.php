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
use Kunena\Forum\Libraries\Tools\KunenaUserCleanup;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$summary = $this->tool['summary'];
$page    = 'index.php?option=com_kunena&view=tools&layout=usercleanup';
$date    = static fn ($value) => $value && !str_starts_with($value, '0000') && !str_starts_with($value, '1000')
    ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC4')) : Text::_('COM_KUNENA_TOOLS_NEVER');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-xmark" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="usercleanup">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-user-search"><?php echo Text::_('COM_KUNENA_TOOLS_USER_SEARCH') ?></label>
                <input type="search" class="form-control" id="kd-user-search" name="search" value="<?php echo $this->escape($this->tool['search']) ?>"
                       data-kd-users="<?php echo $this->escape($this->usersEndpoint) ?>" data-kd-users-go="<?php echo $this->escape(Route::_($page . '&userid=__ID__', false)) ?>"
                       placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_USER_SEARCH_HINT')) ?>">
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
                </button>
            </div>
        </div>

        <?php if ($this->tool['search'] !== '') : ?>
            <?php if ($this->tool['matches']) : ?>
                <ul class="kd-tags mt-3">
                    <?php foreach ($this->tool['matches'] as $id => $match) : ?>
                        <li>
                            <a href="<?php echo Route::_($page . '&userid=' . (int) $id) ?>">
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

    <?php if ($this->tool['userid'] && !$summary) : ?>
        <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_PROFILE_NO_USER') ?></div>
    <?php endif ?>

    <?php if ($summary) : ?>
        <section class="kd-stats kd-stats--auto" aria-label="<?php echo $this->escape($summary['username']) ?>">
            <div class="kd-stat">
                <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_MESSAGES') ?></span>
                <span class="kd-stat__value"><?php echo (int) $summary['messages']['total'] ?></span>
                <span class="kd-stat__hint">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_USERCLEANUP_MESSAGES_HINT', $summary['messages']['published'], $summary['messages']['unapproved'], $summary['messages']['deleted']) ?>
                </span>
            </div>
            <div class="kd-stat">
                <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_TOPICS') ?></span>
                <span class="kd-stat__value"><?php echo (int) $summary['topics'] ?></span>
            </div>
            <div class="kd-stat">
                <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_ATTACHMENTS') ?></span>
                <span class="kd-stat__value"><?php echo (int) $summary['attachments']['total'] ?></span>
                <span class="kd-stat__hint"><?php echo HTMLHelper::_('number.bytes', $summary['attachments']['bytes']) ?></span>
            </div>
            <div class="kd-stat<?php echo $summary['others'] ? ' kd-stat--attention' : '' ?>">
                <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_SHARED') ?></span>
                <span class="kd-stat__value"><?php echo \count($summary['others']) ?></span>
                <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_SHARED_HINT') ?></span>
            </div>
        </section>

        <div class="kd-columns">
            <section class="kd-panel" aria-labelledby="kd-user-account">
                <h3 id="kd-user-account" class="kd-panel__title"><?php echo $this->escape($summary['username']) ?></h3>
                <dl class="kd-kv">
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_NAME') ?></dt>
                    <dd><?php echo $this->escape($summary['name']) ?> (#<?php echo (int) $summary['id'] ?>)</dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_EMAIL') ?></dt>
                    <dd><?php echo $this->escape($summary['email']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_REGISTERED') ?></dt>
                    <dd><?php echo $date($summary['registered']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_LASTVISIT') ?></dt>
                    <dd><?php echo $date($summary['lastVisit']) ?></dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USER_STATUS') ?></dt>
                    <dd>
                        <?php if ($summary['blocked'] || $summary['banBlocks']) : ?>
                            <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_USER_BLOCKED') ?></span>
                        <?php elseif ($summary['banned']) : ?>
                            <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_USER_BANNED') ?></span>
                        <?php else : ?>
                            <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOOLS_USER_ACTIVE') ?></span>
                        <?php endif ?>
                    </dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_IPS') ?></dt>
                    <dd>
                        <?php if ($summary['ips']) : ?>
                            <ul class="kd-tags">
                                <?php foreach ($summary['ips'] as $ip => $count) : ?>
                                    <li><code><?php echo $this->escape($ip) ?></code> × <?php echo (int) $count ?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php else : ?>
                            <?php echo Text::_('COM_KUNENA_TOOLS_NONE') ?>
                        <?php endif ?>
                    </dd>
                    <dt><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_SHARED') ?></dt>
                    <dd>
                        <?php if ($summary['others']) : ?>
                            <ul class="kd-tags">
                                <?php foreach ($summary['others'] as $id => $username) : ?>
                                    <li><a href="<?php echo Route::_($page . '&userid=' . (int) $id) ?>"><?php echo $this->escape($username) ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        <?php else : ?>
                            <?php echo Text::_('COM_KUNENA_TOOLS_NONE') ?>
                        <?php endif ?>
                    </dd>
                </dl>
            </section>

            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel"
                  aria-labelledby="kd-user-actions">
                <input type="hidden" name="task" value="tools.cleanupUser">
                <input type="hidden" name="userid" value="<?php echo (int) $summary['id'] ?>">
                <?php echo HTMLHelper::_('form.token') ?>
                <h3 id="kd-user-actions" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_ACTIONS') ?></h3>

                <fieldset class="kd-choices">
                    <legend><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_MESSAGES') ?></legend>
                    <?php foreach ([KunenaUserCleanup::MESSAGES_KEEP, KunenaUserCleanup::MESSAGES_TRASH, KunenaUserCleanup::MESSAGES_DELETE] as $value) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="messages" id="kd-messages-<?php echo $value ?>" value="<?php echo $value ?>"
                                <?php echo $value === KunenaUserCleanup::MESSAGES_KEEP ? 'checked' : '' ?>>
                            <label class="form-check-label" for="kd-messages-<?php echo $value ?>"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_MESSAGES_' . strtoupper($value)) ?></label>
                        </div>
                    <?php endforeach ?>
                </fieldset>

                <fieldset class="kd-choices">
                    <legend><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_PROFILE') ?></legend>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="attachments" id="kd-attachments" value="1">
                        <label class="form-check-label" for="kd-attachments"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_DELETE_ATTACHMENTS') ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="profile" id="kd-profile" value="1">
                        <label class="form-check-label" for="kd-profile"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_CLEAR_PROFILE') ?></label>
                    </div>
                </fieldset>

                <fieldset class="kd-choices">
                    <legend><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_ACCOUNT') ?></legend>
                    <?php foreach ([KunenaUserCleanup::ACCOUNT_KEEP, KunenaUserCleanup::ACCOUNT_BAN, KunenaUserCleanup::ACCOUNT_BLOCK] as $value) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="account" id="kd-account-<?php echo $value ?>" value="<?php echo $value ?>"
                                <?php echo $value === KunenaUserCleanup::ACCOUNT_KEEP ? 'checked' : '' ?>>
                            <label class="form-check-label" for="kd-account-<?php echo $value ?>"><?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_ACCOUNT_' . strtoupper($value)) ?></label>
                        </div>
                    <?php endforeach ?>
                </fieldset>

                <?php if ($this->canAdmin) : ?>
                    <div class="kd-form__actions">
                        <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                            <span class="fa-solid fa-user-xmark" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_APPLY') ?>
                        </button>
                        <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=mergeusers&from=' . (int) $summary['id']) ?>">
                            <span class="fa-solid fa-code-merge" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_MERGE') ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
                <?php endif ?>
            </form>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
