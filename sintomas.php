<?php
session_start();

if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit;
}

$sintomas_respiratorios = [
    "Congestión nasal",
    "Dolor de garganta",
    "Dolor torácico",
    "Dificultad para respirar",
    "Dificultad para tragar",
    "Estornudos",
    "Irritación",
    "Ronquera",
    "Sangrado nasal",
    "Secreción nasal",
    "Silibancias",
    "Tos con flema",
    "Tos persistente",
    "Tos seca"
];

$sintomas_digestivos = [
    "Acidez",
    "Diarrea",
    "Dolor abdominal",
    "Dolor al masticar",
    "Dolor anal",
    "Dolor o ardor en el estómago",
    "Dificultad para defecar",
    "Gases",
    "Heces duras",
    "Heces líquidas",
    "Hinchazón",
    "Náuseas",
    "Pérdida de apetito",
    "Regurgitación",
    "Sangrado leve",
    "Vómitos",
    "Vómitos con sangre"
];

$sintomas_nerviosos = [
    "Cambios de humor",
    "Dificultad para concentrarse",
    "Dificultad para dormir",
    "Dolor de cabeza",
    "Irritabilidad",
    "Mareos",
    "Nerviosismo",
    "Pérdida de interés",
    "Pérdida del gusto",
    "Pérdida del olfato",
    "Sensibilidad a la luz",
    "Tristeza persistente",
    "Visión borrosa"
];

$sintomas_musculoesqueleticos = [
    "Dificultad para moverse",
    "Dolor al estar sentado",
    "Dolor al girar el cuello",
    "Dolor de cuello",
    "Dolor en la espalda baja",
    "Dolor en las articulaciones",
    "Dolor muscular",
    "Hinchazón",
    "Inflamación en las articulaciones",
    "Reducción del rango de movimiento",
    "Rigidez",
    "Sensibilidad al tacto"
];

$sintomas_integumentario = [
    "Ampollas en los labios",
    "Caída del cabello",
    "Enrojecimiento",
    "Erupción cutánea",
    "Hinchazón de las glándulas salivales",
    "Mal olor",
    "Moretones",
    "Picazón",
    "Picazón en los pies",
    "Piel agrietada",
    "Piel seca",
    "Piel pálida",
    "Secreción blanca"
];

$sintomas_urinario = [
    "Dolor agudo en el abdomen inferior derecho",
    "Dolor al orinar",
    "Dolor intenso en el costado",
    "Micción frecuente",
    "Orina turbia",
    "Sangre en la orina"
];


$sintomas_endocrino = [
    "Aumento de peso",
    "Pérdida de peso",
    "Sed excesiva",
    "Sudoración excesiva"
];

$sintomas_inmunologico_generales = [
    "Debilidad",
    "Deshidratación",
    "Dolor facial",
    "Escalofríos",
    "Fatiga",
    "Fiebre",
    "Inflamación de las amígdalas"
];

$sintomas_otros = [
    "Palpitaciones",
    "Hormigueo",
    "Lagrimeo",
    "Ojos rojos",
    "Picazón en los ojos",
    "Sensación de cuerpo extraño",
    "Dolor de oído",
    "Pérdida de audición temporal",
    "Boca seca"
];


// Arrays de iconos para cada sistema (usando códigos de Font Awesome)
$iconos = [
    'respiratorio' => 'fa-lungs',
    'digestivo' => 'fa-apple-alt',
    'nervioso' => 'fa-brain',
    'musculoesqueletico' => 'fa-bone',
    'integumentario' => 'fa-hand-sparkles',
    'urinario' => 'fa-toilet',
    'endocrino' => 'fa-weight-scale',
    'inmunologico' => 'fa-shield-virus',
    'otros' => 'fa-stethoscope'
];

