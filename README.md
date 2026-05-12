# KYA PHP SDK 🚀

> Le SDK officiel pour intégrer l'authentification par passeports d'agents IA KYA sur vos plateformes PHP.

## 📦 Installation

Installez le SDK via Composer :

```bash
composer require kya/sdk-php


🛡️ Utilisation Rapide
Le SDK permet de vérifier instantanément si une requête provient d'un agent IA certifié et si celui-ci dispose des droits nécessaires (ex: budget).

use Kya\Sdk\Verifier;

$verifier = new Verifier();

// 1. Les données reçues de l'Agent (via Headers ou POST)
$passport = $request->json('passport');
$nonce = $request->header('X-KYA-Nonce');
$signature = $request->header('X-KYA-Signature');

// 2. Vérification complète
$result = $verifier->verify($passport, $nonce, $signature, 250.00);

if ($result['status'] === 'success') {
    echo "Agent identifié : " . $result['agent_name'];
    // Procéder à l'action...
} else {
    echo "Accès refusé : " . $result['message'];
}

🤖 Comment ça marche ?
Le SDK utilise la bibliothèque Libsodium pour valider la signature cryptographique Ed25519 du passeport. Il garantit que :

Le passeport n'a pas été modifié.

L'agent possède bien la clé privée associée.

L'humain derrière l'agent est vérifié par la plateforme KYA.

⚖️ Licence

MIT


---

### 🚦 On en est où ?

Ton projet `kya-php-sdk` est maintenant techniquement prêt. Pour qu'il devienne "réel" et installable avec une commande, il ne nous manque que deux choses :

1.  **Le mettre sur GitHub** : Créer un dépôt public.
2.  **Le lier à Packagist** : C'est le site qui gère les commandes `composer require`.

**Est-ce que tu as déjà un compte GitHub prêt ?** Si oui, je peux t'expliquer comment "pousser" ton dossier `kya-php-sdk` dessus proprement en ligne de commande. 

*PS: On n'a pas encore fait le Middleware (le truc automatique), on le fera juste après pour que ce soit la "Cerise sur le gâteau" de ton SDK.*