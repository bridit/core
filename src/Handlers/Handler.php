<?php declare(strict_types=1);

namespace Brid\Core\Handlers;

use Brid\Core\Foundation\Application;

class Handler extends Application
{

  protected mixed $context;
  
  public function handle($event = null, $context = null)
  {
    $this->context = $context;
  }

}
