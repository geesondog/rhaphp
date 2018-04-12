CREATE TABLE IF NOT EXISTS `rh_vote_baoming` (
  `bm_id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL,
  `username` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `cover` varchar(500) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `create_time` int(10) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `vote_total` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(128) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:正常0：隐藏',
  PRIMARY KEY (`bm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_vote_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL,
  `bm_id` int(11) NOT NULL,
  `openid` varchar(64) DEFAULT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_vote_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(11) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
