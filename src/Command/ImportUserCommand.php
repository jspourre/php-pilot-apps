<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\ImportJson;
use App\Entity\Users;
use App\Entity\Address;
use App\Entity\Geo;
use App\Entity\Compagny;

class ImportUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-user';
    protected $link = "https://jsonplaceholder.typicode.com/users";


    public function __construct(ImportJson $importJson, EntityManagerInterface $entityManager,ContainerInterface $container)
    {   
        parent::__construct();
        $this->json = $importJson->ImportJson($this->link);
        $this->em = $entityManager;
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription("Import User")
            ->setHelp("This command will import users")
            ;
    }
    /*
    *Verification to avoid double entry
    */
    protected function verficiation($json)
    {
        $user = $this->container->get('doctrine')
        ->getRepository(Users::class)
        ->findOneBy(['username' => $json->username]);

        if(!$user)
        {
            return true;
        }
    }
    protected function execute(InputInterface $input, OutputInterface $output )
    {
        
       
        $output->writeln("<fg=green>---- Starting import of users ----</>");
        $sucess_nb = 0;
        //looping inside json
        for ($i = 0; $i < count($this->json) ; ++$i) {
            
            $json = $this->json[$i];

            if($this->verficiation($json))
            {
                $address = $json->address;
                $geo = $address->geo;
                $compagnie = $json->company;
                $output->writeln("<fg=green>Importing data for ".$json->name." with username ".$json->username."</>");
                //saving geo
                $geoloc = new Geo;
                $geoloc->setLat($geo->lat);
                $geoloc->setLng($geo->lng);
                $this->em->persist($geoloc);
                //saving addres
                $addr = New Address;
                $addr->setStreet($address->street);
                $addr->setSuite($address->suite);
                $addr->setCity($address->city);
                $addr->setZipcode($address->zipcode);
                $addr->setGeo($geoloc);
                $this->em->persist($addr);
                //saving compagnie
                $compagny = New Compagny;
                $compagny->setName($compagnie->name);
                $compagny->setCatchPhrase($compagnie->catchPhrase);
                $compagny->setBs($compagnie->bs);
                $this->em->persist($compagny);
                //Saving user
                $user = new Users;
                $user->setId($json->id);
                $user->setName($json->name);
                $user->setUserName($json->username);
                $user->setEmail($json->email);
                $user->setAddress($addr);
                $user->setPhone($json->phone);
                $user->setWebsite($json->website);
                $user->setCompagny($compagny);
                $this->em->persist($user);
                $sucess_nb++;
            }
            else
            {
                $output->writeln("<fg=yellow>".$json->name." with username ".$json->username." is already in database</>");
            }
        }
        $this->em->flush(); //Persist objects that did not make up an entire batch
        $this->em->clear();
        if($sucess_nb == 0)
        {
            $output->writeln("<bg=yellow;options=bold>Ending import with no elements to import</>");
        }
        else
        {
            $output->writeln("<bg=green;options=bold>Ending import with success of ".$sucess_nb." elements</>");
        }
    }
}