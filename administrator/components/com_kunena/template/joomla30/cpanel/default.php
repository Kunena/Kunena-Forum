<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CPanel
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<!-- Main page container -->
<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		<!-- Right side -->
         </div>
		<!-- Right side -->
			<div class="span10">
			<section class="content-block" role="main">
						<div class="alert alert-info">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Welcome on the New Kunena Environment!!!</strong> Its all about Bootstrap.
						</div>
				<div class="row-fluid">
					<div class="span7">
					<div class="well well-small" style="min-height:140px;">
                       <div class="nav-header"><?php echo 'FAQs'; ?></div>
		<div class="row-striped">
		<div class="clr">&nbsp;</div>
           <div class="pull-left"><div><img src="components/com_kunena/media/icons/kunena_logo.png" alt=""  /></div></div>
           <div class="pull-left">
			<ul>
            <li> <a href="http://docs.kunena.org/index.php/K_2.0_Installation_Guide" target="_blank">Faq: How to Setup </a></li>
            <li> <a href="http://www.kunena.org/forum/K-2-0-Support/125990-kunena-2-0-3-known-issues" target="_blank">Faq: What are the knowing errors </a></li>
            <li> <a href="http://www.kunena.org/forum" target="_blank">Faq: Free or Paid Support </a></li>
            </ul>
		    </div>
                    </div>
                    </div>

                    <div class="well well-small" style="min-height:120px;">
                       <div class="nav-header">Dashboard</div>
                         <div class="row-striped">
                         <br />
                  <div class="btn-group">
                <div class="btn" style="float:left;background-color: white !important;background-image:none;">
			         <div class="icon" style="text-align:center;">
                     <a href="index.php?option=com_kunena&view=categories">
					<img
					src="components/com_kunena/media/icons/large/categories.png"
					border="0" alt="Categories" /><br/>
                    <span>
						Categories
					</span></a>
					 </div>
                     </div>
                     <div class="btn" style="float:left;background-color: white !important;background-image:none;">
			         <div class="icon" style="text-align:center;">
				<a href="index.php?option=com_kunena&view=templates">
					<img src="components/com_kunena/media/icons/large/templates.png" alt="Templates" /><br/>
                    <span>
						Templates
					</span>
				</a>
			</div>
            </div>
            <div class="btn" style="float:left;background-color: white !important;background-image:none;">
			         <div class="icon" style="text-align:center;">
                     <a href="index.php?option=com_plugins&view=plugins&filter_folder=kunena">
					<img
					src="components/com_kunena/media/icons/large/pluginsmanager.png"
					border="0" alt="Plugin Manager" /><br/>
					<span>
						Plugin Manager
					</span></a>
					 </div>
                     </div>
                     <div class="btn" style="float:left;background-color: white !important;background-image:none;">
			         <div class="icon" style="text-align:center;">
				<a href="index.php?option=com_kunena&view=tools">
					<img src="components/com_kunena/media/icons/large/purgerestatements.png" alt="Installer" /><br/>
                    <span>
						Tools
					</span></a>
			</div>
            	</div>
            </div>
            </div>
			 </div>
             </div>
					<div class="span5">

							<div class="well well-small">
                            <div class="module-title nav-header">Kunena v3</div>
                            <div>
                            <table class="table table-striped" style="margin-bottom:0px;">
			<tr>
				<td>Version:</td>
				<td style="color:green"><?php echo KunenaForum::version(); ?></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td><?php echo KunenaForum::versionName(); ?></td>
			</tr>
			<tr>
				<td>Author:</td>
				<td><a href="http://www.kunena.org/team" target="_blank">Kunena Team</a></td>
			</tr>
			<tr>
				<td>Copyright:</td>
				<td>&copy; 2008 - 2012 Kunena, All rights reserved.</td>
			</tr>
			<tr>
				<td>License:</td>
				<td>GNU General Public License</td>
			</tr>
			<tr>
				<td>More info:</td>
				<td><a href="http://www.kunena.org/terms-of-use" target="_blank">http://www.kunena.org/terms-of-use</a></td>
			</tr>
		</table>
        </div>
        </div>
        	</div>

					</div>
				</div>
			</section>
			<div class="center">
	         <?php echo KunenaVersion::getLongVersionHTML (); ?>
	        </div>
		</div>
		</div>
		<!-- /Main page container -->