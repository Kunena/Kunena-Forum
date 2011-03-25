<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<form enctype="multipart/form-data" class="form-validate" name="kuserform" method="post" action="#">
		<div class="kuserprofile">
			<h2 class="kheader"><a href="#" title="Profile for severdia" rel="ksection-detailsbox">Profile: severdia</a></h2>
			<div class="kprofilebox" id="ksection-detailsbox">
				<div id="kprofile-leftcol">
					<div class="kavatar-lg">
						<img alt="" src="images/avatar_lg.png" class="kavatar" />
					</div>

				</div>
				<div id="kprofile-rightcol">

					<div id="kprofile-stats" class="clearfix">
						<ul>
							<li><span class="kicon-button kbuttononline-yes"><span class="online-yes"><span>NOW ONLINE</span></span></span></li>
							<li><strong>Rank: </strong>Fresh Boarder</li>
							<li class="kprofile-rank"><img alt="" src="images/ranks/rank0.gif"></li>
						</ul>
						<ul>
							<li><strong>Register Date:</strong> <span title="1 day, 2 hours ago">Yesterday</span></li>
							<li><strong>Last Visit Date:</strong> <span title="4 hours, 48 minutes ago">Today</span></li>
							<li><strong>Time Zone:</strong> GMT -8:00</li>
							<li><strong>Local Time:</strong> 04:00</li>
						</ul>
						<ul>
							<li><strong>Posts:</strong> 1</li>
							<li><strong>Profile Views:</strong> 0</li>
							<li><a rel="follow" title="" href="#">Private Messages</a></li>
							<li><a title="Click here to send a private message to this user." onclick="joms.messaging.loadComposeWindow('16301')" href="javascript:void(0)"><span title="Click here to send a private message to this user." class="kicon-profile kicon-profile-pm"></span></a></li>
						</ul>
						<ul class="kuserprofile-about">
							<li><strong>About Me:</strong> Est id novum delicatissimi, an petentium consequuntur eam. Ne vix meis nonumy mediocrem, an porro menandri constituam nec, ea vel lucilius quaestio elaboraret.</li>
						</ul>
					</div>

				</div>

				<div class="clr"></div>

				<div class="clrline"></div>

				<div id="kprofile-edit">
					<dl class="tabs">
						<dt class="open">User Account</dt>
						<dd style="display: none;">
							<div class="kblock kedituser">
								<h2 class="kheader"><a rel="kedit-user-information">Edit User Information</a></h2>

								<ul class="kform kedit-user-information clearfix" id="kedit-user-information">
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="username">User Name</label>
										</div>
										<div class="kform-field">
											<input type="text" disabled="disabled" value="test" id="username" name="username" class="kinputbox" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="name">Name</label>
										</div>
										<div class="kform-field">
											<input type="text" size="40" value="Test User 2" name="name" id="name" class="kinputbox required hasTip" title="Name :: Enter your full name" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="email">E-mail</label>
										</div>
										<div class="kform-field">
											<input type="text" size="40" value="test@test.com" name="email" id="email" class="kinputbox required validate-email hasTip" title="E-mail::Enter your E-mail" />
										</div>
									</li>

									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="password">Password</label>
										</div>
										<div class="kform-field">
											<input type="password" size="40" value="" name="password" id="password" class="kinputbox validate-password hasTip" title="Password::Enter your Password" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="password2">Verify Password</label>
										</div>
										<div class="kform-field">
											<input type="password" size="40" value="" name="password2" id="password2" class="kinputbox validate-passverify hasTip" title="Verify Password::Verify your Password" />
										</div>
									</li>
								</ul>
							</div>

							<div class="kblock kedituser">
								<h2 class="kheader"><a rel="kflattable">Global Settings</a></h2>
								<ul class="kform kedit-user-information clearfix">
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label title="Front-end Language::Default Language for the Site Front-end" class="hasTip" for="paramslanguage" id="paramslanguage-lbl">Front-end Language</label>
										</div>
										<div class="kform-field">
											<select class="kinputbox hasTip" id="paramslanguage" name="params[language]" title="Front-end Language::Default Language for the Site Front-end">
												<option selected="selected" value="">- Select Language -</option>
												<option value="en-GB">English (United Kingdom)</option>
											</select>
										</div>
									</li>
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label title="Time Zone::Time Zone for this User" class="hasTip" for="paramstimezone" id="paramstimezone-lbl">Time Zone</label>
										</div>
										<div class="kform-field">
											<select class="kinputbox hasTip" id="paramstimezone" name="params[timezone]" title="Time Zone::Time Zone for this User">
												<option value="-12">(UTC -12:00) International Date Line West</option>
												<option value="-11">(UTC -11:00) Midway Island, Samoa</option>
												<option value="-10">(UTC -10:00) Hawaii</option>
												<option value="-9.5">(UTC -09:30) Taiohae, Marquesas Islands</option>
												<option value="-9">(UTC -09:00) Alaska</option>
												<option value="-8">(UTC -08:00) Pacific Time (US &amp; Canada)</option>
												<option value="-7">(UTC -07:00) Mountain Time (US &amp; Canada)</option>
												<option value="-6">(UTC -06:00) Central Time (US &amp; Canada), Mexico City</option>
												<option value="-5">(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
												<option value="-4">(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
												<option value="-4.5">(UTC -04:30) Venezuela</option>
												<option value="-3.5">(UTC -03:30) St. John's, Newfoundland, Labrador</option>
												<option value="-3">(UTC -03:00) Brazil, Buenos Aires, Georgetown</option>
												<option value="-2">(UTC -02:00) Mid-Atlantic</option>
												<option value="-1">(UTC -01:00) Azores, Cape Verde Islands</option>
												<option selected="selected" value="0">(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca, Reykjavik</option>
												<option value="1">(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris</option>
												<option value="2">(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa</option>
												<option value="3">(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
												<option value="3.5">(UTC +03:30) Tehran</option>
												<option value="4">(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
												<option value="4.5">(UTC +04:30) Kabul</option>
												<option value="5">(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
												<option value="5.5">(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo</option>
												<option value="5.75">(UTC +05:45) Kathmandu</option>
												<option value="6">(UTC +06:00) Almaty, Dhaka</option>
												<option value="6.5">(UTC +06:30) Yagoon</option>
												<option value="7">(UTC +07:00) Bangkok, Hanoi, Jakarta, Phnom Penh</option>
												<option value="8">(UTC +08:00) Beijing, Perth, Singapore, Hong Kong</option>
												<option value="8.75">(UTC +08:00) Ulaanbaatar, Western Australia</option>
												<option value="9">(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
												<option value="9.5">(UTC +09:30) Adelaide, Darwin, Yakutsk</option>
												<option value="10">(UTC +10:00) Eastern Australia, Guam, Vladivostok</option>
												<option value="10.5">(UTC +10:30) Lord Howe Island (Australia)</option>
												<option value="11">(UTC +11:00) Magadan, Solomon Islands, New Caledonia</option>
												<option value="11.5">(UTC +11:30) Norfolk Island</option>
												<option value="12">(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
												<option value="12.75">(UTC +12:45) Chatham Island</option>
												<option value="13">(UTC +13:00) Tonga</option>
												<option value="14">(UTC +14:00) Kiribati</option>
											</select>
										</div>
									</li>
								</ul>

							</div>
						</dd>
						<dt class="closed">Profile Information</dt>
						<dd style="display: none;">

							<div class="kblock keditprofile">
								<h2 class="kheader"><a rel="kedit-profile-information">Edit Profile Information</a></h2>

								<ul class="kform kedit-user-information clearfix" id="kedit-profile-information">
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="personaltext">Personal Text</label>
										</div>
										<div class="kform-field">
											<input type="text" value="" name="personaltext" id="personaltext" maxlength="50" class="kinputbox hasTip" title="Personal Text::Enter your Personal Text" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="birthdate1">Birthdate</label>
										</div>
										<div class="kform-field">
											<span title="Birthdate::Year (YYYY) - Month (MM) - Day (DD)" class="editlinktip hasTip">
												<input type="text" value="0001" name="birthdate1" id="birthdate1" maxlength="4" size="4" class="kinputbox" />
												<input type="text" value="01" name="birthdate2" id="birthdate2" maxlength="2" size="2" class="kinputbox" />
												<input type="text" value="01" name="birthdate3" id="birthdate3" maxlength="2" size="2" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="location">Location</label>
										</div>
										<div class="kform-field">
											<input type="text" value="" name="location" id="location" class="kinputbox hasTip" title="Location::Enter your Location" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="gender">Gender</label>
										</div>
										<div class="kform-field">
											<select size="1" class="kinputbox hasTip" id="gender" name="gender" title="Gender::Select your Gender">
												<option selected="selected" value="0">Unknown</option>
												<option value="1">Male</option>
												<option value="2">Female</option>
											</select>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="websitename">Web site Name</label>
										</div>
										<div class="kform-field">
											<span title="Web site Name::Example: Kunena" class="editlinktip hasTip">
												<input type="text" value="" name="websitename" id="websitename" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="websiteurl">Web site URL</label>
										</div>
										<div class="kform-field">
											<span title="Web site URL::Example: www.Kunena.com" class="editlinktip hasTip">
												<input type="text" value="" name="websiteurl" id="websiteurl" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="twitter">Twitter</label>
										</div>
										<div class="kform-field">
											<span title="Twitter::This is your Twitter username." class="editlinktip hasTip">
												<input type="text" value="" name="twitter" id="twitter" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="facebook">Facebook</label>
										</div>
										<div class="kform-field">
											<span title="Facebook::This is your Facebook username." class="editlinktip hasTip">
												<input type="text" value="" name="facebook" id="facebook" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="myspace">MySpace</label>
										</div>
										<div class="kform-field">
											<span title="MySpace::This is your MySpace username." class="editlinktip hasTip">
												<input type="text" value="" name="myspace" id="myspace" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="skype">SKYPE</label>
										</div>
										<div class="kform-field">
											<span title="SKYPE::This is your Skype handle." class="editlinktip hasTip">
												<input type="text" value="" name="skype" id="skype" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="linkedin">Linkedin</label>
										</div>
										<div class="kform-field">
											<span title="Linkedin::This is your LinkedIn username." class="editlinktip hasTip">
												<input type="text" value="" name="linkedin" id="linkedin" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="delicious">Delicious</label>
										</div>
										<div class="kform-field">
											<span title="Delicious::This is your Delicious username." class="editlinktip hasTip">
												<input type="text" value="" name="delicious" id="delicious" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="friendfeed">FriendFeed</label>
										</div>
										<div class="kform-field">
											<span title="FriendFeed::This is your FriendFeed username." class="editlinktip hasTip">
												<input type="text" value="" name="friendfeed" id="friendfeed" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="digg">Digg</label>
										</div>
										<div class="kform-field">
											<span title="Digg::This is your Digg username." class="editlinktip hasTip">
												<input type="text" value="" name="digg" id="digg" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="yim">YIM</label>
										</div>
										<div class="kform-field">
											<span title="YIM::This is your Yahoo! Instant Messenger nickname." class="editlinktip hasTip">
												<input type="text" value="" name="yim" id="yim" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="aim">AIM</label>
										</div>
										<div class="kform-field">
											<span title="AIM::This is your AOL Instant Messenger nickname." class="editlinktip hasTip">
												<input type="text" value="" name="aim" id="aim" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="gtalk">GTALK</label>
										</div>
										<div class="kform-field">
											<span title="GTALK::This is your Gtalk nickname." class="editlinktip hasTip">
												<input type="text" value="" name="gtalk" id="gtalk" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="icq">ICQ</label>
										</div>
										<div class="kform-field">
											<span title="ICQ::This is your ICQ number." class="editlinktip hasTip">
												<input type="text" value="" name="icq" id="icq" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="msn">MSN</label>
										</div>
										<div class="kform-field">
											<span title="MSN::Your MSN messenger e-mail address." class="editlinktip hasTip">
												<input type="text" value="" name="msn" id="msn" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="blogspot">Blogger</label>
										</div>
										<div class="kform-field">
											<span title="Blogger::This is your Blogger username." class="editlinktip hasTip">
												<input type="text" value="" name="blogspot" id="blogspot" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="flickr">Flickr</label>
										</div>
										<div class="kform-field">
											<span title="Flickr::This is your Flickr username." class="editlinktip hasTip">
												<input type="text" value="" name="flickr" id="flickr" class="kinputbox" />
											</span>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="bebo">Bebo</label>
										</div>
										<div class="kform-field">
											<span title="Bebo::This is your Bebo member ID." class="editlinktip hasTip">
												<input type="text" value="" name="bebo" id="bebo" class="kinputbox" />
											</span>
										</div>
									</li>

									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="kbbcode-message">Signature</label>
										</div>
										<div class="kform-field">
											<span title="Signature::You can use smileys and BBCodes in your signature" class="editlinktip hasTip">
												<textarea cols="30" rows="10" id="kbbcode-message" name="signature" class="ktxtarea required"></textarea>
											</span>
										</div>
									</li>
								</ul>
							</div>
						</dd>
						<dt class="closed">Avatar Image</dt>
						<dd style="display: none;">

							<div class="kblock keditavatar">
								<h2 class="kheader"><a rel="kchange-avatar-image">Change Avatar Image</a></h2>

								<ul class="kform kedit-user-information clearfix" id="kchange-avatar-image">
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="kavatar-upload">Upload New Avatar</label>
										</div>
										<div class="kform-field">
											<input type="file" name="avatarfile" class="button hasTip" id="kavatar-upload" title="Allowed file extensions::jpg,jpeg,gif,png,zip,txt,doc,gz,tgz" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="avatar_category_select">Select Avatar From Gallery</label>
										</div>
										<div class="kform-field">
											<select onchange="switch_avatar_category(this.options[this.selectedIndex].value)" id="avatar_category_select" name="&nbsp;gallery" class="hasTip" title="Select Avatar From Gallery::Change Gallery">
												<option selected="selected" value="default">Default Gallery</option>
											</select>
											<div class="clr"></div>
											<script type="text/javascript"><!--
												function switch_avatar_category(gallery){
													if (gallery == "")
														return;
													var url = "";
													var urlreg = new  RegExp("_GALLERY_","g");
													location.href=url.replace(urlreg,gallery);
												}
											// --></script>

											<span>
												<label for="kavatar0"><img alt="" src="images/avatars/airplane.gif" /></label>
												<input type="radio" value="gallery/airplane.gif" name="avatar" id="kavatar0">
											</span>
											<span>
												<label for="kavatar1"><img alt="" src="images/avatars/ball.gif"></label>
												<input type="radio" value="gallery/ball.gif" name="avatar" id="kavatar1">
											</span>
											<span>
												<label for="kavatar2"><img alt="" src="images/avatars/butterfly.gif"></label>
												<input type="radio" value="gallery/butterfly.gif" name="avatar" id="kavatar2">
											</span>
											<span>
												<label for="kavatar3"><img alt="" src="images/avatars/car.gif"></label>
												<input type="radio" value="gallery/car.gif" name="avatar" id="kavatar3">
											</span>
											<span>
												<label for="kavatar4"><img alt="" src="images/avatars/dog.gif"></label>
												<input type="radio" value="gallery/dog.gif" name="avatar" id="kavatar4">
											</span>
											<span>
												<label for="kavatar5"><img alt="" src="images/avatars/duck.gif"></label>
												<input type="radio" value="gallery/duck.gif" name="avatar" id="kavatar5">
											</span>
											<span>
												<label for="kavatar6"><img alt="" src="images/avatars/fish.gif"></label>
												<input type="radio" value="gallery/fish.gif" name="avatar" id="kavatar6">
											</span>
											<span>
												<label for="kavatar7"><img alt="" src="images/avatars/frog.gif"></label>
												<input type="radio" value="gallery/frog.gif" name="avatar" id="kavatar7">
											</span>
											<span>
												<label for="kavatar8"><img alt="" src="images/avatars/guitar.gif"></label>
												<input type="radio" value="gallery/guitar.gif" name="avatar" id="kavatar8">
											</span>
											<span>
												<label for="kavatar9"><img alt="" src="images/avatars/kick.gif"></label>
												<input type="radio" value="gallery/kick.gif" name="avatar" id="kavatar9">
											</span>
											<span>
												<label for="kavatar10"><img alt="" src="images/avatars/pinkflower.gif"></label>
												<input type="radio" value="gallery/pinkflower.gif" name="avatar" id="kavatar10">
											</span>
											<span>
												<label for="kavatar11"><img alt="" src="images/avatars/redflower.gif"></label>
												<input type="radio" value="gallery/redflower.gif" name="avatar" id="kavatar11">
											</span>
											<span>
												<label for="kavatar12"><img alt="" src="images/avatars/skater.gif"></label>
												<input type="radio" value="gallery/skater.gif" name="avatar" id="kavatar12">
											</span>
										</div>
									</li>
								</ul>
							</div>
						</dd>
						<dt class="closed">Forum Settings</dt>
						<dd style="display: none;">

							<div class="kblock keditsettings">
								<h2 class="kheader"><a rel="kedit-user-forum-settings">Edit Forum Settings</a></h2>

								<ul class="kform kedit-user-information clearfix" id="kedit-user-forum-settings">
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="messageordering">Preferred Message Ordering</label>
										</div>
										<div class="kform-field">
											<select size="1" class="kinputbox hasTip" id="messageordering" name="messageordering" title="Preferred Message Ordering::Select Preferred Message Ordering">
												<option selected="selected" value="0">Default</option>
												<option value="2">First post first</option>
												<option value="1">Last post first</option>
											</select>
										</div>
									</li>
									<li class="kedit-user-information-row krow-even">
										<div class="kform-label">
											<label for="hidemail">Hide E-mail</label>
										</div>
										<div class="kform-field">
											<select size="1" class="kinputbox hasTip" id="hidemail" name="hidemail" title="Hide E-mail::Select 'Yes' if you want to hide e-mail">
												<option value="0">No</option>
												<option selected="selected" value="1">Yes</option>
											</select>
										</div>
									</li>
									<li class="kedit-user-information-row krow-odd">
										<div class="kform-label">
											<label for="showonline">Show Online</label>
										</div>
										<div class="kform-field">
											<select size="1" class="kinputbox hasTip" id="showonline" name="showonline" title="Show Online::Select 'Yes' if you want to show online">
												<option value="0">No</option>
												<option selected="selected" value="1">Yes</option>
											</select>
										</div>
									</li>
								</ul>
							</div>
						</dd>
					</dl>

					<div class="kpost-buttons">
						<button title="Click here to save" type="submit" class="kbutton"> Save </button>
						<button onclick="javascript:window.history.back();" title="Click here to cancel" type="button" class="kbutton"> Cancel </button>
					</div>

				</div>

			</div>
			<div class="clr"></div>
		</div>

	</form>