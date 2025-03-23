<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h4 class="card-title"><?= $pageTitle ?></h4>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                        <i class="bi bi-plus"></i> Novo Aluno
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Nsc/Idade</th>
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
                                    <td></td>
                                    <td><?= htmlspecialchars($student['guardian_name'] ?? 'Não definido') ?></td>
                                    <td>
                                        <?php if ($student['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/students/show/<?= $student['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="editStudent(<?= htmlspecialchars(json_encode($student)) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteStudent(<?= $student['id'] ?>)">
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
                    <div class="form-check mb-3">
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
        $('#createStudentModal, #editStudentModal').on('shown.bs.modal', function() {
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

    function viewAlunoDetails(id) {
        // Show loading state
        document.getElementById('studentDetailsContent').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
        $('#alunoInfoModal').modal('show');

        // Fetch student details with proper error handling
        fetch(`/students/get-info/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response error');
                return response.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);

                // Debug - log the returned data to see what's available
                console.log("Student data:", data);

                // Populate modal with data
                document.getElementById('detail_name').textContent = data.name || 'N/A';
                document.getElementById('detail_email').textContent = data.email || 'N/A';

                // Add these fields from student_info table
                document.getElementById('detail_birth_date').textContent = data.birth_date || 'N/A';
                document.getElementById('detail_registration').textContent = data.registration_number || 'N/A';
                document.getElementById('detail_address').textContent = formatAddress(data) || 'N/A';
                document.getElementById('detail_health').textContent = data.health_observations || 'N/A';

                // Etc. for other fields
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('studentDetailsContent').innerHTML =
                    `<div class="alert alert-danger">Erro ao carregar detalhes: ${error.message}</div>`;
            });
    }

    // Helper function to format address
    function formatAddress(data) {
        if (!data.address_street) return '';

        return `${data.address_street}, ${data.address_number || 'S/N'}${data.address_complement ? ', ' + data.address_complement : ''} - ${data.address_district}, ${data.address_city}/${data.address_state}`;
    }

    // Função para mostrar formulário de edição
    function showEditForm() {
        document.getElementById('infoDisplay').style.display = 'none';
        document.getElementById('infoForm').style.display = 'block';
        document.getElementById('saveInfoBtn').style.display = 'inline-block';
        document.getElementById('cancelEditBtn').style.display = 'inline-block';
    }

    // Função para cancelar edição
    function cancelEdit() {
        document.getElementById('infoForm').style.display = 'none';
        document.getElementById('infoDisplay').style.display = 'block';
        document.getElementById('saveInfoBtn').style.display = 'none';
        document.getElementById('cancelEditBtn').style.display = 'none';
    }

    // Função para formatar data (YYYY-MM-DD para DD/MM/YYYY)
    function formatDate(dateString) {
        if (!dateString) return '-';

        const parts = dateString.split('-');
        if (parts.length !== 3) return dateString;

        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    // Função para formatar gênero
    function formatGender(gender) {
        if (!gender) return '-';

        const genders = {
            'M': 'Masculino',
            'F': 'Feminino',
            'O': 'Outro'
        };

        return genders[gender] || gender;
    }

    // Função para formatar endereço completo
    function formatAddress(info) {
        if (!info.address_street) return '-';

        let address = `${info.address_street}`;
        if (info.address_number) address += `, ${info.address_number}`;
        if (info.address_complement) address += ` - ${info.address_complement}`;
        if (info.address_district) address += `, ${info.address_district}`;
        if (info.address_city) address += `, ${info.address_city}`;
        if (info.address_state) address += `/${info.address_state}`;
        if (info.address_zipcode) address += ` - CEP: ${info.address_zipcode}`;

        return address;
    }

    // Inicializar máscaras para os campos
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $().mask === 'function') {
            $('.telefone').mask('(00) 00000-0000');
            $('#edit_cep').mask('00000-000');
        }
    });
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