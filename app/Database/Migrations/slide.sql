CREATE TABLE slider_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    institution_id INT NOT NULL,
    order_position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);

CREATE TABLE attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    present BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    document VARCHAR(20) NOT NULL,
    type VARCHAR(20) NOT NULL,
    institution_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);

CREATE TABLE pending_authorizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_name VARCHAR(255) NOT NULL,
    person_type VARCHAR(20) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    requested_by VARCHAR(255) NOT NULL,
    institution_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);
