<?php
$test_labels = [ 'iq' => 'IQ', 'pauli' => 'Pauli', 'disc' => 'DISC', 'papi' => 'PAPI Kostick' ];
$subtest_labels = [
    'iq_numbers' => 'IQ - 1 Berhitung Angka',
    'iq_assembly' => 'IQ - 2 Gabungan Bagian',
    'iq_words' => 'IQ - 3 Hubungan Kata',
    'iq_abstraction' => 'IQ - 4 Abstraksi Nonverbal',
];
$type_labels = [ 'multiple_choice' => 'Pilihan Ganda', 'multi_select' => 'Pilihan Ganda (Multi-select)', 'numeric' => 'Numerik', 'paired' => 'Pernyataan' ];
$database = new Recruitment_Database();
$questions = $database->get_psychotest_question_bank();
$notice = '';
$error = $database->has_error()
    ? 'Bank soal belum dapat dimuat dari Oracle. Silakan coba kembali.'
    : '';
$pdf_preview = false;
$pdf_preview_test = 'disc';
$pdf_preview_subtest = '';
$pdf_preview_text = '';
$pdf_source_name = '';
$pdf_preview_count = 0;
$pdf_preview_questions = [];
$pdf_selected_indexes = [];
$pdf_parse_error = '';
$success_redirect_url = '';
$pdf_importer = new Recruitment_Psychotest_Pdf_Importer();

