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
<?php
foreach (array_keys($services) as $service) {
?>
    $.getJSON('json.php?view=uptime&service=<?= rawurlencode($service) ?>&callback=?', buildChart);
<?php
}
?>
});
</script>
