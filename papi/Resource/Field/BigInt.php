<?php
declare(strict_types=1);

namespace papi\Resource\Field;

class BigInt extends Field
{
    private ?int $length;

    public function __construct(?int $length = null, ?string $properties = null)
    {
        parent::__construct($properties);
        $this->length = $length;
    }

    protected function getDefaultProperties(): string
    {
        return $this->length ? "BIGINT($this->length)" : 'BIGINT';
    }

    public function getPHPTypeName(): string
    {
        return 'integer';
    }
}