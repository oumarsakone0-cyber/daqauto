# products.php 接口合规检查报告（不修改代码）

基于 `c:\Users\16641\products.php` 的接口约定，对当前项目前端/接口调用做符合性检查。

---

## 一、products.php 中与 VIN 相关的接口

### 1. decode-vin（GET）

| 项目 | 要求 |
|------|------|
| 路由 | `handleGet($action)`，`$action` 来自 `$_GET['action']` |
| 触发 | `?action=decode-vin` |
| 方法 | **GET** |
| VIN 来源 | `$_GET['vin']` |
| 后端逻辑 | `decodeVIN()`，调用 NHTSA 免费 API，返回 `car_make/car_model/...` 等字段 |

### 2. decodeVIN2（POST）

| 项目 | 要求 |
|------|------|
| 路由 | `handlePost($action)`，`$action` 来自 `$_GET['action']` |
| 触发 | **必须** `?action=decodeVIN2`（驼峰，无连字符） |
| 方法 | **POST** |
| VIN 来源 | POST body JSON：`{ "vin": "17位VIN码" }`，从 `php://input` 解析 |
| 校验 | VIN 必填且长度 17 |
| 成功响应（数据库命中） | `200` + `{ "success": true, "message": "...", "data": { ... }, "source": "database" }` |
| 成功响应（外部 API） | `200` + `{ "success": true, "message": "...", "data": { brand_name, series_name, model_list, ... }, "source": "api" }` |
| 错误响应 | `400`/`405`/`500` + `{ "success": false, "error": "..." }` |
| 说明 | 先查表 `vin_decoded_data`，有则返回；无则调 Tanshu API，**不写库** |

---

## 二、当前前端与 products.php 的对应关系

### 1. 当前前端实际调用

- **AddProductModal.vue** 中“Decode”按钮调用的是 **`decodeVIN(vin)`**，不是 `decodeVIN2`。
- `decodeVIN` 内部使用 **`vinDataApi.getVinData(vin)`** 和（未命中时）外部 VIN 解码逻辑。
- **api.js** 中：
  - **vinDataApi.getVinData(vin)**：请求 `https://daqauto.com/apitest/save_vin_data.php?action=get&vin=...`（**GET**）。
  - **vinDataApi.saveVinData(vinData)**：请求 `https://daqauto.com/apitest/save_vin_data.php`（**POST**，保存 VIN 数据）。

即：当前前端 **没有** 调用 `products.php` 的 **decodeVIN2**，也没有调用 `products.php` 的 **decode-vin**；VIN 的“查库 + 解码”走的是 **save_vin_data.php**，不是 **products.php**。

### 2. 与 products.php 的符合性结论

| 接口 | 前端是否调用 | 是否符合 products.php 约定 |
|------|--------------|----------------------------|
| **decode-vin** (GET) | 否 | 未调用，无法判定；若将来调用需用 GET + `?action=decode-vin&vin=xxx` |
| **decodeVIN2** (POST) | 否 | 未调用；若将来调用需：POST + `?action=decodeVIN2` + body `{ "vin": "17位" }` |

结论：**当前代码没有使用 products.php 的 VIN 接口**，因此既没有违反、也没有依赖 products.php 的 decode-vin/decodeVIN2 约定；与 `c:\Users\16641\products.php` 的“要求”在 VIN 相关部分无冲突，只是未对接。

---

## 三、若要让前端走 products.php 的 decodeVIN2（仅说明，不修改）

要符合 `c:\Users\16641\products.php` 的 decodeVIN2，需满足：

1. **请求**  
   - 方法：**POST**  
   - URL：`{base}/products.php?action=decodeVIN2`（action 必须为 **decodeVIN2**，不能是 `decode-vin2`）。  
   - Body：`Content-Type: application/json`，内容 `{ "vin": "17位VIN" }`。

2. **响应处理**  
   - 成功：`result.success === true`，车辆数据在 `result.data`，来源在 `result.source`（`'database'` | `'api'`）。  
   - 错误：`result.success === false` 或 HTTP 4xx/5xx，错误信息在 `result.error`。

3. **后端当前仅支持 action=decodeVIN2**  
   - 文件中只有 `case 'decodeVIN2':`，没有 `case 'decode-vin2':`。  
   - 若前端传 `action=decode-vin2`，会落入 `default`，返回 404 “Action non trouvée”。

---

## 四、products.php 其它与本次检查相关的点

- **action 来源**：所有 action 均来自 **GET** 参数 `$_GET['action']`（POST 请求时也是 URL 上的 `?action=xxx`）。
- **响应格式**：`sendResponse($statusCode, $data)` 会设置 `http_response_code($statusCode)` 并输出 `json_encode($data, JSON_UNESCAPED_UNICODE)`，即响应体就是传入的 `$data` 的 JSON。

---

**总结**：在不修改任何代码的前提下，当前项目**未使用** products.php 的 decode-vin 与 decodeVIN2，因此与 `c:\Users\16641\products.php` 的 VIN 接口约定**无不符合之处**；若后续要对接 decodeVIN2，需按上文第三节的请求/响应约定调用，且 action 必须为 **decodeVIN2**。
