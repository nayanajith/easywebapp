delimiter ||
/*Function for year1 processing*/

DROP FUNCTION IF EXISTS process_y1||
CREATE FUNCTION process_y1(index_no_ VARCHAR(8), year_ TINYINT(1)) RETURNS TEXT MODIFIES SQL DATA
BEGIN
   -- Course revision 1,2,3,4..
   DECLARE revision_    INT(1);

   -- Parameter for the compulsory courses
   -- the list of comma seperated courses which student should sit
   DECLARE to_sit_comp_       TEXT;
   -- Number of all compulsory courses student have still to sit
   DECLARE to_sit_comp_count_ INT(2);
   -- Number of all compulsory courses for this year
   DECLARE comp_all_count_    INT(2);

   -- Parameter for the lms NOTE: lms uses an exceptiona gradeing mechenism ie. NC/CM
   -- the list of comma seperated courses which student could sit
   DECLARE to_sit_lms_        TEXT;
   -- The minimum number of course which the student should sit
   DECLARE to_sit_lms_min_    INT(2);
   -- The number of courses which the student have to sit
   DECLARE to_sit_lms_count_  INT(2);
   -- The number of courses which the student have sit
   DECLARE lms_count_         INT(2);

   -- Effective date for  deploma/degree
   DECLARE effective_date     DATE;
   -- Minumum GPA to pass year-2
   DECLARE min_gpa            FLOAT; 
   -- Students current GPA
   DECLARE gpa                FLOAT; 
   -- to hold wether student passwd the year or not
   DECLARE pass               BOOLEAN; 
   -- to hold wether student eligible for HDIT
   DECLARE DIG_DIP            VARCHAR(10); 


   -- passing the optional lms courses is not required to get the HDIT
   SET to_sit_lms_min_  = 1;

   -- minimum gpa to pass year2
   SET min_gpa          = 1.5; 

   -- get the latest revision number used currently for the courses
   SELECT MAX(revision) INTO revision_ FROM bit_course WHERE student_year=year_;


   -- The list of compulsory courses not sit and the count of the student
   SELECT GROUP_CONCAT(course_id),COUNT(*) INTO to_sit_comp_,to_sit_comp_count_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( SELECT LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50);

   -- count of all compulsory courses for the year
   SELECT count(*) INTO comp_all_count_ FROM bit_course WHERE student_year=year_ AND revision=revision_  AND compulsory;

   -- year-3 total gpa of the student 
   SELECT class_gpa INTO gpa FROM bit_gpa WHERE index_no=index_no_ AND year='1';

   -- count the number of lms asessments passed 'CM'
   SELECT count(*) INTO lms_count_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND final_mark = 'CM' ;

   -- the last date of the last exam which sit by the student to pass the DEPLOMA/HDEPLOMA/DEGREE
   SELECT MAX(LEFT(exam_hid,10)) INTO effective_date FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1;

   -- Checking the pass criteria of year-2
   IF gpa >= min_gpa THEN
      SET pass=TRUE;
   ELSE
      SET pass=false;
   END IF;

   -- If the student satisfy all the conditions make an entry for HDIT
   IF lms_count_ >= to_sit_lms_min_ AND  to_sit_comp_count_ = 0 THEN
      IF index_no_ LIKE '01%' OR  index_no_ LIKE '02%' OR index_no_ LIKE '03%' OR index_no_ LIKE '04%' OR index_no_ LIKE '05%' OR index_no_ LIKE '06%' THEN
         SET DIG_DIP ='CIT'; 
      ELSE
         SET DIG_DIP ='DIT'; 
      END IF;
      REPLACE INTO bit_student_state(`index_no`,`year1_state`,`year1_pass`,`year1_date`)values(index_no_,DIG_DIP,pass,effective_date);
   ELSE
      -- Prepare the comment for the compulsory courses
      IF to_sit_comp_count_ = 0 THEN
         SET to_sit_comp_  = '';
      ELSE
         IF to_sit_comp_count_ = comp_all_count_ THEN
            SET to_sit_comp_  = 'TO SIT: ALL year 1';
         ELSE
            SET to_sit_comp_  = CONCAT('TO SIT: ',to_sit_comp_);
         END IF;
      END IF;

      -- Prepare the comment for the lms courses
      IF lms_count_ >= to_sit_lms_min_ THEN
         SET to_sit_lms_ = '';
      ELSE
         SET to_sit_lms_ = 'TO SIT: LMS Assesments';
      END IF;


       -- update the database with the information found
      REPLACE INTO bit_student_state(`index_no`,`year1_state`,year1_pass)values(index_no_,CONCAT_WS(',',to_sit_comp_,to_sit_lms_),pass);
   END IF;
   
   -- return some information to the query 
   RETURN CONCAT_WS(', ',to_sit_comp_,to_sit_lms_,pass,'DIT:',DIG_DIP,lms_count_,to_sit_comp_count_);

