<?php

declare(strict_types=1);

use Bee\Core\Config\Configuration;
use Bee\Core\Http\HttpRequest;
use Bee\Core\Http\HttpResponse;

/**
 * Example REST controller for the modern router and BeeModel.
 */
final class exampleArticleController
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function index(HttpRequest $request): array
    {
        $page = sanitize_integer($request->query['page'] ?? 1, 1) ?? 1;
        $perPage = sanitize_integer($request->query['per_page'] ?? 10, 1, 100) ?? 10;
        $status = sanitize_string($request->query['status'] ?? '', 20);

        $query = exampleArticleModel::orderBy('created_at', 'desc');
        if (in_array($status, ['draft', 'published'], true)) {
            $query = $query->where('status', $status);
        }

        $pagination = $query->paginate($perPage, $page);
        $pagination['data'] = array_map(
            static fn (exampleArticleModel $article): array => $article->toArray(),
            $pagination['data']
        );

        return [
            'application' => $this->configuration->application->name,
            ...$pagination,
        ];
    }

    public function show(int $id): HttpResponse
    {
        $article = exampleArticleModel::find($id);
        if (!$article instanceof exampleArticleModel) {
            return HttpResponse::json(['message' => 'Article not found.'], 404);
        }

        return HttpResponse::json(['data' => $article->toArray()]);
    }

    public function store(HttpRequest $request): HttpResponse
    {
        $data = $this->validatedData($request);
        if ($data instanceof HttpResponse) {
            return $data;
        }

        $article = exampleArticleModel::create($data);

        return HttpResponse::json(['data' => $article->toArray()], 201);
    }

    public function update(int $id, HttpRequest $request): HttpResponse
    {
        $article = exampleArticleModel::find($id);
        if (!$article instanceof exampleArticleModel) {
            return HttpResponse::json(['message' => 'Article not found.'], 404);
        }
        $data = $this->validatedData($request, $article);
        if ($data instanceof HttpResponse) {
            return $data;
        }

        $article->fill($data)->save();

        return HttpResponse::json(['data' => $article->toArray()]);
    }

    public function destroy(int $id): HttpResponse
    {
        $article = exampleArticleModel::find($id);
        if (!$article instanceof exampleArticleModel) {
            return HttpResponse::json(['message' => 'Article not found.'], 404);
        }
        $article->delete();

        return HttpResponse::noContent();
    }

    /** @return array<string, mixed>|HttpResponse */
    private function validatedData(
        HttpRequest $request,
        ?exampleArticleModel $article = null
    ): array|HttpResponse
    {
        $title = sanitize_string($request->body['title'] ?? '', 160);
        $content = sanitize_string($request->body['content'] ?? '', 10000);
        $status = sanitize_string($request->body['status'] ?? 'draft', 20);
        if ($title === '' || $content === '' || !in_array($status, ['draft', 'published'], true)) {
            return HttpResponse::json([
                'message' => 'Title, content and a valid status are required.',
            ], 422);
        }

        $slugSource = sanitize_string($request->body['slug'] ?? '', 180);

        return [
            'title' => $title,
            'slug' => exampleArticleModel::uniqueSlug(
                $slugSource === '' ? $title : $slugSource,
                $article === null ? null : (int) $article->id
            ),
            'excerpt' => sanitize_string($request->body['excerpt'] ?? '', 255),
            'content' => $content,
            'status' => $status,
            'views' => sanitize_integer($request->body['views'] ?? 0, 0) ?? 0,
            'metadata' => is_array($request->body['metadata'] ?? null)
                ? $request->body['metadata']
                : [],
        ];
    }
}
