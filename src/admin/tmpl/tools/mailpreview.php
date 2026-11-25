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

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaMailPreview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$data         = $this->tool;
$rendered     = $data['rendered'];
$edit         = $data['edit'];
$languageName = $data['languages'][$data['language']] ?? $data['language'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-envelope-open-text" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_mails&view=templates&filter[extension]=com_kunena') ?>">
                <span class="fa-solid fa-pen-to-square" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_MAILS_EDIT') ?>
            </a>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="mailpreview">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-mails-template"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_TEMPLATE') ?></label>
                <select class="form-select" id="kd-mails-template" name="template">
                    <?php foreach (KunenaMailPreview::TEMPLATES as $id => $label) : ?>
                        <option value="<?php echo $this->escape($id) ?>"<?php echo $id === $data['template'] ? ' selected' : '' ?>><?php echo Text::_($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="kd-form__field">
                <label for="kd-mails-language"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_LANGUAGE') ?></label>
                <select class="form-select" id="kd-mails-language" name="language">
                    <?php foreach ($data['languages'] as $tag => $name) : ?>
                        <option value="<?php echo $this->escape($tag) ?>"<?php echo $tag === $data['language'] ? ' selected' : '' ?>><?php echo $this->escape($name) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-eye" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_PREVIEW') ?>
                </button>
            </div>
        </div>
    </form>

    <?php if (!$rendered) : ?>
        <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_MISSING') ?></div>
    <?php else : ?>
        <section class="kd-panel" aria-labelledby="kd-mails-subject">
            <p class="kd-form__label"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_SUBJECT') ?>
                <?php if ($rendered['customized']) : ?>
                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_CUSTOMIZED') ?></span>
                <?php endif ?>
            </p>
            <h3 id="kd-mails-subject" class="kd-panel__title"><?php echo $this->escape($rendered['subject']) ?></h3>

            <p class="kd-form__label mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_TEXT') ?></p>
            <?php // The addresses are links, as mail programs show them (the link to unsubscribe can be tried). ?>
            <pre class="kd-mail-text"><?php echo preg_replace(
                '#https?://[^\s<]+#',
                '<a href="$0" target="_blank" rel="noopener">$0</a>',
                $this->escape($rendered['body'])
            ) ?></pre>

            <?php if (trim($rendered['html']) !== '') : ?>
                <p class="kd-form__label mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_HTML') ?></p>
                <?php // No script and no access to this page; a link opens in a new tab (the link to unsubscribe can be tried). ?>
                <iframe class="kd-mail-html" sandbox="allow-popups allow-popups-to-escape-sandbox"
                        srcdoc="<?php echo $this->escape('<base target="_blank">' . $rendered['html']) ?>"
                        title="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_MAILS_HTML')) ?>"></iframe>
            <?php endif ?>

            <?php // The form below has the tags as buttons.
            if ($rendered['tags'] && !($this->canAdmin && $edit)) : ?>
                <p class="kd-form__label mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_TAGS') ?></p>
                <ul class="kd-tags">
                    <?php foreach ($rendered['tags'] as $tag) : ?>
                        <li><code>{<?php echo $this->escape(strtoupper($tag)) ?>}</code></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </section>

        <?php if ($this->canAdmin && $edit) : ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel kd-mail-edit"
                  id="kd-mails-edit" aria-labelledby="kd-mails-edit-title">
                <input type="hidden" name="task" value="tools.saveMailTemplate">
                <input type="hidden" name="template" value="<?php echo $this->escape($data['template']) ?>">
                <input type="hidden" name="language" value="<?php echo $this->escape($data['language']) ?>">
                <?php echo HTMLHelper::_('form.token') ?>

                <h3 class="kd-panel__title" id="kd-mails-edit-title"><?php echo Text::sprintf('COM_KUNENA_TOOLS_MAILS_EDIT_TITLE', $this->escape($languageName)) ?></h3>
                <p class="kd-form__hint">
                    <?php echo Text::_($edit['customized'] ? 'COM_KUNENA_TOOLS_MAILS_EDIT_CUSTOMIZED' : 'COM_KUNENA_TOOLS_MAILS_EDIT_DEFAULT') ?>
                </p>

                <div class="kd-form__field">
                    <label for="kd-mails-edit-subject"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_SUBJECT') ?></label>
                    <input type="text" class="form-control" id="kd-mails-edit-subject" name="subject" value="<?php echo $this->escape($edit['subject']) ?>" required data-kd-tag-target>
                </div>
                <div class="kd-form__field">
                    <label for="kd-mails-edit-body"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_TEXT') ?></label>
                    <textarea class="form-control kd-mail-edit__text" id="kd-mails-edit-body" name="body" rows="<?php echo min(24, max(8, substr_count($edit['body'], "\n") + 2)) ?>"
                              required data-kd-tag-target><?php echo $this->escape($edit['body']) ?></textarea>
                </div>

                <?php if ($data['html']) : ?>
                    <div class="kd-form__field" data-kd-tag-editor="kd-mails-edit-htmlbody">
                        <label for="kd-mails-edit-htmlbody"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_HTML') ?></label>
                        <?php echo Editor::getInstance(Factory::getApplication()->get('editor'))
                            ->display('htmlbody', $edit['htmlbody'], '100%', '320', '60', '20', false, 'kd-mails-edit-htmlbody') ?>
                    </div>
                <?php else : ?>
                    <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_EDIT_NO_HTML') ?></p>
                <?php endif ?>

                <?php if ($edit['tags']) : ?>
                    <div class="kd-form__field">
                        <span class="kd-form__label"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_EDIT_TAGS') ?></span>
                        <div class="kd-tags">
                            <?php foreach ($edit['tags'] as $tag) : ?>
                                <button type="button" class="kd-tag-insert" data-kd-insert="{<?php echo $this->escape(strtoupper($tag)) ?>}">{<?php echo $this->escape(strtoupper($tag)) ?>}</button>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>

                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-floppy-disk" aria-hidden="true"></span>
                        <?php echo Text::_('JSAVE') ?>
                    </button>
                    <?php if ($edit['customized']) : ?>
                        <button type="submit" class="kd-btn kd-btn--ghost" form="kd-mails-reset" data-kd-confirm="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_MAILS_RESET_CONFIRM')) ?>">
                            <span class="fa-solid fa-rotate-left" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_MAILS_RESET') ?>
                        </button>
                    <?php endif ?>
                </div>
            </form>

            <?php if ($edit['customized']) : ?>
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="kd-mails-reset" hidden>
                    <input type="hidden" name="task" value="tools.resetMailTemplate">
                    <input type="hidden" name="template" value="<?php echo $this->escape($data['template']) ?>">
                    <input type="hidden" name="language" value="<?php echo $this->escape($data['language']) ?>">
                    <?php echo HTMLHelper::_('form.token') ?>
                </form>
            <?php endif ?>
        <?php endif ?>

        <?php if ($this->canAdmin) : ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel">
                <input type="hidden" name="task" value="tools.sendMailPreview">
                <input type="hidden" name="template" value="<?php echo $this->escape($data['template']) ?>">
                <input type="hidden" name="language" value="<?php echo $this->escape($data['language']) ?>">
                <?php echo HTMLHelper::_('form.token') ?>
                <div class="kd-form">
                    <div class="kd-form__field">
                        <label for="kd-mails-to"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_TO') ?></label>
                        <input type="email" class="form-control" id="kd-mails-to" name="to" value="<?php echo $this->escape($data['me']) ?>" required>
                    </div>
                    <div>
                        <button type="submit" class="kd-btn kd-btn--primary">
                            <span class="fa-solid fa-paper-plane" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_MAILS_SEND') ?>
                        </button>
                    </div>
                </div>
            </form>
        <?php endif ?>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
