<div class="row">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operau00e7u00e3o realizada com sucesso!
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#alunoModal">
                            <i class="bi bi-plus-circle"></i> Novo Aluno
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Matrícula</th>
                                <th>Data de Nascimento</th>
                                <th>Responsável</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos as $aluno): ?>
                                <tr>
                                    <td><?= htmlspecialchars($aluno['nome']) ?></td>
                                    <td><?= htmlspecialchars($aluno['matricula']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($aluno['data_nascimento'])) ?></td>
                                    <td>
                                        <?php if ($aluno['responsavel_nome']): ?>
                                            <a href="/responsaveis/show/<?= $aluno['responsavel_id'] ?>">
                                                <?= htmlspecialchars($aluno['responsavel_nome']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Sem responsu00e1vel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($aluno['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"  
                                        data-bs-toggle="modal" 
                                        data-bs-target="#alunoEditModal" onclick="editAluno(<?= $aluno['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteAluno(<?= $aluno['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginau00e7u00e3o -->
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
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Pru00f3ximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cadastro -->
<div class="modal fade" id="alunoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="alunoForm" action="/alunos/store" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="matricula" class="form-label">Matrícula</label>
                        <input type="text" class="form-control" id="matricula" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="mb-3">
                        <label for="responsavel_id" class="form-label">Responsável</label>
                        <select class="form-select" id="responsavel_id" name="responsavel_id" required>
                            <option value="">Selecione um responsável</option>
                            <?php foreach ($responsaveis as $responsavel): ?>
                                <option value="<?= $responsavel['id'] ?>">
                                    <?= htmlspecialchars($responsavel['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="alunoForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ediu00e7u00e3o -->
<div class="modal fade" id="alunoEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="alunoEditForm" action="/alunos/update" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_matricula" class="form-label">Matru00edcula</label>
                        <input type="text" class="form-control" id="edit_matricula" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="edit_data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_responsavel_id" class="form-label">Responsu00e1vel</label>
                        <select class="form-select" id="edit_responsavel_id" name="responsavel_id" required>
                            <option value="">Selecione um responsu00e1vel</option>
                            <?php foreach ($responsaveis as $responsavel): ?>
                                <option value="<?= $responsavel['id'] ?>">
                                    <?= htmlspecialchars($responsavel['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="alunoEditForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function editAluno(id) {
        // Resetar o formulu00e1rio antes de preencher com novos dados
        document.getElementById('alunoEditForm').reset();
        
        // Buscar dados do aluno via AJAX
        fetch(`/alunos/getById?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_nome').value = data.nome;
                document.getElementById('edit_matricula').value = data.matricula;
                document.getElementById('edit_data_nascimento').value = data.data_nascimento;
                document.getElementById('edit_responsavel_id').value = data.responsavel_id;
                document.getElementById('edit_active').checked = data.active == 1;
            })
            .catch(error => console.error('Erro:', error));
    }

    function deleteAluno(id) {
        if (confirm('Tem certeza que deseja excluir este aluno?')) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('/alunos/delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erro ao excluir aluno: ' + data.error);
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    }
</script>
