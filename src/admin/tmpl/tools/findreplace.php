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
use Kunena\Forum\Libraries\Tools\KunenaFindReplace;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$options = $this->tool['options'];
$found   = $this->tool['found'];

// The text escaped, the parts which match the needle marked.
$mark = function (string $text, string $needle) use ($options): string {
    $text = $this->escape($text);

    if ($needle === '') {
        return $text;
    }

    return (string) preg_replace('/' . preg_quote($this->escape($needle), '/') . '/u' . ($options['case'] ? '' : 'i'), '<mark>$0</mark>', $text);
};
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="findreplace">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-replace-search"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_FIND') ?></label>
                <input type="text" class="form-control" id="kd-replace-search" name="search" value="<?php echo $this->escape($options['search']) ?>" required
                       minlength="<?php echo KunenaFindReplace::MIN_LENGTH ?>" placeholder="http://old.example.com">
            </div>
            <div class="kd-form__field">
                <label for="kd-replace-replace"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_WITH') ?></label>
                <input type="text" class="form-control" id="kd-replace-replace" name="replace" value="<?php echo $this->escape($options['replace']) ?>"
                       placeholder="https://new.example.com">
            </div>
        </div>
        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="subjects" id="kd-replace-subjects" value="1"<?php echo $options['subjects'] ? ' checked' : '' ?>>
            <label class="form-check-label" for="kd-replace-subjects"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_SUBJECTS') ?></label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="case" id="kd-replace-case" value="1"<?php echo $options['case'] ? ' checked' : '' ?>>
            <label class="form-check-label" for="kd-replace-case"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_CASE') ?></label>
        </div>
        <div class="kd-form__actions">
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-eye" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_PREVIEW') ?>
            </button>
        </div>
    </form>

    <?php if ($this->tool['error']) : ?>
        <div class="alert alert-warning"><?php echo Text::sprintf($this->tool['error'], KunenaFindReplace::MIN_LENGTH) ?></div>
    <?php endif ?>

    <?php if ($found !== null) : ?>
        <section class="kd-section" aria-labelledby="kd-replace-found">
            <div class="kd-section__head">
                <h3 id="kd-replace-found" class="kd-section__title"><?php echo Text::plural('COM_KUNENA_TOOLS_REPLACE_N_FOUND', $found['total']) ?></h3>
                <?php if ($found['total'] && $this->canAdmin) : ?>
                    <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                        <input type="hidden" name="task" value="tools.findReplace">
                        <input type="hidden" name="search" value="<?php echo $this->escape($options['search']) ?>">
                        <input type="hidden" name="replace" value="<?php echo $this->escape($options['replace']) ?>">
                        <input type="hidden" name="subjects" value="<?php echo (int) $options['subjects'] ?>">
                        <input type="hidden" name="case" value="<?php echo (int) $options['case'] ?>">
                        <?php echo HTMLHelper::_('form.token') ?>
                        <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_REPLACE_CONFIRM')) ?>">
                            <span class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></span>
                            <?php echo Text::plural('COM_KUNENA_TOOLS_REPLACE_APPLY_N', min($found['total'], KunenaFindReplace::BATCH)) ?>
                        </button>
                    </form>
                <?php endif ?>
            </div>
            <?php if ($found['total'] > KunenaFindReplace::BATCH) : ?>
                <p class="kd-section__desc"><?php echo Text::sprintf('COM_KUNENA_TOOLS_REPLACE_BATCH', KunenaFindReplace::BATCH) ?></p>
            <?php endif ?>

            <?php if ($found['items']) : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></th>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_BEFORE') ?></th>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_AFTER') ?></th>
                                <th scope="col" class="w-5 text-end">×</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($found['items'] as $item) :
                                $link = Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . $item['catid'] . '&id=' . $item['thread'] . '&mesid=' . $item['id']) . '#' . $item['id']; ?>
                                <tr>
                                    <th scope="row">
                                        <a href="<?php echo $this->escape($link) ?>" target="_blank" rel="noopener"><?php echo $this->escape($item['subject']) ?></a>
                                        <?php if ($item['newSubject'] !== $item['subject']) : ?>
                                            <br><span class="kd-form__hint">→ <?php echo $this->escape($item['newSubject']) ?></span>
                                        <?php endif ?>
                                    </th>
                                    <td><code class="kd-check__code"><?php echo $mark($item['before'], $options['search']) ?></code></td>
                                    <td><code class="kd-check__code"><?php echo $mark($item['after'], $options['replace']) ?></code></td>
                                    <td class="text-end"><?php echo (int) $item['count'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($found['total'] > \count($found['items'])) : ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', $found['total'] - \count($found['items'])) ?></p>
                <?php endif ?>
            <?php endif ?>
        </section>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
