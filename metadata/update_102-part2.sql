/**
 * Modify existing tables.
 */

ALTER TABLE ultimate1_category DROP KEY categoryTitle, DROP categoryTitle, DROP categoryDescription;
ALTER TABLE ultimate1_content DROP contentTitle, DROP contentDescription, DROP contentText, DROP attachments;
ALTER TABLE ultimate1_content DROP enableBBCodes, DROP enableHtml, DROP enableSmilies, DROP publishDate;
ALTER TABLE ultimate1_content DROP status, DROP visibility;
ALTER TABLE ultimate1_page DROP pageTitle;

