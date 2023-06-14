# v0.0.5.2 16th May 2023 add column for info in attachments
ALTER TABLE `#__xbjournals_calendars` ADD `readonly` tinyint(3) NOT NULL DEFAULT '0' AFTER `catid`;