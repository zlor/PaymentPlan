-- --------------------------------------------------------
-- 主机:                           192.168.10.10
-- 服务器版本:                        5.7.19-0ubuntu0.16.04.1 - (Ubuntu)
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 正在导出表  payment_plan.admin_menu 的数据：~40 rows (大约)
DELETE FROM `admin_menu`;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `created_at`, `updated_at`) VALUES
	(1, 2, 23, '系统参数（envs）', 'fa-bar-chart', 'envs', NULL, '2018-02-04 17:51:18'),
	(2, 0, 22, '后台管理', 'fa-tasks', NULL, NULL, '2018-02-04 17:51:18'),
	(3, 2, 24, '用户档案（Users）', 'fa-users', 'auth/users', NULL, '2018-02-04 17:51:18'),
	(4, 2, 25, '角色档案（Roles）', 'fa-user', 'auth/roles', NULL, '2018-02-04 17:51:18'),
	(5, 2, 26, '权限档案（Permission）', 'fa-ban', 'auth/permissions', NULL, '2018-02-04 17:51:18'),
	(6, 2, 27, '菜单（Menu）', 'fa-bars', 'auth/menu', NULL, '2018-02-04 17:51:18'),
	(7, 2, 28, 'Operation log', 'fa-history', 'auth/logs', NULL, '2018-02-04 17:51:18'),
	(8, 0, 2, '付款管理', 'fa-bars', NULL, '2018-01-16 19:07:53', '2018-02-01 09:28:52'),
	(9, 0, 1, '账期总览', 'fa-bars', 'bill/gather', '2018-01-16 19:10:30', '2018-02-01 09:28:52'),
	(10, 8, 3, '计划录入', 'fa-tasks', 'plan/batch/schedule', '2018-01-16 19:11:10', '2018-02-04 17:48:28'),
	(11, 8, 4, '计划调整(第一次)', 'fa-edit', 'audit/batch/schedule', '2018-01-16 19:11:49', '2018-02-04 17:51:53'),
	(12, 8, 7, '按计划付款', 'fa-check-circle-o', 'pay/schedule', '2018-01-16 19:12:10', '2018-02-04 17:51:18'),
	(13, 8, 8, '计划进度', 'fa-percent', 'progress/schedule', NULL, '2018-02-04 17:51:18'),
	(14, 0, 12, '基础管理', 'fa-file', NULL, '2018-01-16 19:13:31', '2018-02-04 17:51:18'),
	(15, 34, 21, '供应商档案', 'fa-truck', 'base/suppliers', '2018-01-16 19:14:13', '2018-02-04 17:51:18'),
	(16, 14, 13, '账期档案', 'fa-calendar', 'base/bill_periods', '2018-01-16 19:15:00', '2018-02-04 17:51:18'),
	(17, 14, 14, '付款计划档案', 'fa-dedent', 'base/bill/payment_schedules', '2018-01-16 19:16:05', '2018-02-04 17:51:18'),
	(18, 14, 15, '付款明细档案', 'fa-bars', 'base/bill/payment_details', '2018-01-16 19:16:34', '2018-02-04 17:51:18'),
	(19, 2, 29, 'Scheduling', 'fa-clock-o', 'scheduling', '2018-01-17 10:56:57', '2018-02-04 17:51:18'),
	(20, 2, 30, 'Media manager', 'fa-file', 'media', '2018-01-17 10:57:06', '2018-02-04 17:51:18'),
	(21, 2, 31, 'Log viwer', 'fa-database', 'logs', '2018-01-17 10:58:38', '2018-02-04 17:51:18'),
	(22, 2, 32, 'Config', 'fa-toggle-on', 'config', '2018-01-17 11:02:11', '2018-02-04 17:51:18'),
	(23, 2, 33, 'Messages', 'fa-paper-plane', 'messages', '2018-01-17 12:24:25', '2018-02-04 17:51:18'),
	(24, 2, 34, 'Backup', 'fa-copy', 'backup', '2018-01-17 13:11:57', '2018-02-04 17:51:18'),
	(25, 33, 18, '类型档案', 'fa-map-o', 'base/bill/payment_types', '2018-01-18 14:28:15', '2018-02-04 17:51:18'),
	(26, 2, 35, 'Helpers', 'fa-gears', '', '2018-01-17 14:31:32', '2018-02-04 17:51:18'),
	(27, 26, 36, 'Scaffold', 'fa-keyboard-o', 'helpers/scaffold', '2018-01-17 14:31:32', '2018-02-04 17:51:18'),
	(28, 26, 37, 'Database terminal', 'fa-database', 'helpers/terminal/database', '2018-01-17 14:31:32', '2018-02-04 17:51:18'),
	(29, 26, 38, 'Laravel artisan', 'fa-terminal', 'helpers/terminal/artisan', '2018-01-17 14:31:32', '2018-02-04 17:51:18'),
	(30, 26, 39, 'Routes', 'fa-list-alt', 'helpers/routes', '2018-01-17 14:31:32', '2018-02-04 17:51:18'),
	(31, 33, 17, '付款物料档案', 'fa-tree', 'base/bill/payment_materiels', '2018-01-18 11:57:56', '2018-02-04 17:51:18'),
	(32, 34, 20, '供应商-分组', 'fa-bars', '/base/supplier_owners', '2018-01-18 12:22:14', '2018-02-04 17:51:18'),
	(33, 14, 16, '物料管理', 'fa-archive', NULL, '2018-01-18 14:26:05', '2018-02-04 17:51:18'),
	(34, 14, 19, '供应商管理', 'fa-truck', NULL, '2018-01-18 14:26:50', '2018-02-04 17:51:18'),
	(35, 0, 10, '收款管理', 'fa-bars', '/collect/money', '2018-02-01 09:15:15', '2018-02-04 17:51:18'),
	(36, 35, 11, '按期收款', 'fa-circle-o', '/collect/period', '2018-02-01 09:19:33', '2018-02-04 17:51:18'),
	(37, 8, 9, '按期付款', 'fa-circle-thin', '/pay/period', '2018-02-01 09:26:59', '2018-02-04 17:51:18'),
	(38, 8, 5, '计划调整(第二次)', 'fa-edit', 'final/batch/schedule', '2018-02-04 17:49:57', '2018-02-04 17:51:18'),
	(39, 8, 6, '应付款敲定', 'fa-check', 'lock/batch/schedule', '2018-02-04 17:51:02', '2018-02-04 17:51:18'),
	(40, 8, 0, '应付款发票', 'money-bill-alt', 'pay/invoice', '2018-04-17 13:32:14', '2018-04-17 13:32:14');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
