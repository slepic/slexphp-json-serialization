<?php declare(strict_types=1);

namespace Slexphp\Tests\Serialization\Json\Encoder;

use PHPUnit\Framework\TestCase;
use Slexphp\Serialization\Json\Encoder\JsonEncoder;
use Slexphp\Serialization\Contracts\Encoder\EncodeExceptionInterface;
use Slexphp\Serialization\Contracts\Encoder\EncoderInterface;

final class JsonEncoderTest extends TestCase
{
    public function testImplements(): void
    {
        $encoder = new JsonEncoder();
        $this->assertInstanceOf(EncoderInterface::class, $encoder);
    }

    /**
     * @param mixed $value
     * @param int|null $options
     * @param int|null $depth
     *
     * @dataProvider provideEncodeSuccessData
     */
    public function testEncodeSuccess($value, ?int $options = null, ?int $depth = null): void
    {
        $expectedOutput = \json_encode($value, $options ?? 0, $depth ?? 512);
        if (!\is_string($expectedOutput)) {
            $this->expectException(\Throwable::class);
        }
        $encoder = new JsonEncoder($options, $depth);
        $output = $encoder->encode($value);
        if (\is_string($expectedOutput)) {
            $this->assertSame($expectedOutput, $output);
        }
    }

    public function provideEncodeSuccessData(): array
    {
        return [
            [new \stdClass()],
            [[]],
            ['string'],
            [1],
            ['1'],
            [null],
            [false],
            [true],
            ['true'],
            [['x' => 'y']],
            [(object) ['x' => 'y']],
            [['x', 'y']],
            [$depth4 = [[[[]]]], null, 4]
        ];
    }

    /**
     * @param mixed $value
     * @param int $expectCode
     * @param int|null $options
     * @param int|null $depth
     *
     * @dataProvider provideEncodeFailureData
     */
    public function testEncodeFailure($value, int $expectCode, ?int $options = null, ?int $depth = null): void
    {
        $encoder = new JsonEncoder($options, $depth);

        try {
            $result = $encoder->encode($value);
            throw new \Exception('Exception not thrown, instead encoded as: ' . $result);
        } catch (EncodeExceptionInterface $e) {
            self::assertSame($expectCode, $e->getCode());

            $previous = $e->getPrevious();
            if (($options ?? 0) & \JSON_THROW_ON_ERROR) {
                self::assertInstanceOf(\JsonException::class, $previous);
                self::assertSame($previous->getMessage(), $e->getMessage());
                self::assertSame($previous->getCode(), $e->getCode());
            } else {
                self::assertSame(null, $e->getPrevious());
            }
        }
    }

    public function provideEncodeFailureData(): array
    {
        $recursion = [];
        $recursion['self'] =& $recursion;

        return [
            [$recursion, \JSON_ERROR_RECURSION, null, null],
            [$recursion, \JSON_ERROR_RECURSION, \JSON_THROW_ON_ERROR, null],
            [$depth5 = [[[[[]]]]], \JSON_ERROR_DEPTH, null, 4],
            [$depth5, \JSON_ERROR_DEPTH, \JSON_THROW_ON_ERROR, 4],
        ];
    }
}
