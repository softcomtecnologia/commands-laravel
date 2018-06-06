<?php

namespace Softcomtecnologia\CommandsLaravel\GenerateModules\Stub;

use Softcomtecnologia\CommandsLaravel\GenerateModules\AbstractStub;
use Illuminate\Filesystem\Filesystem;

class MigrationsStub extends AbstractStub
{

    public function __construct(Filesystem $file, array $arguments)
    {
        parent::__construct($file, $arguments);

        $this->setFilePath('src/Database/Migrations')
            ->setStubPath(__DIR__ . '/../templates/empty.stub');
    }


    public function replacesDefaultFields() {}
}