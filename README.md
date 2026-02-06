# DaqAuto - 汽车零部件电商平台

一个基于 Vue 3 + Vite 构建的现代化汽车零部件电商平台，支持 VIN 码解码、产品管理、订单处理等功能。

## ✨ 主要功能

- 🚗 **VIN 码解码**：自动识别车辆信息，支持多车型选择
- 📦 **产品管理**：完整的商品添加、编辑、删除功能
- 🛒 **购物车系统**：支持多商品购物车管理
- 📊 **数据分析**：产品销量和订单统计
- 💬 **实时聊天**：WebSocket 支持的客服聊天系统
- 🌍 **多语言支持**：自动翻译功能
- 🎨 **响应式设计**：完美适配移动端和桌面端

## 🚀 快速开始

### 环境要求

- Node.js >= 16.0.0
- npm >= 7.0.0
- PHP >= 7.4
- MySQL >= 5.7

### 安装步骤

1. **克隆项目**
   ```bash
   git clone <repository-url>
   cd daqauto-main
   ```

2. **安装依赖**
   ```bash
   npm install
   ```

3. **配置数据库**
   ```bash
   # 复制配置文件示例
   cp config.example.php config.php
   
   # 编辑 config.php，填入您的数据库信息
   ```

4. **创建数据库表**
   ```sql
   -- 执行 database/create_cart_table.sql 创建购物车表
   -- 执行 add_model_list_column.sql 添加 model_list 字段（如果表已存在）
   ```

5. **配置 VIN API**
   
   编辑 `vite.config.js`，确保 VIN API 代理配置正确：
   ```javascript
   proxy: {
     '/api/vin': {
       target: 'https://api.tanshuapi.com',
       changeOrigin: true,
       rewrite: (path) => path.replace(/^\/api\/vin/, '/api/vin'),
       secure: true,
     }
   }
   ```

6. **启动开发服务器**
   ```bash
   npm run dev
   ```

   访问 `http://localhost:5173` 查看应用

### 生产环境部署

1. **构建项目**
   ```bash
   npm run build
   ```

2. **上传文件**
   - 将 `dist` 目录内容上传到 Web 服务器
   - 将 `save_vin_data.php` 上传到服务器 API 目录
   - 确保 `config.php` 已正确配置且不在公开目录

3. **配置 Web 服务器**
   
   **Nginx 示例配置：**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/dist;
       index index.html;

       location / {
           try_files $uri $uri/ /index.html;
       }

       location /api/ {
           proxy_pass http://localhost:80;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
       }
   }
   ```

## 📁 项目结构

```
daqauto-main/
├── src/                    # 源代码目录
│   ├── components/         # Vue 组件
│   │   ├── boutiques/     # 店铺管理组件
│   │   ├── product/       # 产品相关组件
│   │   └── ...
│   ├── composables/       # 组合式函数
│   ├── services/          # API 服务
│   ├── stores/            # Pinia 状态管理
│   └── router/            # 路由配置
├── public/                # 静态资源
├── database/              # 数据库脚本
├── save_vin_data.php      # VIN 数据 API 端点
├── config.example.php      # 配置文件示例
├── vite.config.js         # Vite 配置
└── package.json           # 项目依赖
```

## 🔧 配置说明

### 数据库配置

创建 `config.php` 文件（基于 `config.example.php`）：

```php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'your_database_name',
        'username' => 'your_username',
        'password' => 'your_password',
        'charset' => 'utf8mb4'
    ],
    // ...
];
```

### VIN API 配置

VIN 解码功能需要配置外部 API。当前使用 TanshuAPI，您可以在 `vite.config.js` 中修改代理配置。

## 📝 API 文档

### VIN 数据 API

**端点：** `save_vin_data.php`

**保存 VIN 数据（POST）**
```javascript
POST /save_vin_data.php
Content-Type: application/json

{
  "vin": "LFP84ACP8E1G21741",
  "brand_name": "Toyota",
  "series_name": "Camry",
  // ... 其他车辆信息
}
```

**查询 VIN 数据（GET）**
```javascript
GET /save_vin_data.php?action=get&vin=LFP84ACP8E1G21741
```

## 🛠️ 开发指南

### 添加新功能

1. 在 `src/components` 中创建新组件
2. 在 `src/router/index.js` 中添加路由（如需要）
3. 在 `src/services` 中添加 API 调用（如需要）

### 代码规范

- 使用 Vue 3 Composition API (`<script setup>`)
- 遵循 ESLint 规则
- 组件命名使用 PascalCase
- 文件命名使用 PascalCase（组件）或 camelCase（工具函数）

## 🐛 常见问题

### VIN 解码失败

- 检查网络连接和 API 配置
- 确认 VIN 码格式正确（17 位字符）
- 查看浏览器控制台和 PHP 错误日志

### 数据库连接失败

- 确认 `config.php` 配置正确
- 检查数据库服务是否运行
- 验证数据库用户权限

### CORS 错误

- 确保 `save_vin_data.php` 中的 CORS 头设置正确
- 检查服务器配置是否允许跨域请求

## 📄 许可证

[在此添加您的许可证信息]

## 👥 贡献

欢迎提交 Issue 和 Pull Request！

## 📞 联系方式

[在此添加联系方式]

---

**注意：** 在生产环境部署前，请确保：
- ✅ 已创建并配置 `config.php`
- ✅ 已执行数据库迁移脚本
- ✅ 已设置正确的环境变量
- ✅ 已配置 Web 服务器
- ✅ 已测试所有功能
