<?php
/*******
 * @package xbJournals
 * @filesource admin/helpers/xbjournals.php
 * @version 0.0.0.5 4th April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Filter\OutputFilter;

class XbjournalsHelper extends ContentHelper
{
	public static function getActions($component = 'com_xbjournals', $section = 'component', $categoryid = 0) {
	    
	    $user 	=Factory::getUser();
	    $result = new JObject;
	    if (empty($categoryid)) {
	        $assetName = $component;
	        $level = $section;
	    } else {
	        $assetName = $component.'.category.'.(int) $categoryid;
	        $level = 'category';
	    }
	    $actions = Access::getActions('com_xbjournals', $level);
	    foreach ($actions as $action) {
	        $result->set($action->name, $user->authorise($action->name, $assetName));
	    }
	    return $result;
	}
	
	public static function addSubmenu($vName = 'dashboard') {
		JHtmlSidebar::addEntry(
            Text::_('XBCULTURE_ICONMENU_DASHBOARD'),
            'index.php?option=com_xbjournals&view=dashboard',
            $vName == 'dashboard'
	        );
		JHtmlSidebar::addEntry(
		    Text::_('XBCULTURE_ICONMENU_SERVERS'),
		    'index.php?option=com_xbjournals&view=servers',
		    $vName == 'servers'
		    );
		JHtmlSidebar::addEntry(
		    Text::_('XBCULTURE_ICONMENU_NEWSERVER'),
		    'index.php?option=com_xbjournals&view=server&layout=edit',
		    $vName == 'server'
		    );
		JHtmlSidebar::addEntry(
		    Text::_('XBCULTURE_ICONMENU_JOURNALS'),
		    'index.php?option=com_xbjournals&view=journals',
		    $vName == 'journals'
		    );
	}
    
	/**
	 * @name checkDataExists()
	 * @desc checks (case insensitive) if a given title exists in a given db table
	 * @param string $value - the title to search for
	 * @param string $table - the table name to search
	 * @param string $col - the column in the table (default 'title')
	 * @return int - the id if found otherwise false
	 */
	public static function checkDBvalueExists($value, $table, $col = 'title') {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))
	    ->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($value)));
	    $db->setQuery($query);
	    $res = $db->loadResult();
	    if ($res > 0) {
	        return $res;
	    }
	    return false;
	}
	
	/**
	 * @name getServerCalendars()
	 * @desc Checks given server for a list of available calendars
	 * if not in database #__xbjournals_calendars then adds
	 * if already in database then if disabled then publish it
	 * if in database but no longer on server then disable it
	 * @param unknown $serverid
	 */
	public static function getServerCalendars($serverid) {
	    
	    $conn = self::getServerConnectionDetails($serverid);
	    
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbCalDav/SimpleCalDAVClient.php';
	    
	    $client = new SimpleCalDAVClient();
	    
	    $client->connect($conn['url'],$conn['username'],$conn['password']);
	    
	    $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
	    
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $existingcals = array();
	    $newcnt = 0;
	    foreach ($arrayOfCalendars as $cal) {
	        $calurl = $cal->getURL();
	        $calid = $cal->getCalendarID();
	        $calname = $cal->getDisplayName();
	        $calctag = $cal->getCTag();
	        $calorder = $cal->getOrder();
	        $calrgb = $cal->getRBGcolor();
	        
	        $query->clear();
	        $query->select('id')->from('#__xbjournals_calendars');
	        $query->where('cal_url = '.$db->q($calurl).' AND cal_calendar_id = '.$db->q('calid'));
	        $db->setQuery($query);
	        $res = $db->loadResult();
	        //iff we've already got it add to exist list
	        if ($res>0) {
	            $existingcals[] = $res;
	        } else { //we need to add it
	            $query->clear();
	            $query->insert($db->quoteName('#__xbjournals_calendars'));
	            $query->columns('server_id,cal_displayname,cal_url,cal_ctag,cal_calendar_id,cal_rgb_color,cal_order,title,alias,access,state');
	            $query->values($db->q($serverid).','.$db->q($calname).','.$db->q($calurl).','.$db->q($calctag).','.$db->q($calid)
	                .','.$db->q($calrgb).','.$db->q($calorder).','.$db->q($calname).','.$db->q(strtolower($calname)).','.$db->q('1').','.$db->q('1'));
	            //try
	            $db->setQuery($query);
	            $db->execute();
	            $existingcals[] = $db->insertid();
	            $newcnt ++;
	        }
	        //TODO check if calendars have disappeared from server and unpublish them
	        
	    } //end foreach calendar
	    return $newcnt;
	}
	
	public static function getServerConnectionDetails($serverid) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('url, username, password')->from('#__xbjournals_servers')->where('id = '.$db->quote($serverid));
	    $db->setQuery($query);
	    $ans = $db->loadAssoc();
	    return $ans;
	}
	
	public static function checkValidServer(string $url, string $user, string $pword) {
	    
	    $message = '';
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbCalDav/SimpleCalDAVClient.php';
	    
	    $client = new CalDAVClient($url, $user, $pword);
	    // Valid CalDAV-Server? Or is it just a WebDAV-Server?
	    if( ! $client->isValidCalDAVServer() ) {	
	        $res = $client->GetHttpResultCode();
	        switch ($res) {
	            case '401':
	                $message = 'Login failed';
	                break;
	            case '':
	                $message = 'Can\'t reach server';
	                break;
	            default:
	                $message = 'Could\'nt find a CalDAV-collection under the url ('.$res.')';
	                break;
	        }
	        Factory::getApplication()->enqueueMessage($message,'Error');
	        return false;
	    }
	    // Check for errors
	    $res = $client->GetHttpResultCode();
	    if( $res != '200' ) {
    	    switch ($res) {
    	        case '401':
    	            $message .= 'Login failed';
    	            break;
    	        case '':
    	            $message .= 'Can\'t reach server';
    	            break;    	            
    	        default:
    	            $message .= 'Recieved unknown HTTP status while checking the connection after establishing it ('.$res.')';
    	            break;
    	    }
    	    Factory::getApplication()->enqueueMessage($message,'Error');
    	    return false;
	    }	    
	    return true;
	}

	public static function checkVjournalCalendar($calendarid) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('*')->from('#__xbjournals_calendars')->where('id = '.$db->q($calendarid));
	    $db->setQuery($query);
	    $cal = $db->loadObject();
	}
	
}
