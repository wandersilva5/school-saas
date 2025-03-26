-- Migration script for courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Course name',
    code VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Course code/identifier',
    description TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Course description',
    workload INT COMMENT 'Workload in hours',
    duration VARCHAR(50) COLLATE utf8mb4_unicode_ci COMMENT 'Course duration (e.g., "1 year", "2 semesters")',
    requirements TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Pre-requisites or requirements',
    institution_id INT NOT NULL,
    active TINYINT(1) DEFAULT 1 COMMENT 'Whether the course is active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_course_code (code, institution_id),
    CONSTRAINT courses_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indices for common queries
CREATE INDEX idx_courses_institution ON courses(institution_id);
CREATE INDEX idx_courses_active ON courses(active);

-- Insert sample courses data
INSERT INTO courses (name, code, description, workload, duration, requirements, institution_id) 
SELECT 
    'Fundamental Education', 'FUN', 'Basic education from grades 1-9', 1800, '9 years', 'Age 6+', i.id 
FROM institutions i WHERE i.id = 1
UNION ALL
SELECT 
    'High School', 'HS', 'Secondary education program', 2400, '3 years', 'Completed Fundamental Education', i.id
FROM institutions i WHERE i.id = 1
UNION ALL
SELECT 
    'Technical Computing', 'TECH-COMP', 'Technical computing program', 1200, '2 years', 'Completed Fundamental Education', i.id
FROM institutions i WHERE i.id = 1;