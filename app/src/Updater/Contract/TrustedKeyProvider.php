<?php

declare(strict_types=1);

namespace Bee\Updater\Contract;

interface TrustedKeyProvider
{
    /** Returns the raw Ed25519 public key bytes, or null when it is unknown. */
    public function publicKey(string $keyId): ?string;
}
