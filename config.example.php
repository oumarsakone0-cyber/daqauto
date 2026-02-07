<?php
/**
 * Database Configuration File Example / 数据库配置文件示例
 * 
 * Usage / 使用方法：
 * 1. Copy this file as config.php / 复制此文件为 config.php
 * 2. Fill in actual database configuration information / 填入实际的数据库配置信息
 * 3. Ensure config.php is added to .gitignore / 确保 config.php 已添加到 .gitignore 中
 */

return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'your_database_name',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8mb4'
    ],
    'api' => [
        'vin_api_url' => 'https://api.tanshuapi.com/api/vin',
        'translation_api_url' => 'https://api.mymemory.translated.net/get'
    ],
    'error_log' => [
        'path' => __DIR__ . '/php_errors.log',
        'enabled' => true
    ]
];
