<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diabery - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; min-height: 100vh; }
        .login-card { max-width: 400px; width: 90%; margin: auto; }
    </style>
</head>
<body>
    <div class="card login-card shadow border-0">
        <div class="card-body p-4">
            <h2 class="text-center fw-bold text-primary mb-4">Diabery</h2>
            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Entrar</button>
            </form>
            @if ($errors->any())
                <div class="alert alert-danger mt-3 small">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>