<?php
declare(strict_types=1);

namespace papi\Resource\Field;

class LongBlob extends Field
{
    public function getDefaultProperties(): string
    {
        return 'longblob';
    }

    public function getPHPTypeName(): string
    {
        return 'string';
    }
}