<?php
/**
 * admin/pages/vacancies.php
 *
 * Manajemen lowongan kerja.
 *
 * URL: /recruitment-admin/vacancies/
 *
 * Fungsi yang dapat dikembangkan:
 * - Create Vacancy
 * - Read Vacancy
 * - Update Vacancy
 * - Delete/Archive Vacancy
 *
 * TODO: Implementasi CRUD menggunakan Recruitment_Database.
 */
?>
<div class="wrap recruitment-admin">
    <h1>Lowongan</h1>
    <p>Data lowongan dikelola melalui post type WordPress.</p>
    <p><a class="button button-primary" href="<?= esc_url( recruitment_get_admin_url( 'vacancies' ) ) ?>">Buka daftar lowongan</a></p>
</div>
