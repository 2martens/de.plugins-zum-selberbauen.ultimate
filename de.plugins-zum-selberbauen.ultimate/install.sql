/**** tables ****/
DROP TABLE IF EXISTS ultimate1_1_config;
CREATE TABLE ultimate1_1_config (
    configID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    configTitle VARCHAR(255) NOT NULL DEFAULT '',
    metaDescription VARCHAR(255) NOT NULL DEFAULT '',
    metaKeywords VARCHAR(255) NOT NULL DEFAULT '',
    templateName VARCHAR(255) NOT NULL DEFAULT '',
    storage VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS ultimate1_1_category;
CREATE TABLE ultimate1_1_category (
    categoryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    parentCategoryID INT(10) NOT NULL DEFAULT 0,
    categoryTitle VARCHAR(255) NOT NULL DEFAULT '',
    categoryDescription VARCHAR(255) NOT NULL DEFAULT '',
    categorySlug VARCHAR(255) NOT NULL DEFAULT '',
    KEY (parentCategoryID)
);

DROP TABLE IF EXISTS ultimate1_1_content_to_category;
CREATE TABLE ultimate1_1_content_to_category (
    contentID INT(10) NOT NULL DEFAULT 0,
    categoryID INT(10) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS ultimate1_1_page;
CREATE TABLE ultimate1_1_page (
    pageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    authorID INT(10) NOT NULL DEFAULT 0,
    contentID INT(10) NOT NULL DEFAULT 0,
    pageTitle VARCHAR(255) NOT NULL DEFAULT '',
    pageSlug VARCHAR(255) NOT NULL DEFAULT '',
    lastModified INT(10) NOT NULL DEFAULT 0,
    KEY (authorID),
    KEY (contentID)
);

DROP TABLE IF EXISTS ultimate1_1_content;
CREATE TABLE ultimate1_1_content (
    contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    authorID INT(10) NOT NULL DEFAULT 0,
    contentTitle VARCHAR(255) NOT NULL DEFAULT '',
    contentDescription VARCHAR(255) NOT NULL DEFAULT '',
    contentText MEDIUMTEXT NOT NULL,
    enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
    enableHtml TINYINT(1) NOT NULL DEFAULT 0,
    enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
    lastModified INT(10) NOT NULL DEFAULT 0,
    KEY (authorID),
    FULLTEXT INDEX (contentTitle, contentDescription, contentText)
);

DROP TABLE IF EXISTS ultimate1_1_component;
CREATE TABLE ultimate1_1_component (
    componentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    className VARCHAR(255) NOT NULL DEFAULT '' UNIQUE KEY,
    title VARCHAR(255) NOT NULL DEFAULT '',
    INDEX (className)   
);

/**** foreign keys ****/
ALTER TABLE ultimate1_1_page ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID);
ALTER TABLE ultimate1_1_page ADD FOREIGN KEY (contentID) REFERENCES ultimate1_1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID);
ALTER TABLE ultimate1_1_content_to_category ADD FOREIGN KEY (contentID) REFERENCES ultimate1_1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content_to_category ADD FOREIGN KEY (categoryID) REFERENCES ultimate1_1_category (categoryID) ON DELETE CASCADE;

/**** default entries ****/
-- default category
INSERT INTO ultimate1_1_category (categoryTitle, categorySlug) VALUES ('wcf.acp.ultimate.category.default', 'wcf.acp.ultimate.category.default.slug');
-- INSERT INTO ultimate1_1_content (contentTitle, contentDescription, contentText) VALUES ('Test', 'Ein kleiner Test', 'Das ist ein schöner Testtext, um zu prüfen, ob alles klappt.');
-- INSERT INTO ultimate1_1_config (templateName, requiredContents) VALUES ('test', 'a:1:{i:1;s:17:"SiteComponentPage";}');
-- INSERT INTO ultimate1_1_link (configID, linkSlug) VALUES (1, 'test');