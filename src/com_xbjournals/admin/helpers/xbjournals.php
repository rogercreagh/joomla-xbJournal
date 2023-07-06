<?php
/*******
 * @package xbJournals
 * @filesource admin/helpers/xbjournals.php
 * @version 0.1.0.1 6th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
//use Joomla\String\StringHelper;


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
//	    if ($vName != 'categories') {
	        JHtmlSidebar::addEntry(
                Text::_('XBJOURNALS_ICONMENU_DASHBOARD'),
                'index.php?option=com_xbjournals&view=dashboard',
                $vName == 'dashboard'
    	        );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_SERVERS'),
    		    'index.php?option=com_xbjournals&view=servers',
    		    $vName == 'servers'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_NEWSERVER'),
    		    'index.php?option=com_xbjournals&view=server&layout=edit',
    		    $vName == 'server'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_CALENDARS'),
    		    'index.php?option=com_xbjournals&view=calendars',
    		    $vName == 'calendars'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_JOURNALS'),
    		    'index.php?option=com_xbjournals&view=journals',
    		    $vName == 'journals'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_NOTEBOOKS'),
    		    'index.php?option=com_xbjournals&view=notes',
    		    $vName == 'notes'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_ATTACHMENTS'),
    		    'index.php?option=com_xbjournals&view=attachments',
    		    $vName == 'attachments'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_CATEGORIES'),
    		    'index.php?option=com_xbjournals&view=jcategories',
    		    $vName == 'jcategories'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_NEWCAT'),
    		    'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbjournals',
    		    $vName == 'category'
    		    );
    		JHtmlSidebar::addEntry(
    		    Text::_('XBJOURNALS_ICONMENU_EDITCATS'),
    		    'index.php?option=com_categories&view=categories&extension=com_xbjournals',
    		    $vName == 'categories'
    		    );
//	    } else {
	        
//	    }
	}
    
	/**
	 * @name checkDataExists()
	 * @desc checks (case insensitive) if a given title (or other text column) value exists in a given db table
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
	 * @param $serverid
	 */
	public static function getServerCalendars($serverid, $ret = 'cnts') {
	    
	    $msg = '<p>Calendars found on Server #'.$serverid.'</p>';
	    $clist = '<ul>';
	    $cnts = array('new'=>0, 'update'=>0, 'same'=>0, 'novj'=>0, 'arch'=>0, 'tot'=>0);
	    
	    $conn = self::getServerConnectionDetails($serverid);
	    
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbcaldav/xbVjournalHelper.php';
	    
	    $client = new xbVjournalHelper();
	    
	    $client->connect($conn['url'],$conn['username'],$conn['password']);
	    
	    $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
	    $cnts['tot'] = count($arrayOfCalendars);
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $scalids = array();
	    foreach ($arrayOfCalendars as $cal) {
	        $calurl = $cal->getURL();
	        $calid = $cal->getCalendarID();
	        $calname = $cal->getDisplayName();
	        $clist .= '<li><b>'.$calname.'</b>';
	        $alias = OutputFilter::stringURLSafe(strtolower($calname));
	        $calctag = $cal->getCTag();
	        $calorder = $cal->getOrder();
	        $calrgb = $cal->getRBGcolor();
	        $calcomps = $cal->getComponents();
	        $vjok = true;
	        $note = '';
	        if (strpos($calcomps,'VJOURNAL') ===false) {
	            $vjok = false;
	            $clist .= ' - <span class="xbit xbhlt">'.Text::_('VJOURNAL not enabled').'</span>';	
	            $cnts['novj'] ++;
	        }
	        $clist .= '</li>';
	        if ($vjok) {
    	        $query->clear();
    	        $query->select('id, cal_ctag')->from('#__xbjournals_calendars');
    	        $query->where('cal_url = '.$db->q($calurl).' AND cal_calendar_id = '.$db->q($calid));
    	        $db->setQuery($query);
    	        $res = $db->loadAssoc();
    	        if ($res['id']>0) {
    	            $scalids[] = $res['id'];
    	            //check if it has changed, if so update
    	            if ($res['cal_ctag'] != $calctag) {
    	                //set modified to now
    	                $query->clear;
    	                $query->update($db->qn('#__xbjournals_calendars'))
    	                ->set($db->qn('cal_displayname').' = '.$db->q($calname))
    	                ->set($db->qn('cal_url').' = '.$db->q($calurl))
    	                ->set($db->qn('cal_ctag').' = '.$db->q($calctag))
    	                ->set($db->qn('cal_calendar_id').' = '.$db->q($calid))
    	                ->set($db->qn('cal_rgb_color').' = '.$db->q($calrgb))
    	                ->set($db->qn('cal_order').' = '.$db->q($calorder))
    	                ->set($db->qn('cal_components').' = '.$db->q($calcomps))
    	                ->set($db->qn('title').' = '.$db->q($calname))
    	                ->set($db->qn('alias').' = '.$db->q($alias))
    //	                ->set($db->qn('description').' = '.$db->q($desc))
    	                ->set($db->qn('state').' = '.$db->q($pub))
    	                ->set($db->qn('modified').' = '.$db->q(date('Y-m-d H:i:s')));
    	                $query->where($db->qn('id').' = '.$db->q($res['id']));
    	                $db->setQuery($query);
    	                try {
    	                    $db->execute();
    	                } catch (Exception $e) {
    	                    $this->doError('Error updating calendar in database',$e);
    	                }	                	                
    	                $cnts['update']++;
    	            } else {
    	                $cnts['same'] ++;
    	            }
    	        } else { //we need to add it, set created date to now
    	            $query->clear();
    	            $query->insert($db->quoteName('#__xbjournals_calendars'));
    	            $query->columns('server_id,cal_displayname,cal_url,cal_ctag,cal_calendar_id,'
    	                .'cal_rgb_color,cal_order,cal_components,title,alias,access,state,created');
    	            $query->values($db->q($serverid).','.$db->q($calname).','.$db->q($calurl).','.$db->q($calctag).','.$db->q($calid)
    	                .','.$db->q($calrgb).','.$db->q($calorder).','.$db->q($calcomps).','.$db->q($calname).','
    	                .$db->q($alias).','.$db->q('1').','.$db->q('1').','.$db->q(date('Y-m-d H:i:s')));
    	            //try
    	            $db->setQuery($query);
    	            $db->execute();
    	            $scalids[] = $db->insertid();	            
    	            $cnts['new'] ++;
    	        }   	        
	        } //endif vjok	        
	    } //end foreach calendar
	    // check if calendars have disappeared from server and unpublish them
	    $query->clear();
	    $query->select('id')->from($db->quoteName('#__xbjournals_calendars'));
	    $db->setQuery($query);
	    $lcalids = $db->loadColumn();
	    if ((!empty($scalids)) && (is_array($lcalids))) {
    	    $lostids = array_diff($lcalids, $scalids);
    	    foreach ($lostids as $cal) {
    	        $query->clear();
    	        $query->update($db->qn('#__xbjournals_calendars'))
    	        ->set($db->qn('state').' = '.$db->q('0')) //set state to unpublished
    	        ->set($db->qn('note').' = '.$db->q(Text::_('Calendar no longer on server')))
    	        ->set($db->qn('modified').' = '.$db->q(date('Y-m-d H:i:s')));
    	        $query->where($db->qn('id').' = '.$db->q($cal));
    	        $db->setQuery($query);
    	        try {
    	            $db->execute();
    	        } catch (Exception $e) {
    	            $this->doError('Error updating calendar state in database',$e);
    	        }  
    	        $cnts['arch'] ++;
    	    }
	        
	    }
	    //update server dates (modified) and note ()
	    $query->clear();	    
	    $query->update($db->qn('#__xbjournals_servers'))
       	    ->set($db->qn('note').' = '.$db->q($cnts['tot'].' calendars, '.$cnts['novj'].' Vjournal not enabled'))
	        ->set($db->qn('modified').' = '.$db->q(date('Y-m-d H:i:s')));
	    $query->where($db->qn('id').' = '.$db->q($serverid));
	    $db->setQuery($query);
	    try {
	        $db->execute();
	    } catch (Exception $e) {
	        $this->doError('Error updating server state in database',$e);
	    }  
	    if ($ret == 'list') {
	        $msg .= $cnts['new'].' new calendars added, '.$cnts['update'].' updated, '.$cnts['same'].' unchanged, '.$cnts['arch'].' archived, '.$cnts['novj'].' no VJOURNAL';
	        $msg .= $clist;
	        return $msg;
	    }
	    return $cnts;
	}
	
	/**
	 * @name listServerCalendars()
	 * @desc Checks given server for a list of available calendars
	 * @param $serverid
	 * @return string html list with title
	 */
	public static function listServerCalendars($serverid) {
	    
	    $conn = self::getServerConnectionDetails($serverid);
	    
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbcaldav/xbVjournalHelper.php';
	    
	    $client = new xbVjournalHelper();
	    
	    $client->connect($conn['url'],$conn['username'],$conn['password']);
	    
	    $arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
	    $clist = '<p>Calendars available on Server #'.$serverid.'</p><ul>';
	    foreach ($arrayOfCalendars as $cal) {
//	        $calurl = $cal->getURL();
//	        $calid = $cal->getCalendarID();
	        $clist .= '<li><b>'.$cal->getDisplayName().'</b>';
//	        $alias = OutputFilter::stringURLSafe(strtolower($calname));
//	        $calctag = $cal->getCTag();
//	        $calorder = $cal->getOrder();
//	        $calrgb = $cal->getRBGcolor();
	        $calcomps = $cal->getComponents();
	        if (strpos($calcomps,'VJOURNAL') ===false ) {
	            $clist .= ' <span class="xbit xbhlt">VJOURNAL not enabled</span>';
	        }
	        $clist.='</li>';	        
	    } //end foreach calendar
	    $clist .= '</ul>';
	    return $clist;
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
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbcaldav/xbCalDAVClient.php';
	    
	    $client = new xbCalDAVClient($url, $user, $pword);
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
	    //incomplete - what is this to do?
	}
	
	/**
	 * @name getCalendarVJournalItems()
	 * 
	 * @param int $calid
	 * @return array[]
	 */
	public static function getCalendarVJournalItems(int $calid, $start = null, $end = null, $dateprop = '', $type = '') {
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbjournals/helpers/xbcaldav/xbVjournalHelper.php';
	    $cal = self::getCalendarDetaisl($calid);
	    $conn = self::getServerConnectionDetails($cal['server_id']);
	    $calhelper = new xbVjournalHelper();
	    $calhelper->connect($conn['url'],$conn['username'],$conn['password']);
	    $calhelper->setCalendarByUrl($cal['cal_url']);
	    switch ($type) {
   	        case 'journals':
	            $calitems = $calhelper->getJournals($start, $end);
    	        break;
   	        case 'notes':
   	            $calitems = $calhelper->getNotes($start, $end);
   	            break;
	        default:
        	    $calitems = $calhelper->getVjournals($start, $end, $dateprop);
	        break;
	    }
	    $journalentries = array();	    
	    foreach ($calitems as $calitem) {
	        $journalentry = $calhelper->parseVjournalObject($calitem);
	        
	        if (!empty($journalentry)) $journalentries[] = $journalentry;
	    }
//	    Factory::getApplication()->enqueueMessage('<pre>'.print_r($journalentries[0],true).'</pre><pre>'.print_r($journalentries[count($journalentries)-1],true).'</pre>');
	    return $journalentries;
	}
	
	public static function getCalendarDetaisl($calid) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('*')->from('#__xbjournals_calendars')->where('id = '.$db->quote($calid));
	    $db->setQuery($query);
	    $ans = $db->loadAssoc();
	    return $ans;
	    
	}

	public static function getVjournalParent(string $parentuid) {
        $parent = self::getObjFromCol('#__xbjournals_vjournal_entries','uid',$parentuid);
	    return '<a href="index.php?option=xbjournals&view=vjournal&id='.$parent->id.'">'.$parent->title.' ('.$parent->entry_type.' #'.$parent->id.')</a>'; //title might not be unique
	}
	
	public static function getVjournalSubitems(string $itemuid) {
	   $list = '';
	   $db = Factory::getDbo();
	   $query = $db->getQuery(true);
	   $query->select('id, title')->from($db->qn('#__xbjournals_vjournal_entries'))
	       ->where($db->qn('parentuid').' = '.$db->q($itemuid));
	   $db->setQuery($query);
	   $kids = $db->loadObjectList();
	   if ($kids) {
	       $list = '<ul>';
	       foreach ($kids as $kid) {
	           if ($kid->title == '') $kid->title = '(untitled)';
	           $list .= '<li><a href="index.php?option=xbjournals&view=journal&id='.$kid->id.'">'.$kid->title.'</a></li>';
	       }
	       $list .= '</ul>';
	   }	   
	   return $list;
	}
	
	public static function getVjournalAttachments(int $itemid) {
	    $list = '';
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('uri, filename, label, localpath')
	       ->from($db->qn('#__xbjournals_vjournal_attachments'))
	       ->where('entry_id = '. $itemid);
	    $db->setQuery($query);
	    $atts = $db->loadObjectList();
	    if ($atts) {
	        $list = '<ul>';
	        foreach ($atts as $at) {
	            $path = '';
	            $list .= '<li>';
	            if ($at->localpath) {
	                $path = $at->localpath;
	            } elseif ($at->uri) {
	                $path = $at->uri;
	            }
	            $name =  ($at->label) ? $at->label : $at->filename;
	            if ($path) {
	                $list .= '<a href="'.$path.'" target="_blank">'.$name.'</a>';
	            } else {
	                $list .= $name;
	            }
	            $list .= ' </li>';
	        }
	        $list .= '</ul>';
	    }
	    return $list;	        
	}
	
	/** 
	 * @name vCalDate2SqlDate()
	 * @desc converts a Vcal date string to an sql compatible string with time set to zeroes if not in input
	 * @param string $datestr -should be YYYYMMDD or YYYYMMDDTHHMMSS or YYYYMMDDTHHMMSSZ
	 * @return string - Y-m-d H:i:s
	 */
	public static function vCalDate2SqlDate (string $datestr) {
	    //input 
	    //output should be Y-m-d H:i:s (always datetime, with 00:00:00 if date only)
	    //TODO timezones
	    $datestr = str_replace('T',' ',$datestr);
	    // $datestr = str_replace(array('T','Z'), ' ', $datestr);
	    if (strlen($datestr) < 14) $datestr .= '000000';
	    //convert string to sql date format
	    $datestr = trim($datestr,' Z');
	    $date = DateTime::createFromFormat('Ymd His',$datestr);
	    $datestr = $date->format('Y-m-d H:i:s');
	    return $datestr;
	}
	
	/** 
	 * @name date2VcalDate()
	 * @desc converts any valid date to a Vcal compatible date or datetie string
	 * BEWARE ambiguous date strings (eg using unexpected day-month ordering) may produce unexpected results
	 * BEWARE this function uses unix timstamp for conversion and will fail with dates before 01/01/1970
	 * @param string $datestr - the input string
	 * @param boolean $time - true to include time in output, false for date only
	 * @return string
	 */
	public static function date2VcalDate (string $datestr, $dotime = true) {
	    $datearr = date_parse($datestr);
	    $vcalDate = $datearr['year'].sprintf('%02d',$datearr['month']).sprintf('%02d',$datearr['day']);
	    if ($dotime) {
	        $vcalDate .= 'T'.sprintf('%02d',$datearr['hour']).sprintf('%02d',$datearr['minute']).sprintf('%02d',$datearr['second']).'Z';
	    }
	    return $vcalDate;
	}
	
