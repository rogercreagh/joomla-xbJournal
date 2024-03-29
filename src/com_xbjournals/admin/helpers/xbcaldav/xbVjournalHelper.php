<?php
/*******
 * @package xbcaldav Library
 * @filesource admin/helpers/xbcaldav/xbVjournalHelper.php
 * @version 0.0.7.1 4th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * based on SimpleCalDavClient by Michael Palm <palm.michael@gmx.de>
 * portions copyright (c) Michael Palm <palm.michael@gmx.de>, 2014
 * original source https://github.com/
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 * @desc class to encapsulate the CalDAV functions needed for VJournal operations
 * 
 ******/

// No direct access to this file
defined('_JEXEC') or die;
//comment the above line out if not using within Joomla CMS

/** this library is based on SimpleCalDAVClient by Michael Palm (c) 2014 Michael Palm <palm.michael@gmx.de> 
 * SimpleCalDavClient library was heavily based on AgenDAV caldav-client-v2.php by Jorge L�pez P�rez <jorge@adobo.org> which
 * again is heavily based on DAViCal caldav-client-v2.php by Andrew McMillan <andrew@mcmillan.net.nz>.
 * We stand on the shoulders of giants.
 * 
 * This version provides core functions for communicating with a CalDAV server to manage VJOURNAL components
 * VJOURNAL is like VEVENT but entries typically do not have an end date (DTEND), it is just attached to a day
 * If an VJOURNAL entry has no DTSTART then it is treated as a NOTE which does not belong to a particular date.
 * There is no separate component type for VNOTE, it is just a VJOURNAL without a DTSTART property, but in this library they
 * are identified on reading from the server and treated separately.
 * 
 *   
 **/

use Joomla\CMS\Factory;

require_once 'xbCalDAVClient.php';
require_once('CalDAVException.php');
// require_once('CalDAVFilter.php');
require_once('CalDAVObject.php');

class xbVjournalHelper {
    private $client;
    private $serverid;
    
    /**
     * @name connect()
     * @desc creates a new client object and opens connection to the server. 
     * @param string $url - url for CalDAV server eg 'https://nextcloud.mydomain/remote.php/dav/principals/users/username/'
     * @param string $user - username to login to server
     * @param string $pass - password to login to server
     * @throws CalDAVException
     */
    public function connect( $url, $user, $pass )
    {
        //  Connect to CalDAV-Server and log in
        $client = new xbCalDAVClient($url, $user, $pass);
        
        // Valid CalDAV-Server? Or is it just a WebDAV-Server?
        if( ! $client->isValidCalDAVServer() )
        {
            
            if( $client->GetHttpResultCode() == '401' ) // unauthorisized
            {
                throw new CalDAVException('Login failed', $client);
            }
            
            elseif( $client->GetHttpResultCode() == '' ) // can't reach server
            {
                throw new CalDAVException('Can\'t reach server', $client);
            }
            
            else throw new CalDAVException('Could\'nt find a CalDAV-collection under the url', $client);
        }
        
        // Check for errors
        if( $client->GetHttpResultCode() != '200' ) {
            if( $client->GetHttpResultCode() == '401' ) // unauthorisized
            {
                throw new CalDAVException('Login failed', $client);
            }
            
            elseif( $client->GetHttpResultCode() == '' ) // can't reach server
            {
                throw new CalDAVException('Can\'t reach server', $client);
            }
            
            else // Unknown status
            {
                throw new CalDAVException('Recieved unknown HTTP status while checking the connection after establishing it', $client);
            }
        }
        
        $this->client = $client;
    }
    
    /**
     * @name findCalendars()
     * @desc gets a list of all valid calendars on the server. Includes ones without VJOURNAL support
     * check $calendar->Components to see if VJOURNAL included
     * @throws Exception
     * @return array of Calendar objects xbCalDAVCalendar[] accessible by the current principal (user)
     */
    public function findCalendars($journalsOnly = false)
    {
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        
         $cals = $this->client->FindCalendars(true);
         if ($journalsOnly) {
             foreach ($cals as $cal) {
                 $valcals = array();
                 if (strpos($cal->Components,'VJOURNAL') !== false) {
                     $valcals[]= $cal;
                 }
             }
             $cals = $valcals;
         }
         return $cals;
    }
    
