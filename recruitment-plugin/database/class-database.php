<?php

/**
 * Oracle database gateway for the recruitment domain.
 *
 * Connection details are read once from constants, environment variables, or
 * the WordPress options API. Queries stay in this class so pages do not know
 * how Oracle is configured or connected.
 */
class Recruitment_Database {

    private ?PDO $connection = null;

    /**
     * @param array<string, string> $config Optional configuration overrides.
     */
    public function __construct( array $config = [] ) {
        $this->config = array_merge( $this->get_default_config(), $config );
    }

    /** @var array<string, string> */
    private array $config;

    /**
     * Return all published vacancies from Oracle.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_vacancies(): array {
        return $this->fetch_all(
            'SELECT id, title, location, dealer, region, job_type, description FROM daw_vacancies WHERE status = :status ORDER BY id DESC',
            [ 'status' => 'published' ]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_applicants(): array {
        return $this->fetch_all( 'SELECT id, name, email, phone, created_at FROM daw_applicants ORDER BY created_at DESC' );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_applications(): array {
        return $this->fetch_all( 'SELECT id, vacancy_id, applicant_id, application_token, status, created_at FROM daw_applications ORDER BY created_at DESC' );
    }

    /**
     * Persist an application and its public token in one transaction.
     *
     * The unique index on daw_applications.application_token is the final
     * race-safe guard; a duplicate token causes this method to retry.
     *
     * @param array<string, mixed> $payload
     * @return array{success: bool, token?: string, error?: string}
     */
    public function save_application( array $payload ): array {
        if ( ! $this->is_configured() || ! class_exists( 'PDO' ) || ! in_array( 'oci', PDO::getAvailableDrivers(), true ) ) {
            return $this->save_sandbox_application( $payload );
        }

        for ( $attempt = 0; $attempt < 3; $attempt++ ) {
            try {
                $token = $this->generate_application_token();
                $connection = $this->get_connection();
                $connection->beginTransaction();

                $applicant = $payload['applicant'];
                $statement = $connection->prepare(
                    'INSERT INTO daw_applicants (name, email, phone, created_at) VALUES (:name, :email, :phone, CURRENT_TIMESTAMP)'
                );
                $statement->execute(
                    [
                        'name'  => $applicant['full_name'],
                        'email' => $applicant['email'],
                        'phone' => $applicant['phone'],
                    ]
                );
                $applicant_id = (int) $connection->lastInsertId();

                $statement = $connection->prepare(
                    'INSERT INTO daw_applications (vacancy_id, applicant_id, application_token, status, created_at) VALUES (:vacancy_id, :applicant_id, :application_token, :status, CURRENT_TIMESTAMP)'
                );
                $statement->execute(
                    [
                        'vacancy_id'       => $payload['application']['vacancy_id'],
                        'applicant_id'     => $applicant_id,
                        'application_token' => $token,
                        'status'           => $payload['application']['status'],
                    ]
                );

                $connection->commit();
                return [ 'success' => true, 'token' => $token ];
            } catch ( PDOException $exception ) {
                if ( isset( $connection ) && $connection->inTransaction() ) {
                    $connection->rollBack();
                }
                if ( $this->is_unique_violation( $exception ) ) {
                    continue;
                }
                error_log( 'Recruitment application save failed: ' . $exception->getMessage() );
                return [ 'success' => false, 'error' => 'Data lamaran gagal disimpan. Silakan coba lagi.' ];
            }
        }

        return [ 'success' => false, 'error' => 'Token lamaran gagal dibuat unik. Silakan coba lagi.' ];
    }

    /** @return array<string, mixed>|null */
    public function find_application_by_token( string $token, string $contact = '' ): ?array {
        if ( ! $this->is_configured() || ! class_exists( 'PDO' ) || ! in_array( 'oci', PDO::getAvailableDrivers(), true ) ) {
            $applications = $this->read_sandbox_applications();
            foreach ( $applications as $application ) {
                if ( hash_equals( (string) $application['token'], $token ) && ( '' === $contact || strtolower( $application['email'] ) === strtolower( $contact ) || (string) ( $application['phone'] ?? '' ) === $contact ) ) {
                    return $application;
                }
            }
            return null;
        }

        $rows = $this->fetch_all(
            'SELECT a.id, a.application_token AS token, a.status, a.created_at, p.name, p.email FROM daw_applications a JOIN daw_applicants p ON p.id = a.applicant_id WHERE a.application_token = :token AND (:contact_email = \'\' OR LOWER(p.email) = LOWER(:contact_email) OR p.phone = :contact_phone)',
            [ 'token' => $token, 'contact_email' => $contact, 'contact_phone' => $contact ]
        );
        return $rows[0] ?? null;
    }

