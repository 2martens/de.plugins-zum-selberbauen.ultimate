/**** tables ****/
DROP TABLE IF EXISTS ultimate1_1_config;
CREATE TABLE ultimate1_1_config (
    configID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    templateName VARCHAR(255) NOT NULL DEFAULT '',
    requiredContents VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS ultimate1_1_link;
CREATE TABLE ultimate1_1_link (
    linkID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    configID INT(10) NOT NULL DEFAULT 0,
    linkSlug VARCHAR(255) NOT NULL DEFAULT '',
    KEY (configID)
);

DROP TABLE IF EXISTS ultimate1_1_content;
CREATE TABLE ultimate1_1_content (
    contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    contentTitle VARCHAR(255) NOT NULL DEFAULT '',
    contentDescription VARCHAR(255) NOT NULL DEFAULT '',
    contentText MEDIUMTEXT NOT NULL,
    FULLTEXT INDEX (contentTitle, contentDescription, contentText)
);

/**** foreign keys ****/
ALTER TABLE ultimate1_1_link ADD FOREIGN KEY (configID) REFERENCES ultimate1_1_config (configID) ON DELETE CASCADE;


/**** default entries ****/
-- INSERT INTO ultimate1_1_content (contentTitle, contentDescription, contentText) VALUES ('Test', 'Ein kleiner Test', 'Das ist ein schöner Testtext, um zu prüfen, ob alles klappt.');
-- INSERT INTO ultimate1_1_config (templateName, requiredContents) VALUES ('test', 'a:1:{i:1;s:17:"SiteComponentPage";}');
-- INSERT INTO ultimate1_1_link (configID, linkSlug) VALUES (1, 'test');