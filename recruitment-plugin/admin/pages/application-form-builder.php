<?php
$form_types = [
    'short'         => 'Jawaban Singkat',
    'paragraph'     => 'Paragraf / Jawaban Panjang',
    'number'        => 'Angka',
    'date'          => 'Tanggal',
    'email'         => 'Email',
    'phone'         => 'Nomor Telepon',
    'file'          => 'Upload File',
    'dropdown'      => 'Pilihan Dropdown',
    'single_choice' => 'Pilihan Satu Jawaban',
];

$default_sections = [
    [
        'title' => 'Identitas',
        'description' => 'Informasi pribadi pelamar',
        'questions' => [
            [ 'key' => 'foto_terkini', 'title' => 'Foto Terkini', 'type' => 'file', 'required' => true, 'options' => [] ],
            [ 'key' => 'nama_lengkap', 'title' => 'Nama Lengkap', 'type' => 'short', 'required' => true, 'options' => [] ],
            [ 'key' => 'jenis_kelamin', 'title' => 'Jenis Kelamin', 'type' => 'single_choice', 'required' => true, 'options' => [ 'Laki-laki', 'Perempuan' ] ],
            [ 'key' => 'tempat_lahir', 'title' => 'Tempat Lahir', 'type' => 'short', 'required' => true, 'options' => [] ],
            [ 'key' => 'tanggal_lahir', 'title' => 'Tanggal Lahir', 'type' => 'date', 'required' => true, 'options' => [] ],
            [ 'key' => 'alamat_saat_ini', 'title' => 'Alamat Saat Ini', 'type' => 'paragraph', 'required' => true, 'options' => [] ],
            [ 'key' => 'nomor_telepon', 'title' => 'Nomor Telepon / WhatsApp', 'type' => 'phone', 'required' => true, 'options' => [] ],
            [ 'key' => 'email_aktif', 'title' => 'Email Aktif', 'type' => 'email', 'required' => true, 'options' => [] ],
            [ 'key' => 'nomor_ktp', 'title' => 'Nomor KTP', 'type' => 'short', 'required' => true, 'options' => [] ],
        ],
    ],
    [
        'title' => 'Pendidikan',
        'description' => 'Riwayat pendidikan pelamar',
        'questions' => [
            [ 'key' => 'pendidikan_terakhir', 'title' => 'Pendidikan Terakhir', 'type' => 'dropdown', 'required' => true, 'options' => [ 'SMA / SMK', 'Diploma (D1/D2/D3)', 'S1 (Sarjana)', 'S2 (Magister)' ] ],
            [ 'key' => 'nama_institusi', 'title' => 'Nama Institusi', 'type' => 'short', 'required' => true, 'options' => [] ],
            [ 'key' => 'jurusan_program_studi', 'title' => 'Jurusan / Program Studi', 'type' => 'short', 'required' => false, 'options' => [] ],
            [ 'key' => 'ipk', 'title' => 'IPK', 'type' => 'number', 'required' => false, 'options' => [] ],
            [ 'key' => 'tahun_lulus', 'title' => 'Tahun Lulus', 'type' => 'number', 'required' => true, 'options' => [] ],
        ],
    ],
    [
        'title' => 'Pengalaman Kerja',
        'description' => 'Riwayat pekerjaan sebelumnya',
        'questions' => [
            [ 'key' => 'nama_perusahaan', 'title' => 'Nama Perusahaan', 'type' => 'short', 'required' => false, 'options' => [] ],
            [ 'key' => 'jabatan_posisi', 'title' => 'Jabatan / Posisi', 'type' => 'short', 'required' => false, 'options' => [] ],
            [ 'key' => 'periode_kerja', 'title' => 'Periode Kerja', 'type' => 'short', 'required' => false, 'options' => [] ],
            [ 'key' => 'alasan_berhenti', 'title' => 'Alasan Berhenti', 'type' => 'paragraph', 'required' => false, 'options' => [] ],
        ],
    ],
    [
        'title' => 'Keluarga',
        'description' => 'Informasi status dan latar belakang keluarga',
        'questions' => [
            [ 'key' => 'status_pernikahan', 'title' => 'Status Pernikahan', 'type' => 'single_choice', 'required' => true, 'options' => [ 'Belum Menikah', 'Menikah', 'Cerai' ] ],
            [ 'key' => 'susunan_keluarga_inti', 'title' => 'Susunan Keluarga Inti', 'type' => 'paragraph', 'required' => false, 'options' => [] ],
        ],
    ],
    [
        'title' => 'Minat dan Konsep Pribadi',
        'description' => 'Ekspektasi dan motivasi pelamar',
        'questions' => [
            [ 'key' => 'gaji_yang_diharapkan', 'title' => 'Gaji yang Diharapkan', 'type' => 'short', 'required' => true, 'options' => [] ],
            [ 'key' => 'motivasi_bergabung', 'title' => 'Motivasi Bergabung', 'type' => 'paragraph', 'required' => true, 'options' => [] ],
        ],
    ],
];

