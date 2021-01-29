<?php
declare(strict_types=1);

namespace migrations;

use config\Resources;
use framework\CLI\ConsoleOutput;
use framework\Migrations\Migration;

class CreateResources extends Migration
{
    public function migrate(): void
    {
        foreach (Resources::getItems() as $resource) {
            $resourceObject = new $resource();
            try {
                $this->handler->create(
                    $resourceObject->getTableName(),
                    $resourceObject->getMigrationColumns(),
                );
            } catch (\Exception $exception) {
                ConsoleOutput::errorDie($exception->getMessage());
            }
        }
        ConsoleOutput::output('Resources created');

        foreach (Resources::getItems() as $resource) {
            $resourceObject = new $resource();
            foreach ($resourceObject->getRelations() as $relation) {
                try {
                    $relation->createRelation();
                } catch (\Exception $exception) {
                    ConsoleOutput::errorDie($exception->getMessage());
                }
            }
        }
        ConsoleOutput::output('Relations created');
    }
}