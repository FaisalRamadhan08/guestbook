<?php

namespace App\Models;

use CodeIgniter\Model;

class TamuModel extends Model
{
    protected $table            = 'tamu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'email', 'pesan'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nama'    => 'required|alpha_space|min_length[3]|max_length[255]',
        'email'   => 'required|valid_email|max_length[255]',
        'pesan' => 'required|max_length[1000]',
    ];
    protected $validationMessages   = [
        'nama' => [
            'required'    => 'Nama wajib diisi.',
            'alpha_space' => 'Nama hanya boleh mengandung huruf dan spasi.',
            'min_length'  => 'Nama minimal {param} karakter.',
            'max_length'  => 'Nama maksimal {param} karakter.',
        ],
        'email' => [
            'required'    => 'Email wajib diisi.',
            'valid_email' => 'Format email tidak valid.',
            'max_length'  => 'Email maksimal {param} karakter.',
        ],
        'pesan' => [
            'required'    => 'Pesan wajib diisi.',
            'max_length'  => 'Pesan maksimal {param} karakter.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
