CREATE DATABASE visit_counter;
USE visit_counter;
CREATE TABLE posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    visits SMALLINT NOT NULL DEFAULT 0
);

INSERT INTO posts VALUES (
    1,
    'HELLO WORLD',
    'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ut nesciunt exercitationem eius nobis. Est in magnam velit laborum fugit alias pariatur quo eius tempora quisquam, facere tempore reiciendis distinctio culpa!',
    0
);
