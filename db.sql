-- DATABASE STRUCTUUR VOOR RECEPTIFY
-- Gemaakt voor Tom – volledig clean & veilig

-- ============================
-- USERS TABEL
-- ============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- RECEPTEN TABEL
-- ============================
CREATE TABLE recepten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    beschrijving TEXT NOT NULL,
    ingredienten TEXT NOT NULL,
    bereiding TEXT NOT NULL,
    afbeelding VARCHAR(255) DEFAULT NULL,
    likes INT DEFAULT 0,
    specialiteit TINYINT(1) DEFAULT 0,
    user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_recept_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL
);

-- ============================
-- FAVORIETEN TABEL
-- ============================
CREATE TABLE favorieten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recept_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fav_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,

    CONSTRAINT fk_fav_recept FOREIGN KEY (recept_id)
        REFERENCES recepten(id) ON DELETE CASCADE
);

-- INDEXES VOOR SNELLERE LIKE/FAVORIET CHECKS
CREATE INDEX idx_fav_user ON favorieten(user_id);
CREATE INDEX idx_fav_recept ON favorieten(recept_id);