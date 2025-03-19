<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Alunos</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                        <i class="bi bi-plus"></i> Novo Aluno
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Responsável</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['student_name']) ?></td>
                                        <td><?= htmlspecialchars($student['student_email']) ?></td>
                                        <td><?= htmlspecialchars($student['guardian_name'] ?? 'Não definido') ?></td>
                                        <td>
                                            <?php if ($student['active']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editStudent(<?= htmlspecialchars(json_encode($student)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?= $student['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Aluno -->
<div class="modal fade" id="createStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/students/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control phone" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="guardian_id" class="form-label">Responsável</label>
                        <select class="form-select" id="guardian_id" name="guardian_id" required>
                            <option value="">Selecione um responsável</option>
                            <?php foreach ($guardians as $guardian): ?>
                                <option value="<?= $guardian['id'] ?>">
                                    <?= htmlspecialchars($guardian['name']) ?> - <?= htmlspecialchars($guardian['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Aluno -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStudentForm" action="/students/update/" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control phone" id="edit_phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_guardian_id" class="form-label">Responsável</label>
                        <select class="form-select" id="edit_guardian_id" name="guardian_id" required>
                            <option value="">Selecione um responsável</option>
                            <?php foreach ($guardians as $guardian): ?>
                                <option value="<?= $guardian['id'] ?>">
                                    <?= htmlspecialchars($guardian['name']) ?> - <?= htmlspecialchars($guardian['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    // Initialize phone mask
    $('.phone').mask('(00) 00000-0000');
       
    // Initialize Select2 for guardian selects
    $('#guardian_id, #edit_guardian_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Selecione um responsável',
        allowClear: true,
        language: {
            noResults: function() {
                return "Nenhum responsável encontrado";
            }
        }
    });

    // Reinitialize Select2 when modal opens
    $('#createStudentModal, #editStudentModal').on('shown.bs.modal', function () {
        $('#guardian_id, #edit_guardian_id').select2({
            dropdownParent: $(this),
            width: '100%'
        });
    });
});

function editStudent(student) {
    try {
        // First set the ID and trigger the AJAX call
        const studentId = student.id;
        
        fetch(`/students/show/${studentId}`, {
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
            $('#editStudentForm').attr('action', `/students/update/${data.id}`);
            
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

function deleteStudent(id) {
    if (confirm('Tem certeza que deseja excluir este aluno?')) {
        window.location.href = `/students/delete/${id}`;
    }
}
</script>

<style>
.select2-container--bootstrap-5 {
    width: 100% !important;
}
.select2-container--bootstrap-5 .select2-selection {
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}
</style>
<?php endpush() ?>
