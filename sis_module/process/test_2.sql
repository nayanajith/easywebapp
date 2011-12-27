USE sis;
/*
SELECT index_no,count(*)
FROM bit_marks 
INNER JOIN bit_course 
USING(course_id) 
WHERE repeat_max >= 1 AND semester IN(1,2) AND grand_final_mark >= 50
GROUP BY index_no
HAVING count(*) >= 8
*/

delimiter ||
DROP FUNCTION IF EXISTS tosit||
CREATE FUNCTION tosit(index_no_ VARCHAR(255), year_ TINYINT(1)) RETURNS TEXT READS SQL DATA
BEGIN
   DECLARE course_id_         VARCHAR(300);
   DECLARE cur_revision_      VARCHAR(2);
   DECLARE stu_revision_      VARCHAR(2);

   DECLARE stu_comp_course_   INT(2);
   DECLARE stu_opt_course_    INT(2);

   DECLARE stu_comp_course_ts_   TEXT;
   DECLARE stu_opt_course_ts_    TEXT;


   DECLARE MIN_COURSE_COMP_1  INT(2); 

   DECLARE MIN_COURSE_COMP_2  INT(2); 
   DECLARE MIN_COURSE_OPT_2   INT(2); 

   DECLARE MIN_COURSE_COMP_3  INT(2); 
   DECLARE MIN_COURSE_OPT_3   INT(2); 

   DECLARE MIN_COURSE_COMP_4  INT(2); 
   DECLARE MIN_COURSE_OPT_4   INT(2); 

   SET MIN_COURSE_COMP_1      = 8; 

   SET MIN_COURSE_COMP_2      = 4;
   SET MIN_COURSE_OPT_2       = 4; 

   SET MIN_COURSE_COMP_3      = 2;
   SET MIN_COURSE_OPT_3       = 3;
                 
   SET MIN_COURSE_COMP_4      = 4;
   SET MIN_COURSE_OPT_4       = 4;



   
