<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

use stdClass;

final readonly class Manifest
{
    public function __construct(private stdClass $data)
    {
    }

    public function packageId(): string
    {
        return $this->data->package_id;
    }

    public function productId(): string
    {
        return $this->data->product_id;
    }

    public function sourceVersion(): string
    {
        return $this->data->source_version;
    }

    public function targetVersion(): string
    {
        return $this->data->target_version;
    }

    public function keyId(): string
    {
        return $this->data->key_id;
    }

    public function checksumsSha256(): string
    {
        return $this->data->checksums_sha256;
    }

    public function data(): stdClass
    {
        return $this->data;
    }
}
