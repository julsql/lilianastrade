<?php

namespace App\DataFixtures;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Mana;
use App\Entity\MyCollection;
use App\Entity\Merchant;
use App\Entity\Type;
use App\Entity\Color;
use App\Entity\Edition;
use App\Entity\Rarity;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Generates initialization data for Merchant :
     *  [pseudo, email]
     * @return \\Generator
     */
    private static function merchantDataGenerator()
    {
        yield ["User", "user@email.com"];
        yield ["Skyfred", "skyfred@gmail.com"];
        yield ["Wolffair", "juliette.debono2002@gmail.com"];
    }


    /**
     * Generates initialization data for Cards : [name, owner]
     * @return \\Generator
     */
    private static function myCollectionDataGenerator()
    {
        yield ["Collection", "User"];
        yield ["Collection utilisée", "Skyfred"];
        yield ["Collection réserve", "Skyfred"];
        yield ["Collection", "Wolffair"];
    }

    /**
     * Generates initialization data for Type :
     *  [name]
     * @return \\Generator
     */
    private static function subtypeDataGenerator()
    {
        yield ["Normal", "la carte n'est pas légendaire"];
        yield ["Legendary", "Si un joueur controle deux cartes ont le même nom, leur propriétaire sacrifie une des deux"];
        yield ["Instant", "Carte à usage unique jouable durant n'importe quel tour"];
        yield ["Basic Land", "Carte permanente, Terrain classique qui permet d'avoir du mana"];
    }

    /**
     * Generates initialization data for Type :
     *  [name]
     * @return \\Generator
     */
    private static function typeDataGenerator()
    {
        yield ["Land", "Carte permanente ayant un ou plusieurs effets", "Normal"];
        yield ["Sorcery", "Carte à usage unique qui ne se joue que pendant el tour de son propriétaire", "Normal"];
        yield ["Enchantment", "Carte permanente ayant un ou plusieurs effets", "Normal"];
        yield ["Creature", "Carte permanente ayant une force et une endurance et possiblement des capacités", "Normal"];
        yield ["Artifact", "Carte permanente ayant une ou plusieurs capacités", "Normal"];
        yield ["Terrain", "Carte permanente ayant un ou plusieurs effets", "Legendary"];
        yield ["Sorcery", "Carte à usage unique qui ne se joue que pendant el tour de son propriétaire", "Legendary"];
        yield ["Enchantment", "Carte permanente ayant un ou plusieurs effets", "Legendary"];
        yield ["Creature", "Carte permanente ayant une force et une endurance et possiblement des capacités", "Legendary"];
        yield ["Artifact", "Carte permanente ayant une ou plusieurs capacités", "Legendary"];
        yield ["Planeswalker", "Carte permanente similaire à un joueur", "Legendary"];
    }

    /**
     * Generates initialization data for Color :
     *  [name]
     * @return \\Generator
     */
    private static function colorDataGenerator()
    {
        yield ["red"];
        yield ["white"];
        yield ["black"];
        yield ["blue"];
        yield ["green"];
        yield ["colorless"];
    }

    /**
     * Generates initialization data for Mana :
     *  [name]
     * @return \\Generator
     */
    private static function manaDataGenerator()
    {
        for ($i = 0; $i <= 20; $i += 1) {
            yield [$i];
        }
        yield [100];
    }

    /**
     * Generates initialization data for Edition :
     *  [name]
     * @return \\Generator
     */
    private static function editionDataGenerator()
    {
        yield ["The Brothers War", date_create("2022-11-01")];
        yield ["Dominaria", date_create("2022-09-01")];
        yield ["New Capenna", date_create("2022-04-01")];
        yield ["Kamigawa", date_create("2022-02-01")];
        yield ["Midnight Hunt", date_create("2021-09-01")];
        yield ["Commander Legends", date_create("2020-11-01")];
        yield ["Ikoria", date_create("2020-04-01")];
        yield ["Core 2021", date_create("2020-07-01")];
        yield ["Core Set 2020", date_create("2019-07-01")];
        yield ["Core Set 2019", date_create("2018-07-01")];
        yield ["Magic Origins", date_create("2015-07-01")];
        yield ["Magic 2015", date_create("2014-07-01")];
    }

    /**
     * Generates initialization data for Rarity :
     *  [name]
     * @return \\Generator
     */
    private static function rarityDataGenerator()
    {
        yield ["Common"];
        yield ["Uncommon"];
        yield ["Rare"];
        yield ["Mythic"];
    }

    /**
     * Generates initialization data for Deck :
     *  [collection, name, owner, published]
     * @return \\Generator
     */
    private static function deckDataGenerator()
    {
        yield ["Orzhov", "Blanc noir", "Skyfred", true];
        yield ["Vial Smasher/Clone", "Grixis", "Skyfred", true];
        yield ["Kinnan", "Deck combo", "Skyfred", false];
        yield ["Témur Landfall", "Toucheterre aggro", "User", false];
        yield ["Kenneth", "Cinq couleurs et combo", "Wolffair", true];
        yield ["Vent des vertues", "Tricolore toucheterre", "Wolffair", true];
    }

    /**
     * Generates initialization data for MyCollection :
     *  [collection, name]
     * @return \\Generator
     */
    private static function myCardsDataGenerator()
    {
        yield ["Kinnan, Bonder Prodigy",      "Collection utilisée", "Skyfred", ['Kinnan', 'Vial Smasher/Clone'], ['Creature'], "Legendary", ['green', 'blue'], 2, "Dominaria", "Mythic"];
        yield ["Elvish Mystic",               "Collection réserve", "Skyfred", ['Kinnan'], ['Creature'], "Normal", ['green'], 1, "Dominaria", "Common"];
        yield ["Fyndhorn Elves",              "Collection", "Wolffair", ['Vent des vertues'], ['Creature'], "Normal", ['green'], 1, "Ikoria", "Common"];
        yield ["Joraga Treespeaker",          "Collection", "Wolffair", ['Vent des vertues'], ['Creature'], "Normal", ['green'], 1, "Ikoria", "Uncommon"];
        yield ["Prophet of Distortion",       "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Creature'], "Normal", ['blue'], 1, "Ikoria", "Uncommon"];
        yield ["Eldrazi Mimic",               "Collection", "Wolffair", ['Kenneth'], ['Creature'], "Normal", ['colorless'], 2, "Ikoria", "Rare"];
        yield ["Incubation Druid",            "Collection", "User", ['Témur Landfall'], ['Creature'], "Normal", ['green'], 2, "Ikoria", "Rare"];
        yield ["Kiora's Follower",            "Collection", "User", ['Témur Landfall'], ['Creature'], "Normal", ['green', 'blue'], 2, "Dominaria", "Uncommon"];
        yield ["Ornithopter of Paradise",     "Collection", "User", ['Témur Landfall'], ['Artifact', 'Creature'], "Normal", ['colorless'], 2, "Dominaria", "Common"];
        yield ["Phyrexian Revoker",           "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Artifact', 'Creature'], "Normal", ['colorless'], 2, "Ikoria", "Rare"];
        yield ["Elvish Archdruid",            "Collection réserve", "Skyfred", ['Kinnan'], ['Creature'], "Normal", ['green'], 3, "Ikoria", "Rare"];
        yield ["Eternal Witness",             "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Creature'], "Normal", ['green'], 3, "Ikoria", "Uncommon"];
        yield ["Gyre Engineer",               "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Creature'], "Normal", ['green', 'blue'], 3, "Ikoria", "Uncommon"];
        yield ["Omnath, Locus of Mana",       "Collection", "Wolffair", ['Vent des vertues'], ['Creature'], "Legendary", ['green'], 3, "Dominaria", "Mythic"];
        yield ["Trophy Mage",                 "Collection", "Wolffair", ['Kenneth'], ['Creature'], "Normal", ['blue'], 3, "Dominaria", "Uncommon"];
        yield ["Arixmethes, Slumbering Isle", "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Creature'], "Legendary", ['green', 'blue'], 4, "Ikoria", "Rare"];
        yield ["Karametra's Acolyte",         "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Creature'], "Legendary", ['green'], 4, "Ikoria", "Uncommon"];
        yield ["Nylea, Keen-Eyed",            "Collection", "User", ['Témur Landfall'], ['Enchantment', 'Creature'], "Legendary", ['green'], 4, "Ikoria", "Mythic"];
        yield ["Solemn Simulacrum",           "Collection utilisée", "Skyfred", ['Orzhov'], ['Artifact', 'Creature'], "Normal", ['colorless'], 4, "Ikoria", "Rare"];
        yield ["Murkfiend Liege",             "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Creature'], "Normal", ['green', 'blue'], 11, "Ikoria", "Rare"];
        yield ["Seedborn Muse",               "Collection", "User", ['Témur Landfall'], ['Creature'], "Normal", ['green'], 5, "The Brothers War", "Rare"];
        yield ["Wandering Archaic",           "Collection", "User", ['Témur Landfall'], ['Creature'], "Normal", ['colorless'], 5, "Ikoria", "Rare"];
        yield ["Bane of Progress",            "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Creature'], "Normal", ['green'], 6, "Dominaria", "Rare"];
        yield ["Cradle Clearcutter",          "Collection", "Wolffair", ['Kenneth'], ['Artifact', 'Creature'], "Normal", ['colorless'], 6, "Ikoria", "Uncommon"];
        yield ["Endbringer",                  "Collection utilisée", "Skyfred", ['Orzhov'], ['Creature'], "Normal", ['colorless'], 6, "Ikoria", "Rare"];
        yield ["Hullbreaker Horror",          "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Creature'], "Normal", ['blue'], 7, "Ikoria", "Rare"];
        yield ["Nezahal, Primal Tide",        "Collection réserve", "Skyfred", ['Kinnan'], ['Creature'], "Legendary", ['blue'], 7, "Ikoria", "Rare"];
        yield ["Kamahl, Heart of Krosa",      "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Creature'], "Normal", ['green'], 8, "Ikoria", "Mythic"];
        yield ["Su-Chi Cave Guard",           "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Artifact', 'Creature'], "Normal", ['colorless'], 8, "Ikoria", "Uncommon"];
        yield ["Rust Goliath",                "Collection utilisée", "Skyfred", ['Orzhov'], ['Artifact', 'Creature'], "Normal", ['colorless'], 10, "Ikoria", "Common"];
        yield ["Jace, Wielder of Mysteries",  "Collection", "Wolffair", ['Vent des vertues'], ['Planeswalker'], "Legendary", ['blue'], 4, "Ikoria", "Rare"];
        yield ["An Offer You Can't Refuse",   "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Instant'], "", ['blue'], 1, "Ikoria", "Uncommon"];
        yield ["Brainstorm",                  "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Instant'], "", ['blue'], 1, "Ikoria", "Common"];
        yield ["Worldly Tutor",               "Collection utilisée", "Skyfred", ['Orzhov'], ['Instant'], "", ['green'], 1, "Dominaria", "Uncommon"];
        yield ["Counterspell",                "Collection", "User", ['Témur Landfall'], ['Instant'], "", ['blue'], 2, "Ikoria", "Common"];
        yield ["Dramatic Reversal",           "Collection utilisée", "Skyfred", ['Kinnan'], ['Instant'], "", ['blue'], 2, "Ikoria", "Common"];
        yield ["Negate",                      "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Instant'], "", ['blue'], 2, "Ikoria", "Common"];
        yield ["Scatter Ray",                 "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Instant'], "", ['blue'], 2, "Ikoria", "Common"];
        yield ["Beast Within",                "Collection", "Wolffair", ['Kenneth'], ['Instant'], "", ['green'], 3, "Ikoria", "Uncommon"];
        yield ["Blue Sun's Zenith",           "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Instant'], "", ['blue'], 4, "Dominaria", "Rare"];
        yield ["Cultivate",                   "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Sorcery'], "Normal", ['green'], 3, "Midnight Hunt", "Common"];
        yield ["Drown in Dreams",             "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Instant'], "", ['blue'], 3, "The Brothers War", "Rare"];
        yield ["Ertai's Scorn",               "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Instant'], "", ['blue'], 3, "The Brothers War", "Uncommon"];
        yield ["Krosan Grip",                 "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Instant'], "", ['green'], 3, "Ikoria", "Uncommon"];
        yield ["Monster Manual",              "Collection utilisée", "Skyfred", ['Orzhov'], ['Sorcery'], "Normal", ['green'], 3, "Dominaria", "Rare"];
        yield ["Titan's Presence",            "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Instant'], "", ['colorless'], 3, "Dominaria", "Uncommon"];
        yield ["Windfall",                    "Collection", "Wolffair", ['Vent des vertues'], ['Sorcery'], "Normal", ['blue'], 3, "Ikoria", "Uncommon"];
        yield ["Foil",                        "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Instant'], "", ['blue'], 4, "Ikoria", "Uncommon"];
        yield ["Unexpected Results",          "Collection utilisée", "Skyfred", ['Orzhov'], ['Sorcery'], "Normal", ['green', 'blue'], 4, "Ikoria", "Rare"];
        yield ["Sol Ring",                    "Collection réserve", "Skyfred", ['Kinnan'], ['Artifact'], "Normal", ['colorless'], 1, "Midnight Hunt", "Uncommon"];
        yield ["Arcane Signet",               "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Artifact'], "Normal", ['colorless'], 2, "Dominaria", "Common"];
        yield ["Moonsilver Key",              "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Artifact'], "Normal", ['colorless'], 2, "Dominaria", "Uncommon"];
        yield ["Simic Signet",                "Collection utilisée", "Skyfred", ['Orzhov'], ['Artifact'], "Normal", ['colorless'], 2, "Ikoria", "Common"];
        yield ["Swiftfoot Boots",             "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Artifact'], "Normal", ['colorless'], 2, "The Brothers War", "Uncommon"];
        yield ["Basalt Monolith",             "Collection", "Wolffair", ['Kenneth'], ['Artifact'], "Normal", ['colorless'], 3, "Ikoria", "Uncommon"];
        yield ["Commander's Sphere",          "Collection", "Wolffair", ['Kenneth'], ['Artifact'], "Normal", ['colorless'], 3, "Dominaria", "Common"];
        yield ["Mirage Mirror",               "Collection utilisée", "Skyfred", ['Orzhov'], ['Artifact'], "Normal", ['colorless'], 3, "Ikoria", "Rare"];
        yield ["Relic of Legends",            "Collection réserve", "Skyfred", ['Kinnan'], ['Artifact'], "Normal", ['colorless'], 3, "The Brothers War", "Uncommon"];
        yield ["Staff of Domination",         "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Artifact'], "Normal", ['colorless'], 3, "Ikoria", "Mythic"];
        yield ["Rocket Launcher",             "Collection utilisée", "Skyfred", ['Orzhov'], ['Artifact'], "Normal", ['colorless'], 4, "Dominaria", "Rare"];
        yield ["Sylvan Library",              "Collection réserve", "Skyfred", ['Kinnan'], ['Enchantment'], "Normal", ['green'], 2, "Ikoria", "Rare"];
        yield ["Awakening Zone",              "Collection", "Wolffair", ['Kenneth'], ['Enchantment'], "Normal", ['green'], 3, "Midnight Hunt", "Rare"];
        yield ["Freed from the Real",         "Collection utilisée", "Skyfred", ['Orzhov'], ['Enchantment'], "Normal", ['blue'], 3, "Ikoria", "Common"];
        yield ["Court of Bounty",             "Collection", "User", ['Témur Landfall'], ['Enchantment'], "Normal", ['green'], 4, "Ikoria", "Rare"];
        yield ["Leyline of Anticipation",     "Collection utilisée", "Skyfred", ['Orzhov'], ['Enchantment'], "Normal", ['blue'], 4, "Midnight Hunt", "Rare"];
        yield ["Command Tower",               "Collection utilisée", "Skyfred", ['Vial Smasher/Clone'], ['Land'], "Normal", ['colorless'], 0, "Dominaria", "Common"];
        yield ["Emergence Zone",              "Collection", "Wolffair", ['Kenneth'], ['Land'], "Normal", ['colorless'], 0, "Ikoria", "Uncommon"];
        yield ["Exotic Orchard",              "Collection", "User", ['Témur Landfall'], ['Land'], "Normal", ['colorless'], 0, "Dominaria", "Rare"];
        yield ["Forest",                      "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Basic Land'], "", ['colorless'], 0, "Ikoria", "Common"];
        yield ["Island",                      "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Basic Land'], "", ['colorless'], 0, "The Brothers War", "Common"];
        yield ["Simic Growth Chamber",        "Collection utilisée", "Skyfred", ['Orzhov', 'Vial Smasher/Clone'], ['Land'], "Normal", ['colorless'], 0, "Ikoria", "Uncommon"];
        yield ["Temple of Mystery",           "Collection", "Wolffair", ['Kenneth', 'Vent des vertues'], ['Land'], "Normal", ['colorless'], 0, "Midnight Hunt", "Rare"];
        yield ["Vineglimmer Snarl",           "Collection", "User", ['Témur Landfall'], ['Land'], "Normal", ['colorless'], 0, "Midnight Hunt", "Rare"];
        yield ["Yavimaya Coast",              "Collection réserve", "Skyfred", ['Kinnan'], ['Land'], "Normal", ['colorless'], 0, "Ikoria", "Rare"];
    }

    public function load(ObjectManager $manager)
    {
        $collRepo = $manager->getRepository(MyCollection::class);
        $mercRepo = $manager->getRepository(Merchant::class);
        $deckRepo = $manager->getRepository(Deck::class);
        $typeRepo = $manager->getRepository(Type::class);
        $colorRepo = $manager->getRepository(Color::class);
        $manaRepo = $manager->getRepository(Mana::class);
        $editionRepo = $manager->getRepository(Edition::class);
        $rarityRepo = $manager->getRepository(Rarity::class);

        foreach (self::merchantDataGenerator() as [$name, $useremail] ) {
            $merchant = new Merchant();
            if ($useremail) {
                $user = $manager->getRepository(User::class)->findOneBy(['email' => $useremail]);
                $merchant->setUser($user);
            }
            $merchant->setPseudo($name);
            $manager->persist($merchant);
        }
        $manager->flush();

        foreach (self::myCollectionDataGenerator() as [$name, $owner])
        {
            $merc = $mercRepo->findOneBy(['pseudo' => $owner]);
            $coll = new MyCollection();
            $coll->setName($name);
            $merc->addMyCollection($coll);
            // there's a cascade persist on collection which avoids persisting down the relation
            $manager->persist($merc);
        }

        $manager->flush();

        foreach (self::deckDataGenerator() as [$name, $description, $owner, $published])
        {
            $merc = $mercRepo->findOneBy(['pseudo' => $owner]);
            $deck = new Deck();
            $deck->setName($name);
            $deck->setDescription($description);
            $deck->setPublished($published);
            $merc->addDeck($deck);
            // there's a cascade persist on collection which avoids persisting down the relation
            $manager->persist($merc);
        }

        $manager->flush();

        foreach (self::colorDataGenerator() as [$name])
        {
            $color = new Color();
            $color->setName($name);
            $manager->persist($color);
        }
        $manager->flush();

        foreach (self::manaDataGenerator() as [$value])
        {
            $mana = new Mana();
            $mana->setValue($value);
            $manager->persist($mana);
        }
        $manager->flush();

        foreach (self::editionDataGenerator() as [$name, $date])
        {
            $edition = new Edition();
            $edition->setName($name);
            $edition->setDate($date);
            $manager->persist($edition);
        }
        $manager->flush();

        foreach (self::rarityDataGenerator() as [$name])
        {
            $rarity = new Rarity();
            $rarity->setName($name);
            $manager->persist($rarity);
        }
        $manager->flush();

        foreach (self::subtypeDataGenerator() as [$name, $description])
        {
            $subtype = new Type();
            $subtype->setName($name);
            $subtype->setDescription($description);
            $manager->persist($subtype);
        }
        $manager->flush();

        foreach (self::typeDataGenerator() as [$name, $description, $subcat])
        {
            $type = new Type();
            $type->setName($name);
            $type->setDescription($description);
            if ($subcat != "") {
                $subtype = $typeRepo->findOneBy(['name' => $subcat]);
                $subtype->addSubType($type);
                $manager->persist($subtype);
            }
            // there's a cascade persist on cards which avoids persisting down the relation
            $manager->persist($type);

        }
        $manager->flush();

        foreach (self::myCardsDataGenerator() as [$name, $collection, $owner, $mydecks, $mytypes, $subtype, $mycolors, $mana, $edition, $rarity])
        {
            $owner = $mercRepo->findOneBy(['pseudo' => $owner]);
            $coll = $collRepo->findOneBy(['name' => $collection, 'owner' => $owner]);

            $decks = array();
            foreach ($mydecks as $deck) {
                $decks[] = $deckRepo->findOneBy(['name' => $deck]);
            }

            $types = array();
            // Ajouter plusieurs type, le subtype ne va qu'au dernier.
            foreach ($mytypes as $type) {
                $types[] = $typeRepo->findOneBy(['name' => $type]);
            }
            if ($subtype != "") {
                $subtype = $typeRepo->findOneBy(['name' => $subtype]);
                array_pop($types);
                $types[] = $typeRepo->findOneBy(['name' => $type, 'parent' => $subtype]);
            }

            $colors = array();
            foreach ($mycolors as $color) {
                $colors[] = $colorRepo->findOneBy(['name' => $color]);
            }
            $mana = $manaRepo->findOneBy(['value' => $mana]);
            $edition = $editionRepo->findOneBy(['name' => $edition]);
            $rarity = $rarityRepo->findOneBy(['name' => $rarity]);
            $card = new Card();
            $card->setName($name);
            $coll->addCard($card);

            $card->setImageName(str_replace(" ", "_", $name) . ".jpg");

            if ($decks) {
                foreach ($decks as $deck) {
                    $deck->addCard($card);
                    $manager->persist($deck);
                }
            }
            foreach ($types as $type) {
                $type->addCard($card);
            }
            foreach ($colors as $color) {
                $color->addCard($card);
            }
            $mana->addCard($card);
            $edition->addCard($card);
            $rarity->addCard($card);
            // there's a cascade persist on cards which avoids persisting down the relation
            $manager->persist($card);
            $manager->persist($coll);
            $manager->persist($type);
            $manager->persist($color);
            $manager->persist($mana);
            $manager->persist($edition);
            $manager->persist($rarity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

