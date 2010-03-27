<?php

/**
 * Password Reminder
 *
 * @version 1.0 - 29.06.2010
 * @author Roland 'rosali' Liebl
 * @website http://myCrystal.googlecode.com
 * @licence GNU GPL
 *
 * @TODO Use hmail_autoban plugin by planned http_request plugin (TODO with calendar plugin)
 *
 **/
 
/** USAGE
 *
 * #1- Configure "pwtools/config/config.inc.php.dist".
 * #2- Copy file to "config.inc.php" ("config.inc.php.dist" must still be present !!!).
 * #3- Register plugin ("./config/main.inc.php ::: $cmail_config['plugins']").
 *
 * Requirements: plugin savepassword
 *               plugin settings *
 *               plugin taskbar **
 *               plugin sendmessage
 *
 *               (*) recommended
 *               (**) If you don't use it nav in: ./?_task=settings&_action=plugin.pwtools_reset&_username=[username]
 *                    NOTICE: You can also use a form method post <input type=... name="_username" />
 *
 *
 **/

class pwtools extends cmail_plugin
{

  function init()
  {
    $this->add_texts('localization/');
    $cmail = cmail::get_instance();
    $this->_load_config('pwtools');
    $this->include_script('pwtools.js');
    
    // send password message stuff
    $this->add_hook('template_object_pwtoolsresetpw', array($this, 'pwtools_reset_form'));
    $this->add_hook('render_page', array($this, 'add_labels_to_login_page'));

    $this->add_hook('startup', array($this, 'pwtools_reset_do'));
    // catch action if requested in authenticated state
    $this->register_action('plugin.pwtools_reset_do', array($this, 'pwtools_redirect'));    
    $this->add_hook('startup', array($this, 'pwtools_reset'));
    // catch action if requested in authenticated state
    $this->register_action('plugin.pwtools_reset', array($this, 'pwtools_redirect'));
    
    // settings stuff
    $this->register_action('plugin.pwtools', array($this, 'pwtools_init'));
    $this->register_handler('plugin.pwtools_form', array($this, 'pwtools_form'));
    
    $this->register_action('pwtools-save', array($this, 'pwtools_save'));
    
  }
      
  function _load_config($plugin)
  {
    $cmail = cmail::get_instance();
    $config = "plugins/" . $plugin . "/config/config.inc.php";
    if(file_exists($config))
      include $config;
    else if(file_exists($config . ".dist"))
      include $config . ".dist";
    if(is_array($cmail_config)){
      if(is_array($cmail_config['settingsnav']) && is_array($cmail->config->get('settingsnav'))){
        $nav = array_merge($cmail->config->get('settingsnav'), $cmail_config['settingsnav']);
        $cmail_config['settingsnav'] = $nav;
      }
      $arr = array_merge($cmail->config->all(),$cmail_config);
      $cmail->config->merge($arr);
    }
  }
  
  // send password message stuff
  
  function pwtools_redirect()
  {
  
    $cmail = cmail::get_instance();
    $cmail->task = "mail";
    $cmail->action = "";
    header('Location: ./?_task=mail');
    exit;
    //$cmail->output->send("pwtools.redirect");  
  }
  
  function add_labels_to_login_page($a){
  
    if($a['template'] != "login")
      return $a;
  
    $cmail = cmail::get_instance();
    $cmail->output->add_label(
      'pwtools.pwtoolsuserempty',
      'pwtools.pwtoolsusernotfound'
    );   
    
    return $a;
  
  }
  
