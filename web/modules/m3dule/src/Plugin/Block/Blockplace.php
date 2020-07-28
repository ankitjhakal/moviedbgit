<?php
namespace Drupal\m3dule\Plugin\Block;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Routing;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
/**
 * Provides a 'List config' Block.
 *
 * @Block(
 *   id = "list_config_block",
 *   admin_label = @Translation("List Config block"),
 *   category = @Translation("List Config World"),
 * )
 */
class Blockplace extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function listshow() {
    $config = \Drupal::config('m3dule.settings');
    // kint($config->get('title'));
    // kint($config->get('image'));
    // die();
    $items=array();
    $items[] = [
      'title' => $config->get('title'),
      'url' =>   $config->get('image'),
      'desc' => $config->get('description'),
     ];
    return array(
     'theme' => 'list',
     'items' => $items,
     'title' => 'Configuration',
    );
  }
  /**
   * [build description]
   * @return [type] [description]
   */
  public function build() {
    $data = $this->listshow();
    return  array(
      '#theme' => $data['theme'],
      '#items' => $data['items'],
      '#title' => $data['title'],
      '#cache' => [
        'max-age' => 0,
      ]
    );
  }
}
