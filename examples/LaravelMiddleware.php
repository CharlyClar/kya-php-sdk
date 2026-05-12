<?php

namespace App\Http\Middleware;

use Closure;
use Kya\Sdk\Verifier;
use Illuminate\Http\Request;

class KyaAgentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. On cherche les "papiers" de l'agent dans les headers
        $passport = $request->input('kya_passport'); // Ou $request->header('X-KYA-Passport')
        $signature = $request->header('X-KYA-Signature');
        $nonce = $request->header('X-KYA-Nonce');

        // 2. Si pas de passeport, on laisse passer (c'est peut-être un humain)
        if (!$passport || !$signature || !$nonce) {
            return $next($request);
        }

        // 3. Si un passeport est présent, on utilise le SDK KYA pour vérifier
        $verifier = new Verifier();
        $result = $verifier->verify(json_decode($passport, true), $nonce, $signature);

        if ($result['status'] !== 'success') {
            return response()->json(['error' => 'Agent KYA non autorisé', 'details' => $result['message']], 401);
        }

        // 4. Succès ! On ajoute les infos de l'agent à la requête pour Amazon
        $request->attributes->add(['kya_agent' => $result['agent_name']]);

        return $next($request);
    }
}