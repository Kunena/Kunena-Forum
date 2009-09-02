<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
	<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php echo $this->loadCommonTemplate('pathway'); ?>

<!-- B: Cat list Top -->
<table class="fb_list_top" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
	<tr>
		<td class="fb_list_markallcatsread">
                		</td>
		<td class="fb_list_categories">
                <form id = "jumpto" name = "jumpto" method = "post" target = "_self" action = "/index.php/kunena">

    <span style = "width: 100%; text-align: right;">

        <input type = "hidden" name = "func" value = "showcat"/>
	<select name="catid" id="catid" class="inputbox fbs" size="1"  onchange = "if(this.options[this.selectedIndex].value > 0){ this.form.submit() }"><option value="0"  selected="selected">Board Categories&#32;</option><option value="1" >Main Forum</option><option value="2" >...&nbsp;Welcome Mat</option><option value="3" >...&nbsp;Suggestion Box</option></select>        <input type = "submit" name = "Go"  class="fb_button fbs" value = "Go"/>
    </span>
</form>

		</td>
	</tr>

</table>
<!-- F: Cat list Top -->


            <!-- B: List Cat -->
<div class="fb__bt_cvr1" id="fb_block1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
            <table class = "fb_blocktable"  width="100%" id = "fb_cat1" border = "0" cellspacing = "0" cellpadding = "0">

                <thead>
                    <tr>
                        <th colspan = "5">
                            <div class = "fb_title_cover fbm" >
                                <a class="fb_title fbl" href="/index.php/kunena/1-main-forum" title="" rel="follow">Main Forum</a>This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.                            </div>
                            <img id = "BoxSwitch_1__catid_1" class = "hideshow" src = "http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt = ""/>
                        </th>
                    </tr>

                </thead>
                <tbody id = "catid_1">
                    <tr class = "fb_sth fbs ">
                        <th class = "th-1 fb_sectiontableheader" width="1%">&nbsp;</th>
                        <th class = "th-2 fb_sectiontableheader" align="left">Forum</th>
                        <th class = "th-3 fb_sectiontableheader" align="center" width="5%">Topics</th>

                        <th class = "th-4 fb_sectiontableheader" align="center" width="5%">

Replies                        </th>

                        <th class = "th-5 fb_sectiontableheader" align="left" width="25%">
Last Post                        </th>
                    </tr>

                    
                                <tr class = "fb_sectiontableentry2" id="fb_cat2">
                                    <td class = "td-1" align="center">
                                        <a href="/index.php/kunena/2-welcome-mat" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/folder_nonew.gif" border="0" alt="No New Posts" title="No New Posts" /></a>                                    </td>

                                    <td class = "td-2" align="left">
                                        <div class = "fb_thead-title fbl">
                                            <a href="/index.php/kunena/2-welcome-mat" title="" rel="follow">Welcome Mat</a>
                                                                                    </div>

                                        
                                            <div class = "fb_thead-desc fbm">
We encourage new members to post a short introduction of themselves in this forum category. Get to know each other and share you common interests.<br />
                                            </div>

                                                                            </td>

                                    <td class = "td-3  fbm" align="center" >1</td>

                                    <td class = "td-4  fbm" align="center" >
0                                    </td>

                                    
                                        <td class = "td-5" align="left">
                                            <div class = "fb_latest-subject fbm">

<a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1" title="" rel="follow">Welcome to Kunena!</a>                                            </div>

                                            <div class = "fb_latest-subject-by fbs">
by <a href="/index.php/kunena/fbprofile/userid-62" title="" rel="nofollow">Kunena</a> | <b>Yesterday</b>&#32;08:12 <a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/tlatest.gif" border="0" alt="Show most recent message" title="Show most recent message"/></a>                                            </div>
                                        </td>
                                </tr>

                                    
                                <tr class = "fb_sectiontableentry1" id="fb_cat3">
                                    <td class = "td-1" align="center">
                                        <a href="/index.php/kunena/3-suggestion-box" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/folder_nonew.gif" border="0" alt="No New Posts" title="No New Posts" /></a>                                    </td>

                                    <td class = "td-2" align="left">
                                        <div class = "fb_thead-title fbl">
                                            <a href="/index.php/kunena/3-suggestion-box" title="" rel="follow">Suggestion Box</a>
                                                                                    </div>

                                        
                                            <div class = "fb_thead-desc fbm">
Have some feedback and input to share?<br />
Don&#039;t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.                                            </div>

                                                                            </td>

                                    <td class = "td-3  fbm" align="center" >0</td>

                                    <td class = "td-4  fbm" align="center" >

0                                    </td>

                                    
                                        <td class = "td-5"  align="left">
No Posts                                        </td>

                                        </tr>

                                    </tbody>
            </table>


 </div>
 </div>
 </div>
 </div>
 </div>
<!-- F: List Cat -->

<!-- B: Cat list Bottom --><table class="fb_list_bottom" border = "0" cellspacing = "0" cellpadding = "0" width="100%">	<tr>
		<td class="fb_list_markallcatsread">
                		</td>

		<td class="fb_list_categories">
                <form id = "jumpto" name = "jumpto" method = "post" target = "_self" action = "/index.php/kunena">
    <span style = "width: 100%; text-align: right;">

        <input type = "hidden" name = "func" value = "showcat"/>
	<select name="catid" id="catid" class="inputbox fbs" size="1"  onchange = "if(this.options[this.selectedIndex].value > 0){ this.form.submit() }"><option value="0"  selected="selected">Board Categories&#32;</option><option value="1" >Main Forum</option><option value="2" >...&nbsp;Welcome Mat</option><option value="3" >...&nbsp;Suggestion Box</option></select>        <input type = "submit" name = "Go"  class="fb_button fbs" value = "Go"/>

    </span>
</form>
		</td>
	</tr>

</table><!-- F: Cat list Bottom --><!-- WHOIS ONLINE -->
<div class="fb__bt_cvr1">
<div class="fb__bt_cvr2">
<div class="fb__bt_cvr3">
<div class="fb__bt_cvr4">
<div class="fb__bt_cvr5">
    <table class = "fb_blocktable" id ="fb_whoisonline"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>

            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <a class = "fb_title fbl" href = "/index.php/kunena/who">
						Online                        <b>0</b>
						Members						and                        <b>1</b>
						Guest                        </a>

                    </div>
                    <img id = "BoxSwitch_whoisonline__whoisonline_tbody" class = "hideshow" src = "http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt = ""/>
                </th>
            </tr>
        </thead>

        <tbody id = "whoisonline_tbody">
            <tr class = "fb_sectiontableentry1">
                <td class = "td-1 fbm" align="left">

                                                                  



                    <!--               groups     -->

                                    </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>

<!-- /WHOIS ONLINE -->


<?php echo $this->loadCommonTemplate('footer'); ?>
	</div>