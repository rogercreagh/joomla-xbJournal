<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/attachment.php
 * @version 0.1.4.0 12th October 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbjournalsModelAttachment extends JModelItem {
 
    protected function populateState() {
        $app = Factory::getApplication('admin');
        
        // Load state from the request.
        $id = $app->input->getInt('id');
        $this->setState('xbjattachment.id', $id);
        
        // Load the parameters.
//         $params = $app->getParams();
//         $this->setState('params', $params);
        
    }
    
    public function getItem($id = null) {
        if (!isset($this->item) || !is_null($id)) {
            $id    = is_null($id) ? $this->getState('xbjattachment.id') : $id;
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('a.id AS id, a.entry_id AS entry_id, a.atthash AS atthash,'
                .'a.uri AS uri, a.encoding AS encoding, a.fmttype AS fmttype,'
                .'a.value AS value, a.filename AS filename,a.label AS label,'
                .'a.otherparams AS otherparams, a.info AS info, a.localpath AS localpath');
            $query->from('#__xbjournals_vjournal_attachments AS a');
            $query->where('a.id = '.$id);
            $db->setQuery($query);
            if ($this->item = $db->loadObject()) {
                
                $item = &$this->item;
                // Load the JSON string
                $otherparams = new Registry;
                $otherparams->loadString($item->otherparams, 'JSON');
                $item->otherparams = $otherparams;
                
//                 // Merge global params with item params
//                 $params = clone $this->getState('params');
//                 $params->merge($item->params);
//                 $item->params = $params;
                
            }
            return $this->item;
        }
    }
    
}
