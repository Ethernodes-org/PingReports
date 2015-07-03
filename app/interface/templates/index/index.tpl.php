<a id="a-uptime"></a>
<div class="starter-template" style="padding-top: 50px;" id="container-uptime">
</div>
<?php
foreach ($services as $service => $svc) {
?>
<a id="a-<?= str_replace('.', '-', $service) ?>">...</a>
<div style="text-align: center; font-weight: bold; padding-top: 40px;">
    <?= $service ?> ::
    <a href="<?= $svc['cleanURL'] ?>" target="_blank"><?= $svc['url'] ?> &raquo;</a>
</div>
<div class="starter-template" id="container-<?= str_replace('.', '-', $service) ?>">
</div>
<?php
}
?>

<script type="text/javascript">
$(function () {
    $.getJSON('json.php?view=uprime&callback=?', buildChart);
<?php
foreach (array_keys($services) as $service) {
?>
    $.getJSON('json.php?view=details&service=<?= rawurlencode($service) ?>&callback=?', buildChart);
<?php
}
?>
});
</script>
