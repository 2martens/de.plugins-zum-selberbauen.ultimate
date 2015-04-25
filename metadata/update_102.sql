/**
 * NEW TABLES
 */

DROP TABLE IF EXISTS ultimate1_category_language;
CREATE TABLE ultimate1_category_language (
  languageEntryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  categoryID INT(10) NOT NULL,
  languageID INT(10) NULL,
  categoryTitle VARCHAR(255) NOT NULL DEFAULT '',
  categoryDescription VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY (categoryID, languageID)
);

DROP TABLE IF EXISTS ultimate1_content_language;
CREATE TABLE ultimate1_content_language (
  languageEntryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  contentVersionID INT(10) NOT NULL,
  languageID INT(10) NULL,
  contentTitle VARCHAR(255) NULL,
  contentDescription VARCHAR(255) NULL,
  contentText MEDIUMTEXT NULL,
  UNIQUE KEY (contentVersionID, languageID)
);

DROP TABLE IF EXISTS ultimate1_content_version;
CREATE TABLE ultimate1_content_version (
  versionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  contentID INT(10) NOT NULL,
  authorID INT(10) NOT NULL,
  versionNumber INT(10) NOT NULL DEFAULT 0,
  attachments SMALLINT(5) NOT NULL DEFAULT 0,
  enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
  enableHtml TINYINT(1) NOT NULL DEFAULT 0,
  enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
  publishDate INT(10) NOT NULL DEFAULT 0,
  status INT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY (contentID, versionNumber),
  KEY (authorID)
);

DROP TABLE IF EXISTS ultimate1_page_language;
CREATE TABLE ultimate1_page_language (
  languageEntryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  pageID INT(10) NOT NULL,
  languageID INT(10) NULL,
  pageTitle VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY (pageID, languageID)
);

/**
 * MODIFY TABLES
 */
ALTER TABLE ultimate1_menu_item ADD objectID INT(10) NULL AFTER type;

/**
 * Drop existing tables
 */

DROP TABLE ultimate1_user_group_to_content;
DROP TABLE ultimate1_user_group_to_page;

/**
 * Foreign keys
 */

ALTER TABLE ultimate1_category_language ADD FOREIGN KEY (categoryID) REFERENCES ultimate1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE ultimate1_category_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_version ADD FOREIGN KEY (contentID) REFERENCES ultimate1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_version ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_language ADD FOREIGN KEY (contentVersionID) REFERENCES ultimate1_content_version (versionID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_page_language ADD FOREIGN KEY (pageID) REFERENCES ultimate1_page (pageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_page_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
