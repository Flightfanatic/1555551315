<?php

/**
 * Keyboard shortcuts
 *
 * Enables some common tasks to be executed with keyboard shortcuts
 *
 * @version 1.0
 * @author Patrik Kullman
 * @website http://www.netzorz.se
 * 
 * Shortcuts, list view:
 * ?:	Show shortcut help
 * a:	Select all visible messages
 * A:	Mark all as read (as Google Reader)
 * c:	Compose new message
 * f:	Forward message
 * j:	Go to previous page of messages (as Gmail)
 * k:	Go to next page of messages (as Gmail)
 * p:	Print message
 * r:	Reply to message
 * R:	Reply to all of message
 * s:	Jump to quicksearch
 * u:	Check for new mail (update)
 *
 * Shortcuts, mail view:
 * f:	Forward message
 * j:	Go to previous message (as Gmail)
 * k:	Go to next message (as Gmail)
 * p:	Print message
 * r:	Reply to message
 * R:	Reply to all of message
 */
class keyboard_shortcuts extends rcube_plugin
{
    public $task = 'mail';
    
    function init()
    {
      // only init in authenticated state and if newuserdialog is finished
      if($_SESSION['username'] && empty($_SESSION['plugin.newuserdialog'])){
        $this->include_stylesheet('jquery-ui-1.7.2.custom.css');
        $this->include_stylesheet('keyboard_shortcuts.css');
        $this->include_script('jquery-ui-1.7.2.custom.min.js');
        $this->include_script('keyboard_shortcuts.js');
        $this->add_hook('template_container', array($this, 'html_output'));
        $this->add_texts('localization', true);
      }
    }

    function html_output($p) {
      if ($p['name'] == "listcontrols") {
        $c = "";
        $c .= "<div id='keyboard_shortcuts_help'>";
        $c .= "";
        $c .= "<div><h4>".$this->gettext("mailboxview")."</h4>";
        $c .= "<div class='shortcut_key'>?</div> ".$this->gettext('help')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>a</div> ".$this->gettext('selectallvisiblemessages')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>A</div> ".$this->gettext('markallvisiblemessagesasread')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>c</div> ".$this->gettext('compose')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>f</div> ".$this->gettext('forwardmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>j</div> ".$this->gettext('previousmessages')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>k</div> ".$this->gettext('nextmessages')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>p</div> ".$this->gettext('printmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>r</div> ".$this->gettext('replytomessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>R</div> ".$this->gettext('replytoallmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>s</div> ".$this->gettext('quicksearch')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>u</div> ".$this->gettext('checkmail')."<br class='clear' />";
        $c .= "</div>";
        $c .= "<div><h4>".$this->gettext("messagesdisplaying")."</h4>";
        $c .= "<div class='shortcut_key'>f</div> ".$this->gettext('forwardmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>j</div> ".$this->gettext('previousmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>k</div> ".$this->gettext('nextmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>p</div> ".$this->gettext('printmessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>r</div> ".$this->gettext('replytomessage')."<br class='clear' />";
        $c .= "<div class='shortcut_key'>R</div> ".$this->gettext('replytoallmessage')."<br class='clear' />";
        $c .= "</div></div>";
        $cmail = cmail::get_instance();   
        $skin  = $cmail->config->get('skin');
        if(!file_exists('plugins/keyboard_shortcuts/skins/' . $skin . '/images/keys-help.png')){
          $skin = "default";
        }
        $c .= "<a href='#' title='".$this->gettext("show")." ".$this->gettext("keyboard_shortcuts")."' onclick='return keyboard_shortcuts_show_help()'><img align='top' src='plugins/keyboard_shortcuts/skins/".$skin."/images/keys-help.png'></a>";
        $p['content'] = $c . $p['content'];
      }
      return $p;
    }
}
