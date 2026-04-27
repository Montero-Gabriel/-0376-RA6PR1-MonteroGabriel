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
        <?php if ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'admin' || $_SESSION['user_rol'] === 'superadmin'): ?>
            <h1>🔧 TAULER ADMINISTRADOR</h1>
            <p style="color:#666; margin-bottom:20px;">Veu TOTS els usuaris i el seu fitxatge</p>
        <?php else: ?>
            <h1>📋 El meu fitxatge</h1>
            <p style="color:#666; margin-bottom:20px;">Només pots veure la teva informació personal</p>
        <?php endif; ?>

        <?php if (isset($missatge) && $missatge): ?>
            <?php 
            $color_fondo = '#dcfce7';
            $color_texto = '#166534';
            if (str_starts_with($missatge, '❌')) {
                $color_fondo = '#fee2e2';
                $color_texto = '#991b1b';
            } elseif (str_starts_with($missatge, '⚠️')) {
                $color_fondo = '#fef3c7';
                $color_texto = '#92400e';
            }
            ?>
            <div style="background:<?= $color_fondo ?>; color:<?= $color_texto ?>; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:500;">
                <?= htmlspecialchars($missatge) ?>
            </div>
        <?php endif; ?>

        <div class="welcome-card">
            <h2>👋 Benvingut al sistema, <?= htmlspecialchars($_SESSION['user_nom']) ?></h2>
            
            <?php if (isset($_SESSION['sessio_activa']) && $_SESSION['sessio_activa']): ?>
                <div style="margin-top:15px; padding:15px; background:#dcfce7; border-radius:8px;">
                    <strong>🟢 Estàs fitxat des de:</strong> <?= date('H:i:s d/m/Y', strtotime($_SESSION['hora_entrada'])) ?>
                </div>
            <?php elseif (isset($sessioActual) && $sessioActual && $sessioActual['estat'] === 'activa'): ?>
                <div style="margin-top:15px; padding:15px; background:#dcfce7; border-radius:8px;">
                    <strong>🟢 Estàs fitxat des de:</strong> <?= date('H:i:s d/m/Y', strtotime($sessioActual['hora_entrada'])) ?>
                </div>
            <?php else: ?>
                <div style="margin-top:15px; padding:15px; background:#fef3c7; border-radius:8px;">
                    <strong>🟡 No estàs fitxat actualment</strong> - Marca entrada per començar el torn
                </div>
            <?php endif; ?>
        </div>

        <!-- NOMÉS PER A ADMINISTRADORS -->
        <?php if ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'admin' || $_SESSION['user_rol'] === 'superadmin'): ?>
        <?php 
        // COMPROVAR USUARIS ACTIUS
        $alertes = [];
        foreach ($totsUsuaris ?? [] as $usuari) {
            if ($usuari['estat'] == 'activa' && $usuari['hores'] > 8) {
                $alertes[] = $usuari;
            }
        }
        ?>

        <?php if (count($alertes) > 0): ?>
        <div class="welcome-card" style="border-left: 8px solid #ef4444; background: #fff1f2;">
            <h2 style="color: #b91c1c;">🚨 LLISTA VERMELLA - TEMPS EXCEDIT!</h2>
            <br>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($alertes as $a): ?>
                <li style="padding: 12px; background: white; margin-bottom: 8px; border-radius: 8px;">
                    <strong>🔴 <?= htmlspecialchars($a['nom']) ?></strong> porta més de 8 hores treballant! Actualment <strong><?= number_format($a['hores'], 1) ?> h</strong>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="welcome-card">
            <h2>👥 TOTS ELS USUARIS DEL SISTEMA</h2>
            <br>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background:#667eea; color:white;">
                            <th style="padding:12px; text-align:left; border-radius:8px 0 0 0;">ID</th>
                            <th style="padding:12px; text-align:left;">Nom Usuari</th>
                            <th style="padding:12px; text-align:left;">Projecte Actual</th>
                            <th style="padding:12px; text-align:left;">Estat</th>
                            <th style="padding:12px; text-align:left;">Hora Entrada</th>
                            <th style="padding:12px; text-align:left;">Hora Sortida</th>
                            <th style="padding:12px; text-align:left; border-radius:0 8px 0 0;">Hores totals</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($totsUsuaris ?? [] as $usuari): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;"><?= htmlspecialchars($usuari['id_usuari'] ?? $usuari['id'] ?? '-') ?></td>
                            <td style="padding:12px;"><strong><?= htmlspecialchars($usuari['nom']) ?></strong></td>
                            <td style="padding:12px;"><em><?= htmlspecialchars($usuari['projecte'] ?? $usuari['nom_projecte'] ?? '-') ?></em></td>
                            <td style="padding:12px;">
                                <?php if ($usuari['estat'] == 'activa'): ?>
                                    <span style="color:green;">🟢 Fitxat</span>
                                <?php elseif ($usuari['estat'] == 'finalitzada'): ?>
                                    <span style="color:gray;">⚫ Finalitzat</span>
                                <?php else: ?>
                                    <span style="color:orange;">🟡 Sense fitxar</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px;"><?= $usuari['hora_entrada'] ? date('H:i d/m', strtotime($usuari['hora_entrada'])) : '-' ?></td>
                            <td style="padding:12px;"><?= $usuari['hora_sortida'] ? date('H:i d/m', strtotime($usuari['hora_sortida'])) : '-' ?></td>
                            <td style="padding:12px;"><strong><?= $usuari['hores'] ?? '0' ?> h</strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- BOTONS DE FITXATGE PER TOTS ELS USUARIS INCLÒS ADMINISTRADORS -->
        <div style="display: grid; grid-template-columns: 1fr; gap: 25px; max-width: 600px; margin: 40px auto;">
            
            <?php if (!(isset($_SESSION['sessio_activa']) && $_SESSION['sessio_activa'])): ?>
            <form method="POST" action="/marcar-entrada" style="background: #667eea; border-radius: 20px; padding: 40px; color: white; text-align: center;">
                <h2 style="font-size: 3rem; margin-bottom: 15px;">▶️</h2>
                <h2 style="margin-bottom: 10px;">COMENÇAR TORN</h2>
                <p style="opacity:0.9; margin-bottom: 20px;">Clica aquí quan comencis a treballar</p>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; text-align: left;">Nom del projecte que faràs</label>
                    <input type="text" name="projecte" required placeholder="Escriu aquí el nom del projecte" style="width: 100%; padding: 15px; border-radius: 10px; border: none; font-size: 1rem;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; text-align: left;">Quantes hores ha de durar aquesta tasca?</label>
                    <input type="number" name="hores_estimades" required min="0.5" max="24" step="0.5" placeholder="Exemple: 4" style="width: 100%; padding: 15px; border-radius: 10px; border: none; font-size: 1rem;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 20px; background: white; color: #667eea; border: none; border-radius: 12px; font-size: 1.3rem; font-weight: 700; cursor: pointer;">
                    ENTRAR AL TREBALL
                </button>
            </form>
            <?php endif; ?>

            <?php if (isset($_SESSION['sessio_activa']) && $_SESSION['sessio_activa']): ?>
            <form method="POST" action="/marcar-sortida" style="background: #ef4444; border-radius: 20px; padding: 40px; color: white; text-align: center;">
                <h2 style="font-size: 3rem; margin-bottom: 15px;">⏹️</h2>
                <h2 style="margin-bottom: 10px;">FINALITZAR TORN</h2>
                <p style="opacity:0.9; margin-bottom: 20px;">Clica aquí quan acabis de treballar</p>
                <button type="submit" style="width: 100%; padding: 20px; background: white; color: #ef4444; border: none; border-radius: 12px; font-size: 1.3rem; font-weight: 700; cursor: pointer;">
                    SORTIR
                </button>
            </form>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
