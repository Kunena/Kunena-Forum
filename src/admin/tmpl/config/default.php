<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Config
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$this->getDocument()->getWebAssetManager()
    ->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('showon');

Text::script('COM_KUNENA_CONFIG_UNSAVED_LEAVE');

// The sections are the tabs of config.xml; each sub-fieldset of a tab is a group of options.
$sections = [];

foreach ($this->form->getXml()->xpath('//fieldset[not(ancestor::fieldset)]') as $fieldset) {
    $name = (string) $fieldset['name'];

    if ($name === 'permissions' && !$this->canAdmin) {
        continue;
    }

    [$label, $icon, $desc] = ConfigHelper::SECTIONS[$name] ?? [(string) $fieldset['label'], 'gear', (string) $fieldset['description']];

    $groups = [];

    foreach ($fieldset->xpath('fieldset') ?: [$fieldset] as $group) {
        $groups[] = [
            'name'   => (string) $group['name'],
            'label'  => $group === $fieldset ? '' : (string) $group['label'],
            'desc'   => $group === $fieldset ? '' : (string) $group['description'],
            'showon' => (string) $group['showon'],
        ];
    }

    $sections[$name] = ['label' => $label, 'icon' => $icon, 'desc' => $desc, 'groups' => $groups];
}

$first = array_key_first($sections);

?>

