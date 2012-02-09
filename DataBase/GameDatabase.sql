CREATE TABLE IF NOT EXISTS Profile (
  ProfileID int(11) NOT NULL AUTO_INCREMENT,
  UserName varchar(30) NOT NULL DEFAULT '',
  Password varchar(40) NOT NULL DEFAULT '',
  DateJoined date DEFAULT NULL,
  Email varchar(40),
  Description longtext,
  FailedPass int(11),
  LastLoggedIn date DEFAULT NULL,
  PRIMARY KEY (ProfileID)
);

INSERT INTO Profile (ProfileID, UserName, Password, DateJoined, Description, FailedPass, LastLoggedIn)
VALUES (1, 'Admin', 'changeme', Now(), 'I am the Administrator', 0, Now());

CREATE TABLE IF NOT EXISTS ProfilePicture (
  ProfilePictureID int(11) NOT NULL auto_increment,
  ProfileID int(11) DEFAULT 0,
  FileName varchar(100) NOT NULL,
  FileSize int(11) NOT NULL,
  FileType varchar(60) NOT NULL,
  FileEntryDate datetime NOT NULL,
  FileData longblob NOT NULL,
  ThumbWidth int(11) NOT NULL,
  ThumbHeight int(11) NOT NULL,
  ThumbData longblob NOT NULL,
  PRIMARY KEY (ProfilePictureID),
  FOREIGN KEY (ProfileID) REFERENCES Profile (ProfileID) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS Game (
  GameID INT(11) NOT NULL AUTO_INCREMENT,
  ProfileID int(11),
  Name varchar(30),
  GameDescription longtext,
  NumberOfRatings int(11) DEFAULT 0,
  CurrentRating int(11) DEFAULT 0,
  DateSubmitted date DEFAULT NULL,
  DateReleased date DEFAULT NULL,
  MinPlayers int(5),
  MaxPlayers int(5),
  Engine varchar(30) DEFAULT 'Not Listed',
  Website varchar(30) DEFAULT 'Not Listed',
  PRIMARY KEY (GameID),
  FOREIGN KEY (ProfileID) REFERENCES Profile (ProfileID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Comment (
  CommentID int(11) NOT NULL AUTO_INCREMENT,
  GameID int(11),
  ProfileID int(11),
  DateSubmitted date DEFAULT NULL,
  Subject varchar(30) DEFAULT 'No Subject',
  Comment mediumtext,
  PositiveFeedback int(11) DEFAULT 0,
  ParentPost int(11) DEFAULT 0,
  NegativeFeedback int(11) DEFAULT 0,
  PRIMARY KEY (CommentID),
  FOREIGN KEY (GameID) REFERENCES Game (GameID) ON DELETE CASCADE,
  FOREIGN KEY (ProfileID) REFERENCES Profile (ProfileID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ScreenShot (
  ScreenShotID int(11) NOT NULL auto_increment,
  GameId int(11),
  FileName varchar(100) NOT NULL,
  FileSize int(11) NOT NULL,
  FileType varchar(60) NOT NULL,
  FileEntryDate datetime NOT NULL,
  FileData longblob NOT NULL,
  ThumbWidth int(11) NOT NULL,
  ThumbHeight int(11) NOT NULL,
  ThumbData longblob NOT NULL,
  PRIMARY KEY  (ScreenShotID),
  FOREIGN KEY (GameID) REFERENCES Game (GameID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Genre (
  GenreID int(11) NOT NULL AUTO_INCREMENT,
  GenreDesc varchar(15) ,
  PRIMARY KEY (GenreID)
);

/*
Turns out we will be abreviating
After writing the genre, do not put a comma as MySQL does not know what to do about it, and will send an error!
*/
INSERT INTO Genre (GenreID, GenreDesc) VALUES (1, 'fps');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (2, 'adventure');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (3, 'action');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (4, 'puzzle');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (5, 'rts');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (6, 'horror');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (7, 'sports');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (8, 'rpg');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (9, 'tps');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (10, 'fighter');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (11, 'simulation');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (12, 'remake');
INSERT INTO Genre (GenreID, GenreDesc) VALUES (13, 'roguelike');

CREATE TABLE IF NOT EXISTS HasGenre (
  GenreID int(11) DEFAULT 0,
  GameID int(11),
  UNIQUE KEY (GenreID, gameID),
  FOREIGN KEY (GenreID) REFERENCES Genre (GenreID),
  FOREIGN KEY (GameID) REFERENCES Game (GameID)
);

CREATE TABLE IF NOT EXISTS Platform (
  PlatformID int(11) NOT NULL AUTO_INCREMENT,
  PlatformDesc varchar(15),
  PRIMARY KEY (PlatformID)
);

/*
After writing the platform, do not put the comma as MySQL doesn’t know how to read that, and will give an error message
*/
INSERT INTO Platform (PlatformID, PlatformDesc) VALUES (1, 'windows');
INSERT INTO Platform (PlatformID, PlatformDesc) VALUES (2, 'mac os x');
INSERT INTO Platform (PlatformID, PlatformDesc) VALUES (3, 'linux');
INSERT INTO Platform (PlatformID, PlatformDesc) VALUES (4, 'dos');

CREATE TABLE IF NOT EXISTS HasPlatform (
  PlatformID int(11),
  GameID int(11),
  FOREIGN KEY (PlatformID) REFERENCES Platform (PlatformID) ON DELETE CASCADE,
  FOREIGN KEY (GameID) REFERENCES Game (GameID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS HasFeedBack (
  ProfileID int(11) NOT NULL,
  CommentID int(11) NOT NULL,
  PRIMARY KEY (ProfileID),
  FOREIGN KEY (ProfileID) REFERENCES Profile (ProfileID) ON DELETE CASCADE,
  FOREIGN KEY (CommentID) REFERENCES Comment (CommentID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS HasRatings (
  ProfileID int(11) NOT NULL,
  GameID int(11) NOT NULL,
  Rating int(4),
  PRIMARY KEY (ProfileID),
  FOREIGN KEY (ProfileID) REFERENCES Profile (ProfileID) ON DELETE CASCADE,
  FOREIGN KEY (GameID) REFERENCES Game (GameID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Upload (
  UploadID int(11) NOT NULL auto_increment,
  GameID int(11) NOT NULL,
  FileName varchar(100) NOT NULL,
  FileSize int(11) NOT NULL,
  FileType varchar(60) NOT NULL,
  FileEntryDate datetime NOT NULL,
  FileData longblob NOT NULL,
  PlatformID int(11) DEFAULT 0,
  PRIMARY KEY  (UploadID),
  FOREIGN KEY (GameID) REFERENCES Game (GameID) ON DELETE CASCADE
);