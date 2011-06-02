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
<form enctype="multipart/form-data" name="categoryform" method="post" id="categoryform" class="categoryform form-validate" action="#">
	<div class="ksection">
		<h2 class="kheader"><a rel="ksection-categories" title="Manage Categories">Manage Categories</a></h2>
			<table class="kcategories" id="kcategories-manager">
				<thead>
					<tr>
						<th class="ktable-firstcol"><a title="Sort" href="#">Name</a></th>
						<th><a title="Sort" href="#">ID</a></th>
						<th><a title="Sort" href="#">Order</a></th>
						<th><a title="Sort" href="#">Locked</a></th>
						<th><a title="Sort" href="#">Moderated</a></th>
						<th><a title="Sort" href="#">Review</a></th>
						<th><a title="Sort" href="#">Anonymous</a></th>
						<th><a title="Sort" href="#">Polls</a></th>
						<th><a title="Sort" href="#">Published</a></th>
						<th><a title="Sort" href="#">Public Access</a></th>
						<th><a title="Sort" href="#">Admin Access</a></th>
						<th><a title="Sort" href="#">Checked Out</a></th>
						<th class="kcategory-checkbox">
							<input type="checkbox" class="kcatmanage-checkall" name="" value="0" />
						</th>
					</tr>
				</thead>
				<tr class="krow-odd">
					<td class="ktable-firstcol"><a title="View Category Title" href="#">Category Title</a></td>
					<td>44</td>
					<td>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderup')" title="Move Up">   <img src="images/arrow-up.png" alt="Move Up" /></a></span>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderdown')" title="Move Down">  <img src="images/arrow-down.png" alt="Move Down" /></a></span>
						<input type="text" name="order[71]" size="5" value="53" class="hasTip" title="Order :: Category Order" />
					</td>
					<td><img src="images/icon-locked.png" alt="Locked"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td>Public</td>
					<td>Super Administrator</td>
					<td>severdia</td>
					<td class="kcategory-checkbox">
						<input type="checkbox" class="kcatmanage-checkbox" name="" value="0" />
					</td>
				</tr>
				<tr class="krow-even">
					<td class="ktable-firstcol">|— <a title="View Category Title" href="#">Category Title</a></td>
					<td>44</td>
					<td>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderup')" title="Move Up">   <img src="images/arrow-up.png" alt="Move Up" /></a></span>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderdown')" title="Move Down">  <img src="images/arrow-down.png" alt="Move Down" /></a></span>
						<input type="text" name="order[71]" size="5" value="54" class="hasTip" title="Order :: Category Order" />
					</td>
					<td>&nbsp;</td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td>Public</td>
					<td>Super Administrator</td>
					<td>severdia</td>
					<td class="kcategory-checkbox">
						<input type="checkbox" class="kcatmanage-checkbox" name="" value="0" />
					</td>
				</tr>
				<tr class="krow-odd">
					<td class="ktable-firstcol">|— <a title="View Category Title" href="#">Category Title</a></td>
					<td>44</td>
					<td>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderup')" title="Move Up">   <img src="images/arrow-up.png" alt="Move Up" /></a></span>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderdown')" title="Move Down">  <img src="images/arrow-down.png" alt="Move Down" /></a></span>
						<input type="text" name="order[71]" size="5" value="55" class="hasTip" title="Order :: Category Order" />
					</td>
					<td><img src="images/icon-locked.png" alt="Locked"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/unpublish.png" alt="Active"></td>
					<td>Public</td>
					<td>Super Administrator</td>
					<td>severdia</td>
					<td class="kcategory-checkbox">
						<input type="checkbox" class="kcatmanage-checkbox" name="" value="0" />
					</td>
				</tr>
				<tr class="krow-even">
					<td class="ktable-firstcol">|— |— <a title="View Category Title" href="#">Category Title</a></td>
					<td>44</td>
					<td>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderup')" title="Move Up">   <img src="images/arrow-up.png" alt="Move Up" /></a></span>
						<span><a href="#reorder" onclick="return listItemTask('cb14','orderdown')" title="Move Down">  <img src="images/arrow-down.png" alt="Move Down" /></a></span>
						<input type="text" name="order[71]" size="5" value="57" class="hasTip" title="Order :: Category Order" />
					</td>
					<td>&nbsp;</td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td><img src="images/publish.png" alt="Active"></td>
					<td>Public</td>
					<td>Super Administrator</td>
					<td>severdia</td>
					<td class="kcategory-checkbox">
						<input type="checkbox" class="kcatmanage-checkbox" name="" value="0" />
					</td>
				</tr>
			</table>
		<div class="kpost-buttons">
			<button class="kbutton" type="submit" title="Click here to create a new category"> New Category </button>
		</div>
	</div>

	<div id="ksection-modbox">
		<select name="do" id="kmoderate-select" class="kinputbox" size="1">
			<option value="SelectOption">Select batch option...</option>
			<option value="PublishSelected">Publish</option>
			<option value="UnpublishSelected">Unpublish</option>
			<option value="DeleteSelected">Delete</option>
			<option value="LockSelected">Lock</option>
			<option value="LockSelected">Make Anonymous</option>
			<option value="LockSelected">Make Public</option>
			<option value="LockSelected">Allow Polls</option>
			<option value="LockSelected">Disallow Polls</option>
		</select>
		<button>Submit</button>

	</div>
</form>