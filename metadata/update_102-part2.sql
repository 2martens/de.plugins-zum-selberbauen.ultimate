/**
 * Modify existing tables.
 */

ALTER TABLE ultimate1_category DROP KEY categoryTitle;
ALTER TABLE ultimate1_category DROP categoryTitle; 
ALTER TABLE ultimate1_category DROP categoryDescription;
ALTER TABLE ultimate1_content DROP contentTitle;
ALTER TABLE ultimate1_content DROP contentDescription;
ALTER TABLE ultimate1_content DROP contentText;
ALTER TABLE ultimate1_content DROP attachments;
ALTER TABLE ultimate1_content DROP enableBBCodes;
ALTER TABLE ultimate1_content DROP enableHtml;
ALTER TABLE ultimate1_content DROP enableSmilies;
ALTER TABLE ultimate1_content DROP publishDate;
ALTER TABLE ultimate1_content DROP status;
ALTER TABLE ultimate1_content DROP visibility;
ALTER TABLE ultimate1_page DROP pageTitle;

