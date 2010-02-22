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

defined( '_JEXEC' ) or die();

$kunena_config =& CKunenaConfig::getInstance();

// Team credits page is not translated
?>
<div class="k_bt_cvr1">
  <div class="k_bt_cvr2">
    <div class="k_bt_cvr3">
      <div class="k_bt_cvr4">
        <div class="k_bt_cvr5">
          <table class = "kblocktable" id ="kforumcredits" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
              <tr>
                <th> <div class = "ktitle_cover"> <span class="ktitle kl" >Team credits</span> </div>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class = "kcreditsdesc"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr valign="top">
                      <td width="170"><img src="<?php echo KUNENA_DIRECTURL . '/template/default/images/kunena.logo.png';?>" alt="Kunena"  align="left" hspace="5" vspace="5"/></td>
                      <td><div  class="kcredits_intro"> An open source project like Kunena requires the dedication and investment of personal time from various contributors.
                          This version of Kunena Forum has been made possible by the following contributors (in alphabetical ordering):</div></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2" style="padding-left:20px;padding-right:20px;"><ul  class="kteam">
                          <li class="kteammember"><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a> Kunena developer and admin of the world largest Yamaha Star VMax community at <a href="http://www.starVmax.com/forum/" target='_blank' rel='follow'>www.starVmax.com/forum/</a></li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=66" target='_blank' rel='follow'>johnnydement</a> Kunena moderator</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=2171" target='_blank' rel='follow'>LDA</a> Kunena moderator</li>
                          <li class="kteammember"><a href="http://www.herppi.net" target='_blank' rel='follow'>Matias</a> Kunena developer</li>
                          <li class="kteammember"><a href="http://www.camelcity.com" target='_blank' rel='follow'>Noel Hunter</a> Kunena developer and admin of <a href="http://www.housecalls.com/view-qaa?func=listcat" target='_blank' rel='follow'>House Calls Q&amp;A Forum/</a></li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=122" target='_blank' rel='follow'>Roland76</a> Kunena developer</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=114" target='_blank' rel='follow'>severdia</a> Kunena developer</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=314" target='_blank' rel='follow'>Spock</a> Kunena moderator</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=148" target='_blank' rel='follow'>whouse</a> Kunena developer</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=1288" target='_blank' rel='follow'>xillibit</a> Kunena developer</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=447" target='_blank' rel='follow'>@quila</a> Kunena moderator</li>
                          <li class="kteammember"><a href="http://www.kunena.com/community/profile?userid=634" target='_blank' rel='follow'>810</a> Kunena developer</li>
                        </ul></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2"><div  class="kcredits_more">Special thanks go to Beat and the CB Testing team, Ida and JoniJnm for significant contributions to Kunena. In addition many members of <a href="http://www.kunena.com" target='_blank' rel='follow'>www.Kunena.com</a> have contributed and helped make this a more stable and bugfree version.
                          Our Thanks go out to all contributors of Kunena! Greetings from the global Kunena forum team! <br />
                          <br />
                          <?php
                $catid = (int)$catid;

                // Add a link to go back to the latest category we where viewing...
                echo '<div>To return to the forum ' . CKunenaLink::GetCategoryLink('showcat', $catid, JText::_('COM_KUNENA_USER_RETURN_B'), $rel='nofollow') . '<div>';
                ?>
                        </div></td>
                    </tr>
                  </table>

				<!-- Version Info -->
				<div class="kfooter"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008, 2010, 2010 <a href = "http://www.Kunena.com" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
				<!-- /Version Info -->

                  </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Begin: Forum Jump -->
<div class="k_bt_cvr1">
  <div class="k_bt_cvr2">
    <div class="k_bt_cvr3">
      <div class="k_bt_cvr4">
        <div class="k_bt_cvr5">
          <table  class = "kblocktable" id="kbottomarea"  border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
              <tr>
                <th class = "th-right"> <?php
                //(JJ) FINISH: CAT LIST BOTTOM
				if ($kunena_config->enableforumjump) {
					CKunenaTools::loadTemplate('/forumjump.php');
				}
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
