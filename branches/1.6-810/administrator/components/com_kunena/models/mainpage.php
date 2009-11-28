<?php
/**
 * @version		$Id: config.php 1014 2009-08-17 07:18:07Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Configuration Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelMainpage extends JModel
{



}

?>
<div id="wrapper">

      <table style="width: 90%;" class="adminform" align="center">
            <tbody>
                  <tr align="top">
                        <td width="60%"><div id="cpanel">
                        <h3 style="clear: both; margin-top:10px; margin-left:180px; color: #03C"><?php echo JText::_('MANAGE FORUM'); ?></h3>
                                    <div style="float: left; margin-left: 5px;">


                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&task=showAdministration">
                                                <img src="components/com_kunena/images/kunenaforumadm.png" alt="<?php echo JText::_('MANAGE FORUM'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Forum'); ?></span>
                                                </a>
                                          </div>
                                   <div style="float: left; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&task=showprofiles">
                                                <img src="components/com_kunena/images/kunenauser.png" alt="<?php echo JText::_('MANAGE USERS'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Users'); ?></span>
                                                </a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;" id="smillies">
                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&task=showsmilies">
                                                <img src="components/com_kunena/images/kunenasmiley.png" alt="<?php echo JText::_('SMILLIES'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Smillies'); ?></span></a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;margin-bottom:30px;" id="quick_slide_add_button">
                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&task=ranks">
                                                <img src="components/com_kunena/images/kunenaranks.png" alt="<?php echo JText::_('RANKS'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Ranks'); ?></span></a>
                                          </div>
                                    </div>
                                    <h3 style="clear: both;  margin-left:175px; color: #03C"><?php echo JText::_('KUNENA OPTIONS'); ?></h3>
                                    <div style="float: left; margin-left: 5px;">
                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&view=config">
                                                <img src="components/com_kunena/images/icon-48-config.png" alt="<?php echo JText::_('OPTIONS'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('OPTIONS'); ?></span>
                                                </a>
                                          </div>
                                    <div style="float: left; margin-left: 5px;">
                                          <div class="icon">
                                          <a
			href="http://www.Kunena.com" target="_blank">
                                                <img src="components/com_kunena/images/icon-48-help_header_news-48.png" alt="<?php echo JText::_('KUNENA NEWS'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('KUNENA NEWS'); ?>
                                                </span>
                                                </a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;margin-bottom: 20px;">
                                          <div class="icon">
                                                <a href="index2.php?option=com_kunena&view=info">
                                                <img src="components/com_kunena/images/icon-48-help_header.png" alt="<?php echo JText::_('INFO'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('INFO'); ?>
                                                </span>
                                                </a>
                                          </div>
                                    </div>
                              </div></td>

                      		  <td width="40%" style="padding-right:90px;">
								<div id="cpanel">

                                    <img src="components/com_kunena/images/logo.png" style="display: block;margin: 0 auto;" alt="Kunena logo" />
                                          <h3>An open source project like Kunena requires the dedication and investment of personal time from various contributors.  <a href="index2.php?option=com_kunena&view=info">Read More.</div>
                              	</div>

                                    </div>
                              	</div>
							  </td>
                  </tr>
            </tbody>
      </table>


</div>
<form action="index.php" method="get" name="adminForm">
      <input type="hidden" name="option" value="com_kunena" />
      <input type="hidden" name="boxchecked" value="0" />
</form>