if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) && isset( $_POST['question_bank_action'] ) ) {
    check_admin_referer( 'recruitment_question_bank' );
    if ( ! current_user_can( 'manage_recruitment' ) ) {
        wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengelola bank soal.', 'recruitment-plugin' ) );
    }

    $action = sanitize_key( wp_unslash( (string) $_POST['question_bank_action'] ) );
    $question_id = isset( $_POST['question_id'] ) && is_scalar( $_POST['question_id'] )
        ? absint( wp_unslash( (string) $_POST['question_id'] ) )
        : 0;
    $posted = isset( $_POST['question'] ) && is_array( $_POST['question'] )
        ? wp_unslash( $_POST['question'] )
        : [];
    $test = isset( $posted['test'] ) && is_scalar( $posted['test'] )
        ? sanitize_key( (string) $posted['test'] )
        : '';
    $prompt = isset( $posted['prompt'] ) && is_scalar( $posted['prompt'] )
        ? sanitize_textarea_field( (string) $posted['prompt'] )
        : '';
    $explanation = isset( $posted['explanation'] ) && is_scalar( $posted['explanation'] )
        ? sanitize_textarea_field( (string) $posted['explanation'] )
        : '';
    $is_active = isset( $posted['is_active'] ) && is_scalar( $posted['is_active'] ) && '1' === (string) $posted['is_active'];
    $result = [ 'success' => false, 'error' => 'Aksi bank soal tidak valid.' ];

    if ( in_array( $action, [ 'preview_pdf_import', 'save_pdf_import' ], true ) ) {
        $pdf_preview_test = $test;
        if ( ! in_array( $pdf_preview_test, [ 'iq', 'disc', 'papi' ], true ) ) {
            $result = [ 'success' => false, 'error' => 'Jenis tes untuk impor PDF tidak valid.' ];
        } elseif ( 'preview_pdf_import' === $action ) {
            $upload = isset( $_FILES['source_pdf'] ) && is_array( $_FILES['source_pdf'] )
                ? $_FILES['source_pdf']
                : [];
            $upload_error = isset( $upload['error'] ) && is_scalar( $upload['error'] )
                ? (int) $upload['error']
                : UPLOAD_ERR_NO_FILE;
            $temporary_path = isset( $upload['tmp_name'] ) && is_string( $upload['tmp_name'] )
                ? $upload['tmp_name']
                : '';
            $filename = isset( $upload['name'] ) && is_string( $upload['name'] )
                ? sanitize_file_name( $upload['name'] )
                : '';
            $pdf_source_name = $filename;
            $mime = class_exists( 'finfo' ) && '' !== $temporary_path
                ? ( new finfo( FILEINFO_MIME_TYPE ) )->file( $temporary_path )
                : '';
            $header = '' !== $temporary_path && is_readable( $temporary_path )
                ? file_get_contents( $temporary_path, false, null, 0, 5 )
                : false;

            if ( UPLOAD_ERR_OK !== $upload_error || ! is_uploaded_file( $temporary_path ) ) {
                $result = [ 'success' => false, 'error' => UPLOAD_ERR_INI_SIZE === $upload_error || UPLOAD_ERR_FORM_SIZE === $upload_error
                    ? 'Ukuran PDF melebihi batas unggah server.'
                    : 'Pilih file PDF yang valid untuk diproses.' ];
            } elseif ( 'pdf' !== strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) )
                || 'application/pdf' !== $mime
                || '%PDF-' !== $header ) {
                $result = [ 'success' => false, 'error' => 'File harus berformat PDF yang valid (ekstensi dan MIME application/pdf).' ];
            } else {
                $result = $pdf_importer->extract_text( $temporary_path );
                if ( ! empty( $result['success'] ) ) {
                    $pdf_preview = true;
                    $pdf_preview_text = (string) ( $result['text'] ?? '' );
                    $parsed = $pdf_importer->parse_questions( $pdf_preview_text, $pdf_preview_test );
                    if ( ! empty( $parsed['success'] ) ) {
                        $pdf_preview_questions = array_values( $parsed['questions'] ?? [] );
                        $pdf_preview_count = count( $pdf_preview_questions );
                        $pdf_selected_indexes = array_keys( $pdf_preview_questions );
                        $pdf_preview_subtest = (string) ( $pdf_preview_questions[0]['subtest'] ?? '' );
                    } else {
                        $pdf_parse_error = (string) ( $parsed['error'] ?? 'Format grup soal perlu diperiksa.' );
                    }
                }
            }
        } else {
            $pdf_preview = true;
            $pdf_preview_text = isset( $_POST['pdf_import_text'] ) && is_scalar( $_POST['pdf_import_text'] )
                ? sanitize_textarea_field( wp_unslash( (string) $_POST['pdf_import_text'] ) )
                : '';
            $pdf_source_name = isset( $_POST['pdf_source_name'] ) && is_scalar( $_POST['pdf_source_name'] )
                ? sanitize_file_name( wp_unslash( (string) $_POST['pdf_source_name'] ) )
                : '';
            $parsed = $pdf_importer->parse_questions( $pdf_preview_text, $pdf_preview_test );
            if ( ! empty( $parsed['success'] ) ) {
                $pdf_preview_questions = array_values( $parsed['questions'] ?? [] );
                $pdf_preview_count = count( $pdf_preview_questions );
                $pdf_preview_subtest = (string) ( $pdf_preview_questions[0]['subtest'] ?? '' );
                $posted_indexes = isset( $_POST['selected_question_indexes'] ) && is_array( $_POST['selected_question_indexes'] )
                    ? wp_unslash( $_POST['selected_question_indexes'] )
                    : [];
                foreach ( $posted_indexes as $posted_index ) {
                    if ( is_scalar( $posted_index ) && ctype_digit( (string) $posted_index ) ) {
                        $index = (int) $posted_index;
                        if ( isset( $pdf_preview_questions[ $index ] ) ) {
                            $pdf_selected_indexes[] = $index;
                        }
                    }
                }
                $pdf_selected_indexes = array_values( array_unique( $pdf_selected_indexes ) );
                if ( ! $pdf_selected_indexes ) {
                    $result = [ 'success' => false, 'error' => 'Pilih minimal satu soal untuk ditambahkan ke bank.' ];
                } else {
                    $selected_questions = array_map(
                        static fn ( int $index ): array => $pdf_preview_questions[ $index ],
                        $pdf_selected_indexes
                    );
                    $result = $database->create_psychotest_questions( $pdf_preview_test, $selected_questions );
                }
            } else {
                $result = [ 'success' => false, 'error' => (string) ( $parsed['error'] ?? 'Format soal PDF tidak valid.' ) ];
                $pdf_parse_error = (string) ( $parsed['error'] ?? 'Format soal PDF tidak valid.' );
            }
        }
    } elseif ( 'create_question' === $action && '' !== trim( $prompt ) ) {
        $type = isset( $posted['type'] ) && is_scalar( $posted['type'] )
            ? sanitize_key( (string) $posted['type'] )
            : '';
        $choices_text = isset( $posted['choices'] ) && is_scalar( $posted['choices'] )
            ? (string) $posted['choices']
            : '';
        $choices = preg_split( '/\r\n|\r|\n/', $choices_text ) ?: [];
        $choices = array_values( array_map( 'sanitize_text_field', array_filter( $choices, static function ( $choice ): bool {
            return is_scalar( $choice ) && '' !== trim( (string) $choice );
        } ) ) );
        $subtest = 'iq' === $test
            ? 'iq_numbers'
            : ( isset( $posted['subtest'] ) && is_scalar( $posted['subtest'] )
            ? sanitize_key( (string) $posted['subtest'] )
            : '' );
        $answer = isset( $posted['answer'] ) && is_scalar( $posted['answer'] )
            ? sanitize_text_field( (string) $posted['answer'] )
            : '';
        $items = preg_split( '/\r\n|\r|\n/', $prompt ) ?: [];
        $result = $database->create_psychotest_question(
            [
                'test' => $test,
                'subtest' => $subtest,
                'prompt' => $prompt,
                'explanation' => $explanation,
                'is_active' => $is_active,
                'type' => $type,
                'choices' => $choices,
                'answer' => $answer,
                'items' => array_values( array_map( 'sanitize_text_field', $items ) ),
            ]
        );
    } elseif ( 'save_question' === $action && $question_id > 0 && '' !== trim( $prompt ) ) {
        if ( 'iq' === $test ) {
            $choices_text = isset( $posted['choices'] ) && is_scalar( $posted['choices'] )
                ? (string) $posted['choices']
                : '';
            $choices = preg_split( '/\r\n|\r|\n/', $choices_text ) ?: [];
            $choices = array_values( array_map( 'sanitize_text_field', array_filter( $choices, static function ( $choice ): bool {
                return is_scalar( $choice ) && '' !== trim( (string) $choice );
            } ) ) );
            $subtest = isset( $posted['subtest'] ) && is_scalar( $posted['subtest'] )
                ? sanitize_key( (string) $posted['subtest'] )
                : '';
            $type = isset( $posted['type'] ) && is_scalar( $posted['type'] )
                ? sanitize_key( (string) $posted['type'] )
                : '';
            $answer = isset( $posted['answer'] ) && is_scalar( $posted['answer'] )
                ? sanitize_text_field( (string) $posted['answer'] )
                : '';
            $result = $database->save_iq_question(
                [
                    'id' => $question_id,
                    'subtest' => $subtest,
                    'prompt' => $prompt,
                    'explanation' => $explanation,
                    'is_active' => $is_active,
                    'type' => $type,
                    'choices' => $choices,
                    'answer' => $answer,
                ]
            );
        } elseif ( 'pauli' === $test ) {
            $result = $database->save_pauli_question(
                [
                    'id' => $question_id,
                    'prompt' => $prompt,
                    'explanation' => $explanation,
                    'is_active' => $is_active,
                ]
            );
        } elseif ( in_array( $test, [ 'disc', 'papi' ], true ) ) {
            $items = preg_split( '/\r\n|\r|\n/', $prompt ) ?: [];
            $result = $database->save_personality_question(
                [
                    'id' => $question_id,
                    'test' => $test,
                    'items' => array_values( array_map( 'sanitize_text_field', $items ) ),
                    'explanation' => $explanation,
                    'is_active' => $is_active,
                ]
            );
        } else {
            $result = [ 'success' => false, 'error' => 'Jenis tes tidak valid.' ];
        }
    } elseif ( 'save_question' === $action || 'create_question' === $action ) {
        $result = [
            'success' => false,
            'error' => 'create_question' === $action
                ? 'Lengkapi isi soal sebelum menambahkan ke bank.'
                : 'Pilih soal yang akan diperbarui dan lengkapi isinya.',
        ];
    }

    if ( ! empty( $result['success'] ) && 'preview_pdf_import' !== $action ) {
        $redirect_args = [ 'page' => 'recruitment-question-bank', 'saved' => '1' ];
        if ( 'save_pdf_import' === $action ) {
            $redirect_args['pdf_imported'] = '1';
        }
        $filter = isset( $_GET['test'] ) && is_scalar( $_GET['test'] )
            ? sanitize_key( wp_unslash( (string) $_GET['test'] ) )
            : $test;
        if ( 'save_pdf_import' === $action ) {
            $filter = $test;
        }
        if ( isset( $test_labels[ $filter ] ) ) {
            $redirect_args['test'] = $filter;
        }
        $redirect_url = add_query_arg( $redirect_args, admin_url( 'admin.php' ) );
        if ( ! headers_sent() ) {
            wp_safe_redirect( $redirect_url );
            exit;
        }
        $success_redirect_url = $redirect_url;
        $notice = 'save_pdf_import' === $action
            ? 'Soal hasil impor berhasil disimpan pada bank Oracle.'
            : 'Soal berhasil disimpan pada bank Oracle.';
        $questions = $database->get_psychotest_question_bank();
        if ( $database->has_error() ) {
            $error = 'Soal berhasil disimpan, tetapi daftar bank soal gagal dimuat ulang dari Oracle.';
        }
    }

    if ( empty( $result['success'] ) ) {
        $error = (string) ( $result['error'] ?? 'Soal gagal disimpan ke Oracle.' );
    }
}

