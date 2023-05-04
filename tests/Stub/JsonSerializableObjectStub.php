<?php

namespace IC\Json\Tests\Stub;

use JsonSerializable;

class JsonSerializableObjectStub implements JsonSerializable
{

    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

}
