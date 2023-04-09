# sql installation file for component xbJournals v0.0.0.5 4th April 2023

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
  `description` varchar(512) NOT NULL DEFAULT '',
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


CREATE TABLE IF NOT EXISTS `#__xbjournals_calendars` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `server_id` int(10) NOT NULL DEFAULT '0' COMMENT 'link to xbjournals_servers table',
  `cal_displayname` varchar(190) NOT NULL DEFAULT '',
  `cal_url` varchar(190) NOT NULL DEFAULT '',
  `cal_ctag` varchar(190) NOT NULL DEFAULT '',
  `cal_calendar_id`  varchar(190) NOT NULL DEFAULT '',
  `cal_rgb_color` varchar(8) NOT NULL DEFAULT '',
  `cal_order` int(10)  NOT NULL  DEFAULT '0',
  `last_checked` datetime,
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `catid` int(10) NOT NULL  DEFAULT '0',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `version` int(10) NOT NULL DEFAULT '0',
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

# CREATE UNIQUE INDEX `journalaliasindex` ON `#__xbjournals` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbjournals_vjournal_entries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `journal_id` int(10) NOT NULL DEFAULT '0' COMMENT 'link to xbjournals table', 
  `entry_type` varchar(10) NOT NULL DEFAULT 'journal' COMMENT 'journal or note',
  `cal_href` varchar(511) NOT NULL DEFAULT '',
  `cal_data` BLOB,
  `cal_etag` varchar(511) NOT NULL DEFAULT '',
  `cal_dtstamp`  varchar(190) NOT NULL DEFAULT '',
  `cal_uid` varchar(190) NOT NULL DEFAULT '',
  `cal_categories` varchar(190) NOT NULL DEFAULT '' COMMENT 'list, used as tags',
  `cal_class` varchar(190) NOT NULL DEFAULT '',
  `cal_created` varchar(190) NOT NULL DEFAULT '',
  `cal_description` varchar(190) NOT NULL DEFAULT '',
  `cal_dtstart` varchar(190) NOT NULL DEFAULT '',
  `cal_last_modified` varchar(190) NOT NULL DEFAULT '',
  `cal_organizer` varchar(190) NOT NULL DEFAULT '',
  `cal_recurid` varchar(190) NOT NULL DEFAULT '',
  `cal_seq` varchar(190) NOT NULL DEFAULT '',
  `cal_status` varchar(190) NOT NULL DEFAULT '',
  `cal_summary` varchar(190) NOT NULL DEFAULT '' COMMENT 'used for title with formating stripped',
  `cal_url` varchar(190) NOT NULL DEFAULT '',
  `cal_rrule` varchar(190) NOT NULL DEFAULT '',
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `description` text NOT NULL DEFAULT '',
  `catid` int(10) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `modified` datetime,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` mediumtext,
   PRIMARY KEY (`id`)
  )  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# CREATE UNIQUE INDEX `entryaliasindex` ON `#__xbjournals_vjournal_entries` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbjournals_vjournal_items` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) NOT NULL DEFAULT '0',
  `cal_attach` varchar(190) NOT NULL DEFAULT '',
  `cal_attendee` varchar(190) NOT NULL DEFAULT '',
  `cal_comment` varchar(190) NOT NULL DEFAULT '',
  `cal_contact` varchar(190) NOT NULL DEFAULT '',
  `cal_description` varchar(190) NOT NULL DEFAULT '',
  `cal_exdate` varchar(190) NOT NULL DEFAULT '',
  `cal_related` varchar(190) NOT NULL DEFAULT '',
  `cal_rdate` varchar(190) NOT NULL DEFAULT '',
  `cal_rstatus` varchar(190) NOT NULL DEFAULT '',
  `cal_x-prop` varchar(190) NOT NULL DEFAULT '',
  `cal_iana-prop` varchar(190) NOT NULL DEFAULT '',
   PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

