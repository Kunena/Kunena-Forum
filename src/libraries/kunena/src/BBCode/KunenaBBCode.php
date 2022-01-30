<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\BBCode;

\defined('_JEXEC') or die();

use Exception;
use GeSHi;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Http\Transport\StreamTransport;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Uri\UriHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Nbbc\BBCode;
use Nbbc\BBCodeLibrary;
use stdClass;

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

require_once KPATH_FRAMEWORK . '/External/Nbbc/src/BBCode.php';
require_once KPATH_FRAMEWORK . '/External/Nbbc/src/BBCodeLibrary.php';
require_once KPATH_FRAMEWORK . '/External/Nbbc/src/BBCodeLexer.php';
require_once KPATH_FRAMEWORK . '/External/Nbbc/src/Debugger.php';

/**
 * @see     \Nbbc\BBCode;
 * Class KunenaBBCode
 *
 * @since   6.0
 */
class KunenaBBCode extends \Nbbc\BBCode
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $autoLink_disable = 0;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $parent = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $output_limit = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $text_length = 0;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $url_target = null;

	/**
	 * @var     KunenaBBCodeLibrary
	 * @since   Kunena 6.0
	 */
	protected $defaults;

	/**
	 * @var     array|array[]
	 * @since   Kunena 6.0
	 */
	protected $tag_rules;

	/**
	 * @var     array|string[]
	 * @since   Kunena 6.0
	 */
	protected $smileys;

	/**
	 * @var     array|string[]
	 * @since   Kunena 6.0
	 */
	protected $url_pattern;

	/**
	 * Use KunenaBbcode::getInstance() instead.
	 *
	 * @internal
	 *
	 * @param   bool  $relative  relative
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($relative = true)
	{
		parent::__construct();
		$this->defaults  = new KunenaBBCodeLibrary;
		$this->tag_rules = $this->defaults->default_tag_rules;

		$this->smileys = $this->defaults->default_smileys;

		if (empty($this->smileys))
		{
			$this->SetEnableSmileys(false);
		}

		if (JDEBUG && KunenaFactory::getConfig()->debug && KunenaForum::isDev())
		{
			/*
			 * Uncomment the two lines only whne need to have the log of debug else it's way too slow
			 *
			 * $this->setDebug(true);
			 * $this->setLogFile(Factory::getApplication()->get('log_path'). '/kunena.NBBC_BBCODE.php');
			 */
		}

		$this->SetSmileyDir(JPATH_ROOT);
		$this->SetSmileyURL($relative ? Uri::root(true) : rtrim(Uri::root(), '/'));
		$this->SetDetectURLs(true);
		// The following call works with a hack in Nbbc\BBCode class in he method fillTemplate
		$this->SetURLPattern([$this, 'parseUrl']);
		$this->SetURLTarget('_blank');

		PluginHelper::importPlugin('kunena');
		Factory::getApplication()->triggerEvent('onKunenaBbcodeConstruct', [$this]);
	}

	/**
	 * Get global instance from BBCode parser.
	 *
	 * @param   bool  $relative  relative
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public static function getInstance($relative = true): KunenaBBCode
	{
		static $instance = false;

		if (!isset($instance[\intval($relative)]))
		{
			$instance                     = [];
			$instance[\intval($relative)] = new KunenaBBCode($relative);
		}

		$instance[\intval($relative)]->autoLink_disable = 0;

		return $instance[\intval($relative)];
	}

	/**
	 * @param   mixed  $params  params
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function parseUrl($params)
	{
		$url  = $params['url'];
		$text = $params['text'];

		$config = KunenaFactory::getConfig();

		if ($config->autoLink)
		{
			if (preg_match('#^mailto:#ui', $url))
			{
				// Cloak email addresses
				$email = $text;

				$layout = KunenaLayout::factory('BBCode/Email');

				if ($layout->getPath())
				{
					return (string) $layout
						->set('email', $email)
						->set('mailto', $this->IsValidEmail($email));
				}

				if ($this->canCloakEmail($params))
				{
					return HTMLHelper::_('email.cloak', $email, $this->IsValidEmail($email));
				}
				else
				{
					return '<a href="mailto:' . $email . '">' . $email . '</a>';
				}
			}

			// Remove http(s):// from the text
			$text = preg_replace('#^http(s?)://#ui', '', $text);

			if ($config->trimLongUrls)
			{
				// Shorten URL text if they are too long
				$text = preg_replace('#^(.{' . $config->trimLongUrlsFront . '})(.{4,})(.{' . $config->trimLongUrlsBack . '})$#u', '\1...\3', $text);
			}
		}

		if (!isset($params['query']))
		{
			$params['query'] = '';
		}

		if (!isset($params['path']))
		{
			$params['path'] = '';
		}

		if ($config->autoEmbedSoundcloud && empty($this->parent->forceMinimal) && isset($params['host']))
		{
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if (strstr($params['host'], 'soundcloud.') && !empty($path[1]))
			{
				return '<iframe allowtransparency="true" width="100%" height="350" src="https://w.soundcloud.com/player/?url=' . $params['url'] . '&amp;auto_play=false&amp;visual=true" style="border: 0"></iframe><br />';
			}
		}

		if ($config->autoEmbedInstagram && empty($this->parent->forceMinimal) && isset($params['host']))
		{
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if (strstr($params['host'], 'instagram.') && !empty($path[1]))
			{
				return KunenaBBCodeLibrary::DoInstagram('', '', '', '', '', rtrim($params['url']));
			}
		}

		if ($config->autoEmbedYoutube && empty($this->parent->forceMinimal) && isset($params['host']))
		{
			// Convert youtube links to embedded player
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if (strstr($params['host'], '.youtube.') && !empty($path[1]) && $path[1] == 'watch' && !empty($query['v']))
			{
				$video = $query['v'];
			}
			elseif ($params['host'] == 'youtu.be' && !empty($path[1]))
			{
				$video = $path[1];
			}
			elseif (strstr($params['host'], '.youtube.') && !empty($path[1]) && $path[1] == 'embed')
			{
				$video = $path[2];
			}

			if (isset($video))
			{
				$uri = Uri::getInstance();

				if ($uri->isSSL())
				{
					return '<div class="embed-responsive embed-responsive-16by9"><iframe width="425" height="344" src="https://www.youtube-nocookie.com/embed/' . urlencode($video) . '" allowfullscreen style="border: 0"></iframe></div>';
				}
				else
				{
					return '<div class="embed-responsive embed-responsive-16by9"><iframe width="425" height="344" src="http://www.youtube-nocookie.com/embed/' . urlencode($video) . '" allowfullscreen style="border: 0"></iframe></div>';
				}
			}
		}

		if ($config->autoEmbedEbay && empty($this->parent->forceMinimal) && isset($params['host']) && strstr($params['host'], '.ebay.'))
		{
			parse_str($params['query'], $query);
			$path   = explode('/', $params['path']);
			$itemid = '';

			if ($path[1] == 'itm')
			{
				if (isset($path[3]) && is_numeric($path[3]))
				{
					$itemid = $path[3];
				}
				elseif (isset($path[2]) && is_numeric($path[2]))
				{
					$itemid = $path[2];
				}

				if (isset($itemid))
				{
					// Convert ebay item to embedded widget
					return KunenaBBCodeLibrary::renderEbayLayout($itemid);
				}

				return;
			}

			parse_str($params['query'], $query);

			if (isset($path[1]) && $path[1] == 'sch' && !empty($query['_nkw']))
			{
				// Convert ebay search to embedded widget
				KunenaBBCodeLibrary::renderEbayLayout($itemid);
			}

			if (strstr($params['host'], 'myworld.') && !empty($path[1]))
			{
				// Convert seller listing to embedded widget
				KunenaBBCodeLibrary::renderEbayLayout($itemid);
			}
		}

		if (isset($params['host']) && strstr($params['host'], 'twitter.'))
		{
			$path = explode('/', $params['path']);

			if (isset($path[3]))
			{
				return $this->defaults->renderTweet($path[3]);
			}
		}

		if ($config->autoLink)
		{
			$internal = false;
			$layout   = KunenaLayout::factory('BBCode/URL');

			if ($config->smartLinking)
			{
				$text = $this->get_title($url);
			}

			$uri  = Uri::getInstance($url);
			$host = $uri->getHost();

			// The cms will catch most of these well
			if (empty($host) || Uri::isInternal($url))
			{
				$internal = true;
			}

			if ($layout->getPath())
			{
				return (string) $layout
					->set('content', $text)
					->set('url', $url)
					->set('target', $this->url_target)
					->set('internal', $internal);
			}

			$url = htmlspecialchars($url, ENT_COMPAT, 'UTF-8');

			if (strpos($url, '/index.php') !== 0)
			{
				return "<a class=\"bbcode_url\" href=\"{$url}\" target=\"_blank\" rel=\"nofollow\">{$text}</a>";
			}
			else
			{
				return "<a class=\"bbcode_url\" href=\"{$url}\" target=\"_blank\">{$text}</a>";
			}
		}

		// Auto-linking has been disabled.
		return $text;
	}

	/**
	 * @see     BBCode::SetEnableSmileys()
	 *
	 * @param   string  $email  email
	 *
	 * @return  void
	 * @since   Kunena 6.0
	 */
	public function IsValidEmail($email)
	{
	}

	/**
	 * @param   mixed  $params  params
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function canCloakEmail(&$params): bool
	{
		if (PluginHelper::isEnabled('content', 'emailcloak'))
		{
			$plugin = PluginHelper::getPlugin('content', 'emailcloak');
			$params = new Registry($plugin->params);

			if ($params->get('mode', 1))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Used for Smart Auto Linking, it loads the content of the url given to search the title from it
	 *
	 * @param   string  $url  url
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 */
	public function get_title(string $url): string
	{
		$str = @file_get_contents($url);

		if ($str !== false)
		{
			if (\strlen($str) > 0)
			{
				// Supports line breaks inside <title>
				$str = trim(preg_replace('/\s+/', ' ', $str));

				// Ignore case
				preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title);

				return $title[1];
			}
		}

		return '';
	}

	/**
	 * @param   string  $string  string
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function autoDetectURLs($string): array
	{
		$search = preg_split('/(?xi)
		\b
		(
			(?:
				(?:https?|ftp):\/\/
				|
				www\d{0,3}\.
				|
				mailto:
				|
				(?:[a-zA-Z0-9._-]{2,}@)
			)
			(?:
				[^\s()<>]+
				|
				\((?:[^\s()<>]+|(\(?:[^\s()<>]+\)))*\)
			)+
			(?:
				\((?:[^\s()<>]+|(\(?:[^\s()<>]+\)))*\)
				|
				[^\s`!()\[\]{};:\'"\.,<>?«»“”‘’™]
			)
		)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE);

		$output = [];

		if ($search !== false)
		{
			foreach ($search as $index => $token)
			{
				if ($index && 1)
				{
					if (preg_match("/^(https?|ftp|mailto):/ui", $token))
					{
						// Protocol has been provided, so just use it as-is.
						$url = $token;
					}
					else
					{
						// Add scheme to emails and raw domain URLs.
						$url = (strpos($token, '@') ? 'mailto:' : 'http://') . $token;
					}

					// Never start URL from the middle of text (except for punctuation).
					$invalid = preg_match('#[^\s`!()\[\]{};\'"\.,<>?«»“”‘’]$#u', $search[$index - 1]);
					$invalid |= !$this->IsValidURL($url, true);

					// We have a full, complete, and properly-formatted URL, with protocol.
					// Now we need to apply the $this->getURLPattern() template to turn it into HTML.
					$params = UriHelper::parse_url($url);

					if (!$invalid && substr($url, 0, 7) == 'mailto:')
					{
						$email = StringHelper::substr($url, 7);

						if ($this->canCloakEmail($params))
						{
							$output[$index] = HTMLHelper::_('email.cloak', $email, $this->IsValidEmail($email));
						}
						else
						{
							$output[$index] = $email;
						}
					}
					elseif ($invalid || empty($params['host']) || !empty($params['pass']))
					{
						$output[$index - 1] .= $token;
						$output[$index]     = '';
					}
					else
					{
						$params['url']  = $url;
						$params['link'] = $url;
						$params['text'] = $token;
						$output[$index] = $this->FillTemplate($this->getURLPattern(), $params);
					}
				}
				else
				{
					$output[$index] = $token;
				}
			}
		}

		return $output;
	}

	/**
	 * @see     BBCode::IsValidURL()
	 * Regular expression taken from https://gist.github.com/729294
	 *
	 * @param   bool    $local_too  local
	 *
	 * @param   string  $string     string
	 *
	 * @param   bool    $email_too  email
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function IsValidURL($string, $email_too = true, $local_too = false)
	{
		static $re = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';

		if (empty($string))
		{
			return false;
		}

		if ($local_too && $string[0] == '/')
		{
			$string = 'http://www.domain.com' . $string;
		}

		if ($email_too && substr($string, 0, 7) == "mailto:")
		{
			return $this->IsValidEmail(substr($string, 7));
		}

		if (preg_match($re, $string))
		{
			return true;
		}

		return false;
	}
}

/**
 * Class KunenaBbcodeLibrary
 *
 * @since   Kunena 6.0
 */
