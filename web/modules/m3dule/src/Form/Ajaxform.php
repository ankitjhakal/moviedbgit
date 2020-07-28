<?php
namespace Drupal\m3dule\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class Ajaxform extends FormBase {

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
    $form['msg'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result"></div>',
    ];
    $form['no_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('first_no'),
    ];
    $form['no_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('second_no'),
    ];
    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('calculate'),
      '#ajax' => [
        'callback' => '::setMessage',
      ]
    ];
    return $form;
  }

  public function setMessage(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result',
        '<div class="my_result">' .$this->t('the result is @result',['@result' =>($form_state->getValue('no_1')+$form_state->getValue('no_2'))]),
        )
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
