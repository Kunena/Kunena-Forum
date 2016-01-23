<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Layouts.Attachment
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAttachment $attachment */
$attachment = $this->attachment;

if ($attachment->protected)
{
	$url_href = $attachment->getUrl();
}
else
{
	$url_href = JUri::root() . $attachment->getUrl();
}

?>
<a href="<?php echo $url_href; ?>" title="<?php echo $attachment->getFilename(); ?>">
	<?php
	if ($attachment->isImage() && !$attachment->protected)
	{
		echo '<img src="' . JUri::root() . $attachment->getUrl(true) . ' " height="40" width="40" />';
	}
	elseif ($attachment->isImage() && $attachment->protected)
	{
		echo '<img src="' . $attachment->getUrl(true) . ' " height="40" width="40" />';
	}
	else
	{
		echo '<i class="icon-flag-2 icon-big"></i>';
	}
	?>
</a>
