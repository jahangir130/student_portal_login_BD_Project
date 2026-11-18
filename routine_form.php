<?php
session_start(); require_once 'config.php'; require_once 'functions.php'; require_admin('routines');
$id=(int)($_GET['id']??0); $editing=$id>0; $error='';
$bubtDepartments=$pdo->query('SELECT department_name FROM bubt_departments ORDER BY department_name')->fetchAll(PDO::FETCH_COLUMN);
$days=['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
$periodEnd=['08:15:00'=>'09:45:00','09:45:00'=>'11:15:00','11:15:00'=>'12:45:00','13:15:00'=>'14:45:00','14:45:00'=>'16:15:00','16:15:00'=>'17:45:00'];
$periodLabels=['08:15:00'=>'08:15 AM – 09:45 AM','09:45:00'=>'09:45 AM – 11:15 AM','11:15:00'=>'11:15 AM – 12:45 PM','13:15:00'=>'01:15 PM – 02:45 PM','14:45:00'=>'02:45 PM – 04:15 PM','16:15:00'=>'04:15 PM – 05:45 PM'];
$data=['department'=>'CSE','intake_no'=>'55','section_no'=>'6','class_day'=>'Monday','course_code'=>'','course_name'=>'','teacher_name'=>'','start_time'=>'08:15:00','end_time'=>'09:45:00','room_no'=>''];
if($editing){$st=$pdo->prepare('SELECT * FROM class_routines WHERE routine_id=?');$st->execute([$id]);$found=$st->fetch();if(!$found){http_response_code(404);exit('Routine entry not found.');}$data=array_merge($data,$found); if(empty($data['intake_no'])||empty($data['section_no'])){if(preg_match('/(\d{1,2})[^0-9]+(?:.*?)(\d{1,2})/',$data['semester']??'', $m)){if((int)$m[1]>=1&&(int)$m[1]<=55)$data['intake_no']=$m[1];if((int)$m[2]>=1&&(int)$m[2]<=10)$data['section_no']=$m[2];}}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); foreach($data as $k=>$v){$data[$k]=trim((string)($_POST[$k]??$v));}
 $intake=(int)$data['intake_no']; $section=(int)$data['section_no']; $start=$data['start_time'];
 if($data['department']===''||$intake<1||$intake>65535||$section<1||$section>255||!in_array($data['class_day'],$days,true)||$data['course_code']===''||$data['course_name']===''||$data['teacher_name']===''||$data['room_no']===''){$error='Enter a valid Department, positive Intake/Batch and positive Section/Group, and complete all required fields.';}
 elseif(!isset($periodEnd[$start])){$error='Select one of the six fixed 90-minute periods. Break: 12:45 PM–01:15 PM.';}
 else{
  $semester='Intake '.$intake.' - Section '.$section;
  // Check every resource at the same fixed time slot.
  $st=$pdo->prepare('SELECT * FROM class_routines WHERE class_day=? AND start_time=? AND routine_id<>?');$st->execute([$data['class_day'],$start,$id]);$sameSlot=$st->fetchAll();
  foreach($sameSlot as $r){
   if(strcasecmp(trim($r['room_no']),$data['room_no'])===0){$error='Room conflict: '.$data['room_no'].' is already booked in this period.';break;}
   if(strcasecmp(trim($r['teacher_name']),$data['teacher_name'])===0){$error='Teacher conflict: this teacher already has a class in this period.';break;}
   $sameSection=((int)($r['intake_no']??0)===$intake && (int)($r['section_no']??0)===$section);
   if(!$sameSection && empty($r['intake_no']) && empty($r['section_no'])){$sameSection=(strcasecmp(trim($r['semester']),$semester)===0);}
   if(strcasecmp(trim($r['department']),$data['department'])===0 && $sameSection){$error='Student conflict: this department intake and section already has a class in this period.';break;}
  }
  // A section may have at most three consecutive 90-minute periods.
  if(!$error){$st=$pdo->prepare('SELECT start_time FROM class_routines WHERE department=? AND intake_no=? AND section_no=? AND class_day=? AND routine_id<>?');$st->execute([$data['department'],$intake,$section,$data['class_day'],$id]);$slots=$st->fetchAll(PDO::FETCH_COLUMN);$slots[]=$start;$mins=array_map(fn($t)=>(int)substr($t,0,2)*60+(int)substr($t,3,2),$slots);sort($mins);$run=1;foreach($mins as $i=>$m){if($i>0&&$m===$mins[$i-1]+90){$run++;}else{$run=1;}if($run>3){$error='Student routine rule: maximum 3 consecutive classes. Leave at least one period free after three classes.';break;}}}
  // Warn against an isolated first-and-last-period-only teaching day.
  if(!$error){$st=$pdo->prepare('SELECT start_time FROM class_routines WHERE LOWER(TRIM(teacher_name))=LOWER(TRIM(?)) AND class_day=? AND routine_id<>?');$st->execute([$data['teacher_name'],$data['class_day'],$id]);$teacherSlots=$st->fetchAll(PDO::FETCH_COLUMN);$teacherSlots[]=$start;$teacherMins=array_map(fn($t)=>(int)substr($t,0,2)*60+(int)substr($t,3,2),$teacherSlots);$hasFirst=in_array(495,$teacherMins,true);$hasLast=in_array(975,$teacherMins,true);$hasMiddle=count(array_filter($teacherMins,fn($m)=>$m!==495&&$m!==975))>0;if($hasFirst&&$hasLast&&!$hasMiddle){$error='Teacher balance rule: avoid assigning only the 1st and 6th periods to this teacher on the same day.';}}
  if(!$error){$end=$periodEnd[$start];if($editing){$st=$pdo->prepare('UPDATE class_routines SET department=?,semester=?,intake_no=?,section_no=?,class_day=?,course_code=?,course_name=?,teacher_name=?,start_time=?,end_time=?,room_no=? WHERE routine_id=?');$st->execute([$data['department'],$semester,$intake,$section,$data['class_day'],$data['course_code'],$data['course_name'],$data['teacher_name'],$start,$end,$data['room_no'],$id]);}else{$st=$pdo->prepare('INSERT INTO class_routines(department,semester,intake_no,section_no,class_day,course_code,course_name,teacher_name,start_time,end_time,room_no,created_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');$st->execute([$data['department'],$semester,$intake,$section,$data['class_day'],$data['course_code'],$data['course_name'],$data['teacher_name'],$start,$end,$data['room_no'],$_SESSION['admin_id']]);}header('Location: routines.php');exit;}
 }
}
page_start($editing?'Edit Class':'Add Class');admin_header('routines'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">ACADEMIC MANAGEMENT</span><h1><?= $editing?'Edit Class':'Add Class to Routine' ?></h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="form-panel"><a class="back-link" href="routines.php">← Back to Class Routine</a><h2>Class details</h2><p class="muted">University-wide routine: select any BUBT department and enter the actual Intake/Batch and Section/Group used by that program. Values are not hard-coded to a guessed range. Each class is 90 minutes. Break: 12:45 PM–01:15 PM.</p><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<div><label>Department *</label><input name="department" list="bubt_departments" value="<?= e($data['department']) ?>" placeholder="Select or type department" required><datalist id="bubt_departments"><?php foreach($bubtDepartments as $departmentName): ?><option value="<?= e($departmentName) ?>"><?php endforeach; ?></datalist></div>
<div><label>Intake / Batch *</label><input type="number" name="intake_no" min="1" max="65535" step="1" value="<?= e($data['intake_no']) ?>" placeholder="e.g. 56" required></div>
<div><label>Section / Group *</label><input type="number" name="section_no" min="1" max="255" step="1" value="<?= e($data['section_no']) ?>" placeholder="e.g. 1" required></div>
<div><label>Day *</label><select name="class_day"><?php foreach($days as $d): ?><option value="<?= e($d) ?>" <?= $data['class_day']===$d?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?></select></div>
<div><label>Course Code *</label><input name="course_code" value="<?= e($data['course_code']) ?>" placeholder="CSE 207" required></div><div><label>Course Name *</label><input name="course_name" value="<?= e($data['course_name']) ?>" required></div><div><label>Teacher Name *</label><input name="teacher_name" value="<?= e($data['teacher_name']) ?>" required></div>
<div><label>Class Period *</label><select name="start_time" required><?php foreach($periodLabels as $time=>$label): ?><option value="<?= e($time) ?>" <?= substr($data['start_time'],0,8)===$time?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
<div><label>Room Number *</label><input name="room_no" value="<?= e($data['room_no']) ?>" placeholder="e.g. 2517" required></div></div><div class="form-actions"><a class="btn light" href="routines.php">Cancel</a><button class="btn primary">Save Class</button></div></form></div><?php page_end(); ?>
