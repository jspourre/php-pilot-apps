<?php
namespace App\Command;

use App\Command\ImportUserCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Users;
use App\Entity\Posts;

class ImportPostsCommand extends ImportUserCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-posts';
    protected $link = "https://jsonplaceholder.typicode.com/posts";

    protected function configure()
    {
        $this
            ->setDescription("Import Posts")
            ->setHelp("This command will import posts")
            ;
    }

    /*
    * Making an array with ID of user as key and object as value
    */
    protected function userArray($users)
    {   
        $user_array = array();
        foreach ($users as $user) 
        {
            $user_array[$user->getId()] = $user;
        }
        return $user_array;
    }

    /*
    *Verification to avoid double entry
    */
    protected function verficiation($json)
    {
        $post = $this->container->get('doctrine')
        ->getRepository(Posts::class)
        ->findOneBy([
            'title' => $json->title,
            'id' => $json->id
            ]);

        if(!$post)
        {
            return true;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $sucess_nb = 0;
        $output->writeln("<fg=green> Starting import</>");
        $users = $this->container->get('doctrine')
                ->getRepository(Users::class)
                ->findAll();
        $user_array = $this->userArray($users);

        for ($i = 0; $i < count($this->json) ; ++$i) {
            
            $json = $this->json[$i];
            if($user_array[$json->userId] && $this->verficiation($json))
            {
                $output->writeln("<fg=green> Post id:".$json->id." is written by ".$user_array[$json->userId]->getUsername()."</>"); 
                $post = New Posts; 
                $post->setId($json->id);
                $post->setTitle($json->title);
                $post->setBody($json->title);
                $post->setUserId($user_array[$json->userId]);
                $this->em->persist($post);
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