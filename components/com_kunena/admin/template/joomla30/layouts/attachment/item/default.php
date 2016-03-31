<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Layouts.Attachment
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAttachment $attachment */
$attachment = $this->attachment;
$config = KunenaFactory::getConfig();

if ($config->access_component)
{
?>
<a href="<?php echo $attachment->getUrl(); ?>" title="<?php echo $attachment->getFilename(); ?>">
	<?php if ($attachment->isImage())
	{
		echo '<img src="' . JUri::root() . $attachment->getUrl(true) . ' " height="40" width="40" />';
	}
	else
	{
		echo '<i class="icon-flag-2 icon-big"></i>';
	}
	?>
</a>
<?php
}
else
{
	if ($attachment->isImage())
	{
		echo '<i class="icon-picture icon-big"></i>';
	}
	else
	{
		echo '<i class="icon-flag-2 icon-big"></i>';
	}
} ?>
