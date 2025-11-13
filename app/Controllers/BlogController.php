<?php
namespace App\Controllers;

use App\Models\PostModel;

class BlogController extends BaseController
{
    public function index()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 9; 
        $offset = ($page - 1) * $limit;

        $postsModel = new PostModel();
        $total = (new PostModel())->where('published', 1)->countAllResults();
        $posts = $postsModel
            ->where('published', 1)
            ->orderBy('published_at', 'DESC')
            ->findAll($limit, $offset);

        $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;

        return view('blog/index', [
            'posts' => $posts,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function show($slug)
    {
        $postsModel = new PostModel();
        $post = $postsModel->findBySlug($slug);
        if (!$post || !$post['published']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Post not found');
        }
        
        $recent = $postsModel
            ->select('id, title, slug')
            ->where('published', 1)
            ->where('id !=', $post['id'])
            ->orderBy('published_at', 'DESC')
            ->findAll(3);

        return view('blog/show', [
            'post' => $post,
            'recentPosts' => $recent,
        ]);
    }
}