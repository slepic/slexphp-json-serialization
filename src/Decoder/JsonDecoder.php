<?php declare(strict_types=1);

namespace Slexphp\Serialization\Json\Decoder;

use Slexphp\Serialization\Contracts\Decoder\DecodeException;
use Slexphp\Serialization\Contracts\Decoder\DecoderInterface;

/**
 * @template-implements DecoderInterface<string|bool|int|float|array|\stdClass|null>
 */
class JsonDecoder implements DecoderInterface
{
    private bool $assoc;
    private int $depth;
    private int $options;

    public function __construct(?bool $assoc = null, ?int $depth = null, ?int $options = null)
    {
        $this->assoc = $assoc ?? false;
        $this->depth = $depth ?? 512;
        $this->options = $options ?? 0;
    }

    public function decode(string $json)
    {
        try {
            /** @var string|int|float|array|\stdClass|null $result */
            $result = \json_decode($json, $this->assoc, $this->depth, $this->options);
        } catch (\JsonException $e) {
            throw new DecodeException($e->getMessage(), (int) $e->getCode(), $e);
        }

        if (($this->options & \JSON_THROW_ON_ERROR) === 0) {
            $lastError = \json_last_error();
            if ($result === null && $lastError !== \JSON_ERROR_NONE) {
                throw new DecodeException(\json_last_error_msg(), $lastError);
            }
        }

        return $result;
    }
}
