<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * FixCircularForeignKeys
 * 
 * MASALAH SEBELUMNYA:
 * Ada circular dependency antara tabel users dan kelas:
 *   - kelas.id_user  → users.id_user  (wali kelas)
 *   - users.id_kelas → kelas.id_kelas (kelas siswa)
 * 
 * Ini menyebabkan konflik ON DELETE CASCADE — jika user dihapus,
 * kelas terhapus, lalu semua user di kelas itu terhapus juga (infinite loop).
 * 
 * SOLUSI:
 * - kelas.id_user  → users.id_user  : ON DELETE SET NULL (wali kelas boleh kosong)
 * - users.id_kelas → kelas.id_kelas : ON DELETE SET NULL (siswa boleh tidak punya kelas)
 * 
 * Hapus semua FK lama yang konflik, buat ulang dengan benar.
 */
class FixCircularForeignKeys extends Migration
{
    public function up()
    {
        // Nonaktifkan sementara FK check agar bisa drop
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // ============================================================
        // Hapus FK lama yang mungkin duplikat/salah
        // (ignore error jika FK tidak ada)
        // ============================================================
        $fkToDropUsers = [
            'fk_id_kelas',          // dari migration lama
            'users_id_kelas_foreign', // dari migration lama lainnya
        ];
        foreach ($fkToDropUsers as $fk) {
            try {
                $this->db->query("ALTER TABLE `users` DROP FOREIGN KEY `{$fk}`");
            } catch (\Exception $e) {
                // FK tidak ada, lanjut
            }
        }

        $fkToDropKelas = [
            'kelas_id_user_foreign',
        ];
        foreach ($fkToDropKelas as $fk) {
            try {
                $this->db->query("ALTER TABLE `kelas` DROP FOREIGN KEY `{$fk}`");
            } catch (\Exception $e) {
                // FK tidak ada, lanjut
            }
        }

        // ============================================================
        // Pastikan kolom nullable (agar SET NULL bisa bekerja)
        // ============================================================
        $this->db->query("ALTER TABLE `users` MODIFY `id_kelas` INT UNSIGNED NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `kelas` MODIFY `id_user` INT UNSIGNED NULL DEFAULT NULL");

        // ============================================================
        // Buat FK yang benar
        // ============================================================

        // kelas.id_user → users.id_user
        // Jika user (wali kelas) dihapus → kelas.id_user jadi NULL
        $this->db->query("
            ALTER TABLE `kelas`
            ADD CONSTRAINT `fk_kelas_wali`
            FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");

        // users.id_kelas → kelas.id_kelas
        // Jika kelas dihapus → semua siswa di kelas itu: id_kelas jadi NULL
        $this->db->query("
            ALTER TABLE `users`
            ADD CONSTRAINT `fk_users_kelas`
            FOREIGN KEY (`id_kelas`) REFERENCES `kelas`(`id_kelas`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");

        // Aktifkan kembali FK check
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        try { $this->db->query("ALTER TABLE `kelas` DROP FOREIGN KEY `fk_kelas_wali`"); } catch (\Exception $e) {}
        try { $this->db->query("ALTER TABLE `users` DROP FOREIGN KEY `fk_users_kelas`"); } catch (\Exception $e) {}

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}