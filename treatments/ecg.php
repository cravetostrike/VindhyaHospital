<?php
$active_id = 'ecg';
$active_dept = array (
  'name' => 'ECG',
  'parent_service' => 'Diagnostic Services',
  'parent_slug' => 'diagnostic-services',
  'subtitle' => 'Electrocardiogram for heart rhythm mapping',
  'image' => 'images/patient_safety.png',
  'intro' => 'An Electrocardiogram (ECG) records the electrical signals from your heart. It is a quick and painless test used to detect heart rhythm disorders, coronary artery blocks, and acute heart attacks.',
  'features' => 
  array (
    0 => '12-lead digital ECG recording.',
    1 => 'Rhythm strip analysis for arrhythmias.',
    2 => 'Pre-operative cardiac checkups.',
    3 => 'Bedside ECG in emergency room and ICU.',
  ),
  'why_choose' => 
  array (
    0 => 'Advanced digital multi-channel ECG machines.',
    1 => 'Instant results for quick diagnosis.',
    2 => 'Expert medical review of abnormal rhythms.',
    3 => '24/7 availability for cardiac emergencies.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>