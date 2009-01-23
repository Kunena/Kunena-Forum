<?PHP
/**
* @version $Id: parser.inc.php 944 2008-08-10 20:11:08Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/
############################################################################
# CATEGORY: Parser.TagParser                 DEVELOPMENT DATUM: 13.11.2007 #
# VERSION:  00.08.00                         LAST EDIT   DATUM: 12.12.2007 #
# FILENAME: parser.inc.php                                                 #
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

# PARSER SPECS
#  Nonrecursive parsing
#  Linear analisys, onepass translation
#  Warranted encoding
#  Specific parsing rules implementable
#  Flexible event based user processing

# ERROR
#  RemoveOrEscape into interpreter?
#  parser reduce parse steps
#    change ParseNext, UnEscape, ParseTag, CheckTag
#  separate run instead task passing
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

define('TAGPARSER_RET_OK', 0);
define('TAGPARSER_RET_ERR', 1);

define('TAGPARSER_RET_NOTHING', 0);
define('TAGPARSER_RET_REPLACED', 1);
define('TAGPARSER_RET_RECURSIVE', 2);

# NO SUPPORT FOR RECURSIVE Standard & Extended REPARSING IMPLEMENTED
# IF NEEDED, CALL RECURSION INSIDE PARSER, HANDLE DOUBLE ENCODE YOURSELF

# PARSING (Tag-Support):
# [img noborder]
# [img='bildname']
# [img name='bla' x='10' y='20' html='']
# [img from='http://www.domain.ch/x.gif' x='10' y='20']
# [img name='bla' x='10' y='20']
# [img name="bla" x="10" y="20"]

# is_a >PHP4.2.0

$GLOBALS['microtime_total'] = 0;
$GLOBALS['microtime_prev'] = 0;
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    $newtime = ((float)$usec + (float)$sec);
    if($GLOBALS['microtime_prev']) {
        $GLOBALS['microtime_total'] += ($newtime-$GLOBALS['microtime_prev']);
        //echo 'T:'.$GLOBALS['microtime_total'].',d:'.($newtime-$GLOBALS['microtime_prev']);
        //echo ":<br>\n";
    }
    $GLOBALS['microtime_prev'] = $newtime;
}


class TagParser {
    # main parser class

    function TagParser() {
        # Constructor
    }

    function Parse(&$task) {
        # Parses Text for tag-based transformation of task text
        # remove=1 -> Remove Tags with illegal Content
        microtime_float();
        // fast access
        $interpreter =& $task->interpreter;
        $skip = $task->dry;
        $remove = $task->drop_errtag;
        // output text is input text
        $text =& $task->text;
        // internal state pass
        $pos_act =& $task->pos_act;
        $pos_act = 0;
        $pos_encode_last =& $task->pos_encode_last;
        $pos_encode_last = 0; // encode as soon as a matching tag is executed
        $st =& $task->st;
        $st = Array(); $sti = 0; // stackarr and TopPositionOfStack
        // scan for candidate of tag
        while(TRUE) {
            microtime_float();
            // next tag candidate
            if($interpreter->ParseNext($task)!==TAGPARSER_RET_OK) {
                break; // terminate event
            }
            // tag start detected - no further manipulation of inner functions except offset
            $tag_start = $pos_act;
            // verify escape, remove escape
            if($interpreter->UnEscape($task)==TAGPARSER_RET_REPLACED) {
                // was escaped, cancel tag, did unescape, continue after pos
                // no further linear encoding - later on
                continue; // terminate event
            }

            // parse UNescaped STARTORENDTAG-Start [ found
            $tag = NULL;
            if($interpreter->ParseTag($tag, $task)
            !==TAGPARSER_RET_OK) {
                # ERROR SEPARATE FROM UNSUPPORTED REMOVES
                $offset = 0;
                $this->RemoveOrEncode($offset, $task, $tag_start, $tag->tag_end, 'parsetag');
                $pos_act += $offset;
                unset($offset); // opt
                continue; // continue (if needed) after tag
            }
            $tag_end = $tag->tag_end;
            #echo 'TAG:'.$tag_start.":".$tag_end.":".$tag->name;
            #echo "\n";
            #var_dump($tag);
            #echo "\n";

            // verify tag validity content
            if($interpreter->CheckTag($task, $tag)
            !==TAGPARSER_RET_OK) {
                # ERROR SEPARATE FROM UNSUPPORTED REMOVES
                # we have found a syntactically correct tag, check semantics or remove
                # ERROR ENCODE WOULD BE WRONG!! (DOUBLE ENCODE LATER ON)
                $offset = 0;
                $this->RemoveOrEncode($offset, $task, $tag_start, $tag_end, 'checktag');
                $pos_act += $offset;
                unset($offset); // opt
                continue; // continue (if needed) after tag
            }

            // now realtag!
            // linear encode till current tag
            $encode_len = $tag_start-$pos_encode_last;
            $textnew = '';
            if(!$skip
            && ($task->interpreter->Encode($textnew, $task
            , substr($text, $pos_encode_last, $encode_len), 'text')
            !==TAGPARSER_RET_NOTHING)) {
                // Replaced
                $encode_diff = strlen($textnew)-$encode_len;
                $text = substr($text, 0, $pos_encode_last)
                .$textnew.substr($text, $tag_start);
                $tag->Offset($encode_diff);
                $tag_start += $encode_diff;
                $tag_end += $encode_diff;
                $pos_act += $encode_diff;
                unset($encode_diff); //opt
                #echo 'ENCODE:'.$pos_encode_last.":".$tag_start.":";
                #echo "\n";
            }
            unset($textnew); // opt
            $pos_encode_last = $tag_start;
            unset($encode_len); //opt

            // tag length
            $tag_len = $tag_end-$tag_start+1; // [<5x<6]<7   7-5=2
            // go tag events
            #echo 'CLASS:'.get_class($tag);
            #echo "\n";
            #if(get_class($tag)=='ParserEventTagEnd') {
            #if(strget_class($tag)=='ParserEventTagEnd') {
            if(is_a($tag, 'ParserEventTagEnd')) {
                // ENDTAG found
                $i=$sti-1;
                // seek tag on stack
                while($i>=0) {
                    $temp = $st[$i];
                    if($temp->name==$tag->name) {
                        // $i representing index of starting tag
                        break;
                    }
                    $i--;
                }
                unset($temp); // opt
                if($i==-1) {
                    // Tag not on Stack -> Ignore
                    // endtag without start -- illegal in any case - no event
                    $err = new ParserErrorContext('parser.err.tag.nostart');
                    $err->GrabContext($task, $tag);
                    $task->ErrorPush($err);
                    unset($err); //opt
                    if($remove) {
                        // remove tag, continue on prev tagstart
                        $text = substr($text, 0, $tag_start).substr($text, $tag_end+1);
                        $pos_act = $tag_start;
                    } else {
                        // tag wrong, linear encoding follows! continue parsing after
                        $pos_act = $tag_end+1;
                    }
                    // option would be to encode it as tagremove
                    continue;
                }
                // Tag on Stack at Pos $i -> reduce stack to $i IF needed!
                while($sti>($i+1)) {
                    // pop top
                    --$sti;
                    $starttag =& $st[$sti];
                    $starttag_len = $starttag->tag_end-$starttag->tag_start+1;
                    // late event
                    $tag_new = NULL;
                    if($interpreter->TagSingleLate($tag_new, $task, $starttag)
                    !==TAGPARSER_RET_NOTHING) {
                        if($skip) {
                            continue;
                        }
                        // tag replacement
                        $templen = strlen($tag_new)-$starttag_len;
                        $text = substr($text, 0, $starttag->tag_start)
                        .$tag_new.substr($text, $starttag->tag_end+1);
                        // marks are always behind tag!
                        $tag->Offset($templen);
                        $tag_start += $templen;
                        $tag_end += $templen;
                        $pos_act += $templen;
                        unset($templen); //opt
                        continue;
                    } else {
                        // bad tag on stack (open tag only)
                        $err = new ParserErrorContext('parser.err.tag.remain');
                        $err->GrabContext($task, $starttag);
                        $task->ErrorPush($err);
                        unset($err); //opt
                        $offset = 0;
                        $this->RemoveOrEncode($offset, $task, $starttag->tag_start, $starttag->tag_end, 'unsupported');
                        // was tag from stack - so all indices are behind
                        $tag->Offset($offset);
                        $tag_start += $offset;
                        $tag_end += $offset;
                        $pos_encode_last += $offset;
                        $pos_act += $offset;
                        unset($offset); //opt
                        continue; // continue (if needed) after tag
                    }
                    unset($starttag, $starttag_len); //opt
                }
                unset($i); //opt
                // Pop Top-Element (Actual)
                unset($st[$sti]); //opt
                $sti--;
                $starttag =& $st[$sti];
                // TRY STD-&EXT-TAG-REPLACEMENT OR KILL TAGS (START & END)
                $tag_new = $tag_new_start = $tag_new_end = NULL;
                if($task->interpreter->TagStandard($tag_new_start, $tag_new_end, $task
                , $starttag)
                !==TAGPARSER_RET_NOTHING) {
                    if($skip) {
                        continue;
                    }
                    // length in between tags
                    $midlen = $tag_start-$starttag->tag_end-1;
                    $text = substr($text, 0, $starttag->tag_start).$tag_new_start
                    .substr($text, $starttag->tag_end+1, $midlen)
                    .$tag_new_end.substr($text, $tag_end+1);
                    // To Starttag End
                    $totallen = strlen($tag_new_start)+$midlen+strlen($tag_new_end);
                    $pos_act = $starttag->tag_start+$totallen;
                    // linear encoding continue after
                    $pos_encode_last = $pos_act;
                    unset($midlen, $totallen); //opt
                } else if($task->interpreter->TagExtended($tag_new, $task, $starttag,
                substr($text, $starttag->tag_end+1, $tag_start-$starttag->tag_end-1))
                !==TAGPARSER_RET_NOTHING) {
                    if($skip) {
                        continue;
                    }
                    $text = substr($text, 0, $starttag->tag_start)
                    .$tag_new.substr($text, $tag_end+1);
                    $templen = strlen($tag_new);
                    // linear encoding continue after
                    $pos_encode_last = $pos_act = $starttag->tag_start+$templen;
                    unset($templen); //opt
                } else {
                    // UNSUPPORTED TAG
                    $err = new ParserErrorContext('parser.err.tag.unsupported');
                    $err->GrabContext($task, $starttag);
                    $task->ErrorPush($err);
                    unset($err); //opt
                    $offset_start = $offset_end = 0;
                    $this->RemoveOrEncode($offset_end, $task, $tag_start, $tag_end, 'unsupported');
                    $this->RemoveOrEncode($offset_start, $task, $starttag->tag_start, $starttag->tag_end, 'unsupported');
                    $tag->Offset($offset_end+$offset_start);
                    $pos_act += $offset_end+$offset_start;
                    $pos_encode_last = $pos_act;
                    unset($offset_end, $offset_start); //opt
                }
                unset($starttag); //opt
            } else {
                // STARTTAG FOUND
                $tag_new = NULL;
                $kind = $task->interpreter->TagSingle($tag_new, $task, $tag);
                if($kind!==TAGPARSER_RET_NOTHING) {
                    if($skip) {
                        continue;
                    }
                    $text = substr($text, 0, $tag_start).$tag_new.substr($text, $tag_end+1);
                    if($kind==TAGPARSER_RET_RECURSIVE) {
                        // recursive parsing possible, start from prev tagposition!
                        $pos_act = $tag_start;
                        $pos_encode_last = $pos_act;
                    } else {
                        $templen = strlen($tag_new);
                        // NONrecursive parsing
                        $pos_act = $tag_start+$templen; // parse continue after
                        $pos_encode_last = $pos_act; // linear encoding continue after
                    }
                } else {
                    // PUSH new ELEM
                    $st[$sti] = $tag; // not by ref -- store & forget
                    unset($tag);
                    $sti++;
                    $pos_act = $tag_end+1; // parse continue after
                    $pos_encode_last = $pos_act; // linear encoding continue after
                }
            }
        }
        // encode last linear part
        $textnew = '';
        if(!$skip
        && ($task->interpreter->Encode($textnew, $task, substr($text, $pos_encode_last), 'text')
        !==TAGPARSER_RET_NOTHING)) {
            $text = substr($text, 0, $pos_encode_last).$textnew;
        }
        unset($textnew); //opt
        // empty stack, stack should be empty
        while($sti>0) {
            // pop top
            --$sti;
            $starttag =& $st[$sti];
            $starttag_len = $starttag->tag_end-$starttag->tag_start+1;
            #var_dump($starttag);

            // late event
            $tag_new = NULL;
            if($interpreter->TagSingleLate($tag_new, $task, $starttag)
            !==TAGPARSER_RET_NOTHING) {
                if($skip) {
                    continue;
                }
                // tag replacement
                $text = substr($text, 0, $starttag->tag_start)
                .$tag_new.substr($text, $starttag->tag_end+1);
                // no more marks tag_start ... pos_act
            } else {
                // bad tag on stack
                $err = new ParserErrorContext('parser.err.tag.remain');
                $err->GrabContext($task, $starttag);
                $task->ErrorPush($err);
                unset($err); //opt
                $offset = 0;
                $this->RemoveOrEncode($offset, $task, $starttag->tag_start, $starttag->tag_end, 'unsupported');
                // no more marks tag_start ... pos_act
                unset($offset); //opt
            }
            unset($starttag, $starttag_len); //opt
        }
        microtime_float();

        $task->interpreter->PostProcessing($task);

        if(count($task->errarr)) {
            return TAGPARSER_RET_ERR;
        }

        return TAGPARSER_RET_OK;
    }

    function RemoveOrEncode(&$offset, &$task, $tag_start, $tag_end, $context) {
        # Remove or encode tag range
        #echo "ROE:".$tag_start.":".$tag_end.":".$task->text;
        #echo "\n";
        # SKIP handling on request
        if($task->dry) {
            return TAGPARSER_RET_OK;
        }
        $offset = 0;
        $text =& $task->text;
        $tag_len = $tag_end-$tag_start+1;
        if($task->drop_errtag) {
            // Remove
            #echo 'DROP';
            #echo "\n";
            $text = substr($text, 0, $tag_start)
            .substr($text, $tag_end+1);
            $offset = -$tag_len;
        } else {
            // encode tag with context
            $textnew = '';
            if($task->interpreter->Encode($textnew, $task
            , substr($text, $tag_start, $tag_len)
            , 'tagremove.'.$context)
            !==TAGPARSER_RET_NOTHING) {
                // Replaced
                $text = substr($text, 0, $tag_start)
                .$textnew.substr($text, $tag_end+1);
                $offset = strlen($textnew)-$tag_len;
            }
        }
        #echo "ROEE:".$task->text;
        #echo "\n";
        return TAGPARSER_RET_OK;
    }
}


class TagInterpreter {
    # class to define interface and describe event types

    var $parser = NULL;

    function TagInterpreter(&$parser) {
        # Interpreter constructor
        $this->parser =& $parser;
    }

    function &NewTask() {
        # Builds new Task
        # RET
        # object: the task object
        # TAGPARSER_RET_ERR: error creating
        $task = new ParserTask($this);
        return $task;
    }

    // All Parse functions need no parent call
    function ParseNext(&$task) {
        # Parse next candidate for execution
        # Candidate could be cancelled later on!
        # $text:     the full text
        # &$pos_act: position to begin parsing
        # RET:
        # TAGPARSER_RET_OK: found candidate
        # TAGPARSER_RET_ERR: end of parsing, latest occurence
        return TAGPARSER_RET_ERR;
    }

    function UnEscape(&$task) {
        # Check if current tag is escaped, unescape
        # Linear encoding is done later on!
        # This escape only prevents tag parser from early tag identification abort
        # RET:
        # TAGPARSER_RET_NOTHING: No Escaping done, continue
        # TAGPARSER_RET_REPLACED: Escaping done
        // Default: Say there are no escaped value - treat it as tag
        return TAGPARSER_RET_NOTHING;
    }

    function ParseTag(&$tag, &$task) {
        # Parse Tag content and create construct
        # Tag is getting omitted if TAGPARSER_RET_ERR (later encoded as text)
        # $pos_end MUST be >$task->tag_start on exit
        # RET:
        # TAGPARSER_RET_OK continue
        # TAGPARSER_RET_ERR tag parse error, skip
        return TAGPARSER_RET_ERR;
    }

    function CheckTag(&$task, $tag) {
        # Check tag content for conformity to drop wrong
        # RET:
        # TAGPARSER_RET_OK OK
        # TAGPARSER_RET_ERR with $err
        return TAGPARSER_RET_ERR;
    }

    // All String functions need consideration for parent calls!
    function Encode(&$text_new, &$task, $text_old, $context) {
        # Encode strings for output
        # Regard interpreter mode if needed
        # IN:  $text_old, $context
        #   As simple strings
        # OUT: $tag_new
        #   As full replacement
        # context: 'text'
        # context: 'tagremove.parsetag', 'tagremove.checktag', 'tagremove.unsupported'
        # RET:
        # TAGPARSER_RET_NOTHING: No Encoding done
        # TAGPARSER_RET_REPLACED: Encoding done
        return TAGPARSER_RET_NOTHING;
    }

    // All Tag functions need no parent calls!
    function TagSingle(&$tag_new, &$task, $tag) {
        # Funktion replaces TAGs with corresponding
        # IN:  $tag      As object
        # OUT: $tag_new  As full replacement
        # RET:
        # TAGPARSER_RET_NOTHING: None done
        # TAGPARSER_RET_REPLACED: done recursive
        # TAGPARSER_RET_RECURSIVE: done nonrecursive
        # NOTE: return 0 means later TagStandard on close event or TagSingleLate
        return TAGPARSER_RET_NOTHING;
    }

    function TagStandard(&$tag_new_start, &$tag_new_end, &$task, $tag) {
        # Function replaces TAGs with corresponding
        # IN:  $tag As object
        # OUT: $tag_new_start, $tag_new_end
        #   As tag replacement
        # RET:
        # TAGPARSER_RET_NOTHING: None done
        # TAGPARSER_RET_REPLACED: done recursive
        # TAGPARSER_RET_RECURSIVE: done nonrecursive (UNIMPLEMENTED)
        # NOTE: return 0 means TagExtended is checked
        return TAGPARSER_RET_NOTHING;
    }

    function TagExtended(&$tag_new, &$task, $tag, $between) {
        # Funktion replaces TAGs with corresponding
        # IN:  $tag, between   As object, between text
        # OUT: $tag_new        As full replacement
        # RET:
        # TAGPARSER_RET_NOTHING: None done
        # TAGPARSER_RET_REPLACED: done recursive
        # TAGPARSER_RET_RECURSIVE: done nonrecursive (UNIMPLEMENTED)
        # NOTE: TAGPARSER_RET_NOTHING means finally unsupported tag
        return TAGPARSER_RET_NOTHING;
    }

    function TagSingleLate(&$tag_new, &$task, $tag) {
        # Funktion replaces TAGs with corresponding
        # IN:  $tag      As object
        # OUT: $tag_new  As full replacement
        # RET:
        # TAGPARSER_RET_NOTHING: None done
        # TAGPARSER_RET_REPLACED: done recursive
        # TAGPARSER_RET_RECURSIVE: done nonrecursive
        # NOTE: return TAGPARSER_RET_NOTHING means unsupported
        return TAGPARSER_RET_NOTHING;
    }

    function PostProcessing(&$task)
    {
    	// Override if post processing is required...
    }

}

class ParserTask {
    # A specific Task to get parsed continuing parser states
    // object assoc
    var $interpreter = NULL;
    // persistent process data
    var $errarr = array();
    var $text = NULL;
    // config
    //  dry run, disables replacements
    var $dry = FALSE;
    //  drop errortag drops each tag producing errors
    var $drop_errtag = FALSE;
    // run specific
    // stack
    var $st = array();
    // scan states
    var $pos_act = 0;
    // encode as soon as a matching tag is executed
    var $pos_encode_last = 0;

    function ParserTask($interpreter) {
        # DO NOT CALL DIRECTLY:
        # use $interpreter->NewTask()
        $this->interpreter =& $interpreter;
    }

    function setText($text) {
        # define new text to parse
        $this->text = $text;
        return TAGPARSER_RET_OK;
    }

    function Reset() {
        # Reset this tasks' error state
        $this->errarr = array();
        return TAGPARSER_RET_OK;
    }

    function Parse($text=NULL) {
        # Parse this task
        if($text!==NULL) {
            $this->text = $text;
        }
        // call parser framework
        // ERROR REMOVE (, 1)??
        return $this->interpreter->parser->Parse($this);
    }

    function ErrorPush($err) {
        # Push err to tasks ErrorArray
        $this->errarr[] = $err;
    }

    function ErrorShow() {
        # Show all errors of this task
        reset($this->errarr);
        while(list($tempkey, $tempval) = each($this->errarr)) {
            // check interface
            echo $tempval->Show();
            echo '<br />';
            echo "\n";
        }
    }
}

class ParserRun {
    # UNUSED, should be inside Parse() instead of $task
    # A single run of the parser function
    # encapsulating the run information instead of the resulting persistent object
    var $task = null;
    // stack ERROR REALLY?
    var $st = array();
    // scan states
    var $pos_act = 0;
    // encode as soon as a matching tag is executed
    var $pos_encode_last = 0;

    function ParserRun($task) {
        $this->task = $task;
    }
}

class ParserEvent {
    # A ParserEvent happens on TagInterpreter::ParseTag
    var $tag_start = NULL;
    var $tag_end = NULL;
    var $name = '';

    function ParserEvent($tag_start, $tag_end, $name) {
        # Constructor
        $this->tag_start = $tag_start;
        $this->tag_end = $tag_end;
        $this->name = $name;
    }

    function Offset($offset) {
        # Move tag by offset (case replace prev tags)
        $this->tag_start += $offset;
        $this->tag_end += $offset;
    }
}

class ParserEventTag extends ParserEvent {
    # Representing a starttag with options
    # Derive from to put tags with special infos on stack
    var $options = array();

    function setOptions($opt) {
        // clone
        $this->options = $opt;
    }
}

class ParserEventTagEnd extends ParserEvent {
    # Representing an endtag
}

class ParserErrorContext {
    # A dump of a present TagParser::Parse error state with context
    var $error;
    var $pos = NULL;
    var $text = NULL;
    var $tag = NULL;

    function ParserErrorContext($error) {
        # Constructor
        $this->error = $error;
    }

    function GrabContext($task, $tag=NULL) {
        # Grab Context state of this error via COPY. This is a dump
        // keep in mind, pos is after encoding, not input related!
        $this->pos = $task->pos_act;
        // pos -errtext_neg +errtext_pos
        $this->text = substr($task->text, $task->pos_act-10, $task->pos_act+20);
        // snip bigger and store real pos?
        // tag
        if($tag!==NULL) {
            $this->tag = $tag;
            $this->pos = $tag->tag_start;
            $tag_len = $tag->tag_end-$tag->tag_start+1;
            $this->text = substr($task->text, $tag->tag_start, $tag_len);
            // snip bigger and store real pos?
        }
        // input counters instead of output counters?!
        // could also grab line number? (parse for in parser)
    }

    function Show() {
        # Show this error
        echo 'ERROR:'.$this->error.' @'.$this->pos.' near "'.$this->text.'"';
    }
}
?>