$notice = '';
$sections = get_option( 'recruitment_application_form', null );
if ( ! is_array( $sections ) ) {
    $sections = $default_sections;
}

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['recruitment_form_builder_save'] ) ) {
    check_admin_referer( 'recruitment_form_builder_save' );
    if ( ! current_user_can( 'manage_recruitment' ) ) {
        wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengubah formulir ini.', 'recruitment-plugin' ) );
    }

    $submitted_sections = isset( $_POST['form_sections'] ) ? wp_unslash( $_POST['form_sections'] ) : [];
    $sections = [];
    $used_keys = [];
    foreach ( is_array( $submitted_sections ) ? $submitted_sections : [] as $section ) {
        if ( ! is_array( $section ) ) {
            continue;
        }
        $section_title = sanitize_text_field( $section['title'] ?? '' );
        if ( '' === $section_title ) {
            continue;
        }

        $questions = [];
        foreach ( is_array( $section['questions'] ?? null ) ? $section['questions'] : [] as $question ) {
            if ( ! is_array( $question ) ) {
                continue;
            }
            $question_title = sanitize_text_field( $question['title'] ?? '' );
            $question_type = sanitize_key( $question['type'] ?? 'short' );
            if ( '' === $question_title || ! isset( $form_types[ $question_type ] ) ) {
                continue;
            }

            $key = sanitize_key( $question['key'] ?? '' );
            if ( '' === $key ) {
                $key = sanitize_title( $question_title );
            }
            $base_key = $key ?: 'field';
            $suffix = 2;
            while ( isset( $used_keys[ $key ] ) ) {
                $key = $base_key . '_' . $suffix;
                $suffix++;
            }
            $used_keys[ $key ] = true;

            $options = [];
            if ( in_array( $question_type, [ 'dropdown', 'single_choice' ], true ) ) {
                $raw_options = preg_split( '/\r\n|\r|\n/', (string) ( $question['options'] ?? '' ) ) ?: [];
                foreach ( $raw_options as $option ) {
                    $option = sanitize_text_field( $option );
                    if ( '' !== $option ) {
                        $options[] = $option;
                    }
                }
            }

            $questions[] = [
                'key' => $key,
                'title' => $question_title,
                'type' => $question_type,
                'required' => ! empty( $question['required'] ),
                'options' => $options,
            ];
        }

        $sections[] = [
            'title' => $section_title,
            'description' => sanitize_text_field( $section['description'] ?? '' ),
            'questions' => $questions,
        ];
    }

    update_option( 'recruitment_application_form', $sections );
    $notice = 'Perubahan formulir berhasil disimpan.';
}

