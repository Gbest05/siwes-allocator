    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom App JavaScript -->
    <script src="<?= (require __DIR__ . '/../../../config/app.php')['base_url'] ?>/js/app.js"></script>

    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $jsFile): ?>
            <script src="<?= (require __DIR__ . '/../../../config/app.php')['base_url'] ?>/js/<?= $jsFile ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
