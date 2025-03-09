<?php require_once VIEWS_PATH . '/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Adicionar Novo Slide</h1>
            <a href="/carousel" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="/carousel/store" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="image" class="form-label">Imagem do Slide</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            <div class="form-text">Tamanho recomendado: 1920x680 pixels. Formatos aceitos: JPG, PNG, WebP.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Pré-visualização</label>
                            <div class="border rounded p-2 text-center bg-light" id="preview-container">
                                <img id="image-preview" src="" alt="Pré-visualização" style="max-width: 100%; max-height: 300px; display: none;">
                                <p id="no-preview" class="mb-0 py-5">Selecione uma imagem para ver a pré-visualização</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Salvar Slide</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const noPreview = document.getElementById('no-preview');
        
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    noPreview.style.display = 'none';
                };
                
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.style.display = 'none';
                noPreview.style.display = 'block';
            }
        });
    });
</script>
<?php endpush() ?>

<?php require_once VIEWS_PATH . '/partials/footer.php'; ?>
