<?PHP
/**
* @version $Id: interpreter.bbcode.inc.php 1077 2008-10-20 19:01:15Z racoon $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/
############################################################################
# CATEGORY: Parser.TagParser                 DEVELOPMENT DATUM: 13.11.2007 #
# VERSION:  00.08.00                         LAST EDIT   DATUM: 12.12.2007 #
# FILENAME: interpreter.bbcode.inc.php                                     #
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

# parser interpreter task -interaction
# check interfaces & description (strip in implementation, extend interface decl)
# test execution
# update all tags to fb standard
## quote
## username
## call missingtag replacement on close (IMG)
# list implement
# errors

# ERROR
# implement self-link parsing (on Encode)
#
#
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

# parser states
define('BBCODE_PARSE_START',      'start');
define('BBCODE_PARSE_NAME',       'name');
define('BBCODE_PARSE_SPACE',      'space');
define('BBCODE_PARSE_KEY_OR_END', 'key_or_end');
define('BBCODE_PARSE_EQUAL',      'equal');
define('BBCODE_PARSE_VAL',        'val');
define('BBCODE_PARSE_VALQUOT',    'valquot');

// For backward compatibility with pre-PHP5
function fb_stripos($haystack , $needle , $offset=0) {
    if(function_exists('stripos')) { #PHP5
        return stripos($haystack, $needle, $offset);
    }
    else {
        // Nasty overhead but it does the trick
        return strpos(strtolower($haystack), strtolower($needle), $offset);
    }
}

class BBCodeInterpreter extends TagInterpreter {
    # Extension for TagParser to interprete BBCode (with noparse, code state)
    var $tag_start = '[';
    var $tag_end = ']';

    function &NewTask() {
        # Builds new Task
        # RET
        # object: the task object
        # TAGPARSER_RET_ERR
        $task = new BBCodeParserTask($this);
        return $task;
    }
    

    function ParseNext(&$task) {
        #  function ParseNext($text, &$pos_act) {
        # Parse next candidate for execution
        # Candidate could be cancelled later on!
        # $text:     the full text
        # &$pos_act: position to begin parsing
        # RET: TAGPARSER_RET_OK found, TAGPARSER_RET_ERR end
        $text =& $task->text;
        $pos_act =& $task->pos_act;
        // in_code state
        if($task->in_code) {
            // seek [/code] only
            // ERROR: code NOT caseinsensitive! Correct core logic!
            // throw this into interpreter part!
            $checkpos = fb_stripos($text, '[/code]', $pos_act);
            if($checkpos!==FALSE) {
                $pos_act = $checkpos;
                return TAGPARSER_RET_OK;
            }
            // temporarily close code tag if not exists
            $pos_act = strlen($text);
            $text .= '[/code]';
            return TAGPARSER_RET_OK;
            //return TAGPARSER_RET_ERR;
        }
        // in_noparse state
        if($task->in_noparse) {
            // seek [/noparse] only
            $checkpos = fb_stripos($text, '[/noparse]', $pos_act);
            // ERROR: noparse NOT caseinsensitive! Correct core logic!
            // throw this into interpreter part!
            if($checkpos!==FALSE) {
                $pos_act = $checkpos;
                return TAGPARSER_RET_OK;
            }
            return TAGPARSER_RET_ERR;
        }
        // parse tag_start in regular case
        // need text, pos_act, [length]
        $checkpos = fb_stripos($text, $this->tag_start, $pos_act);
        if($checkpos!==FALSE) {
            $pos_act = $checkpos;
            return TAGPARSER_RET_OK;
        }
        return TAGPARSER_RET_ERR;
    }

    function UnEscape(&$task) {
        # Check if current tag is escaped, unescape
        # RET: 0 continue,
        $text =& $task->text;
        $pos_act =& $task->pos_act;
        $nextchar = substr($text, $pos_act+1, 1);
        if($nextchar==$this->tag_start) {
            // escaped tagstart: remove escape!
            // abc[[def => pos_act@3, [@3, [@4 => abc[def
            if($task->dry) {
                // omit this tag
                $pos_act += 2;
                return TAGPARSER_RET_REPLACED;
            }
            $text = substr($text, 0, $pos_act).substr($text, $pos_act+1);
            $pos_act += 1;
            return TAGPARSER_RET_REPLACED;
        }
        // no escape
        return TAGPARSER_RET_NOTHING;
    }

    function ParseTag(&$tag, &$task) {
        # Parse Tag content and create construct
        # logs error itself to task
        # RET:
        # TAGPARSER_RET_OK continue
        # TAGPARSER_RET_ERR tag parse error, skip
        // this is a real char-by-char parser!
        $text =& $task->text;
        $pos =& $task->pos_act;
        $pos_start = $pos;
        $tagname = '';
        $nowkey = '';
        $nowval = '';
        $arr = array();
        $quot=''; // ' or "
        $isesc = FALSE; // escape \ used for quotes!
        $isend = FALSE; // TRUE if endtag
        $mode = BBCODE_PARSE_START;
        // scan through string
        #echo 'POS:'.$pos."\n";
        while(TRUE) {
            $pos++; // start with second char
            $char = substr($text, $pos, 1);
            #echo 'CHAR:'.$mode.':'.$char."\n";
            // missing tag end, overflow prevention!
            if($char===FALSE) {
                // cancel this tag
                $err = new ParserErrorContext('parser.err.tag.parsetag.endless');
                $err->GrabContext($task);
                $task->ErrorPush($err);
                unset($err); //opt
                $tag->tag_end = $pos+1;
                return TAGPARSER_RET_ERR;
            }
            // modes
            if($mode==BBCODE_PARSE_START) {
                // switch to name in any case
                $mode=BBCODE_PARSE_NAME;
                if($char=='/') {
                    $isend = TRUE;
                    continue;
                }
                // continue on name totally
            }
            if($mode==BBCODE_PARSE_NAME) {
                if($char==$this->tag_end
                || $char=='='
                || $char==' ') {
                    if($tagname=='') {
                        // cancel this nameless tag
                        $err = new ParserErrorContext('parser.err.tag.parsetag.noname');
                        $err->GrabContext($task);
                        $task->ErrorPush($err);
                        unset($err); //opt
                        $tag->tag_end = $pos+1;
                        return TAGPARSER_RET_ERR;
                    }
                }
                if($char==$this->tag_end) {
                    break;
                }
                if($char=='=') {
                    $mode = BBCODE_PARSE_EQUAL;
                    $nowkey .= 'default';
                    continue;
                }
                if($char==' ') {
                    $mode = BBCODE_PARSE_SPACE;
                    continue;
                }
                // build tagname
                $tagname .= strtolower($char);
                #echo 'TAG:'.$tagname."\n";
                continue;
            }
            if($mode==BBCODE_PARSE_SPACE) {
                if($char==' ') {
                    continue; // eat up spaces
                }
                if($char==$this->tag_end) {
                    break;
                }
                $nowkey .= strtolower($char);
                $mode=BBCODE_PARSE_KEY_OR_END;
                continue;
            }
            if($mode==BBCODE_PARSE_KEY_OR_END) {
                if($char=='=') {
                    $mode = BBCODE_PARSE_EQUAL;
                    continue;
                }
                if($char==$this->tag_end) {
                    $arr[$nowkey] = TRUE;
                    break;
                }
                if($char==' ') {
                    $arr[$nowkey] = TRUE;
                    $nowkey = '';
                    $mode = BBCODE_PARSE_SPACE;
                    continue;
                }
                $nowkey .= strtolower($char);
            }
            if($mode==BBCODE_PARSE_EQUAL) {
                $quot='';
                // quotescan "
                if($char=='"') {
                    $quot='"';
                    $mode = BBCODE_PARSE_VALQUOT;
                    continue;
                }
                // quotescan '
                if($char=='\'') {
                    $quot='\'';
                    $mode = BBCODE_PARSE_VALQUOT;
                    continue;
                }
                if($char==' ') {
                    $arr[$nowkey] = TRUE;
                    $nowkey = '';
                    $mode = BBCODE_PARSE_SPACE;
                    continue;
                }
                if($char==$this->tag_end) {
                    $arr[$nowkey] = TRUE;
                    break;
                }
                $nowval .= $char;
                $mode=BBCODE_PARSE_VAL;
                continue;
            }
            if($mode==BBCODE_PARSE_VALQUOT) {
                if($isesc) {
                    $nowval .= $char;
                    $isesc = FALSE;
                    continue;
                }
                if($char=='\\') {
                    // ONE backspace
                    #echo 'ESCAPE'."\n";
                    $isesc = TRUE;
                    continue;
                }
                if($char==$quot) {
                    $arr[$nowkey] = $nowval;
                    $nowkey = $nowval = '';
                    $mode = BBCODE_PARSE_SPACE;
                    continue;
                }
                $nowval .= $char;
                continue; //opt
            }
            if($mode==BBCODE_PARSE_VAL) {
                if($char==' ') {
                    $arr[$nowkey] = $nowval;
                    $nowkey = $nowval = '';
                    $mode = BBCODE_PARSE_SPACE;
                    continue;
                }
                if($char==$this->tag_end) {
                    $arr[$nowkey] = $nowval;
                    break;
                }
                $nowval .= $char;
                continue; //opt
            }
        }
        // end position points to tag closing
        #$pos_end = $pos;
        // create tag object
        if($isend) {
            // no reference!
            #echo 'TAGEND:'.$tagname.':'.$pos_start.":".$pos_end."\n";
            $tag = new ParserEventTagEnd($pos_start, $pos, $tagname);
        } else {
            // no reference!
            #echo 'TAG:'.$tagname.':'.$pos_start.":".$pos_end."\n";
            $tag = new ParserEventTag($pos_start, $pos, $tagname);
            $tag->setOptions($arr);
        }
        // parser position after tag: this is next char
        $pos++;

        return TAGPARSER_RET_OK;
    }

    function CheckTag(&$task, $tag) {
        # Parse Tag content and create construct
        # RET: 0 OK, errorinfo
        // check tagname regexp
        // $tag->name
        // dropping [*] here would lead to remove! so it goes to stack!
        /*
        $err = new ParserErrorContext('parser.err.tag.check');
        $err->GrabContext($task, $tag);
        $task->ErrorPush($err);
        */
        // optionally check tag
        return TAGPARSER_RET_OK;
    }
}

class BBCodeParserTask extends ParserTask {
    # stateful task for parser runs
    # parse state in_code in [code]
    var $in_code = FALSE;
    # parse state in_noparse in [noparse]
    var $in_noparse = FALSE;
}
?>