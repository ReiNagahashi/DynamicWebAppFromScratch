CREATE TABLE IF NOT EXISTS users(
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR(20),
    email VARCHAR(20),
    password VARCHAR(20),
    created_at DATETIME,
    updated_at DATETIME,
    subscription VARCHAR(20),
    subscription_status VARCHAR(10),
    subscription_created_at DATETIME,
    subscription_end_at DATETIME
);

CREATE TABLE IF NOT EXISTS posts(
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(30),
    content TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    user_id INT,
    FOREIGN KEY user_fk(user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS comments(
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    comment_text TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    user_id INT,
    post_id INT,
    FOREIGN KEY user_fk(user_id) REFERENCES users(user_id),
    FOREIGN KEY post_fk(post_id) REFERENCES posts(post_id)

);


CREATE TABLE IF NOT EXISTS post_likes(
    user_id INT,
    post_id INT,
    FOREIGN KEY user_fk(user_id) REFERENCES users(user_id),
    FOREIGN KEY post_fk(post_id) REFERENCES posts(post_id),
    PRIMARY KEY (user_id, post_id)
);


CREATE TABLE IF NOT EXISTS comment_likes(
    user_id INT,
    comment_id INT,
    FOREIGN KEY user_fk(user_id) REFERENCES users(user_id),
    FOREIGN KEY comment_fk(comment_id) REFERENCES comments(comment_id),
    PRIMARY KEY (user_id, comment_id)
);

CREATE TABLE IF NOT EXISTS user_settings(
    entry_id INT PRIMARY KEY,
    user_id INT,
    meta_key VARCHAR(20),
    meta_value VARCHAR(20),
    FOREIGN KEY user_fk(user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS tags(
    tag_id INT PRIMARY KEY,
    tag_name VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS post_tags(
    post_id INT,
    tag_id INT,
    FOREIGN KEY post_fk(post_id) REFERENCES posts(post_id),
    FOREIGN KEY tag_fk(tag_id) REFERENCES tags(tag_id),
    PRIMARY KEY (post_id, tag_id)
);

CREATE TABLE IF NOT EXISTS categories(
    category_id INT PRIMARY KEY,
    category_name VARCHAR(20)
);

-- ALTER TABLE posts
-- ADD 
--     category_id INT;

-- ALTER TABLE posts
-- ADD CONSTRAINT category_fk
-- FOREIGN KEY (category_id) REFERENCES categories(category_id);

