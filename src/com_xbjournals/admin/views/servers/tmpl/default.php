<?php
/*******
 * @package xbJournals Compnent
 * @filesource admin/views/servers/tmpl/default.php
 * @version 0.1.2.3 21st July 2023
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

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='title';
    $listDirn = 'ascending';
}

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbjournals.server');
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_xbjournals&task=servers.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'xbjournalsServersList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$servereditlink='index.php?option=com_xbjournals&view=server&task=server.edit&id=';

Factory::getDocument()->addScriptDeclaration('function pleaseWait(targ) {
		document.getElementById(targ).style.display = "block";
	}');

?>
<form action="<?php echo Route::_('index.php?option=com_xbjournals&view=servers'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
    	<div id="waiter" class="xbbox alert-info" style="display:none;">
          <table style="width:100%">
              <tr>
                  <td style="width:200px;"><img src="/media/com_xbjournals/images/waiting.gif" style="height:100px" /> </td>
                  <td style="vertical-align:middle;"><b><?php echo Text::_('XBJOURNALS_WAITING_REPLY'); ?></b> </td>
              </tr>
          </table>
    	</div>
		<h3><?php echo Text::_( 'XBJOURNALS_SERVERS_USER' ); ?></h4>
		<p><?php echo Text::_('XBJOURNALS_SERVERS_USER_SUBTITLE'); ?></p>
		<?php // Search tools bar
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>	
        <div class="pull-right pagination xbm0">
    		<?php  echo $this->pagination->getPagesLinks(); ?>
    	</div>
    	<div class="pull-left">
    		<?php  echo $this->pagination->getResultsCounter(); ?> 
            <?php if($this->pagination->pagesTotal > 1) echo ' on '.$this->pagination->getPagesCounter(); ?>
    		<p>              
                <?php echo 'Sorted by '.$listOrder.' '.$listDirn ; ?>
    		</p>
    	</div>
        <div class="clearfix"></div>      

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" id="xbjournalsServersList">	
			<thead>
				<tr>
					<th class="nowrap center hidden-phone" style="width:25px;">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', 
						    $listDirn, $listOrder, null, 'asc', 'XBJOURNALS_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="nowrap center" style="width:55px">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_TITLE','title',$listDirn,$listOrder); ?>
					</th>					
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_USERNAME','username',$listDirn,$listOrder); ?>
						 &amp;
						<?php echo HTMLHelper::_('searchtools.sort','XBJOURNALS_DOMAIN','url',$listDirn,$listOrder); ?>
						 &amp; <?php echo Text::_('XBJOURNALS_PATH'); ?>
					</th>
					<th class="hidden-tablet hidden-phone" >
						<?php echo (Text::_('XBJOURNALS_DESCRIPTION'));?>
					</th>
					<th>
						<?php echo (Text::_('XBJOURNALS_CALENDARS')); ?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">
						<?php echo HTMLHelper::_('searchtools.sort', 'Updated', 'modified', $listDirn, $listOrder );?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">					
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
					<th>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbjournals.server.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbjournals.server.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbjournals.server.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
				<td class="order nowrap center hidden-phone">
					<?php
						$iconClass = '';
						if (!$canChange) {
							$iconClass = ' inactive';
						} elseif (!$saveOrder) {
							$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
						}
					?>
					<span class="sortable-handler<?php echo $iconClass; ?>">
						<span class="icon-menu" aria-hidden="true"></span>
					</span>
					<?php if ($canChange && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<td class="center hidden-phone">
					<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<div class="btn-group">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'server.', $canChange, 'cb'); ?>
						<?php if ($item->note!=""){ ?>
							<span class="btn btn-micro active hasTooltip" title="" data-original-title="<?php echo '<b>'.JText::_( 'XBJOURNALS_ADMIN_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
								<i class="icon-info xbinfo"></i>
							</span>
						<?php } else {?>
							<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
						<?php } ?>
					</div>
				</td>
				<td>
					<p class="xb12 xbbold xbmb8">
					<?php if ($item->checked_out) {
					    $couname = Factory::getUser($item->checked_out)->username;
					    echo HTMLHelper::_('jgrid.checkedout', $i, JText::_('XBJOURNALS_OPENED_BY').': '.$couname, $item->checked_out_time, 'server.', $canCheckin);
					} ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::_($servereditlink.$item->id);?>"
							title="<?php echo Text::_('XBJOURNALS_EDIT'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td>
					<?php if ($item->url == '') : ?>
						<p class="xbnit"><?php echo Text::_('XBJOURNALS_LOCAL_STORAGE'); ?>
					<?php else : ?>
						<?php echo '<span class="xbnit">'.Text::_('XBJOURNALS_USERNAME').'</span>: <b>'.$item->username.'</b>'; ?>
						<?php $urlarr = parse_url($item->url); ?>
						<br /><?php echo $urlarr["scheme"].'://'.$urlarr["host"]; ?>
						<br /><?php echo $urlarr["path"]; ?>
						<?php //echo '<span class="xbnit">'.Text::_('XBJOURNALS_PASSWORD').'</span>: '.$item->password; ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->description; ?>
				</td>
				<td>
					<?php if ($item->ccnt > 0) : ?>
						<details>
							<summary>
								<?php echo $item->ccnt.' '.lcfirst(Text::_('XBJOURNALS_CALENDARS')).' '.Text::_('XBJOURNALS_FOUND'); ?>
							</summary>
							<ul>
								<?php foreach ($item->calendars as $i=>$cal) : ?>
								    <li><a href="index.php?option=com_xbjournals&view=calendar&id=<?php echo $cal['id'];?>">
								    	<?php if (!$cal['vjok']) echo '<span class="xbdim">'; ?>
								    	<?php echo $cal['title']; ?>
								    	<?php if (!$cal['vjok']) echo'</span>'; ?>
								    </a></li>
								<?php  endforeach; ?>
							</ul>
						</details>
					<?php else : ?>
						<?php echo Text::_('XBJOURNALS_NO_CALS_FOUND'); ?>
						<br />
					<?php endif; ?>
				</td>
				<td class="hidden-phone">
					upd:<?php echo HtmlHelper::date($item->updated, 'M Y H:i');?>				
					mod:<?php echo HtmlHelper::date($item->modified, 'M Y H:i');?>				
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>					
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		</table>
	
	<?php endif; ?>

	</div>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</div>
</form>