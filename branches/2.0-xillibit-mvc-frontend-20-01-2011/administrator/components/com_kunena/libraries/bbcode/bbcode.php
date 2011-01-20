<?php
/**
 * @version $Id$
 * Kunena Component - KunenaBBCode Class
 * @package Kunena
 *
 * @Copyright (C) 2009-2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_ADMIN . '/libraries/bbcode/nbbc/nbbc.php';

// TODO: add possibility to hide contents from these tags:
// [hide], [confidential], [spoiler], [attachment], [code]

/**
 * Kunena BBCode Class
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.7
 */
class KunenaBBCode extends BBCode {
	public $autolink_disable = false;
	/**
	 * Object Constructor
	 *
	 * @param
	 * @return	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct ();
		$this->defaults = new KunenaBBCodeLibrary;
		$this->tag_rules = $this->defaults->default_tag_rules;
		$this->smileys = $this->defaults->default_smileys;
		// TODO: Make smilie path configurable or define
		$this->SetSmileyURL ( JURI::root () . 'components/com_kunena/template/default/images/emoticons' );
		$this->SetDetectURLs ( true );
	}

	/**
	 * Get Singleton Instance
	 *
	 * @param
	 * @return	void
	 * @since	1.7
	 */
	public function &getInstance() {
		static $instance = false;
		if (! $instance) {
			$instance = new KunenaBBCode ();
		}
		return $instance;
	}
}

class KunenaBBCodeLibrary extends BBCodeLibrary {
	var $default_smileys = array();
	var $default_tag_rules = array(
			'b' => array(
				'simple_start' => "<b>",
				'simple_end' => "</b>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<b>",
				'plain_end' => "</b>"
			),

			'i' => array(
				'simple_start' => "<i>",
				'simple_end' => "</i>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>"
			),

			'u' => array(
				'simple_start' => "<u>",
				'simple_end' => "</u>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<u>",
				'plain_end' => "</u>"
			),

			's' => array(
				'simple_start' => "<strike>",
				'simple_end' => "</strike>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>"
			),

			'strike' => array(
				'simple_start' => "<strike>",
				'simple_end' => "</strike>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>"
			),

			'tt' => array(
				'simple_start' => "<tt>",
				'simple_end' => "</tt>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link'),
				'plain_start' => "<i>",
				'plain_end' => "</i>"
			),

			'pre' => array(
				'simple_start' => "<pre>",
				'simple_end' => "</pre>",
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'plain_start' => "<i>",
				'plain_end' => "</i>"
			),

			'font' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'allow' => array('_default' => '/^[a-zA-Z0-9._ -]+$/'),
				'method' => 'DoFont',
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
			),

			'color' => array(
				'mode' => BBCODE_MODE_ENHANCED,
				'allow' => array('_default' => '/^#?[a-zA-Z0-9._ -]+$/'),
				'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
			),

