<div class="row">
    <div class="col-12">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="card-title"><?= $pageTitle ?></h4>
            </div>
            <?php if ($hasSecretariaRole): ?>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAlertModal">
                        <i class="bi bi-plus"></i> Novo Alerta
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Prioridade</th>
                                <th>Destinatários</th>
                                <th>Data Início</th>
                                <th>Data Fim</th>
                                <th>Criado por</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($alerts)): ?>
                                <?php foreach ($alerts as $alert): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($alert['title']) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-info';
                                            if ($alert['priority'] === 'alta') {
                                                $badgeClass = 'bg-danger';
                                            } elseif ($alert['priority'] === 'média') {
                                                $badgeClass = 'bg-warning';
                                            } elseif ($alert['priority'] === 'baixa') {
                                                $badgeClass = 'bg-success';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst(htmlspecialchars($alert['priority'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($alert['target_roles'] === 'all'): ?>
                                                <span class="badge bg-secondary">Todos</span>
                                            <?php else: ?>
                                                <?php
                                                $roleNames = explode(',', $alert['target_roles']);
                                                foreach ($roleNames as $roleName):
                                                ?>
                                                    <span class="badge bg-primary"><?= htmlspecialchars($roleName) ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $alert['start_date'] ? date('d/m/Y', strtotime($alert['start_date'])) : 'Indefinido' ?></td>
                                        <td><?= $alert['end_date'] ? date('d/m/Y', strtotime($alert['end_date'])) : 'Indefinido' ?></td>
                                        <td><?= htmlspecialchars($alert['created_by_name'] ?? 'Sistema') ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" onclick="viewAlert(<?= $alert['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <?php if ($hasSecretariaRole): ?>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="editAlert(<?= $alert['id'] ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteAlert(<?= $alert['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Nenhum alerta encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Navegação de página">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/alerts?page=<?= $currentPage - 1 ?>">Anterior</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="/alerts?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="/alerts?page=<?= $currentPage + 1 ?>">Próxima</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Alerta -->
<?php if ($hasSecretariaRole): ?>
    <div class="modal fade" id="createAlertModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/alerts/store" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="priority" class="form-label">Prioridade</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="baixa">Baixa</option>
                                    <option value="média">Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                                <small class="text-muted">Deixe em branco para disponibilizar imediatamente</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <small class="text-muted">Deixe em branco para não definir prazo</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destinatários</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="target_all" checked>
                                <label class="form-check-label" for="target_all">
                                    Todos os perfis
                                </label>
                            </div>
                            <div id="roles_container" class="mt-2" style="display: none;">
                                <?php foreach ($roles as $role): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input role-checkbox" type="checkbox" name="target_roles[]"
                                            id="role_<?= $role['id'] ?>" value="<?= $role['name'] ?>">
                                        <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
<?php endif; ?>

<!-- Modal Visualizar Alerta -->
<div class="modal fade" id="viewAlertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <span id="view_priority_badge" class="badge mb-2"></span>
                    <p><strong>Destinatários:</strong> <span id="view_target_roles"></span></p>
                    <p><strong>Período:</strong> <span id="view_period"></span></p>
                    <p><strong>Criado por:</strong> <span id="view_created_by"></span></p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p id="view_message"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Alerta -->
<?php if ($hasSecretariaRole): ?>
    <div class="modal fade" id="editAlertModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Alerta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAlertForm" action="/alerts/update/" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_priority" class="form-label">Prioridade</label>
                                <select class="form-select" id="edit_priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="baixa">Baixa</option>
                                    <option value="média">Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_message" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="edit_message" name="message" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_start_date" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date">
                                <small class="text-muted">Deixe em branco para disponibilizar imediatamente</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_end_date" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date">
                                <small class="text-muted">Deixe em branco para não definir prazo</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Destinatários</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_target_all">
                                <label class="form-check-label" for="edit_target_all">
                                    Todos os perfis
                                </label>
                            </div>
                            <div id="edit_roles_container" class="mt-2">
                                <?php foreach ($roles as $role): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input edit-role-checkbox" type="checkbox" name="target_roles[]"
                                            id="edit_role_<?= $role['id'] ?>" value="<?= $role['name'] ?>">
                                        <label class="form-check-label" for="edit_role_<?= $role['id'] ?>">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
<?php endif; ?>

<?php push('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcionalidade para alternar seleção de destinatários (criar)
        const targetAllCheckbox = document.getElementById('target_all');
        const rolesContainer = document.getElementById('roles_container');
        const roleCheckboxes = document.querySelectorAll('.role-checkbox');

        if (targetAllCheckbox) {
            targetAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    rolesContainer.style.display = 'none';
                    roleCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                } else {
                    rolesContainer.style.display = 'block';
                }
            });
        }

        // Funcionalidade para alternar seleção de destinatários (editar)
        const editTargetAllCheckbox = document.getElementById('edit_target_all');
        const editRolesContainer = document.getElementById('edit_roles_container');
        const editRoleCheckboxes = document.querySelectorAll('.edit-role-checkbox');

        if (editTargetAllCheckbox) {
            editTargetAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    editRolesContainer.style.display = 'none';
                    editRoleCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                } else {
                    editRolesContainer.style.display = 'block';
                }
            });
        }
    });

    // Função para visualizar um alerta
    function viewAlert(id) {
        fetch(`/alerts/get-by-id?id=${id}`, {
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
                // Preencher os dados no modal
                document.getElementById('view_title').textContent = data.title;
                document.getElementById('view_message').textContent = data.message;
                document.getElementById('view_created_by').textContent = data.created_by_name || 'Sistema';

                // Definir a badge de prioridade
                const priorityBadge = document.getElementById('view_priority_badge');
                let badgeClass = 'bg-info';
                if (data.priority === 'alta') {
                    badgeClass = 'bg-danger';
                } else if (data.priority === 'média') {
                    badgeClass = 'bg-warning';
                } else if (data.priority === 'baixa') {
                    badgeClass = 'bg-success';
                }
                priorityBadge.className = `badge ${badgeClass}`;
                priorityBadge.textContent = data.priority.charAt(0).toUpperCase() + data.priority.slice(1);

                // Definir os destinatários
                let targetRolesText = 'Todos';
                if (data.target_roles !== 'all') {
                    targetRolesText = data.target_roles.split(',').join(', ');
                }
                document.getElementById('view_target_roles').textContent = targetRolesText;

                // Definir o período
                let periodText = 'Sempre visível';
                if (data.start_date && data.end_date) {
                    const startDate = new Date(data.start_date);
                    const endDate = new Date(data.end_date);
                    periodText = `${startDate.toLocaleDateString()} até ${endDate.toLocaleDateString()}`;
                } else if (data.start_date) {
                    const startDate = new Date(data.start_date);
                    periodText = `A partir de ${startDate.toLocaleDateString()}`;
                } else if (data.end_date) {
                    const endDate = new Date(data.end_date);
                    periodText = `Até ${endDate.toLocaleDateString()}`;
                }
                document.getElementById('view_period').textContent = periodText;

                // Abrir o modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewAlertModal'));
                viewModal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar o alerta: ' + error.message);
            });
    }

    // Função para editar um alerta
    function editAlert(id) {
        fetch(`/alerts/get-by-id?id=${id}`, {
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
                // Preencher o formulário de edição
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_title').value = data.title;
                document.getElementById('edit_message').value = data.message;
                document.getElementById('edit_priority').value = data.priority;

                if (data.start_date) {
                    document.getElementById('edit_start_date').value = data.start_date;
                } else {
                    document.getElementById('edit_start_date').value = '';
                }

                if (data.end_date) {
                    document.getElementById('edit_end_date').value = data.end_date;
                } else {
                    document.getElementById('edit_end_date').value = '';
                }

                // Atualizar destinatários
                const editTargetAllCheckbox = document.getElementById('edit_target_all');
                const editRolesContainer = document.getElementById('edit_roles_container');
                const editRoleCheckboxes = document.querySelectorAll('.edit-role-checkbox');

                if (data.target_roles === 'all') {
                    editTargetAllCheckbox.checked = true;
                    editRolesContainer.style.display = 'none';
                    editRoleCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                } else {
                    editTargetAllCheckbox.checked = false;
                    editRolesContainer.style.display = 'block';

                    // Limpar todas as seleções primeiro
                    editRoleCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Marcar os perfis selecionados
                    const selectedRoles = data.selected_roles || [];
                    editRoleCheckboxes.forEach(checkbox => {
                        if (selectedRoles.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                }

                // Atualizar a URL do formulário
                document.getElementById('editAlertForm').action = `/alerts/update/${data.id}`;

                // Abrir o modal
                const editModal = new bootstrap.Modal(document.getElementById('editAlertModal'));
                editModal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar o alerta para edição: ' + error.message);
            });
    }

    // Função para excluir um alerta
    function deleteAlert(id) {
        if (confirm('Tem certeza que deseja excluir este alerta?')) {
            fetch(`/alerts/delete/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Erro na requisição');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Reload para atualizar a lista
                        window.location.reload();
                    } else {
                        throw new Error(data.error || 'Operação não realizada');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir o alerta: ' + error.message);
                });
        }
    }
</script>
<?php endpush() ?>