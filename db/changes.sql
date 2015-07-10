-- 2015-07-09
ALTER TABLE `ping_result`
ADD COLUMN `connect_time_avg` float(50);

ALTER TABLE `ping_result`
ADD COLUMN `connect_time_max` float(50);

ALTER TABLE `ping_result`
ADD COLUMN `total_time_avg` float(50);

ALTER TABLE `ping_result`
ADD COLUMN `total_time_max` float(50);

ALTER TABLE `ping_result`
ADD COLUMN `total` int;

ALTER TABLE `ping_result`
ADD COLUMN `failed` int;
