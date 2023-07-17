<?php
/*******
 * @package xbJournals
 * @filesource admin/models/fields/domains.php
 * @version 0.1.1.3 15th July 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldDomains extends JFormFieldList {
    
    protected $type = 'Domains';
    
    public function getOptions() {
        //this will get a list of all domains 
 //   	$params = ComponentHelper::getParams('com_xbjournals');
    	//poss param to show state " (", state, ")"
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
 
        $query->select('a.url AS url',)
	        ->from('#__xbjournals_servers AS a');
        // Get the urls.
        $db->setQuery($query);
        $urls = $db->loadColumn();
        $doms = array();
        foreach ($urls as $url) {
            if ($url != '') {
                $urlarr = parse_url($url); 
                $doms[] = $urlarr["scheme"].'://'.$urlarr["host"];
            }
        }
        $doms = array_unique($doms);
        foreach ($doms as $dom) {
            $options[] = array('value'=>$dom, 'text'=>$dom);
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
