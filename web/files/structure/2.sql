
--
-- Data for table `structure_page`
--

INSERT INTO `structure_page` (`id`, `structure_id`, `template_id`, `alias`, `is_search`, `is_canonical`, `is_not_breadcrumbs`, `is_not_menu`, `domain_id`) VALUES
 ('1', '000001', '1', 'index', '0', '0', '0', '0', '1'),
 ('2', '000001000001', '1', 't1', '0', '0', '0', '0', '1'),
 ('3', '000001000002', '1', 't2', '0', '0', '0', '0', '1'),
 ('4', '000001000001000001', '1', 't1.1', '0', '0', '0', '0', '1'),
 ('5', '000001000002000001', '1', 't2.1', '0', '0', '0', '0', '1'),
 ('72', '000002', '1', 'index', '0', '0', '0', '0', '40'),
 ('73', '000002000001', '1', 't1', '0', '0', '0', '0', '40'),
 ('74', '000002000002', '1', 't2', '0', '0', '0', '0', '40'),
 ('75', '000002000001000001', '1', 't1.1', '0', '0', '0', '0', '40'),
 ('76', '000002000002000001', '1', 't2.1', '0', '0', '0', '0', '40');



--
-- Data for table `structure_page_lang`
--

INSERT INTO `structure_page_lang` (`id`, `owner_id`, `lang`, `title`, `head`, `description`, `h1`) VALUES
 ('1', '1', 'ru', 'Главная - 50kopeek.loc', '', '', ''),
 ('2', '2', 'ru', 't1', '', '', ''),
 ('3', '2', 'ua', '', '', '', ''),
 ('4', '3', 'ru', 't2', '', '', ''),
 ('5', '3', 'ua', '', '', '', ''),
 ('6', '4', 'ru', 't1.1', '', '', ''),
 ('7', '4', 'ua', '', '', '', ''),
 ('8', '5', 'ru', 't2.1', '', '', ''),
 ('9', '5', 'ua', '', '', '', ''),
 ('14', '1', 'ua', 'Главная - 50kopeek.loc', 'qqq', '', ''),
 ('137', '72', 'ru', 'Главная - acms9-en.loc', '', '', ''),
 ('138', '72', 'ua', '', '', '', ''),
 ('139', '73', 'ru', 't1', '', '', ''),
 ('140', '73', 'ua', '', '', '', ''),
 ('141', '74', 'ru', 't2', '', '', ''),
 ('142', '74', 'ua', '', '', '', ''),
 ('143', '75', 'ru', 't1.1', '', '', ''),
 ('144', '75', 'ua', '', '', '', ''),
 ('145', '76', 'ru', 't2.1', '', '', ''),
 ('146', '76', 'ua', '', '', '', '');



--
-- Data for table `structure_page_block`
--

INSERT INTO `structure_page_block` (`alias`, `page_id`, `content`, `type_id`, `priority`) VALUES
 ('content1', '1', 'a:4:{i:0;s:4:\"menu\";s:6:\"action\";s:5:\"index\";s:7:\"menu_id\";s:1:\"1\";s:8:\"template\";s:6:\"header\";}', '1', '0'),
 ('content1', '72', 'a:4:{i:0;s:4:\"menu\";s:6:\"action\";s:5:\"index\";s:7:\"menu_id\";s:1:\"1\";s:8:\"template\";s:6:\"header\";}', '1', '0'),
 ('content4', '1', 'a:4:{i:0;s:4:\"menu\";s:6:\"action\";s:5:\"index\";s:7:\"menu_id\";s:1:\"1\";s:8:\"template\";s:6:\"footer\";}', '1', '0'),
 ('content4', '72', 'a:4:{i:0;s:4:\"menu\";s:6:\"action\";s:5:\"index\";s:7:\"menu_id\";s:1:\"1\";s:8:\"template\";s:6:\"footer\";}', '1', '0');



--
-- Data for table `structure_domain`
--

INSERT INTO `structure_domain` (`id`, `alias`, `is_active`, `is_root`, `template_id`) VALUES
 ('1', '50kopeek.loc', '1', '1', '1'),
 ('40', 'acms9-en.loc', '1', '0', '1');



--
-- Data for table `menu`
--

INSERT INTO `menu` (`id`, `title`, `type`, `is_active`) VALUES
 ('1', 'ффф', 'NESTED', '1');



--
-- Data for table `menu_link`
--

INSERT INTO `menu_link` (`id`, `parent_id`, `menu_id`, `is_active`, `link`, `class`, `style`, `page_id`, `rank`, `image`) VALUES
 ('1', NULL, '1', '1', NULL, NULL, NULL, '2', '1', NULL),
 ('2', '1', '1', '1', NULL, NULL, NULL, '4', '1', NULL),
 ('3', NULL, '1', '1', NULL, NULL, NULL, '3', '2', NULL),
 ('4', '3', '1', '1', NULL, NULL, NULL, '5', '1', NULL);



--
-- Data for table `menu_link_lang`
--

INSERT INTO `menu_link_lang` (`id`, `owner_id`, `lang`, `title`) VALUES
 ('1', '1', 'ru', 't1'),
 ('2', '2', 'ru', 't1.1'),
 ('3', '3', 'ru', 't2'),
 ('4', '4', 'ru', 't2.1');


