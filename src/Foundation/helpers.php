<?php

if (! function_exists('app')) {
  /**
   * Get the available application instance.
   *
   * @param  string|null  $abstract
   * @param  array  $parameters
   * @return mixed|\Brid\Core\Foundation\Application
   */
  function app($abstract = null, array $parameters = [])
  {
    if (is_null($abstract)) {
      return \Brid\Core\Foundation\Application::getInstance();
    }

    return \Brid\Core\Foundation\Application::getInstance()->make($abstract, $parameters);
  }
}

if (! function_exists('path')) {
  /**
   * Get the path to the base of the install.
   *
   * @param  string  $path
   * @return string
   */
  function path(string $path = ''): string
  {
    return app()->getBasePath() . \Illuminate\Support\Str::start($path, '/');
  }
}

if (! function_exists('storage_path')) {
  /**
   * Get the path to the storage of the install.
   *
   * @param  string  $path
   * @return string
   */
  function storage_path(string $path = ''): string
  {
    return app()->getStoragePath() .  \Illuminate\Support\Str::start($path, '/');
  }
}

if (! function_exists('resource_path')) {
  /**
   * Get the path to resource.
   *
   * @param  string  $path
   * @return string
   */
  function resource_path(string $path = ''): string
  {
    return path('/resources' . \Illuminate\Support\Str::start($path, '/'));
  }
}

if (! function_exists('config')) {
  /**
   * @param string $key
   * @param mixed|null $default
   * @return mixed
   */
  function config(string $key, mixed $default = null): mixed
  {
    return \Illuminate\Support\Arr::get(app()->get('config') ?? [], $key, $default);
  }
}

if (! function_exists('dispatch')) {
  /**
   * Dispatch a job to its appropriate handler.
   *
   * @param mixed $job
   * @return void
   */
  function dispatch(mixed $job): void
  {
    app(\Brid\Core\Queue\QueueManager::class)->push($job);
  }
}

if (! function_exists('dispatch_now')) {
  /**
   * Dispatch a job to sync handler.
   *
   * @param mixed $job
   * @return void
   */
  function dispatch_now(mixed $job): void
  {
    app(\Brid\Core\Queue\QueueManager::class)
      ->connection('sync')
      ->push($job);
  }
}

if (!function_exists('unaccent')) {

  function unaccent(string $value): string
  {
    return strtr($value, ['??'=>'S', '??'=>'s', '??'=>'Z', '??'=>'z', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'C', '??'=>'E', '??'=>'E',
      '??'=>'E', '??'=>'E', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'N', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'U',
      '??'=>'U', '??'=>'U', '??'=>'U', '??'=>'Y', '??'=>'B', '??'=>'Ss', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'c',
      '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'e', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'o', '??'=>'n', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o',
      '??'=>'o', '??'=>'o', '??'=>'u', '??'=>'u', '??'=>'u', '??'=>'y', '??'=>'b', '??'=>'y']);
  }

}
