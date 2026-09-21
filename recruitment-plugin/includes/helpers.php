<?php

/**
 * helpers.php — helper functions and WordPress mock (sandbox only).
 *
 * Berisi:
 * 1. Helper functions yang dipakai lintas module.
 * 2. Mock WordPress functions agar sandbox dapat berjalan tanpa WordPress.
 *    Hapus bagian mock ini saat dijalankan di WordPress asli.
 */

// ---------------------------------------------------------------------------
// Plugin helper functions
// ---------------------------------------------------------------------------

function recruitment_get_plugin_url( string $path = '' ): string {
    return RECRUITMENT_PLUGIN_URL . ltrim( $path, '/' );
}

function recruitment_get_plugin_path( string $path = '' ): string {
    return RECRUITMENT_PLUGIN_PATH . ltrim( $path, '/' );
}

/**
 * Get the URL to a specific public application page.
 *
 * @param string $slug E.g. 'careers', 'job-detail', 'application-form'.
 */
function recruitment_get_application_url( string $slug = 'careers', array $args = [] ): string {
    $page = get_page_by_path( 'recruitment-' . sanitize_title( $slug ) );
    $url  = $page ? get_permalink( $page ) : home_url( '/' );

    return add_query_arg( $args, $url );
}

function recruitment_get_public_url( string $screen = 'careers', array $args = [] ): string {
    $page_id = absint( get_option( 'recruitment_public_page_id', 0 ) );
    $base_url = $page_id ? get_permalink( $page_id ) : home_url( '/' );

    return add_query_arg( array_merge( [ 'recruitment_page' => sanitize_key( $screen ) ], $args ), $base_url );
}

/**
 * Return the public vacancy list used by careers, detail, and application pages.
 *
 * @return array<int, array<string, mixed>>
 */
function recruitment_get_public_jobs(): array {
    $fallback_jobs = [
        [ 'id' => '1', 'title' => 'Sales Executive', 'location' => 'Airmadidi', 'dealer' => 'DAW Airmadidi', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Bergabunglah dengan tim penjualan DAW dan jadilah bagian dari keluarga Honda yang terus berkembang.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal D3 semua jurusan', 'Memiliki kemampuan komunikasi yang baik' ] ],
        [ 'id' => '2', 'title' => 'Service Advisor', 'location' => 'Manado', 'dealer' => 'DAW Manado', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Menjadi penghubung antara pelanggan dan bengkel serta memastikan setiap kendaraan mendapatkan penanganan terbaik.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal D3 Teknik Otomotif atau terkait', 'Berpengalaman sebagai Service Advisor min. 2 tahun' ] ],
        [ 'id' => '3', 'title' => 'Kepala Bengkel / Foreman', 'location' => 'Gorontalo', 'dealer' => 'AHASS Gorontalo', 'region' => 'Gorontalo', 'type' => 'Full Time', 'description' => 'Memimpin operasional bengkel AHASS dan memastikan semua pekerjaan servis berjalan sesuai standar Honda.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal SMK Teknik Otomotif', 'Pengalaman sebagai mekanik min. 3 tahun' ] ],
        [ 'id' => '4', 'title' => 'Marketing Coordinator', 'location' => 'Ternate', 'dealer' => 'DAW Ternate', 'region' => 'Maluku Utara', 'type' => 'Full Time', 'description' => 'Mengelola kegiatan marketing dan promosi untuk meningkatkan brand awareness Honda di wilayah Maluku Utara.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal S1 Marketing / Komunikasi', 'Pengalaman di bidang marketing min. 2 tahun' ] ],
        [ 'id' => '5', 'title' => 'Finance & Accounting Staff', 'location' => 'Manado', 'dealer' => 'DAW Head Office', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Bertanggung jawab dalam pengelolaan keuangan dan laporan akuntansi perusahaan.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal S1 Akuntansi / Keuangan', 'Berpengalaman min. 1 tahun di bidang keuangan' ] ],
        [ 'id' => '6', 'title' => 'Mekanik / Teknisi Motor', 'location' => 'Kotamobagu', 'dealer' => 'AHASS Kotamobagu', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Melakukan perawatan dan perbaikan kendaraan Honda sesuai standar layanan AHASS.', 'deadline' => '30 September 2026', 'postedDate' => '1 Agustus 2026', 'requirements' => [ 'Pendidikan minimal SMK Teknik Otomotif', 'Memiliki sertifikat pelatihan Honda' ] ],
    ];

    $vacancies = get_posts( [ 'post_type' => 'daw_vacancy', 'post_status' => 'publish', 'numberposts' => -1 ] );
    if ( ! $vacancies ) {
        return $fallback_jobs;
    }

    $jobs = [];
    foreach ( $vacancies as $vacancy ) {
        $jobs[] = recruitment_map_vacancy( $vacancy );
    }

    return $jobs;
}

/**
 * @return array<string, mixed>|null
 */
function recruitment_get_public_job( int $job_id ): ?array {
    foreach ( recruitment_get_public_jobs() as $job ) {
        if ( (int) $job['id'] === $job_id ) {
            return $job;
        }
    }

    return null;
}

/**
 * @param WP_Post|object $vacancy
 * @return array<string, mixed>
 */
function recruitment_map_vacancy( $vacancy ): array {
    $deadline = (string) get_post_meta( $vacancy->ID, '_daw_deadline', true );
    return [
        'id' => (string) $vacancy->ID,
        'title' => get_the_title( $vacancy ),
        'location' => (string) get_post_meta( $vacancy->ID, '_daw_location', true ),
        'dealer' => (string) get_post_meta( $vacancy->ID, '_daw_dealer', true ),
        'region' => (string) get_post_meta( $vacancy->ID, '_daw_region', true ),
        'type' => (string) get_post_meta( $vacancy->ID, '_daw_type', true ),
        'description' => wp_trim_words( wp_strip_all_tags( $vacancy->post_content ), 24 ),
        'deadline' => $deadline ?: 'Belum ditentukan',
        'postedDate' => get_the_date( 'j F Y', $vacancy ),
        'requirements' => [ 'Lihat detail lowongan untuk persyaratan lengkap.' ],
    ];
}

/**
 * Get the URL to a specific admin page.
 *
 * @param string $slug E.g. 'dashboard', 'vacancies', 'applicants'.
 */
function recruitment_get_admin_url( string $slug = 'dashboard' ): string {
    return add_query_arg( 'page', sanitize_key( $slug ), admin_url( 'admin.php' ) );
}
