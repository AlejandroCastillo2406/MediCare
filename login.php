<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM Usuarios WHERE correo = ? AND contraseña = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$correo, $contrasena]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['correo'] = $usuario['correo'];

        $sqlDatos = "SELECT * FROM DatosPersonales WHERE correo = ?";
        $stmtDatos = $conn->prepare($sqlDatos);
        $stmtDatos->execute([$correo]);
        $datosPersonales = $stmtDatos->fetch(PDO::FETCH_ASSOC);

        if (!$datosPersonales) {
            header("Location: completar_datos.php");
            exit;
        } else {
            header("Location: seleccion.php");
            exit;
        }
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="Zv25INGe762hTy1oN0xzrvbrkRys0Xp7-csMJQDu-vc" />
    <title>Acceso - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #c99322;
            --color-secondary: rgba(255,255,255,0.4);;
            --color-background: #0a0a0a;
            --color-text: #a8a8a8;
            --gradient-primary: linear-gradient(135deg, #c99322 10%, #333333 50%, #c99322 100%);

        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            overflow: hidden;
            perspective: 1000px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: 
                radial-gradient(circle at 3% 25%, rgba(255,255,255,0.03) 0%, transparent 25%),
                radial-gradient(circle at 97% 75%, rgba(255,255,255,0.03) 0%, transparent 25%),
                var(--color-background);
        }

        .login-container {
            width: 1100px;
            height: 700px;
            display: flex;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 50px 100px rgba(0,0,0,0.3);
            transform-style: preserve-3d;
            transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .login-visual {
            flex: 1.2;
            position: relative;
            background: var(--gradient-primary);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: orbital-spin 15s linear infinite;
        }

        .login-visual-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: #a8a8a8;
        }

        .login-visual-content img {
            max-width: 250px;
            margin-bottom: 10px; /* Ajusta este valor para mover el logo hacia arriba */
        }


        .login-visual h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            margin-bottom: 20px;
            letter-spacing: -2px;
            text-transform: uppercase;
            margin-top: -60px;
        }

        .login-visual p {
            max-width: 400px;
            opacity: 0.8;
            line-height: 1.6;
        }

        .login-form {
            flex: 1;
            background-color: #161616;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-primary);
        }

        .login-form h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-align: center;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 25px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid transparent;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            color: var(--color-text);
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        .input-wrapper input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(0, 168, 255, 0.1);
        }

        .input-wrapper .icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            transition: color 0.3s ease;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: var(--gradient-primary);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 7px 50px rgba(208, 173, 0, 0.2);
            
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 100px rgba(208, 173, 0, 0.2);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: var(--color-secondary);
        }

        .error-message {
            color: #ff4757;
            text-align: center;
            margin-top: 15px;
            font-weight: 600;
        }

        @keyframes orbital-spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

     
    @media (max-width: 1024px) {
    .login-container {
        width: 90%;
        height: auto;
        flex-direction: column;
  
    }

    .login-visual {
        display: none;
    }

    .login-form {
        width: 100%;
        padding: 30px 20px;
       min-height: 50vh;
        justify-content: flex-start;
    }

    .login-form h1 {
        font-size: 2rem;
        margin-bottom: 30px;
    }

    .input-wrapper input {
        padding: 12px 16px;
        font-size: 0.95rem;
    }

    .login-btn {
        padding: 12px;
        font-size: 1rem;
    }

    .signup-link {
        margin-top: 15px;
        font-size: 0.95rem;
    }

    .error-message {
        font-size: 0.95rem;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-visual">
            <div class="login-visual-content">
                <img src="images/image.png" alt="Logo de Medicare360" style="">
                <h2>MediCare360</h2>
                <p>En Medicare360, cuidamos de tu bienestar con una visión completa, porque tu salud merece una protección sin límites.</p>
            </div>
        </div>
        <form class="login-form" method="post" action="">
            <h1>Acceso Personalizado</h1>
            <div class="input-wrapper">
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <div class="icon">✉️</div>
            </div>
            <div class="input-wrapper">
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <div class="icon">🔐</div>
            </div>
            <button type="submit" class="login-btn">Iniciar Sesión</button>
            <div class="signup-link">
                ¿No tienes cuenta? <a href="registrar.php">Regístrate</a>
            </div>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>