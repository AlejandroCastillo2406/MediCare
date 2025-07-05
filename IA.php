<?php
session_start();

if (!isset($_SESSION['correo'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente de IA - MediCare360</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: 
                radial-gradient(circle at 10% 20%, #161616 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, #161616 0%, transparent 40%),
                var(--color-background);
            padding: 20px;
        }

        .ia-container {
            width: 100%;
            max-width: 800px;
            background: #161616;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
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

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .ia-description {
            text-align: center;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        .ia-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        textarea {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--color-text);
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            min-height: 150px;
            resize: vertical;
            transition: all 0.3s ease;
        }

        textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(208, 173, 0, 0.1);
        }

        .submit-btn {
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
            box-shadow: 0 5px 15px rgba(208, 173, 0, 0.2);
        }

        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(208, 173, 0, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--color-secondary);
        }

        .warning {
            text-align: center;
            font-size: 1.5rem;
            color: #ff4d4d;
            margin-top: 20px;
        }

        .location-status {
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .location-success {
            color: #4CAF50;
        }

        .location-error {
            color: #ff4d4d;
        }
    </style>
</head>
<body>
    <div class="ia-container">
        <h1>Asistente de IA</h1>
        <p class="ia-description">Describe tus síntomas en tus propias palabras y nuestra inteligencia artificial te ayudará a identificar posibles condiciones médicas.</p>
        
        <div class="location-status" id="locationStatus">
            <i class="fas fa-spinner fa-spin"></i> Solicitando acceso a tu ubicación...
        </div>
        
        <form class="ia-form" method="post" action="procesar_ia.php" id="symptomsForm">
            <input type="hidden" id="lat" name="lat">
            <input type="hidden" id="lng" name="lng">
            <textarea name="sintomas" placeholder="Ejemplo: Tengo dolor de cabeza desde hace 3 días, fiebre de 38°C y dolor muscular..." required></textarea>
            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                <i class="fas fa-robot"></i> Analizar síntomas
            </button>
        </form>
        
        <p class="warning">Advertencia: Este sistema proporciona información general y no sustituye un diagnóstico médico profesional. Consulta a un médico para obtener un diagnóstico preciso.</p>
        
        <a href="seleccion.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver a selección
        </a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const locationStatus = document.getElementById('locationStatus');
            const submitBtn = document.getElementById('submitBtn');
            const latInput = document.getElementById('lat');
            const lngInput = document.getElementById('lng');
            
            // Verificar si el navegador soporta geolocalización
            if (!navigator.geolocation) {
                locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle location-error"></i> Tu navegador no soporta geolocalización. Los resultados pueden ser menos precisos.';
                submitBtn.disabled = false;
                return;
            }
            
            // Solicitar ubicación
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Ubicación obtenida con éxito
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    
                    locationStatus.innerHTML = '<i class="fas fa-check-circle location-success"></i> Ubicación obtenida correctamente.';
                    submitBtn.disabled = false;
                },
                function(error) {
                    // Error al obtener ubicación
                    let errorMessage;
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = "Permiso denegado. Los resultados pueden ser menos precisos.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = "Información de ubicación no disponible.";
                            break;
                        case error.TIMEOUT:
                            errorMessage = "Tiempo de espera agotado al solicitar ubicación.";
                            break;
                        case error.UNKNOWN_ERROR:
                            errorMessage = "Error desconocido al obtener ubicación.";
                            break;
                    }
                    
                    locationStatus.innerHTML = `<i class="fas fa-exclamation-triangle location-error"></i> ${errorMessage}`;
                    submitBtn.disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
            
            // Validación del formulario
            document.getElementById('symptomsForm').addEventListener('submit', function(e) {
                const sintomas = document.querySelector('textarea[name="sintomas"]').value.trim();
                if (sintomas.length < 10) {
                    e.preventDefault();
                    alert("Por favor, describe tus síntomas con más detalle (mínimo 10 caracteres).");
                    return false;
                }
                return true;
            });
        });
    </script>
</body>
</html>