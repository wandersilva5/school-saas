<div class="row">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

<?php push('scripts') ?>
<script>
    function editClass(id) {
        // Resetar o formulário antes de preencher com novos dados
        document.getElementById('classEditForm').reset();
        
        // Buscar dados da turma via AJAX
        fetch(`/classes/getById?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                return response.json();
            })
            .then(data => {
                // Preencher o formulário com os dados
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_shift').value = data.shift;
                document.getElementById('edit_year').value = data.year;
                document.getElementById('edit_capacity').value = data.capacity;
                document.getElementById('edit_active').checked = data.active == 1;
                
                // Abrir o modal
                const modal = new bootstrap.Modal(document.getElementById('classEditModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar dados da turma: ' + error.message);
            });
    }

    function deleteClass(id) {
        if (confirm('Tem certeza que deseja excluir esta turma?')) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('/classes/delete', {
                method: 'POST',
                body: formData,
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
                if (data.success) {
                    // Exibir toast via redirecionamento e sessão
                    window.location.href = '/classes?success=1';
                } else {
                    window.location.href = `/classes?error=${encodeURIComponent(data.error || 'Erro desconhecido')}`;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                window.location.href = `/classes?error=${encodeURIComponent(error.message)}`;
            });
        }
    }
    
    // Função para exibir toast
    function showToast(type, message) {
        const toastEl = document.getElementById('toast-notification');
        const toast = new bootstrap.Toast(toastEl);
        
        // Configurar aparência do toast
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        if (type === 'success') {
            toastEl.classList.add('bg-success', 'text-white');
        } else if (type === 'error') {
            toastEl.classList.add('bg-danger', 'text-white');
        } else if (type === 'warning') {
            toastEl.classList.add('bg-warning');
        } else if (type === 'info') {
            toastEl.classList.add('bg-info');
        }
        
        // Definir a mensagem
        document.querySelector('.toast-body').textContent = message;
        
        // Mostrar o toast
        toast.show();
    }
</script>
<?php endpush() ?>
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal">
                            <i class="bi bi-plus-circle"></i> Nova Turma
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="/classes">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="name" class="form-label">Nome da Turma</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= isset($filters['name']) ? $filters['name'] : '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="shift" class="form-label">Turno</label>
                                    <select class="form-select" id="shift" name="shift">
                                        <option value="">Todos</option>
                                        <option value="Manhã" <?= isset($filters['shift']) && $filters['shift'] === 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                                        <option value="Tarde" <?= isset($filters['shift']) && $filters['shift'] === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                                        <option value="Noite" <?= isset($filters['shift']) && $filters['shift'] === 'Noite' ? 'selected' : '' ?>>Noite</option>
                                        <option value="Integral" <?= isset($filters['shift']) && $filters['shift'] === 'Integral' ? 'selected' : '' ?>>Integral</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="year" class="form-label">Ano Letivo</label>
                                    <input type="number" class="form-control" id="year" name="year" value="<?= isset($filters['year']) ? $filters['year'] : '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="capacity" class="form-label">Capacidade Mínima</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="<?= isset($filters['capacity']) ? $filters['capacity'] : '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Todos</option>
                                        <option value="1" <?= isset($filters['status']) && $filters['status'] === '1' ? 'selected' : '' ?>>Ativa</option>
                                        <option value="0" <?= isset($filters['status']) && $filters['status'] === '0' ? 'selected' : '' ?>>Inativa</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <a href="/classes" class="btn btn-outline-secondary me-2">Limpar</a>
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Turno</th>
                                <th>Ano Letivo</th>
                                <th>Capacidade</th>
                                <th>Alunos</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?= htmlspecialchars($class['name']) ?></td>
                                    <td><?= htmlspecialchars($class['shift']) ?></td>
                                    <td><?= htmlspecialchars($class['year']) ?></td>
                                    <td><?= htmlspecialchars($class['capacity']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $class['student_count'] ?>/<?= $class['capacity'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($class['active'] === 1): ?>
                                            <span class="badge bg-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/classes/show/<?= $class['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-primary" onclick="editClass(<?= $class['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteClass(<?= $class['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Update pagination links to include filters -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cadastro -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="classForm" action="/classes/store" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da Turma</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="shift" class="form-label">Turno</label>
                        <select class="form-select" id="shift" name="shift" required>
                            <option value="">Selecione o turno</option>
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Noite">Noite</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Ano Letivo</label>
                        <input type="number" class="form-control" id="year" name="year" min="2020" max="2030" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacidade</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="100" value="30" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="classForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="classEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="classEditForm" action="/classes/update" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome da Turma</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_shift" class="form-label">Turno</label>
                        <select class="form-select" id="edit_shift" name="shift" required>
                            <option value="">Selecione o turno</option>
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Noite">Noite</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_year" class="form-label">Ano Letivo</label>
                        <input type="number" class="form-control" id="edit_year" name="year" min="2020" max="2030" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_capacity" class="form-label">Capacidade</label>
                        <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" max="100" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativa</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="classEditForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>