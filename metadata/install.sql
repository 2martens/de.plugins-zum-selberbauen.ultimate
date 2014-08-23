/**** tables ****/

DROP TABLE IF EXISTS ultimate1_block;
CREATE TABLE ultimate1_block (
	blockID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	blockTypeID INT(10) NOT NULL,
	query VARCHAR(255) NOT NULL DEFAULT '',
	parameters VARCHAR(255) NOT NULL DEFAULT '',
	showOrder INT(10) NOT NULL DEFAULT 0,
	additionalData TEXT NOT NULL,
	KEY (blockTypeID)
);

DROP TABLE IF EXISTS ultimate1_blocktype;
CREATE TABLE ultimate1_blocktype (
	blockTypeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	blockTypeName VARCHAR(255) NOT NULL DEFAULT '',
	blockTypeClassName VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY packageID (packageID, blockTypeName)
);

DROP TABLE IF EXISTS ultimate1_block_to_template;
CREATE TABLE ultimate1_block_to_template (
	blockID INT(10) NOT NULL,
	templateID INT(10) NOT NULL,
	KEY (blockID),
	KEY (templateID)
);

DROP TABLE IF EXISTS ultimate1_category;
CREATE TABLE ultimate1_category (
	categoryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryParent INT(10) NOT NULL DEFAULT 0,
	categorySlug VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY categorySlug (categoryParent, categorySlug)
);

DROP TABLE IF EXISTS ultimate1_category_language;
CREATE TABLE ultimate1_category_language (
	languageEntryID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID INT(10) NOT NULL,
	languageID INT(10) NULL,
	categoryTitle VARCHAR(255) NOT NULL DEFAULT '',
	categoryDescription VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY (categoryID, languageID)
);

