
ALTER TABLE links
  ADD COLUMN group_include character varying[];
ALTER TABLE links
  ADD COLUMN group_exclude character varying[];

ALTER TABLE files
  ADD COLUMN group_include character varying[];
ALTER TABLE files
  ADD COLUMN group_exclude character varying[];

ALTER TABLE configs
  ADD COLUMN group_include character varying[];
ALTER TABLE configs
  ADD COLUMN group_exclude character varying[];

ALTER TABLE commands_idle
  ADD COLUMN group_include character varying[];
ALTER TABLE commands_idle
  ADD COLUMN group_exclude character varying[];
