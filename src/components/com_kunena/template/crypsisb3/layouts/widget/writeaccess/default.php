<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

$topic  = KunenaForumTopicHelper::get($this->id);
$config = KunenaFactory::getConfig();
?>

<div class="kfrontend">
	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<div class="btn btn-default btn-sm" data-toggle="collapse"
			     data-target="#writeaccess"><?php echo KunenaIcons::collapse(); ?></div>
		</div>
	</div>

	<h3 class="btn-link">
		<?php echo Text::_('COM_KUNENA_WRITEACCESS'); ?>
	</h3>

	<div class="row-fluid collapse in" id="writeaccess">
		<div class="well-small">
			<ul class="unstyled col-md-6">
				<li>
					<?php
					if ($topic->getCategory()->isAuthorised('topic.create'))
					{ ?>
				<li>
					<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_CREATETOPIC'); ?>
				</li>
				<?php }
				else
				{ ?>
					<li>
						<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_CREATETOPIC'); ?>
					</li>
				<?php }

				if ($topic->isAuthorised('reply', KunenaUserHelper::getMyself()))
				{ ?>
					<li>
						<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_REPLY'); ?>
					</li>
				<?php }
				else
				{ ?>
					<li>
						<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_REPLY'); ?>
					</li>
				<?php }

				if ($topic->isAuthorised('edit') || $topic->isAuthorised('reply'))
				{
					if ($config->image_upload == '')
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_IMAGE_ADDATTACH'); ?>
						</li> <?php
					}
					elseif ($config->image_upload == 'everybody')
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_IMAGE_ADDATTACH'); ?>
						</li> <?php
					}
					elseif ($config->image_upload == 'admin' && KunenaUserHelper::getMyself()->isAdmin() ||
						$config->image_upload == 'moderator' && KunenaUserHelper::getMyself()->isModerator() ||
						$config->image_upload == 'registered' && KunenaUserHelper::getMyself()->exists())
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_IMAGE_ADDATTACH'); ?>
						</li> <?php
					}
					else
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_IMAGE_ADDATTACH'); ?>
						</li> <?php
					}

					if ($config->file_upload == '')
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_ADDATTACH'); ?>
						</li> <?php
					}
					elseif ($config->file_upload == 'everybody')
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_ADDATTACH'); ?>
						</li> <?php
					}
					elseif ($config->file_upload == 'admin' && KunenaUserHelper::getMyself()->isAdmin() ||
						$config->file_upload == 'moderator' && KunenaUserHelper::getMyself()->isModerator() ||
						$config->file_upload == 'registered' && KunenaUserHelper::getMyself()->exists())
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_ADDATTACH'); ?>
						</li> <?php
					}
					else
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_ADDATTACH'); ?>
						</li> <?php
					}
				}

				if ($topic->isAuthorised('edit') || $topic->getUserTopic()->posts && $config->useredit)
				{
					if ($config->useredit == 3 && $topic->getLastPostAuthor()->userid != KunenaUserHelper::getMyself()->userid)
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_EDITPOST'); ?>
						</li>
						<?php
					}
					elseif ($config->useredit == 4 && $topic->getFirstPostAuthor()->userid != KunenaUserHelper::getMyself()->userid)
					{
						if (KunenaUserHelper::getMyself()->isAdmin())
						{
							?>
							<li>
								<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_EDITPOST'); ?>
							</li>
							<?php
						}
						else
						{
							?>
							<li>
								<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_EDITPOST'); ?>
							</li>
							<?php
						}
					}
					else
					{
						?>
						<li>
							<b><?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_EDITPOST'); ?>
						</li>
						<?php
					}
				}
				else
				{ ?>
					<li>
						<b><?php echo Text::_('COM_KUNENA_ACCESS_NOTALLOWED'); ?></b> <?php echo Text::_('COM_KUNENA_ACCESS_ALLOWED_EDITPOST'); ?>
					</li>
				<?php }
				?>
				</li>
			</ul>
		</div>
	</div>
</div>
