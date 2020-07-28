<?php
namespace Drupal\m3dule\Plugin\WebformHandler;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "m3dule_custom_validator",
 *   label = @Translation("Query length validator"),
 *   category = @Translation("Settings"),
 *   description = @Translation("Form alter to validate it."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class WebVal extends WebformHandlerBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;
  protected $FormService;

  /**
  * Class constructor.
  */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $FormService) {
    $this->FormService = $FormService;
  }

  /**
  * {@inheritdoc}
  */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
   return new static(
     $container->get('m3dule.result')
   );
  }
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
   // kint($webform_submission);
    $values = $webform_submission->getData();
    // kint($values);
    $sid = $webform_submission->id();
    // kint($sid);
    // drupal_set_message($sid);
    // $service = \Drupal::service('m3dule.result');
    // $msg = $service->getresult($sid,"quiz");
    $msg = $this->FormService->getresult($sid, "quiz");
    drupal_set_message($msg);
  }

  // public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
  //   drupal_set_message(kint($form_state));
  //   // $this->validateQuery($formState);
  // }
  // /**
  //  * Validate Query.
  //  */
  // private function validateQuery(FormStateInterface $formState) {
  //   $values = $formState->getValues('data');
  //   // kint($formState);
  //   $desc = explode(" ", $values['query']);
  //   // $desc = explode(" ", $values);
  //   // if (!isset($desc[9])) {
  //   //   // kint($formState);
  //   //   $formState->setErrorByName('query', $this->t('Query length must be greater than 10 words!'));
  //   // }
  // }
}
