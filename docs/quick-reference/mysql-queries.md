# MySQL Queries Quick Reference

Common queries for validating database state during development and testing.

## Accessing MySQL

### From Project Root
```bash
make sql

mysql>
```

### Exit MySQL
```bash
exit
quit
\q
```

---

## Basic Queries

### Show All Databases
```sql
SHOW DATABASES;
```

### Select Database
```sql
USE database_name;
```

### Show All Tables
```sql
SHOW TABLES;
```

### Describe Table Structure
```sql
DESCRIBE tasks;
DESC tasks;
```

**Example Output:**
```
+-------------+-----------------+------+-----+-----------+----------------+
| Field       | Type            | Null | Key | Default   | Extra          |
+-------------+-----------------+------+-----+-----------+----------------+
| id          | bigint unsigned | NO   | PRI | NULL      | auto_increment |
| title       | varchar(255)    | NO   |     | NULL      |                |
| description | text            | YES  |     | NULL      |                |
| status      | varchar(255)    | NO   |     | pendiente |                |
| created_at  | timestamp       | YES  |     | NULL      |                |
| updated_at  | timestamp       | YES  |     | NULL      |                |
+-------------+-----------------+------+-----+-----------+----------------+
```

---

## Count Queries

### Count All Records
```sql
SELECT COUNT(*) FROM tasks;
```

### Count by Status
```sql
SELECT COUNT(*) FROM tasks WHERE status = 'pendiente';
```

### Count Grouped by Field
```sql
SELECT status, COUNT(*) as count 
FROM tasks 
GROUP BY status;
```

**Example Output:**
```
+---------------+-------+
| status        | count |
+---------------+-------+
| pendiente     |     7 |
| en progreso   |     6 |
| completada    |     7 |
+---------------+-------+
```

### Count with Multiple Conditions
```sql
SELECT COUNT(*) 
FROM tasks 
WHERE status = 'pendiente' 
AND created_at >= '2025-10-01';
```

---

## Select Queries

### Select All Records
```sql
SELECT * FROM tasks;
```

### Select Specific Columns
```sql
SELECT id, title, status FROM tasks;
```

### Select with Limit
```sql
SELECT * FROM tasks LIMIT 5;

SELECT * FROM tasks LIMIT 5 OFFSET 5;
```

### Select with Ordering
```sql
SELECT * FROM tasks ORDER BY created_at DESC;

SELECT * FROM tasks ORDER BY created_at ASC;

SELECT * FROM tasks ORDER BY status ASC, created_at DESC;
```

### Select with WHERE Clause
```sql
SELECT * FROM tasks WHERE status = 'pendiente';

SELECT * FROM tasks 
WHERE status = 'pendiente' 
AND created_at >= '2025-10-01';

SELECT * FROM tasks 
WHERE status = 'pendiente' 
OR status = 'en progreso';

SELECT * FROM tasks 
WHERE status IN ('pendiente', 'en progreso');

SELECT * FROM tasks WHERE title LIKE '%urgent%';

SELECT * FROM tasks WHERE LOWER(title) LIKE '%urgent%';
```

---

## Validation Queries (After Seeders)

### Check Seeder Created Records
```sql
SELECT COUNT(*) FROM tasks;
```

### Verify Data Quality
```sql
SELECT COUNT(*) FROM tasks WHERE title IS NULL;

SELECT COUNT(*) FROM tasks WHERE title = '';

SELECT COUNT(*) FROM tasks WHERE description IS NULL;

SELECT COUNT(*) 
FROM tasks 
WHERE status NOT IN ('pendiente', 'en progreso', 'completada');
```

### Sample Data Inspection
```sql
SELECT * FROM tasks WHERE status = 'pendiente' LIMIT 3;
SELECT * FROM tasks WHERE status = 'en progreso' LIMIT 3;
SELECT * FROM tasks WHERE status = 'completada' LIMIT 3;

SELECT * FROM tasks ORDER BY created_at DESC LIMIT 5;

SELECT * FROM tasks ORDER BY created_at ASC LIMIT 5;
```

---

## Validation Queries (After API Tests)

