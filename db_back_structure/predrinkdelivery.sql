-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 10, 2020 at 04:26 AM
-- Server version: 5.7.31-0ubuntu0.16.04.1
-- PHP Version: 7.0.33-0ubuntu0.16.04.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `predrinkdelivery`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_distance_metres` (`lat1` DOUBLE, `lng1` DOUBLE, `lat2` DOUBLE, `lng2` DOUBLE) RETURNS DOUBLE BEGIN
    DECLARE rlo1 DOUBLE;
    DECLARE rla1 DOUBLE;
    DECLARE rlo2 DOUBLE;
    DECLARE rla2 DOUBLE;
    DECLARE dlo DOUBLE;
    DECLARE dla DOUBLE;
    DECLARE a DOUBLE;
    
    SET rlo1 = RADIANS(lng1);
    SET rla1 = RADIANS(lat1);
    SET rlo2 = RADIANS(lng2);
    SET rla2 = RADIANS(lat2);
    SET dlo = (rlo2 - rlo1) / 2;
    SET dla = (rla2 - rla1) / 2;
    SET a = SIN(dla) * SIN(dla) + COS(rla1) * COS(rla2) * SIN(dlo) * SIN(dlo);
    RETURN (6378137 * 2 * ATAN2(SQRT(a), SQRT(1 - a)));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Admin, 2 = Website Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `alcohol_awareness`
--

CREATE TABLE `alcohol_awareness` (
  `aid` int(11) NOT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `block_users`
--

CREATE TABLE `block_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blocked_user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bonus_list`
--

CREATE TABLE `bonus_list` (
  `bonus_id` int(11) NOT NULL,
  `bonus_type` mediumtext CHARACTER SET utf8mb4,
  `bonus_amount` decimal(10,2) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `no_of_trips` int(11) NOT NULL,
  `no_of_deliveries` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `new_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive / expired'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `brand_category_allocation`
--

