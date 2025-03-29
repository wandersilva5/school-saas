<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="text-muted mb-0 form-label"> Nome da Disciplina </p>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($course['name']) ?></h4>
                    </div>
                    <div>
                        <a href="/courses" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCourseModal">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Código do Curso</h6>
                                <h5 class="mb-0 text-success"><?= htmlspecialchars($course['code'] ?? 'Não informado') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Status</h6>
                                <?php if ($course['active']): ?>
                                    <h5 class="mb-0 text-success">Ativo</h5>
                                <?php else: ?>
                                    <h5 class="mb-0 text-danger">Inativo</h5>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Duração</h6>
                                <h5 class="mb-0"><?= htmlspecialchars($course['duration'] ?? 'Não informado') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Carga Horária</h6>
                                <h5 class="mb-0"><?= $course['workload'] ? htmlspecialchars($course['workload']) . ' horas' : 'Não informado' ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações detalhadas do curso -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h5 class="border-bottom pb-2">Informações Detalhadas</h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-info-circle"></i> Descrição</h6>
                                <?php if (!empty($course['description'])): ?>
                                    <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                                <?php else: ?>
                                    <p class="text-muted">Nenhuma descrição fornecida para este curso.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-list-check"></i> Requisitos</h6>
                                <?php if (!empty($course['requirements'])): ?>
                                    <p><?= nl2br(htmlspecialchars($course['requirements'])) ?></p>
                                <?php else: ?>
                                    <p class="text-muted">Nenhum requisito especificado para este curso.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção para matérias/disciplinas do curso (se implementado) -->
                <div class="row mt-4">
                    <!-- Botão para abrir o modal de adicionar disciplina -->
                    <div class="d-flex justify-content-between mb-4">
                        <h3>Disciplinas do Curso</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                            <i class="bi bi-plus-circle"></i> Adicionar Disciplina
                        </button>
                    </div>

                    <?php if (isset($subjects) && !empty($subjects)): ?>
                        <!-- Lista de disciplinas existentes -->
                        <!-- Lista de disciplinas existentes -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Carga Horária</th>
                                        <th>Professor</th>
                                        <th>Semestre</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $subject) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subject->code) ?></td>
                                            <td><?= htmlspecialchars(strtok($subject->description, ':')) ?></td>
                                            <td><?= htmlspecialchars($subject->workload) ?> horas</td>
                                            <td><?= htmlspecialchars($subject->teacher_name ?? 'Não atribuído') ?></td>
                                            <td><?= htmlspecialchars($subject->semester ?? '-') ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('subjects/show/' . $subject->id) ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('subjects/edit/' . $subject->id) ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('<?= base_url('subjects/delete/' . $subject->id) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>

                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Não há disciplinas cadastradas para este curso.

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar curso -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm" action="/courses/update/<?= $course['id'] ?>" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_code" class="form-label">Código do Curso</label>
                            <input type="text" class="form-control" id="edit_code" name="code" value="<?= htmlspecialchars($course['code']) ?>" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="edit_name" class="form-label">Nome do Curso</label>
                            <input type="text" class="form-control" id="edit_name" name="name" value="<?= htmlspecialchars($course['name']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_duration" class="form-label">Duração</label>
                            <input type="text" class="form-control" id="edit_duration" name="duration" placeholder="Ex: 2 anos, 4 semestres" value="<?= htmlspecialchars($course['duration'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_workload" class="form-label">Carga Horária (horas)</label>
                            <input type="number" class="form-control" id="edit_workload" name="workload" min="0" value="<?= htmlspecialchars($course['workload'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_requirements" class="form-label">Requisitos</label>
                        <textarea class="form-control" id="edit_requirements" name="requirements" rows="2"><?= htmlspecialchars($course['requirements'] ?? '') ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1" <?= $course['active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editCourseForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar disciplina -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubjectModalLabel">Adicionar Nova Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="<?= base_url('subjects/store') ?>" method="post" id="addSubjectForm">
                <div class="modal-body">
                    <!-- Campo oculto para o ID do curso -->
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="col-md-4">
                            <label for="workload" class="form-label">Carga Horária (horas) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="workload" name="workload" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="semester" class="form-label">Semestre <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Selecione o semestre</option>
                                <?php for ($i = 1; $i <= 10; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?>º Semestre</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da Disciplina <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição Completa</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Professor</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Selecione um professor</option>
                            <?php if (isset($teachers) && !empty($teachers)) : ?>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher->id ?>"><?= htmlspecialchars($teacher->name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Disciplina</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation if needed
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });

    function confirmDelete(url) {
        if (confirm('Tem certeza que deseja excluir esta disciplina?')) {
            window.location.href = url;
        }
    }

    // Preparar description quando o formulário for enviado
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addSubjectForm');

        if (form) {
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validar campos obrigatórios
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Preparar o campo de descrição para combinar nome e descrição
                const nameField = document.getElementById('name');
                const descriptionField = document.getElementById('description');

                if (nameField.value.trim() && isValid) {
                    let fullDescription = nameField.value.trim();
                    if (descriptionField.value.trim()) {
                        fullDescription += ': ' + descriptionField.value.trim();
                    }
                    descriptionField.value = fullDescription;
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
<?php endpush() ?>