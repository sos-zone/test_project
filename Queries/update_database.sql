USE importTest;

ALTER TABLE `tblProductData`
ADD `strProductStock` INT UNSIGNED NOT NULL AFTER `stmTimestamp`,
ADD `strProductCost` DECIMAL UNSIGNED NOT NULL AFTER `strProductStock`,
ADD `strProductDiscontinued` TINYINT AFTER `dtmDiscontinued`;
