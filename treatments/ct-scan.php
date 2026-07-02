<?php
$active_id = 'ct-scan';
$active_dept = array (
  'name' => 'C-T scan',
  'parent_service' => 'Diagnostic Services',
  'parent_slug' => 'diagnostic-services',
  'subtitle' => 'Advanced multi-slice Computed Tomography scanning',
  'image' => 'images/patient_safety.png',
  'intro' => 'Computed Tomography (CT) combines a series of X-Ray images taken from different angles to create cross-sectional views of bones, blood vessels, and soft tissues. It is essential for trauma assessments.',
  'features' => 
  array (
    0 => 'CT Brain and Head Trauma evaluations.',
    1 => 'HRCT Chest for detailed lung imaging.',
    2 => 'Contrast-enhanced CT scans (CECT) for abdominal organs.',
    3 => 'CT Angiography and skeletal reconstructions.',
  ),
  'why_choose' => 
  array (
    0 => 'Multi-slice CT scan system for fast acquisitions.',
    1 => 'Highly detailed cross-sectional imaging capability.',
    2 => 'Available 24/7 for emergency trauma cases.',
    3 => 'Radiologist-reviewed staging and diagnostic reports.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>