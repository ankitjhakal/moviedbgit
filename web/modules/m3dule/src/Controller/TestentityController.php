<?php
namespace Drupal\m3dule\Controller;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
  * This class contains functions for display page like actor listing,movielist.
*/
class TestentityController extends ControllerBase {

  /**
     * This function is used to return movielist data array to twig file.
     * @return mixed
  */
  public function testEntity() {

    //Query fired to fetch node id of actor content type where title equal to $name.
    $entity_storage = \Drupal::entityTypeManager()->getStorage('node');
    $entity_ids = $entity_storage->getQuery()
      ->condition('type', 'actor')
      ->condition('status', '1')
      ->condition('title', 'aamir', 'CONTAINS')
      ->execute();
    kint($entity_ids);
    return $entity_ids;
 }
}
