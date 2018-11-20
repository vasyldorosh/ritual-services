
--
-- Data for table `structure_page`
--

INSERT INTO `structure_page` (`id`, `structure_id`, `template_id`, `alias`, `is_search`, `is_canonical`, `is_not_breadcrumbs`, `is_not_menu`, `domain_id`) VALUES
 ('1', '000001', '1', 'index', '0', '0', '0', '0', '1');



--
-- Data for table `structure_page_lang`
--

INSERT INTO `structure_page_lang` (`id`, `owner_id`, `lang`, `title`, `head`, `description`, `h1`) VALUES
 ('1', '1', 'ru', 'Главная - 50kopeek.loc', '', '', '');



--
-- Data for table `structure_domain`
--

INSERT INTO `structure_domain` (`id`, `alias`, `is_active`, `is_root`, `template_id`) VALUES
 ('1', '50kopeek.loc', '1', '1', '1');


