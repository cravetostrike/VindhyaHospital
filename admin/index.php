<?php
/**
 * VHRC Admin Dashboard Overview (index.php)
 */

// Include header layout (handles security and loads database connection)
include_once __DIR__ . '/includes/header.php';

try {
    // 1. Fetch Metrics counts from SQLite
    $total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $pending_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn();
    $approved_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Approved'")->fetchColumn();
    $total_doctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();

    // 2. Fetch the 5 most recent bookings
    $recent_stmt = $pdo->query("SELECT * FROM appointments ORDER BY id DESC LIMIT 5");
    $recent_bookings = $recent_stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching dashboard statistics: " . $e->getMessage());
}
?>

<!-- Metrics Counters Row -->
<div class="metrics-grid">
    <!-- Counter 1: Total -->
    <div class="metric-card total">
        <div class="metric-info">
            <h3>Total Bookings</h3>
            <span><?php echo number_format($total_appointments); ?></span>
        </div>
        <div class="metric-icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

    <!-- Counter 2: Pending -->
    <div class="metric-card pending">
        <div class="metric-info">
            <h3>Pending Review</h3>
            <span><?php echo number_format($pending_appointments); ?></span>
        </div>
        <div class="metric-icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
    </div>

    <!-- Counter 3: Approved -->
    <div class="metric-card approved">
        <div class="metric-info">
            <h3>Approved Slots</h3>
            <span><?php echo number_format($approved_appointments); ?></span>
        </div>
        <div class="metric-icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>

    <!-- Counter 4: Doctors -->
    <div class="metric-card doctors">
        <div class="metric-info">
            <h3>Medical Staff</h3>
            <span><?php echo number_format($total_doctors); ?></span>
        </div>
        <div class="metric-icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M12 5v14"/><path d="M5 12h14"/></svg>
        </div>
    </div>
</div>

<!-- Recent Bookings Table Panel -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Recent Appointment Requests</h3>
        <a href="manage-appointments.php" class="btn-add-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; box-shadow: none;">
            <span>View All</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </div>
    
    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Specialty Department</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_bookings)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--clr-admin-text-muted); padding: 3rem;">
                                No appointments registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($booking['patient_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['patient_phone']); ?></td>
                                <td><?php echo htmlspecialchars($booking['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['department'] ?? 'General Medicine'); ?></td>
                                <td>
                                    <span class="badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </td>
                                <td><small><?php echo htmlspecialchars($booking['created_at']); ?></small></td>
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
