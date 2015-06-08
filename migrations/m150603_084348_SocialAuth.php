<?php

class m150603_084348_SocialAuth extends CDbMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `SocialAuth` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `userId` int(11) NOT NULL,
              `provider` varchar(100) NOT NULL,
              `identifier` varchar(100) NOT NULL,
              `createDate` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE (`provider`, `identifier`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    public function down()
    {
        $this->execute("
            DROP TABLE IF EXISTS `SocialAuth`;
        ");
    }
}