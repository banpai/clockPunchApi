SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_activity
-- ----------------------------
DROP TABLE IF EXISTS `t_activity`;
CREATE TABLE `t_activity` (
  `id` int(11) NOT NULL,
  `actname` varchar(100) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `entkbn` int(11) DEFAULT NULL,
  `flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_friendship
-- ----------------------------
DROP TABLE IF EXISTS `t_friendship`;
CREATE TABLE `t_friendship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(100) DEFAULT NULL,
  `otheropenid` varchar(100) DEFAULT NULL,
  `activity` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `entkbn` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Reference_1` (`activity`),
  CONSTRAINT `FK_Reference_1` FOREIGN KEY (`activity`) REFERENCES `t_activity` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_invitation
-- ----------------------------
DROP TABLE IF EXISTS `t_invitation`;
CREATE TABLE `t_invitation` (
  `id` int(11) NOT NULL COMMENT '主键id,自动增长',
  `image` varchar(100) DEFAULT NULL COMMENT '邀请卡的图片地址',
  `wellKnow1` varchar(100) DEFAULT NULL COMMENT '名言1',
  `wellKnow2` varchar(100) DEFAULT NULL COMMENT '鸡汤1',
  `position` int(100) DEFAULT NULL COMMENT '生成图片的中间的文字',
  `size` int(100) DEFAULT NULL COMMENT '生成的二维码下面的文字',
  `flag` int(11) DEFAULT NULL COMMENT '0是废弃，1是生效',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='cms修改邀请卡的表';

-- ----------------------------
-- Table structure for t_like
-- ----------------------------
DROP TABLE IF EXISTS `t_like`;
CREATE TABLE `t_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `openid` varchar(100) DEFAULT NULL COMMENT '微信openid',
  `otheropenid` varchar(100) DEFAULT NULL,
  `likeTime` time DEFAULT NULL COMMENT '创建时间',
  `likeDate` date DEFAULT NULL COMMENT '更新时间',
  `version` int(11) DEFAULT NULL COMMENT '版本号',
  `likestatus` varchar(10) DEFAULT NULL COMMENT '点赞状态（0：失败 1：成功）',
  `entkbn` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COMMENT='点赞记录表';

-- ----------------------------
-- Table structure for t_members
-- ----------------------------
DROP TABLE IF EXISTS `t_members`;
CREATE TABLE `t_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `flag` int(1) DEFAULT '1' COMMENT '0是废弃，1是正在使用',
  `openid` varchar(100) DEFAULT NULL COMMENT '微信openid',
  `city` varchar(100) DEFAULT NULL COMMENT '城市',
  `province` varchar(100) DEFAULT NULL COMMENT '省份',
  `country` varchar(100) DEFAULT NULL COMMENT '国籍',
  `sex` int(1) DEFAULT NULL COMMENT '性别',
  `punchDays` int(100) DEFAULT '0' COMMENT '连续签到天数',
  `punchDate` date DEFAULT NULL COMMENT '最后一次签到日期',
  `headimgurl` varchar(500) DEFAULT NULL COMMENT '头像地址',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT NULL COMMENT '更新时间',
  `version` int(11) NOT NULL COMMENT '版本信息',
  `integral` int(11) DEFAULT NULL,
  `nickname` varchar(500) DEFAULT NULL COMMENT '这个用户的专门的二维码图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';

-- ----------------------------
-- Table structure for t_project
-- ----------------------------
DROP TABLE IF EXISTS `t_project`;
CREATE TABLE `t_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id,自动增长',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `contant` text COMMENT '中间富文本的内容',
  `image` varchar(500) DEFAULT NULL COMMENT '头部的背景图片',
  `subtitle` varchar(100) DEFAULT NULL COMMENT '次要标题',
  `flag` int(11) DEFAULT NULL COMMENT '0是废弃，1是生效',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='cms配置我要参加页面';

-- ----------------------------
-- Table structure for t_punch
-- ----------------------------
DROP TABLE IF EXISTS `t_punch`;
CREATE TABLE `t_punch` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `openid` varchar(100) DEFAULT NULL COMMENT '微信openid',
  `punchDate` date DEFAULT NULL COMMENT '创建时间',
  `punchTime` time DEFAULT NULL COMMENT '更新时间',
  `version` int(11) DEFAULT NULL COMMENT '版本号',
  `punchtatus` varchar(10) DEFAULT NULL COMMENT '打卡状态（0：失败 1：成功）',
  `entkbn` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COMMENT='打卡记录表';

-- ----------------------------
-- Table structure for t_showcard
-- ----------------------------
DROP TABLE IF EXISTS `t_showcard`;
CREATE TABLE `t_showcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id,自动增长',
  `image` varchar(100) DEFAULT NULL COMMENT '邀请卡的图片地址',
  `position` int(100) DEFAULT NULL COMMENT '生成图片的中间的文字',
  `wellKnow` varchar(100) DEFAULT NULL COMMENT '鸡汤',
  `size` int(100) DEFAULT NULL COMMENT '生成的二维码下面的文字',
  `flag` int(11) DEFAULT NULL COMMENT '0是废弃，1是生效',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='cms修改打卡图的表';

-- ----------------------------
-- Table structure for t_userinfo
-- ----------------------------
DROP TABLE IF EXISTS `t_userinfo`;
CREATE TABLE `t_userinfo` (
  `id` int(11) NOT NULL COMMENT '主键id,自动增长',
  `name` varchar(100) DEFAULT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT 'cms用户密码',
  `openid` varchar(100) DEFAULT NULL COMMENT '用户微信openid',
  `role` int(11) DEFAULT NULL COMMENT '用户角色',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `version` int(11) DEFAULT NULL COMMENT '版本信息(乐观排他字段)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='cms用户用表';

-- ----------------------------
-- Table structure for t_warntime
-- ----------------------------
DROP TABLE IF EXISTS `t_warntime`;
CREATE TABLE `t_warntime` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id,自动增长',
  `time` time DEFAULT NULL,
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='cms修改早起模板消息定时发送的时间';
