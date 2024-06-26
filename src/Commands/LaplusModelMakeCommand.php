<?php

namespace Rapid\Laplus\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class LaplusModelMakeCommand extends ModelMakeCommand
{

    protected $name = 'make:model-laplus';

    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        $this->createPresent();
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createPresent()
    {
        if (!$this->option('inline'))
        {
            $controller = Str::studly(class_basename($this->argument('name')));

            $modelName = $this->qualifyClass($this->getNameInput());

            $this->call('make:present', array_filter([
                'name' => "{$controller}Present",
                // '--model' => $this->option('resource') || $this->option('api') ? $modelName : null,
                // '--api' => $this->option('api'),
                // '--requests' => $this->option('requests') || $this->option('all'),
                // '--test' => $this->option('test'),
                // '--pest' => $this->option('pest'),
            ]));
        }
    }

    protected function getStub()
    {
        if ($this->option('inline'))
        {
            return __DIR__ . '/stubs/model-inline.stub';
        }
        else
        {
            return __DIR__ . '/stubs/model.stub';
        }
    }

    protected function getOptions()
    {
        return [
            ...parent::getOptions(),
            ['inline', 'i', InputOption::VALUE_NONE, 'Make present inline'],
        ];
    }

}