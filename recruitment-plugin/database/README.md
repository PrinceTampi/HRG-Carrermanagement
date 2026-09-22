# Database

`class-database.php` is the Oracle gateway. It creates one lazy PDO OCI connection per request and reads configuration from constants, environment variables, or the `recruitment_oracle_config` WordPress option.

Supported settings:

```text
RECRUITMENT_ORACLE_DSN
RECRUITMENT_ORACLE_USER
RECRUITMENT_ORACLE_PASSWORD
```

Pages and components must call `Recruitment_Database` methods rather than creating PDO connections or embedding SQL.

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

When the database schema is finalized, add the insert transaction to the
database gateway and pass this payload to it from the application service.

## Application Token Migration

Run `migrations/001_application_token.sql` once against Oracle before enabling
production submissions. It adds `daw_applications.application_token` and a
unique index used to protect token uniqueness during concurrent submissions.