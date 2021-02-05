<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use config\Migrations;
use papi\CLI\ConsoleOutput;
use papi\console\db\CreateResources;

(new CreateResources())->execute();
foreach (Migrations::getItems() as $migration) {
    (new $migration)->execute();
}
ConsoleOutput::success('All migrations executed!');