ALTER TABLE courses ADD COLUMN cert_date DATE NULL;
ALTER TABLE courses DROP COLUMN start_date;
ALTER TABLE courses DROP COLUMN end_date;
DESCRIBE courses;
