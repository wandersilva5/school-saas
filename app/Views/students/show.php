<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($student['name']) ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($student['email']) ?>
                        </p>
                        <p class="text-muted mb-0">
                            <i class="bi bi-telephone"></i> <?= htmlspecialchars($student['phone'] ?? 'Não informado') ?>
                        </p>
                    </div>
                    <div>
                        <a href="/students" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Status</h6>
                                <?php if ($student['active']): ?>
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
                                <h6 class="card-subtitle text-muted mb-1">Matrícula</h6>
                                <h5 class="mb-0"><?= htmlspecialchars($student_info['registration_number'] ?? 'Não informado') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Data de Nascimento</h6>
                                <h5 class="mb-0"><?= isset($student_info['birth_date']) ? format_date($student_info['birth_date']) : 'Não informado' ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Idade</h6>
                                <h5 class="mb-0"><?= isset($student_info['birth_date']) ? calculate_age($student_info['birth_date']) . ' anos' : 'Não informado' ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abas de informações -->
                <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                            <i class="bi bi-info-circle"></i> Informações Pessoais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab" aria-controls="academic" aria-selected="false">
                            <i class="bi bi-book"></i> Dados Acadêmicos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab" aria-controls="medical" aria-selected="false">
                            <i class="bi bi-heart-pulse"></i> Dados Médicos
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="studentTabsContent">
                    <!-- Informações Pessoais -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Dados Pessoais</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Nome Completo</th>
                                            <td><?= htmlspecialchars($student['name']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?= htmlspecialchars($student['email']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Telefone</th>
                                            <td><?= htmlspecialchars($student['phone'] ?? 'Não informado') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Data de Nascimento</th>
                                            <td><?= isset($student_info['birth_date']) ? format_date($student_info['birth_date']) : 'Não informado' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Gênero</th>
                                            <td>
                                                <?php
                                                $gender = $student_info['gender'] ?? '';
                                                if ($gender == 'M') echo 'Masculino';
                                                elseif ($gender == 'F') echo 'Feminino';
                                                elseif ($gender == 'O') echo 'Outro';
                                                else echo 'Não informado';
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Endereço</h5>
                                <?php if (
                                    isset($student_info['address_street']) ||
                                    isset($student_info['address_number']) ||
                                    isset($student_info['address_district']) ||
                                    isset($student_info['address_city'])
                                ): ?>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th width="40%">Logradouro</th>
                                                <td>
                                                    <?= htmlspecialchars($student_info['address_street'] ?? 'Não informado') ?>
                                                    <?= !empty($student_info['address_number']) ? ', ' . htmlspecialchars($student_info['address_number']) : '' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Complemento</th>
                                                <td><?= htmlspecialchars($student_info['address_complement'] ?? 'Não informado') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Bairro</th>
                                                <td><?= htmlspecialchars($student_info['address_district'] ?? 'Não informado') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Cidade/UF</th>
                                                <td>
                                                    <?= htmlspecialchars($student_info['address_city'] ?? 'Não informada') ?>
                                                    <?= !empty($student_info['address_state']) ? '/' . htmlspecialchars($student_info['address_state']) : '' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>CEP</th>
                                                <td><?= htmlspecialchars($student_info['address_zipcode'] ?? 'Não informado') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Endereço não informado
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-12 mt-3">
                                <h5 class="border-bottom pb-2 mb-3">Responsável</h5>
                                <?php if (isset($guardian) && !empty($guardian)): ?>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th width="20%">Nome</th>
                                                <td><?= htmlspecialchars($guardian['name']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><?= htmlspecialchars($guardian['email']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Telefone</th>
                                                <td><?= htmlspecialchars($guardian['phone'] ?? 'Não informado') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Nenhum responsável vinculado
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Dados Acadêmicos -->
                    <div class="tab-pane fade" id="academic" role="tabpanel" aria-labelledby="academic-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Matrícula e Situação</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Número de Matrícula</th>
                                            <td><?= htmlspecialchars($student_info['registration_number'] ?? 'Não informado') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php if ($student['active']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inativo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data de Cadastro</th>
                                            <td><?= format_date($student['created_at']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Escola Anterior</th>
                                            <td><?= htmlspecialchars($student_info['previous_school'] ?? 'Não informado') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Turmas</h5>
                                <?php if (isset($classes) && !empty($classes)): ?>
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Turma</th>
                                                <th>Turno</th>
                                                <th>Ano</th>
                                                <th>Situação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($classes as $class): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($class['name']) ?></td>
                                                    <td><?= htmlspecialchars($class['shift']) ?></td>
                                                    <td><?= htmlspecialchars($class['year']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $class['status'] === 'Ativo' ? 'success' : 'secondary' ?>">
                                                            <?= htmlspecialchars($class['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Aluno não está matriculado em nenhuma turma
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (isset($student_info['observation']) && !empty($student_info['observation'])): ?>
                                <div class="col-md-12 mt-3">
                                    <h5 class="border-bottom pb-2 mb-3">Observações Acadêmicas</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <?= nl2br(htmlspecialchars($student_info['observation'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dados Médicos -->
                    <div class="tab-pane fade" id="medical" role="tabpanel" aria-labelledby="medical-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Informações Médicas</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Tipo Sanguíneo</th>
                                            <td><?= htmlspecialchars($student_info['blood_type'] ?? 'Não informado') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Plano de Saúde</th>
                                            <td><?= htmlspecialchars($student_info['health_insurance'] ?? 'Não informado') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Contato de Emergência</h5>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Nome</th>
                                            <td><?= htmlspecialchars($student_info['emergency_contact'] ?? 'Não informado') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Telefone</th>
                                            <td><?= htmlspecialchars($student_info['emergency_phone'] ?? 'Não informado') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (isset($student_info['health_observations']) && !empty($student_info['health_observations'])): ?>
                                <div class="col-md-12 mt-3">
                                    <h5 class="border-bottom pb-2 mb-3">Observações de Saúde</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <?= nl2br(htmlspecialchars($student_info['health_observations'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-md-12 mt-3">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Não há observações médicas registradas
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar aluno -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editStudentForm" action="/students/update/<?= $student['id'] ?>" method="POST">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">Dados Básicos</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address-info" type="button" role="tab" aria-controls="address-info" aria-selected="false">Endereço</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-info-tab" data-bs-toggle="tab" data-bs-target="#medical-info" type="button" role="tab" aria-controls="medical-info" aria-selected="false">Informações Médicas</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0" id="myTabContent">
                        <!-- Dados Básicos -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="registration_number" class="form-label">Número de Matrícula</label>
                                    <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= htmlspecialchars($student_info['registration_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-9 mb-6">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="phone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone" id="phone" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                                </div>
                               
                                <div class="col-md-3 mb-3">
                                    <label for="birth_date" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= $student_info['birth_date'] ?? '' ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="gender" class="form-label">Gênero</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Selecione</option>
                                        <option value="M" <?= (isset($student_info['gender']) && $student_info['gender'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                                        <option value="F" <?= (isset($student_info['gender']) && $student_info['gender'] == 'F') ? 'selected' : '' ?>>Feminino</option>
                                        <option value="O" <?= (isset($student_info['gender']) && $student_info['gender'] == 'O') ? 'selected' : '' ?>>Outro</option>
                                    </select>
                                </div>
                                <div class="col-md-9 mb-3">
                                    <label for="previous_school" class="form-label">Escola Anterior</label>
                                    <input type="text" class="form-control" id="previous_school" name="previous_school" value="<?= htmlspecialchars($student_info['previous_school'] ?? '') ?>">
                                </div>
                                <div class="col-md-12 mb-8">
                                    <label for="guardian_id" class="form-label">Responsável</label>
                                    <select class="form-select" id="guardian_id" name="guardian_id">
                                        <option value="">Selecione um responsável</option>
                                        <?php foreach ($guardians as $guardian_item): ?>
                                            <option value="<?= $guardian_item['id'] ?>" <?= (isset($student['guardian_id']) && $student['guardian_id'] == $guardian_item['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($guardian_item['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" <?= $student['active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="active">Ativo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="tab-pane fade" id="address-info" role="tabpanel" aria-labelledby="address-tab">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="address_street" class="form-label">Logradouro</label>
                                    <input type="text" class="form-control" id="address_street" name="address_street" value="<?= htmlspecialchars($student_info['address_street'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="address_number" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="address_number" name="address_number" value="<?= htmlspecialchars($student_info['address_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="address_complement" class="form-label">Complemento</label>
                                    <input type="text" class="form-control" id="address_complement" name="address_complement" value="<?= htmlspecialchars($student_info['address_complement'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="address_district" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="address_district" name="address_district" value="<?= htmlspecialchars($student_info['address_district'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="address_zipcode" class="form-label">CEP</label>
                                    <input type="text" class="form-control cep" id="address_zipcode" name="address_zipcode" value="<?= htmlspecialchars($student_info['address_zipcode'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address_city" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="address_city" name="address_city" value="<?= htmlspecialchars($student_info['address_city'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address_state" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="address_state" name="address_state" maxlength="2" value="<?= htmlspecialchars($student_info['address_state'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Informações Médicas -->
                        <div class="tab-pane fade" id="medical-info" role="tabpanel" aria-labelledby="medical-info-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_type" class="form-label">Tipo Sanguíneo</label>
                                    <select class="form-select" id="blood_type" name="blood_type">
                                        <option value="">Selecione</option>
                                        <?php
                                        $blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        foreach ($blood_types as $type):
                                        ?>
                                            <option value="<?= $type ?>" <?= (isset($student_info['blood_type']) && $student_info['blood_type'] == $type) ? 'selected' : '' ?>><?= $type ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="health_insurance" class="form-label">Plano de Saúde</label>
                                    <input type="text" class="form-control" id="health_insurance" name="health_insurance" value="<?= htmlspecialchars($student_info['health_insurance'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_contact" class="form-label">Contato de Emergência</label>
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="<?= htmlspecialchars($student_info['emergency_contact'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_phone" class="form-label">Telefone de Emergência</label>
                                    <input type="text" class="form-control telefone" id="emergency_phone" name="emergency_phone" value="<?= htmlspecialchars($student_info['emergency_phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="health_observations" class="form-label">Observações de Saúde</label>
                                    <textarea class="form-control" id="health_observations" name="health_observations" rows="3"><?= htmlspecialchars($student_info['health_observations'] ?? '') ?></textarea>
                                    <small class="text-muted">Informe aqui alergias, condições médicas, medicações de uso contínuo, etc.</small>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="observation" class="form-label">Observações Gerais</label>
                                    <textarea class="form-control" id="observation" name="observation" rows="3"><?= htmlspecialchars($student_info['observation'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editStudentForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
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
</script>
<?php endpush() ?>