<?php

declare(strict_types=1);

namespace Slexphp\Serialization\Json\Decoder;

use Slexphp\Serialization\Contracts\Decoder\DecodeException;
use Slexphp\Serialization\Contracts\Decoder\DecoderInterface;

/**
 * @template-implements DecoderInterface<array>
 */
class JsonAssocDecoder extends JsonDecoder implements DecoderInterface
{
    public function __construct(?int $depth = null, ?int $options = null)
    {
        parent::__construct(true, $depth, $options);
    }

    public function decode(string $value): array
    {
        $result = parent::decode($value);
        if (!\is_array($result)) {
            throw new DecodeException('Json string does not contain array nor object');
        }
        return $result;
    }
}
