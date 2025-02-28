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
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning">
                                        <i class="bi bi-trash"></i> Desativar</button>
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
    /* Minimal custom styles that aren't available in Bootstrap */
    .upload-area {
        min-height: 300px;
        cursor: pointer;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }

    .upload-area.dragover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }

    .preview-container {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    #imagePreview {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .upload-icon {
        width: 64px;
        height: 64px;
    }
</style>

<!-- Modal de Novo Usuário -->
<div class="modal fade" id="institutionModal" tabindex="-1" aria-labelledby="institutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="institutionModalLabel">Cadastro de Instituição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" method="POST" action="/institutions/store" enctype="multipart/form-data">
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
                        <label for="domain" class="form-label">domain</label>
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
                    <div class="text-center" id="placeholder">
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
            <button type="submit" form="institutionForm" class="btn btn-primary">Salvar</button>
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
        const placeholder = document.getElementById('placeholder');
        const loading = document.getElementById('loading');
        const uploadForm = document.getElementById('uploadForm');

        // Previne o comportamento padrão de arrastar e soltar do navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone quando item é arrastado sobre
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        // Manipula o drop
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            handleFiles(files);
        }

        // Clique na área de upload
        dropZone.addEventListener('click', function() {
            imageInput.click();
        });

        // Quando um arquivo é selecionado através do input
        imageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        // Manipula os arquivos selecionados
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                if (file.type.startsWith('image/')) {
                    // Mostra loading
                    loading.classList.remove('d-none');
                    loading.classList.add('d-flex');
                    placeholder.classList.add('d-none');

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = function() {
                            // Esconde loading e mostra preview
                            loading.classList.remove('d-flex');
                            loading.classList.add('d-none');
                            previewContainer.style.display = 'block';
                            imagePreview.src = e.target.result;
                        }
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
            // Aqui você pode adicionar a lógica para enviar a imagem para o servidor
            alert('Imagem pronta para upload!');
        });
    });
</script>