/*
   DECLARE year_comp_course_csr    CURSOR FOR SELECT course_id,semester FROM bit_course WHERE student_year=year_ AND compulsory AND revision=(SELECT MAX(revision) FROM bit_course WHERE student_year=year_ AND compulsory);
   DECLARE year_opt_course_csr    CURSOR FOR SELECT course_id,semester FROM bit_course WHERE student_year=year_ AND NOT compulsory AND revision=(SELECT MAX(revision) FROM bit_course WHERE student_year=year_ AND compulsory);


   DECLARE stu_comp_course_csr     CURSOR FOR SELECT course_id,base_course_id,final_mark,grand_final_mark,semester,compulsory,student_year FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50;

   DECLARE stu_opt_course_csr     CURSOR FOR SELECT course_id,base_course_id,final_mark,grand_final_mark,semester,compulsory,student_year FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50;
*/

   SELECT count(*) INTO stu_comp_course_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50;
   SELECT count(*) INTO stu_opt_course_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50;

   SELECT GROUP_CONCAT(course_id) INTO stu_comp_course_ts_ FROM bit_course WHERE revision=(SELECT max(revision) from bit_course where student_year=year_) AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( select LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50);

   SELECT GROUP_CONCAT(course_id) INTO stu_opt_course_ts_ FROM bit_course WHERE revision=(SELECT max(revision) from bit_course where student_year=year_) AND student_year=year_ AND NOT compulsory AND LEFT(alt_course_id,4) NOT IN( select LEFT(alt_course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50);


            RETURN CONCAT(stu_comp_course_,',',stu_comp_course_ts_);
   /*
   SET course_id_             ='';

   SELECT MAX(RIGHT(course_id,2))   INTO stu_revision_ FROM bit_marks_course_view WHERE index_no=index_no_ AND student_year=year_; 
   SELECT MAX(revision)             INTO cur_revision_ FROM bit_course WHERE student_year=year_;
*/

/*
   CASE year_
      WHEN 1 THEN
         /*
         OPEN     stu_comp_course_csr;
         SELECT FOUND_ROWS() INTO stu_comp_course_;
         LOOP
            FETCH    stu_course_csr INTO course_id_;
         END LOOP;
         -- FETCH    stu_course_csr INTO course_id_;
         CLOSE    stu_comp_course_csr;

         OPEN     stu_opt_course_csr;
         SELECT FOUND_ROWS() INTO stu_opt_course_;
         -- FETCH    stu_course_csr INTO course_id_;
         CLOSE    stu_opt_course_csr;

         */
/*
         IF MIN_COURSE_COMP_1 > stu_comp_course_
         THEN
            RETURN CONCAT(stu_comp_course_,',',stu_comp_course_ts_);
         ELSE
            RETURN 'DIT';
         END IF;
      WHEN 2 THEN
         IF MIN_COURSE_COMP_2 >  stu_comp_course_
         THEN
            SELECT CONCAT(stu_comp_course_,',',stu_comp_course_ts_) INTO course_id_;
         END IF;

         IF MIN_COURSE_OPT_2 >  stu_opt_course_
         THEN
            SELECT CONCAT(course_id_,'-',stu_opt_course_,',',stu_opt_course_ts_) INTO course_id_;
         END IF;

         RETURN course_id_;
   END CASE;



   

/*
DECLARE offset TINYINT(3) UNSIGNED;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET s = NULL;

SET count = 0;
SET offset = 1;

REPEAT
IF NOT ISNULL(s) AND offset > 0 THEN
SET offset = LOCATE(ss, s, offset);
IF offset > 0 THEN
SET count = count + 1;
SET offset = offset + 1;
END IF;
END IF;
UNTIL ISNULL(s) OR offset = 0 END REPEAT;
*/

-- RETURN revision;
END;

||

delimiter ;



-- SELECT mc.index_no,IF(COUNT(*)>=8,'DIT',CONCAT('TO SIT:',(SELECT IF((LENGTH(GROUP_CONCAT(course_id)) - LENGTH(REPLACE(GROUP_CONCAT(course_id), ',', ''))) >= 8,'ALL',GROUP_CONCAT(course_id)) from bit_marks_course_view where index_no=mc.index_no and student_year=1 and repeat_max >=1 and grand_final_mark < 50)))
/*
SELECT index_no,IF(COUNT(*)>=8,'DIT',tosit(index_no,1))
FROM bit_marks 
INNER JOIN bit_course
USING(course_id)
WHERE repeat_max >= 1 AND student_year=1 AND grand_final_mark >= 50
GROUP BY index_no
*/

delimiter ||
DROP FUNCTION IF EXISTS tosit_y2||
CREATE FUNCTION tosit_y2(index_no_ VARCHAR(8), year_ TINYINT(1)) RETURNS TEXT READS SQL DATA
BEGIN
   DECLARE revision_    INT(1);
   DECLARE to_sit_comp_ TEXT;
   DECLARE to_sit_opt_  TEXT;

   SELECT MAX(revision) INTO revision_ FROM bit_course WHERE student_year=year_;

   SELECT GROUP_CONCAT(course_id) INTO to_sit_comp_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( select LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50;

   SELECT GROUP_CONCAT(course_id) INTO to_sit_opt_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND NOT compulsory AND LEFT(alt_course_id,4) NOT IN( select LEFT(alt_course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50;

   RETURN CONCAT(to_sit_comp_,to_sit_opt_);
END;             
||
delimiter ;



SELECT m.index_no,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50) comp,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50) opt,
tosit_y2(m.index_no,2)

FROM  bit_marks AS m
INNER JOIN bit_course AS c
USING(course_id)
WHERE 
c.student_year=2 
HAVING(comp >= 4  AND opt >=4)





/*year 1 pass*/
-- select index_no,class_gpa,(select max(exam_hid) from bit_marks_course_view where index_no=bit_gpa.index_no and student_year=1) from bit_gpa where year=1 and class_gpa >= 1.5;

/*year 2 pass*/
-- select index_no,class_gpa,(select max(exam_hid) from bit_marks_course_view where index_no=bit_gpa.index_no and student_year=2) from bit_gpa where year=2 and class_gpa >= 1.5;

/*year 3 pass*/
-- select index_no,class_gpa,(select max(exam_hid) from bit_marks_course_view where index_no=bit_gpa.index_no and student_year=3) from bit_gpa where year=3T and class_gpa >= 2;

/*
SELECT m.index_no,c.alt_course_id,m.class_grade,min(grand_final_mark)
FROM bit_marks m, courseadmin.bitanalyse b, bit_course c
WHERE m.index_no=b.indexno AND m.course_id=c.course_id AND c.semester IN(1,2) AND ( b.Year1 LIKE '%DIT%' OR  b.Year1 LIKE '%CIT%' )
*/




/*
UPDATE bit_marks  SET repeat_max=0;
UPDATE bit_marks m,(
   SELECT m.exam_hid, m.index_no, m.base_course_id, m.grand_final_mark, (
      SELECT COUNT(base_course_id) 
      FROM bit_marks 
      WHERE index_no=m.index_no AND base_course_id=m.base_course_id
      GROUP BY base_course_id
   ) AS count
   FROM bit_marks m
   WHERE m.state='PR' 
      AND m.exam_hid=(
         SELECT exam_hid 
         FROM bit_marks 
         WHERE index_no=m.index_no AND base_course_id=m.base_course_id 
         ORDER BY grAND_final_mark DESC 
         LIMIT 1
      )
   GROUP BY m.index_no,m.base_course_id
) AS r
SET m.repeat_max=r.count
WHERE m.exam_hid=r.exam_hid AND m.index_no=r.index_no AND m.base_course_id=r.base_course_id
*/

/*
SELECT m.exam_hid, m.index_no, m.base_course_id, m.grand_final_mark, (
   SELECT COUNT(base_course_id) 
   FROM bit_marks 
   WHERE index_no=m.index_no 
      AND base_course_id=m.base_course_id 
   GROUP BY base_course_id) AS count
FROM bit_marks m
WHERE m.state='PR' 
   AND m.index_no='0807834' 
   AND m.exam_hid=(
      SELECT exam_hid FROM bit_marks WHERE index_no=m.index_no AND base_course_id=m.base_course_id ORDER BY grAND_final_mark DESC LIMIT 1
   )
GROUP BY m.index_no,m.base_course_id
*/


/*
SELECT * FROM deal_status
inner join
  (SELECT deal_id as did, max(timestamp) as ts
     FROM deal_status GROUP BY deal_id) as ds
  on deal_status.deal_id = ds.did AND deal_status.timestamp = ds.ts
*/


/*
SELECT r.index_no,count(distinct r.index_no,r.alt_course_id)
FROM(
SELECT  m.index_no,c.alt_course_id,max(grAND_final_mark) grAND_final_mark
FROM bit_marks m, bit_course c
WHERE m.course_id=c.course_id AND  state='PR' AND c.semester IN(1,2) AND  m.grAND_final_mark >= 50
GROUP BY m.index_no,c.alt_course_id
)as r
GROUP BY r.index_no
HAVING(count(distinct r.index_no,r.alt_course_id) >= 8)
*/

   

/*
  SELECT  m.index_no,COUNT(DISTINCT m.index_no,c.alt_course_id)
   FROM bit_marks m, bit_course c 
   WHERE c.course_id=m.course_id AND state='PR' AND c.semester IN(1,2) AND grAND_final_mark >= 50
   GROUP BY m.index_no
   HAVING(COUNT(DISTINCT m.index_no,c.alt_course_id) >=8);

/*173751  173751*/

/*
  SELECT m.index_no, c.alt_course_id, m.exam_hid, max(m.grAND_final_mark) 
   FROM bit_marks m, bit_course c 
   WHERE c.course_id=m.course_id 
      AND c.semester IN(1,2) 
      AND state='PR' 
      AND grAND_final_mark >= 50 
      AND index_no='0000622'
   GROUP BY m.index_no, c.alt_course_id  
   ORDER BY m.index_no
   LIMIT 100

/*
SELECT r.index_no, count(r.index_no), r.exam_hid 
FROM(
   SELECT m.index_no, m.exam_hid, m.course_id, c.alt_course_id, max(m.grAND_final_mark) 
   FROM bit_marks m, bit_course c 
   WHERE c.course_id=m.course_id 
      AND c.semester IN(1,2) 
      AND state='PR' 
      AND grAND_final_mark >= 50 
   GROUP BY m.index_no, c.alt_course_id  
   ORDER BY m.index_no
) AS r 
GROUP BY r.index_no 
HAVING(count(*) >=8);
*/
