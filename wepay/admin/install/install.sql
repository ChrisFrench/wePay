-- -----------------------------------------------------
-- Table `#__wepay_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__wepay_config` (
  `config_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `config_name` VARCHAR(255) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`config_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;
-- -----------------------------------------------------
-- Table `#__wepay_accounts`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wepay_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `joomla_user_id` int(11) NOT NULL,
  `scope_id` int(11) NOT NULL,
  `wepay_account_id` int(11) DEFAULT NULL COMMENT 'The user''s account, which will hold they payments they receive\n',
  `wepay_account_uri` varchar(255) DEFAULT NULL COMMENT 'A uri that corresponds to the account''s page on WePay.',
  `wepay_name` varchar(255) DEFAULT NULL COMMENT 'What you would like to name the account. (Note: This appears on people''s credit card statements, so we suggest using the name of the person or business accepting payments)',
  `wepay_description` varchar(255) DEFAULT NULL COMMENT 'The description or purpose of the account you''re creating.',
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `datecreated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8;

-- ----------------------------
--  Table structure for `#__wepay_users`
-- ----------------------------

CREATE TABLE IF NOT EXISTS `#__wepay_users` (
  `id` int(11) DEFAULT NULL,
  `joomla_user_id` int(11) DEFAULT NULL,
  `wepay_userid` int(11) DEFAULT NULL COMMENT 'The unique ID from wepay of the user that has granted you authorization',
  `wepay_access_token` varchar(255) DEFAULT NULL COMMENT 'The token that allows you to make calls on behalf of that user',
  `wepay_token_type` varchar(255) DEFAULT 'BEARER' COMMENT 'For now, this will always be BEARER',
  `wepay_expires_in` datetime DEFAULT NULL COMMENT 'When the token will expire. If not present, the token will not expire automatically.',
  `datecreated` datetime DEFAULT NULL,
  `oauth_code` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8;


-- --------------------------------------------------------
-- Table structure for table `#__wepay_scopes`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `#__wepay_scopes` (
  `scope_id` int(11) NOT NULL AUTO_INCREMENT,
  `scope_name` varchar(255) NOT NULL COMMENT 'Plain English name for the scope',
  `scope_identifier` varchar(255) NOT NULL COMMENT 'String unique ID for the scope',
  `scope_url` varchar(255) NOT NULL COMMENT 'URL for the scope item',
  `scope_table` varchar(255) NOT NULL COMMENT 'The DB table to perform the JOIN',
  `scope_table_field` varchar(255) NOT NULL COMMENT 'The DB table field to use for the JOIN',
  `scope_table_name_field` varchar(255) NOT NULL COMMENT 'The DB table field to use for the item name',
  `scope_params` text NOT NULL COMMENT 'JSON-encoded object with any other information you want to store about the scope',
  PRIMARY KEY (`scope_id`),
  KEY `scope_identifier` (`scope_identifier`)
) 
ENGINE=MyISAM  
DEFAULT CHARSET=utf8;