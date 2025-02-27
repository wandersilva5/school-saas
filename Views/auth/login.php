<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #154A9A 0%, #1e6ddf 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-box h2 {
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
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<!-- Debug: POST recebido -->\n";
        echo "<!-- Email: " . htmlspecialchars($_POST['email'] ?? '') . " -->\n";
    }
    ?>
    <div class="container">
        <div class="login-box">
            <h2>Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
                <div class="text-center mt-3">
                    <a href="/register" class="text-decoration-none">Criar conta</a> |
                    <a href="/forgot-password" class="text-decoration-none">Esqueceu a senha?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>