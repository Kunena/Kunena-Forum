<?PHP
/**
* @version $Id: interpreter.fireboard.inc.php 1076 2008-10-18 14:12:52Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/
############################################################################
# CATEGORY: Parser.TagParser                 DEVELOPMENT DATUM: 13.11.2007 #
# VERSION:  00.08.00                         LAST EDIT   DATUM: 12.12.2007 #
# FILENAME: interpreter.fireboard.inc.php                                  #
# AUTOR:    Miro Dietiker, MD Systems, All rights reserved                 #
# LICENSE:  http://www.gnu.org/copyleft/gpl.html GNU/GPL                   #
# CONTACT: m.dietiker@md-systems.ch        © 2007 Miro Dietiker 13.11.2007 #
############################################################################
# This parser is based on an earlier CMS parser implementation.
# It has been completely rewritten and generalized for FireBoard and
# was also heavily tested.
# However it should be: extensible, fast, ungreedy regarding resources
# stateful, enforcing strict output rules as defined
# Hope it works ;-)
############################################################################

# implement further extended links (username, ...)

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// full path does not work for some reason
// require_once ($mainframe->getCfg("absolute_path")Ê.Ê"/components/com_fireboard/sources/interpreter.bbcode.inc.php");
include_once("interpreter.bbcode.inc.php");

class FireBoardBBCodeInterpreter extends BBCodeInterpreter {
    # these are samples... we used the parser to refer to files!
    # did here a local caching, but using also database lookups - removed
    var $spoilerid = 0;

    function &NewTask() {
        # Builds new Task
        # RET
        # object: the task object
        # TAGPARSER_RET_ERR
        $task = new FireBoardBBCodeParserTask($this);
        return $task;
    }

	function hyperlink($text) {
	    $text = ' '.$text.' ';

		// match protocol://address:port/path/file.extension?some=variable&another=asf%
		// match protocol://address/path/file.extension?some=variable&another=asf%
	    // match www.something.domain:port/path/file.extension?some=variable&another=asf%
	    // match www.something.domain/path/file.extension?some=variable&another=asf%
	    $text = preg_replace('/(?<!S)((http(s?):\/\/)|(www.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/', '<a href="http$3://$4$5" target="_blank" rel="nofollow">$4$5</a>', $text);

	    // match name@address
	    $text = preg_replace('/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/', '<a href="mailto://$1">$1</a>', $text);

	    return substr($text, 1, -1);
	}

	function PostProcessing(&$task)
	{
		if ($GLOBALS["fbConfig"]->trimlongurls)
		{
		    // shorten URL text if they are too long (>65chars)
		    $task->text = preg_replace('/<a href=(\"|\')((http(s?):\/\/)?(([^\'\"]{'.$GLOBALS["fbConfig"]->trimlongurlsfront.'})([^\'\"]{4,})([^\'\"]{'.
		    							$GLOBALS["fbConfig"]->trimlongurlsback.'})))\1(.*)>\3?\5<\/a>/', '<a href="\2" \9>\6...\8</a>', $task->text);
		}

		if ($GLOBALS["fbConfig"]->autoembedyoutube)
		{
			// convert youtube links to embedded player
			$task->text = preg_replace('/<a href=[^>]+youtube.([^>\/]+)\/watch\?[^>]*v=([^>"&]+)[^>]+>[^<]+<\/a>/',
										'<object width="425" height="344"><param name="movie" value="http://www.youtube.$1/v/$2&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.$1/v/$2&hl=en&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>',
										$task->text);
			// convert youtube playlists to embedded player
			$task->text = preg_replace('/<a href=[^>]+youtube.([^>\/]+)\/view_play_list\?[^>]*p=([^>"&]+)[^>]+>[^<]+<\/a>/',
										'<object width="480" height="385"><param name="movie" value="http://www.youtube.$1/p/$2"></param><embed src="http://www.youtube.$1/p/$2" type="application/x-shockwave-flash" width="480" height="385"></embed></object>',
										$task->text);
		}

		if ($GLOBALS["fbConfig"]->autoembedebay)
		{
			// convert ebay item to embedded widget
			$task->text = preg_replace('/<a href=[^>]+ebay.([^>\/]+)\/[^>]*QQitemZ([0-9]+)[^>]+>[^<]+<\/a>/',
										'<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid=$2&campid=5336042350"></embed></object>',
										$task->text);
			$task->text = preg_replace('/<a href=[^>]+ebay.([^>\/]+)\/[^>]*ViewItem[^>"]+Item=([0-9]+)[^>]*>[^<]+<\/a>/',
										'<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid=$2&campid=5336042350"></embed></object>',
										$task->text);

			// convert ebay search to embedded widget
			$task->text = preg_replace('/<a href=[^>]+ebay.([^>\/]+)\/[^>]*satitle=([^>&"]+)[^>]+>[^<]+<\/a>/',
										'<object width="355" height="300"><param name="movie" value="http://togo.ebay.$1/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=search&query=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=search&query=$2&campid=5336042350"></embed></object>',
										$task->text);

			// convert seller listing to embedded widget
			$task->text = preg_replace('/<a href=[^>]+ebay.([^>\/]+)\/[^>]*QQsassZ([^>&"]+)[^>]*>[^<]+<\/a>/',
										'<object width="355" height="355"><param name="movie" value="http://togo.ebay.$1/togo/seller.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&seller=$2&campid=5336042350" /><embed src="http://togo.ebay.$1/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.$1/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&seller=$2&campid=5336042350"></embed></object>',
										$task->text);
		}
	}

    function Encode(&$text_new, &$task, $text_old, $context) {
        # Encode strings for output
        # Regard interpreter mode if needed
        # context: 'text'
        # context: 'tagremove'
        # RET:
        # TAGPARSER_RET_NOTHING: No Escaping done
        # TAGPARSER_RET_REPLACED: Escaping done
        // special states are liable for encoding (Extended Tag hit)
        if($task->in_code) {
            // everything inside [code] is getting converted/encoded by tag delegation
            return TAGPARSER_RET_NOTHING;
        }
        if($task->in_noparse) {
            // noparse is also needed to get encoded
            $text_new = htmlspecialchars($text_old, ENT_QUOTES);
            return TAGPARSER_RET_REPLACED;
        }
        // generally
        $text_new = $text_old;
        // pasting " " allows regexp to apply on \s at end

        // HTMLize from plaintext
        $text_new = htmlspecialchars($text_new, ENT_QUOTES);
        if($context=='text'
         && ($task->autolink_disable==0)) {
          // Build links HTML2HTML
          $text_new = FireBoardBBCodeInterpreter::hyperlink($text_new);
          // Calculate smilies HTML2HTML
          $text_new = smile::smileParserCallback($text_new, $task->history, $task->emoticons, $task->iconList);
	  }
        return TAGPARSER_RET_REPLACED;
    }

    function TagStandard(&$tns, &$tne, &$task, $tag) {
        # Function replaces TAGs with corresponding
        if($task->in_code) {
            return TAGPARSER_RET_NOTHING;
        }
        if($task->in_noparse) {
            // hits deactivated by default
            switch(strtolower($tag->name)) {
                case 'noparse':
                    // specify noparse output - this only strips
                    $tns = ""; $tne = '';
                    #reenter regular replacements
                    $task->in_noparse = FALSE;
                    return TAGPARSER_RET_REPLACED;
                    break;
                default:
                    break;
            }
            // tagname code is not processed
            return TAGPARSER_RET_NOTHING;
        }
        switch (strtolower($tag->name)) {
            case 'b':
                $tns = "<b>"; $tne = '</b>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'i':
                $tns = "<i>"; $tne = '</i>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'u':
                $tns = "<u>"; $tne = '</u>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'size':
                if(!isset($tag->options['default'])
                || strlen($tag->options['default'])==0) {
                    return TAGPARSER_RET_NOTHING;
                }
                $size_css = array(1 => 'fbxs', 'fbs', 'fbm', 'fbl', 'fbxl', 'fbxxl');
                if (isset($size_css[$tag->options['default']])) {
                    $tns = '<span class="'.$size_css[$tag->options['default']].'">'; $tne = '</span>';
                    return TAGPARSER_RET_REPLACED;
                }
                $tns = "<span style='font-size:".htmlspecialchars($tag->options['default'], ENT_QUOTES)."'>";
                $tne = '</span>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'ol':
                $tns = "<ol>"; $tne = '</ol>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'ul':
                $tns = "<ul>"; $tne = '</ul>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'li':
                $tns = "<li>"; $tne = '</li>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'color':
                if(!isset($tag->options['default'])
                || strlen($tag->options['default'])==0) {
                    return TAGPARSER_RET_NOTHING;
                }
                $tns = "<span style='color: ".htmlspecialchars($tag->options['default'], ENT_QUOTES)."'>"; $tne = '</span>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'highlight':
                $tns = "<span style='font-weight: 700;'>"; $tne = '</span>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'left':
                $tns = "<div style='text-align: left'>"; $tne = '</div>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'center':
                $tns = "<div style='text-align: center'>"; $tne = '</div>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'right':
                $tns = "<div style='text-align: right'>"; $tne = '</div>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'indent':
                $tns = "<blockquote>"; $tne = '</blockquote>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'email':
                $task->autolink_disable--;
                if(isset($tag->options['default'])) {
                    $tempstr = $tag->options['default'];
                    if(substr($tempstr, 0, 7)!=='mailto:') {
                      $tempstr = 'mailto:'.$tempstr;
                    }
                    $tns = "<a href='".htmlspecialchars($tempstr, ENT_QUOTES)."'>"; $tne = '</a>';
                    return TAGPARSER_RET_REPLACED;
                }
                break;
            case 'url':
                $task->autolink_disable--;
                // www. > http://www.
                if(isset($tag->options['default'])) {
                    $tempstr = $tag->options['default'];
                    if(substr($tempstr, 0, 4)=='www.') {
                      $tempstr = 'http://'.$tempstr;
                    }
                    $tns = "<a href='".htmlspecialchars($tempstr, ENT_QUOTES)."' rel=\"nofollow\" target=\"_blank\">"; $tne = '</a>';
                    return TAGPARSER_RET_REPLACED;
                }
                break;
             default:
                break;
        }
        return TAGPARSER_RET_NOTHING;
    }

    function TagExtended(&$tag_new, &$task, $tag, $between) {
        # Function replaces TAGs with corresponding
        # Encode was already been called for between
        global $my;
        if($task->in_code) {
            switch(strtolower($tag->name)) {
                case 'code:1': // fb ancient compatibility
                case 'code':

                    $types = array ("php", "mysql", "html", "js", "javascript");

                    $code_start_html = '<div class="fbcode"><table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr><td><b>'._FB_MSG_CODE.'</b></td></tr><tr><td><hr />';
                    if (in_array($tag->options["type"], $types)) {
                        $t_type = $tag->options["type"];
                    }
                    else {
                        $t_type = "php";
                    }

                    // make sure we show line breaks
                    $code_start_html .= "<code class=\"{$t_type}\">";
                    $code_end_html    = '</code><hr /></td></tr></table></div>';

					// Preserve spaces and tabs in code
                    $codetext = str_replace("\t", "__FBTAB__", $between);

                    $codetext = htmlspecialchars($codetext, ENT_QUOTES);
                    $codetext = str_replace(" ", "&nbsp;", $codetext);

                    $tag_new = $code_start_html. $codetext .$code_end_html;
                      #reenter regular replacements
                    $task->in_code = FALSE;
                    return TAGPARSER_RET_REPLACED;
                    break;

                default:
                    break;
            }
            return TAGPARSER_RET_NOTHING;
        }
        switch(strtolower($tag->name)) {
            # call htmlentities if Encode() did not already!!!
            # in general $between was already Encoded (if not explicitly suppressed!)
            case 'email':
                $tempstr = $between;
                if(substr($tempstr, 0, 7)=='mailto:') {
                  $between = substr($tempstr, 7);
                }
                else {
                  $tempstr = 'mailto:'.$tempstr;
                }
                $tag_new = "<a href='".$tempstr."'>".$between.'</a>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'url':
                $tempstr = $between;
                if(substr($tempstr, 0, 4)=='www.') {
                  $tempstr = 'http://'.$tempstr;
                }
                $tag_new = "<a href='".$tempstr."' rel=\"nofollow\" target=\"_blank\">".$between.'</a>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'img':
                if($between) {
                    $task->autolink_disable--; # continue autolink conversion
                    // Make sure we add image size if specified and while we are
                    // at it also set maximum image width from text width config.
                    //
                    // NOTICE: image max variables from config are not intended
                    // for formating but to limit the size of uploads, which can
                    // be larger than the available post area to support super-
                    // sized popups.
                    $imgmaxsize = (int)(($GLOBALS["fbConfig"]->rtewidth * 9) / 10); // 90% of text width
                    $imgtagsize = isset($tag->options["size"]) ? (int)htmlspecialchars($tag->options["size"]) : 0;

                    if($imgtagsize>0 && $imgtagsize<$imgmaxsize)
                    {
                    	$imgmaxsize = $imgtagsize;
                    }

                    $tag_new = "";
                    $tag_new .= "<img src='".$between.($imgtagsize ?"' width='".$imgmaxsize:'')."' style='max-width:".$imgmaxsize."px; ' />";

                    return TAGPARSER_RET_REPLACED;
                }
                return TAGPARSER_RET_NOTHING;
                break;
            case 'file':
                if($between) {
                    $task->autolink_disable--; # continue autolink conversion
                    $tag_new = "<div class=\"fb_file_attachment\"><span class=\"contentheading\">"._FB_FILEATTACH."</span><br>"._FB_FILENAME
                    ."<a href='".$between."' target=\"_blank\" rel=\"nofollow\">".(($tag->options["name"])?htmlspecialchars($tag->options["name"]):$between)."</a><br>"._FB_FILESIZE.htmlspecialchars($tag->options["size"], ENT_QUOTES)."</div>";
                    return TAGPARSER_RET_REPLACED;
                }
                return TAGPARSER_RET_NOTHING;

                break;
            case 'quote':
                $tag_new = '<span class="fb_quote">'.$between.'</span>';
                return TAGPARSER_RET_REPLACED;
                break;
            case 'list':
                $tag_new = '<ul>';
                $tag_new .= "\n";
                $linearr = explode('[*]', $between);
                for($i=0; $i<count($linearr); $i++) {
                    $tmp = trim($linearr[$i]);
                    if(strlen($tmp)) {
                        $tag_new .= '<li>'.trim($linearr[$i]).'</li>';
                        $tag_new .= "\n";
                    }
                }
                $tag_new .= '</ul>';
                $tag_new .= "\n";
                return TAGPARSER_RET_REPLACED;
                break;
			case 'video':
				$task->autolink_disable--;
				if (!$between) return TAGPARSER_RET_NOTHING;

				// --- config start ------------
				$vid_minwidth = 20; $vid_minheight = 20; // min. display size
				//$vid_maxwidth = 640; $vid_maxheight = 480; // max. display size
				$vid_maxwidth = (int)(($GLOBALS["fbConfig"]->rtewidth * 9) / 10); // Max 90% of text width
				$vid_maxheight = 480; // max. display size
				$vid_sizemax = 100; // max. display zoom in percent
				// --- config end --------------

				$vid["type"] = (isset($tag->options["type"]))?htmlspecialchars(strtolower($tag->options["type"])):'';
				$vid["param"] = (isset($tag->options["param"]))?htmlspecialchars($tag->options["param"]):'';

				if (!$vid["type"]) {
					$vid_players = array(
						'divx' => 'divx',
						'flash' => 'swf',
						'mediaplayer' => 'avi,mp3,wma,wmv',
						'quicktime' => 'mov,qt,qti,qtif,qtvr',
						'realplayer', 'rm'
					);
					foreach($vid_players as $vid_player => $vid_exts)
						foreach(explode(',', $vid_exts) as $vid_ext)
							if (preg_match('/^(.*\.'.$vid_ext.')$/i', $between) > 0) {
								$vid["type"] = $vid_player;
								break 2;
							}
					unset($vid_players);
				}
				if (!$vid["type"]) {
					if ($vid_auto = (preg_match('/^http:\/\/.*?([^.]*)\.[^.]*(\/|$)/', $between, $vid_regs) > 0)) {
						$vid["type"] = strtolower($vid_regs[1]);
						switch($vid["type"]) {
							case 'clip': $vid["type"] = 'clip.vn'; break;
							case 'web': $vid["type"] = 'web.de'; break;
							case 'wideo': $vid["type"] = 'wideo.fr'; break;
						}
					}
				}

				$vid_providers = array(
					'animeepisodes' => array ('flash', 428, 352, 0, 0, 'http://video.animeepisodes.net/vidiac.swf', '\/([\w\-]*).htm', array(array(6, 'flashvars', 'video=%vcode%'))),
					'biku' => array ('flash', 450, 364, 0, 0, 'http://www.biku.com/opus/player.swf?VideoID=%vcode%&embed=true&autoStart=false', '\/([\w\-]*).html'),
					'bofunk' => array ('flash', 446, 370, 0, 0, 'http://www.bofunk.com/e/%vcode%', ''),
					'break' => array ('flash', 464, 392, 0, 0, 'http://embed.break.com/%vcode%', ''),
					'clip.vn' => array ('flash', 448, 372, 0, 0, 'http://clip.vn/w/%vcode%,en,0', '\/watch\/([\w\-]*),vn'),
					'clipfish' => array ('flash', 464, 380, 0, 0, 'http://www.clipfish.de/videoplayer.swf?as=0&videoid=%vcode%&r=1&c=0067B3', 'videoid=([\w\-]*)'),
					'clipshack' => array ('flash', 430, 370, 0, 0, 'http://clipshack.com/player.swf?key=%vcode%', 'key=([\w\-]*)', array(array(6, 'wmode', 'transparent'))),
					'collegehumor' => array ('flash', 480, 360, 0, 0, 'http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=%vcode%&fullscreen=1', '\/video:(\d*)'),
					'current' => array ('flash', 400, 400, 0, 0, 'http://current.com/e/%vcode%', '\/items\/(\d*)', array(array(6, 'wmode', 'transparent'))),
					'dailymotion' => array ('flash', 420, 331, 0, 0, 'http://www.dailymotion.com/swf/%vcode%', '\/video\/([a-zA-Z0-9]*)'),
					'downloadfestival' => array ('flash', 450, 358, 0, 0, 'http://www.downloadfestival.tv/mofo/video/player/playerb003External.swf?rid=%vcode%', '\/watch\/([\d]*)'),
					'flashvars' => array ('flash', 480, 360, 0, 0, $between, '', array(array(6, 'flashvars', $vid["param"]))),
					'fliptrack' => array ('flash', 402, 302, 0, 0, 'http://www.fliptrack.com/v/%vcode%', '\/watch\/([\w\-]*)'),
					'fliqz' => array ('flash', 450, 392, 0, 0, 'http://content.fliqz.com/components/2d39cfef9385473c89939c2a5a7064f5.swf', 'vid=([\w]*)', array(
						array(6, 'flashvars', 'file=%vcode%&'), array(6, 'wmode', 'transparent'), array(6, 'bgcolor', '#000000'))),
					'gametrailers' => array ('flash', 480, 392, 0, 0, 'http://www.gametrailers.com/remote_wrap.php?mid=%vcode%', '\/(\d*).html'),
					'gamevideos' => array ('flash', 420, 405, 0, 0, 'http://www.gamevideos.com/swf/gamevideos11.swf?embedded=1&fullscreen=1&autoplay=0&src=http://www.gamevideos.com/video/videoListXML%3Fid%3D%vcode%%26adPlay%3Dfalse', '\/video\/id\/(\d*)', array(
						array(6, 'bgcolor', '#000000'), array(6, 'wmode', 'window'))),
					'glumbert' => array ('flash', 448, 336, 0, 0, 'http://www.glumbert.com/embed/%vcode%', '\/media\/([\w\-]*)', array(array(6, 'wmode', 'transparent'))),
					'gmx' => array ('flash', 425, 367, 0, 0, 'http://video.gmx.net/movie/%vcode%', '\/watch\/(\d*)'),
					'google' => array ('flash', 400, 326, 0, 0, 'http://video.google.com/googleplayer.swf?docId=%vcode%', 'docid=(\d*)'),
					'googlyfoogly' => array ('mediaplayer', 400, 300, 0, 25, 'http://media.googlyfoogly.com/images/videos/%vcode%.wmv', ''),
					'ifilm' => array ('flash', 448, 365, 0, 0, 'http://www.ifilm.com/efp', '\/video\/(\d*)', array(array(6, 'flashvars', 'flvbaseclip=%vcode%'))),
					'jumpcut' => array ('flash', 408, 324, 0, 0, 'http://jumpcut.com/media/flash/jump.swf?id=%vcode%&asset_type=movie&asset_id=%vcode%&eb=1', '\/\?id=([\w\-]*)'),
					'kewego' => array ('flash', 400, 368, 0, 0, 'http://www.kewego.com/p/en/%vcode%.html', '\/([\w\-]*)\.html', array(array(6, 'wmode', 'transparent'))),
					'liveleak' => array ('flash', 450, 370, 0, 0, 'http://www.liveleak.com/player.swf', '\/view\?i=([\w\-]*)', array(
						array(6, 'flashvars', 'autostart=false&token=%vcode%'), array(6, 'wmode', 'transparent'))),
					'livevideo' => array ('flash', 445, 369, 0, 0, 'http://www.livevideo.com/flvplayer/embed/%vcode%', ''),
					'megavideo' => array ('flash', 432, 351, 0, 0, 'http://www.megavideo.com/v/%vcode%..0', '', array(array(6, 'wmode', 'transparent'))),
					'metacafe' => array ('flash', 400, 345, 0, 0, 'http://www.metacafe.com/fplayer/%vcode%/.swf', '\/watch\/(\d*\/[\w\-]*)', array(array(6, 'wmode', 'transparent'))),
					'mofile' => array ('flash', 480, 395, 0, 0, 'http://tv.mofile.com/cn/xplayer.swf', '\.com\/([\w\-]*)', array(
						array(6, 'flashvars', 'v=%vcode%&autoplay=0&nowSkin=0_0'), array(6, 'wmode', 'transparent'))),
					'multiply' => array ('flash', 400, 350, 0, 0, 'http://images.multiply.com/multiply/multv.swf', '', array(
						array(6, 'flashvars', 'first_video_id=%vcode%&base_uri=multiply.com&is_owned=1'))),
					'myspace' => array ('flash', 430, 346, 0, 0, 'http://lads.myspace.com/videos/vplayer.swf', 'VideoID=(\d*)', array(array(6, 'flashvars', 'm=%vcode%&v=2&type=video'))),
					'myvideo' => array ('flash', 470, 406, 0, 0, 'http://www.myvideo.de/movie/%vcode%', '\/watch\/(\d*)'),
					'quxiu' => array ('flash', 437, 375, 0, 0, 'http://www.quxiu.com/photo/swf/swfobj.swf?id=%vcode%', '\/play_([\d_]*)\.htm', array(array(6, 'menu', 'false'))),
					'revver' => array ('flash', 480, 392, 0, 0, 'http://flash.revver.com/player/1.0/player.swf?mediaId=%vcode%', '\/video\/([\d_]*)'),
					'rutube' => array ('flash', 400, 353, 0, 0, 'http://video.rutube.ru/%vcode%', '\.html\?v=([\w]*)'),
					'sapo' => array ('flash', 400, 322, 0, 0, 'http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/%vcode%/mov/1', 'videos\.sapo\.pt\/([\w]*)', array(array(6, 'wmode', 'transparent'))),
					'sevenload' => array ('flash', 425, 350, 0, 0, 'http://sevenload.com/pl/%vcode%/425x350/swf', '\/videos\/([\w]*)', array(
						array(6, 'flashvars', 'apiHost=api.sevenload.com&showFullScreen=1'))),
					'sharkle' => array ('flash', 340, 310, 0, 0, 'http://sharkle.com/sharkle.swf?rnd=%vcode%&buffer=3', '', array(array(6, 'wmode', 'transparent'))),
					'spikedhumor' => array ('flash', 400, 345, 0, 0, 'http://www.spikedhumor.com/player/vcplayer.swf?file=http://www.spikedhumor.com/videocodes/%vcode%/data.xml&auto_play=false', '\/articles\/([\d]*)'),
					'stickam' => array ('flash', 400, 300, 0, 0, 'http://player.stickam.com/flashVarMediaPlayer/%vcode%', 'mId=([\d]*)'),
					'streetfire' => array ('flash', 428, 352, 0, 0, 'http://videos.streetfire.net/vidiac.swf', '\/([\w-]*).htm', array(array(6, 'flashvars', 'video=%vcode%'))),
					'stupidvideos' => array ('flash', 451, 433, 0, 0, 'http://img.purevideo.com/images/player/player.swf?sa=1&sk=5&si=2&i=%vcode%', '\/\?m=new#([\d_]*)'),
					'toufee' => array ('flash', 550, 270, 0, 0, 'http://toufee.com/movies/Movie.swf', 'u=[a-zA-Z]*(\d*)', array(array(6, 'flashvars', 'movieID=%vcode%&domainName=toufee'))),
					'tudou' => array ('flash', 400, 300, 0, 0, 'http://www.tudou.com/v/%vcode%', '\/view\/([\w-]*)', array(array(6, 'wmode', 'transparent'))),
					'unf-unf' => array ('flash', 425, 350, 0, 0, 'http://www.unf-unf.de/video/flvplayer.swf?file=http://www.unf-unf.de/video/clips/%vcode%.flv', '\/([\w-]*).html', array(array(6, 'wmode', 'transparent'))),
					'uume' => array ('flash', 400, 342, 0, 0, 'http://www.uume.com/v/%vcode%_UUME'), '\/play_([\w-]*)',
					'veoh' => array ('flash', 540, 438, 0, 0, 'http://www.veoh.com/videodetails2.swf?player=videodetailsembedded&type=v&permalinkId=%vcode%'), '\/videos\/([\w-]*)',
					'videoclipsdump' => array ('flash', 480, 400, 0, 0, 'http://www.videoclipsdump.com/player/simple.swf', '', array(
						array(6, 'flashvars', 'url=http://www.videoclipsdump.com/files/%vcode%.flv&autoplay=0&watermark=http://www.videoclipsdump.com/flv_watermark.php&buffer=10&full=0&siteurl=http://www.videoclipsdump.com&interval=10000&totalrotate=3'))),
					'videojug' => array ('flash', 400, 345, 0, 0, 'http://www.videojug.com/film/player?id=%vcode%', ''),
					'videotube' => array ('flash', 480, 400, 0, 0, 'http://www.videotube.de/flash/player.swf', '\/watch\/(\d*)', array(
						array(6, 'flashvars', 'baseURL=http://www.videotube.de/watch/%vcode%'), array(6, 'wmode', 'transparent'))),
					'vidiac' => array ('flash', 428, 352, 0, 0, 'http://www.vidiac.com/vidiac.swf', '\/([\w-]*).htm', array(array(6, 'flashvars', 'video=%vcode%'))),
					'vidilife' => array ('flash', 445, 369, 0, 0, 'http://www.vidiLife.com/flash/flvplayer.swf?autoStart=0&popup=1&video=http://www.vidiLife.com/media/flash_api.cfm?id=%vcode%&version=8', ''),
					'vimeo' => array ('flash', 400, 321, 0, 0, 'http://www.vimeo.com/moogaloop.swf?clip_id=%vcode%&server=www.vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=0&color=', '\.com\/(\d*)'),
					'wangyou' => array ('flash', 441, 384, 0, 0, 'http://v.wangyou.com/images/x_player.swf?id=%vcode%', '\/p(\d*).html', array(array(6, 'wmode', 'transparent'))),
					'web.de' => array ('flash', 425, 367, 0, 0, 'http://video.web.de/movie/%vcode%', '\/watch\/(\d*)'),
					'wideo.fr' => array ('flash', 400, 368, 0, 0, 'http://www.wideo.fr/p/fr/%vcode%.html', '\/([\w-]*).html', array(array(6, 'wmode', 'transparent'))),
					'youku' => array ('flash', 480, 400, 0, 0, 'http://player.youku.com/player.php/sid/%vcode%/v.swf', '\/v_show\/id_c.00(.*)\.html'),
					'youtube' => array ('flash', 425, 355, 0, 0, 'http://www.youtube.com/v/%vcode%&rel=1', '\/watch\?v=([\w\-]*)', array(array(6, 'wmode', 'transparent'))),
					'_default' => array ($vid["type"], 480, 360, 0, 25, $between, '')
				);
				list($vid_type, $vid_width, $vid_height, $vid_addx, $vid_addy, $vid_source, $vid_match, $vid_par2) =
					(isset($vid_providers[$vid["type"]]))?$vid_providers[$vid["type"]]:$vid_providers["_default"];
				unset($vid_providers);
				if ($vid_auto) {
					if ($vid_match and (preg_match("/$vid_match/i", $between, $vid_regs) > 0))
						$between = $vid_regs[1];
					else
						return TAGPARSER_RET_NOTHING;
				}
				$vid_source = preg_replace('/%vcode%/', $between, $vid_source);
				if (!is_array($vid_par2)) $vid_par2 = array();

				$vid_size = intval($tag->options["size"]);
				if (($vid_size > 0) and ($vid_size < $vid_sizemax)) {
					$vid_width = (int)($vid_width * $vid_size / 100);
					$vid_height = (int)($vid_height * $vid_size / 100);
				}
				$vid_width += $vid_addx; $vid_height += $vid_addy;
				if (!isset($tag->options["size"])) {
					if (isset($tag->options["width"])) $vid_width = intval($tag->options["width"]);
					if (isset($tag->options["height"])) $vid_height = intval($tag->options["height"]);
				}
				if ($vid_width < $vid_minwidth) $vid_width = $vid_minwidth;
				if ($vid_width > $vid_maxwidth) $vid_width = $vid_maxwidth;
				if ($vid_height < $vid_minheight) $vid_height = $vid_minheight;
				if ($vid_height > $vid_maxheight) $vid_height = $vid_maxheight;

				switch ($vid_type) {
					case 'divx':
						$vid_par1 = array(
							array(1, 'classid', 'clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616'),
							array(1, 'codebase', 'http://go.divx.com/plugin/DivXBrowserPlugin.cab'),
							array(4, 'type', 'video/divx'), array(4, 'pluginspage', 'http://go.divx.com/plugin/download/'),
							array(6, 'src', $vid_source), array(6, 'autoplay', 'false'),
							array(5, 'width', $vid_width), array(5, 'height', $vid_height));
						$vid_allowpar = array('previewimage');
						break;
					case 'flash':
						$vid_par1 = array(
							array(1, 'classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'),
							array(1, 'codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab'),
							array(2, 'movie', $vid_source),
							array(4, 'src', $vid_source), array(4, 'type', 'application/x-shockwave-flash'),
							array(4, 'pluginspage', 'http://www.macromedia.com/go/getflashplayer'),
							array(6, 'quality', 'high'), array(6, 'allowFullScreen', 'true'), array(6, 'allowScriptAccess', 'never'),
							array(5, 'width', $vid_width), array(5, 'height', $vid_height));
						$vid_allowpar = array('flashvars', 'wmode', 'bgcolor', 'quality');
						break;
					case 'mediaplayer':
						$vid_par1 = array(
							array(1, 'classid', 'clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95'),
							array(1, 'codebase', 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab'),
							array(4, 'type', 'application/x-mplayer2'), array(4, 'pluginspage', 'http://www.microsoft.com/Windows/MediaPlayer/'),
							array(6, 'src', $vid_source), array(6, 'autostart', 'false'), array(6, 'autosize', 'true'),
						  array(5, 'width', $vid_width), array(5, 'height', $vid_height));
						$vid_allowpar = array();
						break;
					case 'quicktime':
						$vid_par1 = array(
							array(1, 'classid', 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B'),
							array(1, 'codebase', 'http://www.apple.com/qtactivex/qtplugin.cab'),
							array(4, 'type', 'video/quicktime'), array(4, 'pluginspage', 'http://www.apple.com/quicktime/download/'),
							array(6, 'src', $vid_source), array(6, 'autoplay', 'false'), array(6, 'scale', 'aspect'),
						  array(5, 'width', $vid_width), array(5, 'height', $vid_height));
						$vid_allowpar = array();
						break;
					case 'realplayer':
						$vid_par1 = array(
							array(1, 'classid', 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'),
							array(4, 'type', 'audio/x-pn-realaudio-plugin'),
							array(6, 'src', $vid_source), array(6, 'autostart', 'false'), array(6, 'controls', 'ImageWindow,ControlPanel'),
							array(5, 'width', $vid_width), array(5, 'height', $vid_height));
						$vid_allowpar = array();
						break;
					default:
						return TAGPARSER_RET_NOTHING;
				}

				$vid_par3 = array();
				foreach($tag->options as $vid_key => $vid_value) {
					if (in_array(strtolower($vid_key), $vid_allowpar))
						array_push($vid_par3, array(6, $vid_key, htmlspecialchars($vid_value)));
				}

				$vid_object = $vid_param = $vid_embed = array();
				foreach(array_merge($vid_par1, $vid_par2, $vid_par3) as $vid_data) {
					list($vid_key, $vid_name, $vid_value) = $vid_data;
					if ($vid_key & 1) $vid_object[$vid_name] = ' '.$vid_name.'="'.preg_replace('/%vcode%/', $between, $vid_value).'"';
					if ($vid_key & 2) $vid_param[$vid_name] = '<param name="'.$vid_name.'" value="'.preg_replace('/%vcode%/', $between, $vid_value).'" />';
					if ($vid_key & 4) $vid_embed[$vid_name] = ' '.$vid_name.'="'.preg_replace('/%vcode%/', $between, $vid_value).'"';
				}

				$tag_new = '<object'; foreach($vid_object as $vid_data) $tag_new .= $vid_data; $tag_new .= '>';
				foreach($vid_param as $vid_data) $tag_new .= $vid_data;
				$tag_new .= '<embed'; foreach($vid_embed as $vid_data) $tag_new .= $vid_data; $tag_new .= ' /></object>';
				return TAGPARSER_RET_REPLACED;
				break;
            case 'ebay':
                if($between) {
                    $task->autolink_disable--; # continue autolink conversion

                    $tage_new = "";
                    if (is_numeric($between))
                    {
                    	// Numeric: we have to assume this is an item id
                    	$tag_new .= '<object width="355" height="300"><param name="movie" value="http://togo.ebay.com/togo/togo.swf" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid='.$between.'&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=normal&itemid='.$between.'&campid=5336042350"></embed></object>';
                    }
                    else
                    {
                    	// Non numeric: we have to assume this is a search
                    	$tag_new .= '<object width="355" height="300"><param name="movie" value="http://togo.ebay.com/togo/togo.swf?2008013100" /><param name="flashvars" value="base=http://togo.ebay.com/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=search&query='.$between.'&campid=5336042350" /><embed src="http://togo.ebay.com/togo/togo.swf?2008013100" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang='.$GLOBALS["fbConfig"]->ebaylanguagecode.'&mode=search&query='.$between.'&campid=5336042350"></embed></object>';
                    }

                    return TAGPARSER_RET_REPLACED;
                }
                return TAGPARSER_RET_NOTHING;

                break;
            case 'hide':
                if($between) {
                    if ($my->id==0)
                    {
                    	// Hide between content from non registered users
                    	$tag_new = '';
                    }
                    else
                    {
                    	// Display but highlight the fact that it is hidden from guests
                    	$tag_new = '<b>' . _FB_BBCODE_HIDE . '</b>' . '<span class="fb_quote">'.$between.'</span>';
                    }
                    return TAGPARSER_RET_REPLACED;
                }
                return TAGPARSER_RET_NOTHING;

                break;
            case 'spoiler':
                if($between) {

                    if ($this->spoilerid==0)
                    {
                    	// Only need the script for the first spoiler we find
	                    $tag_new = '<script language = "JavaScript" type = "text/javascript">'.
	                    			'function fb_showDetail(srcElement) {'.
										'var targetID, srcElement, targetElement, imgElementID, imgElement;'.
										'targetID = srcElement.id + "_details";'.
										'imgElementID = srcElement.id + "_img";'.
										'targetElement = document.getElementById(targetID);'.
										'imgElement = document.getElementById(imgElementID);'.
										'if (targetElement.style.display == "none") {'.
											'targetElement.style.display = "";'.
											'imgElement.src = "/components/com_fireboard/template/default/images/english/emoticons/w00t.png";'.
										'} else {'.
											'targetElement.style.display = "none";'.
											'imgElement.src = "/components/com_fireboard/template/default/images/english/emoticons/pinch.png";'.
										'}}	</script>';
                    }
                    else
                    {
                    	$tag_new = '';
                    }

                    $this->spoilerid++;

                    $randomid = rand();

                    $tag_new .= '<div id="'.$randomid.'" onClick="javascript:fb_showDetail(this);" style="cursor:pointer;"><img id="'.$randomid.'_img"'.
                    			'src="/components/com_fireboard/template/default/images/english/emoticons/pinch.png" border="0"> <strong>'.
                    			(isset($tag->options["title"]) ? ($tag->options["title"]) : (_FB_BBCODE_SPOILER))
                    			. '</strong></div><div id="'. $randomid . '_details" style="display:None;"><span class="fb_quote">' . $between . '</span></div>';

                    return TAGPARSER_RET_REPLACED;
                }
                return TAGPARSER_RET_NOTHING;

                break;

            default:
                break;
        }
        return TAGPARSER_RET_NOTHING;
    }

    function TagSingle(&$tag_new, &$task, $tag) {

        # Function replaces TAGs with corresponding
        // trace states (for parsing & encoding)
        if($task->in_code) {
            return TAGPARSER_RET_NOTHING;
        }
        if($task->in_noparse) {
            return TAGPARSER_RET_NOTHING;
        }
        switch (strtolower($tag->name)) {
            case 'code:1': // fb ancient compatibility
            case 'code':
                $task->in_code = TRUE;
                return TAGPARSER_RET_NOTHING; # treat it as unprocessed (to push on stack)!
                break;
            case 'noparse':
                $task->in_noparse = TRUE;
                return TAGPARSER_RET_NOTHING; # treat it as unprocessed!
                break;
            case 'email':
            case 'url':
            case 'img':
            case 'file':
            case 'video':
            case 'ebay':
            	$task->autolink_disable++; # stop autolink conversion
                return TAGPARSER_RET_NOTHING;
                break;
            case 'br':
                $tag_new = "<br />";
                return TAGPARSER_RET_REPLACED; // nonrecursive
                // helper meta-replacement to get it rid from stack appearance
                // this is later on replaced again from TagExtended (if in [list])
            case '*':
                $tag_new = "[*]";
                return TAGPARSER_RET_REPLACED; // nonrecursive
                break;
            default:
                break;
        }
        return TAGPARSER_RET_NOTHING;
    }

    function TagSingleLate(&$tag_new, &$task, $tag) {
        # Function replaces TAGs with corresponding
        if($task->in_code) {
            return TAGPARSER_RET_NOTHING;
        }
        if($task->in_noparse) {
            return TAGPARSER_RET_NOTHING;
        }
        switch (strtolower($tag->name)) {
            // Replace unclosed img tag
            case 'img':
                $task->autolink_disable--; # continue autolink conversion
                // htmlspecialchars($tag->options['default'], ENT_QUOTES)
                if(isset($tag->options['default'])) { $tag->options['name'] = $tag->options['default']; }
                $tag_new = "<img class='c_img' BORDER='0' src='".htmlspecialchars($tag->options['default'], ENT_QUOTES)."'";
                if(isset($tag->options['width'])) {
                    $tag->options['width'] = (int)$tag->options['width'];
                    $tag_new .= " width='".$tag->options['width']."'";
                }
                if(isset($tag->options['height'])) {
                    $tag->options['height'] = (int)$tag->options['height'];
                    $tag_new .= " height='".$tag->options['height']."'";
                }
                if(isset($tag->options['left'])) {
                    $tag_new .= " align='left'";
                } else if(isset($tag->options['right'])) {
                    $tag_new .= " align='right'";
                }
                $tag_new .= " border='0'";
                $tag_new .= ">";
                return TAGPARSER_RET_REPLACED;
                break;
            default:
                break;
        }
        return TAGPARSER_RET_NOTHING;
    }
}

class FireBoardBBCodeParserTask extends BBCodeParserTask {
    # stateful task for parser runs
    # inside link used for autolinkdetection outside
    var $autolink_disable = 0;
    // ERROR autolinking don't work after wrong nested elements..
    // reason is internal state is wrong after dropping tags (where start occured stateful)
    // so we should trace this too :-S
    //emoticon things!
    var $history = 0; // 1=grey
    var $emoticons = 1; // true if to be replaced
    var $iconList = array(); // smilies
}

class FireBoardBBCodeInterpreterPlain extends BBCodeInterpreter {
    # This class uses standardinterpreter, but removes all formatting outputs!
    # directly derivated from FireBoardBBCodeInterpreter after extensive testing

    function MyTagInterpreterSearch($references) {
        # Constructor
        MyTagInterpreter::MyTagInterpreter();

        # use params (references) to load your specific data, access to DB
    }

    function Encode(&$text_new, &$task, $text_old, $context) {
        return TAGPARSER_RET_NOTHING;
    }

    function TagStandard(&$tns, &$tne, &$task, $tag) {
        $tns = ''; $tne = '';
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
