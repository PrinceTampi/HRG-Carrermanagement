<?php
/**
 * public/components/job-card.php
 *
 * Komponen card untuk satu lowongan kerja.
 *
 * Variabel yang diharapkan tersedia di scope:
 *   $i   (int)    — index (0-based), digunakan untuk label nomor.
 *   $job (array)  — data lowongan: title, dept, type.
 *
 * Contoh tampilan:
 *   Frontend Developer
 *   Technology
 *   Full Time
 *   [View Job]
 */
?>
<article>
    <span><?= str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ?></span>
    <h3><?= esc_html( $job['title'] ?? 'Lowongan' ) ?></h3>
    <p><?= esc_html( ( $job['dept'] ?? '' ) . ' · ' . ( $job['type'] ?? '' ) ) ?></p>
    <a class="button" href="?page=job-detail">View Job <span aria-hidden="true">-&gt;</span></a>
</article>
<?php

/**
 * Minimal esc_html() polyfill for sandbox (no WordPress).
 * Remove when running in WordPress.
 */
if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( string $text ): string {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
}
