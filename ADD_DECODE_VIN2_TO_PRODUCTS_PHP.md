# 如何将 decodeVIN2() 添加到 products.php

## 概述 / Overview

本文档说明如何将 `decodeVIN2()` 函数集成到您的 `products.php` 文件中。

This document explains how to integrate the `decodeVIN2()` function into your `products.php` file.

## 步骤 / Steps

### 1. 添加 decodeVIN2() 方法到类中 / Add decodeVIN2() method to class

在 `products.php` 的类中，找到 `decodeVIN()` 方法的位置，在其后添加 `decodeVIN2()` 方法。

In the `products.php` class, find the location of the `decodeVIN()` method and add the `decodeVIN2()` method after it.

**将 `decodeVIN2_class_method.php` 文件中的整个方法复制到 products.php 的类中。**

**Copy the entire method from `decodeVIN2_class_method.php` into the class in products.php.**

### 2. 添加 switch case / Add switch case

在 `products.php` 的 `switch ($action)` 语句中，添加以下 case：

In the `switch ($action)` statement in `products.php`, add the following case:

```php
case 'decode-vin2':
    $this->decodeVIN2();
    break;
```

**完整示例 / Full example:**

```php
switch ($action) {
    case 'decode-vin':
        $this->decodeVIN();
        break;
    
    case 'decode-vin2':
        $this->decodeVIN2();
        break;
    
    // ... 其他 cases / other cases ...
}
```

### 3. 验证数据库配置 / Verify database configuration

确保 `decodeVIN2()` 方法中的数据库配置正确：

Ensure the database configuration in the `decodeVIN2()` method is correct:

- 如果存在 `config.php` 文件，函数会自动使用其中的配置
- If `config.php` exists, the function will automatically use its configuration
- 如果不存在，请修改函数中的默认配置值
- If it doesn't exist, modify the default configuration values in the function

### 4. 测试 / Testing

1. 确保前端已更新（`api.js` 和 `AddProductModal.vue` 已包含 `decodeVIN2` 相关代码）
   Ensure the frontend is updated (`api.js` and `AddProductModal.vue` contain `decodeVIN2` related code)

2. 在浏览器中测试 VIN 解码功能
   Test the VIN decoding functionality in the browser

3. 检查浏览器控制台和服务器日志以查看调试信息
   Check browser console and server logs for debugging information

## 功能说明 / Function Description

`decodeVIN2()` 函数的功能：

The functionality of the `decodeVIN2()` function:

1. **数据库检查 / Database Check**: 首先检查 VIN 是否存在于数据库中
   First checks if the VIN exists in the database

2. **数据库返回 / Database Return**: 如果找到，直接返回数据库中的数据（已经是英文格式）
   If found, directly returns the data from the database (already in English format)

3. **外部 API 调用 / External API Call**: 如果未找到，调用 Tanshu API 获取数据
   If not found, calls Tanshu API to get data

4. **数据返回 / Data Return**: 返回 API 数据（不保存到数据库，根据用户要求）
   Returns API data (does not save to database, as per user requirements)

## 注意事项 / Notes

- `decodeVIN2()` 方法应该是 `private` 方法
- The `decodeVIN2()` method should be a `private` method
- 确保类中有 `sendResponse()` 方法用于发送响应
- Ensure the class has a `sendResponse()` method for sending responses
- 如果您的类响应方式不同，请告诉我具体格式，我可以调整代码
- If your class response method is different, let me know the specific format and I can adjust the code

## 文件清单 / File Checklist

- [ ] `decodeVIN2()` 方法已添加到类中
- [ ] `decodeVIN2()` method added to class
- [ ] `case 'decode-vin2'` 已添加到 switch 语句中
- [ ] `case 'decode-vin2'` added to switch statement
- [ ] 数据库配置已验证
- [ ] Database configuration verified
- [ ] 前端代码已更新（`api.js` 和 `AddProductModal.vue`）
- [ ] Frontend code updated (`api.js` and `AddProductModal.vue`)
- [ ] 功能测试通过
- [ ] Functionality tested and working
