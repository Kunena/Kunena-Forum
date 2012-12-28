<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Load Mootools
JHtml::_('behavior.framework', true);
JHtml::_('script','system/multiselect.js',false,true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Load Less
$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/styles.css' );
?>
<!-- Container -->
<!-- Left side -->
				
				<!-- Main navigation -->
					<ul class="nav nav-list">
						<li class="current"><a href="<?php JUri::base(true)?>index.php?option=com_kunena"><i class="icon-dashboard"></i>Dashboard</a></li>
                        <li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=config"><i class="icon-wrench"></i>Config</a></li>
						<li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=categories"><i class="icon-list-view"></i>Categories</a></li>		
                       <!-- <li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=messages"><i class="icon-list-view"></i>Messages</a></li>		-->
                        <li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=users"><i class="icon-user"></i>Users</a></li>
                        <li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=trash"><i class="icon-trash"></i>Trash</a></li>
						<li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=attachments"><i class="icon-folder-open"></i>File explorer</a></li>
						<li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=smilies"><i class="icon-basket"></i>Smilies</a></li>
                        <li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=ranks"><i class="icon-basket"></i>Ranks</a></li>
						<li><a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=templates"><i class="icon-eye-open"></i>Templates</a></li>						
						<li><a href="<?php JUri::base(true)?>index.php?option=com_plugins&view=plugins&filter_folder=kunena"><i class="icon-cube"></i>Plugins</a></li>
						<li>
							<a href="<?php JUri::base(true)?>index.php?option=com_kunena&view=tools"><i class="icon-tools"></i>Tools</a></li>
					</ul>
				<!-- /Main navigation -->				
			<!-- /Left side -->