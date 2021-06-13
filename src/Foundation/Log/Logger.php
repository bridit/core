<?php

namespace Brid\Core\Foundation\Log;

use DI\DependencyException;
use DI\NotFoundException;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Logger
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
class Logger extends AbstractLogger
{

  /**
   * @var LoggerInterface[]
   */
  protected array $loggers;

  /**
   * @var bool
   */
  protected bool $booted = false;

  public function __construct()
  {
    $this->bootLoggers();
  }

  protected function bootLoggers(): void
  {
    
    $logging = config('logging');
    $default = $logging['default'] ?? 'stack';

    $channels = 'stack' === $default
      ? $logging['channels']['stack']['channels']
      : [$default];

    foreach ($channels as $channel)
    {
      $logger = $this->getLoggerInstance($channel, $logging['channels'][$channel] ?? []);

      if (!$logger instanceof LoggerInterface) {
        continue;
      }

      $this->loggers[] = $logger;
    }

  }

  /**
   * @param string $channel
   * @param array $config
   * @param string $notifyLevel
   * @return LoggerInterface|null
   * @todo Implement StderrLogger
   */
  protected function getLoggerInstance(string $channel, array $config = [], string $notifyLevel = LogLevel::WARNING): ?LoggerInterface
  {

    if ($config['driver'] === 'custom') {
      $logger = new $config['via'];
      return $logger(array_merge(['level' => LogLevel::WARNING], $config));
    }

    try {
      return app()->get('logger.' . $config['driver']);
    } catch (DependencyException|NotFoundException $e) {
      return null;
    }

  }

  public function log($level, $message, array $context = [])
  {

    if (!$this->booted) {
      $this->bootLoggers();
    }

    foreach ($this->loggers as $logger)
    {
      $logger->{$level}($message, $context);
    }
    
  }

}
