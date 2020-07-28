<?php
namespace Drupal\m3dule\Controller;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\image\Entity\ImageStyle;
/**
  * This class contains functions for display page like actor listing,movielist.
*/
class PhpController extends ControllerBase {

  /**
     * This function is used to return movielist data array to twig file.
     * @return mixed
  */
  public function movie_list() {
    // To get form object
    $form = $this->formBuilder()->getForm('Drupal\m3dule\Form\FilterByName');
    // Get render html of form
    $form_rendered = \Drupal::service('renderer')->render($form);
    // Get query parameter value
    $name = \Drupal::request()->query->get('word');
    if($name == " ") {
      $name=NULL;
    }
    //Query fired to fetch node id of actor content type where title equal to $name.
    $bundle = 'actor';
    $query = \Drupal::entityQuery('node')
     ->condition('status', 1)
     ->condition('type', $bundle)
     ->condition('title', $name, 'CONTAINS');
    $act_id = $query->execute();
    //Query fired to fetch node id of movie content type where title equal to $name.
    $bundle = 'movie';
    if($name) {
      // kint($act_id);
      $query = \Drupal::entityQuery('node')
         ->condition('status', 1)
         ->condition('type', $bundle)
         ->condition('title', $name, 'CONTAINS');
      $res=$query->execute();
      // kint($res);
      if(!empty($act_id)) {
        foreach ($act_id as $id) {
         // code...
          $query = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('type', $bundle)
            ->condition('field_paragraph.entity:paragraph.field_actor.target_id',$id);
          $res=array_merge($res,$query->execute());

        }
        // kint($res);
      }
      $result = array_unique($res);
      // kint($result);
      $nids = $result;
    }
    //Query fired to fetch node ids of movie where actor node id existin paragraph field.
    else {
     $query = \Drupal::entityQuery('node')
       ->condition('status', 1)
       ->condition('type', $bundle)
       ->sort('field_release_date', 'DESC');
     $ids = $query->execute();
     $nids = $ids;
    }
    // kint($nids);
    if(empty($nids)) {
     drupal_set_message("No Results Found");
     return $this->redirect('m3dule_movie');

    }
    else {
     // Load all nids through this fucntion.
     $nodes = entity_load_multiple('node', $nids);
     $items = array();
     $actor = array();
     // Foreach for fetching all fields data and stored in an array $items
     foreach($nodes as $node) {
       $mid = $node->id();
       $node_title = $node->title->value;
       $node_body = $node->get('body')->value;
       $date = $node->get('field_release_date')->value;
       $database = \Drupal::database();
       $query = $database->query("SELECT value FROM {votingapi_result}
          where function = 'vote_average' and entity_id = $mid");
       $result = $query->fetchAll();
       $rating = $result[0]->value/20;
       $halfStarFlag = false;
       if($result[0]->value%20 !=0) {
         $halfStarFlag = true;
       }
       $node_image_fid = $node->get('field_movie_poster')->target_id;
       if (!is_null($node_image_fid)) {
         $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
         $image_entity_url = $image_entity->uri->value;
         $image_entity_url = ImageStyle::load('thumbnail')->buildUrl($image_entity_url);
       }
       else {
         $image_entity_url = "/sites/default/files/default_images/obama.jpg";
       }
       // To access paragraph field using it's target id.
       $target_id = $node->get('field_paragraph')->target_id;
       $paragraph = Paragraph::load($target_id);
       $values = array();
       // Get all the values of node id from field actor.
       $array = $paragraph->field_actor->getValue();
       // kint($array);
       foreach($array as $value) {
         // kint($value);
         if(isset($value['target_id'])) {
           $values[] = $value['target_id'];
         }
       }
       $j = 0;
       $actor = array();
       $url = array();
       // Fetch actor name after loading a node and url to attach with actorname.
       foreach($values as $value) {
         $node_details = Node::load($value);
         $actor[$j]['name'] = $node_details->title->value;
         $actor[$j]['urls'] = '/movielist/node/'.$value;
         $j++;
       }
       $items[] = [
         'name' => $node_title,
         'url' => $image_entity_url,
         'desc' => $node_body,
         'date' => $date,
         'rating' => $rating,
         'cast' => $actor,
         'url_movie' => "/node/".$mid,
         'halfStarFlag' => $halfStarFlag,
       ];
     }
     $length = count($items);
     $content_per_page = 2;
     $no_of_pages = ceil($length/$content_per_page);
     $current_page = \Drupal::request()->query->get('q');
     if ($current_page == NULL || $current_page > $no_of_pages) {
       $current_page = 1;
     }
     $offset = ($current_page-1)*$content_per_page;
     // Return an renderable array applying unique theme for page.

     return array(
       '#theme' => 'movie_list',
       '#items' => array_slice($items, $offset, $content_per_page),
       '#title' => 'our movie list',
       '#no_of_pages' => $no_of_pages,
       '#form' => $form_rendered,
       '#current_page' => $current_page,
       '#word' => $name,
     );
    }
  }
  /**
     * This function is used to return actor list data array.
     * @return mixed
  */
  public function actor_list() {
    // Query fired to fetch all node ids of movie content type.
    $bundle = 'actor';
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle)
        ->pager(3);
    $nids = $query->execute();
    if (empty($nids)) {
      drupal_set_message("No Results Found");
      return $this->redirect('m3dule_actor');
    }
    else {
      $m_ids = array();
      // Fetched the recent movie node id of an actor which contains that actor nodeid.
      foreach($nids as $nid) {
        $bundle = 'movie';
        $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle)
        ->condition('field_paragraph.entity:paragraph.field_actor.target_id',$nid)
        ->range(0, 1)
        ->sort('field_release_date', 'DESC');
        $m_ids[] = $query->execute();
      }
      $actor_nodes = entity_load_multiple('node', $nids);
      $items = array();
      // Foreach for fetching all fields data and stored in an array $items
      foreach($actor_nodes as $node) {
        $nid = $node->id();
        $database = \Drupal::database();
        $query = $database->query("SELECT value FROM {votingapi_result}
           where function = 'vote_average' and entity_id = $nid");
        $result = $query->fetchAll();
        $rating = $result[0]->value/20;
        $halfStarFlag = false;
        if($result[0]->value%20 !=0) {
          $halfStarFlag = true;
        }
        $node_title = $node->title->value;
        $node_body = $node->get('body')->value; //can use getString() getValue() instead of value
        $ratings = $node->field_rating->getValue();
        $rating = $ratings[0]['rating'];
        $rating = $rating/20;
        $node_image_fid = $node->get('field_actor_image')->target_id;
        if(!is_null($node_image_fid)) {
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $image_entity_url = $image_entity->uri->value;
          $image_entity_url = ImageStyle::load('thumbnail')->buildUrl($image_entity_url);
        }
        else {
          $image_entity_url = "/sites/default/files/default_images/obama.png";
        }
        $items[] = [
          'name' => $node_title,
          'url' => $image_entity_url,
          'desc' => $node_body,
          'ratings' => $rating,
          'actor_url' => "/node/".$nid,
          'halfStarFlag' => $halfStarFlag,
        ];
      }
      // Added two columns of recent movie name with its url.
      $count=0;
      foreach($m_ids as $id) {
        foreach ($id as $value) {
          $node =  Node::load($value);
          $movie_title = $node->title->value;
          $items[$count]['recent_movie'] = $movie_title;
          $items[$count]['movie_url'] = "/node/".$value;
          $count = $count+1;
        }
      }
    }

    // Return an renderable array applying unique theme for page.
    $path = base_path();
     return [
       'results' => array(
         '#theme' => 'actor_list',
         '#items' => $items,
         '#title' => 'Actor list',
         '#path' => 'path'),
     'pager' => [
       '#type' => 'pager'
       ],
     ];

  }
  /**
     * This function is used to return list of movies of a particular actor.
     * @param  $type type of id       $node  id of a node.
     * @return mixed
  */
  public function actor_movieslist($type,  $node) {
    $node_details = Node::load($node);
    $list_title = $node_details->title->value;
    $list_title = 'listed movies of '.$list_title;
    $nid = $node_details->nid->value;
    $bundle = 'movie';
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle)
        ->condition('field_paragraph.entity:paragraph.field_actor.target_id',$nid)
        ->sort('field_release_date', 'DESC');
    $m_ids = $query->execute();
    if(empty($m_ids)) {
      drupal_set_message("No Results Found");
      return $this->redirect('m3dule_actor_movielist');
    }
    else {
      $nodes = entity_load_multiple('node', $m_ids);
      $items = array();
      $actor = array();
      foreach($nodes as $node) {
        $mid = $node->id();
        $node_title = $node->title->value;
        $node_image_fid = $node->get('field_movie_poster')->target_id;
        if(!is_null($node_image_fid)) {
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $image_entity_url = $image_entity->uri->value;
          $image_entity_url = ImageStyle::load('thumbnail')->buildUrl($image_entity_url);
        }
        else {
          $image_entity_url = "/sites/default/files/default_images/obama.png";
        }
        $target_id = $node->get('field_paragraph')->target_id;
        $paragraph = Paragraph::load($target_id);
        $values = array();
        $array = $paragraph->field_actor->getValue();
        foreach($array as $value) {
          if(isset($value['target_id'])) {
            $values[] = $value['target_id'];
          }
        }
        $j = 0;
        $no = 0;
        $actor = array();
        $url = array();
        // Fetched costar name and role of actor in particular movie.
        foreach($values as $value) {
          $node_details = Node::load($value);
          /* If value of nid($value) of actor equal to $nid,will get role of that
             Actor in that movie.
          */
          if($value == $nid) {
            $roles = $paragraph->field_role->getValue();
            if(isset($roles[$no]['value'])) {
              $actor_role = $roles[$no]['value'];
            }
            else {
              $actor_role='none';
            }
          }
          // Else fetched costar of movie with its id.
          else {
            $actor[$j]['name'] = $node_details->title->value;
            $actor[$j]['actor_id'] = $value;
            $j++;
          }
          $no++;
        }
        // kint($actor_role);
        $items[] = [
          'movie_name' => $node_title,
          'image_path' => $image_entity_url,
          'actor_role' => $actor_role,
          'costars' => $actor,
          'movie_url' =>  $mid,
        ];
      }
      return array(
        '#theme' => 'actor_movies_list',
        '#items' => $items,
        '#title' => $list_title,
      );
    }
  }
  /**
     * This function is used to return JsonResponse to popup a box for costar.
     * @param  $movie=Null,$nid=Null Default parameters for movie and actor node id.
     * @return mixed
  */
  public function costar($movie=NULL, $nid=NULL) {
    $node = Node::load($movie);
    $target_id = $node->get('field_paragraph')->target_id;
    $paragraph = Paragraph::load($target_id);
    $values = array();
    $array = $paragraph->field_actor->getValue();
    foreach($array as $value) {
      if(isset($value['target_id'])) {
        $values[] = $value['target_id'];
      }
    }
    $no = 0;
    $actor = array();
    foreach($values as $value) {
      $node = Node::load($value);
      if($value == $nid) {
        $roles = $paragraph->field_role->getValue();
        if(isset($roles[$no]['value'])) {
          $role = $roles[$no]['value'];
        }
        else {
          $role='none';
        }
        $node_title = $node->title->value;
        $node_image_fid = $node->get('field_actor_image')->target_id;
        // kint($node_image_fid );
        if(!is_null($node_image_fid)) {
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $image_entity_url = $image_entity->uri->value;
          $image_entity_url = ImageStyle::load('thumbnail')->buildUrl($image_entity_url);
        }
        else {
          $image_entity_url = "/sites/default/files/default_images/obama.png";
        }
      }
      $no++;
    }
    $items = [
       'name' => $node_title,
       'image' => $image_entity_url,
       'role' => $role,
     ];
    return new JsonResponse($items);
  }
}
?>