### Find Specific Record
```sql
SELECT * FROM tasks WHERE id = 21;

SELECT * FROM tasks WHERE title = 'Task from cURL';

SELECT * FROM tasks 
WHERE title = 'Task from cURL' 
AND status = 'pendiente';
```

### Verify Record Exists
```sql
SELECT EXISTS(
    SELECT 1 FROM tasks WHERE title = 'Task from cURL'
) as record_exists;
```

**Output:**
```
+---------------+
| record_exists |
+---------------+
|             1 |
+---------------+
```

### Verify Record Doesn't Exist (After DELETE)
```sql
SELECT COUNT(*) FROM tasks WHERE id = 21;
```

### Compare Before/After State
```sql
SELECT COUNT(*) FROM tasks;

SELECT COUNT(*) FROM tasks;
```

---

## Date and Time Queries

### Filter by Date
```sql
SELECT * FROM tasks WHERE DATE(created_at) = CURDATE();

SELECT * FROM tasks WHERE DATE(created_at) = '2025-10-25';

SELECT * FROM tasks WHERE created_at >= '2025-10-01';

SELECT * FROM tasks 
WHERE created_at BETWEEN '2025-10-01' AND '2025-10-31';

SELECT * FROM tasks 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);

SELECT * FROM tasks 
WHERE MONTH(created_at) = MONTH(NOW()) 
AND YEAR(created_at) = YEAR(NOW());
```

### Date Aggregations
```sql
SELECT DATE(created_at) as date, COUNT(*) as count 
FROM tasks 
GROUP BY DATE(created_at) 
ORDER BY date DESC;

SELECT 
    YEAR(created_at) as year,
    MONTH(created_at) as month,
    COUNT(*) as count 
FROM tasks 
GROUP BY YEAR(created_at), MONTH(created_at) 
ORDER BY year DESC, month DESC;
```

---

## Advanced Queries

### Statistics
```sql
SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN status = 'pendiente' THEN 1 END) as pendiente,
    COUNT(CASE WHEN status = 'en progreso' THEN 1 END) as en_progreso,
    COUNT(CASE WHEN status = 'completada' THEN 1 END) as completada
FROM tasks;
```

### Min/Max Values
```sql
SELECT 
    MIN(created_at) as oldest,
    MAX(created_at) as newest
FROM tasks;

SELECT 
    MIN(id) as first_id,
    MAX(id) as last_id
FROM tasks;
```

### Distinct Values
```sql
SELECT DISTINCT status FROM tasks;

SELECT COUNT(DISTINCT status) as unique_statuses FROM tasks;
```

### Text Search
```sql
SELECT * FROM tasks WHERE LOWER(title) LIKE '%urgent%';

SELECT * FROM tasks WHERE title LIKE 'Task%';

SELECT * FROM tasks WHERE title LIKE '%urgent';

SELECT * FROM tasks 
WHERE title LIKE '%urgent%' 
AND title LIKE '%important%';

SELECT * FROM tasks 
WHERE title LIKE '%urgent%' 
OR title LIKE '%important%';
```

---

## Relationship Queries

### Join with Users (if relationship exists)
```sql
SELECT 
    tasks.id,
    tasks.title,
    tasks.status,
    users.name as user_name,
    users.email as user_email
FROM tasks
INNER JOIN users ON tasks.user_id = users.id;

SELECT 
    users.name,
    COUNT(tasks.id) as task_count
FROM users
LEFT JOIN tasks ON users.id = tasks.user_id
GROUP BY users.id, users.name
ORDER BY task_count DESC;
```

---

## Data Modification (Use Carefully)

### Update Records
```sql
UPDATE tasks SET status = 'completada' WHERE id = 1;

UPDATE tasks 
SET status = 'completada', updated_at = NOW() 
WHERE id = 1;

UPDATE tasks SET status = 'completada' WHERE status = 'en progreso';
```

### Delete Records
```sql
DELETE FROM tasks WHERE id = 1;

DELETE FROM tasks WHERE status = 'completada';

DELETE FROM tasks;

TRUNCATE TABLE tasks;
```

---

## Validation Query Templates

