<?php include '../includes/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%; border-radius: 12px;">
        <h2 class="text-center mb-4" style="color:#8B5E3C;">Crear Cuenta</h2>

        <form action="../actions/auth.php" method="POST">
            <input type="hidden" name="action" value="register">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn w-100" style="background-color:#D97706; color:white; font-weight:600;">
                Registrarse
            </button>
        </form>

        <p class="text-center mt-3" style="color:#8B5E3C;">
            ¿Ya tienes cuenta? <a href="login.php" style="color:#D97706; text-decoration:none;">Inicia sesión</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
