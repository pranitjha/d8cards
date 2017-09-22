<?php
/**
 * @file
 * Contains Drupal\cron_queue_email\Plugin\QueueWorker\RegisteredUserEmailBase.php
 */

namespace Drupal\cron_queue_email\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides base functionality for the NodePublish Queue Workers.
 */
abstract class RegisteredUserEmailBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

    /**
     * The node storage.
     *
     * @var \Drupal\Core\Entity\EntityStorageInterface
     */
    protected $userStorage;
    protected $mailManager;
    /**
     * Creates a new RegisteredUserEmailBase object.
     *
     * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
     *   The node storage.
     */
    public function __construct(EntityStorageInterface $user_storage,  MailManagerInterface $mail_manager) {
        $this->userStorage = $user_storage;
        $this->mailManager = $mail_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $container->get('entity.manager')->getStorage('user'),
            $container->get('plugin.manager.mail')
        );
    }

    /**
     * Publishes a node.
     *
     * @param NodeInterface $node
     * @return int
     */
    protected function sendEmail($user) {
        $params['message'] = 'Welcome new registered user. This is the email body';
        $result = $this->mailManager->mail('mymodule', 'register_user_send_email', 'pranit.er@gmail.com', 'en', $params , $reply=NULL,$send= TRUE);
        //mail($module, $key, $to, $langcode, $params = array(), $reply = NULL, $send = TRUE);
        if ($result['result'] !== true) {
            $message = t('There was a problem sending your email notification to @email for creating node @id.', array('@email' => $to, '@id' => $entity->id()));
            drupal_set_message($message, 'error');
            \Drupal::logger('d8mail')->error($message);
            return;
        }
        $message= 'email sent';
        drupal_set_message($message);
        \Drupal::logger('d8mail')->notice($message);
    }

    /**
     * {@inheritdoc}
     */
    public function processItem($data) {
        $user = $this->userStorage->load($data->uid);
        if ($user) {
            return $this->sendEmail($user);
        }
    }
}