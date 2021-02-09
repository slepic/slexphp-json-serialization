<?php

declare(strict_types=1);

namespace Slexphp\Tests\Serialization\Json\Decoder;

use PHPUnit\Framework\TestCase;
use Slexphp\Serialization\Contracts\Decoder\DecodeExceptionInterface;
use Slexphp\Serialization\Contracts\Decoder\DecoderInterface;
use Slexphp\Serialization\Json\Decoder\JsonAssocDecoder;

final class JsonAssocDecoderTest extends TestCase
{
    public function testImplements(): void
    {
        $decoder = new JsonAssocDecoder();
        self::assertInstanceOf(DecoderInterface::class, $decoder);
    }

    /**
     * @param string $value
     * @param int|null $depth
     * @param int|null $options
     *
     * @dataProvider provideDecodeSuccessData
     */
    public function testDecodeSuccess(
        string $value,
        ?int $depth = null,
        ?int $options = null
    ): void {
        $expectation = \json_decode($value, true, $depth ?? 512, $options ?? 0);
        $decoder = new JsonAssocDecoder($depth, $options);
        $result = $decoder->decode($value);
        if (\is_object($expectation)) {
            self::assertEquals($expectation, $result);
        } else {
            self::assertSame($expectation, $result);
        }
    }

    public function provideDecodeSuccessData(): array
    {
        return [
            ['{}'],
            ['{"x":"y"}'],
            ['[[[]]]', 4],
        ];
    }

    /**
     * @param string $value
     * @param int $expectCode
     * @param int|null $depth
     * @param int|null $options
     *
     * @dataProvider provideDecodeFailureData
     */
    public function testDecodeFailure(
        string $value,
        int $expectCode,
        ?int $depth = null,
        ?int $options = null
    ): void {
        $decoder = new JsonAssocDecoder($depth, $options);

        try {
            $result = $decoder->decode($value);
            throw new \Exception('JsonDecodeException not thrown, instead decoded as ' . \gettype($result));
        } catch (DecodeExceptionInterface $e) {
            self::assertSame($expectCode, $e->getCode());

            $previous = $e->getPrevious();
            if ($expectCode !== 0 && $options & \JSON_THROW_ON_ERROR) {
                self::assertInstanceOf(\JsonException::class, $previous);
                self::assertSame($previous->getMessage(), $e->getMessage());
                self::assertSame($previous->getCode(), $e->getCode());
            } else {
                self::assertSame(null, $previous);
            }
        }
    }

    public function provideDecodeFailureData(): array
    {
        return [
            ['{', \JSON_ERROR_SYNTAX],
            ['{', \JSON_ERROR_SYNTAX],
            ['{', \JSON_ERROR_SYNTAX, null, \JSON_THROW_ON_ERROR],
            ['[[[[]]]]', \JSON_ERROR_DEPTH, 4],
            ['[[[[]]]]', \JSON_ERROR_DEPTH, 4, \JSON_THROW_ON_ERROR],
            ['true', 0],
            ['"string"', 0, null, \JSON_THROW_ON_ERROR],
        ];
    }
}
