<?php

namespace Softcomtecnologia\CommandsLaravel\GenerateModules\Stub;

use Softcomtecnologia\CommandsLaravel\GenerateModules\AbstractStub;
use Illuminate\Filesystem\Filesystem;

class SeedersStub extends AbstractStub
{

    public function __construct(Filesystem $file, array $arguments)
    {
        parent::__construct($file, $arguments);

        $this->setFilePath('src/Database/Seeders')
             ->setStubPath(__DIR__ . '/../templates/controller.stub');
    }


    public function replacesDefaultFields() {}
}