<?php

namespace App\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;

class MakeResourceControllerTest extends ControllerMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:resource-controller-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make resource controller test';

    protected $type = 'Controller';


    protected function getStub()
    {
        return __DIR__."/stubs/controller.stub";
    }

    protected function getNameInput()
    {
        return trim($this->argument('name') . "Test");
    }

    protected function rootNamespace()
    {
        return 'Tests';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Feature\ResourceController';
    }

    protected function buildAbstractClass()
    {
        $stub = __DIR__."/stubs/abstract.stub";
        $stub = $this->files->get($stub);
        return $stub;
    }


    public function handle() {
        $path = base_path('tests')."/Feature/ResourceController/ResourceControllerAbstract.php";
        if(! $this->files->exists($path)) {
            $this->files->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $this->buildAbstractClass());
        }
        return parent::handle();
    }


    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path('tests').str_replace('\\', '/', $name).'.php';
    }



    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }


}
