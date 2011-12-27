delimiter ||
DROP FUNCTION IF EXISTS tosit_y2||
CREATE FUNCTION tosit_y2(index_no_ VARCHAR(8), year_ TINYINT(1)) RETURNS TEXT READS SQL DATA
BEGIN
   DECLARE revision_    INT(1);

   DECLARE to_sit_comp_       TEXT;
   DECLARE to_sit_comp_min_   INT(2);
   DECLARE to_sit_comp_count_ INT(2);

   DECLARE to_sit_opt_        TEXT;
   DECLARE to_sit_opt_min_    INT(2);
   DECLARE to_sit_opt_count_  INT(2);

   DECLARE to_sit_lms_        TEXT;
   DECLARE lms_pass           boolean;

   SET to_sit_opt_min_  = 4;
   SET to_sit_comp_min_ = 4;

   SELECT MAX(revision) INTO revision_ FROM bit_course WHERE student_year=year_;

   SELECT GROUP_CONCAT(course_id),COUNT(*) INTO to_sit_comp_,to_sit_comp_count_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND compulsory AND LEFT(course_id,4) NOT IN( SELECT LEFT(course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50);

   SELECT GROUP_CONCAT(course_id) INTO to_sit_opt_ FROM bit_course WHERE revision=revision_ AND student_year=year_ AND NOT compulsory AND LEFT(alt_course_id,4) NOT IN( SELECT LEFT(alt_course_id,4) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=year_ AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50);


   SELECT count(*) INTO to_sit_opt_count_ FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=2 AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50;
   SELECT count(*) INTO lms_pass FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=index_no_ AND student_year=2 AND repeat_max >= 1 AND final_mark = 'CM';


   IF !lms_pass THEN
      SET to_sit_lms_ = '. TO SIT: LMS Assesments';
   ELSE
      SET to_sit_lms_ = '';
   END IF;

   IF ISNULL(to_sit_comp_) THEN
      SET to_sit_comp_ = CONCAT('TO SIT: ALL Compulsory');
   ELSE
      SET to_sit_comp_ = CONCAT('TO SIT:',to_sit_comp_);
   END IF;

   IF to_sit_opt_min_-to_sit_opt_count_ > 0 THEN
      SET to_sit_opt_  = CONCAT('. TO SIT:',to_sit_opt_min_-to_sit_opt_count_,' FROM (',to_sit_opt_,')');
   ELSE
      SET to_sit_opt_  = '';
   END IF;

   RETURN CONCAT(to_sit_comp_,to_sit_opt_,to_sit_lms_);
END;             
||
delimiter ;



SELECT m.index_no,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND compulsory AND grand_final_mark >= 50) comp,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND NOT compulsory AND grand_final_mark >= 50) opt,
(SELECT count(*) FROM bit_marks INNER JOIN bit_course USING(course_id) WHERE index_no=m.index_no AND student_year=2 AND repeat_max >= 1 AND final_mark = 'CM') lms,
tosit_y2(m.index_no,2) tosit

FROM  bit_marks AS m
INNER JOIN bit_course AS c
USING(course_id)
WHERE 
c.student_year=2
HAVING(NOT(comp >= 4  AND opt >=4))
