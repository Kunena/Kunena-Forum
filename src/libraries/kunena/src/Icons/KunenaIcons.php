<?php
/**
 * Kunena Component
 *
 * @package    Kunena.Framework
 * @subpackage Icons
 *
 * @copyright  Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Icons;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaIcons
 *
 * @since   Kunena 5.0
 */
class KunenaIcons
{
	/**
	 * Return the arrow down icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function arrowdown(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('arrow-down');
	}

	/**
	 * Return the arrow up icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function arrowup(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('arrow-up');
	}

	/**
	 * Return the arrow down icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function arrowdownanchor(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-down hasTooltip" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function arrowupanchor(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-arrow-up hasTooltip" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function chevronright(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chevron-right" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('arrow-right');
	}

	/**
	 * Return the members icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function members(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-users fa-3x" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('people');
	}

	/**
	 * Return the user icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function user(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-user-circle" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('person');
	}

	/**
	 * Return the lock icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function lock(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-lock" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('lock');
	}

	/**
	 * Return the star icon (secret key)
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function star(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-star-fill" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon kfavoritestar ksmall" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('star-fill');
	}

	/**
	 * Return the star icon (secret key)
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function starOpen(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-star" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function shield(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-shield-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('shield');
	}

	/**
	 * Return the flag icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function flag(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-flag" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function poll(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chart-bar" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('kanban');
	}

	/**
	 * Return the stats icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function stats(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-chart-bar fa-3x" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('bar-chart');
	}

	/**
	 * Return the search icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function search(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-search" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('search');
	}

	/**
	 * Return the collapse icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function collapse(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-compress" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('arrows-collapse');
	}

	/**
	 * Return the clock icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function clock(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-clock" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('clock');
	}

	/**
	 * Return the thumbs-up icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function thumbsup(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-thumbs-up" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('heart-fill');
	}

	/**
	 * Return the secure icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function secure(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-shield-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('shield');
	}

	/**
	 * Return the cancel icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function cancel(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-times" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('x-circle');
	}

	/**
	 * Return the ip icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
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

		return KunenaSvgIcons::loadsvg('compass');
	}

	/**
	 * Return the email icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function email(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-envelope" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-email" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('envelope');
	}

	/**
	 * Return the email icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function emailOpen(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-envelope-open" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-profile kicon-profile-email" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('envelope-open');
	}

	/**
	 * Return the bookmark icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function bookmark(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-bookmark" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('bookmark');
	}

	/**
	 * Return the back icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function back(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-angle-left" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('arrow-left');
	}

	/**
	 * Return the save icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function save(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-save" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('check-circle');
	}

	/**
	 * Return the edit icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function edit(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-edit" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('pencil');
	}

	/**
	 * Return the pencil icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function pencil(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-pencil-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('pencil');
	}

	/**
	 * Return the attach icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function attach(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-paperclip" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function plus(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the rss icon
	 *
	 * @param   null  $text  text
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function rss($text = null): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$class         = ' ' . KunenaTemplate::getInstance()->tooltips();

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-rss ' . $class . '" title="' . Text::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS') . '" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('rss');
	}

	/**
	 * Return the upload icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function upload(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-upload" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('upload');
	}

	/**
	 * Return the picture icon
	 *
	 * @param   bool  $big  big
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function picture($big = false): string
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

		return KunenaSvgIcons::loadsvg('image');
	}

	/**
	 * Return the file icon
	 *
	 * @param   bool  $big  big
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function file($big = false): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($big)
		{
			$big = ' icon-big';
		}

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-file fa-2x' . $big . '" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('document-richtext');
	}

	/**
	 * Return the delete icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function delete(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-trash-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('trash');
	}

	/**
	 * Return the check icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function check(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-check" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('check');
	}

	/**
	 * Return the quote icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function quote(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-quote-left" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('document-text');
	}

	/**
	 * Return the poll add icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function poll_add(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<span id="kbutton-poll-add"><i class="fa fa-plus btn btn-xs btn-small btn-default"
						title="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i></span>';
		}

		return KunenaSvgIcons::loadsvg('pie-chart-fill');
	}

	/**
	 * Return the poll rem icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function poll_rem(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<span id="kbutton-poll-rem"><i class="fa fa-minus btn btn-xs btn-small btn-default"
						title="' . Text::_('COM_KUNENA_POLL_ADD_POLL_OPTION') . '" aria-hidden="true"> </i></span>';
		}

		return KunenaSvgIcons::loadsvg('bar-chart');
	}

	/**
	 * Return the undo icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function undo(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-reply" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function shuffle(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-random" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 5.0
	 */
	public static function caticon($categoryicon, $new = null, $big = true): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');
		$caticon       = $ktemplate->params->get('DefaultCategoryicon');
		$bigicon       = ' ';

