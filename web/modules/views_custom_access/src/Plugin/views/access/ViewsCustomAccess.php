<?php


namespace Drupal\views_custom_access\Plugin\views\access;

use Drupal\views\Plugin\views\access\AccessPluginBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Class ViewsCustomAccess
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *     id = "ViewsCustomAccess",
 *     title = @Translation("Customised access for views"),
 *     help = @Translation("Add custom logic to access() method"),
 * )
 */
class ViewsCustomAccess extends AccessPluginBase
{
    /**
     * {@inheritdoc}
     */
    public function summaryTitle()
    {
        return $this->t('Customised Settings');
    }


    protected function defineOptions() {
      $options = parent::defineOptions();
      $options['restriction'] = ['default' => 'none'];
      // kint($options);
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
      // kint($form);
    }
    /**
    * {@inheritdoc}
    */
    public function alterRouteDefinition(Route $route)
    {
       $route->setRequirement('_access', 'TRUE');
    }
    /**
     * {@inheritdoc}
     */
    public function access(AccountInterface $account)
    {
        // Spurious logic check
        $ret = rand(0, 1);
        $message = $ret == 0 ? 'won\'t' : 'will';
        // \Drupal::messenger()->addMessage($ret . ' so ' . $message . ' display the view');
        Drupal_set_message($ret . ' so ' . $message . ' display the view');

        return $ret;
    }


}
?>
