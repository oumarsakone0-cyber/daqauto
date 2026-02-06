-- Add model_list column to vin_decoded_data table / 为vin_decoded_data表添加model_list字段
-- Execute this SQL statement to add the column (if column doesn't exist) / 执行此SQL语句来添加字段（如果字段不存在）

ALTER TABLE `vin_decoded_data` 
ADD COLUMN IF NOT EXISTS `model_list` TEXT DEFAULT NULL COMMENT 'Model list (JSON format) / 车型列表（JSON格式）' AFTER `language`;

-- If MySQL version doesn't support IF NOT EXISTS, use the following statement: / 如果MySQL版本不支持IF NOT EXISTS，使用以下语句：
-- ALTER TABLE `vin_decoded_data` 
-- ADD COLUMN `model_list` TEXT DEFAULT NULL COMMENT 'Model list (JSON format) / 车型列表（JSON格式）' AFTER `language`;
