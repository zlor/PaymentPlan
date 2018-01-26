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
INSERT INTO payment_plan.admin_permissions (name, slug, http_method, http_path, created_at, updated_at) VALUES ('Admin helpers', 'ext.helpers', null, '/helpers/*', '2018-01-17 14:31:32', '2018-01-17 14:31:32');