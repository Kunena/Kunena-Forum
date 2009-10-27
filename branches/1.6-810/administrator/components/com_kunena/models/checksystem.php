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
class KunenaModelChecksystem extends JModel
{
function mainpage()
    {
    	
    	$systemcheck = new ControllerCheckSystem();
    	$systemcheck->DBStatus();
    }
	
}
?>
    <div id="wrapper">	
	<table style="width: 964px;" class="adminform" align="center">
            <tbody>
                  <tr align="center">
                        <td width="80%" style="padding-right:65px;"><div id="cpanel">
                                    <h3><?php echo JText::_('System Options'); ?></h3>
                                    <div style="float: center; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="">
                                                <img src="components/com_kunena/interface/images/fbtable.png" alt="<?php echo JText::_('PRUNE FORUM'); ?>" /><br/>
                                                <span><?php echo JText::_('Prune Forum'); ?></span>
                                                </a>
                                          
                                   
                                    <div style="float: center; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="" >
                                                <img src="components/com_kunena/interface/images/fbusers.png" alt="<?php echo JText::_('SYNC USERS'); ?>" /><br/>
                                                <span><?php echo JText::_('Sync Users'); ?></span>
                                                </a>
                                          
    
                     
                                    <div style="float: center; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="">
                                                <img src="components/com_kunena/interface/images/fbupgrade.png" alt="<?php echo JText::_('Recount stats'); ?>" /><br/>
                                                <span><?php echo JText::_('Recount stats'); ?></span>
                                                </a>
                                          
                                          <div style="float: center; margin-left: 5px;" id="quick_group_manage_button">
                                          <div class="icon">
                                                <a href="">
                                                <img src="components/com_kunena/interface/images/delete.png" alt="<?php echo JText::_('Delete Sample Messages'); ?>" /><br/>
                                                <span><?php echo JText::_('Delete Sample Messages'); ?></span>
                                                </a>
                                          </div>
                                    </div>
	<div id="info" align="left" style="height:center;">

                        <td width="20%"><div id="cpanel"  align="left"  style="margin-right:40px;" >
                                    <h3><?php echo JText::_('System Info'); ?></h3>
                                    
	<div id="info" align="left"  style="height:center;">
	
		<p><?php echo JText::_('COMPONENT_VERSION'); ?>: <?php echo  JText::_('version');?></p>
				<p><?php echo JText::_('PHP VERSION'); ?> : <?php echo JText::_('PHP_VERSION'); ?></p>
				<p><?php echo JText::_('DATABASE_VERSION'); ?> :<?php echo JText::_('<strong><font color="green"> 5.0.41-community-nt</strong>'); ?> </p>
</div>
		
                                    </div></td>
		 
	<table class="adminlist" width="50%" align="right" >		
	<table style="width: 424px;" class="adminform" align="left">
		
			
            
		</table> 
</table>

	</div>
	
		
</div>

<form action="index.php" method="get" name="adminForm">
	<input type="hidden" name="option" value="com_kunena" />
	<input type="hidden" name="client" value="<?php echo $client->id;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="c" value="check_system" />
	<input type="hidden" name="boxchecked" value="0" />
</form>	