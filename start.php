<?php

function log_emails_init() {
				
   elgg_unregister_plugin_hook_handler('send', 'notification:email', '_elgg_send_email_notification');
   elgg_register_plugin_hook_handler('send', 'notification:email', 'log_emails_send');

}
elgg_register_event_handler('init','system','log_emails_init');
		

/**
 * Create a log notification
 * 
 * @param string $hook   Hook name
 * @param string $type   Hook type
 * @param bool   $result Has the notification been sent
 * @param array  $params Hook parameters
 */
function log_emails_send($hook, $type, $result, $params) {
   $message = $params['notification'];

   $sender = $message->getSender();
   $recipient = $message->getRecipient();

   if (!$sender) {
      return false;
   }

   if (!$recipient || !$recipient->email) {
      return false;
   }

   $to = $recipient->email;

   $site = elgg_get_site_entity();
   // If there's an email address, use it - but only if it's not from a user.
   if (!($sender instanceof \ElggUser) && $sender->email) {
      $from = $sender->email;
   } else if ($site->email) {
      $from = $site->email;
   } else {
      // If all else fails, use the domain of the site.
      $from = 'noreply@' . $site->getDomain();
   }

   $date = date("Y-m-d H:i:s"); 
   error_log(sprintf(elgg_echo('log_emails:content'), $from, $to, $date, $message->subject, $message->body), 3, "php://stderr");

   return true;
   //return elgg_send_email($from, $to, $message->subject, $message->body, $params);
}

?>