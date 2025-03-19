<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #154A9A 0%, #1e6ddf 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-box {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: 2rem auto;
        }

        .register-box h2 {
            color: #154A9A;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background-color: #154A9A;
            border-color: #154A9A;
            width: 100%;
            padding: 0.8rem;
        }

        .btn-primary:hover {
            background-color: #1e6ddf;
            border-color: #1e6ddf;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-box">
            <h2>Criar Conta</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) && isset($redirect)): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = '<?= $redirect ?>';
                    }, 2000);
                </script>
            <?php endif; ?>

            <form method="POST" action="/register">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="surname" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
                <div class="text-center mt-3">
                    <span>Já tem uma conta?</span>
                    <a href="/login" class="text-decoration-none">Fazer login</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>