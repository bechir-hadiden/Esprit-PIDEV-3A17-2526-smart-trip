<?php
// src/Controller/FaceTravelController.php
// ✅ 100% GRATUIT — Aucune API payante, aucune clé requise

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FaceTravelController extends AbstractController
{
    private array $destinations = [
        'happy' => [
            ['city'=>'Rio de Janeiro','country'=>'Brésil','flag'=>'🇧🇷','continent'=>'Amérique du Sud','climate'=>'Tropical','season'=>'Déc–Mars','budget'=>'1 200€/pers.','duration'=>'10 jours','tags'=>['Carnaval','Plage','Samba','Fête','Soleil'],'gem'=>false,'desc'=>"Rio vibre à la même fréquence que votre joie. Copacabana, le Carnaval, les caipirinhas au coucher du soleil — chaque instant est une célébration de la vie."],
            ['city'=>'Barcelone','country'=>'Espagne','flag'=>'🇪🇸','continent'=>'Europe','climate'=>'Méditerranéen','season'=>'Avr–Oct','budget'=>'700€/pers.','duration'=>'7 jours','tags'=>['Architecture','Plage','Fête','Gastronomie','Art'],'gem'=>false,'desc'=>"Gaudí, la Barceloneta, les tapas et la movida nocturne — Barcelone amplifie votre bonne humeur jusqu'à l'euphorie."],
            ['city'=>'Medellín','country'=>'Colombie','flag'=>'🇨🇴','continent'=>'Amérique du Sud','climate'=>'Printemps éternel','season'=>'Toute l\'année','budget'=>'900€/pers.','duration'=>'9 jours','tags'=>['Culture','Fleurs','Musique','Innovation','Gastronomie'],'gem'=>true,'desc'=>"La ciudad de la eterna primavera. Medellín a transformé son énergie en art, musique et fleurs perpétuelles — votre joie y trouvera un écho incroyable."],
            ['city'=>'Zanzibar','country'=>'Tanzanie','flag'=>'🇹🇿','continent'=>'Afrique','climate'=>'Tropical','season'=>'Juin–Oct','budget'=>'1 400€/pers.','duration'=>'10 jours','tags'=>['Plage','Épices','Plongée','Couchers de soleil','Culture'],'gem'=>true,'desc'=>"L'île aux épices où l'Afrique rencontre l'Arabie sur des plages de sable blanc immaculé. Votre joie mérite un paradis aussi rare."],
            ['city'=>'Tbilissi','country'=>'Géorgie','flag'=>'🇬🇪','continent'=>'Europe/Asie','climate'=>'Continental','season'=>'Avr–Oct','budget'=>'800€/pers.','duration'=>'8 jours','tags'=>['Vin','Montagne','Gastronomie','Hospitalité','Architecture'],'gem'=>true,'desc'=>"La capitale du vin naturel et de l'hospitalité légendaire. Les Géorgiens célèbrent la vie avec une intensité qui correspondra à votre énergie positive."],
            ['city'=>'Valence','country'=>'Espagne','flag'=>'🇪🇸','continent'=>'Europe','climate'=>'Méditerranéen','season'=>'Mar–Nov','budget'=>'620€/pers.','duration'=>'6 jours','tags'=>['Paella','Plage','Design','Feria','Soleil'],'gem'=>false,'desc'=>"La ville qui a inventé la paella, avec ses plages, sa cité des arts futuriste et sa feria légendaire — Valence célèbre la joie avec authenticité."],
        ],
        'sad' => [
            ['city'=>'Kyoto','country'=>'Japon','flag'=>'🇯🇵','continent'=>'Asie','climate'=>'Tempéré','season'=>'Mars–Mai / Sep–Nov','budget'=>'1 500€/pers.','duration'=>'12 jours','tags'=>['Temples','Zen','Cerisiers','Sérénité','Thé'],'gem'=>false,'desc'=>"Les jardins de mousse, les temples dorés et les cerisiers parlent à l'âme mélancolique. Kyoto transforme la tristesse en poésie pure."],
            ['city'=>'Reykjavik','country'=>'Islande','flag'=>'🇮🇸','continent'=>'Europe','climate'=>'Subarctique','season'=>'Sep–Mars','budget'=>'1 800€/pers.','duration'=>'8 jours','tags'=>['Aurores boréales','Geysers','Fjords','Solitude','Magie'],'gem'=>false,'desc'=>"Sous les aurores boréales et dans les sources chaudes, la mélancolie se dissout. L'Islande offre un espace infini pour contempler et guérir."],
            ['city'=>'Luang Prabang','country'=>'Laos','flag'=>'🇱🇦','continent'=>'Asie','climate'=>'Tropical','season'=>'Nov–Fév','budget'=>'1 100€/pers.','duration'=>'10 jours','tags'=>['Moines','Mékong','Temples','Silence','Spiritualité'],'gem'=>true,'desc'=>"La cérémonie de l'aumône au lever du soleil, les moines en robe safran, le Mékong lent — Luang Prabang est un poème pour les âmes en quête de silence."],
            ['city'=>'Porto','country'=>'Portugal','flag'=>'🇵🇹','continent'=>'Europe','climate'=>'Atlantique','season'=>'Avr–Oct','budget'=>'600€/pers.','duration'=>'6 jours','tags'=>['Fado','Port','Azulejos','Librairies','Poésie'],'gem'=>false,'desc'=>"Porto et ses ruelles escarpées, ses librairies légendaires, son porto au bord du Douro — une ville qui comprend la mélancolie et la transforme en beauté."],
            ['city'=>'Tallinn','country'=>'Estonie','flag'=>'🇪🇪','continent'=>'Europe','climate'=>'Continental','season'=>'Juin–Sep','budget'=>'700€/pers.','duration'=>'6 jours','tags'=>['Médiéval','Forêts','Silence','Architecture','Hiver magique'],'gem'=>true,'desc'=>"La vieille ville médiévale de Tallinn, figée dans le temps, invite à la contemplation. Une capitale nordique où la mélancolie devient une force douce."],
            ['city'=>'Plovdiv','country'=>'Bulgarie','flag'=>'🇧🇬','continent'=>'Europe','climate'=>'Continental','season'=>'Avr–Oct','budget'=>'500€/pers.','duration'=>'6 jours','tags'=>['Art','Histoire','Rues pavées','Cafés','Roman ancien'],'gem'=>true,'desc'=>"La plus vieille ville habitée d'Europe, ses maisons ottomanes colorées et son quartier artistique — un lieu pour les âmes sensibles et contemplatives."],
        ],
        'angry' => [
            ['city'=>'Ubud, Bali','country'=>'Indonésie','flag'=>'🇮🇩','continent'=>'Asie','climate'=>'Tropical','season'=>'Avr–Oct','budget'=>'1 000€/pers.','duration'=>'10 jours','tags'=>['Yoga','Rizières','Spa','Spiritualité','Silence'],'gem'=>false,'desc'=>"Les rizières en terrasse, les retraites de yoga et les temples cachés de Bali absorbent le stress comme une éponge. Ubud vous rend à vous-même."],
            ['city'=>'Îles Féroé','country'=>'Danemark','flag'=>'🇫🇴','continent'=>'Europe','climate'=>'Océanique','season'=>'Mai–Sep','budget'=>'1 600€/pers.','duration'=>'7 jours','tags'=>['Falaises','Solitude','Randonnée','Vent','Nature brute'],'gem'=>true,'desc'=>"Au milieu de l'Atlantique Nord, les falaises vertigineuses des Féroé vous confrontent à votre propre insignifiance — libératrice. Le vent emporte tout stress."],
            ['city'=>'Chiang Mai','country'=>'Thaïlande','flag'=>'🇹🇭','continent'=>'Asie','climate'=>'Tropical montagnard','season'=>'Nov–Fév','budget'=>'900€/pers.','duration'=>'10 jours','tags'=>['Temples','Éléphants','Massages','Forêt','Méditation'],'gem'=>false,'desc'=>"Les temples dorés, les massages thaïlandais et les forêts de bambou de Chiang Mai dissolvent les tensions. Le nord de la Thaïlande guérit l'âme stressée."],
            ['city'=>'Patagonie','country'=>'Argentine / Chili','flag'=>'🇦🇷','continent'=>'Amérique du Sud','climate'=>'Subpolaire','season'=>'Nov–Mars','budget'=>'2 000€/pers.','duration'=>'14 jours','tags'=>['Glaciers','Trekking','Sauvage','Infini','Vide'],'gem'=>false,'desc'=>"Face aux glaciers de Patagonie, les problèmes humains rapetissent jusqu'à l'invisible. La nature brute et infinie remet tout en perspective."],
            ['city'=>'Oman','country'=>'Oman','flag'=>'🇴🇲','continent'=>'Asie','climate'=>'Désertique','season'=>'Oct–Mars','budget'=>'1 300€/pers.','duration'=>'10 jours','tags'=>['Désert','Wadi','Oasis','Hospitalité','Silence total'],'gem'=>true,'desc'=>"Les wadis turquoise, les dunes de sable ocre et l'hospitalité omanie — Oman est une parenthèse hors du monde pour qui a besoin de se reconstruire."],
            ['city'=>'Madère','country'=>'Portugal','flag'=>'🇵🇹','continent'=>'Atlantique','climate'=>'Subtropical','season'=>'Toute l\'année','budget'=>'800€/pers.','duration'=>'8 jours','tags'=>['Levadas','Randonnée','Fleurs','Falaises','Sérénité'],'gem'=>true,'desc'=>"L'île des fleurs et des levadas au milieu de l'Atlantique. Marcher dans les forêts de lauriers millénaires de Madère recharge les batteries en profondeur."],
        ],
        'surprised' => [
            ['city'=>'Tokyo','country'=>'Japon','flag'=>'🇯🇵','continent'=>'Asie','climate'=>'Tempéré','season'=>'Mars–Mai / Sep–Nov','budget'=>'1 600€/pers.','duration'=>'12 jours','tags'=>['Futuriste','Manga','Gastronomie','Temples','Contrastes'],'gem'=>false,'desc'=>"Tokyo ne cesse jamais de surprendre — même les Tokyoïtes. Une ville qui repousse les limites de l'imaginaire à chaque coin de rue."],
            ['city'=>'Madagascar','country'=>'Madagascar','flag'=>'🇲🇬','continent'=>'Afrique','climate'=>'Tropical','season'=>'Avr–Oct','budget'=>'1 400€/pers.','duration'=>'14 jours','tags'=>['Lémuriens','Baobabs','Unique','Faune endémique','Aventure'],'gem'=>true,'desc'=>"Une île-continent avec 80% d'espèces endémiques au monde. Chaque jour à Madagascar est une découverte que vous n'aviez pas imaginée possible."],
            ['city'=>'Cappadoce','country'=>'Turquie','flag'=>'🇹🇷','continent'=>'Asie','climate'=>'Continental','season'=>'Avr–Juin / Sep–Nov','budget'=>'900€/pers.','duration'=>'7 jours','tags'=>['Ballons','Cheminées de fées','Grottes','Paysage lunaire','Magie'],'gem'=>false,'desc'=>"Des maisons troglodytes, des cheminées de fées et des vols en montgolfière au lever du soleil — la Cappadoce est un paysage qui ne devrait pas exister."],
            ['city'=>'Socotra','country'=>'Yémen','flag'=>'🇾🇪','continent'=>'Asie','climate'=>'Désertique','season'=>'Oct–Avr','budget'=>'1 800€/pers.','duration'=>'10 jours','tags'=>['Draconiers','Alien','Unique au monde','Aventure extrême','Isolement'],'gem'=>true,'desc'=>"L'île aux arbres dragonnier en forme de champignon géant — Socotra ressemble à une autre planète. La destination la plus visuellement unique du monde."],
            ['city'=>'Lalibela','country'=>'Éthiopie','flag'=>'🇪🇹','continent'=>'Afrique','climate'=>'Montagnard','season'=>'Oct–Mars','budget'=>'1 600€/pers.','duration'=>'12 jours','tags'=>['Églises rupestres','Histoire','Café originel','Spirituel','Unique'],'gem'=>true,'desc'=>"Les églises chrétiennes taillées dans la roche vive au XIIe siècle, encore actives. Lalibela est un miracle architectural qui défie toute explication."],
            ['city'=>'Îles Lofoten','country'=>'Norvège','flag'=>'🇳🇴','continent'=>'Europe','climate'=>'Subarctique','season'=>'Juin–Sep / Jan–Mars','budget'=>'1 700€/pers.','duration'=>'9 jours','tags'=>['Fjords','Aurores','Pêche','Montagne','Paysage de carte postale'],'gem'=>true,'desc'=>"Des villages de pêcheurs aux couleurs vives accrochés sur des fjords vertigineux sous les aurores boréales — les Lofoten sont une destination qui émerveille absolument."],
        ],
        'fearful' => [
            ['city'=>'Maldives','country'=>'Maldives','flag'=>'🇲🇻','continent'=>'Asie','climate'=>'Tropical','season'=>'Nov–Avr','budget'=>'2 500€/pers.','duration'=>'8 jours','tags'=>['Luxe','Lagon','Isolement','Sécurité','Paradis'],'gem'=>false,'desc'=>"Un atoll isolé, un bungalow sur l'eau turquoise, aucune voiture ni bruit — les Maldives créent une bulle de sécurité absolue où l'anxiété disparaît."],
            ['city'=>'Interlaken','country'=>'Suisse','flag'=>'🇨🇭','continent'=>'Europe','climate'=>'Alpin','season'=>'Juin–Sep','budget'=>'2 000€/pers.','duration'=>'7 jours','tags'=>['Alpes','Lacs','Trains pittoresques','Sécurité','Nature'],'gem'=>false,'desc'=>"Encadrée par les Alpes et deux lacs turquoise, Interlaken est le cocon parfait. La Suisse offre une sécurité et une beauté qui apaisent instantanément."],
            ['city'=>'Açores','country'=>'Portugal','flag'=>'🇵🇹','continent'=>'Atlantique','climate'=>'Subtropical océanique','season'=>'Toute l\'année','budget'=>'900€/pers.','duration'=>'9 jours','tags'=>['Volcans','Baleines','Lacs de cratère','Douceur','Nature'],'gem'=>true,'desc'=>"Archipel au milieu de l'Atlantique, les Açores offrent une nature bienveillante et majestueuse. Les lacs de cratère verts apaisent toute anxiété profondément."],
            ['city'=>'Hokkaido','country'=>'Japon','flag'=>'🇯🇵','continent'=>'Asie','climate'=>'Subarctique','season'=>'Fév (neige) / Juil–Sep','budget'=>'1 600€/pers.','duration'=>'10 jours','tags'=>['Neige','Ramen','Silence','Onsen','Nature préservée'],'gem'=>true,'desc'=>"L'île la plus nordique du Japon, préservée et silencieuse. Les onsen sous la neige d'Hokkaido sont la thérapie la plus douce et la plus réconfortante qui soit."],
            ['city'=>'Gand','country'=>'Belgique','flag'=>'🇧🇪','continent'=>'Europe','climate'=>'Océanique','season'=>'Avr–Oct','budget'=>'750€/pers.','duration'=>'6 jours','tags'=>['Canaux','Médiéval','Bières','Chaleur humaine','Université'],'gem'=>true,'desc'=>"Gand est plus douce que Bruges, plus humaine que Bruxelles. Ses canaux gothiques et ses habitants chaleureux accueillent les âmes vulnérables avec bienveillance."],
            ['city'=>'Cinque Terre','country'=>'Italie','flag'=>'🇮🇹','continent'=>'Europe','climate'=>'Méditerranéen','season'=>'Avr–Oct','budget'=>'950€/pers.','duration'=>'7 jours','tags'=>['Villages colorés','Sentiers','Mer','Pesto','Douceur de vivre'],'gem'=>false,'desc'=>"Cinq villages accrochés aux falaises ligures, reliés par des sentiers fleuris et la mer — un décor de conte de fées qui enveloppe les âmes sensibles de douceur."],
        ],
        'disgusted' => [
            ['city'=>'Marrakech','country'=>'Maroc','flag'=>'🇲🇦','continent'=>'Afrique','climate'=>'Semi-aride','season'=>'Mars–Mai / Sep–Nov','budget'=>'600€/pers.','duration'=>'7 jours','tags'=>['Souks','Riads','Épices','Hammam','Dépaysement total'],'gem'=>false,'desc'=>"Les souks labyrinthiques, les riads cachés, les parfums d'épices — Marrakech vous transporte dans un autre univers en moins de 3 heures d'avion depuis Paris."],
            ['city'=>'Hanoi','country'=>'Vietnam','flag'=>'🇻🇳','continent'=>'Asie','climate'=>'Subtropical','season'=>'Oct–Avr','budget'=>'900€/pers.','duration'=>'12 jours','tags'=>['Pho','Chaos','Histoire','Vieux quartier','Authenticité'],'gem'=>false,'desc'=>"Le chaos délicieux d'Hanoi — scooters, vendeurs de rue, temples entre les immeubles — est la rupture totale avec le quotidien dont vous avez besoin."],
            ['city'=>'Harar','country'=>'Éthiopie','flag'=>'🇪🇹','continent'=>'Afrique','climate'=>'Tropical d\'altitude','season'=>'Oct–Mars','budget'=>'1 200€/pers.','duration'=>'10 jours','tags'=>['Hyènes','Islam ancien','Café originel','Unique','Dépaysement absolu'],'gem'=>true,'desc'=>"La 4ème ville sainte de l'islam, où les habitants nourrissent des hyènes à la main chaque soir. Harar est la définition même du dépaysement absolu."],
            ['city'=>'Sri Lanka','country'=>'Sri Lanka','flag'=>'🇱🇰','continent'=>'Asie','climate'=>'Tropical','season'=>'Déc–Mars / Juin–Sep','budget'=>'1 000€/pers.','duration'=>'12 jours','tags'=>['Thé','Éléphants','Temples','Plages','Épices'],'gem'=>true,'desc'=>"En une île, le Sri Lanka concentre jungle, désert, éléphants, temples et plages. Un antidote puissant à la monotonie et à la lassitude du quotidien."],
            ['city'=>'Géorgie (Tbilissi)','country'=>'Géorgie','flag'=>'🇬🇪','continent'=>'Europe/Asie','climate'=>'Continental','season'=>'Avr–Oct','budget'=>'750€/pers.','duration'=>'9 jours','tags'=>['Vin naturel','Bains soufrés','Caucase','Culture inconnue','Dépaysement'],'gem'=>true,'desc'=>"Là où l'Europe finit et l'Asie commence — un pays que personne ne vous a recommandé, et c'est exactement pour ça qu'il va vous transformer de l'intérieur."],
            ['city'=>'Oaxaca','country'=>'Mexique','flag'=>'🇲🇽','continent'=>'Amérique du Nord','climate'=>'Subtropical','season'=>'Nov–Avr','budget'=>'1 100€/pers.','duration'=>'10 jours','tags'=>['Mezcal','Art indigène','Gastronomie','Día de muertos','Couleurs'],'gem'=>true,'desc'=>"Oaxaca est la capitale de la cuisine mexicaine, du mezcal artisanal et de l'art indigène zapotèque. Une ville qui célèbre la vie avec une intensité et des couleurs renversantes."],
        ],
        'neutral' => [
            ['city'=>'Amsterdam','country'=>'Pays-Bas','flag'=>'🇳🇱','continent'=>'Europe','climate'=>'Océanique','season'=>'Avr–Oct','budget'=>'850€/pers.','duration'=>'6 jours','tags'=>['Canaux','Vélo','Musées','Tulipes','Liberté'],'gem'=>false,'desc'=>"Amsterdam accueille les esprits équilibrés avec une liberté rare. Ses musées, ses canaux et son rythme cycliste sont parfaits pour explorer sans se précipiter."],
            ['city'=>'Montréal','country'=>'Canada','flag'=>'🇨🇦','continent'=>'Amérique du Nord','climate'=>'Continental','season'=>'Juin–Sep','budget'=>'1 200€/pers.','duration'=>'10 jours','tags'=>['Art','Gastronomie','Bilingue','Festivals','Quartiers'],'gem'=>false,'desc'=>"La métropole créative et bilingue qui mélange l'Europe et l'Amérique avec désinvolture. Montréal s'explore librement, sans plan ni pression."],
            ['city'=>'Ljubljana','country'=>'Slovénie','flag'=>'🇸🇮','continent'=>'Europe','climate'=>'Continental','season'=>'Avr–Oct','budget'=>'600€/pers.','duration'=>'6 jours','tags'=>['Château','Lac Bled','Vélo','Chaleur','Compact'],'gem'=>true,'desc'=>"La plus petite et la plus belle des capitales européennes. Ljubljana est à taille humaine, sans foule, avec le lac Bled à 45 minutes — un bonheur tranquille."],
            ['city'=>'Valparaíso','country'=>'Chili','flag'=>'🇨🇱','continent'=>'Amérique du Sud','climate'=>'Méditerranéen','season'=>'Nov–Mars','budget'=>'1 300€/pers.','duration'=>'10 jours','tags'=>['Street art','Collines','Bohème','Poésie','Funiculaires'],'gem'=>true,'desc'=>"La ville-poème de Pablo Neruda, avec ses maisons colorées sur les collines et ses funiculaires centenaires. Valparaíso est une découverte douce et inoubliable."],
            ['city'=>'Séville','country'=>'Espagne','flag'=>'🇪🇸','continent'=>'Europe','climate'=>'Méditerranéen','season'=>'Mars–Mai / Sep–Nov','budget'=>'650€/pers.','duration'=>'7 jours','tags'=>['Flamenco','Tapas','Alcazar','Orange','Soleil'],'gem'=>false,'desc'=>"Ses orangers en fleurs, son Alcazar mauresque et ses soirées de flamenco — Séville est la définition du voyage parfait pour une âme sereine et curieuse."],
            ['city'=>'Tallinn','country'=>'Estonie','flag'=>'🇪🇪','continent'=>'Europe','climate'=>'Continental','season'=>'Juin–Sep','budget'=>'700€/pers.','duration'=>'6 jours','tags'=>['Médiéval','Numérique','Nordique','Forêts','Design'],'gem'=>true,'desc'=>"La capitale la plus numérique du monde dans un écrin médiéval préservé. Tallinn est la surprise parfaite pour un esprit ouvert qui aime les paradoxes."],
        ],
    ];

    #[Route('/face-travel', name: 'face_travel', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('face_travel/index.html.twig');
    }

    // ✅ GRATUIT — Aucune API externe, logique PHP pure
    #[Route('/face-travel/suggest', name: 'face_travel_suggest', methods: ['POST'])]
    public function suggest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $mood = $data['mood'] ?? 'neutral';

        if (!isset($this->destinations[$mood])) {
            $mood = 'neutral';
        }

        $list = $this->destinations[$mood];

        // Destination principale aléatoire
        $main = $list[array_rand($list)];

        // 4 alternatives depuis d'autres humeurs
        $allOthers = [];
        foreach ($this->destinations as $key => $dests) {
            if ($key === $mood) continue;
            foreach ($dests as $d) {
                $allOthers[] = $d;
            }
        }
        shuffle($allOthers);
        $alts = array_slice($allOthers, 0, 4);

        return $this->json([
            'city'         => $main['city'],
            'country'      => $main['country'],
            'flag'         => $main['flag'],
            'continent'    => $main['continent'],
            'climate'      => $main['climate'],
            'best_season'  => $main['season'],
            'budget'       => $main['budget'],
            'duration'     => $main['duration'],
            'description'  => $main['desc'],
            'tags'         => $main['tags'],
            'hidden_gem'   => $main['gem'],
            'alternatives' => array_map(fn($a) => [
                'city'    => $a['city'],
                'country' => $a['country'],
                'flag'    => $a['flag'],
                'reason'  => 'Une autre belle option selon votre humeur',
            ], $alts),
        ]);
    }

    #[Route('/booking/{destination}', name: 'booking')]
    public function booking(string $destination): Response
    {
        return $this->render('face_travel/booking.html.twig', [
            'destination' => $destination,
        ]);
    }
}