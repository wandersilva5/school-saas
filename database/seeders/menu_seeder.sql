-- Inserindo menus principais
INSERT INTO menus (name, url, icon, header, route, required_roles, order_index) VALUES
-- Principal
('Dashboard', '/dashboard-institution', 'bi-house-door', 'Principal', 'dashboard-institution', 'TI', 10),
('Calendário', '/calendar', 'bi-calendar3', 'Principal', 'calendar', 'TI', 20),

-- Acadêmico
('Cursos', '/courses', 'bi-book', 'Acadêmico', 'courses', 'TI', 30),
('Turmas', '/classes', 'bi-people', 'Acadêmico', 'classes', 'TI', 40),
('Alunos', '/students', 'bi-person-badge', 'Acadêmico', 'students', 'TI', 50),
('Responsáveis', '/guardians', 'bi-person-circle', 'Acadêmico', 'guardians', 'TI', 60),

-- Administração
('Usuários', '/users', 'bi-people-fill', 'Administração', 'users', 'TI', 70),
('Configurações Gerais', '/settings', 'bi-sliders', 'Administração', 'settings', 'TI', 80),
('Imagens do Slider', '/slider-images', 'bi-images', 'Administração', 'slider-images', 'TI', 90);

-- Vinculando menus ao papel TI (assumindo que o ID do papel TI é 1)
INSERT INTO menu_roles (menu_id, role_id)
SELECT id, 1 FROM menus WHERE required_roles LIKE '%TI%';
