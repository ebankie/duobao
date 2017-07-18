<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array (
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array ('Addons' => ONETHINK_ADDON_PATH) , //扩展模块列表
    'DEFAULT_MODULE' => 'Weixin' ,//Home
    'MODULE_DENY_LIST' => array ('Common' , 'User') ,
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '_/.(2;l@tx~XvF)`4aZC:f+Q1h<b*&pN%dms#g=q' , //默认数据加密KEY

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => TRUE ,

    /* 用户相关设置 */
    'USER_MAX_CACHE' => 1000 , //最大缓存用户数
    'USER_ADMINISTRATOR' => 1 , //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => TRUE , //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 3 , //URL模式
    'VAR_URL_PARAMS' => '' , // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/' , //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '' , //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE' => 'mysql' , // 数据库类型
    'DB_HOST' => 'localhost' , // 服务器地址
    'DB_NAME' => 'duobao' , // 数据库名
    'DB_USER' => 'root' , // 用户名
    'DB_PWD' => 'ewangtx2017' ,  // 密码
    'DB_PORT' => '3306' , // 端口
    'DB_PREFIX' => 'ewshop_' , // 数据库表前缀
    'DB_CHARSET' => 'utf8' , // 字符集

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array (2 => '主题' , 1 => '目录' , 3 => '段落') ,
);
