<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/classes/BeeModel.php';

final class ArticleModel extends BeeModel
{
    public static PDO $connection;
    protected string $table = 'articles';
    protected array $fillable = ['title', 'status', 'views', 'metadata'];
    protected array $casts = ['views' => 'integer', 'metadata' => 'json'];
    protected bool $timestamps = true;

    protected function pdo(): PDO
    {
        return self::$connection;
    }
}

$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE articles (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, status TEXT, views INTEGER, metadata TEXT, created_at TEXT, updated_at TEXT)');
ArticleModel::$connection = $pdo;

$article = new ArticleModel(['title' => 'First', 'status' => 'draft', 'views' => '5', 'metadata' => ['featured' => true], 'ignored' => 'unsafe']);
coreAssert($article->save(), 'A new model must be insertable.');
coreAssert($article->exists(), 'Inserted model must be marked as persisted.');
coreAssert(is_int($article->views) && $article->views === 5, 'Attribute casts must apply on access.');
coreAssert($article->metadata === ['featured' => true], 'JSON casts must serialize and hydrate arrays.');
coreAssert(!array_key_exists('ignored', $article->toArray()), 'Fillable fields must protect mass assignment.');

$found = ArticleModel::find((int) $article->id);
coreAssert($found instanceof ArticleModel && $found->title === 'First', 'find() must support a scalar primary key.');
coreAssert(ArticleModel::find(['id' => $article->id]) instanceof ArticleModel, 'Legacy array-based find() must remain compatible.');
coreAssert(ArticleModel::first(['status' => 'draft']) instanceof ArticleModel, 'Legacy first() must remain compatible.');
coreAssert(ArticleModel::column('title', ['id' => $article->id]) === 'First', 'Legacy column() must remain compatible.');
coreAssert(count(ArticleModel::fetchAll()) === 1, 'Legacy fetchAll() must still return rows.');

$article->title = 'Updated';
coreAssert($article->isDirty('title') && $article->save(), 'Dirty models must update changed fields.');
coreAssert(!$article->isDirty(), 'A successful save must synchronize original values.');

foreach ([['Second', 'published', 10], ['Third', 'published', 20]] as [$title, $status, $views]) {
    (new ArticleModel(compact('title', 'status', 'views')))->save();
}

$published = ArticleModel::where('status', 'published')->orderBy('views', 'desc')->get();
coreAssert(count($published) === 2 && $published[0]->title === 'Third', 'Fluent queries must filter, order and hydrate models.');
coreAssert(ArticleModel::whereIn('id', [1, 3])->count() === 2, 'whereIn() and count() must work with bindings.');
coreAssert(ArticleModel::where('views', '>=', 10)->exists(), 'exists() must use constrained queries.');
$page = ArticleModel::orderBy('id')->paginate(2, 2);
coreAssert($page['total'] === 3 && count($page['data']) === 1, 'paginate() must return data and metadata.');
coreAssert(ArticleModel::where('status', 'draft')->update(['status' => 'published']), 'Constrained bulk updates must work.');
coreAssert(ArticleModel::where('title', 'Second')->delete(), 'Constrained bulk deletes must work.');

try {
    ArticleModel::newQuery()->orderBy('id; DROP TABLE articles');
    throw new RuntimeException('Unsafe identifiers must be rejected.');
} catch (InvalidArgumentException) {
}

coreAssert($found->delete(), 'Hydrated models must retain their database connection.');

echo "PASS: BeeModel legacy APIs and modern ORM queries work\n";
