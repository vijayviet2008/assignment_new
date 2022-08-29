<?php

namespace Drupal\specbee_location;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Returns current time for specbee location.
 */
class CurrentTime {


  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new WorkspaceSwitcherBlock instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Get formated current time.
   */
  public function getCurrentTime() {
    $admin_config = $this->configFactory->get('specbee_location.settings');
    $site_timezone = $admin_config->get('site_timezone');
    $now = DrupalDateTime::createFromTimestamp(time());
    $now->setTimezone(new \DateTimeZone($site_timezone));
    return $now->format('jS M Y h:i A');
  }

}
