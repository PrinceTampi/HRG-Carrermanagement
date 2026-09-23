<?php $user = wp_get_current_user(); ?>
<div class="recruitment-admin">
<?php require recruitment_get_plugin_path( 'admin/components/header.php' ); ?>
<div class="admin-layout">
    <?php require recruitment_get_plugin_path( 'admin/components/sidebar.php' ); ?>
    <main class="content-shell">
        <p class="eyebrow">Authenticated area</p>
        <h1>Good to see you, <?= esc_html( $user->display_name ) ?>.</h1>
        <p class="lede">Ringkasan aktivitas rekrutmen.</p>
        <section class="stats-row">
            <?php
            // TODO: Ambil data dari Recruitment_Database
            $stats = [
                [ 'label' => 'Total Vacancies',       'value' => '—' ],
                [ 'label' => 'Total Applicants',       'value' => '—' ],
                [ 'label' => 'New Applications',       'value' => '—' ],
                [ 'label' => 'Applications in Review', 'value' => '—' ],
            ];
            foreach ( $stats as $stat ) :
                require recruitment_get_plugin_path( 'admin/components/stats-card.php' );
            endforeach;
            ?>
        </section>
    </main>
</div>
<footer><span>DAW Recruitment</span><span>HRD Dashboard</span></footer>
</div>
