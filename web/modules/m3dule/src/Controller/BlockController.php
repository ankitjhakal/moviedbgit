<?php
namespace Drupal\m3dule\Controller;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\block\Entity\Block;
/**
  * This class contains functions for logical operations for various functions of site.
  */
class BlockController extends ControllerBase {
  public function placeBlock() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [];
    $plugin_block = $block_manager->createInstance('list_config_block', $config);
    // $access_result = $plugin_block->access(\Drupal::currentUser());
    $render = $plugin_block->build();
    // kint($render);
    return $render;
  }
}
