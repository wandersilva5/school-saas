<!-- Content -->
<div class="row">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Operação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-heard">
            <div class="d-flex justify-content-between align-items-center mt-2">
                <h1 class="h3 mb-0"><? print($pageTitle)  ?></h1>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 ms-auto">
                    <!-- Botão para abrir o modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#institutionModal">
                        <i class="fas fa-user-plus"></i> Criar Nova instituição
                    </button>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class=" table-primary">
                        <tr>
                            <th>Logo</th>
                            <th>Nome</th>
                            <th>Domínio</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($institutions as $institution): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($institution['logo_url']) ?? "" ?>" alt="logo" width="80"></td>
                                <td><?= htmlspecialchars($institution['name']) ?></td>
                                <td><?= htmlspecialchars($institution['domain']) ?></td>
                                <td><?= date('d/m/Y', strtotime($institution['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Próximo</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal de Novo Usuário -->
<div class="modal fade" id="institutionModal" tabindex="-1" aria-labelledby="institutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="institutionModalLabel">Cadastro de Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="institutionForm" method="POST" action="/institutions/store">
                    <!-- Campo Nome -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required minlength="3" maxlength="100"
                            placeholder="Digite o nome completo">
                        <div class="invalid-feedback">
                            Por favor, informe um nome válido.
                        </div>
                    </div>

                    <!-- Campo Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Por favor, informe um email válido.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="institutionForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>


<script>
    function editRoles(userId, userName, currentRoles) {
        document.getElementById('userId').value = userId;
        document.getElementById('userName').textContent = userName;

        // Limpa todas as checkboxes
        document.querySelectorAll('#editRolesModal input[type="checkbox"]').forEach(cb => cb.checked = false);

        // Marca as checkboxes dos roles atuais
        if (currentRoles) {
            const roles = currentRoles.split(',');
            roles.forEach(role => {
                const cb = document.querySelector(`#editRolesModal input[value="${role.trim()}"]`);
                if (cb) cb.checked = true;
            });
        }

        new bootstrap.Modal(document.getElementById('editRolesModal')).show();
    }
</script>
