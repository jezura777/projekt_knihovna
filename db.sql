-- CREATE DATABASE knihovnadivnice;
-- USE knihovnadivnice;

CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    born DATE,
    died DATE
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    published DATE,
    author_id INT NOT NULL,
    copies INT DEFAULT 1,

    FOREIGN KEY(author_id) REFERENCES authors(id)
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    born DATE,
    email VARCHAR(255) UNIQUE NOT NULL
);


CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    due_on DATE,
    returned_on DATETIME,

    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(book_id) REFERENCES book(id)
);
