<?php
$public_url = recruitment_get_public_url( 'careers' );
$hr_login_url = recruitment_get_public_url( 'portal', [ 'recruitment_page' => 'login', 'target' => 'hr' ] );
$department_login_url = recruitment_get_public_url( 'portal', [ 'recruitment_page' => 'login', 'target' => 'user_department' ] );
?>
<main class="daw-site-portal">
	<header class="daw-site-portal__header">
		<a class="daw-site-portal__brand" href="<?= esc_url( $public_url ) ?>"><span>D</span><strong>DAW Recruitment</strong></a>
		<p>Portal layanan karier dan sistem internal PT. Daya Adicipta Wisesa.</p>
	</header>

	<div class="daw-site-portal__sections">
		<section class="daw-site-portal__public" aria-labelledby="daw-public-site-title">
			<div class="daw-site-portal__section-heading"><span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span><div><p>Portal eksternal</p><h1 id="daw-public-site-title">Public Site</h1></div></div>
			<p>Lihat lowongan kerja DAW dan informasi proses rekrutmen.</p>
			<a class="daw-site-portal__button daw-site-portal__button--light" href="<?= esc_url( $public_url ) ?>">Buka Situs Karier <span aria-hidden="true">&rarr;</span></a>
		</section>

		<section class="daw-site-portal__admin" aria-labelledby="daw-admin-site-title">
			<div class="daw-site-portal__section-heading"><span class="dashicons dashicons-lock" aria-hidden="true"></span><div><p>Portal internal</p><h2 id="daw-admin-site-title">Admin Site</h2></div></div>
			<p>Pilih area kerja. Login diperlukan sebelum membuka dashboard.</p>
			<div class="daw-site-portal__admin-options">
				<a href="<?= esc_url( $hr_login_url ) ?>"><span class="dashicons dashicons-groups" aria-hidden="true"></span><span><strong>Admin HR</strong><small>Manajemen rekrutmen</small></span><span class="daw-site-portal__arrow" aria-hidden="true">&rarr;</span></a>
				<a href="<?= esc_url( $department_login_url ) ?>"><span class="dashicons dashicons-building" aria-hidden="true"></span><span><strong>Admin User Department</strong><small>Request lowongan dan wawancara user</small></span><span class="daw-site-portal__arrow" aria-hidden="true">&rarr;</span></a>
			</div>
		</section>
	</div>
	<footer class="daw-site-portal__footer">&copy; <?= esc_html( gmdate( 'Y' ) ) ?> PT. Daya Adicipta Wisesa</footer>
</main>