<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<script type="text/javascript">
    Joomla.orderTable = function () {
        const table = document.getElementById("sortTable");
        const direction = document.getElementById("directionTable");
        const order = table.options[table.selectedIndex].value;

        if (order !== '<?php echo $this->list->Ordering; ?>') {
            var dirn = 'asc';
        } else {
            var dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users'); ?>"
                      method="post" id="adminForm"
                      name="adminForm">
                    <input type="hidden" name="type" value="list"/>
                    <input type="hidden" name="filter_order"
                           value="<?php echo $this->escape($this->state->get('list.ordering')) ?>"/>
                    <input type="hidden" name="filter_order_Dir"
                           value="<?php echo $this->escape($this->state->get('list.direction')) ?>"/>

                    <div id="filter-bar" class="btn-toolbar">
                        <div class="filter-search btn-group pull-left">
                            <label for="filter_search"
                                   class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_INPUT_SEARCH') ?></label>
                            <input type="text" name="filter_search" id="filter_search" class="filter form-control"
                                   placeholder="<?php echo Text::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>"
                                   value="<?php echo $this->filter->Search; ?>"
                                   title="<?php echo Text::_('COM_KUNENA_FIELD_INPUT_SEARCH'); ?>"/>
                        </div>
                        <div class="btn-group pull-left">
                            <button class="btn btn-outline-primary tip" type="submit"
                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i
                                        class="icon-search"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>
                            </button>
                            <button class="btn btn-outline-primary tip" type="button"
                                    title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>"
                                    onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i
                                        class="icon-remove"></i> <?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>
                            </button>
                        </div>
                        <div class="btn-group pull-right hidden-phone">
                            <label for="limit"
                                   class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
							<?php echo $this->pagination->getLimitBox(); ?>
                        </div>
                        <div class="btn-group pull-right hidden-phone">
                            <label for="directionTable"
                                   class="element-invisible"><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></label>
                            <select name="directionTable" id="directionTable" class="input-medium"
                                    onchange="Joomla.orderTable()">
                                <option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->list->Direction); ?>
                            </select>
                        </div>
                        <div class="btn-group pull-right">
                            <label for="sortTable"
                                   class="element-invisible"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
                            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                                <option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
								<?php echo HTMLHelper::_('select.options', $this->sortFields, 'value', 'text', $this->list->Ordering); ?>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <table class="table table-striped" id="userList">
                        <thead>
                        <tr>
                            <th width="1%" class="nowrap center"><input type="checkbox" name="toggle" value=""
                                                                        onclick="Joomla.checkAll(this)"/></th>
                            <th><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th class="hidden-phone"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="5%"
                                class="hidden-phone"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_GEN_IP', 'ip', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="10%"
                                class="nowrap hidden-phone hidden-tablet"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_A_RANKS', 'rank', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="5%"
                                class="nowrap center hidden-phone hidden-tablet"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="5%"
                                class="nowrap center hidden-phone"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="5%"
                                class="nowrap center hidden-phone"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="5%"
                                class="nowrap center hidden-phone hidden-tablet"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                            <th width="1%"
                                class="nowrap center"><?php echo HTMLHelper::_('grid.sort', 'COM_KUNENA_ANN_ID', 'id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?></th>
                        </tr>
                        <tr>
                            <td class="hidden-phone">
                            </td>
                            <td class="nowrap">
                                <label for="filter_username"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS'); ?></label>
                                <input class="input-block-level input-filter filter form-control" type="text"
                                       name="filter_username"
                                       id="filter_username"
                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                       value="<?php echo $this->filter->Username; ?>"
                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                            </td>
                            <td class="nowrap">
                                <label for="filter_email"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS'); ?></label>
                                <input class="input-block-level input-filter filter form-control" type="text"
                                       name="filter_email"
                                       id="filter_email"
                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                       value="<?php echo $this->filter->Email; ?>"
                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                            </td>
                            <td class="nowrap">
                                <label for="filter_ip"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS'); ?></label>
                                <input class="input-block-level input-filter filter form-control" type="text"
                                       name="filter_ip"
                                       id="filter_ip"
                                       placeholder="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"
                                       value="<?php echo $this->filter->Ip; ?>"
                                       title="<?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>"/>
                            </td>
                            <td class="nowrap">
                                <label for="filter_rank"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_USERS_FIELD_INPUT_SEARCHUSERS'); ?></label>
                                <select name="filter_rank" id="filter_rank" class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?></option>
									<?php echo HTMLHelper::_('select.options', $this->ranksOptions(), 'value', 'text', $this->filter->Rank); ?>
                                </select>
                            </td>
                            <td class="nowrap center hidden-phone">
                                <label for="filter_signature"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filter_signature" id="filter_signature"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
									<?php echo HTMLHelper::_('select.options', $this->signatureOptions(), 'value', 'text', $this->filter->Signature); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filter_block"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filter_block" id="filter_block" class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
									<?php echo HTMLHelper::_('select.options', $this->blockOptions(), 'value', 'text', $this->filter->Block, true); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filter_banned"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filter_banned" id="filter_banned"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
									<?php echo HTMLHelper::_('select.options', $this->bannedOptions(), 'value', 'text', $this->filter->Banned); ?>
                                </select>
                            </td>
                            <td class="nowrap center">
                                <label for="filter_moderator"
                                       class="element-invisible"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></label>
                                <select name="filter_moderator" id="filter_moderator"
                                        class="select-filter filter form-control"
                                        onchange="Joomla.orderTable()">
                                    <option value=""><?php echo Text::_('COM_KUNENA_FIELD_LABEL_ALL'); ?></option>
									<?php echo HTMLHelper::_('select.options', $this->moderatorOptions(), 'value', 'text', $this->filter->Moderator); ?>
                                </select>
                            </td>
                            <td class="nowrap center hidden-phone">
                            </td>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="12">
								<?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                        </tfoot>
                        <tbody>
						<?php
						$i                        = 0;
						$img_no                   = '<i class="icon-cancel"></i>';
						$img_yes                  = '<i class="icon-checkmark"></i>';

						if ($this->pagination->total > 0)
							:
							foreach ($this->users as $user)
								:
								$userBlockTask = $user->isBlocked() ? 'unblock' : 'block';
								$userBannedTask   = $user->isBanned() ? 'unban' : 'ban';
								$userModerateTask = $user->isModerator() ? 'unmoderate' : 'moderate';
								?>
                                <tr>
                                    <td>
										<?php echo HTMLHelper::_('grid.id', $i, intval($user->userid)) ?>
                                    </td>
                                    <td>
										<span class="editlinktip hasTip  kwho-<?php echo $user->getType(0, true); ?>"
                                              title="<?php echo $this->escape($user->username); ?> ">
											<?php echo $user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb'); ?>
											<a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>"
                                               title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo Text::sprintf('COM_USERS_EDIT_USER', $this->escape($user->name)); ?>">
											<span class="fa fa-pen-square mr-2"
                                                  aria-hidden="true"></span><?php echo $this->escape($user->username); ?></a>
											<small>
												(<?php echo Text::sprintf('COM_KUNENA_LABEL_USER_NAME', $this->escape($user->name)); ?>
												)
											</small>
										</span>
                                    </td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>"
                                           title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($user->name); ?>">
											<?php echo $this->escape($user->email); ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>"
                                           title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($user->name); ?>">
											<?php echo $this->escape($user->ip); ?></a>
                                    </td>
                                    <td class="hidden-phone hidden-tablet">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>"
                                           title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($user->name); ?>">
											<?php echo $this->escape($user->getRank(0, 'title')); ?></a>
                                    </td>
                                    <td class="center hidden-phone hidden-tablet">
										<span class="editlinktip <?php echo $user->signature ? 'hasTip' : ''; ?>"
                                              title="<?php echo $this->escape($user->signature); ?> ">
											<?php
											if ($user->signature)
											{
												?>
                                                <a href="#edit"
                                                   onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','edit')"><?php echo Text::_('COM_KUNENA_YES'); ?></a>
											<?php }
											else
											{
												?>
                                                <a href="#edit"
                                                   onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','edit')"><?php echo Text::_('COM_KUNENA_NO'); ?></a>
											<?php } ?>
										</span>
                                    </td>
                                    <td class="center hidden-phone">
                                        <a class="btn btn-micro <?php echo !$user->isBlocked() ? 'active' : ''; ?>"
                                           href="javascript: void(0);"
                                           onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $userBlockTask ?>')">
											<?php echo !$user->isBlocked() ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
                                    <td class="center hidden-phone">
                                        <a class="btn btn-micro <?php echo $user->isBanned() ? 'active' : ''; ?>"
                                           href="javascript: void(0);"
                                           onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $userBannedTask ?>')">
											<?php echo $user->isBanned() ? $img_yes : $img_no; ?>
                                        </a>
                                    </td>
                                    <td class="center hidden-phone hidden-tablet">
										<?php if ($user->moderator)
											:
											?>
                                            <a class="btn btn-micro active"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $userModerateTask ?>')">
												<?php echo $img_yes; ?>
                                            </a>
										<?php else

											:
											?>
                                            <a class="btn btn-micro active"
                                               href="javascript: void(0);"
                                               onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $userModerateTask ?>')">
												<?php echo $img_no; ?>
                                            </a>
										<?php endif; ?>
                                    </td>
                                    <td class="center"><?php echo $this->escape($user->userid); ?></td>
                                </tr>
								<?php $i++;
							endforeach;
						else                        :
							?>
                            <tr>
                                <td colspan="10">
                                    <div class="card card-block bg-faded p-2 center filter-state">
											<span><?php echo Text::_('COM_KUNENA_FILTERACTIVE'); ?>
												<button class="btn btn-outline-primary" type="button"
                                                        onclick="document.getElements('.filter').set('value', '');this.form.submit();"><?php echo Text::_('COM_KUNENA_FIELD_LABEL_FILTERCLEAR'); ?></button>
											</span>
                                    </div>
                                </td>
                            </tr>
						<?php endif; ?>
                        </tbody>
                    </table>
					<?php echo $this->loadTemplate('subscribecatsusers'); ?>
					<?php echo $this->loadTemplate('moderators'); ?>
					
					<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
