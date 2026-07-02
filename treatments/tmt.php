<?php
$active_id = 'tmt';
$active_dept = array (
  'name' => 'T.M.T',
  'parent_service' => 'Diagnostic Services',
  'parent_slug' => 'diagnostic-services',
  'subtitle' => 'Treadmill Test for cardiac stress evaluations',
  'image' => 'images/patient_safety.png',
  'intro' => 'A Treadmill Test (TMT), also known as a cardiac stress test, records your ECG and blood pressure while you exercise on a treadmill. It helps identify coronary artery blockages that may be hidden during rest.',
  'features' => 
  array (
    0 => 'Continuous ECG monitoring during exercise phases.',
    1 => 'Blood pressure tracking at periodic intervals.',
    2 => 'Staged increase in treadmill speed and slope (Bruce Protocol).',
    3 => 'Post-exercise recovery phase monitoring.',
  ),
  'why_choose' => 
  array (
    0 => 'Advanced treadmill stress testing workstations.',
    1 => 'Supervised by experienced cardiologists.',
    2 => 'Continuous patient safety protocols during the test.',
    3 => 'Detailed stress-ECG reports.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>