<?php
/*******
 * @package xbcaldav Library
 * @filesource admin/helpers/xbcaldav/xbCalDAVCalendar.php
 * @version 0.0.6.0 8th June 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * based on SimpleCalDavClient by Michael Palm <palm.michael@gmx.de>
 * portions copyright (c) Michael Palm <palm.michael@gmx.de>, 2014
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 * @desc a simple class representing a Calendar object with getters and setters for properties
 ******/

// No direct access to this file
defined('_JEXEC') or die;
//comment the above line out if not using within Joomla CMS

class xbCalDAVCalendar {
	private $url;
	private $displayname;
	private $ctag;  //changes every time the calendar is updated on the server
	private $calendar_id;
	private $rgba_color;
	private $rbg_color;
	private $order;
	private $components; //a comma separated list of component types in uppercase that the calendar accepts.
		
	function __construct ( $url, $displayname = null, $ctag = null, $calendar_id = null, 
	       $rbg_color = null, $order = null, $components = 'VEVENT,VJOURNAL,VTODO' ) {
	    
		$this->url = $url;
		$this->displayname = $displayname;
		$this->ctag = $ctag;
		$this->calendar_id = $calendar_id;
		$this->rbg_color = $rbg_color;
		$this->order = $order;
		$this->components = $components;
		
	}
	
	function __toString () {
	    return( '(URL: '.$this->url.'   Ctag: '.$this->ctag.'   Displayname: '.$this->displayname .'   Components: '.$this->components.')'. "\n" );
	}
	
	// Getters
	
	function getURL () {
		return $this->url;
	}
	
	function getDisplayName () {
		return $this->displayname;
	}
	
	function getCTag () {
		return $this->ctag;
	}
	
	function getCalendarID () {
		return $this->calendar_id;
	}
	
	function getRBGcolor () {
		return $this->rbg_color;
	}
	
	function getOrder () {
		return $this->order;
	}
	
	function getComponents () {
	    return $this->components;
	}
	
	
	// Setters
	
	function setURL ( $url ) {
		$this->url = $url;
	}
	
	function setDisplayName ( $displayname ) {
		$this->displayname = $displayname;
	}
	
	function setCtag ( $ctag ) {
		$this->ctag = $ctag;
	}
	
	function setCalendarID ( $calendar_id ) {
		$this->calendar_id = $calendar_id;
	}
	
	function setRBGcolor ( $rbg_color ) {
		$this->rbg_color = $rbg_color;
	}
	
	function setOrder ( $order ) {
		$this->order = $order;
	}
	
	function setComponents( $comps) {
	    $this->components = $comps;
	}

}
