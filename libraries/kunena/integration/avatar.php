<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaAvatar
 */
class KunenaAvatar
{
	public $avatarSizes = null;
	protected $resize = false;

	protected static $instance = false;

	static public function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JDispatcher::getInstance();
			$classes = $dispatcher->trigger('onKunenaGetAvatar');

			foreach ($classes as $class)
			{
				if (!is_object($class))
				{
					continue;
				}

				self::$instance = $class;
				break;
			}

			if (!self::$instance)
			{
				self::$instance = new KunenaAvatar();
			}
		}

		return self::$instance;
	}

	public function load($userlist)
	{

	}

	public function getEditURL()
	{
		return '';
	}

	protected function _getURL($user, $sizex, $sizey)
	{
		return '';
	}

	public function getSize($sizex = 90, $sizey = 90)
	{
		$size = new StdClass();
		$size->x = intval($sizex);
		$size->y = intval($sizey);

		if (!intval($sizex))
		{
			$template = KunenaFactory::getTemplate();
			$name = ucfirst(strtolower($sizex));
			$size->x = intval($template->params->get('avatarSizeX'.$name, 90));
			$size->y = intval($template->params->get('avatarSizeY'.$name, 90));
		}

		return $size;
	}

	public function getURL($user, $sizex = 90, $sizey = 90)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$size = $this->getSize($sizex, $sizey);

		if (!$size->x || !$size->y)
		{
			return;
		}

		$result = $this->_getURL($user, $size->x, $size->y);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $result;
	}

	public function getLink($user, $class='', $sizex = 90, $sizey = 90)
	{
		$size = $this->getSize($sizex, $sizey);
		$avatar = $this->getURL($user, $size->x, $size->y);

		if (!$avatar)
		{
			return;
		}

		if ($class)
		{
			$class=' class="'.$class.'"';
		}

		// Style is needed to resize avatar for JomSocial and other integration that do not have automatic resizing
		if (!$this->resize)
		{
			$style = 'style="max-width: '.$size->x.'px; max-height: '.$size->y.'px"';
		}
		else $style = '';

		$link = '<img'.$class.' src="'.$avatar.'" alt="'.JText::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()).'" '.$style.' />';

		return $link;
	}
}
