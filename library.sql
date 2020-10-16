/*
SQLyog v10.2 
MySQL - 5.7.26 : Database - library
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`library` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `library`;

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '垃圾箱',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员';

/*Data for the table `admin` */

insert  into `admin`(`id`,`username`,`password`,`created_at`,`updated_at`,`is_del`) values (1,'admin','64bd24e6a8d12970b4f8dbff08e614c8','2020-10-15 21:08:08','0000-00-00 00:00:00',0);

/*Table structure for table `book` */

DROP TABLE IF EXISTS `book`;

CREATE TABLE `book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `c_id` int(11) NOT NULL COMMENT '创建者',
  `category_id` int(11) NOT NULL COMMENT '分类id',
  `title` varchar(50) NOT NULL COMMENT '书籍名称',
  `author` varchar(30) NOT NULL COMMENT '作者',
  `press` varchar(30) NOT NULL COMMENT '出版社',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '借阅状态(0/1)',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '垃圾箱',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`,`author`,`press`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='书籍';

/*Data for the table `book` */

insert  into `book`(`id`,`c_id`,`category_id`,`title`,`author`,`press`,`created_at`,`updated_at`,`status`,`is_del`) values (1,0,4,'123','111','222','2020-10-16 00:49:49','0000-00-00 00:00:00',0,0),(2,0,4,'123','111','222','2020-10-16 00:51:15','0000-00-00 00:00:00',1,0),(3,0,4,'123','111','222','2020-10-16 00:51:33','0000-00-00 00:00:00',0,0),(4,0,4,'123','111','222','2020-10-16 00:35:52','0000-00-00 00:00:00',1,0),(5,0,4,'123','111','222','2020-10-16 00:35:55','0000-00-00 00:00:00',1,0),(6,1,4,'123','333','444','2020-10-16 00:35:58','0000-00-00 00:00:00',1,1),(7,1,4,'123','333','444','2020-10-15 23:03:17','0000-00-00 00:00:00',0,0),(8,1,4,'123','333','444','2020-10-15 23:03:17','0000-00-00 00:00:00',0,0),(9,1,4,'123','333','444','2020-10-15 23:03:17','0000-00-00 00:00:00',0,0),(10,1,4,'123','333','444','2020-10-15 23:03:17','0000-00-00 00:00:00',0,0),(11,1,4,'123','333','444','2020-10-15 23:14:02','0000-00-00 00:00:00',0,0),(12,1,4,'123','333','444','2020-10-15 23:14:02','0000-00-00 00:00:00',0,0),(13,1,4,'123','333','444','2020-10-15 23:14:02','0000-00-00 00:00:00',0,0),(14,1,4,'123','333','444','2020-10-15 23:14:02','0000-00-00 00:00:00',0,0),(15,1,4,'123','333','444','2020-10-15 23:14:02','0000-00-00 00:00:00',0,0),(16,1,4,'123','333','444','2020-10-15 23:17:24','0000-00-00 00:00:00',0,0),(17,1,4,'123','333','444','2020-10-15 23:17:24','0000-00-00 00:00:00',0,0),(18,1,4,'123','333','444','2020-10-15 23:17:24','0000-00-00 00:00:00',0,0),(19,1,4,'123','333','444','2020-10-15 23:17:24','0000-00-00 00:00:00',0,0),(20,1,4,'123','333','44555','2020-10-15 23:26:17','0000-00-00 00:00:00',0,0);

/*Table structure for table `borrow` */

DROP TABLE IF EXISTS `borrow`;

CREATE TABLE `borrow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `book_id` int(11) NOT NULL COMMENT '书籍id',
  `student_id` int(11) NOT NULL COMMENT '学生id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `back_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '归还时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '归还状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='借阅记录';

/*Data for the table `borrow` */

insert  into `borrow`(`id`,`book_id`,`student_id`,`created_at`,`back_at`,`status`) values (7,1,1,'2020-10-16 00:49:49','2020-10-16 12:49:49',1),(8,2,2,'2020-10-16 00:50:50','2020-10-16 12:50:50',1),(9,3,2,'2020-10-16 00:51:33','2020-10-16 12:51:33',1),(10,4,2,'2020-10-16 00:35:52','2020-10-21 00:00:00',0),(11,5,2,'2020-10-16 00:35:55','2020-10-21 00:00:00',0),(12,6,2,'2020-10-16 00:35:58','2020-10-21 00:00:00',0),(13,2,2,'2020-10-16 00:51:15','2020-10-21 00:00:00',0);

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `c_id` int(11) NOT NULL COMMENT '创建者',
  `title` varchar(50) NOT NULL COMMENT '分类名称',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '垃圾箱',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='分类';

/*Data for the table `category` */

insert  into `category`(`id`,`c_id`,`title`,`created_at`,`updated_at`,`is_del`) values (1,0,'fwef','2020-10-15 22:26:42','0000-00-00 00:00:00',1),(2,0,'123','2020-10-15 22:27:37','0000-00-00 00:00:00',1),(3,0,'12351','2020-10-15 22:28:44','0000-00-00 00:00:00',1),(4,0,'1gerg123','2020-10-15 22:19:51','0000-00-00 00:00:00',0),(5,0,'fwegg','2020-10-15 22:28:35','0000-00-00 00:00:00',0);

/*Table structure for table `student` */

DROP TABLE IF EXISTS `student`;

CREATE TABLE `student` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `code` varchar(30) NOT NULL COMMENT '学号',
  `name` varchar(30) NOT NULL COMMENT '姓名',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '垃圾箱',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='学生';

/*Data for the table `student` */

insert  into `student`(`id`,`code`,`name`,`created_at`,`update`,`is_del`) values (1,'001','张三','2020-10-15 23:38:57','0000-00-00 00:00:00',0),(2,'002','李四','2020-10-15 23:39:06','0000-00-00 00:00:00',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
