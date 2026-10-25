<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$cols            = !empty($this->actions) ? 6 : 7;
$colspan         = !empty($this->actions) ? 4 : 3;
$view            = $this->input->getWord('view');
$layout          = $this->input->getWord('layout');
$this->ktemplate = KunenaFactory::getTemplate();
$me              = KunenaUserHelper::getMyself();

if (KunenaConfig::getInstance()->ratingEnabled) {
    $this->addStyleSheet('rating.css');
}

// Reload the list in the background and make new topics blink.
if ($this->ktemplate->params->get('ajaxTopicList', 1)) {
    Factory::getApplication()->getDocument()->addScriptOptions(
        'com_kunena.topiclist',
        ['interval' => (int) $this->ktemplate->params->get('ajaxTopicListInterval', 30)]
    );
    $this->addScript('assets/js/topiclist.js');
}
?>
<header class="agora-list-head">
    <div class="agora-list-title">
        <span class="agora-stats-head-icon" aria-hidden="true"><?php echo KunenaIcons::file(); ?></span>
        <h1>
            <?php echo $this->escape($this->headerText); ?>

            <?php if ($layout != 'unread') : ?>
                <small data-ktopics-count><?php echo KunenaCategory::getInstance()->totalCount($this->pagination->total); ?></small>
            <?php endif; ?>
        </h1>
    </div>

    <?php if ($view != 'user') : ?>
        <div class="agora-list-time" id="filter-time">
            <form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>"
                  id="timeselect" name="timeselect"
                  method="post" target="_self">
                <?php $this->displayTimeFilter('sel'); ?>
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
    <?php endif; ?>
</header>

<?php
if ($this->config->enableForumJump && !$this->embedded && $this->topics) {
    echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
} ?>

