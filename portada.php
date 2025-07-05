<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portada - MediCare360</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #00a8ff;
            --color-secondary: #8e44ad;
            --color-background: #0c1445;
            --color-text: #f1f2f6;
            --gradient-primary: linear-gradient(135deg, #00a8ff, #8e44ad);
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
            height: 100vh;
            overflow: hidden;
            background: radial-gradient(circle at 3% 25%, rgba(255,255,255,0.03) 0%, transparent 25%),
                        radial-gradient(circle at 97% 75%, rgba(255,255,255,0.03) 0%, transparent 25%),
                        var(--color-background);
        }

        .presentation-container {
            text-align: center;
            animation: fadeOut 5s forwards;
        }

        .presentation-container h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .presentation-container p {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 40px;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }

        .integrantes-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .integrante-card {
            background: rgba(255, 255, 255, 0.1); 
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, background-color 0.3s ease; 
            text-align: center;
            width: 200px; 
            margin-bottom: 20px; 
        }

        .integrante-card:hover {
            transform: scale(1.05); 
            background-color: rgba(0, 168, 255, 0.2); 
        }

        .integrante-card p {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--color-text);
        }


        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    </style>
        <script>
            setTimeout(() => {
            window.location.href = 'login.php';
        }, 5000);
        </script>
</head>
<body>
    <div class="presentation-container">
        <img src="images/tec.png" alt="Logo de MediCare360" class="logo">
        <h1>Instituto Tecnológico Superior de Poza Rica</h1>
        <p><b>Docente:</b> Simon Garcia Ortiz</p>
        <p><b>Integrantes:</b></p>
    <div class="integrantes-container">
    <div class="integrante-card">
        <p>Julinka Rosemary Hernandez Mateos</p>
    </div>
    <div class="integrante-card">
        <p>César Alejandro Castillo Garcés</p>
    </div>
    <div class="integrante-card">
        <p>Genaro Jongitud Castellanos</p>
    </div>
    <div class="integrante-card">
        <p>Jesús Adolfo Fuentes Negrete</p>
    </div>
</body>
</html>
