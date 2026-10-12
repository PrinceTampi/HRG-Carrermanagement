# Database

## Structure

```text
database/
  connection/
    class-oracle-connection.php  # Oracle config and shared lazy PDO_OCI connection
  class-database.php             # Domain queries used by pages and services
  migrations/                    # Versioned Oracle schema changes
```

`Recruitment_Oracle_Connection` owns Oracle configuration and provides one lazy
PDO connection shared for the request. `Recruitment_Database` owns the
recruitment queries and transaction operations. Pages and components should
call its methods; they must not create PDO connections, keep database state in
PHP sessions/files, or embed SQL.

Configuration is read in priority order from PHP constants, process
environment variables, the `.env` file named by `RECRUITMENT_DOTENV_PATH`, and
the `recruitment_oracle_config` WordPress option.

Supported settings:

```text
RECRUITMENT_ORACLE_DSN
RECRUITMENT_ORACLE_USER
RECRUITMENT_ORACLE_PASSWORD
```

The PHP `pdo_oci` extension and valid Oracle settings are required for database
operations. If either is unavailable, database operations report an error;
there is no JSON or session-backed database fallback.

## Application Payload Contract

`includes/class-application.php` normalizes the public application form into a
database-ready payload. The page does not create a connection or execute SQL.
The payload is grouped into:

```text
application   vacancy_id, status
applicant     identity and contact fields
education     repeatable education rows
work_history  repeatable employment rows
family        marital and family relationship fields
preferences   salary, benefits, and known employee fields
consents      PDP consent flags
photo         uploaded file metadata for the storage layer
```

The application service passes this payload to the database gateway, which
persists it in Oracle.

## Application Token Migration

Run `migrations/001_application_token.sql` once against Oracle before enabling
production submissions. It adds `daw_applications.application_token` and a
unique index used to protect token uniqueness during concurrent submissions.