<?php

/**
 * @file
 * Contains \Drupal\assets_test\Controller\AttachAssetsController.
 */

namespace Drupal\attach_assets\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Query\TableSortExtender;
use Drupal\Core\Database\Database;


/**
 * Class AttachAssetsController.
 */
class AttachAssetsController extends ControllerBase {
  /**
   * Index page for attach assets example.
   *
   * @return mixed
   *   Render array.
   */
  public function index() {
    $header = array(t('ID'), t('Title'), t('Created Date'));

    $query = \Drupal::database()->select('node_field_data','nd');
    $query->fields('nd', array('nid', 'title', 'created'));
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $result = $pager->execute();

    foreach($result as $row) {
      $row->created = date("d-m-Y", $row->created);
      $rows[] = array('data' => (array) $row);
    }

    $build = array(
      '#markup' => t('List of all Nodes')
    );

    $build['node_table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows
    );
    $build['pager'] = array(
      '#type' => 'pager'
    );
    $build['#attached']['library'][] = 'attach_assets/css_on_page';
    return $build;
  }

  /**
   *
   * @return mixed
   */

  /**
   * Get recent nodes.
   *
   * @param int $count
   *   Number of nodes to fetch.
   * @return \Drupal\Core\Database\StatementInterface|null
   *   A prepared statement, or NULL if the query is not valid.
   */
  protected function recentNodes($count = 10) {
    $query = $this->database->select('node', 'n');
    $query->join('node_field_data', 'nfd', 'n.nid = nfd.nid');

    return $query->fields('n')
      ->fields('nfd')
      ->addTag('node_access')
      ->addMetaData('base_table', 'node')
      ->orderBy('nfd.created', 'DESC')
      ->range(0, $count)
      ->execute();
  }
}