// Arrays de colores para cada sistema (gradientes dentro de la misma paleta)
$colores = [
    'respiratorio' => '#b59441;',
    'digestivo' => '#b59441;',
    'nervioso' => '#b59441;',
    'musculoesqueletico' => '#b59441;',
    'integumentario' => '#b59441;',
    'urinario' => '#b59441;',
    'endocrino' => '#b59441;',
    'inmunologico' => '#b59441;',
    'otros' => '#b59441;'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Síntomas - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Colores principales y gradiente */
        :root {
            --color-primary: #c99322;
            --color-secondary: rgba(255,255,255,0.4);;
            --color-background: #0a0a0a;
            --color-text: #a8a8a8;
            --gradient-primary: linear-gradient(135deg, #c99322 10%, #333333 50%, #c99322 100%);

        }

        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Configuración general con animación de fondo */
        body {
            font-family: 'Exo 2', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            min-height: 100vh;
            background: 
                radial-gradient(circle at 10% 20%, #161616 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, #161616 0%, transparent 40%),
                var(--color-background);
            padding: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* Partículas animadas en el fondo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        /* Header futurista */
        .header {
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .header h1 {
            font-size: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            margin: 0;
        }

        .header h1::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 2px;
            background: var(--gradient-primary);
        }

        /* Indicador de progreso */
        .progress-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px 0;
        
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-dots {
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .dot.active {
            background: var(--gradient-primary);
            transform: scale(1.2);
           
        }

        /* Contenedor principal */
        .main-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 100px 20px 80px;
        }

        /* Tarjeta principal con efecto de cristal */
        .sintomas-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            padding: 20px;
        }

        /* Tarjetas de sistemas con efecto 3D */
        .sistema-card {
            display: none;
            width: 100%;
            background: #161616;
            border-radius: 24px;
            backdrop-filter: blur(30px);
            padding: 40px;
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 
                0 30px 60px rgba(0,0,0,0.4),
                0 0 0 1px rgba(255,255,255,0.1) inset;
            transform-style: preserve-3d;
            transform: perspective(1000px) rotateX(0deg);
        }

        .sistema-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #161616
    ;
            z-index: -1;
        }

        .sistema-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-primary);
        }

        .active-card {
            display: block;
            animation: cardEntrance 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: perspective(1000px) rotateX(10deg) translateY(30px);
            }
            to {
                opacity: 1;
                transform: perspective(1000px) rotateX(0deg) translateY(0);
            }
        }

        /* Header de sistema con icono */
        .sistema-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .sistema-icon {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            background: var(--gradient-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
            box-shadow: 0 7px 20px rgba(208, 173, 0, 0.2);;
        }

        .sistema-icon i {
            font-size: 28px;
            color: white;
        }

        .sistema h2 {
            font-size: 28px;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .sistema-description {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 30px;
            max-width: 80%;
        }

        /* Contenedor de síntomas con efecto de hover mejorado */
        .sintomas {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        /* Estilo futurista para los botones de síntomas */
        .sintoma {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--color-text);
            border-radius: 12px;
            padding: 12px 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .sintoma input[type="checkbox"] {
            display: none;
        }

        .sintoma::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .sintoma.checked {
            border-color: transparent;
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(247, 106, 74,0.5);
        }

        .sintoma.checked::before {
            opacity: 1;
        }

        .sintoma:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px  rgba(208, 173, 0, 0.2);
            border-color: var(--color-primary);
        }

        /* Contador de síntomas seleccionados */
        .sintomas-counter {
            position: absolute;
            top: 40px;
            right: 40px;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            padding: 10px 15px;
            border-radius: 50px;
            font-size: 14px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .counter-icon {
            margin-right: 8px;
            color: var(--color-primary);
        }

        /* Botones de navegación */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .btn-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 25px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .btn-nav:hover::before {
            opacity: 1;
        }

        .btn-nav:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(208, 173, 0, 0.2);
        }

        .btn-nav i {
            margin-right: 10px;
            font-size: 18px;
        }

        .btn-next i {
            margin-right: 0;
            margin-left: 10px;
        }

        /* Botón de envío con efecto de pulso */
        .submit-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            background: var(--gradient-primary);
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 15px 25px rgba(208, 173, 0, 0.2);;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            width: auto;
        }

        .submit-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150%;
            height: 150%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
            transition: transform 0.6s, opacity 0.6s;
        }

        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px rgba(208, 173, 0, 0.2);;
        }

        .submit-btn:hover::after {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .submit-btn i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Animación de pulso para el botón de envío */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(74, 108, 247, 0.5);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(74, 108, 247, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(74, 108, 247, 0);
            }
        }

        /* Progress bar en la parte inferior */
        .progress-bar-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px 0;
            background: rgba(12, 20, 69, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.3);
        }
        
        .progress-steps {
            display: flex;
            gap: 8px;
        }
        
        .step {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: var(--gradient-primary);
            transform: scale(1.3);
            box-shadow: 0 0 10px rgba(74, 108, 247, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sistema-card {
                padding: 30px 25px;
            }

            .sistema-description {
                max-width: 100%;
            }

            .sintomas-counter {
                top: 30px;
                right: 25px;
            }

            .sistema-icon {
                width: 50px;
                height: 50px;
            }

            .sistema h2 {
                font-size: 22px;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                padding: 80px 15px 70px;
            }

            .sintomas {
                gap: 10px;
            }

            .sintoma {
                padding: 10px 15px;
                font-size: 0.9rem;
                flex-basis: 100%;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn-nav {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Partículas animadas -->
    <div class="particles" id="particles"></div>
    <header class="header">
        <h1>MediCare360 - Síntomas</h1>
    </header>

    <div class="main-container">
        <div class="sintomas-container">
            <form id="sintomasForm" method="post" action="diagnostico.php">
            
                <!-- Campos ocultos -->
                <input type="hidden" id="lat" name="lat">
                <input type="hidden" id="lng" name="lng">
                <script>
  // Al cargar la página pedimos la ubicación
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function(pos) {
        document.getElementById('lat').value = pos.coords.latitude;
        document.getElementById('lng').value = pos.coords.longitude;
      },
      function(err) {
        console.warn('Error al obtener geolocalización:', err.message);
      }
    );
  } else {
    console.warn('Geolocalización no soportada.');
  }
</script>

                <!-- Sistema Respiratorio -->
                <div id="card-1" class="sistema-card active-card">
                    <div class="sintomas-counter">
                        <i class="fas fa-check-circle counter-icon"></i>
                        <span id="counter-1">0 seleccionados</span>
                    </div>
                    <div class="sistema">
                        <div class="sistema-header">
                            <div class="sistema-icon" style="background: <?= $colores['respiratorio'] ?>">
                                <i class="fas <?= $iconos['respiratorio'] ?>"></i>
                            </div>
                            <h2>Sistema Respiratorio</h2>
                        </div>
                        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema respiratorio que has experimentado recientemente.</p>
                        <div class="sintomas">
                            <?php foreach ($sintomas_respiratorios as $sintoma): ?>
                                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'respiratorio')">
                                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="nav-buttons">
                        <div></div> <!-- Espaciador -->
                        <button type="button" class="btn-nav btn-next" onclick="navigateCard(1, 2)">
                            Sistema Digestivo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Sistema Digestivo -->
                <div id="card-2" class="sistema-card">
                    <div class="sintomas-counter">
                        <i class="fas fa-check-circle counter-icon"></i>
                        <span id="counter-2">0 seleccionados</span>
                    </div>
                    <div class="sistema">
                        <div class="sistema-header">
                            <div class="sistema-icon" style="background: <?= $colores['digestivo'] ?>">
                                <i class="fas <?= $iconos['digestivo'] ?>"></i>
                            </div>
                            <h2>Sistema Digestivo</h2>
                        </div>
                        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema digestivo que has experimentado recientemente.</p>
                        <div class="sintomas">
                            <?php foreach ($sintomas_digestivos as $sintoma): ?>
                                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'digestivo')">
                                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="nav-buttons">
                        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(2, 1)">
                            <i class="fas fa-arrow-left"></i> Sistema Respiratorio
                        </button>
                        <button type="button" class="btn-nav btn-next" onclick="navigateCard(2, 3)">
                            Sistema Nervioso <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Sistema Nervioso -->
                <div id="card-3" class="sistema-card">
                    <div class="sintomas-counter">
                        <i class="fas fa-check-circle counter-icon"></i>
                        <span id="counter-nervioso">0 seleccionados</span>
                    </div>
                    <div class="sistema">
                        <div class="sistema-header">
                            <div class="sistema-icon" style="background: <?= $colores['nervioso'] ?>">
                                <i class="fas <?= $iconos['nervioso'] ?>"></i>
                            </div>
                            <h2>Sistema Nervioso</h2>
                        </div>
                        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema nervioso que has experimentado recientemente.</p>
                        <div class="sintomas">
                            <?php foreach ($sintomas_nerviosos as $sintoma): ?>
                                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'nervioso')">
                                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="nav-buttons">
                        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(3, 2)">
                            <i class="fas fa-arrow-left"></i> Sistema Digestivo
                        </button>
                        <button type="button" class="btn-nav btn-next" onclick="navigateCard(3, 4)">
                            Sistema Musculoesquelético <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <!-- Sistema Musculoesquelético -->