			'size' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoSize',
				'allow' => array('_default' => '/^[0-9.]+(px|em|pt|%)?$/D'),
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
			),

			'sup' => array(
				'simple_start' => "<sup>",
				'simple_end' => "</sup>",
				'class' => 'inline',
				'allow_in' => array('listitem', 'block', 'columns', 'inline', 'link')
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
				'allow_in' => array('listitem', 'block', 'columns')
			),

			'hide' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoHide',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns')
			),

			'confidential' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoConfidential',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns')
			),

			'map' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoMap',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT
			),

			'ebay' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoEbay',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT
			),

			'article' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoArticle',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT
			),

			'tableau' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoTableau',
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT
			),

			'video' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoVideo',
				'allow' => array( 'type' => '/^[\w\d.-_]*$/', 'param' => '/^[\w]*$/', 'size' => '/^\d*$/', 'width' => '/^\d*$/', 'height' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT
			),

			'img' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoImage',
				'allow' => array( 'size' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns', 'link'),
				'content' => BBCODE_PROHIBIT,
				'plain_start' => "[image]"
			),

			'file' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoFile',
				'allow' => array( 'size' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT,
				'plain_start' => "[file]"
			),

			'attachment' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoAttachment',
				'allow' => array( '_default' => '/^\d*$/' ),
				'class' => 'block',
				'allow_in' => array('listitem', 'block', 'columns'),
				'content' => BBCODE_PROHIBIT,
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
				'plain_start' => "<a href=\"{\$link}\">",
				'plain_end' => "</a>",
				'plain_content' => array('_content', '_default'),
				'plain_link' => array('_default', '_content'),
			),

			'email' => array(
				'mode' => BBCODE_MODE_LIBRARY,
				'method' => 'DoEmail',
				'class' => 'link',
				'allow_in' => array('listitem', 'block', 'columns', 'inline'),
				'content' => BBCODE_REQUIRED,
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
				'plain_start' => "\n",
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
	);

	function __construct() {
		$db = JFactory::getDBO ();
		$query = "SELECT code, location FROM #__kunena_smileys";
		$db->setQuery ( $query );
		$smileys = $db->loadObjectList ();

		foreach ( $smileys as $smiley ) {
			$this->default_smileys[$smiley->code] = $smiley->location;
		}
	}

	// Format a [url] tag by producing an <a>...</a> element.
	// The URL only allows http, https, mailto, and ftp protocols for safety.
	function DoURL($bbcode, $action, $name, $default, $params, $content) {
		// We can't check this with BBCODE_CHECK because we may have no URL before the content
		// has been processed.
		if ($action == BBCODE_CHECK) {
			$bbcode->autolink_disable = 1;
			return true;
		}

		$bbcode->autolink_disable = 0;
		$url = is_string ( $default ) ? $default : $bbcode->UnHTMLEncode ( strip_tags ( $content ) );
		if ($bbcode->IsValidURL ( $url )) {
			if ($bbcode->debug)
				echo "ISVALIDURL<br />";
			if ($bbcode->url_targetable !== false && isset ( $params ['target'] ))
				$target = " target=\"" . htmlspecialchars ( $params ['target'] ) . "\"";
			else
				$target = "";
			if ($bbcode->url_target !== false)
				if (! ($bbcode->url_targetable == 'override' && isset ( $params ['target'] )))
					$target = " target=\"" . htmlspecialchars ( $bbcode->url_target ) . "\"";
			return '<a href="' . htmlspecialchars ( $url ) . '" class="bbcode_url"' . $target . '>' . $content . '</a>';
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
		if (! is_string ( $default ) || strlen ( $default ) == "") {
			$elem = 'ul';
			$type = '';
		} else if ($default == '1') {
			$elem = 'ol';
			$type = '';
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

		static $spoilerid = 0;
		if (empty ( $spoilerid )) {
			// Only need the script for the first spoiler we find
			$document = JFactory::getDocument();
			$document->addCustomTag (
			'<script language = "JavaScript" type = "text/javascript">
			function kShowDetail(srcElement) {
				var targetID, srcElement, targetElement, imgElementID, imgElement;
				targetID = srcElement.id + "_details";
				imgElementID = srcElement.id + "_img";
				targetElement = document.getElementById(targetID);
				imgElement = document.getElementById(imgElementID);
				if (targetElement.style.display == "none") {
					targetElement.style.display = "";
					imgElement.src = "' . JURI::root() . 'components/com_kunena/template/default/images/emoticons/w00t.png";
				} else {
					targetElement.style.display = "none";
					imgElement.src = "' . JURI::root() . 'components/com_kunena/template/default/images/emoticons/pinch.png";
				}
			} </script>' );
		}
		$spoilerid ++;
		$randomid = 'spoiler_' . rand ();
		return '<div id="' . $randomid . '" onclick="javascript:kShowDetail(this);" class = "kspoiler" ><img id="' . $randomid . '_img"' . ' src="' . JURI::root() . 'components/com_kunena/template/default/images/emoticons/pinch.png" border="0" alt=":pinch:" /> <strong>' . (isset ( $params ["title"] ) ? ($params ["title"]) : (JText::_ ( 'COM_KUNENA_BBCODE_SPOILER' ))) . '</strong></div><div id="' . $randomid . '_details" style="display:none;"><span class="fb_quote">' . $content . '</span></div>';
	}

	function DoHide($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (JFactory::getUser ()->id == 0) {
			// Hide between content from non registered users
			return JText::_ ( 'COM_KUNENA_BBCODE_HIDDENTEXT' );
		} else {
			// Display but highlight the fact that it is hidden from guests
			return '<b>' . JText::_ ( 'COM_KUNENA_BBCODE_HIDE' ) . '</b>' . '<div class="kmsgtext-hide">' . $content . '</div>';
		}
	}

	function DoConfidential($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$me = KunenaFactory::getUser ();
		if ((!empty($bbcode->parent->message->userid) && $bbcode->parent->message->userid == $me->userid) || $me->isModerator($bbcode->parent->message->catid)) {
			// Display but highlight the fact that it is hidden from everyone except admins and mods
			return '<b>' . JText::_ ( 'COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT' ) . '</b><div class="kmsgtext-confidential">' . $content . '</div>';
		}
	}

	function DoMap($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		static $id = false;
		static $sensor = true;
		$document = JFactory::getDocument();

		if ($id === false) $document->addScript('http://maps.google.com/maps/api/js?sensor='.($sensor == true ? 'true' : 'false'));

		$id ++;
		$mapid = 'kgooglemap'.$id;

		$document->addScriptDeclaration("
		// <![CDATA[
			var geocoder;
			var $mapid;

			window.addEvent('domready', function() {
				geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(37.333586,-121.894684);
			var myOptions = {
				zoom: 10,
					center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			$mapid = new google.maps.Map($('".$mapid."'), myOptions);
			});

			window.addEvent('domready', function() {
			var address = '$content';
			if (geocoder) {
				geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$mapid.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						map: $mapid,
						position: results[0].geometry.location
					});
				} else {
					var contentString = '<p><strong>".KunenaHtmlParser::JSText('COM_KUNENA_GOOGLE_MAP_NO_GEOCODE')." <i>$content</i></strong></p>';
					var infowindow$mapid = new google.maps.InfoWindow({ content: contentString });
						infowindow$mapid.open($mapid);
				}
				});
			}
			});

		// ]]>"
		);

		return '<div id="'.$mapid.'" class="kgooglemap">'.KunenaHtmlParser::JSText('COM_KUNENA_GOOGLE_MAP_NOT_VISIBLE').'</div>';
	}

	function DoEbay($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$config = KunenaFactory::getConfig();
		$ebay_maxwidth = (int) (($config->rtewidth * 9) / 10); // Max 90% of text width
		$ebay_maxheight = (int) ($config->rteheight); // max. display size

		if (is_numeric ( $content )) {
			// Numeric: we have to assume this is an item id
			return '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=' . $content . '&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=normal&itemid=' . $content . '&campid=5336042350"></embed></object>';
		} else {
			// Non numeric: we have to assume this is a search
			return '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=' . $content . '&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $config->ebaylanguagecode . '&mode=search&query=' . $content . '&campid=5336042350"></embed></object>';
		}
	}

	function DoArticle($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$articleid = intval($content);

		jimport ( 'joomla.version' );
		$jversion = new JVersion ();
		$user = JFactory::getUser ();
		$db = JFactory::getDBO ();
		$site = JFactory::getApplication('site');
		if ($jversion->RELEASE == '1.5') {
			$query = 'SELECT a.*, u.name AS author, u.usertype, cc.title AS category, s.title AS section,
				s.published AS sec_pub, cc.published AS cat_pub, s.access AS sec_access, cc.access AS cat_access
				FROM #__content AS a
				LEFT JOIN #__categories AS cc ON cc.id = a.catid
				LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = "content"
				LEFT JOIN #__users AS u ON u.id = a.created_by
				WHERE a.id=' . $db->quote ( $articleid );

			$db->setQuery ( $query );
			$article = $db->loadObject ();
			if ($article) {
				// Get credentials to check if the user has right to see the article
				$params = clone($site->getParams('com_content'));
				$aparams = new JParameter($article->attribs);
				$params->merge($aparams);

				if (($article->catid && $article->cat_access > $user->get('aid', 0))
					|| ($article->sectionid && $article->sec_access > $user->get('aid', 0))
					|| ($article->access > $user->get('aid', 0))) {
					$denied = true;
				}
			}
		} else {
			$query = 'SELECT a.*, u.name AS author, u.usertype, cc.title AS category,
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
				$registry->loadJSON($article->attribs);
				$article->params = clone $params;
				$article->params->merge($registry);
				$params = $article->params;

				$viewlevels = $user->getAuthorisedViewLevels();
				if ( !in_array($article->access, $viewlevels) ) {
					$denied = true;
				}
			}
		}

		$html = $link = '';
		if (!$article || (!$article->cat_pub && $article->catid) || (!$article->sec_pub && $article->sectionid)) {
			// FIXME: translation
			$html = JText::_ ( "Article cannot be shown" );
		} elseif (!empty($denied)) {
			// FIXME: translation
			$html = JText::_("This message contains an article, but you do not have permissions to see it.");
		} else {
			// Identify the source of the event to be Kunena itself
			// this is important to avoid recursive event behaviour with our own plugins
			$params->set('ksource', 'kunena');
			JPluginHelper::importPlugin('content');
			$dispatcher = JDispatcher::getInstance();
			$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, 0));

			require_once (JPATH_ROOT.'/components/com_content/helpers/route.php');
			if ($jversion->RELEASE == '1.5') {
				$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->sectionid));
			} else {
				$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid));
			}

			// TODO: make configurable
			if (!$default) $default = 'intro';
			switch ($default) {
				case 'full':
					if ( !empty($article->fulltext) ) {
						$html = $article->fulltext;
						$link = '<a href="'.$url.'" class="readon">'.JText::sprintf('Read article...').'</a>';
						break;
					}
					// continue to intro
				case 'intro':
					if ( !empty($article->introtext) ) {
						$html = $article->introtext;
						$link = '<a href="'.$url.'" class="readon">'.JText::sprintf('Read more...').'</a>';
						break;
					}
					// continue to link
				case 'link':
				default:
					$link = '<a href="'.$url.'" class="readon">'.$article->title.'</a>';
					break;
			}
		}
		return '<div class="kmsgtext-article">' . $html . '</div>' . $link;
	}

	function DoQuote($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$post = isset($params["post"]) ? $params["post"] : false;
		$user = isset($default) ? $default : false;
		$html = '';
		if ($user) $html .= "<b>" . $user . " " . JText::_ ( 'COM_KUNENA_POST_WROTE' ) . ":</b>\n";
		$html .= '<div class="kmsgtext-quote">' . $content . '</div>';
		return $html;
	}

	function DoCode($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		static $geshipath = false;
		if ($geshipath === false && KunenaFactory::getConfig ()->highlightcode) {
			$geshipath = null;
			if (substr ( JVERSION, 0, 3 ) == '1.5') {
				$geshipath = JPATH_ROOT . '/libraries/geshi';
				jimport ( 'geshi.geshi' );
			} else {
				$geshipath = JPATH_ROOT . '/plugins/content/geshi/geshi';
				require_once ($geshipath . '/geshi.php');
			}
			if (file_exists ( $geshipath . '/geshi.php' )) {
				if (substr ( JVERSION, 0, 3 ) == '1.5')
					$geshipath .= '/geshi';
			} else {
				$geshipath = null;
			}
		}
		if ($geshipath) {
			$type = isset ( $params ["type"] ) ? $params ["type"] : "php";
			if ($type == "js")
				$type = "javascript";
			else if ($type == "html")
				$type = "html4strict";
			if (! file_exists ( $geshipath . DS . $type . ".php" ))
				$type = "php";
			$geshi = new GeSHi ( $bbcode->UnHTMLEncode($content), $type );
			$geshi->enable_keyword_links ( false );
			$code = $geshi->parse_code ();
			return '<div class="highlight">' . $code . '</div>';
		} else {
			$types = array ("php", "mysql", "html", "js", "javascript" );
			if (! empty ( $params ["type"] ) && in_array ( $params ["type"], $types )) {
				$t_type = $params ["type"];
			} else {
				$t_type = "php";
			}
			return "<div class=\"highlight\"><pre class=\"{$t_type}\">{$content}</pre></div>";
		}
	}

	function doTableau($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK) {
			$bbcode->autolink_disable = 1;
			return true;
		}

		$bbcode->autolink_disable = 0;
		if (!$content)
			return;

		$config = KunenaFactory::getConfig();

		$viz_maxwidth = (int) (($config->rtewidth * 9) / 10); // Max 90% of text width
		$viz_maxheight = (isset ( $params ["height"] ) && is_numeric($params ["height"])) ? (int) $params ["height"] : (int) $config->rteheight;

		//$url_data = parse_url ( $between );
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

	function DoVideo($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (! $content)
			return;

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
			$vid_auto = (preg_match ( '/^http:\/\/.*?([^.]*)\.[^.]*(\/|$)/', $content, $vid_regs ) > 0);
			if ($vid_auto) {
				$vid ["type"] = JString::strtolower ( $vid_regs [1] );
				switch ($vid ["type"]) {
					case 'clip' :
						$vid ["type"] = 'clip.vn';
						break;
					case 'web' :
						$vid ["type"] = 'web.de';
						break;
					case 'wideo' :
						$vid ["type"] = 'wideo.fr';
						break;
				}
			}
		}

		$vid_providers = array (

		'animeepisodes' => array ('flash', 428, 352, 0, 0, 'http://video.animeepisodes.net/vidiac.swf', '\/([\w\-]*).htm', array (array (6, 'flashvars', 'video=%vcode%' ) ) ),

		'biku' => array ('flash', 450, 364, 0, 0, 'http://www.biku.com/opus/player.swf?VideoID=%vcode%&embed=true&autoStart=false', '\/([\w\-]*).html', '' ),

		'bofunk' => array ('flash', 446, 370, 0, 0, 'http://www.bofunk.com/e/%vcode%', '', '' ),

		'break' => array ('flash', 464, 392, 0, 0, 'http://embed.break.com/%vcode%', '', '' ),

		'clip.vn' => array ('flash', 448, 372, 0, 0, 'http://clip.vn/w/%vcode%,en,0', '\/watch\/([\w\-]*),vn', '' ),

		'clipfish' => array ('flash', 464, 380, 0, 0, 'http://www.clipfish.de/videoplayer.swf?as=0&videoid=%vcode%&r=1&c=0067B3', 'videoid=([\w\-]*)', '' ),

		'clipshack' => array ('flash', 430, 370, 0, 0, 'http://clipshack.com/player.swf?key=%vcode%', 'key=([\w\-]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'collegehumor' => array ('flash', 480, 360, 0, 0, 'http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=%vcode%&fullscreen=1', '\/video:(\d*)', '' ),

		'current' => array ('flash', 400, 400, 0, 0, 'http://current.com/e/%vcode%', '\/items\/(\d*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'dailymotion' => array ('flash', 420, 331, 0, 0, 'http://www.dailymotion.com/swf/%vcode%', '\/video\/([a-zA-Z0-9]*)', '' ),

		'downloadfestival' => array ('flash', 450, 358, 0, 0, 'http://www.downloadfestival.tv/mofo/video/player/playerb003External.swf?rid=%vcode%', '\/watch\/([\d]*)', '' ),

		// Cannot allow public flash objects as it opens up a whole set of vulnerabilities through hacked flash files
		//				'flashvars' => array ('flash', 480, 360, 0, 0, $content, '', array (array (6, 'flashvars', $vid ["param"] ) ) ),
		//
		'fliptrack' => array ('flash', 402, 302, 0, 0, 'http://www.fliptrack.com/v/%vcode%', '\/watch\/([\w\-]*)', '' ),

		'fliqz' => array ('flash', 450, 392, 0, 0, 'http://content.fliqz.com/components/2d39cfef9385473c89939c2a5a7064f5.swf', 'vid=([\w]*)', array (array (6, 'flashvars', 'file=%vcode%&' ), array (6, 'wmode', 'transparent' ), array (6, 'bgcolor', '#000000' ) ) ),

		'gametrailers' => array ('flash', 480, 392, 0, 0, 'http://www.gametrailers.com/remote_wrap.php?mid=%vcode%', '\/(\d*).html', '' ),

		'gamevideos' => array ('flash', 420, 405, 0, 0, 'http://www.gamevideos.com/swf/gamevideos11.swf?embedded=1&fullscreen=1&autoplay=0&src=http://www.gamevideos.com/video/videoListXML%3Fid%3D%vcode%%26adPlay%3Dfalse', '\/video\/id\/(\d*)', array (array (6, 'bgcolor', '#000000' ), array (6, 'wmode', 'window' ) ) ),

		'glumbert' => array ('flash', 448, 336, 0, 0, 'http://www.glumbert.com/embed/%vcode%', '\/media\/([\w\-]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'gmx' => array ('flash', 425, 367, 0, 0, 'http://video.gmx.net/movie/%vcode%', '\/watch\/(\d*)', '' ),

		'google' => array ('flash', 400, 326, 0, 0, 'http://video.google.com/googleplayer.swf?docId=%vcode%', 'docid=(\d*)', '' ),

		'googlyfoogly' => array ('mediaplayer', 400, 300, 0, 25, 'http://media.googlyfoogly.com/images/videos/%vcode%.wmv', '', '' ),

		'ifilm' => array ('flash', 448, 365, 0, 0, 'http://www.ifilm.com/efp', '\/video\/(\d*)', array (array (6, 'flashvars', 'flvbaseclip=%vcode%' ) ) ),

		'jumpcut' => array ('flash', 408, 324, 0, 0, 'http://jumpcut.com/media/flash/jump.swf?id=%vcode%&asset_type=movie&asset_id=%vcode%&eb=1', '\/\?id=([\w\-]*)', '' ),

		'kewego' => array ('flash', 400, 368, 0, 0, 'http://www.kewego.com/p/en/%vcode%.html', '\/([\w\-]*)\.html', array (array (6, 'wmode', 'transparent' ) ) ),

		'liveleak' => array ('flash', 450, 370, 0, 0, 'http://www.liveleak.com/player.swf', '\/view\?i=([\w\-]*)', array (array (6, 'flashvars', 'autostart=false&token=%vcode%' ), array (6, 'wmode', 'transparent' ) ) ),

		'livevideo' => array ('flash', 445, 369, 0, 0, 'http://www.livevideo.com/flvplayer/embed/%vcode%', '', '' ),

		'megavideo' => array ('flash', 432, 351, 0, 0, 'http://www.megavideo.com/v/%vcode%..0', '', array (array (6, 'wmode', 'transparent' ) ) ),

		'metacafe' => array ('flash', 400, 345, 0, 0, 'http://www.metacafe.com/fplayer/%vcode%/.swf', '\/watch\/(\d*\/[\w\-]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'mofile' => array ('flash', 480, 395, 0, 0, 'http://tv.mofile.com/cn/xplayer.swf', '\.com\/([\w\-]*)', array (array (6, 'flashvars', 'v=%vcode%&autoplay=0&nowSkin=0_0' ), array (6, 'wmode', 'transparent' ) ) ),

		'multiply' => array ('flash', 400, 350, 0, 0, 'http://images.multiply.com/multiply/multv.swf', '', array (array (6, 'flashvars', 'first_video_id=%vcode%&base_uri=multiply.com&is_owned=1' ) ) ),

		'myspace' => array ('flash', 430, 346, 0, 0, 'http://lads.myspace.com/videos/vplayer.swf', 'VideoID=(\d*)', array (array (6, 'flashvars', 'm=%vcode%&v=2&type=video' ) ) ),

		'myvideo' => array ('flash', 470, 406, 0, 0, 'http://www.myvideo.de/movie/%vcode%', '\/watch\/(\d*)', '' ),

		'quxiu' => array ('flash', 437, 375, 0, 0, 'http://www.quxiu.com/photo/swf/swfobj.swf?id=%vcode%', '\/play_([\d_]*)\.htm', array (array (6, 'menu', 'false' ) ) ),

		'revver' => array ('flash', 480, 392, 0, 0, 'http://flash.revver.com/player/1.0/player.swf?mediaId=%vcode%', '\/video\/([\d_]*)', '' ),

		'rutube' => array ('flash', 400, 353, 0, 0, 'http://video.rutube.ru/%vcode%', '\.html\?v=([\w]*)' ),

		'sapo' => array ('flash', 400, 322, 0, 0, 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/%vcode%/mov/1', 'videos\.sapo\.pt\/([\w]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'sevenload' => array ('flash', 425, 350, 0, 0, 'http://sevenload.com/pl/%vcode%/425x350/swf', '\/videos\/([\w]*)', array (array (6, 'flashvars', 'apiHost=api.sevenload.com&showFullScreen=1' ) ) ),

		'sharkle' => array ('flash', 340, 310, 0, 0, 'http://sharkle.com/sharkle.swf?rnd=%vcode%&buffer=3', '', array (array (6, 'wmode', 'transparent' ) ) ),

		'spikedhumor' => array ('flash', 400, 345, 0, 0, 'http://www.spikedhumor.com/player/vcplayer.swf?file=http://www.spikedhumor.com/videocodes/%vcode%/data.xml&auto_play=false', '\/articles\/([\d]*)', '' ),

		'stickam' => array ('flash', 400, 300, 0, 0, 'http://player.stickam.com/flashVarMediaPlayer/%vcode%', 'mId=([\d]*)', '' ),

		'streetfire' => array ('flash', 428, 352, 0, 0, 'http://videos.streetfire.net/vidiac.swf', '\/([\w-]*).htm', array (array (6, 'flashvars', 'video=%vcode%' ) ) ),

		'stupidvideos' => array ('flash', 451, 433, 0, 0, 'http://img.purevideo.com/images/player/player.swf?sa=1&sk=5&si=2&i=%vcode%', '\/\?m=new#([\d_]*)', '' ),

		'toufee' => array ('flash', 550, 270, 0, 0, 'http://toufee.com/movies/Movie.swf', 'u=[a-zA-Z]*(\d*)', array (array (6, 'flashvars', 'movieID=%vcode%&domainName=toufee' ) ) ),

		'tudou' => array ('flash', 400, 300, 0, 0, 'http://www.tudou.com/v/%vcode%', '\/view\/([\w-]*)', array (array (6, 'wmode', 'transparent' ) ) ),

		'unf-unf' => array ('flash', 425, 350, 0, 0, 'http://www.unf-unf.de/video/flvplayer.swf?file=http://www.unf-unf.de/video/clips/%vcode%.flv', '\/([\w-]*).html', array (array (6, 'wmode', 'transparent' ) ) ),

		'uume' => array ('flash', 400, 342, 0, 0, 'http://www.uume.com/v/%vcode%_UUME', '\/play_([\w-]*)', '' ),

		'veoh' => array ('flash', 540, 438, 0, 0, 'http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%vcode%', '\/videos\/([\w-]*)', '' ),

		'videoclipsdump' => array ('flash', 480, 400, 0, 0, 'http://www.videoclipsdump.com/player/simple.swf', '', array (array (6, 'flashvars', 'url=http://www.videoclipsdump.com/files/%vcode%.flv&autoplay=0&watermark=http://www.videoclipsdump.com/flv_watermark.php&buffer=10&full=0&siteurl=http://www.videoclipsdump.com&interval=10000&totalrotate=3' ) ) ),

		'videojug' => array ('flash', 400, 345, 0, 0, 'http://www.videojug.com/film/player?id=%vcode%', '', '' ),

		'videotube' => array ('flash', 480, 400, 0, 0, 'http://www.videotube.de/flash/player.swf', '\/watch\/(\d*)', array (array (6, 'flashvars', 'baseURL=http://www.videotube.de/watch/%vcode%' ), array (6, 'wmode', 'transparent' ) ) ),

		'vidiac' => array ('flash', 428, 352, 0, 0, 'http://www.vidiac.com/vidiac.swf', '\/([\w-]*).htm', array (array (6, 'flashvars', 'video=%vcode%' ) ) ),

		'vidilife' => array ('flash', 445, 369, 0, 0, 'http://www.vidiLife.com/flash/flvplayer.swf?autoStart=0&popup=1&video=http://www.vidiLife.com/media/flash_api.cfm?id=%vcode%&version=8', '', '' ),

		'vimeo' => array ('flash', 400, 321, 0, 0, 'http://www.vimeo.com/moogaloop.swf?clip_id=%vcode%&server=www.vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=0&color=', '\.com\/(\d*)', '' ),

		'wangyou' => array ('flash', 441, 384, 0, 0, 'http://v.wangyou.com/images/x_player.swf?id=%vcode%', '\/p(\d*).html', array (array (6, 'wmode', 'transparent' ) ) ),

		'web.de' => array ('flash', 425, 367, 0, 0, 'http://video.web.de/movie/%vcode%', '\/watch\/(\d*)', '' ),

		'wideo.fr' => array ('flash', 400, 368, 0, 0, 'http://www.wideo.fr/p/fr/%vcode%.html', '\/([\w-]*).html', array (array (6, 'wmode', 'transparent' ) ) ),

		'youku' => array ('flash', 480, 400, 0, 0, 'http://player.youku.com/player.php/sid/%vcode%/v.swf', '\/v_show\/id_(.*)\.html', '' ),

		'youtube' => array ('flash', 425, 355, 0, 0, 'http://www.youtube.com/v/%vcode%?fs=1&hd=0&rel=1', '\/watch\?v=([\w\-]*)' , array (array (6, 'wmode', 'transparent' ) ) ),

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

		if (! is_object ( $bbcode->parent ) && ! isset ( $bbcode->parent->attachments )) {
			return '[Attachment]'; // TODO
		}
		$attachments = &$bbcode->parent->attachments;
		$attachment = null;
		if (! empty ( $default )) {
			$attobj = CKunenaAttachments::getInstance ();
			$attachment = $attobj->getAttachment ( $default );
			if (is_object ( $attachment )) {
				unset ( $attachments [$attachment->id] );
			}
		} else if (empty ( $content )) {
			$attachment = array_shift ( $attachments );
		} else {
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
				if ($att->filename == trim($bbcode->UnHTMLEncode(strip_tags($content)))) {
					$attachment = $att;
					break;
				}
			}
		}

		if (is_object ( $attachment ) && ! empty ( $attachment->disabled )) {
			// Hide between content from non registered users
			return '<div class="kmsgattach">' . $attachment->textLink . '</div>';
		} else {
			if (is_object ( $attachment ) && is_file ( JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}" )) {
				$bbcode->parent->inline_attachments [$attachment->id] = $attachment;
				$link = JURI::base () . "{$attachment->folder}/{$attachment->filename}";
				if (empty ( $attachment->imagelink )) {
					return "<div class=\"kmsgattach\"><h4>" . JText::_ ( 'COM_KUNENA_FILEATTACH' ) . "</h4>" . JText::_ ( 'COM_KUNENA_FILENAME' ) . " <a href=\"" . $link . "\" target=\"_blank\" rel=\"nofollow\">" . $attachment->filename . "</a><br />" . JText::_ ( 'COM_KUNENA_FILESIZE' ) . ' ' . number_format ( intval ( $attachment->size ) / 1024, 0, '', ',' ) . ' KB' . "</div>";
				} else {
					return "<div class=\"kmsgimage\">{$attachment->imagelink}</div>";
				}
			} else {
				return '<div class="kmsgattach"><h4>' . JText::sprintf ( 'COM_KUNENA_ATTACHMENT_DELETED', $content ) . '</h4></div>';
			}
		}
	}

	function DoFile($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		if (JFactory::getUser()->id == 0 && KunenaFactory::getConfig()->showfileforguest == 0) {
			// Hide between content from non registered users
			return '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE' ) . '</b>';
		} else {
			jimport ( 'joomla.filesystem.file' );
			// Make sure that filename does not contain path or URL
			$filename = basename(! empty ( $params ["name"] ) ? $params ["name"] : trim($bbcode->UnHTMLEncode(strip_tags($content))));

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

	function DoImage($bbcode, $action, $name, $default, $params, $content) {
		if ($action == BBCODE_CHECK)
			return true;

		$config = KunenaFactory::getConfig();
		if (JFactory::getUser()->id == 0 && $config->showimgforguest == 0) {
			// Hide between content from non registered users
			return '<b>' . JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG' ) . '</b>';
		}
		$fileurl = trim($bbcode->UnHTMLEncode(strip_tags($content)));
		if ($config->bbcode_img_secure != 'image') {
			if (!preg_match("/\\.(?:gif|jpeg|jpg|jpe|png)$/ui", $fileurl)) {
				// If the image has not legal extension, return it as link or text
				$fileurl = $bbcode->HTMLEncode ( $fileurl );
				if ($config->bbcode_img_secure == 'link') {
					if (! preg_match ( "`^(https?://)`", $fileurl )) {
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
			$attachments = &$bbcode->parent->attachments;
			$attachment = null;
			foreach ( $attachments as $att ) {
				if ($att->filename == $filename && $att->folder == 'media/kunena/attachments/legacy/images') {
					$attachment = $att;
					unset ( $attachments [$att->id] );
					$bbcode->parent->inline_attachments [$attachment->id] = $attachment;
					return "<div class=\"kmsgimage\">{$attachment->imagelink}</div>";
				}
			}
			// No match -- assume that we have normal img tag
		}

		// Make sure we add image size if specified
		$width = ($params ['size'] ? ' width="' . $params ['size'] . '"' : '');
		$fileurl = $bbcode->HTMLEncode ( $fileurl );

		// Need to check if we are nested inside a URL code
		if ($bbcode->autolink_disable == 0 && $config->lightbox) {
			return '<a title="" rel="lightbox[gallery]" href="' . $fileurl . '"><img src="' . $fileurl . '"' . $width . ' alt="" /></a>';
		}
		return "<img src=\"{$fileurl}\" {$width} alt=\"\" />";
	}
}