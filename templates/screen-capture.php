var page = require('webpage').create();

page.viewportSize = {width: <?php echo $width ?>, height: <?php echo $height ?>};

<?php if (isset($userAgent)) : ?>
page.settings.userAgent = '<?php echo $userAgent ?>';
<?php endif ?>

<?php if (isset($clipOptions)) : ?>
page.clipRect = <?php echo json_encode($clipOptions) ?>;
<?php endif ?>

<?php if (isset($timeout)) : ?>
page.settings.resourceTimeout = <?php echo $timeout ?>;
<?php endif ?>

page.open('<?php echo $url ?>', function (status) {
    if (status !== 'success') {
        console.log('Unable to load the address!');
        phantom.exit(1);
    }

    <?php if (isset($includedJsScripts)) : ?>
        <?php foreach ($includedJsScripts as $script) : ?>
            page.injectJs('<?php echo $script ?>');
        <?php endforeach ?>
    <?php endif ?>

    page.evaluate(function() {
        <?php if (isset($backgroundColor)) : ?>
            /* This will set the page background color */
            if (document && document.body) {
                document.body.bgColor = '<?php echo $backgroundColor ?>';
            }
        <?php endif ?>

        <?php if (isset($includedJsSnippets)) : ?>
            <?php foreach ($includedJsSnippets as $script) : ?>
                <?php echo $script ?>
            <?php endforeach ?>
        <?php endif ?>
    });

    setTimeout(function() {
            page.render('<?php echo $imageLocation ?>');
            phantom.exit();
    }, <?php echo (isset($delay) ? $delay : 0); ?>);
});
