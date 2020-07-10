<?php declare(strict_types=1);

namespace Slexphp\Serialization\Json\Encoder;

class JsonEncoderBuilder
{
    private int $options = 0;
    private int $depth = 512;

    public function getDepthLimit(): int
    {
        return $this->depth;
    }

    public function setDepthLimit(int $limit): void
    {
        $this->depth = $limit;
    }

    public function createEncoder(): JsonEncoder
    {
        return new JsonEncoder($this->options, $this->depth);
    }
}
