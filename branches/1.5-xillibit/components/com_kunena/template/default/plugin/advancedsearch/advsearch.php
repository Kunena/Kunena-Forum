<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on Fireboard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die('Restricted access');

if(isset($this->advsearch_hide) && $this->advsearch_hide==1)
{
    $fb_advsearch_class = ' class="fb-hidden"';
    $fb_advsearch_style = ' style="display: none;"';
    $fb_advsearch_img = KUNENA_URLIMAGESPATH . 'expand.gif';
} else {
    $fb_advsearch_class = ' class="fb-visible"';
    $fb_advsearch_style = '';
    $fb_advsearch_img = KUNENA_URLIMAGESPATH . 'shrink.gif';
}
?>

<form action="<?php echo JRoute::_(KUNENA_LIVEURLREL. '&amp;func=advsearch'); ?>" method="post" id="searchform" name="adminForm">
    <table id="fb_forumsearch" class="fb_blocktable" border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th align="left" colspan="2">
                    <div class="fb_title_cover fbm">
                        <span class="fb_title fbl"><?php echo _KUNENA_SEARCH_ADVSEARCH; ?></span>
                    </div>
                    <img id="BoxSwitch__advsearch_main" class="hideshow" src="<?php echo $fb_advsearch_img ?>" alt=""/>
                </th>
            </tr>
        </thead>

        <tbody id="advsearch_main"<?php echo $fb_advsearch_class.$fb_advsearch_style; ?>>
            <tr class="fb_sectiontableentry1">
                <td class="td-1" width="50%">
                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_SEARCHBY_KEYWORD; ?>
                        </legend>

                        <div style="line-height: 28px">
                            <?php echo _KUNENA_SEARCH_KEYWORDS; ?>:
                        </div>
                        <div style="line-height: 28px">
                            <input type="text" class="fbs input" name="q" size="35" value="<?php echo html_entity_decode_utf8($this->q); ?>" style="width:250px"/>

                            <select class="fbs" name="titleonly">
                                <option value="0"<?php if ($this->params['titleonly']==0) echo $this->selected;?>><?php echo _KUNENA_SEARCH_SEARCH_POSTS; ?></option>
                                <option value="1"<?php if ($this->params['titleonly']==1) echo $this->selected;?>><?php echo _KUNENA_SEARCH_SEARCH_TITLES; ?></option>
                            </select>
                        </div>
                    </fieldset>
                </td>
                <td class="td-1" width="50%">
                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_SEARCHBY_USER; ?>
                        </legend>

                        <div style="line-height: 28px">
                            <?php echo _KUNENA_SEARCH_UNAME; ?>:
                            <label for="exactname"><input type="checkbox" name="exactname" value="1" <?php if ($this->params['exactname']) echo $this->checked; ?> />
                            <?php echo _KUNENA_SEARCH_EXACT; ?></label>
                        </div>
                        <div id="userfield" style="line-height: 28px">
                            <input class="fbs input" type="text" name="searchuser" value="<?php echo html_entity_decode_utf8($this->params['searchuser']); ?>" style="width:250px"/>

                            <select class="fbs" name="starteronly">
                                <option value="0"<?php if ($this->params['starteronly']==0) echo $this->selected;?>><?php echo _KUNENA_SEARCH_USER_POSTED; ?></option>
                                <!--<option value="1"<?php if ($this->params['starteronly']==1) echo $this->selected;?>><?php echo _KUNENA_SEARCH_USER_STARTED; ?></option>
                                <option value="2"<?php if ($this->params['starteronly']==2) echo $this->selected;?>><?php echo _KUNENA_SEARCH_USER_ACTIVE; ?></option>-->
                            </select>
                        </div>
                    </fieldset>
		</td>
	</tr>
	<tr>
	<td colspan="2">

    <table id="fb_forumsearch_adv" class="fb_blocktable" border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th align="left" colspan="2">
				    <div class="fb_title_cover fbm">
					    <span class="fb_title fbl"><?php echo _KUNENA_SEARCH_OPTIONS; ?></span>
                    </div>
                    <img id="BoxSwitch__advsearch_options" class="hideshow" src="<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt=""/>
                </th>
            </tr>
        </thead>

        <tbody id="advsearch_options">
            <tr class="fb_sectiontableentry1">
                <td class="td-1" width="50%" style="vertical-align:top;">