  function pwtools_reset($a){
    if($a['action'] != "plugin.pwtools_reset" || !isset($_SESSION['temp']))
      return $a;

    // kill remember_me cookies
    setcookie ('rememberme_user','',time()-3600);
    setcookie ('rememberme_pass','',time()-3600);

    $cmail = cmail::get_instance();      
    
    if(!isset($_POST['_username'])){ 
      $user = trim(urldecode($_GET['_username']));
    }
    else{
      $user = get_input_value('_username', cmail_INPUT_POST);
    }
    
    if(class_exists("hmail_login")){
      $temparr = explode("@",$user);
      if(!isset($temparr[1]))
        $user = $user . "@" . $cmail->config->get('hmail_default_domain');
      $user = hmail_login::resolve_alias($user);
    }   
   
    if($user != ""){  
      // get user row    
      $sql_result = $cmail->db->query("SELECT * FROM ".get_table_name('users')." WHERE  username=?", $user);
      $userrec = $cmail->db->fetch_assoc($sql_result);

      // user found
      if(!is_array($userrec))
        $error = 'pwtoolsusernotfound';
      else{
        $prefs = unserialize($userrec['preferences']);   
        //reminder enabled
        if($prefs['pwtoolsenabled'] != 1){
          $error = 'pwtoolsinactive';
        }
        else{
          // secret question/answer and external email defined      
          if($prefs['pwtoolsquestion'] != "" && $prefs['pwtoolsanswer'] != "" && $prefs['pwtoolsaddress'] != ""){
            $_SESSION['pwtools']['username'] = $user;
            $_SESSION['pwtools']['question'] = $prefs['pwtoolsquestion'];
            $_SESSION['pwtools']['answer']   = $prefs['pwtoolsanswer'];
            $_SESSION['pwtools']['email']    = $prefs['pwtoolsaddress'];
            $_SESSION['pwtools']['pass']     = $userrec['password'];
            $cmail->output->set_pagetitle($this->gettext('pwreminder'));
            // kill keep alive
            $cmail->output->add_script("cmail.set_env({task:'settings',keep_alive:false,action:'',comm_path:'./'});");
            $cmail->output->send("pwtools.recpw");          
          }
          else{
            $error = 'pwtoolsimcomplete';
          }
        }
      }
    }
    else{
      $error = 'pwtoolsuserempty';
    }
    
    $cmail->output->show_message("pwtools.".$error, 'error');
    $cmail->kill_session();
    $_POST['_user'] = $user;
    $cmail->output->send('login');
  
  }
  
  function pwtools_reset_form($a){
  
    $user = $_SESSION['pwtools']['username'];
    $question = $_SESSION['pwtools']['question'];
     
    // return the complete edit form as table
    $out .= '<fieldset><legend>' . $this->gettext('pwreminder') . ' ::: ' . $user . '</legend>' . "\n";
    $out .= '<br />' . "\n";
    $out .= "<table>\n";  
    $field_id = 'pwtoolsquestion';
    $input_pwtoolsquestion = new html_inputfield(array('name' => '_pwtoolsquestion', 'readonly' => 'readonly', 'id' => $field_id, 'value' => $question, 'maxlength' => 320, 'size' => 35));

    $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsquestion')),
                $input_pwtoolsquestion->show($question));                
                
    $field_id = 'pwtoolsanswer';
    $input_pwtoolsanswer = new html_inputfield(array('name' => '_pwtoolsanswer', 'id' => $field_id, 'value' => '', 'maxlength' => 320, 'size' => 35));

