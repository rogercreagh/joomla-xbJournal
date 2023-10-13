<?php
/*******
 * @package xbJournals Component
 * @filesource admin/views/attachment/view.html.php
 * @version 0.1.4.0 12th October 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbjournalsViewAttachment extends JViewLegacy {
    
    protected $form = null;
    
    public function display($tpl = null) {
        
        $this->item = $this->get('Item');
 //       $this->canDo = XbjournalsHelper::getActions('com_xbjournals', 'calendar', $this->item->id);
        
 //       $params      = $this->get('State')->get('params');
        
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
            return false;
        }
        
        $this->addToolBar();
        
        parent::display($tpl);
        // Set the document
        $this->setDocument();
        
    }
    
    protected function addToolBar()
    {
        $input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $user = Factory::getUser();
        $userId = $user->get('id');
//        $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        
 //       $canDo = $this->canDo;
        
//        $isNew = ($this->item->id == 0);
        
//         if ($isNew) {
//             $title = Text::_('XBJOURNALS_ADMIN_NEWCALENDAR_TITLE');
//         } elseif ($checkedOut) {
//             $title = Text::_('XBJOURNALS_ADMIN_VIEWCALENDAR_TITLE');
//         } else {
//             $title = Text::_('XBJOURNALS_ADMIN_EDITCALENDAR_TITLE');
//         }
$title = 'xbJournals : Attachment Details';
        ToolBarHelper::title($title, 'calendar');
        
//        ToolbarHelper::apply('calendar.apply');

        $bar = Toolbar::getInstance('toolbar');
        
//         $text = Text::_('JAPPLY');
//         $btnHtml = "<button onclick=\"pleaseWait('waiter'); Joomla.submitbutton('server.apply');\"
//                 class=\"btn btn-small button-apply btn-success\"><span class=\"icon-apply icon-white\"></span>$text</button>";
//         $bar->appendButton('Custom', $btnHtml);
        
//         if (!$isNew) {
            
//             $text = Text::_('XBJOURNALS_LIST_CALS');
//             $btnHtml = "<button onclick=\"pleaseWait('waiter'); Joomla.submitbutton('server.listcals');\" 
//                 class=\"btn btn-small\"><span class=\"icon-list-2\"></span>$text</button>";
//             $bar->appendButton('Custom', $btnHtml);
            
//             $text = Text::_('XBJOURNALS_GET_CALS');
//             $btnHtml = "<button onclick=\"pleaseWait('waiter'); Joomla.submitbutton('server.getcals');
//             \" class=\"btn btn-small\"><span class=\"icon-folder-plus\"></span>$text</button>";
//             $bar->appendButton('Custom', $btnHtml);
                        
//         }
//        ToolbarHelper::save('calendar.save');
//        ToolbarHelper::save2new('server.save2new');
//        ToolbarHelper::save2copy('server.save2copy');
//         if ($isNew) {
//             ToolbarHelper::cancel('calendar.cancel','JTOOLBAR_CANCEL');
//         } else {
             ToolbarHelper::cancel('calendar.cancel','JTOOLBAR_CLOSE');
//         }
        ToolbarHelper::custom(); //spacer
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbjournals/doc?tmpl=component#calendaredit' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(strip_tags(($this->item->id == 0) ? Text::_('XBJOURNALS_ADMIN_NEWCALENDAR_TITLE') : Text::_('XBJOURNALS_ADMIN_VIEWATTACHMENT_TITLE')));
    }
    
}