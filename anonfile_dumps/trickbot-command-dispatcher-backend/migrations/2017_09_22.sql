update files set group_include='{}', group_exclude='{}';
update commands_idle set group_include='{}', group_exclude='{}';
update configs set group_include='{}', group_exclude='{}';
update links set group_include='{}', group_exclude='{}';

ALTER TABLE commands_idle
   ALTER COLUMN group_include SET DEFAULT '{}';
UPDATE commands_idle SET group_include = '{}' WHERE group_include IS NULL;
ALTER TABLE commands_idle
   ALTER COLUMN group_include SET NOT NULL;

ALTER TABLE commands_idle
   ALTER COLUMN group_exclude SET DEFAULT '{}';
UPDATE commands_idle SET group_exclude = '{}' WHERE group_exclude IS NULL;
ALTER TABLE commands_idle
   ALTER COLUMN group_exclude SET NOT NULL;


ALTER TABLE configs
   ALTER COLUMN group_include SET DEFAULT '{}';
UPDATE configs SET group_include = '{}' WHERE group_include IS NULL;
ALTER TABLE configs
   ALTER COLUMN group_include SET NOT NULL;

ALTER TABLE configs
   ALTER COLUMN group_exclude SET DEFAULT '{}';
UPDATE configs SET group_exclude = '{}' WHERE group_exclude IS NULL;
ALTER TABLE configs
   ALTER COLUMN group_exclude SET NOT NULL;

ALTER TABLE files
   ALTER COLUMN group_include SET DEFAULT '{}';
UPDATE files SET group_include = '{}' WHERE group_include IS NULL;
ALTER TABLE files
   ALTER COLUMN group_include SET NOT NULL;

ALTER TABLE files
   ALTER COLUMN group_exclude SET DEFAULT '{}';
UPDATE files SET group_exclude = '{}' WHERE group_exclude IS NULL;
ALTER TABLE files
   ALTER COLUMN group_exclude SET NOT NULL;

ALTER TABLE links
   ALTER COLUMN group_include SET DEFAULT '{}';
UPDATE links SET group_include = '{}' WHERE group_include IS NULL;
ALTER TABLE links
   ALTER COLUMN group_include SET NOT NULL;

ALTER TABLE links
   ALTER COLUMN group_exclude SET DEFAULT '{}';
UPDATE links SET group_exclude = '{}' WHERE group_exclude IS NULL;
ALTER TABLE links
   ALTER COLUMN group_exclude SET NOT NULL;
