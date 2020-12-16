<?php

namespace Ntpages\LaravelRedirector\Http\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Closure;

use Ntpages\LaravelRedirector\Events\DetectedRedirect;
use Ntpages\LaravelRedirector\Models\Redirect;

class RedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $config = config('redirector');
        $url = rtrim($request->getPathInfo(), '/');

        if ($config['cache']) {

            // todo:    extra cache layer can be added to avoid the check every time
            //          store url if redirected the time that main cache key have left

            $redirects = Cache::remember($config['cache']['key'], $config['cache']['ttl'], function () {
                // makes sense only if the cron is configured
                return Redirect::healthy()->get();
            })->reject(function (Redirect $redirect) use ($url) {
                /**
                 * Trimming result to the same state as after the query.
                 * @see Redirect::scopeWhereUrl()
                 */
                return !preg_match("#^$url(\?.*)?$#i", $redirect->from_url);
            });
        } else {
            $redirects = Redirect::whereUrl($url)->get();
        }

        /** @var Redirect|bool $redirect */
        switch ($redirects->count()) {
            case 0:
                $redirect = false;
                break;

            case 1:
                $redirect = $redirects->first(function (Redirect $redirect) use ($request) {
                    return $this->checkGetParams($request, $redirect);
                });
                break;

            default:
                $redirect = $redirects->sortByDesc(function (Redirect $redirect) {
                    // guarantees none destructive get parameter override behaviour
                    // fixme: migrate to mysql: `ORDER BY CHAR_LENGTH(from_url) DESC`
                    return strlen($redirect->from_url);

                })->first(function (Redirect $redirect) use ($request) {
                    return $this->checkGetParams($request, $redirect);
                });
        }

        if ($redirect) {
            event(new DetectedRedirect($redirect));

            return redirect($redirect->to_url, $redirect->status_code);
        }

        return $next($request);
    }

    /**
     * Check based on the correctness of the value.
     * Model creation process should be validated.
     * @param Request $request
     * @param Redirect $redirect
     * @return bool
     */
    private function checkGetParams(Request $request, Redirect $redirect): bool
    {
        $query = explode('?', $redirect->from_url)[1] ?? null;
        if ($query) {
            // if there where a query there should be parameters
            parse_str($query, $params);

            foreach ($params as $key => $val) {
                // all the params should be in the current url
                if (!$request->query->has($key) || $request->query->get($key) != $val) {
                    return false;
                }
            }
        }

        return true;
    }
}
