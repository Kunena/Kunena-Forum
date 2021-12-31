<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

HTMLHelper::_('bootstrap.framework');

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates'); ?>"
                      method="post" id="adminForm"
                      name="adminForm">
                    <input type="hidden" name="task" value=""/>
                    <input type="hidden" name="templatename" value="<?php echo $this->escape($this->templatename); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 style="text-transform: capitalize;"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE'); ?>
                                    : <?php echo $this->escape($this->details->name); ?></h1>
                                <div class="tabbable-panel">
                                    <div class="tabbable-line">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="tab_info-tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab_info" type="button" role="tab"
                                                        aria-controls="tab_info"
                                                        aria-selected="true"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_INFO'); ?></button>
                                            </li>
											<?php foreach ($this->form->getFieldsets() as $fieldset)
												:
												?>
												<?php if ($fieldset->name != 'template')
												:
												?>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link"
                                                            id="tab_<?php echo strtolower($fieldset->name); ?>-tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#tab_<?php echo strtolower($fieldset->name); ?>"
                                                            type="button" role="tab"
                                                            aria-controls="tab_<?php echo strtolower(Text::_($fieldset->name)); ?>"
                                                            aria-selected="true"><?php echo Text::_($fieldset->name); ?></button>
                                                </li>
											<?php endif; ?>
											<?php endforeach; ?>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="tab_info" role="tabpanel"
                                                 aria-labelledby="tab_info-tab">
                                                <table class="table table-bordered table-striped">
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?>
                                                            :
                                                        </td>
                                                        <td>
                                                            <strong><?php echo Text::_($this->details->author); ?></strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?>
                                                            :
                                                        </td>
                                                        <td><?php echo Text::_($this->details->version); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?>
                                                            :
                                                        </td>
                                                        <td><?php echo Text::_($this->details->creationdate); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DESCRIPTION'); ?>
                                                            :
                                                        </td>
                                                        <td><?php $path = KPATH_SITE . '/template/' . $this->templatename . '/assets/images/template_thumbnail.png';

															if (is_file($path))
																:
																?>
                                                                <div>
                                                                    <img loading=lazy
                                                                         src="<?php echo Uri::root(true); ?>/components/com_kunena/template/<?php echo $this->escape($this->templatename); ?>/assets/images/template_thumbnail.png"
                                                                         alt="<?php echo $this->escape($this->templatename); ?>"/>
                                                                </div>
															<?php endif; ?>
                                                            <div><?php echo Text::_($this->details->description); ?></div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

											<?php foreach ($this->form->getFieldsets() as $fieldset)
												:
												?>
												<?php if ($fieldset->name != 'template')
												:
												?>
                                                <div class="tab-pane fade show"
                                                     id="tab_<?php echo strtolower($fieldset->name); ?>"
                                                     role="tabpanel"
                                                     aria-labelledby="tab_<?php echo strtolower(Text::_($fieldset->name)); ?>-tab">
                                                    <table class="table table-bordered table-striped">
														<?php foreach ($this->form->getFieldset($fieldset->name) as $field)
															:
															?>
															<?php if ($field->hidden)
															:
															?>
                                                            <tr style="display: none">
                                                                <td class="paramlist_key"><?php echo $field->label; ?></td>
                                                                <td class="paramlist_value"><?php echo $field->input; ?></td>
                                                            </tr>
														<?php else

															:
															?>
                                                            <tr>
                                                                <td width="40%"
                                                                    class="paramlist_key"><?php echo $field->label; ?></td>
                                                                <td class="paramlist_value"><?php echo $field->input; ?></td>
                                                            </tr>
														<?php endif; ?>
														<?php endforeach; ?>
                                                    </table>
                                                </div>
											<?php endif; ?>
											<?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
            <div class="pull-right small">
				<?php echo KunenaVersion::getLongVersionHTML(); ?>
            </div>
        </div>
    </div>
</div>
