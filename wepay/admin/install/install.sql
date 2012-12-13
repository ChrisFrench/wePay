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
  `user_id` int(11) NOT NULL,
  `wepay_userid` int(11) DEFAULT NULL COMMENT 'The unique ID from wepay of the user that has granted you authorization',
  `wepay_account_id` int(11) DEFAULT NULL COMMENT 'The user''s account, which will hold they payments they receive\n',
  `wepay_account_uri` varchar(255) DEFAULT NULL COMMENT 'A uri that corresponds to the account''s page on WePay.',
  `wepay_access_token` varchar(255) DEFAULT NULL COMMENT 'The token that allows you to make calls on behalf of that user',
  `wepay_token_type` varchar(255) DEFAULT 'BEARER' COMMENT 'For now, this will always be BEARER',
  `wepay_expires_in` datetime DEFAULT NULL COMMENT 'When the token will expire. If not present, the token will not expire automatically.',
  `oauth_code` varchar(255) DEFAULT NULL,
  `wepay_name` varchar(255) DEFAULT NULL COMMENT 'What you would like to name the account. (Note: This appears on people''s credit card statements, so we suggest using the name of the person or business accepting payments)',
  `wepay_description` varchar(255) DEFAULT NULL COMMENT 'The description or purpose of the account you''re creating.',
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `datecreated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
