<?php

namespace Brid\Core\Queue;

use Brid\Core\Contracts\Foundation\Container;
use Brid\Core\Contracts\Queue\Job as JobContract;

class SyncJob extends Job implements JobContract
{

  /**
   * The class name of the job.
   *
   * @var string
   */
  protected string $job;

  /**
   * The queue message data.
   *
   * @var string
   */
  protected string $payload;

  /**
   * Create a new job instance.
   *
   * @param Container $container
   * @param string $payload
   * @param string|null $queue
   */
  public function __construct(Container $container, string $payload, string $queue = null)
  {
    $this->container = $container;
    $this->payload = $payload;
    $this->queue = $queue ?? 'default';
    $this->connectionName = 'sync';
  }

  /**
   * Release the job back into the queue.
   *
   * @param int $delay
   * @return void
   */
  public function release(int $delay = 0): void
  {
    parent::release($delay);
  }

  /**
   * Get the number of times the job has been attempted.
   *
   * @return int
   */
  public function attempts(): int
  {
    return 1;
  }

  /**
   * Get the job identifier.
   *
   * @return string
   */
  public function getJobId(): string
  {
    return '';
  }

  /**
   * Get the raw body string for the job.
   *
   * @return string
   */
  public function getRawBody(): string
  {
    return $this->payload;
  }

  /**
   * Get the name of the queue the job belongs to.
   *
   * @return string
   */
  public function getQueue(): string
  {
    return 'default';
  }

}
