SET AUTOCOMMIT = 0;
START TRANSACTION;

--
-- Database: `heartbeat-one`
--
CREATE DATABASE IF NOT EXISTS `heartbeat-one` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `heartbeat-one`;

-- --------------------------------------------------------

--
-- Table `monitor`
--

CREATE TABLE `monitor` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `master_host` varchar(30) NOT NULL,
  `updated` datetime(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for table `monitor`
--
ALTER TABLE `monitor` ADD PRIMARY KEY (`id`);
  
ALTER TABLE `monitor` ADD UNIQUE(`master_host`);


ALTER TABLE `monitor`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;
