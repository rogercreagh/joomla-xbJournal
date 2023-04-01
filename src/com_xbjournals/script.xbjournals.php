<?php
/*******
 * @package xbJournals
 * @filesource script.xbjournals.php
 * @version 0.0.0.1 1st April 2023
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

class com_xbfilmsInstallerScript 
{	
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    protected $extension = 'com_xbjournals';
    protected $ver = 'v0';
    protected $date = '';
    
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
        //clear the packageuninstall flag if it is set
        $oldval = Factory::getSession()->clear('xbpkg');
        
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbjournals/xbjournals.xml'));
    	$message = 'Uninstalling xbJournals component v.'.$componentXML['version'].' '.$componentXML['creationDate'];
    	//are we also clearing data?
    	$savedata = ComponentHelper::getParams('com_xbjournals')->get('savedata',0);
    	if ($savedata == 0) {
    	    if ($this->uninstalldata()) {
    	        $message .= ' ... xbJournals data tables deleted';
    	    }
     	} else {
    	    $message .= ' xbJournals data tables have <b>NOT</b> been deleted. CATEGORIES may be recovered on re-install, but TAG links will be lost although tags have not been deleted.';

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
    	            $message .= '<br />'.$cnt.' xbJournals category extensions renamed as "<b>!</b>com_xbjournals<b>!</b>". They will be recovered on reinstall with original ids.';
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
/**************
     	$delfiles = '';
    	//since v1.0.1
    	$delfiles .= '/models/fields/allpeople.php,/models/fields/filmpeople.php,/models/fields/catsubtree.php,/models/fields/characters.php';
        $delfiles .= ',models/fields/film.php';
        //reset above after v1.2.0
        $delfiles = explode(',',$delfiles);
        $cnt = 0; $dcnt=0;
    	$ecnt = 0;
    	$message = 'Deleting Redundant Files in '.JPATH_ROOT.'/[administrator/]components/com_xbfilms/ <br />';
    	foreach ($delfiles as $f) {
    	    if (substr($f,0,1)=='/') {
    	        $name = JPATH_ROOT.'/components/com_xbfilms'.$f;
    	    } else {
    	        $name = JPATH_ADMINISTRATOR.'/components/com_xbfilms/'.$f;
    	    }
    	    if (file_exists($name)) {
    	        if (is_dir($name)) {
    	            if ($this->rrmdir($name)) {
    	                $dcnt ++;
        	               $message .= 'RMDIR '.$f.'<br />';
    	            }
    	        } else {
    	            if (unlink($name)) {
        	            $message .= 'DEL '.$f.'<br />';
    	                $cnt ++;
    	            } else {
    	                $message .= 'DELETE FAILED: '.$f.'<br />';
    	                $ecnt ++;
    	            }
    	        }
    	    } else {
        	  //  $message .= 'FILE NOT FOUND: '.$f.'<br />';
    	    }
    	}
    	if (($cnt+$ecnt+$dcnt)>0) {
    	    $message .= $cnt.' files, '.$dcnt.' folders cleared';
    	    $mtype = ($ecnt>0) ? 'Warning' : 'Message';
    	    Factory::getApplication()->enqueueMessage($message, $mtype);
    	}
 ***********************/    
    }
    
    function postflight($type, $parent) {
    	if ($type=='install') {
    	    $app = Factory::getApplication();
    	    $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbjournals/xbjournals.xml'));
    		$message = '<b>xbJournals '.$componentXML['version'].' '.$componentXML['creationDate'].'</b><br />';
    		       	
         	// Recover categories if they exist assigned to extension !com_xbjournals!
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
         	$message .= $cnt.' existing xbJournal categories restored. ';
         	// create default categories using category table
         	$cats = array(
         		array("title"=>"Uncategorised","desc"=>"default fallback category for all xbJournals items"));
         	$message .= $this->createCategory($cats);
         	
         	$app->enqueueMessage($message,'Info');  

	        // we assume people default categories are already installed by xbpeople
	        // we assume that indicies for xbpersons and xbcharacter tables have been handled by xbpeople install
	        //set up indicies for xbjournals tables - can't be done in install.sql as they may already exists
	        //mysql doesn't support create index if not exists.
	        $message = 'Checking indicies... ';
	        
	        $prefix = $app->get('dbprefix');
	        $querystr = 'ALTER TABLE '.$prefix.'xbjournals ADD INDEX journalaliasindex (alias)';
	        $err=false;
	        try {
	            $db->setQuery($querystr);
	            $db->execute();
	        } catch (Exception $e) {
	            if($e->getCode() == 1061) {
	                $message .= '- journal alias index already exists. ';
	            } else {
	                $message .= '[ERROR creating journalaliasindex: '.$e->getCode().' '.$e->getMessage().']';
	                $app->enqueueMessage($message, 'Error');
	                $message = 'Checking indicies... ';
	                $err = true;
	            }
	        }
	        if (!$err) {
	            $message .= '- journal alias index created. ';
	        }
	        $querystr = 'ALTER TABLE '.$prefix.'xbjournalentries ADD INDEX entryaliasindex (alias)';
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
	            $message .= '- jornalentry alias index created.';
	        }
	        
	        $app->enqueueMessage($message,'Info');
	        
	        
	              
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
	        echo '</div>';
    	}
	}
     
	public function createCategory($cats) {
		$message = 'Creating '.$this->extension.' categories. ';
		foreach ($cats as $cat) {
			$db = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id')->from($db->quoteName('#__categories'))
			->where($db->quoteName('title')." = ".$db->quote($cat['title']))
			->where($db->quoteName('extension')." = ".$db->quote('com_xbfilms'));
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
					} else {
						throw new Exception(500, $category->getError());
						//return '';
					}
				} else {
					throw new Exception(500, $category->getError());
					//return '';
				}
			}
		}
		return $message;
	}
	
	protected function uninstalldata() {
	    $message = '';
	    $db = Factory::getDBO();
	    $db->setQuery('DROP TABLE IF EXISTS `#__xbjournals`, `#__xbjournalentries`, `#__xbjournalentryitems`');
	    $res = $db->execute();
	    if ($res === false) {
	        $message = 'Error deleting xbFilms tables, please check manually';
	        Factory::getApplication()->enqueueMessage($message,'Error');
	        return false;
	    }
	    return true;
	}
	
	protected function rrmdir($dir) {
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (filetype($dir."/".$object) == "dir") {
	                    $this->rrmdir($dir."/".$object);
	                } else {
	                    unlink($dir."/".$object);
	                }
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	        return true;
	    }
	    return false;
	}
}
