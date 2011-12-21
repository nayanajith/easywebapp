<?php
class Attendance{

	protected $self=array();

	//Store dates events of coresponding dates will store here
	protected $month_arr= array();

	//Available status in database
	protected $event_status_arr=array(
		'Master Menu Authorized'=>"-1", 
		'Master FP Registered'	=>'-1', 
		'Master Menu Exit'		=>'-1', 
		'Granted(ID & FP)'		=>'1', 
		'User FP Registered'		=>'-1', 
		'Denied(FP)'				=>'-1', 
		'Master Menu Fail'		=>'-1', 
		'Time Changed'				=>'-1', 
		'Denied(ID)'				=>'-1', 
		'Card Deleted'				=>'-1', 
		'FP Download Success'	=>'-1', 
		'Granted(ID)'				=>'-1', 
		'Power ON'					=>'-1', 
		'Cover Closed'				=>'-1', 
		'Forced Open'				=>'-1' 
		);

		protected $attend_state=array(
		"UNKNOWN",
		"ABSENT",			//1
		"NOT_YET_ARRIVE",	//2
		"NOT_YET_LEAVE",	//3
		"OUT_NOT_RECORDED",//4
		//"HALF_DAY",			//5
		"LESS_THAN_8_HRS",			//5
		"FULL_DAY"			//6
		);

		/*
		 * Construct of the attendance class
		 */
		public function __construct($month=null) {

			//$empNo	=$_SESSION['username'];
			$empNo	='00000011';
			$empName	=$_SESSION['fullname'];

			$this->self['in_time']	='08.30.00';
			$this->self['out_time']	='16.30.00';
			$this->self['reliaf']	='00.30.00';
			$this->self['hour']		=3600;
			$this->self['today']		=getdate();
			$this->self['duration']	=8*$this->self['hour'];
			$this->self['days']		=0;
			$this->self['last_month']=false;

			//Setting the month
			if($month == 'last'){
				$this->self['today']=getdate(strtotime('last day last month'));
				$this->self['last_month']=true;
			}

			//Set number of days for the given month/year
			$this->self['days']=date('t',mktime(0,0,0,$this->self['today']['mon'],1,$this->self['today']['year']));

			//query for last month
			$sql_last_month= "
		    SELECT * 
		    FROM in_out 
		    WHERE Event_UserID= '".$empNo."' 
		    AND YEAR(Event_DateTime_Str)=YEAR(NOW()) 
		    AND MONTH(Event_DateTime_Str)=MONTH(NOW()-INTERVAL 1 MONTH) 
		    ORDER BY Event_DateTime_Str ASC";

			//query for this month
			$sql_this_month= "
		    SELECT * 
		    FROM in_out 
		    WHERE Event_UserID= '".$empNo."' 
		    AND YEAR(Event_DateTime_Str)=YEAR(NOW()) 
		    AND MONTH(Event_DateTime_Str)=MONTH(NOW()) 
		    ORDER BY Event_DateTime_Str ASC";

			 echo $sql_this_month;
				
			if($GLOBALS['DB_TYPE']=='mssql'){
				//query for last month
				$sql_last_month= "
            SELECT * 
            FROM TBL_History
            WHERE Event_UserID= '".$empNo."' 
            AND DATEPART(yy, Event_DateTime_Str)=DATEPART(yy, GETDATE()) 
            AND DATEPART(mm,Event_DateTime_Str)=DATEPART(mm,DATEADD ( mm , -1, GETDATE())) 
            ORDER BY Event_DateTime_Str DESC";

				//query for this month
				$sql_this_month= "
            SELECT *
            FROM TBL_History
            WHERE (Event_UserID = '".$empNo."') 
            AND (DATEPART(yy, Event_DateTime_Str) = DATEPART(yy, GETDATE())) 
            AND (DATEPART(mm, Event_DateTime_Str) = DATEPART(mm, GETDATE()))
            ORDER BY Event_DateTime_Str DESC";
			}
				
			//Select the relevant query
			$sql=$sql_this_month;
			if($month == 'last'){
				$sql=$sql_last_month;
			}


			//Initializing array
			for($i=1;$i<=$this->self['today']['mday'];$i++){
				$this->month_arr[$i]=array();
			}

			opendb();
			
			$res = mysql_query($sql,$GLOBALS['CONNECTION']);
			if($GLOBALS['DB_TYPE']=='mssql'){
				mssql_select_db("KEICONEW", $GLOBALS['CONNECTION']);
			   $res = mssql_query($sql,$GLOBALS['CONNECTION']);
			}
			
			closedB();
			//while($row =mssql_fetch_array($res)){
			while($row =mysql_fetch_array($res)){
				//Finger print and ID status
				$event_status=$this->event_status_arr[$row['Event_TrType']];

				//Time of the event in milliseconds
				$time_secs=strtotime($row['Event_DateTime_Str']);

				//Date of the event
				$date=date('j',$time_secs);

				/*Fill the array with valid avents of the month
				 * all the valid events in each day will included as 2D array
				 */
				if($event_status==1){
					$this->month_arr[$date][]=$time_secs;
				}
			}
			echo json_encode($this->month_arr);
		}

