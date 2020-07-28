<?php

namespace Drupal\m3dule\Controller;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use \Drupal\block\Entity\Block;


/**
 * Controller routines for HeyTaco! block.
 */
class TestedDI extends ControllerBase {

  protected $account;

  public function __construct(AccountInterface $account) {
    $this->account = $account;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }
  public function placeblockwithDI() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [];
    $plugin_block = $block_manager->createInstance('BlockWith_DI', $config);
    // $access_result = $plugin_block->access(\Drupal::currentUser());
    $render = $plugin_block->build();

    // kint($render);
    return array(
      '#type' => 'markup',
      '#markup' => '<h1> Last used on ' . $render['lastaccesstime'] . ' and by user ' . $render['name'] . ' </h1>',
    );
  }
}
