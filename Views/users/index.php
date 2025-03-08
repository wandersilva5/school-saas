<div class="row">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="bi bi-plus-circle"></i> Novo Usuário
                        </button>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfis</th>
                                <th>Data Cadastro</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $roles = explode(',', $user['roles'] ?? '');
                                        foreach ($roles as $role):
                                            if (!empty($role)):
                                        ?>
                                                <span class="badge bg-primary"><?= htmlspecialchars($role) ?></span>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"  
                                        data-bs-toggle="modal" 
                                        data-bs-target="#userEditModal" onclick="editUser(<?= $user['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($totalPages > 1): ?>
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

<!-- Modal de Cadastro -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" action="/users/store" method="POST">
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

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Ativo</label>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Perfil</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Selecione um perfil</option>
                            <?php if (!empty($availableRoles) && is_array($roles)): ?>
                                <?php foreach ($availableRoles as $role): ?>
                                    <?php if (isset($role['id']) && isset($role['name'])): ?>
                                        <option value="<?= $role['id'] ?>">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione um perfil.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="userForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="userEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userEditForm" action="/users/update" method="POST">
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
                        <label for="edit_password" class="form-label">Senha (deixe em branco para manter)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role_id" class="form-label">Perfil</label>
                        <select class="form-select" id="edit_role_id" name="role_id" required>
                            <option value="">Selecione um perfil</option>
                            <?php if (!empty($availableRoles) && is_array($availableRoles)): ?>
                                <?php foreach ($availableRoles as $role): ?>
                                    <?php if (isset($role['id']) && isset($role['name'])): ?>
                                        <option value="<?= $role['id'] ?>">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="userEditForm" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>

    function editUser(userId) {
        // Resetar o formulário antes de preencher com novos dados
        document.getElementById('userEditForm').reset();

        // Fazer uma requisição AJAX para obter os dados do usuário
        fetch(`/users/get/${userId}`)
            .then(response => response.json())
            .then(data => {
                // Preencher o formulário com os dados do usuário
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_email').value = data.email;

                // Marcar o checkbox de ativo de acordo com o status do usuário
                document.getElementById('edit_active').checked = data.active == 1;

                // Selecionar o perfil do usuário (assumindo que role_id está no primeiro elemento do array)
                if (data.user_roles && data.user_roles.length > 0) {
                    document.getElementById('edit_role_id').value = data.user_roles[0].role_id;
                }

                // Abrir o modal
                const editModal = new bootstrap.Modal(document.getElementById('userEditModal'));
                editModal.show();
            })
            .catch(error => {
                console.error('Erro ao buscar dados do usuário:', error);
                alert('Erro ao carregar dados do usuário. Por favor, tente novamente.');
            });
    }

    function deleteUser(userId) {
        if (confirm('Tem certeza que deseja excluir este usuário?')) {
            window.location.href = `/users/delete/${userId}`;
        }
    }

    // Corrigir problema de modal travado
    document.addEventListener('DOMContentLoaded', function() {
        // Garantir que os backdrops sejam removidos ao fechar modais
        const modals = ['userModal', 'userEditModal'];
        
        modals.forEach(modalId => {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function () {
                    // Remover qualquer backdrop que possa ter ficado
                    const backdrops = document.getElementsByClassName('modal-backdrop');
                    while(backdrops.length > 0) {
                        backdrops[0].parentNode.removeChild(backdrops[0]);
                    }
                    // Remover a classe modal-open do body
                    document.body.classList.remove('modal-open');
                    // Remover o estilo inline do body
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                });
            }
        });
    });
</script>
<?php endpush() ?>