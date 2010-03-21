<?php
/**
 * RoundCube Calendar
 *
 * Plugin to add a calendar to RoundCube.
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 */
class calendar extends rcube_plugin
{
  public $task = '?(?!login|logout).*';

  public $backend = null;

  /** Some utility functions */
  public $utils = null;

  function init() {
    $cmail = cmail::get_instance();
    
    if(file_exists($this->home . "/config/config.inc.php")) {
      $this->load_config('config/config.inc.php');
    } else {
      $this->load_config('config/config.inc.php.dist'); 
    }
    
    $backend_type = $cmail->config->get('backend', 'dummy');
    require('program/backend/' . $backend_type . '.php');

    if($backend_type === "google") {
      $this->backend = new Google($cmail,
                                  $cmail->config->get('username'), 
                                  $cmail->config->get('password'));
    } else if($backend_type === "caldav") {
      $myusername = $cmail->config->get('caldav_username');
      $mypassword = $cmail->config->get('caldav_password');
      
      if ($cmail->config->get('caldav_use_roundcube_login') === true) {
        $myusername = $_SESSION['username'];
        $mypassword = $cmail->decrypt($_SESSION['password']);
        
        // Strip top-level domain from login (username@domain.com -> username)
        if ($cmail->config->get('username_domain') !== '' /* global RoundCube setting */
          && $cmail->config->get('caldav_loginwithout_tld') === true) {
          $a_myusername = explode('@', $_SESSION['username'], 2);
          
          if ($a_myusername !== false && !empty($a_myusername))
            $myusername = $a_myusername[0];
        }
      }
      
      $this->backend = new CalDAV($cmail,
                                  $cmail->config->get('caldav_server'),
                                  $myusername,
                                  $mypassword,
                                  $cmail->config->get('caldav_calendar') /* FIXME currenty ignored */);
    } else if($backend_type === "database") {
      $this->backend = new Database($cmail);
    } else {
      $this->backend = new Dummy();
    }

    // Set up utils
    require('program/utils.php');
    $this->utils = new Utils($cmail, $this->backend);
    
    $this->add_texts('localization/', true);
    
    $this->register_action('plugin.calendar', array($this, 'startup'));
    $this->register_action('plugin.getSettings', array($this, 'getSettings'));
    $this->add_hook('list_prefs_sections', array($this, 'calendarLink'));
    $this->add_hook('user_preferences', array($this, 'settingsTable'));
    $this->add_hook('save_preferences', array($this, 'saveSettings'));

    //print
    $this->register_action('plugin.calendar_print', array($this, 'calprint'));
    $this->add_hook('template_object_datetime', array($this, 'datetime'));

    //backend actions
    $this->register_action('plugin.newEvent', array($this, 'newEvent'));
    $this->register_action('plugin.editEvent', array($this, 'editEvent'));
    $this->register_action('plugin.moveEvent', array($this, 'moveEvent'));
    $this->register_action('plugin.resizeEvent', array($this, 'resizeEvent'));
    $this->register_action('plugin.removeEvent', array($this, 'removeEvent'));
    $this->register_action('plugin.getEvents', array($this, 'getEvents'));
    $this->register_action('plugin.exportEvents', array($this, 'exportEvents'));
    
    // add taskbar button
    $this->add_button(array(
      'name'    => 'calendar',
      'class'   => 'button-calendar',
      'label'   => 'calendar.calendar',
      'href'    => './?_task=dummy&_action=plugin.calendar',
      ), 'taskbar');

    // add styles
    $skin = $cmail->config->get('skin');
    if(!file_exists($this->home . '/skins/' . $skin . '/calendar.css')) {
      $skin = "default";
    }
    $this->include_stylesheet('skins/' . $skin . '/calendar.css');
  }

  function calendarLink($args)
  {
    $temp = $args['list']['server'];
    unset($args['list']['server']);
    $args['list']['calendarlink']['id'] = 'calendarlink';
    $args['list']['calendarlink']['section'] = $this->gettext('calendar');
    $args['list']['server'] = $temp;

    return $args;
  }


