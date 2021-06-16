<?php

namespace Brid\Core\Queue;

use Brid\Core\Contracts\Foundation\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Support\Str;
use Throwable;

abstract class Job
{

  /**
   * @var Container
   */
  protected Container $container;

  /**
   * Indicates if the job has been deleted.
   *
   * @var bool
   */
  protected bool $deleted = false;

  /**
   * Indicates if the job has been released.
   *
   * @var bool
   */
  protected bool $released = false;

  /**
   * Indicates if the job has failed.
   *
   * @var bool
   */
  protected bool $failed = false;

  /**
   * The name of the connection the job belongs to.
   *
   * @var string
   */
  protected string $connectionName;

  /**
   * The name of the queue the job belongs to.
   *
   * @var string
   */
  protected string $queue;

  /**
   * Get the raw body of the job.
   *
   * @return string
   */
  abstract public function getRawBody(): string;

  /**
   * Get the ID of the job.
   *
   * @return string|null
   */
  public function id(): ?string
  {
    return $this->payload()['id'] ?? null;
  }

  /**
   * Fire the job.
   *
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  public function fire(): void
  {
    $payload = $this->payload();

    [$class, $method] = Str::parseCallback($payload['job'], 'call');

    ($this->resolve($class))->{$method}($this, $payload['data']);
  }

  /**
   * Delete the job from the queue.
   *
   * @return void
   */
  public function delete(): void
  {
    $this->deleted = true;
  }

  /**
   * Determine if the job has been deleted.
   *
   * @return bool
   */
  public function isDeleted(): bool
  {
    return $this->deleted;
  }

  /**
   * Release the job back into the queue.
   *
   * @param int $delay
   * @return void
   */
  public function release(int $delay = 0)
  {
    $this->released = true;
  }

  /**
   * Determine if the job was released back into the queue.
   *
   * @return bool
   */
  public function isReleased(): bool
  {
    return $this->released;
  }

  /**
   * Determine if the job has been deleted or released.
   *
   * @return bool
   */
  public function isDeletedOrReleased(): bool
  {
    return $this->isDeleted() || $this->isReleased();
  }

  /**
   * Determine if the job has been marked as a failure.
   *
   * @return bool
   */
  public function hasFailed(): bool
  {
    return $this->failed;
  }

  /**
   * Mark the job as "failed".
   *
   * @return void
   */
  public function markAsFailed(): void
  {
    $this->failed = true;
  }

  /**
   * Delete the job, call the "failed" method, and raise the failed job event.
   *
   * @param Throwable|null $e
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  public function fail(Throwable $e = null): void
  {
    $this->markAsFailed();

    if ($this->isDeleted()) {
      return;
    }

    try {
      // If the job has failed, we will delete it, call the "failed" method and then call
      // an event indicating the job has failed so it can be logged if needed. This is
      // to allow every developer to better keep monitor of their failed queue jobs.
      $this->delete();

      $this->failed($e);
    } finally {
      // @todo send event
    }
  }

  /**
   * Process an exception that caused the job to fail.
   *
   * @param Throwable|null $e
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  protected function failed(Throwable $e = null): void
  {
    $payload = $this->payload();

    [$class, $method] = Str::parseCallback($payload['job'], 'fire');

    $instance = $this->resolve($class);
      
    if (method_exists($instance, 'failed')) {
      $instance->failed($this, $e);
    }
  }

  /**
   * Resolve the given class.
   *
   * @param string $class
   * @return mixed
   * @throws DependencyException
   * @throws NotFoundException
   */
  protected function resolve(string $class): mixed
  {
    return $this->container->make($class);
  }

  /**
   * Get the decoded body of the job.
   *
   * @return array
   */
  public function payload(): array
  {
    return json_decode($this->getRawBody(), true);
  }

  /**
   * Get the number of times to attempt a job.
   *
   * @return int|null
   */
  public function maxTries(): ?int
  {
    return $this->payload()['maxTries'] ?? null;
  }

  /**
   * Get the number of times to attempt a job after an exception.
   *
   * @return int|null
   */
  public function maxExceptions(): ?int
  {
    return $this->payload()['maxExceptions'] ?? null;
  }

  /**
   * The number of seconds to wait before retrying a job that encountered an uncaught exception.
   *
   * @return int|null
   */
  public function backoff(): ?int
  {
    return $this->payload()['backoff'] ?? $this->payload()['delay'] ?? null;
  }

  /**
   * Get the number of seconds the job can run.
   *
   * @return int|null
   */
  public function timeout(): ?int
  {
    return $this->payload()['timeout'] ?? null;
  }

  /**
   * Get the timestamp indicating when the job should timeout.
   *
   * @return int|null
   */
  public function retryUntil(): ?int
  {
    return $this->payload()['retryUntil'] ?? $this->payload()['timeoutAt'] ?? null;
  }

  /**
   * Get the name of the queued job class.
   *
   * @return string
   */
  public function getName(): string
  {
    return $this->payload()['job'];
  }

  /**
   * Get the name of the connection the job belongs to.
   *
   * @return string
   */
  public function getConnectionName(): string
  {
    return $this->connectionName;
  }

  /**
   * Get the name of the queue the job belongs to.
   *
   * @return string
   */
  public function getQueue(): string
  {
    return $this->queue;
  }

}