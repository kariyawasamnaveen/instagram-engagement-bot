<?php
    $default_customizer_settings = json_encode(app_config('config')['default_customizer_settings']);
    $user_theme_setting = get_option('user_theme_setting', $default_customizer_settings);
?>
<script>
    const theme_setting = JSON.parse('<?= $user_theme_setting ?>');
    // localStorage.clear();
    for (const key in theme_setting) {
        if (theme_setting.hasOwnProperty(key)) {
            const storageKey = 'tabler' + key.charAt(0).toUpperCase() + key.slice(1);
            if (!localStorage.getItem(storageKey)) {
                localStorage.setItem(storageKey, theme_setting[key]);
            }
        }
    }
</script>


<!-- General scripts -->
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/vendors/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/vendors/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/vendors/selectize.min.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/admin/vendors/autosize/autosize.min.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/admin/vendors/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<!-- Core scripts -->
<script src="<?=BASE?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<!-- Core scripts -->
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/core.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/admin/dist/js/admin-core.min.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/admin/dist/js/customizer.js"></script>
<!-- toast -->
<script type="text/javascript" src="<?php echo BASE; ?>assets/plugins/jquery-toast/js/jquery.toast.js"></script>
<!-- emoji picker -->
<script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/config.js"></script>
<script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/util.js"></script>
<script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/jquery.emojiarea.js"></script>
<script src="<?php echo BASE; ?>assets/plugins/emoji-picker/lib/js/emoji-picker.js"></script>
<!-- flags icon -->
<script type="text/javascript" src="<?php echo BASE; ?>assets/plugins/flags/js/docs.js"></script>

<?php if(segment('1') == 'statistics'){ ?>
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/chart_template.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo BASE; ?>assets/admin/vendors/js/notify.min.js"></script>
<!-- general JS -->
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/process.js"></script>
<script type="text/javascript" src="<?php echo BASE; ?>assets/js/general.js"></script>

<?php if (segment(1) == 'new_order') : ?>
    <script type="text/javascript" src="<?=BASE ?>/assets/js/client.js"></script>
<?php endif; ?>

<?=htmlspecialchars_decode(get_option('embed_javascript', ''), ENT_QUOTES)?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelectorAll('.lists-index-ajax').length > 0) {
            loadTableData(window.location.href);
        }
    });
</script>

<script type="text/javascript">
    // PWA Dashboard Logic
    let deferredPrompt;
    const pwaItem = document.getElementById('pwa-install-item');
    const iosModal = document.getElementById('ios-install-guide-modal');
    
    if (pwaItem) {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            pwaItem.style.display = 'block';
            console.log('PWA beforeinstallprompt fired');
        });

        // Forced visibility for Android/Desktop if not installed
        if (!isIOS && !isStandalone) {
            pwaItem.style.display = 'block';
        }

        // Show guide trigger for iOS if not installed
        if (isIOS && !isStandalone) {
            pwaItem.style.display = 'block';
        }

        pwaItem.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
                pwaItem.style.display = 'none';
            } else if (isIOS) {
                if (iosModal) iosModal.style.display = 'block';
            } else {
                alert("Please check your browser menu and select 'Install App' or 'Add to Home screen'.");
            }
        });
    }

    if (iosModal) {
        const closeBtn = iosModal.querySelector('.close-ios-modal');
        if (closeBtn) {
            closeBtn.onclick = function() {
                iosModal.style.display = 'none';
            }
        }
        window.onclick = function(event) {
            if (event.target == iosModal) {
                iosModal.style.display = 'none';
            }
        }
    }
</script>