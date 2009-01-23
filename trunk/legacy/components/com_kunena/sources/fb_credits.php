<?php
/**
* @version $Id: fb_credits.php 1026 2008-08-25 02:48:14Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
global $is_Moderator;

// Team credits page is not translated

?>

<div class="<?php echo $boardclass; ?>_bt_cvr1">
  <div class="<?php echo $boardclass; ?>_bt_cvr2">
    <div class="<?php echo $boardclass; ?>_bt_cvr3">
      <div class="<?php echo $boardclass; ?>_bt_cvr4">
        <div class="<?php echo $boardclass; ?>_bt_cvr5">
          <table class = "fb_blocktable" id ="fb_forumcredits" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
              <tr>
                <th> <div class = "fb_title_cover"> <span class="fb_title" >Team credits</span> </div>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class = "<?php echo $boardclass; ?>creditsdesc"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr valign="top">
                      <td width="170"><img src="<?php echo JB_DIRECTURL . '/template/default/images/logo.png';?>" alt="FireBoard"  align="left" hspace="5" vspace="5"/></td>
                      <td><div  class="fb_credits_intro"> An open source project like FireBoard requires the dedication and investment of personal time from various contributors.
                          This version of FireBoard has been made possible by the following contributors (in alphabetical ordering):</div></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2" style="padding-left:20px;padding-right:20px;"><ul  class="fb_team">
                          <li class="fb_teammember"><a href="http://www.taher-zadeh.com" target='_blank' rel='follow'>danialt</a> FireBoard developer and admin of <a href="http://www.bestofjoomla.com/" target='_blank' rel='follow'>www.BestofJoomla.com</a></li>
                          <li class="fb_teammember"><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a> FireBoard developer and admin of <a href="http://www.starVmax.com/Forum/" target='_blank' rel='follow'>www.starVmax.com/Forum/</a></li>
                          <li class="fb_teammember"><a href="http://www.greatpixels.com" target='_blank' rel='follow'>greatpixels</a> FireBoard developer, designer and admin of <a href="http://www.bestofjoomla.com" target='_blank' rel='follow'>www.BestofJoomla.com</a></li>
                          <li class="fb_teammember"><a href="http://www.racoonpages.de/" target='_blank' rel='follow'>racoon</a> FireBoard developer</li>
                          <li class="fb_teammember"><a href="http://www.bestofjoomla.com/component/option,com_fireboard/Itemid,38/func,fbprofile/task,showprf/userid,2333/" target='_blank' rel='follow'>sisko1990</a> FireBoard translation coordinator and moderator of <a href="http://www.bestofjoomla.com/component/option,com_fireboard/Itemid,38/" target='_blank' rel='follow'>www.BestofJoomla.com/Forum</a></li>
                        </ul></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2"><div  class="fb_credits_more">In addition many members of <a href="http://www.bestofjoomla.com" target='_blank' rel='follow'>www.BestOfJoomla.com</a> have contributed and helped make this a more stable and bugfree version.
                          Our Thanks go out to all contributors of FireBoard! Greetings from the global FireBoard team! <br />
                          <br />
                          <?php
                $catid = (int)$catid;

                // Add a link to go back to the latest category we where viewing...
                echo '<div>To return to the forum ' . fb_link::GetCategoryLink('showcat', $catid, _USER_RETURN_B, $rel='nofollow') . '<div>';
                ?>
                        </div></td>
                    </tr>
                  </table></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
  <div class="<?php echo $boardclass; ?>_bt_cvr2">
    <div class="<?php echo $boardclass; ?>_bt_cvr3">
      <div class="<?php echo $boardclass; ?>_bt_cvr4">
        <div class="<?php echo $boardclass; ?>_bt_cvr5">
          <table  class = "fb_blocktable" id="fb_bottomarea"  border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
              <tr>
                <th class = "th-right"> <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                ?>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Finish: Forum Jump -->
