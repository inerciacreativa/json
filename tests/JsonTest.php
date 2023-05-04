<?php declare(strict_types=1);

namespace IC\Json\Tests;

use DateTime;
use DateTimeZone;
use IC\Json\Exception\JsonDecodeException;
use IC\Json\Exception\JsonEncodeException;
use IC\Json\Json;
use IC\Json\Tests\Stub\JsonSerializableObjectStub;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use SplStack;
use stdClass;

final class JsonTest extends TestCase
{

    public function testEncodeBasic(): void
    {
        $this->assertSame('"1"', Json::encode('1'));
    }

    public function testEncodeWithDefaults(): void
    {
        $this->assertSame('"/🎁"', Json::encode('/🎁'));
    }

    public function testEncodeEscapeEverything(): void
    {
        $this->assertSame('"\/\ud83c\udf81"', Json::encode('/🎁', 0));
    }

    public function testEncodeSimpleArray(): void
    {
        $this->assertSame('[1,2]', Json::encode([1, 2]));
        $this->assertSame('{"a":1,"b":2}', Json::encode(['a' => 1, 'b' => 2]));
    }

    public function testEncodeSimpleObject(): void
    {
        $data    = new stdClass();
        $data->a = 1;
        $data->b = 2;
        $this->assertSame('{"a":1,"b":2}', Json::encode($data));
    }

    public function testEncodeEmpty(): void
    {
        $this->assertSame('[]', Json::encode([]));
        $this->assertSame('{}', Json::encode(new stdClass()));
    }

    public function testEncodeNullObject(): void
    {
        $this->assertSame('{}', Json::encode((object) null));
    }

    public function testEncodeJsonSerializable(): void
    {
        $data = new JsonSerializableObjectStub(['id' => 42, 'title' => 'json serializable']);
        $this->assertSame('{"id":42,"title":"json serializable"}', Json::encode($data));
    }

    public function testEncodeWithSerializableReturningEmptyData(): void
    {
        $this->assertSame('[]', Json::encode(new JsonSerializableObjectStub([])));
        $this->assertSame('{}', Json::encode(new JsonSerializableObjectStub((object) null)));
    }

    public function testEncodeXmlDocument(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <file>
          <apiKey>ieu2iqw4ok</apiKey>
          <methodProperties>
            <FindByString>Jose</FindByString>
          </methodProperties>
        </file>';

        $document = simplexml_load_string($xml);
        $this->assertSame('{"apiKey":"ieu2iqw4ok","methodProperties":{"FindByString":"Jose"}}', Json::encode($document));
    }

    public function testEncodeSimpleXmlElement(): void
    {
        $data = new SimpleXMLElement('<value>42</value>');
        $this->assertSame('["42"]', Json::encode($data));
    }

    public function testEncodeSimpleXmlElementWithinArray(): void
    {
        $data = [new SimpleXMLElement('<value>42</value>')];
        $this->assertSame('[["42"]]', Json::encode($data));
    }

    public function testEncodeSplStack(): void
    {
        $postsStack = new SplStack();
        $postsStack->push(new JsonSerializableObjectStub(['id' => 815, 'title' => 'first title']));
        $postsStack->push(new JsonSerializableObjectStub(['id' => 274, 'title' => 'another title']));

        $this->assertSame('{"1":{"id":274,"title":"another title"},"0":{"id":815,"title":"first title"}}', Json::encode($postsStack));
    }

    public function testEncodeDateTime(): void
    {
        $input = new DateTime('October 31, 2022', new DateTimeZone('UTC'));
        $this->assertEquals('{"date":"2022-10-31 00:00:00.000000","timezone_type":3,"timezone":"UTC"}', Json::encode($input));
    }

    public function testHtmlEncodeBasic(): void
    {
        $this->assertSame('"1"', Json::htmlEncode('1'));
    }

    public function testHtmlEncodeEscapesCharacters(): void
    {
        $this->assertSame('"\u0026\u003C\u003E\u0022\u0027\/"', Json::htmlEncode('&<>"\'/'));
    }

    public function testHtmlEncodeSimpleArray(): void
    {
        $this->assertSame('[1,2]', Json::htmlEncode([1, 2]));
        $this->assertSame('{"a":1,"b":2}', Json::htmlEncode(['a' => 1, 'b' => 2]));
    }

    public function testHtmlEncodeSimpleObject(): void
    {
        $data    = new stdClass();
        $data->a = 1;
        $data->b = 2;
        $this->assertSame('{"a":1,"b":2}', Json::htmlEncode($data));
    }

    public function testHtmlEncodeNullObject(): void
    {
        $this->assertSame('{}', Json::htmlEncode((object) null));
    }

    public function testHtmlEncodeJsonSerializable(): void
    {
        $data = new JsonSerializableObjectStub(['id' => 42, 'title' => 'json serializable']);
        $this->assertSame('{"id":42,"title":"json serializable"}', Json::htmlEncode($data));
    }

    public function testDecodeArray(): void
    {
        $this->assertSame(['a' => 1, 'b' => 2], Json::decode('{"a":1,"b":2}'));
    }

    public function testDecodeEmptyValueThrowsException(): void
    {
        $this->expectException(JsonDecodeException::class);
        Json::decode('');
    }

    public function testDecodeInvalidJsonThrowsException(): void
    {
        $this->expectException(JsonDecodeException::class);
        Json::decode('{"a":1,"b":2');
    }

    public function testHandleJsonError(): void
    {
        // Basic syntax error
        try {
            Json::decode("{'a': '1'}");
        } catch (JsonDecodeException $e) {
            $this->assertSame('Syntax error', $e->getMessage());
        }

        $fp   = fopen('php://stdin', 'rb');
        $data = ['a' => $fp];

        try {
            Json::encode($data);
        } catch (JsonEncodeException $e) {
            $this->assertSame('Type is not supported', $e->getMessage());
        } finally {
            fclose($fp);
        }
    }

}
