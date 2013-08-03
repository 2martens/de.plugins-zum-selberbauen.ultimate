/**** tables ****/

DROP TABLE IF EXISTS ultimate1_block;
CREATE TABLE ultimate1_block (
	blockID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	blockTypeID INT(10) NOT NULL,
	query VARCHAR(255) NOT NULL DEFAULT '',
	parameters VARCHAR(255) NOT NULL DEFAULT '',
	additionalData TEXT NOT NULL,
	KEY (blockTypeID)
);

DROP TABLE IF EXISTS ultimate1_blocktype;
CREATE TABLE ultimate1_blocktype (
	blockTypeID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	blockTypeName VARCHAR(255) NOT NULL DEFAULT '',
	blockTypeClassName VARCHAR(255) NOT NULL DEFAULT '',
	fixedHeight TINYINT(1) NOT NULL DEFAULT 1,
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
	categoryTitle VARCHAR(255) NOT NULL DEFAULT '',
	categoryDescription VARCHAR(255) NOT NULL DEFAULT '',
	categorySlug VARCHAR(255) NOT NULL DEFAULT '',
	UNIQUE KEY categoryTitle (categoryParent, categoryTitle),
	UNIQUE KEY categorySlug (categoryParent, categorySlug)
);

DROP TABLE IF EXISTS ultimate1_content;
CREATE TABLE ultimate1_content (
	contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	authorID INT(10) NOT NULL,
	contentTitle VARCHAR(255) NOT NULL DEFAULT '',
	contentDescription VARCHAR(255) NOT NULL DEFAULT '',
	contentSlug VARCHAR(255) NOT NULL DEFAULT '',
	contentText MEDIUMTEXT NOT NULL,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	cumulativeLikes MEDIUMINT(7) NOT NULL DEFAULT 0,
	publishDate INT(10) NOT NULL DEFAULT 0,
	lastModified INT(10) NOT NULL DEFAULT 0,
	status INT(1) NOT NULL DEFAULT 0,
	visibility ENUM('public', 'protected', 'private') NOT NULL DEFAULT 'public',
	KEY (authorID)
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

DROP TABLE IF EXISTS ultimate1_user_group_to_content;
CREATE TABLE ultimate1_user_group_to_content (
	groupID INT(10) NOT NULL,
	contentID INT(10) NOT NULL,
	KEY (groupID),
	KEY (contentID)
);

DROP TABLE IF EXISTS ultimate1_user_group_to_page;
CREATE TABLE ultimate1_user_group_to_page (
	groupID INT(10) NOT NULL,
	pageID INT(10) NOT NULL,
	KEY (groupID),
	KEY (pageID)
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
ALTER TABLE ultimate1_content ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
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
ALTER TABLE ultimate1_user_group_to_content ADD FOREIGN KEY (contentID) REFERENCES ultimate1_content (contentID) ON DELETE CASCADE;
ALTER TABLE ultimate1_user_group_to_content ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;
ALTER TABLE ultimate1_user_group_to_page ADD FOREIGN KEY (pageID) REFERENCES ultimate1_page (pageID) ON DELETE CASCADE;
ALTER TABLE ultimate1_user_group_to_page ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;
ALTER TABLE ultimate1_template_to_layout ADD FOREIGN KEY (layoutID) REFERENCES ultimate1_layout (layoutID) ON DELETE CASCADE;
ALTER TABLE ultimate1_template_to_layout ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_to_template ADD FOREIGN KEY (templateID) REFERENCES ultimate1_template (templateID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_to_template ADD FOREIGN KEY (widgetAreaID) REFERENCES ultimate1_widget_area (widgetAreaID) ON DELETE CASCADE;
ALTER TABLE ultimate1_widget_area_option ADD FOREIGN KEY (widgetAreaID) REFERENCES ultimate1_widget_area (widgetAreaID) ON DELETE CASCADE;

/**** default entries ****/
-- default category
INSERT INTO ultimate1_category (categoryTitle, categorySlug) VALUES ('ultimate.category.1.categoryTitle', 'uncategorized');
INSERT INTO ultimate1_category (categoryTitle, categoryDescription, categorySlug) 
	VALUES ('ultimate.category.2.categoryTitle', 'ultimate.category.2.categoryDescription', 'pages');

-- default layouts
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'index');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'content');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'page');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (0, 'category');
INSERT INTO ultimate1_layout (objectID, objectType) VALUES (1, 'category');

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