<?php

/**
 * Archive (archivefolder)
 *
 * Sample plugin that adds a new button to the mailbox toolbar
 * To move messages to a special archive folder.
 * Based on Mark As Junk sample plugin.
 *
 * @version 1.2
 * @author Andre Rodier, Thomas Bruederli, Roland 'rosali' Liebl
 * @website http://myroundcube.googlecode.com 
 */
class archivefolder extends rcube_plugin
{
  public $task = 'mail|settings';

  function init()
  {  
    $this->register_action('plugin.archive', array($this, 'request_action'));   
    $this->add_hook('render_mailboxlist', array($this, 'render_mailboxlist'));
    $this->add_hook('list_prefs_sections', array($this, 'archivefoldersection'));
    $this->add_hook('user_preferences', array($this, 'prefs_dummy'));
    $this->add_texts('localization/');
    
    // There is no "Archived flags"
    // $GLOBALS['IMAP_FLAGS']['ARCHIVED'] = 'Archive';
    
    $cmail = cmail::get_instance();

    if ($cmail->task == 'settings') {
      $dont_override = $cmail->config->get('dont_override', array());
      if (!in_array('archive_mbox', $dont_override)) {
        $this->add_hook('user_preferences', array($this, 'prefs_table'));
        $this->add_hook('save_preferences', array($this, 'save_prefs'));
      }
    }
    else{
      if(isset($_SESSION['username'])){
        $skin  = $cmail->config->get('skin');
        $_skin = get_input_value('_skin', RCUBE_INPUT_POST);

        if($_skin != "")
          $skin = $_skin;

        if(!file_exists('plugins/archivefolder/skins/' . $skin . '/archivefolder.css')){
          $skin = "default";
        }
        $this->include_stylesheet('skins/' . $skin . '/archivefolder.css');          
      }
    }
  }
  
  function archivefoldersection($args)
  {
    $this->add_texts('localization');  
    $temp = $args['list']['server'];
    unset($args['list']['server']);
    $args['list']['folderslink']['id'] = 'folderslink';
    $args['list']['folderslink']['section'] = $this->gettext('archivefolder.folders');
    $args['list']['server'] = $temp;
    return $args;     
  }

  function prefs_dummy($args)
  {
    if ($args['section'] == 'folderslink') {
      $args['blocks']['main']['options']['folderslink']['title']    = $this->gettext('folders') . " ::: " . $_SESSION['username'];
      $args['blocks']['main']['options']['folderslink']['content']  = "<script type='text/javascript'>\n";
      $args['blocks']['main']['options']['folderslink']['content'] .= "  parent.location.href='./?_task=settings&_action=folders'\n";
      $args['blocks']['main']['options']['folderslink']['content'] .= "</script>\n";
    }

    return $args;

  }
   
  function render_mailboxlist($p)
  {
  
    $cmail = cmail::get_instance();
    if ($cmail->task == 'mail' && ($cmail->action == '' || $cmail->action == 'show') && ($archive_folder = $cmail->config->get('archive_mbox'))) {   
      $this->include_script('archivefolder.js');
      $this->add_button(
        array(
            'command' => 'plugin.archive',
            'imagepas' => 'archive_pas.png',
            'imageact' => 'archive_act.png',
            'title' => 'buttontitle',
            'domain' => $this->ID,
        ),
        'toolbar');
      
      // add label for contextmenu  
      $cmail->output->add_label(
        'archivefolder.buttontitle'
      );  

      // set env variable for client
      $cmail->output->set_env('archive_folder', $archive_folder);

      // add archive folder to the list of default mailboxes
      if (($default_folders = $cmail->config->get('default_imap_folders')) && !in_array($archive_folder, $default_folders)) {
        $default_folders[] = $archive_folder;
        $cmail->config->set('default_imap_folders', $default_folders);
      }
      
    }

    // set localized name for the configured archive folder  
    if ($archive_folder) {  
      if (isset($p['list'][$archive_folder]))  
        $p['list'][$archive_folder]['name'] = $this->gettext('archivefolder.archivefolder');  
      else // search in subfolders  
        $this->_mod_folder_name($p['list'], $archive_folder, $this->gettext('archivefolder.archivefolder'));  
    }  
          
    return $p;
  }
  
  function _mod_folder_name(&$list, $folder, $new_name)  
  {  
    foreach ($list as $idx => $item) {  
      if ($item['id'] == $folder) {  
        $list[$idx]['name'] = $new_name;  
        return true;  
      } else if (!empty($item['folders']))  
        if ($this->_mod_folder_name($list[$idx]['folders'], $folder, $new_name))  
          return true;  
    }  
    return false;  
  }  

  function request_action()
  {
    $this->add_texts('localization');      
    $uids = get_input_value('_uid', RCUBE_INPUT_POST);
    $mbox = get_input_value('_mbox', RCUBE_INPUT_POST);
    
    $cmail = cmail::get_instance();
    
    // There is no "Archive flags", but I left this line in case it may be useful
    // $cmail->imap->set_flag($uids, 'ARCHIVE');
    
    if (($archive_mbox = $cmail->config->get('archive_mbox')) && $mbox != $archive_mbox) {
      $cmail->output->command('move_messages', $archive_mbox);
      $cmail->output->command('display_message', $this->gettext('archived'), 'confirmation');
    }
    
    $cmail->output->send();
  }

  function prefs_table($args)
  {
    if ($args['section'] == 'folders') {
      $this->add_texts('localization');
      
      $cmail = cmail::get_instance();
      $select = cmail_mailbox_select(array('noselection' => '---', 'realnames' => true, 'maxlength' => 30));
      
      $args['blocks']['main']['options']['archive_mbox']['title'] = Q($this->gettext('archivefolder'));
      $args['blocks']['main']['options']['archive_mbox']['content'] = $select->show($cmail->config->get('archive_mbox'), array('name' => "_archive_mbox"));

    }

    return $args;
  }

  function save_prefs($args)
  {
    if ($args['section'] == 'folders') {  
      $args['prefs']['archive_mbox'] = get_input_value('_archive_mbox', RCUBE_INPUT_POST);  
      return $args;  
    }
  }

}
?>