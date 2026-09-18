# Database

`class-database.php` is the Oracle gateway. It creates one lazy PDO OCI connection per request and reads configuration from constants, environment variables, or the `recruitment_oracle_config` WordPress option.

Supported settings:

```text
RECRUITMENT_ORACLE_DSN
RECRUITMENT_ORACLE_USER
RECRUITMENT_ORACLE_PASSWORD
```

Pages and components must call `Recruitment_Database` methods rather than creating PDO connections or embedding SQL.