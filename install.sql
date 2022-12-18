CREATE TABLE `{$prefix}qubit_bt_manager_servers`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `pfid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '平台ID',
    `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `mame` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
    `bt_panel` varchar(256) NOT NULL COMMENT '宝塔面板地址',
    `key` varchar(50) NOT NULL COMMENT 'api密钥',
    `status` int(1) NOT NULL DEFAULT 0 COMMENT '服务器状态',
    `mysql_root` varchar(100) NOT NULL DEFAULT '' COMMENT '数据库root密码',
    `backup_path` varchar(100) NOT NULL DEFAULT '' COMMENT '备份路径',
    `webserver` varchar(100) NOT NULL DEFAULT '' COMMENT 'web服务类型',
    `sites_path` varchar(100) NOT NULL DEFAULT '' COMMENT '网站目录',
    `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
    `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
    `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `pfid` (`pfid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '服务器表';

CREATE TABLE `{$prefix}qubit_bt_manager_configs`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `pfid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '平台ID',
    `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `name` varchar(256) NOT NULL DEFAULT '' COMMENT '配置自定义名称',
    `config` text DEFAULT '' COMMENT '保存的配置信息',
    `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
    `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '建站配置表';

CREATE TABLE `{$prefix}qubit_bt_manager_sites`  (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
    `pfid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '平台ID',
    `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `server_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务器ID',
    `config_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置ID',
    
    `domain` varchar(50) NOT NULL DEFAULT '' COMMENT '域名',
    `port` int(5) NOT NULL DEFAULT '80' COMMENT '端口',
    `domain_list`  text DEFAULT '' COMMENT '域名列表',
    `path` varchar(100) NOT NULL DEFAULT '' COMMENT '根目录',
    `source_path` varchar(100) NOT NULL DEFAULT '' COMMENT '源目录',
    
    `siteStatus` int(1) unsigned NOT NULL DEFAULT 0 COMMENT '站点创建状态:0=待创建,1=已创建',
    `siteId` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '站点ID',
    `ftpStatus` int(1) unsigned NOT NULL DEFAULT 0 COMMENT 'ftp创建状态:0=未创建,1=已创建',
    `databaseStatus` int(1) unsigned NOT NULL DEFAULT 0 COMMENT '数据库创建状态:0=未创建,1=已创建',
    `databaseUser` varchar(50) NOT NULL DEFAULT '' COMMENT '数据库账号',
    `databasePass` varchar(50) NOT NULL DEFAULT '' COMMENT '数据库密码',
    
    `sslStatus` int(1) unsigned NOT NULL DEFAULT 0 COMMENT 'sll创建状态:0=未创建,1=已创建',
    `sslMsg` varchar(100) NOT NULL DEFAULT '' COMMENT 'ssl结果',
    
    `create_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
    `update_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
    `delete_time` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `pfid` (`pfid`) USING BTREE,
    INDEX `uid` (`uid`) USING BTREE,
    INDEX `server_id` (`server_id`) USING BTREE,
    INDEX `config_id` (`config_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '建站表';