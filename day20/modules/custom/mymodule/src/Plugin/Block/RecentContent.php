<?php

namespace Drupal\mymodule\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\node\NodeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'RecentContent' block.
 *
 * @Block(
 *  id = "recent_content",
 *  admin_label = @Translation("Recent content"),
 * )
 */
class RecentContent extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Node storage.
   *
   * @var NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Renderer.
   *
   * @var RendererInterface
   */
  protected $renderer;

  /**
   * Current user.
   *
   * @var AccountProxy
   */
  protected $accountProxy;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      NodeStorageInterface $node_storage,
      RendererInterface $renderer,
      AccountProxy $accountProxy
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->nodeStorage = $node_storage;
    $this->renderer = $renderer;
    $this->accountProxy = $accountProxy;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->get('entity.manager')->getStorage('node'),
        $container->get('renderer'),
        $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#cache']['tags'][] = 'node_list';
    $build['#cache']['contexts'][] = 'user';

    $ids = $this->nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('uid', $this->accountProxy->id())
        ->sort('changed', 'DESC')
        ->range(0, 5)
        ->execute();
    $nodes = $this->nodeStorage->loadMultiple($ids);

    foreach ($nodes as $node) {
      $build['recent_content']['#markup'] .= '- ' . $node->label() . '<br />';
      $this->renderer->addCacheableDependency($build, $node);
    }

    return $build;
  }

}
