-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema patata
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `patata` ;

-- -----------------------------------------------------
-- Schema patata
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `patata` ;
USE `patata` ;

-- -----------------------------------------------------
-- Table `personas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `personas` ;

CREATE TABLE IF NOT EXISTS `personas` (
  `pers_id` INT NOT NULL AUTO_INCREMENT,
  `pers_nombres` VARCHAR(128) NOT NULL,
  `pers_apellidos` VARCHAR(128) NOT NULL,
  `pers_documento` VARCHAR(16) NOT NULL,
  `pers_email` VARCHAR(64) NULL,
  `pers_telefono` VARCHAR(16) NULL,
  `pers_direccion` VARCHAR(128) NULL,
  PRIMARY KEY (`pers_id`),
  UNIQUE INDEX `pers_documento_UNIQUE` (`pers_documento` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuarios` ;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `usua_id` INT NOT NULL AUTO_INCREMENT,
  `usua_usuario` VARCHAR(16) NOT NULL,
  `usua_contrasenha` VARCHAR(64) NOT NULL,
  `usua_habilitado` TINYINT NOT NULL,
  `usua_tipo` ENUM('ADMINISTRADOR', 'OPERADOR') NOT NULL,
  `usua_pers_id` INT NOT NULL,
  PRIMARY KEY (`usua_id`),
  INDEX `fk_usuarios_personas_idx` (`usua_pers_id` ASC) VISIBLE,
  UNIQUE INDEX `usua_usuario_UNIQUE` (`usua_usuario` ASC) VISIBLE,
  CONSTRAINT `fk_usuarios_personas`
    FOREIGN KEY (`usua_pers_id`)
    REFERENCES `personas` (`pers_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `terminales`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `terminales` ;

CREATE TABLE IF NOT EXISTS `terminales` (
  `term_id` INT NOT NULL AUTO_INCREMENT,
  `term_nombre` VARCHAR(16) NOT NULL,
  `term_habilitado` TINYINT NOT NULL,
  PRIMARY KEY (`term_id`),
  UNIQUE INDEX `term_nombre_UNIQUE` (`term_nombre` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cajas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cajas` ;

CREATE TABLE IF NOT EXISTS `cajas` (
  `caja_id` INT NOT NULL AUTO_INCREMENT,
  `caja_apertura` DATETIME NOT NULL,
  `caja_cierre` DATETIME NULL,
  `caja_apertura_usua_id` INT NOT NULL,
  `caja_cierre_usua_id` INT NULL,
  `caja_term_id` INT NOT NULL,
  PRIMARY KEY (`caja_id`),
  INDEX `fk_cajas_usuarios1_idx` (`caja_apertura_usua_id` ASC) VISIBLE,
  INDEX `fk_cajas_usuarios2_idx` (`caja_cierre_usua_id` ASC) VISIBLE,
  INDEX `fk_cajas_terminales2_idx` (`caja_term_id` ASC) VISIBLE,
  CONSTRAINT `fk_cajas_usuarios1`
    FOREIGN KEY (`caja_apertura_usua_id`)
    REFERENCES `usuarios` (`usua_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cajas_usuarios2`
    FOREIGN KEY (`caja_cierre_usua_id`)
    REFERENCES `usuarios` (`usua_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cajas_terminales2`
    FOREIGN KEY (`caja_term_id`)
    REFERENCES `terminales` (`term_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `cuentas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cuentas` ;

CREATE TABLE IF NOT EXISTS `cuentas` (
  `cuen_id` INT NOT NULL AUTO_INCREMENT,
  `cuen_nombre` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`cuen_id`),
  UNIQUE INDEX `cuen_nombre_UNIQUE` (`cuen_nombre` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `movimientos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `movimientos` ;

CREATE TABLE IF NOT EXISTS `movimientos` (
  `movi_id` INT NOT NULL AUTO_INCREMENT,
  `movi_monto` INT NOT NULL,
  `movi_tipo` ENUM('INGRESO', 'EGRESO') NOT NULL,
  `movi_fecha` DATETIME NOT NULL,
  `movi_descripcion` VARCHAR(256) NULL,
  `movi_caja_id` INT NOT NULL,
  `movi_usua_id` INT NOT NULL,
  `movi_cuen_id` INT NOT NULL,
  PRIMARY KEY (`movi_id`),
  INDEX `fk_movimientos_cajas1_idx` (`movi_caja_id` ASC) VISIBLE,
  INDEX `fk_movimientos_usuarios1_idx` (`movi_usua_id` ASC) VISIBLE,
  INDEX `fk_movimientos_cuentas1_idx` (`movi_cuen_id` ASC) VISIBLE,
  CONSTRAINT `fk_movimientos_cajas1`
    FOREIGN KEY (`movi_caja_id`)
    REFERENCES `cajas` (`caja_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimientos_usuarios1`
    FOREIGN KEY (`movi_usua_id`)
    REFERENCES `usuarios` (`usua_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimientos_cuentas1`
    FOREIGN KEY (`movi_cuen_id`)
    REFERENCES `cuentas` (`cuen_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `saldos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `saldos` ;

CREATE TABLE IF NOT EXISTS `saldos` (
  `sald_id` INT NOT NULL AUTO_INCREMENT,
  `sald_inicial` INT NOT NULL,
  `sald_actual` INT NOT NULL,
  `sald_caja_id` INT NOT NULL,
  `sald_cuen_id` INT NOT NULL,
  PRIMARY KEY (`sald_id`),
  INDEX `fk_saldos_cajas1_idx` (`sald_caja_id` ASC) VISIBLE,
  INDEX `fk_saldos_cuentas1_idx` (`sald_cuen_id` ASC) VISIBLE,
  CONSTRAINT `fk_saldos_cajas1`
    FOREIGN KEY (`sald_caja_id`)
    REFERENCES `cajas` (`caja_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_saldos_cuentas1`
    FOREIGN KEY (`sald_cuen_id`)
    REFERENCES `cuentas` (`cuen_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `logs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `logs` ;

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` INT NOT NULL AUTO_INCREMENT,
  `logs_tabla` VARCHAR(16) NOT NULL,
  `logs_accion` VARCHAR(16) NOT NULL,
  `logs_registro` TEXT NULL,
  `logs_ip` VARCHAR(32) NULL,
  `logs_fecha` DATETIME NULL,
  `logs_usua_id` INT NOT NULL,
  PRIMARY KEY (`log_id`),
  INDEX `fk_logs_usuarios1_idx` (`logs_usua_id` ASC) VISIBLE,
  CONSTRAINT `fk_logs_usuarios1`
    FOREIGN KEY (`logs_usua_id`)
    REFERENCES `usuarios` (`usua_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `personas`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `personas` (`pers_id`, `pers_nombres`, `pers_apellidos`, `pers_documento`, `pers_email`, `pers_telefono`, `pers_direccion`) VALUES (1, 'Cosme', 'Fulanito', '12345678', NULL, NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `usuarios`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `usuarios` (`usua_id`, `usua_usuario`, `usua_contrasenha`, `usua_habilitado`, `usua_tipo`, `usua_pers_id`) VALUES (1, 'admin', md5('admin'), 1, 'ADMINISTRADOR', 1);
INSERT INTO `usuarios` (`usua_id`, `usua_usuario`, `usua_contrasenha`, `usua_habilitado`, `usua_tipo`, `usua_pers_id`) VALUES (2, 'operador', md5('operador'), 1, 'OPERADOR', 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `terminales`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `terminales` (`term_id`, `term_nombre`, `term_habilitado`) VALUES (1, 'Caja 1', 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `cajas`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `cajas` (`caja_id`, `caja_apertura`, `caja_cierre`, `caja_apertura_usua_id`, `caja_cierre_usua_id`, `caja_term_id`) VALUES (1, CONVERT_TZ(NOW(), 'UTC', 'America/Lima'), NULL, 1, NULL, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `cuentas`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `cuentas` (`cuen_id`, `cuen_nombre`) VALUES (1, 'Efectivo');

COMMIT;


-- -----------------------------------------------------
-- Data for table `saldos`
-- -----------------------------------------------------
START TRANSACTION;
USE `patata`;
INSERT INTO `saldos` (`sald_id`, `sald_inicial`, `sald_actual`, `sald_caja_id`, `sald_cuen_id`) VALUES (1, 2000, 2000, 1, 1);

COMMIT;
