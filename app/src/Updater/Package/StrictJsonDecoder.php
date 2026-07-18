<?php

declare(strict_types=1);

namespace Bee\Updater\Package;

use Bee\Updater\Exception\ValidationException;
use JsonException;
use stdClass;

final class StrictJsonDecoder
{
    private int $position = 0;

    private int $length = 0;

    private string $json = '';

    public function __construct(
        private readonly int $maximumBytes = 1048576,
        private readonly int $maximumDepth = 64
    ) {
    }

    public function decodeObject(string $json): stdClass
    {
        if ($json === '' || strlen($json) > $this->maximumBytes) {
            throw new ValidationException(['JSON is empty or exceeds the permitted size.']);
        }

        if (str_starts_with($json, "\xEF\xBB\xBF") || !mb_check_encoding($json, 'UTF-8')) {
            throw new ValidationException(['JSON must be UTF-8 without BOM.']);
        }

        $this->json = $json;
        $this->length = strlen($json);
        $this->position = 0;

        try {
            $this->scanValue(0);
            $this->skipWhitespace();

            if ($this->position !== $this->length) {
                throw new ValidationException(['JSON contains trailing data.']);
            }

            $decoded = json_decode($json, false, $this->maximumDepth, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ValidationException(['Invalid JSON: ' . $exception->getMessage()]);
        }

        if (!$decoded instanceof stdClass) {
            throw new ValidationException(['The JSON root must be an object.']);
        }

        return $decoded;
    }

    private function scanValue(int $depth): void
    {
        if ($depth > $this->maximumDepth) {
            throw new ValidationException(['JSON exceeds the permitted nesting depth.']);
        }

        $this->skipWhitespace();
        $character = $this->json[$this->position] ?? '';

        if ($character === '{') {
            $this->scanObject($depth + 1);
            return;
        }

        if ($character === '[') {
            $this->scanArray($depth + 1);
            return;
        }

        if ($character === '"') {
            $this->scanString();
            return;
        }

        if (preg_match('/\G(?:-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?|true|false|null)/', $this->json, $match, 0, $this->position) === 1) {
            $this->position += strlen($match[0]);
            return;
        }

        throw new ValidationException(['Invalid JSON token.']);
    }

    private function scanObject(int $depth): void
    {
        $this->position++;
        $keys = [];
        $this->skipWhitespace();

        if (($this->json[$this->position] ?? '') === '}') {
            $this->position++;
            return;
        }

        while (true) {
            $this->skipWhitespace();
            $key = $this->scanString();

            if (isset($keys[$key])) {
                throw new ValidationException([sprintf('Duplicate JSON object key: %s.', $key)]);
            }

            $keys[$key] = true;
            $this->skipWhitespace();
            $this->expect(':');
            $this->scanValue($depth);
            $this->skipWhitespace();

            $separator = $this->json[$this->position] ?? '';
            if ($separator === '}') {
                $this->position++;
                return;
            }

            $this->expect(',');
        }
    }

    private function scanArray(int $depth): void
    {
        $this->position++;
        $this->skipWhitespace();

        if (($this->json[$this->position] ?? '') === ']') {
            $this->position++;
            return;
        }

        while (true) {
            $this->scanValue($depth);
            $this->skipWhitespace();

            $separator = $this->json[$this->position] ?? '';
            if ($separator === ']') {
                $this->position++;
                return;
            }

            $this->expect(',');
        }
    }

    private function scanString(): string
    {
        $start = $this->position;
        $this->expect('"');

        while ($this->position < $this->length) {
            $character = $this->json[$this->position++];

            if ($character === '"') {
                $encoded = substr($this->json, $start, $this->position - $start);

                try {
                    return json_decode($encoded, true, 2, JSON_THROW_ON_ERROR);
                } catch (JsonException $exception) {
                    throw new ValidationException(['Invalid JSON string: ' . $exception->getMessage()]);
                }
            }

            if ($character === '\\') {
                $escape = $this->json[$this->position++] ?? '';
                if (!str_contains('"\\/bfnrtu', $escape)) {
                    throw new ValidationException(['Invalid JSON string escape.']);
                }

                if ($escape === 'u') {
                    $hex = substr($this->json, $this->position, 4);
                    if (strlen($hex) !== 4 || ctype_xdigit($hex) === false) {
                        throw new ValidationException(['Invalid JSON Unicode escape.']);
                    }
                    $this->position += 4;
                }
            } elseif (ord($character) < 0x20) {
                throw new ValidationException(['JSON strings cannot contain control characters.']);
            }
        }

        throw new ValidationException(['Unterminated JSON string.']);
    }

    private function skipWhitespace(): void
    {
        while ($this->position < $this->length && str_contains(" \t\r\n", $this->json[$this->position])) {
            $this->position++;
        }
    }

    private function expect(string $character): void
    {
        if (($this->json[$this->position] ?? '') !== $character) {
            throw new ValidationException([sprintf('Expected JSON token %s.', $character)]);
        }

        $this->position++;
    }
}
