<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$attachment = $this->attachment;
?>
<div id="results1" class="kmsgimage">
	<?php echo $this->subLayout('Attachment/Item')->set('attachment', $attachment); ?>
</div>
