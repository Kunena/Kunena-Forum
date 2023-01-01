<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

$this->ktemplate = KunenaFactory::getTemplate();
$bootstrap = $this->ktemplate->params->get('bootstrap');

if ($bootstrap) {
    HTMLHelper::_('bootstrap.framework');
}

$tabs = $this->getTabs();
?>

<h1 class="float-start">
    <?php echo Text::sprintf('COM_KUNENA_USER_PROFILE', $this->escape($this->profile->getName())); ?>
</h1>

<h2 class="float-end">
    <?php if ($this->profile->isAuthorised('edit')) :
        ?>
        <?php echo $this->profile->getLink(
            KunenaIcons::edit() . ' ' . Text::_('COM_KUNENA_EDIT'),
            Text::_('COM_KUNENA_EDIT'),
            'nofollow',
            'edit',
            'btn'
        ); ?>
    <?php endif; ?>
</h2>

<?php
echo $this->subLayout('User/Item/Summary')
    ->set('profile', $this->profile)
    ->set('config', $this->config)
    ->set('candisplaymail', $this->candisplaymail)
    ->set('me', $this->me)
    ->set('private', $this->private)
    ->set('points', $this->points)
    ->set('medals', $this->medals)
    ->set('socials', $this->socials)
    ->set('avatar', $this->avatar)
    ->set('banInfo', $this->banInfo);
?>

<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_summary'); ?>

<div class="tabs">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php foreach ($tabs as $name => $tab) : ?>
            <li class="nav-item" role="presentation">
                <button <?php echo $tab->active ? ' class="nav-link active"' : ' class="nav-link"'; ?>
                        id="<?php echo $name; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $name; ?>"
                        type="button" role="tab"
                        aria-controls="home" aria-selected="true"><?php echo $tab->title; ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content" id="myTabContent">
        <?php foreach ($tabs as $name => $tab) : ?>
            <div class="tab-pane fade show <?php echo $tab->active ? ' in active show' : ''; ?>"
                 id="<?php echo $name; ?>" role="tabpanel" aria-labelledby="<?php echo $name; ?>-tab">
                <?php echo $tab->content; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="clearfix"></div>

