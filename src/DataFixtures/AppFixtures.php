<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser  ->setFirstName('Guillaume')
                    ->setLastName('DELPORTE')
                    ->setEmail('lior@symfony.com')
                    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                    ->setPicture('https://scontent-frt3-2.xx.fbcdn.net/v/t1.0-9/76643717_1468371110006788_8504457368350752768_n.jpg?_nc_cat=107&_nc_sid=85a577&_nc_ohc=QMPs6B0vgt0AX_1oVCt&_nc_ht=scontent-frt3-2.xx&oh=69cb6dc7c3796d0f4bd5f584a89e1d36&oe=5EB5842C')
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                    ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // nous gérons les utilisateurs
        $user = [];
        $genres = ['male', 'female'];

        for($i=1; $i <=10; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user   ->setFirstName($faker->firstname($genre))
                    ->setLastName($faker->lastname)
                    ->setEmail($faker->email)
                    ->setIntroduction($faker->sentence())
                    ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                    ->setHash($hash)
                    ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }


        //nous gérons les annonces

        for ($i = 1; $i < 30; $i++) {
            $ad = new AD();

            $title = $faker->sentence();
            $coverImage = $faker->ImageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            //join (séparateur, tableau) : renvoie une chaîne des éléments d'un tableau.

            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

            for($j = 1; $j <= mt_rand(2,5); $j++) {
                $image = new Image();

                $image  ->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence)
                        ->setAd($ad);

                $manager->persist($image);
            }


            $manager->persist($ad);
        }
        $manager->flush();
    }
}
