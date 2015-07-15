<html>
<head>
    <meta charset="utf-8">
    <title>AmiLabs Ping Reports</title>
    <meta name="description" content="">
    <meta name="author" content="AmiLabs">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/plugins.css">
    <link rel="stylesheet" href="/css/custom.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/app.js?v=13"></script>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="#a-uptime">UPTIME</a>
                    </li>
<?php
foreach($services as $service){
?>
                    <li class="active">
                        <a href="#a-<?= str_replace('.', '-', $service) ?>"><?= $service ?></a>
                    </li>
<?php
}
?>
                    <li class="active">
                        <a href="<?= $externalLink ?>">{ AmiLabs Tools}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?= $content ?>
    </div>

    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <footer class="site-footer site-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-10">
                    <h4 class="footer-heading"><span id="year-copy">2015</span> © <a href="http://amilabs.co/">AmiLabs</a></h4>
                    <ul class="footer-nav list-inline">
                        <li>Crafted with <a href="https://github.com/amilabs/DevKit">AmiLabs/DevKit</a></li>
                    </ul>
                </div>
                <div class="col-sm-2 text-right" style="padding-top: 25px;">
                    <iframe src="https://ghbtns.com/github-btn.html?user=amilabs&repo=pingreports&type=fork&count=true" frameborder="0" scrolling="0" width="170px" height="20px"></iframe>
                </div>
            </div>
        </div>
    </footer>
    <script src="/js/jquery.ui.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/highcharts.js"></script>
    <script src="/js/modules/exporting.js"></script>
</body>
</html>
