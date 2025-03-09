<?php require_once VIEWS_PATH . '/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Editar Slide</h1>
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
                    <form action="/carousel/update/<?= $slide['id']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label">Imagem Atual</label>
                            <div class="border rounded p-2 text-center bg-light mb-3">
                                <img src="<?= $slide['image_url']; ?>" alt="Imagem Atual" style="max-width: 100%; max-height: 300px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">Nova Imagem</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Tamanho recomendado: 1920x680 pixels. Formatos aceitos: JPG, PNG, WebP. Deixe em branco para manter a imagem atual.</div>
                        </div>

                        <div class="mb-4" id="preview-container" style="display: none;">
                            <label class="form-label">Pré-visualização da Nova Imagem</label>
                            <div class="border rounded p-2 text-center bg-light">
                                <img id="image-preview" src="" alt="Pré-visualização" style="max-width: 100%; max-height: 300px;">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Atualizar Slide</button>
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
        const previewContainer = document.getElementById('preview-container');
        
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    });
</script>
<?php endpush() ?>

<?php require_once VIEWS_PATH . '/partials/footer.php'; ?>
