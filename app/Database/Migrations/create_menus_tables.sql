CREATE TABLE menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    header VARCHAR(100) NOT NULL,
    route VARCHAR(100) NOT NULL,
    required_roles TEXT NOT NULL,
    order_index INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu_roles (
    menu_id INT,
    role_id INT,
    PRIMARY KEY (menu_id, role_id),
    FOREIGN KEY (menu_id) REFERENCES menus(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
