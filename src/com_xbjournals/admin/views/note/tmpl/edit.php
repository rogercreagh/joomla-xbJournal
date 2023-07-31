<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/note/tmpl/edit.php
 * @version 0.1.3.1 31st July 2023
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
      	updatePvMd();
	}
	
	function htmltomd() {
		var html = window.parent.Joomla.editors.instances['jform_html_desc'].getValue();
    	var converter = new showdown.Converter();
        var descMd = converter.makeMarkdown(html);
        document.getElementById('jform_description').value = descMd;
        updatePvMd();
	}
	
	function htmltotext() {
		var descHtml = window.parent.Joomla.editors.instances['jform_html_desc'].getValue();
		var descText = stripHtml(descHtml);
        document.getElementById('jform_description').value = descText;
        updatePvMd();
	}
	
	function clearmd() {
    	var descMd = document.getElementById('jform_description').value;
		var descText = stripHtml(descMd);
        document.getElementById('jform_description').value = descText;
        updatePvMd();
	}
	
    function stripHtml(html) {
	   let doc = new DOMParser().parseFromString(html, 'text/html');
	   return doc.body.textContent || "";
    }

	function updatePvMd() {
    	var descText = document.getElementById('jform_description').value;
		var converter = new showdown.Converter();
        var descHtml = converter.makeHtml(descText);
		document.getElementById('mdpv').innerHTML= descHtml;
    }

</script>
<style type="text/css" media="screen">
    #jform_url { width: 500px; }   
</style>
<form action="<?php echo JRoute::_('index.php?option=com_xbjournals&view=note&layout=edit&id=' . (int) $this->item->id); ?>"
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
			<?php echo Text::_('Calendar').': <b>'.XbjournalsHelper::getValueFromId('#__xbjournals_calendars',$this->item->calendar_id).'</b>'; ?>
			<?php //echo $this->form->renderField('calendar_id'); ?>
		</div>
		<div class="span4">
			<?php echo Text::_('Type').': <b>'.$this->item->entry_type.'</b>'; ?>
		</div>
		<div class="span4">
			<?php echo Text::_('Updated').': <b>'.HtmlHelper::date($this->item->updated , 'd M Y H:i').'</b>'; ?>
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
    	    			<button type="button" class="btn btn-small hasPopover" onclick="clearmd();" 
    	    				 title data-content="This will attempt to remove any markdown codes from the text editor" data-original-title="Clear Markdown" >
    	    				 Clear Markdown
    	    			</button>&nbsp;
    	    			<button type="button" class="btn btn-small hasPopover" onclick="texttohtml();"
    	    				 title data-content="This will transfer the text to the html editor including any markdown formatting" data-original-title="Text &amp; MD to HTML" >
    	    				 	Text to HTML <b>-&gt;</b>
    	    			</button>
    	    		</div><br /><br />
	          		<?php echo $this->form->renderField('description'); ?>
                    <p><button type="button" class="btn btn-small hasPopover" onclick="updatePvMd();"
                    	 title data-content="This will preview the current text as Markdown in the box below" data-original-title="Preview Markdown" >
                    	 	Preview Markdown Text
                    </button>&nbsp;<br />
					<div id="mdpv" class="xbbox xbboxwht" ><span class="xbnote xbit">click button to update preview</span></div>
	   			</div>
	    		<div class="span5 form-vertical">
    	    			<button type="button" class="btn btn-small hasPopover" onclick="htmltotext()"
    	    				 title data-content="This will copy the HTML back to the text editor as plain text without markdown" data-original-title="HTML to Text" >
    	    				 	<b>&lt;-</b> HTMLto Text
    	    			</button>&nbsp;
    	    			<button type="button" class="btn btn-small hasPopover" onclick="htmltomd()"
    	    				 title data-content="This will attempt to convert the html editor content to markdown in the text editor" data-original-title="HTML to Markdown" >
    	    				 	<b>&lt;-</b> HTMLto MD
    	    			</button><br /><br/>
	          		<?php echo $this->form->renderField('html_desc'); ?>   					
	   			</div>
				<div class="span3">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
				
	   		</div>
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'attachments', JText::_('XBJOURNALS_ATTACHMENTS')); ?>
				<p><i><?php echo Text::_('Local Attachment path'); ?> <code><?php echo $this->attpath; ?></code></i>
				    <?php echo $this->form->renderField('attachments'); ?>
				</p>
				<?php $attcnt = count($this->item->atts); 
				$col = 1;
				if ($attcnt > 0 ) : ?>
				<p class="xbniit"><?php echo Text::_('Attachment image previews'); ?></p>
				<div>
    				<table class="xbcentre">
    					<tr>
    						<?php foreach ($this->item->atts as $att) : ?>
    						    <td class="xbtdimg">
    						    	<p class="xbniit"><?php echo $att->filename; ?></p>
    						    	<img src="<?php echo $att->path?>" style="max-width:200px; max-height:200px;"/>
    						    </td>
    						    <?php $col ++; 
    						      if ($col>4) $col = 1;
    						      if ($col == 1) echo '</tr><tr>';
    						    ?>
    						    
    						<?php endforeach; ?>
    					</tr>
    				</table>
				</div>
				<?php endif; ?>
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'dates', JText::_('XBJOURNALS_DATES')); ?>
				<table cellpadding="8px">
					<tr>
						<td colspan="3"><?php echo Text::_('Notebook entries are not tied to a calendar date'); ?></td>
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

