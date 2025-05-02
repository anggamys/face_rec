<?php
// components/footer.php
$pageSpecificJs = $pageSpecificJs ?? ""; ?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <!-- <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Entry System</h5>
                <p>Providing secure access management solutions.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white">Home</a></li>
                    <li><a href="features.php" class="text-white">Features</a></li>
                    <li><a href="about.php" class="text-white">About</a></li>
                    <li><a href="contact.php" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Info</h5>
                <ul class="list-unstyled">
                    <li>123 Security Street, Tech City</li>
                    <li>Phone: (123) 456-7890</li>
                    <li>Email: info@entrysystem.com</li>
                </ul>
            </div>
        </div> -->
        <hr class="my-4 bg-light" />
        <div class="text-center">
            <p class="mb-0">© <?= date(
                "Y"
            ) ?> Entry System. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- Optional Page JS -->
<?php if ($pageSpecificJs): ?>
<script><?= $pageSpecificJs ?></script>
<?php endif; ?>

</body>
</html>
