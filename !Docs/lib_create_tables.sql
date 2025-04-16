USE library;


DROP TABLE IF EXISTS book_movements;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS db_users;


CREATE TABLE db_users (
  id INT NOT NULL AUTO_INCREMENT UNIQUE,
  username VARCHAR(20) NOT NULL UNIQUE,
  password VARCHAR(200) NOT NULL,
  is_admin BOOLEAN DEFAULT false,
  email VARCHAR(50),
  dob DATE,
  PRIMARY KEY(id)
);


CREATE TABLE books (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  genre VARCHAR(50) NOT NULL,
  author VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  max_days INT NOT NULL DEFAULT 14,
  PRIMARY KEY(id)
);


CREATE TABLE book_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  user_id INT,
  quantity INT NOT NULL,
  movement_date DATE NOT NULL DEFAULT (CURRENT_DATE),

  FOREIGN KEY (book_id) REFERENCES books(id) ON UPDATE NO ACTION ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES db_users(id) ON UPDATE NO ACTION ON DELETE CASCADE
);


