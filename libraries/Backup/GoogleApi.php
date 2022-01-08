<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());

//require_once APPPATH . 'third_party/Google/src/Google/autoload.php';
//require_once APPPATH . 'third_party/Google/src/Google/Client.php';
//D:\WWW\application\third_party\Google\src\Google\Service\Client.php
//D:\WWW\application\third_party\Google\vendor\google\apiclient-services\src\Google\Service\Calendar.php
//D:\WWW\application\third_party\Google\vendor\google\apiclient-services\src\Google\Service\Calendar\CalendarList.php
require_once APPPATH . 'third_party/Google/vendor/autoload.php';
require_once APPPATH . 'third_party/Google/src/Google/Client.php';
require_once APPPATH . 'third_party/Google/vendor/google/apiclient-services/src/Google/Service/Calendar.php';
//require_once APPPATH . 'third_party/Google/vendor/google/apiclient-services/src/Google/Service/Calendar/CalendarList.php';


class GoogleApi extends Google_Client {
    function __construct($params = array()) {
        parent::__construct();
    }
} 
?>