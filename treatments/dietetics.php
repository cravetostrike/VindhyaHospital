<?php
$active_id = 'dietetics';
$active_dept = array (
  'name' => 'Dietetics',
  'parent_service' => 'Clinical Support Services',
  'parent_slug' => 'clinical-support-services',
  'subtitle' => 'Clinical nutrition and diet counseling',
  'image' => 'images/hospital_admission.png',
  'intro' => 'Dietetics provides expert nutritional counseling to support disease management, recovery, and overall wellness. Our clinical dietitians design specialized food plans for hospitalized and OPD patients.',
  'features' => 
  array (
    0 => 'Diabetic, renal, and hypertension diet charts.',
    1 => 'Enteral and parenteral nutrition plans for critical patients.',
    2 => 'Weight management and lifestyle counseling.',
    3 => 'Nutritional assessment and pediatric growth guidance.',
  ),
  'why_choose' => 
  array (
    0 => 'Certified clinical nutritionists.',
    1 => 'Inpatient diet plans monitored by healthcare teams.',
    2 => 'Customizable diet sheets for outpatient follow-up.',
    3 => 'Scientific approach to therapeutic diets.',
  ),
);
require_once __DIR__ . '/treatment.php';
?>