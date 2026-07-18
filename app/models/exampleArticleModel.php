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
}
