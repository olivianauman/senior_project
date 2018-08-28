-- Tutor CS db info

DROP TABLE IF EXISTS scheduled_sessions, sessions, course_docs, tutors, professors, students, courses, users;


-- Each user has access to other roles, we track that we boolean fields here
CREATE TABLE users (
	hawk_id VARCHAR(50) NOT NULL,
	first_name VARCHAR(120) NOT NULL,
	last_name VARCHAR(120) NOT NULL,
	hashedpass VARCHAR(120) NOT NULL,
	student BOOLEAN NOT NULL,
	tutor BOOLEAN NOT NULL,
	administrator BOOLEAN NOT NULL,
	professor BOOLEAN NOT NULL,
	PRIMARY KEY (hawk_id)
);

CREATE TABLE courses (
	course_id VARCHAR(15) NOT NULL,
	name VARCHAR(120) NOT NULL,
	PRIMARY KEY (course_id)
);

CREATE TABLE students (
	hawk_id VARCHAR(50) NOT NULL,
	FOREIGN KEY (hawk_id) REFERENCES users (hawk_id) ON DELETE CASCADE,
	-- Supporting international numbers up to 15 digits
	phone_number VARCHAR(15),
	course_id VARCHAR(15) NOT NULL,
	session_credits INT NOT NULL DEFAULT 20,
	FOREIGN KEY (course_id) REFERENCES courses (course_id),
	PRIMARY KEY (hawk_id)
);

-- Assuming a professor only teaches one course
CREATE TABLE professors (
	hawk_id VARCHAR(50) NOT NULL,
	FOREIGN KEY (hawk_id) REFERENCES users (hawk_id) ON DELETE CASCADE,
	course_id VARCHAR(15) NOT NULL,
	FOREIGN KEY (course_id) REFERENCES courses (course_id),
	PRIMARY KEY (hawk_id)
);

CREATE TABLE tutors (
	hawk_id VARCHAR(50) NOT NULL,
	FOREIGN KEY (hawk_id) REFERENCES users (hawk_id) ON DELETE CASCADE,
	-- Supporting international numbers up to 15 digits
	phone_number VARCHAR(15),
	course_id VARCHAR(15) NOT NULL,
	FOREIGN KEY (course_id) REFERENCES courses (course_id),
	PRIMARY KEY (hawk_id)
);

CREATE TABLE course_docs (
	id INT NOT NULL AUTO_INCREMENT,
	course_id VARCHAR(15) NOT NULL,
	doc_name VARCHAR(30) NOT NULL,
	doc_data VARCHAR(500) NOT NULL,
	FOREIGN KEY (course_id) REFERENCES courses (course_id),
	PRIMARY KEY (id)
);

CREATE TABLE sessions (
	id INT NOT NULL AUTO_INCREMENT,
	slot DATETIME NOT NULL,
	course_id VARCHAR(15) NOT NULL,
	FOREIGN KEY (course_id) REFERENCES courses (course_id),
	tutor_id VARCHAR(50) NOT NULL,
	available BOOLEAN DEFAULT TRUE,
	FOREIGN KEY (tutor_id) REFERENCES tutors (hawk_id),
	PRIMARY KEY (id)
);

CREATE TABLE scheduled_sessions (
	id INT NOT NULL AUTO_INCREMENT,
	session_id INT NOT NULL,
	FOREIGN KEY (session_id) REFERENCES sessions (id),
	student_id VARCHAR(50) NOT NULL,
	FOREIGN KEY (student_id) REFERENCES students (hawk_id),
	doc_id INT,
	FOREIGN KEY (doc_id) REFERENCES course_docs (id),
	PRIMARY KEY (id)
);

