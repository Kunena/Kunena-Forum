<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Route\KunenaRoute;

if ($this->category->allowRatings) :
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->registerAndUseStyle('kunena.rating', 'media/kunena/core/css/rating.css')
        ->registerAndUseScript('krating', 'media/kunena/core/js/krating.js');

    // The texts of the stars and of the answers of the server (the keys of KunenaRate::save()).
    foreach (
        [
            'COM_KUNENA_RATING_LABEL', 'COM_KUNENA_RATING_STAR', 'COM_KUNENA_RATING_WARNING_LABEL', 'COM_KUNENA_RATE_LOGIN',
            'COM_KUNENA_RATE_NOT_YOURSELF', 'COM_KUNENA_RATE_NOT_ALLOWED_WHEN_BANNED', 'COM_KUNENA_RATE_ALLREADY',
            'COM_KUNENA_RATE_SUCCESSFULLY_SAVED',
        ] as $key
    ) {
        Text::script($key);
    } ?>

    <input id="topic_id" type="hidden" value="<?php echo $this->topic->id; ?>"/>
    <input id="krating-catid" type="hidden" value="<?php echo $this->category->id; ?>"/>
    <input type="hidden" id="krating_url" name="krating_url"
           value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loadrate&format=json&' . Session::getFormToken() . '=1'); ?>"/>
    <input type="hidden" id="krating_submit_url" name="url"
           value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=setrate&topic_id=' . $this->topic->id . '&format=json&' . Session::getFormToken() . '=1'); ?>"/>
    <span id="krating"
         data-bs-toggle="tooltip" title="<?php echo Text::sprintf('COM_KUNENA_RATE_TOOLTIP', $this->topic->rating, $this->topic->getReviewCount()); ?>"
         class="hasTooltip">
        <span class="unseen element-invisible"></span>
    </span>
<?php endif;
