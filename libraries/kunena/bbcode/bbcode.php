<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage BBCode
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_FRAMEWORK . '/external/nbbc/nbbc.php';
jimport('joomla.utilities.string');

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Class KunenaBbcode
 *
 * @since   2.0
 */
class KunenaBbcode extends NBBC_BBCode {
	public $autolink_disable = 0;
	/**
	 * @var object
	 */
	public $parent = null;

    /**
     * Use KunenaBbcode::getInstance() instead.
     *
     * @param bool $relative
     * @internal
     */
    public function __construct($relative = true) {
		parent::__construct();
		$this->defaults = new KunenaBbcodeLibrary;
		$this->tag_rules = $this->defaults->default_tag_rules;

		$this->smileys = $this->defaults->default_smileys;
		if (empty($this->smileys)) $this->SetEnableSmileys(false);
		$this->SetSmileyDir ( JPATH_ROOT );
		$this->SetSmileyURL ( $relative ? JUri::root(true) : rtrim(JUri::root(), '/') );
		$this->SetDetectURLs ( true );
		$this->SetURLPattern (array($this, 'parseUrl'));
		$this->SetURLTarget('_blank');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');
		$dispatcher->trigger( 'onKunenaBbcodeConstruct', array( $this ) );
	}

	/**
	 * Get Singleton Instance
	 *
	 * @param
	 * @return	KunenaBbcode
	 * @since	1.7
	 */
    /**
     * Get global instance from BBCode parser.
     *
     * @param bool $relative
     * @return mixed
     */
    public static function getInstance($relative = true) {
		static $instance = false;
		if (!isset($instance[intval($relative)])) {
			$instance[intval($relative)] = new KunenaBbcode ($relative);
		}
		$instance[intval($relative)]->autolink_disable = 0;
		return $instance[intval($relative)];
	}

    /**
     * @param $params
     * @return string
     */
    public function parseUrl($params) {
		$url = $params['url'];
		$text = $params['text'];

		$config = KunenaFactory::getConfig ();
		if ($config->autolink) {
			if (preg_match('#^mailto:#ui', $url)) {
				// Cloak email addresses
				$email = substr($text, 7);
				return JHtml::_('email.cloak', $email, $this->IsValidEmail($email));
			}

			// Remove http(s):// from the text
			$text = preg_replace ( '#^http(s?)://#ui', '', $text );

			if ($config->trimlongurls) {
				// shorten URL text if they are too long
				$text = preg_replace ( '#^(.{' . $config->trimlongurlsfront . '})(.{4,})(.{' . $config->trimlongurlsback . '})$#u', '\1...\3', $text );
			}
		}

		if (!isset($params['query'])) $params['query'] = '';
		if (!isset($params['path'])) $params['path'] = '';

		if ($config->autoembedyoutube && empty($this->parent->forceMinimal) && isset($params['host'])) {
			// convert youtube links to embedded player
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if (strstr($params['host'], '.youtube.') && !empty($path[1]) && $path[1]=='watch' && !empty($query['v'])) {
				$video = $query['v'];
			} elseif ($params['host'] == 'youtu.be' && !empty($path[1])) {
				$video = $path[1];
			}
			if (isset($video)) {
				$uri = JURI::getInstance();
				if ( $uri->isSSL() ) {
					return '<object width="425" height="344"><param name="movie" value="https://www.youtube.com/v/'
							.urlencode($video).'?version=3&feature=player_embedded&fs=1&cc_load_policy=1"></param><param name="allowFullScreen" value="true"></param><embed src="https://www.youtube.com/v/'
									.urlencode($video).'?version=3&feature=player_embedded&fs=1&cc_load_policy=1" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>';
				} else {
					return '<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/'
							.urlencode($video).'?version=3&feature=player_embedded&fs=1&cc_load_policy=1"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/'
									.urlencode($video).'?version=3&feature=player_embedded&fs=1&cc_load_policy=1" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>';
				}
			}
		}
		if ($config->autoembedebay && empty($this->parent->forceMinimal) && isset($params['host']) && strstr($params['host'], '.ebay.')) {
			parse_str($params['query'], $query);
			$path = explode('/', $params['path']);

			if ($path[1] == 'itm') {
				if (isset($path[3]) && is_numeric($path[3])) $itemid = intval($path[3]);
				elseif (isset($path[2]) && is_numeric($path[2])) $itemid = intval($path[2]);
				if (isset($itemid)) {
					// convert ebay item to embedded widget
					return '<object width="355" height="300"><param name="movie" value="http://togo.ebay.com/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang='
						. $config->ebaylanguagecode . '&mode=normal&itemid='.$itemid.'&campid='.$config->ebay_affiliate_id.'" /><embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang='
						. $config->ebaylanguagecode . '&mode=normal&itemid='.$itemid.'&campid='.$config->ebay_affiliate_id.'"></embed></object>';
				}
				/*
				$text = preg_replace ( '#.*\.ebay\.([^/]+)/.*QQitemZ([0-9]+).+#u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350"></embed></object>', $text );
				$text = preg_replace ( '#.*\.ebay\.([^/]+)/.*ViewItem.+Item=([0-9]+).*#u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350"></embed></object>', $text );
				*/
			}

			parse_str($params['query'], $query);

			if (isset($path[1]) && $path[1] == 'sch' && !empty($query['_nkw'])) {
				// convert ebay search to embedded widget
				return '<object width="355" height="300"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query='
					. urlencode($query['_nkw']) .'&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang='
					. $config->ebaylanguagecode . '&mode=search&query=' . urlencode($query['_nkw']) . '&campid=5336042350"></embed></object>';
				/*
				$text = preg_replace ( '#.*\.ebay\.([^/]+)/.*satitle=([^&]+).*#u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=$2&campid=5336042350"></embed></object>', $text );
				*/
			}
			if (strstr($params['host'], 'myworld.') && !empty($path[1])) {
				// convert seller listing to embedded widget
				return '<object width="355" height="355"><param name="movie" value="http://togo.ebay.com/togo/seller.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang='
					. $config->ebaylanguagecode . '&seller=' . urlencode($path[1]) . '&campid=5336042350" /><embed src="http://togo.ebay.com/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.com/togo/&lang='
					. $config->ebaylanguagecode . '&seller=' . urlencode($path[1]) . '&campid=5336042350"></embed></object>';
				/*
				$text = preg_replace ( '#.*\.ebay\.([^/]+)/.*QQsassZ([^&]+).*#u', '<object width="355" height="355"><param name="movie" value="http://togo.ebay.$1/togo/seller.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&seller=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $config->ebaylanguagecode . '&seller=$2&campid=5336042350"></embed></object>', $text );
				*/
			}
		}

		if ($config->autolink) {
			return "<a class=\"bbcode_url\" href=\"{$url}\" target=\"_blank\" rel=\"nofollow\">{$text}</a>";
		}

		// Auto-linking has been disabled.
		return $text;
	}

