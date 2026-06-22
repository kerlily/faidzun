<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Filters\AuthFilter;
use App\Filters\RoleFilter;

class Filters extends BaseConfig
{
    /**
     * Daftar semua filter yang tersedia.
     * Key = nama alias yang dipakai di Routes.php
     */
    public array $aliases = [
        'csrf'     => \CodeIgniter\Filters\CSRF::class,
        'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot' => \CodeIgniter\Filters\Honeypot::class,
        'auth'     => AuthFilter::class,
        'role'     => RoleFilter::class,
    ];

    /**
     * Filter yang selalu jalan di setiap request (global).
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf',
        ],
        'after' => [
            'toolbar',
        ],
    ];

    /**
     * Filter yang jalan berdasarkan HTTP method.
     */
    public array $methods = [];

    /**
     * Filter yang jalan berdasarkan pattern URL.
     * 
     * Semua route kecuali '/' dan '/login/*' wajib sudah login.
     */
    public array $filters = [
        'auth' => [
            'before' => [
                'dashboard',
                'dashboard/*',
                'guru',
                'guru/*',
                'siswa',
                'siswa/*',
                'kelas',
                'kelas/*',
                'mapel',
                'mapel/*',
                'jadwal-mengajar',
                'jadwal-mengajar/*',
                'penugasan',
                'penugasan/*',
                'absensi',
                'absensi/*',
                'tugas-saya',
                'riwayat-absensi',
                'riwayat-absensi/*',
                'profil',
                'profil/*',
                'panduan',
            ],
        ],
    ];
}