-- Test data for users table --
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("student", "Jon", "Jameson", TRUE, FALSE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("student2", "Jane", "Doe", TRUE, FALSE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("student3", "Adam", "Smith", TRUE, FALSE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("student4", "Greg", "Meyer", TRUE, FALSE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("student5", "Josh", "Nguyen", TRUE, FALSE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("tutor", "Johanna", "Mendez", FALSE, TRUE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("tutor2", "Ed", "Sheeran", FALSE, TRUE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("tutor3", "Miranda", "Edwards", FALSE, TRUE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("tutor4", "Jenny", "Brown", FALSE, TRUE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("admin", "Kelly", "Fields", FALSE, FALSE, TRUE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("admin2", "Jimmy", "Neutron", FALSE, FALSE, TRUE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("professor", "Nick", "Jerin", FALSE, FALSE, FALSE, TRUE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("professor2", "Ned", "Catsonfire", FALSE, FALSE, FALSE, TRUE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("professor3", "James", "Brady", FALSE, FALSE, FALSE, TRUE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("professor4", "Allison", "Jones", FALSE, FALSE, FALSE, TRUE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");
INSERT INTO users(hawk_id, first_name, last_name, student, tutor, administrator, professor, hashedpass)
	VALUES ("studor", "Yolanda", "Platte", TRUE, TRUE, FALSE, FALSE, "$2a$12$cbVDVCLee5L76fr2yT.juOn6V2P816/kkGDALnbFwYE79BgAKmBRW");

-- Test data for courses table --
INSERT INTO courses(course_id, name)
	VALUES ("CS:1110", "Introduction to Computer Science");
INSERT INTO courses(course_id, name)
	VALUES ("CS:1210","Computer Science I: Fundamentals");
INSERT INTO courses(course_id, name)
	VALUES ("CS:1020", "Principles of Computing");
INSERT INTO courses(course_id, name)
	VALUES ("UWSD:5500", "Underwater Scuba Diving");

-- Test data for students table --
INSERT INTO students(hawk_id, phone_number, course_id, session_credits)
	VALUES ("student", "319-867-5309","CS:1210", 10);
INSERT INTO students(hawk_id, phone_number, course_id, session_credits)
	VALUES ("student2", "786-829-5378","CS:1110", 20);
INSERT INTO students(hawk_id, phone_number, course_id, session_credits)
	VALUES ("student3", "786-821-5578","CS:1110", 15);
INSERT INTO students(hawk_id, phone_number, course_id, session_credits)
	VALUES ("student4", "986-929-6778","CS:1020", 10);
INSERT INTO students(hawk_id, phone_number, course_id, session_credits)
	VALUES ("student5", "324-758-5375","UWSD:5500", 20);

--Test data for Professors table --
INSERT INTO professors(hawk_id, course_id)
	VALUES ("professor", "CS:1210");
INSERT INTO professors(hawk_id, course_id)
	VALUES ("professor2", "CS:1110");
INSERT INTO professors(hawk_id, course_id)
	VALUES ("professor3", "UWSD:5500");
INSERT INTO professors(hawk_id, course_id)
	VALUES ("professor4", "CS:1020");

--Test data for Tutors table --
INSERT INTO tutors(hawk_id, phone_number, course_id)
	VALUES ("tutor", "319-555-5555","CS:1210");
INSERT INTO tutors(hawk_id, phone_number, course_id)
	VALUES ("tutor2", "324-875-9876","CS:1110");
INSERT INTO tutors(hawk_id, phone_number, course_id)
	VALUES ("tutor3", "364-575-6876","CS:1020");
INSERT INTO tutors(hawk_id, phone_number, course_id)
	VALUES ("tutor4", "326-875-9476","UWSD:5500");

--Test data for course docs table --
INSERT INTO course_docs(course_id, doc_name, doc_data)
	VALUES ("CS:1210", "Test One Study Guide", "Lorem ipsum dolor sit amet, eam aliquip fabulas ut, vis laoreet reprimique at. Modus errem est et, decore vocent cu per. Assum tempor has ut. No cum clita decore. Mea te accommodare ullamcorper, ei nam laoreet tacimates efficiantur.u explicari hendrerit pro, eam omnes minimum theophrastus ei, ex eos cetero.");
INSERT INTO course_docs(course_id, doc_name, doc_data)
	VALUES ("CS:1110", "Practice Problems Week 2", "Esse eleifend cum no. Essent epicurei an mei, te quo solum fugit augue, dolor integre an quo. Pro an vide oporteat, tempor officiis assueverit per in, legere tibique vim an. Nec putent recteque ad. Sit enim dolor graeco ne, duo ludus maiestatis ad, etiam postea expetenda mel id. Dicit aperiam.");
INSERT INTO course_docs(course_id, doc_name, doc_data)
	VALUES ("CS:1020", "Practice Problems Week 5", "Lorem ipsum dolor sit amet, vim laoreet incorrupte et, ne usu persecuti deseruisse. Per possit perpetua no. Ridens possit comprehensam no mea, no eam nonumy appetere probatus. His efficiendi omittantur an, ius no prima reque. Eligendi indoctum constituam eu has. Ei alia quaestio nec.

His an vidit labore petentium graeco.");
INSERT INTO course_docs(course_id, doc_name, doc_data)
	VALUES ("UWSD:5500", "Coding Underwater for Dummies", "Lorem ipsum dolor sit amet, ea mel esse probo. Errem deserunt tacimates duo ex. Ferri dolorem nam ut, ex habeo inciderint voluptatibus qui. Vel amet saperet in, utinam melius facilisis mei ne.

Mea solet luptatum persequeris te, te phaedrum oportere dissentiunt quo. An cum rationibus accommodare, ei modo mundi feugiat.");

--Test data for available sessions table --
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-04 10:00:00", "CS:1210","tutor", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-05 10:00:00", "CS:1210","tutor", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-06 10:00:00", "CS:1210","tutor", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-07 10:00:00", "CS:1210","tutor", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-08 10:00:00", "CS:1210","tutor", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-05 10:00:00", "CS:1110","tutor2", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-05 12:00:00", "CS:1110","tutor2", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-06-05 10:00:00", "CS:1110","tutor2", FALSE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-15 10:00:00", "CS:1110","tutor2", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-07 10:00:00", "CS:1110","tutor2", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-10 01:00:00", "CS:1110","tutor2", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-11 02:00:00", "CS:1110","tutor2", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-09 10:00:00", "CS:1110","tutor2", TRUE);
INSERT INTO sessions(slot, course_id, tutor_id, available)
	VALUES ("2018-05-09 08:00:00", "CS:1110","tutor2", TRUE);

-- Test data for scheduled sessions table --
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(1, "student", 1);
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(2, "student", 1);
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(3, "student", 2);
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(7, "student2", 2);
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(8, "student2", 3);
INSERT INTO scheduled_sessions(session_id, student_id, doc_id)
	VALUES(9, "student2", 3);
