<?php
$active_id = 'physiotherapy';
$active_dept = array (
  'name' => 'Physiotherapy',
  'parent_service' => 'Clinical Support Services',
  'parent_slug' => 'clinical-support-services',
  'subtitle' => 'Physical therapy and muscle rehabilitation',
  'image' => 'images/hospital_admission.png',
  'intro' => 'The Physiotherapy section offers comprehensive rehabilitation programs to restore movement, improve strength, and manage pain following injuries, surgeries, or neurological conditions.',
  'features' => 
  array (
    0 => 'Orthopedic rehab following joint replacement and fracture surgeries.',
    1 => 'Neurological physiotherapy for stroke and paralysis patients.',
    2 => 'Electrotherapy treatments (SWD, IFT, TENS, Ultrasound).',
    3 => 'Manual therapy and therapeutic exercise training.',
  ),
  'why_choose' => 
  array (
    0 => 'Equipped rehabilitation gym.',
    1 => 'Highly experienced male and female physiotherapists.',
    2 => 'Personalized care plans based on recovery milestones.',
    3 => 'Integrated post-surgical orthopedic program.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>