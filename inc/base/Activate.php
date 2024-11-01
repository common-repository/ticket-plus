<?php

/**
 * @package TicketPlus
 */

namespace Tidplus\base;

defined( 'ABSPATH' ) or die( 'You can not access the file directly' );

class Activate
{
	// Main method that activates the plugin
	public static function activate() {
		self::setup_tables();
		flush_rewrite_rules();
	}

	// Method for setting up tables required for the plugin into wp database
	private static function setup_tables() {	
		self::setup_tickets_table();
		self::setup_orders_table();
		self::setup_country_table();
		self::setup_settings_table();
                self::setup_woocommerce_table();
		self::insert_default_data();
	}
        

        // Method for adding tickets table in wp database
	private static function setup_tickets_table() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$sql = "CREATE TABLE IF NOT EXISTS $table (
			`ticket_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`address` text COLLATE utf8_unicode_ci NOT NULL,
                        `ticket_timestamp` int(11) NOT NULL,
                        `ticket_time` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'json',
                        `price` int(11) NOT NULL,
			`status` int(11) DEFAULT '1',
                        `details` text COLLATE utf8_unicode_ci NOT NULL,
			`extra_details` text COLLATE utf8_unicode_ci NOT NULL,
			`comments` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'json',
			`extra` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'json',
                        `units` int(11),
			PRIMARY KEY (`ticket_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	// Method for adding orders table in wp database
	private static function setup_orders_table() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_orders';
		$sql = "CREATE TABLE IF NOT EXISTS $table (
                        `order_id` int(11) NOT NULL AUTO_INCREMENT,
			`ticket_id` int(11) NOT NULL,
			`ticket_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                        `units` int(11),
                        `price` int(11) NOT NULL,
                        `currency` varchar (20) NOT NULL,
                        `payment_method` text COLLATE utf8_unicode_ci NOT NULL,
                        `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                        `user_surname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			`user_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			`user_phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`date_added` TIMESTAMP (6) NOT NULL,
                        `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (`order_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}


	// Method for adding currency table in wp database
	private static function setup_country_table() {
		global $wpdb;

		$table = $wpdb->prefix . 'tidplus_country';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			  `ID` int(5) NOT NULL AUTO_INCREMENT,
			  `name` varchar(20) NOT NULL,
			  `code` varchar(2) NOT NULL,
			  `dial_code` varchar(5) NOT NULL,
			  `currency_name` varchar(20) NOT NULL,
			  `currency_symbol` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `currency_code` varchar(10) NOT NULL,
			  PRIMARY KEY (`ID`),
			  UNIQUE KEY `code` (`code`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

	}

	// Method for adding settings table in wp database
	private static function setup_settings_table() {
		global $wpdb;

		$table = $wpdb->prefix . 'tidplus_settings';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			`settings_id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`description` text COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (`settings_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}
        
        
        // Method for adding settings table in wp database
	private static function setup_woocommerce_table() {
		global $wpdb;

		$table = $wpdb->prefix . 'tidplus_woocommerce';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            cart_item_key VARCHAR(64) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                            token VARCHAR(64) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                            order_item_id BIGINT UNSIGNED DEFAULT '0' NOT NULL,
                                            product_id BIGINT UNSIGNED DEFAULT '0' NOT NULL,
                                            calendar_id BIGINT UNSIGNED DEFAULT '0' NOT NULL,
                                            currency VARCHAR(32) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                            currency_code VARCHAR(8) DEFAULT '' COLLATE utf8_unicode_ci NOT NULL,
                                            data TEXT COLLATE utf8_unicode_ci NOT NULL,
                                            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY cart_item_key (cart_item_key),
                                            KEY token (token),
                                            KEY order_item_id (order_item_id),
                                            KEY product_id (product_id),
                                            KEY date_created (date_created)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

	// Method for inserting default data in the database for this plugin
	private static function insert_default_data() {
//		self::insert_default_ticket();
		self::insert_country_data();
		self::insert_settings_data();

		/**
		 * Author : Nam Loc Vo
		 * email : namloc254@gmail.com
		 * Description : Automatic insert default paypal settings to table tidplus_settings when actived plugin. 
		 */
		self::insert_paypal_settings_data();
	}

	// Method for inserting a ticket for the doctor by default
	private static function insert_default_ticket() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_ticket';
		$sql = "INSERT INTO $table (`ticket_id`, `name`, `address`,`price`, `status`) VALUES 
				(1, 'Default Ticket', 'no address', '', 0);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

	}
        
        
        // Method for inserting a ticket for the doctor by default
	private static function insert_woocommerce_data() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_woocommerce';
		$sql = "INSERT INTO $table (`cart_item_key`, `token`, `order_item_id`,`product_id`, `calendar_id`, `currency`, `currency_code`, `data`, `date_created`) VALUES 
				('', '', 0, 0, 0, '', '', '', '');";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

	}

	// Method for inserting all the country data for convenience
	private static function insert_country_data() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_country';
		$sql = "INSERT INTO $table (`ID`, `name`, `code`, `dial_code`, `currency_name`, `currency_symbol`, `currency_code`) VALUES
			(1, 'Afghanistan', 'AF', '+93', 'Afghan afghani', '؋', 'AFN'),
			(2, 'Aland Islands', 'AX', '+358', '', '', ''),
			(3, 'Albania', 'AL', '+355', 'Albanian lek', 'L', 'ALL'),
			(4, 'Algeria', 'DZ', '+213', 'Algerian dinar', 'د.ج', 'DZD'),
			(5, 'AmericanSamoa', 'AS', '+1684', '', '', ''),
			(6, 'Andorra', 'AD', '+376', 'Euro', '€', 'EUR'),
			(7, 'Angola', 'AO', '+244', 'Angolan kwanza', 'Kz', 'AOA'),
			(8, 'Anguilla', 'AI', '+1264', 'East Caribbean dolla', '$', 'XCD'),
			(9, 'Antarctica', 'AQ', '+672', '', '', ''),
			(10, 'Antigua and Barbuda', 'AG', '+1268', 'East Caribbean dolla', '$', 'XCD'),
			(11, 'Argentina', 'AR', '+54', 'Argentine peso', '$', 'ARS'),
			(12, 'Armenia', 'AM', '+374', 'Armenian dram', '', 'AMD'),
			(13, 'Aruba', 'AW', '+297', 'Aruban florin', 'ƒ', 'AWG'),
			(14, 'Australia', 'AU', '+61', 'Australian dollar', '$', 'AUD'),
			(15, 'Austria', 'AT', '+43', 'Euro', '€', 'EUR'),
			(16, 'Azerbaijan', 'AZ', '+994', 'Azerbaijani manat', '', 'AZN'),
			(17, 'Bahamas', 'BS', '+1242', '', '', ''),
			(18, 'Bahrain', 'BH', '+973', 'Bahraini dinar', '.د.ب', 'BHD'),
			(19, 'Bangladesh', 'BD', '+880', 'Bangladeshi taka', '৳', 'BDT'),
			(20, 'Barbados', 'BB', '+1246', 'Barbadian dollar', '$', 'BBD'),
			(21, 'Belarus', 'BY', '+375', 'Belarusian ruble', 'Br', 'BYR'),
			(22, 'Belgium', 'BE', '+32', 'Euro', '€', 'EUR'),
			(23, 'Belize', 'BZ', '+501', 'Belize dollar', '$', 'BZD'),
			(24, 'Benin', 'BJ', '+229', 'West African CFA fra', 'Fr', 'XOF'),
			(25, 'Bermuda', 'BM', '+1441', 'Bermudian dollar', '$', 'BMD'),
			(26, 'Bhutan', 'BT', '+975', 'Bhutanese ngultrum', 'Nu.', 'BTN'),
			(27, 'Bolivia, Plurination', 'BO', '+591', '', '', ''),
			(28, 'Bosnia and Herzegovi', 'BA', '+387', '', '', ''),
			(29, 'Botswana', 'BW', '+267', 'Botswana pula', 'P', 'BWP'),
			(30, 'Brazil', 'BR', '+55', 'Brazilian real', 'R$', 'BRL'),
			(31, 'British Indian Ocean', 'IO', '+246', '', '', ''),
			(32, 'Brunei Darussalam', 'BN', '+673', '', '', ''),
			(33, 'Bulgaria', 'BG', '+359', 'Bulgarian lev', 'лв', 'BGN'),
			(34, 'Burkina Faso', 'BF', '+226', 'West African CFA fra', 'Fr', 'XOF'),
			(35, 'Burundi', 'BI', '+257', 'Burundian franc', 'Fr', 'BIF'),
			(36, 'Cambodia', 'KH', '+855', 'Cambodian riel', '៛', 'KHR'),
			(37, 'Cameroon', 'CM', '+237', 'Central African CFA ', 'Fr', 'XAF'),
			(38, 'Canada', 'CA', '+1', 'Canadian dollar', '$', 'CAD'),
			(39, 'Cape Verde', 'CV', '+238', 'Cape Verdean escudo', 'Esc or $', 'CVE'),
			(40, 'Cayman Islands', 'KY', '+ 345', 'Cayman Islands dolla', '$', 'KYD'),
			(41, 'Central African Repu', 'CF', '+236', '', '', ''),
			(42, 'Chad', 'TD', '+235', 'Central African CFA ', 'Fr', 'XAF'),
			(43, 'Chile', 'CL', '+56', 'Chilean peso', '$', 'CLP'),
			(44, 'China', 'CN', '+86', 'Chinese yuan', '¥ or 元', 'CNY'),
			(45, 'Christmas Island', 'CX', '+61', '', '', ''),
			(46, 'Cocos (Keeling) Isla', 'CC', '+61', '', '', ''),
			(47, 'Colombia', 'CO', '+57', 'Colombian peso', '$', 'COP'),
			(48, 'Comoros', 'KM', '+269', 'Comorian franc', 'Fr', 'KMF'),
			(49, 'Congo', 'CG', '+242', '', '', ''),
			(50, 'Congo, The Democrati', 'CD', '+243', '', '', ''),
			(51, 'Cook Islands', 'CK', '+682', 'New Zealand dollar', '$', 'NZD'),
			(52, 'Costa Rica', 'CR', '+506', 'Costa Rican colón', '₡', 'CRC'),
			(53, 'Cote d''Ivoire', 'CI', '+225', 'West African CFA fra', 'Fr', 'XOF'),
			(54, 'Croatia', 'HR', '+385', 'Croatian kuna', 'kn', 'HRK'),
			(55, 'Cuba', 'CU', '+53', 'Cuban convertible pe', '$', 'CUC'),
			(56, 'Cyprus', 'CY', '+357', 'Euro', '€', 'EUR'),
			(57, 'Czech Republic', 'CZ', '+420', 'Czech koruna', 'Kč', 'CZK'),
			(58, 'Denmark', 'DK', '+45', 'Danish krone', 'kr', 'DKK'),
			(59, 'Djibouti', 'DJ', '+253', 'Djiboutian franc', 'Fr', 'DJF'),
			(60, 'Dominica', 'DM', '+1767', 'East Caribbean dolla', '$', 'XCD'),
			(61, 'Dominican Republic', 'DO', '+1849', 'Dominican peso', '$', 'DOP'),
			(62, 'Ecuador', 'EC', '+593', 'United States dollar', '$', 'USD'),
			(63, 'Egypt', 'EG', '+20', 'Egyptian pound', '£ or ج.م', 'EGP'),
			(64, 'El Salvador', 'SV', '+503', 'United States dollar', '$', 'USD'),
			(65, 'Equatorial Guinea', 'GQ', '+240', 'Central African CFA ', 'Fr', 'XAF'),
			(66, 'Eritrea', 'ER', '+291', 'Eritrean nakfa', 'Nfk', 'ERN'),
			(67, 'Estonia', 'EE', '+372', 'Euro', '€', 'EUR'),
			(68, 'Ethiopia', 'ET', '+251', 'Ethiopian birr', 'Br', 'ETB'),
			(69, 'Falkland Islands (Ma', 'FK', '+500', '', '', ''),
			(70, 'Faroe Islands', 'FO', '+298', 'Danish krone', 'kr', 'DKK'),
			(71, 'Fiji', 'FJ', '+679', 'Fijian dollar', '$', 'FJD'),
			(72, 'Finland', 'FI', '+358', 'Euro', '€', 'EUR'),
			(73, 'France', 'FR', '+33', 'Euro', '€', 'EUR'),
			(74, 'French Guiana', 'GF', '+594', '', '', ''),
			(75, 'French Polynesia', 'PF', '+689', 'CFP franc', 'Fr', 'XPF'),
			(76, 'Gabon', 'GA', '+241', 'Central African CFA ', 'Fr', 'XAF'),
			(77, 'Gambia', 'GM', '+220', '', '', ''),
			(78, 'Georgia', 'GE', '+995', 'Georgian lari', 'ლ', 'GEL'),
			(79, 'Germany', 'DE', '+49', 'Euro', '€', 'EUR'),
			(80, 'Ghana', 'GH', '+233', 'Ghana cedi', '₵', 'GHS'),
			(81, 'Gibraltar', 'GI', '+350', 'Gibraltar pound', '£', 'GIP'),
			(82, 'Greece', 'GR', '+30', 'Euro', '€', 'EUR'),
			(83, 'Greenland', 'GL', '+299', '', '', ''),
			(84, 'Grenada', 'GD', '+1473', 'East Caribbean dolla', '$', 'XCD'),
			(85, 'Guadeloupe', 'GP', '+590', '', '', ''),
			(86, 'Guam', 'GU', '+1671', '', '', ''),
			(87, 'Guatemala', 'GT', '+502', 'Guatemalan quetzal', 'Q', 'GTQ'),
			(88, 'Guernsey', 'GG', '+44', 'British pound', '£', 'GBP'),
			(89, 'Guinea', 'GN', '+224', 'Guinean franc', 'Fr', 'GNF'),
			(90, 'Guinea-Bissau', 'GW', '+245', 'West African CFA fra', 'Fr', 'XOF'),
			(91, 'Guyana', 'GY', '+595', 'Guyanese dollar', '$', 'GYD'),
			(92, 'Haiti', 'HT', '+509', 'Haitian gourde', 'G', 'HTG'),
			(93, 'Holy See (Vatican Ci', 'VA', '+379', '', '', ''),
			(94, 'Honduras', 'HN', '+504', 'Honduran lempira', 'L', 'HNL'),
			(95, 'Hong Kong', 'HK', '+852', 'Hong Kong dollar', '$', 'HKD'),
			(96, 'Hungary', 'HU', '+36', 'Hungarian forint', 'Ft', 'HUF'),
			(97, 'Iceland', 'IS', '+354', 'Icelandic króna', 'kr', 'ISK'),
			(98, 'India', 'IN', '+91', 'Indian rupee', '₹', 'INR'),
			(99, 'Indonesia', 'ID', '+62', 'Indonesian rupiah', 'Rp', 'IDR'),
			(100, 'Iran, Islamic Republ', 'IR', '+98', '', '', ''),
			(101, 'Iraq', 'IQ', '+964', 'Iraqi dinar', 'ع.د', 'IQD'),
			(102, 'Ireland', 'IE', '+353', 'Euro', '€', 'EUR'),
			(103, 'Isle of Man', 'IM', '+44', 'British pound', '£', 'GBP'),
			(104, 'Israel', 'IL', '+972', 'Israeli new shekel', '₪', 'ILS'),
			(105, 'Italy', 'IT', '+39', 'Euro', '€', 'EUR'),
			(106, 'Jamaica', 'JM', '+1876', 'Jamaican dollar', '$', 'JMD'),
			(107, 'Japan', 'JP', '+81', 'Japanese yen', '¥', 'JPY'),
			(108, 'Jersey', 'JE', '+44', 'British pound', '£', 'GBP'),
			(109, 'Jordan', 'JO', '+962', 'Jordanian dinar', 'د.ا', 'JOD'),
			(110, 'Kazakhstan', 'KZ', '+7 7', 'Kazakhstani tenge', '', 'KZT'),
			(111, 'Kenya', 'KE', '+254', 'Kenyan shilling', 'Sh', 'KES'),
			(112, 'Kiribati', 'KI', '+686', 'Australian dollar', '$', 'AUD'),
			(113, 'Korea, Democratic Pe', 'KP', '+850', '', '', ''),
			(114, 'Korea, Republic of S', 'KR', '+82', '', '', ''),
			(115, 'Kuwait', 'KW', '+965', 'Kuwaiti dinar', 'د.ك', 'KWD'),
			(116, 'Kyrgyzstan', 'KG', '+996', 'Kyrgyzstani som', 'лв', 'KGS'),
			(117, 'Laos', 'LA', '+856', 'Lao kip', '₭', 'LAK'),
			(118, 'Latvia', 'LV', '+371', 'Euro', '€', 'EUR'),
			(119, 'Lebanon', 'LB', '+961', 'Lebanese pound', 'ل.ل', 'LBP'),
			(120, 'Lesotho', 'LS', '+266', 'Lesotho loti', 'L', 'LSL'),
			(121, 'Liberia', 'LR', '+231', 'Liberian dollar', '$', 'LRD'),
			(122, 'Libyan Arab Jamahiri', 'LY', '+218', '', '', ''),
			(123, 'Liechtenstein', 'LI', '+423', 'Swiss franc', 'Fr', 'CHF'),
			(124, 'Lithuania', 'LT', '+370', 'Euro', '€', 'EUR'),
			(125, 'Luxembourg', 'LU', '+352', 'Euro', '€', 'EUR'),
			(126, 'Macao', 'MO', '+853', '', '', ''),
			(127, 'Macedonia', 'MK', '+389', '', '', ''),
			(128, 'Madagascar', 'MG', '+261', 'Malagasy ariary', 'Ar', 'MGA'),
			(129, 'Malawi', 'MW', '+265', 'Malawian kwacha', 'MK', 'MWK'),
			(130, 'Malaysia', 'MY', '+60', 'Malaysian ringgit', 'RM', 'MYR'),
			(131, 'Maldives', 'MV', '+960', 'Maldivian rufiyaa', '.ރ', 'MVR'),
			(132, 'Mali', 'ML', '+223', 'West African CFA fra', 'Fr', 'XOF'),
			(133, 'Malta', 'MT', '+356', 'Euro', '€', 'EUR'),
			(134, 'Marshall Islands', 'MH', '+692', 'United States dollar', '$', 'USD'),
			(135, 'Martinique', 'MQ', '+596', '', '', ''),
			(136, 'Mauritania', 'MR', '+222', 'Mauritanian ouguiya', 'UM', 'MRO'),
			(137, 'Mauritius', 'MU', '+230', 'Mauritian rupee', '₨', 'MUR'),
			(138, 'Mayotte', 'YT', '+262', '', '', ''),
			(139, 'Mexico', 'MX', '+52', 'Mexican peso', '$', 'MXN'),
			(140, 'Micronesia, Federate', 'FM', '+691', '', '', ''),
			(141, 'Moldova', 'MD', '+373', 'Moldovan leu', 'L', 'MDL'),
			(142, 'Monaco', 'MC', '+377', 'Euro', '€', 'EUR'),
			(143, 'Mongolia', 'MN', '+976', 'Mongolian tögrög', '₮', 'MNT'),
			(144, 'Montenegro', 'ME', '+382', 'Euro', '€', 'EUR'),
			(145, 'Montserrat', 'MS', '+1664', 'East Caribbean dolla', '$', 'XCD'),
			(146, 'Morocco', 'MA', '+212', 'Moroccan dirham', 'د.م.', 'MAD'),
			(147, 'Mozambique', 'MZ', '+258', 'Mozambican metical', 'MT', 'MZN'),
			(148, 'Myanmar', 'MM', '+95', 'Burmese kyat', 'Ks', 'MMK'),
			(149, 'Namibia', 'NA', '+264', 'Namibian dollar', '$', 'NAD'),
			(150, 'Nauru', 'NR', '+674', 'Australian dollar', '$', 'AUD'),
			(151, 'Nepal', 'NP', '+977', 'Nepalese rupee', '₨', 'NPR'),
			(152, 'Netherlands', 'NL', '+31', 'Euro', '€', 'EUR'),
			(153, 'Netherlands Antilles', 'AN', '+599', '', '', ''),
			(154, 'New Caledonia', 'NC', '+687', 'CFP franc', 'Fr', 'XPF'),
			(155, 'New Zealand', 'NZ', '+64', 'New Zealand dollar', '$', 'NZD'),
			(156, 'Nicaragua', 'NI', '+505', 'Nicaraguan córdoba', 'C$', 'NIO'),
			(157, 'Niger', 'NE', '+227', 'West African CFA fra', 'Fr', 'XOF'),
			(158, 'Nigeria', 'NG', '+234', 'Nigerian naira', '₦', 'NGN'),
			(159, 'Niue', 'NU', '+683', 'New Zealand dollar', '$', 'NZD'),
			(160, 'Norfolk Island', 'NF', '+672', '', '', ''),
			(161, 'Northern Mariana Isl', 'MP', '+1670', '', '', ''),
			(162, 'Norway', 'NO', '+47', 'Norwegian krone', 'kr', 'NOK'),
			(163, 'Oman', 'OM', '+968', 'Omani rial', 'ر.ع.', 'OMR'),
			(164, 'Pakistan', 'PK', '+92', 'Pakistani rupee', '₨', 'PKR'),
			(165, 'Palau', 'PW', '+680', 'Palauan dollar', '$', ''),
			(166, 'Palestinian Territor', 'PS', '+970', '', '', ''),
			(167, 'Panama', 'PA', '+507', 'Panamanian balboa', 'B/.', 'PAB'),
			(168, 'Papua New Guinea', 'PG', '+675', 'Papua New Guinean ki', 'K', 'PGK'),
			(169, 'Paraguay', 'PY', '+595', 'Paraguayan guaraní', '₲', 'PYG'),
			(170, 'Peru', 'PE', '+51', 'Peruvian nuevo sol', 'S/.', 'PEN'),
			(171, 'Philippines', 'PH', '+63', 'Philippine peso', '₱', 'PHP'),
			(172, 'Pitcairn', 'PN', '+872', '', '', ''),
			(173, 'Poland', 'PL', '+48', 'Polish z?oty', 'zł', 'PLN'),
			(174, 'Portugal', 'PT', '+351', 'Euro', '€', 'EUR'),
			(175, 'Puerto Rico', 'PR', '+1939', '', '', ''),
			(176, 'Qatar', 'QA', '+974', 'Qatari riyal', 'ر.ق', 'QAR'),
			(177, 'Romania', 'RO', '+40', 'Romanian leu', 'lei', 'RON'),
			(178, 'Russia', 'RU', '+7', 'Russian ruble', '', 'RUB'),
			(179, 'Rwanda', 'RW', '+250', 'Rwandan franc', 'Fr', 'RWF'),
			(180, 'Reunion', 'RE', '+262', '', '', ''),
			(181, 'Saint Barthelemy', 'BL', '+590', '', '', ''),
			(182, 'Saint Helena, Ascens', 'SH', '+290', '', '', ''),
			(183, 'Saint Kitts and Nevi', 'KN', '+1869', '', '', ''),
			(184, 'Saint Lucia', 'LC', '+1758', 'East Caribbean dolla', '$', 'XCD'),
			(185, 'Saint Martin', 'MF', '+590', '', '', ''),
			(186, 'Saint Pierre and Miq', 'PM', '+508', '', '', ''),
			(187, 'Saint Vincent and th', 'VC', '+1784', '', '', ''),
			(188, 'Samoa', 'WS', '+685', 'Samoan t?l?', 'T', 'WST'),
			(189, 'San Marino', 'SM', '+378', 'Euro', '€', 'EUR'),
			(190, 'Sao Tome and Princip', 'ST', '+239', '', '', ''),
			(191, 'Saudi Arabia', 'SA', '+966', 'Saudi riyal', 'ر.س', 'SAR'),
			(192, 'Senegal', 'SN', '+221', 'West African CFA fra', 'Fr', 'XOF'),
			(193, 'Serbia', 'RS', '+381', 'Serbian dinar', 'дин. or din.', 'RSD'),
			(194, 'Seychelles', 'SC', '+248', 'Seychellois rupee', '₨', 'SCR'),
			(195, 'Sierra Leone', 'SL', '+232', 'Sierra Leonean leone', 'Le', 'SLL'),
			(196, 'Singapore', 'SG', '+65', 'Brunei dollar', '$', 'BND'),
			(197, 'Slovakia', 'SK', '+421', 'Euro', '€', 'EUR'),
			(198, 'Slovenia', 'SI', '+386', 'Euro', '€', 'EUR'),
			(199, 'Solomon Islands', 'SB', '+677', 'Solomon Islands doll', '$', 'SBD'),
			(200, 'Somalia', 'SO', '+252', 'Somali shilling', 'Sh', 'SOS'),
			(201, 'South Africa', 'ZA', '+27', 'South African rand', 'R', 'ZAR'),
			(202, 'South Georgia and th', 'GS', '+500', '', '', ''),
			(203, 'Spain', 'ES', '+34', 'Euro', '€', 'EUR'),
			(204, 'Sri Lanka', 'LK', '+94', 'Sri Lankan rupee', 'Rs or රු', 'LKR'),
			(205, 'Sudan', 'SD', '+249', 'Sudanese pound', 'ج.س.', 'SDG'),
			(206, 'Suriname', 'SR', '+597', 'Surinamese dollar', '$', 'SRD'),
			(207, 'Svalbard and Jan May', 'SJ', '+47', '', '', ''),
			(208, 'Swaziland', 'SZ', '+268', 'Swazi lilangeni', 'L', 'SZL'),
			(209, 'Sweden', 'SE', '+46', 'Swedish krona', 'kr', 'SEK'),
			(210, 'Switzerland', 'CH', '+41', 'Swiss franc', 'Fr', 'CHF'),
			(211, 'Syrian Arab Republic', 'SY', '+963', '', '', ''),
			(212, 'Taiwan', 'TW', '+886', 'New Taiwan dollar', '$', 'TWD'),
			(213, 'Tajikistan', 'TJ', '+992', 'Tajikistani somoni', 'ЅМ', 'TJS'),
			(214, 'Tanzania, United Rep', 'TZ', '+255', '', '', ''),
			(215, 'Thailand', 'TH', '+66', 'Thai baht', '฿', 'THB'),
			(216, 'Timor-Leste', 'TL', '+670', '', '', ''),
			(217, 'Togo', 'TG', '+228', 'West African CFA fra', 'Fr', 'XOF'),
			(218, 'Tokelau', 'TK', '+690', '', '', ''),
			(219, 'Tonga', 'TO', '+676', 'Tongan pa?anga', 'T$', 'TOP'),
			(220, 'Trinidad and Tobago', 'TT', '+1868', 'Trinidad and Tobago ', '$', 'TTD'),
			(221, 'Tunisia', 'TN', '+216', 'Tunisian dinar', 'د.ت', 'TND'),
			(222, 'Turkey', 'TR', '+90', 'Turkish lira', '', 'TRY'),
			(223, 'Turkmenistan', 'TM', '+993', 'Turkmenistan manat', 'm', 'TMT'),
			(224, 'Turks and Caicos Isl', 'TC', '+1649', '', '', ''),
			(225, 'Tuvalu', 'TV', '+688', 'Australian dollar', '$', 'AUD'),
			(226, 'Uganda', 'UG', '+256', 'Ugandan shilling', 'Sh', 'UGX'),
			(227, 'Ukraine', 'UA', '+380', 'Ukrainian hryvnia', '₴', 'UAH'),
			(228, 'United Arab Emirates', 'AE', '+971', 'United Arab Emirates', 'د.إ', 'AED'),
			(229, 'United Kingdom', 'GB', '+44', 'British pound', '£', 'GBP'),
			(230, 'United States', 'US', '+1', 'United States dollar', '$', 'USD'),
			(231, 'Uruguay', 'UY', '+598', 'Uruguayan peso', '$', 'UYU'),
			(232, 'Uzbekistan', 'UZ', '+998', 'Uzbekistani som', '', 'UZS'),
			(233, 'Vanuatu', 'VU', '+678', 'Vanuatu vatu', 'Vt', 'VUV'),
			(234, 'Venezuela, Bolivaria', 'VE', '+58', '', '', ''),
			(235, 'Vietnam', 'VN', '+84', 'Vietnamese ??ng', '₫', 'VND'),
			(236, 'Virgin Islands, Brit', 'VG', '+1284', '', '', ''),
			(237, 'Virgin Islands, U.S.', 'VI', '+1340', '', '', ''),
			(238, 'Wallis and Futuna', 'WF', '+681', 'CFP franc', 'Fr', 'XPF'),
			(239, 'Yemen', 'YE', '+967', 'Yemeni rial', '﷼', 'YER'),
			(240, 'Zambia', 'ZM', '+260', 'Zambian kwacha', 'ZK', 'ZMW'),
			(241, 'Zimbabwe', 'ZW', '+263', 'Botswana pula', 'P', 'BWP');";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

	// Method for inserting default settings data into the table for this plugin
	private static function insert_settings_data() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_settings';
		$sql = "INSERT INTO $table (`settings_id`, `type`, `description`) VALUES 
				(1, 'default_ticket_id', '1'),
				(2, 'default_currency', '230'),
                                (3, 'customer_notification', ''),
                                (4, 'admin_notification', ''),
				(5, 'default_email', ' '),
                                (6, 'email_name', ' '),
				(7, 'organiser_name', ' '),
				(8, 'phone_number', ' '),
                                (9, 'terms_conditions', ' '),
                                (10, 'terms_conditions_link', ' '),
                                (11, 'show_ticket_info', ' '),
                                (12, 'manual_approve', ' ');";   
				

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

	/**
	 * Void
	 * Author : Nam Loc Vo
	 * email : namloc254@gmail.com
	 * Description : Inseart default paypal settings to table tidplus_settings when actived plugin
	 */
	private static function insert_paypal_settings_data() {
		global $wpdb;
		$table = $wpdb->prefix . 'tidplus_settings';
		$sql = "INSERT INTO $table (`settings_id`, `type`, `description`) VALUES 
				(13, 'paypal_enable', ''),
				(14, 'paypal_email', ''),
				(15, 'paypal_live_client_id', ''),
				(16, 'paypal_live_client_secret', ''),
				(17, 'paypal_sandbox_mode', ''),
				(18, 'paypal_test_client_id', ''),
				(19, 'paypal_test_client_secret', '')";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

}