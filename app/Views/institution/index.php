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
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Contato</th>
                            <th>Data de Criação</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($institutions as $institution): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($institution['logo_url'])): ?>
                                        <img src="<?= base_url($institution['logo_url']) ?>" alt="logo" width="80" class="img-thumbnail">
                                    <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="currentColor" class="bi bi-building img-thumbnail" viewBox="0 0 16 16">
                                            <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z" />
                                        </svg>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($institution['name']) ?></td>
                                <td><?= htmlspecialchars($institution['domain']) ?></td>
                                <td><?= htmlspecialchars($institution['email']) ?></td>
                                <td><?= htmlspecialchars($institution['phone']) ?></td>
                                <td><?= htmlspecialchars($institution['name_contact']) ?></td>
                                <td><?= date('d/m/Y', strtotime($institution['created_at'])) ?></td>
                                <td>
                                    <?php if ($institution['active'] === 1): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-warning edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editInstitutionModal"
                                            data-id="<?= $institution['id'] ?>"
                                            data-name="<?= htmlspecialchars($institution['name']) ?>"
                                            data-domain="<?= htmlspecialchars($institution['domain']) ?>"
                                            data-email="<?= htmlspecialchars($institution['email']) ?>"
                                            data-phone="<?= htmlspecialchars($institution['phone']) ?>"
                                            data-name-contact="<?= htmlspecialchars($institution['name_contact']) ?>"
                                            data-logo="<?= htmlspecialchars($institution['logo_url']) ?>">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger ">
                                            <i class="bi bi-trash"></i> Desativar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($totalPages > 1): ?>
                        <!-- Botão Anterior -->
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= ($currentPage <= 1) ? '#' : '?page=' . ($currentPage - 1) ?>"
                                tabindex="-1">
                                Anterior
                            </a>
                        </li>

                        <!-- Números das Páginas -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botão Próximo -->
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= ($currentPage >= $totalPages) ? '#' : '?page=' . ($currentPage + 1) ?>">
                                Próximo
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>



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
                    <div class="row">
                        <!-- Campo Nome -->
                        <div class="col-8 mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" name="name" required minlength="3" maxlength="100"
                                placeholder="Digite o nome completo">
                            <div class="invalid-feedback">
                                Por favor, informe um nome válido.
                            </div>
                        </div>

                        <!-- Campo Domain -->
                        <div class="col-4 mb-3">
                            <label for="domain" class="form-label">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain" required>
                            <div class="invalid-feedback">
                                Por favor, informe um domínio válido.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo Email -->
                        <div class="col-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                placeholder="contato@instituicao.com">
                            <div class="invalid-feedback">
                                Por favor, informe um email válido.
                            </div>
                        </div>

                        <!-- Campo Telefone -->
                        <div class="col-3 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required
                                placeholder="(00) 00000-0000">
                            <div class="invalid-feedback">
                                Por favor, informe um telefone válido.
                            </div>
                        </div>

                        <!-- Campo Nome do Contato -->
                        <div class="col-5 mb-3">
                            <label for="name_contact" class="form-label">Nome do Contato</label>
                            <input type="text" class="form-control" id="name_contact" name="name_contact" required
                                placeholder="Nome do responsável">
                            <div class="invalid-feedback">
                                Por favor, informe o nome do contato.
                            </div>
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
                <button type="submit" form="uploadForm" class="btn btn-primary">Salvar</button>
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
                <form id="editForm" method="POST" action="institution/update" enctype="multipart/form-data">
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

                    <!-- Campo Email -->
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>

                    <!-- Campo Telefone -->
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control" id="editPhone" name="phone" required>
                    </div>

                    <!-- Campo Nome do Contato -->
                    <div class="mb-3">
                        <label for="editNameContact" class="form-label">Nome do Contato</label>
                        <input type="text" class="form-control" id="editNameContact" name="name_contact" required>
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

<?php push('styles') ?>
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
<?php endpush() ?>

<?php push('scripts') ?>
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
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const nameContact = this.getAttribute('data-name-contact');
                const logo = this.getAttribute('data-logo');

                document.getElementById('editId').value = id;
                document.getElementById('editName').value = name;
                document.getElementById('editDomain').value = domain;
                document.getElementById('editEmail').value = email;
                document.getElementById('editPhone').value = phone;
                document.getElementById('editNameContact').value = nameContact;
                document.getElementById('existingLogoUrl').value = logo;
                document.getElementById('editPreviewContainer').style.display = 'block';
                document.getElementById('editImagePreview').src = logo;
            });
        });

        // Add this inside the DOMContentLoaded event listener
        const nameInput = document.getElementById('name');
        const domainInput = document.getElementById('domain');
        const editNameInput = document.getElementById('editName');
        const editDomainInput = document.getElementById('editDomain');

        function formatDomain(text) {
            return text
                .toLowerCase()
                .trim()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // Remove acentos
                .replace(/[^a-z0-9-]/g, '-') // Substitui caracteres especiais por hífen
                .replace(/-+/g, '-') // Remove hífens duplicados
                .replace(/^-|-$/g, ''); // Remove hífens do início e fim
        }

        // Para o formulário de criação
        nameInput.addEventListener('input', function() {
            domainInput.value = formatDomain(this.value);
        });

        // Para o formulário de edição
        editNameInput.addEventListener('input', function() {
            editDomainInput.value = formatDomain(this.value);
        });

        // Função para preservar parâmetros na paginação
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') !== '#') {
                    e.preventDefault();
                    const currentUrl = new URL(window.location.href);
                    const newPage = new URLSearchParams(this.getAttribute('href')).get('page');
                    currentUrl.searchParams.set('page', newPage);
                    window.location.href = currentUrl.toString();
                }
            });
        });
    });
</script>
<?php endpush() ?>