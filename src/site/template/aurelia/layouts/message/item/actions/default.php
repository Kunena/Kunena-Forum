<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

$config          = KunenaConfig::getInstance();
$this->ktemplate = KunenaFactory::getTemplate();
$fullactions     = $this->ktemplate->params->get('fullactions');
$quick           = $this->ktemplate->params->get('quick');
?>

	<?php if (!$fullactions)
	:
	?>
	<?php if (empty($this->message_closed))
	:
	?>
    <div class="kmessagepadding">
		<?php if ($this->quickReply && $quick == 0) : ?>
			<button type="button" class="btn btn-outline-primary border kicon-button btn-small" data-bs-toggle="modal" data-bs-target="#kreply<?php echo $this->message->displayField('id'); ?>_form">
				<?php echo KunenaIcons::undo() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
			</button>
		<?php endif; ?>

		<?php if ($this->quickReply && $quick == 1) : ?>
            <a id="btn_qreply" href="#kreply<?php echo $this->message->displayField('id'); ?>_form" role="button"
               class="btn btn-outline-primary border"
               data-bs-toggle="modal" data-bs-target="#kreply<?php echo $this->message->displayField('id'); ?>_form"
               rel="nofollow">
				<?php echo KunenaIcons::undo() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
            </a>
		<?php endif; ?>

		<?php echo $this->messageButtons->get('reply'); ?>
		<?php echo $this->messageButtons->get('quote'); ?>
		<?php echo $this->messageButtons->get('edit'); ?>
		<?php
		if ($this->message->isAuthorised('delete'))
			:
			?>
			<?php echo $this->messageButtons->get('delete'); ?>
		<?php endif; ?>
		<?php echo $this->messageButtons->get('thankyou'); ?>
		<?php echo $this->messageButtons->get('unthankyou'); ?>

		<?php if ($this->messageButtons->get('moderate'))
			:
			?>
            <br/>
            <br/>
			<?php echo $this->messageButtons->get('moderate'); ?>
			<?php echo $this->messageButtons->get('undelete'); ?>
			<?php echo $this->messageButtons->get('permdelete'); ?>
			<?php echo $this->messageButtons->get('publish'); ?>
			<?php echo $this->messageButtons->get('spam'); ?>
		<?php endif; ?>
    </div>

<?php else

	:
	?>

    <div class="kreplymessage">
		<?php echo $this->message_closed; ?>
    </div>
<?php endif;
endif; ?>

	<?php if ($fullactions) : ?>
	<?php if (empty($this->message_closed)) : ?>
        <div class="btn-toolbar btn-marging kmessagepadding">

			<?php if ($this->quickReply && $quick == 0) : ?>
                
                <button type="button" class="btn btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#kreply<?php echo $this->message->displayField('id'); ?>_form">
  <?php echo KunenaIcons::undo() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
</button>
			<?php endif; ?>

			<?php if ($this->quickReply && $quick == 1) : ?>
                
                <button type="button" class="btn btn-outline-primary border" data-bs-toggle="modal" data-bs-target="#kreply<?php echo $this->message->displayField('id'); ?>_form">
  <?php echo KunenaIcons::undo() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_QUICK_REPLY'); ?>
</button>
			<?php endif; ?>

            <div class="btn-group">
                <button class="btn btn-outline-primary border" data-bs-toggle="dropdown">
					<?php echo KunenaIcons::edit() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_ACTION'); ?>
                </button>
                <button class="btn btn-outline-primary border dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><?php echo $this->messageButtons->get('reply_dropdown'); ?></li>
                    <li><?php echo $this->messageButtons->get('quote_dropdown'); ?></li>
                    <li><?php echo $this->messageButtons->get('edit_dropdown'); ?></li>
					<?php
					if ($config->userDeleteMessage > 0) : ?>
                        <li><?php echo $this->messageButtons->get('delete_dropdown'); ?></li>
					<?php endif; ?>
                </ul>
            </div>

			<?php if ($this->messageButtons->get('moderate')) : ?>
                <div class="btn-group">
                    <button class="btn btn-outline-primary border" data-bs-toggle="dropdown">
						<?php echo KunenaIcons::shuffle() . ' ' . Text::_('COM_KUNENA_MESSAGE_ACTIONS_LABEL_MODERATE'); ?>
                    </button>
                    <button class="btn btn-outline-primary border dropdown-toggle" data-bs-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><?php echo $this->messageButtons->get('moderate_dropdown'); ?></li>
                        <li><?php echo $this->messageButtons->get('delete_dropdown'); ?></li>
                        <li><?php echo $this->messageButtons->get('undelete_dropdown'); ?></li>
                        <li><?php echo $this->messageButtons->get('permdelete_dropdown'); ?></li>
                        <li><?php echo $this->messageButtons->get('publish_dropdown'); ?></li>
                        <li><?php echo $this->messageButtons->get('spam_dropdown'); ?></li>
                    </ul>
                </div>
			<?php endif; ?>

			<?php echo $this->messageButtons->get('thankyou'); ?>
			<?php echo $this->messageButtons->get('unthankyou'); ?>
        </div>

	<?php else : ?>
        <div class="kreplymessage">
			<?php echo $this->message_closed; ?>
        </div>
	<?php endif;
endif;
