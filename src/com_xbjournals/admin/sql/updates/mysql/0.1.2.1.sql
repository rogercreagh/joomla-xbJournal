# v0.1.2.1 19th July 2023 add column for html_desc in entries
ALTER TABLE `#__xbjournals_vjournal_entries` ADD `html_desc` text DEFAULT '' AFTER `description`;