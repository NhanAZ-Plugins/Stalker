# Changelog

## 2.1.2

- Redact `login` and `register` commands by default, including different letter case.
- Allow case-insensitive custom command redaction and retain redaction if a configuration entry has the wrong type.
- Use a safe in-memory message format when the configured value is not a string.
- Shut down the dedicated log writer when the plugin is disabled.
- Add redaction tests, PHPStan analysis and a pinned DevTools PHAR build.

## 2.1.1

- Historical release before the command logging and lifecycle fixes above.
