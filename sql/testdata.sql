
INSERT INTO user (username, starttime) VALUES ("gerb030", NOW());
INSERT INTO user (username, starttime) VALUES ("VoteUser 1", NOW());

INSERT INTO pokerround (`session`, ownerusername, starttime, closed) VALUES ("12345", "VoteUser 1", NOW(), false);

INSERT INTO pokerround_user (pokerround_id, user_id, voted) VALUES (1, 1, NULL);
INSERT INTO pokerround_user (pokerround_id, user_id, voted) VALUES (1, 2, NULL);