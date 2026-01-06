/* -----------------------------------------------------------
   1) CREATE DATABASE (only if it does not already exist)
------------------------------------------------------------ */
CREATE DATABASE IF NOT EXISTS child_management;

/* Select the database so all commands run inside it */
USE child_management;


/* -----------------------------------------------------------
   2) CREATE TABLES (only if they do not already exist)
------------------------------------------------------------ */


/* Table to store child personal details */
CREATE TABLE children (
    child_id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(100),
    dob DATE,
    age_group VARCHAR(10),
    gender VARCHAR(10),
    center VARCHAR(50),
    child_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE child_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT,
    domain VARCHAR(20),
    question TEXT,
    answer ENUM('yes','no'),
    FOREIGN KEY (child_id) REFERENCES children(child_id)
);





SELECT 
COUNT(*) AS total,
SUM(answer='yes') AS completed
FROM child_milestones
WHERE child_id = ?
