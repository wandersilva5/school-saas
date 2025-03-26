<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($course['name']) ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-code"></i> <?= htmlspecialchars($course['code']) ?>
                        </p>
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
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Cadastro</h6>
                                <h5 class="mb-0"><?= isset($course['created_at']) ? format_date($course['created_at']) : 'N/A' ?></h5>
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
                    <div class="col-md-12 mb-3">
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

                    <div class="col-md-12 mb-3">
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
                    <div class="col-md-12 mb-3">
                        <h5 class="border-bottom pb-2">Disciplinas do Curso</h5>
                    </div>
                    
                    <?php if (isset($subjects) && !empty($subjects)): ?>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Nome da Disciplina</th>
                                            <th>Carga Horária</th>
                                            <th>Semestre</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subjects as $subject): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($subject['code']) ?></td>
                                                <td><?= htmlspecialchars($subject['name']) ?></td>
                                                <td><?= $subject['workload'] ? htmlspecialchars($subject['workload']) . ' horas' : 'N/A' ?></td>
                                                <td><?= $subject['semester'] ?? 'N/A' ?></td>
                                                <td>
                                                    <a href="/subjects/view/<?= $subject['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Não há disciplinas cadastradas para este curso.
                                <a href="/subjects/create?course_id=<?= $course['id'] ?>" class="btn btn-sm btn-outline-primary float-end">
                                    <i class="bi bi-plus"></i> Adicionar Disciplina
                                </a>
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
</script>
<?php endpush() ?>