<?php /*
                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_FIND_WITH; ?>
                        </legend>

                        <div>
                            <select class="fbs" name="replyless" style="width:150px">
                                <option value="0"<?php if ($replyless==0) echo $selected;?>><?php echo _KUNENA_SEARCH_LEAST; ?></option>
                                <option value="1"<?php if ($replyless==1) echo $selected;?>><?php echo _KUNENA_SEARCH_MOST; ?></option>
                            </select>

                            <input type="text" class="bginput" style="font-size:11px" name="replylimit" size="3" value="<?php echo $replylimit; ?>"/>
                            <?php echo _KUNENA_SEARCH_ANSWERS; ?>
                        </div>
                    </fieldset>
*/ ?>

                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_FIND_POSTS; ?>
                        </legend>

                        <div>
                            <select class="fbs" name="searchdate">
                                <option value="lastvisit"<?php if ($this->params['searchdate']=="lastvisit") echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_LASTVISIT; ?></option>
                                <option value="1"<?php if ($this->params['searchdate']==1) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_YESTERDAY; ?></option>
                                <option value="7"<?php if ($this->params['searchdate']==7) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_WEEK; ?></option>
                                <option value="14"<?php if ($this->params['searchdate']==14) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_2WEEKS; ?></option>
                                <option value="30"<?php if ($this->params['searchdate']==30) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_MONTH; ?></option>
                                <option value="90"<?php if ($this->params['searchdate']==90) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_3MONTHS; ?></option>
                                <option value="180"<?php if ($this->params['searchdate']==180) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_6MONTHS; ?></option>
                                <option value="365"<?php if ($this->params['searchdate']==365) echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_YEAR; ?></option>
                                <option value="all"<?php if ($this->params['searchdate']=="all") echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_ANY; ?></option>
                            </select>

                            <select class="fbs" name="beforeafter">
                                <option value="after"<?php if ($this->params['beforeafter']=="after") echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_NEWER; ?></option>
                                <option value="before"<?php if ($this->params['beforeafter']=="before") echo $this->selected;?>><?php echo _KUNENA_SEARCH_DATE_OLDER; ?></option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_SORTBY; ?>
                        </legend>

                        <div>
                            <select class="fbs" name="sortby">
                                <option value="title"<?php if ($this->params['sortby']=="title") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_TITLE; ?></option>
<?php /*
                                <option value="replycount"<?php if ($this->params['sortby']=="replycount") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_POSTS; ?></option>
*/ ?>
                                <option value="views"<?php if ($this->params['sortby']=="views") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_VIEWS; ?></option>
<?php /*
                                <option value="threadstart"<?php if ($this->params['sortby']=="threadstart") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_START; ?></option>
*/ ?>
                                <option value="lastpost"<?php if ($this->params['sortby']=="lastpost") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_POST; ?></option>
<?php /*
                                <option value="postusername"<?php if ($this->params['sortby']=="postusername") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_USER; ?></option>
*/ ?>
                                <option value="forum"<?php if ($this->params['sortby']=="forum") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_FORUM; ?></option>
                            </select>

                            <select class="fbs" name="order">
                                <option value="inc"<?php if ($this->params['order']=="inc") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_INC; ?></option>
                                <option value="dec"<?php if ($this->params['order']=="dec") echo $this->selected;?>><?php echo _KUNENA_SEARCH_SORTBY_DEC; ?></option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_START; ?>
                        </legend>

                        <div>
                            <input class="fbs input" type="text" name="limitstart" value="<?php echo $this->limitstart; ?>" size="5" />

                            <select class="fbs" name="limit">
                               <option value="5"<?php if ($this->limit==5) echo $this->selected;?>><?php echo _KUNENA_SEARCH_LIMIT5; ?></option>
                               <option value="10"<?php if ($this->limit==10) echo $this->selected;?>><?php echo _KUNENA_SEARCH_LIMIT10; ?></option>
                               <option value="15"<?php if ($this->limit==15) echo $this->selected;?>><?php echo _KUNENA_SEARCH_LIMIT15; ?></option>
                               <option value="20"<?php if ($this->limit==20) echo $this->selected;?>><?php echo _KUNENA_SEARCH_LIMIT20; ?></option>
                           </select>
                        </div>
                    </fieldset>

                </td>

                <td class="td-1" width="50%">
                    <fieldset class="fieldset">
                        <legend style="padding:0px">
                            <?php echo _KUNENA_SEARCH_SEARCHIN; ?>
                        </legend>

                        <div>
                            <div>
                                <?php echo $this->categorylist; ?>
                            </div>

                            <div>
                                <label for="childforums"><input type="checkbox" name="childforums" value="1" <?php if ($this->params['childforums']) echo 'checked="checked"'; ?> />
                                <?php echo _KUNENA_SEARCH_SEARCHIN_CHILDREN; ?></label>
                            </div>
                        </div>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="fb_list_bottom" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<td style="padding: 0 5px; height: 28px; line-height: 28px;">
        			<input class="fbs button" type="submit" value="<?php echo _KUNENA_SEARCH_SEND; ?>"/>
        			<input class="fbs button" type="reset" value="<?php echo _KUNENA_SEARCH_CANCEL; ?>" onclick="window.location='<?php echo JRoute::_(KUNENA_LIVEURLREL);?>';"/>
				</td>
			</tr>
		</tbody>
	</table>

                 </td>
            </tr>
        </tbody>
    </table>
</form>


