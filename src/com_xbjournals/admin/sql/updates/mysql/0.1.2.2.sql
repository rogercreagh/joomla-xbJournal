# v0.1.2.2 20th July 2023 add column for last_checked in entries and servers
ALTER TABLE `#__xbjournals_vjournal_entries` ADD `last_checked` datetime AFTER `otherprops`;
ALTER TABLE `#__xbjournals_servers` ADD `updated` datetime AFTER `password`;