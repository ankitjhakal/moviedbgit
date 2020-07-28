<?php

namespace Drupal\queueworker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class UserQueryForm.
 *
 * @package Drupal\queueworker\Form
 */
class UserQueryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_query_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#attributes' => [
      'placeholder' => 'Username',
      ],
      '#required' => true,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#attributes' => [
      'placeholder' => 'Email',
      ],
      '#required' => true,
    ];
    $form['query'] = [
      '#type' => 'textarea',
      '#attributes' => [
      'placeholder' => 'Query',
      ],
      '#description' => 'Upto 200 characters allowed',
      '#required' => true,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Send',
    ];
    return $form;
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var QueueFactory $queue_factory */
    $queue_factory = \Drupal::service('queue');
    /** @var QueueInterface $queue */
    $queue = $queue_factory->get('email_processor');
    $item = new \stdClass();
    $item->username = $form_state->getValue('name');
    $item->email = $form_state->getValue('email');
    $item->query = $form_state->getValue('query');
    $queue->createItem($item);
  }
}
