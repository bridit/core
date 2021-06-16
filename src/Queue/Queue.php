<?php

namespace Brid\Core\Queue;

use Closure;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class Queue
{

  /**
   * Create a payload string from the given job and data.
   *
   * @param  Closure|string|object  $job
   * @return string
   *
   * @throws RuntimeException
   */
  protected function createPayload(mixed $job)
  {
    $payload = json_encode($this->getPayloadArray($job));

    if (JSON_ERROR_NONE !== json_last_error()) {
      throw new RuntimeException('Unable to JSON encode payload. Error code: ' . json_last_error());
    }

    return $payload;
  }
  
  /**
   * @param mixed $job
   * @return array
   */
  protected function getPayloadArray(mixed $job): array
  {
    
    return [
      'id' => Uuid::uuid4()->toString(),
      'displayName' => $this->getDisplayName($job),
      'job' => QueuedHandler::class . '@call',
      'maxTries' => null,
      'maxExceptions' => null,
      'failOnTimeout' => false,
      'timeout' => null,
      'retryUntil' => null,
      'data' => [
        'commandName' => get_class($job),
        'command' => serialize($job)
      ]
    ];

  }

  /**
   * Get the display name for the given job.
   *
   * @param object $job
   * @return string
   */
  protected function getDisplayName(object $job): string
  {
    return method_exists($job, 'displayName')
      ? $job->displayName()
      : get_class($job);
  }

}