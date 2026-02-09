<?php
// Create a simple footer.php file in the same directory
?>
    </main>

    <footer style="
        background: #333;
        color: white;
        padding: 40px 20px;
        text-align: center;
        margin-top: 50px;
    ">
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 30px;
                margin-bottom: 30px;
            ">
                <div style="flex: 1; min-width: 250px;">
                    <h3 style="color: #ff6600; margin-bottom: 15px;">LalaGO</h3>
                    <p style="color: #ccc; line-height: 1.6;">
                        Your favorite food, delivered fast. Order from the best restaurants in town.
                    </p>
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="margin-bottom: 15px;">Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="../index.php" style="color: #ccc; text-decoration: none;">Home</a></li>
                        <li style="margin-bottom: 8px;"><a href="categories.php" style="color: #ccc; text-decoration: none;">Restaurants</a></li>
                        <li style="margin-bottom: 8px;"><a href="../contact.php" style="color: #ccc; text-decoration: none;">Contact Us</a></li>
                        <li style="margin-bottom: 8px;"><a href="../about.php" style="color: #ccc; text-decoration: none;">About</a></li>
                    </ul>
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="margin-bottom: 15px;">Contact Info</h4>
                    <p style="color: #ccc; margin-bottom: 8px;">
                        <i class="fas fa-phone" style="margin-right: 10px; color: #ff6600;"></i>
                        +1 234 567 890
                    </p>
                    <p style="color: #ccc; margin-bottom: 8px;">
                        <i class="fas fa-envelope" style="margin-right: 10px; color: #ff6600;"></i>
                        support@lalago.com
                    </p>
                </div>
            </div>
            
            <div style="
                border-top: 1px solid #444;
                padding-top: 20px;
                color: #999;
                font-size: 14px;
            ">
                <p>&copy; <?php echo date('Y'); ?> LalaGO. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Cart functionality
        document.getElementById('cartBtn').addEventListener('click', function() {
            window.location.href = '../cart.php';
        });
        
        document.getElementById('userProfile').addEventListener('click', function() {
            <?php if(isset($_SESSION['user_id'])): ?>
                window.location.href = '../profile.php';
            <?php else: ?>
                window.location.href = '../login.php';
            <?php endif; ?>
        });
        
        // Update cart count (example - you would get this from your database)
        function updateCartCount() {
            // Replace with your actual cart count logic
            const cartCount = localStorage.getItem('cartCount') || 0;
            document.querySelector('.cart-count').textContent = cartCount;
        }
        
        // Initialize
        updateCartCount();
    </script>
</body>
</html>