/***************** functions that could move to xbLibrary *******************************/	
	
	/**
	 * @name checkValueExists()
	 * @desc returns true if given value exists in given table column (case insensitive)
	 * @param string $value - text to check
	 * @param string $table - the table to check in
	 * @param string $col- the column to check
	 * @return boolean - true if value is found in column
	 */
	public static function checkValueExists( $value,  $table, $col) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))
	    ->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($value)));
	    $db->setQuery($query);
	    $res = $db->loadResult();
	    if ($res > 0) {
	        return true;
	    }
	    return false;
	}

	/**
	 * @name getItemCnt
	 * @desc returns the number of items in a table
	 * @param string $table
	 * @return integer
	 */
	public static function getItemCnt($table) {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('COUNT(*)')->from($db->quoteName($table));
	    $db->setQuery($query);
	    $cnt=-1;
	    try {
	        $cnt = $db->loadResult();
	    } catch (Exception $e) {
	        $dberr = $e->getMessage();
	        Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
	    }
	    return $cnt;
	}

	/**
	 * @name deleteFromTable()
	 * @desc deletes items from specified table according to specified condition
	 * @param string $table - the table name
	 * @param string $condition - the text to be in the query WHERE clause
	 * @throws \Exception
	 * @return boolean
	 */
	public static function deleteFromTable(string $table, string $condition) {
	    $db = Factory::getDbo();
	    //delete existing role list
	    $query = $db->getQuery(true);
	    $query->delete($db->quoteName($table));
	    $query->where($condition);
	    $db->setQuery($query);
	    try {
	        $db->execute();
	    }
	    catch (\RuntimeException $e) {
	        throw new \Exception($e->getMessage(), 500);
	        return false;
	    }
	    return true;
	}
	
    public static function truncateToText(string $source, int $maxlen=250, string $split = 'word') { //null=exact|false=word|true=sentence 
        if ($maxlen < 5) return $source; //silly the elipsis '...' is 3 chars
        $action = strpos(' firstsent lastsent word abridge exact',$split); 
        // firstsent = 1 lastsent = 11, word = 20, abridge = 25, exact = 33
        $lastword = '';
        //todo for php8.1+ we could use enum
	    if (!$action) return $source; //invalid $split value
	    $source = trim(html_entity_decode(strip_tags($source)));
	    if ((strlen($source)<$maxlen) && ($action > 19)) return $source; //not enough chars anyway
	    $maxlen = $maxlen - 4; // allow space for ellipsis
	    // for abridge we'll save the last word to add back preceeded by ellipsis after truncating
	    if ($action == 25) {
	        $lastspace = strrpos($source, ' ');
            $excess = strlen($source) - $maxlen;
	        if ($lastspace && ($lastspace > $maxlen)) {
	            $lastword = substr($source, $lastspace);
	        } else {
	            // no space to get lastword outside maxlen, so just take last 6 chars as lastword	            
	            $lastword = ($excess>6) ? substr($source, strlen($source)-6) : substr($source,strlen($source)-$excess);	            
	        }
	        $maxlen = $maxlen - strlen($lastword);
	    }	    
	    $source = substr($source, 0, $maxlen);
	    //for exact trim at maxlength
	    if ($action == 33) return $source.'...';
	    //for word or abridge simply find the last space and add the ellipsisplus lastword for abridge
	    $lastwordend = strrpos($source, ' ');
	    if ($action > 19) {
    	    if ($lastwordend) {
    	        $source = substr($source,$lastwordend);
    	    }
    	    return $source.'...'.$lastword;
	    }
	    //ok so we are doing first/last complete sentence
	    // get a temp version with '? ' and '! ' replaced by '. '
	    $dotsonly = str_replace(array('! ','? '),'. ',$source.' ');
	    if ($action == 1) {
	        // look for first ". " as end of sentence
	        $dot = strpos($dotsonly,'. ');
	    } else {
	        // look for last ". " as end of sentence
	        $dot = strrpos($dotsonly,'. ');
	    }
	    if ($dot !== false) {
	        return substr($source, 0, $dot+1).'...';
	        
	    }
	    return $source;
	}
	
	public static function truncateHtml(string $source, int $maxlen=250, bool $wordbreak = true) {
	    if ($maxlen < 10) return $source; //silly the elipsis '...' is 3 chars empire->emp...  workspace-> work... 'and so on' -> 'and so...'
	    $maxlen = $maxlen - 3; //to allow for 3 char ellipsis '...' rather thaan utf8 
	    if (($wordbreak) && (strpos($source,' ') === false )) $wordbreak = false; //nowhere to wordbreak
        $truncstr = substr($source, 0, $maxlen);
	    if (!self::isHtml($source)) {
	        //we can just truncate and find a wordbreak if needed
	        if (!$wordbreak || ($wordbreak) && (substr($source, $maxlen+1,1)== ' ')) {
	            //weve got a word at the end
	            return $truncstr.'...';
	        }
	        //ok we've got to look for a wordbreak (space or newline)
	        $lastspace = strrpos(str_replace("\n"," ",$truncstr),' ');
	        if ($lastspace) { // not if it is notfound or is first character (pos=0)
	            return substr($truncstr, 0, $lastspace).'...';
	        }
	        // still here - no spaces left in truncstr so return it all
	        return $truncstr.'...';
	    }
	    //ok so it is html
	    //get rid of any unclosed tag at the end of $truncstr
	    // Check if we are within a tag, if we are remove it
	    if (strrpos($truncstr, '<') > strrpos($truncstr, '>')) {
	        $lasttagstart = strrpos($truncstr, '<');
	        $truncstr = trim(substr($truncstr, 0, $lasttagstart));
	    }
	    $testlen = strlen(trim(html_entity_decode(strip_tags($truncstr))));
	    while ( $testlen > $maxlen ) {
	        $toloose = $testlen - $maxlen;
	        $trunclen = strlen($truncstr);
	        $endlasttag = strrpos($truncstr,'>');
	        if (($trunclen - $endlasttag) >= $toloose) {
	            $truncstr = substr($truncstr, $trunclen - $toloose);
	        } else {
	            //we need to remove another tag
	            $lasttagstart = strrpos($truncstr,'<');
	            if ($lasttagstart) {
	                $truncstr = substr($truncstr, 0, $lastagstart);
	            } else {
	                $truncstr = substr($truncstr, 0, $maxlen);
	            }
	        }
	        $testlen = strlen(trim(html_entity_decode(strip_tags($truncstr))));
	    }
	    if (!$wordbreak) return $truncstr.'...';
	    $lastspace = strrpos(str_replace("\n",' ',$truncstr),' ');
	    if ($lastspace) {
	        $truncstr = substr($truncstr, 0, $lastspace);
	    }
	    return $truncstr.'...';
	}
	
	/**
	 * 
	 * @param string $test
	 * @return number
	 */
	public static function isHtml(string $test) {
	    // regex for self-closed tag (<[a-z]+?[^>]*?\/>) eg <br />
	    //regex for closed tag <[a-z]+?[^<]*? >[^<]*?<\/.*? > eg <p>para</p>
	    return preg_match("<[a-z]+?[^<]*?>[^<]*?<\/.*?>|<[a-z]+?[^>]*?\/>",$test);
	}
	
	public static function removeHtml(string $source, bool $replents = true) {
	    $plainstr = $source;
	    //remove self closed tags complete
	    preg_replace('/<[a-z]+?[^>]*?\/>/i','',$plainstr);
	    //remove non-self closed tags maintaining content
	    preg_replace('/<[a-z]+?[^<]*?>([^<]*?)<\/.*?>/i','$1',$plainstr);
	    //if we have a broken tag or unclosed tag it will not be replaced
	    if ($replents) $plainstr = html_entity_decode($plainstr); 
	    return $plainstr;
	}
	
	public static function penPont() {
	    $params = ComponentHelper::getParams('com_xbjournals');
	    $beer = trim($params->get('roger_beer'));
	    //Factory::getApplication()->enqueueMessage(password_hash($beer));
	    $hashbeer = $params->get('penpont');
	    if (password_verify($beer,$hashbeer)) { return true; }
	    return false;
	}
	
	
	/***
	 * @name checkComponent()
	 * @desc test whether a component is installed and enabled.
	 * NB This sets the seesion variable if component installed to 1 if enabled or 0 if disabled.
	 * Test sess variable==1 if wanting to use component
	 * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
	 * @return boolean|number - true= installed and enabled, 0= installed not enabled, null = not installed
	 */
	public static function checkComponent($name) {
	    $sname=substr($name,4).'_ok';
	    $sess= Factory::getSession();
	    $db = Factory::getDbo();
	    $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote($name));
	    $res = $db->loadResult();
	    if (is_null($res)) {
	        $sess->clear($sname);
	    } else {
	        $sess->set($sname,$res);
	    }
	    return $res;
	}
	
	/**
	 * @name credit()
	 * @desc tests if reg code is installed and returns blank, or credit for site and PayPal button for admin
	 * @param string $ext - extension name to display, must match 'com_name' and xml filename and crosborne link page when converted to lower case
	 * @return string - empty is registered otherwise for display
	 */
	public static function credit(string $ext) {
	    if (self::penPont()) {
	        return '';
	    }
	    $lext = strtolower($ext);
	    $credit='<div class="xbcredit">';
	    if (Factory::getApplication()->isClient('administrator')==true) {
	        $xmldata = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_'.$lext.'/'.$lext.'.xml');
	        $credit .= '<a href="http://crosborne.uk/'.$lext.'" target="_blank">'
	            .$ext.' Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a>';
	            $credit .= '<br />'.Text::_('XBJOURNALS_BEER_TAG');
	            $credit .= Text::_('XBJOURNALS_BEER_FORM');
	    } else {
	        $credit .= $ext.' by <a href="http://crosborne.uk/'.$lext.'" target="_blank">CrOsborne</a>';
	    }
	    $credit .= '</div>';
	    return $credit;
	}
	
	/**
	 * @name getExtensionInfo()
	 * @param string $element 'mod_...' or 'com_...' for component or module, for plugin the plugin=string from the xml plus the folder (type of plugin))
	 * @return false if not installed, version string if installed followed but '(not enabled)' if not enabled
	 */
	public static function getExtensionInfo($element, $folder=null) {
	    $db = Factory::getDBO();
	    $qry = $db->getQuery(true);
	    $qry->select('enabled, manifest_cache')
	    ->from($db->quoteName('#__extensions'))
	    ->where('element = '.$db->quote($element));
	    if ($folder) {
	        $qry->where('folder = '.$db->quote($folder));
	    }
	    $db->setQuery($qry);
	    $res = $db->loadAssoc();
	    if (is_null($res)) {
	        return false;
	    } else {
	        $manifest = json_decode($res['manifest_cache'],true);
	    }
	    $manifest['enabled'] = $res['enabled'];
	    return $manifest;
	}
	
	/**
	 * @name getCat()
	 * @desc given category id returns title and description
	 * @param int $catid
	 * @return object|null
	 */
	public static function getCat($catid) {
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('*')
	    ->from('#__categories AS a ')
	    ->where('a.id = '.$catid);
	    $db->setQuery($query);
	    return $db->loadObject();
	}

	/**
	 * @name getChildCats()
	 * @desc for a given category returns an array of child category ids
	 * @param int $pid - id of the parent category
	 * @param string $ext - the extension the parent belongs to (or null to look it up)
	 * @param boolean $incroot - whether to include the parent id in the return array
	 * @return array of ids
	 */
	public static function getChildCats(int $pid, $ext = null, $incroot = true) {
	    $db    = Factory::getDbo();
	    $query = $db->getQuery(true);
	    if (is_null($ext)) {
	        $query->select($db->quoteName('extension'))
	        ->from($db->quoteName('#__categories')
	            ->where($db->quoteName('id').'='.$pid));
	        $ext = $db->loadResult();
	    }
	    $query->clear();
	    $query->select('*')->from('#__categories')->where('id='.$pid);
	    $db->setQuery($query);
	    $pcat=$db->loadObject();
	    $start = $incroot ? '>=' : '>';
	    $query->clear();
	    $query->select('id')->from('#__categories')->where('extension = '.$db->quote($ext));
	    $query->where(' lft'.$start.$pcat->lft.' AND rgt <='.$pcat->rgt);
	    $db->setQuery($query);
	    return $db->loadColumn();
	}
		
	/**
	 * @name getTag()
	 * @desc gets a tag's details given its id
	 * @param (int) $tagid
	 * @return mixed
	 */
	public static function getTag($tagid) {
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('*')
	    ->from('#__tags AS a ')
	    ->where('a.id = '.$tagid);
	    $db->setQuery($query);
	    return $db->loadObject();
	}
	
	/**
	 * @name createCategory()
	 * @desc creates a new category if it doesn't exist, returns id of category
	 * NB passing a name and no alias will check for alias based on name.
	 * @param (string) $name for category
	 * @param string $alias - usually lowercase name with hyphens for spaces, must be unique, will be created from name if not supplied
	 * @param number $parentid - id of parent category (defaults to root
	 * @param string $ext - the extension owning the category
	 * @param string $desc - optional description
	 * @return integer - id of new or existing category, or false if error. Error message is enqueued
	 */
	public static function createCategory($name, $alias='',$parentid = 1,  $ext='com_xbjournals', $desc='' ) {
	    if ($alias=='') {
	        //create alias from name
	        $alias = OutputFilter::stringURLSafe(strtolower($name));
	    }
	    //check category doesn't already exist
	    $catid = self::getCatIdFromAlias('#__categories',$alias, $ext);
	    if ($catid>0) {
	        return $catid;
	    } else {
	        $db = Factory::getDbo();
	        $query = $db->getQuery(true);
	        //get category model
	        $basePath = JPATH_ADMINISTRATOR.'/components/com_categories';
	        require_once $basePath.'/models/category.php';
	        $config  = array('table_path' => $basePath.'/tables');
	        //setup data for new category
	        $category_model = new CategoriesModelCategory($config);
	        $category_data['id'] = 0;
	        $category_data['parent_id'] = $parentid;
	        $category_data['published'] = 1;
	        $category_data['language'] = '*';
	        $category_data['params'] = array('category_layout' => '','image' => '');
	        $category_data['metadata'] = array('author' => '','robots' => '');
	        $category_data['extension'] = $ext;
	        $category_data['title'] = $name;
	        $category_data['alias'] = $alias;
	        $category_data['description'] = $desc;
	        if(!$category_model->save($category_data)){
	            Factory::getApplication()->enqueueMessage('Error creating category: '.$category_model->getError(), 'error');
	            return false;
	        }
	        $id = $category_model->getItem()->id;
	        return $id;
	    }
	}
	
	/**
	 * @name getCatIdFromAlias()
	 * @desc given a table name and an alias string returns the id of the corresponding item
	 * @param (string) $table
	 * @param (string) $alias
	 * @param string $ext
	 * @return mixed|void|NULL
	 */
	public static function getCatIdFromAlias($table,$alias, $ext = 'com_xbjournals') {
	    $alias = trim($alias,"' ");
	    $table = trim($table,"' ");
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))
	       ->where($db->quoteName('alias')." = ".$db->quote($alias))
	       ->where($db->quoteName('extension')." = ".$db->quote($ext));
	    $db->setQuery($query);
	    $res =0;
	    $res = $db->loadResult();
	    return $res;
	}
	
    /**
     * @name getIdFromCol()
     * @desc returns an object of the first item in the table having the column value
     * Intended for use with a column with unique values
     * NB parameter values are assumed to be clean and trimmed.
     * @param string $table - the table to search
     * @param string $col - the colun to search in
     * @param unknown $value - the value to search for. If a string escape any quotes
     * @return mixed|void|mixed[]
     */
	public static function getObjFromCol(string $table, string $col, $value) {
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query->select('*')->from($db->quoteName($table))->where($db->quoteName($col)." = ".$db->quote($value));
	    $db->setQuery($query);
	    $res = $db->loadObject();
	    return $res;
	}
	
	/**
	 * @name checkTitleExists()
	 * @desc returns true if given title exists in given table (case insensitive)
	 * If table is xbcharacters then uses name column rather than title
	 * @param string $title
	 * @param string $table
	 * @return boolean
	 */
	public static function checkTitleExists( $title,  $table) {
	    $col = ($table == '#__xbcharacters') ? 'name' : 'title';
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('id')->from($db->quoteName($table))
	    ->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($title)));
	    $db->setQuery($query);
	    $res = $db->loadResult();
	    if ($res > 0) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * @name sitePageHeader()
	 * @desc builds a page header string from passed data
	 * @param array $displayData
	 * @return string
	 */
	public static function sitePageheader($displayData) {
	    $header ='';
	    if (!empty($displayData)) {
	        $header = '	<div class="row-fluid"><div class="span12 xbpagehead">';
	        if ($displayData['showheading']) {
	            $header .= '<div class="page-header"><h1>'.$displayData['heading'].'</h1></div>';
	        }
	        if ($displayData['title'] != '') {
	            $header .= '<h3>'.$displayData['title'].'</h3>';
	            if ($displayData['subtitle']!='') {
	                $header .= '<h4>'.$displayData['subtitle'].'</h4>';
	            }
	            if ($displayData['text'] != '') {
	                $header .= '<p>'.$displayData['text'].'</p>';
	            }
	        }
	    }
	    return $header;
	}
	
	/**
	 * @name doError()
	 * @desc reports error to user without throwing exception- use for admin db errors in try..catch
	 * @param string $message
	 * @param Exception $e
	 */
	public static function doError(string $message, Exception $e) {
	    Factory::getApplication()->enqueueMessage($message.'<br />' .$e->getMessage().' ('.$e->getCode().')','Error');
	}
	
}
