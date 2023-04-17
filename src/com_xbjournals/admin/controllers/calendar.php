<?php
/*******
 * @package xbJournals Component
 * @filesource admin/controllers/calendar.php
 * @version 0.0.0.9 12th April 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2023
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class XbjournalsControllerCalendar extends  FormController {
    
    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
        //$this->registerTask('savepreview', 'save');
    }
    
}
