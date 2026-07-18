<?php

declare(strict_types=1);

use Bee\Core\Database\ModelQuery;

/**
 * Example model for the modern BeeModel API.
 *
 * Required table: example_articles (see README.md).
 */
final class exampleArticleModel extends BeeModel
{
    protected string $table = 'example_articles';

    protected array $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'views',
        'metadata',
    ];

    protected array $guarded = ['id'];

    protected array $casts = [
        'views' => 'integer',
        'metadata' => 'json',
    ];

    protected bool $timestamps = true;

    public static function published(): ModelQuery
    {
        return static::where('status', 'published');
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = mb_substr(sanitize_slug($value) ?: 'article', 0, 170, 'UTF-8');
        $slug = $base;
        $suffix = 2;

        while (true) {
            $query = static::where('slug', $slug);
            if ($ignoreId !== null) {
                $query = $query->where('id', '!=', $ignoreId);
            }
            if (!$query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $suffix++;
        }
    }
}
