<?php

namespace Kya\Sdk;

class Verifier
{
    /**
     * Vérifie la validité d'un passeport KYA et les droits associés.
     * * @param array $passport Le JSON du passeport décodé
     * @param string $nonce Le challenge qui a été signé
     * @param string $signature La signature fournie par l'agent (Base64)
     * @param float|null $amountRequested Optionnel : montant à vérifier
     * @return array
     */
    public function verify(array $passport, string $nonce, string $signature, ?float $amountRequested = null): array
    {
        // 1. Vérification de la signature cryptographique
        try {
            $publicKeyBase64 = $passport['credentialSubject']['publicKey'] ?? null;
            
            if (!$publicKeyBase64) {
                return ['status' => 'error', 'message' => 'Clé publique manquante dans le passeport.'];
            }

            $publicKey = base64_decode($publicKeyBase64);
            $signatureRaw = base64_decode($signature);
            
            $isSignatureValid = sodium_crypto_sign_verify_detached($signatureRaw, $nonce, $publicKey);

            if (!$isSignatureValid) {
                return ['status' => 'error', 'message' => 'Échec de la vérification cryptographique (Signature invalide).'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erreur technique lors de la vérification : ' . $e->getMessage()];
        }

        // 2. Vérification des capacités (Budget)
        if ($amountRequested !== null) {
            $capabilities = $passport['credentialSubject']['capabilities'] ?? [];
            $actions = $capabilities['actions'] ?? [];

            if (!in_array('financial', $actions)) {
                return ['status' => 'denied', 'message' => "L'agent n'a pas la capacité financière."];
            }

            $maxAmount = (float)($capabilities['constraints']['max_amount'] ?? 0);
            if ($amountRequested > $maxAmount) {
                return [
                    'status' => 'denied',
                    'message' => "Limite dépassée ($amountRequested € demandés, max $maxAmount €)."
                ];
            }
        }

        return [
            'status' => 'success',
            'message' => 'Passeport KYA authentifié.',
            'agent_name' => $passport['credentialSubject']['agentName'] ?? 'Unknown'
        ];
    }
}