  function startup($template = 'calendar.calendar') {
    $temparr = explode(".", $template);
    $domain = $temparr[0];
    $template = $temparr[1];
    
    $cmail = cmail::get_instance();
    
    $cmail->output->set_pagetitle($this->gettext('calendar'));

    $skin = $cmail->config->get('skin');
    if(!file_exists($this->home . '/skins/' . $skin . '/jquery-ui.css') || !file_exists($this->home . '/skins/' . $skin . '/fullcalendar.css')) {
      $skin = "default";
    }
    $this->include_stylesheet('skins/' . $skin . '/jquery-ui.css');
    $this->include_stylesheet('skins/' . $skin . '/fullcalendar.css');
    $this->include_stylesheet('skins/' . $skin . '/calendar.datePicker.css');
    
    $this->register_handler('plugin.category_css', array($this, 'generateCSS'));
    $this->register_handler('plugin.category_html', array($this, 'generateHTML'));

    $this->include_script('program/js/jquery-ui.js');
    if($template == "calendar") {
      $this->include_script('program/js/jquery-qtip.js');
      $this->include_script('program/js/date.js');
      $this->include_script('program/js/jquery.datePicker.js');
    }
    $this->include_script('program/js/fullcalendar.min.js');
    $this->include_script("program/js/$template.js");
    
    if($template == "calendar") {
      $this->add_button(array(
        'command' => 'plugin.calendar_print',
        'href' => '#',
        'title' => 'print',
        'imagepas' => 'skins/' . $skin . '/images/spacer.gif',
        'imageact' => 'skins/' . $skin . '/images/preview.png'),
        'toolbar'
      );
      $this->add_button(array(
        'command' => 'plugin.calendar_datepicker',
        'href' => '#',
        'id' => 'dp_position',
        'title' => 'calendar.selectdate',
        'class' => 'date-pick',
        'imagepas' => 'skins/' . $skin . '/images/calendar.png',
        'imageact' => 'skins/' . $skin . '/images/calendar.png'),
        'toolbar'
      );
      $this->add_button(array(
        'command' => 'plugin.exportEvents',
        'href' => './?_task=dummy&amp;_action=plugin.exportEvents',
        'title' => 'calendar.export',
        'imagepas' => 'skins/' . $skin . '/images/export.png',
        'imageact' => 'skins/' . $skin . '/images/export.png'),
        'toolbar'
      );
    }
    if($template == "print") {
      $this->add_button(array(
        'command' => 'plugin.calendar_toggle_view',
        'href' => '#',
        'title' => 'calendar.toggle_view',
        'imagepas' => 'skins/' . $skin . '/images/spacer.gif',
        'imageact' => 'skins/' . $skin . '/images/toggle_view.png'),
        'toolbar'
      );
      $this->add_button(array(
        'command' => 'plugin.calendar_do_print',
        'href' => '#',
        'title' => 'print',
        'imagepas' => 'skins/' . $skin . '/images/spacer.gif',
        'imageact' => 'skins/' . $skin . '/images/print.png'),
        'toolbar'
      );
    }
    $cmail->output->send("$domain.$template");
  }
  
  function calprint() {
    $this->startup('calendar.print');
    exit;
  }
  
  function datetime($args) {
    $cmail = cmail::get_instance();
    $args['content'] = date($cmail->config->get("date_long"),time());
    return($args);
  }
  
  function newEvent() {
    $start = $this->toGMT(get_input_value('_start', RCUBE_INPUT_POST));
    $summary = trim(get_input_value('_summary', RCUBE_INPUT_POST));
    $description = trim(get_input_value('_description', RCUBE_INPUT_POST));
    $location = trim(get_input_value('_location', RCUBE_INPUT_POST));
    $categories = trim(get_input_value('_categories', RCUBE_INPUT_POST));
    $allDay = get_input_value('_allDay', RCUBE_INPUT_POST);
    $allDay = ($allDay === "true")?1:0;
    
    $this->backend->newEvent($start, $summary, $description, $location, $categories, $allDay);
   
    $cmail = cmail::get_instance();
    $cmail->output->command('plugin.reloadCalendar', array());
  }

  function editEvent() {
    $id = get_input_value('_event_id', RCUBE_INPUT_POST);
    $summary = trim(get_input_value('_summary', RCUBE_INPUT_POST));
    $description = trim(get_input_value('_description', RCUBE_INPUT_POST));
    $location = trim(get_input_value('_location', RCUBE_INPUT_POST));
    $categories = trim(get_input_value('_categories', RCUBE_INPUT_POST));
    
    $this->backend->editEvent($id, $summary, $description, $location, $categories);
  }
  
