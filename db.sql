-- ---------------------------------------------------------
-- DATABASE STRUCTUUR VOOR RECEPTIFY
-- ---------------------------------------------------------

-- USERS (registratie + login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TAGS (hoofdcategorie)
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(255) NOT NULL UNIQUE
);

-- SPECIALITEITEN
CREATE TABLE IF NOT EXISTS specialiteiten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(255) NOT NULL UNIQUE
);

-- SUBTAGS (ingrediënten)
CREATE TABLE IF NOT EXISTS subtags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(255) NOT NULL UNIQUE
);

-- RECEPTEN
CREATE TABLE IF NOT EXISTS recepten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titel VARCHAR(255) NOT NULL,
    beschrijving TEXT,
    ingredienten TEXT,
    bereiding TEXT,
    tijd INT DEFAULT 0,
    tools VARCHAR(255),
    personen INT DEFAULT 1,
    tag_id INT DEFAULT NULL,
    likes INT DEFAULT 0,
    afbeelding VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- KOPPELTABEL: RECEPT → SPECIALITEITEN
CREATE TABLE IF NOT EXISTS recept_specialiteiten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recept_id INT NOT NULL,
    specialiteit_id INT NOT NULL,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE,
    FOREIGN KEY (specialiteit_id) REFERENCES specialiteiten(id) ON DELETE CASCADE
);

-- KOPPELTABEL: RECEPT → SUBTAGS (ingrediënten)
CREATE TABLE IF NOT EXISTS recept_subtags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recept_id INT NOT NULL,
    subtag_id INT NOT NULL,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE,
    FOREIGN KEY (subtag_id) REFERENCES subtags(id) ON DELETE CASCADE
);

-- KOPPELTABEL: RECEPT → TAGS (voor tag.php filter)
CREATE TABLE IF NOT EXISTS recept_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recept_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- LIKES
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recept_id INT NOT NULL,
    UNIQUE KEY unique_like (user_id, recept_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE
);

-- FAVORIETEN
CREATE TABLE IF NOT EXISTS favorieten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recept_id INT NOT NULL,
    UNIQUE KEY unique_fav (user_id, recept_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE
);

-- RAPPORTEN (gebruikt door admin_panel.php)
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recept_id INT NOT NULL,
    reason VARCHAR(255) DEFAULT 'Automatisch rapport',
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recept_id) REFERENCES recepten(id) ON DELETE CASCADE
);
