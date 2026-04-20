# 📋 Document de Disseny i Planificació del Projecte Web
## Versió 1.0 | 20/04/2026

---

## 🎯 Objectiu General
Aplicació web de gestió de recursos amb arquitectura MVC, desenvolupada en PHP, per a gestió interna d'empresa o organització.

---

## ✅ Llista de Funcionalitats de l'Aplicació

### Funcionalitats per a Usuaris Registrats:
- [x] Iniciar sessió i tancar sessió de forma segura
- [x] Veure el tauler principal (dashboard) amb informació personalitzada
- [x] Visualitzar el seu perfil d'usuari i modificar les seves dades
- [x] Canviar la contrasenya del compte
- [x] Visualitzar llistat de recursos disponibles
- [x] Filtrar i buscar entre els elements del sistema
- [x] Visualitzar detalls complets de cada element
- [x] Enviar sol·licituds o consultes
- [x] Veure l'historial de les seves accions i operacions realitzades
- [x] Rebre notificacions del sistema

### Funcionalitats per a Gestors:
- [x] Totes les funcionalitats d'usuari registrat
- [x] Crear nous elements i registres al sistema
- [x] Modificar dades d'elements existents
- [x] Eliminar registres (marcar com a eliminats)
- [x] Aprovar o rebutjar sol·licituds d'usuaris
- [x] Veure llistat complet d'usuaris del seu àmbit
- [x] Generar informes bàsics
- [x] Assignar tasques i recursos

### Funcionalitats per a Administradors:
- [x] Totes les funcionalitats de gestor
- [x] Gestionar completament tots els usuaris del sistema
- [x] Assignar i modificar rols dels usuaris
- [x] Accés a la configuració general de l'aplicació
- [x] Veure logs i registres d'activitat completa del sistema
- [x] Fer còpies de seguretat de la base de dades
- [x] Gestionar categories i paràmetres generals
- [x] Bloquejar i activar comptes d'usuari

### Funcionalitats Públiques (Sense inici de sessió):
- [x] Pàgina d'inici pública
- [x] Formulari de contacte
- [x] Informació general de l'organització
- [x] Pàgina d'inici de sessió
- [x] Recuperació de contrasenya per correu electrònic

---

## 📊 Dades que utilitzarà l'Aplicació

### Dades introduïdes pels usuaris:
| Tipus de dada | Descripció |
|---|---|
| Dades de compte | Correu electrònic, nom complet, nom d'usuari, contrasenya, foto de perfil |
| Dades de perfil | Telèfon, càrrec, departament, ubicació, dades addicionals |
| Contingut creat | Títols, descripcions, dates, categories, fitxers adjunts |
| Sol·licituds | Motiu, descripció detallada, data sol·licitada, observacions |
| Comentaris | Text, fitxers adjunts |
| Preferències | Configuració personal, tema visual, notificacions |

### Dades generades pel sistema:
- Data i hora de creació i modificació de tots els registres
- Registre d'accés i activitat dels usuaris
- Estat dels elements (actiu, pendent, aprovat, eliminat)
- Logs d'operacions realitzades
- Dades de sessió i tokens de seguretat
- Relacions entre els diferents elements de la base de dades

---

## 👥 Rols i Permisos de l'Aplicació

| Rol | Nivell d'accés | Descripció |
|---|---|---|
| **Convidat** | 0 | Usuari no registrat, només accés a zones públiques |
| **Usuari** | 1 | Usuari registrat bàsic, pot veure i sol·licitar |
| **Gestor** | 2 | Responsable de secció, pot gestionar contingut |
| **Administrador** | 3 | Control total del sistema, tots els permisos |

> ✅ Els rols són jeràrquics: cada rol inclou tots els permisos dels rols amb nivell inferior.

---

## 🖥️ Estructura de Pantalles i Navegació

### Mapa de Navegació Esquemàtic:
```
🔹 PÀGINES PÚBLIQUES
├─ /                  Pàgina d'inici
├─ /login             Inici de sessió
├─ /recuperar         Recuperació de contrasenya
└─ /contacte          Formulari de contacte

🔹 ZONA PRIVADA (després de login)
├─ /dashboard         Tauler principal / Home
│  ├─ Estadístiques personals
│  ├─ Notificacions recents
│  └─ Accions ràpides
│
├─ /perfil
│  ├─ Veure perfil
│  └─ Editar dades personals
│
├─ /recursos
│  ├─ Llistat general
│  ├─ Filtres i buscador
│  ├─ Vista detall per element
│  ├─ Formulari de creació (gestor+)
│  └─ Formulari d'edició (gestor+)
│
├─ /usuaris
│  ├─ Llistat d'usuaris (gestor+)
│  ├─ Perfil d'usuari
│  └─ Gestionar rols (admin)
│
├─ /informes
│  └─ Generació i descàrrega d'informes
│
└─ /administracio
   ├─ Configuració general
   ├─ Logs del sistema
   ├─ Còpies de seguretat
   └─ Gestió de categories
```

### Flux d'Usuari Estàndard:
```
Usuari accedeix -> Pàgina Inici -> Botó Iniciar Sessió ->
-> Introdueix credencials -> Dashboard -> Accedeix a les seccions
```

---

## 📁 Estructura Final del Projecte

```
/
├─ public/              Arxius accessibles públicament
│  ├─ index.php         Punt d'entrada principal
│  ├─ assets/
│  │  ├─ css/
│  │  ├─ js/
│  │  └─ img/
│  └─ uploads/          Fitxers pujats per usuaris
│
├─ src/                 Codi font de l'aplicació
│  ├─ controllers/      Controladors MVC
│  ├─ models/           Models de dades
│  └─ views/            Plantilles de visualització
│
├─ config/              Fitxers de configuració
│  └─ database.php      Connexió amb base de dades
│
├─ database/
│  └─ schema.sql        Estructura completa de la BD
│
└─ docs/                Documentació del projecte
```

---

## ✔️ Següents passos
1. Definir i crear l'esquema complet de la base de dades
2. Implementar el sistema d'autenticació i sessions
3. Crear l'estructura base de controladors i models
4. Implementar el sistema de rols i permisos
5. Desenvolupar cada una de les pantalles enumerades