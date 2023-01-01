<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

$attachment = $this->attachment;
?>

<a href="<?php echo $attachment->getUrl(); ?>" data-bs-toggle="tooltip" title="<?php echo $attachment->getFilename(); ?>">
    <?php echo $this->escape($attachment->getShortName()); ?>
</a>