END;             
||

/*Function for year 2 processing*/

DROP FUNCTION IF EXISTS process_y2||
CREATE FUNCTION process_y2(index_no_ VARCHAR(8), year_ TINYINT(1)) RETURNS TEXT MODIFIES SQL DATA
BEGIN
   -- Course revision 1,2,3,4..
   DECLARE revision_    INT(1);

   -- Parameter for the compulsory courses
   -- the list of comma seperated courses which student should sit
   DECLARE to_sit_comp_       TEXT;
   -- Number of all compulsory courses student have still to sit
   DECLARE to_sit_comp_count_ INT(2);
   -- Number of all compulsory courses for this year
   DECLARE comp_all_count_    INT(2);

   -- Parameter for the optional courses
   -- the list of comma seperated courses which student could sit
   DECLARE to_sit_opt_        TEXT;
   -- The minimum number of course which the student should sit
   DECLARE to_sit_opt_min_    INT(2);
   -- The number of courses which the student have sit
   DECLARE opt_count_         INT(2);

   -- Parameter for the lms NOTE: lms uses an exceptiona gradeing mechenism ie. NC/CM
   -- the list of comma seperated courses which student could sit
   DECLARE to_sit_lms_        TEXT;
   -- The minimum number of course which the student should sit
   DECLARE to_sit_lms_min_    INT(2);
   -- The number of courses which the student have to sit
   DECLARE to_sit_lms_count_  INT(2);
   -- The number of courses which the student have sit
   DECLARE lms_count_         INT(2);

   -- Effective date for deploma or degree
   DECLARE effective_date     DATE;
   -- Minumum GPA to pass year-2
   DECLARE min_gpa            FLOAT; 
   -- Students current GPA
   DECLARE gpa                FLOAT; 
   -- to hold wether student passwd the year or not
   DECLARE pass               BOOLEAN; 
   -- to hold wether student eligible for HDIT
   DECLARE DIG_DIP            VARCHAR(10); 


   -- 4 optional course should be passed ('C') to get the HDIT 
   SET to_sit_opt_min_  = 4;

   -- passing the optional lms courses is not required to get the HDIT
   SET to_sit_lms_min_  = 0;

   -- minimum gpa to pass year2
   SET min_gpa          = 1.5; 

   -- get the latest revision number used currently for the courses
   SELECT MAX(revision) INTO revision_ FROM bit_course WHERE student_year=year_;


   -- The list of compulsory courses not sit and the count of the student
   SELECT GROUP_CONCAT(course_id),COUNT(*) INTO to_sit_comp_,to_sit_comp_count_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( SELECT LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50);

   -- The list of optional course not sit and the count of the student
   SELECT GROUP_CONCAT(course_id) INTO to_sit_opt_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND NOT compulsory AND LEFT(alt_course_id,4) NOT IN( SELECT LEFT(alt_course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50);

   -- count of optional courses sit by the student
   SELECT count(*) INTO opt_count_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50;

   -- count of all compulsory courses for the year
   SELECT count(*) INTO comp_all_count_ FROM bit_course WHERE student_year=year_ AND revision=revision_  AND compulsory;

   -- year-3 total gpa of the student 
   SELECT class_gpa INTO gpa FROM bit_gpa WHERE index_no=index_no_ AND year='2T';

   IF year_ = 2 THEN
      -- for year 1 lms asessment should be passed
      SET lms_count_=1;
   ELSE
      -- count the number of lms asessments passed 'CM'
      SELECT count(*) INTO lms_count_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND final_mark = 'CM' ;
   END IF;

   -- the last date of the last exam which sit by the student to pass the DEPLOMA/HDEPLOMA/DEGREE
   SELECT MAX(LEFT(exam_hid,10)) INTO effective_date FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1;

   -- Checking the pass criteria of year-2
   IF gpa >= min_gpa THEN
      SET pass=TRUE;
   ELSE
      SET pass=false;
   END IF;

   -- If the student satisfy all the conditions make an entry for HDIT
   IF lms_count_ >= to_sit_lms_min_ AND  opt_count_ >= to_sit_opt_min_ AND to_sit_comp_count_ = 0 THEN
      IF index_no_ LIKE '01%' OR  index_no_ LIKE '02%' OR index_no_ LIKE '03%' OR index_no_ LIKE '04%' OR index_no_ LIKE '05%' OR index_no_ LIKE '06%' OR index_no_ LIKE '07%' THEN
         SET DIG_DIP ='ACIT'; 
      ELSE
         SET DIG_DIP ='HDIT'; 
      END IF;
      REPLACE INTO bit_student_state(`index_no`,`year2_state`,`year2_pass`,`year2_date`)values(index_no_,DIG_DIP,pass,effective_date);
   ELSE
      -- Prepare the comment for the compulsory courses
      IF to_sit_comp_count_ = 0 THEN
         SET to_sit_comp_  = '';
      ELSE
         IF to_sit_comp_count_ = comp_all_count_ THEN
            SET to_sit_comp_  = 'TO SIT: ALL Compulsory year 2';
         ELSE
            SET to_sit_comp_  = CONCAT('TO SIT: ',to_sit_comp_);
         END IF;
      END IF;

      -- Prepare the comment for the optional courses
      IF opt_count_  >= to_sit_opt_min_ THEN
         SET to_sit_opt_  = '';
      ELSE
         SET to_sit_opt_  = CONCAT('TO SIT:',to_sit_opt_min_-opt_count_,' FROM (',to_sit_opt_,')');
      END IF;

      -- Prepare the comment for the lms courses
      IF lms_count_ >= to_sit_lms_min_ THEN
         SET to_sit_lms_ = '';
      ELSE
         SET to_sit_lms_ = 'TO SIT: LMS Assesments';
      END IF;

       -- update the database with the information found
      REPLACE INTO bit_student_state(`index_no`,`year2_state`,year2_pass)values(index_no_,CONCAT_WS(',',to_sit_comp_,to_sit_opt_,to_sit_lms_),pass);
   END IF;
   
   -- return some information to the query 
   RETURN CONCAT_WS(', ',to_sit_comp_,to_sit_opt_,to_sit_lms_,pass,'HDIT:',DIG_DIP,lms_count_,opt_count_,to_sit_comp_count_);

END;             
||


/*Function for year 2 processing*/

DROP FUNCTION IF EXISTS process_y3||
CREATE FUNCTION process_y3(index_no_ VARCHAR(8), year_ TINYINT(1)) RETURNS TEXT MODIFIES SQL DATA
BEGIN
   -- Course revision 1,2,3,4..
   DECLARE revision_    INT(1);

   -- Parameter for the compulsory courses
   -- the list of comma seperated courses which student should sit
   DECLARE to_sit_comp_       TEXT;
   -- Number of all compulsory courses student have still to sit
   DECLARE to_sit_comp_count_ INT(2);
   -- Number of all compulsory courses for this year
   DECLARE comp_all_count_    INT(2);

   -- Parameter for the optional courses
   -- the list of comma seperated courses which student could sit
   DECLARE to_sit_opt_        TEXT;
   -- The minimum number of course which the student should sit
   DECLARE to_sit_opt_min_    INT(2);
   -- The number of courses which the student have sit
   DECLARE opt_count_         INT(2);

   -- Minimum mark which student should obtain to pass  a course
   DECLARE pass_mark          INT(3);

   -- Effective date for HDIT
   DECLARE effective_date     DATE;
   -- Minumum GPA to pass year-2
   DECLARE min_gpa            FLOAT; 
   -- Students current GPA
   DECLARE gpa                FLOAT; 
   -- to hold wether student passwd the year or not
   DECLARE pass               BOOLEAN; 
   -- to hold the indivdiual project code
   DECLARE ind_project        VARCHAR(10); 
   -- to hold wether student have completed the individual project 
   DECLARE ind_project_done   BOOLEAN; 
   -- Minimum mark which student should obtain to pass the project 
   DECLARE ind_project_pass_mark          INT(3);


   -- 4 optional course should be passed ('C') to get the BIT
   SET to_sit_opt_min_  = 3;
   SET min_gpa          = 2.0; 
   SET ind_project      = 'IT610%'; 
   SET ind_project_pass_mark  =50;
   SET pass_mark        = 40;


   -- get the latest revision number used currently for the courses
   SELECT MAX(revision) INTO revision_ FROM bit_course WHERE student_year=year_;


   -- The list of compulsory courses not sit and the count of the student
   SELECT GROUP_CONCAT(course_id),COUNT(*) INTO to_sit_comp_,to_sit_comp_count_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( SELECT LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= pass_mark);

   -- The list of optional course not sit and the count of the student
   SELECT GROUP_CONCAT(course_id) INTO to_sit_opt_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND NOT compulsory AND LEFT(alt_course_id,4) NOT IN( SELECT LEFT(alt_course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= pass_mark);

   -- count of optional courses sit by the student
   SELECT count(*) INTO opt_count_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= pass_mark;

   -- check whether the student have completed the individual project
   SELECT count(*) INTO ind_project_done FROM bit_marks WHERE index_no=index_no_ AND course_id LIKE ind_project AND repeat_max >= 1 AND grand_final_mark >= ind_project_pass_mark;

   -- count of all compulsory courses for the year
   SELECT count(*) INTO comp_all_count_ FROM bit_course WHERE student_year=year_ AND revision=revision_  AND compulsory;

     -- year-3 total gpa of the student 
   SELECT class_gpa INTO gpa FROM bit_gpa WHERE index_no=index_no_ AND year='3T';

     -- the last date of the last exam which sit by the student to pass the DEPLOMA/HDEPLOMA/DEGREE
   SELECT MAX(LEFT(exam_hid,10)) INTO effective_date FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1;

   -- Checking the pass criteria of year-2
   IF gpa >= min_gpa THEN
      SET pass=TRUE;
   ELSE
      SET pass=false;
   END IF;

   -- If the student satisfy all the conditions make an entry for HDIT
   IF opt_count_ >= to_sit_opt_min_ AND to_sit_comp_count_ = 0 THEN
      REPLACE INTO bit_student_state(`index_no`,`year3_state`,`year3_pass`,`year3_date`)values(index_no_,'BIT',pass,effective_date);
   ELSE
      -- Prepare the comment for the compulsory courses
      IF to_sit_comp_count_ = 0 THEN
         SET to_sit_comp_  = '';
      ELSE
         IF to_sit_comp_count_ = comp_all_count_ THEN
            SET to_sit_comp_  = 'TO SIT: ALL Compulsory';
         ELSE
            SET to_sit_comp_  = CONCAT('TO SIT: ',to_sit_comp_);
         END IF;
      END IF;

      -- Prepare the comment for the optional courses
      IF opt_count_  >= to_sit_opt_min_ THEN
         SET to_sit_opt_  = '';
      ELSE
         SET to_sit_opt_  = CONCAT('TO SIT:',to_sit_opt_min_-opt_count_,' FROM (',to_sit_opt_,')');
      END IF;

       -- update the database with the information found
      REPLACE INTO bit_student_state(`index_no`,`year2_state`,year2_pass)values(index_no_,CONCAT_WS(',',to_sit_comp_,to_sit_opt_),pass);
   END IF;
   
   -- return some information to the query 
   RETURN CONCAT_WS(', ',to_sit_comp_,to_sit_opt_,pass,'HDIT:',pass,to_sit_comp_count_);

END;             
||

delimiter ;



/*
SELECT m.index_no,process_y2(m.index_no,2) tosit
FROM  bit_marks AS m
INNER JOIN bit_course AS c
USING(course_id)
WHERE c.student_year=2
GROUP BY index_no
*/

SELECT m.index_no,process_y3(m.index_no,3) tosit
FROM  bit_marks AS m
INNER JOIN bit_course AS c
USING(course_id)
WHERE c.student_year=3
AND index_no LIKE '06%'
GROUP BY index_no



/*
REPLACE INTO bit_student_state()(m.index_no,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50) comp,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50) opt,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND final_mark = 'CM') lms,
tosit_y2(m.index_no,2) tosit

FROM  bit_marks AS m
INNER JOIN bit_course AS c
USING(course_id)
WHERE 
c.student_year=2
HAVING(comp >= 4  AND opt >=4 AND lms >=1)
*/
