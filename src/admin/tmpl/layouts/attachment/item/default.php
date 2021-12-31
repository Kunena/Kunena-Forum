<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Layouts.Attachment
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

$attachment = $this->attachment;
$config     = KunenaFactory::getConfig();

if ($config->attachmentProtection)
{
	$url = $attachment->getUrl(true);
	$src = $attachment->getUrl();
}
else
{
	$url = Uri::root() . $attachment->getUrl();
	$src = $url;
}

if ($config->accessComponent)
{
	?>
	<a href="<?php echo $url; ?>" title="<?php echo $attachment->getFilename(); ?>">
		<?php
		if ($attachment->isImage())
	{
			echo '<img loading=lazy src="' . $src . ' " height="40" width="40" />';
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
}
