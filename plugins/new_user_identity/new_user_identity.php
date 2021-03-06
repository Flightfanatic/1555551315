<?php
/**
 * New user identity
 *
 * Populates a new user's default identity from LDAP on their first visit.
 *
 * This plugin requires that a working public_ldap directory be configured.
 *
 * @version 1.0
 * @author Kris Steinhoff
 *
 * Example configuration:
 *
 *  // The id of the address book to use to automatically set a new
 *  // user's full name in their new identity. (This should be an
 *  // string, which refers to the $cmail_config['ldap_public'] array.)
 *  $cmail_config['new_user_identity_addressbook'] = 'People';
 *  
 *  // When automatically setting a new users's full name in their
 *  // new identity, match the user's login name against this field.
 *  $cmail_config['new_user_identity_match'] = 'uid';
 */
class new_user_identity extends cmail_plugin
{
    function init()
    {
        $this->add_hook('create_user', array($this, 'lookup_user_name'));
    }

    function lookup_user_name($args)
    {
        $cmail = cmail::get_instance();
        if ($addressbook = $cmail->config->get('new_user_identity_addressbook')) {
            $match = $cmail->config->get('new_user_identity_match');
            $ldap = $cmail->get_address_book($addressbook);
            $ldap->prop['search_fields'] = array($match);
            $results = $ldap->search($match, $args['user'], TRUE);
            if (count($results->records) == 1) {
                $args['user_name'] = $results->records[0]['name'];
            }
        }
        return $args;
    }
}
?>