### After Running Seeder
```sql
SELECT COUNT(*) FROM tasks;

DESCRIBE tasks;

SELECT * FROM tasks LIMIT 5;

SELECT status, COUNT(*) as count FROM tasks GROUP BY status;

SELECT COUNT(*) FROM tasks WHERE title IS NULL;

SELECT COUNT(*) FROM tasks 
WHERE status NOT IN ('pendiente', 'en progreso', 'completada');
```

### After POST Request (Create)
```sql
SELECT * FROM tasks WHERE title = 'Task from cURL';

SELECT * FROM tasks WHERE id = 21;

SELECT COUNT(*) FROM tasks;
```

### After PUT Request (Update)
```sql
SELECT * FROM tasks WHERE id = 21;

SELECT status FROM tasks WHERE id = 21;

SELECT updated_at FROM tasks WHERE id = 21;
```

### After DELETE Request
```sql
SELECT COUNT(*) FROM tasks WHERE id = 21;

SELECT COUNT(*) FROM tasks;
```

---

## Useful MySQL Commands

### Show Current Database
```sql
SELECT DATABASE();
```

### Show Current User
```sql
SELECT USER();
```

### Show Current Time
```sql
SELECT NOW();
SELECT CURDATE();
SELECT CURTIME();
```

### Clear Screen (Linux/Mac)
```bash
\! clear
system clear
```

### Execute SQL from File
```bash
mysql -u root -p database_name < file.sql

source /path/to/file.sql
```

### Show Last Query Execution Time
```sql
SET profiling = 1;

SELECT * FROM tasks WHERE status = 'pendiente';

SHOW PROFILES;
```

---

## Troubleshooting Queries

### Check for Duplicate Records
```sql
SELECT title, COUNT(*) as count 
FROM tasks 
GROUP BY title 
HAVING count > 1;
```

### Find Records with Odd Data
```sql
SELECT * FROM tasks WHERE description = '';

SELECT * FROM tasks WHERE created_at < '2020-01-01';

SELECT * FROM tasks WHERE created_at > NOW();

SELECT * FROM tasks WHERE user_id NOT IN (SELECT id FROM users);
```

### Check Table Size
```sql
SELECT 
    table_name,
    table_rows,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC;
```

---

## Best Practices

### ✅ Do

1. **Always use LIMIT when exploring**
```sql
   ✅ SELECT * FROM tasks LIMIT 10;
   ❌ SELECT * FROM tasks;
```

2. **Use transactions for modifications**
```sql
   START TRANSACTION;
   UPDATE tasks SET status = 'completada' WHERE id = 1;
   SELECT * FROM tasks WHERE id = 1;
   COMMIT;
```

3. **Use COUNT before DELETE**
```sql
   SELECT COUNT(*) FROM tasks WHERE status = 'completada';
   DELETE FROM tasks WHERE status = 'completada';
```

### ❌ Don't

1. **Don't modify production data without backup**
2. **Don't DELETE or UPDATE without WHERE clause**
```sql
   ❌ DELETE FROM tasks;
   ✅ DELETE FROM tasks WHERE id = 1;
```

3. **Don't use SELECT * in production code**
```sql
   ❌ SELECT * FROM tasks;
   ✅ SELECT id, title, status FROM tasks;
```

---

## Quick Command Reference

| Task | Command |
|------|---------|
| Count records | `SELECT COUNT(*) FROM tasks;` |
| View structure | `DESCRIBE tasks;` |
| Sample data | `SELECT * FROM tasks LIMIT 5;` |
| Find by ID | `SELECT * FROM tasks WHERE id = 1;` |
| Find by field | `SELECT * FROM tasks WHERE status = 'pendiente';` |
| Order results | `SELECT * FROM tasks ORDER BY created_at DESC;` |
| Group and count | `SELECT status, COUNT(*) FROM tasks GROUP BY status;` |
| Check exists | `SELECT COUNT(*) FROM tasks WHERE title = 'X';` |

---

## Related Documentation

- [Phase 3: Validation](../workflow/phase-3-validation.md)
- [cURL Templates](./curl-templates.md)
- [Testing Standards](../standards/testing.md)