<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostModel;
use CodeIgniter\HTTP\ResponseInterface;

class PostsController extends BaseController
{
    protected PostModel $posts;

    public function __construct()
    {
        $this->posts = new PostModel();
    }

    public function index()
    {
        $all = $this->posts->orderBy('created_at', 'DESC')->findAll();
        return view('admin/posts/index', ['posts' => $all]);
    }

    public function create()
    {
        return view('admin/posts/form', ['post' => null]);
    }

    public function store()
    {
        $title = $this->request->getPost('title');
        $slug = url_title($title, '-', true);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'image_url' => $this->request->getPost('image_url') ?: null,
            'published' => $this->request->getPost('published') ? 1 : 0,
            'author_id' => session()->get('user_id'),
            'published_at' => $this->request->getPost('published') ? date('Y-m-d H:i:s') : null,
        ];
        $this->posts->insert($data);
        return redirect()->to('/admin/posts')->with('success', 'Post created');
    }

    public function edit($id)
    {
        $post = $this->posts->find($id);
        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Post not found');
        }
        return view('admin/posts/form', ['post' => $post]);
    }

    public function update($id)
    {
        $post = $this->posts->find($id);
        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Post not found');
        }

        $title = $this->request->getPost('title');
        $slug = url_title($title, '-', true);
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'image_url' => $this->request->getPost('image_url') ?: null,
            'published' => $this->request->getPost('published') ? 1 : 0,
            'author_id' => session()->get('user_id'),
            'published_at' => $this->request->getPost('published') ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ];
        $this->posts->update($id, $data);
        return redirect()->to('/admin/posts')->with('success', 'Post updated');
    }

    public function delete($id)
    {
        $this->posts->delete($id);
        return redirect()->to('/admin/posts')->with('success', 'Post deleted');
    }

    public function fetchImage()
    {
        $apiUrl = 'https://dog.ceo/api/breeds/image/random';
        $client = service('curlrequest');
        $imageUrl = null;

        try {
            $apiRes = $client->get($apiUrl, ['timeout' => 5]);
            if ($apiRes->getStatusCode() === 200) {
                $data = json_decode($apiRes->getBody(), true);
                if (($data['status'] ?? '') === 'success' && !empty($data['message'])) {
                    $imageUrl = (string) $data['message'];
                }
            }
        } catch (\Throwable $e) {
            
        }

        if (!$imageUrl) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to fetch image URL']);
        }

        $uploadsDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0755, true);
        }

        $ext = 'jpg';
        try {
            $imgRes = $client->get($imageUrl, ['timeout' => 10]);
            if ($imgRes->getStatusCode() !== 200) {
                return $this->response->setStatusCode(502)->setJSON(['error' => 'Upstream image error']);
            }
            $ctype = strtolower($imgRes->getHeaderLine('Content-Type'));
            if (str_starts_with($ctype, 'image/')) {
                if ($ctype === 'image/jpeg' || $ctype === 'image/jpg') {
                    $ext = 'jpg';
                } elseif ($ctype === 'image/png') {
                    $ext = 'png';
                } elseif ($ctype === 'image/gif') {
                    $ext = 'gif';
                } elseif ($ctype === 'image/webp') {
                    $ext = 'webp';
                }
            }
            $body = $imgRes->getBody();

            // Basic size guard (max ~5MB)
            if (strlen($body) > 5 * 1024 * 1024) {
                return $this->response->setStatusCode(413)->setJSON(['error' => 'Image too large']);
            }

            $filename = 'dog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $fullPath = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
            if (@file_put_contents($fullPath, $body) === false) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save image']);
            }

            $localUrl = '/uploads/' . $filename;

            $postId = (int) ($this->request->getGet('post_id') ?? 0);
            if ($postId > 0) {
                $existing = $this->posts->find($postId);
                if ($existing) {
                    $this->posts->update($postId, ['image_url' => $localUrl]);
                }
            }

            return $this->response->setJSON(['image_url' => $localUrl])->setStatusCode(200);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Download failed']);
        }
    }
}