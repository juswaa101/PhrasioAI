<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class WriterController extends Controller
{
    public function index()
    {
        $title = '';
        $content = '';
        return view('writer', compact('title', 'content'));
    }

    public function generate(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        if ($request->title == null) {
            return;
        }

        $title = trim(stripslashes(strip_tags($request->title)));

        $result = OpenAI::completions()->create([
            "model" => "text-davinci-003",
            "temperature" => 0.7,
            "top_p" => 1,
            "frequency_penalty" => 0,
            "presence_penalty" => 0,
            'max_tokens' => 600,
            'prompt' => sprintf('Write 10 research title with description about: %s', $title),
        ]);

        $content = trim($result['choices'][0]['text']);
        return response()->json(['title' => $title, 'content' => $content]);
    }
}
