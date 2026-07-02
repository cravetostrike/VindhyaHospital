<?php
$active_id = 'x-ray';
$active_dept = array (
  'name' => 'X-Ray',
  'parent_service' => 'Diagnostic Services',
  'parent_slug' => 'diagnostic-services',
  'subtitle' => 'Digital radiography for skeletal and thoracic imaging',
  'image' => 'images/patient_safety.png',
  'intro' => 'Digital X-Ray uses small doses of ionizing radiation to produce pictures of the bones and chest. It is the fastest and easiest way to diagnose bone fractures, joint dislocations, and pneumonia.',
  'features' => 
  array (
    0 => 'Chest X-Ray for heart and lung assessments.',
    1 => 'Bone and spine radiography for fracture detections.',
    2 => 'Abdominal X-Rays for bowel obstructions or calculi.',
    3 => 'Mobile bedside radiography for critical patients.',
  ),
  'why_choose' => 
  array (
    0 => 'Digital radiography panels for high-resolution images.',
    1 => 'Minimal radiation exposure guidelines.',
    2 => 'Fast processing, ready for surgical team review.',
    3 => 'Digital storage for future comparisons.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>