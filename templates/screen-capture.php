var page = require('webpage').create();

page.viewportSize = {width: <?php echo $width ?>, height: <?php echo $height ?>};

<?php if (isset($userAgent)) : ?>
page.settings.userAgent = '<?php echo $userAgent ?>';
<?php endif ?>

<?php if (isset($clipOptions)) : ?>
page.clipRect = <?php echo json_encode($clipOptions) ?>;
<?php endif ?>

page.open('<?php echo $url ?>', function () {

    <?php if (isset($includedJsScripts)) : ?>
        <?php foreach ($includedJsScripts as $script) : ?>
            page.injectJs('<?php echo $script ?>');
        <?php endforeach ?>
    <?php endif ?>

    /* This will set the page background color white */
    page.evaluate(function() {
        <?php if (isset($backgroundColor)) : ?>
            document.body.bgColor = '<?php echo $backgroundColor ?>';
        <?php endif ?>

        <?php if (isset($includedJsSnippets)) : ?>
            <?php foreach ($includedJsSnippets as $script) : ?>
                <?php echo $script ?>
            <?php endforeach ?>
        <?php endif ?>
    });

    page.render('<?php echo $imageLocation ?>');
    phantom.exit();
});
