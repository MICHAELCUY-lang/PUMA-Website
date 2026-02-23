# Maintenance Scripts

This directory contains various utility scripts for database maintenance, debugging, and testing.

## Files

- `check_avatars.php`: Debug script to check member avatar image paths.
- `check_data_loss.php`: Checks row counts of tables to ensure no data loss during migrations.
- `check_fks.php`: Inspects foreign key constraints.
- `check_members_indexes.php`: Lists indexes on the members table.
- `dump_schema.php`: Dumps the schema of the members table to a text file.
- `fix_members_schema.php`: Script to recreate the members table without unique constraints if needed.
- `investigate_events.php`: Investigates event table schema and status columns.
- `investigate_members.php`: Checks member schema details.
- `list_all_tables.php`: Lists all tables in the SQLite database.
- `test_event_api.php`: Tests the Event creation API endpoint.
- `test_fixes.php`: Tests various fixes including event deletion and member creation.
- `test_unique_constraint.php`: Tests inserting members with NULL user_id to verify constraints.

## Usage

Run these scripts from the command line:

```bash
php maintenance_scripts/filename.php
```

Note: These scripts have been updated to include the correct autoload paths when running from this directory.
