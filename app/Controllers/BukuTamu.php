<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TamuModel;
use CodeIgniter\HTTP\ResponseInterface;

class BukuTamu extends BaseController
{
    protected $tamuModel;
    protected $helpers = ['form', 'url'];

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
                'nama' => 'required|min_length[3]',
                'email' => 'permit_empty|valid_email',
                'pesan' => 'required|min_length[10]'
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
                return redirect()->back();
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

        // Mengambil data dengan berdasarkan tanggal
        if (!empty($startDate) && !empty($endDate)) {
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
