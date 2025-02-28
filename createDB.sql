CREATE DATABASE IF NOT EXISTS BLOGG;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(32) NOT NULL,
    user_name VARCHAR(32) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    email VARCHAR(64) NOT NULL UNIQUE,
    `role` BOOLEAN DEFAULT FALSE,
    profileContent TEXT,
    CreatedDate DATETIME NOT NULL DEFAULT NOW()

);

CREATE TABLE IF NOT EXISTS blogposts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    blogContent TEXT NOT NULL,
    user_id INT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    CreatedDate DATETIME NOT NULL DEFAULT NOW()

);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commentContent TEXT NOT NULL,
    user_id INT,
    blog_id INT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(blog_id) REFERENCES blogposts(id) ON DELETE CASCADE,
    CreatedDate DATETIME NOT NULL DEFAULT NOW()
    
);

CREATE TABLE IF NOT EXISTS friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(friend_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE(user_id, friend_id), /*tillåter inte 1, 1 och en till 1, 1  (men 1, 1 tillåts)*/
    CONSTRAINT chk_not_self CHECK (user_id <> friend_id)/*1, 1 tillåts ej -- duplicering 1, 2 och 2, 1 tillåts -- tillsammans hindrar de 1, 2 och 2, 1 att finnas*/


);

/*-------------------Menar vi så här i stället?------user i f2u lagrar en referens till en tabell med vänner?-----------*/
/*den fungerar inte... kopplat FK i mellanliggande till PK i friendsfriends  -- måste Länka båda befintliga FK till user_id och Skapa en tredje FK som kopplar till PK i friendsfriend*/

-- CREATE DATABASE IF NOT EXISTS BLOGG;

-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(32) NOT NULL,
--     user_name VARCHAR(32) NOT NULL UNIQUE,
--     pwd VARCHAR(255) NOT NULL,
--     email VARCHAR(64) NOT NULL UNIQUE,
--     `role` BOOLEAN DEFAULT FALSE,
--     profile_text TEXT,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()

-- );

-- CREATE TABLE IF NOT EXISTS blogposts (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(255) NOT NULL,
--     blogContent TEXT NOT NULL,
--     user_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()

-- );

-- CREATE TABLE IF NOT EXISTS comments (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     commentContent TEXT NOT NULL,
--     user_id INT,
--     blog_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY(blog_id) REFERENCES blogposts(id) ON DELETE CASCADE,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()
-- );



-- CREATE TABLE IF NOT EXISTS friendsfriends (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     all_friends_id INT NOT NULL



    
-- );

-- CREATE TABLE IF NOT EXISTS friends (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     friend_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY(friend_id) REFERENCES friendfriends(id) ON DELETE CASCADE,
--     UNIQUE(user_id, friend_id)
-- );

-- /*---------måste Länka båda befintliga FK till user_id och Skapa en tredje FK som kopplar till PK i friendsfriend*/
-- /*så här typ */

-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(32) NOT NULL,
--     user_name VARCHAR(32) NOT NULL UNIQUE,
--     pwd VARCHAR(255) NOT NULL,
--     email VARCHAR(64) NOT NULL UNIQUE,
--     `role` BOOLEAN DEFAULT FALSE,
--     profile_text TEXT,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()

-- );

-- CREATE TABLE IF NOT EXISTS blogposts (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     title VARCHAR(255) NOT NULL,
--     blogContent TEXT NOT NULL,
--     user_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()

-- );

-- CREATE TABLE IF NOT EXISTS comments (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     commentContent TEXT NOT NULL,
--     user_id INT,
--     blog_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY(blog_id) REFERENCES blogposts(id) ON DELETE CASCADE,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW(),
    
-- );

-- CREATE TABLE IF NOT EXISTS f2u (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     friend_id INT,
--     FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY(friend_id) REFERENCES users(id) ON DELETE CASCADE,

--     FOREIGN KEY(friend_relation) REFERENCES friends_relation(id) ON DELETE CASCADE, /*DENNA la Jag till*/

    
--     UNIQUE(user_id, friend_id) /*tillåter inte 1, 1 och en till 1, 1  (men 1, 1 tillåts)*/
--     CONSTRAINT chk_not_self CHECK (user_id <> friend_id)/*1, 1 tillåts ej -- duplicering 1, 2 och 2, 1 tillåts -- tillsammans hindrar de 1, 2 och 2, 1 att finnas*/

    
-- );

-- CREATE TABLE IF NOT EXISTS friends_relation (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     relation VARCHAR(50);
    
-- );


-- /*Annars kan man spara i en json direkt i user, verkar krångligt att skriva till...*/

-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     `name` VARCHAR(32) NOT NULL,
--     user_name VARCHAR(32) NOT NULL UNIQUE,
--     pwd VARCHAR(255) NOT NULL,
--     email VARCHAR(64) NOT NULL UNIQUE,
--     `role` BOOLEAN DEFAULT FALSE,
--     profile_text TEXT,
--     CreatedDate DATETIME NOT NULL DEFAULT NOW()

--     friends JSON,  /*spara friends här*/

-- );


