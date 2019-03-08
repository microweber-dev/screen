var page = require('webpage').create();
var fs = require('fs');

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

<?php if (isset($blockAnalytics) && $blockAnalytics == true) : ?>
page.onResourceRequested = function(requestData, request) {
	// Google Analytics Tracking
	if ((/google-analytics\.com/gi).test(requestData['url'])){
		request.abort();
	}
	// HubSpot Tracking
	if ((/track.hubspot.com/gi).test(requestData['url'])){
		request.abort();
	}
	// Lucky Orange Tracking
	if ((/luckyorange\.com/gi).test(requestData['url'])){
		request.abort();
	}
	// Lucky Orange Tracking
	if ((/livestatserver\.com/gi).test(requestData['url'])){
		request.abort();
	}
};
<?php endif ?>

<?php if (!empty($cookieJar)) : ?>
Array.prototype.forEach.call(JSON.parse('<?php echo $cookieJar ?>'), function(x){
    phantom.addCookie(x);
});
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

            <?php if (!empty($cookiesPath)) : ?>
                fs.write('<?php echo $cookiesPath ?>', JSON.stringify(phantom.cookies), "w");
            <?php endif ?>

            phantom.exit();
    }, <?php echo (isset($delay) ? $delay : 0); ?>);
});
