<?php
/*******
 * @package xbJournals
 * @filesource admin/models/fields/people.php
 * @version 0.0.3.3 9th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldJournals extends JFormFieldList {
    
    protected $type = 'Journals';
    
    public function getOptions() {
        //this will get a list of all calendars that have entriesoftype Journal
    	$params = ComponentHelper::getParams('com_xbjournals');
    	//poss param to show state " (", state, ")"
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
 
        $query->select('a.calendar_id As value')
	        ->select('CONCAT(c.title," Journal") AS text',)
	        ->from('#__xbjournals_vjournal_entries AS a')
	        ->join('LEFT','#__xbjournals_calendars AS c ON c.id = a.calendar_id')
	        ->where('a.entry_type = '.$db->quote('Journal'))
			->group('c.id')
        	->order('text');
        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
