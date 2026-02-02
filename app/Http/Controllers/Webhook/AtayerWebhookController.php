<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AtayerWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('GITHUB_WEBHOOK_SECRET');

        $signature = $request->header('X-Hub-Signature-256');
        $payload   = $request->getContent();

        if (!$signature) {
            Log::warning('Atayer Webhook: Missing signature');
            return response('❌ Missing signature', 403);
        }

        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Atayer Webhook: Invalid signature');
            return response('❌ Invalid signature', 403);
        }

        // 🚀 git pull من المسار الصحيح
        $projectPath = '/home/u931238931/domains/atayeer.lisre.online/public_html/atayer';
        exec("cd $projectPath && git pull origin main > /dev/null 2>&1 &");

        Log::info('Atayer Webhook: Auto Deploy Done');

        return response('✅ Auto Deploy: Git Pull Done', 200);
    }
}
