# IC JSON

The package provides methods to encode and decode JSON.

- It has sensible defaults, so you don't have to specify flags all the time.
- It has handy method to encode for HTML safely.
- It throws `\IC\Json\Exception\JsonEncodeException` when encoding fails.
- It throws `\IC\Json\Exception\JsonDecodeException` when decoding fails.
- It handles `\JsonSerializable`, `\DateTimeInterface`, and `\SimpleXMLElement` well.

## Requirements

- PHP 8.0 or higher.
- `SimpleXML` PHP extension.

## Installation

The package could be installed with composer:

```shell
composer require inerciacreativa/json --prefer-dist
```

## General usage

Encoding:

```php
use \IC\Json\Json;

$data = ['name' => 'Jose', 'team' => 'Inercia Creativa'];
$json = Json::encode($data);
```

Encoding for HTML:

```php
use \IC\Json\Json;

$data = ['name' => 'Jose', 'team' => 'Inercia Creativa'];
$json = Json::htmlEncode($data);
```

Decoding:

```php
use \IC\Json\Json;

$json = '{"name":"Jose","team":"Inercia Creativa"}';
$data = Json::decode($json);
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
composer phpunit
```

### Static analysis

The code is statically analyzed with [PHPStan](https://phpstan.org/). To run static analysis:

```shell
composer phpstan
```

## License

The IC JSON is free software. It is released under the terms of the MIT License.
Please see [`LICENSE`](./LICENSE) for more information.

Maintained by [Inercia Creativa](https://www.inerciacreativa.com/).
