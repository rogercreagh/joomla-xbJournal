<?php
/*******
 * @package xbPeople
 * @filesource admin/models/fields/xbcats.php
 * @version 0.10.0.3 27th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2022
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @desc create a form field type to select a category allowing both a parent and a number of levels to be specified.
 * based on code from joomla3|4-/libraires/legacy|src/Form/Field/category|CategoryField.php
 * Joomla! Content Management System
 * @copyright  (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;

FormHelper::loadFieldClass('list');

class JFormFieldXbcats extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.6
     */
    public $type = 'Xbcats';
    
    /**
     * Method to get the field options for category
     * Use the extension attribute in a form to specify the.specific extension for
     * which categories should be displayed.
     * Use the show_root attribute to specify whether to show the global category root in the list.
     *
     * @return  array    The field option objects.
     *
     * @since   1.6
     */
    protected function getOptions()
    {
        $options = array();
        $extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
        $published = (string) $this->element['published'];
//        $language  = (string) $this->element['language'];
        $parent_id = 1;
        $levels = 0;
        $maxlevel = 0;
        $parent = (string) $this->element['parent'];
        $levels = (int) $this->element['levels'];
        if ($parent)  { 
            //for php8 use str_starts_with($parent, string 'com_')
        }
        if ($parent) {
            if (substr($parent,0,4) == 'com_')  { //we're looking in the option params for a component
                //for php8 use str_starts_with($parent, string 'com_')
                $parent = explode('.',$parent);
                $params = ComponentHelper::getParams($parent[0]);
                if ($params) {
                    $parent_id = $params->get($parent[1],1);
                }
            } else { // maybe the parent setting is in the cat extension options and $parent is just the option name
                $params = ComponentHelper::getParams($extension);
                if ($params) $parent_id = $params->get($parent,1);               
            }
        }
        if ($levels) {
            //if level set get maxlevel
            $maxlevel = $levels;
            if ($parent_id>1) {
                $maxlevel += XbcultureHelper::getCat($parent_id)->level;
            }
        }
        
        // Load the category options for a given extension.
        if (!empty($extension))
        {
            $db     = Factory::getDbo();
            $user   = Factory::getUser();
            $groups = implode(',', $user->getAuthorisedViewLevels());
            
            $query = $db->getQuery(true)
                ->select('DISTINCT a.id AS value, a.title AS text, a.level, a.language, a.published, a.lft')
                ->from('#__categories AS a')
                ->join('LEFT', $db->qn('#__categories','b').' ON '.
                    $db->qn('a.lft').' > '.$db->qn('b.lft').' AND '.$db->qn('a.rgt').' < '.$db->qn('b.rgt'))
                    ->where($db->qn('a.parent_id').' > 0');
            
            // Filter on extension.
            $query->where($db->qn('a.extension').' = ' . $db->quote($extension));
            
            // Filter on user access level
//            $query->where($db->qn('a.access').' IN (' . $groups . ')');
    
            // Limit options to only children of parent
            if ($parent_id > 1) {
                $query->where('b.id = '. $parent_id);
            }
            //limit how far down the tree to go
            if ($levels && $maxlevel) {
                $query->where($db->qn('a.level').' <= '.$db->q($maxlevel));
            }
                    
            // Filter on the published state
            //no value forces published only, missing element shows all states same as "-2,0,1,2" 
            if ($published==='') { // === does not include 0
                $published = 1;
            }
            if (is_numeric($published)) { //includes 0
                $query->where('a.published = ' . (int) $published);
            } else {
                if (is_array($published)) { 
                    $published = ArrayHelper::toInteger($published);
                } else {
                    $published = ArrayHelper::toInteger(explode(',',$published));
                }
                $query->where('a.published IN (' . implode(',', $published) . ')');
            }
                        
            //never show ROOT
//            $query->where($db->qn('a.lft') . ' > 0');
            
            // Filter on the language
//             if ($language)
//             {
//                    $query->where('a.language = ' . $db->quote($language));
//             }
            
            $query->order('a.lft ASC');
            
            $db->setQuery($query);
            $options = $db->loadObjectList();
            
            foreach ($options as $opt) {
                if ($opt->level > 1) {
                    $opt->text = str_repeat('-', ($opt->level)-1).' '.$opt->text;
                }
            }
            if (($this->element['incparent']=='true') && ($parent_id > 1)) {
                //get parent name & id
                $query = $db->getQuery(true);
                $query->select('a.id AS value, a.title AS text')
                    ->from('#__categories AS a')
                    ->where($db->qn('a.id').' ='.$db->q($parent_id));
                $db->setQuery($query);
                $parentopt = $db->loadObjectList();
                $options = array_merge($parentopt,$options);
            }
            
        }
        else {
            Log::add('Extension attribute is empty in the XbCats field', Log::WARNING, 'jerror');
        }
        
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        
        return $options;
    }
}
