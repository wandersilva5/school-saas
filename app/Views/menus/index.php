<div class="row">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $pageTitle ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMenuModal">
            Novo Menu
        </button>
    </div>

    <?php
    $menusByHeader = [];
    foreach ($menus as $menu) {
        $menusByHeader[$menu['header']][] = $menu;
    }
    ?>

    <div class="row">
        <?php foreach ($menusByHeader as $header => $headerMenus): ?>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="content-section">
                    <h5 class="section-title"><?= htmlspecialchars($header) ?></h5>
                    <div class="card-body">
                        <ul class="list-group menu-sortable" data-header="<?= htmlspecialchars($header) ?>">
                            <?php foreach ($headerMenus as $menu): ?>
                                <li class="list-group-item d-flex align-items-center" data-id="<?= $menu['id'] ?>">
                                    <i class="bi bi-grip-vertical me-2 handle"></i>
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <i class="bi <?= htmlspecialchars($menu['icon']) ?> me-2"></i>
                                        <span><?= htmlspecialchars($menu['name']) ?></span>
                                        <small class="text-muted ms-2">(<?= htmlspecialchars($menu['url']) ?>)</small>
                                    </div>
                                    <div class="btn-group ms-auto">
                                        <button class="btn btn-sm btn-outline-primary edit-menu"
                                            data-menu='<?= json_encode($menu) ?>'
                                            data-bs-toggle="modal"
                                            data-bs-target="#editMenuModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-menu"
                                            data-id="<?= $menu['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Criação -->
<div class="modal fade" id="createMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createMenuForm">
                    <?php include '_form.php'; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveMenu">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMenuForm">
                    <input type="hidden" id="menuId">
                    <?php include '_form.php'; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateMenu">Atualizar</button>
            </div>
        </div>
    </div>
</div>

<?php push('styles') ?>
<style>
    .menu-sortable .handle {
        cursor: move;
        color: #999;
    }

    .menu-sortable .list-group-item {
        transition: background-color 0.2s;
    }

    .menu-sortable .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .sortable-ghost {
        opacity: 0.5;
        background-color: #e9ecef;
    }
</style>
<?php endpush() ?>

<?php push('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para processar os roles selecionados
        function getSelectedRoles(form) {
            const checkboxes = form.querySelectorAll('input[name="required_roles[]"]:checked');
            return Array.from(checkboxes).map(cb => cb.value).join(',');
        }

        document.getElementById('icon').addEventListener('blur', function() {
            const iconInput = this.value.trim();
            const iconButton = document.querySelector('#icon-picker i');
            if (iconInput && iconInput.startsWith('bi-')) {
                iconButton.className = 'bi ' + iconInput;
            }
        });

        // Criar menu
        document.getElementById('saveMenu').addEventListener('click', function() {
            const form = document.getElementById('createMenuForm');
            const formData = new FormData(form);
            formData.set('required_roles', getSelectedRoles(form));

            fetch('/menus/store', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                });
        });

        // Editar menu
        document.querySelectorAll('.edit-menu').forEach(button => {
            button.addEventListener('click', function() {
                const menu = JSON.parse(this.dataset.menu);
                const form = document.getElementById('editMenuForm');
                document.getElementById('menuId').value = menu.id;

                // Preenche campos básicos
                ['name', 'url', 'icon', 'header', 'route', 'order_index'].forEach(field => {
                    form.querySelector(`[name="${field}"]`).value = menu[field];
                });

                // Preenche checkboxes de roles
                const roles = menu.required_roles.split(',');
                form.querySelectorAll('input[name="required_roles[]"]').forEach(checkbox => {
                    checkbox.checked = roles.includes(checkbox.value);
                });

                // Atualiza preview do ícone
                const inputGroup = form.querySelector('input[name="icon"]').closest('.input-group');
                let preview = inputGroup.querySelector('.selected-icon');
                if (!preview) {
                    preview = document.createElement('i');
                    preview.className = `bi ${menu.icon} ms-2 selected-icon`;
                    inputGroup.appendChild(preview);
                } else {
                    preview.className = `bi ${menu.icon} ms-2 selected-icon`;
                }
            });
        });

        // Atualizar menu
        document.getElementById('updateMenu').addEventListener('click', function() {
            const form = document.getElementById('editMenuForm');
            const formData = new FormData(form);
            const id = document.getElementById('menuId').value;
            formData.set('required_roles', getSelectedRoles(form));

            fetch(`/menus/update/${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                });
        });

        // Inicializa Sortable em todas as listas
        document.querySelectorAll('.menu-sortable').forEach(el => {
            new Sortable(el, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    const header = evt.target.dataset.header;
                    const items = Array.from(evt.target.children).map((item, index) => ({
                        id: parseInt(item.dataset.id),
                        order_index: (index + 1) * 10
                    }));

                    fetch('/menus/reorder', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                items: items,
                                header: header
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Opcional: adicionar notificação de sucesso
                                console.log('Ordem atualizada com sucesso');
                            } else {
                                console.error('Erro ao reordenar:', data.error);
                                // Opcional: reverter alterações visuais
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Erro na requisição:', error);
                            location.reload();
                        });
                }
            });
        });

        // Excluir menu
        document.querySelectorAll('.delete-menu').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Confirma a exclusão?')) {
                    const id = this.dataset.id;
                    fetch(`/menus/delete/${id}`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                }
            });
        });
    });
</script>
<?php endpush() ?>