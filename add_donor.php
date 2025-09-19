<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
require_once "db.php"; // your DB connection (must define $conn)

// Set timezone to IST (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Helper: compute age parts and age string from a YYYY-MM-DD dob
function compute_age_from_dob($dob_string) {
    $result = ['years' => null, 'months' => null, 'days' => null, 'ageString' => ''];
    if (!$dob_string) return $result;

    // Try to create DateTime; expects Y-m-d
    try {
        $birth = new DateTime($dob_string);
    } catch (Exception $e) {
        // fallback - try strtotime
        $ts = strtotime($dob_string);
        if ($ts === false) return $result;
        $birth = (new DateTime())->setTimestamp($ts);
    }

    $today = new DateTime();
    if ($birth > $today) return $result;

    $diff = $today->diff($birth);
    $years = (int)$diff->y;
    $months = (int)$diff->m;
    $days = (int)$diff->d;
    $parts = [];
    if ($years > 0) $parts[] = $years . ' ' . ($years === 1 ? 'Year' : 'Years');
    if ($months > 0) $parts[] = $months . ' ' . ($months === 1 ? 'Month' : 'Months');

    if (count($parts) > 0) {
        $ageString = implode(' and ', $parts);
    } else {
        if ($days === 0) $ageString = 'Born today';
        else $ageString = $days . ' ' . ($days === 1 ? 'Day' : 'Days');
    }

    $result['years'] = $years;
    $result['months'] = $months;
    $result['days'] = $days;
    $result['ageString'] = $ageString;
    return $result;
}

// default values (so form retains values on validation error)
$category = $patient_name = $first_name = $middle_name = $surname = $gender = $dob = $age = '';
$patient_first_name = $patient_middle_name = $patient_surname = '';
$occupation = $address = $city_village = $district = $state = $pincode = $mobile_no = $unit_no = $bag_type = $segment_no = $marriage_anniversary = $blood_group = '';
$donated_before = $donation_count = $hours_since_meal = $collection_date = $collection_time = $medication = $health = $bp = $temperature = $weight = '';
$success = $error = '';

// Set max date for date picker to today (prevent future dates)
$dob_max_for_picker = date('Y-m-d');

