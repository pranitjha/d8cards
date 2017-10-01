<?php

namespace Drupal\mymodule\EventSubscriber;

use Drupal\Core\Session\AccountProxy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CustomHeaderSubscriber implements EventSubscriberInterface {

  /**
   * @var AccountProxy
   */
  protected $currentUser;
  /**
   * CustomHeaderSubscriber constructor.
   *
   * @param AccountProxy $currentUser
   */
  public function __construct(AccountProxy $currentUser) {
    $this->currentUser = $currentUser;
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE] = array('addAccessAllowOriginHeaders', 0);
    return $events;
  }

  public function addAccessAllowOriginHeaders(FilterResponseEvent $event) {
    if (!$this->currentUser->id()) {
      $response= $event->getResponse();
      $response->headers->set('Access-Control-Allow-Origin', '*');
    }
  }

}
