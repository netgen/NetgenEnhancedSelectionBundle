CREATE TABLE `sckenhancedselection` (
  `contentobject_attribute_id` int(11) NOT NULL default '0',
  `contentobject_attribute_version` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  KEY `sckenhancedselection_coaid_coav` ( `contentobject_attribute_id`, `contentobject_attribute_version` ),
  KEY `sckenhancedselection_coaid_coav_iden` ( `contentobject_attribute_id`, `contentobject_attribute_version`, `identifier` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
