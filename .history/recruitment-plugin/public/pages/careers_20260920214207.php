<?php

if ( ! defined( 'ABSPATH' ) ) {
    http_response_code( 403 );
    exit;
}

$fallback_jobs = [
    [ 'id' => '1', 'title' => 'Sales Executive', 'location' => 'Airmadidi', 'dealer' => 'DAW Airmadidi', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Bergabunglah dengan tim penjualan DAW dan jadilah bagian dari keluarga Honda yang terus berkembang.', 'requirements' => [ 'Pendidikan minimal D3 semua jurusan', 'Memiliki kemampuan komunikasi yang baik' ] ],
    [ 'id' => '2', 'title' => 'Service Advisor', 'location' => 'Manado', 'dealer' => 'DAW Manado', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Menjadi penghubung antara pelanggan dan bengkel serta memastikan setiap kendaraan mendapatkan penanganan terbaik.', 'requirements' => [ 'Pendidikan minimal D3 Teknik Otomotif atau terkait', 'Berpengalaman sebagai Service Advisor min. 2 tahun' ] ],
    [ 'id' => '3', 'title' => 'Kepala Bengkel / Foreman', 'location' => 'Gorontalo', 'dealer' => 'AHASS Gorontalo', 'region' => 'Gorontalo', 'type' => 'Full Time', 'description' => 'Memimpin operasional bengkel AHASS dan memastikan semua pekerjaan servis berjalan sesuai standar Honda.', 'requirements' => [ 'Pendidikan minimal SMK Teknik Otomotif', 'Pengalaman sebagai mekanik min. 3 tahun' ] ],
    [ 'id' => '4', 'title' => 'Marketing Coordinator', 'location' => 'Ternate', 'dealer' => 'DAW Ternate', 'region' => 'Maluku Utara', 'type' => 'Full Time', 'description' => 'Mengelola kegiatan marketing dan promosi untuk meningkatkan brand awareness Honda di wilayah Maluku Utara.', 'requirements' => [ 'Pendidikan minimal S1 Marketing / Komunikasi', 'Pengalaman di bidang marketing min. 2 tahun' ] ],
    [ 'id' => '5', 'title' => 'Finance & Accounting Staff', 'location' => 'Manado', 'dealer' => 'DAW Head Office', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Bertanggung jawab dalam pengelolaan keuangan dan laporan akuntansi perusahaan.', 'requirements' => [ 'Pendidikan minimal S1 Akuntansi / Keuangan', 'Berpengalaman min. 1 tahun di bidang keuangan' ] ],
    [ 'id' => '6', 'title' => 'Mekanik / Teknisi Motor', 'location' => 'Kotamobagu', 'dealer' => 'AHASS Kotamobagu', 'region' => 'Sulawesi Utara', 'type' => 'Full Time', 'description' => 'Melakukan perawatan dan perbaikan kendaraan Honda sesuai standar layanan AHASS.', 'requirements' => [ 'Pendidikan minimal SMK Teknik Otomotif', 'Memiliki sertifikat pelatihan Honda' ] ],
];

$jobs = [];
$vacancies = get_posts( [ 'post_type' => 'daw_vacancy', 'post_status' => 'publish', 'numberposts' => -1 ] );
foreach ( $vacancies as $vacancy ) {
    $jobs[] = [
        'id' => (string) $vacancy->ID,
        'title' => get_the_title( $vacancy ),
        'location' => (string) get_post_meta( $vacancy->ID, '_daw_location', true ),
        'dealer' => (string) get_post_meta( $vacancy->ID, '_daw_dealer', true ),
        'type' => (string) get_post_meta( $vacancy->ID, '_daw_type', true ),
        'description' => wp_trim_words( wp_strip_all_tags( $vacancy->post_content ), 24 ),
        'requirements' => [ 'Lihat detail lowongan untuk persyaratan lengkap.' ],
    ];
}

$jobs = $jobs ?: $fallback_jobs;
$dealers = [ 'DAW Bitung', 'DAW Main Dealer Maumbi' ];
$types = [
    'Sales',
    'Marketing',
    'Administration',
    'Human Capital / HR',
    'Finance & Accounting',
    'Information Technology',
    'Parts',
    'Technical Service',
    'Customer Care / HC3',
    'Warehouse / Logistics',
    'Management Trainee',
    'Other',
];
$search = sanitize_text_field( wp_unslash( $_GET['job_search'] ?? '' ) );
$dealer = sanitize_text_field( wp_unslash( $_GET['job_dealer'] ?? '' ) );
$type = sanitize_text_field( wp_unslash( $_GET['job_type'] ?? '' ) );
$jobs = array_values( array_filter( $jobs, static function ( array $job ) use ( $search, $dealer, $type ): bool {
    return ( '' === $search || false !== stripos( $job['title'], $search ) )
        && ( '' === $dealer || $job['dealer'] === $dealer )
        && ( '' === $type || $job['type'] === $type );
} ) );
?>
<div class="daw-recruitment">
    <?php require recruitment_get_plugin_path( 'components/public/header.php' ); ?>
    <section class="daw-recruitment__hero">
        <div class="daw-recruitment__hero-placeholder" aria-label="Placeholder gambar workplace DAW">DAW workplace image</div>
        <div class="daw-recruitment__hero-inner">
            <span class="daw-recruitment__eyebrow">Lowongan tersedia</span>
            <h1>Bangun Kariermu<br><span>Bersama DAW</span></h1>
            <p>Temukan peluang karier dan berkembang bersama PT. Daya Adicipta Wisesa, dealer resmi Honda di Indonesia Timur.</p>
            <div class="daw-recruitment__hero-actions"><a class="daw-recruitment__button" href="#daw-vacancies">Lihat Lowongan</a><a class="daw-recruitment__button daw-recruitment__button--outline" href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Tracking Lamaran</a></div>
            <div class="daw-recruitment__stats"><div class="daw-recruitment__stat"><strong><?= esc_html( count( $jobs ) ) ?></strong><span>Lowongan Aktif</span></div><div class="daw-recruitment__stat"><strong>3</strong><span>Wilayah</span></div><div class="daw-recruitment__stat"><strong>12+</strong><span>Dealer &amp; AHASS</span></div><div class="daw-recruitment__stat"><strong>500+</strong><span>Karyawan</span></div></div>
        </div>
    </section>
    <section class="daw-recruitment__section daw-recruitment__section--light"><div class="daw-recruitment__container">
        <div class="daw-recruitment__section-title"><h2>Mengapa Bergabung dengan DAW?</h2><p>Kami percaya bahwa karyawan adalah aset terbesar perusahaan.</p></div>
        <div class="daw-recruitment__benefits"><?php foreach ( [ [ '📈', 'Pengembangan Karier', 'Jalur karier yang jelas dengan program pengembangan terstruktur.' ], [ '🤝', 'Lingkungan Profesional', 'Budaya kerja yang kolaboratif dan berorientasi pada pertumbuhan.' ], [ '🎓', 'Kesempatan Belajar', 'Pelatihan rutin dan program mentoring dari para profesional.' ], [ '🏆', 'Berkembang Bersama Tim', 'Reward berbasis kinerja dan pengakuan atas kontribusi.' ] ] as $benefit ) : ?><article class="daw-recruitment__benefit"><div class="daw-recruitment__benefit-icon"><?= esc_html( $benefit[0] ) ?></div><h3><?= esc_html( $benefit[1] ) ?></h3><p><?= esc_html( $benefit[2] ) ?></p></article><?php endforeach; ?></div>
    </div></section>
    <section class="daw-recruitment__section" id="daw-vacancies"><div class="daw-recruitment__container">
        <div class="daw-recruitment__section-title"><h2>Temukan Posisi yang Tepat untukmu</h2><p>Jelajahi berbagai kesempatan kerja yang tersedia di jaringan DAW.</p></div>
        <form class="daw-recruitment__search" method="get"><input name="job_search" value="<?= esc_attr( $search ) ?>" placeholder="Cari posisi atau kata kunci..."><button class="daw-recruitment__button" type="submit">Cari</button></form>
        <form class="daw-recruitment__filters" method="get"><div><label for="job-dealer">Kategori DAW Dealer</label><select id="job-dealer" name="job_dealer"><option value="">Semua Dealer</option><?php foreach ( $dealers as $item ) : ?><option value="<?= esc_attr( $item ) ?>" <?= selected( $dealer, $item, false ) ?>><?= esc_html( $item ) ?></option><?php endforeach; ?></select></div><div><label for="job-type">Jenis Pekerjaan</label><select id="job-type" name="job_type"><option value="">Semua Posisi</option><?php foreach ( $types as $item ) : ?><option value="<?= esc_attr( $item ) ?>" <?= selected( $type, $item, false ) ?>><?= esc_html( $item ) ?></option><?php endforeach; ?></select></div><div class="daw-recruitment__filter-actions"><button class="daw-recruitment__button" type="submit">Cari Lowongan</button><a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Reset Filter</a></div></form>
        <p class="daw-recruitment__result-count"><strong><?= esc_html( count( $jobs ) ) ?></strong> Lowongan tersedia</p>
        <div class="daw-recruitment__job-grid"><?php if ( ! $jobs ) : ?><div class="daw-recruitment__panel"><strong>Tidak ada lowongan yang sesuai.</strong><p>Belum ada posisi yang sesuai dengan filter yang dipilih.</p></div><?php endif; ?><?php foreach ( $jobs as $i => $job ) : require recruitment_get_plugin_path( 'components/public/job-card.php' ); endforeach; ?></div>
    </div></section>
    <?php require recruitment_get_plugin_path( 'components/public/footer.php' ); ?>
</div>