<form action="<?php echo Route::_('index.php?option=com_kunena&view=config'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" autocomplete="off">
    <div id="kunena" class="kd">
        <div class="kd-config" id="kd-config" data-first="<?php echo $first; ?>">
            <aside class="kd-config__aside">
                <label class="kd-search">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <span class="visually-hidden"><?php echo Text::_('COM_KUNENA_CPANEL_SETTINGS_FILTER'); ?></span>
                    <input type="search" id="kd-config-search" placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_CONFIG_SEARCH')); ?>" autocomplete="off">
                </label>

                <nav class="kd-config__nav" aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_CONFIG_SECTIONS')); ?>">
                    <?php foreach ($sections as $name => $section) : ?>
                        <a class="kd-config__link" href="#<?php echo $name; ?>" data-section="<?php echo $name; ?>">
                            <span class="kd-config__linkicon fa-solid fa-<?php echo $section['icon']; ?>" aria-hidden="true"></span>
                            <span class="kd-config__linktext"><?php echo Text::_($section['label']); ?></span>
                            <span class="kd-config__count" hidden></span>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <p class="kd-pill kd-pill--warn kd-config__dirty" id="kd-config-dirty" hidden>
                    <span class="fa-solid fa-circle-exclamation" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_CONFIG_UNSAVED'); ?>
                </p>
            </aside>

            <div class="kd-config__main">
                <p class="kd-config__empty" id="kd-config-empty" hidden>
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_CONFIG_NO_MATCH'); ?>
                </p>

                <?php foreach ($sections as $name => $section) : ?>
                    <section class="kd-config__section" id="kd-section-<?php echo $name; ?>" data-section="<?php echo $name; ?>" aria-labelledby="kd-section-<?php echo $name; ?>-title"<?php echo $name === $first ? '' : ' hidden'; ?>>
                        <header class="kd-config__head">
                            <span class="kd-config__headicon fa-solid fa-<?php echo $section['icon']; ?>" aria-hidden="true"></span>
                            <div>
                                <h2 id="kd-section-<?php echo $name; ?>-title" class="kd-config__title"><?php echo Text::_($section['label']); ?></h2>
                                <?php if ($section['desc']) : ?>
                                    <p class="kd-config__desc"><?php echo Text::_($section['desc']); ?></p>
                                <?php endif; ?>
                            </div>
                        </header>

                        <?php foreach ($section['groups'] as $group) :
                            $fields = $this->form->getFieldset($group['name']);

                            if (!$fields) {
                                continue;
                            }

                            $showon = $group['showon'] ? " data-showon='" . json_encode(FormHelper::parseShowOnConditions($group['showon'], 'jform'), JSON_HEX_APOS | JSON_HEX_AMP) . "'" : ''; ?>
                            <div class="kd-config__group"<?php echo $showon; ?>>
                                <?php if ($group['label'] && count($section['groups']) > 1) : ?>
                                    <h3 class="kd-config__grouptitle"><?php echo Text::_($group['label']); ?></h3>
                                <?php endif; ?>
                                <?php if ($group['desc']) : ?>
                                    <p class="kd-config__groupdesc"><?php echo Text::_($group['desc']); ?></p>
                                <?php endif; ?>
                                <?php foreach ($fields as $field) : ?>
                                    <?php echo $field->renderField(['class' => 'kd-row', 'descClass' => 'kd-row__desc']); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>

        <footer class="kd-footer">
            <?php echo KunenaVersion::getLongVersionHTML(); ?>
        </footer>
    </div>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="section" id="kd-config-section" value="<?php echo $first; ?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
    (function () {
        var root = document.getElementById('kd-config');
        var form = document.getElementById('adminForm');
        var search = document.getElementById('kd-config-search');
        var sectionInput = document.getElementById('kd-config-section');
        var empty = document.getElementById('kd-config-empty');
        var dirtyBadge = document.getElementById('kd-config-dirty');
        var sections = root.querySelectorAll('.kd-config__section');
        var links = root.querySelectorAll('.kd-config__link');
        var current = root.dataset.first;
        var dirty = false;
        var submitting = false;

        function show(name, updateHash) {
            if (!root.querySelector('.kd-config__section[data-section="' + name + '"]')) {
                name = root.dataset.first;
            }

            current = name;
            sectionInput.value = name;

            sections.forEach(function (section) {
                section.hidden = section.dataset.section !== name;
            });

            links.forEach(function (link) {
                var active = link.dataset.section === name;
                link.classList.toggle('is-active', active);

                if (active) {
                    link.setAttribute('aria-current', 'true');
                } else {
                    link.removeAttribute('aria-current');
                }
            });

            if (updateHash && history.replaceState) {
                history.replaceState(null, '', '#' + name);
            }
        }

        // Search: every option whose label or help text matches, in every section.
        function filter() {
            var query = search.value.trim().toLowerCase();

            root.classList.toggle('is-searching', query.length > 1);

            if (query.length < 2) {
                root.querySelectorAll('.kd-nomatch').forEach(function (element) {
                    element.classList.remove('kd-nomatch');
                });
                links.forEach(function (link) {
                    link.querySelector('.kd-config__count').hidden = true;
                });
                empty.hidden = true;
                show(current, false);

                return;
            }

            var total = 0;

            sections.forEach(function (section) {
                var inSection = 0;

                section.querySelectorAll('.kd-config__group').forEach(function (group) {
                    var inGroup = 0;

                    group.querySelectorAll('.kd-row').forEach(function (row) {
                        var match = row.textContent.toLowerCase().indexOf(query) > -1;
                        row.classList.toggle('kd-nomatch', !match);
                        inGroup += match ? 1 : 0;
                    });

                    group.classList.toggle('kd-nomatch', inGroup === 0);
                    inSection += inGroup;
                });

                section.hidden = inSection === 0;
                total += inSection;

                var count = root.querySelector('.kd-config__link[data-section="' + section.dataset.section + '"] .kd-config__count');
                count.textContent = inSection;
                count.hidden = inSection === 0;
            });

            empty.hidden = total > 0;
        }

        links.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                if (search.value) {
                    search.value = '';
                    filter();
                }

                show(link.dataset.section, true);
                root.querySelector('.kd-config__main').scrollIntoView({block: 'start', behavior: 'smooth'});
            });
        });

        search.addEventListener('input', filter);

        // An invalid option in a hidden section: open its section, so the message is next to it.
        form.addEventListener('invalid', function (event) {
            var section = event.target.closest('.kd-config__section');

            if (section && section.hidden && !search.value) {
                show(section.dataset.section, true);
            }
        }, true);

        // The limits of the fields (min, max, step, type): the validation of Joomla does not check them, and the save would
        // store the value as it is. Only the fields changed on this page are checked: a value saved before out of the limits
        // must not prevent the save of another option. A field hidden by showon is not checked either.
        var changed = new Set();

        ['input', 'change'].forEach(function (type) {
            form.addEventListener(type, function (event) {
                if (event.target !== search && event.target.willValidate) {
                    changed.add(event.target);
                }
            });
        });

        // Joomla (core.js) is deferred: it is there once the document is loaded.
        document.addEventListener('DOMContentLoaded', function () {
            var submitbutton = Joomla.submitbutton;

            Joomla.submitbutton = function (task, formSelector, validate) {
                if (task !== 'config.cancel') {
                    var invalid = Array.prototype.find.call(form.elements, function (element) {
                        return changed.has(element) && !element.checkValidity() && !element.closest('.kd-row.hidden');
                    });

                    if (invalid) {
                        search.value = '';
                        filter();
                        show(invalid.closest('.kd-config__section').dataset.section, true);
                        invalid.reportValidity();

                        return;
                    }
                }

                submitbutton(task, formSelector, validate);
            };
        });

        // The validation of Joomla (toolbar buttons) marks the fields with the class invalid: open the section of the first one.
        document.addEventListener('click', function (event) {
            if (!event.target.closest('joomla-toolbar-button')) {
                return;
            }

            setTimeout(function () {
                var invalid = form.querySelector('.kd-config__section .invalid');
                var section = invalid ? invalid.closest('.kd-config__section') : null;

                if (section && section.hidden) {
                    search.value = '';
                    filter();
                    show(section.dataset.section, true);
                }
            }, 0);
        });

        // Changes which are not saved yet.
        form.addEventListener('change', function (event) {
            if (event.target === search) {
                return;
            }

            dirty = true;
            dirtyBadge.hidden = false;
        });

        form.addEventListener('submit', function () {
            submitting = true;
        });

        window.addEventListener('beforeunload', function (event) {
            if (dirty && !submitting && form.task.value === '') {
                event.preventDefault();
                event.returnValue = Joomla.Text._('COM_KUNENA_CONFIG_UNSAVED_LEAVE');
            }
        });

        window.addEventListener('hashchange', function () {
            show(location.hash.substring(1), false);
        });

        show(location.hash.substring(1) || current, false);
    })();
</script>