<div id="card-4" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-musculoesqueletico">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['musculoesqueletico'] ?>">
                <i class="fas <?= $iconos['musculoesqueletico'] ?>"></i>
            </div>
            <h2>Sistema Musculoesquelético</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema musculoesquelético que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_musculoesqueleticos as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'musculoesqueletico')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(4, 3)">
            <i class="fas fa-arrow-left"></i> Sistema Nervioso
        </button>
        <button type="button" class="btn-nav btn-next" onclick="navigateCard(4, 5)">
            Sistema Integumentario <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Sistema Integumentario -->
<div id="card-5" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-integumentario">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['integumentario'] ?>">
                <i class="fas <?= $iconos['integumentario'] ?>"></i>
            </div>
            <h2>Sistema Integumentario</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con tu piel, cabello y uñas que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_integumentario as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'integumentario')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(5, 4)">
            <i class="fas fa-arrow-left"></i> Sistema Musculoesquelético
        </button>
        <button type="button" class="btn-nav btn-next" onclick="navigateCard(5, 6)">
            Sistema Urinario <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Sistema Urinario -->
<div id="card-6" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-urinario">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['urinario'] ?>">
                <i class="fas <?= $iconos['urinario'] ?>"></i>
            </div>
            <h2>Sistema Urinario</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema urinario que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_urinario as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'urinario')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(6, 5)">
            <i class="fas fa-arrow-left"></i> Sistema Integumentario
        </button>
        <button type="button" class="btn-nav btn-next" onclick="navigateCard(6, 7)">
            Sistema Endocrino <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Sistema Endocrino -->
