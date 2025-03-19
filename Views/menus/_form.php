<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="url" class="form-label">URL</label>
            <input type="text" class="form-control" id="url" name="url" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="icon" class="form-label">Ícone</label>
            <div class="input-group">
                <input type="text" class="form-control" id="icon" name="icon" required>
                <button class="btn btn-outline-secondary" type="button" id="icon-picker">
                    <i class="bi bi-card-image"></i>
                </button>

                <!-- O ícone selecionado aparecerá aqui -->
            </div>
            <small class="form-text text-muted">Clique no botão para selecionar um ícone</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="header" class="form-label">Seção</label>
            <select class="form-select" id="header" name="header" required>
                <option value="">Selecione...</option>
                <?php foreach ($headers as $header): ?>
                    <option value="<?= htmlspecialchars($header['header']) ?>">
                        <?= htmlspecialchars($header['header']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="route" class="form-label">Rota</label>
            <input type="text" class="form-control" id="route" name="route" required>
            <small class="form-text text-muted">Nome da rota para ativação do menu</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="order_index" class="form-label">Ordem</label>
            <input type="number" class="form-control" id="order_index" name="order_index" value="0" required>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Papéis Necessários</label>
            <div class="border rounded p-3">
                <?php foreach ($roles as $role): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            name="required_roles[]"
                            value="<?= htmlspecialchars($role['name']) ?>"
                            id="role_<?= $role['id'] ?>">
                        <label class="form-check-label" for="role_<?= $role['id'] ?>">
                            <?= htmlspecialchars($role['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small class="form-text text-muted">Selecione um ou mais papéis</small>
        </div>
    </div>
</div>
