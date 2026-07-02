<?php
$active_id = 'radiology-services';
$active_dept = array (
  'name' => 'Radiology Services',
  'subtitle' => 'Advanced medical imaging and interventional radiology',
  'image' => 'images/policies_procedures.png',
  'intro' => 'Our Radiology Services department provides advanced medical imaging techniques to visualize the internal structures of the body. With fixed and mobile X-Ray systems and advanced C-Arm setups, we provide essential support for both diagnostics and surgical interventions.',
  'is_major' => true,
  'sub_services' => 
  array (
    'xray-fix-mobile-carm' => 'X-RAY Fix Mobile C-Arm',
  ),
  'features' => 
  array (
    0 => 'High-definition fixed digital X-Ray systems.',
    1 => 'Mobile X-Ray units for bedside imaging in ICU and wards.',
    2 => 'Advanced C-Arm imaging support inside operating theaters.',
  ),
  'why_choose' => 
  array (
    0 => 'Trained radiographers and imaging specialists.',
    1 => 'Minimal radiation exposure protocols (ALARA).',
    2 => 'Seamless integration with surgical operating teams.',
    3 => 'Instant image retrieval and digital storage.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>