# 🕒 Aplicació de Tracking d'Hores de Feina
## Projecte RA6PR1 - Montero Gabriel

> Aplicació web de control de presència i seguiment de temps per equips de fins a 400 empleats. Desenvolupada amb PHP + MySQL.

---

## 📋 Requisits Funcionals Identificats del Briefing del Client

| Categoria | Descripció | Prioritat |
|-----------|------------|-----------|
| ✅ **Control d'Hores** | Marcatge d'entrada i sortida per empleat | MÀXIMA |
| ✅ **Assignació per Projectes** | Registre de temps dedicat a cada projecte concret | MÀXIMA |
| ✅ **Vista en Temps Real** | Visualització immediata de qui està treballant i en què | MÀXIMA |
| ✅ **Llista Vermella d'Incompliments** | Detecció automàtica d'arribades tardanes, sortides anticipades i hores insuficients | MÀXIMA |
| ✅ **Alertes i Resum** | Notificacions automàtiques sense necessitat de cerca manual | ALTA |
| ✅ **Reports de Projectes** | Cost total en hores per projecte, comparació pressupostat vs real | ALTA |
| ✅ **Gràfics Visuals** | Dashboard amb gràfics interactius i fàcil de llegir | ALTA |
| ✅ **Facilitat d'Ús** | Un sol clic per marcar, sense formularis complexos | MÀXIMA |
| ✅ **Compatibilitat Mòbil** | Panell d'administrador accessible des de mòbil | MÀXIMA |

---

## 🏗️ Arquitectura del Projecte

```
/
├── 📁 config/          # Configuracions (base de dades, entorn)
├── 📁 public/          # Arxius accessibles públicament
│   ├── index.php
│   ├── login.php
│   ├── dashboard.php
│   └── assets/
├── 📁 src/             # Codi font PHP
│   ├── models/
│   ├── controllers/
│   └── views/
├── 📁 database/        # Scripts SQL i migracions
├── 📁 docs/            # Documentació tècnica
└── README.md
```

---

## 📊 Disseny de Base de Dades

### Entitats Principals:
1. **Usuaris** (empleats i administradors)
2. **Projectes**
3. **Registres de Temps**
4. **Sessions de Treball**
5. **Alertes i Incompliments**
6. **Logs d'Activitat**

---

## 🚀 Pla d'Implementació

1. ✅ **Anàlisi de requisits i documentació**
2. 🔄 **Estructura del projecte i fitxers base**
3. Disseny i creació de la base de dades MySQL
4. Sistema d'autenticació i gestió de rols
5. Mòdul de marcatge d'hores
6. Assignació de temps a projectes
7. Motor de detecció d'incompliments
8. Panell d'administració i dashboard
9. Generació de reports i gràfics
10. Disseny responsive i optimització per mòbil
11. Proves i validacions
12. Desplegament en servidor

---

## ⚙️ Característiques Tècniques

- **Backend**: PHP 8.1+ (POO, sense dependències externes per màxima velocitat)
- **Base de Dades**: MySQL 8.0
- **Frontend**: HTML5 + CSS3 + Vanilla JS (sense frameworks pesats)
- **Gràfics**: Chart.js
- **Seguretat**: Password Hash, CSRF Protection, SQL Injection Prevention
- **Rendiment**: Optimitzat per 400 usuaris simultanis

---

> Documentació actualitzada pas a pas durant el desenvolupament.