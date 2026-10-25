<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$this->ktemplate = KunenaFactory::getTemplate();
$bootstrap       = $this->ktemplate->params->get('bootstrap');

HTMLHelper::_('behavior.multiselect');

if ($bootstrap) {
    HTMLHelper::_('dropdown.init');
}

echo $this->subLayout('Widget/Datepicker');

// Load tribute script and css to have autocomplte on user field
$this->addScript('tribute.js');
$this->addStyleSheet('tribute.css');
$this->addScript('assets/js/search.js');

$select = 'class="form-select"';

// The options are open on the first visit of the form and when the user changed one of them.
$options = !$this->state->get('searchwords')
    || $this->state->get('query.searchuser')
    || (int) $this->state->get('query.show')
    || $this->state->get('query.childforums') === 0;
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search'); ?>" method="post" class="agora-search">
    <input type="hidden" name="task" value="results" />
    <?php if ($this->me->exists()) : ?>
        <input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>" />
    <?php endif; ?>
    <?php echo HTMLHelper::_('form.token'); ?>

    <header class="agora-search-head">
        <span class="agora-stats-head-icon" aria-hidden="true"><?php echo KunenaIcons::search(); ?></span>
        <h1><?php echo Text::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></h1>
    </header>

    <div class="agora-search-bar" id="main-search">
        <div class="agora-search-query">
            <span class="agora-search-query-icon" aria-hidden="true"><?php echo KunenaIcons::search(); ?></span>
            <label for="kkeywordsearch" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_KEYWORDS'); ?></label>
            <input id="kkeywordsearch" type="search" name="query" class="form-control" value="<?php echo $this->escape($this->state->get('searchwords')); ?>"
                   placeholder="<?php echo Text::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>" />
            <button type="submit" class="btn btn-primary">
                <?php echo Text::_('COM_KUNENA_SEARCH_SEND'); ?>
            </button>
        </div>
        <div class="agora-search-mode">
            <label for="mode" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></label>
            <?php $this->displayModeList('mode', $select); ?>
        </div>
    </div>

    <details class="agora-search-options" id="searchoptions"<?php echo $options ? ' open' : ''; ?>>
        <summary>
            <?php echo KunenaIcons::cog(); ?>
            <?php echo Text::_('COM_KUNENA_SEARCH_OPTIONS'); ?>
        </summary>

        <div class="agora-search-fields">
            <?php if (!$this->config->pubProfile && !Factory::getApplication()->getIdentity()->guest || $this->config->pubProfile) : ?>
                <fieldset class="agora-search-field">
                    <legend><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?></legend>
                    <label for="kusersearch" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_UNAME'); ?></label>
                    <input id="kusersearch" type="text" name="searchuser" class="form-control" value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>"
                           placeholder="<?php echo Text::_('COM_KUNENA_SEARCH_UNAME'); ?>" />
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="exactname" id="kexactname" value="1"<?php echo $this->state->get('query.exactname') ? ' checked="checked"' : ''; ?> />
                        <label class="form-check-label" for="kexactname"><?php echo Text::_('COM_KUNENA_SEARCH_EXACT'); ?></label>
                    </div>
                </fieldset>
            <?php endif; ?>

            <fieldset class="agora-search-field">
                <legend><?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?></legend>
                <label for="date" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?></label>
                <?php $this->displayDateList('date', $select); ?>
                <label for="beforeafter" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?></label>
                <?php $this->displayBeforeAfterList('beforeafter', $select); ?>
            </fieldset>

            <fieldset class="agora-search-field">
                <legend><?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?></legend>
                <label for="sort" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?></label>
                <?php $this->displaySortByList('sort', $select); ?>
                <label for="order" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?></label>
                <?php $this->displayOrderList('order', $select); ?>
            </fieldset>

            <fieldset class="agora-search-field">
                <legend><?php echo Text::_('COM_KUNENA_SEARCH_AT_A_SPECIFIC_DATE'); ?></legend>
                <div id="searchatdate" class="input-group date" data-date-format="<?php echo $this->config->datePickerFormat; ?>">
                    <label for="searchatdate-input" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_AT_A_SPECIFIC_DATE'); ?></label>
                    <input type="text" class="form-control" id="searchatdate-input" name="searchatdate" data-date-format="<?php echo $this->config->datePickerFormat; ?>">
                    <button class="btn btn-outline-secondary" type="button" id="button-searchatdate"
                            aria-label="<?php echo Text::_('COM_KUNENA_SEARCH_LABEL_SELECT_DATE'); ?>">
                        <?php echo KunenaIcons::calendar(); ?>
                    </button>
                </div>
            </fieldset>

            <fieldset class="agora-search-field">
                <legend><?php echo Text::_('COM_KUNENA_SEARCH_START'); ?></legend>
                <div class="agora-search-pair">
                    <label for="klimitstart" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_START'); ?></label>
                    <input id="klimitstart" type="text" name="limitstart" class="form-control" value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5" />
                    <label for="limit" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_START'); ?></label>
                    <?php $this->displayLimitList('limit', $select); ?>
                </div>
            </fieldset>

            <?php if ($this->isModerator) : ?>
                <fieldset class="agora-search-field">
                    <legend><?php echo Text::_('COM_KUNENA_SEARCH_SHOW'); ?></legend>
                    <?php
                    $showOptions = [
                        0 => 'COM_KUNENA_SEARCH_SHOW_NORMAL',
                        1 => 'COM_KUNENA_SEARCH_SHOW_UNAPPROVED',
                        2 => 'COM_KUNENA_SEARCH_SHOW_TRASHED',
                    ];

                    foreach ($showOptions as $showValue => $showText) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show" id="kshow<?php echo $showValue; ?>" value="<?php echo $showValue; ?>"<?php echo $this->state->get('query.show') == $showValue ? ' checked="checked"' : ''; ?> />
                            <label class="form-check-label" for="kshow<?php echo $showValue; ?>"><?php echo Text::_($showText); ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>

            <fieldset class="agora-search-field agora-search-field-wide">
                <legend><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></legend>
                <label for="categorylist" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></label>
                <?php $this->displayCategoryList('categorylist', 'class="form-select" size="8" multiple'); ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="childforums" id="kchildforums" value="1"<?php echo $this->state->get('query.childforums') ? ' checked="checked"' : ''; ?> />
                    <label class="form-check-label" for="kchildforums"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?></label>
                </div>
            </fieldset>
        </div>

        <div class="agora-search-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo KunenaIcons::search(); ?> <?php echo Text::_('COM_KUNENA_SEARCH_SEND'); ?>
            </button>
            <button type="reset" class="btn btn-outline-secondary" onclick="window.history.back();">
                <?php echo KunenaIcons::cancel(); ?> <?php echo Text::_('COM_KUNENA_CANCEL'); ?>
            </button>
        </div>
    </details>
</form>