    $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsanswer')),
                $input_pwtoolsanswer->show());
                
    $out .= "\n</table>";
    $out .= '<br />' . "\n";
    $out .= "</fieldset>\n";
                       
    $field_id = 'pwtoolsusername';
    $input_pwtoolsusername = new html_hiddenfield(array('name' => '_username', 'id' => $field_id, 'value' => $user));
    $out .= $input_pwtoolsusername->show($user);
    
    $a['content'] = $out;
    
    return $a;
  
  }
  
  function pwtools_reset_do($a){
      
    if($a['action'] != "plugin.pwtools_reset_do" || !isset($_SESSION['temp']))
      return $a;      
      
    $cmail = cmail::get_instance();
    
    $user =  get_input_value('_username', cmail_INPUT_POST);
    $question = get_input_value('_pwtoolsquestion', cmail_INPUT_POST);
    $answer = get_input_value('_pwtoolsanswer', cmail_INPUT_POST);
    $pwtoolsanswer = $_SESSION['pwtools']['answer'];
    if($pwtoolsanswer != "")
      $pwtoolsanswer = $cmail->decrypt($pwtoolsanswer);
      
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE) 
      $s = "s"; 
    $Crystal_url = "http$s://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?_task=logout"; 
    
    $link  = '<a target="_new" href="' . $Crystal_url . '">'; 
    $link .= $Crystal_url; 
    $link .= '</a>'; 
  
    if(
      strtolower($question) == strtolower($_SESSION['pwtools']['question']) &&
      strtolower($answer) == strtolower($pwtoolsanswer) &&
      strtolower($user) == strtolower($_SESSION['pwtools']['username']) &&
      isset($_SESSION['pwtools']['email']) &&
      $_SESSION['pwtools']['pass'] != ""
      ){
      
        if(file_exists("./plugins/pwtools/localization/" . $_SESSION['language'] . ".inc")){ 
          $lang = $_SESSION['language']; 
        } 
        else{ 
          $lang = "en_US"; 
        } 
  
        if(file_exists("./plugins/pwtools/localization/" . $lang . "/send_pw_subject.txt")){ 
          $subject_pattern = file_get_contents("./plugins/pwtools/localization/" . $lang . "/send_pw_subject.txt"); 
        } 
        else if(file_exists("./plugins/pwtools/localization/en_US/send_pw_subject.txt")){ 
          $subject_pattern = file_get_contents("./plugins/pwtools/localization/en_US/send_pw_subject.txt"); 
        } 
        else{ 
          $subject_pattern = file_get_contents("./plugins/pwtools/localization/en_US/send_pw_subject.txt.dist"); 
        } 
        
        if(file_exists("./plugins/pwtools/localization/" . $lang . "/send_pw_body.html")){ 
          $body_pattern = file_get_contents("./plugins/pwtools/localization/" . $lang . "/send_pw_body.html"); 
        } 
        else if(file_exists("./plugins/pwtools/localization/en_US/send_pw_body.html")){ 
          $body_pattern = file_get_contents("./plugins/pwtools/localization/en_US/send_pw_body.html"); 
        } 
        else{ 
          $body_pattern = file_get_contents("./plugins/pwtools/localization/en_US/send_pw_body.html.dist"); 
        } 
    
        $footer = $cmail->config->get('pwtools_footer'); 
        if($body_pattern && $subject_pattern){  
          $subject = sprintf( 
                      $subject_pattern, 
                      $user,
                      $this->getVisitorIP()
                      );
                      
          $body = sprintf( 
                      $body_pattern, 
                      $user,
                      $cmail->decrypt($_SESSION['pwtools']['pass']),
                      $link,
                      $footer
                      );
          
          $rc = sendmessage::compose($cmail->config->get('admin_email'),$_SESSION['pwtools']['email'],$subject,$body,1);
          if($rc){
            $message = sprintf($this->gettext('pwtools.pwtoolscheckaccount'), $_SESSION['pwtools']['email'] );
            $type = "confirmation";
          }
          else{
            $message = $this->gettext('pwtools.pwtoolssendingfailed');
            $type = "error";
          }
          
          $cmail->output->command('display_message', $message, $type);
          $cmail->kill_session();
          $cmail->output->add_script("cmail.set_env({task:'settings',keep_alive:false,action:'',comm_path:'./'});");
          $_POST['_user'] = $user;
          $cmail->output->send('login');
        
        }
    }
    else{
      if(class_exists('hmail_autoban')){
        // ToDo request an invalid login
      }
      // kill keep alive
      $cmail->output->add_script("cmail.set_env({task:'settings',keep_alive:false,action:'',comm_path:'./'});");
      $cmail->output->show_message('pwtools.pwtoolsfailed','error');
      $cmail->output->send("pwtools.recpw");
    }
  
  }
  
  function getVisitorIP()
  { 
  
    //Regular expression pattern for a valid IP address 
    $ip_regexp = "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/"; 

    //Retrieve IP address from which the user is viewing the current page 
    if (isset ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) && !empty ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) { 
      $visitorIP = (!empty ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) ? $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] : ((!empty ($HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'])) ? $HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'] : @ getenv ('HTTP_X_FORWARDED_FOR')); 
    } 
    else { 
      $visitorIP = (!empty ($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty ($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : @ getenv ('REMOTE_ADDR')); 
    } 

    return $visitorIP; 
  }
  
  // settings stuff
  
  function pwtools_init()
  {
    
    $cmail = cmail::get_instance();
    $cmail->output->set_pagetitle($this->gettext('pwreminder')); 
    $cmail->output->send('pwtools.pwtools');
    
  }
  
  function pwtools_save()
  {
  
    $cmail = cmail::get_instance();
    $a_prefs = $cmail->user->get_prefs();  
 
    $pwtools['pwtoolsenabled'] = get_input_value('_pwtoolsenabled', cmail_INPUT_POST);
    $pwtools['pwtoolsaddress'] = get_input_value('_pwtoolsaddress', cmail_INPUT_POST);
    $pwtools['pwtoolsquestion'] = get_input_value('_pwtoolsquestion', cmail_INPUT_POST);
    $pwtools['pwtoolsanswer'] = $cmail->encrypt(get_input_value('_pwtoolsanswer', cmail_INPUT_POST));
    
    $a_prefs = array_merge($a_prefs,$pwtools);
    
    $cmail->user->save_prefs($a_prefs);
    
    // overwrite object property
    $cmail->user->data['preferences'] = serialize($a_prefs);
       
    $this->pwtools_init();
  
  }

  function pwtools_form()
  {
    $cmail = cmail::get_instance();

    // add some labels to client
    $cmail->output->add_label(
      'pwtools.pwtoolsquestionempty',    
      'pwtools.pwtoolsanswerempty',
      'pwtools.pwtoolsanswernotmatch',
      'pwtools.pwtoolsactive',
      'pwtools.pwtoolsinactive'
    );
    
    $cmail->output->add_script("var settings_account=true;");  

    $settings = $cmail->user->get_prefs();
    
    $enabled     = $settings['pwtoolsenabled'];
    $address     = $settings['pwtoolsaddress'];
    $question    = $settings['pwtoolsquestion'];
    $answer      = $cmail->decrypt($settings['pwtoolsanswer']);
                
		$IDENTITIES = $cmail->user->list_identities();
		
		$identities = array();
		foreach($IDENTITIES as $key => $identity){
      $email = $identity['email'];
      if(class_exists("hmail_login"))
        $email = hmail_login::resolve_alias($email);
      if(strtolower($email) != strtolower($cmail->user->data['username']))
        $identities[$email] = strtolower($email);
    }
          
    $cmail->output->set_env('product_name', $cmail->config->get('product_name'));

    // allow the following attributes to be added to the <table> tag
    $attrib_str = create_attrib_string($attrib, array('style', 'class', 'id', 'cellpadding', 'cellspacing', 'border', 'summary'));

    // return the complete edit form as table
    $out .= '<fieldset><legend>' . $this->gettext('pwreminder') . ' ::: ' . $cmail->user->data['username'] . '</legend>' . "\n";
    $out .= '<br />' . "\n";
    $out .= '<table' . $attrib_str . ">\n\n";

    // show pwtools properties
    
    if(count($identities) > 0){
      $cmail->output->add_script("cmail.pwtools = true;");
      $selector = '<select name="_pwtoolsaddress">' . "\n";
      foreach($identities as $key => $val){
        if(strtolower($address) == $val)
          $selected = " selected=\"selected\"";
        else
          $selected = "";
        $selector .= '<option value="' . $val . "\"$selected>" . $val . "</option>\n";
      }
      $selector .= "</select>\n";
          
      $field_id = 'pwtoolsenabled';
      $input_pwtoolsenabled = new html_checkbox(array('name' => '_pwtoolsenabled', 'id' => $field_id, 'value' => 1));

      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsenabled')),
                $input_pwtoolsenabled->show($enabled?1:0));
                
      $field_id = 'pwtoolsaddress';

      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsaddress')),
                $selector);
                
      $field_id = 'pwtoolsquestion';
      $input_pwtoolsquestion = new html_inputfield(array('name' => '_pwtoolsquestion', 'id' => $field_id, 'value' => $question, 'maxlength' => 320, 'size' => 40));

      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsquestion')),
                $input_pwtoolsquestion->show($question));                
                
      $field_id = 'pwtoolsanswer';
      $input_pwtoolsanswer = new html_inputfield(array('name' => '_pwtoolsanswer', 'id' => $field_id, 'value' => $answer, 'maxlength' => 320, 'size' => 40));

      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsanswer')),
                $input_pwtoolsanswer->show($answer));
                
      $field_id = 'pwtoolsanswerconfirm';
      $input_pwtoolsanswerconfirm = new html_inputfield(array('name' => '_pwtoolsanswerconfirm', 'id' => $field_id, 'value' => $answerconfirm, 'maxlength' => 320, 'size' => 40));

      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label>:</td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsanswerconfirm')),
                $input_pwtoolsanswerconfirm->show($answer));                
    }
    else{
      $cmail->output->add_script("cmail.pwtools = false;");
      $field_id = 'pwtoolsaddidentity';
      $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label></td><td>%s</td></tr>\n",
                $field_id,
                rep_specialchars_output($this->gettext('pwtoolsaddidentity')),
                "&raquo;&nbsp;<i><a target=\"_parent\" href=\"./?_task=settings&_action=add-identity&_iid=0\">" . $this->gettext('pwtoolsadd') . "</a></i>&nbsp;&laquo;");    
    }
                               
    $out .= "\n</table>";
    $out .= '<br />' . "\n";
    $out .= "</fieldset>\n";    

    $cmail->output->add_gui_object('pwtoolsform', 'pwtools-form');
    
    return $out;
  }

}

?>