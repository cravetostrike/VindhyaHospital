<?php
$active_id = 'oxygen-plant';
$active_dept = array (
  'name' => 'Oxygen Plant',
  'parent_service' => 'Clinical Support Services',
  'parent_slug' => 'clinical-support-services',
  'subtitle' => 'Continuous medical-grade oxygen supply infrastructure',
  'image' => 'images/hospital_admission.png',
  'intro' => 'Vindhya Hospital features a dedicated in-house Oxygen Generation Plant. This infrastructure generates high-purity medical oxygen, delivered directly to ICU beds, operating theaters, and wards via central pipelines.',
  'features' => 
  array (
    0 => 'PSA medical oxygen generation plant.',
    1 => 'Central pipeline delivery to critical care beds.',
    2 => 'Continuous pressure monitoring and alarm systems.',
    3 => 'Backup cylinder manifolds ensuring zero supply disruptions.',
  ),
  'why_choose' => 
  array (
    0 => 'Uninterrupted, self-reliant oxygen generation capability.',
    1 => 'Zero reliance on external oxygen cylinder logistics.',
    2 => 'High-purity medical oxygen meeting clinical specifications.',
    3 => 'Enhanced safety in critical care units.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>