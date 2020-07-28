<?php
namespace Drupal\m3dule\Plugin\Block;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Provides a 'Hey Taco' Results Block.
 *
 * @Block(
 *   id = "BlockWith_DI",
 *   admin_label = @Translation("HeyTaco! Leaderboard"),
 * )
 */
class BlockWithDI extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var $account \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;
  protected $dateFormatter;
  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('date.formatter')
    );
  }

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $account, DateFormatterInterface $date_formatter) {
    // kint($configuration);
    // kint($plugin_id);
    // kint($plugin_definition);
    // kint($account);
    // die();
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->account = $account;
    $this->dateFormatter = $date_formatter;
  }

  public function build() {
    $datail= [];
    $date = $this->dateFormatter->format($this->account->getLastAccessedTime(), 'short');
    $detail['lastaccesstime']= $date;
    // kint($date);
    $detail['name']=$this->account->getUsername();
    return $detail;
  }
}
