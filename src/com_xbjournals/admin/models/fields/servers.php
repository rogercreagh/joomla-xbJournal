<?php
/*******
 * @package xbJournals
 * @filesource admin/models/fields/servers.php
 * @version 0.1.1.2 12th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldServers extends JFormFieldList {
    
    protected $type = 'Servers';
    
    public function getOptions() {
        //this will get a list of all servers 
 //   	$params = ComponentHelper::getParams('com_xbjournals');
    	//poss param to show state " (", state, ")"
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
 
        $query->select('a.id As value')
	        ->select('title AS text',)
	        ->from('#__xbjournals_servers AS a')
        	->order('text');
        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