// default collection_date (server-side safeguard) - now using IST timezone
$collection_date_default = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // sanitize & assign posted values (trim)
    $category = trim($_POST['category'] ?? '');
    $patient_name = trim($_POST['patient_name'] ?? '');
    $patient_first_name = trim($_POST['patient_first_name'] ?? '');
    $patient_middle_name = trim($_POST['patient_middle_name'] ?? '');
    $patient_surname = trim($_POST['patient_surname'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $city_village = trim($_POST['city_village'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $address = $city_village . ', ' . $district . ', ' . $state . ' - ' . $pincode;
    $mobile_no = trim($_POST['mobile_no'] ?? '');
    $unit_no = trim($_POST['unit_no'] ?? '');
    $bag_type = trim($_POST['bag_type'] ?? '');
    $segment_no = trim($_POST['segment_no'] ?? '');
    $marriage_anniversary = trim($_POST['marriage_anniversary'] ?? '');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $donated_before = trim($_POST['donated_before'] ?? '');
    $donation_count = trim($_POST['donation_count'] ?? '');
    $hours_since_meal = trim($_POST['hours_since_meal'] ?? '');
    $collection_date = trim($_POST['collection_date'] ?? '') ?: $collection_date_default;
    $collection_time = trim($_POST['collection_time'] ?? '') ?: date('H:i');
    $medication = trim($_POST['medication'] ?? '');
    $health = trim($_POST['health'] ?? '');
    $bp = trim($_POST['bp'] ?? '');
    $temperature = trim($_POST['temperature'] ?? '');
    $weight = trim($_POST['weight'] ?? '');
    $healthy_today = trim($_POST['healthy_today'] ?? '');
    $slept_well = trim($_POST['slept_well'] ?? '');
    $ate_recently = trim($_POST['ate_recently'] ?? '');
    $no_contagious_disease = trim($_POST['no_contagious_disease'] ?? '');

    // Server-side compute age and validate
    $ageInfo = compute_age_from_dob($dob);
    $computedYears = $ageInfo['years'];
    $age = $ageInfo['ageString']; // string like "24 Years and 5 Months"

    // Mobile number validation
    // In your PHP code where you validate mobile number:
    if (empty($mobile_no)) {
    $error = "Mobile number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile_no)) {
    $error = "Invalid mobile number. Please enter exactly 10 digits.";
    } elseif (!preg_match('/^[6-9][0-9]{9}$/', $mobile_no)) {
    $error = "Invalid mobile number format. Please enter a valid 10-digit Indian mobile number starting with 6,7,8 or 9.";
    } elseif (empty($dob) || $computedYears === null) {
        $error = "Invalid or missing Date of Birth.";
    } elseif ($computedYears < 18) {
        $error = "Donor must be at least 18 years old. Current age is: " . ($computedYears ?? 'N/A') . " years.";
    } else {
        // Insert using prepared statement
        $stmt = $conn->prepare("INSERT INTO donors (category, patient_name, first_name, middle_name, surname, gender, dob, age, occupation, address, mobile_no, unit_no, bag_type, segment_no, marriage_anniversary, blood_group, donated_before, donation_count, hours_since_meal, collection_date, collection_time, medication, health, bp, temperature, weight, healthy_today, slept_well, ate_recently, no_contagious_disease) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        if (!$stmt) {
            $error = "Database prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssssssssssssssssssssssssssssss",
                $category, $patient_name, $first_name, $middle_name, $surname, $gender, $dob, $age,
                $occupation, $address, $mobile_no, $unit_no, $bag_type, $segment_no, $marriage_anniversary, $blood_group,
                $donated_before, $donation_count, $hours_since_meal, $collection_date, $collection_time,
                $medication, $health, $bp, $temperature, $weight, $healthy_today, $slept_well, $ate_recently, $no_contagious_disease
            );

            if ($stmt->execute()) {
                $success = "Donor added successfully!";
                // Clear form values after success
                $category = $patient_name = $first_name = $middle_name = $surname = $gender = $dob = $age = '';
                $occupation = $address = $city_village = $district = $state = $pincode = $mobile_no = $unit_no = $bag_type = $segment_no = $marriage_anniversary = $blood_group = '';
                $donated_before = $donation_count = $hours_since_meal = $collection_date = $collection_time = $medication = $health = $bp = $temperature = $weight = '';
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Add Donor</title>
    <link rel="stylesheet" href="css/add_donor.css">
    <script src="js/add_donor.js"></script>
</head>

<body>
<div class="dashboard-content">
    <h2 class="section-title">Add New Donor</h2>

    <?php if (!empty($success)) echo "<p class='success'>".htmlspecialchars($success)."</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>".htmlspecialchars($error)."</p>"; ?>

    <form method="POST" class="donor-form" id="donor-form" novalidate>
        <!-- Donation Information Section -->
        <div class="form-section">
            <div class="form-section-title">Donation Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="category" required onchange="togglePatientFields()">
                        <option value="">Select Category</option>
                        <option value="Replacement" <?php if ($category === 'Replacement') echo 'selected'; ?>>Replacement</option>
                        <option value="Voluntary" <?php if ($category === 'Voluntary') echo 'selected'; ?>>Voluntary</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Segment No.</label>
                    <input type="text" name="segment_no" id="segment_no" placeholder="Enter segment number (e.g., SEG001, A1B2C3)" pattern="[A-Za-z0-9]+" title="Please enter alphanumeric characters only" value="<?php echo htmlspecialchars($segment_no ?? ''); ?>">
                </div>
            </div>

            <!-- Patient Name Fields - Only shown for Replacement -->
            <div id="patient-fields" class="form-row" style="display: none;">
                <div class="form-group">
                    <label>Patient's First Name</label>
                    <input type="text" name="patient_first_name" id="patient_first_name" placeholder="Enter patient's first name" value="<?php echo htmlspecialchars($patient_first_name ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Patient's Middle Name</label>
                    <input type="text" name="patient_middle_name" placeholder="Enter patient's middle name" value="<?php echo htmlspecialchars($patient_middle_name ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Patient's Surname</label>
                    <input type="text" name="patient_surname" id="patient_surname" placeholder="Enter patient's surname" value="<?php echo htmlspecialchars($patient_surname ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Unit No</label>
                    <input type="text" name="unit_no" placeholder="Enter unit number" required value="<?php echo htmlspecialchars($unit_no); ?>">
                </div>
                <div class="form-group">
                    <label>Bag Type</label>
                    <select name="bag_type" required>
                        <option value="">Select Bag Type</option>
                        <option value="SB" <?php if ($bag_type === 'SB') echo 'selected'; ?>>SB (Single Bag)</option>
                        <option value="DB" <?php if ($bag_type === 'DB') echo 'selected'; ?>>DB (Double Bag)</option>
                        <option value="TB" <?php if ($bag_type === 'TB') echo 'selected'; ?>>TB (Triple Bag)</option>
                        <option value="QB" <?php if ($bag_type === 'QB') echo 'selected'; ?>>QB (Quadruple Bag)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Collection Date</label>
                    <input type="date" name="collection_date" id="collection_date" class="modern-date-input" required value="<?php echo htmlspecialchars($collection_date ?: $collection_date_default); ?>">
                </div>
                <div class="form-group">
                    <label>Collection Time</label>
                    <input type="time" name="collection_time" id="collection_time" class="modern-time-input" required value="<?php echo htmlspecialchars($collection_time ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="form-section">
            <div class="form-section-title">Personal Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="Enter first name" required value="<?php echo htmlspecialchars($first_name); ?>">
                </div>
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" placeholder="Enter middle name" value="<?php echo htmlspecialchars($middle_name); ?>">
                </div>
                <div class="form-group">
                    <label>Surname</label>
                    <input type="text" name="surname" placeholder="Enter surname" required value="<?php echo htmlspecialchars($surname); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="modern-date-input" required value="<?php echo htmlspecialchars($dob); ?>"max="<?php echo $dob_max_for_picker; ?>"onchange="calculateAge()">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" name="age" id="age" placeholder="Enter Age" readonly value="<?php echo htmlspecialchars($age); ?>">
                    <div id="age-error" class="error-message"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Occupation</label>
                    <input type="text" name="occupation" placeholder="Enter occupation" value="<?php echo htmlspecialchars($occupation); ?>">
                </div>
                <div class="form-group">
                    <label>Marriage Anniversary</label>
                    <input type="date" name="marriage_anniversary" id="marriage_anniversary" class="modern-date-input" value="<?php echo htmlspecialchars($marriage_anniversary); ?>">
                </div>
                <div class="form-group">
                   <label for="mobile_no">Mobile Number</label>
                    <input type="tel" name="mobile_no" id="mobile_no" placeholder="Enter 10-digit mobile no." required maxlength="10"pattern="[6-9][0-9]{9}" value="<?php echo htmlspecialchars($mobile_no ?? ''); ?>">
                    <div id="mobile-error" class="error-message"></div>
                </div>
            </div>
            <h3 style="color: #495057; margin-bottom: 15px;">Address</h3>
            <div class="form-row">
                <div class="form-group">

                    <label>City/Village</label>
                    <input type="text" name="city_village" id="city_village" placeholder="Enter city or village" required value="<?php echo htmlspecialchars($city_village); ?>">
                </div>
                <div class="form-group">
                    <label>District</label>
                    <input type="text" name="district" id="district" placeholder="Enter district" required value="<?php echo htmlspecialchars($district); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state" id="state" placeholder="Enter state" required value="<?php echo htmlspecialchars($state); ?>">
                </div>
                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode" id="pincode" placeholder="Pincode" readonly value="<?php echo htmlspecialchars($pincode); ?>">
                </div>
            </div>
        </div>

        <!-- Blood Information Section -->
        <div class="form-section">
            <div class="form-section-title">Blood Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Group</label>
                    <select name="blood_group">
                        <option value="">Select Group</option>
                        <?php
                        $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                        foreach ($groups as $g) {
                            $sel = ($blood_group === $g) ? 'selected' : '';
                            echo "<option value=\"{$g}\" {$sel}>{$g}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Donated Before</label>
                    <select name="donated_before" id="donated_before_select" onchange="toggleDonationCount(this.value)">
                        <option value="">Select Option</option>
                        <option value="No" <?php if ($donated_before === 'No') echo 'selected'; ?>>First Time Donor</option>
                        <option value="Yes" <?php if ($donated_before === 'Yes') echo 'selected'; ?>>Yes</option>
                    </select>
                </div>  
                <div class="form-group">
                    <label>Donation Count</label>
                    <input type="number" name="donation_count" id="donation_count" placeholder="How many times?" value="<?php echo htmlspecialchars($donation_count); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Hours Since Last Meal</label>
                <input type="number" name="hours_since_meal" placeholder="Enter hours since last meal" value="<?php echo htmlspecialchars($hours_since_meal); ?>">
            </div>
        </div>
        
        <!-- Health Information Section -->
        <div class="form-section">
            <div class="form-section-title">Health Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Are you healthy today?</label>
                    <select name="healthy_today" required>
                        <option value="">Select Answer</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Did you sleep well last night?</label>
                    <select name="slept_well" required>
                        <option value="">Select Answer</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Within last 4 hours have you ate something?</label>
                    <select name="ate_recently" required>
                        <option value="">Select Answer</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>You don't have any contagious disease like Jaundice, Malaria, HIV/AIDS</label>
                    <select name="no_contagious_disease" required>
                        <option value="">Select Answer</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- General Physical Examination Section -->
        <div class="form-section">
            <div class="form-section-title">General Physical Examination</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Pressure</label>
                    <input type="text" name="bp" placeholder="e.g., 120/80" value="<?php echo htmlspecialchars($bp); ?>">
                </div>
                <div class="form-group">
                    <label>Temperature (°C)</label>
                    <input type="text" name="temperature" placeholder="e.g., 37.0" value="<?php echo htmlspecialchars($temperature); ?>">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="text" name="weight" placeholder="e.g., 70" value="<?php echo htmlspecialchars($weight); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Current Medications</label>
                <textarea name="medication" placeholder="List any current medications or write 'None'"><?php echo htmlspecialchars($medication); ?></textarea>
            </div>
            <div class="form-group">
                <label>General Health Condition</label>
                <textarea name="health" placeholder="Describe general health condition"><?php echo htmlspecialchars($health); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit" id="submit-btn">Add Donor</button>
    </form>
</div>
</body>
</html>
