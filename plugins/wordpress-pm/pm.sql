		CREATE TABLE wp_pm_conversation (
			id bigint(200) NOT NULL auto_increment,
			sender bigint(200) NOT NULL,
			reciever  bigint(200) NOT NULL,
			seen tinytext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		CREATE TABLE wp_pm_messages (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NOT NULL,
			attachment_id bigint(200) NULL,
			sender_id bigint(200) NOT NULL,
			reciever_id bigint(200) NOT NULL,
			message longtext NULL,
			status tinytext NULL,
			seen tinytext NULL,
			delete_status boolean DEFAULT 0 NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		CREATE TABLE wp_pm_deleted_conversation (
			id bigint(200) NOT NULL auto_increment,
			user bigint(200) NOT NULL,
			conv_id bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		CREATE TABLE wp_pm_blocked_conversation (
			id bigint(200) NOT NULL auto_increment,
			blocked_by bigint(200) NOT NULL,
			blocked_user bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		CREATE TABLE wp_pm_attachments (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NULL,
			type tinytext NULL,
			size bigint(200) NULL,
			url longtext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;