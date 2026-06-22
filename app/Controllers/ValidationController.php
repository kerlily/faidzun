<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class ValidationController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('validation/v_login');
    }
    public function proses()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $model = new UsersModel();
        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            session()->set([
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'role' => $user['role'],
                'id_kelas' => $user['id_kelas'],
                'logged_in' => true
            ]);
            return redirect()->to('/dashboard'); // Ganti sesuai halaman utama
        }

        // Jika gagal login
        return redirect()->back()->with('swal_error', 'Username atau password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
