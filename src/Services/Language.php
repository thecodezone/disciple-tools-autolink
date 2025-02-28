<?php

namespace DT\Autolink\Services;

class Language
{
    public function switch_user_locale( $user_id, $data ): bool
    {
        if ( !empty( $data['dt_autolink_locale'] ) ) {
            $locale = $data['dt_autolink_locale'];
            switch_to_locale( $locale );
            $user = get_user_by( 'id', $user_id );
            $user->locale = $locale;
            wp_update_user( $user );
            return true;
        }
        return false;
    }

    public function get_available_languages( $user_id = null ): array
    {
        if ( $user_id === null ){
            $user_id = get_current_user_id();
        }
        $languages = dt_get_available_languages();
        $dt_user_locale = get_user_locale( $user_id );

        $available_languages = [];
        foreach ( $languages as $key => $language ) {
            $available_languages[] = [
                'code' => $key,
                'name' => $language,
                'selected' => $dt_user_locale === $language['language']
            ];
        }
        return $available_languages;
    }
}
