<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 **/

/*
############################################################################
# CATEGORY: Parser.TagParser                 DEVELOPMENT DATUM: 13.11.2007 #
# VERSION:  00.08.00                         LAST EDIT   DATUM: 12.12.2007 #
# FILENAME: interpreter.Kunena.inc.php                                  #
# AUTOR:    Miro Dietiker, MD Systems, All rights reserved                 #
# LICENSE:  http://www.gnu.org/copyleft/gpl.html GNU/GPL                   #
# CONTACT: m.dietiker@md-systems.ch        (c) 2007 Miro Dietiker 13.11.2007 #
############################################################################
# This parser is based on an earlier CMS parser implementation.
# It has been completely rewritten and generalized for Kunena and
# was also heavily tested.
# However it should be: extensible, fast, ungreedy regarding resources
# stateful, enforcing strict output rules as defined
# Hope it works ;-)
############################################################################

implement further extended links (username, ...)
*/

defined( '_JEXEC' ) or die();

include_once (KUNENA_PATH_LIB . DS . "kunena.parser.bbcode.php");

include_once (KUNENA_PATH_LIB . DS . "kunena.google.maps.class.php");

class KunenaBBCodeInterpreter extends BBCodeInterpreter {
	// these are samples... we used the parser to refer to files!
	// did here a local caching, but using also database lookups - removed
	var $spoilerid = 0;

	function &NewTask() {
		/*
		# Builds new Task
		# RET
		# object: the task object
		# TAGPARSER_RET_ERR
		*/
		$task = new KunenaBBCodeParserTask ( $this );
		return $task;
	}

	function hyperlink($text) {
		$text = ' ' . $text . ' ';

		// match protocol://address:port/path/file.extension?some=variable&another=asf%
		// match protocol://address/path/file.extension?some=variable&another=asf%

		// match www.something.domain:port/path/file.extension?some=variable&another=asf%
		// match www.something.domain/path/file.extension?some=variable&another=asf%

		$text = preg_replace ( '/(?<!S)((http(s?):\/\/)|(www\.[a-zA-Z0-9-_]+\.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/u', '<a href="http$3://$4$5" target="_blank" rel="nofollow">$4$5</a>', $text );

		// match name@address
		$text = preg_replace ( '/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/u', '<a href="mailto:$1">$1</a>', $text );

		return substr ( $text, 1, - 1 );
	}

	function PostProcessing(&$task) {
		$kunena_config = KunenaFactory::getConfig ();
		if ($kunena_config->trimlongurls) {
			// shorten URL text if they are too long (>65chars)
			$task->text = preg_replace ( '/<a href=(\"|\')((http(s?):\/\/)?(([^\'\"]{' . $kunena_config->trimlongurlsfront . '})([^\'\"]{4,})([^\'\"]{' . $kunena_config->trimlongurlsback . '})))\1(.*)>\3?\5<\/a>/u', '<a href="\2" \9>\6...\8</a>', $task->text );
		}

		if ($kunena_config->autoembedyoutube) {
			// convert youtube links to embedded player
			$task->text = preg_replace ( '/<a href=[^>]+\/\/(\w+\.youtube\.[^\/]+)\/watch\?[^>]*v=([^>"&\']+)[^>]+>[^<]+<\/a>/u', '<object width="425" height="344"><param name="movie" value="http://$1/v/$2&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><embed src="http://$1/v/$2&hl=en&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>', $task->text );
			// convert youtube playlists to embedded player
			$task->text = preg_replace ( '/<a href=[^>]+\/\/(\w+\.youtube\.[^\/]+)\/view_play_list\?[^>]*p=([^>"&]+)[^>]+>[^<]+<\/a>/u', '<object width="480" height="385"><param name="movie" value="http://$1/p/$2"></param><embed src="http://$1/p/$2" type="application/x-shockwave-flash" width="480" height="385"></embed></object>', $task->text );
		}

		if ($kunena_config->autoembedebay) {
			// convert ebay item to embedded widget
			$task->text = preg_replace ( '/<a href=[^>]+ebay.([^>\/]+)\/[^>]*QQitemZ([0-9]+)[^>]+>[^<]+<\/a>/u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350"></embed></object>', $task->text );
			$task->text = preg_replace ( '/<a href=[^>]+ebay.([^>\/]+)\/[^>]*ViewItem[^>"]+Item=([0-9]+)[^>]*>[^<]+<\/a>/u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=$2&campid=5336042350"></embed></object>', $task->text );

			// convert ebay search to embedded widget
			$task->text = preg_replace ( '/<a href=[^>]+ebay.([^>\/]+)\/[^>]*satitle=([^>&"]+)[^>]+>[^<]+<\/a>/u', '<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=search&query=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=search&query=$2&campid=5336042350"></embed></object>', $task->text );

			// convert seller listing to embedded widget
			$task->text = preg_replace ( '/<a href=[^>]+ebay.([^>\/]+)\/[^>]*QQsassZ([^>&"]+)[^>]*>[^<]+<\/a>/u', '<object width="355" height="355"><param name="movie" value="http://togo.ebay.$1/togo/seller.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&seller=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.$1/togo/&lang=' . $kunena_config->ebaylanguagecode . '&seller=$2&campid=5336042350"></embed></object>', $task->text );
		}
	}

