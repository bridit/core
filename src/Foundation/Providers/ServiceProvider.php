<?php

namespace Brid\Core\Foundation\Providers;

use DI\Container;

class ServiceProvider
{

  protected Container $container;
  
  public function register(Container $container)
  {
    $this->container = $container;
  }
  
}