class KunenaBBCodeLibrary extends BBCodeLibrary
{
	/**
	 * The bearer token to get tweet data
	 *
	 * @var     string
	 * @since   Kunena 4.0
	 */
	public $token = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $mapid = 0;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $default_tag_rules = [
		'b'            => [
			'simple_start' => "<b>",
			'simple_end'   => "</b>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'plain_start'  => "<b>",
			'plain_end'    => "</b>",
			'allow_params' => false,
		],
		'i'            => [
			'simple_start' => "<i>",
			'simple_end'   => "</i>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
			'allow_params' => false,
		],
		'u'            => [
			'simple_start' => "<u>",
			'simple_end'   => "</u>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'plain_start'  => "<u>",
			'plain_end'    => "</u>",
			'allow_params' => false,
		],
		's'            => [
			'simple_start' => "<s>",
			'simple_end'   => "</s>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'plain_start'  => "<s>",
			'plain_end'    => "</s>",
			'allow_params' => false,
		],
		'pre'          => [
			'simple_start' => "<pre>",
			'simple_end'   => "</pre>",
			'class'        => 'block',
			'allow_in'     => ['listitem', 'block', 'columns'],
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		],
		'font'         => [
			'mode'     => BBCode::BBCODE_MODE_LIBRARY,
			'allow'    => ['_default' => '/^[a-zA-Z0-9._, -]+$/'],
			'method'   => 'doFont',
			'class'    => 'inline',
			'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'color'        => [
			'mode'     => BBCode::BBCODE_MODE_ENHANCED,
			'allow'    => ['_default' => '/^#?[a-zA-Z0-9._ -]+$/'],
			'template' => '<span style="color: {$_default/tw};">{$_content/v}</span>',
			'class'    => 'inline',
			'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'size'         => [
			'mode'     => BBCode::BBCODE_MODE_LIBRARY,
			'allow'    => ['_default' => '/^[0-9.]+$/D'],
			'method'   => 'doSize',
			'class'    => 'inline',
			'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'sup'          => [
			'simple_start' => "<sup>",
			'simple_end'   => "</sup>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'allow_params' => false,
		],
		'sub'          => [
			'simple_start' => "<sub>",
			'simple_end'   => "</sub>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'allow_params' => false,
		],
		'spoiler'      => [
			'simple_start' => "<span class=\"bbcode_spoiler\">",
			'simple_end'   => "</span>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'hide'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoHide',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_content' => [],
		],
		'confidential' => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoConfidential',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_content' => [],
		],
		'map'          => [
			'mode'     => BBCode::BBCODE_MODE_LIBRARY,
			'method'   => 'DoMap',
			'class'    => 'block',
			'allow'    => ['type' => '/^[\w\d.-_]*$/', 'zoom' => '/^\d*$/', 'control' => '/^\d*$/'],
			'allow_in' => ['listitem', 'block', 'columns'],
			'content'  => BBCode::BBCODE_VERBATIM,
		],
		'ebay'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoEbay',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "[ebay]",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'article'      => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoArticle',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_start'   => "\n[article]\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'tableau'      => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoTableau',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "\n[tableau]\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'video'        => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoVideo',
			'allow'         => ['type' => '/^[\w\d.-_]*$/', 'param' => '/^[\w]*$/', 'size' => '/^\d*$/', 'width' => '/^\d*$/', 'height' => '/^\d*$/'],
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "[video]",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'img'          => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoImage',
			'allow'         => ['size' => '/^\d*$/'],
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns', 'link'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "[image]",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'file'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoFile',
			'allow'         => ['size' => '/^\d*$/'],
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "\n[file]\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'attachment'   => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoAttachment',
			'allow'         => ['_default' => '/^\d*$/'],
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "\n[attachment]\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'highlight'    => [
			'simple_start' => "<span style='font-weight: 700;'>",
			'simple_end'   => "</span>",
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		],
		'acronym'      => [
			'mode'     => BBCode::BBCODE_MODE_ENHANCED,
			'template' => '<span class="bbcode_acronym" title="{$_default/e}">{$_content/v}</span>',
			'class'    => 'inline',
			'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'url'          => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoUrl',
			'class'         => 'link',
			'allow_in'      => ['listitem', 'block', 'columns', 'inline'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_content' => ['_content', '_default'],
			'plain_link'    => ['_default', '_content'],
		],
		'email'        => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'doEmail',
			'class'         => 'link',
			'allow_in'      => ['listitem', 'block', 'columns', 'inline'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_start'   => "<a href=\"mailto:{\$link}\">",
			'plain_end'     => "</a>",
			'plain_content' => ['_content', '_default'],
			'plain_link'    => ['_default', '_content'],
		],
		'wiki'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => "doWiki",
			'class'         => 'link',
			'allow_in'      => ['listitem', 'block', 'columns', 'inline'],
			'end_tag'       => BBCode::BBCODE_PROHIBIT,
			'content'       => BBCode::BBCODE_PROHIBIT,
			'plain_start'   => "<b>[",
			'plain_end'     => "]</b>",
			'plain_content' => ['title', '_default'],
			'plain_link'    => ['_default', '_content'],
		],
		'rule'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => "doRule",
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'end_tag'       => BBCode::BBCODE_PROHIBIT,
			'content'       => BBCode::BBCODE_PROHIBIT,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'plain_start'   => "\n-----\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'br'           => [
			'mode'          => BBCode::BBCODE_MODE_SIMPLE,
			'simple_start'  => "<br>\n",
			'simple_end'    => "",
			'class'         => 'inline',
			'allow_in'      => ['listitem', 'block', 'columns', 'inline', 'link'],
			'end_tag'       => BBCode::BBCODE_PROHIBIT,
			'content'       => BBCode::BBCODE_PROHIBIT,
			'before_tag'    => "s",
			'after_tag'     => "s",
			'plain_start'   => "\n",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'left'         => [
			'simple_start'  => "\n<div class=\"bbcode_left\" style=\"text-align:left\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'right'        => [
			'simple_start'  => "\n<div class=\"bbcode_right\" style=\"text-align:right\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'center'       => [
			'simple_start'  => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'rtl'          => [
			'simple_start' => '<div style="direction: rtl;">',
			'simple_end'   => '</div>',
			'class'        => 'inline',
			'allow_in'     => ['listitem', 'block', 'columns', 'inline', 'link'],
		],
		'indent'       => [
			'simple_start'  => "\n<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'table'        => [
			'simple_start'  => "\n<table>",
			'simple_end'    => "</table>\n",
			'class'         => 'table',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'end_tag'       => BBCode::BBCODE_REQUIRED,
			'content'       => BBCode::BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'tr'           => [
			'simple_start'  => "\n<tr>",
			'simple_end'    => "</tr>\n",
			'class'         => 'tr',
			'allow_in'      => ['table'],
			'end_tag'       => BBCode::BBCODE_REQUIRED,
			'content'       => BBCode::BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'th'           => [
			'simple_start'  => "<th>",
			'simple_end'    => "</th>",
			'class'         => 'columns',
			'allow_in'      => ['tr'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'td'           => [
			'simple_start'  => "<td>",
			'simple_end'    => "</td>",
			'class'         => 'columns',
			'allow_in'      => ['tr'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'columns'      => [
			'simple_start'  => "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n",
			'simple_end'    => "\n</td></tr></tbody></table>\n",
			'class'         => 'columns',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'end_tag'       => BBCode::BBCODE_REQUIRED,
			'content'       => BBCode::BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'nextcol'      => [
			'simple_start'  => "\n</td><td class=\"bbcode_column\">\n",
			'class'         => 'nextcol',
			'allow_in'      => ['columns'],
			'end_tag'       => BBCode::BBCODE_PROHIBIT,
			'content'       => BBCode::BBCODE_PROHIBIT,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "",
		],
		'code'         => [
			'mode'          => BBCode::BBCODE_MODE_ENHANCED,
			'template'      => "\n<div class=\"bbcode_code\">\n<div class=\"bbcode_code_head\">Code:</div>\n<div class=\"bbcode_code_body\" style=\"white-space:pre\">{\$_content/v}</div>\n</div>\n",
			'class'         => 'code',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'before_tag'    => "sns",
			'after_tag'     => "sn",
			'before_endtag' => "sn",
			'after_endtag'  => "sns",
			'plain_start'   => "\n<b>Code:</b>\n",
			'plain_end'     => "\n",
		],
		'quote'        => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => "DoQuote",
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n<b>Quote:</b>\n",
			'plain_end'     => "\n",
		],
		'list'         => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'class'         => 'list',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'ul'           => [
			'mode'          => BBcode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'default'       => ['_default' => 'disc'],
			'class'         => 'list',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'ol'           => [
			'mode'          => BBcode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'allow'         => ['_default' => '/^[\d\w]*$/'],
			'default'       => ['_default' => '1'],
			'class'         => 'list',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		],
		'*'            => [
			'simple_start'  => "<li>",
			'simple_end'    => "</li>\n",
			'class'         => 'listitem',
			'allow_in'      => ['list'],
			'end_tag'       => BBCode::BBCODE_OPTIONAL,
			'before_tag'    => "s",
			'after_tag'     => "s",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n * ",
			'plain_end'     => "\n",
		],
		'li'           => [
			'simple_start'  => "<li>",
			'simple_end'    => "</li>\n",
			'class'         => 'listitem',
			'allow_in'      => ['listitem', 'block', 'columns', 'list'],
			'before_tag'    => "s",
			'after_tag'     => "s",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n * ",
			'plain_end'     => "\n",
		],
		'terminal'     => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoTerminal',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'class'         => 'code',
			'allow'         => ['colortext' => '/^|#[0-9a-fA-F]+|[a-zA-Z]+$/'],
			'before_tag'    => "sns",
			'after_tag'     => "sn",
			'before_endtag' => "ns",
			'after_endtag'  => "sns",
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "\nTerminal:\n",
			'plain_end'     => "\n",
		],
		'tweet'        => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoTweet',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_content' => [],
		],
		'soundcloud'   => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoSoundcloud',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_VERBATIM,
			'plain_start'   => "[soundcloud]",
			'plain_end'     => "",
			'plain_content' => [],
		],
		'instagram'    => [
			'mode'     => BBCode::BBCODE_MODE_LIBRARY,
			'method'   => 'DoInstagram',
			'allow_in' => ['listitem', 'block', 'columns'],
			'class'    => 'block',
			'allow'    => ['colortext' => '/^[\w\d.-_]*$/'],
			'content'  => BBCode::BBCODE_PROHIBIT,
		],
		'private'      => [
			'mode'          => BBCode::BBCODE_MODE_LIBRARY,
			'method'        => 'DoPrivate',
			'class'         => 'block',
			'allow_in'      => ['listitem', 'block', 'columns'],
			'content'       => BBCode::BBCODE_REQUIRED,
			'plain_content' => [],
		],
	];

	/**
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function __construct()
	{
		if (!KunenaFactory::getConfig()->disableEmoticons)
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select([$db->quoteName('code'), $db->quoteName('location')])
				->from($db->quoteName('#__kunena_smileys'));
			$db->setQuery($query);
			$smileys = $db->loadObjectList();

			$template = KunenaFactory::getTemplate();

			foreach ($smileys as $smiley)
			{
				$this->default_smileys [$smiley->code] = $template->getSmileyPath($smiley->location);
			}
		}

		// Translate plain text "Quote:"
		$this->default_tag_rules['quote']['plain_start'] = Text::_('COM_KUNENA_LIB_BBCODE_QUOTE_TITLE');
	}

	/**
	 * Query from eBay API the JSON stream of item id given to render
	 *
	 * @param   int  $ItemID  The eBay ID of object to query
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function getEbayItem(int $ItemID): string
	{
		$config = KunenaFactory::getConfig();

		$options = new Registry;

		$transport = new StreamTransport($options);

		// Create a 'stream' transport.
		$http = new Http($options, $transport);

		$data = ['grant_type' => 'client_credentials', 'scope' => 'https://api.ebay.com/oauth/api_scope'];

		$headersOauth = ['Content-Type' => 'application/x-www-form-urlencoded',
		                 'Authorization' => 'Basic ' . base64_encode($config->ebayApiKey . ':' . $config->ebayCertId), ];

		$responseOauth = $http->post('https://api.ebay.com/identity/v1/oauth2/token', $data, $headersOauth);

		if ($responseOauth->code == '200')
		{
			$resp = json_decode($responseOauth->body);

			$token = $resp->access_token;

			$headers = ['X-EBAY-API-IAF-TOKEN' => $token, 'X-EBAY-API-CALL-NAME' => 'GetSingleItem', 'X-EBAY-API-VERSION' => 1157];

			$response = $http->get('https://open.api.ebay.com/shopping?callname=GetSingleItem&siteid=0&responseencoding=JSON&ItemID='
				. $ItemID . '&trackingid=' . $config->ebayAffiliateId . '&trackingpartnercode=9', $headers, '10');

			if ($response->code == '200')
			{
				$responseItem = json_decode($response->body);

				if ($responseItem->Ack == 'Success')
				{
					return $responseItem->Item;
				}
				else
				{
					$errors = $responseItem->Errors;

					return $errors[0]->LongMessage;
				}
			}
		}
		else
		{
			$respOauthError = json_decode($responseOauth->body);

			return $respOauthError->error_description;
		}

		return '';
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 */
	public static function DoInstagram($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($content))
		{
			return self::renderInstagram($bbcode, $content);
		}

		return false;
	}

	/**
	 * Method to render the instagram content
	 *
	 * @param   string  $content
	 *
	 * @return string
	 * @since Kunena 5.2
	 */
	public static function renderInstagram($bbcode, $content)
	{
		$before  = $content;
		$content = strip_tags($content);

		$content = trim($content);

		$url_parsed = parse_url($content);

		if (isset($url_parsed['scheme']))
		{
			if ($url_parsed['scheme'] == 'https' || $url_parsed['scheme'] == 'http')
			{
				$content = $url_parsed['host'] . $url_parsed['path'];
			}
			else
			{
				$content = $url_parsed['path'];
			}
		}

		if (preg_match('/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im', $content))
		{
			if (!preg_match('#^(/|https?:|ftp:)#ui', $content))
			{
				// Add scheme to raw domain URLs.
				$url = "https://{$content}";
			}

			return '<div class="embed-container"><iframe src="' . rtrim($url, '/') . '/embed/" style="border: 0"></iframe></div>';
		}

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return "<a href=\"" . $content . "\" rel=\"nofollow\" target=\"_blank\">" . $content . '</a>';
		}

		if (!empty($content))
		{
			return '<div class="embed-container"><iframe src="https://www.instagram.com/p/' . $content . '/embed/" style="border: 0"></iframe></div>';
		}
		else
		{
			return $before;
		}
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|mixed|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoEmail($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$email     = \is_string($default) ? $default : $bbcode->UnHTMLEncode($content);
		$text      = \is_string($default) ? $bbcode->UnHTMLEncode($content) : $default;
		$text      = trim($text && $email != $text ? $text : '');
		$mailto    = $bbcode->IsValidEmail($email);
		$textCloak = $bbcode->IsValidEmail($text);

		$layout = KunenaLayout::factory('BBCode/Email');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('email', $email)
				->set('mailto', $mailto)
				->set('text', $text)
				->set('textCloak', $textCloak);
		}

		if ($bbcode->canCloakEmail($params))
		{
			return HTMLHelper::_('email.cloak', htmlspecialchars($email), $bbcode->IsValidEmail($email), htmlspecialchars($text), $bbcode->IsValidEmail($text));
		}
		else
		{
			return '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($text) . '</a>';
		}
	}

	// Format a [size] tag by producing a <span> with a style with a different font-size.

	/**
	 * Format a [url] tag by producing an <a>...</a> element.
	 * The URL only allows http, https, mailto, and ftp protocols for safety.
	 *
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoURL($bbcode, $action, $name, $default, $params, $content)
	{
		// We can't check this with BBCODE_CHECK because we may have no URL before the content
		// Has been processed.
		if ($action == BBCode::BBCODE_CHECK)
		{
			$bbcode->autoLink_disable++;

			return true;
		}

		$bbcode->autoLink_disable--;
		$url      = $default ? $default : strip_tags($bbcode->UnHTMLEncode($content));
		$url      = preg_replace('# #u', '%20', $url);
		$internal = false;

		if (preg_match('#^(index.php?)#uim', $url))
		{
			$url = Route::_($url, false);
		}

		if (preg_match('#^(/index.php?)#uim', $url))
		{
			$str = substr($url, 1);
			$url = Route::_($str, false);
		}

		if (!preg_match('#^(/|https?:|ftp:)#uim', $url))
		{
			$url = "http://{$url}";
		}

		if (!$bbcode->IsValidURL($url, false, true))
		{
			return htmlspecialchars($params['_tag'], ENT_COMPAT, 'UTF-8') . $content . htmlspecialchars($params['_endtag'], ENT_COMPAT, 'UTF-8');
		}

		if (isset($params['target']))
		{
			$target = $bbcode->url_target;
			$class  = $params['class'];
		}
		else
		{
			$target = '';
			$class  = null;
		}

		$uri  = Uri::getInstance($url);
		$host = $uri->getHost();

		// The cms will catch most of these well
		if (empty($host) || Uri::isInternal($url))
		{
			$internal = true;
		}

		$smart = KunenaConfig::getInstance()->smartLinking;

		if ($smart)
		{
			$content = $bbcode->get_title($url);

			if (!isset($content))
			{
				$content = $url;
			}
		}

		$layout = KunenaLayout::factory('BBCode/URL');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('class', $class)
				->set('content', $content)
				->set('url', $url)
				->set('target', $target)
				->set('internal', $internal);
		}

		return false;
	}

	// Format a [list] tag, which is complicated by the number of different
	// ways a list can be started.  The following parameters are allowed:
	//
	//   [list]           Unordered list, using default marker
	//   [list=circle]    Unordered list, using circle marker
	//   [list=disc]      Unordered list, using disc marker
	//   [list=square]    Unordered list, using square marker
	//
	//   [list=1]         Ordered list, numeric, starting at 1
	//   [list=A]         Ordered list, capital letters, starting at A
	//   [list=a]         Ordered list, lowercase letters, starting at a
	//   [list=I]         Ordered list, capital Roman numerals, starting at I
	//   [list=i]         Ordered list, lowercase Roman numerals, starting at i
	//   [list=greek]     Ordered list, lowercase Greek letters, starting at alpha
	//   [list=01]        Ordered list, two-digit numeric with 0-padding, starting at 01

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoSize($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if ($default == 200)
		{
			$default = 6;
		}
		elseif ($default == 150)
		{
			$default = 4;
		}
		elseif ($default == 100)
		{
			$default = 3;
		}
		elseif ($default == 85)
		{
			$default = 2;
		}
		elseif ($default == 50)
		{
			$default = 1;
		}

		$layout = KunenaLayout::factory('BBCode/Size');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('content', $content)
				->set('size', $default);
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 */
	public function DoList($bbcode, $action, $name, $default, $params, $content)
	{
		// Allowed list styles, striaght from the CSS 2.1 spec.  The only prohibited
		// list style is that with image-based markers, which often slows down web sites.
		$list_styles    = ['1' => 'decimal', '01' => 'decimal-leading-zero', 'i' => 'lower-roman', 'I' => 'upper-roman', 'a' => 'lower-alpha', 'A' => 'upper-alpha'];
		$ci_list_styles = ['circle' => 'circle', 'disc' => 'disc', 'square' => 'square', 'greek' => 'lower-greek', 'armenian' => 'armenian', 'georgian' => 'georgian'];
		$ul_types       = ['circle' => 'circle', 'disc' => 'disc', 'square' => 'square'];
		$default        = trim($default);

		if (!$default)
		{
			$tag_rule = $bbcode->getRule($name);

			if (isset($tag_rule ['default']))
			{
				$default = $tag_rule ['default'] ['_default'];
			}
			else
			{
				$default = $tag_rule ['default'] = array();
			}
		}

		if ($action == BBCode::BBCODE_CHECK)
		{
			if (!\is_string($default) || \strlen($default) == "")
			{
				return true;
			}
			elseif (isset($list_styles [$default]))
			{
				return true;
			}
			elseif (isset($ci_list_styles [strtolower($default)]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		// Choose a list element (<ul> or <ol>) and a style.
		$type = '';
		$elem = 'ul';

		if (!\is_string($default) || \strlen($default) == "")
		{
			$elem = 'ul';
		}
		elseif ($default == '1')
		{
			$elem = 'ol';
		}
		elseif (isset($list_styles [$default]))
		{
			$elem = 'ol';
			$type = $list_styles [$default];
		}
		else
		{
			$default = strtolower($default);

			if (isset($ul_types [$default]))
			{
				$elem = 'ul';
				$type = $ul_types [$default];
			}
			elseif (isset($ci_list_styles [$default]))
			{
				$elem = 'ol';
				$type = $ci_list_styles [$default];
			}
		}

		// Generate the HTML for it.
		if (\strlen($type))
		{
			return "\n<$elem class=\"bbcode_list\" style=\"list-style-type:$type\">\n$content</$elem>\n";
		}
		else
		{
			return "\n<$elem class=\"bbcode_list\">\n$content</$elem>\n";
		}
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoSpoiler($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited)
		{
			return "[{$name}]{$content}";
		}

		// Display only spoiler text in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return '[' . ($default ? $default : Text::_('COM_KUNENA_BBCODE_SPOILER')) . ']';
		}

		$document = Factory::getApplication()->getDocument();
		$title    = $default ? $default : Text::_('COM_KUNENA_BBCODE_SPOILER');
		$hidden   = ($document instanceof HtmlDocument);

		$layout = KunenaLayout::factory('BBCode/Spoiler');

		if ($layout->getPath())
		{
			$title   = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $title);
			$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

			return (string) $layout
				->set('title', $title)
				->set('hidden', $hidden)
				->set('content', $content)
				->set('params', $params);
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoHide($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited)
		{
			return "[{$name}]{$content}";
		}

		// Display nothing in activity streams etc..
		if (!empty($bbcode->parent->forceSecure))
		{
			return '';
		}

		$me = KunenaUserHelper::getMyself();

		if (!Factory::getApplication()->getIdentity()->guest)
		{
			$layout = KunenaLayout::factory('BBCode/Hide');

			if ($layout->getPath())
			{
				return (string) $layout
					->set('me', $me)
					->set('content', $content)
					->set('params', $params);
			}
		}
		else
		{
			return '<br />' . Text::_('COM_KUNENA_BBCODE_HIDDENTEXT') . '<br />';
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoConfidential($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited)
		{
			return "[{$name}]{$content}";
		}

		// Display nothing in activity streams etc..
		if (!empty($bbcode->parent->forceSecure))
		{
			return '';
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$me        = KunenaUserHelper::getMyself();
		$message   = $this->getMessage();
		$moderator = $me->userid && $me->isModerator($message ? $message->getCategory() : null);

		if (isset($bbcode->parent->message->userid))
		{
			$message_userid = $bbcode->parent->message->userid;
		}
		else
		{
			$message_userid = $bbcode->parent->userid;
		}

		if (($me->userid && $message_userid == $me->userid) || $moderator)
		{
			$layout = KunenaLayout::factory('BBCode/Confidential');

			if ($layout->getPath())
			{
				return (string) $layout
					->set('me', $me)
					->set('content', $content)
					->set('params', $params);
			}
		}
		else
		{
			return '<div class="kmsgtext-confidentialguests">' . Text::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT_GUESTS') . '</div>';
		}

		return false;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	protected function getMessage()
	{
		if (empty($this->parent))
		{
			return false;
		}

		if ($this->parent instanceof KunenaMessage)
		{
			return $this->parent;
		}

		return $this->parent->message ?? false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  bool|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoMap($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$content = trim($content);

		if (empty($content))
		{
			echo '<div class="alert alert-error">' . Text::_('COM_KUNENA_LIB_BBCODE_MAP_ERROR_CITY_MISSING') . '</div>';

			return false;
		}

		$config = KunenaFactory::getTemplate()->params;

		$document = Factory::getApplication()->getDocument();

		// Display only link in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal) || !($document instanceof HtmlDocument) || KunenaFactory::getTemplate()->isHmvc() && !$config->get('Maps'))
		{
			$url = 'https://maps.google.com/?q=' . urlencode($bbcode->UnHTMLEncode($content));

			return '<a href="' . $url . '" rel="nofollow noopener noreferrer" target="_blank">' . $content . '</a>';
		}

		$this->mapid++;

		$layout = KunenaLayout::factory('BBCode/Map');

		$kunena_config = KunenaFactory::getConfig();

		if ($layout->getPath())
		{
			return (string) $layout
				->set('content', $content)
				->set('mapid', $this->mapid)
				->set('params', $params)
				->set('config', $kunena_config);
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoEbay($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$config = KunenaFactory::getTemplate()->params;

		if (KunenaFactory::getTemplate()->isHmvc() && !$config->get('ebay'))
		{
			return false;
		}

		$config = KunenaFactory::getConfig();

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return '<a target="_blank" rel="noopener noreferrer" href="http://www.ebay.com/itm/' . $content . '?lang=' . $config->ebayLanguageCode . '&campid=' . $config->ebayAffiliateId . '">www.ebay.com/itm/' . $content . '</a>';
		}

		return self::renderEbayLayout($content);
	}

	/**
	 * Render eBay layout from template
	 *
	 * @param   int  $ItemID  id
	 *
	 * @return  false|string
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function renderEbayLayout(int $ItemID)
	{
		$config = KunenaFactory::getConfig();

		if (empty($config->ebayApiKey) || empty($config->ebayCertId))
		{
			echo '<b>' . Text::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_NO_EBAY_APP_ID') . '</b>';

			return false;
		}

		if (!is_numeric($ItemID))
		{
			echo '<b>' . Text::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_WRONG_ITEM_ID') . '</b>';

			return false;
		}

		$layout = KunenaLayout::factory('BBCode/eBay');

		if ($layout->getPath())
		{
			$ebay = self::getEbayItemFromCache($ItemID);

			if (\is_object($ebay))
			{
				return (string) $layout
					->set('content', $ItemID)
					// ->set('params', $params)
					->set('naturalurl', $ebay->ViewItemURLForNaturalSearch)
					->set('pictureurl', $ebay->PictureURL[0])
					->set('status', $ebay->ListingStatus)
					->set('title', $ebay->Title)
					->setLayout(is_numeric($ItemID) ? 'default' : 'search');
			}
			else
			{
				echo '<b>' . $ebay . '</b>';

				return false;
			}
		}

		return false;
	}

	/**
	 * Load eBay object item from cache
	 *
	 * @param   int  $ItemID  The eBay ID of object to query
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public static function getEbayItemFromCache(int $ItemID): string
	{
		$cache = Factory::getCache('Kunena_ebay_request');
		$cache->setCaching(true);
		$cache->setLifeTime(KunenaFactory::getConfig()->get('cacheTime', 60));

		return $cache->call(['KunenaBbcodeLibrary', 'getEbayItem'], $ItemID);
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoArticle($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$lang = Factory::getApplication()->getLanguage();
		$lang->load('com_content');

		$articleid = \intval($content);

		$config = KunenaFactory::getConfig();
		$user   = Factory::getApplication()->getIdentity();
		$site   = Factory::getApplication('site');

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('a.*, u.name AS author, cc.title AS category,
			0 AS sec_pub, 0 AS sectionid, cc.published AS cat_pub, cc.access AS cat_access')
			->from($db->quoteName('#__content', 'a'))
			->leftJoin($db->quoteName('#__categories', 'cc') . ' ON cc.id = a.catid')
			->leftJoin($db->quoteName('#__users', 'u') . ' ON u.id = a.created_by')
			->where('a.id = ' . $db->quote($articleid));
		$db->setQuery($query);
		$article = $db->loadObject();

		if ($article)
		{
			// Get credentials to check if the user has right to see the article
			$params   = $site->getParams('com_content');
			$registry = new Registry;
			$registry->loadString($article->attribs);
			$article->params = clone $params;
			$article->params->merge($registry);
			$params = $article->params;

			$viewlevels = $user->getAuthorisedViewLevels();

			if (!\in_array($article->access, $viewlevels))
			{
				$denied = true;
			}
		}

		$html = $link = '';

		if (!$article || (!$article->cat_pub && $article->catid) || (!$article->sec_pub && $article->sectionid))
		{
			$html = Text::_('COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_UNPUBLISHED');
		}
		elseif (!empty($denied) && !$params->get('show_noauth'))
		{
			$html = Text::_('COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_NO_PERMISSIONS');
		}
		else
		{
			$article->slug    = !empty($article->alias) ? ($article->id . ':' . $article->alias) : $article->id;
			$article->catslug = !empty($article->category_alias) ? ($article->catid . ':' . $article->category_alias) : $article->catid;
			$url              = Route::_(RouteHelper::getArticleRoute($article->slug, $article->catslug));

			if (!$default)
			{
				$default = $config->articleDisplay;
			}

			// Do not display full text if there's no permissions to display the full article.
			if (!empty($denied) && $default == 'full')
			{
				$default = 'intro';
			}

			switch ($default)
			{
				case 'full':
					if (!empty($article->fulltext) && !empty($article->introtext))
					{
						$article->text = $article->introtext . '<br />' . $article->fulltext;
						break;
					}
					elseif (empty($article->fulltext) && !empty($article->introtext))
					{
						$article->text = $article->introtext;
						break;
					}
					break;

				// Continue to intro if fulltext is empty
				case 'intro':
					if (!empty($article->introtext))
					{
						$article->text = $article->introtext;

						if (!empty($article->fulltext))
						{
							$link = '<a href="' . $url . '" class="readon">' . Text::sprintf('COM_KUNENA_LIB_BBCODE_ARTICLE_MORE') . '</a>';
						}
						else
						{
							$link = '';
						}
					}
					break;

				// Continue to link if introtext is empty
				case 'link':
				default:
					$link = '<a href="' . $url . '" class="readon">' . $article->title . '</a>';
					break;
			}

			if (!empty($article->text))
			{
				// Identify the source of the event to be Kunena itself
				// this is important to avoid recursive event behaviour with our own plugins
				$params->set('ksource', 'kunena');
				PluginHelper::importPlugin('content');

				Factory::getApplication()->triggerEvent('onContentPrepare', ['text', &$article, &$params, 0]);
				$article->text       = HTMLHelper::_('string.truncate', $article->text, $bbcode->output_limit - $bbcode->text_length);
				$bbcode->text_length += \strlen($article->text);
				$html                = $article->text;
			}

			if (!empty($denied))
			{
				$link = '<span class="readon">' . Text::_('COM_CONTENT_REGISTER_TO_READ_MORE') . '</span>';
			}
		}

		return ($html ? '<div class="kmsgtext-article">' . $html . '</div>' : '') . $link;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	public function DoQuote($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$default  = isset($default) ? htmlspecialchars($default, ENT_COMPAT, 'UTF-8') : false;

		$matches = [];
		preg_match('/userid=(\d+)/', $default, $matches);

		$userid = 0;
		foreach($matches as $match)
		{
			if (is_numeric($match))
			{
				$userid = (int) $match;
			}
		}

		if ($userid > 0)
		{
			$username = KunenaUserHelper::get($userid)->getName();
		}

		$matches_post = [];
		preg_match('/post=(\d+)/', $default, $matches_post);

		$postid = 0;
		foreach($matches_post as $match)
		{
			if (is_numeric($match))
			{
				$postid = (int) $match;
			}
		}

		if ($postid > 0)
		{
			$message = KunenaMessageHelper::get($postid);
			$msglink = Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getUrl(null, false);
		}

		// Support bbcode quote tag done in K5.1 and first versions of K5.2
		if ($userid==0 && $postid==0)
		{
			$user  = isset($default) ? htmlspecialchars($default, ENT_COMPAT, 'UTF-8') : false;
			$username = '';

			if ($user)
			{
				$username = $user . " " . Text::_('COM_KUNENA_POST_WROTE') . ': ';
			}

			$msglink = '';
		}

		$layout = KunenaLayout::factory('BBCode/Quote');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('username', $username)
				->set('msglink', $msglink)
				->set('content', $content);
		}

		return '';
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoCode($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$type = isset($params["type"]) ? $params["type"] : (isset($default) ? $default : "php");

		if ($type == 'js')
		{
			$type = 'javascript';
		}
		elseif ($type == 'html')
		{
			$type = 'html4strict';
		}

		if ($type == 'less' || $type == 'scss' || $type == 'sass')
		{
			$type = 'css';
		}

		$highlight = KunenaFactory::getConfig()->highlightCode && empty($bbcode->parent->forceMinimal);

		if ($highlight && !class_exists('GeSHi'))
		{
			$paths = [
				JPATH_ROOT . '/plugins/content/geshiall/geshi/geshi.php',
				JPATH_ROOT . '/plugins/content/geshi/geshi/geshi.php',
			];

			foreach ($paths as $path)
			{
				if (!class_exists('GeSHi') && is_file($path))
				{
					require_once $path;
				}
			}
		}

		if ($highlight && class_exists('GeSHi'))
		{
			$geshi = new GeSHi($bbcode->UnHTMLEncode($content), $type);
			$geshi->enable_keyword_links(false);
			$code = $geshi->parse_code();
		}
		else
		{
			$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$code = '<pre lang="xml:' . $type . '">' . $content . '</pre>';
		}

		return '<div class="highlight">' . $code . '</div>';
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function doTableau($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			$bbcode->autoLink_disable++;

			return true;
		}

		$bbcode->autoLink_disable--;

		if (!$content)
		{
			return '';
		}

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return '[tableau]';
		}

		$config = KunenaFactory::getConfig();

		$maxwidth  = (int) $config->rteWidth;
		$maxheight = (int) (isset($params["height"]) && is_numeric($params["height"])) ? $params["height"] : $config->rteHeight;

		if (preg_match('/(https?:\/\/.*?)\/(?:.*\/)*(.*\/.*)\?.*:toolbar=(yes|no)/', $content, $matches))
		{
			$tableauserver = $matches[1];
			$vizualization = $matches[2];
			$toolbar       = $matches[3];

			$layout = KunenaLayout::factory('BBCode/Tableau');

			if ($layout->getPath())
			{
				return (string) $layout
					->set('params', $params)
					->set('server', $tableauserver)
					->set('width', $maxwidth)
					->set('height', $maxheight)
					->set('content', $vizualization)
					->set('toolbar', $toolbar);
			}
		}

		return '';
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string|void
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoVideo($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			$bbcode->autoLink_disable++;

			return true;
		}

		$config = KunenaFactory::getTemplate()->params;

		if (!$content || KunenaFactory::getTemplate()->isHmvc() && !$config->get('Video'))
		{
			return '';
		}

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return '[video]';
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$vid_minwidth  = 200;
		$vid_minheight = 44; // Min. display size
		$vid_sizemax   = 100; // max. display zoom in percent

		$vid ["type"]  = (isset($params ["type"])) ? StringHelper::strtolower($params ["type"]) : '';
		$vid ["param"] = (isset($params ["param"])) ? $params ["param"] : '';

		if (!$vid ["type"])
		{
			$vid_players = ['divx' => 'divx', 'flash' => 'swf', 'mediaplayer' => 'avi,mp3,wma,wmv', 'quicktime' => 'mov,qt,qti,qtif,qtvr', 'realplayer', 'rm'];

			foreach ($vid_players as $vid_player => $vid_exts)
			{
				foreach (explode(',', $vid_exts) as $vid_ext)
				{
					if (preg_match('/^(.*\.' . $vid_ext . ')$/i', $content) > 0)
					{
						$vid ["type"] = $vid_player;
						break 2;
					}
				}
			}

			unset($vid_players);
		}

		if (!$vid ["type"])
		{
			$vid_auto = preg_match('#^https?://.*?([^.]*)\.[^.]*(/|$)#u', $content, $vid_regs);

			if ($vid_auto)
			{
				$vid ["type"] = StringHelper::strtolower($vid_regs [1]);

				switch ($vid ["type"])
				{
					case 'wideo' :
						$vid ["type"] = 'wideo.fr';
						break;
				}
			}
		}

		$vid_providers = [

			'bofunk' => ['flash', 446, 370, 0, 0, 'http://www.bofunk.com/e/%vcode%', '', ''],

			'break' => ['flash', 464, 392, 0, 0, 'http://embed.break.com/%vcode%', '', ''],

			'clipfish' => ['flash', 464, 380, 0, 0, 'https://www.clipfish.de/videoplayer.swf?as=0&videoid=%vcode%&r=1&c=0067B3', 'videoid=([\w\-]*)', ''],

			'dailymotion' => ['flash', 464, 380, 0, 0, 'http://www.dailymotion.com/swf/video/%vcode%?autoPlay=0', '\/([\w]*)_', [[6, 'wmode', 'transparent']]],

			'metacafe' => ['flash', 400, 345, 0, 0, 'http://www.metacafe.com/fplayer/%vcode%/.swf', '\/watch\/(\d*\/[\w\-]*)', [[6, 'wmode', 'transparent']]],

			'myspace' => ['iframe', 430, 346, 0, 0, 'https://media.myspace.com/play/video/%vcode%', '', [[6, 'wmode', 'transparent']]],

			'rutube' => ['flash', 400, 353, 0, 0, 'https://video.rutube.ru/%vcode%', '\.html\?v=([\w]*)'],

			'sapo' => ['flash', 400, 322, 0, 0, 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/%vcode%/mov/1', 'videos\.sapo\.pt\/([\w]*)', [[6, 'wmode', 'transparent']]],

			'veoh' => ['flash', 540, 438, 0, 0, 'http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%vcode%', '\/videos\/([\w-]*)', ''],

			'videojug' => ['flash', 400, 345, 0, 0, 'http://www.videojug.com/film/player?id=%vcode%', '', ''],

			'vimeo' => ['iframe', 400, 321, 0, 0, 'https://player.vimeo.com/video/%vcode%?color=ff0179', '\.com\/(\d*)', ''],

			'youtube' => ['iframe', 425, 355, 0, 0, 'https://www.youtube-nocookie.com/embed/%vcode%', '\/watch\?v=([\w\-]*)', [[6, 'wmode', 'transparent']]],

			'youku' => ['flash', 425, 355, 0, 0, 'http://player.youku.com/player.php/Type/Folder/Fid/18787874/Ob/1/sid/%vcode%/v.swf', '\/watch\?v=([\w\-]*)', [[6, 'wmode', 'transparent']]],

			// Cannot allow public flash objects as it opens up a whole set of vulnerabilities through hacked flash files
			//				'_default' => array ($vid ["type"], 480, 360, 0, 25, $content, '', '' )
			//
		];

		if (isset($vid_providers [$vid ["type"]]))
		{
			list($vid_type, $vid_width, $vid_height, $vid_addx, $vid_addy, $vid_source, $vid_match, $vid_par2) = (isset($vid_providers [$vid ["type"]])) ? $vid_providers [$vid ["type"]] : $vid_providers ["_default"];
		}
		else
		{
			return;
		}

		unset($vid_providers);

		if (!empty($vid_auto))
		{
			if ($vid_match && (preg_match("/$vid_match/i", $content, $vid_regs) > 0))
			{
				$content = $vid_regs [1];
			}
			else
			{
				return;
			}
		}

		$uri = Uri::getInstance();

		if ($uri->isSSL() && $vid ["type"] == 'youtube')
		{
			$vid_source = preg_replace("/^http:/", "https:", $vid_source);
		}

		$vid_source = preg_replace('/%vcode%/', $content, $vid_source);

		if (!\is_array($vid_par2))
		{
			$vid_par2 = [];
		}

		$vid_size = isset($params ["size"]) ? \intval($params ["size"]) : 0;

		if (($vid_size > 0) && ($vid_size < $vid_sizemax))
		{
			$vid_width  = (int) ($vid_width * $vid_size / 100);
			$vid_height = (int) ($vid_height * $vid_size / 100);
		}

		$vid_width  += $vid_addx;
		$vid_height += $vid_addy;

		if (!isset($params ["size"]))
		{
			if (isset($params ["width"]))
			{
				if ($params ['width'] == '1')
				{
					$params ['width'] = $vid_minwidth;
				}
			}

			if (isset($params ["width"]))
			{
				$vid_width = \intval($params ["width"]);
			}

			if (isset($params ["height"]))
			{
				if ($params ['height'] == '1')
				{
					$params ['height'] = $vid_minheight;
				}
			}

			if (isset($params ["height"]))
			{
				$vid_height = \intval($params ["height"]);
			}
		}

		switch ($vid_type)
		{
			case 'divx' :
				$vid_par1     = [[1, 'classid', 'clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616'], [1, 'codebase', 'http://go.divx.com/plugin/DivXBrowserPlugin.cab'], [4, 'type', 'video/divx'], [4, 'pluginspage', 'http://go.divx.com/plugin/download/'], [6, 'src', $vid_source], [6, 'autoplay', 'false'], [5, 'width', $vid_width], [5, 'height', $vid_height]];
				$vid_allowpar = ['previewimage'];
				break;
			case 'flash' :
				$vid_par1     = [[1, 'classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'], [1, 'codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'], [2, 'movie', $vid_source], [4, 'src', $vid_source], [4, 'type', 'application/x-shockwave-flash'], [4, 'pluginspage', 'http://www.macromedia.com/go/getflashplayer'], [6, 'quality', 'high'], [6, 'allowFullScreen', 'true'], [6, 'allowScriptAccess', 'never'], [5, 'width', $vid_width], [5, 'height', $vid_height]];
				$vid_allowpar = ['flashvars', 'wmode', 'bgcolor', 'quality'];
				break;
			case 'iframe' :
				return '<div class="embed-responsive embed-responsive-16by9"><iframe src="' . $vid_source . '" width="' . $vid_width . '" height="' . $vid_height . '" allowfullscreen style="border: 0"></iframe></div>';
				break;
			case 'mediaplayer' :
				$vid_par1     = [[1, 'classid', 'clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95'], [1, 'codebase', 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab'], [4, 'type', 'application/x-mplayer2'], [4, 'pluginspage', 'http://www.microsoft.com/Windows/MediaPlayer/'], [6, 'src', $vid_source], [6, 'autostart', 'false'], [6, 'autosize', 'true'], [5, 'width', $vid_width], [5, 'height', $vid_height]];
				$vid_allowpar = [];
				break;
			case 'quicktime' :
				$vid_par1     = [[1, 'classid', 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B'], [1, 'codebase', 'http://www.apple.com/qtactivex/qtplugin.cab'], [4, 'type', 'video/quicktime'], [4, 'pluginspage', 'http://www.apple.com/quicktime/download/'], [6, 'src', $vid_source], [6, 'autoplay', 'false'], [6, 'scale', 'aspect'], [5, 'width', $vid_width], [5, 'height', $vid_height]];
				$vid_allowpar = [];
				break;
			case 'realplayer' :
				$vid_par1     = [[1, 'classid', 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'], [4, 'type', 'audio/x-pn-realaudio-plugin'], [6, 'src', $vid_source], [6, 'autostart', 'false'], [6, 'controls', 'ImageWindow,ControlPanel'], [5, 'width', $vid_width], [5, 'height', $vid_height]];
				$vid_allowpar = [];
				break;
			default :
				return;
		}

		$vid_par3 = [];

		foreach ($params as $vid_key => $vid_value)
		{
			if (\in_array(StringHelper::strtolower($vid_key), $vid_allowpar))
			{
				array_push($vid_par3, [6, $vid_key, $bbcode->HTMLEncode($vid_value)]);
			}
		}

		$vidinternalObject = $vid_param = $vid_embed = [];

		foreach (array_merge($vid_par1, $vid_par2, $vid_par3) as $vid_data)
		{
			list($vid_key, $vid_name, $vid_value) = $vid_data;

			if ($vid_key && 1)
			{
				$vidinternalObject [$vid_name] = ' ' . $vid_name . '="' . preg_replace('/%vcode%/', $content, $vid_value) . '"';
			}

			if ($vid_key && 2)
			{
				$vid_param [$vid_name] = '<param name="' . $vid_name . '" value="' . preg_replace('/%vcode%/', $content, $vid_value) . '" />';
			}

			if ($vid_key && 4)
			{
				$vid_embed [$vid_name] = ' ' . $vid_name . '="' . preg_replace('/%vcode%/', $content, $vid_value) . '"';
			}
		}

		$tag_new = '<div class="embed-responsive embed-responsive-16by9"> <object';

		foreach ($vidinternalObject as $vid_data)
		{
			$tag_new .= $vid_data;
		}

		$tag_new .= '>';

		foreach ($vid_param as $vid_data)
		{
			$tag_new .= $vid_data;
		}

		$tag_new .= '<embed';

		foreach ($vid_embed as $vid_data)
		{
			$tag_new .= $vid_data;
		}

		$tag_new .= ' /></object></div>';

		return $tag_new;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public function DoAttachment($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$attachments = null;

		if ($bbcode->parent instanceof KunenaMessage)
		{
			$attachments = $bbcode->parent->getAttachments();
		}
		elseif (\is_object($bbcode->parent) && isset($bbcode->parent->attachments))
		{
			$attachments = &$bbcode->parent->attachments;
		}

		$attachment = null;

		if (!empty($default))
		{
			$attachment = KunenaAttachmentHelper::get($default);
			unset($attachments [$attachment->id]);
		}
		elseif (empty($content))
		{
			$attachment = array_shift($attachments);
		}
		elseif (!empty($attachments))
		{
			foreach ($attachments as $att)
			{
				if ($att->getFilename() == $content)
				{
					$attachment = $att;
					unset($attachments [$att->id]);
					break;
				}
			}
		}

		// Display tag in activity streams etc..
		if (!isset($attachments) || !empty($bbcode->parent->forceMinimal))
		{
			if ($attachment->isImage())
			{
				$hide = KunenaFactory::getConfig()->showImgForGuest == 0 && Factory::getApplication()->getIdentity()->id == 0;

				if (!$hide)
				{
					return "<div class=\"kmsgimage\">{$attachment->getImageLink()}</div>";
				}
			}
			elseif ($attachment->isVideo())
			{
				$hide = KunenaFactory::getConfig()->showFileForGuest == 0 && Factory::getApplication()->getIdentity()->id == 0;

				if (!$hide)
				{
					return "<div class=\"kmsgvideo\">{$attachment->getUrl()}</div>";
				}
			}
			else
			{
				$hide = KunenaFactory::getConfig()->showFileForGuest == 0 && Factory::getApplication()->getIdentity()->id == 0;

				if (!$hide)
				{
					return "<div class=\"kmsgattach\"><h4>" . Text::_('COM_KUNENA_FILEATTACH') . "</h4>" . Text::_('COM_KUNENA_FILENAME') . " <a href=\"" . $attachment->getUrl() . "\" target=\"_blank\" rel=\"nofollow\">" . $attachment->filename . "</a><br />" . Text::_('COM_KUNENA_FILESIZE') . ' ' . number_format(\intval($attachment->size) / 1024, 0, '', ',') . ' KB' . "</div>";
				}
			}
		}

		if (!$attachment && !empty($bbcode->parent->inline_attachments))
		{
			foreach ($bbcode->parent->inline_attachments as $att)
			{
				if ($att->getFilename() == trim(strip_tags($content)))
				{
					$attachment = $att;
					break;
				}
			}
		}

		if (!$attachment)
		{
			return $bbcode->HTMLEncode($content);
		}

		return $this->renderAttachment($attachment, $bbcode);
	}

	/**
	 * @param   KunenaAttachment  $attachment    attachment
	 * @param   object            $bbcode        bbcode
	 * @param   bool              $displayImage  display image
	 *
	 * @return  string|void
	 *
	 * @since   Kunena 6.0
	 * @throws null
	 * @throws Exception
	 */
	protected function renderAttachment(KunenaAttachment $attachment, object $bbcode, $displayImage = true)
	{
		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$layout                                              = KunenaLayout::factory('BBCode/Attachment')
			->set('attachment', $attachment)
			->set('canLink', $bbcode->autoLink_disable == 0);
		$config                                              = KunenaConfig::getInstance();
		$bbcode->parent->inline_attachments[$attachment->id] = $attachment;

		if (!$attachment->exists() || !$attachment->getPath())
		{
			return (string) $layout->setLayout('deleted');
		}

		if (!$attachment->isAuthorised() && !$config->showImgForGuest && $attachment->id != '0')
		{
			return;
		}

		if (!$attachment->isAuthorised())
		{
			return (string) $layout->setLayout('unauthorised');
		}

		if ($displayImage && $attachment->isImage())
		{
			$layout->setLayout('image');
		}

		return (string) $layout;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public function DoFile($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return '[ ' . basename(!empty($params ["name"]) ? $params ["name"] : trim(strip_tags($content))) . ' ]';
		}

		// Make sure that filename does not contain path or URL.
		$filename = basename(!empty($params['name']) ? $params['name'] : $bbcode->UnHTMLEncode(trim(strip_tags($content))));
		$path     = "attachments/legacy/files/{$filename}";
		$filepath = KPATH_MEDIA . '/' . $path;
		$fileurl  = KURL_MEDIA . '/' . $path;

		// Legacy attachments support.
		if (isset($bbcode->parent->attachments))
		{
			$attachments = &$bbcode->parent->attachments;

			foreach ($attachments as $id => $attachment)
			{
				if ($attachment->getFilename() == $filename && $attachment->folder == 'media/kunena/attachments/legacy/files')
				{
					unset($attachments[$id]);

					return $this->renderAttachment($attachment, $bbcode, false);
				}
			}
		}

		$layout = KunenaLayout::factory('BBCode/File')
			->set('url', null)
			->set('filename', null)
			->set('size', 0)
			->set('canLink', $bbcode->autoLink_disable == 0);

		if (Factory::getApplication()->getIdentity()->id == 0 && KunenaFactory::getConfig()->showFileForGuest == 0)
		{
			// Hide between content from non registered users
			return (string) $layout
				->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'))
				->setLayout('unauthorised');
		}

		$layout->set('filename', $filename);
		$layout->set('size', isset($params['size']) ? $params['size'] : 0);

		if (!is_file($filepath))
		{
			// File does not exist (or URL was pointing somewhere else).
			$layout
				->set('title', Text::sprintf('COM_KUNENA_ATTACHMENT_DELETED', $bbcode->HTMLEncode($filename)))
				->setLayout('deleted');
		}
		else
		{
			$layout
				->set('title', Text::_('COM_KUNENA_FILEATTACH'))
				->set('url', $fileurl)
				->set('size', fileSize($filepath));
		}

		return (string) $layout;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 * @throws  null
	 */
	public function DoImage($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$fileurl = $bbcode->UnHTMLEncode(trim(strip_tags($content)));

		if (!$bbcode->IsValidURL($fileurl, false, true))
		{
			return htmlspecialchars($params['_tag'], ENT_COMPAT, 'UTF-8') . $content . htmlspecialchars($params['_endtag'], ENT_COMPAT, 'UTF-8');
		}

		$filename = basename($fileurl);

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return "<a href=\"" . $bbcode->HTMLEncode($fileurl) . "\" rel=\"nofollow\" target=\"_blank\">" . $bbcode->HTMLEncode($filename) . '</a>';
		}

		// Legacy attachments support.
		if (isset($bbcode->parent->attachments) && strpos($fileurl, '/media/kunena/attachments/legacy/images/'))
		{
			// Remove attachment from the attachments list and show it if it exists.
			$attachments = &$bbcode->parent->attachments;

			foreach ($attachments as $id => $attachment)
			{
				if ($attachment->getFilename() == $filename && $attachment->folder == 'media/kunena/attachments/legacy/images')
				{
					unset($attachments[$id]);

					return $this->renderAttachment($attachment, $bbcode);
				}
			}
		}

		preg_match('/[img(\s*(?!alt)([\w\-\.]+\s*\/?]/', $params['_tag'], $matches);
		$matches = rtrim($matches[0], "]");

		$config = KunenaFactory::getConfig();
		$layout = KunenaLayout::factory('BBCode/Image')
			->set('title', Text::_('COM_KUNENA_FILEATTACH'))
			->set('url', null)
			->set('filename', null)
			->set('size', isset($params['size']) ? $params['size'] : 0)
			->set('alt', isset($params['alt']) ? $matches : 0)
			->set('canLink', $bbcode->autoLink_disable == 0);

		if (Factory::getApplication()->getIdentity()->id == 0 && $config->showImgForGuest == 0)
		{
			// Hide between content from non registered users.
			return (string) $layout->set('title', Text::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'))->setLayout('unauthorised');
		}

		// Obey image security settings.
		if ($config->bbcodeImgSecure != 'image')
		{
			if ($bbcode->autoLink_disable == 0 && !preg_match("/\\.(?:gif|jpeg|jpg|jpe|png)$/ui", $fileurl))
			{
				// If the image has not legal extension, return it as link or text.
				if ($config->bbcodeImgSecure == 'link')
				{
					if (!preg_match('`^(/|https?://)`', $fileurl))
					{
						$fileurl = 'http://' . $fileurl;
					}

					// TODO: call URL layout instead..
					return "<a href=\"" . $bbcode->HTMLEncode($fileurl) . "\" rel=\"nofollow\" target=\"_blank\">" . $bbcode->HTMLEncode($fileurl) . '</a>';
				}
				else
				{
					return $bbcode->HTMLEncode($fileurl);
				}
			}
		}

		return (string) $layout->set('url', $fileurl);
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoTerminal($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		$layout = KunenaLayout::factory('BBCode/Terminal');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('content', $content)
				->set('params', $params);
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoTweet($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$config = KunenaFactory::getTemplate()->params;

		if (KunenaFactory::getTemplate()->isHmvc() && !$config->get('Twitter'))
		{
			return false;
		}

		$tweetid = trim($content);

		if (!is_numeric($tweetid))
		{
			return false;
		}

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal))
		{
			return "<a href=\"https://twitter.com/kunena/status/" . $tweetid . "\" rel=\"nofollow\" target=\"_blank\">" . Text::_('COM_KUNENA_LIB_BBCODE_TWEET_STATUS_LINK') . "</a>";
		}

		return $this->renderTweet($tweetid);
	}

	/**
	 * Render the tweet by loading the right layout
	 *
	 * @param   int  $tweetid  The tweet id to render in layout
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function renderTweet(int $tweetid): string
	{
		$tweet = $this->getTweet($tweetid);

		$layout = KunenaLayout::factory('BBCode/twitter');

		if ($tweet->error === false)
		{
			if ($layout->getPath())
			{
				return (string) $layout
					->set('tweetid', $tweet->id_str)
					->set('user_profile_url_normal', $tweet->user->profile_image_url)
					->set('user_profile_url_big', $tweet->user->profile_image_url_big)
					->set('user_name', $tweet->user->name)
					->set('user_screen_name', $tweet->user->screen_name)
					->set('tweet_created_at', $tweet->created_at)
					->set('tweet_text', $tweet->text)
					->set('retweet_count', $tweet->retweet_count)
					->set('favorite_count', $tweet->favorite_count)
					->set('verified', $tweet->user->verified)
					->setLayout('default');
			}
		}
		else
		{
			return '<b>' . $tweet->error . '</b>';
		}

		return false;
	}

	/**
	 * Get JSON tweet data by using OAuth 2.0 authentication
	 *
	 * @param   int  $tweetid  The tweet ID to query against twitter API
	 *
	 * @return  object
	 *
	 * @since   Kunena 6.0
	 * @throws Exception
	 */
	protected function getTweet(int $tweetid)
	{
		// FIXME: use AJAX instead...
		$config          = KunenaFactory::getConfig();
		$uri             = Uri::getInstance();
		$consumer_key    = trim($config->twitterConsumerKey);
		$consumer_secret = trim($config->twitterConsumerSecret);

		if (File::exists(JPATH_CACHE . '/kunena_tweet/kunenatweetdisplay-' . $tweetid . '.json'))
		{
			$tweet_data = file_get_contents(JPATH_CACHE . '/kunena_tweet/kunenatweetdisplay-' . $tweetid . '.json');

			if ($tweet_data !== false)
			{
				return json_decode($tweet_data);
			}
		}

		if (!empty($consumer_key) && !empty($consumer_secret) && empty($this->token))
		{
			$bearer_token_credentials     = $consumer_key . ":" . $consumer_secret;
			$b64_bearer_token_credentials = base64_encode($bearer_token_credentials);

			$url = 'https://api.twitter.com/oauth2/token';

			$options = new Registry;

			$transport = new StreamTransport($options);

			// Create a 'stream' transport.
			$http = new Http($options, $transport);

			$headers = [
				'Authorization' => "Basic " . $b64_bearer_token_credentials,
			];

			$data     = "grant_type=client_credentials";
			$response = $http->post($url, $data, $headers, '10');

			if ($response->code == 200)
			{
				$this->token = json_decode($response->body)->access_token;
			}
			else
			{
				$tweet        = new stdClass;
				$tweet->error = Text::_('COM_KUNENA_LIB_BBCODE_TWITTER_COULD_NOT_GET_TOKEN');

				return $tweet;
			}
		}
		elseif (empty($consumer_key) || empty($consumer_secret))
		{
			$tweet        = new stdClass;
			$tweet->error = Text::_('COM_KUNENA_LIB_BBCODE_TWITTER_CONSUMMER_KEY_SECRET_INVALID');

			return $tweet;
		}

		if (!empty($this->token))
		{
			$url = 'https://api.twitter.com/1.1/statuses/show.json?id=' . $tweetid;

			$options = new Registry;

			$transport = new StreamTransport($options);

			// Create a 'stream' transport.
			$http = new Http($options, $transport);

			$headers = [
				'Authorization' => "Bearer " . $this->token,
			];

			$response = $http->get($url, $headers, '10');

			if ($response->code == 200)
			{
				$tweet_data = json_decode($response->body);

				if ($uri->isSSL())
				{
					$tweet_data->user->profile_image_url = $tweet_data->user->profile_image_url_https;
				}

				$tweet_data->user->profile_image_url_big = str_replace('normal', 'bigger', $tweet_data->user->profile_image_url);

				if (!empty($tweet_data->entities->urls))
				{
					foreach ($tweet_data->entities->urls as $url)
					{
						if (isset($url->display_url))
						{
							$d_url = $url->display_url;
						}
						else
						{
							$d_url = $url->url;
						}

						// We need to check to verify that the URL has the protocol, just in case
						if (strpos($url->url, 'http') !== 0)
						{
							// Prepend http since there's no protocol
							$link = 'http://' . $url->url;
						}
						else
						{
							$link = $url->url;
						}

						$tweet_data->text = str_replace($url->url, '<a href="' . $link . '" target="_blank" rel="nofollow noopener noreferrer">' . $d_url . '</a>', $tweet_data->text);
					}
				}

				if (!empty($tweet_data->entities->user_mentions))
				{
					foreach ($tweet_data->entities->user_mentions as $mention)
					{
						$tweet_data->text = str_replace('@' . $mention->screen_name, '<a href="https://twitter.com/' . $mention->screen_name . '" target="_blank" rel="nofollow noopener noreferrer">@' . $mention->screen_name . '</a>', $tweet_data->text);
					}
				}

				if (!empty($tweet_data->entities->hashtags))
				{
					foreach ($tweet_data->entities->hashtags as $hashtag)
					{
						$tweet_data->text = str_replace('#' . $hashtag->text, '<a href="https://twitter.com/hashtag/' . $hashtag->text . '?src=hash" target="_blank" rel="nofollow noopener noreferrer">#' . $hashtag->text . '</a>', $tweet_data->text);
					}
				}

				if (!empty($tweet_data->extended_entities->media))
				{
					foreach ($tweet_data->extended_entities->media as $media)
					{
						$tweet_data->text = str_replace($tweet_data->extended_entities->media[0]->url, '', $tweet_data->text);

						if ($media->type == 'photo')
						{
							if ($uri->isSSL())
							{
								$tweet_data->text .= '<img loading=lazy src="' . $media->media_url_https . '" alt="tweet" />';
							}
							else
							{
								$tweet_data->text .= '<img loading=lazy src="' . $media->media_url . '" alt="tweet" />';
							}
						}
						elseif ($media->type == 'video')
						{
							if ($uri->isSSL())
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img loading=lazy src="' . $media->media_url_https . '" alt="tweet" /></a>';
							}
							else
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img loading=lazy src="' . $media->media_url . '" alt="tweet" /></a>';
							}
						}
						elseif ($media->type == 'animated_gif')
						{
							if ($uri->isSSL())
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img loading=lazy src="' . $media->media_url_https . '" alt="tweet" /></a>';
							}
							else
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img loading=lazy src="' . $media->media_url . '" alt="tweet" /></a>';
							}
						}
					}
				}

				if (!Folder::exists(JPATH_CACHE . '/kunena_tweet'))
				{
					Folder::create(JPATH_CACHE . '/kunena_tweet');
				}

				$tweet_data->error = false;

				file_put_contents(JPATH_CACHE . '/kunena_tweet/kunenatweetdisplay-' . $tweetid . '.json', json_encode($tweet_data));

				return $tweet_data;
			}
			else
			{
				$tweet        = new stdClass;
				$tweet->error = Text::_('COM_KUNENA_LIB_BBCODE_TWITTER_INVALID_TWEET_ID');

				return $tweet;
			}
		}

		return false;
	}

	/**
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 */
	public function DoSoundcloud($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($content))
		{
			// Display tag in activity streams etc..
			if (!empty($bbcode->parent->forceMinimal))
			{
				return "<a href=\"" . $content . "\" rel=\"nofollow\" target=\"_blank\">" . $content . '</a>';
			}

			$content = strip_tags($content);

			$url = trim($content);

			if (!preg_match('#^(/|https?:|ftp:)#ui', $url))
			{
				// Add scheme to raw domain URLs.
				$url = "https://{$content}";
			}

			$url_parsed = parse_url($url);

			if ($url_parsed['host'] == 'soundcloud.com')
			{
				return '<iframe allowtransparency="true" width="100%" height="350" style="border: 0" src="https://w.soundcloud.com/player/?url=' . $content . '&amp;auto_play=false&amp;visual=true"></iframe><br />';
			}
		}

		return false;
	}

	/**
	 * Handle private bbcode tag in the message
	 *
	 * @param   mixed  $bbcode   bbcode
	 * @param   mixed  $action   action
	 * @param   mixed  $name     name
	 * @param   mixed  $default  default
	 * @param   mixed  $params   params
	 * @param   mixed  $content  content
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function DoPrivate($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCode::BBCODE_CHECK)
		{
			return true;
		}

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited)
		{
			return "[{$name}]{$content}";
		}

		// Display nothing in activity streams etc..
		if (!empty($bbcode->parent->forceSecure))
		{
			return '';
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$me        = KunenaUserHelper::getMyself();
		$message   = $this->getMessage();
		$moderator = $me->userid && $me->isModerator($message ? $message->getCategory() : null);

		if (isset($bbcode->parent->message->userid))
		{
			$message_userid = $bbcode->parent->message->userid;
		}
		else
		{
			$message_userid = $bbcode->parent->userid;
		}

		// Set variable to avoid issue where isn't a private message
		if (!isset($bbcode->parent->pm))
		{
			$pm = '';
		}
		else
		{
			foreach ($bbcode->parent->pm as $privatemessage)
			{
				$pm = $privatemessage->displayField('body');
			}
		}

		if (($me->userid && $message_userid == $me->userid) || $moderator)
		{
			$layout = KunenaLayout::factory('BBCode/Private');

			if ($layout->getPath())
			{
				return (string) $layout
					->set('me', $me)
					->set('content', $pm)
					->set('params', $params);
			}
		}
		else
		{
			return '<div class="kmsgtext-confidentialguests">' . Text::_('COM_KUNENA_BBCODE_SECURE_TEXT_GUESTS') . '</div>';
		}

		return false;
	}
}
