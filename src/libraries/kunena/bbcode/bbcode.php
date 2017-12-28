<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      BBCode
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die ();

use Joomla\String\StringHelper;

require_once KPATH_FRAMEWORK . '/external/nbbc/nbbc.php';
jimport('joomla.utilities.string');

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Class KunenaBbcode
 *
 * @since   2.0
 */
class KunenaBbcode extends NBBC_BBCode
{
	public $autolink_disable = 0;

	/**
	 * @var object
	 */
	public $parent = null;

	/**
	 * Use KunenaBbcode::getInstance() instead.
	 *
	 * @param bool $relative
	 *
	 * @internal
	 */
	public function __construct($relative = true)
	{
		parent::__construct();
		$this->defaults  = new KunenaBbcodeLibrary;
		$this->tag_rules = $this->defaults->default_tag_rules;

		$this->smileys = $this->defaults->default_smileys;

		if (empty($this->smileys))
		{
			$this->SetEnableSmileys(false);
		}

		$this->SetSmileyDir(JPATH_ROOT);
		$this->SetSmileyURL($relative ? JUri::root(true) : rtrim(JUri::root(), '/'));
		$this->SetDetectURLs(true);
		$this->SetURLPattern(array($this, 'parseUrl'));
		$this->SetURLTarget('_blank');

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$dispatcher->trigger('onKunenaBbcodeConstruct', array($this));
	}

	/**
	 * Get global instance from BBCode parser.
	 *
	 * @param bool $relative
	 *
	 * @return mixed
	 */
	public static function getInstance($relative = true)
	{
		static $instance = false;

		if (!isset($instance[intval($relative)]))
		{
			$instance[intval($relative)] = new KunenaBbcode ($relative);
		}

		$instance[intval($relative)]->autolink_disable = 0;

		return $instance[intval($relative)];
	}