    public function is_configured(): bool {
        return '' !== $this->config['username'] && '' !== $this->config['password'] && '' !== $this->config['dsn'];
    }

    /**
     * @param array<string, mixed> $parameters
     * @return array<int, array<string, mixed>>
     */
    private function fetch_all( string $sql, array $parameters = [] ): array {
        if ( ! $this->is_configured() || ! class_exists( 'PDO' ) || ! in_array( 'oci', PDO::getAvailableDrivers(), true ) ) {
            return [];
        }

        try {
            $statement = $this->get_connection()->prepare( $sql );
            $statement->execute( $parameters );
            return $statement->fetchAll( PDO::FETCH_ASSOC );
        } catch ( PDOException $exception ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'Recruitment Oracle query failed: ' . $exception->getMessage() );
            }
            return [];
        }
    }

    private function get_connection(): PDO {
        if ( null === $this->connection ) {
            $this->connection = new PDO(
                $this->config['dsn'],
                $this->config['username'],
                $this->config['password'],
                [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
            );
        }

        return $this->connection;
    }

    private function generate_application_token(): string {
        return 'DAW-' . gmdate( 'Y' ) . '-' . strtoupper( rtrim( strtr( base64_encode( random_bytes( 6 ) ), '+/', '-_' ), '=' ) );
    }

    private function is_unique_violation( PDOException $exception ): bool {
        return false !== stripos( $exception->getMessage(), 'ORA-00001' ) || false !== stripos( $exception->getMessage(), 'unique constraint' );
    }

    /** @param array<string, mixed> $payload */
    private function save_sandbox_application( array $payload ): array {
        $applications = $this->read_sandbox_applications();
        for ( $attempt = 0; $attempt < 3; $attempt++ ) {
            try {
                $token = $this->generate_application_token();
            } catch ( Throwable $exception ) {
                return [ 'success' => false, 'error' => 'Token lamaran gagal dibuat. Silakan coba lagi.' ];
            }
            $exists = array_filter( $applications, static fn ( array $item ): bool => hash_equals( (string) $item['token'], $token ) );
            if ( $exists ) {
                continue;
            }
            $applications[] = [
                'token'   => $token,
                'status'  => 'submitted',
                'name'    => $payload['applicant']['full_name'],
                'email'   => $payload['applicant']['email'],
                'phone'   => $payload['applicant']['phone'],
                'created_at' => gmdate( 'c' ),
            ];
            $path = sys_get_temp_dir() . '/daw-recruitment-applications.json';
            if ( false === file_put_contents( $path, json_encode( $applications, JSON_PRETTY_PRINT ), LOCK_EX ) ) {
                return [ 'success' => false, 'error' => 'Data lamaran gagal disimpan. Silakan coba lagi.' ];
            }
            return [ 'success' => true, 'token' => $token ];
        }
        return [ 'success' => false, 'error' => 'Token lamaran gagal dibuat unik. Silakan coba lagi.' ];
    }

    /** @return array<int, array<string, mixed>> */
    private function read_sandbox_applications(): array {
        $path = sys_get_temp_dir() . '/daw-recruitment-applications.json';
        if ( ! is_readable( $path ) ) {
            return [];
        }
        $data = json_decode( (string) file_get_contents( $path ), true );
        return is_array( $data ) ? $data : [];
    }

    /** @return array<string, string> */
    private function get_default_config(): array {
        $options = function_exists( 'get_option' ) ? (array) get_option( 'recruitment_oracle_config', [] ) : [];

        return [
            'dsn'      => (string) ( defined( 'RECRUITMENT_ORACLE_DSN' ) ? RECRUITMENT_ORACLE_DSN : ( getenv( 'RECRUITMENT_ORACLE_DSN' ) ?: ( $options['dsn'] ?? '' ) ) ),
            'username' => (string) ( defined( 'RECRUITMENT_ORACLE_USER' ) ? RECRUITMENT_ORACLE_USER : ( getenv( 'RECRUITMENT_ORACLE_USER' ) ?: ( $options['username'] ?? '' ) ) ),
            'password' => (string) ( defined( 'RECRUITMENT_ORACLE_PASSWORD' ) ? RECRUITMENT_ORACLE_PASSWORD : ( getenv( 'RECRUITMENT_ORACLE_PASSWORD' ) ?: ( $options['password'] ?? '' ) ) ),
        ];
    }
}