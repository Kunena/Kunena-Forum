<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$attachment = $this->attachment;
$location   = $attachment->getUrl();

if (!$attachment->isAudio())
{
	return;
}
?>
<div class="clearfix"></div>

<audio src="<?php echo $location; ?>" controls>
	Your browser does not support the <code>audio</code> element.
</audio>
<p><?php echo $attachment->getShortName(); ?> <a href="<?php echo $location; ?>" title="Download" download> <i
				class="icon icon-download"></i></a></p>
<div class="clearfix"></div>
