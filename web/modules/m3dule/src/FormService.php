<?php
namespace Drupal\m3dule;
class FormService {

  public function getresult($sid,$webid) {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM {webform_submission_data}
      where sid = $sid and webform_id = '$webid'");
    $arr = $query->fetchAll();
    $output=array();
    $i=0;
    foreach($arr as $value){
      $output[$i][$value->name]=$value->name;
      $output[$i]['value']=$value->value;
      $i++;
    }
    $count=0;
    if($output[0]['value']=="600") {
      $count+=1;
    }
    if($output[1]['value']=="8") {
      $count+=1;
    }
    if($output[10]['value']=="50") {
      $count+=1;
    }
    if($output[11]['value']=="20") {
      $count+=1;
    }
    if($count>=1) {
      return "You have given ".$count ." correct answers. So you have passed the ".$output[9]['value']." quiz";
    }
    else {
      return "You have given ".$count." correct answers. So you have failed the ".$output[9]['value']." quiz";
    }
  }

}
 ?>
