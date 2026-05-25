-- CREATE DATABASE knihovnadivnice;
-- USE knihovnadivnice;

CREATE TABLE IF NOT EXISTS autori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jmeno VARCHAR(255) NOT NULL,
    datum_narozeni DATE,
    datum_umrti DATE
);

CREATE TABLE IF NOT EXISTS knihy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazev VARCHAR(100),
    datum_vydani DATE,
    autor_id INT,
    pocet INT DEFAULT 1,

    FOREIGN KEY(autor_id) REFERENCES autori(id)
);

CREATE TABLE IF NOT EXISTS uzivatele (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jmeno VARCHAR(255) NOT NULL,
    datum_narozeni DATE,
    email VARCHAR(255) UNIQUE
);


CREATE TABLE IF NOT EXISTS vypujcky (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uzivatel_id INT NOT NULL,
    knizka_id INT NOT NULL,

    datum_zpracovani DATETIME,
    datum_vypujceni DATE,
    datum_vraceni DATE,

    FOREIGN KEY(uzivatel_id) REFERENCES uzivatele(id),
    FOREIGN KEY(knizka_id) REFERENCES knihy(id)
);
