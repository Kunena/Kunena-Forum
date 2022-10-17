<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

$markAllReadUrl = KunenaCategoryHelper::get()->getMarkReadUrl();
$config         = KunenaFactory::getConfig();
$status         = $config->userStatus;
$config         = KunenaFactory::getTemplate()->params;
?>

<div class="klogout">
	<?php if ($config->get('displayDropdownMenu'))
		:
		?>
            <div class="btn-group">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
					<?php
					$showOnlineStatus = $this->me->showOnline == 1;

					if ($this->me->getStatus() == 0 && $status && $showOnlineStatus)
						:
						echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' green', 20, 20, 'green');
                    elseif ($this->me->getStatus() == 1 && $status && $showOnlineStatus)
						:
						echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' yellow', 20, 20, 'yellow');
                    elseif ($this->me->getStatus() == 2 && $status && $showOnlineStatus)
						:
						echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' red', 20, 20, 'red');
                    elseif ($this->me->getStatus() == 3 && $status || !$showOnlineStatus)
						:
						echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' grey', 20, 20, 'grey');
					else

						:
						echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType') . ' none', 20, 20, 'none');
					endif; ?>
                </button>

                <div class="dropdown-menu dropdown-menu-end" id="nav-menu userdropdownlogout" role="menu">
					<?php if (KunenaFactory::getTemplate()->params->get('displayDropdownContent'))
						:
						?>
                        <div class="center">
                            <p>
                                <strong><?php echo $this->me->getLink(null, null, '', '', KunenaTemplate::getInstance()->tooltips()); ?></strong>
                            </p>
                            <a href="<?php echo $this->me->getURL(); ?>">
								<?php echo $this->me->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post'); ?>
                            </a>
                            <p><?php echo $this->subLayout('User/Item/Status')->set('user', $this->me); ?></p>
                            <p>
								<?php echo KunenaIcons::clock(); ?>
								<?php echo $this->me->getLastVisitDate()->toKunena('config_postDateFormat'); ?>
                            </p>
                        </div>

                        <div class="dropdown-divider"></div>

						<?php if ($status) : ?>
  						<div id="status-online">
							<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=0&' . Session::getFormToken() . '=1'); ?>"
								class="btn btn-sm btn-outline-success text-center d-block m-2">
								<?php echo KunenaIcons::online(); ?>
								<?php echo Text::_('COM_KUNENA_ONLINE') ?>
							</a>

							<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=1&' . Session::getFormToken() . '=1'); ?>"
								class="btn btn-sm btn-outline-warning text-center d-block m-2">
								<?php echo KunenaIcons::away(); ?>
								<?php echo Text::_('COM_KUNENA_AWAY') ?>
							</a>
							<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=2&' . Session::getFormToken() . '=1'); ?>"
								class="btn btn-sm btn-outline-danger text-center d-block m-2">
								<?php echo KunenaIcons::busy(); ?>
								<?php echo Text::_('COM_KUNENA_BUSY') ?>
							</a>
							<a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=status&status=3&' . Session::getFormToken() . '=1'); ?>"
								class="btn btn-sm btn-outline-secondary text-center d-block m-2">
								<?php echo KunenaIcons::invisible(); ?>
								<?php echo Text::_('COM_KUNENA_INVISIBLE') ?>
							</a>
						</div> 
                       <div class="dropdown-divider"></div>

                        <div id="statustext">
							<?php HTMLHelper::_('bootstrap.renderModal', 'statusText'); ?>
                            <a data-bs-toggle="modal" data-bs-target="#statusTextModal" class="btn btn-link">
								<?php echo KunenaIcons::edit(); ?>
								<?php echo Text::_('COM_KUNENA_STATUS') ?>
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>
					<?php endif; ?>

						<?php if (!empty($this->announcementsUrl))
						:
						?>
                        <div id="announcement">
                            <a href="<?php echo $this->announcementsUrl; ?>" class="btn btn-link">
								<?php echo KunenaIcons::pencil(); ?>
								<?php echo Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS') ?>
                            </a>
                        </div>
					<?php endif; ?>

						<?php if (!empty($this->pm_link))
						:
						?>
                        <div id="mail">
                            <a href="<?php echo $this->pm_link; ?>" class="btn btn-link">
								<?php echo KunenaIcons::email(); ?>
								<?php echo $this->inboxCount; ?>
                            </a>
                        </div>
					<?php endif; ?>

                        <div id="settings">
                            <a href="<?php echo $this->me->getUrl(false, 'edit'); ?>" class="btn btn-link">
								<?php echo KunenaIcons::cog(); ?>
								<?php echo Text::_('COM_KUNENA_LOGOUTMENU_LABEL_PREFERENCES'); ?>
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>

						<?php if ($markAllReadUrl)
						:
						?>
                        <div id="allread">
                            <a href="<?php echo $markAllReadUrl; ?>" class="btn btn-link">
								<?php echo KunenaIcons::drawer(); ?>
								<?php echo Text::_('COM_KUNENA_MARK_ALL_READ'); ?>
                            </a>
                        </div>
					<?php endif ?>
						<?php if ($this->plglogin)
						:
						?>
                        <div class="dropdown-divider"></div>
						<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_logout'); ?>
                        <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post"
                              id="logout-form" class="form-inline">
                            <div>
                                <button class="btn btn-link" name="submit" type="submit">
									<?php echo KunenaIcons::out(); ?>
									<?php echo Text::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>
                                </button>
                            </div>
                            <input type="hidden" name="view" value="user"/>
                            <input type="hidden" name="task" value="logout"/>
							<?php echo HTMLHelper::_('form.token'); ?>
                        </form>
					<?php endif ?>
						<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_logout_bottom'); ?>
					<?php endif ?>
                </div>
            </div>
	<?php
	endif;

	/*
	 * Note these have to be outsize the dropdown as z-index stack context is different
	 * from the parent forcing the dropsown to take over z-index calculation
	 */
	?>
</div>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" method="post" id="status-text-form"
      class="form-inline">
	<?php echo $this->subLayout('Widget/Modal')
		->set('id', 'statusTextModal')
		->set('name', 'status_text')
		->set('label', Text::_('COM_KUNENA_STATUS_MESSAGE'))
		->set('description', Text::_('COM_KUNENA_STATUS_TYP'))
		->set('data', $this->me->status_text)
		->set('form', 'status-text-form'); ?>
    <input type="hidden" name="view" value="user"/>
    <input type="hidden" name="task" value="statustext"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<script>
	const status = document.querySelector('input[name=status]')
	if (status !== null) {
		status.addEventListener('change', () => {
			document.getElementById('status-form').submit();
		});
	}

	const btnStatusText = document.getElementById('btn_statustext')
	if (btnStatusText) {
		btnStatusText.addEventListener('click', () => {
			document.getElementById('status-text-form').submit();
		});
	}
</script>