		if ($categoryicon == ' ' || $categoryicon == null)
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
			elseif ($topicicontype == 'image')
			{
				if (empty($categoryicon))
				{
					if (!empty($caticon))
					{
						$bigicon = ' kicon-foldernew ';
					}
					else
					{
						$bigicon = ' kicon-folder ';
					}
				}
			}
			else
			{
				$bigicon = ' icon-big ';
			}
		}
		else
		{
			if ($topicicontype == 'fa')
			{
				if (!$categoryicon)
				{
					$bigicon = 'fa-folder-open ';
				}
			}

			if ($topicicontype == 'image')
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
			return '<i class="fa ' . $categoryicon . $bigicon . $newchar . '" title="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
		}

		if ($topicicontype == 'svg')
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
					return '<span class="icon ' . $categoryicon . $bigicon . '" title="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"></span>';
				}
			}
		}

		if ($topicicontype == 'image')
		{
			return '<div class="' . $categoryicon . $bigicon . $newchar . '" title="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </div>';
		}

		return '<i class="icon ' . $categoryicon . $bigicon . $newchar . '" title="' . Text::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" aria-hidden="true"> </i>';
	}

	/**
	 * Return the home icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function home(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-home hasTooltip" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('house');
	}

	/**
	 * Return the calendar icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function calendar(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-calendar-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('calendar');
	}

	/**
	 * Return the hamburger icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function hamburger(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fas fa-bars" aria-hidden="true"></i> <b class="caret"></b>';
		}

		return KunenaSvgIcons::loadsvg('three-dots-vertical');
	}

	/**
	 * Return the info icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function info(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-info-circle" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('alert-circle');
	}

	/**
	 * Return the online icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function online(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus green" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the away icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function away(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-plus yellow" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('plus');
	}

	/**
	 * Return the busy icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function busy(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus red" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('dash');
	}

	/**
	 * Return the invisible icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function invisible(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-minus grey" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('dash');
	}

	/**
	 * Return the cog icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function cog(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-cog" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('gear');
	}

	/**
	 * Return the drawer icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function drawer(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-inbox" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('inbox');
	}

	/**
	 * Return the out icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function out(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-sign-out-alt" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('power');
	}

	/**
	 * Return the grid icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function grid(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-th" aria-hidden="true"></i>';
		}

		return KunenaSvgIcons::loadsvg('grid');
	}

	/**
	 * Return the globe icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function globe(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-globe" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function location(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-map-marker" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function pm(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="far fa-comments" aria-hidden="true"></i>';
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
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function report(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-exclamation" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-report" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('alert-octagon');
	}

	/**
	 * Return the stick icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function sticky(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return '<i class="fa fa-exclamation" aria-hidden="true"></i>';
		}

		if ($topicicontype == 'image')
		{
			return '<span class="kicon-sticky" aria-hidden="true"></span>';
		}

		return KunenaSvgIcons::loadsvg('award');
	}

	/**
	 * Return the report icon
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 5.0
	 */
	public static function reportname(): string
	{
		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'fa')
		{
			return 'fa fa-exclamation';
		}

		if ($topicicontype == 'image')
		{
			return 'kicon-report';
		}

		return KunenaSvgIcons::loadsvg('alert-octagon');
	}
}
