<?php

/**
 * Additional Message Headers
 *
 * Very simple plugin which will read additional headers for outgoing messages from the config file.
 *
 * Enable the plugin in config/main.inc.php and add your desired headers.
 *
 * @version 1.0
 * @author Ziba Scott
 * @website http://Crystal.net
 * 
 * Example:
 *
 * $cmail_config['additional_message_headers']['X-Remote-Browser'] = $_SERVER['HTTP_USER_AGENT'];
 * $cmail_config['additional_message_headers']['X-Originating-IP'] = $_SERVER['REMOTE_ADDR'];
 * $cmail_config['additional_message_headers']['X-Crystal-Server'] = $_SERVER['SERVER_ADDR'];
 * if( isset( $_SERVER['MACHINE_NAME'] )) {
 *     $cmail_config['additional_message_headers']['X-Crystal-Server'] .= ' (' . $_SERVER['MACHINE_NAME'] . ')';
 * }
 */
class additional_message_headers extends cmail_plugin
{
    public $task = 'mail';
    
    function init()
    {
        $this->add_hook('outgoing_message_headers', array($this, 'message_headers'));
    }

    function message_headers($args){

        // additional email headers
        $additional_headers = cmail::get_instance()->config->get('additional_message_headers',array());
        foreach($additional_headers as $header=>$value){
            $args['headers'][$header] = $value;
        }

        return $args;
    }
}
