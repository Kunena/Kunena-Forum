<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Framework
 * @subpackage Icons
 *
 * @copyright  Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Class KunenaIcons
 *
 * @since 5.0
 */
class KunenaIcons
{
	/**
	 * Return the arrow down icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function arrowdown()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-down');
	}

	/**
	 * Return the arrow up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function arrowup()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-up');
	}

	/**
	 * Return the arrow down icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function arrowdownanchor()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon kforumtop"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-down');
	}

	/**
	 * Return the arrow up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function arrowupanchor()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon kforumbottom"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-up');
	}

	/**
	 * Return the chevron right icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function chevronright()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chevron-right" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-chevron-right" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-right');
	}

	/**
	 * Return the members icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function members()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-users fa-3x" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-user icon-big" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user glyphicon-super" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('people');
	}

	/**
	 * Return the user icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function user()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-user-circle" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-user" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('person');
	}

	/**
	 * Return the lock icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function lock()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-lock" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-lock" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('lock');
	}

	/**
	 * Return the star icon (secret key)
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function star()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-star" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-star" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon kfavoritestar ksmall" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('star');
	}

	/**
	 * Return the shield icon (reviewed)
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function shield()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-shield-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-eye-open" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('eye-slash');
	}

	/**
	 * Return the flag icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function flag()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-flag" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-flag" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-flag" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicmy ksmall"></span>';
		}

		return KunenaSvgIcons::loadsvg('flag');
	}

	/**
	 * Return the poll icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function poll()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chart-bar" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-bars" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('kanban');
	}

	/**
	 * Return the stats icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function stats()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chart-bar fa-3x" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-bars icon-big" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats glyphicon-super" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('bar-chart');
	}

	/**
	 * Return the search icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function search()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-search" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-search" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('search');
	}

	/**
	 * Return the collapse icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function collapse()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-compress" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-compress" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrows-collapse');
	}

	/**
	 * Return the clock icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function clock()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-clock" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-clock" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('clock');
	}

	/**
	 * Return the thumbs-up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function thumbsup()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-thumbs-up" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-thumbs-up" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('heart-fill');
	}

	/**
	 * Return the secure icon
	 *
	 * @return string
	 *
	 * @since K6.0
	 * @throws Exception
	 */
	public static function secure()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-shield-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-shield" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-shield" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('shield');
	}

	/**
	 * Return the cancel icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function cancel()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-times" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-remove" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('x-circle');
	}

	/**
	 * Return the ip icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function ip()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if (!KunenaUserHelper::getMyself()->isModerator())
		{
			return false;
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-compass" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-compass" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-compass" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('compass');
	}

	/**
	 * Return the email icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function email()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-envelope" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-envelope" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-email" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('envelope');
	}

	/**
	 * Return the bookmark icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function bookmark()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bookmark" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-bookmark" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('bookmark');
	}

	/**
	 * Return the back icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function back()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-angle-left" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-arrow-left" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-left');
	}

	/**
	 * Return the save icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function save()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-save" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-save" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-save" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('check-circle');
	}

	/**
	 * Return the edit icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function edit()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-edit" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-edit" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('pencil');
	}

	/**
	 * Return the pencel icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function pencil()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-pencil-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-pencil-2" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('pencil');
	}

	/**
	 * Return the attach icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function attach()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-paperclip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-flag-2 icon-white" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicattach ksmall"></span>';
		}

		return KunenaSvgIcons::loadsvg('images');
	}

	/**
	 * Return the plus icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function plus()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-plus" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the rss icon
	 *
	 * @param   null  $text  text
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function rss($text = null)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$class         = ' ' . KunenaTemplate::getInstance()->tooltips();

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-rss ' . $class . '" title="' . Text::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-feed ' . $class . '" title="' . Text::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-list-alt ' . $class . '" title="' . Text::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('rss');
	}

	/**
	 * Return the upload icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function upload()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-upload" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-upload" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('upload');
	}

	/**
	 * Return the picture icon
	 *
	 * @param   bool  $big
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function picture($big = false)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($big)
		{
			$big = ' icon-big';
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-image fa-3x' . $big . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-picture' . $big . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-picture' . $big . '" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('image');
	}

	/**
	 * Return the file icon
	 *
	 * @param   bool  $big
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function file($big = false)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($big)
		{
			$big = ' icon-big';
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-file fa-3x' . $big . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-file' . $big . '" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-file' . $big . '" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('document-richtext');
	}

	/**
	 * Return the delete icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function delete()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-trash-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-trash" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-trash" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('trash');
	}

	/**
	 * Return the poll add icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function poll_add()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<span id="kbutton-poll-add"><i class="fa fa-plus btn btn-xs btn-small btn-default"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i></span>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-add" class="icon icon-plus btn btn-small"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-add" class="glyphicon glyphicon-plus btn btn-xs btn-default"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}

		return KunenaSvgIcons::loadsvg('pie-chart-fill');
	}

	/**
	 * Return the poll rem icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function poll_rem()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<span id="kbutton-poll-rem"><i class="fa fa-minus btn btn-xs btn-small btn-default"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i></span>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-rem" class="icon icon-minus btn btn-small"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-rem" class="glyphicon glyphicon-minus btn btn-xs btn-default"
						alt="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}

		return KunenaSvgIcons::loadsvg('bar-chart');
	}

	/**
	 * Return the undo icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function undo()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-reply" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-undo" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-reply" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-clockwise');
	}

	/**
	 * Return the shuffle icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function shuffle()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-random" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-shuffle" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-random" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('arrow-repeat');
	}

	/**
	 * Return the category icon
	 *
	 * @param   string  $categoryicon  icon
	 * @param   null    $new           new
	 * @param   bool    $big           big
	 *
	 * @return string
	 * @since K5.0
	 * @throws Exception
	 */
	public static function caticon($categoryicon, $new = null, $big = true)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$caticon       = $ktemplate->params->get('DefaultCategoryicon');

		if ($categoryicon == ' ')
		{
			$categoryicon = $caticon;
		}

		if ($new)
		{
			$newchar = 'knewchar';
		}
		else
		{
			$newchar = '';
		}

		if ($big)
		{
			if ($topicicontype == 'fa')
			{
				if (!empty($caticon))
				{
					$bigicon = ' fa-3x ';
				}
				else
				{
					$bigicon = ' fa-3x fa-folder-open ';
				}

				if ($categoryicon)
				{
					$bigicon = ' fa-3x ';
				}
			}
			elseif ($topicicontype == 'B2')
			{
				if (!empty($caticon))
				{
					$bigicon = ' icon-big ';
				}
				else
				{
					$bigicon = ' icon-big icon-folder ';
				}

				if ($categoryicon)
				{
					$bigicon = ' icon-big ';
				}
			}
			elseif ($topicicontype == 'B3')
			{
				if (!empty($caticon))
				{
					$bigicon = ' glyphicon-big ';
				}
				else
				{
					$bigicon = ' glyphicon-big glyphicon-folder-open ';
				}

				if ($categoryicon)
				{
					$bigicon = ' glyphicon-big ';
				}
			}
			elseif ($topicicontype == 'image')
			{
				if (!empty($caticon))
				{
					$bigicon = ' kicon-foldernew ';
				}
				else
				{
					$bigicon = ' kicon-folder ';
				}

				if ($categoryicon)
				{
					$bigicon = ' icon-big ';
				}
			}
			else
			{
				$bigicon = ' icon-big ';
			}
		}
		else
		{
			$bigicon = ' ';

			if ($topicicontype == 'fa')
			{
				if (!$categoryicon)
				{
					$bigicon = 'fa-folder-open ';
				}
			}
			elseif ($topicicontype == 'B2')
			{
				if (!$categoryicon)
				{
					$bigicon = 'icon-folder ';
				}
			}

			if ($topicicontype == 'B3')
			{
				if (!$categoryicon)
				{
					$bigicon = 'glyphicon-folder-open ';
				}
			}
			elseif ($topicicontype == 'image')
			{
				if (!$categoryicon)
				{
					if ($new)
					{
						$bigicon = 'kicon-folder-sm-new ';
					}
					else
					{
						$bigicon = 'kicon-folder-sm ';
					}
				}
			}
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa ' . $categoryicon . $bigicon . $newchar . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon ' . $categoryicon . $bigicon . $newchar . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </span>';
		}

		if ($topicicontype == 'B4')
		{
			if (!$categoryicon)
			{
				if ($newchar)
				{
					return KunenaSvgIcons::loadsvg('folder-fill');
				}
				else
				{
					return KunenaSvgIcons::loadsvg('folder');
				}
			}
			else
			{
				$svg = @file_get_contents(Uri::root() . 'media/kunena/core/svg/' . $categoryicon . '.svg');

				if ($svg)
				{
					return KunenaSvgIcons::loadsvg($categoryicon);
				}
				else
				{
					return '<span class="icon ' . $categoryicon . $bigicon . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"></span>';
				}
			}
		}

		if ($topicicontype == 'image')
		{
			return '<span class="' . $categoryicon . $bigicon . $newchar . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </span>';
		}

		return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
	}

	/**
	 * Return the home icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function home()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-home hasTooltip" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<span class="icon icon-home hasTooltip" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-home hasTooltip" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('house');
	}

	/**
	 * Return the calendar icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function calendar()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-calendar-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-calendar" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('calendar');
	}

	/**
	 * Return the hamburger icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function hamburger()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-bars" aria-hidden="true"></i> <b class="caret"></b>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon-large icon-list" aria-hidden="true"></i> <b class="caret"></b>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-large glyphicon-menu-hamburger" aria-hidden="true"></span> <b class="caret"></b>';
		}

		return KunenaSvgIcons::loadsvg('three-dots-vertical');
	}

	/**
	 * Return the info icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function info()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-info-circle" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-info" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('alert-circle');
	}

	/**
	 * Return the online icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function online()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus green" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon-plus green" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus green" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the away icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function away()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus yellow" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon-plus yellow" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus yellow" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the busy icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function busy()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus red" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon-minus red" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus red" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('dash');
	}

	/**
	 * Return the invisible icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function invisible()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus grey" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon-minus grey" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus grey" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('dash');
	}

	/**
	 * Return the cog icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function cog()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-cog" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-cog" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('gear');
	}

	/**
	 * Return the drawer icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function drawer()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-inbox" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-drawer" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('inbox');
	}

	/**
	 * Return the out icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function out()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-sign-out-alt" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-out" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('power');
	}

	/**
	 * Return the grid icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function grid()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-th" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-grid-view-2" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-th" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('grid');
	}

	/**
	 * Return the globe icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function globe()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-globe" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-link" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-website" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('window');
	}

	/**
	 * Return the location icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function location()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-map-marker" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-location" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-location" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('geo');
	}

	/**
	 * Return the pm icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function pm()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-comments" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-comments-2" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-pm" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('chat');
	}

	/**
	 * Return the report icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function report()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-exclamation" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B2')
		{
			return '<i class="icon icon-flag" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-report" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('alert-octagon');
	}

	/**
	 * Return the report icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 * @throws Exception
	 */
	public static function reportname()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'B2')
		{
			return 'icon icon-flag';
		}

		if ($topicicontype == 'B3')
		{
			return 'glyphicon glyphicon-exclamation-sign';
		}

		if ($topicicontype == 'B4')
		{
			return 'alert-circle';
		}

		if ($topicicontype == 'fa')
		{
			return 'fa fa-exclamation';
		}

		if ($topicicontype == 'image')
		{
			return 'kicon-report';
		}

		return '';
	}
}
