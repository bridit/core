<?php

namespace Brid\Core\Foundation;

use ArrayAccess;
use Brid\Core\Foundation\Log\Logger;
use Carbon\Carbon;
use DI\Definition\ArrayDefinition;
use DI\Definition\ValueDefinition;
use Dotenv\Dotenv;

class Application extends Container implements ArrayAccess
{

  /**
   * @var string
   */
  protected string $basePath;

  /**
   * @var string
   */
  protected string $storagePath;

  /**
   * Application constructor.
   * @param string|null $basePath
   */
  protected function boot(string $basePath = null): static
  {

    $this->basePath = $basePath ?? realpath(__DIR__ . '/../../../../..');

    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
      mb_parse_str(urldecode($_SERVER['QUERY_STRING']), $_GET);
    }

    $this->bootDotEnv();
    $this->bootStorage();
    $this->bootConfig();
    $this->bootLocalization();
    $this->bootLogger();
    $this->bootProviders();

    $this->set("Brid\Core\Contracts\Foundation\Container", fn() => static::getInstance());

    \Illuminate\Support\Facades\Facade::setFacadeApplication($this);
    AliasLoader::getInstance(config('app.aliases', []))->register();

    return $this;

  }

  public function getBasePath(): string
  {
    return $this->basePath;
  }

  public function getStoragePath(): string
  {
    return $this->storagePath;
  }

  protected function bootDotEnv(): void
  {

    if (!is_readable(path('.env'))) {
      return;
    }

    $dotenv = Dotenv::createImmutable(path());
    $dotenv->safeLoad();

  }

  protected function bootStorage(): void
  {

    $this->storagePath = env('APP_STORAGE') ?? $this->basePath . '/storage';

    if ('/' === substr($this->storagePath, -1)) {
      $this->storagePath = substr($this->storagePath, 0, strlen($this->storagePath) - 1);
    }

    if (! is_dir($this->storagePath)) {
      mkdir($this->storagePath, 0755, true);
    }

  }

  protected function bootConfig(): void
  {

    if ($this->bootCachedConfig()) {
      return;
    }

    $config = new Config();

    foreach (glob(path('/config/') . '*.php') as $fileName)
    {
      $key = str_replace('.php', '', basename($fileName));

      $config[$key] = require $fileName;
    }

    $this->set('config', new ValueDefinition($config));

  }

  protected function bootCachedConfig(): bool
  {

    $fileName = path('/bootstrap/cache/config.php');

    if (!is_readable($fileName)) {
      return false;
    }

    $this->set('config', new ArrayDefinition(require $fileName));

    return true;

  }

  protected function bootLocalization(): void
  {
    $appConfig = config('app');

    date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

    Carbon::setLocale($appConfig['locale'] ?? $appConfig['fallback_locale'] ?? 'en');
  }

  protected function bootLogger(): void
  {
    $this->set('logger', new Logger());
  }

  protected function bootProviders(): void
  {

    $serviceProviders = array_map(fn($item) => is_string($item) ? new $item : $item, config('app.providers', []));

    $booted = [];

    foreach ($serviceProviders as $serviceProvider)
    {
      if (in_array($serviceProvider::class, $booted)) {
        continue;
      }

      $serviceProvider->register($this);
      $serviceProvider->boot();

      $booted[] = $serviceProvider::class;
    }

  }

  /**
   * Register an existing instance as shared in the container.
   *
   * @param string $abstract
   * @param  mixed   $instance
   * @return mixed
   */
  public function instance(string $abstract, mixed $instance): mixed
  {
    if ($this->offsetExists($abstract)) {
      $this->offsetUnset($abstract);
    }

    $this->offsetSet($abstract, $instance);

    return $instance;
  }

  /**
   * Determine if a given offset exists.
   *
   * @param string $offset
   * @return bool
   */
  public function bound(string $offset): bool
  {
    return $this->offsetExists($offset);
  }

  /**
   * Determine if a given offset exists.
   *
   * @param  string  $offset
   * @return bool
   */
  public function offsetExists($offset): bool
  {
    return $this->has($offset);
  }

  /**
   * Get the value at a given offset.
   *
   * @param  string  $offset
   * @return mixed
   */
  public function offsetGet($offset): mixed
  {
    return $this->make($offset);
  }

  /**
   * Set the value at a given offset.
   *
   * @param  string  $offset
   * @param  mixed  $value
   * @return void
   */
  public function offsetSet($offset, mixed $value): void
  {
    $this->set($offset, $value instanceof Closure ? $value : function () use ($value) {
      return $value;
    });
  }

  /**
   * Unset the value at a given offset.
   *
   * @param  string  $offset
   * @return void
   */
  public function offsetUnset($offset): void
  {
    unset($this->bindings[$offset], $this->instances[$offset], $this->resolved[$offset]);
  }

  /**
   * Dynamically access container services.
   *
   * @param string $key
   * @return mixed
   */
  public function __get(string $key)
  {
    return $this->get($key);
  }

  /**
   * Dynamically set container services.
   *
   * @param string $key
   * @param  mixed  $value
   * @return void
   */
  public function __set(string $key, mixed $value)
  {
    $this->offsetSet($key, $value);
  }

}