CREATE TABLE `brand_category_allocation` (
  `brand_category_allocation_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active , 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `brand_mst`
--

CREATE TABLE `brand_mst` (
  `brand_id` int(11) NOT NULL,
  `brand_code` varchar(100) NOT NULL,
  `brand_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `brand_logo` varchar(255) NOT NULL,
  `is_top_brand` tinyint(4) NOT NULL DEFAULT '0',
  `in_loyalty_club` tinyint(4) NOT NULL COMMENT '1 = Yes, 0= No',
  `slider_img` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cancel_order_reasons`
--

CREATE TABLE `cancel_order_reasons` (
  `creason_id` int(11) NOT NULL,
  `reason` longtext CHARACTER SET utf8mb4,
  `other_reason` longtext CHARACTER SET utf8mb4,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_qty` double NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `promocode_id` int(11) NOT NULL,
  `gift_card_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_charge` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cart_product`
--

CREATE TABLE `cart_product` (
  `cart_product_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `volume_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` double NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `from_where` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Normal, 2 = Loyalty Club, 3 = VIP Club',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `allow_split_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category_mst`
--

CREATE TABLE `category_mst` (
  `category_id` int(11) NOT NULL,
  `category_code` varchar(100) NOT NULL,
  `category_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `category_img` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `in_loyalty_club` tinyint(4) NOT NULL COMMENT '1 = Yes, 0= No',
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL,
  `message_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `offline_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '''send'',''receive''',
  `message` longtext CHARACTER SET utf8mb4,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_thumb` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `msg_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `seen_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_us_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext,
  `contactno` varchar(25) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(6) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_charges`
--

CREATE TABLE `delivery_charges` (
  `charge_id` int(11) NOT NULL,
  `miles` decimal(10,2) DEFAULT NULL,
  `base_rate` decimal(10,2) DEFAULT NULL,
  `pay_driver_pickup` decimal(10,2) DEFAULT NULL,
  `pay_driver_dropoff` decimal(10,2) DEFAULT NULL,
  `dzone_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_receipt`
--

CREATE TABLE `delivery_receipt` (
  `delivery_receipt_id` int(11) NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `otp` varchar(25) NOT NULL,
  `delivery_status` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = normal user, 1 = seller'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zone`
--

CREATE TABLE `delivery_zone` (
  `dzone_id` int(11) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `area_code` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=> active, 0=> Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `device_token`
--

CREATE TABLE `device_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_token` text NOT NULL,
  `device_id` varchar(225) NOT NULL,
  `device_name` varchar(225) NOT NULL,
  `device_type` varchar(50) NOT NULL,
  `app_version` double NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=true.0=false'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_bonus`
--

CREATE TABLE `driver_bonus` (
  `driver_bonus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bonus_id` int(11) DEFAULT NULL,
  `bonus_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Normal Bonus, 2 = Bonus Provided By Admin',
  `order_id` int(11) DEFAULT NULL,
  `reason` longtext,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_by_invite`
--

CREATE TABLE `driver_by_invite` (
  `ref_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_docs`
--

CREATE TABLE `driver_docs` (
  `driver_doc_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Active, 0 =Inactive',
  `delete_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = yes, 0 = no'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_earnings`
--

CREATE TABLE `driver_earnings` (
  `earning_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tips` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_requests`
--

CREATE TABLE `driver_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'driver_id',
  `request` longtext CHARACTER SET utf8,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `birthdate` varchar(255) DEFAULT NULL,
  `mobileno` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Active, 0 =Inactive',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_schedule`
--

CREATE TABLE `driver_schedule` (
  `sch_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_target`
--

CREATE TABLE `driver_target` (
  `target_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = daily, 2 = weekly, 3 = monthly',
  `target_amount` decimal(10,2) NOT NULL,
  `target_start_date` date DEFAULT NULL,
  `target_end_date` date DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_vehicle_image_request`
--

CREATE TABLE `driver_vehicle_image_request` (
  `vehicle_image_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Active, 0 =Inactive',
  `request_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver_vehicle_requests`
--

CREATE TABLE `driver_vehicle_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'driver_id',
  `request` longtext CHARACTER SET utf8,
  `vehicle_id` int(11) DEFAULT NULL,
  `car_name` varchar(255) DEFAULT NULL,
  `vehicle_reg_no` varchar(255) DEFAULT NULL,
  `vehicle_make` varchar(255) DEFAULT NULL,
  `ins_policy_no` varchar(255) DEFAULT NULL,
  `ins_certificate_no` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Active, 0 =Inactive',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `faq_mst`
--

CREATE TABLE `faq_mst` (
  `faq_id` int(11) NOT NULL,
  `faq_question` varchar(1000) CHARACTER SET utf8mb4 NOT NULL,
  `faq_answer` text CHARACTER SET utf8mb4 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gift_card`
--

CREATE TABLE `gift_card` (
  `card_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1= active , 0 = inactive',
  `redeem_code` varchar(255) DEFAULT NULL,
  `is_redeem` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = yes, 0 = no',
  `gift_car_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gift_card_history`
--

CREATE TABLE `gift_card_history` (
  `gift_card_history_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_history` longtext NOT NULL,
  `payment_status` varchar(255) NOT NULL COMMENT '1 = Pending, 2 = Failed, 3 = Successful ',
  `used_amount` decimal(10,2) NOT NULL,
  `balance_amount` decimal(10,2) NOT NULL,
  `temp_used_amount` decimal(10,2) NOT NULL,
  `temp_balance_amount` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `group_member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = admin, 0 =not admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Active, 0 = Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `help_support`
--

CREATE TABLE `help_support` (
  `help_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `subject` tinytext NOT NULL,
  `message` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = User, 2 = Seller, 3 = Driver',
  `mobileno` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logtbl`
--

CREATE TABLE `logtbl` (
  `logid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime NOT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Normal User, 2 = Driver'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `module_access`
--

CREATE TABLE `module_access` (
  `module_access_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notcompleted_order_reasons`
--

CREATE TABLE `notcompleted_order_reasons` (
  `ncreason_id` int(11) NOT NULL,
  `reason` longtext,
  `other_reason` longtext,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `notification_status` int(11) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `message` text CHARACTER SET utf8mb4 NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_seller` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = User, 1 = Seller',
  `driver_id` int(11) DEFAULT NULL,
  `is_accepted` tinyint(4) DEFAULT '0' COMMENT '0 = Pending, 1 = Accepted, 2 = Rejected',
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `notification_count` int(11) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `is_notified` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = yes, 0 = no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notification_type_mst`
--

CREATE TABLE `notification_type_mst` (
  `notification_type_id` int(11) NOT NULL,
  `notification` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `offline_data`
--

CREATE TABLE `offline_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method` varchar(255) NOT NULL,
  `json` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_no` varchar(255) NOT NULL,
  `track_no` int(11) DEFAULT NULL,
  `order_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Pending, 2 = accepted by seller, 3 = accepted by driver, 4 = Delivered, 5 = Cancelled, 6 = Order Placed, 7 = reject by seller, 8 = cancel by driver, 9 = picked up, 10 = start delivery, 11 = end delivery, 12 = pause, 13 = not completed',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delivered_date` date NOT NULL,
  `delivered_time` varchar(255) DEFAULT NULL,
  `order_payment_type` tinyint(4) DEFAULT NULL COMMENT '1 = Card Payment, 2 = COD, 3 = Gift Card Payment, 4 = Wallet',
  `delivery_charges` decimal(10,2) NOT NULL,
  `tips` decimal(10,2) DEFAULT NULL,
  `wallet_amount` decimal(10,2) NOT NULL,
  `shipping_id` int(11) NOT NULL,
  `promocode_id` int(11) NOT NULL,
  `gift_card_id` int(11) NOT NULL,
  `loyalty_point` int(11) NOT NULL,
  `total_qty` double NOT NULL,
  `total_tax` decimal(10,2) NOT NULL,
  `total_discount` decimal(10,2) NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `updated_date` timestamp NULL DEFAULT NULL,
  `send_as_gift` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `payment_done` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `amount_paid` decimal(10,2) NOT NULL,
  `order_code` text NOT NULL,
  `order_cancellation_reason` longtext CHARACTER SET utf8mb4,
  `to_be_delivered_date` date DEFAULT NULL,
  `to_be_delivered_date_utc` date DEFAULT NULL,
  `start_slot` varchar(255) DEFAULT NULL,
  `end_slot` varchar(255) DEFAULT NULL,
  `no_completion_reason` int(11) DEFAULT NULL,
  `not_completed_reason_other` longtext CHARACTER SET utf8mb4,
  `driver_cancel` int(11) DEFAULT NULL,
  `is_pick_up` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No ( for user)',
  `is_repeat_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No ',
  `order_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = normal order,  2 = Repeat Order',
  `add_info` longtext CHARACTER SET utf8mb4,
  `order_done_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Order now, 2= Order schedule'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_canceled`
--

CREATE TABLE `order_canceled` (
  `order_canceled_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `reason` longtext,
  `is_confirmed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Accepted, 0 = Pending, 2 = Rejected',
  `amount_refunded` decimal(10,2) DEFAULT '0.00',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` varchar(255) DEFAULT NULL,
  `payment_history` longtext,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_delivery`
--

CREATE TABLE `order_delivery` (
  `order_delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_job_id` int(11) DEFAULT NULL,
  `job_id` varchar(255) DEFAULT NULL,
  `delivery_type` enum('Janet-Collection','stuart') NOT NULL DEFAULT 'Janet-Collection' COMMENT '1  = Janet-Collection, 2 = Stuart',
  `user_id` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `transport_type` varchar(255) DEFAULT NULL,
  `eta_destination` varchar(255) DEFAULT NULL,
  `eta_origin` varchar(255) DEFAULT NULL,
  `driver` longtext,
  `origin_comment` text,
  `destination_comment` text,
  `delivery_id` varchar(255) DEFAULT NULL,
  `delivery_status` varchar(255) DEFAULT NULL,
  `proof` text,
  `cancellation` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_driver`
--

CREATE TABLE `order_driver` (
  `order_driver_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = Pending, 1 = Accepted, 2 = Rejected, 3 = Completed',
  `distance` decimal(10,2) DEFAULT NULL COMMENT 'in miles',
  `duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_job`
--

CREATE TABLE `order_job` (
  `id` int(11) NOT NULL,
  `job_id` varchar(255) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `delivery_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_on` varchar(255) DEFAULT NULL,
  `package_type` varchar(255) DEFAULT NULL,
  `transport_type` varchar(255) DEFAULT NULL,
  `assignment_code` varchar(255) DEFAULT NULL,
  `distance` decimal(10,3) DEFAULT NULL,
  `duration` decimal(2,2) DEFAULT NULL,
  `ended_on` datetime DEFAULT NULL,
  `pricing` longtext,
  `rating` decimal(4,2) DEFAULT NULL,
  `tracking_url` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

CREATE TABLE `order_product` (
  `order_product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `volume_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` double NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `net_total` decimal(10,2) NOT NULL,
  `is_refund` tinyint(4) NOT NULL COMMENT '1 = Yes, 0= No',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `allow_split_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `is_picked_up` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes',
  `delivery_charge` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Active, 0 = Inactive',
  `delivery_by` tinyint(4) NOT NULL COMMENT '1 = Janet-Collection, 2 = Stuart'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_return_transaction`
--

CREATE TABLE `order_return_transaction` (
  `order_transaction_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_history` longtext NOT NULL,
  `product_return_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_transaction`
--

CREATE TABLE `order_transaction` (
  `order_transaction_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_mode` tinyint(4) NOT NULL COMMENT '1 = Card Payment, 2 = COD, 3 = Gift Card Payment, 4 = Wallet',
  `payment_status` varchar(255) NOT NULL,
  `payment_history` longtext NOT NULL,
  `is_returned` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `is_cancelled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_unique_id` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `country_id` varchar(255) DEFAULT NULL,
  `seller_id` int(11) DEFAULT '0',
  `product_name` text CHARACTER SET utf8mb4 NOT NULL,
  `description` text CHARACTER SET utf8mb4,
  `currency` varchar(4) NOT NULL DEFAULT '£',
  `feature_img` varchar(255) DEFAULT NULL,
  `top_pick` tinyint(4) NOT NULL DEFAULT '0',
  `drink_type` tinyint(4) DEFAULT NULL COMMENT '1 = Alcoholic, 2 = Non - Alcoholic',
  `abv_percent` double DEFAULT NULL,
  `alchol_units` double NOT NULL,
  `have_return_policy` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Have Return Policy, 0 = Do not have a return policy',
  `no_of_return_days` double NOT NULL,
  `in_loyalty_club` tinyint(4) NOT NULL COMMENT '1 = Yes, 0= No',
  `in_vip_club` tinyint(4) NOT NULL COMMENT '1 = Yes, 0= No',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products_returned`
--

CREATE TABLE `products_returned` (
  `product_return_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `volume_id` int(11) NOT NULL,
  `reason` longtext,
  `is_confirmed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Accepted, 0 = Pending, 2 = Rejected',
  `amount_refunded` decimal(10,2) DEFAULT '0.00',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` varchar(255) NOT NULL,
  `payment_history` longtext NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `product_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_detail_unique_id` varchar(255) DEFAULT NULL,
  `volume_id` int(11) NOT NULL,
  `default_sell_price` decimal(10,2) NOT NULL,
  `actual_price` decimal(10,2) NOT NULL,
  `normal_discount` decimal(10,2) NOT NULL,
  `normal_sell_price` decimal(10,2) NOT NULL,
  `loyalty_club_discount` decimal(10,2) NOT NULL,
  `loyalty_club_sell_price` decimal(10,2) NOT NULL,
  `vip_club_discount` decimal(10,2) NOT NULL,
  `vip_club_sell_price` decimal(10,2) NOT NULL,
  `units` double NOT NULL COMMENT 'total stock',
  `pack_size` tinyint(4) NOT NULL,
  `min_stock_limit` double NOT NULL,
  `max_stock_limit` double NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_favourite`
--

CREATE TABLE `product_favourite` (
  `product_favourite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Active, 0 = Inactive',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `pimg_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_rating`
--

CREATE TABLE `product_rating` (
  `product_rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` decimal(10,1) NOT NULL COMMENT 'from 5',
  `review` longtext CHARACTER SET utf8,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_return_policy`
--

CREATE TABLE `product_return_policy` (
  `product_return_policy_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `promocodes`
--

CREATE TABLE `promocodes` (
  `promocode_id` int(11) NOT NULL,
  `promocode` varchar(255) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Percentage ''%'', 2 = Flat',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = General, 2 = Product, 3 = Brand, 4 = Category',
  `expiry_date` datetime NOT NULL,
  `used_limit` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `promocode_history`
--

CREATE TABLE `promocode_history` (
  `pc_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `promocode_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `repeat_orders`
--

CREATE TABLE `repeat_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule_order_list_id` int(11) NOT NULL,
  `to_be_notified_on` datetime DEFAULT NULL,
  `is_notified` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = yes, 0 = no',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_order_list`
--

CREATE TABLE `schedule_order_list` (
  `schedule_order_list_id` int(11) NOT NULL,
  `schedule_on_title` varchar(255) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Day, 2 = Week, 3 = Month, 4 = Year',
  `total_days` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seller`
--

CREATE TABLE `seller` (
  `seller_id` int(11) NOT NULL,
  `seller_name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `country_code` varchar(6) DEFAULT NULL,
  `address` longtext NOT NULL,
  `password` varchar(255) NOT NULL,
  `verify_doc` varchar(255) DEFAULT NULL,
  `verify_doc1` varchar(255) DEFAULT NULL,
  `verify_doc2` varchar(255) DEFAULT NULL,
  `verify_doc3` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin_verified` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = verified, 0 = not verified',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Active, 0 =Inactive',
  `gender` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Male, 2 =Female',
  `dob` date DEFAULT NULL,
  `notification_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `is_online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Yes, 0= No',
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `routing_no` varchar(255) DEFAULT NULL,
  `dzone_id` int(11) DEFAULT NULL,
  `has_connect_ac` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes',
  `city` varchar(255) DEFAULT NULL,
  `postalcode` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `timezone_utc` varchar(255) DEFAULT NULL,
  `delivery_by` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Janet-Collection, 2 = Stuart',
  `transport_type` varchar(255) DEFAULT NULL,
  `package_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seller_faq_mst`
--

CREATE TABLE `seller_faq_mst` (
  `faq_id` int(11) NOT NULL,
  `faq_question` varchar(1000) CHARACTER SET utf8mb4 NOT NULL,
  `faq_answer` text CHARACTER SET utf8mb4 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seller_rating`
--

CREATE TABLE `seller_rating` (
  `seller_rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `rating` decimal(10,2) NOT NULL COMMENT 'from 5',
  `review` longtext CHARACTER SET utf8,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seller_verifications`
--

CREATE TABLE `seller_verifications` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `doc_name` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `setting_id` int(11) NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `value` longtext CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_mst`
--

CREATE TABLE `shipping_mst` (
  `shipping_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contactno` varchar(255) NOT NULL,
  `address` longtext NOT NULL,
  `zipcode_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_connect_accounts`
--

CREATE TABLE `stripe_connect_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `routing_number` varchar(255) DEFAULT NULL,
  `bank_account` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `public_key` text,
  `secret_key` text,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = seller, 2 = driver',
  `sort_code` varchar(255) DEFAULT NULL,
  `card_color` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = yes, 0 = no',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `response` longtext,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active , 0 = inactive',
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_transfer_transaction`
--

CREATE TABLE `stripe_transfer_transaction` (
  `stripe_transaction_id` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_history` longtext NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '1 = seller, 2 = driver',
  `amount` decimal(10,2) NOT NULL,
  `source_transaction` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stuart_tbl`
--

CREATE TABLE `stuart_tbl` (
  `id` int(11) NOT NULL,
  `data` longtext,
  `created_on` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_code` varchar(100) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_address` text,
  `supplier_email` varchar(255) NOT NULL,
  `supplier_mobileno` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `testimonials_id` int(11) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4,
  `image` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive',
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trading_hours`
--

CREATE TABLE `trading_hours` (
  `thr_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `start_time_utc` time DEFAULT NULL,
  `end_time_utc` time DEFAULT NULL,
  `weekday` tinyint(4) DEFAULT NULL COMMENT '1 = Sunday, 2 = Monday, 3 = Tuesday, 4 = Wednesday, 5 = Thursday, 6 = Friday, 7 = Saturday',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=> active, 0=> Inactive',
  `timezone_utc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `userno` varchar(255) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `token` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobileno` varchar(255) NOT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Normal User, 2 = Driver, 3 = Business Account User',
  `birthdate` date NOT NULL,
  `social_id` varchar(1000) NOT NULL,
  `social_type` varchar(255) NOT NULL COMMENT 'facebook / google',
  `shipping_id` int(11) NOT NULL,
  `password_updated` tinyint(1) NOT NULL,
  `loyalty_point` decimal(10,2) NOT NULL,
  `wallet` decimal(10,2) NOT NULL,
  `is_vip_club_member` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `member_since` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verification_doc` varchar(255) NOT NULL,
  `is_admin_verified` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_online` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = offline , 1 = online',
  `dzone_id` int(11) DEFAULT NULL,
  `has_connect_ac` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0 = No',
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `routing_no` varchar(255) DEFAULT NULL,
  `name_of_card` varchar(255) DEFAULT NULL,
  `driver_unique_code` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` text,
  `postalcode` varchar(255) DEFAULT NULL,
  `doc_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0 = No',
  `last_seen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_images`
--

CREATE TABLE `vehicle_images` (
  `vehicle_image_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = Active, 0 =Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_mst`
--

CREATE TABLE `vehicle_mst` (
  `vehicle_id` int(11) NOT NULL,
  `maker` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle_info` longtext CHARACTER SET utf8,
  `vehicle_policy_number` varchar(255) DEFAULT NULL,
  `ins_certificate_no` varchar(255) DEFAULT NULL,
  `vehicle_ins_policy` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `verification_code`
--

CREATE TABLE `verification_code` (
  `vcid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vcode` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `volume_mst`
--

CREATE TABLE `volume_mst` (
  `volume_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `volumne_value` decimal(10,2) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `volume_type`
--

CREATE TABLE `volume_type` (
  `volume_type_id` int(11) NOT NULL,
  `volume_type` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 = active 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_history`
--

CREATE TABLE `wallet_history` (
  `transaction_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL COMMENT '1 = Debit, 2 = Credit',
  `debit_credit_amount` decimal(10,2) NOT NULL,
  `balance_amount` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `note` longtext NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_status` varchar(255) NOT NULL COMMENT '1 = Pending, 2 = Failed, 3 = Successful ',
  `payment_history` longtext NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `is_withdrawn` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 = Yes, 0= No ( for driver)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_withdraw_history`
--

CREATE TABLE `wallet_withdraw_history` (
  `transaction_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `withdrawn_amount` decimal(10,2) NOT NULL,
  `balance_amount` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` varchar(255) NOT NULL COMMENT '1 = Pending, 2 = Failed, 3 = Successful ',
  `payment_history` longtext NOT NULL,
  `transaction_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

CREATE TABLE `website` (
  `id` int(11) NOT NULL,
  `string_key` varchar(255) DEFAULT NULL,
  `string_value` longtext CHARACTER SET utf8mb4,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `website_notification`
--

CREATE TABLE `website_notification` (
  `notification_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `notification_type` varchar(255) NOT NULL COMMENT '1 = order placed, 2 = driver request, 3 = new driver, 4 = new seller, 5 = driver vehicle request',
  `message` text CHARACTER SET utf8mb4 NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification_status` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL,
  `is_notified` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zipcode`
--

CREATE TABLE `zipcode` (
  `zipcode_id` int(11) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `area` longtext NOT NULL,
  `dzone_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD KEY `admin_id_2` (`admin_id`);

--
-- Indexes for table `alcohol_awareness`
--
ALTER TABLE `alcohol_awareness`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `block_users`
--
ALTER TABLE `block_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bonus_list`
--
ALTER TABLE `bonus_list`
  ADD PRIMARY KEY (`bonus_id`);

--
-- Indexes for table `brand_category_allocation`
--
ALTER TABLE `brand_category_allocation`
  ADD PRIMARY KEY (`brand_category_allocation_id`);

--
-- Indexes for table `brand_mst`
--
ALTER TABLE `brand_mst`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `cancel_order_reasons`
--
ALTER TABLE `cancel_order_reasons`
  ADD PRIMARY KEY (`creason_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_product`
--
ALTER TABLE `cart_product`
  ADD PRIMARY KEY (`cart_product_id`);

--
-- Indexes for table `category_mst`
--
ALTER TABLE `category_mst`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `chat_ibfk_1` (`user_id`),
  ADD KEY `chat_ibfk_2` (`to_user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_us_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `delivery_charges`
--
ALTER TABLE `delivery_charges`
  ADD PRIMARY KEY (`charge_id`);

--
-- Indexes for table `delivery_receipt`
--
ALTER TABLE `delivery_receipt`
  ADD PRIMARY KEY (`delivery_receipt_id`);

--
-- Indexes for table `delivery_zone`
--
ALTER TABLE `delivery_zone`
  ADD PRIMARY KEY (`dzone_id`);

--
-- Indexes for table `device_token`
--
ALTER TABLE `device_token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `driver_bonus`
--
ALTER TABLE `driver_bonus`
  ADD PRIMARY KEY (`driver_bonus_id`);

--
-- Indexes for table `driver_by_invite`
--
ALTER TABLE `driver_by_invite`
  ADD PRIMARY KEY (`ref_id`);

--
-- Indexes for table `driver_docs`
--
ALTER TABLE `driver_docs`
  ADD PRIMARY KEY (`driver_doc_id`);

--
-- Indexes for table `driver_earnings`
--
ALTER TABLE `driver_earnings`
  ADD PRIMARY KEY (`earning_id`);

--
-- Indexes for table `driver_requests`
--
ALTER TABLE `driver_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `driver_schedule`
--
ALTER TABLE `driver_schedule`
  ADD PRIMARY KEY (`sch_id`);

--
-- Indexes for table `driver_target`
--
ALTER TABLE `driver_target`
  ADD PRIMARY KEY (`target_id`);

--
-- Indexes for table `driver_vehicle_image_request`
--
ALTER TABLE `driver_vehicle_image_request`
  ADD PRIMARY KEY (`vehicle_image_id`);

--
-- Indexes for table `driver_vehicle_requests`
--
ALTER TABLE `driver_vehicle_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `faq_mst`
--
ALTER TABLE `faq_mst`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `gift_card`
--
ALTER TABLE `gift_card`
  ADD PRIMARY KEY (`card_id`);

--
-- Indexes for table `gift_card_history`
--
ALTER TABLE `gift_card_history`
  ADD PRIMARY KEY (`gift_card_history_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_member_id`);

--
-- Indexes for table `help_support`
--
ALTER TABLE `help_support`
  ADD PRIMARY KEY (`help_id`);

--
-- Indexes for table `logtbl`
--
ALTER TABLE `logtbl`
  ADD PRIMARY KEY (`logid`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `module_access`
--
ALTER TABLE `module_access`
  ADD PRIMARY KEY (`module_access_id`);

--
-- Indexes for table `notcompleted_order_reasons`
--
ALTER TABLE `notcompleted_order_reasons`
  ADD PRIMARY KEY (`ncreason_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `to_user_id` (`to_user_id`);

--
-- Indexes for table `notification_type_mst`
--
ALTER TABLE `notification_type_mst`
  ADD PRIMARY KEY (`notification_type_id`);

--
-- Indexes for table `offline_data`
--
ALTER TABLE `offline_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_canceled`
--
ALTER TABLE `order_canceled`
  ADD PRIMARY KEY (`order_canceled_id`);

--
-- Indexes for table `order_delivery`
--
ALTER TABLE `order_delivery`
  ADD PRIMARY KEY (`order_delivery_id`);

--
-- Indexes for table `order_driver`
--
ALTER TABLE `order_driver`
  ADD PRIMARY KEY (`order_driver_id`);

--
-- Indexes for table `order_job`
--
ALTER TABLE `order_job`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_product`
--
ALTER TABLE `order_product`
  ADD PRIMARY KEY (`order_product_id`);

--
-- Indexes for table `order_return_transaction`
--
ALTER TABLE `order_return_transaction`
  ADD PRIMARY KEY (`order_transaction_id`);

--
-- Indexes for table `order_transaction`
--
ALTER TABLE `order_transaction`
  ADD PRIMARY KEY (`order_transaction_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `products_returned`
--
ALTER TABLE `products_returned`
  ADD PRIMARY KEY (`product_return_id`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`product_detail_id`);

--
-- Indexes for table `product_favourite`
--
ALTER TABLE `product_favourite`
  ADD PRIMARY KEY (`product_favourite_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`pimg_id`);

--
-- Indexes for table `product_rating`
--
ALTER TABLE `product_rating`
  ADD PRIMARY KEY (`product_rating_id`);

--
-- Indexes for table `product_return_policy`
--
ALTER TABLE `product_return_policy`
  ADD PRIMARY KEY (`product_return_policy_id`);

--
-- Indexes for table `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`promocode_id`);

--
-- Indexes for table `promocode_history`
--
ALTER TABLE `promocode_history`
  ADD PRIMARY KEY (`pc_history_id`);

--
-- Indexes for table `repeat_orders`
--
ALTER TABLE `repeat_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule_order_list`
--
ALTER TABLE `schedule_order_list`
  ADD PRIMARY KEY (`schedule_order_list_id`);

--
-- Indexes for table `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`seller_id`);

--
-- Indexes for table `seller_faq_mst`
--
ALTER TABLE `seller_faq_mst`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `seller_rating`
--
ALTER TABLE `seller_rating`
  ADD PRIMARY KEY (`seller_rating_id`);

--
-- Indexes for table `seller_verifications`
--
ALTER TABLE `seller_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `shipping_mst`
--
ALTER TABLE `shipping_mst`
  ADD PRIMARY KEY (`shipping_id`);

--
-- Indexes for table `stripe_connect_accounts`
--
ALTER TABLE `stripe_connect_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stripe_transfer_transaction`
--
ALTER TABLE `stripe_transfer_transaction`
  ADD PRIMARY KEY (`stripe_transaction_id`);

--
-- Indexes for table `stuart_tbl`
--
ALTER TABLE `stuart_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonials_id`);

--
-- Indexes for table `trading_hours`
--
ALTER TABLE `trading_hours`
  ADD PRIMARY KEY (`thr_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  ADD PRIMARY KEY (`vehicle_image_id`);

--
-- Indexes for table `vehicle_mst`
--
ALTER TABLE `vehicle_mst`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- Indexes for table `verification_code`
--
ALTER TABLE `verification_code`
  ADD PRIMARY KEY (`vcid`);

--
-- Indexes for table `volume_mst`
--
ALTER TABLE `volume_mst`
  ADD PRIMARY KEY (`volume_id`);

--
-- Indexes for table `volume_type`
--
ALTER TABLE `volume_type`
  ADD PRIMARY KEY (`volume_type_id`);

--
-- Indexes for table `wallet_history`
--
ALTER TABLE `wallet_history`
  ADD PRIMARY KEY (`transaction_history_id`);

--
-- Indexes for table `wallet_withdraw_history`
--
ALTER TABLE `wallet_withdraw_history`
  ADD PRIMARY KEY (`transaction_history_id`);

--
-- Indexes for table `website`
--
ALTER TABLE `website`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `website_notification`
--
ALTER TABLE `website_notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `to_user_id` (`order_id`);

--
-- Indexes for table `zipcode`
--
ALTER TABLE `zipcode`
  ADD PRIMARY KEY (`zipcode_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `alcohol_awareness`
--
ALTER TABLE `alcohol_awareness`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `block_users`
--
ALTER TABLE `block_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bonus_list`
--
ALTER TABLE `bonus_list`
  MODIFY `bonus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `brand_category_allocation`
--
ALTER TABLE `brand_category_allocation`
  MODIFY `brand_category_allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14997;
--
-- AUTO_INCREMENT for table `brand_mst`
--
ALTER TABLE `brand_mst`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;
--
-- AUTO_INCREMENT for table `cancel_order_reasons`
--
ALTER TABLE `cancel_order_reasons`
  MODIFY `creason_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;
--
-- AUTO_INCREMENT for table `cart_product`
--
ALTER TABLE `cart_product`
  MODIFY `cart_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;
--
-- AUTO_INCREMENT for table `category_mst`
--
ALTER TABLE `category_mst`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=289;
--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_us_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
--
-- AUTO_INCREMENT for table `delivery_charges`
--
ALTER TABLE `delivery_charges`
  MODIFY `charge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;
--
-- AUTO_INCREMENT for table `delivery_receipt`
--
ALTER TABLE `delivery_receipt`
  MODIFY `delivery_receipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=916;
--
-- AUTO_INCREMENT for table `delivery_zone`
--
ALTER TABLE `delivery_zone`
  MODIFY `dzone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `device_token`
--
ALTER TABLE `device_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1102;
--
-- AUTO_INCREMENT for table `driver_bonus`
--
ALTER TABLE `driver_bonus`
  MODIFY `driver_bonus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `driver_by_invite`
--
ALTER TABLE `driver_by_invite`
  MODIFY `ref_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `driver_docs`
--
ALTER TABLE `driver_docs`
  MODIFY `driver_doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;
--
-- AUTO_INCREMENT for table `driver_earnings`
--
ALTER TABLE `driver_earnings`
  MODIFY `earning_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `driver_requests`
--
ALTER TABLE `driver_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `driver_schedule`
--
ALTER TABLE `driver_schedule`
  MODIFY `sch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=629;
--
-- AUTO_INCREMENT for table `driver_target`
--
ALTER TABLE `driver_target`
  MODIFY `target_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
--
-- AUTO_INCREMENT for table `driver_vehicle_image_request`
--
ALTER TABLE `driver_vehicle_image_request`
  MODIFY `vehicle_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `driver_vehicle_requests`
--
ALTER TABLE `driver_vehicle_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `faq_mst`
--
ALTER TABLE `faq_mst`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `gift_card`
--
ALTER TABLE `gift_card`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `gift_card_history`
--
ALTER TABLE `gift_card_history`
  MODIFY `gift_card_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `group_member_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `help_support`
--
ALTER TABLE `help_support`
  MODIFY `help_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `logtbl`
--
ALTER TABLE `logtbl`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `module_access`
--
ALTER TABLE `module_access`
  MODIFY `module_access_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notcompleted_order_reasons`
--
ALTER TABLE `notcompleted_order_reasons`
  MODIFY `ncreason_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3533;
--
-- AUTO_INCREMENT for table `notification_type_mst`
--
ALTER TABLE `notification_type_mst`
  MODIFY `notification_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `offline_data`
--
ALTER TABLE `offline_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;
--
-- AUTO_INCREMENT for table `order_canceled`
--
ALTER TABLE `order_canceled`
  MODIFY `order_canceled_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `order_delivery`
--
ALTER TABLE `order_delivery`
  MODIFY `order_delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `order_driver`
--
ALTER TABLE `order_driver`
  MODIFY `order_driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;
--
-- AUTO_INCREMENT for table `order_job`
--
ALTER TABLE `order_job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `order_product`
--
ALTER TABLE `order_product`
  MODIFY `order_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;
--
-- AUTO_INCREMENT for table `order_return_transaction`
--
ALTER TABLE `order_return_transaction`
  MODIFY `order_transaction_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `order_transaction`
--
ALTER TABLE `order_transaction`
  MODIFY `order_transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4056;
--
-- AUTO_INCREMENT for table `products_returned`
--
ALTER TABLE `products_returned`
  MODIFY `product_return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `product_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5531;
--
-- AUTO_INCREMENT for table `product_favourite`
--
ALTER TABLE `product_favourite`
  MODIFY `product_favourite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360;
--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `pimg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;
--
-- AUTO_INCREMENT for table `product_rating`
--
ALTER TABLE `product_rating`
  MODIFY `product_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `product_return_policy`
--
ALTER TABLE `product_return_policy`
  MODIFY `product_return_policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `promocode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `promocode_history`
--
ALTER TABLE `promocode_history`
  MODIFY `pc_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `repeat_orders`
--
ALTER TABLE `repeat_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `schedule_order_list`
--
ALTER TABLE `schedule_order_list`
  MODIFY `schedule_order_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `seller`
--
ALTER TABLE `seller`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `seller_faq_mst`
--
ALTER TABLE `seller_faq_mst`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `seller_rating`
--
ALTER TABLE `seller_rating`
  MODIFY `seller_rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `seller_verifications`
--
ALTER TABLE `seller_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT for table `shipping_mst`
--
ALTER TABLE `shipping_mst`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
--
-- AUTO_INCREMENT for table `stripe_connect_accounts`
--
ALTER TABLE `stripe_connect_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `stripe_transfer_transaction`
--
ALTER TABLE `stripe_transfer_transaction`
  MODIFY `stripe_transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `stuart_tbl`
--
ALTER TABLE `stuart_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;
--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonials_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `trading_hours`
--
ALTER TABLE `trading_hours`
  MODIFY `thr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;
--
-- AUTO_INCREMENT for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  MODIFY `vehicle_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `vehicle_mst`
--
ALTER TABLE `vehicle_mst`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `verification_code`
--
ALTER TABLE `verification_code`
  MODIFY `vcid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `volume_mst`
--
ALTER TABLE `volume_mst`
  MODIFY `volume_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1490;
--
-- AUTO_INCREMENT for table `volume_type`
--
ALTER TABLE `volume_type`
  MODIFY `volume_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `wallet_history`
--
ALTER TABLE `wallet_history`
  MODIFY `transaction_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;
--
-- AUTO_INCREMENT for table `wallet_withdraw_history`
--
ALTER TABLE `wallet_withdraw_history`
  MODIFY `transaction_history_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `website`
--
ALTER TABLE `website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `website_notification`
--
ALTER TABLE `website_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;
--
-- AUTO_INCREMENT for table `zipcode`
--
ALTER TABLE `zipcode`
  MODIFY `zipcode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12352;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
