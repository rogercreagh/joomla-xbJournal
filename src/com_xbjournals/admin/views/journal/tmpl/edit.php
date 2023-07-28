<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/journal/tmpl/edit.php
 * @version 0.1.2.4 23rd July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

// Factory::getDocument()->addScriptDeclaration('function pleaseWait(targ) {
// 		document.getElementById(targ).style.display = "block";
// 	}');

$doc = Factory::getDocument();
$doc->addScript(Uri::root() . '/media/com_xbjournals/js/showdown.js');

?>
<script >
	function texttohtml() {
    	var descText = document.getElementById('jform_description').value;
    	var converter = new showdown.Converter();
        var descHtml = converter.makeHtml(descText);
      	window.parent.Joomla.editors.instances['jform_html_desc'].setValue(descHtml);
	}
	
	function htmltomd() {
		var html = window.parent.Joomla.editors.instances['jform_html_desc'].getValue();
    	var converter = new showdown.Converter();
        var descMd = converter.makeMarkdown(html);
        document.getElementById('jform_description').value = descMd;
	}
	
	function htmltotext() {
		var descHtml = window.parent.Joomla.editors.instances['jform_html_desc'].getValue();
		var descText = stripHtml(descHtml);
        document.getElementById('jform_description').value = descText;
	}
	
	function clearmd() {
    	var descMd = document.getElementById('jform_description').value;
		var descText = stripHtml(descMd);
        document.getElementById('jform_description').value = descText;
	}
	
    function stripHtml(html) {
	   let doc = new DOMParser().parseFromString(html, 'text/html');
	   return doc.body.textContent || "";
    }

</script>
<style type="text/css" media="screen">
    #jform_url { width: 500px; }   
