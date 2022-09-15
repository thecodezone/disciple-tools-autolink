<?php
$logo_url = $logo_url ?? $this->functions->fetch_logo();

?>
<header class="navbar">
    <img class="navbar__logo" src="<?php echo $logo_url ?>">
    <app-menu></app-menu>
</header>
