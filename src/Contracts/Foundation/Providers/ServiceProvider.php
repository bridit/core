<?php

namespace Brid\Core\Contracts\Foundation\Providers;

use DI\Container;

interface ServiceProvider
{

  public static function load(Container $container);

}