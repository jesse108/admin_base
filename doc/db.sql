CREATE TABLE `user` (
  `id` bigint(20) NOT NULL primary key AUTO_INCREMENT,
  `username` varchar(16) COLLATE utf8_bin NOT NULL,
  `password` varchar(32) COLLATE utf8_bin NOT NULL,
  `create_time` int(10) NOT NULL DEFAULT 0 comment '创建时间',
  `update_time` int(10) not null default 0 comment '更新时间',
  `register_ip` varchar(32) comment '登陆IP',
  `login_time` int(10) NOT NULL DEFAULT '0',
  `login_ip` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  key(`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';


CREATE TABLE `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `table_name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_id` bigint(20) NOT NULL,
  `old_data` text COLLATE utf8_bin,
  `update_data` text COLLATE utf8_bin,
  `type` tinyint(2) NOT NULL DEFAULT '1',
  `actor_id` bigint(20) DEFAULT NULL,
  `actor_type` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`,`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=616 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='通用日志表'


CREATE TABLE `queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) not null default 0,
  `data` text comment '数据',
  `status` tinyint(2) not null default 1,
  `read_time` int(10) not null default 0,
  key(`update_time`),
  key(`status`,`read_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='通用日志表'


