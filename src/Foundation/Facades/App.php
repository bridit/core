<?php

namespace Brid\Core\Foundation\Facades;

use Brid\Core\Foundation\Application;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed make($abstract, array $parameters = [])
 */
class App extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return Application
   */
  protected static function getFacadeAccessor(): Application
  {
    return \Brid\Core\Foundation\Application::getInstance();
  }
}
