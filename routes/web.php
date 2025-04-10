<?php

return [
    ''       => ['controller' => 'HomeController', 'action' => 'index'],
    'error'  => ['controller' => 'HomeController', 'action' => 'error'],
    'login'  => ['controller' => 'AuthController', 'action' => 'login'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],

    // Rotas para o painel de controle
    'dashboard'             => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard-institution' => ['controller' => 'DashboardInstitutionController', 'action' => 'index'],
    'dashboard-responsavel' => ['controller' => 'DashboardResponsavelController', 'action' => 'index'],
    'home-agent'            => ['controller' => 'HomeAgentController', 'action' => 'index'],
    'home-institution'      => ['controller' => 'HomeInstitutionController', 'action' => 'index'],


    // Rotas para gerenciamento de acesso
    'access-management'              => ['controller' => 'AccessManagementController', 'action' => 'index'],
    'access-management/update-roles' => ['controller' => 'AccessManagementController', 'action' => 'updateUserRoles'],
    'access-management/create-user'  => ['controller' => 'AccessManagementController', 'action' => 'createUser'],

    // Rotas para gerenciamento de alertas
    'alerts'              => ['controller' => 'AlertController', 'action' => 'index'],
    'alerts/get-by-id'    => ['controller' => 'AlertController', 'action' => 'getById'],
    'alerts/user-alerts'  => ['controller' => 'AlertController', 'action' => 'userAlerts'],
    'alerts/store'        => ['controller' => 'AlertController', 'action' => 'store'],
    'alerts/update/{id}'  => ['controller' => 'AlertController', 'action' => 'update'],
    'alerts/delete/{id}'  => ['controller' => 'AlertController', 'action' => 'delete'],

    // Rotas para calendário
    'calendar' => ['controller' => 'CalendarController', 'action' => 'index'],
    // Rotas para instituições
    'institution'             => ['controller' => 'InstitutionController', 'action' => 'index'],
    'institution/list'        => ['controller' => 'InstitutionController', 'action' => 'list'],
    'institution/select/{id}' => ['controller' => 'InstitutionController', 'action' => 'select'],
    'institution/store'       => ['controller' => 'InstitutionController', 'action' => 'store'],
    'institution/update/{id}' => ['controller' => 'InstitutionController', 'action' => 'update'],

    // Rotas para usuários das instituições
    'users'             => ['controller' => 'UserController', 'action' => 'index'],
    'users/show/{id}'   => ['controller' => 'UserController', 'action' => 'show'],
    'users/update-info' => ['controller' => 'UserController', 'action' => 'updateInfo'],
    'users/store'       => ['controller' => 'UserController', 'action' => 'store'],
    'users/update/{id}' => ['controller' => 'UserController', 'action' => 'update'],
    'users/delete/{id}' => ['controller' => 'UserController', 'action' => 'delete'],

    // Rotas para Slider Images
    'slider-images'              => ['controller' => 'SliderImageController', 'action' => 'index'],
    'slider-images/store'        => ['controller' => 'SliderImageController', 'action' => 'store'],
    'slider-images/delete/{id}'  => ['controller' => 'SliderImageController', 'action' => 'delete'],
    'slider-images/update-order' => ['controller' => 'SliderImageController', 'action' => 'updateOrder'],

    // Rotas para Alunos
    'students'              => ['controller' => 'StudentController', 'action' => 'index'],
    'students/show/{id}'    => ['controller' => 'StudentController', 'action' => 'show'],
    'students/get-info/{id}' => ['controller' => 'StudentController', 'action' => 'getInfo'],
    'students/edit/{id}'    => ['controller' => 'StudentController', 'action' => 'show'],
    'students/store'        => ['controller' => 'StudentController', 'action' => 'store'],
    'students/edit/{id}'    => ['controller' => 'StudentController', 'action' => 'edit'],
    'students/update/{id}'  => ['controller' => 'StudentController', 'action' => 'update'],
    'students/delete/{id}'  => ['controller' => 'StudentController', 'action' => 'delete'],

    // Rotas para Responsáveis
    'guardians'             => ['controller' => 'GuardianController', 'action' => 'index'],
    'guardians/store'       => ['controller' => 'GuardianController', 'action' => 'store'],
    'guardians/edit/{id}'   => ['controller' => 'GuardianController', 'action' => 'edit'],
    'guardians/update/{id}' => ['controller' => 'GuardianController', 'action' => 'update'],
    'guardians/delete/{id}' => ['controller' => 'GuardianController', 'action' => 'delete'],

    // Rotas para Calendário e Eventos (ordem é importante)
    'calendar'                   => ['controller' => 'CalendarController', 'action' => 'index'],
    'calendar/store'             => ['controller' => 'CalendarController', 'action' => 'store'],
    'calendar/day-events/{date}' => ['controller' => 'CalendarController', 'action' => 'getDayEvents'],
    'calendar/update/{id}'       => ['controller' => 'CalendarController', 'action' => 'update'],
    'calendar/delete/{id}'       => ['controller' => 'CalendarController', 'action' => 'delete'],

    // Rotas para courses
    'courses'             => ['controller' => 'CourseController', 'action' => 'index'],
    'courses/show/{id}'   => ['controller' => 'CourseController', 'action' => 'show'],
    'courses/getById'     => ['controller' => 'CourseController', 'action' => 'getById'],
    'courses/store'       => ['controller' => 'CourseController', 'action' => 'store'],
    'courses/edit/{id}'   => ['controller' => 'CourseController', 'action' => 'edit'],
    'courses/update/{id}' => ['controller' => 'CourseController', 'action' => 'update'],
    'courses/delete/{id}' => ['controller' => 'CourseController', 'action' => 'delete'],

    // Rotas para menus
    'menus'             => ['controller' => 'MenuController', 'action' => 'index'],
    'menus/store'       => ['controller' => 'MenuController', 'action' => 'store'],
    'menus/update/{id}' => ['controller' => 'MenuController', 'action' => 'update'],
    'menus/delete/{id}' => ['controller' => 'MenuController', 'action' => 'delete'],
    'menus/reorder'     => ['controller' => 'MenuController', 'action' => 'reorder'],

    // Rotas para classe
    'classes'                    => ['controller' => 'ClassController', 'action' => 'index'],
    'classes/store'              => ['controller' => 'ClassController', 'action' => 'store'],
    'classes/getById'            => ['controller' => 'ClassController', 'action' => 'getById'],
    'classes/update'             => ['controller' => 'ClassController', 'action' => 'update'],
    'classes/delete'             => ['controller' => 'ClassController', 'action' => 'delete'],
    'classes/show/{id}'          => ['controller' => 'ClassController', 'action' => 'show'],
    'classes/add-student'        => ['controller' => 'ClassController', 'action' => 'addStudent'],
    'classes/remove-student'     => ['controller' => 'ClassController', 'action' => 'removeStudent'],
    'classes/update-status'      => ['controller' => 'ClassController', 'action' => 'updateStatus'],
    'classes/available-students' => ['controller' => 'ClassController', 'action' => 'getAvailableStudents'],

    // Rotas para classe aluno
    'class-students'             => ['controller' => 'ClassStudentController', 'action' => 'index'],
    'class-students/store'       => ['controller' => 'ClassStudentController', 'action' => 'store'],
    'class-students/edit/{id}'   => ['controller' => 'ClassStudentController', 'action' => 'edit'],
    'class-students/update/{id}' => ['controller' => 'ClassStudentController', 'action' => 'update'],
    'class-students/delete/{id}' => ['controller' => 'ClassStudentController', 'action' => 'delete'],

    // Rotas para disciplinas
    'subjects'             => ['controller' => 'SubjectController', 'action' => 'index'],
    'subjects/show/{id}'   => ['controller' => 'SubjectController', 'action' => 'show'],
    'subjects/store'       => ['controller' => 'SubjectController', 'action' => 'store'],
    'subjects/edit/{id}'   => ['controller' => 'SubjectController', 'action' => 'edit'],
    'subjects/update/{id}' => ['controller' => 'SubjectController', 'action' => 'update'],
    'subjects/delete/{id}' => ['controller' => 'SubjectController', 'action' => 'delete'],

    // Rotas para Pagamentos
    'payments'                       => ['controller' => 'PaymentController', 'action' => 'index'],
    'payments/show/{id}'             => ['controller' => 'PaymentController', 'action' => 'show'],
    'payments/create'                => ['controller' => 'PaymentController', 'action' => 'create'],
    'payments/store'                 => ['controller' => 'PaymentController', 'action' => 'store'],
    'payments/batch-generate'        => ['controller' => 'PaymentController', 'action' => 'batchGenerate'],
    'payments/edit/{id}'             => ['controller' => 'PaymentController', 'action' => 'edit'],
    'payments/update/{id}'           => ['controller' => 'PaymentController', 'action' => 'update'],
    'payments/delete/{id}'           => ['controller' => 'PaymentController', 'action' => 'delete'],
    'payments/generate-boleto/{id}'  => ['controller' => 'PaymentController', 'action' => 'generateBoleto'],
    'payments/view-boleto/{id}'      => ['controller' => 'PaymentController', 'action' => 'viewBoleto'],

    // Rotas para Configurações Bancárias
    'bank-config'        => ['controller' => 'BankConfigController', 'action' => 'index'],
    'bank-config/update' => ['controller' => 'BankConfigController', 'action' => 'update'],

    // Rotas para 

];