<?php if (!empty($this->filterBar)) : ?>
    <?php
    $filterCategories = HTMLHelper::_(
        'kunenaforum.categorylist',
        'catid',
        0,
        [HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FILTER_ALL_CATEGORIES'))],
        [],
        'class="form-select form-select-sm" aria-label="' . Text::_('COM_KUNENA_CATEGORY') . '"',
        'value',
        'text',
        $this->input->getInt('catid', 0),
        'kfilter-catid'
    );
    // The address without the filters: the other parameters (non SEF addresses, userid, ...) stay.
    $filterBase     = Uri::getInstance()->toString(['scheme', 'host', 'port', 'path']);
    $filterKeep     = array_filter(
        array_diff_key(Uri::getInstance()->getQuery(true), array_flip(['fq', 'fauthor', 'fstatus', 'ftag', 'catid', 'start', 'limitstart'])),
        'is_scalar'
    );
    $filterResetUrl = $filterBase . ($filterKeep ? '?' . http_build_query($filterKeep) : '');
    $statusOptions = [
        ''          => 'COM_KUNENA_FILTER_STATUS_ALL',
        'noreplies' => 'COM_KUNENA_FILTER_STATUS_NOREPLIES',
        'replied'   => 'COM_KUNENA_FILTER_STATUS_REPLIED',
        'sticky'    => 'COM_KUNENA_FILTER_STATUS_STICKY',
        'locked'    => 'COM_KUNENA_FILTER_STATUS_LOCKED',
        'poll'      => 'COM_KUNENA_FILTER_STATUS_POLL',
    ];
    ?>
    <form action="<?php echo $this->escape($filterBase); ?>" method="get"
          id="ktopicsfilter" class="row g-2 align-items-center agora-list-filters" role="search">
        <?php foreach ($filterKeep as $keepKey => $keepValue) : ?>
            <input type="hidden" name="<?php echo $this->escape($keepKey); ?>" value="<?php echo $this->escape($keepValue); ?>"/>
        <?php endforeach; ?>
        <div class="col-12 col-md-3">
            <?php echo $filterCategories; ?>
        </div>
        <div class="col-6 col-md-2">
            <select name="fstatus" class="form-select form-select-sm" aria-label="<?php echo Text::_('COM_KUNENA_FILTER_STATUS'); ?>">
                <?php foreach ($statusOptions as $value => $label) : ?>
                    <option value="<?php echo $value; ?>"<?php echo $this->filters['status'] === $value ? ' selected' : ''; ?>><?php echo Text::_($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php $filterTags = KunenaTopicTags::isEnabled() ? KunenaTopicTags::getAvailable() : []; ?>
        <?php if ($filterTags) : ?>
            <div class="col-6 col-md-2">
                <select name="ftag" class="form-select form-select-sm" aria-label="<?php echo Text::_('COM_KUNENA_FILTER_TAG'); ?>">
                    <option value="0"><?php echo Text::_('COM_KUNENA_FILTER_ALL_TAGS'); ?></option>
                    <?php foreach ($filterTags as $filterTag) : ?>
                        <option value="<?php echo (int) $filterTag->id; ?>"<?php echo (int) $this->filters['tag'] === (int) $filterTag->id ? ' selected' : ''; ?>><?php echo $this->escape($filterTag->title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="col-6 col-md-3">
            <input type="search" name="fq" value="<?php echo $this->escape($this->filters['q']); ?>" class="form-control form-control-sm"
                   placeholder="<?php echo Text::_('COM_KUNENA_FILTER_SUBJECT'); ?>" aria-label="<?php echo Text::_('COM_KUNENA_FILTER_SUBJECT'); ?>" autocomplete="off"/>
        </div>
        <?php if (KunenaMultilanguage::isEnabled()) : ?>
            <div class="col-6 col-md-2">
                <select name="flang" class="form-select form-select-sm" data-default="<?php echo $this->escape(KunenaMultilanguage::getCurrent()); ?>" aria-label="<?php echo Text::_('COM_KUNENA_FILTER_LANGUAGE'); ?>">
                    <option value="all"<?php echo $this->filters['language'] === 'all' ? ' selected' : ''; ?>><?php echo Text::_('COM_KUNENA_FILTER_ALL_LANGUAGES'); ?></option>
                    <?php foreach (KunenaMultilanguage::getLanguages() as $languageCode => $languageTitle) : ?>
                        <option value="<?php echo $this->escape($languageCode); ?>"<?php echo $this->filters['language'] === $languageCode ? ' selected' : ''; ?>><?php echo $this->escape($languageTitle); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="col-6 col-md-2">
            <input type="search" name="fauthor" value="<?php echo $this->escape($this->filters['author']); ?>" class="form-control form-control-sm"
                   placeholder="<?php echo Text::_('COM_KUNENA_FILTER_AUTHOR'); ?>" aria-label="<?php echo Text::_('COM_KUNENA_FILTER_AUTHOR'); ?>" autocomplete="off"/>
        </div>
        <div class="col-6 col-md-2 text-end">
            <button type="submit" class="btn btn-sm btn-outline-primary"><?php echo Text::_('COM_KUNENA_FILTER_APPLY'); ?></button>
            <a href="<?php echo $this->escape($filterResetUrl); ?>" class="btn btn-sm btn-outline-secondary" data-ktopics-reset><?php echo Text::_('COM_KUNENA_FILTER_RESET'); ?></a>
        </div>
    </form>
<?php endif; ?>

<div class="agora-list-toolbar">
    <div data-ktopics-pagination>
        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination->setDisplayedPages(4))
            ->set('display', true); ?>
    </div>
    <div class="agora-list-search">
        <?php echo $this->subLayout('Widget/Search')
            ->set('catid', 'all')
            ->setLayout('topic'); ?>
    </div>
</div>

<div class="kfrontend agora-topics">
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post"
          name="ktopicsform"
          id="ktopicsform">
        <?php echo HTMLHelper::_('form.token'); ?>
        <?php if ($view == 'user') : ?>
            <input type="hidden" name="userid" value="<?php echo $this->user->userid; ?>"/>
        <?php endif; ?>
        <table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?>">
            <thead>
            <tr>
                <th scope="col" class="center d-none d-md-table-cell">
                    <a id="forumtop"> </a>
                    <a href="#forumbottom" rel="nofollow" aria-label="<?php echo Text::_('COM_KUNENA_GEN_GOTOBOTTOM'); ?>">
                        <?php echo KunenaIcons::arrowdown(); ?>
                    </a>
                </th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>
                    / <?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?></th>

                <?php if (!empty($this->actions)) : ?>
                    <th scope="col" class="center"><label>
                            <input class="kcheckall" type="checkbox" name="toggle" value=""/>
                        </label>
                    </th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody class="topic-list">
            <?php if (empty($this->topics) && empty($this->subcategories)) : ?>
                <tr>
                    <th scope="col" colspan="4" class="text-center"><?php echo Text::_('COM_KUNENA_VIEW_NO_TOPICS') ?></th>
                </tr>
            <?php else : ?>
                <?php $counter = 2; ?>

                <?php foreach ($this->topics as $i => $topic) {
                    echo $this->subLayout('Topic/Row')
                        ->set('topic', $topic)
                        ->set('position', 'kunena_topic_' . $i)
                        ->set('checkbox', !empty($this->actions));

                    if ($this->ktemplate->params->get('displayModule')) {
                        echo $this->subLayout('Widget/Module')
                            ->set('position', 'kunena_topic_' . $counter++)
                            ->set('cols', $cols)
                            ->setLayout('table_row');
                    }
                } ?>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="agora-topics-foot">
            <a id="forumbottom"> </a>
            <a href="#forumtop" rel="nofollow" class="agora-topics-top" aria-label="<?php echo Text::_('COM_KUNENA_GEN_GOTOTOP'); ?>">
                <?php echo KunenaIcons::arrowup(); ?>
            </a>

            <?php if (!empty($this->topics) && !empty($this->moreUri)) {
                echo HTMLHelper::_('kunenaforum.link', $this->moreUri, Text::_('COM_KUNENA_MORE'), null, 'btn btn-outline-primary', 'follow');
            } ?>

            <?php if (!empty($this->actions)) : ?>
                <div class="agora-topics-bulk">
                    <?php echo HTMLHelper::_('select.genericlist', $this->actions, 'task', 'class="form-select kchecktask" ', 'value', 'text', 0, 'kchecktask'); ?>
                    <?php if (isset($this->actions['move'])) :
                        $options = [HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_BULK_CHOOSE_DESTINATION'))];
                        echo HTMLHelper::_('kunenaforum.categorylist', 'target', 0, $options, [], 'class="form-select fbs" disabled="disabled"', 'value', 'text', 0, 'kchecktarget');
                    endif; ?>
                    <?php if (isset($this->actions['status'])) :
                        $statusOptions = [HTMLHelper::_('select.option', '-1', Text::_('COM_KUNENA_BULK_CHOOSE_STATUS'))];

                        foreach (KunenaFactory::getTemplate()->getUserTopicIcons() as $statusIcon) {
                            $statusOptions[] = HTMLHelper::_('select.option', (string) $statusIcon->id, Text::_($statusIcon->title));
                        }

                        echo HTMLHelper::_('select.genericlist', $statusOptions, 'status_icon', 'class="form-select" disabled="disabled"', 'value', 'text', '-1', 'kcheckstatus');
                    endif; ?>
                    <button type="submit" name="kcheckgo" class="btn btn-primary"><?php echo Text::_('COM_KUNENA_GO') ?></button>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="agora-list-toolbar" data-ktopics-pagination>
    <?php echo $this->subLayout('Widget/Pagination/List')
        ->set('pagination', $this->pagination->setDisplayedPages(4))
        ->set('display', true); ?>
</div>

