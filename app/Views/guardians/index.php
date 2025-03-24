<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGuardianModal">
                            <i class="bi bi-plus"></i> Novo Responsável
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guardians as $guardian): ?>
                                <tr>
                                    <td><?= htmlspecialchars($guardian['name']) ?></td>
                                    <td><?= htmlspecialchars($guardian['email']) ?></td>
                                    <td><?= $guardian['phone'] ?></td>
                                    <td>
                                        <a href="/users/edit/<?= $guardian['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
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

<!-- Modal Criar Responsável -->
<div class="modal fade" id="createGuardianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Responsável</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/guardians/store" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control cpf" id="cpf" name="cpf" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control phone" id="phone" name="phone" required>
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

<!-- Modal Editar Responsável -->
<div class="modal fade" id="editGuardianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Responsável</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGuardianForm" action="/guardians/update/" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control cpf" id="edit_cpf" name="cpf" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control phone" id="edit_phone" name="phone" required>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializa as máscaras
        $('.cpf').mask('000.000.000-00');
        $('.phone').mask('(00) 00000-0000');
    });

    function editGuardian(guardian) {
        // Atualiza a action do formulário
        $('#editGuardianForm').attr('action', `/guardians/update/${guardian.id}`);

        // Preenche os campos
        $('#edit_id').val(guardian.id);
        $('#edit_name').val(guardian.name);
        $('#edit_cpf').val(guardian.cpf);
        $('#edit_email').val(guardian.email);
        $('#edit_phone').val(guardian.phone);
        $('#edit_address').val(guardian.address);

        // Abre o modal
        $('#editGuardianModal').modal('show');
    }

    function deleteGuardian(id) {
        if (confirm('Tem certeza que deseja excluir este responsável?')) {
            window.location.href = `/guardians/delete/${id}`;
        }
    }
</script>
<?php endpush() ?>