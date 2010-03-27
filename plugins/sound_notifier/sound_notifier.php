<?php
/**
 * sound_notifier
 *
 *
 * @version 1.0 - 17.08.2010
 * @author Roland 'rosali' Liebl
 * @website http://myCrystal.googlecode.com
 * @licence GNU GPL
 *
 **/

/** USAGE
 *
 * #1- Register plugin ("./config/main.inc.php ::: $cmail_config['plugins']").
 *
 **/

class sound_notifier extends cmail_plugin
{

  public $task = 'mail|settings';

  function init()
  {

    $this->add_hook('render_page', array($this, 'bind_sound'));
    $this->add_hook('new_messages', array($this, 'play_sound'));
    $this->load_config('config/config.inc.php.dist');
    if(file_exists("./plugins/sound_notifier/config/config.inc.php"))
      $this->load_config('config/config.inc.php');               
    $cmail = cmail::get_instance();
    if ($cmail->task == 'settings') {
      $dont_override = $cmail->config->get('dont_override', array());
      if (!in_array('sound_notifier', $dont_override)) {
        $this->add_texts('localization/');
         $this->add_hook('user_preferences', array($this, 'prefs_table'));
        $this->add_hook('save_preferences', array($this, 'save_prefs'));
      }
    }
  }

  function bind_sound($args){

    if($args['template'] == "mail"){
      $cmail = cmail::get_instance();
      $this->include_script("js/soundmanager2-nodebug-jsmin.js");
      $sounds = $cmail->config->get('sound_notifier_registry');
      $choice = $cmail->config->get('sound_notifier_choice');
      if(!$choice)
        $choice = 'Default';
      $sound = $sounds[$choice];
      $cmail->output->set_env('sound_notifier', $sound);
      $this->include_script("js/sound_notifier.js");
      $content = $args['content'];
      $script  = "<script type=\"text/javascript\">\n";
      $script .= "soundManager.url = \"plugins/sound_notifier/swf/\";\n";
      $script .= "soundManager.debugMode = false;\n";
      $script .= "soundManager.consoleOnly = false;\n";
      $script .= "</script>\n";
      $cmail->output->add_footer($script);

    }
    
    return $args;
    
  }

  function play_sound($args){

    if($args['count'] > 0){
      $cmail = cmail::get_instance();
      $cmail->output->command("plugin.sound");
    }
  }

  function prefs_table($args)
  {
    if ($args['section'] == 'mailbox') {
      $this->add_texts('localization');
      $cmail = cmail::get_instance();
      $sound_notifier= $cmail->config->get('sound_notifier');
      $content = "";
      $sound  = $cmail->config->get('sound_notifier_choice');
      if(!$sound)
        $sound = 'Default';
      $sounds = $cmail->config->get('sound_notifier_registry');
      if(is_array($sounds)){
        $onchange = "";
        if(function_exists("json_encode")){
          $cmail->output->set_env('sound_notifier', $sound);
          $cmail->output->add_script("var sounds = " . json_encode($sounds) . ";");
          $this->include_script("js/soundmanager2-nodebug-jsmin.js");          
          $this->include_script("js/sound_notifier.js");
          $onchange = "cmail_playsound_onchange(this.value)";
          $script  = "<script type=\"text/javascript\">\n";
          $script .= "soundManager.url = \"plugins/sound_notifier/swf/\";\n";
          $script .= "soundManager.debugMode = false;\n";
          $script .= "soundManager.consoleOnly = false;\n";
          $script .= "</script>\n";          
        }       
        $field_id = 'rcmfd_sound_notifier_sounds';
        $select = new html_select(array('name' => '_sound_notifier_choice', 'id' => $field_id, 'onchange' => $onchange));
        foreach($sounds as $key => $val){
          $select->add($this->gettext($key),$key);        
        }
        $content .= $select->show($sound);
      }
      $field_id = 'rcmfd_sound_notifier'; 
      $checkbox = new html_checkbox(array('name' => '_sound_notifier', 'id' => $field_id, 'value' => 1));
      $content .= $checkbox->show($sound_notifier?1:0);      
      $args['blocks']['new_message']['options']['sound_notifier'] = array( 
            'title' => html::label($field_id, Q($this->gettext('soundnotifier'))), 
            'content' => $content . "\n" . $script
      );
    }

    return $args;

  }

  function save_prefs($args)
  {
    if($args['section'] == 'mailbox'){
      $args['prefs']['sound_notifier'] = get_input_value('_sound_notifier', cmail_INPUT_POST);
      $args['prefs']['sound_notifier_choice'] = get_input_value('_sound_notifier_choice', cmail_INPUT_POST);
      return $args;
    }
  }


}

?>