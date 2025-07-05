<?php
// registrar.php
session_start();
include 'conexion.php';

$message = ""; // Variable para almacenar el mensaje

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $nombre = $_POST['nombre'];
    $ap_paterno = $_POST['ap_paterno'];
    $ap_materno = $_POST['ap_materno'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Verifica que la contraseña no sea nula
    if (empty($contrasena)) {
        $message = "La contraseña no puede estar vacía.";
    } else {
        // Prepara la consulta SQL para verificar si el correo ya existe
        $sqlCheck = "SELECT * FROM Usuarios WHERE correo = :correo";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':correo', $correo);
        $stmtCheck->execute();

        // Verifica si ya existe un usuario con el mismo correo
        if ($stmtCheck->rowCount() > 0) {
            $message = "Error: Correo ya existente.";
        } else {
            // Prepara la consulta SQL para insertar el nuevo usuario
            $sql = "INSERT INTO Usuarios (nombre, ap_paterno, ap_materno, correo, contraseña) VALUES (:nombre, :ap_paterno, :ap_materno, :correo, :contrasena)";
            $stmt = $conn->prepare($sql);

            // Enlaza los parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':ap_paterno', $ap_paterno);
            $stmt->bindParam(':ap_materno', $ap_materno);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':contrasena', $contrasena);

            try {
                // Ejecuta la consulta
                $stmt->execute();
                $message = "Registro exitoso.";
            } catch (PDOException $e) {
                // Verifica si el error es de clave única
                if ($e->getCode() == 23000) {
                    $message = "Error: Correo ya en uso."; // Mensaje simplificado
                } else {
                    $message = "Error al registrar: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MediCare360</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: 
                radial-gradient(circle at 3% 25%, rgba(255,255,255,0.03) 0%, transparent 25%),
                radial-gradient(circle at 97% 75%, rgba(255,255,255,0.03) 0%, transparent 25%),
                var(--color-background);
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .register-container:hover {
            transform: scale(1.05);
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px;
            border: 2px solid transparent;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
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

        .register-btn {
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
            box-shadow: 0 5px 100px rgba(208, 173, 0, 0.2);
        }

        .register-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 7px 50px rgba(208, 173, 0, 0.2);
        }

        p {
            margin-top: 20px;
            font-size: 0.9rem;
        }

        p a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        p a:hover {
            color: var(--color-secondary);
        }

        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.05);;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            color: var(--color-text);
        }

        .close {
            color: var(--color-primary);
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <script>
        function mostrarModal(mensaje) {
            document.getElementById("modalMensaje").style.display = "flex";
            document.getElementById("mensaje").innerText = mensaje;
        }

        function cerrarModal() {
            document.getElementById("modalMensaje").style.display = "none";
        }

        window.onload = function() {
            const mensaje = "<?php echo addslashes($message); ?>";
            if (mensaje) {
                mostrarModal(mensaje);
            }
        };
    </script>
</head>
<body>
    <div class="register-container">
        <h1>Registro</h1>
        <form method="post" action="">
            <div class="input-wrapper">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <div class="icon">👤</div>
            </div>
            <div class="input-wrapper">
                <input type="text" name="ap_paterno" placeholder="Apellido Paterno" required>
                <div class="icon">👨‍👩‍👧‍👦</div>
            </div>
            <div class="input-wrapper">
                <input type="text" name="ap_materno" placeholder="Apellido Materno" required>
                <div class="icon">👨‍👩‍👧‍👦</div>
            </div>
            <div class="input-wrapper">
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <div class="icon">✉️</div>
            </div>
            <div class="input-wrapper">
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <div class="icon">🔑</div>
            </div>
            <button type="submit" class="register-btn">Registrar</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>

    <!-- Modal -->
    <div id="modalMensaje" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <p id="mensaje"></p>
        </div>
    </div>
</body>
</html>