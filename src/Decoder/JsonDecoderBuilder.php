<?php declare(strict_types=1);

namespace Slexphp\Serialization\Json\Decoder;

class JsonDecoderBuilder
{
    private bool $assoc = false;
    private int $depth = 512;
    private int $options = 0;

    public function getDepthLimit(): int
    {
        return $this->depth;
    }

    public function setDepthLimit(int $limit): void
    {
        $this->depth = $limit;
    }

    public function isObjectsAsArrays(): bool
    {
        return $this->assoc;
    }

    public function setObjectsAsArrays(bool $objectsAsArrays = true): void
    {
        $this->assoc = $objectsAsArrays;
    }

    public function isBigIntAsString(): bool
    {
        return ($this->options & \JSON_BIGINT_AS_STRING) !== 0;
    }

    public function setBigIntAsString(bool $bigIntAsString): void
    {
        if ($bigIntAsString) {
            $this->options |= \JSON_BIGINT_AS_STRING;
        } else {
            $this->options &= ~\JSON_BIGINT_AS_STRING;
        }
    }

    public function isInvalidUtf8Ignored(): bool
    {
        return ($this->options & \JSON_INVALID_UTF8_IGNORE) !== 0;
    }

    public function setIgnoreInvalidUtf8(bool $ignoreInvalidUtf8 = true): void
    {
        if ($ignoreInvalidUtf8) {
            $this->options |= \JSON_INVALID_UTF8_IGNORE;
        } else {
            $this->options &= ~\JSON_INVALID_UTF8_IGNORE;
        }
    }

    public function isInvalidUtf8Substituted(): bool
    {
        return ($this->options & \JSON_INVALID_UTF8_SUBSTITUTE) !== 0;
    }

    public function setSubstituteInvalidUtf8(bool $substituteInvalidUtf8 = true): void
    {
        if ($substituteInvalidUtf8) {
            $this->options |= \JSON_INVALID_UTF8_SUBSTITUTE;
        } else {
            $this->options &= ~\JSON_INVALID_UTF8_SUBSTITUTE;
        }
    }

    public function createDecoder(): JsonDecoder
    {
        return new JsonDecoder($this->assoc, $this->depth, $this->options);
    }
}