	function Encode(&$text_new, &$task, $text_old, $context) {
		/*
		# Encode strings for output
		# Regard interpreter mode if needed
		# context: 'text'
		# context: 'tagremove'
		# RET:
		# TAGPARSER_RET_NOTHING: No Escaping done
		# TAGPARSER_RET_REPLACED: Escaping done
		*/

		// special states are liable for encoding (Extended Tag hit)

		if ($task->in_code) {
			// everything inside [code] is getting converted/encoded by tag delegation

			return TAGPARSER_RET_NOTHING;
		}
		if ($task->in_noparse) {
			// noparse is also needed to get encoded

			$text_new = kunena_htmlspecialchars ( $text_old, ENT_QUOTES );
			return TAGPARSER_RET_REPLACED;
		}
		// generally

		$text_new = $text_old;
		// pasting " " allows regexp to apply on \s at end



		// HTMLize from plaintext

		$text_new = KunenaParser::escape ( $text_new );
		if ($context == 'text' && ($task->autolink_disable == 0)) {
			// Build links HTML2HTML

			$text_new = KunenaBBCodeInterpreter::hyperlink ( $text_new );
			// Calculate smilies HTML2HTML

			$text_new = smile::smileParserCallback ( $text_new, $task->history, $task->emoticons, $task->iconList );
		}
		return TAGPARSER_RET_REPLACED;
	}

	function TagStandard(&$tns, &$tne, &$task, $tag) {
		// Function replaces TAGs with corresponding

		if ($task->in_code) {
			return TAGPARSER_RET_NOTHING;
		}
		if ($task->in_noparse) {
			// hits deactivated by default

			switch (JString::strtolower ( $tag->name )) {
				case 'noparse' :
					// specify noparse output - this only strips

					$tns = "";
					$tne = '';
					//reenter regular replacements

					$task->in_noparse = FALSE;
					return TAGPARSER_RET_REPLACED;
					break;
				default :
					break;
			}
			// tagname code is not processed

			return TAGPARSER_RET_NOTHING;
		}
		switch (JString::strtolower ( $tag->name )) {
			case 'b' :
				$tns = "<b>";
				$tne = '</b>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'i' :
				$tns = "<i>";
				$tne = '</i>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'u' :
				$tns = "<u>";
				$tne = '</u>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'strike' :
				$tns = "<strike>";
				$tne = '</strike>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'sub' :
				$tns = "<sub>";
				$tne = '</sub>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'sup' :
				$tns = "<sup>";
				$tne = '</sup>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'size' :
				if (! isset ( $tag->options ['default'] ) || strlen ( $tag->options ['default'] ) == 0) {
					return TAGPARSER_RET_NOTHING;
				}
				$size_css = array (1 => 'kmsgtext-xs', 'kmsgtext-s', 'kmsgtext-m', 'kmsgtext-l', 'kmsgtext-xl', 'kmsgtext-xxl' );
				if (isset ( $size_css [$tag->options ['default']] )) {
					$tns = '<span class="' . $size_css [$tag->options ['default']] . '">';
					$tne = '</span>';
					return TAGPARSER_RET_REPLACED;
				}
				$tns = "<span style='font-size:" . kunena_htmlspecialchars ( $tag->options ['default'], ENT_QUOTES ) . "'>";
				$tne = '</span>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'font' :
				if (! isset ( $tag->options ['default'] ) || empty ( $tag->options ['default'] ) ) {
					return TAGPARSER_RET_NOTHING;
				}
				$tns = '<span style="font-family:'.kunena_htmlspecialchars ( $tag->options ['default'] ).'">';
				$tne = '</span>';
				return TAGPARSER_RET_REPLACED;
				break;
				case 'li' :
				$tns = "<li>";
				$tne = '</li>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'pre' :
				$tns = "<pre>";
				$tne = '</pre>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'tt' :
				$tns = "<tt>";
				$tne = '</tt>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'color' :
				if (! isset ( $tag->options ['default'] ) || strlen ( $tag->options ['default'] ) == 0) {
					return TAGPARSER_RET_NOTHING;
				}
				$tns = "<span style='color: " . kunena_htmlspecialchars ( $tag->options ['default'], ENT_QUOTES ) . "'>";
				$tne = '</span>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'highlight' :
				$tns = "<span style='font-weight: 700;'>";
				$tne = '</span>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'left' :
				$tns = "<div style='text-align: left'>";
				$tne = '</div>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'center' :
				$tns = "<div style='text-align: center'>";
				$tne = '</div>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'right' :
				$tns = "<div style='text-align: right'>";
				$tne = '</div>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'indent' :
				$tns = "<blockquote>";
				$tne = '</blockquote>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'tr' :
				$tns = "<tr>";
				$tne = '</tr>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'th' :
				$tns = "<th>";
				$tne = '</th>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'td' :
				$tns = "<td>";
				$tne = '</td>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'email' :
				$task->autolink_disable --;
				if (isset ( $tag->options ['default'] )) {
					$tempstr = $tag->options ['default'];
					if (substr ( $tempstr, 0, 7 ) !== 'mailto:') {
						$tempstr = 'mailto:' . $tempstr;
					}
					$tns = "<a href='" . kunena_htmlspecialchars ( $tempstr, ENT_QUOTES ) . "'>";
					$tne = '</a>';
					return TAGPARSER_RET_REPLACED;
				}
				break;
			case 'url' :
				$task->autolink_disable --;
				// www. > http://www.

				if (isset ( $tag->options ['default'] )) {
					$tempstr = $tag->options ['default'];
					if (substr ( $tempstr, 0, 4 ) == 'www.') {
						$tempstr = 'http://' . $tempstr;
					}
					$tns = "<a href='" . kunena_htmlspecialchars ( $tempstr, ENT_QUOTES ) . "' rel=\"nofollow\" target=\"_blank\">";
					$tne = '</a>';
					return TAGPARSER_RET_REPLACED;
				}
				break;
			case 'glow' :
				//TODO: add support
				return TAGPARSER_RET_REPLACED;
			case 'shadow' :
				//TODO: add support
				return TAGPARSER_RET_REPLACED;
			case 'move' :
				//TODO: add support
				return TAGPARSER_RET_REPLACED;
			default :
				break;
		}
		return TAGPARSER_RET_NOTHING;
	}

