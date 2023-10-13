<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/calendar/tmpl/edit.php
 * @version 0.1.1.0 11th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

Factory::getDocument()->addScriptDeclaration('function pleaseWait(targ) {
		document.getElementById(targ).style.display = "block";
	}');

?>
<style type="text/css" media="screen">
    #jform_url { width: 500px; }   
</style>
<form action="<?php echo JRoute::_('index.php?option=com_xbjournals&view=calendar&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    	<div id="waiter" class="xbbox alert-info" style="display:none;">
          <table style="width:100%">
              <tr>
                  <td style="width:200px;"><img src="/media/com_xbjournals/images/waiting.gif" style="height:100px" /> </td>
                  <td style="vertical-align:middle;"><b><?php echo Text::_('XBJOURNALS_WAITING_REPLY'); ?></b> </td>
              </tr>
          </table>
    	</div>
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
		<div class="span6">
			<?php echo $this->form->renderField('server_id'); ?>
		</div>
		<div class="span6">
			<?php echo $this->form->renderField('last_checked'); ?>
		</div>
	</div>

    <div class="row-fluid form-horizontal">
		<div class="span12">
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('XBJOURNALS_DETAILS')); ?>
			<div class="row-fluid">
	    		<div class="span9">
	          		<h4><?php echo Text::_('XBJOURNALS_CALENDAR_LOCAL_INFO'); ?></h4>
	          		<?php echo $this->form->renderField('description'); ?>   					
	   			</div>
				<div class="span3">
					<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
	   		</div>
	 		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'server', JText::_('XBJOURNALS_SERVER_INFO')); ?>
	          	<h4><?php echo Text::_('XBJOURNALS_CALENDAR_SERVER_INFO'); ?></h4>
	          	<p class="xbit"><?php echo Text::_('XBJOURNALS_CALENDAR_SERVER_INFO_DESC'); ?></p>
    			<div class="row-fluid">
    				<div class="span6">
    					<?php echo $this->form->renderField('cal_displayname'); ?>
    					<?php echo $this->form->renderField('cal_url'); ?>
    					<?php echo $this->form->renderField('cal_ctag'); ?>
    					<?php echo $this->form->renderField('cal_calendar_id'); ?>
    				</div>
    				<div class="span6">
    					<?php echo $this->form->renderField('cal_rgb_color'); ?>
    					<?php echo $this->form->renderField('cal_order'); ?>
    					<?php echo $this->form->renderField('cal_components'); ?>
    					<?php echo $this->form->renderField('readonly'); ?>
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

