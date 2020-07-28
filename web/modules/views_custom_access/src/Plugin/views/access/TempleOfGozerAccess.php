<?php
namespace Drupal\views_custom_access\Plugin\views\access;

use Drupal\views\Plugin\views\access\AccessPluginBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
/**
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "temple_of_gozer_access",
 *   title = @Translation("Temple of Gozer"),
 *   help = @Translation("Access will be granted to only the Gatekeeper or the Keymaster.")
 * )
 */
class TempleOfGozerAccess extends AccessPluginBase {


  public function access(AccountInterface $account) {
    $full_moon = TRUE;
    if ($this->options['restriction'] === 'full_moon') {
      $full_moon = $this->templeAccess->isFullMoon();
    }
    return ($this->templeAccess->isUserTheGatekeeper($account) || $this->templeAccess->isUserTheKeymaster($account)) && $full_moon;
  }

  public function summaryTitle() {
    if (isset($this->options['restriction'])) {
      return $this->t('Temple of Gozer (Restriction: @restriction)', ['@restriction' => $this->options['restriction']]);
    }
    return $this->t('Temple of Gozer');
  }

  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['restriction'] = ['default' => 'none'];
    return $options;
  }

  public function buildOptionsForm(&$form, \Drupal\core\Form\FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['restriction'] = [
      '#type' => 'select',
      '#title' => $this->t('Additional Restriction'),
      '#default_value' => $this->options['restriction'],
      '#options' => [
        'none' => $this->t('None'),
        'full_moon' => $this->t('Full moon'),
      ],
    ];
  }

  public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_custom_access', 'temple_of_gozer.access_handler::access');
  }

}
?>
