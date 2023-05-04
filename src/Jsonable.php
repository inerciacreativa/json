<?php declare(strict_types=1);

namespace IC\Json;

/**
 * Objects implementing Jsonable can be represented as JSON data.
 */
interface Jsonable
{

    /**
     * Returns a JSON representation of the object.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string;

}
