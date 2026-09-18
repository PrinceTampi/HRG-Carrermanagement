<?php
/**
 * public/components/footer.php
 *
 * Footer halaman Karir (public area).
 */
?>
<footer class="daw-recruitment__footer">
    <div class="daw-recruitment__footer-grid">
        <div><div class="daw-recruitment__brand"><span class="daw-recruitment__logo">D</span><span>DAW</span></div><p>PT. Daya Adicipta Wisesa<br>Dealer resmi Honda di Indonesia Timur.</p></div>
        <div><h4>Perusahaan</h4><ul><li><a href="#">Tentang Kami</a></li><li><a href="#">Produk</a></li><li><a href="#">Layanan</a></li></ul></div>
        <div><h4>Karier</h4><ul><li><a href="<?= esc_url( recruitment_get_public_url( 'careers' ) ) ?>">Lowongan Kerja</a></li><li><a href="<?= esc_url( recruitment_get_public_url( 'tracking' ) ) ?>">Tracking Lamaran</a></li></ul></div>
        <div><h4>Kontak</h4><ul><li>recruitment@daw.co.id</li><li>0431-XXX-XXXX</li><li>Manado, Sulawesi Utara</li></ul></div>
    </div>
    <div class="daw-recruitment__footer-bottom"><span>&copy; <?= esc_html( gmdate( 'Y' ) ) ?> PT. Daya Adicipta Wisesa.</span><a href="<?= esc_url( admin_url( 'admin.php?page=recruitment-dashboard' ) ) ?>">Admin Login</a></div>
</footer>
