# 更新日志

## [准备发布] - 2026-02-04

### ✨ 新增功能
- VIN 码解码功能，支持多车型选择
- 数据库缓存机制，提高查询性能
- 自动翻译功能，将 API 返回数据翻译为英文
- 配置文件支持，便于环境管理

### 🔧 优化改进
- 统一数据库配置管理，支持通过 `config.php` 配置
- 优化代码结构，移除重复声明
- 改进错误处理和日志记录
- 增强 CORS 支持

### 🗑️ 清理
- 删除所有测试文件（test_*.php, test_*.html）
- 删除调试文件（debug_post.php, view_errors.php, check_errors.php）
- 删除临时文档和指南文件
- 删除未使用的 PHP 文件

### 📝 文档
- 创建完整的 README.md 快速开始指南
- 创建 DEPLOYMENT.md 部署指南
- 创建 config.example.php 配置文件示例
- 更新 .gitignore 确保敏感文件不被提交

### 🔒 安全
- 将敏感信息（数据库密码）移至配置文件
- 更新 .gitignore 排除配置文件
- 添加配置文件示例供参考

### 🐛 修复
- 修复 `originalModelList` 变量重复声明错误
- 修复 `model_list` 字段保存和检索问题
- 优化数据库连接超时处理

---

## 技术栈

- **前端**: Vue 3 + Vite + Tailwind CSS
- **后端**: PHP 7.4+
- **数据库**: MySQL 5.7+
- **状态管理**: Pinia
- **路由**: Vue Router 4

## 主要依赖

- vue: ^3.5.13
- vite: ^6.3.5
- axios: ^1.9.0
- pinia: ^3.0.3
- vue-router: ^4.5.1
- tailwindcss: ^4.1.7