  function moveEvent() {
    $id = get_input_value('_event_id', RCUBE_INPUT_POST);
    $start = $this->toGMT(get_input_value('_start', RCUBE_INPUT_POST));
    $end = $this->toGMT(get_input_value('_end', RCUBE_INPUT_POST));
    $allDay = get_input_value('_allDay', RCUBE_INPUT_POST);
    $allDay = ($allDay === "true")?1:0;
    
    $this->backend->moveEvent($id, $start, $end, $allDay);
    $cmail = cmail::get_instance();
    $cmail->output->command('plugin.reloadCalendar', array());
  }
  
  function resizeEvent() {
    $id = get_input_value('_event_id', RCUBE_INPUT_POST);
    $start = $this->toGMT(get_input_value('_start', RCUBE_INPUT_POST));
    $end = $this->toGMT(get_input_value('_end', RCUBE_INPUT_POST));
    
    $this->backend->resizeEvent($id, $start, $end);
  }
  
  function removeEvent() {
    $id = get_input_value('_event_id', RCUBE_INPUT_POST);
      
    $this->backend->removeEvent($id);
  }
  
  function getEvents() {
    // "start" and "end" are from fullcalendar, not RoundCube.
    $start = $this->toGMT(get_input_value('start', RCUBE_INPUT_GET));
    $end = $this->toGMT(get_input_value('end', RCUBE_INPUT_GET));
    
    echo $this->utils->jsonEvents($start, $end);
    exit;
  }
  
  function exportEvents() {
    $start = $this->toGMT(get_input_value('_start', RCUBE_INPUT_POST));
    $end = $this->toGMT(get_input_value('_end', RCUBE_INPUT_POST));
    
    header("Content-Type: text/calendar");
    header("Content-Disposition: inline; filename=calendar.ics");
    
    echo $this->utils->exportEvents($start, $end);
    exit;
  }

  function getSettings() {
    $cmail = cmail::get_instance();

    $settings = array();
    
    // configuration
    $settings['default_view'] = (string)$cmail->config->get('default_view', "agendaWeek");
    $settings['time_format'] = (string)$cmail->config->get('time_format', "HH:mm");
    $settings['timeslots'] = (int)$cmail->config->get('timeslots', 2);
    $settings['first_day'] = (int)$cmail->config->get('first_day', 1);
    $settings['first_hour'] = (int)$cmail->config->get('first_hour', 6);

    // localisation
    $settings['days'] = array(
      rcube_label('sunday'),   rcube_label('monday'),
      rcube_label('tuesday'),  rcube_label('wednesday'),
      rcube_label('thursday'), rcube_label('friday'),
      rcube_label('saturday')
    );
    $settings['days_short'] = array(
      rcube_label('sun'), rcube_label('mon'),
      rcube_label('tue'), rcube_label('wed'),
      rcube_label('thu'), rcube_label('fri'),
      rcube_label('sat')
    );
    $settings['months'] = array(
      $cmail->gettext('longjan'), $cmail->gettext('longfeb'),
      $cmail->gettext('longmar'), $cmail->gettext('longapr'),
      $cmail->gettext('longmay'), $cmail->gettext('longjun'),
      $cmail->gettext('longjul'), $cmail->gettext('longaug'),
      $cmail->gettext('longsep'), $cmail->gettext('longoct'),
      $cmail->gettext('longnov'), $cmail->gettext('longdec')
    );
    $settings['months_short'] = array(
      $cmail->gettext('jan'), $cmail->gettext('feb'),
      $cmail->gettext('mar'), $cmail->gettext('apr'),
      $cmail->gettext('may'), $cmail->gettext('jun'),
      $cmail->gettext('jul'), $cmail->gettext('aug'),
      $cmail->gettext('sep'), $cmail->gettext('oct'),
      $cmail->gettext('nov'), $cmail->gettext('dec')
    );
    $settings['today'] = rcube_label('today');

    $cmail->output->command('plugin.getSettings', array('settings' => $settings));
  }
  
