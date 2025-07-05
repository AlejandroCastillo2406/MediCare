<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit;
}

// 1) Procesar síntomas seleccionados
$sintomas_seleccionados = isset($_POST['sintomas']) ? $_POST['sintomas'] : [];
if (empty($sintomas_seleccionados)) {
    echo "<script>alert('No se seleccionaron síntomas.'); window.location.href='sintomas.php';</script>";
    exit;
}
$sintomas_listados = implode("','", $sintomas_seleccionados);

// 2) Determinar la enfermedad principal
$query_enfermedades = "
    SELECT e.nombre, COUNT(DISTINCT s.sintoma) AS num_coincidencias
FROM Enfermedades e
INNER JOIN Sintomas s ON e.nombre = s.nombre_enfermedad
WHERE s.sintoma IN ('$sintomas_listados')
GROUP BY e.nombre
ORDER BY num_coincidencias DESC
LIMIT 1;

";
$stmt_enf = $conn->prepare($query_enfermedades);
$stmt_enf->execute();
$enf = $stmt_enf->fetch(PDO::FETCH_ASSOC);
$nombre_enfermedad = $enf['nombre'] ?? null;

// 3) Obtener recomendaciones
$recomendaciones = [];
if ($nombre_enfermedad) {
    $query_rec = "
        SELECT recomendacion
          FROM Recomendaciones
         WHERE nombre_enfermedad = :nombre_enfermedad
    ";
    $stmt_rec = $conn->prepare($query_rec);
    $stmt_rec->execute(['nombre_enfermedad' => $nombre_enfermedad]);
    $recomendaciones = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);
}

// 4) Mapear cada enfermedad a una especialidad para Google Places
$mapEspecialidades = [
    'Alergia estacional'      => 'alergólogo',
    'Amigdalitis'             => 'otorrinolaringólogo',
    'Anemia'                  => 'hematólogo',
    'Ansiedad'                => 'psiquiatra',
    'Apendicitis'             => 'cirujano general',
    'Artritis'                => 'reumatólogo',
    'Asma'                    => 'neumólogo',
    'Bronquitis'              => 'neumólogo',
    'Cálculos renales'        => 'urólogo',
    'Candidiasis'             => 'ginecólogo',
    'Cervicalgia'             => 'ortopedista',
    'Cistitis'                => 'urólogo',
    'Colitis'                 => 'gastroenterólogo',
    'Conjuntivitis'           => 'oftalmólogo',
    'COVID-19'                => 'infectólogo',
    'Depresión'               => 'psiquiatra',
    'Dermatitis'              => 'dermatólogo',
    'Deshidratación'          => 'médico general',
    'Diabetes'                => 'endocrinólogo',
    'Diarrea'                 => 'gastroenterólogo',
    'Eczema'                  => 'dermatólogo',
    'Embarazo'                => 'obstetra',
    'Esguince'                => 'ortopedista',
    'Estreñimiento'           => 'gastroenterólogo',
    'Faringitis'              => 'otorrinolaringólogo',
    'Gastritis'               => 'gastroenterólogo',
    'Gastroenteritis'         => 'gastroenterólogo',
    'Gripe'                   => 'médico general',
    'Hemorroides'             => 'coloproctólogo',
    'Hepatitis A'             => 'infectólogo',
    'Herpes labial'           => 'dermatólogo',
    'Hipertensión'            => 'cardiólogo',
    'Hipertiroidismo'         => 'endocrinólogo',
    'Hipotiroidismo'          => 'endocrinólogo',
    'Infección de garganta'   => 'otorrinolaringólogo',
    'Infección urinaria'      => 'urólogo',
    'Insomnio'                => 'psiquiatra',
    'Lumbalgia'               => 'ortopedista',
    'Migraña'                 => 'neurólogo',
    'Neumonía'                => 'neumólogo',
    'Otitis'                  => 'otorrinolaringólogo',
    'Paperas'                 => 'infectólogo',
    'Pie de atleta'           => 'dermatólogo',
    'Reflujo gastroesofágico' => 'gastroenterólogo',
    'Resfriado común'         => 'médico general',
    'Rubéola'                 => 'infectólogo',
    'Sarampión'               => 'infectólogo',
    'Sinusitis'               => 'otorrinolaringólogo',
    'Tendinitis'              => 'ortopedista',
    'Urticaria'               => 'alergólogo',
    'Varicela'                => 'infectólogo',
];
$searchKeyword = $mapEspecialidades[$nombre_enfermedad] ?? $nombre_enfermedad;

// 5) Capturar geolocalización enviada desde sintomas.php
$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : null;
$lng = isset($_POST['lng']) ? floatval($_POST['lng']) : null;

