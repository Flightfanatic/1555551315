<?php

/**
 * RSS
 *
 * @version 1.0 - 15.01.2010
 * @author Roland 'rosali' Liebl
 * @website http://myCrystal.googlecode.com
 * @licence GNU GPL
 *
 **/
 
/** USAGE
 *
 * #1- Copy "./plugins/rss/config/config.inc.php.dist" to "config.inc.php".
 * #2- Configure config.inc.php-
 * #3- Register plugin ("./config/main.inc.php ::: $cmail_config['plugins']").
 *
 **/ 
 
class rss extends cmail_plugin
{
  public $task = "mail";
  
  function init() {
    $this->add_texts('localization/');  
    if(file_exists("./plugins/rss/config/config.inc.php"))
      $this->load_config('config/config.inc.php');
    else
      $this->load_config('config/config.inc.php.dist');
    $this->add_hook('template_object_loginform', array($this, 'show_rss_checkbox'));
    $this->add_hook('login_after', array($this, 'redirect_rss'));
    $this->add_hook('startup', array($this, 'check_auth'));
    $this->register_action('plugin.rss', array($this, 'show_rss'));
    $this->add_hook('render_page', array($this, 'rss_image')); 
  }
  
  function rss_image($p){
    if($p['template'] != "mail")
      return $p;
    $cmail = cmail::get_instance();  
    $skin  = $cmail->config->get('skin');
    
    // abort if there are no css adjustments
    if(!file_exists('plugins/rss/skins/' . $skin . '/rss.css')){
      if(!file_exists('plugins/rss/skins/default/rss.css'))   
        return $p;
      else
        $skin = "default";
    }

    $this->include_stylesheet('skins/' . $skin . '/rss.css');
    $this->include_script('rss.js');
    $html = '<div id="rss"><a href="#" onclick="show_rss_link()">RSS</a>';
    $p['content'] = $p['content'] . $html;
    return $p;  
  }

  function check_auth($args){
    if($args['action'] == "rss"){
      header('Location: ./?_task=mail&_action=plugin.rss');
      exit;
    }
    if($args['action'] == "plugin.rss"){
      if(!$_SESSION['user_id']){
        $this->http_auth();
      }
    }
    return $args;
  }

  function http_auth(){
    $cmail = cmail::get_instance();
    $CONFIG = $cmail->config->all();
     
    if(!empty($_POST['_user'])){
      $_SERVER['PHP_AUTH_USER'] = trim($_POST['_user']);
      $_SERVER['PHP_AUTH_PW'] = trim($_POST['_pass']);
    }
  
    if(!isset($_SERVER['PHP_AUTH_USER'])){  
      $this->http_unauthorized();
    }
  
    if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
  
      $user = trim($_SERVER['PHP_AUTH_USER']);
      $pass = trim($_SERVER['PHP_AUTH_PW']);    
      $host = $cmail->autoselect_host();

      if(class_exists("hmail_login", false)){
        $temp = explode("@",$user);
        if(count($temp) == 1 && $user != ""){
          $user = $user . "@" . $cmail->config->get('hmail_default_domain');
        }
        $user = hmail_login::resolve_alias($user);
      }
             
      if($cmail->login($user,$pass,$host)){
 
        // create new session ID
        unset($_SESSION['temp']);
        cmail_sess_regenerate_id();

        // send auth cookie if necessary
        $cmail->authenticate_session();

        // log successful login
        write_log('userlogins', sprintf('Successful HTTP login for %s (id %d) from %s',  
          $cmail->user->get_username(),  
          $cmail->user->ID,  
          $_SERVER['REMOTE_ADDR']));
          
        header('Location: ' .  $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        exit;          
      }
      else{
        $this->http_unauthorized();
      }      
    }
  }
  
  function http_unauthorized(){
    $cmail = cmail::get_instance();
    $CONFIG = $cmail->config->all();
    header('WWW-Authenticate: Basic realm="' . $CONFIG['useragent'] . '"');
    header('HTTP/1.0 401 Unauthorized');
    $js = '
    <html>
    <head>
    <title>' . $CONFIG['useragent'] . '</title>
    <script type="text/javascript">
    <!--
      document.location.href="' . $_SERVER['PHP_SELF'] . '";
    //-->
    </script>
    </head>
    <body></body>
    </html>
    ';
    echo $js;
    exit;
  }
  
  function show_rss_checkbox($args){
    $content  = $args['content'];
    $content = str_ireplace ('</tbody>',
      '<tr><td class="title"><label for="rcmrss">' . $this->gettext('rss_plugin_name','rss') . '</label></td><td><input name="_rss" value="1" type="checkbox" /></td>
      </tr></tbody>',$content);
    $args['content'] = $content;
    return $args;
  }
  
  function redirect_rss($args){
    if($_POST['_rss']){
      if(class_exists('wrapper',false)){
        echo '
          <html>
          <head>
          <title>' . $CONFIG['useragent'] . '</title>
          <script type="text/javascript">
          <!--
            parent.location.href="' . $_SERVER['PHP_SELF'] . '?_action=plugin.rss";
          //-->
          </script>
          </head>
          <body></body>
          </html>
        ';
      }
    	else{
        header('Location: ./?_task=mail&_action=plugin.rss');
      }
    	exit;
    }
    return $args;
  }

  function show_rss(){
    $cmail = cmail::get_instance();
    $IMAP = $cmail->imap;
    $CONFIG = $cmail->config->all();
    include "rss_driver.inc.php";
    exit;
  }
}

?>