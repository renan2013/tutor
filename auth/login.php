<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                header('Location: /index.php');
                exit;
            } else {
                $error = 'Correo electrónico o contraseña incorrectos.';
            }
        } catch (Exception $e) {
            $error = 'Hubo un error al intentar iniciar sesión.';
        }
    }
}

$page_title = "Iniciar Sesión";
$header_title = "Bienvenido de Nuevo";

include '../includes/header.php';
?>

<div class="max-w-md mx-auto bg-surface-container rounded-xl p-xl border border-outline-variant shadow-lg">
    <div class="text-center mb-xl">
        <div class="w-16 h-16 bg-primary-container/20 rounded-full flex items-center justify-center mx-auto mb-md">
            <span class="material-symbols-outlined text-4xl text-primary-container">login</span>
        </div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Iniciar Sesión</h2>
        <p class="font-body-sm text-on-surface-variant">Accede a tus cursos y herramientas favoritas.</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-error-container text-on-error-container p-md rounded-lg mb-lg flex items-center gap-md border border-error">
            <span class="material-symbols-outlined">error</span>
            <p class="font-body-sm"><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-lg">
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
            Entrar
        </button>
    </form>

    <div class="mt-xl text-center">
        <p class="font-body-sm text-on-surface-variant">
            ¿No tienes una cuenta? <a href="register.php" class="text-primary hover:underline">Regístrate ahora</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
