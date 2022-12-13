# DROP TABLE IF EXISTS `#__xbfilms`, `#__xbfilmperson`, `#__xbfilmcharacter`, `#__xbfilmreviews`;

DELETE FROM `#__ucm_history` WHERE ucm_type_id in 
	(select type_id from `#__content_types` where type_alias in ('com_xbfilms.film','com_xbfilms.person','com_xbfilms.character','com_xbfilms.review','com_xbfilms.category'));
DELETE FROM `#__ucm_base` WHERE ucm_type_id in 
	(select type_id from `#__content_types` WHERE type_alias in ('com_xbfilms.film','com_xbfilms.person','com_xbfilms.character','com_xbfilms.review','com_xbfilms.category'));
DELETE FROM `#__ucm_content` WHERE core_type_alias in ('com_xbfilms.film','com_xbfilms.person','com_xbfilms.character','com_xbfilms.review','com_xbfilms.category');
DELETE FROM `#__contentitem_tag_map`WHERE type_alias in ('com_xbfilms.film','com_xbfilms.person','com_xbfilms.character','com_xbfilms.review','com_xbfilms.category');
DELETE FROM `#__content_types` WHERE type_alias in ('com_xbfilms.film','com_xbfilms.person','com_xbfilms.character','com_xbfilms.review','com_xbfilms.category');
