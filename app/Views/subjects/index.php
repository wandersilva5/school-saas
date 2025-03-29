<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?? 'Disciplinas' ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                            <i class="bi bi-plus-circle"></i> Nova Disciplina
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Disciplina</th>
                                <th>Curso</th>
                                <th>Semestre</th>
                                <th>Carga Horária</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subjects)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma disciplina encontrada. Crie sua primeira disciplina!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subject['code']) ?></td>
                                        <td><?= htmlspecialchars($subject['name']) ?></td>
                                        <td><?= htmlspecialchars($subject['course_name']) ?></td>
                                        <td><?= $subject['semester'] ? htmlspecialchars($subject['semester']) . 'º' : 'Não definido' ?></td>
                                        <td><?= $subject['workload'] ? htmlspecialchars($subject['workload']) . ' horas' : 'Não definido' ?></td>
                                        <td>
                                            <?php if ($subject['active']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/subjects/show/<?= $subject['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-primary" onclick="editSubject(<?= $subject['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteSubject(<?= $subject['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Subject Modal -->
<div class="modal fade" id="createSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastro de uma nova Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createSubjectForm" action="/subjects/store" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Código da Disciplina</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Nome da Disciplina</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_id" class="form-label">Curso</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Selecione um curso</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="semester" class="form-label">Semestre</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">Selecione</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?>º Semestre</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="workload" class="form-label">Carga Horária (horas)</label>
                            <input type="number" class="form-control" id="workload" name="workload" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="createSubjectForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSubjectForm" action="/subjects/update/" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_code" class="form-label">Código da Disciplina</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="edit_name" class="form-label">Nome da Disciplina</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_course_id" class="form-label">Curso</label>
                            <select class="form-select" id="edit_course_id" name="course_id" required>
                                <option value="">Selecione um curso</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_semester" class="form-label">Semestre</label>
                            <select class="form-select" id="edit_semester" name="semester">
                                <option value="">Selecione</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?>º Semestre</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_workload" class="form-label">Carga Horária (horas)</label>
                            <input type="number" class="form-control" id="edit_workload" name="workload" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1" checked>
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label">Professor</label>
                        <select class="form-select" id="edit_user_id" name="user_id">
                            <option value="">Selecione um professor</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="editSubjectForm" class="btn btn-primary">Salvar</button>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>
    function editSubject(id) {
        $('#edit_id').val(id);
        $.ajax({
            url: '/subjects/edit/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#edit_code').val(data.code);
                $('#edit_name').val(data.name);
                $('#edit_course_id').val(data.course_id);
                $('#edit_semester').val(data.semester);
                $('#edit_workload').val(data.workload);
                $('#edit_description').val(data.description);
                $('#edit_active').prop('checked', data.active);
                $('#edit_user_id').val(data.user_id);
                $('#editSubjectModal').modal('show');
            }
        });
    }
    function editSubject(subjectId) {
        try {
            // First set the ID and trigger the AJAX call
            fetch(`/subjects/edit/${subjectId}`, {
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
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    // Update form action
                    $('#editStudentForm').attr('action', `/subjects/update/${data.id}`);

                    // Fill form fields
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_phone').val(data.phone);

                    // Update guardian select
                    if (data.guardian_user_id) {
                        $('#edit_guardian_id').val(data.guardian_user_id).trigger('change');
                    } else {
                        $('#edit_guardian_id').val('').trigger('change');
                    }

                    // Update active checkbox
                    $('#edit_active').prop('checked', Boolean(parseInt(data.active)));

                    // Show modal
                    $('#editStudentModal').modal('show');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar dados do aluno: ' + error.message);
                });
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao processar dados do aluno: ' + error.message);
        }
    }
</script>
<?php endpush() ?>