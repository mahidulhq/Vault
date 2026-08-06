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
('Returnal', 1, 2, 3, 2021, 'Fight to survive on a dynamic planet in a fast-paced rogue-like shooter.', 'default_cover.jpg'),
('Ratchet & Clank: Rift Apart', 1, 2, 1, 2021, 'Blast your way through an interdimensional adventure with Ratchet and Rivet.', 'default_cover.jpg'),
('Assassins Creed Valhalla', 1, 3, 3, 2020, 'Lead epic Viking raids against Saxon troops and fortresses in Britain.', 'default_cover.jpg'),
('Far Cry 6', 1, 3, 3, 2021, 'Join a modern guerrilla revolution to liberate a tropical nation.', 'default_cover.jpg'),
('Watch Dogs Legion', 1, 3, 3, 2020, 'Build an underground resistance force from anyone you meet in London.', 'default_cover.jpg'),
('Frostpunk', 4, 1, 2, 2018, 'Build and manage the last heat-driven city on an icy Earth.', 'default_cover.jpg'),
('Crusader Kings III', 4, 1, 2, 2020, 'Guide a royal dynasty through the Middle Ages in grand strategic detail.', 'default_cover.jpg'),
('Hearts of Iron IV', 4, 1, 2, 2016, 'Take command of any nation during World War II in grand strategy style.', 'default_cover.jpg'),
('Pikmin 4', 4, 4, 1, 2023, 'Guide tiny creatures to overcome obstacles and explore a mysterious world.', 'default_cover.jpg'),
('Animal Crossing: New Horizons', 3, 4, 1, 2020, 'Create your personal island paradise and customize your village life.', 'default_cover.jpg'),
('Luigi Mansion 3', 3, 4, 1, 2019, 'Vacuum up ghosts and solve puzzles in a haunted luxury hotel.', 'default_cover.jpg'),
('Xenoblade Chronicles 3', 2, 4, 2, 2022, 'Join Noah and Mio amid turmoil between hostile nations of Aionios.', 'default_cover.jpg'),
('Bayonetta 3', 1, 4, 3, 2022, 'The Umbra Witch battles man-made biological weapons across cities.', 'default_cover.jpg'),
('Splatoon 3', 1, 4, 1, 2022, 'Enter the Splatlands for turf wars and colorful ink-shooting battles.', 'default_cover.jpg'),
('Super Mario Odyssey', 3, 4, 1, 2017, 'Cap-throw your way through immense 3D kingdoms to rescue Princess Peach.', 'default_cover.jpg'),
('Mario Kart 8 Deluxe', 5, 4, 1, 2017, 'Race friends on dozens of tracks with iconic Nintendo drivers.', 'default_cover.jpg'),
('Dirt 5', 5, 3, 1, 2020, 'Off-road racing experience featuring extreme routes and dynamic weather.', 'default_cover.jpg'),
('WWE 2K23', 5, 3, 2, 2023, 'Step into the ring with an expanded roster of WWE Legends and Superstars.', 'default_cover.jpg'),
('PGA Tour 2K23', 5, 3, 1, 2022, 'Hit the links against PGA Tour pros and build your custom course.', 'default_cover.jpg'),
('Riders Republic', 5, 3, 2, 2021, 'Jump into a massive multiplayer playground for bikes, skis, and wingsuits.', 'default_cover.jpg'),
('F1 23', 5, 1, 1, 2023, 'The official video game of the 2023 FIA Formula One World Championship.', 'default_cover.jpg'),
('Subnautica', 3, 1, 2, 2018, 'Descend into the depths of an alien underwater ocean world.', 'default_cover.jpg'),
('Outer Wilds', 3, 1, 1, 2019, 'Explore a solar system trapped in an endless 22-minute time loop.', 'default_cover.jpg'),
('No Man Sky', 3, 1, 1, 2016, 'Explore a procedural universe full of unique planets and lifeforms.', 'default_cover.jpg'),
('It Takes Two', 3, 2, 2, 2021, 'Embark on a crazy journey built purely for co-op two-player action.', 'default_cover.jpg'),
('Ori and the Will of the Wisps', 3, 3, 1, 2020, 'Embark on an adventure in a vast world filled with enemies and puzzles.', 'default_cover.jpg'),
('Celeste', 3, 4, 1, 2018, 'Help Madeline survive her inner demons on her journey to Celeste Mountain.', 'default_cover.jpg'),
('Inside', 3, 1, 2, 2016, 'A story-driven platformer combining intense action with challenging puzzles.', 'default_cover.jpg'),
('Little Nightmares II', 3, 2, 2, 2021, 'A suspense-adventure game where you play as a boy trapped in a distorted world.', 'default_cover.jpg'),
('Stray', 3, 2, 1, 2022, 'A third-person cat adventure game set amidst the detailed neon alleyways.', 'default_cover.jpg'),
('Sifu', 1, 2, 2, 2022, 'A third-person kung fu action game following a young student seeking revenge.', 'default_cover.jpg'),
('Ghostrunner 2', 1, 1, 3, 2023, 'First-person cyberpunk action where fast slice-and-dice combat reigns.', 'default_cover.jpg'),
('Lies of P', 2, 1, 3, 2023, 'A dark Souls-like game inspired by the story of Pinocchio in a Belle Epoque world.', 'default_cover.jpg'),
('Lord of the Fallen', 2, 3, 3, 2023, 'Journey across two parallel realms to overthrow Adyr the demon god.', 'default_cover.jpg'),
('Remnant II', 1, 3, 3, 2023, 'Pits survivors of humanity against new deadly creatures in terrifying worlds.', 'default_cover.jpg'),
('Payday 3', 1, 1, 3, 2023, 'The ultimate co-op heist shooter experience returning the iconic Payday Crew.', 'default_cover.jpg'),
('Exoprimal', 1, 3, 2, 2023, 'Online team-based action game that pits human exosuit technology against dinosaurs.', 'default_cover.jpg'),
('Company of Heroes 3', 4, 1, 3, 2023, 'Bigger and better than ever, bringing tactical WWII combat to new theaters.', 'default_cover.jpg'),
('Desperados III', 4, 1, 3, 2020, 'A real-time stealth tactics game set in a harsh Wild West scenario.', 'default_cover.jpg'),
('Command & Conquer Remastered', 4, 1, 2, 2020, 'Definitive real-time strategy collection restored in modern high resolution.', 'default_cover.jpg'),
('Northgard', 4, 4, 2, 2018, 'A strategy game based on Norse mythology control a clan of Vikings.', 'default_cover.jpg'),
('Into the Breach', 4, 4, 1, 2018, 'Control futuristic mechs to defeat alien threats in turn-based puzzle combat.', 'default_cover.jpg'),
('Skater XL', 5, 2, 1, 2020, 'Head into authentic street skateboarding with total board control features.', 'default_cover.jpg'),
('Session: Skate Sim', 5, 3, 1, 2022, 'Ultra-realistic skateboarding sim inspired by the 90s golden era.', 'default_cover.jpg'),
('Windjammers 2', 5, 4, 1, 2022, 'Fast-paced arcade flying disc game combining hand-drawn graphics and mechanics.', 'default_cover.jpg'),
('AO Tennis 2', 5, 2, 1, 2020, 'A tennis experience designed by and for its community of sports fans.', 'default_cover.jpg'),
('Super Mega Baseball 4', 5, 3, 1, 2023, 'More than 200 larger-than-life former pros hit the field alongside favorite series characters.', 'default_cover.jpg');
