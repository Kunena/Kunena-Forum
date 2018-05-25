-- Insert default ranks

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
