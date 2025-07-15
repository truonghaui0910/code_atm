-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `autoseo`;

DROP TABLE IF EXISTS `channel`;
CREATE TABLE `channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `channel_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `channel_code` text COLLATE utf8_unicode_ci,
  `client_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `del_status` tinyint(3) unsigned DEFAULT '0' COMMENT '0:chua xoa,1:xoa',
  `status` tinyint(3) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'join voi bang users',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel_id` (`channel_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_client` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `health` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `client` (`id`, `url_client`, `status`, `health`) VALUES
(3,	'http://159.203.180.111',	1,	0),
(4,	'http://159.203.188.109',	1,	0),
(5,	'http://159.203.178.185',	1,	0),
(6,	'http://159.203.186.220',	1,	0),
(7,	'http://159.203.186.213',	1,	0);

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fb_access_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_playlist` int(11) NOT NULL DEFAULT '0',
  `date_end` int(11) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fb_id` (`fb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `customers` (`id`, `fb_id`, `fb_access_token`, `total_playlist`, `date_end`, `description`, `name`, `email`, `avatar`) VALUES
(5,	'1184601585005431',	'EAAbZCtcUGF5YBAMhFXgr1EX0MuxBQGzoEuT9YyE5EMwesxZCluCSISiexU6jAoTLk0qbb7pfYoPNuMWSt9xvgsKy3f5gvws3q6idURDgynYBvfZAZAMcB5KrrSH4D3So1g0rHhdr41k33V9dOw6ScSxf4ZCUwVEFsjDX3cSThWAZDZD',	0,	0,	NULL,	'Hòa Thân Sweet',	'thanhhoakhmt1k5@gmail.com',	'https://graph.facebook.com/v2.10/1184601585005431/picture?type=normal'),
(6,	'2006879946213660',	'EAAbZCtcUGF5YBAK0YEZBxM2ed642GyZBV8kNIX7mEpjTsKSOkZCp9r42dptKqUznCgZC98wXTnvGF5HLkzDpM7zXhU8Bw32WxxRAo3czxaObVY9wqKhUZA0wy4OwdvKGZCXpvEwUxbNnQtLZALn8XR3qHzArwR7DHm4HogSlZAn7MZAwZDZD',	0,	0,	NULL,	'Quan Vũ',	'getmoneykhmt1@gmail.com',	'https://graph.facebook.com/v2.10/2006879946213660/picture?type=normal'),
(7,	'1518186608273689',	'EAAbZCtcUGF5YBAB4OBCJGKLkBfLRF5D44Le6MJ6egjoAGGCpygD1NzSEhlSJxwrhotxx6Sgu4A32nscFgYoZB7pbNWZCoM09jw9DrYUlwHOhszlhXEIzwnKYZAwZC6WTh97pazfOgzGRwKTTE6ityfT3lfbenBz8fqpvu1ZCZBmr4c6Iz3YrJkn',	0,	0,	NULL,	'Sáng Nguyễn Văn',	'meolovegau@gmail.com',	'https://graph.facebook.com/v2.10/1518186608273689/picture?type=normal'),
(8,	'1779322002361262',	'EAAbZCtcUGF5YBADf745IHXycrSxjyefFu2QxirZBczvLPi4gKfU4hvhbn1fsIhf3qHo6D3JtCqr29vNvuO7IjTjA7yOKC44X4yB7ITSJvmLkchCpXZC5Wv6G6rnvIAMuQHTGqv0G263hok9SByyCwkNsz37mLjtnGqO51z3LQZDZD',	0,	0,	NULL,	'Trường Phạm',	'truongpv0910@gmail.com',	'https://graph.facebook.com/v2.10/1779322002361262/picture?type=normal'),
(9,	'788582611314421',	'EAAbZCtcUGF5YBAPxC5TKt8kYDrpcHvoAfIQlIV33OH1Y31dm8GGSZCL4ZAwGzJkucn3ywsBlyXS4F5ZAc7ojIOfCt7zvoViH3Y4YrN2DE8cNRQpVqO5tZC5p0oCzRyZA9gEqEGhVem5lLHnKyQrRr7ZCXE5A99A8W0mcEt618x2P9euzMyorzpn',	0,	0,	NULL,	'Tuan Nguyen',	'ngoisaocodon_8790@yahoo.com.vn',	'https://graph.facebook.com/v2.10/788582611314421/picture?type=normal');

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ma hoa don: user_name+ten goi cuoc+thoi gian',
  `user_id` int(11) NOT NULL COMMENT 'id cua bang users',
  `system_create_date` int(11) NOT NULL COMMENT 'ngay tao invoice',
  `create_date` int(11) NOT NULL,
  `due_date` int(11) NOT NULL COMMENT 'ngay het han nop tien',
  `payment_money` float unsigned NOT NULL COMMENT 'so tien phai thanh toan',
  `user_approve` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'nguoi phe duyet hoa don',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0:moi,1:da thanh toan,3:qua han',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `invoice` (`id`, `invoice_id`, `user_id`, `system_create_date`, `create_date`, `due_date`, `payment_money`, `user_approve`, `status`) VALUES
(1,	'TRUONGPVSEO2211117083844',	8,	1511253528,	1511253524,	1511426324,	200000,	NULL,	0),
(2,	'TRUONGPV11SEO3211117101259',	14,	1511259206,	1511259179,	1511431979,	300000,	NULL,	0);

DROP TABLE IF EXISTS `lockprocess`;
CREATE TABLE `lockprocess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `time` int(11) NOT NULL,
  `time_gmt7` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `oauth_google`;
CREATE TABLE `oauth_google` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '1: running, 14 waiting, 13: die frist scan, 15: die second scan',
  `count` tinyint(3) unsigned DEFAULT NULL,
  `new` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `package_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ma goi cuoc',
  `package_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'ten goi cuoc',
  `number_playlist` int(5) unsigned NOT NULL COMMENT 'so playlist duoc phep tao',
  `duration` int(5) unsigned NOT NULL COMMENT 'thoi gian het han tinh theo ngay',
  `price` float unsigned NOT NULL COMMENT 'gia cua goi cuoc',
  `status` int(1) unsigned NOT NULL COMMENT '1:active,0:incative',
  `order_package` int(5) unsigned NOT NULL COMMENT 'de phan biet xem goi cuoc nao lon hon,cang to thi cang lon',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `package` (`id`, `package_code`, `package_name`, `number_playlist`, `duration`, `price`, `status`, `order_package`) VALUES
(1,	'FREE',	'FREE 3 Day',	9,	3,	0,	1,	1),
(2,	'SEO2',	'SEO2',	500,	15,	200000,	1,	2),
(3,	'SEO3',	'SEO3',	1000,	20,	300000,	1,	3),
(4,	'SEO4',	'SEO4',	2000,	30,	400000,	1,	4);

DROP TABLE IF EXISTS `playlist_config`;
CREATE TABLE `playlist_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` text COLLATE utf8_unicode_ci,
  `selected_channel_num` int(5) unsigned DEFAULT NULL COMMENT 'so kenh da chon de tao playlist',
  `playlist_num` int(10) unsigned DEFAULT NULL COMMENT 'so playlist muon tao',
  `source_type` tinyint(3) unsigned DEFAULT NULL COMMENT '1:theo key,0:theo link',
  `source` text COLLATE utf8_unicode_ci COMMENT 'nguon lay du lieu',
  `subscribe_type` tinyint(3) unsigned DEFAULT NULL COMMENT '0:khong sub,1:theo doi tat ca thoi gian,2:theo ngay,3:theo tuan,4:theo thang hien tai',
  `priority_type` tinyint(3) unsigned DEFAULT NULL COMMENT 'do uu tien lay video, 0:tu dong,1:theo moi that,2:theo cu nhat',
  `meta_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'chuoi regex de loc video theo chat luong hoac thoi luong',
  `playlist_title` text COLLATE utf8_unicode_ci COMMENT 'chuoi string phan cach boi  @;@',
  `playlist_description` text COLLATE utf8_unicode_ci COMMENT 'mo ta chung co cac playlist, viet theo gia code',
  `topic` text COLLATE utf8_unicode_ci COMMENT 'ten chu de theo keyword',
  `create_time` int(11) unsigned NOT NULL,
  `log` longtext COLLATE utf8_unicode_ci,
  `video_total` int(11) unsigned DEFAULT NULL COMMENT 'list id video tim theo tu khoa',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0:new,1:prepaid,2:running,3:done',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `playlist_detail`;
CREATE TABLE `playlist_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'id cua bang users',
  `channel_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `channel_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `playlist_id` text COLLATE utf8_unicode_ci COMMENT 'id cua playlist=> link playlist',
  `source_type` tinyint(3) unsigned DEFAULT NULL COMMENT '1:theo key,0:theo link',
  `source` text COLLATE utf8_unicode_ci COMMENT 'link video, link playlist, link channel hoac keyword',
  `source_processing` text COLLATE utf8_unicode_ci COMMENT 'source sau khi phan loai: channel link, playlist link, video link, keyword',
  `subscribe_type` tinyint(3) unsigned DEFAULT NULL COMMENT '0:khong sub,1:theo doi tat ca thoi gian,2:theo ngay,3:theo tuan,4:theo thang hien tai',
  `priority_type` tinyint(3) unsigned DEFAULT NULL COMMENT '0:tu dong,1:moi nhat,2:cu nhat',
  `meta_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'regex ',
  `title` text COLLATE utf8_unicode_ci COMMENT 'tieu de cua playlist',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'mo ta cua playlist',
  `number_video_playlist` text COLLATE utf8_unicode_ci COMMENT 'so luong video trong playlist',
  `number_view_playlist` text COLLATE utf8_unicode_ci COMMENT 'so luong view cua playlist',
  `last_number_view_playlist` int(11) unsigned DEFAULT '0' COMMENT 'so luong view cuoi cua playlist',
  `last_number_video_playlist` int(11) unsigned DEFAULT '0' COMMENT 'so luong video cuoi cua playlist',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `location` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ma quoc gia tu client',
  `language` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ma ngon ngu tu client',
  `next_time_run` int(11) unsigned DEFAULT '0' COMMENT 'thoi gian chay lan tiep theo',
  `rate_next_time_run` double unsigned DEFAULT '1' COMMENT 'rate next time run',
  `rate_priority_normal` double unsigned DEFAULT '0.1' COMMENT 'ty le giua video vip va video thuong 1/10',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0:new,1:prepaid,2:running,3:done,4:stop,5:loi',
  `error_code` tinyint(3) unsigned DEFAULT '0' COMMENT '0: none, 1: create pll, 2: error insert',
  `log` text COLLATE utf8_unicode_ci,
  `total_video` int(10) unsigned DEFAULT '0' COMMENT 'tong so video lay duoc',
  `total_video_avaiable` int(10) unsigned DEFAULT '0' COMMENT 'so luong video con lai',
  `total_video_priority` int(10) unsigned DEFAULT '0' COMMENT 'so luong video vip',
  `total_video_added` int(10) unsigned DEFAULT '0' COMMENT 'so luong video da duoc added',
  `list_video_total` text COLLATE utf8_unicode_ci COMMENT 'danh sach id tat ca video quet duoc',
  `list_video_priority` text COLLATE utf8_unicode_ci COMMENT 'danh sach id video uu tien',
  `list_video_available` text COLLATE utf8_unicode_ci COMMENT 'danh sach id video con lai',
  `list_video_added` text COLLATE utf8_unicode_ci COMMENT 'danh sach resouce id va id video da them vao pll',
  `list_video_x` text COLLATE utf8_unicode_ci COMMENT 'danh sach x resouce id va id video da them vao pll',
  `next_time_subscribe` int(11) unsigned DEFAULT '0' COMMENT 'thoi gian tiep theo quet theo doi',
  `next_time_reindex_x` int(11) unsigned DEFAULT '0' COMMENT 'thoi gian tiep theo dong bo lai vi tri video x',
  `next_time_sync` int(11) unsigned DEFAULT NULL COMMENT 'thoi gian tiep theo dong bo views + video cua pll',
  `index_priority` int(10) unsigned DEFAULT '0' COMMENT 'vi tri vip hien tai',
  `index_normal` int(10) unsigned DEFAULT '1' COMMENT 'vi tri thuong hien tai',
  `next_time_order` int(11) unsigned DEFAULT NULL COMMENT 'thoi gian tiep theo quet order video',
  `topic` text COLLATE utf8_unicode_ci COMMENT 'ten chu de theo keyword',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `playlist_queue`;
CREATE TABLE `playlist_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `playlist_id` varchar(255) NOT NULL,
  `video_id` varchar(255) NOT NULL,
  `position` int(5) NOT NULL COMMENT 'vi tri them video',
  `status` tinyint(3) NOT NULL COMMENT 'status=0: new, 1: pending, 2:done, 3: error',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0: add, 1: delete',
  `log` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `playlist_queue` (`id`, `user_id`, `playlist_id`, `video_id`, `position`, `status`, `type`, `log`) VALUES
(7,	11,	'PLRIfM67hUSGufmCD99hoOJIp8K-K2vVxY',	'3tmd-ClpJxA',	3,	2,	0,	'[\"3tmd-ClpJxA;;UExSSWZNNjdoVVNHdWZtQ0Q5OWhvT0pJcDhLLUsydlZ4WS4zRjM0MkVCRTg0MkYyQTM0;;3\"]'),
(8,	13,	'PLQY7mvmN2D2wGPMjPk-l9loWcwXgPYrPo',	'sWTBgRs-9s8',	1,	2,	0,	'[\"sWTBgRs-9s8;;UExRWTdtdm1OMkQyd0dQTWpQay1sOWxvV2N3WGdQWXJQby4yODlGNEE0NkRGMEEzMEQy;;1\"]'),
(9,	13,	'PLQY7mvmN2D2yKfWRzctrbBRmjVO_3i3u9',	'sWTBgRs-9s8',	0,	2,	0,	'[\"sWTBgRs-9s8;;UExRWTdtdm1OMkQyeUtmV1J6Y3RyYkJSbWpWT18zaTN1OS41NkI0NEY2RDEwNTU3Q0M2;;0\"]');

DROP TABLE IF EXISTS `plcountry`;
CREATE TABLE `plcountry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `plcountry` (`id`, `code`, `name`, `status`) VALUES
(1,	'us',	'United States',	1),
(2,	'af',	'Afghanistan (افغانستان)',	1),
(3,	'al',	'Albania (Shqipëria)',	1),
(4,	'dz',	'Algeria (الجزائر)',	1),
(5,	'as',	'American Samoa (Amerika Sāmoa)',	1),
(6,	'ad',	'Andorra',	1),
(7,	'ao',	'Angola',	1),
(8,	'ai',	'Anguilla',	1),
(9,	'aq',	'Antarctica',	1),
(10,	'ag',	'Antigua and Barbuda',	1),
(11,	'ar',	'Argentina',	1),
(12,	'am',	'Armenia (Hayastán)',	1),
(13,	'aw',	'Aruba',	1),
(14,	'au',	'Australia',	1),
(15,	'at',	'Austria (Österreich)',	1),
(16,	'az',	'Azerbaijan (Azərbaycan)',	1),
(17,	'bs',	'Bahamas',	1),
(18,	'bh',	'Bahrain (البحرين)',	1),
(19,	'bd',	'Bangladesh (বাংলাদেশ)',	1),
(20,	'bb',	'Barbados',	1),
(21,	'by',	'Belarus (Беларусь)',	1),
(22,	'be',	'Belgium (België)',	1),
(23,	'bz',	'Belize',	1),
(24,	'bj',	'Benin (Bénin)',	1),
(25,	'bm',	'Bermuda',	1),
(26,	'bt',	'Bhutan (འབྲུག་ཡུལ)',	1),
(27,	'bo',	'Bolivia',	1),
(28,	'ba',	'Bosnia and Herzegovina (Bosna i Hercegovina)',	1),
(29,	'bw',	'Botswana',	1),
(30,	'bv',	'Bouvet Island',	1),
(31,	'br',	'Brazil (Brasil)',	1),
(32,	'io',	'British Indian Ocean Territory',	1),
(33,	'bn',	'Brunei (بروني)',	1),
(34,	'bg',	'Bulgaria',	1),
(35,	'bf',	'Burkina Faso',	1),
(36,	'bi',	'Burundi',	1),
(37,	'kh',	'Cambodia (Kampuchea)',	1),
(38,	'cm',	'Cameroon (Cameroun)',	1),
(39,	'ca',	'Canada',	1),
(40,	'cv',	'Cape Verde (Cabo Verde)',	1),
(41,	'ky',	'Cayman Islands',	1),
(42,	'cf',	'Central African Republic (République Centrafricaine)',	1),
(43,	'td',	'Chad (Tchad)',	1),
(44,	'cl',	'Chile',	1),
(45,	'cn',	'China (中国)',	1),
(46,	'cx',	'Christmas Island',	1),
(47,	'cc',	'Cocos (Keeling) Islands',	1),
(48,	'co',	'Colombia',	1),
(49,	'km',	'Comoros (جزر القمر)',	1),
(50,	'cg',	'Congo',	1),
(51,	'cd',	'Congo - Democratic Republic of',	1),
(52,	'ck',	'Cook Islands',	1),
(53,	'cr',	'Costa Rica',	1),
(54,	'ci',	'Cote d&#039;Ivoire',	1),
(55,	'hr',	'Croatia (Hrvatska)',	1),
(56,	'cy',	'Cyprus (Kypros)',	1),
(57,	'cz',	'Czech Republic (Česká Republika)',	1),
(58,	'dk',	'Denmark (Danmark)',	1),
(59,	'dj',	'Djibouti (جيبوتي)',	1),
(60,	'dm',	'Dominica',	1),
(61,	'do',	'Dominican Republic (República Dominicana)',	1),
(62,	'tl',	'Timor-Leste',	1),
(63,	'ec',	'Ecuador',	1),
(64,	'eg',	'Egypt (مصر)',	1),
(65,	'sv',	'El Salvador',	1),
(66,	'gq',	'Equatorial Guinea (Guinea Ecuatorial)',	1),
(67,	'er',	'Eritrea (إرتريا)',	1),
(68,	'ee',	'Estonia (Eesti)',	1),
(69,	'et',	'Ethiopia (Ityop&#039;ia)',	1),
(70,	'fk',	'Falkland Islands (Islas Malvinas)',	1),
(71,	'fo',	'Faroe Islands (Føroyar)',	1),
(72,	'fj',	'Fiji',	1),
(73,	'fi',	'Finland (Suomi)',	1),
(74,	'fr',	'France',	1),
(75,	'gf',	'French Guiana (Guyane)',	1),
(76,	'pf',	'French Polynesia (Polynésie Française)',	1),
(77,	'tf',	'French Southern Territories',	1),
(78,	'ga',	'Gabon',	1),
(79,	'gm',	'Gambia',	1),
(80,	'ge',	'Georgia (Sak&#039;art&#039;velo)',	1),
(81,	'de',	'Germany (Deutschland)',	1),
(82,	'gh',	'Ghana',	1),
(83,	'gi',	'Gibraltar',	1),
(84,	'gr',	'Greece (Hellas)',	1),
(85,	'gl',	'Greenland (Kalaallit Nunaat)',	1),
(86,	'gd',	'Grenada',	1),
(87,	'gp',	'Guadeloupe',	1),
(88,	'gu',	'Guam (Guåhån)',	1),
(89,	'gt',	'Guatemala',	1),
(90,	'gn',	'Guinea (Guinée)',	1),
(91,	'gw',	'Guinea-Bissau (Guiné-Bissau)',	1),
(92,	'gy',	'Guyana',	1),
(93,	'ht',	'Haiti (Haïti)',	1),
(94,	'hm',	'Heard Island and McDonald Islands',	1),
(95,	'hn',	'Honduras',	1),
(96,	'hk',	'Hong Kong (香港)',	1),
(97,	'hu',	'Hungary (Magyarország)',	1),
(98,	'is',	'Iceland (Ísland)',	1),
(99,	'in',	'India',	1),
(100,	'id',	'Indonesia',	1),
(101,	'iq',	'Iraq',	1),
(102,	'ie',	'Ireland (Éire)',	1),
(103,	'il',	'Israel (إسرائيل)',	1),
(104,	'it',	'Italy (Italia)',	1),
(105,	'jm',	'Jamaica',	1),
(106,	'jp',	'Japan (日本)',	1),
(107,	'jo',	'Jordan (الأردن)',	1),
(108,	'kz',	'Kazakhstan (Қазақстан)',	1),
(109,	'ke',	'Kenya',	1),
(110,	'ki',	'Kiribati',	1),
(111,	'kw',	'Kuwait (الكويت)',	1),
(112,	'kg',	'Kyrgyzstan (Кыргызстан)',	1),
(113,	'la',	'Laos (Lao)',	1),
(114,	'lv',	'Latvia (Latvija)',	1),
(115,	'lb',	'Lebanon (لبنان)',	1),
(116,	'ls',	'Lesotho',	1),
(117,	'lr',	'Liberia',	1),
(118,	'ly',	'Libya',	1),
(119,	'li',	'Liechtenstein',	1),
(120,	'lt',	'Lithuania (Lietuva)',	1),
(121,	'lu',	'Luxembourg (Lëtzebuerg)',	1),
(122,	'mo',	'Macao',	1),
(123,	'mk',	'Macedonia (Makedonija)',	1),
(124,	'mg',	'Madagascar (Madagasikara)',	1),
(125,	'mw',	'Malawi',	1),
(126,	'my',	'Malaysia',	1),
(127,	'mv',	'Maldives (Dhivehi Raajje)',	1),
(128,	'ml',	'Mali',	1),
(129,	'mt',	'Malta',	1),
(130,	'mh',	'Marshall Islands',	1),
(131,	'mq',	'Martinique',	1),
(132,	'mr',	'Mauritania (Muritan)',	1),
(133,	'mu',	'Mauritius (Maurice)',	1),
(134,	'yt',	'Mayotte',	1),
(135,	'mx',	'Mexico (México)',	1),
(136,	'fm',	'Micronesia - Federated States of',	1),
(137,	'md',	'Moldova',	1),
(138,	'mc',	'Monaco',	1),
(139,	'mn',	'Mongolia (Mongol Uls)',	1),
(140,	'ms',	'Montserrat',	1),
(141,	'ma',	'Morocco (Amerruk)',	1),
(142,	'mz',	'Mozambique (Moçambique)',	1),
(143,	'na',	'Namibia',	1),
(144,	'nr',	'Nauru',	1),
(145,	'np',	'Nepal (Nepāla)',	1),
(146,	'nl',	'Netherlands (Nederland)',	1),
(147,	'nc',	'New Caledonia (Nouvelle-Calédonie)',	1),
(148,	'nz',	'New Zealand',	1),
(149,	'ni',	'Nicaragua',	1),
(150,	'ne',	'Niger',	1),
(151,	'ng',	'Nigeria',	1),
(152,	'nu',	'Niue',	1),
(153,	'nf',	'Norfolk Island',	1),
(154,	'mp',	'Northern Mariana Islands',	1),
(155,	'no',	'Norway (Norge)',	1),
(156,	'om',	'Oman (عُمان)',	1),
(157,	'pk',	'Pakistan (پاکستان)',	1),
(158,	'pw',	'Palau (Belau)',	1),
(159,	'ps',	'West Bank',	1),
(160,	'pa',	'Panama (Panamá)',	1),
(161,	'pg',	'Papua New Guinea',	1),
(162,	'py',	'Paraguay',	1),
(163,	'pe',	'Peru (Perú)',	1),
(164,	'ph',	'Philippines (Pilipinas)',	1),
(165,	'pn',	'Pitcairn',	1),
(166,	'pl',	'Poland (Polska)',	1),
(167,	'pt',	'Portugal',	1),
(168,	'pr',	'Puerto Rico',	1),
(169,	'qa',	'Qatar (قطر)',	1),
(170,	're',	'Reunion (Réunion)',	1),
(171,	'ro',	'Romania (România)',	1),
(172,	'ru',	'Russia (Россия)',	1),
(173,	'rw',	'Rwanda',	1),
(174,	'kn',	'Saint Kitts and Nevis',	1),
(175,	'lc',	'Saint Lucia',	1),
(176,	'vc',	'Saint Vincent and the Grenadines',	1),
(177,	'ws',	'Samoa',	1),
(178,	'sm',	'San Marino',	1),
(179,	'st',	'Sao Tome and Principe (São Tomé e Príncipe)',	1),
(180,	'sa',	'Saudi Arabia (المملكة العربية السعودية)',	1),
(181,	'sn',	'Senegal (Sénégal)',	1),
(182,	'sc',	'Seychelles (Sesel)',	1),
(183,	'sl',	'Sierra Leone',	1),
(184,	'sg',	'Singapore',	1),
(185,	'sk',	'Slovakia (Slovensko)',	1),
(186,	'si',	'Slovenia (Slovenija)',	1),
(187,	'sb',	'Solomon Islands',	1),
(188,	'so',	'Somalia (Soomaaliya)',	1),
(189,	'za',	'South Africa (Suid-Afrika)',	1),
(190,	'gs',	'South Georgia and the South Sandwich Islands',	1),
(191,	'kr',	'South Korea (한국)',	1),
(192,	'es',	'Spain (España)',	1),
(193,	'lk',	'Sri Lanka (Sri Lankā)',	1),
(194,	'sh',	'Saint Helena',	1),
(195,	'pm',	'Saint Pierre and Miquelon',	1),
(196,	'sr',	'Suriname',	1),
(197,	'sj',	'Svalbard and Jan Mayen',	1),
(198,	'sz',	'Swaziland',	1),
(199,	'se',	'Sweden (Sverige)',	1),
(200,	'ch',	'Switzerland (Schweiz)',	1),
(201,	'tw',	'Taiwan (中華民國)',	1),
(202,	'tj',	'Tajikistan (Тоҷикистон)',	1),
(203,	'tz',	'Tanzania',	1),
(204,	'th',	'Thailand (ประเทศไทย)',	1),
(205,	'tg',	'Togo',	1),
(206,	'tk',	'Tokelau',	1),
(207,	'to',	'Tonga',	1),
(208,	'tt',	'Trinidad and Tobago',	1),
(209,	'tn',	'Tunisia (Tunes)',	1),
(210,	'tr',	'Turkey (Türkiye)',	1),
(211,	'tm',	'Turkmenistan (Türkmenistan)',	1),
(212,	'tc',	'Turks and Caicos Islands',	1),
(213,	'tv',	'Tuvalu',	1),
(214,	'ug',	'Uganda',	1),
(215,	'ua',	'Ukraine (Україна)',	1),
(216,	'ae',	'United Arab Emirates (الإمارات العربيّة المتّحدة)',	1),
(217,	'uk',	'United Kingdom',	1),
(218,	'um',	'United States Minor Outlying Islands',	1),
(219,	'uy',	'Uruguay (República Oriental del Uruguay)',	1),
(220,	'uz',	'Uzbekistan (Ўзбекистон)',	1),
(221,	'vu',	'Vanuatu',	1),
(222,	'va',	'Holy See (Vatican City State) (Città del Vaticano)',	1),
(223,	've',	'Venezuela',	1),
(224,	'vn',	'Vietnam (Việt Nam)',	1),
(225,	'vg',	'British Virgin Islands',	1),
(226,	'vi',	'United States Virgin Islands',	1),
(227,	'wf',	'Wallis and Futuna (Wallis-et-Futuna)',	1),
(228,	'eh',	'Western Sahara',	1),
(229,	'ye',	'Yemen (اليمن)',	1),
(230,	'zm',	'Zambia',	1),
(231,	'zw',	'Zimbabwe',	1);

DROP TABLE IF EXISTS `pllanguage`;
CREATE TABLE `pllanguage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alphabet` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `pllanguage` (`id`, `code`, `name`, `alphabet`, `status`) VALUES
(1,	'en',	'English',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(2,	'ar',	'Arabic (العربية)',	',␣,ي,و,ه,ن,م,ل,ك,ق,ف,غ,ع,ظ,ط,ض,ص,ش,س,ز,ر,ذ,د,خ,ح,ج,ث,ت,ب,ا,0,1,2,3,4,5,6,7,8,9',	1),
(3,	'bg',	'Bulgarian (Български)',	',␣,а,б,в,г,д,е,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ь,ю,я,0,1,2,3,4,5,6,7,8,9',	1),
(4,	'ca',	'Catalan (Català)',	',␣,a,b,c,ç,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(5,	'zh-CN',	'Chinese - Simplified (中国 - 简体)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(6,	'zh-TW',	'Chinese - Traditional (中文 - 繁體)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(7,	'hr',	'Croatian (Hrvatski)',	',␣,a,b,c,č,ć,d,đ,e,f,g,h,i,j,k,l,m,n,o,p,r,s,š,t,u,v,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(8,	'cs',	'Czech (Čeština)',	',␣,a,á,b,c,č,d,ď,e,é,ě,f,g,h,i,í,j,k,l,m,n,ň,o,ó,p,q,r,ř,s,š,t,ť,u,ú,ů,v,w,x,y,ý,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(9,	'da',	'Danish (Dansk)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,æ,ø,å,0,1,2,3,4,5,6,7,8,9',	1),
(10,	'et',	'Estonian (Eesti)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,š,z,ž,t,u,v,w,õ,ä,ö,ü,x,y,0,1,2,3,4,5,6,7,8,9',	1),
(11,	'nl',	'Dutch (Nederlands)',	',␣,a,ä,b,c,d,e,ë,f,g,h,i,ï,j,k,l,m,n,o,ö,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(12,	'fi',	'Finnish (Suomi)',	',␣,a,å,ä,b,c,d,e,f,g,h,i,j,k,l,m,n,o,ö,p,q,r,s,š,t,u,v,w,x,y,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(13,	'fr',	'French (Français)',	',␣,a,à,â,b,c,ç,d,e,é,è,ê,ë,f,g,h,i,î,ï,j,k,l,m,n,o,ô,ö,p,q,r,s,t,u,ù,û,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(14,	'de',	'German (Deutsch)',	',␣,a,ä,b,c,d,e,f,g,h,i,j,k,l,m,n,o,ö,p,q,r,s,ß,t,u,ü,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(15,	'el',	'Greek (ελληνικά)',	',␣,α,β,γ,δ,ε,ζ,η,θ,ι,κ,λ,μ,ν,ξ,ο,π,ρ,σ,ς,τ,υ,φ,χ,ψ,ω,0,1,2,3,4,5,6,7,8,9',	1),
(16,	'iw',	'Hebrew (עברית)',	',␣,א,ב,ג,ד,ה,ו,ז,ח,ט,י,כ,ך,ל,מ,ם,נ,ן,ס,ע,פ,ף,צ,ץ,ק,ר,ש,ת,0,1,2,3,4,5,6,7,8,9',	1),
(17,	'hi',	'Hindi (हिंदी)',	',␣,अ,आ,इ,ई,उ,ऊ,ए,ऐ,ओ,औ,ः,ॲ,ऑ,अँ,आँ,ऋ,क,ख,ग,घ,ङ,च,छ,ज,झ,ञ,ट,ठ,ड,ढ,ण,त,थ,द,ध,न,प,फ,ब,भ,म,य,र,ल,व,श,ष,स,ह,क्ष,ज्ञ,त्र,0,1,2,3,4,5,6,7,8,9',	1),
(18,	'hu',	'Hungarian (Magyar)',	',␣,a,á,b,c,d,e,é,f,g,h,i,í,j,k,l,m,n,o,ó,ö,ő,p,q,r,s,t,u,ú,ü,ű,ı,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(19,	'is',	'Icelandic',	',␣,a,á,æ,b,þ,d,ð,e,é,f,g,h,i,í,j,k,l,m,n,o,ó,ö,p,r,s,t,u,ú,v,x,y,ý,0,1,2,3,4,5,6,7,8,9',	1),
(20,	'id',	'Indonesian (Bahasa Indonesia)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(21,	'it',	'Italian (Italiano)',	',␣,a,à,b,c,d,e,é,è,f,g,h,i,í,ì,ï,j,k,l,m,n,o,ó,ò,p,q,r,s,t,u,ú,ù,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(22,	'ja',	'Japanese (日本語)',	',␣,あ,い,う,え,お,か,き,く,け,こ,さ,し,す,せ,そ,た,ち,つ,て,と,な,に,ぬ,ね,の,は,ひ,ふ,へ,ほ,ま,み,む,め,も,や,ゆ,よ,ら,り,る,れ,ろ,わ,を,ん,0,1,2,3,4,5,6,7,8,9',	1),
(23,	'ko',	'Korean (한국어)',	',␣,ㄱ,ㄴ,ㄷ,ㄹ,ㅁ,ㅂ,ㅅ,ㅇ,ㅈ,ㅊ,ㄲ,ㄸ,ㅃ,ㅆ,ㅉ,ㅏ,ㅑ,ㅓ,ㅕ,ㅗ,ㅛ,ㅜ,ㅠ,ㅡ,ㅣ,ㅐ,ㅒ,ㅔ,ㅖ,ㅘ,ㅙ,ㅚ,ㅝ,ㅞ,ㅟ,ㅢ,0,1,2,3,4,5,6,7,8,9',	1),
(24,	'lv',	'Latvian (Latviešu Valoda)',	',␣,a,ā,b,c,č,d,e,ē,g,ģ,h,i,ī,j,k,ķ,l,ļ,m,n,ņ,o,p,r,s,š,t,u,ū,v,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(25,	'lt',	'Lithuanian (Lietuvių Kalba)',	',␣,a,ą,b,c,č,d,e,ę,ė,f,g,h,i,į,y,j,k,l,m,n,o,p,r,s,š,t,u,ų,ū,v,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(26,	'no',	'Norwegian (Norsk)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,æ,ø,å,0,1,2,3,4,5,6,7,8,9',	1),
(27,	'pl',	'Polish (Polski)',	',␣,a,ą,b,c,ć,d,e,ę,f,g,h,i,j,k,l,ł,m,n,ń,o,ó,p,r,s,ś,t,u,w,y,z,ź,ż,0,1,2,3,4,5,6,7,8,9',	1),
(28,	'pt',	'Portuguese (Português)',	'a,c,e,g,i,k,m,o,q,s,u,w,y,0,2,4,6,8,10',	1),
(29,	'ro',	'Romanian (Român)',	',␣,a,ă,â,b,c,d,e,f,g,h,i,î,j,k,l,m,n,o,p,q,r,s,ș,t,ț,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(30,	'ru',	'Russian (Русский)',	',␣,а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,0,1,2,3,4,5,6,7,8,9',	1),
(31,	'sr',	'Serbian (Cрпски)',	',␣,а,b,б,в,c,č,ć,d,đ,г,д,ђ,е,f,g,h,i,ж,з,и,ј,k,к,l,л,љ,m,м,n,н,њ,o,о,п,p,р,r,s,š,с,t,т,u,v,ћ,у,ф,х,ц,ч,џ,ш,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(32,	'sk',	'Slovak (Slovenský)',	',␣,a,á,ä,b,c,č,d,ď,e,é,f,g,h,i,í,j,k,l,ĺ,ľ,m,n,ň,o,ó,ô,p,q,r,s,š,t,ť,u,ú,v,w,x,y,ý,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(33,	'sl',	'Slovenian (Slovenščina)',	',␣,a,b,c,č,ć,d,đ,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,š,t,u,v,z,ž,0,1,2,3,4,5,6,7,8,9',	1),
(34,	'es',	'Spanish (Español)',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,ñ,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(35,	'sv',	'Swedish (Svenska)',	',␣,a,å,ä,b,c,d,e,f,g,h,i,j,k,l,m,n,o,ö,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(36,	'tl',	'Tagalog',	',␣,a,b,c,d,e,f,g,h,i,j,k,l,m,n,ñ,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(37,	'th',	'Thai (ภาษาไทย)',	',␣,ก,ข,ฃ,ค,ฅ,ฆ,ง,จ,ฉ,ช,ซ,ฌ,ญ,ฎ,ฏ,ฐ,ฑ,ฒ,ณ,ด,ต,ถ,ท,ธ,น,บ,ป,ผ,ฝ,พ,ฟ,ภ,ม,ย,ร,ล,ว,ศ,ษ,ส,ห,ฬ,อ,ฮ,0,1,2,3,4,5,6,7,8,9',	1),
(38,	'tr',	'Turkish (Türk)',	',␣,a,b,c,ç,d,e,f,g,ğ,h,ı,i,j,k,l,m,n,o,ö,p,r,s,ş,t,u,ü,v,y,z,0,1,2,3,4,5,6,7,8,9',	1),
(39,	'uk',	'Ukrainian (Українська)',	',␣,а,б,в,г,ґ,д,е,є,ж,з,и,і,ї,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ь,ю,я,0,1,2,3,4,5,6,7,8,9',	1),
(40,	'ur',	'Urdu (اُردُو‎)',	',␣,ا,ب,پ,ت,ٹ,ث,ج,چ,ح,خ,د,ڈ,ذ,ر,ڑ,ز,ژ,س,ش,ص,ض,ط,ظ,ع,غ,ف,ق,ک,گ,ل,م,ن,ں,و,ہ,‍‌ء,ی,ے,0,1,2,3,4,5,6,7,8,9',	1),
(41,	'vi',	'Vietnamese (Việt)',	',␣,a,ă,â,b,c,d,đ,e,ê,g,h,i,k,l,m,n,o,ô,ơ,p,q,r,s,t,u,ư,v,x,y,0,1,2,3,4,5,6,7,8,9',	1);

DROP TABLE IF EXISTS `title_auto_gen`;
CREATE TABLE `title_auto_gen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'id cua bang uses',
  `group_id` int(11) unsigned NOT NULL COMMENT 'id trong title_autogen_group',
  `group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_content` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'noi dung title auto gen',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `title_auto_gen_group`;
CREATE TABLE `title_auto_gen_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'id cua bang users',
  `group_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'mo ta user',
  `role` tinyint(3) unsigned DEFAULT '0' COMMENT '1:admin,0:normal',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0:inactive,1:active',
  `is_default` tinyint(2) unsigned DEFAULT NULL COMMENT '1: la account default cua customer, 0: account tam',
  `fb_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` int(11) unsigned DEFAULT NULL COMMENT 'id trong bang package',
  `package_start_date` int(11) unsigned DEFAULT NULL COMMENT 'ngay dang ky su dung',
  `package_end_date` int(11) unsigned DEFAULT NULL COMMENT 'ngay het han su dung',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `customer_id` (`fb_id`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `user_name`, `description`, `role`, `status`, `is_default`, `fb_id`, `phone`, `package_id`, `package_start_date`, `package_end_date`) VALUES
(9,	'Hòa X',	NULL,	'$2y$10$0mS1yIrvy/mXa2Xli6qaA.SJ.Ha9SZF2LxB0R7.9TDt/4A5SWmREO',	'ohlyP5ooUZjBaVmD7I3rGTOG7n34G2zxpHiQZcqm9pqk7l7Ei3hwzc9UTmBA',	'2017-11-10 17:52:48',	'2017-11-21 10:18:58',	'hoabt2',	NULL,	0,	1,	1,	'1184601585005431',	'01694725828',	1,	1511263138,	1511522338),
(10,	'Binh',	NULL,	'$2y$10$MbuUHB.HPAgv.jLB2kEo1.cPg4wP9Gm49lCoa1vQywBkymynvV59C',	'LXteyQ1RI0KOrzJHVDYZra8TYStvRm3toNgEbgZVvX6S7eLQyw16dqhXOm0b',	'2017-11-10 18:07:39',	'2017-11-10 18:07:39',	'binhbt',	NULL,	0,	1,	1,	'2006879946213660',	'111222',	NULL,	NULL,	NULL),
(11,	'Sang',	NULL,	'$2y$10$YPaLCVTPv.XCFaegiNlP8emZAmUePxTL9RiYVQ15/rIfa9QdYPlXi',	'RWb1MKki1MAp8y4uQYr3Iwp7sfGTfggWMncVBMvw6ui0lhojgOtI9aScMpTP',	'2017-11-11 02:54:16',	'2017-11-11 02:54:16',	'autoseo',	NULL,	0,	1,	1,	'1518186608273689',	'0969567938',	NULL,	NULL,	NULL),
(12,	'Phạm Văn Trường',	NULL,	'$2y$10$CURc0xX9Dw57nBs8eAReZOyeB72TxH2mGPhdlZEua3jYhc.Trsi6q',	'6nQEt8MJMsrT9t3Npg0kcn57e6ffiMegxJ9oa3J7nLe4G3byuEH92eo3O6oO',	'2017-11-11 08:57:00',	'2017-11-21 08:29:48',	'truongpv',	NULL,	0,	0,	1,	'1779322002361262',	'0988666813',	1,	1511256588,	1511515788),
(13,	'nguyen manh tuan',	NULL,	'$2y$10$6EsBMT9PERMGjSfbeOrOSuDvWK24FcB9B1GUnktcDsrQT286e7Muq',	NULL,	'2017-11-15 15:55:13',	'2017-11-15 15:55:13',	'manhtuan87',	NULL,	0,	1,	1,	'788582611314421',	'0934295956',	NULL,	NULL,	NULL),
(14,	'Phạm Trường',	NULL,	'$2y$10$tKzTJsN/bU4f4N6uwGx4v.9TRWWXEn7PpXtbbmzsnnCbU336enxPm',	'1tUqtYCUq23npBJkWbbjD4Mkm8akp0GZCvE3lK1ZcvhWoHqhXouyqVOKIH6n',	'2017-11-21 08:47:04',	'2017-11-21 08:47:10',	'truongpv11',	NULL,	0,	0,	1,	'1779322002361262',	'0977154077',	1,	1511257630,	1511516830);

-- 2017-11-21 15:34:50
