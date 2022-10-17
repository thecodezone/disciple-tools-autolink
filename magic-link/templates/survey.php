<?php include('parts/header.php'); ?>
<?php include('parts/navbar.php'); ?>

<form class="container" action="<?php echo esc_url($action); ?>" method="POST">
    <?php wp_nonce_field('dt_autolink_survey'); ?>

    <dt-tile title="<?php echo esc_attr($question['label']); ?>" class="question">
        <div class="question__progress-bar" style="
            '--progress': <?php echo esc_attr($progress); ?>;"></div>

        <dt-text class="question__input" type="number" value="<?php echo esc_attr($answer); ?>" name="<?php echo esc_attr($question['name']) ?>"></dt-text>

        <div class="question__pagination">
            <?php if ($previous_url) : ?><a class="pagination__previous" href="<?php echo esc_url($previous_url) ?>"></a><?php endif; ?>
            <input type="submit" class="pagination__next" value="">
        </div>
    </dt-tile>
    </div>


    <?php include('parts/footer.php'); ?>