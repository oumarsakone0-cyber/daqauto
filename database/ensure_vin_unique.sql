-- Ensure same VIN is only stored once / 保证同一 VIN 只保存一条
-- Run if your table does not yet have a unique constraint on vin.
-- 若表尚未对 vin 建唯一约束，请执行此脚本。

ALTER TABLE `vin_decoded_data`
ADD UNIQUE KEY `uk_vin` (`vin`);

-- If the above fails (e.g. duplicate VINs already exist), remove duplicates first, then run again.
-- 若报错（例如已有重复 VIN），请先清理重复数据后再执行。
