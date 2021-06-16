<?php

namespace Brid\Core\Queue;

use Brid\Core\Contracts\Foundation\Container;
use Brid\Core\Contracts\Queue\Job;
use Carbon\Carbon;
use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class QueuedHandler
{

  public function __construct(protected Container $container) {}

  /**
   * Handle the queued job.
   *
   * @param Job $job
   * @param array $data
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  public function call(Job $job, array $data)
  {
    $command = $this->getCommand($data);

    try {
      $command->handle();
    } catch (Throwable $e) {
      $job->fail($e);
    }


    if (! $job->isDeletedOrReleased()) {
      $job->delete();
    }
  }

  /**
   * Get the command from the given payload.
   *
   * @param array $data
   * @return mixed
   *
   * @throws DependencyException
   * @throws NotFoundException
   * @throws RuntimeException
   */
  protected function getCommand(array $data): mixed
  {
    if (Str::startsWith($data['command'], 'O:')) {
      return unserialize($data['command']);
    }

    if (isset($data['commandName'])) {
      return $this->container->make($data['commandName'], $data['commandArgs'] ?? []);
    }
    
    throw new RuntimeException('Unable to extract job payload.');
  }

  /**
   * Call the failed method on the job instance.
   *
   * The exception that caused the failure will be passed.
   *
   * @param Job $job
   * @param Throwable $e
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  public function failed(Job $job, Throwable $e): void
  {
    $command = $this->getCommand($job->payload()['data']);

    $this->persistFailedJob($job, $e);

    if (method_exists($command, 'failed')) {
      $command->failed($e);
    }
  }

  /**
   * Persist failed job on table.
   *
   * @param Job $job
   * @param Throwable $e
   * @return void
   *
   * @throws DependencyException
   * @throws NotFoundException
   */
  protected function persistFailedJob(Job $job, Throwable $e)
  {
    $connection = config('queue.failed.connection', null);
    
    if (blank($connection)) {
      return;
    }
    
    $tableName  = config('queue.failed.table', 'failed_jobs');

    $this->container
      ->get('db')
      ->connection($connection)
      ->table($tableName)
      ->insert([
        'id' => $job->id(),
        'created_at' => Carbon::now(),
        'connection' => $job->getConnectionName(),
        'queue' => $job->getQueue(),
        'job' => $job->getName(),
        'command' => $job->payload()['data']['commandName'],
        'payload' => json_encode($job->payload()),
        'exception' => (string) $e,
      ]);
  }

}