if ( '' === $notice && '1' === sanitize_text_field( wp_unslash( $_GET['saved'] ?? '' ) ) ) {
    $notice = '1' === sanitize_text_field( wp_unslash( $_GET['pdf_imported'] ?? '' ) )
        ? 'Soal hasil impor berhasil disimpan pada bank Oracle.'
        : 'Soal berhasil disimpan pada bank Oracle.';
}
$filter_test = sanitize_key( $_GET['test'] ?? '' );
$filter_status = sanitize_key( $_GET['status'] ?? '' );
$search = sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) );
$filtered_questions = array_values( array_filter( $questions, static function ( $question ) use ( $filter_test, $filter_status, $search ) {
    $matches_test = '' === $filter_test || $filter_test === ( $question['test'] ?? '' );
    $matches_status = '' === $filter_status || $filter_status === ( $question['status'] ?? '' );
    $matches_search = '' === $search || false !== stripos( (string) ( $question['prompt'] ?? '' ), $search );
    return $matches_test && $matches_status && $matches_search;
} ) );
$test_counts = array_fill_keys( array_keys( $test_labels ), 0 );
foreach ( $questions as $question ) {
    if ( isset( $test_counts[ $question['test'] ?? '' ] ) ) {
        $test_counts[ $question['test'] ]++;
    }
}
$test_stat_cards = [
    [ 'label' => 'IQ', 'count' => $test_counts['iq'] ],
    [ 'label' => 'Pauli', 'count' => $test_counts['pauli'] ],
    [ 'label' => 'Kepribadian', 'count' => $test_counts['disc'] + $test_counts['papi'] ],
    [ 'label' => 'DISC', 'count' => $test_counts['disc'] ],
    [ 'label' => 'PAPI Kostick', 'count' => $test_counts['papi'] ],
];
$active_count = count( array_filter( $questions, static function ( $question ) {
    return 'active' === ( $question['status'] ?? 'inactive' );
} ) );
$admin_user = wp_get_current_user();
$hr_identity = recruitment_get_current_hr_identity();
$logout_form = recruitment_logout_form( 'Keluar', home_url( '/' ) );
?>
<div class="wrap daw-question-bank" data-question-bank data-filter-test="<?= esc_attr( isset( $test_labels[ $filter_test ] ) ? $filter_test : '' ) ?>">
    <header class="daw-question-bank__header"><div><p class="daw-question-bank__breadcrumb"><a href="<?= esc_url( recruitment_get_admin_url( 'psychotest-settings' ) ) ?>">Setting Psikotes</a><span>/</span>Bank Soal</p><h1>Bank Soal Psikotes</h1><p>Tambah atau perbarui soal IQ, Pauli, DISC, dan PAPI yang digunakan tes online.</p></div><span class="daw-question-bank__profile"><span><?= esc_html( $hr_identity['avatar'] ) ?></span><span class="daw-question-bank__profile-copy"><strong><?= esc_html( $hr_identity['name'] ) ?></strong><small><?= esc_html( $hr_identity['email'] ) ?></small></span><?= $logout_form ?></span></header>
    <?php if ( '' !== $notice ) : ?><div class="notice notice-success is-dismissible"><p><?= esc_html( $notice ) ?></p></div><?php endif; ?>
    <?php if ( '' !== $error ) : ?><div class="notice notice-error"><p><?= esc_html( $error ) ?></p></div><?php endif; ?>
    <section class="daw-question-bank__stats" aria-label="Jumlah soal per tes">
        <?php foreach ( $test_stat_cards as $card ) : ?><div class="daw-question-bank__stat"><strong><?= esc_html( (string) $card['count'] ) ?></strong><span><?= esc_html( $card['label'] ) ?></span></div><?php endforeach; ?>
    </section>
    <section class="daw-question-bank__panel">
        <div class="daw-question-bank__panel-heading"><div><h2>Bank Soal Psikotes</h2><p><?= esc_html( (string) count( $filtered_questions ) ) ?> soal ditampilkan<?php if ( isset( $test_labels[ $filter_test ] ) ) : ?> untuk <?= esc_html( $test_labels[ $filter_test ] ) ?><?php endif; ?></p><small>Soal baru ditambahkan setelah urutan terakhir. Sesi yang sedang berjalan tetap menggunakan soal yang sudah dibuka saat sesi dimulai.</small></div><div class="daw-question-bank__panel-actions">
            <form method="get" class="daw-question-bank__filters"><input type="hidden" name="page" value="recruitment-question-bank"><label class="screen-reader-text" for="question-test-filter">Filter tes</label><select id="question-test-filter" name="test"><option value="">Semua Tes</option><?php foreach ( $test_labels as $test_key => $test_label ) : ?><option value="<?= esc_attr( $test_key ) ?>" <?= selected( $filter_test, $test_key, false ) ?>><?= esc_html( $test_label ) ?></option><?php endforeach; ?></select><label class="screen-reader-text" for="question-status-filter">Filter status</label><select id="question-status-filter" name="status"><option value="">Semua Status</option><option value="active" <?= selected( $filter_status, 'active', false ) ?>>Aktif</option><option value="inactive" <?= selected( $filter_status, 'inactive', false ) ?>>Nonaktif</option></select><label class="screen-reader-text" for="question-search">Cari soal</label><input id="question-search" type="search" name="s" value="<?= esc_attr( $search ) ?>" placeholder="Cari soal..."><button type="submit" class="button">Filter</button></form>
            <button type="button" class="button button-primary" data-new-question>Tambah Soal</button>
        </div></div>
        <div class="daw-question-bank__pdf-import">
            <div class="daw-question-bank__pdf-import-copy"><h3>Impor soal dari PDF</h3><p>Pilih jenis tes, lalu unggah naskah soal kosong berbasis teks. Kategori subtes IQ akan dikenali otomatis dari judul PDF; gunakan satu subtes IQ per PDF. Hasil ekstraksi hanya disimpan setelah ditinjau dan dikonfirmasi.</p><small>PDF scan/OCR belum didukung. Jangan unggah hasil tes peserta atau dokumen berisi identitas.</small></div>
            <form method="post" enctype="multipart/form-data" class="daw-question-bank__pdf-upload">
                <?php wp_nonce_field( 'recruitment_question_bank' ); ?>
                <input type="hidden" name="question_bank_action" value="preview_pdf_import">
                <label for="pdf-import-test">Jenis tes</label>
                <select id="pdf-import-test" name="question[test]" data-pdf-import-test required><option value="disc" <?= selected( $pdf_preview_test, 'disc', false ) ?>>DISC</option><option value="papi" <?= selected( $pdf_preview_test, 'papi', false ) ?>>PAPI Kostick</option><option value="iq" <?= selected( $pdf_preview_test, 'iq', false ) ?>>IQ</option></select>
                <label for="pdf-import-file">File PDF</label>
                <input id="pdf-import-file" type="file" name="source_pdf" accept="application/pdf,.pdf" required>
                <button type="submit" class="button">Ekstrak untuk ditinjau</button>
            </form>
        </div>
        <?php if ( $pdf_preview ) : ?>
            <form method="post" class="daw-question-bank__pdf-preview" data-pdf-preview>
                <?php wp_nonce_field( 'recruitment_question_bank' ); ?>
                <input type="hidden" name="question_bank_action" value="save_pdf_import">
                <input type="hidden" name="question[test]" value="<?= esc_attr( $pdf_preview_test ) ?>">
                <input type="hidden" name="pdf_source_name" value="<?= esc_attr( $pdf_source_name ) ?>">
                <textarea name="pdf_import_text" hidden><?= esc_textarea( $pdf_preview_text ) ?></textarea>
                <div class="daw-question-bank__pdf-preview-heading">
                    <div><h3>Preview Soal Hasil Import</h3><p><?= esc_html( (string) $pdf_preview_count ) ?> soal terdeteksi dari <?= esc_html( '' !== $pdf_source_name ? $pdf_source_name : 'PDF' ) ?> — pilih soal yang ingin disimpan.</p></div>
                    <span class="daw-question-bank__pdf-draft">Draft Import</span>
                </div>
                <?php if ( '' !== $pdf_parse_error ) : ?><p class="daw-question-bank__pdf-validation daw-question-bank__pdf-validation--error"><?= esc_html( $pdf_parse_error ) ?></p><?php elseif ( $pdf_preview_count > 0 ) : ?><p class="daw-question-bank__pdf-validation"><?= esc_html( (string) $pdf_preview_count ) ?> <?= 'iq' === $pdf_preview_test ? 'soal IQ' : 'grup pernyataan' ?> valid · kategori <?= esc_html( $test_labels[ $pdf_preview_test ] ?? $pdf_preview_test ) ?><?php if ( 'iq' === $pdf_preview_test ) : ?> · <?= esc_html( $subtest_labels[ $pdf_preview_subtest ] ?? $pdf_preview_subtest ) ?><?php endif; ?></p><?php endif; ?>
                <?php if ( $pdf_preview_questions ) : ?>
                    <div class="daw-question-bank__pdf-selection-bar">
                        <label><input type="checkbox" data-pdf-select-all <?= count( $pdf_selected_indexes ) === $pdf_preview_count ? 'checked' : '' ?>> Pilih semua soal</label>
                        <span><strong data-pdf-selected-count><?= esc_html( (string) count( $pdf_selected_indexes ) ) ?></strong>/<?= esc_html( (string) $pdf_preview_count ) ?> dipilih</span>
                    </div>
                    <div class="daw-question-bank__pdf-question-list">
                        <?php foreach ( $pdf_preview_questions as $question_index => $import_question ) :
                            $is_selected = in_array( $question_index, $pdf_selected_indexes, true );
                            $question_items = array_values( (array) ( $import_question['items'] ?? [] ) );
                            $question_choices = array_values( (array) ( $import_question['choices'] ?? [] ) );
                            $answer_indexes = array_values( array_filter( explode( '|', (string) ( $import_question['answer'] ?? '' ) ), static fn ( string $answer ): bool => ctype_digit( $answer ) ) );
                            ?>
                            <article class="daw-question-bank__pdf-question">
                                <header><label><input type="checkbox" name="selected_question_indexes[]" value="<?= esc_attr( (string) $question_index ) ?>" data-pdf-question-checkbox <?= checked( $is_selected, true, false ) ?>><strong>#<?= esc_html( (string) ( $question_index + 1 ) ) ?></strong></label><span class="daw-question-bank__tag daw-question-bank__tag--<?= esc_attr( $pdf_preview_test ) ?>"><?= esc_html( $test_labels[ $pdf_preview_test ] ?? $pdf_preview_test ) ?></span><?php if ( 'iq' === $pdf_preview_test && isset( $subtest_labels[ $pdf_preview_subtest ] ) ) : ?><small><?= esc_html( $subtest_labels[ $pdf_preview_subtest ] ) ?></small><?php endif; ?></header>
                                <?php if ( 'iq' === $pdf_preview_test ) : ?>
                                    <p class="daw-question-bank__pdf-prompt"><?= nl2br( esc_html( (string) ( $import_question['prompt'] ?? '' ) ) ) ?></p>
                                    <div class="daw-question-bank__pdf-choices"><?php foreach ( $question_choices as $choice_index => $choice ) : ?><span class="<?= in_array( (string) ( $choice_index + 1 ), $answer_indexes, true ) ? 'is-correct' : '' ?>"><?= esc_html( chr( 65 + $choice_index ) . '. ' . (string) $choice ) ?></span><?php endforeach; ?></div>
                                    <?php if ( $answer_indexes ) : ?><small class="daw-question-bank__pdf-answer">Kunci Jawaban: <?= esc_html( implode( ', ', array_map( static fn ( string $answer ): string => chr( 64 + (int) $answer ), $answer_indexes ) ) ) ?></small><?php endif; ?>
                                <?php else : ?>
                                    <div class="daw-question-bank__pdf-choices"><?php foreach ( $question_items as $item_index => $item ) : ?><span><?= esc_html( chr( 65 + $item_index ) . '. ' . (string) $item ) ?></span><?php endforeach; ?></div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="button button-primary" data-pdf-apply <?= $pdf_preview_questions && $pdf_selected_indexes ? '' : 'disabled' ?>>Terapkan <span data-pdf-apply-count><?= esc_html( (string) count( $pdf_selected_indexes ) ) ?></span> soal terpilih</button>
            </form>
        <?php endif; ?>
        <div class="daw-question-bank__table-wrap"><table class="widefat striped daw-question-bank__table"><thead><tr><th>No</th><th>Pertanyaan / Soal</th><th>Tes</th><th>Tipe</th><th>Status</th><th>Diperbarui</th><th>Aksi</th></tr></thead><tbody>
            <?php if ( ! $filtered_questions ) : ?><tr><td colspan="7" class="daw-question-bank__empty"><?= '' !== $error ? 'Data bank soal belum tersedia karena Oracle tidak dapat dimuat.' : 'Belum ada soal yang sesuai dengan filter.' ?></td></tr><?php endif; ?>
            <?php foreach ( $filtered_questions as $index => $question ) :
                $question_test = (string) ( $question['test'] ?? '' );
                $question_type = (string) ( $question['type'] ?? '' );
                ?>
                <tr>
                    <td><?= esc_html( (string) ( $question['number'] ?? ( $index + 1 ) ) ) ?></td>
                    <td><strong><?= nl2br( esc_html( (string) ( $question['prompt'] ?? '' ) ) ) ?></strong><?php if ( '' !== trim( (string) ( $question['explanation'] ?? '' ) ) ) : ?><small class="daw-question-bank__explanation"><?= nl2br( esc_html( (string) $question['explanation'] ) ) ?></small><?php endif; ?></td>
                    <td><span class="daw-question-bank__tag daw-question-bank__tag--<?= esc_attr( $question_test ) ?>"><?= esc_html( $test_labels[ $question_test ] ?? $question_test ) ?><?php if ( 'iq' === $question_test && isset( $subtest_labels[ $question['subtest'] ?? '' ] ) ) : ?><small><?= esc_html( $subtest_labels[ $question['subtest'] ] ) ?></small><?php endif; ?></span></td>
                    <td><span class="daw-question-bank__tag daw-question-bank__tag--type"><?= esc_html( $type_labels[ $question_type ] ?? $question_type ) ?></span></td>
                    <td><span class="daw-question-bank__status daw-question-bank__status--<?= esc_attr( $question['status'] ?? 'inactive' ) ?>"><?= 'active' === ( $question['status'] ?? '' ) ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td><?= esc_html( $question['updated'] ?? '—' ) ?></td>
                    <td class="daw-question-bank__actions">
                        <button type="button" class="button-link" data-edit-question data-id="<?= esc_attr( $question['id'] ) ?>" data-number="<?= esc_attr( (string) ( $question['number'] ?? '' ) ) ?>" data-prompt="<?= esc_attr( $question['prompt'] ) ?>" data-explanation="<?= esc_attr( $question['explanation'] ?? '' ) ?>" data-active="<?= esc_attr( (string) ( 'active' === ( $question['status'] ?? '' ) ? 1 : 0 ) ) ?>" data-test="<?= esc_attr( $question_test ) ?>" data-subtest="<?= esc_attr( $question['subtest'] ?? '' ) ?>" data-type="<?= esc_attr( $question_type ) ?>" data-choices="<?= esc_attr( implode( "\n", (array) ( $question['choices'] ?? [] ) ) ) ?>" data-answer="<?= esc_attr( $question['answer'] ?? '' ) ?>">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </section>
    <dialog class="daw-question-bank__dialog" data-question-dialog aria-labelledby="question-dialog-title">
        <form method="post" data-question-form>
            <?php wp_nonce_field( 'recruitment_question_bank' ); ?>
            <input type="hidden" name="question_bank_action" value="save_question" data-question-action><input type="hidden" name="question_id" value="" data-question-id>
            <div class="daw-question-bank__dialog-heading"><h2 id="question-dialog-title">Edit Soal</h2><button type="button" class="button-link" aria-label="Tutup" data-close-question-dialog>&times;</button></div>
            <label data-question-prompt-label>Pertanyaan / Soal<textarea name="question[prompt]" rows="4" required data-question-prompt></textarea><small data-question-prompt-help></small></label>
            <input type="hidden" name="question[subtest]" data-question-subtest-value>
            <div class="daw-question-bank__dialog-grid"><label>Tes<select data-question-test disabled><?php foreach ( $test_labels as $test_key => $test_label ) : ?><option value="<?= esc_attr( $test_key ) ?>"><?= esc_html( $test_label ) ?></option><?php endforeach; ?></select><input type="hidden" name="question[test]" data-question-test-value></label><label data-question-type-label>Tipe<select data-question-type disabled><?php foreach ( $type_labels as $type_key => $type_label ) : ?><option value="<?= esc_attr( $type_key ) ?>"><?= esc_html( $type_label ) ?></option><?php endforeach; ?></select><input type="hidden" name="question[type]" data-question-type-value></label></div>
            <label data-question-choices-label>Opsi jawaban<textarea name="question[choices]" rows="4" data-question-choices></textarea><small>Satu pilihan per baris; setiap baris disimpan sebagai opsi jawaban soal ini.</small></label>
            <label data-question-answer-label>Jawaban benar<input name="question[answer]" data-question-answer><small>Gunakan nomor opsi atau teks opsi; pisahkan jawaban multi-select dengan tanda |.</small></label>
            <label>Pembahasan / Catatan<textarea name="question[explanation]" rows="3" maxlength="4000" data-question-explanation></textarea><small>Hanya ditampilkan di Bank Soal admin, tidak ditampilkan kepada pelamar.</small></label>
            <label class="daw-question-bank__active"><input type="checkbox" name="question[is_active]" value="1" checked data-question-active> Aktifkan soal untuk tes pelamar</label>
            <div class="daw-question-bank__dialog-actions"><button type="button" class="button" data-close-question-dialog>Batal</button><button type="submit" class="button button-primary" data-question-submit>Simpan Perubahan</button></div>
        </form>
    </dialog>
</div>
<?php if ( '' !== $success_redirect_url ) : ?>
<script>window.location.replace(<?= wp_json_encode( $success_redirect_url ) ?>);</script>
<?php endif; ?>
