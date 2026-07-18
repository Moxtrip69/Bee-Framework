<?php

declare(strict_types=1);

namespace Bee\Updater\Security;

use Bee\Updater\Contract\TrustedKeyProvider;
use InvalidArgumentException;

final readonly class ArrayTrustedKeyProvider implements TrustedKeyProvider
{
    /**
     * @param array<string, string> $publicKeys Raw Ed25519 public keys indexed by key ID.
     */
    public function __construct(private array $publicKeys)
    {
        foreach ($publicKeys as $keyId => $publicKey) {
            if ($keyId === '' || strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
                throw new InvalidArgumentException(sprintf('Invalid Ed25519 public key: %s.', $keyId));
            }
        }
    }

    public function publicKey(string $keyId): ?string
    {
        return $this->publicKeys[$keyId] ?? null;
    }
}
