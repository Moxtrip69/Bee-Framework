<?php

declare(strict_types=1);

if (!function_exists('bee_sanitize_scalar')) {
    function bee_sanitize_scalar(mixed $value): ?string
    {
        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return null;
    }
}

if (!function_exists('bee_sanitize_utf8')) {
    function bee_sanitize_utf8(string $value): string
    {
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

        return class_exists(Normalizer::class)
            ? (Normalizer::normalize($value, Normalizer::FORM_C) ?: $value)
            : $value;
    }
}

if (!function_exists('bee_sanitize_decimal')) {
    function bee_sanitize_decimal(mixed $value): ?string
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/[\s\x{00A0}]+/u', '', trim($value));
        if (!is_string($value) || preg_match('/^[+-]?[0-9.,]+$/D', $value) !== 1) {
            return null;
        }

        $comma = strrpos($value, ',');
        $dot = strrpos($value, '.');
        if ($comma !== false && $dot !== false) {
            $decimal = $comma > $dot ? ',' : '.';
            $thousands = $decimal === ',' ? '.' : ',';
            $value = str_replace($thousands, '', $value);
            $value = str_replace($decimal, '.', $value);
        } elseif ($comma !== false) {
            $value = substr_count($value, ',') === 1
                ? str_replace(',', '.', $value)
                : str_replace(',', '', $value);
        } elseif (substr_count($value, '.') > 1) {
            $value = str_replace('.', '', $value);
        }

        return preg_match('/^[+-]?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)$/D', $value) === 1
            ? $value
            : null;
    }
}

if (!function_exists('sanitize_string')) {
    function sanitize_string(mixed $value, ?int $maxLength = null): string
    {
        $value = bee_sanitize_scalar($value) ?? '';
        $value = bee_sanitize_utf8(strip_tags($value));
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        return $maxLength === null ? $value : mb_substr($value, 0, max(0, $maxLength), 'UTF-8');
    }
}

if (!function_exists('sanitize_name')) {
    function sanitize_name(mixed $value, int $maxLength = 120): string
    {
        $value = sanitize_string($value);
        $value = preg_replace("/[^\\p{L}\\p{M}'’.-]+/u", ' ', $value) ?? '';

        return mb_substr(preg_replace('/\s+/u', ' ', trim($value)) ?? '', 0, max(0, $maxLength), 'UTF-8');
    }
}

if (!function_exists('sanitize_phone')) {
    function sanitize_phone(mixed $value): ?string
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null) {
            return null;
        }

        $hasPlus = str_starts_with(ltrim($value), '+');
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if (strlen($digits) < 7 || strlen($digits) > 15) {
            return null;
        }

        return ($hasPlus ? '+' : '') . $digits;
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email(mixed $value): ?string
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null) {
            return null;
        }

        $value = mb_strtolower(trim($value), 'UTF-8');
        if (substr_count($value, '@') !== 1) {
            return null;
        }
        [$local, $domain] = explode('@', $value, 2);
        if (function_exists('idn_to_ascii')) {
            $asciiDomain = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($asciiDomain === false) {
                return null;
            }
            $domain = $asciiDomain;
        }
        $email = $local . '@' . strtolower($domain);

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : null;
    }
}

if (!function_exists('sanitize_address')) {
    function sanitize_address(mixed $value, int $maxLength = 255): string
    {
        $value = sanitize_string($value);
        $value = preg_replace("/[^\\p{L}\\p{M}\\p{N}\s.,#'’°\/-]+/u", '', $value) ?? '';

        return mb_substr(trim($value), 0, max(0, $maxLength), 'UTF-8');
    }
}

if (!function_exists('sanitize_integer')) {
    function sanitize_integer(mixed $value, ?int $min = null, ?int $max = null): ?int
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null || preg_match('/^[+-]?[0-9]+$/D', trim($value)) !== 1) {
            return null;
        }
        $integer = filter_var(trim($value), FILTER_VALIDATE_INT);
        if ($integer === false || ($min !== null && $integer < $min) || ($max !== null && $integer > $max)) {
            return null;
        }

        return $integer;
    }
}

if (!function_exists('sanitize_number')) {
    function sanitize_number(mixed $value, ?float $min = null, ?float $max = null): ?float
    {
        $normalized = bee_sanitize_decimal($value);
        if ($normalized === null) {
            return null;
        }
        $number = filter_var($normalized, FILTER_VALIDATE_FLOAT);
        if ($number === false || !is_finite($number) || ($min !== null && $number < $min) || ($max !== null && $number > $max)) {
            return null;
        }

        return $number;
    }
}

if (!function_exists('sanitize_money')) {
    function sanitize_money(mixed $value, int $decimals = 2): ?string
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null) {
            return null;
        }
        $value = preg_replace('/[^0-9.,+\-\s\x{00A0}]/u', '', $value) ?? '';
        $number = sanitize_number($value);

        return $number === null ? null : number_format($number, max(0, min(8, $decimals)), '.', '');
    }
}

if (!function_exists('sanitize_boolean')) {
    function sanitize_boolean(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (!is_scalar($value)) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}

if (!function_exists('sanitize_url')) {
    function sanitize_url(mixed $value): ?string
    {
        $value = bee_sanitize_scalar($value);
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) && filter_var($value, FILTER_VALIDATE_URL) !== false
            ? $value
            : null;
    }
}

if (!function_exists('sanitize_slug')) {
    function sanitize_slug(mixed $value, int $maxLength = 200): string
    {
        $value = mb_strtolower(sanitize_string($value), 'UTF-8');
        $value = strtr($value, [
            "\u{00E1}" => 'a', "\u{00E9}" => 'e', "\u{00ED}" => 'i', "\u{00F3}" => 'o',
            "\u{00FA}" => 'u', "\u{00FC}" => 'u', "\u{00F1}" => 'n', "\u{00E7}" => 'c',
        ]);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = is_string($ascii) ? $ascii : $value;
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';

        return mb_substr(trim($value, '-'), 0, max(0, $maxLength), 'UTF-8');
    }
}