  function settingsTable($args) {
    if ($args['section'] == 'calendarlink') {
      $cmail = cmail::get_instance();
      
      $args['blocks']['calendar']['name'] = $this->gettext('calendar');
 
      $default_view = $cmail->config->get('default_view', "agendaWeek");    
      $field_id = 'rcmfd_default_view';
      $select = new html_select(array('name' => '_default_view', 'id' => $field_id));
      $select->add($this->gettext('day'), "agendaDay");
      $select->add($this->gettext('week'), "agendaWeek");
      $select->add($this->gettext('month'), "month");
      $args['blocks']['calendar']['options']['default_view'] = array(
        'title' => html::label($field_id, Q($this->gettext('default_view'))),
        'content' => $select->show($cmail->config->get('default_view')),
      );
      
      $time_format = $cmail->config->get('time_format', "HH:mm");    
      $field_id = 'rcmfd_time_format';
      $choices = array('HH:mm', 'H:mm', 'h:mmt');    
      $select = new html_select(array('name' => '_time_format', 'id' => $field_id));
      $select->add($choices);      
      $args['blocks']['calendar']['options']['time_format'] = array(
        'title' => html::label($field_id, Q($this->gettext('time_format'))),
        'content' => $select->show($cmail->config->get('time_format')),
      );
      
      $timeslots = $cmail->config->get('timeslots', 2);    
      $field_id = 'rcmfd_timeslot';
      $choices = array('1', '2', '3', '4', '6');    
      $select = new html_select(array('name' => '_timeslots', 'id' => $field_id));
      $select->add($choices);      
      $args['blocks']['calendar']['options']['timeslots'] = array(
        'title' => html::label($field_id, Q($this->gettext('timeslots'))),
        'content' => $select->show($cmail->config->get('timeslots')),
      );
      
      $first_day = $cmail->config->get('first_day', 1);    
      $field_id = 'rcmfd_timeslot';   
      $select = new html_select(array('name' => '_first_day', 'id' => $field_id));
      $select->add(rcube_label('sunday'), '0');
      $select->add(rcube_label('monday'), '1');
      $select->add(rcube_label('tuesday'), '2');
      $select->add(rcube_label('wednesday'), '3');
      $select->add(rcube_label('thursday'), '4');
      $select->add(rcube_label('friday'), '5');
      $select->add(rcube_label('saturday'), '6');
      $args['blocks']['calendar']['options']['first_day'] = array(
        'title' => html::label($field_id, Q($this->gettext('first_day'))),
        'content' => $select->show($cmail->config->get('first_day')),
      );
    }
    return $args;
  }
  
  function saveSettings($args) {
    if ($args['section'] == 'calendarlink') {
      $cmail = cmail::get_instance();
      $args['prefs']['default_view'] = get_input_value('_default_view', RCUBE_INPUT_POST);
      $args['prefs']['time_format'] = get_input_value('_time_format', RCUBE_INPUT_POST);
      $args['prefs']['timeslots'] = get_input_value('_timeslots', RCUBE_INPUT_POST);
      $args['prefs']['first_day'] = get_input_value('_first_day', RCUBE_INPUT_POST);
    }
    
    return $args;
  }
  
  function toGMT($time) {
    return date('Y-m-d H:i:s',$time - date('Z'));
  }
  
  function generateCSS() {
    $cmail = cmail::get_instance();
    $categories = $cmail->config->get('categories');    

    $css = "";
    if(!empty($categories)) {
      $css .= "<style type=\"text/css\">\n";
      foreach ($categories as $class => $color) {
        $css .= "." . $class . ",\n";
        $css .= ".fc-agenda ." . $class . " .fc-event-time,\n";
        $css .= "." . $class . " a {\n";
        $css .= "background-color: #" . $color . ";\n";
        $css .= "border-color: #" . $color . ";\n";
        $css .= "}\n";
      }
      $css .= "</style>";
    }
    return($css);
  }

  function generateHTML() {
    $cmail = cmail::get_instance();
    $categories = $cmail->config->get('categories');    

    $select = "<select name=\"categories\">\n";
    $select .= "<option value=\"\"></option>\n";
    foreach ($categories as $class => $color) {
      $select .= "<option value=\"" . $class . "\">" . $this->gettext($class) . "</option>\n";
    }
    $select .= "</select>";

    return($select);
  }
}  
?>