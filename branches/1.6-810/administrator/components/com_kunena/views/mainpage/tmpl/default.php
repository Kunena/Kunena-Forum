<?php
/**
 * @version		$Id: default.php 1020 2009-08-17 18:45:26Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

// Display the main toolbar.
$this->_displayMainToolbar();

// Add the component HTML helper path.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
// loading Modal Box
JHTML::_( 'behavior.modal' );
// getting client variable
$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

?>

<div id="wrapper">
      <table style="width: 924px;" class="adminform">
            <tbody>
                  <tr align="top">
                        <td width="60%"><div id="cpanel">
                        <h3 style="clear: both; padding-right:45px;"><?php echo JText::_('MANAGE FORUM'); ?></h3>
                                    <div style="float: left; margin-left: 5px;">

                                    
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=forum'; ?>">
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/fbforumadm.png'; ?>" alt="<?php echo JText::_('MANAGE FORUM'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Forum'); ?></span>
                                                </a>
                                          </div>
                                   
                                    <div style="float: left; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=users'.(($modal_settings) ? '&tmpl=component' : ''); ?>" <?php if($modal_settings) echo ' class="modal"  rel="{handler: \'iframe\', size: {x: 400, y: 200}}"'; ?>>
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/fbuser.png'; ?>" alt="<?php echo JText::_('MANAGE USERS'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Users'); ?></span>
                                                </a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;" id="smillies">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=smillies'; ?>">
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/fbsmiley.png'; ?>" alt="<?php echo JText::_('SMILLIES'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Smillies'); ?></span></a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;margin-bottom:30px;" id="quick_slide_add_button">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=ranks'; ?>">
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/fbranks.png'; ?>" alt="<?php echo JText::_('RANKS'); ?>" /><br/>
                                                <span><?php echo JText::_('Manage Ranks'); ?></span></a>
                                          </div>
                                    </div>
                                    <h3 style="clear: both;"><?php echo JText::_('KUNENA OPTIONS'); ?></h3>
                                    <div style="float: left; margin-left: 5px;">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=option'.(($modal_settings) ? '&tmpl=component' : ''); ?>" <?php if($modal_settings) echo ' class="modal"  rel="{handler: \'iframe\', size: {x: 400, y: 400}}"'; ?>>
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/icon-48-config.png'; ?>" alt="<?php echo JText::_('OPTIONS'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('OPTIONS'); ?></span>
                                                </a>
                                          </div>
                                    
                                    <div style="float: left; margin-left: 5px;">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=check_system'.(($modal_settings) ? '&tmpl=component' : ''); ?>" <?php if($modal_settings) echo ' class="modal"  rel="{handler: \'iframe\', size: {x: 800, y: 300}}"'; ?>>
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/icon-48-checkin.png'; ?>" alt="<?php echo JText::_('CHECK SYSTEM'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('CHECK SYSTEM'); ?>
                                                </span>
                                                </a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=news'.(($modal_settings) ? '&tmpl=component' : ''); ?>" <?php if($modal_settings) echo ' class="modal"  rel="{handler: \'iframe\', size: {x: 800, y: 400}}"'; ?>>
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/icon-48-help_header_news-48.png'; ?>" alt="<?php echo JText::_('KUNENA ULTIMATE NEWS'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('KUNENA ULTIMATE NEWS'); ?>
                                                </span>
                                                </a>
                                          </div>
                                    </div>
                                    <div style="float: left; margin-left: 5px;margin-bottom: 20px;">
                                          <div class="icon">
                                                <a href="<?php echo $uri->root().'administrator/index.php?option=com_kunena&c=info&task=info'.(($modal_settings) ? '&tmpl=component' : ''); ?>" <?php if($modal_settings) echo ' class="modal"  rel="{handler: \'iframe\', size: {x: 400, y: 200}}"'; ?>>
                                                <img src="<?php echo $uri->root().'administrator/components/com_kunena/interface/images/icon-48-help_header.png'; ?>" alt="<?php echo JText::_('INFO'); ?>" />
                                                <br/>
                                                <span><?php echo JText::_('INFO'); ?>
                                                </span>
                                                </a>
                                          </div>
                                    </div>
                              </div></td>
                        
                      		  <td width="40%">
								<div id="cpanel">
                                    <div class="kunena_news">
                                          <?php if($kunena_news) : ?>
										  <h3><?php echo JText::_('KUNENA_LATEST_NEWS'); ?></h3>
                                          <ul>
                                                <?php echo ViewMainpage::loadKunenaRSS(); ?>
                                          </ul>
                                          <?php else : ?>
                                          <img src="components/com_kunena/interface/images/logo.png" style="display: block;margin: 0 auto;" alt="Kunena logo" /> 
                                          <h3><?php echo JText::_('KUNENA_MAINMENU_INFO'); ?>
                                          <?php endif; ?>
                                    </div>
                              	</div>
							  </td>
                  </tr>
            </tbody>
      </table>
     
      
      
</div>
<form action="index.php" method="get" name="adminForm">
      <input type="hidden" name="option" value="com_kunena" />
      <input type="hidden" name="client" value="<?php echo $client->id;?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="c" value="mainpage" />
      <input type="hidden" name="boxchecked" value="0" />
</form>