    /**
     * function setCalendar()
     *
     * Sets the actual calendar to work with
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    function setCalendar ( CalDAVCalendar $calendar )
    {
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        
        $this->client->SetCalendar($this->client->first_url_part.$calendar->getURL());
        
        // Is there a '/' at the end of the calendar_url?
        if ( ! preg_match( '#^.*?/$#', $this->client->calendar_url, $matches ) ) { $this->url = $this->client->calendar_url.'/'; }
        else { $this->url = $this->client->calendar_url; }
    }
    
    function setCalendarByUrl($calurl) {
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        
        $this->client->SetCalendar($this->client->first_url_part.$calurl);
        
        // Is there a '/' at the end of the calendar_url?
        if ( ! preg_match( '#^.*?/$#', $this->client->calendar_url, $matches ) ) { $this->url = $this->client->calendar_url.'/'; }
        else { $this->url = $this->client->calendar_url; }
        
    }

    /**
     * function create()
     * Creates a new calendar resource on the CalDAV-Server (event, todo, etc.).
     *
     * Arguments:
     * @param $cal iCalendar-data of the resource you want to create.
     *           	Notice: The iCalendar-data contains the unique ID which specifies where the event is being saved.
     *
     * Return value:
     * @return CalDAVObject - representation (see CalDAVObject.php) of your created resource
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    function create ( $cal )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Parse $cal for UID
        if (! preg_match( '#^UID:(.*?)\r?\n?$#m', $cal, $matches ) ) { throw new Exception('Can\'t find UID in $cal'); }
        else { $uid = $matches[1]; }
        
        // Does $this->url.$uid.'.ics' already exist?
        $result = $this->client->GetEntryByHref( $this->url.$uid.'.ics' );
        if ( $this->client->GetHttpResultCode() == '200' ) { throw new CalDAVException($this->url.$uid.'.ics already exists. UID not unique?', $this->client); }
        else if ( $this->client->GetHttpResultCode() == '404' );
        else throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        
        // Put it!
        $newEtag = $this->client->DoPUTRequest( $this->url.$uid.'.ics', $cal );
        
        // PUT-request successfull?
        if ( $this->client->GetHttpResultCode() != '201' )
        {
            if ( $this->client->GetHttpResultCode() == '204' ) // $url.$uid.'.ics' already existed on server
            {
                throw new CalDAVException( $this->url.$uid.'.ics already existed. Entry has been overwritten.', $this->client);
            }
            
            else // Unknown status
            {
                throw new CalDAVException('Recieved unknown HTTP status', $this->client);
            }
        }
        
        return new CalDAVObject($this->url.$uid.'.ics', $cal, $newEtag);
    }
    
    /**
     * function change()
     * Changes a calendar resource (event, todo, etc.) on the CalDAV-Server.
     *
     * Arguments:
     * @param $href See CalDAVObject.php
     * @param $cal The new iCalendar-data that should be used to overwrite the old one.
     * @param $etag See CalDAVObject.php
     *
     * Return value:
     * @return An CalDAVObject-representation (see CalDAVObject.php) of your changed resource
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    function change ( $href, $new_data, $etag )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Does $href exist?
        $result = $this->client->GetEntryByHref($href);
        if ( $this->client->GetHttpResultCode() == '200' );
        else if ( $this->client->GetHttpResultCode() == '404' ) throw new CalDAVException('Can\'t find '.$href.' on the server', $this->client);
        else throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        
        // $etag correct?
        if($result[0]['etag'] != $etag) { throw new CalDAVException('Wrong entity tag. The entity seems to have changed.', $this->client); }
        
        // Put it!
        $newEtag = $this->client->DoPUTRequest( $href, $new_data, $etag );
        
        // PUT-request successfull?
        if ( $this->client->GetHttpResultCode() != '204' && $this->client->GetHttpResultCode() != '200' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
        
        return new CalDAVObject($href, $new_data, $newEtag);
    }
    
    /**
     * function delete()
     * Delets an event or a TODO from the CalDAV-Server.
     *
     * Arguments:
     * @param $href See CalDAVObject.php
     * @param $etag See CalDAVObject.php
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    function delete ( $href, $etag )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Does $href exist?
        $result = $this->client->GetEntryByHref($href);
        if(count($result) == 0) throw new CalDAVException('Can\'t find '.$href.'on server', $this->client);
        
        // $etag correct?
        if($result[0]['etag'] != $etag) { throw new CalDAVException('Wrong entity tag. The entity seems to have changed.', $this->client); }
        
        // Do the deletion
        $this->client->DoDELETERequest($href, $etag);
        
        // Deletion successfull?
        if ( $this->client->GetHttpResultCode() != '200' and $this->client->GetHttpResultCode() != '204' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
    }
    
    /**
     * @name getVjournals()
     * @desc returns all Vjournal items within the specifeid date range for specified date property
     * NB defaults to using created date rather than dtstart to ensure notes get included 
     * @param string $start
     * @param string $end
     * @param string $dateprop
     * @throws Exception
     * @throws CalDAVException
     * @return CalDAVObject[]
     */
    function getVjournals($start = null, $end = null, $dateprop = "DTSTAMP"){
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Are $start and $end in the correct format?
        if ( ( isset($start) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $start, $matches ) )
            or ( isset($end) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $end, $matches ) ) )
        { trigger_error('$start or $end are in the wrong format. They must have the format yyyymmddThhmmssZ and should be in GMT', E_USER_ERROR); }
        
        // Get it!
        $results = $this->client->GetVjournals( $start, $end, $dateprop );
        
        // GET-request successfull?
        if ( $this->client->GetHttpResultCode() != '207' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
        
        // Reformat
        $report = array();
        foreach($results as $item) $report[] = new CalDAVObject($this->url.$item['href'], $item['data'], $item['etag']);
        
        return $report;
    }
        
    /**
     * @name getJournals()
     * @desc gets only jounal entries ie Vjournal items with DTSTART set
     * if start and end both set then gets items with DTSTART in the range
     * @param string $start
     * @param string $end
     * @param string $dateprop - option property to use to filter dates
     * @throws Exception
     * @throws CalDAVException
     * @return CalDAVObject[]
     */
    function getJournals( $start = '19520915T193000Z', $end = null, $dateprop = 'DTSTAMP' )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Are $start and $end in the correct format?
        if ( ( isset($start) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $start, $matches ) )
            or ( isset($end) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $end, $matches ) ) )
        { trigger_error('$start or $end are in the wrong format. They must have the format yyyymmddThhmmssZ and should be in GMT', E_USER_ERROR); }
        
        // Get it!
        $results = $this->client->GetJournalsOnly( $start, $end, $dateprop );
        
        // GET-request successfull?
        if ( $this->client->GetHttpResultCode() != '207' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
        
        // Reformat
        $report = array();
        foreach($results as $item) $report[] = new CalDAVObject($this->url.$item['href'], $item['data'], $item['etag']);
        
        return $report;
    }
    
    /**
     * @name getNotes()
     * @desc gets VJOURNAL components without a DTSTART or DTEND - these are Notes
     * @throws Exception
     * @throws CalDAVException
     * @return CalDAVObject[]
     */
    function getNotes($start = null, $end = null, $dateprop = '' )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
 
        // Are $start and $end in the correct format?
        if ( ( isset($start) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $start, $matches ) )
            or ( isset($end) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $end, $matches ) ) )
        { trigger_error('$start or $end are in the wrong format. They must have the format yyyymmddThhmmssZ and should be in GMT', E_USER_ERROR); }
        
        
        // Get it!
        $results = $this->client->GetNotes();
        
        // GET-request successfull?
        if ( $this->client->GetHttpResultCode() != '207' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
        
        // Reformat
        $report = array();
        foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['data'], $event['etag']);
        
        return $report;
    }

    /**
     * function getCustomReport()
     * Sends a custom request to the server
     * (Sends a REPORT-request with a custom <C:filter>-tag)
     *
     * You can either write the filterXML yourself or build an CalDAVFilter-object (see CalDAVFilter.php).
     *
     * See http://www.rfcreader.com/#rfc4791_line1524 for more information about how to write filters on your own.
     *
     * Arguments:
     * @param string $filterXML  stuff, you want to send encapsulated in the <C:filter>-tag.
     *
     * Return value:
     * @return array of CalDAVObjects (See CalDAVObject.php), representing the found calendar resources.
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    function getCustomReport ( $filterXML )
    {
        // Connection and calendar set?
        if(!isset($this->client)) throw new Exception('No connection. Try connect().');
        if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
        
        // Get report!
        $this->client->SetDepth('1');
        
        // Get it!
        $results = $this->client->DoCalendarQuery('<C:filter>'.$filterXML.'</C:filter>');
        
        // GET-request successfull?
        if ( $this->client->GetHttpResultCode() != '207' )
        {
            throw new CalDAVException('Recieved unknown HTTP status', $this->client);
        }
        
        // Reformat
        $report = array();
        foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['data'], $event['etag']);
        
        return $report;
    }

    /**
     * @name function parseVjournalObject()
     * @desc takes a CalDAVOject which has href,etag, and data properties
     * and parses each line in the data reurning an array indexed by property name
     * with each row containing an array of property name, parameters, and value
     * @param CalDAVObject $calitem
     * @return array[] 
     */
    function parseVjournalObject(CalDAVObject $calitem) {
        
        $journalentry = array();
        $calok = false;
        $journalok = false;

        $journalentry['etag'] = array('property'=>'etag','value'=>$calitem->getEtag(),'params'=>array());
        $journalentry['href'] = array('property'=>'href','value'=>$calitem->getHref(),'params'=>array());
        $lines = $calitem->getData();
        //	        $lines = str_replace("\r\n ", "", $lines);
        //	        $lines = str_replace("\r ", "", $lines);
        $lines = str_replace("\n"." ", "", $lines); //unfold on newline followed by space
        $lines = explode("\n",$lines); //create array of lines each consisting of property(;parameters):value
        $n = 0;
        foreach ($lines as $line) {
            $n++;
            $cpos = self::getColon($line);
            //the value is everything after the first colon not inside double quotes
            $value = substr($line,$cpos+1); 
            $params = explode(';',substr($line,0,strpos($line,':'))); //make an array of property and any values
            $property = array_shift($params); //extract the property
            //TODO handle case with multiple values separated by commas before unescaping
            if ($property != 'ATTACH') { //unescape newline text and comma and semicolons
                //actually this should only be for values of type text
                $value =str_replace('\n',"\n",$value);
                $value =str_replace('\,',',',$value);
                $value =str_replace('\;',';',$value);
            }
            if (!$calok) { //spool through looking for start of vcalendar
                if (($property == 'BEGIN') && ($value == 'VCALENDAR')) {
                    $calok = true ;
                }
            } else {
                if (!$journalok) { //get calendar properties and look for start of vjournal
                    //ignoring timezone and other stuff with own begin-end wrapper
                    switch ($property) {
                        case 'VERSION':
                            $journalentry['version'] = array('property'=>'version','value'=>$value,'params'=>array());
                            break;
                        case 'PRODID':
                            $journalentry['prodid'] = array('property'=>'prodid','value'=>$value,'params'=>array());
                            break;
                        case 'BEGIN':
                            if ($value == 'VJOURNAL') {
                                $journalok = true;
                            }
                            //else set flag to wait for end
                            break;
                        case 'END':
                            if ($value == 'VCALENDAR') {
                                $calok = false;
                            }
                            break;
                    }
                } else { //calok and journalok
                    $property = strtolower($property);
                    if (($property == 'end') && ($value == 'VJOURNAL')) {
                        $journalok = false;
                    //elseif begin set flag to wait for end
                    } else {
                        //we need property in the item array and also as the entry array key
                        $valparam = array('property'=>$property,'value'=>$value,'params'=>$params);
                        $pkey = $property;
                        if (key_exists($property, $journalentry))
                            $pkey = $property.$n;
                        $journalentry[$pkey] = $valparam;                       
                    }
                } //end else cal and journal ok
            } //end cal ok
        } //end foreach line        
        return $journalentry;
    } //end parseCalDAVObject
    
    /** 
     * @name function getColon()
     * @desc finds the position of the first colon char not inside double quotes
     * recursive function. returns false if no colon found or position (starting at 0) of colon
     * @param string $line - the text to search
     * @param int start - the position to start the search (for recursion)
     * @return false | int
     */
    function getColon(string $line, int $start=0) {
        $cpos = strpos($line,':',$start);
        if ($cpos === false) return false;
        $qpos = strpos($line,'"',$start);
        if ($qpos === false) return $cpos;
        if ($qpos<$cpos) {
            $q2pos = strpos($line,'"',$qpos);
            if ($q2pos === false) return false;            
            $cpos = $this->getColon($line,$q2pos);
        }
        return $cpos;
    }
  
}

    /**
     * function getEvents()
     * Gets a all events from the CalDAV-Server which lie in a defined time interval.
     *
     * Arguments:
     * @param $start The starting point of the time interval. Must be in the format yyyymmddThhmmssZ and should be in
     *           		GMT. If omitted the value is set to -infinity.
     * @param $end The end point of the time interval. Must be in the format yyyymmddThhmmssZ and should be in
     *           		GMT. If omitted the value is set to +infinity.
     *
     * Return value:
     * @return an array of CalDAVObjects (See CalDAVObject.php), representing the found events.
     *
     * Debugging:
     * @throws CalDAVException
     * For debugging purposes, just sorround everything with try { ... } catch (Exception $e) { echo $e->__toString(); exit(-1); }
     */
    /**** NOT USED
     function getEvents ( $start = null, $end = null )
     {
     // Connection and calendar set?
     if(!isset($this->client)) throw new Exception('No connection. Try connect().');
     if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
     
     // Are $start and $end in the correct format?
     if ( ( isset($start) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $start, $matches ) )
     or ( isset($end) and ! preg_match( '#^\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ$#', $end, $matches ) ) )
     { trigger_error('$start or $end are in the wrong format. They must have the format yyyymmddThhmmssZ and should be in GMT', E_USER_ERROR); }
     
     // Get it!
     $results = $this->client->GetEvents( $start, $end );
     
     // GET-request successfull?
     if ( $this->client->GetHttpResultCode() != '207' )
     {
     throw new CalDAVException('Recieved unknown HTTP status', $this->client);
     }
     
     // Reformat
     $report = array();
     foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['data'], $event['etag']);
     
     return $report;
     }
     ****/
    
    /**** NOT USED
     function getEtags() {
     // Connection and calendar set?
     if(!isset($this->client)) throw new Exception('No connection. Try connect().');
     if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
     
     // 	    // Get it!
     // 	    $results = $this->client->GetNotes();
     
     // 	    // GET-request successfull?
     // 	    if ( $this->client->GetHttpResultCode() != '207' )
     // 	    {
     // 	        throw new CalDAVException('Recieved unknown HTTP status', $this->client);
     // 	    }
     
     // 	    // Reformat
     // 	    $report = array();
     // 	    foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['data'], $event['etag']);
     
     // 	    return $report;
     }
     ****/
     
     /**** NOT USED
      function getEntryByHref($href = null) {
      // Connection and calendar set?
      if(!isset($this->client)) throw new Exception('No connection. Try connect().');
      if(!isset($this->client->calendar_url)) throw new Exception('No calendar selected. Try findCalendars() and setCalendar().');
      
      // Get it!
      $results = $this->client->GetEntryByHref();
      
      // GET-request successfull?
      if ( $this->client->GetHttpResultCode() != '207' )
      {
      throw new CalDAVException('Recieved unknown HTTP status', $this->client);
      }
      
      // Reformat
      $report = array();
      foreach($results as $event) $report[] = new CalDAVObject($this->url.$event['href'], $event['data'], $event['etag']);
      
      return $report;
      }
      ****/
     
     

    

    