# v0.0.5.2 16th May 2023 add column for info in attachments
ALTER TABLE `#__xbjournals_vjournal_attachments` ADD `info` VARCHAR(1022) NOT NULL DEFAULT '' AFTER `otherparams`;