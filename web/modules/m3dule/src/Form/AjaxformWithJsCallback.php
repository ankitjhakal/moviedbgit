<?php
namespace Drupal\m3dule\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;

class AjaxformWithJsCallback extends FormBase {

  /**
     * This function is used to return formid.
     * @return formid
  */
  public function getFormId() {
    return 'ajax_form';
  }
  /**
     * This function is used to return $form object.
     * @return mixed
  */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['cat_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('cat name'),
    ];
    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('log cat!'),
      '#ajax' => [
        'callback' => '::logSomething',
      ]
    ];
    $form['#attached']['library'][] = 'm3dule/m3dule.loggy';
    return $form;
  }

  public function logSomething(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(
      new InvokeCommand(NULL,'loggy', [$form_state->getValue('cat_name')])
    );
    return $response;
  }
  /**
     * This function is used to redirect after submission to movielist page.
     * @param  $form array ,$form_state Default parameters for form submission.
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
 }
}
?>
