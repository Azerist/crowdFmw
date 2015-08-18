
#Create all the tables with their attributes...

CREATE TABLE task (
	id int PRIMARY KEY AUTO_INCREMENT,
	name varchar(64) NOT NULL,
	description varchar(512),
	status varchar(16),
	extParams varchar(128),
	target int,
	current int DEFAULT 0
);

CREATE TABLE worker (
	id int PRIMARY KEY AUTO_INCREMENT,
	username varchar(16) NOT NULL UNIQUE,
	password varchar(256) NOT NULL
);

CREATE TABLE requester (
	id int PRIMARY KEY AUTO_INCREMENT,
	username varchar(16) NOT NULL UNIQUE,
	password varchar(256) NOT NULL
);

CREATE TABLE admin (
	id int PRIMARY KEY AUTO_INCREMENT,
	username varchar(16) NOT NULL UNIQUE,
	password varchar(256) NOT NULL
);

CREATE TABLE question (
	id int PRIMARY KEY AUTO_INCREMENT,
	question varchar(256) NOT NULL,
	input varchar(256),
	inputType varchar(16)
);

CREATE TABLE answer (
	id int PRIMARY KEY AUTO_INCREMENT,
	answer varchar(128)
);

CREATE TABLE contribution (
	id int PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE assignment (
	id int PRIMARY KEY AUTO_INCREMENT
);

#Create the default admin account
INSERT INTO admin(username,password) VALUES ('admin','$2y$10$JpeiPcsCNU.FbZ75WpuOmeAVOvtv/D2pr031jJXXCaibQ1t1AY8Ay');

#Create the foreign keys linking the tables
ALTER TABLE task
ADD(
	id_requester int NOT NULL,
	FOREIGN KEY (id_requester) REFERENCES requester(id)
);

ALTER TABLE question
ADD(
	id_task int NOT NULL,
	FOREIGN KEY (id_task) REFERENCES task(id) ON DELETE CASCADE
);

ALTER TABLE answer
ADD(
	id_question int NOT NULL,
	FOREIGN KEY (id_question) REFERENCES question(id) ON DELETE CASCADE
);

ALTER TABLE contribution
ADD(
	id_question int NOT NULL,
	id_worker int NOT NULL,
	id_answer int NOT NULL,
	FOREIGN KEY (id_question) REFERENCES question(id) ON DELETE CASCADE,
	FOREIGN KEY (id_worker) REFERENCES worker(id),
	FOREIGN KEY (id_answer) REFERENCES answer(id)
);

ALTER TABLE assignment
ADD(
	id_task int NOT NULL,
	id_worker int NOT NULL,
	FOREIGN KEY (id_task) REFERENCES task(id) ON DELETE CASCADE,
	FOREIGN KEY (id_worker) REFERENCES worker(id)
);

