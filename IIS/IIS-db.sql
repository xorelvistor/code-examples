-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úte 10. pro 2013, 15:39
-- Verze MySQL: 5.6.14
-- Verze PHP: 5.3.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `xkonec55`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `oddeleni`
--

CREATE TABLE IF NOT EXISTS `oddeleni` (
  `kodOddeleni` char(3) COLLATE latin2_czech_cs NOT NULL,
  `nazev` varchar(80) COLLATE latin2_czech_cs DEFAULT NULL,
  `zamereni` varchar(80) COLLATE latin2_czech_cs DEFAULT NULL,
  PRIMARY KEY (`kodOddeleni`),
  UNIQUE KEY `nazev` (`nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

--
-- Vypisuji data pro tabulku `oddeleni`
--

INSERT INTO `oddeleni` (`kodOddeleni`, `nazev`, `zamereni`) VALUES
('NAZ', 'Nazehlovani', 'Nazehlovani tricek, kosil, dresu'),
('nez', 'nezarazen', 'bez zarazeni do oddeleni'),
('POL', 'Polepy', 'Polepy aut,reklam'),
('VYL', 'Vylohy', 'Realizace vyloh');

--
-- Spouště `oddeleni`
--
DROP TRIGGER IF EXISTS `smaz_oddeleni`;
DELIMITER //
CREATE TRIGGER `smaz_oddeleni` BEFORE DELETE ON `oddeleni`
 FOR EACH ROW BEGIN
DELETE FROM vedouci WHERE oddeleni_id=OLD.kodOddeleni;
UPDATE zamestnanec SET oddeleni_id='nez' WHERE oddeleni_id=OLD.kodOddeleni;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabulky `pristup`
--

CREATE TABLE IF NOT EXISTS `pristup` (
  `login` varchar(15) COLLATE latin2_czech_cs NOT NULL,
  `heslo` varchar(60) COLLATE latin2_czech_cs DEFAULT NULL,
  `role` varchar(15) COLLATE latin2_czech_cs DEFAULT NULL,
  PRIMARY KEY (`login`),
  UNIQUE KEY `heslo` (`heslo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

--
-- Vypisuji data pro tabulku `pristup`
--

INSERT INTO `pristup` (`login`, `heslo`, `role`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 'administrator'),
('1', 'ee11cbb19052e40b07aac0ca060c23ee', 'zamestnanec'),
('2', '7e3864761227abd208c855e2dc0b178d', 'zamestnanec'),
('23980497', 'b8a39481701dabd0805b252d1da45204', 'zakaznik'),
('3', '9b876c895710a52d01c84a884c2d8bce', 'zamestnanec'),
('39505987', '955db0b81ef1989b4a4dfeae8061a9a6', 'zakaznik'),
('4', '5de2526363f4935076e41da0b6f9a502', 'zamestnanec'),
('5', '75fa63c5c948eb94686461db076f4d10', 'zamestnanec'),
('52864112', '5cef8890f7f160a6023356b6a73637b6', 'zakaznik'),
('56732722', '221e652557f97676d62b3d786a488b54', 'zakaznik'),
('6', '5b4bfa6ce733e12b09e2a57e534712aa', 'zamestnanec'),
('7', '487852ddca69664960fe587da128f53a', 'zamestnanec'),
('8', '7fcd3d01db512814d00803b530b7fe5a', 'zamestnanec'),
('86415885', 'c76a16ab28ac5b7b8fd00f2944b3e89f', 'zakaznik'),
('9', '79337e8dcb03de02342b7cdb159b7789', 'zamestnanec');

-- --------------------------------------------------------

--
-- Struktura tabulky `resi`
--

CREATE TABLE IF NOT EXISTS `resi` (
  `zamestnanec_id` int(5) NOT NULL,
  `zakazka_id` int(10) NOT NULL,
  PRIMARY KEY (`zamestnanec_id`,`zakazka_id`),
  KEY `FK_Resi_Zakazka` (`zakazka_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

--
-- Vypisuji data pro tabulku `resi`
--

INSERT INTO `resi` (`zamestnanec_id`, `zakazka_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(4, 2),
(5, 3),
(1, 5),
(3, 5),
(2, 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `vedouci`
--

CREATE TABLE IF NOT EXISTS `vedouci` (
  `zamestnanec_id` int(5) NOT NULL,
  `oddeleni_id` char(3) COLLATE latin2_czech_cs NOT NULL,
  PRIMARY KEY (`zamestnanec_id`,`oddeleni_id`),
  UNIQUE KEY `zamestnanec_id` (`zamestnanec_id`),
  UNIQUE KEY `oddeleni_id` (`oddeleni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

--
-- Vypisuji data pro tabulku `vedouci`
--

INSERT INTO `vedouci` (`zamestnanec_id`, `oddeleni_id`) VALUES
(4, 'NAZ'),
(3, 'nez'),
(5, 'VYL');

-- --------------------------------------------------------

--
-- Struktura tabulky `vydaj`
--

CREATE TABLE IF NOT EXISTS `vydaj` (
  `cisloDokladu` int(8) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `castka` int(11) DEFAULT NULL,
  `popis` varchar(50) COLLATE latin2_czech_cs DEFAULT NULL,
  `zakazka_id` int(10) NOT NULL,
  PRIMARY KEY (`cisloDokladu`),
  KEY `FK_Vydaj_Zakazka` (`zakazka_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vydaj`
--

INSERT INTO `vydaj` (`cisloDokladu`, `datum`, `castka`, `popis`, `zakazka_id`) VALUES
(1, '2012-03-02', 27400, 'material', 1),
(2, '2012-03-08', 12500, 'material', 1),
(3, '2012-03-17', 9700, 'material', 2),
(4, '2012-03-22', 1550, 'material', 2),
(5, '2012-04-22', 11490, 'material', 3),
(6, '2012-03-12', 22600, 'material', 2),
(7, '2012-03-22', 33530, 'material', 2),
(8, '2012-04-12', 12300, 'material', 5),
(9, '2012-04-18', 8200, 'material', 5),
(10, '2012-04-17', 10820, 'material', 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `zadavatel`
--

CREATE TABLE IF NOT EXISTS `zadavatel` (
  `ic` int(8) NOT NULL,
  `nazev` varchar(80) COLLATE latin2_czech_cs DEFAULT NULL,
  `zkratka` varchar(5) COLLATE latin2_czech_cs DEFAULT NULL,
  `dic` char(10) COLLATE latin2_czech_cs DEFAULT NULL,
  `mesto` varchar(20) COLLATE latin2_czech_cs DEFAULT NULL,
  `ulice` varchar(20) COLLATE latin2_czech_cs DEFAULT NULL,
  `cp` varchar(10) COLLATE latin2_czech_cs DEFAULT NULL,
  `psc` int(5) DEFAULT NULL,
  `ucet` varchar(30) COLLATE latin2_czech_cs DEFAULT NULL,
  `kontakt` varchar(50) COLLATE latin2_czech_cs DEFAULT NULL,
  PRIMARY KEY (`ic`),
  UNIQUE KEY `nazev` (`nazev`),
  UNIQUE KEY `dic` (`dic`),
  UNIQUE KEY `kontakt` (`kontakt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

--
-- Vypisuji data pro tabulku `zadavatel`
--

INSERT INTO `zadavatel` (`ic`, `nazev`, `zkratka`, `dic`, `mesto`, `ulice`, `cp`, `psc`, `ucet`, `kontakt`) VALUES
(23980497, 'Domaci potreby STAR', 'STAR', 'CZ23980497', 'Olomouc', 'Tesarova', '48', 64492, '574334885/300', '777334637'),
(39505987, 'Zamecnictvi Vasek', 'ZV', 'CZ39505987', 'Ceska Trebova', 'Jiraskova', '52', 23940, '88434874/100', 'zamecnictvi.vasek@email.cz'),
(52864112, 'Elektro Zouhar', 'EZ', 'CZ52864112', 'Svitavy', 'Zouharova', '12', 60523, '35652879/800', '731144529'),
(56732722, 'Nabytek ALFA', 'ALFA', 'CZ56732722', 'Jihlava', 'Namesti Miru', '112', 37746, '65463561/300', '442678229'),
(86415885, 'SAKO s.r.o.', 'SAKO', 'CZ86415885', 'Decin', 'Popelarska', '521', 57703, '23355569/800', 'info@sako.cz');

-- --------------------------------------------------------

--
-- Struktura tabulky `zakazka`
--

CREATE TABLE IF NOT EXISTS `zakazka` (
  `cisloZakazky` int(10) NOT NULL AUTO_INCREMENT,
  `popis` varchar(150) COLLATE latin2_czech_cs DEFAULT NULL,
  `prijato` date DEFAULT NULL,
  `termin` date DEFAULT NULL,
  `zaloha` int(11) DEFAULT NULL,
  `rozpocet` int(11) DEFAULT NULL,
  `stav` varchar(10) COLLATE latin2_czech_cs NOT NULL,
  `zadavatel_id` int(8) NOT NULL,
  PRIMARY KEY (`cisloZakazky`),
  KEY `FK_Zakazka_Zadavatel` (`zadavatel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=7 ;

--
-- Vypisuji data pro tabulku `zakazka`
--

INSERT INTO `zakazka` (`cisloZakazky`, `popis`, `prijato`, `termin`, `zaloha`, `rozpocet`, `stav`, `zadavatel_id`) VALUES
(1, 'vyloha 3x3m', '2012-02-29', '2012-03-06', 10000, 30000, 'resi se', 52864112),
(2, 'polep aut', '2012-03-06', '2012-03-12', 5000, 15000, 'hotovo', 52864112),
(3, 'tricka', '2012-03-16', '2012-03-30', 0, 10000, 'resi se', 86415885),
(4, 'pracovni odevy', '2012-04-16', '2012-05-12', 3000, 12000, 'resi se', 39505987),
(5, 'vyloha 5x7', '2012-03-10', '2012-03-25', 15000, 60000, 'resi se', 23980497),
(6, 'polep sluzebnich vozidel', '2012-03-19', '2012-04-28', 6000, 35000, 'resi se', 56732722);

--
-- Spouště `zakazka`
--
DROP TRIGGER IF EXISTS `uvolni_resitele`;
DELIMITER //
CREATE TRIGGER `uvolni_resitele` AFTER UPDATE ON `zakazka`
 FOR EACH ROW BEGIN
IF NEW.stav = 'hotovo' OR NEW.stav = 'storno'  THEN
DELETE FROM resi WHERE zakazka_id=OLD.cisloZakazky;
END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabulky `zamestnanec`
--

CREATE TABLE IF NOT EXISTS `zamestnanec` (
  `osobniCislo` int(5) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(20) COLLATE latin2_czech_cs DEFAULT NULL,
  `prijmeni` varchar(20) COLLATE latin2_czech_cs DEFAULT NULL,
  `tituly` varchar(20) COLLATE latin2_czech_cs DEFAULT NULL,
  `rc` char(10) COLLATE latin2_czech_cs DEFAULT NULL,
  `telefon` int(9) DEFAULT NULL,
  `pracUvazek` varchar(10) COLLATE latin2_czech_cs DEFAULT NULL,
  `oddeleni_id` char(3) COLLATE latin2_czech_cs NOT NULL,
  PRIMARY KEY (`osobniCislo`),
  UNIQUE KEY `rc` (`rc`),
  KEY `FK_Zamestnanec_Oddeleni` (`oddeleni_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `zamestnanec`
--

INSERT INTO `zamestnanec` (`osobniCislo`, `jmeno`, `prijmeni`, `tituly`, `rc`, `telefon`, `pracUvazek`, `oddeleni_id`) VALUES
(1, 'Jan', 'Soukal', 'Pan', '6802132133', 731286621, 'plny', 'POL'),
(2, 'Jan', 'Suk', 'Ing', '9010134078', 774185869, 'plny', 'NAZ'),
(3, 'Jakub', 'Drzy', 'Mgr', '7701213456', 777563214, 'polovicni', 'nez'),
(4, 'Jiri', 'Oplzly', 'Ing', '7102469870', 715649789, 'plny', 'VYL'),
(5, 'Jirina', 'Hruba', '', '6852132100', 542693861, 'plny', 'NAZ'),
(6, 'Jana', 'Hruba', 'Phd', '8861132100', 542693861, 'plny', 'NAZ'),
(7, 'Jitka', 'Plasilova', '', '7254124778', 773388799, 'polovicni', 'NAZ'),
(8, 'Jaroslav', 'Blaha', 'Ing', '8211013887', 737002939, 'plny', 'VYL'),
(9, 'Jaromir', 'Krenek', '', '7712276886', 775263980, 'polovicni', 'POL');

--
-- Spouště `zamestnanec`
--
DROP TRIGGER IF EXISTS `smaz_vedouciho`;
DELIMITER //
CREATE TRIGGER `smaz_vedouciho` BEFORE DELETE ON `zamestnanec`
 FOR EACH ROW BEGIN
DELETE FROM vedouci WHERE zamestnanec_id=OLD.osobnicislo;
DELETE FROM resi WHERE zamestnanec_id=OLD.osobnicislo;
END
//
DELIMITER ;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `resi`
--
ALTER TABLE `resi`
  ADD CONSTRAINT `FK_Resi_Zakazka` FOREIGN KEY (`zakazka_id`) REFERENCES `zakazka` (`cisloZakazky`),
  ADD CONSTRAINT `FK_Resi_Zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`osobniCislo`);

--
-- Omezení pro tabulku `vedouci`
--
ALTER TABLE `vedouci`
  ADD CONSTRAINT `FK_Vedouci_Oddeleni` FOREIGN KEY (`oddeleni_id`) REFERENCES `oddeleni` (`kodOddeleni`),
  ADD CONSTRAINT `FK_Vedouci_Zamestnanec` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`osobniCislo`);

--
-- Omezení pro tabulku `vydaj`
--
ALTER TABLE `vydaj`
  ADD CONSTRAINT `FK_Vydaj_Zakazka` FOREIGN KEY (`zakazka_id`) REFERENCES `zakazka` (`cisloZakazky`);

--
-- Omezení pro tabulku `zakazka`
--
ALTER TABLE `zakazka`
  ADD CONSTRAINT `FK_Zakazka_Zadavatel` FOREIGN KEY (`zadavatel_id`) REFERENCES `zadavatel` (`ic`);

--
-- Omezení pro tabulku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  ADD CONSTRAINT `FK_Zamestnanec_Oddeleni` FOREIGN KEY (`oddeleni_id`) REFERENCES `oddeleni` (`kodOddeleni`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