DROP TABLE IF EXISTS ultimate1_content;
CREATE TABLE ultimate1_content (
	contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	authorID INT(10) NOT NULL,
	contentSlug VARCHAR(255) NOT NULL DEFAULT '',
  lastModified INT(10) NOT NULL DEFAULT 0,
	cumulativeLikes MEDIUMINT(7) NOT NULL DEFAULT 0,
	views MEDIUMINT(7) NOT NULL DEFAULT 0,
	KEY (authorID)
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

DROP TABLE IF EXISTS ultimate1_content_to_category;
CREATE TABLE ultimate1_content_to_category (
	contentID INT(10) NOT NULL,
	categoryID INT(10) NOT NULL,
	KEY (contentID),
	KEY (categoryID)
);

DROP TABLE IF EXISTS ultimate1_content_to_page;
CREATE TABLE ultimate1_content_to_page (
	contentID INT(10) NOT NULL UNIQUE KEY,
	pageID INT(10) NOT NULL UNIQUE KEY
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

DROP TABLE IF EXISTS ultimate1_layout;
CREATE TABLE ultimate1_layout (
	layoutID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	objectID INT(10) NOT NULL,
	objectType ENUM('category', 'content', 'index', 'page') NOT NULL,
	UNIQUE KEY (objectID, objectType)
);

DROP TABLE IF EXISTS ultimate1_link;
CREATE TABLE ultimate1_link (
	linkID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	linkName VARCHAR(255) NOT NULL DEFAULT '',
	linkDescription VARCHAR(255) NOT NULL DEFAULT '',
	linkURL VARCHAR(255) NOT NULL DEFAULT '' UNIQUE KEY
);

DROP TABLE IF EXISTS ultimate1_link_to_category;
CREATE TABLE ultimate1_link_to_category (
	linkID INT(10) NOT NULL,
	categoryID INT(10) NOT NULL,
	KEY (linkID),
	KEY (categoryID)
);

DROP TABLE IF EXISTS ultimate1_media_mimetype;
CREATE TABLE ultimate1_media_mimetype (
	mimeTypeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	mimeType VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS ultimate1_menu;
CREATE TABLE ultimate1_menu (
	menuID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	menuName VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS ultimate1_menu_item;
CREATE TABLE ultimate1_menu_item (
	menuItemID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	menuID INT(10) NOT NULL,
	menuItemName VARCHAR(255) NOT NULL DEFAULT '',
	menuItemParent VARCHAR(255) NOT NULL DEFAULT '',
	menuItemController VARCHAR(255) NULL DEFAULT NULL,
	menuItemLink VARCHAR(255) NOT NULL DEFAULT '',
	showOrder INT(10) NOT NULL DEFAULT 0,
	permissions TEXT NULL,
	options TEXT NULL,
	type ENUM('category', 'content', 'custom', 'page') NOT NULL,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0,
	className VARCHAR(255) NOT NULL DEFAULT '',
	isLandingPage TINYINT(1) NOT NULL DEFAULT 0,
	UNIQUE KEY (menuID, menuItemName)
);

DROP TABLE IF EXISTS ultimate1_menu_to_template;
CREATE TABLE ultimate1_menu_to_template (
	menuID INT(10) NOT NULL,
	templateID INT(10) NOT NULL,
	KEY (menuID),
	UNIQUE KEY (templateID)
);

DROP TABLE IF EXISTS ultimate1_meta;
CREATE TABLE ultimate1_meta (
	metaID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	objectID INT(10) NOT NULL,
	objectType ENUM('category', 'content', 'page') NOT NULL,
	metaDescription VARCHAR(255) NOT NULL DEFAULT '',
	metaKeywords VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY (objectID, objectType)
);

DROP TABLE IF EXISTS ultimate1_page;
CREATE TABLE ultimate1_page (
	pageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	authorID INT(10) NOT NULL,
	pageParent INT(10) NOT NULL DEFAULT 0,
	pageTitle VARCHAR(255) NOT NULL DEFAULT '',
	pageSlug VARCHAR(255) NOT NULL DEFAULT '' UNIQUE KEY,
	publishDate INT(10) NOT NULL DEFAULT 0,
	lastModified INT(10) NOT NULL DEFAULT 0,
	status INT(1) NOT NULL DEFAULT 0,
	visibility ENUM('public', 'protected', 'private') NOT NULL DEFAULT 'public',
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

DROP TABLE IF EXISTS ultimate1_template;
CREATE TABLE ultimate1_template (
	templateID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	templateName VARCHAR(255) NOT NULL DEFAULT '',
	widgetAreaSide ENUM('left', 'right') NOT NULL DEFAULT 'right',
	showWidgetArea TINYINT(1) NOT NULL DEFAULT 1
);

DROP TABLE IF EXISTS ultimate1_template_to_layout;
CREATE TABLE ultimate1_template_to_layout (
	layoutID INT(10) NOT NULL UNIQUE KEY,
	templateID INT(10) NOT NULL,
	KEY (templateID)
);

DROP TABLE IF EXISTS ultimate1_widget_area;
CREATE TABLE ultimate1_widget_area (
	widgetAreaID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	widgetAreaName VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS ultimate1_widget_area_option;
CREATE TABLE ultimate1_widget_area_option (
	widgetAreaID INT(10) NOT NULL,
	boxID INT(10) NOT NULL,
	showOrder INT(10) NOT NULL,
	UNIQUE KEY widgetAreaOption (widgetAreaID, boxID)
);

DROP TABLE IF EXISTS ultimate1_widget_area_to_template;
CREATE TABLE ultimate1_widget_area_to_template (
	templateID INT(10) NOT NULL,
	widgetAreaID INT(10) NOT NULL,
	UNIQUE KEY (templateID),
	KEY (widgetAreaID)
);

/**** foreign keys ****/
ALTER TABLE ultimate1_block ADD FOREIGN KEY (blockTypeID) REFERENCES ultimate1_blocktype (blockTypeID) ON DELETE CASCADE;
ALTER TABLE ultimate1_block_to_template ADD FOREIGN KEY (blockID) REFERENCES ultimate1_block (blockID) ON DELETE CASCADE;
ALTER TABLE ultimate1_block_to_template ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_blocktype ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_category_language ADD FOREIGN KEY (categoryID) REFERENCES ultimate1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE ultimate1_category_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_version ADD FOREIGN KEY (contentID) REFERENCES ultimate1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_version ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_language ADD FOREIGN KEY (contentVersionID) REFERENCES ultimate1_content_version (versionID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_to_category ADD FOREIGN KEY (contentID) REFERENCES ultimate1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_to_category ADD FOREIGN KEY (categoryID) REFERENCES ultimate1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_to_page ADD FOREIGN KEY (contentID) REFERENCES ultimate1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_content_to_page ADD FOREIGN KEY (pageID) REFERENCES ultimate1_page (pageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_link_to_category ADD FOREIGN KEY (linkID) REFERENCES ultimate1_link (linkID) ON DELETE CASCADE;
ALTER TABLE ultimate1_link_to_category ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE ultimate1_menu_item ADD FOREIGN KEY (menuID) REFERENCES ultimate1_menu (menuID) ON DELETE CASCADE;
ALTER TABLE ultimate1_menu_to_template ADD FOREIGN KEY (menuID) REFERENCES ultimate1_menu (menuID) ON DELETE CASCADE;
ALTER TABLE ultimate1_menu_to_template ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_page ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE ultimate1_page_language ADD FOREIGN KEY (pageID) REFERENCES ultimate1_page (pageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_page_language ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_template_to_layout ADD FOREIGN KEY (layoutID) REFERENCES ultimate1_layout (layoutID) ON DELETE CASCADE;
ALTER TABLE ultimate1_template_to_layout ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_to_template ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_to_template ADD FOREIGN KEY (widgetAreaID) REFERENCES ultimate1_widget_area (widgetAreaID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_option ADD FOREIGN KEY (widgetAreaID) REFERENCES ultimate1_widget_area (widgetAreaID) ON DELETE CASCADE;

/**** default entries ****/
-- default category
INSERT INTO ultimate1_category (categorySlug) VALUES ('uncategorized');
INSERT INTO ultimate1_category (categorySlug) VALUES ('pages');

-- default layouts
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'index');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'content');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'page');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'category');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (1, 'category');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (2, 'category');

-- mime types
INSERT INTO ultimate1_media_mimetype (mimeType) VALUES 
('application/x-dvi'),
('application/x-shockwave-flash'),
('audio/basic'),
('audio/x-mpeg'),
('audio/x-pn-realaudio'),
('audio/x-pn-realaudio-plugin'),
('audio/x-qt-stream'),
('audio/x-wav'),
('image/gif'),
('image/jpeg'),
('image/png'),
('image/tiff'),
('image/vnd.wap.wbmp'),
('image/x-portable-anymap'),
('image/x-portable-bitmap'),
('image/x-portable-graymap'),
('image/x-portable-pixmap'),
('image/x-rgb'),
('video/mpeg'),
('video/quicktime'),
('video/vnd.vivo'),
('video/x-msvideo'),
('video/x-sgi-movie');
