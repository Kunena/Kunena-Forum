<?php
/**
* @version $Id: smile.class.php 1013 2008-08-15 06:46:43Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// ################################################################
// MOS Intruder Alerts
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ################################################################

include_once(JB_ABSSOURCESPATH."parser.inc.php");
include_once(JB_ABSSOURCESPATH."interpreter.fireboard.inc.php");

class smile
{

    function smileParserCallback($fb_message, $history, $emoticons, $iconList = null)
    {
        // from context HTML into HTML

        // where $history can be 1 or 0. If 1 then we need to load the grey
        // emoticons for the Topic History. If 0 we need the normal ones

        $type = ($history == 1) ? "-grey" : "";
        $message_emoticons = array();
        $message_emoticons = $iconList? $iconList : smile::getEmoticons($history);
        // now the text is parsed, next are the emoticons
	    $fb_message_txt = $fb_message;

        if ($emoticons != 1)
        {
            reset($message_emoticons);

            while (list($emo_txt, $emo_src) = each($message_emoticons)) {
                $fb_message_txt = str_replace($emo_txt, '<img src="' . $emo_src . '" alt="" style="vertical-align: middle;border:0px;" />', $fb_message_txt);
            }
        }

        return $fb_message_txt;
    }

    function smileReplace($fb_message, $history, $emoticons, $iconList = null)
    {

        $fb_message_txt = $fb_message;

        //implement the new parser
        $parser = new TagParser();
        $interpreter = new FireBoardBBCodeInterpreter($parser);
        $task = $interpreter->NewTask();
        $task->SetText($fb_message_txt);
        $task->dry = FALSE;
        $task->drop_errtag = FALSE;
	    $task->history = $history;
	    $task->emoticons = $emoticons;
	    $task->iconList = $iconList;
        $task->Parse();
	    // Show Parse errors for debug
	    //$task->ErrorShow();

        return $task->text;
    }
    /**
    * function to retrieve the emoticons out of the database
    *
    * @author Niels Vandekeybus <progster@wina.be>
    * @version 1.0
    * @since 2005-04-19
    * @param boolean $grayscale
    *            determines wether to return the grayscale or the ordinary emoticon
    * @param boolean  $emoticonbar
    *            only list emoticons to be displayed in the emoticonbar (currently unused)
    * @return array
    *             array consisting of emoticon codes and their respective location (NOT the entire img tag)
    */
    function getEmoticons($grayscale, $emoticonbar = 0)
    {
        global $database;
        $grayscale == 1 ? $column = "greylocation" : $column = "location";
        $sql = "SELECT `code` , `$column` FROM `#__fb_smileys`";

        if ($emoticonbar == 1)
        $sql .= " where `emoticonbar` = 1";

        $sql .= ";";
        $database->setQuery($sql);
        $smilies = $database->loadObjectList();
        	check_dberror("Unable to load smilies.");

        $smileyArray = array();
        foreach ($smilies as $smiley) {                                                    // We load all smileys in array, so we can sort them
            $smileyArray[$smiley->code] = '' . JB_URLEMOTIONSPATH . '' . $smiley->$column; // This makes sure that for example :pinch: gets translated before :p
        }

        if ($emoticonbar == 0)
        { // don't sort when it's only for use in the emoticonbar
            array_multisort(array_keys($smileyArray), SORT_DESC, $smileyArray);
            reset($smileyArray);
        }

        return $smileyArray;
    }

    function topicToolbar($selected, $tawidth)
    {
        //TO USE
        // $topicToolbar = smile:topicToolbar();
        // echo $topicToolbar;
        //$selected var is used to check the right selected icon
        //important for the edit function
        $selected = (int)$selected;
?>

        <table border = "0" cellspacing = "0" cellpadding = "0" class = "fb_flat">
            <tr>
                <td>
                    <input type = "radio" name = "topic_emoticon" value = "0"<?php echo $selected==0?" checked=\"checked\" ":"";?>/><?php echo _NO_SMILIE; ?>

            <input type = "radio" name = "topic_emoticon" value = "1"<?php echo $selected==1?" checked=\"checked\" ":"";?>/>

            <img src = "<?php echo JB_URLEMOTIONSPATH ;?>exclam.gif" alt = "" border = "0"/>

            <input type = "radio" name = "topic_emoticon" value = "2"<?php echo $selected==2?" checked=\"checked\" ":"";?>/>

            <img src = "<?php echo JB_URLEMOTIONSPATH ;?>question.gif" alt = "" border = "0"/>

            <input type = "radio" name = "topic_emoticon" value = "3"<?php echo $selected==3?" checked=\"checked\" ":"";?>/>

            <img src = "<?php echo JB_URLEMOTIONSPATH ;?>arrow.gif" alt = "" border = "0"/>

            <?php
            if ($tawidth <= 320) {
                echo '</tr><tr>';
            }
            ?>

                <input type = "radio" name = "topic_emoticon" value = "4"<?php echo $selected==4?" checked=\"checked\" ":"";?>/>

                <img src = "<?php echo JB_URLEMOTIONSPATH ;?>love.gif" alt = "" border = "0"/>

                <input type = "radio" name = "topic_emoticon" value = "5"<?php echo $selected==5?" checked=\"checked\" ":"";?>/>

                <img src = "<?php echo JB_URLEMOTIONSPATH ;?>grin.gif" alt = "" border = "0"/>

                <input type = "radio" name = "topic_emoticon" value = "6"<?php echo $selected==6?" checked=\"checked\" ":"";?>/>

                <img src = "<?php echo JB_URLEMOTIONSPATH ;?>shock.gif" alt = "" border = "0"/>

                <input type = "radio" name = "topic_emoticon" value = "7"<?php echo $selected==7?" checked=\"checked\" ":"";?>/>

                <img src = "<?php echo JB_URLEMOTIONSPATH ;?>smile.gif" alt = "" border = "0"/>
                </td>
            </tr>
        </table>

        <?php
    }

    /**
     * Strips all known HTML tags and replaces them with bbcode
     * Removes all unknown tags
     */
    function fbStripHtmlTags($text)
    {
        $fb_message_txt = $text;
        $fb_message_txt = preg_replace("/<p>/si", "", $fb_message_txt);
        $fb_message_txt = preg_replace("%</p>%si", "\n", $fb_message_txt);
        $fb_message_txt = preg_replace("/<br>/si", "\n", $fb_message_txt);
        $fb_message_txt = preg_replace("%<br />%si", "\n", $fb_message_txt);
        $fb_message_txt = preg_replace("%<br />%si", "\n", $fb_message_txt);
        $fb_message_txt = preg_replace("/&nbsp;/si", " ", $fb_message_txt);
        $fb_message_txt = preg_replace("/<OL>/si", "[ol]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</OL>%si", "[/ol]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<ul>/si", "[ul]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</ul>%si", "[/ul]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<LI>/si", "[li]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</LI>%si", "[/li]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<div class=\\\"fb_quote\\\">/si", "[quote]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</div>%si", "[/quote]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<b>/si", "[b]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</b>%si", "[/b]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<i>/si", "[i]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</i>%si", "[/i]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<u>/si", "[u]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</u>%si", "[/u]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<s>/si", "[s]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</s>%si", "[/s]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<strong>/si", "[b]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</strong>%si", "[/b]", $fb_message_txt);
        $fb_message_txt = preg_replace("/<em>/si", "[i]", $fb_message_txt);
        $fb_message_txt = preg_replace("%</em>%si", "[/i]", $fb_message_txt);

        //okay, now we've converted all HTML to known boardcode, nuke everything remaining itteratively:
        while ($fb_message_txt != strip_tags($fb_message_txt)) {
            $fb_message_txt = strip_tags($fb_message_txt);
        }

        return $fb_message_txt;
    } // fbStripHtmlTags()
    /**
     * This will convert all remaining HTML tags to innocent tags able to be displayed in full
     */
    function fbHtmlSafe($text)
    {
        $fb_message_txt = $text;
        $fb_message_txt = str_replace("<", "&lt;", $fb_message_txt);
        $fb_message_txt = str_replace(">", "&gt;", $fb_message_txt);
        return $fb_message_txt;
    } // fbHtmlSafe()
    /**
     * This function will write the TextArea
     */
    function fbWriteTextarea($areaname, $html, $width, $height, $useRte, $emoticons)
    {
        // well $html is the $message to edit, generally it means in PLAINTEXT @FireBoard!
        global $editmode;
        // ERROR: mixed global $editmode
        global $fbConfig;

        // (JJ) JOOMLA STYLE CHECK
        if ($fbConfig->joomlastyle < 1) {
            $boardclass = "fb_";
        }
        ?>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "fb_leftcolumn" valign = "top">
                <strong><a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=faq').'#boardcode';?>"><?php echo _COM_BOARDCODE; ?></a></strong>:
            </td>

            <td>
                <table border = "0" cellspacing = "0" cellpadding = "0" class = "fb-postbuttonset">
                    <tr>
                        <td class = "fb-postbuttons">
                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "b" name = "addbbcode0" value = " B " style = "font-weight:bold; " onclick = "bbstyle(0)" onmouseover = "helpline('b')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "i" name = "addbbcode2" value = " i " style = "font-style:italic; " onclick = "bbstyle(2)" onmouseover = "helpline('i')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "u" name = "addbbcode4" value = " u " style = "text-decoration: underline;" onclick = "bbstyle(4)" onmouseover = "helpline('u')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "q" name = "addbbcode6" value = "Quote" onclick = "bbstyle(6)" onmouseover = "helpline('q')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "c" name = "addbbcode8" value = "Code" onclick = "bbstyle(8)" onmouseover = "helpline('c')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "k" name = "addbbcode10" value = "ul" onclick = "bbstyle(10)" onmouseover = "helpline('k')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "o" name = "addbbcode12" value = "ol" onclick = "bbstyle(12)" onmouseover = "helpline('o')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "l" name = "addbbcode18" value = "li" onclick = "bbstyle(18)" onmouseover = "helpline('l')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "p" name = "addbbcode14" value = "Img" onclick = "bbstyle(14)" onmouseover = "helpline('p')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "w" name = "addbbcode16" value = "URL" style = "text-decoration: underline; " onclick = "bbstyle(16)" onmouseover = "helpline('w')"/>

                            <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "h" name = "addbbcode24" value = "Hide" onclick = "bbstyle(20)" onmouseover = "helpline('h')"/>

                            &nbsp;<?php echo _SMILE_COLOUR; ?>:

                    <select name = "addbbcode20"
                        onchange = "bbfontstyle('[color=' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/color]');" onmouseover = "helpline('s')" class = "<?php echo $boardclass;?>slcbox">
                        <option style = "color:black;   background-color: #FAFAFA" value = ""><?php echo _COLOUR_DEFAULT; ?></option>

                        <option style = "color:#FF0000; background-color: #FAFAFA" value = "#FF0000"><?php echo _COLOUR_RED; ?></option>

                        <option style = "color:#800080; background-color: #FAFAFA" value = "#800080"><?php echo _COLOUR_PURPLE; ?></option>

                        <option style = "color:#0000FF; background-color: #FAFAFA" value = "#0000FF"><?php echo _COLOUR_BLUE; ?></option>

                        <option style = "color:#008000; background-color: #FAFAFA" value = "#008000"><?php echo _COLOUR_GREEN; ?></option>

                        <option style = "color:#FFFF00; background-color: #FAFAFA" value = "#FFFF00"><?php echo _COLOUR_YELLOW; ?></option>

                        <option style = "color:#FF6600; background-color: #FAFAFA" value = "#FF6600"><?php echo _COLOUR_ORANGE; ?></option>

                        <option style = "color:#000080; background-color: #FAFAFA" value = "#000080"><?php echo _COLOUR_DARKBLUE; ?></option>

                        <option style = "color:#825900; background-color: #FAFAFA" value = "#825900"><?php echo _COLOUR_BROWN; ?></option>

                        <option style = "color:#9A9C02; background-color: #FAFAFA" value = "#9A9C02"><?php echo _COLOUR_GOLD; ?></option>

                        <option style = "color:#A7A7A7; background-color: #FAFAFA" value = "#A7A7A7"><?php echo _COLOUR_SILVER; ?></option>
                    </select>

                    &nbsp;<?php echo _SMILE_SIZE; ?>:

                    <select name = "addbbcode22" onchange = "bbfontstyle('[size=' + this.form.addbbcode22.options[this.form.addbbcode22.selectedIndex].value + ']', '[/size]')" onmouseover = "helpline('f')" class = "<?php echo $boardclass;?>button">
                        <option value = "1"><?php echo _SIZE_VSMALL; ?></option>

                        <option value = "2"><?php echo _SIZE_SMALL; ?></option>

                        <option value = "3" selected = "selected"><?php echo _SIZE_NORMAL; ?></option>

                        <option value = "4"><?php echo _SIZE_BIG; ?></option>

                        <option value = "5"><?php echo _SIZE_VBIG; ?></option>
                    </select>

					<?php if ($fbConfig->showspoilertag) {?>
                    <script type="text/javascript">
						function fb_spoiler_help() {document.postform.helpbox.value = 'Spoiler: [spoiler] ... [/spoiler]';}
					</script>
                    <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "s" name = "addbbcode40" value = "Spoiler" onclick = "bbfontstyle('[spoiler]', '[/spoiler]')" onmouseover = "fb_spoiler_help()"/>
					<?php } ?>

					<?php if ($fbConfig->showebaytag) {?>
                    <script type="text/javascript">
						function fb_ebay_help() {document.postform.helpbox.value = 'eBay: [ebay]ItemId[/ebay]';}
					</script>
                    <input type = "button" class = "<?php echo $boardclass;?>button" accesskey = "e" name = "addbbcode30" value = "eBay" onclick = "bbfontstyle('[ebay]', '[/ebay]')" onmouseover = "fb_ebay_help()"/>
					<?php } ?>

					<?php if ($fbConfig->showvideotag) {?>
                    &nbsp;<span style="white-space:nowrap;">
					<script type="text/javascript">
						function fb_vid_help1() {document.postform.helpbox.value = 'Video: [video type=provider size=100 width=480 height=360]xyz[/video]';}
						function fb_vid_help2() {document.postform.helpbox.value = 'Video: [video size=100 width=480 height=360]http://myvideodomain.com/myvideo[/video]';}
					</script>
					<a href = "javascript: bbfontstyle('[video]', '[/video]')" onmouseover = "fb_vid_help2()">Video:</a>
					<select name = "fb_vid_code1" onchange = "bbfontstyle('[video type=' + this.form.fb_vid_code1.options[this.form.fb_vid_code1.selectedIndex].value, '[/video]');" onmouseover = "fb_vid_help1()" class = "<?php echo $boardclass;?>button">
						<?php
						$vid_provider = array('','AnimeEpisodes','Biku','Bofunk','Break','Clip.vn','Clipfish','Clipshack','Collegehumor','Current',
							'DailyMotion','DivX,divx]http://','DownloadFestival','Flash,flash]http://','FlashVars,flashvars param=]http://','Fliptrack',
							'Fliqz','Gametrailers','Gamevideos','Glumbert','GMX','Google','GooglyFoogly','iFilm','Jumpcut','Kewego','LiveLeak','LiveVideo',
							'MediaPlayer,mediaplayer]http://','MegaVideo','Metacafe','Mofile','Multiply','MySpace','MyVideo','QuickTime,quicktime]http://','Quxiu',
							'RealPlayer,realplayer]http://','Revver','RuTube','Sapo','Sevenload','Sharkle','Spikedhumor','Stickam','Streetfire','StupidVideos','Toufee','Tudou',
							'Unf-Unf','Uume','Veoh','VideoclipsDump','Videojug','VideoTube','Vidiac','VidiLife','Vimeo','WangYou','WEB.DE','Wideo.fr','YouKu','YouTube');
						foreach($vid_provider as $vid_type) {
							list($vid_name, $vid_type) = explode(',', $vid_type);
							echo '<option value = "'.(($vid_type)?$vid_type:strtolower($vid_name).']').'">'.$vid_name.'</option>';
						}
						?>
					</select></span>
					<?php } ?>

                    &nbsp;&nbsp;<a href = "javascript: bbstyle(-1)"onmouseover = "helpline('a')"><small><?php echo _BBCODE_CLOSA; ?></small></a>
                        </td>
                    </tr>

                    <tr>
                        <td class = "<?php echo $boardclass;?>posthint">
                            <input type = "text" name = "helpbox" size = "45" class = "<?php echo $boardclass;?>inputbox" maxlength = "100" value = "<?php echo _BBCODE_HINT;?>"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
            <td valign = "top" class = "fb_leftcolumn">
                <strong><?php echo _MESSAGE; ?></strong>:

                <?php
                if ($emoticons != 1)
                {
                ?>

                    <br/>

                    <br/>

                    <div align = "right">
                        <table border = "0" cellspacing = "3" cellpadding = "0">
                            <tr>
                                <td colspan = "4" style = "text-align: center;">
                                    <strong><?php echo _GEN_EMOTICONS; ?></strong>
                                </td>
                            </tr>

                            <tr>
                                <?php
                                generate_smilies(); //the new function Smiley mod
                                ?>
                            </tr>
                        </table>
                    </div>

                <?php
                }
                ?>
            </td>

            <td valign = "top">
                <textarea class = "<?php echo $boardclass;?>txtarea" name = "<?php echo $areaname;?>" id = "<?php echo $areaname;?>"><?php echo htmlspecialchars($html, ENT_QUOTES); ?></textarea>
<?php
if ($editmode) {
    // Moderator edit area
     ?>
     <fieldset>
     <legend><?php echo _FB_EDITING_REASON?></legend>
        <input name="modified_reason" size="40" maxlength="200"  type="text"><br />

     </fieldset>
<?php
}
?>
            </td>
        </tr>

<?php
    } // fbWriteTextarea()

    function purify($text)
    {
        $text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
        $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        $text = preg_replace('/<!--.+?-->/', '', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = preg_replace('/&nbsp;/', ' ', $text);
        $text = preg_replace('/&amp;/', ' ', $text);
        $text = preg_replace('/&quot;/', ' ', $text);
        //smilies
        $text = preg_replace('/:laugh:/', ':-D', $text);
        $text = preg_replace('/:angry:/', ' ', $text);
        $text = preg_replace('/:mad:/', ' ', $text);
        $text = preg_replace('/:unsure:/', ' ', $text);
        $text = preg_replace('/:ohmy:/', ':-O', $text);
        $text = preg_replace('/:blink:/', ' ', $text);
        $text = preg_replace('/:huh:/', ' ', $text);
        $text = preg_replace('/:dry:/', ' ', $text);
        $text = preg_replace('/:lol:/', ':-))', $text);
        $text = preg_replace('/:money:/', ' ', $text);
        $text = preg_replace('/:rolleyes:/', ' ', $text);
        $text = preg_replace('/:woohoo:/', ' ', $text);
        $text = preg_replace('/:cheer:/', ' ', $text);
        $text = preg_replace('/:silly:/', ' ', $text);
        $text = preg_replace('/:blush:/', ' ', $text);
        $text = preg_replace('/:kiss:/', ' ', $text);
        $text = preg_replace('/:side:/', ' ', $text);
        $text = preg_replace('/:evil:/', ' ', $text);
        $text = preg_replace('/:whistle:/', ' ', $text);
        $text = preg_replace('/:pinch:/', ' ', $text);
        //bbcode
        $text = preg_replace('/\[hide==([1-3])\](.*?)\[\/hide\]/s', '', $text);
        $text = preg_replace('/(\[b\])/', ' ', $text);
        $text = preg_replace('/(\[\/b\])/', ' ', $text);
        $text = preg_replace('/(\[s\])/', ' ', $text);
        $text = preg_replace('/(\[\/s\])/', ' ', $text);
        $text = preg_replace('/(\[i\])/', ' ', $text);
        $text = preg_replace('/(\[\/i\])/', ' ', $text);
        $text = preg_replace('/(\[u\])/', ' ', $text);
        $text = preg_replace('/(\[\/u\])/', ' ', $text);
        $text = preg_replace('/(\[quote\])/', ' ', $text);
        $text = preg_replace('/(\[\/quote\])/', ' ', $text);
        $text = preg_replace('/(\[code:1\])(.*?)(\[\/code:1\])/', '\\2', $text);
        $text = preg_replace('/(\[ul\])(.*?)(\[\/ul\])/s', '\\2', $text);
        $text = preg_replace('/(\[li\])(.*?)(\[\/li\])/s', '\\2', $text);
        $text = preg_replace('/(\[ol\])(.*?)(\[\/ol\])/s', '\\2', $text);
        $text = preg_replace('/\[img size=([0-9][0-9][0-9])\](.*?)\[\/img\]/s', '\\2', $text);
        $text = preg_replace('/\[img size=([0-9][0-9])\](.*?)\[\/img\]/s', '\\2', $text);
        $text = preg_replace('/\[img\](.*?)\[\/img\]/s', '\\1', $text);
        $text = preg_replace('/\[url\](.*?)\[\/url\]/s', '\\1', $text);
        $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/s', '\\2 (\\1)', $text);
        $text = preg_replace('/<A (.*)>(.*)<\/A>/i', '\\2', $text);
        $text = preg_replace('/\[file(.*?)\](.*?)\[\/file\]/s', '\\2', $text);
        $text = preg_replace('/\[hide(.*?)\](.*?)\[\/hide\]/s', ' ', $text);
        $text = preg_replace('/\[spoiler(.*?)\](.*?)\[\/spoiler\]/s', ' ', $text);
        $text = preg_replace('/\[size=([1-7])\](.+?)\[\/size\]/s', '\\2', $text);
        $text = preg_replace('/\[color=(.*?)\](.*?)\[\/color\]/s', '\\2', $text);
        $text = preg_replace('/\[video\](.*?)\[\/video\]/s', '\\1', $text);
        $text = preg_replace('/\[ebay\](.*?)\[\/ebay\]/s', '\\1', $text);
        $text = preg_replace('#/n#s', ' ', $text);
        $text = strip_tags($text);
        //$text = stripslashes(htmlspecialchars($text));
        $text = stripslashes($text);
        return ($text);
    } //purify

    function urlMaker($text)
    {
        $text = str_replace("\n", " \n ", $text);
        $words = explode(' ', $text);

        for ($i = 0; $i < sizeof($words); $i++)
        {
            $word = $words[$i];
            //Trim below is necessary is the tag is placed at the begin of string
            $c = 0;

            if (strtolower(substr($words[$i], 0, 7)) == 'http://')
            {
                $c = 1;
                $word = '<a href=\"' . $words[$i] . '\" target=\"_new\">' . $word . '</a>';
            }
            elseif (strtolower(substr($words[$i], 0, 8)) == 'https://')
            {
                $c = 1;
                $word = '<a href=\"' . $words[$i] . '\" target=\"_new\">' . $word . '</a>';
            }
            elseif (strtolower(substr($words[$i], 0, 6)) == 'ftp://')
            {
                $c = 1;
                $word = '<a href=\"' . $words[$i] . '\" target=\"_new\">' . $word . '</a>';
            }
            elseif (strtolower(substr($words[$i], 0, 4)) == 'ftp.')
            {
                $c = 1;
                $word = '<a href=\"ftp://' . $words[$i] . '\" target=\"_new\">' . $word . '</a>';
            }
            elseif (strtolower(substr($words[$i], 0, 4)) == 'www.')
            {
                $c = 1;
                $word = '<a href="http://' . $words[$i] . '\" target=\"_new\">' . $word . '</a>';
            }
            elseif (strtolower(substr($words[$i], 0, 7)) == 'mailto:')
            {
                $c = 1;
                $word = '<a href=\"' . $words[$i] . '\">' . $word . '</a>';
            }

            if ($c == 1)
            $words[$i] = $word;
            //$words[$i] = str_replace ("\n ", "\n", $words[$i]);
        }

        $ret = str_replace(" \n ", "\n", implode(' ', $words));
        return $ret;
    }
    /* **************************************************************
	* htmlwrap() function - v1.7
	* Copyright (c) 2004-2008 Brian Huisman AKA GreyWyvern
	*
	* This program may be distributed under the terms of the GPL
	*   - http://www.gnu.org/licenses/gpl.txt
	*
	*
	* htmlwrap -- Safely wraps a string containing HTML formatted text (not
	* a full HTML document) to a specified width
	*
	*
	* Requirements
	*   htmlwrap() requires a version of PHP later than 4.1.0 on *nix or
	* 4.2.3 on win32.
	*
	*
	* Changelog
	* 1.7  - Fix for buggy handling of \S with PCRE_UTF8 modifier
	*         - Reported by marj
	*
	* 1.6  - Fix for endless loop bug on certain special characters
	*         - Reported by Jamie Jones & Steve
	*
	* 1.5  - Tags no longer bulk converted to lowercase
	*         - Fixes a bug reported by Dave
	*
	* 1.4  - Made nobreak algorithm more robust
	*         - Fixes a bug reported by Jonathan Wage
	*
	* 1.3  - Added automatic UTF-8 encoding detection
	*      - Fixed case where HTML entities were not counted correctly
	*      - Some regexp speed tweaks
	*
	* 1.2  - Removed nl2br feature; script now *just* wraps HTML
	*
	* 1.1  - Now optionally works with UTF-8 multi-byte characters
	*
	*
	* Description
	*
	* string htmlwrap ( string str [, int width [, string break [, string nobreak]]])
	*
	* htmlwrap() is a function which wraps HTML by breaking long words and
	* preventing them from damaging your layout.  This function will NOT
	* insert <br /> tags every "width" characters as in the PHP wordwrap()
	* function.  HTML wraps automatically, so this function only ensures
	* wrapping at "width" characters is possible.  Use in places where a
	* page will accept user input in order to create HTML output like in
	* forums or blog comments.
	*
	* htmlwrap() won't break text within HTML tags and also preserves any
	* existing HTML entities within the string, like &nbsp; and &lt;  It
	* will only count these entities as one character.
	*
	* The function also allows you to specify "protected" elements, where
	* line-breaks are not inserted.  This is useful for elements like <pre>
	* if you don't want the code to be damaged by insertion of newlines.
	* Add the names of the elements you wish to protect from line-breaks as
	* as a space separate list to the nobreak argument.  Only names of
	* valid HTML tags are accepted.  (eg. "code pre blockquote")
	*
	* htmlwrap() will *always* break long strings of characters at the
	* specified width.  In this way, the function behaves as if the
	* wordwrap() "cut" flag is always set.  However, the function will try
	* to find "safe" characters within strings it breaks, where inserting a
	* line-break would make more sense.  You may edit these characters by
	* adding or removing them from the $lbrks variable.
	*
	* htmlwrap() is safe to use on strings containing UTF-8 multi-byte
	* characters.
	*
	* See the inline comments and http://www.greywyvern.com/php.php
	* for more info
	******************************************************************** */

	function htmlwrap($str, $width = 60, $break = "\n", $nobreak = "") {

	  // Split HTML content into an array delimited by < and >
	  // The flags save the delimeters and remove empty variables
	  $content = preg_split("/([<>])/", $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	  // Transform protected element lists into arrays
	  $nobreak = explode(" ", strtolower($nobreak));

	  // Variable setup
	  $intag = false;
	  $innbk = array();
	  $drain = "";

	  // List of characters it is "safe" to insert line-breaks at
	  // It is not necessary to add < and > as they are automatically implied
	  $lbrks = "/?!%)-}]\\\"':;&";

	  // Is $str a UTF8 string?
//	  $utf8 = (preg_match("/^([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$/", $str)) ? "u" : "";
	  // original utf8 problems seems to cause problems with very long text (forumposts)
	  // replaced by a little simpler function call by fxstein 8-13-08
	  $utf8 = (mb_detect_encoding($str . 'a' , 'UTF-8') == 'UTF-8') ? "u" : "";

	  while (list(, $value) = each($content)) {
	    switch ($value) {

	      // If a < is encountered, set the "in-tag" flag
	      case "<": $intag = true; break;

	      // If a > is encountered, remove the flag
	      case ">": $intag = false; break;

	      default:

	        // If we are currently within a tag...
	        if ($intag) {

	          // Create a lowercase copy of this tag's contents
	          $lvalue = strtolower($value);

	          // If the first character is not a / then this is an opening tag
	          if ($lvalue{0} != "/") {

	            // Collect the tag name
	            preg_match("/^(\w*?)(\s|$)/", $lvalue, $t);

	            // If this is a protected element, activate the associated protection flag
	            if (in_array($t[1], $nobreak)) array_unshift($innbk, $t[1]);

	          // Otherwise this is a closing tag
	          } else {

	            // If this is a closing tag for a protected element, unset the flag
	            if (in_array(substr($lvalue, 1), $nobreak)) {
	              reset($innbk);
	              while (list($key, $tag) = each($innbk)) {
	                if (substr($lvalue, 1) == $tag) {
	                  unset($innbk[$key]);
	                  break;
	                }
	              }
	              $innbk = array_values($innbk);
	            }
	          }

	        // Else if we're outside any tags...
	        } else if ($value) {

	          // If unprotected...
	          if (!count($innbk)) {

	            // Use the ACK (006) ASCII symbol to replace all HTML entities temporarily
	            $value = str_replace("\x06", "", $value);
	            preg_match_all("/&([a-z\d]{2,7}|#\d{2,5});/i", $value, $ents);
	            $value = preg_replace("/&([a-z\d]{2,7}|#\d{2,5});/i", "\x06", $value);

	            // Enter the line-break loop
	            do {
	              $store = $value;

	              // Find the first stretch of characters over the $width limit
	              if (preg_match("/^(.*?\s)?([^\s]{".$width."})(?!(".preg_quote($break, "/")."|\s))(.*)$/s{$utf8}", $value, $match)) {

	                if (strlen($match[2])) {
	                  // Determine the last "safe line-break" character within this match
	                  for ($x = 0, $ledge = 0; $x < strlen($lbrks); $x++) $ledge = max($ledge, strrpos($match[2], $lbrks{$x}));
	                  if (!$ledge) $ledge = strlen($match[2]) - 1;

	                  // Insert the modified string
	                  $value = $match[1].substr($match[2], 0, $ledge + 1).$break.substr($match[2], $ledge + 1).$match[4];
	                }
	              }

	            // Loop while overlimit strings are still being found
	            } while ($store != $value);

	            // Put captured HTML entities back into the string
	            foreach ($ents[0] as $ent) $value = preg_replace("/\x06/", $ent, $value, 1);
	          }
	        }
	    }

	    // Send the modified segment down the drain
	    $drain .= $value;
	  }

	  // Return contents of the drain
	  return $drain;
	}
} //class
?>
