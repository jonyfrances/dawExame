--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) default NULL,
 `email` varchar(255) NOT NULL,
 `created_at` datetime NOT NULL,
 `updated_at` datetime NOT NULL,
 `password_digest` varchar(255) default NULL,
 `remember_digest` varchar(255) default NULL,
 `admin` tinyint(1) default NULL,
 `activation_digest` varchar(255) default NULL,
 `activated` tinyint(1) default NULL,
 `activated_at` datetime default NULL,
 `reset_digest` varchar(255) default NULL,
 `reset_sent_at` datetime default NULL,
 `roles` longtext COMMENT '(DC2Type:json)',
 PRIMARY KEY (`id`),
 UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `teachers`
--

CREATE TABLE IF NOT EXISTS `teachers` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) default NULL,
 `email` varchar(255) NOT NULL,
 `image` varchar(255) default NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` VALUES (1,'James S. Morrison','James.Morrison@gmail.com','teacher_1.jpg'),(2,'Patty Smith','Patty.Smith@gmail.com','teacher_2.jpg'),(3,'Peter Frampton','Peter.Frampton@gmail.com','teacher_3.jpg'),(4,'Mary Kinget','Mary.Kinget@berkeley.edu','teacher_4.jpg'),(5,'Julia Williams','Julia.Williams@mit.edu','teacher_5.jpg'),(6,'Paulo Nabo da Horta','Paulo.Horta@ualg.pt','teacher_6.jpg');


--
-- Table structure for table `coursecategories`
--

CREATE TABLE IF NOT EXISTS `coursecategories` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(255) default NULL,
 `description` varchar(255) default NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Dumping data for table `coursecategories`
--

INSERT INTO `coursecategories` VALUES (1,'Featured',NULL),(2,'New',NULL),(3,'Best Seller',NULL),(4,'Legacy',NULL),(5,'Classic',NULL);



--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
 `id` int(11) NOT NULL auto_increment,
 `cat_id` int(11) NOT NULL,
 `teacher_id` int(11) NOT NULL,
 `name` varchar(255) default NULL,
 `description` varchar(255) default NULL,
 `price` int(5) default NULL,
 `image` varchar(255) default NULL,
 `sales` int(5) default NULL,
 PRIMARY KEY (`id`),
 CONSTRAINT FOREIGN KEY (`cat_id`) REFERENCES `coursecategories` (`id`),
 CONSTRAINT FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Dumping data for table `courses`
--

INSERT INTO `courses` VALUES (1,1,2,'Literature Course','The best!',40,'course_1.jpg',345),(2,2,5,'Social Media Course','The best Social in the Internet!',35,'course_2.jpg',233),(3,3,4,'Android Course','Best Mobile app development online!',50,'course_3.jpg',124),(4,5,5,'Literature Course','The best present day literature course!',30,'course_4.jpg',456),(5,4,5,'Marketing Course','The latest economy theories!',60,'course_5.jpg',896),(6,1,3,'Javascript Course','The most complete!',55,'course_6.jpg',753),(7,3,5,'Music Course','Let your inspiration be free!',20,'course_7.jpg',345),(8,1,2,'Vue.JS Course','Become an expert in frontend programming!',45,'course_7.jpg',245),(9,2,6,'Advanced Symfony Programming','Simply the best!',60,'course_8.jpg',644);


--
-- Table structure for table `enrolls`
--


CREATE TABLE IF NOT EXISTS `enrolls` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`course_id` int(11) NOT NULL,
`enroll_date` datetime NOT NULL,
 PRIMARY KEY (`id`),
 CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
 CONSTRAINT FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

