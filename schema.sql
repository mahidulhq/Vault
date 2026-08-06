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

INSERT INTO `Genres` (`Genre_ID`, `Genre_Name`) VALUES 
(1, 'Action'), 
(2, 'RPG'), 
(3, 'Adventure'), 
(4, 'Strategy'), 
(5, 'Sports');

INSERT INTO `Platforms` (`Platform_ID`, `Platform_Name`) VALUES 
(1, 'PC'), 
(2, 'PlayStation 5'), 
(3, 'Xbox Series X'), 
(4, 'Nintendo Switch');

INSERT INTO `AgeRatings` (`AgeRating_ID`, `Rating_Name`, `Description`) VALUES 
(1, 'E', 'Everyone - Content is generally suitable for all ages.'),
(2, 'T', 'Teen - Content is generally suitable for ages 13 and up.'),
(3, 'M', 'Mature 17+ - Content is generally suitable for ages 17 and up.');

INSERT INTO `Games` (`Title`, `Genre_ID`, `Platform_ID`, `AgeRating_ID`, `Release_Year`, `Description`, `Cover_Image`) VALUES
('Elden Ring: Shadow of the Erdtree', 2, 1, 3, 2024, 'An action RPG set in the Lands Between featuring challenging boss encounters.', 'default_cover.jpg'),
('Cyberpunk 2077: Phantom Liberty', 2, 1, 3, 2023, 'An open-world action-adventure story set in Night City.', 'default_cover.jpg'),
('Grand Theft Auto V', 1, 2, 3, 2013, 'A sprawling open-world crime drama set in Los Santos.', 'default_cover.jpg'),
('God of War Ragnarok', 1, 2, 3, 2022, 'Embark on an epic journey through the Nine Realms with Kratos and Atreus.', 'default_cover.jpg'),
('Red Dead Redemption 2', 3, 1, 3, 2018, 'An epic tale of life in Americas unforgiving heartland.', 'default_cover.jpg'),
('Baldurs Gate 3', 2, 1, 3, 2023, 'A story-rich, party-based RPG set in the universe of Dungeons & Dragons.', 'default_cover.jpg'),
('Starfield', 2, 3, 3, 2023, 'A next-generation role-playing game set amongst the stars.', 'default_cover.jpg'),
('Super Mario Bros. Wonder', 3, 4, 1, 2023, 'Classic Mario side-scrolling gameplay transformed by Wonder Flowers.', 'default_cover.jpg'),
('Halo Infinite', 1, 3, 2, 2021, 'The legendary Master Chief returns in an expansive open-world campaign.', 'default_cover.jpg'),
('Civilization VI', 4, 1, 1, 2016, 'Build an empire to stand the test of time in this turn-based strategy game.', 'default_cover.jpg'),
('Forza Horizon 5', 5, 3, 1, 2021, 'Explore the vibrant open world landscapes of Mexico in top cars.', 'default_cover.jpg'),
('The Witcher 3: Wild Hunt', 2, 1, 3, 2015, 'A story-driven open world RPG set in a dark fantasy universe.', 'default_cover.jpg'),
('Spiderman 2', 1, 2, 2, 2023, 'Peter Parker and Miles Morales battle classic villains in NYC.', 'default_cover.jpg'),
('Final Fantasy XVI', 2, 2, 3, 2023, 'An epic dark fantasy world where fate is decided by powerful Eikons.', 'default_cover.jpg'),
('Metroid Dread', 3, 4, 2, 2021, 'Join Samus Aran in a fierce fight against alien threats on Planet ZDR.', 'default_cover.jpg'),
('Persona 5 Royal', 2, 4, 2, 2019, 'Wear the mask of Joker and join the Phantom Thieves of Hearts.', 'default_cover.jpg'),
('Street Fighter 6', 1, 2, 2, 2023, 'The next evolution of the Street Fighter series featuring new modes.', 'default_cover.jpg'),
('Starcraft II', 4, 1, 2, 2010, 'A premier real-time strategy game featuring Terran, Zerg, and Protoss.', 'default_cover.jpg'),
('Monster Hunter: World', 1, 1, 2, 2018, 'Battle gigantic monsters in epic landscapes using unique weaponry.', 'default_cover.jpg'),
('Resident Evil 4 Remake', 1, 2, 3, 2023, 'Leon S. Kennedy searches for the President daughter in a remote village.', 'default_cover.jpg'),
('Hades', 1, 4, 2, 2020, 'Defy the god of the dead as you hack and slash out of the Underworld.', 'default_cover.jpg'),
('Hollow Knight', 3, 4, 1, 2017, 'Forge your path in an interconnected insect world full of mystery.', 'default_cover.jpg'),
('Diablo IV', 2, 1, 3, 2023, 'Slaughter countless demons and master powerful skills in Sanctuary.', 'default_cover.jpg'),
('Age of Empires IV', 4, 1, 2, 2021, 'Command armies in historical battles that shaped the world.', 'default_cover.jpg'),
('Madden NFL 24', 5, 3, 1, 2023, 'Lead your NFL franchise to glory with updated player AI.', 'default_cover.jpg'),
('Gran Turismo 7', 5, 2, 1, 2022, 'The ultimate driving simulator brings together classic racing modes.', 'default_cover.jpg'),
('Dark Souls III', 2, 1, 3, 2016, 'A dark fantasy adventure in a ruined world full of grueling bosses.', 'default_cover.jpg'),
('Seasons of Conquest', 4, 1, 1, 2022, 'Tactical grand strategy focusing on medieval kingdom management.', 'default_cover.jpg'),
('Shadow Tactics: Blades of the Shogun', 4, 1, 2, 2016, 'Stealth strategy game set in Japan around the Edo period.', 'default_cover.jpg'),
('MLB The Show 23', 5, 2, 1, 2023, 'Experience modern and historic baseball eras on the field.', 'default_cover.jpg'),
('Tekken 8', 1, 2, 2, 2024, 'Fist meets fate in the latest installment of the iconic fighting series.', 'default_cover.jpg'),
('Armored Core VI', 1, 1, 2, 2023, 'Assemble and pilot your custom mech in fast-paced 3D battles.', 'default_cover.jpg'),
('XCOM 2', 4, 1, 2, 2016, 'Lead an underground resistance force to free Earth from alien rule.', 'default_cover.jpg'),
('Dead Space Remake', 1, 2, 3, 2023, 'Isaac Clarke explores the USG Ishimura filled with terrifying Necromorphs.', 'default_cover.jpg'),
('Alan Wake 2', 3, 1, 3, 2023, 'A psychological survival horror story involving two connected heroes.', 'default_cover.jpg'),
('Super Smash Bros. Ultimate', 1, 4, 1, 2018, 'Gaming icons clash in the ultimate fighting showdown for Nintendo Switch.', 'default_cover.jpg'),
('Fire Emblem: Engage', 4, 4, 2, 2023, 'Summon legendary heroes to save Elyos in tactical turn-based combat.', 'default_cover.jpg'),
('Dragon Age: Inquisition', 2, 1, 3, 2014, 'Lead the Inquisition to restore order to a fantasy continent.', 'default_cover.jpg'),
('Stardew Valley', 3, 4, 1, 2016, 'Inherit your grandfather old farm plot and build a thriving rural life.', 'default_cover.jpg'),
('Sea of Thieves', 3, 3, 2, 2018, 'Live the essential pirate life with sailing, fighting, and looting.', 'default_cover.jpg'),
('Total War: Warhammer III', 4, 1, 2, 2022, 'Rally your forces and step into the Realm of Chaos strategy sandbox.', 'default_cover.jpg'),
('Overwatch 2', 1, 1, 2, 2022, 'Team-based hero shooter set in an optimistic future world.', 'default_cover.jpg'),
('Apex Legends', 1, 1, 2, 2019, 'Master an expanding roster of Legends in a competitive battle royale.', 'default_cover.jpg'),
('Rocket League', 5, 1, 1, 2015, 'High-powered hybrid of arcade-style soccer and vehicular mayhem.', 'default_cover.jpg'),
('Tony Hawk Pro Skater 1+2', 5, 2, 2, 2020, 'Drop back in with iconic skateboarding levels rebuilt from the ground up.', 'default_cover.jpg'),
('Demon Souls Remake', 2, 2, 3, 2020, 'Experience the brutal origin story of the Souls franchise on PS5.', 'default_cover.jpg'),
('Control', 1, 1, 3, 2019, 'Unleash supernatural abilities to defeat an enemy in a secretive agency.', 'default_cover.jpg'),
('Ghost of Tsushima', 1, 2, 3, 2020, 'Jin Sakai must wage an unconventional war for the freedom of Tsushima.', 'default_cover.jpg'),
('Death Stranding', 3, 1, 3, 2019, 'Reconnect a fractured society in an open-world action-adventure game.', 'default_cover.jpg'),
('Horizon Forbidden West', 1, 2, 2, 2022, 'Aloy braves a majestic but dangerous frontier to save Earth climate.', 'default_cover.jpg'),