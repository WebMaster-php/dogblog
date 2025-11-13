<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;

class DogApi extends BaseController
{
    public function random()
    {
        $client = service('curlrequest');
        try {
            $response = $client->get('https://dog.ceo/api/breeds/image/random', [
                'timeout' => 5,
            ]);
            $status = $response->getStatusCode();
            if ($status !== 200) {
                return $this->response->setStatusCode(502)
                    ->setJSON(['error' => 'Upstream error']);
            }
            $json = json_decode($response->getBody(), true);
            if (($json['status'] ?? '') === 'success' && !empty($json['message'])) {
                return $this->response->setJSON(['image_url' => $json['message']]);
            }
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Failed to fetch image']);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Request failed']);
        }
    }
}