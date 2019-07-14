SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `urtv` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `urtv`;

CREATE TABLE `categories` (
  `ID` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Group_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `categories_groups` (
  `ID` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `categories_videos` (
  `ID` int(11) NOT NULL,
  `Movie_ID` int(11) DEFAULT NULL,
  `Category_ID` int(11) NOT NULL,
  `Stream_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `change_email` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Code` varchar(32) NOT NULL,
  `Time` varchar(11) NOT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments_streams` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Stream_ID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `likes` (
  `ID` int(11) NOT NULL,
  `Movie_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Type` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `likes_streams` (
  `ID` int(11) NOT NULL,
  `Stream_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Type` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `movies` (
  `ID` int(11) NOT NULL,
  `User_ID` int(7) NOT NULL,
  `Title` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Views` int(11) NOT NULL,
  `Size` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  `Describtion` text NOT NULL,
  `Password` varchar(60) DEFAULT NULL,
  `Verified` varchar(3) NOT NULL,
  `Filename` varchar(32) NOT NULL,
  `Author` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `newsletter` (
  `ID` int(11) NOT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `reset_password` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Code` varchar(100) NOT NULL,
  `Time` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `streams` (
  `ID` int(11) NOT NULL,
  `User_ID` int(7) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Views` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  `Describtion` text NOT NULL,
  `Password` varchar(60) DEFAULT NULL,
  `Streamkey_last` bigint(10) DEFAULT NULL,
  `Streamkey_active` bigint(10) DEFAULT NULL,
  `Filename` varchar(32) NOT NULL,
  `Planned_date` datetime DEFAULT NULL,
  `Author` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `subscription` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Author_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Movie_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `unverified_users` (
  `ID` int(7) NOT NULL,
  `Login` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Password` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Name` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Surname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Code` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `ID` int(7) NOT NULL,
  `Login` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Password` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Name` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Surname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Privileges` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Space` int(11) NOT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `categories`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `categories_ibfk_1` (`Group_ID`);

ALTER TABLE `categories_groups`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `categories_videos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Category_ID` (`Category_ID`),
  ADD KEY `categories_videos_ibfk_2` (`Stream_ID`),
  ADD KEY `Movie_ID` (`Movie_ID`);

ALTER TABLE `change_email`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `User_ID` (`User_ID`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Movie_ID` (`Movie_ID`),
  ADD KEY `User_ID` (`User_ID`);

ALTER TABLE `comments_streams`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Stream_ID` (`Stream_ID`),
  ADD KEY `User_ID` (`User_ID`);

ALTER TABLE `likes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `likes_ibfk_1` (`Movie_ID`),
  ADD KEY `User_ID` (`User_ID`);

ALTER TABLE `likes_streams`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `likes_streams_ibfk_1` (`Stream_ID`),
  ADD KEY `likes_streams_ibfk_2` (`User_ID`);

ALTER TABLE `movies`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `movies_ibfk_1` (`User_ID`);

ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Code_UNIQUE` (`Code`),
  ADD KEY `User_ID` (`User_ID`);

ALTER TABLE `streams`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `User_ID_UNIQUE` (`User_ID`);

ALTER TABLE `subscription`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `newsletter_ibfk_1` (`Author_ID`),
  ADD KEY `newsletter_ibfk_2` (`User_ID`);

ALTER TABLE `tags`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Movie_ID` (`Movie_ID`);

ALTER TABLE `unverified_users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`),
  ADD UNIQUE KEY `Code` (`Code`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Login` (`Login`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `ID` (`ID`);


ALTER TABLE `categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories_groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories_videos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `change_email`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `comments_streams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `likes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `likes_streams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `movies`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `newsletter`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reset_password`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `streams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscription`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tags`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `unverified_users`
  MODIFY `ID` int(7) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `ID` int(7) NOT NULL AUTO_INCREMENT;


ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`Group_ID`) REFERENCES `categories_groups` (`ID`);

ALTER TABLE `categories_videos`
  ADD CONSTRAINT `categories_videos_ibfk_1` FOREIGN KEY (`Category_ID`) REFERENCES `categories` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_videos_ibfk_2` FOREIGN KEY (`Stream_ID`) REFERENCES `streams` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_videos_ibfk_3` FOREIGN KEY (`Movie_ID`) REFERENCES `movies` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `change_email`
  ADD CONSTRAINT `change_email_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movies` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `comments_streams`
  ADD CONSTRAINT `comments_streams_ibfk_1` FOREIGN KEY (`Stream_ID`) REFERENCES `streams` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_streams_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movies` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `likes_streams`
  ADD CONSTRAINT `likes_streams_ibfk_1` FOREIGN KEY (`Stream_ID`) REFERENCES `streams` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_streams_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reset_password`
  ADD CONSTRAINT `reset_password_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `streams`
  ADD CONSTRAINT `streams_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`Movie_ID`) REFERENCES `movies` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
