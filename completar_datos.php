<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit;
}

$correo = $_SESSION['correo'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sexo = $_POST['sexo'];
    $edad = $_POST['edad'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $embarazo = $_POST['embarazo'];
    $actividad_fisica = $_POST['actividad_fisica'];
    $alimentacion = $_POST['alimentacion'];

    $sql = "INSERT INTO DatosPersonales (correo, sexo, edad, peso, altura, embarazo, actividad_fisica, alimentacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$correo, $sexo, $edad, $peso, $altura, $embarazo, $actividad_fisica, $alimentacion]);

    echo "<script>alert('Datos personales guardados correctamente.'); window.location.href='seleccion.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Personal - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    /* Colores principales para usar en toda la página */
    :root {
            --color-primary: #c99322;
            --color-secondary: rgba(255,255,255,0.4);;
            --color-background: #0a0a0a;
            --color-text: #a8a8a8;
            --gradient-primary: linear-gradient(135deg, #c99322 10%, #333333 50%, #c99322 100%);

        }

    * {
        /* Elimina márgenes y rellenos predeterminados */
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    /* Diseño estetico de la página */
    body {
        /* Tipo letra */
        font-family: 'Inter', sans-serif;
        background-color: var(--color-background);
        /* Color del texto principal */
        color: var(--color-text);
        /* Centra el contenido horizontal y verticalmente */
        display: flex;
        justify-content: center;
        align-items: center;
        /* La página ocupa al menos toda la altura de la pantalla */
        min-height: 100vh;
        /* Fondo con degradados radiales decorativos */
        background: 
        radial-gradient(circle at 3% 25%, rgba(255,255,255,0.05) 0%, transparent 25%),
        radial-gradient(circle at 97% 75%, rgba(255,255,255,0.05) 0%, transparent 25%),
        var(--color-background);
        /* Espacio alrededor del contenido */
        padding: 20px;
        /* Evita que aparezca la barra de desplazamiento horizontal */
        overflow-x: hidden;
    }

    /* Contenedor donde todo lo de DatosPersonales */
    .personal-data-container {
        /* Ancho máximo del contenedor */
        width: 100%;
        max-width: 700px;
        /* Fondo semi-transparente */
        background: #161616;
        /* Bordes redondeados */
        border-radius: 20px;
        /* Sombra decorativa debajo del contenedor */
        box-shadow: 0 50px 100px rgba(0,0,0,0.3);
        /* Espacio interno */
        padding: 40px;
        /* Efecto de desenfoque detrás del contenedor */
        backdrop-filter: blur(10px);
        /* Posición relativa para elementos internos */
        position: relative;
        /* Evita que el contenido se desborde */
        overflow: hidden;
    }

     /* Línea decorativa en la parte superior del contenedor de DatosPersonales */
    .personal-data-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: var(--gradient-primary);
    }

    /* Diseño de los titulos */
    h1 {
     /* Centra el título */
    text-align: center;
    /* Aplica un degradado al texto */
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    /* Espacio debajo del título */
    margin-bottom: 30px;
    /* Tamaño del título */
    font-size: 2.5rem;
    }

    .form-grid {
        /* Crea una cuadrícula para organizar los campos del formulario */
        display: grid;
        grid-template-columns: 1fr 1fr;
        /* Espacio entre elementos de la cuadrícula */
        gap: 20px;
    }

    /* Ajusta la cuadrícula para pantallas pequeñas */
    @media (max-width: 768px) {
        .form-grid {
         grid-template-columns: 1fr;
        }
    }

    /* Contenedor de cada campo de entrada */
    .input-wrapper {
        
        position: relative;
    }

    /* Estilo para las etiquetas de los campos */
    .input-wrapper label {
        
        display: block;
        margin-bottom: 10px;
        color: var(--color-text);
        opacity: 0.7;
        font-weight: 600;
    }

    /* Estilo para los campos de texto y selección */
    .input-wrapper input, 
    .input-wrapper select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid transparent;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--color-text);
        font-size: 1rem;
        /* Efecto de transición suave */
        transition: all 0.3s ease;
        outline: none;
        font-family: 'Inter', sans-serif;
    }

    .input-wrapper select option {
    background: rgba(255, 255, 255, 0.05); /* Dark gray for the dropdown options */
    color: #a8a8a8; /* Light gray text for the options */
    }
    /* Efecto al enfocar los campos */
    .input-wrapper input:focus, 
    .input-wrapper select:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(74,108,247,0.1);
    }

    /* Organiza los botones de radio en una fila */
    .radio-group {
        display: flex;
        gap: 15px;
    }

    /* Contenedor para cada botón de radio */
    .radio-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Estilo del botón de radio */
    .radio-wrapper input[type="radio"] {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        outline: none;
        transition: all 0.3s ease;
        position: relative;
    }

     /* Estilo cuando el botón de radio está seleccionado */
    .radio-wrapper input[type="radio"]:checked {
        background: var(--gradient-primary);
        border-color: transparent;
    }

    /* Puntito blanco dentro del botón de radio cuando está seleccionado */
    .radio-wrapper input[type="radio"]:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    /* Botón de enviar ocupa todo el ancho */
    .submit-btn {
        grid-column: 1 / -1;
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
        /* Sombra decorativa */
        box-shadow: 0 5px 100px rgba(208, 173, 0, 0.2);
        margin-top: 20px;
    }

    /* Efecto de zoom al pasar el mouse */
    .submit-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 7px 50px rgba(208, 173, 0, 0.2);
    }

    /* Animación decorativa para rotar y hacer zoom */
    @keyframes bg-animate {
        0% { 
            transform: rotate(0deg) scale(1);
            opacity: 0.5;
        }
        100% { 
            transform: rotate(360deg) scale(1.2);
            opacity: 0.7;
        }
    }
    .custom-select {
    position: relative;
    width: 100%;
}

