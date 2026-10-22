-- Existing users keep their current roles. An administrator must explicitly
-- grant Super User to people who may create/re-create application forms.
ALTER TABLE `users` ADD COLUMN `is_super_user` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_mr`;
