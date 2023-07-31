<?php
/*******
 * @package xbJournals Component
 * @filesource admin/tables/note.php
 * @version 0.1.3.1 31st July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Table\Observer\Tags;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class XbjournalsTableNote extends Table
{
    public function __construct(&$db) {
        $this->setColumnAlias('published', 'state');
        parent::__construct('#__xbjournals_vjournal_entries', 'id', $db);
        Tags::createObserver($this, array('typeAlias' => 'com_xbjournals.note'));
    }
    
    public function check() {
        
        //first check that connection is valid
//         if (!XbjournalsHelper::checkValidServer($this->url,$this->username, $this->password)) {
//             $this->setError(Text::_('Invalid server connection'));
//             return false;
//         }
        
        $params = ComponentHelper::getParams('com_xbjournals');
        
        $title = trim($this->title);
        //require title
        if ($title == '') {
            $this->setError(Text::_('XBJOURNALS_PROVIDE_VALID_TITLE'));
            return false;
        }
        
        if (($this->id == 0) && (XbjournalsHelper::checkDBvalueExists($title,'#__xbjournals_vjournal_entries'))) {
            $this->setError(Text::sprintf('XBJOURNALS_TITLE_EXISTS',$title));
            return false;
        }
        
        $this->title = $title;
        //create alias if not set - title is already unique
        if (trim($this->alias) == '') {
            $this->alias = $title;
        }
        $this->alias = OutputFilter::stringURLSafe(strtolower($this->alias));
        //check alias and if exists add cycle num
        if (($this->id == 0) && (XbjournalsHelper::checkDBvalueExists($this->alias,'#__xbjournals_vjournal_entries','alias'))) {
            $num=1;
            $test = $this->alias.'-'.$num;
            while (XbjournalsHelper::checkDBvalueExists($test,'#__xbjournals_vjournal_entries','alias')) {
                $num ++;
                $test = $this->alias.'-'.$num;
            }
            $this->alias = $test;
        }
/***/        
        //set metadata to defaults
        $metadata = json_decode($this->metadata,true);
        // meta.author will be created_by_alias (see above)
        if ($metadata['author'] == '') {
            if ($this->created_by_alias =='') {
                $metadata['author'] = $params->get('def_author');
            } else {
                $metadata['author'] = $this->created_by_alias;
            }
        }
        //meta.description can be set to first 150 chars of summary if not otherwise set and option is set
//        $summary_metadesc = $params->get('summary_metadesc');
//        if (($summary_metadesc) && (trim($metadata['metadesc']) == '')) {
//            $metadata['metadesc'] = HtmlHelper::_('string.truncate', strip_tags($this->description),150,true,false);
//        }
        //meta.rights will be set to default if not otherwise set
//        $def_rights = $params->get('def_rights');
//        if (($def_rights != '') && (trim($metadata['rights']) == '')) {
//            $metadata['rights'] = $def_rights;
//        }
        $this->metadata = json_encode($metadata);
/***/        
        return true;
    }
    
    public function bind($array, $ignore = '') {
        
        if (isset($array['params']) && is_array($array['params'])) {
            // Convert the params field to a string.
            $parameters = new Registry;
            $parameters->loadArray($array['params']);
            $array['params'] = (string)$parameters;
        }
        
        // 		if (isset($array['rules']) && is_array($array['rules'])) {
        //             $rules = new JAccessRules($array['rules']);
        //             $this->setRules($rules);
        //         }
        
        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new Registry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }
        return parent::bind($array, $ignore);
        
}

/**
 * Replacement for table class checkIn() function to write null instead of zeros in the datetime field.
 * {@inheritDoc}
 * @see \Joomla\CMS\Table\Table::checkIn()
 */
    public function checkIn($pk = null) {
        $checkedOutField = $this->getColumnAlias('checked_out');
        $checkedOutTimeField = $this->getColumnAlias('checked_out_time');
        
        // If there is no checked_out or checked_out_time field, just return true.
        if (!property_exists($this, $checkedOutField) || !property_exists($this, $checkedOutTimeField)) {
            return true;
        }
        
        if (is_null($pk)) {
            $pk = array();
            
            foreach ($this->_tbl_keys as $key) {
                $pk[$this->$key] = $this->$key;
            }
        } elseif (!is_array($pk)) {
            $pk = array($this->_tbl_key => $pk);
        }
        
        foreach ($this->_tbl_keys as $key) {
            $pk[$key] = empty($pk[$key]) ? $this->$key : $pk[$key];
            
            if ($pk[$key] === null) {
                throw new \UnexpectedValueException('Null primary key not allowed.');
            }
        }
        
        // Check the row in by primary key.
        $query = $this->_db->getQuery(true)
        ->update($this->_tbl)
        ->set($this->_db->quoteName($checkedOutField) . ' = 0' )
        ->set($this->_db->quoteName($checkedOutTimeField) . ' = NULL' );
        parent::appendPrimaryKeys($query, $pk);
        $this->_db->setQuery($query);
        
        // Check for a database error.
        $this->_db->execute();
        
        // Set table values in the object.
        $this->$checkedOutField     =  0;
        $this->$checkedOutTimeField =  '';
        
        $dispatcher = \JEventDispatcher::getInstance();
        $dispatcher->trigger('onAfterCheckin', array($this->_tbl));
        
        return true;
    }

}