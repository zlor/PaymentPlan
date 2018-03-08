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

-- 正在导出表  payment_plan.admin_permissions 的数据：~30 rows (大约)
TRANSLATE FROM `admin_permissions`;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
	(1, 'All permission', '*', '', '*', NULL, NULL),
	(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
	(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
	(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
	(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
	(6, 'Scheduling', 'ext.scheduling', NULL, '/scheduling*', '2018-01-17 10:56:57', '2018-01-17 10:56:57'),
	(7, 'Media manager', 'ext.media-manager', NULL, '/media*', '2018-01-17 10:57:06', '2018-01-17 10:57:06'),
	(8, 'Logs', 'ext.log-viewer', NULL, '/logs*', '2018-01-17 10:58:38', '2018-01-17 10:58:38'),
	(9, 'Admin Config', 'ext.config', NULL, '/config*', '2018-01-17 11:02:11', '2018-01-17 11:02:11'),
	(10, 'Admin messages', 'ext.messages', NULL, '/messages*', '2018-01-17 12:24:25', '2018-01-17 12:24:25'),
	(11, 'Backup', 'ext.backup', NULL, '/backup*', '2018-01-17 13:11:57', '2018-01-17 13:11:57'),
	(12, 'ActionLog', 'auth.log', NULL, '/auth/logs*', NULL, NULL),
	(13, 'Admin helpers', 'ext.helpers', NULL, '/helpers/*', '2018-01-17 14:31:32', '2018-01-17 14:31:32'),
	(14, '账期汇总页面', 'gather', 'GET', '/bill/gather\r\n/bill/*/gather\r\n/bill/period', '2018-01-27 11:50:10', '2018-01-27 12:00:32'),
	(15, '账期汇总》[资金设置/状态激活]', 'gather.action', 'GET,POST,PUT', '/bill/set_pool*\r\n/bill/period*', '2018-01-27 11:54:33', '2018-01-27 12:00:46'),
	(16, '付款管理》计划录入', 'plan.schedule', '', '/plan/schedule*\r\n/plan/batch/schedule*', '2018-01-27 11:57:04', '2018-01-27 12:00:55'),
	(17, '付款管理》计划调整(第一次)', 'audit.schedule', '', '/audit/schedule*\r\n/audit/batch/schedule*', '2018-01-27 11:58:14', '2018-01-27 12:01:09'),
	(18, '付款管理》计划调整(第二次)', 'final.schedule', '', '/final/schedule*\r\n/final/batch/schedule*', '2018-01-27 11:58:56', '2018-01-27 12:01:18'),
	(19, '付款管理》应付款敲定', 'lock.schedule', '', '/lock/schedule*\r\n/lock/batch/schedule*', '2018-01-27 11:59:53', '2018-01-27 12:01:27'),
	(20, '付款管理》计划进度', 'progress.schedule', '', '/progress/schedule*', '2018-01-27 12:02:28', '2018-01-27 12:02:28'),
	(21, '付款管理》付款（按计划、按期）', 'pay.schedule', '', '/pay/schedule/detail*\r\n/pay/period*', '2018-01-27 12:03:21', '2018-01-27 12:03:21'),
	(22, '基础档案', 'base', '', '/base*', '2018-01-27 12:03:56', '2018-01-27 12:03:56'),
	(23, '基础档案》供应商', 'base.supplier', '', '/base/suppliers*', '2018-01-27 12:04:25', '2018-01-27 12:04:25'),
	(24, '基础档案》供应商_分组', 'base.supplier.owner', '', '/base/supplier_owners*', '2018-01-27 12:05:14', '2018-01-27 12:05:14'),
	(25, '基础档案》账期', 'base.period', '', '/base/bill_periods*', '2018-01-27 12:05:51', '2018-01-27 12:05:51'),
	(26, '基础档案》计划类型', 'base.payment_type', '', '/base/bill/payment_types*', '2018-01-27 12:06:46', '2018-01-27 12:06:46'),
	(27, '基础档案》物料', 'base.materiel', '', '/base/bill/payment_materiels*', '2018-01-27 12:07:38', '2018-01-27 12:07:38'),
	(28, '基础档案》计划', 'base.schedule', '', '/base/bill/payment_schedules*', '2018-01-27 12:08:21', '2018-01-27 12:08:21'),
	(29, '基础档案》付款明细', 'base.payment_detail', '', '/base/bill/payment_details*', '2018-01-27 12:09:12', '2018-01-27 12:09:12'),
	(30, '通用数据获取(付款计划)', 'common.select', '', '/select*', '2018-01-27 12:10:18', '2018-01-27 12:10:18');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
