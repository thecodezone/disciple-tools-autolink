<?php

namespace DT\Autolink\Middleware;

use DT\Autolink\CodeZone\Router\Middleware\Middleware;
use DT\Autolink\Illuminate\Http\RedirectResponse;
use DT\Autolink\Illuminate\Http\Request;
use DT\Autolink\Symfony\Component\HttpFoundation\Response;
use function DT\Autolink\redirect;

class Leader implements Middleware {
	public function handle( Request $request, Response $response, callable $next ){

        $churches           = \DT_Posts::list_posts( 'groups', [
           'assigned_to' => [ get_current_user_id() ],
           'sort'        => '-post_date'
       ], false );
       // dd($request->path());
        if ($churches['total'] > 0 && $request->path() !== "autolink/genmap" ) {
               $response = new RedirectResponse( "/autolink/genmap", 302 );
        }elseif ($request->path() !== "autolink"){
               $response = new RedirectResponse( "/autolink", 302 );
        }
		return $next( $request, $response );
	}
}
