<?php
$logo_url = $logo_url ?? $this->functions->fetch_logo();

?>
<navbar class="navbar">
    <img class="navbar__logo" src="<?php echo $logo_url ?>">
    <i class="fi-list"></i>
</navbar>
