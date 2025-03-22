<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card" style="height:700px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Turma: <?= htmlspecialchars($class['name']) ?></h4>
                        <p class="text-muted mb-0">
                            <?= htmlspecialchars($class['shift']) ?> | Ano Letivo: <?= htmlspecialchars($class['year']) ?>
                        </p>
                    </div>
                    <div>
                        <a href="/classes" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="bi bi-person-plus"></i> Adicionar Aluno
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Capacidade</h6>
                                <h3 class="card-title mb-0"><?= htmlspecialchars($class['capacity']) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Alunos</h6>
                                <h3 class="card-title mb-0"><?= count($students) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Vagas</h6>
                                <h3 class="card-title mb-0"><?= $class['capacity'] - count($students) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Status</h6>
                                <?php if ($class['active']): ?>
                                    <h5 class="mb-0 text-success">Ativa</h5>
                                <?php else: ?>
                                    <h5 class="mb-0 text-danger">Inativa</h5>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3">Lista de Alunos</h5>
                <?php if (empty($students)): ?>
                    <div class="alert alert-info">
                        Não há alunos matriculados nesta turma.
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Nome</th>
                                    <th>Idade</th>
                                    <th>Aniversário</th>
                                    <th>Status</th>
                                    <th>Data de Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['name']) ?></td>
                                        <td><?= calculate_age($student['birth_date']) ?> anos</td>
                                        <td><?= format_date($student['birth_date']) ?></td>
                                        <td>
                                            <span class="badge <?= $student['status'] === 'Ativo' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= htmlspecialchars($student['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($student['joined_at'])) ?></td>
                                        <td>
                                            <div class="d-inline-block" style="position: static;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                        Status
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" style="position: absolute; z-index: 9999;">
                                                        <li><a class="dropdown-item" href="#" onclick="updateStudentStatus(<?= $student['id'] ?>, 'Ativo')">Ativo</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateStudentStatus(<?= $student['id'] ?>, 'Concluído')">Concluído</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateStudentStatus(<?= $student['id'] ?>, 'Transferido')">Transferido</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateStudentStatus(<?= $student['id'] ?>, 'Desistente')">Desistente</a></li>
                                                    </ul>
                                                </div>
                                                <button class="btn btn-sm btn-danger" onclick="removeStudent(<?= $student['id'] ?>)">
                                                    <i class="bi bi-person-dash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Aluno -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Aluno à Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStudentForm" action="/classes/add-student" method="POST">
                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Aluno</label>
                        <select class="form-select" id="student_id" name="student_id" required>
                            <option value="">Selecione um aluno</option>
                            <!-- Esta lista será preenchida via AJAX -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="addStudentForm" class="btn btn-primary">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<?php push('styles') ?>
<style>
   /* Remover o overflow do container da tabela para permitir que elementos fiquem visíveis */
   .table-responsive {
        overflow: visible !important;
    }
    
    /* Fazer com que a tabela tenha scroll horizontal, não o container */
    .table {
        width: 100%;
    }
    
    /* Garantir posicionamento correto dos dropdowns */
    .dropdown-menu {
        z-index: 9999 !important;
    }
    
    /* Ajuste para dispositivos móveis */
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto !important;
        }
        
        .dropdown-menu {
            position: fixed !important;
            top: auto !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: 90% !important;
            max-width: 300px !important;
        }
    }
</style>
<?php endpush() ?>

<?php push('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    // Inicializar o Select2 quando o modal for aberto
    document.getElementById('addStudentModal').addEventListener('shown.bs.modal', function() {
        // Carregar a lista de alunos disponíveis
        fetch('/classes/available-students?class_id=<?= $class['id'] ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao buscar alunos disponíveis');
                }
                return response.json();
            })
            .then(data => {
                const selectElement = document.getElementById('student_id');
                selectElement.innerHTML = '<option value="">Selecione um aluno</option>';

                data.forEach(student => {
                    const option = new Option(student.name + ' (' + student.email + ')', student.id);
                    selectElement.add(option);
                });

                // Inicializar Select2
                $('#student_id').select2({
                    dropdownParent: $('#addStudentModal'),
                    width: '100%',
                    placeholder: 'Selecione um aluno'
                });
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('error', 'Erro ao carregar lista de alunos: ' + error.message);
            });
    });

    // Função para atualizar o status de um aluno na turma
    function updateStudentStatus(studentId, status) {
        const formData = new FormData();
        formData.append('class_id', <?= $class['id'] ?>);
        formData.append('student_id', studentId);
        formData.append('status', status);

        fetch('/classes/update-status', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Redirecionar para a mesma página para mostrar o toast da sessão
                    window.location.href = `/classes/show/<?= $class['id'] ?>?success=1`;
                } else {
                    window.location.href = `/classes/show/<?= $class['id'] ?>?error=${encodeURIComponent(data.error || 'Erro desconhecido ao atualizar status')}`;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                window.location.href = `/classes/show/<?= $class['id'] ?>?error=${encodeURIComponent(error.message)}`;
            });
    }

    // Função para remover um aluno da turma
    function removeStudent(studentId) {
        if (confirm('Tem certeza que deseja remover este aluno da turma?')) {
            const formData = new FormData();
            formData.append('class_id', <?= $class['id'] ?>);
            formData.append('student_id', studentId);

            fetch('/classes/remove-student', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Redirecionar para a mesma página para mostrar o toast da sessão
                        window.location.href = `/classes/show/<?= $class['id'] ?>?success=1`;
                    } else {
                        window.location.href = `/classes/show/<?= $class['id'] ?>?error=${encodeURIComponent(data.error || 'Erro desconhecido ao remover aluno')}`;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    window.location.href = `/classes/show/<?= $class['id'] ?>?error=${encodeURIComponent(error.message)}`;
                });
        }
    }
</script>
<?php endpush() ?>