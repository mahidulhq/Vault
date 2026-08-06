CREATE DATABASE `vault_db`;
USE `vault_db`;

CREATE TABLE `Admins` (
    `Admin_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Username` VARCHAR(50) NOT NULL UNIQUE,
    `Email` VARCHAR(100) NOT NULL UNIQUE,
    `Password` VARCHAR(255) NOT NULL
);

CREATE TABLE `Users` (
    `User_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Username` VARCHAR(50) NOT NULL UNIQUE,
    `Email` VARCHAR(100) NOT NULL UNIQUE,
    `Password` VARCHAR(255) NOT NULL
);

CREATE TABLE `Genres` (
    `Genre_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Genre_Name` VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE `Platforms` (
    `Platform_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Platform_Name` VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE `AgeRatings` (
    `AgeRating_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Rating_Name` VARCHAR(20) NOT NULL UNIQUE,
    `Description` TEXT
);

CREATE TABLE Games (
    Game_ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(150) NOT NULL,
    Genre_ID INT,
    Platform_ID INT,
    AgeRating_ID INT,
    Release_Year INT,
    Description TEXT,
    Cover_Image VARCHAR(255) DEFAULT 'default_cover.jpg',
    FOREIGN KEY (Genre_ID) REFERENCES Genres(Genre_ID),
    FOREIGN KEY (Platform_ID) REFERENCES Platforms(Platform_ID),
    FOREIGN KEY (AgeRating_ID) REFERENCES AgeRatings(AgeRating_ID)
);

CREATE TABLE Reviews (
    Review_ID INT AUTO_INCREMENT PRIMARY KEY,
    Game_ID INT NOT NULL,
    User_ID INT NOT NULL,
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),
    Review_Text TEXT,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Game_ID) REFERENCES Games(Game_ID),
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID)
);

CREATE TABLE Favorites (
    Favorite_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID INT NOT NULL,
    Game_ID INT NOT NULL,
    Date_Added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID),
    FOREIGN KEY (Game_ID) REFERENCES Games(Game_ID),
    UNIQUE (User_ID, Game_ID)
);
