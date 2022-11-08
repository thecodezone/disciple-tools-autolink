<?php include( 'parts/header.php' ); ?>
<?php include( 'parts/navbar.php' ); ?>

<form class="container" action="<?php echo esc_url( $action ); ?>" method="POST">
    <?php wp_nonce_field( 'dt_autolink_survey' ); ?>

    <dt-tile title="<?php echo esc_attr( $question['label'] ); ?>" class="question">
        <div class="question__progress-bar" style="
            --progress: <?php echo esc_attr( $progress ); ?>;
            --progress-border-radius: <?php
            if ( $progress < 99 ) {
                echo '20rem';
            } else {
                echo '0';
            }; ?>"></div>

        <dt-text class="question__input" type="number" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $question['name'] ) ?>"></dt-text>

        <div class="question__pagination">
            <?php if ( $previous_url ) : ?>
                <dt-button type="submit" class="pagination__previous" href="<?php echo esc_url( $previous_url ) ?>" rounded>
                    <dt-icon icon="mdi:chevron-left"></dt-icon>
                </dt-button>
            <?php endif; ?>
            <dt-button type="submit" class="pagination__next" rounded>
                <dt-icon icon="mdi:chevron-right"></dt-icon>
            </dt-button>
        </div>
    </dt-tile>
    </div>


    <?php include( 'parts/footer.php' ); ?>
