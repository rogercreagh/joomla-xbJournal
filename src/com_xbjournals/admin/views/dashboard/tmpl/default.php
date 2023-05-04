<?php
/*******
 * @package xbJournals
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 0.0.2.0 4th May 2023
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
		<br />
		    <div class="xbinfopane">
      	<div class="row-fluid hidden-phone">
        	<?php echo HtmlHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
        		<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_SYSINFO'), 'sysinfo'); ?>
                  <?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
 				<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
			</div>
       	</div>
		
	</div>
	<div id="j-main-container" >
			<h3><?php echo Text::_('XBJOURNALS_STATUS_SUM'); ?></h3>
			<div class="row-fluid">
            	<div class="span8">
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxcyan">
								<h4><?php echo Text::_( 'XBJOURNALS_SERVERS' ); ?></h4>
                            	<?php if (empty($this->servers)) : ?>
                            		<div class="alert alert-no-items">
                            			<?php echo Text::_('No server records found'); ?>
                            		</div>
                            	<?php else : ?>
                            		<?php $scnt = count($this->servers); ?>
                            		<p><?php echo $scnt; ?> <?php  echo ($scnt == 1) ? Text::_('XBJOURNALS_SERVER') : Text::_('XBJOURNALS_SERVERS');
                                		  echo ' '.Text::_('XBJOURNALS_FOUND'); ?></p>
                            		<table class="table table-striped table-hover">	
                            			<thead>
                            				<tr>
                            					<th>
                            						<?php echo Text::_('XBJOURNALS_TITLE'); ?>
                            					</th>					
                            					<th>
                            						<?php echo Text::_('XBJOURNALS_DOMAIN');?>
                            					</th>
                            					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">
                            						<?php echo Text::_('XBJOURNALS_UPDATED');?>
                            					</th>
                            					<th class="nowrap hidden-tablet hidden-phone" style="width:100px;">					
                            						<?php echo Text::_('JGRID_HEADING_ID');?>
                            					<th>
                            					</th>
                            				</tr>
                            			</thead>
                            			<tbody>
                            			<?php foreach ($this->servers as $i => $item) :
                                            $canEdit    = $user->authorise('core.edit', 'com_xbjournals.server.'.$item->id);
                                            $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                                                    || $item->checked_out==$userId || $item->checked_out==0;
                            				$canEditOwn = $user->authorise('core.edit.own', 'com_xbjournals.map.'.$item->id) && $item->created_by == $userId;
                                            $canChange  = $user->authorise('core.edit.state', 'com_xbjournals.map.'.$item->id) && $canCheckin;
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
        					</div>
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxgrn">
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
        					</div>
            			
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxblue">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->journalStates['total']; ?></span> 
        							<a href="index.php?option=com_xbjournals&view=journals"><?php echo ucfirst(Text::_('XBJOURNALS_JOURNALS')); ?>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->journalStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->journalStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->journalStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->journalStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->journalStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->journalStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->journalStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_TRASHED')); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxmag">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->notebookStates['total']; ?></span> 
        							<a href="index.php?option=com_xbjournals&view=notebooks"><?php echo ucfirst(Text::_('XBJOURNALS_NOTEBOOKS')); ?>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->notebookStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->notebookStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->notebookStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->notebookStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->notebookStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->notebookStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->notebookStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_TRASHED')); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
              	</div>
				<div id="xbinfo" class="span4">
					<div class="row-fluid">
			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_SYSINFO'), 'sysinfo','xbaccordion'); ?>
                			<p><b><?php echo Text::_( 'XBJOURNALS_COMPONENT' ); ?></b>
        						<br /><?php echo Text::_('XBJOURNALS_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
        							$this->xmldata['creationDate'];?>
                          	</p>
                            <hr />
                          	<p><b><?php echo Text::_( 'XBJOURNALS_CLIENT'); ?></b>
        						<br/><?php echo Text::_( 'XBJOURNALS_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XBJOURNALS_BROWSER').' '.$this->client['browser']; ?>
                         	</p>
        				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
		        			<p>Config (Options) Settings:
		        			</p>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
        				<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_ABOUT'), 'about'); ?>
							<p><?php echo Text::_( 'XBJOURNALS_ABOUT_INFO' ); ?></p>
						<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
						<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_LICENCE'), 'license'); ?>
							<p><?php echo Text::_( 'XBJOURNALS_LICENSE_GPL' ); ?>
								<br><?php echo Text::sprintf('XBJOURNALS_LICENSE_INFO','xbJournals');?>
								<br /><?php echo $this->xmldata['copyright']; ?>
							</p>		        		
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBJOURNALS_REGINFO'), 'reginfo','xbaccordion'); ?>
                            <?php  if (XbjournalsHelper::penPont()) {
                                echo Text::_('XBJOURNALS_BEER_THANKS'); 
                            } else {
                                echo Text::_('XBJOURNALS_BEER_LINK');
                            }?>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
						<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
					</div>		
				</div>
			</div>	
			<div class="row-fluid">
            	<div class="span6">
					<div class="xbbox xbboxyell">
						<h3 class="xbtitle">
							<span class="badge badge-info pull-right"><?php //echo Text::_('XBJOURNALS_TOTAL').' '. $this->calendarStates['total']; ?></span> 
							<a href="index.php?option=com_xbjournals&view=catslist"><?php echo Text::_('XBJOURNALS_CATEGORIES'); ?></a>
						</h3>
        				<div class="row-striped">
        					<div class="row-fluid">
        						<div class="span6">
        							<span class="badge badge-success xbmr10"><?php echo $this->catStates['published']; ?></span>
        							<?php echo Text::_('XBJOURNALS_PUBLISHED'); ?>
        						</div>
        						<div class="span6">
        							<span class="badge <?php echo $this->catStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->catStates['unpublished']; ?></span>
        							<?php echo Text::_('XBJOURNALS_UNPUBLISHED'); ?>
        						</div>
        					</div>
         					<div class="row-fluid">
        						<div class="span6">
        							<span class="badge <?php echo $this->catStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->catStates['archived']; ?></span>
        							<?php echo Text::_('XBJOURNALS_ARCHIVED'); ?>
        						</div>
        						<div class="span6">
        							<span class="badge <?php echo $this->catStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->catStates['trashed']; ?></span>
        							<?php echo Text::_('XBJOURNALS_TRASHED'); ?>
        						</div>
        					</div>
                         </div>
                         <h3 class="xbsubtitle"><?php  echo Text::_('XBJOURNALS_COUNT_CATS'); ?></h3>
                         <div class="row-striped">
        					<div class="row-fluid">
        						    <?php echo $this->catlist; ?>
        					</div>
        				</div>
					</div>            			
            	</div>
            	<div class="span6">
        			<div class="xbbox xbboxmag">
        				<h3 class="xbtitle">
        					<span class="badge badge-info pull-right"><?php echo ('0') ; ?></span> 
        					<a href="index.php?option=com_xbjournals&view=tagslist"><?php echo Text::_('XBJOURNALS_TAGS'); ?></a>
        				</h3>
        				<div class="row-striped">
        					<div class="row-fluid">
                              <?php echo 'Journal Entries: ';
        						echo '<span class="bkcnt badge  pull-right">'.$this->tags['journals'].'</span>'; ?>
                            </div>  
                            <div class="row-fluid">
                              <?php echo 'Notebook entries: ';
        						echo '<span class="percnt badge pull-right">'.$this->tags['notes'].'</span>'; ?>
                            </div>  
                         </div>
        				 <h3 class="xbsubtitle"><?php echo Text::_('XBJOURNALS_COUNT_TAGS'); ?></h3>
                          <div class="row-fluid">
                             <div class="row-striped">
            					<div class="row-fluid">
            						<?php echo $this->taglist; ?>
                               </div>
                             </div>
            			</div>
	        		</div>
            	</div>
            	</div>
           	</div>






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