	/**
	 * @param $params
	 *
	 * @return string
	 */
	public function parseUrl($params)
	{
		$url  = $params['url'];
		$text = $params['text'];

		$config = KunenaFactory::getConfig();
		if ($config->autolink)
		{
			if (preg_match('#^mailto:#ui', $url))
			{
				// Cloak email addresses
				$email = substr($text, 7);

				$layout = KunenaLayout::factory('BBCode/Email');

				if ($layout->getPath())
				{
					return (string) $layout
						->set('email', $email)
						->set('mailto', $this->IsValidEmail($email));
				}

				if ($this->canCloakEmail($params))
				{
					return JHtml::_('email.cloak', $email, $this->IsValidEmail($email));
				}
				else
				{
					return '<a href="mailto:' . $email . '">' . $email . '</a>';
				}
			}

			// Remove http(s):// from the text
			$text = preg_replace('#^http(s?)://#ui', '', $text);

			if ($config->trimlongurls)
			{
				// shorten URL text if they are too long
				$text = preg_replace('#^(.{' . $config->trimlongurlsfront . '})(.{4,})(.{' . $config->trimlongurlsback . '})$#u', '\1...\3', $text);
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

		if ($config->autoembedsoundcloud && empty($this->parent->forceMinimal) && isset($params['host']))
		{
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if (strstr($params['host'], 'soundcloud.') && !empty($path[1]))
			{
				return '<iframe allowtransparency="true" width="100%" height="350" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . $params['url'] . '&amp;auto_play=false&amp;visual=true"></iframe><br />';
			}
		}

		if ($config->autoembedyoutube && empty($this->parent->forceMinimal) && isset($params['host']))
		{
			// convert youtube links to embedded player
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
				$uri = JURI::getInstance();

				if ($uri->isSSL())
				{
					return '<div class="embed-responsive embed-responsive-16by9"><iframe width="425" height="344" src="https://www.youtube.com/embed/' . urlencode($video) . '" frameborder="0" allowfullscreen></iframe></div>';
				}
				else
				{
					return '<div class="embed-responsive embed-responsive-16by9"><iframe width="425" height="344" src="http://www.youtube.com/embed/' . urlencode($video) . '" frameborder="0" allowfullscreen></iframe></div>';
				}
			}
		}

		if ($config->autoembedebay && empty($this->parent->forceMinimal) && isset($params['host']) && strstr($params['host'], '.ebay.'))
		{
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

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
					// convert ebay item to embedded widget
					return KunenaBbcodeLibrary::renderEbayLayout($itemid);
				}

				return;
			}

			parse_str($params['query'], $query);

			// FIXME: ebay search and seller listings are not supported.
			if (isset($path[1]) && $path[1] == 'sch' && !empty($query['_nkw']))
			{
				// Convert ebay search to embedded widget
				KunenaBbcodeLibrary::renderEbayLayout($itemid);

				// TODO: Remove in Kunena 4.0
				return '<object width="355" height="300"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebay_language . '&mode=search&query='
					. urlencode($query['_nkw']) . '&campid=' . $config->ebay_affiliate_id . '" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang='
					. $config->ebay_language . '&mode=search&query=' . urlencode($query['_nkw']) . '&campid=' . $config->ebay_affiliate_id . '"></embed></object>';

			}

			if (strstr($params['host'], 'myworld.') && !empty($path[1]))
			{
				// convert seller listing to embedded widget

				KunenaBbcodeLibrary::renderEbayLayout($itemid);

				// TODO: Remove in Kunena 4.0
				return '<object width="355" height="355"><param name="movie" value="http://togo.ebay.com/togo/seller.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang='
					. $config->ebay_language . '&seller=' . urlencode($path[1]) . '&campid=' . $config->ebay_affiliate_id . '" /><embed src="http://togo.ebay.com/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.com/togo/&lang='
					. $config->ebay_language . '&seller=' . urlencode($path[1]) . '&campid=' . $config->ebay_affiliate_id . '"></embed></object>';
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

		if ($config->autolink)
		{
			$layout = KunenaLayout::factory('BBCode/URL');

			if ($layout->getPath())
			{
				return (string) $layout
					->set('content', $text)
					->set('url', $url)
					->set('target', $this->url_target);
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
	 * @param $string
	 *
	 * @return array
	 */
	function Internal_AutoDetectURLs($string)
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
				[^\s`!()\[\]{};:\'"\.,<>?«»“”‘’]
			)
		)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE);

		$output = array();

		foreach ($search as $index => $token)
		{
			if ($index & 1)
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
				// Now we need to apply the $this->url_pattern template to turn it into HTML.
				$params = Joomla\Uri\UriHelper::parse_url($url);

				if (!$invalid && substr($url, 0, 7) == 'mailto:')
				{
					$email = StringHelper::substr($url, 7);

					if ($this->canCloakEmail($params))
					{
						$output[$index] = JHtml::_('email.cloak', $email, $this->IsValidEmail($email));
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
					$output[$index] = $this->FillTemplate($this->url_pattern, $params);
				}
			}
			else
			{
				$output[$index] = $token;
			}
		}

		return $output;
	}

	/**
	 * @param $params
	 *
	 * @return bool
	 */
	public function canCloakEmail(&$params)
	{

		if (JPluginHelper::isEnabled('content', 'emailcloak'))
		{
			$plugin = JPluginHelper::getPlugin('content', 'emailcloak');
			$params = new JRegistry($plugin->params);

			if ($params->get('mode', 1))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * @see BBCode::IsValidURL()
	 * Regular expression taken from https://gist.github.com/729294
	 *
	 * @param      $string
	 * @param bool $email_too
	 * @param bool $local_too
	 *
	 * @return bool
	 */
	public function IsValidURL($string, $email_too = true, $local_too = false)
	{
		static $re = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';

		if (empty($string)) return false;
		if ($local_too && $string[0] == '/') $string = 'http://www.domain.com' . $string;
		if ($email_too && substr($string, 0, 7) == "mailto:") return $this->IsValidEmail(substr($string, 7));
		if (preg_match($re, $string)) return true;

		return false;
	}
}

/**
 * Class KunenaBbcodeLibrary
 */
class KunenaBbcodeLibrary extends BBCodeLibrary
{
	/**
	 * The bearer token to get tweet data
	 *
	 * @var string
	 * @since  K4.0
	 */
	public $token = null;

	var $default_smileys = array();

	public $mapid = 0;

	var $default_tag_rules = array(
		'b' => array(
			'simple_start' => "<b>",
			'simple_end'   => "</b>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<b>",
			'plain_end'    => "</b>",
		),

		'i' => array(
			'simple_start' => "<i>",
			'simple_end'   => "</i>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'u' => array(
			'simple_start' => "<u>",
			'simple_end'   => "</u>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<u>",
			'plain_end'    => "</u>",
		),

		's' => array(
			'simple_start' => "<strike>",
			'simple_end'   => "</strike>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'strike' => array(
			'simple_start' => "<strike>",
			'simple_end'   => "</strike>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'tt' => array(
			'simple_start' => "<tt>",
			'simple_end'   => "</tt>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'pre' => array(
			'simple_start' => "<pre>",
			'simple_end'   => "</pre>",
			'class'        => 'block',
			'allow_in'     => array('listitem', 'block', 'columns'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'font' => array(
			'mode'     => BBCODE_MODE_LIBRARY,
			'allow'    => array('_default' => '/^[a-zA-Z0-9._ -]+$/'),
			'method'   => 'DoFont',
			'class'    => 'inline',
			'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
		),

		'color' => array(
			'mode'     => BBCODE_MODE_ENHANCED,
			'allow'    => array('_default' => '/^#?[a-zA-Z0-9._ -]+$/'),
			'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
			'class'    => 'inline',
			'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
		),

		'size' => array(
			'mode'     => BBCODE_MODE_LIBRARY,
			'method'   => 'DoSize',
			'allow'    => array('_default' => '/^[0-9.]+(px|em|pt|%)?$/D'),
			'class'    => 'inline',
			'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
		),

		'sup' => array(
			'simple_start' => "<sup>",
			'simple_end'   => "</sup>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
		),

		'sub' => array(
			'simple_start' => "<sub>",
			'simple_end'   => "</sub>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link')
		),

		'spoiler' => array(
			'mode'        => BBCODE_MODE_LIBRARY,
			'method'      => 'DoSpoiler',
			'class'       => 'block',
			'allow_in'    => array('listitem', 'block', 'columns'),
			'content'     => BBCODE_REQUIRED,
			'plain_start' => "\nSpoiler:\n<i>",
			'plain_end'   => "</i>",
		),

		'hide' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoHide',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_REQUIRED,
			'plain_content' => array(),
		),

		'confidential' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoConfidential',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_REQUIRED,
			'plain_content' => array(),
		),

		'map' => array(
			'mode'     => BBCODE_MODE_LIBRARY,
			'method'   => 'DoMap',
			'class'    => 'block',
			'allow'    => array('type' => '/^[\w\d.-_]*$/', 'zoom' => '/^\d*$/', 'control' => '/^\d*$/'),
			'allow_in' => array('listitem', 'block', 'columns'),
			'content'  => BBCODE_VERBATIM,
		),

		'ebay' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoEbay',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "[ebay]",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'article' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoArticle',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_REQUIRED,
			'plain_start'   => "\n[article]\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'tableau' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoTableau',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "\n[tableau]\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'video' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoVideo',
			'allow'         => array('type' => '/^[\w\d.-_]*$/', 'param' => '/^[\w]*$/', 'size' => '/^\d*$/', 'width' => '/^\d*$/', 'height' => '/^\d*$/'),
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "[video]",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'img' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoImage',
			'allow'         => array('size' => '/^\d*$/'),
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns', 'link'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "[image]",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'file' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoFile',
			'allow'         => array('size' => '/^\d*$/'),
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "\n[file]\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'attachment' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoAttachment',
			'allow'         => array('_default' => '/^\d*$/'),
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "\n[attachment]\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'highlight' => array(
			'simple_start' => "<span style='font-weight: 700;'>",
			'simple_end'   => "</span>",
			'class'        => 'inline',
			'allow_in'     => array('listitem', 'block', 'columns', 'inline', 'link'),
			'plain_start'  => "<i>",
			'plain_end'    => "</i>",
		),

		'acronym' => array(
			'mode'     => BBCODE_MODE_ENHANCED,
			'template' => '<span class="bbcode_acronym" title="{$_default/e}">{$_content/v}</span>',
			'class'    => 'inline',
			'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
		),

		'url' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoUrl',
			'class'         => 'link',
			'allow_in'      => array('listitem', 'block', 'columns', 'inline'),
			'content'       => BBCODE_REQUIRED,
			'plain_start'   => "<a href=\"{\$link}\" rel=\"nofollow\" target=\"_blank\">",
			'plain_end'     => "</a>",
			'plain_content' => array('_content', '_default'),
			'plain_link'    => array('_default', '_content'),
		),

		'email' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoEmail',
			'class'         => 'link',
			'allow_in'      => array('listitem', 'block', 'columns', 'inline'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "<a href=\"mailto:{\$link}\">",
			'plain_end'     => "</a>",
			'plain_content' => array('_content', '_default'),
			'plain_link'    => array('_default', '_content'),
		),

		'wiki' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => "DoWiki",
			'class'         => 'link',
			'allow_in'      => array('listitem', 'block', 'columns', 'inline'),
			'end_tag'       => BBCODE_PROHIBIT,
			'content'       => BBCODE_PROHIBIT,
			'plain_start'   => "<b>[",
			'plain_end'     => "]</b>",
			'plain_content' => array('title', '_default'),
			'plain_link'    => array('_default', '_content'),
		),

		'rule' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => "DoRule",
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'end_tag'       => BBCODE_PROHIBIT,
			'content'       => BBCODE_PROHIBIT,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'plain_start'   => "\n-----\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'br' => array(
			'mode'          => BBCODE_MODE_SIMPLE,
			'simple_start'  => "<br />\n",
			'simple_end'    => "",
			'class'         => 'inline',
			'allow_in'      => array('listitem', 'block', 'columns', 'inline', 'link'),
			'end_tag'       => BBCODE_PROHIBIT,
			'content'       => BBCODE_PROHIBIT,
			'before_tag'    => "s",
			'after_tag'     => "s",
			'plain_start'   => "\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'hr' => array(
			'mode'          => BBCODE_MODE_SIMPLE,
			'simple_start'  => "<hr />\n",
			'simple_end'    => "",
			'class'         => 'inline',
			'allow_in'      => array('listitem', 'block', 'columns', 'inline', 'link'),
			'end_tag'       => BBCODE_PROHIBIT,
			'content'       => BBCODE_PROHIBIT,
			'before_tag'    => "s",
			'after_tag'     => "s",
			'plain_start'   => "\n-----\n",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'left' => array(
			'simple_start'  => "\n<div class=\"bbcode_left\" style=\"text-align:left\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'right' => array(
			'simple_start'  => "\n<div class=\"bbcode_right\" style=\"text-align:right\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'center' => array(
			'simple_start'  => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'indent' => array(
			'simple_start'  => "\n<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n",
			'simple_end'    => "\n</div>\n",
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'table' => array(
			'simple_start'  => "\n<table>",
			'simple_end'    => "</table>\n",
			'class'         => 'table',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'end_tag'       => BBCODE_REQUIRED,
			'content'       => BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'tr' => array(
			'simple_start'  => "\n<tr>",
			'simple_end'    => "</tr>\n",
			'class'         => 'tr',
			'allow_in'      => array('table'),
			'end_tag'       => BBCODE_REQUIRED,
			'content'       => BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'th' => array(
			'simple_start'  => "<th>",
			'simple_end'    => "</th>",
			'class'         => 'columns',
			'allow_in'      => array('tr'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'td' => array(
			'simple_start'  => "<td>",
			'simple_end'    => "</td>",
			'class'         => 'columns',
			'allow_in'      => array('tr'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'columns' => array(
			'simple_start'  => "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n",
			'simple_end'    => "\n</td></tr></tbody></table>\n",
			'class'         => 'columns',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'end_tag'       => BBCODE_REQUIRED,
			'content'       => BBCODE_REQUIRED,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'nextcol' => array(
			'simple_start'  => "\n</td><td class=\"bbcode_column\">\n",
			'class'         => 'nextcol',
			'allow_in'      => array('columns'),
			'end_tag'       => BBCODE_PROHIBIT,
			'content'       => BBCODE_PROHIBIT,
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "",
		),

		'code' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoCode',
			'allow'         => array('type' => '/^[\w]*$/',),
			'class'         => 'code',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'before_tag'    => "sns",
			'after_tag'     => "sn",
			'before_endtag' => "sn",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'quote' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoQuote',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\nQuote:\n",
			'plain_end'     => "\n",
		),

		'list' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'class'         => 'list',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'ul' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'default'       => array('_default' => 'disc'),
			'class'         => 'list',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'ol' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoList',
			'allow'         => array('_default' => '/^[\d\w]*$/',),
			'default'       => array('_default' => '1'),
			'class'         => 'list',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'before_tag'    => "sns",
			'after_tag'     => "sns",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n",
			'plain_end'     => "\n",
		),

		'*' => array(
			'simple_start'  => "<li>",
			'simple_end'    => "</li>\n",
			'class'         => 'listitem',
			'allow_in'      => array('list'),
			'end_tag'       => BBCODE_OPTIONAL,
			'before_tag'    => "s",
			'after_tag'     => "s",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n * ",
			'plain_end'     => "\n",
		),

		'li' => array(
			'simple_start'  => "<li>",
			'simple_end'    => "</li>\n",
			'class'         => 'listitem',
			'allow_in'      => array('listitem', 'block', 'columns', 'list'),
			'before_tag'    => "s",
			'after_tag'     => "s",
			'before_endtag' => "sns",
			'after_endtag'  => "sns",
			'plain_start'   => "\n * ",
			'plain_end'     => "\n",
		),

		'terminal' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoTerminal',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'class'         => 'code',
			'allow'         => array('colortext' => '/^|#[0-9a-fA-F]+|[a-zA-Z]+$/'),
			'before_tag'    => "sns",
			'after_tag'     => "sn",
			'before_endtag' => "ns",
			'after_endtag'  => "sns",
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "\nTerminal:\n",
			'plain_end'     => "\n",
		),

		'tweet' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoTweet',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_REQUIRED,
			'plain_content' => array(),
		),

		'soundcloud' => array(
			'mode'          => BBCODE_MODE_LIBRARY,
			'method'        => 'DoSoundcloud',
			'class'         => 'block',
			'allow_in'      => array('listitem', 'block', 'columns'),
			'content'       => BBCODE_VERBATIM,
			'plain_start'   => "[soundcloud]",
			'plain_end'     => "",
			'plain_content' => array(),
		),

		'instagram' => array(
			'mode'     => BBCODE_MODE_LIBRARY,
			'method'   => 'DoInstagram',
			'allow_in' => array('listitem', 'block', 'columns'),
			'class'    => 'block',
			'allow'    => array('colortext' => '/^[\w\d.-_]*$/'),
			'content'  => BBCODE_PROHIBIT,
		),
	);

	/**
	 *
	 */
	function __construct()
	{
		if (!KunenaFactory::getConfig()->disemoticons)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT code, location FROM #__kunena_smileys";
			$db->setQuery($query);
			$smileys = $db->loadObjectList();

			$template = KunenaFactory::getTemplate();

			foreach ($smileys as $smiley)
			{
				$this->default_smileys [$smiley->code] = $template->getSmileyPath($smiley->location);
			}
		}

		// Translate plain text "Quote:"
		$this->default_tag_rules['quote']['plain_start'] = "\n" . JText::_('COM_KUNENA_LIB_BBCODE_QUOTE_TITLE') . "\n";
	}

	/**
	 * @return KunenaForumMessage|null
	 */
	protected function getMessage()
	{
		if (empty($this->parent))
		{
			return null;
		}
		elseif ($this->parent instanceof KunenaForumMessage)
		{
			return $this->parent;
		}
		elseif (isset($this->parent->message)
		)
		{
			return $this->parent->message;
		}

		return null;
	}

	/**
	 * @param KunenaBbcode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|mixed|string
	 */
	public function DoEmail($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$email     = is_string($default) ? $default : $bbcode->UnHTMLEncode($content);
		$text      = is_string($default) ? $bbcode->UnHTMLEncode($content) : $default;
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
			return JHtml::_('email.cloak', htmlspecialchars($email), $bbcode->IsValidEmail($email), htmlspecialchars($text), $bbcode->IsValidEmail($text));
		}
		else
		{
			return '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($text) . '</a>';
		}
	}

	/**
	 * Format a [url] tag by producing an <a>...</a> element.
	 * The URL only allows http, https, mailto, and ftp protocols for safety.
	 *
	 * @param KunenaBbcode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	public function DoURL($bbcode, $action, $name, $default, $params, $content)
	{
		// We can't check this with BBCODE_CHECK because we may have no URL before the content
		// has been processed.
		if ($action == BBCODE_CHECK)
		{
			$bbcode->autolink_disable++;

			return true;
		}

		$bbcode->autolink_disable--;
		$url = $default ? $default : strip_tags($bbcode->UnHTMLEncode($content));
		$url = preg_replace('# #u', '%20', $url);

		if (!preg_match('#^(/|https?:|ftp:)#ui', $url))
		{
			// Add scheme to raw domain URLs.
			$url = "http://{$url}";
		}

		if (!$bbcode->IsValidURL($url, false, true))
		{
			return htmlspecialchars($params['_tag'], ENT_COMPAT, 'UTF-8') . $content . htmlspecialchars($params['_endtag'], ENT_COMPAT, 'UTF-8');
		}

		if ($bbcode->url_targetable !== false && isset ($params['target']))
		{
			$target = $params['target'];
		}
		elseif ($bbcode->url_target !== false)
		{
			$target = $bbcode->url_target;
		}
		else
		{
			$target = '';
		}

		$layout = KunenaLayout::factory('BBCode/URL');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('content', $content)
				->set('url', $url)
				->set('target', $target);
		}
	}

	// Format a [size] tag by producing a <span> with a style with a different font-size.

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoSize($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$layout = KunenaLayout::factory('BBCode/Size');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('content', $content)
				->set('size', $default);
		}
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
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	function DoList($bbcode, $action, $name, $default, $params, $content)
	{
		// Allowed list styles, striaght from the CSS 2.1 spec.  The only prohibited
		// list style is that with image-based markers, which often slows down web sites.
		$list_styles    = Array('1' => 'decimal', '01' => 'decimal-leading-zero', 'i' => 'lower-roman', 'I' => 'upper-roman', 'a' => 'lower-alpha', 'A' => 'upper-alpha');
		$ci_list_styles = Array('circle' => 'circle', 'disc' => 'disc', 'square' => 'square', 'greek' => 'lower-greek', 'armenian' => 'armenian', 'georgian' => 'georgian');
		$ul_types       = Array('circle' => 'circle', 'disc' => 'disc', 'square' => 'square');
		$default        = trim($default);

		if (!$default)
		{
			$default = $bbcode->tag_rules [$name] ['default'] ['_default'];
		}

		if ($action == BBCODE_CHECK)
		{
			if (!is_string($default) || strlen($default) == "")
			{
				return true;
			}
			else if (isset ($list_styles [$default]))
			{
				return true;
			}
			else if (isset ($ci_list_styles [strtolower($default)]))
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

		if (!is_string($default) || strlen($default) == "")
		{
			$elem = 'ul';
		}
		else if ($default == '1')
		{
			$elem = 'ol';
		}
		else if (isset ($list_styles [$default]))
		{
			$elem = 'ol';
			$type = $list_styles [$default];
		}
		else
		{
			$default = strtolower($default);

			if (isset ($ul_types [$default]))
			{
				$elem = 'ul';
				$type = $ul_types [$default];
			}
			else if (isset ($ci_list_styles [$default]))
			{
				$elem = 'ol';
				$type = $ci_list_styles [$default];
			}
		}

		// Generate the HTML for it.
		if (strlen($type))
		{
			return "\n<$elem class=\"bbcode_list\" style=\"list-style-type:$type\">\n$content</$elem>\n";
		}
		else
		{
			return "\n<$elem class=\"bbcode_list\">\n$content</$elem>\n";
		}
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoSpoiler($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
			return '[' . ($default ? $default : JText::_('COM_KUNENA_BBCODE_SPOILER')) . ']';
		}

		$document = JFactory::getDocument();
		$title    = $default ? $default : JText::_('COM_KUNENA_BBCODE_SPOILER');
		$hidden   = ($document instanceof JDocumentHTML);

		$layout = KunenaLayout::factory('BBCode/Spoiler');

		if ($layout->getPath())
		{
			return (string) $layout
				->set('title', $title)
				->set('hidden', $hidden)
				->set('content', $content)
				->set('params', $params);
		}
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoHide($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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

		if (!JFactory::getUser()->guest)
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
			return '<br />' . JText::_('COM_KUNENA_BBCODE_HIDDENTEXT') . '<br />';
		}
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoConfidential($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
			return '<div class="kmsgtext-confidentialguests">' . JText::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT_GUESTS') . '</div>';
		}
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	public function DoMap($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$content = trim($content);

		if (empty($content))
		{
			echo '<div class="alert alert-error">' . JText::_('COM_KUNENA_LIB_BBCODE_MAP_ERROR_CITY_MISSING') . '</div>';

			return;
		}

		$config = KunenaFactory::getTemplate()->params;

		$document = JFactory::getDocument();

		// Display only link in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal) || !($document instanceof JDocumentHTML) || KunenaFactory::getTemplate()->isHmvc() && !$config->get('maps'))
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
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoEbay($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
			return '<a target="_blank" rel="noopener noreferrer" href="http://www.ebay.com/itm/' . $content . '?lang=' . $config->ebaylanguagecode . '&campid=' . $config->ebay_affiliate_id . '">www.ebay.com/itm/' . $content . '</a>';
		}

		return self::renderEbayLayout($content);
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 * @throws Exception
	 */
	function DoArticle($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_content');

		$articleid = intval($content);

		$config = KunenaFactory::getConfig();
		$user   = JFactory::getUser();
		$db     = JFactory::getDBO();
		/** @var JSite $site */
		$site = JFactory::getApplication('site');

		$query = 'SELECT a.*, u.name AS author, cc.title AS category,
			0 AS sec_pub, 0 AS sectionid, cc.published AS cat_pub, cc.access AS cat_access
			FROM #__content AS a
			LEFT JOIN #__categories AS cc ON cc.id = a.catid
			LEFT JOIN #__users AS u ON u.id = a.created_by
			WHERE a.id=' . $db->quote($articleid);
		$db->setQuery($query);
		$article = $db->loadObject();

		if ($article)
		{
			// Get credentials to check if the user has right to see the article
			$params   = $site->getParams('com_content');
			$registry = new JRegistry();
			$registry->loadString($article->attribs);
			$article->params = clone $params;
			$article->params->merge($registry);
			$params = $article->params;

			$viewlevels = $user->getAuthorisedViewLevels();

			if (!in_array($article->access, $viewlevels))
			{
				$denied = true;
			}
		}

		$html = $link = '';

		if (!$article || (!$article->cat_pub && $article->catid) || (!$article->sec_pub && $article->sectionid))
		{
			$html = JText::_('COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_UNPUBLISHED');
		}
		elseif (!empty($denied) && !$params->get('show_noauth'))
		{
			$html = JText::_('COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_NO_PERMISSIONS');
		}
		else
		{
			require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');
			$article->slug    = !empty($article->alias) ? ($article->id . ':' . $article->alias) : $article->id;
			$article->catslug = !empty($article->category_alias) ? ($article->catid . ':' . $article->category_alias) : $article->catid;
			$url              = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug));

			if (!$default)
			{
				$default = $config->article_display;
			}

			// Do not display full text if there's no permissions to display the full article.
			if (!empty($denied) && $default == 'full')
			{
				$default = 'intro';
			}

			switch ($default)
			{
				case 'full':
					if (!empty($article->fulltext))
					{
						$article->text = $article->introtext . ' ' . $article->fulltext;

						if (!empty($article->fulltext))
						{
							$link = '<a href="' . $url . '" class="readon">' . JText::sprintf('COM_KUNENA_LIB_BBCODE_ARTICLE_READ') . '</a>';
						}
						else
						{
							$link = '';
						}

						break;
					}
				// continue to intro if fulltext is empty
				case 'intro':
					if (!empty($article->introtext))
					{
						$article->text = $article->introtext;

						if (!empty($article->fulltext))
						{
							$link = '<a href="' . $url . '"class="readon">' . JText::sprintf('COM_KUNENA_LIB_BBCODE_ARTICLE_MORE') . '</a>';
						}
						else
						{
							$link = '';
						}

						break;
					}
				// continue to link if introtext is empty
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
				JPluginHelper::importPlugin('content');
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onContentPrepare', array('text', &$article, &$params, 0));
				$article->text       = JHtml::_('string.truncate', $article->text, $bbcode->output_limit - $bbcode->text_length);
				$bbcode->text_length += strlen($article->text);
				$html                = $article->text;
			}

			if (!empty($denied))
			{
				$link = '<span class="readon">' . JText::_('COM_CONTENT_REGISTER_TO_READ_MORE') . '</span>';
			}
		}

		return ($html ? '<div class="kmsgtext-article">' . $html . '</div>' : '') . $link;
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	function DoQuote($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$user  = isset($default) ? htmlspecialchars($default, ENT_COMPAT, 'UTF-8') : false;
		$wrote = '';

		if ($user)
		{
			$wrote = $user . " " . JText::_('COM_KUNENA_POST_WROTE') . ': ';
		}

		$html .= '<blockquote><p class="kmsgtext-quote">' . $wrote . $content . '</p></blockquote>';

		return $html;
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	function DoCode($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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

		$highlight = KunenaFactory::getConfig()->highlightcode && empty($bbcode->parent->forceMinimal);

		if ($highlight && !class_exists('GeSHi'))
		{
			$paths = array(
				JPATH_ROOT . '/plugins/content/geshiall/geshi/geshi.php',
				JPATH_ROOT . '/plugins/content/geshi/geshi/geshi.php'
			);

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
			$geshi = new GeSHi ($bbcode->UnHTMLEncode($content), $type);
			$geshi->enable_keyword_links(false);
			$code = $geshi->parse_code();
		}
		else
		{
			$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$code = '<pre xml:' . $type . '>' . $content . '</pre>';
		}

		return '<div class="highlight">' . $code . '</div>';
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function doTableau($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			$bbcode->autolink_disable++;

			return true;
		}

		$bbcode->autolink_disable--;

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

		$maxwidth  = (int) $config->rtewidth;
		$maxheight = (int) (isset($params["height"]) && is_numeric($params["height"])) ? $params["height"] : $config->rteheight;

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
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	function DoVideo($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			$bbcode->autolink_disable++;

			return true;
		}

		$config = KunenaFactory::getTemplate()->params;
		if (!$content || KunenaFactory::getTemplate()->isHmvc() && !$config->get('video'))
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
		$vid_minheight = 44; // min. display size
		$vid_maxwidth  = ( int ) ((KunenaFactory::getConfig()->rtewidth * 9) / 10); // Max 90% of text width
		$vid_maxheight = 720; // max. display size
		$vid_sizemax   = 100; // max. display zoom in percent

		$vid ["type"]  = (isset ($params ["type"])) ? Joomla\String\StringHelper::strtolower($params ["type"]) : '';
		$vid ["param"] = (isset ($params ["param"])) ? $params ["param"] : '';

		if (!$vid ["type"])
		{
			$vid_players = array('divx' => 'divx', 'flash' => 'swf', 'mediaplayer' => 'avi,mp3,wma,wmv', 'quicktime' => 'mov,qt,qti,qtif,qtvr', 'realplayer', 'rm');

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

			unset ($vid_players);
		}
		if (!$vid ["type"])
		{
			$vid_auto = preg_match('#^https?://.*?([^.]*)\.[^.]*(/|$)#u', $content, $vid_regs);

			if ($vid_auto)
			{
				$vid ["type"] = Joomla\String\StringHelper::strtolower($vid_regs [1]);

				switch ($vid ["type"])
				{
					case 'wideo' :
						$vid ["type"] = 'wideo.fr';
						break;
				}
			}
		}

		$vid_providers = array(

			'bofunk' => array('flash', 446, 370, 0, 0, 'http://www.bofunk.com/e/%vcode%', '', ''),

			'break' => array('flash', 464, 392, 0, 0, 'http://embed.break.com/%vcode%', '', ''),

			'clipfish' => array('flash', 464, 380, 0, 0, 'https://www.clipfish.de/videoplayer.swf?as=0&videoid=%vcode%&r=1&c=0067B3', 'videoid=([\w\-]*)', ''),

			'dailymotion' => array('flash', 464, 380, 0, 0, 'http://www.dailymotion.com/swf/video/%vcode%?autoPlay=0', '\/([\w]*)_', array(array(6, 'wmode', 'transparent'))),

			'metacafe' => array('flash', 400, 345, 0, 0, 'http://www.metacafe.com/fplayer/%vcode%/.swf', '\/watch\/(\d*\/[\w\-]*)', array(array(6, 'wmode', 'transparent'))),

			'myspace' => array('iframe', 430, 346, 0, 0, 'https://media.myspace.com/play/video/%vcode%', '', array(array(6, 'wmode', 'transparent'))),

			'rutube' => array('flash', 400, 353, 0, 0, 'https://video.rutube.ru/%vcode%', '\.html\?v=([\w]*)'),

			'sapo' => array('flash', 400, 322, 0, 0, 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/%vcode%/mov/1', 'videos\.sapo\.pt\/([\w]*)', array(array(6, 'wmode', 'transparent'))),

			'veoh' => array('flash', 540, 438, 0, 0, 'http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%vcode%', '\/videos\/([\w-]*)', ''),

			'videojug' => array('flash', 400, 345, 0, 0, 'http://www.videojug.com/film/player?id=%vcode%', '', ''),

			'vimeo' => array('iframe', 400, 321, 0, 0, 'https://player.vimeo.com/video/%vcode%?color=ff0179', '\.com\/(\d*)', ''),

			'youtube' => array('iframe', 425, 355, 0, 0, 'https://www.youtube.com/embed/%vcode%', '\/watch\?v=([\w\-]*)', array(array(6, 'wmode', 'transparent'))),

			'youku' => array('flash', 425, 355, 0, 0, 'http://player.youku.com/player.php/Type/Folder/Fid/18787874/Ob/1/sid/%vcode%/v.swf', '\/watch\?v=([\w\-]*)', array(array(6, 'wmode', 'transparent'))),

			// Cannot allow public flash objects as it opens up a whole set of vulnerabilities through hacked flash files
			//				'_default' => array ($vid ["type"], 480, 360, 0, 25, $content, '', '' )
			//
		);

		if (isset ($vid_providers [$vid ["type"]]))
		{
			list ($vid_type, $vid_width, $vid_height, $vid_addx, $vid_addy, $vid_source, $vid_match, $vid_par2) = (isset ($vid_providers [$vid ["type"]])) ? $vid_providers [$vid ["type"]] : $vid_providers ["_default"];
		}
		else
		{
			return;
		}

		unset ($vid_providers);

		if (!empty ($vid_auto))
		{
			if ($vid_match and (preg_match("/$vid_match/i", $content, $vid_regs) > 0))
			{
				$content = $vid_regs [1];
			}
			else
			{
				return;
			}
		}

		$uri = JURI::getInstance();

		if ($uri->isSSL() && $vid ["type"] == 'youtube')
		{
			$vid_source = preg_replace("/^http:/", "https:", $vid_source);
		}

		$vid_source = preg_replace('/%vcode%/', $content, $vid_source);

		if (!is_array($vid_par2))
		{
			$vid_par2 = array();
		}

		$vid_size = isset ($params ["size"]) ? intval($params ["size"]) : 0;

		if (($vid_size > 0) and ($vid_size < $vid_sizemax))
		{
			$vid_width  = ( int ) ($vid_width * $vid_size / 100);
			$vid_height = ( int ) ($vid_height * $vid_size / 100);
		}

		$vid_width  += $vid_addx;
		$vid_height += $vid_addy;

		if (!isset ($params ["size"]))
		{
			if (isset ($params ["width"]))
			{
				if ($params ['width'] == '1')
				{
					$params ['width'] = $vid_minwidth;
				}
			}

			if (isset ($params ["width"]))
			{
				$vid_width = intval($params ["width"]);
			}

			if (isset ($params ["height"]))
			{
				if ($params ['height'] == '1')
				{
					$params ['height'] = $vid_minheight;
				}
			}

			if (isset ($params ["height"]))
			{
				$vid_height = intval($params ["height"]);
			}
		}


		switch ($vid_type)
		{
			case 'divx' :
				$vid_par1     = array(array(1, 'classid', 'clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616'), array(1, 'codebase', 'http://go.divx.com/plugin/DivXBrowserPlugin.cab'), array(4, 'type', 'video/divx'), array(4, 'pluginspage', 'http://go.divx.com/plugin/download/'), array(6, 'src', $vid_source), array(6, 'autoplay', 'false'), array(5, 'width', $vid_width), array(5, 'height', $vid_height));
				$vid_allowpar = array('previewimage');
				break;
			case 'flash' :
				$vid_par1     = array(array(1, 'classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'), array(1, 'codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'), array(2, 'movie', $vid_source), array(4, 'src', $vid_source), array(4, 'type', 'application/x-shockwave-flash'), array(4, 'pluginspage', 'http://www.macromedia.com/go/getflashplayer'), array(6, 'quality', 'high'), array(6, 'allowFullScreen', 'true'), array(6, 'allowScriptAccess', 'never'), array(5, 'width', $vid_width), array(5, 'height', $vid_height));
				$vid_allowpar = array('flashvars', 'wmode', 'bgcolor', 'quality');
				break;
			case 'iframe' :
				return '<div class="embed-responsive embed-responsive-16by9"><iframe src="' . $vid_source . '" frameborder="0" width="' . $vid_width . '" height="' . $vid_height . '" allowfullscreen></iframe></div>';
				break;
			case 'mediaplayer' :
				$vid_par1     = array(array(1, 'classid', 'clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95'), array(1, 'codebase', 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab'), array(4, 'type', 'application/x-mplayer2'), array(4, 'pluginspage', 'http://www.microsoft.com/Windows/MediaPlayer/'), array(6, 'src', $vid_source), array(6, 'autostart', 'false'), array(6, 'autosize', 'true'), array(5, 'width', $vid_width), array(5, 'height', $vid_height));
				$vid_allowpar = array();
				break;
			case 'quicktime' :
				$vid_par1     = array(array(1, 'classid', 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B'), array(1, 'codebase', 'http://www.apple.com/qtactivex/qtplugin.cab'), array(4, 'type', 'video/quicktime'), array(4, 'pluginspage', 'http://www.apple.com/quicktime/download/'), array(6, 'src', $vid_source), array(6, 'autoplay', 'false'), array(6, 'scale', 'aspect'), array(5, 'width', $vid_width), array(5, 'height', $vid_height));
				$vid_allowpar = array();
				break;
			case 'realplayer' :
				$vid_par1     = array(array(1, 'classid', 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'), array(4, 'type', 'audio/x-pn-realaudio-plugin'), array(6, 'src', $vid_source), array(6, 'autostart', 'false'), array(6, 'controls', 'ImageWindow,ControlPanel'), array(5, 'width', $vid_width), array(5, 'height', $vid_height));
				$vid_allowpar = array();
				break;
			default :
				return;
		}

		$vid_par3 = array();
		foreach ($params as $vid_key => $vid_value)
		{
			if (in_array(Joomla\String\StringHelper::strtolower($vid_key), $vid_allowpar))
			{
				array_push($vid_par3, array(6, $vid_key, $bbcode->HTMLEncode($vid_value)));
			}
		}

		$vid_object = $vid_param = $vid_embed = array();

		foreach (array_merge($vid_par1, $vid_par2, $vid_par3) as $vid_data)
		{
			list ($vid_key, $vid_name, $vid_value) = $vid_data;

			if ($vid_key & 1)
			{
				$vid_object [$vid_name] = ' ' . $vid_name . '="' . preg_replace('/%vcode%/', $content, $vid_value) . '"';
			}

			if ($vid_key & 2)
			{
				$vid_param [$vid_name] = '<param name="' . $vid_name . '" value="' . preg_replace('/%vcode%/', $content, $vid_value) . '" />';
			}

			if ($vid_key & 4)
			{
				$vid_embed [$vid_name] = ' ' . $vid_name . '="' . preg_replace('/%vcode%/', $content, $vid_value) . '"';
			}
		}

		$tag_new = '<div class="embed-responsive embed-responsive-16by9"> <object';

		foreach ($vid_object as $vid_data)
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
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	function DoAttachment($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$attachments = null;

		if ($bbcode->parent instanceof KunenaForumMessage)
		{
			$attachments = $bbcode->parent->getAttachments();
		}
		elseif (is_object($bbcode->parent) && isset($bbcode->parent->attachments))
		{
			$attachments = &$bbcode->parent->attachments;
		}

		/** @var KunenaAttachment $att */
		/** @var KunenaAttachment $attachment */

		$attachment = null;
		if (!empty ($default))
		{
			$attachment = KunenaAttachmentHelper::get($default);
			unset ($attachments [$attachment->id]);
		}
		elseif (empty ($content))
		{
			$attachment = array_shift($attachments);
		}
		elseif (!empty ($attachments))
		{
			foreach ($attachments as $att)
			{
				if ($att->getFilename() == $content)
				{
					$attachment = $att;
					unset ($attachments [$att->id]);
					break;
				}
			}
		}

		// Display tag in activity streams etc..
		if (!isset($attachments) || !empty($bbcode->parent->forceMinimal))
		{
			if ($attachment->isImage())
			{
				$hide = KunenaFactory::getConfig()->showimgforguest == 0 && JFactory::getUser()->id == 0;
				if (!$hide)
				{
					return "<div class=\"kmsgimage\">{$attachment->getImageLink()}</div>";
				}
			}
			else
			{
				$hide = KunenaFactory::getConfig()->showfileforguest == 0 && JFactory::getUser()->id == 0;
				if (!$hide)
				{
					return "<div class=\"kmsgattach\"><h4>" . JText::_('COM_KUNENA_FILEATTACH') . "</h4>" . JText::_('COM_KUNENA_FILENAME') . " <a href=\"" . $attachment->getUrl() . "\" target=\"_blank\" rel=\"nofollow\">" . $attachment->filename . "</a><br />" . JText::_('COM_KUNENA_FILESIZE') . ' ' . number_format(intval($attachment->size) / 1024, 0, '', ',') . ' KB' . "</div>";
				}
			}
		}

		if (!$attachment && !empty ($bbcode->parent->inline_attachments))
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
	 * @param KunenaAttachment $attachment
	 * @param                  $bbcode
	 * @param bool             $displayImage
	 *
	 * @return string
	 */
	protected function renderAttachment(KunenaAttachment $attachment, $bbcode, $displayImage = true)
	{
		// Display nothing in subscription mails
		if (!empty($bbcode->context))
		{
			return '';
		}

		$layout                                              = KunenaLayout::factory('BBCode/Attachment')
			->set('attachment', $attachment)
			->set('canLink', $bbcode->autolink_disable == 0);
		$config                                              = KunenaConfig::getInstance();
		$bbcode->parent->inline_attachments[$attachment->id] = $attachment;

		if (!$attachment->exists() || !$attachment->getPath())
		{
			return (string) $layout->setLayout('deleted');
		}
		elseif (!$attachment->isAuthorised() && !$config->showimgforguest && $attachment->id != '0')
		{
			return null;
		}
		elseif (!$attachment->isAuthorised())
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
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	function DoFile($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
			$filename = basename(!empty ($params ["name"]) ? $params ["name"] : trim(strip_tags($content)));

			return '[ ' . basename(!empty ($params ["name"]) ? $params ["name"] : trim(strip_tags($content))) . ' ]';
		}

		// Make sure that filename does not contain path or URL.
		$filename = basename(!empty($params['name']) ? $params['name'] : $bbcode->UnHTMLEncode(trim(strip_tags($content))));
		$path     = "attachments/legacy/files/{$filename}";
		$filepath = KPATH_MEDIA . '/' . $path;
		$fileurl  = KURL_MEDIA . '/' . $path;

		// Legacy attachments support.
		if (isset($bbcode->parent->attachments))
		{
			/** @var array|KunenaAttachment[] $attachments */
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
			->set('canLink', $bbcode->autolink_disable == 0);

		if (JFactory::getUser()->id == 0 && KunenaFactory::getConfig()->showfileforguest == 0)
		{
			// Hide between content from non registered users
			return (string) $layout
				->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE'))
				->setLayout('unauthorised');
		}

		$layout->set('filename', $filename);
		$layout->set('size', isset($params['size']) ? $params['size'] : 0);

		if (!is_file($filepath))
		{
			// File does not exist (or URL was pointing somewhere else).
			$layout
				->set('title', JText::sprintf('COM_KUNENA_ATTACHMENT_DELETED', $bbcode->HTMLEncode($filename)))
				->setLayout('deleted');
		}
		else
		{
			$layout
				->set('title', JText::_('COM_KUNENA_FILEATTACH'))
				->set('url', $fileurl)
				->set('size', filesize($filepath));
		}

		return (string) $layout;
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	function DoImage($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
			/** @var array|KunenaAttachment[] $attachments */
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

		$config = KunenaFactory::getConfig();
		$layout = KunenaLayout::factory('BBCode/Image')
			->set('title', JText::_('COM_KUNENA_FILEATTACH'))
			->set('url', null)
			->set('filename', null)
			->set('size', isset($params['size']) ? $params['size'] : 0)
			->set('alt', isset($params['alt']) ? $params['alt'] : 0)
			->set('canLink', $bbcode->autolink_disable == 0);


		if (JFactory::getUser()->id == 0 && $config->showimgforguest == 0)
		{
			// Hide between content from non registered users.
			return (string) $layout->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG'))->setLayout('unauthorised');
		}

		// Obey image security settings.
		if ($config->bbcode_img_secure != 'image')
		{
			if ($bbcode->autolink_disable == 0 && !preg_match("/\\.(?:gif|jpeg|jpg|jpe|png)$/ui", $fileurl))
			{
				// If the image has not legal extension, return it as link or text.
				if ($config->bbcode_img_secure == 'link')
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
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoTerminal($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param              $action
	 * @param              $name
	 * @param              $default
	 * @param              $params
	 * @param              $content
	 *
	 * @return bool|string
	 */
	public function DoTweet($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
		{
			return true;
		}

		$config = KunenaFactory::getTemplate()->params;
		if (KunenaFactory::getTemplate()->isHmvc() && !$config->get('twitter'))
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
			return "<a href=\"https://twitter.com/kunena/status/" . $tweetid . "\" rel=\"nofollow\" target=\"_blank\">" . JText::_('COM_KUNENA_LIB_BBCODE_TWEET_STATUS_LINK') . "</a>";
		}

		return $this->renderTweet($tweetid);
	}

	/**
	 * Render the tweet by loading the right layout
	 *
	 * @param   int $tweetid The tweet id to render in layout
	 *
	 * @return string
	 */
	public function renderTweet($tweetid)
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
	}

	/**
	 * Get JSON tweet data by using OAuth 2.0 authentification
	 *
	 * @param   int $tweetid The tweet ID to query against twitter API
	 *
	 * @return string
	 */
	protected function getTweet($tweetid)
	{
		// FIXME: use AJAX instead...
		jimport('joomla.filesystem.folder');
		$config          = KunenaFactory::getConfig();
		$uri             = JURI::getInstance();
		$consumer_key    = trim($config->twitter_consumer_key);
		$consumer_secret = trim($config->twitter_consumer_secret);

		if (JFile::exists(JPATH_CACHE . '/kunena_tweet/kunenatweetdisplay-' . $tweetid . '.json'))
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

			$options = new JRegistry;

			$transport = new JHttpTransportStream($options);

			// Create a 'stream' transport.
			$http = new JHttp($options, $transport);

			$headers = array(
				'Authorization' => "Basic " . $b64_bearer_token_credentials,
			);

			$data     = "grant_type=client_credentials";
			$response = $http->post($url, $data, $headers);

			if ($response->code == 200)
			{
				$this->token = json_decode($response->body)->access_token;
			}
			else
			{
				$tweet        = new stdClass;
				$tweet->error = JText::_('COM_KUNENA_LIB_BBCODE_TWITTER_COULD_NOT_GET_TOKEN');

				return $tweet;
			}
		}
		elseif (empty($consumer_key) || empty($consumer_secret))
		{
			$tweet        = new stdClass;
			$tweet->error = JText::_('COM_KUNENA_LIB_BBCODE_TWITTER_CONSUMMER_KEY_SECRET_INVALID');

			return $tweet;
		}

		if (!empty($this->token))
		{
			$url = 'https://api.twitter.com/1.1/statuses/show.json?id=' . $tweetid;

			$options = new JRegistry;

			$transport = new JHttpTransportStream($options);

			// Create a 'stream' transport.
			$http = new JHttp($options, $transport);

			$headers = array(
				'Authorization' => "Bearer " . $this->token,
			);

			$data     = array();
			$response = $http->get($url, $headers);

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
								$tweet_data->text .= '<img src="' . $media->media_url_https . '" alt="tweet" />';
							}
							else
							{
								$tweet_data->text .= '<img src="' . $media->media_url . '" alt="tweet" />';
							}
						}
						elseif ($media->type == 'video')
						{
							if ($uri->isSSL())
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img src="' . $media->media_url_https . '" alt="tweet" /></a>';
							}
							else
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img src="' . $media->media_url . '" alt="tweet" /></a>';
							}
						}
						elseif ($media->type == 'animated_gif')
						{
							if ($uri->isSSL())
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img src="' . $media->media_url_https . '" alt="tweet" /></a>';
							}
							else
							{
								$tweet_data->text .= '<a href="' . $media->url . '"><img src="' . $media->media_url . '" alt="tweet" /></a>';
							}
						}
					}
				}

				if (!JFolder::exists(JPATH_CACHE . '/kunena_tweet'))
				{
					JFolder::create(JPATH_CACHE . '/kunena_tweet');
				}

				$tweet_data->error = false;

				file_put_contents(JPATH_CACHE . '/kunena_tweet/kunenatweetdisplay-' . $tweetid . '.json', json_encode($tweet_data));

				return $tweet_data;
			}
			else
			{
				$tweet        = new stdClass;
				$tweet->error = JText::_('COM_KUNENA_LIB_BBCODE_TWITTER_INVALID_TWEET_ID');

				return $tweet;
			}
		}
	}

	/**
	 * Query from eBay API the JSON stream of item id given to render
	 *
	 * @param   int $ItemID The eBay ID of object to query
	 *
	 * @return string
	 */
	public static function getEbayItem($ItemID)
	{
		$config = KunenaFactory::getConfig();

		if (is_numeric($ItemID) && $config->ebay_api_key && ini_get('allow_url_fopen'))
		{
			$options = new JRegistry;

			$transport = new JHttpTransportStream($options);

			// Create a 'stream' transport.
			$http = new JHttp($options, $transport);

			$response = $http->get('http://open.api.ebay.com/shopping?callname=GetSingleItem&appid=' . $config->ebay_api_key . '&siteid=' . $config->ebay_language . '&responseencoding=JSON&ItemID=' . $ItemID . '&version=889&trackingid=' . $config->ebay_affiliate_id . '&trackingpartnercode=9');

			if ($response->code == '200')
			{
				$resp = json_decode($response->body);

				return $resp;
			}
		}

		return '';
	}


	/**
	 * Load eBay object item from cache
	 *
	 * @param   int $ItemID The eBay ID of object to query
	 *
	 * @return string
	 */
	public static function getEbayItemFromCache($ItemID)
	{
		$cache = JFactory::getCache('Kunena_ebay_request');
		$cache->setCaching(true);
		$cache->setLifeTime(KunenaFactory::getConfig()->get('cache_time', 60));
		$ebay_item = $cache->call(array('KunenaBbcodeLibrary', 'getEbayItem'), $ItemID);

		return $ebay_item;
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoSoundcloud($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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
				return '<iframe allowtransparency="true" width="100%" height="350" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . $content . '&amp;auto_play=false&amp;visual=true"></iframe><br />';
			}
		}
	}

	/**
	 * @param $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function DoInstagram($bbcode, $action, $name, $default, $params, $content)
	{
		if ($action == BBCODE_CHECK)
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

			$content = trim($content);

			$url_parsed = parse_url($content);

			if ($url_parsed['scheme'] == 'https' || $url_parsed['scheme'] == 'http')
			{
				$content = $url_parsed['host'] . $url_parsed['path'];
			}
			else
			{
				$content = $url_parsed['path'];
			}

			if (preg_match('/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im', $content, $matches))
			{
				if (!preg_match('#^(/|https?:|ftp:)#ui', $content))
				{
					// Add scheme to raw domain URLs.
					$url = "https://{$content}";
				}

				return '<div class="embed-container"><iframe src="' . rtrim($url, '/') . '/embed/" frameborder="0" scrolling="no"></iframe></div>';
			}
		}
	}

	/**
	 * Render eBay layout from template
	 *
	 * @param $ItemID
	 *
	 * @return bool|string
	 */
	public static function renderEbayLayout($ItemID)
	{
		$config = KunenaFactory::getConfig();

		if (empty($config->ebay_api_key))
		{
			echo '<b>' . JText::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_NO_EBAY_APP_ID') . '</b>';

			return false;
		}
		elseif (!is_numeric($ItemID))
		{
			echo '<b>' . JText::_('COM_KUNENA_LIB_BBCODE_EBAY_ERROR_WRONG_ITEM_ID') . '</b>';

			return false;
		}

		$layout = KunenaLayout::factory('BBCode/eBay');

		if ($layout->getPath())
		{
			$ebay = self::getEbayItemFromCache($ItemID);

			if (is_object($ebay) && $ebay->Ack == 'Success')
			{
				return (string) $layout
					->set('content', $ItemID)
					//->set('params', $params)
					->set('naturalurl', $ebay->Item->ViewItemURLForNaturalSearch)
					->set('pictureurl', $ebay->Item->PictureURL[0])
					->set('status', $ebay->Item->ListingStatus)
					->set('ack', $ebay->Ack)
					->set('title', $ebay->Item->Title)
					->setLayout(is_numeric($ItemID) ? 'default' : 'search');
			}
		}
	}
}
