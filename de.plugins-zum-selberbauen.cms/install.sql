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
    contentText MEDIUMTEXT NOT NULL,
    FULLTEXT INDEX (contentText)
);

/**** foreign keys ****/
ALTER TABLE ultimate1_1_link ADD FOREIGN KEY (configID) REFERENCES ultimate1_1_config (configID) ON DELETE CASCADE;
