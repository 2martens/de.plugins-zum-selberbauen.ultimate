/**** tables ****/


DROP TABLE IF EXISTS ultimate1_1_category;
CREATE TABLE ultimate1_1_category (
    categoryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    categoryParent INT(10) NOT NULL DEFAULT 0,
    categoryTitle VARCHAR(255) NOT NULL DEFAULT '',
    categoryDescription VARCHAR(255) NOT NULL DEFAULT '',
    categorySlug VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY categoryTitle (parentCategoryID, categoryTitle),
    UNIQUE KEY categorySlug (parentCategoryID, categorySlug)
);

DROP TABLE IF EXISTS ultimate1_1_content_to_category;
CREATE TABLE ultimate1_1_content_to_category (
    contentID INT(10) NOT NULL,
    categoryID INT(10) NOT NULL,
    KEY (contentID),
    KEY (categoryID)
);

DROP TABLE IF EXISTS ultimate1_1_content_to_page;
CREATE TABLE ultimate1_1_content_to_page (
    contentID INT(10) NOT NULL UNIQUE KEY,
    pageID INT(10) NOT NULL UNIQUE KEY
);

DROP TABLE IF EXISTS ultimate1_1_page;
CREATE TABLE ultimate1_1_page (
    pageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    authorID INT(10) NOT NULL,
    pageParent INT(10) NOT NULL DEFAULT 0,    
    pageTitle VARCHAR(255) NOT NULL DEFAULT '',
    pageSlug VARCHAR(255) NOT NULL DEFAULT '' UNIQUE KEY,
    publishDate INT(10) NOT NULL DEFAULT 0,
    lastModified INT(10) NOT NULL DEFAULT 0,
    status INT(1) NOT NULL DEFAULT 0,
    KEY (authorID)
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
    publishDate INT(10) NOT NULL DEFAULT 0,
    lastModified INT(10) NOT NULL DEFAULT 0,
    status INT(1) NOT NULL DEFAULT 0,
    KEY (authorID)
);


/**** foreign keys ****/
ALTER TABLE ultimate1_1_page ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content_to_category ADD FOREIGN KEY (contentID) REFERENCES ultimate1_1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content_to_category ADD FOREIGN KEY (categoryID) REFERENCES ultimate1_1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content_to_page ADD FOREIGN KEY (contentID) REFERENCES ultimate1_1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_1_content_to_page ADD FOREIGN KEY (pageID) REFERENCES ultimate1_1_page (pageID) ON DELETE CASCADE;

/**** default entries ****/
-- default category
INSERT INTO ultimate1_1_category (categoryTitle, categorySlug) VALUES ('ultimate.category.1.categoryTitle', 'uncategorized');
