<?php
$this->part('header', $render_data);
$this->part('skip-links', $render_data);
$this->part('main-header', $render_data);
?>
<main id="main-content" class="site-main" tabindex="-1">
<?php
echo $TEMPLATE_CONTENT;
?>
</main>
<?php
$this->part('main-footer', $render_data);
$this->part('footer', $render_data);
