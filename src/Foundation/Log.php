<?php

namespace Brid\Core\Foundation;

/**
 * Class Log
 * @package Brid\Core
 * @method static emergency($message, array $context = array())
 * @method static alert($message, array $context = array())
 * @method static critical($message, array $context = array())
 * @method static error($message, array $context = array())
 * @method static warning($message, array $context = array())
 * @method static notice($message, array $context = array())
 * @method static info($message, array $context = array())
 * @method static debug($message, array $context = array())
 */
class Log
{

  public static function __callStatic(string $name, array $arguments)
  {
    return app()
      ->get('logger')
      ->log($name, $arguments[0], $arguments[1] ?? []);
  }

}