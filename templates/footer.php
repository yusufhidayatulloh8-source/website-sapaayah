</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h4>SAPA Ayah</h4>
            <strong>Sahabat Pembelajar Ayah</strong>
            <p><?= e(setting('site_tagline', 'Menyapa untuk Tumbuh, Tumbuh untuk Menyapa')) ?></p>
            <div class="social-links">
                <a href="https://instagram.com/bapakmilenial_" target="_blank" rel="noopener">IG</a>
                <a href="https://youtube.com/@bapakmilenial9081" target="_blank" rel="noopener">YT</a>
                <a href="<?= base_url('?page=kontak') ?>">WA</a>
            </div>
        </div>
        <div>
            <h4>Program</h4>
            <ul>
                <li>SAPA Journey</li>
                <li>SAPA Class</li>
                <li>SAPA Camp</li>
                <li>SAPA Recovery</li>
                <li>SAPA Mentor</li>
            </ul>
        </div>
        <div>
            <h4>Sumber Daya</h4>
            <ul>
                <li><a href="<?= base_url('?page=artikel') ?>">Artikel & Stories</a></li>
                <li><a href="<?= base_url('?page=gallery') ?>">Video</a></li>
                <li><a href="<?= base_url('?page=event') ?>">Event</a></li>
                <li><a href="<?= base_url('?page=gallery') ?>">Galeri</a></li>
            </ul>
        </div>
        <div>
            <h4>Kontak</h4>
            <p><?= e(setting('site_address', 'Jakarta, Indonesia')) ?></p>
            <p>Email: <?= e(setting('site_email', 'info@sapaayah.or.id')) ?></p>
            <p>Phone: <?= e(setting('site_phone', '+62 812-3456-7890')) ?></p>
        </div>
    </div>
    <div class="container footer-bottom">
        <small>&copy; <?= date('Y') ?> Yayasan SAPA Ayah. All rights reserved.</small>
    </div>
</footer>
<a class="wa-float" target="_blank" rel="noopener" href="https://wa.me/<?= e(setting('whatsapp_number', '6281234567890')) ?>">WhatsApp</a>
<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
