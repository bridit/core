<?php

namespace Brid\Core\Foundation;

use Brid\Core\Foundation\Log\Logger;
use Psr\Log\LoggerInterface;

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

  /**
   * @var Logger|null
   */
  protected static ?Logger $logger = null;

  /**
   * @return Logger
   * @throws \DI\DependencyException
   * @throws \DI\NotFoundException
   */
  protected static function getLogger(): Logger
  {
    if (null !== static::$logger) {
      return static::$logger;
    }

    return static::$logger = app()->get('logger');
  }

  /**
   * @param string $channel
   * @return LoggerInterface
   */
  public static function on(string $channel): LoggerInterface
  {
    return static::getLogger()->on($channel);
  }

  /**
   * @param string $name
   * @param array $arguments
   * @return mixed
   */
  public static function __callStatic(string $name, array $arguments)
  {
    return static::getLogger()->log($name, $arguments[0], $arguments[1] ?? []);
  }

}