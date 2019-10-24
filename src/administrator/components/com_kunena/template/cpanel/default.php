<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      CPanel
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
				<div class="row clearfix">
					<div class="col-xl-4 col-md-12">
						<div class="card proj-t-card">
							<div class="card-body">
								<div class="row align-items-center mb-30">
									<div class="col-auto">
										<i class="fas fa-eye text-cyan f-30"></i>
									</div>
									<div class="col pl-0">
										<h6 class="mb-0">Ticket Answered</h6>
										<h6 class="mb-0 text-cyan">Live Update</h6>
									</div>
								</div>
								<div class="row align-items-center text-center">
									<div class="col">
										<h6 class="mb-0">327</h6></div>
									<div class="col"><i class="fas fa-exchange-alt text-cyan f-18"></i></div>
									<div class="col">
										<h6 class="mb-0">10 Days</h6></div>
								</div>
								<h6 class="pt-badge bg-cyan"><i class="fas fa-exclamation text-white f-18"></i></h6>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-6">
						<div class="card proj-t-card">
							<div class="card-body">
								<div class="row align-items-center mb-30">
									<div class="col-auto">
										<i class="fas fa-cloud-download-alt text-cyan f-30"></i>
									</div>
									<div class="col pl-0">
										<h6 class="mb-0">Kunena Version Check</h6>
										<h6 class="mb-0 text-cyan">Last Check: Today</h6>
									</div>
								</div>
								<div class="row align-items-center text-center">
									<div class="col pl-5"><img src="components/com_kunena/media/icons/kunena_logo.png" style="width: 70%"/></div>
									<div class="col">
										<h6 class="mb-0">6.0.0-alpha</h6></div>
								</div>
								<h6 class="pt-badge bg-cyan"><i class="fas fa-check text-white f-18"></i></h6>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-6">
						<div class="card proj-t-card">
							<div class="card-body">
								<div class="row align-items-center mb-30">
									<div class="col-auto">
										<i class="fas fa-lightbulb text-cyan f-30"></i>
									</div>
									<div class="col pl-0">
										<h6 class="mb-0">Unique Innovation</h6>
										<h6 class="mb-0 text-cyan">Last Check</h6>
									</div>
								</div>
								<div class="row align-items-center text-center">
									<div class="col">
										<h6 class="mb-0">Today</h6></div>
									<div class="col"><i class="fas fa-exchange-alt text-cyan f-18"></i></div>
									<div class="col">
										<h6 class="mb-0">248</h6></div>
								</div>
								<h6 class="pt-badge bg-cyan">73%</h6>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-md-12">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=categories');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">20 / 330</h3>
										<p class="mb-0">Last Edit: Welcome</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-list-alt bg-cyan"></i>
									</div>
									<span class="pt-badge bg-cyan">
										<a href="<?php echo Route::_('index.php?option=com_kunena&view=categories&layout=create');?>">
											<i class="fas fa-plus" style="width: 12px;height: 12px;margin-top: -40px;font-size: 12px"></i>
										</a>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=users');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_USERS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">30,564</h3>
										<p class="mb-0">May 23 - June 01 (2017)</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-users bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=attachments');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_FILES') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">423</h3>
										<p class="mb-0">photo.png (topic id: 44343)</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-photo-video bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=smilies');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_EMOTICONS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">426</h3>
										<p class="mb-0">Icons</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-grin-squint-tears bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-12">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=config');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CONFIG') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan"> .</h3>
										<p class="mb-0"> .</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-edit bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=statistics');?>">
												<?php echo Text::_('COM_KUNENA_MENU_STATISTICS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">.</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-chart-bar bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=templates');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TEMPLATES') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">1</h3>
										<p class="mb-0">Installed</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-palette bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=ranks');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">12</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-bars bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-12">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=plugins');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">13</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-plug bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=trash');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TRASH') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">12</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-trash-alt bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=logs');?>">
												<?php echo Text::_('COM_KUNENA_LOG_MANAGER') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">12</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-clipboard-list bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6">
						<div class="card proj-t-card comp-card">
							<div class="card-body">
								<div class="row align-items-center">
									<div class="col">
										<h6 class="mb-25">
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=tools');?>">
												<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TOOLS') ?>
											</a>
										</h6>
										<h3 class="fw-700 text-cyan">12</h3>
										<p class="mb-0">Items</p>
									</div>
									<div class="col-auto">
										<i class="fas fa-tools bg-cyan"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
</div>
