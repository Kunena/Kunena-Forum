<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$attachment = $this->attachment;
?>

<a href="<?php echo $attachment->getUrl(); ?>" title="<?php echo $attachment->getFilename(); ?>">
	<?php echo $this->escape($attachment->getShortName()); ?>
</a>
