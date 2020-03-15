CREATE TABLE IF NOT EXISTS `#__kunena_aliases`
(
    `alias` varchar(255) NOT NULL,
    `type`  varchar(10)  NOT NULL,
    `item`  varchar(32)  NOT NULL,
    `state` tinyint(4)   NOT NULL default '0',
    UNIQUE KEY `alias` (alias),
    KEY `state` (state),
    KEY `item` (item),
    KEY `type` (type)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_announcement`
(
    `id`           int(3)     NOT NULL auto_increment,
    `title`        tinytext   NOT NULL,
    `created_by`   int(11)    NOT NULL default '0',
    `sdescription` text       NOT NULL,
    `description`  text       NOT NULL,
    `created`      datetime            DEFAULT NULL,
    `published`    tinyint(1) NOT NULL default '0',
    `publish_up`   datetime            DEFAULT NULL,
    `publish_down` datetime            DEFAULT NULL,
    `ordering`     tinyint(4) NOT NULL default '0',
    `showdate`     tinyint(1) NOT NULL default '1',
    PRIMARY KEY (id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_attachments`
(
    `id`            int(11)      NOT NULL auto_increment,
    `mesid`         int(11)      NOT NULL default '0',
    `userid`        int(11)      NOT NULL default '0',
    `protected`     tinyint(4)   NOT NULL default '0',
    `hash`          char(32)     NULL,
    `size`          int(11)      NULL,
    `folder`        varchar(255) NOT NULL,
    `filetype`      varchar(20)  NOT NULL,
    `filename`      varchar(190) NOT NULL,
    `filename_real` varchar(190) NOT NULL default ''
        COMMENT 'Filename for downloads',
    `caption`       varchar(255) NOT NULL default '',
    `inline`        tinyint(4)   NOT NULL default '0',
    PRIMARY KEY (id),
    KEY `mesid` (mesid),
    KEY `userid` (userid),
    KEY `hash` (hash),
    KEY `filename` (filename),
    KEY `filename_real` (filename_real)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_categories`
(
    `id`               int(11)      NOT NULL auto_increment,
    `parent_id`        int(11)      NULL     default '0',
    `name`             tinytext     NULL,
    `alias`            varchar(255) NOT NULL,
    `icon`             varchar(20)  NOT NULL,
    `icon_id`          tinyint(4)   NOT NULL default '0',
    `locked`           tinyint(4)   NOT NULL default '0',
    `accesstype`       varchar(20)  NOT NULL default 'joomla.level',
    `access`           int(11)      NOT NULL default '0',
    `pub_access`       int(11)      NOT NULL default '1',
    `pub_recurse`      tinyint(4)   NULL     default '1',
    `admin_access`     int(11)      NOT NULL default '0',
    `admin_recurse`    tinyint(4)   NULL     default '1',
    `ordering`         int(11)      NOT NULL default '0',
    `published`        tinyint(4)   NOT NULL default '0',
    `channels`         text         NULL,
    `checked_out`      int(11)      NOT NULL default '0',
    `checked_out_time` datetime     NULL     DEFAULT NULL,
    `review`           tinyint(4)   NOT NULL default '0',
    `allow_anonymous`  tinyint(4)   NOT NULL default '0',
    `post_anonymous`   tinyint(4)   NOT NULL default '0',
    `hits`             int(11)      NOT NULL default '0',
    `description`      text         NOT NULL,
    `headerdesc`       text         NOT NULL,
    `topictemplate`    text         NOT NULL,
    `class_sfx`        varchar(20)  NOT NULL,
    `allow_polls`      tinyint(4)   NOT NULL default '0',
    `topic_ordering`   varchar(16)  NOT NULL default 'lastpost',
    `iconset`          varchar(255) NOT NULL,
    `numTopics`        mediumint(8) NOT NULL default '0',
    `numPosts`         mediumint(8) NOT NULL default '0',
    `last_topic_id`    int(11)      NOT NULL default '0',
    `last_post_id`     int(11)      NOT NULL default '0',
    `last_post_time`   int(11)      NOT NULL default '0',
    `params`           text         NOT NULL,
    `allow_ratings`    tinyint(4)   NOT NULL default '0',
    PRIMARY KEY (id),
    KEY `parent_id` (parent_id),
    KEY `category_access` (accesstype, access),
    KEY `published_pubaccess_id` (published, pub_access, id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_configuration`
(
    `id`     int(11) NOT NULL default '0',
    `params` text    NULL,
    PRIMARY KEY (id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_topics`
(
    `id`                    int(11)    NOT NULL auto_increment,
    `category_id`           int(11)    NOT NULL default '0',
    `subject`               tinytext   NULL,
    `icon_id`               int(11)    NOT NULL default '0',
    `label_id`              int(11)    NOT NULL default '0',
    `locked`                tinyint(4) NOT NULL default '0',
    `hold`                  tinyint(4) NOT NULL default '0',
    `ordering`              int(11)    NOT NULL default '0',
    `posts`                 int(11)    NOT NULL default '0',
    `hits`                  int(11)    NOT NULL default '0',
    `attachments`           int(11)    NOT NULL default '0',
    `poll_id`               int(11)    NOT NULL default '0',
    `moved_id`              int(11)    NOT NULL default '0',
    `first_post_id`         int(11)    NOT NULL default '0',
    `first_post_time`       int(11)    NOT NULL default '0',
    `first_post_userid`     int(11)    NOT NULL default '0',
    `first_post_message`    text       NULL,
    `first_post_guest_name` tinytext   NULL,
    `last_post_id`          int(11)    NOT NULL default '0',
    `last_post_time`        int(11)    NOT NULL default '0',
    `last_post_userid`      int(11)    NOT NULL default '0',
    `last_post_message`     text       NULL,
    `last_post_guest_name`  tinytext   NULL,
    `rating`                tinyint(6) NOT NULL default '0',
    `params`                text       NULL,
    PRIMARY KEY (id),
    KEY `category_id` (category_id),
    KEY `locked` (locked),
    KEY `hold` (hold),
    KEY `posts` (posts),
    KEY `hits` (hits),
    KEY `first_post_userid` (first_post_userid),
    KEY `last_post_userid` (last_post_userid),
    KEY `first_post_time` (first_post_time),
    KEY `last_post_time` (last_post_time),
    KEY `last_post_id` (last_post_id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_messages`
(
    `id`              int(11)      NOT NULL auto_increment,
    `parent`          int(11)      NULL     default '0',
    `thread`          int(11)      NULL     default '0',
    `catid`           int(11)      NOT NULL default '0',
    `name`            tinytext     NULL,
    `userid`          int(11)      NOT NULL default '0',
    `email`           tinytext     NULL,
    `subject`         tinytext     NULL,
    `time`            int(11)      NOT NULL default '0',
    `ip`              varchar(128) NULL,
    `topic_emoticon`  int(11)      NOT NULL default '0',
    `locked`          tinyint(4)   NOT NULL default '0',
    `hold`            tinyint(4)   NOT NULL default '0',
    `ordering`        int(11)      NULL     default '0',
    `hits`            int(11)      NULL     default '0',
    `moved`           tinyint(4)   NULL     default '0',
    `modified_by`     int(7)       NULL,
    `modified_time`   int(11)      NULL,
    `modified_reason` tinytext     NULL,
    PRIMARY KEY (id),
    KEY `thread` (thread),
    KEY `ip` (ip),
    KEY `userid` (userid),
    KEY `locked` (locked),
    KEY `parent_hits` (parent, hits),
    KEY `catid_parent` (catid, parent),
    KEY `time_hold` (time, hold),
    KEY `hold` (hold)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_messages_text`
(
    `mesid`   int(11)    NOT NULL default '0',
    `message` mediumtext NOT NULL,
    PRIMARY KEY (mesid)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_polls`
(
    `id`             int(11)      NOT NULL auto_increment,
    `title`          varchar(150) NOT NULL,
    `threadid`       int(11)      NOT NULL,
    `polltimetolive` datetime     NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY `threadid` (threadid)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_polls_options`
(
    `id`     int(11)      NOT NULL auto_increment,
    `pollid` int(11)      NULL,
    `text`   varchar(100) NULL,
    `votes`  int(11)      NULL,
    PRIMARY KEY (id),
    KEY `pollid` (pollid)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_polls_users`
(
    `pollid`   int(11)   NULL,
    `userid`   int(11)   NULL,
    `votes`    int(11)   NULL,
    `lasttime` timestamp NOT NULL default '0000-00-00 00:00:00',
    `lastvote` int(11)   NULL,
    UNIQUE KEY `pollid` (pollid, userid)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_ranks`
(
    `rank_id`      mediumint(8) unsigned NOT NULL auto_increment,
    `rank_title`   varchar(255)          NOT NULL default '',
    `rank_min`     mediumint(8) unsigned NOT NULL default '0',
    `rank_special` tinyint(1) unsigned   NOT NULL default '0',
    `rank_image`   varchar(255)          NOT NULL default '',
    PRIMARY KEY (rank_id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_rate`
(
    `id`       int(11)               NOT NULL auto_increment,
    `topic_id` int(11)               NULL,
    `userid`   int(11)               NULL,
    `rate`     mediumint(8) unsigned NOT NULL default '0',
    `time`     DATETIME              NULL     DEFAULT NULL,
    PRIMARY KEY (id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_sessions`
(
    `userid`     int(11) NOT NULL default '0',
    `allowed`    text    NULL,
    `lasttime`   int(11) NOT NULL default '0',
    `readtopics` text    NULL,
    `currvisit`  int(11) NOT NULL default '0',
    PRIMARY KEY (userid),
    KEY `currvisit` (currvisit)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_smileys`
(
    `id`           int(4)      NOT NULL auto_increment,
    `code`         varchar(12) NOT NULL default '',
    `location`     varchar(50) NOT NULL default '',
    `greylocation` varchar(60) NOT NULL default '',
    `emoticonbar`  tinyint(4)  NOT NULL default '0',
    PRIMARY KEY (id)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_thankyou`
(
    `postid`       int(11)  NOT NULL,
    `userid`       int(11)  NOT NULL,
    `targetuserid` int(11)  NOT NULL,
    `time`         datetime NULL DEFAULT NULL,
    UNIQUE KEY `postid` (postid, userid),
    KEY `userid` (userid),
    KEY `targetuserid` (targetuserid)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_user_categories`
(
    `user_id`     int(11)    NOT NULL,
    `category_id` int(11)    NOT NULL,
    `role`        tinyint(4) NOT NULL default '0',
    `allreadtime` int(11)    NOT NULL default '0',
    `subscribed`  tinyint(4) NOT NULL default '0',
    `params`      text       NOT NULL,
    PRIMARY KEY (user_id, category_id),
    KEY `category_subscribed` (category_id, subscribed),
    KEY `role` (role)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_user_read`
(
    `user_id`     int(11) NOT NULL,
    `topic_id`    int(11) NOT NULL,
    `category_id` int(11) NOT NULL,
    `message_id`  int(11) NOT NULL,
    `time`        int(11) NOT NULL,
    UNIQUE KEY `user_topic_id` (user_id, topic_id),
    KEY `category_user_id` (category_id, user_id),
    KEY `time` (time)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_user_topics`
(
    `user_id`      int(11)      NOT NULL default '0',
    `topic_id`     int(11)      NOT NULL default '0',
    `category_id`  int(11)      NOT NULL,
    `posts`        mediumint(8) NOT NULL default '0',
    `last_post_id` int(11)      NOT NULL default '0',
    `owner`        tinyint(4)   NOT NULL default '0',
    `favorite`     tinyint(4)   NOT NULL default '0',
    `subscribed`   tinyint(4)   NOT NULL default '0',
    `params`       text         NULL,
    UNIQUE KEY `user_topic_id` (user_id, topic_id),
    KEY `topic_id` (topic_id),
    KEY `posts` (posts),
    KEY `owner` (owner),
    KEY `favorite` (favorite),
    KEY `subscribed` (subscribed)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_users`
(
    `userid`           int(11)      NOT NULL default '0',
    `status`           tinyint(1)   NOT NULL DEFAULT '0',
    `status_text`      varchar(255) NOT NULL DEFAULT '',
    `view`             varchar(8)   NOT NULL default '',
    `signature`        text         NULL,
    `moderator`        int(11)      NULL     default '0',
    `banned`           datetime     NULL     DEFAULT NULL,
    `ordering`         int(11)      NULL     default '0',
    `posts`            int(11)      NULL     default '0',
    `avatar`           varchar(255) NULL,
    `timestamp`        int(11)      NULL     default '0',
    `karma`            int(11)      NULL     default '0',
    `karma_time`       int(11)      NULL     default '0',
    `group_id`         int(4)       NULL     default '1',
    `uhits`            int(11)      NULL     default '0',
    `personalText`     tinytext     NULL,
    `gender`           tinyint(4)   NOT NULL default '0',
    `birthdate`        date         NULL     DEFAULT NULL,
    `location`         varchar(50)  NULL,
    `friendfeed`       varchar(75)  NULL,
    `icq`              varchar(75)  NULL,
    `bebo`             varchar(75)  NULL,
    `digg`             varchar(75)  NULL,
    `telegram`         varchar(75)  NULL,
    `vk`               varchar(75)  NULL,
    `microsoft`        varchar(75)  NULL,
    `skype`            varchar(75)  NULL,
    `twitter`          varchar(75)  NULL,
    `facebook`         varchar(75)  NULL,
    `google`           varchar(75)  NULL,
    `github`           varchar(75)  NULL,
    `myspace`          varchar(75)  NULL,
    `linkedin`         varchar(75)  NULL,
    `linkedin_company` varchar(75)  NULL,
    `delicious`        varchar(75)  NULL,
    `instagram`        varchar(75)  NULL,
    `qqsocial`         varchar(75)  NULL,
    `blogspot`         varchar(75)  NULL,
    `flickr`           varchar(75)  NULL,
    `apple`            varchar(75)  NULL,
    `qzone`            varchar(75)  NULL,
    `vimeo`            varchar(75)  NULL,
    `whatsapp`         varchar(25)  NULL,
    `weibo`            varchar(75)  NULL,
    `wechat`           varchar(75)  NULL,
    `yim`              varchar(75)  NULL,
    `websitename`      varchar(50)  NULL,
    `websiteurl`       varchar(50)  NULL,
    `rank`             tinyint(4)   NOT NULL default '0',
    `hideEmail`        tinyint(1)   NOT NULL default '1',
    `showOnline`       tinyint(1)   NOT NULL default '1',
    `canSubscribe`     tinyint(1)   NOT NULL default '-1',
    `userListtime`     int(11)      NULL     default '-2',
    `thankyou`         int(11)      NULL     default '0',
    `ip`               varchar(125) NULL,
    `socialshare`      tinyint(1)   NOT NULL default '1',
    PRIMARY KEY (userid),
    KEY `group_id` (group_id),
    KEY `posts` (posts),
    KEY `uhits` (uhits),
    KEY `banned` (banned),
    KEY `moderator` (moderator)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_users_banned`
(
    `id`             int(11)      NOT NULL auto_increment,
    `userid`         int(11)      NULL,
    `ip`             varchar(128) NULL,
    `blocked`        tinyint(4)   NOT NULL default '0',
    `expiration`     datetime     NULL     DEFAULT NULL,
    `created_by`     int(11)      NOT NULL,
    `created_time`   datetime     NULL     DEFAULT NULL,
    `reason_private` text         NULL,
    `reason_public`  text         NULL,
    `modified_by`    int(11)      NULL,
    `modified_time`  datetime     NULL     DEFAULT NULL,
    `comments`       text         NULL,
    `params`         text         NULL,
    PRIMARY KEY (id),
    KEY `userid` (userid),
    KEY `ip` (ip),
    KEY `expiration` (expiration),
    KEY `created_time` (created_time)
)
    DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__kunena_version`
(
    `id`          int(11)     NOT NULL auto_increment,
    `version`     varchar(20) NOT NULL,
    `versiondate` date        NOT NULL,
    `installdate` date        NOT NULL,
    `build`       varchar(20) NOT NULL,
    `versionname` varchar(40) NULL,
    `state`       text        NOT NULL,
    PRIMARY KEY (id)
)
    DEFAULT CHARACTER SET utf8;

INSERT INTO `#__mail_templates` (`template_id`, `language`, `subject`, `body`, `htmlbody`, `attachments`, `params`)
VALUES ('com_kunena.reply', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}'),
       ('com_kunena.replymoderator', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}'),
       ('com_kunena.report', '', 'COM_CONFIG_SENDMAIL_SUBJECT', 'COM_CONFIG_SENDMAIL_BODY', '', '',
        '{"tags":["mail", "subject", "message", "messageUrl", "once"]}');

INSERT INTO `#__kunena_ranks` (`rank_id`, `rank_title`, `rank_min`, `rank_special`, `rank_image`)
VALUES (1, 'COM_KUNENA_SAMPLEDATA_RANK1', 0, 0, 'rank1.gif'),
       (2, 'COM_KUNENA_SAMPLEDATA_RANK2', 20, 0, 'rank2.gif'),
       (3, 'COM_KUNENA_SAMPLEDATA_RANK3', 40, 0, 'rank3.gif'),
       (4, 'COM_KUNENA_SAMPLEDATA_RANK4', 80, 0, 'rank4.gif'),
       (5, 'COM_KUNENA_SAMPLEDATA_RANK5', 160, 0, 'rank5.gif'),
       (6, 'COM_KUNENA_SAMPLEDATA_RANK6', 320, 0, 'rank6.gif'),
       (7, 'COM_KUNENA_SAMPLEDATA_RANK_ADMIN', 0, 1, 'rankadmin.gif'),
       (8, 'COM_KUNENA_SAMPLEDATA_RANK_MODERATOR', 0, 1, 'rankmod.gif'),
       (9, 'COM_KUNENA_SAMPLEDATA_RANK_SPAMMER', 0, 1, 'rankspammer.gif'),
       (10, 'COM_KUNENA_SAMPLEDATA_RANK_BANNED', 0, 1, 'rankbanned.gif');

INSERT INTO `#__kunena_smileys` (`id`, `code`, `location`, `greylocation`, `emoticonbar`)
VALUES (1, 'B)', 'cool.png', 'cool-grey.png', 1),
       (2, '8)', 'cool.png', 'cool-grey.png', 0),
       (3, '8-)', 'cool.png', 'cool-grey.png', 0),
       (4, ':-(', 'sad.png', 'sad-grey.png', 0),
       (5, ':(', 'sad.png', 'sad-grey.png', 1),
       (6, ':sad:', 'sad.png', 'sad-grey.png', 0),
       (7, ':cry:', 'sad.png', 'sad-grey.png', 0),
       (8, ':)', 'smile.png', 'smile-grey.png', 1),
       (9, ':-)', 'smile.png', 'smile-grey.png', 0),
       (10, ':cheer:', 'cheerful.png', 'cheerful-grey.png', 1),
       (11, ';)', 'wink.png', 'wink-grey.png', 1),
       (12, ';-)', 'wink.png', 'wink-grey.png', 0),
       (13, ':wink:', 'wink.png', 'wink-grey.png', 0),
       (14, ';-)', 'wink.png', 'wink-grey.png', 0),
       (15, ':P', 'tongue.png', 'tongue-grey.png', 1),
       (16, ':p', 'tongue.png', 'tongue-grey.png', 0),
       (17, ':-p', 'tongue.png', 'tongue-grey.png', 0),
       (18, ':-P', 'tongue.png', 'tongue-grey.png', 0),
       (19, ':razz:', 'tongue.png', 'tongue-grey.png', 0),
       (20, ':angry:', 'angry.png', 'angry-grey.png', 1),
       (21, ':mad:', 'angry.png', 'angry-grey.png', 0),
       (22, ':unsure:', 'unsure.png', 'unsure-grey.png', 1),
       (23, ':o', 'shocked.png', 'shocked-grey.png', 0),
       (24, ':-o', 'shocked.png', 'shocked-grey.png', 0),
       (25, ':O', 'shocked.png', 'shocked-grey.png', 0),
       (26, ':-O', 'shocked.png', 'shocked-grey.png', 0),
       (27, ':eek:', 'shocked.png', 'shocked-grey.png', 0),
       (28, ':ohmy:', 'shocked.png', 'shocked-grey.png', 1),
       (29, ':huh:', 'wassat.png', 'wassat-grey.png', 1),
       (30, ':?', 'confused.png', 'confused-grey.png', 0),
       (31, ':-?', 'confused.png', 'confused-grey.png', 0),
       (32, ':???', 'confused.png', 'confused-grey.png', 0),
       (33, ':dry:', 'ermm.png', 'ermm-grey.png', 1),
       (34, ':ermm:', 'ermm.png', 'ermm-grey.png', 0),
       (35, ':lol:', 'grin.png', 'grin-grey.png', 1),
       (36, ':X', 'sick.png', 'sick-grey.png', 0),
       (37, ':x', 'sick.png', 'sick-grey.png', 0),
       (38, ':sick:', 'sick.png', 'sick-grey.png', 1),
       (39, ':silly:', 'silly.png', 'silly-grey.png', 1),
       (40, ':y32b4:', 'silly.png', 'silly-grey.png', 0),
       (41, ':blink:', 'blink.png', 'blink-grey.png', 1),
       (42, ':blush:', 'blush.png', 'blush-grey.png', 1),
       (43, ':oops:', 'blush.png', 'blush-grey.png', 1),
       (44, ':kiss:', 'kissing.png', 'kissing-grey.png', 1),
       (45, ':rolleyes:', 'blink.png', 'blink-grey.png', 0),
       (46, ':roll:', 'blink.png', 'blink-grey.png', 0),
       (47, ':woohoo:', 'w00t.png', 'w00t-grey.png', 1),
       (48, ':side:', 'sideways.png', 'sideways-grey.png', 1),
       (49, ':S', 'dizzy.png', 'dizzy-grey.png', 1),
       (50, ':s', 'dizzy.png', 'dizzy-grey.png', 0),
       (51, ':evil:', 'devil.png', 'devil-grey.png', 1),
       (52, ':twisted:', 'devil.png', 'devil-grey.png', 0),
       (53, ':whistle:', 'whistling.png', 'whistling-grey.png', 1),
       (54, ':pinch:', 'pinch.png', 'pinch-grey.png', 1),
       (55, ':D', 'laughing.png', 'laughing-grey.png', 0),
       (56, ':-D', 'laughing.png', 'laughing-grey.png', 0),
       (57, ':grin:', 'laughing.png', 'laughing-grey.png', 0),
       (58, ':laugh:', 'laughing.png', 'laughing-grey.png', 0),
       (59, ':|', 'neutral.png', 'neutral-grey.png', 0),
       (60, ':-|', 'neutral.png', 'neutral-grey.png', 0),
       (61, ':neutral:', 'neutral.png', 'neutral-grey.png', 0),
       (62, ':mrgreen:', 'mrgreen.png', 'mrgreen-grey.png', 0),
       (63, ':?:', 'question.png', 'question-grey.png', 0),
       (64, ':!:', 'exclamation.png', 'exclamation-grey.png', 0),
       (65, ':arrow:', 'arrow.png', 'arrow-grey.png', 0),
       (66, ':idea:', 'idea.png', 'idea-grey.png', 0);

INSERT INTO `#__kunena_aliases` (`alias`, `type`, `item`, `state`)
VALUES ('announcement', 'view', 'announcement', 1),
       ('category', 'view', 'category', 1),
       ('common', 'view', 'common', 1),
       ('credits', 'view', 'credits', 1),
       ('home', 'view', 'home', 1),
       ('misc', 'view', 'misc', 1),
       ('search', 'view', 'search', 1),
       ('statistics', 'view', 'statistics', 1),
       ('topic', 'view', 'topic', 1),
       ('topics', 'view', 'topics', 1),
       ('user', 'view', 'user', 1),
       ('category/create', 'layout', 'category.create', 1),
       ('create', 'layout', 'category.create', 0),
       ('category/default', 'layout', 'category.default', 1),
       ('default', 'layout', 'category.default', 0),
       ('category/edit', 'layout', 'category.edit', 1),
       ('edit', 'layout', 'category.edit', 0),
       ('category/manage', 'layout', 'category.manage', 1),
       ('manage', 'layout', 'category.manage', 0),
       ('category/moderate', 'layout', 'category.moderate', 1),
       ('moderate', 'layout', 'category.moderate', 0),
       ('category/user', 'layout', 'category.user', 1);
