<?php
/*******
 * @package xbJournals
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 0.0.0.5 4th April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');

$servereditlink='index.php?option=com_xbjournals&view=server&task=server.edit&id=';
$calendareditlink ='index.php?option=com_xbjournals&view=calendar&task=calendar.edit&id=';
?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
		<h4><?php echo Text::_( 'XBJOURNALS_SERVERS' ); ?></h4>
	
	<?php
        // Search tools bar
//        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	

	<?php if (empty($this->servers)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('No server records found'); ?>
		</div>
	<?php else : ?>
		<?php $scnt = count($this->servers); ?>
		<p>
		<?php echo $scnt; ?> <?php  echo ($scnt == 1) ? Text::_('XBJOURNALS_SERVER') : Text::_('XBJOURNALS_SERVERS');
		  echo ' '.Text::_('XBJOURNALS_FOUND'); ?></p>
		<table class="table table-striped table-hover">	
			<thead>
				<tr>
					<th>
						<?php echo Text::_('Title'); ?>
					</th>					
					<th>
						<?php echo Text::_('Domain');?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">
						<?php echo Text::_('Updated');?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">					
						<?php echo Text::_('JGRID_HEADING_ID');?>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->servers as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.server.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbmaps.map.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbmaps.map.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
				<td>
					<p class="xb12 xbbold xbmb8">
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::_($servereditlink.$item->id);?>"
							title="<?php echo JText::_('edit server'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td>
					<?php echo parse_url($item->url, PHP_URL_HOST);?>
					<br />Username: <?php echo $item->username; ?>
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>
				</td>
				<td class="hidden-phone">
					<span class="xbnit"><?php echo HtmlHelper::date($item->modified, 'd M Y');?></span>
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		</table>
	
	<?php endif; ?>

		<h4><?php echo Text::_( 'XBJOURNALS_CALENDARS' ); ?></h4>

	<?php if (empty($this->calendars)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('No calendar records found'); ?>
		</div>
	<?php else : ?>
		<?php $ccnt = count($this->calendars); ?>
		<p>
		<?php echo $ccnt; ?> <?php  echo ($ccnt == 1) ? Text::_('XBJOURNALS_CALENDAR') : Text::_('XBJOURNALS_CALENDARS');
		  echo ' '.Text::_('XBJOURNALS_FOUND'); ?></p>
		<table class="table table-striped table-hover">	
			<thead>
				<tr>
					<th>
						<?php echo Text::_('Title'); ?>
					</th>					
					<th>
						<?php echo Text::_('Server');?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">
						<?php echo Text::_('Checked');?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">					
						<?php echo Text::_('JGRID_HEADING_ID');?>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->calendars as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.calendar.'.$item->id);
 				$canEditOwn = $user->authorise('core.edit.own', 'com_xbjournals.calendar.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbjournals.calendar.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
				<td>
					<p class="xb12 xbbold xbmb8">
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::_($calendareditlink.$item->id);?>"
							title="<?php echo JText::_('edit calendar'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td>
					<?php echo $item->server; ?>
				</td>
				<td class="hidden-phone">
					<span class="xbnit"><?php echo HtmlHelper::date($item->last_checked, 'd M Y');?></span>
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		</table>
	
	<?php endif; ?>

        <?php 
//        echo '<pre>'.print_r($this->journalitems,true).'</pre>';
        
//        echo '<pre>'.print_r($this->notes,true).'</pre>';
        ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>
</form>