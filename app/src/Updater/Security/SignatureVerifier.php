<?php

declare(strict_types=1);

namespace Bee\Updater\Security;

use Bee\Updater\Contract\TrustedKeyProvider;
use Bee\Updater\Exception\ValidationException;

final readonly class SignatureVerifier
{
    public function __construct(private TrustedKeyProvider $trustedKeys)
    {
    }

    public function verify(string $manifestBytes, string $encodedSignature, string $keyId): void
    {
        $publicKey = $this->trustedKeys->publicKey($keyId);

        if ($publicKey === null) {
            throw new ValidationException([sprintf('Unknown signing key: %s.', $keyId)]);
        }

        if (strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            throw new ValidationException([sprintf('Invalid trusted public key: %s.', $keyId)]);
        }

        $signature = base64_decode($encodedSignature, true);
        if ($signature === false || base64_encode($signature) !== $encodedSignature
            || strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            throw new ValidationException(['signature.sig must contain one canonical Base64 Ed25519 signature.']);
        }

        if (!sodium_crypto_sign_verify_detached($signature, $manifestBytes, $publicKey)) {
            throw new ValidationException(['The manifest signature is invalid.']);
        }
    }
}
