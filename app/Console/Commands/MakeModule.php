<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Str;

class MakeModule extends Command
{
    protected  $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}
                                        {--all : All items}
                                        {--migration : Only migration}
                                        {--view : Only View}
                                        {--model : Only Model}
                                        {--controller : Only Controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->option('all')) {
            $this->input->setOption("migration", true);
            $this->input->setOption("view", true);
            $this->input->setOption("model", true);
            $this->input->setOption("controller", true);
        }

        if($this->option('model')) {
            $this->createModel();
        }
        if($this->option('migration')) {
            $this->createMigration();
        }

        if($this->option('view')) {
            $this->createView();
        }

        if($this->option('controller')) {
            $this->createController();
        }


    }

    private function createModel()
    {
        try {
            $model = Str::singular(class_basename($this->argument('name')));
            $this->call('make:model', [
                'name' => "App\\Modules\\".str_replace('/', '\\', trim($this->argument('name')))."\\Models\\".$model
            ]);
        }catch(\Exception $e) {
            $e->getMessage();
        }

    }

    private function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));
        $name = Carbon::now()->format('Y_m_d_his')."_create_".$table."_table";
        $className = 'Create'.ucfirst($table).'Table';
        $migrationPath = $this->getMigrationPath($this->argument('name'), $name);

        if ($this->alreadyExists($migrationPath)) {
            $this->error('Migration already exists!');
        } else {

            $this->makeDirectory($migrationPath);

            $stub = $this->files->get(base_path('resources/stubs/migration.web.stub'));

            $stub = str_replace(
                [
                    'DummyClassName',
                    'DummyTableName',
                ],
                [
                    $className,
                    $table,
                ],
                $stub
            );

            $this->files->put($migrationPath, $stub);
            $this->info('Migration created successfully.');
        }
    }

    private function getMigrationPath($argument, $name)
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $argument)."/migration/".$name.".php";

    }

    protected function createView()
    {
        $paths = $this->getViewPath($this->argument('name'));

        foreach ($paths as $path) {
            $view = Str::studly(class_basename($this->argument('name')));

            if ($this->alreadyExists($path)) {
                $this->error('View already exists!');
            } else {
                $this->makeDirectory($path);

                $stub = $this->files->get(base_path('resources/stubs/view.stub'));

                $stub = str_replace(
                    [
                        '',
                    ],
                    [
                    ],
                    $stub
                );

                $this->files->put($path, $stub);
            }
        }
        $this->createLang();
        $this->info('View created successfully.');
    }

    protected function getViewPath($name)
    {

        $arrFiles = collect([
            'create',
            'edit',
            'index',
            'show',
        ]);

        /** @var array $paths */
        $paths = $arrFiles->map(function($item) use ($name){
            return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name).'/Views/'.$item.".blade.php";
        });

        return $paths;
    }

    private function createLang()
    {
        $name = Str::singular(Str::snake(class_basename($this->argument('name'))));
        $langPath = $this->getLangPath($this->argument('name'), $name);

        $this->makeDirectory($langPath);
        $this->files->put($langPath, '<?');

        $this->info('Lang directory created successfully.');
    }

    private function getLangPath($argument, $name)
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $argument)."/lang/ru/$name.php";

    }

    private function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));
        $modelName = Str::singular(Str::studly(class_basename($this->argument('name'))));

        $controllerPath = $this->getControllerPath($this->argument('name'));

        $this->makeDirectory($controllerPath);

        $stub = $this->files->get(base_path('resources/stubs/controller.model.stub'));

        $namespaceController = "App\\Modules\\".str_replace('/', '\\', trim($this->argument('name')))."\\Controllers";
        $fullModelClass = "App\\Modules\\".str_replace('/', '\\', trim($this->argument('name')))."\\Models\\{$modelName}";

        $stub = str_replace(
            [
                'DummyNamespace',
                'DummyRootNamespace',
                'DummyClass',
                'DummyFullModelClass',
                'DummyModelClass',
                'DummyModelVariable',
            ],
            [
                $namespaceController,
                $this->laravel->getNamespace(),
                $controller.'Controller',
                $fullModelClass,
                $modelName,
                lcfirst(($modelName))
            ],
            $stub
        );

        $this->files->put($controllerPath, $stub);
        $this->info("Controller created");

        $this->createRoutes($controller, $modelName, $namespaceController);

    }

    private function getControllerPath($argument)
    {
        $controller = Str::studly(class_basename($argument));
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $argument)."/Controllers/"."{$controller}Controller.php";

    }

    private function makeDirectory($path)
    {
        $this->files->makeDirectory(dirname($path),0755, true, true);
    }

    private function createRoutes($controller, $modelName, $namespace)
    {
        $routePath = $this->getRoutesPath($this->argument('name'));

        Str::singular(strstr($this->argument('name'), '/'.$modelName, true));
        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {

            $this->makeDirectory($routePath);

            $stub = $this->files->get(base_path('resources/stubs/routes.web.stub'));
            $prefix = Str::singular(strstr(  $this->argument('name'),"/$modelName",true));

            $stub = str_replace(
                [
                    'DummyNamespace',
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                    'DummyAs',
                ],
                [
                    $namespace,
                    $controller.'Controller',
                    Str::snake(lcfirst($prefix), '-'),
                    Str::plural(lcfirst($modelName)),
                    $prefix !== '' ? lcfirst($prefix.'.') :'',
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }
    }

    private function getRoutesPath($argument)
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $argument)."/routes/web.php";

    }

    private function alreadyExists($routePath)
    {
        return $this->files->exists($routePath);
    }


}

