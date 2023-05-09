<?php
/*******
 * @package xbJournals
 * @filesource admin/models/fields/xbitemcats.php
 * @version 0.0.3.39th May 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Log\Log;

FormHelper::loadFieldClass('list');

/**
 * @name XbItemCats field class
 * @desc Returns an options list for all categories actually in use in a specified itemtable
 * NB Assumes that table has  column named catid or is specified as "#__tablename.catidcolum"
 * @author rogerco
 *
 */
class JFormFieldXbitemcats extends JFormFieldList {
	
	protected $type = 'XbItemCats';
	
	public function getOptions() {
		
		$options = array();
		$catext = $this->element['extension'] ? (string) $this->element['extension'] : 'com_xbpeople';
		$published = $this->element['published'] ? (string) $this->element['published'] : '';
//		$language  = (string) $this->element['language'];
		if (!empty($this->element['itemtable'])) {
			$itemtable = (string) $this->element['itemtable'];
			$itemtable = explode('.',$itemtable);
			$catcol = (count($itemtable)>1) ? $itemtable[1] : 'catid';
			$itemtable = $itemtable[0];
		
    		$db = Factory::getDbo();
    		$query  = $db->getQuery(true);
    		$query->select('DISTINCT a.id AS value, a.title AS text, a.level')
                ->from($db->quotename('#__categories').' AS '.$db->qn('a'))
                ->join('INNER', $db->qn($itemtable).'AS b ON '.$db->qn('b.'.$catcol).' = a.id' )
    		    ->where('extension = '.$db->quote($catext));
       		if ($published) {
    			$query->where($db->qn('a.published').' IN ('.$published.')');
    		}
    		$query->order('lft');
    		$db->setQuery($query);
    		$options = $db->loadObjectList();
    		foreach ($options as &$item) {
    			if ($item->level>1) {
    				$item->text = str_repeat('- ', $item->level - 1).$item->text;				
    			}
    		}
    		// Merge any additional options in the XML definition.
    		$options = array_merge(parent::getOptions(), $options);
    		return $options;
		} else {
		    Log::add('Itemtable attribute is empty in the XbitemCats field', Log::WARNING, 'jerror');
		}
	}
		
}
	