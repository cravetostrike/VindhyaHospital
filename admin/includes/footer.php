<?php
/**
 * VHRC Administrative Footer Template
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
    <?php if ($current_page !== 'login.php'): ?>
            </main> <!-- Main Content Container Ends -->
        </div> <!-- Content Panel Shell Ends -->
    </div> <!-- Admin Wrapper Ends -->
    <?php endif; ?>

    <!-- Custom Administrative JS Scripts -->
    <script src="../js/admin.js"></script>
</body>
</html>