		/*
		 * Return common date string format
		 * MM/DD/YYY
		 */
		public function cdf($dd,$mm,$yyyy){
			return sprintf("%s/%s/%s",$mm,$dd,$yyy);
		}

		/*
		 * --------HELP--------
		 getdate()
		 Array
		 (
		 	 [seconds] => 40
		 	 [minutes] => 58
		 	 [hours]   => 21
		 	 [mday]    => 17
		 	 [wday]    => 2
		 	 [mon]     => 6
		 	 [year]    => 2003
		 	 [yday]    => 167
		 	 [weekday] => Tuesday
		 	 [month]   => June
		 	 [0]       => 1055901520
		 )


		 date('m/d/y', strtotime('first day')); # 02/01/10
		 date('m/d/y', strtotime('last day')); # 02/28/10
		 date('m/d/y', strtotime('last day next month')); # 03/31/10
		 date('m/d/y', strtotime('last day last month')); # 01/31/10
		 date('m/d/y', strtotime('2009-12 last day')); # 12/31/09 - this doesn't work if you reverse the order of the year and month
		 date('m/d/y', strtotime('2009-03 last day')); # 03/31/09
		 date('m/d/y', strtotime('2009-03')); # 03/01/09
		 date('m/d/y', strtotime('last day of march 2009')); # 03/31/09
		 date('m/d/y', strtotime('last day of march')); # 03/31/10
		 */

		/*
		 echo "<pre>";
		 print_r($this->month_arr);
		 echo "</pre>";
		 */

		//IF the given day is today it will return true
		public function is_today($day){
			if($this->self['last_month']){
				return false;
			}

			$today=getDate();
			if($today['mday']== $day){
				return true;
			}
		}

		//Return Human readable time -> convert seconds to h:m:s
		public function get_hr_time($secs){
			$min		=intval($secs/60);
			$rem_sec	=$secs%60;
			$hrs		=intval($min/60);
			$rem_min	=$min%60;
			return $hrs.":".$rem_min.":".$rem_sec;
		}


		public function get_day_info($day){
			$in_secs	=strtotime($this->self['in_time']);
			$out_secs=strtotime($this->self['out_time']);

			//The array to be return filled with the detail of on day
			$day_info=array(
			'day'			=>$day,
			'state'		=>0,
			'in'			=>0,
			'out'			=>0,
			'in_offset'	=>0,
			'out_offset'=>0,
			'duration'	=>0
			);

			$events;
			if(isset($this->month_arr[$day])){
				$events=$this->month_arr[$day];
			}else{
				return $day_info;
			}

			//Working on single evented days
			//-1=>yet not came/leave
			//-2=>absent
			if(sizeof($events)==0){
				//Check for the events old
				if($this->is_today($day)){
					$day_info['state']=2;
				}else{
					$day_info['state']=1;
				}
				return $day_info;
			}elseif(sizeof($events)==1){
				//Check for the events old
				$day_info['in']			=$events[0];//in time of the user
				$day_info['in_offset']	=strtotime(date('H:i:s',$day_info['in']))-$in_secs; //in offset of the user

				if($this->is_today($day)){
					$day_info['state']=3;
				}else{
					$day_info['state']=4;
				}
			}elseif(sizeof($events)>=2){
				sort($events);
				$day_info['in']			=$events[0];
				$day_info['in_offset']	=strtotime(date('H:i:s',$events[0]))-$in_secs;
				$day_info['out']			=$events[sizeof($events)-1];
				$day_info['out_offset']	=strtotime(date('H:i:s',$events[sizeof($events)-1]))-$out_secs;
				$day_info['duration']	=($events[sizeof($events)-1]-$events[0]);


				if($day_info['duration']>=$this->self['duration']){
					$day_info['state']=6;
				}else{
					$day_info['state']=5;
				}
			}
			return $day_info;
		}

