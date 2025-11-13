<?php
namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'slug', 'content', 'image_url', 'published', 'author_id', 'published_at'];

    public function getPublishedPosts()
    {
        return $this->where('published', 1)->orderBy('published_at', 'DESC')->findAll();
    }

    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }
}