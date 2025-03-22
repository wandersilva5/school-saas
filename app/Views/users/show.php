<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                        </p>
                        <p class="text-muted mb-0">
                            <i class="bi bi-person-vcard"></i> <?=  htmlspecialchars($user['cpf']) ?? 'Não informado' ?>
                        </p>
                    </div>
                    <div>
                        <a href="/users" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Status</h6>
                                <?php if ($user['active']): ?>
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
                                <h6 class="card-subtitle text-muted mb-1">Perfis</h6>
                                <div>
                                    <?php
                                    $roleNames = $user['role_names'] ?? [];
                                    if (empty($roleNames) && isset($user['roles'])) {
                                        $roleNames = is_array($user['roles']) ? $user['roles'] : explode(',', $user['roles'] ?? '');
                                    }
                                    
                                    foreach ($roleNames as $roleName):
                                        if (!empty($roleName)):
                                    ?>
                                            <span class="badge bg-primary"><?= htmlspecialchars($roleName) ?></span>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Cadastro | Última Atualização</h6>
                                <h5 class="mb-0"><?= format_date($user['created_at']);?> | <?=format_date($user['updated_at']) ;?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle text-muted mb-1">Última Atualização</h6>
                                <h5 class="mb-0"><?= $user['cpf'] ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações adicionais do usuário -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h5 class="border-bottom pb-2">Informações Detalhadas</h5>
                    </div>
                </div>

                <div class="row">
                    <!-- Se existir informações específicas na tabela user_info -->
                    <?php if (isset($user_info) && !empty($user_info)): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-person"></i> Informações Pessoais</h6>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <?php if (!empty($user_info['phone'])): ?>
                                                <tr>
                                                    <th scope="row">Telefone</th>
                                                    <td><?= htmlspecialchars($user_info['phone']) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($user_info['cpf'])): ?>
                                                <tr>
                                                    <th scope="row">CPF</th>
                                                    <td><?= htmlspecialchars($user_info['cpf']) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($user_info['birth_date'])): ?>
                                                <tr>
                                                    <th scope="row">Data de Nascimento</th>
                                                    <td><?= format_date($user_info['birth_date']) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($user_info['gender'])): ?>
                                                <tr>
                                                    <th scope="row">Gênero</th>
                                                    <td>
                                                        <?= $user_info['gender'] == 'M' ? 'Masculino' : 
                                                            ($user_info['gender'] == 'F' ? 'Feminino' : 'Outro') ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if (
                            !empty($user_info['address_street']) || 
                            !empty($user_info['address_number']) || 
                            !empty($user_info['address_district']) || 
                            !empty($user_info['address_city'])
                        ): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-geo-alt"></i> Endereço</h6>
                                        <p>
                                            <?= htmlspecialchars($user_info['address_street'] ?? '') ?>
                                            <?= !empty($user_info['address_number']) ? ', ' . htmlspecialchars($user_info['address_number']) : '' ?>
                                            <?= !empty($user_info['address_complement']) ? ' - ' . htmlspecialchars($user_info['address_complement']) : '' ?>
                                            <br>
                                            <?= htmlspecialchars($user_info['address_district'] ?? '') ?>
                                            <?= !empty($user_info['address_city']) ? ', ' . htmlspecialchars($user_info['address_city']) : '' ?>
                                            <?= !empty($user_info['address_state']) ? '/' . htmlspecialchars($user_info['address_state']) : '' ?>
                                            <?= !empty($user_info['address_zipcode']) ? ' - CEP: ' . htmlspecialchars($user_info['address_zipcode']) : '' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($user_info['observation'])): ?>
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-card-text"></i> Observações</h6>
                                        <p><?= nl2br(htmlspecialchars($user_info['observation'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Não há informações adicionais cadastradas para este usuário.
                                <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#addUserInfoModal">
                                    <i class="bi bi-plus"></i> Adicionar Informações
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar/editar informações do usuário -->
<div class="modal fade" id="addUserInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= isset($user_info) ? 'Editar' : 'Adicionar' ?> Informações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userInfoForm" action="/users/update-info" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <?php if (isset($user_info['id'])): ?>
                        <input type="hidden" name="info_id" value="<?= $user_info['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control telefone" id="phone" name="phone" 
                                value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="birth_date" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                value="<?= $user_info['birth_date'] ?? '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="gender" class="form-label">Gênero</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Selecione</option>
                                <option value="M" <?= (isset($user_info['gender']) && $user_info['gender'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= (isset($user_info['gender']) && $user_info['gender'] == 'F') ? 'selected' : '' ?>>Feminino</option>
                                <option value="O" <?= (isset($user_info['gender']) && $user_info['gender'] == 'O') ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">Endereço</h5>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="address_street" class="form-label">Rua/Avenida</label>
                            <input type="text" class="form-control" id="address_street" name="address_street" 
                                value="<?= htmlspecialchars($user_info['address_street'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="address_number" class="form-label">Número</label>
                            <input type="text" class="form-control" id="address_number" name="address_number" 
                                value="<?= htmlspecialchars($user_info['address_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="address_complement" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="address_complement" name="address_complement" 
                                value="<?= htmlspecialchars($user_info['address_complement'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="address_district" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="address_district" name="address_district" 
                                value="<?= htmlspecialchars($user_info['address_district'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="address_city" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="address_city" name="address_city" 
                                value="<?= htmlspecialchars($user_info['address_city'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="address_state" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="address_state" name="address_state" maxlength="2" 
                                value="<?= htmlspecialchars($user_info['address_state'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="address_zipcode" class="form-label">CEP</label>
                            <input type="text" class="form-control cep" id="address_zipcode" name="address_zipcode" 
                                value="<?= htmlspecialchars($user_info['address_zipcode'] ?? '') ?>">
                        </div>

                        <div class="col-12 mb-3">
                            <h5 class="border-bottom pb-2">Informações Adicionais</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="observation" class="form-label">Observações</label>
                            <textarea class="form-control" id="observation" name="observation" rows="3"><?= htmlspecialchars($user_info['observation'] ?? '') ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="userInfoForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar usuário -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="/users/update/<?= $user['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Senha (deixe em branco para manter)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1" <?= $user['active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perfis</label>
                        <?php if (!empty($allRoles) && is_array($allRoles)): ?>
                            <?php 
                            $userRoleIds = is_array($user['roles']) ? $user['roles'] : [];
                            foreach ($allRoles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>"
                                        <?= in_array($role['id'], $userRoleIds) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editUserForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializa máscaras
        $('.telefone').mask('(00) 00000-0000');
        $('.cpf').mask('000.000.000-00');
        $('.cep').mask('00000-000');
    });
</script>
<?php endpush() ?>