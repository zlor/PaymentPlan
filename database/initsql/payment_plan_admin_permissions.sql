INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('All permission', '*', '', '*', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Dashboard', 'dashboard', 'GET', '/', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Login', 'auth.login', '', '/auth/login
/auth/logout', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('User setting', 'auth.setting', 'GET,PUT', '/auth/setting', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Auth management', 'auth.management', '', '/auth/roles
/auth/permissions
/auth/menu
/auth/logs', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Scheduling', 'ext.scheduling', null, '/scheduling*', '2018-01-17 10:56:57', '2018-01-17 10:56:57');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Media manager', 'ext.media-manager', null, '/media*', '2018-01-17 10:57:06', '2018-01-17 10:57:06');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Logs', 'ext.log-viewer', null, '/logs*', '2018-01-17 10:58:38', '2018-01-17 10:58:38');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Admin Config', 'ext.config', null, '/config*', '2018-01-17 11:02:11', '2018-01-17 11:02:11');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Admin messages', 'ext.messages', null, '/messages*', '2018-01-17 12:24:25', '2018-01-17 12:24:25');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Backup', 'ext.backup', null, '/backup*', '2018-01-17 13:11:57', '2018-01-17 13:11:57');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('ActionLog', 'auth.log', null, '/auth/logs*', null, null);
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Admin helpers', 'ext.helpers', null, '/helpers/*', '2018-01-17 14:31:32', '2018-01-17 14:31:32');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('账期汇总页面', 'gather', 'GET', '/bill/gather
/bill/*/gather
/bill/period', '2018-01-27 11:50:10', '2018-01-27 12:00:32');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('账期汇总》[资金设置/状态激活]', 'gather.action', 'GET,POST,PUT', '/bill/set_pool*
/bill/period*', '2018-01-27 11:54:33', '2018-01-27 12:00:46');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('计划录入', 'plan.schedule', '', '/plan/schedule*', '2018-01-27 11:57:04', '2018-01-27 12:00:55');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('计划核定》初稿核定', 'audit.schedule', '', '/audit/schedule*', '2018-01-27 11:58:14', '2018-01-27 12:01:09');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('计划核定》终稿核定', 'final.schedule', '', '/final/schedule*', '2018-01-27 11:58:56', '2018-01-27 12:01:18');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('计划核定》应付款敲定', 'lock.schedule', '', '/lock/schedule*', '2018-01-27 11:59:53', '2018-01-27 12:01:27');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('计划进度', 'progress.schedule', '', '/progress/schedule*', '2018-01-27 12:02:28', '2018-01-27 12:02:28');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('付款录入', 'pay.schedule', '', '/pay/schedule/detail*', '2018-01-27 12:03:21', '2018-01-27 12:03:21');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案', 'base', '', '/base*', '2018-01-27 12:03:56', '2018-01-27 12:03:56');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》供应商', 'base.supplier', '', '/base/suppliers*', '2018-01-27 12:04:25', '2018-01-27 12:04:25');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》供应商_分组', 'base.supplier.owner', '', '/base/supplier_owners*', '2018-01-27 12:05:14', '2018-01-27 12:05:14');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》账期', 'base.period', '', '/base/bill_periods*', '2018-01-27 12:05:51', '2018-01-27 12:05:51');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》计划类型', 'base.payment_type', '', '/base/bill/payment_types*', '2018-01-27 12:06:46', '2018-01-27 12:06:46');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》物料', 'base.materiel', '', '/base/bill/payment_materiels*', '2018-01-27 12:07:38', '2018-01-27 12:07:38');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》计划', 'base.schedule', '', '/base/bill/payment_schedules*', '2018-01-27 12:08:21', '2018-01-27 12:08:21');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('基础档案》付款明细', 'base.payment_detail', '', '/base/bill/payment_details*', '2018-01-27 12:09:12', '2018-01-27 12:09:12');
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('通用数据获取(付款计划)', 'common.select', '', '/select*', '2018-01-27 12:10:18', '2018-01-27 12:10:18');