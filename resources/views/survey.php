<?php

/**
 * @var string $action
 * @var array $question
 * @var string $answer
 * @var string $progress
 * @var string $previous_url
 * @var string $next_url
 */

use function DT\Autolink\config;

$this->layout( 'layouts/app' );

?>

<form class="survey container"
      action="<?php echo esc_url( $action ); ?>"
      method="POST">

    <?php wp_nonce_field( config('plugin.nonce') ); ?>

    <dt-tile title="<?php echo esc_attr( $question['label'] ); ?>"
             class="question">
        <div class="section__inner">
            <div class="question__progress-bar"
                 style="
                     --progress: <?php echo esc_attr( $progress ); ?>;"></div>

            <dt-text class="question__input"
                     type="number"
                     value="<?php echo esc_attr( $answer ); ?>"
                     name="<?php echo esc_attr( $question['name'] ) ?>"></dt-text>

            <div class="question__pagination">
                <?php if ( $previous_url ) : ?>
                    <dt-button type="submit"
                               class="pagination__previous"
                               href="<?php echo esc_url( $previous_url ) ?>"
                               rounded>
                        <dt-icon icon="mdi:chevron-left"></dt-icon>
                    </dt-button>
                <?php endif; ?>
                <dt-button type="submit"
                           class="pagination__next"
                           rounded>
                    <dt-icon icon="mdi:chevron-right"></dt-icon>
                </dt-button>
            </div>
        </div>
    </dt-tile>
</form>
