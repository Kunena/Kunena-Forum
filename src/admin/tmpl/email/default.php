<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template
 * @subpackage    Logs
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect')
	->useScript('bootstrap.popover');
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<div id="dashboard-icons" class="btn-group">
					<a class="btn btn-outline-primary" href="#">
						<i class="icon-big icon-user"></i><br/>
						<span><?php echo Text::_('Subscription') ?></span>
					</a>
					<a class="btn btn-outline-primary" href="#">
						<i class="icon-big icon-shield"></i><br/>
						<span><?php echo Text::_('Moderator') ?></span>
					</a>
					<a class="btn btn-outline-primary" href="#">
						<i class="icon-big icon-checkmark"></i><br/>
						<span><?php echo Text::_('Approved') ?></span>
					</a>
					<a class="btn btn-outline-primary" href="#">
						<i class="icon-big icon-notification-circle"></i><br/>
						<span><?php echo Text::_('Report') ?></span>
					</a>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
