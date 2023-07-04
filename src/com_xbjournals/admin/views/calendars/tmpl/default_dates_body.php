<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/calendars/tmpl/default_dates_body.php
 * @version 0.0.7.2 4th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

?>
<div class="container-fluid">

	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
							<?php // echo LayoutHelper::render('joomla.form.field.calendar', array('showtime' => 'false', 'readonly' =>'false', 'disabled'=>'false', 'required'=>'false'),'value'=>'', ); ?>
			
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
	<div class="row-fluid">
		<div class="control-group span8">
			<div class="controls">
				<label for="dateprop">Date to filter by</label>
				<select name="dateprop" id="dateprop">
					<option value="DTSTART">DTSTART</option>
					<option value="CREATED">CREATED</option>
					<option value="LAST-MODIFIED">LAST-MODIFIED</option>
					<option value="DTSTAMP">DTSTAMP</option>
				</select>				
			</div>
		</div>
	</div>
	<div id="waiting" class="xbbox alert-info" style="display:none;">
      <table style="width:100%">
          <tr>
              <td style="width:200px;"><img src="/media/com_xbjournals/images/waiting.gif" style="height:100px" /> </td>
              <td style="vertical-align:middle;"><b><?php echo Text::_('XBJOURNALS_WAITING_REPLY'); ?></b> </td>
          </tr>
      </table>
	</div>
</div>
