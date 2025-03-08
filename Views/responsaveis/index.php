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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#responsavelModal">
                            <i class="bi bi-plus-circle"></i> Novo Responsável
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>CPF</th>
                                <th>Alunos</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($responsaveis as $responsavel): ?>
                                <tr>
                                    <td><?= htmlspecialchars($responsavel['nome']) ?></td>
                                    <td><?= htmlspecialchars($responsavel['email']) ?></td>
                                    <td><?= htmlspecialchars($responsavel['telefone']) ?></td>
                                    <td><?= htmlspecialchars($responsavel['cpf']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $responsavel['total_alunos'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($responsavel['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"  
                                        data-bs-toggle="modal" 
                                        data-bs-target="#responsavelEditModal" onclick="editResponsavel(<?= $responsavel['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteResponsavel(<?= $responsavel['id'] ?>)">
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
<div class="modal fade" id="responsavelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Responsável</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="responsavelForm" action="/responsaveis/store" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="responsavelForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="responsavelEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Responsável</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="responsavelEditForm" action="/responsaveis/update" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="edit_cpf" name="cpf" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="responsavelEditForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function editResponsavel(id) {
        // Resetar o formulário antes de preencher com novos dados
        document.getElementById('responsavelEditForm').reset();
        
        // Buscar dados do responsável via AJAX
        fetch(`/responsaveis/getById?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_nome').value = data.nome;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_telefone').value = data.telefone;
                document.getElementById('edit_cpf').value = data.cpf;
                document.getElementById('edit_active').checked = data.active == 1;
            })
            .catch(error => console.error('Erro:', error));
    }

    function deleteResponsavel(id) {
        if (confirm('Tem certeza que deseja excluir este responsável?')) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('/responsaveis/delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erro ao excluir responsável: ' + data.error);
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    }
</script>
