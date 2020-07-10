<?php declare(strict_types=1);

namespace Slexphp\Serialization\Json\Encoder;

use Slexphp\Serialization\Contracts\Encoder\EncodeException;
use Slexphp\Serialization\Contracts\Encoder\EncoderInterface;

/**
 * @template-implements EncoderInterface<string|bool|int|float|array|object|null>
 */
class JsonEncoder implements EncoderInterface
{
    private int $options;
    private int $depth;

    public function __construct(?int $options = null, ?int $depth = null)
    {
        $this->options = $options ?? 0;
        $this->depth = $depth ?? 512;
    }

    public function encode($value): string
    {
        try {
            $result = \json_encode($value, $this->options, $this->depth);
        } catch (\JsonException $e) {
            throw new EncodeException($e->getMessage(), (int) $e->getCode(), $e);
        }

        if (!\is_string($result)) {
            throw new EncodeException(\json_last_error_msg(), \json_last_error());
        }

        return $result;
    }
}
