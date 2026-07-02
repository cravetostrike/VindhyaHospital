<?php
$active_id = 'ultrasound';
$active_dept = array (
  'name' => 'Ultrasound',
  'parent_service' => 'Diagnostic Services',
  'parent_slug' => 'diagnostic-services',
  'subtitle' => 'High-resolution sonography and doppler imaging',
  'image' => 'images/patient_safety.png',
  'intro' => 'Ultrasound uses high-frequency sound waves to capture real-time images of internal organs, blood flow, and developing fetuses. It is non-invasive and safe, utilizing no radiation.',
  'features' => 
  array (
    0 => 'Abdomen and Pelvis ultrasound scanning.',
    1 => 'Obstetric and anomaly scans for fetal wellness.',
    2 => 'Color Doppler for arterial and venous blood flow.',
    3 => 'Small parts ultrasound (Thyroid, Scrotum, Breast).',
  ),
  'why_choose' => 
  array (
    0 => 'High-definition ultrasound machines.',
    1 => 'Supervised by experienced radiologists.',
    2 => 'Clean, private, and comfortable scanning rooms.',
    3 => 'Immediate scan reports with image printing.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>