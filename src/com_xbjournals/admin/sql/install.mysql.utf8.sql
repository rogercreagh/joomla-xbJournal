# sql installation file for component xbJournals v0.0.0.7 11th April 2023

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`, `table`, `field_mappings`, `router`,`rules`) 
VALUES
('XbJournals Entry', 'com_xbjournals.entry', 
'{"formFile":"administrator\\/components\\/com_xbjournals\\/models\\/forms\\/entry.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbjournalreviews","key":"id","type":"Entry","prefix":"XbjournalsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "cal_description",
    "core_catid": "catid"
  }}',
'XbjournalsHelperRoute::getReviewRoute',''),

('XbJournals Category', 'com_xbjournals.category',
'{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
'{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},
"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
	"core_content_item_id":"id",
	"core_title":"title",
	"core_state":"published",
	"core_alias":"alias",
	"core_created_time":"created_time",
	"core_modified_time":"modified_time",
	"core_body":"description", 
	"core_hits":"hits",
	"core_publish_up":"null",
	"core_publish_down":"null",
	"core_access":"access", 
	"core_params":"params", 
	"core_featured":"null", 
	"core_metadata":"metadata", 
	"core_language":"language", 
	"core_images":"null", 
	"core_urls":"null", 
	"core_version":"version",
	"core_ordering":"null", 
	"core_metakey":"metakey", 
	"core_metadesc":"metadesc", 
	"core_catid":"parent_id", 
	"core_xreference":"null", 
	"asset_id":"asset_id"}, 
  "special":{
    "parent_id":"parent_id",
	"lft":"lft",
	"rgt":"rgt",
	"level":"level",
	"path":"path",
	"extension":"extension",
	"note":"note"}}',
'XbjournalsHelperRoute::getCategoryRoute','');

CREATE TABLE IF NOT EXISTS `#__xbjournals_servers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `url` varchar(190) NOT NULL DEFAULT '',
  `username` varchar(190) NOT NULL DEFAULT '',
  `password` varchar(190) NOT NULL DEFAULT '',
  `description` varchar(4094) NOT NULL DEFAULT '',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(10) NOT NULL  DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# moved to install script in case data is not deleted on uninstall CREATE UNIQUE INDEX `serveraliasindex` ON `#__xbjournals_servers` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbjournals_calendars` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `server_id` int(10) NOT NULL DEFAULT '0' COMMENT 'link to xbjournals_servers table',
  `cal_displayname` varchar(190) NOT NULL DEFAULT '',
  `cal_url` varchar(190) NOT NULL DEFAULT '',
  `cal_ctag` varchar(190) NOT NULL DEFAULT '',
  `cal_calendar_id`  varchar(190) NOT NULL DEFAULT '',
  `cal_rgb_color` varchar(8) NOT NULL DEFAULT '',
  `cal_order` int(10)  NOT NULL  DEFAULT '0',
  `cal_components` varchar(190) NOT NULL DEFAULT 'VEVENT,VJOURNAL,VTODO',
  `last_checked` datetime,
  `title` varchar(190) NOT NULL DEFAULT '' COMMENT 'default to cal_displayname but can be edited. Prefer unique',
  `alias` varchar(190) NOT NULL DEFAULT '' COMMENT 'derive from title with seq number to be unique',
  `description` varchar(4094) NOT NULL DEFAULT '',
  `catid` int(10) NOT NULL  DEFAULT '0' COMMENT 'default to uncategorised',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'enforce state unpublished (0) if does not support VJOURNAL',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(10) NOT NULL  DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# moved to install script in case data is not deleted on uninstall CREATE UNIQUE INDEX `calendaraliasindex` ON `#__xbjournals_calendars` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbjournals_vjournal_entries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `calendar_id` int(10) NOT NULL DEFAULT '0' COMMENT 'link to xbjournals_calendars', 
  `entry_type` ENUM('Journal','Note') NOT NULL DEFAULT 'Journal' COMMENT 'note if dtstart not set',  
  `etag` varchar(511) NOT NULL,
  `href` varchar(511) NOT NULL,
  `data` text COMMENT 'original raw ical',
  `version` varchar(190) NOT NULL DEFAULT '0',
  `prodid` varchar(190) NOT NULL DEFAULT '0',
  `dtstamp`  varchar(190) NOT NULL DEFAULT '',  
  `uid` varchar(190) NOT NULL DEFAULT '',
  `sequence` varchar(190) NOT NULL DEFAULT '0',
  `summary` varchar(1022) NOT NULL DEFAULT '' COMMENT 'used as Joomla title',
  `description` text DEFAULT '',
  `geo` varchar(190) NOT NULL DEFAULT '',
  `location` varchar(510) NOT NULL DEFAULT '',
  `url` varchar(254) NOT NULL DEFAULT '',
  `class` varchar(190) NOT NULL DEFAULT '',
  `status` varchar(190) NOT NULL DEFAULT '',
  `dtstart` varchar(190) COMMENT 'null if note',
  `categories` varchar(1022) NOT NULL DEFAULT '' COMMENT 'used as joomla tags',
  `comments` varchar(4094) NOT NULL DEFAULT '',
  `otherprops` varchar(4094) NOT NULL DEFAULT '',
  `title` varchar(254) NOT NULL DEFAULT '' COMMENT 'uses cal_summary truncated to 254 chars',
  `alias` varchar(254) NOT NULL DEFAULT '', 
  `catid` int(10) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime COMMENT 'ical created value',
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `modified` datetime COMMENT 'ical modified-time',
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` mediumtext COMMENT 'admin note',
   PRIMARY KEY (`id`)
  )  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# moved to install script in case data is not deleted on uninstall CREATE UNIQUE INDEX `entryaliasindex` ON `#__xbjournals_vjournal_entries` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbjournals_vjournal_attachments` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) NOT NULL DEFAULT '0' COMMENT 'link to entry',
  `inline_data` BLOB,
  `uri` text,
  `encoding` varchar(190) NOT NULL DEFAULT '0',
  `fmttype` varchar(190) NOT NULL DEFAULT '0',
  `value` varchar(1022) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

