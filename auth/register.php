<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nombre) || empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo electrónico no es válido.';
    } else {
        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Este correo electrónico ya está registrado.';
            } else {
                // Insertar nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'estudiante')");
                $stmt->execute([$nombre, $email, $hashed_password]);

                $_SESSION['usuario_id'] = $pdo->lastInsertId();
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_rol'] = 'estudiante';

                header('Location: ../index.php');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Hubo un error al procesar el registro. Inténtalo de nuevo.';
        }
    }
}

$page_title = "Registro de Estudiante";
$header_title = "Crea tu Cuenta";

include '../includes/header.php';
?>

<div class="max-w-md mx-auto bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
    <div class="text-center mb-xl">
        <div class="flex items-center justify-center mx-auto mb-md">
            <img src="/tutor/assets/imgs/logo_learning.png" alt="Learn Design Logo" class="h-20 object-contain drop-shadow-md">
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-error-container text-on-error-container p-md rounded-lg mb-lg flex items-center gap-md border border-error">
            <span class="material-symbols-outlined">error</span>
            <p class="font-body-sm"><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" class="space-y-lg">
        <div>
            <label for="nombre" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" required 
                class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
        </div>
        <div>
            <label for="email" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Correo Electrónico</label>
            <input type="email" id="email" name="email" required 
                class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
        </div>
        <div>
            <label for="password" class="block font-label-lg text-label-lg text-on-surface-variant mb-xs">Contraseña</label>
            <input type="password" id="password" name="password" required 
                class="w-full bg-surface-container-highest text-on-surface border border-outline-variant rounded-lg px-md py-3 focus:outline-none focus:border-primary-container transition-all">
        </div>

        <button type="submit" class="w-full bg-primary-container text-on-primary font-label-lg text-label-lg py-3 rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md">
            Registrarse
        </button>
    </form>

    <div class="mt-xl text-center">
        <p class="font-body-sm text-on-surface-variant">
            ¿Ya tienes una cuenta? <a href="login.php" class="text-primary hover:underline">Inicia Sesión</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
