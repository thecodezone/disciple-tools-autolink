<?php

namespace DT\Autolink\Controllers;

use DT\Autolink\GuzzleHttp\Psr7\Request;
use DT\Autolink\Services\Language;
use function DT\Autolink\container;
use function DT\Autolink\extract_request_input;
use function DT\Autolink\response;

class LanguageController
{
    public function switch( Request $request ){

        $body = extract_request_input( $request );
        $user_id = get_current_user_id();
        $result = container()->get( Language::class )->switch_user_locale( $user_id, $body );
		if ( ! $result ) {
          return response( [ "message" => "Invalid request" ], 400 );
        }
        return response( [ 'success' => true ] );
    }
}
