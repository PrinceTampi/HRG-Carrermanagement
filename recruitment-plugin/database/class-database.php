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
        return $this->fetch_all( 'SELECT id, vacancy_id, applicant_id, status, created_at FROM daw_applications ORDER BY created_at DESC' );
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