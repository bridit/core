<?php

namespace Brid\Core\Foundation;

use Brid\Core\Foundation\Log\Logger;
use Carbon\Carbon;
use DI\Definition\ArrayDefinition;
use Dotenv\Dotenv;

class Application extends Container
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

    $config = [];

    foreach (glob(path('/config/') . '*.php') as $fileName)
    {
      $key = str_replace('.php', '', basename($fileName));

      $config[$key] = require $fileName;
    }

    $this->set('config', new ArrayDefinition($config));

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

}