<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PostModel;

class PostsApi extends BaseController
{
    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        $limit = $limit > 0 ? min($limit, 50) : 10; // cap to avoid abuse
        $status = strtolower((string) ($this->request->getGet('status') ?? 'published'));

        $totalQuery = new PostModel();
        if ($status === 'draft') {
            $totalQuery->where('published', 0);
        } elseif ($status === 'all') {
            // no status filter
        } else { 
            $totalQuery->where('published', 1);
        }
        $total = $totalQuery->countAllResults();

        $offset = ($page - 1) * $limit;

        $itemsQuery = new PostModel();
        if ($status === 'draft') {
            $itemsQuery->where('published', 0);
        } elseif ($status === 'all') {
            // no status filter
        } else {
            $itemsQuery->where('published', 1);
        }

        $rows = $itemsQuery
            ->select('id, title, slug, image_url, content, published_at')
            ->orderBy('published_at', 'DESC')
            ->findAll($limit, $offset);

        $posts = array_map(function ($p) {
            $p['dog_image_url'] = $p['image_url'] ?? null;
            return $p;
        }, $rows);

        $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;

        return $this->response->setJSON([
            'posts' => $posts,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => $totalPages,
                'status' => $status,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $post = (new PostModel())->findBySlug($slug);
        if (!$post || (int)($post['published'] ?? 0) !== 1) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'Post not found']);
        }
        $data = [
            'id' => $post['id'],
            'title' => $post['title'],
            'slug' => $post['slug'],
            'image_url' => $post['image_url'],
            'content' => $post['content'],
            'published_at' => $post['published_at'],
        ];
        return $this->response->setJSON(['post' => $data]);
    }
}