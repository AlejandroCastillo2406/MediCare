<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit;
}

// Configuración de la API de Gemini (Google Generative Language API)
$gemini_api_key = "AIzaSyCrlwUcQaNVDkSAGTd5dtveophr9gXmGJc";
$gemini_endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $gemini_api_key;

// Configuración de la API de Google Places
$google_places_api_key = "AIzaSyCUv0bmRnGTLwNA8dgDY80Ol9J7_ZRUSWg";
$google_places_endpoint = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";

// Función para registrar errores en un archivo de log
function logError($message) {
    $log_file = __DIR__ . '/api_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Obtener los síntomas enviados desde el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sintomas'])) {
    $sintomas = trim($_POST['sintomas']);
    $lat = isset($_POST['lat']) ? $_POST['lat'] : null;
    $lng = isset($_POST['lng']) ? $_POST['lng'] : null;
    $ubicacion = ($lat && $lng) ? "$lat,$lng" : null;

    // Validar que los síntomas no estén vacíos
    if (empty($sintomas)) {
        $respuesta_IA = "Por favor, describe tus síntomas.";
        $especialistas = [];
    } else {
        // Primera consulta a Gemini para diagnóstico y recomendaciones
        $data_diagnostico = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => "Eres doctor que responde en español. Basándote en los síntomas proporcionados ('$sintomas'), 
                            genera una respuesta en el siguiente formato exacto:
                            Posible enfermedad: [nombre de la enfermedad o condición, solo mostrar 1 enfermedad]
                            **Recomendaciones:**
                            1. [primera recomendación]
                            2. [segunda recomendación]
                            3. [tercera recomendación]
                            
                            No agregues nada más, solo respóndeme en ese formato."
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 200
            ]
        ];

        // Configurar la solicitud a Gemini para diagnóstico
        $ch = curl_init($gemini_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_diagnostico));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);

        // Ejecutar la solicitud de diagnóstico
        $response_diagnostico = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Procesar la respuesta de diagnóstico
        if ($http_code == 200 && $response_diagnostico !== false) {
            $result = json_decode($response_diagnostico, true);
            $respuesta_IA = $result['candidates'][0]['content']['parts'][0]['text'] ?? "No se pudo obtener un diagnóstico.";
            
            // Extraer el nombre de la enfermedad para buscar especialistas
            $enfermedad = "";
            if (preg_match('/Posible enfermedad:\s*(.+)/', $respuesta_IA, $matches)) {
                $enfermedad = trim($matches[1]);
            }
            
            // Si encontramos una enfermedad, buscamos el especialista adecuado
            if (!empty($enfermedad)) {
                // Segunda consulta a Gemini para obtener el tipo de especialista
                $data_especialista = [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => "¿Qué tipo de médico especialista trata la condición '$enfermedad'? Responde solo con el nombre del especialista, por ejemplo: 'Cardiólogo', 'Neurólogo', 'Dermatólogo', etc. No incluyas nada más en tu respuesta."
                                ]
                            ]
                        ]
                    ],
                    "generationConfig" => [
                        "temperature" => 0.3,
                        "maxOutputTokens" => 50
                    ]
                ];
                
                $ch = curl_init($gemini_endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_especialista));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json"
                ]);
                
                $response_especialista = curl_exec($ch);
                $http_code_esp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                $especialidad = "";
                if ($http_code_esp == 200 && $response_especialista !== false) {
                    $result_esp = json_decode($response_especialista, true);
                    $especialidad = trim($result_esp['candidates'][0]['content']['parts'][0]['text'] ?? "");
                }
                
                // Buscar especialistas con Google Places
                $especialistas = [];
                if ($ubicacion && !empty($especialidad)) {
                    $google_params = [
                        "location" => $ubicacion,
                        "radius" => 5000, // 5km de radio
                        "keyword" => $especialidad,
                        "key" => $google_places_api_key
                    ];
                    
                    $google_url = $google_places_endpoint . "?" . http_build_query($google_params);
                    
                    $ch = curl_init($google_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $google_response = curl_exec($ch);
                    $google_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $google_error = curl_error($ch);
                    curl_close($ch);
                    
                    if ($google_http_code == 200 && $google_response !== false) {
                        $google_result = json_decode($google_response, true);
                        $results = $google_result['results'] ?? [];
                        
                        // Limitar a 3 resultados
                        $count = 0;
                        foreach ($results as $place) {
                            if ($count >= 3) break;
                            $especialistas[] = [
                                "name" => $place['name'] ?? "Especialista Desconocido",
                                "address" => $place['vicinity'] ?? "Dirección no disponible",
                                "rating" => $place['rating'] ?? "Sin calificación",
                                "especialidad" => $especialidad
                            ];
                            $count++;
                        }
                    } else {
                        $respuesta_IA .= "\n\nError al buscar especialistas: Código $google_http_code.";
                    }
                } elseif (!$ubicacion) {
                    $respuesta_IA .= "\n\nNo se pudo obtener tu ubicación para buscar especialistas cercanos.";
                }
            }
        } elseif ($http_code == 429) {
            $respuesta_IA = "Error: Has excedido el límite de solicitudes (Código 429). Por favor, espera un momento o verifica tu cuota en https://console.cloud.google.com/";
        } else {
            $respuesta_IA = "Error al conectar con Gemini 1.5 Flash: Código $http_code. Detalle: $error. Verifica tu clave API y el endpoint en https://console.cloud.google.com/";
            logError("HTTP $http_code: $error\nResponse: $response_diagnostico");
        }
    }
} else {
    $respuesta_IA = "No se proporcionaron síntomas.";
    $especialistas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #c99322;
            --color-secondary: rgba(255,255,255,0.4);
            --color-background: #0a0a0a;
            --color-text: #a8a8a8;
            --gradient-primary: linear-gradient(135deg, #c99322 10%, #333333 50%, #c99322 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background: 
                radial-gradient(circle at 10% 20%, #161616 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, #161616 0%, transparent 40%),
                var(--color-background);
        }

        .ia-container {
            width: 100%;
            max-width: 800px;
            background: #161616;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            position: relative;
        }

        .ia-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gradient-primary);
        }

        h1, h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        h2 {
            font-size: 1.8rem;
        }

        .resultado {
            margin-bottom: 20px;
        }

        .diagnostico {
            background: rgba(255,255,255,0.03);
            border-left: 3px solid var(--color-primary);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
            text-align: left;
        }

        .recomendaciones {
            margin-top: 20px;
        }

        .recomendaciones h3 {
            color: var(--color-primary);
            margin-bottom: 10px;
            text-align: center;
        }

        .recomendacion-card {
            background: rgba(255,255,255,0.03);
            border-left: 3px solid var(--color-primary);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
            text-align: left;
        }

        .recomendacion-card p {
            margin: 0;
        }

        .especialistas {
            background: #161616;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h3 {
            color: var(--color-primary);
            margin-bottom: 10px;
            text-align: center;
            font-size: 1.2rem;
        }

        .especialista-card {
            background: rgba(255,255,255,0.03);
            border-left: 3px solid var(--color-primary);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
        }

        .especialista-card h4 {
            margin: 0 0 5px 0;
            color: #fff;
        }

        .especialista-card p {
            margin: 5px 0;
        }

        .especialidad {
            font-style: italic;
            color: var(--color-primary);
        }

        .rating {
            color: #FFD700;
        }

        .error {
            color: #ff4d4d;
            font-weight: 600;
        }

        .back-link {
            display: block;
            text-align: center;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--color-secondary);
        }

        .map-link {
            color: var(--color-text);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .map-link:hover {
            color: var(--color-text);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="ia-container">
        <h1>MediCare360 - Análisis de Síntomas</h1>
        
        <div class="resultado">
            <?php
            // Separar el diagnóstico y las recomendaciones
            $lines = explode("\n", $respuesta_IA);
            $diagnostico = "";
            $recomendaciones = [];
            $in_recomendaciones = false;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (str_contains($line, 'Posible enfermedad:')) {
                    $diagnostico = $line;
                } elseif (str_contains($line, 'Recomendaciones:')) {
                    $in_recomendaciones = true;
                } elseif ($in_recomendaciones && preg_match('/^\d+\./', $line)) {
                    // Extraer solo el texto de la recomendación, sin el número
                    $recomendaciones[] = trim(preg_replace('/^\d+\.\s*/', '', $line));
                }
            }
            ?>

            <?php if ($diagnostico): ?>

                <h3>Posible Enfermedad:</h3>
                <div class="diagnostico">
                    
                    <?php echo nl2br(htmlspecialchars($enfermedad)); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($recomendaciones)): ?>
                <div class="recomendaciones">
                    <h3>Recomendaciones:</h3>
                    <?php foreach ($recomendaciones as $rec): ?>
                        <div class="recomendacion-card">
                            <p><?php echo htmlspecialchars($rec); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($especialistas)): ?>
            <div class="resultado">
                <h3>Especialistas Cercanos</h3>
                <p>Basado en tu ubicación y condición, te recomendamos estos especialistas:</p>
                <?php foreach ($especialistas as $esp): ?>
                    <div class="especialista-card">
                        <h4><?php echo htmlspecialchars($esp['name']); ?></h4>
                        <p class="especialidad"><?php echo htmlspecialchars($esp['especialidad']); ?></p>
                        <p>
                            <i class="fas fa-map-marker-alt"></i> 
                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($esp['address']); ?>" target="_blank" class="map-link">
                                <?php echo htmlspecialchars($esp['address']); ?>
                            </a>
                        </p>
                        <?php if ($esp['rating'] !== "Sin calificación"): ?>
                            <p class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($esp['rating']); ?>/5</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="error">No se encontraron especialistas cercanos.</p>
        <?php endif; ?>

        <a href="seleccion.php" class="back-link">Volver a seleccionar modo</a>
    </div>
</body>
</html>