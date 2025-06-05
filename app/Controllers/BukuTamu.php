<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TamuModel;
use CodeIgniter\HTTP\ResponseInterface;

class BukuTamu extends BaseController
{
    protected $tamuModel;
    protected $helpers = ['form', 'url']; // Load helpers

    public function __construct()
    {
        $this->tamuModel = new TamuModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Form Input Buku Tamu'
        ];
        return view('bukutamu/form', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules(
            [
                'nama' => 'required|min_length[3]', //  minimal 3 karakter
                'email' => 'permit_empty|valid_email', // email valid jika diisi
                'pesan' => 'required|min_length[10]' // minimal 10 karakter
            ],
            [   // error messages
                'nama' => [
                    'required' => 'Nama wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.'
                ],
                'email' => [
                    'valid_email' => 'Format email tidak valid.'
                ],
                'pesan' => [
                    'required' => 'Pesan wajib diisi.',
                    'min_length' => 'Pesan minimal 10 karakter.'
                ]
            ]
        );

        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $tamu = new TamuModel();
            try {
                $tamu->insert([
                    "nama" => $this->request->getPost('nama'),
                    "email" => $this->request->getPost('email'),
                    "pesan" => $this->request->getPost('pesan'),
                ]);

                // Set flashdata untuk pesan sukses
                session()->setFlashdata('success', 'Data tamu berhasil ditambahkan!');
                return redirect()->back(); // Ganti dengan route/URL form Anda
                // Atau jika Anda ingin tetap di halaman yang sama tanpa input lama:
                // return redirect()->back();

            } catch (\Exception $e) {
                // Set flashdata untuk pesan error jika terjadi kesalahan database
                session()->setFlashdata('error', 'Gagal menambahkan data tamu: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        } else {
            // Set flashdata untuk pesan error validasi
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }
    }

    // Halaman admin untuk melihat daftar pesan
    public function admin()
    {
        if (!session()->get('is_admin')) {

            return redirect()->to(base_url('admin/access_denied'));
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Mengambil data dengan filter tanggal
        if (!empty($startDate) && !empty($endDate)) {
            // 
            $tamu = $this->tamuModel
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->orderBy('created_at', 'DESC')
                ->findAll();
        } else {
            $tamu = $this->tamuModel->orderBy('created_at', 'DESC')->findAll();
        }

        $data = [
            'guests'    => $tamu,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];

        return view('bukutamu/adminList', $data);
    }

    public function setAdmin()
    {
        session()->set('is_admin', true);
        session()->setFlashdata('success', 'Mode Admin Berhasil Diaktifkan! Anda sekarang bisa mengakses daftar pesan.');

        return redirect()->to(base_url('/admin'));
    }

    public function unsetAdmin()
    {
        session()->remove('is_admin');
        session()->setFlashdata('info', 'Mode Admin Dinonaktifkan.');

        return redirect()->to(base_url('/'));
    }

    public function edit($id = null)
    {

        if (!session()->get('is_admin')) {
            session()->setFlashdata('error', 'Akses ditolak.');
            return redirect()->to(base_url('/'));
        }

        $tamu = $this->tamuModel->find($id);

        if (!$tamu) {
            session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
            return redirect()->to(base_url('admin/list'));
        }

        $data = [
            'title' => 'Edit Tamu',
            'tamu'  => $tamu,
            // 'validation' => session('validation') // Ini akan dihandle oleh session()->getFlashdata('errors') di view
        ];

        return view('bukutamu/edit_form', $data);
    }

    // Metode untuk memproses UPDATE
    // public function update($id)
    // {
    //     if (!session()->get('is_admin')) {
    //         session()->setFlashdata('error', 'Akses ditolak.');
    //         return redirect()->to(base_url('/'));
    //     }

    //     // Pastikan ID ada
    //     if ($id === null) {
    //         session()->setFlashdata('error', 'ID tamu tidak ditemukan untuk pembaruan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     $tamu = $this->tamuModel->find($id);

    //     if (!$tamu) {
    //         session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     // Metode harus POST untuk update
    //     if ($this->request->getMethod() === 'post') {
    //         $validation = \Config\Services::validation();
    //         $validation->setRules(
    //             [
    //                 'nama' => 'required|min_length[3]',
    //                 'email' => 'permit_empty|valid_email',
    //                 'pesan' => 'required|min_length[10]',
    //             ],
    //             [
    //                 'nama' => [
    //                     'required' => 'Nama wajib diisi.',
    //                     'min_length' => 'Nama minimal 3 karakter.'
    //                 ],
    //                 'email' => [
    //                     'valid_email' => 'Format email tidak valid.'
    //                 ],
    //                 'pesan' => [
    //                     'required' => 'Pesan wajib diisi.',
    //                     'min_length' => 'Pesan minimal 10 karakter.'
    //                 ]
    //             ]
    //         );

    //         if ($validation->withRequest($this->request)->run()) {
    //             try {
    //                 $this->tamuModel->update($id, [
    //                     "nama" => $this->request->getPost('nama'),
    //                     "email" => $this->request->getPost('email'),
    //                     "pesan" => $this->request->getPost('pesan'),
    //                 ]);
    //                 session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
    //                 return redirect()->to(base_url('/admin')); // Redirect ke daftar admin setelah berhasil
    //             } catch (\Exception $e) {
    //                 session()->setFlashdata('error', 'Gagal memperbarui data: ' . $e->getMessage());
    //                 return redirect()->back()->withInput();
    //             }
    //         } else {
    //             session()->setFlashdata('errors', $validation->getErrors()); // Ubah 'validation' menjadi 'errors'
    //             return redirect()->back()->withInput(); // Kembali ke form edit dengan input lama
    //         }
    //     }
    //     // Jika request bukan POST (misal GET langsung ke /tamu/update/id), redirect ke form edit
    //     return redirect()->to(base_url('admin/edit/' . $id));
    // }

    // public function update($id)
    // {

    //     $tamu = $this->tamuModel->find($id);

    //     if (!$tamu) {
    //         session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
    //         return redirect()->to('/admin');
    //     }

    //     if ($this->request->getMethod() === 'post') {
    //         $validation = \Config\Services::validation();
    //         $validation->setRules(
    //             [
    //                 'nama' => 'required|min_length[3]',
    //                 'email' => 'permit_empty|valid_email',
    //                 'pesan' => 'required|min_length[10]',
    //             ],
    //             [
    //                 'nama' => [
    //                     'required' => 'Nama wajib diisi.',
    //                     'min_length' => 'Nama minimal 3 karakter.'
    //                 ],
    //                 'email' => [
    //                     'valid_email' => 'Format email tidak valid.'
    //                 ],
    //                 'pesan' => [
    //                     'required' => 'Pesan wajib diisi.',
    //                     'min_length' => 'Pesan minimal 10 karakter.'
    //                 ]
    //             ]
    //         );

    //         if ($validation->withRequest($this->request)->run()) {
    //             try {
    //                 $this->tamuModel->update($id, [
    //                     "nama" => $this->request->getPost('nama'),
    //                     "email" => $this->request->getPost('email'),
    //                     "pesan" => $this->request->getPost('pesan'),
    //                     "tanggal" => $this->request->getPost('tanggal') ?? date('Y-m-d'),
    //                 ]);
    //                 session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
    //                 return redirect()->to('/admin');
    //             } catch (\Exception $e) {
    //                 session()->setFlashdata('error', 'Gagal memperbarui data: ' . $e->getMessage());
    //                 return redirect()->back()->withInput();
    //             }
    //         } else {
    //             session()->setFlashdata('validation', $validation);
    //             return redirect()->back()->withInput();
    //         }
    //     }

    //     return view('bukutamu/edit_form', [
    //         'title' => 'Edit Tamu',
    //         'action' => base_url("/tamu/update/$id"),
    //         'tamu' => $tamu,
    //         'validation' => session('validation')
    //     ]);
    // }

    // Metode untuk memproses UPDATE
    // public function update($id)
    // {
    //     if (!session()->get('is_admin')) {
    //         session()->setFlashdata('error', 'Akses ditolak.');
    //         return redirect()->to(base_url('/'));
    //     }

    //     // Pastikan ID ada
    //     if ($id === null) {
    //         session()->setFlashdata('error', 'ID tamu tidak ditemukan untuk pembaruan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     $tamu = $this->tamuModel->find($id);

    //     if (!$tamu) {
    //         session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     // Metode harus POST untuk update
    //     if ($this->request->getMethod() === 'post') {
    //         $validation = \Config\Services::validation();
    //         $validation->setRules(
    //             [
    //                 'nama' => 'required|min_length[3]',
    //                 'email' => 'permit_empty|valid_email',
    //                 'pesan' => 'required|min_length[10]',
    //             ],
    //             [
    //                 'nama' => [
    //                     'required' => 'Nama wajib diisi.',
    //                     'min_length' => 'Nama minimal 3 karakter.'
    //                 ],
    //                 'email' => [
    //                     'valid_email' => 'Format email tidak valid.'
    //                 ],
    //                 'pesan' => [
    //                     'required' => 'Pesan wajib diisi.',
    //                     'min_length' => 'Pesan minimal 10 karakter.'
    //                 ]
    //             ]
    //         );

    //         if ($validation->withRequest($this->request)->run()) {
    //             try {
    //                 // Debug: Log data yang akan diupdate
    //                 log_message('info', 'Updating data for ID ' . $id . ': ' . json_encode([
    //                     "nama" => $this->request->getPost('nama'),
    //                     "email" => $this->request->getPost('email'),
    //                     "pesan" => $this->request->getPost('pesan'),
    //                 ]));

    //                 $updateResult = $this->tamuModel->update($id, [
    //                     "nama" => $this->request->getPost('nama'),
    //                     "email" => $this->request->getPost('email'),
    //                     "pesan" => $this->request->getPost('pesan'),
    //                 ]);

    //                 // Debug: Cek hasil update
    //                 if ($updateResult === false) {
    //                     $errors = $this->tamuModel->errors();
    //                     log_message('error', 'Model update failed: ' . json_encode($errors));
    //                     session()->setFlashdata('error', 'Update gagal: ' . implode(', ', $errors));
    //                     return redirect()->back()->withInput();
    //                 }

    //                 log_message('info', 'Update successful for ID ' . $id);
    //                 session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
    //                 return redirect()->to(base_url('/admin'));
    //             } catch (\Exception $e) {
    //                 log_message('error', 'Exception during update: ' . $e->getMessage());
    //                 session()->setFlashdata('error', 'Gagal memperbarui data: ' . $e->getMessage());
    //                 return redirect()->back()->withInput();
    //             }
    //         } else {
    //             // Ganti 'errors' menjadi 'validation' untuk konsistensi dengan view
    //             session()->setFlashdata('validation', $validation);
    //             return redirect()->back()->withInput();
    //         }
    //     }
    //     // Jika request bukan POST (misal GET langsung ke /tamu/update/id), redirect ke form edit
    //     return redirect()->to(base_url('admin/edit/' . $id));
    // }

    // Metode untuk memproses UPDATE dengan debugging yang lebih detail
    // Metode untuk memproses UPDATE dengan debug lengkap
    // public function update($id)
    // {
    //     if (!session()->get('is_admin')) {
    //         session()->setFlashdata('error', 'Akses ditolak. Silakan masukkan kunci admin.');
    //         return redirect()->to(base_url('/'));
    //     }

    //     if ($id === null) {
    //         session()->setFlashdata('error', 'ID tamu tidak ditemukan untuk pembaruan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     $tamu = $this->tamuModel->find($id);
    //     if (!$tamu) {
    //         session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
    //         return redirect()->to(base_url('/admin'));
    //     }

    //     if ($this->request->getMethod() === 'post') {
    //         $validation = \Config\Services::validation();
    //         $validation->setRules([
    //             'nama' => 'required|min_length[3]',
    //             'email' => 'permit_empty|valid_email',
    //             'pesan' => 'required|min_length[10]',
    //         ]);

    //         if ($validation->withRequest($this->request)->run()) {
    //             try {
    //                 // Method 1: Bypass model validation
    //                 $updateData = [
    //                     'nama' => $this->request->getPost('nama'),
    //                     'email' => $this->request->getPost('email'),
    //                     'pesan' => $this->request->getPost('pesan'),
    //                     'updated_at' => date('Y-m-d H:i:s') // Manual timestamp jika needed
    //                 ];

    //                 $result = $this->tamuModel->update($id, $updateData, false); // false = skip validation

    //                 // Method 2: Jika method 1 gagal, gunakan query builder langsung
    //                 if (!$result) {
    //                     $db = \Config\Database::connect();
    //                     $result = $db->table('tamu')
    //                         ->where('id', $id)
    //                         ->update($updateData);
    //                 }

    //                 if ($result) {
    //                     session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
    //                     return redirect()->to(base_url('/admin'));
    //                 } else {
    //                     session()->setFlashdata('error', 'Gagal memperbarui data.');
    //                     return redirect()->back()->withInput();
    //                 }
    //             } catch (\Exception $e) {
    //                 session()->setFlashdata('error', 'Error: ' . $e->getMessage());
    //                 return redirect()->back()->withInput();
    //             }
    //         } else {
    //             session()->setFlashdata('errors', $validation->getErrors());
    //             return redirect()->back()->withInput();
    //         }
    //     }

    //     return redirect()->to(base_url('admin/edit/' . $id));
    // }

    // NO Validation
    // BukuTamu.php Controller@update
    // public function update($id)
    // {
    //     // ... kode akses admin dan find tamu ...

    //     if ($this->request->getMethod() === 'post') {
    //         // Hapus atau komen bagian validasi yang ini:
    //         /*
    //     $validation = \Config\Services::validation();
    //     $validation->setRules(
    //         [
    //             'nama' => 'required|min_length[3]',
    //             'email' => 'permit_empty|valid_email',
    //             'pesan' => 'required|min_length[10]',
    //         ],
    //         [
    //             // ... pesan validasi ...
    //         ]
    //     );
    //     if ($validation->withRequest($this->request)->run()) {
    //     */

    //         // Ganti dengan ini (memanggil validasi dari model)
    //         if (!$this->tamuModel->validate($this->request->getPost())) {
    //             session()->setFlashdata('errors', $this->tamuModel->errors());
    //             return redirect()->back()->withInput();
    //         }

    //         try {
    //             $updated = $this->tamuModel->update($id, [
    //                 "nama" => $this->request->getPost('nama'),
    //                 "email" => $this->request->getPost('email'),
    //                 "pesan" => $this->request->getPost('pesan'),
    //             ]);

    //             if ($updated) {
    //                 session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
    //                 return redirect()->to(base_url('/admin'));
    //             } else {
    //                 // Jika update() mengembalikan false, kemungkinan validasi model gagal
    //                 $errors = $this->tamuModel->errors(); // Dapatkan pesan kesalahan validasi dari model
    //                 if (!empty($errors)) {
    //                     session()->setFlashdata('error', 'Gagal memperbarui data: ' . implode(', ', $errors));
    //                     // Atau set flashdata 'errors' untuk ditampilkan di form edit lagi
    //                     session()->setFlashdata('errors', $errors);
    //                     return redirect()->back()->withInput();
    //                 } else {
    //                     // Jika update() mengembalikan false tapi tidak ada error validasi model,
    //                     // mungkin ada masalah lain (misal: tidak ada perubahan data)
    //                     session()->setFlashdata('error', 'Gagal memperbarui data. Tidak ada perubahan yang terdeteksi atau masalah internal.');
    //                     return redirect()->back()->withInput();
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             session()->setFlashdata('error', 'Gagal memperbarui data (Exception): ' . $e->getMessage());
    //             return redirect()->back()->withInput();
    //         }
    //     }
    //     return redirect()->to(base_url('admin/edit/' . $id));
    // }

    public function update($id)
    {
        $tamu = $this->tamuModel->where('id', $id)->first();

        // lakukan validasi data artikel
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'id' => 'required',
        ]);
        $isDataValid = $validation->withRequest($this->request)->run();

        // jika data valid, maka simpan ke database
        if ($isDataValid) {
            $dataToUpdate = [
                "nama"  => $this->request->getPost('nama'),
                "email" => $this->request->getPost('email'),
                "pesan" => $this->request->getPost('pesan')
            ];
            $this->tamuModel->update($id, $dataToUpdate); // <--- Correct way to call update

            session()->setFlashdata('success', 'Data tamu berhasil diperbarui.');
            return redirect()->to(base_url('/admin'));
        } else {
            session()->setFlashdata('errors', $validation->getErrors());
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        // Pastikan hanya admin yang bisa mengakses
        if (!session()->get('is_admin')) {
            session()->setFlashdata('error', 'Akses ditolak. Anda bukan admin.');
            return redirect()->to(base_url('/'));
        }

        $tamu = $this->tamuModel->find($id);

        if (!$tamu) {
            session()->setFlashdata('error', 'Data tamu tidak ditemukan.');
        } else {
            try {
                $this->tamuModel->delete($id);
                session()->setFlashdata('success', 'Data tamu berhasil dihapus.');
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Gagal menghapus data: ' . $e->getMessage());
            }
        }

        return redirect()->to('/admin');
    }




    public function exportCsv()
    {
        // Pengecekan sesi admin sebelum mengizinkan export
        if (!session()->get('is_admin')) {
            session()->setFlashdata('error', 'Akses ditolak. Anda bukan admin.');
            return redirect()->to(base_url('admin/access_denied'));
        }

        // Ambil filter tanggal dari GET
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        // Siapkan query
        $query = $this->tamuModel->orderBy('created_at', 'ASC');

        if (!empty($startDate) && !empty($endDate)) {
            $query->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59');
        }

        $guests = $query->findAll();

        // Atur response untuk CSV
        $filename = 'data_buku_tamu_' . date('Ymd_His') . '.csv';
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        ob_start();
        $output = fopen('php://output', 'w');

        // Header CSV
        fputcsv($output, ['ID', 'Nama', 'Email', 'Pesan', 'Tanggal Submit']);

        foreach ($guests as $guest) {
            fputcsv($output, [
                $guest['id'],
                $guest['nama'],
                $guest['email'],
                $guest['pesan'],
                $guest['created_at']
            ]);
        }

        fclose($output);
        $csvContent = ob_get_clean();
        $response->setBody($csvContent);
        return $response;
    }
}
