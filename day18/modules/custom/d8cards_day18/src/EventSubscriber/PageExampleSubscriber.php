<?php
 /**
  * @file
  * Contains \Drupal\d8cards_day18\PageExampleSubscriber.
  */

 namespace Drupal\d8cards_day18\EventSubscriber;

 use Symfony\Component\EventDispatcher\EventSubscriberInterface;


 class PageExampleSubscriber implements EventSubscriberInterface {
   
   static function getSubscribedEvents() {
     $events['simple_page_load'][] = array('onSimplePageLoad', 0);
     return $events;
   }

   public function onSimplePageLoad() {
     drupal_set_message("Event Subscribed on Simple Page Load");

     \Drupal::logger('d8cards_day18')->notice('Simple page was displayed and Event Subscribed.');
   }
 }
