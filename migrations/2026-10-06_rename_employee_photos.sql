-- Фото сотрудников переименованы по схеме <id сотрудника>_<время загрузки>.<расширение>
-- Применять один раз к уже существующей базе (arch.sql уже содержит новые имена).

SET NAMES utf8mb4;

UPDATE employees SET img_path = '16_2026-03-19_00-13-33.jpeg' WHERE id = 16 AND img_path = '1773868413__ (50).jpeg';
UPDATE employees SET img_path = '17_2026-03-19_00-21-10.jpeg' WHERE id = 17 AND img_path = '1773868870_Tʜᴇ Nᴀʀʀᴀᴛᴏʀ _ Iᴄᴏɴ.jpeg';
UPDATE employees SET img_path = '18_2026-03-19_00-24-49.jpeg' WHERE id = 18 AND img_path = '1773869089__ (51).jpeg';
UPDATE employees SET img_path = '20_2026-03-19_00-33-41.jpeg' WHERE id = 20 AND img_path = '1773869621_saul goodman.jpeg';
UPDATE employees SET img_path = '21_2026-03-19_00-35-45.jpeg' WHERE id = 21 AND img_path = '1773869745_BCS.jpeg';
UPDATE employees SET img_path = '23_2026-03-19_00-37-32.jpeg' WHERE id = 23 AND img_path = '1773869852_antónio chigolho.jpeg';
UPDATE employees SET img_path = '24_2026-03-19_00-37-48.jpeg' WHERE id = 24 AND img_path = '1773869868_Tony Soprano.jpeg';
UPDATE employees SET img_path = '25_2026-03-19_00-41-03.jpeg' WHERE id = 25 AND img_path = '1773870063__ (52).jpeg';
UPDATE employees SET img_path = '26_2026-03-19_00-46-23.jpeg' WHERE id = 26 AND img_path = '1773870383_Joaquin Phoenix.jpeg';
UPDATE employees SET img_path = '27_2026-03-19_00-47-41.jpeg' WHERE id = 27 AND img_path = '1773870461__ (53).jpeg';
UPDATE employees SET img_path = '28_2026-03-19_00-50-05.jpeg' WHERE id = 28 AND img_path = '1773870605__ (55).jpeg';
UPDATE employees SET img_path = '29_2026-03-19_00-51-16.jpeg' WHERE id = 29 AND img_path = '1773870676_ᴊɪᴍ ᴄᴀʀʀᴇʏ.jpeg';
