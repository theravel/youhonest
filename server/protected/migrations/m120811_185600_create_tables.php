<?php

class m120811_185600_create_tables extends CDbMigration
{
	public function up()
	{
        $this->execute("
            CREATE TABLE `authorizations_fb` (
              `authorization_id` int(11) NOT NULL,
              PRIMARY KEY  (`authorization_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE `authorizations_vk` (
              `authorization_id` int(11) NOT NULL auto_increment,
              PRIMARY KEY  (`authorization_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE `comments_vk` (
              `comment_id` int(11) NOT NULL auto_increment,
              `authorization_id` int(11) NOT NULL,
              `date` int(11) NOT NULL,
              PRIMARY KEY  (`comment_id`),
              KEY `Index by user` (`authorization_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE `connections` (
              `user_id` int(11) NOT NULL auto_increment,
              `network_id` int(11) NOT NULL,
              `authorization_id` int(11) NOT NULL,
              UNIQUE KEY `PK` (`user_id`,`network_id`,`authorization_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE `dislikes_vk` (
              `dislike_id` int(11) NOT NULL auto_increment,
              `authorization_id` int(11) NOT NULL,
              `date` int(11) NOT NULL,
              PRIMARY KEY  (`dislike_id`),
              KEY `Index by user` (`authorization_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE `networks` (
              `network_id` int(11) NOT NULL auto_increment,
              `name` varchar(50) NOT NULL,
              `url_pattern` varchar(100) NOT NULL,
              `enabled` tinyint(4) NOT NULL default '0',
              `authorization_table` varchar(50) NOT NULL,
              `dislike_table` varchar(50) NOT NULL,
              `comment_table` varchar(50) NOT NULL,
              PRIMARY KEY  (`network_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            INSERT INTO `networks` VALUES ('1', 'VKontakte', '(vk.com)|(vkontakte.ru)', '1', 'authorizations_vk', 'dislikes_vk', '');
        ");
	}

	public function down()
	{
		$this->execute("
            DROP TABLE IF EXISTS `authorizations_fb`;
        ");
		$this->execute("
            DROP TABLE IF EXISTS `authorizations_vk`;
        ");
		$this->execute("
            DROP TABLE IF EXISTS `comments_vk`;
        ");
		$this->execute("
            DROP TABLE IF EXISTS `connections`;
        ");
		$this->execute("
            DROP TABLE IF EXISTS `dislikes_vk`;
        ");
		$this->execute("
            DROP TABLE IF EXISTS `networks`;
        ");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}