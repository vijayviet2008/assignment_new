<?php

namespace Drupal\specbee_location\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\specbee_location\CurrentTime;

/**
 * Provides site location and time.
 *
 * @Block(
 *   id = "specbee_location_time",
 *   admin_label = @Translation("Location and Time"),
 *   category = @Translation("specbee_location")
 * )
 */
class LocationTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current time service.
   *
   * @var Drupal\specbee_location\CurrentTime
   */
  protected $currentTime;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Constructs a new WorkspaceSwitcherBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param Drupal\specbee_location\CurrentTime $current_time
   *   The current time.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, CurrentTime $current_time, CacheBackendInterface $cache_backend) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->currentTime = $current_time;
    $this->cacheBackend = $cache_backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('specbee_location.current_time'),
      $container->get('specbee_location.specbee_location_cache')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $cid = 'specbee_location:' . \Drupal::languageManager()->getCurrentLanguage()->getId();
    $tags = [$cid];
    if ($cache = $this->cacheBackend->get($cid)) {
      $data = $cache->data;
    }
    else {
      $admin_config = $this->configFactory->get('specbee_location.settings');
      $country = $admin_config->get('country');
      $city = $admin_config->get('city');
      $data = [
        'country' => $country,
        'city' => $city,
      ];
      $this->cacheBackend->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT, $tags);
    }

    $current_time = $this->currentTime->getCurrentTime();
    $output = [
      '#theme' => 'location_time',
      '#data' => $data,
      '#current_time' => $current_time,
      '#cache' => [
        'max-age' => 60,
        'tags' => ['timezone'],
        'context' => ['route.specbee_location.settings_form'],
      ],
    ];
    return $output;
  }

}
