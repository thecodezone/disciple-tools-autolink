<div class="container">
    <dt-tile>
        <div class="logo">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/dt-assets/images/disciple-tools-logo-white.png' ) ?>" alt="Disciple.Tools" class="logo__image">
        </div>

        <form>
            <dt-alert context="alert" dismissable>
                This alert is loading inside a magic link plugin.
            </dt-alert>
            <dt-text id="username" name="field-name"  placeholder="Field Name" value=""></dt-text>
            <dt-text id="password" name="field-name"  placeholder="Password" value=""></dt-text>
            <button class="button button--primary">
                Sign In
            </button>
            <a class="button button--link">
                Create Account
            </a>
        </form>
    </dt-tile>
</div>