<div id="card-7" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-endocrino">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['endocrino'] ?>">
                <i class="fas <?= $iconos['endocrino'] ?>"></i>
            </div>
            <h2>Sistema Endocrino</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema endocrino que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_endocrino as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'endocrino')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(7, 6)">
            <i class="fas fa-arrow-left"></i> Sistema Urinario
        </button>
        <button type="button" class="btn-nav btn-next" onclick="navigateCard(7, 8)">
            Sistema Inmunológico <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Sistema Inmunológico -->
<div id="card-8" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-inmunologico">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['inmunologico'] ?>">
                <i class="fas <?= $iconos['inmunologico'] ?>"></i>
            </div>
            <h2>Sistema Inmunológico</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con tu sistema inmunológico que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_inmunologico_generales as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'inmunologico')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>">
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(8, 7)">
            <i class="fas fa-arrow-left"></i> Sistema Endocrino
        </button>
        <button type="button" class="btn-nav btn-next" onclick="navigateCard(8, 9)">
            Otros Síntomas <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Otros Sistemas -->
<div id="card-9" class="sistema-card">
    <div class="sintomas-counter">
        <i class="fas fa-check-circle counter-icon"></i>
        <span id="counter-otros">0 seleccionados</span>
    </div>
    <div class="sistema">
        <div class="sistema-header">
            <div class="sistema-icon" style="background: <?= $colores['otros'] ?>">
                <i class="fas <?= $iconos['otros'] ?>"></i>
            </div>
            <h2>Otros Síntomas</h2>
        </div>
        <p class="sistema-description">Selecciona los síntomas relacionados con otros aspectos generales que has experimentado recientemente.</p>
        <div class="sintomas">
            <?php foreach ($sintomas_otros as $sintoma): ?>
                <div class="sintoma" onclick="toggleSintoma(this, '<?= $sintoma ?>', 'otros')">
                    <input type="checkbox" id="<?= $sintoma ?>" name="sintomas[]" value="<?= $sintoma ?>"hidden>
                    <label for="<?= $sintoma ?>"><?= $sintoma ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="nav-buttons">
        <button type="button" class="btn-nav btn-prev" onclick="navigateCard(9, 8)">
            <i class="fas fa-arrow-left"></i> Sistema Inmunológico
        </button>
        <button type="submit" class="submit-btn">Guardar Síntomas</button>
    </div>
</div>
<script>
// Esta variable mantiene el total de síntomas seleccionados globalmente
let sintomasSeleccionados = 0;

// Esta función maneja la selección de un síntoma
function toggleSintoma(element, sintoma, system) {
    const checkbox = element.querySelector('input[type="checkbox"]');

    // Cambiar el estado del checkbox
    checkbox.checked = !checkbox.checked;
    element.classList.toggle('checked');

    // Si el síntoma está seleccionado, lo sumamos al total
    if (checkbox.checked) {
        sintomasSeleccionados++;
    } else {
        // Si se desmarca, lo restamos del total
        sintomasSeleccionados--;
    }

    // Actualizamos el contador global en todas las tarjetas
    updateGlobalCounter();

    // Actualizamos el contador específico de la tarjeta
    const cardCounter = document.getElementById('counter-' + system);
    if (cardCounter) {
        cardCounter.innerText = `${sintomasSeleccionados} seleccionados`;
    }
}

// Esta función actualiza el contador global visible en todas las tarjetas
function updateGlobalCounter() {
    // Aquí se muestra el total de síntomas seleccionados en todas las tarjetas
    const globalCounters = document.querySelectorAll('.sintomas-counter span');
    globalCounters.forEach(counter => {
        counter.innerText = `${sintomasSeleccionados} seleccionados`;
    });
}

// Función para cambiar de tarjeta
function navigateCard(currentCardNumber, nextCardNumber) {
    const currentCard = document.getElementById('card-' + currentCardNumber);
    currentCard.classList.remove('active-card');

    const nextCard = document.getElementById('card-' + nextCardNumber);
    nextCard.classList.add('active-card');
}

</script>
</body>
</html>
