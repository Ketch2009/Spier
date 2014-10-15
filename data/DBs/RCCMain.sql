SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `RCCMain` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `RCCMain` ;

-- -----------------------------------------------------
-- Table `RCCMain`.`Customers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Customers` (
  `idCustomer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `RCCPass` VARBINARY(45) NULL,
  `idLkSalutation` INT UNSIGNED NULL,
  `FirstName` VARCHAR(20) NULL,
  `LastName` VARCHAR(20) NULL,
  `boptGender` VARCHAR(1) NULL,
  `idLkWebCountry` INT UNSIGNED NULL,
  `DOB` DATE NULL,
  `flgHideHelpBtn` VARCHAR(1) NULL,
  `flgAgreedTcCs` VARCHAR(1) NULL,
  `WhenAgreedTcCs` DATETIME NULL,
  `flgRememberMe` VARCHAR(1) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  `CurrentBalance` FLOAT NULL,
  PRIMARY KEY (`idCustomer`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = big5;


-- -----------------------------------------------------
-- Table `RCCMain`.`CarerPlan`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`CarerPlan` (
  `idCarerPlan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `PlanName` VARCHAR(45) NULL,
  `flgFixedChgPerMonth` VARCHAR(1) NULL,
  `PerMonthFixed` FLOAT UNSIGNED NULL,
  `flgChargePerBookingP` VARCHAR(1) NULL,
  `BookingPercent` FLOAT UNSIGNED NULL,
  `flgChargePerBookingF` VARCHAR(1) NULL,
  `BookingFixed` FLOAT UNSIGNED NULL,
  `flgIncludeSMS` VARCHAR(1) NULL,
  `flgIncludeEmail` VARCHAR(1) NULL,
  `flgIncludeCheckIn` VARCHAR(1) NULL,
  `flgIncludeCustRecords` VARCHAR(1) NULL,
  `FreeBookingsPerMonth` TINYINT UNSIGNED NULL,
  `flgLimitClinicCount` VARCHAR(1) NULL,
  `ClinicLimit` TINYINT UNSIGNED NULL,
  `flgEnglishOnly` VARCHAR(1) NULL,
  PRIMARY KEY (`idCarerPlan`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Carers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Carers` (
  `idCarer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idParent` INT UNSIGNED NULL,
  `idCarerPlanMonthly` INT UNSIGNED NULL,
  `RCCPass` VARBINARY(45) NULL,
  `idLkSalutation` INT UNSIGNED NULL,
  `FirstName` VARCHAR(20) NULL,
  `LastName` VARCHAR(20) NULL,
  `StatusStage` VARCHAR(25) NULL,
  `MedRegNo` VARCHAR(45) NULL,
  `CustomURL` VARCHAR(45) NULL,
  `flgHideHelpBtn` VARCHAR(1) NULL,
  `flgRememberMe` VARCHAR(1) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  `flgAgreedTcCs` VARCHAR(1) NULL,
  `WhenAgreed` DATETIME NULL,
  `flgAllowInstantBooking` VARCHAR(1) NULL,
  `MyStatement` MEDIUMTEXT NULL,
  `CalcedOverallRating` TINYINT UNSIGNED NULL,
  `CalcedWaitRating` TINYINT UNSIGNED NULL,
  `ExpiryOfTrial` DATE NULL,
  `flgVettingDone` VARCHAR(1) NULL,
  `flgPatientMan` VARCHAR(1) NULL,
  `flgPaymentMan` VARCHAR(1) NULL,
  `flgDashBMan` VARCHAR(1) NULL,
  PRIMARY KEY (`idCarer`),
  INDEX `fk_Carers_1_idx` (`idCarerPlanMonthly` ASC),
  CONSTRAINT `fk_Carers_1`
    FOREIGN KEY (`idCarerPlanMonthly`)
    REFERENCES `RCCMain`.`CarerPlan` (`idCarerPlan`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Lookups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Lookups` (
  `idLookup` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Type` VARCHAR(6) NULL,
  `LkVal` VARCHAR(10) NULL,
  `LkDesc` VARCHAR(200) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  PRIMARY KEY (`idLookup`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`ContactMethods`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`ContactMethods` (
  `idContactMethod` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `optContactFor` VARCHAR(1) NULL,
  `idLinkTo` INT UNSIGNED NULL,
  `optCMethod` VARCHAR(1) NULL,
  `MethInfo` VARCHAR(100) NULL,
  `flgSyncCal` VARCHAR(1) NULL,
  `flgConfirmed` VARCHAR(1) NULL,
  `VerConfCode` VARCHAR(5) NULL,
  `flgUseAsDefault` VARCHAR(1) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  PRIMARY KEY (`idContactMethod`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Appointment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Appointment` (
  `idCAppointment` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `WhenDay` DATE NULL,
  `flgSetAsUnavailableForWork` VARCHAR(1) NULL,
  `StartTime` TIME NULL,
  `EndTime` TIME NULL,
  `idCustomer` INT UNSIGNED NULL,
  `flgAppntSentToCust` VARCHAR(1) NULL,
  `flgAppntSentToCarer` VARCHAR(1) NULL,
  `ExternalSyncID` VARCHAR(45) NULL,
  `WhenSynced` DATETIME NULL,
  `flgSyncComplete` VARCHAR(1) NULL,
  `flgCarerSeen` VARCHAR(1) NULL,
  `optCarerAccepted` VARCHAR(1) NULL,
  PRIMARY KEY (`idCAppointment`),
  INDEX `fk_Calendar_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_Calendar_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Countries`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Countries` (
  `idCountry` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Description` VARCHAR(20) NULL,
  `BrowserDef` VARCHAR(6) NULL,
  `ShortDef` VARCHAR(2) NULL,
  `optLTRorRTL` VARCHAR(1) NULL,
  `idLkCurrency` INT UNSIGNED NULL,
  `optMetricImperial` VARCHAR(1) NULL,
  `idLkLanguage` INT UNSIGNED NULL,
  PRIMARY KEY (`idCountry`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Businesses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Businesses` (
  `idBusiness` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `BusinessName` VARCHAR(45) NULL,
  `BusinessSttmt` MEDIUMTEXT NULL,
  `ManagementSoft` VARCHAR(45) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  PRIMARY KEY (`idBusiness`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Ratings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Ratings` (
  `idRating` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `OverallRating` TINYINT UNSIGNED NULL,
  `WaitTimeRating` TINYINT UNSIGNED NULL,
  `Comment` VARCHAR(200) NULL,
  `idCarer` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  PRIMARY KEY (`idRating`),
  INDEX `fk_Ratings_1_idx` (`idCarer` ASC),
  INDEX `fk_Ratings_2_idx` (`idCustomer` ASC),
  CONSTRAINT `fk_Ratings_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ratings_2`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`LanguageLink`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`LanguageLink` (
  `idLkLangauge` INT UNSIGNED NOT NULL,
  `idCarer` INT UNSIGNED NOT NULL,
  `optLevel` TINYINT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  PRIMARY KEY (`idLkLangauge`, `idCarer`),
  INDEX `fk_LanguageLink_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_LanguageLink_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Addresses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Addresses` (
  `idAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `optContactFor` VARCHAR(1) NULL,
  `idLinkTo` INT UNSIGNED NULL,
  `Line1` VARCHAR(45) NULL,
  `Line2` VARCHAR(45) NULL,
  `Area` VARCHAR(45) NULL,
  `TownOrCity` VARCHAR(45) NULL,
  `PostalCode` VARCHAR(7) NULL,
  `idCountry` INT UNSIGNED NULL,
  PRIMARY KEY (`idAddress`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`CarerBusinessJoin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`CarerBusinessJoin` (
  `idCarerBusinessJoin` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idBusiness` INT UNSIGNED NULL,
  `idCarer` INT UNSIGNED NULL,
  `flgDefaultForBilling` VARCHAR(1) NULL,
  `idLkJobTitle` VARCHAR(45) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  PRIMARY KEY (`idCarerBusinessJoin`),
  INDEX `fk_CarerPracticeJoin_1_idx` (`idCarer` ASC),
  INDEX `fk_CarerPracticeJoin_2_idx` (`idBusiness` ASC),
  CONSTRAINT `fk_CarerPracticeJoin_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CarerPracticeJoin_2`
    FOREIGN KEY (`idBusiness`)
    REFERENCES `RCCMain`.`Businesses` (`idBusiness`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`InsuranceCos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`InsuranceCos` (
  `idInsuranceCo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `InsuranceName` VARCHAR(45) NULL,
  PRIMARY KEY (`idInsuranceCo`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`ProfileViews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`ProfileViews` (
  `idProfileViews` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `WhenViewed` DATETIME NULL,
  `flgSeenByCarer` VARCHAR(1) NULL,
  PRIMARY KEY (`idProfileViews`),
  INDEX `fk_ProfileViews_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_ProfileViews_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`SessionsHistory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`SessionsHistory` (
  `idSessionHistory` VARCHAR(45) NOT NULL,
  `Session` VARCHAR(30) NOT NULL,
  `optSessFor` VARCHAR(1) NULL,
  `idLinkTo` INT UNSIGNED NULL,
  `SessStart` DATETIME NOT NULL,
  `LastAJAX` DATETIME NULL,
  `LastPage` VARCHAR(45) NULL,
  `RemoteIPV4` INT UNSIGNED NULL,
  `latitude` VARCHAR(11) NULL,
  `longitude` VARCHAR(11) NULL,
  `idLkCountry` INT UNSIGNED NULL,
  PRIMARY KEY (`idSessionHistory`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`InsuranceLink`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`InsuranceLink` (
  `idInsuranceLink` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idInsuranceCo` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `RefNumber` VARCHAR(45) NULL,
  `PlanName` VARCHAR(45) NULL,
  `Expiry` DATE NULL,
  `WhenAdded` DATETIME NULL,
  `flgDeleted` VARCHAR(1) NULL,
  PRIMARY KEY (`idInsuranceLink`),
  INDEX `fk_InsuranceLink_1_idx` (`idCustomer` ASC),
  INDEX `fk_InsuranceLink_2_idx` (`idInsuranceCo` ASC),
  CONSTRAINT `fk_InsuranceLink_1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_InsuranceLink_2`
    FOREIGN KEY (`idInsuranceCo`)
    REFERENCES `RCCMain`.`InsuranceCos` (`idInsuranceCo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Endorsements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Endorsements` (
  `idEndorsement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idFromCarer` INT UNSIGNED NULL,
  `idToCarer` INT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  PRIMARY KEY (`idEndorsement`),
  INDEX `fk_Endorsements_1_idx` (`idFromCarer` ASC),
  INDEX `fk_Endorsements_2_idx` (`idToCarer` ASC),
  CONSTRAINT `fk_Endorsements_1`
    FOREIGN KEY (`idFromCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Endorsements_2`
    FOREIGN KEY (`idToCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`CarerMessages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`CarerMessages` (
  `idCarerMessage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `idLkLangauge` INT UNSIGNED NULL,
  `MsgPurpose` VARCHAR(45) NULL,
  `MsgSubject` VARCHAR(45) NULL,
  `MsgBody` MEDIUMTEXT NULL,
  `flgUseForSMS` VARCHAR(1) NULL,
  `flgUseForeMail` VARCHAR(1) NULL,
  PRIMARY KEY (`idCarerMessage`),
  INDEX `fk_CarerMessages_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_CarerMessages_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`MsgSendHistory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`MsgSendHistory` (
  `idMsgSendHistory` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarerMessage` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `idAppointment` INT UNSIGNED NULL,
  `WhenSent` DATETIME NULL,
  `flgSeenByCustomer` VARCHAR(1) NULL,
  `FailCount` TINYINT UNSIGNED NULL,
  PRIMARY KEY (`idMsgSendHistory`),
  INDEX `fk_MsgSendHistory_1_idx` (`idCarerMessage` ASC),
  INDEX `fk_MsgSendHistory_2_idx` (`idCustomer` ASC),
  INDEX `fk_MsgSendHistory_3_idx` (`idAppointment` ASC),
  CONSTRAINT `fk_MsgSendHistory_1`
    FOREIGN KEY (`idCarerMessage`)
    REFERENCES `RCCMain`.`CarerMessages` (`idCarerMessage`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_MsgSendHistory_2`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_MsgSendHistory_3`
    FOREIGN KEY (`idAppointment`)
    REFERENCES `RCCMain`.`Appointment` (`idCAppointment`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`QualsAndMemberships`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`QualsAndMemberships` (
  `idQualsAndMembership` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `idLkName` INT UNSIGNED NULL,
  `boptQualificationOrMembership` VARCHAR(1) NULL,
  `DateAchieved` DATE NULL,
  `idLkTreatment` INT UNSIGNED NULL,
  `RefNumber` VARCHAR(45) NULL,
  PRIMARY KEY (`idQualsAndMembership`),
  INDEX `fk_QualsAndMemberships_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_QualsAndMemberships_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`MessageDefaults`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`MessageDefaults` (
  `idMessageDefaults` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idLkLanguage` INT UNSIGNED NULL,
  `MsgPurpose` VARCHAR(45) NULL,
  `MsgSubject` VARCHAR(45) NULL,
  `MsgBody` MEDIUMTEXT NULL,
  `flgUseForSMS` VARCHAR(1) NULL,
  `flgUseForeMail` VARCHAR(1) NULL,
  PRIMARY KEY (`idMessageDefaults`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`AllAttachments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`AllAttachments` (
  `idAllAttachments` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `AttachType` VARCHAR(1) NULL,
  `TableAttachTo` VARCHAR(15) NULL,
  `idTableRecord` INT UNSIGNED NULL,
  `OrigNameOrLink` VARCHAR(255) NULL,
  `StoredAs` VARCHAR(45) NULL,
  `flgDeleted` VARCHAR(1) NULL,
  `CreatedOn` DATETIME NULL,
  PRIMARY KEY (`idAllAttachments`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`AffiliatedHospitals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`AffiliatedHospitals` (
  `idAffiliatedHospital` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `idLkName` INT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  PRIMARY KEY (`idAffiliatedHospital`),
  INDEX `fk_AffiliatedHospitals_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_AffiliatedHospitals_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`CarerAccount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`CarerAccount` (
  `idCarerAccount` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `CurrentBalance` FLOAT NULL,
  `flgEOMRun` VARCHAR(1) NULL,
  PRIMARY KEY (`idCarerAccount`),
  INDEX `fk_CarerAccount_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_CarerAccount_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`AccountTrns`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`AccountTrns` (
  `idAccountTrn` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarerAccount` INT UNSIGNED NULL,
  `TrnValue` FLOAT NULL,
  `WhenAdded` DATETIME NULL,
  `Description` VARCHAR(200) NULL,
  PRIMARY KEY (`idAccountTrn`),
  INDEX `fk_AccountTrns_1_idx` (`idCarerAccount` ASC),
  CONSTRAINT `fk_AccountTrns_1`
    FOREIGN KEY (`idCarerAccount`)
    REFERENCES `RCCMain`.`CarerAccount` (`idCarerAccount`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`PayMethods`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`PayMethods` (
  `idPayMethods` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `boptPayMethod` VARCHAR(1) NULL,
  `boptCardType` VARCHAR(1) NULL,
  `CardNumber` VARBINARY(30) NULL,
  `NameOnCard` VARBINARY(120) NULL,
  `DateFrom` VARBINARY(10) NULL,
  `DateExp` VARBINARY(10) NULL,
  `CSC` VARBINARY(6) NULL,
  `EmailAddress` VARBINARY(120) NULL,
  `PasswordOpt` VARCHAR(45) NULL,
  PRIMARY KEY (`idPayMethods`),
  INDEX `fk_PayMethods_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_PayMethods_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`WorkSchedules`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`WorkSchedules` (
  `idWorkSchedule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idBusiness` INT UNSIGNED NULL,
  `idCarer` INT UNSIGNED NULL,
  `ScheduleName` VARCHAR(45) NULL,
  `boptDaysOfWeek` VARCHAR(7) NULL,
  `TimeStart` TIME NULL,
  `TimeEnd` TIME NULL,
  PRIMARY KEY (`idWorkSchedule`),
  INDEX `fk_WorkSchedules_1_idx` (`idCarer` ASC),
  INDEX `fk_WorkSchedules_2_idx` (`idBusiness` ASC),
  CONSTRAINT `fk_WorkSchedules_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_WorkSchedules_2`
    FOREIGN KEY (`idBusiness`)
    REFERENCES `RCCMain`.`Businesses` (`idBusiness`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`ServicesOffered`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`ServicesOffered` (
  `idServicesOffered` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idWorkSchedule` INT UNSIGNED NULL,
  `idLkServiceName` INT UNSIGNED NULL,
  `idLkCategory` INT UNSIGNED NULL,
  `flgPriceAdvertised` VARCHAR(1) NULL,
  `idLkCurrency` INT UNSIGNED NULL,
  `InitialCharge` FLOAT UNSIGNED NULL,
  `flgVariableCharging` VARCHAR(1) NULL,
  `flgChargeTime` VARCHAR(1) NULL,
  `InitialTimeMins` SMALLINT UNSIGNED NULL,
  `flgChargeTravel` VARCHAR(1) NULL,
  `InitialDistance` SMALLINT UNSIGNED NULL,
  `flgInitTimeOrDistance` VARCHAR(1) NULL,
  `flgAdditionalTime` VARCHAR(1) NULL,
  `AdditionalTimeSlice` SMALLINT UNSIGNED NULL,
  `AdditionalTimeCharge` FLOAT UNSIGNED NULL,
  `flgAdditionalDistance` VARCHAR(1) NULL,
  `AdditionalDistanceSlice` SMALLINT UNSIGNED NULL,
  `AdditionalDistanceCharge` FLOAT UNSIGNED NULL,
  `flgAddnlTimeOrDistance` VARCHAR(1) NULL,
  `CountServicesBooked` SMALLINT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  PRIMARY KEY (`idServicesOffered`),
  INDEX `fk_ServicesOffered_1_idx` (`idWorkSchedule` ASC),
  CONSTRAINT `fk_ServicesOffered_1`
    FOREIGN KEY (`idWorkSchedule`)
    REFERENCES `RCCMain`.`WorkSchedules` (`idWorkSchedule`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`AvailableExtras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`AvailableExtras` (
  `idAvailableExtras` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idServicesOffered` INT UNSIGNED NULL,
  `idLkExtraName` INT UNSIGNED NULL,
  `InitialCharge` FLOAT UNSIGNED NULL,
  PRIMARY KEY (`idAvailableExtras`),
  INDEX `fk_AvailableExtras_1_idx` (`idServicesOffered` ASC),
  CONSTRAINT `fk_AvailableExtras_1`
    FOREIGN KEY (`idServicesOffered`)
    REFERENCES `RCCMain`.`ServicesOffered` (`idServicesOffered`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`Consultants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Consultants` (
  `idConsultant` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `ConsultantName` VARCHAR(45) NULL,
  `DefaultShare` FLOAT UNSIGNED NULL,
  PRIMARY KEY (`idConsultant`),
  INDEX `fk_Consultants_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_Consultants_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RCCMain`.`Prescriptions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Prescriptions` (
  `idPrescription` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `Description` VARCHAR(60) NULL,
  `Dosage` VARCHAR(45) NULL,
  `idLkUnits` INT UNSIGNED NULL,
  `flgMorning` VARCHAR(1) NULL,
  `flgAfternoon` VARCHAR(1) NULL,
  `flgNight` VARCHAR(1) NULL,
  `Duration` VARCHAR(10) NULL,
  `idLkDurationUnits` INT UNSIGNED NULL,
  `boptBeforeAfterMeal` VARCHAR(1) NULL,
  `Instructions` MEDIUMTEXT NULL,
  PRIMARY KEY (`idPrescription`),
  INDEX `fk_Prescriptions_1_idx` (`idCarer` ASC),
  CONSTRAINT `fk_Prescriptions_1`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RCCMain`.`Engagement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`Engagement` (
  `idEngagement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `flgIsEstimate` VARCHAR(1) NULL,
  `idAppointment` INT UNSIGNED NULL,
  `idCarer` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `idLkCurrency` INT UNSIGNED NULL,
  `BillTotal` FLOAT UNSIGNED NULL,
  PRIMARY KEY (`idEngagement`),
  INDEX `fk_ServicesHeader_1_idx` (`idCustomer` ASC),
  INDEX `fk_ServicesHeader_2_idx` (`idCarer` ASC),
  CONSTRAINT `fk_ServicesHeader_1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ServicesHeader_2`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = dec8;


-- -----------------------------------------------------
-- Table `RCCMain`.`EngagementDetails`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`EngagementDetails` (
  `idEngagementDetail` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCostsHeader` INT UNSIGNED NULL,
  `idPrescription` INT UNSIGNED NULL,
  `idConsultant` INT UNSIGNED NULL,
  `idServicesOffered` INT UNSIGNED NULL,
  `ItemPrice` FLOAT UNSIGNED NULL,
  `Discount` FLOAT UNSIGNED NULL,
  `LineTotal` FLOAT UNSIGNED NULL,
  `JSONDetails` MEDIUMTEXT NULL,
  PRIMARY KEY (`idEngagementDetail`),
  INDEX `fk_CostDetails_1_idx` (`idCostsHeader` ASC),
  CONSTRAINT `fk_CostDetails_1`
    FOREIGN KEY (`idCostsHeader`)
    REFERENCES `RCCMain`.`Engagement` (`idEngagement`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `RCCMain`.`CustomerPayment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`CustomerPayment` (
  `idCustomerPayment` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCarer` INT UNSIGNED NULL,
  `idCustomer` INT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  `boptPayMethod` VARCHAR(1) NULL,
  `flgSyncChecked` VARCHAR(1) NULL,
  `ChequeNumber` VARCHAR(45) NULL,
  `Amount` FLOAT UNSIGNED NULL,
  `flgMovedToCustBalance` VARCHAR(1) NULL,
  PRIMARY KEY (`idCustomerPayment`),
  INDEX `fk_CustomerPayment_1_idx` (`idCustomer` ASC),
  INDEX `fk_CustomerPayment_2_idx` (`idCarer` ASC),
  CONSTRAINT `fk_CustomerPayment_1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CustomerPayment_2`
    FOREIGN KEY (`idCarer`)
    REFERENCES `RCCMain`.`Carers` (`idCarer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RCCMain`.`TicketHeaders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`TicketHeaders` (
  `idTicketHeader` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCustomer` INT UNSIGNED NULL,
  `optAddedByCustOrSupport` VARCHAR(1) NULL,
  `WhenAdded` DATETIME NULL,
  `optAwaitingCorS` VARCHAR(1) NULL,
  `TicketSubject` VARCHAR(100) NULL,
  `Status` TINYINT UNSIGNED NULL,
  `boptPriority` VARCHAR(1) NULL,
  PRIMARY KEY (`idTicketHeader`),
  INDEX `fk_TicketHeader_1_idx` (`idCustomer` ASC),
  CONSTRAINT `fk_TicketHeader_1`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RCCMain`.`TicketDetails`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`TicketDetails` (
  `idTicketDetail` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idTicketHeader` INT UNSIGNED NULL,
  `WhenAdded` DATETIME NULL,
  `flgAddedByCustomer` VARCHAR(1) NULL,
  `ItemDetails` MEDIUMTEXT NULL,
  PRIMARY KEY (`idTicketDetail`),
  INDEX `fk_TicketDetails_1_idx` (`idTicketHeader` ASC),
  CONSTRAINT `fk_TicketDetails_1`
    FOREIGN KEY (`idTicketHeader`)
    REFERENCES `RCCMain`.`TicketHeaders` (`idTicketHeader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `RCCMain`.`TicketSeenBy`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `RCCMain`.`TicketSeenBy` (
  `idTicketSeenBy` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idTicketDetail` INT UNSIGNED NULL,
  `LastView` DATETIME NULL,
  `idCustomer` INT UNSIGNED NULL,
  PRIMARY KEY (`idTicketSeenBy`),
  INDEX `fk_TicketSeenBy_1_idx` (`idTicketDetail` ASC),
  INDEX `fk_TicketSeenBy_2_idx` (`idCustomer` ASC),
  CONSTRAINT `fk_TicketSeenBy_1`
    FOREIGN KEY (`idTicketDetail`)
    REFERENCES `RCCMain`.`TicketDetails` (`idTicketDetail`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TicketSeenBy_2`
    FOREIGN KEY (`idCustomer`)
    REFERENCES `RCCMain`.`Customers` (`idCustomer`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
