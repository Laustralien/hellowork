<?php

namespace App\Controller;

use App\Entity\Offer;
use App\FormType\OfferFormType;
use App\Repository\OfferRepository;
use App\Service\PoleemploiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/', name: 'app_offer')]
    public function index(): Response
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'OfferController',
        ]);
    }
    #[Route('/new', name: 'app_offer_new')]
    public function newOffer(OfferRepository $offerRepository, Request $request): Response
    {
        $isSaved = false;
        $offer = new Offer();
        $form = $this->createForm(OfferFormType::class, $offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $offer = $form->getData();
            $offerRepository->save($offer, true);
            $isSaved = true;
        }
        return $this->render('offer/new.html.twig', [
            'controller_name' => 'OfferController',
            'formOffer' => $form,
            'saved' => ($isSaved) ? $offer : null
        ]);
    }
    #[Route('/list', name: 'app_offer_list')]
    public function list(OfferRepository $offerRepository): Response
    {
        $lastImported = $offerRepository->findFew();

        return $this->render('offer/list.html.twig', [
            'controller_name' => 'OfferController',
            'data' => $lastImported,
        ]);
    }
    #[Route('/import/{city}', name: 'app_offer_import')]
    #[Route('/import/{city}/{bearer}', name: 'app_offer_import_bearer')]
    public function import(PoleemploiClient $poleemploiClient, OfferRepository $offerRepository, int $city, ?string $bearer): Response
    {
        if (strlen($city) != 5) {
            return new Response('cityCode malformed or missing', 400);
        }

        $dataToImport = $poleemploiClient->searchOffersByCity($city, $bearer);
        if ($dataToImport['status'] >= 300){
            return new Response('Error getting data : ' . $dataToImport['data'], 400);
        }
        foreach ($dataToImport['data'] as $offer) {
            try {
                $offerEntity = (new Offer())->createFromJsonArray($offer);
                $offerRepository->save($offerEntity,true);
            }catch (\Exception $e){
                echo $e->getMessage() .' for ' . ( $offer['origineOffre']['urlOrigine']);
            }
        }

        return new Response('Import successfull : ' . count($dataToImport['data']), 200);
    }


    #[Route(path: '/report', name: 'app_offer_report')]
    public function report(OfferRepository $offerRepository)
    {

        return $this->render('offer/report.html.twig', [
            'controller_name' => 'OfferController',
            'data' => 'TODO',
        ]);
    }

    #[Route(path: '/test', name: 'app_offer_test')]
    public function test(OfferRepository $offerRepository)
    {
        $dataToImport = json_decode(
            '[
  {
    "id": "161JGGW",
    "intitule": "Réceptionniste (H/F)",
    "description": "Nous recrutons un Réceptionniste (F/H) pour intégrer la Team de lAppartCity de Bordeaux Centre. \n\nAppartCity a obtenu le Label Capital « Meilleur Employeur » en 2022 pour la 4ème année consécutive et « Entreprise engagée pour la diversité »\n\n Votre poste :\n\nRattaché(e) à votre Directeur de Service, vous avez pour mission daccueillir les clients, deffectuer les opérations liées à leur arrivée et à leur départ, et de répondre aux différentes demandes des clients, permettant ainsi le bon déroulement de leur séjour au sein de lAppartCity.\n\nVous travaillez en journée, en horaire du matin ou en horaire du soir et pouvez être amené à remplacer ponctuellement le Réceptionniste de nuit durant ses absences (repos, congés ).\n\nVos principales missions :\n- Accueillir les clients à la réception et au téléphone,\n- Les informer sur les formalités liées à leur séjour et les services proposés,\n- Gérer les arrivées et les départs,\n- Effectuer les encaissements et la facturation,\n- Appliquer la politique tarifaire - consignes et procédures de la check-list « réception »,\n- Coordonner lactivité de la réception avec celle des étages,\n- Mettre à jour le planning des appartements,\n- Appliquer les procédures de travail et les directives définies par le groupe.\n\nVotre profil :\n- Diplômé ou pas, AppartCity privilégie lexpérience professionnelle.\n- Exemplarité, passionné par le métier, sens du service client et goût pour le terrain.\n- Polyvalence, autonomie et dynamisme\n- Bonne maîtrise de langlais\n\nLe petit plus : connaissance de Protel ou dun autre logiciel hôtelier.\n\nConditions Salariales\n\nCDI à 35h à pourvoir dès à présent  au sein de lAppartCity de Bordeaux Centre\n\nHoraires de travail : shift du soir principalement de 15h à 23h\n\n1 week-end de repos par mois\n\nCe poste est ouvert à un travailleur en situation de Handicap.\n\nVotre package salarial : salaire 1880,71€/brut par mois, tickets restaurants de 10€ avec une prise en charge employeur de 60%, mutuelle intéressante et prise en charge des frais de transport en commun à hauteur de 75%.\n\nVotre parcours recrutement :\n\nNous nous engageons à analyser votre dossier de candidature dans un délai de 15 jours. Sil correspond au poste proposé, nous vous contacterons pour un premier échange téléphonique puis un entretien.",
    "dateCreation": "2023-09-11T15:10:07.000Z",
    "dateActualisation": "2023-09-11T15:10:09.000Z",
    "lieuTravail": {
      "libelle": "33 - BORDEAUX",
      "latitude": 44.851895,
      "longitude": -0.587877,
      "codePostal": "33000",
      "commune": "33063"
    },
    "romeCode": "G1703",
    "romeLibelle": "Réception en hôtellerie",
    "appellationlibelle": "Réceptionniste en hôtellerie",
    "entreprise": {
      "nom": "APPARTCITY",
      "description": "Nous rejoindre, cest avant tout intégrer une aventure humaine. Leader français des appart-hôtels avec une centaine détablissements, nous comptons à ce jour 1500 collaborateurs. Vous êtes authentique, positif et souhaitez intégrer une entreprise qui valorise ses collaborateurs, alors nous pouvons certainement faire équipe !",
      "logo": "https://entreprise.pole-emploi.fr/static/img/logos/91c5e28bba434c6d8b1e21505d49a8e8.png",
      "url": "http://www.appartcity.com",
      "entrepriseAdaptee": false
    },
"typeContrat": "CDI",
"typeContratLibelle": "Contrat à durée indéterminée",
"natureContrat": "Contrat travail",
"experienceExige": "E",
"experienceLibelle": "2 ans",
"formations": [
{
"codeFormation": "42754",
"domaineLibelle": "hôtellerie restauration",
"niveauLibelle": "CAP, BEP et équivalents",
"exigence": "S"
}
],
"langues": [
      {
          "libelle": "Anglais",
        "exigence": "S"
      }
    ],
    "competences": [
      {
          "code": "104174",
        "libelle": "Accueillir, orienter et renseigner un client",
        "exigence": "S"
      },
      {
          "code": "104292",
        "libelle": "Effectuer le suivi des réservations",
        "exigence": "S"
      },
      {
          "code": "300305",
        "libelle": "Effectuer le suivi des commandes, la facturation",
        "exigence": "S"
      },
      {
          "code": "300363",
        "libelle": "Identifier, traiter une demande client",
        "exigence": "S"
      },
      {
          "libelle": "Tableur Utilisation normale",
        "exigence": "S"
      },
      {
          "libelle": "Traitement de texte Utilisation normale",
        "exigence": "S"
      }
    ],
    "salaire": {
    "libelle": "Mensuel de 1880,71 Euros à 1880,72 Euros sur 12 mois",
      "complement1": "Chèque repas",
      "complement2": "Mutuelle"
    },
    "dureeTravailLibelle": "35H Travail en horaires fractionnés",
    "dureeTravailLibelleConverti": "Temps plein",
    "alternance": false,
    "contact": {
    "nom": "APPARTCITY - Mme Maeva PIARULLI",
      "coordonnees1": "Pour postuler, utiliser le lien suivant : https://candidat.pole-emploi.fr/offres/recherche/detail/161JGGW",
      "courriel": "Pour postuler, utiliser le lien suivant : https://candidat.pole-emploi.fr/offres/recherche/detail/161JGGW"
    },
    "nombrePostes": 1,
    "accessibleTH": false,
    "deplacementCode": "1",
    "deplacementLibelle": "Jamais",
    "qualificationCode": "6",
    "qualificationLibelle": "Employé qualifié",
    "codeNAF": "55.20Z",
    "secteurActivite": "55",
    "secteurActiviteLibelle": "Hébergement touristique et autre hébergement de courte durée",
    "qualitesProfessionnelles": [
      {
          "libelle": "Faire preuve de réactivité",
        "description": "Capacité à réagir rapidement face à des évènements et à des imprévus, en hiérarchisant les actions, en fonction de leur degré durgence / dimportance."
      },
      {
          "libelle": "Gérer son stress",
        "description": "Capacité à garder le contrôle de soi pour agir efficacement face à des situations irritantes, imprévues, stressantes."
      },
      {
          "libelle": "Faire preuve de rigueur et de précision",
        "description": "Capacité à réaliser des tâches en suivant avec exactitude les règles, les procédures, les instructions qui ont été fournies, sans réaliser derreur et à transmettre clairement des informations. Se montrer ponctuel et respectueux des règles de savoir-vivre usuelles."
      }
    ],
    "origineOffre": {
    "origine": "1",
      "urlOrigine": "https://candidat.pole-emploi.fr/offres/recherche/detail/161JGGW"
    },
    "offresManqueCandidats": false
  }
]', true);

        foreach ($dataToImport as $offer) {
            try{
                $offerEntity = (new Offer())->createFromJsonArray($offer);
                $offerRepository->save($offerEntity,true);
            }catch (\Exception $e ){
                return new Response('Something happened while adding one offer : ' . $e->getMessage(),400);
            }
        }
        return $this->render('offer/list.html.twig', [
            'controller_name' => 'OfferController',
            'data' => 'Object created ' . count($dataToImport),
        ]);
    }


}
