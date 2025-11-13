<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PostModel;

class DogPostsSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $client = service('curlrequest');

        $uploadsDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0755, true);
        }

        $posts = new PostModel();

        for ($i = 0; $i < 30; $i++) {
            $imageUrl = null;
            try {
                $apiRes = $client->get('https://dog.ceo/api/breeds/image/random', ['timeout' => 5]);
                if ($apiRes->getStatusCode() === 200) {
                    $data = json_decode($apiRes->getBody(), true);
                    if (($data['status'] ?? '') === 'success' && !empty($data['message'])) {
                        $imageUrl = (string) $data['message'];
                    }
                }
            } catch (\Throwable $e) {
                // skip on error
            }

            $localUrl = null;
            if ($imageUrl) {
                try {
                    $imgRes = $client->get($imageUrl, ['timeout' => 10]);
                    if ($imgRes->getStatusCode() === 200) {
                        $ctype = strtolower($imgRes->getHeaderLine('Content-Type'));
                        $ext = 'jpg';
                        if (str_starts_with($ctype, 'image/')) {
                            if ($ctype === 'image/png') {
                                $ext = 'png';
                            } elseif ($ctype === 'image/gif') {
                                $ext = 'gif';
                            } elseif ($ctype === 'image/webp') {
                                $ext = 'webp';
                            } else {
                                $ext = 'jpg';
                            }
                        }
                        $body = $imgRes->getBody();
                        if (strlen($body) <= 5 * 1024 * 1024) { // max ~5MB
                            $filename = 'seed_dog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            @file_put_contents($uploadsDir . DIRECTORY_SEPARATOR . $filename, $body);
                            $localUrl = '/uploads/' . $filename;
                        }
                    }
                } catch (\Throwable $e) {
                    // skip on error
                }
            }

            // Build post content
            $title = $faker->sentence(3);
            $slug = url_title($title, '-', true);
            $content = '';
            $paraCount = random_int(3, 6);
            for ($p = 0; $p < $paraCount; $p++) {
                $content .= '<p>' . $faker->paragraph(random_int(3, 6)) . '</p>';
            }
            $content .= '<p><em>Photo courtesy of <a href="https://dog.ceo" target="_blank" rel="noopener">dog.ceo</a>.</em></p>';
            $publishedAt = $faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d H:i:s');

            // Insert post
            $posts->insert([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'image_url' => $localUrl ?: ($imageUrl ?: null),
                'published' => 1,
                'author_id' => null,
                'published_at' => $publishedAt,
            ]);
        }
    }
}