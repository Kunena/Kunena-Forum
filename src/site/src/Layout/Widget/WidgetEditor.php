<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K4.0
 */
class WidgetEditor extends KunenaLayout
{
	/**
	 * Check if user is able to have images and links buttons in the editor
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena 5.2.0
	 */
	public function getAllowedtoUseLinksImages()
	{
		$this->ktemplate  = KunenaFactory::getTemplate();
		$templatesettings = $this->ktemplate->params;
		$config           = KunenaConfig::getInstance();
		$me               = KunenaUserHelper::getMyself();

		if ($me->checkUserAllowedLinksImages())
		{
			$this->addScriptOptions('com_kunena.ckeditor_remove_buttons_url_image', $config->new_users_prevent_post_url_images);
			$editorbuttons = $templatesettings->get('editorButtons');

			if (empty($editorbuttons))
			{
				$templatesettings->set('editorButtons', 'Image,Link,Unlink');
			}
			else
			{
				if (strstr($editorbuttons, 'Image') !== false && strstr($editorbuttons, 'Link,Unlink') === false)
				{
					$editorbuttons .= ',Link,Unlink';
					$templatesettings->set('editorButtons', $editorbuttons);
				}
				elseif (strstr($editorbuttons, 'Link,Unlink') !== false && strstr($editorbuttons, 'Image') == false)
				{
					$editorbuttons .= ',Image';
					$templatesettings->set('editorButtons', $editorbuttons);
				}
				else
				{
					$editorbuttons .= ',Link,Unlink,Image';
					$templatesettings->set('editorButtons', $editorbuttons);
				}
			}
		}
	}
}
