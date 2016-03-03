-- MySQL Script generated by MySQL Workbench
-- Čt 3. březen 2016, 18:59:59 CET
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema prometheus
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema prometheus
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `prometheus` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `prometheus` ;

-- -----------------------------------------------------
-- Table `prometheus`.`player`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`player` (
  `player_id` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(255) NOT NULL,
  `hash` CHAR(60) NOT NULL,
  `token` CHAR(60) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `gender` ENUM('M','F') NOT NULL,
  `osloveni` VARCHAR(255) NOT NULL,
  `race` VARCHAR(45) NOT NULL,
  `last_active` TIMESTAMP NULL,
  `last_login` TIMESTAMP NULL,
  `stuck` TINYINT NOT NULL DEFAULT 0,
  `won` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`player_id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  UNIQUE INDEX `token_UNIQUE` (`token` ASC),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`log` (
  `log_id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NULL,
  `message` TEXT NOT NULL,
  `inserted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  INDEX `fk_log_1_idx` (`player_id` ASC),
  CONSTRAINT `fk_log_1`
    FOREIGN KEY (`player_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`chat_private`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`chat_private` (
  `chat_private_id` INT NOT NULL AUTO_INCREMENT,
  `sender_id` INT NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `inserted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_private_id`),
  INDEX `fk_chat_private_1_idx` (`sender_id` ASC),
  CONSTRAINT `fk_chat_private_1`
    FOREIGN KEY (`sender_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`receiver`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`receiver` (
  `receiver_id` INT NOT NULL AUTO_INCREMENT,
  `chat_private_id` INT NOT NULL,
  `player_id` INT NOT NULL,
  `sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`receiver_id`),
  INDEX `fk_receiver_1_idx` (`chat_private_id` ASC),
  INDEX `fk_receiver_2_idx` (`player_id` ASC),
  CONSTRAINT `fk_receiver_1`
    FOREIGN KEY (`chat_private_id`)
    REFERENCES `prometheus`.`chat_private` (`chat_private_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_receiver_2`
    FOREIGN KEY (`player_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`chat_broadcast`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`chat_broadcast` (
  `chat_broadcast_id` INT NOT NULL AUTO_INCREMENT,
  `sender_id` INT NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `inserted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_broadcast_id`),
  INDEX `fk_chat_broadcast_1_idx` (`sender_id` ASC),
  CONSTRAINT `fk_chat_broadcast_1`
    FOREIGN KEY (`sender_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`answer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`answer` (
  `answer_id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NOT NULL,
  `answer` VARCHAR(255) NOT NULL,
  `inserted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`answer_id`),
  INDEX `fk_answer_1_idx` (`player_id` ASC),
  CONSTRAINT `fk_answer_1`
    FOREIGN KEY (`player_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


-- -----------------------------------------------------
-- Table `prometheus`.`visit`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `prometheus`.`visit` (
  `visit_id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NOT NULL,
  `table_id` INT NOT NULL,
  `type` ENUM('enter','exit') NOT NULL,
  `distance` ENUM('see','read') NOT NULL,
  `inserted` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`visit_id`),
  INDEX `fk_visit_1_idx` (`player_id` ASC),
  CONSTRAINT `fk_visit_1`
    FOREIGN KEY (`player_id`)
    REFERENCES `prometheus`.`player` (`player_id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
