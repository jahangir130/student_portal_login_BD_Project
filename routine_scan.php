<?php
session_start();require_once 'config.php';require_once 'functions.php';require_admin('routines');
$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$text=trim($_POST['extracted_text']??'');
if(!isset($_FILES['routine_image'])||$_FILES['routine_image']['error']!==UPLOAD_ERR_OK){$error='Please choose a routine image.';}
elseif($_FILES['routine_image']['size']>5*1024*1024){$error='Image must be 5 MB or smaller.';}
else{$tmp=$_FILES['routine_image']['tmp_name'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if(!isset($allowed[$mime])){$error='Only JPG, PNG and WEBP images are allowed.';}
else{$dir=__DIR__.'/uploads/routines';if(!is_dir($dir)){mkdir($dir,0755,true);}$filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
if(move_uploaded_file($tmp,$dir.'/'.$filename)){$st=$pdo->prepare('INSERT INTO routine_scans(image_path,extracted_text,uploaded_by) VALUES(?,?,?)');$st->execute(['uploads/routines/'.$filename,$text,$_SESSION['admin_id']]);$success='Image and OCR draft saved. Review it and add class records manually.';}else{$error='Could not save the uploaded image.';}}}}
page_start('Scan Routine Image');admin_header('routines'); ?>
<main class="main-content"><header class="topbar"><div><span class="crumb">SMART ROUTINE SCANNER</span><h1>Scan Routine Image</h1></div><div class="top-user"><?= e($_SESSION['admin_name']) ?><span class="top-avatar">A</span></div></header>
<div class="form-panel scan-panel"><a class="back-link" href="routines.php">← Back to Class Routine</a><h2>Upload timetable image</h2><p class="muted">Select a clear JPG, PNG or WEBP image. OCR runs in your browser with Tesseract.js. Review the extracted text carefully.</p>
<?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="success"><?= e($success) ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" id="scanForm"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="upload-box"><div class="upload-symbol">⇧</div><strong>Select routine image</strong><span>JPG / PNG / WEBP · Maximum 5 MB</span><input type="file" name="routine_image" id="routineImage" accept="image/jpeg,image/png,image/webp" required></div>
<img id="imagePreview" class="image-preview" alt="Routine preview" hidden>
<div class="scan-actions"><button type="button" class="btn dark" id="scanButton">Scan Image (OCR)</button><span id="scanStatus" class="scan-status">Select an image to begin.</span></div>
<label class="scan-label" for="extractedText">Extracted text — review and correct it</label><textarea id="extractedText" name="extracted_text" rows="12" placeholder="OCR text appears here. You can edit it manually."></textarea>
<button class="btn primary save-scan" type="submit">Save Image & OCR Draft</button></form>
<div class="hint">OCR reads text but may not understand timetable columns. After scanning, add each class as a structured entry using Add Class.</div></div><?php page_end(); ?>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
const input=document.getElementById('routineImage'),preview=document.getElementById('imagePreview'),button=document.getElementById('scanButton'),statusText=document.getElementById('scanStatus'),output=document.getElementById('extractedText');
input.addEventListener('change',()=>{const file=input.files[0];if(file){preview.src=URL.createObjectURL(file);preview.hidden=false;statusText.textContent='Image ready. Click Scan Image (OCR).';}});
button.addEventListener('click',async()=>{const file=input.files[0];if(!file){statusText.textContent='Please select an image first.';return;}button.disabled=true;statusText.textContent='Scanning image…';
try{const result=await Tesseract.recognize(file,'eng',{logger:m=>{if(m.status==='recognizing text')statusText.textContent='Recognizing text: '+Math.round((m.progress||0)*100)+'%';}});output.value=result.data.text||'No text detected. Try a clearer image.';statusText.textContent='Scan finished. Review the extracted text.';}
catch(err){statusText.textContent='OCR could not run. Check your internet connection and retry.';}finally{button.disabled=false;}});
</script>
