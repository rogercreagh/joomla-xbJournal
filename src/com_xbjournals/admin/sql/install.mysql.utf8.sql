# sql installation file for component xbJournals 12th December 2022

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`, `table`, `field_mappings`, `router`,`rules`) 
VALUES
('XbJournals Journal', 'com_xbjournals.journal', 
'{"formFile":"administrator\\/components\\/com_xbjournals\\/models\\/forms\\/journal.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbjournals","key":"id","type":"Journal","prefix":"XbjournalsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "displayname",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "description",
    "core_catid": "catid"
  }}',
'XbjournalsHelperRoute::getJournalRoute',''),


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
    "core_title": "summary",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "styled-description",
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

CREATE TABLE IF NOT EXISTS `#__xbjournals` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `displayname` varchar(190) NOT NULL DEFAULT '',
  `url` varchar(190) NOT NULL DEFAULT '',
  `ctag`
  `calandar_id`
  `rgba_color`
  `rgb_color`
  `order`
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `catid` int(10) NOT NULL  DEFAULT '0',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
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

CREATE TABLE IF NOT EXISTS `#__xbjournalentries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dtstamp`
  `uid`
  `sequence`
  `created`
  `last_modified`
  `summary`
  `styled-description`
  `class`
  `status`
  `etag`
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
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

# CREATE UNIQUE INDEX `entryaliasindex` ON `#__xbjournalentries` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbentryitems` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
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

