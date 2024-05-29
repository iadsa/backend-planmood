<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateExceptionHandler extends Command
{
    protected $signature = 'make:exception-handler';
    protected $description = 'Create the exception handler class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $path = app_path('Exceptions/Handler.php');
        $directory = dirname($path);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if ($this->files->exists($path)) {
            $this->error('Handler already exists!');
            return;
        }

        $this->createHandler($path);
        $this->info('Handler created successfully.');
    }

    protected function createHandler($path)
    {
        $stub = $this->getStub();
        $this->files->put($path, $stub);
    }

    protected function getStub()
    {
        return <<<EOD
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    protected \$dontReport = [
        //
    ];

    protected \$dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        \$this->reportable(function (Throwable \$e) {
            //
        });
    }

    protected function unauthenticated(\$request, AuthenticationException \$exception)
    {
        if (\$request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('auth.login'));
    }
}
EOD;
    }
}
