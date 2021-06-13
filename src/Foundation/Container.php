<?php

namespace Brid\Core\Foundation;

use Brid\Core\Contracts\Foundation\Container as ContainerContract;

class Container extends \DI\Container implements ContainerContract
{

  /**
   * The current globally available container (if any).
   *
   * @var ?Container
   */
  protected static ?Container $instance = null;

  /**
   * Get the globally available instance of the container.
   *
   * @param mixed ...$args
   * @return static
   */
  public static function getInstance(...$args): static
  {
    if (null !== static::$instance) {
      return static::$instance;
    }

    static::$instance = new static;

    if (method_exists(static::$instance, 'boot')) {
      static::$instance->boot(...$args);
    }

    return static::$instance;
  }

  /**
   * Set the shared instance of the container.
   *
   * @param  Container|null  $container
   * @return Container|null
   */
  public static function setInstance(Container $container = null): ?Container
  {
    return static::$instance = $container;
  }

}