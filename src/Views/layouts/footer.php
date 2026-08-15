    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Custom App JavaScript (Cache-Busting for Render / CDN) -->
    <script src="<?= \App\Core\Helper::asset('js/app.js') ?>?v=<?= file_exists(__DIR__ . '/../../../public/js/app.js') ? filemtime(__DIR__ . '/../../../public/js/app.js') : '2.1.0' ?>"></script>

    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $jsFile): ?>
            <script src="<?= \App\Core\Helper::asset('js/' . $jsFile) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
