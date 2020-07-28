<?php
namespace Drupal\m3dule\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
/**
 * Provides a 'Link' Block.
 *
 * @Block(
 *   id = "link_block",
 *   admin_label = @Translation("Links"),
 *   category = @Translation("login register links"),
 * )
 */

 class Link extends BlockBase {
   /**
   * {@inheritdoc}
   */

  public function build() {
    return array(
      '#theme' => 'link_block',
      '#loginlink' => '/user/login',
      '#registerlink' => '/user/register',
    );
  }
}
