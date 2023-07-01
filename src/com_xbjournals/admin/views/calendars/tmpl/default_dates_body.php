<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/calendars/tmpl/default_dates_body.php
 * @version 0.0.7.0 30th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

//use Joomla\CMS\Layout\LayoutHelper;


?>
<div class="container-fluid">

	<div class="row-fluid">

		<div class="control-group span6">
			<div class="controls">
				<label for="startdate">First Date:</label>
				<input type="date" id="startdate" name="startdate" />
				<?php //echo LayoutHelper::render('startdate', array()); ?>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<label for="enddate">Last Date:</label>
				<input type="date" id="enddate" name="enddate" />
				<?php //echo LayoutHelper::render('enddate', array()); ?>
			</div>
		</div>
	</div>
</div>
