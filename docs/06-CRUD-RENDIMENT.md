# 6 - Operacions CRUD i Rendiment
## Documentació interacció amb base de dades

---

### ✅ Sentència DDL (Data Definition Language)
Exemple de sentència CREATE extreta de `database/schema.sql`:
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dni VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(80) NOT NULL,
    cognoms VARCHAR(120) NOT NULL,
    departament VARCHAR(60) NULL,
    horari_entrada TIME DEFAULT '08:00:00',
    horari_sortida TIME DEFAULT '16:00:00',
    hores_diaries_requerides DECIMAL(3,1) DEFAULT 8.0,
    rol ENUM('empleat', 'administrador', 'superadmin') DEFAULT 'empleat',
    actiu BOOLEAN DEFAULT TRUE,
    data_creacio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_connexio TIMESTAMP NULL
) ENGINE=InnoDB;
```

**Mètode PDO utilitzat:** `exec()`
> Per a sentències DDL (CREATE, ALTER, DROP, TRUNCATE) s'utilitza el mètode `PDO::exec()` ja que aquestes operacions no retornen cap conjunt de resultats, només el nombre de files afectades. Aquest mètode executa la sentència directament sense preparació prèvia.

---

### ✅ Sentència DML (Data Manipulation Language)
Exemple de sentència UPDATE:
```sql
UPDATE users 
SET ultima_connexio = NOW() 
WHERE id = :user_id
```

**Mètode PDO utilitzat:** `execute()`
> Per a sentències DML (UPDATE, DELETE, INSERT) que utilitzen paràmetres s'utilitza sempre `PDOStatement::execute()` després de preparar la consulta amb `prepare()`. Això permet utilitzar consultes preparades, evitar injeccions SQL i optimitzar l'execució repetida de la mateixa sentència.

---

### ✅ Consulta SELECT amb fetchAll()
Exemple extret de la funció `dbGetAll()` a `config/database.php` línia 73:
```php
function dbGetAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}
```

Exemple d'ús amb columnes específiques:
```php
// ✅ BONA PRÀCTICA: Demanar només les columnes necessàries
$empleats = dbGetAll("SELECT id, nom, cognoms, email FROM users WHERE actiu = 1");
```

---

### ⚡ Per què és més eficient especificar columnes en lloc de usar `SELECT *`

| Motiu | Explicació |
|-------|------------|
| **Menys dades transferides** | No s'envien per la xarxa camps que no necessites (TEXT, BLOB, camps auditories). Es redueix la latència i el consum de memòria tant al servidor BD com a l'aplicació PHP. |
| **Millor ús d'índexs** | Si totes les columnes de la consulta estan contingudes en un índex, MySQL pot realitzar una **"Index Only Scan"** sense necessitat d'accedir a les dades de la taula, multiplicant per 10-100 la velocitat. |
| **Optimitzador de consultes** | L'optimitzador MySQL pot triar millors plans d'execució quan coneix exactament quins camps necessites. |
| **Memòria PHP** | Cada fila recuperada s'emmagatzema en memòria RAM de PHP. Amb menys columnes pots processar més registres sense sobrecarregar el servidor ni excedir límits de memòria. |
| **Mantenibilitat** | Si s'afegeixen noves columnes a la taula la consulta no es trenca i no rebràs dades imprevistes. |
| **Cache de consultes** | Les consultes amb columnes explícites tenen molta més probabilitat de reutilitzar la memòria cau de MySQL. |

> 📊 **Impacte de rendiment**: En taules amb molts registres i camps grans, la diferència de temps entre `SELECT *` i `SELECT col1,col2` pot ser de més de 10 vegades més ràpid.

---

### 📋 Resum mètodes PDO
| Mètode | Ús recomanat | Tipus de sentències |
|--------|--------------|---------------------|
| `PDO::exec()` | Sentències que no retornen resultats | DDL (CREATE, ALTER, DROP) |
| `PDOStatement::execute()` | Consultes preparades amb paràmetres | DML (INSERT, UPDATE, DELETE) i SELECT |
| `fetchAll()` | Recuperar múltiples registres | Resultats de SELECT |