<?php
/*******
 * @package xbJournals Component
 * @filesource script.xbjournals.php
 * @version 0.0.1.2 23rd April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;

class com_xbjournalsInstallerScript 
{	
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    protected $extension = 'com_xbjournals';
    protected $ver = 'v0';
    protected $date = '';
    private $uncatid = 0;
    
    function preflight($type, $parent) {
        $jversion = new Version();
        $jverthis = $jversion->getShortVersion();
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbJournals requires Joomla version greater than '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
        $message='';
        if ($type=='update') {
        	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbjournals/xbjournals.xml'));
        	$this->ver = $componentXML['version'];
        	$this->date = $componentXML['creationDate'];
        	$message = 'Updating xbJournals component from '.$componentXML['version'].' '.$componentXML['creationDate'];
        	$message .= ' to '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
        }
        if ($message!='') { Factory::getApplication()->enqueueMessage($message,'');}
    }
    
    function install($parent) {        
    }
    
    function uninstall($parent) {
        $app = Factory::getApplication();
        
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbjournals/xbjournals.xml'));
        $message = 'Uninstalling xbJournals component v.'.$componentXML['version'].' '.$componentXML['creationDate'];
        //are we also clearing data?
        $savedata = ComponentHelper::getParams('com_xbjournals')->get('savedata',0);
        if ($savedata == 0) {
            if ($this->uninstalldata()) {
                $message .= ' ... xbJournals data tables deleted';
            }
        } else {
            $message .= ' xbJournals data tables and images folder have <b>NOT</b> been deleted. CATEGORIES may be recovered on re-install, but TAG links will be lost although tags have not been deleted.';
            
            // allow categories to be recovered with same id
            $db = Factory::getDbo();
            $db->setQuery(
                $db->getQuery(true)
                ->update('#__categories')
                ->set('extension='.$db->q('!com_xbjournals!'))
                ->where('extension='.$db->q('com_xbjournals'))
                )
                ->execute();
            $cnt = $db->getAffectedRows();
                
            if ($cnt>0) {
                $message .= '<br />'.$cnt.' xbJournals categories [extension] renamed as "<b>!</b>com_xbjournals<b>!</b>". They will be recovered on reinstall with original ids to link with saved xbJournals data.';
            }
        }
        $app->enqueueMessage($message,'Info');
    }
    
    function update($parent) {        
        $message = '<br />Visit the <a href="index.php?option=com_xbjournals&view=dashboard" class="btn btn-small btn-info">';
        $message .= 'xbJournals Dashboard</a> page for overview of status.</p>';
        $message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbjournals/changelog" target="_blank">
            www.crosborne.co.uk/xbjournals/changelog</a></p>';
        Factory::getApplication()->enqueueMessage($message,'Message');
    }
    
    function postflight($type, $parent) {
        if ($type=='install') {
            $app = Factory::getApplication();
            $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbjournals/xbjournals.xml'));
            $message = '<b>xbJournals '.$componentXML['version'].' '.$componentXML['creationDate'].'</b><br />';
                        
            //create xbjournals image folder
            if (!file_exists(JPATH_ROOT.'/images/xbjournals')) {
                mkdir(JPATH_ROOT.'/images/xbjournals',0775);
                $message .= 'Journals images folder created (/images/xbjournals/).<br />';
            } else{
                $message .= '"/images/xbjournals/" already exists.<br />';
            }
            
            // Recover categories if they exist assigned to extension !com_xbfilms!
            $db = Factory::getDbo();
            $qry = $db->getQuery(true);
            $qry->update('#__categories')
            ->set('extension='.$db->q('com_xbjournals'))
            ->where('extension='.$db->q('!com_xbjournals!'));
            $db->setQuery($qry);
            try {
                $db->execute();
                $cnt = $db->getAffectedRows();
            } catch (Exception $e) {
                $app->enqueueMessage($e->getMessage(),'Error');
            }
            $message .= $cnt.' existing xbJournals categories restored. ';
            // create default categories using category table
            $cats = array(
                array("title"=>"Uncategorised","desc"=>"default fallback category for all xbJournal items"));
            //    array("title"=>"Remote","desc"=>"category for remote xbJournal entries"),
            //    array("title"=>"Local","desc"=>"category for locally created xbJournal entries"));
            $message .= $this->createCategories($cats);
            
            $app->enqueueMessage($message,'Info');
            
            //set up indicies for books and bookreviews tables - can't be done in install.sql as they may already exists
            //mysql doesn't support create index if not exists.
            $message = 'Checking indicies... ';
            
            $prefix = $app->get('dbprefix');
            $querystr = 'ALTER TABLE '.$prefix.'xbjournals_servers ADD INDEX serveraliasindex (alias)';
            $err=false;
            try {
                $db->setQuery($querystr);
                $db->execute();
            } catch (Exception $e) {
                if($e->getCode() == 1061) {
                    $message .= '- server alias index already exists. ';
                } else {
                    $message .= '[ERROR creating serveraliasindex: '.$e->getCode().' '.$e->getMessage().']';
                    $app->enqueueMessage($message, 'Error');
                    $message = 'Checking indicies... ';
                    $err = true;
                }
            }
            if (!$err) {
                $message .= '- server alias index created. ';
            }
            $querystr = 'ALTER TABLE '.$prefix.'xbjournals_calendars ADD INDEX calendaraliasindex (alias)';
            $err=false;
            try {
                $db->setQuery($querystr);
                $db->execute();
            } catch (Exception $e) {
                if($e->getCode() == 1061) {
                    $message .= '- calendar alias index already exists. ';
                } else {
                    $message .= '[ERROR creating calendaraliasindex: '.$e->getCode().' '.$e->getMessage().']';
                    $app->enqueueMessage($message, 'Error');
                    $message = 'Checking indicies... ';
                    $err = true;
                }
            }
            if (!$err) {
                $message .= '- calendar alias index created. ';
            }
            $querystr = 'ALTER TABLE '.$prefix.'xbjournals_vjournal_entries ADD INDEX entryaliasindex (alias)';
            $err=false;
            try {
                $db->setQuery($querystr);
                $db->execute();
            } catch (Exception $e) {
                if($e->getCode() == 1061) {
                    $message .= '- entry alias index already exists';
                } else {
                    $message .= '<br />[ERROR creating entryaliasindex: '.$e->getCode().' '.$e->getMessage().']<br />';
                    $app->enqueueMessage($message, 'Error');
                    $message = '';
                    $err = true;
                }
            }
            if (!$err) {
                $message .= '- entry alias index created.';
            }
            $querystr = 'ALTER TABLE '.$prefix.'xbjournals_vjournal_entries ADD INDEX entryuidindex (uid)';
            $err=false;
            try {
                $db->setQuery($querystr);
                $db->execute();
            } catch (Exception $e) {
                if($e->getCode() == 1061) {
                    $message .= '- entry uid index already exists';
                } else {
                    $message .= '<br />[ERROR creating entryuidindex: '.$e->getCode().' '.$e->getMessage().']<br />';
                    $app->enqueueMessage($message, 'Error');
                    $message = '';
                    $err = true;
                }
            }
            if (!$err) {
                $message .= '- entry uid index created.';
            }
            
            
            $app->enqueueMessage($message);
                        
            echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
            echo '<h3>xbJournals Component installed</h3>';
            echo '<p>version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'<br />';
            echo '<p>For help and information see <a href="https://crosborne.co.uk/xbjournals/doc" target="_blank">
	            www.crosborne.co.uk/xbjournals/doc</a> or use Help button in xbJournals Dashboard</p>';
            echo '<h4>Next steps</h4>';
           echo '<p><b>Important</b> Before starting review &amp; set the component options&nbsp;&nbsp;';
            echo '<a href="index.php?option=com_config&view=component&component=com_xbjournals" class="btn btn-small btn-info">xbJournals Options</a>';
            echo '<br /><i>After saving the options you will exit to the Dashboard for an overview</i>';
            echo '</p>';
            echo '<p><b>Dashboard</b> <i>The Dashboard view provides an overview of the component status</i>&nbsp;&nbsp;:';
            echo '<a href="index.php?option=com_xbjournals&view=dashboard">xbJournals Dashboard</a> (<i>but save the options first!</i>)';
            echo '</p>';
            echo '</div>';
        }
    }
 
 	protected function uninstalldata() {
 	    $message = '';
 	    $db = Factory::getDBO();
 	    $db->setQuery('DROP TABLE IF EXISTS `#__xbjournals_calendars`, `#__xbjournals_servers`, `#__xbjournals_vjournal_entries`, `#__xbjournals_vjournal_attachments`');
 	    $res = $db->execute();
 	    if ($res === false) {
 	        $message = 'Error deleting xbJournals tables, please check manually';
 	        Factory::getApplication()->enqueueMessage($message,'Error');
 	        return false;
 	    }
 	    return true;
 	}
 	
 	public function createCategories($cats) {
 	    $message = 'Creating '.$this->extension.' categories. ';
 	    foreach ($cats as $cat) {
 	        $db = Factory::getDBO();
 	        $query = $db->getQuery(true);
 	        $query->select('id')->from($db->quoteName('#__categories'))
 	        ->where($db->quoteName('title')." = ".$db->quote($cat['title']))
 	        ->where($db->quoteName('extension')." = ".$db->quote('com_xbjournals'));
 	        $db->setQuery($query);
 	        if ($db->loadResult()>0) {
 	            $message .= '"'.$cat['title'].' already exists<br /> ';
 	        } else {
 	            $category = Table::getInstance('Category');
 	            $category->extension = $this->extension;
 	            $category->title = $cat['title'];
 	            if (array_key_exists('alias', $cat)) { $category->alias = $cat['alias']; }
 	            $category->description = $cat['desc'];
 	            $category->published = 1;
 	            $category->access = 1;
 	            $category->params = '{"category_layout":"","image":"","image_alt":""}';
 	            $category->metadata = '{"page_title":"","author":"","robots":""}';
 	            $category->language = '*';
 	            // Set the location in the tree
 	            $category->setLocation(1, 'last-child');
 	            // Check to make sure our data is valid
 	            if ($category->check()) {
 	                if ($category->store(true)) {
 	                    // Build the path for our category
 	                    $category->rebuildPath($category->id);
 	                    $message .= $cat['title'].' id:'.$category->id.' created ok. ';
 	                    if  ($category->alias == 'uncategorised') $this->uncatid = $category->id;
 	                } else {
 	                    //throw new Exception(500, $category->getError());
 	                    Factory::getApplication()->enqueueMessage($category->getError(),'Error');
 	                }
 	            } else {
 	                //throw new Exception(500, $category->getError());
 	                Factory::getApplication()->enqueueMessage($category->getError(),'Error');
 	            }
 	        }
 	    }
 	    return $message;
 	}
 	
 	public function createLocals() {
        $db = Factory::getDbo();
 	    $query = $db->getQuery(true);
 	    $query->insert($db->qn('#__xbjournals_servers'))
            ->columns(`title`, `alias`, `description`, `access`, `state`, `created`, `created_by`, `created_by_alias`,
                `modified`, `modified_by`, `checked_out`, `checked_out_time`, `ordering`, `note`)
            ->values('Local Server', 'local-server', 'Not synchronized with any server', '1', '1', current_timestamp(), '0', '', 
                NULL, '0', '0', NULL, '0', 'Created by xbJournals install. Do not delete.');	    
  	    $db->setQuery($query);
  	    try {
  	        $db->execute();
  	        $localserver = $db->insertid();
  	    } catch (Exception $e) {
  	        Factory::getApplication()->enqueueMessage('Failed to create local server entry'.'<br />'.$e->getMessage(),'Warning');
  	        return false;
  	    }
  	    $query->clear();
  	    $query->insert($db->qn('#__xbjournals_calendars'))
            ->columns(`server_id`, `cal_displayname`, `cal_url`, `cal_ctag`, `cal_calendar_id`, `cal_rgb_color`, `cal_order`, `cal_components`,
                `title`, `alias`, `description`,
                `catid`, `access`, `state`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `metadata`, `note`)
                ->values($db->q($localserver), 'Local Calendar', '', 'https://mydomain/1', 'asdf', '#ccc', '0', 'VJOURNAL',
                    'Local Calendar', 'local-calendar', 'For local journals and notebooks. Not a CalDAV calendar',
                    $db->q($this->uncatid), '1', '1', current_timestamp(), '0', '', NULL, '0', '0', NULL, '0', 'Created by xbJournals install. Do not delete.');
        $db->setQuery($query);
        try {
            $db->execute();
            $localcal = $db->insertid();
        } catch (Exception $e) {
            Factory::getApplication()->enqueueMessage('Failed to create local calendar entry'.'<br />'.$e->getMessage(),'Warning');
            return false;
        }
        return $localcal;
 	}
}
