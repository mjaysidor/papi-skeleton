<?php

declare(strict_types=1);

namespace papi\Resource\Field;

/**
 * Postgresql "boolean"" column type
 */
class Boolean extends Field
{
    protected function getDefaultProperties(): string
    {
        return "boolean";
    }

    public function getPHPTypeName(): string
    {
        return 'boolean';
    }
}
