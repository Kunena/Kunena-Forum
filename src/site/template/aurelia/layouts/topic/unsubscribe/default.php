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

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$item   = $this->unsubscribe;
$prompts = [
    KunenaEmailUnsubscribe::TOPIC  => 'COM_KUNENA_EMAIL_UNSUBSCRIBE_TOPIC',
    KunenaEmailUnsubscribe::FOLLOW => 'COM_KUNENA_EMAIL_UNSUBSCRIBE_FOLLOW',
];
$prompt = $prompts[$item->type] ?? 'COM_KUNENA_EMAIL_UNSUBSCRIBE_CATEGORY';
?>

<h1><?php echo Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE'); ?></h1>

<form method="post" action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>" class="card card-body">
    <input type="hidden" name="task" value="unsubscribemail"/>
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($item->type, ENT_QUOTES, 'UTF-8'); ?>"/>
    <input type="hidden" name="id" value="<?php echo (int) $item->id; ?>"/>
    <input type="hidden" name="uid" value="<?php echo (int) $item->uid; ?>"/>
    <input type="hidden" name="sig" value="<?php echo htmlspecialchars($item->sig, ENT_QUOTES, 'UTF-8'); ?>"/>

    <p><?php echo Text::sprintf($prompt, '<strong>' . htmlspecialchars((string) $item->name, ENT_QUOTES, 'UTF-8') . '</strong>'); ?></p>

    <div>
        <button type="submit" class="btn btn-outline-primary"><?php echo Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE'); ?></button>
    </div>
</form>
