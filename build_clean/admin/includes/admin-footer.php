<?php
/**
 * Закрывающие теги и JS для админки
 * Путь: /admin/includes/admin-footer.php
 */
?>
    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script src="/assets/js/notifications.js" defer></script>
<script src="/admin/assets/admin.js" defer></script>
<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= h($js) ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>