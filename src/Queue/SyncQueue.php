<?php

namespace Brid\Core\Queue;

use Brid\Core\Contracts\Foundation\Container;
use Brid\Core\Contracts\Queue\Queue as QueueContract;
use DateInterval;
use DateTimeInterface;
use Throwable;

class SyncQueue extends Queue implements QueueContract
{

  public function __construct(protected Container $container) {}

  /**
   * Push a new job onto the queue.
   *
   * @param string $job
   * @param string|null $queue
   * @return void
   *
   * @throws Throwable
   */
  public function push(mixed $job, string $queue = null): void
  {
    $queueJob = $this->resolveJob($this->createPayload($job), $queue);

    try {
      $queueJob->fire();
    } catch (Throwable $e) {
      $this->handleException($queueJob, $e);
    }
  }

  /**
   * Resolve a Sync job instance.
   *
   * @param string $payload
   * @param string|null $queue
   * @return SyncJob
   */
  protected function resolveJob(string $payload, string $queue = null)
  {
    return new SyncJob($this->container, $payload, $queue);
  }

  /**
   * Handle an exception that occurred while processing a job.
   *
   * @param  Job  $queueJob
   * @param  Throwable  $e
   * @return void
   *
   * @throws Throwable
   */
  protected function handleException(Job $queueJob, Throwable $e)
  {
    $queueJob->fail($e);

    throw $e;
  }

  /**
   * Push a raw payload onto the queue.
   *
   * @param  string  $payload
   * @param  string|null  $queue
   * @param  array  $options
   * @return mixed
   */
  public function pushRaw($payload, $queue = null, array $options = []): mixed
  {
    //
  }

  /**
   * Push a new job onto the queue after a delay.
   *
   * @param DateTimeInterface|DateInterval|int  $delay
   * @param string $job
   * @param string|null $queue
   * @return void
   *
   * @throws Throwable
   */
  public function later(mixed $delay, string $job, string $queue = null): void
  {
    $this->push($job, $queue);
  }

  /**
   * Pop the next job off of the queue.
   *
   * @param string|null $queue
   * @return Job|null
   */
  public function pop(string $queue = null): ?Job
  {
    return null;
  }

}