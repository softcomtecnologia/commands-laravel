<?php
namespace Softcomtecnologia\CommandsLaravel\GenerateModules;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;

class MakeModuleCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = "make:generate-module {module : Module name}";
    /**
     * @var string
     */
    protected $description = "Create new reusable module in Laravel.";
    /**
     * @var array
     */
    protected $class = [];
    /**
     * @var string
     */
    protected $myPackage = "";
    /**
     * @var string
     */
    protected $pathStub = "";
    /**
     * @return string
     */
    protected function getMyPackage()
    {
        return $this->myPackage;
    }
    /**
     * @param $package
     * @return $this
     */
    protected function setMyPackage($package)
    {
        $this->myPackage = $package;

        return $this;
    }
    /**
     * @param $stubPath
     * @return $this
     */
    protected function setMyStubPath($stubPath)
    {
        $this->pathStub = $stubPath;

        return $this;
    }
    /**
     * @param $className
     * @return $this
     */
    public function addClass($className)
    {
        if (is_array($className)) {
            $array  = array_flip($this->class);

            foreach ($className as $key => $class) {
                $args = null;

                if (is_array($class)) {
                    $args  = $class;
                    $class = $key;
                }

                if (!array_key_exists($class, $array)) {
                    $this->class[] = [
                        "class" => $class,
                        "args" => $args
                    ];
                }
            }

            return $this;
        }

        $this->class[] = $className;

        return $this;
    }
    /**
     * @return string
     */
    public function getBaseDir()
    {
        return App::getFacadeApplication()->basePath();
    }
    /**
     * @throws \Exception
     */
    public function fire()
    {
        $this->buildClass();
    }
    /**
     * @throws \Exception
     */
    protected function buildClass()
    {
        if (!$this->argument('module')) {
            throw new \Exception("Not found argument module.");
        }

        $files  = new Filesystem();

        if ($this->_verifyIsDirectory($files) != 'y') {
            $this->info("Operation canceled by user");
            return;
        }

        foreach ($this->class as $class) {
            if (!is_subclass_of($class['class'], AbstractStub::class)) {
                throw new \Exception("The class {$class['class']} is not subclass of `" . AbstractStub::class . "`");
            }

            $obj = new $class['class']($files, $this->argument());

            if (is_array($class['args'])) {
                foreach ($class['args'] as $args){
                    if (!is_array($args)) {
                        $args = $class['args'];
                    }
                    $this->_objectBuild($this->_setArgsObj($obj, $args));
                }
            } else{
                $this->_objectBuild($obj);
            }
        }
    }
    /**
     * @param $obj
     */
    protected function _objectBuild($obj)
    {
        $this->_confgurationClass($obj);

        if ($obj->build()) {
            $this->info("File `{$obj->fullPathFileName()}` successfully created.");
        } else {
            $this->warn("it was not possible to create the file `{$obj->fileName()}`");
        }
    }
    /**
     * @param $obj
     * @param array $args
     * @return mixed
     */
    protected function _setArgsObj($obj, array $args)
    {
        if (isset($args['fileName'])) {
            $obj->setFileName($args['fileName']);
        }

        if (isset($args['filePath'])) {
            $obj->setFilePath($args['filePath']);
        }

        if (isset($args['fileExtension'])) {
            $obj->setFileExtension($args['fileExtension']);
        }

        if (isset($args['stubPath']) && $args['stubPath'] != '') {
            $obj->setStubPath($args['stubPath']);
        }

        return $obj;
    }
    /**
     * @param $obj
     */
    protected function _confgurationClass($obj)
    {
        $obj->setPackage($this->getMyPackage());

        if ($this->pathStub != '') {
            $path = substr(strstr($obj->getStubPath(), 'templates'), 9);
            $obj->setStubPath($this->pathStub . $path);
        }


    }
    /**
     * @param $files
     * @return string
     */
    protected function _verifyIsDirectory($files)
    {
        $path = dirname(App::getFacadeApplication()->basePath()) . '/' . $this->argument('module');
        $exec = 'y';

        if ($files->isDirectory($path)) {
            $exec = $this->ask("Directory `{$this->argument('module')}` already exists, to continue? " .
                "This action will overwrite existing files! [Y,N]");
        }

        return strtolower($exec);
    }
}
