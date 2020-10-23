<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use MemoChou1993\Lexicon\Console\ClearCommand;
use MemoChou1993\Lexicon\Console\SyncCommand;

class DemoController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return Redirector|View
     */
    public function __invoke(Request $request)
    {
        $language = $request->input('language', 'en');

        if (! in_array($language, ['en', 'zh'])) {
            return redirect()->route('demo');
        }

        if ($request->input('sync')) {
            Artisan::call(SyncCommand::class);

            return redirect()->route('demo', ['language' => $language]);
        }

        if ($request->input('clear')) {
            Artisan::call(ClearCommand::class);

            return redirect()->route('demo', ['language' => $language]);
        }

        $file = sprintf('%s/%s.php', lang_path($language), config('lexicon.filename'));

        $keys = [];

        if (file_exists($file)) {
            $keys = include $file;

            if ($request->input('dump')) {
                dd(file_get_contents($file));
            }
        }

        return view('demo', [
            'language' => $language,
            'keys' => $keys,
        ]);
    }
}
