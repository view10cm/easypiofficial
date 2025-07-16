<nav class="navbar bg-light px-4 py-2 shadow-sm" style="border-bottom:1px solid #ddd;">
    <div class="container-fluid">
        <div class="row w-100 align-items-center ">
            <div class="col-auto">
                <a href="../pages/dashboard.php">
                   <img src="../assets/easyPiLogo.png" alt="EasyPi Logo" style="height:50px; width:150px;">
           </a> </div>
            <div class="col d-flex justify-content-center">
                <!-- Search bar removed -->
            </div>
            <div class="col-auto d-flex justify-content-end gap-2">
                <!-- Date and Time Display -->
                <div class="d-flex align-items-center me-3">
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;" id="currentDate">
                                    <?php 
                                    date_default_timezone_set('Asia/Manila');
                                    echo date('M j, Y'); 
                                    ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.8rem;" id="currentTime">
                                    <?php echo date('g:i A'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php
                // Start session if not already started
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Include the notification component
                include_once __DIR__ . '/notifications.php';
                
                // Render the notification dropdown
                echo renderNotificationDropdown();
                ?>
            </div>
        </div>
    </div>
</nav>

<script>
    // Update time every second using JavaScript
    function updateTime() {
        const now = new Date();
        const phTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
        
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = phTime.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        }
        
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            dateElement.textContent = phTime.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        }
    }
    
    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);
</script>
</script>