.select-selected {
    background: rgba(255, 255, 255, 0.05); /* Fondo semi-transparente para coincidir con el input */
    padding: 12px 15px;
    border: 2px solid transparent;
    border-radius: 8px;
    color: var(--color-text); /* Usa la variable de texto existente */
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
}

.select-selected:hover {
    border-color: var(--color-primary);
}

.select-items {
    position: absolute;
    background: rgba(255, 255, 255, 0.05); /* Fondo gris oscuro para las opciones */
    top: 100%;
    left: 0;
    right: 0;
    z-index: 99;
    border-radius: 8px;
    margin-top: 5px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.select-items div {
    padding: 12px 15px;
    color: var(--color-text); /* Texto gris claro */
    cursor: pointer;
}

.select-items div:hover {
    background: rgba(255, 255, 255, 0.1); /* Efecto al pasar el mouse */
}

.select-hide {
    display: none;
}
    </style>
</head>
<body>
    <div class="personal-data-container">
        <form method="post" action="">
            <h1>Perfil de Salud Personal</h1>
            
            <div class="form-grid">
                <div class="input-wrapper">
                    <label>Sexo</label>
                    <div class="radio-group">
                        <div class="radio-wrapper">
                            <input type="radio" name="sexo" value="Hombre" required>
                            <label>Hombre</label>
                        </div>
                        <div class="radio-wrapper">
                            <input type="radio" name="sexo" value="Mujer">
                            <label>Mujer</label>
                        </div>
                    </div>
                </div>

                <div class="input-wrapper">
                    <label for="edad">Edad</label>
                    <input type="number" name="edad" placeholder="Ingrese su edad" required min="0" max="120">
                </div>

                <div class="input-wrapper">
                    <label for="peso">Peso (kg)</label>
                    <input type="number" step="0.1" name="peso" placeholder="Ingrese su peso" required min="0" max="500">
                </div>

                <div class="input-wrapper">
                    <label for="altura">Altura (m)</label>
                    <input type="number" step="0.01" name="altura" placeholder="Ingrese su altura" required min="0" max="3">
                </div>

                <div class="input-wrapper">
                    <label>Estado de Embarazo</label>
                    <div class="radio-group">
                        <div class="radio-wrapper">
                            <input type="radio" name="embarazo" value="No" required>
                            <label>No</label>
                        </div>
                        <div class="radio-wrapper">
                            <input type="radio" name="embarazo" value="Sí">
                            <label>Sí</label>
                        </div>
                    </div>
                </div>

                <div class="input-wrapper">
                    <label for="actividad_fisica">Nivel de Actividad Física</label>
                    <div class="custom-select">
                        <div class="select-selected">Seleccione su nivel</div>
                        <div class="select-items select-hide">
                            <div data-value="Escasa">Escasa</div>
                            <div data-value="Regular">Regular</div>
                            <div data-value="Constante">Constante</div>
                        </div>
                    </div>
                    <input type="hidden" name="actividad_fisica" id="actividad_fisica" required>
                </div>

                <div class="input-wrapper">
                    <label for="alimentacion">Calidad de Alimentación</label>
                    <div class="custom-select">
                        <div class="select-selected">Evalúe su alimentación</div>
                        <div class="select-items select-hide">
                            <div data-value="Buena">Buena</div>
                            <div data-value="Regular">Regular</div>
                            <div data-value="Mala">Mala</div>
                        </div>
                    </div>
                    <input type="hidden" name="alimentacion" id="alimentacion" required>
                </div>

                <button type="submit" class="submit-btn">Guardar Perfil de Salud</button>
            </div>
        </form>
    </div>

    <script>
    document.querySelectorAll(".custom-select").forEach(function (customSelect) {
        const selected = customSelect.querySelector(".select-selected");
        const items = customSelect.querySelector(".select-items");
        const hiddenInput = customSelect.parentElement.querySelector("input[type='hidden']");

        selected.addEventListener("click", function () {
            items.classList.toggle("select-hide");
        });

        items.querySelectorAll("div").forEach(function (item) {
            item.addEventListener("click", function () {
                selected.textContent = this.textContent;
                hiddenInput.value = this.getAttribute("data-value");
                items.classList.add("select-hide");
            });
        });

        document.addEventListener("click", function (e) {
            if (!customSelect.contains(e.target)) {
                items.classList.add("select-hide");
            }
        });
    });
    </script>
</body>
</html>