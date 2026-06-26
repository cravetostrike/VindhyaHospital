<?php
/**
 * VHRC Manage Bookings Panel (manage-appointments.php)
 */

// Include header layout (handles security and database connection)
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

// Handle status updates or deletions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Appointment request Approved successfully.";
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Appointment request Rejected successfully.";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Appointment booking deleted successfully.";
        }
    } catch (PDOException $e) {
        $error = "Operation failed: " . $e->getMessage();
    }
}

// Fetch all appointments ordered by newest first
try {
    $stmt = $pdo->query("SELECT * FROM appointments ORDER BY id DESC");
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching appointments: " . $e->getMessage());
}
?>

<!-- Alert Banners -->
<?php if (!empty($message)): ?>
    <div class="login-alert" style="background-color: var(--clr-success-bg); color: var(--clr-success); border-color: rgba(16, 185, 129, 0.2); margin-bottom: 2rem;">
        ✔ <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="login-alert" style="margin-bottom: 2rem;">
        ✖ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Main Appointments Panel -->
<div class="panel-card">
    <div class="panel-header">
        <h3>All Registered Appointment Bookings</h3>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Phone & Email</th>
                        <th>Date Requested</th>
                        <th>Specialty Department</th>
                        <th>Message / Symptoms</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--clr-admin-text-muted); padding: 3rem;">
                                No appointments registered in the system yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><small>#<?php echo htmlspecialchars($booking['id']); ?></small></td>
                                <td><strong><?php echo htmlspecialchars($booking['patient_name']); ?></strong></td>
                                <td>
                                    <div><?php echo htmlspecialchars($booking['patient_phone']); ?></div>
                                    <small style="color: var(--clr-admin-text-muted);"><?php echo htmlspecialchars($booking['patient_email'] ?: 'N/A'); ?></small>
                                </td>
                                <td><strong><?php echo htmlspecialchars($booking['appointment_date']); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['department'] ?? 'General Medicine'); ?></td>
                                <td>
                                    <div style="max-width: 260px; font-size: 0.85rem; line-height: 1.4; color: var(--clr-admin-text-muted);">
                                        <?php echo htmlspecialchars($booking['message'] ?: 'No symptoms described.'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <!-- Approve Action -->
                                        <?php if ($booking['status'] !== 'Approved'): ?>
                                            <a href="manage-appointments.php?action=approve&id=<?php echo $booking['id']; ?>" class="action-btn approve" title="Approve Request">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Reject Action -->
                                        <?php if ($booking['status'] !== 'Rejected'): ?>
                                            <a href="manage-appointments.php?action=reject&id=<?php echo $booking['id']; ?>" class="action-btn reject" title="Reject Request">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Delete Action -->
                                        <a href="manage-appointments.php?action=delete&id=<?php echo $booking['id']; ?>" class="action-btn delete confirm-action" data-confirm-message="Are you sure you want to delete this appointment booking completely?" title="Delete Booking">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
