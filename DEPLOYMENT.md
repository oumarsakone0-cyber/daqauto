# 部署指南

本文档说明如何将 DaqAuto 项目部署到生产环境。

## 📋 部署前检查清单

- [ ] 已完成 `config.php` 配置
- [ ] 已执行数据库迁移脚本
- [ ] 已测试所有功能
- [ ] 已设置正确的文件权限
- [ ] 已配置 Web 服务器

## 🗄️ 数据库设置

### 1. 创建数据库

```sql
CREATE DATABASE IF NOT EXISTS your_database_name 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. 执行迁移脚本

```bash
# 创建购物车表
mysql -u your_username -p your_database_name < database/create_cart_table.sql

# 添加 model_list 字段（如果表已存在）
mysql -u your_username -p your_database_name < add_model_list_column.sql
```

### 3. 创建 VIN 解码数据表

如果表不存在，`save_vin_data.php` 会在首次请求时自动创建。或者您可以手动创建：

```sql
CREATE TABLE IF NOT EXISTS `vin_decoded_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vin` varchar(17) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `series_name` varchar(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `drivetrain` varchar(50) DEFAULT NULL,
  `model_list` TEXT DEFAULT NULL COMMENT '车型列表（JSON格式）',
  `decoded_at` datetime DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vin` (`vin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 🔧 服务器配置

### Nginx 配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/daqauto/dist;
    index index.html;

    # 前端路由
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API 代理
    location /api/ {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # PHP 文件处理
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 静态资源缓存
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Apache 配置示例

在 `.htaccess` 文件中：

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.html$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.html [L]
</IfModule>
```

## 📦 构建和部署

### 1. 构建前端

```bash
npm run build
```

构建产物在 `dist` 目录中。

### 2. 上传文件

```bash
# 上传前端文件
scp -r dist/* user@server:/var/www/daqauto/dist/

# 上传 PHP API 文件
scp save_vin_data.php user@server:/var/www/daqauto/api/

# 上传配置文件（确保不在公开目录）
scp config.php user@server:/var/www/daqauto/config.php
```

### 3. 设置文件权限

```bash
# 设置目录权限
chmod 755 /var/www/daqauto
chmod 644 /var/www/daqauto/dist/*
chmod 644 /var/www/daqauto/api/save_vin_data.php

# 设置日志文件权限（如果存在）
chmod 666 /var/www/daqauto/php_errors.log
```

## 🔒 安全配置

### 1. 保护配置文件

确保 `config.php` 不在 Web 根目录，或使用 `.htaccess` 保护：

```apache
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

### 2. 设置环境变量

对于敏感信息，考虑使用环境变量：

```php
// 在 config.php 中
$db_pass = getenv('DB_PASSWORD') ?: 'default_password';
```

### 3. 启用 HTTPS

使用 Let's Encrypt 或其他 SSL 证书提供商启用 HTTPS。

## 🧪 部署后测试

1. **测试前端访问**
   ```
   https://your-domain.com
   ```

2. **测试 API 端点**
   ```bash
   curl https://your-domain.com/api/save_vin_data.php?action=get&vin=TESTVIN1234567890
   ```

3. **测试 VIN 解码功能**
   - 在前端输入有效的 VIN 码
   - 检查数据是否正确保存到数据库
   - 验证缓存功能是否正常工作

## 📊 监控和日志

### PHP 错误日志

错误日志位置：`/var/www/daqauto/php_errors.log`

查看日志：
```bash
tail -f /var/www/daqauto/php_errors.log
```

### Web 服务器日志

- Nginx: `/var/log/nginx/access.log` 和 `/var/log/nginx/error.log`
- Apache: `/var/log/apache2/access.log` 和 `/var/log/apache2/error.log`

## 🔄 更新部署

1. 备份当前版本
2. 拉取最新代码
3. 运行 `npm install` 安装新依赖
4. 运行 `npm run build` 重新构建
5. 上传新文件
6. 清除浏览器缓存测试

## ❓ 故障排除

### 问题：API 返回 500 错误

- 检查 PHP 错误日志
- 验证数据库连接配置
- 确认文件权限正确

### 问题：CORS 错误

- 检查 `save_vin_data.php` 中的 CORS 头设置
- 验证 Web 服务器配置

### 问题：VIN 解码失败

- 检查网络连接
- 验证 API 配置
- 查看浏览器控制台错误

---

**提示：** 建议在生产环境使用进程管理器（如 PM2）或 Docker 容器化部署。
