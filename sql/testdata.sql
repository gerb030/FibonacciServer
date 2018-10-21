
INSERT INTO user (username, emailaddress, created) VALUES ("gerb030", "mac.gerb@gmail.com", NOW());
INSERT INTO user (username, emailaddress, created) VALUES ("VoteUser 1", "test@test.com", NOW());

INSERT INTO pokerround (`session`, ownerusername, starttime, lastupdated, closed) VALUES ("12345", "VoteUser 1", NOW(), NOW(), false);

INSERT INTO pokerround_user (pokerround_id, user_id, voted) VALUES (1, 1, NULL);
INSERT INTO pokerround_user (pokerround_id, user_id, voted) VALUES (1, 2, NULL);