<?php
/*******
 * @package xbJournals
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 0.0.2.0 29th April 2023
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
        			<p><b><?php echo Text::_( 'xbJournals Component' ); ?></b>
						<br /><?php echo Text::_('XBCULTURE_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
							$this->xmldata['creationDate'];?>
                  	</p>
                    <hr />
                    <?php  if (XbjournalsHelper::penPont()) {
                        echo Text::_('XBCULTURE_BEER_THANKS'); 
                    } else {
                        echo Text::_('XBCULTURE_BEER_LINK');
                    }?>
                    <hr />
                  	<p><b><?php echo Text::_( 'XBCULTURE_CLIENT'); ?></b>
						<br/><?php echo Text::_( 'XBCULTURE_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XBCULTURE_BROWSER').' '.$this->client['browser']; ?>
                 	</p>
				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
				<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_ABOUT'), 'about'); ?>
					<p><?php echo Text::_( 'XBJOURNALS_ABOUT_INFO' ); ?></p>
				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
				<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_LICENSE'), 'license'); ?>
					<p><?php echo Text::_( 'XBCULTURE_LICENSE_GPL' ); ?>
						<br><?php echo Text::sprintf('XBCULTURE_LICENSE_INFO','xbFilms');?>
						<br /><?php echo $this->xmldata['copyright']; ?>
					</p>
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
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->serverStates['total']; ?></span> 
        							<a href="index.php?option=com_xbjournals&view=servers"><?php echo Text::_('XBJOURNALS_SERVERS'); ?></a>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->serverStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->serverStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->serverStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->serverStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->serverStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->serverStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->serverStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_TRASHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->trackCnts['mapswithtracks']>0 ?'badge-cyan' : ''; ?> xbmr10"><?php echo $this->trackCnts['mapswithtracks']; ?></span>
        									<?php echo Text::_('XBJOURNALS_MAPS_WITH_TRACKS'); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerCnts['mapswithmarkers']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerCnts['mapswithmarkers']; ?></span>
        									<?php echo Text::_('XBJOURNALS_MAPS_WITH_MARKERS'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			</div>
            		</div>
            		<div class="row-fluid">
            			<div class="span12">
        					<div class="xbbox xbboxgrn">
        						<h3 class="xbtitle">
        							<span class="badge badge-info pull-right"><?php echo Text::_('XBJOURNALS_TOTAL').' '. $this->calendarStates['total']; ?></span> 
        							<a href="index.php?option=com_xbjournals&view=calendars"><?php echo ucfirst(Text::_('XBJOURNALS_CALENDARS')); ?></a>
        						</h3>
        						<div class="row-striped">
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge badge-success xbmr10"><?php echo $this->calendarStates['published']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_PUBLISHED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->calendarStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->calendarStates['unpublished']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_UNPUBLISHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span6">
        									<span class="badge <?php echo $this->calendarStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->calendarStates['archived']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_ARCHIVED')); ?>
        								</div>
        								<div class="span6">
        									<span class="badge <?php echo $this->calendarStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->calendarStates['trashed']; ?></span>
        									<?php echo ucfirst(Text::_('XBJOURNALS_TRASHED')); ?>
        								</div>
        							</div>
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->trackCnts['tracksonmaps']>0 ?'badge-cyan' : ''; ?> xbmr10"><?php echo $this->trackCnts['tracksonmaps']; ?></span>
        									<?php echo Text::_('XBJOURNALS_TRACKS_WITH_MAPS'); ?>
        								</div>
        							</div>
        						</div>
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
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerCnts['markersonmaps']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerCnts['markersonmaps']; ?></span>
        									<?php echo Text::_('XBJOURNALS_MARKERS_WITH_MAPS'); ?>
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
        							<div class="row-fluid">
        								<div class="span12">
        									<span class="badge <?php echo $this->markerCnts['markersonmaps']>0 ?'badge-mag' : ''; ?> xbmr10"><?php echo $this->markerCnts['markersonmaps']; ?></span>
        									<?php echo Text::_('XBJOURNALS_MARKERS_WITH_MAPS'); ?>
        								</div>
        							</div>
        						</div>
        					</div>
            			
            			</div>
            		</div>
              	</div>
				<div id="xbinfo" class="span4">
					<div class="row-fluid">
			        	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-cpanel', array('active' => 'sysinfo')); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBJOURNALS_KEY_CONFIG'), 'keyconfig','xbaccordion'); ?>
		        		Maps
		        		<ul>
		        			<li>Categories: 
		        				<?php if (!$this->mapcats) {
		        				    echo Text::_('XBJOURNALS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->mapcat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b><?php echo Text::_($this->maptags ? 'XBJOURNALS_ENABLED' : 'XBJOURNALS_DISABLED'); ?></b>
		        			</li>
		        			<li>Default Type: <b><?php echo $this->params->get('map_type')?></b>
		        			</li>
		        		</ul>
		        		Markers
		        		<ul>
		        			<li>Categories:        			
		        				<?php if (!$this->mrkcats) {
		        				    echo Text::_('XBJOURNALS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->markercat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b><?php echo Text::_($this->mrktags ? 'XBJOURNALS_ENABLED' : 'XBJOURNALS_DISABLED'); ?></b>
		        			</li>
		        			<li>Images Folder: <b>/images/<?php echo $this->params->get('def_markers_folder')?></b>
		        			</li>
		        		</ul>
		        		Tracks
		        		<ul>
		        			<li>Categories:		        			
		        				<?php if (!$this->trkcats) {
		        				    echo Text::_('XBJOURNALS_DISABLED');
		        				} else {
		        				    echo 'Default '.$this->trackcat;
		        				} ?>
		        				
		        			</li>
		        			<li>Tags: <b>
		        				<?php echo Text::_($this->trktags ? 'XBJOURNALS_ENABLED' : 'XBJOURNALS_DISABLED'); ?></b>
		        			</li>
		        			<li>GPX Folder: <b>/<?php echo $this->params->get('base_gpx_folder')?></b>
		        			</li>
		        			<li>Single Track View: <b>
		        				<?php echo Text::_($this->trkview ? 'XBJOURNALS_ENABLED' : 'XBJOURNALS_DISABLED');?></b>
		        			</li>
		        		</ul>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBJOURNALS_SYSINFO'), 'sysinfo','xbaccordion'); ?>
	        			<p><b><?php echo Text::_( 'XBJOURNALS_COMPONENT' ); ?></b>
							<br /><?php echo Text::_('XBJOURNALS_VERSION').': '.$this->xmldata['version'].'<br/>'.
								$this->xmldata['creationDate'];?>
						</p>
						<p><b><?php echo Text::_( 'XBJOURNALS_CONTENT_PLUGIN' ); ?></b>: ;
						<?php if(PluginHelper::isEnabled('content','xbjournals')) {
							$man = XbmapsGeneral::getExtManifest('plugin','xbjournals','content');
							if ($man) {
								$man = json_decode($man);
								echo '<br />'.Text::_('XBJOURNALS_VERSION').': '.$man->version;
								echo '<br />'.$man->creationDate;
							} else {
								echo 'problem with manifest';
							}
						} else {
							echo Text::_( 'XBJOURNALS_NOT_INSTALLED' ); 
						}?>
						</p>
						<p><b><?php echo Text::_( 'XBJOURNALS_BUTTON_PLUGIN' ); ?></b>: ;
						<?php if(PluginHelper::isEnabled('editor-xtd','xbjournals')) {
							$man = XbmapsGeneral::getExtManifest('plugin','xbjournals','editors-xtd');
							if ($man) {
								$man = json_decode($man);
								echo '<br />'.Text::_('XBJOURNALS_VERSION').': '.$man->version;
								echo '<br />'.$man->creationDate;
							} else {
								echo 'problem with manifest';
							}
						} else {
							echo Text::_( 'XBJOURNALS_NOT_INSTALLED' ); 
						}?>
						</p>
						<p><b><?php echo Text::_( 'XBJOURNALS_YOUR_CLIENT' ); ?></b>
							<br/><?php echo $this->client['platform'].'<br/>'.$this->client['browser']; ?>
						</p>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		        		<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', Text::_('XBJOURNALS_REGINFO'), 'reginfo','xbaccordion'); ?>
		        		 <?php if (XbmapsGeneral::penPont()) : ?>
		        			<p><?php echo Text::_('XBJOURNALS_THANKS_REG'); ?></p>
		        		<?php else : ?>
		        			<p><b><?php echo Text::_('XBMAPS'); ?></b> <?php echo Text::_('XBJOURNALS_REG_ASK'); ?></p>
		        			 <?php echo Text::_('XBJOURNALS_BEER_TAG').'<br />'.Text::_('XBJOURNALS_BEER_FORM');?>
		        		<?php endif; ?>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', JText::_('XBJOURNALS_ABOUT'), 'about','xbaccordion'); ?>
	        			<p><?php echo JText::_( 'XBJOURNALS_ABOUT_INFO' ); ?></p>
	        			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	        			<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-cpanel', JText::_('XBJOURNALS_LICENSE'), 'license','xbaccordion'); ?>
	        			<p><?php echo JText::_( 'XBJOURNALS_LICENSE_INFO' ); ?></p>
	        			<hr />
	        			<p>
	        				<?php echo Text::_( 'XBMAPS' ).' '.$this->xmldata['copyright']; ?>
	        			</p>
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
                 <h3 class="xbsubtitle"><?php  echo Text::_('XBJOURNALS_COUNT_CATS'); ?><span class="xb09 xbnorm"> <i>(<?php echo Text::_('XBJOURNALS_MAPS_MRKS_TRKS'); ?>)</i></span></h3>
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
					<span class="badge badge-info pull-right"><?php echo ($this->tags['tagcnts']['mapcnt'] + $this->tags['tagcnts']['mrkcnt']  + $this->tags['tagcnts']['trkcnt']) ; ?></span> 
					<a href="index.php?option=com_xbjournals&view=tagslist"><?php echo Text::_('XBJOURNALS_TAGS'); ?></a>
				</h3>
				<div class="row-striped">
					<div class="row-fluid">
                      <?php echo 'Films: ';
						echo '<span class="bkcnt badge  pull-right">'.$this->tags['tagcnts']['mapcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'People: ';
						echo '<span class="percnt badge pull-right">'.$this->tags['tagcnts']['mrkcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Reviews: ';
						echo '<span class="revcnt badge pull-right">'.$this->tags['tagcnts']['trkcnt'].'</span>'; ?>
                    </div>  
                 </div>
				 <h3 class="xbsubtitle"><?php echo Text::_('XBJOURNALS_COUNT_TAGS'); ?><span class="xb09 xbnorm"><i>(<?php echo Text::_('XBJOURNALS_MAPS_MRKS_TRKS'); ?>)</i></span></h3>
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