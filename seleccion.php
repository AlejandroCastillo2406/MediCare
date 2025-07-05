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
    <title>Selección - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
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

        .selection-container {
            width: 100%;
            max-width: 800px;
            background: #161616;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }

        .selection-container::before {
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
            margin-bottom: 30px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .options {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
            flex-wrap: wrap;
        }

        .option-card {
            width: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.4s ease;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(208, 173, 0, 0.2);
            border-color: var(--color-primary);
        }

        .option-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--color-primary);
        }

        .option-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .option-description {
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .option-btn {
            padding: 10px 20px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .option-btn:hover {
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .options {
                flex-direction: column;
                align-items: center;
            }
            
            .option-card {
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="selection-container">
        <h1>¿Cómo prefieres continuar?</h1>
        <p>Selecciona el método que prefieras para realizar tu diagnóstico</p>
        
        <div class="options">
            <div class="option-card" onclick="window.location.href='sintomas.php'">
                <div class="option-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="option-title">Seleccionar síntomas</h3>
                <p class="option-description">Elige manualmente los síntomas que estás experimentando para obtener un diagnóstico preciso.</p>
                <button class="option-btn">Seleccionar</button>
            </div>
            
            <div class="option-card" onclick="window.location.href='IA.php'">
                <div class="option-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="option-title">Inteligencia Artificial</h3>
                <p class="option-description">Describe tus síntomas en lenguaje natural y nuestra inteligencia artificial te ayudará.</p>
                <button class="option-btn">Usar IA</button>
            </div>
        </div>
    </div>
</body>
</html>