	function TagExtended(&$tag_new, &$task, $tag, $between) {
		/*
		# Function replaces TAGs with corresponding
		# Encode was already been called for between
		*/
		$kunena_config = KunenaFactory::getConfig ();
		$kunena_my = &JFactory::getUser ();
		if ($task->in_code) {
			switch (JString::strtolower ( $tag->name )) {
				case 'code:1' : // fb ancient compatibility

				case 'code':
					$kunena_config = KunenaFactory::getConfig();
					if ($kunena_config->highlightcode) {
						if (substr(JVERSION, 0, 3) == 1.5) {
							$path = JPATH_ROOT.'/libraries/geshi';
							jimport('geshi.geshi');
						} else {
							$path = JPATH_ROOT.'/plugins/content/geshi/geshi';
							require_once($path.'/geshi.php');
						}
						if (file_exists($path.'/geshi.php')) {
							if (substr(JVERSION, 0, 3) == 1.5) $path .= '/geshi';
							$type = isset($tag->options["type"]) ? $tag->options["type"] : "php";
							if ($type == "js") $type = "javascript";
							else if ($type == "html") $type = "html4strict";
							if (!file_exists($path.DS.$type.".php"))
								$type = "php";
							$code = str_replace("\t", "	", $between);
							$geshi = new GeSHi($code, $type);
							//$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS,37);
							$geshi->enable_keyword_links(false);
							//$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);
							$code = $geshi->parse_code();
							$code = str_replace("\n","<br />",$code);
							//$code = ereg_replace(">([0-9]+)<br \/","><b>\\1.<\/b><br \/",$code);
							$tag_new = '<div class="highlight">'.$code.'</div>';
							$task->in_code = FALSE;
						}
						else
							return TAGPARSER_RET_NOTHING;
						return TAGPARSER_RET_REPLACED;
					} else {
						$types = array ("php", "mysql", "html", "js", "javascript" );
						if (! empty ( $tag->options ["type"] ) && in_array ( $tag->options ["type"], $types )) {
							 $t_type = $tag->options ["type"];
						} else {
							$t_type = "php";
						}
						// Preserve spaces and tabs in code
						$code = str_replace ( "\t", "__KTAB__", $between );
						$code = str_replace("\r\n","__KRN__",$code);
						$code = str_replace("\n","__KRN__",$code);
						$code = str_replace("\r","__KRN__",$code);
						$code = kunena_htmlspecialchars ( $code );
						$tag_new = "<div class=\"highlight\"><pre class=\"{$t_type}\">{$code}</pre></div>";
						$task->in_code = FALSE;
						return TAGPARSER_RET_REPLACED;
					}
					break;

				default :
					break;
			}
			return TAGPARSER_RET_NOTHING;
		}
		switch (JString::strtolower ( $tag->name )) {
			// in general $between was already Encoded (if not explicitly suppressed!)

			case 'ol' :
				// <br /> is not allowed inside <ol>
				$tag_new = "<ol>" . strtr($between, array("\r\n"=>' ', "\n"=>' ', "\r"=>' ')) . '</ol>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'ul' :
				// <br /> is not allowed inside <ul>
				$tag_new = "<ul>" . strtr($between, array("\r\n"=>' ', "\n"=>' ', "\r"=>' ')) . '</ul>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'table' :
				// <br /> is not allowed inside <table>
				$tag_new = "<table>" . strtr($between, array("\r\n"=>' ', "\n"=>' ', "\r"=>' ')) . '</table>';
				return TAGPARSER_RET_REPLACED;
				break;

			case 'email' :
				$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
				if (substr ( $tempstr, 0, 7 ) == 'mailto:') {
					$between = substr ( $tempstr, 7 );
				} else {
					$tempstr = 'mailto:' . $tempstr;
				}
				$tag_new = "<a href='" . $tempstr . "'>" . $between . '</a>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'url' :
				$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
				if (! preg_match ( "`^(https?://)`", $tempstr )) {
					$tempstr = 'http://' . $tempstr;
				}
				$tag_new = "<a href='" . $tempstr . "' rel=\"nofollow\" target=\"_blank\">" . $between . '</a>';
				return TAGPARSER_RET_REPLACED;
				break;
			case 'img' :
				if ($between) {
					if ($kunena_config->bbcode_img_secure != 'image') {
						static $file_ext = null;
						$matches = null;

						if (empty ( $file_ext )) {
							$params = &JComponentHelper::getParams ( 'com_media' );
							$file_ext = explode ( ',', $params->get ( 'upload_extensions' ) );
						}
						preg_match ( '/\.([\w\d]+)$/', $between, $matches );
						if (! isset ( $matches [1] ) || ! in_array ( JString::strtolower ( $matches [1] ), $file_ext )) {
							// if the image has not exentions return it like a link and if it's allowed in configuration
							if ($kunena_config->bbcode_img_secure == 'link') {
								$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
								if (! preg_match ( "`^(https?://)`", $tempstr )) {
									$tempstr = 'http://' . $tempstr;
								}

								$tag_new = "<a href='" . $tempstr . "' rel=\"nofollow\" target=\"_blank\">" . $between . '</a>';
								return TAGPARSER_RET_REPLACED;
							} else {
								$tag_new = $between;
								return TAGPARSER_RET_REPLACED;
							}
							break;
						}
					}

					$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
					if ($kunena_my->id == 0 && $kunena_config->showimgforguest == 0) {
						// Hide between content from non registered users

						$tag_new = '<b>' . JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG') . '</b>';
					} else {
						$task->autolink_disable --; // continue autolink conversion

						// Make sure we add image size if specified
						$imgtagsize = isset ( $tag->options ["size"] ) ? ( int ) kunena_htmlspecialchars ( $tag->options ["size"] ) : 0;

						// Need to check if we are nested inside a URL code
						if ($task->autolink_disable == 0) {
							// This part: <div style=\"table-layout:fixed; display:table;\"> ... </div> compliments of IE8
							$tag_new = '<a title="" rel="lightbox" href="'.$tempstr.'"><img src="'.$tempstr.'"'.($imgtagsize ? ' width="'.$imgtagsize.'"' : '').' alt="" /></a>';
						} else {
							// This part: <div style=\"table-layout:fixed; display:table;\"> ... </div> compliments of IE8
							$tag_new = "<img src='" . $tempstr . ($imgtagsize ? "' width='" . $imgtagsize : '') . "' alt='' />";
						}

					}
					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;
				break;
			case 'file' :
				if ($between) {
					$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
					if ($kunena_my->id == 0 && $kunena_config->showfileforguest == 0) {
						// Hide between content from non registered users

						$tag_new = '<b>' . JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE') . '</b>';
					} else {
						$task->autolink_disable --; // continue autolink conversion

						$tag_new = "<div class=\"kmsgattach\"><h4>" . JText::_('COM_KUNENA_FILEATTACH') . "</h4>" . JText::_('COM_KUNENA_FILENAME') . " <a href='" . $tempstr . "' target=\"_blank\" rel=\"nofollow\">" . kunena_htmlspecialchars ( !empty($tag->options ["name"]) ? $tag->options ["name"] : $tempstr) . "</a><br />" . JText::_('COM_KUNENA_FILESIZE') . ' ' . kunena_htmlspecialchars ( isset($tag->options ["size"]) ? $tag->options ["size"] : '?' ) . "</div>";
					}
					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
			case 'attachment':
				if (! is_object ( $this->parent ) && ! isset ( $this->parent->attachments )) {
					return TAGPARSER_RET_REPLACED;
				}
				$attachments = &$this->parent->attachments;
				$attachment = null;
				if (empty($between)) {
					$attachment = array_shift ( $attachments );
				} else if ((string) intval ( $between ) == $between) {
					$attid = intval ( $between );
					if (isset ( $attachments [$attid] )) {
						$attachment = $attachments [$attid];
						unset ( $attachments [$attid] );
					} else if (isset ( $this->parent->inline_attachments [$attid] )) {
						$attachment = $this->parent->inline_attachments [$attid];
					}
				} else {
					foreach ($attachments as $att) {
						if ($att->filename == $between) {
							$attachment = $att;
							unset ( $attachments [$att->id] );
							break;
						}
					}
					if (!$attachment && !empty($this->parent->inline_attachments)) {
						foreach ($this->parent->inline_attachments as $att) {
							if ($att->filename == $between) {
								$attachment = $att;
								break;
							}
						}
					}
				}

				$task->autolink_disable --; // continue autolink conversion
				if (is_object ( $attachment ) && !empty($attachment->disabled)) {
					// Hide between content from non registered users
					$tag_new = '<div class="kmsgattach">' . $attachment->textLink . '</div>';
				} else {
					if (is_object ( $attachment ) && is_file(JPATH_ROOT . "/{$attachment->folder}/{$attachment->filename}")) {
						$this->parent->inline_attachments[$attachment->id] = $attachment;
						$link = JURI::base () . "{$attachment->folder}/{$attachment->filename}";
						if (empty($attachment->imagelink)) {
							$tag_new = "<div class=\"kmsgattach\"><h4>" . JText::_ ( 'COM_KUNENA_FILEATTACH' ) . "</h4>" . JText::_ ( 'COM_KUNENA_FILENAME' ) . " <a href='" . $link . "' target=\"_blank\" rel=\"nofollow\">" . $attachment->filename . "</a><br />" . JText::_ ( 'COM_KUNENA_FILESIZE' ) . ' ' .number_format(intval($attachment->size)/1024,0,'',',').' KB'. "</div>";
						} else {
							$tag_new = "<div class=\"kmsgimage\">{$attachment->imagelink}</div>";
						}
					} else {
						$tag_new = '<div class="kmsgattach"><h4>' . JText::sprintf ( 'COM_KUNENA_ATTACHMENT_DELETED', kunena_htmlspecialchars ($between) ) . '</h4></div>';
					}
				}
				return TAGPARSER_RET_REPLACED;
				break;
			case 'quote' :
				$post = isset($tag->options["post"]) ? $tag->options["post"] : false;
				$user = isset($tag->options["default"]) ? $tag->options["default"] : false;
				$tag_new = '';
				if ($user) $tag_new .= "<b>" . $user . " " . JText::_ ( 'COM_KUNENA_POST_WROTE' ) . ":</b>\n";
				$tag_new .= '<div class="kmsgtext-quote">' . $between . '</div>';
				return TAGPARSER_RET_REPLACED;
				break;
//
// disable module bbcode
// TODO: make safe to use - prevent public from calling modules that are not allowed
//			case 'module' :
//				if ($between) {
//					$tempstr = kunena_htmlspecialchars ( $between, ENT_QUOTES );
//
//					if (JDocumentHTML::countModules ( $tempstr )) {
//						$document = &JFactory::getDocument ();
//						$renderer = $document->loadRenderer ( 'modules' );
//						$options = array ('style' => 'xhtml' );
//						$position = $tempstr;
//						$tag_new = $renderer->render ( $position, $options, null );
//					} else {
//						trigger_error ( 'Joomla module: ' . $tempstr . ' does not exist.', E_USER_NOTICE );
//					}
//
//					return TAGPARSER_RET_REPLACED;
//				}
//				return TAGPARSER_RET_NOTHING;
//
//				break;
			case 'article' :
				if ($between) {
					$articleid = (int)$between;

					// FIXME: works only in J1.5
					$query = 'SELECT a.*, u.name AS author, u.usertype, cc.title AS category, s.title AS section,
						s.published AS sec_pub, cc.published AS cat_pub, s.access AS sec_access, cc.access AS cat_access
						FROM #__content AS a
						LEFT JOIN #__categories AS cc ON cc.id = a.catid
						LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = "content"
						LEFT JOIN #__users AS u ON u.id = a.created_by
						WHERE a.id='.$articleid;
					$kunena_db = JFactory::getDBO();
					$kunena_db->setQuery($query);
					$article = $kunena_db->loadObject();

					$user	= & JFactory::getUser();

					// Are the section and category published?
					if ((!$article->cat_pub && $article->catid) || (!$article->sec_pub && $article->sectionid)) {
						$tag_new = JText::_("Article cannot be shown");
					} else if ((($article->cat_access > $user->get('aid', 0)) && $article->catid)
						|| (($article->sec_access > $user->get('aid', 0)) && $article->sectionid)
						|| ($article->access > $user->get('aid', 0))) {
						$tag_new = JText::_("This message contains an article, but you do not have permissions to see it.");
					} else {
						$kunena_app = JFactory::getApplication();
						$dispatcher	= JDispatcher::getInstance();
						$params = clone($kunena_app->getParams('com_content'));
						$aparams = new JParameter($article->attribs);
						$params->merge($aparams);

						$article->text = $article->introtext ."\n\n".$article->fulltext;
						JPluginHelper::importPlugin('content');
						$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, 0));
						$tag_new = $article->text;
					}

					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
			case 'list' :
				$type = isset($tag->options['type']) ? $tag->options['type'] : '';
				$type = ($type == 'decimal' ? 'ol' : 'ul');
				$tag_new = "<{$type}>";
				if (strstr($between,'[*]')) {
					$linearr = explode ( '[*]', $between );
					for($i = 0; $i < count ( $linearr ); $i ++) {
						$tmp = JString::trim ( $linearr [$i] );
						if (strlen ( $tmp )) {
							$tag_new .= '<li>' . JString::trim ( $linearr [$i] ) . '</li>';
						}
					}
				} else {
					$tag_new .= strtr($between, array("\r\n"=>' ', "\n"=>' ', "\r"=>' '));
				}
				$tag_new .= "</{$type}>";
				return TAGPARSER_RET_REPLACED;
				break;
			case 'video' :
				$task->autolink_disable --;
				if (! $between)
					return TAGPARSER_RET_NOTHING;

				// --- config start ------------

				$vid_minwidth = 425;
				$vid_minheight = 344; // min. display size

				//$vid_maxwidth = 640; $vid_maxheight = 480; // max. display size

				$vid_maxwidth = ( int ) (($kunena_config->rtewidth * 9) / 10); // Max 90% of text width
				$vid_maxheight = 480; // max. display size

				$vid_sizemax = 100; // max. display zoom in percent

				// --- config end --------------



				$vid ["type"] = (isset ( $tag->options ["type"] )) ? kunena_htmlspecialchars ( JString::strtolower ( $tag->options ["type"] ) ) : '';
				$vid ["param"] = (isset ( $tag->options ["param"] )) ? kunena_htmlspecialchars ( $tag->options ["param"] ) : '';

				if (! $vid ["type"]) {
					$vid_players = array ('divx' => 'divx', 'flash' => 'swf', 'mediaplayer' => 'avi,mp3,wma,wmv', 'quicktime' => 'mov,qt,qti,qtif,qtvr', 'realplayer', 'rm' );
					foreach ( $vid_players as $vid_player => $vid_exts )
						foreach ( explode ( ',', $vid_exts ) as $vid_ext )
							if (preg_match ( '/^(.*\.' . $vid_ext . ')$/i', $between ) > 0) {
								$vid ["type"] = $vid_player;
								break 2;
							}
					unset ( $vid_players );
				}
				if (! $vid ["type"]) {
					$vid_auto = (preg_match ( '/^http:\/\/.*?([^.]*)\.[^.]*(\/|$)/', $between, $vid_regs ) > 0);
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
				'flashvars' => array ('flash', 480, 360, 0, 0, $between, '', array (array (6, 'flashvars', $vid ["param"] ) ) ),
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
				'uume' => array ('flash', 400, 342, 0, 0, 'http://www.uume.com/v/%vcode%_UUME', '\/play_([\w-]*)', ''),
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
				'youku' => array ('flash', 480, 400, 0, 0, 'http://player.youku.com/player.php/sid/%vcode%/v.swf', '\/v_show\/id_c.00(.*)\.html', '' ),
				'youtube' => array ('flash', 425, 355, 0, 0, 'http://www.youtube.com/v/%vcode%&rel=1', '\/watch\?v=([\w\-]*)' , array (array (6, 'wmode', 'transparent' ) ) ),
				'_default' => array ($vid ["type"], 480, 360, 0, 25, $between, '', '' ) );
				list ( $vid_type, $vid_width, $vid_height, $vid_addx, $vid_addy, $vid_source, $vid_match, $vid_par2 ) = (isset ( $vid_providers [$vid ["type"]] )) ? $vid_providers [$vid ["type"]] : $vid_providers ["_default"];

				unset ( $vid_providers );
				if (! empty ( $vid_auto )) {
					if ($vid_match and (preg_match ( "/$vid_match/i", $between, $vid_regs ) > 0))
						$between = $vid_regs [1];
					else
						return TAGPARSER_RET_NOTHING;
				}
				$vid_source = preg_replace ( '/%vcode%/', $between, $vid_source );
				if (! is_array ( $vid_par2 ))
					$vid_par2 = array ();

				$vid_size = isset ( $tag->options ["size"] ) ? intval ( $tag->options ["size"] ) : 0;
				if (($vid_size > 0) and ($vid_size < $vid_sizemax)) {
					$vid_width = ( int ) ($vid_width * $vid_size / 100);
					$vid_height = ( int ) ($vid_height * $vid_size / 100);
				}
				$vid_width += $vid_addx;
				$vid_height += $vid_addy;
				if (! isset ( $tag->options ["size"] )) {
					if (isset ( $tag->options ["width"] ))
						if($tag->options ['width'] == '1') {
							$tag->options ['width'] = $vid_minwidth;
						}
						if ( isset($tag->options ["width"])) {
							$vid_width = intval ( $tag->options ["width"] );
						}
					if (isset ( $tag->options ["height"] ))
						if($tag->options ['height'] == '1') {
							$tag->options ['height'] = $vid_minheight;
						}
						if ( isset($tag->options ["height"])) {
							$vid_height = intval ( $tag->options ["height"] );
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
						return TAGPARSER_RET_NOTHING;
				}

				$vid_par3 = array ();
				foreach ( $tag->options as $vid_key => $vid_value ) {
					if (in_array ( JString::strtolower ( $vid_key ), $vid_allowpar ))
						array_push ( $vid_par3, array (6, $vid_key, kunena_htmlspecialchars ( $vid_value ) ) );
				}

				$vid_object = $vid_param = $vid_embed = array ();
				foreach ( array_merge ( $vid_par1, $vid_par2, $vid_par3 ) as $vid_data ) {
					list ( $vid_key, $vid_name, $vid_value ) = $vid_data;
					if ($vid_key & 1)
						$vid_object [$vid_name] = ' ' . $vid_name . '="' . preg_replace ( '/%vcode%/', $between, $vid_value ) . '"';
					if ($vid_key & 2)
						$vid_param [$vid_name] = '<param name="' . $vid_name . '" value="' . preg_replace ( '/%vcode%/', $between, $vid_value ) . '" />';
					if ($vid_key & 4)
						$vid_embed [$vid_name] = ' ' . $vid_name . '="' . preg_replace ( '/%vcode%/', $between, $vid_value ) . '"';
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
				return TAGPARSER_RET_REPLACED;
				break;
			case 'ebay' :
				if ($between) {
					$task->autolink_disable --; // continue autolink conversion

					$ebay_maxwidth = (int) (($kunena_config->rtewidth * 9) / 10); // Max 90% of text width
					$ebay_maxheight = (int) ($kunena_config->rteheight); // max. display size

					$tag_new = "";
					if (is_numeric ( $between )) {
						// Numeric: we have to assume this is an item id
						$tag_new .= '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=' . $between . '&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=normal&itemid=' . $between . '&campid=5336042350"></embed></object>';
					} else {
						// Non numeric: we have to assume this is a search
						$tag_new .= '<object width="'.$ebay_maxwidth.'" height="'.$ebay_maxheight.'"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=search&query=' . $between . '&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=' . $kunena_config->ebaylanguagecode . '&mode=search&query=' . $between . '&campid=5336042350"></embed></object>';
					}

					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
			case 'map' :
				if ($between) {
					$task->autolink_disable --;  // continue autolink conversion

					$map_maxwidth = (int) (($kunena_config->rtewidth * 9) / 10); // Max 90% of text width
					$map_maxheight = (int) ($kunena_config->rteheight); // max. display size

					$kmap = & KunenaGoogleMaps::getInstance ();
					$tag_new = $kmap->addMap($between);

					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
//			case 'tableau' :
//				if ($between) {
//					$task->autolink_disable --;  // continue autolink conversion
//
//					$map_maxwidth = (int) (($kunena_config->rtewidth * 9) / 10); // Max 90% of text width
//					$map_maxheight = (int) ($kunena_config->rteheight); // max. display size
//
//						$tag_new = '<a href="http://maps.google.com/?q='.$between.'" rel="nofollow" target="_blank">'.$between.'</a>';
//					$tag_new = '<script type="text/javascript" src="http://public.tableausoftware.com/javascripts/api/viz_v1.js"></script><object class="tableauViz" width="604" height="969" style="display:none;"><param name="name" value="WorldCup/WorldCupWinningPercentages" /><param name="toolbar" value="yes" /></object><noscript>World Cup Winning Percentages <br /><a href="#"><img alt="World Cup Winning Percentages " src="http://public.tableausoftware.com/static/images/WorldCup-WorldCupWinningPercentages_rss.png" height="100%" /></a></noscript><div style="width:604px;height:22px;padding:0px 10px 0px 0px; color:black;font:normal 8pt verdana,helvetica,arial,sans-serif;"><div style="float:right; padding-right:8px;"><a href="http://www.tableausoftware.com/public?ref=http://public.tableausoftware.com/views/WorldCup/WorldCupWinningPercentages" target="_blank">Powered by Tableau</a></div></div>';
//
// <script type="text/javascript" src="http://public.tableausoftware.com/javascripts/api/viz_v1.js"></script><object class="tableauViz" width="604" height="969" style="display:none;"><param name="name" value="WorldCup/WorldCupWinningPercentages" /><param name="toolbar" value="yes" /></object><noscript>World Cup Winning Percentages <br /><a href="#"><img alt="World Cup Winning Percentages " src="http://public.tableausoftware.com/static/images/WorldCup-WorldCupWinningPercentages_rss.png" height="100%" /></a></noscript><div style="width:604px;height:22px;padding:0px 10px 0px 0px; color:black;font:normal 8pt verdana,helvetica,arial,sans-serif;"><div style="float:right; padding-right:8px;"><a href="http://www.tableausoftware.com/public?ref=http://public.tableausoftware.com/views/WorldCup/WorldCupWinningPercentages" target="_blank">Powered by Tableau</a></div></div>
// http://public.tableausoftware.com/views/WorldCup/WorldCupWinningPercentages?:embed=y&:toolbar=yes
//
//
//					return TAGPARSER_RET_REPLACED;
//				}
//				return TAGPARSER_RET_NOTHING;
//
//				break;

			case 'hide' :
				if ($between) {
					if ($kunena_my->id == 0) {
						// Hide between content from non registered users
						$tag_new = JText::_('COM_KUNENA_BBCODE_HIDDENTEXT');
					} else {
						// Display but highlight the fact that it is hidden from guests
						$tag_new = '<b>' . JText::_('COM_KUNENA_BBCODE_HIDE') . '</b>' . '<div class="kmsgtext-hide">' . $between . '</div>';
					}
					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
			case 'confidential' :
				if ($between) {
					if (CKunenaTools::isModerator($kunena_my->id)) {
						// Display but highlight the fact that it is hidden from everyone except admins and mods
						$tag_new = '<b>' . JText::_('COM_KUNENA_BBCODE_CONFIDENTIAL_TEXT') . '</b><div class="kmsgtext-confidential">' . $between . '</div>';
					}
					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;
			case 'spoiler' :
				if ($between) {

					if ($this->spoilerid == 0) {
						// Only need the script for the first spoiler we find
						$kunena_app = & JFactory::getApplication ();
						$kunena_app->addCustomHeadTag ( '<script language = "JavaScript" type = "text/javascript">' . 'function kShowDetail(srcElement) {' . 'var targetID, srcElement, targetElement, imgElementID, imgElement;' . 'targetID = srcElement.id + "_details";' . 'imgElementID = srcElement.id + "_img";' . 'targetElement = document.getElementById(targetID);' . 'imgElement = document.getElementById(imgElementID);' . 'if (targetElement.style.display == "none") {' . 'targetElement.style.display = "";' . 'imgElement.src = "' . KUNENA_JLIVEURL . '/components/com_kunena/template/default/images/emoticons/w00t.png";' . '} else {' . 'targetElement.style.display = "none";' . 'imgElement.src = "' . KUNENA_JLIVEURL . '/components/com_kunena/template/default/images/emoticons/pinch.png";' . '}}	</script>' );
					}

					$this->spoilerid ++;

					$randomid = 'spoiler_'.rand ();

					$tag_new = '<div id="' . $randomid . '" onclick="javascript:kShowDetail(this);" class = "kspoiler" ><img id="' . $randomid . '_img"' . ' src="' . KUNENA_JLIVEURL . '/components/com_kunena/template/default/images/emoticons/pinch.png" border="0" alt=":pinch:" /> <strong>' . (isset ( $tag->options ["title"] ) ? ($tag->options ["title"]) : (JText::_('COM_KUNENA_BBCODE_SPOILER'))) . '</strong></div><div id="' . $randomid . '_details" style="display:none;"><span class="fb_quote">' . $between . '</span></div>';

					return TAGPARSER_RET_REPLACED;
				}
				return TAGPARSER_RET_NOTHING;

				break;

			default :
				break;
		}
		return TAGPARSER_RET_NOTHING;
	}

	function TagSingle(&$tag_new, &$task, $tag) {

		// Function replaces TAGs with corresponding

		// trace states (for parsing & encoding)

		if ($task->in_code) {
			return TAGPARSER_RET_NOTHING;
		}
		if ($task->in_noparse) {
			return TAGPARSER_RET_NOTHING;
		}
		switch (JString::strtolower ( $tag->name )) {
			case 'code:1' : // fb ancient compatibility

			case 'code' :
				$task->in_code = TRUE;
				return TAGPARSER_RET_NOTHING; // treat it as unprocessed (to push on stack)!

				break;
			case 'noparse' :
				$task->in_noparse = TRUE;
				return TAGPARSER_RET_NOTHING; // treat it as unprocessed!

				break;
			case 'email' :
			case 'url' :
			case 'img' :
			case 'file' :
			case 'article' :
			case 'attachment' :
			case 'map' :
			case 'video' :
			case 'ebay' :
				$task->autolink_disable ++; // stop autolink conversion

				return TAGPARSER_RET_NOTHING;
				break;
			case 'br' :
				$tag_new = "<br />";
				return TAGPARSER_RET_REPLACED; // nonrecursive

			// helper meta-replacement to get it rid from stack appearance

			// this is later on replaced again from TagExtended (if in [list])

			case '*' :
				$tag_new = "[*]";
				return TAGPARSER_RET_REPLACED; // nonrecursive

				break;
			case 'hr' :
				$tag_new = "<hr />";
				return TAGPARSER_RET_REPLACED; // nonrecursive

				break;
			default :
				break;
		}
		return TAGPARSER_RET_NOTHING;
	}

	function TagSingleLate(&$tag_new, &$task, $tag) {
		// Function replaces TAGs with corresponding

		if ($task->in_code) {
			return TAGPARSER_RET_NOTHING;
		}
		if ($task->in_noparse) {
			return TAGPARSER_RET_NOTHING;
		}
		switch (JString::strtolower ( $tag->name )) {
			// Replace unclosed img tag

			case 'img' :
				$task->autolink_disable --; // continue autolink conversion

				// kunena_htmlspecialchars($tag->options['default'], ENT_QUOTES)

				if (! isset ( $tag->options ['name'] ))
					break;
				$tag_new = "<img class='c_img' BORDER='0' src='" . kunena_htmlspecialchars ( $tag->options ['name'], ENT_QUOTES ) . "'";
				if (isset ( $tag->options ['width'] )) {
					$tag->options ['width'] = ( int ) $tag->options ['width'];
					$tag_new .= " width='" . $tag->options ['width'] . "'";
				}
				if (isset ( $tag->options ['height'] )) {
					$tag->options ['height'] = ( int ) $tag->options ['height'];
					$tag_new .= " height='" . $tag->options ['height'] . "'";
				}
				if (isset ( $tag->options ['left'] )) {
					$tag_new .= " align='left'";
				} else if (isset ( $tag->options ['right'] )) {
					$tag_new .= " align='right'";
				}
				$tag_new .= " border='0'";
				$tag_new .= ">";
				return TAGPARSER_RET_REPLACED;
				break;
			default :
				break;
		}
		return TAGPARSER_RET_NOTHING;
	}
}

class KunenaBBCodeParserTask extends BBCodeParserTask {
	// stateful task for parser runs
	// inside link used for autolinkdetection outside

	var $autolink_disable = 0;
	// ERROR autolinking don't work after wrong nested elements..
	// reason is internal state is wrong after dropping tags (where start occured stateful)
	// so we should trace this too :-S
	//emoticon things!

	var $history = 0; // 1=grey

	var $emoticons = 1; // true if to be replaced

	var $iconList = array (); // smilies

}

class KunenaBBCodeInterpreterPlain extends BBCodeInterpreter {
	// This class uses standardinterpreter, but removes all formatting outputs!

	// directly derivated from KunenaBBCodeInterpreter after extensive testing



	function Encode(&$text_new, &$task, $text_old, $context) {
		return TAGPARSER_RET_NOTHING;
	}

	function TagStandard(&$tns, &$tne, &$task, $tag) {
		$tns = '';
		$tne = '';
		return TAGPARSER_RET_NOTHING;
	}

	function TagExtended(&$tag_new, &$task, $tag, $between) {
		$tag_new = $between;
		return TAGPARSER_RET_NOTHING;
	}

	function TagSingle(&$tag_new, &$task, $tag) {
		$tag_new = '';
		return TAGPARSER_RET_NOTHING;
	}

	function TagSingleLate(&$tag_new, &$task, $tag) {
		$tag_new = '';
		return TAGPARSER_RET_NOTHING;
	}
}
?>