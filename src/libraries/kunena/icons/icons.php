<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Framework
 * @subpackage Icons
 *
 * @copyright  Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die();


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
	 */
	static public function arrowdown()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the arrow up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function arrowup()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the arrow down icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function arrowdownanchor()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon kforumtop"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-down hasTooltip" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the arrow up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function arrowupanchor()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon kforumbottom"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-up hasTooltip" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the chevron right icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function chevronright()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chevron-right" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-chevron-right" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-chevron-right" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the members icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function members()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-user fa-big" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-user icon-big" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user glyphicon-super" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-user icon-big" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the user icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function user()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-user" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-user" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-user" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the lock icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function lock()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-lock" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-lock" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-lock" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the star icon (secret key)
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function star()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-star" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-star" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon kfavoritestar ksmall" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-star" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the shield icon (reviewed)
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function shield()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-shield" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-eye-open" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-eye-open" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the flag icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function flag()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-flag" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-flag" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-flag" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicmy ksmall" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-flag" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the poll icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function poll()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bar-chart" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-bars" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-bars" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the stats icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function stats()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bar-chart fa-big" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-bars icon-big" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats glyphicon-super" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-bars icon-big" aria-hidden="true"></span>';
		}
	}


	/**
	 * Return the search icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function search()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-search" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-search" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-search" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the collapse icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function collapse()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return 'fa';
		}
		elseif ($topicicontype == 'B2')
		{
			return 'b2';
		}
		elseif ($topicicontype == 'B3')
		{
			return 'b3';
		}
		else
		{
			return 'b3';
		}
	}

	/**
	 * Return the clock icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function clock()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-clock-o" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-clock" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-time" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-clock" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the thumbs-up icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function thumbsup()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-thumbs-up" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-thumbs-up" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the cancel icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function cancel()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-times" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-remove" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-remove" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the ip icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function ip()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if (!KunenaUserHelper::getMyself()->isModerator())
		{
			return;
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-compass" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-compass" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-compass" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-compass" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the email icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function email()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-envelope" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-envelope" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-email" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-envelope" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the bookmark icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function bookmark()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bookmark" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-bookmark" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-bookmark" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the back icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function back()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-angle-left" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-arrow-left" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-arrow-left" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the save icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function save()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-save" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-save" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-save" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-save" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the edit icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function edit()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-edit" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-edit" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-edit" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the pencel icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function pencil()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-pencil" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-pencil-2" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-pencil-2" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the attach icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function attach()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-paperclip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-flag-2 icon-white" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicattach ksmall" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-flag-2 icon-white" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the plus icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function plus()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-plus" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-plus" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the rss icon
	 *
	 * @param   null $text
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function rss($text = null)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$class = ' ' . KunenaTemplate::getInstance()->tooltips();

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-rss ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-feed ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-list-alt ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-feed ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the upload icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function upload()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-upload" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-upload" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-upload" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-upload" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the picture icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function picture()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="large-kicon fa fa-picture-o" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-picture" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-picture" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-picture" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the file icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function file()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="large-kicon fa fa-file-o" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-file" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-file" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-file" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the delete icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function delete()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="large-kicon fa fa-trash" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-trash" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-trash" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-trash" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the poll add icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function poll_add()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i id="kbutton-poll-add" class="fa fa-plus btn btn-xs btn-small btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-add" class="icon icon-plus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-add" class="glyphicon glyphicon-plus btn btn-xs btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		else
		{
			return '<i id="kbutton-poll-add" class="icon icon-plus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
	}

	/**
	 * Return the poll rem icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function poll_rem()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i id="kbutton-poll-rem" class="fa fa-minus btn btn-xs btn-small btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-rem" class="icon icon-minus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-rem" class="glyphicon glyphicon-minus btn btn-xs btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
		else
		{
			return '<i id="kbutton-poll-rem" class="icon icon-minus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i>';
		}
	}

	/**
	 * Return the undo icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function undo()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-reply" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-undo" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-reply" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-undo" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the shuffle icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function shuffle()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-random" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-shuffle" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-random" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-shuffle" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the category icon
	 *
	 * @param      $categoryicon
	 * @param null $new
	 * @param bool $big
	 *
	 * @return string
	 * @since K5.0
	 */
	static public function caticon($categoryicon, $new = null, $big = true)
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$caticon = $ktemplate->params->get('DefaultCategoryicon');

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
					$bigicon = ' fa-big ';
				}
				else
				{
					$bigicon = ' fa-big fa-folder-open ';
				}

				if ($categoryicon)
				{
					$bigicon = ' fa-big ';
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
			return '<i class="fa ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </span>';
		}
		else
		{
			return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}
	}

	/**
	 * Return the home icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function home()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-home hasTooltip" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-home hasTooltip" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-home hasTooltip" aria-hidden="true"></span>';
		}
		else
		{
			return '<span class="icon icon-home hasTooltip" aria-hidden="true"></span>';
		}
	}

	/**
	 * Return the calendar icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function calendar()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-calendar" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-calendar" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-calendar" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the hamburger icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function hamburger()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bars fa-large" aria-hidden="true"></i> <b class="caret"></b>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-large icon-list" aria-hidden="true"></i> <b class="caret"></b>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-large glyphicon-menu-hamburger" aria-hidden="true"></span> <b class="caret"></b>';
		}
		else
		{
			return '<i class="icon-large icon-list" aria-hidden="true"></i> <b class="caret"></b>';
		}
	}

	/**
	 * Return the info icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function info()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-info-circle" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-info" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-info" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the online icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function online()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus green" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-plus green" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus green" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon-plus green" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the away icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function away()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus yellow" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-plus yellow" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus yellow" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon-plus yellow" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the busy icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function busy()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus red" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-minus red" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus red" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon-minus red" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the invisible icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function invisible()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus grey" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-minus grey" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus grey" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon-minus grey" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the cog icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function cog()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-cog" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-cog" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-cog" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the drawer icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function drawer()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-inbox" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-drawer" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-inbox" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-drawer" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the out icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function out()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-sign-out" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-out" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-out" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the grid icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function grid()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-th" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-grid-view-2" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-th" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-grid-view-2" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the globe icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function globe()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-globe" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-link" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-website" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-link" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the location icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function location()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-map-marker" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-location" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-location" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-location" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the pm icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function pm()
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-comments-o" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-comments-2" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-comment" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-pm" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="icon icon-comments-2" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the report icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function report()
	{
		$ktemplate = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-exclamation" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-flag" aria-hidden="true"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-report" aria-hidden="true"></span>';
		}
		else
		{
			return '<i class="kicon-report" aria-hidden="true"></i>';
		}
	}

	/**
	 * Return the report icon
	 *
	 * @return string
	 *
	 * @since K5.0
	 */
	static public function reportname()
	{
		$ktemplate = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'B2')
		{
			return 'icon icon-flag';
		}
		elseif ($topicicontype == 'B3')
		{
			return 'glyphicon glyphicon-exclamation-sign';
		}
		elseif ($topicicontype == 'fa')
		{
			return 'fa fa-exclamation';
		}
		elseif ($topicicontype == 'image')
		{
			return 'kicon-report';
		}
		else
		{
			return '';
		}
	}
}