    /**
     * @param $string
     * @return array
     */
    function Internal_AutoDetectURLs($string) {
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
		)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE );

		$output = array();
		foreach ($search as $index => $token) {
			if ($index & 1) {
				if (preg_match("/^(https?|ftp|mailto):/ui", $token)) {
					// Protocol has been provided, so just use it as-is.
					$url = $token;
				} else {
					// Add scheme to emails and raw domain URLs.
					$url = (strpos($token, '@') ? 'mailto:' : 'http://') . $token;
				}
				// Never start URL from the middle of text (except for punctuation).
				$invalid = preg_match('#[^\s`!()\[\]{};\'"\.,<>?«»“”‘’]$#u', $search[$index-1]);
				$invalid |= !$this->IsValidURL($url, true);

				// We have a full, complete, and properly-formatted URL, with protocol.
				// Now we need to apply the $this->url_pattern template to turn it into HTML.
				$params = JString::parse_url($url);
				if (!$invalid && substr($url, 0, 7) == 'mailto:') {
					$email = JString::substr($url, 7);
					$output[$index] = JHtml::_('email.cloak', $email, $this->IsValidEmail($email));

				} elseif ($invalid || empty($params['host']) || !empty($params['pass'])) {
					$output[$index-1] .= $token;
					$output[$index] = '';

				} else {
					$params['url'] = $url;
					$params['link'] = $url;
					$params['text'] = $token;
					$output[$index] = $this->FillTemplate($this->url_pattern, $params);
				}
			} else {
				$output[$index] = $token;
			}
		}
		return $output;
	}

	/**
	 * @see BBCode::IsValidURL()
	 * Regular expression taken from https://gist.github.com/729294
	 */
	public function IsValidURL($string, $email_too = true, $local_too = false) {
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
class KunenaBbcodeLibrary extends BBCodeLibrary {
	var $default_smileys = array();
	var $default_tag_rules = array(
			'b' => array(
				'simple_start' => "<b>",
				'simple_end' => "</b>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<b>",
				'plain_end' => "</b>",
			),

			'i' => array(
				'simple_start' => "<i>",
				'simple_end' => "</i>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'u' => array(
				'simple_start' => "<u>",
				'simple_end' => "</u>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<u>",
				'plain_end' => "</u>",
			),

			's' => array(
				'simple_start' => "<strike>",
				'simple_end' => "</strike>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'strike' => array(
				'simple_start' => "<strike>",
				'simple_end' => "</strike>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'tt' => array(
				'simple_start' => "<tt>",
				'simple_end' => "</tt>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'pre' => array(
				'simple_start' => "<pre>",
				'simple_end' => "</pre>",
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'font' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'allow' => array('_default' => '/^[a-zA-Z0-9._ -]+$/'),
				'method' => 'DoFont',
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
			),

			'color' => array(
				'mode' => BBCODE_MODE_ENHANCED,
				'allow' => array('_default' => '/^#?[a-zA-Z0-9._ -]+$/'),
				'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
			),

			'size' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoSize',
				'allow' => array('_default' => '/^[0-9.]+(px|em|pt|%)?$/D'),
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
			),

			'sup' => array(
				'simple_start' => "<sup>",
				'simple_end' => "</sup>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
			),

			'sub' => array(
				'simple_start' => "<sub>",
				'simple_end' => "</sub>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
			),

			'spoiler' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoSpoiler',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "\nSpoiler:\n<i>",
				'plain_end' => "</i>",
			),

			'hide' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoHide',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_REQUIRED,
				'plain_content' => array(),
			),

			'confidential' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoConfidential',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_REQUIRED,
				'plain_content' => array(),
			),

			'map' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoMap',
				'class' => 'block',
				'allow' => array( 'type' => '/^[\w\d.-_]*$/', 'zoom' => '/^\d*$/', 'control' => '/^\d*$/' ),
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
			),

			'ebay' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoEbay',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "[ebay]",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'article' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoArticle',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "\n[article]\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'tableau' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoTableau',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "\n[tableau]\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'video' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoVideo',
				'allow' => array( 'type' => '/^[\w\d.-_]*$/', 'param' => '/^[\w]*$/', 'size' => '/^\d*$/', 'width' => '/^\d*$/', 'height' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "[video]",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'img' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoImage',
				'allow' => array( 'size' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns', 'link'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "[image]",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'file' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoFile',
				'allow' => array( 'size' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "\n[file]\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'attachment' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoAttachment',
				'allow' => array( '_default' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "\n[attachment]\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'highlight' => array(
				'simple_start' => "<span style='font-weight: 700;'>",
				'simple_end' => "</span>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>",
			),

			'acronym' => array(
				'mode' => BBCODE_MODE_ENHANCED,
				'template' => '<span class="bbcode_acronym" title="{$_default/e}">{$_content/v}</span>',
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
			),

			'url' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoUrl',
				'class' => 'link',
				'allow_in' => array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_REQUIRED,
				'plain_start' => "<a href=\"{\$link}\" rel=\"nofollow\" target=\"_blank\">",
				'plain_end' => "</a>",
				'plain_content' => array('_content', '_default'),
				'plain_link' => array('_default', '_content'),
			),

			'email' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoEmail',
				'class' => 'link',
				'allow_in' => array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_VERBATIM,
				'plain_start' => "<a href=\"mailto:{\$link}\">",
				'plain_end' => "</a>",
				'plain_content' => array('_content', '_default'),
				'plain_link' => array('_default', '_content'),
			),

			'wiki' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => "DoWiki",
				'class' => 'link',
				'allow_in' => array('listitem', 'block', 'columns', 'inline'),
				'end_tag' => BBCODE_PROHIBIT,
				'content' => BBCODE_PROHIBIT,
				'plain_start' => "<b>[",
				'plain_end' => "]</b>",
				'plain_content' => array('title', '_default'),
				'plain_link' => array('_default', '_content'),
			),

			'rule' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => "DoRule",
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'end_tag' => BBCODE_PROHIBIT,
				'content' => BBCODE_PROHIBIT,
				'before_tag' => "sns",
				'after_tag' => "sns",
				'plain_start' => "\n-----\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'br' => array(
				'mode' => BBCODE_MODE_SIMPLE,
				'simple_start' => "<br />\n",
				'simple_end' => "",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'end_tag' => BBCODE_PROHIBIT,
				'content' => BBCODE_PROHIBIT,
				'before_tag' => "s",
				'after_tag' => "s",
				'plain_start' => "\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'hr' => array(
				'mode' => BBCODE_MODE_SIMPLE,
				'simple_start' => "<hr />\n",
				'simple_end' => "",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'end_tag' => BBCODE_PROHIBIT,
				'content' => BBCODE_PROHIBIT,
				'before_tag' => "s",
				'after_tag' => "s",
				'plain_start' => "\n-----\n",
				'plain_end' => "",
				'plain_content' => array(),
			),

			'left' => array(
				'simple_start' => "\n<div class=\"bbcode_left\" style=\"text-align:left\">\n",
				'simple_end' => "\n</div>\n",
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'right' => array(
				'simple_start' => "\n<div class=\"bbcode_right\" style=\"text-align:right\">\n",
				'simple_end' => "\n</div>\n",
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'center' => array(
				'simple_start' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n",
				'simple_end' => "\n</div>\n",
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'indent' => array(
				'simple_start' => "\n<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n",
				'simple_end' => "\n</div>\n",
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'table' => array(
				'simple_start' => "\n<table>",
				'simple_end' => "</table>\n",
				'class' => 'table',
				'allow_in' => array('listitem', 'block', 'columns'),
				'end_tag' => BBCODE_REQUIRED,
				'content' => BBCODE_REQUIRED,
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'tr' => array(
				'simple_start' => "\n<tr>",
				'simple_end' => "</tr>\n",
				'class' => 'tr',
				'allow_in' => array('table'),
				'end_tag' => BBCODE_REQUIRED,
				'content' => BBCODE_REQUIRED,
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'th' => array(
				'simple_start' => "<th>",
				'simple_end' => "</th>",
				'class' => 'columns',
				'allow_in' => array('tr'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'td' => array(
				'simple_start' => "<td>",
				'simple_end' => "</td>",
				'class' => 'columns',
				'allow_in' => array('tr'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'columns' => array(
				'simple_start' => "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n",
				'simple_end' => "\n</td></tr></tbody></table>\n",
				'class' => 'columns',
				'allow_in' => array('listitem', 'block', 'columns'),
				'end_tag' => BBCODE_REQUIRED,
				'content' => BBCODE_REQUIRED,
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'nextcol' => array(
				'simple_start' => "\n</td><td class=\"bbcode_column\">\n",
				'class' => 'nextcol',
				'allow_in' => array('columns'),
				'end_tag' => BBCODE_PROHIBIT,
				'content' => BBCODE_PROHIBIT,
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "",
			),

			'code' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoCode',
				'allow' => array( 'type' => '/^[\w]*$/', ),
				'class' => 'code',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_VERBATIM,
				'before_tag' => "sns",
				'after_tag' => "sn",
				'before_endtag' => "sn",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'quote' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoQuote',
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\nQuote:\n",
				'plain_end' => "\n",
			),

			'list' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoList',
				'class' => 'list',
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'ul' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoList',
				'default' => array( '_default' => 'circle' ),
				'class' => 'list',
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'ol' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoList',
				'allow' => array( '_default' => '/^[\d\w]*$/', ),
				'default' => array( '_default' => '1' ),
				'class' => 'list',
				'allow_in' => array('listitem', 'block', 'columns'),
				'before_tag' => "sns",
				'after_tag' => "sns",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n",
				'plain_end' => "\n",
			),

			'*' => array(
				'simple_start' => "<li>",
				'simple_end' => "</li>\n",
				'class' => 'listitem',
				'allow_in' => array('list'),
				'end_tag' => BBCODE_OPTIONAL,
				'before_tag' => "s",
				'after_tag' => "s",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n * ",
				'plain_end' => "\n",
			),

			'li' => array(
				'simple_start' => "<li>",
				'simple_end' => "</li>\n",
				'class' => 'listitem',
				'allow_in' => array('listitem', 'block', 'columns', 'list'),
				'before_tag' => "s",
				'after_tag' => "s",
				'before_endtag' => "sns",
				'after_endtag' => "sns",
				'plain_start' => "\n * ",
				'plain_end' => "\n",
			),

			'terminal' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoTerminal',
				'allow_in' => array('listitem', 'block', 'columns'),
				'class' => 'block',
				'allow' => array( 'colortext' => '/^#[0-9a-fA-F]+|[a-zA-Z]+$/' ),
				'content' => BBCODE_PROHIBIT,
				'plain_start' => "\nTerminal:\n",
				'plain_end' => "\n",
			),
	);

	function __construct() {
		if (!KunenaFactory::getConfig()->disemoticons) {
			$db = JFactory::getDBO ();
			$query = "SELECT code, location FROM #__kunena_smileys";
			$db->setQuery ( $query );
			$smileys = $db->loadObjectList ();

			$template = KunenaFactory::getTemplate();
			foreach ( $smileys as $smiley ) {
				$this->default_smileys [$smiley->code] = $template->getSmileyPath($smiley->location);
			}
		}
		// Translate plain text "Quote:"
		$this->default_tag_rules['quote']['plain_start'] = "\n".JText::_('COM_KUNENA_LIB_BBCODE_QUOTE_TITLE')."\n";
	}

	/**
	 * @param KunenaBbcode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|mixed|string
	 */
	function DoEmail($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			return true;
		}
		$email = is_string ( $default ) ? $default : $bbcode->UnHTMLEncode($content);
		$text = is_string ( $default ) ? $bbcode->UnHTMLEncode($content) : $default;
		return JHtml::_('email.cloak', htmlspecialchars ( $email ), $bbcode->IsValidEmail ( $email ), htmlspecialchars ( $text ), $bbcode->IsValidEmail ( $text ));
	}

	/**
	 * Format a [url] tag by producing an <a>...</a> element.
	 * The URL only allows http, https, mailto, and ftp protocols for safety.
	 *
	 * @param KunenaBbcode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoURL($bbcode, $action, $name, $default, $params, $content) {
		// We can't check this with BBCODE_CHECK because we may have no URL before the content
		// has been processed.
		if ($action == BBCODE_CHECK) {
			$bbcode->autolink_disable++;
			return true;
		}

		$bbcode->autolink_disable--;
		$url = is_string ( $default ) ? $default : $bbcode->UnHTMLEncode ( strip_tags ( $content ) );
		$url = preg_replace('# #u', '%20', $url);
		if (!preg_match('#^(/|https?:|ftp:)#ui', $url)) {
			// Add scheme to raw domain URLs.
			$url = "http://{$url}";
		}

		if ($bbcode->IsValidURL ( $url, false, true )) {
			if ($bbcode->debug)
				echo "ISVALIDURL<br />";
			if ($bbcode->url_targetable !== false && isset ( $params ['target'] ))
				$target = " target=\"" . htmlspecialchars ( $params ['target'] ) . "\"";
			elseif ($bbcode->url_target !== false)
				$target = " target=\"" . htmlspecialchars ( $bbcode->url_target ) . "\"";
			else
				$target = '';
			return '<a href="' . htmlspecialchars ( $url ) . '" class="bbcode_url" rel="nofollow"' . $target . '>' . $content . '</a>';
		}
		return htmlspecialchars ( $params ['_tag'] ) . $content . htmlspecialchars ( $params ['_endtag'] );
	}

	// Format a [size] tag by producing a <span> with a style with a different font-size.
	function DoSize($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$size_css = array (1 => 'kmsgtext-xs', 'kmsgtext-s', 'kmsgtext-m', 'kmsgtext-l', 'kmsgtext-xl', 'kmsgtext-xxl' );
		if (isset ( $size_css [$default] )) {
			$size = "class=\"{$size_css [$default]}\"";
		} elseif (!empty($default)) {
			$size = "style=\"font-size:{$default}\"";
		} else {
			$size = "class=\"{$size_css [3]}\"";
		}
		return "<span {$size}>{$content}</span>";
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
	function DoList($bbcode, $action, $name, $default, $params, $content) {
		// Allowed list styles, striaght from the CSS 2.1 spec.  The only prohibited
		// list style is that with image-based markers, which often slows down web sites.
		$list_styles = Array ('1' => 'decimal', '01' => 'decimal-leading-zero', 'i' => 'lower-roman', 'I' => 'upper-roman', 'a' => 'lower-alpha', 'A' => 'upper-alpha' );
		$ci_list_styles = Array ('circle' => 'circle', 'disc' => 'disc', 'square' => 'square', 'greek' => 'lower-greek', 'armenian' => 'armenian', 'georgian' => 'georgian' );
		$ul_types = Array ('circle' => 'circle', 'disc' => 'disc', 'square' => 'square' );
		$default = trim ( $default );
		if (! $default)
			$default = $bbcode->tag_rules [$name] ['default'] ['_default'];

		if ($action == BBCODE_CHECK) {
			if (! is_string ( $default ) || strlen ( $default ) == "")
				return true;
			else if (isset ( $list_styles [$default] ))
				return true;
			else if (isset ( $ci_list_styles [strtolower ( $default )] ))
				return true;
			else
				return false;
		}

		// Choose a list element (<ul> or <ol>) and a style.
		$type = '';
		$elem = 'ul';
		if (! is_string ( $default ) || strlen ( $default ) == "") {
			$elem = 'ul';
		} else if ($default == '1') {
			$elem = 'ol';
		} else if (isset ( $list_styles [$default] )) {
			$elem = 'ol';
			$type = $list_styles [$default];
		} else {
			$default = strtolower ( $default );
			if (isset ( $ul_types [$default] )) {
				$elem = 'ul';
				$type = $ul_types [$default];
			} else if (isset ( $ci_list_styles [$default] )) {
				$elem = 'ol';
				$type = $ci_list_styles [$default];
			}
		}

		// Generate the HTML for it.
		if (strlen ( $type ))
			return "\n<$elem class=\"bbcode_list\" style=\"list-style-type:$type\">\n$content</$elem>\n";
		else
			return "\n<$elem class=\"bbcode_list\">\n$content</$elem>\n";
	}

	function DoSpoiler($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited) {
			return "[{$name}]{$content}";
		}
		// Display only spoiler text in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			return '[' . ($default ? $default : JText::_ ('COM_KUNENA_BBCODE_SPOILER')) . ']';
		}
		$document = JFactory::getDocument();
		if (!($document instanceof JDocumentHTML)) {
			// Static version
			return '<div class="kspoiler"><div class="kspoiler-header"><span class="kspoiler-title">' . ($default ? ($default) : (JText::_ ( 'COM_KUNENA_BBCODE_SPOILER' )))
				. '</span> <span class="kspoiler-hide">' . JText::_ ( 'COM_KUNENA_LIB_BBCODE_SPOILER_HIDE' )
				. '</span></div><div class="kspoiler-wrapper"><div class="kspoiler-content">' . $content . '</div></div></div>';
		}

		return '<div class="kspoiler"><div class="kspoiler-header"><span class="kspoiler-title">' . ($default ? ($default) : (JText::_ ( 'COM_KUNENA_BBCODE_SPOILER' )))
			. '</span> <span class="kspoiler-expand">' . JText::_ ( 'COM_KUNENA_LIB_BBCODE_SPOILER_EXPAND' ) . '</span><span class="kspoiler-hide" style="display:none">'
			. JText::_ ( 'COM_KUNENA_LIB_BBCODE_SPOILER_HIDE' ) . '</span></div><div class="kspoiler-wrapper"><div class="kspoiler-content" style="display:none">' . $content . '</div></div></div>';
	}

	function DoHide($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited) {
			return "[{$name}]{$content}";
		}
		// Display nothing in activity streams etc..
		if (!empty($bbcode->parent->forceSecure)) {
			return;
		}
		if (JFactory::getUser ()->id == 0) {
			// Hide between content from non registered users
			return JText::_ ( 'COM_KUNENA_BBCODE_HIDDENTEXT' );
		} else {
			// Display but highlight the fact that it is hidden from guests
			return '<b>' . JText::_ ( 'COM_KUNENA_BBCODE_HIDE_IN_MESSAGE' ) . '</b>' . '<div class="kmsgtext-hide">' . $content . '</div>';
		}
	}

	function DoConfidential($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (!empty($bbcode->lost_start_tags[$name]) && !$bbcode->was_limited) {
			return "[{$name}]{$content}";
		}
		// Display nothing in activity streams etc..
		if (!empty($bbcode->parent->forceSecure)) {
			return;
		}
		$me = KunenaUserHelper::getMyself();
		if (($me->userid && $bbcode->parent->message->userid == $me->userid) || $me->isModerator(isset($bbcode->parent->message) ? $bbcode->parent->message->getCategory() : null)) {
			// Display but highlight the fact that it is hidden from everyone except admins and mods
			return '<b>' . JText::_ ( 'COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT' ) . '</b><div class="kmsgtext-confidential">' . $content . '</div>';
		}
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoMap($bbcode, $action, $name, $default, $params, $content) {
		static $id = false;
		static $sensor = true;

		if ($action == BBCODE_CHECK)
			return true;

		$document = JFactory::getDocument();

		// Display only link in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal) || !($document instanceof JDocumentHTML)) {
			$url = 'https://maps.google.com/?q='.urlencode($bbcode->UnHTMLEncode($content));
			return '<a href="'.$url.'" rel="nofollow" target="_blank">'.$content.'</a>';
		}

		if ($id === false) {
			$document->addScript('https://maps.google.com/maps/api/js?sensor='.($sensor == true ? 'true' : 'false'));
			$id = 0;
		}

		$id ++;
		$mapid = 'kgooglemap'.$id;

		$map_type = isset($params ['type']) ? strtoupper($params ["type"]): 'ROADMAP';
		$map_typeId = array('HYBRID','ROADMAP','SATELLITE','TERRAIN');
		if ( !in_array($map_type, $map_typeId) ) $map_type = 'ROADMAP';
		$map_zoom = isset($params ['zoom']) ? (int) $params ['zoom']: 10;
		$map_control = $params ['control'] ? (int) $params ['control'] : 0;

		$document->addScriptDeclaration("
		// <![CDATA[
			var geocoder;
			var $mapid;

			window.addEvent('domready', function() {
				geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(37.333586,-121.894684);
			var myOptions = {
				zoom: $map_zoom,
				disableDefaultUI: $map_control,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.$map_type
			};
			$mapid = new google.maps.Map(document.id('".$mapid."'), myOptions);

			var address = ".json_encode($content).";
			if (geocoder) {
				geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$mapid.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						position: results[0].geometry.location,
				 		map: $mapid
					});
				} else {
					var contentString = '<p><strong>".JText::_('COM_KUNENA_GOOGLE_MAP_NO_GEOCODE', true)." <i>".json_encode(addslashes($content))."</i></strong></p>';
					var infowindow$mapid = new google.maps.InfoWindow({ content: contentString });
						infowindow$mapid.open($mapid);
				}
				});
			}
			});

		// ]]>"
		);

		return '<div id="'.$mapid.'" class="kgooglemap">'.JText::_('COM_KUNENA_GOOGLE_MAP_NOT_VISIBLE', true).'</div>';
	}

	function DoEbay($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$config = KunenaFactory::getConfig();

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			return '<a target="_blank" href="http://www.ebay.com/itm/'.$content.'?lang=' . $config->ebaylanguagecode . '&campid='.$config->ebay_affiliate_id.'">www.ebay.com/itm/'.$content.'</a>';
		}

		$ebay_maxwidth = (int) (($config->rtewidth * 9) / 10); // Max 90% of text width
		$ebay_maxheight = (int) ($config->rteheight); // max. display size

		if (is_numeric ( $content )) {
			// Numeric: we have to assume this is an item id
			return '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=' . $content . '&campid='.$config->ebay_affiliate_id.'" /><embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=' . $content . '&campid='.$config->ebay_affiliate_id.'"></embed></object>';
		} else {
			// Non numeric: we have to assume this is a search
			return '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=' . $content . '&campid='.$config->ebay_affiliate_id.'" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=' . $content . '&campid='.$config->ebay_affiliate_id.'"></embed></object>';
		}
	}

	function DoArticle($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$lang = JFactory::getLanguage();
		$lang->load('com_content');

		$articleid = intval($content);

		$config = KunenaFactory::getConfig();
		$user = JFactory::getUser ();
		$db = JFactory::getDBO ();
		/** @var JSite $site */
		$site = JFactory::getApplication('site');

		$query = 'SELECT a.*, u.name AS author, cc.title AS category,
			0 AS sec_pub, 0 AS sectionid, cc.published AS cat_pub, cc.access AS cat_access
			FROM #__content AS a
			LEFT JOIN #__categories AS cc ON cc.id = a.catid
			LEFT JOIN #__users AS u ON u.id = a.created_by
			WHERE a.id='.$db->quote($articleid);
		$db->setQuery($query);
		$article = $db->loadObject();
		if ($article) {
			// Get credentials to check if the user has right to see the article
			$params = $site->getParams('com_content');
			$registry = new JRegistry();
			$registry->loadString($article->attribs);
			$article->params = clone $params;
			$article->params->merge($registry);
			$params = $article->params;

			$viewlevels = $user->getAuthorisedViewLevels();
			if ( !in_array($article->access, $viewlevels) ) {
				$denied = true;
			}
		}

		$html = $link = '';
		if (!$article || (!$article->cat_pub && $article->catid) || (!$article->sec_pub && $article->sectionid)) {
			$html = JText::_ ( 'COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_UNPUBLISHED' );
		} elseif (!empty($denied) && !$params->get('show_noauth')) {
			$html = JText::_( 'COM_KUNENA_LIB_BBCODE_ARTICLE_ERROR_NO_PERMISSIONS' );
		} else {
			require_once (JPATH_ROOT.'/components/com_content/helpers/route.php');
			$article->slug = !empty($article->alias) ? ($article->id.':'.$article->alias) : $article->id;
			$article->catslug = !empty($article->category_alias) ? ($article->catid.':'.$article->category_alias) : $article->catid;
			$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug));

			if (!$default) $default = $config->article_display;
			// Do not display full text if there's no permissions to display the full article.
			if (!empty($denied) && $default == 'full') $default = 'intro';
			switch ($default) {
				case 'full':
					if ( !empty($article->fulltext) ) {
						$article->text = $article->introtext. ' '. $article->fulltext;
						$link = '<a href="'.$url.'" class="readon">'.JText::sprintf('COM_KUNENA_LIB_BBCODE_ARTICLE_READ').'</a>';
						break;
					}
					// continue to intro if fulltext is empty
				case 'intro':
					if ( !empty($article->introtext) ) {
						$article->text = $article->introtext;
						$link = '<a href="'.$url.'" class="readon">'.JText::sprintf('COM_KUNENA_LIB_BBCODE_ARTICLE_MORE').'</a>';
						break;
					}
					// continue to link if introtext is empty
				case 'link':
				default:
					$link = '<a href="'.$url.'" class="readon">'.$article->title.'</a>';
					break;
			}
			if (!empty($article->text)) {
				// Identify the source of the event to be Kunena itself
				// this is important to avoid recursive event behaviour with our own plugins
				$params->set('ksource', 'kunena');
				JPluginHelper::importPlugin('content');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onContentPrepare', array ('text', &$article, &$params, 0));
				$article->text = JHTML::_('string.truncate', $article->text, $bbcode->output_limit-$bbcode->text_length);
				$bbcode->text_length += strlen($article->text);
				$html = $article->text;
			}
			if (!empty($denied)) {
				$link = '<span class="readon">'. JText::_('COM_CONTENT_REGISTER_TO_READ_MORE') .'</span>';
			}
		}
		return ($html ? '<div class="kmsgtext-article">' . $html . '</div>' : '') . $link;
	}

	function DoQuote($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$post = isset($params["post"]) ? $params["post"] : false;
		$user = isset($default) ? htmlspecialchars($default, ENT_COMPAT, 'UTF-8') : false;
		$html = '';
		if ($user) $html .= "<b>" . $user . " " . JText::_ ( 'COM_KUNENA_POST_WROTE' ) . ":</b>\n";
		$html .= '<div class="kmsgtext-quote">' . $content . '</div>';
		return $html;
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoCode($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			return true;
		}

		$type = isset ( $params ["type"] ) ? $params ["type"] : "php";
		if ($type == 'js') {
			$type = 'javascript';
		} elseif ($type == 'html') {
			$type = 'html4strict';
		}
		$highlight = KunenaFactory::getConfig()->highlightcode && empty($bbcode->parent->forceMinimal);
		if ($highlight && !class_exists('GeSHi')) {
			$paths = array(
				JPATH_ROOT.'/plugins/content/geshiall/geshi/geshi.php',
				JPATH_ROOT.'/plugins/content/geshi/geshi/geshi.php'
			);
			foreach ($paths as $path) {
				if (!class_exists('GeSHi') && file_exists($path)) {
					require_once $path;
				}
			}

		}
		if ($highlight && class_exists('GeSHi')) {
			$geshi = new GeSHi ( $bbcode->UnHTMLEncode($content), $type );
			$geshi->enable_keyword_links ( false );
			$code = $geshi->parse_code ();
		} else {
			$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$code = '<pre xml:'.$type.'>'.$content.'</pre>';
		}
		return '<div class="highlight">'.$code.'</div>';
	}

	function doTableau($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			$bbcode->autolink_disable++;
			return true;
		}

		$bbcode->autolink_disable--;
		if (!$content)
			return;

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			return '[tableau]';
		}

		$config = KunenaFactory::getConfig();

		$viz_maxwidth = (int) (($config->rtewidth * 9) / 10); // Max 90% of text width
		$viz_maxheight = (isset ( $params ["height"] ) && is_numeric($params ["height"])) ? (int) $params ["height"] : (int) $config->rteheight;

		if(preg_match ('/(https?:\/\/.*?)\/(?:.*\/)*(.*\/.*)\?.*:toolbar=(yes|no)/', $content, $matches)){
			$tableauserver = $matches[1];
			$vizualization = $matches[2];
			$toolbar = $matches[3];

			return '<script type="text/javascript" src="'.$tableauserver.
					'/javascripts/api/viz_v1.js"></script><object class="tableauViz" width="'.$viz_maxwidth.
					'" height="'.$viz_maxheight.'" style="display:none;"><param name="name" value="'.$vizualization.
					'" /><param name="toolbar" value="'.$toolbar.'" /></object>';
		}
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoVideo($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			$bbcode->autolink_disable++;
			return true;
		}

		$bbcode->autolink_disable--;
		if (! $content)
			return;

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			return '[video]';
		}

		$vid_minwidth = 200;
		$vid_minheight = 44; // min. display size
		$vid_maxwidth = ( int ) ((KunenaFactory::getConfig()->rtewidth * 9) / 10); // Max 90% of text width
		$vid_maxheight = 720; // max. display size
		$vid_sizemax = 100; // max. display zoom in percent

		$vid ["type"] = (isset ( $params ["type"] )) ? JString::strtolower ( $params ["type"] ) : '';
		$vid ["param"] = (isset ( $params ["param"] )) ? $params ["param"] : '';

		if (! $vid ["type"]) {
			$vid_players = array ('divx' => 'divx', 'flash' => 'swf', 'mediaplayer' => 'avi,mp3,wma,wmv', 'quicktime' => 'mov,qt,qti,qtif,qtvr', 'realplayer', 'rm' );
			foreach ( $vid_players as $vid_player => $vid_exts )
				foreach ( explode ( ',', $vid_exts ) as $vid_ext )
					if (preg_match ( '/^(.*\.' . $vid_ext . ')$/i', $content ) > 0) {
						$vid ["type"] = $vid_player;
						break 2;
					}
			unset ( $vid_players );
		}
		if (! $vid ["type"]) {
			$vid_auto = preg_match ( '#^http://.*?([^.]*)\.[^.]*(/|$)#u', $content, $vid_regs );
			if ($vid_auto) {
				$vid ["type"] = JString::strtolower ( $vid_regs [1] );
				switch ($vid ["type"]) {
					case 'wideo' :
						$vid ["type"] = 'wideo.fr';
						break;
				}
			}
		}

		$vid_providers = array (

		'bofunk' => array ('flash', 446, 370, 0, 0, 'http://www.bofunk.com/e/%vcode%', '', '' ),

		'break' => array ('flash', 464, 392, 0, 0, 'http://embed.break.com/%vcode%', '', '' ),

		'clipfish' => array ('flash', 464, 380, 0, 0, 'http://www.clipfish.de/videoplayer.swf?as=0&videoid=%vcode%&r=1&c=0067B3', 'videoid=([\w\-]*)', '' ),

		'dailymotion' => array('flash', 464, 380, 0, 0, 'http://www.dailymotion.com/swf/video/%vcode%?autoPlay=0', '\/([\w]*)_', array (array (6, 'wmode', 'transparent' ) )),

		'metacafe' => array ('flash', 400, 345, 0, 0, 'http://www.metacafe.com/fplayer/%vcode%/.swf', '\/watch\/(\d*\/[\w\-]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'myspace' => array ('flash', 430, 346, 0, 0, 'http://lads.myspace.com/videos/vplayer.swf', 'VideoID=(\d*)', array (array (6, 'flashvars', 'm=%vcode%&v=2&type=video' ) ) ),

		'rutube' => array ('flash', 400, 353, 0, 0, 'http://video.rutube.ru/%vcode%', '\.html\?v=([\w]*)' ),

		'sapo' => array ('flash', 400, 322, 0, 0, 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/%vcode%/mov/1', 'videos\.sapo\.pt\/([\w]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'streetfire' => array ('flash', 428, 352, 0, 0, 'http://videos.streetfire.net/vidiac.swf', '\/([\w-]*).htm', array (array (6, 'flashvars', 'video=%vcode%' ) ) ),

		'veoh' => array ('flash', 540, 438, 0, 0, 'http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%vcode%', '\/videos\/([\w-]*)', '' ),

		'videojug' => array ('flash', 400, 345, 0, 0, 'http://www.videojug.com/film/player?id=%vcode%', '', '' ),

		'vimeo' => array ('flash', 400, 321, 0, 0, 'http://www.vimeo.com/moogaloop.swf?clip_id=%vcode%&server=www.vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=0&color=', '\.com\/(\d*)', '' ),

		'wideo.fr' => array ('flash', 400, 368, 0, 0, 'http://www.wideo.fr/p/fr/%vcode%.html', '\/([\w-]*).html', array (array (6, 'wmode', 'transparent' ) ) ),

		'youtube' => array ('flash', 425, 355, 0, 0, 'http://www.youtube.com/v/%vcode%?fs=1&hd=0&rel=1&cc_load_policy=1', '\/watch\?v=([\w\-]*)' , array (array (6, 'wmode', 'transparent' ) ) ),

		'youku' => array ('flash', 425, 355, 0, 0, 'http://player.youku.com/player.php/Type/Folder/Fid/18787874/Ob/1/sid/%vcode%/v.swf', '\/watch\?v=([\w\-]*)' , array (array (6, 'wmode', 'transparent' ) ) ),

		// Cannot allow public flash objects as it opens up a whole set of vulnerabilities through hacked flash files
		//				'_default' => array ($vid ["type"], 480, 360, 0, 25, $content, '', '' )
		//
		);

		if (isset ( $vid_providers [$vid ["type"]] )) {
			list ( $vid_type, $vid_width, $vid_height, $vid_addx, $vid_addy, $vid_source, $vid_match, $vid_par2 ) = (isset ( $vid_providers [$vid ["type"]] )) ? $vid_providers [$vid ["type"]] : $vid_providers ["_default"];
		} else {
			return;
		}

		unset ( $vid_providers );
		if (! empty ( $vid_auto )) {
			if ($vid_match and (preg_match ( "/$vid_match/i", $content, $vid_regs ) > 0))
				$content = $vid_regs [1];
			else
				return;
		}

		$uri = JURI::getInstance();
		if ( $uri->isSSL() && $vid ["type"] == 'youtube' ) $vid_source = preg_replace("/^http:/", "https:", $vid_source);
		$vid_source = preg_replace ( '/%vcode%/', $content, $vid_source );
		if (! is_array ( $vid_par2 ))
			$vid_par2 = array ();

		$vid_size = isset ( $params ["size"] ) ? intval ( $params ["size"] ) : 0;
		if (($vid_size > 0) and ($vid_size < $vid_sizemax)) {
			$vid_width = ( int ) ($vid_width * $vid_size / 100);
			$vid_height = ( int ) ($vid_height * $vid_size / 100);
		}
		$vid_width += $vid_addx;
		$vid_height += $vid_addy;
		if (! isset ( $params ["size"] )) {
			if (isset ( $params ["width"] ))
				if ($params ['width'] == '1') {
					$params ['width'] = $vid_minwidth;
				}
			if (isset ( $params ["width"] )) {
				$vid_width = intval ( $params ["width"] );
			}
			if (isset ( $params ["height"] ))
				if ($params ['height'] == '1') {
					$params ['height'] = $vid_minheight;
				}
			if (isset ( $params ["height"] )) {
				$vid_height = intval ( $params ["height"] );
			}
		}

		if ($vid_width < $vid_minwidth)
			$vid_width = $vid_minwidth;
		if ($vid_width > $vid_maxwidth)
			$vid_width = $vid_maxwidth;
		if ($vid_height < $vid_minheight)
			$vid_height = $vid_minheight;
		if ($vid_height > $vid_maxheight)
			$vid_height = $vid_maxheight;

		switch ($vid_type) {
			case 'divx' :
				$vid_par1 = array (array (1, 'classid', 'clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616' ), array (1, 'codebase', 'http://go.divx.com/plugin/DivXBrowserPlugin.cab' ), array (4, 'type', 'video/divx' ), array (4, 'pluginspage', 'http://go.divx.com/plugin/download/' ), array (6, 'src', $vid_source ), array (6, 'autoplay', 'false' ), array (5, 'width', $vid_width ), array (5, 'height', $vid_height ) );
				$vid_allowpar = array ('previewimage' );
				break;
			case 'flash' :
				$vid_par1 = array (array (1, 'classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' ), array (1, 'codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab' ), array (2, 'movie', $vid_source ), array (4, 'src', $vid_source ), array (4, 'type', 'application/x-shockwave-flash' ), array (4, 'pluginspage', 'http://www.macromedia.com/go/getflashplayer' ), array (6, 'quality', 'high' ), array (6, 'allowFullScreen', 'true' ), array (6, 'allowScriptAccess', 'never' ), array (5, 'width', $vid_width ), array (5, 'height', $vid_height ) );
				$vid_allowpar = array ('flashvars', 'wmode', 'bgcolor', 'quality' );
				break;
			case 'mediaplayer' :
				$vid_par1 = array (array (1, 'classid', 'clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95' ), array (1, 'codebase', 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab' ), array (4, 'type', 'application/x-mplayer2' ), array (4, 'pluginspage', 'http://www.microsoft.com/Windows/MediaPlayer/' ), array (6, 'src', $vid_source ), array (6, 'autostart', 'false' ), array (6, 'autosize', 'true' ), array (5, 'width', $vid_width ), array (5, 'height', $vid_height ) );
				$vid_allowpar = array ();
				break;
			case 'quicktime' :
				$vid_par1 = array (array (1, 'classid', 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' ), array (1, 'codebase', 'http://www.apple.com/qtactivex/qtplugin.cab' ), array (4, 'type', 'video/quicktime' ), array (4, 'pluginspage', 'http://www.apple.com/quicktime/download/' ), array (6, 'src', $vid_source ), array (6, 'autoplay', 'false' ), array (6, 'scale', 'aspect' ), array (5, 'width', $vid_width ), array (5, 'height', $vid_height ) );
				$vid_allowpar = array ();
				break;
			case 'realplayer' :
				$vid_par1 = array (array (1, 'classid', 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' ), array (4, 'type', 'audio/x-pn-realaudio-plugin' ), array (6, 'src', $vid_source ), array (6, 'autostart', 'false' ), array (6, 'controls', 'ImageWindow,ControlPanel' ), array (5, 'width', $vid_width ), array (5, 'height', $vid_height ) );
				$vid_allowpar = array ();
				break;
			default :
				return;
		}

		$vid_par3 = array ();
		foreach ( $params as $vid_key => $vid_value ) {
			if (in_array ( JString::strtolower ( $vid_key ), $vid_allowpar ))
				array_push ( $vid_par3, array (6, $vid_key, $bbcode->HTMLEncode ( $vid_value ) ) );
		}

		$vid_object = $vid_param = $vid_embed = array ();
		foreach ( array_merge ( $vid_par1, $vid_par2, $vid_par3 ) as $vid_data ) {
			list ( $vid_key, $vid_name, $vid_value ) = $vid_data;
			if ($vid_key & 1)
				$vid_object [$vid_name] = ' ' . $vid_name . '="' . preg_replace ( '/%vcode%/', $content, $vid_value ) . '"';
			if ($vid_key & 2)
				$vid_param [$vid_name] = '<param name="' . $vid_name . '" value="' . preg_replace ( '/%vcode%/', $content, $vid_value ) . '" />';
			if ($vid_key & 4)
				$vid_embed [$vid_name] = ' ' . $vid_name . '="' . preg_replace ( '/%vcode%/', $content, $vid_value ) . '"';
		}

		$tag_new = '<object';
		foreach ( $vid_object as $vid_data )
			$tag_new .= $vid_data;
		$tag_new .= '>';
		foreach ( $vid_param as $vid_data )
			$tag_new .= $vid_data;
		$tag_new .= '<embed';
		foreach ( $vid_embed as $vid_data )
			$tag_new .= $vid_data;
		$tag_new .= ' /></object>';

		return $tag_new;
	}

	function DoAttachment($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

			// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal) || ! is_object ( $bbcode->parent ) && ! isset ( $bbcode->parent->attachments )) {
			$filename = basename(trim(strip_tags($content)));
			return '['.JText::_('COM_KUNENA_FILEATTACH').' '.basename(! empty ( $params ["name"] ) ? $params ["name"] : trim(strip_tags($content))).']';
		}
		$attachments = &$bbcode->parent->attachments;
		$attachment = null;
		if (! empty ( $default )) {
			$attachment = KunenaForumMessageAttachmentHelper::get ($default);
			if (is_object ( $attachment )) {
				unset ( $attachments [$attachment->id] );
			}
		} elseif (empty ( $content )) {
			$attachment = array_shift ( $attachments );
		} elseif (!empty ( $attachments )) {
			foreach ( $attachments as $att ) {
				if ($att->filename == $content) {
					$attachment = $att;
					unset ( $attachments [$att->id] );
					break;
				}
			}
		}
		if (! $attachment && ! empty ( $bbcode->parent->inline_attachments )) {
			foreach ( $bbcode->parent->inline_attachments as $att ) {
				if ($att->filename == trim(strip_tags($content))) {
					$attachment = $att;
					break;
				}
			}
		}

		if (!is_object ( $attachment )) {
			return htmlspecialchars($content);

		} elseif ($attachment->exists() && (!$attachment->authorise() || $attachment->disabled)) {
			// Hide between content from non registered users
			$link = $attachment->getTextLink();
			return '<div class="kmsgattach">' . $link . '</div>';

		} elseif ($attachment->exists() && is_file ( JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}" )) {
			$bbcode->parent->inline_attachments [$attachment->id] = $attachment;
			// TODO: use absolute / relative url depending on where BBCode is shown
			$link = JUri::root() . "{$attachment->folder}/{$attachment->filename}";
			$image = $attachment->getImageLink();
			if (empty ( $image )) {
				return "<div class=\"kmsgattach\"><h4>" . JText::_ ( 'COM_KUNENA_FILEATTACH' ) . "</h4>" . JText::_ ( 'COM_KUNENA_FILENAME' ) . " <a href=\"" . $link . "\" target=\"_blank\" rel=\"nofollow\">" . $attachment->filename . "</a><br />" . JText::_ ( 'COM_KUNENA_FILESIZE' ) . ' ' . number_format ( intval ( $attachment->size ) / 1024, 0, '', ',' ) . ' KB' . "</div>";
			} else {
				return "<div class=\"kmsgimage\">{$attachment->getImageLink()}</div>";
			}

		} else {
			return '<div class="kmsgattach"><h4>' . JText::sprintf ( 'COM_KUNENA_ATTACHMENT_DELETED', htmlspecialchars($content) ) . '</h4></div>';

		}
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoFile($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			$filename = basename(! empty ( $params ["name"] ) ? $params ["name"] : trim(strip_tags($content)));
			return '[ '.basename(! empty ( $params ["name"] ) ? $params ["name"] : trim(strip_tags($content))).' ]';
		}

		if (JFactory::getUser()->id == 0 && KunenaFactory::getConfig()->showfileforguest == 0) {
			// Hide between content from non registered users
			return '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE' ) . '</b>';
		} else {
			jimport ( 'joomla.filesystem.file' );
			// Make sure that filename does not contain path or URL
			$filename = basename(! empty ( $params ["name"] ) ? $params ["name"] : trim(strip_tags($content)));

			$filepath = "attachments/legacy/files/{$filename}";
			if (! is_file ( KPATH_MEDIA . '/' . $filepath )) {
				// File does not exist (or URL was pointing somewhere else)
				return '<div class="kmsgattach"><h4>' . JText::sprintf ( 'COM_KUNENA_ATTACHMENT_DELETED', $bbcode->HTMLEncode ( $filename ) ) . '</h4></div>';
			} else {
				if (isset ( $bbcode->parent->attachments )) {
					// Remove attachment from the attachments list
					$attachments = &$bbcode->parent->attachments;
					foreach ( $attachments as $att ) {
						if ($att->filename == $filename && $att->folder == 'media/kunena/attachments/legacy/files') {
							$attachment = $att;
							unset ( $attachments [$att->id] );
							$bbcode->parent->inline_attachments [$attachment->id] = $attachment;
							break;
						}
					}
				}

				$fileurl = KURL_MEDIA . $filepath;
				$filesize = isset ( $params ["size"] ) ? $params ["size"] : filesize ( KPATH_MEDIA . '/' . $filepath );

				$tag_new = "<div class=\"kmsgattach\"><h4>" . JText::_ ( 'COM_KUNENA_FILEATTACH' ) . "</h4>";
				$tag_new .= JText::_ ( 'COM_KUNENA_FILENAME' ) . " <a href=\"" . $bbcode->HTMLEncode ( $fileurl ) . "\" target=\"_blank\" rel=\"nofollow\">" . $bbcode->HTMLEncode ( $filename ) . "</a><br />";
				$tag_new .= JText::_ ( 'COM_KUNENA_FILESIZE' ) . ' ' . $bbcode->HTMLEncode ( $filesize ) . "</div>";
				return $tag_new;
			}
		}
	}

	/**
	 * @param KunenaBBCode $bbcode
	 * @param $action
	 * @param $name
	 * @param $default
	 * @param $params
	 * @param $content
	 * @return bool|string
	 */
	function DoImage($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			return true;
		}

		$fileurl = trim(strip_tags($content));

		// Display tag in activity streams etc..
		if (!empty($bbcode->parent->forceMinimal)) {
			return "<a href=\"" . $fileurl . "\" rel=\"nofollow\" target=\"_blank\">" . basename($fileurl) . '</a>';
		}

		$config = KunenaFactory::getConfig();
		if (JFactory::getUser()->id == 0 && $config->showimgforguest == 0) {
			// Hide between content from non registered users
			return '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG' ) . '</b>';
		}

		if ($config->bbcode_img_secure != 'image') {
			if ($bbcode->autolink_disable == 0 && !preg_match("/\\.(?:gif|jpeg|jpg|jpe|png)$/ui", $fileurl)) {
				// If the image has not legal extension, return it as link or text
				$fileurl = $bbcode->HTMLEncode ( $fileurl );
				if ($config->bbcode_img_secure == 'link') {
					if (! preg_match ( '`^(/|https?://)`', $fileurl )) {
						$fileurl = 'http://' . $fileurl;
					}
					return "<a href=\"" . $fileurl . "\" rel=\"nofollow\" target=\"_blank\">" . $fileurl . '</a>';
				} else {
					return $fileurl;
				}
			}
		}

		// Legacy attachments support (mostly used to remove image from attachments list), but also fixes broken links
		if (isset ( $bbcode->parent->attachments ) && strpos ( $fileurl, '/media/kunena/attachments/legacy/images/' )) {
			// Make sure that filename does not contain path or URL
			$filename = basename($fileurl);

			// Remove attachment from the attachments list and show it if it exists
			/** @var array|KunenaForumMessageAttachment[] $attachments */
			$attachments = &$bbcode->parent->attachments;
			$attachment = null;
			foreach ( $attachments as $att ) {
				if ($att->filename == $filename && $att->folder == 'media/kunena/attachments/legacy/images') {
					$attachment = $att;
					unset ( $attachments [$att->id] );
					$bbcode->parent->inline_attachments [$attachment->id] = $attachment;
					return "<div class=\"kmsgimage\">{$attachment->getImageLink()}</div>";
				}
			}
			// No match -- assume that we have normal img tag
		}

		// Make sure we add image size if specified
		$width = ($params ['size'] ? ' width="' . (int) $params ['size'] . '"' : '');
		$fileurl = $bbcode->HTMLEncode ( $fileurl );

		// Need to check if we are nested inside a URL code
		if ($bbcode->autolink_disable == 0 && $config->lightbox) {
			return '<div class="kmsgimage"><a href="'.$fileurl.'" title="" rel="lightbox[gallery]"><img src="'.$fileurl.'"'.$width.' style="max-height:'.$config->imageheight.'px;" alt="" /></a></div>';
		}
		return '<div class="kmsgimage"><img src="' . $fileurl .'"'. $width .' style="max-height:'.$config->imageheight.'px;" alt="" /></div>';
	}

	function DoTerminal($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$colortext = isset($params ["colortext"]) ? $params ["colortext"] : '#ffffff';

		return "<div class=\"highlight\"><pre style=\"font-family:monospace;background-color:#444444;\"><span style=\"color:{$colortext};\">{$content}</span></pre></div>";
	}
}
