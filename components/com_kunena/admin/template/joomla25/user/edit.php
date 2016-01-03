<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Users
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewUser $this */

JHtml::addIncludePath(KPATH_ADMIN.'/libraries/html/html');
$db = JFactory::getDBO();
$document = JFactory::getDocument();
$document->addScriptDeclaration(' var current_count = '.JString::strlen($this->user->signature).'
var max_count = '.(int) $this->config->maxsig.'

function textCounter(field, target) {
	if (field.value.length > max_count) {
		field.value = field.value.substring(0, max_count);
	} else {
		current_count = max_count - field.value.length;
		target.value = current_count;
	}
}');

$paneOptions = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
}',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="1" />
						<input type="hidden" name="uid" value="<?php echo $this->user->userid; ?>" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<article class="data-block">
							<div class="data-container">
								<div class="tabbable">
									<ul class="nav nav-tabs">
										<li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS'); ?></a></li>
										<?php /*
											<li><a href="#tab2" data-toggle="tab"><?php echo JText::_('User Info'); ?></a></li>
					*/ ?>
										<li><a href="#tab3" data-toggle="tab"><?php echo JText::_('COM_KUNENA_MOD_NEW'); ?></a></li>
										<li><a href="#tab4" data-toggle="tab"><?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS'); ?></a></li>
										<li><a href="#tab5" data-toggle="tab"><?php echo JText::_('COM_KUNENA_TOPIC_SUBSCRIPTIONS'); ?></a></li>
										<li><a href="#tab6" data-toggle="tab"><?php echo JText::_('COM_KUNENA_TRASH_IP'); ?></a></li>
										<li><a href="#tab7" data-toggle="tab"><?php echo JText::_('COM_KUNENA_USER_LABEL_FORUM_SETTINGS'); ?></a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="tab1">
											<fieldset>
												<legend><?php echo JText::_('COM_KUNENA_UAVATAR'); ?></legend>
												<div><?php echo $this->avatar; ?></div>
												<?php if ($this->editavatar) : ?>
													<div>
														<label><input type="checkbox" value="1" name="deleteAvatar" /> <?php echo JText::_('COM_KUNENA_DELAV'); ?></label>
													</div>
												<?php endif; ?>
											</fieldset>
											<fieldset>
												<legend><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?>:</legend>
												<div>
													<textarea class="input-xxlarge" name="signature" cols="4" rows="6"
															  onkeyup="textCounter(this, this.form.current_count);"><?php echo $this->escape( $this->user->signature ); ?></textarea>
												</div>
												<div>
													<label><input type="checkbox" value="1" name="deleteSig" /> <?php echo JText::_('COM_KUNENA_DELSIG'); ?></label>
												</div>
												<div>
													<?php echo JText::sprintf('COM_KUNENA_SIGNATURE_LENGTH_COUNTER', intval($this->config->maxsig),
														'<input class="span1" readonly="readonly" type="text" name="current_count" value="'.(intval($this->config->maxsig)-JString::strlen($this->user->signature)).'" />');?>
												</div>
											</fieldset>
										</div>
										<?php /*
												<div class="tab-pane" id="tab2">
													<fieldset>
													<table class="table table-striped">
														<tr>
															<td>Personal Text</td>
															<td><input type="text" maxlength="50" name="personaltext" value="" /></td>
														</tr>
														<tr>
															<td>Birthdate</td>
															<td>
																<span class="editlinktip hasTip" title="Birthdate::Year (YYYY) - Month (MM) - Day (DD)" >
																	<input type="text" size="4" maxlength="4" class="input-mini" name="birthdate1" value="0001" />
																	<input type="text" size="2" maxlength="2" class="input-mini" name="birthdate2" value="01" />
																	<input type="text" size="2" maxlength="2" class="input-mini" name="birthdate3" value="01" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Location</td>
															<td><input type="text" name="location" value="" /></td>
														</tr>
														<tr>
															<td>Gender</td>
															<td>
																<select id="gender" name="gender" class="inputbox" size="1">
																	<option value="0" selected="selected">Unknown</option>
																	<option value="1">Male</option>
																	<option value="2">Female</option>
																</select>
															</td>
														</tr>
														<tr>
															<td>Web site Name</td>
															<td>
																<span class="editlinktip hasTip" title="Web site Name::Example: Kunena" >
																	<input type="text" name="websitename" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Web site URL</td>
															<td>
																<span class="editlinktip hasTip" title="Web site URL::Example: www.kunena.org" >
																	<input type="text" name="websiteurl" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Twitter</td>
															<td>
																<span class="editlinktip hasTip" title="Twitter::This is your Twitter username." >
																	<input type="text" name="twitter" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Facebook</td>
															<td>
																<span class="editlinktip hasTip" title="Facebook::This is your Facebook username." >
																	<input type="text" name="facebook" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>MySpace</td>
															<td>
																<span class="editlinktip hasTip" title="MySpace::This is your MySpace username." >
																	<input type="text" name="myspace" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>SKYPE</td>
															<td>
																<span class="editlinktip hasTip" title="SKYPE::This is your Skype handle." >
																	<input type="text" name="skype" value="jelle810" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Linkedin</td>
															<td>
																<span class="editlinktip hasTip" title="Linkedin::This is your LinkedIn username." >
																	<input type="text" name="linkedin" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Delicious</td>
															<td>
																<span class="editlinktip hasTip" title="Delicious::This is your Delicious username." >
																	<input type="text" name="delicious" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>FriendFeed</td>
															<td>
																<span class="editlinktip hasTip" title="FriendFeed::This is your FriendFeed username." >
																	<input type="text" name="friendfeed" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Digg</td>
															<td>
																<span class="editlinktip hasTip" title="Digg::This is your Digg username." >
																	<input type="text" name="digg" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>YIM</td>
															<td>
																<span class="editlinktip hasTip" title="YIM::This is your Yahoo! Instant Messenger nickname." >
																	<input type="text" name="yim" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>AIM</td>
															<td>
																<span class="editlinktip hasTip" title="AIM::This is your AOL Instant Messenger nickname." >
																	<input type="text" name="aim" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>GTALK</td>
															<td>
																<span class="editlinktip hasTip" title="GTALK::This is your Gtalk nickname." >
																	<input type="text" name="gtalk" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>ICQ</td>
															<td>
																<span class="editlinktip hasTip" title="ICQ::This is your ICQ number." >
																	<input type="text" name="icq" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>MSN</td>
															<td>
																<span class="editlinktip hasTip" title="MSN::Your MSN messenger e-mail address." >
																	<input type="text" name="msn" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Blogger</td>
															<td>
																<span class="editlinktip hasTip" title="Blogger::This is your Blogger username." >
																	<input type="text" name="blogspot" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Flickr</td>
															<td>
																<span class="editlinktip hasTip" title="Flickr::This is your Flickr username." >
																	<input type="text" name="flickr" value="" />
																</span>
															</td>
														</tr>
														<tr>
															<td>Bebo</td>
															<td>
																<span class="editlinktip hasTip" title="Bebo::This is your Bebo member ID." >
																	<input type="text" name="bebo" value="" />
																</span>
															</td>
														</tr>
												</table>
											</fieldset>
											</div>
						*/ ?>
										<div class="tab-pane" id="tab3">
											<fieldset>
												<legend><?php echo JText::_('COM_KUNENA_MODCHANGE'); ?></legend>
												<table class="table table-striped">
													<tr>
														<td width="20%"><?php echo JText::_('COM_KUNENA_ISMOD'); ?></td>
														<td><?php echo JText::_('COM_KUNENA_MODCATS'); ?></td>
													</tr>
													<tr>
														<td><?php echo $this->selectMod; ?></td>
														<td><?php echo $this->modCats; ?></td>
													</tr>
												</table>
											</fieldset>
										</div>

										<div class="tab-pane" id="tab4">
											<fieldset>
												<legend><?php echo JText::_('COM_KUNENA_SUBFOR') . ' ' . $this->escape($this->user->username); ?></legend>
												<table class="table table-striped">
													<thead>
													<tr>
														<?php /*
																<th width="1%" class="hidden-phone">
																	<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(<?php echo count($this->categories); ?>);" />
																</th>
						*/ ?>
														<th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
														<th width="1%"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
													</tr>
													</thead>
													<?php
													if (!empty($this->subscatslist)) : foreach($this->subscatslist as $cat) :
														?>
														<tr>
															<td><?php echo $this->escape($cat->name); ?> <small><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($cat->alias)); ?></small></td>
															<td><?php echo $this->escape($cat->id); ?></td>
														</tr>
													<?php endforeach; else : ?>
														<tr>
															<td><?php echo JText::_('COM_KUNENA_NOCATSUBS'); ?></td>
														</tr>
													<?php endif; ?>
												</table>
											</fieldset>
										</div>

										<div class="tab-pane" id="tab5">
											<fieldset>
												<legend><?php echo JText::_('COM_KUNENA_SUBFOR') . ' ' . $this->escape($this->user->username); ?></legend>
												<table class="table table-striped">
													<thead>
													<tr>
														<?php /*
																<th width="1%" class="hidden-phone">
																	<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(<?php echo count($this->categories); ?>);" />
																</th>
						*/ ?>
														<th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
														<th width="1%"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
													</tr>
													</thead>

													<?php
													if ($this->sub) : foreach ( $this->sub as $topic ) :
														?>
														<tr>
															<td><?php echo $this->escape($topic->subject); ?></td>
															<td><?php echo $this->escape($topic->id); ?></td>
														</tr>
													<?php endforeach; else : ?>
														<tr>
															<td><?php echo JText::_('COM_KUNENA_NOSUBS'); ?></td>
														</tr>
													<?php endif; ?>
												</table>
											</fieldset>
										</div>

										<div class="tab-pane" id="tab6">
											<fieldset>
												<legend><?php echo JText::sprintf('COM_KUNENA_IPFOR', $this->escape($this->user->username)); ?></legend>
												<table class="table table-striped">
													<?php
													$i=0; foreach ($this->ipslist as $ip => $list) :
														$userlist = array();
														$mescnt = 0;
														foreach ($list as $curuser) {
															if ($curuser->userid == $this->user->userid) {
																$mescnt += intval($curuser->mescnt);
																continue;
															}
															$userlist[] = $this->escape($curuser->username).' ('.$this->escape($curuser->mescnt).')';
														}
														$userlist = implode(', ', $userlist);
														?>
														<tr>
															<td width="30"><?php echo ++$i; ?></td>
															<td width="60"><strong><?php echo $this->escape($ip); ?></strong></td>
															<td>(<?php echo JText::sprintf('COM_KUNENA_IP_OCCURENCES', $mescnt).(!empty($userlist)?" ".JText::sprintf('COM_KUNENA_USERIDUSED', $this->escape($userlist)):''); ?>)</td>
														</tr>
													<?php endforeach; ?>
												</table>
											</fieldset>
										</div>

										<div class="tab-pane" id="tab7">
											<fieldset>
												<table class="table table-striped">
													<tr>
														<td width="20%"><?php echo JText::_('COM_KUNENA_PREFOR'); ?></td>
														<td><?php echo $this->selectOrder; ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_RANKS'); ?></td>
														<td><?php echo $this->selectRank; ?></td>
													</tr>
												</table>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</article>
					</form>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML (); ?>
				</div>
			</div>
		</div>
	</div>
</div>
