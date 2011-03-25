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
					<!-- Loop this LI and the module position LI for each post  -->
					<li class="kposts-row krow-odd">
						<ul class="kposthead">
							<li class="kposthead-replytitle"><h3>RE: Reply Topic Title</h3></li>
							<li class="kposthead-postid" ><a href="#" title="Post #2 in this thread">#2</a></li>
							<li class="kposthead-postip">IP: <a href="http://whois.domaintools.com/10.0.1.101" title="View IP details" target="_blank" rel="external">10.0.1.101</a></li>
							<li class="kposthead-posttime">Posted 13 hours, 44 minutes ago</li>
						</ul>
						<ul class="kpost-user-details">
							<li class="kpost-user-username"><a href="#" title="View profile">Username</a></li>
							<li class="kpost-user-avatar"><img src="images/avatar_lg.png" alt="Users avatar"/></li>
							<li class="kpost-user-status">
								<span class="kicon-button kbuttononline-no"><span class="online-no"><span>Offline</span></span></span>
							</li>
							<li class="kpost-user-rank">Expert</li>
							<li class="kpost-user-points">
								<img alt="" src="images/rankadmin.gif">
							</li>
							<li class="kpost-user-posts">Posts: 276</li>
							<li class="kpost-user-icons">
								<span title="Gender: Male" class="kicon-profile kicon-profile-gender-male"></span>
								<a href="http://www.google.com" target="_blank" rel="external"><span title="Web site Name" class="kicon-profile kicon-profile-website"></span></a>
							</li>
							<!-- <li class="kpost-user-perstext">This is my Personal Text.</li> -->
						</ul>
						<div class="kpost-container">
							<ul class="kpost-post-body">
								<li class="kpost-body">
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dignissim eros in turpis aliquam eget gravida purus mollis. Phasellus sollicitudin, felis vitae vehicula faucibus, felis nisi tincidunt turpis, ac eleifend leo tortor quis neque. Nunc in lacus nec felis cursus dignissim quis ac libero. Maecenas et nunc ut ante eleifend bibendum. Fusce at arcu nec ligula pharetra blandit. Etiam enim est, cursus nec laoreet vitae, sagittis quis arcu. Integer vel nisl libero. Donec ullamcorper dui in libero semper at molestie neque varius. Curabitur viverra consectetur sollicitudin. Curabitur cursus orci tincidunt arcu aliquet accumsan. Vivamus lacinia luctus felis sit amet suscipit. Ut risus est, dictum id elementum non, iaculis non sapien. Morbi non purus nibh. Nunc eleifend fringilla commodo. Pellentesque auctor, risus sed molestie facilisis, felis massa vulputate mauris, et gravida mauris eros ac arcu. Praesent posuere, justo eu aliquam hendrerit, nulla nisi facilisis arcu, et posuere leo magna id orci. Sed ut ipsum tristique erat ultrices dignissim. Quisque pellentesque sagittis libero et iaculis. Maecenas dui quam, sagittis id ullamcorper et, venenatis sed leo.
								<br /><br />
								Maecenas lacinia fringilla elit, consequat viverra tellus condimentum vel. Curabitur non lacus sit amet nisl mollis porttitor. Nullam placerat euismod nibh fringilla hendrerit. Vivamus quam turpis, sodales sit amet tincidunt porttitor, fermentum nec velit. Quisque in purus metus. Pellentesque tincidunt risus id sem vulputate pulvinar. Nulla facilisi. Aliquam consequat lacinia blandit. Aliquam sit amet tortor quam. Aliquam nec neque pulvinar sem bibendum rutrum.
								</li>
								<li class="kpost-body-attach">
									<span class="kattach-title">Attachments</span>
									<ul>
										<!-- Loop this LI for each attachment  -->
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example1</a> (132KB)
											</span>
										</li>
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example2</a> (16KB)
											</span>
										</li>
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example3</a> (244KB)
											</span>
										</li>
									</ul>
									<div class="clr"></div>
								</li>
								<li class="kpost-body-lastedit">Last edit: 2 months ago by severdia</li>
								<li class="kpost-body-editreason">Reason: Fixed a bunch of typos</li>
								<li class="kpost-body-sig">This is my signature</li>
							</ul>
							<?php include 'default_row_actions.php'; ?>
							<div class="clr"></div>
						</div>
					</li>
					<li class="kmodules kmodule-1">MODULE POSITION</li>
					<li class="kposts-row krow-even">
						<ul class="kposthead">
							<li class="kposthead-replytitle"><h3>RE: Reply Topic Title</h3></li>
							<li class="kposthead-postid" ><a href="#" title="Post #1 in this thread">#1</a></li>
							<li class="kposthead-postip">IP: <a href="http://whois.domaintools.com/10.0.1.101" title="View IP details" target="_blank" rel="external">10.0.1.101</a></li>
							<li class="kposthead-posttime">Posted 13 hours, 44 minutes ago</li>
						</ul>
						<ul class="kpost-user-details">
							<li class="kpost-user-username"><a href="#" title="View profile">Username</a></li>
							<li class="kpost-user-avatar"><img src="images/avatar_lg.png" alt="Users avatar"/></li>
							<li class="kpost-user-status">
								<span class="kicon-button kbuttononline-yes"><span class="online-yes"><span>Now Online</span></span></span>
							</li>
							<li class="kpost-user-rank">Beginner</li>
							<li class="kpost-user-points"><img alt="" src="images/rank0.gif"></li>
							<li class="kpost-user-posts">Posts: 276</li>
							<li class="kpost-user-icons">
								<span title="Gender: Female" class="kicon-profile kicon-profile-gender-female"></span>
								<a href="http://www.twitter.com" target="_blank" rel="external"><span title="Follow on Twitter" class="kicon-profile kicon-profile-twitter"></span></a>
							</li>
							<!-- <li class="kpost-user-perstext">This is my Personal Text.</li> -->
						</ul>
						<div class="kpost-container">
							<ul class="kpost-post-body">
								<li class="kpost-body">
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dignissim eros in turpis aliquam eget gravida purus mollis. Phasellus sollicitudin, felis vitae vehicula faucibus, felis nisi tincidunt turpis, ac eleifend leo tortor quis neque. Nunc in lacus nec felis cursus dignissim quis ac libero. Maecenas et nunc ut ante eleifend bibendum. Fusce at arcu nec ligula pharetra blandit. Etiam enim est, cursus nec laoreet vitae, sagittis quis arcu. Integer vel nisl libero. Donec ullamcorper dui in libero semper at molestie neque varius. Curabitur viverra consectetur sollicitudin. Curabitur cursus orci tincidunt arcu aliquet accumsan. Vivamus lacinia luctus felis sit amet suscipit. Ut risus est, dictum id elementum non, iaculis non sapien. Morbi non purus nibh. Nunc eleifend fringilla commodo. Pellentesque auctor, risus sed molestie facilisis, felis massa vulputate mauris, et gravida mauris eros ac arcu. Praesent posuere, justo eu aliquam hendrerit, nulla nisi facilisis arcu, et posuere leo magna id orci. Sed ut ipsum tristique erat ultrices dignissim. Quisque pellentesque sagittis libero et iaculis. Maecenas dui quam, sagittis id ullamcorper et, venenatis sed leo.
								<br /><br />
								Maecenas lacinia fringilla elit, consequat viverra tellus condimentum vel. Curabitur non lacus sit amet nisl mollis porttitor. Nullam placerat euismod nibh fringilla hendrerit. Vivamus quam turpis, sodales sit amet tincidunt porttitor, fermentum nec velit. Quisque in purus metus. Pellentesque tincidunt risus id sem vulputate pulvinar. Nulla facilisi. Aliquam consequat lacinia blandit. Aliquam sit amet tortor quam. Aliquam nec neque pulvinar sem bibendum rutrum.
								</li>
								<li class="kpost-body-attach">
									<span class="kattach-title">Attachments</span>
									<ul>
										<!-- Loop this LI for each attachment  -->
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example1</a> (132KB)
											</span>
										</li>
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example2</a> (16KB)
											</span>
										</li>
										<li class="kattach-details">
											<a rel="lightbox[thumb81475]" title="attach-example" href="attach-example.jpg">
												<img alt="attach-example.jpg" src="images/attachment-example.jpg" title="attach-example">
											</a>
											<span>
												<a rel="lightbox[simple81475] nofollow" title="attach-example.jpg" href="attach-example.jpg">attach-example3</a> (244KB)
											</span>
										</li>
									</ul>
									<div class="clr"></div>
								</li>
								<li class="kpost-body-lastedit">Last edit: 2 months ago by severdia</li>
								<li class="kpost-body-editreason">Reason: Fixed a bunch of typos</li>
								<li class="kpost-body-sig">This is my signature</li>
							</ul>
							<?php include 'default_row_actions.php'; ?>
							<div class="clr"></div>
						</div>
					</li>
					<li class="kmodules kmodule-1">MODULE POSITION</li>