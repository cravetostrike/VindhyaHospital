<?php
$active_id = 'health-services';
$active_dept = array (
  'name' => 'Health Services',
  'subtitle' => 'Government schemes, corporate panels, and health insurance desk',
  'image' => 'images/diversity_team.png',
  'intro' => 'Vindhya Hospital is committed to making healthcare accessible and affordable for all. The Health Services department coordinates various health insurance policies, corporate medical schemes, and government wellness programs to ensure cashless and subsidized treatments.',
  'is_major' => true,
  'sub_services' => 
  array (
    'mediclaim' => 'Mediclaim',
    'ayushman' => 'Ayushman',
    'echs' => 'ECHS',
    'sghs' => 'SGHS',
  ),
  'features' => 
  array (
    0 => 'Dedicated TPA and insurance coordination desk.',
    1 => 'Cashless hospitalization support under corporate panels.',
    2 => 'Empanelled under government schemes like Ayushman Bharat.',
    3 => 'Specialized support for veteran schemes (ECHS).',
  ),
  'why_choose' => 
  array (
    0 => 'Smooth and hassle-free insurance claims processing.',
    1 => 'Subsidized and cashless treatments for eligible cardholders.',
    2 => 'Transparent documentation and billing guidelines.',
    3 => 'Helpdesk assistance for registration and approvals.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>