$total_questions = array_sum( array_map( static function ( $section ) {
    return count( $section['questions'] ?? [] );
}, $sections ) );
?>
<div class="wrap recruitment-form-builder">
    <form method="post" data-form-builder>
        <?php wp_nonce_field( 'recruitment_form_builder_save' ); ?>
        <input type="hidden" name="recruitment_form_builder_save" value="1">
        <header class="recruitment-form-builder__topbar">
            <div>
                <p class="recruitment-form-builder__breadcrumb">Recruitment <span>/</span> Form Lamaran</p>
                <h1>Form Lamaran Kerja DAW <span class="recruitment-form-builder__draft">Draft</span></h1>
                <p class="recruitment-form-builder__summary"><span data-section-count><?= esc_html( count( $sections ) ) ?></span> section · <span data-question-count><?= esc_html( $total_questions ) ?></span> pertanyaan · dapat disesuaikan dengan kebutuhan lowongan</p>
            </div>
            <div class="recruitment-form-builder__actions">
                <button type="button" class="button" data-preview-form>Pratinjau</button>
                <button type="button" class="button" data-add-section>+ Tambah Section</button>
                <button type="submit" class="button button-primary">Simpan Perubahan</button>
            </div>
        </header>

        <?php if ( $notice ) : ?>
            <div class="notice notice-success is-dismissible"><p><?= esc_html( $notice ) ?></p></div>
        <?php endif; ?>

        <div class="recruitment-form-builder__sections" data-form-sections>
            <?php foreach ( $sections as $section_index => $section ) : ?>
                <section class="recruitment-form-section" data-form-section>
                    <header class="recruitment-form-section__header">
                        <span class="recruitment-form-section__number" data-section-number><?= esc_html( sprintf( '%02d', $section_index + 1 ) ) ?></span>
                        <div class="recruitment-form-section__heading">
                            <label class="screen-reader-text" for="section-title-<?= esc_attr( $section_index ) ?>">Nama section</label>
                            <input id="section-title-<?= esc_attr( $section_index ) ?>" class="recruitment-form-section__title" name="form_sections[<?= esc_attr( $section_index ) ?>][title]" value="<?= esc_attr( $section['title'] ?? '' ) ?>" data-section-title required>
                            <input class="recruitment-form-section__description" name="form_sections[<?= esc_attr( $section_index ) ?>][description]" value="<?= esc_attr( $section['description'] ?? '' ) ?>" placeholder="Deskripsi section" data-section-description>
                        </div>
                        <span class="recruitment-form-section__count"><span data-section-question-count><?= esc_html( count( $section['questions'] ?? [] ) ) ?></span> pertanyaan</span>
                        <div class="recruitment-form-section__actions">
                            <button type="button" class="button button-small" data-move-section="up" aria-label="Pindahkan section ke atas">↑</button>
                            <button type="button" class="button button-small" data-move-section="down" aria-label="Pindahkan section ke bawah">↓</button>
                            <button type="button" class="button button-small button-link-delete" data-remove-section>Hapus</button>
                        </div>
                    </header>
                    <div class="recruitment-form-section__questions" data-section-questions>
                        <?php foreach ( (array) ( $section['questions'] ?? [] ) as $question_index => $question ) : ?>
                            <article class="recruitment-form-question" data-form-question>
                                <div class="recruitment-form-question__row">
                                    <span class="recruitment-form-question__number" data-question-number><?= esc_html( $question_index + 1 ) ?></span>
                                    <span class="recruitment-form-question__icon" aria-hidden="true">≡</span>
                                    <div class="recruitment-form-question__identity">
                                        <strong data-question-label><?= esc_html( $question['title'] ?? '' ) ?></strong>
                                        <span class="recruitment-form-question__meta"><span data-question-type-label><?= esc_html( $form_types[ $question['type'] ?? 'short' ] ?? 'Jawaban Singkat' ) ?></span><span data-question-required><?= ! empty( $question['required'] ) ? 'Wajib diisi' : 'Opsional' ?></span></span>
                                    </div>
                                    <div class="recruitment-form-question__actions">
                                        <button type="button" class="button button-small" data-move-question="up" aria-label="Pindahkan pertanyaan ke atas">↑</button>
                                        <button type="button" class="button button-small" data-move-question="down" aria-label="Pindahkan pertanyaan ke bawah">↓</button>
                                        <details class="recruitment-form-question__edit"><summary class="button button-small">Edit</summary>
                                            <div class="recruitment-form-question__editor">
                                                <input type="hidden" name="form_sections[<?= esc_attr( $section_index ) ?>][questions][<?= esc_attr( $question_index ) ?>][key]" value="<?= esc_attr( $question['key'] ?? '' ) ?>" data-question-key>
                                                <label>Pertanyaan<input name="form_sections[<?= esc_attr( $section_index ) ?>][questions][<?= esc_attr( $question_index ) ?>][title]" value="<?= esc_attr( $question['title'] ?? '' ) ?>" data-question-title required></label>
                                                <label>Jenis jawaban<select name="form_sections[<?= esc_attr( $section_index ) ?>][questions][<?= esc_attr( $question_index ) ?>][type]" data-question-type><?php foreach ( $form_types as $type => $label ) : ?><option value="<?= esc_attr( $type ) ?>" <?= selected( $question['type'] ?? 'short', $type, false ) ?>><?= esc_html( $label ) ?></option><?php endforeach; ?></select></label>
                                                <label class="recruitment-form-question__options" data-question-options>Opsi jawaban<textarea name="form_sections[<?= esc_attr( $section_index ) ?>][questions][<?= esc_attr( $question_index ) ?>][options]" rows="3" data-question-options-value><?= esc_textarea( implode( "\n", (array) ( $question['options'] ?? [] ) ) ) ?></textarea><small>Satu opsi untuk setiap baris.</small></label>
                                                <label class="recruitment-form-question__required"><input type="checkbox" name="form_sections[<?= esc_attr( $section_index ) ?>][questions][<?= esc_attr( $question_index ) ?>][required]" value="1" data-question-required-input <?= checked( ! empty( $question['required'] ), true, false ) ?>> Wajib diisi</label>
                                                <button type="button" class="button button-link-delete" data-remove-question>Hapus pertanyaan</button>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="recruitment-form-section__add-question" data-add-question>+ Tambah Pertanyaan</button>
                </section>
            <?php endforeach; ?>
        </div>
    </form>
    <dialog class="recruitment-form-preview" data-form-preview>
        <div class="recruitment-form-preview__header"><h2>Pratinjau Form Lamaran</h2><button type="button" class="button" data-close-preview>Tutup</button></div>
        <div class="recruitment-form-preview__body" data-preview-content></div>
    </dialog>
    <template data-question-template>
        <article class="recruitment-form-question" data-form-question>
            <div class="recruitment-form-question__row">
                <span class="recruitment-form-question__number" data-question-number></span><span class="recruitment-form-question__icon" aria-hidden="true">≡</span>
                <div class="recruitment-form-question__identity"><strong data-question-label>Pertanyaan baru</strong><span class="recruitment-form-question__meta"><span data-question-type-label>Jawaban Singkat</span><span data-question-required>Opsional</span></span></div>
                <div class="recruitment-form-question__actions"><button type="button" class="button button-small" data-move-question="up" aria-label="Pindahkan pertanyaan ke atas">↑</button><button type="button" class="button button-small" data-move-question="down" aria-label="Pindahkan pertanyaan ke bawah">↓</button>
                    <details class="recruitment-form-question__edit" open><summary class="button button-small">Edit</summary><div class="recruitment-form-question__editor">
                        <input type="hidden" data-question-key>
                        <label>Pertanyaan<input value="Pertanyaan baru" data-question-title required></label>
                        <label>Jenis jawaban<select data-question-type><?php foreach ( $form_types as $type => $label ) : ?><option value="<?= esc_attr( $type ) ?>"><?= esc_html( $label ) ?></option><?php endforeach; ?></select></label>
                        <label class="recruitment-form-question__options" data-question-options>Opsi jawaban<textarea rows="3" data-question-options-value></textarea><small>Satu opsi untuk setiap baris.</small></label>
                        <label class="recruitment-form-question__required"><input type="checkbox" value="1" data-question-required-input> Wajib diisi</label><button type="button" class="button button-link-delete" data-remove-question>Hapus pertanyaan</button>
                    </div></details>
                </div>
            </div>
        </article>
    </template>
    <template data-section-template>
        <section class="recruitment-form-section" data-form-section>
            <header class="recruitment-form-section__header"><span class="recruitment-form-section__number" data-section-number></span><div class="recruitment-form-section__heading"><label class="screen-reader-text">Nama section</label><input class="recruitment-form-section__title" value="Section baru" data-section-title required><input class="recruitment-form-section__description" placeholder="Deskripsi section" data-section-description></div><span class="recruitment-form-section__count"><span data-section-question-count>0</span> pertanyaan</span><div class="recruitment-form-section__actions"><button type="button" class="button button-small" data-move-section="up" aria-label="Pindahkan section ke atas">↑</button><button type="button" class="button button-small" data-move-section="down" aria-label="Pindahkan section ke bawah">↓</button><button type="button" class="button button-small button-link-delete" data-remove-section>Hapus</button></div></header><div class="recruitment-form-section__questions" data-section-questions></div><button type="button" class="recruitment-form-section__add-question" data-add-question>+ Tambah Pertanyaan</button>
        </section>
    </template>
</div>
