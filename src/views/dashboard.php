<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tauler Principal - Tracking d'Hores</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 30px;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
        }

        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .card p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <header>
        <h2>⏱️ Tracking d'Hores</h2>
        <div class="user-info">
            <span>Benvingut, <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong></span>
            <a href="/logout" class="logout-btn">Tancar sessió</a>
        </div>
    </header>

    <div class="container">
        <h1>Tauler Principal</h1>

        <?php if ($missatge): ?>
        <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px;">
            ✅ <?= htmlspecialchars($missatge) ?>
        </div>
        <?php endif; ?>

        <div class="welcome-card">
            <h2>👋 Benvingut al sistema, <?= htmlspecialchars($_SESSION['user_nom']) ?></h2>
            
            <?php if ($sessioActual && $sessioActual['estat'] === 'activa'): ?>
                <div style="margin-top:15px; padding:15px; background:#dcfce7; border-radius:8px;">
                    <strong>🟢 Estàs fitxat des de:</strong> <?= date('H:i:s d/m/Y', strtotime($sessioActual['hora_entrada'])) ?>
                </div>
            <?php else: ?>
                <div style="margin-top:15px; padding:15px; background:#fef3c7; border-radius:8px;">
                    <strong>🟡 No estàs fitxat actualment</strong> - Marca entrada per començar el torn
                </div>
            <?php endif; ?>
        </div>

        <div class="grid">
            <form method="POST" action="/marcar-entrada" class="card">
                <h3>🕒 Marcar Entrada</h3>
                <p>Registra l'hora d'entrada al lloc de treball i comença la sessió laboral.</p>
                <button type="submit" style="margin-top:15px; background:#667eea; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; width:100%;">Marcar Entrada</button>
            </form>

            <form method="POST" action="/marcar-sortida" class="card">
                <h3>🚪 Marcar Sortida</h3>
                <p>Finalitza la sessió laboral i registra l'hora de sortida.</p>
                <button type="submit" style="margin-top:15px; background:#ef4444; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; width:100%;">Marcar Sortida</button>
            </form>

            <div class="card">
                <h3>📋 Projectes</h3>
                <p>Visualitza tots els projectes assignats i el temps treballat en cadascun.</p>
            </div>

            <div class="card">
                <h3>📊 Informes</h3>
                <p>Consulta els informes detallats de les hores treballades per setmana i mes.</p>
            </div>
        </div>
    </div>
</body>
</html>