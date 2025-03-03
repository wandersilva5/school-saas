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
                <h1 class="h3 mb-0"><?php echo $pageTitle; ?></h1>
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
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning">
                                        <i class="bi bi-trash"></i> Desativar</button>
                                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editInstitutionModal" data-id="<?= $institution['id'] ?>" data-name="<?= htmlspecialchars($institution['name']) ?>" data-domain="<?= htmlspecialchars($institution['domain']) ?>" data-logo="<?= htmlspecialchars($institution['logo_url']) ?>">
                                        <i class="bi bi-pencil"></i> Editar</button>
                                </td>
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

<style>
    /* Minimal custom styles that aren't disponíveis em Bootstrap */
    .upload-area {
        min-height: 300px;
        cursor: pointer;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-area.dragover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }

    .preview-container {
        display: none;
        margin-top: 20px;
    }

    #imagePreview {
        width: 100%;
        max-height: 300px;
        object-fit: contain;
    }

    .upload-icon {
        width: 64px;
        height: 64px;
    }

    .modal-body {
        overflow-y: auto;
    }

    .position-absolute {
        position: absolute !important;
    }
</style>

<!-- Modal de Nova Instituição -->
<div class="modal fade" id="institutionModal" tabindex="-1" aria-labelledby="institutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="institutionModalLabel">Cadastro de Instituição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" method="POST" action="/institution/store" enctype="multipart/form-data">
                    <!-- Campo Nome -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required minlength="3" maxlength="100"
                            placeholder="Digite o nome completo">
                        <div class="invalid-feedback">
                            Por favor, informe um nome válido.
                        </div>
                    </div>

                    <!-- Campo Domain -->
                    <div class="mb-3">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="domain" name="domain" required>
                        <div class="invalid-feedback">
                            Por favor, informe um domínio válido.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Logo da Instituição</label>
                        <input type="file" class="form-control d-none" id="imageInput" name="logo_url" accept="image/*">
                        <div class="invalid-feedback">
                            Por favor, informe um domínio válido.
                        </div>
                    </div>
                    <div class="text-center" id="dropZone">
                        <svg class="upload-icon text-primary mb-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        <p class="text-muted mb-0">Clique aqui ou arraste uma imagem</p>
                    </div>

                    <div class="preview-container" id="previewContainer">
                        <img id="imagePreview" src="" alt="Preview" class="rounded-3">
                    </div>

                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none align-items-center justify-content-center" id="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="uploadForm" class="btn btn-primary">Cadastrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Instituição -->
<div class="modal fade" id="editInstitutionModal" tabindex="-1" aria-labelledby="editInstitutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInstitutionModalLabel">Editar Instituição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="/institution/update" enctype="multipart/form-data">
                    <input type="hidden" id="editId" name="id">
                    <!-- Campo Nome -->
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="editName" name="name" required minlength="3" maxlength="100"
                            placeholder="Digite o nome completo">
                        <div class="invalid-feedback">
                            Por favor, informe um nome válido.
                        </div>
                    </div>

                    <!-- Campo Domain -->
                    <div class="mb-3">
                        <label for="editDomain" class="form-label">Domain</label>
                        <input type="text" class="form-control" id="editDomain" name="domain" required>
                        <div class="invalid-feedback">
                            Por favor, informe um domínio válido.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editImage" class="form-label">Logo da Instituição</label>
                        <input type="file" class="form-control d-none" id="editImageInput" name="logo_url" accept="image/*">
                        <input type="hidden" id="existingLogoUrl" name="existing_logo_url">
                        <div class="invalid-feedback">
                            Por favor, informe um domínio válido.
                        </div>
                    </div>
                    <div class="text-center" id="editDropZone">
                        <svg class="upload-icon text-primary mb-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        <p class="text-muted mb-0">Clique aqui ou arraste uma imagem</p>
                    </div>

                    <div class="preview-container" id="editPreviewContainer">
                        <img id="editImagePreview" src="" alt="Preview" class="rounded-3">
                    </div>

                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none align-items-center justify-content-center" id="editLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do DOM
        const dropZone = document.getElementById('dropZone');
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const loading = document.getElementById('loading');
        const uploadForm = document.getElementById('uploadForm');

        const editDropZone = document.getElementById('editDropZone');
        const editImageInput = document.getElementById('editImageInput');
        const editPreviewContainer = document.getElementById('editPreviewContainer');
        const editImagePreview = document.getElementById('editImagePreview');
        const editLoading = document.getElementById('editLoading');
        const editForm = document.getElementById('editForm');

        // Previne o comportamento padrão de arrastar e soltar do navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            editDropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone quando item é arrastado sobre
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
            editDropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
            editDropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            e.currentTarget.classList.add('dragover');
        }

        function unhighlight(e) {
            e.currentTarget.classList.remove('dragover');
        }

        // Manipula o drop
        dropZone.addEventListener('drop', handleDrop, false);
        editDropZone.addEventListener('drop', handleDropEdit, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            handleFiles(files, imageInput, previewContainer, imagePreview, loading);
        }

        function handleDropEdit(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            handleFiles(files, editImageInput, editPreviewContainer, editImagePreview, editLoading);
        }

        // Clique na área de upload
        dropZone.addEventListener('click', function() {
            imageInput.click();
        });

        editDropZone.addEventListener('click', function() {
            editImageInput.click();
        });

        // Quando um arquivo é selecionado através do input
        imageInput.addEventListener('change', function() {
            handleFiles(this.files, imageInput, previewContainer, imagePreview, loading);
        });

        editImageInput.addEventListener('change', function() {
            handleFiles(this.files, editImageInput, editPreviewContainer, editImagePreview, editLoading);
        });

        // Manipula os arquivos selecionados
        function handleFiles(files, input, previewContainer, imagePreview, loading) {
            if (files.length > 0) {
                const file = files[0];

                if (file.type.startsWith('image/')) {
                    // Mostra loading
                    loading.classList.remove('d-none');
                    loading.classList.add('d-flex');

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // Esconde loading e mostra preview
                        loading.classList.remove('d-flex');
                        loading.classList.add('d-none');
                        previewContainer.style.display = 'block';
                        imagePreview.src = e.target.result;
                    }

                    reader.readAsDataURL(file);
                } else {
                    alert('Por favor, selecione um arquivo de imagem válido.');
                }
            }
        }

        // Manipula o envio do formulário
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!imageInput.files[0]) {
                alert('Por favor, selecione uma imagem primeiro.');
                return;
            }
            uploadForm.submit();
        });

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!editImageInput.files[0] && !document.getElementById('existingLogoUrl').value) {
                alert('Por favor, selecione uma imagem primeiro.');
                return;
            }
            editForm.submit();
        });

        // Preenche o modal de edição com as informações da instituição
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const domain = this.getAttribute('data-domain');
                const logo = this.getAttribute('data-logo');

                document.getElementById('editId').value = id;
                document.getElementById('editName').value = name;
                document.getElementById('editDomain').value = domain;
                document.getElementById('existingLogoUrl').value = logo;
                editPreviewContainer.style.display = 'block';
                editImagePreview.src = logo;
            });
        });
    });
</script>