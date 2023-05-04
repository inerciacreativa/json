<?php declare(strict_types=1);

namespace IC\Json;

use DateTimeInterface;
use Exception;
use IC\Json\Exception\JsonDecodeException;
use IC\Json\Exception\JsonEncodeException;
use JsonSerializable;
use SimpleXMLElement;
use stdClass;

/**
 * Json is a helper class providing enhanced data encoding and decoding.
 */
final class Json
{

    /**
     * Returns the JSON representation of a value.
     *
     * @param mixed $value
     * @param int   $options
     *
     * @return string The encoding result.
     *
     * @throws JsonEncodeException When encode fails.
     */
    public static function encode(mixed $value, int $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE): string
    {
        $value = self::processValue($value);

        try {
            return json_encode($value, JSON_THROW_ON_ERROR | $options);
        } catch (Exception $exception) {
            throw new JsonEncodeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param mixed $value
     *
     * @return string The encoding result.
     *
     * @throws JsonEncodeException When encode fails.
     */
    public static function htmlEncode(mixed $value): string
    {
        return self::encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
    }

    /**
     * Decodes a JSON string into an array.
     *
     * @param string $value
     * @param bool   $asArray
     * @param int    $options
     * @param int    $depth
     *
     * @return array
     *
     * @throws JsonDecodeException When decode fails.
     */
    public static function decode(string $value, bool $asArray = true, int $options = 0, int $depth = 512): array
    {
        try {
            return json_decode($value, $asArray, $depth, JSON_THROW_ON_ERROR | $options);
        } catch (Exception $exception) {
            throw new JsonDecodeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Processes the value before encode.
     *
     * @param $value
     *
     * @return mixed
     */
    private static function processValue($value): mixed
    {
        if (is_object($value)) {
            if ($value instanceof JsonSerializable) {
                return self::processValue($value->jsonSerialize());
            }

            if (($value instanceof DateTimeInterface) || ($value instanceof SimpleXMLElement)) {
                $value = (array) $value;
            } else {
                $result = [];

                foreach ($value as $k => $v) {
                    $result[$k] = $v;
                }

                $value = $result;
            }

            if ($value === []) {
                return new stdClass();
            }
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $value[$k] = self::processValue($v);
                }
            }
        }

        return $value;
    }

}
