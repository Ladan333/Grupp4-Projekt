CREATE DATABASE IF NOT EXISTS BLOGG;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(32) NOT NULL,
    last_name VARCHAR(32) NOT NULL,
    user_name VARCHAR(32) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    email VARCHAR(64) NOT NULL UNIQUE,
    role BOOLEAN DEFAULT FALSE,
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

CREATE TABLE IF NOT EXISTS follows (
    user_id INT NOT NULL,
    follow_id INT NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(follow_id) REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY(user_id, follow_id) 

);

CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id), ON DELETE CASCADE
    FOREIGN KEY (post_id) REFERENCES blogposts(id) ON DELETE CASCADE

);

CREATE TABLE IF NOT EXISTS dms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unread_status BOOLEAN DEFAULT TRUE,
    message_content TEXT,
    message_image MEDIUMTEXT,
    CreatedDate DATETIME NOT NULL DEFAULT NOW(),
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE
);

    SELECT dms.*, 
           user1.username AS user1_name, 
           user2.username AS user2_name
    FROM dms
    JOIN users user1 ON user1.id = dms.user1_id
    JOIN users user2 ON user2.id = dms.user2_id
    WHERE (dms.user1_id = :user_id AND dms.user2_id = :reciever)
       OR (dms.user1_id = :reciever AND dms.user2_id = :user_id)
    ORDER BY dms.id ASC
    LIMIT 25
;

ALTER TABLE blogposts
ADD COLUMN image_base64 MEDIUMTEXT;

ALTER TABLE users
ADD COLUMN profile_image MEDIUMTEXT;

--EXEMPELDATA ATT JOBBA MED

--THIS USERS DATA USES HASHED PASSWORDS AND WORKS WITH THE UPDATED DATABASE
--PASSWORDS ARE 111, 222, 333 ETC
INSERT INTO users
(first_name, last_name, user_name, pwd, email, role, profileContent)
VALUES
('Wednes', 'day', 'Wednesday333', '$2y$10$SyZ3LVFIGOnPPYn7AChFe.ZAQAK/x1HSBCpr/xHeFliB4S6WMCol.', 'Wednesday@gmail.com',0,'I love writing about tech.'),
('Thurs', 'day', 'Thursday444', '$2y$10$h85j9upNURTexX9jnR26guwm8Ne6HtrB6rDhOehZSwCIREhvSpTKm', 'Thursday@gmail.com',1, 'Web developer and blogger.'),
('Fri', 'day', 'Friday555', '$2y$10$LC7XrgBvZkcsZgt6DJjy1OrS9b469SJohUfC6RQrrj2f9R8ZOjz0m', 'Friday@gmail.com',0,'Web developer and blogger.'),
('Satur', 'day', 'Saturday666', '$2y$10$7fipSxM0P5yMuOw6npSP4O0xRjJ5dFLFARio.U5iP0uSr4ZD74ewm', 'Saturday@gmail.com',1,'Web developer and blogger.'),
('Sun', 'day', 'Sunday777', '$2y$10$NmJxShnIatP8rQH5JBDHD.LjnPlPnKIC5LkdMRSgvmV9aa.Hp7j..', 'Sunday@gmail.com',0,'Web developer and blogger.');



INSERT INTO blogposts (title, blogContent, user_id)
VALUES
    ('The Future of AI', 'Artificial Intelligence is growing rapidly...', 1),
    ('Top 10 Web Development Tips', 'Web development is evolving every day...', 2),
    ('Why Writing is a Great Hobby', 'Writing helps to express your thoughts...', 3),
    ('Exploring Italy: A Travel Guide', 'Italy is a beautiful country with rich history...', 4),
    ('Understanding Cybersecurity', 'Cybersecurity is crucial in today’s world...', 5);

INSERT INTO comments (commentContent, user_id, blog_id)
VALUES
    ('Great article on AI!', 2, 1),
    ('Thanks for the tips, very useful.', 3, 2),
    ('I love writing too! Thanks for sharing.', 4, 3),
    ('Italy is my dream destination!', 5, 4),
    ('Cybersecurity is becoming more important every day.', 1, 5);

INSERT INTO follows (user_id, follow_id)
VALUES
    (1, 2),  -- Alice följer Bob
    (2, 3),  -- Bob följer Charlie
    (3, 4),  -- Charlie följer David
    (4, 5),  -- David följer Eva
    (5, 1);  -- Eva följer Alice

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
/* Databas för likes
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES blogposts(id) ON DELETE CASCADE,
    UNIQUE(user_id, post_id) -- En användare kan bara gilla ett inlägg en gång
);