// 6) Llamar a Google Places y obtener hasta 3 especialistas
$especialistas = [];
if ($lat && $lng && $searchKeyword) {
    $apiKey   = 'AIzaSyCUv0bmRnGTLwNA8dgDY80Ol9J7_ZRUSWg';
    $radius   = 5000; // metros
    $endpoint = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

    $params = http_build_query([
        'location' => "{$lat},{$lng}",
        'radius'   => $radius,
        'type'     => 'doctor',
        'keyword'  => $searchKeyword,
        'key'      => $apiKey,
    ]);
    $url = "{$endpoint}?{$params}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($resp, true);
    if (!empty($data['results'])) {
        foreach (array_slice($data['results'], 0, 3) as $place) {
            $especialistas[] = [
                'name'     => $place['name']     ?? 'Nombre no disponible',
                'vicinity' => $place['vicinity'] ?? 'Dirección no disponible',
                'rating'   => $place['rating']   ?? null,
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;7 00&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --color-primary: #c99322;
            --color-secondary: rgba(255,255,255,0.4);
            --color-background: #0a0a0a;
            --color-text: #a8a8a8;
            --gradient-primary: linear-gradient(135deg, #c99322 10%, #333333 50%, #c99322 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle, #333333 30%, #0a0a0a 60%);
            color: var(--color-text);
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; padding: 20px; overflow-x: hidden;
        }
        .resultado-container {
            background: #161616;
            border-radius: 20px; padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            max-width: 800px; width: 100%; text-align: center;
            display: none;
        }
        h1 {
            font-size: 2.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .resultado h2, .especialistas h3, .recomendaciones h3 {
            color: var(--color-primary);
            font-size: 1.8rem; margin-bottom: 20px;
        }
        .especialista-card {
            background: rgba(255,255,255,0.03);
            border-left: 3px solid var(--color-primary);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
            text-align: left;
        }
        .especialista-card h4 {
            margin: 0 0 5px 0;
            color: #fff;
            font-size: 1.2rem;
        }
        .especialista-card p {
            margin: 5px 0;
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
        .especialidad {
            font-style: italic;
            color: var(--color-primary);
        }
        .rating {
            color: #FFD700;
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
        .boton-regresar {
            display: inline-block; padding: 12px 30px;
            background: var(--gradient-primary); color: #fff;
            text-decoration: none; border-radius: 10px;
            font-weight: 600; margin-top: 20px;
            transition: all 0.4s ease;
        }
        .boton-regresar:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(208, 173, 0, 0.2)
        }
        .alerta {
            position: fixed; top:50%; left:50%;
            transform:translate(-50%,-50%);
            background:var(--gradient-primary); color:#fff;
            padding:30px 50px; border-radius:15px;
            box-shadow:0 20px 40px rgba(0,0,0,0.4);
            font-size:1.5rem; text-align:center; z-index:1000;
            animation:fadeIn 1s ease;
        }
        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    </style>
</head>
<body>
    <div class="alerta" id="alerta">
        ¡Atención! Esta página es solo un apoyo, se recomienda acudir a un médico.
    </div>

    <div class="resultado-container" id="resultado">
        <h1>Diagnóstico</h1>
        <div class="resultado">
            <?php if ($nombre_enfermedad): ?>
                <h2>Posible enfermedad: <?= htmlspecialchars($nombre_enfermedad, ENT_QUOTES, 'UTF-8') ?></h2>
                <div class="recomendaciones">
                    <h3>Recomendaciones:</h3>
                    <?php if (!empty($recomendaciones)): ?>
                        <?php foreach ($recomendaciones as $rec): ?>
                            <div class="recomendacion-card">
                                <p><?php echo htmlspecialchars($rec, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay recomendaciones disponibles.</p>
                    <?php endif; ?>
                </div>
                <div class="especialistas">
                    <?php if (!empty($especialistas)): ?>
                        <h3>Especialistas cercanos:</h3>
                        <?php foreach ($especialistas as $esp): ?>
                            <div class="especialista-card">
                                <h4><?php echo htmlspecialchars($esp['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <p class="especialidad"><?php echo htmlspecialchars($mapEspecialidades[$nombre_enfermedad] ?? 'Especialista', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p>
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($esp['vicinity']); ?>" target="_blank" class="map-link">
                                        <?php echo htmlspecialchars($esp['vicinity'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </p>
                                <?php if ($esp['rating'] !== null): ?>
                                    <p class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($esp['rating'], ENT_QUOTES, 'UTF-8'); ?>/5</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h3>No se encontraron especialistas de “<?= htmlspecialchars($nombre_enfermedad, ENT_QUOTES, 'UTF-8') ?>” cerca de ti.</h3>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <h2>No se encontró ninguna enfermedad que coincida con los síntomas seleccionados.</h2>
                <p>Se recomienda acudir a su médico de confianza.</p>
            <?php endif; ?>
            <a href="seleccion.php" class="boton-regresar">Regresar a Seleccionar Modo</a>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('alerta').style.display = 'none';
            document.getElementById('resultado').style.display = 'block';
        }, 5000);
    </script>
</body>
</html>