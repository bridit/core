<?php

namespace Brid\Core\Queue;

use Brid\Core\Contracts\Foundation\Container;
use Brid\Core\Contracts\Queue\Queue;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class QueueManager
{

  /**
   * The Container instance.
   *
   * @var Container
   */
  protected Container $container;

  /**
   * @var array
   */
  protected array $config;

  /**
   * The array of resolved queue connections.
   *
   * @var array
   */
  protected array $connections = [];

  /**
   * The array of resolved queue connectors.
   *
   * @var array
   */
  protected array $connectors = [];

  public function __construct(Container $container)
  {

    $this->container = $container;

    $this->config = config('queue');

    $this->connectors['sync'] = fn() => $this->container->make(SyncQueue::class);
    $this->connectors['sqs'] = fn() => $this->container->make('Brid\\Sqs\\SqsQueue');

  }

  /**
   * Resolve a queue connection instance.
   *
   * @param string|null $name
   * @return Queue
   */
  public function connection(string $name = null): Queue
  {
    $name = $name ?: $this->getDefaultDriver();

    // If the connection has not been resolved yet we will resolve it now as all
    // of the connections are resolved when they are actually needed so we do
    // not make any unnecessary connection to the various queue end-points.
    if (! isset($this->connections[$name])) {
      $this->connections[$name] = $this->resolve($name);
    }

    return $this->connections[$name];
  }

  /**
   * Resolve a queue connection.
   *
   * @param string $name
   * @return Queue
   */
  protected function resolve(string $name): Queue
  {
    $config = $this->getConfig($name);

    return $this->getConnector($config['driver'])
//      ->setContainer($this->app)
//      ->connect($config)
//      ->setConnectionName($name)
      ;
  }

  /**
   * Get the connector for a given driver.
   *
   * @param string $driver
   * @return Queue
   *
   * @throws InvalidArgumentException
   */
  protected function getConnector(string $driver): Queue
  {
    if (! isset($this->connectors[$driver])) {
      throw new InvalidArgumentException("No connector for [$driver].");
    }

    return call_user_func($this->connectors[$driver]);
  }

  /**
   * Get the queue connection configuration.
   *
   * @param string $name
   * @return array
   */
  protected function getConfig(string $name): array
  {
    if ($name !== 'null') {
      return Arr::get($this->config, "connections.{$name}");
    }

    return ['driver' => 'null'];
  }

  /**
   * Get the name of the default queue connection.
   *
   * @return string
   */
  public function getDefaultDriver(): string
  {
    return $this->config['default'];
  }

  /**
   * Set the name of the default queue connection.
   *
   * @param string $name
   * @return void
   */
  public function setDefaultDriver(string $name): void
  {
    $this->config['default'] = $name;
  }
  
  /**
   * Dynamically pass calls to the default connection.
   *
   * @param string $method
   * @param array $parameters
   * @return mixed
   */
  public function __call(string $method, array $parameters)
  {
    return $this->connection()->$method(...$parameters);
  }

}