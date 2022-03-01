<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebmailHost;
use \App\WebmailScriptType;

class Webhosts extends Controller
{
    public function indexHosts(Request $request)
    {
        if ($request->method() == "POST")
        {
            WebmailHost::destroy($request->get('del'));
            return redirect(route("config_web_hosts"));
        }

        return view("webhosts.webhosts", [ "hosts" => \App\WebmailHost::get() ]);
    }

    public function indexTypes(Request $request)
    {
        $scriptTypes = WebmailScriptType::get(["name"]);
        return view("webhosts.script_types", ["types" => $scriptTypes]);
    }

    public function editHost(Request $request, $id = null) {
        $host = WebmailHost::findOrNew($id);

        $send_script = "";
        $grab_script = "";
        $cookies = [];

        if ($id) {
            $grab_script = $host->scripts->first() ? $host->scripts->first()->toArray()["script"] : "";
            $send_script = $host->scripts->last() ? $host->scripts->last()->toArray()["script"] : "";
            $cookies = $host->cookies()->get()->map(function ($value) {
                return ['name' => $value->name, 'domain' => $value->domain];
            })->toArray();
        }

        if ($request->method() == "POST" && $request->get("action") == "save")
        {
            $this->validate($request, \App\WebmailHost::$rules, []);
            $host->name = $request->get("name");
            $host->save();

            //dd($request->get("cookieDomain"));

            $host->scripts()->delete();

            if ($request->exists("grab_script"))
            {
                $host->scripts()->create([
                    'idtype' => 1,
                    'idhost' => $host->id,
                    'script' => $request->get("grab_script")
                ]);
            }

            if ($request->exists("send_script"))
            {
                $host->scripts()->create([
                    'idtype' => 2,
                    'idhost' => $host->id,
                    'script' => $request->get("send_script")
                ]);
            }

            $host->cookies()->delete();
            if ($request->exists("cookieName")){
                $cookieNames = $request->get("cookieName");
                $cookieDomains = $request->get("cookieDomain");
                $cookies = collect([]);
                for ($i = 0; $i < count($request->get("cookieName")); $i++)
                {
                    $cookies->push([
                        "idhost" => $host->id,
                        "name" => $cookieNames[$i],
                        "domain" => isset($cookieDomains[$i]) ? $cookieDomains[$i] : ""
                    ]);
                }

                $host->cookies()->createMany($cookies->toArray());
                //dd($host->cookies()->get());
            }

            return redirect(route("config_web_hosts"));
        }

        return view("webhosts.edit_webhosts", compact("host", "send_script", "grab_script", "cookies"));
    }
}
