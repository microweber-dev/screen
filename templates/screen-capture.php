var page = require('webpage').create();

page.viewportSize = {width: <?= $width ?>, height: <?= $height ?>};

<?php if (isset($clipWidth) && $clipWidth && isset($clipHeight) && $clipHeight) : ?>
    page.clipRect = {
    top: 0,
    left: 0,
    width: <?= $clipWidth ?>,
    height: <?= $clipHeight ?>
    };
<?php endif ?>

page.open('<?= $url ?>', function () {
    page.render('<?= $imageLocation ?>');
    phantom.exit();
});