</style>
<form action="<?php echo JRoute::_('index.php?option=com_xbjournals&view=journal&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <!-- 
    	<div id="waiter" class="xbbox alert-info" style="display:none;">
          <table style="width:100%">
              <tr>
                  <td style="width:200px;"><img src="/media/com_xbjournals/images/waiting.gif" style="height:100px" /> </td>
                  <td style="vertical-align:middle;"><b><?php //echo Text::_('XBJOURNALS_WAITING_REPLY'); ?></b> </td>
              </tr>
          </table>
    	</div>
     -->
 	<div class="row-fluid">
		<div class="span10">
         	<div class="row-fluid">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
        </div>
	</div>
	<div class="row-fluid">
		<div class="span4">
			<?php echo Text::_('Calendar').': '.XbjournalsHelper::getValueFromId('#__xbjournals_calendars',$this->item->calendar_id); ?>
			<?php //echo $this->form->renderField('calendar_id'); ?>
		</div>
		<div class="span4">
			<?php echo Text::_('type').': '.$this->item->entry_type; ?>
		</div>
		<div class="span4">
			<?php echo Text::_('updated').': '.HtmlHelper::date($this->item->updated , 'd M Y H:i'); ?>
		</div>
	</div>

    <div class="row-fluid form-horizontal">
		<div class="span12">
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('XBJOURNALS_DETAILS')); ?>
	        <h4><?php echo Text::_('XBJOURNALS_CALENDAR_LOCAL_INFO'); ?></h4>
			<div class="row-fluid">
	    		<div class="span4 form-vertical">
    	    		<div class="pull-right">
    	    			<button type="button" class="btn btn-small" onclick="clearmd();">Clear Markdown</button>&nbsp;
    	    			<button type="button" class="btn btn-small" onclick="texttohtml();">Text to HTML <b>-&gt;</b></button>
    	    		</div><br />
	          		<?php echo $this->form->renderField('description'); ?>
	   			</div>
	    		<div class="span5 form-vertical">
    	    			<button type="button" class="btn btn-small" onclick="htmltotext()"><b>&lt;-</b> HTMLto Text</button>&nbsp;
    	    			<button type="button" class="btn btn-small" onclick="htmltomd()"><b>&lt;-</b> HTMLto MD</button><br />
	          		<?php echo $this->form->renderField('html_desc'); ?>   					
	   			</div>
				<div class="span3">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
				
	   		</div>
				    					<?php echo $this->form->renderField('attachments'); ?>
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'dates', JText::_('XBJOURNALS_DATES')); ?>
				<table cellpadding="8px">
					<tr>
						<td><?php echo Text::_('dtstart'); ?></td>
						<td><?php if (substr($this->item->dtstart, -1, 8) =='00:00:00') {
						    echo HtmlHelper::date($this->item->dtstart , 'd M Y'); 
						} else {
						    echo HtmlHelper::date($this->item->dtstart , 'd M Y H:i');
						} ?></td>
						<td><i>DTSTART is the calendar entry date of the item</i></td>
					</tr>
					<tr>
						<td><?php echo Text::_('dtstamp'); ?></td>
						<td><?php echo HtmlHelper::date($this->item->dtstamp , 'd M Y H:i:s'); ?></td>
						<td><i>DTSTAMP is set by the server when the item is modified</i></td>
					</tr>
					<tr>
						<td><?php echo Text::_('created'); ?></td>
						<td><?php echo HtmlHelper::date($this->item->created , 'd M Y H:i:s'); ?></td>
						<td><i>Created is the Joomla field set when the item is first entered</i></td>
					</tr>
					<tr>
						<td><?php echo Text::_('modified'); ?></td>
						<td><?php echo HtmlHelper::date($this->item->modified , 'd M Y H:i:s'); ?></td>
						<td><i>Modified is the Joomla field when the item is changed and saved locally, or when it is updated from the server</i></td>
					</tr>
					<tr>
						<td><?php echo Text::_('updated'); ?></td>
						<td><?php echo HtmlHelper::date($this->item->updated , 'd M Y H:i:s'); ?></td>
						<td><i>Last Checked is updated when the item is was last checked on the server even if it was unchanged</i></td>
					</tr>
				</table>
			
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'server', JText::_('XBJOURNALS_SERVER_INFO')); ?>
	          	<h4><?php echo Text::_('XBJOURNALS_CALENDAR_SERVER_INFO'); ?></h4>
	          	<p class="xbit"><?php echo Text::_('XBJOURNALS_CALENDAR_SERVER_INFO_DESC'); ?></p>
    			<div class="row-fluid form-vertical">
    				<div class="span4">
    					<?php echo $this->form->renderField('etag'); ?>
    					<?php echo $this->form->renderField('href'); ?>
    					<?php echo $this->form->renderField('uid'); ?>
    					<?php echo $this->form->renderField('parentuid'); ?>
    					<?php echo $this->form->renderField('sequence'); ?>
    				</div>
    				<div class="span4">
    					<?php echo $this->form->renderField('class'); ?>
    					<?php echo $this->form->renderField('categories'); ?>
    					<?php echo $this->form->renderField('status'); ?>
    					<?php echo $this->form->renderField('x-status'); ?>
    				</div>
    				<div class="span4">
    					<?php echo $this->form->renderField('url'); ?>
    					<?php echo $this->form->renderField('geo'); ?>
    					<?php echo $this->form->renderField('location'); ?>
    					<?php echo $this->form->renderField('dtstart'); ?>    					
    				</div>
				</div>
				<div class="row-fluid form-horizontal">
					<div class="span6">
    					<?php echo $this->form->renderField('summary'); ?>
    					<?php echo $this->form->renderField('comments'); ?>
    					<?php echo $this->form->renderField('attendees'); ?>
					</div>
					<div class="span6">
    					<?php echo $this->form->renderField('itemparams'); ?>
    					<?php echo $this->form->renderField('otherprops'); ?>
					</div>
				</div>
			
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('XBJOURNALS_PUBLISHING')); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
 			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
			
	 	</div>
	 </div>
    <input type="hidden" name="task" value="calendar.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>

