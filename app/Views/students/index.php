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
                                        <button class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#alunoInfoModal" 
                                                onclick="viewAlunoDetails(<?= $aluno['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
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

<?php include '_detalhes.php'; ?>

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

function viewAlunoDetails(alunoId) {
        // Resetar áreas de exibição
        document.getElementById('infoDisplay').style.display = 'none';
        document.getElementById('infoForm').style.display = 'none';
        document.getElementById('infoLoading').style.display = 'block';
        document.getElementById('saveInfoBtn').style.display = 'none';
        document.getElementById('cancelEditBtn').style.display = 'none';
        
        // Atualizar ID do aluno no formulário
        document.getElementById('edit_aluno_id').value = alunoId;
        
        // Buscar informações do aluno via AJAX
        fetch(`/alunos/get-info?aluno_id=${alunoId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('infoLoading').style.display = 'none';
                
                if (data.success && data.info) {
                    // Preencher os campos de exibição
                    document.getElementById('info_matricula').textContent = data.info.registration_number || '-';
                    document.getElementById('info_data_nascimento').textContent = formatDate(data.info.birth_date) || '-';
                    document.getElementById('info_genero').textContent = formatGender(data.info.gender) || '-';
                    document.getElementById('info_tipo_sanguineo').textContent = data.info.blood_type || '-';
                    
                    // Formatar endereço completo
                    const endereco = formatAddress(data.info);
                    document.getElementById('info_endereco').textContent = endereco || '-';
                    
                    document.getElementById('info_contato_emergencia').textContent = data.info.emergency_contact || '-';
                    document.getElementById('info_telefone_emergencia').textContent = data.info.emergency_phone || '-';
                    document.getElementById('info_plano_saude').textContent = data.info.health_insurance || '-';
                    document.getElementById('info_obs_saude').textContent = data.info.health_observations || '-';
                    document.getElementById('info_escola_anterior').textContent = data.info.previous_school || '-';
                    document.getElementById('info_observacoes').textContent = data.info.observation || '-';
                    
                    // Preencher os campos do formulário para edição posterior
                    document.getElementById('edit_info_id').value = data.info.id;
                    document.getElementById('edit_matricula').value = data.info.registration_number || '';
                    document.getElementById('edit_data_nascimento').value = data.info.birth_date || '';
                    document.getElementById('edit_genero').value = data.info.gender || 'M';
                    document.getElementById('edit_tipo_sanguineo').value = data.info.blood_type || '';
                    document.getElementById('edit_rua').value = data.info.address_street || '';
                    document.getElementById('edit_numero').value = data.info.address_number || '';
                    document.getElementById('edit_complemento').value = data.info.address_complement || '';
                    document.getElementById('edit_bairro').value = data.info.address_district || '';
                    document.getElementById('edit_cidade').value = data.info.address_city || '';
                    document.getElementById('edit_estado').value = data.info.address_state || '';
                    document.getElementById('edit_cep').value = data.info.address_zipcode || '';
                    document.getElementById('edit_contato_emergencia').value = data.info.emergency_contact || '';
                    document.getElementById('edit_telefone_emergencia').value = data.info.emergency_phone || '';
                    document.getElementById('edit_plano_saude').value = data.info.health_insurance || '';
                    document.getElementById('edit_obs_saude').value = data.info.health_observations || '';
                    document.getElementById('edit_escola_anterior').value = data.info.previous_school || '';
                    document.getElementById('edit_observacoes').value = data.info.observation || '';
                    
                    // Mostrar área de exibição
                    document.getElementById('infoDisplay').style.display = 'block';
                } else {
                    // Se não há informações, mostrar formulário para cadastro
                    document.getElementById('infoForm').style.display = 'block';
                    document.getElementById('saveInfoBtn').style.display = 'inline-block';
                    document.getElementById('cancelEditBtn').style.display = 'none'; // Não precisa mostrar cancelar na criação
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                document.getElementById('infoLoading').style.display = 'none';
                alert('Erro ao carregar informações do aluno');
            });
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