		//Generate table row for the calender events
		public function calendar_tr_gen($key,$value,$key_title,$value_title){
			if($value=='Out not recorded' || $value=='Less than 8 hrs'){
				return "<tr>
				<th title='".$key_title."' style='color:green'>".$key."</th>
				<td title='".$value_title."' style='color:red;font-size:11px;' width=100%>".$value."</td>
				</tr>";
			}else{
				return "<tr>
				<th title='".$key_title."' style='color:green'>".$key."</th>
				<td title='".$value_title."' style='color:black;font-size:11px;' width=100%>".$value."</td>
				</tr>";
			}
		}
		
		
		//Attendence summery of month
   protected $attend_summery=array(
      "FULL_DAY"        =>0,        
      //"HALF_DAY"        =>0,        
      "LESS_THAN_8_HRS"        =>0,        
      "OUT_NOT_RECORDED"=>0,
      "NOT_YET_LEAVE"   =>0,  
      "NOT_YET_ARRIVE"  =>0,  
      "ABSENT"          =>0,        
      "CUMUL_TIME"      =>0
         );
		public function print_day_info($day){

			$day_info=$this->get_day_info($day);

			$info_table="<table width=100% >";
			//echo "<td>".$day_info['day']."</td>";
			//$info_table.=$this->calendar_tr_gen("S",style_text($this->attend_state[$day_info['state']]),'State',"Comment");
			switch($day_info['state']){
				case 1:
				case 2:
					if($this->is_holiday($day)==false){
						$info_table.=$this->calendar_tr_gen("S:",style_text($this->attend_state[$day_info['state']]),"State","");
						$this->attend_summery[$this->attend_state[$day_info['state']]]+=1;
					}else{
						//$info_table.=$this->calendar_tr_gen("",$this->is_holiday($day),"","");
					}
					break;
				case 3:
				case 4:
					$info_table.=$this->calendar_tr_gen("S:",style_text($this->attend_state[$day_info['state']]),'State',"Comment");
					$info_table.=$this->calendar_tr_gen("I:",date('h:i:s a',$day_info['in']),'In Time',"Offset:".$this->get_hr_time($day_info['in_offset']));
					$this->attend_summery[$this->attend_state[$day_info['state']]]+=1;
					break;
				case 5:
					$info_table.=$this->calendar_tr_gen("S:",style_text($this->attend_state[$day_info['state']]),'State',"");
					$info_table.=$this->calendar_tr_gen("D:",$this->get_hr_time($day_info['duration']),"Duration","See Note");
					$info_table.=$this->calendar_tr_gen("I:",date('h:i:s a',$day_info['in']),"In Time","Offset:".$this->get_hr_time($day_info['in_offset']));
					$info_table.=$this->calendar_tr_gen("O:",date('h:i:s a',$day_info['out']),'Out Time',"Offset:".$this->get_hr_time($day_info['out_offset']));

					$this->attend_summery[$this->attend_state[$day_info['state']]]+=1;
					$this->attend_summery['CUMUL_TIME']+=$day_info['duration']/$this->self['hour'];
					break;
				case 6:
					$info_table.=$this->calendar_tr_gen("S:",style_text($this->attend_state[$day_info['state']]),'State',"");
					$info_table.=$this->calendar_tr_gen("D:",$this->get_hr_time($day_info['duration']),"Duration","See Note");
					$info_table.=$this->calendar_tr_gen("I:",date('h:i:s a',$day_info['in']),'In Time',"Offset:".$this->get_hr_time($day_info['in_offset']));
					$info_table.=$this->calendar_tr_gen("O:",date('h:i:s a',$day_info['out']),'Out Time',"Offset:".$this->get_hr_time($day_info['out_offset']));

					$this->attend_summery[$this->attend_state[$day_info['state']]]+=1;
					$this->attend_summery['CUMUL_TIME']+=$day_info['duration']/$this->self['hour'];
					break;
				default:
					$info_table.=$this->calendar_tr_gen("","","","");
			}
			return $info_table."</table>";
		}

		public function print_month_info(){
			echo "<h3>Attendance of the month".$today['month']." up to ".$today['mday']."</h3>";
			echo "<table border=1 style='border-collapse:collapse;' cellpadding=5>";
			//echo "<tr><th>DATE</th><th>IN</th><th>IN OFFSET</th><th>OUT</th><th>OUT OFFSET</th><th>DURATION</th><th>STATE</th></tr>";
			echo "<tr><th>DATE</th><th>IN</th><th>OUT</th><th>DURATION</th></tr>";
			foreach ($this->month_arr as $day) {
				$this->print_day_info($day);
			}
			echo "</table>";
		}


		//Print Symmery

		public function print_summery(){
			//echo "<br><h3>Summery of the month ".$this->self['today']['month']."</h3>";
			//echo "<br><h3>Summery of the month</h3>";
			echo "<table border=0 style='border-collapse:collapse;' cellpadding=5>";
			/*
			 foreach ($attend_summery as $key => $value) {
			 echo "<tr><th>".$key."</th><td>".$value."</td></tr>";
			 }
			 */
			echo "<tr><td>Cumulative time of the month</td><td>:".$this->attend_summery['CUMUL_TIME']." hrs</td></tr>";
			echo "<tr><td>Absents days of the month</td><td>:".$this->attend_summery['ABSENT']."</td></tr>";
			echo "<tr><td>Half-Day days of the month</td><td>:".$this->attend_summery['LESS_THAN_8_HRS']."</td></tr>";
			echo "<tr><td>Out Not Recorded days of the month</td><td>:".$this->attend_summery['OUT_NOT_RECORDED']."</td></tr>";
			echo "</table>";
		}



