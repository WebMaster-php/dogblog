<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/admin/posts');
        }
        return view('auth/login');
    }

    public function attempt()
    {
        $usernameOrEmail = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $users = new UserModel();
        $user = $users->where('username', $usernameOrEmail)
                      ->orWhere('email', $usernameOrEmail)
                      ->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        session()->set(['user_id' => $user['id'], 'username' => $user['username']]);
        return redirect()->to('/admin/posts');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}