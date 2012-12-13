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