		/*
		 * Holiday array generated automatically or by hand to be
		 * displayed in calender
		 * date -> [10/31/2010] 
		 */
		protected $holiday_arr=array();
		public function gen_holiday($holidays){
			$this->holiday_arr=$holidays;
			for($day=1;$day<=$this->self['days'];$day++){
				$day_str=$this->self['today']['year']."-".$this->self['today']['mon']."-".$day;
				$day_info=getdate(strtotime($day_str));
				//Sunday and Saturday added as holiday
				if($day_info['weekday']=='Sunday' || $day_info['weekday']=='Saturday'){
					//$this->holiday_arr[$this->self['today']['mon']."/".$day."/".$this->self['today']['year']]='Weekend';
					$this->holiday_arr[$this->self['today']['mon']."/".$day."/".$this->self['today']['year']]='&nbsp;';
				}
			}
		}


		/*
		 * Style the box according to the holiday and other specialities
		 * date -> [10/31/2010] 
		 * $chk_date -> 31  -> dd
		 */
		public function is_holiday($chk_date){
			$holiday=false;
			foreach ($this->holiday_arr as $date => $reason) {
				if(strtotime($this->self['today']['mon']."/".$chk_date."/".$this->self['today']['year'])==strtotime($date)){
					$holiday=$reason;
				}
			}
			return $holiday;
		}
		
		protected $colors=array(
		    "cal_title_ft" =>"blue",
		    "cal_title_bg" =>"#C9D7F1",
		    "cal_title_bdr"=>"gray",
		    "day_ft"       =>"black",
		    "day_bg"       =>"whitesmoke",
		    "holiday_ft"   =>"red",
		    "holiday_bg"   =>"#79CDCD",
		    "today_ft"     =>"black",
		    "today_bg"     =>"silver"
		);
		/*
		 * Styles for the calender
		 */
		public function style(){
		   echo "
		   <style type='text/css'>
		    .calendar{border-collapse:collapse;border-color:#C9D7F1;width:850px;}
		    .title   {background:#C9D7F1;font-weight:bold;color:blue;border:1px solid silver;}
		    .day     {background:whitesmoke;}
		    .date    {border:1px solid gray;padding:2px;font-weight:bold;}
		    .today   {background:silver;}
		    .holiday {background:#79CDCD;color:red;}
		    .hol_inf {color:blue;font-size:11px;}
		   </style>
		   "; 	
		}

		/*
		 * Style the date box according to the date state(holiday,today)
		 */
		public function day_style($date){
			if($this->is_today($date)){
				return "class=today";
			}elseif($this->is_holiday($date)!=false){
				return "class=holiday";
			}else{
				return "class=day";
			}
		}


		/*
		 * Generate calendar array and print the calendar
		 */
		public function print_calendar(){

			//fill the days of each week in order
			$week = array(
				"Monday"		=>0,
				"Tuesday"	=>0,
				"Wednesday"	=>0,
				"Thursday"	=>0,
				"Friday"		=>0,
				"Saturday"	=>0,
				"Sunday"		=>0
			);

			//Normally there are six lines in a month
			//this array will hold those lines to be printed after
			$lines_arr = array();

			//fill the line_arry according to the given month
			for($day=1;$day<=$this->self['days'];$day++){
				$day_str=$this->self['today']['year']."-".$this->self['today']['mon']."-".$day;
				$day_info=getdate(strtotime($day_str));

				$week[$day_info['weekday']]=$day;

				//Week ends from Sunday (last column)
				$end_day='Sunday';
				if($day_info['weekday']==$end_day || $day==$this->self['days']){
					$lines_arr[]=$week;
					foreach($week as $key=>$value){
						$week[$key]=0;
					}
				}
			}

			//Printing the line array as a table (calender)
			$this->style();
			echo "<h3>Attendance Calendar of the month ".$this->self['today']['month']."</h3>";
			echo "<table cellpadding=5 cellspacing=0 width=100% class=calendar border=1 >";
			echo "<tr>";
			foreach ($week as $day => $value) {
				echo "<th class=title>".$day."</th>";
			}
			echo "</tr>";

			foreach ($lines_arr as $line) {
				echo "<tr>";
				foreach ($line as $day => $date) {
					if($date==0){
						echo "<td></td>";
					}else{
						echo "<td width=70 ".$this->day_style($date)."  align='left' valign='top' title='".$this->is_holiday($date)."'>"
						."<a class=date>".$date."</a>
						&nbsp;<span class=hol_inf>".$this->is_holiday($date)."</span>"
						.$this->print_day_info($date)."</td>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";
		}
}

?>
