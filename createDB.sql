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

CREATE TABLE IF NOT EXISTS follows (
    user_id INT NOT NULL,
    follow_id INT NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(follow_id) REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY(user_id, follow_id)

);

--EXEMPELDATA ATT JOBBA MED
INSERT INTO users (`name`, user_name, pwd, email, `role`, profileContent)
VALUES
    ('Alice Johnson', 'alicej', 'password123', 'alice@example.com', FALSE, 'Loves writing tech blogs.'),
    ('Bob Smith', 'bobsmith', 'securepass', 'bob@example.com', TRUE, 'Web developer and blogger.'),
    ('Charlie Brown', 'charlieb', 'mypassword', 'charlie@example.com', FALSE, 'Aspiring writer.'),
    ('David Miller', 'davidm', '123456', 'david@example.com', FALSE, 'Writes about travel and food.'),
    ('Eva Green', 'evag', 'eva1234', 'eva@example.com', TRUE, 'Tech enthusiast and editor.');


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


