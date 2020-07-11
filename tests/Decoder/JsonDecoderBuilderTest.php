<?php declare(strict_types=1);

namespace Slexphp\Tests\Serialization\Json\Decoder;

use PHPUnit\Framework\TestCase;
use Slexphp\Serialization\Json\Decoder\JsonDecoder;
use Slexphp\Serialization\Json\Decoder\JsonDecoderBuilder;

final class JsonDecoderBuilderTest extends TestCase
{
    private JsonDecoderBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new JsonDecoderBuilder();
    }

    public function testDefault(): void
    {
        $expectedDecoder = new JsonDecoder();
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testDepth(): void
    {
        self::assertSame(512, $this->builder->getDepthLimit());
        $this->builder->setDepthLimit(22);
        self::assertSame(22, $this->builder->getDepthLimit());
        $expectedDecoder = new JsonDecoder(false, 22, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testAssoc(): void
    {
        self::assertFalse($this->builder->isObjectsAsArrays());

        $this->builder->setObjectsAsArrays();
        self::assertTrue($this->builder->isObjectsAsArrays());
        $expectedDecoder = new JsonDecoder(true, 512, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);

        $this->builder->setObjectsAsArrays(false);
        self::assertFalse($this->builder->isObjectsAsArrays());
        $expectedDecoder = new JsonDecoder(false, 512, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testBigIntAsString(): void
    {
        self::assertFalse($this->builder->isBigIntAsString());

        $this->builder->setBigIntAsString();
        self::assertTrue($this->builder->isBigIntAsString());
        $expectedDecoder = new JsonDecoder(false, 512, \JSON_BIGINT_AS_STRING);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);

        $this->builder->setBigIntAsString(false);
        self::assertFalse($this->builder->isBigIntAsString());
        $expectedDecoder = new JsonDecoder(false, 512, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testInvalidUtf8Ignored(): void
    {
        self::assertFalse($this->builder->isInvalidUtf8Ignored());

        $this->builder->setIgnoreInvalidUtf8();
        self::assertTrue($this->builder->isInvalidUtf8Ignored());
        $expectedDecoder = new JsonDecoder(false, 512, \JSON_INVALID_UTF8_IGNORE);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);

        $this->builder->setIgnoreInvalidUtf8(false);
        self::assertFalse($this->builder->isInvalidUtf8Ignored());
        $expectedDecoder = new JsonDecoder(false, 512, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testInvalidUtf8Substituted(): void
    {
        self::assertFalse($this->builder->isInvalidUtf8Substituted());

        $this->builder->setSubstituteInvalidUtf8();
        self::assertTrue($this->builder->isInvalidUtf8Substituted());
        $expectedDecoder = new JsonDecoder(false, 512, \JSON_INVALID_UTF8_SUBSTITUTE);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);

        $this->builder->setSubstituteInvalidUtf8(false);
        self::assertFalse($this->builder->isInvalidUtf8Substituted());
        $expectedDecoder = new JsonDecoder(false, 512, 0);
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }

    public function testMultiple(): void
    {
        $this->builder->setDepthLimit(22);
        $this->builder->setObjectsAsArrays(true);
        $this->builder->setBigIntAsString(true);
        $this->builder->setIgnoreInvalidUtf8(true);
        $this->builder->setSubstituteInvalidUtf8(true);

        self::assertSame(22, $this->builder->getDepthLimit());
        self::assertTrue($this->builder->isObjectsAsArrays());
        self::assertTrue($this->builder->isBigIntAsString());
        self::assertTrue($this->builder->isInvalidUtf8Ignored());
        self::assertTrue($this->builder->isInvalidUtf8Substituted());

        $expectedDecoder = new JsonDecoder(
            true,
            22,
            \JSON_BIGINT_AS_STRING | \JSON_INVALID_UTF8_IGNORE | \JSON_INVALID_UTF8_SUBSTITUTE
        );
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);

        $this->builder->setDepthLimit(25);
        $this->builder->setIgnoreInvalidUtf8(false);

        self::assertSame(25, $this->builder->getDepthLimit());
        self::assertTrue($this->builder->isObjectsAsArrays());
        self::assertTrue($this->builder->isBigIntAsString());
        self::assertFalse($this->builder->isInvalidUtf8Ignored());
        self::assertTrue($this->builder->isInvalidUtf8Substituted());

        $expectedDecoder = new JsonDecoder(
            true,
            25,
            \JSON_BIGINT_AS_STRING | \JSON_INVALID_UTF8_SUBSTITUTE
        );
        $decoder = $this->builder->createDecoder();
        self::assertEquals($expectedDecoder, $decoder);
    }
}
