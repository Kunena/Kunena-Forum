<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Framework
 * @subpackage Icons
 *
 * @copyright  Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
			return '<i class="fa fa-arrow-down hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-down hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-down hasTooltip"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-down hasTooltip"></span>';
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
			return '<i class="fa fa-arrow-up hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-arrow-up hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-up hasTooltip"></span>';
		}
		else
		{
			return '<span class="icon icon-arrow-up hasTooltip"></span>';
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
			return '<span class="icon icon-chevron-right"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-chevron-right"></span>';
		}
		else
		{
			return '<span class="icon icon-chevron-right"></span>';
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
			return '<span class="icon icon-user icon-big"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user glyphicon-super"></span>';
		}
		else
		{
			return '<span class="icon icon-user icon-big"></span>';
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
			return '<span class="icon icon-user"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-user"></span>';
		}
		else
		{
			return '<span class="icon icon-user"></span>';
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
			return '<span class="icon icon-lock"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-lock"></span>';
		}
		else
		{
			return '<span class="icon icon-lock"></span>';
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
			return '<span class="icon icon-star"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-star"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon kfavoritestar ksmall"></span>';
		}
		else
		{
			return '<span class="icon icon-star"></span>';
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
			return '<span class="icon icon-eye-open"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-eye-open"></span>';
		}
		else
		{
			return '<span class="icon icon-eye-open"></span>';
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
			return '<span class="icon icon-flag"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-flag"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicmy ksmall"></span>';
		}
		else
		{
			return '<span class="icon icon-flag"></span>';
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
			return '<span class="icon icon-bars"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats"></span>';
		}
		else
		{
			return '<span class="icon icon-bars"></span>';
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
			return '<span class="icon icon-bars icon-big"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-stats glyphicon-super"></span>';
		}
		else
		{
			return '<span class="icon icon-bars icon-big"></span>';
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
			return '<span class="icon icon-search"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-search"></span>';
		}
		else
		{
			return '<span class="icon icon-search"></span>';
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
			return '<i class="fa fa-clock-o"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-clock"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-time"></span>';
		}
		else
		{
			return '<i class="icon icon-clock"></i>';
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
			return '<i class="fa fa-thumbs-o-up"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-thumbs-up"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-thumbs-up"></span>';
		}
		else
		{
			return '<i class="icon icon-thumbs-up"></i>';
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
			return '<i class="fa fa-times"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-remove"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-remove"></span>';
		}
		else
		{
			return '<i class="icon icon-remove"></i>';
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
			return '<i class="fa fa-compass"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-compass"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-compass"></span>';
		}
		else
		{
			return '<i class="icon icon-compass"></i>';
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
			return '<i class="fa fa-envelope"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-envelope"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-envelope"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-email"></span>';
		}
		else
		{
			return '<i class="icon icon-envelope"></i>';
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
			return '<i class="fa fa-bookmark"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-bookmark"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-bookmark"></span>';
		}
		else
		{
			return '<i class="icon icon-bookmark"></i>';
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
			return '<i class="fa fa-angle-left"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-arrow-left"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-arrow-left"></span>';
		}
		else
		{
			return '<i class="icon icon-arrow-left"></i>';
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
			return '<i class="fa fa-save"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-save"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-save"></span>';
		}
		else
		{
			return '<i class="icon icon-save"></i>';
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
			return '<i class="fa fa-edit"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-edit"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-edit"></span>';
		}
		else
		{
			return '<i class="icon icon-edit"></i>';
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
			return '<i class="fa fa-pencil"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-pencil-2"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-pencil"></span>';
		}
		else
		{
			return '<i class="icon icon-pencil-2"></i>';
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
			return '<i class="fa fa-paperclip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-flag-2 icon-white"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-paperclip"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon ktopicattach ksmall"></span>';
		}
		else
		{
			return '<i class="icon icon-flag-2 icon-white"></i>';
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
			return '<i class="fa fa-plus"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-plus"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus"></span>';
		}
		else
		{
			return '<i class="icon icon-plus"></i>';
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
			return '<i class="fa fa-rss ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-feed ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-list-alt ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '"></span>';
		}
		else
		{
			return '<i class="icon icon-feed ' . $class . '" title="' . JText::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '"></i>';
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
			return '<i class="fa fa-upload"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-upload"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-upload"></span>';
		}
		else
		{
			return '<i class="icon icon-upload"></i>';
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
			return '<i class="large-kicon fa fa-picture-o"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-picture"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-picture"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-picture"></i>';
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
			return '<i class="large-kicon fa fa-file-o"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-file"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-file"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-file"></i>';
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
			return '<i class="large-kicon fa fa-trash"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="large-kicon icon icon-trash"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="large-kicon glyphicon glyphicon-trash"></span>';
		}
		else
		{
			return '<i class="large-kicon icon icon-trash"></i>';
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
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-add" class="icon icon-plus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-add" class="glyphicon glyphicon-plus btn btn-xs btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		else
		{
			return '<i id="kbutton-poll-add" class="icon icon-plus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
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
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i id="kbutton-poll-rem" class="icon icon-minus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<i id="kbutton-poll-rem" class="glyphicon glyphicon-minus btn btn-xs btn-default"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
		}
		else
		{
			return '<i id="kbutton-poll-rem" class="icon icon-minus btn btn-small"
						alt="' . JText::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '"> </i>';
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
			return '<i class="fa fa-reply"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-undo"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-share-alt"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-reply"></span>';
		}
		else
		{
			return '<i class="icon icon-undo"></i>';
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
			return '<i class="fa fa-random"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-shuffle"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-random"></span>';
		}
		else
		{
			return '<i class="icon icon-shuffle"></i>';
		}
	}

	/**
	 * Return the category icon
	 *
	 * @return string
	 *
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
			return '<i class="fa ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"> </i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"> </i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"> </span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"> </span>';
		}
		else
		{
			return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '"> </i>';
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
			return '<i class="fa fa-home hasTooltip"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<span class="icon icon-home hasTooltip"></span>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-home hasTooltip"></span>';
		}
		else
		{
			return '<span class="icon icon-home hasTooltip"></span>';
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
			return '<i class="fa fa-calendar"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-calendar"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-calendar"></span>';
		}
		else
		{
			return '<i class="icon icon-calendar"></i>';
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
			return '<i class="fa fa-bars fa-large"></i> <b class="caret"></b>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-large icon-list"></i> <b class="caret"></b>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-large glyphicon-menu-hamburger"></span> <b class="caret"></b>';
		}
		else
		{
			return '<i class="icon-large icon-list"></i> <b class="caret"></b>';
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
			return '<i class="fa fa-info-circle"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-info"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-info-sign"></span>';
		}
		else
		{
			return '<i class="icon icon-info"></i>';
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
			return '<i class="fa fa-plus green"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-plus green"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus green"></span>';
		}
		else
		{
			return '<i class="icon-plus green"></i>';
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
			return '<i class="fa fa-plus yellow"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-plus yellow"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-plus yellow"></span>';
		}
		else
		{
			return '<i class="icon-plus yellow"></i>';
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
			return '<i class="fa fa-minus red"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-minus red"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus red"></span>';
		}
		else
		{
			return '<i class="icon-minus red"></i>';
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
			return '<i class="fa fa-minus grey"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon-minus grey"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-minus grey"></span>';
		}
		else
		{
			return '<i class="icon-minus grey"></i>';
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
			return '<i class="fa fa-cog"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-cog"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-cog"></span>';
		}
		else
		{
			return '<i class="icon icon-cog"></i>';
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
			return '<i class="fa fa-inbox"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-drawer"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-inbox"></span>';
		}
		else
		{
			return '<i class="icon icon-drawer"></i>';
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
			return '<i class="fa fa-sign-out"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-out"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-log-out"></span>';
		}
		else
		{
			return '<i class="icon icon-out"></i>';
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
			return '<i class="fa fa-th"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-grid-view-2"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-th"></span>';
		}
		else
		{
			return '<i class="icon icon-grid-view-2"></i>';
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
			return '<i class="fa fa-globe"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-link"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-globe"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-website"></span>';
		}
		else
		{
			return '<i class="icon icon-link"></i>';
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
			return '<i class="fa fa-map-marker"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-location"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-map-marker"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-location"></span>';
		}
		else
		{
			return '<i class="icon icon-location"></i>';
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
			return '<i class="fa fa-comments-o"></i>';
		}
		elseif ($topicicontype == 'B2')
		{
			return '<i class="icon icon-comments-2"></i>';
		}
		elseif ($topicicontype == 'B3')
		{
			return '<span class="glyphicon glyphicon-comment"></span>';
		}
		elseif ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-pm"></span>';
		}
		else
		{
			return '<i class="icon icon-comments-2"></i>';
		}
	}
}
